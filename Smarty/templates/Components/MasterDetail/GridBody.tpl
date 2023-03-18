<div id="{$MasterDetailLayoutMap.mapname}" data-mapname="{$MasterDetailLayoutMap.mapnameraw}" style="display: inline"></div>
<script type="text/javascript" src="include/js/ListView.js"></script>
<script>
var MasterDetail_Pagination = 0;
if (MasterDetail_TargetField === undefined) {
	var MasterDetail_TargetField = Array();
}
masterdetailwork.MasterMapID['{$MasterDetailLayoutMap.mapname}'] = '{$MasterMapID}';
masterdetailwork.MasterButtons['{$MasterDetailLayoutMap.mapname}'] = '{$MasterButtons|json_encode}';
masterdetailwork.MasterHide['{$MasterDetailLayoutMap.mapname}'] = {$MasterDetailHide};
MasterDetail_TargetField['mdgrid{$MasterDetailLayoutMap.mapname}'] = '{$MasterTargetField}';
var pageOptions = false;
if ({$MasterDetail_Pagination} > 0) {
	pageOptions = {
		useClient: true,
		perPage: {$MasterDetail_Pagination}
	};
}
function loadMDGrid{$MasterDetailLayoutMap.mapname}() {
	MDInstance['mdgrid{$MasterDetailLayoutMap.mapname}'] = new tui.Grid({
		el: document.getElementById('{$MasterDetailLayoutMap.mapname}'), // Container element
		rowHeaders: ['checkbox'],
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
				whiteSpace: 'normal',
				renderer: {
					type: mdLinkRender
				},
				filter: {$mdfield.fieldinfo.uitype|getGridFilter:$mdfield.fieldinfo.uitype}
			},
			{/foreach}
			{if !empty($MasterDetailLayoutMap.listview.cbgridactioncol)}
			{$MasterDetailLayoutMap.listview.cbgridactioncol}
			{/if}
		],
		data: {
			api: {
				readData: {
					url: '{$MasterDetailLayoutMap.listview.datasource}&pid={$MasterID}',
					method: 'GET'
				}
			}
		},
		useClientSort: false,
		pageOptions: pageOptions,
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
	MDInstance['mdgrid{$MasterDetailLayoutMap.mapname}'].on('editingFinish', masterdetailwork.inlineedit);
	MDInstance['mdgrid{$MasterDetailLayoutMap.mapname}'].on('onGridMounted', masterdetailwork.GridMounted);
	MDInstance['mdgrid{$MasterDetailLayoutMap.mapname}'].on('check', masterdetailwork.checkUnCheckRows);
	MDInstance['mdgrid{$MasterDetailLayoutMap.mapname}'].on('uncheck', masterdetailwork.checkUnCheckRows);
	MDInstance['mdgrid{$MasterDetailLayoutMap.mapname}'].on('checkAll', masterdetailwork.checkUnCheckRows);
	MDInstance['mdgrid{$MasterDetailLayoutMap.mapname}'].on('uncheckAll', masterdetailwork.checkUnCheckRows);
}
loadMDGrid{$MasterDetailLayoutMap.mapname}();
</script>