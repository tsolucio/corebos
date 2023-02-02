let cbConn = new Vtiger_WSClient('');

class PaintDocuments {

	constructor(cbUserID, cbFolderID, WSID) {
		this.cbUserID = cbUserID;
		this.cbFolderID = cbFolderID;
		this.WSID = WSID;
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
		return imageEditor.toDataURL();
	}

	Create = () => {
		this.Upsert(false);
	}

	Upsert = (docid) => {
		if (paint.Title().value == '' || paint.FileName().value == '') {
			ldsPrompt.show(alert_arr.ERROR, alert_arr.LBL_REQUIRED_FIELDS, 'error');
			return false;
		}
		cbConn.extendSession(function (result) {
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
				'relations': paint.WSID,
			};
			if (docid) {
				map['id'] = docid;
				cbConn.doUpdate('Documents', map, paint.Message);
			} else {
				cbConn.doCreate('Documents', map, paint.Message);
			}
		});
	}

	Message = (result, args) => {
		if(result) {
			const id = result.id.split('x');
			const folderid = result.folderid.split('x');
			cbConn.doSetRelated(id[1], folderid[1]);
			this.Title().value = '';
			this.FileName().value = '';
			this.Description().value = '';
			imageEditor.clearObjects();
			setTimeout(
				() => {
					imageEditor.loadImageFromURL('include/components/toast-ui/image-editor/blank.png', 'blank');
				},
				500
			);
			imageEditor.loadImageFromURL('include/components/toast-ui/image-editor/blank.png', 'blank');
			ldsPrompt.show(alert_arr.LBL_SUCCESS, alert_arr.LBL_CREATED_SUCCESS, 'success');
		} else {
			ldsPrompt.show(alert_arr.ERROR, alert_arr.LBL_ERROR_CREATING, 'error');
		}
	}
}