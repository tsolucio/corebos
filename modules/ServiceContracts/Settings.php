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
$custom_menu_array['SyncHelpDesk']['location'] = 'index.php?module=ServiceContracts&action=HDSync';
$custom_menu_array['SyncHelpDesk']['image_src'] = 'include/LD/assets/icons/utility/sync_60.png';
$custom_menu_array['SyncHelpDesk']['label'] = getTranslatedString('SyncHelpDesk', 'ServiceContracts');
$custom_menu_array['SyncHelpDesk']['desc'] = getTranslatedString('SyncHelpDeskDescription', 'ServiceContracts');
include 'modules/Vtiger/Settings.php';
?>
