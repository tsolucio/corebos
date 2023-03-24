var RLInstance = Array();

var relatedlistgrid = {

	RLInstanceInfo: [],
	FieldLabels: [],
	RelatedFields: [],
	Tooltips: [],
	MapName: [],
	Wizard: [],
	WizardWorkflows: [],
	NextStep: [],
	PopupAction: [],
	CreateView: [],

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
				relatedlistgrid.loadedTooltips = [];
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
			relatedlistgrid.loadedTooltips = [];
		}, 1300);
	},

	upsert: (Grid, module, recordid, CurrentRecord = '', related_fieldname, cbfromid = 0) => {
		let record = recordid || '';
		if (record!='') {
			record = '&record='+record;
		}
		if (CurrentRecord!='') {
			CurrentRecord = '&MDCurrentRecord='+CurrentRecord+'&RLFieldName='+related_fieldname+'&'+related_fieldname+'='+CurrentRecord+'&cbfromid='+cbfromid;
		} else if (document.getElementById('record')) {
			let recid = document.getElementById('record').value;
			CurrentRecord = '&MDCurrentRecord='+recid+'&'+related_fieldname+'='+recid+'&cbfromid='+cbfromid;
		}
		let rlgridinfo = JSON.stringify({
			'name': Grid,
			'module': module,
			'mapname': relatedlistgrid.MapName[Grid],
		});
		window.open('index.php?module='+module+'&action=EditView&Module_Popup_Edit=1&FILTERFIELDSMAP='+relatedlistgrid.MapName[Grid]+'&RelatedListGridInfo='+rlgridinfo+record+CurrentRecord, null, cbPopupWindowSettings + ',dependent=yes');
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
					relatedlistgrid.loadedTooltips = [];
				});
			}
		}
	},

	Tooltip: (id, Grid, rowKey, module) => {
		if (!relatedlistgrid.isTooltipLoaded(id) && RLInstance[Grid] !== undefined) {
			const data = RLInstance[Grid].getData();
			relatedlistgrid.loadedTooltips.push(id);
			let fields = JSON.parse(relatedlistgrid.Tooltips[Grid]);
			let fieldLabel = JSON.parse(relatedlistgrid.FieldLabels[Grid]);
			let body = '';
			if (typeof fields[module] == 'string') {
				fields[module] = [fields[module]];
			}
			for (let i in fields[module]) {
				body += `
					<dl class="slds-list_horizontal slds-p-bottom_x-small">
						<dt class="slds-item_label slds-text-color_weak slds-truncate" style="width: 50%">
							<strong>${fieldLabel[module][fields[module][i]]}:</strong>
						</dt>
						<dd class="slds-item_detail slds-truncate" style="width: 50%">${data[rowKey][fields[module][i]]}</dd>
					</dl>`;
			}
			const el = `
			<div class="cbds-tooltip__wrapper--inner cbds-tooltip__margin--left">
				<section class="slds-popover slds-nubbin_bottom-left" role="dialog">
					<header class="slds-popover__header" style="background: #1589ee;color: white">
						<div class="slds-media slds-media_center slds-has-flexi-truncate">
						<div class="slds-media__figure">
							<span class="slds-icon_container slds-icon-utility-error">
								<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#preview"></use>
								</svg>
							</span>
						</div>
						<div class="slds-media__body">
							<h2 class="slds-truncate" title="${alert_arr.QuickView}">${alert_arr.QuickView}</h2>
						</div>
						</div>
					</header>
					<div class="slds-popover__body">
						${body}
					</div>
				</section>
			</div>`;
			const createEl = document.createElement('div');
			createEl.id = `tooltip-${id}`;
			createEl.classList.add('cbds-tooltip__wrapper');
			createEl.innerHTML = el;
			if (document.getElementById(`cbds-tooltip__trigger-${id}`) !== null) {
				document.getElementById(`cbds-tooltip__trigger-${id}`).appendChild(createEl);
			}
		}
	},

	isTooltipLoaded: (id) => {
		return relatedlistgrid.loadedTooltips.indexOf(id) == -1 ? false : true;
	},

	loadedTooltips: [],

	openWizard: (grid, id, mapid, module) => {
		let getWizardActive = localStorage.getItem('currentWizardActive');
		let modalContainer = document.getElementById('global-modal-container');
		if (getWizardActive == null) {
			localStorage.setItem('currentWizardActive', id+mapid);
			if (modalContainer) {
				ldsModal.close();
			}
		} else {
			if (getWizardActive == id+mapid) {
				if (modalContainer) {
					modalContainer.style.display = '';
					return false;
				}
			} else {
				localStorage.setItem('currentWizardActive', id+mapid);
				if (modalContainer) {
					ldsModal.close();
				}
			}
		}
		let workflows = JSON.parse(relatedlistgrid.WizardWorkflows[grid]);
		let waitTime = 0;
		for (let i in workflows) {
			ExecuteFunctions('execwf', 'wfid='+workflows[i]+'&ids='+id);
			waitTime = 1000;
		}
		let url = 'index.php?module=Utilities&action=UtilitiesAjax&file=RelatedListWidgetActions&rlaction=Wizard&mapid='+mapid;
		ldsModal.show('Wizard', '<div id="cbds-loader" style="height: 200px"></div>', 'large');
		loadJS('include/js/wizard.js?ver=1.0.1');
		relatedlistgrid.loader('show');
		setTimeout(function () {
			relatedlistgrid.Request(url, 'post', {
				grid: grid,
				recordid: id,
				isModal: true,
				modname: module,
			}).then(function (response) {
				ldsModal.close();
				ldsModal.show('Wizard', response, 'large', '', '', false);
				let wizardTitle = document.getElementById('wizard-title').innerHTML;
				document.getElementById('global-modal-container__title').innerHTML = wizardTitle;
				document.getElementById('wizard-title').innerHTML = '';
				let ProceedToNextStep = JSON.parse(relatedlistgrid.NextStep[grid]);
				const event = new CustomEvent('onWizardModal', {detail: {
					'ProceedToNextStep': ProceedToNextStep[module]
				}});
				window.dispatchEvent(event);
			});
		}, waitTime);
	},

	Request: async (url, method, body = {}) => {
		const options = {
			method: method,
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
			}
		};
		if (method == 'post') {
			options.body = '&'+csrfMagicName+'='+csrfMagicToken+'&data='+JSON.stringify(body);
		}
		const response = await fetch(url, options);
		return response.text();
	},

	findRelatedField: (module, grid) => {
		const modules = JSON.parse(relatedlistgrid.RelatedFields[grid]);
		return modules[module];
	},

	loader: (type) => {
		const loaderid = document.getElementById('cbds-loader');
		if (type == 'show') {
			const loader = document.createElement('div');
			loader.classList.add('cbds-loader');
			loader.id = 'cbds-loader';
			loaderid.appendChild(loader);
		} else if (type == 'hide') {
			if (loaderid) {
				loaderid.remove();
			}
		}
	},
};

class RLinkRender {

	constructor(props) {
		let el;
		let rowKey = props.rowKey;
		let columnName = props.columnInfo.name;
		let fieldValue = props.grid.getValue(rowKey, `${columnName}_attributes`);
		let tooltip = JSON.parse(relatedlistgrid.Tooltips[props.grid.el.id]);
		if (typeof fieldValue == 'object' && fieldValue != null) {
			if (fieldValue.length > 0) {
				el = document.createElement('a');
				el.href = fieldValue[0].mdLink;
				if (fieldValue[0].mdTarget) {
					el.target = fieldValue[0].mdTarget;
				}
				el.innerHTML = String(fieldValue[0].mdValue);
			} else if (columnName == 'parentaction') {
				let parent_id = props.grid.getValue(rowKey, 'parent_id');
				let parent_module = props.grid.getValue(rowKey, 'parent_module');
				el = document.createElement('a');
				el.href = `index.php?module=${parent_module}&action=DetailView&record=${parent_id}`;
				el.target = '_blank';
				if (tooltip[parent_module] !== undefined) {
					props.value = `<span>${props.value}</span>
					<span class="slds-icon_container slds-float_right slds-m-right_small cbds-tooltip__trigger slds-p-left_xx-small"
						id="cbds-tooltip__trigger-${parent_id}"
						onmouseover="relatedlistgrid.Tooltip(${parent_id}, '${props.grid.el.id}', ${rowKey}, '${parent_module}')"
						style="position: absolute">
						<svg class="slds-icon slds-icon-text-default slds-icon_x-small" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info"></use>
						</svg>
					</span>`;
				}
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
		if (props.module === undefined) {
			this.el.value = String(props.value);
		} else {
			if (props.formattedValue != '') {
				this.el.innerHTML = String(props.formattedValue);
			} else {
				this.el.textContent = String(props.value);
			}
		}
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
		let related_parent_fieldname = props.grid.getValue(rowKey, 'related_parent_fieldname') || '';
		let related_child = props.grid.getValue(rowKey, 'related_child') || '';
		let recordid = parent_module == '' ? child_id : parent_id;
		let module = parent_module == '' ? child_module : parent_module;
		if (related_parent_fieldname == '') {
			related_parent_fieldname = related_fieldname;
		}
		el = document.createElement('span');
		let className = 'slds-button_icon-border-filled';
		if (parent_module == module) {
			className = 'slds-button_icon-brand';
		}
		let popupbutton = '';
		let popupactions = JSON.parse(relatedlistgrid.PopupAction[`${props.grid.el.id}`]);
		let createview = JSON.parse(relatedlistgrid.CreateView[`${props.grid.el.id}`]);
		if (parent_module == '' && popupactions[related_child] !== undefined) {
			if (popupactions[related_child].conditions.fieldname != '') {
				if (popupactions[related_child].conditions.fieldname.indexOf('.') === -1) {
					//access direct record values
					let popupid = 0;
					let fieldname = popupactions[related_child].conditions.fieldname;
					let popup = popupactions[related_child].conditions.popup;
					let fldvalue = props.grid.getValue(rowKey, `${fieldname}`);
					let fldvalue_raw = props.grid.getValue(rowKey, `${fieldname}_raw`);
					if (fldvalue != null && popup != '') {
						for (let i in popup.values) {
							if (popup.values[i].value == fldvalue || popup.values[i].value == fldvalue_raw) {
								popupid = popup.values[i].id;
								break;
							}
						}
						if (popupid > 0) {
							popupbutton += `
							<button type="button" class="slds-button slds-button_icon slds-button_icon-border-filled" onclick="getProcessInfo('','DetailView','Save','','${popupid}|${related_child}|${recordid}')">
								<svg class="slds-button__icon" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info"></use>
								</svg>
							</button>`;
						}
					}
				}
			} else {
				//no conditions: show action in evey row
				popupbutton += `
				<button type="button" class="slds-button slds-button_icon slds-button_icon-border-filled" onclick="getProcessInfo('','DetailView','Save','','${popupactions[related_child].id}|${related_child}|${recordid}')">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info"></use>
					</svg>
				</button>`;
			}
		}
		let actions = `
		<div class="slds-button-group" role="group">
			${popupbutton}
			<div class="slds-dropdown-trigger slds-dropdown-trigger_hover slds-is-open slds-button_last">
				<button type="button" class="slds-button slds-button_icon ${className}" aria-haspopup="true" aria-expanded="true">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#threedots"></use>
					</svg>
					<span class="slds-assistive-text">${alert_arr.LBL_SHOW_MORE}</span>
				</button>
				<div class="slds-dropdown slds-dropdown_right slds-dropdown_actions" style="width: 8rem;">
				<ul class="slds-dropdown__list" role="menu">`;
		if (parent_module != '') {
			actions += `
			<li class="slds-dropdown__item">
				<a onclick="relatedlistgrid.upsert('${props.grid.el.id}', '${related_child}', '', ${recordid}, '${related_fieldname}', ${recordid});" role="menuitem" tabindex="0">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use>
					</svg>
					<span class="slds-truncate">${alert_arr['JSLBL_Create']}</span>
				</a>
			</li>
			`;
		} else {
			actions += `
			<li class="slds-dropdown__item">
				<a onclick="relatedlistgrid.upsert('${props.grid.el.id}', '${related_child}', '', ${recordid}, '${related_fieldname}', ${recordid});" role="menuitem" tabindex="0">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use>
					</svg>
					<span class="slds-truncate">${alert_arr['JSLBL_Create']}</span>
				</a>
			</li>`;
			if (relatedlistgrid.findRelatedField(related_child, props.grid.el.id) == '') {
				actions = `
				<div class="slds-button-group" role="group">
				${popupbutton}
				<div class="slds-dropdown-trigger slds-dropdown-trigger_hover slds-is-open slds-button_last">
					<button type="button" class="slds-button slds-button_icon slds-button_icon-border-filled" aria-haspopup="true" aria-expanded="true">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#threedots"></use>
						</svg>
						<span class="slds-assistive-text">${alert_arr.LBL_SHOW_MORE}</span>
					</button>
					<div class="slds-dropdown slds-dropdown_right slds-dropdown_actions" style="width: 8rem;">
					<ul class="slds-dropdown__list" role="menu">`;
			}
		}
		if (createview[related_child] !== undefined) {
			if (createview[related_child].conditions.fieldname != '') {
				if (createview[related_child].conditions.fieldname.indexOf('.') === -1) {
					//access direct record values
					let docreate = 0;
					let fieldname = createview[related_child].conditions.fieldname;
					let create = createview[related_child].conditions.create;
					let fldvalue = props.grid.getValue(rowKey, `${fieldname}`);
					let fldvalue_raw = props.grid.getValue(rowKey, `${fieldname}_raw`);
					if (fldvalue != null && create != '') {
						for (let i in create.value) {
							if (create.value[i] == fldvalue || create.value[i] == fldvalue_raw) {
								docreate = 1;
								break;
							}
						}
						if (docreate == 1) {
							actions += `
							<li class="slds-dropdown__item" id="create__${child_id}">
								<a onclick="relatedlistgrid.upsert('${props.grid.el.id}', '${related_child}', '', ${parent_of_child}, '${related_parent_fieldname}', ${recordid});" role="menuitem" tabindex="0">
									<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use>
									</svg>
									<span class="slds-truncate">${alert_arr['JSLBL_Create']}</span>
								</a>
							</li>`;
						}
					}
				} else {
					//render from backend
					actions += `<li class="slds-dropdown__item" id="create__${recordid}"></li>`;
				}
			}
		}
		let wizard = JSON.parse(relatedlistgrid.Wizard[`${props.grid.el.id}`]);
		if (wizard[parent_module] !== undefined && wizard[parent_module] != '') {
			if (wizard[parent_module].length === undefined && wizard[parent_module].id !== undefined) {
				actions += `
					<li class="slds-dropdown__item">
						<a onclick="relatedlistgrid.openWizard('${props.grid.el.id}', ${recordid}, ${wizard[parent_module].id}, '${parent_module}');" role="menuitem" tabindex="0">
							<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#record_create"></use>
							</svg>
							<span class="slds-truncate">${wizard[parent_module].label}</span>
						</a>
					</li>`;
			} else {
				for (let i in wizard[parent_module]) {
					actions += `
					<li class="slds-dropdown__item">
						<a onclick="relatedlistgrid.openWizard('${props.grid.el.id}', ${recordid}, ${wizard[parent_module][i].id}, '${parent_module}');" role="menuitem" tabindex="0">
							<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#record_create"></use>
							</svg>
							<span class="slds-truncate">${wizard[parent_module][i].label}</span>
						</a>
					</li>`;
				}
			}
		}
		if (permissions.parent_edit == 'yes') {
			actions += `
			<li class="slds-dropdown__item">
				<a onclick="relatedlistgrid.upsert('${props.grid.el.id}', '${module}', ${recordid});" role="menuitem" tabindex="0">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
					</svg>
					<span class="slds-truncate">${alert_arr['JSLBL_Edit']}</span>
				</a>
			</li>`;
		}
		if (permissions.child_edit == 'yes') {
			actions += `
			<li class="slds-dropdown__item">
				<a onclick="relatedlistgrid.upsert('${props.grid.el.id}', '${module}', ${recordid});" role="menuitem" tabindex="0">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
					</svg>
					<span class="slds-truncate">${alert_arr['JSLBL_Edit']}</span>
				</a>
			</li>`;
		}
		const parent_delete = props.columnInfo.renderer.options.parent_delete;
		const child_delete = props.columnInfo.renderer.options.child_delete;
		if (parent_delete == 'O' && permissions.parent_edit == 'yes') {
			actions += `
			<li class="slds-dropdown__item">
				<a onclick="relatedlistgrid.delete('${props.grid.el.id}', '${parent_module}', ${recordid}, '${related_parent_fieldname}');" role="menuitem" tabindex="0">
					<svg class="slds-button__icon slds-button__icon_left cbds-color-compl-red--sober" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
					</svg>
					<span class="slds-truncate cbds-color-compl-red--sober">${alert_arr['JSLBL_Delete']}</span>
				</a>
			</li>`;
		}
		if (child_delete == 'O' && permissions.child_edit == 'yes') {
			actions += `
			<li class="slds-dropdown__item">
				<a onclick="relatedlistgrid.delete('${props.grid.el.id}', '${child_module}', ${recordid}, '${related_parent_fieldname}');" role="menuitem" tabindex="0">
					<svg class="slds-button__icon slds-button__icon_left cbds-color-compl-red--sober" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
					</svg>
					<span class="slds-truncate cbds-color-compl-red--sober">${alert_arr['JSLBL_Delete']}</span>
				</a>
			</li>`;
		}
		if (parent_module == '' && popupactions[related_child] !== undefined) {
			if (popupactions[related_child].conditions.fieldname != '') {
				let url = 'index.php?module=Utilities&action=UtilitiesAjax&file=RelatedListWidgetActions&rlaction=PopupAction';
				if (popupactions[related_child].conditions.fieldname.indexOf('.') !== -1 && popupactions[related_child].conditions.popup != '') {
					//get the value in a related module
					let minfo = popupactions[related_child].conditions.fieldname.split('.');
					let popupvalues = popupactions[related_child].conditions.popup.values;
					if (popupvalues.length == undefined) {
						popupvalues = [popupactions[related_child].conditions.popup.values];
					}
					relatedlistgrid.Request(url, 'post', {
						recordid: recordid,
						module: related_child,
						relatedmodule: minfo[0],
						fieldname: minfo[1],
						relatedfield: popupactions[related_child].conditions.relatedfield,
						values: popupvalues,
					}).then(function (popupid) {
						if (popupid !== 'false') {
							actions += `
							<button type="button" class="slds-button slds-button_icon slds-button_icon-border-filled" onclick="getProcessInfo('','DetailView','Save','','${popupid}|${related_child}|${recordid}')">
								<svg class="slds-button__icon" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info"></use>
								</svg>
							</button>`;
							el.innerHTML = actions;
						}
					});
				}
			}
		}
		actions += '</ul></div></div></div>';
		el.innerHTML = actions;
		this.el = el;
		this.render(props);
		if (createview[related_child] !== undefined) {
			if (createview[related_child].conditions.fieldname != '') {
				if (createview[related_child].conditions.fieldname.indexOf('.') !== -1) {
					let url = 'index.php?module=Utilities&action=UtilitiesAjax&file=RelatedListWidgetActions&rlaction=CreateView';
					//get the value in a related module
					let cinfo = createview[related_child].conditions.fieldname.split('.');
					relatedlistgrid.Request(url, 'post', {
						recordid: recordid,
						module: related_child,
						relatedmodule: cinfo[0],
						fieldname: cinfo[1],
						relatedfield: createview[related_child].conditions.relatedfield,
						values: createview[related_child].conditions.create.value,
					}).then(function (response) {
						if (response == 'true') {
							let link = `
							<a onclick="relatedlistgrid.upsert('${props.grid.el.id}', '${related_child}', '', ${parent_of_child}, '${related_parent_fieldname}', ${recordid});" role="menuitem" tabindex="0">
								<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use>
								</svg>
								<span class="slds-truncate">${alert_arr['JSLBL_Create']}</span>
							</a>`;
							document.getElementById(`create__${recordid}`).innerHTML = link;
						}
					});

				}
			}
		}
	}

	getElement() {
		return this.el;
	}

	render(props) {
		this.el.value = String(props.value);
	}
}