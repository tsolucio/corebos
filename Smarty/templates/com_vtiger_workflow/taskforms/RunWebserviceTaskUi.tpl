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
		 "value":"IOMap",
		 "columncondition":"or"},
		{"groupid":"1",
		 "columnname":"vtiger_cbmap:maptype:maptype:cbMap_Map_Type:V",
		 "comparator":"e",
		 "value":"Webservice Mapping",
		 "columncondition":""}
	];
	var advSearch = '&query=true&searchtype=advance&advft_criteria='+convertArrayOfJsonObjectsToString(searchConditions);
	var SpecialSearch = encodeURI(advSearch);
        {/literal}
</script>
<script src="modules/{$module->name}/resources/fieldexpressionpopup.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/updatemassivefieldstaskscript.js" type="text/javascript" charset="utf-8"></script>
<div class="slds-form-element">
    <label class="slds-form-element__label" for="form-element-01">{'Select Config Map'|@getTranslatedString}</label>
	<div class="slds-form-element__control">
	<input id="bmapid" name="bmapid" type="hidden" value="{$task->bmapid}">
	<input id="bmapid_display" name="bmapid_display" readonly="" style="border:1px solid #bababa;" type="text" value="{$task->bmapid_display}">&nbsp;
	<img src="{'select.gif'|@vtiger_imageurl:$THEME}" tabindex="1" alt="{'LBL_SELECT'|@getTranslatedString}" title="{'LBL_SELECT'|@getTranslatedString}"
	onclick="return window.open('index.php?module=cbMap&action=Popup&html=Popup_picker&form=new_task&forfield=bmapid&srcmodule=GlobalVariable'+SpecialSearch,'vtlibui10wf','width=1680,height=850,resizable=0,scrollbars=0,top=150,left=200');" style="cursor:hand;cursor:pointer" align="absmiddle">&nbsp;
	<input type="image" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}"
	alt="{'LBL_CLEAR'|@getTranslatedString}" title="{'LBL_CLEAR'|@getTranslatedString}" onClick="this.form.bmapid.value=''; this.form.bmapid_display.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;
	</div>
</div>