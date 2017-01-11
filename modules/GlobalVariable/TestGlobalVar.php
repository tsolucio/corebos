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

function gv_getGVVarNames() {
	global $current_user, $adb;
	require_once 'modules/PickList/PickListUtils.php';
	$roleid=$current_user->roleid;
	$picklistValues = getAssignedPicklistValues('gvname', $roleid, $adb);
	if(!empty($picklistValues)){
		foreach($picklistValues as $order=>$pickListValue){
			$options[$pickListValue] = getTranslatedString($pickListValue,'GlobalVariable');
		}
	}
	asort($options);
	$options = get_select_options_with_id($options, '--none--');
	return $options;
}
?>
<script type="text/javascript">
	function gvSearchVariableValue() {
		var vlist = jQuery('#vlist').val();
		var ulist = jQuery('#ulist').val();
		var mlist = jQuery('#mlist').val();
		jQuery.ajax({
			url: "index.php?action=GlobalVariableAjax&file=SearchGlobalVar&module=GlobalVariable&gvname="+vlist+"&gvuserid="+ulist+"&gvmodule="+mlist+"&gvdefault=default&returnvalidation=1",
			context: document.body
		}).done(function(response) {
			obj = JSON.parse(response);
			var out = '';
			jQuery.each(obj.validation, function(i, val) {
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
#gvtestresults {
	width: 96%;
	margin: auto;
	font-size: medium;
}
</style>
<table width="98%" align="center" border="0" cellspacing="0" cellpadding="0" class="small">
<tbody><tr><td style="height:2px"></td></tr>
<tr>
	<td nowrap="" class="moduleName" style="padding-left:36px;padding-right:50px;height:32px;background: url(modules/GlobalVariable/GlobalVariable.png) left center no-repeat;"><?php echo getTranslatedString('GlobalVariable','GlobalVariable').'&nbsp;-&nbsp;'.getTranslatedString('Test','GlobalVariable');?></td>
</tr>
<tr><td style="height:2px"></td></tr>
</tbody></table>
<table width="560px" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td class='gvtestlabeltext'><?php echo getTranslatedString('SINGLE_GlobalVariable','GlobalVariable');?></td>
	<td><select name="vlist" id="vlist" style='width: 250px;'><?php echo gv_getGVVarNames();?></select></td>
</tr>
<tr>
	<td class='gvtestlabeltext'><?php echo getTranslatedString('User','GlobalVariable');?></td>
	<td><select name="ulist" id="ulist" style='width: 250px;'><?php echo getUserslist();?></select></td>
</tr>
<tr>
	<td class='gvtestlabeltext'><?php echo getTranslatedString('Module','GlobalVariable');?></td>
	<td><select name="mlist" id="mlist" style='width: 250px;'><?php 
	$mlist = getAllowedPicklistModules(1);
	$modlist = array();
	foreach ($mlist as $mod) {
		$modlist[$mod] = getTranslatedString($mod,$mod);
	}
	asort($modlist);
	echo get_select_options_with_id($modlist, '');?></select></td>
</tr>
<tr><td style="height:6px"></td></tr>
<tr>
	<td colspan="2" align="center"><button onclick="javascript:gvSearchVariableValue();"><?php echo getTranslatedString('Search Value','GlobalVariable');?></button></td>
</tr>
<tr><td style="height:6px"></td></tr>
</table>
<div name="gvtestresults" id="gvtestresults"></div>
