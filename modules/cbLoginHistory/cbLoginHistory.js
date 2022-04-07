/*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************/

var Report_ListView_PageSize = 20;
GlobalVariable_getVariable('Report_ListView_PageSize', 20, 'cbLoginHistory', '').then(function (response) {
	var obj = JSON.parse(response);
	Report_ListView_PageSize = obj.Report_ListView_PageSize;
});

var Grid = tui.Grid;
var gridInstance = {};
document.addEventListener('DOMContentLoaded', function (event) {
	loadJS('index.php?module=cbLoginHistory&action=cbLoginHistoryAjax&file=getjslanguage')
		.then(() => {
			gridInstance = new Grid({
				el: document.getElementById('lhgrid'),
				columns: [{
					name: 'User Name',
					header: mod_alert_arr.LBL_USER_NAME,
					sortingType: 'desc',
					sortable: true
				},
				{
					name: 'User IP',
					header: mod_alert_arr.LBL_USER_IP,
					whiteSpace: 'normal',
					sortingType: 'desc',
					sortable: true
				},
				{
					name: 'Signin Time',
					header: mod_alert_arr.LBL_SIGN_IN_TIME,
					whiteSpace: 'normal',
					sortingType: 'desc',
					sortable: true
				},
				{
					name: 'Signout Time',
					header: mod_alert_arr.LBL_SIGN_OUT_TIME,
					whiteSpace: 'normal',
					sortingType: 'desc',
					sortable: true
				},
				{
					name: 'Status',
					header: mod_alert_arr.LBL_STATUS,
					whiteSpace: 'normal',
					sortingType: 'desc',
					sortable: true
				}],
				data: {
					api: {
						readData: {
							url: 'index.php?module=cbLoginHistory&action=cbLoginHistoryAjax&file=getJSON',
							method: 'GET',
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
	gridInstance.setRequestParams({ 'user_list': document.getElementById('user_list').value });
	gridInstance.setPerPage(parseInt(Report_ListView_PageSize));
}