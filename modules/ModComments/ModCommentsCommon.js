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
		addComment : function(domkeyid, parentid) {
			var textBoxField = $('txtbox_'+domkeyid);
			var editareaDOM = $('editarea_'+domkeyid);
			var contentWrapDOM = $('contentwrap_'+domkeyid);
			if (textBoxField.value == '') {
				return;
			}
			
			var url = 'module=ModComments&action=ModCommentsAjax&file=DetailViewAjax&ajax=true&ajxaction=WIDGETADDCOMMENT&parentid='+encodeURIComponent(parentid);
			url += '&comment=' + encodeURIComponent(textBoxField.value);
			
			VtigerJS_DialogBox.block();
			$("vtbusy_info").style.display="inline"; 
			 
			new Ajax.Request('index.php',{
				queue: {position: 'end', scope: 'command'},
				method: 'post',
				postBody:url,
				onComplete: function(response) {
					 $("vtbusy_info").style.display="none"; 
					VtigerJS_DialogBox.unblock();
					
					var responseTextTrimmed = trim(response.responseText);
					if (responseTextTrimmed.substring(0, 10) == ':#:SUCCESS') {
						textBoxField.value = '';
						contentWrapDOM.innerHTML = responseTextTrimmed.substring(10)+contentWrapDOM.innerHTML;
					} else {
						alert(alert_arr.OPERATION_DENIED);
					}
				}}
			);
		},
		reloadContentWithFiltering : function(widget, parentid, criteria, targetdomid, indicator) {
			if($(indicator)) $(indicator).style.display="inline";
			
			var url = 'module=ModComments&action=ModCommentsAjax&file=ModCommentsWidgetHandler&ajax=true';
			url += '&widget=' + encodeURIComponent(widget) + '&parentid='+encodeURIComponent(parentid);
			url += '&criteria='+ encodeURIComponent(criteria);
			
			new Ajax.Request('index.php',{
				queue: {position: 'end', scope: 'command'},
				method: 'post',
				postBody:url,
				onComplete: function(response) {
					if($(indicator)) $(indicator).style.display="none";
					
					if($(targetdomid)) $(targetdomid).innerHTML = response.responseText;
				}}
			);
		}
	}
}

