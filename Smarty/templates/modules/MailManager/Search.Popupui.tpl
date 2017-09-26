{strip}
<form method="POST" action="javascript:void(0);" style="display:inline;">
	<table border="0" cellspacing="0" cellpadding="0" width="90%" class="mailClient" style="z-index: 9999;background-color: #fff;">
		<tr>
			<td>
				<table class="slds-table slds-no-row-hover">
					<tr class="slds-text-title--header">
						<th scope="col">
							<div class="slds-truncate moduleName">
								<b>{'LBL_Search'|getTranslatedString}</b>
							</div>
						</th>
						<th scope="col" style="padding: .5rem;text-align: right;">
							<div class="slds-truncate">
								<img src="{'close.gif'|vtiger_imageurl:$THEME}" class="mm_clickable" border=0 onclick="MailManager.popup_close();">
							</div>
						</th>
					</tr>
				</table>
				<table class="slds-table slds-no-row-hover slds-table--bordered search-mail-table">
					<tr class="slds-line-height--reset">
						<td class="dvtCellInfo">
							<input type="text" class="slds-input" id="_search_popupui_input_" name="_search_popupui_input_">
							<input type="hidden" id="_search_popupui_target_" name="_search_popupui_target_">
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr class="slds-line-height--reset">
			<td align=center style="padding: .5rem;">
				<input type="button" class="slds-button slds-button--small slds-button_success" value="{'LBL_ADD_ITEM'|getTranslatedString}" onclick="MailManager.search_consume_input(this.form);">
				&nbsp;<input type="button" class="slds-button slds-button--small slds-button--destructive" value="{'LBL_Cancel'|getTranslatedString}" onclick="MailManager.popup_close();">
			</td>
		</tr>
	</table>
</form>
{/strip}