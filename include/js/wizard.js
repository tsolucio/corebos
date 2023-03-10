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
loadJS('index.php?module=Settings&action=SettingsAjax&file=getjslanguage');

class WizardComponent {

	constructor() {
		this.steps = 0;
		this.MCModule = '';
		this.ActiveStep = 0;
		this.CheckedRows = [];
		this.CreatedRows = [];
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
		this.WizardConditionQuery = [];
		this.WizardValidate = [];
		this.WizardGoBack = [];
		this.WizardRequiredAction = [];
		this.WizardCustomFunction = [];
		this.IsDuplicatedFrom = [];
		this.ApplyFilter = [];
		this.WizardSaveAction = [];
		this.WizardSaveActive = [];
		this.WizardSaveIsActive = [];
		this.WizardFilterFromContext = [];
		this.WizardConfirmStep = [];
		this.WizardInfoFields = [];
		this.Module = [];
		this.Context = {};
		this.Operation = '';
		this.ProceedToNextStep = false;
		this.ResetWizard = true;
		this.MainSelectedId = 0; //the record selected/duplicated in first step
		this.SubWizardInfoMainId = 0;
		this.isSubWizard = false;
		this.url = 'index.php?module=Utilities&action=UtilitiesAjax&file=WizardAPI';
		//formtemplate params
		this.FormFields = [];
		this.FormModule = [];
		this.FormIDS = [];
		this.Suboperation = [];
		this.Interval = [];
		this.Calendar = [];
		this.TreeViewID = [];//used for Parent ID in TreeView mode
	}

	Init() {
		this.ActionButtons();
		this.Events();
		this.CleanWizard();
		if (this.isModal) {
			this.el('global-modal-container__title').classList.remove('slds-text-heading_medium', 'slds-modal__title');
			this.el('global-modal-container__title').innerHTML = this.el('wizard-steps').innerHTML;
			this.el('wizard-steps').remove();
		}
		if (!this.ResetWizard[this.ActiveStep] && this.WizardSaveAction[this.ActiveStep] && this.ActiveStep == 0) {
			this.RenderButtons();
		}
		if (this.isModal && this.ProceedToNextStep) {
			const prc = this.Next('');
			if (prc) {
				this.MoveToStep('');
			}
		}
	}

	Events() {
		this.ClickEv();
	}

	GoTo(step) {
		this.isSubWizard = true;
		if (!this.CheckSelection('')) {
			return false;
		}
		this.ActiveStep = step-1;
		for (let i = 0; i < this.steps; i++) {
			if (step >= i) {
				this.el(`header-${i}`).classList.add('slds-is-active');
			} else {
				this.el(`header-${i}`).classList.remove('slds-is-active');
			}
			if (i == step) {
				this.el(`seq-${i}`).style.display = 'block';
				const prc = this.Next('');
				if (prc) {
					this.MoveToStep('');
				}
			} else {
				this.el(`seq-${i}`).style.display = 'none';
			}
		}
		const checkedRows = this.WizardInstance[`wzgrid${this.ActiveStep}`].getCheckedRows();
		if (checkedRows.length == 0) {
			delete this.CheckedRows[this.ActiveStep];
		}
	}

	/**
	 * Register all click events in Wizard
	 */
	ClickEv() {
		const ids = [
			'btn-next',
			'btn-back'
		];
		if (!this.isSubWizard) {
			for (let i in ids) {
				this.el(ids[i]).addEventListener('click', async function (event) {
					event.preventDefault();
					const prc = await wizard.Next(event);
					if (prc) {
						wizard.MoveToStep(event);
					}
				}, true);
			}
		}
	}

	/**
	 * Move to next step
	 * @param {Object} event
	 */
	async Next(ev) {
		let type = 'next';
		if (ev != '') {
			type = ev.target.dataset.type;
		}
		this.Info(type);
		let confirmstep = this.WizardConfirmStep[this.ActiveStep] !== undefined ? JSON.parse(this.WizardConfirmStep[this.ActiveStep]) : '';
		if (!this.isSubWizard && confirmstep != '' && confirmstep.confirm && !confirm(confirmstep.message) && type == 'next') {
			return false;
		}
		if (this.WizardInstance[`wzgrid${this.ActiveStep+1}`] !== undefined) {
			const checkedRows = this.WizardInstance[`wzgrid${this.ActiveStep+1}`].getCheckedRows();
			if (checkedRows.length == 0) {
				delete this.CheckedRows[this.ActiveStep+1];
			}
		}
		if (this.WizardValidate[this.ActiveStep]) {
			if (!this.CheckSelection(ev)) {
				return false;
			}
		}
		if (this.SubWizardInfoMainId != 0) {
			this.MainSelectedId = parseInt(this.SubWizardInfoMainId);
		}
		this.FilterRows(ev, this.WizardFilterFromContext[this.ActiveStep+1]);
		if (this.WizardRequiredAction[this.ActiveStep] == 'duplicate' && this.IsDuplicatedFrom[this.ActiveStep] == undefined && type == 'next') {
			const checkedRows = this.WizardInstance[`wzgrid${this.ActiveStep}`].getCheckedRows();
			if (checkedRows.length != 1 && this.WizardValidate[this.ActiveStep]) {
				ldsNotification.show(alert_arr.ERROR, alert_arr.LBL_SELECT_ROW, 'error');
				return false;
			}
			if (this.CheckedRows[this.ActiveStep] !== undefined && this.CheckedRows[this.ActiveStep].length > 0) {
				const url = `${this.url}&wizardaction=CustomCreate&subaction=CustomOfferDetail`;
				await this.DuplicateRow(ev);
				if (this.ActiveStep == 0) {
					return this.FinishRequest(url, false);
				}
				return true;
			}
		} else {
			delete this.IsDuplicatedFrom[this.ActiveStep-1];
		}
		if (this.WizardSaveIsActive[this.ActiveStep] !== undefined && this.WizardSaveIsActive[this.ActiveStep] == true) {
			return true;
		}
		let WizardSaveAction = this.WizardSaveIsActive[this.ActiveStep] === undefined ? false : this.WizardSaveIsActive[this.ActiveStep];
		if (this.WizardCustomFunction[this.ActiveStep] != '' && !WizardSaveAction) {
			if (this.CheckedRows[this.ActiveStep] !== undefined && this.CheckedRows[this.ActiveStep].length > 0) {
				this.CallCustomFunction(ev);
			}
		}
		//suboperation: TreeView. Get records from backend.
		if (this.Suboperation[this.ActiveStep+1] == 'TreeView') {
			this.WizardInstance[`wzgrid${this.ActiveStep+1}`].clear();
			this.WizardInstance[`wzgrid${wizard.ActiveStep+1}`].setRequestParams({
				parentid: this.TreeViewID[this.ActiveStep-1],
				child: this.Module[this.ActiveStep],
			});
			this.WizardInstance[`wzgrid${wizard.ActiveStep+1}`].setPerPage(parseInt(20));
			this.WizardInstance[`wzgrid${wizard.ActiveStep+1}`].reloadData();
			return true;
		}
		switch (this.Operation) {
		case 'CREATEPRODUCTCOMPONENTS':
			if (this.WizardMode[this.ActiveStep] == 'SELECTPRODUCT') {
				this.CheckedRows[this.ActiveStep-1] = [];
				this.CheckedRows[this.ActiveStep] = [];
				this.WizardInstance[`wzgrid${this.ActiveStep}`].uncheckAll();
				if (this.WizardInstance[`wzgrid${this.ActiveStep-1}`]) {
					this.WizardInstance[`wzgrid${this.ActiveStep-1}`].uncheckAll();
				}
				return this.FilterRows(ev);
			}
			if (this.WizardMode[this.ActiveStep] == 'CREATEPRODUCTCOMPONENT') {
				if (this.CheckedRows[this.ActiveStep] !== undefined && this.CheckedRows[this.ActiveStep].length != 0) {
					this.CreatedRows.push(this.CheckedRows[this.ActiveStep]);
				}
				this.Info(type);
				await this.Create_ProductComponent(ev);
				if (this.steps == this.ActiveStep+1) {
					await this.Finish();
				} else {
					const url = `${this.url}&wizardaction=CustomCreate&subaction=CustomOfferDetail`;
					if (type == 'next') {
						this.FinishRequest(url, false);
					}
					return true;
				}
			}
			if (this.WizardMode[this.ActiveStep] == 'ListView' && this.WizardCurrentModule[this.ActiveStep] == 'ProductComponent' && type == 'back') {
				this.CheckedRows[this.ActiveStep-1] = [];
				this.WizardInstance[`wzgrid${this.ActiveStep-1}`].uncheckAll();
			}
			break;
		case 'MASSCREATE':
		case 'MASSCREATETREEVIEW':
			//use condition query from the map with current_record id
			if (this.WizardConditionQuery[`${this.ActiveStep+1}`] !== undefined && this.WizardConditionQuery[`${this.ActiveStep+1}`] != '') {
				this.WizardInstance[`wzgrid${this.ActiveStep+1}`].clear();
				this.WizardInstance[`wzgrid${this.ActiveStep+1}`].setRequestParams({
					parentid: 0,
					showdata: true,
					currentid: document.getElementById('parent_id').value,
					conditionquery: this.WizardConditionQuery[`${this.ActiveStep+1}`],
				});
				this.WizardInstance[`wzgrid${this.ActiveStep+1}`].setPerPage(parseInt(20));
			}
			return this.MassCreateGrid(ev, this.Operation);
		case 'FORMTEMPLATE':
			if (type == 'back') {
				this.HideEvents();
				return true;
			}
			if (this.steps == this.ActiveStep+1) {
				this.CloseModal();
				return false;
			}
			let res = await this.SaveForm();
			if (res && this.Suboperation[this.ActiveStep+1] == 'CalendarView') {
				this.el(`seq-${this.ActiveStep+1}`).style.display = 'block';
				await this.CalendarView();
				this.Interval[this.ActiveStep+1] = setInterval(function () {
					wizard.RenderEvents();
				}, 5000);
			}
			return res;
			break;
		default:
		}
		return true;
	}

	Info(type, mode = '') {
		let list = '';
		let activeStep = this.ActiveStep+1;
		if (mode == 'save') {
			activeStep = this.ActiveStep;
		}
		if (type == 'back') {
			activeStep = this.ActiveStep-1;
		}
		if (this.WizardInfoFields[activeStep] == undefined) {
			return false;
		}
		let flds = JSON.parse(this.WizardInfoFields[activeStep]);
		if (flds.length == undefined) {
			flds = [flds];
		}
		let insertedIds = [];
		let headers = '';
		for (let k in flds) {
			headers += `
			<th scope="col">
				<div class="slds-truncate" title="Column 1">
				${flds[k].label}
				</div>
			</th>`;
		}
		if (this.el(`wizard-columns-info-${activeStep}`)) {
			this.el(`wizard-columns-info-${activeStep}`).innerHTML = headers;
		}
		for (let i in this.CreatedRows) {
			this.CreatedRows[i].forEach(function(row, index) {
				for (let j in row) {
					if (insertedIds.includes(row[j].id)) {
						continue;
					}
					insertedIds.push(row[j].id);
					let fields = '';
					for (let k in flds) {
						fields += `
						<td>
							<div class="slds-truncate">
								${row[j][flds[k].name]}
							</div>
						</td>`;
					}
					list += `<tr class="slds-hint-parent">${fields}</tr>`;
				}
			});
		}
		if (this.el(`wizard-info-${activeStep}`)) {
			this.el(`wizard-info-${activeStep}`).innerHTML = list;
		}
	}

	async FinishRequest(url, resetWizard) {
		let cStep = this.ActiveStep;
		if (this.isSubWizard) {
			cStep = this.ActiveStep-1;
		}
		let response = await this.Request(url, 'post', {
			'masterid': this.RecordID,
			'step': cStep,
			'isSubWizard': this.isSubWizard
		});
		if (response == 'no_create') {
			return false;
		}
		if (response) {
			if (response != 'no_alert') {
				ldsNotification.show(alert_arr.LBL_SUCCESS, alert_arr.LBL_CREATED_SUCCESS, 'success');
			}
			if (this.isModal) {
				RLInstance[this.gridInstance].readData(1);
				this.CheckedRows[this.ActiveStep] = [];
				this.WizardInstance[`wzgrid${this.ActiveStep}`].uncheckAll();
				if (resetWizard && !this.isSubWizard) {
					ldsModal.close();
					this.ActiveStep = 0;
					this.IsDuplicatedFrom = [];
					this.ProceedToNextStep = false;
					this.CheckedRows = [];
					this.GridData = [];
					this.GroupData = [];
					this.gridInstance = [];
					this.WizardInstance = [];
					localStorage.removeItem('currentWizardActive');
				} else {
					//if we click "save" make sure that "finish" will not create twice records
					let nextBtn = this.el('btn-next');
					if (nextBtn != null && this.steps == this.ActiveStep+1) {
						nextBtn.setAttribute('onclick', 'wizard.CloseModal()');
					}
				}
			} else {
				setTimeout(function () {
					location.reload(true);
				}, 1000);
			}
		} else {
			if (document.activeElement.tagName.toLowerCase() == 'button') {
				document.activeElement.innerHTML = alert_arr.JSLBL_FINISH;
			}
			ldsNotification.show(alert_arr.ERROR, alert_arr.LBL_WRONG, 'error');
		}
		if (this.el(`save-wizard-${this.ActiveStep}`) !== null) {
			this.el(`save-wizard-${this.ActiveStep}`).innerHTML = alert_arr.JSLBL_SAVE;
		}
		this.loader('hide');
		return true;
	}

	async Finish(resetWizard = true) {
		//decrement steps and active step with 1 because ThankYou template is a "fake" step
		if (this.Suboperation[this.ActiveStep] == 'ThankYou') {
			this.ActiveStep = this.ActiveStep-1;
			this.steps = this.steps-1;
			document.activeElement.innerHTML = `${alert_arr.JSLBL_Loading}...`;
		}
		switch (this.Operation) {
		case 'CREATEPRODUCTCOMPONENTS':
			const url = `${this.url}&wizardaction=CustomCreate&subaction=CustomOfferDetail`;
			if (this.WizardInstance[`wzgrid${this.ActiveStep}`] != undefined) {
				const checkedRows = this.WizardInstance[`wzgrid${this.ActiveStep}`].getCheckedRows();
				if (checkedRows.length == 0 && !resetWizard) {
					ldsNotification.show(alert_arr.ERROR, alert_arr.LBL_SELECT_MORE_ROWS, 'error');
					return false;
				}
			}
			if (this.WizardCustomFunction[this.ActiveStep] != '') {
				if (this.el(`save-wizard-${wizard.ActiveStep}`) !== null) {
					this.el(`save-wizard-${this.ActiveStep}`).innerHTML = `${alert_arr.JSLBL_Loading}...`;
					this.el(`save-wizard-${this.ActiveStep}`).setAttribute('disabled', '');
				}
				this.WizardSaveIsActive[this.ActiveStep] = true;
				await this.CallCustomFunction();
				await this.FinishRequest(url, resetWizard);
				this.CheckedRows[this.ActiveStep] = [];
				if (this.WizardInstance[`wzgrid${this.ActiveStep}`] !== undefined) {
					this.WizardInstance[`wzgrid${this.ActiveStep}`].uncheckAll();
				}
			} else {
				await this.FinishRequest(url, resetWizard);
			}
			break;
		default:
		}
	}

	/**
	 * Close and reset wizard modal
	 */
	CloseModal() {
		ldsModal.close();
		this.ActiveStep = 0;
		this.IsDuplicatedFrom = [];
		this.ProceedToNextStep = false;
		this.CheckedRows = [];
		this.GridData = [];
		this.GroupData = [];
		this.gridInstance = [];
		this.WizardInstance = [];
		this.FormFields = [];
		this.FormModule = [];
		this.FormIDS = [];
		this.Suboperation = [];
		this.TreeViewID = [];
		localStorage.removeItem('currentWizardActive');
	}

	/**
	 * Check for checked rows in grid
	 * @param {Object} event
	 * @param {String} action
	 */
	CheckSelection(ev, action = '') {
		let type = 'next';
		if (ev != '') {
			type = ev.target.dataset.type;
		}
		if (type == 'next' && this.WizardValidate[this.ActiveStep]) {
			const checkedRows = this.WizardInstance[`wzgrid${this.ActiveStep}`].getCheckedRows();
			if (checkedRows.length != 1 && action == 'singlerow') {
				ldsNotification.show(alert_arr.ERROR, alert_arr.LBL_SELECT_ROW, 'error');
				return false;
			}
			if (checkedRows.length == 0) {
				ldsNotification.show(alert_arr.ERROR, alert_arr.LBL_SELECT_MORE_ROWS, 'error');
				return false;
			}
		}
		return true;
	}

	/**
	 * Filter records for every step with direct query from map
	 * @param {Object} event
	 */
	FilterRows(ev, filterFromContext = '', currentIdx = '') {
		let type = 'next';
		if (ev != '') {
			type = ev.target.dataset.type;
		}
		if (type == 'back') {
			return true;
		}
		if (currentIdx == '') {
			currentIdx = this.ActiveStep+1;
		}
		if (this.WizardFilterBy[currentIdx] != '' && this.WizardInstance[`wzgrid${currentIdx}`] !== undefined) {
			let ctx = {};
			if (filterFromContext != '') {
				const ContextObj = JSON.parse(filterFromContext);
				for (let i in ContextObj) {
					if (this.Context[ContextObj[i].find] != undefined) {
						ctx[ContextObj[i].find] = this.Context[ContextObj[i].find];
					}
				}
			}
			const module = this.WizardCurrentModule[currentIdx];
			this.WizardInstance[`wzgrid${currentIdx}`].setRequestParams({
				formodule: module,
				query: this.WizardFilterBy[currentIdx],
				context: ctx,
				filterFromContext: filterFromContext,
				showdata: true,
			});
			this.WizardInstance[`wzgrid${currentIdx}`].setPerPage(parseInt(20));
		}
		return true;
	}

	/**
	 * Filter rows for specific IDS.
	 */
	FilterDataForStep() {
		if (this.WizardMode[this.ActiveStep].includes('CREATE')) {
			return false;
		}
		if (wizard.WizardInstance[`wzgrid${wizard.ActiveStep}`] !== undefined) {
			const module = wizard.WizardCurrentModule[wizard.ActiveStep];
			wizard.WizardInstance[`wzgrid${wizard.ActiveStep}`].clear();
			wizard.WizardInstance[`wzgrid${wizard.ActiveStep}`].setRequestParams({
				formodule: module,
				filterrows: true,
				step: wizard.ActiveStep,
				showdata: true,
			});
			setTimeout(function () {
				wizard.WizardInstance[`wzgrid${wizard.ActiveStep}`].setPerPage(parseInt(20));
			}, 100);
		}
	}

	FilterGrid(ev) {
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
		ev.instance.clear();
		ev.instance.setRequestParams({
			formodule: wizard.WizardCurrentModule[wizard.ActiveStep],
			filtergrid: true,
			step: wizard.ActiveStep,
			forColumn: ev.columnName,
			value: ev.filterState[0].state[0].value,
			operator: operatorData[ev.filterState[0].state[0].code],
		});
		ev.instance.setPerPage(parseInt(20));
	}

	/**
	 * Clear active filter in Wizard Listview.
	 * @param {String} step to clear the filter
	 */
	ClearFilter(step) {
		this.WizardInstance[`wzgrid${step}`].clear();
		this.WizardInstance[`wzgrid${step}`].setRequestParams({
			formodule: wizard.WizardCurrentModule[step],
			step: step,
			showdata: true
		});
		this.WizardInstance[`wzgrid${step}`].setPerPage(parseInt(20));
		this.CheckedRows[step] = [];
	}

	/**
	 * Perform an inline edit in backend.
	 * @param {Object} tuigrid object
	 */
	InlineEdit(ev) {
		let rowkey = ev.rowKey;
		let fieldName = ev.columnName;
		let fieldValue = ev.value;
		let recordid = ev.instance.getValue(rowkey, 'id');
		let modulename = ev.instance.getValue(rowkey, '__modulename');
		let page = wizard.WizardInstance[`wzgrid${wizard.ActiveStep}`].getPagination()._currentPage;
		if (modulename != '' && recordid != null) {
			let fileurl = 'module=Utilities&action=UtilitiesAjax&file=MasterDetailGridLayoutActions&mdaction=inline_edit&recordid='+recordid+'&rec_module='+modulename+'&fldName='+fieldName+'&fieldValue='+encodeURIComponent(fieldValue);
			if (recordid != '') {
				GridValidation(recordid, modulename, fieldName, fieldValue).then(function (msg) {
					if (msg == '%%%OK%%%') {
						jQuery.ajax({
							method: 'POST',
							url: 'index.php?' + fileurl
						}).done(function (response) {
							let res = JSON.parse(response);
							if (res.success) {
								ev.instance.readData(page);
								wizard.CheckRows(page);
							} else {
								ldsNotification.show(alert_arr.ERROR, alert_arr.Failed, 'error');
							}
						});
					} else {
						ldsNotification.show(alert_arr.ERROR, msg, 'error');
					}
				});
			}
		}
	}

	/**
	 * Duplicate a selected row in a specified step.
	 * @param {Object} button instance
	 */
	async DuplicateRow(ev = '') {
		let type = 'next';
		if (ev != '') {
			type = ev.target.dataset.type;
		}
		if (type == 'back') {
			return true;
		}
		//move to step+2 if the product in the previous step is not selected
		if (this.IdVal().length == 0) {
			this.MoveToStep('');
			return true;
		}
		let url = `${this.url}&wizardaction=Duplicate&subaction=Duplicate`;
		let response = await this.Request(url, 'post', {
			step: this.ActiveStep,
			recordid: this.IdVal(),
			modulename: this.WizardCurrentModule[this.ActiveStep]
		});
		if (response) {
			if (this.CheckedRows[this.ActiveStep] !== undefined && this.CheckedRows[this.ActiveStep].length != 0) {
				this.CreatedRows.push(this.CheckedRows[this.ActiveStep]);
				this.Info(type);
			}
			this.Context = response;
			if (this.MainSelectedId == 0) {
				this.MainSelectedId = response.id;
			}
			if (this.WizardCustomFunction[this.ActiveStep] != '') {
				let holdStep = this.ActiveStep;
				if (this.isSubWizard) {
					this.ActiveStep = this.ActiveStep-1;
				}
				this.CallCustomFunction();
				this.ActiveStep = holdStep;
			}
			this.IsDuplicatedFrom[this.ActiveStep] = 1;
			if (this.CheckedRows[this.ActiveStep] !== undefined) {
				this.CheckedRows[this.ActiveStep][1] = [response];
			}
			if (this.WizardFilterFromContext[this.ActiveStep] != '') {
				this.FilterRows(ev, this.WizardFilterFromContext[this.ActiveStep], this.ActiveStep);
			}
		}
		return response;
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
	MoveToStep(ev, instantShow = false) {
		let type = 'next';
		if (ev != '') {
			type = ev.target.dataset.type;
		}
		if (!instantShow) {
			switch (type) {
			case 'next':
				if (this.ActiveStep + 1 != this.steps) {
					this.ActiveStep++;
					this.el(`seq-${this.ActiveStep}`).style.display = 'block';
					if (this.el(`seq-${this.ActiveStep - 1}`)) {
						this.el(`seq-${this.ActiveStep - 1}`).style.display = 'none';
					}
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
		}
		if (this.Suboperation[this.ActiveStep] == 'ThankYou') {
			this.el('btn-next').style.display = 'none';
			this.el('btn-back').style.display = 'none';
			document.getElementsByClassName('slds-modal__close')[0].style.display = 'none';
			return true;
		}
		if (this.el('btn-back')) {
			if (this.ActiveStep >= 1) {
				this.el('btn-back').removeAttribute('disabled');
			} else {
				this.el('btn-back').setAttribute('disabled', '');
			}
			if (this.WizardGoBack[this.ActiveStep-1] == 0) {
				this.el('btn-back').setAttribute('disabled', '');
			}
		}
		let el = document.getElementById('save-btn');
		if (el !== null) {
			el.innerHTML = '';
		}
		if (!this.ResetWizard[this.ActiveStep] && this.WizardSaveAction[this.ActiveStep]) {
			//create a save button
			let btn = document.createElement('button');
			btn.setAttribute('onclick', 'wizard.Finish(false)');
			btn.innerHTML = alert_arr.JSLBL_SAVE;
			btn.style.float = 'right';
			btn.style.marginLeft = '5px';
			btn.classList.add('slds-button');
			btn.classList.add('slds-button_neutral');
			btn.id = `save-wizard-${this.ActiveStep}`;
			btn.setAttribute('disabled', '');
			el.appendChild(btn);
			this.WizardSaveIsActive[this.ActiveStep] = true;
			//change the color of next button
			if (this.el('btn-next') !== null) {
				if (this.ActiveStep + 1 != this.steps) {
					this.el('btn-next').style.background = '#fff';
					this.el('btn-next').style.borderColor = '#c9c9c9';
					this.el('btn-next').style.color = '#007ad1';
				} else {
					this.el('btn-next').style.background = '#007ad1';
					this.el('btn-next').style.borderColor = '#007ad1';
					this.el('btn-next').style.color = '#fff';
				}
			}
			if (this.CheckedRows[this.ActiveStep] !== undefined && this.CheckedRows[this.ActiveStep].length == 0) {
				this.ButtonsUI('uncheck');
			} else {
				this.ButtonsUI('check');
			}
		} else {
			if (this.el('btn-next') !== null) {
				this.el('btn-next').style.background = '#007ad1';
				this.el('btn-next').style.borderColor = '#007ad1';
				this.el('btn-next').style.color = '#fff';
			}
		}
		if (this.el('btn-next')) {
			if (this.ActiveStep + 1 == this.steps && type == 'next') {
				this.el('btn-next').innerHTML = alert_arr.JSLBL_FINISH;
				setTimeout(function () {
					wizard.el('btn-next').setAttribute('onclick', 'wizard.Finish()');
				}, 200);
				return false;
			} else {
				this.el('btn-next').innerHTML = alert_arr.JSLBL_NEXT;
				this.el('btn-next').removeAttribute('onclick');
			}
		}
	}

	/**
	 * Render custom buttons in the first step of wizard
	 */
	RenderButtons() {
		let el = document.getElementById('save-btn');
		if (el !== null) {
			el.innerHTML = '';
		}
		let btn = document.createElement('button');
		btn.setAttribute('onclick', 'wizard.Finish(false)');
		btn.innerHTML = alert_arr.JSLBL_SAVE;
		btn.style.float = 'right';
		btn.style.marginLeft = '5px';
		btn.classList.add('slds-button');
		btn.classList.add('slds-button_neutral');
		btn.id = `save-wizard-${this.ActiveStep}`;
		btn.setAttribute('disabled', '');
		el.appendChild(btn);
		this.WizardSaveIsActive[this.ActiveStep] = true;		
	}

	/**
	 * Enable/disable custom buttons
	 * @param {String} mode check | uncheck
	 */
	ButtonsUI(mode) {
		let el = this.el(`save-wizard-${this.ActiveStep}`);
		if (!el) {
			return false;
		}
		let empty = [];
		for (let i in this.CheckedRows[this.ActiveStep]) {
			if (this.CheckedRows[this.ActiveStep][i].length > 0) {
				empty.push(false);
			}
		}
		const checkedRows = this.WizardInstance[`wzgrid${this.ActiveStep}`].getCheckedRows();
		if (checkedRows.length == 0) {
			empty = [];
		}
		if (mode == 'check') {
			if (empty.length != 0) {
				el.removeAttribute('disabled');
				el.style.background = '#007ad1';
				el.style.color = '#fff';
			}
		} else {
			if (empty.length == 0) {
				el.style.background = '#fff';
				el.style.color = '#c9c9c9';
				el.setAttribute('disabled', '');
			}
		}
	}

	/**
	 * Save selected rows in CheckedRows array for every step and page in grid
	 * @param {String} mode check | uncheck
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
	 * Edit or Duplicate a record.
	 * @param {String} id to upsert
	 * @param {String} mode edit | duplicate
	 */
	Upsert(id, action = '', moduleName = '') {
		let url = `&step=${this.ActiveStep}&WizardView=true`;
		if (action == 'edit') {
			url += `&Module_Popup_Edit=1&wizardaction=${action}`;
		} else if (action == 'duplicate') {
			url += `&Module_Popup_Edit=1&isDuplicate=true&wizardaction=${action}`;
		}
		let module = this.WizardCurrentModule[this.ActiveStep];
		if (moduleName != '') {
			module = moduleName;
		}
		window.open('index.php?module='+module+'&action=EditView&record='+id+url+'&cbfromid='+id, null, cbPopupWindowSettings + ',dependent=yes');
	}

	/**
	 * Delete a selected row in Wizard Listview.
	 * @param {String} id to delete
	 * @param {String} module name
	 */
	Delete(id, modulename = '') {
		if (confirm(alert_arr.ARE_YOU_SURE)) {
			let module = this.WizardCurrentModule[this.ActiveStep];
			if (modulename != '' && module == null) {
				module = modulename;
			}
			var url = `${this.url}&wizardaction=Delete&subaction=DeleteRecords`;
			this.Request(url, 'post', {
				recordid: id,
				modulename: module
			}).then(function (response) {
				if (response) {
					ldsNotification.show(alert_arr.LBL_SUCCESS, alert_arr.LBL_DELETE_SUCCESS, 'success');
					let page = wizard.WizardInstance[`wzgrid${wizard.ActiveStep}`].getPagination();
					if (page === null) {
						wizard.WizardInstance[`wzgrid${wizard.ActiveStep}`].readData(1, {
							page: 1
						}, true);
					} else {
						wizard.WizardInstance[`wzgrid${wizard.ActiveStep}`].readData(page._currentPage, {
							page: page._currentPage
						}, true);
					}
				} else {
					ldsNotification.show(alert_arr.ERROR, alert_arr.Failed, 'error');
				}
			});
		}
	}

	save(step, action = 'edit') {
		if (action == 'duplicate' && this.WizardRequiredAction[this.ActiveStep] == 'duplicate') {
			this.IsDuplicatedFrom[this.ActiveStep] = 1;
		}
		let page = this.WizardInstance[`wzgrid${step}`].getPagination();
		const totalCount = this.WizardInstance[`wzgrid${step}`].getPaginationTotalCount();
		const totalPage = Math.ceil(totalCount/20);
		if (action == 'duplicate') {
			page._currentPage = totalPage;
		}
		setTimeout(function () {
			if (wizard.WizardRequiredAction[wizard.ActiveStep] == 'duplicate' && wizard.WizardMode[wizard.ActiveStep] == 'SELECTPRODUCT') {
				wizard.WizardInstance[`wzgrid${wizard.ActiveStep}`].clear();
				let reqParams = {
					page: 1,
					step: wizard.ActiveStep,
					mode: wizard.WizardMode[wizard.ActiveStep]
				};
				if (wizard.IsDuplicatedFrom[wizard.ActiveStep] == 1) {
					reqParams.query = '';
					reqParams.required_action = 'duplicate';
				}
				wizard.WizardInstance[`wzgrid${wizard.ActiveStep}`].setRequestParams(reqParams);
				wizard.WizardInstance[`wzgrid${wizard.ActiveStep}`].setPerPage(parseInt(20));
			} else {
				if (page === null) {
					wizard.WizardInstance[`wzgrid${step}`].readData(1, {
						page: 1
					}, true);
				} else {
					wizard.WizardInstance[`wzgrid${step}`].readData(page._currentPage, {
						page: page._currentPage
					}, true);
				}
			}
			if (page !== null) {
				wizard.CheckRows(page._currentPage);
			}
		}, 1000);
	}

	/**
	 * Check all rows in grid when we move in pages
	 * @param {Object} grid event
	 */
	CheckRows(ev) {
		if (this.CheckedRows[this.ActiveStep] === undefined) {
			return false;
		}
		let pnum = 1;
		if (typeof ev == 'number') {
			pnum = ev;
		} else {
			pnum = ev.page;
		}
		const _currentPage = this.CheckedRows[this.ActiveStep][pnum];
		for (let i in _currentPage) {
			this.WizardInstance[`wzgrid${this.ActiveStep}`].check(_currentPage[i].rowKey);
		}
	}

	/**
	 * Hide last step in Wizard
	 */
	Hide(skipStep = '') {
		for (var i = 1; i < this.steps; i++) {
			if (skipStep != '' && i == skipStep) {
				continue;
			}
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
			ldsNotification.show(alert_arr.ERROR, alert_arr.LBL_SELECT_MORE_ROWS, 'error');
			return false;
		}
		this.WizardInstance[`wzgrid${step}`].removeRows(rowKeys);
	}

	/**
	 * Call custom functions defined in the Wizard map.
	 * @param {Object} button instance
	 */
	async CallCustomFunction(ev = '') {
		let type = 'next';
		if (ev != '') {
			type = ev.target.dataset.type;
		}
		if (type == 'back') {
			delete this.IsDuplicatedFrom[this.ActiveStep-1];
			return true;
		}
		if (this.SubWizardInfoMainId != 0) {
			//make sure we have the mainid when `save` action is set in the first step
			this.MainSelectedId = parseInt(this.SubWizardInfoMainId);
		}
		if (this.Context.id === undefined) {
			this.Context['id'] = this.MainSelectedId;
		}
		if (this.WizardCustomFunction[this.ActiveStep] == '') {
			return true;
		}
		const url = `${this.url}&wizardaction=CustomCreate&subaction=${this.WizardCustomFunction[this.ActiveStep]}&step=${this.ActiveStep}&rid=${this.Context.id}&mainid=${this.MainSelectedId}`;
		let rows = [];
		for (let i in this.CheckedRows[this.ActiveStep]) {
			let ids = [];
			for (let j in this.CheckedRows[this.ActiveStep][i]) {
				ids.push(this.CheckedRows[this.ActiveStep][i][j].id);
			}
			rows.push(ids);
		}
		if (this.CheckedRows[this.ActiveStep] !== undefined && this.CheckedRows[this.ActiveStep].length != 0) {
			this.CreatedRows.push(this.CheckedRows[this.ActiveStep]);
		}
		await this.Request(url, 'post', rows).then(function (response) {
			if (response) {
				wizard.Info(type, 'save');
				if (response.length > 0) {
					ldsNotification.show(alert_arr.LBL_SUCCESS, alert_arr.LBL_CREATED_SUCCESS, 'success');
				}
			} else {
				ldsNotification.show(alert_arr.ERROR, alert_arr.LBL_WRONG, 'error');
			}
			if (wizard.el(`save-wizard-${wizard.ActiveStep}`) !== null) {
				wizard.el(`save-wizard-${wizard.ActiveStep}`).innerHTML = alert_arr.JSLBL_SAVE;
			}
		});
	}

	/**
	 * Create ProductComponent records. *Specific use case
	 * @param {Object} event
	 */
	async Create_ProductComponent(ev) {
		let type = 'next';
		if (ev != '') {
			type = ev.target.dataset.type;
		}
		if (type == 'back') {
			return true;
		}
		let rows = [];
		//get FROMID
		rows.push([this.MainSelectedId]);
		//get TOIDS
		if (this.CheckedRows[this.ActiveStep] == undefined && this.WizardMode[this.ActiveStep+1] == 'ListView') {
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
		let response = await this.Request(url, 'post', rows);
		if (response != 'no_create') {
			if (response) {
				ldsNotification.show(alert_arr.LBL_SUCCESS, alert_arr.LBL_CREATED_SUCCESS, 'success');
				this.FilterDataForStep();
			} else {
				ldsNotification.show(alert_arr.ERROR, alert_arr.LBL_WRONG, 'error');
			}
		}
		this.loader('hide');
		return true;
	}

	/**
	 * Filter all Qutes and InventoryDetals for MassCreate. *Specific use case
	 * @param {Object} event
	 */
	MassCreateGrid(ev, operation) {
		let type = 'next';
		if (ev != '') {
			type = ev.target.dataset.type;
		}
		let module = this.WizardCurrentModule[this.ActiveStep+1];
		if (type == 'back' || this.WizardInstance[`wzgrid${this.ActiveStep}`] === undefined) {
			return true;
		}
		if (!this.CheckSelection(ev)) {
			return false;
		}
		const checkedRows = this.WizardInstance[`wzgrid${this.ActiveStep}`].getCheckedRows();
		if (checkedRows.length == 0 && !this.isModal) {
			ldsNotification.show(alert_arr.ERROR, alert_arr.LBL_SELECT_MORE_ROWS, 'error');
			return false;
		}
		if (operation == 'MASSCREATE' && checkedRows.length != 1 && this.ActiveStep == 0 && !this.isModal) {
			ldsNotification.show(alert_arr.ERROR, alert_arr.LBL_SELECT_ROW, 'error');
			return false;
		}
		if (this.ActiveStep == 0) {//second step
			const findColName = this.RelatedFieldName();
			if (findColName == '') {
				ldsNotification.show(alert_arr.ERROR, alert_arr.LBL_UNABLE_TO_FILTER, 'error');
				return false;
			}
			let ids = this.IdVal();
			if (this.isModal) {
				ids = [this.RecordID];
			}
			if (ids.length == 0) {
				ldsNotification.show(alert_arr.ERROR, alert_arr.LBL_UNABLE_TO_FILTER, 'error');
				return false;
			}
			if (typeof findColName == 'string' && findColName.indexOf('.') > -1) {
				module = this.WizardCurrentModule[this.ActiveStep];
			}
			this.WizardInstance[`wzgrid${this.ActiveStep+1}`].clear();
			this.WizardInstance[`wzgrid${this.ActiveStep+1}`].setRequestParams({
				forids: JSON.stringify(ids),
				formodule: module,
				forfield: findColName,
				parentid: this.RecordID,
				currentid: 0,
				showdata: true,
				conditionquery: this.WizardConditionQuery[`${this.ActiveStep+1}`]
			});
			this.WizardInstance[`wzgrid${this.ActiveStep+1}`].setPerPage(parseInt(20));
		}
		if (operation == 'MASSCREATE' && this.steps-2 == this.ActiveStep) {
			this.Mapping(0, 1);
			return true;
		}
		if (operation == 'MASSCREATETREEVIEW' && this.steps-1 == this.ActiveStep && type == 'next') {
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
		if (this.steps-1 == this.ActiveStep && type == 'next') {//mass create in last step
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
		let parent = this.IdVal(s1);
		if (this.isModal) {
			parent = [this.RecordID];
		}
		const ids = [
			{
				id: parent,
				module: this.WizardCurrentModule[s1]
			},
			{
				id: this.IdVal(s2),
				module: this.WizardCurrentModule[s2]
			}
		];
		const url = `${this.url}&wizardaction=Mapping&formodule=${this.MCModule}`;
		this.Request(url, 'post', ids).then(function (response) {
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
			let	checkedRows = this.WizardInstance[`wzgrid${this.ActiveStep}`].getCheckedRows();
			const wizardcolumns = JSON.parse(this.WizardColumns[this.ActiveStep]);
			let data = [];
			for (let j in checkedRows) {
				let row = {};
				for (let i in wizardcolumns) {
					row[wizardcolumns[i].name] = checkedRows[j][wizardcolumns[i].name];
					if (checkedRows[j][`${wizardcolumns[i].name}_raw`] !== undefined) {
						row[wizardcolumns[i].name] = checkedRows[j][`${wizardcolumns[i].name}_raw`];
					}
				}
				data.push(row);
			}
			let parent = this.IdVal(0);
			if (this.isModal) {
				parent = [this.RecordID];
			}
			filterData = [
				{
					id: parent,
					relmodule: this.WizardCurrentModule[0],
					relatedRows: this.IdVal(this.ActiveStep-1)
				},
				{
					data: data,
					createmodule: this.MCModule
				}
			];
		}
		if (Object.keys(filterData).length === 0) {
			ldsNotification.show(alert_arr.ERROR, alert_arr.LBL_WRONG, 'error');
			return false;
		}
		this.loader('show');
		const url = `${this.url}&wizardaction=MassCreate&formodule=${this.MCModule}&subaction=${this.WizardMode[0]}`;
		this.Request(url, 'post', filterData).then(function (response) {
			if (response) {
				ldsNotification.show(alert_arr.LBL_SUCCESS, alert_arr.LBL_CREATED_SUCCESS, 'success');
				if (wizard.isModal) {
					RLInstance[wizard.gridInstance].readData(1);
					ldsModal.close();
					wizard.ActiveStep = 0;
					wizard.CheckedRows = [];
					wizard.GridData = [];
					wizard.GroupData = [];
					localStorage.removeItem('currentWizardActive');
				} else {
					setTimeout(function () {
						location.reload(true);
					}, 1000);
				}
			} else {
				ldsNotification.show(alert_arr.ERROR, alert_arr.LBL_WRONG, 'error');
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
			columns.push(wizardcolumns[i]);
			filtercolumns.push(wizardcolumns[i].name);
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

	CleanWizard() {
		const btnswitch = document.querySelectorAll('#codewithhbtnswitch');
		if (btnswitch.length > 0) {
			for (let i in btnswitch) {
				if (typeof btnswitch[i] == 'object') {
					btnswitch[i].remove();
				}
			}
		}
		document.getElementsByClassName('slds-modal__header')[0].style.background = '#dfdfdf';
	}

	ActionButtons() {
		const footer = document.getElementsByClassName('slds-modal__footer');
		let buttons = '';
		if (!this.isSubWizard) {
			buttons += `
			<button type="button" class="slds-button slds-button_brand slds-path__mark-complete" disabled id="btn-back" data-type="back" style="float:left">
				<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevronleft"></use>
				</svg>
				${alert_arr.JSLBL_BACK}
			</button>
			<button type="button" class="slds-button slds-button_brand slds-path__mark-complete slds-float_right" id="btn-next" data-type="next">
				${alert_arr.JSLBL_NEXT}
				<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevronright"></use>
				</svg>
			</button>`;
		}
		buttons += `<div id="save-btn" class="slds-float_right"></div>`;
		footer[footer.length-1].innerHTML = buttons;
	}

	async SaveForm() {
		let mandatoryFields = Array();
		let data = Array();
		if (this.FormFields.length > 0) {
			for (let i in this.FormFields) {
				let val = this.el(`${this.FormFields[i]}_formtemplate_${this.ActiveStep}`);
				let block = this.el(`${this.FormFields[i]}_form_block`);
				if (val.hasAttribute('required') && val.value == '') {
					val.classList.add('slds-has-error');
					mandatoryFields.push(true);
					if (this.el(`${this.FormFields[i]}_form_error`) == null) {
						let error = document.createElement('div');
						error.id = `${this.FormFields[i]}_form_error`;
						error.innerHTML = `Complete <strong>${val.dataset.label}</strong> field`;
						error.style.color = 'red';
						block.appendChild(error);
					}
				} else {
					if (this.el(`${this.FormFields[i]}_form_error`) != null) {
						this.el(`${this.FormFields[i]}_form_error`).remove();
					}
					val.classList.remove('slds-has-error');
					delete mandatoryFields[this.FormFields[i]];
				}
				data[this.FormFields[i]] = val.value;
			}
		}
		if (mandatoryFields.length == 0) {
			let id = await this.CreateFormRow(data);
			if (id) {
				this.FormIDS[this.ActiveStep] = id;
				if (this.TreeViewID[this.ActiveStep] === undefined) {
					this.TreeViewID[this.ActiveStep] = [];
				}
				this.TreeViewID[this.ActiveStep].push(id);
			}
			return id;
		}
		return false;
	}

	/**
	 * Create a new row from Form in the Wizard Form Template
	 * @param {Array}
	 */
	async CreateFormRow(data) {
		const url = `${this.url}&wizardaction=CreateForm&subaction=CreateForm`;
		const array = Object.entries(data).map(([key, value]) => ({ key, value }));
		return await this.Request(url, 'post', {
			data: JSON.stringify(array),
			recordid: this.FormIDS[this.ActiveStep] !== undefined ? this.FormIDS[this.ActiveStep] : 0,
			modulename: this.FormModule[this.ActiveStep]
		});
	}

	/**
	 * Init calendar view
	 */
	CalendarView() {
		const container = document.getElementById(`calendar-${this.ActiveStep+1}`);
		this.Calendar[this.ActiveStep+1] = new FullCalendar.Calendar(container, {
			locale: gVTuserLanguage.split('_')[0],
			initialView: 'timeGridWeek',
			allDaySlot: false,
			slotDuration: '00:30:00',
			slotLabelInterval: '00:30:00',
			headerToolbar: {
				left: 'prev,next',
				center: 'title',
				right: 'timeGridWeek,timeGridDay'
			},
			buttonText: {
				today: mod_alert_arr.LBL_TODAY,
				week: mod_alert_arr.LBL_WEEK
			},
			editable: true,
			eventResizableFromStart: false,
			eventResize: function (ev, dayDelta, revertFunc) {
				const url_ = `${wizard.url}&wizardaction=UpdateEvent&subaction=UpdateEvent`;
				wizard.Request(url_, 'post', {
					'eventId': ev.event._def.publicId,
					'dateStart': wizard.ConvertDate(ev.event._instance.range.start),
					'dateEnd': wizard.ConvertDate(ev.event._instance.range.end),
				}).then(function () {
					wizard.RenderEvents();
				});
			},
			eventDrop: function (ev, dayDelta, revertFunc) {
				const url_ = `${wizard.url}&wizardaction=UpdateEvent&subaction=UpdateEvent`;
				wizard.Request(url_, 'post', {
					'eventId': ev.event._def.publicId,
					'dateStart': wizard.ConvertDate(ev.event._instance.range.start),
					'dateEnd': wizard.ConvertDate(ev.event._instance.range.end),
				}).then(function () {
					wizard.RenderEvents();
				});
			},
			eventClick: function(ev, jsEvent, view) {
				let eurl = `index.php?module=cbCalendar&action=EditView&Module_Popup_Edit=1&record=${ev.event._def.publicId}&cbfromid=${ev.event._def.publicId}`;
				window.open(eurl, null, cbPopupWindowSettings + ',dependent=yes');
			},
			dateClick: function(info) {
				let template = `
				<div class="slds-dropdown-trigger slds-dropdown-trigger_click slds-is-open" style="margin-left: -18%;">
					<button onclick="wizard.HideEvents()" class="slds-button slds-button_icon slds-button_icon-border-filled" aria-haspopup="true" aria-expanded="true" title="Hide">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#hide"></use>
						</svg>
						<span class="slds-assistive-text">Hide</span>
					</button>
					<div class="slds-dropdown slds-dropdown_left slds-dropdown_small">
						<ul class="slds-dropdown__list" role="menu">
							<li class="slds-dropdown__item" role="presentation">
								<a onclick="wizard.CreateEvent('${info.dateStr}', 'Call')" role="menuitem" tabindex="0">
									<span class="slds-truncate">
										<svg class="slds-icon slds-icon_x-small slds-icon-text-default slds-m-right_x-small" aria-hidden="true">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#call"></use>
										</svg>
										${mod_alert_arr.LBL_CALL}
									</span>
								</a>
							</li>
							<li class="slds-dropdown__item" role="presentation">
								<a onclick="wizard.CreateEvent('${info.dateStr}', 'Meeting')" role="menuitem" tabindex="0">
									<span class="slds-truncate">
										<svg class="slds-icon slds-icon_x-small slds-icon-text-default slds-m-right_x-small" aria-hidden="true">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#meet_focus_presenter"></use>
										</svg>
										${mod_alert_arr.LBL_MEETING}
									</span>
								</a>
							</li>
							<li class="slds-dropdown__item" role="presentation">
								<a onclick="wizard.CreateEvent('${info.dateStr}', 'Task')" role="menuitem" tabindex="0">
									<span class="slds-truncate">
										<svg class="slds-icon slds-icon_x-small slds-icon-text-default slds-m-right_x-small" aria-hidden="true">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#user"></use>
										</svg>
										${mod_alert_arr.LBL_TASK}
									</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
				`;
				wizard.el('global-modal-eventcreator').innerHTML = template;
			}
		});
		this.Calendar[this.ActiveStep+1].render();
		return true;
	}

	/**
	 * Create an event in the Calendar.
	 * @param {String} date
	 * @param {String} event type
	 */
	CreateEvent(dateStr, type) {
		this.HideEvents();
		let url = `index.php?action=EditView&module=cbCalendar&Module_Popup_Edit=1&dtstart=${this.ConvertDate(dateStr)}&dtend=${this.ConvertDate(dateStr)}&activitytype=${type}&rel_id=${this.FormIDS[this.ActiveStep-1]}`;
		window.open(url, null, cbPopupWindowSettings + ',dependent=yes');
	}

	HideEvents() {
		this.el('global-modal-eventcreator').innerHTML = '';
	}

	/**
	 * Renders all created events in Calendar.
	 */
	async RenderEvents() {
		if (this.Calendar[this.ActiveStep] == undefined) {
			return true;
		}
		const url = `${this.url}&wizardaction=GetEvents&subaction=GetEvents`;
		let res = await this.Request(url, 'post', {
			recordid: this.FormIDS[this.ActiveStep-1]
		});
		this.Calendar[this.ActiveStep].removeAllEvents();
		for (let i in res) {
			let backgroundColor = 'blue';
			let activitytype = res[i].activitytype;
			if (activitytype == 'Meeting') {
				backgroundColor = 'orange';
			} else if (activitytype == 'Task') {
				backgroundColor = 'green';
			} else if (activitytype == 'Call') {
				backgroundColor = 'brown';
			}
			this.Calendar[this.ActiveStep].addEvent({
				id: res[i].activityid,
				title: res[i].subject,
				start: new Date(`${res[i].date_start}T${res[i].time_start}`),
				end: new Date(`${res[i].due_date}T${res[i].time_end}`),
				backgroundColor: backgroundColor,
				borderColor: backgroundColor,
				color: 'black',
			});
		}
	}

	ConvertDate(dateStr) {
		const date = new Date(dateStr);
		const yyyy = date.getFullYear();
		const mm = String(date.getMonth() + 1).padStart(2, '0');
		const dd = String(date.getDate()).padStart(2, '0');
		const h = String(date.getHours()).padStart(2, '0');
		const i = String(date.getMinutes()).padStart(2, '0');
		const s = String(date.getSeconds()).padStart(2, '0');
		const formattedDate = `${yyyy}-${mm}-${dd} ${h}:${i}:${s}`;
		return formattedDate;
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
			el. innerHTML += '<div class="slds-button-group" role="group">';
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
			el. innerHTML += '</div>';
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

class TreeViewActions {

	constructor(props) {
		let el;
		let deleteRow = '';
		let rowKey = props.rowKey;
		let recordid = props.grid.getValue(rowKey, 'record_id') || '';
		let module = props.grid.getValue(rowKey, 'record_module');
		let parent = props.grid.getValue(rowKey, '__parent') || false;
		el = document.createElement('span');
		if (!parent) {
			deleteRow = `
			<li class="slds-dropdown__item" role="presentation">
				<a onclick="wizard.Delete(${recordid}, '${module}')" title="${alert_arr['JSLBL_Delete']}">
					<svg class="slds-button__icon slds-button__icon_left cbds-color-compl-red--sober" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
					</svg>
					<span class="slds-truncate cbds-color-compl-red--sober">${alert_arr['JSLBL_Delete']}</span>
				</a>
			</li>
			`;
		}
		let actions = `
		<div class="slds-button-group" role="group">
			<div class="slds-dropdown-trigger slds-dropdown-trigger_hover slds-is-open">
				<button class="slds-button slds-button_icon slds-button_icon-border-filled" aria-haspopup="true" aria-expanded="true">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#threedots"></use>
					</svg>
					<span class="slds-assistive-text">${alert_arr.LBL_SHOW_MORE}</span>
				</button>
				<div class="slds-dropdown slds-dropdown_right slds-dropdown_actions" style="width: 9rem;">
					<ul class="slds-dropdown__list" role="menu">
						<li class="slds-dropdown__item" role="presentation">
							<a onclick="wizard.Upsert(${recordid}, 'edit', '${module}')" title="${alert_arr['JSLBL_Edit']}">
								<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
								</svg>
								<span class="slds-truncate">${alert_arr['JSLBL_Edit']}</span>
							</a>
						</li>
						${deleteRow}
					</ul>
				</div>
			</div>
		</div>`;
		el.innerHTML = actions;
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

var wizard = new WizardComponent();