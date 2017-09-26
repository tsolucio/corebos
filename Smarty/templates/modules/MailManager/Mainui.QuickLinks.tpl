{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
<div class="flexipageComponent">
	<article class="slds-card container MEDIUM forceBaseCard runtime_sales_mergeMergeCandidatesPreviewCard" aria-describedby="header" style="margin: 0;">
		<div class="slds-card__header slds-grid">
			<header class="slds-media slds-media--center slds-has-flexi-truncate">
				<div class="slds-media__body">
					<h2 class="header-title-container">
						<span class="slds-text-heading--small slds-truncate actionLabel"><strong class="txtGreen">{'LBL_Mailbox'|@getTranslatedString}</strong></span>
					</h2>
				</div>
			</header>
		</div>
		<div class="slds-card__body slds-card__body--inner mail-action">
			{if $MAILBOX && $MAILBOX->exists()}
			<div class="actionData">
				<input type=hidden name="mm_selected_folder" id="mm_selected_folder">
				<input type="hidden" name="_folder" id="mailbox_folder">
			</div>
			<div class="actionData">
				<img src="{'compose.gif'|@vtiger_imageurl:$THEME}" border='0'>
				<a href="#Compose" id="_mailfolder_mm_compose" onclick="MailManager.mail_compose();">{'LBL_Compose'|@getTranslatedString}</a>
			</div>
			<div class="actionData">
				<img src="{'reload.gif'|@vtiger_imageurl:$THEME}" border='0'/>
				<a href='#Reload' id="_mailfolder_mm_reload" onclick="MailManager.reload_now();">{'LBL_Refresh'|@getTranslatedString}</a>
			</div>
			{/if}
			<div class="actionData">
				<img align="absbottom" src="{'settings_top.gif'|@vtiger_imageurl:$THEME}" border='0'/>
				<a href='#Settings' id="_mailfolder_mm_settings" onclick="MailManager.open_settings();">{'JSLBL_Settings'|@getTranslatedString}</a>
			</div>
			<div class="actionData">
				<img src="{'mymail.gif'|@vtiger_imageurl:$THEME}" border='0'/>
				<a href="#Drafts" id="_mailfolder_mm_drafts" onclick="MailManager.folder_drafts();">{'LBL_Drafts'|@getTranslatedString}</a>
			</div>
		</div>
	</article>
</div>
{include file="SentMailFolders.tpl"}