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
*************************************************************************************************
*  Module       : cbupdater
*  Version      : 5.5.0
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/
global $adb, $log, $mod_strings, $app_strings, $currentModule, $current_user, $theme;
require_once 'modules/cbupdater/cbupdaterHelper.php';

$error = false;
$errmsg = '';

$smarty = new vtigerCRM_Smarty();
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('CUSTOM_MODULE', true);
$smarty->assign('SINGLE_MOD', getTranslatedString('SINGLE_'.$currentModule));
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
include 'modules/cbupdater/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
echo $smarty->fetch('Buttons_List.tpl');

$ids = vtlib_purify($_REQUEST['idstring']);

if (!empty($ids)) {
	echo '<br>';
	require_once 'modules/cbupdater/cbupdater.php';
	require_once 'modules/cbupdater/cbupdaterWorker.php';
	if (isset($_REQUEST['doundo'])) {
		$whattodo = 'undo';
	} else {
		$whattodo = 'apply';
	}
	$sql = 'select cbupdaterid,filename,pathfilename,classname, cbupd_no, description from vtiger_cbupdater
			inner join vtiger_crmentity on crmid=cbupdaterid
			where deleted=0 and ';
	if ($ids=='all') {
		$sql .= "execstate in ('Pending','Continuous')";
	} else {
		$ids = str_replace(';', ',', $ids);
		$ids = trim($ids, ',');
		$sql .= $adb->sql_escape_string(" cbupdaterid in ($ids)");
	}
	$cbacc=$adb->getColumnNames('vtiger_cbupdater');
	if (in_array('blocked', $cbacc)) {
		$sql .= " and blocked != '1' ";
	}
	$rs = $adb->query($sql);
	$totalops = $adb->num_rows($rs);
	$totalopsok = 0;
	while ($upd = $adb->fetch_array($rs)) {
		if (file_exists($upd['pathfilename'])) {
			include $upd['pathfilename'];
			if (class_exists($upd['classname'])) {
				$updobj = new $upd['classname'];
				if (method_exists($updobj, ($whattodo == 'undo' ? 'undoChange' : 'applyChange'))) {
					try {
						$msg = '<b><a href="index.php?module=cbupdater&action=DetailView&record='.$upd['cbupdaterid'].'">';
						$msg.= getTranslatedString('ChangeSet', $currentModule).' '.$upd['cbupd_no'].'</a>:</b> ';
						$msg.= $upd['filename'].'::'.$upd['classname'];
						if (isset($upd['description'])) {
							$msg.= '<br>'.$upd['description'];
						}
						cbupdater_show_message($msg);
						if ($whattodo == 'undo') {
							$updobj->undoChange();
						} else {
							$updobj->applyChange();
						}
						if (!$updobj->updError) {
							$totalopsok++;
						}
					} catch (Exception $e) {
						$error = true;
						$errmsg = $e->getMessage();
						cbupdater_show_error($errmsg);
					}
				} else {
					$error = true;
					$errmsg = getTranslatedString('err_nomethod', $currentModule);
					cbupdater_show_error($errmsg);
				}
			} else {
				$error = true;
				$errmsg = getTranslatedString('err_invalidclass', $currentModule);
				cbupdater_show_error($errmsg);
			}
		} else {
			$error = true;
			$errmsg = getTranslatedString('err_noupdatefile', $currentModule);
			cbupdater_show_error($errmsg. ' : ' . $upd['pathfilename']);
		}
	}
} else {
	$error = true;
	$errmsg = getTranslatedString('err_noupdatesselected', $currentModule);
	cbupdater_show_error($errmsg);
}
cbupdater_dowork_finishExecution($totalops, $totalopsok, ($totalops-$totalopsok));
?>