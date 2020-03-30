
function copysql() {
	showSQLMsg(alert_arr.Copied, 'success');
	let cb = document.getElementById('checkboxsqlwsq');
	if (cb.checked) {
		document.getElementById('bqsql').select();
	} else {
		document.getElementById('bqwsq').select();
	}
	document.execCommand('copy');
	window.getSelection().removeAllRanges();
	hideSQLMsg();
}

function testsql(qid) {
}

function toggleSQLView() {
	let cb = document.getElementById('checkboxsqlwsq');
	if (cb.checked) {
		document.getElementById('bqsql').style.display = 'flex';
		document.getElementById('bqwsq').style.display = 'none';
		cb.value = 1;
	} else {
		document.getElementById('bqsql').style.display = 'none';
		document.getElementById('bqwsq').style.display = 'flex';
		cb.value = 0;
	}
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
	document.getElementById('cbqmsgdiv').classList.remove('bldcontainer-hidden');
	document.getElementById('cbqmsgdiv').classList.add('bldcontainer-visible');
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
			document.getElementById('cbqmsgdiv').classList.add('bldcontainer-hidden');
		},
		4000
	);
}

function appendEmptyFieldRow() {
	let emptyRow = {
		'fieldname': 'custom',
		'operators': 'custom',
		'alias': '',
		'sort': 'NONE',
		'group': '0',
		'instruction': ''
	};
	fieldGridInstance.appendRows([emptyRow]);
	fieldData.push(emptyRow);
}

function getInstruction(field, operator, alias) {
	let fins = '';
	let fnam = 'expression';
	if (field!='custom') {
		fnam = field;
	}
	if (operator!='custom') {
		let op = validOperations.find(x => x.value === operator);
		switch (operator) {
			case 'ifelse':
				fins = 'if '+fnam+' then expression else expression end';
				break;
			case 'add':
			case 'sub':
			case 'mul':
			case 'div':
			case 'distinct':
			case 'ltequals':
			case 'gtequals':
			case 'lt':
			case 'gt':
				fins = fnam + ' ' + op.text;
				break;
			case 'power':
			case 'round':
			case 'ceil':
			case 'floor':
			case 'modulo':
			case 'concat':
			case 'stringposition':
			case 'stringlength':
			case 'stringreplace':
			case 'substring':
			case 'uppercase':
			case 'lowercase':
			case 'time_diff(a)':
			case 'time_diff(a,b)':
			case 'time_diffdays(a)':
			case 'time_diffdays(a,b)':
			case 'time_diffyears(a)':
			case 'time_diffyears(a,b)':
			case 'add_days':
			case 'sub_days':
			case 'add_months':
			case 'sub_months':
			case 'add_time':
			case 'sub_time':
			case 'sum':
			case 'min':
			case 'max':
			case 'avg':
			case 'aggregation':
			case 'aggregation_fields':
			case 'aggregate_time':
			case 'isString':
			case 'isNumeric':
			case 'coalesce':
			case 'hash':
			case 'setype':
			case 'number_format':
				if (op.text.indexOf(',')!=-1) {
					fins = op.text.replace(/\(.+?,/, '('+fnam+',');
				} else {
					fins = op.text.replace(/\(.+\)/, '('+fnam+')');
				}
				break;
			case 'today':
			case 'tomorrow':
			case 'yesterday':
			case 'count':
				fins = op.text;
				break;
			default:
				break;
		}
	} else {
		fins = fnam;
	}
	if (alias!='') {
		fins += ' as '+alias;
	}
	return fins;
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
					sortable: true,
					onAfterChange(ev) {
						fieldData[ev.rowKey].instruction = getInstruction(ev.value, fieldData[ev.rowKey].operators, fieldData[ev.rowKey].alias);
						fieldData[ev.rowKey].fieldname = ev.value;
						fieldGridInstance.resetData(fieldData);
					}
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
					sortable: false,
					onAfterChange(ev) {
						fieldData[ev.rowKey].instruction = getInstruction(fieldData[ev.rowKey].fieldname, ev.value, fieldData[ev.rowKey].alias);
						fieldData[ev.rowKey].operators = ev.value;
						fieldGridInstance.resetData(fieldData);
					}
				},
				{
					name: 'alias',
					header: mod_alert_arr.LBL_ALIAS,
					editor: 'text',
					whiteSpace: 'normal',
					width: 250,
					sortable: false,
					onAfterChange(ev) {
						fieldData[ev.rowKey].instruction = getInstruction(fieldData[ev.rowKey].fieldname, fieldData[ev.rowKey].operators, ev.value);
						fieldData[ev.rowKey].alias = ev.value;
						fieldGridInstance.resetData(fieldData);
					}
				},
				{
					name: 'sort',
					header: mod_alert_arr.LBL_SORT,
					whiteSpace: 'normal',
					width: 150,
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
					name: 'group',
					header: mod_alert_arr.LBL_GROUP,
					whiteSpace: 'normal',
					width: 150,
					formatter: 'listItemText',
					editor: {
						type: 'radio',
						options: {
							listItems:[
							{ text: alert_arr.NO, value: '0' },
							{ text: alert_arr.YES, value: '1' }
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
	});
});
