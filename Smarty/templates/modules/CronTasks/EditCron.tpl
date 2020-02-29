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

<section role="dialog" tabindex="-1" class="slds-modal slds-fade-in-open slds-modal_small" aria-labelledby="EditInvHeading" aria-modal="true" aria-describedby="EditInv">
<div class="slds-modal__container">
	<header class="slds-modal__header">
		<button class="slds-button slds-button_icon slds-modal__close slds-button_icon-inverse" title="{$APP.LBL_CLOSE}" onClick="hide('editdiv');">
			<svg class="slds-button__icon slds-button__icon_large" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
			</svg>
			<span class="slds-assistive-text">{$APP.LBL_CLOSE}</span>
		</button>
		<h2 id="EditInvHeading" class="slds-modal__title slds-hyphenate slds-page-header__title">{$CRON_DETAILS.label}</h2>
	</header>
	<div class="slds-modal__content slds-p-around_medium">
		<input id="min_freq" type="hidden" value="{$MIN_CRON_FREQUENCY}">
		<input id="desc" type="hidden" value="{'LBL_MINIMUM_FREQUENCY'|@getTranslatedString:'CronTasks'} {$MIN_CRON_FREQUENCY} {'LBL_MINUTES'|@getTranslatedString:'CronTasks'}" size="35" maxlength="40">
		<div class="slds-form-element">
			<label class="slds-form-element__label" for="cron_status">{$MOD.LBL_STATUS}</label>
			<div class="slds-form-element__control">
				<select class="slds-select slds-page-header__meta-text" id="cron_status" name="cron_status">
					<option value="1" {if $CRON_DETAILS.status eq 1}selected{/if}>{$MOD.LBL_ACTIVE}</option>
					<option value="0" {if $CRON_DETAILS.status neq 1}selected{/if}>{$MOD.LBL_INACTIVE}</option>
				</select>
			</div>
		</div>
		<div class="slds-form-element">
			<label class="slds-form-element__label" for="CronTime">{$MOD.LBL_FREQUENCY}</label>
			<div class="slds-form-element__control slds-grid slds-gutters">
				<div class="slds-col slds-size_1-of-2">
				<input class="slds-input slds-page-header__meta-text" id="CronTime" name="CronTime" value="{$CRON_DETAILS.frequency}" style="{if $CRON_DETAILS.time eq 'daily'}display: none;{/if}" type="text">
				<input class="slds-input slds-page-header__meta-text" id="CronDay" name="CronDay" value="{if $CRON_DETAILS.time neq 'daily'}00:00{else}{$CRON_DETAILS.hourmin}{/if}" style="{if $CRON_DETAILS.time neq 'daily'}display: none;{/if}" type="text">
				</div>
				<div class="slds-col slds-size_1-of-2">
				<select class="slds-select slds-page-header__meta-text" id="cron_time" name="cron_status" onchange="change_input_time()">
					<option value="min" {if $CRON_DETAILS.time eq 'min'}selected{/if}>{$MOD.LBL_MINUTES}</option>
					<option value="hours" {if $CRON_DETAILS.time eq 'hour'}selected{/if}>{$MOD.LBL_HOURS}</option>
					<option value="daily" {if $CRON_DETAILS.time eq 'daily'}selected{/if}>{$MOD.LBL_DAILY}</option>
				</select>
				</div>
			</div>
		</div>
		<p class="slds-icon_container slds-icon-utility-info slds-m-top_large slds-page-header__meta-text">
			<svg class="slds-icon slds-icon slds-icon_xx-small slds-icon-text-default" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info"></use>
			</svg>
			{$CRON_DETAILS.description|@getTranslatedString:$CRON_MODULE}
		</p>
	</div>
	<footer class="slds-modal__footer" style="width:100%;">
		<button class="slds-button slds-button_neutral" onClick="hide('editdiv');">{$APP.LBL_CANCEL_BUTTON_LABEL}</button>
		<button class="slds-button slds-button_brand" onClick="fetchSaveCron('{$CRON_DETAILS.id}')">{$APP.LBL_SAVE_BUTTON_LABEL}</button>
	</footer>
</div>
</section>
<div class="slds-backdrop slds-backdrop_open"></div>
