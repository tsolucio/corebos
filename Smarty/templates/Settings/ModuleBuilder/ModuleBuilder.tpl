<div id="showMsg"></div>
<script type="text/javascript" src="include/js/{$LANGUAGE}.lang.js"></script>
<script src="modules/Settings/ModuleBuilder/ModuleBuilder.js"></script>
<div id="vtlib_modulebuilder" style="display:block;position:absolute;width:500px;"></div>
{assign var="MODULEICON" value='builder'}
{assign var="MODULESECTION" value=$MOD.LBL_MODULE_BUILDER}
{assign var="MODULESECTIONDESC" value=$MOD.LBL_MODULE_BUILDER_DESCRIPTION}
{include file='SetMenu.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher">
<div class="slds-modal__container slds-p-around_none">
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tr>
		<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
			<div align=center>
				<br>
				<table border="0" cellpadding="10" cellspacing="0" width="100%">
				<tr>
					<td>
						<div id="vtlib_modulemanager_list">
							<div id="moduleListsModal" style="display: none">
								{include file="Smarty/templates/Settings/ModuleBuilder/modulesList.tpl"}
							</div>
							<button class="slds-button slds-button_brand" onclick="mb.openModal()" style="float: right;margin-bottom: 15px; text-transform: uppercase;">
								{$MOD.LBL_MB_ALLMODULES}&nbsp;
								<svg class="slds-icon slds-icon_small" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#all"></use>
								</svg>
							</button>
							<button class="slds-button slds-button_destructive" onclick="mb.resetTemplate();" style="float: right;margin-bottom: 15px; text-transform: uppercase;">
								Start new module&nbsp;
								<svg class="slds-icon slds-icon_small" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use>
								</svg>
							</button>
							<hr style="border-bottom: 2px solid #1589ee; border-top: 0px">
							<br>
							<div id="moduleLists"></div>
							<div class="slds-path">
							  <div class="slds-grid slds-path__track">
							    <div class="slds-grid slds-path__scroller-container">
							      <div class="slds-path__scroller">
							        <div class="slds-path__scroller_inner">
							          <ul class="slds-path__nav" role="listbox" aria-orientation="horizontal">
							            <li class="slds-path__item slds-is-current slds-is-active" id="general-information" onclick="mb.backTo(1, true)">
							              <a aria-selected="true" class="slds-path__link">
							                <span class="slds-path__stage">
							                  <svg class="slds-icon slds-icon_x-small" aria-hidden="true">
							                    <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#check"></use>
							                  </svg>
							                </span>
							                <span class="slds-path__title">{$MOD.LBL_MB_GENERAL}</span>
							              </a>
							            </li>
							            <li class="slds-path__item slds-is-incomplete" role="presentation" id="block-information" onclick="mb.backTo(2, true)">
							              <a aria-selected="false" class="slds-path__link">
							                <span class="slds-path__stage">
							                  <svg class="slds-icon slds-icon_x-small" aria-hidden="true">
							                    <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#check"></use>
							                  </svg>
							                </span>
							                <span class="slds-path__title">{$MOD.LBL_MB_MODULEBLOCKS}</span>
							              </a>
							            </li>
							            <li class="slds-path__item slds-is-incomplete" role="presentation" id="field-information" onclick="mb.backTo(3, true)">
							              <a aria-selected="false" class="slds-path__link">
							                <span class="slds-path__stage">
							                  <svg class="slds-icon slds-icon_x-small" aria-hidden="true">
							                    <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#check"></use>
							                  </svg>
							                </span>
							                <span class="slds-path__title">{$MOD.LBL_MB_MODULEFIELDS}</span>
							              </a>
							            </li>
							            <li class="slds-path__item slds-is-incomplete" role="presentation" id="filters" onclick="mb.backTo(4, true)">
							              <a aria-selected="false" class="slds-path__link">
							                <span class="slds-path__stage">
							                  <svg class="slds-icon slds-icon_x-small" aria-hidden="true">
							                    <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#check"></use>
							                  </svg>
							                </span>
							                <span class="slds-path__title">{$MOD.LBL_MB_CVS}</span>
							              </a>
							            </li>
							            <li class="slds-path__item slds-is-incomplete" role="presentation" id="relationship" onclick="mb.backTo(5, true)">
							              <a aria-selected="false" class="slds-path__link">
							                <span class="slds-path__stage">
							                  <svg class="slds-icon slds-icon_x-small" aria-hidden="true">
							                    <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#check"></use>
							                  </svg>
							                </span>
							                <span class="slds-path__title">{$MOD.LBL_MB_RELATEDLISTS}</span>
							              </a>
							            </li>
							          </ul>
							        </div>
							      </div>
							    </div>
							  </div>
							</div>
							<ol class="slds-setup-assistant">
								<li class="slds-setup-assistant__item">
									{include file="Smarty/templates/Settings/ModuleBuilder/Step1.tpl"}
									{include file="Smarty/templates/Settings/ModuleBuilder/Step2.tpl"}
									{include file="Smarty/templates/Settings/ModuleBuilder/Step3.tpl"}
									{include file="Smarty/templates/Settings/ModuleBuilder/Step4.tpl"}
									{include file="Smarty/templates/Settings/ModuleBuilder/Step5.tpl"}
									{include file="Smarty/templates/Settings/ModuleBuilder/Step6.tpl"}
								</li>
							</ol>
						</div>
					</td>
				</tr>
				</table>
			</div>
		</td>
	</tr>
</table>
</div>