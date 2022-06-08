{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  coreBOS Open Source
   * The Initial Developer of the Original Code is coerBOS.
   * All Rights Reserved.
 ********************************************************************************/
-->

BUTTONWITHICON.variation neutral, brand, destructive...
BUTTONWITHICON.title button hover text
BUTTONWITHICON.id button HTML ID
BUTTONWITHICON.name button HTML name
BUTTONWITHICON.size large/medium/small
BUTTONWITHICON.position left/right
BUTTONWITHICON.library LDS icon library
BUTTONWITHICON.icon LDS icon name
*}

<button
	class="slds-button slds-button_{$BUTTONWITHICON.variation}"
	title="{$BUTTONWITHICON.title}"
	type="button"
	id="{$BUTTONWITHICON.id}"
	name="{if empty($BUTTONWITHICON.name)}{$BUTTONWITHICON.id}{else}{$BUTTONWITHICON.name}{/if}"
	{if isset($BUTTONWITHICON.onclick)}onclick="{$BUTTONWITHICON.onclick}"{/if}>
	<svg class="slds-button__icon slds-button__icon_{$BUTTONWITHICON.size} slds-button__icon_{$BUTTONWITHICON.position}" aria-hidden="true">
	<use xlink:href="include/LD/assets/icons/{$BUTTONWITHICON.library}-sprite/svg/symbols.svg#{$BUTTONWITHICON.icon}"></use>
	</svg>
	{$BUTTONWITHICON.title}
</button>