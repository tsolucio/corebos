{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/
-->*}

<table width="100%" cellspacing="0" cellpadding="5">
	<tr>
		<td class="heading2">{'LBL_IMPORT_STEP_2'|@getTranslatedString:$MODULE}:</td>
		<td class="big">{'LBL_IMPORT_STEP_2_DESCRIPTION'|@getTranslatedString:$MODULE}</td>
		<td>&nbsp;</td>
	</tr>
	<tr id="file_type_container">
		<td>&nbsp;</td>
		<td><span>{'LBL_FILE_TYPE'|@getTranslatedString:$MODULE}</span></td>
		<td>
			<select name="type" id="type" class="small" onchange="ImportJs.handleFileTypeChange();">
				{foreach item=_FILE_TYPE from=$SUPPORTED_FILE_TYPES}
				<option value="{$_FILE_TYPE}">{$_FILE_TYPE|@getTranslatedString:$MODULE}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr id="file_encoding_container">
		<td>&nbsp;</td>
		<td><span>{'LBL_CHARACTER_ENCODING'|@getTranslatedString:$MODULE}</span></td>
		<td>
			<select name="file_encoding" id="file_encoding" class="small">
				{foreach key=_FILE_ENCODING item=_FILE_ENCODING_LABEL from=$SUPPORTED_FILE_ENCODING}
				<option value="{$_FILE_ENCODING}">{$_FILE_ENCODING_LABEL|@getTranslatedString:$MODULE}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr id="delimiter_container">
		<td>&nbsp;</td>
		<td><span>{'LBL_DELIMITER'|@getTranslatedString:$MODULE}</span></td>
		<td>
			<select name="delimiter" id="delimiter" class="small">
				{foreach key=_DELIMITER item=_DELIMITER_LABEL from=$SUPPORTED_DELIMITERS}
				<option value="{$_DELIMITER}">{$_DELIMITER_LABEL|@getTranslatedString:$MODULE}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr id="has_header_container">
		<td>&nbsp;</td>
		<td><span>{'LBL_HAS_HEADER'|@getTranslatedString:$MODULE}</span></td>
		<td><input type="checkbox" class="small" id="has_header" name="has_header" checked /></td>
	</tr>
</table>