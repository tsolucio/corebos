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
<script type="text/javascript" src="include/jquery/jquery.js"></script>
<script type="text/javascript" src="include/jquery/jquery-ui.js"></script>
<script type="text/javascript" src="include/js/smoothscroll.js"></script>
<script type="text/javaScript" src="include/js/dedup.js"></script>

<form enctype="multipart/form-data" name="mergeDuplicates" method="post" action="index.php?module={$MODULE}&action=FindDuplicateRecords" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="module" value="{$MODULE}">
	<input type="hidden" name="parenttab" value="{$CATEGORY}">
	<input type="hidden" name="action" value="FindDuplicateRecords">
	<input type="hidden" name="selectedColumnsString"/>
	<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout searchUIBasic" style="width: 80%;" align="center">
		<thead>
			<tr class="slds-text-title--header">
				<th scope="col">
					<div class="slds-truncate moduleName">
						<span class="moduleName">{$APP.LBL_SELECT_MERGECRITERIA_HEADER}</span>
					</div>
					<span >{$APP.LBL_SELECT_MERGECRITERIA_TEXT}</span>
				</th>
				<th scope="col" style="padding: .5rem;text-align: right;">
					<div class="slds-truncate">
						<span align="right" onClick="mergeshowhide('mergeDup')" onmouseover="this.style.cursor='pointer';">
							<img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0">
						</span>
					</div>
				</th>
			</tr>
		</thead>
	</table>
	<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout searchUIBasic" style="width: 80%;border-top: none;" align="center">
		<tbody>
			<tr class="slds-line-height--reset">
				<td class="dvtCellLabel text-left" width="45%"><b>{$APP.LBL_AVAILABLE_FIELDS}</b></td>
				<td class="dvtCellLabel" width="10%">&nbsp;</td>
				<td class="dvtCellLabel text-left" width="45%"><b>{$APP.LBL_SELECTED_FIELDS}</b></td>
			</tr>
			<tr>
				<td class="dvtCellInfo" width="45%">
					<select id="availList" multiple size="10" name="availList" class="slds-select" style="height:100px;width:100%">{$AVALABLE_FIELDS}</select>
				</td>
				<td class="dvtCellInfo" width="10%">
					<div align="center">
						<input type="button" name="Button" value="&nbsp;&rsaquo;&rsaquo;&nbsp;" onClick="addColumn()" class="slds-button slds-button--small slds-button_success btn-width" /><br /><br />
						<input type="button" name="Button1" value="&nbsp;&lsaquo;&lsaquo;&nbsp;" onClick="delColumn()" class="slds-button slds-button--small slds-button--destructive btn-width" /><br /><br />
					</div>
				</td>
				<td class="dvtCellInfo" width="45%">
					<select id="selectedColumns" size="10" name="selectedColumns" multiple class="slds-select" style="height:100px;width:100%">{$FIELDS_TO_MERGE}</select>
				</td>
			</tr> 
			<tr>
				<td colspan="3" align="center">
					<input type="submit" name="save&merge" value="{$APP.LBL_SAVE_MERGE_BUTTON_TITLE}" class="slds-button slds-button--small slds-button_success" onClick="return formSelectColumnString()"/>
					<input type="button" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="slds-button slds-button--small slds-button--destructive" type="button" onClick="mergeshowhide('mergeDup');">
				</td>
			</tr>
		</tbody>
	</table>
</form>

