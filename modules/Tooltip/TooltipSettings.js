/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/

/**
 * this function takes a fieldname and returns the fields related to it
 */
function getRelatedFieldInfo(id){
	var modulename = $('pick_module').value;

	var fieldname = id.options[id.options.selectedIndex].value;
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Tooltip&action=TooltipAjax&file=EditQuickView&field_name='+fieldname+'&module_name='+modulename+'&parenttab=Settings&ajax=true',
			onComplete: function(response) {
				if(response.responseText == false){
					alert(alert_arr.ERR_FIELD_SELECTION);
				}else{
					var related_fields = response.responseText;
					$('fieldList').innerHTML = related_fields;
				}
			}
		}
	);
}

/**
 * this function saves the tooltip related information in the database using an ajax call
 */
function saveTooltipInformation(fieldid, checkedFields){
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Tooltip&action=TooltipAjax&file=SaveTooltipInformation&fieldid='+fieldid+'&checkedFields='+checkedFields+'&parenttab=Settings&ajax=true',
			onComplete: function(response) {
				if(response.responseText == "FAILURE"){
					alert(alert_arr.ERR_FIELD_SELECTION);
					return false;
				}else{
					//success
					var div = document.getElementById('fieldList');
					div.innerHTML = response.responseText;
				}
			}
		}
	);
}

/**
 * this function saves the tooltip
 */
function doSaveTooltipInfo(){
	var fieldid = document.getElementById('fieldid').value;
	var div = document.getElementById('fieldList');
	var fields = div.getElementsByTagName('input');
	var checkedFields = [];
	
	for(var i=0, j=0;i<fields.length;i++){
		if(fields[i].type == "checkbox" && fields[i].checked == true){
			checkedFields[j++] = fields[i].value;
		}
	}
	relatedFields = checkedFields.join(",");
	saveTooltipInformation(fieldid, relatedFields);
}

/**
 * this function takes a fieldid and displays the quick editview for that field
 */
function displayEditView(){
	var node = document.getElementById('pick_field');
	getRelatedFieldInfo(node);
}
