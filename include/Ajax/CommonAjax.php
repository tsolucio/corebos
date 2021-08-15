<?php
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
$moduleFilepath='..';

if (isset($_REQUEST['module'])) {
	if (isset($_REQUEST['actionname'])) {
		require_once 'vtlib/Vtiger/controllers/ActionController.php';
		require_once 'include/utils/Request.php';
		$moduleFilepath = 'modules/' . $_REQUEST['module'] . '/actions/'. $_REQUEST['actionname'].'.php';
	} elseif (isset($_REQUEST['file'])) {
		$moduleFilepath = 'modules/'.$_REQUEST['module'].'/'.$_REQUEST['file'].'.php';
		if (!file_exists($moduleFilepath)) {
			$moduleFilepath = 'modules/Vtiger/'.$_REQUEST['file'].'.php';
		}
	}
}
checkFileAccessForInclusion($moduleFilepath);
require_once $moduleFilepath;

if (isset($_REQUEST['actionname']) && class_exists($_REQUEST['actionname'] . '_Action')) {
	$request = new Vtiger_Request($_REQUEST);
	$action = $_REQUEST['actionname'] . '_Action';
	$init = new $action($request);
}
?>
