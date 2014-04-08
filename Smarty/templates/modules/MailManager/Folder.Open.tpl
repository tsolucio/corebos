{************************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
<span class="moduleName" id="mail_fldrname">{$FOLDER->name()}</span>
<div class="mailClientBg mm_outerborder" id="email_con" name="email_con">
<table width="100%" cellpadding=3 cellspacing=0 border=0 class="small">
	
{if $FOLDER->mails()}
<tr>
	<td>
		
	</td>
	<td align="right" colspan="4">
		<table>
			<tr>
			{if $FOLDER->hasPrevPage()}
				<td>
					<a href="#{$FOLDER->name()}/page/{$FOLDER->pageCurrent(-1)}" onclick="MailManager.folder_open('{$FOLDER->name()}', {$FOLDER->pageCurrent(-1)});">
					<img border="0" src="modules/Webmails/images/previous.gif" title="{'LBL_Previous'|@getTranslatedString}"></a>
				</td>
			{/if}

				<td><b>{$FOLDER->pageInfo()}</b></td>

			{if $FOLDER->hasNextPage()}
				<td><a href="#{$FOLDER->name()}/page/{$FOLDER->pageCurrent(1)}" onclick="MailManager.folder_open('{$FOLDER->name()}', {$FOLDER->pageCurrent(1)});">
					<img border="0" src="modules/Webmails/images/next.gif" title="{'LBL_Next'|@getTranslatedString}"></a>
				</td>
			{/if}
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td colspan="5">
	<table class="mm_tableHeadBg small" width="100%" cellspacing="0" border="0" cellpadding="2px" >
		<tr>
			<td align="left" style="width: 10%;" nowrap="nowrap">
				<input align="left" type="checkbox" class='small'  name="selectall" id="parentCheckBox" onClick='MailManager.toggleSelect(this.checked,"mc_box");'/>&nbsp;&nbsp;
				<input type=button class='crmbutton small delete' onclick="MailManager.massMailDelete('{$FOLDER->name()}');" value="{'LBL_Delete'|@getTranslatedString}"/>
			</td>
			<td align="left">
				<select class='small' id="moveFolderList" onchange="MailManager.moveMail(this);">
					<option value="">{'LBL_MOVE_TO'|@getTranslatedString:$MODULE}</option>
					{foreach item=folder from=$FOLDERLIST}
						<option value="{$folder|@htmlentities}" >{$folder|@htmlentities}</option>
					{/foreach}
				</select>
			</td>
			<td class="moduleName" align="right">{'LBL_Search'|@getTranslatedString}
				<input type="text" id='search_txt' class='small' value="{$QUERY}" />
				{'LBL_IN'|@getTranslatedString}
				<select class='small' id="search_type">
					{foreach item=arr from=$SEARCHOPTIONS}
						{if $arr eq $TYPE}
							<option value="{$arr}" selected>{$arr|getTranslatedString}</option>
						{else}
							<option value="{$arr}" >{$arr|getTranslatedString}</option>
						{/if}
					{/foreach}
				</select>
				<input type=submit class="crmbutton small edit" onclick="MailManager.search_mails('{$FOLDER->name()}');" value="{'LBL_FIND'|@getTranslatedString}" id="mm_search"/>
			</td>
		</tr>
	</table>
	</td>
</tr>
{/if}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="cmall mm_mailwrapper">
	{foreach item=MAIL from=$FOLDER->mails()}
	<tr class="mm_lvtColData {if $MAIL->isRead()}mm_normal{else}mm_bold{/if} mm_clickable"
		 id="_mailrow_{$MAIL->msgNo()}" onmouseover='MailManager.highLightListMail(this);' onmouseout='MailManager.unHighLightListMail(this);'>
		<td width="3%"><input type='checkbox' value = "{$MAIL->msgNo()}" name = 'mc_box' class='small'
                              onclick='MailManager.toggleSelectMail(this.checked, this);'></td>
		<td width="27%" onclick="MailManager.mail_open('{$FOLDER->name()}', {$MAIL->msgNo()});">{$MAIL->from(30)}</td>
		<td onclick="MailManager.mail_open('{$FOLDER->name()}', {$MAIL->msgNo()});">{$MAIL->subject()}</td>
		<td width="17%" align="right" onclick="MailManager.mail_open('{$FOLDER->name()}', {$MAIL->msgNo()});">{$MAIL->date(true)}</td>
	</tr>
	{foreachelse}
</table>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td><a href='#{$FOLDER->name()}' onclick="MailManager.folder_open('{$FOLDER->name()}');"><b>{$FOLDER->name()}</b></a></td>
</tr>
<tr>
	<td>{'LBL_No_Mails_Found'|@getTranslatedString}</td>
</tr>
{/foreach}
</table>
</div>
