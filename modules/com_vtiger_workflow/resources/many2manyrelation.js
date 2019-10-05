let fileurl = 'module=com_vtiger_workflow&action=com_vtiger_workflowAjax&file=getrelatedmods&currentmodule='+moduleName;
$(document).ready(function () {
	jQuery.ajax({
		method: 'GET',
		url: 'index.php?' + fileurl
	}).done(function (modlistres) {
		document.getElementById('relModlist_type').innerHTML =modlistres;
	});
});

function changeIdlistVal(recval) {
	var idinputval = document.getElementById('idlist').value;
	if (document.getElementById('checkbox-'+recval).checked) {
		document.getElementById('idlist').value = (idinputval == '') ? recval : idinputval+','+recval;
	} else {
		var idlistvals=idinputval.split(',');
		for (var i = 0; i < idlistvals.length; i++) {
			if (idlistvals[i] == recval) {
				idlistvals.splice(i, 1);
				document.getElementById('idlist').value = idlistvals;
			}
		}
	}
}

function doGlobalGridSelect() {
	let ops = document.querySelectorAll("input[name='options[]']");
	let globalcheck = document.getElementById('checkbox-0').checked;
	let idlist = '';
	for (let op = 0; op < ops.length; ++op) {
		ops[op].checked = globalcheck;
		if (globalcheck) {
			if (idlist=='') {
				idlist = ops[op].value;
			} else {
				idlist += ','+ops[op].value;
			}
		}
	}
	document.getElementById('idlist').value = idlist;
}

function addrecList(recId, recvalue) {
	var reclist ='<tr id="row-'+recId+'" aria-level="1" aria-posinset="1" aria-selected="false" aria-setsize="4">'+
		'<td class="slds-text-align_right" role="gridcell" style="width: 3.25rem;">'+
		'<div class="slds-checkbox">'+
			'<input type="checkbox" onclick="changeIdlistVal('+recId+')" name="options[]" value='+recId+' id="checkbox-'+recId+'" aria-labelledby="check-button-label-04 column-group-header" value="checkbox-04" checked />'+
			'<label class="slds-checkbox__label" for="checkbox-'+recId+'" id="check-button-label-04">'+
			'<span class="slds-checkbox_faux"></span>'+
			'<span class="slds-form-element__label slds-assistive-text">Select item 4</span>'+
			'</label>'+
		'</div>'+
		'</td>'+
		'<td class="slds-tree__item" data-label="Entity Name" scope="row">'+
			'<div class="slds-truncate">'+(document.getElementById('radio-5').checked ? '*' : recvalue)+'</div>'+
		'</td>'+
		'<td data-label="Entity" role="gridcell" style="width: 18rem;">'+
			'<div class="slds-truncate">'+document.getElementById('relModlist_type').value+'</div>'+
		'</td>';
	return reclist;
}

jQuery(document).ready(function () {
	var recsavedDiv='';
	if (relrecords.length > 0) {
		for (var i=0; i<relrecords.length; i++) {
			var reclist ='<tr id="row-'+relrecords[i].recid+'" aria-level="1" aria-posinset="1" aria-selected="false" aria-setsize="4">'+
				'<td class="slds-text-align_right" role="gridcell" style="width: 3.25rem;">'+
				'<div class="slds-checkbox">'+
					'<input type="checkbox" onclick="changeIdlistVal('+relrecords[i].recid+')" name="options[]" value='+relrecords[i].recid+' id="checkbox-'+relrecords[i].recid+'" aria-labelledby="check-button-label-04 column-group-header" value="checkbox-04" checked />'+
					'<label class="slds-checkbox__label" for="checkbox-'+relrecords[i].recid+'" id="check-button-label-04">'+
					'<span class="slds-checkbox_faux"></span>'+
					'<span class="slds-form-element__label slds-assistive-text">Select item 4</span>'+
					'</label>'+
				'</div>'+
				'</td>'+
				'<td class="slds-tree__item" data-label="Entity Name" scope="row">'+
					'<div class="slds-truncate">'+(document.getElementById('radio-5').checked ? '*' : relrecords[i].entityName)+'</div>'+
				'</td>'+
				'<td data-label="Entity" role="gridcell" style="width: 18rem;">'+
					'<div class="slds-truncate">'+relrecords[i].entityType+'</div>'+
				'</td>';
			recsavedDiv += reclist;
		}
		document.getElementById('selected_recordsDiv').innerHTML = recsavedDiv;
	}
});