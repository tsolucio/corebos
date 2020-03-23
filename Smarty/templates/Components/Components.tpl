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

{* Creates the main menu *}
{function cbmenu i=0}
<nav class="slds-context-bar__secondary" role="navigation">
	<ul class="slds-grid" id="cbmenu">
	{foreach $menu as $menuitem}
		{if $menuitem.mtype == 'menu'}
		<li class="slds-context-bar__item slds-context-bar__dropdown-trigger slds-dropdown-trigger slds-dropdown-trigger_hover" aria-haspopup="true">
			<a href="javascript:void(0);" class="slds-context-bar__label-action" title="{$menuitem.mlabel}">
				<span class="slds-truncate">{$menuitem.mlabel}</span>
			</a>
			{if !empty($menuitem.submenu)}
			<div class="slds-context-bar__icon-action slds-p-left_none" tabindex="0">
				<svg aria-hidden="true" class="slds-button__icon">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevrondown"></use>
				</svg>
			</div>
			{call cbsubmenu submenu=$menuitem.submenu i=$i}
			{/if}
		</li>
		{elseif $menuitem.mtype == 'module'}
		<li class="slds-context-bar__item">
			<a href="index.php?action=index&amp;module={$menuitem.mvalue}" class="slds-context-bar__label-action" title="{$menuitem.mlabel}">
				<span class="slds-truncate">{$menuitem.mlabel}</span>
			</a>
		</li>
		{/if}
		{$i = $i+1}
	{/foreach}
	</ul>
	<div class="slds-context-bar__tertiary" style="float:left; margin-top:auto; margin-bottom:auto;">
		<div class="slds-form-element">
			<div class="slds-form-element__control">
				<div class="slds-select_container">
				<select id="qccombo" class="slds-select" onchange="QCreate(this);">
					<option value="none">{$APP.LBL_QUICK_CREATE}...</option>
					{foreach item=detail from=$QCMODULE}
						<option value="{$detail.1}">{$APP.NEW}&nbsp;{$detail.0}</option>
					{/foreach}
				</select>
				</div>
			</div>
		</div>
	</div>
</nav>
{/function}

{* Creates the second level menu *}
{function cbsubmenu j=0}
<div class="slds-dropdown slds-dropdown_center slds-nubbin_top">
	<ul class="slds-dropdown__list" role="menu" id="menu{$i}">
	{foreach $submenu as $menuitem}
		{if $menuitem.mtype == 'module' && empty($menuitem.submenu)}
		<li class="slds-dropdown__item" role="presentation">
			<a href="index.php?action=index&amp;module={$menuitem.mvalue}" role="menuitem" tabindex="-1">
				<span class="slds-truncate">{$menuitem.mlabel}</span>
			</a>
		</li>
		{elseif $menuitem.mtype == 'module' && !empty($menuitem.submenu)}
		<li class="slds-dropdown__item" role="presentation">
		{call cbsubsubmenu submenuitem=$menuitem i=$i j=$j k=0}
		{$j = $j + 1}
		</li>
		{elseif $menuitem.mtype == 'menu' && !empty($menuitem.submenu)}
		<li class="slds-dropdown__item" role="presentation">
		{call cbsubsubmenu submenuitem=$menuitem i=$i j=$j k=0}
		{$j = $j + 1}
		</li>
		{elseif $menuitem.mtype == 'headtop'}
		<li class="slds-dropdown__header slds-has-divider_top-space" role="separator">
			<span class="slds-text-title_caps">{$menuitem.mlabel}</span>
		</li>
		{elseif $menuitem.mtype == 'headbottom'}
		<li class="slds-dropdown__header slds-has-divider_bottom-space" role="separator">
			<span class="slds-text-title_caps">{$menuitem.mlabel}</span>
		</li>
		{elseif $menuitem.mtype == 'sep'}
		<li class="slds-dropdown__header slds-has-divider_top-space" role="separator"></li>
		{elseif $menuitem.mtype == 'url'}
		<li class="slds-dropdown__item" role="presentation">
			<a href="{$menuitem.mvalue}" role="menuitem" tabindex="-1">
				<span class="slds-truncate">{$menuitem.mlabel}</span>
			</a>
		</li>
		{/if}
	{/foreach}
	</ul>
</div>
{/function}

{* Creates all the other levels recursively *}
{function cbsubsubmenu}
	{if !empty($submenuitem.submenu)}
		{if $submenuitem.mtype == 'module'}
		<a href="index.php?action=index&amp;module={$menuitem.mvalue}" role="menuitem" tabindex="-1">
		{else}
		<a href="javascript:void(0);" role="menuitem" tabindex="-1">
		{/if}
			<span class="slds-truncate" style="padding-right: 20px;">{$submenuitem.mlabel}</span>
			<svg aria-hidden="true" class="slds-button__icon">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevronright"></use>
			</svg>
		</a>
		<ul id="submenu{$i}-{$j}{if $k > 0}-{$k}{/if}" class="moreMenu">
		{foreach $submenuitem.submenu as $submenu_item}
			<li class="slds-dropdown__item" role="presentation">
				{$k = $k + 1}
				{call cbsubsubmenu submenuitem=$submenu_item i=$i j=$j k=$k}
			</li>
		{/foreach}
		</ul>
	{elseif $submenuitem.mtype == 'module'}
		<li class="slds-dropdown__item" role="presentation">
			<a href="index.php?action=index&amp;module={$submenuitem.mvalue}" role="menuitem" tabindex="-1">
				<span class="slds-truncate">{$submenuitem.mlabel}</span>
			</a>
		</li>
	{elseif $submenuitem.mtype == 'headtop'}
		<li class="slds-dropdown__header slds-has-divider_top-space" role="separator">
			<span class="slds-text-title_caps">{$submenuitem.mlabel}</span>
		</li>
	{elseif $submenuitem.mtype == 'headbottom'}
		<li class="slds-dropdown__header slds-has-divider_bottom-space" role="separator">
			<span class="slds-text-title_caps">{$submenuitem.mlabel}</span>
		</li>
	{elseif $submenuitem.mtype == 'sep'}
		<li class="slds-dropdown__header slds-has-divider_top-space" role="separator"></li>
	{elseif $submenuitem.mtype == 'url'}
		<li class="slds-dropdown__item" role="presentation">
			<a href="{$submenuitem.mvalue}" role="menuitem" tabindex="-1">
				<span class="slds-truncate">{$submenuitem.mlabel}</span>
			</a>
		</li>
	{/if}
{/function}
