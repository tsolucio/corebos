/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function star(id, starred) {
	location.href = 'index.php?module=Rss&action=Star&record='+id+'&starred='+starred;
	var elem = document.getElementById('star-'+id);
	if (elem.src.indexOf('onstar.gif') != -1) {
		elem.src = 'themes/images/offstar.gif';
	} else {
		elem.src = 'themes/images/onstar.gif';
	}
}
function getRequest() {
	if ( !httpRequest ) {
		httpRequest = new XMLHttpRequest();
	}
	return httpRequest;
}
function makeRequest(targetUrl) {
	var httpRequest = getRequest();
	httpRequest.open('GET', targetUrl, false, false, false);
	httpRequest.send('');
	switch (httpRequest.status) {
	case 200:
		return httpRequest.responseText;
		break;
	default:
		alert(alert_arr.PROBLEM_ACCESSSING_URL+targetUrl+alert_arr.CODE+httpRequest.status);
		return null;
		break;
	}
}
function verify_data(form) {
	var isError = false;
	var errorMessage = '';
	if (trim(form.rssurl.value) == '') {
		isError = true;
		errorMessage += '\nRSS Feed URL';
	}
	// Here we decide whether to submit the form.
	if (isError == true) {
		alert(alert_arr.MISSING_REQUIRED_FIELDS + errorMessage);
		return false;
	}
	return true;
}

function display(url, id) {
	document.getElementById('rsstitle').innerHTML = document.getElementById(id).innerHTML;
	document.getElementById('mysite').src = url;
}

function GetRssFeedList(id) {
	document.getElementById('status').style.display='inline';
	jQuery.ajax({
		method:'POST',
		url:'index.php?module=Rss&action=RssAjax&file=ListView&directmode=ajax&record='+id
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		document.getElementById('rssfeedscont').innerHTML=response;
	});
}

function DeleteRssFeeds(id) {
	if (id != '') {
		if (confirm(i18n_DELETE_RSSFEED_CONFIRMATION)) {
			show('status');
			var feed = 'feed_'+id;
			document.getElementById(feed).parentNode.removeChild(document.getElementById(feed));
			jQuery.ajax({
				method:'POST',
				url:'index.php?module=Rss&return_module=Rss&action=RssAjax&file=Delete&directmode=ajax&record='+id,
			}).done(function (response) {
				document.getElementById('status').style.display='none';
				document.getElementById('rssfeedscont').innerHTML=response;
				document.getElementById('mysite').src = '';
				document.getElementById('rsstitle').innerHTML = '&nbsp';
			});
		}
	} else {
		alert(alert_arr.LBL_NO_FEEDS_SELECTED);
	}
}

function SaveRssFeeds() {
	document.getElementById('status').style.display='inline';
	var rssurl = document.getElementById('rssurl').value;
	rssurl = rssurl.replace(/&/gi, '##amp##');
	jQuery.ajax({
		method:'POST',
		url:'index.php?module=Rss&action=RssAjax&file=Popup&directmode=ajax&rssurl='+rssurl
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		if (isNaN(parseInt(response))) {
			var rrt = response;
			document.getElementById('temp_alert').innerHTML = rrt;
			removeHTMLTags();
			document.getElementById('rssurl').value = '';
		} else {
			GetRssFeedList(response);
			getrssfolders();
			document.getElementById('rssurl').value = '';
			document.getElementById('PopupLay').style.display='none';
		}
	});
}
function makedefaultRss(id) {
	if (id != '') {
		document.getElementById('status').style.display='inline';
		jQuery.ajax({
			method:'POST',
			url:'index.php?module=Rss&action=RssAjax&file=Popup&directmode=ajax&record='+id
		}).done(function (response) {
			document.getElementById('status').style.display='none';
			getrssfolders();
		});
	}
}
function getrssfolders() {
	jQuery.ajax({
		method:'POST',
		url:'index.php?module=Rss&action=RssAjax&file=ListView&folders=true'
	}).done(function (response) {
		document.getElementById('status').style.display='none';
		document.getElementById('rssfolders').innerHTML=response;
	});
}

function removeHTMLTags() {
	if (document.getElementById && document.getElementById('temp_alert')) {
		var strInputCode = document.getElementById('temp_alert').innerHTML;
		var strTagStrippedText = strInputCode.replace(/<\/?[^>]+(>|$)/g, '');
		alert('Output Message:\n' + strTagStrippedText);
	}
}
