let fileurl = 'module=com_vtiger_workflow&action=com_vtiger_workflowAjax&file=getrelatedmods&reltype=*&currentmodule='+moduleName;
$(document).ready(function () {
	jQuery.ajax({
		method: 'GET',
		url: 'index.php?' + fileurl
	}).done(function (modlistres) {
		document.getElementById('relModlist').innerHTML = modlistres;
		Array.from(document.querySelector('#relModlist').options).forEach(function (option_element) {
			if (option_element.value == selectedModule) {
				option_element.selected = true;
			}
		});
	});
});

function filterWorkFlowBasedOnRelatedModule() {
	document.getElementById('workflowid_display').onclick = null;
	document.getElementById('workflowid_clear').onclick = null;

	var BasicSearch = '&query=true&search=true&searchtype=BasicSearch&search_field=module_name&search_text='+document.getElementById('relModlist').value;
	var SpecialSearch = encodeURI(BasicSearch);
	document.getElementById('workflowid_display').addEventListener('click', function () {
		window.open('index.php?module=com_vtiger_workflow&action=Popup&html=Popup_picker&form=new_task&forfield=workflowid&srcmodule=GlobalVariable&'+SpecialSearch, 'vtlibui10wf', cbPopupWindowSettings);
	}, false);

	document.getElementById('workflowid_clear').addEventListener('click', function () {
		window.open('index.php?module=com_vtiger_workflow&action=Popup&html=Popup_picker&form=new_task&forfield=workflowid&srcmodule=GlobalVariable&'+SpecialSearch, 'vtlibui10wf', cbPopupWindowSettings);
	}, false);
}