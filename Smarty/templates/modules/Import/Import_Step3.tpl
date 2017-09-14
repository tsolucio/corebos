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
<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout small detailview_table">
	<tr class="slds-line-height--reset">
		<td class="dvtCellLabel text-left heading2" width=20%>
			<input type="checkbox" class="small" id="auto_merge" name="auto_merge" onclick="ImportJs.toogleMergeConfiguration();" />
			{'LBL_IMPORT_STEP_3'|@getTranslatedString:$MODULE}:
		</td>
		<td class="dvtCellInfo big" colspan="2" width=80%>
			<span class="big"><b>{'LBL_IMPORT_STEP_3_DESCRIPTION'|@getTranslatedString:$MODULE}</b></span><br/>
			<span class="small"><i>( {'LBL_IMPORT_STEP_3_DESCRIPTION_DETAILED'|@getTranslatedString:$MODULE} )</i></span>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout small detailview_table" id="duplicates_merge_configuration" style="display:none;"> 
				<tr class="slds-line-height--reset">
					<td class="dvtCellLabel text-left">
						<span class="small">{'LBL_SPECIFY_MERGE_TYPE'|@getTranslatedString:$MODULE}</span>&nbsp;&nbsp;
						<select name="merge_type" id="merge_type" class="slds-select" style="width:35%;">
							{foreach key=_MERGE_TYPE item=_MERGE_TYPE_LABEL from=$AUTO_MERGE_TYPES}
								<option value="{$_MERGE_TYPE}">{$_MERGE_TYPE_LABEL|@getTranslatedString:$MODULE}</option>
							{/foreach}
						</select>
					</td>
				</tr>
				<tr class="slds-line-height--reset">
					<td class="dvtCellLabel text-left small">{'LBL_SELECT_MERGE_FIELDS'|@getTranslatedString:$MODULE}</td>
				</tr>
				<tr class="slds-line-height--reset">
					<td class="dvtCellInfo">
						<table class="calDayHour slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout small detailview_table"> 
							<tr class="slds-line-height--reset">
								<td class="dvtCellLabel text-left" width="40%"><b>{'LBL_AVAILABLE_FIELDS'|@getTranslatedString:$MODULE}</b></td>
								<td width="15%"></td>
								<td class="dvtCellLabel text-left" width="40%"><b>{'LBL_SELECTED_FIELDS'|@getTranslatedString:$MODULE}</b></td>
							</tr>
							<tr class="slds-line-height--reset">
								<td class="dvtCellInfo text-left" width="40%">
									<select id="available_fields" multiple size="10" name="available_fields" class="slds-select" style="height:100px;width:100%">
										{foreach key=_FIELD_NAME item=_FIELD_INFO from=$AVAILABLE_FIELDS}
										<option value="{$_FIELD_NAME}">{$_FIELD_INFO->getFieldLabelKey()|@getTranslatedString:$FOR_MODULE}</option>
										{/foreach}
									</select>
								</td>
								<td width="15%">
									<div align="center">
										<input type="button" name="Button" value="&nbsp;&rsaquo;&rsaquo;&nbsp;" onClick="copySelectedOptions('available_fields', 'selected_merge_fields')" class="slds-button slds-button--small slds-button_success btn-width" /><br /><br />
										<input type="button" name="Button1" value="&nbsp;&lsaquo;&lsaquo;&nbsp;" onClick="removeSelectedOptions('selected_merge_fields')" class="slds-button slds-button--small slds-button--destructive btn-width" />
									</div>
								</td>
								<td class="dvtCellInfo text-left" width="40%">
									<input type="hidden" id="merge_fields" size="10" name="merge_fields" value="" />
									<select id="selected_merge_fields" size="10" name="selected_merge_fields" multiple class="slds-select" style="height:100px;width:100%">
										{foreach key=_FIELD_NAME item=_FIELD_INFO from=$ENTITY_FIELDS}
										<option value="{$_FIELD_NAME}">{$_FIELD_INFO->getFieldLabelKey()|@getTranslatedString:$FOR_MODULE}</option>
										{/foreach}
									</select>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>