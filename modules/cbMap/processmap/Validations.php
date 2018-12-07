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
		restrictions: none
	expression - accept a workflow expression and evaluate it in the context of the new screen values
		restrictions: map name or ID
	custom - launch custom function that can be found in the indicated file
		restrictions: file name, validation test name, function name and label to show on error (will be translated)
 *************************************************************************************************/

include_once 'include/validation/load_validations.php';

class Validations extends processcbMap {

	/*
	 * $arguments[0] array with all the values to validate, the fieldname as the index of the array
	 * $arguments[1] crmid of the record being validated
	 */
	public function processMap($arguments) {
		global $adb;
		$mapping=$this->convertMap2Array();
		$tabid = getTabid($mapping['origin']);
		$screen_values = $arguments[0];
		$v = new cbValidator($screen_values);
		$validations = array();
		foreach ($mapping['fields'] as $valfield => $vals) {
			$fl = $adb->pquery('select fieldlabel from vtiger_field where tabid=? and columnname=?', array($tabid,$valfield));
			$fieldlabel = $adb->query_result($fl, 0, 0);
			$i18n = getTranslatedString($fieldlabel, $mapping['origin']);
			foreach ($vals as $val) {
				if (isset($screen_values['action']) && $screen_values['action']=='MassEditSave' && empty($screen_values[$valfield.'_mass_edit_check'])) {
					continue; // we are not saving this field in mass edit save so we don't have to check it
				}
				$rule = $val['rule'];
				$restrictions = $val['rst'];
				switch ($rule) {
					case 'required':
					case 'accepted':
						if (isset($screen_values[$valfield])) {
							$v->rule($rule, $valfield)->label($i18n);
						}
						break;
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
						if (substr($restrictions[0], 0, 2)=='{{' && substr($restrictions[0], -2)=='}}'
							&& isset($screen_values[substr($restrictions[0], 2, strlen($restrictions[0])-4)])
						) {
							$rulevalue = $screen_values[substr($restrictions[0], 2, strlen($restrictions[0])-4)];
						} else {
							$rulevalue = $restrictions[0];
						}
						$v->rule($rule, $valfield, $rulevalue)->label($i18n);
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
						$v->rule($rule, $valfield, $mapping['origin'], $arguments[1])->label($i18n);
						break;
					case 'expression':
						$v->rule($rule, $valfield, $arguments[1], $restrictions[0])->label($i18n);
						break;
					case 'custom':
						if (file_exists($restrictions[0])) {
							@include_once $restrictions[0];
							if (function_exists($restrictions[2])) {
								$lbl = (isset($restrictions[3]) ? getTranslatedString($restrictions[3], $mapping['origin']) : getTranslatedString('INVALID', $mapping['origin']));
								$v->addRule($restrictions[1], $restrictions[2], $lbl);
								$v->rule($restrictions[1], $valfield)->label($i18n);
							}
						}
						break;
					default:
						continue;
						break;
				}
			}
		}
		if (!$v->validate()) {
			$validations = $v->errors();
		}
		if (count($validations)==0) {
			return true;
		} else {
			return $validations;
		}
	}

	private function convertMap2Array() {
		$xml = $this->getXMLContent();
		$mapping=$val_fields=array();
		$mapping['origin'] = (String)$xml->originmodule->originname;
		foreach ($xml->fields->field as $v) {
			$fieldname = (String)$v->fieldname;
			if (empty($fieldname)) {
				continue;
			}
			$allvals=array();
			foreach ($v->validations->validation as $val) {
				$retval = array();
				$retval['rule'] = (String)$val->rule;
				if (empty($retval['rule'])) {
					continue;
				}
				$rst = array();
				if (isset($val->restrictions)) {
					foreach ($val->restrictions->restriction as $rv) {
						$rst[]=(String)$rv;
					}
				}
				$retval['rst'] = $rst;
				$allvals[]=$retval;
			}
			$val_fields[$fieldname] = $allvals;
		}
		$mapping['fields'] = $val_fields;
		return $mapping;
	}

	public static function ValidationsExist($module) {
		global $adb;
		$q = 'select 1 from vtiger_cbmap
			inner join vtiger_crmentity on crmid=cbmapid
			where deleted=0 and maptype=? and targetname=? limit 1';
		$rs = $adb->pquery($q, array('Validations',$module));
		return ($rs && $adb->num_rows($rs)==1);
	}

	public static function processAllValidationsFor($module) {
		global $adb;
		$screen_values = json_decode($_REQUEST['structure'], true);
		if (in_array($module, getInventoryModules())) {
			$products = array();
			foreach ($screen_values as $sv_name => $sv) {
				if (strpos($sv_name, 'hdnProductId') !== false) {
					$i = substr($sv_name, 12);
					$qty_i = 'qty'.$i;
					$name_i = 'productName'.$i;
					$type_i = 'lineItemType'.$i;
					$products[$i]['crmid'] = $sv;
					$products[$i]['qty'] = $screen_values[$qty_i];
					$products[$i]['name'] = $screen_values[$name_i];
					$products[$i]['type'] = $screen_values[$type_i];
				}
			}
			$screen_values['pdoInformation'] = $products;
		}
		if (!empty($screen_values['record'])) {
			$module_to_edit = CRMEntity::getInstance($screen_values['module']);
			$module_to_edit->retrieve_entity_info($screen_values['record'], $screen_values['module']);
			foreach ($module_to_edit->column_fields as $key => $value) {
				$screen_values['current_'.$key] = $value;
			}
		}
		$record = (isset($_REQUEST['record']) ? vtlib_purify($_REQUEST['record']) : (isset($screen_values['record']) ? vtlib_purify($screen_values['record']) : 0));
		$q = 'select cbmapid from vtiger_cbmap
			inner join vtiger_crmentity on crmid=cbmapid
			where deleted=0 and maptype=? and targetname=?';
		$rs = $adb->pquery($q, array('Validations', $module));
		$focus = new cbMap();
		$focus->mode = '';
		$validation = true;
		while ($val = $adb->fetch_array($rs)) {
			$focus->id = $val['cbmapid'];
			$focus->retrieve_entity_info($val['cbmapid'], 'cbMap');
			$validation = $focus->Validations($screen_values, $record);
			if ($validation!==true) {
				break;
			}
		}
		return $validation;
	}

	public static function formatValidationErrors($errors, $module) {
		$error = '';
		foreach ($errors as $field => $errs) {
			foreach ($errs as $err) {
				$error.= $err . "\n";
			}
			if (strpos($error, "{custommsg|") > 0) {
				preg_match_all('/{(.*)}/', $error, $match);
				$res = explode('|', $match[1][0]);
				include_once 'include/validation/'.$res[1].'.php';
				$error = call_user_func(array(__NAMESPACE__ .$res[1], $res[2]), $field);
			}
		}
		return $error;
	}
}
?>