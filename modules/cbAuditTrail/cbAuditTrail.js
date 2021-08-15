/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
var Report_ListView_PageSize = 40;
GlobalVariable_getVariable('Report_ListView_PageSize', 40, 'cbAuditTrail', '').then(function (response) {
	var obj = JSON.parse(response);
	Report_ListView_PageSize = obj.Report_ListView_PageSize;
});

function auditenable() {
	setAuditStatus('enabled');
}

function auditdisable() {
	setAuditStatus('disabled');
}

function setAuditStatus(status) {
	document.getElementById('status').style.display = 'block';
	jQuery.ajax({
		method:'POST',
		url:'index.php?module=cbAuditTrail&action=cbAuditTrailAjax&file=SaveAuditTrail&ajax=true&audit_trail=' + status,
	}).done(function (response) {
		document.getElementById('status').style.display = 'none';
		location.reload();
	});
}

var Grid = tui.Grid;
var gridInstance = {};
document.addEventListener('DOMContentLoaded', function (event) {
	loadJS('index.php?module=cbAuditTrail&action=cbAuditTrailAjax&file=getjslanguage')
	.then(() => {
		gridInstance = new Grid({
			el: document.getElementById('atgrid'),
			columns: [
				{
					name: 'User Name',
					header: mod_alert_arr.LBL_USER_NAME,
					sortingType: 'desc',
					sortable: true
				},
				{
					name: 'Module',
					header: alert_arr.Module,
					whiteSpace: 'normal',
					sortingType: 'desc',
					sortable: true
				},
				{
					name: 'Action',
					header: mod_alert_arr.LBL_ACTION,
					whiteSpace: 'normal',
					sortingType: 'desc',
					sortable: true
				},
				{
					name: 'Record',
					header: mod_alert_arr.LBL_RECORD_ID,
					whiteSpace: 'normal',
					sortingType: 'desc',
					sortable: true
				},
				{
					name: 'Action Date',
					header: mod_alert_arr.LBL_ACTION_DATE,
					whiteSpace: 'normal',
					sortingType: 'desc',
					sortable: true
				}
			],
			data: {
				api: {
					readData: {
						url: 'index.php?module=cbAuditTrail&action=cbAuditTrailAjax&file=getJSON',
						method: 'GET',
						// serializer(params) {
						// 	params.user_list = document.getElementById('user_list').value;
						// 	params.action_search = document.getElementById('action_search').value;
						// 	return Object.keys(params).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(params[key])).join('&')
						// }
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
});

function reloadgriddata() {
	gridInstance.setRequestParams({'user_list': document.getElementById('user_list').value, 'action_search': document.getElementById('action_search').value});
	gridInstance.setPerPage(parseInt(Report_ListView_PageSize));
}