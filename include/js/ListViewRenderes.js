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
			}
			el.href = `index.php?module=${moduleName}&action=DetailView&record=`+fieldId;
			el.innerHTML = String(props.value);
			this.el = el;
			this.render(props);
		} else {
			el = document.createElement('span');
			if (tooltip) {
				el.id = `tooltip-el-${recordid}-${columnName}`;
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
			this.el.innerHTML = String(props.formattedValue);
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
		let actionPermission = props.grid.getValue(rowKey, 'action');
		el = document.createElement('span');
		let actions = '<div class="slds-button-group" role="group">';
		const str = '|';
		const editUrl = `index.php?module=${module}&action=EditView&record=${recordid}&return_module=${module}&return_action=index`;
		const deleteUrl = `javascript:confirmdelete('index.php?module=${module}&action=Delete&record=${recordid}&return_module=${module}&return_action=index&parenttab=ptab');`;
		let status = '';
		if (actionPermission.cbCalendar.status != undefined) {
			status = `
			<li class="slds-dropdown__item" role="presentation">
				<a onclick="ajaxChangeCalendarStatus('${actionPermission.cbCalendar.status}',${recordid});" role="menuitem" tabindex="-1">
					<span class="slds-truncate" title="Close">
					    <svg class="slds-button__icon" aria-hidden="true">
					        <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
					    </svg>
						Close
					</span>
				</a>
			</li>`;

		}
		let modified = '';
		if (actionPermission.isModified) {
			modified = `
	          	<li class="slds-dropdown__item" role="presentation">
					<a role="menuitem" tabindex="-1">
						<span class="slds-truncate" title="Notification">
					        <svg class="slds-button__icon" aria-hidden="true">
					          <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#notification"></use>
					        </svg>
							Modified
						</span>
					</a>
	          	</li>`;
		}
		let customActions = '';
		if (modified != '' && status != '') {
			customActions += `
				<a onclick="ajaxChangeCalendarStatus('${actionPermission.cbCalendar.status}',${recordid});" class="slds-button slds-button_icon slds-button_icon-border-filled">
				    <svg class="slds-button__icon" aria-hidden="true">
				      <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
				    </svg>
				    Close
				</a>
				<div class="slds-dropdown-trigger slds-dropdown-trigger_hover slds-button_last">
			    <button class="slds-button slds-button_icon slds-button_icon-border-filled" aria-haspopup="true" title="Show More">
			        <svg class="slds-button__icon" aria-hidden="true">
			          <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#down"></use>
			        </svg>
			        <span class="slds-assistive-text">Show More</span>
			    </button>
				<div class="slds-dropdown slds-dropdown_right slds-dropdown_actions">
					<ul class="slds-dropdown__list" role="menu">
						${modified}
			        </ul>
			    </div>
			</div>`;
		} else if (modified == '' && status != '') {
			customActions += `
				<a onclick="ajaxChangeCalendarStatus('${actionPermission.cbCalendar.status}',${recordid});" class="slds-button slds-button_icon slds-button_icon-border-filled">
				    <svg class="slds-button__icon" aria-hidden="true">
				      <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
				    </svg>
				    Close
				</a>`;
		} else if (modified != '' && status == '') {
			customActions += `
				<a class="slds-button slds-button_icon slds-button_icon-border-filled">
					<span class="slds-truncate" title="Notification">
				        <svg class="slds-button__icon" aria-hidden="true">
				          <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#notification"></use>
				        </svg>
					</span>
				</a>`;
		}
		if (actionPermission.edit && actionPermission.delete) {
			actions += `
				<a class="slds-button slds-button_icon slds-button_icon-border-filled" href="${editUrl}">
				    <svg class="slds-button__icon" aria-hidden="true">
				      <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
				    </svg>
				</a>
				<a class="slds-button slds-button_icon slds-button_icon-border-filled" href="${deleteUrl}">
				    <svg class="slds-button__icon" aria-hidden="true">
				      <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
				    </svg>
				</a>`;
			if (modified != '' || status != '') {
				actions += `
					<div class="slds-dropdown-trigger slds-dropdown-trigger_hover slds-button_last">
				    <button class="slds-button slds-button_icon slds-button_icon-border-filled" aria-haspopup="true" title="Show More">
				        <svg class="slds-button__icon" aria-hidden="true">
				          <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#down"></use>
				        </svg>
				        <span class="slds-assistive-text">Show More</span>
				    </button>
					<div class="slds-dropdown slds-dropdown_right slds-dropdown_actions">
						<ul class="slds-dropdown__list" role="menu">
							${modified}
				          	${status}
				        </ul>
				    </div>
				</div>`;
			}
		} else if (!actionPermission.edit && actionPermission.delete) {
			actions += `
				<a class="slds-button slds-button_icon slds-button_icon-border-filled" href="${deleteUrl}">
				    <svg class="slds-button__icon" aria-hidden="true">
				      <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
				    </svg>
				</a>
				${customActions}
			`;
		} else if (actionPermission.edit && !actionPermission.delete) {
			actions += `
				<a class="slds-button slds-button_icon slds-button_icon-border-filled" href="${editUrl}">
				    <svg class="slds-button__icon" aria-hidden="true">
				      <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
				    </svg>
				</a>
				${customActions}
			`;
		} else {
			actions += customActions;
		}
		actions += '</div>';
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