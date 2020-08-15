{*
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/ *}
<table width="100%" class="slds-table slds-table_cell-buffer slds-no-row-hover slds-table_bordered" border="0" cellspacing="0" cellpadding="5">
<tr>
	<td>
	{include file='Components/PageSubTitle.tpl' PAGESUBTITLE=$SELMODULE|@getTranslatedString:$SELMODULE|cat:" "|cat: $MOD.LBL_MODULE_NUMBERING}
	</td>
	<td width="100%" >
		<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
		{$STATUSMSG}
		</label>
	</td>
	<td width="80%" nowrap class="cblds-t-align_right" align=right>
		<b>{$MOD.LBL_MODULE_NUMBERING_FIX_MISSING}</b>
		<input type="button" class="slds-button slds-button_brand create" value="{$APP.LBL_APPLY_BUTTON_LABEL}" onclick="updateModEntityExisting(this, this.form);"/>
	</td>
</tr>
<tr>
	<td width="20%" nowrap >
		<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
		{$MOD.LBL_USE_PREFIX}
		</label>
		</td>
		<td width="40%" colspan=2 >
		<input type="text" name="recprefix" class="slds-input" style="width:40%" value="{$MODNUM_PREFIX}" />
	</td>
</tr>
<tr>
	<td width="20%" nowrap >
		<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="description">
		<abbr class="slds-required" title="Indica un campo obbligatorio">* </abbr>
		{$MOD.LBL_START_SEQ}
		</label>
	</td>
	<td width="80%" colspan=2 >
		<input type="text" name="recnumber" class="slds-input" style="width:40%" value="{$MODNUM}" />
	</td>
</tr>
<tr>
	<td width="20%" nowrap colspan="3" align ="center" class="cblds-t-align_right">
		<button type="button" name="Button" class="slds-button slds-button_success save" onclick="updateModEntityNoSetting(this, this.form);">
		<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true"> <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use> </svg>
		{$APP.LBL_SAVE_BUTTON_LABEL}
		</button>
		</td>
	</td>
</tr>
</table>