<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
include_once 'modules/cbMap/cbMap.php';
require_once 'data/CRMEntity.php';
require_once 'include/utils/utils.php';
require_once 'include/database/PearDatabase.php';
require_once 'include/utils/utils.php';

$mapid = vtlib_purify($_REQUEST['mapid']);
$focus1 = CRMEntity::getInstance('cbMap');
$focus1->retrieve_entity_info($mapid, 'cbMap');
$maptype = $focus1->column_fields['maptype'];
$content = $focus1->column_fields['content'];

function libxml_display_error($error) {
	$return = "<br/>\n";
	switch ($error->level) {
		case LIBXML_ERR_WARNING:
			$return .= 'Warning: ';
			break;
		case LIBXML_ERR_ERROR:
			$return .= 'Error: ';
			break;
		case LIBXML_ERR_FATAL:
			$return .= 'Fatal Error: ';
			break;
	}
	$return .= trim($error->message);
	$return .= " on line <b>$error->line</b>\n";
	return $return;
}

function libxml_display_errors() {
	$errors = libxml_get_errors();
	foreach ($errors as $error) {
		print libxml_display_error($error);
	}
	libxml_clear_errors();
}

// Enable user error handling
libxml_use_internal_errors(true);
$content1 = htmlspecialchars_decode($content);
$xml = new DOMDocument();
$xml->loadXML($content1);

if (!file_exists('modules/cbMap/XSD_schemas/' . $maptype . '.xsd')) {
	echo 'VALIDATION_NOT_IMPLEMENTED_YET';
} else {
	if (!$xml->schemaValidate('modules/cbMap/XSD_schemas/' . $maptype . '.xsd')) {
		libxml_display_errors();
	}
}
