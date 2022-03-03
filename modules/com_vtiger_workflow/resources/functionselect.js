function openFunctionSelection(fillin) {
	fetch(
		'index.php?module=com_vtiger_workflow&action=com_vtiger_workflowAjax&file=functionselect&fillin='+fillin,
		{
			method: 'post',
			headers: {
				'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			credentials: 'same-origin',
			body: '&'+csrfMagicName+'='+csrfMagicToken
		}
	)
	.then(response => response.text())
	.then(response => {
		document.getElementById(wfexpselectionDIV).innerHTML = response;
		show(wfexpselectionDIV);
	});
	return false;
}

function setSelectedFunction(fillinID) {
	var fi = document.getElementById(fillinID);
	fi.value += document.getElementById('selectedfunction').value;
	hide(wfexpselectionDIV);
	return false;
}

function dblClickFunctionSelect(selected) {
	setFunctionInformation(selected);
	document.getElementById('wffnselectbutton').click();
}

function setFunctionInformation(selected) {
	unselectAllFunctions();
	selected.setAttribute('aria-selected', true);
	selected.classList.add('slds-color__background_gray-4');
	if (wfexpfndefs[selected.dataset.value]) {
		document.getElementById('selectedfunction').value = wfexpfndefs[selected.dataset.value].name;
		document.getElementById('funcname').innerHTML = wfexpfndefs[selected.dataset.value].name;
		document.getElementById('funcdesc').innerHTML = wfexpfndefs[selected.dataset.value].desc;
		let ptbl = `<table class="slds-table slds-table_cell-buffer slds-table_bordered">
		<thead>
		<tr class="slds-line-height_reset">
			<th scope="col">
				<div class="slds-truncate" title="${mod_alert_arr.PName}">${mod_alert_arr.PName}</div>
			</th>
			<th scope="col">
				<div class="slds-truncate" title="${mod_alert_arr.PType}">${mod_alert_arr.PType}</div>
			</th>
			<th scope="col">
				<div class="slds-truncate" title="${mod_alert_arr.POptional}">${mod_alert_arr.POptional}</div>
			</th>
			<th scope="col">
				<div class="slds-truncate" title="${mod_alert_arr.Description}">${mod_alert_arr.Description}</div>
			</th>
		</tr>
		</thead>
		<tbody>`;
		wfexpfndefs[selected.dataset.value].params.forEach(element => {
			ptbl += `<tr class="slds-hint-parent">
			<td>
			<div class="slds-truncate">${element.name}</div>
			</td>
			<td>
			<div class="slds-truncate">${element.type}</div>
			</td>
			<td>
			<div class="slds-truncate">${element.optional ? alert_arr.YES : alert_arr.NO}</div>
			</td>
			<td>
			<div class="slds-truncate slds-cell-wrap">${element.desc}</div>
			</td></tr>`;
		});
		document.getElementById('funcparams').innerHTML = ptbl+'</tbody></table>';
		document.getElementById('funcex').innerHTML = wfexpfndefs[selected.dataset.value].examples.join('<br/>');
	}
}

function unselectAllFunctions() {
	var lis = document.querySelectorAll('#wffnlist > li');
	for (var i = 0; i < lis.length; ++i) {
		lis[i].setAttribute('aria-selected', false);
		lis[i].classList.remove('slds-color__background_gray-4');
	}
}

function setFilteredFunctions(fns) {
	var fnlist = document.getElementById('wffnlist');
	var lis = '';
	Object.keys(fns)
		.forEach(fn => {
			lis += `<li aria-selected="false" class="slds-p-around_xx-small" draggable="false" role="option" tabindex="-1" onClick="setFunctionInformation(this);" onDblClick="dblClickFunctionSelect(this);" data-value="${fn}">${fn}</li>`;
		});
	fnlist.innerHTML = lis;
}

function wffnFilterSearch(srch) {
	var fns = {};
	document.getElementById('fnfiltercat').value = 'All';
	if (srch=='') {
		fns = wfexpfndefs;
	} else {
		srch = srch.toUpperCase();
		fns = Object.keys(wfexpfndefs)
			.filter(fn => wfexpfndefs[fn].nameuc.indexOf(srch) > -1)
			.reduce((res, key) => {
				res[key] = wfexpfndefs[key];
				return res;
			}, {});
	}
	setFilteredFunctions(fns);
}

function wffnFilterCategories(cat) {
	document.getElementById('fnfiltersrch').value = '';
	var fns = {};
	if (cat=='' || cat=='All') {
		fns = wfexpfndefs;
	} else {
		fns = Object.keys(wfexpfndefs)
			.filter(fn => wfexpfndefs[fn].categories.indexOf(cat) > -1)
			.sort()
			.reduce((res, key) => {
				res[key] = wfexpfndefs[key];
				return res;
			}, {});
	}
	setFilteredFunctions(fns);
}