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

namespace StaySense\Orchestrator;


use StaySense\Orchestrator\Utils\HTTPClient;
use Throwable;
use StaySense\Orchestrator\Utils\Security;
use StaySense\Orchestrator\Config\Config;
use StaySense\Orchestrator\Verbs\CreateCampaign;
use StaySense\Orchestrator\Exceptions\HTTPRequestFailed;
use StaySense\Orchestrator\Exceptions\ProfileIDNotSet;
use StaySense\Orchestrator\UserProfile\ProfileID;


class Orchestrator
{

  /**
   * Cache Directory
   * @var string
   */
  private $_dir;

  /**
   * API Key for StaySense Platform
   * @var string
   */
  private $_apiKey;

  /**
   * Config object
   * @var ArrayObject
   */
  private $_config;

  /**
   * Site ID
   * @var ArrayObject
   */
  private $_siteID;

  /**
   * Profile ID
   * @var string
   */
   private $_profileID;


  /**
   *
   * StaySense Orchestrator™ constructor function for use in full stack efforts
   *
   */
  public function __construct($dir = '/cache-dir/', $dev = false)
  {
    $this->_dir = $dir;

    $this->_http = new HTTPClient($this->_dir, $dev);

    $this->_loadConfig();
  }



  /**
   * Sets the API Key in the class
   * @param [type] $apiKey [description]
   */
  public function setAPIKey($apiKey)
  {
    if(Security::validate($apiKey))
    {
      $this->_apiKey = $apiKey;
    }

    return true;
  }


  /**
  * Sets the Site ID for the SDK
  * @param [type] $siteID [description]
  */
  public function setSiteID($siteID)
  {
    $this->_siteID = $siteID;

    return true;
  }

  public function setProfileID()
  {
    $profile = new ProfileID();
    $this->_profileID = $profile->id();

    return true;
  }


  /**
   * Load configuration in to the instannce
   * @return [type] [description]
   */
  private function _loadConfig()
  {
    $config = new Config();

    try{
      $this->_config = $config->load();
    }catch(Throwable $exception)
    {
      throw new \SettingsNotLoaded("Could not load settings file.", 1);
    }

  }



  /**
   * Gets all available campaigns for a given siteId
   * @param  [type] $siteId [description]
   * @return [type]         [description]
   */
  public function getCampaigns($siteId)
  {
    $args = [
      "site_id" => $siteId
    ];

    try {
      $request = $this->_http->requestFactory("getCampaigns", $args);

      $result = $this->_http->executeRequest($request);

    } catch (Throwable $exception) {
      throw new HTTPRequestFailed($exception->getMessage(), 1);
    }

    return $result;
  }



  /**
   * Creates a campaign within a specific site ID
   * @param  string $siteId [description]
   * @return arrayObject         [description]
   */
  public function createCampaign($siteId, $type, $name = '', $percent = 100, $engine_type = null)
  {
    $args = [
      "site_id" => $siteId,
      "campaign_name" => $name,
      "campaign_type" => $type,
      "traffic_percent" => $percent,
      "engine_type" => $engine_type
    ];

    try {
      $request = $this->_http->requestFactory("createCampaign", $args, 'POST');

      $result = $this->_http->executeRequest($request);
    } catch (Throwable $exception) {
      throw new HTTPRequestFailed($exception->getMessage(), 1);
    }

    return $result;

  }



  /**
   * Gets all active experiments for a given campaign
   * @param  [type] $campaignID [description]
   * @return [type]             [description]
   */
  public function getExperiments($campaignID)
  {
    $args = [
      "campaign_id" => $campaignID
    ];

    try {
      $request = $this->_http->requestFactory("getExperiments", $args);

      $result = $this->_http->executeRequest($request);
    } catch (Throwable $exception) {
      throw new HTTPRequestFailed($exception->getMessage(), 1);
    }

    return $result;
  }



  /**
   * Creates an experiment for a given campaign
   * @param  string $campaignID [description]
   * @return arrayObject             [description]
   */
  public function createExperiment($campaignID, $name = '')
  {
    $args = [
      "campaign_id" => $campaignID,
      "experiment_name" => $name
    ];

    try {
      $request = $this->_http->requestFactory("createExperiment", $args, 'POST');

      $result = $this->_http->executeRequest($request);
    } catch (Throwable $exception) {
      throw new HTTPRequestFailed($exception->getMessage(), 1);
    }

    return $result;
  }



  /**
   * Gets all variants for a given campaign and experiment
   * @param  string $campaignID   [description]
   * @param  string $experimentID [description]
   * @return arrayObject               [description]
   */
  public function getVariants($campaignID, $experimentID)
  {
    $args = [
      "campaign_id" => $campaignID,
      "experiment_id" => $experimentID
    ];

    try {
      $request = $this->_http->requestFactory("getVariants", $args);

      $result = $this->_http->executeRequest($request);

    } catch (Throwable $exception) {
      throw new HTTPRequestFailed($exception->getMessage(), 1);
    }

    return $result;
  }



  /**
   * Creates a variant for given campaign and experiment
   * @param  string $campaignID   [description]
   * @param  string $experimentID [description]
   * @return arrayObject              [description]
   */
  public function createVariant($campaignID, $experimentID, $name = '', $content = null)
  {
    $args = [
      "campaign_id" => $campaignID,
      "experiment_id" => $experimentID,
      "variant_name" => $name
    ];

    if($content != null)
    {
      $args["variant_content"] = $content;
    }

    try {
      $request = $this->_http->requestFactory("createVariant", $args, 'POST');

      $result = $this->_http->executeRequest($request);
    } catch (Throwable $exception) {
      throw new HTTPRequestFailed($exception->getMessage(), 1);
    }

    return $result;
  }


  /**
   * Public function that creates a site for a series of campaigns and experiments
   * to be housed under. Essentially a logical grouping key.
   *
   * @param  [type] $URL    [description]
   * @param  [type] $userID [description]
   * @return [type]         [description]
   */
  public function createSite($URL, $userID)
  {
    if(empty($URL) || empty($userID))
    {
      return false;
    }

    $args = [
      "site_url" => $URL,
      "user_id" => $userID
    ];

    try {
      $request = $this->_http->requestFactory("createSite", $args, 'GET');

      $result = $this->_http->executeRequest($request);
    } catch (Throwable $exception) {
      throw new HTTPRequestFailed($exception->getMessage(), 1);
    }

    return $result;
  }

  /**
   * Sends request to the platform to determine which variant to serve
   *
   * @param  string $campaignID   [description]
   * @param  string $experimentID [description]
   * @param  string $profileID    [description]
   * @return arrayObject
   */
  public function getVariant($campaignID, $experimentID)
  {
    if(empty($this->_profileID))
    {
      throw new ProfileIDNotSet("This route requires a user profile ID for execution.", 1);
    }

    $args = [
      "campaign_id" => $campaignID,
      "experiment_id" => $experimentID,
      "profile_id" => $this->_profileID
    ];

    try {
      $request = $this->_http->requestFactory("getVariant", $args);

      $result = $this->_http->executeRequest($request);

    } catch (Throwable $exception) {
      throw new HTTPRequestFailed($exception->getMessage(), 1);
    }

    return $result;
  }



  /**
   * Sends conversion event to the platform
   * @param  string $campaignID   [description]
   * @param  string $experimentID [description]
   * @param  string $variantID    [description]
   * @param  string $profileID    [description]
   * @return boolean               [description]
   */
  public function logConversionEvent($campaignID, $experimentID, $variantID)
  {
    if(empty($this->_profileID))
    {
      throw new ProfileIDNotSet("This route requires a user profile ID for execution.", 1);
    }

    $args = [
      "campaign_id" => $campaignID,
      "experiment_id" => $experimentID,
      "variant_id" => $variantID,
      "profile_id" => $this->_profileID
    ];

    try {
      $request = $this->_http->requestFactory("logConversion", $args);

      $result = $this->_http->executeRequest($request);

    } catch (Throwable $exception) {
      throw new HTTPRequestFailed($exception->getMessage(), 1);
    }

    return $result;
  }


}
