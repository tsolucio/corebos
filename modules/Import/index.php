<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

if (!isset($_REQUEST['step']) && !isset($_REQUEST['mode'])) {
	echo '<br><br>';
	$smarty = new vtigerCRM_Smarty();
	$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-info');
	$smarty->assign('ERROR_MESSAGE', getTranslatedString('ImportInfo', 'Import'));
	$smarty->display('applicationmessage.tpl');
} else {
	require_once 'modules/Import/api/Request.php';
	require_once 'modules/Import/controllers/Import_Index_Controller.php';
	require_once 'modules/Import/controllers/Import_ListView_Controller.php';
	require_once 'modules/Import/controllers/Import_Controller.php';
	global $current_user, $VTIGER_BULK_SAVE_MODE;

	$previousBulkSaveMode = isset($VTIGER_BULK_SAVE_MODE) ? $VTIGER_BULK_SAVE_MODE : false;
	$VTIGER_BULK_SAVE_MODE = true;
	$requestObject = new Import_API_Request($_REQUEST);
	Import_Index_Controller::process($requestObject, $current_user);
	$VTIGER_BULK_SAVE_MODE = $previousBulkSaveMode;
}
?>