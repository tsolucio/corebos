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

TOOLTIPLEFT.set to 1 to show tooltip on the left
*}

<section class="tooltip" role="dialog">
<span class="slds-icon_container slds-icon-utility-info">
	<svg class="slds-icon slds-icon slds-icon_xx-small slds-icon-text-default" aria-hidden="true">
	<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info"></use>
	</svg>
</span>
<span class="tooltiptext{if isset($TOOLTIPLEFT)}right{/if} slds-popover slds-nubbin_{if isset($TOOLTIPLEFT)}right{else}left{/if}-top-corner">
{block name=TOOLTIPInfo}Nothing to add!{/block}
</span>
</section>
