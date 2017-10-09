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
					{$MOD.ASSIGN_PICKLIST_VALUES} - {$FIELDLABEL}
				</div>
			</th>
		</tr>
	</table>
	<br/>
	<table class="slds-table slds-no-row-hover" id="assignPicklistTable">
		<thead>
			<tr>
				<td class="dvtCellLabel text-left" width="40%">
					<b>{$MOD.LBL_PICKLIST_VALUES}</b>
				</td>
				<td width="5%"></td>
				<td class="dvtCellLabel text-left" width="40%">
					<b>{$MOD.LBL_PICKLIST_VALUES_ASSIGNED_TO} {$ROLENAME}</b>
				</td>
				<td width="5%"></td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="dvtCellInfo" width="40%">
					<select multiple id="availList" name="availList" class="slds-select crmFormList" style="overflow:auto; height: 150px;">
						{foreach item=pick_val key=pick_key from=$PICKVAL}
							<option value="{$pick_key}">{$pick_val}</option>
						{/foreach}
					</select>
				</td>
				<td width="5%">
					<img border="0" title="Add" alt="Move Right" onclick="moveRight();" src="{'arrow_right.png'|@vtiger_imageurl:$THEME}"/>
					<br/>
					<br/>
					<img border="0" title="Remove" alt="Remove" onclick="removeValue();" src="{'arrow_left.png'|@vtiger_imageurl:$THEME}"/>
				</td>
				<td class="dvtCellInfo" width="40%">
					<select multiple id="selectedColumns" name="selectedColumns" class="slds-select crmFormList" style="overflow:auto; height: 150px;">
						{foreach item=val key=key from=$ASSIGNED_VALUES}
							<option value="{$key}">{$val}</option>
						{/foreach}
					</select>
				</td>
				<td width="5%">
					<img border="0" title="Move Up" alt="Move Up" onclick="moveUp();" src="{'arrow_up.png'|@vtiger_imageurl:$THEME}"/>
					<br/>
					<br/>
					<img border="0" title="Move Down" alt="Move Down" onclick="moveDown();" src="{'arrow_down.png'|@vtiger_imageurl:$THEME}"/>
				</td>
			</tr>
			<tr>
				<td>
					<a href='javascript:;' onclick="showRoleSelectDiv('{$ROLEID}')" id="addRolesLink">
						<b>{$MOD.LBL_ADD_TO_OTHER_ROLES}</b>
					</a>
				</td>
				<td colspan="3" align="center" nowrap>
					<input type="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" name="save" class="slds-button slds-button--small slds-button_success" onclick="saveAssignedValues('{$MODULE}','{$FIELDNAME}','{$ROLEID}');">
					<input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" name="cancel" class="slds-button slds-button--small slds-button--destructive" onclick="fnhide('actiondiv');">
				</td>
			</tr>
		</tbody>
	</table>
</div>
