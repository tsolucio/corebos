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
				url: '{$MasterDetailLayoutMap.listview.datasource}&pid={$MasterID}',
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
mdgrid{$MasterDetailLayoutMap.mapname}.on('editingFinish', function(ev) {
	let rowkey = ev.rowKey;
	let modulename = ev.instance.getValue(rowkey, 'record_module');
	let fieldName = ev.columnName;
	let fieldValue = ev.value;
	let recordid = ev.instance.getValue(rowkey, 'record_id') || '';
	let fileurl = 'module=Utilities&action=UtilitiesAjax&file=MasterDetailGridLayoutActions&mdaction=inline_edit&recordid='+recordid+'&rec_module='+modulename+'&fldName='+fieldName+'&fieldValue='+encodeURIComponent(fieldValue);
	if(recordid != '') {
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?' + fileurl
		}).done(function (response) {
			res = JSON.parse(response);
			if (res.success == true) {
				ev.instance.readData(1);
			} else {
				alert(alert_arr.Failed);
			}
		});
	}
});
</script>