{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  coreBOS Open Source
   * The Initial Developer of the Original Code is coerBOS.
   * All Rights Reserved.
 ********************************************************************************/
-->

RELATIONFIELD.id
RELATIONFIELD.value
RELATIONFIELD.display
RELATIONFIELD.module
RELATIONFIELD.filter
*}
<input id="{$RELATIONFIELD.id}" name="{$RELATIONFIELD.id}" type="hidden" value="{$RELATIONFIELD.value}">
<input id="{$RELATIONFIELD.id}_display" name="{$RELATIONFIELD.id}_display" readonly type="text" class="slds-input" style="width:350px;border:1px solid #bababa;padding-left:revert;" onclick="return window.open('index.php?module={$RELATIONFIELD.module}&action=Popup&html=Popup_picker&form={$RELATIONFIELD.form}&forfield={$RELATIONFIELD.id}&srcmodule=GlobalVariable'+{$RELATIONFIELD.filter}, 'vtlibui10wf', cbPopupWindowSettings);" value="{$RELATIONFIELD.display}">&nbsp;
<span class="slds-icon_container slds-icon-standard-choice" title="{'LBL_SELECT'|getTranslatedString}" onclick="return window.open('index.php?module={$RELATIONFIELD.module}&action=Popup&html=Popup_picker&form={$RELATIONFIELD.form}&forfield={$RELATIONFIELD.id}&srcmodule=GlobalVariable'+{$RELATIONFIELD.filter}, 'vtlibui10wf', cbPopupWindowSettings);">
	<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
	<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#choice"></use>
	</svg>
</span>
<span class="slds-icon_container slds-icon-standard-choice" title="{'LBL_CLEAR'|getTranslatedString}" onclick="document.getElementById('{$RELATIONFIELD.id}').value=''; document.getElementById('{$RELATIONFIELD.id}_display').value=''; return false;">
	<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
	<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
	</svg>
</span>