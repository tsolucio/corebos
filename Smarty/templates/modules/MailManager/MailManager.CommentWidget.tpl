{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ('License'); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
<div class="mailClient mailClientBg" style="width:700px">
    <form name="commentwidget">
        <table border="0" cellpadding="5" cellspacing="0" width="100%">
        <tbody>
            <tr>
                <td class="mailSubHeader" align="left">
                    <b style="font-size:14px">{'JSLBL_ADD_COMMENT'|@getTranslatedString}</b>
                </td>
                <td class="mailSubHeader" align="right">
                    <a href="javascript:fninvsh('_relationpopupdiv_');"><img src="themes/images/close.gif" align="absmiddle" border="0"></a>
                </td>
            </tr>
        </tbody>
        </table>

        <table align="center" border="0" cellpadding="0" cellspacing="5" width="98%">
        <tbody>
            <tr>
                <td>
                    <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tbody>
                        <tr>
                            <td><textarea cols="90" rows="10" style="width: 685px; height: 100px;" class="detailedViewTextBox small" name="commentcontent"
                                    onblur="this.className='detailedViewTextBox'" onfocus="this.className='detailedViewTextBoxOn'" ></textarea>
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
        </table>

        <table class="layerPopupTransport" border="0" cellpadding="5" cellspacing="0" width="100%">
        <tbody><tr>
                <td align="center">
                    <input class="crmbutton save small" onclick="if(MailManager.addCommentValidate(this.form)) MailManager.mail_associate_create(this.form);" value="{'LBL_SAVE_LABEL'|getTranslatedString}" type="button">&nbsp;&nbsp;
                    <input class="crmbutton cancel small" onclick="fninvsh('_relationpopupdiv_');" value="{'LBL_Cancel'|getTranslatedString}" type="button">
                </td>
            </tr>
        </tbody>
        </table>
        <input type=hidden name="_mlinkto" value="{$PARENT}">
        <input type=hidden name="_mlinktotype" value="{$LINKMODULE}">
        <input type=hidden name="_msgno" value="{$MSGNO}">
        <input type=hidden name="_folder" value="{$FOLDER}">

    </form>
</div>
