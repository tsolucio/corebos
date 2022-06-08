{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  coreBOS Open Source
   * The Initial Developer of the Original Code is coerBOS.
   * All Rights Reserved.
 ********************************************************************************/
-->

BUTTONICON.title button hover text
BUTTONICON.id button HTML ID
BUTTONICON.size large/medium/small
BUTTONICON.library LDS icon library
BUTTONICON.icon LDS icon name
*}

<button class="slds-button slds-button_icon" title="{$BUTTONICON.title}" type="button" id="{$BUTTONICON.id}">
	<svg class="slds-button__icon slds-button__icon_{$BUTTONICON.size}" aria-hidden="true">
	<use xlink:href="include/LD/assets/icons/{$BUTTONICON.library}-sprite/svg/symbols.svg#{$BUTTONICON.icon}"></use>
	</svg>
	<span class="slds-assistive-text">{$BUTTONICON.title}</span>
</button>