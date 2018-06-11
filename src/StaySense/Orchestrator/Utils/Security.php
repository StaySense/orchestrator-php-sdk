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

use StaySense\Orchestrator\Exceptions\APIKeyNotValid;

class Security{

  private static $_format = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';

  /**
   * Validates the API key is the correct format to catch format errors
   * @param  string $token The API Token for the platform
   * @return boolean        True or False
   */
  public static function validate($token)
  {
    if( preg_match(self::$_format, $token) )
    {
      return 1;
    } else {
      throw new \APIKeyNotValid("API Key Not Valid", 1);
    }
  }

}
