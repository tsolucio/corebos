var masterdetailwork = {

	moveup: (MDGrid, recordid, gridinstance, module, rowkey) => {
		if (rowkey == 0) {
			return false;
		}
		let prevrowkey = rowkey-1;
		let previd = gridinstance.getValue(prevrowkey, 'record_id') || '';
		let mapname = document.getElementById(MDGrid.substring(6)).dataset.mapname;
		var fileurl = 'module=Utilities&action=UtilitiesAjax&file=MasterDetailGridLayoutActions&mdaction=move&direction=up&recordid='+recordid+'&previd='+previd+'&detail_module='+module+'&mapname='+mapname;
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?' + fileurl
		}).done(function (response) {
			res = JSON.parse(response);
			if (res.success == true) {
				gridinstance.readData(1);
			}
		});
	},
	movedown: (MDGrid, recordid, gridinstance, module, rowkey) => {
		let prevrowkey = rowkey+1;
		let previd = gridinstance.getValue(prevrowkey, 'record_id') || '';
		let mapname = document.getElementById(MDGrid.substring(6)).dataset.mapname;
		var fileurl = 'module=Utilities&action=UtilitiesAjax&file=MasterDetailGridLayoutActions&mdaction=move&direction=down&recordid='+recordid+'&previd='+previd+'&detail_module='+module+'&mapname='+mapname;
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?' + fileurl
		}).done(function (response) {
			res = JSON.parse(response);
			if (res.success == true) {
				gridinstance.readData(1);
			} else {
				alert(alert_arr.Failed);
			}
		});
	},
	delete: (MDGrid, module, recordid, gridinstance) => {
		if (confirm(alert_arr.ARE_YOU_SURE)) {
			let mapname = document.getElementById(MDGrid.substring(6)).dataset.mapname;
			var fileurl = 'module=Utilities&action=UtilitiesAjax&file=MasterDetailGridLayoutActions&mdaction=delete&detail_module='+module+'&detail_id='+recordid+'&mapname='+mapname;
			jQuery.ajax({
				method: 'POST',
				url: 'index.php?' + fileurl
			}).done(function (response) {
				res = JSON.parse(response);
				if (res.success == true) {
					gridinstance.readData(1);
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
			jQuery.ajax({
				method: 'POST',
				url: 'index.php?' + fileurl
			}).done(function (response) {
				res = JSON.parse(response);
				if (res.success == true) {
					ev.instance.readData(1);
				} else {
					alert(alert_arr.Failed);
				}
			});
		}
	},

	save: (mdgridInstance, module) => {
		setTimeout(function () {
			window[mdgridInstance].readData(1);
		}, 1300);
	},

	MDUpsert: (MDGrid, module, recordid) => {
		record = recordid || '';
		if (record!='') {
			record = '&record='+record;
		}
		let mapname = document.getElementById(MDGrid.substring(6)).dataset.mapname;
		let mdgridinfo = JSON.stringify({
			'name': MDGrid,
			'module': module,
			'mapname': mapname,
		});
		window.open('index.php?module='+module+'&action=EditView&Module_Popup_Edit=1&FILTERFIELDSMAP='+mapname+'&MDGridInfo='+mdgridinfo+record, null, cbPopupWindowSettings + ',dependent=yes');
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
};

class mdActionRender {

	constructor(props) {
		let el;
		let rowKey = props.rowKey;
		let recordid = props.grid.getValue(rowKey, 'record_id') || '';
		let module = props.grid.getValue(rowKey, 'record_module');
		el = document.createElement('span');
		let actions = '<div class="slds-button-group" role="group">';
		let mdgridob = 'mdgrid'+props.grid.el.id;
		if (props.columnInfo.renderer.options.moveup) {
			actions += `
			<button class="slds-button slds-button_icon slds-button_icon-border-filled" onclick="masterdetailwork.moveup('mdgrid${props.grid.el.id}', ${recordid}, ${mdgridob},'${module}', ${rowKey});" title="${alert_arr['MoveUp']}">
				<svg class="slds-button__icon" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#up"></use>
				</svg>
			</button>`;
		}
		if (props.columnInfo.renderer.options.movedown) {
			actions += `
			<button class="slds-button slds-button_icon slds-button_icon-border-filled" onclick="masterdetailwork.movedown('mdgrid${props.grid.el.id}', ${recordid}, ${mdgridob}, '${module}', ${rowKey});" title="${alert_arr['MoveDown']}">
				<svg class="slds-button__icon" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#down"></use>
				</svg>
			</button>`;
		}
		if (props.columnInfo.renderer.options.edit) {
			actions += `
			<button class="slds-button slds-button_icon slds-button_icon-border-filled" onclick="masterdetailwork.MDUpsert('mdgrid${props.grid.el.id}', '${module}', ${recordid});" title="${alert_arr['LBL_EDIT']}">
				<svg class="slds-button__icon" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
				</svg>
			</button>`;
		}
		if (props.columnInfo.renderer.options.delete) {
			actions += `
			<button class="slds-button slds-button_icon slds-button_icon-border-filled" onclick="masterdetailwork.delete('mdgrid${props.grid.el.id}', '${module}', ${recordid},${mdgridob});" title="${alert_arr['LBL_DELETE']}">
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
