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
		window.addEventListener('DOMContentLoaded', (event) => {
			wizard.Init();
			setTimeout(function() {
				wizard.Hide();
			}, 200);
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
		background-color: rgba(0,0,0,.5);
		z-index: 100
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
		border-style: double;
		border-color: #ccc transparent;
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
	<div id="loader" class="loading style-2" style="display: none">
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
											<div class="slds-setup-assistant__step-summary-content slds-media__body">
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
	<div class="slds-grid slds-path__action" style="float: right;margin-top: 10px">
		<button class="slds-button slds-button_brand slds-path__mark-complete" disabled id="btn-back" data-type="back">
			Back
		</button>
		<button class="slds-button slds-button_brand slds-path__mark-complete" id="btn-next" data-type="next">
			Next
		</button>
	</div>
{/if}