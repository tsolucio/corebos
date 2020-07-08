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

class CheckboxRender {

	constructor(props) {
		const { grid, rowKey } = props;
		const label = document.createElement('label');
		label.className = 'checkbox';
		label.setAttribute('for', String(rowKey));
		const Input = document.createElement('input');
		Input.name = 'selected_id[]';
		Input.setAttribute('onclick', 'ListView.getCheckedRows("", this);');
		Input.className = 'hidden-input listview-checkbox';
		Input.id = String(rowKey);
		label.appendChild(Input);
		Input.type = 'checkbox';
		Input.addEventListener('change', () => {
			if (Input.checked) {
				grid.check(rowKey);
			} else {
				grid.uncheck(rowKey);
			}
		});
		this.el = label;
		this.render(props);
	}

	getElement() {
		return this.el;
	}

	render(props) {
		const Input = this.el.querySelector('.hidden-input');
		const checked = Boolean(props.value);
		Input.checked = checked;
	}
}

class LinkRender {

	constructor(props) {
		let el;
		let module = document.getElementById('curmodule').value;
		let rowKey = props.rowKey;
		let columnName = props.columnInfo.name;
		let recordid = props.grid.getValue(rowKey, 'recordid');
		let referenceField = props.grid.getValue(rowKey, 'reference');
		let referenceValue = props.grid.getValue(rowKey, referenceField);
		let relatedRows = props.grid.getValue(rowKey, 'relatedRows');
		const { tooltip } = props.columnInfo.renderer.options;
		if (columnName == referenceField) {
			el = document.createElement('a');
			if (tooltip) {
				el.id = `tooltip-el-${recordid}-${columnName}`;
				el.setAttribute('onmouseover', `ListView.addTooltip("${recordid}", "${columnName}", "${module}")`);
			}
			el.href = `index.php?module=${module}&action=DetailView&record=`+recordid;
			el.innerHTML = String(props.value);
			this.el = el;
			this.render(props);
		} else if (relatedRows[columnName] != undefined) {
			let moduleName = relatedRows[columnName][0];
			let fieldId = relatedRows[columnName][1];
			el = document.createElement('a');
			if (tooltip) {
				el.id = `tooltip-el-${recordid}-${columnName}`;
				el.setAttribute('onmouseover', `ListView.addTooltip("${recordid}", "${columnName}", "${moduleName}")`);
			}
			el.href = `index.php?module=${moduleName}&action=DetailView&record=`+fieldId;
			el.innerHTML = String(props.value);
			this.el = el;
			this.render(props);
		} else {
			el = document.createElement('span');
			if (tooltip) {
				el.id = `tooltip-el-${recordid}-${columnName}`;
				el.setAttribute('onmouseover', `ListView.addTooltip("${recordid}", "${columnName}", "${module}")`);
			}
			el.innerHTML = String(props.value);
			this.el = el;
			this.render(props);
		}
	}

	getElement() {
		return this.el;
	}

	render(props) {
		if (props.formattedValue != '') {
			this.el.textContent = String(props.formattedValue);
		} else {
			this.el.textContent = String(props.value);
		}
	}
}

class ActionRender {

	constructor(props) {
		let el;
		let module = document.getElementById('curmodule').value;
		let rowKey = props.rowKey;
		let recordid = props.grid.getValue(rowKey, 'recordid');
		el = document.createElement('span');
		const actions = `
			<a href="index.php?module=${module}&action=EditView&record=${recordid}&return_module=${module}&return_action=index">${alert_arr['LNK_EDIT']}</a> | 
			<a href="javascript:confirmdelete('index.php?module=${module}&action=Delete&record=${recordid}&return_module=${module}&return_action=index&parenttab=ptab');">${alert_arr['LNK_DELETE']}</a>`;
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