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
                    <input type="hidden" name="_op" value="setconfigonesignal">
                    <div class="slds-form-element">
                        <label class="slds-checkbox_toggle slds-grid">
                            <span class="slds-form-element__label slds-m-bottom_none">{'_active'|@getTranslatedString:$MODULE}</span>
                            <input type="checkbox" name="onesignal_active" aria-describedby="toggle-desc" {if $isActive}checked{/if} />
                            <span id="toggle-desc" class="slds-checkbox_aux_container" aria-live="assertive">
                                <span class="slds-checkbox_faux"></span>
                                <span class="slds-checkbox_on">{'LBL_ENABLED'|@getTranslatedString:'Settings'}</span>
                                <span class="slds-checkbox_off">{'LBL_DISABLED'|@getTranslatedString:'Settings'}</span>
                            </span>
                        </label>
                    </div>
                    <div class="slds-form-element slds-m-top_small">
                        <label class="slds-form-element__label" for="appid">{'onesignal_appid'|@getTranslatedString:$MODULE}</label>
                        <div class="slds-form-element__control">
                            <input type="text" id="appid" name="appid" class="slds-input" value="{$appid}" />
                        </div>
                    </div>
                    <div class="slds-form-element slds-m-top_small">
                        <label class="slds-form-element__label" for="apikey">{'onesignal_apikey'|@getTranslatedString:$MODULE}</label>
                        <div class="slds-form-element__control">
                            <input type="text" id="apikey" name="apikey" class="slds-input" value="{$apikey}" />
                        </div>
                    </div>
                    <div class="slds-m-top_large">
                        <button type="submit" class="slds-button slds-button_brand">{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString:$MODULE}</button>
                        {if $isActive}
                            <input type="hidden" id="testit" name="testit" value="0" />
                            <button type="submit" class="slds-button slds-button_success" onclick="document.getElementById('testit').value=1;">{'Test'|@getTranslatedString:'GlobalVariable'}</button>
                        {/if}
                    </div>
                </form>
            {/if}
        </div>
    </div>
</section>