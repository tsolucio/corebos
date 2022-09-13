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
		this.ActiveStep = 0;
		this.steps = steps;
		this.WizardInstance = [];
		this.WizardRelModules = [];
		this.WizardEntityNames = [];
		this.CheckedRows = [];
		this.WizardCurrentModule = [];
		this.GridData = [];
		this.WizardColumns = [];
		this.GroupData = [];
		this.WizardMode = '';
		this.GroupByField = '';
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
		if (this.WizardMode == 'Create_PurchaseOrder') {
			return this.PurchaseOrder(ev);
		}
		return true;
	}

	/**
	 * Filter all Qutes and InventoryDetals for MassCreate. *Specific use case
	 */
	PurchaseOrder(ev) {
		const type = ev.target.dataset.type;
		if (this.ActiveStep != 2) {
			if (type == 'back' || this.WizardInstance[`wzgrid${this.ActiveStep}`] === undefined) {
				return true;
			}
			const checkedRows = this.WizardInstance[`wzgrid${this.ActiveStep}`].getCheckedRows();
			if (checkedRows.length == 0) {
				ldsPrompt.show('Error', 'Please select at least one row', 'error');
				return false;
			}
		}
		if (this.ActiveStep == 0) {//second step
			const findColName = this.ColumnToFilter();
			if (findColName == '') {
				ldsPrompt.show('Error', 'Unable to filter data. Try again!', 'error');
				return false;
			}
			const ids = this.IdVal();
			if (ids.length == 0) {
				ldsPrompt.show('Error', 'Unable to filter data. Try again!', 'error');
				return false;
			}
			const module = this.WizardCurrentModule[this.ActiveStep+1];
			this.WizardInstance[`wzgrid${this.ActiveStep+1}`].setRequestParams({
				forids: JSON.stringify(ids),
				formodule: module,
				forfield: findColName
			});
			this.WizardInstance[`wzgrid${this.ActiveStep+1}`].setPerPage(parseInt(20));
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
			this.MassCreate();
		}
		return true;
	}

	MassCreate() {
		let data = [];
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
		const filterData = Object.fromEntries(Object.entries(data).filter(value => value[1]));
		if (Object.keys(filterData).length === 0) {
			ldsPrompt.show('Error', 'Something went wrong. Try again!', 'error');
			return false;
		}
		this.loader('show');
		const url = `index.php?module=Utilities&action=UtilitiesAjax&file=WizardAPI&wizardaction=MassCreate&formodule=${MCModule}`;
		fetch(
			url,
			{
				method: 'post',
				headers: {
					'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
				},
				credentials: 'same-origin',
				body: '&'+csrfMagicName+'='+csrfMagicToken+'&data='+JSON.stringify(filterData)
			}
		).then(response => response.json()).then(response => {console.log(response)
			if (response) {
				ldsPrompt.show('Success', 'PurchaseOrder created successfully!', 'success');
			} else {
				ldsPrompt.show('Error', 'Something went wrong. Try again!', 'error');
			}
			this.loader('hide');
		});
	}

	TreeGrid() {
		const wizardcolumns = JSON.parse(this.WizardColumns[this.ActiveStep]);
		let columns = [];
		let filtercolumns = [];
		columns.push({
			header: 'Vendor',
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

	/**
	 * Get related fieldname between two modules
	 */
	ColumnToFilter() {
		const relmodule = this.WizardCurrentModule[this.ActiveStep];
		const module = this.WizardCurrentModule[this.ActiveStep+1];
		let relmods = JSON.parse(this.WizardRelModules[this.ActiveStep+1]);
		for (let i in relmods) {
			if (module == relmods[i].module && relmodule == relmods[i].relmodule) {
				return relmods[i];
			}
		}
		return '';
	}

	/**
	 * Get only selected id values in grid
	 */
	IdVal() {
		const ids = [];
		for (let i in this.CheckedRows[this.ActiveStep]) {
			for (var j = 0; j < this.CheckedRows[this.ActiveStep][i].length; j++) {
				ids.push(this.CheckedRows[this.ActiveStep][i][j]['id']);
			}
		}
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
		if (this.ActiveStep + 1 == this.steps && type == 'next') {
			this.el(`btn-next`).innerHTML = 'Finish';
			return false;
		} else {
			this.el(`btn-next`).innerHTML = 'Next';
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
}