{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}

{if empty($smarty.request.ajax)}
<table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td colspan="4" class="dvInnerHeader">
	<div style="float: left; font-weight: bold;">
	<div style="float: left;">
	<a href="javascript:showHideStatus('tbl{$UIKEY}','aid{$UIKEY}','$IMAGE_PATH');"><img id="aid{$UIKEY}" src="{'activate.gif'|@vtiger_imageurl:$THEME}" style="border: 0px solid rgb(0, 0, 0);" alt="Hide" title="Hide"></a>
	</div><b>&nbsp;{$WIDGET_TITLE}</b></div>
	<span style="float: right;">
		<img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border=0 id="indicator{$UIKEY}" style="display:none;">
		{$APP.LBL_SHOW} <select class="small" onchange="ModCommentsCommon.reloadContentWithFiltering('{$WIDGET_NAME}', '{$ID}', this.value, 'tbl{$UIKEY}', 'indicator{$UIKEY}');">
			<option value="All" {if $CRITERIA eq 'All'}selected{/if}>{$APP.LBL_ALL}</option>
			<option value="Last5" {if $CRITERIA eq 'Last5'}selected{/if}>{$MOD.LBL_LAST5}</option>
			<option value="Mine" {if $CRITERIA eq 'Mine'}selected{/if}>{$MOD.LBL_MINE}</option>
		</select>
	</span>
	</td>
</tr>
</table>
{/if}

<div id="tbl{$UIKEY}">

	<table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">

	<tr style="height: 25px;">
		<td colspan="4" align="left" class="dvtCellInfo" >
		<div id="contentwrap_{$UIKEY}" style="overflow: auto; height: 250px; width: 100%;">
			{foreach item=COMMENTMODEL from=$COMMENTS}
				{include file="modules/ModComments/widgets/DetailViewBlockCommentItem.tpl" COMMENTMODEL=$COMMENTMODEL}
			{/foreach}
		</div>
		</td>
	</tr>

	<tr style="height: 25px;">
	<td class="dvtCellLabel" align="right">
		{$MOD.LBL_ADD_COMMENT}
	</td>
	<td width="100%" colspan="3" class="dvtCellInfo" align="left">
		<div id="editarea_{$UIKEY}">
			<textarea id="txtbox_{$UIKEY}" class="detailedViewTextBox" onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'" cols="90" rows="8"></textarea>
			<br><input type="button" class="crmbutton small save" value="{$APP.LBL_SAVE_LABEL}" onclick="ModCommentsCommon.addComment('{$UIKEY}', '{$ID}');"/> {$APP.LBL_OR}
			<a href="javascript:;" onclick="$('txtbox_{$UIKEY}').value='';" class="link">{$APP.LBL_CLEAR_BUTTON_LABEL}</a>
		</div>
	</td>
	</tr>

	</table>
</div>
