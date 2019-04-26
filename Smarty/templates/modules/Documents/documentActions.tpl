<table width="100%" border="0" cellpadding="5" cellspacing="0" class="detailview_actionlinks actionlinks_events_todo">
	<!-- Start: Actions for Documents Module -->
	<tr class="actionlink actionlink_downloaddocument"><td align="left" style="padding-left:10px;">
		{if $DLD_TYPE eq 'I' && $FILE_STATUS eq '1' && $FILE_EXIST eq 'yes'}
			<br><a href="index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=downloadfile&fileid={$FILEID}&entityid={$NOTESID}" onclick="javascript:dldCntIncrease({$NOTESID});" class="webMnu"><img src="{'fbDownload.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle" title="{$MOD.LNK_DOWNLOAD}" border="0"/></a>
			<a href="index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=downloadfile&fileid={$FILEID}&entityid={$NOTESID}" onclick="javascript:dldCntIncrease({$NOTESID});">{$MOD.LBL_DOWNLOAD_FILE}</a>
		{elseif $DLD_TYPE eq 'E' && $FILE_STATUS eq '1'}
			<br><a target="_blank" href="{$DLD_PATH}" onclick="javascript:dldCntIncrease({$NOTESID});"><img src="{'fbDownload.gif'|@vtiger_imageurl:$THEME}"" align="absmiddle" title="{$MOD.LNK_DOWNLOAD}" border="0"></a>
			<a target="_blank" href="{$DLD_PATH}" onclick="javascript:dldCntIncrease({$NOTESID});">{$MOD.LBL_DOWNLOAD_FILE}</a>
		{/if}
	</td></tr>
	{if $CHECK_INTEGRITY_PERMISSION eq 'yes'}
		<tr class="actionlink actionlink_checkdocinteg"><td align="left" style="padding-left:10px;">
			<br><a href="javascript:;" onClick="checkFileIntegrityDetailView({$NOTESID});"><img id="CheckIntegrity_img_id" src="{'yes.gif'|@vtiger_imageurl:$THEME}" alt="Check integrity of this file" title="Check integrity of this file" hspace="5" align="absmiddle" border="0"/></a>
			<a href="javascript:;" onClick="checkFileIntegrityDetailView({$NOTESID});">{$MOD.LBL_CHECK_INTEGRITY}</a>&nbsp;
			<input type="hidden" id="dldfilename" name="dldfilename" value="{$FILEID}-{$FILENAME}">
			<span id="vtbusy_integrity_info" style="display:none;"><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<span id="integrity_result" style="display:none"></span>
		</td></tr>
	{/if}
	<tr class="actionlink actionlink_emaildocument"><td align="left" style="padding-left:10px;">
		{if $DLD_TYPE eq 'I' &&  $FILE_STATUS eq '1' && $FILE_EXIST eq 'yes'}
			<input type="hidden" id="dldfilename" name="dldfilename" value="{$FILEID}-{$FILENAME}">
			<br><a href="javascript: document.DetailView.return_module.value='Documents'; document.DetailView.return_action.value='DetailView'; document.DetailView.module.value='Documents'; document.DetailView.action.value='EmailFile'; document.DetailView.record.value={$NOTESID}; document.DetailView.return_id.value={$NOTESID}; sendfile_email();" class="webMnu"><img src="{'attachment.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle" border="0"/></a>
			<a href="javascript: document.DetailView.return_module.value='Documents'; document.DetailView.return_action.value='DetailView'; document.DetailView.module.value='Documents'; document.DetailView.action.value='EmailFile'; document.DetailView.record.value={$NOTESID}; document.DetailView.return_id.value={$NOTESID}; sendfile_email();">{$MOD.LBL_EMAIL_FILE}</a>
		{/if}
	</td></tr>
	<tr><td>&nbsp;</td></tr>
</table>