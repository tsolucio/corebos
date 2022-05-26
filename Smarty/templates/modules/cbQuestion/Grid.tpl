<div id="cbQuestionGrid{$QuestionID}"></div>
<script type="text/javascript">
var Grid = tui.Grid;
var gridInstance = {};
var MasterDetail_Pagination = {$RowsperPage};
document.addEventListener('DOMContentLoaded', function (event) {
	gridInstance = new Grid({
		el: document.getElementById('cbQuestionGrid{$QuestionID}'),
		columns: {$Properties},
		data: {
			api: {
				readData: {
					url: 'index.php?module=cbQuestion&action=cbQuestionAjax&file=getJSON&qid={$QuestionID}&contextid={$RecordID}',
					method: 'GET'
				}
			}
		},
		useClientSort: false,
		pageOptions: {
			perPage: MasterDetail_Pagination
		},
		rowHeight: 'auto',
		bodyHeight: 'auto',
		scrollX: false,
		scrollY: true,
		columnOptions: {
			resizable: true
		},
		header: {
			align: 'left',
			valign: 'top'
		}
	});
	tui.Grid.applyTheme('striped');
});

function reloadgriddata() {
	gridInstance.setPerPage(parseInt(MasterDetail_Pagination));
}
</script>
