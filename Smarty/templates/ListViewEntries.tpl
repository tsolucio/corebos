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
   <input name="where_export" type="hidden" value="">
   <input name="step" type="hidden">
   <input name="excludedRecords" type="hidden" id="excludedRecords" value="">
   <input name="numOfRows" id="numOfRows" type="hidden" value="">
   <input name="allids" type="hidden" id="allids" value="{if isset($ALLIDS)}{$ALLIDS}{/if}">
   <input name="selectedboxes" id="selectedboxes" type="hidden" value="{$SELECTEDIDS}">
   <input name="allselectedboxes" id="allselectedboxes" type="hidden" value="{$ALLSELECTEDIDS}">
   <input name="current_page_boxes" id="current_page_boxes" type="hidden" value="{$CURRENT_PAGE_BOXES}">
</form>
<!-- List View's Buttons and Filters starts -->
<table width="100%" class="layerPopupTransport">
   <tr>
      <td width="25%" class="small" nowrap align="left">
         <span id="gridRecordCountHeader"></span>
         <span id="filteredData"></span>
      </td>
      <td>
         <table align="center">
            <tr>
               <td>
                  <!-- Filters -->
                  {if empty($HIDE_CUSTOM_LINKS) || $HIDE_CUSTOM_LINKS neq '1'}
                  <table cellpadding="5" cellspacing="0" class="small cblds-table-border_sep cblds-table-bordersp_medium">
                     <tr>
                        <td style="padding-left:5px;padding-right:5px" align="center">
                           <b><font size=2>{$APP.LBL_VIEW}</font></b> 
                           <span id="filterOptions"></span>
                        </td>
                        <td style="padding-left:5px;padding-right:5px" align="center">
                           <a href="index.php?module={$MODULE}&action=CustomView&parenttab={$CATEGORY}">{$APP.LNK_CV_CREATEVIEW}</a>
                           <span id="filterEditActions"></span>
                           <span id="filterDeleteActions"></span>
                        </td>
                     </tr>
                  </table>
                  <!-- Filters END-->
                  {/if}
               </td>
            </tr>
         </table>
      </td>
      <!-- Page Navigation -->
      <td nowrap align="right" width="25%" class="cblds-t-align_right">
      </td>
   </tr>
</table>
<table border=0 cellspacing=0 cellpadding=2 width=100% class="small cblds-table-border_sep cblds-table-bordersp_small">
   <tr>
      <td style="padding-right:20px" nowrap>{include file='ListViewButtons.tpl'}</td>
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
<div id="listview-tui-grid"></div>
<!-- List View Master Holder starts -->
<table border=0 cellspacing=1 cellpadding=0 width=100% class="lvtBg">
   <tr>
      <td>
         <table border=0 cellspacing=0 cellpadding=2 width=100%>
            <tr>
               <td style="padding-right:20px" nowrap>{include file='ListViewButtons.tpl'}</td>
               <td align="right" width=40%>&nbsp;</td>
            </tr>
         </table>
      </td>
   </tr>
   <tr>
      <td>
         <table width="100%">
            <tr>
               <td class="small" nowrap align="left"><span id="gridRecordCountFooter"></span></td>
               <td nowrap width="50%" align="right" class="cblds-t-align_right">
               </td>
            </tr>
         </table>
      </td>
   </tr>
</table>
<div id="basicsearchcolumns" style="display:none;">
   <select name="search_field" id="bas_searchfield" class="txtBox" style="width:150px">
   {html_options options=$SEARCHLISTHEADER}
   </select>
</div>
