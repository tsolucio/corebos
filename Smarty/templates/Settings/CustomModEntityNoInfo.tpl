{*
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/ *}

<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table">
	<tr>
		<td nowrap width="20%" class="small dvtCellLabel text-left">
			<strong>{$SELMODULE|@getTranslatedString:$SELMODULE} {$MOD.LBL_MODULE_NUMBERING}</strong>
		</td>
		<td width="50%" nowrap class="small dvtCellInfo" align=left>
			<b>{$MOD.LBL_MODULE_NUMBERING_FIX_MISSING}</b>
			<input type="button" class="slds-button slds-button--small slds-button--brand" value="{$APP.LBL_APPLY_BUTTON_LABEL}" onclick="updateModEntityExisting(this, this.form);"/>
		</td>
		<td width="30%" class="small dvtCellLabel">
			 <b>{$STATUSMSG}</b>
		</td>
	</tr>
	<tr>
		<td nowrap class="small dvtCellLabel text-left"><strong>{$MOD.LBL_USE_PREFIX}</strong></td>
		<td colspan=2 class="small dvtCellInfo">
		<input type="text" name="recprefix" class="small slds-input" value="{$MODNUM_PREFIX}" />
		</td>
	</tr>
	<tr>
		<td nowrap class="small dvtCellLabel text-left"><strong>{$MOD.LBL_START_SEQ}<font color='red'>*</font></strong></td>
		<td colspan=2 class="small dvtCellInfo">
			<input type="text" name="recnumber" class="small slds-input" value="{$MODNUM}" />
		</td>
	</tr>
	<tr>
		<td width="20%" nowrap colspan="3" align ="center">
			<input type="button" name="Button" class="slds-button slds-button--small slds-button_success" value="{$APP.LBL_SAVE_BUTTON_LABEL}" onclick="updateModEntityNoSetting(this, this.form);" />
			<input type="button" name="Button" class="slds-button slds-button--small slds-button--destructive" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onclick="history.back(-1);" />
		</td>
	</tr>
</table>

