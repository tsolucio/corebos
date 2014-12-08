{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{strip}
<form method="POST" action="javascript:void(0);" style="display:inline;" name="relationship">
{if $LINKEDTO}
<table width="100%" cellpadding="2px" cellspacing=0 border=0>
<tr>
	<td width="4px"><input type="radio" name="_mlinkto" value="{$LINKEDTO.record}"></td>
	<td>{$LINKEDTO.detailviewlink}</td>
	<td>{$LINKEDTO.module|getTranslatedString:$moduleName}</td>
</tr>
<tr>
	<td align="center" colspan="3">
		{if $LinkToAvailableActions|count neq 0}
		<select class="small" onchange="MailManager.mail_associate_actions(this.form);" name="_mlinktotype"  id="_mlinktotype">
			<option value="" class="mm_blur">{'LBL_ACTIONS'|getTranslatedString}</option>
			{foreach item=moduleName from=$LinkToAvailableActions}
				<option value="{$moduleName}">{"LBL_MAILMANAGER_ADD_$moduleName"|getTranslatedString:'MailManager'}</option>
			{/foreach}
		</select>
		{/if}
	</td>
</tr>
</table>
{/if}

{if $LOOKUPS}
{assign var="LOOKRECATLEASTONE" value=false}

<table width="100%" cellpadding=3 cellspacing=0 border=0>
{foreach item=RECORDS key=MODULE from=$LOOKUPS}
	{foreach item=RECORD from=$RECORDS}
	{assign var="LOOKRECATLEASTONE" value=true}
	<tr>
		<td>
			<input type="radio" name="_mlinkto" value="{$RECORD.id}" style="align: absmiddle;">
		</td>
		<td align=left>
			<a target="_blank" href="index.php?module={$MODULE}&action=DetailView&record={$RECORD.id}">{$RECORD.label|textlength_check}</a>
		</td>
		<td align=right>
			{assign var=RECORD_MODNAME value=$MODULES.$MODULE.name}
			{assign var="SINGLE_MODLABEL" value="SINGLE_$RECORD_MODNAME"}
			{$SINGLE_MODLABEL|@getTranslatedString:$MODULE}
		</td>
	</tr>
	{/foreach}
{/foreach}

{if $LOOKRECATLEASTONE}
<tr>
	<td colspan="3" align="center">
		{if $LinkToAvailableActions|count neq 0}
		<select class="small" onchange="MailManager.mail_associate_actions(this.form);" name="_mlinktotype"  id="_mlinktotype">
			<option value="" class="mm_blur">{'LBL_ACTIONS'|getTranslatedString}</option>
			{foreach item=moduleName from=$LinkToAvailableActions}
				<option value="{$moduleName}">{"LBL_MAILMANAGER_ADD_$moduleName"|getTranslatedString:'MailManager'}</option>
			{/foreach}
		</select>
		{/if}
	</td>
</tr>
{else}
<tr>
	<td align="center" colspan=3>
		{'LBL_No_Matching_Record_Found'|getTranslatedString}<br/>
		{if $AllowedModules|count neq 0}
		<select class="small" onchange="MailManager.mail_associate_create_wizard(this.form);" name="_mlinktotype"  id="_mlinktotype">
			<option value="" class="mm_blur">{'LBL_ACTIONS'|getTranslatedString}</option>
			{foreach item=moduleName from=$AllowedModules}
				<option value="{$moduleName}">{"LBL_MAILMANAGER_ADD_$moduleName"|getTranslatedString:'MailManager'}</option>
			{/foreach}
		</select>
		{/if}
	</td>
</tr>
{/if}
</table>
{else}
	{if $LINKEDTO eq ""}
	<table width="100%" cellpadding=0 cellspacing=0 border=0>
		<tr>
			<td align="center" colspan=3>
				{'LBL_No_Matching_Record_Found'|getTranslatedString}<br/>
				{if $AllowedModules|count neq 0}
				<select class="small" onchange="MailManager.mail_associate_create_wizard(this.form);" name="_mlinktotype"  id="_mlinktotype">
					<option value="" class="mm_blur">{'LBL_ACTIONS'|getTranslatedString}</option>
					{foreach item=moduleName from=$AllowedModules}
						<option value="{$moduleName}">{"LBL_MAILMANAGER_ADD_$moduleName"|getTranslatedString:'MailManager'}</option>
					{/foreach}
				</select>
				{/if}
			</td>
		</tr>
	</table>
	{/if}
{/if}
<input type="hidden" class="small" name="_folder" value="{$FOLDER}">
<input type="hidden" class="small" name="_msgno" value="{$MSGNO}">
</form>
{/strip}