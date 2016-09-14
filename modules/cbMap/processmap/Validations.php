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
 *  Module       : Business Mappings:: Module Field Validations Mapping
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
 * The accepted format is:
 <map>
  <originmodule>
    <originid>22</originid>  {optional}
    <originname>SalesOrder</originname>
  </originmodule>
  <fields>
    <field>
      <fieldname>subject</fieldname>   {field to validate}
      <fieldID>999</fieldID>  {optional}
      <validations>  {if more than one is present they must all pass to accept the value}
        <validation>
          <rule>{rule_name}</rule>
          <restrictions>
          <restriction>{values depend on the rule}</restriction>
          </restrictions>
        </validation>
        .....
      </validations>
    </field>
    <field>
     .....
    </field>
  </fields>

  where {rule_name} can be:
	required - Required field
		restrictions: none
	equals - Field must match another field (email/password confirmation)
		restrictions: name of the other field
	different - Field must be different than another field
		restrictions: name of the other field
	accepted - Checkbox or Radio must be accepted (yes, on, 1, true)
		restrictions: none
	numeric - Must be numeric
		restrictions: none
	integer - Must be integer number
		restrictions: none
	array - Must be array
		restrictions: none
	length - String must be certain length
		restrictions: number
	lengthBetween - String must be between given lengths
		restrictions: two restriction of type number
	lengthMin - String must be greater than given length
		restrictions: number
	lengthMax - String must be less than given length
		restrictions: number
	min - Minimum
		restrictions: number
	max - Maximum
		restrictions: number
	in - Performs in_array check on given array values
		restrictions: list of values of the array
	notIn - Negation of in rule (not in array of values)
		restrictions: list of values of the array
	ip - Valid IP address
		restrictions: IP number
	email - Valid email address
		restrictions: none
	url - Valid URL
		restrictions: none
	urlActive - Valid URL with active DNS record
		restrictions: none
	alpha - Alphabetic characters only
		restrictions: none
	alphaNum - Alphabetic and numeric characters only
		restrictions: none
	slug - URL slug characters (a-z, 0-9, -, _)
		restrictions: none
	regex - Field matches given regex pattern
		restrictions: regular expression. be careful you may have to put this inside a CDATA
	date - Field is a valid date
		restrictions: none
	dateFormat - Field is a valid date in the given format
		restrictions: date format
	dateBefore - Field is a valid date and is before the given date
		restrictions: date in ISO format
	dateAfter - Field is a valid date and is after the given date
		restrictions: date in ISO format
	contains - Field is a string and contains the given string
		restrictions: string
	creditCard - Field is a valid credit card number
		restrictions: list of accepted credit cards, if none given all supported cards will be checked
		supported cards: Visa visa, Mastercard mastercard, Dinersclub dinersclub, American Express amex or Discover discover
	IBAN_BankAccount - validate IBAN Bank Account number
		restrictions: none
	EU_VAT - validate EU VAT number
		restrictions: none
	notDuplicate - checks that no other record with the same value exists on the given fieldname
		restrictions: fieldname
 *************************************************************************************************/

require_once('modules/com_vtiger_workflow/include.inc');
require_once('modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc');
require_once('modules/com_vtiger_workflow/VTEntityMethodManager.inc');
require_once('modules/com_vtiger_workflow/VTSimpleTemplate.inc');
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');
require_once('modules/com_vtiger_workflow/expression_engine/include.inc');
require_once 'include/Webservices/Retrieve.php';
include_once 'include/validation/load_validations.php';

class Mapping extends processcbMap {

	/*
	 * $arguments[0] array with all the values to validate, the fieldname as the index of the array
	 * $arguments[1] crmid of the record being validated
	 */
	function processMap($arguments) {
		global $adb, $current_user;
		$mapping=$this->convertMap2Array();
		$tabid = getTabid($mapping['origin']);
		$v = new cbValidator($arguments[0]);
		$validations = array();
		foreach ($mapping['fields'] as $valfield => $vals) {
			$fl = $adb->pquery('select fieldlabel from vtiger_field where tabid=? and columnname=?', array($tabid,$valfield));
			$fieldlabel = $adb->query_result($fl, 0, 0);
			$i18n = getTranslatedString($fieldlabel,$mapping['origin']);
			foreach ($vals as $rule => $restrictions) {
				switch ($rule) {
					case 'required':
					case 'accepted':
					case 'numeric':
					case 'integer':
					case 'array':
					case 'email':
					case 'url':
					case 'urlActive':
					case 'alpha':
					case 'alphaNum':
					case 'slug':
					case 'date':
					case 'IBAN_BankAccount':
					case 'EU_VAT':
						$v->rule($rule, $valfield)->label($i18n);
						break;
					case 'equals':
					case 'different':
					case 'length':
					case 'lengthMin':
					case 'lengthMax':
					case 'min':
					case 'max':
					case 'ip':
					case 'dateFormat':
					case 'dateBefore':
					case 'dateAfter':
					case 'contains':
						$v->rule($rule, $valfield, $restrictions[0])->label($i18n);
						break;
					case 'lengthBetween':
						if ($restrictions[0]<$restrictions[1]) {
							$min = $restrictions[0];
							$max = $restrictions[1];
						} else {
							$min = $restrictions[1];
							$max = $restrictions[0];
						}
						$v->rule($rule, $valfield, $min, $max)->label($i18n);
						break;
					case 'in':
					case 'notIn':
						$v->rule($rule, $valfield, $restrictions)->label($i18n);
						break;
					case 'regex': // CDATA?
						$v->rule($rule, $valfield, $restrictions[0])->label($i18n);
						break;
					case 'creditCard':
						if (count($restrictions)>0) {
							$v->rule($rule, $valfield, $restrictions)->label($i18n);
						} else {
							$v->rule($rule, $valfield->label($i18n));
						}
						break;
					case 'notDuplicate':
						$v->rule($rule, $valfield, $restrictions[0], $mapping['origin'], $arguments[1])->label($i18n);
						break;
					default:
						continue;
						break;
				}
			}
			if(!$v->validate()) {
				$validations[$valfield] = $v->errors();
			}
		}
		return $validations;
	}

	function convertMap2Array() {
		$xml = $this->getXMLContent();
		$mapping=$val_fields=array();
		$mapping['origin'] = (String)$xml->originmodule->originname;
		foreach($xml->fields->field as $k=>$v) {
			$fieldname = (String)$v->fieldname;
			if(empty($fieldname)) continue;
			$allvals=array();
			foreach($v->validations->validation as $key=>$val) {
				$rule = (String)$val->rule;
				if(empty($rule)) continue;
				$rst = array();
				if (isset($val->restrictions)) {
					foreach($val->restrictions->restriction as $rk=>$rv) {
						$rst[]=(String)$rv;
					}
				}
				$allvals[$rule]=$rst;
			}
			$val_fields[$fieldname]['validations']=$allvals;
		}
		$mapping['fields'] = $val_fields;
		return $mapping;
	}

}
?>