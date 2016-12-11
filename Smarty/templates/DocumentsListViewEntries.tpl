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
{if $smarty.request.ajax neq ''}
&#&#&#{$ERROR}&#&#&#
{/if}
<table class="layerPopupTransport" width="100%">
    <tr>
        <td class="small" nowrap="" width="25%"></td>
        <td>
            <!-- Filters -->
			{if empty($HIDE_CUSTOM_LINKS) || $HIDE_CUSTOM_LINKS neq '1'}
				<table border=0 cellspacing=0 cellpadding=0 class="small" align="center">
					<tr>
						<td align="center" style="padding-left:5px;padding-right:5px">
							<b><font size=2>{$APP.LBL_VIEW}</font></b> <SELECT NAME="viewname" id="viewname" class="small" onchange="showDefaultCustomView(this,'{$MODULE}','{$CATEGORY}')">{$CUSTOMVIEW_OPTION}</SELECT>
						</td>
						{if $ALL eq 'All'}
							<td align="center" style="padding-left:5px;padding-right:5px">
								<a href="index.php?module={$MODULE}&action=CustomView&parenttab={$CATEGORY}">{$APP.LNK_CV_CREATEVIEW}</a>
								<span class="small">|</span>
								<span class="small" disabled>{$APP.LNK_CV_EDIT}</span>
								<span class="small">|</span>
								<span class="small" disabled>{$APP.LNK_CV_DELETE}</span>
							</td>
						{else}
							<td>
								<a href="index.php?module={$MODULE}&action=CustomView&parenttab={$CATEGORY}">{$APP.LNK_CV_CREATEVIEW}</a>
								<span class="small">|</span>
								{if $CV_EDIT_PERMIT neq 'yes'}
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
			{/if}
			<!-- Filters  END-->
		</td>
		<td height="38px" class="small" nowrap="" width="25%"> </td>
	</tr>
</table>
<form name="massdelete" method="POST" id="massdelete">
	<input name='search_url' id="search_url" type='hidden' value='{$SEARCH_URL}'>
	<input name="idlist" id="idlist" type="hidden">
	<input name="change_owner" type="hidden">
	<input name="change_status" type="hidden">
	<input name="action" type="hidden">
	<input name="where_export" type="hidden" value="{$export_where}">
	<input name="step" type="hidden">
	<input name="allids" type="hidden" id="allids" value="{if isset($ALLIDS)}{$ALLIDS}{/if}">
	<input name="allselectedboxes" id="allselectedboxes" type="hidden" value="">
	<input name="current_page_boxes" id="current_page_boxes" type="hidden" value="{$CURRENT_PAGE_BOXES}">
	<!-- List View Master Holder starts -->
	<table border="0" cellspacing="1" cellpadding="0" width="100%" class="lvtBg">
		<tr>
			{if $NO_FOLDERS eq 'yes'}
				<td width="100%" valign="top" height="250px;"><br><br>
					<div align="center"> <br><br><br><br><br>
						<table width="80%" cellpadding="5" cellspacing="0"  class="searchUIBasic small" align="center" border=0>
							<tr>
								<td align="center" style="padding:20px;">
									<a href="javascript:;" onclick="fnvshobj(this,'orgLay');">{$MOD.LBL_CLICK_HERE}</a>&nbsp;{$MOD.LBL_TO_ADD_FOLDER}
								</td>
							</tr>
						</table>
                    </div>
				</td>
			{else}
				<td>
					<table width="100%">
						<tr>
							<td align="center">
								<!-- List View's Buttons and Filters starts -->
								<table border=0 cellspacing=0 cellpadding=2 width=100% class="small">
									<tr>
										<td>
											<table border=0 cellspacing=0 cellpadding=0>
												<tr>
													<td style="padding-right:20px" nowrap>{include file='ListViewButtons.tpl'}</td>
												</tr>
											</table>
										</td>
										<td width="100%" align="right"></td>
									</tr>
								</table>
	                            <!-- List View's Buttons and Filters ends -->

                                {foreach item=folder from=$FOLDERS}
									<!-- folder division starts -->
									{assign var=foldercount value=$FOLDERS|@count}
		
                                    <div id='{$folder.folderid}' class="documentModuleFolderView">
										<input type="hidden" name="numOfRows" id="numOfRows_selectall{$folder.folderid}" value="">
										<input type="hidden" name="folderidVal" id="folderid_selectall{$folder.folderid}" value="{$folder.folderid}">
										<input type="hidden" name="selectedboxes" id="selectedboxes_selectall{$folder.folderid}" value="">
										<input type="hidden" name="excludedRecords" id="excludedRecords_selectall{$folder.folderid}" value="">
                                        <table class="reportsListTable" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
											<tr>
												<td class="mailSubHeader" width="40%" align="left">
													<b>{$folder.foldername}</b>
													&nbsp;&nbsp;
													{if $folder.description neq ''}
														<font class="copy">[<i>{$folder.description}</i>]</font>
													{/if}
												</td>
                                                <td class="mailSubHeader small" align="center" nowrap>{$folder.recordListRange}</td>
                                                {$folder.record_count}&nbsp;&nbsp;&nbsp;&nbsp;{$folder.navigation}
											</tr>
                                            <tr>
												<td colspan="4" >
													<div id="FileList_{$folder.folderid}">
														<!-- File list table for a folder starts -->
														<table border=0 cellspacing=1 cellpadding=3 width=100%>
                                                        <!-- Table Headers -->
															{assign var="header_count" value=$folder.header|@count}
																<tr>
																	<td class="lvtCol" width="10px"><input type="checkbox"  name="selectall{$folder.folderid}" id="currentPageRec_selectall{$folder.folderid}" onClick='toggleSelect_ListView(this.checked,"selected_id{$folder.folderid}","selectall{$folder.folderid}");'></td>
																	{foreach name="listviewforeach" item=header from=$folder.header}
																		<td class="lvtCol">{$header}</td>
																	{/foreach}
																</tr>
																<tr>
																	<td id="linkForSelectAll_selectall{$folder.folderid}" class="linkForSelectAll" style="display:none;" colspan=10>
																		<span id="selectAllRec_selectall{$folder.folderid}" class="selectall" style="display:inline;" onClick="toggleSelectDocumentRecords('{$MODULE}',true,'selected_id{$folder.folderid}','selectall{$folder.folderid}')">{$APP.LBL_SELECT_ALL} <span class="folder" id="count_selectall{$folder.folderid}"> </span> {$APP.LBL_RECORDS_IN} <span class="folder">{$folder.foldername}</span> {$APP.LBL_FOLDER}</span>
																		<span id="deSelectAllRec_selectall{$folder.folderid}" class="selectall" style="display:none;" onClick="toggleSelectDocumentRecords('{$MODULE}',false,'selected_id{$folder.folderid}','selectall{$folder.folderid}')">{$APP.LBL_DESELECT_ALL} <span class="folder">{$folder.foldername}</span> {$APP.LBL_FOLDER}</span>
																	</td>
																</tr>

																<!-- Table Contents -->

                                                                {foreach item=entity key=entity_id from=$folder.entries}
                                                                <tr class="lvtColData" bgcolor=white onMouseOver="this.className='lvtColDataHover'" onMouseOut="this.className='lvtColData'" id="row_{$entity_id}">
																	<td width="2%"><input type="checkbox" name="selected_id{$folder.folderid}" id="{$entity_id}" value= '{$entity_id}' onClick='check_object(this,"selectall{$folder.folderid}")'></td>
                                                                    {foreach item=recordid key=record_id from=$entity}
																		{foreach item=data from=$recordid}
																			{* vtlib customization: Trigger events on listview cell *}
																				<td onmouseover="vtlib_listview.trigger('cell.onmouseover', this)" onmouseout="vtlib_listview.trigger('cell.onmouseout', this)">{$data}</td>
																			{* END *}
																		{/foreach}
																	{/foreach}
																</tr>

                                                                <!-- If there are no entries for a folder -->
                                                                {foreachelse}
																	{if $foldercount eq 1}
																		<tr>
																			<td align="center" style="background-color:#efefef;height:340px" colspan="{$header_count+1}">
																				<div style="border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 45%; position: relative;">
																					{assign var=vowel_conf value='LBL_A'}
																					{assign var=MODULE_CREATE value=$SINGLE_MOD}
																					{if $CHECK.EditView eq 'yes'}
																						<table border="0" cellpadding="5" cellspacing="0" width="98%">
																							<tr>
																								<td rowspan="2" width="25%"><img src="{'empty.jpg'|@vtiger_imageurl:$THEME}" height="60" width="61"></td>
																								<td style="border-bottom: 1px solid rgb(204, 204, 204);" nowrap="nowrap" width="75%">
																									<span class="genHeaderSmall">
																										{* vtlib customization: Use translation string only if available *}
																											{$APP.LBL_NO} {if $APP.$MODULE_CREATE}{$APP.$MODULE_CREATE}{else}{$MODULE_CREATE}{/if} {$APP.LBL_FOUND} !
																									</span>
																								</td>
																							</tr>
																							<tr>
																								<td class="small" align="left" nowrap="nowrap">{$APP.LBL_YOU_CAN_CREATE} {$APP.$vowel_conf}
																									{* vtlib customization: Use translation string only if available *}
																									{if $APP.$MODULE_CREATE}
																										{$APP.$MODULE_CREATE}
																									{else}
																										{$MODULE_CREATE}
																										{/if}
																										{$APP.LBL_NOW}. {$APP.LBL_CLICK_THE_LINK}:<br>
																										&nbsp;&nbsp;-<a href="index.php?module={$MODULE}&action=EditView&return_action=DetailView&parenttab={$CATEGORY}">{$APP.LBL_CREATE} {$APP.$vowel_conf} {$APP.$MODULE_CREATE}</a>
																								</td>
																							</tr>
																						</table>
																					{else}
																						<table border="0" cellpadding="5" cellspacing="0" width="98%">
																							<tr>
																								<td rowspan="2" width="25%"><img src="{'denied.gif'|@vtiger_imageurl:$THEME}"></td>
																								<td style="border-bottom: 1px solid rgb(204, 204, 204);" nowrap="nowrap" width="75%"><span class="genHeaderSmall">
																								{if $MODULE_CREATE eq 'SalesOrder' || $MODULE_CREATE eq 'PurchaseOrder' || $MODULE_CREATE eq 'Invoice' || $MODULE_CREATE eq 'Quotes'}
																									{$APP.LBL_NO} {$APP.$MODULE_CREATE} {$APP.LBL_FOUND} !</span></td>
																								{else}
																									{* vtlib customization: Use translation string only if available *}
																									{$APP.LBL_NO} {if $APP.$MODULE_CREATE}{$APP.$MODULE_CREATE}{else}{$MODULE_CREATE}{/if} {$APP.LBL_FOUND} !</span></td>
																								{/if}
																							</tr>
																							<tr>
																								<td class="small" align="left" nowrap="nowrap">{$APP.LBL_YOU_ARE_NOT_ALLOWED_TO_CREATE} {$APP.$vowel_conf}
																									{if $MODULE_CREATE eq 'SalesOrder' || $MODULE_CREATE eq 'PurchaseOrder' || $MODULE_CREATE eq 'Invoice' || $MODULE_CREATE eq 'Quotes'}
																										{$MOD.$MODULE_CREATE}
																									{else}
																										{* vtlib customization: Use translation string only if available *}
																										{if $APP.$MODULE_CREATE}{$APP.$MODULE_CREATE}{else}{$MODULE_CREATE}{/if}
																									{/if}
																										<br>
																								</td>
																							</tr>
																						</table>
																					{/if}
																				</div>
																			</td>
																		</tr>
																	{/if}
																{/foreach}
														</table>
													</div>
                                                    <!-- File list table for a folder ends -->
												</td>
											</tr>
										</table>
									</div>
                                    <!-- folder division ends -->
								{/foreach}
                                <!-- $FOLDERS ends -->
							</td>
						{/if}
						<!-- conditional statement for $NO_FOLDERS ends -->
					</tr>
				</table>
			</td>
		</tr>
	</table>
</form>
{$SELECT_SCRIPT}
<div id="basicsearchcolumns" style="display:none;"><select name="search_field" id="bas_searchfield" class="txtBox" style="width:150px">{html_options  options=$SEARCHLISTHEADER}</select></div>
<script>
	{literal}
		function showHideFolders(show_ele, hide_ele) {
			var show_obj = document.getElementById(show_ele);
			var hide_obj = document.getElementById(hide_ele);
			if (show_obj != null) {
				show_obj.style.display="block";
			}
			if (hide_obj != null) {
				hide_obj.style.display="none";
			}
		}
	{/literal}
</script>