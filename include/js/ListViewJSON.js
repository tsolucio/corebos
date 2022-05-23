/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
* granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************/
loadJS('index.php?module=cbQuestion&action=cbQuestionAjax&file=getjslanguage');
const defaultURL = 'index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions';
let PageSize = 20;
let lvtuiGrid = tui.Grid;
let lvdataGridInstance = Array();
let SearchColumns = 0;
let ListViewCopy = 0;
let Application_Filter_All_Edit = 1;
let DocumentFolderView = 1;
let Application_MassAction_Multipage = 0;
let lastPage = sessionStorage.getItem(gVTModule+'_lastPage');
let urlParams = new URLSearchParams(window.location.search);
GlobalVariable_getVariable('Application_ListView_PageSize', 20, gVTModule, '').then(function (response) {
	let obj = JSON.parse(response);
	PageSize = obj.Application_ListView_PageSize;
});
GlobalVariable_getVariable('Application_ListView_SearchColumns', 0).then(function (response) {
	let obj = JSON.parse(response);
	SearchColumns = obj.Application_ListView_SearchColumns;
});
GlobalVariable_getVariable('Application_Filter_All_Edit', 1).then(function (response) {
	let obj = JSON.parse(response);
	Application_Filter_All_Edit = obj.Application_Filter_All_Edit;
});
GlobalVariable_getVariable('Document_Folder_View', 1).then(function (response) {
	let obj = JSON.parse(response);
	DocumentFolderView = obj.Document_Folder_View;
});
GlobalVariable_getVariable('Application_MassAction_Multipage', 0).then(function (response) {
	let obj = JSON.parse(response);
	Application_MassAction_Multipage = obj.Application_MassAction_Multipage;
});
document.addEventListener('DOMContentLoaded', function () {
	ListView.loader('show');
	ListView.Show();
}, false);

const ListView = {

	Action: '',
	Module: gVTModule,
	Instance: 1,
	SearchParams: false,
	CheckedRows: [],
	RelationRows: [],
	RelatedModule: '',

	Request: async (url, method, body = {}) => {
		let headers = {
			'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
		};
		const options = {
			method: method,
			credentials: 'same-origin',
			headers: headers
		};
		if (method == 'post') {
			headers['Content-Type'] = 'application/json';
			options.body = JSON.stringify(body);
		}
		const response = await fetch(url, options);
		return response.json();
	},

	/**
	 * Load the grid in default view
	 * @param {Boolean} actionType
	 * @param {String} urlstring
	 * @param {String} searchtype
	 */
	Show: (actionType = false, urlstring = '', searchtype = '') => {
		ListView.Action = actionType;
		if (document.getElementById('curmodule') != undefined) {
			ListView.Module = document.getElementById('curmodule').value;
		}
		if (!lastPage) {
			lastPage = 1;
		}
		let url = `${defaultURL}&functiontocall=listViewJSON&formodule=${ListView.Module}&lastPage=${lastPage}`;
		if (actionType == 'filter') {
			document.getElementById('basicsearchcolumns').innerHTML = '';
			document.basicSearch.search_text.value = '';
			if (ListView.Module == 'Documents' && DocumentFolderView == 1) {
				DocumentsView.Show(url, urlstring);
			} else {
				ListView.Filter(url, urlstring);
			}
			document.getElementById('status').style.display = 'none';
		} else if (actionType == 'search') {
			ListView.Search(url, urlstring, searchtype);
			document.getElementById('status').style.display = 'none';
		} else if (actionType == 'alphabetic') {
			ListView.ListViewAlpha(urlstring);
			document.getElementById('status').style.display = 'none';
		} else if (actionType == 'massedit') {
			//use this function to reload data in every change
			lastPage = sessionStorage.getItem(ListView.Module+'_lastPage');
			ListView.Reload(lastPage, true);
			document.getElementById('status').style.display = 'none';
		} else if (actionType == 'RecycleBin') {
			lvdataGridInstance[ListView.Instance].destroy();
			const select_module = document.getElementById('select_module').value;
			url = `${defaultURL}&functiontocall=listViewJSON&formodule=${select_module}&lastPage=${lastPage}&isRecycleModule=true`;
			ListView.Module = select_module;
			ListView.loader('show');
			ListView.Default(url);
			ListView.RenderFilter(url);
			ListView.updateData();
		} else {
			if (ListView.Module != '' && ListView.Module != undefined && ListView.Module != 'RecycleBin') {
				if (ListView.Module == 'Documents' && DocumentFolderView == 1) {
					DocumentsView.Show(url);
				} else {
					ListView.Default(url);
				}
			} else if (ListView.Module == 'RecycleBin') {
				const select_module = document.getElementById('select_module').value;
				url = `${defaultURL}&functiontocall=listViewJSON&formodule=${select_module}&lastPage=${lastPage}&isRecycleModule=true`;
				ListView.Module = select_module;
				ListView.Default(url);
			}
		}
		const content = document.getElementsByClassName('tui-grid-content-area');
		if (ListView.Module == '') {
			const contentArea = setInterval(function () {
				if (content[0]) {
					content[0].style.height = 'auto';
					clearInterval(contentArea);
				}
			}, 100);
		} else {
			if (content[0]) {
				content[0].style.height = 'auto';
			}
		}
	},
	/**
	 * List of modules that can't edit in listview
	 */
	deniedMods: () => {
		return ['cbupdater'];
	},
	/**
	 * Get all headers for table
	 * @param {Object} headerObj
	 */
	getColumnHeaders: (headerObj) => {
		let res = [];
		let header = {};
		let filter = {};
		let sortable = true;
		for (let index in headerObj) {
			const fieldname = headerObj[index].fieldname;
			const fieldvalue = headerObj[index].fieldvalue;
			const uitype = headerObj[index].uitype;
			const tooltip = headerObj[index].tooltip;
			let edit = headerObj[index].edit;
			let editor;
			let formatter;
			let values = {};
			if (uitype == '15' || uitype == '16' || uitype == '52' || uitype == '53') {
				values = headerObj[index].picklist;
			}
			if (ListView.deniedMods().includes(ListView.Module) || ListView.Module == '') {
				edit = false;
			}
			if (DocumentFolderView == 1 && ListView.Module == 'Documents') {
				edit = false;
				sortable = false;
			}
			if (edit) {
				if (uitype == 10) {
					ListView.RelatedModule = headerObj[index].relatedModule;
				}
				editor = ListView.getEditorType(uitype, values, fieldname);
			} else {
				editor = false;
			}
			if (fieldname == 'cblvactioncolumn') {
				header = {
					name: fieldname,
					header: ' ',
					sortable: false,
					whiteSpace: 'normal',
					width: 40,
					renderer: {
						type: ActionRender,
					},
				};
			} else {
				if (SearchColumns == 1) {
					if (uitype == '7' || uitype == '9' || uitype == '71' || uitype == '72') {
						filter = {
							type: 'number',
							showApplyBtn: true,
							showClearBtn: true
						};
					} else if (uitype == '5' || uitype == '50' || uitype == '70') {
						filter = {
							type: 'date',
							showApplyBtn: true,
							showClearBtn: true
						};
					} else {
						filter = {
							type: 'text',
							showApplyBtn: true,
							showClearBtn: true
						};
					}
					if (uitype == '15' || uitype == '52' || uitype == '53' || uitype == '56') {
						if (edit) {
							formatter = 'listItemText';
						} else {
							formatter = false;
						}
					} else {
						formatter = false;
					}
					header = {
						name: fieldname,
						header: fieldvalue,
						sortingType: 'desc',
						sortable: sortable,
						formatter: formatter,
						editor: editor,
						filter: filter,
						whiteSpace: 'normal',
						onAfterChange(ev) {
							const idx = lvdataGridInstance[ListView.Instance].getIndexOfRow(ev.rowKey);
							ListView.updateFieldData(ev, idx);
						},
						renderer: {
							type: LinkRender,
							options: {
								tooltip: tooltip
							}
						},
					};
					if ((fieldname == 'modifiedtime' || fieldname == 'modifiedby') && (ListView.Module == '' || ListView.Module == 'RecycleBin')) {
						header = {
							name: fieldname,
							header: fieldvalue,
							sortable: false,
							whiteSpace: 'normal',
							formatter: formatter
						};
					}
				} else {
					header = {
						name: fieldname,
						header: fieldvalue,
						sortingType: 'desc',
						sortable: sortable,
						formatter: formatter,
						editor: editor,
						whiteSpace: 'normal',
						onAfterChange(ev) {
							const idx = lvdataGridInstance[ListView.Instance].getIndexOfRow(ev.rowKey);
							ListView.updateFieldData(ev, idx);
						},
						renderer: {
							type: LinkRender,
							options: {
								tooltip: tooltip
							}
						},
					};
				}
			}
			res.push(header);
		}
		return res;
	},
	/**
	 * Enable editor in listview
	 * @param {Number} uitype
	 * @param {Object} values
	 */
	getEditorType: (uitype, values, fieldname) => {
		const disableEditor = ['4', '1024', '1025', 'createdtime', 'modifiedtime'];
		if (uitype == '56') {
			editor =  {
				type: 'radio',
				options: {
					listItems: [
						{ text: alert_arr.YES, value: '1' },
						{ text: alert_arr.NO, value: '0' },
					]
				}
			};
		} else if (uitype == '10') {
			editor = {
				type: UIType10Editor,
				options: {
					customTextEditorOptions: {
						relatedModule: ListView.RelatedModule
					}
				}
			};
		} else if (disableEditor.includes(uitype)) {
			editor = false;
		} else if (uitype == '15' || uitype == '16') {
			let listItems = [];
			for (let f in values) {
				let listValues = {};
				listValues = {
					text: values[f].label,
					value: values[f].value
				};
				listItems.push(listValues);
			}
			editor = {
				type: 'select',
				options: {
					listItems: listItems
				}
			};
		} else if (uitype == '50') {
			editor = {
				type: 'datePicker',
				options: {
					format: userDateFormat.replace(/m/g, 'M')+' HH:mm A',
					timepicker: true
				}
			};
		} else if (uitype == '5') {
			editor = {
				type: 'datePicker',
				options: {
					format: userDateFormat.replace(/m/g, 'M')
				}
			};
		} else if (uitype == '53') {
			let listItems = [];
			for (let f in values) {
				let listValues = {
					text: values[f],
					value: f,
				};
				listItems.push(listValues);
			}
			editor = {
				type: 'select',
				options: {
					listItems: listItems
				}
			};
		} else {
			editor = 'text';
		}
		return editor;
	},
	/**
	 * Load the default view in the first time
	 * @param {String} module
	 * @param {String} url
	 */
	Default: (url) => {
		ListView.Request(`${url}&columns=true`, 'get').then(function (response) {
			const advft_criteria = urlParams.get('advft_criteria');
			const advft_criteria_groups = urlParams.get('advft_criteria_groups');
			const searchtype = urlParams.get('searchtype');
			if (advft_criteria != null && searchtype == null) {
				url += `&search=${advft_criteria}&advft_criteria_groups=${advft_criteria_groups}&searchtype=${searchtype}`;
			}
			let headers = ListView.getColumnHeaders(response[0]);
			let filters = response[1];
			ListView.setFilters(filters);
			lvdataGridInstance[ListView.Instance] = new lvtuiGrid({
				el: document.getElementById('listview-tui-grid'),
				columns: headers,
				rowHeaders: [{
					type: 'checkbox',
					header: `
						<label for="all-checkbox" class="checkbox">
							<input type="checkbox" id="selectCurrentPageRec" class="listview-checkbox" onclick="toggleSelect_ListView(this.checked,'selected_id[]');" name="_checked" />
						</label>`,
					renderer: {
						type: CheckboxRender,
						options: {
							idIns: ListView.Instance
						}
					}
				}],
				data: {
					api: {
						readData: {
							url: url,
							method: 'GET'
						}
					}
				},
				useClientSort: false,
				pageOptions: {
					perPage: PageSize
				},
				rowHeight: 'auto',
				bodyHeight: 'auto',
				scrollX: false,
				scrollY: false,
				columnOptions: {
					resizable: true
				},
				header: {
					align: 'left',
					valign: 'top'
				},
				onGridUpdated: (ev) => {
					const lastPage = lvdataGridInstance[ListView.Instance].getPagination()._currentPage;
					sessionStorage.setItem(ListView.Module+'_lastPage', lastPage);
					ListView.updateData();
					const rows = document.getElementById('allselectedboxes').value;
					if (rows != '' && ListView.Action != 'massedit') {
						ListView.checkRows();
					}
					let RequestParams = {
						'fromInstance': true
					};
					if (ListView.SearchParams) {
						RequestParams.search = ListView.SearchParams.search;
						RequestParams.searchtype = ListView.SearchParams.searchtype;
					}
					lvdataGridInstance[ListView.Instance].setRequestParams(RequestParams);
				}
			});
			ListView.loader('hide');
			ListView.noData();
			ListView.registerEvent(url);
			tui.Grid.applyTheme('striped');
		});
	},
	/**
	 * Register a grid event
	 * @param {String} url
	 */
	registerEvent: (url, instance = 1) => {
		lvdataGridInstance[instance].on('filter', (ev) => {
			const operatorData = {
				eq: 'e',
				contain: 'c',
				ne: 'n',
				start: 's',
				ls: 'l',
				gt: 'g',
				lte: 'm',
				gte: 'h',
				after: 'a',
				afterEq: 'h',
				before: 'b',
				beforeEq: 'm',
			};
			const operator = operatorData[ev.filterState[0].state[0]['code']];
			const urlstring = `&query=true&search_field=${ev.columnName}&search_text=${ev.filterState[0].state[0]['value']}&searchtype=BasicSearch&operator=${operator}`;
			const searchtype = 'Basic';
			ListView.Search(url, urlstring, searchtype);
		});
		lvdataGridInstance[instance].on('click', (ev) => {
			if (ev.nativeEvent.target.innerText == 'Clear') {
				ListView.Reload();
			}
		});
		lvdataGridInstance[instance].on('successResponse', function (data) {
			const filteredData = document.getElementById('filteredData');
			const res = JSON.parse(data.xhr.response);
			const search_mode = res.search_mode;
			if (search_mode) {
				filteredData.innerHTML = `
				<span class="slds-badge slds-theme_success">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info"></use>
					</svg>
					${alert_arr.filterApplied}
				</span>`;
			} else {
				filteredData.innerHTML = '';
			}
		});
		lvdataGridInstance[instance].on('checkAll', (ev) => {
			const checkedRows = lvdataGridInstance[instance].getCheckedRowKeys();
			ListView.getCheckedRows(checkedRows, 'check', instance, ev);
		});
		lvdataGridInstance[instance].on('check', (ev) => {
			const checkedRows = lvdataGridInstance[instance].getCheckedRowKeys();
			ListView.getCheckedRows(checkedRows, 'check', instance, ev);
		});
		lvdataGridInstance[instance].on('uncheckAll', (ev) => {
			const checkedRows = lvdataGridInstance[instance].getCheckedRowKeys();
			ListView.getCheckedRows(checkedRows, 'uncheck', instance, ev);
		});
		lvdataGridInstance[instance].on('uncheck', (ev) => {
			const checkedRows = lvdataGridInstance[instance].getCheckedRowKeys();
			ListView.getCheckedRows(checkedRows, 'uncheck', instance, ev);
		});
	},
	/**
	 * Get the new headers in a onchange search
	 * @param {String} url
	 * @param {String} urlstring
	 * @param {String} searchtype
	 */
	Search: (url, urlstring, searchtype) => {
		//set search_url value to input
		if (searchtype == 'Basic') {
			const parseUrl = urlstring.split('&');
			let urlArr = [];
			for (var arg in parseUrl) {
				const URI = parseUrl[arg].split('=');
				urlArr[URI[0]] = decodeURI(URI[1]);
			}
			document.getElementById('search_url').value = `&query=true&search_field=${urlArr['search_field']}&search_text=${urlArr['search_text']}&searchtype=BasicSearch`;
		} else {
			document.getElementById('search_url').value = urlstring + '&query=true';
		}
		if (lvdataGridInstance[ListView.Instance]) {
			lvdataGridInstance[ListView.Instance].clear();
			ListView.SearchParams = {
				'search': urlstring,
				'searchtype': searchtype,
				'fromInstance': true
			};
			lvdataGridInstance[ListView.Instance].setRequestParams(ListView.SearchParams);
			//update pagination onchange
			if (ListView.Module == 'Documents' && DocumentFolderView == 1) {
				lvdataGridInstance[ListView.Instance].reloadData();
				lvdataGridInstance[ListView.Instance].setPerPage(parseInt(PageSize));
			} else {
				lvdataGridInstance[ListView.Instance].setPerPage(parseInt(PageSize));
				ListView.noData();
			}
			ListView.updateData();
		}
	},
	/**
	 * Get results for alphabetic search
	 * @param {String} url
	 */
	ListViewAlpha: (url) => {
		//set search_url value to input
		const parseUrl = url.split('&');
		let urlArr = [];
		for (let arg in parseUrl) {
			const URI = parseUrl[arg].split('=');
			urlArr[URI[0]] = URI[1];
		}
		document.getElementById('search_url').value = `&query=true&search_field=${urlArr['search_field']}&search_text=${urlArr['search_text']}&searchtype=BasicSearch&type=alpbt&operator=${urlArr['operator']}`;
		for (let idIns in lvdataGridInstance) {
			ListView.Instance = idIns;
			lvdataGridInstance[ListView.Instance].clear();
			ListView.SearchParams = {
				'search': url,
				'searchtype': 'Basic',
				'fromInstance': true
			};
			lvdataGridInstance[ListView.Instance].setRequestParams(ListView.SearchParams);
			lvdataGridInstance[ListView.Instance].on('successResponse', function (data) {
				const res = JSON.parse(data.xhr.response);
				const export_where = res.export_where;
				if (export_where && ListView.Module != '') {
					document.getElementsByName('where_export')[0].value = export_where;
				}
			});
			//update pagination onchange
			if (ListView.Module == 'Documents' && DocumentFolderView == 1) {
				lvdataGridInstance[ListView.Instance].reloadData();
			} else {
				lvdataGridInstance[ListView.Instance].setPerPage(parseInt(PageSize));
			}
			ListView.updateData();
			ListView.noData();
		}
	},
	/**
	 * Get the new headers in a onchange data
	 */
	Reload: (lastPage = 1, reload = true) => {
		ListView.SearchParams = false;
		if (ListView.Module == 'Documents' && DocumentFolderView == 1) {
			DocumentsView.Reload(lastPage, reload);
			return false;
		}
		lvdataGridInstance[ListView.Instance].clear();
		let RequestParams = {
			'search': '',
			'searchtype': '',
		};
		if (reload) {
			if (ListView.Action == 'massedit' || ListView.Action == 'inlineedit') {
				RequestParams.lastPage = lastPage;
			} else {
				RequestParams.page = lastPage;
				RequestParams.fromInstance = true;
			}
			lvdataGridInstance[ListView.Instance].setRequestParams(RequestParams);
		} else {
			RequestParams.fromInstance = true;
			lvdataGridInstance[ListView.Instance].setRequestParams(RequestParams);
		}
		document.getElementsByName('search_text')[0].value = '';
		//update pagination onchange
		if (reload) {
			lvdataGridInstance[ListView.Instance].setPerPage(parseInt(PageSize));
		}
		const content = document.getElementsByClassName('tui-grid-content-area');
		if (ListView.Module == '') {
			const contentArea = setInterval(function () {
				if (content[0]) {
					content[0].style.height = 'auto';
					clearInterval(contentArea);
				}
			}, 100);
		} else {
			if (content[0]) {
				content[0].style.height = 'auto';
			}
		}
		ListView.updateData();
		if (ListView.Action == 'massedit') {
			document.getElementById('allselectedboxes').value = '';
			document.getElementById('idstring').value = '';
			document.getElementsByName('massedit_recordids')[0].value = '';
			ListView.CheckedRows = [];
			ListView.Action = '';
		}
	},
	/**
	 * Get the new headers in a onchange filter
	 * @param {String} url
	 */
	Filter: (url, viewname) => {
		lvdataGridInstance[ListView.Instance].setRequestParams({'search': '', 'searchtype': ''});
		lvdataGridInstance[ListView.Instance].clear();
		ListView.Request(`${url}&columns=true&viewname=${viewname}`, 'get').then(function (response) {
			let headers = ListView.getColumnHeaders(response[0]);
			let filters = response[1];
			//update options for basic search
			document.getElementById('bas_searchfield').innerHTML = '';
			for (let h in headers) {
				if (headers[h]['name'] != 'cblvactioncolumn') {
					let option = document.createElement('option');
					option.innerHTML = headers[h]['header'];
					option.value = headers[h]['name'];
					document.getElementById('bas_searchfield').appendChild(option);
				}
			}
			ListView.setFilters(filters, true);
			lvdataGridInstance[ListView.Instance].setColumns(headers);
			ListView.updateData();
			lvdataGridInstance[ListView.Instance].setPerPage(parseInt(PageSize));
		});
	},
	/**
	 * Get columns for RecycleBin filter
	 * @param {String} url
	 */
	RenderFilter: (url) => {
		ListView.Request(`${url}&columns=true`, 'get').then(function (response) {
			let headers = ListView.getColumnHeaders(response[0]);
			document.getElementById('bas_searchfield').innerHTML = '';
			for (let h in headers) {
				if (headers[h]['name'] != 'cblvactioncolumn') {
					let option = document.createElement('option');
					option.innerHTML = headers[h]['header'];
					option.value = headers[h]['name'];
					document.getElementById('bas_searchfield').appendChild(option);
				}
			}
		});
	},
	/**
	 * Get all checked rows to delete them
	 * @param {Object} type
	 * @param {String} el
	 */
	getCheckedRows: (checkedRows, event, instance = 1, ev) => {
		let currentRows = [];
		let relationRows = [];
		let	select_options = '';
		ListView.Instance = instance;
		const selectCurrentPageRec = document.getElementById('selectCurrentPageRec');
		if (Application_MassAction_Multipage == 0 || (DocumentFolderView == 1 && ListView.Module == 'Documents')) {
			for (let id in checkedRows) {
				let recordId = lvdataGridInstance[ListView.Instance].getValue(parseInt(checkedRows[id]), 'recordid');
				let parent = lvdataGridInstance[ListView.Instance].getValue(parseInt(checkedRows[id]), 'parent');
				if (!recordId.includes('parent_')) {
					currentRows.push(recordId);
				}
				relationRows.push(`${parent}__${recordId}`);
			}
			ListView.CheckedRows[instance] = currentRows.filter(Number);
			ListView.CheckedRows.map(function (currentValue, index, arr) {
				if (select_options != '') {
					select_options += ';';
				}
				select_options += currentValue.join(';');
			});
			ListView.RelationRows[instance] = relationRows;
		} else {
			let recordId = lvdataGridInstance[ListView.Instance].getValue(parseInt(ev.rowKey), 'recordid');
			if (event == 'uncheck' || event == 'uncheckAll') {
				if (checkedRows.length == 0) {
					for (let i = 0; i < PageSize; i++) {
						recordId = lvdataGridInstance[ListView.Instance].getValue(parseInt(i), 'recordid');
						const idx = ListView.CheckedRows.indexOf(recordId);
						delete ListView.CheckedRows[idx];
					}
				} else {
					const idx = ListView.CheckedRows.indexOf(recordId);
					delete ListView.CheckedRows[idx];
				}
				selectCurrentPageRec.checked = false;
			} else {
				if (checkedRows.length == PageSize) {
					for (let id in checkedRows) {
						recordId = lvdataGridInstance[ListView.Instance].getValue(parseInt(checkedRows[id]), 'recordid');
						if (!ListView.CheckedRows.includes(recordId)) {
							ListView.CheckedRows.push(recordId);
						}
					}
					selectCurrentPageRec.checked = true;
				} else {
					if (!ListView.CheckedRows.includes(recordId)) {
						ListView.CheckedRows.push(recordId);
					}
				}
			}
			select_options = ListView.CheckedRows.filter(Number).join(';');
		}
		if (!select_options.endsWith(';') && select_options != '') {
			select_options += ';';
		}
		document.getElementById('allselectedboxes').value = select_options;
	},
	/**
	 * Remove all checked rows
	 * @param {String} selectedType
	 */
	removeRows: (selectedType = '') => {
		let RequestParams = {};
		if (ListView.SearchParams) {
			RequestParams.search = ListView.SearchParams.search;
			RequestParams.searchtype = ListView.SearchParams.searchtype;
			lvdataGridInstance[ListView.Instance].setRequestParams(RequestParams);
		}
		lvdataGridInstance[ListView.Instance].reloadData();
		lvdataGridInstance[ListView.Instance].removeCheckedRows();
		document.getElementById('status').style.display = 'none';
		if (selectedType == 'all') {
			document.getElementById('gridRecordCountHeader').innerHTML = '';
			document.getElementById('gridRecordCountFooter').innerHTML = '';
			document.getElementById('numOfRows').value = '';
			document.getElementById('linkForSelectAll').style.display = 'none';
		} else {
			ListView.updateData();
			document.getElementById('linkForSelectAll').style.display = 'none';
		}
	},
	/**
	 * Update data in every change
	 */
	updateData: () => {
		if (ListView.Module == 'Documents' && DocumentFolderView == 1) {
			return false;
		}
		if (Object.keys(lvdataGridInstance[ListView.Instance]).length == 0) {
			document.getElementById('gridRecordCountHeader').innerHTML = '';
			document.getElementById('gridRecordCountFooter').innerHTML = '';
			return 0;
		}
		const gridInstance = lvdataGridInstance[ListView.Instance].store.data.pageOptions;
		const page = gridInstance.page;
		const totalCount = gridInstance.totalCount;
		const currentPageSize = lvdataGridInstance[ListView.Instance].getRowCount();
		const limit_start_rec = (page-1) * PageSize;
		const currentPage = (limit_start_rec + 1) + ' - ' + (limit_start_rec + currentPageSize);
		if (totalCount > 0) {
			document.getElementById('gridRecordCountHeader').innerHTML = alert_arr['LBL_SHOWING'] + currentPage + alert_arr['LBL_RECORDS'] + totalCount;
			document.getElementById('gridRecordCountFooter').innerHTML = alert_arr['LBL_SHOWING'] + currentPage + alert_arr['LBL_RECORDS'] + totalCount;
		} else {
			document.getElementById('gridRecordCountHeader').innerHTML = '';
			document.getElementById('gridRecordCountFooter').innerHTML = '';
		}
		document.getElementById('numOfRows').value = totalCount;
		document.getElementById('count').innerHTML = totalCount;
		return totalCount;
	},
	/**
	 * Update filter action in every change
	 * @param {Object} filters
	 * @param {Boolean} reload
	 */
	setFilters: (filters, reload = false) => {
		if (ListView.Module == '') {
			return;
		}
		if (reload) {
			document.getElementById('filterOptions').innerHTML = '';
			document.getElementById('filterEditActions').innerHTML = '';
			document.getElementById('filterDeleteActions').innerHTML = '';
		}
		let select = document.createElement('select');
		select.id = 'viewname';
		select.name = 'viewname';
		select.className = 'slds-select';
		select.setAttribute('style', 'max-width:240px;');
		select.setAttribute('onchange', 'showDefaultCustomView(this, "'+ListView.Module+'", "")');
		select.innerHTML = filters.customview_html;
		if (document.getElementById('filterOptions') !== null) {
			document.getElementById('filterOptions').innerHTML = '';
			document.getElementById('filterOptions').appendChild(select);
		}

		//create filterActions
		let fedit = document.createElement('span');
		let edit_query = {
			'module': ListView.Module,
			'action': 'CustomView',
			'record': filters.viewid,
			'permitall': 'false'
		};
		if (Application_Filter_All_Edit == 1 && filters.viewinfo.viewname == 'All') {
			edit_query.permitall = 'true';
		}
		let edit_query_string = ListView.encodeQueryData(edit_query);
		edit_query = {};
		if (filters.edit_permit == 'yes' || Application_Filter_All_Edit == 1) {
			fedit.innerHTML = `| <a href="index.php?${edit_query_string}">${alert_arr['LNK_EDIT_ACTION']}</a> |`;
		} else {
			fedit.innerHTML = `| ${alert_arr['LNK_EDIT_ACTION']} |`;
		}
		if (document.getElementById('filterEditActions') !== null) {
			document.getElementById('filterEditActions').innerHTML = '';
			document.getElementById('filterEditActions').appendChild(fedit);
		}
		//delete a filter
		let fdelete = document.createElement('span');
		edit_query = {
			'module': 'CustomView',
			'action': 'Delete',
			'dmodule': ListView.Module,
			'record': filters.viewid,
		};
		edit_query_string = ListView.encodeQueryData(edit_query);
		if (filters.delete_permit == 'yes') {
			fdelete.innerHTML = `
			<a href="javascript:confirmdelete('index.php?${edit_query_string}')">
				${alert_arr['LNK_DELETE_ACTION']}
			</a>`;
		} else {
			fdelete.innerHTML = `${alert_arr['LNK_DELETE_ACTION']}`;
		}
		if (document.getElementById('filterDeleteActions') !== null) {
			document.getElementById('filterDeleteActions').innerHTML = '';
			document.getElementById('filterDeleteActions').appendChild(fdelete);
		}
		let fpublic = document.createElement('span');
		if (filters.setpublic.ChangedStatus != '') {
			fpublic.innerHTML = `|
			<a id="customstatus_id" onclick="ChangeCustomViewStatus(${filters.viewid}, ${filters.setpublic.Status}, ${filters.setpublic.ChangedStatus}, '${ListView.Module}')">
				${filters.setpublic.Label}
			</a>`;
		}
		if (document.getElementById('filterPublicActions') !== null) {
			document.getElementById('filterPublicActions').innerHTML = '';
			document.getElementById('filterPublicActions').appendChild(fpublic);
		}
	},
	/**
	 * Build query
	 */
	encodeQueryData: (data) => {
		const ret = [];
		for (let d in data) {
			ret.push(encodeURIComponent(d) + '=' + encodeURIComponent(data[d]));
		}
		return ret.join('&');
	},
	/**
	 * Check rows in grid
	 */
	checkRows: () => {
		let actualVal = document.getElementById('allselectedboxes');
		let idsArr = actualVal.value.split(';');
		let j = 0;
		for (let i = 0; i <= PageSize; i++) {
			let recordId = lvdataGridInstance[ListView.Instance].getValue(parseInt(i), 'recordid');
			if (idsArr.includes(recordId)) {
				document.getElementById(i).checked = true;
				j++;
			}
		}
		const selectCurrentPageRec = document.getElementById('selectCurrentPageRec');
		if (j == parseInt(PageSize)) {
			selectCurrentPageRec.checked = true;
		} else {
			selectCurrentPageRec.checked = false;
		}
	},
	/**
	 * Update values in listview
	 * @param {Object} ev
	 * @param {String|Number} idx
	 */
	updateFieldData: (ev, idx) => {
		const recordid = lvdataGridInstance[ListView.Instance].getValue(idx, 'recordid');
		const columnName = ev.columnName;
		const value = ev.value;
		const preValue = ev.preValue;
		if (value != preValue) {
			jQuery.ajax({
				method: 'POST',
				url: `${defaultURL}&functiontocall=listViewJSON&method=updateDataListView`,
				data: {
					modulename: ListView.Module,
					value: value,
					columnName: columnName,
					recordid: recordid,
				}
			}).then(function (response) {
				const lastPage = sessionStorage.getItem(gVTModule+'_lastPage');
				ListView.Action = 'inlineedit';
				ListView.Reload(lastPage);
			});
		}
	},
	/**
	 * Show tooltip in Listview
	 * @param {String} recordid
	 * @param {String} fieldname
	 * @param {String} modulename
	 */
	addTooltip: (recordid, fieldname, modulename) => {
		const tooltipUrl = `index.php?module=Tooltip&action=TooltipAjax&file=ComputeTooltip&fieldname=${fieldname}&id=${recordid}&modname=${modulename}&ajax=true&submode=getTooltip`;
		let getId = document.getElementById(`tooltip-${recordid}-${fieldname}`);
		if (!ListView.isTooltipLoaded(recordid)) {
			ListView.loadedTooltips.push(recordid);
			ListView.Request(`${tooltipUrl}&returnarray=true`, 'get').then(function (response) {
				if (getId != null) {
					getId.remove();
				}
				let body = '';
				let width = '';
				for (let label in response) {
					if (label == 'ModComments') {
						width = 'width:700px';
					} else {
						body += `
							<dl class="slds-list_horizontal slds-p-bottom_x-small">
								<dt class="slds-item_label slds-text-color_weak slds-truncate">
									<strong>${label}:</strong>
								</dt>
								<dd class="slds-item_detail slds-truncate">${response[label]}</dd>
							</dl>`;
					}
				}
				const el = `
				<div class="cbds-tooltip__wrapper--inner">
					<section class="slds-popover slds-nubbin_bottom" role="dialog" style="${width};">
						<header class="slds-popover__header" style="background: #1589ee;color: white">
							<div class="slds-media slds-media_center slds-has-flexi-truncate">
							<div class="slds-media__figure">
								<span class="slds-icon_container slds-icon-utility-error">
									<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#preview"></use>
									</svg>
								</span>
							</div>
							<div class="slds-media__body">
								<h2 class="slds-truncate slds-text-heading_medium" title="${alert_arr.QuickView}">${alert_arr.QuickView}</h2>
							</div>
							</div>
						</header>
						<div class="slds-popover__body">
							${body}
						</div>
					</section>
				</div>`;
				const createEl = document.createElement('div');
				createEl.id = `tooltip-${recordid}-${fieldname}`;
				createEl.classList.add('cbds-tooltip__wrapper');
				createEl.innerHTML = el;
				document.getElementById(`cbds-tooltip__trigger-${recordid}-${fieldname}`).appendChild(createEl);
			});
		}
	},
	/**
	 * Show No Data message in listview
	 */
	noData: () => {
		const nr_records = lvdataGridInstance[ListView.Instance].store.data.rawData.length;
		if (nr_records == 0) {
			let nr = 0;
			let index = 0;
			for (let currentIdx in lvdataGridInstance) {
				if (currentIdx == ListView.Instance) {
					index = nr;
					break;
				}
				nr++;
			}
			const url = `${defaultURL}&functiontocall=checkButton&formodule=${ListView.Module}`;
			ListView.Request(url, 'get').then(function (response) {
				const no_data_template = document.getElementsByClassName('tui-grid-layer-state-content')[index];
				const grid_template = document.getElementsByClassName('tui-grid-content-area')[index];
				const mod_label = document.getElementsByClassName('hdrLink')[0].innerText;
				grid_template.style.height = '240px';
				let create_template = '';
				let import_template = '';
				if (response.CreateView == 'yes' && ListView.Module != '' && gVTModule != 'RecycleBin') {
					create_template = `
					<a href="index.php?module=${ListView.Module}&action=EditView&return_action=DetailView">
						<button class="slds-button slds-button_neutral">${alert_arr.LBL_CREATE} ${mod_label}</button>
					</a>`;
				}
				if (response.Import == 'yes' && ListView.Module != '' && gVTModule != 'RecycleBin') {
					import_template = `
					<a class="slds-card__footer-action" href="index.php?module=${ListView.Module}&action=Import&step=1&return_module=${ListView.Module}&return_action=ListView">
						${alert_arr.LBL_IMPORT} ${mod_label}
					</a>`;
				}
				no_data_template.innerHTML = `
				<article class="slds-card" style="width: 40%;margin-left: auto;margin-right: auto;">
					<div class="slds-card__header slds-grid">
						<header class="slds-media slds-media_center slds-has-flexi-truncate">
							<div class="slds-media__figure">
								<span class="slds-icon_container slds-icon-standard-record-create">
									<svg class="slds-icon slds-icon_small" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#record_create"></use>
									</svg>
								</span>
							</div>
							<div class="slds-media__body">
								<h2 class="slds-card__header-title">
									<span>${alert_arr.LBL_NO_DATA}</span>
								</h2>
							</div>
							<div class="slds-no-flex">
								${create_template}
							</div>
						</header>
					</div>
					<footer class="slds-card__footer">
						${import_template}
					</footer>
				</article>`;
			});
		}
	},
	/**
	 * Render actions onclick for every row in listview
	 * @param {String} recordid
	 */
	RenderActions: (recordid) => {
		if (document.getElementById(`list__${recordid}`) !== null) {
			return false;
		}
		[...document.getElementById('listview-tui-grid').getElementsByClassName('slds-dropdown_right')].forEach(dd => {
			if (dd.id != `dropdown-${recordid}`) {
				dd.classList.remove('slds-is-open');
				findUp(dd, '.tui-grid-cell').classList.remove('tui-grid-cell-has-overflow');
			}
		});
		let currentModule = ListView.Module;
		if (gVTModule == 'RecycleBin') {
			ListView.Module = gVTModule;
		}
		const url = `${defaultURL}&functiontocall=getRecordActions&formodule=${ListView.Module}&recordid=${recordid}`;
		ListView.Request(url, 'get').then(function (response) {
			ListView.Module = currentModule;
			let button_template = `<ul class="slds-dropdown__list" role="menu" id="list__${recordid}">`;
			if (response == true) { //recycle bin module
				const select_module = document.getElementById('select_module').value;
				button_template += `
				<li class="slds-dropdown__item" role="presentation">
					<a onclick="restore(${recordid}, '${select_module}')">
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#refresh"></use>
						</svg>
						${alert_arr.Restore}
					</a>
				</li>
				<li class="slds-dropdown__item" role="presentation">
					<a onclick="callEmptyRecyclebin('${recordid}');">
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
						</svg>
						${alert_arr.LNK_DELETE_ACTION}
					</a>
				</li>`;
			} else {
				const edit_action = response.edit;
				const delete_action = response.delete;
				const view_action = response.view;
				const calendar_action = (response.calendar) ? response.calendar.status : undefined;
				if (calendar_action != undefined) {
					button_template += `
					<li class="slds-dropdown__item" role="presentation">
						<a onclick="ajaxChangeCalendarStatus('${calendar_action}',${recordid});">
							<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
							</svg>
							${alert_arr.LBL_CLOSE_TITLE}
						</a>
					</li>`;
				}
				if (edit_action.edit) {
					button_template += `
					<li class="slds-dropdown__item" role="presentation">
						<a href="${edit_action.link}">
							<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
							</svg>
							${alert_arr.LNK_EDIT_ACTION}
						</a>
					</li>`;
				}
				if (delete_action.delete) {
					button_template += `
					<li class="slds-dropdown__item" role="presentation">
						<a onclick="javascript:confirmdelete('${delete_action.link}');">
							<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
							</svg>
							${alert_arr.LNK_DELETE_ACTION}
						</a>
					</li>`;
				}
				if (view_action.view) {
					button_template += `
					<li class="slds-dropdown__item" role="presentation">
						<div class="
							cbds-color-compl-red--sober
							slds-p-vertical_x-small
							slds-p-horizontal_small
							"
						>
							<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#notification"></use>
							</svg>
							${alert_arr.LBL_MODIFIED}
						</div>
					</li>`;
				}
				button_template += '</ul>';
			}
			let ddWrapper = document.getElementById(`dropdown-${recordid}`);
			ddWrapper.innerHTML = button_template;
			findUp(ddWrapper, '.slds-dropdown-trigger_hover').classList.add('slds-is-open');
			findUp(ddWrapper, '.tui-grid-cell').classList.add('tui-grid-cell-has-overflow');
		});
	},
	/**
	 * Show or hide loader in listview
	 * @param {String} type show/hide
	 */
	loader: (type) => {
		const tuiId = document.getElementById('listview-tui-grid');
		if (type == 'show' && tuiId) {
			const loader = document.createElement('div');
			tuiId.style.height = '400px';
			tuiId.appendChild(loader);
			loader.classList.add('cbds-loader');
			loader.id = 'cbds-loader';
		} else if (type == 'hide' && tuiId) {
			const loader = document.getElementById('cbds-loader');
			tuiId.style.height = 'auto';
			if (loader) {
				loader.remove();
			}
		}
	},
	/**
	 * See if certain recordid already has a loaded tooltip
	 * @param {String} recordid
	 */
	isTooltipLoaded: (recordid) => {
		return ListView.loadedTooltips.indexOf(recordid) == -1 ? false : true;
	},
	/**
	 * Keep track of already loaded tooltips
	 */
	loadedTooltips: [],

	SaveRelatedRows: (recordid, fieldname) => {
		const relatedRow = document.getElementById(`txtbox_${fieldname}_${recordid}`).value;
		if (relatedRow == '') {
			return false;
		}
		jQuery.ajax({
			method: 'POST',
			url: `${defaultURL}&functiontocall=listViewJSON&method=updateDataListView`,
			data: {
				modulename: ListView.Module,
				value: relatedRow,
				columnName: fieldname,
				recordid: recordid,
			}
		}).then(function (response) {
			const lastPage = sessionStorage.getItem(gVTModule+'_lastPage');
			ListView.Action = 'inlineedit';
			ListView.Reload(lastPage);
		});
	}
};


const DocumentsView = {

	Show: (url, viewname = '') => {
		ListView.Request(`${url}&columns=true&viewname=${viewname}`, 'get').then(function (response) {
			const childNames = Object.keys(response[0]).map((key) => response[0][key].fieldname);
			let filters = response[1];
			let folders = response[2];
			ListView.setFilters(filters);
			for (let id in folders) {
				let fldId= folders[id][0];
				if (ListView.Action == 'filter') {
					lvdataGridInstance[fldId].destroy();
				}
				if (folders[id][0] === undefined) {
					fldId = '__empty__';
				}
				ListView.Instance = fldId;
				let lastPage = sessionStorage.getItem(`Documents_${ListView.Instance}_lastPage`);
				if (lastPage == null) {
					lastPage = 1;
				}
				if (ListView.Action == 'search') {
					lastPage = 1;
				}
				let headers = ListView.getColumnHeaders(response[0], ListView.Instance);
				lvdataGridInstance[ListView.Instance] = new lvtuiGrid({
					el: document.getElementById('listview-tui-grid'),
					columns: headers,
					treeColumnOptions: {
						name: childNames[0],
						useCascadingCheckbox: true
					},
					rowHeaders: [{
						type: 'checkbox',
						header: `
							<label for="all-checkbox" class="checkbox">
								<input type="checkbox" id="currentPageRec_selectall${ListView.Instance}" class="listview-checkbox" onclick="toggleSelect_ListView(this.checked,'selected_id${ListView.Instance}', 'selectall${ListView.Instance}');" name="_checked" />
							</label>`,
						renderer: {
							type: CheckboxRender,
							options: {
								idIns: ListView.Instance
							}
						}
					}],
					data: {
						api: {
							readData: {
								url: `${url}&perPage=${PageSize}&folderid=${fldId}&lastPage=${lastPage}`,
								method: 'GET'
							}
						}
					},
					rowHeight: 'auto',
					bodyHeight: 'auto',
					scrollX: false,
					scrollY: false,
					columnOptions: {
						resizable: true
					},
					header: {
						align: 'left',
						valign: 'top',
						height: 70,
						complexColumns: [{
							header: folders[id][1],
							name: 'basic',
							childNames: childNames
						}]
					},
					onGridUpdated: (ev) => {
						ListView.Instance = fldId;
						ListView.updateData(fldId);
						const rows = document.getElementById('allselectedboxes').value;
						if (rows != '' && ListView.Action != 'massedit') {
							ListView.checkRows(fldId);
						}
						let RequestParams = {
							'fromInstance': true
						};
						if (ListView.SearchParams) {
							RequestParams.search = ListView.SearchParams.search;
							RequestParams.searchtype = ListView.SearchParams.searchtype;
						}
						lvdataGridInstance[ListView.Instance].setRequestParams(RequestParams);
					}
				});
				ListView.loader('hide');
				ListView.registerEvent(url, fldId);
				tui.Grid.applyTheme('striped');
			}
		});
	},

	Search: (urlstring, searchtype) => {
		for (let insId in lvdataGridInstance) {
			ListView.Instance = insId;
			ListView.Show('search', urlstring, searchtype);
		}
	},

	Reload: (lastPage=1, reload=true) => {
		for (let idIns in lvdataGridInstance) {
			ListView.Instance = idIns;
			lvdataGridInstance[ListView.Instance].clear();
			if (reload) {
				lvdataGridInstance[ListView.Instance].setRequestParams({'search': '', 'searchtype': '', 'page': lastPage});
			} else {
				lvdataGridInstance[ListView.Instance].setRequestParams({'search': '', 'searchtype': ''});
			}
			document.getElementsByName('search_text')[0].value = '';
			if (reload) {
				lvdataGridInstance[ListView.Instance].reloadData();
			}
			ListView.updateData();
			if (ListView.Action != 'massedit') {
				document.getElementById('allselectedboxes').value = '';
			}
		}
	},

	MoveFile: () => {
		let url = `${defaultURL}&functiontocall=listViewJSON&formodule=${ListView.Module}`;
		let checkedRows = document.getElementById('allselectedboxes').value.split(';');
		checkedRows = checkedRows.filter(Number);
		if (checkedRows.length == 0) {
			ldsPrompt.show(alert_arr.ERROR, alert_arr.SELECT);
			return false;
		}
		ListView.Request(`${url}&columns=true&folders=all`, 'get').then(function (response) {
			let content = `${alert_arr.LBL_NO_DATA}! ${alert_arr.LBL_CREATE}`;
			if (response[2].length > 0) {
				let list = ``;
				response[2].map(function (currentValue, index, arr) {
					list += `
					<li class="slds-item" data-id="${currentValue[0]}" onclick="DocumentsView.Move(this)">
						<a>${currentValue[1]}</a>
					</li>
					`;
				});
				content = `
				<ul class="slds-has-dividers_top slds-has-block-links_space">
					${list}
				</ul>
				`;
			}
			ldsModal.show('Move file', content, 'small');
		});
	},

	Move: (el) => {
		const docid = el.dataset.id;
		let url = '';
		let checkedRows = document.getElementById('allselectedboxes').value.split(';');
		checkedRows = checkedRows.filter(Number);
		ListView.RelationRows.map(function (currentValue, index) {
			for (let i in currentValue) {
				if (!currentValue[i].includes('parent')) {
					const parent = currentValue[i].split('__');
					DocumentsView.RemoveFromRelatedList(parent[1], parent[0]);
				}
			}
		});
		DocumentsView.AddToRelatedList(checkedRows.join(';'), docid);
		DocumentsView.Reload();
		ldsModal.close();
	},

	AddToRelatedList: (entity_id, recordid) => {
		const url = `index.php?module=Documents&destination_module=DocumentFolders&idlist=${entity_id}&parentid=${recordid}`;
		jQuery.ajax({
			method: 'POST',
			url: `${url}&action=DocumentsAjax&file=updateDocumentsRelations&mode=Ajax&actionType=listview`
		});
	},

	RemoveFromRelatedList: (idlist, recordid) => {
		const url = `index.php?module=Documents&destination_module=DocumentFolders&idlist=${idlist}&parentid=${recordid}`;
		jQuery.ajax({
			method: 'POST',
			url: `${url}&action=DocumentsAjax&file=updateDocumentsRelations&mode=delete&actionType=listview`
		});
	},

	SearchDocuments: () => {
		const search_text = document.getElementById('search_text').value;
		if (search_text != '') {
			for (let idIns in lvdataGridInstance) {
				ListView.Instance = idIns;
				lvdataGridInstance[ListView.Instance].clear();
				lvdataGridInstance[ListView.Instance].setRequestParams({
					'search': '',
					'searchtype': '',
					'searchFullDocuments': search_text
				});
				lvdataGridInstance[ListView.Instance].reloadData();
			}
		}
	}
};