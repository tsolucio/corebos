/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************/

/**
 * Generic uitype popup selection handler
 */
function vtlib_setvalue_from_popup(recordid, value, target_fieldname, formname, currentID = 0) {
	var ret = false;
	var wodform = false;
	if (formname == 'ListView') {
		var domnode_id = window.opener.document.getElementById(`txtbox_${target_fieldname}_${currentID}`);
		var domnode_display = window.opener.document.getElementById(`txtbox_${target_fieldname}_${currentID}_display`);
		domnode_id.value = recordid;
		domnode_display.innerHTML = value;
		return true;
	}
	if (window.opener.document.forms[formname]) {
		wodform = window.opener.document.forms[formname];
		ret = true;
	} else if (window.opener.document.QcEditView) {
		wodform = window.opener.document.QcEditView;
		ret = true;
	} else if (window.opener.document.EditView) {
		wodform = window.opener.document.EditView;
		ret = true;
	} else if (window.opener.document.DetailView) {
		wodform = window.opener.document.DetailView;
		ret = true;
	}
	if (ret) {
		var domnode_id = wodform[target_fieldname];
		if (!domnode_id) {
			domnode_id = window.opener.document.getElementById('txtbox_'+ target_fieldname);
		}
		var domnode_display = wodform[target_fieldname+'_display'];
		if (!domnode_display) {
			domnode_display = window.opener.document.getElementById(target_fieldname+'_display');
		}
		if (domnode_id) {
			domnode_id.value = recordid;
			domnode_id.dispatchEvent(new Event('change'));
		}
		if (domnode_display) {
			domnode_display.value = value;
		}
	}
	var func = window.opener.gVTModule + 'setValueFromCapture';
	if (typeof window.opener[func] == 'function') {
		window.opener[func](recordid, value, target_fieldname);
		ret = true;
	}
	return ret;
}

/*
 * Generic uitype popup open action
 */
function vtlib_open_popup_window(fromlink, fldname, MODULE, ID) {
	var modfld = document.getElementById(fldname+'_type');
	var mod = '';
	if (modfld) {
		mod = modfld.value;
	} else {
		if (fromlink == 'qcreate') {
			modfld = document.QcEditView[fldname+'_type'];
		} else if (fromlink != '') {
			modfld = document.forms[fromlink][fldname+'_type'];
		} else {
			modfld = document.EditView[fldname+'_type'];
		}
		if (modfld) {
			mod = modfld.value;
		} else {
			mod = '';
		}
	}
	if (fromlink == 'qcreate') {
		window.open('index.php?module='+ mod +'&action=Popup&html=Popup_picker&form=vtlibPopupView&forfield='+fldname+'&srcmodule='+MODULE+'&forrecord='+ID, 'vtlibui10qc', cbPopupWindowSettings);
	} else if (fromlink != '') {
		window.open('index.php?module='+ mod +'&action=Popup&html=Popup_picker&form='+fromlink+'&forfield='+fldname+'&srcmodule='+MODULE+'&forrecord='+ID, 'vtlibui10', cbPopupWindowSettings);
	} else {
		window.open('index.php?module='+ mod +'&action=Popup&html=Popup_picker&form=vtlibPopupView&forfield='+fldname+'&srcmodule='+MODULE+'&forrecord='+ID, 'vtlibui10', cbPopupWindowSettings);
	}
	return true;
}

/**
 * Show the vtiger field help if available.
 */
function vtlib_field_help_show(basenode, fldname) {
	var domnode = jQuery('#vtlib_fieldhelp_div');

	if (typeof(fieldhelpinfo) == 'undefined') {
		return;
	}

	var helpcontent = fieldhelpinfo[fldname];
	if (typeof(helpcontent) == 'undefined') {
		return;
	}

	if (domnode.length==0) {
		domnode = document.createElement('div');
		domnode.id = 'vtlib_fieldhelp_div';
		domnode.className = 'slds-popover slds-popover_tooltip slds-popover__body';
		domnode.style.position = 'absolute';
		domnode.style.color = 'white';
		domnode.style.fontWeight = 'bold';
		document.body.appendChild(domnode);
		domnode = jQuery('#vtlib_fieldhelp_div');
		domnode.on('mouseenter', function () {
			jQuery(this).show();
		});
		domnode.on('mouseleave', vtlib_field_help_hide);
	} else {
		domnode.show();
	}
	domnode.html(helpcontent);
	fnvshobj(basenode, 'vtlib_fieldhelp_div');
}

/**
 * Hide the vtiger field help
 */
function vtlib_field_help_hide(evt) {
	var domnode = jQuery('#vtlib_fieldhelp_div');
	if (domnode) {
		domnode.hide();
	}
}

/**
 * Listview Javascript Event handlers API
 *
 * Example:
 * vtlib_listview.register('cell.onmouseover', function(evtparams, moreparams) { console.log(evtparams); }, [10,20]);
 * vtlib_listview.register('cell.onmouseout', function(evtparams) {console.log(evtparams); });
 */
var vtlib_listview = {
	/**
	 * Callback function handlers that needs to be triggered for an event
	 *
	 * _handlers = {
	 *     'event1' : [ [handlerfn11, handlerfn11_moreparams], [handlerfn2, handlerfn12_moreparams] ],
	 *     'event2' : [ [handlerfn21, handlerfn21_moreparams], [handlerfn2, handlerfn22_moreparams] ]
	 * }
	 */
	_handlers : {},

	/**
	 * Register handler function for the event
	 */
	register : function (evttype, handler, callback_params) {
		if (typeof(callback_params) == 'undefined') {
			callback_params = false;
		}
		if (typeof(vtlib_listview._handlers[evttype]) == 'undefined') {
			vtlib_listview._handlers[evttype] = [];
		}
		// Event handlerinfo is an array having (function, optional_more_parameters)
		vtlib_listview._handlers[evttype].push([handler, callback_params]);
	},

	/**
	 * Invoke handler function based on event type
	 */
	invoke_handler : function (evttype, event_params) {
		var evthandlers = vtlib_listview._handlers[evttype];
		if (typeof(evthandlers) == 'undefined') {
			return;
		}
		for (var index = 0; index < evthandlers.length; ++index) {
			var evthandlerinfo = evthandlers[index];
			// Event handlerinfo is an array having (function, optional_more_parameters)
			var evthandlerfn = evthandlerinfo[0];
			if (typeof(evthandlerfn) == 'function') {
				evthandlerfn(event_params, evthandlerinfo[1]);
			}
		}
	},
	getFieldInfo : function (fieldid) {
		var node = document.getElementById(fieldid);
		var innerNodes = node.getElementsByTagName('span');
		var event_params = {};
		if (typeof(innerNodes) != 'undefined') {
			var cellhandler = false;
			for (var index = 0; index < innerNodes.length; ++index) {
				var innerNodeAttrs = innerNodes[index].attributes;
				if (typeof(innerNodeAttrs) != 'undefined' && typeof(innerNodeAttrs.type) != 'undefined' && innerNodeAttrs['type'].nodeValue == 'vtlib_metainfo') {
					cellhandler = innerNodes[index];
					break;
				}
			}
			if (!cellhandler) {
				return;
			}
			event_params = {
				'domnode': node,
				'module' : cellhandler.attributes['vtmodule'].nodeValue,
				'fieldname': cellhandler.attributes['vtfieldname'].nodeValue,
				'recordid': cellhandler.attributes['vtrecordid'].nodeValue
			};
		}
		return event_params;
	},
	/**
	 * Trigger handler function for the event
	 */
	trigger : function (evttype, node) {
		if (evttype == 'cell.onmouseover' || evttype == 'cell.onmouseout' || evttype == 'invoiceasset.onmouseout') {
			// Catch hold of DOM element which has meta inforamtion.
			var innerNodes = node.getElementsByTagName('span');
			if (typeof(innerNodes) != 'undefined') {
				var cellhandler = false;
				for (var index = 0; index < innerNodes.length; ++index) {
					var innerNodeAttrs = innerNodes[index].attributes;
					if (typeof(innerNodeAttrs) != 'undefined' && typeof(innerNodeAttrs.type) != 'undefined' && innerNodeAttrs['type'].nodeValue == 'vtlib_metainfo') {
						cellhandler = innerNodes[index];
						break;
					}
				}
				if (!cellhandler) {
					return;
				}
				var event_params = {
					'event'  : evttype,
					'domnode': node,
					'module' : cellhandler.attributes['vtmodule'].nodeValue,
					'fieldname': cellhandler.attributes['vtfieldname'].nodeValue,
					'recordid': cellhandler.attributes['vtrecordid'].nodeValue
				};
				vtlib_listview.invoke_handler(evttype, event_params);
			}
		}
	}
};

/**
 * DetailView widget loader API
 */
function vtlib_loadDetailViewWidget(urldata, target, indicator) {
	if (typeof(target) == 'undefined') {
		target = false;
	} else {
		target = document.getElementById(target);
	}
	if (typeof(indicator) == 'undefined') {
		indicator = false;
	} else {
		indicator = document.getElementById(indicator);
	}
	if (indicator) {
		indicator.style.display='block';
	}
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?'+urldata
	}).done(function (response) {
		if (target) {
			target.innerHTML = response;
			if (typeof(ParseAjaxResponse)== 'function') {
				ParseAjaxResponse(response);
			} else {
				// Evaluate all the script tags in the response text.
				vtlib_executeJavascriptInElement(target);
			}
			if (indicator) {
				indicator.style.display='none';
			}
		}
	});
	return false; // To stop event propagation
}

function vtlib_executeJavascriptInElement(element) {
	// Evaluate all the script tags in the element.
	var scriptTags = element.getElementsByTagName('script');
	for (var i = 0; i< scriptTags.length; i++) {
		if (scriptTags[i].innerHTML != '') {
			eval(scriptTags[i].innerHTML);
		}
	}
}

/**
 * return themeurl
 *
 */
function vtlib_vtiger_imageurl(theme) {
	return 'themes/'+theme+'/images';
}

function convertArrayOfJsonObjectsToString(arrayofjson) {
	var rdo = '[';
	var len = arrayofjson.length;
	for (var i=0; i < len; i++) {
		rdo = rdo + JSON.stringify(arrayofjson[i])+',';
	}
	rdo = rdo.substring(0, rdo.length-1)+']';
	return rdo;
}

function ExecuteFunctions(functiontocall, params) {
	if (typeof coreBOS_runningUnitTests != 'undefined') {
		return Promise.resolve(true);
	}
	var baseurl = 'index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions';

	// Return a new promise avoiding jquery and prototype
	return new Promise(function (resolve, reject) {
		var url = baseurl+'&functiontocall='+functiontocall;
		var req = new XMLHttpRequest();
		req.open('POST', url, true);  // make call asynchronous

		req.onload = function () {
			// check the status
			if (req.status == 200) {
				// Resolve the promise with the response text
				resolve(req.response);
			} else {
				// Otherwise reject with the status text which will hopefully be a meaningful error
				reject(new Error(req.statusText));
			}
		};

		// Handle errors
		req.onerror = function () {
			reject(new Error('Network/Script Error'));
		};

		// Make the request
		req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		req.send(params);
	});
}

function vtlib_toggleModule(module, action, type) {
	if (typeof(type) == 'undefined') {
		type = '';
	}
	var URL = 'index.php?module=Settings&action=SettingsAjax&file=ModuleManager&module_name=' + encodeURIComponent(module) + '&' + action + '=true&module_type=' + type;
	VtigerJS_DialogBox.block();
	jQuery.ajax({
		method:'POST',
		url:URL
	}).done(function (response) {
		VtigerJS_DialogBox.unblock();
		// Reload the page to apply the effect of module setting
		window.location.href = 'index.php?module=Settings&action=ModuleManager';
	});
}