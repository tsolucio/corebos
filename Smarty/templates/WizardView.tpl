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
	<script src="include/js/wizard.js"></script>
	<script type="text/javascript">
		var MCModule = '{$formodule}';
		var wizard = new WizardComponent({$wizardTotal});
		wizard.GroupByField = '{$GroupBy}';
		wizard.Operation = '{$wizardOperation}';
		wizard.DeleteSession().then(function() {	
			window.addEventListener('DOMContentLoaded', (event) => {
				wizard.Init();
				setTimeout(function() {
					wizard.Hide();
				}, 200);
			});
		});
	</script>
	<style type="text/css">
	.loading {
		width: 100%;
		height: 100%;
		position: fixed;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		background-color: rgb(0 0 0 / 22%);
		z-index: 1000
	}
	.loading-wheel {
		width: 20px;
		height: 20px;
		margin-top: -40px;
		margin-left: -40px;
		position: absolute;
		top: 50%;
		left: 50%;
		border-width: 30px;
		border-radius: 50%;
		-webkit-animation: spin 1s linear infinite;
	}
	.style-2 .loading-wheel {
		border-style: dashed;
		border-color: #fff transparent;
	}
	@-webkit-keyframes spin {
		0% {
			-webkit-transform: rotate(0);
		}
		100% {
			-webkit-transform: rotate(-360deg);
		}
	}
	</style>
	<div id="loader" class="loading style-2">
		<div class="loading-wheel"></div>
	</div>
	{if $wizardTitle!=''}
	<div class="slds-page-header__name slds-m-bottom_medium">
		<div class="slds-page-header__name-title">
			<span class="slds-page-header__title slds-truncate" title="{$wizardTitle}">{$wizardTitle}</span>
		</div>
	</div>
	{/if}
	<div class="slds-path">
		<div class="slds-grid slds-path__track">
			<div class="slds-grid slds-path__scroller-container">
				<div class="slds-path__scroller">
					<div class="slds-path__scroller_inner">
						<ul class="slds-path__nav" role="listbox" aria-orientation="horizontal">
							{foreach from=$wizardSteps item=step name=stepwizard}
							{assign var="slds_active" value=""}
							{if $smarty.foreach.stepwizard.index eq 0}
								{assign var="slds_active" value="slds-is-active"}
							{/if}
							<li class="slds-path__item slds-is-incomplete {$slds_active}" role="presentation" id="header-{$smarty.foreach.stepwizard.index}">
								<a aria-selected="false" class="slds-path__link" href="#" id="path-11" role="option" tabindex="-1">
									<span class="slds-path__title">{$step.title}</span>
								</a>
							</li>
							{/foreach}
						</ul>
						{foreach from=$wizardSteps item=step name=stepwizard}
						<article class="slds-setup-assistant__step slds-m-around_medium" id="seq-{$smarty.foreach.stepwizard.index}">
							<div class="slds-setup-assistant__step-summary">
								<div class="slds-media">
									<div class="slds-media__body slds-m-top_x-small">
										<div class="slds-media">
											<div class="slds-setup-assistant__step-summary-content slds-media__body">												{if in_array('delete', $step.actions)}
												<button class="slds-button slds-button_neutral" onclick="wizard.DeleteRowFromGrid({$smarty.foreach.stepwizard.index})">Remove</button>
												{/if}
												{if $step.description neq ''}
												<div class="slds-text-heading_small">
													<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
														<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info"></use>
													</svg>
													{$step.description}
												</div>
												{/if}
												{$wizardViews[$smarty.foreach.stepwizard.index]}
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
	{if isset($isWigdet)}
	<div class="slds-grid slds-path__action">
	{else}
	<div class="slds-grid slds-path__action" style="position: fixed;bottom: 0;background: #f3f3f3;padding: 15px;width: 100%;margin-left: -25px;z-index: 900;border-top: 1px solid #1b96ff">
	{/if}
		<button type="button" class="slds-button slds-button_brand slds-path__mark-complete" disabled id="btn-back" data-type="back">
			<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevronleft"></use>
			</svg>
			Back
		</button>
		<button type="button" class="slds-button slds-button_outline-brand slds-path__mark-complete" id="btn-reset" data-type="reset" onclick="location.reload(true)">
			<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#skip_back"></use>
			</svg>
			Reset Wizard
		</button>
		<button type="button" class="slds-button slds-button_brand slds-path__mark-complete" id="btn-next" data-type="next">
			Next
			<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevronright"></use>
			</svg>
		</button>
	</div>
{/if}