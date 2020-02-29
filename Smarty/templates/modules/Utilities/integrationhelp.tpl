{include file='Buttons_List.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43" aria-modal="true">
<div class="slds-modal__container slds-p-around_none">
	<header class="slds-modal__header slds-grid slds-grid_align-spread slds-grid_vertical-align-center">
		<h2 id="header43" class="slds-text-heading_medium">
		<div class="slds-media__figure">
			<svg aria-hidden="true" class="slds-icon slds-icon-standard-user slds-m-right_small">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#sync"></use>
			</svg>
			{$TITLE_MESSAGE}
		</div>
		</h2>
	</header>
	<div class="slds-modal__content slds-app-launcher__content slds-p-around_medium">
		<ul class="slds-grid slds-grid_pull-padded slds-wrap">
		{foreach from=$integrations item=integration}
			<li class="slds-p-horizontal_small slds-size_1-of-1 slds-medium-size_1-of-3" onclick="gotourl('{$integration.url}');">
				<div class="slds-app-launcher__tile slds-text-link_reset">
				<div class="slds-app-launcher__tile-figure">
					<span class="slds-avatar slds-avatar_large">
					<abbr class="slds-avatar__initials slds-icon-custom-27" title="{$integration.title}">{$integration.abbr}</abbr>
					</span>
				</div>
				<div class="slds-app-launcher__tile-body">
					<a href="{$integration.url}">{$integration.title}</a>
					<p>{$integration.desc}</p>
				</div>
				</div>
			</li>
		{/foreach}
		</ul>
	</div>
</div>
</section>
