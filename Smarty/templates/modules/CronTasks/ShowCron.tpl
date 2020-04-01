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
	<div class="slds-page-header__meta-text">
		<label class="slds-form-element__label" for="cron_status">{$MOD.LBL_STATUS}</label>
		{if $CRON_DETAILS.status eq 1} {$MOD.LBL_ACTIVE} {else} {$MOD.LBL_INACTIVE} {/if}
	</div>
	<div class="slds-page-header__meta-text">
		<label class="slds-form-element__label" for="CronTime">{$MOD.LBL_FREQUENCY}</label>
		{$CRON_DETAILS.frequency} {if $CRON_DETAILS.time eq 'min'} {$MOD.LBL_MINUTES} {elseif $CRON_DETAILS.time eq 'daily'} {$MOD.LBL_DAILY} {else} {$MOD.LBL_HOURS} {/if}
	</div>
	<p class="slds-icon_container slds-icon-utility-info slds-m-top_large slds-page-header__meta-text">
		<svg class="slds-icon slds-icon slds-icon_xx-small slds-icon-text-default" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info"></use>
		</svg>
		{$CRON_DETAILS.description|@getTranslatedString:$CRON_MODULE}
	</p>
{/block}
{block name=ModalFooter}
<button class="slds-button slds-button_neutral" onClick="hide('editdiv');">{$APP.LBL_CLOSE}</button>
{/block}