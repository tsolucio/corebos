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
	  <master>true|false</master> {optional: used for integration mapping between two systems}
	</field>
	<field>
	  <fieldname>description</fieldname>   {destination field on invoice}
	  <Orgfields>  {if more than one is present they will be concatenated with the delimiter}
		<Orgfield>
		  <OrgfieldName>$(assigned_user_id : (Users) first_name)</OrgfieldName>
		  <OrgfieldID>FIELD</OrgfieldID>
		</Orgfield>
		<Orgfield>
		  <OrgfieldName>$(assigned_user_id : (Users) last_name)</OrgfieldName>
		  <OrgfieldID>FIELD</OrgfieldID>
		</Orgfield>
		<Orgfield>
		  <OrgfieldName>The user assigned to the Sales Order is: $(assigned_user_id : (Users) first_name) $(assigned_user_id : (Users) last_name)</OrgfieldName>  {this is a constant string}
		  <OrgfieldID>TEMPLATE</OrgfieldID>
		</Orgfield>
		<delimiter> - </delimiter>
	  </Orgfields>
	</field>
	<field>
	 .....
	</field>
  </fields>
 *************************************************************************************************/

require_once 'modules/com_vtiger_workflow/include.inc';
require_once 'modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc';
require_once 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
require_once 'modules/com_vtiger_workflow/VTSimpleTemplate.inc';
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'modules/com_vtiger_workflow/expression_engine/include.inc';
require_once 'modules/com_vtiger_workflow/VTSimpleTemplateOnData.inc';
require_once 'include/Webservices/Retrieve.php';

class Mapping extends processcbMap {

	/**
	 * $arguments[0] origin column_fields array
	 * $arguments[1] target column_fields array
	 */
	public function processMap($arguments) {
		global $current_user;
		$mapping=$this->convertMap2Array();
		$ofields = $arguments[0];
		if (!empty($ofields['record_id'])) {
			$setype = getSalesEntityType($ofields['record_id']);
			$entityId = vtws_getId(vtws_getEntityId($setype), $ofields['record_id']);
		}
		if (empty($ofields['assigned_user_id'])) {
			$userwsid = vtws_getEntityId('Users');
			$ofields['assigned_user_id'] = vtws_getId($userwsid, $current_user->id);
		}
		$tfields = $arguments[1];
		foreach ($mapping['fields'] as $targetfield => $sourcefields) {
			$value = '';
			$delim = (isset($sourcefields['delimiter']) ? $sourcefields['delimiter'] : '');
			foreach ($sourcefields['merge'] as $fieldinfo) {
				$idx = array_keys($fieldinfo);
				if (strtoupper($idx[0])=='CONST') {
					$const = array_pop($fieldinfo);
					$value.= $const.$delim;
				} elseif (strtoupper($idx[0])=='EXPRESSION') {
					$testexpression = array_pop($fieldinfo);
					$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($testexpression)));
					$expression = $parser->expression();
					$exprEvaluater = new VTFieldExpressionEvaluater($expression);
					if (empty($ofields['record_id'])) {
						$exprEvaluation = $exprEvaluater->evaluate(false);
					} else {
						$entity = new VTWorkflowEntity($current_user, $entityId);
						$exprEvaluation = $exprEvaluater->evaluate($entity);
					}
					$value.= $exprEvaluation.$delim;
				} elseif (!empty($ofields['record_id']) && (strtoupper($idx[0])=='FIELD' || strtoupper($idx[0])=='TEMPLATE')) {
					$util = new VTWorkflowUtils();
					$adminUser = $util->adminUser();
					$entityCache = new VTEntityCache($adminUser);
					$testexpression = array_pop($fieldinfo);
					if (strtoupper($idx[0])=='FIELD') {
						$testexpression = trim($testexpression);
						if (substr($testexpression, 0, 1) != '$') {
							$testexpression = '$' . $testexpression;
						}
					}
					$ct = new VTSimpleTemplate($testexpression);
					$value.= $ct->render($entityCache, $entityId).$delim;
					$util->revertUser();
				} elseif (empty($ofields['record_id']) && (strtoupper($idx[0])=='FIELD' || strtoupper($idx[0])=='TEMPLATE')) {
					$util = new VTWorkflowUtils();
					$adminUser = $util->adminUser();
					$entityCache = new VTEntityCache($adminUser);
					$testexpression = array_pop($fieldinfo);
					if (strtoupper($idx[0])=='FIELD') {
						$testexpression = trim($testexpression);
						if (substr($testexpression, 0, 1) != '$') {
							$testexpression = '$' . $testexpression;
						}
					}
					$ct = new VTSimpleTemplateOnData($testexpression);
					$value.= $ct->render($entityCache, $mapping['origin'], $ofields).$delim;
					$util->revertUser();
				} else {
					$fieldname = array_pop($fieldinfo);
					$value.= (isset($ofields[$fieldname]) ? $ofields[$fieldname] : '').$delim;
				}
			}
			$value = rtrim($value, $delim);
			$tfields[$targetfield] = $value;
		}
		return $tfields;
	}

	public function convertMap2Array() {
		$xml = $this->getXMLContent();
		$mapping=$target_fields=array();
		$mapping['origin'] = (String)$xml->originmodule->originname;
		$mapping['target'] = (String)$xml->targetmodule->targetname;
		foreach ($xml->fields->field as $v) {
			$fieldname = (String)$v->fieldname;
			if (!empty($v->value)) {
				$target_fields[$fieldname]['value'] = (String)$v->value;
			}
			$allmergeFields=array();
			foreach ($v->Orgfields->Orgfield as $value) {
				$allmergeFields[]=array((String)$value->OrgfieldID=>(String)$value->OrgfieldName);
			}
			if (isset($v->Orgfields->delimiter)) {
				$target_fields[$fieldname]['delimiter']=(String)$v->Orgfields->delimiter;
			}
			$target_fields[$fieldname]['merge']=$allmergeFields;
			if (isset($v->master)) {
				$target_fields[$fieldname]['master'] = filter_var((String)$v->master, FILTER_VALIDATE_BOOLEAN);
			} else {
				$target_fields[$fieldname]['master'] = false;
			}
		}
		$mapping['fields'] = $target_fields;
		return $mapping;
	}
}
?>