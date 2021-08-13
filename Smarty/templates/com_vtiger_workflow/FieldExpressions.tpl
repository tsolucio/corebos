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
#editpopup {
	top:35%;
	left:20%;
	height:350px;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function (event) {
	loadJS('index.php?module=cbMap&action=cbMapAjax&file=getjslanguage');
});
var wfexpfndefs = {$FNDEFS};
var wfexpselectionDIV = 'selectfunction';
</script>
<div id="selectfunction"></div>
<div id='editpopup' class='layerPopup slds-align_absolute-center' style='display:none;z-index:1;'>
	<div id='editpopup_draghandle' style='cursor: move;' class="slds-grid slds-badge_lightest">
		<div class="slds-col slds-size_3-of-4 slds-page-header__title slds-m-top_xx-small">
			<div>
				&nbsp;{$MOD.LBL_SET_VALUE}&nbsp;
			</div>
		</div>
		<div class="slds-col slds-size_1-of-4 cblds-t-align_right">
			<button class="slds-button slds-button_icon slds-button_icon-border-filled" aria-pressed="false" title="{'LNK_HELP'|@getTranslatedString}" type="button" onclick="toggleExpEditorHelp(this);">
				<svg class="slds-button__icon" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info"></use>
				</svg>
				<span class="slds-assistive-text">{'LNK_HELP'|@getTranslatedString}</span>
			</button>
			<button class="slds-button slds-button_icon slds-button_icon-border-filled" aria-haspopup="true" id="editpopup_close" title="{$APP.LBL_CLOSE}" type="button">
				<svg class="slds-button__icon" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
				</svg>
				<span class="slds-assistive-text">{$APP.LBL_CLOSE}</span>
			</button>
		</div>
	</div>
	<div id="exphelpbody" style="display:none;">
		<h2 class="slds-expression__title slds-m-left_xx-small">{$MOD.EXP_RULES}</h2>
		<ul class="slds-list_dotted">
			<li>{$MOD.EXP_RULE1} {'Example'|getTranslatedString:'cbMap'}, <code>first_name=='John'</code></li>
			<li>{$MOD.EXP_RULE2}</li>
			<li>{$MOD.EXP_RULE3} == ({$MOD['equal to']}), != ({$MOD['not equal to']}), &gt;, &lt;, &gt;=, &lt;=</li>
			<li>{$MOD.EXP_RULE4}</li>
		</ul>
		<h2 class="slds-expression__title slds-m-left_xx-small">{'Examples'|getTranslatedString:'cbMap'}</h2>
		<div class="slds-grid">
		<div class="slds-col slds-size_1-of-5">
			<header class="slds-popover__header">
				<h2 class="slds-text-heading_small">{$MOD.LBL_RAW_TEXT}</h2>
			</header>
			<div class="slds-popover__body slds-page-header__meta-text" id="dialog-body-id-98">
				<p>2000<br>any text</p>
			</div>
		</div>
		<div class="slds-col slds-size_1-of-5">
			<header class="slds-popover__header">
				<h2 class="slds-text-heading_small">{$MOD.LBL_FIELD}</h2>
			</header>
			<div class="slds-popover__body slds-page-header__meta-text" id="dialog-body-id-98">
				<p>annual_revenue<br>accountname</p>
			</div>
		</div>
		<div class="slds-col slds-size_2-of-5">
			<header class="slds-popover__header">
				<h2 class="slds-text-heading_small">{$MOD.LBL_EXPRESSION}</h2>
			</header>
			<div class="slds-popover__body slds-page-header__meta-text" id="dialog-body-id-98">
				<p>annual_revenue / 12<br>
				<span style="color:blue;">if</span> mailingcountry == 'Spain' <span style="color:blue;">then</span> <span style="color:blue;">concat</span>(firstname,' ',lastname) <span style="color:blue;">else</span> <span style="color:blue;">concat</span>(lastname,' ',firstname) <span style="color:blue;">end</span>
				</p>
			</div>
		</div>
		<div class="slds-col slds-size_1-of-5">
			<header class="slds-popover__header">
				<h2 class="slds-text-heading_small">{$APP.LBL_MORE}</h2>
			</header>
			<div class="slds-popover__body slds-page-header__meta-text" id="dialog-body-id-98">
				<p>See the <code>testexpression</code> variable in <a href="https://github.com/tsolucio/coreBOSTests/blob/master/modules/com_vtiger_workflow/expression_engine/VTExpressionEvaluaterTest.php" target="_blank">the unit tests</a>.</p>
			</div>
		</div>
		</div>
	</div>
	<div id="expeditorbody">
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
			<button class="slds-button slds-button_neutral" id="editpopup_functions" onclick="return openFunctionSelection('editpopup_expression');">
				<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
				</svg>
				{$MOD.LBL_USE_FUNCTION_DASHDASH}
			</button>
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
			<input id="evalid_type" name="evalid_type" type="hidden" value="{if isset($workflow)}{$workflow->moduleName}{/if}">
			<input id="evalid_display" name="evalid_display" readonly type="text" style="border:1px solid #bababa;background-color:white;width:200px;" class="slds-input" value="" onClick='return vtlib_open_popup_window("","evalid","com_vtiger_workflow","");'>
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
</div>