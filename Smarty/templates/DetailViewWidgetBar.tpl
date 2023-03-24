{if !empty($CUSTOM_LINKS.DETAILVIEWWIDGET)}
	<div class="slds-grid slds-gutters" style="background: white; width: 99.5%;margin-left: 0.0%;">
	{foreach key=CUSTOMLINK_NO item=CUSTOMLINK from=$CUSTOM_LINKS.DETAILVIEWWIDGET}
		{assign var="customlink_href" value=$CUSTOMLINK->linkurl}
		{assign var="customlink_label" value=$CUSTOMLINK->linklabel}
		{* Ignore block:// type custom links which are handled earlier *}
		{if preg_match("/^top:\/\/.*/", $customlink_href)}
			{if $customlink_label eq ''}
				{assign var="customlink_label" value=$customlink_href}
			{else}
				{* Pickup the translated label provided by the module *}
				{assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
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
			<div id="detailview_top_{$CUSTOMLINK->linkid}" style="{$widget_width} {$widget_height}">
				<input type="hidden" id="{$CUSTOMLINK->linklabel|replace:' ':''}LINKID" value="{$CUSTOMLINK->linkid}">
				{process_widget widgetLinkInfo=$CUSTOMLINK}
			</div>
		{/if}
	{/foreach}
	</div>
{/if}