<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once('modules/cbMap/cbMap.php');
require_once('data/CRMEntity.php');
require_once('include/utils/utils.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');

$mapid  = $_REQUEST["mapid"];
$focus1 = CRMEntity::getInstance("cbMap");
$focus1->retrieve_entity_info($mapid, "cbMap");
$maptype = $focus1->column_fields["maptype"];
$content = $focus1->column_fields["content"];

function libxml_display_error($error)
{
    $return = "<br/>\n";
    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "Warning: ";
            break;
        case LIBXML_ERR_ERROR:
            $return .= "Error: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "Fatal Error: ";
            break;
    }
    $return .= trim($error->message);
    $return .= " on line <b>$error->line</b>\n";
    
    return $return;
}

function libxml_display_errors()
{
    $errors = libxml_get_errors();
    foreach ($errors as $error) {
        print libxml_display_error($error);
    }
    libxml_clear_errors();
}

// Enable user error handling
libxml_use_internal_errors(true);
$content1 = htmlspecialchars_decode($content);
$xml      = new DOMDocument();
$xml->loadXML($content1);

if (!file_exists('modules/cbMap/XSD_schemas/' . $maptype . '.xsd')) {
    echo "VALIDATION_NOT_IMPLEMENTED_YET";
    
} else {
    if (!$xml->schemaValidate('modules/cbMap/XSD_schemas/' . $maptype . '.xsd')) {
        libxml_display_errors();
    }
}
?>