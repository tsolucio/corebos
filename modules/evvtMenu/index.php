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
global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $current_user;
require('user_privileges/user_privileges_'.$current_user->id.'.php');

require_once('Smarty_setup.php');
require_once('modules/evvtMenu/evvtMenu.inc');
$category = getParentTab();
$smarty = new vtigerCRM_Smarty();

$menu_structure = getMenuBranch(0);
$elements = getMenuPicklist(0,0);
$json = getMenuJSON2();

$smarty->assign("PARENTS", $elements);
$smarty->assign("MENUSTRUCTURE", $json);
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

$smarty->display(vtlib_getModuleTemplate($currentModule,'index.tpl'));
?>
