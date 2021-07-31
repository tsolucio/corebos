/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************/

function addRowToContextTable(key ='', value = '') {
	const numrow = document.getElementById('context_rows').rows.length;
	const tr = document.createElement('tr');
	tr.id = `row-${numrow}`;
	const td0 = tr.insertCell(0);
	td0.innerHTML = `
	<div class="slds-truncate">
		<input type="text" class="slds-input" name="context_variable" value=${key}>
	</div>`;
	const td2 = tr.insertCell(1);
	td2.innerHTML = `
	<div class="slds-truncate">
		<input type="text" class="slds-input" name="context_value" value=${value}>
	</div>`;
	const td3 = tr.insertCell(2);
	td3.innerHTML = `
	<a onclick="deleteContextRow(${numrow})" id="delete-${numrow}">
	<svg class="slds-button__icon" aria-hidden="true">
		<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
	</svg>
	</a>`;
	document.getElementById('context_rows').appendChild(tr);
}

function deleteContextRow(rowid) {
	const row = document.getElementById(`row-${rowid}`);
	row.parentNode.removeChild(row);
	resetRowId();
}

function resetRowId() {
	const numrow = document.getElementById('context_rows').rows.length;
	let j = 0;
	for (let i = 0; i < numrow + 1; i++) {
		const row = document.getElementById(`row-${i}`);
		const delete_row = document.getElementById(`delete-${i}`);
		if (row) {
			row.id = `row-${j}`;
			delete_row.id = `delete-${j}`;
			delete_row.removeAttribute('onclick');
			delete_row.setAttribute('onclick', `deleteContextRow(${j})`);
			j++;
		}
	}
}

function export_results() {
	const qtype = document.getElementById('qtype').value;
	const qsqlqry = (document.getElementById('sqlquery').checked ? '1' : '0');
	const context_var = document.getElementsByName('context_variable');
	const context_val = document.getElementsByName('context_value');
	let context_data = Array();
	for (var i = 0; i < context_var.length; i++) {
		const variable = context_var[i].value;
		const value = context_val[i].value;
		context_data.push({
			variable: variable,
			value: value,
		});
	}
	const bqname = document.getElementById('bqname').value;
	const bqmodule = document.getElementById('bqmodule').value;
	const issqlwsq_disabled = (document.getElementById('checkboxsqlwsq').checked ? true : false);
	const recordid = document.getElementById('record').value;
	let cbq = JSON.stringify({
		'qname': bqname,
		'qtype': qtype,
		'qmodule': document.getElementById('bqmodule').value,
		'qpagesize': document.getElementById('qpagesize').value,
		//'qcolumns': (qsqlqry=='1' ? document.getElementById('bqsql').value : (qtype=='Mermaid' ? document.getElementById('bqwsq').value : getSQLSelect())),
		'qcolumns': (qsqlqry=='1' ? document.getElementById('bqsqlcoulumns').value : (qtype=='Mermaid' ? document.getElementById('bqwsq').value : getSQLSelect())),
		//'qcondition': (qtype=='Mermaid' ? '' : getSQLConditions()),
		'qcondition': (qtype=='Mermaid' ? '' : (issqlwsq_disabled ? document.getElementById('bqsqlconditions').value : getSQLConditions())),
		'orderby': getSQLOrderBy().substr(9),
		'groupby': getSQLGroupBy().substr(9),
		'typeprops': document.getElementById('qprops').value,
		'sqlquery': qsqlqry,
		'condfilterformat': '0',
		'context_variable': context_data,
		'issqlwsq_disabled': issqlwsq_disabled,
		'record_id': recordid
	});
	const evaluatewith = document.getElementById('evaluatewith').value;
	let cbqctx = '';
	if (evaluatewith!=0 && evaluatewith!='') {
		cbqctx = JSON.stringify({
			'RECORDID': evaluatewith,
			'MODULE': document.getElementById('evaluatewith_type').value
		});
	}
	document.getElementById('export_text').innerHTML = mod_alert_arr.Exporting;
	const columns = getDataColumns();
	let headers = [];
	columns.forEach(cinfo => {
		headers.push({
			field: cinfo.header,
			module: bqmodule
		});
	});
	const headerflds = JSON.stringify({
		headers: headers
	});
	fetch(
		'index.php?module=cbQuestion&action=cbQuestionAjax&actionname=qactions&method=exportBuilderData',
		{
			method: 'post',
			headers: {
				'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			credentials: 'same-origin',
			body: '&'+csrfMagicName+'='+csrfMagicToken+'&cbQuestionRecord='+encodeURIComponent(cbq)+'&cbQuestionContext='+encodeURIComponent(cbqctx)+'&bqname='+bqname+'&columns='+encodeURIComponent(headerflds)
		}
	).then(response => response.json()).then(response => {
		document.getElementById('export_text').innerHTML = mod_alert_arr.export_results;
		window.open(`cache/${response}.csv`, '_blank');
	});
}