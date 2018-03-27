/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
********************************************************************************/

//added to hide date selection option, if a user doesn't have permission on standard filter column
function standardFilterDisplay() {
	if (getObj("stdDateFilterField")) {
		if (document.CustomView.stdDateFilterField.selectedIndex > -1 && document.CustomView.stdDateFilterField.options[document.CustomView.stdDateFilterField.selectedIndex].value == "not_accessible") {
			getObj('stdDateFilter').disabled = true;
			getObj('startdate').disabled = true;
			getObj('enddate').disabled = true;
			getObj('jscal_trigger_date_start').style.visibility="hidden";
			getObj('jscal_trigger_date_end').style.visibility="hidden";
		} else {
			getObj('stdDateFilter').disabled = false;
			getObj('startdate').disabled = false;
			getObj('enddate').disabled = false;
			getObj('jscal_trigger_date_start').style.visibility="visible";
			getObj('jscal_trigger_date_end').style.visibility="visible";
		}
	}
}