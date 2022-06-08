{*
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
*}
<table border=0 cellspacing=1 cellpadding=0 width=100%>
<tr><td>
	<!-- List View's Buttons and Filters starts -->
	<table border=0 cellspacing=0 cellpadding=2 width=100% class="small">
	<tr>
	<!-- Buttons -->
			<td style="padding-right:20px" nowrap>
				<input type="hidden" id="search_url" value="{if isset($SEARCH_URL)}{$SEARCH_URL}{/if}">
				<input type="hidden" name="idlist" id="idlist">
				<input name="numOfRows" id="numOfRows" type="hidden" value="{$NUMOFROWS}">
				<input name="maxrecords" type="hidden" value="{$MAX_RECORDS}" id='maxrecords'>
				<input type="hidden" id="excludedRecords" name="excludedRecords" value="{if isset($excludedRecords)}{$excludedRecords}{/if}">
				<input type="hidden" name="allselectedboxes" id="allselectedboxes" value="{$ALLSELECTEDIDS}">
				<input name="current_page_boxes" id="current_page_boxes" type="hidden" value="{$CURRENT_PAGE_BOXES}">
				<input type="hidden" name="selected_module" id="selected_module" value="{$SELECTED_MODULE}">
				<input type="hidden" name="selected_module_translated" id="selected_module_translated" value="{$SELECTED_MODULE|@getTranslatedString:$SELECTED_MODULE}">
				<input class="crmbutton small edit" type="button" onclick ="massRestore();" value="{$MOD.LBL_MASS_RESTORE}">
				{if $IS_ADMIN eq 'true'}
				<input class="crmbutton small delete" type="button" onclick ="callEmptyRecyclebin();" value="{$MOD.LBL_EMPTY_RBMODULE}">
				<input class="crmbutton small delete" type="button" onclick ="document.getElementById('rb_empty_conf_id').style.display = 'block';" value="{$MOD.LBL_EMPTY_RECYCLEBIN}">
				{/if}
			</td>
				<!-- Record Counts -->
			<td style="padding-right:20px" class="small" nowrap id="gridRecordCountHeader"></td>
			<td style="padding-right:20px" class="small" nowrap id="filteredData"></td>
			<!-- Page Navigation -->
			<td width=100% align="right" class="cblds-t-align_right">
				<b>{$MOD.LBL_SELECT_MODULE} : </b>
				<select id="select_module" onChange="ListView.Show('RecycleBin');" class="small">
				{foreach key=mod_name item=module from=$MODULE_NAME}
				{assign var="modulelabel" value=$module|@getTranslatedString:$module}
				{if $module eq $SELECTED_MODULE}
					<option value="{$module}" selected>{$modulelabel}</option>
				{else}
						<option value="{$module}">{$modulelabel}</option>
				{/if}
				{/foreach}
				</select>
			</td>
		</tr>
	</table>
	<table border=0 cellspacing=1 cellpadding=3 width=100%>
		<tr>
			<td id="linkForSelectAll" class="linkForSelectAll" style="display:none;" colspan=15>
				<span id="selectAllRec" class="selectall" style="display:inline;" onClick="toggleSelectAll_Records('{$MODULE}',true,'selected_id[]')">{$APP.LBL_SELECT_ALL} <span id="count"> </span> {$APP.LBL_RECORDS_IN} {$MODULE|@getTranslatedString:$MODULE}</span>
				<span id="deSelectAllRec" class="selectall" style="display:none;" onClick="toggleSelectAll_Records('{$MODULE}',false,'selected_id[]')">{$APP.LBL_DESELECT_ALL} {$MODULE|@getTranslatedString:$MODULE}</span>
			</td>
		</tr>
	</table>
	<!-- List View's Buttons and Filters ends -->
	<div id="listview-tui-grid"></div>
	<table border=0 cellspacing=1 cellpadding=3 width=100% class="lvt small">
	<!-- Table Headers -->
	<tr><td>
	<table border=0 cellspacing=0 cellpadding=2 width=100% class="small">
		<tr>
			<!-- Buttons -->
			<td style="padding-right:20px" nowrap>
				<input type="hidden" name="idlist" id="idlist">
				<input type="hidden" name="selected_module" id="selected_module" value="{$SELECTED_MODULE}">
				<input class="crmbutton small edit" type="button" onclick ="massRestore();" value="{$MOD.LBL_MASS_RESTORE}">
				{if $IS_ADMIN eq 'true'}
					<input class="crmbutton small delete" type="button" onclick ="callEmptyRecyclebin();" value="{$MOD.LBL_EMPTY_RBMODULE}">
					<input class="crmbutton small delete" type="button" onclick ="document.getElementById('rb_empty_conf_id').style.display = 'block';" value="{$MOD.LBL_EMPTY_RECYCLEBIN}">
				{/if}
			</td>
			<!-- Record Counts -->
			<td style="padding-right:20px" class="small" nowrap id="gridRecordCountFooter"></td>
			<!-- Page Navigation -->
			<td width=100% align="right">&nbsp;</td>
		</tr>
	</table>
</td></tr>
</table>
</div>