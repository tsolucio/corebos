class MasterGrid {

	constructor() {
		this.id = 0;
		this.idx = 0;
		this.module = null;
		this.fields = [];
	}

	EmptyRow() {
		const flds = JSON.parse(this.fields);
		let row = document.createElement('tr');
		row.id = `grid-id-${this.idx}`;
		for (let i in flds) {
			let data = document.createElement('td');
			data.innerHTML = this.GenerateField(flds[i]);
			row.appendChild(data);
		}
		let actions = document.createElement('td');
		actions.innerHTML = this.GenerateActions();
		row.appendChild(actions);
		document.getElementById(`mastergrid-${this.id}`).appendChild(row);
		this.idx++;
	}

	GenerateField(field) {
		let fld = '';
		let editable = '';
		if (!field.editable) {
			editable = 'readonly';
		}
		switch(field.uitype) {
			case '1':
			case '2':
				fld += `
					<input type="text" data-grid-id="${field.name}" class="slds slds-input" ${editable}>
				`;
				break;
			case '10':
				fld += `
					<input type="text" data-grid-id="${field.name}" class="slds slds-input" ${editable}>
				`;
				break;
			case '15':
			case '16':
				if (editable != '') {
					editable = 'disabled';
				}
				fld += `
					<select class="slds slds-select" data-grid-id=${field.name} ${editable}>
				`;
				for (let i in field.type.picklistValues) {
					fld += `<option value="${field.type.picklistValues[i].value}">${field.type.picklistValues[i].label}</option>`;
				}
				fld += `</select>`;
				break;
			default:
				fld += `
					<input type="text" class="slds slds-input" data-grid-id="${field.name}" ${editable}>
				`;
		}
		return fld;
		console.log(fld)
	}

	DeleteRow(idx) {
		document.getElementById(`grid-id-${idx}`).remove();
	}

	DuplicateRow(idx) {
		let el = document.getElementById(`grid-id-${idx}`);
		let grid = document.getElementById(`mastergrid-${this.id}`);
		let newrow = el.cloneNode(true);
		newrow.id = `grid-id-${this.idx}`;
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