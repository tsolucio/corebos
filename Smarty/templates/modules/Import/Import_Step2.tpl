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
		<td class="dvtCellLabel text-left heading2" width=20%>{'LBL_IMPORT_STEP_2'|@getTranslatedString:$MODULE}:</td>
		<td class="dvtCellInfo big" colspan="2" width=80%>{'LBL_IMPORT_STEP_2_DESCRIPTION'|@getTranslatedString:$MODULE}</td>
	</tr>
	<tr id="file_type_container">
		<td>&nbsp;</td>
		<td class="dvtCellLabel" width=25%><span>{'LBL_FILE_TYPE'|@getTranslatedString:$MODULE}</span></td>
		<td class="dvtCellInfo" width=50%>
			<select name="type" id="type" class="slds-select" onchange="ImportJs.handleFileTypeChange();">
				{foreach item=_FILE_TYPE from=$SUPPORTED_FILE_TYPES}
				<option value="{$_FILE_TYPE}">{$_FILE_TYPE|@getTranslatedString:$MODULE}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr id="file_encoding_container">
		<td>&nbsp;</td>
		<td class="dvtCellLabel" width=25%><span>{'LBL_CHARACTER_ENCODING'|@getTranslatedString:$MODULE}</span></td>
		<td class="dvtCellInfo" width=50%>
			<select name="file_encoding" id="file_encoding" class="slds-select">
				{foreach key=_FILE_ENCODING item=_FILE_ENCODING_LABEL from=$SUPPORTED_FILE_ENCODING}
				<option value="{$_FILE_ENCODING}">{$_FILE_ENCODING_LABEL|@getTranslatedString:$MODULE}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr id="delimiter_container">
		<td>&nbsp;</td>
		<td class="dvtCellLabel" width=25%><span>{'LBL_DELIMITER'|@getTranslatedString:$MODULE}</span></td>
		<td class="dvtCellInfo" width=50%>
			<select name="delimiter" id="delimiter" class="slds-select">
				{foreach key=_DELIMITER item=_DELIMITER_LABEL from=$SUPPORTED_DELIMITERS}
				<option value="{$_DELIMITER}">{$_DELIMITER_LABEL|@getTranslatedString:$MODULE}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr id="has_header_container">
		<td>&nbsp;</td>
		<td class="dvtCellLabel" width=25%><span>{'LBL_HAS_HEADER'|@getTranslatedString:$MODULE}</span></td>
		<td class="dvtCellInfo" width=50%>
			<span class="slds-checkbox">
				<input type="checkbox" class="small" id="has_header" name="has_header" checked />
				<label class="slds-checkbox__label" for="has_header">
					<span class="slds-checkbox--faux"></span>
				</label>
			</span>
		</td>
	</tr>
</table>