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
		<input type="hidden" name="_op" value="setconfigsendgrid">
		<div class="slds-form-element">
			<label class="slds-checkbox_toggle slds-grid">
			<span class="slds-form-element__label slds-m-bottom_none">{'_active'|@getTranslatedString:$MODULE}</span>
			<input type="checkbox" name="sendgrid_active" aria-describedby="toggle-desc" {if $isActive}checked{/if} />
			<span id="toggle-desc" class="slds-checkbox_faux_container" aria-live="assertive">
				<span class="slds-checkbox_faux"></span>
				<span class="slds-checkbox_on">{'LBL_ENABLED'|@getTranslatedString:'Settings'}</span>
				<span class="slds-checkbox_off">{'LBL_DISABLED'|@getTranslatedString:'Settings'}</span>
			</span>
			</label>
		</div>
		<br />
		<br />
		<div class="slds-grid slds-gutters">
			<div class="slds-col slds-size_1-of-2">
				<h1 class="slds-page-header__title">{'TransEmail_title'|@getTranslatedString:'vtsendgrid'}</h1>
				<h2 class="small">{'TransEmail_subtitle'|@getTranslatedString:'vtsendgrid'}</h2>
				<hr />
				<br />
				<div class="slds-form-element">
					<div class="slds-form-element__control">
						<div class="slds-checkbox">
						<input type="checkbox" name="usesg_transactional" id="usesg_transactional" {if $usesg_transactional}checked{/if}/>
						<label class="slds-checkbox__label" for="usesg_transactional">
							<span class="slds-checkbox_faux"></span>
							<span class="slds-form-element__label">{'Active'|@getTranslatedString:$MODULE}</span>
						</label>
						</div>
					</div>
				</div>
				<div class="slds-form-element slds-m-top_small">
					<font color="red">*</font>&nbsp;
					<label class="slds-form-element__label" for="srv_transactional">{'LBL_OUTGOING_MAIL_SERVER'|@getTranslatedString:'Settings'}</label>
					<div class="slds-form-element__control">
						<input type="text" id="srv_transactional" name="srv_transactional" class="slds-input" value="{$srv_transactional}" />
					</div>
				</div>
				<div class="slds-form-element slds-m-top_small">
					<label class="slds-form-element__label" for="user_transactional">{'LBL_USERNAME'|@getTranslatedString:'Settings'}</label>
					<div class="slds-form-element__control">
						<input type="text" id="user_transactional" name="user_transactional" class="slds-input" value="{$user_transactional}" />
					</div>
				</div>
				<div class="slds-form-element slds-m-top_small">
					<label class="slds-form-element__label" for="pass_transactional">{'LBL_PASWRD'|@getTranslatedString:'Settings'}</label>
					<div class="slds-form-element__control">
						<input type="password" id="pass_transactional" name="pass_transactional" class="slds-input" value="{$pass_transactional}" />
					</div>
				</div>
				<div class="slds-form-element slds-m-top_small">
					<label class="slds-form-element__label" for="apiurl_transactional">{'LBL_API_URL'|@getTranslatedString:$MODULE}</label>
					<div class="slds-form-element__control">
						<input type="text" id="apiurl_transactional" name="apiurl_transactional" class="slds-input" value="{$apiurl_transactional}" />
					</div>
				</div>
			</div>
			<div class="slds-col slds-size_1-of-2">
				<h1 class="slds-page-header__title">{'MktEmail_title'|@getTranslatedString:'vtsendgrid'}</h1>
				<h2 class="small">{'MktEmail_subtitle'|@getTranslatedString:'vtsendgrid'}</h2>
				<hr />
				<br />
				<div class="slds-form-element">
					<div class="slds-form-element__control">
						<div class="slds-checkbox">
						<input type="checkbox" name="usesg_marketing" id="usesg_marketing" {if $usesg_marketing}checked{/if}/>
						<label class="slds-checkbox__label" for="usesg_marketing">
							<span class="slds-checkbox_faux"></span>
							<span class="slds-form-element__label">{'Active'|@getTranslatedString:$MODULE}</span>
						</label>
						</div>
					</div>
				</div>
				<div class="slds-form-element slds-m-top_small">
					<font color="red">*</font>&nbsp;
					<label class="slds-form-element__label" for="srv_marketing">{'LBL_OUTGOING_MAIL_SERVER'|@getTranslatedString:'Settings'}</label>
					<div class="slds-form-element__control">
						<input type="text" id="srv_marketing" name="srv_marketing" class="slds-input" value="{$srv_marketing}" />
					</div>
				</div>
				<div class="slds-form-element slds-m-top_small">
					<label class="slds-form-element__label" for="user_marketing">{'LBL_USERNAME'|@getTranslatedString:'Settings'}</label>
					<div class="slds-form-element__control">
						<input type="text" id="user_marketing" name="user_marketing" class="slds-input" value="{$user_marketing}" />
					</div>
				</div>
				<div class="slds-form-element slds-m-top_small">
					<label class="slds-form-element__label" for="pass_marketing">{'LBL_PASWRD'|@getTranslatedString:'Settings'}</label>
					<div class="slds-form-element__control">
						<input type="password" id="pass_marketing" name="pass_marketing" class="slds-input" value="{$pass_marketing}" />
					</div>
				</div>
			</div>
		</div>
		<br />
		<div class="slds-m-top_large">
			<button type="submit" class="slds-button slds-button_brand">{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString:$MODULE}</button>
		</div>
		</form>
	{/if}
	</div>
</div>
</section>