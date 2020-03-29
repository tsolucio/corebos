
function copysql() {
	showSQLMsg(alert_arr.Copied, 'success');
	document.getElementById('bqsql').select();
	document.execCommand('copy');
	window.getSelection().removeAllRanges();
	hideSQLMsg();
}

function changecbqModule(newmodule) {
	document.getElementById('save_conditions').innerHTML='';
	document.getElementById('bqsql').value='';
	document.getElementById('evalid_type').value = (newmodule=='Workflow' ? 'com_vtiger_workflow' : newmodule);
	moduleName = newmodule;
	conditions = null;
	builderconditions.changeModule();
}

function showSQLMsg(msg, role) {
	role = role || 'info';
	document.getElementById('sqlmsg').innerHTML=msg;
	let msgdiv = document.getElementById('sqlmsgdiv');
	msgdiv.classList.remove('slds-theme_info','slds-theme_error','slds-theme_success','slds-theme_warning');
	msgdiv.classList.add('slds-theme_'+role);
	document.getElementById('sqlmsgicon').childNodes[1].href.baseVal='include/LD/assets/icons/utility-sprite/svg/symbols.svg#'+role;
	msgdiv.classList.remove('bld-hidden');
}

function hideSQLMsg() {
	setTimeout(
		function () {
			document.getElementById('sqlmsgdiv').classList.add('bld-hidden');
		},
		2000
	);
}

function appendEmptyFieldRow() {
	fieldGridInstance.appendRows([{
		'fieldname': 'custom',
		'operators': 'custom',
		'alias': '',
		'sort': 'NONE',
		'instruction': ''
	}]);
}

const fieldGrid = tui.Grid;
const dataGrid = tui.Grid;
var fieldGridInstance;
document.addEventListener('DOMContentLoaded', function (event) {
	loadJS('index.php?module=cbQuestion&action=cbQuestionAjax&file=getjslanguage')
	.then(() => {
		fieldGridInstance = new fieldGrid({
			el: document.getElementById('fieldgrid'),
			columns: [
				{
					name: 'fieldname',
					header: mod_alert_arr.LBL_FIELD,
					formatter: 'listItemText',
					editor: {
						type: 'select',
						options: {
							listItems: arrayOfFields
						}
					},
					sortingType: 'desc',
					sortable: true
				},
				{
					name: 'operators',
					header: mod_alert_arr.LBL_OPERATION,
					formatter: 'listItemText',
					editor: {
						type: 'select',
						options: {
							listItems: validOperations
						}
					},
					whiteSpace: 'normal',
					sortable: false
				},
				{
					name: 'alias',
					header: mod_alert_arr.LBL_ALIAS,
					editor: 'text',
					whiteSpace: 'normal',
					sortable: false
				},
				{
					name: 'sort',
					header: mod_alert_arr.LBL_SORT,
					whiteSpace: 'normal',
					formatter: 'listItemText',
					editor: {
						type: 'select',
						options: {
							listItems: [
								{ text: mod_alert_arr.LBL_NONE, value: 'NONE' },
								{ text: mod_alert_arr.ASC, value: 'ASC' },
								{ text: mod_alert_arr.DESC, value: 'DESC' }
							]
						}
					},
					sortable: false
				},
				{
					name: 'instruction',
					header: mod_alert_arr.LBL_INSTRUCTION,
					editor: 'text',
					whiteSpace: 'normal',
					sortable: false
				}
			],
			data: fieldData,
			useClientSort: false,
			rowHeight: 'auto',
			bodyHeight: 350,
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
		// const pgcontainer = document.getElementById('tui-pagination-container');
		// const pginstance = new Pagination(pgcontainer, {
		// 	useClient: true,
		// 	perPage: 5
		// });
		// pginstance.getCurrentPage();
	});
});
