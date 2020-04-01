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

<div id="new_workflow_popup" class="layerPopup" style="display:none;z-index:10;">
	<form action="index.php" method="post" accept-charset="utf-8" onsubmit="return wfCreateSubmit();">
	<section role="dialog" tabindex="-1" class="slds-modal slds-fade-in-open" aria-labelledby="EditInvHeading" aria-modal="true" aria-describedby="EditInv">
	<div class="slds-modal__container">
		<header class="slds-modal__header">
			<button class="slds-button slds-button_icon slds-modal__close slds-button_icon-inverse" title="{$APP.LBL_CLOSE}" type="button" onClick="hide('new_workflow_popup');">
				<svg class="slds-button__icon slds-button__icon_large" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
				</svg>
				<span class="slds-assistive-text">{$APP.LBL_CLOSE}</span>
			</button>
			<h2 id="EditInvHeading" class="slds-modal__title slds-hyphenate slds-page-header__title">{$MOD.LBL_CREATE_WORKFLOW}</h2>
		</header>
		<div class="slds-modal__content slds-p-around_x-small">
			<div class="popup_content">
				<div class="slds-form-element__control">
					<span class="slds-radio">
					<input type="radio" name="source" id="wffrommodule" value="from_module" checked="" />
					<label class="slds-radio__label" for="wffrommodule">
						<span class="slds-radio_faux"></span>
						<span class="slds-form-element__label slds-page-header__meta-text">{$MOD.LBL_FOR_MODULE}</span>
					</label>
					</span>
					<span class="slds-radio slds-m-top_xx-small slds-m-bottom_xx-small">
					<input type="radio" name="source" id="wffromtpl" value="from_template" />
					<label class="slds-radio__label" for="wffromtpl">
						<span class="slds-radio_faux"></span>
						<span class="slds-form-element__label slds-page-header__meta-text">{$MOD.LBL_FROM_TEMPLATE}</span>
					</label>
					</span>
				</div>
				<div class="slds-form-element">
					<label class="slds-form-element__label slds-page-header__meta-text" for="module_list">{$MOD.LBL_CREATE_WORKFLOW_FOR}</label>
					<div class="slds-form-element__control">
						<div class="slds-select_container">
							<input type="hidden" name="pick_module" value="{$listModule}" id="pick_module">
							<select class="slds-select slds-page-header__meta-text"" name="module_name" id="module_list">
								{foreach item=moduleName from=$moduleNames}
								<option value="{$moduleName}" {if $moduleName eq $listModule}selected{/if}>
									{$moduleName|@getTranslatedString:$moduleName}
								</option>
								{/foreach}
							</select>
						</div>
					</div>
				</div>
				<div class="slds-form-element" id="template_select_field" style="display:none;">
					<label class="slds-form-element__label slds-page-header__meta-text" for="module_list">{$MOD.LBL_CHOOSE_A_TEMPLATE}</label>
					<div class="slds-form-element__control">
						<div class="slds-select_container">
							<span id="template_list_busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
							<span id="template_list_foundnone" style='display:none;'><b>{$MOD.LBL_NO_TEMPLATES}</b></span>
							<select id="template_list" name="template_id" class="slds-select slds-page-header__meta-text"></select>
						</div>
					</div>
				</div>
				<input type="hidden" name="save_type" value="new">
				<input type="hidden" name="module" value="{$module->name}">
				<input type="hidden" name="action" value="editworkflow">
			</div>
		</div>
		<footer class="slds-modal__footer" style="width:100%;">
			<button class="slds-button slds-button_neutral" type="button" name="cancel" id='new_workflow_popup_cancel' onClick="hide('new_workflow_popup');">
			{$APP.LBL_CANCEL_BUTTON_LABEL}
			</button>
			<button class="slds-button slds-button_brand" type="submit" name="save" id='new_workflow_popup_save'>
			{$APP.LBL_CREATE_BUTTON_LABEL}
			</button>
		</footer>
	</div>
	</section>
	</form>
	<div class="slds-backdrop slds-backdrop_open"></div>
</div>
