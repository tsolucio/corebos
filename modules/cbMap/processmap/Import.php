<?php 
 /*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Module       : Business Mappings:: Import
 *  Version      : 5.4.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
 * The accepted format is:
  <map> 
  <targetmodule> 
  <targetname>ProductDetail</targetname> 
  </targetmodule> 
  * 
  <fields> 
     <field>
          <fieldname>productvin</fieldname>
          <fieldID></fieldID>
          <value>productvin</value>
          <predefined></predefined> 
          <Orgfields>
              <Relfield>
                  <RelfieldName>cf_880</RelfieldName>
                  <RelModule>Products</RelModule>
                  <linkfield>productvin</linkfield>
              </Relfield>
          </Orgfields>
      </field>
      .............
  </fields>
 <matches> 
      <match> 
          <fieldname>productdetailname</fieldname> 
          <fieldID> </fieldID> 
          <value>productdetailname</value> 
          <predefined></predefined> 
      </match> 
      ....................
  </matches> 
  <options> 
  <update>FIRST/LAST/ALL</update> 
  </options> 
  </map>
 *************************************************************************************************/

require_once('modules/cbMap/cbMap.php');
require_once('modules/cbMap/processmap/processMap.php');

class Import extends processcbMap {
	private $mapping = array();
	private $input = array();
	private $output = array();

	function processMap($arguments) {
		$this->convertMap2Array();
		return $this;
	}

	public function getCompleteMapping() {
		return $this->mapping;
	}

	
        public function getMapTargetModule(){
            if(isset($this->mapping["targetmodule"]['targetname']))
			return $this->mapping["targetmodule"]['targetname'];
		return array();
	}
                
	private function convertMap2Array() {
		$xml = $this->getXMLContent();
                foreach ($xml->fields->field as $k => $v) {
                    $fieldname= isset($v->fieldname) ? (String)$v->fieldname : '';
                    $value= isset($v->value) ? (String)$v->value : '';
                    $predefined= isset($v->predefined) ? (String)$v->predefined : '';
                    if(empty($v->Orgfields[0]->Relfield)){
                        $fieldinfo[$fieldname] = array('value'=>$value,'predefined'=>$predefined);
                    }
                    elseif(!empty($v->Orgfields[0]->Relfield) && isset($v->Orgfields[0]->Relfield) ){
                        $allRelValues=array();
                        foreach ($v->Orgfields[0]->Relfield as $key => $value1) { 
                            if ($key == 'RelfieldName') {
                                $allRelValues['fieldname']=(string) $value1;
                            }
                            if ($key == 'RelModule') {
                                $allRelValues['relmodule']=(string) $value1;
                            }
                            if ($key == 'linkfield') {
                                $allRelValues['linkfield']=(string) $value1;
                            } 
                            $allmergeFields[]=$allRelValues;
                        }
                        $fieldinfo[$fieldname] = array('value'=>$value,'predefined'=>$predefined,"relatedFields" => $allmergeFields);
                    }
                }
                foreach ($xml->matches[0] as $key => $value) {
                    $fldname = (string) $value->fieldname;
                    $fldval = (string) $value->value;
                    $match_fields[$fldname] = $fldval;
                }
                 foreach ($xml->options[0] as $key => $value) {
                    $update_rules[$key] = (string) $value;
                }
                $mapping= array('target' => $fieldinfo, 'match' => $match_fields,'options'=>$update_rules);;                
		$this->mapping = $mapping;
	}

}

?>