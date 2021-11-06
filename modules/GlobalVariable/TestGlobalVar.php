<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module       : Global Variable Tester
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'include/Webservices/getmaxloadsize.php';
require_once 'include/utils/CommonUtils.php';

function gv_getGVVarNames() {
	global $current_user, $adb, $default_charset;
	require_once 'modules/PickList/PickListUtils.php';
	$roleid=$current_user->roleid;
	$picklistValues = getAssignedPicklistValues('gvname', $roleid, $adb);
	if (!empty($picklistValues)) {
		foreach ($picklistValues as $pickListValue) {
			$options[$pickListValue] = getTranslatedString($pickListValue, 'GlobalVariable');
		}
	}
	asort($options);
	if (empty($_REQUEST['gvname'])) {
		$selected = '--none--';
	} else {
		$selected = htmlspecialchars(vtlib_purify($_REQUEST['gvname']), ENT_QUOTES, $default_charset);
	}
	$options = get_select_options_with_id($options, $selected);
	return $options;
}
$phpmaxsize = get_maxloadsize();
$phpmaxsizeMB = readableBytes($phpmaxsize, 'MB');
$warning = '<b>'.getTranslatedString('VTLIB_LBL_WARNING', 'Settings').':</b> '.getTranslatedString('PHP_MAX_UPLOAD', 'GlobalVariable');
?>
<script type="text/javascript">
	function gvSearchVariableValue() {
		var vlist = jQuery('#vlist').val();
		var ulist = jQuery('#ulist').val();
		var mlist = jQuery('#mlist').val();
		jQuery.ajax({
			url: 'index.php?action=GlobalVariableAjax&file=SearchGlobalVar&module=GlobalVariable&gvname='+vlist+'&gvuserid='+ulist+'&gvmodule='+mlist+'&gvdefault=default&returnvalidation=1',
			context: document.body
		}).done(function (response) {
			obj = JSON.parse(response);
			var out = '';
			jQuery.each(obj.validation, function (i, val) {
				out = out + val + '<br>';
			});
			if (vlist=='Application_Upload_MaxSize') {
				out = out + '<?php echo $warning; ?>: <b><?php echo $phpmaxsizeMB.' ('.$phpmaxsize.')'; ?></b><br><br>';
			}
			out = out + 'Time spent: ' + obj.timespent + ' msec<br>';
			jQuery('#gvtestresults').html(out);
		});
	}
</script>
<style type="text/css">
.gvtestlabeltext {
	font-size: medium;
	font-weight: bold;
	padding-left:10px;
	padding-right:20px;
}
#gvtestresults {
	width: 96%;
	margin: auto;
	font-size: medium;
}
</style>
<div id="page-header-placeholder"></div>
<div id="page-header" class="slds-page-header slds-m-vertical_medium">
	<div class="slds-page-header__row">
		<div class="slds-page-header__col-title">
			<div class="slds-media">
				<div class="slds-media__figure">
					<a class="hdrLink" href="index.php?action=index&module=GlobalVariable">
						<span class="slds-icon_container slds-icon-standard-system-and-global-variable" title="<?php echo getTranslatedString('GlobalVariable', 'GlobalVariable'); ?>">
							<svg class="slds-icon slds-page-header__icon" id="page-header-icon" aria-hidden="true">
								<use xmlns:xlink="http://www.w3.org/1999/xlink"
									xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#system_and_global_variable" />
							</svg>
							<span class="slds-assistive-text"><?php echo getTranslatedString('GlobalVariable', 'GlobalVariable'); ?></span>
						</span>
					</a>
				</div>
				<div class="slds-media__body">
					<div class="slds-page-header__name">
						<div class="slds-page-header__name-title">
							<h1>
								<span><?php echo getTranslatedString('GlobalVariable', 'GlobalVariable'); ?></span>
								<span class="slds-page-header__title slds-truncate" title="<?php echo getTranslatedString('GlobalVariable', 'GlobalVariable'); ?>">
									<a class="hdrLink" href="index.php?action=index&module=GlobalVariable"><?php echo getTranslatedString('GlobalVariable', 'GlobalVariable'); ?></a>
								</span>
							</h1>
							<p class="slds-page-header__row slds-page-header__name-meta">
							<?php echo getTranslatedString('GlobalVariable', 'GlobalVariable').'&nbsp;-&nbsp;'.getTranslatedString('Test', 'GlobalVariable');?>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="slds-page-header__col-actions">
		</div>
		<div id="page-header-surplus">
		</div>
	</div>
</div>
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
<div id="view" class="slds-modal__container slds-p-around_none slds-card">
<div class="slds-form-element">
	<label class="slds-form-element__label gvtestlabeltext" for="vlist"><?php echo getTranslatedString('SINGLE_GlobalVariable', 'GlobalVariable');?></label>
	<div class="slds-form-element__control">
		<select name="vlist" id="vlist" class='slds-select slds-m-left_large slds-page-header__meta-text' style="width:40%;"><?php echo gv_getGVVarNames();?></select>
	</div>
</div>
<div class="slds-form-element">
	<label class="slds-form-element__label gvtestlabeltext" for="ulist"><?php echo getTranslatedString('User', 'GlobalVariable');?></label>
	<div class="slds-form-element__control">
		<select name="ulist" id="ulist" class='slds-select slds-m-left_large slds-page-header__meta-text' style="width:40%;"><?php echo getUserslist();?></select>
	</div>
</div>
<div class="slds-form-element">
	<label class="slds-form-element__label gvtestlabeltext" for="mlist"><?php echo getTranslatedString('Module', 'GlobalVariable');?></label>
	<div class="slds-form-element__control">
		<select name="mlist" id="mlist" class='slds-select slds-m-left_large slds-page-header__meta-text' style="width:40%;">
<?php
$mlist = getAllowedPicklistModules(1);
$modlist = array();
foreach ($mlist as $mod) {
	$modlist[$mod] = getTranslatedString($mod, $mod);
}
asort($modlist);
echo get_select_options_with_id($modlist, '');
?>
		</select>
	</div>
</div>
<div class="slds-form-element slds-m-around_large">
	<button class="slds-button slds-button_neutral" type="button" onclick="javascript:gvSearchVariableValue();">
		<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
		</svg>
		<?php echo getTranslatedString('Search Value', 'GlobalVariable');?>
	</button>
</div>
<div name="gvtestresults" id="gvtestresults"></div>
</div>
</section>
