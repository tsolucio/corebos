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

class Google_Service_Vault_SavedQuery extends Google_Model
{
  public $createTime;
  public $displayName;
  public $matterId;
  protected $queryType = 'Google_Service_Vault_Query';
  protected $queryDataType = '';
  public $savedQueryId;

  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  public function getCreateTime()
  {
    return $this->createTime;
  }
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  public function getDisplayName()
  {
    return $this->displayName;
  }
  public function setMatterId($matterId)
  {
    $this->matterId = $matterId;
  }
  public function getMatterId()
  {
    return $this->matterId;
  }
  /**
   * @param Google_Service_Vault_Query
   */
  public function setQuery(Google_Service_Vault_Query $query)
  {
    $this->query = $query;
  }
  /**
   * @return Google_Service_Vault_Query
   */
  public function getQuery()
  {
    return $this->query;
  }
  public function setSavedQueryId($savedQueryId)
  {
    $this->savedQueryId = $savedQueryId;
  }
  public function getSavedQueryId()
  {
    return $this->savedQueryId;
  }
}
