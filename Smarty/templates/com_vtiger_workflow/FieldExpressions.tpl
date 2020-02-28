{*<!--
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
-->*}
<style>
/* Tooltip container */
.tooltip {
  position: relative;
  display: inline-block;
}

/* Tooltip text */
.tooltip .tooltiptext {
  visibility: hidden;
  position: absolute;
  left: 25px;
  top: -5px;
}

/* Show the tooltip text when you mouse over the tooltip container */
.tooltip:hover .tooltiptext {
  visibility: visible;
}
#editpopup {
	top:35%;
	left:20%;
	height:350px;
}
</style>
<div id='editpopup' class='layerPopup slds-align_absolute-center' style='display:none;'>
	<div id='editpopup_draghandle' style='cursor: move;' class="slds-grid slds-badge_lightest">
		<div class="slds-col slds-size_3-of-4 slds-page-header__title slds-m-top_xx-small">
			<div>
				&nbsp;{$MOD.LBL_SET_VALUE}&nbsp;
				<section aria-describedby="dialog-body-id-98" aria-labelledby="dialog-heading-id-103" class="tooltip" role="dialog">
					<span class="slds-icon_container slds-icon-utility-info">
						<svg class="slds-icon slds-icon slds-icon_xx-small slds-icon-text-default" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info"></use>
						</svg>
					</span>
					<span class="tooltiptext slds-popover slds-nubbin_left-top-corner" style="width:50rem;">
						<header class="slds-popover__header">
							<h2 class="slds-text-heading_small" id="dialog-heading-id-103">{$MOD.LBL_RAW_TEXT}</h2>
						</header>
						<div class="slds-popover__body slds-page-header__meta-text" id="dialog-body-id-98">
							<p>2000<br>any text</p>
						</div>
						<header class="slds-popover__header">
							<h2 class="slds-text-heading_small" id="dialog-heading-id-103">{$MOD.LBL_FIELD}</h2>
						</header>
						<div class="slds-popover__body slds-page-header__meta-text" id="dialog-body-id-98">
							<p>annual_revenue<br>accountname</p>
						</div>
						<header class="slds-popover__header">
							<h2 class="slds-text-heading_small" id="dialog-heading-id-103">{$MOD.LBL_EXPRESSION}</h2>
						</header>
						<div class="slds-popover__body slds-page-header__meta-text" id="dialog-body-id-98">
							<p>annual_revenue / 12<br>
							<span style="color:blue;">if</span> mailingcountry == 'Spain' <span style="color:blue;">then</span> <span style="color:blue;">concat</span>(firstname,' ',lastname) <span style="color:blue;">else</span> <span style="color:blue;">concat</span>(lastname,' ',firstname) <span style="color:blue;">end</span>
							</p>
						</div>
						<header class="slds-popover__header">
							<h2 class="slds-text-heading_small" id="dialog-heading-id-103">{$APP.LBL_MORE}</h2>
						</header>
						<div class="slds-popover__body slds-page-header__meta-text" id="dialog-body-id-98">
							<p>See the <code>testexpression</code> variable in <a href="https://github.com/tsolucio/coreBOSTests/blob/master/modules/com_vtiger_workflow/expression_engine/VTExpressionEvaluaterTest.php" target="_blank">the unit tests</a>.</p>
						</div>
					</span>
				</section>
			</div>
		</div>
		<div class="slds-col slds-size_1-of-4 cblds-t-align_right">
			<button class="slds-button slds-button_icon slds-button_icon-border-filled" aria-haspopup="true" id="editpopup_close" title="{$APP.LBL_CLOSE}" type="button">
				<svg class="slds-button__icon" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
				</svg>
				<span class="slds-assistive-text">{$APP.LBL_CLOSE}</span>
			</button>
		</div>
	</div>
	<div class="slds-grid">
		<div class="slds-col slds-size_4-of-4 slds-p-around_xxx-small">
			<select id='editpopup_expression_type' class='slds-select'>
				<option value="rawtext">{$MOD.LBL_RAW_TEXT}</option>
				<option value="fieldname">{$MOD.LBL_FIELD}</option>
				<option value="expression">{$MOD.LBL_EXPRESSION}</option>
			</select>
		</div>
	</div>
	<div class="slds-grid">
		<div class="slds-col slds-size_2-of-4 slds-p-around_xxx-small">
			<select id='editpopup_fieldnames' class='slds-select'>
				<option value="">{$MOD.LBL_USE_FIELD_VALUE_DASHDASH}</option>
			</select>
		</div>
		<div class="slds-col slds-size_2-of-4 slds-p-around_xxx-small">
			<select id='editpopup_functions' class='slds-select'>
				<option value="">{$MOD.LBL_USE_FUNCTION_DASHDASH}</option>
			</select>
		</div>
	</div>
	<div class="slds-grid">
		<div class="slds-col slds-size_4-of-4 slds-p-around_xx-small">
			<input type="hidden" id='editpopup_field' />
			<input type="hidden" id='editpopup_field_type' />
			<textarea name="Name" id='editpopup_expression' class="slds-textarea" style="height:200px;"></textarea>
		</div>
	</div>
	<div class="slds-grid">
		<div class="slds-col slds-size_2-of-4 slds-p-around_small">
			<input id="evalid" name="evalid" type="hidden" value="">
			<input id="evalid_type" name="evalid_type" type="hidden" value="{$workflow->moduleName}">
			<input id="evalid_display" name="evalid_display" readonly type="text" style="border:1px solid #bababa;background-color:white;width:200px;" class="slds-input" value="">
			<span class="slds-icon_container slds-icon-utility-search slds-input__icon slds-p-left_x-small slds-p-right_small" onClick='return vtlib_open_popup_window("","evalid","com_vtiger_workflow","");'>
				<svg class="slds-icon slds-icon slds-icon_small slds-icon-text-default" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
				</svg>
			</span>
			<button name="evaluate" type="button" class="slds-button slds-button_brand" onclick="evaluateit();">{$MOD.evaluate}</button>
		</div>
		<div class="slds-col slds-size_2-of-4 slds-p-around_small" id="evaluateexpressionresult"></div>
	</div>
	<div class="slds-grid">
		<div class="slds-col slds-size_4-of-4 slds-p-around_small slds-align_absolute-center">
			<button name="save" id='editpopup_save' type="button" class="slds-button slds-button_success">{$APP.LBL_SAVE_BUTTON_LABEL}</button>
			<button name="cancel" id='editpopup_cancel' type="button" class="slds-button slds-button_destructive">{$APP.LBL_CANCEL_BUTTON_LABEL}</button>
		</div>
	</div>
</div>