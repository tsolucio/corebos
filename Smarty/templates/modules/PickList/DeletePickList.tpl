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
<div style="position:relative;display: block;z-index: 999;" class="layerPopup">
	<table class="slds-table slds-no-row-hover layerHeadingULine">
		<tr class="slds-text-title--header">
			<th scope="col">
				<div class="layerPopupHeading slds-truncate moduleName">
					{$MOD.DELETE_PICKLIST_VALUES} - {$FIELDLABEL}
				</div>
			</th>
		</tr>
	</table>

	<table class="slds-table slds-no-row-hover">
		<tr>
			<td class="dvtCellInfo" valign=top align=left colspan="2">
				<select id="delete_availPickList" multiple="multiple" class="slds-select" size="5" name="availList">
					{foreach item=pick_val key=pick_key from=$PICKVAL}
						<option value="{$pick_key}">{$pick_val}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td class="dvtCellLabel">
				<b>{$MOD.LBL_REPLACE_WITH}</b>
			</td>
			<td class="dvtCellInfo">
				<select id="replace_picklistval" name="replaceList" class="slds-select">
					<option value=""></option>
					{foreach item=pick_val key=pick_key from=$PICKVAL}
						<option value="{$pick_key}">{$pick_val}</option>
					{/foreach}
					{foreach item=nonedit from=$NONEDITPICKLIST}
						<option value="{$nonedit}">{$nonedit}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2" valign=top align=center>
				<input type="button" value="{$APP.LBL_DELETE_BUTTON_LABEL}" name="del" class="slds-button slds-button--small slds-button--destructive" onclick="validateDelete('{$FIELDNAME}','{$MODULE}');">
				<input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" name="cancel" class="slds-button slds-button--small slds-button--brand" onclick="fnhide('actiondiv');">
			</td>
		</tr>

		{if is_array($NONEDITPICKLIST)}
		<tr>
			<td colspan=3>
				<table border=0 cellspacing=0 cellpadding=0 width=100%>
					<tr><td><b>{$MOD.LBL_NON_EDITABLE_PICKLIST_ENTRIES} :</b></td></tr>
					<tr><td>
					<select id="nonEditablePicklistVal" name="nonEditablePicklistVal" multiple="multiple" wrap size="5" style="width: 100%">
					{foreach item=nonedit from=$NONEDITPICKLIST}
						<option value="{$nonedit}" disabled>{$nonedit}</option>
					{/foreach}
					</select>
				</table>
			</td>
		</tr>
		{/if}
	</table>
</div>
