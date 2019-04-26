<?php
/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/utils/utils.php';
require_once 'Smarty_setup.php';

$mode = isset($_REQUEST['mode']) ? vtlib_purify($_REQUEST['mode']) : '';

if ($mode == 'Ajax' && !empty($_REQUEST['xmode'])) {
	$mode = vtlib_purify($_REQUEST['xmode']);
}

/** Based on the mode include the MailScanner file. */
if ($mode == 'scannow') {
	include 'vtigercron.php';
} elseif ($mode == 'edit') {
	include 'modules/Settings/MailScanner/MailScannerEdit.php';
} elseif ($mode == 'save') {
	include 'modules/Settings/MailScanner/MailScannerSave.php';
} elseif ($mode == 'remove') {
	include 'modules/Settings/MailScanner/MailScannerRemove.php';
} elseif ($mode == 'rule') {
	include 'modules/Settings/MailScanner/MailScannerRule.php';
} elseif ($mode == 'ruleedit') {
	include 'modules/Settings/MailScanner/MailScannerRuleEdit.php';
} elseif ($mode == 'rulesave') {
	include 'modules/Settings/MailScanner/MailScannerRuleSave.php';
} elseif ($mode == 'rulemove_up' || $mode == 'rulemove_down') {
	include 'modules/Settings/MailScanner/MailScannerRuleMove.php';
} elseif ($mode == 'ruledelete') {
	include 'modules/Settings/MailScanner/MailScannerRuleDelete.php';
} elseif ($mode == 'folder') {
	include 'modules/Settings/MailScanner/MailScannerFolder.php';
} elseif ($mode == 'foldersave') {
	include 'modules/Settings/MailScanner/MailScannerFolderSave.php';
} elseif ($mode == 'folderupdate') {
	include 'modules/Settings/MailScanner/MailScannerFolderUpdate.php';
} else {
	include 'modules/Settings/MailScanner/MailScannerInfo.php';
}
?>
