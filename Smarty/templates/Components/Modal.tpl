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

{if empty($MODAL.size)}
	{$MODAL.size = 'small'}
{/if}
{if isset($MODAL.modalID)}
<div id="{$MODAL.modalID}" style="display:none;">
{/if}
<section role="dialog" tabindex="-1" class="slds-modal slds-fade-in-open slds-modal_{$MODAL.size}" aria-labelledby="ModalHeadingID" aria-modal="true" aria-describedby="{if isset($MODAL.ariaDescribe)}{$MODAL.ariaDescribe}{/if}">
<div class="slds-modal__container">
	<header class="slds-modal__header">
		<button type="button" class="slds-button slds-button_icon slds-modal__close slds-button_icon-inverse" title="{$APP.LBL_CLOSE}" onClick="hide('{$MODAL.hideID}');">
			<svg class="slds-button__icon slds-button__icon_large" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
			</svg>
			<span class="slds-assistive-text">{$APP.LBL_CLOSE}</span>
		</button>
		<h2 id="ModalHeadingID" class="slds-modal__title slds-hyphenate slds-page-header__title">{$MODAL.label}</h2>
	</header>
	<div class="slds-modal__content slds-p-around_medium">
	{block name=ModalContent}Nothing to say!{/block}
	</div>
	<footer class="slds-modal__footer" style="width:100%;">
	{block name=ModalFooter}&nbsp;{/block}
	</footer>
</div>
</section>
<div class="slds-backdrop slds-backdrop_open"></div>
{if isset($MODAL.modalID)}
</div>
{/if}
