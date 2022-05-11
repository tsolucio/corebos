<div id="cbQuestionGrid{$QuestionID}"></div>
<script type="text/javascript">
var Grid = tui.Grid;
var gridInstance = {};
var Report_ListView_PageSize = {$RowsperPage};
document.addEventListener('DOMContentLoaded', function (event) {
	gridInstance = new Grid({
		el: document.getElementById('cbQuestionGrid{$QuestionID}'),
		columns: {$Properties},
		data: {
			api: {
				readData: {
					url: 'index.php?module=cbQuestion&action=cbQuestionAjax&file=getJSON&qid={$QuestionID}&recordid={$RecordID}',
					method: 'GET'
				}
			}
		},
		useClientSort: false,
		pageOptions: {
			perPage: Report_ListView_PageSize
		},
		rowHeight: 'auto',
		bodyHeight: 500,
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
	gridInstance.setPerPage(parseInt(Report_ListView_PageSize));
}
</script>
