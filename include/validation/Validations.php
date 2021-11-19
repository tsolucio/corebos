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
 *************************************************************************************************/

function validate_IBAN_BankAccount($field, $iban, $params, $fields) {
	/* Ejemplos
	Bank Name 	IBAN 	Entidad 	Oficina 	Digito Control 	Cuenta
	CAIXABANK 	ES9121000418450200051332 	1210 	0418 	45 	0200051332
	BANCO SANTANDER 	ES1800491500042710151321 	0049 	1500 	04 	2710151321
	ABANCA CORPORACION BANCARIA 	ES3320805801143040000499 	2080 	5801 	14 	3040000499
	BANCO DE CREDITO BALEAR 	ES0200246912500600865953 	0024 	6912 	50 	0600865953
	BANCO ESPAÃ‘OL DE CREDITO 	ES0200302053020000875271 	0030 	2053 	02 	0000875271
	BANCO SANTANDER 	ES1800492352072414205418 	0049 	2352 	07 	2414205418
	IBERCAJA BANCO 	ES3320852066640300082802 	2085 	2066 	64 	0300082802
	*/
	// definimos un array de valores con el valor de cada letra
	$letras = array (
		'A' => 10,		'B' => 11,
		'C' => 12,		'D' => 13,
		'E' => 14,		'F' => 15,
		'G' => 16,		'H' => 17,
		'I' => 18,		'J' => 19,
		'K' => 20,		'L' => 21,
		'M' => 22,		'N' => 23,
		'O' => 24,		'P' => 25,
		'Q' => 26,		'R' => 27,
		'S' => 28,		'T' => 29,
		'U' => 30,		'V' => 31,
		'W' => 32,		'X' => 33,
		'Y' => 34,		'Z' => 35,
	);

	// Eliminamos los posibles espacios al inicio y final
	$iban = trim($iban);
	// Convertimos en mayusculas
	$iban = strtoupper($iban);
	// eliminamos espacio y guiones que haya en el iban
	$iban = str_replace(array(' ','-'), '', $iban);

	if (strlen($iban) == 24) {
		// obtenemos los codigos de las dos letras
		$valorLetra1 = $letras[substr($iban, 0, 1)];
		$valorLetra2 = $letras[substr($iban, 1, 1)];
		// obtenemos los siguientes dos valores
		$siguienteNumeros = substr($iban, 2, 2);
		$valor = substr($iban, 4, strlen($iban)) . $valorLetra1 . $valorLetra2 . $siguienteNumeros;
		return (bcmod($valor, 97) == 1);
	}
	return false;
}

function validate_EU_VAT_NotBlank($field, $num_tva, $params, $fields) {
	if ($num_tva=='') {
		return false;
	}
	return validate_EU_VAT($field, $num_tva, $params, $fields);
}

// Intra-Community VAT number verification - www.bigotconsulting.fr (thanks)
function validate_EU_VAT($field, $num_tva, $params, $fields) {
	if ($num_tva=='') {
		return true;
	}
	if (extension_loaded('soap')) {
		ini_set('soap.wsdl_cache_enabled', '0');
		$prefix = substr($num_tva, 0, 2);
		$tva = substr($num_tva, 2);
		$param = array('countryCode' => $prefix, 'vatNumber' => $tva);
		$soap = new SoapClient('https://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl');
		try {
			$xml = $soap->checkVat($param);
		} catch (Exception $e) {
			return false;
		}
		return (($xml->valid)=='1');
	}
	return false;
}

/** check if record exists with the same value in the given field
 * params[0] module name
 * params[1] crmid
 */
function validate_notDuplicate($field, $fieldval, $params, $fields) {
	global $adb, $current_user;
	$module = $params[0];
	$crmid = $params[1];
	$otherfields = $params[2];
	$queryGenerator = new QueryGenerator($module, $current_user);
	$queryGenerator->setFields(array('id'));
	if (getUItypeByFieldName($module, $field)==10) {
		if (!empty($fieldval)) {
			if (strpos($fieldval, 'x') > 0) {
				list($wsid, $relcrmid) = explode('x', $fieldval);
			} else {
				$relcrmid = $fieldval;
			}
			$relmod = getSalesEntityType($relcrmid);
			$queryGenerator->addReferenceModuleFieldCondition($relmod, $field, 'id', $relcrmid, 'e');
		}
	} else {
		$queryGenerator->addCondition($field, $fieldval, 'e');
	}
	if (isset($crmid) && $crmid !='') {
		$queryGenerator->addCondition('id', $crmid, 'n', 'and');
	}
	if (isset($otherfields)) {
		foreach ($otherfields as $field) {
			if (getUItypeByFieldName($module, $field)==10) {
				if (!empty($fields[$field])) {
					if (strpos($fields[$field], 'x') > 0) {
						list($wsid, $relcrmid) = explode('x', $fields[$field]);
					} else {
						$relcrmid = $fields[$field];
					}
					$relmod = getSalesEntityType($relcrmid);
					$queryGenerator->addReferenceModuleFieldCondition($relmod, $field, 'id', $relcrmid, 'e', 'and');
				}
			} else {
				$queryGenerator->addCondition($field, $fields[$field], 'e', 'and');
			}
		}
	}
	$query = $queryGenerator->getQuery();
	$result = $adb->pquery($query, array());
	return ($result && $adb->num_rows($result) == 0);
}

/** check if related record exists on given module
 * params[0] related module name
 */
function validateRelatedModuleExists($field, $fieldval, $params, $fields) {
	global $adb;
	$existsrelated = true;
	$relatedmodule = $params[0];
	if (!empty($relatedmodule) && !empty($fields['record']) && !empty($fields['module'])) {
		$crmid = $fields['record'];
		$module = $fields['module'];
		$moduleId = getTabid($module);
		$relatedModuleId = getTabid($relatedmodule);
		$moduleInstance = CRMEntity::getInstance($module);
		$relationResult = $adb->pquery(
			'SELECT * FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=?',
			array($moduleId, $relatedModuleId)
		);

		if (!$relationResult || !$adb->num_rows($relationResult)) {
			// MODULES_NOT_RELATED
			return false;
		}

		$relationInfo = $adb->fetch_array($relationResult);
		$params = array($crmid, $moduleId, $relatedModuleId);
		global $GetRelatedList_ReturnOnlyQuery, $currentModule;
		$holdValue = $GetRelatedList_ReturnOnlyQuery;
		$GetRelatedList_ReturnOnlyQuery = true;
		$holdCM = $currentModule;
		$currentModule = $module;
		$relationData = call_user_func_array(array($moduleInstance, $relationInfo['name']), array_values($params));
		$currentModule = $holdCM;
		$GetRelatedList_ReturnOnlyQuery = $holdValue;
		if (!isset($relationData['query'])) {
			// OPERATIONNOTSUPPORTED
			return false;
		}
		$query = mkXQuery($relationData['query'], '1');
		$query = stripTailCommandsFromQuery($query).' LIMIT 1';
		$result = $adb->pquery($query, array());
		if ($result) {
			$existsrelated = ($adb->num_rows($result) > 0);
		}
	}
	return $existsrelated;
}

/** accept a workflow expression and evaluate it
 * in the context of the new screen values
 */
function validate_expression($field, $fieldval, $params, $fields) {
	$bmap = $params[1];
	// check that cbmapid is correct and load it
	if (preg_match('/^[0-9]+x[0-9]+$/', $bmap)) {
		list($cbmapws, $bmap) = explode('x', $bmap);
	}
	if (is_numeric($bmap)) {
		$cbmap = cbMap::getMapByID($bmap);
	} else {
		$cbmapid = GlobalVariable::getVariable('BusinessMapping_'.$bmap, cbMap::getMapIdByName($bmap));
		$cbmap = cbMap::getMapByID($cbmapid);
	}
	if (empty($params[0])) { // isNew
		if (empty($cbmap) || ($cbmap->column_fields['maptype'] != 'Condition Expression' && $cbmap->column_fields['maptype'] != 'DecisionTable')) {
			return false;
		}
		if ($cbmap->column_fields['maptype'] == 'Condition Expression') {
			return $cbmap->ConditionExpression($fields);
		} else {
			$dt = $cbmap->DecisionTable($fields);
			return ($dt!='__DoesNotPass__');
		}
	} else { // editing
		$fields['record_id'] = $params[0];
		$return = coreBOS_Rule::evaluate($bmap, $fields);
		if ($cbmap->column_fields['maptype'] == 'DecisionTable') {
			return ($return!='__DoesNotPass__');
		} else {
			return $return;
		}
	}
}

/** validate taxes on Products and Services **/
function cbTaxclassRequired($field, $fieldval, $params, $fields) {
	if ($fields['action'] == 'DetailViewEdit') {
		return true;
	}
	require_once 'include/utils/InventoryUtils.php';
	if ($fields['mode'] == 'edit') {
		$tax_details = getTaxDetailsForProduct($fields['record'], 'available_associated');
	} else {
		$tax_details = getAllTaxes('available');
	}
	// at least one of the checkboxes must be accepted
	$accepted = false;
	$i = 0;
	while (!$accepted && $i < count($tax_details)) {
		$accepted = ($fields[$tax_details[$i]['taxname'].'_check']=='on' || $fields[$tax_details[$i]['taxname'].'_check']=='1');
		$i++;
	}
	// and it's value positive
	if ($accepted && $fields[$tax_details[$i-1]['taxname']] < 0) {
		return false;
	}
	return $accepted;
}
?>