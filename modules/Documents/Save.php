<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
if (empty($_REQUEST['filelocationtype']) ||
	((empty($_REQUEST['mode']) || $_REQUEST['mode'] != 'edit')
	&& (empty($_REQUEST['filelocationtype']) || $_REQUEST['filelocationtype'] == 'I')
	&& !empty($_FILES['filename']['error']) && $_FILES['filename']['error'] == 4
	&& empty($_FILES['filename']['size']))
) {
	$_REQUEST['filelocationtype'] = 'E';
}
if (isset($_REQUEST['notecontent']) && $_REQUEST['notecontent'] != '') {
	$_REQUEST['notecontent'] = vtlib_purify($_REQUEST['notecontent']);
}
require_once 'modules/Vtiger/Save.php';
?>
