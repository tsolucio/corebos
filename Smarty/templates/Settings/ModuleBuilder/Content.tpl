<button class="slds-button slds-button_neutral" onclick="ModuleBuilder.openModal()" style="float: right;">
    {$MOD.LBL_MB_ALLMODULES}
</button>
<hr><br>
<div id="moduleLists"></div>
<span id="progresstext" class="slds-badge" style="margin-bottom: 5px">{$MOD.LBL_MB_PROGRESS}: 0%</span>
<div class="slds-progress-bar" aria-valuemin="0" aria-valuemax="100" role="progressbar">
    <span class="slds-progress-bar__value" id="progress" style="width:0%">
    </span>
</div>
<ol class="slds-setup-assistant">
    <li class="slds-setup-assistant__item">
        <article class="slds-setup-assistant__step" id="step-1">
            <div class="slds-setup-assistant__step-summary">
                <div class="slds-media">
                    <div class="slds-media__figure">
                        <div class="slds-progress-ring slds-progress-ring_large">
                            <div class="slds-progress-ring__content">1</div>
                        </div>
                    </div>
                    <div class="slds-media__body slds-m-top_x-small">
                        <div class="slds-media">
                            <div class="slds-setup-assistant__step-summary-content slds-media__body">
                            <h3 class="slds-setup-assistant__step-summary-title slds-text-heading_small">{$MOD.LBL_MB_GENERAL}</h3>
                            <div class="slds-form-element">
                                <label class="slds-form-element__label" for="modulename">
                                    <abbr class="slds-required" title="required">* </abbr>{$MOD.LBL_MB_MODULENAME}
                                </label>
                                <div class="slds-form-element__control">
                                    <input type="text" id="modulename" placeholder="{$MOD.LBL_MB_MODULENAME}" onchange="ModuleBuilder.checkForModule(this.id);ModuleBuilder.updateProgress(this.id, 1)" required="" class="slds-input" />
                                </div>
                            </div>
                            <div class="slds-form-element">
                                <label class="slds-form-element__label" for="modulelabel">
                                    <abbr class="slds-required" title="required">* </abbr>{$MOD.LBL_MB_MODULELABEL}
                                </label>
                                <div class="slds-form-element__control">
                                    <input type="text" id="modulelabel" placeholder="{$MOD.LBL_MB_MODULELABEL}" onchange="ModuleBuilder.updateProgress(this.id, 1)" required="" class="slds-input" />
                                </div>
                                </div>
                                <div class="slds-form-element">
                                    <label class="slds-form-element__label" for="parentmenu">{$MOD.LBL_MB_PARENTMENU}</label>
                                    <div class="slds-form-element__control">
                                        <div class="slds-select_container">
                                            <select class="slds-select" id="parentmenu" onchange="ModuleBuilder.updateProgress(this.id, 1)">
                                                <option value="" disabled="" selected=""></option>
                                                {foreach from=$MENU item=m key=k}
                                                <option value="{$m}">{$m}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="slds-form-element">
                                    <label class="slds-form-element__label" for="moduleicon">{$MOD.LBL_MB_MODULEICON}
                                        <a href="https://www.lightningdesignsystem.com/icons/" class="slds-badge slds-theme_success slds-m-top_x-small slds-m-bottom_xx-small slds-m-left_small" target="_blank"> {$MOD.LBL_MB_LISTICONS}</a>
                                    </label>
                                    <div class="slds-form-element__control slds-grid slds-gutters">
                                    <div class="slds-col slds-size_1-of-12">
                                        <span class="slds-icon_container slds-icon-utility-announcement" id="moduleiconshow">
                                            <svg class="slds-icon slds-icon-text-default">
                                                <use xlink:href="" id="moduleiconshowsvg"></use>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="slds-col slds-size_11-of-12">
                                        <select class="slds-select" id="moduleicon" onchange="ModuleBuilder.updateProgress(this.id, 1);ModuleBuilder.showModuleIcon(this.value);">
                                            <option value="" disabled="" selected=""></option>
                                            {foreach from=$ICONS item=i key=k}
                                                <option value="{$i}">{$i}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="slds-media__figure slds-media__figure_reverse">
                                <button class="slds-button slds-button_success" disabled="true" onclick="ModuleBuilder.SaveModule(1);" id="btn-step-1" style="color: white">
                                    {$MOD.LBL_MB_NEXT}&nbsp;
                                    <svg class="slds-icon slds-icon--small" aria-hidden="true">
                                        <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#forward"></use>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </article>

        <article class="slds-setup-assistant__step" id="step-2" style="display: none;">
            <div class="slds-setup-assistant__step-summary">
                <div class="slds-media">
                    <div class="slds-media__figure">
                        <div class="slds-progress-ring slds-progress-ring_large">
                            <div class="slds-progress-ring__content">2</div>
                        </div>
                    </div>
                    <div class="slds-media__body slds-m-top_x-small">
                        <div class="slds-media">
                            <div class="slds-setup-assistant__step-summary-content slds-media__body">
                                <h3 class="slds-setup-assistant__step-summary-title slds-text-heading_small">{$MOD.LBL_MB_MODULEBLOCKS}</h3>
                                <div class="slds-media__figure slds-media__figure_reverse">
                                    <button class="slds-button slds-button_outline-brand" onclick="ModuleBuilder.generateInput()">{$MOD.LBL_MB_NEWBLOCK}</button>
                                </div>
                                <div class="slds-form-element">
                                    <label class="slds-form-element__label" for="blocks_label">
                                        <abbr class="slds-required" title="required">* </abbr>{$MOD.LBL_MB_BLOCKLABEL}</label>
                                    <div class="slds-form-element__control" id="blocks_inputs">
                                        <input type="hidden" id="number_block" value="1">
                                        <input type="text" id="blocks_label_1" placeholder="LBL_BLOCKNAME_INFORMATION" required="" class="slds-input" />
                                    </div>
                                </div>
                            </div>
                            <div class="slds-media__figure slds-media__figure_reverse">
                                <button class="slds-button slds-button_success" onclick="ModuleBuilder.SaveModule(2);" style="color: white">{$MOD.LBL_MB_NEXT}&nbsp;
                                    <svg class="slds-icon slds-icon--small" aria-hidden="true">
                                        <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#forward"></use>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </article>

        <article class="slds-setup-assistant__step" id="step-3" style="display: none">
            <div class="slds-setup-assistant__step-summary">
                <div class="slds-media">
                    <div class="slds-media__figure">
                        <div class="slds-progress-ring slds-progress-ring_large">
                            <div class="slds-progress-ring__content">3</div>
                        </div>
                    </div>
                    <div class="slds-media__body slds-m-top_x-small">
                        <div class="slds-media">
                            <div class="slds-setup-assistant__step-summary-content slds-media__body">
                                <h3 class="slds-setup-assistant__step-summary-title slds-text-heading_small">{$MOD.LBL_MB_MODULEFIELDS}</h3>
                                <div class="slds-form-element">
                                    <div class="slds-form-element__control">
                                        <div class="slds-media__figure slds-media__figure_reverse">
                                            <button class="slds-button slds-button_brand" onclick="ModuleBuilder.generateFields()">{$MOD.LBL_MB_ADDFIELD}&nbsp;
                                            <svg class="slds-icon slds-icon--small" aria-hidden="true">
                                                <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use>
                                            </svg>
                                            </button>
                                            <input type="hidden" id="number_field" value="0">
                                        </div>
                                        <div class="slds-form-element">
                                            <table id="Table"></table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="slds-media__figure slds-media__figure_reverse">
                                <button class="slds-button slds-button_success" onclick="ModuleBuilder.SaveModule(3);" style="color: white">{$MOD.LBL_MB_NEXT}&nbsp;
                                    <svg class="slds-icon slds-icon--small" aria-hidden="true">
                                        <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#forward"></use>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </article>

        <article class="slds-setup-assistant__step" id="step-4" style="display: none">
            <div class="slds-setup-assistant__step-summary">
                <div class="slds-media">
                    <div class="slds-media__figure">
                        <div class="slds-progress-ring slds-progress-ring_large">
                            <div class="slds-progress-ring__content">4</div>
                        </div>
                    </div>
                    <div class="slds-media__body slds-m-top_x-small">
                        <div class="slds-media">
                            <div class="slds-setup-assistant__step-summary-content slds-media__body">
                                <h3 class="slds-setup-assistant__step-summary-title slds-text-heading_small">{$MOD.LBL_MB_CVS}</h3>
                                <div class="slds-form-element">
                                    <div class="slds-form-element__control">
                                        <div class="slds-media__figure slds-media__figure_reverse">
                                            <button class="slds-button slds-button_brand" onclick="ModuleBuilder.generateCustomView()">{$MOD.LBL_MB_ADDCV}&nbsp;
                                            <svg class="slds-icon slds-icon--small" aria-hidden="true">
                                                <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use>
                                            </svg>
                                            </button>
                                            <input type="hidden" id="number_customview" value="0">
                                        </div>
                                        <div class="slds-form-element">
                                            <table id="CustomView"></table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="slds-media__figure slds-media__figure_reverse">
                                <button class="slds-button slds-button_success" onclick="ModuleBuilder.SaveModule(4);" style="color: white">{$MOD.LBL_MB_NEXT}&nbsp;
                                    <svg class="slds-icon slds-icon--small" aria-hidden="true">
                                        <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#forward"></use>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </li>
</ol>