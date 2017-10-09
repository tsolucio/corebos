{*
<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
 ********************************************************************************/
-->*}
<table>
	<tr>
		<td class="dvtCellLabel text-left" width="40%">
			<b>{$MOD.LBL_SELECT_ROLES}</b>
		</td>
	</tr>
	<tr>
		<td class="dvtCellInfo">
			<select multiple id="roleselect" name="roleselect" class="slds-select crmFormList" style="overflow:auto; height: 150px;">
				{foreach item=rolename key=roleid from=$ROLES}
				<option value="{$roleid}">{$rolename}</option>
				{/foreach}
			</select>
		</td>
	</tr>
</table>