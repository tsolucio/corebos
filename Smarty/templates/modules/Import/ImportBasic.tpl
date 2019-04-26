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

<form onsubmit="VtigerJS_DialogBox.block();" action="index.php" enctype="multipart/form-data" method="POST" name="importBasic">
	<input type="hidden" name="module" value="{$FOR_MODULE}" />
	<input type="hidden" name="action" value="Import" />
	<input type="hidden" name="mode" value="upload_and_parse" />
	<table style="width:80%;margin-left:auto;margin-right:auto;margin-top:10px;" cellpadding="5" cellspacing="12" class="searchUIBasic" id="import-table">
		<tr>
			<td class="heading2 cblds-p_x-large" align="left" colspan="2">
				{'LBL_IMPORT'|@getTranslatedString:$MODULE} {$FOR_MODULE|@getTranslatedString:$FOR_MODULE}
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
			<td class="leftFormBorder1 cblds-m_x-large" width="60%" valign="top">
			{include file='modules/Import/Import_Step1.tpl'}
			</td>
			<td class="leftFormBorder1 cblds-m_x-large" width="40%" valign="top">
			{include file='modules/Import/Import_Step2.tpl'}
			</td>
		</tr>
		<tr>
			<td class="leftFormBorder1 cblds-m_x-large" colspan="2" valign="top">
			{include file='modules/Import/Import_Step3.tpl'}
			</td>
		</tr>
		<tr>
			<td align="right" colspan="2" class="cblds-t-align_right cblds-m_x-large">
			{include file='modules/Import/Import_Basic_Buttons.tpl'}
			</td>
		</tr>
	</table>
</form>