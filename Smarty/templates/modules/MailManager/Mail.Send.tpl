{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
<form action="javascript:void(0)" method="POST" id="_mail_replyfrm_" ENCTYPE="multipart/form-data" name='submit'>
<span class="moduleName" id="send_mail_fldrname" name="send_mail_fldrname">{'LBL_Compose'|@getTranslatedString}</span>
<div class="mm_outerborder"  id="send_email_con" name="send_email_con">
    <div id="_popupsearch_" style="display:none;position:absolute;width:500px;z-index:80000;"></div>
        <input type="hidden" name="emailid" class="detailedViewTextBox" id="emailid"/>
        <input type="hidden" name="docids" class="detailedViewTextBox" id="docids"/>
        <input type="hidden" name="attachmentCount" class="detailedViewTextBox" id="attachmentCount"/>
        <table width="100%" cellpadding=2 cellspacing=0 border=0 class="small" style='clear: both;'>
{strip}
            

            <tr valign=top>
                <td>
                    <a href="javascript:void(0);"onclick="MailManager.mail_reply_close();" style="font-size:14px">&#171;
                        <b>{'LBL_Cancel'|@getTranslatedString}</b></a>&nbsp;
                </td>
            </tr>
			<tr valign=top>
                <td>
                    <table width="100%" cellpadding=2 cellspacing=0 border=0 class="small">
                        <tr>
                            <td align=right style="width:50px">{'LBL_TO'|@getTranslatedString}:</td>
                            <td colspan="2">
                                <input type="text" name="to" class="detailedViewTextBox" id="_mail_replyfrm_to_" style="width:85%;">
                                <input type="hidden" name="linkto" class="detailedViewTextBox">

                                <img id='_mail_replyfrm_popup_to_' class="mm_clickable" align="absmiddle" src="{'select.gif'|vtiger_imageurl:$THEME}" border=0 onclick="MailManager.search_popupui('_mail_replyfrm_to_', '_mail_replyfrm_popup_to_');">
                                <img class="mm_clickable" align="absmiddle" src="{'clear_field.gif'|vtiger_imageurl:$THEME}" border=0 onclick="MailManager.clear_input('_mail_replyfrm_to_');" >
                            </td>
                        </tr>
                        <tr>
                            <td  align=right style="width:50px">{'LBL_CC'|@getTranslatedString}:</td>
                            <td colspan="2">
                                <input type="text" name="cc" class="detailedViewTextBox" id="_mail_replyfrm_cc_" style="width:85%;">

                                <img id='_mail_replyfrm_popup_cc_' class="mm_clickable" align="absmiddle" src="{'select.gif'|vtiger_imageurl:$THEME}" border=0 onclick="MailManager.search_popupui('_mail_replyfrm_cc_', '_mail_replyfrm_popup_cc_');">
                                <img class="mm_clickable" align="absmiddle" src="{'clear_field.gif'|vtiger_imageurl:$THEME}" border=0 onclick="MailManager.clear_input('_mail_replyfrm_cc_');" >
                            </td>
                        </tr>
                        <tr>
                            <td  align=right style="width:50px">{'LBL_BCC'|@getTranslatedString}:</td>
                            <td colspan="2">
                                <input type="text" name="bcc" class="detailedViewTextBox" id="_mail_replyfrm_bcc_" style="width:85%;">

                                <img id='_mail_replyfrm_popup_bcc_' class="mm_clickable" align="absmiddle" src="{'select.gif'|vtiger_imageurl:$THEME}" border=0 onclick="MailManager.search_popupui('_mail_replyfrm_bcc_', '_mail_replyfrm_popup_bcc_');">
                                <img class="mm_clickable" align="absmiddle" src="{'clear_field.gif'|vtiger_imageurl:$THEME}" border=0 onclick="MailManager.clear_input('_mail_replyfrm_bcc_');" >
                            </td>
                        </tr>
                        <tr>
                            <td align=right style="width:50px">{'LBL_SUBJECT'|@getTranslatedString}:</td>
                            <td colspan="2">
                                <input type="text" name="subject" class="detailedViewTextBox" id="_mail_replyfrm_subject_">
                            </td>
                        </tr>

                        <tr>
                            <td valign="top" align="right">
                                {'LBL_ATTACHMENTS'|getTranslatedString}
                            </td>
                            <td width="80%">
                                <div id="file-uploader"></div>
                                <div id="file_list"></div>
                            </td>
                            <td valign="top" align="left" style="white-space:nowrap;">
                                <button onclick="MailManager.getDocuments();" class="crmbutton small edit">{'LBL_SELECT_DOCUMENTS'|@getTranslatedString}</button>
                            </td>
                        </tr>

                        <br>
                        <tr>
                            <td colspan="3" align="center">
                                <button class="crmbutton small edit" onclick="MailManager.mail_reply_send(this.form);">{'LBL_Send'|@getTranslatedString}</button>&nbsp;
                                <button class="crmbutton small edit"
                                        onclick="MailManager.save_draft(this.form)">
					{'LBL_SAVE_NOW'|@getTranslatedString}
                                </button>&nbsp;
                                <button class="crmbutton small edit"
                                        onclick="window.open('index.php?module=MailManager&action=PopupMailManagerTemplate&subject_id=_mail_replyfrm_subject_&body_id=_mail_replyfrm_body_','emailtemplate','top=100,left=200,height=400,width=500,resizable=yes,scrollbars=yes,menubar=no,addressbar=no,status=yes');">
					{'LBL_SELECT_EMAIL_TEMPLATE'|@getTranslatedString}
                                </button>
                            </td>
                        </tr>
                        <tr valign=top>
                            <td width="100%" colspan="3">
                                <textarea name="body" id="_mail_replyfrm_body_" rows="20" class="detailedViewTextBox" style="height: 100%;"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" align="center">
                                <button class="crmbutton small edit" onclick="MailManager.mail_reply_send(this.form);">{'LBL_Send'|@getTranslatedString}</button>&nbsp;
                                <button class="crmbutton small edit"
                                        onclick="MailManager.save_draft(this.form)">
					{'LBL_SAVE_NOW'|@getTranslatedString}
                                </button>&nbsp;
                                <button class="crmbutton small edit"
                                        onclick="window.open('index.php?module=MailManager&action=PopupMailManagerTemplate&subject_id=_mail_replyfrm_subject_&body_id=_mail_replyfrm_body_','emailtemplate','top=100,left=200,height=400,width=500,resizable=yes,scrollbars=yes,menubar=no,addressbar=no,status=yes');">
					{'LBL_SELECT_EMAIL_TEMPLATE'|@getTranslatedString}
                                </button>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </form>
{/strip}
</div>