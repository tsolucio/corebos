<?php
/*************************************************************************************************
 * Copyright 2012 JPL TSolucio, S.L.   --   This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
*************************************************************************************************
*  Module       : OOMerge GENDOC
*  Version      : 5.3.0
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/
include_once 'config.inc.php';
require_once 'include/logging.php';
require_once 'data/Tracker.php';
require_once 'include/utils/utils.php';
require_once 'modules/evvtgendoc/OpenDocument.php';
require_once 'data/CRMEntity.php';
if (file_exists('modules/evvtgendoc/commands_'. OpenDocument::$compile_language . '.php')) {
	include 'modules/evvtgendoc/commands_'. OpenDocument::$compile_language . '.php';
} else {
	include 'modules/evvtgendoc/commands_en.php';
}

//Array de moduls relacionats amb el que estem tractant
global $related_module;
$related_module = array(
	'Accounts' => array(
		'MemberOf' => 'account_id',
	),
	'Contacts' => array(
		'Accounts' => 'account_id',
	),
	'SalesOrder' => array(
		'Accounts' => 'account_id',
		'Contacts' => 'contact_id',
		'Quotes' => 'quote_id',
		'Potentials' => 'potential_id',
		'Users' => 'assigned_user_id',
	),
	'Quotes' => array(
		'Accounts' => 'account_id',
		'Contacts' => 'contact_id',
		'Potentials' => 'potential_id',
		'Users' => 'assigned_user_id',
	),
	'Invoice' => array(
		'Accounts' => 'account_id',
		'Contacts' => 'contact_id',
		'SalesOrder' => 'salesorder_id',
		'Users' => 'assigned_user_id',
	),
	'Issuecards' => array(
		'Accounts' => 'accid',
		'Contacts' => 'ctoid',
		'Users' => 'assigned_user_id',
	),
	'Potentials' => array(
		'RelatedTo' => 'related_to'
	),
	'Campaigns' => array(
		'Product' => 'product_id'
	),
	'Faq' => array(
		'Product' => 'product_id'
	),
	'ServiceContracts' => array(
		'RelatedTo' => 'sc_related_to'
	),
	'ConfidentialInfo' => array(
		'RelatedTo' => 'cirelto'
	),
	'Timecontrol' => array(
		'Product' => 'product_id',
		'TCRelatedTo' => 'relatedto'
	),
	'ModComments' => array(
		'MCRelatedTo' => 'related_to'
	),
	'CobroPago' => array(
		'CPParent' => 'parent_id',
		'CPRelatedTo' => 'related_id'
	),
	'Project' => array(
		'Accounts' => 'linktoaccountscontacts',
		'Contacts' => 'linktoaccountscontacts',
		'RelatedTo' => 'linktoaccountscontacts'
	),
	'InventoryDetails' => array(
		'Accounts' => 'account_id',
		'Contacts' => 'contact_id',
		'Vendors' => 'vendor_id',
		'Products' => 'productid',
		'Services' => 'productid',
		'Invoice' => 'related_to',
		'Quotes' => 'related_to',
		'SalesOrder' => 'related_to',
		'PurchaseOrder' => 'related_to',
	),
	'HelpDesk' => array(
		'HDRelatedTo' => 'parent_id',
		'HDProducts' => 'product_id',
	),
	'Organization' => array(
		'Accounts' => 'accid'
	),
);


//Array de mapeig de moduls especials, p.e. el presciptors son comptes
//aleshores el tag Prescriptor el mapegem a Accounts
//També per a llistes de relacionats, quan tenim més d'un tipus d'entitat per modul, p.e.
//Productes i Soports són 2 tipus del Modul Products. La clau serà el nom del relatedlist.
//Para UItype 10 con varios módulos posibles, el valor será un array con el nombre de los módulos
//implicados.
global $special_modules;
$special_modules = array(
	'MemberOf' => 'Accounts',
	'RelatedTo' => array('Contacts',
		'Accounts'
	),
	'Product' => array('Services',
		'Products'
	),
	'MCRelatedTo' => array('Project',
		'Contacts',
		'Potentials',
		'ProjectTask',
		'Leads',
		'Accounts'
	),
	'TCRelatedTo' => array('SalesOrder',
		'HelpDesk',
		'ProjectMilestone',
		'Assets',
		'Accounts',
		'Vendors',
		'Potentials',
		'PurchaseOrder',
		'Quotes',
		'Project',
		'ProjectTask',
		'ServiceContracts',
		'Leads',
		'Contacts',
		'Campaigns',
		'Invoice'
	),
	'CPParent' => array(
		'Accounts',
		'Contacts',
		'Leads',
		'Vendors',
	),
	'CPRelatedTo' => array('Products',
		'ServiceContracts',
		'Invoice',
		'SalesOrder',
		'Campaigns',
		'HelpDesk',
		'ProjectMilestone',
		'Assets',
		'Services',
		'PurchaseOrder',
		'Quotes',
		'Potentials',
		'Project',
		'ProjectTask'
	),
	'HDRelatedTo' => array(
		'Accounts',
		'Contacts'
	),
	'HDProducts' => array(
		'Products',
		'Services'
	),
);

$image_modules = array(
	'Contacts' => 'imagename',
	'Products' => 'imagename',
	'ProductList' => 'imagename',
);

$special_inv = array_inverse($special_modules);

global $iter_modules;
$iter_modules = array();

global $from_compile;
$from_compile = true;

function compile($text, $id, $module, $changeamp = false, $applyformat = true) {
	global $expressionGD;
	if (empty($text)) {
		return $text;
	}
	$trobat = explode('{', $text);
	$marcadors = array();
	foreach ($trobat as $tros) {
		if (substr_count($tros, '}') > 0) {
			$trobat2 = explode('}', $tros);
			$marcadors[] = $trobat2[0];
		}
	}
	OpenDocument::debugmsg('COMPILE TEXT');
	OpenDocument::debugmsg(array(
		'TEXT' => $text,
		'ID' => $id,
		'MODULE' => $module
	));
	$compiled_text = $text;
	if (!empty($marcadors)) {
		OpenDocument::debugmsg('Markers found');
		OpenDocument::debugmsg($marcadors);
		foreach ($marcadors as $marcador) {
			if (substr($marcador, 0, strlen($expressionGD)) == $expressionGD) {
				$replacewith = eval_expression($marcador, $id);
			} elseif ($changeamp) {
				$compiled_marc = retrieve_from_db($marcador, $id, $module, $applyformat);
				$encoding = mb_detect_encoding($compiled_marc);
				if ($encoding != 'UTF-8') {
					$compiledtext = mb_convert_encoding($compiled_marc, 'UTF-8', 'HTML-ENTITIES');
				} else {
					$compiledtext = $compiled_marc;
				}
				$compiledtext = str_replace('<br>', '<text:line-break/>', $compiledtext);
				$replacewith = str_replace('&', '&amp;', $compiledtext);
			} else {
				$replacewith = retrieve_from_db($marcador, $id, $module, $applyformat);
			}
			OpenDocument::debugmsg('REPLACE WITH: '.$replacewith);
			$compiled_text = str_replace('{'.$marcador.'}', $replacewith, $compiled_text);
		}
	}
	return $compiled_text;
}

function eval_expression($marcador, $entityid) {
	global $expressionGD,$adb,$current_user;
	include_once 'modules/com_vtiger_workflow/expression_engine/VTParser.inc';
	include_once 'modules/com_vtiger_workflow/expression_engine/VTTokenizer.inc';
	include_once 'modules/com_vtiger_workflow/expression_engine/VTExpressionEvaluater.inc';
	$lenexp = strlen($expressionGD);
	$compile_expression = substr($marcador, $lenexp);
	$type = getSalesEntityType($entityid);
	if (strpos($compile_expression, '~')!==false && strpos($compile_expression, '¬')!==false) {
		$compile_expression = str_replace('~', '{', $compile_expression);
		$compile_expression = str_replace('¬', '}', $compile_expression);
		OpenDocument::debugmsg('COMPILE EXPRESSION: GenDocWF substitution: '.$compile_expression.' WITH: '.$type.' ('.$entityid.')');
		$compile_expression = compile($compile_expression, $entityid, $type, false, false);
	}
	OpenDocument::debugmsg('COMPILE EXPRESSION: '.$compile_expression.' WITH: '.$type.' ('.$entityid.')');
	$entityws = $adb->getone("SELECT id FROM vtiger_ws_entity WHERE name='$type'");
	$entityId = $entityws.'x'.$entityid;
	$entity = new VTWorkflowEntity($current_user, $entityId);
	$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($compile_expression)));
	$expression = $parser->expression();
	$exprEvaluater = new VTFieldExpressionEvaluater($expression);
	$exprEvaluation = $exprEvaluater->evaluate($entity);
	OpenDocument::debugmsg('COMPILE EXPRESSION: '.$exprEvaluation);
	return $exprEvaluation;
}

function retrieve_from_db($marcador, $id, $module, $applyformat = true) {
	global $current_user,$repe,$adb,$related_module,$special_modules,$special_inv,$iter_modules,$default_charset,$genxmlaggregates;
	global $dateGD, $repeticionGD, $lineGD;
	$module = trim(preg_replace('/\*(\w|\s)+\*/', '', $module));
	OpenDocument::debugmsg("retrieve_from_db: $marcador with $module($id)");
	$token_pair = explode('.', $marcador);
	if (count($token_pair) == 1) {
		if (module_exists($token_pair[0]) || (!empty($special_modules[$token_pair[0]])) && module_exists($special_modules[$token_pair[0]])) {
			if (module_exists($module)) {
				require_once "modules/$module/$module.php";
				$focus = new $module();
				//Comprovem que l'entitat existisca i no estiga borrada
				if (!entity_exists($focus, $id, $module)) {
					return false;
				}
				$focus->retrieve_entity_info($id, $module);
				$mod_rel = (module_exists($token_pair[0]) ? $token_pair[0] : $special_modules[$token_pair[0]]);
				require_once "modules/$mod_rel/$mod_rel.php";
				$focus_rel = new $mod_rel();
				if (areModulesRelated($token_pair[0], $module)) {
					return entity_exists($focus_rel, $focus->column_fields[$related_module[$module][$token_pair[0]]], $mod_rel);
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			$date_format = 'd-m-Y';
			if (substr($token_pair[0], 0, strlen($dateGD)+1)==$dateGD.':') {
				list($token_pair[0],$date_format) = explode(':', $token_pair[0]);
			}
			switch ($token_pair[0]) {
				case $dateGD: // fecha
					switch ($date_format) {
						case 'l':
							require_once 'modules/cbtranslation/cbtranslation.php';
							$reemplazo = cbtranslation::getDayOfWeekName(date('N') % 7, OpenDocument::$compile_language);
							break;
						case 'F':
							require_once 'modules/cbtranslation/cbtranslation.php';
							$reemplazo = cbtranslation::getMonthName(date('n')-1, OpenDocument::$compile_language);
							break;
						default:
							$reemplazo = date($date_format);
					}
					break;
				case $repeticionGD:
					$reemplazo = $repe[count($repe)-1];
					break;
				case $lineGD:
					$reemplazo = '<hr />';
					break;
				default:
					$reemplazo = '{'.$marcador.'}';
			}
			return $reemplazo;
		}
	}
	if (count($token_pair) == 3) {
		if ($token_pair[2]=='enletras') {
			require_once 'modules/cbtranslation/number2string.php';
			$nuevomarcador = $token_pair[0].'.'.$token_pair[1];
			$reemplazo = retrieve_from_db($nuevomarcador, $id, $module, false);
			$reemplazo = strtolower(number2string::convert($reemplazo, OpenDocument::$compile_language));
			return $reemplazo;
		}
		if ($token_pair[0]=='genxmlsum') {
			return $genxmlaggregates['sum'][$token_pair[1]][$token_pair[2]];
		}
	}
	if (count($token_pair) == 2 && $token_pair[0]=='genxmlcount') {
		return $genxmlaggregates['cnt'][$token_pair[1]]['genxmlcnt'];
	}
	//Anem a vore si tenim un modul com el que ens passen
	if (module_exists($module)) {
		require_once "modules/$module/$module.php";
		$focus = new $module();
		//Comprovem que l'entitat existisca i no estiga borrada
		if (!entity_exists($focus, $id, $module)) {
			return false;
		}

		list($cachedresult,$found) = VTCacheUtils::lookupCachedInformation('retrieve_from_db::'.$id.$module);
		if ($found) {
			$focus->column_fields = $cachedresult;
		} else {
			$focus->retrieve_entity_info($id, $module);
			VTCacheUtils::updateCachedInformation('retrieve_from_db::'.$id.$module, $focus->column_fields);
		}

		if ($module == $token_pair[0]) {
			if (array_key_exists($token_pair[1], $focus->column_fields)) {
				$cadena = $focus->column_fields[$token_pair[1]];
				if ($applyformat) {
					if (is_date($token_pair[1], $module) && !empty($cadena)) {
						$date = new DateTimeField($cadena);
						$cadena = $date->getDisplayDate($current_user);
						if (strpos($cadena, '0000')!==false || $cadena=='--') {
							$cadena='';
						}
					}
					switch (getTypeOfDataByFieldName($module, $token_pair[1])) {
						case 'I':
							$cadena = number_format($cadena, 0);
							break;
						case 'N':
						case 'NN':
							$cadena = CurrencyField::convertToUserFormat($cadena, null, true);
							break;
						//case 'C':
						//    $cadena = ($cadena == '1' ? 'Yes' : 'No');
						//    break;
					}

					$backtrace = debug_backtrace();
					$come_from_compile = false;
					foreach ($backtrace as $trace) {
						if ($trace['function'] == 'compile') {
							$come_from_compile = true;
						}
					}

					if ($come_from_compile && is_numeric($cadena)) {
						$uitype = getUitypefield($module, $token_pair[1]);
						switch ($uitype) {
							case 56:
								if ($cadena == 1) {
									$cadena = 'Yes';
								} else {
									$cadena = 'No';
								}
								break;
							case 101:
							case 77:
								$cadena = getUserFullName($cadena);
								break;
							default:
								break;
						}
					}
				}
				if (is_picklist($token_pair[1], $module)) {
					$reemplazo = getTranslatedString(elimina_llave(html_entity_decode($cadena, ENT_QUOTES, $default_charset)), $module);
				} else {
					$reemplazo = elimina_llave(html_entity_decode($cadena, ENT_QUOTES, $default_charset));
				}
			} elseif (in_array($token_pair[0], getInventoryModules()) && ($token_pair[1] == 'TaxPercent' || $token_pair[1] == 'TaxTotal' )) {
				$totalFra = $focus->column_fields['hdnGrandTotal'];
				$sbtotalFra = $focus->column_fields['hdnSubTotal'];
				if ($applyformat) {
					switch ($token_pair[1]) {
						case 'TaxTotal':
							$reemplazo = CurrencyField::convertToUserFormat(($totalFra-$sbtotalFra), null, true);
							break;
						case 'TaxPercent':
							$reemplazo = CurrencyField::convertToUserFormat((($totalFra-$sbtotalFra)*100/$sbtotalFra), null, true);
							break;
					}
				}
			} else {
				$reemplazo = '{'.$marcador.'}';
			}
		} elseif ($token_pair[0] == 'CurrentUser') {
			$reemplazo = isset($current_user->column_fields[$token_pair[1]]) ? getTranslatedString($current_user->column_fields[$token_pair[1]], 'Users') : '';
		} elseif ($token_pair[0] == 'CreatedBy' || $token_pair[0] == 'ModifiedBy') {
			$uid = ($token_pair[0] == 'CreatedBy' ? $focus->column_fields['created_user_id'] : $focus->column_fields['modifiedby']);
			require_once 'modules/Users/Users.php';
			$usr = new Users();
			if (!entity_exists($usr, $uid, 'Users')) {
				return false;
			}
			$usr->retrieve_entity_info($uid, 'Users');
			$reemplazo = isset($usr->column_fields[$token_pair[1]]) ? getTranslatedString($usr->column_fields[$token_pair[1]], 'Users') : '';
		} elseif (array_key_exists($token_pair[0], $iter_modules)) {
			if ($token_pair[0] == 'ProductList' || $token_pair[0] == 'ServiceList' || $token_pair[0] == 'QuestionList' || $token_pair[0] == 'QuestionListCat') {
				if (array_key_exists($token_pair[1], $iter_modules[$token_pair[0]][0])) {
					$reemplazo=getTranslatedString(elimina_llave(html_entity_decode($iter_modules[$token_pair[0]][0][$token_pair[1]], ENT_QUOTES, $default_charset)), $module);
				} else {
					$reemplazo=retrieve_from_db($marcador, $iter_modules[$token_pair[0]][0]['productid'], $token_pair[0], $applyformat);
				}
			} else {
				$reemplazo = retrieve_from_db($marcador, $iter_modules[$token_pair[0]][0], $token_pair[0], $applyformat);
			}
		} elseif (areModulesRelated($token_pair[0], $module)) {
			$reemplazo = retrieve_from_db($marcador, $focus->column_fields[$related_module[$module][$token_pair[0]]], $token_pair[0], $applyformat);
		} elseif ($token_pair[0] == 'Organization' || $token_pair[0] == 'cbCompany') {
			$res = $adb->query('SELECT * FROM vtiger_cbcompany WHERE defaultcompany=1');
			$org_fields = $adb->getFieldsArray($res);
			if (in_array($token_pair[1], $org_fields)) {
				$reemplazo = $adb->query_result($res, 0, $token_pair[1]);
			} else {
				$id = $adb->query_result($res, 0, 'cbcompanyid');
				$accid = $adb->query_result($res, 0, 'accid');
				if (empty($accid) || $token_pair[1] == 'rm') {
					switch ($token_pair[1]) {
						case 'accountname':
						case 'organizationname':
							$token_pair[1] = 'companyname';
							break;
						case 'rm':
							$token_pair[1] = 'companybr';
							break;
						case 'bill_street':
						case 'ship_street':
							$token_pair[1] = 'address';
							break;
						case 'bill_city':
						case 'ship_city':
							$token_pair[1] = 'city';
							break;
						case 'bill_code':
						case 'ship_code':
							$token_pair[1] = 'postalcode';
							break;
						case 'bill_state':
						case 'ship_state':
						case 'bill_provincia':
						case 'ship_provincia':
							$token_pair[1] = 'state';
							break;
						case 'email1':
							$token_pair[1] = 'email';
							break;
					}
					$marcador = 'cbCompany.'.$token_pair[1];
					$reemplazo = retrieve_from_db($marcador, $id, 'cbCompany', $applyformat);
				} else {
					$marcador = 'Accounts.'.$token_pair[1];
					$reemplazo = retrieve_from_db($marcador, $accid, 'Accounts', $applyformat);
				}
			}
		} else {
			$reemplazo = '{'.$marcador.'}';
		}
	} elseif (array_key_exists($token_pair[0], $iter_modules)) {
		if ($token_pair[0] == 'ProductList' || $token_pair[0] == 'ServiceList' || $token_pair[0] == 'QuestionList') {
			if (array_key_exists($token_pair[1], $iter_modules[$token_pair[0]][0])) {
				$reemplazo = getTranslatedString(elimina_llave(html_entity_decode($iter_modules[$token_pair[0]][0][$token_pair[1]], ENT_QUOTES, $default_charset)), $module);
			} else {
				$reemplazo = retrieve_from_db($marcador, $iter_modules[$token_pair[0]][0]['productid'], $token_pair[0], $applyformat);
			}
		} elseif (array_key_exists($module, $special_modules)) {
			if (is_array($special_modules[$module])) {
				$reemplazo = '.';
				foreach ($special_modules[$module] as $multvalue) {
					$entitymod = getEntityModule($id);
					if ($entitymod == $multvalue) {
						$reemplazo = retrieve_from_db($multvalue.'.'.$token_pair[1], $id, $multvalue, $applyformat);
						if ($reemplazo == '{'.$token_pair[0].'.'.$token_pair[1].'}') {
							$reemplazo = '';
						}
						break;
					}
				}
			} else {
				$map = $special_modules[$module];
				$reemplazo = retrieve_from_db($map.'.'.$token_pair[1], $id, $map, $applyformat);
			}
		} else {
			$reemplazo = retrieve_from_db($marcador, $iter_modules[$token_pair[0]][0], $token_pair[0], $applyformat);
		}
	} elseif (array_key_exists($module, $special_modules)) {
		if (is_array($special_modules[$module])) {
			$reemplazo = '.';
			foreach ($special_modules[$module] as $multvalue) {
				$entitymod = getEntityModule($id);
				if ($entitymod == $multvalue) {
					$reemplazo = retrieve_from_db($multvalue.'.'.$token_pair[1], $id, $multvalue, $applyformat);
					if ($reemplazo == '{'.$token_pair[0].'.'.$token_pair[1].'}') {
						$reemplazo = '';
					}
					break;
				}
			}
		} else {
			$map = $special_modules[$module];
			$reemplazo = retrieve_from_db($map.'.'.$token_pair[1], $id, $map, $applyformat);
		}
	} elseif ($token_pair[0] == 'QuestionList' && $module == 'QuestionListCat') {
		$reemplazo = retrieve_from_db($marcador, $id['Revision'], 'Revision', $applyformat);
	} else {
		$reemplazo = '{'.$marcador.'}';
	}

	$reemplazo = str_replace("\r\n", '<br>', $reemplazo);
	$reemplazo = str_replace("\n", '<br>', $reemplazo);
	return $reemplazo;
}

function eval_existe($condition, $id, $module) {
	global $special_modules, $enGD;
	OpenDocument::debugmsg('<h3>IFEXIST -- Condition: '.$condition.' ID: '.$id.' MODULE: '.$module.'</h3>');
	$condition_pair = explode('=', $condition);
	if (count($condition_pair) == 2) {
		$comp = '=';
		for ($i=0; $i<count($condition_pair); $i++) {
			$condition_pair[$i] = trim($condition_pair[$i]);
		}
	} else {
		$condition_pair = explode(' '.$enGD.' ', $condition);
		if (count($condition_pair) == 2) {
			$comp = $enGD;
			$valstr = trim($condition_pair[1]);
			$valstr = substr($valstr, 0, -1);
			$valstr = substr($valstr, 1);
			$values = explode(',', $valstr);
		}
	}
	$token_pair = explode('.', $condition_pair[0]);
	if (is_related_list($module, $token_pair[0])) {
		return eval_paracada($condition, $id, $module, true);
	} else {
		$value = retrieve_from_db($condition_pair[0], $id, $module);
		if (count($condition_pair) == 1) {
			if (count($token_pair) == 2) {
				if (is_array($special_modules[$token_pair[0]])) {
					return !empty($value);
				} else {
					return ($value != '{'.$condition_pair[0].'}');
				}
			} else {
				return $value;
			}
		} else {
			$cond = multiple_values($value);
			$val = multiple_values($condition_pair[1]);
			switch ($comp) {
				case '=':
					$cond_ok = ($cond == $val);
					break;
				case $enGD:
					$cond_ok = (count(array_intersect($cond, $values)) > 0);
					break;
			}
			OpenDocument::debugmsg('<b>IFEXIST -- RESULT: '.($cond_ok ? 'TRUE' : 'FALSE').'</b><br>');
			return $cond_ok;
		}
	}
}

function eval_noexiste($condition, $id, $module) {
	global $special_modules, $enGD;
	OpenDocument::debugmsg('<h3>IFNOTEXIST -- Condition: '.$condition.' ID: '.$id.' MODULE: '.$module.'</h3>');
	$condition_pair = explode('=', $condition);
	if (count($condition_pair) == 2) {
		$comp = '=';
		for ($i=0; $i<count($condition_pair); $i++) {
			$condition_pair[$i] = trim($condition_pair[$i]);
		}
	} else {
		$condition_pair = explode(' '.$enGD.' ', $condition);
		if (count($condition_pair) == 2) {
			$comp = $enGD;
			$valstr = trim($condition_pair[1]);
			$valstr = substr($valstr, 0, -1);
			$valstr = substr($valstr, 1);
			$values = explode(',', $valstr);
		}
	}

	$token_pair = explode('.', $condition_pair[0]);
	if (is_related_list($module, $token_pair[0])) {
		return !eval_paracada($condition, $id, $module, true);
	} else {
		$value = retrieve_from_db($condition_pair[0], $id, $module);

		if (count($condition_pair) == 1) {
			if (count($token_pair) == 2) {
				if (is_array($special_modules[$token_pair[0]])) {
					return empty($value);
				} else {
					return ($value == '{'.$condition_pair[0].'}');
				}
			} else {
				return !$value;
			}
		} else {
			$cond = multiple_values($value);
			$val = multiple_values($condition_pair[1]);
			switch ($comp) {
				case '=':
					return $cond != $val;
				break;
				case $enGD:
					return (count(array_intersect($cond, $values)) == 0);
				break;
			}
		}
	}
}

function getModuleFromCondition($condition) {
	$condition_pair = explode('=', $condition);
	$condition_pair[0] = trim($condition_pair[0]);
	$token_pair = explode('.', $condition_pair[0]);
	return $token_pair[0];
}

function make_json_condition($modname, $text_condition) {
	preg_match_all('/(\w+)\s?(>|<|=|!=|<=|>=)\s?(\'?\w+\'?)\s?(&&|\|\|)?\s?/', $text_condition, $pieces, PREG_SET_ORDER);
	$conds_array = array();
	$opmap = array(
		'string' => array(
			'=' => 'is',
			'!=' => 'does not start with',
		),
		'number' => array(
			'=' => 'equal to',
			'!=' => 'does not equal',
			'>' => 'greater than',
			'<' => 'less than',
			'<=' => 'less than or equal to',
			'>=' => 'greater than or equal to',
		),
		'bool' => array(
			'=' => 'is',
			'!=' => 'is not',
		),
		'datetime' => array(
			'=' => 'is',
			'!=' => 'is not',
			'>' => 'greater than',
			'<' => 'less than',
			'<=' => 'less than or equal to',
			'>=' => 'greater than or equal to',
		),
	);
	$typeofdatamap = array(
		'string' => array('V', 'E'),
		'number' => array('I', 'N', 'NN'),
		'bool' => array('C'),
		'datetime' => array('D', 'DT', 'T'),
	);
	if (count($pieces) > 0) {
		foreach ($pieces as $piece) {
			$field_tod = getTypeOfDataByFieldName($modname, trim($piece[1]));
			$type = 'string';
			foreach ($typeofdatamap as $typeofdata => $typesofdata) {
				if (in_array($field_tod, $typesofdata)) {
					$type = $typeofdata;
				}
			}
			if ($field_tod === 'C') {
				$piece[3] = $piece[3] == '1' ? 'true:boolean' : 'false:boolean';
			}
			if (isset($piece[4])) {
				$glue = $piece[4] == '||' ? 'or' : 'and';
			} else {
				$glue = 'and';
			}
			$cond = array(
				'fieldname' => trim($piece[1]),
				'operation' => $opmap[$type][trim($piece[2])],
				'value' => trim(str_replace('\'', '', $piece[3])),
				'valuetype' => 'rawtext',
				'joincondition' => $glue,
				'groupid' => '0',
			);
			$conds_array[] = $cond;
		}
	}
	return json_encode($conds_array);
}

function eval_paracada($condition, $id, $module, $check = false) {
	global $adb, $iter_modules, $special_modules, $currentModule, $related_module, $rootmod, $enGD, $current_user;
	OpenDocument::debugmsg('<h3>FOREACH -- Condition: '.$condition.' ID: '.$id.' MODULE: '.$module.'</h3>');
	if (!module_exists($module)) {
		return false;
	}
	if ($module == 'Organization') {
		$module = 'cbCompany';
	}
	preg_match('/(.+)\s*(>|<|=|!=|<=|>=| '.$enGD.' | !'.$enGD.' )\s*(.+)/', $condition, $splitcondition);
	if (!empty($splitcondition)) {
		$condition_pair = array(
			$splitcondition[1],
			$splitcondition[3],
		);
		$comp = trim($splitcondition[2]);
		if (substr($comp, 0, 1) == '!') {
			$comp = substr($comp, 1);
			$negado = true;
		} else {
			$negado = false;
		}
	} else {
		$condition_pair = (array)$condition;
		$comp = '';
		$negado = false;
	}
	if ($comp == $enGD) {
		$valstr = trim($condition_pair[1]);
		$valstr = substr($valstr, 0, -1);
		$valstr = substr($valstr, 1);
		$values = explode(',', $valstr);
	}

	for ($i=0; $i<count($condition_pair); $i++) {
		$condition_pair[$i] = trim($condition_pair[$i]);
	}

	$token_pair = explode('.', $condition_pair[0]);

	preg_match('/(\w+)\s\[(.+)+\]/', $condition, $cond_elements); // Multiple conditions?
	if (!empty($cond_elements) && isset($cond_elements[2])) {
		$json_condition = make_json_condition($cond_elements[1], $cond_elements[2]);
		OpenDocument::debugmsg($json_condition);
		$comp = 'wfeval';
		$token_first_space_split = explode(' ', $token_pair[0]);
		$token_pair[0] = $token_first_space_split[0];
	}

	preg_match('/\*((\w+)\s(ASC|DESC|asc|desc))\*/', $condition, $sortinfo); // Has sort condition?
	if (!empty($sortinfo)) {
		$token_pair[0] = str_replace($sortinfo[0], '', $token_pair[0]);
	}

	$token_pair[0] = trim($token_pair[0]);
	if (array_key_exists($token_pair[0], $special_modules)) {
		$relmodule = $special_modules[$token_pair[0]];
		$SQL_label = " AND label='{$token_pair[0]}'";
	} else {
		$relmodule = $token_pair[0];
		$SQL_label = '';
	}
	if ($relmodule != $rootmod) {
		if (module_exists($relmodule) && module_exists($module)) {
			$tab_mod = getTabid($module);
			$tab_rel = getTabid($relmodule);

			$SQL = 'SELECT name FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=?'.$SQL_label;
			$res = $adb->pquery($SQL, array($tab_mod,$tab_rel));
			$func_rel = $adb->query_result($res, 0, 'name');

			require_once "modules/$module/$module.php";
			$focus = new $module();
			//Comprovem que l'entitat existisca i no estiga borrada
			if (!entity_exists($focus, $id, $module)) {
				return false;
			}

			$focus->retrieve_entity_info($id, $module);
			$currentModule_anterior = $currentModule;
			$currentModule = $module;
			$related = array();
			$related['entries'] = array();
			if (!empty($func_rel)) {
				global $GetRelatedList_ReturnOnlyQuery;
				$GetRelatedList_ReturnOnlyQuery = true;
				$relatedsql = $focus->$func_rel($id, $tab_mod, $tab_rel);
				$GetRelatedList_ReturnOnlyQuery = false;
				if (!empty($sortinfo)) {
					list($sortstring, $bare_sortstring, $fieldname, $sortorder) = $sortinfo;
					$columnname = getColumnnameByFieldname($tab_rel, $fieldname);
					$sortinfo = array('cname' => $columnname, 'order' => $sortorder);
				} else {
					$sortinfo = false;
				}
				$related = getRelatedCRMIDs($relatedsql['query'], $sortinfo);
			} else {
				if (areModulesRelated($token_pair[0], $module)) {
					$clave = $focus->column_fields[$related_module[$module][$token_pair[0]]];
				} else {
					$clave = '';
				}
				if (!empty($clave)) {
					$related['entries'] = array($clave => $clave);
				}
			}
			$currentModule = $currentModule_anterior;
			if (!$check) {
				$iter_modules[$token_pair[0]] = array();
			}
			if (count($related['entries']) > 0) {
				foreach ($related['entries'] as $key => $value) {
					//Ara tenim totes les entitats relacionades, si ens pasen un parametre
					//per a filtrar, el filtrem ara.
					if (count($condition_pair) == 2) {
						$conditions = multiple_values(retrieve_from_db($condition_pair[0], $key, $token_pair[0]));
						$cond = $conditions[0];
						$cond = str_replace(',', '.', $cond);
						if (!empty($token_pair[1])) {
							$uitype = getUItypeByFieldName($module, $token_pair[1]);
							if (in_array($uitype, array(7, 71, 72))) {
								$numField = new CurrencyField($cond);
								$cond = $numField->getDBInsertedValue($current_user, false);
							}
						}
						$vals = multiple_values($condition_pair[1]);
						$val = $vals[0];
						switch ($comp) {
							case '>':
								$cond_ok = ($cond > $val);
								break;
							case '<':
								$cond_ok = ($cond < $val);
								break;
							case '=':
								$cond_ok = ($cond == $val);
								break;
							case '<=':
								$cond_ok = ($cond <= $val);
								break;
							case '>=':
								$cond_ok = ($cond >= $val);
								break;
							case $enGD:
								$cond_ok = (count(array_intersect($conditions, $values)) > 0);
								break;
							case 'wfeval':
								include_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
								include_once 'modules/com_vtiger_workflow/VTJsonCondition.inc';
								include_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
								$rs = $adb->pquery('SELECT id FROM vtiger_ws_entity WHERE name=?', array($relmodule));
								$wsid = $adb->query_result($rs, 0, 'id');
								$wsid = $wsid . 'x' . $value;
								$util = new VTWorkflowUtils();
								$adminUser = $util->adminUser();
								$entityCache = new VTEntityCache($adminUser);
								$entityCache->forId($wsid);
								$cs = new VTJsonCondition();
								$util->revertUser();
								$cond_ok = $cs->evaluate($json_condition, $entityCache, $wsid);
								break;
						}
						if ($negado && ($comp != 'wfeval')) {
							$cond_ok = !$cond_ok;
						}
						OpenDocument::debugmsg(array('ID' => $key, 'NEG' => $negado, 'COND' => $cond . $comp . $val, 'EVAL'=> $cond_ok));
					} else {
						$cond_ok = true;
					}
					if ($cond_ok) {
						if (!$check) {
							$iter_modules[$token_pair[0]][] = $key;
						} else {
							return true;
						}
					}
				}
			}
		} else {
			$tmp_iter = array();
			switch ($token_pair[0]) {
				case 'Organization':
					$res = $adb->query('SELECT cbcompanyid FROM vtiger_cbcompany WHERE defaultcompany=1');
					$id = $adb->query_result($res, 0, 'cbcompanyid');
					$token_pair[0] = 'cbCompany';
					$tmp_iter = array($id);
					break;
				case 'ProductList':
					require_once "modules/$module/$module.php";
					$focus = new $module();
					//Comprovem que l'entitat existisca i no estiga borrada
					if (!entity_exists($focus, $id, $module)) {
						return false;
					}
					$focus->retrieve_entity_info($id, $module);
					$tmp_iter = getProductList($module, $id);
					break;
				case 'ServiceList':
					require_once "modules/$module/$module.php";
					$focus = new $module();
					//Comprovem que l'entitat existisca i no estiga borrada
					if (!entity_exists($focus, $id, $module)) {
						return false;
					}
					$focus->retrieve_entity_info($id, $module);
					$tmp_iter = getServiceList($module, $id);
					break;
				case 'QuestionList':
					//Si esta debajo de un paracada QuestionListCat, resolvemos
					//las dependencias.
					if ($module == 'QuestionListCat') {
						$module = 'Revision';
						$idqlc = $id;
						$id = $idqlc['Revision'];
						$condition_pair = array(
						'QuestionList.subcategoriapregunta',
						$idqlc['subcategoriapregunta']
						);
						$comp = '=';
					}
					require_once "modules/$module/$module.php";
					$focus = new $module();
					//Comprovem que l'entitat existisca i no estiga borrada
					if (!entity_exists($focus, $id, $module)) {
						return false;
					}
					$focus->retrieve_entity_info($id, $module);
					$tmp_iter = getQuestionList($module, $id);
					break;
				case 'QuestionListCat':
					require_once "modules/$module/$module.php";
					$focus = new $module();
					//Comprovem que l'entitat existisca i no estiga borrada
					if (!entity_exists($focus, $id, $module)) {
						return false;
					}
					$focus->retrieve_entity_info($id, $module);
					$tmp_iter = getQuestionListCat($module, $id);
					break;
			}
			if (count($condition_pair) == 2) {
				foreach ($tmp_iter as $iter_elem) {
					list($cond_module,$cond_field) = explode('.', $condition_pair[0]);
					$cond = array(trim($iter_elem[$cond_field]));
					$val = multiple_values($condition_pair[1]);
					if ($negado) {
						switch ($comp) {
							case '=':
								$cond_ok = ($cond != $val);
								break;
							case $enGD:
								$cond_ok = (count(array_intersect($cond, $values)) == 0);
								break;
						}
					} else {
						switch ($comp) {
							case '=':
								$cond_ok = ($cond == $val);
								break;
							case $enGD:
								$cond_ok = (count(array_intersect($cond, $values)) > 0);
								break;
						}
					}
					if ($cond_ok) {
						if (!$check) {
							$iter_modules[$token_pair[0]][] = $iter_elem;
						} else {
							if ($token_pair[0] == 'QuestionListCat') {
								$iter_modules[$token_pair[0]] = array($iter_elem);
							}
							return true;
						}
					}
				}
			} else {
				$iter_modules[$token_pair[0]] = $tmp_iter;
			}
		}
	}
}

function eval_imagen($entity, $id, $module) {
	global $adb, $image_modules, $iter_modules, $related_module;
	OpenDocument::debugmsg("eval_image: $entity, $id, $module");
	list($mod,$field) = explode('.', $entity);
	$att_name = '';
	if ($mod == $module) {
		$entid = $id;
	} elseif (array_key_exists($mod, $iter_modules)) {
		$entid = $iter_modules[$mod][0];
		if ($mod == 'ProductList') {
			$entid = $iter_modules[$mod][0]['productid'];
		}
		if ($entity=='Products.imagename') { // special multiimage field
			$sql = 'select vtiger_attachments.name
				from vtiger_products
				left join vtiger_seattachmentsrel on vtiger_seattachmentsrel.crmid=vtiger_products.productid
				inner join vtiger_attachments on vtiger_attachments.attachmentsid=vtiger_seattachmentsrel.attachmentsid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
				where vtiger_crmentity.setype="Products Image" and productid=?';
			$result = $adb->pquery($sql, array($entid));
			if ($result && $adb->num_rows($result)>0) {
				$att_name = $adb->query_result($result, 0, 0);
			} else {
				return 'modules/evvtgendoc/no_image_entity.jpg';
			}
		}
	} elseif (($mod=='Products' || $mod=='Services') && array_key_exists('InventoryDetails', $iter_modules)) {
		$focus = CRMEntity::getInstance('InventoryDetails');
		$focus->retrieve_entity_info($iter_modules['InventoryDetails'][0], 'InventoryDetails');
		$entid = $focus->column_fields[$related_module['InventoryDetails'][$mod]];
		if ($entity=='Products.imagename') { // special multiimage field
			$sql = 'select vtiger_attachments.name
				from vtiger_products
				left join vtiger_seattachmentsrel on vtiger_seattachmentsrel.crmid=vtiger_products.productid
				inner join vtiger_attachments on vtiger_attachments.attachmentsid=vtiger_seattachmentsrel.attachmentsid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
				where vtiger_crmentity.setype="Products Image" and productid=?';
			$result = $adb->pquery($sql, array($entid));
			if ($result && $adb->num_rows($result)>0) {
				$att_name = $adb->query_result($result, 0, 0);
			} else {
				$att_name = 'modules/evvtgendoc/no_image_entity.jpg';
			}
		} else {
			$att_name = retrieve_from_db($entity, $iter_modules['InventoryDetails'][0], 'InventoryDetails');
		}
		if (empty($att_name)) {
			return 'modules/evvtgendoc/no_image_entity.jpg';
		}
	} else {
		$focus = CRMEntity::getInstance($module);
		$focus->retrieve_entity_info($id, $module);
		$entid = $focus->column_fields[$related_module[$module][$mod]];
		if (!empty($entid)) {
			$att_name = retrieve_from_db($entity, $id, $module);
		} else {
			return 'modules/evvtgendoc/no_image_entity.jpg';
		}
	}
	if ($att_name=='') {
		$mname = getSalesEntityType($entid);
		$focus = CRMEntity::getInstance($mname);
		$focus->retrieve_entity_info($entid, $mname);
		if (is_null($field)) {
			if (is_null($focus->column_fields[$image_modules[$mname]])) {
				return 'modules/evvtgendoc/no_image_entity.jpg';
			} else {
				$att_name = $focus->column_fields[$image_modules[$mname]];
			}
		} else {
			if ($mod=='Project' && $field=='GanttChart') {
				$prjobj = CRMEntity::getInstance('Project');
				$gcinfo = $prjobj->get_gantt_chart($entid, 0, 0, false);
				$gclink = $gcinfo['entries'][0][0];
				$gclink = substr($gclink, 9);
				$gclink = substr($gclink, 0, strpos($gclink, "'"));
				OpenDocument::debugmsg("GanttChart image: $gclink");
				return $gclink;
			}
			$att_name = $focus->column_fields[$field];
		}
	}
	$att_name = str_replace(' ', '_', $att_name);
	$SQL_att = 'SELECT at.attachmentsid, at.name, at.path
		FROM vtiger_seattachmentsrel ar
		LEFT JOIN vtiger_attachments at ON ar.attachmentsid=at.attachmentsid
		WHERE crmid=? AND at.name=?';
	$res_att = $adb->pquery($SQL_att, array($entid,$att_name));
	$imatge = $adb->query_result($res_att, 0, 'name');
	$ruta = $adb->query_result($res_att, 0, 'path');
	$prefix = $adb->query_result($res_att, 0, 'attachmentsid').'_';
	$path = $ruta.$prefix.$imatge;
	if (file_exists($path)) {
		OpenDocument::debugmsg("image found: $path");
		return $path;
	} else {
		if ($entity == 'ProductList') {
			return 'not_show_image';
		} else {
			return 'modules/evvtgendoc/not_found.jpg';
		}
	}
}

function eval_incluir($entity, $id, $module) {
	global $related_module, $iter_modules, $special_modules;
	if (module_exists($entity) || array_key_exists($entity, $special_modules)) {
		if ($entity == $module) {
			$entid = $id;
		} elseif (array_key_exists($entity, $iter_modules)) {
			$entid = $iter_modules[$entity][0];
		} else {
			require_once "modules/$module/$module.php";
			$focus = new $module();
			//Comprovem que l'entitat existisca i no estiga borrada
			if (!entity_exists($focus, $id, $module)) {
				return $entity;
			}
			$focus->retrieve_entity_info($id, $module);
			$entid = $focus->column_fields[$related_module[$module][$entity]];
		}

		$document = get_plantilla($entid);
		return $document['doc_no'];
	} else {
		return $entity;
	}
}

function iterations() {
	global $iter_modules;
	if (!empty($iter_modules)) {
		$keys = array_keys($iter_modules);
		$last_module = $keys[count($iter_modules)-1];
		$iter = count($iter_modules[$last_module]);
	} else {
		$iter = 0;
	}
	if ($iter == 0) {
		array_pop($iter_modules);
	}
	return $iter;
}

function pop_iter_modules() {
	global $iter_modules,$rootmod;
	$keys = array_keys($iter_modules);
	$last_module = $keys[count($iter_modules)-1];
	if ($rootmod != $last_module) {
		array_shift($iter_modules[$last_module]);
		if (count($iter_modules[$last_module]) == 0) {
			array_pop($iter_modules);
		}
	}
}

function get_current_iter_module() {
	global $iter_modules;
	$keys = array_keys($iter_modules);
	return $keys[count($iter_modules)-1];
}

function array_inverse($array) {
	$ret = array();
	foreach ($array as $key => $value) {
		if (is_array($value)) {
			foreach ($value as $multvalue) {
				if (array_key_exists($multvalue, $ret)) {
					$ret[$multvalue][] = $key;
				} else {
					$ret[$multvalue] = array($key);
				}
			}
		} else {
			if (array_key_exists($value, $ret)) {
				$ret[$value][] = $key;
			} else {
				$ret[$value] = array($key);
			}
		}
	}
	return $ret;
}

function module_exists($module) {
	global $adb;
	$res = $adb->pquery('SELECT COUNT(*) as qtab FROM vtiger_tab WHERE name=? AND presence=0', array($module));
	return ($adb->query_result($res, 0, 'qtab') != 0);
}

function entity_exists($focus, $id, $module) {
	global $adb;
	$params = array($id);
	switch ($module) {
		case 'Users':
		case 'CreatedBy':
		case 'ModifiedBy':
			$SQL = 'SELECT COUNT(*) as qtab FROM vtiger_users WHERE id=? AND deleted=0';
			break;
		default:
			$crmEntityTable = CRMEntity::getcrmEntityTableAlias($module);
			$SQL = 'SELECT COUNT(*) as qtab FROM '.$crmEntityTable.' WHERE vtiger_crmentity.crmid=? AND vtiger_crmentity.setype=? AND vtiger_crmentity.deleted=0';
			$params[] = $module;
	}
	$res = $adb->pquery($SQL, $params);
	return ($adb->query_result($res, 0, 'qtab') != 0);
}

function getProductList($module, $id) {
	global $adb;
	$fmt_number = array(
		'qty_per_unit' => 'D',
		'unit_price' => 'D',
		'commissionrate' => 'D',
		'quantity' => 'D',
		'listprice' => 'D',
		'discount_percent' => 'D',
		'discount_amount' => 'D',
		'tax1' => 'D',
		'tax2' => 'D',
		'tax3' => 'D',
		'extgros' => 'D',
		'extnet' => 'D',
		'linetax' => 'D',
		'linetotal' => 'D',
	);

	if (in_array($module, getInventoryModules())) {
		$query = "select vtiger_products.productname as productname, 'Products' as entitytype, vtiger_products.*,
			vtiger_inventoryproductrel.id, vtiger_inventoryproductrel.productid, sequence_no, quantity,
			listprice, comment, vtiger_inventoryproductrel.description, quantity * listprice AS extgros,
			coalesce(tax1, 0) AS tax1, coalesce(tax2, 0) AS tax2, coalesce(tax3, 0) AS tax3,
			COALESCE(discount_percent, COALESCE(discount_amount *100 / ( quantity * listprice ), 0)) AS discount_percent,
			COALESCE(discount_amount, COALESCE(discount_percent * quantity * listprice /100, 0)) AS discount_amount,
			(quantity * listprice) - COALESCE(discount_amount, COALESCE(discount_percent * quantity * listprice /100, 0)) AS extnet,
			((quantity * listprice) - COALESCE(discount_amount, COALESCE(discount_percent * quantity * listprice /100, 0))) * (COALESCE(tax1, 0) + COALESCE(tax2, 0) + COALESCE(tax3, 0)) /100 AS linetax,
			((quantity * listprice) - COALESCE(discount_amount, COALESCE(discount_percent * quantity * listprice /100, 0))) * (1 + (COALESCE(tax1, 0) + COALESCE(tax2, 0) + COALESCE(tax3, 0)) /100) AS linetotal
			from vtiger_inventoryproductrel
			inner join vtiger_products on vtiger_products.productid=vtiger_inventoryproductrel.productid
			where id=? ORDER BY sequence_no";
	}

	$result = $adb->pquery($query, array($id));
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$row = $adb->fetchByAssoc($result, $i);
		foreach ($fmt_number as $field => $format) {
			switch ($format) {
				case 'I':
					$row[$field] = number_format($row[$field], 0);
					break;
				case 'D':
					$row[$field] = CurrencyField::convertToUserFormat($row[$field], null, true);
					break;
			}
		}
		$row['comment'] = nl2br($row['comment']);
		//Parche para poder usar totalprice en plantillas antiguas.
		$row['totalprice'] = $row['linetotal'];
		$ret[] = $row;
	}
	return $ret;
}

function getServiceList($module, $id) {
	global $adb;

	$fmt_number = array(
		'qty_per_unit' => 'D',
		'unit_price' => 'D',
		'commissionrate' => 'D',
		'quantity' => 'D',
		'listprice' => 'D',
		'discount_percent' => 'D',
		'discount_amount' => 'D',
		'tax1' => 'D',
		'tax2' => 'D',
		'tax3' => 'D',
		'extgros' => 'D',
		'extnet' => 'D',
		'linetax' => 'D',
		'linetotal' => 'D',
	);

	if (in_array($module, getInventoryModules())) {
		$query = "select vtiger_service.servicename as productname, 'Services' as entitytype, vtiger_service.*,
			vtiger_inventoryproductrel.id, vtiger_inventoryproductrel.productid , sequence_no , quantity ,
			listprice, comment, vtiger_inventoryproductrel.description, quantity * listprice AS extgros,
			coalesce(tax1, 0) AS tax1, coalesce(tax2, 0) AS tax2, coalesce(tax3, 0) AS tax3,
			COALESCE(discount_percent, COALESCE(discount_amount * 100 / (quantity * listprice), 0)) AS discount_percent,
			COALESCE(discount_amount, COALESCE(discount_percent * quantity * listprice /100, 0)) AS discount_amount,
			(quantity * listprice) - COALESCE(discount_amount, COALESCE(discount_percent * quantity * listprice /100, 0)) AS extnet,
			((quantity * listprice) - COALESCE(discount_amount, COALESCE(discount_percent * quantity * listprice /100, 0))) * (COALESCE(tax1, 0) + COALESCE(tax2, 0) + COALESCE(tax3, 0)) /100 AS linetax,
			((quantity * listprice) - COALESCE(discount_amount, COALESCE(discount_percent * quantity * listprice /100, 0))) * (1 + (COALESCE(tax1, 0) + COALESCE(tax2, 0) + COALESCE(tax3, 0)) /100) AS linetotal
			from vtiger_inventoryproductrel
			inner join vtiger_service on vtiger_service.serviceid=vtiger_inventoryproductrel.productid 
			where id=? ORDER BY sequence_no";
	}

	$result = $adb->pquery($query, array($id));
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$row = $adb->fetchByAssoc($result, $i);
		foreach ($fmt_number as $field => $format) {
			switch ($format) {
				case 'I':
					$row[$field] = number_format($row[$field], 0);
					break;
				case 'D':
					$row[$field] = CurrencyField::convertToUserFormat($row[$field], null, true);
					break;
			}
		}
		$row['comment'] = nl2br($row['comment']);
		//Parche para poder usar totalprice en plantillas antiguas.
		$row['totalprice'] = $row['linetotal'];
		$ret[] = $row;
	}
	return $ret;
}

function getQuestionList($module, $id) {
	global $adb;

	if ($module == 'Revision') {
		$query="select pr.*, p.description, p.estadopregunta, p.nivel_pregunta" .
			" FROM vtiger_revision r LEFT JOIN vtiger_cuestiones cu ON cu.cuestionarioid=r.cuestionarioid ".
			" LEFT JOIN pregunta_revision pr ON pr.preguntasid=cu.preguntasid AND pr.revisionid=r.revisionid " .
			" LEFT JOIN vtiger_preguntas p ON pr.preguntasid=p.preguntasid ".
			" WHERE r.revisionid=? ORDER BY cu.cuestionesid";
	}

	$result = $adb->pquery($query, array($id));
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$row = $adb->fetchByAssoc($result, $i);
		$ret[] = $row;
	}
	return $ret;
}

function getQuestionListCat($module, $id) {
	global $adb;

	if ($module == 'Revision') {
		$query="select DISTINCT(pr.subcategoriapregunta)" .
			" FROM vtiger_revision r LEFT JOIN pregunta_revision pr ON pr.revisionid=r.revisionid ".
			" WHERE r.revisionid=? AND NOT pr.subcategoriapregunta IS NULL ORDER BY pr.subcategoriapregunta";
	}

	$result = $adb->pquery($query, array($id));
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$row = $adb->fetchByAssoc($result, $i);
		$selcountSI = "SELECT COUNT(preguntasid) "
			. "FROM pregunta_revision "
			. "WHERE revisionid=$id AND subcategoriapregunta='{$row['subcategoriapregunta']}' AND respuestaid='Si'";
		$resSI = $adb->getone($selcountSI);
		$selcountNO = "SELECT COUNT(preguntasid) "
			. "FROM pregunta_revision "
			. "WHERE revisionid=$id AND subcategoriapregunta='{$row['subcategoriapregunta']}' AND respuestaid='No'";
		$resNO = $adb->getone($selcountNO);
		$row['subcategoriapregunta'] = html_entity_decode($row['subcategoriapregunta'], ENT_NOQUOTES, 'UTF-8');
		$row['cuentaSI'] = $resSI;
		$row['cuentaNO'] = $resNO;
		$row['Revision'] = $id;
		$ret[] = $row;
	}
	return $ret;
}

function elimina_llave($str) {
	$arrstr = preg_replace("/\{.*?\}/i", '', $str);
	return trim($arrstr);
}

if (!function_exists('elimina_puntuacion')) {
	function elimina_puntuacion($cadena) {
		$replac = array(
		'&Ntilde;' => 'N',
		'&ntilde;' => 'n',
		'&agrave;' => 'a',
		'&egrave;' => 'e',
		'&igrave;' => 'i',
		'&ograve;' => 'o',
		'&ugrave;' => 'u',

		'&Agrave;' => 'A',
		'&Egrave;' => 'E',
		'&Igrave;' => 'I',
		'&Ograve;' => 'O',
		'&Ugrave;' => 'U',

		'&aacute;' => 'a',
		'&eacute;' => 'e',
		'&iacute;' => 'i',
		'&oacute;' => 'o',
		'&uacute;' => 'u',

		'&Aacute;' => 'A',
		'&Eacute;' => 'E',
		'&Iacute;' => 'I',
		'&Oacute;' => 'O',
		'&Uacute;' => 'U',

		'&acirc;' => 'a',
		'&ecirc;' => 'e',
		'&icirc;' => 'i',
		'&ocirc;' => 'o',
		'&ucirc;' => 'u',

		'&Acirc;' => 'A',
		'&Ecirc;' => 'E',
		'&Icirc;' => 'I',
		'&Ocirc;' => 'O',
		'&Ucirc;' => 'U',

		'&atilde;' => 'a',
		'&etilde;' => 'e',
		'&itilde;' => 'i',
		'&otilde;' => 'o',
		'&utilde;' => 'u',

		'&Atilde;' => 'A',
		'&Etilde;' => 'E',
		'&Itilde;' => 'I',
		'&Otilde;' => 'O',
		'&Utilde;' => 'U',

		'&auml;' => 'a',
		'&euml;' => 'e',
		'&iuml;' => 'i',
		'&ouml;' => 'o',
		'&uuml;' => 'u',

		'&Auml;' => 'A',
		'&Euml;' => 'E',
		'&Iuml;' => 'I',
		'&Ouml;' => 'O',
		'&Uuml;' => 'U',

		// Otras letras y caracteres especiales
		'&aring;' => 'a',

		// Agregar aqui mas caracteres si es necesario
		'&ordf;' => 'ª',

		'&Ntilde' => 'N',
		'&ntilde' => 'n',
		'&agrave' => 'a',
		'&egrave' => 'e',
		'&igrave' => 'i',
		'&ograve' => 'o',
		'&ugrave' => 'u',

		'&Agrave' => 'a',
		'&Egrave' => 'e',
		'&Igrave' => 'i',
		'&Ograve' => 'o',
		'&Ugrave' => 'u',

		'&aacute' => 'a',
		'&eacute' => 'e',
		'&iacute' => 'i',
		'&oacute' => 'o',
		'&uacute' => 'u',

		'&Aacute' => 'a',
		'&Eacute' => 'e',
		'&Iacute' => 'i',
		'&Oacute' => 'o',
		'&Uacute' => 'u',

		'&acirc' => 'a',
		'&ecirc' => 'e',
		'&icirc' => 'i',
		'&ocirc' => 'o',
		'&ucirc' => 'u',

		'&Acirc' => 'a',
		'&Ecirc' => 'e',
		'&Icirc' => 'i',
		'&Ocirc' => 'o',
		'&Ucirc' => 'u',

		'&atilde' => 'a',
		'&etilde' => 'e',
		'&itilde' => 'i',
		'&otilde' => 'o',
		'&utilde' => 'u',

		'&Atilde' => 'a',
		'&Etilde' => 'e',
		'&Itilde' => 'i',
		'&Otilde' => 'o',
		'&Utilde' => 'u',

		'&auml' => 'a',
		'&euml' => 'e',
		'&iuml' => 'i',
		'&ouml' => 'o',
		'&uuml' => 'u',

		'&Auml' => 'a',
		'&Euml' => 'e',
		'&Iuml' => 'i',
		'&Ouml' => 'o',
		'&Uuml' => 'u',

		// Otras letras y caracteres especiales
		'&aring' => 'a',

		// Agregar aqui mas caracteres si es necesario
		'&ordf' => 'ª',
		'&amp' => 'y',

		'(' => '',
		')' => '',
		'.' => '',
		',' => '',
		';' => '',
		':' => '',
		'-' => '',
		'/' => '',
		);
		// elimina espacios
		$cadena = str_replace(' ', '_', $cadena);
		return utf8_encode(strtr(utf8_decode($cadena), $replac));
	}
}

function multiple_values($values) {
	if (strpos($values, '|##|')) {
		$arrval = explode('|##|', $values);
		for ($i = 0; $i < count($arrval); $i++) {
			$arrval[$i] = trim($arrval[$i]);
		}
		sort($arrval);
	} else {
		$arrval = array($values);
	}
	return $arrval;
}

function is_related_list($module, $related) {
	global $adb;

	$tab_mod = getTabid($module);
	$tab_rel = getTabid($related);
	$SQL = 'SELECT COUNT(name) as cuenta FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=?';
	$res = $adb->pquery($SQL, array($tab_mod,$tab_rel));
	if ($adb->query_result($res, 0, 'cuenta') > 0) {
		return true;
	} elseif ($related == 'QuestionList' || $related == 'QuestionListCat' ||  $related == 'ProductList' || $related == 'ServiceList') {
		return true;
	} else {
		return false;
	}
}

function is_date($field, $module) {
	global $adb;

	$tabid = getTabid($module);
	$ui_date = array(5,6,23,64,70);
	$SQL = "SELECT uitype FROM vtiger_field WHERE fieldname=? AND tabid=?";
	$res = $adb->pquery($SQL, array($field,$tabid));
	$uitype = $adb->query_result($res, 0, 'uitype');

	return in_array($uitype, $ui_date);
}

function is_picklist($field, $module) {
	global $adb;
	$res = $adb->pquery('SELECT * FROM vtiger_field WHERE fieldname=? AND tabid=?', array($field, getTabid($module)));
	if ($res && $adb->num_rows($res)==1) {
		$fld = WebserviceField::fromQueryResult($adb, $res, 0);
		return ($fld->getFieldDataType()=='picklist');
	} else {
		return false;
	}
}

// Funcio que usem principalment al generar la documentacio, pero com es necessaria
// per a la marca {incluir}, la canvie de lloc.
function get_plantilla($entid) {
	global $adb,$root_directory,$app_strings,$current_user;

	$SQL = 'SELECT setype FROM vtiger_crmobject WHERE crmid=?';
	$res = $adb->pquery($SQL, array($entid));
	$relmodule = $adb->query_result($res, 0, 'setype');
	switch ($relmodule) {
		case 'Documents':
			$plantillaid = $entid;
			break;
		case 'Invoice':
			$fld_no = 'invoice_no';
			$camp_plantilla = 'document_id';
			break;
		case 'SalesOrder':
			$fld_no = 'salesorder_no';
			$camp_plantilla = 'document_id';
			break;
		case 'Quotes':
			$fld_no = 'quote_no';
			$camp_plantilla = 'document_id';
			break;
		case 'Accounts':
		case 'Contacts':
		case 'Revision':
			$camp_plantilla = 'document_id';
			break;
		case 'Equipos':
		case 'EsquemaRed':
		case 'Empleados':
		case 'Aplicaciones':
		case 'Fichero':
			$camp_plantilla = 'template';
			break;
		case 'Products':
			$camp_plantilla = 'plantilla';
			break;
		default:
			$camp_plantilla = 'plantillaid';
	}
	//EntityName
	$namefield = getEntityFieldNames($relmodule);
	$namefield = (array)$namefield['fieldname'];

	if ($relmodule != 'Documents') {
		$queryGenerator = new QueryGenerator($relmodule, $current_user);
		$sqlfields = array_merge(array($camp_plantilla,'id'), $namefield);
		if (!empty($fld_no)) {
			array_push($sqlfields, $fld_no);
		}
		$queryGenerator->setFields($sqlfields);
		$query = $queryGenerator->getQuery();
		$query.= ' and vtiger_crmentity.crmid=? ';
		$res = $adb->pquery($query, array($entid));
		$plantillaid = $adb->query_result($res, 0, $camp_plantilla);
		for ($i=0; $i < count($namefield); $i++) {
			$prefix = ($i > 0 ? ' ' : '');
			$entityname = $prefix.$adb->query_result($res, 0, $namefield[$i]);
		}
		$IDplantilla = $plantillaid;
		$ent_no = (!empty($fld_no) ? $adb->query_result($res, 0, $fld_no) : '');
	} else {
		$IDplantilla = $entid;
		$ent_no = '';
	}
	if ($plantillaid != 0) {
		$doc = new Documents();
		$doc->retrieve_entity_info($plantillaid, 'Documents');
		$title = $doc->column_fields['notes_title'];
		$no = $doc->column_fields['note_no'];
		$cat = isset($doc->column_fields['cat_documento']) ? $doc->column_fields['cat_documento'] : '';
		$fijado = !empty($doc->column_fields['fijado_portal']);
	}

	if (!empty($plantillaid)) {
		$SQL_att = 'SELECT at.attachmentsid, at.name, at.path
			FROM vtiger_seattachmentsrel ar
			LEFT JOIN vtiger_attachments at ON ar.attachmentsid=at.attachmentsid
			WHERE crmid=?';
		$res_att = $adb->pquery($SQL_att, array($plantillaid));
		$plantilla = $adb->query_result($res_att, 0, 'name');
		$ruta = $adb->query_result($res_att, 0, 'path');
		$prefix = $adb->query_result($res_att, 0, 'attachmentsid').'_';
		list($name,$ext) = explode('.', $plantilla);
	}

	return array(
		'documentid' => $IDplantilla,
		'document' => (empty($plantilla) ? '' : $root_directory.$ruta.$prefix.$plantilla),
		'relmodule' => $relmodule,
		'ent_no' => $ent_no,
		'name' => $name,
		'title' => $title,
		'doc_no' => $no,
		'fijado' => $fijado,
		'categoria' => $cat,
		'entityname' => (empty($entityname) ? '' : elimina_puntuacion(elimina_acentos($entityname))),
	);
}

function getEntityModule($crmid) {
	global $adb;
	$restype = $adb->pquery('SELECT setype FROM vtiger_crmobject WHERE crmid=? AND deleted=0', array($crmid));
	if ($restype) {
		$modname = $adb->query_result($restype, 0, 'setype');
	} else {
		$modname = '';
	}
	return $modname;
}

function getUitypefield($module, $fieldname) {
	global $adb;
	$restab = $adb->pquery('SELECT tabid FROM vtiger_tab WHERE name=?', array($module));
	$tabid = $adb->query_result($restab, 0, 'tabid');
	$resfield = $adb->pquery('SELECT uitype FROM vtiger_field WHERE tabid=? AND fieldname=?', array($tabid, $fieldname));
	return $adb->query_result($resfield, 0, 'uitype');
}

function getRelatedCRMIDs($relsql, $sortinfo = false) {
	global $adb;
	$relsql = empty($sortinfo) ? $relsql : $relsql . ' ORDER BY ' . $sortinfo['cname'] . ' ' . $sortinfo['order'];
	$res = $adb->pquery($relsql, array());
	$nr = $adb->num_rows($res);
	$ret = array('entries' => array());
	for ($i=0; $i<$nr; $i++) {
		$rcpid = $adb->query_result($res, $i, 'crmid');
		$ret['entries'][$rcpid] = $rcpid;
	}
	return $ret;
}

function areModulesRelated($relmodule, $mergemodule) {
	global $adb, $related_module;
	if (isset($related_module[$mergemodule]) && array_key_exists($relmodule, $related_module[$mergemodule])) {
		return true;
	} elseif ($relmodule=='Users') {
		$related_module[$mergemodule][$relmodule] = 'assigned_user_id';
		return true;
	} else {
		$sql = 'select fieldname
			from vtiger_fieldmodulerel
			inner join vtiger_field on vtiger_field.fieldid = vtiger_fieldmodulerel.fieldid
			where module=? and relmodule=? limit 1';
		$rs = $adb->pquery($sql, array($mergemodule, $relmodule));
		if ($adb->num_rows($rs)>0) {
			$related_module[$mergemodule][$relmodule] = $adb->query_result($rs, 0, 0);
			return true;
		} else {
			return false;
		}
	}
}
?>
