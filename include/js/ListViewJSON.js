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
let module = '';
let PageSize = 20;
const tuiGrid = tui.Grid;
let dataGridInstance;
let SearchColumns = 0;
GlobalVariable_getVariable('Application_ListView_PageSize', 20, module, '').then(function (response) {
	let obj = JSON.parse(response);
	PageSize = obj.Application_ListView_PageSize;
});
GlobalVariable_getVariable('Application_ListView_SearchColumns', 0).then(function (response) {
	let obj = JSON.parse(response);
	SearchColumns = obj.Application_ListView_SearchColumns;
});
document.addEventListener('DOMContentLoaded', function () {
	ListView.ListViewJSON();
}, false);

const ListView = {
	/**
	 * Load the grid in default view
	 * @param {Boolean} actionType
	 * @param {String} urlstring
	 * @param {String} searchtype
	 */
	ListViewJSON: (actionType = false, urlstring = '', searchtype = '') => {
		if (document.getElementById('curmodule') != undefined) {
			module = document.getElementById('curmodule').value;
		}
		let lastPage = sessionStorage.getItem(module+'_lastPage');
		if (!lastPage) {
			lastPage = 1;
		}
		let url = 'index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=listViewJSON&formodule='+module+'&lastPage='+lastPage;
		if (actionType == 'filter') {
			document.getElementById('basicsearchcolumns').innerHTML = '';
			document.basicSearch.search_text.value = '';
			ListView.ListViewFilter(url);
			document.getElementById('status').style.display = 'none';
		} else if (actionType == 'search') {
			ListView.ListViewSearch(url, urlstring, searchtype);
			document.getElementById('status').style.display = 'none';
		} else if (actionType == 'alphabetic') {
			ListView.ListViewAlpha(urlstring);
			document.getElementById('status').style.display = 'none';
		} else if (actionType == 'massedit') {
			//use this function to reload data in every change
			ListView.ListViewReloadData();
			document.getElementById('status').style.display = 'none';
		} else {
			if (module != '' && module != undefined) {
				ListView.ListViewDefault(module, url);
			}
		}
	},
	/**
	 * Get all headers for table
	 * @param {Object} headerObj
	 */
	getColumnHeaders: (headerObj) => {
		let res = [];
		let header = {};
		let filter = {};
		for (let index in headerObj) {
			const fieldname = headerObj[index].fieldname;
			const fieldvalue = headerObj[index].fieldvalue;
			const uitype = headerObj[index].uitype;
			let editor;
			let values = {};
			if (uitype == '15' || uitype == '52' || uitype == '53') {
				values = headerObj[index].picklist;
			}
			editor = ListView.getEditorType(uitype, values);
			if (fieldname == 'action') {
				header = {
					name: fieldname,
					header: fieldvalue,
					sortable: false,
					width: 100,
	      		};
	      	} else {
	      		if (SearchColumns == 0) {
		      		if (uitype == '53' || uitype == '56' || uitype == '77') {
		      			filter = {
							type: 'select',
		      			};
		      		} else if (uitype == '7' || uitype == '9' || uitype == '71' || uitype == '72') {
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
					header = {
						name: fieldname,
						header: fieldvalue,
						sortingType: 'desc',
						sortable: true,
						renderer: {
        					type: LinkRender,
        				},
						filter: filter,
						editor: editor,
						copyOptions: {
							useListItemText: true
						},
				        onAfterChange(ev) {
				        	const idx = dataGridInstance.getIndexOfRow(ev.rowKey);
				        	const referenceField = dataGridInstance.getValue(idx, 'reference');
				        	if (fieldname != referenceField) {
				            	ListView.updateFieldData(ev, idx);
				        	}
				        },
					};
	      		} else {
					header = {
						name: fieldname,
						header: fieldvalue,
						sortingType: 'desc',
						sortable: true,
						renderer: {
        					type: LinkRender,
        				},
						editor: editor,
						copyOptions: {
							useListItemText: true
						},
				        onAfterChange(ev) {
				        	const idx = dataGridInstance.getIndexOfRow(ev.rowKey);
				        	const referenceField = dataGridInstance.getValue(idx, 'reference');
				        	if (fieldname != referenceField) {
				            	ListView.updateFieldData(ev, idx);
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
	getEditorType: (uitype, values) => {
		if (uitype == '56') {
			editor =  {
				type: 'radio',
	            options: {
	              listItems: [
	                { text: 'Yes', value: '1' },
	                { text: 'No', value: '0' },
	              ]
	            }
	        };
		} else if (uitype == '10') {
			editor = false;
		} else if (uitype == '15') {
			let listItems = [];
			for (let f in values) {
				let listValues = {};
				listValues = {
					text: values[f],
					value: values[f]
				};
				listItems.push(listValues);
			}
       	 	editor = {
            	type: 'select',
	            options: {
	            	listItems: listItems
	            }
	        };
		} else if (uitype == '50' || uitype == '5' || uitype == '70') {
			editor = {
	            type: 'datePicker'
	        };
		} else if (uitype == '52' || uitype == '53') {
			let listItems = [];
			for (let f in values) {
				let listValues = {};
				listValues = {
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
	ListViewDefault: (module, url) => {
		fetch(
			url+'&columns=true',
			{
				method: 'get',
				headers: {
					'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
				},
				credentials: 'same-origin',
			}
		).then(response => response.json()).then(response => {
			let headers = ListView.getColumnHeaders(response[0]);
			let filters = response[1];
			ListView.setFilters(filters);
			dataGridInstance = new tuiGrid({
				el: document.getElementById('listview-tui-grid'),
				columns: headers,
				rowHeaders: [{
					type: 'checkbox',
			        header: `
			          <label for="all-checkbox" class="checkbox">
			            <input type="checkbox" id="selectCurrentPageRec" class="listview-checkbox" onclick="toggleSelect_ListView(this.checked,'selected_id[]');ListView.getCheckedRows('currentPage', this);" name="_checked" />
			          </label>
			        `,
					renderer: {
	            		type: CheckboxRender
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
				copyOptions: {
					useListItemText: true
				},
				onGridUpdated: (ev) => {
					const lastPage = dataGridInstance.getPagination()._currentPage;
					sessionStorage.setItem(module+'_lastPage', lastPage);
					ListView.updateData();
					const rows = document.getElementById('allselectedboxes').value;
					if (rows != '') {
						ListView.checkRows();
					}
				}
			});
			dataGridInstance.on('afterFilter', (ev) => {
				const operatorData = {
					eq: 'e',
					contain: 'c',
					ne: 'n',
					start: 's',
					end: 'ew',
				};
				const operator = operatorData[ev.filterState[0].state[0]['code']];
				const urlstring = `&query=true&search_field=${ev.columnName}&search_text=${ev.filterState[0].state[0]['value']}&searchtype=BasicSearch&operator=${operator}`;
				const searchtype = 'Basic';
				ListView.ListViewSearch(url, urlstring, searchtype);
			});
			tui.Grid.applyTheme('striped');
		});
	},
	/**
	 * Get the new headers in a onchange search
	 * @param {String} url
	 * @param {String} urlstring
	 * @param {String} searchtype
	 */
	 ListViewSearch: (url, urlstring, searchtype) => {
	 	//set search_url value to input
	 	if (searchtype == 'Basic') {
			const parseUrl = urlstring.split('&');
			let urlArr = [];
			for (arg in parseUrl) {
				const URI = parseUrl[arg].split('=');
				urlArr[URI[0]] = URI[1];
			}
			document.getElementById('search_url').value = `&query=true&search_field=${urlArr['search_field']}&search_text=${urlArr['search_text']}&searchtype=BasicSearch`;
	 	} else {
	 		document.getElementById('search_url').value = urlstring + '&query=true';
	 	}
		dataGridInstance.clear();
	 	dataGridInstance.setRequestParams({'search': urlstring, 'searchtype': searchtype});
	 	dataGridInstance.reloadData();
		//update pagination onchange
		dataGridInstance.setPerPage(parseInt(PageSize));
	 	ListView.updateData();
	},
	/**
	 * Get results for alphabetic search
	 * @param {String} url
	 */
	 ListViewAlpha: (url) => {
	 	//set search_url value to input
		const parseUrl = url.split('&');
		let urlArr = [];
		for (arg in parseUrl) {
			const URI = parseUrl[arg].split('=');
			urlArr[URI[0]] = URI[1];
		}
		document.getElementById('search_url').value = `&query=true&search_field=${urlArr['search_field']}&search_text=${urlArr['search_text']}&searchtype=BasicSearch&type=alpbt&operator=${urlArr['operator']}`;
		dataGridInstance.clear();
	 	dataGridInstance.setRequestParams({'search': url, 'searchtype': 'Basic'});
	 	dataGridInstance.reloadData();
		dataGridInstance.on('successResponse', function (data) {
			const res = JSON.parse(data.xhr.response);
			const export_where = res.export_where;
			if (export_where) {
				document.getElementsByName('where_export')[0].value = export_where;
			}
	 	});
		//update pagination onchange
		dataGridInstance.setPerPage(parseInt(PageSize));
		const total = ListView.updateData();
		document.getElementById('numOfRows').value = total;
		document.getElementById('count').innerHTML = total;
	},
	/**
	 * Get the new headers in a onchange data
	 */
	 ListViewReloadData: () => {
		dataGridInstance.clear();
	 	dataGridInstance.setRequestParams({'search': '', 'searchtype': ''});
	 	dataGridInstance.reloadData();
	 	//update pagination onchange
	 	dataGridInstance.setPerPage(parseInt(PageSize));
	 	ListView.updateData();
	},
	/**
	 * Get the new headers in a onchange filter
	 * @param {String} url
	 */
	ListViewFilter: (url) => {
		dataGridInstance.setRequestParams({'search': '', 'searchtype': ''});
		dataGridInstance.clear();
		fetch(
			url+'&columns=true',
			{
				method: 'get',
				headers: {
					'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
				},
				credentials: 'same-origin',
			}
		).then(response => response.json()).then(response => {
			let headers = ListView.getColumnHeaders(response[0]);
			let filters = response[1];
			//update options for basic search
			document.getElementById('bas_searchfield').innerHTML = '';
			for (let h in headers) {
				if (headers[h]['name'] != 'action') {
					let option = document.createElement('option');
					option.innerHTML = headers[h]['header'];
					option.value = headers[h]['name'];
					document.getElementById('bas_searchfield').appendChild(option);
				}
			}
			ListView.setFilters(filters, true);
		 	dataGridInstance.setColumns(headers);
		 	dataGridInstance.reloadData();
		});
		ListView.updateData();
		//update pagination onchange
		dataGridInstance.setPerPage(parseInt(PageSize));
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
	getCheckedRows: (type, el = '') => {
		let checkedRows = ListView.getAllCheckedRows(type, el);
		let currentPage = dataGridInstance.getPagination()._currentPage;
		let ids = [];
		let rowKeys = [];
		//add checked rows for current page
		for (let id in checkedRows) {
			let recordId = dataGridInstance.getValue(parseInt(checkedRows[id]), 'recordid');
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
		for (index in actualArr) {
			if (!newIds.includes(actualArr[index])) {
				newIds.push(actualArr[index]);
			}
		}
		select_options = newIds.join(';');
		if (!select_options.endsWith(';') && select_options != '') {
			select_options += ';';
		}
		//remove id for current unchecked row
		if (el.checked == false) {
			let removeId = el.id;
			let recordId = dataGridInstance.getValue(parseInt(removeId), 'recordid');
			select_options = select_options.replace(recordId+';', '');
		}
		//remove all ids for current page if header checkbox is unchecked
		if (checkedRows.length == 0) {
		 	for (let i = 0; i < PageSize; i++) {
		 		let recordId = dataGridInstance.getValue(parseInt(i), 'recordid');
		 		select_options = select_options.replace(recordId+';', '');
		 	}
		}
		document.getElementById('allselectedboxes').value = select_options;
		return rowKeys;
	},
	/**
	 * Remove all checked rows
	 * @param {String} selectedType
	 */
	removeRows: (selectedType = '') => {
		dataGridInstance.reloadData();
		dataGridInstance.removeCheckedRows();
		document.getElementById('status').style.display = 'none';
		if (selectedType == 'all') {
			document.getElementById('gridRecordCountHeader').innerHTML = '';
			document.getElementById('gridRecordCountFooter').innerHTML = '';
			document.getElementById('numOfRows').value = '';
			document.getElementById('linkForSelectAll').style.display = 'none';
		} else {
			const total = ListView.updateData();
			document.getElementById('numOfRows').value = total;
			document.getElementById('count').innerHTML = total;
			document.getElementById('linkForSelectAll').style.display = 'none';
		}
	},
	/**
	 * Update data in every change
	 */
	updateData: () => {
		const gridInstance = dataGridInstance.store.data.pageOptions;
		const page = gridInstance.page;
		const totalCount = gridInstance.totalCount;
		const currentPageSize = dataGridInstance.getRowCount();
		const limit_start_rec = (page-1) * PageSize;
		const currentPage = (limit_start_rec + 1) + ' - ' + (limit_start_rec + currentPageSize);

		for (let i = 0; i < currentPageSize; i++) {
			let recordid = dataGridInstance.getValue(i, 'recordid');
			let referenceField = dataGridInstance.getValue(i, 'reference');
			let referenceValue = dataGridInstance.getValue(i, referenceField);
			let relatedRows = dataGridInstance.getValue(i, 'relatedRows');
			let aAction = `
				<a href="index.php?module=${module}&action=EditView&record=${recordid}&return_module=${module}&return_action=index">${alert_arr['LNK_EDIT']}</a> | 
				<a href="javascript:confirmdelete('index.php?module=${module}&action=Delete&record=${recordid}&return_module=${module}&return_action=index&parenttab=ptab');">${alert_arr['LNK_DELETE']}</a>`;
			let aVal = '<a href="index.php?module='+module+'&action=DetailView&record='+recordid+'">'+referenceValue+'<a>';
			dataGridInstance.setValue(i, referenceField, aVal, false);
			dataGridInstance.setValue(i, 'action', aAction, false);
		}
		if (totalCount > 0) {
			document.getElementById('gridRecordCountHeader').innerHTML = alert_arr['LBL_SHOWING'] + currentPage + alert_arr['LBL_RECORDS'] + totalCount;
			document.getElementById('gridRecordCountFooter').innerHTML = alert_arr['LBL_SHOWING'] + currentPage + alert_arr['LBL_RECORDS'] + totalCount;
		} else {
			document.getElementById('gridRecordCountHeader').innerHTML = '';
			document.getElementById('gridRecordCountFooter').innerHTML = '';
		}
		return totalCount;
	},
	/**
	 * Update filter action in every change
	 * @param {Object} filters
	 * @param {Boolean} reload
	 */
	setFilters: (filters, reload = false) => {
		if (reload == true) {
			document.getElementById('filterOptions').innerHTML = '';
			document.getElementById('filterEditActions').innerHTML = '';
			document.getElementById('filterDeleteActions').innerHTML = '';
		}
		let select = document.createElement('select');
		select.id = 'viewname';
		select.name = 'viewname';
		select.className = 'small';
		select.setAttribute('onchange', 'showDefaultCustomView(this, "'+module+'", "'+filters.category+'")');
		select.innerHTML = filters.customview_html;
		document.getElementById('filterOptions').appendChild(select);

		//create filterActions
		let fedit = document.createElement('span');
		if (filters.edit_permit == 'yes') {
			fedit.innerHTML = `| <a href="index.php?module=${module}&action=CustomView&record=${filters.viewid}&parenttab=${filters.category}">${alert_arr['LNK_EDIT_ACTION']}</a> |`;
		} else {
			fedit.innerHTML = `| ${alert_arr['LNK_EDIT_ACTION']} |`;
		}
		document.getElementById('filterEditActions').appendChild(fedit);
		let fdelete = document.createElement('span');
		if (filters.delete_permit == 'yes') {
			fdelete.innerHTML = `<a href="javascript:confirmdelete('index.php?module=CustomView&action=Delete&dmodule=${module}&record=${filters.viewid}&parenttab=${filters.category}')">${alert_arr['LNK_DELETE_ACTION']}`;
		} else {
			fdelete.innerHTML = `${alert_arr['LNK_DELETE_ACTION']}`;
		}
		document.getElementById('filterDeleteActions').appendChild(fdelete);
	},
	/**
	 * Check rows in grid
	 */
	checkRows: () => {
		let actualVal = document.getElementById('allselectedboxes');
		let idsArr = actualVal.value.split(';');
		for (let i = 0; i <= PageSize; i++) {
			let recordId = dataGridInstance.getValue(parseInt(i), 'recordid');
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
	updateFieldData: (ev, idx) => {
		console.log(idx);
		const recordid = dataGridInstance.getValue(idx, 'recordid');
		const rowKey = ev.rowKey;
		const columnName = ev.columnName;
		const value = ev.value;
		const preValue = ev.preValue;
		if (value != preValue) {
			jQuery.ajax({
				method: 'POST',
				url: 'index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=listViewJSON&method=updateDataListView',
				data: {
					modulename: module,
					value: value,
					columnName: columnName,
					recordid: recordid,
				}
			});
		}
	},
};