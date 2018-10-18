<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************/
require_once 'include/home.php';
require_once 'Smarty_setup.php';
require_once 'modules/Home/HomeBlock.php';
require_once 'include/database/PearDatabase.php';
require_once 'include/utils/UserInfoUtil.php';
require_once 'include/utils/CommonUtils.php';
require_once 'include/freetag/freetag.class.php';
require_once 'modules/Home/HomeUtils.php';

global $app_strings, $mod_strings;
global $adb, $current_user, $theme, $current_language;

$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';

$smarty = new vtigerCRM_Smarty;
$homeObj=new Homestuff;

// Performance Optimization
$tabrows = vtlib_prefetchModuleActiveInfo();

//$query="select name,tabid from vtiger_tab where tabid in (select distinct(tabid) from vtiger_field where tabid <> 29 and tabid <> 16 and tabid <>10) order by name";

// Performance Optimization: Re-written to ignore extension and inactive modules
$modulenamearr = array();
foreach ($tabrows as $resultrow) {
	if ($resultrow['isentitytype'] != '0') {
		// Eliminate: Events, Emails
		if ($resultrow['tabid'] == '16' || $resultrow['tabid'] == '10') {
			continue;
		}
		$modName=$resultrow['name'];
		if (isPermitted($modName, 'DetailView') == 'yes' && vtlib_isModuleActive($modName)) {
			$modulenamearr[$modName]=array($resultrow['tabid'],$modName);
		}
	}
}
uasort($modulenamearr, function ($a, $b) {
	return (strtolower(getTranslatedString($a[1], $a[1])) < strtolower(getTranslatedString($b[1], $b[1]))) ? -1 : 1;
});

//Security Check done for RSS and Dashboards
$allow_rss='no';
$allow_dashbd='no';
$allow_report='no';
if (isPermitted('Rss', 'DetailView') == 'yes' && vtlib_isModuleActive('Rss')) {
	$allow_rss='yes';
}
if (isPermitted('Dashboard', 'DetailView') == 'yes' && vtlib_isModuleActive('Dashboard')) {
	$allow_dashbd='yes';
}

if (isPermitted('Reports', 'DetailView') == 'yes' && vtlib_isModuleActive('Reports')) {
	$allow_report='yes';
}

$homedetails = $homeObj->getHomePageFrame();
$maxdiv = count($homedetails)-1;
$user_name = $current_user->column_fields['user_name'];
$buttoncheck['Calendar'] = isPermitted('Calendar', 'index');
$freetag = new freetag();
$numberofcols = getNumberOfColumns();

$smarty->assign('CHECK', $buttoncheck);
if (vtlib_isModuleActive('Calendar')) {
	$smarty->assign('CALENDAR_ACTIVE', 'yes');
}
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('MODULE', 'Home');
$smarty->assign('CATEGORY', getParenttab('Home'));
$smarty->assign('CURRENTUSER', $user_name);
$smarty->assign('ALL_TAG', $freetag->get_tag_cloud_html('', $current_user->id));
$smarty->assign('USER_TAG_SHOWAS', getTagCloudShowAs($current_user->id));
$smarty->assign('MAXLEN', $maxdiv);
$smarty->assign('ALLOW_RSS', $allow_rss);
$smarty->assign('ALLOW_DASH', $allow_dashbd);
$smarty->assign('ALLOW_REPORT', $allow_report);
$smarty->assign('HOMEFRAME', $homedetails);
$smarty->assign('MODULE_NAME', $modulenamearr);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('LAYOUT', $numberofcols);
$widgetBlockSize = GlobalVariable::getVariable('HomePage_Widget_Group_Size', 12, 'Home');
$smarty->assign('widgetBlockSize', $widgetBlockSize);

// First time login check
include_once 'modules/Users/LoginHistory.php';
$accept_login_delay_seconds = 5*60; // (use..5*60 for 5 min) to overcome redirection post authentication
$smarty->assign('FIRST_TIME_LOGIN', LoginHistory::firstTimeLoggedIn($current_user->user_name, $accept_login_delay_seconds));

$smarty->display('Home/Homestuff.tpl');
?>
