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
{if isset($smarty.request.ajax) && $smarty.request.ajax neq ''}
&#&#&#{$ERROR}&#&#&#
{/if}
<script type="text/javascript" src="include/js/ListView.js"></script>
<form name="massdelete" method="POST" id="massdelete" onsubmit="VtigerJS_DialogBox.block();">
	<input name='search_url' id="search_url" type='hidden' value='{$SEARCH_URL}'>
	<input name="idlist" id="idlist" type="hidden">
	<input name="change_owner" type="hidden">
	<input name="change_status" type="hidden">
	<input name="action" type="hidden">
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
				<table width="100%" class="layerPopupTransport">
					<tr>
						<td width="25%" class="small" nowrap align="left">{$recordListRange}</td>
						<td><table align="center">
								<tr>
									<td>
										<!-- Filters -->
										{if empty($HIDE_CUSTOM_LINKS) || $HIDE_CUSTOM_LINKS neq '1'}
										<table cellpadding="5" cellspacing="0" class="small">
											<tr>
												<td style="padding-left:5px;padding-right:5px" align="center">
													<b><font size=2>{$APP.LBL_VIEW}</font></b> <SELECT NAME="viewname" id="viewname" class="small" style="max-width:240px;" onchange="showDefaultCustomView(this,'{$MODULE}','{$CATEGORY}')">{$CUSTOMVIEW_OPTION}</SELECT>
												</td>
												{if isset($ALL) && $ALL eq 'All'}
												<td style="padding-left:5px;padding-right:5px" align="center"><a href="index.php?module={$MODULE}&action=CustomView&parenttab={$CATEGORY}">{$APP.LNK_CV_CREATEVIEW}</a>
													<span class="small">|</span>
													<span class="small" disabled>{$APP.LNK_CV_EDIT}</span>
													<span class="small">|</span>
													<span class="small" disabled>{$APP.LNK_CV_DELETE}</span>
												</td>
												{else}
												<td style="padding-left:5px;padding-right:5px" align="center">
													<a href="index.php?module={$MODULE}&action=CustomView&parenttab={$CATEGORY}">{$APP.LNK_CV_CREATEVIEW}</a>
													<span class="small">|</span>
													{if $CV_EDIT_PERMIT neq 'yes' || $SQLERROR}
														<span class="small" disabled>{$APP.LNK_CV_EDIT}</span>
													{else}
														<a href="index.php?module={$MODULE}&action=CustomView&record={$VIEWID}&parenttab={$CATEGORY}">{$APP.LNK_CV_EDIT}</a>
													{/if}
													<span class="small">|</span>
													{if $CV_DELETE_PERMIT neq 'yes'}
														<span class="small" disabled>{$APP.LNK_CV_DELETE}</span>
													{else}
														<a href="javascript:confirmdelete('index.php?module=CustomView&action=Delete&dmodule={$MODULE}&record={$VIEWID}&parenttab={$CATEGORY}')">{$APP.LNK_CV_DELETE}</a>
													{/if}
													{if $CUSTOMVIEW_PERMISSION.ChangedStatus neq '' && $CUSTOMVIEW_PERMISSION.Label neq ''}
														<span class="small">|</span>
														<a href="#" id="customstatus_id" onClick="ChangeCustomViewStatus({$VIEWID},{$CUSTOMVIEW_PERMISSION.Status},{$CUSTOMVIEW_PERMISSION.ChangedStatus},'{$MODULE}','{$CATEGORY}')">{$CUSTOMVIEW_PERMISSION.Label}</a>
													{/if}
												</td>
												{/if}
											</tr>
										</table>
										<!-- Filters END-->
										{/if}
									</td>
								</tr>
							</table>
						</td>
						<!-- Page Navigation -->
						<td nowrap align="right" width="25%">
							<table border=0 cellspacing=0 cellpadding=0 class="small">
								<tr>{$NAVIGATION}</tr>
							</table>
						</td>
					</tr>
				</table>
				<table border=0 cellspacing=0 cellpadding=2 width=100% class="small">
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
				{if $MODULE eq 'HelpDesk'}
					{assign var=MODULE_CREATE value='Ticket'}
				{/if}

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
					{if $CHECK.EditView eq 'yes' && $MODULE neq 'Emails' && $MODULE neq 'Webmails'}
						<table border="0" cellpadding="5" cellspacing="0" width="98%">
						<tr>
							<td rowspan="2" width="25%"><img src="{'empty.png'|@vtiger_imageurl:$THEME}" height="60" width="61"></td>
							<td style="border-bottom: 1px solid rgb(204, 204, 204);" nowrap="nowrap" width="75%">
								<span class="genHeaderSmall">{$APP.LBL_NO_DATA}</span>
							</td>
						</tr>
						<tr>
							<td class="small" align="left" nowrap="nowrap">
								{if $MODULE neq 'Calendar'}
									<b><a class="nef_action" href="index.php?module={$MODULE}&action=EditView&return_action=DetailView&parenttab={$CATEGORY}">{$APP.LBL_CREATE} {$APP.$vowel_conf}
										{if $APP.$MODULE_CREATE}{$APP.$MODULE_CREATE}{else}{$MODULE_CREATE}{/if}
										{if $CHECK.Import eq 'yes' && $MODULE neq 'Documents'}
										</a></b><br>
										<b><a class="nef_action" href="index.php?module={$MODULE}&action=Import&step=1&return_module={$MODULE}&return_action=ListView&parenttab={$CATEGORY}">{$APP.LBL_IMPORT} {$MODULE|@getTranslatedString:$MODULE}
										{/if}
									</a></b><br>
								{else}
									<b><a class="nef_action" href="index.php?module=Calendar4You&amp;action=EventEditView&amp;return_module=Calendar4You&amp;activity_mode=Events&amp;return_action=DetailView&amp;parenttab={$CATEGORY}">{$APP.LBL_CREATE} {$APP.LBL_AN} {$APP.Event}</a></b><br>
									<b><a class="nef_action" href="index.php?module=Calendar4You&amp;action=EventEditView&amp;return_module=Calendar4You&amp;activity_mode=Task&amp;return_action=DetailView&amp;parenttab={$CATEGORY}">{$APP.LBL_CREATE} {$APP.LBL_A} {$APP.Task}</a></b>
								{/if}
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
							{if $APP.$MODULE_CREATE}{$APP.$MODULE_CREATE}{else}{$MODULE_CREATE}{/if}
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
				<td style="padding-right:20px" nowrap>{include file='ListViewButtons.tpl'}</td>
				<td align="right" width=40%>
					<table border=0 cellspacing=0 cellpadding=0 class="small">
					<tr>
						{if !empty($WORDTEMPLATES)}
							{if $WORDTEMPLATES|@count gt 0}
								<td>{'LBL_SELECT_TEMPLATE_TO_MAIL_MERGE'|@getTranslatedString:$MODULE}</td>
								<td style="padding-left:5px;padding-right:5px">
									<select class="small" name="mergefile">
									{foreach key=_TEMPLATE_ID item=_TEMPLATE_NAME from=$WORDTEMPLATES}
										<option value="{$_TEMPLATE_ID}">{$_TEMPLATE_NAME}</option>
									{/foreach}
									</select>
								</td>
								<td>
									<input title="{'LBL_MERGE_BUTTON_TITLE'|@getTranslatedString:$MODULE}" accessKey="{'LBL_MERGE_BUTTON_KEY'|@getTranslatedString:$MODULE}"
										class="crmbutton small create" onclick="return massMerge('{$MODULE}')" type="submit" name="Merge" value="{'LBL_MERGE_BUTTON_LABEL'|@getTranslatedString:$MODULE}">
								</td>
							{elseif $IS_ADMIN eq 'true'}
								<td>
									<a href='index.php?module=Settings&action=upload&tempModule={$MODULE}&parenttab=Settings'>{'LBL_CREATE_MERGE_TEMPLATE'|@getTranslatedString:$MODULE}</a>
								</td>
							{/if}
						{/if}
					</tr>
					</table>
				</td>
			</tr>
			</table>
		</td>
		</tr>
		<tr>
			<td>
				<table width="100%">
					<tr>
						<td class="small" nowrap align="left">{$recordListRange}</td>
						<td nowrap width="50%" align="right">
							<table border=0 cellspacing=0 cellpadding=0 class="small">
							<tr>{$NAVIGATION}</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</form>
{if isset($SELECT_SCRIPT)}{$SELECT_SCRIPT}{/if}
<div id="basicsearchcolumns" style="display:none;"><select name="search_field" id="bas_searchfield" class="txtBox" style="width:150px">{html_options options=$SEARCHLISTHEADER}</select></div>