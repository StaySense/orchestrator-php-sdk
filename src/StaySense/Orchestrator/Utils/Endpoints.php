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

class Endpoints {

  private $_development = 'localhost:8000';
  private $_prod = '//orchestrator.app.staysense.io';
  private $_host = '';

  public function __construct($dev = false)
  {

    if($dev == true)
    {
      $this->_host = $this->_development;
    }else{
      $this->_host = $this->_prod;
    }

  }

  public function registerEndpoints()
  {
    return array(
      "getCampaigns" => $this->_host . "/api/v1/get-campaigns",
      "getExperiments" => $this->_host . "/api/v1/get-experiments",
      "getVariants" => $this->_host . "/api/v1/get-variants",
      "getVariant" => $this->_host . "/api/v1/get-variant",
      "createCampaign" => $this->_host . "/api/v1/create-campaign",
      "createExperiment" => $this->_host . "/api/v1/create-experiment",
      "createVariant" => $this->_host . "/api/v1/create-variant",
      "logConversion" => $this->_host . "/api/v1/log-conversion",
      "createSite" => $this->_host ."/api/v1/create-site"
    );
  }
}
