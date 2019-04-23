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

class Google_Service_Firestore_GoogleFirestoreAdminV1Index extends Google_Collection
{
  protected $collection_key = 'fields';
  protected $fieldsType = 'Google_Service_Firestore_GoogleFirestoreAdminV1IndexField';
  protected $fieldsDataType = 'array';
  public $name;
  public $queryScope;
  public $state;

  /**
   * @param Google_Service_Firestore_GoogleFirestoreAdminV1IndexField
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return Google_Service_Firestore_GoogleFirestoreAdminV1IndexField
   */
  public function getFields()
  {
    return $this->fields;
  }
  public function setName($name)
  {
    $this->name = $name;
  }
  public function getName()
  {
    return $this->name;
  }
  public function setQueryScope($queryScope)
  {
    $this->queryScope = $queryScope;
  }
  public function getQueryScope()
  {
    return $this->queryScope;
  }
  public function setState($state)
  {
    $this->state = $state;
  }
  public function getState()
  {
    return $this->state;
  }
}
