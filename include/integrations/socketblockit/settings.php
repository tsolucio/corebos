<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module    : Elasticsearch Settings
 *  Version   : 1.0
 *  Author    : AT Consulting
 *************************************************************************************************/
global $current_user, $adb, $root_directory;
include_once 'include/Webservices/Create.php';
include_once "vtlib/Vtiger/Module.php";

$smarty = new vtigerCRM_Smarty();

$isAppActive = false;

$moduleid = isset($_REQUEST['module_list']) ? vtlib_purify($_REQUEST['module_list']) : '';
$sockethost = isset($_REQUEST['sockethost']) ? vtlib_purify($_REQUEST['sockethost']) : '';
$socketblockedit = ((empty($_REQUEST['blockit']) || $_REQUEST['blockit']!='on') ? '0' : '1');
//check for business action
$ba = $adb->pquery('select businessactionsid,module_list from vtiger_businessactions join vtiger_crmentity on crmid=businessactionsid where deleted=0 and active=? and linklabel=?', array('1', 'socket.io'));
$bacount = $adb->num_rows($ba);

if (!empty($moduleid) && $_REQUEST['_op']=='setconfigsocketblockit') {
	$isFormActive = ((empty($_REQUEST['rvactive']) || $_REQUEST['rvactive']!='on') ? '0' : '1');

	//check for global variable sockethost
	$recexists = $adb->pquery("select globalvariableid,module_list
            from vtiger_globalvariable
            inner join vtiger_crmentity on crmid=globalvariableid
            where deleted=0 and gvname=? and smownerid=?", array('sockethost',$current_user->id));
	$count = $adb->num_rows($recexists);
	$module_list = explode(' |##| ', $adb->query_result($recexists, 0, 1));
	$gvid = ($count>0 ? $adb->query_result($recexists, 0, 0) : '');

	//check for global variable socketblockedit
	$recexists2 = $adb->pquery("select globalvariableid,module_list
            from vtiger_globalvariable
            inner join vtiger_crmentity on crmid=globalvariableid
            where deleted=0 and gvname=? and smownerid=?", array('socketblockedit',$current_user->id));
	$count2 = $adb->num_rows($recexists2);
	$module_list2 = explode(' |##| ', $adb->query_result($recexists2, 0, 1));
	$gvid2 = ($count2>0 ? $adb->query_result($recexists2, 0, 0) : '');

	if ($isFormActive=='1') {
		if ($count > 0 && !in_array($moduleid, $module_list)) {
			$adb->pquery("update vtiger_globalvariable set module_list=CONCAT(module_list,' |##| $moduleid') where globalvariableid=?", array($gvid));
		} elseif ($count == 0) {
			vtws_create('GlobalVariable', array(
				'gvname' => 'sockethost',
				'default_check' => '0',
				'value' => "$sockethost",
				'mandatory' => '0',
				'blocked' => '0',
				'module_list' => "$moduleid",
				'category' => 'System',
				'in_module_list' => '1',
				'assigned_user_id' => vtws_getEntityId('Users').'x'.$current_user->id,
			), $current_user);
		}

		if ($count2 > 0 && !in_array($moduleid, $module_list2)) {
			$adb->pquery("update vtiger_globalvariable set module_list=CONCAT(module_list,' |##| $moduleid') where globalvariableid=?", array($gvid2));
		} elseif ($count2 == 0) {
			vtws_create('GlobalVariable', array(
				'gvname' => 'socketblockedit',
				'default_check' => '0',
				'value' => "$socketblockedit",
				'mandatory' => '0',
				'blocked' => '0',
				'module_list' => "$moduleid",
				'category' => 'System',
				'in_module_list' => '1',
				'assigned_user_id' => vtws_getEntityId('Users').'x'.$current_user->id,
			), $current_user);
		}

		$isAppActive = true;

		if ($ba && $bacount>0) {
			$bactionid = $adb->query_result($ba, 0, 0);
			$module_list3 = explode(' |##| ', $adb->query_result($ba, 0, 1));
			if (!in_array($moduleid, $module_list3)) {
				$adb->pquery("update vtiger_businessactions set module_list=CONCAT(module_list,' |##| $moduleid') where businessactionsid=?", array($bactionid));
			}
		} else {
			$bact = vtws_create(
				'BusinessActions',
				array(
					'linklabel' => 'socket.io',
					'active' => '1',
					'linktype' => 'HEADERSCRIPT',
					'linkurl' => 'socketlockit/socket.io.js',
					'mandatory' => '1',
					'onlyonmymodule' => '1',
					'module_list' => $moduleid,
					'assigned_user_id' => vtws_getEntityId('Users').'x'.$current_user->id,
				),
				$current_user
			);
		}
	} else {
		$index = array_search($moduleid, $module_list);
		unset($module_list[$index]);
		if (count($module_list)>0) {
			$module_del = implode(" |##| ", $module_list);
		} else {
			$module_del = "";
		}

		if ($bacount>0) {
			$bactionid = $adb->query_result($ba, 0, 0);
		}
		$adb->pquery("update vtiger_globalvariable set module_list='$module_del' where globalvariableid=?", array($gvid));
		$adb->pquery("update vtiger_globalvariable set module_list='$module_del' where globalvariableid=?", array($gvid2));
		$adb->pquery("update vtiger_businessactions set module_list='$module_del' where businessactionsid=?", array($bactionid));

		$isAppActive = false;
		$sockethost = "";
		$socketblockedit = "";
	}
} else {
	$sockethost = GlobalVariable::getVariable("sockethost", "", $moduleid);
	$socketblockedit = GlobalVariable::getVariable("socketblockedit", "", $moduleid);
	if ($sockethost!="") {
		$isAppActive = 1;
	}
}
$entitymodules = getAllowedPicklistModules(0);
$opt = '';
foreach ($entitymodules as $module) {
	if ($moduleid == $module) {
		$selected='selected';
	} else {
		$selected = '';
	}
	$opt.="<option value='$module' $selected>".getTranslatedString($module, $module)."</option>";
}


$smarty->assign('isActive', $isAppActive);
$smarty->assign('sockethost', $sockethost);
$smarty->assign('blockit', $socketblockedit);
$smarty->assign('TITLE_MESSAGE', 'Socket Block It');
$smarty->assign('MODULELIST', $opt);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('ERROR', $error);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
include 'modules/cbupdater/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('ISADMIN', is_admin($current_user));
$smarty->display('modules/Utilities/socketblockit.tpl');
?>
