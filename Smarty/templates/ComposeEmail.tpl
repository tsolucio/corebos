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
{if $LOADLDS == 'yes'}
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset={$LBL_CHARSET}">
		<title>{$MOD.TITLE_COMPOSE_MAIL}</title>
		<link REL="SHORTCUT ICON" HREF="themes/images/favicon.ico">
		<link rel="stylesheet" type="text/css" media="all" href="themes/{$THEME}/style.css">
		{/if}
		<script type="text/javascript" src="include/jquery/jquery.js"></script>
		{include file='BrowserVariables.tpl'}
		<script type="text/javascript" src="include/js/general.js"></script>
		<script type="text/javascript" src="include/js/{$LANGUAGE}.lang.js"></script>
		<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
		<script type="text/javascript" src="modules/Products/multifile.js"></script>
		<script type="text/javascript" src="modules/Emails/Emails.js"></script>
		<script type="text/javascript" src="include/js/vtlib.js"></script>
		<script type="text/javascript" src="include/js/Mail.js"></script>
		{if $LOADLDS == 'yes'}
		<link rel="stylesheet" href="include/LD/assets/styles/salesforce-lightning-design-system.css">
		{/if}
		<link rel="stylesheet" href="modules/Emails/style/mail.css">
	{if $LOADLDS == 'yes'}
	</head>
	<body class="slds-p-around_x-small body-bg slds-box slds-theme_shade slds-theme_alert-texture">
	{/if}
	{literal}
	<form name="EditView" id="SendMailForm" method="POST" ENCTYPE="multipart/form-data" action="index.php" onSubmit="if(email_validate(this.form,'')) { VtigerJS_DialogBox.block();} else { return false; }">
	{/literal}
	<input type="hidden" name="merge_template_with" id="merge_template_with" value="{$MERGE_TEMPLATE_WITH}">
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
	<input type="hidden" name="templateid" id="templateid">
	<input type="hidden" name="cbcustominfo1" id="cbcustominfo1" value="{if isset($smarty.request.cbcustominfo1)}{$smarty.request.cbcustominfo1|@urlencode}{/if}" />
	<input type="hidden" name="cbcustominfo2" id="cbcustominfo2" value="{if isset($smarty.request.cbcustominfo2)}{$smarty.request.cbcustominfo2|@urlencode}{/if}" />
	<div class="slds-page-header">
		<div class="slds-page-header__row">
			<div class="slds-page-header__col-title">
				<div class="slds-media">
					<div class="slds-media__body">
						<div class="slds-page-header__name">
							<div class="slds-page-header__name-title">
								<h1>
									<span class="slds-page-header__title slds-truncate" title="{$MOD.LBL_COMPOSE_EMAIL}">
										{$MOD.LBL_COMPOSE_EMAIL}
									</span>
								</h1>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<table class="slds-table">
	<tbody>
		<tr>
			<td align="right">
				<font color="red">*</font><b> {$MOD.LBL_FROM}</b>
			</td>
			<td class="padding-5p">
				<input
					name="from_email"
					id="from_email"
					class="slds-input wd-525"
					type="text"
					value="{if isset($FROM_MAIL)}{$FROM_MAIL}{/if}"
					placeholder="{'LeaveEmptyForUserEmail'|@getTranslatedString:'Settings'}"
				>
			</td>
			<td class="padding-5p" align="left" nowrap>
				<input
					type="checkbox"
					name="individual_emails"
					{if $SEND_INDIVIDUAL_EMAILS eq 1}checked{/if}
				/> {$MOD.LBL_SEND_INDIVIDUAL_EMAILS}
			</td>
		</tr>
		{foreach item=row from=$BLOCKS}
		{foreach item=elements from=$row}
		{if isset($elements.2) && isset($elements.2.0) && $elements.2.0 eq 'parent_id'}
		<tr>
			<td align="right">
				<font color="red">*</font><b> {$MOD.LBL_TO}</b>
			</td>
			<td class="padding-5p">
				<input
					name="listofids"
					id="listofids"
					type="hidden"
					value="{if isset($LISTID)}{$LISTID}{/if}"
				>
				<input
					name="{$elements.2.0}"
					id="{$elements.2.0}"
					type="hidden"
					value="{if isset($IDLISTS)}{$IDLISTS}{/if}"
				>
				<input
					name="relateemailwith"
					id="relateemailwith"
					type="hidden"
					value="{if isset($relateemailwith)}{$relateemailwith}{/if}"
				>
				<input
					type="hidden"
					id="saved_toid"
					name="saved_toid"
					value="{if isset($TO_MAIL)}{$TO_MAIL}{/if}"
				>
				<input
					id="parent_name"
					name="parent_name"
					readonly
					class="slds-input wd-525 border-1p mt-1"
					type="text"
					value="{if isset($TO_MAIL)}{$TO_MAIL}{/if}"
				>
				<div class="slds-button-group" role="group">
					<button
						type="button"
						class="slds-button slds-button_icon slds-button_icon-border-filled"
						onclick="SelectMail('to')"
					>
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
						</svg>
					</button>
					<button
						type="button"
						class="slds-button slds-button_icon slds-button_icon-border-filled btn-red"
						onclick="ClearMail('to')"
					>
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
						</svg>
					</button>
				</div>
			</td>
			<td class="padding-5p" align="left" nowrap>
				<div class="slds-form-element">
					<div class="slds-form-element__control">
						 <div class="slds-select_container">
							<select class="slds-select" name="parent_type">
								{foreach key=labelval item=selectval from=$elements.1.0}
									{if $selectval eq selected}
										{assign var=selectmodule value="selected"}
									{else}
										{assign var=selectmodule value=""}
									{/if}
									<option value="{$labelval}" {$selectmodule}>{$labelval|@getTranslatedString:$labelval}</option>
								{/foreach}
							</select>
						</div>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			{if 'replyto'|@emails_checkFieldVisiblityPermission:'readwrite' eq '0'}
			<td class="padding-5p" align="right">{$MOD.replyto}</td>
				<td class="padding-5p">
					<input
						name="replyto"
						id="replyto"
						class="slds-input wd-525"
						type="text"
						value="{if isset($REPLYTO)}{$REPLYTO}{/if}"
					>
				</td>
			{else}
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			{/if}
			<td valign="top" rowspan="4">
				<div id="attach_cont" class="addEventInnerBox attach-box">
				</div>
			</td>
		</tr>
		<tr>
		{if 'ccmail'|@emails_checkFieldVisiblityPermission:'readwrite' eq '0'}
			<td class="padding-5p" align="right">
				{$MOD.LBL_CC}
			</td>
			<td class="padding-5p">
				<input
					name="ccmail"
					id="cc_name"
					class="slds-input wd-525 mt-1"
					type="text"
					value="{if isset($CC_MAIL)}{$CC_MAIL}{/if}"
				>
				<div class="slds-button-group" role="group">
					<button
						type="button"
						class="slds-button slds-button_icon slds-button_icon-border-filled"
						onclick="SelectMail('cc')"
					>
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
						</svg>
					</button>
					<button
						type="button"
						class="slds-button slds-button_icon slds-button_icon-border-filled btn-red"
						onclick="ClearMail('cc')"
					>
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
						</svg>
					</button>
				</div>
			</td>
		{/if}
		</tr>
		{if 'bccmail'|@emails_checkFieldVisiblityPermission:'readwrite' eq '0'}
		<tr>
			<td class="padding-5p" align="right">
				{$MOD.LBL_BCC}
			</td>
			<td class="padding-5p">
				<input
					name="bccmail"
					id="bcc_name"
					class="slds-input wd-525 mt-1"
					type="text"
					value="{if isset($BCC_MAIL)}{$BCC_MAIL}{/if}"
				>
				<div class="slds-button-group" role="group">
					<button
						type="button"
						class="slds-button slds-button_icon slds-button_icon-border-filled"
						onclick="SelectMail('bcc')"
					>
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
						</svg>
					</button>
					<button
						type="button"
						class="slds-button slds-button_icon slds-button_icon-border-filled btn-red"
						onclick="ClearMail('bcc')"
					>
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
						</svg>
					</button>
				</div>
			</td>
		</tr>
		{/if}
		{elseif isset($elements.2) && isset($elements.2.0) && $elements.2.0 eq 'subject'}
		<tr>
			<td class="padding-5p" align="right" nowrap>
				<font color="red">*</font>{$elements.1.0}:
			</td>
			{if (isset($WEBMAIL) && $WEBMAIL eq 'true') or (isset($RET_ERROR) && $RET_ERROR eq 1)}
			<td class="padding-5p">
				<input
					type="text"
					class="slds-input"
					name="{$elements.2.0}"
					value="{$SUBJECT}"
					id="{$elements.2.0}"
				>
			</td>
			{else}
			<td class="padding-5p">
				<input
					type="text"
					class="slds-input"
					name="{$elements.2.0}"
					value="{$elements.3.0}"
					id="{$elements.2.0}"
				>
			</td>
		{/if}
		</tr>
		{elseif isset($elements.2) && isset($elements.2.0) && $elements.2.0 eq 'filename'}
		<tr>
			<td class="padding-5p" align="right" nowrap>
				{$elements.1.0}:
			</td>
			<td class="padding-5p">
				<input name="del_file_list" type="hidden" value="">
				<div id="files_list" class="file-box">
					<input
						id="my_file_element"
						type="file"
						name="{$elements.2.0}"
						tabindex="7"
						onchange="validateFilename(this)"
					>
					<input
						type="hidden"
						name="{$elements.2.0}_hidden"
						value=""
					>
					<span id="limitmsg" class="slds-float_right limit-box btn-red">
						({'LBL_MAX_SIZE'|@getTranslatedString:$MODULE} {$UPLOADSIZE} {'LBL_FILESIZEIN_MB'|@getTranslatedString:$MODULE})
					</span>
					<span class="slds-float_right slds-p-right_xx-small">
						{$APP.Files_Maximum}{$EMail_Maximum_Number_Attachments} 
					</span>
				</div>
				<script>
					var multi_selector = new MultiSelector(document.getElementById('files_list'), {$EMail_Maximum_Number_Attachments});
					multi_selector.count = 0;
					multi_selector.addElement(document.getElementById('my_file_element'));
				</script>
				<div id="attach_temp_cont" style="display:none;">
				<table class="small" width="100% ">
					{if !empty($smarty.request.attachment) && $select_module!='Documents'}
					<tr>
						<td width="100%" colspan="2">
							{$smarty.request.attachment|@vtlib_purify}
							<input type="hidden" value="{$smarty.request.attachment|@vtlib_purify}" name="pdf_attachment">
						</td>
					</tr>
					{elseif !empty($smarty.request.attachment) && $select_module=='Documents'}
					<div>
						<a href='javascript:void(0)' onclick='this.parentNode.parentNode.removeChild(this.parentNode);'>
							<img src="{'no.gif'|@vtiger_imageurl:$THEME}">
						</a>
						{$DOCNAME}
						<input type='hidden' name='doc_attachments[]' value='{$DOCID}'>
					</div>
					{else}
					{foreach item="attach_files" key="attach_id" from=$elements.3}
					<tr id="row_{$attach_id}">
						<td width="90%">
							{$attach_files}
						</td>
						<td>
							<img src="{'no.gif'|@vtiger_imageurl:$THEME}" onClick="delAttachments({$attach_id})" alt="{$APP.LBL_DELETE_BUTTON}" title="{$APP.LBL_DELETE_BUTTON}" style="cursor:pointer;">
						</td>
					</tr>
					{/foreach}
					<input type='hidden' name='att_id_list' value='{$ATT_ID_LIST}' />
					{/if}
					{if isset($WEBMAIL) && $WEBMAIL eq 'true'}
						{foreach item="attach_files" from=$webmail_attachments}
						<tr>
							<td width="90%">
								{$attach_files}
							</td>
						</tr>
						{/foreach}
					{/if}
				</table>
				</div>
				{if isset($elements.3) && isset($elements.3.0)}{$elements.3.0}{/if}
			</td>
		</tr>
		<tr>
			<td colspan="3" class="padding-5p" align="center">
				<input type='hidden' class='small' name="msgtpopup_type" id="msgtpopup_type" value="MsgTemplate">
				<div id="send-mail-actions">
					<div class="slds-button-group" role="group">
						<button
							title="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_TITLE}"
							accessKey="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_KEY}"
							class="slds-button slds-button_neutral"
							onclick="SelectMail('template')"
							type="button"
						>
							<svg class="slds-button__icon slds-p-right_xxx-small" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#multi_select_checkbox"></use>
							</svg>
							{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_LABEL}
						</button>
						<button
							title="{$APP.LBL_SAVE_BUTTON_TITLE}"
							accessKey="{$APP.LBL_SAVE_BUTTON_KEY}"
							class="slds-button slds-button_neutral"
							onclick="email_validate(this.form, 'save');"
							type="button"
							name="button"
						>
							<svg class="slds-button__icon slds-p-right_xxx-small" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
							</svg>
							{$APP.LBL_SAVE_BUTTON_LABEL}
						</button>
						<button
							name="{$MOD.LBL_SEND}"
							class="slds-button slds-button_neutral"
							type="button"
							onclick="email_validate(this.form, 'send');"
						>
							<svg class="slds-button__icon slds-p-right_xxx-small" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#send"></use>
							</svg>
							{$APP.LBL_SEND}
						</button>
						<button
							class="slds-button slds-button_neutral"
							type="button"
							onclick="searchDocuments()"
						>
							<svg class="slds-button__icon slds-p-right_xxx-small" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#attach"></use>
							</svg>
							{$MOD.LBL_ATTACH_DOCUMENTS}
						</button>
						<button
							class="slds-button slds-button_neutral"
							type="button"
							onclick="MailPreview()"
						>
							<svg class="slds-button__icon slds-p-right_xxx-small" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#preview"></use>
							</svg>
							{$APP.LBL_PREVIEW}
						</button>
						{if $LOADLDS == 'yes'}
						<button
							name="{$APP.LBL_CANCEL_BUTTON_TITLE}"
							accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}"
							class="slds-button slds-button_text-destructive"
							type="button"
							onclick="window.close()"
						>
							<svg class="slds-button__icon slds-p-right_xxx-small" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
							</svg>
							{$APP.LBL_CANCEL_BUTTON_LABEL}
						</button>
						{/if}
					</div>
				</div>
			</td>
		</tr>
		{elseif isset($elements.2) && isset($elements.2.0) && $elements.2.0 eq 'description'}
		<tr>
			<td colspan="3" align="center" valign="top" height="320">
				<textarea style="display: none;" class="detailedViewTextBox" id="description" name="description" cols="90" rows="16">
				{if isset($elements.3) && isset($elements.3.0)}{$elements.3.0}{/if}
				</textarea>
			</td>
		</tr>
		{/if}
	{/foreach}
	{/foreach}
	</tbody>
	</table>
	</form>
</body>
<script type="text/javascript">
var cc_err_msg = '{$MOD.LBL_CC_EMAIL_ERROR}';
var no_rcpts_err_msg = '{$MOD.LBL_NO_RCPTS_EMAIL_ERROR}';
var bcc_err_msg = '{$MOD.LBL_BCC_EMAIL_ERROR}';
var conf_mail_srvr_err_msg = '{$MOD.LBL_CONF_MAILSERVER_ERROR}';
var conf_srvr_storage_err_msg = '{$MOD.LBL_CONF_SERVERSTORAGE_ERROR}';
var remove_image_url = "{'no.gif'|@vtiger_imageurl:$THEME}";
document.getElementById('attach_cont').innerHTML = document.getElementById('attach_temp_cont').innerHTML;
var textAreaName = 'description';
CKEDITOR.replace(textAreaName, {
	extraPlugins : 'uicolor',
	uiColor: '#dfdff1'
});
var oCKeditor = CKEDITOR.instances[textAreaName];
</script>
{if vt_hasRTESpellcheck()}
<script type="text/javascript" src="include/ckeditor/config_spellcheck.js"></script>
{/if}
{if $LOADLDS == 'yes'}
</html>
{/if}