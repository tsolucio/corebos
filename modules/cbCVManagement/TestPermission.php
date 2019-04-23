<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module       : CV Permission Tester
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

function cv_getCVNames() {
	require_once 'modules/PickList/PickListUtils.php';
	$picklistValues = getPicklistValuesSpecialUitypes('1616', '', '');
	$options = array('-1' => 'Get Default View for Module');
	foreach ($picklistValues as $pickListValue) {
		$options[$pickListValue[1]] = $pickListValue[2];
	}
	$options = get_select_options_with_id($options, '-1');
	return $options;
}
?>
<script type="text/javascript">
function gvSearchVariableValue() {
	var vlist = jQuery('#vlist').val();
	var ulist = jQuery('#ulist').val();
	var mlist = jQuery('#mlist').val();
	jQuery.ajax({
		url: "index.php?action=cbCVManagementAjax&file=SearchPermission&module=cbCVManagement&cvid="+vlist+"&cvuserid="+ulist+"&cvmodule="+mlist+"&returnvalidation=1",
		context: document.body
	}).done(function (response) {
		obj = JSON.parse(response);
		var out = '';
		jQuery.each(obj.validation, function (i, val) {
			out = out + val + '<br>';
		});
		out = out + 'Time spent: ' + obj.timespent + ' msec<br>';
		jQuery("#gvtestresults").html(out);
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
.gvmodulename {
	padding-left:36px;
	padding-right:50px;
	height:32px;
	background: url(modules/cbCVManagement/cbCVManagement.png) left center no-repeat;
}
#gvtestresults {
	width: 96%;
	margin: auto;
	font-size: medium;
}
</style>
<table width="98%" align="center" border="0" cellspacing="0" cellpadding="0" class="small">
<tbody><tr><td style="height:2px"></td></tr>
<tr>
	<td nowrap="" class="moduleName gvmodulename">
	<a href="index.php?module=cbCVManagement&action=index">
	<?php echo getTranslatedString('cbCVManagement', 'cbCVManagement').'&nbsp;-&nbsp;'.getTranslatedString('Test', 'cbCVManagement');?>
	</a>
	</td>
</tr>
<tr><td style="height:2px"></td></tr>
</tbody></table>
<table width="560px" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td class='gvtestlabeltext'><?php echo getTranslatedString('SINGLE_cbCVManagement', 'cbCVManagement');?></td>
	<td><select name="vlist" id="vlist" style='width: 250px;'><?php echo cv_getCVNames();?></select></td>
</tr>
<tr>
	<td class='gvtestlabeltext'><?php echo getTranslatedString('User', 'cbCVManagement');?></td>
	<td><select name="ulist" id="ulist" style='width: 250px;'><?php echo getUserslist();?></select></td>
</tr>
<tr>
	<td class='gvtestlabeltext'><?php echo getTranslatedString('Module', 'cbCVManagement');?></td>
	<td><select name="mlist" id="mlist" style='width: 250px;'>
<?php
$mlist = getAllowedPicklistModules(1);
$modlist = array();
foreach ($mlist as $mod) {
	$modlist[$mod] = getTranslatedString($mod, $mod);
}
asort($modlist);
echo get_select_options_with_id($modlist, '');
?></select></td>
</tr>
<tr><td style="height:6px"></td></tr>
<tr>
	<td colspan="2" align="center"><button onclick="javascript:gvSearchVariableValue();"><?php echo getTranslatedString('Search Value', 'cbCVManagement');?></button></td>
</tr>
<tr><td style="height:6px"></td></tr>
</table>
<div name="gvtestresults" id="gvtestresults"></div>
