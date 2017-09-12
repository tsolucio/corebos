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

<form id="UnifiedSearch_moduleform" name="UnifiedSearch_moduleform">
	<table width="90%" cellspacing="0" cellpadding="0" border="0" align="center" class="mailClient mailClientBg">
	<tr>
		<td>
			<table class="slds-table slds-table-no-row-hover slds-table--bordered">
				<thead>
					<tr class="slds-text-title--header">
						<th scope="col">
							<div class="slds-truncate moduleName">
								<b>{$APP.LBL_SELECT_MODULES_FOR_SEARCH}</b>
							</div>
						</th>
						<th scope="col" style="padding: .5rem; text-align: right;">
							<div class="slds-truncate">
								<a href='javascript:void(0);' onclick="UnifiedSearch_SelectModuleToggle(true);">{$APP.LBL_SELECT_ALL}</a> |
								<a href='javascript:void(0);' onclick="UnifiedSearch_SelectModuleToggle(false);">{$APP.LBL_UNSELECT_ALL}</a>
								&nbsp;
								<a href='javascript:void(0)' onclick="UnifiedSearch_SelectModuleCancel();">
									<img src="{'close.gif'|@vtiger_imageurl:$THEME}" style="vertical-align:middle; width: 10px;">
								</a>
							</div>
						</th>
					</tr>
				</thead>
			</table>
			<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz">
				{foreach item=SEARCH_MODULEINFO key=SEARCH_MODULENAME from=$ALLOWED_MODULES name=allowed_modulesloop}
				{if $smarty.foreach.allowed_modulesloop.index % 3 == 0}
				<tr class="slds-line-height--reset" valign=top>
				{/if}
					<td class="dvtCellLabel text-left" style="padding: 0 .5rem;">
						<span class="slds-checkbox search-dropdown" style="display: ruby;">
							<input type='checkbox' name='search_onlyin' id="tag_{$SEARCH_MODULEINFO.label}" value='{$SEARCH_MODULENAME}'
							{if $SEARCH_MODULEINFO.selected}checked=true{/if}>
							<label class="slds-checkbox__label" for="tag_{$SEARCH_MODULEINFO.label}" style="margin-top: 5px;">
								<span class="slds-checkbox--faux"></span>
								<span class="slds-form-element__label">{$SEARCH_MODULEINFO.label}</span>
							</label>
						</span>
					</td>
				{if $smarty.foreach.allowed_modulesloop.index % 3 == 2}
				</tr>
				{/if}
				{/foreach}
			</table>
		</td>
	</tr>
	<tr class="slds-line-height--reset">
		<td align="center" style="padding: .5rem;">
			<input type='button' class='slds-button slds-button--small slds-button_success' value='{$APP.LBL_APPLY_BUTTON_LABEL}' onclick='UnifiedSearch_SelectModuleSave();'>
			<input type='button' class='slds-button slds-button--small slds-button--destructive' value='{$APP.LBL_CANCEL_BUTTON_LABEL}' onclick='UnifiedSearch_SelectModuleCancel();'>
		</td>
	</tr>
	</table>
</form>
