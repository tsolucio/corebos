<?php
/************************************************************************************
 * Copyright 2012 JPL TSolucio, S.L.  --  This file is a part of CobroPago vtiger CRM Extension.
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
 *************************************************************************************/
require_once 'Smarty_setup.php';

global $theme, $currentModule, $mod_strings, $app_strings, $current_user, $adb;
$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';

$smarty = new vtigerCRM_Smarty();
$smarty->assign('MOD', return_module_language($current_language, 'Settings'));
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('APP', $app_strings);
$smarty->assign('CMOD', $mod_strings);
$smarty->assign('MODULE_LBL', $currentModule);
// Operation to be restricted for non-admin users.
if (!is_admin($current_user)) {
	$smarty->display(vtlib_getModuleTemplate('Vtiger', 'OperationNotPermitted.tpl'));
} else {
	$mode = $_REQUEST['mode'];

	if (!empty($mode) && $mode == 'Save') {
		$adb->query('delete from vtiger_cobropagoconfig');
		$adb->pquery('insert into vtiger_cobropagoconfig values(?,?,?,?)', array($_POST['cyp_user'],$_POST['cyp_password'],$_POST['bluepay_mode'],$_POST['block_paid']));
	}
	$results = $adb->query('select * from vtiger_cobropagoconfig');
	$ts_baccid = $adb->query_result($results, 0, 'bluepay_accountid');
	$ts_bskey = $adb->query_result($results, 0, 'bluepay_secretkey');
	$ts_bmode = $adb->query_result($results, 0, 'bluepay_mode');
	$ts_bpaid = $adb->query_result($results, 0, 'block_paid');
?>
	<div style="margin:2em;">
<?php $smarty->display('SetMenu.tpl'); ?>
	<h2><?php echo getTranslatedString('SERVER_CONFIGURATION');?></h2>
	<form name="myform" action="index.php" method="POST">
	<input type="hidden" name="module" value="CobroPago">
	<input type="hidden" name="action" value="CobroPagoConfigServer">
	<input type="hidden" name="parenttab" value="Settings">
	<input type="hidden" name="formodule" value="CobroPago">
	<input type="hidden" name="mode" value="Save">
	<table>
	<!-- 
	<tr>
		<td>
		<b>BLUEPAY AccountId</b>
		</td>
		<td>
		<input type="text" name="cyp_user" value="<?php echo $ts_baccid;?>">
		</td>
	</tr>
	<tr>
		<td>
		<b>BLUEPAY Secret Key</b>
		</td>
		<td>
		<input type="text" name="cyp_password" value="<?php echo $ts_bskey;?>">
		</td>
	</tr>
	<tr>
		<td>
		<b>BLUEPAY Mode</b>
		</td>
		<td>
		<select name="bluepay_mode">
			<option value='LIVE'<?php echo ($ts_bmode=='LIVE' ? ' selected="selected"' : '');?>>LIVE</option>
			<option value='TEST'<?php echo ($ts_bmode=='TEST' ? ' selected="selected"' : '');?>>TEST</option>
		</select>
		</td>
	</tr>
	-->
	<tr>
		<td>
		<b>Prevent edit/delete if paid</b>
		</td>
		<td>
		<input type="checkbox" name="block_paid" <?php echo ($ts_bpaid=='on' ? 'checked' : '');?>>
		</td>
	</tr>
	<tr>
		<td>
		<input type='submit' value='Save'>
		</td> 
	</tr>
	</table>
	</form>
<?php
}
?>