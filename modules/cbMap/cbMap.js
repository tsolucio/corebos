/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
function showMapWindow(mapid) {
	var url = 'index.php?module=cbMap&action=cbMapAjax&file=generateMap&mapid='+mapid;
	window.open(url, 'Create Mapping', 'width=940,height=800,resizable=1,scrollbars=1');
}

function validateMap(mapid) {
	var url = 'index.php?module=cbMap&action=cbMapAjax&file=validateMap';
	var stringData = 'mapid=' + mapid;
	jQuery.ajax({
		type: 'POST',
		url: url,
		data: stringData,
		cache: false,
		async: false,
		success: function (text) {
			function hideresult() {
				document.getElementById('vtbusy_validate_info').style.display = 'none';
			}
			if (text == 'VALIDATION_NOT_IMPLEMENTED_YET') {
				//Map doesn't have a XSD yet
				document.getElementById('map_valid').style.display = 'none';
				document.getElementById('map_error').style.display = 'none';
				var x = document.getElementById('map_not_implemented_yet');
				if (x.style.display === 'none') {
					x.style.display = 'block';
					document.getElementById('vtbusy_validate_info').style.display = '';
					setTimeout(hideresult, 100);
				} else {
					x.style.display = 'none';
				}
			} else if (text) {
				//Display the error(s)
				document.getElementById('map_valid').style.display = 'none';
				document.getElementById('map_not_implemented_yet').style.display = 'none';
				text1 = text.replace(/<\/?[^>]+(>|$)/g, '');
				cleanText = text1.replace('Error: The document has no document element. on line -1', ' ');
				var x = document.getElementById('map_error');
				if (x.style.display === 'none') {
					x.style.display = 'block';
					document.getElementById('vtbusy_validate_info').style.display = '';
					setTimeout(hideresult, 100);
				} else {
					x.style.display = 'none';
				}
				document.getElementById('map_validation_message').innerHTML = cleanText;
			} else {
				//Map is valid
				document.getElementById('map_error').style.display = 'none';
				document.getElementById('map_not_implemented_yet').style.display = 'none';
				var x = document.getElementById('map_valid');
				if (x.style.display === 'none') {
					x.style.display = 'block';
					document.getElementById('vtbusy_validate_info').style.display = '';
					setTimeout(hideresult, 100);
				} else {
					x.style.display = 'none';
				}
			}
		}
	});
}