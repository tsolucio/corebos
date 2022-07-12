{include file='Buttons_List.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-modal="true">
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
                    <input type="hidden" name="_op" value="setconfigfacebook">
                    <div class="slds-form-element">
                        <label class="slds-checkbox_toggle slds-grid">
                            <span class="slds-form-element__label slds-m-bottom_none">{'_active'|@getTranslatedString:$MODULE}</span>
                            <input type="checkbox" name="facebook_active" aria-describedby="toggle-desc" {if $isActive}checked{/if} />
                            <span id="toggle-desc" class="slds-checkbox_faux_container" aria-live="assertive">
                                <span class="slds-checkbox_faux"></span>
                                <span class="slds-checkbox_on">{'LBL_ENABLED'|@getTranslatedString:'Settings'}</span>
                                <span class="slds-checkbox_off">{'LBL_DISABLED'|@getTranslatedString:'Settings'}</span>
                            </span>
                        </label>
                    </div>
                    <div class="slds-form-element slds-m-top_small">
                        <label class="slds-form-element__label" for="fb_hub_verification_token">{'facebook_hub_verification_token'|@getTranslatedString:$MODULE}</label>
                        <div class="slds-form-element__control">
                            <input type="text" id="fb_hub_verification_token" name="fb_hub_verification_token" class="slds-input" value="{$fbHubVerificationToken}" />
                        </div>
                    </div>
                    <div class="slds-form-element slds-m-top_small">
                        <label class="slds-form-element__label" for="fb_access_token">{'facebook_access_token'|@getTranslatedString:$MODULE}</label>
                        <div class="slds-form-element__control">
                            <input type="text" id="fb_access_token" name="fb_access_token" class="slds-input" value="{$fbAccessToken}" />
                        </div>
                    </div>
                    <div class="slds-form-element slds-m-top_small">
                        <label class="slds-form-element__label" for="fb_destination_module">{'facebook_destination_module'|@getTranslatedString:$MODULE}</label>
                        <div class="slds-form-element__control">
                            <input type="text" id="fb_destination_module" name="fb_destination_module" class="slds-input" value="{$fbDestinationModule}" />
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