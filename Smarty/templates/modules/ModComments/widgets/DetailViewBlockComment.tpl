{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions modified/created by Peter A. Gebhardt 2012-Jan-06
 * All Rights Reserved.
 ************************************************************************************
 *}

<!-- script language="JavaScript" type="text/javascript">
{literal}
function toggleDiv(element){
 if(document.getElementById(element).style.display == 'none')
  document.getElementById(element).style.display = 'block';
 else
       document.getElementById(element).style.display = 'none';
}
{/literal}
</script -->

{if empty($smarty.request.ajax)}
<table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
 <td colspan="4" class="dvInnerHeader">
	<div style="float: left; font-weight: bold;">
	<div style="float: left;valign: top; padding:5px;"> 
	<!-- <a href="javascript:showHideStatus('tbl{$UIKEY}','aid{$UIKEY}','$IMAGE_PATH');"><img id="aid{$UIKEY}" src="{'activate.gif'|@vtiger_imageurl:$THEME}" style="border: 0px solid rgb(0, 0, 0);" alt="Hide" title="Hide"></a> -->
	<!-- </div>&nbsp;{$WIDGET_TITLE} (Use Add Comment to toggle display!)</div> -->
	
	<span style="float: right;">
		<img src="themes/images/vtbusy.gif" border=0 id="indicator{$UIKEY}" style="display:none;">
		{$APP.LBL_SHOW} <select class="small" onchange="ModCommentsCommon.reloadContentWithFiltering('{$WIDGET_NAME}', '{$ID}', this.value, 'tbl{$UIKEY}', 'indicator{$UIKEY}');">
			<option value="All" {if $CRITERIA eq 'All'}selected{/if}>{$APP.LBL_ALL}</option>
			<option value="Last5" {if $CRITERIA eq 'Last5'}selected{/if}>{$MOD.LBL_LAST5}</option>
			<option value="Mine" {if $CRITERIA eq 'Mine'}selected{/if}>{$MOD.LBL_MINE}</option>
		</select>
	</span>
	<!-- <span style="valign: top; padding:5px;"> &nbsp;Use Add Comment to toggle display!</span> -->
	<span style="float: right;">
		&nbsp;&nbsp; <input type="button" class="crmbutton small save" value="{$MOD.LBL_ADD_COMMENT}" onclick="javascript:toggleDiv('editarea_{$UIKEY}');">&nbsp;&nbsp;</input>
	</span>
 </td>
</tr>
<tr style="height: 8px;"> 
	<td width="100%" colspan="2" class="dvtCellInfo" align="left">
		<div id="editarea_{$UIKEY}" style="display:none;">
			<textarea id="txtbox_{$UIKEY}" class="detailedViewTextBox" onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'" cols="95" rows="8"></textarea>
		 
			<br><input type="button" class="crmbutton small save" value="{$APP.LBL_SAVE_LABEL}" onclick="ModCommentsCommon.addComment('{$UIKEY}', '{$ID}');"> {$APP.LBL_OR}
			<a href="javascript:;" onclick="$('txtbox_{$UIKEY}').value='';" class="link">{$APP.LBL_CLEAR_BUTTON_LABEL}</a> 

		 <!-- pag 2012	
			<br><input type="button" class="crmbutton small save" value="{$APP.LBL_SAVE_LABEL}" 
						onclick="ModCommentsCommon.addComment('{$UIKEY}', '{$ID}');">{$APP.LBL_OR}
			<a href="javascript:;" onclick="hndCancel('dtlview_{$UIKEY}','editarea_{$UIKEY}','{$UIKEY}')" class="link">{$APP.LBL_CLEAR_BUTTON_LABEL}</a>
		-->		
		</div>
	</td>	
</tr>
</table>
{/if}


{if not empty($COMMENTS)}
<table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">

<div id="tbl{$UIKEY}" style="display:none;">
	<tr style="height: 25px;">
		<td colspan="4" align="left" class="dvtCellInfo" >
		<div id="contentwrap_{$UIKEY}" style="overflow: auto; height: 120px; width: 100%;"> <!-- pag 2011-Nov-17 250px change for screen estate -->
			{foreach item=COMMENTMODEL from=$COMMENTS}
				{include file="modules/ModComments/widgets/DetailViewBlockCommentItem.tpl" COMMENTMODEL=$COMMENTMODEL}
			{/foreach}
		</div>
		</td>
	</tr>
	
	<tr style="height: 8px;">
<!-- 
	<td width="100%" colspan="3" class="dvtCellInfo" align="left" id="mouseArea_{$UIKEY}" onmouseover="hndMouseOver(19,'{$UIKEY}');" onmouseout="fnhide('crmspanid');">
		<span id="dtlview_{$UIKEY}"></span>
		<div id="editarea_{$UIKEY}" style="display:none;">
			<textarea id="txtbox_{$UIKEY}" class="detailedViewTextBox" onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'" cols="90" rows="8"></textarea>
			<br><input type="button" class="crmbutton small save" value="{$APP.LBL_SAVE_LABEL}" onclick="ModCommentsCommon.addComment('{$UIKEY}', '{$ID}');fnhide('crmspanid');"/> {$APP.LBL_OR}
			<a href="javascript:;" onclick="hndCancel('dtlview_{$UIKEY}','editarea_{$UIKEY}','{$UIKEY}')" class="link">{$APP.LBL_CANCEL_BUTTON_LABEL}</a>
		</div>
	</td>	
-->						
	</tr>
</table>
</div>

{/if}
