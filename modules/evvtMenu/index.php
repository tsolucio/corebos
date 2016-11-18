<?php
/*************************************************************************************************
 * Copyright 2013 JPL TSolucio, S.L.  --  This file is a part of JPL TSolucio vtiger CRM Extensions.
* You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
* Vizsage Public License (the "License"). You may not use this file except in compliance with the
* License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
* and share improvements. However, for proper details please read the full License, available at
* http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
* the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
* applicable law or agreed to in writing, any software distributed under the License is distributed
* on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and limitations under the
* License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
*************************************************************************************************
*  Module       : evvtMenu
*  Version      : 1.0
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/

global $app_strings, $mod_strings, $current_language, $currentModule, $theme,$current_user;
require('user_privileges/user_privileges_'.$current_user->id.'.php');

if(!$is_admin) {
	echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
	echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>

		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src= " . vtiger_imageurl('denied.gif', $theme) . " ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'>
			<span class='genHeaderSmall'>$app_strings[LBL_PERMISSION]</span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>
		<a href='javascript:window.history.back();'>$app_strings[LBL_GO_BACK]</a><br>
		</td>
		</tr>
		</tbody></table>
		</div>";
	echo "</td></tr></table>";
	exit;
}

require_once('Smarty_setup.php');
require_once('modules/evvtMenu/evvtMenu.inc');
$category = getParentTab();
$smarty = new vtigerCRM_Smarty();

$menu_structure = getMenuBranch(0);
$menu_html = getMenuBranchHTML($menu_structure);
$elements = getMenuElements();
$json = getMenuJSON2();

$smarty->assign("PARENTS", $elements);
$smarty->assign("MENUSTRUCTURE", $json);
$smarty->assign("MENU", $menu_html);
$smarty->assign("PROFILES", getAllProfileInfo());
$result = $adb->query('select name from vtiger_tab where presence = 0 order by name');
$modulelist = array();
while($moduleinfo=$adb->fetch_array($result)) {
	$modulelist[$moduleinfo['name']] = $moduleinfo['name'];
}
$smarty->assign("MODNAMES", $modulelist);
$smarty->assign("THEME", $theme);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('CATEGORY', $category);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('MODE',$mode);
global $KENDOUI_URL;
$smarty->assign('KUIDIR', rtrim($KENDOUI_URL,'/').'/');

$smarty->display(vtlib_getModuleTemplate($currentModule,'index.tpl'));

?>
