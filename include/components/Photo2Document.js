function convertCanvasToImage(canvas) {
	let image = new Image();
	image.src = canvas.toDataURL('image/png');
	return image;
}

function createdoc() {
	let cbconn = new Vtiger_WSClient('');
	cbconn.extendSession(function (result) {
		var fname = document.getElementById('filename').value;
		if (fname.substr(fname.length - 4) != '.png') {
			fname = fname + '.png';
		}
		var model_filename={
			'name' : fname,
			'size' : 0,
			'type' : 'image/png',
			'content' : document.getElementById('canvas').toDataURL('image/png')
		};
		var module = 'Documents';
		var valuesmap = {
			'assigned_user_id' : p2dcbUserID,
			'notes_title' : document.getElementById('docname').value,
			'notecontent' : document.getElementById('docdesc').value,
			'filename' : model_filename,
			'filetype' : 'image/png',
			'filesize' : 0,
			'filelocationtype' : 'I',
			'filedownloadcount' : 0,
			'filestatus' : 1,
			'folderid'  : p2dcbFolderID+document.getElementById('docfolder').value,
			'relations' : p2dWSID
		};
		cbconn.doCreate(module, valuesmap, afterCreateRecord);
	});
}

function afterCreateRecord(result, args) {
	if (result) {
		ldsPrompt.show(alert_arr.LBL_SUCCESS, i18nP2D.DocumentCreatedRelated, 'success');
	} else {
		ldsPrompt.show(alert_arr.ERROR, i18nP2D.ERROR+' '+i18nP2D.LBL_CREATING, 'error');
	}
}

// Elements for taking the snapshot
var p2dcanvas = document.getElementById('canvas');
var p2dcontext = p2dcanvas.getContext('2d');
var p2dvideo = document.getElementById('video');
if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
	// Not adding `{ audio: true }` since we only want video now
	navigator.mediaDevices.getUserMedia({ video: true }).then(function (stream) {
		p2dvideo.srcObject = stream;
		p2dvideo.play();
	});
}

// Trigger photo take
function p2dSnap() {
	p2dcontext.drawImage(video, 0, 0, 640, 480);
	p2dvideo.style.display = 'none';
	p2dcanvas.style.display = 'block';
}

// clear photo
function p2dClearPicture() {
	p2dvideo.style.display = 'block';
	p2dcanvas.style.display = 'none';
}
