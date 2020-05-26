loadJS('index.php?module=Settings&action=SettingsAjax&file=getjslanguage');
loadJS('modules/Settings/ModuleBuilder/fieldconfigs.js');
const tuiGrid = tui.Grid;
let url = 'index.php?module=Settings&action=SettingsAjax&file=builderUtils';
let dataGridInstance;

const mb = {
    /**
     * Save values for each step
     * @param {number} step 
     * @param {boolean} forward
     * @param {string} buttonid
     */
	SaveModule: (step, forward = true, buttonid = '') => {
		if (step == 1) {
			const modulename = mb.loadValue('modulename');
			const modulelabel = mb.loadValue('modulelabel');
			const parentmenu = mb.loadValue('parentmenu');
			if (modulename == '' || modulelabel == '') {
				document.getElementById('ErrorMessage').style.display = 'block';
				setTimeout(function () {
					document.getElementById('ErrorMessage').style.display = 'none';
				}, 3000);
				return false;
			} else {
				var data = {
					modulename: modulename,
					modulelabel: modulelabel,
					parentmenu: parentmenu,
					step: step
				};
			}
		}
		if (step == 2) {
			var blocks_label = [];
			const number_block = mb.loadValue('number_block');
			for (var i = 1; i <= number_block; i++) {
				blocks_label[i] = mb.loadValue('blocks_label_' + i);
			}
			var data = {
				blocks: blocks_label,
				step: step
			};
		}

		if (step == 3) {
			var fields = [];
			const number_field = mb.loadValue('number_field');
			var btnid = buttonid.split('-')[4];
			if (forward == false) {
				var fieldValues = {};
				var blockid = mb.loadValue('select-for-field-' + btnid);
				var fieldname = mb.loadValue('fieldname_' + btnid);
				const columnname =mb.loadValue('columnname_' + btnid);
				const generatedtype = mb.loadValue('generatedtype_' + btnid);
				const fieldlabel = mb.loadValue('fieldlabel_' + btnid);
				const maximumlength = mb.loadValue('maximumlength_' + btnid);
				const entityidentifier = mb.loadValue('entityidentifier_' + btnid);
				const entityidfield = mb.loadValue('entityidfield_' + btnid);
				const entityidcolumn = mb.loadValue('entityidcolumn_' + btnid);
				const relatedmodules = mb.loadValue('relatedmodules_' + btnid);
				const masseditable = mb.loadValue('Masseditable_' + btnid);
				const displaytype = mb.loadValue('Displaytype_' + btnid);
				const quickcreate = mb.loadValue('Quickcreate_' + btnid);
				const typeofdata = mb.loadValue('Typeofdata_' + btnid);
				const presence = mb.loadValue('Presence_' + btnid);
				const readonly = mb.loadValue('Readonly_' + btnid);
				var uitype = mb.loadValue('Uitype_' + btnid);
				fieldValues = {
					blockid: blockid,
					fieldname: fieldname,
					columnname: columnname,
					generatedtype: generatedtype,
					fieldlabel: fieldlabel,
					maximumlength: maximumlength,
					entityidentifier: entityidentifier,
					entityidfield: entityidfield,
					entityidcolumn: entityidcolumn,
					relatedmodules: relatedmodules,
					masseditable: masseditable,
					displaytype: displaytype,
					quickcreate: quickcreate,
					typeofdata: typeofdata,
					presence: presence,
					readonly: readonly,
					uitype: uitype,
					sequence: number_field,
				};
				fields.push(fieldValues);
				var data = {
					fields: fields,
					step: step
				};
			} else {
				var data = {
					fields: [],
					step: step
				};
			}
		}

		if (step == 4) {
			let customViews = [];
			const number_customview = mb.loadValue('number_customview');
			for (var i = 1; i <= number_customview; i++) {
				var customObj = {
					viewname: mb.loadValue('viewname-'+i),
					setdefault: mb.loadValue('setdefault-'+i),
				};
				const checkSize = document.getElementsByName('checkbox-options-'+i).length;
				var fieldObj = [];
				for (var j = 0; j < checkSize; j++) {
					const checkedValue = document.querySelector('#checkbox-'+j+'-id-'+i);
					if (checkedValue.checked == true) {
						fieldObj.push(checkedValue.value);
					}
				}
				customObj.fields = {
					fieldObj
				};
				customViews.push(customObj);
			}
			var data = {
				customview: customViews,
				step: step
			};
		}

        if (step == 5) {
            let relatedLists = [];
            const number_related = mb.loadValue('number_related');
            for (var i = 1; i <= number_related; i++) {
                let lists = {
                    relatedmodule: mb.loadValue('autocomplete-module-'+i),
                    actions: mb.loadValue('related-action-'+i),
                    name: mb.loadValue('autocomplete-related-'+i),
                    label: mb.loadValue('related-label-'+i),
                };
                relatedLists[i] = lists;
            }
            var data = {
                relatedlists: relatedLists,
                step: step
            };
            console.log(data);
        }

		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=Settings&action=SettingsAjax&file=SaveModuleBuilder',
			data: data
		}).done(function (response) {
			const msg = mod_alert_arr.RecordDeleted;
			mb.loadMessage(msg, true);
			if (forward == false && step == 3) {
				const message = `<p class="slds-section__title" style="float: right">${btnid}: ${mod_alert_arr.Field} &nbsp;<span style="color: blue">${fieldname}</span>&nbsp; ${mod_alert_arr.WasSaved}!</p>`;
				document.getElementById('for-field-' + btnid).innerHTML = '';
				document.getElementById('for-field-inputs-' + btnid).innerHTML = message;
			}
			if (forward == true) {
				document.getElementById('step-' + step).style.display = 'none';
				var nextstep = step + 1;
				var progress = parseInt(nextstep) * 20 - 20;
				document.getElementById('progress').style.width = progress + '%';
				document.getElementById('progresstext').innerHTML = mod_alert_arr.LBL_MB_PROGRESS+': ' + progress + '%';
				document.getElementById('step-' + nextstep).style.display = 'block';
			}
			setTimeout(function () {
				mb.loadMessage('', false);
			}, 3000);
		});
	},
    /**
     * Update progress bar in real time for step 1
     * @param {number} id
     * @param {number} step
     */
	updateProgress: (id, step) => {
		if (step == 1) {
			const data = {
				modulename: mb.loadValue('modulename'),
				modulelabel: mb.loadValue('modulelabel'),
				parentmenu: mb.loadValue('parentmenu'),
				moduleicon: mb.loadValue('moduleicon'),
			};
			var NULL = [];
			for (var i in data) {
				if (data[i] == '') {
					NULL[i] = i;
				}
			}
			var size = Object.keys(NULL).length;
			var progress = (20 - (parseInt(size) * 5));
			document.getElementById('progress').style.width = progress + '%';
			document.getElementById('progresstext').innerHTML = mod_alert_arr.LBL_MB_PROGRESS+': ' + progress + '%';
			if (progress == 20) {
				document.getElementById('btn-step-1').removeAttribute('disabled');
			} else {
				document.getElementById('btn-step-1').setAttribute('disabled', '');
			}
		}
	},
    /**
     * Show module icons in step 1
     * @param {string} iconReference
     */
	showModuleIcon: (iconReference) => {
		let newicon = iconReference.split('-');
		let spn = document.getElementById('moduleiconshow');
		let svg = document.getElementById('moduleiconshowsvg');
		let curicon = svg.getAttribute('xlink:href');
		let category = curicon.substr(24);
		category = category.substr(0, category.indexOf('-'));
		let icon = curicon.substr(curicon.indexOf('#')+1);
		spn.classList.remove('slds-icon-'+category+'-'+icon);
		spn.classList.add('slds-icon-'+newicon[0]+'-'+newicon[1]);
		svg.setAttribute('xlink:href','include/LD/assets/icons/'+newicon[0]+'-sprite/svg/symbols.svg#'+newicon[1]);
	},
    /**
     * Generate block input for step 2
     */
	generateInput: () => {
		const number_block = mb.autoIncrementIds('number_block');
		const input = document.createElement('input');
		input.type = 'text';
		input.id = 'blocks_label_' + number_block;
		input.placeholder = 'LBL_BLOCKNAME_INFORMATION';
		input.className ='slds-input';
		document.getElementById('blocks_inputs').appendChild(input);
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

		for (var i = 0; i < textfields.length; i++) {
            const func = {
                'style': 'width: 15%; margin: 5px'
            };
            const input = mb.createInput(cell, func, textfields[i], textfields[i]+'_', textfields[i]+'_', number_field);
			if (textfields[i]=='fieldname') {
				input.onchange = (elem) => {
					document.getElementById('columnname' + '_' + number_field).value = elem.target.value;
					document.getElementById('fieldlabel' + '_' + number_field).value = elem.target.value;
				}
			}
		}
		for (var i = 0; i < fieldtypes.length; i++) {
			const type = fieldtypes[i].type;
			const values = fieldtypes[i].values;
			const selecttype = document.createElement('select');
			selecttype.id = type + '_' + number_field;
			selecttype.className = 'slds-input';
			selecttype.style = 'width: 15%; margin: 5px';
			cell.appendChild(selecttype);

			const defaultOption = document.createElement('option');
			defaultOption.text = type;
			defaultOption.setAttribute('disabled', '');
			defaultOption.setAttribute('selected', '');
			selecttype.appendChild(defaultOption);
			for (var j in values) {
				const option = document.createElement('option');
				option.value = j;
				option.text = values[j];
				selecttype.appendChild(option);
			}
		}
		//create save button for each field
		var saveBtn = document.createElement('button');
		saveBtn.id ='save-btn-for-field-' + number_field;
		saveBtn.className = 'slds-button slds-button_brand';
		saveBtn.setAttribute('onclick', 'mb.SaveModule(3, false, this.id)');
		saveBtn.innerHTML = mod_alert_arr.LBL_MB_SAVEFIELD;
		cell.appendChild(saveBtn);
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
		document.getElementById('moduleListsModal').style.display = '';
	},
    /**
     * Close modal
     */
	closeModal: () => {
		document.getElementById('moduleListsModal').style.display = 'none';
		document.getElementById('moduleListView').innerHTML = '';
	},
    /**
     * Load all blocks for specific module in step 3
     * @param {object} tableInstance
     * @param {number} number_field
     */
	loadBlocks: (tableInstance, number_field) => {
		jQuery.ajax({
			method: 'GET',
			url: url+'&methodName=loadBlocks',
		}).done(function (response) {
			const res = JSON.parse(response);
			const row = tableInstance.insertRow(0);
			row.setAttribute('id', 'for-field-' + number_field);
			//create select
			const select = document.createElement('select');
			select.id = 'select-for-field-' + number_field;
			select.className = 'slds-input';
			select.style = 'width: 25%; margin: 5px';
			row.appendChild(select);
			//create default option
			const defaultOption = document.createElement('option');
			defaultOption.setAttribute('selected', '');
			defaultOption.setAttribute('disabled', '');
			defaultOption.value = '';
			select.appendChild(defaultOption);
			defaultOption.innerHTML = mod_alert_arr.LBL_CHOOSEFIELDBLOCK + ' ' + number_field;

			for (var i = 0; i < res.length; i++) {
				const options = document.createElement('option');
				options.value = res[i].blocksid;
				options.innerHTML = res[i].blocks_label;
				select.appendChild(options);
			}
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
        const func = {
            'style': 'width: 25%'
        };
        mb.createInput(cell, func, 'Viewname', 'viewname-', 'viewname-', number_customview);
        //create setdefault
        const setdefault = document.createElement('select');
        setdefault.name = 'setdefault-' + number_customview;
        setdefault.id = 'setdefault-' + number_customview;
        setdefault.className = 'slds-input';
        setdefault.setAttribute('style', 'width: 25%');
        for(var val in setdefaultOption[0]) {
            const createOption = document.createElement('option');
            createOption.innerHTML =  setdefaultOption[0][val];
            createOption.value =  val;
            setdefault.appendChild(createOption);
        }
        cell.appendChild(setdefault);

        //get all fields
        const p = document.createElement('p');
        p.innerHTML = 'Choose fields for custom view:';
        cell.appendChild(p);
        jQuery.ajax({
            method: 'GET',
            url: url+'&methodName=loadFields',
        }).done(function(response) {
            const res = JSON.parse(response);
            for(var f in res) {
                const div = document.createElement('div');
                const checkbox = `
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
                `;
                div.innerHTML = checkbox;
                cell.appendChild(div);
            }
        });
    },
    /**
     * Function that load an alert message for success or error
     * @param {text} msg
     * @param {boolean} show
     * @param {text} type [success/error]
     */
	loadMessage: (msg, show = true, type = 'success') => {
		var icon = 'task';
		if (type == 'error') {
			icon = 'first_non_empty';
		}
		const message = `
        <div class="demo-only demo-only_viewport">
          <div class="slds-notification-container">
            <div aria-live="assertive" aria-atomic="true" class="slds-assistive-text">${msg}</div>
            <section class="slds-notification" role="dialog" aria-labelledby="noti1" aria-describedby="dialog-body-id-1">
              <div class="slds-notification__body" id="dialog-body-id-1">
                <a class="slds-notification__target slds-media" href="javascript:void(0);">
                  <span class="slds-icon_container slds-icon-standard-${icon} slds-media__figure" title="${icon}">
                    <svg class="slds-icon slds-icon_small" aria-hidden="true">
                      <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#${icon}"></use>
                    </svg>
                  </span>
                  <div class="slds-media__body">
                    <h2 class="slds-text-heading_small slds-m-bottom_xx-small" id="noti1">
                      ${msg}
                    </h2>
                  </div>
                </a>
              </div>
            </section>
          </div>
        </div>
        `;
		if (show != '') {
			document.getElementById('showMsg').innerHTML = message;
		} else {
			document.getElementById('showMsg').innerHTML = '';
		}
	},
    /**
     * Increment id from each step when generate fields
     * @param {string} id
     */
	autoIncrementIds: (id) => {
		let number = mb.loadValue(id);
		number = parseInt(number) + 1;
		document.getElementById(id).value = number;
		return number;
	},
    /**
     * Get values for inputs
     * @param {string} id
     */
	loadValue: (id) => {
		let value = document.getElementById(id).value;
		return value;
	},
    /**
     * Update grid in every change
     */
	updateData: () => {
		let btn = '';
		for (var i = 0; i < 5; i++) {
			let completed = dataGridInstance.getValue(i, 'completed');
			if (completed == 'Completed') {
				btn = `<button class="slds-button slds-button_brand" aria-live="assertive">
                        <span class="slds-text-not-pressed">
                        <svg class="slds-button__icon slds-button__icon_small slds-button__icon_left" aria-hidden="true">
                            <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#download"></use>
                        </svg>${mod_alert_arr.Export}</span>
                    </button>`;
			} else {
				btn = `<button class="slds-button slds-button_neutral slds-button_dual-stateful" aria-live="assertive">
                        <span class="slds-text-not-pressed">
                        <svg class="slds-button__icon slds-button__icon_small slds-button__icon_left" aria-hidden="true">
                            <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
                        </svg>${mod_alert_arr.StartEditing}</span>
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
		const moduleName = mb.loadValue(id);
		jQuery.ajax({
			method: 'POST',
			url: url,
			data: 'modulename='+moduleName+'&methodName=checkForModule'
		}).done(function (response) {
			if (response == 1) {
				const msg = moduleName+' '+mod_alert_arr.AlreadyExists;
				mb.loadMessage(msg, true, 'error');
			} else {
				mb.loadMessage('', false);
			}
		});
	},
    /**
     * Autocomplete inputs for modules and function names
     * @param {string} el
     * @param {string} type [module/name]
     */
    autocomplete: (el, type) => {
        const forId = el.id.split('-')[2];
        const val = mb.loadValue(el.id);
        let method = 'name';
        if (type == 'module') {
            method = type;
        }
        jQuery.ajax({
            method: 'POST',
            url: url,
            data: 'query='+val+'&methodName=autocomplete&method='+method
        }).done(function(response) {
            document.getElementById('autocomplete-span-'+forId).innerHTML = '';
            document.getElementById('autocomplete-modulespan-'+forId).innerHTML = '';
            let res = JSON.parse(response);
            if (response.length < 3) {
                document.getElementById('autocomplete-span-'+forId).innerHTML = '';
                document.getElementById('autocomplete-modulespan-'+forId).innerHTML = '';
            } else {
                let span = document.createElement('span');
                let ul = `<ul class="slds-dropdown__list" style="background: white; width: 25%; border: 1px solid #d1d1d1; position: absolute; z-index: 1000">`;
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
                    document.getElementById('autocomplete-modulespan-'+forId).appendChild(span);
                } else if (type == 'name') { 
                    document.getElementById('autocomplete-span-'+forId).appendChild(span);
                }
            }
        });
    },
    /**
     * Set values for each input on autocomplete
     * @param {string} name [function name]
     * @param {string} forId
     * @param {string} type [module/name]
     */
    setValueToInput: (name, forId, type) => {
        if (type == 'module') {
            document.getElementById('autocomplete-modulespan-'+forId).innerHTML = '';
            document.getElementById('autocomplete-module-'+forId).value = name;
        } else if (type == 'name') {
            document.getElementById('autocomplete-span-'+forId).innerHTML = '';
            document.getElementById('autocomplete-related-'+forId).value = name;
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

        const span = document.createElement('span');
        span.id = 'autocomplete-span-'+number_related

        var func = {
            'onkeyup': 'mb.autocomplete(this, "name")',
        }
        mb.createLabel(cell, 'Function name');
        mb.createInput(cell, func, 'Function name', 'related-function-', 'autocomplete-related-', number_related);
        cell.appendChild(span);

        const cell2 = mb.createCell(row, 1, 'related_inputs_', number_related);
        mb.createLabel(cell2, 'Label');
        mb.createInput(cell2, '', 'Label', 'related-label-', 'related-label-', number_related);

        const cell3 = mb.createCell(row, 0, 'related_inputs_', number_related);
        mb.createLabel(cell3, 'Actions');
        mb.createInput(cell3, '', 'Actions', 'related-action-', 'related-action-', number_related);

        const cell4 = mb.createCell(row, 0, 'related_inputs_', number_related);
        func = {
            'onkeyup': 'mb.autocomplete(this, "module")',
        }
        mb.createLabel(cell4, 'Related module');
        mb.createInput(cell4, func, 'Related module', 'related-module-', 'autocomplete-module-', number_related);

        const spanModule = document.createElement('span');
        spanModule.id = 'autocomplete-modulespan-'+number_related
        cell4.appendChild(spanModule);
    },
    /**
     * Create html labels
     * @param {object} instance
     * @param {text} value
     */
    createLabel: (instance, value) => {
        const label = document.createElement('label');
        label.innerHTML = value;
        return instance.appendChild(label);
    },
    /**
     * Create html inputs
     * @param {object} instance
     * @param {object} fn
     * @param {string} placeholder
     * @param {string} name
     * @param {string} id
     * @param {number} inc
     */
    createInput: (instance, fn, placeholder, name, id, inc) => {
        const input = document.createElement('input');
        input.className = 'slds-input';
        input.placeholder = placeholder;
        input.id = id+inc;
        input.name = name+inc;
        if (fn != '') {
            for(let f in fn) {
                input.setAttribute(f, fn[f]);
            }
        }
        return instance.appendChild(input);
    },
    /**
     * Get table instance
     * @param {string} id
     */
    getTable: (id) => {
        const table = document.getElementById(id);
        return table;
    },
    /**
     * Create table row
     * @param {object} instance
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
     * @param {object} instance
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
};