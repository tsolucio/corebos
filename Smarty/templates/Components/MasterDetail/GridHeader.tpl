<div class="slds-page-header">
<div class="slds-page-header__row">
<div class="slds-page-header__col-title">
<div class="slds-media">
{if !empty($MasterDetailLayoutMap.toolbar.icon)}
<div class="slds-media__figure">
<span class="slds-icon_container slds-icon-standard-opportunity">
<svg class="slds-icon slds-page-header__icon" aria-hidden="true">
<use xlink:href="include/LD/assets/icons/{$MasterDetailLayoutMap.toolbar.icon}"></use>
</svg>
</span>
</div>
{/if}
<div class="slds-media__body">
<div class="slds-page-header__name">
<div class="slds-page-header__name-title">
<h1>
<span class="slds-page-header__title slds-truncate" title="Recently Viewed">{$MasterDetailLayoutMap.toolbar.title}</span>
</h1>
</div>
</div>
</div>
</div>
</div>
<div class="slds-page-header__col-actions">
<div class="slds-page-header__controls">
<div class="slds-page-header__control">
<ul class="slds-button-group-list">
{if !empty($MasterDetailLayoutMap.toolbar.expandall)}
<li>
<div>
<button class="slds-button slds-button_icon slds-button_icon-border-filled" title="{$APP.LBL_EXPAND_COLLAPSE}">
<svg class="slds-button__icon" aria-hidden="true">
<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#expand_all"></use>
</svg>
<span class="slds-assistive-text">{$APP.LBL_EXPAND_COLLAPSE}</span>
</button>
</div>
</li>
{/if}
{if !empty($MasterDetailLayoutMap.toolbar.create)}
<li>
<div>
<button type="button" class="slds-button slds-button_icon slds-button_icon-border-filled" title="{$APP.LBL_CREATE_BUTTON_LABEL}"
	onclick="masterdetailwork.MDUpsert('mdgrid{$MasterDetailLayoutMap.mapname}', '{$MasterDetailLayoutMap.targetmodule}', '', {$MasterDetaiCurrentRecord})">
<svg class="slds-button__icon" aria-hidden="true">
<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use>
</svg>
<span class="slds-assistive-text">{$APP.LBL_CREATE_BUTTON_LABEL}</span>
</button>
</div>
</li>
{/if}
</ul>
</div>
</div>
</div>
</div>
</div>