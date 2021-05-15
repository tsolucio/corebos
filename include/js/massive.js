function postListViewSelection(module, action) {
	var method = 'post'; // Set method to post by default, if not specified.
	var idlist = document.getElementById('allselectedboxes').value;
	var excludedRecords = document.getElementById('excludedRecords').value;
	var viewid = getviewId();
	var formodule = document.getElementById('curmodule').value;

	if (idlist=='') {
		alert(alert_arr.SELECT);
		return;
	}

	var params = {
		'module': module,
		'action': action,
		'idlist': idlist,
		'viewname' : viewid,
		'excludedRecords' : excludedRecords,
		'formodule' : formodule,
		'__vt5rftk': csrfMagicToken
	};

	// The rest of this code assumes you are not using a library.
	// It can be made less wordy if you use one.
	var form = document.createElement('form');
	form.setAttribute('method', method);
	form.setAttribute('action', 'index.php');
	for (var key in params) {
		var hiddenField = document.createElement('input');
		hiddenField.setAttribute('type', 'hidden');
		hiddenField.setAttribute('name', key);
		hiddenField.setAttribute('value', params[key]);
		form.appendChild(hiddenField);
	}
	document.body.appendChild(form);
	form.submit();
}

function massPrint(module) {
	var select_options = document.getElementById('allselectedboxes').value;
	var x = select_options.split(';');
	var searchurl= document.getElementById('search_url').value;
	var count=x.length;
	var viewid =getviewId();
	var pagenumber= document.getElementsByName('pagenum')[0].value;
	var idstring = '';
	if (count > 1) {
		document.getElementById('idlist').value=select_options;
		idstring = select_options;
	} else {
		alert(alert_arr.SELECT);
		return false;
	}
	// we have to decrese the count value by 1 because when we split
	// with semicolon we will get one extra count
	count = count - 1;
	if (module=='SalesOrder') {
		location.href = 'index.php?module='+module+'&action=CreateSOPDF&return_action=index&return_module='+module+'&idlist='+idstring+'&search_url='+searchurl+'&viewname='+viewid+'&pagenumber='+pagenumber;
	} else {
		location.href = 'index.php?module='+module+'&action=CreatePDF&return_action=index&return_module='+module+'&idlist='+idstring+'&search_url='+searchurl+'&viewname='+viewid+'&pagenumber='+pagenumber;
	}
}

function showMassTag() {
	if (document.getElementById('allids')!=null && document.getElementById('allselectedboxes').value=='') {
		alert(alert_arr.SELECT);
		return;
	}
	document.getElementById('ids').value = document.getElementById('allselectedboxes').value;
	document.getElementById('masstag').style.display = 'block';
}
