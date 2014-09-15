{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
<span class="moduleName" id="mail_fldrname">{$FOLDER->name()}</span>
<div class="mm_outerborder" id="open_email_con" name="open_email_con">
<table width="100%" cellpadding=2 cellspacing=0 border=0 class="small" style='clear: both;'>
	<tr class="mailSubHeader" valign="top">
		
		<td align=left>
			<a href='javascript:void(0);' onclick="MailManager.mail_close();"><b style="font-size:14px">&#171; {'LBL_Go_Back'|@getTranslatedString}</b></a>&nbsp;&nbsp;&nbsp;
			<span class="dvHeaderText" id="_mailopen_subject">{$MAIL->subject()}</span>
		</td>
		<td align="right" nowrap="nowrap">
			{if $MAIL->msgno() < $FOLDER->count()}
				<a href='javascript:void(0);' onclick="MailManager.mail_open( '{$FOLDER->name()}', {$MAIL->msgno(1)} );">
					<img border="0" src="modules/Webmails/images/previous.gif" title="{'LBL_Previous'|@getTranslatedString}"></a>
			{/if}
			{if $MAIL->msgno() > 1}
				<a href='javascript:void(0);' onclick="MailManager.mail_open( '{$FOLDER->name()}', {$MAIL->msgno(-1)} );">
				<img border="0" src="modules/Webmails/images/next.gif" title="{'LBL_Next'|@getTranslatedString}"></a>
			{/if}
		</td>
	</tr>
{strip}
<tr valign=top>
	<td>
		 
		&nbsp;<button class="crmbutton small edit" onclick="MailManager.mail_reply(true);">{'LBL_Reply_All'|@getTranslatedString}</button>
		&nbsp;<button class="crmbutton small edit" onclick="MailManager.mail_reply(false);">{'LBL_Reply'|@getTranslatedString}</button>
		&nbsp;<button class="crmbutton small edit" onclick="MailManager.mail_forward({$MAIL->msgno()});">{'LBL_Forward'|@getTranslatedString}</button>
		&nbsp;<button class="crmbutton small edit" onclick="MailManager.mail_mark_unread('{$FOLDER->name()}', {$MAIL->msgno()});">{'LBL_Mark_As_Unread'|@getTranslatedString}</button>
		&nbsp;<button class="crmbutton small delete" id = 'mail_delete_dtlview' class="small" onclick="MailManager.maildelete('{$FOLDER->name()}',{$MAIL->msgno()},true);">{'LBL_Delete'|@getTranslatedString}</button>
	</td>
	<td rowspan=3 align=right colspan=2>
		<table cellpadding=0 cellspacing=0 border=0 width="100%">
		<tr>		
			<td colspan=2 nowrap="nowrap">
				<table width=100% cellpadding=0 cellspacing=0 border=0 class="rightMailMerge">
				<tr>
					<td class="rightMailMergeHeader" align="center"><b>{'LBL_RELATED_RECORDS'|@getTranslatedString}</b></td>
				</tr>
				<tr>
					<td class="rightMailMergeContent" align="center">
						<button class="small" id="_mailrecord_findrel_btn_" onclick="MailManager.mail_find_relationship();">{'JSLBL_Find_Relation_Now'|@getTranslatedString}</button>
						<div id="_mailrecord_relationshipdiv_"></div>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
	</td>
</tr>
		
<tr valign=top>
	<td>
		<span id="_mailopen_msgid_" style="display:none;">{$MAIL->_uniqueid|@escape:'UTF-8'}</span>
		<table width="100%" cellpadding=2 cellspacing=0 border=0 class="small">
		<tr>
			<td width="100px" align=right>{'LBL_FROM'|@getTranslatedString}:</td>
			<td id="_mailopen_from">
				{foreach item=SENDER from=$MAIL->from()}
					{$SENDER}
				{/foreach}
			</td>
		</tr>
		{if $MAIL->to()}
		<tr>
			<td width="100px" align=right>{'LBL_TO'|@getTranslatedString}:</td>
			<td id="_mailopen_to">
				{foreach item=RECEPIENT from=$MAIL->to() name="TO"}
					{if $smarty.foreach.TO.index > 0}, {/if}{$RECEPIENT}
				{/foreach}
			</td>
		</tr>
		{/if}
		
		{if $MAIL->cc()}
		<tr>
			<td width="100px" align=right>{'LBL_CC'|@getTranslatedString}:</td>
			<td id="_mailopen_cc">
				{foreach item=CC from=$MAIL->cc() name="CC"}
					{if $smarty.foreach.CC.index > 0}, {/if}{$CC}
				{/foreach}
			</td>
		</tr>
		{/if}
		
		{if $MAIL->bcc()}
		<tr>
			<td width="100px" align=right>{'LBL_BCC'|@getTranslatedString}:</td>
			<td id="_mailopen_bcc">
				{foreach item=BCC from=$MAIL->bcc() name="BCC"}
					{if $smarty.foreach.BCC.index > 0}, {/if}{$BCC}
				{/foreach}
			</td>
		</tr>
		{/if}
		
		<tr>
			<td width="100px" align=right>{'LBL_Date'|@getTranslatedString}:</td>
			<td id="_mailopen_date">{$MAIL->date()}</td>
		</tr>
		
		{if $MAIL->attachments(false)}
		<tr>
			<td width="100px" align=right>{'LBL_Attachments'|@getTranslatedString}:</td>
			<td>
				{foreach item=ATTACHVALUE key=ATTACHNAME from=$MAIL->attachments(false) name="attach"}
					<img border=0 src="{'attachments.gif'|@vtiger_imageurl:$THEME}">
					<a href="index.php?module={$MODULE}&action={$MODULE}Ajax&file=index&_operation=mail&_operationarg=attachment_dld&_muid={$MAIL->muid()}&_atname={$ATTACHNAME|@escape:'htmlall':'UTF-8'}">{$ATTACHNAME}</a>
					&nbsp;
				{/foreach}
					<input type="hidden" id="_mail_attachmentcount_" value="{$smarty.foreach.attach.total}" >
			</td>
		</tr>
		{/if}
		
		</table>
	</td>
</tr>
{/strip}
<tr valign=top>
	<td width="100%">
		<div class='mm_body' id="_mailopen_body">
			{$MAIL->body()}
		</div>
	</td>
</tr>
</table>

</div>