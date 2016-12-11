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

{*<!-- module header -->*}
<script type="text/javascript" src="include/js/general.js"></script>
<script type="text/javascript" src="include/js/ListView.js"></script>
<script type="text/javascript" src="include/js/search.js"></script>
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
<!-- Dropdown for Add Event Button -->
<div id='addButtonDropDown' style='width:160px' onmouseover='fnShowButton()' onmouseout='fnRemoveButton()'>
<table width="100%" cellpadding="0" cellspacing="0" border="0">{$ADD_BUTTONEVENTLIST}</table>
</div>
{*<!-- Contents -->*}
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
     <tr>
        <td valign=top><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>

	<td class="showPanelBg" valign="top" width=100% style="padding:10px;">
	 <!-- SIMPLE SEARCH -->
<div id="searchAcc" style="display: block;position:relative;">
<form name="basicSearch" method="post" action="index.php" onSubmit="return callSearch('Basic');">
<table width="98%" cellpadding="5" cellspacing="0"  class="searchUIBasic small" align="center" border=0>
	<tr>
		<td class="searchUIName small" nowrap align="left">
		<span class="moduleName">{$APP.LBL_SEARCH}</span><br><span class="small"><a href="#" onClick="fnhide('searchAcc');show('advSearch');document.basicSearch.searchtype.value='advance';">{$APP.LBL_GO_TO} {$APP.LNK_ADVANCED_SEARCH}</a></span>
		<!-- <img src="{'basicSearchLens.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" alt="{$APP.LNK_BASIC_SEARCH}" title="{$APP.LNK_BASIC_SEARCH}" border=0>&nbsp;&nbsp;-->
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
			  <input name="submit" type="button" class="crmbutton small create" onClick="callSearch('Basic');" value=" {$APP.LBL_SEARCH_NOW_BUTTON} ">&nbsp;
		</td>
		<td class="small" valign="top" onMouseOver="this.style.cursor='pointer';" onclick="moveMe('searchAcc');searchshowhide('searchAcc','advSearch')">[x]</td>
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
</form>
</div>
<!-- ADVANCED SEARCH -->
<div id="advSearch" style="display:none;">
<form name="advSearch" method="post" action="index.php" onSubmit="return callSearch('Advanced');">
	<table  cellspacing=0 cellpadding=5 width=98% class="searchUIAdv1 small" align="center" border=0>
		<tr>
			<td class="searchUIName small" nowrap align="left"><span class="moduleName">{$APP.LBL_SEARCH}</span><br><span class="small"><a href="#" onClick="show('searchAcc');fnhide('advSearch')">{$APP.LBL_GO_TO} {$APP.LNK_BASIC_SEARCH}</a></span></td>
			<td class="small" align="right" valign="top" onMouseOver="this.style.cursor='pointer';" onclick="moveMe('searchAcc');searchshowhide('searchAcc','advSearch')">[x]</td>
		</tr>
	</table>
	<table cellpadding="2" cellspacing="0" width="98%" align="center" class="searchUIAdv2 small" border=0>
		<tr>
			<td align="center" class="small" width=90%>
				{include file='AdvanceFilter.tpl' SOURCE='customview' COLUMNS_BLOCK=$FIELDNAMES}
			</td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=98% class="searchUIAdv3 small" align="center">
		<tr>
			<td align="center" class="small"><input type="button" class="crmbutton small create" value=" {$APP.LBL_SEARCH_NOW_BUTTON} " onClick="callSearch('Advanced');">
			</td>
		</tr>
	</table>
</form>
</div>
</div>
{*<!-- Searching UI -->*}

<div class="small" style="padding: 10px;">
	<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td>
				<table class="small" border="0" cellpadding="3" cellspacing="0" width="100%">
				<tr>
					<td class="dvtTabCache" style="width: 10px;" nowrap="nowrap">&nbsp;</td>
					<td class="dvtUnSelectedCell" align="center" nowrap="nowrap"><a href="index.php?action=index&module=Calendar4You&parenttab={$CATEGORY}">{'Calendar4You'|getTranslatedString:'Calendar4You'}</a></td>
					<td class="dvtTabCache" style="width: 10px;">&nbsp;</td>
					<td class="dvtSelectedCell" align="center" nowrap="nowrap">{$CMOD.LBL_ALL_EVENTS_TODOS}</td>
					<td class="dvtTabCache" style="width: 100%;">&nbsp;</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">
				<table class="dvtContentSpace" border="0" cellpadding="3" cellspacing="0" width="100%">
					<tr>
						<td align="left">
							<!-- content cache -->
							<table border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td style="padding: 10px;">

	   <!-- PUBLIC CONTENTS STARTS-->
	   <div id="ListViewContents" class="small" style="width:100%;position:relative;">
     <form name="massdelete" method="POST">
     <input name='search_url' id="search_url" type='hidden' value='{$SEARCH_URL}'>
     <input name="idlist" id="idlist" type="hidden">
     <input name="change_owner" type="hidden">
     <input name="change_status" type="hidden">
	 <input name="numOfRows" id="numOfRows" type="hidden" value="{$NUMOFROWS}">
	 <input name="excludedRecords" type="hidden" id="excludedRecords" value="{$excludedRecords}">
     <input name="allids" id="allids" type="hidden" value="{if isset($ALLIDS)}{$ALLIDS}{/if}">
     <input name="selectedboxes" id="selectedboxes" type="hidden" value="{$SELECTEDIDS}">
     <input name="allselectedboxes" id="allselectedboxes" type="hidden" value="{$ALLSELECTEDIDS}">
     <input name="current_page_boxes" id="current_page_boxes" type="hidden" value="{$CURRENT_PAGE_BOXES}">
               <table border=0 cellspacing=1 cellpadding=0 width=100% class="lvtBg">
	            <tr >
		      <td>
                            <table class="layerPopupTransport" width="100%">
                                <tr>
                                    <td class="small" nowrap width="25%">
						{$recordListRange}
					</td>
				 <td align="center">
				   <table border=0 cellspacing=0 cellpadding=0 class="small">
					<tr>
						<td style="padding-left:5px;padding-right:5px">
							<b><font size =2>{$APP.LBL_VIEW}</font></b> <SELECT NAME="viewname" id="viewname" class="small" onchange="showDefaultCustomView(this,'{$MODULE}','{$CATEGORY}')">{$CUSTOMVIEW_OPTION}</SELECT>
                        </td>
                        <td>
                            {if $ALL eq 'All'}
								<a href="index.php?module={$MODULE}&action=CustomView&parenttab={$CATEGORY}">{$APP.LNK_CV_CREATEVIEW}</a>
								<span class="small">|</span>
								<span class="small" disabled>{$APP.LNK_CV_EDIT}</span>
								<span class="small">|</span>
                            	<span class="small" disabled>{$APP.LNK_CV_DELETE}</span></td>
						    {else}
								<a href="index.php?module={$MODULE}&action=CustomView&parenttab={$CATEGORY}">{$APP.LNK_CV_CREATEVIEW}</a>
								<span class="small">|</span>
                                <a href="index.php?module={$MODULE}&action=CustomView&record={$VIEWID}&parenttab={$CATEGORY}">{$APP.LNK_CV_EDIT}</a>
                                <span class="small">|</span>
								<a href="javascript:confirmdelete('index.php?module=CustomView&action=Delete&dmodule={$MODULE}&record={$VIEWID}&parenttab={$CATEGORY}')">{$APP.LNK_CV_DELETE}</a>
						    {/if}
							{if $CUSTOMVIEW_PERMISSION.ChangedStatus neq '' && $CUSTOMVIEW_PERMISSION.Label neq ''}
								<span class="small">|</span>
							   		<a href="#" id="customstatus_id" onClick="ChangeCustomViewStatus({$VIEWID},{$CUSTOMVIEW_PERMISSION.Status},{$CUSTOMVIEW_PERMISSION.ChangedStatus},'{$MODULE}','{$CATEGORY}')">{$CUSTOMVIEW_PERMISSION.Label}</a>
							{/if}
						</td>
					</tr>
				   </table>
				 </td><!-- Page Navigation -->
					<td nowrap width="25%" align="right">
						<table border=0 cellspacing=0 cellpadding=0 class="small">
							<tr>{$NAVIGATION}</tr>
						</table>
	                </td>
                        </tr></table>
		         <table border=0 cellspacing=0 cellpadding=2 width=100% class="small">
			      <tr>
				 <td style="padding-right:20px" nowrap>
                                 {foreach key=button_check item=button_label from=$BUTTONS}
                                        {if $button_check eq 'del'}
                                             <input class="crmbutton small delete" type="button" value="{$button_label}" onclick="return massDelete('{$MODULE}')"/>
                                        {elseif $button_check eq 's_mail'}
                                             <input class="crmbutton small edit" type="button" value="{$button_label}" onclick="return eMail('{$MODULE}',this);"/>
                                        {elseif $button_check eq 's_cmail'}
                                             <input class="crmbutton small edit" type="submit" value="{$button_label}" onclick="return massMail('{$MODULE}')"/>
                                        {elseif $button_check eq 'c_status'}
                                             <input class="crmbutton small edit" type="button" value="{$button_label}" onclick="return change(this,'changestatus')"/>
					{elseif $button_check eq 'c_owner'}
						{if $MODULE neq 'Documents' && $MODULE neq 'Products' && $MODULE neq 'Faq' && $MODULE neq 'Vendors' && $MODULE neq 'PriceBooks'}
						     <input class="crmbutton small edit" type="button" value="{$button_label}" onclick="return change(this,'changeowner')"/>
                                                {/if}
                                        {/if}

                                 {/foreach}
                
		                {* vtlib customization: Custom link buttons on the List view basic buttons *}
						{if $CUSTOM_LINKS && $CUSTOM_LINKS.LISTVIEWBASIC}
							{foreach item=CUSTOMLINK from=$CUSTOM_LINKS.LISTVIEWBASIC}
								{assign var="customlink_href" value=$CUSTOMLINK->linkurl}
								{assign var="customlink_label" value=$CUSTOMLINK->linklabel}
								{if $customlink_label eq ''}
									{assign var="customlink_label" value=$customlink_href}
								{else}
									{* Pickup the translated label provided by the module *}
									{assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
								{/if}
								<input class="crmbutton small edit" type="button" value="{$customlink_label}" onclick="{$customlink_href}" />
							{/foreach}
						{/if}
						
						{* vtlib customization: Custom link buttons on the List view *}
						{if $CUSTOM_LINKS && !empty($CUSTOM_LINKS.LISTVIEW)}
							&nbsp;
							<a href="javascript:;" onmouseover="fnvshobj(this,'vtlib_customLinksLay');" onclick="fnvshobj(this,'vtlib_customLinksLay');">
								<b>{$APP.LBL_MORE} {$APP.LBL_ACTIONS} <img src="{'arrow_down.gif'|@vtiger_imageurl:$THEME}" border="0"></b>
							</a>
							<div style="display: none; left: 193px; top: 106px;width:155px; position:absolute;" id="vtlib_customLinksLay" 
								onmouseout="fninvsh('vtlib_customLinksLay')" onmouseover="fnvshNrm('vtlib_customLinksLay')">
								<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr><td style="border-bottom: 1px solid rgb(204, 204, 204); padding: 5px;"><b>{$APP.LBL_MORE} {$APP.LBL_ACTIONS} &#187;</b></td></tr>
								<tr>
									<td>
										{foreach item=CUSTOMLINK from=$CUSTOM_LINKS.LISTVIEW}
											{assign var="customlink_href" value=$CUSTOMLINK->linkurl}
											{assign var="customlink_label" value=$CUSTOMLINK->linklabel}
											{if $customlink_label eq ''}
												{assign var="customlink_label" value=$customlink_href}
											{else}
												{* Pickup the translated label provided by the module *}
												{assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
											{/if}
											<a href="{$customlink_href}" class="drop_down">{$customlink_label}</a>
										{/foreach}
									</td>
								</tr>
								</table>
							</div>
						{/if}
						{* END *}
                    </td>
       		       </tr>
			 </table>
                         <div  class="calDIV" style="overflow:auto;">
			 <table border=0 cellspacing=1 cellpadding=3 width=100% class="lvt small" class="small">
			      <tr>
             			 <td class="lvtCol"><input type="checkbox"  name="selectall" id="selectCurrentPageRec" onClick=toggleSelect_ListView(this.checked,"selected_id")></td>
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
			      {foreach item=entity key=entity_id from=$LISTENTITY}
			      <tr bgcolor=white onMouseOver="this.className='lvtColDataHover'" onMouseOut="this.className='lvtColData'" id="row_{$entity_id}">
				 <td width="2%"><input type="checkbox" NAME="selected_id" id="{$entity_id}" value= '{$entity_id}' onClick=check_object(this); toggleSelectAll(this.name,"selectall")></td>
				 {foreach item=data from=$entity}
				 <td>{$data}</td>
	                         {/foreach}
			      </tr>
			      {foreachelse}
				<tr><td style="background-color:#efefef;height:340px" align="center" colspan="{$smarty.foreach.listviewforeach.iteration+1}">
						<div style="border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 45%; position: relative; z-index: 10000000;">
							{assign var=vowel_conf value='LBL_A'}
							{assign var=MODULE_CREATE value=$SINGLE_MOD}

							{if $CHECK.EditView eq 'yes' && $MODULE neq 'Emails' && $MODULE neq 'Webmails'}
							
							<table border="0" cellpadding="5" cellspacing="0" width="98%">
							<tr>
								<td rowspan="2" width="25%"><img src="{'empty.jpg'|@vtiger_imageurl:$THEME}" height="60" width="61"></td>
								<td style="border-bottom: 1px solid rgb(204, 204, 204);" nowrap="nowrap" width="75%"><span class="genHeaderSmall">{$APP.LBL_NO} {$APP.ACTIVITIES} {$APP.LBL_FOUND} !</span></td>
							</tr>
							<tr>
							<td class="small" align="left" nowrap="nowrap">{$APP.LBL_YOU_CAN_CREATE} {$APP.$vowel_conf} {$APP.$MODULE_CREATE} {$APP.LBL_NOW}. {$APP.LBL_CLICK_THE_LINK}:<br>
									&nbsp;&nbsp;-<a href="index.php?module={$MODULE}&amp;action=EditView&amp;return_module=Calendar&amp;activity_mode=Events&amp;return_action=DetailView&amp;parenttab={$CATEGORY}">{$APP.LBL_CREATE} {$APP.LBL_AN} {$APP.Event}</a><br>
									&nbsp;&nbsp;-<a href="index.php?module={$MODULE}&amp;action=EditView&amp;return_module=Calendar&amp;activity_mode=Task&amp;return_action=DetailView&amp;parenttab={$CATEGORY}">{$APP.LBL_CREATE} {$APP.LBL_A} {$APP.Todo}</a>
								</td>
							</tr>
							</table>
							{else}
							<table border="0" cellpadding="5" cellspacing="0" width="98%">
							<tr>
								<td rowspan="2" width="25%"><img src="{'empty.jpg'|@vtiger_imageurl:$THEME}" height="60" width="61"></td>
								<td style="border-bottom: 1px solid rgb(204, 204, 204);" nowrap="nowrap" width="75%"><span class="genHeaderSmall">{$APP.LBL_NO} {$APP.ACTIVITIES} {$APP.LBL_FOUND} !</span></td>
							</tr>
							<tr>
								<td class="small" align="left" nowrap="nowrap">{$APP.LBL_YOU_ARE_NOT_ALLOWED_TO_CREATE} {$APP.$vowel_conf} {$APP.$MODULE_CREATE}<br>
								</td>
							</tr>
							</table>
							{/if}
						</div>
				</td></tr>
			      {/foreach}
			 </table>
			 </div>
			 <table border=0 cellspacing=0 cellpadding=2 width=100%>
			      <tr>
				 <td style="padding-right:20px" nowrap>
                                 {foreach key=button_check item=button_label from=$BUTTONS}
                                        {if $button_check eq 'del'}
                                            <input class="crmbutton small delete" type="button" value="{$button_label}" onclick="return massDelete('{$MODULE}')"/>
                                        {elseif $button_check eq 's_mail'}
                                             <input class="crmbutton small edit" type="button" value="{$button_label}" onclick="return eMail('{$MODULE}',this)"/>
                                        {elseif $button_check eq 's_cmail'}
                                             <input class="crmbutton small edit" type="submit" value="{$button_label}" onclick="return massMail('{$MODULE}')"/>
                                        {elseif $button_check eq 'c_status'}
                                             <input class="crmbutton small edit" type="button" value="{$button_label}" onclick="return change(this,'changestatus')"/>
					{elseif $button_check eq 'c_owner'}
				                {if $MODULE neq 'Documents' && $MODULE neq 'Products' && $MODULE neq 'Faq' && $MODULE neq 'Vendors' && $MODULE neq 'PriceBooks'}
                                                     <input class="crmbutton small edit" type="button" value="{$button_label}" onclick="return change(this,'changeowner')"/>
                                                {/if}
                                        {/if}

                                 {/foreach}
						
						{* vtlib customization: Custom link buttons on the List view basic buttons *}
						{if $CUSTOM_LINKS && $CUSTOM_LINKS.LISTVIEWBASIC}
							{foreach item=CUSTOMLINK from=$CUSTOM_LINKS.LISTVIEWBASIC}
								{assign var="customlink_href" value=$CUSTOMLINK->linkurl}
								{assign var="customlink_label" value=$CUSTOMLINK->linklabel}
								{if $customlink_label eq ''}
									{assign var="customlink_label" value=$customlink_href}
								{else}
									{* Pickup the translated label provided by the module *}
									{assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
								{/if}
								<input class="crmbutton small edit" type="button" value="{$customlink_label}" onclick="{$customlink_href}" />
							{/foreach}
						{/if}
						
						{* vtlib customization: Custom link buttons on the List view *}
						{if $CUSTOM_LINKS && !empty($CUSTOM_LINKS.LISTVIEW)}
							&nbsp;
							<a href="javascript:;" onmouseover="fnvshobj(this,'vtlib_customLinksLay');" onclick="fnvshobj(this,'vtlib_customLinksLay');">
								<b>{$APP.LBL_MORE} {$APP.LBL_ACTIONS} <img src="{'arrow_down.gif'|@vtiger_imageurl:$THEME}" border="0"></b>
							</a>
							<div style="display: none; left: 193px; top: 106px;width:155px; position:absolute;" id="vtlib_customLinksLay" 
								onmouseout="fninvsh('vtlib_customLinksLay')" onmouseover="fnvshNrm('vtlib_customLinksLay')">
								<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr><td style="border-bottom: 1px solid rgb(204, 204, 204); padding: 5px;"><b>{$APP.LBL_MORE} {$APP.LBL_ACTIONS} &#187;</b></td></tr>
								<tr>
									<td>
										{foreach item=CUSTOMLINK from=$CUSTOM_LINKS.LISTVIEW}
											{assign var="customlink_href" value=$CUSTOMLINK->linkurl}
											{assign var="customlink_label" value=$CUSTOMLINK->linklabel}
											{if $customlink_label eq ''}
												{assign var="customlink_label" value=$customlink_href}
											{else}
												{* Pickup the translated label provided by the module *}
												{assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
											{/if}
											<a href="{$customlink_href}" class="drop_down">{$customlink_label}</a>
										{/foreach}
									</td>
								</tr>
								</table>
							</div>
						{/if}
						{* END *}
                    </td>
				 <td align="right" width=100%>
				   <table border=0 cellspacing=0 cellpadding=0 class="small">
					<tr>
						{$WORDTEMPLATEOPTIONS}{$MERGEBUTTON}
					</tr>
				   </table>
				 </td>
			      </tr>
                              <tr>
                                    <td class="small" nowrap width="50%">
						{$recordListRange}
                                    </td>
                                    <!-- Page Navigation -->
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
{$SELECT_SCRIPT}
	</div>
	 </td></tr></table>
	 </td></tr></table>
         </td></tr></table>
        </div>
     </td>
        <td valign=top><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
   </tr>
</table>

<div id="changeowner" class="statechange">
<table width="100%" border="0" cellpadding="3" cellspacing="0">
<tr>
	<td class="genHeaderSmall" align="left" style="border-bottom:1px solid #CCCCCC;" width="60%">{$APP.LBL_CHANGE_OWNER}</td>
	<td style="border-bottom: 1px solid rgb(204, 204, 204);">&nbsp;</td>
	<td align="right" style="border-bottom:1px solid #CCCCCC;" width="40%"><a href="javascript:fninvsh('changeowner')">{$APP.LBL_CLOSE}</a></td>
</tr>
<tr>
	<td colspan="3">&nbsp;</td>
</tr>
<tr>
	<td width="50%"><b>{$APP.LBL_TRANSFER_OWNERSHIP}</b></td>
	<td width="2%"><b>:</b></td>
	<td width="48%">
	        <form name="change_ownerform_name">
		        <input type = "radio" id= "user_checkbox" name = "user_lead_owner"  {if $CHANGE_GROUP_OWNER neq ''} onclick=checkgroup();{/if}  checked>{$APP.LBL_USER}&nbsp;
			{if $CHANGE_GROUP_OWNER neq ''}
			<input type = "radio" id = "group_checkbox" name = "user_lead_owner" onclick=checkgroup(); >{$APP.LBL_GROUP}<br>
			<select name="lead_group_owner" id="lead_group_owner" class="detailedViewTextBox" style="display:none;">
				{$CHANGE_GROUP_OWNER}
			</select>
			{/if}
			<select name="lead_owner" id="lead_owner" class="detailedViewTextBox">
				{$CHANGE_OWNER}
			</select>
		</form>
	</td>
</tr>
<tr><td colspan="3" style="border-bottom:1px dashed #CCCCCC;">&nbsp;</td></tr>
<tr>
	<td colspan="3" align="center">
	&nbsp;&nbsp;
	<input type="button" name="button" class="crmbutton small edit" value="{$APP.LBL_UPDATE_OWNER}" onClick="ajaxChangeStatus('owner')">
	<input type="button" name="button" class="crmbutton small cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onClick="fninvsh('changeowner')">
</td>
</tr>
</table>
</div>
<script>
{literal}
function ajaxChangeStatus(statusname){
	document.getElementById("status").style.display="inline";
	var viewid = document.massdelete.viewname.value;
	var excludedRecords=document.getElementById("excludedRecords").value;
	var idstring = document.getElementById('allselectedboxes').value;
	if(statusname == 'status'){
		fninvsh('changestatus');
		var url='&leadval='+document.getElementById('lead_status').options[document.getElementById('lead_status').options.selectedIndex].value;
		var urlstring ="module=Users&action=updateLeadDBStatus&return_module=Leads"+url+"&viewname="+viewid+"&idlist="+idstring+"&excludedRecords="+excludedRecords;
	} else if(statusname == 'owner') {
		
	   if(document.getElementById("user_checkbox").checked) {
		    fninvsh('changeowner');
		    var url='&owner_id='+document.getElementById('lead_owner').options[document.getElementById('lead_owner').options.selectedIndex].value+'&owner_type=User';
		    {/literal}
		        var urlstring ="module=Users&action=updateLeadDBStatus&return_module={$MODULE}"+url+"&viewname="+viewid+"&idlist="+idstring+"&excludedRecords="+excludedRecords;
		    {literal}
        } else {
            fninvsh('changeowner');
    		    var url='&owner_id='+document.getElementById('lead_group_owner').options[document.getElementById('lead_group_owner').options.selectedIndex].value+'&owner_type=Group';
    	       {/literal}
    		        var urlstring ="module=Users&action=updateLeadDBStatus&return_module={$MODULE}"+url+"&viewname="+viewid+"&idlist="+idstring+"&excludedRecords="+excludedRecords;
    		    {literal}
        }   
	}
	jQuery.ajax({
			method:"POST",
			url:'index.php?'+ urlstring
	}).done(function(response) {
			document.getElementById("status").style.display="none";
			result = response.split('&#&#&#');
			document.getElementById("ListViewContents").innerHTML= result[2];
			if(result[1] != '')
				alert(result[1]);
			document.getElementById('basicsearchcolumns').innerHTML = '';
	});
}
</script>
{/literal}
