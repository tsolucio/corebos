<div id="showMsg"></div>
<script src="modules/Settings/ModuleBuilder/ModuleBuilder.js"></script>
<div id="vtlib_modulebuilder" style="display:block;position:absolute;width:500px;"></div>
{assign var="MODULEICON" value='builder'}
{assign var="MODULESECTION" value=$MOD.LBL_MODULE_BUILDER}
{assign var="MODULESECTIONDESC" value=$MOD.LBL_MODULE_BUILDER_DESCRIPTION}
{include file='SetMenu.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
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
							    <svg class="slds-icon slds-icon--small" aria-hidden="true" style="color: blue">
							        <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#all"></use>
							    </svg>
							</button>
							<hr style="border-bottom: 2px solid #1589ee; border-top: 0px">
							<br>
							<div id="moduleLists"></div>
							<span id="progresstext" class="slds-badge" style="margin-bottom: 5px;text-transform: uppercase;">{$MOD.LBL_MB_PROGRESS}: 0%</span>
							<div class="slds-progress-bar" aria-valuemin="0" aria-valuemax="100" role="progressbar">
							    <span class="slds-progress-bar__value" id="progress" style="width:0%">
							    </span>
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