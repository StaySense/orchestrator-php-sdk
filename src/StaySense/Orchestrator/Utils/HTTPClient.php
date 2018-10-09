<?php
/**
 * Copyright 2018, StaySense™
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

namespace StaySense\Orchestrator\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Doctrine\Common\Cache\FilesystemCache;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use Kevinrob\GuzzleCache\Storage\DoctrineCacheStorage;
use StaySense\Orchestrator\Exceptions\CacheNotWriteableException;
use StaySense\Orchestrator\Exceptions\HTTPClientNotReady;
use StaySense\Orchestrator\Exceptions\HTTPInvalidEndpoint;
use StaySense\Orchestrator\Exceptions\HTTPRequestObjectInvalid;
use StaySense\Orchestrator\Utils\Endpoints;


class HTTPClient
{

  /**
   * @var GuzzleHttp
   */
  private $_client;

  /**
   * Cache Directory location
   * @var string
   */
  private $_cacheDir;

  /**
   * HTTP Endpoints for the API
   * @var [type]
   */
  private $_endpoints;

  /**
   * StaySense Orchestrator constructor function for full stack deployments.
   * Builds an instance of Guzzle and ensures client side caching is accessible.
   *
   * @param string $dir Cache directory location
   */
  public function __construct($dir, $dev = false)
  {

    $this->_cacheDir = $dir;

    /**
     * Check that the cache is writeable
     */
    $this->ensureCacheIsWritable();

    /**
     * Instantiate our HTTP client
     */
    $this->instantiateGuzzleClient();

    /**
     * Load endpoints config
     * @var Endpoints
     */
    $endpoints = new Endpoints($dev);

    $this->_endpoints = $endpoints->registerEndpoints();

  }

  /**
   * Ensure that our local file cache is available. If it doesnt exist try to create it.
   * If it can't be created or writeable, throw a new error.
   */
  private function ensureCacheIsWritable()
  {
    /**
     * Ensure cache directory exists or is writeable. Attempt to create if not.
     */
    if(file_exists($this->_cacheDir) && !is_writeable($this->_cacheDir))
    {
      try {
        chmod($this->_cacheDir, 0755);
      } catch (Throwable $exception) {
        throw new CacheNotWriteableException("Cache directory exists but is not writeable.", 1);
      }
    }elseif(!file_exists($this->_cacheDir))
    {
      try{
        mkdir($this->_cacheDir, 0755);
      }catch(Throwable $exception)
      {
        throw new CacheNotWriteableException("Cache directory does not exist and could not be created.", 1);
      }
    }

  }


  /**
   * Instantiate our Guzzle Client. This also pushes the cache
   * strategy on to the client.
   *
   * @return boolean
   */
  private function instantiateGuzzleClient()
  {

    /**
     * Creates the cache middleware layer and pushes it on to
     * the HTTP Client
     */
    try {

      $stack = HandlerStack::create();

      $stack->push(
        new CacheMiddleware(
          new PrivateCacheStrategy(
            new DoctrineCacheStorage(
              new FilesystemCache($this->_cacheDir)
            )
          )
        ),
        'cache'
      );

      $this->_client = new Client(['handler' => $stack]);

    } catch (Throwable $exception) {

      throw new HTTPClientNotReady("HTTP Client failed to instantiate. Aborting.", 1);

    }
  }


  /**
  *
  * "Factory" method to build the request dynamically.
  *
  */
  public function requestFactory($endpoint, $args = [], $method = 'GET')
  {

    if(!isset($this->_endpoints[$endpoint]))
    {
      throw new HTTPInvalidEndpoint("No such endpoint '".$endpoint."'. Aborting.", 1);
    }

    //Build GET Requests
    if($method == 'GET')
    {
      $request = http_build_query($args);

      $result = (object) array(
        "url" => $this->_endpoints[$endpoint],
        "query" => $request,
        "method" => $method
      );
    }

    //Build POST Requests
    if($method == 'POST')
    {
      $result = (object) array(
        "url" => $this->_endpoints[$endpoint],
        "form_params" => $args,
        "method" => $method
      );
    }

    if($method == 'PUT')
    {

    }


    return $result;

  }



  /**
   * Takes the request object and fires an HTTP request according to
   *
   * @param  [type] $requestObject [description]
   * @return [type]                [description]
   */
  public function executeRequest($requestObject)
  {

    if($requestObject->method == 'GET')
    {
      if(!$this::verifyGetRequestObject($requestObject))
      {
        return false;
      }

      $result = $this->_client->get(
          $requestObject->url.'?'.$requestObject->query
        );
    }

    if($requestObject->method == 'POST')
    {
      $result = $this->_client->request(
          $requestObject->method,
          $requestObject->url,
          ['form_params' => $requestObject->form_params]
        );
    }


    $response = $this::buildResponseObject($result);

    return $response;

  }

  /**
   * Validate the minimum set of request parameters exist
   *
   * @param  [type] $object [description]
   * @return [type]         [description]
   */
  private static function verifyGetRequestObject($object)
  {
    if(!isset($object->url))
    {
      throw new HTTPRequestObjectInvalid("Request URL must be specified", 1);

      return false;
    }

    if(!isset($object->query))
    {
      throw new HTTPRequestObjectInvalid("Request Query cannot be blank or null", 1);

      return false;
    }

    if(!isset($object->method))
    {
      throw new HTTPRequestObjectInvalid("Request Method must be specified", 1);

      return false;
    }

    return true;
  }


  /**
   * Validate minimum set of POST parameters are set
   *
   * @param  [type] $object [description]
   * @return [type]         [description]
   */
  private static function verifyPostRequestObject($object)
  {
    if(!isset($object->url))
    {
      throw new HTTPRequestObjectInvalid("Request URL must be specified", 1);

      return false;
    }

    if(!isset($object->form_params))
    {
      throw new HTTPRequestObjectInvalid("Request Query cannot be blank or null", 1);

      return false;
    }

    if(!isset($object->method))
    {
      throw new HTTPRequestObjectInvalid("Request Method must be specified", 1);

      return false;
    }

    return true;
  }


  /**
   * Format the response in a two part array
   *
   * @param  [type] $object [description]
   * @return [type]         [description]
   */
  private static function buildResponseObject($object)
  {
    $result = array(
      'httpStatus' => $object->getStatusCode(),
      'body' => self::decodeResponse($object->getBody()->getContents())
    );

    return $result;
  }


  /**
   * Simple helper function to convert JSON to associative array
   *
   * @param  [type] $response [description]
   * @return [type]           [description]
   */
  private static function decodeResponse($response)
  {
    return json_decode($response, true);
  }

}
