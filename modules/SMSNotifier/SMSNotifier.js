/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
if(typeof(SMSNotifier) == 'undefined') {
	var SMSNotifier = {

		checkstatus : function(record) {
			SMSNotifier.loadstatus(record, true);
		},

		loadstatus : function(record, query) {
			var wrapper = document.getElementById('tblStatusInformation');

			if(typeof(query) == 'undefined') query = false;

			if(wrapper) {
				var url = 'module=SMSNotifier&action=SMSNotifierAjax&ajax=true&file=SMSNotifierStatusWidget&record=' + record;
				if(query) {
					url += '&mode=query';
				}
				document.getElementById('vtbusy_info').style.display="block";
				jQuery.ajax({
					method: 'POST',
					url: 'index.php?'+url,
				}).done(function (response) {
					wrapper.innerHTML = response;
					document.getElementById('vtbusy_info').style.display="none";
				});
			}
		}
	};
}