<div style="margin:2em;">
<script type="text/javascript" src="include/chart.js/Chart.bundle.js"></script>
<?php
/*+***********************************************************************************
 * Copyright 2012 JPL TSolucio, S.L.  --  This file is a part of coreBOS.
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

global $theme, $currentModule, $mod_strings, $app_strings, $current_user, $current_language;
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
	$smarty->assign('THEME', $theme);
	$smarty->display('SetMenu.tpl');
	$sistoragesize = coreBOS_Settings::getSetting('cbod_storagesize', 0);
	$sistoragesizelimit = coreBOS_Settings::getSetting('cbod_storagesizelimit', $cbodStorageSizeLimit);

	$newsize = isset($_REQUEST['storagenewsize']) ? vtlib_purify($_REQUEST['storagenewsize']) : $sistoragesizelimit;
	if (empty($newsize)) {
		$newsize = $sistoragesizelimit;
	}

	$mode = isset($_REQUEST['mode']) ? trim(vtlib_purify($_REQUEST['mode'])) : '';

	if (!empty($mode) && $mode == trim($app_strings['LBL_SAVE_BUTTON_LABEL'])) {
		if ($newsize >= $sistoragesizelimit) {
			coreBOS_Settings::setSetting('cbod_storagesizelimit', $newsize);
			$sistoragesizelimit = $newsize;
		} else {
			echo '<div id="errorcontainer" style="padding:20px">
				<div id="errormsg" style="color: #f85454; font-weight: bold; padding: 10px; border: 1px solid #FF0000; background: #FFFFFF; -moz-border-radius: 5px; margin-bottom: 10px;">'.
				getTranslatedString('StorageMustIncrement', $currentModule).'</div></div>';
		}
	}
?>
<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
<tr>
	<td width=50 rowspan=2 valign=top>
	<img src="modules/Documents/images/HardDrive4848.png" alt="<?php echo getTranslatedString('STORAGESIZE_CONFIGURATION');?>" width="48" height="48" border=0 title="<?php echo getTranslatedString('STORAGESIZE_CONFIGURATION');?>">
	</td>
	<td class=heading2 valign=bottom>
	<b><a href="index.php?module=Settings&action=index&parenttab=Settings"><?php echo getTranslatedString('LBL_SETTINGS');?></a> > <?php echo getTranslatedString('STORAGESIZE_CONFIGURATION');?> </b>
	</td>
</tr>
<tr>
	<td valign=top class="small"><?php echo getTranslatedString('STORAGESIZE_CONFIGURATION_DESCRIPTION');?></td>
</tr>
</table>
<form name="myform" action="index.php" method="GET">
<input type="hidden" name="module" value="Documents">
<input type="hidden" name="action" value="StorageConfig">
<input type="hidden" name="parenttab" value="Settings">
<input type="hidden" name="formodule" value="Documents">
<input type="hidden" name="mode" value="Save">
<table>
	<tr>
		<td>
		<canvas id="chart-area" width="570" height="285" style="display: block; width: 570px; height: 285px;"></canvas>
		<b><?php echo getTranslatedString('Total', 'Documents').':</b>&nbsp;&nbsp;'.$sistoragesizelimit;?> Gb<br>
		<b><?php echo getTranslatedString('Occupied', 'Documents').':</b>&nbsp;&nbsp;'.$sistoragesize;?> Gb<br>
		<b><?php echo getTranslatedString('Free', 'Documents').':</b>&nbsp;&nbsp;'.($sistoragesizelimit-$sistoragesize);?> Gb<br>
		</td>
<?php if (!empty($coreBOSOnDemandActive)) {?>
		<td valign="bottom">
		<?php echo getTranslatedString('NewSize', 'Documents');?>: <input type="text" name='storagenewsize' id='storagenewsize' style="width:30px;" maxlength=2 value="<?php echo $sistoragesizelimit; ?>"> <b>Gb</b><br>
		<p width=90% align=center><input type="checkbox" id="accept_charge"><span style="font-size: 12px;font-weight: bold;"><?php echo getTranslatedString('accept_charge', 'Documents'); ?></span><br/><input title="<?php echo $app_strings['LBL_SAVE_BUTTON_TITLE']; ?>" accessKey="<?php echo $app_strings['LBL_SAVE_BUTTON_KEY']; ?>" class="crmbutton small save" type="submit" name="button" value="  <?php echo trim($app_strings['LBL_SAVE_BUTTON_LABEL']); ?>  " style="width:70px;" align=center onclick="return jQuery('#accept_charge').is(':checked');"></p>
		<?php include "modules/Documents/language/{$current_language}.showLicense.html";?><br/><br/><br/>
		</td>
<?php } ?>
	</tr>
</table>
</form>
<script type="text/javascript">
	var config = {
		type: 'pie',
		data: {
			datasets: [{
				data: [
					<?php echo $sistoragesize;?>,
					<?php echo $sistoragesizelimit-$sistoragesize;?>
				],
				backgroundColor: [
					'#A52A2A',
					'#228B22'
				],
				label: '<?php echo getTranslatedString('Total', 'Documents'); ?>'
			}],
			labels: [
				"<?php echo getTranslatedString('Occupied', 'Documents'); ?>",
				"<?php echo getTranslatedString('Free', 'Documents'); ?>"
			]
		},
		options: {
			responsive: true
		}
	};
	var ctx = document.getElementById("chart-area").getContext("2d");
	var storagegrph = new Chart(ctx, config);
</script>
<?php
}
?>
</div>