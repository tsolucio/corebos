<script type="text/javascript">
window.addEventListener('DOMContentLoaded', () => {
	wizard.loader('hide');
});
window.addEventListener('onWizardModal', () => {
	wizard.loader('hide');
});
wizard.Suboperation[{$WizardStep}] = '{$WizardSuboperation}';
document.getElementById('codewithhbtnswitch').remove();
</script>
<div id="treeview-{$WizardStep}"></div>
<script type="text/javascript">
wizard.WizardInstance[`wzgrid{$WizardStep}`] = new tui.Grid({
	el: document.getElementById('treeview-{$WizardStep}'),
	treeColumnOptions: {
		name: 'parentaction',
		useIcon: false,
		useCascadingCheckbox: false
	},
	columns: [
		{
			header: 'Parent',
			name: 'parentaction',
		},
		{foreach from=$Columns item=rlfield name=mdhdr}
		{
			header: '{$rlfield.header}',
			name: '{$rlfield.name}',
			whiteSpace: 'normal',
		},
		{/foreach}
	],
	data: {
		api: {
			readData: {
				url: '',
				method: 'GET'
			}
		}
	},
	useClientSort: false,
	rowHeight: 60,
	bodyHeight: 'auto',
	scrollX: false,
	scrollY: false,
	columnOptions: {
		resizable: false
	},
	header: {
		align: 'left',
	},
	contextMenu: null
});
</script>