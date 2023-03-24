var MDInstance = Array();
var SelectedRecords = Array();
var SelectedRecordsIds = Array();
var ReloadScreenAfterEdit = 0;
GlobalVariable_getVariable('MasterDetail_ReloadScreenAfterEdit', 0).then(function (response) {
	let obj = JSON.parse(response);
	ReloadScreenAfterEdit = obj.MasterDetail_ReloadScreenAfterEdit;
});

var masterdetailwork = {

	MasterMapID: [],
	MasterButtons: [],
	MasterHide: [],

	moveup: (MDGrid, recordid, module, rowkey) => {
		if (rowkey == 0) {
			return false;
		}
		let prevrowkey = rowkey-1;
		let previd = MDInstance[MDGrid].getValue(prevrowkey, 'record_id') || '';
		let mapname = document.getElementById(MDGrid.substring(6)).dataset.mapname;
		var fileurl = 'module=Utilities&action=UtilitiesAjax&file=MasterDetailGridLayoutActions&mdaction=move&direction=up&recordid='+recordid+'&previd='+previd+'&detail_module='+module+'&mapname='+mapname;
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?' + fileurl
		}).done(function (response) {
			let res = JSON.parse(response);
			if (res.success) {
				MDInstance[MDGrid].readData(1);
			}
		});
	},
	movedown: (MDGrid, recordid, module, rowkey) => {
		let prevrowkey = rowkey+1;
		let previd = MDInstance[MDGrid].getValue(prevrowkey, 'record_id') || '';
		let mapname = document.getElementById(MDGrid.substring(6)).dataset.mapname;
		var fileurl = 'module=Utilities&action=UtilitiesAjax&file=MasterDetailGridLayoutActions&mdaction=move&direction=down&recordid='+recordid+'&previd='+previd+'&detail_module='+module+'&mapname='+mapname;
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?' + fileurl
		}).done(function (response) {
			let res = JSON.parse(response);
			if (res.success) {
				MDInstance[MDGrid].readData(1);
			} else {
				alert(alert_arr.Failed);
			}
		});
	},
	delete: (MDGrid, module, recordid) => {
		if (confirm(alert_arr.ARE_YOU_SURE)) {
			let mapname = document.getElementById(MDGrid.substring(6)).dataset.mapname;
			var fileurl = 'module=Utilities&action=UtilitiesAjax&file=MasterDetailGridLayoutActions&mdaction=delete&detail_module='+module+'&detail_id='+recordid+'&mapname='+mapname;
			jQuery.ajax({
				method: 'POST',
				url: 'index.php?' + fileurl
			}).done(function (response) {
				let res = JSON.parse(response);
				if (res.success) {
					MDInstance[MDGrid].readData(1);
				} else {
					alert(alert_arr.Failed);
				}
			});
			return true;
		}
	},
	inlineedit:(ev) => {
		let rowkey = ev.rowKey;
		let modulename = ev.instance.getValue(rowkey, 'record_module');
		let fieldName = ev.columnName;
		let fieldValue = ev.value;
		let recordid = ev.instance.getValue(rowkey, 'record_id') || '';
		let fileurl = 'module=Utilities&action=UtilitiesAjax&file=MasterDetailGridLayoutActions&mdaction=inline_edit&recordid='+recordid+'&rec_module='+modulename+'&fldName='+fieldName+'&fieldValue='+encodeURIComponent(fieldValue);
		if (recordid != '') {
			GridValidation(recordid, modulename, fieldName, fieldValue).then(function (msg) {
				if (msg == '%%%OK%%%') {
					jQuery.ajax({
						method: 'POST',
						url: 'index.php?' + fileurl
					}).done(function (response) {
						let res = JSON.parse(response);
						if (res.success) {
							ev.instance.readData(1);
							if (ReloadScreenAfterEdit == 1) {
								masterdetailwork.MDReload();
							}
						} else {
							ldsPrompt.show(alert_arr.ERROR, alert_arr.Failed, 'error');
						}
					});
				} else {
					ldsPrompt.show(alert_arr.ERROR, msg, 'error');
				}
			});
		}
	},

	save: (mdgridInstance, module) => {
		const method_prefix = mdgridInstance.substring(6);
		masterdetailwork.MDToggle('', method_prefix);
		if (ReloadScreenAfterEdit == 1) {
			masterdetailwork.MDReload();
		} else {
			MDInstance[mdgridInstance].destroy();
			window['loadMDGrid'+method_prefix]();
			let MasterDetail_currentPage = localStorage.getItem('MasterDetail_currentPage');
			if (MasterDetail_currentPage === undefined) {
				MasterDetail_currentPage = 1;
			}
			MDInstance[mdgridInstance].readData(MasterDetail_currentPage, {
				page: MasterDetail_currentPage
			}, true);
		}
	},

	MDUpsert: (MDGrid, module, recordid, CurrentRecord = '') => {
		localStorage.setItem('MasterDetail_currentPage', MDInstance[MDGrid].getPagination()._currentPage);
		localStorage.setItem('MasterDetail_Name', MDGrid);
		let record = recordid || '';
		if (record!='') {
			record = '&record='+record;
		}
		let targetfield = MasterDetail_TargetField[MDGrid];
		if (CurrentRecord!='') {
			CurrentRecord = '&MDCurrentRecord='+CurrentRecord+'&'+targetfield+'='+CurrentRecord;
		} else if (document.getElementById('record')) {
			CurrentRecord = '&MDCurrentRecord='+document.getElementById('record').value+'&'+targetfield+'='+document.getElementById('record').value;
		} else if (document.getElementById('parent_id')) {
			CurrentRecord = '&MDCurrentRecord='+document.getElementById('parent_id').value+'&'+targetfield+'='+document.getElementById('parent_id').value;
		}
		let mapname = document.getElementById(MDGrid.substring(6)).dataset.mapname;
		let mdgridinfo = JSON.stringify({
			'name': MDGrid,
			'module': module,
			'mapname': mapname,
		});
		window.open('index.php?module='+module+'&action=EditView&Module_Popup_Edit=1&FILTERFIELDSMAP='+mapname+'&MDGridInfo='+mdgridinfo+record+CurrentRecord, null, cbPopupWindowSettings + ',dependent=yes');
	},

	MDView: (MDGrid, module, recordid) => {
		document.getElementById('status').style.display='inline';
		let mapname = document.getElementById(MDGrid.substring(6)).dataset.mapname;
		jQuery.ajax({
			method:'POST',
			url:'index.php?module='+module+'&action=DetailView&Module_Popup_Edit=1&FILTERFIELDSMAP='+mapname+'&record='+recordid
		}).done(function (response) {
			document.getElementById('status').style.display='none';
			document.getElementById('qcform').style.display='inline';
			document.getElementById('qcform').innerHTML = response;
			var btnbar = document.querySelector('ul[name="cbHeaderButtonGroup"]');
			btnbar.innerHTML = `
			<button type="button" class="slds-button slds-button_icon-error slds-button__icon_large" onclick="hide('qcform');" title="${alert_arr['LBL_CLOSE_TITLE']}">
				<svg class="slds-button__icon" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
				</svg>
			</button>`;
			jQuery('#qcform').draggable();
			vtlib_executeJavascriptInElement(document.getElementById('qcform'));
		});
	},

	MDReload: () => {
		VtigerJS_DialogBox.block();
		const queryString = window.location.search;
		const urlParams = new URLSearchParams(queryString);
		const crmId = urlParams.get('record');
		const data = {
			'fldName' : '',
			'fieldValue' : ''
		};
		const url = `file=DetailViewAjax&module=${gVTModule}&action=${gVTModule}Ajax&record=${crmId}&recordid=${crmId}&ajxaction=DETAILVIEWLOAD`;
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?' + url,
			data : data
		}).done(function (response) {
			if (response.indexOf(':#:SUCCESS')>-1) {
				const result = response.split(':#:');
				if (result[2] != null) {
					const target = document.getElementsByClassName('detailview_wrapper_table')[0];
					target.innerHTML = result[2];
					vtlib_executeJavascriptInElement(target);
					let MasterDetail_currentPage = localStorage.getItem('MasterDetail_currentPage');
					let MasterDetail_Name = localStorage.getItem('MasterDetail_Name');
					MDInstance[MasterDetail_Name].readData(MasterDetail_currentPage, {
						page: MasterDetail_currentPage
					}, true);
				}
				VtigerJS_DialogBox.hidebusy();
			}
			VtigerJS_DialogBox.unblock();
		});
	},

	MDToggle: (ev, mid = '') => {
		let label = alert_arr.LBL_COLLAPSE;
		if (ev != '') {
			const id = ev.dataset.id;
			if (masterdetailwork.ToggleStatus[id] === undefined) {
				masterdetailwork.ToggleStatus[id] = 'none';
				label = alert_arr.LBL_EXPAND;
			} else {
				switch (masterdetailwork.ToggleStatus[id]) {
				case 'none':
					masterdetailwork.ToggleStatus[id] = 'block';
					label = alert_arr.LBL_COLLAPSE;
					break;
				case 'block':
					masterdetailwork.ToggleStatus[id] = 'none';
					label = alert_arr.LBL_EXPAND;
					break;
				default:
					//do nothing
				}
			}
			document.getElementById(id).style.display = masterdetailwork.ToggleStatus[id];
			document.getElementById(`btn-${id}`).innerHTML = label;
		}
	},

	GridMounted: (ev) => {
		if (masterdetailwork.MasterHide[ev.instance.el.id]) {
			document.getElementById(`masterdetail__${ev.instance.el.id}`).style.display = 'none';
		}
	},

	checkUnCheckRows: (ev) => {
		SelectedRecords = [];
		SelectedRecordsIds = [];
		SelectedRecords = ev.instance.getCheckedRows();
		SelectedRecords.forEach(row => {
			SelectedRecordsIds.push(row.record_id);
		});
	},

	CallToAction: (ev, workflowid) => {
		if (SelectedRecordsIds.length > 0) {
			runBAWorkflow(workflowid.split(','), SelectedRecordsIds.join(';'));
			masterdetailwork.MDReload();
		}
	},

	MDMassEditRecords: (ev, module, mapname) => {
		if (SelectedRecordsIds.length > 0) {
			let viewid = getviewId();
			let idstring = SelectedRecordsIds.join(';');
			jQuery.ajax({
				method: 'POST',
				url: 'index.php?module='+encodeURIComponent(module)+'&action='+encodeURIComponent(module+'Ajax')+'&file=MassEdit&mode=ajax&idstring='+idstring+'&viewname='+viewid+'&excludedRecords='
			}).done(function (response) {
				let result = response;
				let element = document.getElementById(mapname);
				document.getElementById('massedit_form_div').innerHTML=result;
				document.getElementById('massedit_form')['massedit_recordids'].value = document.getElementById('massedit_form')['idstring'].value;
				document.getElementById('massedit_form')['massedit_module'].value = module;
				vtlib_executeJavascriptInElement(document.getElementById('massedit_form_div'));
				AutocompleteSetup();
				fnvshobj(element, 'massedit');
			});
		}
	},

	ToggleStatus: []
};

class mdActionRender {

	constructor(props) {
		let el;
		let rowKey = props.rowKey;
		let permissions = props.grid.getValue(rowKey, 'record_permissions');
		let recordid = props.grid.getValue(rowKey, 'record_id') || '';
		let module = props.grid.getValue(rowKey, 'record_module');
		el = document.createElement('span');
		let editbtn = ``;
		if (props.columnInfo.renderer.options.edit && permissions.edit == 'yes') {
			editbtn = `
			<button type="button" class="slds-button slds-button_icon slds-button_icon-border-filled" onclick="masterdetailwork.MDUpsert('mdgrid${props.grid.el.id}', '${module}', ${recordid});" title="${alert_arr['JSLBL_Edit']}">
				<svg class="slds-button__icon" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
				</svg>
			</button>`;
		}
		let actions = `
		<div class="slds-button-group" role="group">
			${editbtn}
			<div class="slds-dropdown-trigger slds-dropdown-trigger_hover slds-is-open">
				<button class="slds-button slds-button_icon slds-button_icon-border-filled" aria-haspopup="true" aria-expanded="true">
				<svg class="slds-button__icon" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#threedots"></use>
				</svg>
				<span class="slds-assistive-text">${alert_arr.LBL_SHOW_MORE}</span>
			</button>
			<div class="slds-dropdown slds-dropdown_right slds-dropdown_actions" style="width: 9rem;">
			<ul class="slds-dropdown__list" role="menu">`;
		if (props.columnInfo.renderer.options.moveup) {
			actions += `
			<li class="slds-dropdown__item" role="presentation">
				<a onclick="masterdetailwork.moveup('mdgrid${props.grid.el.id}', ${recordid}, '${module}', ${rowKey});" title="${alert_arr['MoveUp']}">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#up"></use>
					</svg>
					<span class="slds-truncate">${alert_arr['MoveUp']}</span>
				</a>
			</li>`;
		}
		if (props.columnInfo.renderer.options.movedown) {
			actions += `
			<li class="slds-dropdown__item" role="presentation">
				<a onclick="masterdetailwork.movedown('mdgrid${props.grid.el.id}', ${recordid}, '${module}', ${rowKey});" title="${alert_arr['MoveDown']}">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#down"></use>
					</svg>
					<span class="slds-truncate">${alert_arr['MoveDown']}</span>
				</a>
			</li>`;
		}
		if (props.columnInfo.renderer.options.delete && permissions.delete == 'yes') {
			actions += `
			<li class="slds-dropdown__item" role="presentation">
				<a onclick="masterdetailwork.delete('mdgrid${props.grid.el.id}', '${module}', ${recordid});" title="${alert_arr['JSLBL_Delete']}">
					<svg class="slds-button__icon slds-button__icon_left cbds-color-compl-red--sober" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
					</svg>
					<span class="slds-truncate cbds-color-compl-red--sober">${alert_arr['JSLBL_Delete']}</span>
				</a>
			</li>`;
		}
		const MDButtons = JSON.parse(masterdetailwork.MasterButtons[props.grid.el.id]);
		if (MDButtons.length > 0) {
			for (let i in MDButtons) {
				const linklabel = MDButtons[i].linklabel.split('_');
				if (`${linklabel[0]}_${linklabel[1]}` == `MasterDetailButton_${masterdetailwork.MasterMapID[props.grid.el.id]}`) {
					actions += `
					<li class="slds-dropdown__item" role="presentation">
						<a onclick="${MDButtons[i].linkurl.replace('$RECORD$', recordid)}" title="${MDButtons[i].linklabel}">
							<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#touch_action"></use>
							</svg>
							<span class="slds-truncate">${linklabel[2] !== undefined ? linklabel[2] : MDButtons[i].linklabel}</span>
						</a>
					</li>`;
				}
			}
		}
		actions += '</ul></div></div></div>';
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


class mdLinkRender {

	constructor(props) {
		let el;
		let rowKey = props.rowKey;
		let columnName = props.columnInfo.name;
		let fieldValue = props.grid.getValue(rowKey, `${columnName}_attributes`);
		if (fieldValue.length > 0) {
			el = document.createElement('a');
			el.href = fieldValue[0].mdLink;
			if (fieldValue[0].mdTarget) {
				el.target = fieldValue[0].mdTarget;
			}
			el.innerHTML = String(fieldValue[0].mdValue);
		} else {
			el = document.createElement('p');
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
