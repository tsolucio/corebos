{*<!--
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
********************************************************************************/
-->

ICON.title icon hover text
ICON.description
ICON.id icon HTML ID
ICON.size large/medium/small
ICON.library LDS icon library
ICON.icon LDS icon name
*}

<span class="slds-icon_container slds-icon-{$ICON.library}-{$ICON.icon}" title="{$ICON.title}" {if isset($ICON.id)}id="{$ICON.id}"{/if}>
	<svg class="slds-icon slds-icon-text-default slds-icon_{$ICON.size}" aria-hidden="true">
		<use xlink:href="include/LD/assets/icons/{$ICON.library}-sprite/svg/symbols.svg#{$ICON.icon}"></use>
	</svg>
	{if isset($ICON.description)}
	<span class="slds-assistive-text">{$ICON.description}</span>
	{/if}
</span>