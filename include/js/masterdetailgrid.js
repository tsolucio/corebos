var masterdetailwork = {

	moveup: (recordid) => {
		console.log('up'+recordid);
	},

	movedown: (recordid) => {
		console.log('down'+recordid);
	},

	delete: (module, recordid) => {
		if (confirm(alert_arr.ARE_YOU_SURE)) {
			document.location.href=url;
			'index.php?module='+Project+'&action=updateRelations&parentid='+Projectid+'&destination_module='+module+'&idlist='+recordid+'&mode=delete';
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
		if (props.columnInfo.renderer.options.moveup) {
			actions += `
			<button class="slds-button slds-button_icon slds-button_icon-border-filled" onclick="masterdetailwork.moveup(${recordid});" title="${alert_arr['MoveUp']}">
				<svg class="slds-button__icon" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#up"></use>
				</svg>
			</button>`;
		}
		if (props.columnInfo.renderer.options.movedown) {
			actions += `
			<button class="slds-button slds-button_icon slds-button_icon-border-filled" onclick="masterdetailwork.movedown(${recordid});" title="${alert_arr['MoveDown']}">
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
			<button class="slds-button slds-button_icon slds-button_icon-border-filled" onclick="masterdetailwork.delete('${module}', ${recordid});" title="${alert_arr['LBL_DELETE']}">
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
