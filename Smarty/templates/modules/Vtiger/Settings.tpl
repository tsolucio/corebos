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
<link rel="stylesheet" type="text/css" href="include/settings.css">
<script src="include/js/Settings.js"></script>
<div class="slds-page-header slds-page-header_record-home slds-m-horizontal_small">
	<div class="slds-page-header__row">
		<div class="slds-page-header__col-title">
			<div class="slds-media">
				<div class="slds-media__figure">
					<span class="slds-icon_container slds-icon-utility-announcement slds-current-color">
						<svg class="slds-icon slds-page-header__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#bundle_config"></use>
						</svg>
					</span>
				</div>
				<div class="slds-media__body">
					<div class="slds-page-header__name">
						<div class="slds-page-header__name-title">
							<h1>
								<span>{$MOD.VTLIB_LBL_MODULE_MANAGER_DESCRIPTION}</span>
								<span class="slds-page-header__title slds-truncate" title="{$MOD.VTLIB_LBL_MODULE_MANAGER_DESCRIPTION}">
									{$MOD.VTLIB_LBL_MODULE_MANAGER} | {$MODULE_LBL}
								</span>
							</h1>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="slds-page-header__row slds-page-header__row_gutters">
		<div class="slds-page-header__col-details">
			<ul class="cbds-modulelist">
				{foreach key=mod_name item=mods_array from=$MENU_ARRAY name=itr}
					<div class="slds-grid slds-gutters slds-m-around_medium">
					{foreach from=$mods_array item=mod_array}
						{if !empty($mod_array.label)}
							{assign var=count value=$smarty.foreach.itr.iteration}
							<div class="slds-col slds-size_4-of-12">
								<a href="{$mod_array.location}" class="slds-box slds-box_link slds-box_x-small slds-media">
									<div class="slds-media__figure slds-media__figure_fixed-width slds-align_absolute-center slds-m-left_xx-small">
										<span class="slds-icon_container slds-icon-utility-knowledge_base">
											<svg class="slds-icon slds-icon-text-default" aria-hidden="true">
												<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#{$mod_array.image_src}"></use>
											</svg>
										</span>
									</div>
									<div class="slds-media__body slds-border_left slds-p-around_small">
										<h2 class="slds-truncate slds-text-heading_small" title="{$mod_array.label}">{$mod_array.label}</h2>
										<p class="slds-m-top_small">{$mod_array.desc}</p>
									</div>
								</a>
							</div>
						{/if}
					{/foreach}
					</div>
				{/foreach}
			</ul>
		</div>
	</div>
</div>