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
<div style="position:relative; display:block;z-index: 999;" class="layerPopup">

	<table class="slds-table slds-no-row-hover layerHeadingULine">
		<tr class="slds-text-title--header">
			<th scope="col">
				<div class="layerPopupHeading slds-truncate moduleName">
				{$MOD.EDIT_PICKLIST_VALUE} - {$FIELDLABEL}
				</div>
			</th>
		</tr>
	</table>
	<br/>
	<table class="slds-table slds-no-row-hover">
		<tr>
			<td class="dvtCellLabel text-left" valign=top colspan="2">
				<b>{$MOD.LBL_SELECT_TO_EDIT}</b>
			</td>
		</tr>
		<tr>
			<td class="dvtCellInfo" colspan="2">
				<select id="edit_availPickList" class="slds-select" name="availList" size="5" onchange="selectForEdit();">
					{foreach item=pick_val key=pick_key from=$PICKVAL}
						<option value="{$pick_key}">{$pick_val}</option>
					{/foreach}
				</select>

				{if is_array($NONEDITPICKLIST)}
					<table border=0 cellspacing=0 cellpadding=0 width=100%>
						<tr><td><b>{$MOD.LBL_NON_EDITABLE_PICKLIST_ENTRIES} :</b></td></tr>
						<tr>
							<td>
								<b>
									<div id="nonedit_pl_values">
										{foreach item=nonedit from=$NONEDITPICKLIST}
											<span class="nonEditablePicklistValues">
												{$nonedit}
											</span><br>
										{/foreach}
									</div>
								</b>
							</td>
						</tr>
					</table>
				{/if}

			</td>
		</tr>
		<tr>
			<td class="dvtCellLabel" width="30%">
				<b>{$MOD.LBL_EDIT_HERE}</b>
			</td>
			<td class="dvtCellInfo">
				<input type="text" id="replaceVal" class="slds-input" style="width: 100%" onchange="pushEditedValue(event)"/>
			</td>
		</tr>
		<tr>
			<td colspan="2" valign=top align="center">
				<input type="button" value="{$APP.LBL_APPLY_BUTTON_LABEL}" name="apply" class="slds-button slds-button--small slds-button_success" onclick="validateEdit('{$FIELDNAME}','{$MODULE}');">
				<input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" name="cancel" class="slds-button slds-button--small slds-button--destructive" onclick="fnhide('actiondiv');">
			</td>
		</tr>
	</table>
</div>
