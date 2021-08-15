function fillinRelatedModules(module, cb) {
	relatedmodules = Array();
	cbws.doInvoke('getRelatedModulesInfomation', {'module':module}, 'post').then(async rmods => {
		for (var rl in rmods) {
			relatedmodules.push(new Array(rmods[rl].related_module, 'N:N'));
		}
		await cbws.doDescribe(module).then(dd => {
			var cc = Array();
			dd.fields.filter(f => f.uitype==10).forEach(f => {
				cc = cc.concat(f.type.refersTo);
			});
			cc.forEach(e => relatedmodules.push(new Array(e, '1:1')));
			relatedmodules.sort();
			cb();
		});
	});
}

function fillinDuelingPickList(dpl, elements) {
	var relmods = '';
	elements.forEach(rl => {
		relmods += `<li role="presentation" class="slds-listbox__item" onclick="dplsetSelected('dpl${dpl}', this);" ondblclick="dplchangelist('dpl${dpl}', this)">
		<div name="dpl${dpl}" class="slds-listbox__option slds-listbox__option_plain slds-media slds-media_small slds-media_inline" aria-selected="false" draggable="true" role="option">
		<span class="slds-media__body">
		<span name="dplspan" class="slds-truncate" title="${rl[0]}">${rl[0]} (${rl[1]})</span>
		</span>
		</div>
		</li>`;
	});
	document.getElementById(dpl).innerHTML = relmods;
}

function dplfindmoveli(fromlist, tolist) {
	let flist = document.getElementById(fromlist);
	let lielems = flist.querySelectorAll('[name="dpl'+fromlist+'"]');
	for (var idx=0; idx<lielems.length; idx++) {
		if (lielems[idx].attributes['aria-selected'].value=='true') {
			dplmove2list(lielems[idx], tolist);
			break;
		}
	}
}

function dplsetSelected(dpl, lielem) {
	const nlist = document.getElementsByName(dpl);
	for (var idx=0; idx<nlist.length; idx++) {
		nlist[idx].setAttribute('aria-selected', 'false');
	}
	lielem.querySelectorAll('div')[0].setAttribute('aria-selected', 'true');
}

function dplchangelist(dpl, lielem) {
	if (dpl=='dplselectedrelations') {
		dplmove2list(lielem, 'notselectedrelations');
	} else {
		dplmove2list(lielem, 'selectedrelations');
	}
}

function dplmove2list(lielem, tolist) {
	const mod = lielem.querySelector('[name="dplspan"]').title;
	if (tolist=='notselectedrelations') {
		let idx = selectedmodules.findIndex(e => e[0]==mod);
		let modelem = selectedmodules.splice(idx, 1);
		notselectedmodules.push(modelem[0]);
	} else {
		let idx = notselectedmodules.findIndex(e => e[0]==mod);
		let modelem = notselectedmodules.splice(idx, 1);
		selectedmodules.push(modelem[0]);
	}
	selectedmodules.sort();
	notselectedmodules.sort();
	fillinDuelingPickList('notselectedrelations', notselectedmodules);
	fillinDuelingPickList('selectedrelations', selectedmodules);
}

function selectModule(module) {
	fillinRelatedModules(module, function () {
		selectedmodules = Array();
		notselectedmodules = relatedmodules;
		fillinDuelingPickList('notselectedrelations', notselectedmodules);
		fillinDuelingPickList('selectedrelations', selectedmodules);
	});
}

function saveModuleMapAction() {
	let params = 'mapid='+document.getElementById('MapID').value;
	params += '&tmodule='+document.getElementById('tmodule').value;
	params += '&DuplicateDirectRelations=' + (document.querySelectorAll('input[name="DuplicateDirectRelations"]')[0].checked ? 1 : 0);
	let cc=Array();
	selectedmodules.map(e => cc=cc.concat(e[0]+'|'+e[1]));
	params += '&relmods=' + encodeURI(cc.join(','));
	saveMapAction(params);
}

function initializeDP() {
	fillinRelatedModules(mapMainModule, function () {
		notselectedmodules = relatedmodules.filter(e => !selectedmodules.some(val => (e[0]==val[0])));
		fillinDuelingPickList('notselectedrelations', notselectedmodules);
	});
}

var relatedmodules = Array();
var notselectedmodules = Array();
var cbws = '';
jQuery(document).ready(function () {
	cbws = new cbWSClient('');
	cbws.extendSession().then(initializeDP);
});
