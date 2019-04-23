<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
if ($_REQUEST['mode'] != 'edit' && $_REQUEST['filelocationtype'] == 'I' && $_FILES['filename']['error'] == 4 && $_FILES['filename']['size'] == 0) {
	$_REQUEST['filelocationtype'] = 'E';
}
if (isset($_REQUEST['notecontent']) && $_REQUEST['notecontent'] != '') {
	$_REQUEST['notecontent'] = vtlib_purify($_REQUEST['notecontent']);
}
require_once 'modules/Vtiger/Save.php';
?>
