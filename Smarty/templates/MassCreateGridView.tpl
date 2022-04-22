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
</script>
<script src="./include/MassCreateGridView/MassCreateGridView.js"></script>
<table border=0 cellspacing=0 cellpadding=2 width=100% class="small cblds-table-border_sep cblds-table-bordersp_small">
<div class="slds-button-group" role="group" style="margin-bottom: 5px">
	<button class="slds-button slds-button_neutral" onclick="MCGrid.Append()" accesskey="A">
		<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use>
		</svg>
		Add Row
	</button>
	<button class="slds-button slds-button_neutral" onclick="MCGrid.Save()">
		<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
		</svg>
		Save
	</button>
	<button class="slds-button slds-button_text-destructive" onclick="MCGrid.Delete()">
		<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
		</svg>
		Delete
	</button>
</div>
</table>
<div id="listview-tui-grid"></div>