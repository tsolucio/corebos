{*<!--
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}

<div class="slds-page-header">
<div class="slds-page-header__row">
	<div class="slds-page-header__col-title">
	<div class="slds-media">
		<div class="slds-media__body">
		<div class="slds-page-header__name">
			<div class="slds-page-header__name-title">
			<h1>
				<span class="slds-page-header__title slds-truncate" title="{$MOD.LBL_TASKS}">
				{$MOD.LBL_TASKS}
				</span>
			</h1>
			</div>
		</div>
		</div>
	</div>
	</div>
	<div class="slds-page-header__col-actions">
		<div class="slds-grid slds-gutters slds-m-around_xxx-small">
			<div class="slds-col">
				<button class="slds-button slds-button_success" type="button" onclick="gotourl('{$module->activatedeactivateTaskUrl($workflow->id,1)}')">
					{$MOD.LBL_ACTIVATE_ALL_BUTTON_LABEL}
				</button>
				<button class="slds-button slds-button_success" type="button" onclick="gotourl('{$module->activatedeactivateTaskUrl($workflow->id,0)}')">
					{$MOD.LBL_DIACTIVATE_ALL_BUTTON_LABEL}
				</button>
				<button class="slds-button slds-button_brand" type="button" id='new_task' style="display:none;" >
					{$MOD.LBL_NEW_TASK_BUTTON_LABEL}
				</button>
			</div>
		</div>
	</div>
</div>
</div>
{include file='com_vtiger_workflow/TaskSummaryList.tpl'}