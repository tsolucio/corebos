{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ('License'); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{strip}
{include file='QuickCreateHidden.tpl'}
<table border="0" cellspacing="0" cellpadding="0" width="90%" class="mailClient mailClientBg">
<tr>
<td>
	<table border="0" cellspacing="0" cellpadding="0" width="100%" class="small">
	<tr>
		<td class="mailSubHeader">
			<b style="font-size:16px">{'LBL_ADD'|getTranslatedString}&nbsp;{$QCMODULE}</b>
        </td>
        <td class="mailSubHeader" align="right">
			<img src="{'close.gif'|vtiger_imageurl:$THEME}" class="mm_clickable" border=0 onclick="MailManager.mail_associate_create_cancel();">
		</td>
	</tr>
	</table>
	<table border="0" cellspacing="0" cellpadding="5" width="100%" class="small" bgcolor="white" >
	{assign var="fromlink" value="qcreate"}
	{foreach item=subdata from=$QUICKCREATE}
		<tr>
			{foreach key=mainlabel item=maindata from=$subdata}
				{include file='EditViewUI.tpl'}
			{/foreach}
		</tr>
	{/foreach}
	</table>
	<table border="0" cellspacing="0" cellpadding="5" width="100%" class=qcTransport>
		<tr>
			<td align=right>
			{if $MODULE eq 'Accounts'}
				<input title="{'LBL_SAVE_LABEL'|getTranslatedString}" accessKey="{'LBL_SAVE_LABEL'|getTranslatedString}" class="crmbutton small save" 
					   onclick="if(getFormValidate()) MailManager.AjaxDuplicateValidate('Accounts', 'accountname', this.form).done(function(form) {ldelim} MailManager.mail_associate_create(form);{rdelim} );" type="button" name="button" value="{'LBL_SAVE_LABEL'|getTranslatedString}">
			{else}
				<input type="button" class="crmbutton small save" value="{'LBL_SAVE_LABEL'|getTranslatedString}" onclick="if(getFormValidate()) MailManager.mail_associate_create(this.form);">
			{/if}
			</td>
			<td><input type="button" class="crmbutton small cancel" value="{'LBL_Cancel'|getTranslatedString}" onclick="MailManager.mail_associate_create_cancel();"></td>
		</tr>
	</table>
</td>
</tr>
</table>
{if $MODULE eq 'Calendar'}
<script id="qcvalidate">
	var qcfieldname = new Array('subject','date_start','time_start','taskstatus');
        var qcfieldlabel = new Array('Subject','Start Date & Time','Start Date & Time','Status');
        var qcfielddatatype = new Array('V~M','DT~M~time_start','T~O','V~O');
</script>
{else}
<script id="qcvalidate">
        var qcfieldname = new Array({$VALIDATION_DATA_FIELDNAME});
        var qcfieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
        var qcfielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
</script>
{/if}
	<input type="hidden" class="small" name="_folder" value="{$FOLDER}">
	<input type="hidden" class="small" name="_msgno" value="{$MSGNO}">
	<input type="hidden" class="small" name="_mlinktotype" value="{$MODULE}">
	<input type="hidden" class="small" name="_mlinkto" value="{$PARENT}">
</form>
{/strip}