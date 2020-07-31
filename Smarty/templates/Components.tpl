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

{* Creates the main menu *}
{function cbmenu i=0}
<nav class="slds-context-bar__secondary" role="navigation">
	<ul class="slds-grid" id="cbmenu">
	{foreach $menu as $menuitem}
		{if $menuitem.mtype == 'menu'}
		<li class="slds-context-bar__item slds-context-bar__dropdown-trigger slds-dropdown-trigger slds-dropdown-trigger_hover" aria-haspopup="true">
			<a href="javascript:void(0);" class="slds-context-bar__label-action" title="{$menuitem.mlabel}">
				<span class="slds-truncate">{$menuitem.mlabel}</span>
			</a>
			{if !empty($menuitem.submenu)}
			<div class="slds-context-bar__icon-action slds-p-left_none" tabindex="0">
				<svg aria-hidden="true" class="slds-button__icon">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevrondown"></use>
				</svg>
			</div>
			{call cbsubmenu submenu=$menuitem.submenu i=$i}
			{/if}
		</li>
		{elseif $menuitem.mtype == 'module'}
		<li class="slds-context-bar__item">
			<a href="index.php?action=index&amp;module={$menuitem.mvalue}" class="slds-context-bar__label-action" title="{$menuitem.mlabel}">
				<span class="slds-truncate">{$menuitem.mlabel}</span>
			</a>
		</li>
		{/if}
		{$i = $i+1}
	{/foreach}
	</ul>
	<div class="slds-context-bar__tertiary" style="float:left; margin-top:auto; margin-bottom:auto;">
		<div class="slds-form-element">
			<div class="slds-form-element__control">
				<div class="slds-select_container">
				<select id="qccombo" class="slds-select" onchange="QCreate(this);">
					<option value="none">{$APP.LBL_QUICK_CREATE}...</option>
					{foreach item=detail from=$QCMODULE}
						<option value="{$detail.1}">{$APP.NEW}&nbsp;{$detail.0}</option>
					{/foreach}
				</select>
				</div>
			</div>
		</div>
	</div>
</nav>
{/function}

{* Creates the second level menu *}
{function cbsubmenu j=0}
<div class="slds-dropdown slds-dropdown_center slds-nubbin_top">
	<ul class="slds-dropdown__list" role="menu" id="menu{$i}">
	{foreach $submenu as $menuitem}
		{if $menuitem.mtype == 'module' && empty($menuitem.submenu)}
		<li class="slds-dropdown__item" role="presentation">
			<a href="index.php?action=index&amp;module={$menuitem.mvalue}" role="menuitem" tabindex="-1">
				<span class="slds-truncate">{$menuitem.mlabel}</span>
			</a>
		</li>
		{elseif $menuitem.mtype == 'module' && !empty($menuitem.submenu)}
		<li class="slds-dropdown__item" role="presentation">
		{call cbsubsubmenu submenuitem=$menuitem i=$i j=$j k=0}
		{$j = $j + 1}
		</li>
		{elseif $menuitem.mtype == 'menu' && !empty($menuitem.submenu)}
		<li class="slds-dropdown__item" role="presentation">
		{call cbsubsubmenu submenuitem=$menuitem i=$i j=$j k=0}
		{$j = $j + 1}
		</li>
		{elseif $menuitem.mtype == 'headtop'}
		<li class="slds-dropdown__header slds-has-divider_top-space" role="separator">
			<span class="slds-text-title_caps">{$menuitem.mlabel}</span>
		</li>
		{elseif $menuitem.mtype == 'headbottom'}
		<li class="slds-dropdown__header slds-has-divider_bottom-space" role="separator">
			<span class="slds-text-title_caps">{$menuitem.mlabel}</span>
		</li>
		{elseif $menuitem.mtype == 'sep'}
		<li class="slds-dropdown__header slds-has-divider_top-space" role="separator"></li>
		{elseif $menuitem.mtype == 'url'}
		<li class="slds-dropdown__item" role="presentation">
			<a href="{$menuitem.mvalue}" role="menuitem" tabindex="-1">
				<span class="slds-truncate">{$menuitem.mlabel}</span>
			</a>
		</li>
		{/if}
	{/foreach}
	</ul>
</div>
{/function}

{* Creates all the other levels recursively *}
{function cbsubsubmenu}
	{if !empty($submenuitem.submenu)}
		{if $submenuitem.mtype == 'module'}
		<a href="index.php?action=index&amp;module={$menuitem.mvalue}" role="menuitem" tabindex="-1">
		{else}
		<a href="javascript:void(0);" role="menuitem" tabindex="-1">
		{/if}
			<span class="slds-truncate" style="padding-right: 20px;">{$submenuitem.mlabel}</span>
			<svg aria-hidden="true" class="slds-button__icon">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevronright"></use>
			</svg>
		</a>
		<ul id="submenu{$i}-{$j}{if $k > 0}-{$k}{/if}" class="moreMenu">
		{foreach $submenuitem.submenu as $submenu_item}
			<li class="slds-dropdown__item" role="presentation">
				{$k = $k + 1}
				{call cbsubsubmenu submenuitem=$submenu_item i=$i j=$j k=$k}
			</li>
		{/foreach}
		</ul>
	{elseif $submenuitem.mtype == 'module'}
		<li class="slds-dropdown__item" role="presentation">
			<a href="index.php?action=index&amp;module={$submenuitem.mvalue}" role="menuitem" tabindex="-1">
				<span class="slds-truncate">{$submenuitem.mlabel}</span>
			</a>
		</li>
	{elseif $submenuitem.mtype == 'headtop'}
		<li class="slds-dropdown__header slds-has-divider_top-space" role="separator">
			<span class="slds-text-title_caps">{$submenuitem.mlabel}</span>
		</li>
	{elseif $submenuitem.mtype == 'headbottom'}
		<li class="slds-dropdown__header slds-has-divider_bottom-space" role="separator">
			<span class="slds-text-title_caps">{$submenuitem.mlabel}</span>
		</li>
	{elseif $submenuitem.mtype == 'sep'}
		<li class="slds-dropdown__header slds-has-divider_top-space" role="separator"></li>
	{elseif $submenuitem.mtype == 'url'}
		<li class="slds-dropdown__item" role="presentation">
			<a href="{$submenuitem.mvalue}" role="menuitem" tabindex="-1">
				<span class="slds-truncate">{$submenuitem.mlabel}</span>
			</a>
		</li>
	{/if}
{/function}

{$productline_classprefix = 'cbds-inventoryline' scope=global}
{$productline_inputprefix = 'cbds-inventoryline__input' scope=global}

{* Define the product line component *}
{function name=InventoryLine template=false data=[] custom=[]}
{* Set some defaults when this is a template call *}
{if $template}
	{$data.meta = []}
	{$data.meta.discount_type = 'p'}
	{$data.meta.discount_amount = 0}
	{$data.meta.linetotal = 0}
	{$data.meta.quantity = 1}
	{$data.meta.description = ''}

	{$data.pricing = []}
	{$data.pricing.cost_price = 0}
	{$data.pricing.cost_gross = 0}
	{$data.pricing.unit_price = 0}
	{$data.pricing.extgross = 0}
	{$data.pricing.extnet = 0}

	{$data.logistics = []}
	{$data.logistics.units_delivered_received = 0}
	{$data.logistics.qtyinstock = 0}
	{$data.logistics.qtyindemand = 0}

	{$data.taxes = []}
	
	{$data.taxes.1 = []}
	{$data.taxes.1.percent = 0}
	{$data.taxes.1.amount = 0}
	{$data.taxes.1.taxname = 'VAT'}
	{$data.taxes.2 = []}
	{$data.taxes.2.percent = 0}
	{$data.taxes.2.amount = 0}
	{$data.taxes.2.taxname = 'Sales tax'}
	{$data.taxes.3 = []}
	{$data.taxes.3.percent = 0}
	{$data.taxes.3.amount = 0}
	{$data.taxes.3.taxname = 'Service tax'}

	{$data.custom = $custom}
{/if}
<!-- LDS Detail line for inventorydetails -->
<div class="{$productline_classprefix} slds-card slds-m-vertical_x-small slds-p-around_none{if $template} {$productline_classprefix}--template{/if}">
	<!-- Main LDS inventory details line -->
	<div class="slds-is-relative slds-p-vertical_none slds-m-bottom_x-small cbds-inventoryline__headingswrapper">
		<div class="slds-grid slds-border_bottom slds-p-vertical_x-small slds-theme_info slds-theme_alert-texture slds-is-absolute cbds-inventoryline__headings">
			<div class="slds-col slds-size_1-of-12 slds-p-left_x-small">
				<div class="slds-text-title slds-text-color_inverse">Image</div>
			</div>
			<div class="slds-col slds-size-9-of-12">
				<div class="slds-grid">
					<div class="slds-col slds-size_3-of-12">
						<div class="slds-text-title slds-text-color_inverse">Product name</div>
					</div>
					<div class="slds-col slds-size_1-of-12 slds-p-left_xx-small">
						<div class="slds-text-title slds-text-color_inverse">Quantity</div>
					</div>
					<div class="slds-grid slds-size_3-of-12">
						<div class="slds-col slds-size_5-of-12">
							<div class="slds-text-title slds-text-color_inverse">Discount type</div>
						</div>
						<div class="slds-col slds-size_6-of-12 slds-p-left_small">
							<div class="slds-text-title slds-text-color_inverse">Discount</div>
						</div>
					</div>
					<div class="slds-col slds-size_2-of-12">
						<div class="slds-text-title slds-text-color_inverse">Discount amount</div>
					</div>
					<div class="slds-col slds-size_2-of-12">
						<div class="slds-text-title slds-text-color_inverse">Line total</div>
					</div>
				</div>
			</div>
			<div class="slds-col slds-size_2-of-12">
				<div class="sslds-text-title slds-p-right_small slds-text-align_right">Line tools</div>
			</div>
		</div>
	</div>
	<div class="slds-grid slds-gutters cbds-detail-line__main">
		<div class="slds-col slds-size_1-of-12">
			<div class="cbds-image-container">
				<img src="{if !$template && isset($data.meta.image) && $data.meta.image != ''}{$data.meta.image}{/if}" class="cbds-image cbds-product-line-image" />
			</div>
		</div>
		<!-- Nested column with input fields -->
		<div class="slds-col slds-size_9-of-12 slds-align-middle slds-p-right_none">
			<fieldset class="slds-form slds-gutters slds-form_compound">
				<legend class="slds-assistive-text">Edit inventorydetails record</legend>
				<div class="slds-form-element__group">
					<div class="slds-form-element__row">
						<!-- Product name form element -->
						<div class="slds-form-element slds-size_3-of-12">
							<div class="slds-combobox_container slds-has-inline-listbox cbds-product-search--hasroot">
								<div class="slds-combobox slds-dropdown-trigger slds-dropdown-trigger_click slds-combobox-lookup" aria-expanded="false" aria-haspopup="listbox" role="combobox">
									<div class="slds-combobox__form-element slds-input-has-icon slds-input-has-icon_right" role="none">
										<input class="slds-input slds-combobox__input {$productline_inputprefix}--name" aria-autocomplete="list" aria-controls="listbox-unique-id" autocomplete="off" role="textbox" placeholder="Search Products and services" type="text" value="{if !$template}{$data.meta.name}{/if}" />
										{call name=LDSIcon lib='utility' icon='search' align='right' size='x-small'}
									</div>
								</div>
							</div>
						</div>
						<!-- // Product name form element -->
						<!-- Product quantity form element -->
						{call name=ProductInputFormElement size='1-of-12' fieldname='quantity' value=$data.meta.quantity  icon='none' istemplate=$template type='number' error='Please input a number greater than 0' min='0.5'}
						<!-- // Product quantity form element -->
						<div class="slds-grid slds-size_3-of-12 slds-p-left_x-small">
							<!-- Discount type form element -->
							{if $data.meta.discount_type == 'p'}{$curval = 'Percentage'}{else}{$curval = 'Direct'}{/if}
							{$options[] = ['val' => 'p', 'label' => 'Percentage']}
							{$options[] = ['val' => 'd', 'label' => 'Direct']}
							{call
								name=ProductDropdownFormElement
								size='5-of-12'
								fieldname='discount_type'
								value=$data.meta.discount_type
								placeholder='Discount type'
								options=$options
								istemplate=$template
								valuelabel=$curval}
							<!-- // Discount type form element -->
							<!-- Discount number (percent/direct) form element -->
							{if $data.meta.discount_type == 'p'}{$icon = 'percent'}{else}{$icon = 'euro'}{/if}
							{call name=ProductInputFormElement size='6-of-12' fieldname='discount_amount' value=$data.meta.discount_amount iconlib='corebos' icon=$icon istemplate=$template type='number' error='Please input a numeric value into this field'}
							<!-- // Discount number (percent/direct) form element -->
						</div>
						<!-- Discount amount form element -->
						{$discount_total = $data.pricing.extgross - $data.pricing.extnet}
						{call name=ProductInputFormElement size='2-of-12' fieldname='discount_total' value=$discount_total iconlib='corebos' icon='euro' istemplate=$template type='currency' readonly=true}
						<!-- // Discount amount form element -->
						<!-- Line total form element -->
						{call name=ProductInputFormElement size='2-of-12' fieldname='linetotal' value=$data.meta.linetotal iconlib='corebos' icon='euro' istemplate=$template type='currency' readonly=true}
						<!-- // Line total form element -->
					</div>
				</div>
			</fieldset>
		</div>
		<!-- // Nested column with input fields -->
		<!-- LDS Line tools column -->
		<div class="slds-col slds-size_2-of-12 slds-align-middle">
			<div class="slds-button-group slds-float_right">
				{call name=LDSButton el='div' iconlib='utility' icon='move' iconsize='x-small' extraclass='cbds-detail-line-dragtool' title='Drag this line'}
				{call name=LDSButton el='button' iconlib='utility' icon='copy' iconsize='x-small' extraclass='cbds-detail-line-copytool' title='Copy this line'}
				{call name=LDSButton el='button' iconlib='utility' icon='delete' iconsize='x-small' extraclass='cbds-button--delete cbds-detail-line-deletetool' title='Delete this line'}
				{call name=LDSButton el='button' iconlib='utility' icon='switch' iconsize='x-small' extraclass='cbds-detail-line-extratool' title='Expand or collapse this line'}
			</div>
		</div>
		<!-- // LDS Line tools column -->
	</div>
	<!-- // Main LDS inventory details line -->
	<!-- Extra LDS inventory line -->
	<div class="slds-grid slds-gutters slds-wrap slds-p-vertical_medium {$productline_classprefix}__extra">
		<!-- LDS extra inventoryline column -->
		<div class="slds-col slds-size_3-of-12">
			<div class="slds-panel">
				<div class="slds-panel__header">
					<h2 class="slds-panel__header-title slds-text-heading_small slds-truncate" title="Pricing">Pricing</h2>
				</div>
				<div class="slds-form slds-form_compound slds-grow">
					<div class="slds-panel__section slds-p-bottom_none">
						<div class="slds-form-element__row cbds-m-bottom_none">
						{call name=ProductInputFormElement size='1-of-2' label='Unit cost price' fieldname='cost_price' value=$data.pricing.cost_price iconlib='corebos' icon='euro' istemplate=$template type='currency' error='Please enter a valid currency amount' readonly=false}
						{call name=ProductInputFormElement size='1-of-2' label='Line cost price' fieldname='cost_gross' value=$data.pricing.cost_gross iconlib='corebos' icon='euro' istemplate=$template type='currency' error='Please enter a valid currency amount' readonly=true}
						</div>
					</div>
					<div class="slds-panel__section slds-p-bottom_none">
						<div class="slds-form-element__row cbds-m-bottom_none">
						{call name=ProductInputFormElement size='1-of-2' label='Gross line price' fieldname='extgross' value=$data.pricing.extgross iconlib='corebos' icon='euro' istemplate=$template type='currency' error='Please enter a valid currency amount' readonly=true}
						{call name=ProductInputFormElement size='1-of-2' label='Net line price' fieldname='extnet' value=$data.pricing.extnet iconlib='corebos' icon='euro' istemplate=$template type='currency' error='Please enter a valid currency amount' readonly=true}
						</div>
					</div>
					<div class="slds-panel__section slds-p-bottom_none">
						<div class="slds-form-element__row cbds-m-bottom_none">
						{call name=ProductInputFormElement size='1-of-2' label='Unit price' fieldname='unit_price' value=$data.pricing.unit_price iconlib='corebos' icon='euro' istemplate=$template type='currency' error='Please enter a valid currency amount' readonly=false}
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- // LDS extra inventoryline column -->
		<!-- LDS extra inventoryline column -->
		<div class="slds-col slds-size_3-of-12">
			<div class="slds-panel">
				<div class="slds-panel__header">
					<h2 class="slds-panel__header-title slds-text-heading_small slds-truncate" title="Logistics">Logistics</h2>
				</div>
				<div class="slds-form slds-form_stacked slds-grow">
					{$usageunitspan = ' (<span class="'|cat:$productline_classprefix|cat:'--usageunit">'|cat:$data.logistics.usageunit|cat:'</span>)'}
					<div class="slds-panel__section slds-p-bottom_none">
					{call name=ProductInputFormElement size='1-of-1' label='Units del. / rec. '|cat:$usageunitspan fieldname='units_delivered_received' value=$data.logistics.units_delivered_received iconlib='corebos' icon='none' istemplate=$template type='currency' error='Please enter a valid number' readonly=false}
					</div>
					<div class="slds-panel__section slds-p-bottom_none">
						<div class="slds-grid">
							<div class="slds-col">
								{call name=ProductInputFormElement size='1-of-1' label='Qty in stock'|cat:$usageunitspan fieldname='qtyinstock' value=$data.logistics.qtyinstock iconlib='corebos' icon='none' istemplate=$template type='currency' error='Please enter a valid number' readonly=true}
							</div>
							<div class="slds-col">
							{call name=ProductInputFormElement size='1-of-1' label='Currently ordered'|cat:$usageunitspan fieldname='qtyindemand' value=$data.logistics.qtyindemand iconlib='corebos' icon='none' istemplate=$template type='currency' error='Please enter a valid number' readonly=true}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- // LDS extra inventoryline column -->
		<!-- LDS extra inventoryline column -->
		<div class="slds-col slds-size_3-of-12 {$productline_classprefix}--taxcol{if $inventoryblock.taxtype == 'group'} {$productline_classprefix}--taxcol-hidden{/if}">
			<div class="slds-panel">
				<div class="slds-panel__header">
					<h2 class="slds-panel__header-title slds-text-heading_small slds-truncate" title="Taxes">Taxes</h2>
				</div>
				<div class="slds-form slds-form_compound slds-grow">
					{foreach from=$data.taxes item=tax key=key}
						{call name=ProductTaxPanelSection fieldname=$tax.taxname label=$tax.taxlabel amount=$tax.amount percent=$tax.percent symbol='euro'}
					{/foreach}
				</div>
			</div>
		</div>
		<!-- // LDS extra inventoryline column -->
		<!-- LDS extra inventoryline column -->
		<div class="slds-col slds-size_3-of-12 {$productline_classprefix}--commentcol{if $inventoryblock.taxtype == 'group'} slds-size_6-of-12{/if}">
			<div class="slds-panel">
				<div class="slds-panel__header">
					<h2 class="slds-panel__header-title slds-text-heading_small slds-truncate" title="Comments">Comments</h2>
				</div>
				<div class="slds-form slds-form_stacked slds-grow">
					<div class="slds-panel__section slds-p-horizontal_none">
						<div class="slds-form-element">
							<div class="slds-form-element__control">
								<textarea rows="10" class="slds-textarea {$productline_classprefix}--comments" placeholder="Type a comment">{if $data.meta.description != ''}{$data.meta.description}{/if}</textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- // LDS extra inventoryline column -->
		<!-- Customfields LDS inventory line -->
		{*
		<div class="slds-col slds-size_4-of-4 {$productline_classprefix}__custom">
			<div class="slds-panel">
				<div class="cbds-panelheader">
					<div class="slds-text-color_inverse slds-align_absolute-center">Custom fields</div>
				</div>
				<div class="slds-grid slds-gutters slds-wrap slds-grid_vertical-align-end slds-p-vertical_medium">
				{foreach from=$data.custom item=customfield key=key name=name}
					<div class="slds-col slds-size_1-of-4 slds-p-bottom_small">
					{if $customfield.type == 'dropdown'}
						{call name=ProductDropdownFormElement fieldname=$key value=$customfield.selected placeholder='' options=$customfield.available istemplate=false label=$customfield.label}
					{elseif $customfield.type == 'text'}
						{call name=ProductInputFormElement label=$customfield.label fieldname=$key value=$customfield.value icon='none' istemplate=false type='text'}
					{elseif $customfield.type == 'checkbox'}
						{call name=ProductInputFormElement label=$customfield.label fieldname=$key value=$customfield.value type='checkbox' icon='none'}
					{/if}
					</div>
				{/foreach}
				</div>
			</div>
		</div>
		*}
		<!-- Customfields LDS inventory line -->
	</div>
	<!-- // Extra LDS inventory line -->
</div>
<!-- // LDS Detail line for inventorydetails -->
{/function}

{*
 * Function: ProductTaxPanelSection
 * ----------------------------------------------------------------------
 * Special function that outputs a custom panel section for the taxes,
 * these should have two fields, percentage and amount
 *
 * @param: The fieldname, should be the internal coreBOS fieldname
 * @param: The label, should be the field label
 * @param: The amount, should be the tax amount in the chosen currency
 * @param: The symbol, should be the currency symbol for the current user.
*}
{function name=ProductTaxPanelSection fieldname='' label='' amount='' percent='' symbol='euro'}
<div class="slds-panel__section slds-panel__section slds-p-bottom_none">
	<span class="slds-form-element__label">{$label}</span>
	<div class="slds-form-element__row cbds-m-bottom_none">
		<div class="slds-form-element slds-size_5-of-12">
			<div class="slds-form-element__control slds-input-has-icon slds-input-has-icon_left">
				<input type="text" class="slds-input {$productline_inputprefix}--{$fieldname}" value="{$percent}" data-type="currency"/>
				{call name=LDSIcon lib='corebos' icon='percent' align='left' size='x-small'}
			</div>
		</div>
		<div class="slds-form-element slds-size_7-of-12">
			<div class="slds-form-element__control slds-input-has-icon slds-input-has-icon_left">
				<input type="text" class="slds-input {$productline_inputprefix}--{$fieldname}_amount" data-type="currency" readonly="readonly" value="{$amount}" />
				{call name=LDSIcon lib='corebos' icon=$symbol align='left' size='x-small'}
			</div>
		</div>
	</div>
</div>
{/function}

{*
 * Function: ProductInputFormElement
 * ----------------------------------------------------------------------
 * Outputs a form element, optionally with an icon (right now fixed to
 * the corebos library of icons)
 *
 * @param: The size of the form element, check LDS documentation
 * 			on: https://www.lightningdesignsystem.com/utilities/sizing
 * @param: The label (Optional)
 * @param: The fieldname, should be the coreBOS fieldname
 * @param: The value, the value of the input field, if any
 * @param: The library for the icon, can be 'corebos' or one of the
 *         ones mentioned on https://www.lightningdesignsystem.com/icons
 * @param: The icon, should be the icon name from the corebos icon lib,
 * 			or 'none' (prevents icon output)
 * @param: Boolean that indicates if this is a build of the template
 * @param: 'text', 'number' or 'currency'. Sets the data-type attribute
 *         A special case is 'checkbox', where a checkbox will display.
 *         Beware in this case that the value should be '1' or '0'
 * @param: The error message that will be displayed when this input
 *         fails the validation that belongs to the type.
 * @param: Readonly, boolean that indicates if the input should be
 *         readonly
 * @param: Min, only useful when type is 'number'. Indicates the
           minimum, otherwise the field will not validate
 * @param: Max, only useful when type is 'number'. Indicates the
           maximum, otherwise the field will not validate
*}
{function name=ProductInputFormElement size='1-of-1' label='' fieldname='' value='' iconlib='utility' icon='' istemplate=false type='text' error='' readonly=false min='' max=''}
<div class="slds-form-element slds-size_{$size}">
	{if $label != '' && $type != 'checkbox'}<label class="slds-form-element__label">{$label}</label>{/if}
	<div class="slds-form-element__control{if $icon != 'none'} slds-input-has-icon slds-input-has-icon_left{/if}">
		{if $type != 'checkbox'}
			<input type="text" data-type="{$type}"{if $min != ''} data-min="{$min}"{/if}{if $max != ''} data-max="{$max}"{/if}{if $readonly} readonly="readonly"{/if}data-error-mess="{$error}" class="slds-input {$productline_inputprefix}--{$fieldname}" value="{$value}"/>
			{if $icon != 'none'}
			{call name=LDSIcon lib=$iconlib icon=$icon align='left' size='x-small' extraclass=$productline_classprefix|cat:'__symbol--'|cat:$fieldname}
			{/if}
		{else}
			{call name=LDSCheckbox label=$label value=$value fieldname=$fieldname}
		{/if}
	</div>
	<div class="slds-form-element__help cbds-form-element__help--fixed"></div>
</div>
{/function}

{*
 * Function: ProductDropdownFormElement
 * ----------------------------------------------------------------------
 * Outputs a form element with a dropdown
 *
 * @param: The size of the form element, check LDS documentation
 * 			on: https://www.lightningdesignsystem.com/utilities/sizing
 * @param: The fieldname, should be the coreBOS fieldname
 * @param: The value, the value of the input field, if any
 * @param: The placeholder text
 * @param: Flat array of options that the dropdown should include
 * @param: Boolean that indicates if this is a build of the template
 * @param: String to optionally provide a label to the field
 * @param: String to optionally override the prefix
*}
{function name=ProductDropdownFormElement size='1-of-1' fieldname='' value='' placeholder='' options=[] istemplate=false label='' prefix='' valuelabel=''}
{if $prefix == ''}{$prefix = $productline_inputprefix}{else}{/if}
<div class="slds-form-element slds-size_{$size}">
	{if $label != ''}<label class="slds-form-element__label">{$label}</label>{/if}
	<div class="slds-form-element__control">
		<div class="slds-combobox_container">
			<div class="slds-combobox slds-dropdown-trigger slds-dropdown-trigger_click slds-combobox-picklist" aria-expanded="false" aria-haspopup="listbox" role="combobox">
				<div class="slds-combobox__form-element slds-input-has-icon slds-input-has-icon_right" role="none">
					<input class="slds-input slds-combobox__input"
						   aria-controls=""
						   autocomplete="off"
						   data-valueholder="nextsibling"
						   role="textbox"
						   placeholder="{$placeholder}"
						   readonly="readonly"
						   type="text"
						   value="{$valuelabel}"
						/>
					<input class="{$prefix}--{$fieldname}" type="hidden" value="{$value}" />
					{call name=LDSIcon lib='utility' icon='down' align='right' size='x-small'}
				</div>
				<div role="listbox">
					<ul class="slds-listbox slds-listbox_vertical slds-dropdown slds-dropdown_fluid" role="presentation">
						{foreach from=$options item=option key=key name=name}
						<li role="presentation" class="slds-listbox__item" data-value="{$option.val}">
							<div class="slds-listbox__option slds-listbox__option_plain" role="option">
								<span class="slds-media__body">
									<span class="slds-truncate" title="{$option.label}">{$option.label}</span>
								</span>
							</div>
						</li>
						{/foreach}
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
{/function}

{*
 * Function: LDSIcon
 * ----------------------------------------------------------------------
 * Outputs a LDS icon, optionally with a container
 *
 * @param: The library for the icon, can be 'corebos' or one of the
 * 			ones mentioned on https://www.lightningdesignsystem.com/icons
 * @param: The icon name
 * @param: Where the icon should align: 'left' or 'right'
 * @param: The size: https://www.lightningdesignsystem.com/utilities/sizing
 * @param: Boolean that indicates of a container should be used
 * @param: An optional extra class that can be used on the SVG item
*}
{function name=LDSIcon lib='utility' icon='' align='left' size='x-small' container=true extraclass=''}
{if $container}<span class="slds-icon_container slds-icon-{$lib}-{$icon} slds-input__icon slds-input__icon_{$align}">{/if}
	{if $lib != 'corebos'}
	<svg class="slds-icon slds-icon slds-icon_{$size} slds-icon-text-default{if $extraclass != ''} {$extraclass}{/if}" aria-hidden="true">
		<use xlink:href="include/LD/assets/icons/{$lib}-sprite/svg/symbols.svg#{$icon}" xmlns:xlink="http://www.w3.org/1999/xlink" />
	</svg>
	{else}
	<div class="slds-text-body_regular slds-text-color_weak{if $extraclass != ''} {$extraclass}{/if}">{if $icon == 'euro'}&euro;{else}%{/if}</div>
	{/if}
{if $container}</span>{/if}
{/function}

{*
 * Function: LDSCheckbox
 * ----------------------------------------------------------------------
 * Outputs a LDS checkbox inside a form element
 *
 * @param: The labeltext for the checkbox
 * @param: The value, either '1' or '0'
 * @param: the internal fieldname
*}
{function name=LDSCheckbox label='' value='' fieldname=''}
<span class="slds-checkbox">
	<input id="" class="{$productline_inputprefix}--{$fieldname}" value="{if value == '1'}on{else}off{/if}" type="checkbox" />
	<label class="slds-checkbox__label" for="">
		<span class="slds-checkbox_faux"></span>
		<span class="slds-form-element__label">{$label}</span>
	</label>
</span>
{/function}

{*
 * Function: LDSButton
 * ----------------------------------------------------------------------
 * Outputs a LDS button, optionally with an icon
 *
 * @param: The HTML tag to be used, defaults to 'button'
 * @param: The icon library name, could be 'corebos' or any one
 *			from https://www.lightningdesignsystem.com/icons
 * @param: The icon name, or 'none' (prevents icon from being output)
 * @param: The icon size
 * @param: An optional extra class to be given to the element
 * @param: The button title (used on hovers)
*}
{function name=LDSButton el='button' iconlib='utility' icon='' iconsize='x-small' extraclass='' title=''}
<{$el} type="button" class="slds-button{if $icon != 'none'} slds-button_icon slds-button_icon-border-filled{/if}{if extraclass != ''} {$extraclass}{/if}" title="{$title}" aria-pressed="false">
	{if $icon != ''}
	{call name=LDSIcon lib=$iconlib icon=$icon size=$iconsize container=false}
	{/if}
	<span class="slds-assistive-text">Copy this line</span>
</{$el}>
{/function}