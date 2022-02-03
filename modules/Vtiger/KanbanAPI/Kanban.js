function kanbanRefresh(kanbanID) {
	let kbinfo = window[kanbanID+'Info'];
	for (const lane in kbinfo.lanes) {
		kanbanGetBoardItems(kanbanID, lane, kbinfo);
	}
}

function kanbanGetBoardItems(kanbanID, lane, kbinfo) {
	kbinfo.lanename = lane;
	let boardid = kbinfo.lanes[lane].id;
	let kbinfostr = '&boardinfo=' + encodeURIComponent(JSON.stringify(kbinfo));
	fetch(
		'index.php?module=Utilities&action=UtilitiesAjax&file=KanbanAPI&method=getBoardItemsFormatted',
		{
			method: 'post',
			headers: {
				'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			credentials: 'same-origin',
			body: '&'+csrfMagicName+'='+csrfMagicToken+kbinfostr+'&kbmodule='+kbinfo.module
		}
	).then(response => response.json()).then(response => {
		if (response && response.length) {
			response.forEach(tile => {
				window[kanbanID].addElement(boardid, {
					id: tile.id,
					lane: lane,
					crmid: tile.crmid,
					kanbanID: kanbanID,
					title: tile.title
				});
			});
			kanbanApplyCustomCSS(kanbanID);
		}
	});
}

// Custom CSS
function kanbanApplyCustomCSS(kanbanID) {
	document.getElementById(kanbanID).querySelectorAll('.tooltip').forEach(element => element.style.position='absolute');
	document.querySelectorAll('.slds-tile .slds-grid ul').forEach(element => element.classList.add('small'));
}

function kanbanSetupInfiniteScroll(kanbanID) {
	const options = {};

	const callback = entries => {
		if (entries[0].isIntersecting) {
			if (window[kanbanID+'Info'].currentPage) {
				kanbanRefresh(kanbanID);
			}
			window[kanbanID+'Info'].currentPage++;
		}
	};

	var observer = new IntersectionObserver(callback, options);
	observer.observe(document.getElementById(kanbanID+'Scroll'));
}

function kbMoveTile(kanbanID, lane, module, record) {
	let kbinfostr = '&boardinfo=' + encodeURIComponent(JSON.stringify(window[kanbanID+'Info']));
	fetch(
		'index.php?module=Utilities&action=UtilitiesAjax&file=KanbanAPI&method=getBoardItemFormatted',
		{
			method: 'post',
			headers: {
				'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			credentials: 'same-origin',
			body: '&'+csrfMagicName+'='+csrfMagicToken+kbinfostr+'&kbmodule='+module+'&tileid='+record
		}
	).then(response => response.json()).then(response => {
		if (response) {
			let element = {
				id: response.id,
				lane: lane,
				crmid: response.crmid,
				kanbanID: kanbanID,
				title: response.title
			};
			window[kanbanID].moveElement(window[kanbanID+'Info'].lanes[lane].id, module+record, element);
		}
	});
}

function kbUpdateAfterDrop(el, target) {
	let crmid = el.getAttribute('data-crmid');
	let kanbanID = el.getAttribute('data-kanbanID');
	let module = window[kanbanID+'Info'].module;
	let fieldName = window[kanbanID+'Info'].lanefield;
	let dstboard = document.getElementById(target.parentElement.getAttribute('data-id')+'lane');
	let dstlane = dstboard.getAttribute('data-lane');
	let srclane = el.getAttribute('data-lane');
	const sentForm = {
		'from_link':'DetailView',
		'cbfromid':crmid,
		'module':module,
		'record':crmid,
		'action':'DetailViewEdit',
		'dtlview_edit_fieldcheck':fieldName
	};
	sentForm[csrfMagicName] = csrfMagicToken;
	sentForm[fieldName] = dstlane;
	(async () => {
		const response = await fetch(
			'index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=ValidationLoad&valmodule='+module,
			{
				method: 'post',
				headers: {
					'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
				},
				credentials: 'same-origin',
				body: `&${csrfMagicName}=${csrfMagicToken}&structure=${JSON.stringify(sentForm)}`
			}
		);
		const msg = await response.text();
		if (msg == '%%%OK%%%') {
			if (dstlane==srclane) {
				// use kanbansequence table and order the cards > seems difficult
			} else {
				dtlViewAjaxDirectFieldSave(dstlane, module, '', fieldName, crmid, '');
				el.setAttribute('data-lane', dstlane);
			}
		} else {
			ldsPrompt.show(alert_arr.VALID_DATA, msg, 'error');
			kbMoveTile(kanbanID, srclane, module, crmid);
		}
	})();
}

function kbDeleteElement(module, record, kanbanID) {
	if (confirm(alert_arr.ARE_YOU_SURE)) {
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module='+module+'&action=Delete&record='+record+'&return_module='+module+'&return_action=Kanban'
		}).done(function (response) {
			window[kanbanID].removeElement(module+record);
		});
	}
}

function kbPopupSaveHook(module, record, mode, kbinfo) {
	let kbinfoname = '';
	let param = kbinfo;
	if (mode=='edit') {
		param = JSON.parse(decodeURIComponent(kbinfo));
		kbinfoname = param.id+'Info';
	} else {
		kbinfoname = param+'Info';
	}
	let kbinfostr = '&boardinfo=' + encodeURIComponent(JSON.stringify(window[kbinfoname]));
	fetch(
		'index.php?module=Utilities&action=UtilitiesAjax&file=KanbanAPI&method=getBoardItemFormatted',
		{
			method: 'post',
			headers: {
				'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			credentials: 'same-origin',
			body: '&'+csrfMagicName+'='+csrfMagicToken+kbinfostr+'&kbmodule='+module+'&tileid='+record
		}
	).then(response => response.json()).then(response => {
		if (response) {
			let element = {
				id: response.id,
				lane: response.lane,
				crmid: response.crmid,
				kanbanID: window[kbinfoname].kanbanID,
				title: response.title
			};
			if (mode=='edit') {
				if (tile.dataset.lane!=response.lane) {
					window[window[kbinfoname].kanbanID].moveElement(window[kbinfoname].lanes[response.lane].id, response.id, element);
				} else {
					window[window[kbinfoname].kanbanID].replaceElement(response.id, element);
				}
			} else {
				window[window[kbinfoname].kanbanID].addElement(window[kbinfoname].lanes[response.lane].id, element);
			}
			kanbanApplyCustomCSS(window[kbinfoname].kanbanID);
		}
	});
}