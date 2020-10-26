<script src="include/js/Inventory.js"></script>
<script src="modules/WorkAssignment/WorkAssignment.js"></script>
<script src="modules/WorkAssignment/lib/js/Sortable.min.js"></script>
<script>
window.addEventListener("load", function(){
	var block = document.getElementsByClassName("cbds-inventory-block")[0];
	window.InventoryDetailsBlock = new InventoryBlock(block, {
			"linesContClass" : "cbds-inventorylines",
			"lineClass" : "cbds-inventoryline",
			"linePrefix" : "cbds-inventoryline",
			"inputPrefix" : "cbds-inventoryline__input",
			"aggrPrefix" : "cbds-inventoryaggr",
			"aggrInputPrefix" : "cbds-inventoryaggr__input",
			"editmode": '{$MASTERMODE}'
	});
});
</script>

{* <pre>{$APP|print_r}</pre> *}

{include file='modules/WorkAssignment/Components.tpl'}
<!-- Detail block -->
<div class="cbds-detail-block cbds-inventory-block">
	<!-- Detail line preheader -->
	<div class="slds-grid slds-p-vertical_medium slds-theme_alt-inverse" style="border-radius: 0.25rem 0.25rem 0 0;">
		<div class="slds-col slds-size_8-of-12 slds-p-left_medium">
			<span class="slds-icon_container slds-icon-utility-picklist-type" title="">
				<svg class="slds-icon slds-icon_small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#picklist_type" xmlns:xlink="http://www.w3.org/1999/xlink" />
				</svg>
			</span>
		</div>
		<div class="slds-col slds-size_4-of-12 slds-grid">
			<div class="slds-col slds-text-color_default">
				<!-- Group/individual dropdown -->
				{$taxtypes[] = ['val' => 'individual', 'label' => $APP.LBL_INDIVIDUAL]}
				{$taxtypes[] = ['val' => 'group', 'label' => $APP.LBL_GROUP]}
				{if $inventoryblock.taxtype == 'group'}{$curtaxtype = $APP.LBL_GROUP}{else}{$curtaxtype = $APP.LBL_INDIVIDUAL}{/if}
				{call name=ProductDropdownFormElement size='1-of-1' fieldname='taxtype' value=$inventoryblock.taxtype placeholder='Tax type' options=$taxtypes prefix='cbds-inventory-block__input' valuelabel=$curtaxtype}
				<!-- // Group/individual dropdown -->
			</div>
			<div class="slds-col slds-p-right_medium">
				<div class="slds-button-group slds-float_right">
					<button type="button" class="slds-button slds-button_icon slds-button_icon-border-inverse cbds-toolbox__tool" data-clickfunction="expandAllLines" title="Expand all lines" aria-pressed="false">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#expand_all"></use>
						</svg>
						<span class="slds-assistive-text">Expand or collapse this line</span>
					</button>
					<button type="button" class="slds-button slds-button_icon slds-button_icon-border-inverse cbds-toolbox__tool" data-clickfunction="collAllLines" title="Collapse all lines" aria-pressed="false">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#collapse_all"></use>
						</svg>
						<span class="slds-assistive-text">Expand or collapse this line</span>
					</button>
					{if $MASTERMODE == 'EditView'}
					<button type="button" class="slds-button slds-button_icon slds-button_icon-border-inverse cbds-toolbox__tool" data-clickfunction="insertNewLine" title="Add line" aria-pressed="false">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
						</svg>
						<span class="slds-assistive-text">Expand or collapse this line</span>
					</button>
					<button type="button" class="slds-button slds-button_icon slds-button_icon-border-inverse cbds-toolbox__tool" data-clickfunction="deleteAllLines" title="Delete all lines" aria-pressed="false">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
						</svg>
						<span class="slds-assistive-text">Expand or collapse this line</span>
					</button>
					{/if}
				</div>
			</div>
		</div>
	</div>
	<!-- // Detail line preheader -->
	<!-- LDS Detail line header -->
	<div class="slds-grid slds-border_bottom slds-p-vertical_small slds-theme_inverse" style="border-radius: 0 0 0.25rem 0.25rem;">
		<div class="slds-col slds-size_1-of-12 slds-p-left_x-small">
			<div class="slds-text-title_caps slds-text-color_inverse">Image</div>
		</div>
		<div class="slds-col slds-size-9-of-12">
			<div class="slds-grid">
				<div class="slds-col slds-size_3-of-12">
					<div class="slds-text-title_caps slds-text-color_inverse slds-truncate">{'LBL_LIST_PRODUCT_NAME'|@getTranslatedString:'Products'}</div>
				</div>
				<div class="slds-col slds-size_1-of-12 slds-p-left_xx-small">
					<div class="slds-text-title_caps slds-text-color_inverse slds-truncate">{'Quantity'|@getTranslatedString:'InventoryDetails'}</div>
				</div>
				<div class="slds-grid slds-size_3-of-12">
					<div class="slds-col slds-size_5-of-12">
						<div class="slds-text-title_caps slds-text-color_inverse slds-truncate">{$MOD.LBL_DISCOUNT_TYPE}</div>
					</div>
					<div class="slds-col slds-size_6-of-12 slds-p-left_small">
						<div class="slds-text-title_caps slds-text-color_inverse slds-truncate">{$MOD.LBL_DISCOUNT}</div>
					</div>
				</div>
				<div class="slds-col slds-size_2-of-12">
					<div class="slds-text-title_caps slds-text-color_inverse slds-truncate">{'Discount Amount'|@getTranslatedString:'InventoryDetails'}</div>
				</div>
				<div class="slds-col slds-size_2-of-12">
					<div class="slds-text-title_caps slds-text-color_inverse slds-truncate">{'Line Total'|@getTranslatedString:'InventoryDetails'}</div>
				</div>
			</div>
		</div>
		<div class="slds-col slds-size_2-of-12">
			<div class="slds-text-title_caps slds-p-right_small slds-text-color_inverse slds-text-align_right">{$MOD.LBL_LINE_TOOLS}</div>
		</div>
	</div>
	<!-- // LDS Detail line header -->
	<div class="cbds-detail-lines cbds-inventorylines">
		{foreach from=$inventoryblock.lines item=productline}
			{call name=InventoryLine data=$productline}
		{/foreach}
	</div>
	<!-- LDS Aggregations block -->
	<article class="slds-card slds-theme_shade">
		<div class="slds-card__header slds-grid">
			<header class="slds-media slds-media_center slds-has-flexi-truncate">
				<div class="slds-media__figure">
					<span class="slds-icon_container slds-icon-standard-contact">
						<svg class="slds-icon slds-icon_small" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#product_required" xmlns:xlink="http://www.w3.org/1999/xlink" />
						</svg>
					</span>
				</div>
				<div class="slds-media__body">
					<h2>
						<a href="javascript:void(0);" class="slds-card__header-link slds-truncate" title="Total number of lines">
							<span class="slds-text-heading_small">{$MOD.LBL_LINES} (<span class="cbds-inventoryaggr--linecount">{count($inventoryblock.lines)}</span>)</span>
						</a>
					</h2>
				</div>
			</header>
			<div class="slds-no-flex">
				<div class="slds-button-group slds-theme_default">
					<button type="button" class="slds-button slds-button_icon slds-button_icon-border cbds-toolbox__tool" data-clickfunction="expandAllLines" title="{$MOD.LBL_EXPAND_ALL_LINES}" aria-pressed="false">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#expand_all"></use>
						</svg>
						<span class="slds-assistive-text">{$MOD.LBL_EXPAND_ALL_LINES}</span>
					</button>
					<button type="button" class="slds-button slds-button_icon slds-button_icon-border cbds-toolbox__tool" data-clickfunction="collAllLines" title="{$MOD.LBL_COLL_ALL_LINES}" aria-pressed="false">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#collapse_all"></use>
						</svg>
						<span class="slds-assistive-text">{$MOD.LBL_COLL_ALL_LINES}</span>
					</button>
					{if $MASTERMODE == 'EditView'}
					<button type="button" class="slds-button slds-button_icon slds-button_icon-border cbds-toolbox__tool" data-clickfunction="insertNewLine" title="{$MOD.LBL_INSERT_LINE}" aria-pressed="false">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
						</svg>
						<span class="slds-assistive-text">{$MOD.LBL_INSERT_LINE}</span>
					</button>
					<button type="button" class="slds-button slds-button_icon slds-button_icon-border cbds-button_delete cbds-toolbox__tool" data-clickfunction="deleteAllLines" title="{$MOD.LBL_DEL_ALL_LINES}" aria-pressed="false">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
						</svg>
						<span class="slds-assistive-text">{$MOD.LBL_DEL_ALL_LINES}</span>
					</button>
					{/if}
				</div>
			</div>
		</div>
		<div class="slds-card__body cbds-inventoryaggr">
			<div class="slds-grid">
				<div class="slds-col slds-size_4-of-12">
					<div class="slds-panel slds-m-around_small slds-theme_shade slds-theme_alert-texture slds-box cbds-inventoryaggr__taxes_group{if $inventoryblock.taxtype == 'group'} active{/if}">
						<div class="slds-panel__header">
							<h2 class="slds-panel__header-title slds-text-heading_small slds-truncate slds-text-color_default" title="{$APP.LBL_TAX}">{$APP.LBL_TAX}</h2>
						</div>
						<div class="slds-panel__body slds-p-around_none">
							<div class="slds-panel__section slds-p-around_none">
								<!-- Group aggregation taxes -->
								<div class="slds-text-color_default slds-form slds-p-around_small">
									<div class="slds-form-element__row slds-wrap">
										{foreach from=$inventoryblock.grouptaxes item=tax key=key name=name}
										<fieldset class="slds-form-element slds-form-element_compound slds-is-editing slds-form-element_horizontal">
											<legend class="slds-form-element__legend slds-form-element__label">{$tax.taxlabel}</legend>
											<div class="slds-form-element__control">
												<div class="slds-form-element__row slds-wrap">
													<div class="slds-size_1-of-1 slds-large-size_1-of-2">
														<div class="slds-form-element">
															<label class="slds-form-element__label">{$MOD.LBL_PERC}</label>
															<div class="slds-form-element__control slds-input-has-icon slds-input-has-icon_left">
																<input data-savefield="{$tax.taxname}_perc" class="slds-input cbds-inventoryaggr__input--{$tax.taxname}" value="{$tax.percent}" type="text" data-type="number" data-taxname="{$tax.taxname}" data-error-mess="{$MOD.LBL_VALID_NUM}"{if $MASTERMODE != 'EditView'} readonly="readonly"{/if}>
																<span class="slds-icon_container slds-input__icon slds-input__icon_left" style="left: 0.75rem;">
																	<div class="slds-text-body_regular slds-text-color_weak">%</div>
																</span>
															</div>
														</div>
													</div>
													<div class="slds-size_1-of-1 slds-large-size_1-of-2">
														<div class="slds-form-element">
															<label class="slds-form-element__label">{$MOD.LBL_AMOUNT}</label>
															<div class="slds-form-element__control slds-input-has-icon slds-input-has-icon_left">
																<input data-savefield="sum_{$tax.taxname}" class="slds-input cbds-inventoryaggr__input--sum_{$tax.taxname}" data-type="currency" readonly="readonly" value="{$tax.amount}" type="text">
																<span class="slds-icon_container slds-input__icon slds-input__icon_left" style="left: 0.3rem;">
																	<div class="slds-text-body_regular">&euro;</div>
																</span>
															</div>
														</div>
													</div>
												</div>
											</div>
										</fieldset>
										{/foreach}
									</div>
								</div>
								<!-- // Group aggregation taxes -->
							</div>
						</div>
					</div>
				</div>
				<div class="slds-col slds-size_4-of-12">
					<div class="slds-panel slds-m-around_small slds-theme_shade slds-theme_alert-texture slds-box">
						<div class="slds-panel__header">
							<h2 class="slds-panel__header-title slds-text-heading_small slds-truncate" title="{$APP.{'Shipping & Handling Tax:'}}">{$APP.{'Shipping & Handling Tax:'}}</h2>
						</div>
						<div class="slds-panel__body slds-p-around_none">
							<div class="slds-panel__section slds-p-around_none">
								<!-- Shipping and handling aggregation -->
								<div class="slds-text-color_default slds-form slds-p-around_small slds-p-around_medium cbds-inventoryaggr__taxes--sh">
									<div class="slds-form-element__row slds-wrap">
										{foreach from=$inventoryblock.shtaxes item=shtax key=key name=name}
										<fieldset class="slds-form-element slds-form-element_compound slds-is-editing slds-form-element_horizontal">
											<legend class="slds-form-element__legend slds-form-element__label">{$shtax.taxlabel}</legend>
											<div class="slds-form-element__control">
												<div class="slds-form-element__row slds-wrap">
													<div class="slds-size_1-of-1 slds-large-size_1-of-2">
														<div class="slds-form-element">
															<label class="slds-form-element__label">{$MOD.LBL_PERC}</label>
															<div class="slds-form-element__control slds-input-has-icon slds-input-has-icon_left">
																<input data-savefield="{$shtax.taxname}_perc" class="slds-input cbds-inventoryaggr__input--{$shtax.taxname}" value="{$shtax.percent}" type="text" data-type="number" data-taxname="{$shtax.taxname}" data-error-mess="{$MOD.LBL_VALID_NUM}"{if $MASTERMODE != 'EditView'} readonly="readonly"{/if}>
																<span class="slds-icon_container slds-input__icon slds-input__icon_left" style="left: 0.75rem;">
																	<div class="slds-text-body_regular slds-text-color_weak">%</div>
																</span>
															</div>
														</div>
													</div>
													<div class="slds-size_1-of-1 slds-large-size_1-of-2">
														<div class="slds-form-element">
															<label class="slds-form-element__label">{$MOD.LBL_AMOUNT}</label>
															<div class="slds-form-element__control slds-input-has-icon slds-input-has-icon_left">
																<input data-savefield="sum_{$shtax.taxname}" class="slds-input cbds-inventoryaggr__input--sum_{$shtax.taxname}" data-type="currency" readonly="readonly" value="{$shtax.amount}" type="text">
																<span class="slds-icon_container slds-input__icon slds-input__icon_left" style="left: 0.3rem;">
																	<div class="slds-text-body_regular slds-text-color_weak">&euro;</div>
																</span>
															</div>
														</div>
													</div>
												</div>
											</div>
										</fieldset>
										{/foreach}
									</div>
								</div>
								<!-- // Shipping and handling aggregation -->
							</div>
						</div>
					</div>
				</div>
				<div class="slds-col slds-size_4-of-12">
					<div class="slds-panel slds-m-around_small slds-box slds-theme_shade">
						<div class="slds-panel__header">
							<h2 class="slds-panel__header-title slds-text-heading_small slds-truncate slds-text-align_right" title="{$APP.LBL_TOTAL}">{$APP.LBL_TOTAL}</h2>
						</div>
						<div class="slds-panel__body">
							<div class="slds-panel__section slds-p-right_none">
								<!-- Totals -->
								<div class="slds-grid">
									<div class="slds-col slds-size_7-of-12">
										<div class="slds-text-title_caps slds-text-align_right slds-p-top_x-small slds-m-right_small">{$APP.{'Gross Total'}}</div>
									</div>
									<div class="slds-col slds-size_5-of-12">
										<div class="slds-grid">
											<div class="slds-col slds-text-color_weak slds-size_2-of-12 slds-p-top_x-small">&euro;</div>
											<div class="slds-col slds-size_10-of-12 slds-form-element">
												<input data-savefield="pl_gross_total" type="text" readonly="readonly" data-type="currency" class="slds-p-right_none slds-text-align_right slds-input cbds-inventoryaggr__input--grosstotal" value="{$inventoryblock.aggr.grosstotal}" />
												<div class="slds-form-element__help"></div>
											</div>
										</div>
									</div>
								</div>
								<div class="slds-section">
									<h3 class="slds-section__title">
										<button aria-controls="cbds-inventoryaggr__discounts"
												type="button"
												aria-expanded="true"
												class="slds-button slds-section__title-action"
												onclick="document.getElementById('cbds-inventoryaggr__discounts').parentElement.classList.toggle('slds-is-open')">
											<svg class="slds-section__title-action-icon slds-button__icon slds-button__icon_left" aria-hidden="true">
												<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#switch"></use>
											</svg>
											<span class="slds-truncate" title="{$APP.Discount}">{$APP.Discount}</span>
										</button>
									</h3>
									<div aria-hidden="false" class="slds-section__content slds-theme_default slds-box slds-p-vertical_none" id="cbds-inventoryaggr__discounts">
										<div class="slds-grid">
											<div class="slds-col slds-size_7-of-12">
												<div class="slds-text-title_caps slds-text-align_right slds-p-top_x-small slds-m-right_small">{$MOD.LBL_LINEDISCOUNTS}</div>
											</div>
											<div class="slds-col slds-size_5-of-12">
												<div class="slds-grid">
													<div class="slds-col slds-text-color_weak slds-size_2-of-12 slds-p-top_x-small">&euro;</div>
													<div class="slds-col slds-size_10-of-12 slds-form-element">
														<input data-savefield="pl_dto_line" type="text" readonly="readonly" data-type="currency" class="slds-p-right_none slds-text-align_right slds-input cbds-inventoryaggr__input--pl_dto_line" value="{$inventoryblock.aggr.pl_dto_line}" />
														<div class="slds-form-element__help"></div>
													</div>
												</div>
											</div>
										</div>
										<div class="slds-grid">
											<div class="slds-col slds-size_7-of-12">
												<div class="slds-text-title_caps slds-text-align_right slds-p-top_x-small slds-m-right_small">{$MOD.LBL_GLBL_DISCOUNT}</div>
											</div>
											<div class="slds-col slds-size_5-of-12">
												<div class="slds-grid">
													<div class="slds-col slds-text-color_weak slds-size_2-of-12 slds-p-top_x-small">&euro;</div>
													<div class="slds-col slds-size_10-of-12 slds-form-element">
														<input data-savefield="pl_dto_global" type="text" data-type="number" class="slds-p-right_none slds-text-align_right slds-input cbds-inventoryaggr__input--pl_dto_global" value="{$inventoryblock.aggr.pl_dto_global}"{if $MASTERMODE != 'EditView'} readonly="readonly"{/if}/>
														<div class="slds-form-element__help"></div>
													</div>
												</div>
											</div>
										</div>
										<div class="slds-grid">
											<div class="slds-col slds-size_7-of-12">
												<div class="slds-text-title_caps slds-text-align_right slds-p-top_x-small slds-m-right_small">{$MOD.LBL_TOTAL_DISCOUNT}</div>
											</div>
											<div class="slds-col slds-size_5-of-12">
												<div class="slds-grid">
													<div class="slds-col slds-text-color_weak slds-size_2-of-12 slds-p-top_x-small">&euro;</div>
													<div class="slds-col slds-size_10-of-12 slds-form-element">
														<input data-savefield="pl_dto_total" type="text" readonly="readonly" data-type="currency" class="slds-p-right_none slds-text-align_right slds-input cbds-inventoryaggr__input--totaldiscount" value="{$inventoryblock.aggr.totaldiscount}" />
														<div class="slds-form-element__help"></div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="slds-section">
									<h3 class="slds-section__title">
										<button aria-controls="cbds-inventoryaggr__sh"
												type="button"
												aria-expanded="true"
												class="slds-button slds-section__title-action"
												onclick="document.getElementById('cbds-inventoryaggr__sh').parentElement.classList.toggle('slds-is-open')">
											<svg class="slds-section__title-action-icon slds-button__icon slds-button__icon_left" aria-hidden="true">
												<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#switch"></use>
											</svg>
											<span class="slds-truncate" title="{$APP.LBL_SHIPPING_AND_HANDLING_CHARGES}">{$APP.LBL_SHIPPING_AND_HANDLING_CHARGES}</span>
										</button>
									</h3>
									<div aria-hidden="false" class="slds-section__content slds-theme_default slds-box slds-p-vertical_none" id="cbds-inventoryaggr__sh">
										<div class="slds-grid">
											<div class="slds-col slds-size_7-of-12">
												<div class="slds-text-title_caps slds-text-align_right slds-p-top_x-small slds-m-right_small">{$APP.LBL_SHIPPING_AND_HANDLING_CHARGES}</div>
											</div>
											<div class="slds-col slds-size_5-of-12">
												<div class="slds-grid">
													<div class="slds-col slds-text-color_weak slds-size_2-of-12 slds-p-top_x-small">&euro;</div>
													<div class="slds-col slds-size_10-of-12 slds-form-element">
														<input data-savefield="pl_sh_total" class="slds-p-right_none slds-text-align_right slds-input cbds-inventoryaggr__input--pl_sh_total" value="{$inventoryblock.aggr.pl_sh_total}" type="text" data-type="currency" data-error-mess="{$MOD.LBL_VALID_CURR}"{if $MASTERMODE != 'EditView'} readonly="readonly"{/if}>
														<div class="slds-form-element__help"></div>
													</div>
												</div>
											</div>
										</div>
										<div class="slds-grid">
											<div class="slds-col slds-size_7-of-12">
												<div class="slds-text-title_caps slds-text-align_right slds-p-top_x-small slds-m-right_small">{$APP.{'Shipping & Handling Tax:'}}</div>
											</div>
											<div class="slds-col slds-size_5-of-12">
												<div class="slds-grid">
													<div class="slds-col slds-text-color_weak slds-size_2-of-12 slds-p-top_x-small">&euro;</div>
													<div class="slds-col slds-size_10-of-12 slds-form-element">
														<input data-savefield="pl_sh_tax" type="text" readonly="readonly" data-type="currency" class="slds-p-right_none slds-text-align_right slds-input cbds-inventoryaggr__input--shtaxtotal" value="{$inventoryblock.aggr.shtaxtotal}" />
														<div class="slds-form-element__help"></div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="slds-grid">
									<div class="slds-col slds-size_7-of-12">
										<div class="slds-text-title_caps slds-text-align_right slds-p-top_x-small slds-m-right_small">{$APP.{'Net Total (bGD)'}}</div>
									</div>
									<div class="slds-col slds-size_5-of-12">
										<div class="slds-grid">
											<div class="slds-col slds-text-color_weak slds-size_2-of-12 slds-p-top_x-small">&euro;</div>
											<div class="slds-col slds-size_10-of-12 slds-form-element">
												<input data-savefield="sum_nettotal" type="text" readonly="readonly" data-type="currency" class="slds-p-right_none slds-text-align_right slds-input cbds-inventoryaggr__input--sum_nettotal" value="{$inventoryblock.aggr.sum_nettotal}" />
												<div class="slds-form-element__help"></div>
											</div>
										</div>
									</div>
								</div>
								<div class="slds-grid">
									<div class="slds-col slds-size_7-of-12">
										<div class="slds-text-title_caps slds-text-align_right slds-p-top_x-small slds-m-right_small">{$APP.{'Net Total (aGD)'}}</div>
									</div>
									<div class="slds-col slds-size_5-of-12">
										<div class="slds-grid">
											<div class="slds-col slds-text-color_weak slds-size_2-of-12 slds-p-top_x-small">&euro;</div>
											<div class="slds-col slds-size_10-of-12 slds-form-element">
												<input data-savefield="pl_net_total" type="text" readonly="readonly" data-type="currency" class="slds-p-right_none slds-text-align_right slds-input cbds-inventoryaggr__input--subtotal" value="{$inventoryblock.aggr.subtotal}" />
												<div class="slds-form-element__help"></div>
											</div>
										</div>
									</div>
								</div>
								<div class="slds-grid">
									<div class="slds-col slds-size_7-of-12">
										<div class="slds-text-title_caps slds-text-align_right slds-p-top_x-small slds-m-right_small">{$MOD.LBL_TOTAL_TAX}</div>
									</div>
									<div class="slds-col slds-size_5-of-12">
										<div class="slds-grid">
											<div class="slds-col slds-text-color_weak slds-size_2-of-12 slds-p-top_x-small">&euro;</div>
											<div class="slds-col slds-size_10-of-12 slds-form-element">
												<input data-savefield="sum_taxtotal" type="text" readonly="readonly" data-type="currency" class="slds-p-right_none slds-text-align_right slds-input cbds-inventoryaggr__input--taxtotal" value="{$inventoryblock.aggr.taxtotal}" />
												<div class="slds-form-element__help"></div>
											</div>
										</div>
									</div>
								</div>
								<div class="slds-grid">
									<div class="slds-col slds-size_7-of-12">
										<div class="slds-text-title_caps slds-text-align_right slds-p-top_x-small slds-m-right_small">{$APP.LBL_ADJUSTMENT}</div>
									</div>
									<div class="slds-col slds-size_5-of-12">
										<div class="slds-grid">
											<div class="slds-col slds-text-color_weak slds-size_2-of-12 slds-p-top_x-small">&euro;</div>
											<div class="slds-col slds-size_10-of-12 slds-form-element">
												<input data-savefield="pl_adjustment" type="text" data-type="currency" class="slds-p-right_none slds-text-align_right slds-input cbds-inventoryaggr__input--pl_adjustment" value="{$inventoryblock.aggr.pl_adjustment}"{if $MASTERMODE != 'EditView'} readonly="readonly"{/if}/>
												<div class="slds-form-element__help"></div>
											</div>
										</div>
									</div>
								</div>
								<div class="slds-grid">
									<div class="slds-col slds-size_7-of-12">
										<div class="slds-text-title_caps slds-text-align_right slds-p-top_x-small slds-m-right_small">{$APP.LBL_TOTAL}</div>
									</div>
									<div class="slds-col slds-size_5-of-12">
										<div class="slds-grid">
											<div class="slds-col slds-text-color_weak slds-size_2-of-12 slds-p-top_x-small">&euro;</div>
											<div class="slds-col slds-size_10-of-12 slds-form-element">
												<input data-savefield="pl_grand_total" type="text" readonly="readonly" data-type="currency" class="slds-p-right_none slds-text-align_right slds-input cbds-inventoryaggr__input--total" value="{$inventoryblock.aggr.total}" />
												<div class="slds-form-element__help"></div>
											</div>
										</div>
									</div>
								</div>
								<!-- // Totals -->
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- Aggregation tax block -->
			<div class="cbds-inventoryaggr__taxes">
			</div>
			<!-- // Aggregation tax block -->
		</div>
	</article>
	<!-- // LDS Aggregations block -->
	<div style="display: none;" class="cbds-inventorylines__domfields"></div>
	<div style="display: none;" class="cbds-inventorylines__todelete"></div>
	<div style="display: none;" class="cbds-inventoryaggr__domfields"></div>
</div>

<!-- Detail block -->
<!-- Template -->
{$custom = $inventoryblock.lines[0].custom}
{call name=InventoryLine template=true custom=$custom}
<!-- // Template -->