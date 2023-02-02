class MasterGrid {

	constructor() {
		this.id = 0;
		this.idx = 0;
		this.module = null;
		this.relatedfield = '';
		this.fields = [];
		this.data = [];
		this.currentRow = {};
	}

	Init() {
		let data = JSON.parse(this.data);
		for (let i in data) {
			this.currentRow = data[i];
			this.EmptyRow();
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
		for (let j = 0; j < mg[instance].idx; j++) {
			let cRow = document.getElementById(`grid-id-${instance}-${j}`);
			if (cRow != null) {
				let rowdata = {};
				for (let i in fields) {
					rowdata[fields[i].name] = cRow.querySelector(`[name=${fields[i].name}]`).value;
				}
				rowdata['id'] = cRow.querySelector(`[name=mastergrid-rowid]`).value;
				data.push(rowdata);
			}
		}
		MasterGridData[instance] = data;
	}

	GenerateField(field) {
		let fld = '';
		let editable = '';
		let fieldvalue = '';
		if (!field.editable) {
			editable = 'readonly';
		}
		if (this.currentRow[field.name] !== undefined) {
			fieldvalue = this.currentRow[field.name];
		}
		switch(field.uitype) {
			case '1':
			case '2':
				fld += `
					<input type="text" value="${fieldvalue}" name="${field.name}" data-grid-name="${field.name}" class="slds slds-input" ${editable}>
				`;
				break;
			case '5'://date
				fld += `
					<input type="date" value="${fieldvalue}" name="${field.name}" data-grid-name="${field.name}" class="slds slds-input" ${editable}>
				`;
				break;
			case '10':
				fld += `
					<input type="text" value="${fieldvalue}" name="${field.name}" data-grid-name="${field.name}" class="slds slds-input" ${editable}>
				`;
				break;
			case '14'://time
				fld += `
					<input type="time" value="${fieldvalue}" name="${field.name}" data-grid-name="${field.name}" class="slds slds-input" ${editable}>
				`;
				break;
			case '15':
			case '16':
				if (editable != '') {
					editable = 'disabled';
				}
				fld += `
					<select class="slds slds-select" name="${field.name}" data-grid-name=${field.name} ${editable}>
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
					<input type="datetime-local" name="${field.name}" data-grid-name="${field.name}" class="slds slds-input" ${editable}>
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
			default:
				fld += `
					<input value="${fieldvalue}" type="text" name="${field.name}" class="slds slds-input" data-grid-name="${field.name}" ${editable}>
				`;
		}
		return fld;
	}

	DeleteRow(idx) {
		document.getElementById(`grid-id-${this.id}-${idx}`).remove();
	}

	DuplicateRow(idx) {
		let el = document.getElementById(`grid-id-${this.id}-${idx}`);
		let grid = document.getElementById(`mastergrid-${this.id}`);
		let newrow = el.cloneNode(true);
		newrow.deleteCell(-1);
		newrow.id = `grid-id-${this.id}-${this.idx}`;
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
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#down"></use>
					</svg>
					<span class="slds-assistive-text"></span>
				</button>
				<div class="slds-dropdown slds-dropdown_right slds-dropdown_actions">
				<ul class="slds-dropdown__list" role="menu">
					<li class="slds-dropdown__item" role="presentation">
						<a role="menuitem" tabindex="-1" onclick="mg[${this.id}].DuplicateRow(${this.idx})">
							<span class="slds-truncate">Duplicate</span>
						</a>
					</li>
					<li class="slds-dropdown__item" role="presentation">
						<a role="menuitem" tabindex="-1" onclick="mg[${this.id}].DeleteRow(${this.idx})">
							<span class="slds-truncate">Delete</span>
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