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
		  <message>This is my custom msg for field: {field}</message> {optional}
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
	requiredWith - Field is required if any other fields are present
		restrictions: list of fields
	requiredWithout - Field is required if any other fields are NOT present
		restrictions: list of fields
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
require_once 'data/CRMEntity.php';
class Validations extends processcbMap {

	/*
	 * $arguments[0] array with all the values to validate, the fieldname as the index of the array
	 * $arguments[1] crmid of the record being validated
	 */
	public function processMap($arguments) {
		$mapping=$this->convertMap2Array();
		$tabid = getTabid($mapping['origin']);
		if (isset($arguments[2]) && $arguments[2]) {
			$mapping = self::addFieldValidations($mapping, $tabid);
		}
		return self::doValidations($mapping, $arguments[0], $arguments[1], $tabid);
	}

	public static function doValidations($mapping, $screen_values, $record, $tabid) {
		global $adb, $current_user, $log;
		foreach ($mapping['fields'] as $valfield => $vals) {
			if (isset($screen_values['action']) && $screen_values['action']=='DetailViewEdit' && $screen_values['dtlview_edit_fieldcheck']!=$valfield
			&& !isset($screen_values[$valfield]) && isset($screen_values['current_'.$valfield])
			) {
				$screen_values[$valfield] = $screen_values['current_'.$valfield];
			}
		}
		if (!empty($screen_values['module'])) {
			$webserviceObject = VtigerWebserviceObject::fromName($adb, $screen_values['module']);
			$handlerPath = $webserviceObject->getHandlerPath();
			$handlerClass = $webserviceObject->getHandlerClass();
			require_once $handlerPath;
			$handler = new $handlerClass($webserviceObject, $current_user, $adb, $log);
			$meta = $handler->getMeta();
			$screen_values = DataTransform::sanitizeDateFieldsForDB($screen_values, $meta);
		}
		$v = new cbValidator($screen_values);
		$validations = array();
		foreach ($mapping['fields'] as $valfield => $vals) {
			$fl = $adb->pquery('select fieldlabel from vtiger_field where tabid=? and (columnname=? or fieldname=?)', array($tabid, $valfield, $valfield));
			if ($fl && $adb->num_rows($fl)>0) {
				$fieldlabel = $adb->query_result($fl, 0, 0);
			} else {
				$fieldlabel = $valfield;
			}
			$i18n = getTranslatedString($fieldlabel, $mapping['origin']);
			foreach ($vals as $val) {
				if (isset($screen_values['action']) && $screen_values['action']=='MassEditSave' && empty($screen_values[$valfield.'_mass_edit_check'])) {
					continue; // we are not saving this field in mass edit save so we don't have to check it
				}
				if (isset($val['msg'])) {
					$val['msg'] = getTranslatedString($val['msg'], $mapping['origin']);
				}
				$rule = $val['rule'];
				$restrictions = $val['rst'];
				switch ($rule) {
					case 'required':
					case 'optional':
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
					case 'cbtaxclassrequired':
						if (isset($val['msg'])) {
							$v->rule($rule, $valfield)->message($val['msg'])->label($i18n);
						} else {
							$v->rule($rule, $valfield)->label($i18n);
						}
						break;
					case 'equals':
					case 'different':
					case 'length':
					case 'lengthMin':
					case 'lengthMax':
					case 'min':
					case 'max':
					case 'bigger':
					case 'greater':
					case 'smaller':
					case 'lesser':
					case 'ip':
					case 'dateFormat':
					case 'dateBefore':
					case 'dateAfter':
					case 'dateEqualOrAfter':
					case 'contains':
					case 'RelatedModuleExists':
						if ($rule=='greater' || $rule=='bigger') {
							$rule='min';
						}
						if ($rule=='smaller' || $rule=='lesser') {
							$rule='max';
						}
						if (substr($restrictions[0], 0, 2)=='{{' && substr($restrictions[0], -2)=='}}'
							&& isset($screen_values[substr($restrictions[0], 2, strlen($restrictions[0])-4)])
						) {
							$rulevalue = $screen_values[substr($restrictions[0], 2, strlen($restrictions[0])-4)];
						} else {
							$rulevalue = $restrictions[0];
						}
						if (isset($val['msg'])) {
							$v->rule($rule, $valfield, $rulevalue)->message($val['msg'])->label($i18n);
						} else {
							$v->rule($rule, $valfield, $rulevalue)->label($i18n);
						}
						break;
					case 'lengthBetween':
						if ($restrictions[0]<$restrictions[1]) {
							$min = $restrictions[0];
							$max = $restrictions[1];
						} else {
							$min = $restrictions[1];
							$max = $restrictions[0];
						}
						if (isset($val['msg'])) {
							$v->rule($rule, $valfield, $min, $max)->message($val['msg'])->label($i18n);
						} else {
							$v->rule($rule, $valfield, $min, $max)->label($i18n);
						}
						break;
					case 'in':
					case 'In':
					case 'notin':
					case 'notIn':
						if ($rule=='In') {
							$rule = 'in';
						}
						if ($rule=='notin') {
							$rule = 'notIn';
						}
						foreach ($restrictions as $rky => $rest) {
							if (substr($rest, 0, 2)=='{{' && substr($rest, -2)=='}}' && isset($screen_values[substr($rest, 2, strlen($rest)-4)])) {
								$restrictions[$rky] = $screen_values[substr($rest, 2, strlen($rest)-4)];
							}
						}
						if (isset($val['msg'])) {
							$v->rule($rule, $valfield, $restrictions)->message($val['msg'])->label($i18n);
						} else {
							$v->rule($rule, $valfield, $restrictions)->label($i18n);
						}
						break;
					case 'regex': // CDATA?
						if (isset($val['msg'])) {
							$v->rule($rule, $valfield, $restrictions[0])->message($val['msg'])->label($i18n);
						} else {
							$v->rule($rule, $valfield, $restrictions[0])->label($i18n);
						}
						break;
					case 'creditCard':
						if (!empty($restrictions)) {
							if (isset($val['msg'])) {
								$v->rule($rule, $valfield, $restrictions)->message($val['msg'])->label($i18n);
							} else {
								$v->rule($rule, $valfield, $restrictions)->label($i18n);
							}
						} else {
							if (isset($val['msg'])) {
								$v->rule($rule, $valfield)->message($val['msg'])->label($i18n);
							} else {
								$v->rule($rule, $valfield)->label($i18n);
							}
						}
						break;
					case 'requiredWith':
					case 'requiredWithout':
						if (isset($val['msg'])) {
							$v->rule($rule, $valfield, $restrictions)->message($val['msg'])->label($i18n);
						} else {
							$v->rule($rule, $valfield, $restrictions)->label($i18n);
						}
						break;
					case 'notDuplicate':
						if (isset($val['msg'])) {
							$v->rule($rule, $valfield, $mapping['origin'], $record, $restrictions)->message($val['msg'])->label($i18n);
						} else {
							$v->rule($rule, $valfield, $mapping['origin'], $record, $restrictions)->label($i18n);
						}
						break;
					case 'expression':
						if (isset($val['msg'])) {
							$v->rule($rule, $valfield, $record, $restrictions[0])->message($val['msg'])->label($i18n);
						} else {
							$v->rule($rule, $valfield, $record, $restrictions[0])->label($i18n);
						}
						break;
					case 'custom':
						if (file_exists($restrictions[0])) {
							@include_once $restrictions[0];
							if (function_exists($restrictions[2])) {
								$lbl = getTranslatedString('INVALID', $mapping['origin']);
								$params = array();
								if (isset($restrictions[3])) {
									if (is_array($restrictions[3])) {
										$params = $restrictions[3];
									} else {
										$lbl = getTranslatedString($restrictions[3], $mapping['origin']);
									}
								}
								if (isset($restrictions[4])) {
									$params = $restrictions[4];
								}
								$v->addRule($restrictions[1], $restrictions[2], $lbl);
								$customValidationMessageFunction = $restrictions[1].'GetMessage';
								if (function_exists($customValidationMessageFunction)) {
									$val['msg'] = $customValidationMessageFunction($valfield, $screen_values[$valfield], $params, $screen_values, $val['msg']);
								}
								if (isset($val['msg'])) {
									$v->rule($restrictions[1], $valfield, $params)->message($val['msg'])->label($i18n);
								} else {
									$v->rule($restrictions[1], $valfield, $params)->label($i18n);
								}
							}
						}
						break;
					default:
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
		if (empty($xml)) {
			return array();
		}
		$mapping=$val_fields=array();
		$mapping['origin'] = (string)$xml->originmodule->originname;
		foreach ($xml->fields->field as $v) {
			$fieldname = (string)$v->fieldname;
			if (empty($fieldname)) {
				continue;
			}
			$allvals=array();
			foreach ($v->validations->validation as $val) {
				$retval = array();
				$retval['rule'] = (string)$val->rule;
				if (empty($retval['rule'])) {
					continue;
				}
				$rst = array();
				if (isset($val->restrictions)) {
					foreach ($val->restrictions->restriction as $rv) {
						$rst[]=(string)$rv;
					}
					if (isset($val->restrictions->parameters)) {
						$params = array();
						foreach ($val->restrictions->parameters->parameter as $prm) {
							$params[(string)$prm->name]=(string)$prm->value;
						}
						$rst[]=$params;
					}
				}
				$retval['rst'] = $rst;
				if (isset($val->message)) {
					$retval['msg']=(string)$val->message;
				}
				$allvals[]=$retval;
			}
			$val_fields[$fieldname] = $allvals;
		}
		$mapping['fields'] = $val_fields;
		return $mapping;
	}

	private static function addFieldValidations($mapping, $tabid) {
		global $adb;
		// add typeofdata validations to repeat them here in case someone hijacks them in the browser
		$validationData = getDBValidationData(array(), $tabid);
		foreach ($validationData as $fname => $finfo) {
			foreach ($finfo as $fvalidation) {
				if (substr($fvalidation, 0, 2)=='I~') {
					if (isset($mapping['fields'][$fname])) {
						$mapping['fields'][$fname][] = array('rule'=>'integer', 'rst'=>array());
					} else {
						$mapping['fields'][$fname] = array(array('rule'=>'integer', 'rst'=>array()));
					}
				}
				if (substr($fvalidation, 0, 2)=='N~') {
					if (isset($mapping['fields'][$fname])) {
						$mapping['fields'][$fname][] = array('rule'=>'min', 'rst'=>array(0));
					} else {
						$mapping['fields'][$fname] = array(array('rule'=>'min', 'rst'=>array(0)));
					}
				}
				if (strpos($fvalidation, '~M')) {
					if ($fname=='taxclass') {
						unset($mapping['fields'][$fname]);
						$mapping['fields']['tax1_check'] = array(array('rule'=>'cbtaxclassrequired', 'rst' => array(), 'msg'=> getTranslatedString('ENTER_VALID_TAX')));
					} else {
						if (isset($mapping['fields'][$fname])) {
							$mapping['fields'][$fname][] = array('rule'=>'required', 'rst'=>array());
						} else {
							$mapping['fields'][$fname] = array(array('rule'=>'required', 'rst'=>array()));
						}
					}
				}
				if (strpos($fvalidation, '~OTH~')) { //D~O~OTH~GE~support_start_date~Support Start Date
					$val = explode('~', $fvalidation);
					switch ($val[3]) {
						case 'GE':
							$comparison = 'dateEqualOrAfter';
							break;
						default:
							$comparison = 'dateAfter';
							break;
					}
					if (isset($mapping['fields'][$fname])) {
						$mapping['fields'][$fname][] = array('rule'=>$comparison, 'rst'=>array('{{'.$val[4].'}}'));
					} else {
						$mapping['fields'][$fname] = array(array('rule'=>$comparison, 'rst'=>array('{{'.$val[4].'}}')));
					}
				}
			}
		}
		// add mysql strict varchar size checks
		// we don't validate checkboxes nor autonumber fields
		$novalrs = $adb->pquery('select columnname from vtiger_field where uitype in (?,?) and tabid=?', array('56', '4', $tabid));
		$novalfields = array();
		while (!$novalrs->EOF) {
			$cl = $novalrs->FetchRow();
			$novalfields[] = strtoupper($cl['columnname']);
		}
		$module = getTabModuleName($tabid);
		$crme = CRMEntity::getInstance($module);
		foreach ($crme->tab_name as $tablename) {
			if ($tablename=='vtiger_crmentity') {
				continue;
			}
			foreach ($adb->database->MetaColumns($tablename) as $fname => $finfo) {
				if ($finfo->type == 'varchar' && !in_array($fname, $novalfields)) {
					$fname = strtolower($fname);
					if (isset($mapping['fields'][$fname])) {
						$mapping['fields'][$fname][] = array('rule'=>'lengthMax', 'rst'=>array($finfo->max_length));
					} else {
						$mapping['fields'][$fname] = array(array('rule'=>'lengthMax', 'rst'=>array($finfo->max_length)));
					}
				}
			}
		}
		return $mapping;
	}

	/**
	 * We just return true because all modules have some validation now that we are checking them all again
	 * at the very least they are going to have the MySQL varchar limit check and that is in the case that
	 * all other validations on the module are deactivated (integer, number, ...)
	 */
	public static function ValidationsExist($module) {
		return true;
	}

	public static function recordIsAssignedToInactiveUser() {
		$screen_values = json_decode($_REQUEST['structure'], true);
		if (isset($screen_values['assigned_user_id'])) {
			global $adb;
			$usrrs = $adb->pquery('select status from vtiger_users where id=?', array($screen_values['assigned_user_id']));
			if ($usrrs && $adb->num_rows($usrrs)==1) {
				return ($adb->query_result($usrrs, 0, 'status')!='Active');
			} else {
				$grprs = $adb->pquery('select 1 from vtiger_groups where groupid=?', array($screen_values['assigned_user_id']));
				return !($grprs && $adb->num_rows($grprs)==1);
			}
		} elseif ($screen_values['module']=='Users') {
			return false;
		} else {
			return recordIsAssignedToInactiveUser($screen_values['record']);
		}
	}

	public static function loadProductValuesFromScreenValues($screen_values) {
		$products = array();
		foreach ($screen_values as $sv_name => $sv) {
			if (strpos($sv_name, 'hdnProductId') !== false) {
				$i = substr($sv_name, 12);
				$qty_i = 'qty'.$i;
				$name_i = 'productName'.$i;
				$type_i = 'lineItemType'.$i;
				$deleted_i = 'deleted'.$i;
				$products[$i]['crmid'] = $sv;
				$products[$i]['qty'] = $screen_values[$qty_i];
				$products[$i]['name'] = $screen_values[$name_i];
				$products[$i]['type'] = $screen_values[$type_i];
				$products[$i]['deleted'] = $screen_values[$deleted_i];
			}
		}
		return $products;
	}

	public static function processAllValidationsFor($module) {
		global $adb, $current_user;
		$screen_values = json_decode($_REQUEST['structure'], true);
		$handler = vtws_getModuleHandlerFromName($module, $current_user);
		$meta = $handler->getMeta();
		$screen_values = DataTransform::sanitizeCurrencyFieldsForDB($screen_values, $meta);
		if (in_array($module, getInventoryModules())) {
			$screen_values['pdoInformation'] = Validations::loadProductValuesFromScreenValues($screen_values);
		}
		$record = (isset($_REQUEST['record']) ? vtlib_purify($_REQUEST['record']) : (isset($screen_values['record']) ? vtlib_purify($screen_values['record']) : 0));
		if (!empty($record)) {
			$screen_values['record'] = $record;
			$module_to_edit = CRMEntity::getInstance($screen_values['module']);
			$module_to_edit->retrieve_entity_info($screen_values['record'], $screen_values['module']);
			foreach ($module_to_edit->column_fields as $key => $value) {
				$screen_values['current_'.$key] = $value;
			}
			if ($screen_values['module']=='Products' || $screen_values['module']=='Services') {
				unset($screen_values['current_taxclass']);
				$tax_details = getTaxDetailsForProduct($screen_values['record'], 'available_associated');
				foreach ($tax_details as $tax) {
					$tax_value = getProductTaxPercentage($tax['taxname'], $screen_values['record']);
					if ($tax_value!='') {
						$screen_values['current_'.$tax['taxname']] = $tax['percentage'];
					}
				}
			}
		}
		$valmaps = array();
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('cbMap');
		$q = 'select cbmapid
			from vtiger_cbmap
			inner join '.$crmEntityTable." on vtiger_crmentity.crmid=cbmapid
			where deleted=0 and maptype=? and targetname=? and mapname like '%_Validations'";
		$rs = $adb->pquery($q, array('Validations', $module));
		while ($val = $adb->fetch_array($rs)) {
			$valmaps[] = $val['cbmapid'];
		}
		$crmGvEntityTable = CRMEntity::getcrmEntityTableAlias('GlobalVariable');
		$q = 'select globalvariableid, bmapid
			from vtiger_globalvariable
			inner join '.$crmGvEntityTable.' on vtiger_crmentity.crmid=globalvariableid
			where vtiger_crmentity.deleted=0 and gvname=? and module_list=? and bmapid!=0 and bmapid is not null';
		$rs = $adb->pquery($q, array('BusinessMapping_Validations', $module));
		if ($rs && $adb->num_rows($rs)>0) {
			while ($gv = $adb->fetch_array($rs)) {
				if (GlobalVariable::isAppliable($gv['globalvariableid'], $module, $current_user->id)) {
					$valmaps[] = $gv['bmapid'];
				}
			}
		}
		$valmaps = array_unique($valmaps);
		$validation = true;
		if (!empty($valmaps)) {
			$focus = new cbMap();
			$focus->mode = '';
			$addFieldValidations = true;
			foreach ($valmaps as $val) {
				$focus->id = $val;
				$focus->retrieve_entity_info($val, 'cbMap');
				$validation = $focus->Validations($screen_values, $record, $addFieldValidations);
				$addFieldValidations = false;
				if ($validation!==true) {
					break;
				}
			}
		} else {
			$tabid = getTabid($module);
			$mapping = self::addFieldValidations(array(), $tabid);
			$mapping['origin'] = $module;
			$validation = self::doValidations($mapping, $screen_values, $record, $tabid);
		}
		return $validation;
	}

	public static function formatValidationErrors($errors, $module) {
		$error = '';
		foreach ($errors as $field => $errs) {
			foreach ($errs as $err) {
				$error.= $err . "\n";
			}
			if (strpos($error, '{custommsg|') > 0) {
				preg_match_all('/{(.*)}/', $error, $match);
				$res = explode('|', $match[1][0]);
				include_once 'include/validation/'.$res[1].'.php';
				$error = call_user_func(array(__NAMESPACE__ .$res[1], $res[2]), $field);
			}
		}
		return nl2br($error);
	}

	public static function flattenMultipicklistArrays($fields) {
		return array_map(
			function ($value) {
				return is_array($value) ? implode(Field_Metadata::MULTIPICKLIST_SEPARATOR, $value) : $value;
			},
			$fields
		);
	}
}
?>
