<article class="slds-setup-assistant__step" id="step-5" style="display: none">
    <div class="slds-setup-assistant__step-summary">
        <div class="slds-media">
            <div class="slds-media__body slds-m-top_x-small">
                <div class="slds-media">
                    <div class="slds-setup-assistant__step-summary-content slds-media__body">
                        <div class="slds-form-element">
                            <div class="slds-form-element__control">
                                <div class="slds-media__figure slds-media__figure_reverse">
                                    <button class="slds-button slds-button_success" onclick="mb.generateRelatedList()" style="color: white">{$MOD.LBL_MB_NEWRL}&nbsp;
                                    <svg class="slds-icon slds-icon_small" aria-hidden="true">
                                        <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use>
                                    </svg>
                                    </button>
                                    <input type="hidden" id="LIST_COUNT" value="0">
                                    <input type="hidden" name="uitype_no" id="uitype_no" class="slds-input"/>
                                </div>
                                <div class="slds-form-element">
                                    <table id="RelatedLists" style="margin: 10px"></table>
                                </div>
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
                                                        <span class="slds-page-header__title slds-truncate" title="{$MOD.LBL_CONDITIONS}">
                                                            <span class="slds-tabs__left-icon">
                                                                <span class="slds-icon_container" title="{$MOD.LBL_CONDITIONS}">
                                                                    <svg class="slds-icon slds-icon_small" style="color:green;" aria-hidden="true">
                                                                        <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#list"></use>
                                                                    </svg>
                                                                </span>
                                                            </span>
                                                            {$MOD.LBL_MB_LISTRLISTS}
                                                        </span>
                                                    </h1>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><br>
                        <div id="loadLists"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br><br><br>
    <div class="slds-docked-form-footer">
        <button class="slds-button slds-button_text-destructive" onclick="mb.backTo(4)" style="color: white; background: #ce4949">
            <svg class="slds-icon slds-icon_small" aria-hidden="true">
                <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#back"></use>
            </svg>
            &nbsp;{$MOD.LBL_MB_BACK}
        </button>
        <button class="slds-button slds-button_success" onclick="mb.SaveModule(5);" style="color: white">{$MOD.LBL_MB_FINISH}&nbsp;
            <svg class="slds-icon slds-icon_small" aria-hidden="true">
                <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#forward"></use>
            </svg>
        </button>
    </div>
</article>