let fileurl = 'module=Reports&action=ReportsAjax&file=getReportInfos';
$(document).ready(function () {
	jQuery.ajax({
		method: 'GET',
		url: 'index.php?' + fileurl
	}).done(function (reports) {
		$response = $.parseJSON(reports);
		$reportsdata = $response.result;
		$htmloptions = '';
		if (reportName != '') {
			displayDivsection();
			$value = reportName.split('$$');
			$htmloptions += '<option value="'+reportName+'">'+$value[1]+'</option>';
		} else {
			$htmloptions += '<option value="">Select Report</option>';
		}
		$.each($reportsdata, function(index, data){
			$htmloptions += '<option value="'+data.reptid+'$$'+data.reptname+'">'+data.reptname+'</option>';
		});
		document.getElementById('report_name').innerHTML =$htmloptions;
	});

	let fileurl2 = 'module=cbQuestion&action=cbQuestionAjax&file=getcbQuestionsTypeFile';
	jQuery.ajax({
		method: 'GET',
		url: 'index.php?' + fileurl2
	}).done(function (questions) {
		$response2 = $.parseJSON(questions);
		$questionsdata = $response2.result;
		$htmloptions2 = '';
		if (questionName != '') {
			displayDivsection();
			$values = questionName.split('$$');
			$htmloptions2 += '<option value="'+questionName+'">'+$values[1]+'</option>';
		} else {
			$htmloptions2 += '<option value="">Select business question</option>';
		}
		$.each($questionsdata, function(index, data2){
			$htmloptions2 += '<option value="'+data2.questionid+'$$'+data2.qname+'">'+data2.qname+'</option>';
		});
		document.getElementById('question_name').innerHTML =$htmloptions2;
	});
});

function displayDivsection() {
	var caseType = document.getElementById("case_type");
	$caseValue = caseType.value;
	switch ($caseValue) {
		case 'report':
			document.getElementById("questionDiv").style.display = "none";
			document.getElementById("reportDiv").style.display = "";
			break;
		case 'question':
			document.getElementById("questionDiv").style.display = "";
			document.getElementById("reportDiv").style.display = "none";
			break;
		default:
			break;
	}
}