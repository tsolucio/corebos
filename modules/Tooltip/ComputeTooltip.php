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
require_once 'include/utils/CommonUtils.php';
require_once 'include/Webservices/DescribeObject.php';
require_once 'include/Webservices/Query.php';
require_once 'modules/Tooltip/TooltipUtils.php';

global $current_user,$log;

$modname = vtlib_purify($_REQUEST['modname']);
$id = vtlib_purify($_REQUEST['id']);
$fieldname = vtlib_purify($_REQUEST['fieldname']);
$tabid = getTabid($modname);
if($tabid == '13' && $fieldname == 'title')
	$fieldname = 'ticket_title';
$result = ToolTipExists($fieldname,$tabid);
if($result !== false){
//get tooltip information
	$viewid = 1;	//viewid is 1 by default
	$descObject = vtws_describe($modname,$current_user);
	$id = vtws_getWebserviceEntityId($modname, $id);
	$sql = "select * from $modname where id ='$id';";
	$result = vtws_query($sql, $current_user);
	if(empty($result)){
		echo getTranslatedString('LBL_RECORD_NOT_FOUND');
		exit(0);
	}
	$result = vttooltip_processResult($result, $descObject);
	$text = getToolTipText($viewid, $fieldname,$modname,$result);
	$tip = getToolTip($text);
	echo $tip;
}else {
	echo false;
}

?>