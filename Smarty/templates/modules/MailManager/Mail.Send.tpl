{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
<form action="javascript:void(0)" method="POST" id="_mail_replyfrm_" ENCTYPE="multipart/form-data" name='submit'>

	<div class="slds-table--scoped">
		<ul class="slds-tabs--scoped__nav" role="tablist" style="margin-bottom: 0;">
			<li class="slds-tabs--scoped__item active" title="{$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}" role="presentation">
				<a href="javascript:void(0);" class="moduleName slds-tabs--scoped__link " id="send_mail_fldrname" name="send_mail_fldrname" role="tab" tabindex="0" aria-selected="true" aria-controls="send_mail_fldrname" style="cursor: text;">{'LBL_Compose'|@getTranslatedString}</a>
			</li>
		</ul>
		<div id="send_mail_fldrname" role="tabpanel" aria-labelledby="send_mail_fldrname" class="slds-tabs--scoped__content slds-truncate">
			<!-- <div class="mm_outerborder" id="send_email_con" name="send_email_con"> -->
			<div class="" id="send_email_con" name="send_email_con">
				<div id="_popupsearch_" style="display:none;position:absolute;width:500px;z-index:80000;"></div>
				<input type="hidden" name="emailid" class="detailedViewTextBox" id="emailid"/>
				<input type="hidden" name="docids" class="detailedViewTextBox" id="docids"/>
				<input type="hidden" name="attachmentCount" class="detailedViewTextBox" id="attachmentCount"/>
				<table class="slds-table slds-no-row-hover slds-table-moz" ng-controller="detailViewng" style="border-collapse:separate; border-spacing: 1rem;">
					{strip}
					<tr class="blockStyleCss">
						<td class="detailViewContainer" valign="top">
							<div class="forceRelatedListSingleContainer">
								<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
									<div class="slds-card__header slds-grid">
										<header class="slds-media slds-media--center slds-has-flexi-truncate">
											<div class="slds-media__body">
												<h2>
													<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small">
														<a href="javascript:void(0);" onclick="MailManager.mail_reply_close();">
														&#171;<b>{'LBL_Cancel'|@getTranslatedString}</b>
														</a>
													</span>
												</h2>
											</div>
										</header>
									</div>
								</article>
							</div>
							<div class="slds-truncate">
								<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table mail-send-table">
									<tr class="slds-line-height--reset">
										<td class="dvtCellLabel" width="10%">{'LBL_TO'|@getTranslatedString}:</td>
										<td class="dvtCellInfo" width="40%">
											<input type="text" name="to" class="slds-input" id="_mail_replyfrm_to_">
											<input type="hidden" name="linkto" class="detailedViewTextBox">
											&nbsp;<img id='_mail_replyfrm_popup_to_' class="mm_clickable" align="absmiddle" src="{'select.gif'|vtiger_imageurl:$THEME}" onclick="MailManager.search_popupui('_mail_replyfrm_to_', '_mail_replyfrm_popup_to_');">
											&nbsp;<img class="mm_clickable" align="absmiddle" src="{'clear_field.gif'|vtiger_imageurl:$THEME}" onclick="MailManager.clear_input('_mail_replyfrm_to_');" >
										</td>
										<td class="dvtCellLabel" width="10%">{'LBL_CC'|@getTranslatedString}:</td>
										<td class="dvtCellInfo" width="40%">
											<input type="text" name="cc" class="slds-input" id="_mail_replyfrm_cc_">
											&nbsp;<img id='_mail_replyfrm_popup_cc_' class="mm_clickable" align="absmiddle" src="{'select.gif'|vtiger_imageurl:$THEME}" border=0 onclick="MailManager.search_popupui('_mail_replyfrm_cc_', '_mail_replyfrm_popup_cc_');">
											&nbsp;<img class="mm_clickable" align="absmiddle" src="{'clear_field.gif'|vtiger_imageurl:$THEME}" border=0 onclick="MailManager.clear_input('_mail_replyfrm_cc_');" >
										</td>
									</tr>
									<tr class="slds-line-height--reset">
										<td class="dvtCellLabel" width="10%">{'LBL_BCC'|@getTranslatedString}:</td>
										<td class="dvtCellInfo" width="40%">
											<input type="text" name="bcc" class="slds-input" id="_mail_replyfrm_bcc_">
											&nbsp;<img id='_mail_replyfrm_popup_bcc_' class="mm_clickable" align="absmiddle" src="{'select.gif'|vtiger_imageurl:$THEME}" border=0 onclick="MailManager.search_popupui('_mail_replyfrm_bcc_', '_mail_replyfrm_popup_bcc_');">
											&nbsp;<img class="mm_clickable" align="absmiddle" src="{'clear_field.gif'|vtiger_imageurl:$THEME}" border=0 onclick="MailManager.clear_input('_mail_replyfrm_bcc_');" >
										</td>
										<td class="dvtCellLabel" width="10%">{'LBL_SUBJECT'|@getTranslatedString}:</td>
										<td class="dvtCellInfo" width="40%">
											<input type="text" name="subject" class="slds-input" id="_mail_replyfrm_subject_">
										</td>
									</tr>
								</table>
								<br/>
								<div class="forceRelatedListSingleContainer">
									<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
										<div class="slds-card__header slds-grid">
											<header class="slds-media slds-media--center slds-has-flexi-truncate">
												<div class="slds-media__body">
													<h2>
														<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small">
															<a href="javascript:jQuery('#file-uploader').toggle();">{'LBL_ATTACHMENTS'|getTranslatedString}</a>
														</span>
													</h2>
												</div>
											</header>
											<div class="slds-no-flex">
												<div class="actionsContainer">
													<button onclick="jQuery('#file-uploader').show();MailManager.getDocuments();" class="slds-button slds-button--small slds-button--brand"><b>{'LBL_SELECT_DOCUMENTS'|@getTranslatedString}</b></button>
													&nbsp;<button onclick="jQuery('#file-uploader').toggle();" class="slds-button slds-button--small slds-button_success">{'LBL_Attachments'|@getTranslatedString:'MailManager'}</button>
												</div>
											</div>
										</div>
										<div class="slds-card__body slds-card__body--inner">
											<div class="commentData">
												<div id="file-uploader" class="dropzone mm-dz-div" style="display: none;">
													<span class="dz-message mmdzmessage"><img alt="{'Drag attachment here or click to upload'|@getTranslatedString}" src="include/dropzone/upload_32.png"></span>
													<span class="dz-message mmdzmessage" id="file-uploader-message">&nbsp;{'Drag attachment here or click to upload'|@getTranslatedString}</span>
												</div>
											</div>
										</div>
									</article>
								</div>
								<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table mail-send-table">
									<tr class="slds-line-height--reset">
										<td colspan="4" align="center">
											<button class="slds-button slds-button--small slds-button--info" onclick="MailManager.mail_reply_send(this.form);">{'LBL_Send'|@getTranslatedString}</button>&nbsp;
											<button class="slds-button slds-button--small slds-button_success" onclick="MailManager.save_draft(this.form)">{'LBL_SAVE_NOW'|@getTranslatedString}</button>&nbsp;
											<button class="slds-button slds-button--small slds-button--brand" onclick="window.open('index.php?module=MailManager&action=PopupMailManagerTemplate&subject_id=_mail_replyfrm_subject_&body_id=_mail_replyfrm_body_','emailtemplate','top=100,left=200,height=400,width=500,resizable=yes,scrollbars=yes,menubar=no,addressbar=no,status=yes');">
												<b>{'LBL_SELECT_EMAIL_TEMPLATE'|@getTranslatedString}</b>
											</button>
										</td>
									</tr>
									<tr class="slds-line-height--reset">
										<td colspan="4" align="center" class="textarea-template-cell">
											<textarea name="body" id="_mail_replyfrm_body_" rows="20" class="slds-textarea"></textarea>
										</td>
									</tr>
									<tr class="slds-line-height--reset">
										<td colspan="4" align="center">
											<button class="slds-button slds-button--small slds-button--info" onclick="MailManager.mail_reply_send(this.form);">{'LBL_Send'|@getTranslatedString}</button>&nbsp;
											<button class="slds-button slds-button--small slds-button_success" onclick="MailManager.save_draft(this.form)">{'LBL_SAVE_NOW'|@getTranslatedString}</button>&nbsp;
											<button class="slds-button slds-button--small slds-button--brand" onclick="window.open('index.php?module=MailManager&action=PopupMailManagerTemplate&subject_id=_mail_replyfrm_subject_&body_id=_mail_replyfrm_body_','emailtemplate','top=100,left=200,height=400,width=500,resizable=yes,scrollbars=yes,menubar=no,addressbar=no,status=yes');">
												<b>{'LBL_SELECT_EMAIL_TEMPLATE'|@getTranslatedString}</b>
											</button>
										</td>
									</tr>
								</table>
							</div>
						</td>
					</tr>
					{/strip}
				</table>
			</div>
		</div>
	</div>
</form>