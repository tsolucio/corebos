/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function setwfexeexppressions() {
	let wfees = [];
	Array.from(document.getElementById('wfexeexptbl').rows).forEach(function (row) {
		let exp = row.cells[0].querySelector('input');
		if (exp==null) {
			return;
		}
		exp = exp.value;
		let etype = row.cells[2].querySelector('input').value;
		if (exp != '') {
			let vname = row.cells[1].querySelector('input').value;
			wfees.push({'exp': exp, 'typ': etype, 'var': vname});
		}
	});
	wfexeexppressions = wfees;
	document.getElementById('wfexeexps').value = JSON.stringify(wfees);
}

function setwfexetable() {
	if (wfexeexppressions!=null) {
		wfexeexppressions.forEach(function (data) {
			addRowTowfeeTable(data);
		});
		document.getElementById('wfexeexps').value = JSON.stringify(wfexeexppressions);
	}
}

function addRowTowfeeTable(data) {
	var numrow = document.getElementById('wfexeexptbl').rows.length;
	var tbody = document.querySelector('#wfexeexptbl > tbody');
	var template = document.getElementById('wfexprow');

	// Clone the new row and insert it into the table
	var clone = template.content.cloneNode(true);
	var td = clone.querySelectorAll('td');
	td[0].querySelector('input').id = 'wfeeexp0'+numrow;
	td[2].querySelector('input').id = 'wfeeexp0'+numrow+'_type';
	td[2].querySelector('button').id = 'wfeeexp0'+numrow+'_button';
	if (data!=null) {
		td[0].querySelector('input').value = data.exp;
		td[1].querySelector('input').value = data.var;
		td[2].querySelector('input').value = data.typ;
	}
	tbody.appendChild(clone);
	document.getElementById('wfeeexp0'+numrow+'_button').innerHTML=`<svg class="slds-button__icon" aria-hidden="true">
<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
</svg>
<span class="slds-assistive-text">${i18nDelete}</span>`;
}

function wfeeeditFieldExpression(fieldValueNode, fieldType) {
	editpopupobj.edit(fieldValueNode.prop('id'), fieldValueNode.val(), fieldType);
}

document.addEventListener('DOMContentLoaded', function (event) {
	jQuery('#editpopup').draggable({ handle: '#editpopup_draghandle' });
	editpopupobj = fieldExpressionPopup(moduleName, $);
	editpopupobj.setModule(moduleName);
	editpopupobj.close();
	setwfexetable();
});
