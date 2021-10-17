{if isset($smarty.cookies.ModuleBuilderID)}
  {assign var=ModuleBuilderID value=$smarty.cookies.ModuleBuilderID}
{else}
  {assign var=ModuleBuilderID value=''}
{/if}
<article class="slds-setup-assistant__step" id="step-6" style="display: none">
    <div class="slds-setup-assistant__step-summary">
        <div class="slds-media">
            <div class="slds-media__figure">
                <div class="slds-progress-ring slds-progress-ring_large">
                    <div class="slds-progress-ring__content">6</div>
                </div>
            </div>
            <div class="slds-media__body slds-m-top_x-small">
                <div class="slds-media">
                    <div class="slds-setup-assistant__step-summary-content slds-media__body">
                        <h3 class="slds-setup-assistant__step-summary-title slds-text-heading_small">{$MOD.LBL_MB_FINISHMODULE}</h3>
                        <div class="slds-form-element">
                            <ol class="slds-setup-assistant">
                              <li class="slds-setup-assistant__item">
                                <article class="slds-setup-assistant__step">
                                  <div class="slds-setup-assistant__step-summary">
                                    <div class="slds-media">
                                      <div class="slds-media__figure">
                                        <div class="slds-progress-ring slds-progress-ring_complete slds-progress-ring_large">
                                          <div class="slds-progress-ring__progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="100">
                                            <svg viewBox="-1 -1 2 2">
                                              <circle class="slds-progress-ring__path" id="slds-progress-ring-path-66" cx="0" cy="0" r="1"></circle>
                                            </svg>
                                          </div>
                                          <div class="slds-progress-ring__content">
                                            <span class="slds-icon_container slds-icon-utility-check" title="{$MOD.LBL_MB_COMPLETE}">
                                              <svg class="slds-icon" aria-hidden="true">
                                                <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#check"></use>
                                              </svg>
                                              <span class="slds-assistive-text">{$MOD.LBL_MB_COMPLETE}</span>
                                            </span>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="slds-media__body slds-m-top_x-small">
                                        <div class="slds-media">
                                          <div class="slds-setup-assistant__step-summary-content slds-media__body">
                                            <h3 class="slds-setup-assistant__step-summary-title slds-text-heading_small">{$MOD.LBL_MB_MODULEINFO}</h3>
                                            <div id="info"></div>
                                          </div>
                                          <div class="slds-media__figure slds-media__figure_reverse">
                                            <button class="slds-button slds-button_neutral slds-button_dual-stateful" onclick="mb.backTo(1, true, {$ModuleBuilderID});">
                                                <svg class="slds-button__icon slds-button__icon_small slds-button__icon_left" aria-hidden="true">
                                                    <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
                                                </svg>
                                                {$MOD.LBL_MB_EDITINFO}
                                            </button>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </article>
                              </li>
                              <li class="slds-setup-assistant__item">
                                <article class="slds-setup-assistant__step">
                                  <div class="slds-setup-assistant__step-summary">
                                    <div class="slds-media">
                                      <div class="slds-media__figure">
                                        <div class="slds-progress-ring slds-progress-ring_complete slds-progress-ring_large">
                                          <div class="slds-progress-ring__progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="100">
                                            <svg viewBox="-1 -1 2 2">
                                              <circle class="slds-progress-ring__path" id="slds-progress-ring-path-66" cx="0" cy="0" r="1"></circle>
                                            </svg>
                                          </div>
                                          <div class="slds-progress-ring__content">
                                            <span class="slds-icon_container slds-icon-utility-check" title="{$MOD.LBL_MB_COMPLETE}">
                                              <svg class="slds-icon" aria-hidden="true">
                                                <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#check"></use>
                                              </svg>
                                              <span class="slds-assistive-text">{$MOD.LBL_MB_COMPLETE}</span>
                                            </span>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="slds-media__body slds-m-top_x-small">
                                        <div class="slds-media">
                                          <div class="slds-setup-assistant__step-summary-content slds-media__body">
                                            <h3 class="slds-setup-assistant__step-summary-title slds-text-heading_small">{$MOD.LBL_MB_MODULEBLOCKS}</h3>
                                            <div id="blocks"></div>
                                          </div>
                                          <div class="slds-media__figure slds-media__figure_reverse">
                                            <button class="slds-button slds-button_neutral slds-button_dual-stateful" onclick="mb.backTo(2, true, {$ModuleBuilderID});">
                                                <svg class="slds-button__icon slds-button__icon_small slds-button__icon_left" aria-hidden="true">
                                                    <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
                                                </svg>
                                                {$MOD.LBL_MB_EDITBLOCKS}
                                            </button>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </article>
                              </li>
                              <li class="slds-setup-assistant__item">
                                <article class="slds-setup-assistant__step">
                                  <div class="slds-setup-assistant__step-summary">
                                    <div class="slds-media">
                                      <div class="slds-media__figure">
                                        <div class="slds-progress-ring slds-progress-ring_complete slds-progress-ring_large">
                                          <div class="slds-progress-ring__progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="100">
                                            <svg viewBox="-1 -1 2 2">
                                              <circle class="slds-progress-ring__path" id="slds-progress-ring-path-66" cx="0" cy="0" r="1"></circle>
                                            </svg>
                                          </div>
                                          <div class="slds-progress-ring__content">
                                            <span class="slds-icon_container slds-icon-utility-check" title="{$MOD.LBL_MB_COMPLETE}">
                                              <svg class="slds-icon" aria-hidden="true">
                                                <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#check"></use>
                                              </svg>
                                              <span class="slds-assistive-text">{$MOD.LBL_MB_COMPLETE}</span>
                                            </span>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="slds-media__body slds-m-top_x-small">
                                        <div class="slds-media">
                                          <div class="slds-setup-assistant__step-summary-content slds-media__body">
                                            <h3 class="slds-setup-assistant__step-summary-title slds-text-heading_small">{$MOD.LBL_MB_MODULEFIELDS}</h3>
                                            <div id="fields"></div>
                                          </div>
                                          <div class="slds-media__figure slds-media__figure_reverse">
                                            <button class="slds-button slds-button_neutral slds-button_dual-stateful" onclick="mb.backTo(3, true, {$ModuleBuilderID});">
                                                <svg class="slds-button__icon slds-button__icon_small slds-button__icon_left" aria-hidden="true">
                                                    <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
                                                </svg>
                                                {$MOD.LBL_MB_EDITFIELDS}
                                            </button>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </article>
                              </li>
                              <li class="slds-setup-assistant__item">
                                <article class="slds-setup-assistant__step">
                                  <div class="slds-setup-assistant__step-summary">
                                    <div class="slds-media">
                                      <div class="slds-media__figure">
                                        <div class="slds-progress-ring slds-progress-ring_complete slds-progress-ring_large">
                                          <div class="slds-progress-ring__progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="100">
                                            <svg viewBox="-1 -1 2 2">
                                              <circle class="slds-progress-ring__path" id="slds-progress-ring-path-66" cx="0" cy="0" r="1"></circle>
                                            </svg>
                                          </div>
                                          <div class="slds-progress-ring__content">
                                            <span class="slds-icon_container slds-icon-utility-check" title="{$MOD.LBL_MB_COMPLETE}">
                                              <svg class="slds-icon" aria-hidden="true">
                                                <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#check"></use>
                                              </svg>
                                              <span class="slds-assistive-text">{$MOD.LBL_MB_COMPLETE}</span>
                                            </span>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="slds-media__body slds-m-top_x-small">
                                        <div class="slds-media">
                                          <div class="slds-setup-assistant__step-summary-content slds-media__body">
                                            <h3 class="slds-setup-assistant__step-summary-title slds-text-heading_small">{$MOD.LBL_MB_MODULECVS}</h3>
                                            <div id="views"></div>
                                          </div>
                                          <div class="slds-media__figure slds-media__figure_reverse">
                                            <button class="slds-button slds-button_neutral slds-button_dual-stateful" onclick="mb.backTo(4, true, {$ModuleBuilderID});">
                                                <svg class="slds-button__icon slds-button__icon_small slds-button__icon_left" aria-hidden="true">
                                                    <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
                                                </svg>
                                                {$MOD.LBL_MB_EDITCVS}
                                            </button>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </article>
                              </li>
                              <li class="slds-setup-assistant__item">
                                <article class="slds-setup-assistant__step">
                                  <div class="slds-setup-assistant__step-summary">
                                    <div class="slds-media">
                                      <div class="slds-media__figure">
                                        <div class="slds-progress-ring slds-progress-ring_complete slds-progress-ring_large">
                                          <div class="slds-progress-ring__progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="100">
                                            <svg viewBox="-1 -1 2 2">
                                              <circle class="slds-progress-ring__path" id="slds-progress-ring-path-66" cx="0" cy="0" r="1"></circle>
                                            </svg>
                                          </div>
                                          <div class="slds-progress-ring__content">
                                            <span class="slds-icon_container slds-icon-utility-check" title="{$MOD.LBL_MB_COMPLETE}">
                                              <svg class="slds-icon" aria-hidden="true">
                                                <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#check"></use>
                                              </svg>
                                              <span class="slds-assistive-text">{$MOD.LBL_MB_COMPLETE}</span>
                                            </span>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="slds-media__body slds-m-top_x-small">
                                        <div class="slds-media">
                                          <div class="slds-setup-assistant__step-summary-content slds-media__body">
                                            <h3 class="slds-setup-assistant__step-summary-title slds-text-heading_small">{$MOD.LBL_MB_MODULERELATEDLISTS}</h3>
                                            <div id="lists"></div>
                                          </div>
                                          <div class="slds-media__figure slds-media__figure_reverse">
                                            <button class="slds-button slds-button_neutral slds-button_dual-stateful" onclick="mb.backTo(5, true, {$ModuleBuilderID});">
                                                <svg class="slds-button__icon slds-button__icon_small slds-button__icon_left" aria-hidden="true">
                                                    <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
                                                </svg>
                                                {$MOD.LBL_MB_EDITRLS}
                                            </button>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </article>
                              </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br><br><br>
    <div class="slds-docked-form-footer">
      <button class="slds-button slds-button_brand slds-button_dual-stateful" onclick="mb.generateManifest()" id="genModule">
          <svg class="slds-button__icon slds-button__icon_small slds-button__icon_left" aria-hidden="true">
              <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
          </svg>
          {$MOD.LBL_MB_GENERATEMODULE}
      </button>
      <button class="slds-button slds-button_neutral slds-button_dual-stateful" id="genModuleProgress" style="display: none">
          <div class="demo-only">
            <div class="slds-spinner_container">
              <div role="status" class="slds-spinner slds-spinner_x-small">
                <span class="slds-assistive-text">Loading</span>
                <div class="slds-spinner__dot-a"></div>
                <div class="slds-spinner__dot-b"></div>
              </div>
            </div>
          </div>
          {$MOD.LBL_MB_GENERATEMODULE}
      </button>
    </div>
</article>