/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function wfListDataProvider(params, callback) {
	const xhr = new XMLHttpRequest();
	var url2call = url + '&page=' + (params.page+1);
	// `params.filters` format: [{ path: 'lastName', direction: 'asc' }, ...];
	var sendparams = '';
	if (params.filters.length) {
		sendparams += '&filters=' + encodeURIComponent(JSON.stringify(params.filters));
	}
	if (params.sortOrders) {
		sendparams += '&sorder=' + encodeURIComponent(JSON.stringify(params.sortOrders));
	}
	xhr.open('POST', url2call, true);
	xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=UTF-8');
	xhr.onload = function () {
		const response = JSON.parse(xhr.responseText);
		grid.size = parseInt(response.total, 10);
		callback(response.data);
	};
	xhr.send(sendparams);
}

function wfRemoveFromList(delWorkflowURL) {
	document.getElementById('confirm-prompt').style.display = 'block';
	document.getElementById('no_button').onclick = function () {
		document.getElementById('confirm-prompt').style.display = 'none';
	};
	document.getElementById('yes_button').onclick = function () {
		document.getElementById('confirm-prompt').style.display = 'none';
		window.location.href = delWorkflowURL;
	};
}

function wfExportList() {
	let url = 'index.php?module=com_vtiger_workflow&action=Export';
	if (grid.selectedItems.length) {
		var idstring = '';
		grid.selectedItems.forEach(function (item) {
			idstring += item.workflow_id+';';
		});
		url += '&export_data=selecteddata&search_type=includesearch&filters=&idstring='+idstring;
	} else if (grid._filters.length) {
		var filters = new Array();
		grid._filters.forEach(function (item) {
			filters.push({'path': item.path, 'value': item.value});
		});
		url += '&export_data=&search_type=includesearch&filters='+encodeURIComponent(JSON.stringify(filters));
	} else {
		url += '&export_data=&search_type=all';
	}
	gotourl(url);
}

function wfDeleteList() {
	wfDoWorkList(i18nWorkflowActions.WORKFLOW_DELETE_CONFIRMATION, i18nWorkflowActions.LBL_DELETE_WORKFLOW, 'deleteworkflow');
}

function wfActivateList() {
	let url = 'activatedeactivateWF&active=true';
	wfDoWorkList(i18nWorkflowActions.WORKFLOW_ACTIVATE_CONFIRMATION, i18nWorkflowActions.LBL_ACTIVATE_WORKFLOW, url);
}

function wfDeactivateList() {
	let url = 'activatedeactivateWF&active=false';
	wfDoWorkList(i18nWorkflowActions.WORKFLOW_DEACTIVATE_CONFIRMATION, i18nWorkflowActions.LBL_DEACTIVATE_WORKFLOW, url);
}

function wfDoWorkList(msg, title, action) {
	if (grid.selectedItems.length) {
		document.getElementById('prompt-message-wrapper').innerHTML = '<p>'+msg+'</p>';
		document.getElementById('modal-wfaction').innerHTML = title;
		document.getElementById('confirm-prompt').style.display = 'block';
		document.getElementById('no_button').onclick = function () {
			document.getElementById('confirm-prompt').style.display = 'none';
		};
		document.getElementById('yes_button').onclick = function () {
			document.getElementById('confirm-prompt').style.display = 'none';
			var params = `&${csrfMagicName}=${csrfMagicToken}`;
			var workURL = 'index.php?module=com_vtiger_workflow&action=com_vtiger_workflowAjax&wfajax=1&file='+action+'&workflow_id=';
			grid.selectedItems.forEach(function (item) {
				fetch(
					workURL+item.workflow_id,
					{
						method: 'post',
						headers: {
							'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
						},
						credentials: 'same-origin',
						body: params
					}
				);
			});
			window.location.reload();
		};
	} else {
		alert(alert_arr.SELECT);
		return false;
	}
}

var workflowCreationMode='from_module';
function wfCreateSubmit() {
	if (document.getElementById('module_list').value=='') {
		alert(alert_arr.SELECT);
		return false;
	}
	if (workflowCreationMode=='from_template' && document.getElementById('template_list').value=='') {
		alert(alert_arr.SELECT);
		return false;
	}
	VtigerJS_DialogBox.block();
	return true;
}

function workflowlistscript($) {

	function jsonget(operation, params, callback) {
		var obj = {
			module:'com_vtiger_workflow',
			action:'com_vtiger_workflowAjax',
			file:operation, ajax:'true'};
		$.each(params, function (key, value) {
			obj[key] = value;
		});
		$.get(
			'index.php',
			obj,
			function (result) {
				var parsed = JSON.parse(result);
				callback(parsed);
			}
		);
	}

	function center(el) {
		el.css({position: 'absolute'});
		el.width('400px');
		el.height('175px');
		placeAtCenter(el.get(0));
	}

	function NewWorkflowPopup() {
		function close() {
			$('#new_workflow_popup').css('display', 'none');
		}

		function show(module) {
			$('#new_workflow_popup').css('display', 'block');
		}

		return {
			close:close, show:show
		};
	}

	var templatesForModule = {};
	function updateTemplateList() {
		var moduleSelect = $('#module_list');
		var currentModule = moduleSelect.val();

		$('#template_list').hide();
		$('#template_list_foundnone').hide();
		$('#template_list_busyicon').show();

		function fillTemplateList(templates) {
			var templateSelect = $('#template_list');
			templateSelect.empty();

			$.each(templates, function (i, v) {
				templateSelect.append('<option value="'+v['id']+'">' + v['title']+'</option>');
			});
			if (templateSelect.children().length > 0) {
				templateSelect.show();
			} else {
				$('#template_list_foundnone').show();
			}
			$('#template_list_busyicon').hide();
		}
		if (templatesForModule[currentModule]==null) {
			jsonget(
				'templatesformodulejson',
				{module_name:currentModule},
				function (templates) {
					templatesForModule[currentModule] = templates;
					fillTemplateList(templatesForModule[currentModule]);
				}
			);
		} else {
			fillTemplateList(templatesForModule[currentModule]);
		}
	}

	$(document).ready(function () {
		var newWorkflowPopup = NewWorkflowPopup();
		$('#new_workflow').click(newWorkflowPopup.show);
		$('#pick_module').change(function () {
			VtigerJS_DialogBox.block();
			$('#filter_modules').submit();
		});

		$('#wffrommodule').click(function () {
			workflowCreationMode='from_module';
			$('#template_select_field').hide();
		});

		$('#wffromtpl').click(function () {
			workflowCreationMode='from_template';
			updateTemplateList();
			$('#template_select_field').show();
		});

		$('#module_list').change(function () {
			if (workflowCreationMode=='from_template') {
				updateTemplateList();
			}
		});

		var filterModule = $('#pick_module').val();
		if (filterModule!='All') {
			$('#module_list').val(filterModule);
			$('#module_list').change();
			$('#list_module').val(filterModule);
			$('#list_module').change();
		}

		$('#new_workflow_popup_save').click(function () {
			if (workflowCreationMode == 'from_template') {
				// No templates selected?
				if ($('#template_list').val() == '') {
					return false;
				}
			}
		});
	});
}
workflowlistscript(jQuery);
