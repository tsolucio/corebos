{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  coreBOS Open Source
 * The Initial Developer of the Original Code is coreBOS
 * All Rights Reserved.
 ********************************************************************************/
-->*}
{if $showDesert}
	{assign var='DESERTInfo' value='LBL_NO_DATA'|@getTranslatedString:$MODULE}
	{include file='Components/Desert.tpl'}
{else}
	<link rel="stylesheet" type="text/css" href="Smarty/templates/Components/Wizard/wizard.css">
	{if !$isModal}
	<script src="include/js/wizard.js"></script>
	{/if}
	<script type="text/javascript">
		wizard.Context = {};
		wizard.CheckedRows = [];
		wizard.MainSelectedId = 0;
		wizard.steps = {$wizardTotal};
		wizard.MCModule = '{$formodule}';
		wizard.GroupByField = '{$GroupBy}';
		wizard.Operation = '{$wizardOperation}';
		wizard.isModal = {$isModal};
		wizard.gridInstance = '{$gridInstance}';
		wizard.RecordID = '{$RecordID}';
		wizard.SubWizardInfoMainId = '{$SubWizardInfo}';
		wizard.IsDuplicatedFromProduct = [];
		wizard.DeleteSession().then(function() {
			window.addEventListener('DOMContentLoaded', () => {
				wizard.Init();
				setTimeout(function() {
					wizard.Hide();
				}, 200);
			});
		});
		var isWizardInit = false;
		window.addEventListener('onWizardModal', (e) => {
			wizard.ActiveStep = 0;
			wizard.ProceedToNextStep = e.detail.ProceedToNextStep;
			if (!isWizardInit) {
				wizard.DeleteSession().then(function() {
					wizard.Init();
					let hideStep = 1;
					if (!e.detail.ProceedToNextStep) {
						hideStep = 0;
					}
					setTimeout(function() {
						wizard.Hide(hideStep);
					}, 400);
				});
				isWizardInit = true;
			}
		});
	</script>
	<div id="loader" class="loading style-2">
		<div class="loading-wheel"></div>
	</div>
	{if $wizardTitle!=''}
		<span class="slds-page-header__title slds-truncate" title="{$wizardTitle}" id="wizard-title">{$wizardTitle}</span>
	{/if}
	<div class="slds-path">
		<div class="slds-grid slds-path__track">
			<div class="slds-grid slds-path__scroller-container">
				<div class="slds-path__scroller">
					<div class="slds-path__scroller_inner">
						<div id="wizard-steps">
							<ul class="slds-path__nav" role="listbox" aria-orientation="horizontal">
								{foreach from=$wizardSteps item=step name=stepwizard}
								{assign var="slds_active" value=""}
								{assign var="currentStep" value=$smarty.foreach.stepwizard.index}
								{if $smarty.foreach.stepwizard.index eq 0}
									{assign var="slds_active" value="slds-is-active"}
								{/if}
								{if $wizardInstantShow}
								<li class="slds-path__item slds-is-incomplete {$slds_active}" role="presentation" id="header-{$smarty.foreach.stepwizard.index}" onclick="wizard.GoTo({$currentStep})">
								{else}
								<li class="slds-path__item slds-is-incomplete {$slds_active}" role="presentation" id="header-{$smarty.foreach.stepwizard.index}">
								{/if}
									<a aria-selected="false" class="slds-path__link" href="#" id="path-11" role="option" tabindex="-1">
										<span class="slds-path__title">{$step.title}</span>
									</a>
								</li>
								{/foreach}
							</ul>
						</div>
						{foreach from=$wizardSteps item=step name=stepwizard}
						<article class="slds-setup-assistant__step slds-m-around_medium" id="seq-{$smarty.foreach.stepwizard.index}" style="margin-top: 0% !important">
							<div class="slds-setup-assistant__step-summary">
								<div class="slds-media">
									<div class="slds-media__body slds-m-top_x-small">
										<div class="slds-media">
											<div class="slds-setup-assistant__step-summary-content slds-media__body">
												{if $step.filter}
												<button class="slds-button slds-button_neutral" onclick="wizard.ClearFilter({$smarty.foreach.stepwizard.index})" style="float: right;">{'LBL_CLEAR'|@getTranslatedString}</button>
												{/if}
												{if in_array('delete', $step.actions)}
												<button class="slds-button slds-button_neutral" onclick="wizard.DeleteRowFromGrid({$smarty.foreach.stepwizard.index})" style="float: right;">{'LNK_REMOVE'|@getTranslatedString}</button>
												{/if}
												{if $step.description neq ''}
												<div class="slds-text-heading_small">
													<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
														<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info"></use>
													</svg>
													{$step.description}
												</div>
												{/if}
												<div class="slds-m-top_large">
												<script type="text/javascript">
													wizard.ApplyFilter[{$smarty.foreach.stepwizard.index}] = '{$step.filter}';
												</script>
												{$wizardViews[$smarty.foreach.stepwizard.index]}
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</article>
						{/foreach}
					</div>
				</div>
			</div>
		</div>
	</div>
	{if !isset($isWigdet)}
	<div class="slds-path__action">
		<div class="slds-grid slds-path__action wizard-action">
			<button type="button" class="slds-button slds-button_brand slds-path__mark-complete" disabled id="btn-back" data-type="back">
				<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevronleft"></use>
				</svg>
				{'LBL_GOBACK_BUTTON_LABEL'|@getTranslatedString}
			</button>
			<button type="button" class="slds-button slds-button_outline-brand slds-path__mark-complete" id="btn-reset" data-type="reset" onclick="location.reload(true)">
				<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#skip_back"></use>
				</svg>
				{'LBL_RESET'|@getTranslatedString} {'LBL_WIZARD'|@getTranslatedString}
			</button>
			<button type="button" class="slds-button slds-button_brand slds-path__mark-complete slds-float_right" id="btn-next" data-type="next">
				{'LBL_NEXT_BUTTON_LABEL'|@getTranslatedString}
				<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevronright"></use>
				</svg>
			</button>
			<div id="save-btn" class="slds-float_right"></div>
		</div>
	</div>
	{/if}
{/if}