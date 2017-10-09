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
<div style="position:relative;display: block;z-index: 999;" id="orgLay" class="layerPopup">
	<table class="slds-table slds-no-row-hover layerHeadingULine">
		<tr class="slds-text-title--header">
			<th scope="col">
				<div class="layerPopupHeading slds-truncate moduleName">
					{$MOD.ADD_PICKLIST_VALUES} - {$FIELDLABEL}
				</div>
			</th>
		</tr>
	</table>
	<br/>
	<table class="slds-table slds-no-row-hover">
		<tr>
			<td class="dvtCellLabel text-left" valign=top>
				<b>{$MOD.LBL_EXISTING_PICKLIST_VALUES}</b>
			</td>
			<td class="dvtCellLabel text-left" valign=top>
				<b>{$MOD.LBL_PICKLIST_ADDINFO}</b>
			</td>
		</tr>
		<tr>
			<td class="dvtCellInfo" valign=top>
				<div id="add_availPickList" name="availList">
					{foreach item=pick_val from=$PICKVAL}
						<div class="picklist_existing_options">{$pick_val}</div>
					{/foreach}
				</div>
				<br>
				{if is_array($NONEDITPICKLIST)}
					<b>{$MOD.LBL_NON_EDITABLE_PICKLIST_ENTRIES} :</b>
					<div id="nonedit_pl_values" name="availList">
						{foreach item=nonedit from=$NONEDITPICKLIST}
							<div class="picklist_noneditable_options">
								{$nonedit}
							</div>
						{/foreach}
					</div>
				{/if}
			</td>
			<td valign=top style="padding: 0;">
				<table class="slds-table">
					<tr>
						<td class="dvtCellInfo">
							<textarea id="add_picklist_values" class="slds-textarea" align="left" rows="10"></textarea>
						</td>
					</tr>
					<tr>
						<td class="dvtCellLabel text-left">
							<b>{$MOD.LBL_SELECT_ROLES} </b><br />
						</td>
					</tr>
					<tr>
						<td class="dvtCellInfo">
							<select id="add_availRoles" multiple="multiple" wrap size="5" class="slds-select" name="add_availRoles">
								{foreach key=role_id item=role_details from=$ROLEDETAILS}
									<option value="{$role_id}">{$role_details.0}</option>
								{/foreach}
							</select>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" valign=top align=center>
				<input type="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" id="saveAddButton" name="save" class="slds-button slds-button--small slds-button_success" onclick="validateAdd('{$FIELDNAME}','{$MODULE}');">
				<input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" name="cancel" class="slds-button slds-button--small slds-button--destructive" onclick="fnhide('actiondiv');">
			</td>
		</tr>
	</table>
</div>
