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
}, false);

const MCGrid = {

	Show: () => {
		mcdataGridInstance = new mctuiGrid({
			el: document.getElementById('ListViewContents'),
			rowHeaders: ['checkbox'],
			data: [],
			scrollX: false,
			scrollY: false,
			columnOptions: {
				resizable: true
			},
			columns: JSON.parse(GridColumns),
			onGridUpdated: (ev) => {
				console.log(ev)
			}
		});
		tui.Grid.applyTheme('striped');
	},

	Append: () => {
		mcdataGridInstance.appendRow(JSON.parse(EmptyData));
	},

	Save: () => {
		const data = mcdataGridInstance.getData();
		fetch(
			'index.php?module=Utilities&action=UtilitiesAjax&file=MassCreateGridAPI&moduleName='+gVTModule,
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
				alert('Created successfully');
				mcdataGridInstance.clear();
			}
		});
	},

	Delete: () => {
		const rows = mcdataGridInstance.getCheckedRowKeys();
		for (let i in rows) {
			mcdataGridInstance.removeRow(rows[i]);
		}
	},
};