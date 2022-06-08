<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $theme;
$custom_menu_array = array();
$custom_menu_array['LeadsMapping']['location'] = 'index.php?module=Settings&action=CustomFieldList&formodule=Leads';
$custom_menu_array['LeadsMapping']['image_src'] = vtiger_imageurl('custom.gif', $theme);
$custom_menu_array['LeadsMapping']['desc'] = getTranslatedString('LEADS_CUSTOM_FIELD_MAPPING_DESCRIPTION', 'Settings');
$custom_menu_array['LeadsMapping']['label'] = getTranslatedString('LEADS_CUSTOM_FIELD_MAPPING', 'Settings');
include 'modules/Vtiger/Settings.php';
?>
