jQuery(document).ready(function () {
	changeFieldTypeListener(document.getElementById('fieldtype').value);
	document.getElementById('block_modulename').value = document.getElementById('msmodules').value;
	fillBlocks('appfield_block');
});

function detailViewSetValues() {
	changeFieldTypeListener(document.getElementById('fieldtype').value);
}

function changeFieldTypeListener(fieldtype) {
	switch (fieldtype) {
	case 'ApplicationFields':
		handleApplicationFieldsCase();
		break;
	case 'FieldList':
		handleFieldListCase();
		break;
	case 'Widget':
		handleWidget();
		break;
	case 'RelatedList':
		handleRelatedList();
		break;
	case 'CodeWithHeader':
	case 'CodeWithoutHeader':
		handleInputDisplay();
		break;
	default:
		document.getElementById('WidgetDiv').style.display = 'none';
		document.getElementById('FieldListselectedDiv').style.display = 'none';
		document.getElementById('AppFieldselectedDiv').style.display = 'none';
		document.getElementById('codeDiv').style.display = 'none';
	}
}

function handleApplicationFieldsCase() {
	document.getElementById('FieldListselectedDiv').style.display = 'none';
	document.getElementById('WidgetDiv').style.display = 'none';
	document.getElementById('RelatedListDiv').style.display = 'none';
	document.getElementById('codeDiv').style.display = 'none';
	document.getElementById('contentHolderDiv').style.display = 'none';
	document.getElementById('AppFieldselectedDiv').style.display = '';
	fillBlocks('appfield_block');
}

function handleFieldListCase() {
	var listoffields = document.getElementById('list_of_fields');
	listoffields.innerHTML = '';
	var selectedmodule = document.getElementById('msmodules').value;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?action=cbMapAjax&mode=ajax&file=getModuleDetailsforLayoutSetup&module=cbMap&selmodule='+selectedmodule+'&query=fields'
	}).done(function (response) {
		response = JSON.parse(response);
		document.getElementById('AppFieldselectedDiv').style.display = 'none';
		document.getElementById('WidgetDiv').style.display = 'none';
		document.getElementById('RelatedListDiv').style.display = 'none';
		document.getElementById('codeDiv').style.display = 'none';
		document.getElementById('FieldListselectedDiv').style.display = '';
		document.getElementById('contentHolderDiv').style.display = '';
		response.forEach(function (fields) {
			var option = document.createElement('option');
			option.value = fields.fieldname;
			option.text = fields.fieldlabel;
			listoffields.appendChild(option);
		});
	});
}

function handleWidget() {
	document.getElementById('codeDiv').style.display = 'none';
	document.getElementById('FieldListselectedDiv').style.display = 'none';
	document.getElementById('AppFieldselectedDiv').style.display = 'none';
	document.getElementById('WidgetDiv').style.display = '';
	document.getElementById('RelatedListDiv').style.display = 'none';
}

function handleRelatedList() {
	document.getElementById('codeDiv').style.display = 'none';
	document.getElementById('FieldListselectedDiv').style.display = 'none';
	document.getElementById('AppFieldselectedDiv').style.display = 'none';
	document.getElementById('WidgetDiv').style.display = 'none';
	document.getElementById('RelatedListDiv').style.display = '';
}

function handleInputDisplay() {
	document.getElementById('FieldListselectedDiv').style.display = 'none';
	document.getElementById('AppFieldselectedDiv').style.display = 'none';
	document.getElementById('contentHolderDiv').style.display = 'none';
	document.getElementById('WidgetDiv').style.display = 'none';
	document.getElementById('codeDiv').style.display = '';
	document.getElementById('RelatedListDiv').style.display = 'none';
}

function fillTempContainer(content) {
	var tempdetail = document.getElementById('content_holder').value;
	var somelastval = tempdetail.slice(-5);
	if (somelastval === 'row$$') {
		return;
	}
	document.getElementById('content_holder').value = tempdetail + content + '$$';
}

function fillSelectedField() {
	var field = document.getElementById('list_of_fields').value;
	var tempdetail = document.getElementById('content_holder').value;
	document.getElementById('content_holder').value = tempdetail +'column##' +field + '$$';
}

function getWidgetCodeContent() {
	var prms = 'loadfrom##'+document.getElementById('loadfrom').value
	+'$$handler_class##'+document.getElementById('handler_class').value
	+'$$handler##'+document.getElementById('handler').value;
	return prms;
}

function fillBlocks(target) {
	var blockfield = document.getElementById(target);
	blockfield.innerHTML = '';
	var selectedmodule = document.getElementById('msmodules').value;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?action=cbMapAjax&mode=ajax&file=getModuleDetailsforLayoutSetup&module=cbMap&selmodule='+selectedmodule+'&query=blocks'
	}).done(function (response) {
		response = JSON.parse(response);
		response.forEach(function (block) {
			var option = document.createElement('option');
			option.value = block.blockid;
			option.text = block.blocklabel;
			blockfield.appendChild(option);
		});
	});
}

function saveNewBlock() {
	var blockmodname = document.getElementById('block_modulename').value;
	var blocklabel = document.getElementById('blocklabel').value;
	var afterblockid = document.getElementById('after_block').value;
	var blocklabelval = trim(blocklabel);
	var relblock = 'no';
	jQuery.ajax({
		method:'POST',
		url:'index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=addBlock&fld_module='+blockmodname+'&ajax=true&mode=add&blocklabel='+
		encodeURIComponent(blocklabelval)+'&after_blockid='+afterblockid+'&relblock='+relblock
	}).done(function (response) {
		document.getElementById('newBlockcDiv').style.display = 'none';
	});
}

function saveDetailLayoutMapAction() {
	let params = 'mapid='+document.getElementById('MapID').value+'&tmodule='+document.getElementById('msmodules').value;
	params += '&type='+document.getElementById('fieldtype').value;
	switch (document.getElementById('fieldtype').value) {
	case 'ApplicationFields':
		params +='&content=blockid##'+document.getElementById('appfield_block').value;
		break;
	case 'FieldList':
		params +='&content='+document.getElementById('content_holder').value;
		break;
	case 'Widget':
		params +='&content=loadfrom##'+document.getElementById('widloadfrom').value;
		break;
	case 'CodeWithHeader':
	case 'CodeWithoutHeader':
		var param = getWidgetCodeContent();
		params += '&content='+param;
		break;
	}
	params = encodeURI(params);
	saveMapAction(params);
}