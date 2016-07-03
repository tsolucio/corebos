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
 *  Module       : Business Mappings:: Module to Module Mapping
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
 * The accepted format is:
 <map>
  <originmodule>
    <originid>22</originid>  {optional}
    <originname>SalesOrder</originname>
  </originmodule>
  <targetmodule>
    <targetid>23</targetid>  {optional}
    <targetname>Invoice</targetname>
  </targetmodule>
  <fields>
    <field>
      <fieldname>subject</fieldname>   {destination field on invoice}
      <fieldID>999</fieldID>  {optional}
      <Orgfields>  {if more than one is present they will be concatenated with the delimiter}
        <Orgfield>
          <OrgfieldName>subject</OrgfieldName>
          <OrgfieldID>634</OrgfieldID>
        </Orgfield>
        <Orgfield>
          <OrgfieldName>sostatus</OrgfieldName>
          <OrgfieldID>778</OrgfieldID>
        </Orgfield>
        <Orgfield>
          <OrgfieldName>_FromSO</OrgfieldName>  {this is a constant string}
          <OrgfieldID>CONST</OrgfieldID>
        </Orgfield>
        <Orgfield>
          <OrgfieldName>add_days(get_date('today'), 30)</OrgfieldName>  {this is a workflow expression}
          <OrgfieldID>EXPRESSION</OrgfieldID>
        </Orgfield>
        <delimiter>;</delimiter>
      </Orgfields>
    </field>
    <field>
     .....
    </field>
  </fields>
 *************************************************************************************************/

require_once('modules/com_vtiger_workflow/include.inc');
require_once('modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc');
require_once('modules/com_vtiger_workflow/VTEntityMethodManager.inc');
require_once('modules/com_vtiger_workflow/VTSimpleTemplate.inc');
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');
require_once('modules/com_vtiger_workflow/expression_engine/include.inc');
require_once 'include/Webservices/Retrieve.php';

class Mapping extends processcbMap {

	/**
	 * $arguments[0] origin column_fields array
	 * $arguments[1] target column_fields array
	 */
	function processMap($arguments) {
		global $adb, $current_user;
		$mapping=$this->convertMap2Array();
		$ofields = $arguments[0];
		if (!empty($ofields['record_id'])) {
			$setype = getSalesEntityType($ofields['record_id']);
			$wsidrs = $adb->pquery('SELECT id FROM vtiger_ws_entity WHERE name=?',array($setype));
			$entityId = $adb->query_result($wsidrs, 0, 0).'x'.$ofields['record_id'];
		}
		$tfields = $arguments[1];
		foreach ($mapping['fields'] as $targetfield => $sourcefields) {
			$value = '';
			$delim = (isset($sourcefields['delimiter']) ? $sourcefields['delimiter'] : '');
			foreach ($sourcefields['merge'] as $pos => $fieldinfo) {
				$idx = array_keys($fieldinfo);
				if (strtoupper($idx[0])=='CONST') {
					$const = array_pop($fieldinfo);
					$value.= $const.$delim;
				} elseif (strtoupper($idx[0])=='EXPRESSION') {
					$testexpression = array_pop($fieldinfo);
					$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($testexpression)));
					$expression = $parser->expression();
					$exprEvaluater = new VTFieldExpressionEvaluater($expression);
					if(empty($ofields['record_id'])){
						$exprEvaluation = $exprEvaluater->evaluate(false);
					}else{
						$entity = new VTWorkflowEntity($current_user, $entityId);
						$exprEvaluation = $exprEvaluater->evaluate($entity);
					}
					$value.= $exprEvaluation.$delim;
				} else {
					$fieldname = array_pop($fieldinfo);
					$value.= $ofields[$fieldname].$delim;
				}
			}
			$value = rtrim($value,$delim);
			$tfields[$targetfield] = $value;
		}
		return $tfields;
	}

	function convertMap2Array() {
		$xml = $this->getXMLContent();
		$mapping=$target_fields=array();
		$mapping['origin'] = (String)$xml->originmodule->originname;
		$mapping['target'] = (String)$xml->targetmodule->targetname;
		foreach($xml->fields->field as $k=>$v) {
			$fieldname = (String)$v->fieldname;
			if(!empty($v->value)){
				$target_fields[$fieldname]['value'] = (String)$v->value;
			}
			$allmergeFields=array();
			foreach($v->Orgfields->Orgfield as $key=>$value) {
				$allmergeFields[]=array((String)$value->OrgfieldID=>(String)$value->OrgfieldName);
			}
			if(isset($v->Orgfields->delimiter))
				$target_fields[$fieldname]['delimiter']=(String)$v->Orgfields->delimiter;
			$target_fields[$fieldname]['merge']=$allmergeFields;
		}
		$mapping['fields'] = $target_fields;
		return $mapping;
	}

}
?>