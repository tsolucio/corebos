{include file='Buttons_List.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43" aria-modal="true">
<div class="slds-modal__container slds-p-around_none">
	<header class="slds-modal__header slds-grid slds-grid_align-spread slds-grid_vertical-align-center">
		<h2 id="header43" class="slds-text-heading_medium">
		<div class="slds-media__figure">
			<svg aria-hidden="true" class="slds-icon slds-icon-standard-user slds-m-right_small">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#sync"></use>
			</svg>
			{$TITLE_MESSAGE}
		</div>
		</h2>
	</header>
	<div class="slds-modal__content slds-app-launcher__content slds-p-around_medium">
	{if $ISADMIN}
		<form role="form" style="margin:0 100px;">
		<input type="hidden" name="module" value="Utilities">
		<input type="hidden" name="action" value="integration">
		<input type="hidden" name="_op" value="setconfigwhatsapp">
		<div class="slds-form-element">
			<label class="slds-checkbox--toggle slds-grid">
			<span class="slds-form-element__label slds-m-bottom--none">{'_active'|@getTranslatedString:$MODULE}</span>
			<input type="checkbox" name="whatsapp_active" aria-describedby="toggle-desc" {if $isActive}checked{/if} />
			<span id="toggle-desc" class="slds-checkbox--faux_container" aria-live="assertive">
				<span class="slds-checkbox--faux"></span>
				<span class="slds-checkbox--on">{'LBL_ENABLED'|@getTranslatedString:'Settings'}</span>
				<span class="slds-checkbox--off">{'LBL_DISABLED'|@getTranslatedString:'Settings'}</span>
			</span>
			</label>
		</div>
		<div class="slds-form-element slds-m-top--small">
			<label class="slds-form-element__label" for="sid">{'whatsapp_sid'|@getTranslatedString:$MODULE}</label>
			<div class="slds-form-element__control">
				<input type="text" id="sid" name="sid" class="slds-input" value="{$sid}" />
			</div>
		</div>
		<div class="slds-form-element slds-m-top--small">
			<label class="slds-form-element__label" for="token">{'whatsapp_token'|@getTranslatedString:$MODULE}</label>
			<div class="slds-form-element__control">
				<input type="text" id="token" name="token" class="slds-input" value="{$token}" />
			</div>
		</div>
		<div class="slds-form-element slds-m-top--small">
			<label class="slds-form-element__label" for="senderphone">{'whatsapp_senderphone'|@getTranslatedString:$MODULE}</label>
			<div class="slds-form-element__control">
				<input type="text" id="senderphone" name="senderphone" class="slds-input" value="{$senderphone}" />
			</div>
		</div>
		<div class="slds-m-top--large">
			<button type="submit" class="slds-button slds-button--brand">{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString:$MODULE}</button>
		</div>
		</form>
	{/if}
	</div>
</div>
</section>