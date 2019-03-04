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
<link rel="stylesheet" type="text/css" href="include/LD/assets/styles/salesforce-lightning-design-system.css" />
<div class="slds-m-top_x-large">
	<div class="slds-notify slds-notify_alert slds-theme_error slds-theme_alert-texture" role="alert">
	<h2>
		<svg class="slds-icon slds-icon_small slds-m-right_x-small" aria-hidden="true">
		<use xlink:href="include/LD//assets/icons/utility-sprite/svg/symbols.svg#ban"></use>
		</svg>{if isset($OPERATION_MESSAGE)}{$OPERATION_MESSAGE}{else}{$APP.LBL_PERMISSION}{/if}<br>
		{if !isset($PUT_BACK_ACTION)}
		<a href='javascript:window.history.back();'>{$APP.LBL_GO_BACK}</a>
		{/if}
	</h2>
	</div>
</div>
