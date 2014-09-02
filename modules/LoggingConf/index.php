<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
* granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *  Module       : EntittyLog
 *  Version      : 5.4.0
 *  Author       : LoggingConf
 *************************************************************************************************/
require_once('include/utils/UserInfoUtil.php');
require_once('include/utils/utils.php');
require_once('modules/LoggingConf/LoggingUtils.php');


global $mod_strings;
global $app_strings;
global $app_list_strings;

$smarty = new vtigerCRM_Smarty;


global $adb;
global $theme;
global $theme_path;
global $image_path;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

//$field_module = Array('Leads','Accounts','Contacts','Potentials','HelpDesk','Products','Notes','Calendar','Events','Vendors','PriceBooks','Quotes','PurchaseOrder','SalesOrder','Invoice','Campaigns','Faq');
 $field_module=getLoggingModules();
 
$allfields=Array();
foreach($field_module as $fld_module=>$mod_name)
{
	$fieldListResult = getDefOrgFieldList($fld_module);
	$noofrows = $adb->num_rows($fieldListResult);
	$language_strings = return_module_language($current_language,$fld_module);
	$allfields[$fld_module] = getStdOutput($fieldListResult, $noofrows, $language_strings,$profileid);
}

function getStdOutput($fieldListResult, $noofrows, $lang_strings,$profileid)
{
	global $adb;
	global $image_path,$theme;
        require_once('modules/LoggingConf/LoggingUtils.php');
	$standCustFld = Array();
	for($i=0; $i<$noofrows; $i++)
	{
		$uitype = $adb->query_result($fieldListResult,$i,"uitype");
		$fieldlabel = $adb->query_result($fieldListResult,$i,"fieldlabel");
		$typeofdata = $adb->query_result($fieldListResult,$i,"typeofdata");
		$fieldtype = explode("~",$typeofdata);
		if($lang_strings[$fieldlabel] !='')
			$standCustFld []= $lang_strings[$fieldlabel];
		else
			$standCustFld []= $fieldlabel;


		if(isLogged($adb->query_result($fieldListResult,$i,"fieldid") ,$adb->query_result($fieldListResult,$i,"tabid")))
		{
			$visible ="<img src='" . vtiger_imageurl('prvPrfSelectedTick.gif', $theme) . "'>";
		}
		else
		{
			$visible = "<img src='" . vtiger_imageurl('no.gif', $theme) . "'>";
		}
		$standCustFld []= $visible;
	}
	$standCustFld=array_chunk($standCustFld,2);
	$standCustFld=array_chunk($standCustFld,4);
	return $standCustFld;
}
if($_REQUEST['fld_module'] != '')
	$smarty->assign("DEF_MODULE",vtlib_purify($_REQUEST['fld_module']));
else
	$smarty->assign("DEF_MODULE",'Movement');
$smarty->assign("FIELD_INFO",$field_module);
$smarty->assign("FIELD_LISTS",$allfields);
$smarty->assign("MOD", return_module_language($current_language,'LoggingConf'));
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);
$smarty->assign("MODE",'view');
$smarty->display("LoggConfContents.tpl");
?>

