{*
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/ *}
<form action="index.php" method="post" name="form" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="fld_module" value="{$MODULE}">
	<input type="hidden" name="module" value="Settings">
	<input type="hidden" name="parenttab" value="Settings">
	<input type="hidden" name="mode">
	<table  class="listTableTopButtons" border="0" cellpadding="5" cellspacing="0" width="100%">
		<tr>
			<td class="big" align="left"><strong>{$MOD.LBL_MAPPED_FIELDS}</strong> </td>
			<td align="right" class="cblds-t-align_right"><input type="button" class="crmButton create small" onclick="javascript: submitForm('form','index.php?module=Settings&action=LeadCustomFieldMapping');" alt="{$MOD.Edit}" title="{$MOD.Edit}" value="{$MOD.Edit}"/>
			</td>
		</tr>
	</table>
	</form>
	<table class="listTable" border="0" cellpadding="5" cellspacing="0" width="100%">
		<tr>
			<td class="colHeader small cblds-p-v_small" width="5%">#</td>
			<td class="colHeader small cblds-p-v_small" width="20%">{$MOD.FieldLabel}</td>
			<td class="colHeader small cblds-p-v_small" width="20%">{$MOD.FieldType}</td>
			<td colspan="3" class="colHeader small cblds-p-v_small" valign="top"><div align="center">{$MOD.LBL_MAPPING_OTHER_MODULES}</div></td>
			<td class="colHeader small cblds-p-v_small" width="20%">{$MOD.LBL_CURRENCY_TOOL}</td>
		</tr>

		<tr>
			<td class="colHeader small cblds-p-v_small" valign="top" width="5%"></td>
			<td class="colHeader small cblds-p-v_small" valign="top" width="20%"></td>
			<td class="colHeader small cblds-p-v_small" valign="top" width="20%"></td>
			<td class="colHeader small cblds-p-v_small" valign="top" width="18%">{$APP.Accounts}</td>
			<td class="colHeader small cblds-p-v_small" valign="top" width="18%">{$APP.Contacts}</td>
			<td class="colHeader small cblds-p-v_small" valign="top" width="19%">{$APP.Potentials}</td>
			<td class="colHeader small cblds-p-v_small" width="20%"></td>
		</tr>
		{foreach item=entries key=id from=$CFENTRIES}
		<tr>
			{foreach item=value from=$entries.map}
				<td class="listTableRow small" valign="top" nowrap>{$value}&nbsp;</td>
			{/foreach}
				<td class="listTableRow small" valign="top" nowrap>
					{if $entries.editable eq 1}
					<form name="form{$entries.cfmid}" method="post">
						<img border="0"  style="cursor: pointer;" src="{'delete.gif'|@vtiger_imageurl:$THEME}"  alt="{'LBL_DELETE_BUTTON_LABEL'|@getTranslatedString:$MODULE}" title="{'LBL_DELETE_BUTTON_LABEL'|@getTranslatedString:$MODULE}" onclick="javascript: deleteForm('form{$entries.cfmid}','index.php?action=DeleteConvertLeadMapping&module=Settings&parenttab=Settings&cfmid={$entries.cfmid}' );">
					</form>
					{/if}
				</td>
			</tr>
			{/foreach}
</table>
</form>
<br>
<strong>{$APP.LBL_NOTE}: </strong> {$MOD.LBL_CUSTOM_MAPP_INFO}
