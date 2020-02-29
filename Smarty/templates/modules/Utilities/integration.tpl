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
		<div class="">
			<div class="slds-notify slds-notify_alert {$ERROR_CLASS} slds-theme_alert-texture" role="alert">
			<h2>
				<svg class="slds-icon slds-icon_small slds-m-right_x-small" aria-hidden="true">
				<use xlink:href="include/LD//assets/icons/utility-sprite/svg/symbols.svg#ban"></use>
				</svg>{$MESSAGE}
			</h2>
			</div>
		</div>
	</div>
</div>
</section>
