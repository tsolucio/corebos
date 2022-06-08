var searchConditions = [
	{
		'groupid':'1',
		'columnname':'vtiger_cbmap:maptype:maptype:cbMap_Map_Type:V',
		'comparator':'e',
		'value':'Condition Expression',
		'columncondition':'or'
	},
	{
		'groupid':'1',
		'columnname':'vtiger_cbmap:maptype:maptype:cbMap_Map_Type:V',
		'comparator':'e',
		'value':'Condition Query',
		'columncondition':''
	}
];
var advSearch = '&query=true&searchtype=advance&advft_criteria='+convertArrayOfJsonObjectsToString(searchConditions);
var SpecialSearch = encodeURI(advSearch);

function changeHitPolicy(value) {
	document.getElementById('aggregate').disabled = (value!='G');
}
/**
 * Set active tab
 * @param {string} acttab
 * @param {string} deactab
 * @param {int} rowKey
 */
function setActiveDTTab(acttab, deactab, rowKey) {
	var ate = document.getElementById(acttab+'-'+rowKey);
	var dte = document.getElementById(deactab+'-'+rowKey);
	var atl = document.getElementById(acttab+'li'+rowKey);
	var dtl = document.getElementById(deactab+'li'+rowKey);
	atl.classList.add('slds-is-active');
	atl.classList.add('slds-has-focus');
	dtl.classList.remove('slds-is-active');
	dtl.classList.remove('slds-has-focus');
	ate.classList.add('slds-show');
	ate.classList.remove('slds-hide');
	dte.classList.add('slds-hide');
	dte.classList.remove('slds-show');
}
/**
 * Change active tabs
 * @param {object} ev
 */
function setRuleDefinition(ev) {
	for (let i in ruleData) {
		const idx = parseInt(ev.rowKey) + 1;
		const ruletype = rulegridInstance.getValue(ev.rowKey, 'ruletype');
		switch (ruletype) {
		case 'businessmap':
			for (var j = 1; j <= ruleData.length; j++) {
				if (j != idx && document.getElementById(`bmeditsection-${j}`)) {
					document.getElementById(`bmeditsection-${j}`).style.display='none';
				}
				if (document.getElementById(`expeditsection-${j}`)) {
					document.getElementById(`expeditsection-${j}`).style.display='none';
				}
				if (document.getElementById(`dteditsection-${j}`)) {
					document.getElementById(`dteditsection-${j}`).style.display='none';
				}
			}
			if (document.getElementById(`bmeditsection-${idx}`)) {
				document.getElementById(`bmeditsection-${idx}`).style.display='block';
			}
			break;
		case 'decisiontable':
			for (var j = 1; j <= ruleData.length; j++) {
				if (j != idx && document.getElementById(`dteditsection-${j}`)) {
					document.getElementById(`dteditsection-${j}`).style.display='none';
				}
				if (document.getElementById(`expeditsection-${j}`)) {
					document.getElementById(`expeditsection-${j}`).style.display='none';
				}
				if (document.getElementById(`bmeditsection-${j}`)) {
					document.getElementById(`bmeditsection-${j}`).style.display='none';
				}
			}
			if (document.getElementById(`dteditsection-${idx}`)) {
				document.getElementById(`dteditsection-${idx}`).style.display='block';
			}
			break;
		default:
			for (var j = 1; j <= ruleData.length; j++) {
				if (j != idx && document.getElementById(`expeditsection-${j}`)) {
					document.getElementById(`expeditsection-${j}`).style.display='none';
				}
				if (document.getElementById(`bmeditsection-${j}`)) {
					document.getElementById(`bmeditsection-${j}`).style.display='none';
				}
				if (document.getElementById(`dteditsection-${j}`)) {
					document.getElementById(`dteditsection-${j}`).style.display='none';
				}
			}
			if (document.getElementById(`expeditsection-${idx}`)) {
				document.getElementById(`expeditsection-${idx}`).style.display='block';
			}
			break;
		}
	}
}
/**
 * Reset fields when change the module
 * @param {string} module
 * @param {int} rowKey
 */
async function getDTModuleFields(module, rowKey) {
	DTModuleFields = await getFieldsForModule(module);
	document.getElementById(`returnfields-${rowKey}`).value = '';
	document.getElementById(`setreturnfields-${rowKey}`).innerHTML = '';
	document.getElementById(`orderbyrule-${rowKey}`).value = '';
	document.getElementById(`setorderbyrule-${rowKey}`).innerHTML = '';
}
/**
 * Get all fields for module
 * @param {string} module
 */
async function getFieldsForModule(module) {
	return await fetch(
		'index.php?module=cbMap&action=cbMapAjax&actionname=mapactions&method=getFieldTranslationForModule',
		{
			method: 'post',
			headers: {
				'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			credentials: 'same-origin',
			body: '&'+csrfMagicName+'='+csrfMagicToken+'&fieldsmodule='+module
		}
	).then(response => response.json()).then(response => {
		return response;
	});
}
/**
 * Load fields in UI
 * @param {string} id
 * @param {int} rowKey
 */
async function loadFields(id, rowKey) {
	const module = document.getElementById(`dtmodule-${rowKey}`).value;
	DTModuleFields = await getFieldsForModule(module);
	const inStyle = {
		style: `background: white;
		border: 1px solid #d1d1d1;
		position: absolute;
		z-index: 1000;
		height: 200px;
		width: 130px;
		overflow:hidden;
		overflow-y:scroll;`
	};
	let listFields = `<ul class="slds-dropdown__list slds-dropdown__scroll" style="${inStyle.style}">`;
	for (let fields in DTModuleFields) {
		listFields += `
		<li class="slds-dropdown__item">
			<a tabindex="${fields}" id="${fields}" onclick="setFieldValues('${id}', ${rowKey}, this.id, '${DTModuleFields[fields]}')">
				<span class="slds-truncate">${DTModuleFields[fields]}</span>
			</a>
		</li>`;
	}
	listFields += '</ul>';
	document.getElementById(`${id}`).innerHTML = listFields;
}
/**
 * Set pills in UI
 * @param {string} inputid
 * @param {int} rowKey
 * @param {string} value
 */
function setFieldValues(inputid, rowKey, value, translatedValue) {
	let template = `
<li class="slds-listbox-item" role="presentation" id="${inputid}-${value}">
	<span class="slds-pill" role="option" aria-selected="true">
	<span class="slds-pill__label" title="${value}">${translatedValue}</span>
	<span class="slds-icon_container slds-pill__remove" id="remove-${inputid}-${value}" onclick="removeField(this, ${rowKey}, '${value}')" style="cursor: pointer">
		<svg class="slds-icon slds-icon_x-small slds-icon-text-default" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
		</svg>
	</span>
	</span>
</li>`;
	if (inputid == `showReturnFields-${rowKey}`) {
		const returnfields = document.getElementById(`returnfields-${rowKey}`).value;
		const getreturnfields = document.getElementById(`setreturnfields-${rowKey}`).innerHTML;
		const pills = `${getreturnfields}${template}`;
		const newValue = `${returnfields}${value},`;
		document.getElementById(`returnfields-${rowKey}`).value = newValue;
		document.getElementById(`setreturnfields-${rowKey}`).innerHTML = pills;
	}
	if (inputid == `showOrderByFields-${rowKey}`) {
		const orderbyrule = document.getElementById(`orderbyrule-${rowKey}`).value;
		const getorderbyrule = document.getElementById(`setorderbyrule-${rowKey}`).innerHTML;
		const pills = `${getorderbyrule}${template}`;
		const newValue = `${orderbyrule}${value},`;
		document.getElementById(`orderbyrule-${rowKey}`).value = newValue;
		document.getElementById(`setorderbyrule-${rowKey}`).innerHTML = pills;
	}
}
/**
 * Remove pills
 * @param {string} el
 * @param {int} rowKey
 * @param {string} val
 */
function removeField(el, rowKey, val) {
	let id = el.id.split('-')[2];
	const type = el.id.split('-')[1];
	const pills = document.getElementById(`${type}-${id}-${val}`);
	let objValues;
	let newValues = '';
	pills.parentNode.removeChild(pills);
	id = el.id.split('-')[3];
	if (type == 'showReturnFields') {
		const returnfields = document.getElementById(`returnfields-${rowKey}`).value;
		objValues = returnfields.split(',');
		for (let i in objValues) {
			if (objValues[i] != id && objValues[i] != '') {
				newValues += `${objValues[i]},`;
			}
		}
		document.getElementById(`returnfields-${rowKey}`).value = newValues;
	}
	if (type == 'showOrderByFields') {
		const orderbyrule = document.getElementById(`orderbyrule-${rowKey}`).value;
		objValues = orderbyrule.split(',');
		for (let i in objValues) {
			if (objValues[i] != id && objValues[i] != '') {
				newValues += `${objValues[i]},`;
			}
		}
		document.getElementById(`orderbyrule-${rowKey}`).value = newValues;
	}
}
/**
 * Close list fields on click
 */
document.addEventListener('click', function (event) {
	const getSection = document.getElementsByClassName('closeList');
	let getIds = Array.prototype.filter.call(getSection, function (el) {
		return el.nodeName;
	});
	for (let i in getIds) {
		document.getElementById(getIds[i].id).innerHTML = '';
	}
});

var DTModuleFields = {};
var FIELD = '';
var ruleData = new Array();
var condGroup = new Array();
var srchData = new Array();
var fieldList = new Array();
const tuiGrid = tui.Grid;
var rulegridInstance;
var rulegrid = '';
var condgridInstance = new Array();
var condgrid = '';
var srchgridInstance = new Array();
var srchgrid = '';
var hitpolicy = '';
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
					const idx = rulegridInstance.getIndexOfRow(ev.rowKey);
					updateruleData(idx, 'fieldname', ev.value);
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
					const idx = rulegridInstance.getIndexOfRow(ev.rowKey);
					updateruleData(idx, 'operators', ev.value);
					generateSection(ev);
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
					const idx = rulegridInstance.getIndexOfRow(ev.rowKey);
					updateruleData(idx, 'alias', ev.value);
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
		preLoadMap();
	});
});
/**
 * Preload rows on grid
 * @param {string} type
 */
function getEmptyFieldRow(type) {
	if (type == 'rule') {
		return {
			'sequence': '1',
			'ruletype': 'expression',
			'output': 'fieldvalue',
		};
	} else if (type == 'condition') {
		return {
			'input': 'inputvar',
			'operation': 'e',
			'field': 'fieldvalue',
		};
	} else if (type == 'search') {
		return {
			'input': 'inputvar',
			'preprocess': 'e',
			'operation': 'expression',
			'field': 'fieldvalue',
		};
	}
}
/**
 * Append empty field row
 * @param {string} type
 * @param {int} rowKey
 */
function appendEmptyFieldRow(type, rowKey = '') {
	let emptyRow = getEmptyFieldRow(type);
	if (type == 'rule') {
		const data = rulegridInstance.store.data.viewData;
		const key = parseInt(data.length) + 1;
		emptyRow.sequence = key;
		rulegridInstance.appendRow(emptyRow);
		ruleData.push(emptyRow);
		if (data.length == 1) {
			generateRuleDefinition(key);
		} else {
			generateRuleDefinition(key, 'none');
		}
	} else if (type == 'condition') {
		condGroup[rowKey].push(emptyRow);
		condgridInstance[rowKey].appendRow(emptyRow);
		updateFieldList(rowKey);
	} else if (type == 'search') {
		srchData[rowKey].push(emptyRow);
		srchgridInstance[rowKey].appendRow(emptyRow);
		updateFieldList(rowKey);
	}
}
/**
 * Delete rows in grid
 */
function deleteFieldRow() {
	const checkedRows = rulegridInstance.getCheckedRowKeys();
	var value = '';
	for (var i = checkedRows.length-1; i >= 0; i--) {
		ruleData.splice(checkedRows[i], 1);
		const idx = parseInt(checkedRows[i]) + 1;
		value = rulegridInstance.getValue(checkedRows[i], 'ruletype');
		if (value == 'expression') {
			value = 'expeditsection';
		} else if (value == 'businessmap') {
			value = 'bmeditsection';
		} else {
			value = 'dteditsection';
		}
		const sectionToRemove = document.getElementById(`${value}-${idx}`);
		sectionToRemove.parentNode.removeChild(sectionToRemove);
	}
	rulegridInstance.removeCheckedRows();
	//udpdate ids on sections
	const getSection = document.getElementsByClassName('expeditsection');
	let getIds = Array.prototype.filter.call(getSection, function (el) {
		return el.nodeName;
	});
	let inc = 1;
	for (let n in getIds) {
		const oldId = document.getElementById(getIds[n].id);
		const type = getIds[n].id.split('-')[0];
		const id = getIds[n].id.split('-')[1];
		//reset all ids after delete one rule
		if (type == 'expeditsection') {
			const removeData = {
				el: [
					'exptextarea',
				],
				'attr': {
					'function-btn-': `openFunctionSelection('exptextarea-${inc}')`,
				}
			};
			setNewIds(removeData, id, inc);
			oldId.id = `expeditsection-${inc}`;
		} else if (type == 'bmeditsection') {
			const bmapid = document.getElementById(`bmapid_${id}`);
			const bmapidDisplay = document.getElementById(`bmapid_${id}_display`);
			const clearBtn = document.getElementById(`clear-btn-${id}`);
			const dtbmselection = document.getElementsByName(`dtbmselection_${id}`)[0];
			dtbmselection.name = `dtbmselection_${inc}`;
			bmapid.id = `bmapid_${inc}`;
			bmapid.name = `bmapid_${inc}`;
			bmapidDisplay.id = `bmapid_${inc}_display`;
			bmapidDisplay.name = `bmapid_${inc}_display`;
			bmapidDisplay.removeAttribute('onclick');
			bmapidDisplay.setAttribute('onclick', `return window.open('index.php?module=cbMap&action=Popup&html=Popup_picker&form=dtbmselection_${inc}&forfield=bmapid_${inc}&srcmodule=GlobalVariable'+SpecialSearch, 'vtlibui10wf', cbPopupWindowSettings)`);
			clearBtn.id = `clear-btn-${inc}`;
			clearBtn.removeAttribute('onclick');
			clearBtn.setAttribute('onclick', `this.form.bmapid_${inc}.value=''; this.form.bmapid_${inc}_display.value=''; return false;`);
			oldId.id = `bmeditsection-${inc}`;
		} else {
			const removeData = {
				el: [
					'dtmodule',
					'returnfields',
					'orderbyrule',
					'showReturnFields',
					'showOrderByFields',
					'setreturnfields',
					'setorderbyrule',
					'tabsearch',
					'tabconditions',
					'condgrid',
					'srchgrid',
				],
				'attr': {
					'loadFields-order-': `loadFields('showOrderByFields-${inc}', ${inc})`,
					'loadFields-return-': `loadFields('showReturnFields-${inc}', ${inc})`,
					'tabsearchli': `setActiveDTTab('tabsearch', 'tabconditions',  ${inc})`,
					'tabconditionsli': `setActiveDTTab('tabconditions', 'tabsearch', ${inc})`,
					'appendEmptyFieldRow-condition-': `appendEmptyFieldRow('condition', ${inc}); event.stopPropagation();`,
					'appendEmptyFieldRow-search-': `appendEmptyFieldRow('search', ${inc}); event.stopPropagation();`,
					'delfield_button-condition-': `deleteCondRow(${inc}); event.stopPropagation();`,
					'delfield_button-search-': `deleteSrchRow(${inc}); event.stopPropagation();`,
				}
			};
			setNewIds(removeData, id, inc);
			//update return fields
			const returnfieldsData = document.getElementById(`returnfields-${inc}`).value.split(',');
			for (let r in returnfieldsData) {
				if (returnfieldsData[r] != '') {
					const showId = document.getElementById(`showReturnFields-${id}-${returnfieldsData[r]}`);
					const removeId = document.getElementById(`remove-showReturnFields-${id}-${returnfieldsData[r]}`);
					showId.id = `showReturnFields-${inc}-${returnfieldsData[r]}`;
					removeId.id = `remove-showReturnFields-${inc}-${returnfieldsData[r]}`;
					removeId.removeAttribute('onclick');
					removeId.setAttribute('onclick', `removeField(this, ${inc}, '${returnfieldsData[r]}')`);
				}
			}
			//update orderby fields
			const orderbyData = document.getElementById(`orderbyrule-${inc}`).value.split(',');
			for (let o in orderbyData) {
				if (orderbyData[o] != '') {
					const showOrderId = document.getElementById(`showOrderByFields-${id}-${orderbyData[o]}`);
					const removeOrderId = document.getElementById(`remove-showOrderByFields-${id}-${orderbyData[o]}`);
					showOrderId.id = `showOrderByFields-${inc}-${orderbyData[o]}`;
					removeOrderId.id = `remove-showOrderByFields-${inc}-${orderbyData[o]}`;
					removeOrderId.removeAttribute('onclick');
					removeOrderId.setAttribute('onclick', `removeField(this, ${inc}, '${orderbyData[o]}')`);
				}
			}
			oldId.id = `dteditsection-${inc}`;
			//reset data for search and condition grids
			condGroup[inc] = new Array();
			condGroup[inc] = condGroup[id];
			delete condGroup[id];
			condgridInstance[inc] = new Array();
			condgridInstance[inc] = condgridInstance[id];
			delete condgridInstance[id];
			srchData[inc] = new Array();
			srchData[inc] = srchData[id];
			delete srchData[id];
			srchgridInstance[inc] = new Array();
			srchgridInstance[inc] = srchgridInstance[id];
			delete srchgridInstance[id];
		}
		inc++;
	}
	for (let i in ruleData) {
		ruleData[i].sequence = parseInt(i) + 1;
		ruleData[i].rowKey = parseInt(i);
		ruleData[i].sortKey = parseInt(i);
	}
	rulegridInstance.resetData(ruleData);
}
/**
 * Update ids for decision table when one row is removed
 * @param {object} data
 * @param {int} id
 * @param {int} inc
 */
function setNewIds(data, id, inc) {
	for (var j in data) {
		if (j == 'el') {
			for (var i = 0; i < data[j].length; i++) {
				const el = document.getElementById(`${data[j][i]}-${id}`);
				el.id = `${data[j][i]}-${inc}`;
				el.name = `${data[j][i]}-${inc}`;
			}
		}
		if (j == 'attr') {
			for (var i in data[j]) {
				const el = document.getElementById(`${i}${id}`);
				el.id = `${i}${inc}`;
				el.name = `${i}${inc}`;
				el.removeAttribute('onclick');
				el.setAttribute('onclick', `${data[j][i]}`);
			}
		}
	}
}
/**
 * Delete row in search grid
 * @param {int} rowKey
 */
function deleteSrchRow(rowKey) {
	const checkedRows = srchgridInstance[rowKey].getCheckedRowKeys();
	for (var i = checkedRows.length-1; i >= 0; i--) {
		srchData[rowKey].splice(checkedRows[i], 1);
	}
	for (let i in srchData[rowKey]) {
		srchData[rowKey][i].rowKey = parseInt(i);
		srchData[rowKey][i].sortKey = parseInt(i);
	}
	srchgridInstance[rowKey].removeCheckedRows();
	srchgridInstance[rowKey].resetData(srchData[rowKey]);
}
/**
 * Delete row in codition grid
 * @param {int} rowKey
 */
function deleteCondRow(rowKey) {
	const checkedRows = condgridInstance[rowKey].getCheckedRowKeys();
	for (var i = checkedRows.length-1; i >= 0; i--) {
		condGroup[rowKey].splice(checkedRows[i], 1);
	}
	for (let i in condGroup[rowKey]) {
		condGroup[rowKey][i].rowKey = parseInt(i);
		condGroup[rowKey][i].sortKey = parseInt(i);
	}
	condgridInstance[rowKey].removeCheckedRows();
	condgridInstance[rowKey].resetData(condGroup[rowKey]);
}
/**
 * Update object on row delete
 * @param {string} row
 * @param {string} field
 * @param {string} value
 */
function updateruleData(row, field, value) {
	ruleData[row][field] = value;
	rulegridInstance.resetData(ruleData);
}
/**
 * Update object on row delete
 * @param {string} row
 * @param {string} field
 * @param {string} value
 * @param {int} rowKey
 */
function updatecondData(row, field, value, rowKey) {
	condGroup[rowKey][row][field] = value;
	condgridInstance[rowKey].resetData(condGroup[rowKey]);
}
/**
 * Update object on row delete
 * @param {string} row
 * @param {string} field
 * @param {string} value
 * @param {int} rowKey
 */
function updatesrchData(row, field, value, rowKey) {
	srchData[rowKey][row][field] = value;
	srchgridInstance[rowKey].resetData(srchData[rowKey]);
}
/**
 * Generate template in first create of the row
 * @param {int} rowKey
 * @param {string} show
 */
function generateRuleDefinition(rowKey, show = '') {
	var inStyle = {
		style: 'display: block;'
	};
	if (show == 'none') {
		inStyle = {
			style: 'display: none;'
		};
	}
	let createSection = document.createElement('section');
	createSection.id = `expeditsection-${rowKey}`;
	createSection.className = 'expeditsection';
	createSection.style = `${inStyle.style}`;
	document.getElementById('show-ruleeditsection').appendChild(createSection);
	const template = `
		<div class="slds-p-around_x-small slds-grid slds-gutters">
			<div class="slds-col slds-size_1-of-2 slds-text-align_left">
				<h2 class="slds-expression__title">${mod_alert_arr.LBL_EXPRESSION}</h2>
			</div>
			<div class="slds-col slds-size_1-of-2 slds-text-align_right">
				<button class="slds-button slds-button_neutral" id="function-btn-${rowKey}" onclick="openFunctionSelection('exptextarea-${rowKey}');">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
					</svg>
					${mod_alert_arr.LBL_FUNCTIONNAME}
				</button>
			</div>
		</div>
		<div class="slds-p-around_x-small slds-grid slds-gutters">
			<div class="slds-col slds-size_1-of-1 slds-text-align_left">
				<textarea id="exptextarea-${rowKey}" class="slds-textarea"></textarea>
			</div>
		</div>`;
	document.getElementById(`expeditsection-${rowKey}`).innerHTML = template;
}
/**
 * Generate all templates for specific row
 * @param {object} ev
 * @param {object} preLoadMap
 */
async function generateSection(ev, preLoadMap = {}) {
	const rowKey = parseInt(ev.rowKey) + 1;
	const value = ev.value;
	const prevValue = ev.prevValue;
	let sectionId = '';
	let template = '';
	var inStyle = {
		style: 'display: block;'
	};

	//remove old section
	if (prevValue == 'expression') {
		sectionId = 'expeditsection';
	} else if (prevValue == 'businessmap') {
		sectionId = 'bmeditsection';
	} else {
		sectionId = 'dteditsection';
	}
	const sectionToRemove = document.getElementById(`${sectionId}-${rowKey}`);
	if (sectionToRemove) {
		sectionToRemove.parentNode.removeChild(sectionToRemove);
	}
	if (value == 'businessmap') {
		if (document.getElementById(`bmeditsection-${rowKey}`)) {
			return;
		}
		let mapId = '';
		if (preLoadMap.mapid) {
			mapId = preLoadMap.mapid;
		}
		let createSection = document.createElement('section');
		createSection.id = `bmeditsection-${rowKey}`;
		createSection.className = 'expeditsection';
		createSection.style = `${inStyle.style}`;
		document.getElementById('show-ruleeditsection').appendChild(createSection);
		template = `
		<div class="slds-form slds-p-around_small">
			<form name="dtbmselection_${rowKey}">
			<label class="slds-form-element__label"> ${mod_alert_arr.LBL_MAP} </label>
			<div class="slds-form-element__control slds-input-has-fixed-addon">
				<input id="bmapid_${rowKey}" name="bmapid_${rowKey}" class="slds-input" type="hidden" value="${mapId}">
				<input id="bmapid_${rowKey}_display" class="slds-input" name="bmapid_${rowKey}_display" readonly="" style="border:1px solid #bababa;" type="text" value="" onclick="return window.open('index.php?module=cbMap&action=Popup&html=Popup_picker&form=dtbmselection_${rowKey}&forfield=bmapid_${rowKey}&srcmodule=GlobalVariable'+SpecialSearch, 'vtlibui10wf', cbPopupWindowSettings);">
				<span class="slds-form-element__addon">
					<button type="image" id="clear-btn-${rowKey}" class="slds-button" alt="${mod_alert_arr.LBL_CLEAR}" title="${mod_alert_arr.LBL_CLEAR}" onClick="this.form.bmapid_${rowKey}.value=''; this.form.bmapid_${rowKey}_display.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
						<svg class="slds-icon slds-icon_small slds-icon-text-light" aria-hidden="true" >
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use> 
						</svg>
					</button>
				</span>
			</div>
			</form>
		</div>`;
		document.getElementById(`bmeditsection-${rowKey}`).innerHTML = template;
		if (preLoadMap.mapid) {
			fetch(
				'index.php?module=cbMap&action=cbMapAjax&actionname=dtactions&method=getEntityName',
				{
					method: 'post',
					headers: {
						'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
					},
					credentials: 'same-origin',
					body: '&'+csrfMagicName+'='+csrfMagicToken+'&MapID='+preLoadMap.mapid
				}
			).then(response => response.json()).then(response => {
				if (response) {
					document.getElementById(`bmapid_${rowKey}_display`).value = response;
				}
			});
		}
	}
	if (value == 'expression') {
		if (document.getElementById(`expeditsection-${rowKey}`)) {
			return;
		}
		let expVal = '';
		if (preLoadMap.expression) {
			expVal = preLoadMap.expression;
		}
		let createSection = document.createElement('section');
		createSection.id = `expeditsection-${rowKey}`;
		createSection.className = 'expeditsection';
		createSection.style = `${inStyle.style}`;
		document.getElementById('show-ruleeditsection').appendChild(createSection);
		const template = `
			<div class="slds-p-around_x-small slds-grid slds-gutters">
				<div class="slds-col slds-size_1-of-2 slds-text-align_left">
					<h2 class="slds-expression__title">${mod_alert_arr.LBL_EXPRESSION}</h2>
				</div>
				<div class="slds-col slds-size_1-of-2 slds-text-align_right">
					<button class="slds-button slds-button_neutral" id="function-btn-${rowKey}" onclick="openFunctionSelection('exptextarea-${rowKey}');">
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
						</svg>
						${mod_alert_arr.LBL_FUNCTIONNAME}
					</button>
				</div>
			</div>
			<div class="slds-p-around_x-small slds-grid slds-gutters">
				<div class="slds-col slds-size_1-of-1 slds-text-align_left">
					<textarea id="exptextarea-${rowKey}" class="slds-textarea">${expVal}</textarea>
				</div>
			</div>`;
		document.getElementById(`expeditsection-${rowKey}`).innerHTML = template;
	}

	if (value == 'decisiontable') {
		if (document.getElementById(`dteditsection-${rowKey}`)) {
			return;
		}
		let module = '';
		let orderby = '';
		let output = '';
		let searches;
		let conditions;
		if (preLoadMap.module) {
			module = preLoadMap.module;
			orderby = preLoadMap.orderby;
			output = preLoadMap.output;
			searches = preLoadMap.searches;
			conditions = preLoadMap.conditions;
		}
		let orderbyObj = orderby.split(',');
		let orderbyTemplate = await generatePill('showOrderByFields', rowKey, orderbyObj, module);
		let outputObj = output.split(',');
		let outputTemplate = await generatePill('showReturnFields', rowKey, outputObj, module);
		//init new array for each instance of grid
		condGroup[rowKey] = new Array();
		srchData[rowKey] = new Array();
		condgridInstance[rowKey] = new Array();
		srchgridInstance[rowKey] = new Array();
		const MapID = document.getElementById('MapID').value;
		let createSection = document.createElement('section');
		createSection.id = `dteditsection-${rowKey}`;
		createSection.className = 'expeditsection';
		createSection.style = `${inStyle.style}`;
		document.getElementById('show-ruleeditsection').appendChild(createSection);
		fetch(
			'index.php?module=cbMap&action=cbMapAjax&actionname=dtactions&method=getModuleValues',
			{
				method: 'post',
				headers: {
					'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
				},
				credentials: 'same-origin',
				body: '&'+csrfMagicName+'='+csrfMagicToken+'&uitype=1613&MapID='+MapID
			}
		).then(response => response.json()).then(response => {
			let moduleList = new Array();
			for (let i in response) {
				const objList = {
					'module': response[i][1],
					'label': response[i][0],
					'selected': response[i][2]
				};
				moduleList.push(objList);
			}
			const moduleListObj = moduleList.sort((a, b) => (a.label > b.label) ? 1 : -1);
			let template = `
			<div class="slds-p-around_small">
				<legend class="slds-form-element__legend slds-form-element__label">${mod_alert_arr.LBL_MODULE}</legend>
				<div class="slds-form-element__control">
					<div class="slds-select_container">
						<select id="dtmodule-${rowKey}" required name="dtmodule-${rowKey}" class="slds-select" onchange="getDTModuleFields(this.value, ${rowKey});updateFieldList(${rowKey})">`;
			for (let i in moduleListObj) {
				let selected = '';
				if (moduleListObj[i].module == module ) {
					selected = 'selected';
				}
				template += `<option value="${moduleListObj[i].module}" ${selected}>${moduleListObj[i].label}</option>`;
			}
			template += `
						</select>
					</div>
				</div>
				<legend class="slds-form-element__legend slds-form-element__label">${mod_alert_arr.LBL_RETURN_FIELDS}</legend>
				<div class="slds-form-element__control">
					<div class="slds-input_container">
						<input type="hidden" id="returnfields-${rowKey}" required name="returnfields-${rowKey}" class="slds-input" value="${output}">
						<div class="slds-pill_container" id="loadFields-return-${rowKey}" onclick="loadFields('showReturnFields-${rowKey}', ${rowKey})" style="cursor: text;">
							<ul class="slds-listbox slds-listbox_horizontal" id="setreturnfields-${rowKey}" role="listbox" aria-orientation="horizontal">
								${outputTemplate}
							</ul>
						</div>
						<span class="closeList" id="showReturnFields-${rowKey}"></span>
					</div>
				</div>
				<legend class="slds-form-element__legend slds-form-element__label">${mod_alert_arr.LBL_ORDERBYCOLUMN}</legend>
				<div class="slds-form-element__control">
					<div class="slds-input_container">
						<input type="hidden" id="orderbyrule-${rowKey}" required name="orderbyrule-${rowKey}" class="slds-input" value="${orderby}">
						<div class="slds-pill_container" id="loadFields-order-${rowKey}" onclick="loadFields('showOrderByFields-${rowKey}', ${rowKey})" style="cursor: text;">
							<ul id="setorderbyrule-${rowKey}" class="slds-listbox slds-listbox_horizontal" role="listbox" aria-orientation="horizontal">
								${orderbyTemplate}
							</ul>
						</div>
						<span class="closeList" id="showOrderByFields-${rowKey}"></span>
					</div>
				</div>
			</div>

			<div class="slds-tabs_default">
			<ul class="slds-tabs_default__nav" role="tablist">
				<li class="slds-tabs_default__item" title="${mod_alert_arr.LBL_SEARCH}" role="presentation" id="tabsearchli${rowKey}" onclick="setActiveDTTab('tabsearch', 'tabconditions', ${rowKey});">
				<a class="slds-tabs_default__link" href="javascript:void(0);" role="tab">
					<span class="slds-tabs__left-icon">
					<span class="slds-icon_container slds-icon-standard-case" title="${mod_alert_arr.LBL_SEARCH}">
						<svg class="slds-icon slds-icon_small" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#search"></use>
						</svg>
					</span>
					</span>${mod_alert_arr.LBL_SEARCH}</a>
				</li>
				<li class="slds-tabs_default__item slds-is-active slds-has-focus" title="${mod_alert_arr.LBL_CONDITIONS}" role="presentation" id="tabconditionsli${rowKey}" onclick="setActiveDTTab('tabconditions', 'tabsearch', ${rowKey});">
				<a class="slds-tabs_default__link" href="javascript:void(0);" role="tab">
					<span class="slds-tabs__left-icon">
					<span class="slds-icon_container slds-icon-standard-opportunity" title="${mod_alert_arr.LBL_CONDITIONS}">
						<svg class="slds-icon slds-icon_small" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#filter"></use>
						</svg>
					</span>
					</span>${mod_alert_arr.LBL_CONDITIONS}</a>
				</li>
			</ul>
			</div>
			<div id="tabconditions-${rowKey}" class="slds-show">
				<div class="slds-p-around_x-small slds-grid slds-gutters">
					<div class="slds-col slds-size_1-of-2 slds-text-align_left">
						<h2 class="slds-expression__title">${mod_alert_arr.LBL_CONDITIONS}</h2>
					</div>
					<div class="slds-col slds-size_1-of-2 slds-text-align_right">
						<button class="slds-button slds-button_neutral" id="appendEmptyFieldRow-condition-${rowKey}" onclick="appendEmptyFieldRow('condition', ${rowKey}); event.stopPropagation();">
							<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
							</svg>
							${mod_alert_arr.LBL_ADDCONDITION}
						</button>
						<button class="slds-button slds-button_text-destructive slds-float_right" type="button" id='delfield_button-condition-${rowKey}' onclick="deleteCondRow(${rowKey}); event.stopPropagation();">
							<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
							</svg>
							${mod_alert_arr.LBL_DELETE}
						</button>
					</div>
				</div>

				<div class="slds-p-around_small">
					<div class="slds-page-header__meta-text slds-m-left_x-small" id="condgrid-${rowKey}" style="width:99%;"></div>
				</div>
			</div>
			<div id="tabsearch-${rowKey}">
				<div class="slds-p-around_x-small slds-grid slds-gutters">
					<div class="slds-col slds-size_1-of-2 slds-text-align_left">
						<h2 class="slds-expression__title">${mod_alert_arr.LBL_SEARCH}</h2>
					</div>
					<div class="slds-col slds-size_1-of-2 slds-text-align_right">
						<button class="slds-button slds-button_neutral" id="appendEmptyFieldRow-search-${rowKey}" onclick="appendEmptyFieldRow('search', ${rowKey}); event.stopPropagation();">
							<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
							</svg>
							${mod_alert_arr.LBL_ADDSEARCH}
						</button>
						<button class="slds-button slds-button_text-destructive slds-float_right" type="button" id='delfield_button-search-${rowKey}' onclick="deleteSrchRow(${rowKey}); event.stopPropagation();">
							<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
							</svg>
							${mod_alert_arr.LBL_DELETE}
						</button>
					</div>
				</div>

				<div class="slds-p-around_small">
					<div class="slds-page-header__meta-text slds-m-left_x-small" id="srchgrid-${rowKey}" style="width:99%;"></div>
				</div>
			</div>`;
			document.getElementById(`dteditsection-${rowKey}`).innerHTML = template;
			searchGrid(rowKey, srchData, searches);
			condGrid(rowKey, condGroup, conditions);
		});
	}
}
/**
 * Generate pill
 * @param {string} type
 * @param {int} rowKey
 * @param {object} data
 */
async function generatePill(type, rowKey, data, module) {
	let template = '';
	for (let i in data) {
		if (data[i] != '') {
			FIELD = await getFieldLabel(data[i], module);
			template += `
			<li class="slds-listbox-item" role="presentation" id="${type}-${rowKey}-${data[i]}">
				<span class="slds-pill" role="option" aria-selected="true">
				<span class="slds-pill__label" title="${data[i]}">${FIELD}</span>
				<span class="slds-icon_container slds-pill__remove" id="remove-${type}-${rowKey}-${data[i]}" onclick="removeField(this, ${rowKey}, '${data[i]}')" style="cursor: pointer">
					<svg class="slds-icon slds-icon_x-small slds-icon-text-default" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
					</svg>
				</span>
				</span>
			</li>`;
		}
	}
	return template;
}

async function getFieldLabel(field, module) {
	return await fetch(
		'index.php?module=cbMap&action=cbMapAjax&actionname=mapactions&method=getFieldLabel',
		{
			method: 'post',
			headers: {
				'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			credentials: 'same-origin',
			body: '&'+csrfMagicName+'='+csrfMagicToken+'&fieldsmodule='+module+'&field='+field
		}
	).then(response => response.json()).then(response => {
		return response;
	});
}
/**
 * Initialize search grid instance
 * @param {int} rowKey
 * @param {object} srchData
 * @param {object} searches
 */
function searchGrid(rowKey, srchData, searches = '') {
	if (searches != '') {
		if (searches[1] == undefined) {
			if (Object.keys(searches.field).length == 0) {
				searches.field = '';
			}
			srchData[rowKey].push(searches);
		} else {
			for (let s in searches) {
				if (Object.keys(searches[s].field).length == 0) {
					searches[s].field = '';
				}
				srchData[rowKey].push(searches[s]);
			}
		}
	}
	srchgrid = [
		{
			name: 'input',
			header: mod_alert_arr.LBL_INPUT,
			editor: 'text',
			sortable: false,
			onAfterChange(ev) {
				const idx = srchgridInstance[rowKey].getIndexOfRow(ev.rowKey);
				updatesrchData(idx, 'input', ev.value, rowKey);
			}
		},
		{
			name: 'operation',
			header: mod_alert_arr.LBL_OPERATION,
			formatter: 'listItemText',
			editor: {
				type: 'select',
				options: {
					listItems: [
						{text: mod_alert_arr.LBL_EQUALS, value: 'e'},
						{text: mod_alert_arr.LBL_NOTEQUAL, value: 'n'},
						{text: mod_alert_arr.LBL_STARTSWITH, value: 's'},
						{text: mod_alert_arr.LBL_ENDSWITH, value: 'ew'},
						{text: mod_alert_arr.LBL_CONTAINS, value: 'c'},
						{text: mod_alert_arr.LBL_DOESNOTCONTAIN, value: 'k'},
						{text: mod_alert_arr.LBL_LESSTHAN, value: 'l'},
						{text: mod_alert_arr.LBL_BEFORE, value: 'b'},
						{text: mod_alert_arr.LBL_GREATERTHAN, value: 'g'},
						{text: mod_alert_arr.LBL_AFTER, value: 'a'},
						{text: mod_alert_arr.LBL_LESSOREQUAL, value: 'm'},
						{text: mod_alert_arr.LBL_GREATEROREQUAL, value: 'h'},
						{text: mod_alert_arr.LBL_NULL, value: 'y'},
						{text: mod_alert_arr.LBL_NOTNULL, value: 'ny'},
						{text: mod_alert_arr.LBL_BETWEENTWODATES, value: 'bw'},
					]
				}
			},
			whiteSpace: 'normal',
			sortable: false,
			onAfterChange(ev) {
				const idx = srchgridInstance[rowKey].getIndexOfRow(ev.rowKey);
				updatesrchData(idx, 'operation', ev.value, rowKey);
			}
		},
		{
			name: 'field',
			header: mod_alert_arr.LBL_FIELDVALUE,
			formatter: 'listItemText',
			editor: {
				type: 'select',
				options: {
					listItems: fieldList
				}
			},
			whiteSpace: 'normal',
			width: 250,
			sortable: false,
			onAfterChange(ev) {
				const idx = srchgridInstance[rowKey].getIndexOfRow(ev.rowKey);
				updatesrchData(idx, 'field', ev.value, rowKey);
			}
		},
		{
			name: 'preprocess',
			header: mod_alert_arr.LBL_EXPRESSION,
			editor: 'text',
			whiteSpace: 'normal',
			width: 250,
			sortable: false,
			onAfterChange(ev) {
				const idx = srchgridInstance[rowKey].getIndexOfRow(ev.rowKey);
				updatesrchData(idx, 'preprocess', ev.value, rowKey);
			}
		},
	];
	srchgridInstance[rowKey] = new tuiGrid({
		el: document.getElementById(`srchgrid-${rowKey}`),
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
		data: srchData[rowKey],
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
		},
		onGridMounted: (ev) => {
			const hideTab = document.getElementById(`tabsearch-${rowKey}`);
			hideTab.className = 'slds-hide';
		}
	});
	return srchgridInstance;
}
/**
 * Initialize condition grid instance
 * @param {int} rowKey
 * @param {object} condGroup
 * @param {object} conditions
 */
function condGrid(rowKey, condGroup = '', conditions = '') {
	if (conditions != '') {
		if (conditions[1] == undefined) {
			if (Object.keys(conditions.field).length == 0) {
				conditions.field = '';
			}
			condGroup[rowKey].push(conditions);
		} else {
			for (let c in conditions) {
				if (Object.keys(conditions[c].field).length == 0) {
					conditions[c].field = '';
				}
				condGroup[rowKey].push(conditions[c]);
			}
		}
	}
	condgrid = [
		{
			name: 'input',
			header: mod_alert_arr.LBL_INPUT,
			editor: 'text',
			sortable: false,
			onAfterChange(ev) {
				const idx = condgridInstance[rowKey].getIndexOfRow(ev.rowKey);
				updatecondData(idx, 'input', ev.value, rowKey);
			}
		},
		{
			name: 'operation',
			header: mod_alert_arr.LBL_OPERATION,
			formatter: 'listItemText',
			editor: {
				type: 'select',
				options: {
					listItems: [
						{text: mod_alert_arr.LBL_EQUALS, value: 'e'},
						{text: mod_alert_arr.LBL_NOTEQUAL, value: 'n'},
						{text: mod_alert_arr.LBL_STARTSWITH, value: 's'},
						{text: mod_alert_arr.LBL_ENDSWITH, value: 'ew'},
						{text: mod_alert_arr.LBL_CONTAINS, value: 'c'},
						{text: mod_alert_arr.LBL_DOESNOTCONTAIN, value: 'k'},
						{text: mod_alert_arr.LBL_LESSTHAN, value: 'l'},
						{text: mod_alert_arr.LBL_BEFORE, value: 'b'},
						{text: mod_alert_arr.LBL_GREATERTHAN, value: 'g'},
						{text: mod_alert_arr.LBL_AFTER, value: 'a'},
						{text: mod_alert_arr.LBL_LESSOREQUAL, value: 'm'},
						{text: mod_alert_arr.LBL_GREATEROREQUAL, value: 'h'},
						{text: mod_alert_arr.LBL_NULL, value: 'y'},
						{text: mod_alert_arr.LBL_NOTNULL, value: 'ny'},
						{text: mod_alert_arr.LBL_BETWEENTWODATES, value: 'bw'},
					]
				}
			},
			whiteSpace: 'normal',
			sortable: false,
			onAfterChange(ev) {
				const idx = condgridInstance[rowKey].getIndexOfRow(ev.rowKey);
				updatecondData(idx, 'operation', ev.value, rowKey);
			}
		},
		{
			name: 'field',
			header: mod_alert_arr.LBL_FIELDVALUE,
			formatter: 'listItemText',
			editor: {
				type: 'select',
				options: {
					listItems: fieldList
				}
			},
			whiteSpace: 'normal',
			width: 250,
			sortable: false,
			onAfterChange(ev) {
				const idx = condgridInstance[rowKey].getIndexOfRow(ev.rowKey);
				updatecondData(idx, 'field', ev.value, rowKey);
			}
		},
	];

	condgridInstance[rowKey] = new tuiGrid({
		el: document.getElementById(`condgrid-${rowKey}`),
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
		data: condGroup[rowKey],
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
	return condgridInstance;
}
/**
 * Read and load map in UI
 */
function preLoadMap() {
	if (DecisionTableMap.decision) {
		DecisionTableMap = DecisionTableMap.decision;
	}
	hitpolicy = DecisionTableMap.hitPolicy;
	document.getElementById('hitpolicy').value = hitpolicy;
	if (hitpolicy == 'G') {
		changeHitPolicy(hitpolicy);
		document.getElementById('aggregate').value = DecisionTableMap.aggregate;
	} else if (hitpolicy == undefined) {
		document.getElementById('hitpolicy').value = 'U';
	}
	if (!DecisionTableMap) {
		return;
	}

	const rules = DecisionTableMap.rules.rule;
	let ruletype = '';
	if (rules[1] == undefined) {
		if (rules.expression) {
			ruletype = 'expression';
		} else if (rules.mapid) {
			ruletype = 'businessmap';
		} else if (rules.decisionTable) {
			ruletype = 'decisiontable';
		}
		const rule = {
			'sequence': rules.sequence,
			'ruletype': ruletype,
			'output': rules.output,
		};
		ruleData.push(rule);
		rulegridInstance.appendRow(rule);
		if (ruletype != '') {
			setRows(ruletype, rules);
		}
	} else {
		for (let r in rules) {
			if (rules[r].expression) {
				ruletype = 'expression';
			} else if (rules[r].mapid) {
				ruletype = 'businessmap';
			} else if (rules[r].decisionTable) {
				ruletype = 'decisiontable';
			} else {
				ruletype = '';
			}
			const rule = {
				'sequence': rules[r].sequence,
				'ruletype': ruletype,
				'output': rules[r].output,
			};
			ruleData.push(rule);
			rulegridInstance.appendRow(rule);
			if (ruletype != '') {
				setRows(ruletype, rules[r]);
			}
		}
	}
}
/**
 * Generate new rows in UI
 * @param {string} ruletype
 * @param {object} rules
 */
function setRows(ruletype, rules) {
	if (ruletype == 'expression') {
		const ev = {
			rowKey: parseInt(rules.sequence) - 1,
			value: 'expression',
			prevValue: 'expression',
		};
		const values = {
			expression: rules.expression
		};
		generateSection(ev, values);
	} else if (ruletype == 'businessmap') {
		const ev = {
			rowKey: parseInt(rules.sequence) - 1,
			value: 'businessmap',
			prevValue: 'businessmap',
		};
		const values = {
			mapid: rules.mapid
		};
		generateSection(ev, values);
	} else {
		const ev = {
			rowKey: parseInt(rules.sequence) - 1,
			value: 'decisiontable',
			prevValue: 'decisiontable',
		};
		const values = {
			module: rules.decisionTable.module,
			orderby: Object.keys(rules.decisionTable.orderby).length == 0 ? '' : rules.decisionTable.orderby+',',
			output: Object.keys(rules.decisionTable.output).length == 0 ? '' : rules.decisionTable.output+',',
			searches: rules.decisionTable.searches.search.condition,
			conditions: rules.decisionTable.conditions == undefined ? '' : rules.decisionTable.conditions.condition,
		};
		generateSection(ev, values);
		updateFieldList(rules.sequence, values.module);
	}
}

async function updateFieldList(rowKey, module = '') {
	fieldList.length = 0;
	if (module == '') {
		module = document.getElementById(`dtmodule-${rowKey}`).value;
	}
	DTModuleFields = await getFieldsForModule(module);
	for (let i in DTModuleFields) {
		const fieldObj = {
			text: DTModuleFields[i], value: i
		};
		fieldList.push(fieldObj);
	}
}