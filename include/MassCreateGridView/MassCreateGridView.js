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

	Save: () => {
		const data = mcdataGridInstance.getData();
		document.getElementById('slds-spinner').style.display = 'block';
		fetch(
			'index.php?module=Utilities&action=UtilitiesAjax&file=MassCreateGridAPI&moduleName='+gVTModule+'&method=MassCreate',
			{
				method: 'post',
				headers: {
					'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
				},
				credentials: 'same-origin',
				body: '&'+csrfMagicName+'='+csrfMagicToken+'&data='+JSON.stringify(data)
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
		let content = `<div class="slds-grid slds-wrap">`;
		activeCols.map(function(currentValue, index) {
			let typeofdata = '';
			if (currentValue.typeofdata == 'M') {
				typeofdata = `<span class="slds-text-color_error">*</span>`;
			}
			content += `
			<div class="slds-col slds-size_3-of-12">
				<div class="slds-form-element">
					<div class="slds-form-element__control">
						<div class="slds-checkbox">
							<input type="checkbox" name="grid-fields" id="checkbox-${currentValue.name}" value="checkbox-${currentValue.name}" ${currentValue.active == 1 ? 'checked' : ''}/>
							<label class="slds-checkbox__label" for="checkbox-${currentValue.name}">
								<span class="slds-checkbox_faux"></span>
								<span class="slds-form-element__label">${currentValue.header} ${typeofdata}</span>
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
		let newColumns = Array();
		columns.map(function(currentValue, idx) {
			const checkbox = document.getElementById(`checkbox-${currentValue.name}`);
			if (checkbox.checked) {
				columns[idx].active = 1;
				newColumns.push(currentValue);
			} else {
				columns[idx].active = 0;
			}
		});
		mcdataGridInstance.setColumns(newColumns);
		ListFields = JSON.stringify(columns);
		ldsModal.close();
		MCGrid.ActiveColumns = newColumns;
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
				body: '&'+csrfMagicName+'='+csrfMagicToken+'&ActiveColumns='+JSON.stringify(MCGrid.ActiveColumns)+'&mapName='+bmapname+'&moduleName='+MCGrid.Module
			}
		).then(response => response.json()).then(response => {
			if (response.length == 0) {
				mcdataGridInstance.setColumns(JSON.parse(GridColumns));
				ldsPrompt.show(alert_arr.ERROR, alert_arr.ERROR_WHILE_EDITING);
			}
		});
	}
};