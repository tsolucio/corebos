class PaintDocuments {

	constructor(cbUserID, cbFolderID, WSID) {
		this.cbUserID = cbUserID;
		this.cbFolderID = cbFolderID;
		this.WSID = WSID;
	}

	Init = () => {
		this.draw = new Paint(document.getElementById('fullscreen'));
	}

	Title = () => {
		return document.getElementById('title');
	}

	Description = () => {
		return document.getElementById('content');
	}

	FileName = () => {
		return document.getElementById('filename');
	}

	FolderID = () => {
		return document.getElementById('folders');
	}

	Canvas = () => {
		let canvas = document.getElementById('canvas-context');
		let context = canvas.getContext('2d');
		let paint = document.getElementsByClassName('paint-canvas');
		context.drawImage(paint[3], 0, 0, 640, 480);
		context.drawImage(paint[4], 0, 0, 640, 480);
		return canvas.toDataURL('image/png');
	}

	Create = () => {
		let cbConn = new Vtiger_WSClient('');
		if (paint.Title().value == '' || paint.FileName().value == '') {
			ldsPrompt.show(alert_arr.ERROR, alert_arr.LBL_REQUIRED_FIELDS, 'error');
			return false;
		}
		cbConn.extendSession(function(result){
			let fname = paint.FileName().value;
			if (fname.substr(fname.length - 4) != '.png') {
				fname = fname + '.png';
			}
			const map = {
				'assigned_user_id': paint.cbUserID,
				'notes_title': paint.Title().value,
				'notecontent': paint.Description().value,
				'filename': {
					'name': fname,
					'size': 0,
					'type': 'image/png',
					'content': paint.Canvas()
				},
				'filetype': 'image/png',
				'filesize': 0,
				'filelocationtype': 'I',
				'filedownloadcount': 0,
				'filestatus': 1,
				'folderid': paint.cbFolderID+paint.FolderID().value,
				'relations': paint.WSID
			};
			cbConn.doCreate('Documents', map, paint.Message);
		});
	}

	Message = (result, args) => {
		if(result) {
			this.Title().value = '';
			this.FileName().value = '';
			this.Description().value = '';
			this.draw.clear();
			let canvas = document.getElementById('canvas-context');
			let context = canvas.getContext('2d');
			context.clearRect(0, 0, 640, 480);
			ldsPrompt.show(alert_arr.LBL_SUCCESS, alert_arr.LBL_CREATED_SUCCESS, 'success');
		} else {
			ldsPrompt.show(alert_arr.ERROR, alert_arr.LBL_ERROR_CREATING, 'error');
		}
	}
}