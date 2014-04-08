/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/

function moveUp(moduleObj,sequence) {
	var oldSequence = moduleObj[sequence]['sequence'];
	var changeSequence  = oldSequence - 1;
	
	moduleObj[sequence]['sequence'] = moduleObj[changeSequence]['sequence'];
	moduleObj[changeSequence]['sequence']=oldSequence;

	temp = new Object();
	temp=moduleObj[sequence];
	moduleObj[sequence]=moduleObj[changeSequence];
	moduleObj[changeSequence]=temp;

	renderModuleSettings(moduleObj);
}

function moveDown(moduleObj,sequence) {
	var oldSequence = moduleObj[sequence]['sequence'];
	var changeSequence  = parseInt(oldSequence) + 1;

	moduleObj[sequence]['sequence'] = moduleObj[changeSequence]['sequence'];
	moduleObj[changeSequence]['sequence']=oldSequence;

	temp = new Object();
	temp=moduleObj[sequence];
	moduleObj[sequence]=moduleObj[changeSequence];
	moduleObj[changeSequence]=temp;
	renderModuleSettings(moduleObj);
}

Object.prototype.size = function () {
	var len = this.length ? --this.length : -1;
	for (var k in this)
	len++;
	return len;
}

function visibleValueChange(sequence,tabid,moduleObj) {
	if(moduleObj[sequence]['sequence'] == sequence && moduleObj[sequence]['tabid'] == tabid){
		if(moduleObj[sequence]['visible'] == 1)
			moduleObj[sequence]['visible'] = '0';
		else
			moduleObj[sequence]['visible'] = '1';
		}
}

function prefValueChange(sequence,tabid,moduleObj) {
	if(moduleObj[sequence]['sequence'] == sequence && moduleObj[sequence]['tabid'] == tabid){
		if(moduleObj[sequence]['value'] == 1)
			moduleObj[sequence]['value'] = '0';
		else
			moduleObj[sequence]['value'] = '1';
		}
}
