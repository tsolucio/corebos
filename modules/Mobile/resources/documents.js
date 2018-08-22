/* To get only filename from a given complete file path */
function getFileNameOnly(filename) {
	var onlyfilename = filename;
	// Normalize the path (to make sure we use the same path separator)
	var filename_normalized = filename.replace(/\\/g, '/');
	if (filename_normalized.lastIndexOf('/') != -1) {
		onlyfilename = filename_normalized.substring(filename_normalized.lastIndexOf('/') + 1);
	}
	return onlyfilename;
}

/* Function to validate the filename */
function validateFilename(form_ele) {
	if (form_ele.value == '') {
		return true;
	}
	var value = getFileNameOnly(form_ele.value);

	// Color highlighting logic
	var err_bg_color = '#FFAA22';

	if (typeof(form_ele.bgcolor) == 'undefined') {
		form_ele.bgcolor = form_ele.style.backgroundColor;
	}

	// Validation starts here
	var valid = true;

	/* Filename length is constrained to 255 at database level */
	if (value.length > 255) {
		alert(alert_arr.LBL_FILENAME_LENGTH_EXCEED_ERR);
		valid = false;
	}

	if (!valid) {
		form_ele.style.backgroundColor = err_bg_color;
		return false;
	}
	form_ele.style.backgroundColor = form_ele.bgcolor;
	form_ele.form[form_ele.name + '_hidden'].value = value;
	displayFileSize(form_ele);
	document.getElementById('filetype').value = form_ele.files[0].type;
	return true;
}

/* Function to validate the filsize */
function validateFileSize(form_ele, uploadSize) {
	if (form_ele.value == '') {
		return true;
	}
	var fileSize = form_ele.files[0].size;
	if (fileSize > uploadSize) {
		alert(alert_arr.LBL_SIZE_SHOULDNOTBE_GREATER + uploadSize/1000000+alert_arr.LBL_FILESIZEIN_MB);
		form_ele.value = '';
		document.getElementById('displaySize').innerHTML= '';
		document.getElementById('filesize').value = '';
	} else {
		displayFileSize(form_ele);
	}
}

/* Function to Display FileSize while uploading */
function displayFileSize(form_ele) {
	var fileSize = form_ele.files[0].size;
	document.getElementById('filesize').value = fileSize;
	if (fileSize < 1024) {
		document.getElementById('displaySize').innerHTML = fileSize + alert_arr.LBL_FILESIZEIN_B;
	} else if (fileSize > 1024 && fileSize < 1048576) {
		document.getElementById('displaySize').innerHTML = Math.round(fileSize / 1024, 2) + alert_arr.LBL_FILESIZEIN_KB;
	} else if (fileSize > 1048576) {
		document.getElementById('displaySize').innerHTML = Math.round(fileSize / (1024 * 1024), 2) + alert_arr.LBL_FILESIZEIN_MB;
	}
}