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
<script type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>
{assign var='BLOCKS' value=getSettingsBlocks()}
{assign var='FIELDS' value=getSettingsFields()}
{assign var="MODULELABEL" value=$MODULE|@getTranslatedString:$MODULE}
{if !isset($MODULEICON)}
	{assign var="MODULEICON" value='settings'}
{/if}
{if !isset($MODULESECTION)}
	{assign var="MODULESECTION" value=$MODULELABEL}
{/if}
{if !isset($MODULESECTIONDESC)}
	{assign var="MODULESECTIONDESC" value=''}
{/if}
<div id="page-header-placeholder"></div>
<div id="page-header" class="slds-page-header slds-m-vertical_medium page-header-relative">
	<div class="slds-page-header__row">
		<div class="slds-page-header__col-title">
			<div class="slds-media">
				<div class="slds-media__figure">
					<a class="hdrLink" href="index.php?action=index&module=Settings">
						<span class="slds-icon_container slds-icon-standard-account" title="{$MODULELABEL}">
							<svg class="slds-icon slds-page-header__icon" id="page-header-icon" aria-hidden="true">
								<use xmlns:xlink="http://www.w3.org/1999/xlink"
									xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#{$MODULEICON}" />
							</svg>
							<span class="slds-assistive-text">{$MODULELABEL}</span>
						</span>
					</a>
				</div>
				<div class="slds-media__body">
					<div class="slds-page-header__name">
						<div class="slds-page-header__name-title">
							<h1>
								<span><a href="index.php?action=index&module=Settings">{$MODULELABEL}</a></span>
								<span class="slds-page-header__title slds-truncate" title="{$MODULESECTION|@addslashes}">
									{if !empty($isDetailView) || !empty($isEditView)}
									<span class="slds-page-header__title slds-truncate" title="{$MODULESECTION|@addslashes}">
										<span class="slds-page-header__name-meta">[ {$TITLEPREFIX} ]</span>
										{$MODULESECTION|textlength_check:30}
									</span>
									{else}
									<a class="hdrLink" href="index.php?action=index&module=Settings">{$MODULESECTION}</a>
									<p valign=top class="small cblds-p-v_none">&nbsp;&nbsp;&nbsp;&nbsp;{$MODULESECTIONDESC}</p>
									{/if}
								</span>
							</h1>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="slds-page-header__col-actions">
		<div class="slds-dropdown-trigger slds-dropdown-trigger_hover">
			<button class="slds-button slds-button_icon slds-button_icon-border-filled" aria-haspopup="true" title="{'LBL_ACTIONS'|@getTranslatedString}" style="color:#0070d2;width:5rem;" type="button">
			{'LBL_ACTIONS'|@getTranslatedString}
			<svg class="slds-button__icon" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#down"></use>
			</svg>
			<span class="slds-assistive-text">{'LBL_ACTIONS'|@getTranslatedString}</span>
			</button>
			<div class="slds-dropdown slds-dropdown_right slds-grid slds-gutters_medium" style="max-width: unset;">
				{foreach key=BLOCKID item=BLOCKLABEL from=$BLOCKS}
				{if $BLOCKLABEL neq 'LBL_MODULE_MANAGER'}
					{assign var=blocklabel value=$BLOCKLABEL|@getTranslatedString:'Settings'}
					<div class="slds-col slds-size_1-of-4">
					<h3 class="slds-accordion__summary-heading slds-accordion__summary-action">
						<svg class="slds-accordion__summary-action-icon slds-button__icon slds-button__icon_left" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevrondown"></use>
						</svg>
						<span class="slds-truncate" title="{$blocklabel}">{$blocklabel}&nbsp;&nbsp;</span>
					</h3>
					<ul class="slds-dropdown__list" role="menu">
						{foreach item=data from=$FIELDS.$BLOCKID}
							{if $data.link neq ''}
								{assign var=label value=$data.name|@getTranslatedString:$data.module}
								{if $label eq $data.name}
								{assign var=label value=$data.name|@getTranslatedString:'Settings'}
								{/if}
								<li class="slds-dropdown__item" role="presentation">
									<a href="{$data.link}" role="menuitem" {if ($smarty.request.action eq $data.action && $smarty.request.module eq $data.module)}class="slds-has-success"{/if}>
									<span class="slds-truncate" title="{$label}">
										<span class="slds-assistive-text">{$label}</span>
										<span class="slds-page-header__meta-text">{$label}</span>
									</span>
									</a>
								</li>
							{/if}
						{/foreach}
					</ul>
					</div>
				{/if}
				{/foreach}
			</div>
		</div>
		</div>
		<div id="page-header-surplus"></div>
	</div>
</div>
