<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/

global $current_user;
global $adb;
if(isset($_REQUEST['file']) && ($_REQUEST['file'] !=''))
{
	checkFileAccessForInclusion('modules/Users/'.$_REQUEST['file'].'.php');
	require_once('modules/Users/'.$_REQUEST['file'].'.php');
}
?>
