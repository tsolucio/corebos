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

<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td class="big" nowrap="nowrap">
			<strong>{$MOD.LBL_SUMMARY}</strong>
		</td>
		<td class="cblds-t-align_right" align="right">
		</td>
	</tr>
</table>
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr>
		<td class="dvtCellLabel" align=right width=20%><b><span style='color:red;'>*</span> {$APP.LBL_UPD_DESC}</b></td>
		<td class="dvtCellInfo" align="left"><input type="text" class="detailedViewTextBox" name="description" id="save_description" value="{$workflow->description}"{if $workflow->executionConditionAsLabel() eq 'MANUAL'} readonly{/if}></td>
	</tr>
	<tr>
		<td class="dvtCellLabel" align=right width=20%><b>{$APP.LBL_MODULE}</b></td>
		<td class="dvtCellInfo" align="left">{$workflow->moduleName|@getTranslatedString:$workflow->moduleName}</td>
	</tr>
	<tr>
		<td class="dvtCellLabel" align=right width=20%><b>{'LBL_WFPURPOSE'|@getTranslatedString:'com_vtiger_workflow'}</b></td>
		<td class="dvtCellInfo" align="left"><textarea id='purpose' name='purpose'>{$workflow->purpose}</textarea></td>
	</tr>
</table>