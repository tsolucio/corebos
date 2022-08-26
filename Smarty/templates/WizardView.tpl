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
	{if $wizardTitle!=''}
	<div class="slds-page-header__name">
		<div class="slds-page-header__name-title">
			<span class="slds-page-header__title slds-truncate" title="{$wizardTitle}">{$wizardTitle}</span>
		</div>
	</div>
	{/if}
	<div class="slds-progress">
		<ol class="slds-progress__list">
		{foreach from=$wizardSteps item=step}
			<li class="slds-progress__item slds-is-active">
			<button class="slds-button slds-progress__marker">
			<span class="slds-assistive-text">{$step.title}</span>
			</button>
			</li>
		{/foreach}
		</ol>
		<div class="slds-progress-bar slds-progress-bar_x-small" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" role="progressbar">
		<span class="slds-progress-bar__value" style="width:0%">
		<span class="slds-assistive-text">Progress: 0%</span>
		</span>
		</div>
		{foreach from=$wizardSteps item=step name=ttip}
			{if $smarty.foreach.ttip.first}
				<div class="slds-popover slds-popover_tooltip slds-nubbin_bottom-left" role="tooltip" id="step-3-tooltip" style="position:absolute;top:-2.8rem;">
			{elseif $smarty.foreach.ttip.last}
				<div class="slds-popover slds-popover_tooltip slds-nubbin_bottom-right" role="tooltip" id="step-3-tooltip" style="position:absolute;top:-2.8rem;left:88%;transform:translateX(12%)">
			{else}
				<div class="slds-popover slds-popover_tooltip slds-nubbin_bottom" role="tooltip" id="step-3-tooltip" style="position:absolute;top:-2.8rem;left:88%;transform:translateX(12%)">
			{/if}
				<div class="slds-popover__body">{$step.title}</div>
			</div>
		{/foreach}
	</div>
{/if}