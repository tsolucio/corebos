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
		this.label = [];
		this.Data = [];
		this.FilteredData = [];
		this.Grid = false;
		this.LastActive = '';
		this.ModuleLabel = '';
		this.ModuleIcon = '';
		this.CheckedRows = [];
		this.Total = {};
		this.Active = {};
		this.Inactive = {};
	}

	async Workflows(label = '', modulename = '') {
		if (label == '') {
			label = alert_arr.LBL_WF_AllModules;
		}
		if (modulename == '') {
			modulename = 'All';
		}
		this.LastActive = modulename;
		this.label = label;
		let rs = await this.Modules(modulename);
		ldsModal.show('', rs, 'large');
		if (document.getElementsByClassName('slds-modal__footer')[0] !== undefined) {
			document.getElementsByClassName('slds-modal__footer')[0].remove();
		}
		this.Element('global-modal-container__content').style.background = '#f3f3f3';
		this.Element('global-modal-container__content').style.height = '100%';
		await this.CreateWorkflow();
		this.GridView(modulename);
		this.Element('currentModule').innerHTML = this.ModuleLabel;
		if (this.ModuleIcon == '') {
			this.ModuleIcon = 'bundle_config';
		}
		this.Element('currentModuleIcon').innerHTML = this.RenderIcon(this.ModuleIcon);
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
			<ul id="Workflow_Modules" class="slds-vertical-tabs__nav cbds-scrollbar" style="max-height: 700px;overflow: hidden;overflow-y: auto;width:10rem !important">
				<input type="text" class="slds-input" placeholder="Search" style="width: 95%" oninput="settings.SearchModules(this)">
				${this.RenderModules(current)}
			</ul>
			<div class="slds-vertical-tabs__content slds-show cbds-scrollbar" id="workflow-treeview" style="overflow-y: auto;max-height: 700px;overflow-x: hidden;">
				${this.SearchBar()}
			</div>
			<div id="new_workflow" style="width: 12rem"></div>
		</div>`;
		return mods;
	}

	SearchModules(ev) {
		let modules = this.Element('Workflow_Modules').querySelectorAll('li');
		for (let i in modules) {
			if (!modules[i].dataset.value.toLowerCase().includes(ev.value)) {
				modules[i].style.display = 'none';
			} else {
				modules[i].style.display = 'block';
			}
		}
	}

	ShowWorkflow(ev, forModule) {
		this.Element(`wf_module_${forModule}`).classList.add('slds-is-active');
		if (this.Element(`wf_module_${this.LastActive}`) !== null) {
			this.Element(`wf_module_${this.LastActive}`).classList.remove('slds-is-active');
		}
		this.LastActive = forModule;
		this.Element('workflow-treeview').innerHTML = this.SearchBar();
		this.Element('currentModule').innerHTML = ev.dataset.value;
		this.Element('currentModuleIcon').innerHTML = this.RenderIcon(ev.dataset.icon);
		this.CreateWorkflow();
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
					header: '-',
					name: 'checkbox',
					renderer: {
						type: WorkflowRender,
						options: {
							fieldname: 'checkbox',
							forModule: this.LastActive
						}
					},
					width: 5
				},
				{
					header: alert_arr.LBL_WF_Workflow,
					name: 'Description',
					renderer: {
						type: WorkflowRender,
						options: {
							fieldname: 'Description',
							forModule: this.LastActive
						}
					},
					width: 270
				},
				{
					header: alert_arr.LBL_WF_Type,
					name: 'tasktypelabel',
					renderer: {
						type: WorkflowRender,
						options: {
							fieldname: 'Type'
						}
					},
				},
				{
					header: alert_arr.LBL_WF_Description,
					name: 'summary'
				},
				{
					header: alert_arr.LBL_WF_Purpose,
					name: 'Purpose'
				},
				{
					header: alert_arr.LBL_WF_Trigger,
					name: 'Trigger'
				},
				{
					header: alert_arr.LBL_WF_Status,
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
					header: alert_arr.LBL_WF_Tools,
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
		this.Element('total_workflows').innerHTML = this.Total[this.LastActive].length;
		this.Element('active_workflows').innerHTML = this.Active[this.LastActive].length;
		this.Element('inactive_workflows').innerHTML = this.Inactive[this.LastActive].length;
	}

	GetData() {
		let treeData = [];
		this.Total[settings.LastActive] = [];
		this.Active[settings.LastActive] = [];
		this.Inactive[settings.LastActive] = [];
		this.Data['data'].forEach(function(row) {
			if (settings.LastActive == 'All') {
				treeData.push(settings.FormatData(row));
				settings.Total[settings.LastActive].push(true);
				if (row.StatusRaw) {
					settings.Active[settings.LastActive].push(true);
				} else {
					settings.Inactive[settings.LastActive].push(true);
				}
			} else {
				if (settings.LastActive == row.ModuleName) {
					treeData.push(row);
					settings.Total[settings.LastActive].push(true);
					if (row.StatusRaw) {
						settings.Active[settings.LastActive].push(true);
					} else {
						settings.Inactive[settings.LastActive].push(true);
					}
				}				
			}
		});
		return treeData;
	}

	async GridFilter(ev, field) {
		let filteredData = [];
		let searchFields = [];
		const fields = ['Status', 'Trigger', 'Purpose', 'summary', 'tasktypelabel', 'Description'];
		for (let f in fields) {
			if (this.Element(fields[f]).value != '') {
				searchFields.push(fields[f]);
			}
		}
		if (searchFields.length == 0) {
			return this.ResetData();
		}
		let data = this.Data['data'];
		for (let j in searchFields) {
			if (filteredData.length > 0) {
				data = filteredData;
			}
			let value = this.Element(searchFields[j]).value;
			filteredData = this.SearchData(data, value, searchFields[j]);
		}
		this.Grid.resetData(filteredData);
	}

	SearchData(data, value, field) {
		let filteredData = [];
		for (let i in data) {
			if (this.LastActive != data[i].ModuleName && this.LastActive != 'All') {
				continue;
			}
			if ((field == 'Status' || field == 'Trigger') && data[i][field] == value) {
				filteredData.push(this.FormatData(data[i]));
			}
			if (field == 'Purpose' && data[i][field].includes(value)) {
				filteredData.push(this.FormatData(data[i]));
			}
			if (field == 'summary' || field == 'tasktypelabel') {
				if (data[i]._children !== undefined) {
					let childs = [];
					for (let j in data[i]._children) {
						if (data[i]._children[j][field] === undefined) {
							continue;
						}
						if (data[i]._children[j][field].includes(value)) {
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
						if (data[i]._children[j]['field_value_mapping'].includes(value) || data[i]._children[j]['workflowid_display'].includes(value) || data[i]._children[j]['bmapid_display'].includes(value)) {
							childs.push(data[i]._children[j]);
						}
					}
					if (childs.length == 0) {
						if (data[i][field].includes(value)) {
							filteredData.push(this.FormatData(data[i]));
						}
						continue;
					}
					let fData = this.FormatData(data[i]);
					fData._children = this.FormatChildrens(childs);
					filteredData.push(this.FormatData(fData));
				} else {
					if (data[i][field].includes(value)) {
						filteredData.push(this.FormatData(data[i]));
					}
				}
			}
		}
		return filteredData;
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
			this.Element(fields[f]).value = '';
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
		if (!confirm('Are you sure you want to perform this acttion?')) {
			return false;
		}
		let url = `index.php?module=com_vtiger_workflow&action=com_vtiger_workflowAjax&file=deleteworkflow&workflow_id=${workflow_id}&mode=ajax`;
		if (type == 'task') {
			url = `index.php?module=com_vtiger_workflow&action=com_vtiger_workflowAjax&file=deletetask&task_id=${workflow_id}&mode=ajax`;
		}
		let response = await Request(url, 'post');
		if (response) {
			this.UpdateGrid();
			ldsNotification.show(alert_arr.LBL_SUCCESS, alert_arr.LBL_DELETE_SUCCESS, 'success');
		} else {
			ldsNotification.show(alert_arr.ERROR, alert_arr.LBL_WRONG, 'error');
		}
	}

	RenderIcon(icon) {
		return `
		<span class="slds-icon_container slds-icon-standard-opportunity">
			<svg class="slds-icon slds-page-header__icon" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#${icon}"></use>
			</svg>
		</span>	
		`;
	}

	RenderModules(current) {
		let mods = `
		<li class="slds-vertical-tabs__nav-item" data-value="${alert_arr.LBL_WF_AllModules}" data-icon="bundle_config" data-valueraw="All" id="wf_module_All" onclick="settings.ShowWorkflow(this, 'All')">
			<a class="slds-vertical-tabs__link" role="tab" tabindex="0" aria-selected="true">
				<span class="slds-vertical-tabs__left-icon">
					<span class="slds-icon_container">
						<svg class="slds-icon slds-icon-text-default slds-icon_small" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#picklist_type"></use>
						</svg>
					</span>
				</span>
				<span class="slds-truncate" title="${alert_arr.LBL_WF_AllModules}">${alert_arr.LBL_WF_AllModules}</span>
			</a>
		</li>
		`;
		this.list_modules.forEach(function(modulename, idx) {
			let className = '';
			if (settings.list_modules_raw[idx] == current) {
				className = 'slds-is-active';
				settings.ModuleLabel = modulename;
				settings.ModuleIcon = settings.list_modules_icons[idx];
			}
			mods += `
			<li class="slds-vertical-tabs__nav-item ${className}" data-value="${modulename}" data-icon="${settings.list_modules_icons[idx]}" data-valueraw="${settings.list_modules_raw[idx]}" id="wf_module_${settings.list_modules_raw[idx]}" onclick="settings.ShowWorkflow(this, '${settings.list_modules_raw[idx]}')">
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

	async doActions(type) {
		if (type == 'export') {
			return this.ExportWorkflow();
		}
		if (this.CheckedRows.length == 0) {
			ldsNotification.show(alert_arr.ERROR, alert_arr.SELECT, 'error');
			return false;
		}
		if (!confirm(alert_arr.GENDOC_CONFIRM_ACTION)) {
			return false;
		}
		for (let i in this.CheckedRows) {
			let url = 'index.php?module=com_vtiger_workflow&action=com_vtiger_workflowAjax&wfajax=1';
			if (type == 'delete') {
				url += '&file=deleteworkflow';
			} else if (type == 'activate') {
				url += '&file=activatedeactivateWF&active=true';
			} else if ('deactivate') {
				url += '&file=activatedeactivateWF&active=false';
			}
			url += `&workflow_id=${this.CheckedRows[i]}`;
			await Request(url, 'post');
		}
		this.UpdateGrid();
	}

	async UpdateGrid() {
		let url = 'index.php?module=com_vtiger_workflow&action=com_vtiger_workflowAjax&file=getJSON&page=all';
		this.Data = JSON.parse(await Request(url, 'get'));
		this.GridView(this.LastActive);
	}

	ExportWorkflow() {
		let exporturl = 'index.php?module=com_vtiger_workflow&action=Export';
		if (this.CheckedRows.length > 0) {
			let idstring = '';
			this.CheckedRows.forEach(function (item) {
				idstring += item.workflow_id+';';
			});
			exporturl += '&export_data=selecteddata&search_type=includesearch&filters=&idstring='+idstring;
		} else {
			exporturl += '&export_data=&search_type=all';
		}
		gotourl(exporturl);
	}

	Element(id) {
		return document.getElementById(id);
	}

	async LoadTemplates(ev, mode = '') {
		if (ev.value == 'from_module') {
			this.Element('template_select_field').style.display = 'none';
			this.Element('template_list_foundnone').style.display = 'none';
		} else {
			let holdModules = this.LastActive;
			if (mode == 'modules') {
				this.LastActive = ev.value;
			}	
			let url = `index.php?module=com_vtiger_workflow&action=com_vtiger_workflowAjax&file=templatesformodulejson&ajax=true&module_name=${this.Element('module_list').value}`;
			this.LastActive = holdModules;
			let response = JSON.parse(await Request(url, 'get'));
			if (response.length == 0) {
				this.Element('template_list').style.display = 'none';
				this.Element('choose_template').style.display = 'none';
				let source = document.getElementsByName('source');
				if (!source[0].checked) {
					this.Element('template_list_foundnone').style.display = 'block';
				} else {
					this.Element('template_list_foundnone').style.display = 'none';
				}
				this.Element('template_list').innerHTML = '';
				this.Element('template_select_field').style.display = 'none';
			} else {
				this.Element('template_select_field').style.display = 'block';
				this.Element('template_list').style.display = 'block';
				this.Element('choose_template').style.display = 'block';
				this.Element('template_list_foundnone').style.display = 'none';
				let templates = '';
				for (let i in response) {
					templates += `<option value="${response[i].id}">${response[i].title}</option>`;
				}
				this.Element('template_list').innerHTML = templates;
			}
		}
	}

	CreateSubmit() {
		if (this.Element('module_list').value=='') {
			alert(alert_arr.SELECT);
			return false;
		}
		let source = document.getElementsByName('source');
		if (source[1].checked && this.Element('template_list').value=='') {
			ldsNotification.show(alert_arr.ERROR, alert_arr.SELECT, 'error');
			return false;
		}
		return true;
	}

	SetCheckedRows() {
		this.CheckedRows = [];
		const rows = document.querySelectorAll('input[name=workflow_list]:checked');
		for (let i = 0; i < rows.length; i++) {
			this.CheckedRows.push(rows[i].dataset.value);
		}
	}


	async CreateWorkflow() {
		let url = `index.php?module=com_vtiger_workflow&action=com_vtiger_workflowAjax&file=WorkflowAPI&operation=getModules`;
		let response = JSON.parse(await Request(url, 'get'));
		let modules = '';
		for (let i in response) {
			let selected = '';
			if (response[i] == this.LastActive) {
				selected = 'selected';
			}
			modules += `<option value="${response[i]}" ${selected}>${response[i]}</option>`;
		}
		let content = `
		<form action="index.php" method="post" accept-charset="utf-8" onsubmit="return settings.CreateSubmit();">
		<div class="slds-m-around_small">
			<div class="slds-form-element__control">
				<span class="slds-radio">
				<input type="radio" name="source" id="wffrommodule" value="from_module" onchange="settings.LoadTemplates(this)" checked="" />
				<label class="slds-radio__label" for="wffrommodule">
					<span class="slds-radio_faux"></span>
					<span class="slds-form-element__label slds-page-header__meta-text">
						${alert_arr.LBL_WF_ForModule}
					</span>
				</label>
				</span>
				<span class="slds-radio slds-m-top_xx-small slds-m-bottom_xx-small">
				<input type="radio" name="source" id="wffromtpl" value="from_template" onchange="settings.LoadTemplates(this)" />
				<label class="slds-radio__label" for="wffromtpl">
					<span class="slds-radio_faux"></span>
					<span class="slds-form-element__label slds-page-header__meta-text">
						${alert_arr.LBL_WF_FromTemplate}
					</span>
				</label>
				</span>
			</div>
			<div class="slds-form-element">
				<div class="slds-form-element__control">
					<div class="slds-select_container">
						<input type="hidden" name="pick_module" value="" id="pick_module">
						<select class="slds-select slds-page-header__meta-text" name="module_name" id="module_list" onchange="settings.LoadTemplates(this, 'modules')">
							${modules}
						</select>
					</div>
				</div>
			</div>
			<span id="template_list_foundnone" style="display:none">${alert_arr.LBL_WF_NoTemplate}</span>
			<div class="slds-form-element" id="template_select_field" style="display:none">
				<label class="slds-form-element__label slds-page-header__meta-text" for="module_list" id="choose_template">
					${alert_arr.LBL_WF_ChooseTemplate}
				</label>
				<div class="slds-form-element__control">
					<div class="slds-select_container">
						<select id="template_list" name="template_id" class="slds-select slds-page-header__meta-text"></select>
					</div>
				</div>
			</div>
			<button class="slds-button slds-button_brand slds-m-top_x-small" type="submit" name="save" id='new_workflow_popup_save'>
				<svg class="slds-icon slds-icon_x-small slds-button__icon_left" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use>
				</svg>
				Create
			</button>
			<input type="hidden" name="__vt5rftk" value="${csrfMagicToken}">
			<input type="hidden" name="save_type" value="new">
			<input type="hidden" name="module" value="com_vtiger_workflow">
			<input type="hidden" name="action" value="editworkflow">
		</div>
		</form>
		<hr>
		<label class="slds-text-heading_small slds-m-around_small slds-m-top_medium">
			Workflow Statistics
		</label>
		<a class="slds-box slds-box_link slds-box_xx-small slds-media slds-m-around_small" style="background:white">
			<div class="slds-media__figure slds-media__figure_fixed-width slds-align_absolute-center slds-m-left_xx-small">
				<span class="slds-icon_container slds-icon-utility-knowledge_base">
					<svg class="slds-icon slds-icon-text-default slds-icon_x-small" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#list"></use>
					</svg>
				</span>
			</div>
			<div class="slds-media__body slds-border_left slds-p-around_small">
				<h4 class="slds-truncate" title="${alert_arr.LBL_WF_Total}">
					${alert_arr.LBL_WF_Total}
				</h4>
				<p class="slds-m-top_small">
					<strong id="total_workflows"></strong>
				</p>
			</div>
		</a>
		<a class="slds-box slds-box_link slds-box_xx-small slds-media slds-m-around_small" style="background:white">
			<div class="slds-media__figure slds-media__figure_fixed-width slds-align_absolute-center slds-m-left_xx-small">
				<span class="slds-icon_container slds-icon-utility-knowledge_base">
					<svg class="slds-icon slds-icon-text-default slds-icon_x-small" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#success"></use>
					</svg>
				</span>
			</div>
			<div class="slds-media__body slds-border_left slds-p-around_small">
				<h4 class="slds-truncate" title="${alert_arr.LBL_WF_Active}">
					${alert_arr.LBL_WF_Active}
				</h4>
				<p class="slds-m-top_small">
					<strong id="active_workflows"></strong>
				</p>
			</div>
		</a>
		<a class="slds-box slds-box_link slds-box_xx-small slds-media slds-m-around_small" style="background:white">
			<div class="slds-media__figure slds-media__figure_fixed-width slds-align_absolute-center slds-m-left_xx-small">
				<span class="slds-icon_container slds-icon-utility-knowledge_base">
					<svg class="slds-icon slds-icon-text-default slds-icon_x-small" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
					</svg>
				</span>
			</div>
			<div class="slds-media__body slds-border_left slds-p-around_small">
				<h4 class="slds-truncate" title="${alert_arr.LBL_WF_Inactive}">
					${alert_arr.LBL_WF_Inactive}
				</h4>
				<p class="slds-m-top_small">
					<strong id="inactive_workflows"></strong>
				</p>
			</div>
		</a>
		`;
		document.getElementById('new_workflow').innerHTML = content;
		if (this.Total[this.LastActive] !== undefined) {
			this.Element('total_workflows').innerHTML = this.Total[this.LastActive].length;
			this.Element('active_workflows').innerHTML = this.Active[this.LastActive].length;
			this.Element('inactive_workflows').innerHTML = this.Inactive[this.LastActive].length;
		}
		return true;
	}

	SearchBar() {
		return `
		<div class="slds-page-header">
			<div class="slds-page-header__row">
				<div class="slds-page-header__col-title">
					<div class="slds-media">
						<div class="slds-media__figure" id="currentModuleIcon">
							<span class="slds-icon_container slds-icon-standard-opportunity">
								<svg class="slds-icon slds-page-header__icon" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#bundle_config"></use>
								</svg>
							</span>
						</div>
						<div class="slds-media__body">
							<div class="slds-page-header__name">
								<div class="slds-page-header__name-title">
									<h1>
										<span id="currentModule"></span>
										<span class="slds-page-header__title slds-truncate">${this.label}</span>
									</h1>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="slds-page-header__col-actions">
					<div class="slds-page-header__controls">
						<div class="slds-page-header__control">
							<ul class="slds-button-group-list">
								<li>
									<button class="slds-button slds-button_neutral" onclick="settings.CreateWorkflow()">
										${alert_arr.LBL_WF_New}
									</button>
								</li>
								<li>
									<div class="slds-dropdown-trigger slds-dropdown-trigger_hover slds-is-open slds-button_last">
										<button class="slds-button slds-button_icon slds-button_icon-border-filled" aria-haspopup="true" aria-expanded="true" title="${alert_arr.LBL_SHOW_MORE}">
											<svg class="slds-button__icon" aria-hidden="true">
												<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#down"></use>
											</svg>
											<span class="slds-assistive-text">${alert_arr.LBL_SHOW_MORE}</span>
										</button>
										<div class="slds-dropdown slds-dropdown_right slds-dropdown_actions">
										<ul class="slds-dropdown__list" role="menu" style="width:8rem">
											<li class="slds-dropdown__item" role="presentation">
												<a onclick="settings.doActions('activate')" role="menuitem" tabindex="0">
													<svg class="slds-button__icon" aria-hidden="true">
														<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#check"></use>
													</svg>
													<span class="slds-truncate" title="${alert_arr.LBL_WF_Activate}">${alert_arr.LBL_WF_Activate}</span>
												</a>
											</li>
											<li class="slds-dropdown__item" role="presentation">
												<a onclick="settings.doActions('deactivate')" role="menuitem" tabindex="0">
													<svg class="slds-button__icon" aria-hidden="true">
														<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#error"></use>
													</svg>
													<span class="slds-truncate" title="${alert_arr.LBL_WF_Deactivate}">${alert_arr.LBL_WF_Deactivate}</span>
												</a>
											</li>
											<li class="slds-dropdown__item" role="presentation">
												<a onclick="gotourl('index.php?module=com_vtiger_workflow&action=Import');" role="menuitem" tabindex="0">
													<svg class="slds-button__icon" aria-hidden="true">
														<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#download"></use>
													</svg>
													<span class="slds-truncate" title="${alert_arr.LBL_IMPORT}">${alert_arr.LBL_IMPORT}</span>
												</a>
											</li>
											<li class="slds-dropdown__item" role="presentation">
												<a onclick="settings.doActions('export')" role="menuitem" tabindex="-1">
													<svg class="slds-button__icon" aria-hidden="true">
														<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#upload"></use>
													</svg>
													<span class="slds-truncate" title="${alert_arr.LBL_EXPORT}">${alert_arr.LBL_EXPORT}</span>
												</a>
											</li>
											</li>
											<li class="slds-dropdown__item" role="presentation">
												<a onclick="settings.doActions('delete')" role="menuitem" tabindex="-1">
													<svg class="slds-button__icon cbds-color-compl-red--sober" aria-hidden="true">
														<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
													</svg>
													<span class="slds-truncate cbds-color-compl-red--sober" title="${alert_arr.LNK_DELETE_ACTION}">${alert_arr.LNK_DELETE_ACTION}</span>
												</a>
											</li>
										</ul>
									</div>
								</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="slds-page-header__row">
				<div class="slds-page-header__col-title">
					<div class="slds-media">
						<div class="slds-media__body">
							<div class="slds-grid slds-gutters">
								<div class="slds-col slds-size_3-of-12">
									<label class="slds-form-element__label">${alert_arr.LBL_WF_Workflow}</label>
									<input type="text" class="slds-input" id="Description" oninput="settings.GridFilter(this, 'Description')">
								</div>
								<div class="slds-col" style="width: 14%">
									<label class="slds-form-element__label">${alert_arr.LBL_WF_Type}</label>
									<select type="text" class="slds-select" id="tasktypelabel" onchange="settings.GridFilter(this, 'tasktypelabel')">
										<option value=""></option>
										${this.RenderOptions('tasktypelabel', '_children')}
									</select>
								</div>
								<div class="slds-col" style="width: 14%">
									<label class="slds-form-element__label">${alert_arr.LBL_WF_Description}</label>
									<input type="text" class="slds-input" id="summary" oninput="settings.GridFilter(this, 'summary')">
								</div>
								<div class="slds-col" style="width: 15%">
									<label class="slds-form-element__label">${alert_arr.LBL_WF_Purpose}</label>
									<input type="text" class="slds-input" id="Purpose" oninput="settings.GridFilter(this, 'Purpose')">
								</div>
								<div class="slds-col" style="width: 14%">
									<label class="slds-form-element__label">${alert_arr.LBL_WF_Trigger}</label>
									<select type="text" class="slds-select" id="Trigger" onchange="settings.GridFilter(this, 'Trigger')">
										<option value=""></option>
										${this.RenderOptions('Trigger')}
									</select>
								</div>
								<div class="slds-col" style="width: 10%">
									<label class="slds-form-element__label">${alert_arr.LBL_WF_Status}</label>
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
		case 'checkbox':
			if (type == 'workflow') {
				actions += `<input type="checkbox" data-value="${workflow_id}" name="workflow_list" onclick="settings.SetCheckedRows()">`;
			}
			break;
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
					<span class="slds-assistive-text">${alert_arr.LNK_DELETE_ACTION}</span>
				</a>`;
			}
			actions += `
			<div class="slds-button-group" role="group">
				<a href="${Record}" target="_blank" class="slds-button slds-button_icon slds-button_icon-border-filled" aria-pressed="false">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#forward_up"></use>
					</svg>
					<span class="slds-assistive-text">${alert_arr.LBL_WF_Redirect}</span>
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
const urlParams = new URLSearchParams(window.location.search);
const openWorkflow = urlParams.get('openWorkflow');
const formodule = urlParams.get('formodule');
if (openWorkflow !== null && formodule !== null) {
	settings.Workflows(alert_arr.LBL_WF_List, formodule);
}