<?php
/************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L.  --  This file is a part of CobroPago vtiger CRM Extension.
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
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('APP', $app_strings);
$smarty->assign('CMOD', $mod_strings);
$smarty->assign('MODULE_LBL', getTranslatedString($currentModule, $currentModule));
$smarty->assign('MODULE', $currentModule);
// Operation to be restricted for non-admin users.
if (!is_admin($current_user)) {
	$smarty->display(vtlib_getModuleTemplate('Vtiger', 'OperationNotPermitted.tpl'));
} else {
	$mode = isset($_POST['mode']) ? vtlib_purify($_POST['mode']) : '';
	// Save Action
	if (!empty($mode) && $mode == 'Save') {
		if (empty($_POST['module_status'])) {
			$module_status ='off';
		} else {
			$module_status = vtlib_purify($_POST['module_status']);
		}
		coreBOS_Settings::delSetting('KEY_DISCOUNT_MODULE_STATUS');
		coreBOS_Settings::setSetting('KEY_DISCOUNT_MODULE_STATUS', $module_status);
	}
	$module_status = coreBOS_Settings::getSetting('KEY_DISCOUNT_MODULE_STATUS', 'off');
	?>
	<div style="margin:2em;">
	<?php $smarty->display('SetMenu.tpl'); ?>
	<div class="slds-card slds-p-around_small slds-m-around_medium">
	<legend class="slds-form-element__legend slds-form-element__label"><?php echo getTranslatedString('MODULE_CONFIGURATION', $currentModule);?></legend>
	<form name="myform" role='form' action="index.php" method="POST">
		<input type="hidden" name="module" value="DiscountLine">
		<input type="hidden" name="action" value="DiscountLineConfig">
		<input type="hidden" name="parenttab" value="Settings">
		<input type="hidden" name="formodule" value="DiscountLine">
		<input type="hidden" name="mode" value="Save">
		<div class="slds-form-element slds-m-top--small">
			<label class="slds-checkbox--toggle slds-grid">
			<span class="slds-form-element__label slds-m-bottom--none"><?php echo getTranslatedString('LBL_CHANGE_MODULE_STATUS', $currentModule);?></span>
			<input type="checkbox" name="module_status" aria-describedby="toggle-module-status" <?php echo ($module_status=='on' ? 'checked' : '');?>/>
			<span id="toggle-module-status" class="slds-checkbox--faux_container" aria-live="assertive">
				<span class="slds-checkbox--faux"></span>
				<span class="slds-checkbox--on"><?php echo getTranslatedString('LBL_ACTIVE', 'Settings');?></span>
				<span class="slds-checkbox--off"><?php echo getTranslatedString('LBL_INACTIVE', 'Settings');?></span>
			</span>
			</label>
		</div>
		<div class="slds-m-top--large">
			<button type="submit" value="Save" class="slds-button slds-button--brand"><?php echo getTranslatedString('LBL_SAVE_BUTTON_LABEL', $currentModule);?></button>
		</div>
	</form>
	</div>
	<?php
}
?>