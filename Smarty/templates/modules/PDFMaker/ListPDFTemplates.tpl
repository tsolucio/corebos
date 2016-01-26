{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
   *
 ********************************************************************************/
-->*}
{* ITS4YOU TT0093 VlMe N *}
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<script>
function ExportTemplates()
{ldelim}
	if(typeof(document.massdelete.selected_id) == 'undefined')
		return false;
        x = document.massdelete.selected_id.length;
        idstring = "";

        if ( x == undefined)
        {ldelim}

                if (document.massdelete.selected_id.checked)
                {ldelim}
                        idstring = document.massdelete.selected_id.value;
                        
                        window.location.href = "index.php?module=PDFMaker&action=PDFMakerAjax&file=ExportPDFTemplate&templates="+idstring;
		     	xx=1;
                {rdelim}
                else
                {ldelim}
                        alert("{$APP.SELECT_ATLEAST_ONE}");
                        return false;
                {rdelim}
        {rdelim}
        else
        {ldelim}
                xx = 0;
                for(i = 0; i < x ; i++)
                {ldelim}
                        if(document.massdelete.selected_id[i].checked)
                        {ldelim}
                                idstring = document.massdelete.selected_id[i].value +";"+idstring
                        xx++
                        {rdelim}
                {rdelim}
                if (xx != 0)
                {ldelim}
                        document.massdelete.idlist.value=idstring;
                        
                        window.location.href = "index.php?module=PDFMaker&action=PDFMakerAjax&file=ExportPDFTemplate&templates="+idstring;
                {rdelim}
                else
                {ldelim}
                        alert("{$APP.SELECT_ATLEAST_ONE}");
                        return false;
                {rdelim}
       {rdelim}

{rdelim}
</script>

{include file='modules/PDFMaker/Buttons_List.tpl'}
<table border="0" cellpadding="0" cellspacing="0" width="98%">   
<tr>
    {*<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>*}
    <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
    <form  name="massdelete" method="POST" onsubmit="VtigerJS_DialogBox.block();">
    <input name="idlist" type="hidden">
    <input name="module" type="hidden" value="PDFMaker">
    <input name="parenttab" type="hidden" value="Tools">
    <input name="action" type="hidden" value="">

    <table border=0 cellspacing=0 cellpadding=0 width="100%" >
    <tr><td>
        <table border=0 cellspacing=0 cellpadding=5 width=100% class="listTableTopButtons">
        <tr>
			<td align="right">
            <input type="button" value="{$MOD.PDFMakerManual}" class="crmbutton small save" title="{$MOD.PDFMakerManual}" onclick="window.location.href='http://www.its4you.sk/images/pdf_maker/pdfmakerfree-for-vtigercrm.pdf'" />&nbsp;
            </td>		
        </tr>
        </table>
        
        <table border=0 cellspacing=0 cellpadding=5 width=100% class="listTable">
        <tr>
            <td width=2% class="colHeader small">#</td>
            <td width=20% class="colHeader small">{$MOD.LBL_MODULENAMES}</td>
        </tr>
        {foreach item=template name=mailmerge from=$PDFTEMPLATES}
        <tr>
            <td class="listTableRow small" valign=top>{$smarty.foreach.mailmerge.iteration}</td>
            <td class="listTableRow small" valign=top>{$template.module}</a></td>
        </tr>
        {/foreach}
        </table>
        </form>
    </td>
    </tr>
    <tr><td align="center" class="small" style="color: rgb(153, 153, 153);">{$MOD.PDF_MAKER} {$VERSION} {$MOD.COPYRIGHT}</td></tr>
    </table>

</td></tr></table>
