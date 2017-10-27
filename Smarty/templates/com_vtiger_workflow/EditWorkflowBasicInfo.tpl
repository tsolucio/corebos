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
<!-- Summary Container -->
<table class="slds-table slds-no-row-hover tableHeading" style="background-color: #fff;">
	<tr class="blockStyleCss">
		<td class="detailViewContainer" valign="top">
			<!-- Summary and Buttons (Save Template, Save, Cancel) -->
			<div class="forceRelatedListSingleContainer">
				<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
					<div class="slds-card__header slds-grid">
						<!-- Summary -->
						<header class="slds-media slds-media--center slds-has-flexi-truncate">
							<div class="slds-media__body">
								<h2>
									<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
										<strong>{$MOD.LBL_SUMMARY}</strong>
									</span>
								</h2>
							</div>
						</header>
						<!-- Buttons -->
						<div class="slds-no-flex">
							{if $saveType eq "edit"}
							<input type="button" class="slds-button slds-button--small slds-button_success" value="{$MOD.LBL_NEW_TEMPLATE}" id="new_template"/>
							{/if}
							<input type="submit" id="save_submit" value="{$APP.LBL_SAVE_LABEL}" class="slds-button slds-button--small slds-button_success" style="display:none;">
							<input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="slds-button slds-button--small slds-button--destructive" onclick="window.location.href='index.php?module=com_vtiger_workflow&action=workflowlist&parenttab=Settings'">
						</div>
					</div>
				</article>
			</div>
			<!-- Description & Module -->
			<div class="slds-truncate">
				<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table">
					<!-- Description Label and field -->
					<tr>
						<td class="dvtCellLabel" align=right width=20%><b>{$APP.LBL_UPD_DESC}<span style='color:red;'>*</span></b></td>
						<td class="dvtCellInfo" align="left"><input type="text" class="slds-input" name="description" id="save_description" value="{$workflow->description}"{if $workflow->executionConditionAsLabel() eq 'MANUAL'} readonly{/if}></td>
					</tr>
					<!-- Module Label and Name -->
					<tr>
						<td class="dvtCellLabel" align=right width=20%><b>{$APP.LBL_MODULE}</b></td>
						<td class="dvtCellInfo" align="left">{$workflow->moduleName|@getTranslatedString:$workflow->moduleName}</td>
					</tr>
				</table>
			</div>

		</td>
	</tr>
</table>
