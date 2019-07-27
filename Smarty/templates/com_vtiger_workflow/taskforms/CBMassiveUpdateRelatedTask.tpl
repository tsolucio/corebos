{*<!--
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}

<script src="modules/{$module->name}/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/parallelexecuter.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
	var moduleName = '{$entityName}';
	{if isset($task->field_value_mapping)}
		{assign var="fvmdoubleslashes" value=$task->field_value_mapping|replace:'\"':'\\\\"'}
		{if empty($fvmdoubleslashes)}
			var fieldvaluemapping = null;
		{else}
			var fieldvaluemapping = JSON.parse('{$fvmdoubleslashes|escape:'quotes'}');
		{/if}
	{else}
		var fieldvaluemapping = null;
	{/if}
{literal}
	var searchConditions = [
		{"groupid":"1",
		 "columnname":"vtiger_cbmap:maptype:maptype:cbMap_Map_Type:V",
		 "comparator":"e",
		 "value":"Condition Expression",
		 "columncondition":"or"},
		{"groupid":"1",
		 "columnname":"vtiger_cbmap:maptype:maptype:cbMap_Map_Type:V",
		 "comparator":"e",
		 "value":"Condition Query",
		 "columncondition":""}
	];
	var advSearch = '&query=true&searchtype=advance&advft_criteria='+convertArrayOfJsonObjectsToString(searchConditions);
	var SpecialSearch = encodeURI(advSearch);
{/literal}
</script>
<script src="modules/{$module->name}/resources/fieldexpressionpopup.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/updatemassivefieldstaskscript.js" type="text/javascript" charset="utf-8"></script>

<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td class="big" nowrap="nowrap">
			<strong>{$MOD.LBL_SET_FIELD_VALUES}</strong>
		</td>
		<td class="small" align="right">
			<span id="workflow_loading" style="display:none">
				<b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0">
			</span>
			<span id="save_fieldvaluemapping_add-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<input type="button" class="crmButton create small" value="{$MOD.LBL_ADD_FIELD}" id="save_fieldvaluemapping_add" style='display: none;'/>
		</td>
	</tr>
</table>
<br>
{include file="com_vtiger_workflow/FieldExpressions.tpl"}
<br>
<input type="hidden" name="field_value_mapping" value="" id="save_fieldvaluemapping_json"/>
<div id="dump" style="display:None;"></div>
<div id="save_fieldvaluemapping"></div>
<div class="slds-grid slds-gutters">
<div class="slds-col slds-size_6-of-12">
<div class="slds-form-element">
	<label class="slds-form-element__label" for="form-element-01">{'Select Condition'|@getTranslatedString}</label>
	<div class="slds-form-element__control">
	<input id="bmapid" name="bmapid" type="hidden" value="{$task->bmapid}">
	<input id="bmapid_display" name="bmapid_display" readonly="" style="border:1px solid #bababa;" type="text" value="{$task->bmapid_display}">&nbsp;
	<img src="{'select.gif'|@vtiger_imageurl:$THEME}" tabindex="1" alt="{'LBL_SELECT'|@getTranslatedString}" title="{'LBL_SELECT'|@getTranslatedString}"
	onclick="return window.open('index.php?module=cbMap&action=Popup&html=Popup_picker&form=new_task&forfield=bmapid&srcmodule=GlobalVariable'+SpecialSearch,'vtlibui10wf','width=680,height=602,resizable=0,scrollbars=0,top=150,left=200');" style="cursor:hand;cursor:pointer" align="absmiddle">&nbsp;
	<input type="image" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}"
	alt="{'LBL_CLEAR'|@getTranslatedString}" title="{'LBL_CLEAR'|@getTranslatedString}" onClick="this.form.bmapid.value=''; this.form.bmapid_display.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;
	</div>
</div>
</div>
<div class="slds-col slds-size_6-of-12">
<div class="slds-form-element">
	<div class="slds-form-element__control slds-m-top_medium">
	<div class="slds-checkbox">
	<input type="checkbox" name="launchrelwf" id="launchrelwf" {if $task->launchrelwf}checked{/if}/>
	<label class="slds-checkbox__label" for="launchrelwf">
	<span class="slds-checkbox_faux"></span>
	<span class="slds-form-element__label">{$MOD.launchrelwf}</span>
	</label>
	</div>
	</div>
</div>
</div>
</div>
