<div id="grid-{$WizardStep}" style="margin-top: -2%;"></div>
<script type="text/javascript">
wizard.WizardRelModules[{$WizardStep}] = '{$relatedmodules|json_encode}';
wizard.WizardEntityNames[{$WizardStep}] = '{$entitynames|json_encode}';
wizard.WizardCurrentModule[{$WizardStep}] = '{$formodule}';
wizard.WizardColumns[{$WizardStep}] = '{$Columns|json_encode}';
wizard.WizardActions[{$WizardStep}] = '{$WizardActions|json_encode}';
wizard.WizardMode[{$WizardStep}] = '{$WizardMode}';
wizard.WizardFilterBy[{$WizardStep}] = {$WizardFilterBy|json_encode};
wizard.WizardConditionQuery[{$WizardStep}] = '{$WizardConditionQuery}';
wizard.WizardValidate[{$WizardStep}] = {$WizardValidate};
wizard.WizardGoBack[{$WizardStep}] = {$WizardGoBack};
wizard.WizardRequiredAction[{$WizardStep}] = '{$WizardRequiredAction}';
wizard.WizardCustomFunction[{$WizardStep}] = '{$WizardCustomFunction}';
wizard.WizardSaveAction[{$WizardStep}] = '{$WizardSaveAction}';
wizard.WizardFilterFromContext[{$WizardStep}] = '{$WizardContext}';
wizard.ResetWizard[{$WizardStep}] = {$ResetWizard};
wizard.WizardConfirmStep[{$WizardStep}] = '{$WizardConfirmStep|json_encode}';
function WizardGrid{$formodule}{$WizardStep}() {
	if (wizard.WizardInstance['wzgrid{$WizardStep}'] !== undefined)  {
		wizard.WizardInstance['wzgrid{$WizardStep}'].destroy();
	}
	wizard.WizardInstance['wzgrid{$WizardStep}'] = new tui.Grid({
		el: document.getElementById('grid-{$WizardStep}'),
		rowHeaders: ['checkbox'],
		columns: [
			{foreach from=$Columns item=i}
			{
				header: '{$i.header}',
				name: '{$i.name}',
				filter: wizard.ApplyFilter[{$WizardStep}] == 1 ? {$i.uitype|getGridFilter:$i.uitype} : false,
				editor: {json_encode($WizardModuleEditor|gridGetEditor:$i.name:$i.uitype)}
			},
			{/foreach}
			{if !empty($WizardActions)}
			{
				header: 'Actions',
				name: 'wizardactions',
				width: 150,
				renderer: {
					type: WizardActions,
					options: {
						actions: {$WizardActions|json_encode}
					}
				}
			}
			{/if}
		],
		data: {
			api: {
				readData: {
					url: 'index.php?module=Utilities&action=UtilitiesAjax&file=WizardAPI&wizardaction=listview&formodule={$formodule}&step={$WizardStep}&mode='+wizard.WizardMode[{$WizardStep}]+'&query='+encodeURIComponent(wizard.WizardFilterBy[{$WizardStep}]),
					method: 'GET'
				}
			}
		},
		useClientSort: false,
		pageOptions: {
			perPage: 20
		},
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
	wizard.WizardInstance['wzgrid{$WizardStep}'].on('checkAll', (ev) => {
		wizard.SaveRows('check', {$WizardStep}, ev);
	});
	wizard.WizardInstance['wzgrid{$WizardStep}'].on('check', (ev) => {
		wizard.SaveRows('check', {$WizardStep}, ev);
	});
	wizard.WizardInstance['wzgrid{$WizardStep}'].on('uncheckAll', (ev) => {
		wizard.SaveRows('uncheck', {$WizardStep}, ev);
	});
	wizard.WizardInstance['wzgrid{$WizardStep}'].on('uncheck', (ev) => {
		wizard.SaveRows('uncheck', {$WizardStep}, ev);
	});
	wizard.WizardInstance['wzgrid{$WizardStep}'].on('afterPageMove', (ev) => {
		wizard.CheckRows(ev);
	});
	wizard.WizardInstance['wzgrid{$WizardStep}'].on('filter', wizard.FilterGrid);
	wizard.WizardInstance['wzgrid{$WizardStep}'].on('editingFinish', wizard.InlineEdit);
}
if (wizard.isModal && {$WizardStep} > 0 && wizard.ProceedToNextStep) {
	WizardGrid{$formodule}{$WizardStep}();
} else {
	WizardGrid{$formodule}{$WizardStep}();
}
window.addEventListener('DOMContentLoaded', () => {
	wizard.loader('hide');
});
window.addEventListener('onWizardModal', () => {
	wizard.loader('hide');
});
</script>