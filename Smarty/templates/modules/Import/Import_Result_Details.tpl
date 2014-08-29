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

<table cellpadding="10" cellspacing="0" align="center" class="dvtSelectedCell thickBorder">
	<tr>
		<td>{'LBL_TOTAL_RECORDS_IMPORTED'|@getTranslatedString:$MODULE}</td>
		<td width="10%">:</td>
		<td width="30%">{$IMPORT_RESULT.IMPORTED} / {$IMPORT_RESULT.TOTAL}</td>
	</tr>
	<tr>
		<td colspan="3">
			<table cellpadding="10" cellspacing="0" class="calDayHour">
				<tr>
					<td>{'LBL_NUMBER_OF_RECORDS_CREATED'|@getTranslatedString:$MODULE}</td>
					<td width="10%">:</td>
					<td width="10%">{$IMPORT_RESULT.CREATED}</td>
				</tr>
				<tr>
					<td>{'LBL_NUMBER_OF_RECORDS_UPDATED'|@getTranslatedString:$MODULE}</td>
					<td width="10%">:</td>
					<td width="10%">{$IMPORT_RESULT.UPDATED}</td>
				</tr>
				<tr>
					<td>{'LBL_NUMBER_OF_RECORDS_SKIPPED'|@getTranslatedString:$MODULE}</td>
					<td width="10%">:</td>
					<td width="10%">{$IMPORT_RESULT.SKIPPED}</td>
				</tr>
				<tr>
					<td>{'LBL_NUMBER_OF_RECORDS_MERGED'|@getTranslatedString:$MODULE}</td>
					<td width="10%">:</td>
					<td width="10%">{$IMPORT_RESULT.MERGED}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>{'LBL_TOTAL_RECORDS_FAILED'|@getTranslatedString:$MODULE}</td>
		<td width="10%">:</td>
		<td width="30%">{$IMPORT_RESULT.FAILED} / {$IMPORT_RESULT.TOTAL}</td>
	</tr>
</table>