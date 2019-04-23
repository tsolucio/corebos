<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'modules/Vtiger/EditView.php';

if (!empty($_REQUEST['record'])) {
	//Added to display the Faq comments information
	$smarty->assign('COMMENT_BLOCK', $focus->getFAQComments($_REQUEST['record']));
}

$smarty->display('salesEditView.tpl');
?>
