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

class Google_Service_CloudSearch_QueryInterpretation extends Google_Model
{
  public $interpretationType;
  public $interpretedQuery;

  public function setInterpretationType($interpretationType)
  {
    $this->interpretationType = $interpretationType;
  }
  public function getInterpretationType()
  {
    return $this->interpretationType;
  }
  public function setInterpretedQuery($interpretedQuery)
  {
    $this->interpretedQuery = $interpretedQuery;
  }
  public function getInterpretedQuery()
  {
    return $this->interpretedQuery;
  }
}
