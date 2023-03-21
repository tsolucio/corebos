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
<div class="slds-button-group" role="group">
{if !empty($MasterDetailLayoutMap.toolbar.massedit)}
<button type="button" class="slds-button slds-button_neutral" title="{$APP.LBL_MASS_EDIT}" onclick="masterdetailwork.MDMassEditRecords(this, '{$MasterDetailLayoutMap.targetmodule}', '{$MasterDetailLayoutMap.mapname}')" data-id="{$MasterDetailLayoutMap.mapname}_massedit" id="btn-{$MasterDetailLayoutMap.mapname}_massedit">
	{$APP.LBL_MASS_EDIT}
</button>
{/if}
{if !empty($MasterDetailLayoutMap.toolbar.actions)}
{foreach from=$MasterDetailLayoutMap.toolbar.actions item=$i}
	<button type="button" class="slds-button slds-button_neutral" title="{$i.label}" onclick="masterdetailwork.CallToAction(this, '{$i.workflow}')" data-id="{$MasterDetailLayoutMap.mapname}--actions" id="btn-{$MasterDetailLayoutMap.mapname}--actions">
		{$i.label}
	</button>
{/foreach}
{/if}
{if !empty($MasterDetailLayoutMap.toolbar.expandall)}
<button type="button" class="slds-button slds-button_neutral" title="{$APP.LBL_EXPAND_COLLAPSE}" onclick="masterdetailwork.MDToggle(this)" data-id="{$MasterDetailLayoutMap.mapname}" id="btn-{$MasterDetailLayoutMap.mapname}">
	{$APP.LBL_COLLAPSE}
</button>
{/if}
{if !empty($MasterDetailLayoutMap.toolbar.create)}
<button type="button" class="slds-button slds-button_neutral" title="{$APP.LBL_CREATE_BUTTON_LABEL}"
	onclick="masterdetailwork.MDUpsert('mdgrid{$MasterDetailLayoutMap.mapname}', '{$MasterDetailLayoutMap.targetmodule}', '', {$MasterDetaiCurrentRecord})">
	{$APP.LBL_CREATE_BUTTON_LABEL}
</button>
{/if}
</div>
</div>
</div>
</div>
</div>
</div>