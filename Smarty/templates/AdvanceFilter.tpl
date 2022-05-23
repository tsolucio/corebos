<!--*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
-->
<script type="text/javascript" src="include/js/advancefilter.js"></script>
{* Get selected value to start out the dropdown with *}
{foreach from=$MODULES_BLOCK item=COLUMNS_BLOCK}
	{foreach from=$COLUMNS_BLOCK item=BLOCK}
		{foreach from=$BLOCK item=FIELD}
			{if $FIELD.selected}
				{$SELECTEDFIELD = $FIELD}
			{/if}
		{/foreach}
	{/foreach}
{/foreach}
<ul id="cbds-advfilt-groups" style="list-style: none !important;"></ul>

<div class="slds-expression__buttons">
	<button type="button" class="slds-button slds-button_neutral" data-onclick="add-group">
		<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
		</svg>{'LBL_NEW_GROUP'|@getTranslatedString:$MODULE}
	</button>
{if $SOURCE == 'listview'}
	<button type="button" class="slds-button slds-button_brand" data-onclick="submit-adv-cond-form">
		<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
		</svg>{'LBL_SEARCH'|@getTranslatedString:$MODULE}
	</button>
	{/if}
</div>

<div id="cbds-advfilt-template__operation-box" style="display: none;">
	<div class="slds-combobox_container">
		<div class="slds-combobox slds-dropdown-trigger slds-dropdown-trigger_click" aria-expanded="false" aria-haspopup="listbox" role="combobox">
			<div class="slds-combobox__form-element slds-input-has-icon slds-input-has-icon_right" role="none">
				<input class="slds-input slds-combobox__input" autocomplete="off" role="textbox" type="text" readonly="" value="" data-valueholder="nextsibling">
				<input type="hidden" value="" />
				<span class="slds-icon_container slds-icon-utility-down slds-input__icon slds-input__icon_right">
					<svg class="slds-icon slds-icon slds-icon_x-small slds-icon-text-default" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#down"></use>
					</svg>
				</span>
			</div>
			<div class="slds-dropdown slds-dropdown_length-5 slds-dropdown_fluid" role="listbox">
				<ul class="slds-listbox slds-listbox_vertical" role="presentation">
				</ul>
			</div>
		</div>
	</div>
</div>

<div id="cbds-advfilt-template__operation-item" style="display: none;">
	<li role="presentation" class="slds-listbox__item">
		<div class="slds-media slds-listbox__option slds-listbox__option_plain slds-media_small" role="option">
			<span class="slds-media__figure slds-listbox__option-icon"></span>
			<span class="slds-media__body">
				<span class="slds-truncate" title="">
				</span>
			</span>
		</div>
	</li>
</div>

<div id="cbds-advfilt-template__group" style="display: none;">
	<li class="slds-expression__group" data-group-no="" data-condcount="" style="display: block !important;">
		<div class="slds-grid slds-gutters_xx-small cbds-advfilt-group__controls slds-hide">
			<div class="slds-col slds-grid slds-grid_align-end">
				<div class="col">
					<div class="slds-combobox_container">
						<div class="slds-combobox slds-dropdown-trigger slds-dropdown-trigger_click cbds-advfilt-group__gluecombo" aria-expanded="false" aria-haspopup="listbox" role="combobox">
							<div class="slds-combobox__form-element slds-input-has-icon slds-input-has-icon_right" role="none">
								<input class="slds-input slds-combobox__input slds-combobox__input-value adv-filt-group__glue" autocomplete="off" role="textbox" type="text" readonly="" value="{$APP.LBL_CRITERIA_AND}" data-valueholder="nextsibling" />
								<input type="hidden" value="and" />
								<span class="slds-icon_container slds-icon-utility-down slds-input__icon slds-input__icon_right">
									<svg class="slds-icon slds-icon slds-icon_x-small slds-icon-text-default" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#down"></use>
									</svg>
								</span>
							</div>
							<div class="slds-dropdown slds-dropdown_length-2 slds-dropdown_fluid" role="listbox" style="min-width: 0">
								<ul class="slds-listbox slds-listbox_vertical" role="group" style="list-style: none !important;">
									<li role="presentation" class="slds-listbox__item" data-value="and" style="display: block !important;">
										<div class="slds-media slds-listbox__option slds-listbox__option_plain slds-media_small" role="option">
											<span class="slds-media__figure slds-listbox__option-icon"></span>
											<span class="slds-media__body">
												<span class="slds-truncate" title="{$APP.LBL_CRITERIA_AND}">{$APP.LBL_CRITERIA_AND}</span>
											</span>
										</div>
									</li>
									<li role="presentation" class="slds-listbox__item" data-value="or" style="display: block !important;">
										<div class="slds-media slds-listbox__option slds-listbox__option_plain slds-media_small" role="option">
											<span class="slds-media__figure slds-listbox__option-icon"></span>
											<span class="slds-media__body">
												<span class="slds-truncate" title="{$APP.LBL_CRITERIA_OR}">{$APP.LBL_CRITERIA_OR}</span>
											</span>
										</div>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
				<div class="slds-col">
					<button type="button" class="slds-button slds-button_icon slds-button_icon-border" data-onclick="delete-group" title="{$APP.LBL_DELETE_GROUP}">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
						</svg>
						<span class="slds-assistive-text">{$APP.LBL_DELETE_GROUP}</span>
					</button>
				</div>
			</div>
		</div>
		<fieldset>
			<ul class="cbds-advfilt-condwrapper" style="list-style: none !important;">
			</ul>
			<div class="slds-expression__buttons">
				<button type="button" class="slds-button slds-button_neutral" data-onclick="add-condition">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
					</svg>
					{$APP.LBL_NEW_CONDITION}
				</button>
			</div>
		</fieldset>
	</li>
</div>

<div id="cbds-advfilt-template__condition" style="display: none;">
	<li class="slds-expression__row slds-expression__row_group slds-p-horizontal_none" data-cond-no="" style="display: block !important;">
		<fieldset>
			<div class="slds-grid slds-gutters_xx-small">
				<div class="slds-col slds-size_1-of-12">
					<div class="slds-form-element">
						{*<label class="slds-form-element__label"></label>*}
						<div class="slds-form-element__control">
							<div class="slds-combobox_container">
								<div class="slds-combobox slds-dropdown-trigger slds-dropdown-trigger_click cbds-advfilt-cond__glue" aria-expanded="false" aria-haspopup="listbox" role="combobox">
									<div class="slds-combobox__form-element slds-input-has-icon slds-input-has-icon_right" role="none">
										<input class="slds-input slds-combobox__input slds-combobox__input-value cbds-advfilt-cond__glue--input" autocomplete="off" role="textbox" type="text" readonly="" disabled="" value="{$APP.LBL_CRITERIA_AND}" data-valueholder="nextsibling" />
										<input type="hidden" value="and" />
										<span class="slds-icon_container slds-icon-utility-down slds-input__icon slds-input__icon_right">
											<svg class="slds-icon slds-icon slds-icon_x-small slds-icon-text-default" aria-hidden="true">
												<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#down"></use>
											</svg>
										</span>
									</div>
									<div class="slds-dropdown slds-dropdown_length-2 slds-dropdown_fluid" role="listbox" style="min-width: 0">
										<ul class="slds-listbox slds-listbox_vertical" role="group" style="list-style: none !important;">
											<li role="presentation" class="slds-listbox__item" data-value="and" style="display: block !important;">
												<div id="option1" class="slds-media slds-listbox__option slds-listbox__option_plain slds-media_small" role="option">
													<span class="slds-media__body">
														<span class="slds-truncate" title="{$APP.LBL_CRITERIA_AND}">{$APP.LBL_CRITERIA_AND}</span>
													</span>
												</div>
											</li>
											<li role="presentation" class="slds-listbox__item" data-value="or" style="display: block !important;">
												<div id="option1" class="slds-media slds-listbox__option slds-listbox__option_plain slds-media_small" role="option">
													<span class="slds-media__body">
														<span class="slds-truncate" title="{$APP.LBL_CRITERIA_OR}">{$APP.LBL_CRITERIA_OR}</span>
													</span>
												</div>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="slds-col">
					<div class="slds-form-element">
						{*<label class="slds-form-element__label">{$APP.LBL_GENERAL_FIELDS}</label>*}
						<div class="slds-form-element__control">
							<div class="slds-combobox_container">
								<div class="slds-combobox slds-dropdown-trigger slds-dropdown-trigger_click cbds-advfilt-cond__field" aria-expanded="false" aria-haspopup="listbox" role="combobox">
									<div class="slds-combobox__form-element slds-input-has-icon slds-input-has-icon_right" role="none">
										<input class="slds-input slds-combobox__input slds-combobox__input-value" autocomplete="none" data-cols="search" role="textbox" data-col-id="" type="text" oninput="AdvancedFilter.searchFields(this)" placeholder="{if !empty($SELECTEDFIELD)}{$SELECTEDFIELD.label}{/if}" data-valueholder="nextsibling" />
										<input type="hidden" value="{if !empty($SELECTEDFIELD)}{$SELECTEDFIELD.value}{/if}" />
										<span class="slds-icon_container slds-icon-utility-down slds-input__icon slds-input__icon_right">
											<svg class="slds-icon slds-icon slds-icon_x-small slds-icon-text-default" aria-hidden="true">
												<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#down"></use>
											</svg>
										</span>
									</div>
									<div class="slds-dropdown slds-dropdown_length-7 slds-dropdown_fluid" role="listbox">
										{if $SOURCE != 'reports-modal'}
											<ul class="slds-listbox slds-listbox_vertical" role="group" data-value-id="" data-col-value="search-value">
											{foreach from=$MODULES_BLOCK item='COLUMNS_BLOCK' key='MODLABEL'}
												<li role="presentation" class="slds-listbox__item cbds-bg-blue--dark">
													<div class="slds-media slds-listbox__option slds-listbox__option_plain slds-media_small" role="presentation">
														<h3 class="slds-text-title_caps slds-text-color_inverse" role="presentation">{$MODLABEL|@getTranslatedString:$MODULE}</h3>
													</div>
												</li>
												{foreach from=$COLUMNS_BLOCK item='BLOCK' key='BLOCKLABEL'}
													<li role="presentation" class="slds-listbox__item">
														<div class="slds-media slds-listbox__option slds-listbox__option_plain slds-media_small" role="presentation">
															<h3 class="slds-text-title_caps" role="presentation">{$BLOCKLABEL}</h3>
														</div>
													</li>
													{foreach from=$BLOCK item='FIELD' key='FIELDLABEL'}
													<li role="presentation" class="slds-listbox__item" data-value="{$FIELD.value}">
														<div class="slds-media slds-listbox__option slds-listbox__option_plain slds-media_small" role="option">
															<span class="slds-media__figure slds-listbox__option-icon"></span>
															<span class="slds-media__body">
																<span class="slds-truncate" title="{$FIELD.label}">{$FIELD.label}</span>
															</span>
														</div>
													</li>
													{/foreach}
												{/foreach}
											{/foreach}
											</ul>
										{else}
										{* Reports fills the dropdowns through javascript and don't provide a Smarty array *}
											<ul class="slds-listbox slds-listbox_vertical cbds-advfilt__fieldcombo-list" role="group">
											</ul>
										{/if}
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="slds-col slds-grow-none">
					<div class="slds-form-element">
						<div class="slds-form-element__control cbds-advfilt-cond__opswrapper">
							{* Filled by JS *}
						</div>
					</div>
				</div>
				<div class="slds-col">
					<div class="slds-form-element cbds-advfilt-cond__value">
						<div class="slds-form-element__control slds-grid slds-p-horizontal_none">
							<div class="slds-col slds-size_8-of-12">
								<input class="slds-input cbds-advfilt-cond__value--input cbds-advfilt-cond__value--validate" type="text" value="">
							</div>
							<div class="slds-col slds-size_4-of-12">
								<div class="slds-button-group">
									{if $SOURCE == 'reports-modal'}
									<button type="button" class="cbds-advfilt-cond__value--compfield slds-button slds-button_icon slds-button_icon-border-filled" title="{$APP.LBL_FIELD_FOR_COMPARISION}" data-onclick="pick-comparison-field">
										<svg class="slds-button__icon" aria-hidden="true">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#replace"></use>
										</svg>
										<span class="slds-assistive-text">
											{$APP.LBL_FIELD_FOR_COMPARISION}
										</span>
									</button>
									{/if}
									<button type="button" disabled="disabled" class="cbds-advfilt-cond__value--datebutt slds-button slds-button_icon slds-button_icon-border-filled" title="{$APP.LBL_ACTION_DATE}" data-onclick="pick-date">
										<svg class="slds-button__icon" aria-hidden="true">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#event"></use>
										</svg>
										<span class="slds-assistive-text">
											{$APP.LBL_ACTION_DATE}
										</span>
									</button>
									{if $SOURCE != 'listview'}
									<button type="button" class="cbds-advfilt-cond__value--clearbutt slds-button slds-button_icon slds-button_icon-border-filled" title="{$APP.LBL_CLEAR}" data-onclick="clear-cond">
										<svg class="slds-button__icon" aria-hidden="true">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
										</svg>
										<span class="slds-assistive-text">
											{$APP.LBL_CLEAR}
										</span>
									</button>
									{/if}
								</div>
							</div>
						</div>
					</div>
					<div class="slds-form-element slds-hide cbds-advfilt-cond__value slds-m-top_xx-small">
						<div class="slds-form-element__control slds-grid slds-p-horizontal_none">
							<div class="slds-col slds-size_8-of-12">
								<input class="slds-input cbds-advfilt-cond__value--input" type="text" value="">
							</div>
							<div class="slds-col slds-size_4-of-12">
								<button type="button" disabled="disabled" class="cbds-advfilt-cond__value--datebutt slds-button slds-button_icon slds-button_icon-border-filled" title="{$APP.LBL_ACTION_DATE}" data-onclick="pick-date">
									<svg class="slds-button__icon" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#event"></use>
									</svg>
									<span class="slds-assistive-text">
										{$APP.LBL_ACTION_DATE}
									</span>
								</button>
							</div>
						</div>
					</div>
				</div>
				<div class="slds-col slds-grow-none slds-size_1-of-12">
					<div class="slds-form-element">
						<div class="slds-form-element__control">
							<button type="button" disabled="disabled" class="slds-float_right slds-button slds-button_icon slds-button_icon-border-filled cbds-advfilt-cond__delete" title="{$APP.LBL_DELETE_BUTTON}" data-onclick="delete-cond">
								<svg class="slds-button__icon" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
								</svg>
								<span class="slds-assistive-text">
									{$APP.LBL_DELETE_BUTTON}
								</span>
							</button>
						</div>
					</div>
				</div>
			</div>
		</fieldset>
	</li>
</div>
<input type="hidden" id="cbds-advfilt__context" value="{$SOURCE}" />
{if $SOURCE == 'customview' || $SOURCE == 'reports' || $SOURCE == 'reports-modal'}
<input type="hidden" id="cbds-advfilt_existing-conditions" value='{$CRITERIA_GROUPS|json_encode}' />
{/if}
{if $SOURCE != 'reports-modal'}
<script>
	window.addEventListener('load', function() {
		var advancedFilter = document.getElementById('cbds-advanced-search');
		window.AdvancedFilter = new cbAdvancedFilter(advancedFilter);
	});
</script>
{/if}
