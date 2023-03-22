<div class="slds-grid slds-gutters" style="background: white; padding-top: 1%;width: 98%;margin-left: 1%;margin-top: -0.5%">
	<div class="slds-col">
		<div class="small detailview_utils_table_top">
			<div class="detailview_utils_table_tabs noprint">
			{if $SinglePane_View eq 'false' && $IS_REL_LIST neq false && $IS_REL_LIST|@count > 0 && empty($Module_Popup_Edit)}
				{if $HASRELATEDPANES eq 'true'}
					{include file='RelatedPanes.tpl' tabposition='top' RETURN_RELATEDPANE=''}
				{else}
					{if !(GlobalVariable::getVariable('Application_Hide_Related_List', 0))}
					<div class="slds-tabs_{$TABSCOPED} slds-tabs_medium">
						<ul class="slds-tabs_{$TABSCOPED}__nav" role="tablist">
							<li class="slds-tabs_{$TABSCOPED}__item slds-is-active" role="presentation">
								<a class="slds-tabs_{$TABSCOPED}__link" role="tab" tabindex="0" aria-selected="true" style="font-weight: 600;font-size: 13px;">
									<span class="{$currentModuleIcon['containerClass']}">
										<svg class="slds-icon slds-icon_small" aria-hidden="true">
											<use xlink:href="include/LD/assets/icons/{$currentModuleIcon['library']}-sprite/svg/symbols.svg#{$currentModuleIcon['icon']}"></use>
										</svg>
									</span>
									{$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}
								</a>
							</li>
							<li class="slds-tabs_{$TABSCOPED}__item slds-tabs_{$TABSCOPED}__overflow-button" role="presentation">
								<div class="slds-dropdown-trigger slds-dropdown-trigger_hover">
									<a class="slds-button" aria-haspopup="true" href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}" style="font-size: 13px">
										{$APP.LBL_MORE} {$APP.LBL_INFORMATION}
										<svg class="slds-button__icon slds-button__icon_x-small slds-button__icon_right" aria-hidden="true">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#down"></use>
										</svg>
									</a>
									<div class="slds-dropdown slds-dropdown_right">
										<ul class="slds-dropdown__list slds-dropdown_length-with-icon-10 cbds-scrollbar" role="menu">
											{foreach key=_RELATION_ID item=_RELATED_MODULE from=$IS_REL_LIST}
											<li class="slds-dropdown__item" role="presentation">
												<a href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&selected_header={$_RELATED_MODULE}&relation_id={$_RELATION_ID}#tbl_{$MODULE}_{$_RELATED_MODULE}" role="menuitem" tabindex="-1">
													<span class="slds-truncate">
														<span class="slds-media slds-media_center">
															{if isset($REL_MOD_ICONS[$_RELATION_ID]['icon'])}
															<span class="slds-media__figure">
																<span class="{$REL_MOD_ICONS[$_RELATION_ID]['containerClass']}">
																	<svg class="slds-icon slds-icon_small" aria-hidden="true">
																		<use xlink:href="include/LD/assets/icons/{$REL_MOD_ICONS[$_RELATION_ID]['library']}-sprite/svg/symbols.svg#{$REL_MOD_ICONS[$_RELATION_ID]['icon']}"></use>
																	</svg>
																</span>
															</span>
															{/if}
															<span class="slds-media__body" style="font-size: 13px">
																{$_RELATED_MODULE|@getTranslatedString:$_RELATED_MODULE}
															</span>
														</span>
													</span>
												</a>
											</li>
											{/foreach}
										</ul>
									</div>
								</div>
							</li>
						</ul>
					</div>
					{/if}
				{/if}
			{/if}
			</div>
			<div class="detailview_utils_table_tabactionsep detailview_utils_table_tabactionsep_top" id="detailview_utils_table_tabactionsep_top"></div>
			<div class="detailview_utils_table_actions detailview_utils_table_actions_top" id="detailview_utils_actions_top">
				<div class="slds-button-group" role="group">
					{if empty($Module_Popup_Edit)}
					<div class="slds-button-group" role="group">
						{include file='Components/DetailViewPirvNext.tpl'}
					</div>
					{/if}
				</div>
			</div>
		</div>
	</div>
</div>