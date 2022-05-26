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

loadJS('index.php?module=Settings&action=SettingsAjax&file=getjslanguage');
const tuiGrid = tui.Grid;
let url = 'index.php?module=Settings&action=SettingsAjax&file=BuilderFunctions';
let dataGridInstance;
let fieldGridInstance;
let viewGridInstance;
let listGridInstance;
let moduleData = new Array();
let MODULEID = localStorage.getItem('ModuleBuilderID');

const mb = {
	/**
	 * Save values for each step
	 * @param {number} step
	 * @param {boolean} forward
	 * @param {string} buttonid
	 */
	SaveModule: (step, forward = true, buttonid = '', recordid = 0) => {
		var data = {};
		if (step == 1) {
			data = {
				modulename: mb.loadElement('modulename'),
				modulelabel: mb.loadElement('modulelabel'),
				parentmenu: mb.loadElement('parentmenu'),
				moduleicon: mb.loadElement('moduleicon'),
				sharingaccess: mb.getRadioValue('sharingaccess'),
				merge: mb.loadElement('merge', true).checked,
				import: mb.loadElement('import', true).checked,
				export: mb.loadElement('export', true).checked,
				step: step
			};
		}
		if (step == 2) {
			var blocks_label = [];
			const BLOCK_COUNT = mb.loadElement('BLOCK_COUNT');
			for (var i = 1; i <= BLOCK_COUNT; i++) {
				const blockLabel = mb.loadElement(`blocks_label_${i}`);
				if (mb.checkBlocks(blockLabel)) {
					blocks_label[i] = blockLabel;
				}
			}
			data = {
				blocks: blocks_label,
				step: step
			};
		}

		if (step == 3) {
			var fields = [];
			const FIELD_COUNT = mb.loadElement('FIELD_COUNT');
			var btnid = buttonid.split('-')[4];
			if (forward == false) {
				let proceed = true;
				if (mb.loadElement(`fieldname_${btnid}`) == '' || mb.loadElement(`fieldlabel_${btnid}`) == '' || mb.loadElement(`UItype_${btnid}`) == '') {
					mb.loadMessage(mod_alert_arr.FieldsEmpty, true, 'error');
					proceed = false;
				}
				if (mb.loadElement(`UItype_${btnid}`) == '10') {
					if (mb.loadElement(`relatedmodules_${btnid}`) == '') {
						mb.loadMessage(mod_alert_arr.Relmod, true, 'error');
						proceed = false;
					}
				}
				if (mb.loadElement(`UItype_${btnid}`) == '15' || mb.loadElement(`UItype_${btnid}`) == '16') {
					if (mb.loadElement(`picklistvalues_${btnid}`) == '') {
						mb.loadMessage(mod_alert_arr.PickListFld, true, 'error');
						proceed = false;
					}
				}
				if (!proceed) {
					return;
				}
				var fieldValues = {
					blockid: mb.getRadioValue(`select-for-field-${btnid}`),
					fieldname: mb.loadElement(`fieldname_${btnid}`),
					fieldlength: mb.loadElement(`fieldlength_${btnid}`),
					columnname: mb.loadElement(`fieldname_${btnid}`),
					fieldlabel: mb.loadElement(`fieldlabel_${btnid}`),
					relatedmodules: mb.loadElement(`relatedmodules_${btnid}`),
					masseditable: mb.loadElement(`Masseditable_${btnid}`),
					displaytype: mb.loadElement(`Displaytype_${btnid}`),
					quickcreate: mb.loadElement(`Quickcreate_${btnid}`),
					typeofdata: mb.loadElement(`Typeofdata_${btnid}`),
					presence: mb.loadElement(`Presence_${btnid}`),
					uitype: mb.loadElement(`UItype_${btnid}`),
					picklistvalues: mb.loadElement(`picklistvalues_${btnid}`),
					generatedtype: mb.loadElement(`generatedtype_${btnid}`),
					sequence: FIELD_COUNT,
				};
				fields.push(fieldValues);
				data = {
					fields: fields,
					step: step,
					recordid: recordid
				};
			} else {
				data = {
					fields: [],
					step: step,
					recordid: recordid
				};
			}
		}

		if (step == 4) {
			let customViews = [];
			let field;
			var btnid = buttonid.split('-')[4];
			const FILTER_COUNT = mb.loadElement('FILTER_COUNT');
			if (forward == false) {
				let proceed = true;
				if (mb.loadElement(`viewname-${FILTER_COUNT}`) == '') {
					mb.loadMessage(mod_alert_arr.ViewnameEmpty_msg, true, 'error');
					proceed = false;
				}
				if (mb.loadElement(`viewfields-${FILTER_COUNT}`) == '') {
					mb.loadMessage(mod_alert_arr.ChoseField, true, 'error');
					proceed = false;
				}
				if (!proceed) {
					return;
				}
				var customObj = {
					viewname: mb.loadElement(`viewname-${FILTER_COUNT}`),
					setdefault: mb.loadElement(`setdefault-${FILTER_COUNT}`),
					fields: mb.loadElement(`viewfields-${FILTER_COUNT}`),
				};
				data = {
					customview: customObj,
					step: step,
					recordid: recordid
				};
			} else {
				data = {
					customview: [],
					step: step,
					recordid: recordid
				};
			}
		}

		if (step == 5) {
			let relatedLists = [];
			const LIST_COUNT = mb.loadElement('LIST_COUNT');
			if (forward == false) {
				let proceed = true;
				if (mb.loadElement(`autocomplete-module-${LIST_COUNT}`) == '') {
					mb.loadMessage(mod_alert_arr.Related_module_label, true, 'error');
					proceed = false;
				}
				if (mb.loadElement(`related-label-${LIST_COUNT}`) == '') {
					mb.loadMessage(mod_alert_arr.Related_module_label, true, 'error');
					proceed = false;
				}
				if (!proceed) {
					return false;
				}
				let lists = {
					relatedmodule: mb.loadElement(`autocomplete-module-${LIST_COUNT}`),
					actions: mb.loadElement(`autocomplete-related-${LIST_COUNT}`) == 'get_dependents_list' ? 'ADD' : 'ADD,SELECT',
					name: mb.loadElement(`autocomplete-related-${LIST_COUNT}`),
					label: mb.loadElement(`related-label-${LIST_COUNT}`),
				};
				data = {
					relatedlists: lists,
					step: step,
					recordid: recordid
				};
			} else {
				data = {
					relatedlists: [],
					step: step,
					recordid: recordid
				};
			}
		}

		jQuery.ajax({
			method: 'POST',
			url: url+'&methodName=Save',
			data: data
		}).done(function (response) {
			const res = JSON.parse(response);
			if (res.moduleid !== undefined) {
				localStorage.setItem('ModuleBuilderID', res.moduleid);
			}
			const msg = mod_alert_arr.RecordSaved;
			if (res != null && res.error) {
				mb.loadMessage(res.error, true, 'error');
				return;
			}
			//show message
			if (forward != false && step == 2) {
				if (blocks_label[1] != '') {
					mb.loadMessage(msg, true);
				}
			}
			if (forward == false) {
				if (step == 3) {
					mb.loadMessage(msg, true);
					mb.removeElement(`for-field-${btnid}`);
					mb.removeElement(`for-field-inputs-${btnid}`);
					mb.loadElement('FIELD_COUNT', true).value = 0;
					const _currentPage = fieldGridInstance.getPagination()._currentPage;
					mb.backTo(3, false, 0, _currentPage);
				}
				if (step == 4) {
					mb.loadMessage(msg, true);
					mb.removeElement(`for-customview-${btnid}`);
					mb.removeElement('FilterBTN', true);
					mb.loadElement('FILTER_COUNT', true).value = 0;
					const _currentPage = viewGridInstance.getPagination()._currentPage;
					mb.backTo(4, false, 0, _currentPage);
				}
				if (step == 5) {
					mb.loadMessage(msg, true);
					document.getElementById('LIST_COUNT').value = 0;
					mb.removeElement('RelatedLists', true);
					const _currentPage = listGridInstance.getPagination()._currentPage;
					mb.backTo(5, false, 0, _currentPage);
				}
			} else {
				if (step == 4) {
					const getData = viewGridInstance.getData();
					const filterAll = getData.find(e => e.viewname === 'All');
					if (!filterAll) {
						mb.loadMessage(mod_alert_arr.FirstFilterAll_msg, true, 'error');
						return false;
					}
				}
				mb.loadElement(`step-${step}`, true).style.display = 'none';
				var nextstep = step + 1;
				mb.loadElement(`step-${nextstep}`, true).style.display = 'block';
			}
			if (step == 3) {
				document.getElementById('FILTER_COUNT').value = 0;
				mb.removeElement('CustomView', true);
				mb.removeElement('loadViews', true);
			}
			//clean UI
			if (step == 1) {
				mb.VerifyModule();
				setTimeout(function () {
					mb.generateDefaultBlocks();
				}, 500);
			} else if (step == 2) {
				mb.backTo(3);
			} else if (step == 3 && forward != false) {
				mb.removeElement('loadFields', true);
				mb.backTo(4);
			} else if (step == 4 && forward != false) {
				document.getElementById('FILTER_COUNT').value = 0;
				document.getElementById('LIST_COUNT').value = 0;
				mb.removeElement('CustomView', true);
				mb.removeElement('loadViews', true);
				mb.backTo(5);
			} else if (step == 5 && forward != false) {
				mb.loadTemplate();
			}
		});
	},

	checkBlocks: (blockLabel) => {
		const currentBlocks = document.getElementById('ul-block-mb').getElementsByTagName('li');
		for (let i = 0; i < currentBlocks.length; i++) {
			if (currentBlocks[i].innerHTML.includes(blockLabel)) {
				return false;
			}
		}
		return true;
	},

	getRadioValue: (name) => {
		var el = document.getElementsByName(name);
		for (var i = 0; i < el.length; i++) {
			if (el[i].checked) {
				return el[i].value;
			}
		}
		return '';
	},

	VerifyModule: () => {
		const modulename = mb.loadElement('modulename');
		const data = {
			modulename: modulename
		};
		jQuery.ajax({
			method: 'POST',
			url: url+'&methodName=VerifyModule',
			data: data
		}).done(function (response) {
			mb.removeElement('loadBlocks', true);
			const res = JSON.parse(response);
			if (res.moduleid != 0) {
				if (res.step > 20) {
					const msg = mod_alert_arr.editmode;
					mb.loadMessage(msg, true);
				}
				//load blocks
				jQuery.ajax({
					method: 'GET',
					url: url+'&methodName=loadValues&step=2&moduleid='+res.moduleid,
				}).done(function (response) {
					const res = JSON.parse(response);
					const getDiv = mb.loadElement('loadBlocks', true);
					const ul = document.createElement('ul');
					ul.className = 'slds-has-dividers_top-space slds-list_ordered';
					ul.id = 'ul-block-mb';
					getDiv.appendChild(ul);
					for (let i = 0; i < res.length; i++) {
						const li = document.createElement('li');
						const id = res[i].blocksid+'-block';
						let removeBtn = `
							<div class="slds-button-group" role="group">
								<button onclick='mb.deleteBlocks("${id}")' class="slds-button slds-button_icon slds-button_icon-border-filled" aria-pressed="false">
									<svg class="slds-button__icon" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
									</svg>
								</button>
							</div>`;
						if (res[i].blocks_label.toUpperCase() == 'LBL_MODULEBLOCK_INFORMATION' || res[i].blocks_label.toUpperCase() == 'LBL_CUSTOM_INFORMATION' || res[i].blocks_label.toUpperCase() == 'LBL_DESCRIPTION_INFORMATION') {
							removeBtn = '';
						}
						li.innerHTML = res[i].blocks_label.toUpperCase()+removeBtn;
						li.className = 'slds-item';
						li.id = 'li-block-mb-'+res[i].blocksid;
						ul.appendChild(li);
					}
					mb.updateProgress(2);
					mb.removeElement('blocks_inputs', true);
					document.getElementById('BLOCK_COUNT').value = 0;
				});
			} else {
				const msg = mod_alert_arr.RecordSaved;
				mb.loadMessage(msg, true);
			}
		});
	},

	/**
	 * Go to back step
	 * @param {number} step
	 * @param {boolean} mod
	 * @param {number} moduleid
	 */
	backTo: (step, mod = false, moduleid = 0, _currentPage = 1) => {
		if (moduleid != 0) {
			localStorage.setItem('ModuleBuilderID', moduleid);
		}
		if (localStorage.getItem('ModuleBuilderID') == undefined) {
			return false;
		}
		let thisStep = step + 1;
		//remove `finish module` step
		mb.removeElement('info', true);
		mb.removeElement('blocks', true);
		mb.loadElement('step-6', true).style.display = 'none';
		if (mod && step == 3) {
			mb.removeElement('loadFields', true);
		}
		if (mod && step == 4) {
			mb.removeElement('loadViews', true);
		}
		if (mod == true) {
			for (let i = 1; i <=5; i++) {
				if (i != step) {
					mb.loadElement(`step-${i}`, true).style.display = 'none';
				}
			}
			mb.loadElement(`step-${step}`, true).style.display = '';
		} else {
			mb.loadElement(`step-${thisStep}`, true).style.display = 'none';
			mb.loadElement(`step-${step}`, true).style.display = '';
		}
		if (step == 1) {
			mb.removeElement('loadFields', true);
			mb.removeElement('loadViews', true);
			mb.removeElement('loadLists', true);
			document.getElementById('modulename').setAttribute('readonly', true);
			//load active module
			jQuery.ajax({
				method: 'GET',
				url: url+'&methodName=loadValues&step='+step+'&moduleid='+moduleid,
			}).done(function (response) {
				const res = JSON.parse(response);
				mb.loadElement('modulename', true).value = res.name;
				mb.loadElement('modulelabel', true).value = res.label;
				mb.loadElement('parentmenu', true).value = res.parent;
				mb.loadElement('moduleicon', true).value = res.icon;
				if (res.sharingaccess == 'private') {
					mb.loadElement('private', true).checked = 'private';
				} else {
					mb.loadElement('public', true).checked = 'public';
				}
				if (res.actions.merge == 'true') {
					mb.loadElement('merge', true).checked = 'true';
				}
				if (res.actions.import == 'true') {
					mb.loadElement('import', true).checked = 'true';
				}
				if (res.actions.export == 'true') {
					mb.loadElement('export', true).checked = 'true';
				}
				mb.updateProgress(1);
			});
		}

		if (step == 2) {
			mb.removeElement('loadFields', true);
			mb.removeElement('loadViews', true);
			mb.removeElement('loadLists', true);
			mb.generateDefaultBlocks();
			const getUl = mb.loadElement('ul-block-mb', true);
			if (getUl != null) {
				mb.removeElement('ul-block-mb');
			}
			//load blocks
			jQuery.ajax({
				method: 'GET',
				url: url+'&methodName=loadValues&step='+step+'&moduleid='+moduleid,
			}).done(function (response) {
				const res = JSON.parse(response);
				const getDiv = mb.loadElement('loadBlocks', true);
				const ul = document.createElement('ul');
				ul.className = 'slds-has-dividers_top-space slds-list_ordered';
				ul.id = 'ul-block-mb';
				getDiv.appendChild(ul);
				for (let i = 0; i < res.length; i++) {
					const li = document.createElement('li');
					const id = res[i].blocksid+'-block';
					let removeBtn = `
						<div class="slds-button-group" role="group">
							<button onclick='mb.deleteBlocks("${id}")' class="slds-button slds-button_icon slds-button_icon-border-filled" aria-pressed="false">
								<svg class="slds-button__icon" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
								</svg>
							</button>
						</div>`;
					if (res[i].blocks_label.toUpperCase() == 'LBL_CUSTOM_INFORMATION' || res[i].blocks_label.toUpperCase() == 'LBL_DESCRIPTION_INFORMATION') {
						removeBtn = '';
					}
					li.innerHTML = res[i].blocks_label.toUpperCase()+removeBtn;
					li.className = 'slds-item';
					li.id = 'li-block-mb-'+res[i].blocksid;
					ul.appendChild(li);
				}
				mb.updateProgress(2);
			});
		}
		if (step == 3) {
			mb.removeElement('loadFields', true);
			mb.removeElement('loadViews', true);
			mb.removeElement('loadLists', true);
			fieldGridInstance = new tuiGrid({
				el: document.getElementById('loadFields'),
				columns: [
					{
						name: 'blockname',
						header: mod_alert_arr.blockname,
					},
					{
						name: 'fieldname',
						header: mod_alert_arr.fieldname,
					},
					{
						name: 'fieldlabel',
						header: mod_alert_arr.fieldlabel,
					},
					{
						name: 'uitype',
						header: mod_alert_arr.uitype,
					},
					{
						name: 'typeofdata',
						header: mod_alert_arr.mandatory,
					},
					{
						name: 'action',
						header: mod_alert_arr.action,
						renderer: {
							type: BuilderActionRender,
							options: {
								type: 'Fields'
							}
						},
						width: 100
					}
				],
				data: {
					api: {
						readData: {
							url: url+'&methodName=loadValues&step='+step+'&moduleid='+moduleid,
							method: 'GET',
							initParams: {
								_currentPage: _currentPage
							}
						}
					}
				},
				useClientSort: false,
				pageOptions: {
					useClient: true,
					perPage: 10
				},
				rowHeight: 'auto',
				bodyHeight: 'auto',
				scrollX: false,
				scrollY: false,
				columnOptions: {
					resizable: true
				},
				header: {
					align: 'left',
					valign: 'top'
				}
			});
			tui.Grid.applyTheme('striped');
			mb.updateProgress(3);
		}
		if (step == 4) {
			mb.removeElement('loadFields', true);
			mb.removeElement('loadViews', true);
			mb.removeElement('loadLists', true);
			viewGridInstance = new tuiGrid({
				el: document.getElementById('loadViews'),
				columns: [
					{
						name: 'viewname',
						header: mod_alert_arr.viewname,
					},
					{
						name: 'setdefault',
						header: mod_alert_arr.setdefault,
					},
					{
						name: 'fields',
						header: mod_alert_arr.fields,
					},
					{
						name: 'action',
						header: mod_alert_arr.action,
						renderer: {
							type: BuilderActionRender,
							options: {
								type: 'CustomView'
							}
						},
						width: 100
					}
				],
				data: {
					api: {
						readData: {
							url: url+'&methodName=loadValues&step='+step+'&moduleid='+moduleid,
							method: 'GET',
							initParams: {
								_currentPage: _currentPage
							}
						}
					}
				},
				useClientSort: false,
				pageOptions: {
					useClient: true,
					perPage: 10
				},
				rowHeight: 'auto',
				bodyHeight: 'auto',
				scrollX: false,
				scrollY: false,
				columnOptions: {
					resizable: true
				},
				header: {
					align: 'left',
					valign: 'top'
				}
			});
			tui.Grid.applyTheme('striped');
			mb.updateProgress(4);
		}
		if (step == 5) {
			mb.removeElement('loadFields', true);
			mb.removeElement('loadViews', true);
			mb.removeElement('loadLists', true);
			listGridInstance = new tuiGrid({
				el: document.getElementById('loadLists'),
				columns: [
					{
						name: 'relatedmodule',
						header: mod_alert_arr.relatedmodule,
					},
					{
						name: 'actions',
						header: mod_alert_arr.actions,
					},
					{
						name: 'functionname',
						header: mod_alert_arr.functionname,
					},
					{
						name: 'label',
						header: mod_alert_arr.fieldlabel,
					},
					{
						name: 'action',
						header: mod_alert_arr.action,
						renderer: {
							type: BuilderActionRender,
							options: {
								type: 'RelatedLists'
							}
						},
						width: 100
					}
				],
				data: {
					api: {
						readData: {
							url: url+'&methodName=loadValues&step='+step+'&moduleid='+moduleid,
							method: 'GET',
							initParams: {
								_currentPage: _currentPage
							}
						}
					}
				},
				useClientSort: false,
				pageOptions: {
					useClient: true,
					perPage: 10
				},
				rowHeight: 'auto',
				bodyHeight: 'auto',
				scrollX: false,
				scrollY: false,
				columnOptions: {
					resizable: true
				},
				header: {
					align: 'left',
					valign: 'top'
				}
			});
			tui.Grid.applyTheme('striped');
			mb.updateProgress(5);
		}
	},
	/**
	 * Update progress bar in real time for step 1
	 * @param {number} step
	 */
	updateProgress: (step) => {
		if (step == 1) {
			const data = {
				modulename: mb.loadElement('modulename'),
				modulelabel: mb.loadElement('modulelabel'),
				parentmenu: mb.loadElement('parentmenu'),
				moduleicon: mb.loadElement('moduleicon'),
			};
			let modInfo = [];
			for (let i in data) {
				if (data[i] == '') {
					modInfo[i] = i;
				}
			}
			const size = Object.keys(modInfo).length;
			const progress = (20 - (parseInt(size) * 5));
			if (progress == 20) {
				mb.loadElement('btn-step-1', true).removeAttribute('disabled');
			} else {
				mb.loadElement('btn-step-1', true).setAttribute('disabled', '');
			}
			document.getElementById('block-information').classList.remove('slds-is-active');
			document.getElementById('field-information').classList.remove('slds-is-active');
			document.getElementById('filters').classList.remove('slds-is-active');
			document.getElementById('relationship').classList.remove('slds-is-active');
		} else {
			if (step == 2) {
				document.getElementById('block-information').classList.add('slds-is-active');
				document.getElementById('field-information').classList.remove('slds-is-active');
				document.getElementById('filters').classList.remove('slds-is-active');
				document.getElementById('relationship').classList.remove('slds-is-active');
			} else if (step == 3) {
				document.getElementById('block-information').classList.add('slds-is-active');
				document.getElementById('field-information').classList.add('slds-is-active');
				document.getElementById('filters').classList.remove('slds-is-active');
				document.getElementById('relationship').classList.remove('slds-is-active');
			} else if (step == 4) {
				document.getElementById('block-information').classList.add('slds-is-active');
				document.getElementById('field-information').classList.add('slds-is-active');
				document.getElementById('filters').classList.add('slds-is-active');
				document.getElementById('relationship').classList.remove('slds-is-active');
			} else if (step == 5) {
				document.getElementById('block-information').classList.add('slds-is-active');
				document.getElementById('field-information').classList.add('slds-is-active');
				document.getElementById('filters').classList.add('slds-is-active');
				document.getElementById('relationship').classList.add('slds-is-active');
			}
		}
	},
	/**
	 * Show module icons in step 1
	 * @param {string} iconReference
	 */
	showModuleIcon: (iconReference) => {
		let newicon = iconReference.split('-');
		let spn = mb.loadElement('moduleiconshow', true);
		let svg = mb.loadElement('moduleiconshowsvg', true);
		let curicon = svg.getAttribute('xlink:href');
		let category = curicon.substr(24);
		category = category.substr(0, category.indexOf('-'));
		let icon = curicon.substr(curicon.indexOf('#')+1);
		spn.classList.remove('slds-icon-'+category+'-'+icon);
		spn.classList.add('slds-icon-'+newicon[0]+'-'+newicon[1]);
		svg.setAttribute('xlink:href', 'include/LD/assets/icons/'+newicon[0]+'-sprite/svg/symbols.svg#'+newicon[1]);
	},
	/**
	 * generate Default Blocks
	 */
	generateDefaultBlocks: () => {
		mb.removeElement('blocks_inputs', true);
		mb.loadElement('BLOCK_COUNT').value = '1';
		jQuery.ajax({
			method: 'GET',
			url: url+'&methodName=loadDefaultBlocks',
		}).done(function (response) {
			const res = JSON.parse(response);
			if (res == 'load') {
				setTimeout(function () {
					mb.generateInput('default');
				}, 1000);
			} else {
				mb.loadElement('BLOCK_COUNT', true).value = 0;
				mb.generateInput();
			}
		});
	},
	/**
	 * Generate block input for step 2
	 */
	generateInput: (type = '') => {
		if (type == 'default') {
			const modulename = mb.loadElement('modulename').toUpperCase();
			const MODULEBLOCK = document.createElement('input');
			MODULEBLOCK.type = 'text';
			MODULEBLOCK.id = 'blocks_label_1';
			MODULEBLOCK.value = `LBL_${modulename}_INFORMATION`; //change this to modulename
			MODULEBLOCK.className ='slds-input';
			mb.loadElement('blocks_inputs', true).appendChild(MODULEBLOCK);
			const CUSTOM = document.createElement('input');
			CUSTOM.type = 'text';
			CUSTOM.id = 'blocks_label_2';
			CUSTOM.value = 'LBL_CUSTOM_INFORMATION';
			CUSTOM.className ='slds-input';
			mb.loadElement('blocks_inputs', true).appendChild(CUSTOM);
			const DESCRIPTION = document.createElement('input');
			DESCRIPTION.type = 'text';
			DESCRIPTION.id = 'blocks_label_3';
			DESCRIPTION.value = 'LBL_DESCRIPTION_INFORMATION';
			DESCRIPTION.className ='slds-input';
			mb.loadElement('blocks_inputs', true).appendChild(DESCRIPTION);
			mb.loadElement('BLOCK_COUNT', true).value = '3';
		} else {
			const BLOCK_COUNT = mb.autoIncrementIds('BLOCK_COUNT');
			const input = document.createElement('input');
			input.type = 'text';
			input.id = 'blocks_label_' + BLOCK_COUNT;
			input.placeholder = 'LBL_BLOCKNAME_INFORMATION';
			input.className ='slds-input';
			mb.loadElement('blocks_inputs', true).appendChild(input);
		}
	},
	/**
	 * Generate field input for step 3
	 */
	generateFields: (fieldid = 0) => {
		let textfields = [{
			type: mod_alert_arr.fieldname,
			value: 'fieldname',
		},
		{
			type: mod_alert_arr.fieldlabel,
			value: 'fieldlabel',
		},
		{
			type: mod_alert_arr.fieldlength,
			value: 'fieldlength',
		},
		{
			type: mod_alert_arr.picklistvalues,
			value: 'picklistvalues',
		},
		{
			type: mod_alert_arr.generatedtype,
			value: 'generatedtype',
		},
		{
			type: mod_alert_arr.relatedmodules,
			value: 'relatedmodules',
		}];
		let fieldtypes = [{
			type: 'UItype',
			values: {
				0: '',
				1: mod_alert_arr.LineText,
				21: mod_alert_arr.BlockTextSmall,
				19: mod_alert_arr.BlockTextLarge,
				4: mod_alert_arr.AutoGenerated,
				5: mod_alert_arr.Date,
				50: mod_alert_arr.DateTime,
				14: mod_alert_arr.Time,
				7: mod_alert_arr.Number,
				71: mod_alert_arr.Currency,
				9: mod_alert_arr.Percentage,
				10: mod_alert_arr.RelationModule,
				101: mod_alert_arr.RelationUsers,
				11: mod_alert_arr.Phone,
				13: mod_alert_arr.Email,
				17: mod_alert_arr.URL,
				56: mod_alert_arr.Checkbox,
				69: mod_alert_arr.Image,
				85: mod_alert_arr.Skype,
				15: mod_alert_arr.SelectWithRole,
				16: mod_alert_arr.Select,
				1613: mod_alert_arr.SelectModules,
				1024: mod_alert_arr.SelectRoles,
				33: mod_alert_arr.SelectMultiple,
				3313: mod_alert_arr.SelectModulesMultiple,

			}
		},
		{
			type: 'Presence',
			values: {
				0: mod_alert_arr.AlwaysActive,
				1: mod_alert_arr.InactiveActive,
				2: mod_alert_arr.ActiveActive,
			}
		},
		{
			type: 'Quickcreate',
			values: {
				0: mod_alert_arr.AlwaysShownNoDeactivate,
				1: mod_alert_arr.NotShownCanBeActivated,
				2: mod_alert_arr.ShownCanBeDeactivated,
				3: mod_alert_arr.NotShownCanNotBeActivated,
			}
		}];
		let mandatory = [{
			type: 'Typeofdata',
			values: {
				'O': mod_alert_arr.optional,
				'M': mod_alert_arr.mandatory,
			}
		},
		{
			type: 'Displaytype',
			values: {
				1: mod_alert_arr.DisplayEverywhere,
				2: mod_alert_arr.ReadOnly,
				3: mod_alert_arr.DisplayByProgrammer,
				4: mod_alert_arr.ReadOnlyModifyWorkflow,
				5: mod_alert_arr.DisplayCreate,
			}
		},
		{
			type: 'Masseditable',
			values: {
				1: mod_alert_arr.MassEditable,
				0: mod_alert_arr.NoMassEditNoActivate,
				2: mod_alert_arr.NoMassEditActivate,
			},
		}];
		const checkboxFields = [];
		if (fieldid > 0) {
			mb.clearField(1);
		}
		if (document.getElementById('for-field-1') && fieldid == 0) {
			const msg = mod_alert_arr.fieldprocces;
			mb.loadMessage(msg, true, 'error');
			return;
		}
		const FIELD_COUNT = mb.autoIncrementIds('FIELD_COUNT');
		const table = mb.getTable('Table');
		const row = mb.createRow(table, 0, 'for-field-inputs-', FIELD_COUNT);
		const cell = mb.createCell(row, 0, 'fields_inputs_', FIELD_COUNT);

		mb.loadBlocks(table, FIELD_COUNT);

		let inStyle = {
			'style': 'margin: 5px',
			'id': '',
			'onchange': '',
			'placeholder': '',
		};
		let fieldTemplate = '<div class="slds-grid slds-gutters">';
		for (var key in textfields) {
			switch(textfields[key].value) {
				case 'relatedmodules':
					inStyle.style = 'margin: 5px; display: none';
					inStyle.id = `show-field-${textfields[key].value}-${FIELD_COUNT}`;
					inStyle.placeholder = 'Value 1,Value 2,...';
					fieldTemplate += `
					<div class="slds-col" style="${inStyle.style}" id="${inStyle.id}">
						<div class="slds-form-element">
						<label class="slds-form-element__label" for="${textfields[key].value}_${FIELD_COUNT}">
							<abbr class="slds-required" title="required">* </abbr> ${textfields[key].type}
						</label>
						<div class="slds-form-element__control">
							<input type="hidden" name="${textfields[key].value}_${FIELD_COUNT}" placeholder="${inStyle.placeholder}" id="${textfields[key].value}_${FIELD_COUNT}" class="slds-input" />
							<div class="slds-pill_container" onclick="mb.loadModules('load-mods')">
								<ul class="slds-listbox slds-listbox_horizontal" role="listbox" id="show-pills">
								</ul>
							</div>
							<span id="load-mods" class="closeList"></span>
						</div>
						</div>
					</div>`;
					break;
				case 'picklistvalues':
					inStyle.style = 'margin: 5px; display: none';
					inStyle.id = `show-field-${textfields[key].value}-${FIELD_COUNT}`;
					inStyle.placeholder = 'Value 1,Value 2,...';
					fieldTemplate += `
					<div class="slds-col" style="${inStyle.style}" id="${inStyle.id}">
						<div class="slds-form-element">
						<label class="slds-form-element__label" for="${textfields[key].value}_${FIELD_COUNT}">
							<abbr class="slds-required" title="required">* </abbr> ${textfields[key].type}
						</label>
						<div class="slds-form-element__control">
							<input type="text" name="${textfields[key].value}_${FIELD_COUNT}" placeholder="${inStyle.placeholder}" id="${textfields[key].value}_${FIELD_COUNT}" class="slds-input" />
						</div>
						</div>
					</div>`;
					break;
				case 'fieldlength':
					fieldTemplate += `
					<div class="slds-col" style="${inStyle.style}" id="show-fieldlength-${FIELD_COUNT}">
						<div class="slds-form-element">
						<label class="slds-form-element__label" for="${textfields[key].value}_${FIELD_COUNT}">
							<abbr class="slds-required" title="required">* </abbr> ${textfields[key].type}
						</label>
						<div class="slds-form-element__control">
							<input type="text" name="${textfields[key].value}_${FIELD_COUNT}" placeholder="${inStyle.placeholder}" id="${textfields[key].value}_${FIELD_COUNT}" class="slds-input" />
						</div>
						</div>
					</div>`;
					break;
				case 'generatedtype':
					inStyle.style = 'display: none';
					fieldTemplate += `
					<div class="slds-col" style="${inStyle.style}" id="show-generatedtype-${FIELD_COUNT}">
						<div class="slds-form-element">
						<label class="slds-form-element__label" for="${textfields[key].value}_${FIELD_COUNT}">
							${textfields[key].type}
						</label>
						<div class="slds-form-element__control">
							<input type="text" name="${textfields[key].value}_${FIELD_COUNT}" placeholder="" id="${textfields[key].value}_${FIELD_COUNT}" class="slds-input" />
						</div>
						</div>
					</div>`;
					break;
				default:
					fieldTemplate += `
					<div class="slds-col" style="${inStyle.style}" id="${inStyle.id}">
						<div class="slds-form-element">
						<label class="slds-form-element__label" for="${textfields[key].value}_${FIELD_COUNT}">
							<abbr class="slds-required" title="required">* </abbr> ${textfields[key].type}
						</label>
						<div class="slds-form-element__control">
							<input type="text" name="${textfields[key].value}_${FIELD_COUNT}" placeholder="${inStyle.placeholder}" id="${textfields[key].value}_${FIELD_COUNT}" class="slds-input" />
						</div>
						</div>
					</div>`;
			}
		}
		fieldTemplate += '</div><div class="slds-grid slds-gutters">';

		for (i = 0; i < mandatory.length; i++) {
			const type = mandatory[i].type;
			const values = mandatory[i].values;
			const selecttype = document.createElement('select');
			if (type == 'UItype') {
				inStyle.onchange = 'mb.showNewOptions(this, FIELD_COUNT)';
			}
			fieldTemplate += `
			<div class="slds-col">
				<div class="slds-form-element">
					<label class="slds-form-element__label" for="${type}_${FIELD_COUNT}">${type}</label>
					<div class="slds-form-element__control">
						<div class="slds-select_container">
							<select class="slds-select" id="${type}_${FIELD_COUNT}" onchange="${inStyle.onchange}">`;
								for (let j in values) {
									let selected = '';
									if (type == 'Masseditable' && j == 1) {
										selected = 'selected';
									}
									fieldTemplate += `<option value="${j}" ${selected}>${values[j]}</option>`;
								}
								fieldTemplate += `
							</select>
						</div>
					</div>
				</div>
			</div>
			`;
		}

		fieldTemplate += '</div><div class="slds-grid slds-gutters">';
		for (i = 0; i < fieldtypes.length; i++) {
			const type = fieldtypes[i].type;
			const values = fieldtypes[i].values;
			const selecttype = document.createElement('select');
			if (type == 'UItype') {
				inStyle.onchange = 'mb.showNewOptions(this, FIELD_COUNT)';
			}
			fieldTemplate += `
			<div class="slds-col">
				<div class="slds-form-element">
					<label class="slds-form-element__label" for="${type}_${FIELD_COUNT}">${type}</label>
					<div class="slds-form-element__control">
						<div class="slds-select_container">
							<select class="slds-select" id="${type}_${FIELD_COUNT}" onchange="${inStyle.onchange}">`;
								for (let j in values) {
									if (values[j] == '') {
										fieldTemplate += `<option value="" selected disabled></option>`;
									} else {
										fieldTemplate += `<option value="${j}">${values[j]}</option>`;
									}
								}
								fieldTemplate += `
							</select>
						</div>
					</div>
				</div>
			</div>
			`;
		}
		fieldTemplate += '</div><div class="slds-grid slds-gutters">';

		for (let i = 0; i < checkboxFields.length; i++) {
			fieldTemplate += `
			<div class="slds-col"><br>
				<div class="slds-form-element">
					<div class="slds-form-element__control">
						<div class="slds-checkbox">
							<input type="checkbox" name="${checkboxFields[i].type}_${FIELD_COUNT}" id="${checkboxFields[i].type}_${FIELD_COUNT}"/>
							<label class="slds-checkbox__label" for="${checkboxFields[i].type}_${FIELD_COUNT}">
								<span class="slds-checkbox_faux"></span>
								<span class="slds-form-element__label">${checkboxFields[i].value}</span>
							</label>
						</div>
					</div>
				</div><br>
			</div>`;
		}
		//create save button for each field
		fieldTemplate += `</div>
		<div class="slds-grid slds-gutters">
			<div class="slds-col"><br>
				<button class="slds-button slds-button_neutral slds-button_dual-stateful" id="save-btn-for-field-${FIELD_COUNT}" onclick="mb.SaveModule(3, false, this.id, ${fieldid})">
					<svg class="slds-button__icon slds-button__icon_small slds-button__icon_left" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
					</svg>${mod_alert_arr.LBL_MB_SAVEFIELD}
				</button>
				<button class="slds-button slds-button_destructive slds-button_dual-stateful" id="clear-btn-for-field-${FIELD_COUNT}" onclick="mb.clearField(1)">
					<svg class="slds-button__icon slds-button__icon_small slds-button__icon_left" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
					</svg>${mod_alert_arr.LBL_MB_CLEAR}
				</button>
			</div>
		</div>`;
		mb.loadElement(`fields_inputs_${FIELD_COUNT}`, true).innerHTML = fieldTemplate;
		if (fieldid > 0) {
			jQuery.ajax({
				method: 'GET',
				url: url+'&methodName=loadTemplate&recordid='+fieldid,
			}).done(function (response) {
				const state = JSON.parse(response).fields.data.contents[0];
				document.getElementById(`fieldname_${FIELD_COUNT}`).value = state.fieldname;
				document.getElementById(`fieldlabel_${FIELD_COUNT}`).value = state.fieldlabel;
				document.getElementById(`Typeofdata_${FIELD_COUNT}`).value = state.typeofdata;
				document.getElementById(`Displaytype_${FIELD_COUNT}`).value = state.displaytype;
				document.getElementById(`Masseditable_${FIELD_COUNT}`).value = state.masseditable;
				document.getElementById(`UItype_${FIELD_COUNT}`).value = state.uitype;
				document.getElementById(`Presence_${FIELD_COUNT}`).value = state.presence;
				document.getElementById(`Quickcreate_${FIELD_COUNT}`).value = state.quickcreate;
				const uitype = document.getElementById(`UItype_${FIELD_COUNT}`);
				const field_c = document.getElementById(`FIELD_COUNT`);
				mb.showNewOptions(uitype, field_c);
				document.getElementById(`fieldlength_${FIELD_COUNT}`).value = state.fieldlength;
				document.getElementById(`picklistvalues_${FIELD_COUNT}`).value = state.picklistvalues;
				document.getElementById(`relatedmodules_${FIELD_COUNT}`).value = state.relatedmodules;
				document.getElementById(`generatedtype_${FIELD_COUNT}`).value = state.generatedtype;
				if (state.uitype == '10') {
					const relatedmodules = state.relatedmodules.split(',');
					for (let r in relatedmodules) {
						if (relatedmodules[r] != '') {
							mb.setModuleValues(relatedmodules[r]);
						}
					}
				}
				const blockel = document.getElementsByName(`select-for-field-1`);
				for (let i = 0; i < blockel.length; i++) {
					if (parseInt(state.blockid) == parseInt(blockel[i].value)) {
						blockel[i].checked = true;
					}
				}
			});
		}
	},

	showNewOptions: (state, id) => {
		switch(state.value) {
			case '10':
				document.getElementById(`fieldlength_${id.value}`).value = '';
				document.getElementById(`show-field-relatedmodules-${id.value}`).style.display = '';
				document.getElementById(`show-field-picklistvalues-${id.value}`).style.display = 'none';
				document.getElementById(`show-fieldlength-${id.value}`).style.display = 'none';
				document.getElementById(`show-generatedtype-${id.value}`).style.display = 'none';
				break;
			case '15':
			case '16':
			case '33':
				document.getElementById(`fieldlength_${id.value}`).value = '';
				document.getElementById(`show-field-picklistvalues-${id.value}`).style.display = '';
				document.getElementById(`show-field-relatedmodules-${id.value}`).style.display = 'none';
				document.getElementById(`show-fieldlength-${id.value}`).style.display = 'none';
				document.getElementById(`show-generatedtype-${id.value}`).style.display = 'none';
				break;
			case '1':
			case '19':
			case '21':
			case '13':
			case '11':
			case '7':
				document.getElementById(`show-fieldlength-${id.value}`).style.display = '';
				document.getElementById(`show-generatedtype-${id.value}`).style.display = 'none';
				document.getElementById(`show-field-relatedmodules-${id.value}`).style.display = 'none';
				document.getElementById(`show-field-picklistvalues-${id.value}`).style.display = 'none';
				break;
			case '5':
			case '50':
			case '14':
				document.getElementById(`show-generatedtype-${id.value}`).style.display = '';
				document.getElementById(`show-fieldlength-${id.value}`).style.display = 'none';
				document.getElementById(`show-field-relatedmodules-${id.value}`).style.display = 'none';
				document.getElementById(`show-field-picklistvalues-${id.value}`).style.display = 'none';
				break;
			default:
				document.getElementById(`fieldlength_${id.value}`).value = '';
				document.getElementById(`show-generatedtype-${id.value}`).style.display = 'none';
				document.getElementById(`show-fieldlength-${id.value}`).style.display = 'none';
				document.getElementById(`show-field-relatedmodules-${id.value}`).style.display = 'none';
				document.getElementById(`show-field-picklistvalues-${id.value}`).style.display = 'none';
			//
		}
	},

	clearField: (id) => {
		mb.removeElement(`for-field-${id}`);
		mb.removeElement(`for-field-inputs-${id}`);
		mb.loadElement('FIELD_COUNT', true).value = 0;
	},

	clearView: (id) => {
		mb.removeElement(`for-customview-${id}`);
		mb.removeElement('FilterBTN', true);
		mb.loadElement('FILTER_COUNT', true).value = 0;
	},

	clearList: (id) => {
		mb.removeElement(`for-related-${id}`);
		mb.loadElement('LIST_COUNT', true).value = 0;
	},
	/**
	 * Open tui grid to list all modules
	 */
	openModal: (_currentPage = 1) => {
		dataGridInstance = new tuiGrid({
			el: document.getElementById('moduleListView'),
			columns: [
				{
					name: 'modulebuilder_name',
					header: mod_alert_arr.ModuleName,
				},
				{
					name: 'date',
					header: mod_alert_arr.DateCreated,
				},
				{
					name: 'completed',
					header: mod_alert_arr.Status,
				},
				{
					name: 'export',
					header: mod_alert_arr.Export,
				}
			],
			data: {
				api: {
					readData: {
						url: url+'&methodName=loadModules',
						method: 'GET',
						initParams: {
							_currentPage: _currentPage
						}
					}
				}
			},
			useClientSort: false,
			pageOptions: {
				useClient: true,
				perPage: 5
			},
			rowHeight: 'auto',
			bodyHeight: 'auto',
			scrollX: false,
			scrollY: false,
			columnOptions: {
				resizable: true
			},
			header: {
				align: 'left',
				valign: 'top'
			},
			onGridUpdated: (ev) => {
				mb.updateData();
			}
		});
		tui.Grid.applyTheme('clean');
		mb.loadElement('moduleListsModal', true).style.display = '';
	},
	/**
	 * Close modal
	 */
	closeModal: () => {
		mb.loadElement('moduleListsModal', true).style.display = 'none';
		document.getElementById('moduleListView').innerHTML = '';
	},
	/**
	 * Load all blocks for specific module in step 3
	 * @param {Table} tableInstance - Current table instance
	 * @param {number} FIELD_COUNT
	 */
	loadBlocks: (tableInstance, FIELD_COUNT) => {
		jQuery.ajax({
			method: 'GET',
			url: url+'&methodName=loadBlocks',
		}).done(function (response) {
			const res = JSON.parse(response);
			const row = tableInstance.insertRow(0);
			row.setAttribute('id', `for-field-${FIELD_COUNT}`);
			let template = `
				<fieldset class="slds-form-element">
				<legend class="slds-form-element__legend slds-form-element__label">${mod_alert_arr.LBL_CHOOSEFIELDBLOCK} ${FIELD_COUNT}</legend>
				<div class="slds-form-element__control">
					<div class="slds-radio_button-group">`;
			let checked = '';
			for (var i = 0; i < res.length; i++) {
				if (i === 0) {
					checked = 'checked';
					template += `
					<span class="slds-button slds-radio_button">
						<input type="radio" ${checked} name="select-for-field-${FIELD_COUNT}" id="radio-${res[i].blocksid}${FIELD_COUNT}" value="${res[i].blocksid}" />
						<label class="slds-radio_button__label" for="radio-${res[i].blocksid}${FIELD_COUNT}">
						<span class="slds-radio_faux">${res[i].blocks_label}</span>
						</label>
					</span>`;
				} else {
					template += `
					<span class="slds-button slds-radio_button">
						<input type="radio" name="select-for-field-${FIELD_COUNT}" id="radio-${res[i].blocksid}${FIELD_COUNT}" value="${res[i].blocksid}" />
						<label class="slds-radio_button__label" for="radio-${res[i].blocksid}${FIELD_COUNT}">
						<span class="slds-radio_faux">${res[i].blocks_label}</span>
						</label>
					</span>`;
				}
			}
			template += `
				</div>
			</div>
			</fieldset>`;
			document.getElementById(`for-field-${FIELD_COUNT}`).innerHTML = template;
		});
	},
	/**
	 * Generate inputs for custom views in step 4
	 */
	generateCustomView: (filterid = 0) => {
		if (filterid > 0) {
			mb.clearView(1);
		}
		const FILTER_COUNT = mb.autoIncrementIds('FILTER_COUNT');
		const table = mb.getTable('CustomView');
		if (document.getElementById('for-customview-1') && filterid == 0) {
			const msg = mod_alert_arr.filterprocces;
			mb.loadMessage(msg, true, 'error');
			return false;
		}
		// get custom view number for filter
		const modulename = mb.loadElement('modulename');
		const _data = {
			modulename: modulename
		};
		jQuery.ajax({
			method: 'POST',
			url: url+'&methodName=getCountFilter',
			data: _data
		}).done(function (response) {
			document.getElementsByName('cvnumber').value = response;
		});
		const row = mb.createRow(table, 0, 'for-customview-', FILTER_COUNT);
		const cell = mb.createCell(row, 0, 'customview_inputs', FILTER_COUNT);
		//create viewname
		const inStyle = {
			'style': 'width: 25%'
		};
		let setdefaultOption = [{
			true:  alert_arr.YES,
			false: alert_arr.NO,
		}];
		var viewTemplate = `
		<div class="slds-grid slds-gutters">
			<div class="slds-col slds-size_1-of-2">
				<div class="slds-form-element">
				<label class="slds-form-element__label" for="viewname-${FILTER_COUNT}">
					<abbr class="slds-required" title="required">* </abbr> ${mod_alert_arr.filter}
				</label>
				<div class="slds-form-element__control">
					<input type="text" placeholder="All" name="viewname-${FILTER_COUNT}" id="viewname-${FILTER_COUNT}" class="slds-input"/>
				</div>
				</div>
			</div>
			<div class="slds-col slds-size_1-of-2">
				<div class="slds-form-element">
					<label class="slds-form-element__label" for="setdefault-${FILTER_COUNT}">${mod_alert_arr.default}</label>
					<div class="slds-form-element__control">
						<div class="slds-select_container">
							<select class="slds-select" name="setdefault-${FILTER_COUNT}" id="setdefault-${FILTER_COUNT}">`;
							for (let val in setdefaultOption[0]) {
								viewTemplate += `<option value="${val}">${setdefaultOption[0][val]}</option>`;
							}
							viewTemplate += `
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>`;

		//get all fields
		viewTemplate += `
		<div class="slds-grid slds-gutters">
			<div class="slds-col"><br>
				<label class="slds-form-element__label">
					<abbr class="slds-required" title="required">* </abbr> ${mod_alert_arr.LBL_CHOOSECUSTOMVIEW}
				</label>
			</div>
		</div>
		<div class="slds-col slds-size_1-of-2">
			<input type="hidden" name="viewfields-${FILTER_COUNT}" id="viewfields-${FILTER_COUNT}">
			<div class="slds-pill_container" onclick="mb.loadFields('load-fields', ${FILTER_COUNT})">
			  <ul class="slds-listbox slds-listbox_horizontal" role="listbox" id="show-fields">
			  </ul>
			  <span id="load-fields" class="closeList"></span>
			</div>
		</div>`;
		mb.loadElement(`customview_inputs${FILTER_COUNT}`, true).innerHTML = viewTemplate;
		//create save button for each field
		if (filterid > 0) {
			//get values for filters
			jQuery.ajax({
				method: 'GET',
				url: url+'&methodName=loadTemplate&recordid='+filterid,
			}).done(function (response) {
				const state = JSON.parse(response).views.data.contents[0];
				document.getElementById(`viewname-${FILTER_COUNT}`).value = state.viewname;
				document.getElementById(`setdefault-${FILTER_COUNT}`).value = state.setdefault;
				for (let f in state.fields) {
					if (typeof state.fields[f] == 'object') {
						mb.setFieldValues(state.fields[f].fieldname, state.fields[f].fieldsid, FILTER_COUNT);
					}
				}
			});
		}
		let btnTemplate = `
		<div class="slds-grid slds-gutters">
			<button class="slds-button slds-button_neutral slds-button_dual-stateful" id="save-btn-for-view-${FILTER_COUNT}" onclick="mb.SaveModule(4, false, this.id, ${filterid})">
				<svg class="slds-button__icon slds-button__icon_small slds-button__icon_left" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
				</svg>${mod_alert_arr.LBL_MB_SAVE}
			</button>
			<button class="slds-button slds-button_destructive slds-button_dual-stateful" id="clear-btn-for-view-${FILTER_COUNT}" onclick="mb.clearView(${FILTER_COUNT})">
				<svg class="slds-button__icon slds-button__icon_small slds-button__icon_left" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
				</svg>${mod_alert_arr.LBL_MB_CLEAR}
			</button>
		</div>`;
		mb.loadElement('FilterBTN', true).innerHTML = btnTemplate;
	},

	loadFields: (id, FILTER_COUNT) => {
		jQuery.ajax({
			method: 'GET',
			url: url+'&methodName=loadFields',
		}).done(function (response) {
			let res = JSON.parse(response);
			const inStyle = {
				style: `background: white;
				border: 1px solid #d1d1d1;
				position: absolute;
				z-index: 1000;
				height: 200px;
				width: 15%;
				overflow:hidden;
				overflow-y:scroll;`
			};
			let listFields = `<ul class="slds-dropdown__list slds-dropdown__scroll" style="${inStyle.style}">`;
			const ids = document.getElementById(`viewfields-${FILTER_COUNT}`).value;
			let show = false;
			for (let r in res) {
				if (!ids.includes(res[r].fieldsid)) {
					listFields += `
					<li class="slds-dropdown__item">
						<a id="${res[r].fieldsid}" onclick="mb.setFieldValues('${res[r].fieldname}', '${res[r].fieldsid}', ${FILTER_COUNT})">
							<span class="slds-truncate">${res[r].fieldname}</span>
						</a>
					</li>`;
				show = true;
				}
			}
			listFields += '</ul>';
			if (show) {
				document.getElementById(`${id}`).innerHTML = listFields;
			}
		});
	},

	setFieldValues: (fieldaname, fieldid, FILTER_COUNT) => {
		let template = `
		<li class="slds-listbox-item" role="presentation" id="${fieldid}">
			<span class="slds-pill" role="option" aria-selected="true">
			<span class="slds-pill__label" title="${fieldaname}">${fieldaname}</span>
			<span class="slds-icon_container slds-pill__remove" id="remove-${fieldid}" onclick="mb.removeFieldForFilter(this, ${FILTER_COUNT})" style="cursor: pointer">
				<svg class="slds-icon slds-icon_x-small slds-icon-text-default" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
				</svg>
			</span>
			</span>
		</li>`;
		const fields = document.getElementById(`viewfields-${FILTER_COUNT}`).value;
		const getfields = document.getElementById('show-fields').innerHTML;
		const pills = `${getfields}${template}`;
		let newValue = `${fields},${fieldid}`;
		const parseId = newValue.split(',');
		newValue = '';
		for (let p in parseId) {
			if (parseId[p] != '') {
				newValue += `${parseId[p]},`;
			}
		}
		document.getElementById(`viewfields-${FILTER_COUNT}`).value = newValue;
		document.getElementById('show-fields').innerHTML = pills;
	},

	removeFieldForFilter: (el, FILTER_COUNT) => {
		let id = el.id.split('-')[1];
		const pills = document.getElementById(`${id}`);
		let objValues;
		let newValues = '';
		pills.parentNode.removeChild(pills);
		const mods = document.getElementById(`viewfields-${FILTER_COUNT}`).value;
		objValues = mods.split(',');
		for (let i in objValues) {
			if (objValues[i] != id && objValues[i] != '') {
				newValues += `${objValues[i]},`;
			}
		}
		document.getElementById(`viewfields-${FILTER_COUNT}`).value = newValues;
	},

	/**
	 * Function that load an alert message for success or error
	 * @param {text} msg
	 * @param {boolean} show
	 * @param {text} type - success/error
	 */
	loadMessage: (msg, show = true, type = 'success') => {
		var icon = 'task';
		if (type == 'error') {
			icon = 'first_non_empty';
		}
		if (show == true) {
			ldsPrompt.show(type.toUpperCase(), msg, type);
		}
	},
	/**
	 * Increment id from each step when generate fields
	 * @param {string} id
	 */
	autoIncrementIds: (id) => {
		let number = mb.loadElement(id);
		number = parseInt(number) + 1;
		mb.loadElement(id, true).value = number;
		return number;
	},
	/**
	 * Update grid in every change
	 */
	updateData: () => {
		let btn = '';
		for (var i = 0; i < dataGridInstance.getRowCount(); i++) {
			let completed = dataGridInstance.getValue(i, 'completed');
			let moduleid = dataGridInstance.getValue(i, 'moduleid');
			if (completed == 'Completed') {
				btn = `
				<div class="slds-button-group" role="group">
					<button onclick="mb.generateManifest(${moduleid})" class="slds-button slds-button_icon slds-button_icon-border-filled" aria-pressed="false">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#download"></use>
						</svg>
					</button>
					<button onclick="mb.backTo(5, true, ${moduleid}); mb.closeModal()" class="slds-button slds-button_icon slds-button_icon-border-filled" aria-pressed="false">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
						</svg>
					</button>
					<button onclick='mb.deleteModule(${moduleid})' class="slds-button slds-button_icon slds-button_icon-border-filled" aria-pressed="false">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
						</svg>
					</button>
				</div>
				`;
			} else {
				let step = 0;
				if (completed == '20%') {
					step = 1;
				} else if (completed == '40%') {
					step = 2;
				} else if (completed == '60%') {
					step = 3;
				} else if (completed == '80%') {
					step = 4;
				}
				btn = `
				<div class="slds-button-group" role="group">
					<button onclick="mb.backTo(${step}, true, ${moduleid}); mb.closeModal()" class="slds-button slds-button_icon slds-button_icon-border-filled" aria-pressed="false">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
						</svg>
					</button>
					<button onclick='mb.deleteModule(${moduleid})' class="slds-button slds-button_icon slds-button_icon-border-filled" aria-pressed="false">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
						</svg>
					</button>
				</div>
				`;
			}
			dataGridInstance.setValue(i, 'export', btn, false);
		}
	},
	/**
	 * Check for module if exists in first step
	 * @param {string} id
	 */
	checkForModule: (id) => {
		const moduleName = mb.loadElement(id);
		jQuery.ajax({
			method: 'POST',
			url: url,
			data: 'modulename='+moduleName+'&methodName=checkForModule'
		}).done(function (response) {
			if (response == 1) {
				const msg = moduleName+' '+mod_alert_arr.Module+' '+mod_alert_arr.AlreadyExists;
				mb.loadMessage(msg, true, 'error');
			} else {
				mb.loadMessage('', false);
			}
		});
	},
	/**
	 * Autocomplete inputs for modules and function names
	 * @param {string} el
	 * @param {string} type - module/name
	 */
	autocomplete: (el, type) => {
		const forId = el.id.split('-')[2];
		const val = mb.loadElement(el.id);
		let method = 'name';
		if (type == 'module') {
			method = type;
		}
		jQuery.ajax({
			method: 'POST',
			url: url,
			data: 'query='+val+'&methodName=autocomplete&method='+method
		}).done(function (response) {
			mb.removeElement('autocomplete-modulespan-'+forId, true);
			let res = JSON.parse(response);
			if (response.length < 3) {
				mb.removeElement('autocomplete-modulespan-'+forId, true);
			} else {
				const inStyle = {
					style: `background: white;
					border: 1px solid #d1d1d1;
					position: absolute;
					z-index: 1000;
					width: 100%`
				};
				let span = document.createElement('span');
				let ul = `<ul class="slds-dropdown__list" style="${inStyle.style}">`;
				for (let i = 0; i < res.length; i++) {
					ul += `<li class="slds-dropdown__item">
							<a onclick="mb.setValueToInput(this.id, ${forId}, '${method}')" tabindex="${i}" id="${res[i]}">
								<span class="slds-truncate">${res[i]}</span>
							</a>
						</li>`;
				}
				ul += '</ul>';
				span.innerHTML = ul;
				if (type == 'module') {
					mb.loadElement('autocomplete-modulespan-'+forId, true).appendChild(span);
				}
			}
		});
	},
	/**
	 * Set values for each input on autocomplete
	 * @param {string} name - function name
	 * @param {string} forId
	 * @param {string} type - module/name
	 */
	setValueToInput: (name, forId, type) => {
		if (type == 'module') {
			mb.removeElement('autocomplete-modulespan-'+forId, true);
			mb.loadElement('autocomplete-module-'+forId, true).value = name;
		}
	},
	/**
	 * Generate related lists for step 5
	 */
	generateRelatedList: (relatedid = 0) => {
		if (relatedid > 0) {
			mb.clearList(1);
		}
		const LIST_COUNT = mb.autoIncrementIds('LIST_COUNT');
		const table = mb.getTable('RelatedLists');
		if (document.getElementById('for-related-1') && relatedid == 0) {
			const msg = mod_alert_arr.relatedprocces;
			mb.loadMessage(msg, true, 'error');
			return;
		}
		const row = mb.createRow(table, 0, 'for-related-', LIST_COUNT);
		const cell = mb.createCell(row, 0, 'related_inputs_', LIST_COUNT);
		let listTemplate = `
		<div class="slds-grid slds-gutters">
			<div class="slds-col">
				<div class="slds-form-element">
					<label class="slds-form-element__label" for="autocomplete-related-${LIST_COUNT}">
						<abbr class="slds-required" title="required">* </abbr> Function name
					</label>
					<div class="slds-form-element__control">
						<div class="slds-select_container">
							<select name="related-function-${LIST_COUNT}" id="autocomplete-related-${LIST_COUNT}" class="slds-select">
								<option value="get_dependents_list">get_dependents_list</option>
								<option value="get_relatedlist_list">get_relatedlist_list</option>
								<option value="get_attachments">get_attachments</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="slds-col">
				<div class="slds-form-element">
					<label class="slds-form-element__label" for="related-label-${LIST_COUNT}">
						<abbr class="slds-required" title="required">* </abbr> Label
					</label>
					<div class="slds-form-element__control">
					<input type="text" name="related-label-${LIST_COUNT}" id="related-label-${LIST_COUNT}" class="slds-input" />
					</div>
				</div>
			</div>
			<div class="slds-col">
				<div class="slds-form-element">
					<label class="slds-form-element__label" for="related-action-${LIST_COUNT}">
						<abbr class="slds-required" title="required">* </abbr> Related module
					</label>
					<div class="slds-form-element__control">
					<input type="text" onkeyup="mb.autocomplete(this, 'module')" name="related-module-${LIST_COUNT}" id="autocomplete-module-${LIST_COUNT}" class="slds-input" />
					</div>
					<span id="autocomplete-modulespan-${LIST_COUNT}"></span>
				</div>
			</div>
		</div><br>
		<button class="slds-button slds-button_neutral slds-button_dual-stateful" id="save-btn-for-list-${LIST_COUNT}" onclick="mb.SaveModule(5, false, this.id, ${relatedid})">
			<svg class="slds-button__icon slds-button__icon_small slds-button__icon_left" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
			</svg>${mod_alert_arr.LBL_MB_SAVE}
		</button>
		<button class="slds-button slds-button_destructive slds-button_dual-stateful" id="clear-btn-for-list-${LIST_COUNT}" onclick="mb.clearList(${LIST_COUNT})">
			<svg class="slds-button__icon slds-button__icon_small slds-button__icon_left" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
			</svg>${mod_alert_arr.LBL_MB_CLEAR}
		</button>`;
		mb.loadElement(`related_inputs_${LIST_COUNT}`, true).innerHTML = listTemplate;
		if (relatedid > 0) {
			jQuery.ajax({
				method: 'GET',
				url: url+'&methodName=loadTemplate&recordid='+relatedid,
			}).done(function (response) {
				const state = JSON.parse(response).lists.data.contents[0];
				document.getElementById(`autocomplete-related-${LIST_COUNT}`).value = state.functionname;
				document.getElementById(`related-label-${LIST_COUNT}`).value = state.label;
				document.getElementById(`autocomplete-module-${LIST_COUNT}`).value = state.relatedmodule;
			});
		}
	},
	/**
	 * Create html labels
	 * @param {Label} instance - Current label instance
	 * @param {text} value
	 */
	createLabel: (instance, value) => {
		const label = document.createElement('label');
		label.innerHTML = value;
		return instance.appendChild(label);
	},
	/**
	 * Create html inputs
	 * @param {object} scope = {
		instance: {Input},
		placeholder: {string},
		name: {string},
		id: {string},
		inc: {number},
		attr: {object},
	 }
	 */
	createInput: (scope) => {
		const input = document.createElement('input');
		input.placeholder = scope.placeholder;
		input.id = scope.id+scope.inc;
		input.name = scope.name+scope.inc;
		if (scope.type != '' && scope.type != undefined) {
			input.setAttribute('type', scope.type);
		} else {
			input.className = 'slds-input';
		}
		if (scope.attr != '') {
			for (let f in scope.attr) {
				input.setAttribute(f, scope.attr[f]);
			}
		}
		return scope.instance.appendChild(input);
	},
	/**
	 * Get table instance
	 * @param {string} id
	 */
	getTable: (id) => {
		const table = mb.loadElement(id, true);
		return table;
	},
	/**
	 * Create table row
	 * @param {Row} instance  - Current row instance
	 * @param {number} index
	 * @param {string} id
	 * @param {number} inc
	 */
	createRow: (instance, index, id, inc) => {
		const row = instance.insertRow(index);
		row.id = id + inc;
		return row;
	},
	/**
	 * Create table data
	 * @param {Cell} instance - Current cell instance
	 * @param {number} index
	 * @param {string} id
	 * @param {number} inc
	 */
	createCell: (instance, index, id, inc) => {
		const cell = instance.insertCell(index);
		cell.id = id + inc;
		cell.style = 'padding: 20px';
		return cell;
	},
	/**
	 * Remove block on step 2
	 * @param {string} blockid - Current cell instance
	 */
	deleteBlocks: (blockid) => {
		const id = blockid.split('-')[0];
		jQuery.ajax({
			method: 'POST',
			url: url+'&methodName=deleteBlocks',
			data: 'blockid='+id
		}).done(function (response) {
			const res = JSON.parse(response);
			if (res == true) {
				mb.removeElement('li-block-mb-'+id);
			}
		});
	},
	/**
	 * Remove Field on step 3
	 * @param {string} fieldsid
	 */
	deleteFields: (fieldsid) => {
		if (confirm(alert_arr.ARE_YOU_SURE)) {
			jQuery.ajax({
				method: 'POST',
				url: url+'&methodName=deleteFields',
				data: 'fieldsid='+fieldsid
			}).done(function (response) {
				const res = JSON.parse(response);
				if (res == true) {
					fieldGridInstance.clear();
					fieldGridInstance.reloadData();
				}
			});
		}
	},
	/**
	 * Remove View on step 4
	 * @param {string} viewid
	 */
	deleteFilters: (viewid) => {
		if (confirm(alert_arr.ARE_YOU_SURE)) {
			jQuery.ajax({
				method: 'POST',
				url: url+'&methodName=deleteFilters',
				data: 'viewid='+viewid
			}).done(function (response) {
				const res = JSON.parse(response);
				if (res == true) {
					viewGridInstance.clear();
					viewGridInstance.reloadData();
				}
			});
		}
	},
	/**
	 * Remove Lists on step 5
	 * @param {string} listid
	 */
	deleteRelationships: (list) => {
		if (confirm(alert_arr.ARE_YOU_SURE)) {
			jQuery.ajax({
				method: 'POST',
				url: url+'&methodName=deleteRelationships',
				data: 'listid='+list
			}).done(function (response) {
				const res = JSON.parse(response);
				if (res == true) {
					listGridInstance.clear();
					listGridInstance.reloadData();
				}
			});
		}
	},
	/**
	 * Remove elements
	 * @param {string} elementId
	 * @param {boolean} type
	 */
	removeElement: (elementId, type = false) => {
		var element = mb.loadElement(elementId, true);
		if (type == true) {
			element.innerHTML = '';
		} else {
			if (element) {
				element.parentNode.removeChild(element);
			}
		}
	},
	/**
	 * Get values for inputs
	 * @param {string} id
	 * @param {boolean} type
	 */
	loadElement: (id, type = false) => {
		let value = '';
		if (type == true) {
			value = document.getElementById(id);
		} else {
			value = document.getElementById(id).value;
		}
		return value;
	},

	generateManifest: (modId = 0) => {
		document.getElementById('genModule').style.display = 'none';
		document.getElementById('genModuleProgress').style.display = 'block';
		jQuery.ajax({
			method: 'POST',
			url: url,
			data: 'methodName=loadTemplate&modId='+modId
		}).then(function (response) {
			let res = JSON.parse(response);
			let modObj = {};
			//moduleData
			for (let i in res) {
				modObj.name = res.info.name;
				modObj.label = res.info.label;
				modObj.parent = res.info.parent;
				modObj.icon = res.info.icon;
				modObj.version = '1.0';
				modObj.short_description = res.info.name;
				modObj.dependencies = {
					vtiger_version: '5.4.0',
					vtiger_max_version: '5.*'
				};
				modObj.license = {
					inline: 'Your license here'
				};
				const table = [{
					name: 'vtiger_'+res.info.name.toLowerCase(),
					sql: '-'
				},
				{
					name: 'vtiger_'+res.info.name.toLowerCase()+'cf',
					sql: '-'
				},
				];
				modObj.tables = {
					table
				};
				//blocks and fields
				let blocks = [];
				let defaultRL = [];
				for (let i = 0; i < res.blocks.length; i++) {
					const blocks_label = res.blocks[i].blocks_label;
					const blockObj = {
						block: {
							label: blocks_label,
							fields: {}
						}
					};
					const field = res.fields.data.contents;
					let fields = [];
					for (let j = 0; j < field.length; j++) {
						if (blocks_label == field[j].blockname) {
							field[j].sequence = j;
							fields.push(field[j]);
							if (field[j].uitype == '10') {
								defaultRL.push(field[j]);
							}
						}
					}
					blockObj.block.fields = fields;
					blocks.push(blockObj);
				}
				modObj.blocks = blocks;

				//customviews
				const views = res.views.data.contents;
				let view = [];
				for (let i = 0; i < views.length; i++) {
					const viewObj = {
						viewname: views[i].viewname,
						setdefault: views[i].setdefault,
						setmetrics: false,
						fields: {}
					};
					let fields = [];
					for (let j = 0; j < views[i].fields.length; j++) {
						fields.push(views[i].fields[j]);
					}
					viewObj.fields = fields;
					view.push(viewObj);
				}
				modObj.customviews = view;

				//relatedlists
				const lists = res.lists.data.contents;
				let relatedlists = [];
				let tempIndex = 0;
				for (let i = 0; i < lists.length; i++) {
					const actions = lists[i].actions.split(',');
					const listObj = {
						function: lists[i].functionname,
						label: lists[i].label,
						sequence: i,
						presence: 0,
						actions: actions,
						relatedmodule: lists[i].relatedmodule,
					};
					relatedlists.push(listObj);
					tempIndex++;
				}
				let defaultrelatedlists = [];
				for (let r = 0; r < defaultRL.length; r++) {
					const relatedmodules = defaultRL[r].relatedmodules.split(',');
					if (relatedmodules.length > 0) {
						for (let i = 0; i < relatedmodules.length; i++) {
							if (relatedmodules[i] != '') {
								const listObj = {
									function: 'get_dependents_list',
									label: relatedmodules[i],
									sequence: parseInt(tempIndex) + parseInt(i),
									presence: 0,
									actions: ['ADD'],
									relatedmodule: relatedmodules[i],
								};
								defaultrelatedlists.push(listObj);
							}
						}
					}
				}
				modObj.relatedlists = relatedlists;
				modObj.defaultrelatedlists = defaultrelatedlists;
				modObj.sharingaccess = res.info.sharingaccess;
				modObj.actions = {
					'Merge': res.info.actions.merge,
					'Import': res.info.actions.import,
					'Export': res.info.actions.export,
				};
			}
			const data = {
				"map": modObj
			}
			jQuery.ajax({
				method: 'POST',
				url: url+'&methodName=generateManifest',
				data: data
			}).done(function (response) {
				const res = JSON.parse(response);
				if (res.success == true) {
					window.location.href = 'cache/'+res.module+'.zip';
					const msg = `Module <b>${res.module}</b> is generated successfully!`;
					mb.resetTemplate();
					mb.loadMessage(msg, true, 'success');
				}
			});
		});
	},

	resetTemplate: () => {
		document.getElementById('modulename').value = '';
		document.getElementById('modulelabel').value = '';
		document.getElementById('parentmenu').selected = '';
		document.getElementById('moduleicon').selected = '';
		document.getElementById('merge').checked = false;
		document.getElementById('import').checked = false;
		document.getElementById('export').checked = false;
		document.getElementById('loadBlocks').innerHTML = '';
		document.getElementById('Table').innerHTML = '';
		document.getElementById('loadFields').innerHTML = '';
		document.getElementById('CustomView').innerHTML = '';
		document.getElementById('loadViews').innerHTML = '';
		document.getElementById('RelatedLists').innerHTML = '';
		document.getElementById('loadLists').innerHTML = '';
		document.getElementById('step-1').style.display = 'block';
		document.getElementById('step-2').style.display = 'none';
		document.getElementById('step-3').style.display = 'none';
		document.getElementById('step-4').style.display = 'none';
		document.getElementById('step-5').style.display = 'none';
		document.getElementById('step-6').style.display = 'none';
		document.getElementById('genModule').style.display = 'block';
		document.getElementById('genModuleProgress').style.display = 'none';
		document.getElementById('block-information').classList.remove('slds-is-active');
		document.getElementById('field-information').classList.remove('slds-is-active');
		document.getElementById('filters').classList.remove('slds-is-active');
		document.getElementById('relationship').classList.remove('slds-is-active');
		document.getElementById('modulename').removeAttribute('readonly');
		localStorage.removeItem('ModuleBuilderID');
	},

	loadTemplate: () => {
		jQuery.ajax({
			method: 'POST',
			url: url,
			data: 'methodName=loadTemplate&modId=0'
		}).then(function (response) {
			let res = JSON.parse(response);
			let label;
			//load info block
			const info = mb.loadElement('info', true);
			let infoTemplate = `
			<table class="slds-table slds-table_cell-buffer slds-table_bordered">
				<thead>
					<tr class="slds-line-height_reset">
						<th scope="col">
							<div class="slds-truncate">${mod_alert_arr.name}</div>
						</th>
						<th scope="col">
							<div class="slds-truncate">${mod_alert_arr.label}</div>
						</th>
						<th scope="col">
							<div class="slds-truncate">${mod_alert_arr.icon}</div>
						</th>
						<th scope="col">
							<div class="slds-truncate">${mod_alert_arr.parent}</div>
						</th>
					</tr>
				</thead>
				<tbody>
				<tr class="slds-hint-parent">
					<td>
						<div class="slds-truncate">${res.info.name}</div>
					</td>
					<td>
						<div class="slds-truncate">${res.info.label}</div>
					</td>
					<td>
						<div class="slds-truncate">${res.info.icon}</div>
					</td>
					<td>
						<div class="slds-truncate">${res.info.parent}</div>
					</td>
				</tr>
				</tbody>
			</table>`;
			document.getElementById('info').innerHTML = infoTemplate;
			//load blocks

			let blockTemplate = `
			<table class="slds-table slds-table_cell-buffer slds-table_bordered">
			  <thead>
				<tr class="slds-line-height_reset">
					<thscope="col">
						<div class="slds-truncate">${mod_alert_arr.blockslist}</div>
					</th>
				</tr>
			  </thead>
			  <tbody>`;
			for (let i in res.blocks) {
				blockTemplate += `
				<tr class="slds-hint-parent">
					<td>
						<div class="slds-truncate">${res.blocks[i].blocks_label}</div>
					</td>
				</tr>`;
			}
			blockTemplate += `
			  </tbody>
			</table>`;
			document.getElementById('blocks').innerHTML = blockTemplate;

			//load fields
			let tableTemplate = `
			<table class="slds-table slds-table_cell-buffer slds-table_bordered">
			  <thead>
				<tr class="slds-line-height_reset">
					<th class="" scope="col">
						<div class="slds-truncate">${mod_alert_arr.fieldname}</div>
					</th>
					<th class="" scope="col">
						<div class="slds-truncate">${mod_alert_arr.fieldlabel}</div>
					</th>
					<th class="" scope="col">
						<div class="slds-truncate">${mod_alert_arr.uitype}</div>
					</th>
					<th class="" scope="col">
						<div class="slds-truncate">${mod_alert_arr.relatedmodule}</div>
					</th>
					<th class="" scope="col">
						<div class="slds-truncate">${mod_alert_arr.massedit}</div>
					</th>
				</tr>
			  </thead>
			  <tbody>`;
			for (let i = 0; i < res.fields['data'].contents.length; i++) {
				const masseditable = res.fields['data'].contents[i].masseditable == 0 ? 'On' : 'Off';
				tableTemplate += `
				<tr class="slds-hint-parent">
					<td>
						<div class="slds-truncate">${res.fields['data'].contents[i].fieldname}</div>
					</td>
					<td>
						<div class="slds-truncate">${res.fields['data'].contents[i].fieldlabel}</div>
					</td>
					<td>
						<div class="slds-truncate">${res.fields['data'].contents[i].uitype}</div>
					</td>
					<td>
						<div class="slds-truncate">${res.fields['data'].contents[i].relatedmodules}</div>
					</td>
					<td>
						<div class="slds-truncate">${masseditable}</div>
					</td>
				</tr>`;
			}
			tableTemplate += `
				</tbody>
			</table>`;
			document.getElementById('fields').innerHTML = tableTemplate;
			//load views
			let viewTemplate = `
			<table class="slds-table slds-table_cell-buffer slds-table_bordered">
			  <thead>
				<tr class="slds-line-height_reset">
					<th class="" scope="col">
						<div class="slds-truncate">${mod_alert_arr.filter}</div>
					</th>
					<th class="" scope="col">
						<div class="slds-truncate">${mod_alert_arr.fields}</div>
					</th>
				</tr>
			  </thead>
			  <tbody>`;
			for (let i = 0; i < res.views['data'].contents.length; i++) {
				viewTemplate += `
				<tr class="slds-hint-parent">
					<td>
						<div class="slds-truncate">${res.views['data'].contents[i].viewname}</div>
					</td>
					<td>
						<div class="slds-truncate">${res.views['data'].contents[i].fields}</div>
					</td>
				</tr>`;
			}
			viewTemplate += `
				</tbody>
			</table>`;
			document.getElementById('views').innerHTML = viewTemplate;

			//load views
			let listTemplate = `
			<table class="slds-table slds-table_cell-buffer slds-table_bordered">
			  <thead>
				<tr class="slds-line-height_reset">
					<th class="" scope="col">
						<div class="slds-truncate">${mod_alert_arr.functionname}</div>
					</th>
					<th class="" scope="col">
						<div class="slds-truncate">${mod_alert_arr.fieldlabel}</div>
					</th>
					<th class="" scope="col">
						<div class="slds-truncate">${mod_alert_arr.actions}</div>
					</th>
					<th class="" scope="col">
						<div class="slds-truncate">${mod_alert_arr.relatedmodules}</div>
					</th>
				</tr>
			  </thead>
			  <tbody>`;
			for (let i = 0; i < res.lists['data'].contents.length; i++) {
				listTemplate += `
				<tr class="slds-hint-parent">
					<td>
						<div class="slds-truncate">${res.lists['data'].contents[i].functionname}</div>
					</td>
					<td>
						<div class="slds-truncate">${res.lists['data'].contents[i].label}</div>
					</td>
					<td>
						<div class="slds-truncate">${res.lists['data'].contents[i].actions}</div>
					</td>
					<td>
						<div class="slds-truncate">${res.lists['data'].contents[i].relatedmodule}</div>
					</td>
				</tr>`;
			}
			listTemplate += `
				</tbody>
			</table>`;
			document.getElementById('lists').innerHTML = listTemplate;
		});
	},

	showInformation: (id) => {
		document.getElementById(id).style.display = 'block';
	},

	hideInformation: (id) => {
		document.getElementById(id).style.display = 'none';
	},

	loadModules: (id) => {
		jQuery.ajax({
			method: 'POST',
			url: url,
			data: 'methodName=getModules'
		}).then(function (response) {
			const modules = JSON.parse(response);
			const inStyle = {
				style: `background: white;
				border: 1px solid #d1d1d1;
				position: absolute;
				z-index: 1000;
				height: 200px;
				width: 100%;
				overflow:hidden;
				overflow-y:scroll;`
			};
			let listMods = `<ul class="slds-dropdown__list slds-dropdown__scroll" style="${inStyle.style}">`;
			for (let m in modules) {
				listMods += `
				<li class="slds-dropdown__item">
					<a tabindex="${modules[m]}" id="${modules[m]}" onclick="mb.setModuleValues('${modules[m]}')">
						<span class="slds-truncate">${modules[m]}</span>
					</a>
				</li>`;
			}
			listMods += '</ul>';
			document.getElementById(`${id}`).innerHTML = listMods;
		});
	},

	setModuleValues: (modulename) => {
		let template = `
		<li class="slds-listbox-item" role="presentation" id="${modulename}">
			<span class="slds-pill" role="option" aria-selected="true">
			<span class="slds-pill__label" title="${modulename}">${modulename}</span>
			<span class="slds-icon_container slds-pill__remove" id="remove-${modulename}" onclick="mb.removeField(this)" style="cursor: pointer">
				<svg class="slds-icon slds-icon_x-small slds-icon-text-default" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
				</svg>
			</span>
			</span>
		</li>`;
		const modules = document.getElementById('relatedmodules_1').value;
		const getmodules = document.getElementById('show-pills').innerHTML;
		const pills = `${getmodules}${template}`;
		const newValue = `${modules},${modulename}`;
		document.getElementById('relatedmodules_1').value = newValue;
		document.getElementById('show-pills').innerHTML = pills;
	},

	removeField: (el) => {
		let id = el.id.split('-')[1];
		const pills = document.getElementById(`${id}`);
		let objValues;
		let newValues = '';
		pills.parentNode.removeChild(pills);
		const mods = document.getElementById('relatedmodules_1').value;
		objValues = mods.split(',');
		for (let i in objValues) {
			if (objValues[i] != id && objValues[i] != '') {
				newValues += `${objValues[i]},`;
			}
		}
		document.getElementById('relatedmodules_1').value = newValues;
	},

	editFilters: (id) => {
		mb.generateCustomView(id);
	},

	editRelationships: (id) => {
		mb.generateRelatedList(id);
	},

	editFields: (id) => {
		mb.generateFields(id);
	},

	deleteModule: (moduleid) => {
		const _currentPage = dataGridInstance.getPagination()._currentPage;
		if (confirm(alert_arr.ARE_YOU_SURE)) {
			const data = {
				'moduleid': moduleid
			}
			jQuery.ajax({
				method: 'POST',
				url: url+'&methodName=deleteModule',
				data: data
			}).done(function (response) {
				const res = JSON.parse(response);
				if (res == true) {
					dataGridInstance.destroy();
					mb.openModal(_currentPage);
					if (localStorage.getItem('ModuleBuilderID') == moduleid) {
						mb.resetTemplate();
					}
				}
			});
		}
	},
};

/**
 * Close list fields on click
 */
document.addEventListener('click', function (event) {
	const getSection = document.getElementsByClassName('closeList');
	let getIds = Array.prototype.filter.call(getSection, function (el) {
		return el.nodeName;
	});
	for (let i in getIds) {
		document.getElementById(getIds[i].id).innerHTML = '';
	}
});

class BuilderActionRender {

	constructor(props) {
		let el;
		let id;
		let functionName = '';
		let functionEditName = '';
		let rowKey = props.rowKey;
		const { type } = props.columnInfo.renderer.options;
		if (type == 'Fields') {
			id = props.grid.getValue(rowKey, 'fieldsid');
			functionName = 'deleteFields';
			functionEditName = 'editFields';
		} else if (type == 'CustomView') {
			id = props.grid.getValue(rowKey, 'customviewid');
			functionName = 'deleteFilters';
			functionEditName = 'editFilters';
		} else if (type == 'RelatedLists') {
			id = props.grid.getValue(rowKey, 'relatedlistid');
			functionName = 'deleteRelationships';
			functionEditName = 'editRelationships';
		}
		el = document.createElement('span');
		let actions = `
			<div class="slds-button-group" role="group">
				<button onclick='mb.${functionEditName}(${id})' class="slds-button slds-button_icon slds-button_icon-brand" aria-pressed="false">
				<svg class="slds-button__icon" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
				</svg>
				</button>
				<button onclick='mb.${functionName}(${id})' class="slds-button slds-button_icon slds-button_icon-border-filled" aria-pressed="false">
				<svg class="slds-button__icon" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
				</svg>
				</button>
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