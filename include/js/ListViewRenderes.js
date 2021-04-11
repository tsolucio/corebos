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
		let el, edit_query, edit_query_string, module;
		if (document.getElementById('curmodule')) {
			module = document.getElementById('curmodule').value;
		} else if (document.getElementById('select_module')) {
			module = document.getElementById('select_module').value;
		}
		let rowKey = props.rowKey;
		let columnName = props.columnInfo.name;
		let recordid = props.grid.getValue(rowKey, 'recordid');
		let referenceField = props.grid.getValue(rowKey, 'reference_field');
		let referenceValue = props.grid.getValue(rowKey, referenceField);
		let relatedRows = props.grid.getValue(rowKey, 'relatedRows');
		const { tooltip } = props.columnInfo.renderer.options;
		if (tooltip) {
			props.formattedValue = `
			<span>${props.value}</span>
			<span class="slds-icon_container slds-icon__svg--default slds-float_right slds-m-right_small cbds-tooltip__trigger slds-p-left_xx-small"
				id="cbds-tooltip__trigger-${recordid}-${columnName}"
				onmouseover="ListView.addTooltip('${recordid}', '${columnName}', '${relatedRows[columnName] != undefined ? moduleName : module}')"
				onclick="(function(e){e.stopPropagation(); e.preventDefault()})(event)">
				<svg class="slds-icon slds-icon-text-default slds-icon_x-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info"></use>
				</svg>
			</span>
			`;
		}
		if (columnName == referenceField) {
			el = document.createElement('a');
			if (tooltip) {
				el.id = `tooltip-el-${recordid}-${columnName}`;
			}
			edit_query = {
				'module': module,
				'action': 'DetailView',
				'record': recordid,
			};
			edit_query_string = ListView.encodeQueryData(edit_query);
			el.href = `index.php?${edit_query_string}`;
			el.innerHTML = String(props.value);
			el.style.marginLeft = '5px';
			this.el = el;
			this.render(props);
		} else if (relatedRows[columnName] != undefined) {
			let moduleName = relatedRows[columnName][0];
			let fieldId = relatedRows[columnName][1];
			el = document.createElement('a');
			if (tooltip) {
				el.id = `tooltip-el-${recordid}-${columnName}`;
			}
			edit_query = {
				'module': moduleName,
				'action': 'DetailView',
				'record': fieldId,
			};
			edit_query_string = ListView.encodeQueryData(edit_query);
			el.href = `index.php?${edit_query_string}`;
			el.innerHTML = String(props.value);
			el.style.marginLeft = '5px';
			this.el = el;
			this.render(props);
		} else {
			if (columnName == 'filename' && module == 'Documents') {
				el = document.createElement('a');
				if (tooltip) {
					el.id = `tooltip-el-${recordid}-${columnName}`;
				}
				el.setAttribute('onclick', `javascript:dldCntIncrease(${recordid})`);
				edit_query = {
					'module': 'Utilities',
					'action': 'UtilitiesAjax',
					'file': 'ExecuteFunctions',
					'functiontocall': 'downloadfile',
					'entityid': recordid,
					'fileid': props.grid.getValue(rowKey, 'fileid'),
				};
				edit_query_string = ListView.encodeQueryData(edit_query);
				el.href = `index.php?${edit_query_string}`;
			} else {
				let fieldType = props.grid.getValue(rowKey, 'uitype_'+columnName);
				let fieldValue = props.grid.getValue(rowKey, columnName);
				if (fieldType == '17') {
					el = document.createElement('a');
					el.href = fieldValue;
					el.target = '_blank';
				} else {
					el = document.createElement('span');
					if (tooltip) {
						el.id = `tooltip-el-${recordid}-${columnName}`;
					}
				}
			}
			el.innerHTML = String(props.value);
			el.style.marginLeft = '5px';
			this.el = el;
			this.render(props);
		}
	}

	getElement() {
		return this.el;
	}

	render(props) {
		if (props.formattedValue != '') {
			this.el.innerHTML = String(props.formattedValue);
		} else {
			this.el.textContent = String(props.value);
		}
	}
}

class ActionRender {

	constructor(props) {
		let el, module;
		if (document.getElementById('curmodule')) {
			module = document.getElementById('curmodule').value;
		} else if (document.getElementById('select_module')) {
			module = document.getElementById('select_module').value;
		}
		let rowKey = props.rowKey;
		let recordid = props.grid.getValue(rowKey, 'recordid');
		el = document.createElement('span');
		let actions = `
			<div class="slds-dropdown-trigger slds-dropdown-trigger_click">
				<button
					class="slds-button slds-button_icon slds-button_icon-border-filled listview-actions-opener"
					aria-haspopup="true"
					title="Show More"
					onclick="ListView.RenderActions(${recordid});"
				>
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#threedots"></use>
					</svg>
					<span class="slds-assistive-text">Show More</span>
				</button>
				<div class="slds-dropdown slds-dropdown_right slds-dropdown_actions" id="dropdown-${recordid}">
			</div>
		`
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