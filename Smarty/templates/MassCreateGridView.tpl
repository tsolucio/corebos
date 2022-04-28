{*<!--
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  coreBOS Open Source
 * The Initial Developer of the Original Code is coreBOS.
 * All Rights Reserved.
********************************************************************************/
-->*}
<script type="text/javascript">
	var GridColumns = '{$GridColumns}';
	var EmptyData = '{$EmptyData}';
	var ListFields = '{$ListFields}';
	var bmapname = '{$bmapname}';
</script>
<script src="./include/MassCreateGridView/MassCreateGridView.js"></script>
<div class="demo-only demo-only_viewport slds-align_absolute-center" id="slds-spinner" style="z-index: 9999;display: none">
	<div role="status" class="slds-spinner slds-spinner_medium">
		<span class="slds-assistive-text">Loading</span>
		<div class="slds-spinner__dot-a"></div>
		<div class="slds-spinner__dot-b"></div>
	</div>
</div>
<table border=0 cellspacing=0 cellpadding=2 width=100% class="small cblds-table-border_sep cblds-table-bordersp_small">
<div class="slds-button-group" role="group" style="margin-bottom: 5px">
	<button class="slds-button slds-button_neutral" onclick="MCGrid.Append()" accesskey="A">
		<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use>
		</svg>
		{$APP.LBL_ADD_ROW}
	</button>
	<button class="slds-button slds-button_neutral" onclick="MCGrid.Save()">
		<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
		</svg>
		{$APP.LBL_SAVE_BUTTON_LABEL}
	</button>
	<button class="slds-button slds-button_text-destructive" onclick="MCGrid.Delete()">
		<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
		</svg>
		{$APP.LBL_DELETE_BUTTON}
	</button>
</div>
<div class="slds-button-group slds-float_right" role="group" style="margin-bottom: 5px;">
	<button class="slds-button slds-button_neutral" onclick="MCGrid.EditFields()">
		<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
		</svg>
		{$APP.LBL_EDIT_COLUMNS}
	</button>
</div>
</table>
<div id="listview-tui-grid"></div>