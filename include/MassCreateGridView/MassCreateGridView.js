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
let mctuiGrid = tui.Grid;
let mcdataGridInstance = Array();

document.addEventListener('DOMContentLoaded', function () {
	MCGrid.Show();
	for (let i = 0; i < 10; i++) {
		MCGrid.Append();
	}
}, false);

const MCGrid = {

	ActiveColumns: [],
	MatchFields: [],
	Module: gVTModule,

	Show: () => {
		MCGrid.ActiveColumns = JSON.parse(GridColumns);
		mcdataGridInstance = new mctuiGrid({
			el: document.getElementById('listview-tui-grid'),
			rowHeaders: ['rowNum', 'checkbox'],
			data: [],
			scrollX: false,
			scrollY: false,
			columnOptions: {
				resizable: true
			},
			header: {
				align: 'left'
			},
			columns: MCGrid.ActiveColumns
		});
		tui.Grid.applyTheme('striped');
		mcdataGridInstance.on('keydown', ev => {
			const rowKey = ev.rowKey + 1;
			const totalRows = mcdataGridInstance.getRowCount();
			if (rowKey == totalRows) {
				MCGrid.Append();
				window.scrollTo(0, document.body.scrollHeight);
			}
		});
	},

	Append: () => {
		mcdataGridInstance.appendRow(JSON.parse(EmptyData));
	},

	FormValidation: (data) => {
		let response = Array();
		data.map(function(row) {
			for (let field in row) {
				if (isNaN(field) && field != 'rowKey' && field != '_attributes') {
					let fieldType = MCGrid.FindFieldType(field);
					if (fieldType[1] == 'M') {
						if (row[field] == null || row[field] == '') {
							response.push(false);
						}
					}
					if (row[field] == null) {
						row[field] = '';
					}
					if (!cbVal(fieldType[0], row[field])) {
						response.push(false);
					}
				}
			}
		});
		return response;
	},

	FindFieldType: (field) => {
		let typeofdata = '';
		let moduleFields = JSON.parse(ListFields);
		moduleFields.map(function(row) {
			if (row.name == field) {
				typeofdata = row.type;
			}
		});
		return typeofdata;
	},

	Save: () => {
		const data = mcdataGridInstance.getData();
		if (MCGrid.FormValidation(data).includes(false)) {
			ldsPrompt.show(alert_arr.ERROR, alert_arr.ERROR_CREATING_TRY_AGAIN);
			return;
		}
		document.getElementById('slds-spinner').style.display = 'block';
		fetch(
			'index.php?module=Utilities&action=UtilitiesAjax&file=MassCreateGridAPI&moduleName='+gVTModule+'&method=MassCreate',
			{
				method: 'post',
				headers: {
					'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
				},
				credentials: 'same-origin',
				body: '&'+csrfMagicName+'='+csrfMagicToken+'&data='+JSON.stringify(data)+'&mapName='+bmapname
			}
		).then(response => response.json()).then(response => {
			if (response.failed_creates.length == 0) {
				ldsPrompt.show(alert_arr.LBL_SUCCESS, alert_arr.LBL_CREATED_SUCCESS, 'success');
				mcdataGridInstance.clear();
			} else {
				let msg = '<ul class="slds-has-dividers_top-space">';
				for (let i in response.failed_creates) {
					msg += `<li class="slds-item"><strong>No. ${response.failed_creates[i].record.element.rowKey+1}:</strong> ${response.failed_creates[i].message}</li>`;
				}
				msg += '</ul>';
				ldsPrompt.show(alert_arr.ERROR, msg);
			}
			document.getElementById('slds-spinner').style.display = 'none';
		});
	},

	Delete: () => {
		const rows = mcdataGridInstance.getCheckedRowKeys();
		for (let i in rows) {
			mcdataGridInstance.removeRow(rows[i]);
		}
	},

	EditFields: () => {
		const activeCols = JSON.parse(ListFields);
		const activeMatchFields = JSON.parse(MatchFields);
		let content = `<div class="slds-grid slds-wrap">`;
		activeCols.map(function(currentValue, index) {
			let typeofdata = '';
			let checked = '';
			let disabled = '';
			let modules = '';
			if (currentValue.typeofdata == 'M') {
				typeofdata = `<span class="slds-text-color_error">*</span>`;
				checked = 'checked';
				disabled = 'disabled';
			}
			if (currentValue.relatedModules.length > 0) {
				modules += `<select id="selected-${currentValue.name}">`;
				for (let i in currentValue.relatedModules) {
					let selected = '';
					if (currentValue.activeModule == currentValue.relatedModules[i]) {
						selected = 'selected';
					}
					modules += `<option ${selected} value="${currentValue.relatedModules[i]}">${currentValue.relatedModules[i]}</option>`;
				}
				modules += `</select>`;
			}
			content += `
			<div class="slds-col slds-size_4-of-12">
				<div class="slds-form-element">
					<div class="slds-form-element__control">
						<div class="slds-checkbox">
							<input type="checkbox" name="grid-fields" id="checkbox-${currentValue.name}" value="checkbox-${currentValue.name}" ${currentValue.active == 1 ? 'checked' : ''} ${checked} ${disabled}/>
							<label class="slds-checkbox__label" for="checkbox-${currentValue.name}">
								<span class="slds-checkbox_faux"></span>
								<span class="slds-form-element__label">${currentValue.header} ${typeofdata} ${modules}</span>
							</label>
						</div>
					</div>
				</div>
			</div>`;
		});
		content += `<br><br>
		<div class="slds-col slds-size_12-of-12">
		<header class="slds-media slds-media_center slds-has-flexi-truncate">
			<div class="slds-media__figure">
				<span class="slds-icon_container slds-icon-standard-account" title="action_list_component">
					<svg class="slds-icon slds-icon_small" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#action_list_component">
						</use>
					</svg>
					<span class="slds-assistive-text">action_list_component</span>
				</span>
				</div>
				<div class="slds-media__body">
				<h2 class="slds-card__header-title">
					<a href="#" class="slds-card__header-link slds-truncate" title="Accounts">
						<span>${alert_arr.LBL_MATCH_COLUMNS}</span>
					</a>
				</h2>
			</div>
		</header>
		</div><br><br>
		`;
		activeMatchFields.map(function(currentValue, index) {
			content += `
			<div class="slds-col slds-size_4-of-12">
				<div class="slds-form-element">
					<div class="slds-form-element__control">
						<div class="slds-checkbox">
							<input type="checkbox" name="grid-fields" id="match-${currentValue.name}" value="match-${currentValue.name}" ${currentValue.active == 1 ? 'checked' : ''}/>
							<label class="slds-checkbox__label" for="match-${currentValue.name}">
								<span class="slds-checkbox_faux"></span>
								<span class="slds-form-element__label">${currentValue.header}</span>
							</label>
						</div>
					</div>
				</div>
			</div>`;
		});
		content += `</div>`;
		ldsModal.show(alert_arr.LBL_SELECT_COLUMNS, content, 'medium', 'MCGrid.UpdateView()');
	},

	UpdateView: () => {
		let columns = JSON.parse(ListFields);
		let matchColumns = JSON.parse(MatchFields);
		let newColumns = Array();
		let newMatchFields = Array();
		let ColumnsDiff = Array();
		let MatchDiff = Array();
		columns.map(function(currentValue, idx) {
			const checkbox = document.getElementById(`checkbox-${currentValue.name}`);
			if (checkbox.checked) {
				columns[idx].active = 1;
				if (document.getElementById(`selected-${currentValue.name}`) !== null) {
					currentValue['activeModule'] = document.getElementById(`selected-${currentValue.name}`).value;
				}
				newColumns.push(currentValue);
				ColumnsDiff.push(currentValue.name);
			} else {
				columns[idx].active = 0;
			}
		});
		matchColumns.map(function(currentValue, idx) {
			const match = document.getElementById(`match-${currentValue.name}`);
			if (match.checked) {
				matchColumns[idx].active = 1;
				newMatchFields.push(currentValue);
				MatchDiff.push(currentValue.name);
			} else {
				matchColumns[idx].active = 0;
			}
		});
		ListFields = JSON.stringify(columns);
		MatchFields = JSON.stringify(matchColumns);
		let difference = MatchDiff.filter(x => ColumnsDiff.indexOf(x) === -1);
		if (difference.length > 0) {
			ldsPrompt.show(alert_arr.ERROR, alert_arr.LBL_MATCH_ERROR);
			return false;
		}
		MCGrid.ActiveColumns = newColumns;
		MCGrid.MatchFields = newMatchFields;
		MCGrid.SaveMap();
	},

	SaveMap: () => {
		fetch(
			'index.php?module=Utilities&action=UtilitiesAjax&file=MassCreateGridAPI&method=SaveMap',
			{
				method: 'post',
				headers: {
					'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
				},
				credentials: 'same-origin',
				body: '&'+csrfMagicName+'='+csrfMagicToken+'&ActiveColumns='+JSON.stringify(MCGrid.ActiveColumns)+'&mapName='+bmapname+'&moduleName='+MCGrid.Module+'&match='+JSON.stringify(MCGrid.MatchFields)
			}
		).then(response => response.json()).then(response => {
			if (typeof response == 'string') {
				mcdataGridInstance.setColumns(JSON.parse(GridColumns));
				ldsPrompt.show(alert_arr.ERROR, response);
				return;
			}
			if (response.length == 0) {
				mcdataGridInstance.setColumns(JSON.parse(GridColumns));
				ldsPrompt.show(alert_arr.ERROR, alert_arr.ERROR_WHILE_EDITING);
				return;
			}
			window.location.href = '';
		});
	}
};