/*************************************************************************************************
 * Copyright 2023 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
class Settings {

	constructor() {
		this.list_modules = [];
		this.list_modules_raw = [];
		this.list_modules_icons = [];
		this.Data = [];
		this.FilteredData = [];
		this.Grid = false;
		this.LastActive = '';
	}

	async Workflows(label, modulename) {
		this.LastActive = modulename;
		let rs = await this.Modules(modulename);
		ldsModal.show(label, rs, 'large');
		document.getElementsByClassName('slds-modal__footer')[0].remove();
		document.getElementById('global-modal-container__content').style.background = '#f3f3f3';
		document.getElementById('global-modal-container__content').style.height = '100%';
		this.GridView(modulename);
	}

	async Modules(current = '') {
		let url = 'index.php?module=com_vtiger_workflow&action=com_vtiger_workflowAjax&file=getJSON&page=all';
		this.Data = JSON.parse(await Request(url, 'get'));
		this.Data['data'].forEach(function(row) {
			if (!settings.list_modules.includes(row.Module)) {
				settings.list_modules.push(row.Module);
				settings.list_modules_raw.push(row.ModuleName);
				settings.list_modules_icons.push(row.ModuleIcon);
			}
		});
		let mods = `
		<div class="slds-vertical-tabs" style="border: 0px !important;height:100% !important">
			<ul id="Workflow_Modules" class="slds-vertical-tabs__nav cbds-scrollbar" style="max-height: 700px;overflow: hidden;overflow-y: auto;">
				<input type="text" class="slds-input" placeholder="Search" style="width: 95%" oninput="settings.SearchModules(this)">
				${this.RenderModules(current)}
			</ul>
			<div class="slds-vertical-tabs__content slds-show cbds-scrollbar" id="workflow-treeview" style="overflow-y: auto;max-height: 700px;overflow-x: hidden;">
				${this.SearchBar()}
			</div>
		</div>`;
		return mods;
	}

	SearchModules(ev) {
		let modules = document.getElementById('Workflow_Modules').querySelectorAll('li');
		for (let i in modules) {
			if (!modules[i].dataset.value.toLowerCase().includes(ev.value)) {
				modules[i].style.display = 'none';
			} else {
				modules[i].style.display = 'block';
			}
		}
	}

	ShowWorkflow(forModule) {
		document.getElementById(`wf_module_${forModule}`).classList.add('slds-is-active');
		document.getElementById(`wf_module_${this.LastActive}`).classList.remove('slds-is-active');
		this.LastActive = forModule;
		document.getElementById('workflow-treeview').innerHTML = this.SearchBar();
		this.GridView(forModule);
	}

	GridView(forModule) {
		if (this.Grid) {
			this.Grid.destroy();
		}
		this.Grid = new tui.Grid({
			el: document.getElementById('workflow-treeview'),
			data: this.GetData(),
			treeColumnOptions: {
				name: 'Description',
				useCascadingCheckbox: true,
				useIcon: false
			},
			columns: [
				{
					header: 'Workflow',
					name: 'Description',
					renderer: {
						type: WorkflowRender,
						options: {
							fieldname: 'Description',
							forModule: this.LastActive
						}
					},
					width: 300
				},
				{
					header: 'Type',
					name: 'tasktypelabel',
					renderer: {
						type: WorkflowRender,
						options: {
							fieldname: 'Type'
						}
					},
				},
				{
					header: 'Description',
					name: 'summary'
				},
				{
					header: 'Purpose',
					name: 'Purpose'
				},
				{
					header: 'Trigger',
					name: 'Trigger'
				},
				{
					header: 'Status',
					name: 'Status',
					renderer: {
						type: WorkflowRender,
						options: {
							fieldname: 'Status'
						}
					},
					width: 120
				},
				{
					header: 'Tools',
					name: 'tools',
					renderer: {
						type: WorkflowRender,
						options: {
							fieldname: 'Tools'
						}
					},
					width: 100
				},
			],
			useClientSort: true,
			scrollX: false,
			scrollY: false,
			columnOptions: {
				resizable: true
			},
			header: {
				align: 'left',
				valign: 'top'
			},
			contextMenu: null
		});
		tui.Grid.applyTheme('default');
	}

	GetData() {
		let treeData = [];
		this.Data['data'].forEach(function(row) {
			if (settings.LastActive == 'All') {
				treeData.push(settings.FormatData(row));
			} else {
				if (settings.LastActive == row.ModuleName) {
					treeData.push(row);
				}				
			}
		});
		return treeData;
	}

	async GridFilter(ev, field) {
		if (ev.value == '') {
			return this.ResetData();
		}
		this.ClearFields(field);
		let url = `index.php?module=com_vtiger_workflow&action=com_vtiger_workflowAjax&file=getJSON&page=all&forField=${field}&forValue=${ev.value}`;
		let filteredData = [];
		let data = this.Data['data'];
		for (let i in data) {
			if (settings.LastActive != data[i].ModuleName && settings.LastActive != 'All') {
				continue;
			}
			if ((field == 'Status' || field == 'Trigger') && data[i][field] == ev.value) {
				filteredData.push(this.FormatData(data[i]));
			}
			if (field == 'Purpose' && data[i][field].includes(ev.value)) {
				filteredData.push(this.FormatData(data[i]));
			}
			if (field == 'summary' || field == 'tasktypelabel') {
				if (data[i]._children !== undefined) {
					let childs = [];
					for (let j in data[i]._children) {
						if (data[i]._children[j][field] === undefined) {
							continue;
						}
						if (data[i]._children[j][field].includes(ev.value)) {
							childs.push(data[i]._children[j]);
						}
					}
					if (childs.length == 0) {
						continue;
					}
					let fData = this.FormatData(data[i]);
					fData._children = this.FormatChildrens(childs);
					filteredData.push(this.FormatData(fData));
				}
			}
			if (field == 'Description') {//this is a special field that search globally
				if (data[i]._children !== undefined) {
					let childs = [];
					for (let j in data[i]._children) {
						if (data[i]._children[j]['field_value_mapping'] === undefined) {
							continue;
						}
						if (data[i]._children[j]['workflowid_display'] === undefined) {
							continue;
						}
						if (data[i]._children[j]['bmapid_display'] === undefined) {
							continue;
						}
						if (data[i]._children[j]['field_value_mapping'].includes(ev.value) || data[i]._children[j]['workflowid_display'].includes(ev.value) || data[i]._children[j]['bmapid_display'].includes(ev.value)) {
							childs.push(data[i]._children[j]);
						}
					}
					if (childs.length == 0) {
						if (data[i][field].includes(ev.value)) {
							filteredData.push(this.FormatData(data[i]));
						}
						continue;
					}
					let fData = this.FormatData(data[i]);
					fData._children = this.FormatChildrens(childs);
					filteredData.push(this.FormatData(fData));
				} else {
					if (data[i][field].includes(ev.value)) {
						filteredData.push(this.FormatData(data[i]));
					}
				}
			}
		}
		this.Grid.resetData(filteredData);
	}

	ResetData(clearFields = false) {
		let filteredData = [];
		let data = this.Data['data'];
		for (let i in data) {
			if (settings.LastActive != data[i].ModuleName && settings.LastActive != 'All') {
				continue;
			}
			filteredData.push(this.FormatData(data[i]));
		}
		if (clearFields) {
			this.ClearFields();
		}
		this.Grid.resetData(filteredData);
	}

	ClearFields(currentField = '') {
		const fields = ['Description', 'tasktypelabel', 'summary', 'Purpose', 'Trigger', 'Status'];
		for (let f in fields) {
			if (fields[f] == currentField) {
				continue;
			}
			document.getElementById(fields[f]).value = '';
		}
	}

	FormatData(data) {
		return {
			'Description': data.Description,
			'Module': data.Module,
			'ModuleIcon': data.ModuleIcon,
			'ModuleName': data.ModuleName,
			'Purpose': data.Purpose,
			'Record': data.Record,
			'RecordDel': data.RecordDel,
			'RecordDetail': data.RecordDetail,
			'Status': data.Status,
			'StatusRaw': data.StatusRaw,
			'Trigger': data.Trigger,
			'isDefaultWorkflow': data.isDefaultWorkflow,
			'type': data.type,
			'workflow_id': data.workflow_id,
			'_children': this.FormatChildrens(data._children),
		};
	}

	FormatChildrens(data) {
		let rows = [];
		for (let i in data) {
			if (data[i].Record === undefined) {
				continue;
			}
			rows.push({
				'Record': data[i].Record,
				'RecordDel': data[i].RecordDel,
				'Status': data[i].Status,
				'StatusRaw': data[i].StatusRaw,
				'summary': data[i].summary,
				'task_id': data[i].task_id,
				'tasktype': data[i].tasktype,
				'tasktypelabel': data[i].tasktypelabel,
				'type': data[i].type,
				'workflow_id': data[i].workflow_id,
			});
		}
		return rows.length == 0 ? false : rows;
	}

	async DeleteWorkflow(workflow_id, type, rowKey) {
		if (!confirm('Are you sure?')) {
			return false;
		}
		let url = `index.php?module=com_vtiger_workflow&action=com_vtiger_workflowAjax&file=deleteworkflow&workflow_id=${workflow_id}&mode=ajax`;
		if (type == 'task') {
			url = `index.php?module=com_vtiger_workflow&action=com_vtiger_workflowAjax&file=deletetask&task_id=${workflow_id}&mode=ajax`;
		}
		let response = await Request(url, 'post');
		if (response) {
			this.Grid.removeRow(rowKey);
			ldsNotification.show(alert_arr.LBL_SUCCESS, alert_arr.LBL_DELETE_SUCCESS, 'success');
		} else {
			ldsNotification.show(alert_arr.ERROR, alert_arr.LBL_WRONG, 'error');
		}
	}

	RenderModules(current) {
		let mods = `
		<li class="slds-vertical-tabs__nav-item" data-value="All" data-valueraw="All" id="wf_module_All" onclick="settings.ShowWorkflow('All')">
			<a class="slds-vertical-tabs__link" role="tab" tabindex="0" aria-selected="true">
				<span class="slds-vertical-tabs__left-icon">
					<span class="slds-icon_container">
						<svg class="slds-icon slds-icon-text-default slds-icon_small" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#picklist_type"></use>
						</svg>
					</span>
				</span>
				<span class="slds-truncate" title="All">All Modules</span>
			</a>
		</li>
		`;
		this.list_modules.forEach(function(modulename, idx) {
			let className = '';
			if (settings.list_modules_raw[idx] == current) {
				className = 'slds-is-active';
			}
			mods += `
			<li class="slds-vertical-tabs__nav-item ${className}" data-value="${modulename}" data-valueraw="${settings.list_modules_raw[idx]}" id="wf_module_${settings.list_modules_raw[idx]}" onclick="settings.ShowWorkflow('${settings.list_modules_raw[idx]}')">
				<a class="slds-vertical-tabs__link" role="tab" tabindex="0" aria-selected="true">
					<span class="slds-vertical-tabs__left-icon">
						<span class="slds-icon_container">
							<svg class="slds-icon slds-icon-text-default slds-icon_small" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#${settings.list_modules_icons[idx]}"></use>
							</svg>
						</span>
					</span>
					<span class="slds-truncate" title="${modulename}">${modulename}</span>
				</a>
			</li>`;
		});
		return mods;
	}

	RenderOptions(forField, depth = '') {
		let types = [];
		this.Data['data'].forEach(function(row) {
			if (depth == '') {
				if (!types.includes(row[forField]) && (settings.LastActive == row.ModuleName || settings.LastActive == 'All')) {
					types.push(row[forField]);
				}
			} else {
				if (row[depth] !== undefined) {
					for (let i in row[depth]) {
						if (!types.includes(row[depth][i][forField]) && (settings.LastActive == row.ModuleName || settings.LastActive == 'All')) {
							types.push(row[depth][i][forField]);
						}
					}
				}
			}
		});
		let el = '';
		let sortedTypes = types.sort();
		for (let j in sortedTypes) {
			if (sortedTypes[j] === undefined) {
				continue;
			}
			el += `<option value="${sortedTypes[j]}">${sortedTypes[j]}</option>`;
		}
		return el;
	}

	SearchBar() {
		return `
		<div class="slds-page-header">
			<div class="slds-page-header__row">
				<div class="slds-page-header__col-title">
					<div class="slds-media">
						<div class="slds-media__body">
							<div class="slds-grid slds-gutters">
								<div class="slds-col slds-size_3-of-12">
									<label class="slds-form-element__label">Workflow</label>
									<input type="text" class="slds-input" id="Description" oninput="settings.GridFilter(this, 'Description')">
								</div>
								<div class="slds-col" style="width: 14%">
									<label class="slds-form-element__label">Type</label>
									<select type="text" class="slds-select" id="tasktypelabel" onchange="settings.GridFilter(this, 'tasktypelabel')">
										<option value=""></option>
										${this.RenderOptions('tasktypelabel', '_children')}
									</select>
								</div>
								<div class="slds-col" style="width: 14%">
									<label class="slds-form-element__label">Description</label>
									<input type="text" class="slds-input" id="summary" oninput="settings.GridFilter(this, 'summary')">
								</div>
								<div class="slds-col" style="width: 15%">
									<label class="slds-form-element__label">Purpose</label>
									<input type="text" class="slds-input" id="Purpose" oninput="settings.GridFilter(this, 'Purpose')">
								</div>
								<div class="slds-col" style="width: 14%">
									<label class="slds-form-element__label">Trigger</label>
									<select type="text" class="slds-select" id="Trigger" onchange="settings.GridFilter(this, 'Trigger')">
										<option value=""></option>
										${this.RenderOptions('Trigger')}
									</select>
								</div>
								<div class="slds-col" style="width: 10%">
									<label class="slds-form-element__label">Status</label>
									<select type="text" class="slds-select" id="Status" onchange="settings.GridFilter(this, 'Status')">
										<option value=""></option>
										${this.RenderOptions('Status')}
									</select>
								</div>
								<div class="slds-col slds-size_1-of-12">
									<button onclick="settings.ResetData(true)" class="slds-button slds-button_icon slds-button_icon-border-filled" style="margin-top:1.5rem">
										<svg class="slds-button__icon" aria-hidden="true">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#filter"></use>
										</svg>				
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>`;
	}
}

class WorkflowRender {

	constructor(props) {
		let rowKey = props.rowKey;
		const { fieldname } = props.columnInfo.renderer.options;
		let Record = props.grid.getValue(rowKey, 'Record');
		let RecordDel = props.grid.getValue(rowKey, 'RecordDel');
		let isDefaultWorkflow = props.grid.getValue(rowKey, 'isDefaultWorkflow');
		let workflow_id = props.grid.getValue(rowKey, 'workflow_id');
		let type = props.grid.getValue(rowKey, 'type');
		if (type == 'task') {
			workflow_id = props.grid.getValue(rowKey, 'task_id');
		}
		let el = document.createElement('span');
		let actions = ``;
		switch (fieldname) {
		case 'Status':
			let Status = props.grid.getValue(rowKey, 'Status');
			let StatusRaw = props.grid.getValue(rowKey, 'StatusRaw');
			if (StatusRaw) {
				actions += `<span class="slds-badge slds-theme_success">${Status}</span>`;
			} else {
				actions += `<span class="slds-badge slds-theme_error">${Status}</span>`;
			}
			break;
		case 'Tools':
			let deleteWf = '';
			if (!isDefaultWorkflow) {
				deleteWf += `
				<a href="javascript:settings.DeleteWorkflow(${workflow_id}, '${type}', ${rowKey})" class="slds-button slds-button_icon slds-button_icon-border-filled" aria-pressed="false">
					<svg class="slds-button__icon cbds-color-compl-red--sober" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
					</svg>
					<span class="slds-assistive-text">Delete</span>
				</a>`;
			}
			actions += `
			<div class="slds-button-group" role="group">
				<a href="${Record}" target="_blank" class="slds-button slds-button_icon slds-button_icon-border-filled" aria-pressed="false">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#forward_up"></use>
					</svg>
					<span class="slds-assistive-text">Redirect</span>
				</a>
				${deleteWf}
			</div>`;
			break;
		case 'Description':
			let { forModule } = props.columnInfo.renderer.options;
			let Description = props.grid.getValue(rowKey, 'Description');
			if (Description !== null) {
				let modLabel = '';
				if (forModule == 'All') {
					modLabel = `(${props.grid.getValue(rowKey, 'Module')})`;
				}
				actions += `<a href="${Record}">${modLabel} ${Description}</a>`;
			}
			break;
		case 'Type':
			let Type = props.grid.getValue(rowKey, 'tasktypelabel');
			if (Type !== null) {
				actions += `<a href="${Record}">${Type}</a>`;
			}
			break;
		default:
			break;
		}
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

var settings = new Settings();