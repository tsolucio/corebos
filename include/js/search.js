/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
********************************************************************************/

function searchshowhide(argg, argg2) {
	var ele_x = document.getElementById(argg);
	var ele_y = document.getElementById(argg2);
	var x, y;
	if (ele_x != null) {
		x = ele_x.style;
	}
	if (ele_y != null) {
		y = ele_y.style;
	}
	if (x != null && x.display=='none' && (y == null || y.display=='none')) {
		x.display='block';
	} else {
		if (y != null) {
			y.display='none';
		}
		if (x != null) {
			x.display='none';
		}
	}
}

function searchhide(argg, argg2) {
	var ele_x = document.getElementById(argg);
	var ele_y = document.getElementById(argg2);
	var x, y;
	if (ele_x != null) {
		x = ele_x.style;
	}
	if (ele_y != null) {
		y = ele_y.style;
	}
	if (y != null) {
		y.display='none';
	}
	if (x != null) {
		x.display='none';
	}
}

function displayModuleList(selectmodule_view) {
	for (var i=0; i < selectmodule_view.options.length; i++) {
		if (selectmodule_view.options[i].value=='All') {
			continue;
		}
		var mod = 'global_list_'+selectmodule_view.options[i].value;
		if (selectmodule_view.value == 'All') {
			show(mod);
		} else {
			hide(mod);
		}
	}
	if (selectmodule_view.value != 'All') {
		show('global_list_'+selectmodule_view.value);
	}
}
