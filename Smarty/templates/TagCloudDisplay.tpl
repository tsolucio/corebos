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

{if $TAG_CLOUD_DISPLAY eq 'true'}
<!-- Tag cloud display -->
	<table style="border:0;width:100%" class="tagCloud">
	<tr>
		<td>
		<div id="tagdiv" style="display:visible;">
		<form method="POST" action="javascript:void(0);" onsubmit="return tagvalidate();">
			<div class="slds-form-element">
				<label
					class="slds-form-element__label"
					for="text-input-id-47"
					style="font-weight: 700; padding-left: 3px;"
					>
					{$APP.LBL_TAG_CLOUD}
				</label>
				<div class="slds-form-element__control slds-input-has-icon slds-input-has-icon_left-right">
					<svg class="slds-icon slds-input__icon slds-input__icon_left slds-icon-text-default" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
					</svg>
					<input value="{$APP.LBL_TAG_IT}" type="text" id="txtbox_tagfields" placeholder="{'Add Tag'|@getTranslatedString:'com_vtiger_workflow'}…" value="" class="slds-input"/>
					<button 
						class="slds-button slds-button_icon slds-input__icon slds-input__icon_right"
						title="{$APP.LBL_TAG_IT}"
						value="{$APP.LBL_TAG_IT}">
						<svg class="slds-button__icon slds-icon-text-light" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#touch_action"></use>
						</svg>
						<span class="slds-assistive-text">{$APP.LBL_TAG_IT}</span>
					</button>
				</div>
			</div>
		</form>
		</div>
		</td>
	</tr>
	<tr>
		<td class="tagCloudDisplay" valign=top> <span id="tagfields"></span></td>
	</tr>
	</table>
<script>
function tagvalidate() {
	if (trim(document.getElementById('txtbox_tagfields').value) != '') {
		SaveTag('txtbox_tagfields', '{$ID}', '{$MODULE}');
	} else {
		alert('{$APP.PLEASE_ENTER_TAG}');
		return false;
	}
}
getTagCloud({$ID});
</script>
<!-- End Tag cloud display -->
{/if}
