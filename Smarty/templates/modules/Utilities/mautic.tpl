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
		<input type="hidden" name="_op" value="setconfigmautic">
		<div class="slds-form-element">
			<label class="slds-checkbox_toggle slds-grid">
			<span class="slds-form-element__label slds-m-bottom_none">{'_active'|@getTranslatedString:$MODULE}</span>
			<input type="checkbox" name="mautic_active" aria-describedby="toggle-desc" {if $isActive}checked{/if} />
			<span id="toggle-desc" class="slds-checkbox_faux_container" aria-live="assertive">
				<span class="slds-checkbox_faux"></span>
				<span class="slds-checkbox_on">{'LBL_ENABLED'|@getTranslatedString:'Settings'}</span>
				<span class="slds-checkbox_off">{'LBL_DISABLED'|@getTranslatedString:'Settings'}</span>
			</span>
			</label>
		</div>
		<div class="slds-form-element slds-m-top_small">
			<label class="slds-form-element__label" for="baseUrl">{'mautic_baseurl'|@getTranslatedString:$MODULE}</label>
			<div class="slds-form-element__control">
				<input type="text" id="baseUrl" name="baseUrl" class="slds-input" value="{$baseUrl}" />
			</div>
		</div>
		<div class="slds-form-element slds-m-top_small">
			<label class="slds-form-element__label" for="version">{'mautic_version'|@getTranslatedString:$MODULE}</label>
			<div class="slds-form-element__control">
				<select id="version" name="version" class="slds-input">
					<option value="BasicAuth" {if $version eq 'BasicAuth'}checked{/if}>{'mautic_version_basicauth'|@getTranslatedString:$MODULE}</option>
					<option value="OAuth2" {if $version eq 'Oauth2'}checked{/if} disabled>{'mautic_version_oauth2'|@getTranslatedString:$MODULE}</option>
				</select>
			</div>
		</div>

		<div class="basic-auth-fields">
			<div class="slds-form-element slds-m-top_small">
				<label class="slds-form-element__label" for="mautic_username">{'mautic_username'|@getTranslatedString:$MODULE}</label>
				<div class="slds-form-element__control">
					<input type="text" id="mautic_username" name="mautic_username" class="slds-input" value="{$mauticUsername}" />
				</div>
			</div>
			<div class="slds-form-element slds-m-top_small">
				<label class="slds-form-element__label" for="mautic_password">{'mautic_password'|@getTranslatedString:$MODULE}</label>
				<div class="slds-form-element__control">
					<input type="text" id="mautic_password" name="mautic_password" class="slds-input" value="{$mauticPassword}" />
				</div>
			</div>
		</div>
		
		<div class="oauth2-fields" style="display: none;">
			<div class="slds-form-element slds-m-top_small">
				<label class="slds-form-element__label" for="clientkey">{'mautic_clientkey'|@getTranslatedString:$MODULE}</label>
				<div class="slds-form-element__control">
					<input type="text" id="clientKey" name="clientKey" class="slds-input" value="{$clientKey}" />
				</div>
			</div>
			<div class="slds-form-element slds-m-top_small">
				<label class="slds-form-element__label" for="clientSecret">{'mautic_clientsecret'|@getTranslatedString:$MODULE}</label>
				<div class="slds-form-element__control">
					<input type="text" id="clientSecret" name="clientSecret" class="slds-input" value="{$clientSecret}" />
				</div>
			</div>
			<div class="slds-form-element slds-m-top_small">
				<label class="slds-form-element__label" for="callback">{'mautic_callback'|@getTranslatedString:$MODULE}</label>
				<div class="slds-form-element__control">
					<input type="text" id="callback" name="callback" class="slds-input" value="{$callback}" />
				</div>
			</div>
		</div>

		<div class="slds-form-element slds-m-top_small">
			<label class="slds-form-element__label" for="mautic_webhook_secret">{'mautic_webhook_secret'|@getTranslatedString:$MODULE}</label>
			<div class="slds-form-element__control">
				<input type="text" id="mautic_webhook_secret" name="mautic_webhook_secret" class="slds-input" value="{$mauticWebhookSecret}" />
			</div>
		</div>

		<div class="slds-grid slds-gutters slds-m-top_medium">
			<div class="slds-col">
				<div class="slds-form-element">
					<label class="slds-checkbox_toggle slds-grid">
					<span class="slds-form-element__label slds-m-bottom_none">{'mautic_sync_with_contacts'|@getTranslatedString:$MODULE}</span>
					<input type="checkbox" name="mautic_sync_lead" aria-describedby="toggle-desc" {if $isLeadSyncActive}checked{/if} />
					<span id="toggle-desc" class="slds-checkbox_faux_container" aria-live="assertive">
						<span class="slds-checkbox_faux"></span>
						<span class="slds-checkbox_on">{'LBL_ENABLED'|@getTranslatedString:'Settings'}</span>
						<span class="slds-checkbox_off">{'LBL_DISABLED'|@getTranslatedString:'Settings'}</span>
					</span>
					</label>
				</div>
			</div>

			<div class="slds-col">
				<div class="slds-form-element">
					<label class="slds-checkbox_toggle slds-grid">
					<span class="slds-form-element__label slds-m-bottom_none">{'mautic_sync_with_accounts'|@getTranslatedString:$MODULE}</span>
					<input type="checkbox" name="mautic_sync_companies" aria-describedby="toggle-desc" {if $isCompaniesSyncActive}checked{/if} />
					<span id="toggle-desc" class="slds-checkbox_faux_container" aria-live="assertive">
						<span class="slds-checkbox_faux"></span>
						<span class="slds-checkbox_on">{'LBL_ENABLED'|@getTranslatedString:'Settings'}</span>
						<span class="slds-checkbox_off">{'LBL_DISABLED'|@getTranslatedString:'Settings'}</span>
					</span>
					</label>
				</div>
			</div>
		</div>
		<div class="slds-m-top_large">
			<button type="submit" class="slds-button slds-button_brand">{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString:$MODULE}</button>
		</div>
		</form>
	{/if}
	</div>
</div>
</section>