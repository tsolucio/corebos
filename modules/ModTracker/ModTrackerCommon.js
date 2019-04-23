/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

var ModTrackerCommon = {
	OVERLAYID : '__ModTrackerCommonOverlay__',

	initOverlay : function () {
		if (document.getElementById(ModTrackerCommon.OVERLAYID)) {
			return;
		}
		var overlaynode = document.createElement('div');
		overlaynode.id = ModTrackerCommon.OVERLAYID;
		overlaynode.style.width = '550px';
		overlaynode.style.display = 'none';
		document.body.appendChild(overlaynode);
	},

	showdiff : function (record, atpoint, highlight) {
		ModTrackerCommon.initOverlay();

		if ( typeof (atpoint) == 'undefined') {
			atpoint = 0;
		}
		if ( typeof (highlight) == 'undefined') {
			highlight = true;
		}

		document.getElementById('status').style.display='inline';
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=ModTracker&action=ModTrackerAjax&file=ShowDiff&id=' + encodeURIComponent(record) + '&atpoint=' + encodeURIComponent(atpoint) + '&highlight=' + encodeURIComponent(highlight),
		}).done(function (response) {
			document.getElementById('status').style.display='none';
			document.getElementById(ModTrackerCommon.OVERLAYID).style.display='inline';
			document.getElementById(ModTrackerCommon.OVERLAYID).innerHTML = response;
			document.getElementById(ModTrackerCommon.OVERLAYID).style.display = 'block';
			placeAtCenter(document.getElementById(ModTrackerCommon.OVERLAYID));
		});
	},

	showhistory : function (record, atpoint, highlight) {
		ModTrackerCommon.initOverlay();

		if ( typeof (atpoint) == 'undefined') {
			atpoint = 0;
		}
		if ( typeof (highlight) == 'undefined') {
			highlight = false;
		}

		document.getElementById('status').style.display='inline';
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=ModTracker&action=ModTrackerAjax&file=ShowDiff&mode=history&id=' + encodeURIComponent(record) + '&atpoint=' + encodeURIComponent(atpoint) + '&highlight=' + encodeURIComponent(highlight),
		}).done(function (response) {
			document.getElementById('status').style.display='none';
			document.getElementById(ModTrackerCommon.OVERLAYID).innerHTML = response;
			document.getElementById(ModTrackerCommon.OVERLAYID).style.display = 'block';
			placeAtCenter(document.getElementById(ModTrackerCommon.OVERLAYID));
		});
	},
	hide : function () {
		document.getElementById(ModTrackerCommon.OVERLAYID).style.display='none';
	}
};
