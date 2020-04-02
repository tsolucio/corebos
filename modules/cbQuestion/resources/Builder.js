var Report_ListView_PageSize = 40;
GlobalVariable_getVariable('Report_ListView_PageSize', 40, 'cbQuestion', '').then(function (response) {
	var obj = JSON.parse(response);
	Report_ListView_PageSize = obj.Report_ListView_PageSize;
});

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

function testBuilderSQL() {
	builderconditions.cbaccess.doPost(
		{
			'operation': 'showqueryfromwsdoquery',
			'sessionName': builderconditions.cbaccess.sessionId,
			'query': document.getElementById('bqwsq').value
		},
		function (resp) {
			if (resp.success) {
				if (resp.result.status=='OK') {
					showSQLMsg(resp.result.msg, 'success');
				} else {
					showSQLMsg(resp.result.msg, 'error');
				}
				document.getElementById('bqsql').value = resp.result.sql;
			} else {
				showSQLMsg(resp.error.message, 'error');
			}
			hideSQLMsg();
		}
	);
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

function getQuestionResults() {
	const qtype = document.getElementById('qtype').value;
	const qsqlqry = (document.getElementById('sqlquery').checked ? '1' : '0');
	let cbq = JSON.stringify({
		'qname': document.getElementById('bqname').value,
		'qtype': qtype,
		'qmodule': document.getElementById('bqmodule').value,
		'qpagesize': document.getElementById('qpagesize').value,
		'qcolumns': (qsqlqry=='1' ? document.getElementById('bqsql').value : (qtype=='Mermaid' ? document.getElementById('bqwsq').value : getSQLSelect())),
		'qcondition': (qtype=='Mermaid' ? '' : getSQLConditions()),
		'orderby': getSQLOrderBy().substr(9),
		'groupby': getSQLGroupBy().substr(9),
		'typeprops': document.getElementById('qprops').value,
		'sqlquery': qsqlqry,
		'condfilterformat': '0',
	});
	fetch(
		'index.php?module=cbQuestion&action=cbQuestionAjax&actionname=qactions&method=getBuilderAnswer',
		{
			method: 'post',
			headers: {
				'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			credentials: 'same-origin',
			body: '&'+csrfMagicName+'='+csrfMagicToken+'&cbQuestionRecord='+encodeURIComponent(cbq)
		}
	).then(response => response.text()).then(response => {
		let cbqa = document.getElementById('cqanswer');
		cbqa.innerHTML = response;
		vtlib_executeJavascriptInElement(cbqa);
	});
	if (qtype=='Mermaid') {
		dataGridInstance.clear();
	} else {
		dataGridInstance.setColumns(getDataColumns());
		dataGridInstance.setRequestParams({'cbQuestionRecord': encodeURIComponent(cbq)});
		dataGridInstance.reloadData();
	}
}

function getDataColumns() {
	let slflds = [];
	fieldData.map(finfo => {
		if (finfo.instruction != '') {
			/* complex conditioning
				module_field no_operator no_alias > fieldname
				module_field operator no_alias > fieldname
				module_field no_operator alias > fieldname
				module_field operator alias > alias
				related_module_field no_operator no_alias > related fieldname
				related_module_field operator no_alias > we have to create an alias
				related_module_field no_operator alias > fieldname
				related_module_field operator alias > alias
			*/
			let fnam = finfo.fieldname;
			let fhdr = finfo.fieldname;
			if (typeof fieldNEcolumn[document.getElementById('bqmodule').value+fnam] != 'undefined') {
				fnam = fieldNEcolumn[document.getElementById('bqmodule').value+fnam]
			}
			if (finfo.fieldname.indexOf(': (')==-1) {
				if (finfo.operators!='custom' && finfo.alias!='') {
					fnam = finfo.alias;
				}
			} else {
				if (finfo.operators=='custom') {
					fnam = fnam.substr(fnam.indexOf(': (')+3).replace(') ', '').toLowerCase();
				} else {
					if (finfo.alias=='') {
						fnam = finfo.fieldname.replace(' : (', '').replace(') ', '').replace(')', '').toLowerCase();
					} else {
						fnam = finfo.alias;
					}
				}
			}
			(finfo.operators=='custom' ? (finfo.fieldname.indexOf(': (')!=-1 ? fnam : finfo.fieldname) : finfo.fieldname),
			slflds.push({
				'name': fnam,
				'header': (finfo.alias=='' ? (finfo.fieldname.indexOf(': (')!=-1 ? fhdr : finfo.fieldname) : finfo.alias),
				'whiteSpace': 'normal',
				'sortable': false
			});
		}
	});
	return slflds;
}

function toggleBlock(block) {
	let bk = document.getElementById(block);
	if (bk.style.display=='' || bk.style.display=='block') {
		bk.style.display='none';
	} else {
		bk.style.display='block';
	}
}

function changecbqModule(newmodule) {
	document.getElementById('save_conditions').innerHTML='';
	document.getElementById('bqwsq').value='';
	document.getElementById('bqsql').value='';
	document.getElementById('evalid_type').value = (newmodule=='Workflow' ? 'com_vtiger_workflow' : newmodule);
	moduleName = newmodule;
	conditions = null;
	fieldData = [];
	fieldData.push(getEmptyFieldRow());
	this.addEventListener(
		'condition_builder_module_changed',
		function (e) {
			fieldGridInstance.clear();
			const meta = builderconditions.getMetaInformation();
			const customElem = arrayOfFields[0];
			arrayOfFields = [];
			arrayOfFields.push(customElem);
			for (const op in meta.fieldLabels) {
				arrayOfFields.push({
					'text': meta.fieldLabels[op],
					'value': op
				});
			}
			fieldGridColumns[0].editor.options.listItems = arrayOfFields;
			fieldGridInstance.setColumns(fieldGridColumns);
			fieldGridInstance.resetData(fieldData);
		},
		false
	);
	builderconditions.changeModule();
	fetch(
		'index.php?module=cbMap&action=cbMapAjax&actionname=mapactions&method=getFieldTablesForModule',
		{
			method: 'post',
			headers: {
				'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			credentials: 'same-origin',
			body: '&'+csrfMagicName+'='+csrfMagicToken+'&fieldsmodule='+newmodule
		}
	).then(response => response.json()).then(response => {
		fieldTableRelation = response;
	});
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

function getSQLSelect() {
	let slflds = [];
	fieldData.map(finfo => {
		if (finfo.instruction != '') {
			/* complex conditioning
				module_field no_operator no_alias > fieldname
				module_field operator no_alias > fieldname
				module_field no_operator alias > fieldname
				module_field operator alias > alias
				related_module_field no_operator no_alias > fieldname
				related_module_field operator no_alias > we have to create an alias
				related_module_field no_operator alias > fieldname
				related_module_field operator alias > alias
			*/
			let fnam = finfo.fieldname;
			if (finfo.fieldname.indexOf(': (')==-1) {
				if (finfo.operators!='custom' && finfo.alias!='') {
					fnam = finfo.alias;
				}
			} else if (finfo.operators!='custom') {
				if (finfo.alias=='') {
					fnam = finfo.fieldname.replace(' : (', '').replace(') ', '').replace(')', '').toLowerCase();
				} else {
					fnam = finfo.alias;
				}
			}
			slflds.push({
				fieldname:fnam,
				operation:'is',
				value:finfo.instruction,
				valuetype:(finfo.fieldname==finfo.instruction || finfo.operators=='custom' ? 'fieldname' : 'expression'),
				joincondition:'and',
				groupid:0,
				groupjoin:''
			});
		}
	});
	if (slflds.length>0) {
		slflds = JSON.stringify(slflds);
	} else {
		slflds = '';
	}
	return slflds;
}

function getSQLConditions() {
	var conditions = [];
	i=0;
	$('#save_conditions').children('.condition_group_block').each(function (j, conditiongroupblock) {
		$(conditiongroupblock).children('.save_condition_group').each(function (k, conditiongroup) {
			$(conditiongroup).children().each(function (l) {
				var fieldname = this.querySelector('div > .cefieldname').value;
				var operation = this.querySelector('div > .ceoperation').value;
				var value = this.querySelector('div > .ceexpressionvalue').value;
				var valuetype = this.querySelector('div > .ceexpressiontype').value;
				var joincondition = this.querySelector('div > .cejoincondition').value;
				var groupid = this.querySelector('div > .groupid').value;
				var groupjoin = '';
				if (groupid != '') {
					let scgj = document.getElementById('save_condition_group_'+groupid+'_joincondition');
					if (scgj != null) {
						groupjoin = scgj.value;
					}
				}
				var condition = {
					fieldname:fieldname,
					operation:operation,
					value:value,
					valuetype:valuetype,
					joincondition:joincondition,
					groupid:groupid,
					groupjoin:groupjoin
				};
				conditions[i++]=condition;
			});
		});
	});
	var cnflds = '';
	if (conditions.length!=0) {
		cnflds = JSON.stringify(conditions);
	}
	return cnflds;
}

function getSQLGroupBy() {
	let gbflds = '';
	fieldData.map(finfo => {
		if (finfo.fieldname != 'custom' && finfo.group == '1') {
			let fnam = finfo.fieldname;
			if (finfo.operators!='custom' && finfo.alias!='') {
				fnam = finfo.alias;
			} else if (finfo.fieldname.indexOf(': (')!=-1) {
				if (finfo.operators == 'custom') {
					fnam = finfo.fieldname.substr(finfo.fieldname.indexOf(': (')+3).replace(') ', '').replace(')', '').toLowerCase();
				} else {
					fnam = finfo.fieldname.replace(' : (', '').replace(') ', '').replace(')', '').toLowerCase();
				}
			} else if (fnam == 'assigned_user_id') {
				fnam = 'vtiger_crmentity.smownerid';
			} else if (fnam == 'created_user_id') {
				fnam = 'vtiger_crmentity.smcreatorid';
			} else if (typeof fieldNEcolumn[document.getElementById('bqmodule').value+fnam]!='undefined') {
				fnam = fieldTableRelation[finfo.fieldname]+'.'+fieldNEcolumn[document.getElementById('bqmodule').value+fnam];
			}
			gbflds += fnam + ',';
		}
	});
	if (gbflds!='') {
		gbflds = 'GROUP BY ' + gbflds.slice(0, -1);
	}
	return gbflds;
}

function getSQLOrderBy() {
	let obflds = '';
	fieldData.map(finfo => {
		if (finfo.fieldname != 'custom' && finfo.sort != 'NONE') {
			let fnam = finfo.fieldname;
			if (finfo.operators!='custom' && finfo.alias!='') {
				fnam = finfo.alias;
			} else if (finfo.fieldname.indexOf(': (')!=-1) {
				if (finfo.operators == 'custom') {
					fnam = finfo.fieldname.substr(finfo.fieldname.indexOf(': (')+3).replace(') ', '').replace(')', '').toLowerCase();
				} else {
					fnam = finfo.fieldname.replace(' : (', '').replace(') ', '').replace(')', '').toLowerCase();
				}
			} else if (fnam == 'assigned_user_id') {
				fnam = 'vtiger_crmentity.smownerid';
			} else if (fnam == 'created_user_id') {
				fnam = 'vtiger_crmentity.smcreatorid';
			} else if (typeof fieldNEcolumn[document.getElementById('bqmodule').value+fnam]!='undefined') {
				fnam = fieldTableRelation[finfo.fieldname]+'.'+fieldNEcolumn[document.getElementById('bqmodule').value+fnam];
			}
			obflds += fnam + ' ' + finfo.sort + ',';
		}
	});
	if (obflds!='') {
		obflds = 'ORDER BY ' + obflds.slice(0, -1);
	}
	return obflds;
}

function getSQLFinal() {
	let sql = 'SELECT '+getSQLSelect();
	sql += ' FROM '+document.getElementById('bqmodule').value;
	let cn = getSQLConditions();
	if (cn!='') {
		sql += ' WHERE '+cn;
	}
	let gb = getSQLGroupBy();
	if (gb!='') {
		sql += ' ' + gb;
	}
	let ob = getSQLOrderBy();
	if (ob!='') {
		sql += ' ' + ob;
	}
	let limit = document.getElementById('qpagesize').value;
	if (limit!=0) {
		sql += ' LIMIT '+limit;
	}
	return sql;
}

function updateWSSQL() {
	document.getElementById('bqwsq').value = getSQLFinal();
}

function updateFieldData(row, field, value) {
	fieldData[row][field] = value;
	updateWSSQL();
}

function getEmptyFieldRow() {
	return {
		'fieldname': 'custom',
		'operators': 'custom',
		'alias': '',
		'sort': 'NONE',
		'group': '0',
		'instruction': ''
	};
}

function appendEmptyFieldRow(ev) {
	let emptyRow = getEmptyFieldRow();
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
			case 'getEntityType':
			case 'number_format':
			case 'getSetting':
			case 'count':
				if (op.text.indexOf("('")!=-1) {
					fins = op.text.replace(/\('?.+?'?,/, "('"+fnam+"',");
				} else if (op.text.indexOf(',')!=-1) {
					if (fnam.indexOf(': (')!=-1) {
						fins = op.text.replace(/\(.+?,/, '($('+fnam+'),');
					} else {
						fins = op.text.replace(/\(.+?,/, '('+fnam+',');
					}
				} else {
					if (fnam.indexOf(': (')!=-1) {
						fins = op.text.replace(/\(.+\)/, '($('+fnam+'))');
					} else {
						fins = op.text.replace(/\(.+\)/, '('+fnam+')');
					}
				}
				break;
			case 'today':
			case 'tomorrow':
			case 'yesterday':
				fins = op.text;
				break;
			default:
				break;
		}
	} else {
		if (fnam.indexOf(': (')!=-1) {
			fins = fnam.substr(fnam.indexOf(': (')+3).replace(') ', '.');
		} else {
			fins = fnam;
		}
	}
	return fins;
}

const tuiGrid = tui.Grid;
var dataGridInstance;
var fieldGridInstance;
var fieldGridColumns = '';
document.addEventListener('DOMContentLoaded', function (event) {
	loadJS('index.php?module=cbQuestion&action=cbQuestionAjax&file=getjslanguage')
	.then(() => {
		fieldGridColumns = [
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
					updateFieldData(ev.rowKey, 'fieldname', ev.value);
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
					updateFieldData(ev.rowKey, 'operators', ev.value);
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
					updateFieldData(ev.rowKey, 'alias', ev.value);
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
				sortable: false,
				onAfterChange(ev) {
					updateFieldData(ev.rowKey, 'sort', ev.value);
				}
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
				sortable: false,
				onAfterChange(ev) {
					updateFieldData(ev.rowKey, 'group', ev.value);
				}
			},
			{
				name: 'instruction',
				header: mod_alert_arr.LBL_INSTRUCTION,
				editor: 'text',
				whiteSpace: 'normal',
				sortable: false,
				onAfterChange(ev) {
					updateFieldData(ev.rowKey, 'instruction', ev.value);
				}
			}
		];
		fieldGridInstance = new tuiGrid({
			el: document.getElementById('fieldgrid'),
			columns: fieldGridColumns,
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
				valign: 'middle'
			}
		});
		dataGridInstance = new tuiGrid({
			el: document.getElementById('resultsgrid'),
			columns: [{
				'name': 'empty',
				'header': 'empty',
				'whiteSpace': 'normal',
				'sortable': false
			}],
			data: {
				api: {
					readData: {
						url: 'index.php?module=cbQuestion&action=cbQuestionAjax&actionname=qactions&method=getBuilderData',
						method: 'GET'
					}
				}
			},
			pageOptions: {
				perPage: Report_ListView_PageSize
			},
			useClientSort: false,
			rowHeight: 'auto',
			bodyHeight: 550,
			scrollX: true,
			scrollY: true,
			columnOptions: {
				resizable: true
			},
			header: {
				align: 'left',
				valign: 'middle'
			}
		});
		tui.Grid.applyTheme('striped');
	});
});
