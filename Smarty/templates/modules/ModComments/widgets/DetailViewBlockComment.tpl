{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}

{if empty($smarty.request.ajax)}
	<input type="hidden" id="comments_parentId" value="{$ID}" />
	<div class="slds-section slds-is-open" style="margin-bottom: 0rem !important">
		<h3 class="slds-section__title">
			<button aria-expanded="true" class="slds-button slds-section__title-action" onclick="showHideStatus('tbl{$UIKEY}','aid{$UIKEY}','$IMAGE_PATH');">
				{if $BLOCKOPEN}
					<svg class="slds-section__title-action-icon slds-button__icon slds-button__icon_left" aria-hidden="true" id="svg_tbl{$UIKEY}_block">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#switch"></use>
					</svg>
					<svg class="slds-section__title-action-icon slds-button__icon slds-button__icon_left" aria-hidden="true" id="svg_tbl{$UIKEY}_none" style="display: none">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevronright"></use>
					</svg>
				{else}
					<svg class="slds-section__title-action-icon slds-button__icon slds-button__icon_left" aria-hidden="true" id="svg_tbl{$UIKEY}_block" style="display: none">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#switch"></use>
					</svg>
					<svg class="slds-section__title-action-icon slds-button__icon slds-button__icon_left" aria-hidden="true" id="svg_tbl{$UIKEY}_none">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevronright"></use>
					</svg>
				{/if}
				<span class="slds-truncate" title="{$WIDGET_TITLE}">
					<strong>{$WIDGET_TITLE}</strong>
				</span>
			</button>
		</h3>
		<span style="float: right;position:relative; left: -16px; top: -25px;">
			{$APP.LBL_SHOW} <select class="small" onchange="ModCommentsCommon.reloadContentWithFiltering('{$WIDGET_NAME}', '{$ID}', this.value, 'tbl{$UIKEY}', 'indicator{$UIKEY}');">
				<option value="All" {if $CRITERIA eq 'All'}selected{/if}>{$APP.LBL_ALL}</option>
				<option value="Last5" {if $CRITERIA eq 'Last5'}selected{/if}>{$MOD.LBL_LAST5}</option>
				<option value="Mine" {if $CRITERIA eq 'Mine'}selected{/if}>{$MOD.LBL_MINE}</option>
			</select>
		</span>
	</div>
	{/if}
	<div id="tbl{$UIKEY}" style="display: {if $BLOCKOPEN}block{else}none{/if};">
		<table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr style="height: 25px;">
			<td colspan="4" align="left" class="dvtCellInfo commentCell">
			<div id="contentwrap_{$UIKEY}" style="overflow: auto; margin-bottom: 20px; width: 100%; word-break: break-all;">
				{foreach item=COMMENTMODEL from=$COMMENTS}
					{include file="modules/ModComments/widgets/DetailViewBlockCommentItem.tpl" COMMENTMODEL=$COMMENTMODEL}
				{/foreach}
			</div>
			</td>
		</tr>
		{if $CANADDCOMMENTS eq 'YES'}
		<tr style="height: 25px;" class='noprint'>
		<td class="dvtCellLabel" align="right">
			{$MOD.LBL_ADD_COMMENT}
		</td>
		<td width="100%" colspan="3" class="dvtCellInfo" align="left">
			<div id="editarea_{$UIKEY}">
				<textarea id="txtbox_{$UIKEY}" class="detailedViewTextBox" onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'" cols="90" rows="8"></textarea>
				<br>
				<button
					class="slds-button slds-button_success"
					title="{$APP.LBL_SAVE_LABEL}"
					onclick="ModCommentsCommon.addComment('{$UIKEY}', '{$ID}');"
					style="color:#ffffff;"
					>
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
						</svg>
						{$APP.LBL_SAVE_LABEL}
				</button>
				<button
					class="slds-button slds-button_neutral"
					title="{$APP.LBL_CLEAR_BUTTON_LABEL}"
					onclick="document.getElementById('txtbox_{$UIKEY}').value='';"
					style="margin-left: 0;"
					>
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#redo"></use>
						</svg>
						{$APP.LBL_CLEAR_BUTTON_LABEL}
				</button>
			</div>
		</td>
		</tr>
		{/if}
		</table>
	</div>
