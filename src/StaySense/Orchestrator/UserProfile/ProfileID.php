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

namespace StaySense\Orchestrator\UserProfile;

use StaySense\Orchestrator\UserProfile\Storage;
use StaySense\Orchestrator\Utils\UUID;

/**
 * ProfileID Class
 */
class ProfileID
{

  /**
   * Unique ID identifying a user
   * @var string uuid.v4
   */
  private $_userID;


  /**
   * Validation regex
   * @var string
   */
  private static $_format = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';

  function __construct()
  {
    $sessionStatus = $this->_verifySessionActive();

    if($sessionStatus === FALSE){
      session_start();
    }

    $this->_init();
  }


  /**
   * Validates a session is active
   *
   * Thanks! http://php.net/manual/en/function.session-status.php#113468
   *
   * @return boolean Session active = true, else false
   */
  private function _verifySessionActive()
  {
    if ( php_sapi_name() !== 'cli' ) {
        if ( version_compare(phpversion(), '5.4.0', '>=') ) {
            return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
        } else {
            return session_id() === '' ? FALSE : TRUE;
        }
    }
    return FALSE;
  }


  /**
   * Initialize our object
   *
   * @return [type] [description]
   */
  private function _init()
  {
    if(isset($_SESSION['_ss_orc_profileId']) && !empty($_SESSION['_ss_orc_profileId']))
    {
      if(self::_validate($_SESSION['_ss_orc_profileId']))
      {
        $this->_setUserID($_SESSION['_ss_orc_profileId']);
        $this->_populateCookie($_SESSION['_ss_orc_profileId']);
      }
    }elseif(isset($_COOKIE['_ss_orc_profileId']) && !empty($_COOKIE['_ss_orc_profileId']))
    {
      if(self::_validate($_COOKIE['_ss_orc_profileId']))
      {
        $this->_setUserID($_COOKIE['_ss_orc_profileId']);
        $this->_populateSession($_COOKIE['_ss_orc_profileId']);
        $this->_populateCookie($_COOKIE['_ss_orc_profileId']);
      }
    }else{
      $profileID = $this->_generateUUIDV4();

      $this->_setUserID($profileID);
      $this->_populateSession($profileID);
      $this->_populateCookie($profileID);
    }

    return true;
  }


  /**
   * Returns the unique user id for the user
   *
   * @return [type] [description]
   */
  public function id()
  {
    return (string) $this->_userID;
  }


  /**
   * Sets the class property _userID
   *
   * @param string $id [description]
   */
  private function _setUserID($id)
  {
    $this->_userID = $id;

    return true;
  }


  /**
   * Set the cookie with a 1 year expiration
   *
   * @param  [type] $id [description]
   * @return [type]     [description]
   */
  private function _populateCookie($id)
  {
    setcookie('_ss_orc_profileId', $id, time()+60*60*24*365, '/');

    return true;
  }


  /**
   * Store the profile id in the session
   *
   * @param  [type] $id [description]
   * @return [type]     [description]
   */
  private function _populateSession($id)
  {
    $_SESSION['_ss_orc_profileId'] = $id;
  }


  /**
   * Generates and returns a UUID V4() token;
   *
   * @return string UUID V4 Token
   */
  private function _generateUUIDV4()
  {
    return UUID::v4();
  }


  /**
   * Validates that our session parameter hasn't been polluted
   *
   * @param  [type] $id [description]
   * @return [type]     [description]
   */
  private static function _validate($id)
  {
    if( preg_match(self::$_format, $id) )
    {
      return TRUE;
    }

    return FALSE;
  }

}
