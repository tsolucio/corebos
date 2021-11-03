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
<script src="modules/{$module->name}/resources/functionselect.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/updatemassivefieldstaskscript.js" type="text/javascript" charset="utf-8"></script>

<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-size_1-of-3 slds-p-around_x-small">
		<div class="slds-form">
			<label class="slds-form-element__label"> {'Select Config Map'|@getTranslatedString} </label>
			<div class="slds-form-element__control slds-input-has-fixed-addon">
				<input id="bmapid" name="bmapid" type="hidden" value="{$task->bmapid}" class="slds-input">
				<input id="bmapid_display" name="bmapid_display" readonly="" style="border:1px solid #bababa;" type="text" value="{$task->bmapid_display}" class="slds-input" onclick="return window.open('index.php?module=cbMap&action=Popup&html=Popup_picker&form=new_task&forfield=bmapid&srcmodule=GlobalVariable'+SpecialSearch, 'vtlibui10wf', cbPopupWindowSettings);" style="cursor:hand;cursor:pointer">
				<span class="slds-form-element__addon" id="fixed-text-addon-post">
					<button type="image" class="slds-button" alt="{'LBL_CLEAR'|@getTranslatedString}" title="{'LBL_CLEAR'|@getTranslatedString}" onClick="this.form.bmapid.value=''; this.form.bmapid_display.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
						<svg class="slds-icon slds-icon_small slds-icon-text-light" aria-hidden="true" >
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
						</svg>
					</button>
				</span>
			</div>
		</div>
	</div>
</div>
