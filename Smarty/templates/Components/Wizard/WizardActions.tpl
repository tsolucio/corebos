<div class="slds-page-header">
	<div class="slds-page-header__row">
		<div class="slds-page-header__col-title">
			<div class="slds-media">
				<div class="slds-media__figure">
					<span class="slds-icon_container slds-icon-standard-{$WizardIcon[$smarty.foreach.stepwizard.index]}">
						<svg class="slds-icon slds-page-header__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#{$WizardIcon[$smarty.foreach.stepwizard.index]}"></use>
						</svg>
					</span>
				</div>
				<div class="slds-media__body">
					<div class="slds-page-header__name">
						<div class="slds-page-header__name-title">
							<h1>
								<span>{$APP.LBL_WIZARD}</span>
								<span class="slds-page-header__title slds-truncate">{$step.title}</span>
							</h1>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="slds-page-header__row">
		<div class="slds-page-header__col-meta">
			{if $step.description neq ''}
			<div class="slds-text-heading_small">
				<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info"></use>
				</svg>
				{$step.description}
			</div>
			{/if}
		</div>
		<div class="slds-page-header__col-controls">
			<div class="slds-page-header__controls">
			{if $step.filter}
			<div class="slds-page-header__control">
				<button type="button" onclick="wizard.ClearFilter({$smarty.foreach.stepwizard.index})" class="slds-button slds-button_icon slds-button_icon-border-filled" title="{'LBL_CLEAR'|@getTranslatedString}">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#filterList"></use>
					</svg>
					<span class="slds-assistive-text">{'LBL_CLEAR'|@getTranslatedString}</span>
				</button>
			</div>
			{/if}
			{if $WizardInfo[{$smarty.foreach.stepwizard.index}]}
			<div class="slds-button-group" role="group">
				<div class="slds-dropdown-trigger slds-dropdown-trigger_hover slds-is-open slds-button_last">
					<button class="slds-button slds-button_icon slds-button_icon-border-filled slds-is-selected" aria-haspopup="true" aria-expanded="true">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info"></use>
						</svg>
						<span class="slds-assistive-text"></span>
					</button>
					<div class="slds-dropdown slds-dropdown_table slds-dropdown_right" style="max-width: 100rem;max-height: 300px;overflow: auto">
						<table class="slds-table slds-table_cell-buffer slds-table_bordered">
							<thead>
								<tr class="slds-text-title_caps" id="wizard-columns-info-{$smarty.foreach.stepwizard.index}">
								</tr>
							</thead>
							<tbody id="wizard-info-{$smarty.foreach.stepwizard.index}">
							</tbody>
						</table>
					</div>
				</div>
			</div>
			{/if}
			{if in_array('delete', $step.actions)}
			<div class="slds-page-header__control">
				<button type="button" onclick="wizard.DeleteRowFromGrid({$smarty.foreach.stepwizard.index})" class="slds-button slds-button_icon slds-button_icon-border-filled" title="{'LNK_REMOVE'|@getTranslatedString}">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
					</svg>
					<span class="slds-assistive-text">{'LNK_REMOVE'|@getTranslatedString}</span>
				</button>
			</div>
			{/if}
			</div>
		</div>
	</div>
</div>