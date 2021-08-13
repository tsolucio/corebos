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
$custom_menu_array['CYP_SERVER_CONFIGURATION']['location'] = 'index.php?module=CobroPago&action=CobroPagoConfigServer';
$custom_menu_array['CYP_SERVER_CONFIGURATION']['image_src']= 'modules/CobroPago/settings.png';
$custom_menu_array['CYP_SERVER_CONFIGURATION']['desc']     = getTranslatedString('SERVER_CONFIGURATION_DESCRIPTION', 'CobroPago');
$custom_menu_array['CYP_SERVER_CONFIGURATION']['label']    = getTranslatedString('SERVER_CONFIGURATION', 'CobroPago');
include 'modules/Vtiger/Settings.php';
?>