/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
function replaceUploadSize(){
	var upload = document.getElementById('key_upload_maxsize').value;
	upload = "'"+upload+"'";
	upload = upload.replace(/000000/g,"");
	upload = upload.replace(/'/g,"");
	document.getElementById('key_upload_maxsize').value = upload;
}


function vtlib_field_help_show_this(basenode, fldname) {
	var domnode = document.getElementById('vtlib_fieldhelp_div');

	

	var helpcontent = document.getElementById('helpInfo').value;


	if(!domnode) {
		domnode = document.createElement('div');
		domnode.id = 'vtlib_fieldhelp_div';
		domnode.className = 'dvtSelectedCell';
		domnode.style.position = 'absolute';
		domnode.style.width = '150px';
		domnode.style.padding = '4px';
		domnode.style.fontWeight = 'normal';
		document.body.appendChild(domnode);

		domnode = document.getElementById('vtlib_fieldhelp_div');
		jQuery("#vtlib_fieldhelp_div").mouseover(function() { 
			jQuery('#vtlib_fieldhelp_div').show(); 
		});
		jQuery("#vtlib_fieldhelp_div").mouseout(vtlib_field_help_hide);
	}
	else {
		domnode.style.display="block";
	}
	domnode.innerHTML = helpcontent;
	fnvshobj(basenode,'vtlib_fieldhelp_div');
}
