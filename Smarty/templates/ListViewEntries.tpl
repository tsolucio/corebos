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
{if !empty($smarty.request.ajax)}
&#&#&#{if isset($ERROR)}{$ERROR}{/if}&#&#&#
{else}
{include file='applicationmessage.tpl'}
{/if}
<script type="text/javascript" src="include/js/ListView.js"></script>
<form name="massdelete" method="POST" id="massdelete" onsubmit="VtigerJS_DialogBox.block();">
	<input name='search_url' id="search_url" type='hidden' value='{$SEARCH_URL}'>
	<input name="idlist" id="idlist" type="hidden">
	<input name="action" id="action" type="hidden">
	<input name="massedit1x1" id="massedit1x1" type="hidden" value="">
	<input name="where_export" type="hidden" value="{$export_where}">
	<input name="step" type="hidden">
	<input name="excludedRecords" type="hidden" id="excludedRecords" value="">
	<input name="numOfRows" id="numOfRows" type="hidden" value="">
	<input name="allids" type="hidden" id="allids" value="{if isset($ALLIDS)}{$ALLIDS}{/if}">
	<input name="selectedboxes" id="selectedboxes" type="hidden" value="{$SELECTEDIDS}">
	<input name="allselectedboxes" id="allselectedboxes" type="hidden" value="{$ALLSELECTEDIDS}">
	<input name="current_page_boxes" id="current_page_boxes" type="hidden" value="{$CURRENT_PAGE_BOXES}">
	<!-- List View Master Holder starts -->
	<table border=0 cellspacing=1 cellpadding=0 width=100% class="lvtBg">
		<tr>
			<td>
				<!-- List View's Buttons and Filters starts -->
				{include file='ListViewFilter.tpl'}
				<table border=0 cellspacing=0 cellpadding=2 width=100% class="small cblds-table-border_sep cblds-table-bordersp_small">
					<tr>
						<!-- Buttons -->
						<td style="padding-right:20px" nowrap>{include file='ListViewButtons.tpl'}</td>
					</tr>
				</table>
				<!-- List View's Buttons and Filters ends -->

			<div>
			<table border=0 cellspacing=1 cellpadding=3 width=100% class="lvt small">
			<!-- Table Headers -->
			<tr>
				<td class="lvtCol"><input type="checkbox" name="selectall" id="selectCurrentPageRec" onClick=toggleSelect_ListView(this.checked,"selected_id")></td>
				{foreach name="listviewforeach" item=header from=$LISTHEADER}
					<td class="lvtCol">{$header}</td>
				{/foreach}
			</tr>
			{include file="ListViewSearchBlock.tpl" SOURCE='customview' COLUMNS_BLOCK=$FIELDNAMES}
			<tr>
				<td id="linkForSelectAll" class="linkForSelectAll" style="display:none;" colspan=15>
					<span id="selectAllRec" class="selectall" style="display:inline;" onClick="toggleSelectAll_Records('{$MODULE}',true,'selected_id')">{$APP.LBL_SELECT_ALL} <span id="count"> </span> {$APP.LBL_RECORDS_IN} {$MODULE|@getTranslatedString:$MODULE}</span>
					<span id="deSelectAllRec" class="selectall" style="display:none;" onClick="toggleSelectAll_Records('{$MODULE}',false,'selected_id')">{$APP.LBL_DESELECT_ALL} {$MODULE|@getTranslatedString:$MODULE}</span>
				</td>
			</tr>
			<!-- Table Contents -->
			{foreach item=entity key=entity_id from=$LISTENTITY}
				<tr bgcolor=white onMouseOver="this.className='lvtColDataHover'" onMouseOut="this.className='lvtColData'" id="row_{$entity_id}">
					<td width="2%">{if $entity_id>0}<input type="checkbox" NAME="selected_id" id="{$entity_id}" value= '{$entity_id}' onClick="check_object(this)">{else}<span class="listview_row_sigma">&Sigma;</span>{/if}</td>
					{foreach item=data from=$entity}
						{* vtlib customization: Trigger events on listview cell *}
						<td onmouseover="vtlib_listview.trigger('cell.onmouseover', this)" onmouseout="vtlib_listview.trigger('cell.onmouseout', this)">{$data}</td>
					{/foreach}
				</tr>
			{foreachelse}
			<tr>
			<td style="background-color:#efefef;height:340px" align="center" colspan="{$smarty.foreach.listviewforeach.iteration+1}">
			<div id="no_entries_found" style="border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 45%; position: relative;">
				{assign var=vowel_conf value='LBL_A'}
				{if $MODULE eq 'Accounts' || $MODULE eq 'Invoice'}
					{assign var=vowel_conf value='LBL_AN'}
				{/if}
				{assign var=MODULE_CREATE value=$SINGLE_MOD}

				{if $SQLERROR}
					<table border="0" cellpadding="5" cellspacing="0" width="98%">
					<tr>
						<td rowspan="2" width="25%"><img src="{'empty.png'|@vtiger_imageurl:$THEME}" height="60" width="61"></td>
						<td style="border-bottom: 1px solid rgb(204, 204, 204);" nowrap="nowrap" width="75%">
							<span class="genHeaderSmall">{$APP.LBL_NO_DATA}</span>
						</td>
					</tr>
					<tr>
						<td class="small" align="left" nowrap="nowrap">{'ERROR_GETTING_FILTER'|@getTranslatedString:$MODULE}</td>
					</tr>
					</table>
				{else}
					{if $CHECK.EditView eq 'yes' && $MODULE neq 'Emails'}
						<table border="0" cellpadding="5" cellspacing="0" width="98%">
						<tr>
							<td rowspan="2" width="25%"><img src="{'empty.png'|@vtiger_imageurl:$THEME}" height="60" width="61"></td>
							<td style="border-bottom: 1px solid rgb(204, 204, 204);" nowrap="nowrap" width="75%">
								<span class="genHeaderSmall">{$APP.LBL_NO_DATA}</span>
							</td>
						</tr>
						<tr>
							<td class="small" align="left" nowrap="nowrap">
								<b><a class="nef_action" href="index.php?module={$MODULE}&action=EditView&return_action=DetailView">{$APP.LBL_CREATE} {$APP.$vowel_conf}
									{$MODULE_CREATE|@getTranslatedString:$MODULE}
									{if $CHECK.Import eq 'yes' && $MODULE neq 'Documents'}
									</a></b><br>
									<b><a class="nef_action" href="index.php?module={$MODULE}&action=Import&step=1&return_module={$MODULE}&return_action=ListView">{$APP.LBL_IMPORT} {$MODULE|@getTranslatedString:$MODULE}
									{/if}
								</a></b><br>
							</td>
						</tr>
						</table>
					{else}
						<table border="0" cellpadding="5" cellspacing="0" width="98%">
						<tr>
							<td rowspan="2" width="25%"><img src="{'denied.gif'|@vtiger_imageurl:$THEME}"></td>
							<td style="border-bottom: 1px solid rgb(204, 204, 204);" nowrap="nowrap" width="75%"><span class="genHeaderSmall">{$APP.LBL_NO_DATA}</span></td>
						</tr>
						<tr>
							<td class="small" align="left" nowrap="nowrap">{$APP.LBL_YOU_ARE_NOT_ALLOWED_TO_CREATE} {$APP.$vowel_conf}
							{$MODULE_CREATE|@getTranslatedString:$MODULE}
							<br>
							</td>
						</tr>
						</table>
					{/if}
				{/if} {* SQL ERROR ELSE END *}
			</div>
			</td>
			</tr>
			{/foreach}
			</table>
			</div>

			<table border=0 cellspacing=0 cellpadding=2 width=100%>
			<tr>
				<td style="padding-right:20px" nowrap></td>
				<td align="right" width=40%>&nbsp;</td>
			</tr>
			</table>
		</td>
		</tr>
		<tr>
			<td>
				<table width="100%">
					<tr>
						<td class="small" nowrap align="left">{$recordListRange}</td>
						<td nowrap width="50%" align="right" class="cblds-t-align_right">
							<table border=0 cellspacing=0 cellpadding=0 class="small" style="display: inline-block;">
							<tr>{$NAVIGATION}</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</form>
<div id="basicsearchcolumns" style="display:none;">
	<select name="search_field" id="bas_searchfield" class="txtBox" style="width:150px">
	{html_options options=$SEARCHLISTHEADER}
	</select>
</div>
