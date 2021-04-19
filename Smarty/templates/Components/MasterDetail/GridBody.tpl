<div id="{$MasterDetailLayoutMap.mapname}" data-mapname="{$MasterDetailLayoutMap.mapnameraw}" style="display: inline"></div>
<script>
var mdgrid{$MasterDetailLayoutMap.mapname} = new tui.Grid({
	el: document.getElementById('{$MasterDetailLayoutMap.mapname}'), // Container element
	columns: [
		{foreach from=$MasterDetailLayoutMap.listview.fields item=mdfield name=mdhdr}
		{
			header: '{$mdfield.fieldinfo.label}',
			name: '{$mdfield.fieldinfo.name}',
			sortable: {if $mdfield.sortable}true{else}false{/if},
			{if !empty($mdfield.sortingType)}
			sortingType: '{$mdfield.sortingType}',
			{/if}
			{if !empty($mdfield.editor)}
			editor: {$mdfield.editor},
			{/if}
			whiteSpace: 'normal'
		},
		{/foreach}
		{if !empty($MasterDetailLayoutMap.listview.cbgridactioncol)}
		{$MasterDetailLayoutMap.listview.cbgridactioncol}
		{/if}
	],
	data: {
		api: {
			readData: {
				url: '{$MasterDetailLayoutMap.listview.datasource}',
				method: 'GET'
			}
		}
	},
	useClientSort: true,
	rowHeight: 'auto',
	bodyHeight: 'auto',
	scrollX: false,
	scrollY: false,
	columnOptions: {
		resizable: true
	},
	header: {
		align: 'left',
	}
});

tui.Grid.applyTheme('striped');
</script>