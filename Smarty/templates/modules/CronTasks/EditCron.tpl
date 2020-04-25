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

{assign var='MODAL' value=['label'=>$CRON_DETAILS.label, 'ariaDescribe'=>$CRON_DETAILS.label, 'hideID'=>'editdiv']}
{extends file='Components/Modal.tpl'}
{block name=ModalContent}
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
	<div class="slds-form-element">
		<label class="slds-form-element__label" for="CronAlertTime">{$MOD.LBL_CRONALERT} ({$MOD.LBL_MINUTES})</label>
		<div class="slds-form-element__control slds-grid slds-gutters">
			<div class="slds-col slds-size_1-of-2">
			<input class="slds-input slds-page-header__meta-text" id="CronAlertTime" name="CronAlertTime" value="{$CRON_DETAILS.alerttime}" type="number" min=-1>
			</div>
			<div class="slds-col slds-size_1-of-2"></div>
		</div>
	</div>
	<p class="slds-icon_container slds-icon-utility-info slds-m-top_large slds-page-header__meta-text">
		<svg class="slds-icon slds-icon slds-icon_xx-small slds-icon-text-default" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info"></use>
		</svg>
		{$CRON_DETAILS.description|@getTranslatedString:$CRON_MODULE}
	</p>
{/block}
{block name=ModalFooter}
	<button class="slds-button slds-button_neutral" onClick="hide('editdiv');">{$APP.LBL_CANCEL_BUTTON_LABEL}</button>
	<button class="slds-button slds-button_brand" onClick="fetchSaveCron('{$CRON_DETAILS.id}')">{$APP.LBL_SAVE_BUTTON_LABEL}</button>
{/block}
