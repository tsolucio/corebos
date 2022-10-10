/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
class WizardComponent {

	constructor(steps) {
		this.steps = steps;
		this.ActiveStep = 0;
		this.CheckedRows = [];
		this.GroupByField = '';
		this.GridData = [];
		this.GroupData = [];
		this.WizardInstance = [];
		this.WizardRelModules = [];
		this.WizardEntityNames = [];
		this.WizardCurrentModule = [];
		this.WizardColumns = [];
		this.WizardActions = [];
		this.WizardMode = [];
		this.WizardFilterBy = [];
		this.WizardValidate = [];
		this.WizardGoBack = [];
		this.IsDuplicatedFromProduct = 0;
		this.Operation = '';
		this.url = 'index.php?module=Utilities&action=UtilitiesAjax&file=WizardAPI';
	}

	Init() {
		this.Events();
	}

	Events() {
		this.ClickEv();
	}

	/**
	 * Register all click events in Wizard
	 */
	ClickEv() {
		const ids = [
			'btn-next',
			'btn-back'
		];
		for (let i in ids) {
			this.el(ids[i]).addEventListener('click', function(event) {
				const prc = wizard.Next(event);
				if (prc) {
					wizard.MoveToStep(event);
				}
			});
		}
	}

	/**
	 * Move to next step
	 * @param {Object} event
	 */
	Next(ev) {
		switch (this.Operation) {
			case 'CREATEPRODUCTCOMPONENTS'://*specific use case
				if (this.WizardMode[this.ActiveStep] == 'SELECTPRODUCT') {
					if (this.IsDuplicatedFromProduct == 0) {
						ldsPrompt.show(alert_arr.ERROR, alert_arr.LBL_DUPLICATE_PRODUCT, 'error');
						return false;
					}
					if (!this.CheckSelection(ev, 'SELECTPRODUCT')) {
						return false;
					}
					return this.FilterRows(ev);
				}
				if (this.WizardMode[this.ActiveStep] == 'CREATEPRODUCTCOMPONENT') {
					if (this.WizardValidate[this.ActiveStep]) {
						if (!this.CheckSelection(ev)) {
							return false;
						}
					}
					return this.Create_ProductComponent(ev);
				}
				const type = ev.target.dataset.type;
				if (this.WizardMode[this.ActiveStep] == 'ListView' && this.WizardCurrentModule[this.ActiveStep] == 'ProductComponent' && type == 'back') {
					this.CheckedRows[this.ActiveStep-1] = [];
					this.WizardInstance[`wzgrid${this.ActiveStep-1}`].uncheckAll();
				}
				break;
			case 'MASSCREATE':
			case 'MASSCREATETREEVIEW':
				return this.MassCreateGrid(ev, this.Operation);
				break;
			default:
		}
		return true;
	}

	/**
	 * Check for checked rows in grid
	 * @param {Object} event
	 * @param {String} action
	 */
	CheckSelection(ev, action = '') {
		const type = ev.target.dataset.type;
		if (type == 'next') {
			const checkedRows = this.WizardInstance[`wzgrid${this.ActiveStep}`].getCheckedRows();
			if (checkedRows.length == 0) {
				ldsPrompt.show(alert_arr.ERROR, alert_arr.LBL_SELECT_MORE_ROWS, 'error');
				return false;
			}
			if (action == 'SELECTPRODUCT' && checkedRows.length != 1) {
				ldsPrompt.show(alert_arr.ERROR, alert_arr.LBL_SELECT_ROW, 'error');
				return false;
			}
		}
		return true;
	}

	/**
	 * Filter records for every step with direct query from map
	 * @param {Object} event
	 */
	FilterRows(ev) {
		const type = ev.target.dataset.type;
		if (type == 'back') {
			return true;
		}
		if (this.WizardFilterBy[this.ActiveStep+1] != '') {
			const module = this.WizardCurrentModule[this.ActiveStep+1];
			this.WizardInstance[`wzgrid${this.ActiveStep+1}`].setRequestParams({
				formodule: module,
				query: this.WizardFilterBy[this.ActiveStep+1]
			});
			this.WizardInstance[`wzgrid${this.ActiveStep+1}`].setPerPage(parseInt(20));
		}
		return true;
	}

	/**
	 * Filter rows for specific IDS.
	 */
	FilterDataForStep() {
		const module = wizard.WizardCurrentModule[wizard.ActiveStep];
		wizard.WizardInstance[`wzgrid${wizard.ActiveStep}`].clear();
		wizard.WizardInstance[`wzgrid${wizard.ActiveStep}`].setRequestParams({
			formodule: module,
			filterrows: true,
			step: wizard.ActiveStep
		});
		setTimeout(function() {
			wizard.WizardInstance[`wzgrid${wizard.ActiveStep}`].setPerPage(parseInt(20));
		}, 100);
	}

	/**
	 * Get related fieldname between two modules
	 */
	RelatedFieldName() {
		const relmodule = this.WizardCurrentModule[this.ActiveStep];
		const module = this.WizardCurrentModule[this.ActiveStep+1];
		let relmods = JSON.parse(this.WizardRelModules[this.ActiveStep+1]);
		for (let i in relmods) {
			if (module == relmods[i].module && relmodule == relmods[i].relmodule) {
				return relmods[i];
			}
		}
		//search for not direct related modules
		let crelmods = JSON.parse(this.WizardRelModules[this.ActiveStep]);
		for (let i in relmods) {
			if (relmods[i].module == module) {
				for (let j in crelmods) {
					if (crelmods[j].relmodule == relmods[i].relmodule) {
						return `${module}.${crelmods[j].relmodule}.${crelmods[j].fieldname}`;
					}
				}
				break;
			}
		}
		return '';
	}

	/**
	 * Get only selected id values in grid
	 */
	IdVal(step = -1) {
		let holdStep = this.ActiveStep;
		if (step > -1) {
			this.ActiveStep = step;
		}
		const ids = [];
		for (let i in this.CheckedRows[this.ActiveStep]) {
			for (var j = 0; j < this.CheckedRows[this.ActiveStep][i].length; j++) {
				ids.push(this.CheckedRows[this.ActiveStep][i][j]['id']);
			}
		}
		this.ActiveStep = holdStep;
		return ids;
	}

	/**
	 * Move in each step in Wizard
	 * @param {Object} event
	 */
	MoveToStep(ev) {
		const type = ev.target.dataset.type;
		switch (type) {
			case 'next':
				if (this.ActiveStep + 1 != this.steps) {
					this.ActiveStep++;
					this.el(`seq-${this.ActiveStep}`).style.display = 'block';
					this.el(`seq-${this.ActiveStep - 1}`).style.display = 'none';
					this.el(`header-${this.ActiveStep}`).classList.add('slds-is-active');
				}
				break;
			case 'back':
				this.ActiveStep--;
				this.el(`seq-${this.ActiveStep}`).style.display = 'block';
				this.el(`seq-${this.ActiveStep + 1}`).style.display = 'none';
				this.el(`header-${this.ActiveStep + 1}`).classList.remove('slds-is-active');
				break;
			default:
			//
		}
		if (this.ActiveStep >= 1) {
			this.el(`btn-back`).removeAttribute('disabled');
		} else {
			this.el(`btn-back`).setAttribute('disabled', '');
		}
		if (this.WizardGoBack[this.ActiveStep-1] == 0) {
			this.el(`btn-back`).setAttribute('disabled', '');
		}
		if (this.ActiveStep + 1 == this.steps && type == 'next') {
			this.el(`btn-next`).innerHTML = alert_arr.JSLBL_FINISH;
			return false;
		} else {
			this.el(`btn-next`).innerHTML = alert_arr.JSLBL_NEXT;
		}
	}

	/**
	 * Save selected rows in CheckedRows array for every step and page in grid
	 * @param {String} mode check | uncheck | checkAll | uncheckAll
	 * @param {String} step
	 * @param {Object} grid event
	 */
	SaveRows(mode, step, ev) {
		const page = this.WizardInstance[`wzgrid${this.ActiveStep}`].getPagination()._currentPage;
		const rows = this.WizardInstance[`wzgrid${this.ActiveStep}`].getCheckedRows();
		if (this.CheckedRows[this.ActiveStep] === undefined) {
			this.CheckedRows[this.ActiveStep] = [];
		}
		this.CheckedRows[this.ActiveStep][page] = rows;
	}

	Upsert(id, action = '') {
		let url = `&step=${this.ActiveStep}&WizardView=true`;
		if (action == 'edit') {
			url += `&Module_Popup_Edit=1&wizardaction=${action}`;
		} else if (action == 'duplicate') {
			url += `&Module_Popup_Edit=1&isDuplicate=true&wizardaction=${action}`;
		}
		const module = this.WizardCurrentModule[this.ActiveStep];
		window.open('index.php?module='+module+'&action=EditView&record='+id+url, null, cbPopupWindowSettings + ',dependent=yes');
	}

	Delete(id) {
		if (confirm(alert_arr.ARE_YOU_SURE)) {
			const module = this.WizardCurrentModule[this.ActiveStep];
			var url = `${this.url}&wizardaction=Delete&subaction=DeleteRecords`;
			this.Request(url, 'post', {
				recordid: id,
				modulename: module
			}).then(function(response) {
				if (response) {
					ldsPrompt.show(alert_arr.LBL_SUCCESS, alert_arr.LBL_DELETE_SUCCESS, 'success');
					let page = wizard.WizardInstance[`wzgrid${wizard.ActiveStep}`].getPagination();
					wizard.WizardInstance[`wzgrid${wizard.ActiveStep}`].readData(page._currentPage, {
						page: page._currentPage
					}, true);
				} else {
					ldsPrompt.show(alert_arr.ERROR, alert_arr.Failed, 'error');
				}
			});
		}
	}

	save(step, action = 'edit') {
		if (step == 0 && action == 'duplicate') {
			this.IsDuplicatedFromProduct = 1;
		}
		let page = this.WizardInstance[`wzgrid${step}`].getPagination();
		const totalCount = this.WizardInstance[`wzgrid${step}`].getPaginationTotalCount();
		const totalPage = Math.ceil(totalCount/20);
		if (action == 'duplicate') {
			page._currentPage = totalPage;
		}
		setTimeout(function() {
			if (wizard.ActiveStep == 0 && wizard.WizardMode[wizard.ActiveStep] == 'SELECTPRODUCT') {
				wizard.WizardInstance[`wzgrid${wizard.ActiveStep}`].clear();
				wizard.WizardInstance[`wzgrid${wizard.ActiveStep}`].setRequestParams({
					page: 1,
					step: wizard.ActiveStep,
					mode: wizard.WizardMode[wizard.ActiveStep]
				});
				wizard.WizardInstance[`wzgrid${wizard.ActiveStep}`].setPerPage(parseInt(20));
			} else {
				wizard.WizardInstance[`wzgrid${step}`].readData(page._currentPage, {
					page: page._currentPage
				}, true);
			}
		}, 500);
	}

	/**
	 * Check all rows in grid when we move in pages
	 * @param {Object} grid event
	 */
	CheckRows(ev) {
		if (this.CheckedRows[this.ActiveStep] === undefined) {
			return false;
		}
		const _currentPage = this.CheckedRows[this.ActiveStep][ev.page];
		for (let i in _currentPage) {
			this.WizardInstance[`wzgrid${this.ActiveStep}`].check(_currentPage[i].rowKey);
		}
	}

	/**
	 * Hide last step in Wizard
	 */
	Hide() {
		for (var i = 1; i < this.steps; i++) {		
			this.el(`seq-${i}`).style.display = 'none';
		}
	}

	/**
	 * Get element id
	 * @param {String} id
	 */
	el(id) {
		return document.getElementById(id);
	}

	ConvertStringToHTML(str) {
		let parser = new DOMParser();
		let doc = parser.parseFromString(str, 'text/html');
		return doc.body;
	}

	loader(type) {
		const loader = document.getElementById('loader');
		if (type == 'show' && loader) {
			loader.style.display = 'block';
		} else if (type == 'hide' && loader) {
			loader.style.display = 'none';
		}
	}

	async Request(url, method, body = {}) {
		const options = {
			method: method,
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
			}
		};
		if (method == 'post') {
			options.body = '&'+csrfMagicName+'='+csrfMagicToken+'&data='+JSON.stringify(body);
		}
		const response = await fetch(url, options);
		return response.json();
	}

	async DeleteSession() {
		const url = `${this.url}&wizardaction=Delete&subaction=DeleteSession`;
		this.Request(url, 'post');
	}


	/**
	 * Delete checked rows in a step
	 * @param {step} string
	 */
	DeleteRowFromGrid(step) {
		const rowKeys = this.WizardInstance[`wzgrid${step}`].getCheckedRowKeys();
		if (rowKeys.length == 0) {
			ldsPrompt.show(alert_arr.ERROR, alert_arr.LBL_SELECT_MORE_ROWS, 'error');
			return false;
		}
		this.WizardInstance[`wzgrid${step}`].removeRows(rowKeys);
	}

	/**
	 * Create ProductComponent records. *Specific use case
	 * @param {Object} event
	 */
	Create_ProductComponent(ev) {
		const type = ev.target.dataset.type;
		if (type == 'back') {
			return true;
		}
		let rows = [];
		//get FROMID
		for (let i in this.CheckedRows[0]) {
			let ids = [];
			for (let j in this.CheckedRows[0][i]) {
				ids.push(this.CheckedRows[0][i][j].id);
			}
			rows.push(ids);
		}
		//get TOIDS
		if (this.CheckedRows[this.ActiveStep] == undefined) {
			this.WizardInstance[`wzgrid${this.ActiveStep+1}`].clear();
			return true;
		}
		for (let i in this.CheckedRows[this.ActiveStep]) {
			let ids = [];
			for (let j in this.CheckedRows[this.ActiveStep][i]) {
				ids.push(this.CheckedRows[this.ActiveStep][i][j].id);
			}
			rows.push(ids);
		}
		this.loader('show');
		const url = `${this.url}&wizardaction=MassCreate&subaction=Create_ProductComponent&formodule=ProductComponent&step=${this.ActiveStep}`;
		this.Request(url, 'post', rows).then(function(response) {
			if (response) {
				ldsPrompt.show(alert_arr.LBL_SUCCESS, alert_arr.LBL_CREATED_SUCCESS, 'success');
				wizard.FilterDataForStep();
			} else {
				ldsPrompt.show(alert_arr.ERROR, alert_arr.LBL_WRONG, 'error');
			}
			wizard.loader('hide');
		});
		return true;
	}

	/**
	 * Filter all Qutes and InventoryDetals for MassCreate. *Specific use case
	 * @param {Object} event
	 */
	MassCreateGrid(ev, operation) {
		const type = ev.target.dataset.type;
		let module = this.WizardCurrentModule[this.ActiveStep+1];
		if (this.ActiveStep != 2) {
			if (type == 'back' || this.WizardInstance[`wzgrid${this.ActiveStep}`] === undefined) {
				return true;
			}
			const checkedRows = this.WizardInstance[`wzgrid${this.ActiveStep}`].getCheckedRows();
			if (checkedRows.length == 0) {
				ldsPrompt.show(alert_arr.ERROR, alert_arr.LBL_SELECT_MORE_ROWS, 'error');
				return false;
			}
			if (operation == 'MASSCREATE' && checkedRows.length != 1 && this.ActiveStep == 0) {
				ldsPrompt.show(alert_arr.ERROR, alert_arr.LBL_SELECT_ROW, 'error');
				return false;
			}
		}
		if (this.ActiveStep == 0) {//second step
			const findColName = this.RelatedFieldName();
			if (findColName == '') {
				ldsPrompt.show(alert_arr.ERROR, alert_arr.LBL_UNABLE_TO_FILTER, 'error');
				return false;
			}
			const ids = this.IdVal();
			if (ids.length == 0) {
				ldsPrompt.show(alert_arr.ERROR, alert_arr.LBL_UNABLE_TO_FILTER, 'error');
				return false;
			}
			if (typeof findColName == 'string' && findColName.indexOf('.') > -1) {
				module = this.WizardCurrentModule[this.ActiveStep];
			}
			this.WizardInstance[`wzgrid${this.ActiveStep+1}`].setRequestParams({
				forids: JSON.stringify(ids),
				formodule: module,
				forfield: findColName
			});
			this.WizardInstance[`wzgrid${this.ActiveStep+1}`].setPerPage(parseInt(20));
		}
		if (operation == 'MASSCREATE' && this.ActiveStep == 1) {
			this.Mapping(0, 1);
			return true;
		}
		if (this.ActiveStep == 1) {//last step(3)
			const data = this.CheckedRows[this.ActiveStep];
			let rows = [];
			for (let i in data) {
				for (let j in data[i]) {
					rows.push(data[i][j]);
				}
			}
			const groupBy = rows.reduce((group, row) => {
				const field = row[this.GroupByField];
				group[field] = group[field] ?? [];
				group[field].push(row);
				return group;
			}, {});
			this.GroupData = groupBy;
			this.TreeGrid();
		}
		if (this.ActiveStep == 2 && type == 'next') {
			this.MassCreate(operation);
		}
		return true;
	}

	/**
	 * Map fields between 2 modules and show them in grid in draft status
	 * @param {s1} step one
	 * @param {s2} step two
	 */
	Mapping(s1, s2) {
		const ids = [{
			id: this.IdVal(s1),
			module: this.WizardCurrentModule[s1]
		},
		{
			id: this.IdVal(s2),
			module: this.WizardCurrentModule[s2]
		}];
		const url = `${this.url}&wizardaction=Mapping&formodule=${MCModule}`;
		this.Request(url, 'post', ids).then(function(response) {
			wizard.WizardInstance[`wzgrid${wizard.ActiveStep}`].clear();
			wizard.WizardInstance[`wzgrid${wizard.ActiveStep}`].setPaginationTotalCount(response.data.contents.length);
			wizard.WizardInstance[`wzgrid${wizard.ActiveStep}`].resetData(response.data.contents);
		});
	}

	MassCreate(operation = '') {
		let data = [];
		let filterData = [];
		if (operation == '') {
			//this is used for treeview
			for (let i in this.GroupData) {
				if (i != '') {
					let rows = {};
					for (let j in this.GroupData[i]) {
						rows[j] = this.GroupData[i][j].id;
					}
					if (isNaN(i)) {
						const doc = this.ConvertStringToHTML(i).getElementsByTagName('a')[0];
						const url = doc.getAttribute('href');
						const urlParser = new URL(`https://example.com/${url}`);
						i = urlParser.searchParams.get('record');
					}
					data[i] = rows;
				}
			}
			filterData = Object.fromEntries(Object.entries(data).filter(value => value[1]));
		} else if (operation == 'MASSCREATE') {
			//MASCREATE operation is for created records based on 2 modules without grouping fields
			const checkedRows = this.WizardInstance[`wzgrid${this.ActiveStep}`].getCheckedRows();
			const wizardcolumns = JSON.parse(this.WizardColumns[this.ActiveStep]);
			let data = [];
			for (let j in checkedRows) {
				let row = {};
				for (let i in wizardcolumns) {
					row[wizardcolumns[i].name] = checkedRows[j][wizardcolumns[i].name];
				}
				data.push(row);
			}
			filterData = [{
				id: this.IdVal(0),
				relmodule: this.WizardCurrentModule[0]
			},{
				data: data,
				createmodule: this.WizardCurrentModule[2]
			}];
		}
		if (Object.keys(filterData).length === 0) {
			ldsPrompt.show(alert_arr.ERROR, alert_arr.LBL_WRONG, 'error');
			return false;
		}
		this.loader('show');
		const url = `${this.url}&wizardaction=MassCreate&formodule=${MCModule}&subaction=${this.WizardMode[0]}`;
		this.Request(url, 'post', filterData).then(function(response) {
			if (response) {
				ldsPrompt.show(alert_arr.LBL_SUCCESS, alert_arr.LBL_CREATED_SUCCESS, 'success');
				setTimeout(function() {
					location.reload(true);
				}, 1000);
			} else {
				ldsPrompt.show(alert_arr.ERROR, alert_arr.LBL_WRONG, 'error');
			}
			wizard.loader('hide');
		});
	}

	TreeGrid() {
		const wizardcolumns = JSON.parse(this.WizardColumns[this.ActiveStep]);
		let columns = [];
		let filtercolumns = [];
		columns.push({
			header: 'Parent',
			name: 'parentaction'
		});
		for (let i in wizardcolumns) {
			columns.push(wizardcolumns[i])
			filtercolumns.push(wizardcolumns[i].name)
		}
		let griddata = [];
		for (let id in this.GroupData) {
			if (id != 0) {
				let rows = [];
				for (let i in this.GroupData[id]) {
					let row = {};
					Object.keys(this.GroupData[id][i]).forEach(key => {
						if (filtercolumns.includes(key)) {
							row[key] = this.GroupData[id][i][key];
						}
					});
					rows.push(row);
				}
				griddata.push({
					parentaction: id,
					_children: rows,
					_attributes: {
						expanded: true
					}
				});
			}
		}
		if (this.WizardInstance[`wzgrid${this.ActiveStep+1}`] !== undefined) {
			this.WizardInstance[`wzgrid${this.ActiveStep+1}`].destroy();
		}
		this.WizardInstance[`wzgrid${this.ActiveStep+1}`] = new tui.Grid({
			el: document.getElementById(`seq-${this.ActiveStep+1}`),
			columns: columns,
			data: griddata,
			treeColumnOptions: {
				name: 'parentaction',
				useIcon: false,
				useCascadingCheckbox: false
			},
			useClientSort: false,
			rowHeight: 'auto',
			bodyHeight: 'auto',
			scrollX: false,
			scrollY: false,
			columnOptions: {
				resizable: true
			},
			header: {
				align: 'left',
			},
			contextMenu: null
		});
	}
}

class WizardActions {

	constructor(props) {
		let rowKey = props.rowKey;
		let recordid = props.grid.getValue(rowKey, 'id');
		let { actions } = props.columnInfo.renderer.options;
		let el = document.createElement('span');
		actions = actions.split(',');
		if (actions.length > 0) {
			el. innerHTML += `<div class="slds-button-group" role="group">`;
			if (actions.includes('edit')) {
				el.innerHTML += `
				<button
					type="button"
					class="slds-button slds-button_icon slds-button_icon-border-filled"
					aria-pressed="false"
					onclick="wizard.Upsert(${recordid}, 'edit')"
				>
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
					</svg>
					<span class="slds-assistive-text">${alert_arr.LNK_EDIT_ACTION}</span>
				</button>`;
			}
			if (actions.includes('duplicate')) {
				el.innerHTML += `
				<button
					type="button"
					class="slds-button slds-button_icon slds-button_icon-border-filled"
					aria-pressed="false"
					onclick="wizard.Upsert(${recordid}, 'duplicate')"
				>
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#file"></use>
					</svg>
					<span class="slds-assistive-text">${alert_arr.LNK_DUPLICATE}</span>
				</button>`;
			}
			if (actions.includes('delete')) {
				el.innerHTML += `
				<button
					type="button"
					class="slds-button slds-button_icon slds-button_icon-border-filled"
					aria-pressed="false"
					onclick="wizard.Delete(${recordid})"
				>
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
					</svg>
					<span class="slds-assistive-text">${alert_arr.LNK_DELETE_ACTION}</span>
				</button>`;
			}
			el. innerHTML += `</div>`;
		}
		this.el = el;
		this.render(props);
	}

	getElement() {
		return this.el;
	}

	render(props) {
		this.el.value = String(props.value);
	}
}