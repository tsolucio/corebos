{strip}
<form method="POST" action="javascript:void(0);" style="display:inline;">
<table border="0" cellspacing="0" cellpadding="0" width="90%" class="mailClient mailClientBg">
<tr>
<td>
	<table border="0" cellspacing="0" cellpadding="0" width="100%" class="small">
	<tr>
		<td class="mailSubHeader moduleName" background="themes/images/qcBg.gif">
			<b>{'LBL_Search'|getTranslatedString}</b>
		</td>
		<td class="mailSubHeader" background="themes/images/qcBg.gif" align="right">
			<img src="{'close.gif'|vtiger_imageurl:$THEME}" class="mm_clickable" border=0 onclick="MailManager.popup_close();">
		</td>
	</tr>
	</table>
	<table border="0" cellspacing="0" cellpadding="5" width="100%" class="small">
	<tr>
		<td class="dvtCellLabel" nowrap="nowrap">
			<input type="text" id="_search_popupui_input_" name="_search_popupui_input_">
			<input type="hidden" id="_search_popupui_target_" name="_search_popupui_target_">
		</td>
	</tr>
	<tr>
		<td align=center>
			<input type="button" class="crmbutton small save" value="{'LBL_ADD_ITEM'|getTranslatedString}" onclick="MailManager.search_consume_input(this.form);">
			<input type="button" class="crmbutton small cancel" value="{'LBL_Cancel'|getTranslatedString}" onclick="MailManager.popup_close();">
		</td>
	</tr>
	</table>
</td>
</tr>
</table>

</form>

{/strip}