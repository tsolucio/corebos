{*<!--
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}

<div id="new_template_popup" style="display:none;z-index:10;">
	<form action="index.php" method="post" accept-charset="utf-8" onsubmit="VtigerJS_DialogBox.block();">
	<section role="dialog" tabindex="-1" class="slds-modal slds-fade-in-open" aria-labelledby="EditInvHeading" aria-modal="true" aria-describedby="EditInv">
	<div class="slds-modal__container">
		<header class="slds-modal__header">
			<button class="slds-button slds-button_icon slds-modal__close slds-button_icon-inverse" title="{$APP.LBL_CLOSE}" type="button" onClick="hide('new_template_popup');">
				<svg class="slds-button__icon slds-button__icon_large" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
				</svg>
				<span class="slds-assistive-text">{$APP.LBL_CLOSE}</span>
			</button>
			<h2 id="EditInvHeading" class="slds-modal__title slds-hyphenate slds-page-header__title">{$MOD.LBL_NEW_TEMPLATE}</h2>
		</header>
		<div class="slds-modal__content slds-p-around_x-small">
			<div class="popup_content">
				<div class="slds-form-element">
					<label class="slds-form-element__label slds-page-header__meta-text" for="wftpltitle">
						<abbr class="slds-required" title="required">* </abbr>{$APP.LBL_TITLE}
					</label>
					<div class="slds-form-element__control slds-page-header__meta-text">
						<input type="text" name="title" id="wftpltitle" class='slds-input'>
					</div>
				</div>
				<input type="hidden" name="module_name" value="{$workflow->moduleName}">
				<input type="hidden" name="save_type" value="new">
				<input type="hidden" name="module" value="{$module->name}">
				<input type="hidden" name="action" value="savetemplate">
				<input type="hidden" name="return_url" value="{$newTaskReturnUrl}">
				<input type="hidden" name="workflow_id" value="{$workflow->id}">
			</div>
		</div>
		<footer class="slds-modal__footer" style="width:100%;">
			<button class="slds-button slds-button_neutral" type="button" name="cancel" id='new_template_popup_cancel' onClick="hide('new_template_popup');">
			{$APP.LBL_CANCEL_BUTTON_LABEL}
			</button>
			<button class="slds-button slds-button_brand" type="submit" name="save" id='new_template_popup_save'>
			{$APP.LBL_CREATE_BUTTON_LABEL}
			</button>
		</footer>
	</div>
	</section>
	</form>
	<div class="slds-backdrop slds-backdrop_open"></div>
</div>