class MasterGrid {

	constructor() {
		this.id = 0;
		this.idx = 0;
		this.module = null;
		this.relatedfield = '';
		this.mapname = '';
		this.fields = [];
		this.data = [];
		this.currentRow = {};
		this.url = 'index.php?module=Utilities&action=UtilitiesAjax&file=MasterGridAPI';
	}

	Init() {
		let data = JSON.parse(this.data);
		for (let i in data) {
			this.currentRow = data[i];
			this.EmptyRow();
			this.currentRow = [];
		}
	}

	EmptyRow(add = false) {
		if (this.id != this.currentRow['__mastergridid'] && !add) {
			return;
		}
		const flds = JSON.parse(this.fields);
		let row = document.createElement('tr');
		row.id = `grid-id-${this.id}-${this.idx}`;
		for (let i in flds) {
			let data = document.createElement('td');
			data.innerHTML = this.GenerateField(flds[i]);
			row.appendChild(data);
		}
		let actions = document.createElement('td');
		actions.innerHTML = this.GenerateActions();
		row.appendChild(actions);
		let inputid = document.createElement('input');
		inputid.type = 'hidden';
		inputid.value = this.currentRow['id'] != undefined ? this.currentRow['id'] : 0;
		inputid.name = 'mastergrid-rowid';
		row.appendChild(inputid);
		document.getElementById(`mastergrid-${this.id}`).appendChild(row);
		this.idx++;
	}

	async TableData(instance) {
		let data = new Array();
		let fields = JSON.parse(mg[instance].fields);
		let isValid = new Array();
		for (let j = 0; j < mg[instance].idx; j++) {
			let cRow = document.getElementById(`grid-id-${instance}-${j}`);
			if (cRow != null) {
				let rowdata = {};
				for (let i in fields) {
					const type = cRow.querySelector(`[name=${fields[i].name}]`).type;
					if (type == 'checkbox') {
						rowdata[fields[i].name] = cRow.querySelector(`[name=${fields[i].name}]`).checked;
					} else {
						const val = cRow.querySelector(`[name=${fields[i].name}]`).value;
						if (cRow.querySelector(`[name=${fields[i].name}]`).hasAttribute('required')) {
							if (val == '' || val == null) {
								isValid.push(true);
							}
						}
						rowdata[fields[i].name] = val;
					}
				}
				rowdata['id'] = cRow.querySelector('[name=mastergrid-rowid]').value;
				rowdata['__mastergridid'] = this.id;
				data.push(rowdata);
			}
		}
		MasterGridData[instance] = data;
		return isValid.length == 0 ? true : false;
	}

	GenerateField(field) {
		let fld = '';
		let editable = '';
		let mandatory = '';
		let fieldvalue = '';
		let fieldvalueDisplay = '';
		if (!field.editable) {
			editable = 'readonly';
		}
		if (field.mandatory) {
			mandatory = 'required';
		}
		if (this.currentRow[field.name] !== undefined) {
			fieldvalue = this.currentRow[field.name];
			if (this.currentRow[`${field.name}_displayValue`] !== null) {
				fieldvalueDisplay = this.currentRow[`${field.name}_displayValue`];
			}
		}
		let checked = 'checked';
		let url = '';
		switch (field.uitype) {
		case '1':
		case '2':
			fld += `<input type="text" ${mandatory} value="${fieldvalue}" name="${field.name}" data-grid-name="${field.name}" class="slds slds-input" ${editable}>`;
			break;
		case '5'://date
			fld += `<input type="date" ${mandatory} value="${fieldvalue}" name="${field.name}" data-grid-name="${field.name}" class="slds slds-input" ${editable}>`;
			break;
		case '10':
			url = `index.php?module=${field.searchin}&action=Popup&html=Popup_picker&form=MasterGrid&forfield=${field.name}&srcmodule=${this.module}&forrecord=${this.currentRow.id}&index=${this.idx}&instance=${this.id}`;
			fld += `
			<input ${mandatory} id="${field.name}_mastergrid_${this.idx}_${this.id}" name="${field.name}" type="hidden" value="${fieldvalue}">
			<span style="display:none;" id="${field.name}_hidden"></span>
			<div class="slds-grid">
				<div class="slds-col slds-size_8-of-10" style="width: 90%">
					<input class="slds-input" value="${fieldvalueDisplay}" id="${field.name}_display_${this.idx}_${this.id}" name="${field.name}_display" readonly="" type="text" style="border:1px solid #c9c9c9"onclick="return window.open('${url}', 'vtlibui10', cbPopupWindowSettings);">
				</div>
				<div class="slds-col slds-size_2-of-10">
					<div class="slds-grid slds-grid_vertical slds-align_absolute-center">
						<button class="slds-button slds-button_icon" title="Select" type="button" onclick="return window.open('${url}', 'vtlibui10', cbPopupWindowSettings);">
							<svg class="slds-button__icon" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#choice"></use>
							</svg>
							<span class="slds-assistive-text">Select</span>
						</button>
						<button class="slds-button slds-button_icon" type="button" onclick="mg[${this.id}].ClearValues('${field.name}', ${this.idx}, ${this.id});">
							<svg class="slds-button__icon" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
							</svg>
							<span class="slds-assistive-text">Clear</span>
						</button>
					</div>
				</div>
			</div>`;
			break;
		case '14'://time
			fld += `<input type="time" ${mandatory} value="${fieldvalue}" name="${field.name}" data-grid-name="${field.name}" class="slds slds-input" ${editable}>`;
			break;
		case '15':
		case '16':
			if (editable != '') {
				editable = 'disabled';
			}
			fld += `<select class="slds slds-select" ${mandatory} name="${field.name}" data-grid-name=${field.name} ${editable}>`;
			for (let i in field.type.picklistValues) {
				let selected = '';
				if (fieldvalue == field.type.picklistValues[i].value) {
					selected = 'selected';
				}
				fld += `<option ${selected} value="${field.type.picklistValues[i].value}">${field.type.picklistValues[i].label}</option>`;
			}
			fld += '</select>';
			break;
		case '50'://datetime
			fld += `<input type="datetime-local" ${mandatory} value="${fieldvalue}" name="${field.name}" data-grid-name="${field.name}" class="slds slds-input" ${editable}>`;
			break;
		case '53':
			fld += `<select class="slds slds-select" name="${field.name}" data-grid-name=${field.name} ${editable}>`;
			for (let i in field.type.assignto.users.options) {
				let selected = '';
				if (fieldvalue == field.type.assignto.users.options[i].userid.split('x')[1]) {
					selected = 'selected';
				}
				fld += `<option ${selected} value="${field.type.assignto.users.options[i].userid.split('x')[1]}">${field.type.assignto.users.options[i].username}</option>`;
			}
			fld += '</select>';
			break;
		case '56':
			if (fieldvalue == '0') {
				checked = '';
			}
			fld += `<input type="checkbox" value="${fieldvalue}" ${checked} name="${field.name}" data-grid-name="${field.name}" ${editable}>`;
			break;
		default:
			fld += `<input value="${fieldvalue}" ${mandatory} type="text" name="${field.name}" class="slds slds-input" data-grid-name="${field.name}" ${editable}>`;
		}
		return fld;
	}

	ClearValues(column, idx, id) {
		document.getElementById(`${column}_mastergrid_${idx}_${id}`).value = '';
		document.getElementById(`${column}_display_${idx}_${id}`).value = '';
	}

	DeleteRow(idx) {
		const el = document.getElementById(`grid-id-${this.id}-${idx}`);
		el.remove();
		const rowid = el.querySelector('[name=mastergrid-rowid]').value;
		if (rowid > 0) {
			Request(this.url, 'post', {
				'rowid': rowid,
				'module': this.module,
				'method': 'deleteRow',
			});
		}
	}

	DuplicateRow(idx) {
		let el = document.getElementById(`grid-id-${this.id}-${idx}`);
		let grid = document.getElementById(`mastergrid-${this.id}`);
		let newrow = el.cloneNode(true);
		newrow.deleteCell(-1);
		newrow.id = `grid-id-${this.id}-${this.idx}`;
		newrow.querySelector('[name=mastergrid-rowid]').value = 0;
		let actions = document.createElement('td');
		actions.innerHTML = this.GenerateActions();
		newrow.appendChild(actions);
		grid.appendChild(newrow);
		this.idx++;
	}

	async Save() {
		MasterGridData = [];
		let modules = [];
		let relatedfield = [];
		let isValid = await this.TableData(this.id);
		if (isValid) {
			modules[this.id] = {
				module: mg[this.id].module,
			};
			relatedfield[this.id] = {
				relatedfield: mg[this.id].relatedfield,
			};
			let newdata = await Request(this.url, 'post', {
				'MasterGridValues': JSON.stringify(Object.entries(MasterGridData)),
				'MasterGridModule': JSON.stringify(Object.entries(modules)),
				'MasterGridRelatedField': JSON.stringify(Object.entries(relatedfield)),
				'module': this.module,
				'method': 'save',
				'id': document.getElementById('record').value,
				'currentModule': document.getElementById('module').value,
				'mapname': this.mapname,
				'__mastergridid': this.id
			});
			this.data = JSON.stringify(JSON.parse(newdata)[0]);
			document.getElementById(`mastergrid-${this.id}`).innerHTML = '';
			this.Init();
			ldsNotification.show(alert_arr.LBL_SUCCESS, alert_arr.LBL_CREATED_SUCCESS, 'success');
		} else {
			ldsNotification.show(alert_arr.ERROR, alert_arr.LBL_REQUIRED_FIELDS);
		}
	}

	GenerateActions() {
		let actions = `
		<div class="slds-button-group" role="group">
			<div class="slds-dropdown-trigger slds-dropdown-trigger_hover slds-is-open slds-button_last">
				<button type="button" class="slds-button slds-button_icon slds-button_icon-border-filled" aria-haspopup="true" aria-expanded="true" title="Show More">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#threedots"></use>
					</svg>
					<span class="slds-assistive-text"></span>
				</button>
				<div class="slds-dropdown slds-dropdown_right slds-dropdown_actions">
				<ul class="slds-dropdown__list" role="menu">
					<li class="slds-dropdown__item" role="presentation">
						<a role="menuitem" tabindex="-1" onclick="mg[${this.id}].DuplicateRow(${this.idx})">
							<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#layers"></use>
							</svg>
							<span class="slds-truncate">${alert_arr.LNK_DUPLICATE}</span>
						</a>
					</li>
					<li class="slds-dropdown__item" role="presentation">
						<a role="menuitem" tabindex="-1" onclick="mg[${this.id}].DeleteRow(${this.idx})">
							<svg class="slds-button__icon slds-button__icon_left cbds-color-compl-red--sober" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
							</svg>
							<span class="slds-truncate cbds-color-compl-red--sober">${alert_arr.JSLBL_Delete}</span>
						</a>
					</li>
				</ul>
			</div>
		</div>`;
		return actions;
	}
}

if (mg === undefined) {
	var mg = Array();
}