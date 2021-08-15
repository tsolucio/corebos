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
	{if isset($task->wfexeexps)}
		{assign var="fvmdoubleslashes" value=$task->wfexeexps|replace:'\"':'\\\\"'}
		{if empty($fvmdoubleslashes)}
			var wfexeexppressions = null;
		{else}
			var wfexeexppressions = JSON.parse('{$fvmdoubleslashes|escape:'quotes'}');
		{/if}
	{else}
		var wfexeexppressions = null;
	{/if}
	var i18nDelete = '{'LBL_DELETE'|@getTranslatedString:'Settings'}';
</script>
<script src="modules/{$module->name}/resources/fieldexpressionpopup.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/functionselect.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/wfexeexp.js" type="text/javascript" charset="utf-8"></script>

<div class="slds-page-header slds-page-header_record-home">
<div class="slds-page-header__row">
<div class="slds-page-header__col-title">
	<h1>
		<span class="slds-page-header__title slds-truncate" title="{'LBL_EXPRESSIONS'|@getTranslatedString:$module->name}">
			<span class="slds-tabs__left-icon">
				<span class="slds-icon_container" title="{'LBL_EXPRESSIONS'|@getTranslatedString:$module->name}">
				<svg class="slds-icon slds-icon_small" style="color:green;" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#formula"></use>
				</svg>
				</span>
			</span>
			{'LBL_EXPRESSIONS'|@getTranslatedString:$module->name}
		</span>
	</h1>
</div>
<div class="slds-page-header__col-actions">
<div class="slds-page-header__controls">
<div class="slds-page-header__control">
<button
	class="slds-button slds-button_neutral slds-button_stateful slds-not-selected"
	aria-live="assertive"
	type="button"
	title="{'LBL_ADD'|@getTranslatedString:'Settings'}"
	onclick="addRowTowfeeTable()"
>
<span class="slds-text-not-selected">
<svg class="slds-button__icon slds-button__icon_small slds-button__icon_left" aria-hidden="true">
<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
</svg>{'LBL_ADD'|@getTranslatedString:'Settings'}</span>
</button>
</div>
</div>
</div>
</div>

<table class="slds-table slds-table_cell-buffer slds-table_bordered slds-table_col-bordered" id="wfexeexptbl">
<thead>
<tr class="slds-line-height_reset">
<th class="" scope="col">
<div class="slds-truncate" title="{'LBL_EXPRESSION'|@getTranslatedString:$module->name}">{'LBL_EXPRESSION'|@getTranslatedString:$module->name}</div>
</th>
<th class="" scope="col">
<div title="{'Variable'|@getTranslatedString:$module->name}">{'Variable'|@getTranslatedString:$module->name}&nbsp;{include file='com_vtiger_workflow/taskforms/wfExecExpressionHelp.tpl'}</div>
</th>
<th class="" scope="col" style="width:6%;">
<div title="{'LBL_DELETE'|@getTranslatedString:$module->name}">{'LBL_DELETE'|@getTranslatedString:$module->name}</div>
</th>
</tr>
</thead>
<tbody>
</tbody>
</table>

<input type="hidden" name="wfexeexps" value="" id="wfexeexps"/>

<div id="dump" style="display:none;"></div>

<template id="wfexprow">
<tr class="slds-hint-parent">
<td scope="row" style="padding-left:0.2rem;">
<div class="slds-form-element slds-truncate slds-page-header__meta-text">
	<div class="slds-form-element__control">
		<input type="text" id="wfeeexp01" class="slds-input" readonly onfocus="wfeeeditFieldExpression($(this), { 'name':'string' })" onchange="setwfexeexppressions()" />
	</div>
</div>
</td>
<td>
<div class="slds-form-element slds-truncate slds-page-header__meta-text">
	<div class="slds-form-element__control">
		<input type="text" class="slds-input" onchange="setwfexeexppressions()" />
	</div>
</div>
</td>
<td>
<input type="hidden" value="" id="wfeeexp01_type"/>
<button
	class="slds-button slds-button_icon slds-button_icon-error"
	title="{'LBL_DELETE'|@getTranslatedString:'Settings'}"
	type="button"
	onclick="document.getElementById('wfexeexptbl').deleteRow(this.closest('tr').rowIndex);setwfexeexppressions()"
>
</button>
</td>
</tr>
</template>