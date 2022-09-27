<div id="grid-{$step}"></div>
<script type="text/javascript">
wizard.WizardRelModules[{$step}] = '{$relatedmodules|json_encode}';
wizard.WizardEntityNames[{$step}] = '{$entitynames|json_encode}';
wizard.WizardCurrentModule[{$step}] = '{$formodule}';
wizard.WizardColumns[{$step}] = '{$Columns|json_encode}';
wizard.WizardActions[{$step}] = '{$WizardActions|json_encode}';
wizard.WizardMode[{$step}] = '{$WizardMode}';
wizard.WizardFilterBy[{$step}] = {$WizardFilterBy|json_encode};
wizard.WizardValidate[{$step}] = {$WizardValidate};
wizard.WizardGoBack[{$step}] = {$WizardGoBack};
function WizardGrid{$formodule}{$step}() {
	wizard.WizardInstance['wzgrid{$step}'] = new tui.Grid({
		el: document.getElementById('grid-{$step}'),
		rowHeaders: ['checkbox'],
		columns: [
			{foreach from=$Columns item=i}
			{
				header: '{$i.header}',
				name: '{$i.name}',
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
					url: 'index.php?module=Utilities&action=UtilitiesAjax&file=WizardAPI&wizardaction=listview&formodule={$formodule}&step={$step}',
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
	wizard.WizardInstance['wzgrid{$step}'].on('checkAll', (ev) => {
		wizard.SaveRows('check', {$step}, ev);
	});
	wizard.WizardInstance['wzgrid{$step}'].on('check', (ev) => {
		wizard.SaveRows('check', {$step}, ev);
	});
	wizard.WizardInstance['wzgrid{$step}'].on('uncheckAll', (ev) => {
		wizard.SaveRows('uncheck', {$step}, ev);
	});
	wizard.WizardInstance['wzgrid{$step}'].on('uncheck', (ev) => {
		wizard.SaveRows('uncheck', {$step}, ev);
	});
	wizard.WizardInstance['wzgrid{$step}'].on('afterPageMove', (ev) => {
		wizard.CheckRows(ev);
	});
}
WizardGrid{$formodule}{$step}();	
</script>