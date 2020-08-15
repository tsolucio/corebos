loadJS('index.php?module=Settings&action=SettingsAjax&file=getjslanguage');
loadJS('modules/Settings/ModuleBuilder/fieldconfigs.js');
const tuiGrid = tui.Grid;
let url = 'index.php?module=Settings&action=SettingsAjax&file=BuilderFunctions';
let dataGridInstance;
let fieldGridInstance;
let viewGridInstance;
let listGridInstance;

const mb = {
	/**
	 * Save values for each step
	 * @param {number} step
	 * @param {boolean} forward
	 * @param {string} buttonid
	 */
	SaveModule: (step, forward = true, buttonid = '') => {
		var data = {};
		if (step == 1) {
			const modulename = mb.loadElement('modulename');
			const modulelabel = mb.loadElement('modulelabel');
			const parentmenu = mb.loadElement('parentmenu');
			const moduleicon = mb.loadElement('moduleicon');
			data = {
				modulename: modulename,
				modulelabel: modulelabel,
				parentmenu: parentmenu,
				moduleicon: moduleicon,
				step: step
			};
		}
		if (step == 2) {
			var blocks_label = [];
			const number_block = mb.loadElement('number_block');
			for (var i = 1; i <= number_block; i++) {
				blocks_label[i] = mb.loadElement(`blocks_label_${i}`);
			}
			data = {
				blocks: blocks_label,
				step: step
			};
		}

		if (step == 3) {
			var fields = [];
			const number_field = mb.loadElement('number_field');
			var btnid = buttonid.split('-')[4];
			if (forward == false) {
				var fieldValues = {};
				var blockid = document.getElementsByName(`select-for-field-${btnid}`);
				blockid = mb.getRadioValue(`select-for-field-${btnid}`);
				var fieldname = mb.loadElement(`fieldname_${btnid}`);
				const columnname = mb.loadElement(`fieldname_${btnid}`);
				const fieldlabel = mb.loadElement(`fieldlabel_${btnid}`);
				const relatedmodules = mb.loadElement(`relatedmodules_${btnid}`);
				const masseditable = mb.loadElement(`Masseditable_${btnid}`);
				const displaytype = mb.loadElement(`Displaytype_${btnid}`);
				const quickcreate = mb.loadElement(`Quickcreate_${btnid}`);
				const typeofdata = mb.loadElement(`Typeofdata_${btnid}`);
				const presence = mb.loadElement(`Presence_${btnid}`);
				var uitype = mb.loadElement(`Uitype_${btnid}`);
				fieldValues = {
					blockid: blockid,
					fieldname: fieldname,
					columnname: columnname,
					fieldlabel: fieldlabel,
					relatedmodules: relatedmodules,
					masseditable: masseditable,
					displaytype: displaytype,
					quickcreate: quickcreate,
					typeofdata: typeofdata,
					presence: presence,
					uitype: uitype,
					sequence: number_field,
				};
				fields.push(fieldValues);
				data = {
					fields: fields,
					step: step
				};
			} else {
				data = {
					fields: [],
					step: step
				};
			}
		}

		if (step == 4) {
			let customViews = [];
			let field;
			const number_customview = mb.loadElement('number_customview');
			for (var i = 1; i <= number_customview; i++) {
				var customObj = {
					//customviewid: mb.loadElement('customviewid-'+i),
					viewname: mb.loadElement('viewname-'+i),
					setdefault: mb.loadElement('setdefault-'+i),
				};
				const checkSize = document.getElementsByName('checkbox-options-'+i).length;
				var fieldObj = [];
				for (var j = 0; j < checkSize; j++) {
					const checkedValue = document.querySelector('#checkbox-'+j+'-id-'+i);
					if (checkedValue.checked == true) {
						fieldObj.push(checkedValue.value);
					}
				}
				field = fieldObj.join(',');
				customObj.fields = {
					field
				};
				customViews.push(customObj);
			}
			data = {
				customview: customViews,
				step: step
			};
		}

		if (step == 5) {
			let relatedLists = [];
			const number_related = mb.loadElement('number_related');
			for (var i = 1; i <= number_related; i++) {
				let lists = {
					relatedmodule: mb.loadElement('autocomplete-module-'+i),
					actions: mb.loadElement('related-action-'+i),
					name: mb.loadElement('autocomplete-related-'+i),
					label: mb.loadElement('related-label-'+i),
				};
				relatedLists[i] = lists;
			}
			data = {
				relatedlists: relatedLists,
				step: step
			};
		}

		jQuery.ajax({
			method: 'POST',
			url: url+'&methodName=Save',
			data: data
		}).done(function (response) {
			const msg = mod_alert_arr.RecordDeleted;
			if (forward != false && step != 3) {
				mb.loadMessage(msg, true);
			}
			if (forward == false && step == 3) {
				fieldGridInstance.clear();
				fieldGridInstance.reloadData();
				mb.removeElement('for-field-' + btnid, true);
				mb.loadElement('for-field-inputs-' + btnid, true).innerHTML = '';
			}
			if (forward == true) {
				mb.loadElement('step-' + step, true).style.display = 'none';
				var nextstep = step + 1;
				var progress = parseInt(nextstep) * 20 - 20;
				mb.loadElement('progress', true).style.width = progress + '%';
				mb.loadElement('progresstext', true).innerHTML = mod_alert_arr.LBL_MB_PROGRESS+': ' + progress + '%';
				mb.loadElement('step-' + nextstep, true).style.display = 'block';
			}
			if (step == 3) {
				document.getElementById('number_customview').value = 0;
				mb.removeElement('CustomView', true);
				mb.removeElement('loadViews', true);
			}
			if (step == 1) {
				mb.generateDefaultBlocks();
			} else if (step == 2) {
				mb.backTo(3);
			} else if (step == 3 && forward != false) {
				mb.removeElement('loadFields', true);
				mb.backTo(4);
			} else if (step == 4) {
				document.getElementById('number_customview').value = 0;
				document.getElementById('number_related').value = 0;
				mb.removeElement('CustomView', true);
				mb.removeElement('loadViews', true);
				mb.backTo(5);
			} else if (step == 5) {
				document.getElementById('number_related').value = 0;
				mb.removeElement('RelatedLists', true);
				mb.loadTemplate();
			}
		});
	},

	getRadioValue: (name) => {
		var ele = document.getElementsByName(name);
		for (var i = 0; i < ele.length; i++) {
			if (ele[i].checked) {
				return ele[i].value;
			}
		}
		return '';
	},
	/**
	 * Go to back step
	 * @param {number} step
	 * @param {boolean} mod
	 * @param {number} moduleid
	 */
	backTo: (step, mod = false, moduleid = 0) => {
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
					mb.loadElement('step-' + i, true).style.display = 'none';
				}
			}
			mb.loadElement('step-' + step, true).style.display = '';
		} else {
			mb.loadElement('step-' + thisStep, true).style.display = 'none';
			mb.loadElement('step-' + step, true).style.display = '';
		}
		if (step == 1) {
			mb.removeElement('loadFields', true);
			mb.removeElement('loadViews', true);
			mb.removeElement('loadLists', true);
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
							<button onclick='mb.removeBlock("${id}")' class="slds-button slds-button_icon slds-button_icon-border-filled" aria-pressed="false">
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
						header: 'blockname',
					},
					{
						name: 'fieldname',
						header: 'fieldname',
					},
					{
						name: 'fieldlabel',
						header: 'fieldlabel',
					},
					{
						name: 'uitype',
						header: 'uitype',
					},
					{
						name: 'typeofdata',
						header: 'mandatory',
					},
					{
						name: 'action',
						header: 'action',
						renderer: {
							type: ActionRender,
							options: {
								type: 'Fields'
							}
						},
						width: 50
					}
				],
				data: {
					api: {
						readData: {
							url: url+'&methodName=loadValues&step='+step+'&moduleid='+moduleid,
							method: 'GET'
						}
					}
				},
				useClientSort: false,
				pageOptions: false,
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
						header: 'viewname',
					},
					{
						name: 'setdefault',
						header: 'setdefault',
					},
					{
						name: 'fields',
						header: 'fields',
					},
					{
						name: 'action',
						header: 'action',
						renderer: {
							type: ActionRender,
							options: {
								type: 'CustomView'
							}
						},
						width: 50
					}
				],
				data: {
					api: {
						readData: {
							url: url+'&methodName=loadValues&step='+step+'&moduleid='+moduleid,
							method: 'GET'
						}
					}
				},
				useClientSort: false,
				pageOptions: false,
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
						header: 'Related module',
					},
					{
						name: 'actions',
						header: 'Actions',
					},
					{
						name: 'functionname',
						header: 'Function name',
					},
					{
						name: 'label',
						header: 'Label',
					},
					{
						name: 'action',
						header: 'action',
						renderer: {
							type: ActionRender,
							options: {
								type: 'RelatedLists'
							}
						},
						width: 50
					}
				],
				data: {
					api: {
						readData: {
							url: url+'&methodName=loadValues&step='+step+'&moduleid='+moduleid,
							method: 'GET'
						}
					}
				},
				useClientSort: false,
				pageOptions: false,
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
			var NULL = [];
			for (var i in data) {
				if (data[i] == '') {
					NULL[i] = i;
				}
			}
			const size = Object.keys(NULL).length;
			const progress = (20 - (parseInt(size) * 5));
			mb.loadElement('progress', true).style.width = progress + '%';
			mb.loadElement('progresstext', true).innerHTML = mod_alert_arr.LBL_MB_PROGRESS+': ' + progress + '%';
			if (progress == 20) {
				mb.loadElement('btn-step-1', true).removeAttribute('disabled');
			} else {
				mb.loadElement('btn-step-1', true).setAttribute('disabled', '');
			}
		} else {
			const progress = parseInt(step) * 20;
			mb.loadElement('progress', true).style.width = progress + '%';
			mb.loadElement('progresstext', true).innerHTML = mod_alert_arr.LBL_MB_PROGRESS+': ' + progress + '%';
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
		mb.loadElement('number_block').value = '1';
		jQuery.ajax({
			method: 'GET',
			url: url+'&methodName=loadDefaultBlocks',
		}).done(function (response) {
			const res = JSON.parse(response);
			if (res == 'load') {
				mb.generateInput('default');
			} else {
				mb.loadElement('number_block', true).value = '0';
				mb.generateInput();
			}
		});
	},
	/**
	 * Generate block input for step 2
	 */
	generateInput: (type = '') => {
		if (type == 'default') {
			const MODULEBLOCK = document.createElement('input');
			MODULEBLOCK.type = 'text';
			MODULEBLOCK.id = 'blocks_label_1';
			MODULEBLOCK.value = 'LBL_MODULEBLOCK_INFORMATION'; //change this to modulename
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
			mb.loadElement('number_block', true).value = '3';
		} else {
			const number_block = mb.autoIncrementIds('number_block');
			const input = document.createElement('input');
			input.type = 'text';
			input.id = 'blocks_label_' + number_block;
			input.placeholder = 'LBL_BLOCKNAME_INFORMATION';
			input.className ='slds-input';
			mb.loadElement('blocks_inputs', true).appendChild(input);
		}
	},
	/**
	 * Generate field input for step 3
	 */
	generateFields: () => {
		const number_field = mb.autoIncrementIds('number_field');
		const table = mb.getTable('Table');
		const row = mb.createRow(table, 0, 'for-field-inputs-', number_field);
		const cell = mb.createCell(row, 0, 'fields_inputs_', number_field);

		mb.loadBlocks(table, number_field);

		let inStyle = {
			'style': 'margin: 5px',
			'id': '',
			'onchange': '',
		};
		let fieldTemplate = '<div class="slds-grid slds-gutters">';
		for (var i = 0; i < textfields.length; i++) {
			if (textfields[i] == 'relatedmodules') {
				inStyle.style = 'margin: 5px; display: none';
				inStyle.id = `show-field-${number_field}`;
			}
			fieldTemplate += `
			<div class="slds-col" style="${inStyle.style}" id="${inStyle.id}">
				<div class="slds-form-element">
				<label class="slds-form-element__label" for="${textfields[i]}_${number_field}">
					<abbr class="slds-required" title="required">* </abbr> ${textfields[i]}
				</label>
				<div class="slds-form-element__control">
					<input type="text" name="${textfields[i]}_${number_field}" id="${textfields[i]}_${number_field}" class="slds-input" />
				</div>
				</div>
			</div>`;
		}
		fieldTemplate += '</div><div class="slds-grid slds-gutters">';

		for (i = 0; i < fieldtypes.length; i++) {
			const type = fieldtypes[i].type;
			const values = fieldtypes[i].values;
			const selecttype = document.createElement('select');
			if (type == 'Uitype') {
				inStyle.onchange = 'mb.showRelationModule(this, number_field)';
			}
			fieldTemplate += `
			<div class="slds-col">
				<div class="slds-form-element">
					<label class="slds-form-element__label" for="${type}_${number_field}">${type}</label>
					<div class="slds-form-element__control">
						<div class="slds-select_container">
							<select class="slds-select" id="${type}_${number_field}" onchange="${inStyle.onchange}">`;
			for (let j in values) {
				fieldTemplate += `<option value="${j}">${values[j]}</option>`;
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
							<input type="checkbox" name="${checkboxFields[i].type}_${number_field}" id="${checkboxFields[i].type}_${number_field}"/>
							<label class="slds-checkbox__label" for="${checkboxFields[i].type}_${number_field}">
								<span class="slds-checkbox_faux"></span>
								<span class="slds-form-element__label">${checkboxFields[i].value}</span>
							</label>
						</div>
					</div>
				</div><br>
			</div>
			`;
		}
		//create save button for each field
		fieldTemplate += `</div>
			<button class="slds-button slds-button_neutral slds-button_dual-stateful" id="save-btn-for-field-${number_field}" onclick="mb.SaveModule(3, false, this.id)">
				<svg class="slds-button__icon slds-button__icon_small slds-button__icon_left" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
				</svg>${mod_alert_arr.LBL_MB_SAVEFIELD}
			</button>
		`;
		mb.loadElement(`fields_inputs_${number_field}`, true).innerHTML = fieldTemplate;
	},

	showRelationModule: (e, id) => {
		if (e.value == 10) {
			document.getElementById(`show-field-${id.value}`).style.display = '';
		} else {
			document.getElementById(`show-field-${id.value}`).style.display = 'none';
		}
	},
	/**
	 * Open tui grid to list all modules
	 */
	openModal: () => {
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
						method: 'GET'
					}
				}
			},
			useClientSort: false,
			pageOptions: {
				perPage: '5'
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
		tui.Grid.applyTheme('striped');
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
	 * @param {number} number_field
	 */
	loadBlocks: (tableInstance, number_field) => {
		jQuery.ajax({
			method: 'GET',
			url: url+'&methodName=loadBlocks',
		}).done(function (response) {
			const res = JSON.parse(response);
			const row = tableInstance.insertRow(0);
			row.setAttribute('id', `for-field-${number_field}`);
			let template = `
				<fieldset class="slds-form-element">
				<legend class="slds-form-element__legend slds-form-element__label">${mod_alert_arr.LBL_CHOOSEFIELDBLOCK} ${number_field}</legend>
				<div class="slds-form-element__control">
					<div class="slds-radio_button-group">`;
			let checked = '';
			for (var i = 0; i < res.length; i++) {
				if (i === 0) {
					checked = 'checked';
					template += `
					<span class="slds-button slds-radio_button">
						<input type="radio" ${checked} name="select-for-field-${number_field}" id="radio-${res[i].blocksid}${number_field}" value="${res[i].blocksid}" />
						<label class="slds-radio_button__label" for="radio-${res[i].blocksid}${number_field}">
						<span class="slds-radio_faux">${res[i].blocks_label}</span>
						</label>
					</span>`;
				} else {
					template += `
					<span class="slds-button slds-radio_button">
						<input type="radio" name="select-for-field-${number_field}" id="radio-${res[i].blocksid}${number_field}" value="${res[i].blocksid}" />
						<label class="slds-radio_button__label" for="radio-${res[i].blocksid}${number_field}">
						<span class="slds-radio_faux">${res[i].blocks_label}</span>
						</label>
					</span>`;
				}
			}
			template += `
				</div>
			</div>
			</fieldset>`;
			document.getElementById(`for-field-${number_field}`).innerHTML = template;
		});
	},
	/**
	 * Generate inputs for custom views in step 4
	 */
	generateCustomView: () => {
		const number_customview = mb.autoIncrementIds('number_customview');
		const table = mb.getTable('CustomView');
		const row = mb.createRow(table, 0, 'for-customview-', number_customview);
		const cell = mb.createCell(row, 0, 'customview_inputs', number_customview);
		//create viewname
		const inStyle = {
			'style': 'width: 25%'
		};
		let viewTemplate = `
		<div class="slds-grid slds-gutters">
			<div class="slds-col">
				<div class="slds-form-element">
				<label class="slds-form-element__label" for="viewname-${number_customview}">
					<abbr class="slds-required" title="required">* </abbr> Viewname
				</label>
				<div class="slds-form-element__control">
					<input type="text" name="viewname-${number_customview}" id="viewname-${number_customview}" class="slds-input"/>
				</div>
				</div>
			</div>
			<div class="slds-col">
				<div class="slds-form-element">
					<label class="slds-form-element__label" for="setdefault-${number_customview}">Set as default</label>
					<div class="slds-form-element__control">
						<div class="slds-select_container">
							<select class="slds-select" name="setdefault-${number_customview}" id="setdefault-${number_customview}">`;
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
			</div>`;
		jQuery.ajax({
			method: 'GET',
			url: url+'&methodName=loadFields',
		}).done(function (response) {
			let res = JSON.parse(response);
			viewTemplate += '<div class="slds-grid slds-gutters">';
			for (let f in res) {
				viewTemplate += `
				<div class="slds-col">
					<div class="slds-form-element">
					<div class="slds-form-element__control">
						<div class="slds-checkbox">
						<input type="checkbox" class="for-checkbox-${number_customview}" name="checkbox-options-${number_customview}" id="checkbox-${f}-id-${number_customview}" value="${res[f]['fieldsid']}"/>
						<label class="slds-checkbox__label" for="checkbox-${f}-id-${number_customview}">
							<span class="slds-checkbox_faux"></span>
							<span class="slds-form-element__label">${res[f]['fieldname']}</span>
						</label>
						</div>
					</div>
					</div>
				</div>`;
				mb.loadElement(`customview_inputs${number_customview}`, true).innerHTML = viewTemplate;
			}
			viewTemplate += '</div>';
		});
		mb.loadElement(`customview_inputs${number_customview}`, true).innerHTML = viewTemplate;
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
		for (var i = 0; i < 5; i++) {
			let completed = dataGridInstance.getValue(i, 'completed');
			let moduleid = dataGridInstance.getValue(i, 'moduleid');
			if (completed == 'Completed') {
				btn = `
				<button class="slds-button slds-button_brand" aria-live="assertive">
					<span class="slds-text-not-pressed">
						<svg class="slds-button__icon slds-button__icon_small slds-button__icon_left" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#download"></use>
						</svg>${mod_alert_arr.Export}
					</span>
				</button>
				<button class="slds-button slds-button_neutral slds-button_dual-stateful" onclick="mb.backTo(5, true, ${moduleid}); mb.closeModal()" aria-live="assertive">
					<span class="slds-text-not-pressed">
						<svg class="slds-button__icon slds-button__icon_small slds-button__icon_left" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
						</svg>${mod_alert_arr.StartEditing}
					</span>
				</button>`;
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
				<button class="slds-button slds-button_neutral slds-button_dual-stateful" onclick="mb.backTo(${step}, true, ${moduleid}); mb.closeModal()" aria-live="assertive">
					<span class="slds-text-not-pressed">
						<svg class="slds-button__icon slds-button__icon_small slds-button__icon_left" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
						</svg>${mod_alert_arr.StartEditing}
					</span>
				</button>`;
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
			mb.removeElement('autocomplete-span-'+forId, true);
			mb.removeElement('autocomplete-modulespan-'+forId, true);
			let res = JSON.parse(response);
			if (response.length < 3) {
				mb.removeElement('autocomplete-span-'+forId, true);
				mb.removeElement('autocomplete-modulespan-'+forId, true);
			} else {
				const inStyle = {
					style: `background: white;
					border: 1px solid #d1d1d1;
					position: absolute;
					z-index: 1000`
				};
				let span = document.createElement('span');
				let ul = `<ul class="slds-dropdown__list" style="${inStyle.style}">`;
				for (let i = 0; i < res.length; i++) {
					ul += `<li class="slds-dropdown__item">
							<a onclick="mb.setValueToInput(this.id, ${forId}, '${method}')" tabindex="${i}" id="${res[i].name}">
								<span class="slds-truncate" title="${res[i].name}">${res[i].name}</span>
							</a>
						</li>`;
				}
				ul += '</ul>';
				span.innerHTML = ul;
				if (type == 'module') {
					mb.loadElement('autocomplete-modulespan-'+forId, true).appendChild(span);
				} else if (type == 'name') {
					mb.loadElement('autocomplete-span-'+forId, true).appendChild(span);
				}
			}
		});
	},
	loadTemplate: () => {
		jQuery.ajax({
			method: 'POST',
			url: url,
			data: 'methodName=loadTemplate'
		}).then(function (response) {
			let res = JSON.parse(response);
			let label;
			//load info block
			const info = mb.loadElement('info', true);
			const infoList = document.createElement('ol');
			info.appendChild(infoList);
			for (let i in res.info) {
				const elList = document.createElement('li');
				if (i == 'name') {
					label = mod_alert_arr.name;
				} else if (i == 'parent') {
					label = mod_alert_arr.parent;
				} else if (i == 'icon') {
					label = mod_alert_arr.icon;
				} else if (i == 'label') {
					label = mod_alert_arr.label;
				}
				elList.innerHTML = `
				<div class="slds-tree__item">
					<span class="slds-has-flexi-truncate">
						<span class="slds-tree__item-label slds-truncate" title="${res.info[i]}">
							<strong>${label}:</strong> ${res.info[i]}
						</span>
					</span>
				</div>`;
				infoList.appendChild(elList);
			}
			//load blocks
			const blocks = mb.loadElement('blocks', true);
			const blockList = document.createElement('ol');
			blocks.appendChild(blockList);
			for (let i in res.blocks) {
				const elList = document.createElement('li');
				const index = parseInt(i) + 1;
				elList.innerHTML = `
					<div class="slds-tree__item">
						<span class="slds-has-flexi-truncate">
							<span class="slds-tree__item-label slds-truncate" title="Blockname: ${res.blocks[i].blocks_label}">
								${index}. ${res.blocks[i].blocks_label}
							</span>
						</span>
					</div>`;
				blockList.appendChild(elList);
			}
			//load views
			const views = mb.loadElement('views', true);
			const viewList = document.createElement('ul');
			viewList.className = 'slds-tree';
			views.appendChild(viewList);
			console.log(res.views['data'].contents.length);
			for (let i = 0; i < res.views['data'].contents.length; i++) {
				const elList = document.createElement('li');
				let tree = `
				<div class="slds-tree__item">
					<button class="slds-button slds-button_icon slds-m-right_x-small" aria-hidden="true" tabindex="-1" title="Expand Tree Branch">
						<svg class="slds-button__icon slds-button__icon_small" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevronright"></use>
						</svg>
					</button>
					<span class="slds-has-flexi-truncate">
						<span class="slds-tree__item-label slds-truncate">
						${res.views['data'].contents[i].viewname}
					</span>
					</span>
				</div>
				<ul role="group">`;
				for (let j in res.views['data'].contents[i].fields) {
					const fields = res.views['data'].contents[i].fields;
					tree += `
					<li aria-level="2" role="treeitem">
						<div class="slds-tree__item">
							<button class="slds-button slds-button_icon slds-m-right_x-small slds-is-disabled" aria-hidden="true" tabindex="-1" title="Expand Tree Item">
								<svg class="slds-button__icon slds-button__icon_small" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevronright"></use>
								</svg>
							</button>
							<span class="slds-has-flexi-truncate">
								<span class="slds-tree__item-label slds-truncate" title="Fieldname: ${fields[j]}">
									${fields[j]}
								</span>
							</span>
						</div>
					</li>`;
				}
				tree += '</ul>';
				elList.innerHTML = tree;
				viewList.appendChild(elList);
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
		} else if (type == 'name') {
			mb.removeElement('autocomplete-span-'+forId, true);
			mb.loadElement('autocomplete-related-'+forId, true).value = name;
		}
	},
	/**
	 * Generate related lists for step 5
	 */
	generateRelatedList: () => {
		const number_related = mb.autoIncrementIds('number_related');
		const table = mb.getTable('RelatedLists');
		const row = mb.createRow(table, 0, 'for-related-', number_related);
		const cell = mb.createCell(row, 0, 'related_inputs_', number_related);

		let listTemplate = `
		<div class="slds-grid slds-gutters">
			<div class="slds-col">
				<div class="slds-form-element">
					<label class="slds-form-element__label" for="autocomplete-related-${number_related}">
						<abbr class="slds-required" title="required">* </abbr> Function name
					</label>
					<div class="slds-form-element__control">
					<input type="text" onkeyup="mb.autocomplete(this, 'name')" name="related-function-${number_related}" id="autocomplete-related-${number_related}" class="slds-input" />
						<span id="autocomplete-span-${number_related}"></span>
					</div>
				</div>
			</div>
			<div class="slds-col">
				<div class="slds-form-element">
					<label class="slds-form-element__label" for="related-label-${number_related}">
						<abbr class="slds-required" title="required">* </abbr> Label
					</label>
					<div class="slds-form-element__control">
					<input type="text" name="related-label-${number_related}" id="related-label-${number_related}" class="slds-input" />
					</div>
				</div>
			</div>
			<div class="slds-col">
				<div class="slds-form-element">
					<label class="slds-form-element__label" for="related-action-${number_related}">
						<abbr class="slds-required" title="required">* </abbr> Actions
					</label>
					<div class="slds-form-element__control">
					<input type="text" name="related-action-${number_related}" id="related-action-${number_related}" class="slds-input" />
					</div>
				</div>
			</div>
			<div class="slds-col">
				<div class="slds-form-element">
					<label class="slds-form-element__label" for="related-action-${number_related}">
						<abbr class="slds-required" title="required">* </abbr> Related module
					</label>
					<div class="slds-form-element__control">
					<input type="text" onkeyup="mb.autocomplete(this, 'module')" name="related-module-${number_related}" id="autocomplete-module-${number_related}" class="slds-input" />
					</div>
					<span id="autocomplete-modulespan-${number_related}"></span>
				</div>
			</div>
		</div>`;
		mb.loadElement(`related_inputs_${number_related}`, true).innerHTML = listTemplate;
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
	removeBlock: (blockid) => {
		const id = blockid.split('-')[0];
		jQuery.ajax({
			method: 'POST',
			url: url+'&methodName=removeBlock',
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
	removeField: (fieldsid) => {
		jQuery.ajax({
			method: 'POST',
			url: url+'&methodName=removeField',
			data: 'fieldsid='+fieldsid
		}).done(function (response) {
			const res = JSON.parse(response);
			if (res == true) {
				fieldGridInstance.clear();
				fieldGridInstance.reloadData();
			}
		});
	},
	/**
	 * Remove View on step 4
	 * @param {string} viewid
	 */
	removeCustomView: (viewid) => {
		jQuery.ajax({
			method: 'POST',
			url: url+'&methodName=removeCustomView',
			data: 'viewid='+viewid
		}).done(function (response) {
			const res = JSON.parse(response);
			if (res == true) {
				viewGridInstance.clear();
				viewGridInstance.reloadData();
			}
		});
	},
	/**
	 * Remove Lists on step 5
	 * @param {string} listid
	 */
	removeRelatedLists: (list) => {
		jQuery.ajax({
			method: 'POST',
			url: url+'&methodName=removeRelatedLists',
			data: 'listid='+list
		}).done(function (response) {
			const res = JSON.parse(response);
			if (res == true) {
				listGridInstance.clear();
				listGridInstance.reloadData();
			}
		});
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
			element.parentNode.removeChild(element);
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

	generateManifest: () => {
		jQuery.ajax({
			method: 'POST',
			url: url+'&methodName=generateManifest',
		}).done(function (response) {

		});
	},
};

class ActionRender {

	constructor(props) {
		let el;
		let id;
		let functionName = '';
		let rowKey = props.rowKey;
		const { type } = props.columnInfo.renderer.options;
		if (type == 'Fields') {
			id = props.grid.getValue(rowKey, 'fieldsid');
			functionName = 'removeField';
		} else if (type == 'CustomView') {
			id = props.grid.getValue(rowKey, 'customviewid');
			functionName = 'removeCustomView';
		} else if (type == 'RelatedLists') {
			id = props.grid.getValue(rowKey, 'relatedlistid');
			functionName = 'removeRelatedLists';
		}
		el = document.createElement('span');
		let actions = `
			<div class="slds-button-group" role="group">
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