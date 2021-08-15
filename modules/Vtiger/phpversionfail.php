<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
header('Content-Type: text/html; charset=UTF-8');
header('X-Frame-Options: DENY');
include_once 'vtigerversion.php';
require_once 'include/utils/utils.php';
require_once 'Smarty_setup.php';

global $default_charset;

$smarty = new vtigerCRM_Smarty();
$smarty->assign('LBL_CHARSET', $default_charset);
$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-danger');
$smarty->assign(
	'ERROR_MESSAGE',
	'A PHP version from 7.0.x (7.3.x minimum recommended) to 7.4.3 is required.<br>Your current PHP version is '.phpversion().'<br>Adapt your PHP installation, and try again!'
);
$smarty->display('modules/Vtiger/phpversionfail.tpl');
die();
?>