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

<table class="slds-table slds-no-row-hover tableHeading" style="background-color: #fff;">
	<tr class="blockStyleCss">
		<td valign="top" style="padding: 0;">
			<div class="forceRelatedListSingleContainer">
				<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
					<div class="slds-card__header slds-grid">
						<!-- Title -->
						<header class="slds-media slds-media--center slds-has-flexi-truncate">
							<div class="slds-media__body">
								<h2>
									<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
										<strong>{$MOD.LBL_CONDITIONS}</strong>
									</span>
								</h2>
							</div>
						</header>
						<!-- Button -->
						<div class="slds-no-flex">
							<span id="workflow_loading" style="display:none">
							<b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0">
							</span>
							<input type="button" class="slds-button slds-button--small slds-button_success" value="{$MOD.LBL_NEW_CONDITION_GROUP_BUTTON_LABEL}" id="save_conditions_add" style='display: none;'/>
						</div>
					</div>
				</article>
			</div>
			<div class="slds-truncate">
				{if $showreeval eq 'true'}
					<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table">
						<tr>
							<td class="dvtCellInfo" colspan=2>
								<span class="slds-checkbox">
								<input type="checkbox" name="reevaluate" id="reevaluate" {if !$edit || !isset($task->reevaluate) || $task->reevaluate eq 1}checked{/if}>
									<label class="slds-checkbox__label" for="reevaluate">
										<span class="slds-checkbox--faux"></span>
									</label>
									<span class="slds-form-element__label">{$MOD.LBL_REEVALCONDITIONS}</span>
								</span>
							</td>
						</tr>
					</table>
				{/if}
				<div id="save_conditions"></div>
				{include file="com_vtiger_workflow/FieldExpressions.tpl"}
			</div>

		</td>
	</tr>
</table>
