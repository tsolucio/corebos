<article class="slds-card">
	<div class="slds-card__header slds-grid">
	<header class="slds-has-flexi-truncate">
		<div class="slds-page-header">
			<div class="slds-page-header__row">
				<div class="slds-page-header__col-title">
					<div class="slds-media">
						<div class="slds-media__body">
							<div class="slds-page-header__name">
							<div class="slds-page-header__name-title">
								<h1>
								<span class="slds-page-header__title slds-truncate">
									{if $title neq ''}
										{$title}
									{/if}
								</span>
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
							{if !empty($WizardArray[$currentModule])}
							<button type="button" class="slds-button slds-button_neutral" onclick="relatedlistgrid.Wizard('{$functionName}', {$CurrentRecord}, {$WizardArray[$currentModule]}, '{$currentModule}')">
								Wizard
							</button>
							{/if}
							<button type="button" class="slds-button slds-button_neutral" onclick="relatedlistgrid.upsert('{$functionName}', '{$MainModule}', '', {$CurrentRecord}, '{$MainRelateField}')">
								{$APP.LBL_CREATE_BUTTON_LABEL} {$MainModule}
							</button>
						</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>
	</div>
	<div class="slds-card__body slds-card__body_inner">
	<div id="{$functionName}" style="display: inline;"></div>
	</div>
</article>
<script>
relatedlistgrid.RLInstanceInfo['{$functionName}']  = '{$RLInstance}';
relatedlistgrid.FieldLabels['{$functionName}']  = '{$FieldLabels}';
relatedlistgrid.RelatedFields['{$functionName}']  = '{$RelatedFields}';
relatedlistgrid.Tooltips['{$functionName}']  = '{$Tooltips}';
relatedlistgrid.MapName['{$functionName}'] = '{$mapname}';
relatedlistgrid.NextStep['{$functionName}'] = '{$NextStep}';
relatedlistgrid.Wizard['{$functionName}'] = '{$Wizard}';
relatedlistgrid.WizardWorkflows['{$functionName}'] = '{$WizardWorkflows}';
function loadRLGrid{$functionName}() {
	RLInstance['{$functionName}'] = new tui.Grid({
		el: document.getElementById('{$functionName}'),
		treeColumnOptions: {
			name: 'parentaction',
			useIcon: false,
			useCascadingCheckbox: false
		},
		columns: [
			{
				header: 'Parent',
				name: 'parentaction',
				renderer: {
					type: RLinkRender
				},
			},
			{foreach from=$Columns item=rlfield name=mdhdr}
			{
				header: '{$rlfield.label}',
				name: '{$rlfield.name}',
				whiteSpace: 'normal',
				renderer: {
					type: RLinkRender
				},
				editor: {json_encode($LastModule|gridGetEditor:$rlfield.name:$rlfield.uitype)}
			},
			{/foreach}
			{if !empty($cbgridactioncol)}
				{$cbgridactioncol}
			{/if}
		],
		data: {
			api: {
				readData: {
					url: 'index.php?module=Utilities&action=UtilitiesAjax&file=RelatedListWidgetActions&rlaction=list&pid={$ID}&mapname={$mapname}&currentmodule='+gVTModule,
					method: 'GET'
				}
			}
		},
		useClientSort: false,
		rowHeight: 'auto',
		bodyHeight: 'auto',
		scrollX: false,
		scrollY: false,
		columnOptions: {
			resizable: true
		},
		header: {
			align: 'left',
		},
		contextMenu: null
	});

	tui.Grid.applyTheme('striped');
	RLInstance['{$functionName}'].on('editingFinish', relatedlistgrid.inlineedit);
}
loadRLGrid{$functionName}();
</script>