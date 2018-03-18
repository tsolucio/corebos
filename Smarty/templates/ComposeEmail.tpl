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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$LBL_CHARSET}">
<title>{$MOD.TITLE_COMPOSE_MAIL}</title>
<link REL="SHORTCUT ICON" HREF="themes/images/favicon.ico">
<style type="text/css">@import url("themes/{$THEME}/style.css");</style>
<script type="text/javascript" src="include/jquery/jquery.js"></script>
<script type="text/javascript" src="include/js/general.js"></script>
<script type="text/javascript" src="include/js/{$LANGUAGE}.lang.js"></script>
<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="modules/Products/multifile.js"></script>
<script type="text/javascript" src="modules/Emails/Emails.js"></script>
</head>
<body marginheight="0" marginwidth="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
{literal}
<form name="EditView" method="POST" ENCTYPE="multipart/form-data" action="index.php" onSubmit="if(email_validate(this.form,'')) { VtigerJS_DialogBox.block();} else { return false; }">
{/literal}
<input type="hidden" name="send_mail" >
<input type="hidden" name="contact_id" value="{if isset($CONTACT_ID)}{$CONTACT_ID}{/if}">
<input type="hidden" name="user_id" value="{if isset($USER_ID)}{$USER_ID}{/if}">
<input type="hidden" name="filename" value="{$FILENAME}">
<input type="hidden" name="module" value="{$MODULE}">
<input type="hidden" name="record" value="{$ID}">
<input type="hidden" name="mode" value="{if isset($MODE)}{$MODE}{/if}">
<input type="hidden" name="action">
<input type="hidden" name="return_action" value="{if isset($RETURN_ACTION)}{$RETURN_ACTION}{/if}">
<input type="hidden" name="return_module" value="{if isset($RETURN_MODULE)}{$RETURN_MODULE}{/if}">
<input type="hidden" name="popupaction" value="create">
<input type="hidden" name="hidden_toid" id="hidden_toid">
<table class="small mailClient" border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody>
	<tr>
	<td colspan=3 >
	<!-- Email Header -->
	<table border=0 cellspacing=0 cellpadding=0 width=100% class="mailClientWriteEmailHeader">
	<tr>
		<td >{$MOD.LBL_COMPOSE_EMAIL}</td>
	</tr>
	</table>
	</td>
</tr>
	<tr>
	<td class="mailSubHeader" align="right"><font color="red">*</font><b>{$MOD.LBL_FROM}</b></td>
	<td class="cellText" style="padding: 5px;">
		<input name="from_email" id="from_email" class="txtBox" type="text" value="{if isset($FROM_MAIL)}{$FROM_MAIL}{/if}" style="width: 525px;" placeholder="{'LeaveEmptyForUserEmail'|@getTranslatedString:'Settings'}">
	</td>
	<td class="cellText" style="padding: 5px;" align="left" nowrap></td>
	</tr>
{foreach item=row from=$BLOCKS}
{foreach item=elements from=$row}
	{if isset($elements.2) && isset($elements.2.0) && $elements.2.0 eq 'parent_id'}
	<tr>
	<td class="mailSubHeader" align="right"><font color="red">*</font><b>{$MOD.LBL_TO}</b></td>
	<td class="cellText" style="padding: 5px;">
		<input name="{$elements.2.0}" id="{$elements.2.0}" type="hidden" value="{if isset($IDLISTS)}{$IDLISTS}{/if}">
		<input type="hidden" name="saved_toid" value="{if isset($TO_MAIL)}{$TO_MAIL}{/if}">
		<input id="parent_name" name="parent_name" readonly class="txtBox" type="text" value="{if isset($TO_MAIL)}{$TO_MAIL}{/if}" style="width: 525px;">&nbsp;
		<span class="mailClientCSSButton">
			<img src="{'select.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" onclick='return window.open("index.php?module="+ document.EditView.parent_type.value +"&action=Popup&html=Popup_picker&form=HelpDeskEditView&popuptype=set_return_emails","test","width=640,height=602,resizable=0,scrollbars=0,top=150,left=200");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;
		</span>
		<span class="mailClientCSSButton" >
			<img src="{'clear_field.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" onClick="document.getElementById('parent_id').value=''; document.getElementById('hidden_toid').value='';document.getElementById('parent_name').value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
		</span>
	</td>
	<td class="cellText" style="padding: 5px;" align="left" nowrap>
		<select name="parent_type">
			{foreach key=labelval item=selectval from=$elements.1.0}
				{if $selectval eq selected}
					{assign var=selectmodule value="selected"}
				{else}
					{assign var=selectmodule value=""}
				{/if}
				<option value="{$labelval}" {$selectmodule}>{$labelval|@getTranslatedString:$labelval}</option>
			{/foreach}
		</select>
		&nbsp;
	</td>
	</tr>
	<tr>
	{if 'ccmail'|@emails_checkFieldVisiblityPermission:'readwrite' eq '0'}
	<td class="mailSubHeader" style="padding: 5px;" align="right">{$MOD.LBL_CC}</td>
	<td class="cellText" style="padding: 5px;">
		<input name="ccmail" id ="cc_name" class="txtBox" type="text" value="{if isset($CC_MAIL)}{$CC_MAIL}{/if}" style="width:525px">&nbsp;
		<span class="mailClientCSSButton">
			<img src="{'select.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" onclick='return window.open("index.php?module="+ document.EditView.parent_type.value +"&action=Popup&html=Popup_picker&form=HelpDeskEditView&popuptype=set_return_emails&email_field=cc_name","test","width=640,height=602,resizable=0,scrollbars=0,top=150,left=200");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;
		</span>
		<span class="mailClientCSSButton" >
			<img src="{'clear_field.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" onClick="document.getElementById('cc_name').value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
		</span>
	</td>
	{else}
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	{/if}
	<td valign="top" class="cellLabel" rowspan="4"><div id="attach_cont" class="addEventInnerBox" style="overflow:auto;height:100px;width:100%;position:relative;left:0px;top:0px;"></div>
	</tr>
	{if 'bccmail'|@emails_checkFieldVisiblityPermission:'readwrite' eq '0'}
	<tr>
	<td class="mailSubHeader" style="padding: 5px;" align="right">{$MOD.LBL_BCC}</td>
	<td class="cellText" style="padding: 5px;">
		<input name="bccmail" id="bcc_name" class="txtBox" type="text" value="{if isset($BCC_MAIL)}{$BCC_MAIL}{/if}" style="width:525px">&nbsp;
		<span class="mailClientCSSButton">
			<img src="{'select.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" onclick='return window.open("index.php?module="+ document.EditView.parent_type.value +"&action=Popup&html=Popup_picker&form=HelpDeskEditView&popuptype=set_return_emails&email_field=bcc_name","test","width=640,height=602,resizable=0,scrollbars=0,top=150,left=200");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;
		</span>
		<span class="mailClientCSSButton" >
			<img src="{'clear_field.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" onClick="document.getElementById('bcc_name').value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
		</span>
	</td>
	</tr>
	{/if}
	{elseif isset($elements.2) && isset($elements.2.0) && $elements.2.0 eq 'subject'}
	<tr>
	<td class="mailSubHeader" style="padding: 5px;" align="right" nowrap><font color="red">*</font>{$elements.1.0} :</td>
		{if (isset($WEBMAIL) && $WEBMAIL eq 'true') or (isset($RET_ERROR) && $RET_ERROR eq 1)}
			<td class="cellText" style="padding: 5px;"><input type="text" class="txtBox" name="{$elements.2.0}" value="{$SUBJECT}" id="{$elements.2.0}" style="width:99%"></td>
		{else}
			<td class="cellText" style="padding: 5px;"><input type="text" class="txtBox" name="{$elements.2.0}" value="{$elements.3.0}" id="{$elements.2.0}" style="width:99%"></td>
		{/if}
	</tr>
	{elseif isset($elements.2) && isset($elements.2.0) && $elements.2.0 eq 'filename'}
	<tr>
	<td class="mailSubHeader" style="padding: 5px;" align="right" nowrap>{$elements.1.0} :</td>
	<td class="cellText" style="padding: 5px;">
		<!--<input name="{$elements.2.0}" type="file" class="small txtBox" value="" size="78"/>-->
		<input name="del_file_list" type="hidden" value="">
		<div id="files_list" style="border: 1px solid grey; width: 500px; padding: 5px; background: rgb(255, 255, 255) none repeat scroll 0%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; font-size: x-small">{$APP.Files_Maximum}{$EMail_Maximum_Number_Attachments}</span>
			<input id="my_file_element" type="file" name="{$elements.2.0}" tabindex="7" onchange="validateFilename(this)" >
			<input type="hidden" name="{$elements.2.0}_hidden" value="" />
			<span id="limitmsg" style= "color:red; display:'';">{'LBL_MAX_SIZE'|@getTranslatedString:$MODULE} {$UPLOADSIZE}{'LBL_FILESIZEIN_MB'|@getTranslatedString:$MODULE}</span>
		</div>
		<script>
			var multi_selector = new MultiSelector( document.getElementById( 'files_list' ), {$EMail_Maximum_Number_Attachments} );
			multi_selector.count = 0
			multi_selector.addElement( document.getElementById( 'my_file_element' ) );
		</script>
		<div id="attach_temp_cont" style="display:none;">
		<table class="small" width="100% ">
			{if !empty($smarty.request.attachment)}
				<tr>
				<td width="100%" colspan="2">{$smarty.request.attachment|@vtlib_purify}<input type="hidden" value="{$smarty.request.attachment|@vtlib_purify}" name="pdf_attachment"></td>
				</tr>
			{else}
				{foreach item="attach_files" key="attach_id" from=$elements.3}
					<tr id="row_{$attach_id}">
						<td width="90%">{$attach_files}</td>
						<td><img src="{'no.gif'|@vtiger_imageurl:$THEME}" onClick="delAttachments({$attach_id})" alt="{$APP.LBL_DELETE_BUTTON}" title="{$APP.LBL_DELETE_BUTTON}" style="cursor:pointer;"></td>
					</tr>
				{/foreach}
				<input type='hidden' name='att_id_list' value='{$ATT_ID_LIST}' />
			{/if}
			{if isset($WEBMAIL) && $WEBMAIL eq 'true'}
				{foreach item="attach_files" from=$webmail_attachments}
					<tr ><td width="90%">{$attach_files}</td></tr>
				{/foreach}
			{/if}
		</table>
		</div>
		{if isset($elements.3) && isset($elements.3.0)}{$elements.3.0}{/if}
	</td>
	</tr>
	<tr>
	<td colspan="3" class="mailSubHeader" style="padding: 5px;" align="center">
		 <input title="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_TITLE}" accessKey="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_KEY}" class="crmbutton small edit" onclick="window.open('index.php?module=Users&action=lookupemailtemplates','emailtemplate','top=100,left=200,height=400,width=500,resizable=yes,scrollbars=yes,menubar=no,addressbar=no,status=yes')" type="button" name="button" value=" {$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_LABEL} ">
		<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" onclick="return email_validate(this.form,'save');" type="button" name="button" value=" {$APP.LBL_SAVE_BUTTON_LABEL} " >&nbsp;
		<input name="{$MOD.LBL_SEND}" value=" {$APP.LBL_SEND} " class="crmbutton small save" type="button" onclick="return email_validate(this.form,'send');">&nbsp;
		<input value="{$MOD.LBL_ATTACH_DOCUMENTS}" class="crmbutton small edit" type="button" onclick="searchDocuments()">
		<input name="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="crmbutton small cancel" type="button" onClick="window.close()">
	</td>
	</tr>
	{elseif isset($elements.2) && isset($elements.2.0) && $elements.2.0 eq 'description'}
	<tr>
	<td colspan="3" align="center" valign="top" height="320">
	<textarea style="display: none;" class="detailedViewTextBox" id="description" name="description" cols="90" rows="16">{if isset($elements.3) && isset($elements.3.0)}{$elements.3.0}{/if}</textarea>
	</td>
	</tr>
	{/if}
{/foreach}
{/foreach}
	<tr>
	<td colspan="3" class="mailSubHeader" style="padding: 5px;" align="center">
		 <input title="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_TITLE}" accessKey="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_KEY}" class="crmbutton small edit" onclick="window.open('index.php?module=Users&action=lookupemailtemplates','emailtemplate','top=100,left=200,height=400,width=500,menubar=no,addressbar=no,status=yes')" type="button" name="button" value=" {$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_LABEL} ">
		<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" onclick="return email_validate(this.form,'save');" type="button" name="button" value=" {$APP.LBL_SAVE_BUTTON_LABEL} " >&nbsp;
		<input name="{$MOD.LBL_SEND}" value=" {$APP.LBL_SEND} " class="crmbutton small save" type="button" onclick="return email_validate(this.form,'send');">&nbsp;
		<input value="{$MOD.LBL_ATTACH_DOCUMENTS}" class="crmbutton small edit" type="button" onclick="searchDocuments()">
		<input name="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="crmbutton small cancel" type="button" onClick="window.close()">
	</td>
	</tr>
</tbody>
</table>
</form>
</body>
<script>
var cc_err_msg = '{$MOD.LBL_CC_EMAIL_ERROR}';
var no_rcpts_err_msg = '{$MOD.LBL_NO_RCPTS_EMAIL_ERROR}';
var bcc_err_msg = '{$MOD.LBL_BCC_EMAIL_ERROR}';
var conf_mail_srvr_err_msg = '{$MOD.LBL_CONF_MAILSERVER_ERROR}';
var conf_srvr_storage_err_msg = '{$MOD.LBL_CONF_SERVERSTORAGE_ERROR}';
var remove_image_url = "{'no.gif'|@vtiger_imageurl:$THEME}";
document.getElementById('attach_cont').innerHTML = document.getElementById('attach_temp_cont').innerHTML;
</script>
<script type="text/javascript" defer="1">
	var textAreaName = 'description';
	CKEDITOR.replace( textAreaName,	{ldelim}
		extraPlugins : 'uicolor',
		uiColor: '#dfdff1'
	{rdelim} ) ;
	var oCKeditor = CKEDITOR.instances[textAreaName];
</script>
</html>
