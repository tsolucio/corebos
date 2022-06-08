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
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43" aria-modal="true">
	<div class="slds-modal__container slds-p-around_none">
		<div class="slds-modal__header slds-grid slds-grid_align-spread slds-grid_vertical-align-center">
			<h2 id="header43" class="slds-text-heading_medium">coreBOS Settings</h2>
		</div>
		{* Loop here for block *}
		{foreach key=BLOCKID item=BLOCKLABEL from=$BLOCKS}
			{if $BLOCKLABEL neq 'LBL_MODULE_MANAGER'}
				<div class="slds-modal__content slds-app-launcher__content " id="modal-content-id-1">
					<div class="slds-section slds-is-open slds-p-around_x-large">
						<h3 class="slds-section__title">
							<button aria-controls="appsContent" aria-expanded="true"
								class="slds-button slds-section__title-action">
								<svg class="slds-section__title-action-icon slds-button__icon slds-button__icon_left"
									aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#switch"></use>
								</svg>
								<span class="slds-truncate" title="{$MOD.$BLOCKLABEL}">{$MOD.$BLOCKLABEL}</span>
							</button>
						</h3>
						<div aria-hidden="false" class="slds-section__content" id="appsContent">
							<div class="slds-assistive-text" id="drag-live-region" aria-live="assertive"></div>
							<ul class="slds-grid slds-grid_pull-padded slds-wrap">
							{* loop here for fields *}
							{foreach item=data from=$FIELDS.$BLOCKID name=itr}
								{if $data.name eq ''}
									&nbsp;
								{else}
									{assign var=label value=$data.name|@getTranslatedString:$data.module}
									{if $data.name eq $label}
									{assign var=label value=$data.name|@getTranslatedString:'Settings'}
									{/if}
									{assign var=count value=$smarty.foreach.itr.iteration}
									{* Item *}
									<li  onclick="gotourl('{$data.link}')" class="slds-p-horizontal_small slds-size_1-of-1 slds-medium-size_1-of-3">
										<div class="slds-app-launcher__tile slds-text-link_reset ">
											<div class="slds-app-launcher__tile-figure" style="color: #0070ba;">
											{* Icon *}
												<span class="slds-icon_container slds-icon-utility-announcement slds-current-color" title="User(s)">
													<svg class="slds-icon slds-icon_large" aria-hidden="true">
														<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#{$data.icon}"></use>
													</svg>
												</span>
											{* Icon End *}
											</div>
											<div class="slds-app-launcher__tile-body">
												<a href="{$data.link}">{$label}</a>
												{* Description *}
												{assign var=description value=$data.description|@getTranslatedString:$data.module}
												{if $data.description eq $description}
												{assign var=description value=$data.description|@getTranslatedString:'Settings'}
												{/if}

												<p>
													{$description}
												</p>

												<div class="slds-popover slds-popover_tooltip slds-nubbin_top-right slds-hide" role="tooltip" id="help-0" style="position:absolute;top:80px;right:30px">
													<div class="slds-popover__body"></div>
												</div>
											</div>
										</div>
									</li>
									{* Item End *}
								{/if}
							{/foreach}
							</ul>
						</div>
					</div>
					<hr />
				</div>
			{/if}
		{/foreach}
	</div>
</section>

			