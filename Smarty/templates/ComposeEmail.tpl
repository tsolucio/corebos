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
<link rel="stylesheet" href="include/LD/assets/styles/salesforce-lightning-design-system.css" type="text/css" />
<link rel="stylesheet" href="include/LD/assets/styles/customLD.css" type="text/css" />
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

		<table class="slds-table slds-table--cell-buffer slds-no-row-hover detailview_table">
			<tbody>
				<tr>
					<td colspan=3>
						<div class="forceRelatedListSingleContainer">
							<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
								<div class="slds-card__header slds-grid">
									<!-- Email Header -->
									<header class="slds-media slds-media--center slds-has-flexi-truncate">
										<div class="slds-media__body">
											<h2>
												<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
													<strong>{$MOD.LBL_COMPOSE_EMAIL}</strong>
												</span>
											</h2>
										</div>
									</header>
								</div>
							</article>
						</div>

						<div class="slds-truncate">
							<table class="slds-table slds-table--cell-buffer slds-no-row-hover detailview_table">
								<!-- "send mail from row" -->
								<tr>
									<td class="dvtCellLabel mailSubHeader" align="right"><b><font color="red">*</font>{$MOD.LBL_FROM}</b></td>
									<td class="dvtCellInfo">
										<input name="from_email" id="from_email" class="slds-input" type="text" value="{if isset($FROM_MAIL)}{$FROM_MAIL}{/if}" placeholder="{'LeaveEmptyForUserEmail'|@getTranslatedString:'Settings'}">
									</td>
								</tr>
								{foreach item=row from=$BLOCKS}
									{foreach item=elements from=$row}
										{if isset($elements.2) && isset($elements.2.0) && $elements.2.0 eq 'parent_id'}
											<!-- "send email To" row -->
											<tr>
												<td class="mailSubHeader dvtCellLabel" align="right"><b><font color="red">*</font>{$MOD.LBL_TO}</b></td>
												<td class="dvtCellInfo">
													<input name="{$elements.2.0}" id="{$elements.2.0}" type="hidden" value="{if isset($IDLISTS)}{$IDLISTS}{/if}">
													<input type="hidden" name="saved_toid" value="{if isset($TO_MAIL)}{$TO_MAIL}{/if}">
													<input id="parent_name" name="parent_name" readonly class="slds-input" type="text" value="{if isset($TO_MAIL)}{$TO_MAIL}{/if}">&nbsp;
													<span class="mailClientCSSButton">
														<img src="{'select.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" onclick='return window.open("index.php?module="+ document.EditView.parent_type.value +"&action=Popup&html=Popup_picker&form=HelpDeskEditView&popuptype=set_return_emails","test","width=640,height=602,resizable=0,scrollbars=0,top=150,left=200");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;
													</span>
													<span class="mailClientCSSButton" >
														<img src="{'clear_field.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" onClick="document.getElementById('parent_id').value=''; document.getElementById('hidden_toid').value='';document.getElementById('parent_name').value=''; return false;" align="absmiddle" style='cursor:pointer'>
													</span>
												</td>
												<td class="cellText" align="left" nowrap>
													<select name="parent_type" class="slds-select">
														{foreach key=labelval item=selectval from=$elements.1.0}
															{if $selectval eq selected} {assign var=selectmodule value="selected"} {else} {assign var=selectmodule value=""} {/if}
															<option value="{$labelval}" {$selectmodule}>{$labelval|@getTranslatedString:$labelval}</option>
														{/foreach}
													</select>
												</td>
											</tr>
											<!-- CC row -->
											<tr>
												{if 'ccmail'|@emails_checkFieldVisiblityPermission:'readwrite' eq '0'}
												<td class="mailSubHeader dvtCellLabel" style="padding: 5px;" align="right">{$MOD.LBL_CC}</td>
												<td class="dvtCellInfo">
													<input name="ccmail" id ="cc_name" class="slds-input" type="text" value="{if isset($CC_MAIL)}{$CC_MAIL}{/if}">&nbsp;
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
												<td valign="top" class="dvtCellLabel" rowspan="4">
													<div id="attach_cont" class="addEventInnerBox" style="overflow:auto;height:100px;width:100%;position:relative;left:0px;top:0px;"></div>
												</td>
											</tr>
											{if 'bccmail'|@emails_checkFieldVisiblityPermission:'readwrite' eq '0'}
												<!-- BCC row -->
												<tr>
													<td class="mailSubHeader dvtCellLabel" style="padding: 5px;" align="right">{$MOD.LBL_BCC}</td>
													<td class="dvtCellInfo" style="padding: 5px;">
														<input name="bccmail" id="bcc_name" class="slds-input" type="text" value="{if isset($BCC_MAIL)}{$BCC_MAIL}{/if}">&nbsp;
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
											<!-- Subject row -->
											<tr>
												<td class="mailSubHeader dvtCellLabel" align="right" nowrap><font color="red">*</font>{$elements.1.0} :</td>
												{if (isset($WEBMAIL) && $WEBMAIL eq 'true') or (isset($RET_ERROR) && $RET_ERROR eq 1)}
													<td class="dvtCellInfo"><input type="text" class="slds-input" name="{$elements.2.0}" value="{$SUBJECT}" id="{$elements.2.0}"></td>
												{else}
													<td class="dvtCellInfo"><input type="text" class="slds-input" name="{$elements.2.0}" value="{$elements.3.0}" id="{$elements.2.0}"></td>
												{/if}
											</tr>
										{elseif isset($elements.2) && isset($elements.2.0) && $elements.2.0 eq 'filename'}
											<!-- Attachment row -->
											<tr>
												<td class="mailSubHeader dvtCellLabel" align="right" nowrap>{$elements.1.0} :</td>
												<td class="dvtCellInfo">
													<!--<input name="{$elements.2.0}" type="file" class="small txtBox" value="" size="78"/>-->
													<input name="del_file_list" type="hidden" value="">
													<div id="files_list" style="padding: 5px; background: rgb(255, 255, 255) none repeat scroll 0%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; font-size: x-small">{$APP.Files_Maximum}{$EMail_Maximum_Number_Attachments}</span>
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
											<!-- Buttons -->
											<tr>
												<td colspan="3" class="mailSubHeader" style="padding: 5px;" align="center">
													 <input title="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_TITLE}" accessKey="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_KEY}" class="slds-button slds-button--brand slds-button--small" onclick="window.open('index.php?module=Users&action=lookupemailtemplates','emailtemplate','top=100,left=200,height=400,width=500,resizable=yes,scrollbars=yes,menubar=no,addressbar=no,status=yes')" type="button" name="button" value=" {$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_LABEL} ">
													<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="slds-button slds-button--small slds-button_success" onclick="return email_validate(this.form,'save');" type="button" name="button" value=" {$APP.LBL_SAVE_BUTTON_LABEL} " >&nbsp;
													<input name="{$MOD.LBL_SEND}" value=" {$APP.LBL_SEND} " class="slds-button slds-button--small slds-button--warning" type="button" onclick="return email_validate(this.form,'send');">&nbsp;
													<input value="{$MOD.LBL_ATTACH_DOCUMENTS}" class="slds-button slds-button--small slds-button--info" type="button" onclick="searchDocuments()">
													<input name="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="slds-button slds-button--small slds-button--destructive" type="button" onClick="window.close()">
												</td>
											</tr>
										{elseif isset($elements.2) && isset($elements.2.0) && $elements.2.0 eq 'description'}
											<!-- Text area email box -->
											<tr>
												<td colspan="3" align="center" valign="top" style="padding:0;">
													{if (isset($WEBMAIL) && $WEBMAIL eq 'true') or (isset($RET_ERROR) && $RET_ERROR eq 1)}
														<input type="hidden" name="from_add" value="{$from_add}">
														<input type="hidden" name="att_module" value="Webmails">
														<input type="hidden" name="mailid" value="{$mailid}">
														<input type="hidden" name="mailbox" value="{$mailbox}">
														<textarea style="display: none;" class="slds-textarea" id="description" name="description" cols="90" rows="8">{$DESCRIPTION}</textarea>
													{else}
														<textarea style="display: none;" class="slds-textarea" id="description" name="description" cols="90" rows="16">{if isset($elements.3) && isset($elements.3.0)}{$elements.3.0}{/if}</textarea>
													{/if}
												</td>
											</tr>
										{/if}
									{/foreach}
								{/foreach}
								<!-- Bottom buttons -->
								<tr>
									<td colspan="3" class="mailSubHeader" style="padding: 5px;" align="center">
										 <input title="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_TITLE}" accessKey="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_KEY}" class="slds-button slds-button--small slds-button--brand" onclick="window.open('index.php?module=Users&action=lookupemailtemplates','emailtemplate','top=100,left=200,height=400,width=500,menubar=no,addressbar=no,status=yes')" type="button" name="button" value=" {$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_LABEL} ">
										<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="slds-button slds-button--small slds-button_success" onclick="return email_validate(this.form,'save');" type="button" name="button" value=" {$APP.LBL_SAVE_BUTTON_LABEL} " >&nbsp;
										<input name="{$MOD.LBL_SEND}" value=" {$APP.LBL_SEND} " class="slds-button slds-button--small slds-button--warning" type="button" onclick="return email_validate(this.form,'send');">&nbsp;
										<input value="{$MOD.LBL_ATTACH_DOCUMENTS}" class="slds-button slds-button--small slds-button--info" type="button" onclick="searchDocuments()">
										<input name="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="slds-button slds-button--small slds-button--destructive" type="button" onClick="window.close()">
									</td>
								</tr>
							</table>
						</div>
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