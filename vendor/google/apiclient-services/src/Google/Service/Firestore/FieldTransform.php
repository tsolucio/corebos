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

class Google_Service_Firestore_FieldTransform extends Google_Model
{
  protected $appendMissingElementsType = 'Google_Service_Firestore_ArrayValue';
  protected $appendMissingElementsDataType = '';
  public $fieldPath;
  protected $removeAllFromArrayType = 'Google_Service_Firestore_ArrayValue';
  protected $removeAllFromArrayDataType = '';
  public $setToServerValue;

  /**
   * @param Google_Service_Firestore_ArrayValue
   */
  public function setAppendMissingElements(Google_Service_Firestore_ArrayValue $appendMissingElements)
  {
    $this->appendMissingElements = $appendMissingElements;
  }
  /**
   * @return Google_Service_Firestore_ArrayValue
   */
  public function getAppendMissingElements()
  {
    return $this->appendMissingElements;
  }
  public function setFieldPath($fieldPath)
  {
    $this->fieldPath = $fieldPath;
  }
  public function getFieldPath()
  {
    return $this->fieldPath;
  }
  /**
   * @param Google_Service_Firestore_ArrayValue
   */
  public function setRemoveAllFromArray(Google_Service_Firestore_ArrayValue $removeAllFromArray)
  {
    $this->removeAllFromArray = $removeAllFromArray;
  }
  /**
   * @return Google_Service_Firestore_ArrayValue
   */
  public function getRemoveAllFromArray()
  {
    return $this->removeAllFromArray;
  }
  public function setSetToServerValue($setToServerValue)
  {
    $this->setToServerValue = $setToServerValue;
  }
  public function getSetToServerValue()
  {
    return $this->setToServerValue;
  }
}
