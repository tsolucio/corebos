{*<!--
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  coreBOS Open Source
 * The Initial Developer of the Original Code is coreBOS.
 * All Rights Reserved.
********************************************************************************/
-->*}
{foreach key=CUSTOMLINK_NO item=CUSTOMLINK from=$CUSTOM_LINKS.LISTVIEWACTION}
	{assign var="customlink_href" value=$CUSTOMLINK->linkurl}
	{assign var="customlink_label" value=$CUSTOMLINK->linklabel}
	{* Ignore block:// type custom links which are handled earlier *}
	{if preg_match("/^block:\/\/.*/", $customlink_href)}
		{if $CUSTOMLINK->widget_header}
			<div>
				<b>{$customlink_label}</b>&nbsp;
				<img id="detailview_block_{$CUSTOMLINK->linkid}_indicator" style="display:none;" src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" />
			</div>
		{/if}
		{process_widget widgetLinkInfo=$CUSTOMLINK}
	{else}
		{if $customlink_label eq ''}
			{assign var="customlink_label" value=$customlink_href}
		{else}
			{* Pickup the translated label provided by the module *}
			{assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
		{/if}
		<br/>
		<input type="hidden" id="{$CUSTOMLINK->linklabel|replace:' ':''}LINKID" value="{$CUSTOMLINK->linkid}">
		<table style="border:0;width:100%" class="rightMailMerge" id="{$CUSTOMLINK->linklabel}">
			{if $CUSTOMLINK->widget_header}
				<tr>
					<td class="rightMailMergeHeader">
						<div>
						<b>{$customlink_label}</b>&nbsp;
						<img id="detailview_block_{$CUSTOMLINK->linkid}_indicator" style="display:none;" src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" />
						</div>
					</td>
				</tr>
			{/if}
			{if $CUSTOMLINK->widget_width neq ''}
				{assign var="widget_width" value="width:"|cat:$CUSTOMLINK->widget_width|cat:";"}
			{else}
				{assign var="widget_width" value=''}
			{/if}
			{if $CUSTOMLINK->widget_height neq ''}
				{assign var="widget_height" value="height:"|cat:$CUSTOMLINK->widget_height|cat:";"}
			{else}
				{assign var="widget_height" value=''}
			{/if}
			<tr style="height:25px">
				<td class="rightMailMergeContent"><div id="detailview_block_{$CUSTOMLINK->linkid}" style="{$widget_width} {$widget_height}"></div></td>
			</tr>
			<script type="text/javascript">
				vtlib_loadDetailViewWidget("{$customlink_href}", "detailview_block_{$CUSTOMLINK->linkid}", "detailview_block_{$CUSTOMLINK->linkid}_indicator");
			</script>
		</table>
	{/if}
{/foreach}
