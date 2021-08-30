<div class="slds-grid" id='{$BOARD.id}lane' data-lane='{$BOARD.name}'>
{if isset($BOARD.image)}
	<div class="slds-media__figure">
		<span class="slds-icon_container slds-icon-{$BOARD.image.library}-{$BOARD.image.icon}" title="{$TITLE}">
		<svg class="slds-icon slds-icon_small" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/{$BOARD.image.library}-sprite/svg/symbols.svg#{$BOARD.image.icon}"></use>
		</svg>
		<span class="slds-assistive-text">{$TITLE}</span>
		</span>
	</div>
{/if}
	<div class="slds-media__body">
		<h2 class="slds-card__header-title">
			<span>{$TITLE}</span>
		</h2>
	</div>
</div>
