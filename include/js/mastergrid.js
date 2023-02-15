class MasterGrid {

	constructor() {
		this.id = 0;
		this.idx = 0;
		this.module = null;
		this.relatedfield = '';
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

	EmptyRow() {
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

	TableData(instance) {
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
				rowdata['id'] = cRow.querySelector(`[name=mastergrid-rowid]`).value;
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
		switch(field.uitype) {
			case '1':
			case '2':
				fld += `
					<input type="text" ${mandatory} value="${fieldvalue}" name="${field.name}" data-grid-name="${field.name}" class="slds slds-input" ${editable}>
				`;
				break;
			case '5'://date
				fld += `
					<input type="date" ${mandatory} value="${fieldvalue}" name="${field.name}" data-grid-name="${field.name}" class="slds slds-input" ${editable}>
				`;
				break;
			case '10':
				let url = `index.php?module=${field.searchin}&action=Popup&html=Popup_picker&form=vtlibPopupView&forfield=${field.name}&srcmodule=${this.module}&forrecord=${this.currentRow.id}&index=${this.idx}`;
				fld += `
				<input ${mandatory} id="${field.name}_mastergrid_${this.idx}" name="${field.name}" type="hidden" value="${fieldvalue}">
				<span style="display:none;" id="${field.name}_hidden"></span>
				<div class="slds-grid slds-wrap">
					<div class="slds-col slds-size_8-of-10" style="margin-right: -25px">
						<input class="slds-input" value="${fieldvalueDisplay}" id="${field.name}_display_${this.idx}" name="${field.name}_display" readonly="" type="text" style="width: 85%;border:1px solid #c9c9c9"onclick="return window.open('${url}', 'vtlibui10', cbPopupWindowSettings);">
					</div>
					<div class="slds-col slds-size_2-of-10">
						<div class="slds-grid slds-grid_vertical slds-align_absolute-center">
							<button class="slds-button slds-button_icon" title="Select" type="button" onclick="return window.open('${url}', 'vtlibui10', cbPopupWindowSettings);">
								<svg class="slds-button__icon" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#choice"></use>
								</svg>
								<span class="slds-assistive-text">Select</span>
							</button>
							<button class="slds-button slds-button_icon" type="button" onclick="mg[${this.id}].ClearValues('${field.name}');">
								<svg class="slds-button__icon" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
								</svg>
								<span class="slds-assistive-text">Clear</span>
							</button>
						</div>
					</div>
				</div>
				`;
				break;
			case '14'://time
				fld += `
					<input type="time" ${mandatory} value="${fieldvalue}" name="${field.name}" data-grid-name="${field.name}" class="slds slds-input" ${editable}>
				`;
				break;
			case '15':
			case '16':
				if (editable != '') {
					editable = 'disabled';
				}
				fld += `
					<select class="slds slds-select" ${mandatory} name="${field.name}" data-grid-name=${field.name} ${editable}>
				`;
				for (let i in field.type.picklistValues) {
					let selected = '';
					if (fieldvalue == field.type.picklistValues[i].value) {
						selected = 'selected';
					}
					fld += `<option ${selected} value="${field.type.picklistValues[i].value}">${field.type.picklistValues[i].label}</option>`;
				}
				fld += `</select>`;
				break;
			case '50'://datetime
				fld += `
					<input type="datetime-local" ${mandatory} value="${fieldvalue}" name="${field.name}" data-grid-name="${field.name}" class="slds slds-input" ${editable}>
				`;
				break;
			case '53':
				fld += `
					<select class="slds slds-select" name="${field.name}" data-grid-name=${field.name} ${editable}>
				`;
				for (let i in field.type.assignto.users.options) {
					let selected = '';
					if (fieldvalue == field.type.assignto.users.options[i].userid.split('x')[1]) {
						selected = 'selected';
					}
					fld += `<option ${selected} value="${field.type.assignto.users.options[i].userid.split('x')[1]}">${field.type.assignto.users.options[i].username}</option>`;
				}
				fld += `</select>`;
				break;
			case '56':
				let checked = 'checked';
				if (fieldvalue == '0') {
					checked = '';
				}
				fld += `
					<input type="checkbox" value="${fieldvalue}" ${checked} name="${field.name}" data-grid-name="${field.name}" ${editable}>
				`;
				break;
			default:
				fld += `
					<input value="${fieldvalue}" ${mandatory} type="text" name="${field.name}" class="slds slds-input" data-grid-name="${field.name}" ${editable}>
				`;
		}
		return fld;
	}

	ClearValues(column) {
		document.getElementById(`${column}_mastergrid`).value = '';
		document.getElementById(`${column}_display`).value = '';
	}

	DeleteRow(idx) {
		const el = document.getElementById(`grid-id-${this.id}-${idx}`);
		el.remove();
		const rowid = el.querySelector(`[name=mastergrid-rowid]`).value;
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
		newrow.querySelector(`[name=mastergrid-rowid]`).value = 0;
		let actions = document.createElement('td');
		actions.innerHTML = this.GenerateActions();
		newrow.appendChild(actions);
		grid.appendChild(newrow);
		this.idx++;
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