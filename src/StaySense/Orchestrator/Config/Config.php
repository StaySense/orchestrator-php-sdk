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

namespace StaySense\Orchestrator\Config;

use StaySense\Orchestrator\Exceptions\SettingsNotLoaded;

/**
 * Configuration loader
 */
class Config
{

  /**
   * File path description
   * @var string
   */
  private $_settingsFile;

  /**
   * Constructor function
   */
  function __construct()
  {
    $this->setSettingsFilePath();
  }

  /**
   * Set the settings file location
   */
  private function setSettingsFilePath()
  {
    $this->_settingsFile = 'Settings.json';
  }

  /**
   * Load configuration in to object.
   * @return [type] [description]
   */
  public function load()
  {
    $json = file_get_contents(__DIR__."/".$this->_settingsFile);

    $json_data = json_decode($json, true);

    return $json_data;
  }

}
