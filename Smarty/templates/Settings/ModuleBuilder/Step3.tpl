<style>
.slds-dropdown__scroll::-webkit-scrollbar {
  width: 8px;
}

.slds-dropdown__scroll::-webkit-scrollbar-track {
  box-shadow: inset 0 0 5px grey; 
  border-radius: 5px;
}
 
.slds-dropdown__scroll::-webkit-scrollbar-thumb {
  background: grey; 
  border-radius: 5px;
}

.slds-dropdown__scroll::-webkit-scrollbar-thumb:hover {
  background: #d3d3d3; 
}
</style>
<article class="slds-setup-assistant__step" id="step-3" style="display: none">
    <div class="slds-setup-assistant__step-summary">
        <div class="slds-media">
            <div class="slds-media__body slds-m-top_x-small">
                <div class="slds-media">
                    <div class="slds-setup-assistant__step-summary-content slds-media__body" style="width: 70%; margin:0 auto;">
                        <div class="slds-form-element">
                            <div class="slds-form-element__control">
                                <div class="slds-media__figure slds-media__figure_reverse">
                                    <button class="slds-button slds-button_success" onclick="mb.generateFields()" style="color: white">{$MOD.LBL_MB_ADDFIELD}&nbsp;
                                        <svg class="slds-icon slds-icon_small" aria-hidden="true">
                                            <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use>
                                        </svg>
                                    </button>
                                      <a href="javascript:void(0)" onmouseover="mb.showInformation('field-help')"  onmouseout="mb.hideInformation('field-help')">
                                        <span class="slds-icon_container slds-icon-utility-info">
                                          <svg class="slds-icon slds-icon slds-icon_xx-small slds-icon-text-default" aria-hidden="true">
                                            <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info"></use>
                                          </svg>
                                        </span>
                                      </a>
                                    <div>
                                      <div class="slds-popover slds-popover_tooltip slds-nubbin_bottom-left" role="tooltip" id="field-help" style="position:fixed;display: none;overflow:hidden">
                                        <div class="slds-popover__body">
                                            Default fields for module <i>assigned_user_id</i>, <i>created_user_id</i>, <i>createdtime</i>, <i>modifiedtime</i> and <i>description</i> are created automatically!
                                        </div>
                                      </div>
                                    </div>
                                    <input type="hidden" id="FIELD_COUNT" value="0">
                                </div>
                                <div class="slds-form-element">
                                    <table id="Table"></table>
                                </div>
                                <br>
                            </div>
                        </div>
                        <br><br>
                        <div class="slds-grid slds-gutters">
                            <div class="slds-col slds-size_1-of-1">
                                <div class="slds-page-header__col-title">
                                    <div class="slds-media">
                                        <div class="slds-media__body">
                                            <div class="slds-page-header__name">
                                                <div class="slds-page-header__name-title">
                                                    <h1>
                                                        <span class="slds-page-header__title slds-truncate">
                                                            <span class="slds-tabs__left-icon">
                                                                <span class="slds-icon_container">
                                                                    <svg class="slds-icon slds-icon_small" style="color:green;" aria-hidden="true">
                                                                        <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#list"></use>
                                                                    </svg>
                                                                </span>
                                                            </span>
                                                            {$MOD.LBL_MB_LISTFIELDS}
                                                        </span>
                                                    </h1>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div id="loadFields"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br><br><br>
    <div class="slds-docked-form-footer">
        <button class="slds-button slds-button_text-destructive" onclick="mb.backTo(2)" style="color: white; background: #ce4949">
            <svg class="slds-icon slds-icon_small" aria-hidden="true">
                <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#back"></use>
            </svg>
            &nbsp;{$MOD.LBL_MB_BACK}
        </button>
        <button class="slds-button slds-button_success" onclick="mb.SaveModule(3);" style="color: white">{$MOD.LBL_MB_NEXT}&nbsp;
            <svg class="slds-icon slds-icon_small" aria-hidden="true">
                <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#forward"></use>
            </svg>
        </button>
    </div>
</article>