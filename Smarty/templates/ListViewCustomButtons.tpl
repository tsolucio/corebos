{* vtlib customization: Custom link buttons on the List view basic buttons *}
{if $CUSTOM_LINKS && $CUSTOM_LINKS.LISTVIEWBASIC}
	{foreach item=CUSTOMLINK from=$CUSTOM_LINKS.LISTVIEWBASIC}
		{assign var="customlink_href" value=$CUSTOMLINK->linkurl}
		{assign var="customlink_label" value=$CUSTOMLINK->linklabel}
		{assign var="customlink_id" value=$CUSTOMLINK->linklabel|replace:' ':''}
		{if $customlink_label eq ''}
			{assign var="customlink_label" value=$customlink_href}
		{else}
			{* Pickup the translated label provided by the module *}
			{assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
		{/if}
		<input class="crmbutton small edit" type="button" id="LISTVIEWBASIC_{$customlink_id}" value="{$customlink_label}" onclick="{$customlink_href}" />
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
		<tr>
			<td style="border-bottom: 1px solid rgb(204, 204, 204); padding: 5px;"><b>{$APP.LBL_MORE} {$APP.LBL_ACTIONS} &#187;</b></td>
		</tr>
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
