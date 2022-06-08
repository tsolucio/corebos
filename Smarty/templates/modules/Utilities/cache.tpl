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
                <form role="form" style="margin:0 100px;" id="cache_form">
                    <input type="hidden" name="module" value="Utilities">
                    <input type="hidden" name="action" value="integration">
                    <input type="hidden" name="_op" value="setconfigcache">
                    <div class="slds-form-element">
                        <label class="slds-checkbox--toggle slds-grid">
                            <span class="slds-form-element__label slds-m-bottom--none">{'_active'|@getTranslatedString:$MODULE}</span>
                            <input type="checkbox" name="cache_active" aria-describedby="toggle-desc" {if $isActive}checked{/if} />
                            <span id="toggle-desc" class="slds-checkbox--faux_container" aria-live="assertive">
				<span class="slds-checkbox--faux"></span>
				<span class="slds-checkbox--on">{'LBL_ENABLED'|@getTranslatedString:'Settings'}</span>
				<span class="slds-checkbox--off">{'LBL_DISABLED'|@getTranslatedString:'Settings'}</span>
			</span>
                        </label>
                    </div>
                    <div class="slds-form-element slds-m-top--small" id="ic-div-server-type">
                        <label class="slds-form-element__label" for="adapter_type">{'cache_select_adapter_type'|@getTranslatedString:$MODULE}</label>
                        <div class="slds-form-element__control">
                            <select id="adapter_type" name="adapter_type" class="slds-input" onchange="Utilities.cache_control_input_visibility(this);">
                                <option value='memory' {if $adapter eq 'memory'} selected {/if}>{'cache_adapter_memory'|@getTranslatedString:$MODULE}</option>
                                <option value='redis' {if $adapter eq 'redis'} selected {/if}>{'cache_adapter_redis'|@getTranslatedString:$MODULE}</option>
                                <option value='memcached' {if $adapter eq 'memcached'} selected {/if}>{'cache_adapter_memcached'|@getTranslatedString:$MODULE}</option>
                            </select>
                        </div>
                    </div>
                    <div id="ip_port_container" {if $adapter eq 'memory'} style="display: none;" {/if}>
                        <div class="slds-form-element slds-m-top--small">
                            <font color="red">*</font>&nbsp;
                            <label class="slds-form-element__label" for="ip">{'cache_ip'|@getTranslatedString:$MODULE}</label>
                            <div class="slds-form-element__control">
                                <input type="text" id="ip" name="ip" class="slds-input" value="{$ip}" />
                                <div id="ip_required_message" style="color: red; display: none;">{'cache_ip_required'|@getTranslatedString:$MODULE}</div>
                            </div>
                        </div>
                        <div class="slds-form-element slds-m-top--small">
                            <font color="red">*</font>&nbsp;
                            <label class="slds-form-element__label" for="port">{'cache_port'|@getTranslatedString:$MODULE}</label>
                            <div class="slds-form-element__control">
                                <input type="text" id="port" name="port" class="slds-input" value="{$port}" />
                                <div id="port_required_message" style="color: red; display: none;">{'cache_port_required'|@getTranslatedString:$MODULE}</div>
                            </div>
                        </div>
                    </div>
                    <div class="slds-m-top--large">
                        <button type="button" class="slds-button slds-button--brand" onclick="Utilities.cache_form_submit_validation(this);">{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString:$MODULE}</button>
                    </div>
                </form>
            {/if}
        </div>
    </div>
</section>