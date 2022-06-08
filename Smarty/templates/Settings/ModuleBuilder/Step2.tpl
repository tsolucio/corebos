<article class="slds-setup-assistant__step" id="step-2" style="display: none">
    <div class="slds-setup-assistant__step-summary">
        <div class="slds-media">
            <div class="slds-media__body slds-m-top_x-small">
                <div class="slds-media">
                    <div class="slds-setup-assistant__step-summary-content slds-media__body">
                        <div style="width: 70%; margin:0 auto;">
                            <div class="slds-media__figure slds-media__figure_reverse">
                                <button class="slds-button slds-button_brand" onclick="mb.generateInput()" style="text-transform: uppercase;">
                                    {$MOD.LBL_MB_NEWBLOCK}&nbsp;
                                    <svg class="slds-icon slds-icon_small" aria-hidden="true">
                                        <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use>
                                    </svg>
                                </button>
                            </div>
                            <div class="slds-form-element">
                                <label class="slds-form-element__label" for="blocks_label">
                                    <abbr class="slds-required" title="required">* </abbr>{$MOD.LBL_MB_BLOCKLABEL}</label>
                                    <input type="hidden" id="BLOCK_COUNT" value="0">
                                <div class="slds-form-element__control" id="blocks_inputs">
                                </div>
                            </div>
                            <br>
                            <div id="loadBlocks"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br><br><br>
    <div class="slds-docked-form-footer">
        <button class="slds-button slds-button_text-destructive" onclick="mb.backTo(1)" style="color: white; background: #ce4949">
            <svg class="slds-icon slds-icon_small" aria-hidden="true">
                <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#back"></use>
            </svg>
            &nbsp;{$MOD.LBL_MB_BACK}
        </button>
        <button class="slds-button slds-button_success" onclick="mb.SaveModule(2);" style="color: white">{$MOD.LBL_MB_NEXT}&nbsp;
            <svg class="slds-icon slds-icon_small" aria-hidden="true">
                <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#forward"></use>
            </svg>
        </button>
    </div>
</article>