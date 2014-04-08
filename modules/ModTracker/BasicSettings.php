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
require_once('Smarty_setup.php');
require_once('modules/ModTracker/ModTrackerUtils.php');

global $app_strings, $mod_strings, $current_language,$currentModule, $theme,$current_user;

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

if(!is_admin($current_user)) {
	echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
	echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>
			<table border='0' cellpadding='5' cellspacing='0' width='98%'>
				<tr>
					<td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme) . "' ></td>
					<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>$app_strings[LBL_PERMISSION]</span></td>
				</tr>
				<tr>
					<td class='small' align='right' nowrap='nowrap'>
						<a href='javascript:window.history.back();'>$app_strings[LBL_GO_BACK]</a><br>
					</td>
				</tr>
			</table>
		</div>";
	echo "</td></tr></table>";
	die;
}

$category = getParentTab();

$smarty = new vtigerCRM_Smarty;
$smarty->assign("MOD",$mod_strings);
$smarty->assign("APP",$app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign('CATEGORY',$category);

$tabid = vtlib_purify($_REQUEST['tabid']);
$status = vtlib_purify($_REQUEST['status']);

if($status != '' && $tabid != ''){
	ModTrackerUtils::modTrac_changeModuleVisibility($tabid, $status);
}
$infomodules = ModTrackerUtils::modTrac_getModuleinfo();
$smarty->assign('INFOMODULES',$infomodules);
$smarty->assign('MODULE',$module);

if($_REQUEST['ajax'] != true) {
	$smarty->display(vtlib_getModuleTemplate($currentModule,'BasicSettings.tpl'));
} else {
	$smarty->display(vtlib_getModuleTemplate($currentModule,'BasicSettingsContents.tpl'));
}
?>