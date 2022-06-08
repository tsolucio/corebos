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
<div id="pickListContents">
<table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
<tr>
	<td class="big cblds-p-v_medium" width="20%" nowrap>
		<strong>{$MOD.LBL_SELECT_PICKLIST}</strong>&nbsp;&nbsp;
	</td>
	<td class="cellText cblds-p-v_medium" width="40%">
		<select name="avail_picklists" id="allpick" class="small detailedViewTextBox" style="font-weight: normal;">
			{foreach key=fld_nam item=fld_lbl from=$ALL_LISTS}
				<option value="{$fld_nam}">{$fld_lbl|getTranslatedString:$MODULE}</option>
			{/foreach}
		</select>
	</td>
	<td nowrap align="right" class="cblds-p-v_medium cblds-t-align_right">
		<input type="button" value="{'LBL_ADD_BUTTON'|@getTranslatedString}" name="add" class="crmButton small create" onclick="showAddDiv();">
		<input type="button" value="{'LBL_EDIT_BUTTON'|@getTranslatedString}" name="del" class="crmButton small edit" onclick="showEditDiv();">
		<input type="button" value="{'LBL_DELETE_BUTTON'|@getTranslatedString}" name="del" class="crmButton small delete" onclick="showDeleteDiv();">
	</td>
</tr>
</table>
<table class="tableHeading" border="0" cellpadding="7" cellspacing="0" width="100%">
<tr>
	<td width="40%">
		<strong>
			{$MOD.LBL_PICKLIST_AVAIL} {$MODULE|@getTranslatedString:$MODULE} {$MOD.LBL_FOR} &nbsp;
		</strong>
		<select name="pickrole" id="pickid" class="detailedViewTextBox" onChange="showPicklistEntries('{$MODULE}' );" style="width : auto;">
			{foreach key=roleid item=role from=$ROLE_LISTS}
				{if $SEL_ROLEID eq $roleid}
					<option value="{$roleid}" selected>{$role}</option>
				{else}
					<option value="{$roleid}">{$role}</option>
				{/if}
			{/foreach}
		</select>
	</td>
</tr>
<tr>
	<td class="small">
		<font color="red">* {$MOD_PICKLIST.LBL_DISPLAYED_VALUES}</font>
	</td>
</tr>
</table>

<table border=0 cellspacing=0 cellpadding=0 width=100% class="listTable">
<tr>
<td valign=top width="50%">
	<table>
	{foreach item=picklists from=$PICKLIST_VALUES}
	<tr>
		{foreach item=picklistfields from=$picklists}
			{if $picklistfields neq ''}
				<td class="listTableTopButtons small" style="padding-left:20px" valign="top" align="left">
					{if !empty($TEMP_MOD[$picklistfields.fieldlabel])}
						<b>{$TEMP_MOD[$picklistfields.fieldlabel]}</b>
					{else}
						<b>{$picklistfields.fieldlabel}</b>
					{/if}
				</td>
				<td class="listTableTopButtons" valign="top" rowspan="2" style="width:200px;">
					<button class="slds-button slds-button_outline-brand slds-m-top_x-small slds-m-left_small" onclick="assignPicklistValues('{$MODULE}','{$picklistfields.fieldname}','{$picklistfields.fieldlabel}');">
						{$MOD_PICKLIST.LBL_ASSIGN_BUTTON}
					</button>
					<div class="slds-form-element slds-m-top_small slds-m-left_small">
						<label class="slds-checkbox_toggle slds-grid">
							<input type="checkbox" name="monoi18n_{$picklistfields.fieldname}" id="monoi18n_{$picklistfields.fieldname}" {if $picklistfields.multii18n}checked{/if}
								onchange="saveMultiLanguage('{$MODULE}', '{$picklistfields.fieldname}', this.checked);"
								aria-describedby="monoi18n_{$picklistfields.fieldname}_desc" />
							<span id="monoi18n_{$picklistfields.fieldname}_desc" class="slds-checkbox_faux_container" aria-live="assertive">
								<span class="slds-checkbox_faux"></span>
								<span class="slds-checkbox_on">{$MOD_PICKLIST.multii18n}</span>
								<span class="slds-checkbox_off">{$MOD_PICKLIST.monoi18n}</span>
						</span>
						</label>
					</div>
					{if !isPicklistValid($picklistfields.fieldname)}
					<div class="slds-form-element slds-m-top_small slds-m-left_small">
						<label class="slds-checkbox_toggle slds-grid slds-text-color_error">{'ERR_InvalidValues'|getTranslatedString:'PickList'}</label>
						<button class="slds-button slds-button_text-destructive slds-m-top_x-small" onclick="fixPicklistValues('{$MODULE}', '{$picklistfields.fieldname}');">
							{'LBL_FIX_NOW'|getTranslatedString:'PickList'}
						</button>
					</div>
					{/if}
				</td>
			{else}
				<td class="listTableTopButtons small" colspan="2">&nbsp;</td>
			{/if}
		{/foreach}
	</tr>
	<tr>
		{foreach item=picklistelements from=$picklists}
			{if $picklistelements neq ''}
				<td colspan="2" valign="top">
				<ul class="slds-m-left_small">
					{foreach item=elements from=$picklistelements.value}
						{if !empty($TEMP_MOD[$elements])}
							<li>{$TEMP_MOD[$elements]}</li>
						{elseif !empty($MOD_PICKLIST[$elements])}
							<li>{$MOD_PICKLIST[$elements]}</li>
						{else}
							<li>{$elements}</li>
						{/if}
					{/foreach}
				</ul>
				</td>
			{else}
				<td colspan="2">&nbsp;</td>
			{/if}
		{/foreach}
	</tr>
	{/foreach}
	</table>
</td>
</tr>
</table>
</div>
