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
<script src="include/js/operation.js"></script>
<ol class="slds-setup-assistant">
	<li class="slds-setup-assistant__item">
		<article class="slds-setup-assistant__step">
			<div class="slds-setup-assistant__step-summary">
				<div class="slds-media">
					<div class="slds-media__figure">
						<div class="slds-progress-ring slds-progress-ring_large">
							<div class="slds-progress-ring__progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
								<svg viewBox="-1 -1 2 2">
									<path class="slds-progress-ring__path" id="slds-progress-ring-path-102" d="M 1 0 A 1 1 0 0 0 1.00 -0.00 L 0 0"></path>
								</svg>
							</div>
							<div class="slds-progress-ring__content">1</div>
						</div>
					</div>
					<div class="slds-media__body slds-m-top_x-small">
						<div class="slds-media">
							<div class="slds-setup-assistant__step-summary-content slds-media__body">
								<h3 class="slds-setup-assistant__step-summary-title slds-text-heading_small">
									Filters
								</h3>
							</div>
							<div>
								{foreach from=$filters key=k item=i}
									<button class="slds-button slds-button_brand" onclick="operation.Filter({$i.record_id}, '{$i.qmodule}')">
										<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#filter"></use>
										</svg>
										{$i.btnName}
									</button>
								{/foreach}
							</div>
						</div>
					</div>
				</div>
			</div>
		</article>
	</li>
	<li class="slds-setup-assistant__item">
		<article class="slds-setup-assistant__step">
			<div class="slds-setup-assistant__step-summary">
				<div class="slds-media">
					<div class="slds-media__figure">
						<div class="slds-progress-ring slds-progress-ring_large">
							<div class="slds-progress-ring__progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
								<svg viewBox="-1 -1 2 2">
									<path class="slds-progress-ring__path" id="slds-progress-ring-path-102" d="M 1 0 A 1 1 0 0 0 1.00 -0.00 L 0 0"></path>
								</svg>
							</div>
							<div class="slds-progress-ring__content">2</div>
						</div>
					</div>
					<div class="slds-media__body slds-m-top_x-small">
						<div class="slds-media">
							<div class="slds-setup-assistant__step-summary-content slds-media__body">
								<h3 class="slds-setup-assistant__step-summary-title slds-text-heading_small">
									ListView
								</h3>
								<div id="listview-content">
									<div class="slds-scoped-notification slds-media slds-media_center slds-scoped-notification_light" role="status">
										<div class="slds-media__figure">
											<span class="slds-icon_container slds-icon-utility-info">
												<svg class="slds-icon slds-icon_small slds-icon-text-default" aria-hidden="true">
													<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info"></use>
												</svg>
												<span class="slds-assistive-text">information</span>
											</span>
										</div>
										<div class="slds-media__body">
											<p>No Data. Please apply the filter.</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</article>
	</li>
	<li class="slds-setup-assistant__item">
		<article class="slds-setup-assistant__step">
			<div class="slds-setup-assistant__step-summary">
				<div class="slds-media">
					<div class="slds-media__figure">
						<div class="slds-progress-ring slds-progress-ring_large">
							<div class="slds-progress-ring__progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
								<svg viewBox="-1 -1 2 2">
									<path class="slds-progress-ring__path" id="slds-progress-ring-path-102" d="M 1 0 A 1 1 0 0 0 1.00 -0.00 L 0 0"></path>
								</svg>
							</div>
							<div class="slds-progress-ring__content">3</div>
						</div>
					</div>
					<div class="slds-media__body slds-m-top_x-small">
						<div class="slds-media">
							<div class="slds-setup-assistant__step-summary-content slds-media__body">
								<h3 class="slds-setup-assistant__step-summary-title slds-text-heading_small">Actions</h3>
							</div>
							<div>
								{foreach from=$actions key=k item=i}
									<button class="slds-button slds-button_success" onclick="{$i.functionName}" style="color: white">
										<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#touch_action"></use>
										</svg>
										{$i.btnName}
									</button>
								{/foreach}
							</div>
						</div>
					</div>
				</div>
			</div>
		</article>
	</li>
</ol>
{/if}