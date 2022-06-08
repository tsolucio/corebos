{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
 ********************************************************************************/
-->*}
{include file='SetMenu.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
<div class="slds-modal__container slds-p-around_none">
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tr>
		<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
			<br />
			<div align=center>

			<table class="settingsSelUITopLine" align="center" border="0" cellpadding="5" cellspacing="0" width="100%">
				<tr>
					<td rowspan="2" valign="top" width="50">
						<span class="slds-icon_container slds-icon-utility-announcement slds-current-color" title="{$MOD.VTLIB_LBL_MODULE_MANAGER}">
							<svg class="slds-icon slds-icon_small" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#bundle_config"></use>
							</svg>
						</span>
					</td>
					<td class="heading2" valign="bottom"> <b>
						<a href="index.php?module=Settings&action=ModuleManager">{$MOD.VTLIB_LBL_MODULE_MANAGER}</a> 
						<span class="slds-icon_container slds-icon-utility-announcement slds-current-color" title="{$MOD.VTLIB_LBL_MODULE_MANAGER}">
							<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevronright"></use>
							</svg>
						</span> {$MODULE_LBL} </td>
				</tr>
				<tr>
					<td class="small" valign="top">{$MOD.VTLIB_LBL_MODULE_MANAGER_DESCRIPTION}</td>
				</tr>
				</table>

				<br>
				<table border="0" cellspacing="0" cellpadding="20" width="100%" class="settingsUI">
					<tr>
						<td>
							<table border="0" cellspacing="0" cellpadding="10" width="100%">
								<tr>
									{foreach key=mod_name item=mod_array from=$MENU_ARRAY name=itr}
									<td width=25% valign=top>
										{if empty($mod_array.label)}
											&nbsp;
										{else}
										<table border=0 cellspacing=0 cellpadding=5 width="100%">
											<tr>
												{assign var=count value=$smarty.foreach.itr.iteration}
												<td rowspan=2 valign=top width="10%">
												<a href="{$mod_array.location}">
													<span class="slds-icon_container slds-icon-utility-announcement slds-current-color" title="{$mod_array.label}">
														<svg class="slds-icon slds-icon_large" aria-hidden="true">
															<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#{$mod_array.image_src}"></use>
														</svg>
													</span>
												</a>
													
												</td>
												<td class=big valign=top>
													<a href="{$mod_array.location}">
													{$mod_array.label}
													</a>
												</td>
											</tr>
											<tr>
												<td class="small" valign=top width="80%">
													{$mod_array.desc}
												</td>
											</tr>
										</table>
										{/if}
									</td>
									{if $count mod 3 eq 0}
										</tr><tr>
									{/if}
									{/foreach}
								</tr>
							</table>
						</td>
					</tr>
				</table>

					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
			</div>
		</td>
	</tr>
</table>
</div>
</section>