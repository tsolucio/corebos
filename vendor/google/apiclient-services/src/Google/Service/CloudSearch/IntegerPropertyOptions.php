<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

class Google_Service_CloudSearch_IntegerPropertyOptions extends Google_Model
{
  public $maximumValue;
  public $minimumValue;
  protected $operatorOptionsType = 'Google_Service_CloudSearch_IntegerOperatorOptions';
  protected $operatorOptionsDataType = '';
  public $orderedRanking;

  public function setMaximumValue($maximumValue)
  {
    $this->maximumValue = $maximumValue;
  }
  public function getMaximumValue()
  {
    return $this->maximumValue;
  }
  public function setMinimumValue($minimumValue)
  {
    $this->minimumValue = $minimumValue;
  }
  public function getMinimumValue()
  {
    return $this->minimumValue;
  }
  /**
   * @param Google_Service_CloudSearch_IntegerOperatorOptions
   */
  public function setOperatorOptions(Google_Service_CloudSearch_IntegerOperatorOptions $operatorOptions)
  {
    $this->operatorOptions = $operatorOptions;
  }
  /**
   * @return Google_Service_CloudSearch_IntegerOperatorOptions
   */
  public function getOperatorOptions()
  {
    return $this->operatorOptions;
  }
  public function setOrderedRanking($orderedRanking)
  {
    $this->orderedRanking = $orderedRanking;
  }
  public function getOrderedRanking()
  {
    return $this->orderedRanking;
  }
}
