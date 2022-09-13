var RLInstance = Array();

var relatedlistgrid = {

	delete: (Grid, module, recordid, fieldname) => {
		if (confirm(alert_arr.ARE_YOU_SURE)) {
			VtigerJS_DialogBox.showbusy();
			let url = 'module=Utilities&action=UtilitiesAjax&file=RelatedListWidgetActions&rlaction=delete&detail_module='+module+'&detail_id='+recordid+'&fieldname='+fieldname;
			jQuery.ajax({
				method: 'POST',
				url: `index.php?${url}`
			}).done(function (response) {
				let res = JSON.parse(response);
				if (res) {
					RLInstance[Grid].readData(1);
				} else {
					alert(alert_arr.Failed);
				}
				VtigerJS_DialogBox.hidebusy();
			});
			return true;
		}		
	},

	save: (Grid, module) => {
		const method_prefix = Grid.substring(9);
		VtigerJS_DialogBox.showbusy();
		setTimeout(function () {
			RLInstance[Grid].readData(1);
			VtigerJS_DialogBox.hidebusy();
		}, 1300);
	},

	upsert: (Grid, module, recordid, CurrentRecord = '', target = false, mode = '') => {
		let record = recordid || '';
		let related_fieldname = '';
		if (record!='') {
			record = '&record='+record;
		}
		if (target) {
			related_fieldname = target_related_fieldname[module];
		} else {
			related_fieldname = origin_related_fieldname[module];
		}
		if (mode == 'recursive') {
			related_fieldname = target_related_fieldname[`${module}-${module}`];
		}
		if (CurrentRecord!='') {
			CurrentRecord = '&MDCurrentRecord='+CurrentRecord+'&RLFieldName='+related_fieldname;
		} else if (document.getElementById('record')) {
			CurrentRecord = '&MDCurrentRecord='+document.getElementById('record').value;
		}
		let rlgridinfo = JSON.stringify({
			'name': Grid,
			'module': module,
			'mapname': mapname,
		});
		window.open('index.php?module='+module+'&action=EditView&Module_Popup_Edit=1&FILTERFIELDSMAP='+mapname+'&RelatedListGridInfo='+rlgridinfo+record+CurrentRecord, null, cbPopupWindowSettings + ',dependent=yes');
	},

	inlineedit: (ev) => {
		let rowkey = ev.rowKey;
		let fieldName = ev.columnName;
		let fieldValue = ev.value;
		let parent_id = ev.instance.getValue(rowkey, 'parent_id') || '';
		let child_id = ev.instance.getValue(rowkey, 'child_id') || '';
		let parent_module = ev.instance.getValue(rowkey, 'parent_module') || '';
		let child_module = ev.instance.getValue(rowkey, 'child_module') || '';
		let recordid = parent_module == '' ? child_id : parent_id;
		if (child_module != '') {
			let fileurl = 'module=Utilities&action=UtilitiesAjax&file=MasterDetailGridLayoutActions&mdaction=inline_edit&recordid='+recordid+'&rec_module='+child_module+'&fldName='+fieldName+'&fieldValue='+encodeURIComponent(fieldValue);
			if (recordid != '') {
				VtigerJS_DialogBox.showbusy();
				GridValidation(recordid, child_module, fieldName, fieldValue).then(function (msg) {
					if (msg == '%%%OK%%%') {
						jQuery.ajax({
							method: 'POST',
							url: 'index.php?' + fileurl
						}).done(function (response) {
							let res = JSON.parse(response);
							if (res.success) {
								ev.instance.readData(1);
							} else {
								ldsPrompt.show(alert_arr.ERROR, alert_arr.Failed, 'error');
							}
							VtigerJS_DialogBox.hidebusy();
						});
					} else {
						ldsPrompt.show(alert_arr.ERROR, msg, 'error');
						VtigerJS_DialogBox.hidebusy();
					}
				});
			}
		}		
	}
};

class RLinkRender {

	constructor(props) {
		let el;
		let rowKey = props.rowKey;
		let columnName = props.columnInfo.name;
		let fieldValue = props.grid.getValue(rowKey, `${columnName}_attributes`);
		if (typeof fieldValue == 'object' && fieldValue != null) {
			if (fieldValue.length > 0) {
				el = document.createElement('a');
				el.href = fieldValue[0].mdLink;
				if (fieldValue[0].mdTarget) {
					el.target = fieldValue[0].mdTarget;
				}
				el.innerHTML = String(fieldValue[0].mdValue);
			} else if (columnName == 'parentaction') {
				let parent_id = props.grid.getValue(rowKey, `parent_id`);
				let parent_module = props.grid.getValue(rowKey, `parent_module`);
				el = document.createElement('a');
				el.href = `index.php?module=${parent_module}&action=DetailView&record=${parent_id}`;
				el.target = `_blank`;
				el.innerHTML = String(props.value);
			} else {
				el = document.createElement('span');
				el.innerHTML = String(props.value);
			}
		} else {
			if (props.value == null) {
				props.value = '';
			}
			el = document.createElement('span');
			el.innerHTML = String(props.value);		
		}
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

class RLActionRender {

	constructor(props) {
		let el;
		let rowKey = props.rowKey;
		let permissions = props.grid.getValue(rowKey, 'record_permissions');
		let parent_id = props.grid.getValue(rowKey, 'parent_id') || '';
		let parent_module = props.grid.getValue(rowKey, 'parent_module') || '';
		let child_id = props.grid.getValue(rowKey, 'child_id') || '';
		let child_module = props.grid.getValue(rowKey, 'child_module') || '';
		let parent_of_child = props.grid.getValue(rowKey, 'parent_of_child') || '';
		let related_fieldname = props.grid.getValue(rowKey, 'related_fieldname') || '';
		let related_child = props.grid.getValue(rowKey, 'related_child') || '';
		let recordid = parent_module == '' ? child_id : parent_id;
		let module = parent_module == '' ? child_module : parent_module;
		el = document.createElement('span');
		let actions = '<div class="slds-button-group" role="group">';
		if (parent_module != '') {
			actions += `
			<button class="slds-button slds-button_icon slds-button_icon-border-filled" onclick="relatedlistgrid.upsert('rlgrid${props.grid.el.id}', '${related_child}', '', ${recordid}, true);" title="${alert_arr['JSLBL_Create']}">
				<svg class="slds-button__icon" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use>
				</svg>
			</button>`;			
		} else {
			actions += `
			<button class="slds-button slds-button_icon slds-button_icon-border-filled" onclick="relatedlistgrid.upsert('rlgrid${props.grid.el.id}', '${related_child}', '', ${recordid}, true, 'recursive');" title="${alert_arr['JSLBL_Create']}">
				<svg class="slds-button__icon" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use>
				</svg>
			</button>`;
			if (target_related_fieldname[`${related_child}-${related_child}`] == '') {
				actions = '<div class="slds-button-group" role="group">';
			}
		}
		if (permissions.parent_edit == 'yes') {
			actions += `
			<button class="slds-button slds-button_icon slds-button_icon-border-filled" onclick="relatedlistgrid.upsert('rlgrid${props.grid.el.id}', '${module}', ${recordid});" title="${alert_arr['JSLBL_Edit']}">
				<svg class="slds-button__icon" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
				</svg>
			</button>`;
		}
		if (permissions.child_edit == 'yes') {
			actions += `
			<button class="slds-button slds-button_icon slds-button_icon-border-filled" onclick="relatedlistgrid.upsert('rlgrid${props.grid.el.id}', '${module}', ${recordid});" title="${alert_arr['JSLBL_Edit']}">
				<svg class="slds-button__icon" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
				</svg>
			</button>`;
		}
		const parent_delete = props.columnInfo.renderer.options.parent_delete;
		const child_delete = props.columnInfo.renderer.options.child_delete;
		if (parent_delete == 'O' && permissions.parent_edit == 'yes') {
			actions += `
			<button class="slds-button slds-button_icon slds-button_icon-border-filled" onclick="relatedlistgrid.delete('rlgrid${props.grid.el.id}', '${parent_module}', ${recordid}, '${related_fieldname}');" title="${alert_arr['JSLBL_Delete']}">
				<svg class="slds-button__icon" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
				</svg>
			</button>`;
		}
		if (child_delete == 'O' && permissions.child_edit == 'yes') {
			actions += `
			<button class="slds-button slds-button_icon slds-button_icon-border-filled" onclick="relatedlistgrid.delete('rlgrid${props.grid.el.id}', '${child_module}', ${recordid}, '${related_fieldname}');" title="${alert_arr['JSLBL_Delete']}">
				<svg class="slds-button__icon" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
				</svg>
			</button>`;
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