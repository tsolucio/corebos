<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once 'Smarty_setup.php';
require_once 'include/utils/utils.php';
require_once 'modules/Tooltip/TooltipUtils.php';

global $mod_strings;
global $app_strings;
global $app_list_strings;

global $adb,$currentModule;
global $theme;

$smarty=new vtigerCRM_Smarty;

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$module_name = vtlib_purify($_REQUEST['module_name']);
$field_name = vtlib_purify($_REQUEST['field_name']);

$related_fields = getFieldList($module_name,$field_name);

$fieldlist = array();
$tabid = getTabid($module_name);

$sql = "select * from vtiger_field where fieldname= ? and tabid= ? and vtiger_field.presence in (0,2)";
$result = $adb->pquery($sql,array($field_name,$tabid));
$fieldid = $adb->query_result($result,0,"fieldid");

$fieldlist[$module_name] = getRelatedFieldslist($fieldid, $related_fields);
if($_REQUEST['module_name'] != ''){
	$smarty->assign("DEF_MODULE",vtlib_purify($_REQUEST['module_name']));
}else{
	$smarty->assign("DEF_MODULE",'Accounts');
}

$smarty->assign("FIELDID",$fieldid);
$smarty->assign("FIELD_INFO",$module_name);
$smarty->assign("FIELD_LISTS",$fieldlist);
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);
$smarty->display(vtlib_getModuleTemplate($currentModule,'EditQuickView.tpl'));

?>