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
let lvmodule = gVTModule;
let PageSize = 20;
let lvtuiGrid = tui.Grid;
let lvdataGridInstance = Array();
let SearchColumns = 0;
let ListViewCopy = 0;
let Application_Filter_All_Edit = 1;
let DocumentFolderView = 1;
let lastPage = sessionStorage.getItem(lvmodule+'_lastPage');
let urlParams = new URLSearchParams(window.location.search);
GlobalVariable_getVariable('Application_ListView_PageSize', 20, lvmodule, '').then(function (response) {
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
document.addEventListener('DOMContentLoaded', function () {
	ListView.loader('show');
	ListView.Show();
}, false);

const ListView = {

	Request: async (url, method, body = {}) => {
		let headers = {
			'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
		};
		const options = {
			method: method,
			credentials: 'same-origin',
			headers: headers
		}
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
	Show: (actionType = false, urlstring = '', searchtype = '', idIns = 1) => {
		if (document.getElementById('curmodule') != undefined) {
			lvmodule = document.getElementById('curmodule').value;
		}
		if (!lastPage) {
			lastPage = 1;
		}
		let url = `${defaultURL}&functiontocall=listViewJSON&formodule=${lvmodule}&lastPage=${lastPage}`;
		if (actionType == 'filter') {
			document.getElementById('basicsearchcolumns').innerHTML = '';
			document.basicSearch.search_text.value = '';
			ListView.Filter(url);
			document.getElementById('status').style.display = 'none';
		} else if (actionType == 'search') {
			ListView.Search(url, urlstring, searchtype, idIns);
			document.getElementById('status').style.display = 'none';
		} else if (actionType == 'alphabetic') {
			ListView.ListViewAlpha(urlstring);
			document.getElementById('status').style.display = 'none';
		} else if (actionType == 'massedit') {
			//use this function to reload data in every change
			ListView.Reload(idIns, lastPage, true);
			document.getElementById('status').style.display = 'none';
		} else if (actionType == 'RecycleBin') {
			lvdataGridInstance[idIns].destroy();
			const select_module = document.getElementById('select_module').value;
			url = `${defaultURL}&functiontocall=listViewJSON&formodule=${select_module}&lastPage=${lastPage}&isRecycleModule=true`;
			ListView.loader('show');
			ListView.Default(select_module, url);
			ListView.RenderFilter(url);
			ListView.updateData(idIns);
		} else {
			if (lvmodule != '' && lvmodule != undefined && lvmodule != 'RecycleBin') {
				if (lvmodule == 'Documents' && DocumentFolderView == 1) {
					DocumentsView.Show(url);
				} else {
					ListView.Default(lvmodule, url);
				}
			} else if (lvmodule == 'RecycleBin') {
				const select_module = document.getElementById('select_module').value;
				url = `${defaultURL}&functiontocall=listViewJSON&formodule=${select_module}&lastPage=${lastPage}&isRecycleModule=true`;
				ListView.Default(select_module, url);
			}
		}
		const content = document.getElementsByClassName('tui-grid-content-area');
		if (lvmodule == '') {
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
	getColumnHeaders: (headerObj, idIns = 1) => {
		let res = [];
		let header = {};
		let filter = {};
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
			if (ListView.deniedMods().includes(lvmodule) || lvmodule == '') {
				edit = false;
			}
			if (edit) {
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
				if (SearchColumns == 0) {
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
						sortable: true,
						formatter: formatter,
						editor: editor,
						filter: filter,
						whiteSpace: 'normal',
						onAfterChange(ev) {
							const idx = lvdataGridInstance[idIns].getIndexOfRow(ev.rowKey);
							const referenceField = lvdataGridInstance[idIns].getValue(idx, 'reference_field');
							ListView.updateFieldData(ev, idx, idIns);
						},
						renderer: {
							type: LinkRender,
							options: {
								tooltip: tooltip
							}
						},
					};
					if ((fieldname == 'modifiedtime' || fieldname == 'modifiedby') && (lvmodule == '' || lvmodule == 'RecycleBin')) {
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
						sortable: true,
						formatter: formatter,
						editor: editor,
						whiteSpace: 'normal',
						onAfterChange(ev) {
							const idx = lvdataGridInstance[idIns].getIndexOfRow(ev.rowKey);
							const referenceField = lvdataGridInstance[idIns].getValue(idx, 'reference_field');
							ListView.updateFieldData(ev, idx, idIns);
						},
						renderer: {
							type: LinkRender,
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
		} else if (uitype == '10' || uitype == '4' || fieldname == 'createdtime' || fieldname == 'modifiedtime') {
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
	Default: (module, url, idIns=1) => {
		ListView.Request(`${url}&columns=true`, 'get').then(function(response) {
			const advft_criteria = urlParams.get('advft_criteria');
			const advft_criteria_groups = urlParams.get('advft_criteria_groups');
			const searchtype = urlParams.get('searchtype');
			if (advft_criteria != null && searchtype == null) {
				url += `&search=${advft_criteria}&advft_criteria_groups=${advft_criteria_groups}&searchtype=${searchtype}`;
			}
			let headers = ListView.getColumnHeaders(response[0]);
			let filters = response[1];
			ListView.setFilters(filters);
			lvdataGridInstance[idIns] = new lvtuiGrid({
				el: document.getElementById('listview-tui-grid'),
				columns: headers,
				rowHeaders: [{
					type: 'checkbox',
					header: `
						<label for="all-checkbox" class="checkbox">
							<input type="checkbox" id="selectCurrentPageRec" class="listview-checkbox" onclick="toggleSelect_ListView(this.checked,'selected_id[]');ListView.getCheckedRows('currentPage', this, ${idIns});" name="_checked" />
						</label>`,
					renderer: {
						type: CheckboxRender,
						options: {
							idIns: idIns
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
					const lastPage = lvdataGridInstance[idIns].getPagination()._currentPage;
					sessionStorage.setItem(module+'_lastPage', lastPage);
					ListView.updateData(idIns);
					const rows = document.getElementById('allselectedboxes').value;
					if (rows != '') {
						ListView.checkRows();
					}
					lvdataGridInstance[idIns].setRequestParams({
						'fromPagination': true
					});
				}
			});
			ListView.loader('hide');
			ListView.noData(idIns);
			ListView.registerEvent(url);
			tui.Grid.applyTheme('striped');
		});
	},
	/**
	 * Register a grid event
	 * @param {String} url
	 */
	registerEvent: (url, idIns=1) => {
		lvdataGridInstance[idIns].on('filter', (ev) => {
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
			ListView.Search(url, urlstring, searchtype, idIns);
		});
		lvdataGridInstance[idIns].on('click', (ev) => {
			if (ev.nativeEvent.target.innerText == 'Clear') {
				ListView.Reload(idIns);
			}
		});
		lvdataGridInstance[idIns].on('successResponse', function (data) {
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
	},
	/**
	 * Get the new headers in a onchange search
	 * @param {String} url
	 * @param {String} urlstring
	 * @param {String} searchtype
	 */
	Search: (url, urlstring, searchtype, idIns=1) => {
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
		if (lvdataGridInstance[idIns]) {
			lvdataGridInstance[idIns].clear();
			lvdataGridInstance[idIns].setRequestParams({
				'search': urlstring,
				'searchtype': searchtype
			});
			//update pagination onchange
			lvdataGridInstance[idIns].setPerPage(parseInt(PageSize));
			ListView.updateData(idIns);
			ListView.noData(idIns);
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
			lvdataGridInstance[idIns].clear();
			lvdataGridInstance[idIns].setRequestParams({'search': url, 'searchtype': 'Basic'});
			lvdataGridInstance[idIns].on('successResponse', function (data) {
				const res = JSON.parse(data.xhr.response);
				const export_where = res.export_where;
				if (export_where && lvmodule != '') {
					document.getElementsByName('where_export')[0].value = export_where;
				}
			});
			//update pagination onchange
			lvdataGridInstance[idIns].setPerPage(parseInt(PageSize));
			ListView.updateData(idIns);
			ListView.noData(idIns);
		}
	},
	/**
	 * Get the new headers in a onchange data
	 */
	Reload: (idIns=1, lastPage = 1, reload = true) => {
		if (lvmodule == 'Documents' && DocumentFolderView == 1) {
			DocumentsView.Reload(lastPage, reload);
			return false;
		}
		lvdataGridInstance[idIns].clear();
		if (reload) {
			lvdataGridInstance[idIns].setRequestParams({'search': '', 'searchtype': '', 'page': lastPage});
		} else {
			lvdataGridInstance[idIns].setRequestParams({'search': '', 'searchtype': ''});
		}
		document.getElementsByName('search_text')[0].value = '';
		//update pagination onchange
		if (reload) {
			lvdataGridInstance[idIns].setPerPage(parseInt(PageSize));
		}
		const content = document.getElementsByClassName('tui-grid-content-area');
		if (lvmodule == '') {
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
		ListView.updateData(idIns);
	},
	/**
	 * Get the new headers in a onchange filter
	 * @param {String} url
	 */
	Filter: (url, idIns=1) => {
		lvdataGridInstance[idIns].setRequestParams({'search': '', 'searchtype': ''});
		lvdataGridInstance[idIns].clear();
		ListView.Request(`${url}&columns=true`, 'get').then(function(response) {
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
			lvdataGridInstance[idIns].setColumns(headers);
			ListView.noData(idIns);
		});
		ListView.updateData(idIns);
		lvdataGridInstance[idIns].setPerPage(parseInt(PageSize));
	},
	/**
	 * Get columns for RecycleBin filter
	 * @param {String} url
	 */
	RenderFilter: (url) => {
		ListView.Request(`${url}&columns=true`, 'get').then(function(response) {
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
	 * Get all checked rows
	 * @param {Object} type
	 * @param {String} el
	 */
	getAllCheckedRows: (type, el) => {
		let checkboxes = document.getElementsByName('selected_id[]');
		let checkboxesChecked = [];
		if (type == 'currentPage') {
			for (let i = 0; i < checkboxes.length; i++) {
				if (el != '' && el.checked == true) {
					checkboxesChecked.push(checkboxes[i].id);
				} else {
					checkboxesChecked = [];
				}
			}
		} else {
			for (let i = 0; i < checkboxes.length; i++) {
				if (checkboxes[i].checked) {
					checkboxesChecked.push(checkboxes[i].id);
				}
			}
		}
		return checkboxesChecked;
	},
	/**
	 * Get all checked rows to delete them
	 * @param {Object} type
	 * @param {String} el
	 */
	getCheckedRows: (type, el = '', idIns=1) => {
		let checkedRows = ListView.getAllCheckedRows(type, el);
		let ids = [];
		let rowKeys = [];
		//add checked rows for current page
		for (let id in checkedRows) {
			let recordId = lvdataGridInstance[idIns].getValue(parseInt(checkedRows[id]), 'recordid');
			ids.push(recordId);
			rowKeys.push(checkedRows[id]);
		}
		let actualVal = document.getElementById('allselectedboxes');
		let	select_options = ids.join(';');
		//get checked rows and add new rows from other pages
		if (!select_options.endsWith(';') && select_options != '') {
			select_options += ';';
		}
		select_options += actualVal.value;
		let actualArr = select_options.split(';');
		let newIds = [];
		for (var index in actualArr) {
			if (!newIds.includes(actualArr[index])) {
				newIds.push(actualArr[index]);
			}
		}
		select_options = newIds.join(';');
		if (!select_options.endsWith(';') && select_options != '') {
			select_options += ';';
		}
		//remove id for current unchecked row
		if (!el.checked) {
			let removeId = el.id;
			let recordId = lvdataGridInstance[idIns].getValue(parseInt(removeId), 'recordid');
			select_options = select_options.replace(recordId+';', '');
		}
		//remove all ids for current page if header checkbox is unchecked
		if (checkedRows.length == 0) {
			for (let i = 0; i < PageSize; i++) {
				let recordId = lvdataGridInstance[idIns].getValue(parseInt(i), 'recordid');
				select_options = select_options.replace(recordId+';', '');
			}
		}
		document.getElementById('allselectedboxes').value = select_options;
		if (select_options.indexOf('on;') !== -1) {
			document.getElementById('allselectedboxes').value = select_options.slice(0, -3);
		}
		return rowKeys;
	},
	/**
	 * Remove all checked rows
	 * @param {String} selectedType
	 */
	removeRows: (selectedType = '', idIns=1) => {
		lvdataGridInstance[idIns].reloadData();
		lvdataGridInstance[idIns].removeCheckedRows();
		document.getElementById('status').style.display = 'none';
		if (selectedType == 'all') {
			document.getElementById('gridRecordCountHeader').innerHTML = '';
			document.getElementById('gridRecordCountFooter').innerHTML = '';
			document.getElementById('numOfRows').value = '';
			document.getElementById('linkForSelectAll').style.display = 'none';
		} else {
			ListView.updateData(idIns);
			document.getElementById('linkForSelectAll').style.display = 'none';
		}
	},
	/**
	 * Update data in every change
	 */
	updateData: (idIns=1) => {
		if (lvmodule == 'Documents' && DocumentFolderView == 1) {
			return false;
		}
		if (Object.keys(lvdataGridInstance[idIns]).length == 0) {
			document.getElementById('gridRecordCountHeader').innerHTML = '';
			document.getElementById('gridRecordCountFooter').innerHTML = '';
			return 0;
		}
		const gridInstance = lvdataGridInstance[idIns].store.data.pageOptions;
		const page = gridInstance.page;
		const totalCount = gridInstance.totalCount;
		const currentPageSize = lvdataGridInstance[idIns].getRowCount();
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
		if (lvmodule == '') {
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
		select.setAttribute('onchange', 'showDefaultCustomView(this, "'+lvmodule+'", "")');
		select.innerHTML = filters.customview_html;
		if (document.getElementById('filterOptions') !== null) {
			document.getElementById('filterOptions').appendChild(select);
		}

		//create filterActions
		let fedit = document.createElement('span');
		let edit_query = {
			'module': lvmodule,
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
			document.getElementById('filterEditActions').appendChild(fedit);
		}
		//delete a filter
		let fdelete = document.createElement('span');
		edit_query = {
			'module': 'CustomView',
			'action': 'Delete',
			'dmodule': lvmodule,
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
			document.getElementById('filterDeleteActions').appendChild(fdelete);
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
	checkRows: (idIns=1) => {
		let actualVal = document.getElementById('allselectedboxes');
		let idsArr = actualVal.value.split(';');
		for (let i = 0; i <= PageSize; i++) {
			let recordId = lvdataGridInstance[idIns].getValue(parseInt(i), 'recordid');
			if (idsArr.includes(recordId)) {
				document.getElementById(i).checked = true;
			}
		}
	},
	/**
	 * Update values in listview
	 * @param {Object} ev
	 * @param {String|Number} idx
	 */
	updateFieldData: (ev, idx, idIns=1) => {
		const recordid = lvdataGridInstance[idIns].getValue(idx, 'recordid');
		const columnName = ev.columnName;
		const value = ev.value;
		const preValue = ev.preValue;
		if (value != preValue) {
			jQuery.ajax({
				method: 'POST',
				url: `${defaultURL}&functiontocall=listViewJSON&method=updateDataListView`,
				data: {
					modulename: lvmodule,
					value: value,
					columnName: columnName,
					recordid: recordid,
				}
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
			ListView.Request(`${tooltipUrl}&returnarray=true`, 'get').then(function(response) {
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
	noData: (idIns=1) => {
		const nr_records = lvdataGridInstance[idIns].store.data.rawData.length;
		if (nr_records == 0) {
			let nr = 0;
			let index = 0;
			for (let currentIdx in lvdataGridInstance) {
				if (currentIdx == idIns) {
					index = nr;
					break;
				}
				nr++;
			}
			const url = `${defaultURL}&functiontocall=checkButton&formodule=${lvmodule}`;
			ListView.Request(url, 'get').then(function(response) {
				const no_data_template = document.getElementsByClassName('tui-grid-layer-state-content')[index];
				const grid_template = document.getElementsByClassName('tui-grid-content-area')[index];
				const mod_label = document.getElementsByClassName('hdrLink')[0].innerText;
				grid_template.style.height = '240px';
				let create_template = '';
				let import_template = '';
				if (response.CreateView == 'yes' && lvmodule != '') {
					create_template = `
					<a href="index.php?module=${lvmodule}&action=EditView&return_action=DetailView">
						<button class="slds-button slds-button_neutral">${alert_arr.LBL_CREATE} ${mod_label}</button>
					</a>`;
				}
				if (response.Import == 'yes' && lvmodule != '') {
					import_template = `
					<a class="slds-card__footer-action" href="index.php?module=${lvmodule}&action=Import&step=1&return_module=${lvmodule}&return_action=ListView">
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
		const url = `${defaultURL}&functiontocall=getRecordActions&formodule=${lvmodule}&recordid=${recordid}`;
		ListView.Request(url, 'get').then(function(response) {
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
	loadedTooltips: []
};


const DocumentsView = {

	Show: (url) => {
		ListView.Request(`${url}&columns=true`, 'get').then(function(response) {
			const childNames = Object.keys(response[0]).map((key) => response[0][key].fieldname);
			let filters = response[1];
			let folders = response[2];
			ListView.setFilters(filters);
			for (let id in folders) {
				const lastPage = sessionStorage.getItem(`Documents_${folders[id][0]}_lastPage`);
				let headers = ListView.getColumnHeaders(response[0], folders[id][0]);
				lvdataGridInstance[folders[id][0]] = new lvtuiGrid({
					el: document.getElementById('listview-tui-grid'),
					columns: headers,
					rowHeaders: [{
						type: 'checkbox',
						header: `
							<label for="all-checkbox" class="checkbox">
								<input type="checkbox" id="currentPageRec_selectall${folders[id][0]}" class="listview-checkbox" onclick="toggleSelect_ListView(this.checked,'selected_id${folders[id][0]}', 'selectall${folders[id][0]}');ListView.getCheckedRows('currentPage', this, ${folders[id][0]});" name="_checked" />
							</label>`,
						renderer: {
							type: CheckboxRender,
							options: {
								idIns: folders[id][0]
							}
						}
					}],
					data: {
						api: {
							readData: {
								url: `${url}&perPage=${PageSize}&folderid=${folders[id][0]}&lastPage=${lastPage}`,
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
						valign: 'top',
						height: 70,
						complexColumns: [{
							header: folders[id][1],
							name: 'basic',
							childNames: childNames
						}]
					},
					onGridUpdated: (ev) => {
						const lastPage = lvdataGridInstance[folders[id][0]].getPagination()._currentPage;
						sessionStorage.setItem(`Documents_${folders[id][0]}_lastPage`, lastPage);
						ListView.updateData(folders[id][0]);
						const rows = document.getElementById('allselectedboxes').value;
						if (rows != '') {
							ListView.checkRows(folders[id][0]);
						}
						lvdataGridInstance[folders[id][0]].setRequestParams({
							'fromPagination': true
						});
					}
				});
				ListView.loader('hide');
				ListView.registerEvent(url, folders[id][0]);
				tui.Grid.applyTheme('striped');
			}
		});
	},

	Search: (urlstring, searchtype) => {
		for (let ins in lvdataGridInstance) {
			ListView.Show('search', urlstring, searchtype, ins);
		}
	},

	Reload: (lastPage=1, reload=true) => {
		for (let idIns in lvdataGridInstance) {
			lvdataGridInstance[idIns].clear();
			if (reload) {
				lvdataGridInstance[idIns].setRequestParams({'search': '', 'searchtype': '', 'page': lastPage});
			} else {
				lvdataGridInstance[idIns].setRequestParams({'search': '', 'searchtype': ''});
			}
			document.getElementsByName('search_text')[0].value = '';
			if (reload) {
				lvdataGridInstance[idIns].setPerPage(parseInt(PageSize));
			}
			ListView.updateData(idIns);
		}
	}
};