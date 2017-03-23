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
		"A" => 10,		"B" => 11,
		"C" => 12,		"D" => 13,
		"E" => 14,		"F" => 15,
		"G" => 16,		"H" => 17,
		"I" => 18,		"J" => 19,
		"K" => 20,		"L" => 21,
		"M" => 22,		"N" => 23,
		"O" => 24,		"P" => 25,
		"Q" => 26,		"R" => 27,
		"S" => 28,		"T" => 29,
		"U" => 30,		"V" => 31,
		"W" => 32,		"X" => 33,
		"Y" => 34,		"Z" => 35 
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
		if (bcmod($valor, 97) == 1) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
	return false;
}

// Intra-Community VAT number verification - www.bigotconsulting.fr (thanks)
function validate_EU_VAT($field, $num_tva, $params, $fields) {
	if ($num_tva=='') return true;
	if (extension_loaded('soap')) {
		ini_set("soap.wsdl_cache_enabled", "0");
		$prefix = substr($num_tva, 0, 2);
		$tva = substr($num_tva, 2);
		$param = array('countryCode' => $prefix, 'vatNumber' => $tva);
		$soap = new SoapClient('http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl');
		try {
			$xml = $soap->checkVat($param);
		} catch (Exception $e) {
			return false;
		}
		return (($xml->valid)=="1");
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
	$queryGenerator = new QueryGenerator($module, $current_user);
	$queryGenerator->setFields(array('id'));
	$queryGenerator->addCondition($field, $fieldval, 'e');
	if(isset($crmid) && $crmid !='') {
		$queryGenerator->addCondition('id',$crmid,'ne','and');
	}
	$query = $queryGenerator->getQuery();
	$result = $adb->pquery($query, array());
	if ($result and $adb->num_rows($result) == 0) {
		return true;
	} else {
		return false;
	}
}

?>