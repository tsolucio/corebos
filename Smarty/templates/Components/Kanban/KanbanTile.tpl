<article class="slds-tile">
	<div class="slds-grid slds-gutters">
		<h3 class="slds-tile__title slds-truncate slds-col slds-size_3-of-4" title="{$Tile.title}">
			<strong>{$Tile.title}</strong>
		</h3>
		<span class="small slds-col slds-size_1-of-4">
			<span style="float:right;">
			{assign var='MENULABEL' value='LBL_ACTIONS'}
			{assign var='MENUIMAGE' value=['library'=>'utility', 'icon'=>'settings']}
			{assign var='MENUBUTTONS' value=$KBMENU_LINKS}
			{include file="Components/DropdownButtons.tpl"}
			</span>
		</span>
	</div>
	<div class="slds-tile__detail">
		<dl class="slds-list_horizontal slds-wrap">
		{foreach from=$Tile.showfields item=finfo key=fkey}
			<dt class="slds-item_label slds-text-color_weak slds-truncate" title="{$finfo.label}">{$finfo.label}:</dt>
			<dd class="slds-item_detail slds-truncate" title="{$finfo.value}">{$finfo.value}</dd>
		{/foreach}
			<dt class="slds-item_label slds-text-color_weak slds-truncate" title="{$APP.LBL_MORE}">{$APP.LBL_MORE}:</dt>
			<dd class="slds-item_detail slds-truncate">
			{if !empty($Tile.morefields)}
				{include file="Components/Kanban/KanbanMoreFields.tpl"}
			{/if}
			</dd>
		</dl>
	</div>
</article>