<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : Business Mappings
 *  Version      : 1.0
 *  Author       : Spike Associates
 *************************************************************************************************
*The accepted format is
 <?xml version="1.0"?>
 <map>
 <originmodule>
 <originname>Accounts</originname>
 </originmodule>
 <wsconfig>
 <wsurl>http://wsurl:port/api/v1/dealerorganization</wsurl>
 <wshttpmethod>POST</wshttpmethod>
<methodname>dealerorganizationcreation</methodname>
 <wsresponsetime></wsresponsetime>
 <wstype>REST</wstype>
 <wsuser></wsuser>
 <wspass></wspass>
 <wsproxyhost></wsproxyhost>
 <wsproxyport></wsproxyport>
<wsheader>
<header>
 <keyname>Content-type</keyname>
 <keyvalue>application/json</keyvalue>
 </header>
 <header>
 <keyname>Authorization:Bearer</keyname>
 <keyvalue>access_token</keyvalue>
 </header>
 </wsheader>
 <inputtype>JSON</inputtype>
 <outputtype>JSON</outputtype>
 </wsconfig>
<fields>
<field>
  <fieldname>sap_code</fieldname>
  <Orgfields>
  <Orgfield>
  <OrgfieldName>siccode</OrgfieldName>
 </Orgfield>
  <delimiter>--None--</delimiter>
 </Orgfields>
</field>
<field>
  <fieldname>customer_no</fieldname>
<Orgfields>
  <Orgfield>
  <OrgfieldName>account_no</OrgfieldName>
</Orgfield>
  <delimiter>--None--</delimiter>
  </Orgfields>
</field>
<field>
  <fieldname>company_name</fieldname>
<Orgfields>
  <Orgfield>
  <OrgfieldName>accountname</OrgfieldName>
</Orgfield>
  <delimiter>--None--</delimiter>
  </Orgfields>
</field>
<field>
  <fieldname>email</fieldname>
<Orgfields>
  <Orgfield>
  <OrgfieldName>email1</OrgfieldName>
</Orgfield>
  <delimiter>--None--</delimiter>
  </Orgfields>
</field>
<field>
  <fieldname>phone</fieldname>
<Orgfields>
  <Orgfield>
  <OrgfieldName>phone</OrgfieldName>
</Orgfield>
  <delimiter>--None--</delimiter>
  </Orgfields>
</field>
<field>
  <fieldname>website</fieldname>
<Orgfields>
  <Orgfield>
  <OrgfieldName>website</OrgfieldName>
</Orgfield>
  <delimiter>--None--</delimiter>
  </Orgfields>
</field>
<field>
  <fieldname>address</fieldname>
<Orgfields>
  <Orgfield>
  <OrgfieldName>bill_street</OrgfieldName>
</Orgfield>
  <delimiter>--None--</delimiter>
  </Orgfields>
</field>
<field>
  <fieldname>zip_code</fieldname>
<Orgfields>
  <Orgfield>
  <OrgfieldName>bill_code</OrgfieldName>
</Orgfield>
  <delimiter>--None--</delimiter>
  </Orgfields>
</field>
<field>
  <fieldname>country</fieldname>
<Orgfields>
  <Orgfield>
  <OrgfieldName>bill_country</OrgfieldName>
</Orgfield>
  <delimiter>--None--</delimiter>
  </Orgfields>
</field>
<field>
  <fieldname>city</fieldname>
<Orgfields>
  <Orgfield>
  <OrgfieldName>bill_city</OrgfieldName>
</Orgfield>
  <delimiter>--None--</delimiter>
  </Orgfields>
</field>
<field>
  <fieldname>state</fieldname>
<Orgfields>
  <Orgfield>
  <OrgfieldName>bill_state</OrgfieldName>
</Orgfield>
  <delimiter>--None--</delimiter>
  </Orgfields>
</field>
<field>
  <fieldname>account</fieldname>
<Orgfields>
	<Relfield>
  <RelfieldName>accountname</RelfieldName>
  <RelModule>Accounts</RelModule>
  <linkfield>account_id</linkfield>
  </Relfield>
  <delimiter>--None--</delimiter>
  </Orgfields>
</field>
</fields>
<Response>
  <field>
  <fieldname>message</fieldname>
  <Orgfields>
  <Orgfield>
  <OrgfieldName>siccode</OrgfieldName>
  </Orgfield>
  <delimiter>--None--</delimiter>
  </Orgfields>
  </field>
  <field>
  <fieldname>organization,_id</fieldname>
  <Orgfields>
  <Orgfield>
  <OrgfieldName>accountname</OrgfieldName>
  </Orgfield>
  <delimiter>--None--</delimiter>
  </Orgfields>
  </field>
  </Response>
 </map>
  *************************************************************************************************/

require_once 'modules/com_vtiger_workflow/include.inc';
require_once 'modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc';
require_once 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
require_once 'modules/com_vtiger_workflow/VTSimpleTemplate.inc';
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'modules/com_vtiger_workflow/expression_engine/include.inc';
require_once 'modules/com_vtiger_workflow/VTSimpleTemplateOnData.inc';
include_once 'modules/com_vtiger_workflow/expression_functions/cbexpSQL.php';
require_once 'include/Webservices/Retrieve.php';
require_once 'modules/cbMap/processmap/Mapping.php';

class WebserviceMapping extends processcbMap {

	public function processMap($arguments) {
		global $current_user;
		$mapping=$this->convertMap2Array();
		if (isset($arguments[1]) && is_array($arguments[1])) {
			$ofields = array_merge($arguments[1], $arguments[0]);
			$ctx = $arguments[1];
		} else {
			$ofields = $arguments[0];
			$ctx = array();
		}
		if (!empty($ofields['record_id'])) {
			$setype = getSalesEntityType($ofields['record_id']);
			$entityId = vtws_getId(vtws_getEntityId($setype), $ofields['record_id']);
		}
		if (empty($ofields['assigned_user_id'])) {
			$userwsid = vtws_getEntityId('Users');
			$ofields['assigned_user_id'] = vtws_getId($userwsid, $current_user->id);
		}
		$tfields = array();
		foreach ($mapping['fields'] as $targetfield => $sourcefields) {
			$value = '';
			$delim = (isset($sourcefields['delimiter']) ? $sourcefields['delimiter'] : '');
			if (isset($sourcefields['relatedFields']) && !empty($sourcefields['relatedFields'])) {
				for ($i = 0; $i < count($sourcefields['relatedFields']); $i++) {
					$relInformation = $sourcefields['relatedFields'][$i];
					$relModule = $relInformation['relmodule'];
					$linkField = $relInformation['linkfield'];
					$fieldName = $relInformation['fieldname'];
					$otherid = $ofields[$linkField];
					if (!empty($otherid)) {
						$otherModule = CRMEntity::getInstance($relModule);
						$otherModule->retrieve_entity_info($otherid, $relModule);
						$takediv = explode(',', $relInformation['linkvalue']);
						$takedivconcat = '';
						if (count($takediv)>1) {
							for ($b = 0; $b < count($takediv); $b++) {
								if (strpos($takediv[$b], "'") !== false) {
									$takedivconcat.= substr($takediv[$b], 1, -1);
								} else {
									$takedivconcat.= $otherModule->column_fields[$takediv[$b]];
								}
							}
						} else {
							$takedivconcat.= $otherModule->column_fields[$fieldName];
						}
						$value = $takedivconcat;
					}
				}
			}

			if (isset($sourcefields['merge'])) {
				foreach ($sourcefields['merge'] as $fieldinfo) {
					$postProcess = '';
					if (!empty($fieldinfo['postProcess'])) {
						$postProcess = $fieldinfo['postProcess'];
						unset($fieldinfo['postProcess']);
					}
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
							if ($ofields['record_id']=='0x0') {
								$entity = new cbexpsql_environmentstub($mapping['origin'], '0x0');
							} else {
								$entity = new VTWorkflowEntity($current_user, $entityId);
							}
							$entity->setContext($ctx);
							$exprEvaluation = $exprEvaluater->evaluate($entity);
						}
						if (is_array($exprEvaluation)) {
							if (is_array($value)) {
								$value = array_merge($value, $exprEvaluation);
							} else {
								$value = $exprEvaluation;
							}
						} else {
							$value.= $exprEvaluation.$delim;
						}
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
					} elseif (strtoupper($idx[0])=='RULE') {
						$mapid = array_pop($fieldinfo);
						$fieldname = array_pop($fieldinfo);
						if (!empty($ofields['record_id'])) {
							$context = $ofields;
							if (strpos($context['record_id'], 'x')===false) {
								$context['record_id'] = vtws_getEntityId(getSalesEntityType($context['record_id'])).'x'.$context['record_id'];
							}
							$entity = new VTWorkflowEntity($current_user, $context['record_id'], true);
							if (is_array($entity->data)) { // valid context
								$context = array_merge($entity->data, $context);
							}
						} else {
							$context = $ofields[$fieldname];
						}
						$value .= coreBOS_Rule::evaluate($mapid, $context).$delim;
					} else {
						$fieldname = array_pop($fieldinfo);
						$value.= (isset($ofields[$fieldname]) ? decode_html($ofields[$fieldname]) : '').$delim;
					}
					if ($postProcess!='') {
						$value = Mapping::postProcess($postProcess, $value);
					}
				}
			}
			if (is_string($value)) {
				$value = rtrim($value, $delim);
			}
			if ($targetfield =='Response' || $targetfield =='WSConfig') {
				$value = $sourcefields;
			}
			$tfields[$targetfield] = $value;
		}
		$mapping['fields'] = $tfields;
		return $mapping;
	}

	public function convertMap2Array() {
		$xml = $this->getXMLContent();
		if (empty($xml)) {
			return array();
		}
		$mapping=$target_fields=array();
		$target_fields1 = array();
		$mapping['origin'] = (string)$xml->originmodule->originname;
		foreach ($xml->fields->field as $v) {
			$fieldname = (string)$v->fieldname;
			if (!empty($v->value)) {
				$target_fields[$fieldname]['value'] = (string)$v->value;
			} elseif (!empty($v->Orgfields[0]->Orgfield) && isset($v->Orgfields[0]->Orgfield)) {
				$allmergeFields = array();
				foreach ($v->Orgfields->Orgfield as $value) {
					$arr = array((string)$value->OrgfieldID=>(string)$value->OrgfieldName);
					if (isset($value->postProcess)) {
						$arr['postProcess'] = (string)$value->postProcess;
					}
					$allmergeFields[] = $arr;
				}
				if (isset($v->Orgfields->delimiter)) {
					$target_fields[$fieldname]['delimiter'] = (string)$v->Orgfields->delimiter;
				}
				$target_fields[$fieldname]['merge'] = $allmergeFields;
				if (isset($v->master)) {
					$target_fields[$fieldname]['master'] = filter_var((string)$v->master, FILTER_VALIDATE_BOOLEAN);
				} else {
					$target_fields[$fieldname]['master'] = false;
				}
			} elseif (!empty($v->Orgfields[0]->Relfield) && isset($v->Orgfields[0]->Relfield)) {
				$allRelValues = array();
				$allmergeFields = array();
				foreach ($v->Orgfields->Relfield as $value1) {
					$allRelValues = array(
						'fieldname'=>(string)$value1->RelfieldName,
						'relmodule'=>(string)$value1->RelModule,
						'linkfield'=>(string)$value1->linkfield,
						'linkvalue'=>isset($value1->Relfieldvalue) ? (string)$value1->Relfieldvalue : '',
					);
				}
				$allmergeFields[] = $allRelValues;
				if (isset($v->Orgfields[0]->Relfield->delimiter)) {
					$target_fields[$fieldname]['delimiter'] = (string)$v->Orgfields[0]->Relfield->delimiter;
				}
				$target_fields[$fieldname]['relatedFields'] = $allmergeFields;
			}
		}
		$mapping['fields'] = $target_fields;
		//response block
		if (!empty($xml->Response[0]) && isset($xml->Response[0])) {
			foreach ($xml->Response[0] as $v) {
				$fieldname1 = (string) $v->fieldname;
				$target_fields1[$fieldname1] = array(
					'field' => (empty($v->destination->field) ? '' : (string)$v->destination->field),
					'context' => (empty($v->destination->context) ? 'wsctx_'.$fieldname1 : (string)$v->destination->context),
				);
			}
		}
		$mapping['Response'] = $target_fields1;

		//ws config block
		if (!empty($xml->wsconfig[0]) && isset($xml->wsconfig[0])) {
			$ind = 0;
			$headers = array();
			foreach ($xml->wsconfig[0] as $k => $v) {
				if ($k != 'wsheader') {
					$target_fields2[$k] = (string)$v[0];
				} else {
					$hd = $v[0];
					foreach ($hd as $v1) {
						$hdr = array();
						foreach ($hd->header[$ind] as $v2) {
							$hdr[] = $v2;
						}
						$formhdr = implode(':', $hdr);
						$ind++;
						$headers[] = $formhdr;
					}
					$target_fields2[$k] = $headers;
				}
			}
		}
		$mapping['WSConfig'] = $target_fields2;
		return $mapping;
	}
}
?>