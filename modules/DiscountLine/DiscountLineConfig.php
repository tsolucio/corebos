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

	# Save Action
	if (!empty($mode) && $mode == 'Save') {
		coreBOS_Settings::delSetting('KEY_MODULE_STATUS');
		coreBOS_Settings::setSetting('KEY_MODULE_STATUS', $_POST['module_status']);
	}
	#Query Information to Show
	$module_status = coreBOS_Settings::getSetting('KEY_MODULE_STATUS','');
?>
	<div style="margin:2em;">
<?php $smarty->display('SetMenu.tpl'); ?>
	<h2><?php echo getTranslatedString('SERVER_CONFIGURATION');?></h2>
	<form name="myform" role='form' action="index.php" method="POST">
		<input type="hidden" name="module" value="DiscountLine">
		<input type="hidden" name="action" value="DiscountLineConfig">
		<input type="hidden" name="parenttab" value="Settings">
		<input type="hidden" name="formodule" value="DiscountLine">
		<input type="hidden" name="mode" value="Save">
		<div class="slds-form-element slds-m-top--small">
			<label class="slds-checkbox--toggle slds-grid">
			<span class="slds-form-element__label slds-m-bottom--none">{'LBL_CHANGE_MODULE_STATUS'|@getTranslatedString:$MODULE}</span>
			<input type="checkbox" name="module_status" aria-describedby="toggle-module-status" {if $module_status}checked{/if} />
			<span id="toggle-module-status" class="slds-checkbox--faux_container" aria-live="assertive">
				<span class="slds-checkbox--faux"></span>
				<span class="slds-checkbox--on">{'LBL_ACTIVATE_MODULE'|@getTranslatedString:$MODULE}</span>
				<span class="slds-checkbox--off">{'LBL_DEACTIVATE_MODULE'|@getTranslatedString:$MODULE}</span>
			</span>
			</label>
		</div>
		<div class="slds-m-top--large">
			<button type="submit" value="Save" class="slds-button slds-button--brand">{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString:$MODULE}</button>
		</div>
	</form>
<?php
}
?>