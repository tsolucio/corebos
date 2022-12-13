{if empty($Module_Popup_Edit)}
	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="detailview_actionlinks actionlinks_events_todo" style="margin-top: 1.5%;">
		<tr>
			<td align="left" class="slds-text-heading_medium">
				<div class="slds-section slds-is-open">
					<h3 class="slds-section__title">
						<button aria-expanded="true" class="slds-button slds-section__title-action">
							<span class="slds-truncate">
								<strong>{$APP.LBL_ACTIONS}</strong>
							</span>
						</button>
					</h3>
				</div>
			</td>
		</tr>
		{if in_array($MODULE, getInventoryModules())}
		<!-- Inventory Actions -->
			{include file="Inventory/InventoryActions.tpl"}
		{/if}
	</table>
	{* vtlib customization: Avoid line break if custom links are present *}
	{if !isset($CUSTOM_LINKS) || empty($CUSTOM_LINKS)}
		<br>
	{/if}
	{* vtlib customization: Custom links on the Detail view basic links *}
	{if $CUSTOM_LINKS && $CUSTOM_LINKS.DETAILVIEWBASIC}
	<ul>
		{foreach item=CUSTOMLINK from=$CUSTOM_LINKS.DETAILVIEWBASIC}
		<li class="actionlink actionlink_customlink actionlink_{$CUSTOMLINK->linklabel|lower|replace:' ':'_'}">
		{assign var="customlink_href" value=$CUSTOMLINK->linkurl}
		{assign var="customlink_label" value=$CUSTOMLINK->linklabel}
		{assign var="customlink_success" value=$CUSTOMLINK->successmsg}
		{assign var="customlink_error" value=$CUSTOMLINK->errormsg}
		{if $customlink_label eq ''}
			{assign var="customlink_label" value=$customlink_href}
		{else}
			{* Pickup the translated label provided by the module *}
			{assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
		{/if}
		{if $customlink_href=='ACTIONSUBHEADER'}
		<span class="genHeaderSmall slds-truncate">
			{$customlink_label}
		</span>
		{else}
			{if $CUSTOMLINK->linkicon}
				{if strpos($CUSTOMLINK->linkicon, '}')>0}
					{assign var="customlink_iconinfo" value=$CUSTOMLINK->linkicon|json_decode:true}
					<span class="slds-icon_container slds-icon-{$customlink_iconinfo.library}-{$customlink_iconinfo.icon}" title="{$customlink_label}">
					<svg class="slds-icon slds-icon-text-default slds-icon_x-small" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/{$customlink_iconinfo.library}-sprite/svg/symbols.svg#{$customlink_iconinfo.icon}"></use>
					</svg>
					<span class="slds-assistive-text">{$customlink_label}</span>
					</span>
				{else}
					<a class="webMnu" href="{$customlink_href}" data-success="{$customlink_success}" data-error="{$customlink_error}" data-title="{$customlink_label}">
					<img hspace=5 align="absmiddle" border=0 src="{$CUSTOMLINK->linkicon}">
					</a>
				{/if}
			{else}
				<a class="webMnu" href="{$customlink_href}" data-success="{$customlink_success}" data-error="{$customlink_error}" data-title="{$customlink_label}"><img hspace=5 align="absmiddle" border=0 src="themes/images/no_icon.png"></a>
			{/if}
				&nbsp;<a class="slds-text-link_reset" href="{$customlink_href}" data-success="{$customlink_success}" data-error="{$customlink_error}" data-title="{$customlink_label}">{$customlink_label}</a>
			{/if}
		</li>
		{/foreach}
	</ul>
	{/if}
	{* vtlib customization: Custom links on the Detail view *}
	{if $CUSTOM_LINKS && $CUSTOM_LINKS.DETAILVIEW}
		<br>
		{if !empty($CUSTOM_LINKS.DETAILVIEW)}
		<table>
			<tr>
				<td class="dvtUnSelectedCell" style="background-color: rgb(204, 204, 204); padding: 5px;">
					<a href="javascript:;" onmouseover="fnvshobj(this,'vtlib_customLinksLay');" onclick="fnvshobj(this,'vtlib_customLinksLay');">
						<b>{$APP.LBL_MORE} {$APP.LBL_ACTIONS} &#187;</b>
					</a>
				</td>
			</tr>
		</table>
		<br>
		<div style="display: none; left: 193px; top: 106px;width:215px; position:absolute;" class="slds-box_border slds-card" id="vtlib_customLinksLay" onmouseout="fninvsh('vtlib_customLinksLay')" onmouseover="fnvshNrm('vtlib_customLinksLay')">
			<table class="slds-p-around_xx-small">
				<tr>
					<td style="border-bottom: 1px solid rgb(204, 204, 204); padding: 5px;">
						<b>{$APP.LBL_MORE} {$APP.LBL_ACTIONS} &#187;</b>
					</td>
				</tr>
				<tr>
					<td class="slds-p-around_xx-small">
					<ul>
						{foreach item=CUSTOMLINK from=$CUSTOM_LINKS.DETAILVIEW}
							{assign var="customlink_href" value=$CUSTOMLINK->linkurl}
							{assign var="customlink_label" value=$CUSTOMLINK->linklabel}
							{if $customlink_label eq ''}
								{assign var="customlink_label" value=$customlink_href}
							{else}
								{* Pickup the translated label provided by the module *}
								{assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
							{/if}
							<li>
							{if $CUSTOMLINK->linkicon}
								{if strpos($CUSTOMLINK->linkicon, '}')>0}
									{assign var="customlink_iconinfo" value=$CUSTOMLINK->linkicon|json_decode:true}
									<span class="slds-icon_container slds-icon-{$customlink_iconinfo.library}-{$customlink_iconinfo.icon}" title="{$customlink_label}">
									<svg class="slds-icon slds-icon-text-default slds-icon_x-small" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/{$customlink_iconinfo.library}-sprite/svg/symbols.svg#{$customlink_iconinfo.icon}"></use>
									</svg>
									<span class="slds-assistive-text">{$customlink_label}</span>
									</span>
								{else}
									<a class="webMnu" href="{$customlink_href}"><img hspace=5 align="absmiddle" border=0 src="{$CUSTOMLINK->linkicon}"></a>
								{/if}
							{else}
								<a class="webMnu" href="{$customlink_href}"><img hspace=5 align="absmiddle" border=0 src="themes/images/no_icon.png"></a>
							{/if}
							&nbsp;<a class="slds-text-link_reset" href="{$customlink_href}">{$customlink_label}</a>
							</li>
						{/foreach}
						</ul>
					</td>
				</tr>
			</table>
		</div>
		{/if}
	{/if}
	{* END *}
	<!-- Action links END -->
	{include file="TagCloudDisplay.tpl"}
	{if !empty($CUSTOM_LINKS.DETAILVIEWWIDGET)}
		{foreach key=CUSTOMLINK_NO item=CUSTOMLINK from=$CUSTOM_LINKS.DETAILVIEWWIDGET}
			{assign var="customlink_href" value=$CUSTOMLINK->linkurl}
			{assign var="customlink_label" value=$CUSTOMLINK->linklabel}
			{* Ignore block:// type custom links which are handled earlier *}
			{if !preg_match("/^block:\/\/.*/", $customlink_href) && !preg_match("/^top:\/\/.*/", $customlink_href)}
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
	{/if}
{/if}