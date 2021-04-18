let fileurl = 'module=Reports&action=ReportsAjax&file=getReportInfos';
$(document).ready(function () {
	displayDivsection();
	jQuery.ajax({
		method: 'GET',
		url: 'index.php?' + fileurl
	}).done(function (reports) {
		$response = $.parseJSON(reports);
		$reportsdata = $response.result;
		$htmloptions = '';
		if (reportName != '') {
			$value = reportName.split('$$');
			$htmloptions += '<option value="'+reportName+'">'+$value[1]+'</option>';
		} else {
			$htmloptions += '<option value="">'+mod_alert_arr.selectReport+'</option>';
		}
		$.each($reportsdata, function (index, data) {
			$htmloptions += '<option value="'+data.reptid+'$$'+data.reptname+'">'+data.reptname+'</option>';
		});
		document.getElementById('report_name').innerHTML =$htmloptions;
	});
});

function displayDivsection() {
	var caseType = document.getElementById('case_type');
	switch (caseType.value) {
	case 'report':
		document.getElementById('questionDiv').style.display = 'none';
		document.getElementById('reportDiv').style.display = '';
		break;
	case 'question':
		document.getElementById('questionDiv').style.display = '';
		document.getElementById('reportDiv').style.display = 'none';
		break;
	default:
		break;
	}
}