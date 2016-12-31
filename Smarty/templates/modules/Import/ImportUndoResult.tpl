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
<script type="text/javascript" src="modules/Import/resources/Import.js"></script>

<input type="hidden" name="module" value="{$FOR_MODULE}" />
<table style="width:70%;margin-left:auto;margin-right:auto;margin-top:10px;" cellpadding="10" cellspacing="10" class="searchUIBasic">
	<tr>
		<td class="heading2" align="left" colspan="2">
			{'LBL_IMPORT'|@getTranslatedString:$MODULE} {$FOR_MODULE|@getTranslatedString:$FOR_MODULE} - {'LBL_UNDO_RESULT'|@getTranslatedString:$MODULE}
		</td>
	</tr>
	{if !empty($ERROR_MESSAGE)}
	<tr>
		<td class="style1" align="left" colspan="2">
			{$ERROR_MESSAGE}
		</td>
	</tr>
	{/if}
	<tr>
		<td colspan="2" valign="top">
			<table cellpadding="10" cellspacing="0" align="center" class="dvtSelectedCell thickBorder">
				<tr>
					<td>{'LBL_TOTAL_RECORDS'|@getTranslatedString:$MODULE}</td>
					<td width="10%">:</td>
					<td width="10%">{$TOTAL_RECORDS}</td>
				</tr>
				<tr>
					<td>{'LBL_NUMBER_OF_RECORDS_DELETED'|@getTranslatedString:$MODULE}</td>
					<td width="10%">:</td>
					<td width="10%">{$DELETED_RECORDS_COUNT}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="right" colspan="2">
		{include file='modules/Import/Import_Done_Buttons.tpl'}
		</td>
	</tr>
</table>