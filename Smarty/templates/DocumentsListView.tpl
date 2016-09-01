{*<!--
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}
<script type="text/javascript" src="include/js/ListView.js"></script>
<script type="text/javascript" src="include/js/search.js"></script>
<script type="text/javascript" src="include/js/Merge.js"></script>
<script type="text/javascript" src="include/js/dtlviewajax.js"></script>
<script type="text/javascript" src="include/js/FieldDependencies.js"></script>
<script type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>
		{include file='Buttons_List.tpl'}
                                <div id="searchingUI" style="display:none;">
                                        <table border=0 cellspacing=0 cellpadding=0 width=100%>
                                        <tr>
                                                <td align=center>
                                                <img src="{'searching.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_SEARCHING}"  title="{$APP.LBL_SEARCHING}">
                                                </td>
                                        </tr>
                                        </table>
                                </div>
                        </td>
                </tr>
                </table>
        </td>
</tr>
</table>

{*<!-- Contents -->*}
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
     <tr>
        <td valign=top><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>

	<td class="showPanelBg" valign="top" width=100% style="padding:10px;">
	 <!-- SIMPLE SEARCH -->
<div id="searchAcc" style="{$DEFAULT_SEARCH_PANEL_STATUS};position:relative;">
<form name="basicSearch" method="post" action="index.php" onSubmit="document.basicSearch.searchtype.searchlaunched='basic';return callSearch('Basic');">
<table width="100%" cellpadding="5" cellspacing="0"  class="searchUIBasic small" align="center" border=0>
	<tr>
		<td class="searchUIName small" nowrap align="left">
		<span class="moduleName">{$APP.LBL_SEARCH}</span><br><span class="small"><a href="#" onClick="fnhide('searchAcc');show('advSearch');document.basicSearch.searchtype.value='advance';document.basicSearch.searchtype.searchlaunched='';">{$APP.LBL_GO_TO} {$APP.LNK_ADVANCED_SEARCH}</a></span>
		<!-- <img src="themes/images/basicSearchLens.gif" align="absmiddle" alt="{$APP.LNK_BASIC_SEARCH}" title="{$APP.LNK_BASIC_SEARCH}" border=0>&nbsp;&nbsp;-->
		</td>
		<td class="small" nowrap align=right><b>{$APP.LBL_SEARCH_FOR}</b></td>
		<td class="small"><input type="text"  class="txtBox" style="width:120px" name="search_text"></td>
		<td class="small" nowrap><b>{$APP.LBL_IN}</b>&nbsp;</td>
		<td class="small" nowrap>
			<div id="basicsearchcolumns_real">
			<select name="search_field" id="bas_searchfield" class="txtBox" style="width:150px">
			 {html_options  options=$SEARCHLISTHEADER }
			</select>
			</div>
                        <input type="hidden" name="searchtype" value="BasicSearch">
                        <input type="hidden" name="module" value="{$MODULE}" id="curmodule">
						<input name="maxrecords" type="hidden" value="{$MAX_RECORDS}" id='maxrecords'>
                        <input type="hidden" name="parenttab" value="{$CATEGORY}">
						<input type="hidden" name="action" value="index">
                        <input type="hidden" name="query" value="true">
						<input type="hidden" name="search_cnt">
		</td>
		<td class="small" nowrap width=40% >
			  <input name="submit" type="button" class="crmbutton small create" onClick="callSearch('Basic');document.basicSearch.searchtype.searchlaunched='basic';" value=" {$APP.LBL_SEARCH_NOW_BUTTON} ">&nbsp;
		</td>
		<td class="small closeX" valign="top" onMouseOver="this.style.cursor='pointer';" onclick="moveMe('searchAcc');searchshowhide('searchAcc','advSearch');document.basicSearch.searchtype.searchlaunched='';">[x]</td>
	</tr>
	<tr>
		<td colspan="7" align="center" class="small">
			<table border=0 cellspacing=0 cellpadding=0 width=100%>
				<tr>
                                                {$ALPHABETICAL}
                                </tr>
                        </table>
		</td>
	</tr>
</table>
</form><br class="searchbreak">
</div>
<!-- ADVANCED SEARCH -->
<div id="advSearch" style="display:none;">
<form name="advSearch" method="post" action="index.php" onSubmit="document.basicSearch.searchtype.searchlaunched='advance';return callSearch('Advanced');">
	<table  cellspacing=0 cellpadding=5 width=100% class="searchUIAdv1 small" align="center" border=0>
		<tr>
			<td class="searchUIName small" nowrap align="left"><span class="moduleName">{$APP.LBL_SEARCH}</span><br><span class="small"><a href="#" onClick="show('searchAcc');fnhide('advSearch');document.basicSearch.searchtype.searchlaunched='';">{$APP.LBL_GO_TO} {$APP.LNK_BASIC_SEARCH}</a></span></td>
			<td class="small closeX" align="right" valign="top" onMouseOver="this.style.cursor='pointer';" onclick="moveMe('searchAcc');searchshowhide('searchAcc','advSearch');document.basicSearch.searchtype.searchlaunched='';">[x]</td>
		</tr>
	</table>
	<table cellpadding="2" cellspacing="0" width="100%" align="center" class="searchUIAdv2 small" border=0>
		<tr>
			<td align="center" class="small" width=90%>
				{include file='AdvanceFilter.tpl' SOURCE='customview' COLUMNS_BLOCK=$FIELDNAMES}
			</td>
		</tr>
	</table>

	<table border=0 cellspacing=0 cellpadding=5 width=100% class="searchUIAdv3 small" align="center">
		<tr>
			<td align="center" class="small"><input type="button" class="crmbutton small create" value=" {$APP.LBL_SEARCH_NOW_BUTTON} " onClick="callSearch('Advanced');document.basicSearch.searchtype.searchlaunched='advance';">
			</td>
		</tr>
	</table>
</form><br>
</div>
{*<!-- Searching UI -->*}
 
 <div id="mergeDup" style="z-index:1;display:none;position:relative;">
	{include file="MergeColumns.tpl"}
</div>
	   <!-- PUBLIC CONTENTS STARTS-->
	  <div id="ListViewContents" class="small" style="width:100%;">
	 		{include file="DocumentsListViewEntries.tpl"}
	</div>

     </td>
   </tr>
</table>

<!-- Add new Folder UI for Documents module starts -->
<script type="text/javascript" src="modules/Documents/Documents.js"></script>
<div id="orgLay" style="display:none;width:350px;" class="layerPopup" >
        <table border=0 cellspacing=0 cellpadding=5 width=100% class=layerHeadingULine>
	        <tr>
				<td class="genHeaderSmall" nowrap align="left" width="30%" id="editfolder_info">{$MOD.LBL_ADD_NEW_FOLDER}
				</td>
				<td align="right"><a href="javascript:;" onClick="closeFolderCreate();"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border="0"></a>
				</td>
	        </tr>
        </table>
        <table border=0 cellspacing=0 cellpadding=5 width=95% align=center>
        <tr>
			<td class="small">
				<table border=0 cellspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
				<tr>
					<td align="right" nowrap class="cellLabel small"><font color='red'>*</font>&nbsp;<b>{$MOD.LBL_FOLDER_NAME}</b></td>
					<td align="left" class="cellText small">
					<input id="folder_id" name="folderId" type="hidden" value=''>
					<input id="fldrsave_mode" name="folderId" type="hidden" value='save'>
					<input id="folder_name" name="folderName" class="txtBox" type="text"> &nbsp;&nbsp;{$MOD.LBL_MAXIMUM_20}
					</td>
				</tr>
				<tr>
					<td class="cellLabel small" align="right" nowrap><b>{$MOD.LBL_FOLDER_DESC}</b>
					</td>
 					<td class="cellText small" align="left"><input id="folder_desc" name="folderDesc" class="txtBox" type="text"> &nbsp;&nbsp;{$MOD.LBL_MAXIMUM_50}
					</td>
				 </tr>
				</table>
			</td>
        </tr>
 	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
        <tr>
			<td class="small" align="center">
                <input name="save" value=" &nbsp;{$APP.LBL_SAVE_BUTTON_LABEL}&nbsp; " class="crmbutton small save" onClick="AddFolder();" type="button">&nbsp;&nbsp;
                <input name="cancel" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="crmbutton small cancel" onclick="closeFolderCreate();" type="button">
			</td>
        </tr>
	</table>
</div>

<!-- Add new folder UI for Documents module ends -->