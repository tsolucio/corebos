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
		<td valign="top">
			<div class="forceRelatedListSingleContainer">
				<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
					<div class="slds-card__header slds-grid">
						<!-- Title -->
						<header class="slds-media slds-media--center slds-has-flexi-truncate">
							<div class="slds-media__body">
								<h2>
									<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
										<strong>{$MOD.LBL_TASKS}</strong>
									</span>
								</h2>
							</div>
						</header>
						<!-- Button -->
						<div class="slds-no-flex">
							<input type="button" class="slds-button slds-button_success slds-button--small" value="{$MOD.LBL_NEW_TASK_BUTTON_LABEL}" id='new_task' style="display:none;" />
						</div>
					</div>
				</article>
			</div>
			<!-- Task Summary list table -->
			<div class="slds-truncate">
				{include file='com_vtiger_workflow/TaskSummaryList.tpl'}
			</div>
		</td>
	</tr>
</table>