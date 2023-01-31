/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

function cbqdowork(work, qid) {
	fetch(
		'index.php?module=cbQuestion&action=cbQuestionAjax&actionname=qactions&method='+work+'&qid='+qid,
		{
			method: 'post',
			headers: {
				'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			credentials: 'same-origin',
			body: '&'+csrfMagicName+'='+csrfMagicToken
		}
	).then(response => response.json()).then(response => {
		document.getElementById('appnotifydiv').outerHTML = response.notify;
		document.getElementById('appnotifydiv').style.display='block';
	});
}

function cbqgetsql(qid) {
	return fetch(
		'index.php?module=cbQuestion&action=cbQuestionAjax&actionname=qactions&method=getSQL&qid='+qid,
		{
			method: 'post',
			headers: {
				'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			credentials: 'same-origin',
			body: '&'+csrfMagicName+'='+csrfMagicToken
		}
	).then(response => response.json());
}

function cbqtestsql(qid) {
	cbqdowork('testSQL', qid);
}

function cbqcreatemap(qid) {
	cbqdowork('createMap', qid);
}

function cbqcreateview(qid) {
	cbqdowork('createView', qid);
}

function cbqcreatemview(qid) {
	cbqdowork('createMView', qid);
}

function cbqremovemview(qid) {
	cbqdowork('removeMView', qid);
}

function cbqaddmviewcron(qid) {
	cbqdowork('addMViewCron', qid);
}

function cbqdelmviewcron(qid) {
	cbqdowork('delMViewCron', qid);
}

function cbqaddmviewwf(qid) {
	cbqdowork('addMViewWF', qid);
}

function cbqdelmviewwf(qid) {
	cbqdowork('delMViewWF', qid);
}

function cbqexecutescript(tablename, qid, script_path) {
	var params = '&tablename='+tablename+'&qid='+qid+'&script_path='+script_path;
	fetch(
		'index.php?module=cbQuestion&action=cbQuestionAjax&actionname=qactions&method=executeScript'+params,
		{
			method: 'post',
			headers: {
				'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			credentials: 'same-origin',
			body: '&'+csrfMagicName+'='+csrfMagicToken
		}
	).then(response => response.json()).then(response => {
		document.getElementById('appnotifydiv').outerHTML = response.notify;
		document.getElementById('appnotifydiv').style.display='block';
	});
}

window.addEventListener("DOMContentLoaded",(e) => {
	const el = document.getElementById("querytype");
	el.addEventListener("change", function(e){
	if(el.value == 'Direct Sql'){
		const txtcolumn = document.getElementById("qcondition");
		const txtorderby = document.getElementById("orderby");
		const txtgroupby = document.getElementById("groupby");
		txtcolumn.disabled = true;
		txtorderby.disabled = true;
		txtgroupby.disabled = true;
		txtcolumn.setAttribute("class", "blury");
		txtcolumn.style.backgroundColor = "#D3D3D3";
		txtorderby.setAttribute("class", "blury");
		txtorderby.style.backgroundColor = "#D3D3D3";
		txtgroupby.setAttribute("class", "blury");
		txtgroupby.style.backgroundColor = "#D3D3D3";
	}
});
})

