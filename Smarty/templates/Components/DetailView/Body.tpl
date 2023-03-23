<table border=0 cellspacing=0 cellpadding=0 width=100% style="margin-top: 1%">
	<tr valign=top>
		<td style="padding:5px">
			<form action="index.php" method="post" name="DetailView" id="formDetailView">
			<input type="hidden" id="hdtxt_IsAdmin" value="{if isset($hdtxt_IsAdmin)}{$hdtxt_IsAdmin}{else}0{/if}">
			{include file='DetailViewHidden.tpl'}
			{foreach item=details key=idx from=$BLOCKS}
				{if $details.__type == 'block' || $details.__type == 'relatedlist'}
					{assign var=header value=$details.__header}
					{assign var=detail value=$details.__fields}
					{include file='Components/DetailView/DetailViewBlocks.tpl'}
				{else if $details.__type == 'widget'}
					{assign var=CUSTOM_LINK_DETAILVIEWWIDGET value=$details.__fields}
					{process_widget widgetLinkInfo=$CUSTOM_LINK_DETAILVIEWWIDGET}
				{/if}
			{/foreach}
			<table border=0 cellspacing=0 cellpadding=0 width=100%>
				<!-- Inventory - Product Details informations -->
				{if isset($ASSOCIATED_PRODUCTS) && $ShowInventoryLines}
				<tr><td>
					{$ASSOCIATED_PRODUCTS}
				</td></tr>
				{/if}
				{if $SinglePane_View eq 'true' && $IS_REL_LIST neq false && $IS_REL_LIST|@count > 0}
					{include file= 'RelatedListNew.tpl'}
				{/if}
			</table>
		</td>
	</tr>
</table>