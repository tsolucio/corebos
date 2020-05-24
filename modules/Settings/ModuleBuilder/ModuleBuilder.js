loadJS('index.php?module=Settings&action=SettingsAjax&file=getjslanguage');
const imported = document.createElement('script');
imported.src = './modules/Settings/ModuleBuilder/fieldconfigs.js';
document.head.appendChild(imported);
const tuiGrid = tui.Grid;
let url = 'index.php?module=Settings&action=SettingsAjax&file=builderUtils';
let dataGridInstance;

const ModuleBuilder = {
	SaveModule: (step, forward = true, buttonid = '') => {
		if (step == 1) {
			const modulename = ModuleBuilder.loadValue('modulename');
			const modulelabel = ModuleBuilder.loadValue('modulelabel');
			const parentmenu = ModuleBuilder.loadValue('parentmenu');
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
			const number_block = ModuleBuilder.loadValue('number_block');
			for (var i = 1; i <= number_block; i++) {
				blocks_label[i] = ModuleBuilder.loadValue('blocks_label_' + i);
			}
			var data = {
				blocks: blocks_label,
				step: step
			};
		}

		if (step == 3) {
			var fields = [];
			const number_field = ModuleBuilder.loadValue('number_field');
			var btnid = buttonid.split('-')[4];
			if (forward == false) {
				var fieldValues = {};
				var blockid = ModuleBuilder.loadValue('select-for-field-' + btnid);
				var fieldname = ModuleBuilder.loadValue('fieldname_' + btnid);
				const columnname =ModuleBuilder.loadValue('columnname_' + btnid);
				const generatedtype = ModuleBuilder.loadValue('generatedtype_' + btnid);
				const fieldlabel = ModuleBuilder.loadValue('fieldlabel_' + btnid);
				const maximumlength = ModuleBuilder.loadValue('maximumlength_' + btnid);
				const entityidentifier = ModuleBuilder.loadValue('entityidentifier_' + btnid);
				const entityidfield = ModuleBuilder.loadValue('entityidfield_' + btnid);
				const entityidcolumn = ModuleBuilder.loadValue('entityidcolumn_' + btnid);
				const relatedmodules = ModuleBuilder.loadValue('relatedmodules_' + btnid);
				const masseditable = ModuleBuilder.loadValue('Masseditable_' + btnid);
				const displaytype = ModuleBuilder.loadValue('Displaytype_' + btnid);
				const quickcreate = ModuleBuilder.loadValue('Quickcreate_' + btnid);
				const typeofdata = ModuleBuilder.loadValue('Typeofdata_' + btnid);
				const presence = ModuleBuilder.loadValue('Presence_' + btnid);
				const readonly = ModuleBuilder.loadValue('Readonly_' + btnid);
				var uitype = ModuleBuilder.loadValue('Uitype_' + btnid);
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
			const number_customview = ModuleBuilder.loadValue('number_customview');
			for (var i = 1; i <= number_customview; i++) {
				var customObj = {
					viewname: ModuleBuilder.loadValue('viewname-'+i),
					setdefault: ModuleBuilder.loadValue('setdefault-'+i),
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

		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=Settings&action=SettingsAjax&file=SaveModuleBuilder',
			data: data
		}).done(function (response) {
			const msg = mod_alert_arr.RecordDeleted;
			ModuleBuilder.loadMessage(msg, true);
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
				ModuleBuilder.loadMessage('', false);
			}, 3000);
		});
	},
	updateProgress: (id, step) => {
		if (step == 1) {
			const data = {
				modulename: document.getElementById('modulename').value,
				modulelabel: document.getElementById('modulelabel').value,
				parentmenu: document.getElementById('parentmenu').value,
				moduleicon: document.getElementById('moduleicon').value,
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
	generateInput: () => {
		const number_block = ModuleBuilder.autoIncrementIds('number_block');
		const input = document.createElement('input');
		input.type = 'text';
		input.id = 'blocks_label_' + number_block;
		input.placeholder = 'LBL_BLOCKNAME_INFORMATION';
		input.className ='slds-input';
		document.getElementById('blocks_inputs').appendChild(input);
	},
	generateFields: () => {
		const number_field = ModuleBuilder.autoIncrementIds('number_field');
		var table = document.getElementById('Table');
		var row = table.insertRow(0);
		row.style = 'border: 1px solid #e4e4e4;';
		row.id = 'for-field-inputs-' + number_field;
		var cell = row.insertCell(0);
		cell.id = 'fields_inputs_' + number_field;
		cell.style = 'padding: 20px';

		ModuleBuilder.loadBlocks(table, number_field);

		for (var i = 0; i < textfields.length; i++) {
			const input = document.createElement('input');
			input.type = 'text';
			input.id = textfields[i] + '_' + number_field;
			input.placeholder = textfields[i];
			input.className = 'slds-input';
			input.style = 'width: 15%; margin: 5px';
			cell.appendChild(input);
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
		saveBtn.setAttribute('onclick', 'ModuleBuilder.SaveModule(3, false, this.id)');
		saveBtn.innerHTML = mod_alert_arr.LBL_MB_SAVEFIELD;
		cell.appendChild(saveBtn);
	},
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
			scrollY: true,
			columnOptions: {
				resizable: true
			},
			header: {
				align: 'left',
				valign: 'top'
			},
			onGridUpdated: (ev) => {
				ModuleBuilder.updateData();
			}
		});
		tui.Grid.applyTheme('striped');
		document.getElementById('moduleListsModal').style.display = '';
	},
	closeModal: () => {
		document.getElementById('moduleListsModal').style.display = 'none';
		document.getElementById('moduleListView').innerHTML = '';
	},
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
	generateCustomView: () => {
		const number_customview = ModuleBuilder.autoIncrementIds('number_customview');
		var table = document.getElementById('CustomView');
		var row = table.insertRow(0);
		row.style = 'border: 1px solid #e4e4e4;';
		row.id = 'for-customview-' + number_customview;
		var cell = row.insertCell(0);
		cell.id = 'customview_inputs_' + number_customview;
		cell.style = 'padding: 20px';
		//create viewname
		const viewname = document.createElement('input');
		viewname.placeholder = 'Viewname';
		viewname.name = 'viewname-'+number_customview;
		viewname.id = 'viewname-'+number_customview;
		viewname.className = 'slds-input';
		viewname.setAttribute('style', 'width: 25%');
		cell.appendChild(viewname);
		//create setdefault
		const setdefault = document.createElement('select');
		setdefault.name = 'setdefault-' + number_customview;
		setdefault.id = 'setdefault-' + number_customview;
		setdefault.className = 'slds-input';
		setdefault.setAttribute('style', 'width: 25%');
		for (var val in setdefaultOption[0]) {
			const createOption = document.createElement('option');
			createOption.innerHTML =  setdefaultOption[0][val];
			createOption.value =  val;
			setdefault.appendChild(createOption);
		}
		cell.appendChild(setdefault);

		//get all fields
		const p = document.createElement('p');
		p.innerHTML = mod_alert_arr.LBL_CHOOSEFIELDFILTER+':';
		cell.appendChild(p);
		jQuery.ajax({
			method: 'GET',
			url: url+'&methodName=loadFields',
		}).done(function (response) {
			const res = JSON.parse(response);
			for (var f in res) {
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
	autoIncrementIds: (id) => {
		let number = ModuleBuilder.loadValue(id);
		number = parseInt(number) + 1;
		document.getElementById(id).value = number;
		return number;
	},
	loadValue: (id) => {
		let value = document.getElementById(id).value;
		return value;
	},
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
	checkForModule: (id) => {
		const moduleName = ModuleBuilder.loadValue(id);
		jQuery.ajax({
			method: 'POST',
			url: url,
			data: 'modulename='+moduleName+'&methodName=checkForModule'
		}).done(function (response) {
			if (response == 1) {
				const msg = moduleName+' '+mod_alert_arr.AlreadyExists;
				ModuleBuilder.loadMessage(msg, true, 'error');
			} else {
				ModuleBuilder.loadMessage('', false);
			}
		});
	},
};