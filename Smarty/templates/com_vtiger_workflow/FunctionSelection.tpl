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

{assign var='MODAL' value=['label'=>$MOD.LBL_FUNCTIONS, 'ariaDescribe'=>$MOD.LBL_FUNCTIONS, 'hideID'=>'selectfunction']}
{extends file='Components/Modal.tpl'}
{block name=ModalContent}
	<input type="hidden" id="selectedfunction">
	<div class="slds-p-around_x-small slds-grid slds-gutters">
		<div class="slds-col slds-size_4-of-12 slds-text-align_left">
			<fieldset class="slds-form-element">
				<div class="slds-form-element__control slds-input-has-icon slds-input-has-icon_right">
					<svg class="slds-icon slds-input__icon slds-input__icon_right slds-icon-text-default" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
					</svg>
					<input type="text" placeholder="{'LBL_SEARCH'|@getTranslatedString}" class="slds-input" id="fnfiltersrch" onkeyup="wffnFilterSearch(this.value);" />
				</div>
			</fieldset>
			<fieldset class="slds-form-element">
				<legend class="slds-form-element__legend slds-form-element__label">{'Category'|@getTranslatedString}</legend>
				<div class="slds-form-element__control">
					<div class="slds-select_container">
						<select class="slds-select" id="fnfiltercat" onchange="wffnFilterCategories(this.value);">
						{foreach from=$FNCATS item=item key=key}
							<option value="{$key}" {if $key=='All'}selected{/if}>{$item}</option>
						{/foreach}
						</select>
					</div>
				</div>
			</fieldset>
			<div>
				<ul aria-label="single select listbox" class="slds-border_top slds-border_right slds-border_bottom slds-border_left slds-m-top_small" role="listbox" id="wffnlist" style="height:350px;overflow-y:scroll;">
				{foreach from=$FNDEFS item=item key=key}
					<li aria-selected="false" class="slds-p-around_xx-small" draggable="false" role="option" tabindex="-1" onclick="setFunctionInformation(this);" ondblclick="dblClickFunctionSelect(this);" data-value="{$key}">{$key}</li>
				{/foreach}
				</ul>
			</div>
		</div>
		<div class="slds-col slds-size_8-of-12 slds-text-align_left">
			<h2 class="slds-expression__title" id="funcname"></h2>
			<div class="slds-page-header__meta-text slds-m-top_small" id="funcdesc"></div>
			<div class="slds-coordinates__title slds-m-top_small">{'FunctionParams'|@getTranslatedString:'cbMap'}</div>
			<div id="funcparams"></div>
			<div class="slds-coordinates__title slds-m-top_small">{'Examples'|@getTranslatedString:'cbMap'}</div>
			<div class="slds-page-header__meta-text slds-m-top_small" id="funcex"></div>
		</div>
	</div>
{/block}
{block name=ModalFooter}
<button name="cancel" id='editpopup_cancel' type="button" onClick="hide('selectfunction');" class="slds-button slds-button_text-destructive" >{$APP.LBL_CANCEL_BUTTON_LABEL}</button>
<button class="slds-button slds-button_neutral" onClick="return setSelectedFunction('{$FillInField}');" id="wffnselectbutton">{$APP.LBL_SELECT}</button>
{/block}