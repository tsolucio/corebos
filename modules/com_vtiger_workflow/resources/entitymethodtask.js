function entityMethodScript($) {

	function jsonget(operation, params, callback) {
		var obj = {
			module:'com_vtiger_workflow',
			action:'com_vtiger_workflowAjax',
			file:operation, ajax:'true'
		};
		$.each(params, function (key, value) {
			obj[key] = value;
		});
		$.get('index.php', obj, function (result) {
			var parsed = JSON.parse(result);
			callback(parsed);
		});
	}

	$(document).ready(function () {
		jsonget('entitymethodjson', {module_name:moduleName}, function (result) {
			$('#method_name_select_busyicon').hide();
			if (result.length==0) {
				$('#method_name_select').hide();
				$('#message_text').show();
			} else {
				$('#method_name_select').show();
				$('#message_text').hide();
				$.each(result, function (i, v) {
					var optionText = '<option value="'+v[0]+'" '+(v[0]==methodName?'selected':'')+'>'+v[1]+'</option>';
					$('#method_name_select').append(optionText);
				});
			}
		});
	});
}
