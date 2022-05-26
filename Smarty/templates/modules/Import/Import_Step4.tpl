{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}
<table width="100%" cellspacing="0" cellpadding="5">
	<tr>
		<td class="heading2" width="10%">
			{'LBL_IMPORT_STEP_4'|@getTranslatedString:$MODULE}:
		</td>
		<td>
			<span class="big">{'LBL_IMPORT_STEP_4_DESCRIPTION'|@getTranslatedString:$MODULE}</span>
		</td>
		<td width="10%">&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="right" class="cblds-t-align_right">
			<div id="savedMapsContainer">
				{include file="modules/Import/Import_Saved_Maps.tpl"}
			</div>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="hidden" name="field_mapping" id="field_mapping" value="" />
			<input type="hidden" name="default_values" id="default_values" value="" />
			<table width="100%" cellspacing="0" cellpadding="5" class="listRow">
				<tr>
					{if $HAS_HEADER eq true}
					<td class="big tableHeading cblds-p-v_mediumsmall" width="25%"><b>{'LBL_FILE_COLUMN_HEADER'|@getTranslatedString:$MODULE}</b></td>
					{/if}
					<td class="big tableHeading cblds-p-v_mediumsmall" width="25%"><b>{'LBL_ROW_1'|@getTranslatedString:$MODULE}</b></td>
					<td class="big tableHeading cblds-p-v_mediumsmall" width="25%"><b>{'LBL_CRM_FIELDS'|@getTranslatedString:$MODULE}</b></td>
					<td class="big tableHeading cblds-p-v_mediumsmall" width="25%"><b>{'LBL_DEFAULT_VALUE'|@getTranslatedString:$MODULE}</b></td>
				</tr>
				{foreach key=_HEADER_NAME item=_FIELD_VALUE from=$ROW_1_DATA name="headerIterator"}
				{assign var="_COUNTER" value=$smarty.foreach.headerIterator.iteration}
				<tr class="fieldIdentifier" id="fieldIdentifier{$_COUNTER}">
					{if $HAS_HEADER eq true}
					<td class="cellLabel cblds-p_large">
						<span name="header_name">{$_HEADER_NAME}</span>
					</td>
					{/if}
					<td class="cellLabel">
						<span>{$_FIELD_VALUE|@textlength_check}</span>
					</td>
					<td class="cellLabel">
						<input type="hidden" name="row_counter" value="{$_COUNTER}" />
						<select name="mapped_fields" class="txtBox" style="width: 100%" onchange="ImportJs.loadDefaultValueWidget('fieldIdentifier{$_COUNTER}')">
							<option value="">{'LBL_NONE'|@getTranslatedString:$FOR_MODULE}</option>
							{foreach key=_FIELD_NAME item=_FIELD_INFO from=$AVAILABLE_FIELDS}
							{assign var="_TRANSLATED_FIELD_LABEL" value=$_FIELD_INFO->getFieldLabelKey()|@getTranslatedString:$FOR_MODULE}
							<option value="{$_FIELD_NAME}" {if $_HEADER_NAME eq $_TRANSLATED_FIELD_LABEL || $_HEADER_NAME eq $_FIELD_NAME} selected {/if} {if $_FIELD_INFO->isMandatory() eq 'true'}style="color:red;"{/if}>{$_TRANSLATED_FIELD_LABEL}{if $_FIELD_INFO->isMandatory() eq 'true'}&nbsp; (*){/if}</option>
							{/foreach}
							{assign var="_TRANSLATED_FIELD_LABEL" value='cbuuid'|@getTranslatedString}
							<option value="cbuuid" {if $_HEADER_NAME eq $_TRANSLATED_FIELD_LABEL || $_HEADER_NAME eq 'cbuuid'} selected {/if}>{$_TRANSLATED_FIELD_LABEL}</option>
						</select>
					</td>
					<td class="cellLabel" name="default_value_container">&nbsp;</td>
				</tr>
				{/foreach}
			</table>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td class="cblds-t-align_right cblds-p-v_mediumsmall">
			<input type="checkbox" name="workflow" id="workflow" class="small" />
			<span class="small">{'LBL_EXECUTE_WF'|@getTranslatedString:$MODULE}</span>&nbsp; : &nbsp;
				<input id="workflowid" name="workflowid" type="hidden" value ="">
				<input type='hidden' class='small' name="workflowid_type" id="workflowid_type" value="com_vtiger_workflow">
				<input
					id="workflowid_display"
					name="workflowid_display"
					readonly
					type="text"
					style="border:1px solid #bababa;"
					onclick='return vtlib_open_popup_window("importAdvanced", "workflowid", "com_vtiger_workflow", "");'
					value ="">&nbsp;
					<span class="slds-icon_container slds-icon-standard-choice" title="{'LBL_SELECT'|getTranslatedString}" onclick='return vtlib_open_popup_window("importAdvanced", "workflowid", "com_vtiger_workflow", "");'>
						<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#choice"></use>
						</svg>
					</span>
					<span class="slds-icon_container slds-icon-standard-choice" title="{'LBL_CLEAR'|getTranslatedString}" onclick="document.getElementById('workflowid').value=''; document.getElementById('workflowid_display').value=''; return false;">
						<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
						</svg>
					</span>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="right" class="cblds-t-align_right cblds-p-v_mediumsmall">
			<input type="checkbox" name="save_map" id="save_map" class="small" />
			<span class="small">{'LBL_SAVE_AS_CUSTOM_MAPPING'|@getTranslatedString:$MODULE}</span>&nbsp; : &nbsp;
			<input type="text" name="save_map_as" id="save_map_as" class="small" />
		</td>
		<td>&nbsp;</td>
	</tr>
</table>
{include file="modules/Import/Import_Default_Values_Widget.tpl"}
