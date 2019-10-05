<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'modules/Webforms/Webforms.php';
require_once 'modules/Webforms/model/WebformsModel.php';

global $current_user, $theme;

if (isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == 'true') {
	if (Webforms_Model::existWebformWithName(vtlib_purify($_REQUEST['name']))) {
		print_r(json_encode(array('success' => false, 'result' => false)));
	} else {
		print_r(json_encode(array('success' => true, 'result' => true)));
	}
} else {
	Webforms::checkAdminAccess($current_user);

	$webform = new Webforms_Model($_REQUEST);
	try {
		$webform->save();
		$URL = 'WebformsDetailView&id=' . $webform->getId();
	} catch (Exception $e) {
		$URL = 'Error&errormsg=' . $e->getMessage();
	}
	header('Location: index.php?module=Webforms&action=' . $URL);
}
?>
