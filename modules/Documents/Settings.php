<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
$custom_menu_array = array();
// Mass Upload widget
$custom_menu_array['CONFIGURATION']['location'] = 'index.php?module=Documents&action=BasicSettings&formodule=Documents';
$custom_menu_array['CONFIGURATION']['image_src']= 'themes/images/toggleactive.png';
$custom_menu_array['CONFIGURATION']['desc'] = getTranslatedString('LBL_MASSUPLOAD_DESCRIPTION', 'Documents');
$custom_menu_array['CONFIGURATION']['label']= getTranslatedString('LBL_DOCUMENTS_MASSUPLOAD', 'Documents');
// Storage space increment
$custom_menu_array['STORAGESIZE_CONFIGURATION']['location'] = 'index.php?module=Documents&action=StorageConfig&formodule=Documents';
$custom_menu_array['STORAGESIZE_CONFIGURATION']['image_src']= 'modules/Documents/images/HardDrive4848.png';
$custom_menu_array['STORAGESIZE_CONFIGURATION']['desc'] = getTranslatedString('STORAGESIZE_CONFIGURATION_DESCRIPTION', 'Documents');
$custom_menu_array['STORAGESIZE_CONFIGURATION']['label'] = getTranslatedString('STORAGESIZE_CONFIGURATION', 'Documents');
include 'modules/Vtiger/Settings.php';
?>