var searchConditions = [
	{"groupid":"1",
	 "columnname":"vtiger_cbmap:maptype:maptype:cbMap_Map_Type:V",
	 "comparator":"e",
	 "value":"Condition Expression",
	 "columncondition":"or"},
	{"groupid":"1",
	 "columnname":"vtiger_cbmap:maptype:maptype:cbMap_Map_Type:V",
	 "comparator":"e",
	 "value":"Condition Query",
	 "columncondition":""}
];
var advSearch = '&query=true&searchtype=advance&advft_criteria='+convertArrayOfJsonObjectsToString(searchConditions);
var SpecialSearch = encodeURI(advSearch);

function changeHitPolicy(value) {
	document.getElementById('aggregate').disabled = (value!='G');
}

function setActiveDTTab(acttab, deactab) {
	var ate = document.getElementById(acttab);
	var dte = document.getElementById(deactab);
	var atl = document.getElementById(acttab+'li');
	var dtl = document.getElementById(deactab+'li');
	atl.classList.add('slds-is-active');
	atl.classList.add('slds-has-focus');
	dtl.classList.remove('slds-is-active');
	dtl.classList.remove('slds-has-focus');
	ate.classList.add('slds-show');
	ate.classList.remove('slds-hide');
	dte.classList.add('slds-hide');
	dte.classList.remove('slds-show');
}

function setRuleExpression(row) {
}

function setRuleBMap(row) {
}

function setRuleDecisionTable(row) {
}

function setRuleDefinition(ev) {
	//rulegridInstance.addRowClassName(ev.rowKey, 'slds-button_icon-brand');
	switch (ruleData[ev.rowKey].ruletype) {
		case 'businessmap':
			document.getElementById('expeditsection').style.display='none';
			document.getElementById('bmeditsection').style.display='block';
			document.getElementById('dteditsection').style.display='none';
			setRuleBMap(ev.rowKey);
			break;
		case 'decisiontable':
			document.getElementById('expeditsection').style.display='none';
			document.getElementById('bmeditsection').style.display='none';
			document.getElementById('dteditsection').style.display='block';
			setRuleDecisionTable(ev.rowKey);
			break;
		default:
			document.getElementById('expeditsection').style.display='block';
			document.getElementById('bmeditsection').style.display='none';
			document.getElementById('dteditsection').style.display='none';
			setRuleExpression(ev.rowKey);
			break;
	}
}

const ruleData = [
	{
		'sequence': '1',
		'ruletype': 'expression',
		'output': 'fieldvalue',
	},
	{
		'sequence': '2',
		'ruletype': 'businessmap',
		'output': 'fieldvalue',
	},
	{
		'sequence': '3',
		'ruletype': 'decisiontable',
		'output': 'fieldvalue',
	}
];
const srchData = [
	{
		'srchinvar': 'inputvar',
		'srchop': 'equals',
		'srchfield': 'fieldvalue',
		'srchexp': 'expression',
	}
];
const condData = [
	{
		'condinvar': 'inputvar',
		'condop': 'equals',
		'condfield': 'fieldvalue',
	}
];
const tuiGrid = tui.Grid;
var rulegridInstance;
var rulegrid = '';
var condgridInstance;
var condgrid = '';
var srchgridInstance;
var srchgrid = '';
document.addEventListener('DOMContentLoaded', function (event) {
	loadJS('index.php?module=cbMap&action=cbMapAjax&file=getjslanguage')
	.then(() => {
		rulegrid = [
			{
				name: 'sequence',
				header: mod_alert_arr.LBL_SEQUENCE,
				editor: 'text',
				sortable: false,
				onAfterChange(ev) {
					// const idx = rulegridInstance.getIndexOfRow(ev.rowKey);
					// ruleData[idx].instruction = getInstruction(ev.value, ruleData[idx].operators, ruleData[idx].alias);
					// updateruleData(idx, 'fieldname', ev.value);
					// rulegridInstance.resetData(ruleData);
				}
			},
			{
				name: 'ruletype',
				header: mod_alert_arr.LBL_RULETYPE,
				formatter: 'listItemText',
				editor: {
					type: 'select',
					options: {
						listItems: [
							{text: mod_alert_arr.LBL_EXPRESSION, value: 'expression'},
							{text: mod_alert_arr.LBL_MAP, value: 'businessmap'},
							{text: mod_alert_arr.LBL_TABLE, value: 'decisiontable'},
						]
					}
				},
				whiteSpace: 'normal',
				sortable: false,
				onAfterChange(ev) {
					// const idx = rulegridInstance.getIndexOfRow(ev.rowKey);
					// ruleData[idx].instruction = getInstruction(ruleData[idx].fieldname, ev.value, ruleData[idx].alias);
					// updateruleData(idx, 'operators', ev.value);
					// rulegridInstance.resetData(ruleData);
				}
			},
			{
				name: 'output',
				header: mod_alert_arr.LBL_OUTPUT,
				editor: {
					type: 'select',
					options: {
						listItems: [
							{text: mod_alert_arr.LBL_EXPRESSIONRESULT, value: 'expression'},
							{text: mod_alert_arr.LBL_FIELDVALUE, value: 'fieldvalue'},
							{text: mod_alert_arr.LBL_OBJECT, value: 'crmobject'},
							{text: mod_alert_arr.LBL_ROW, value: 'row'},
						]
					}
				},
				whiteSpace: 'normal',
				width: 250,
				sortable: false,
				onAfterChange(ev) {
					// const idx = rulegridInstance.getIndexOfRow(ev.rowKey);
					// updateruleData(idx, 'alias', ev.value);
				}
			},
		];
		rulegridInstance = new tuiGrid({
			el: document.getElementById('rulegrid'),
			rowHeaders: [
				{
					type: 'checkbox',
					header: `
					<label for="all-checkbox" class="checkbox">
						<input type="checkbox" id="all-checkbox" class="hidden-input" name="_checked" />
						<span class="custom-input"></span>
					</label>`,
					renderer: {
						type: CheckboxRenderer
					}
				}
			],
			columns: rulegrid,
			data: ruleData,
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
		rulegridInstance.on('click', ev => {
			setRuleDefinition(ev);
		});
		/////////////////////////
		srchgrid = [
			{
				name: 'srchinvar',
				header: mod_alert_arr.LBL_INPUT,
				editor: 'text',
				sortable: false,
				onAfterChange(ev) {
					// const idx = srchgridInstance.getIndexOfRow(ev.rowKey);
					// srchData[idx].instruction = getInstruction(ev.value, srchData[idx].operators, srchData[idx].alias);
					// updatesrchData(idx, 'fieldname', ev.value);
					// srchgridInstance.resetData(srchData);
				}
			},
			{
				name: 'srchop',
				header: mod_alert_arr.LBL_OPERATION,
				formatter: 'listItemText',
				editor: {
					type: 'select',
					options: {
						listItems: [
							{text: mod_alert_arr.LBL_EXPRESSION, value: 'expression'},
							{text: mod_alert_arr.LBL_MAP, value: 'businessmap'},
							{text: mod_alert_arr.LBL_TABLE, value: 'decisiontable'},
						]
					}
				},
				whiteSpace: 'normal',
				sortable: false,
				onAfterChange(ev) {
					// const idx = srchgridInstance.getIndexOfRow(ev.rowKey);
					// srchData[idx].instruction = getInstruction(srchData[idx].fieldname, ev.value, srchData[idx].alias);
					// updatesrchData(idx, 'operators', ev.value);
					// srchgridInstance.resetData(srchData);
				}
			},
			{
				name: 'srchfield',
				header: mod_alert_arr.LBL_FIELDVALUE,
				editor: {
					type: 'select',
					options: {
						listItems: [
							{text: mod_alert_arr.LBL_EXPRESSIONRESULT, value: 'expression'},
							{text: mod_alert_arr.LBL_FIELDVALUE, value: 'fieldvalue'},
							{text: mod_alert_arr.LBL_OBJECT, value: 'crmobject'},
							{text: mod_alert_arr.LBL_ROW, value: 'row'},
						]
					}
				},
				whiteSpace: 'normal',
				width: 250,
				sortable: false,
				onAfterChange(ev) {
					// const idx = srchgridInstance.getIndexOfRow(ev.rowKey);
					// updatesrchData(idx, 'alias', ev.value);
				}
			},
			{
				name: 'srchexp',
				header: mod_alert_arr.LBL_EXPRESSION,
				editor: 'text',
				whiteSpace: 'normal',
				width: 250,
				sortable: false,
				onAfterChange(ev) {
					// const idx = srchgridInstance.getIndexOfRow(ev.rowKey);
					// updatesrchData(idx, 'alias', ev.value);
				}
			},
		];
		srchgridInstance = new tuiGrid({
			el: document.getElementById('srchgrid'),
			rowHeaders: [
				{
					type: 'checkbox',
					header: `
					<label for="all-checkbox" class="checkbox">
						<input type="checkbox" id="all-srchcheckbox" class="hidden-input" name="_checked" />
						<span class="custom-input"></span>
					</label>`,
					renderer: {
						type: CheckboxRenderer
					}
				}
			],
			columns: srchgrid,
			data: srchData,
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
		////////////////////
		condgrid = [
			{
				name: 'condinvar',
				header: mod_alert_arr.LBL_INPUT,
				editor: 'text',
				sortable: false,
				onAfterChange(ev) {
					// const idx = condgridInstance.getIndexOfRow(ev.rowKey);
					// condData[idx].instruction = getInstruction(ev.value, condData[idx].operators, condData[idx].alias);
					// updatecondData(idx, 'fieldname', ev.value);
					// condgridInstance.resetData(condData);
				}
			},
			{
				name: 'condop',
				header: mod_alert_arr.LBL_OPERATION,
				formatter: 'listItemText',
				editor: {
					type: 'select',
					options: {
						listItems: [
							{text: mod_alert_arr.LBL_EXPRESSION, value: 'expression'},
							{text: mod_alert_arr.LBL_MAP, value: 'businessmap'},
							{text: mod_alert_arr.LBL_TABLE, value: 'decisiontable'},
						]
					}
				},
				whiteSpace: 'normal',
				sortable: false,
				onAfterChange(ev) {
					// const idx = condgridInstance.getIndexOfRow(ev.rowKey);
					// condData[idx].instruction = getInstruction(condData[idx].fieldname, ev.value, condData[idx].alias);
					// updatecondData(idx, 'operators', ev.value);
					// condgridInstance.resetData(condData);
				}
			},
			{
				name: 'condfield',
				header: mod_alert_arr.LBL_FIELDVALUE,
				editor: {
					type: 'select',
					options: {
						listItems: [
							{text: mod_alert_arr.LBL_EXPRESSIONRESULT, value: 'expression'},
							{text: mod_alert_arr.LBL_FIELDVALUE, value: 'fieldvalue'},
							{text: mod_alert_arr.LBL_OBJECT, value: 'crmobject'},
							{text: mod_alert_arr.LBL_ROW, value: 'row'},
						]
					}
				},
				whiteSpace: 'normal',
				width: 250,
				sortable: false,
				onAfterChange(ev) {
					// const idx = condgridInstance.getIndexOfRow(ev.rowKey);
					// updatecondData(idx, 'alias', ev.value);
				}
			},
		];
		condgridInstance = new tuiGrid({
			el: document.getElementById('condgrid'),
			rowHeaders: [
				{
					type: 'checkbox',
					header: `
					<label for="all-checkbox" class="checkbox">
						<input type="checkbox" id="all-condcheckbox" class="hidden-input" name="_checked" />
						<span class="custom-input"></span>
					</label>`,
					renderer: {
						type: CheckboxRenderer
					}
				}
			],
			columns: condgrid,
			data: condData,
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
	});
});