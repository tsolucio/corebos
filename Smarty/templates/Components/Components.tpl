{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  coreBOS Open Source
   * The Initial Developer of the Original Code is coerBOS.
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

{* Creates the main menu vertical *}
{function cbmenuvertical i=0}
<nav class="slds-context-bar__secondary" role="navigation">
	<ul id="cbmenu" class="accordion slds-accordion">
	{foreach $menu as $menuitem}
		{if $menuitem.mtype == 'menu'}
		<li class="slds-dropdown__item slds-parent-menu" role="presentation" id="parent{$i}" data-level={$i} data-type="parent">
			<a href="javascript:void(0);" role="menuitem" class="link" title="{$menuitem.mlabel}">
				<span class="slds-truncate">{$menuitem.mlabel}</span>
			{if !empty($menuitem.submenu)}
				<svg aria-hidden="true" class="slds-button__icon cbslds-button_icon-small">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevrondown"></use>
				</svg>
				{call cbsubmenuvertical submenu=$menuitem.submenu i=$i}
			{/if}
			</a>
		</li>
		{elseif $menuitem.mtype == 'module'}
		<li class="slds-dropdown__item slds-context-bar__item" role="presentation">
			<a href="index.php?action=index&amp;module={$menuitem.mvalue}" role="menuitem" class="link" title="{$menuitem.mlabel}">
				<span class="slds-truncate">{$menuitem.mlabel}</span>
			</a>
		</li>
		{elseif $menuitem.mtype == 'url'}
		<li class="slds-dropdown__item slds-context-bar__item" role="presentation">
			<a href="{$menuitem.mvalue}" role="menuitem" class="link" title="{$menuitem.mlabel} tabindex="-1">
				<span class="slds-truncate">{$menuitem.mlabel}</span>
			</a>
		</li>
		{/if}
		{$i = $i+1}
	{/foreach}
	</ul>
</nav>
{/function}

{* Creates all the other levels menu *}
{function cbsubmenuvertical j=0}
	<ul class="submenu slds-dropdown__list slds-is-nested " style="background: aliceblue;" role="menu" id="menu{$i}">
	{foreach $submenu as $menuitem}
		{if $menuitem.mtype == 'module' && empty($menuitem.submenu)}
		<li class="slds-dropdown__item slds-child-menu" role="presentation" data-type="child" data-name="{$menuitem.mvalue}" data-level="{$i}">
			<a href="index.php?action=index&amp;module={$menuitem.mvalue}" role="menuitem" tabindex="-1">
				<span class="slds-truncate" >{$menuitem.mlabel}</span>
			</a>
		</li>
		{elseif ($menuitem.mtype == 'menu' || $menuitem.mtype == 'module') && !empty($menuitem.submenu)}
		<li class="slds-dropdown__item" role="presentation" data-level={$j} data-type="parent">
			<a href="javascript:void(0);" role="menuitem" class="link" title="{$menuitem.mlabel}">
			<span class="slds-truncate">{$menuitem.mlabel}</span>
				<svg aria-hidden="true" class="slds-button__icon cbslds-button_icon-small">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevrondown"></use>
				</svg>
				{call cbsubmenuvertical submenu=$menuitem.submenu i=$i}
			</a>
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
{/function}
{literal}
<script type="text/javascript">
function findParents(el) {
	let parents = [];
	while(el.parentNode) {
		el = el.parentNode;
		parents.push(el);
	}
	return parents;
};
function findUpModuleInMenu() {
	let arr = {};
	const modulename = document.querySelectorAll(`[data-name="${gVTModule}"]`);
	if (modulename.length > 0) {
		const parent = document.getElementById(`menu${modulename[0].dataset.level}`);
		const childs = parent.querySelectorAll(`li span`);
		modulename[0].parentElement.style.display = 'block';
		modulename[0].style.background = '#c5d7e3';

		//for (let i = 0; i < childs.length; i++) {
			//if (childs[i].innerText == modulename[0].innerText) {
			//	childs[i].style.color = '#0061e5';
			//} else {
				//childs[i].style.color = 'black';
			//}
			//for (let j = 0; j < childs[i].parentElement.children.length; j++) {
				//if (childs[i].parentElement.children[j].innerText == modulename[0].innerText) {
					//childs[i].parentElement.children[j].style.color = '#0061e5';
				//}
			//}
		//}
		console.log(modulename);
		
		let parents = findParents(modulename[0]);
		for (let i = 0; i < parents.length; i++) {
			if (parents[i].dataset.type !== undefined && parents[i].dataset.type == 'parent') {
				const submenu = parents[i].getElementsByClassName('submenu');
				const childs1 = submenu[0].querySelectorAll(`li span`);
				for (let j = 0; j < submenu.length; j++) {
					submenu[j].style.display = 'block';
					for (let k = 0; k < childs1.length; k++) {
						if (childs1[k].innerText == modulename[0].innerText) {
							childs1[k].style.color = '#0061e5';
							childs1[k].style.fontWeight = 'bold';
							break;
						} else {
							childs1[k].style.color = 'black';
						}
					}
					if (submenu[j].innerHTML.indexOf(gVTModule)) {
						break;
					}
				}
			}
		}
	}
}
window.addEventListener('DOMContentLoaded', (event) => {
	findUpModuleInMenu();
});
</script>
{/literal}
