/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
if (typeof(ModCommentsCommon) == 'undefined') {
	ModCommentsCommon = {
		addComment : function (domkeyid, parentid) {
			var textBoxField = document.getElementById('txtbox_'+domkeyid);
			//var editareaDOM = document.getElementById('editarea_'+domkeyid);
			var contentWrapDOM = document.getElementById('contentwrap_'+domkeyid);
			if (textBoxField.value == '') {
				return;
			}

			var url = 'module=ModComments&action=ModCommentsAjax&file=DetailViewAjax&ajax=true&ajxaction=WIDGETADDCOMMENT&parentid='+encodeURIComponent(parentid);

			VtigerJS_DialogBox.block();
			VtigerJS_DialogBox.showbusy();

			jQuery.ajax({
				method: 'POST',
				data : {'comment': textBoxField.value},
				url: 'index.php?'+url,
			}).done(function (response) {
				VtigerJS_DialogBox.hidebusy();
				VtigerJS_DialogBox.unblock();
				var responseTextTrimmed = trim(response);
				if (responseTextTrimmed.substring(0, 10) == ':#:SUCCESS') {
					textBoxField.value = '';
					contentWrapDOM.innerHTML = responseTextTrimmed.substring(10)+contentWrapDOM.innerHTML;
				} else {
					alert(alert_arr.OPERATION_DENIED);
				}
			});
		},
		reloadContentWithFiltering : function (widget, parentid, criteria, targetdomid, indicator) {
			if (document.getElementById(indicator)) {
				document.getElementById(indicator).style.display='inline';
			}

			var url = 'module=ModComments&action=ModCommentsAjax&file=ModCommentsWidgetHandler&ajax=true';
			url += '&widget=' + encodeURIComponent(widget) + '&parentid='+encodeURIComponent(parentid);
			url += '&criteria='+ encodeURIComponent(criteria);
			jQuery.ajax({
				method: 'POST',
				url: 'index.php?'+url,
			}).done(function (response) {
				if (document.getElementById(indicator)) {
					document.getElementById(indicator).style.display='none';
				}
				if (document.getElementById(targetdomid)) {
					document.getElementById(targetdomid).innerHTML = response;
				}
			});
		}
	};
}
