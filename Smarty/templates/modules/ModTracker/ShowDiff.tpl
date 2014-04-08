{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}

<div id="orgLay" class="layerPopup">

<!-- Styles for highlighting the string diff -->
<style type='text/css'>
{literal}
del { text-decoration: none; display: none; }
ins { text-decoration: none; background-color: #FDFF00; }
{/literal}
</style>

<table class="layerHeadingULine" border="0" cellpadding="5" cellspacing="0" width="100%">
<tr>
	<td class="layerPopupHeading" align="left" width="70%">
		{$TRACKRECORD->getDisplayName()}

		{* Disabling highlighting triggers *}
		{* START
		{if $smarty.request.highlight eq 'true'}
		<img src="{'public.gif'|@vtiger_imageurl:$THEME}" border=0> <a href='javascript:void(0);' style='display: inline' onclick="ModTrackerCommon.showdiff({$TRACKRECORD->id}, {$ATPOINT}, false);">{'LBL_TURN_OFF_HIGHLIGHTING'|@getTranslatedString:$MODULE}</a>
		{else}
		<img src="{'onstar.gif'|@vtiger_imageurl:$THEME}" border=0> <a href='javascript:void(0);' style='display: inline' onclick="ModTrackerCommon.showdiff({$TRACKRECORD->id}, {$ATPOINT}, true);">{'LBL_TURN_ON_HIGHLIGHTING'|@getTranslatedString:$MODULE}</a>
		{/if}
		END *}
	</td>
	<td align="right" width="2%" valign="top">
		<a href='javascript:void(0);'><img src="{'close.gif'|@vtiger_imageurl:$THEME}" onclick="ModTrackerCommon.hide();" align="right" border="0"></a>
	</td>
</tr>
</table>

<table class="layerHeadingULine" border="0" cellpadding="5" cellspacing="0" width="100%">
<tr>
	<td>{'LBL_CHANGED_BY'|@getTranslatedString:$MODULE} {$TRACKRECORD->getModifiedByLabel()} @ {$TRACKRECORD->getModifiedOn()}</td>

	<td align="right" width="10%">
		{if $ATPOINT_PREV neq $ATPOINT}
			<a href='javascript:void(0);'><img src="{'previous.gif'|@vtiger_imageurl:$THEME}" onclick="ModTrackerCommon.showhistory({$TRACKRECORD->crmid},{$ATPOINT_PREV});" border="0"></a>
		{else}
			<a href='javascript:void(0);'><img src="{'previous_disabled.gif'|@vtiger_imageurl:$THEME}" border="0"></a>
		{/if}

		{if $ATPOINT gt 0}
			<a href='javascript:void(0);'><img src="{'next.gif'|@vtiger_imageurl:$THEME}" onclick="ModTrackerCommon.showhistory({$TRACKRECORD->crmid},{$ATPOINT_NEXT});" border="0"></a>
		{else}
			<a href='javascript:void(0);'><img src="{'next_disabled.gif'|@vtiger_imageurl:$THEME}" border="0"></a>
		{/if}
	</td>
</tr>
</table>

<table border=0 cellspacing=1 cellpadding=0 width=100% class="lvtBg">
<tr>
	<td>
		<table border="0" cellpadding="4" cellspacing="1" width="100%" class='lvt small'>
		<tr valign="top">
			<td width='20%' class='lvtCol'><b>{'LBL_Field'|@getTranslatedString:$MODULE}</b></td>
			<td width='40%' class='lvtCol'><b>{'LBL_Earlier'|@getTranslatedString:$MODULE}</b></td>
			<td width='40%' class='lvtCol'><b>{'LBL_Present'|@getTranslatedString:$MODULE}</b></td>
		</tr>
		{foreach item=DETAIL from=$TRACKRECORD->getDetails()}
		<tr valign=top>
			<td class='dvtCellLabel'>{$DETAIL->getDisplayName()}</td>
			<td class='lvtColData'>{$DETAIL->getDisplayLabelForPreValue()}</td>
			<td class='lvtColData'>{if $smarty.request.highlight eq 'true'}{$DETAIL->diffHighlight()}{else}{$DETAIL->getDisplayLabelForPostValue()}{/if}</td>
		</tr>
        {foreachelse}
        <tr>
			<td colspan="3" align="center">
				{'LBL_ACCESS_TO_FIELD_CHANGES_DENIED'|getTranslatedString:$MODULE}
			</td>
		</tr>
		{/foreach}
		<tr>
			<td class='lvtColData' colspan="3" align="center">
				<input value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" onclick="ModTrackerCommon.hide();" type="button">
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
</div>
