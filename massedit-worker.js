self.addEventListener('message', function (event) {
	var sentForm = event.data;
	if (sentForm['module']!=undefined) {
		var thisp = this, cbierels_es = new EventSource('index.php?module='+sentForm['module']+'&action='+sentForm['module']+'Ajax&file=MassEditSave&params='+encodeURIComponent(JSON.stringify(sentForm)));
		cbierels_es.addEventListener('message', function (event) {
			var result = JSON.parse(event.data);
			thisp.postMessage(result);
			if (event.lastEventId == 'CLOSE') {
				thisp.postMessage('CLOSE');
				cbierels_es.close();
			}
		}, false);

		cbierels_es.addEventListener('error', function (e) {
			cbierels_es.close();
		});
	}
}, false);