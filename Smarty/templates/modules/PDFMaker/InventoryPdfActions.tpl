{if $ENABLE_PDFMAKER eq 'true'}
<table border=0 cellspacing=0 cellpadding=0 style="width:100%;">
{if $TEMPLATE_LANGUAGES|@sizeof > 1}
	<tr>
		<td class="rightMailMergeContent"  style="width:100%;">    	
	    <select name="template_language" id="template_language" class="detailedViewTextBox" style="width:90%;" size="1">
			{html_options  options=$TEMPLATE_LANGUAGES selected=$CURRENT_LANGUAGE}
	    </select>
		</td>
	</tr>
{else}
	{foreach from="$TEMPLATE_LANGUAGES" item="lang" key="lang_key"}
    	<input type="hidden" name="template_language" id="template_language" value="{$lang_key}"/>
	{/foreach}
{/if}
		
<tr>
	<td class="rightMailMergeContent"  style="width:100%;">   		    
	    <a href="javascript:;" onclick="document.location.href='index.php?module=PDFMaker&relmodule={$MODULE}&action=CreatePDFFromTemplate&record={$ID}&language='+document.getElementById('template_language').value;" class="webMnu"><img src="{'actionGeneratePDF.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle" border="0"/></a>
	    <a href="javascript:;" onclick="document.location.href='index.php?module=PDFMaker&relmodule={$MODULE}&action=CreatePDFFromTemplate&record={$ID}&language='+document.getElementById('template_language').value;" class="webMnu">{$APP.LBL_EXPORT_TO_PDF}</a>
	</td>
</tr>

<tr>
  	<td class="rightMailMergeContent"  style="width:100%;">  			
		<a href="javascript:;" onclick="fnvshobj(this,'sendpdfmail_cont');sendPDFmail('{$MODULE}','{$ID}');" class="webMnu"><img src="{'PDFMail.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle" border="0"/></a>
		<a href="javascript:;" onclick="fnvshobj(this,'sendpdfmail_cont');sendPDFmail('{$MODULE}','{$ID}');" class="webMnu">{$APP.LBL_SEND_EMAIL_PDF}</a>  
        <div id="sendpdfmail_cont" style="z-index:100001;position:absolute;"></div>
    </td>
</tr>

<tr>
    <td class="rightMailMergeContent"  style="width:100%;">
        <a href="javascript:;" onclick="getPDFBreaklineDiv(this,'{$ID}');" class="webMnu"><img src="modules/PDFMaker/img/PDF_bl.png" hspace="5" align="absmiddle" border="0"/></a>
        <a href="javascript:;" onclick="getPDFBreaklineDiv(this,'{$ID}');" class="webMnu">{$MOD.LBL_PRODUCT_BREAKLINE}</a>                
        <div id="PDFBreaklineDiv" style="display:none; width:350px; position:absolute;" class="layerPopup"></div>                
    </td>
</tr>

<tr>
    <td class="rightMailMergeContent"  style="width:100%;">
        <a href="javascript:;" onclick="getPDFImagesDiv(this,'{$ID}');" class="webMnu"><img src="modules/PDFMaker/img/PDF_img.png" hspace="5" align="absmiddle" border="0"/></a>
        <a href="javascript:;" onclick="getPDFImagesDiv(this,'{$ID}');" class="webMnu">{$MOD.LBL_PRODUCT_IMAGE}</a>                
        <div id="PDFImagesDiv" style="display:none; width:350px; position:absolute;" class="layerPopup"></div>                
    </td>
</tr>
</table>

{/if}