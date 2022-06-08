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
$custom_menu_array['CONFIGURATION']['location'] = 'index.php?module=ModComments&action=BasicSettings&formodule=ModComments';
$custom_menu_array['CONFIGURATION']['image_src']= 'themes/images/toggleactive.png';
$custom_menu_array['CONFIGURATION']['desc'] = getTranslatedString('LBL_CONFIGURATION_DESCRIPTION', 'ModComments');
$custom_menu_array['CONFIGURATION']['label']= getTranslatedString('LBL_ModComments_SETTINGS', 'ModComments');
include 'modules/Vtiger/Settings.php';
?>