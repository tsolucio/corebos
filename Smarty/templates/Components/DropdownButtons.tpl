{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  coreBOS Open Source
   * The Initial Developer of the Original Code is coerBOS.
   * All Rights Reserved.
 ********************************************************************************/
-->
*}

<div class="slds-dropdown-trigger slds-dropdown-trigger_hover slds-button_last">
{if isset($MENUIMAGE)}
	{assign var='MENUBUTTONWIDTH' value=3}
{else}
	{assign var='MENUBUTTONWIDTH' value=5}
{/if}
<button class="slds-button slds-button_icon slds-button_icon-border-filled" aria-haspopup="true" title="{$MENULABEL|@getTranslatedString}" style="color:#0070d2;width:{$MENUBUTTONWIDTH}rem;" type="button">
{if isset($MENUIMAGE)}
<svg class="slds-button__icon" aria-hidden="true">
<use xlink:href="include/LD/assets/icons/{$MENUIMAGE.library}-sprite/svg/symbols.svg#{$MENUIMAGE.icon}"></use>
</svg>
{else}
{$MENULABEL|@getTranslatedString}
{/if}
<svg class="slds-button__icon" aria-hidden="true">
<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#down"></use>
</svg>
<span class="slds-assistive-text">{$MENULABEL|@getTranslatedString}</span>
</button>
<div class="slds-dropdown slds-dropdown_right">
<ul class="slds-dropdown__list" role="menu">
	{foreach item=CUSTOMLINK from=$MENUBUTTONS}
	{if is_object($CUSTOMLINK)}
		{assign var="customlink_href" value=$CUSTOMLINK->linkurl}
		{assign var="customlink_label" value=$CUSTOMLINK->linklabel}
		{assign var="customlink_id" value=$CUSTOMLINK->linklabel|replace:' ':''}
		{if $customlink_label eq ''}
			{assign var="customlink_label" value=$customlink_href}
		{else}
			{* Pickup the translated label provided by the module *}
			{assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
		{/if}
		<li class="slds-dropdown__item" role="presentation">
			<a href="javascript:void(0);" role="menuitem" onclick="{$customlink_href}">
				{if $CUSTOMLINK->linkicon && strpos($CUSTOMLINK->linkicon, '}')>0}
					{assign var="customlink_iconinfo" value=$CUSTOMLINK->linkicon|json_decode:true}
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/{$customlink_iconinfo.library}-sprite/svg/symbols.svg#{$customlink_iconinfo.icon}"></use>
					</svg>
				{/if}
				<span class="slds-truncate" title="{$customlink_label}">
					<span class="slds-assistive-text">{$customlink_label}</span>
					<span>{$customlink_label}</span>
				</span>
			</a>
		</li>
	{else}
		{$CUSTOMLINK}
	{/if}
	{/foreach}
</ul>
</div>
</div>