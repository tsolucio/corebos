jQuery(document).ready(function () {
	fieldtype = document.getElementById('fieldtype').value;
	selmodule = document.getElementById('msmodules').value;
	changeFieldTypeListener(fieldtype);
	setRelatedListVals(selmodule);
	document.getElementById('block_modulename').value = document.getElementById('msmodules').value;
	fillBlocks('after_block');
	fillBlocks('appfield_block');
});

function detailViewSetValues(selmodule) {
	fillBlocks('after_block');
	setRelatedListVals(selmodule);
	fieldtype = document.getElementById('fieldtype').value;
	changeFieldTypeListener(fieldtype);
}

function changeFieldTypeListener(fieldtype) {
	switch (fieldtype) {
		case 'ApplicationFields':
			handleApplicationFieldsCase();
		break;
		case 'FieldList':
			handleFieldListCase();
		break;
		case 'RelatedList':
			handleRelatedListCase();
		break;
		case 'Widget':
		case 'CodeWithHeader':
		case 'CodeWithoutHeader':
			handleInputDisplay();
		break;
		default:
			document.getElementById('WidgetDiv').style.display = 'none';
			document.getElementById('FieldListselectedDiv').style.display = 'none';
			document.getElementById('RelatedListDiv').style.display = 'none';
			document.getElementById('AppFieldselectedDiv').style.display = 'none';
	}
}

function handleApplicationFieldsCase() {
	document.getElementById('FieldListselectedDiv').style.display = 'none';
	document.getElementById('RelatedListDiv').style.display = 'none';
	document.getElementById('WidgetDiv').style.display = 'none';
	document.getElementById('AppFieldselectedDiv').style.display = '';
	document.getElementById('contentHolderDiv').style.display = '';
	fillBlocks('appfield_block');
}

function handleFieldListCase() {
	listoffields = document.getElementById('list_of_fields');
	listoffields.innerHTML = '';
	var selectedmodule = document.getElementById("msmodules").value;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?action=cbMapAjax&mode=ajax&file=getModuleDetailsforLayoutSetup&module=cbMap&selmodule='+selectedmodule+'&query=fields'
	}).done(function (response) {
		response = JSON.parse(response);
		document.getElementById('AppFieldselectedDiv').style.display = 'none';
		document.getElementById('WidgetDiv').style.display = 'none';
		document.getElementById('FieldListselectedDiv').style.display = '';
		document.getElementById('RelatedListDiv').style.display = 'none';
		document.getElementById('contentHolderDiv').style.display = '';
		response.forEach(function (fields) {
			var option = document.createElement("option");
			option.value = fields.fieldname;
			option.text = fields.fieldlabel;
			listoffields.appendChild(option);
		});
	});
}

function handleRelatedListCase() {
	document.getElementById('FieldListselectedDiv').style.display = 'none';
	document.getElementById('AppFieldselectedDiv').style.display = 'none';
	document.getElementById('contentHolderDiv').style.display = 'none';
	document.getElementById('WidgetDiv').style.display = 'none';
	document.getElementById('RelatedListDiv').style.display = '';
}

function handleInputDisplay() {
	document.getElementById('FieldListselectedDiv').style.display = 'none';
	document.getElementById('AppFieldselectedDiv').style.display = 'none';
	document.getElementById('contentHolderDiv').style.display = 'none';
	document.getElementById('RelatedListDiv').style.display = 'none';
	document.getElementById('WidgetDiv').style.display = '';
}

function setRelatedListVals(selmodule) {
	options = document.getElementById('relmodules');
	options.innerHTML = '';
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?action=cbMapAjax&mode=ajax&file=getModuleDetailsforLayoutSetup&module=cbMap&selmodule='+selmodule+'&query=relatedlist'
	}).done(function (response) {
		var reldata = JSON.parse(response);
		reldata.forEach(function (element) {
			var option = document.createElement("option");
			option.value = element.label;
			option.text = element.label;
			options.appendChild(option);
		});
	});
}

function fillTempContainer(content) {
	tempdetail = document.getElementById('rowcolumnfield_container').value;
	document.getElementById('rowcolumnfield_container').value = tempdetail + content + '$$';
}

function fillSelectedField() {
	field = document.getElementById('list_of_fields').value;
	tempdetail = document.getElementById('rowcolumnfield_container').value;
	document.getElementById('rowcolumnfield_container').value = tempdetail + field + '$$';
}

function addBlockDetails() {
	maincontent = document.getElementById('content_holder').value;
	type = document.getElementById('fieldtype').value;
	switch (type) {
		case 'ApplicationFields':
			label = document.getElementById('label').value;
			sequence = document.getElementById('sequence').value;
			blockid = document.getElementById('appfield_block').value;
			document.getElementById('content_holder').value = maincontent + 'block$$label##'+label + '$$sequence##' + sequence + '$$blockid##' + blockid + '$$';
			break;
		case 'FieldList':
			label = document.getElementById('label').value;
			sequence = document.getElementById('sequence').value;
			rowdata = document.getElementById('rowcolumnfield_container').value;
			document.getElementById('content_holder').value = maincontent + 'block$$label##'+ label + '$$sequence##' + sequence + '$$' + rowdata;
			document.getElementById('rowcolumnfield_container').value = '';
			break;
		default:
	}
}

function getWidgetCodeContent() {
	var prms = 'loadfrom##'+document.getElementById('loadfrom').value+'$$loadcode##'+document.getElementById('loadcode').value
	+'$$handler_path##'+document.getElementById('handler_path').value+'$$handler_class##'+document.getElementById('handler_class').value
	+'$$handler##'+document.getElementById('handler').value;
	return prms;
}

function fillBlocks(target) {
	blockfield = document.getElementById(target);
	blockfield.innerHTML = '';
	var selectedmodule = document.getElementById("msmodules").value;
	jQuery.ajax({
		method: 'POST',
		url: 'index.php?action=cbMapAjax&mode=ajax&file=getModuleDetailsforLayoutSetup&module=cbMap&selmodule='+selectedmodule+'&query=blocks'
	}).done(function (response) {
		response = JSON.parse(response);
		response.forEach(function (block) {
			var option = document.createElement("option");
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
		url:'index.php?module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=addBlock&fld_module='+blockmodname+'&parenttab=Settings&ajax=true&mode=add&blocklabel='+
		encodeURIComponent(blocklabelval)+'&after_blockid='+afterblockid+'&relblock='+relblock
	}).done(function (response) {
		var str = response;
		if (str == 'ERROR') {
			alert(alert_arr.LABEL_ALREADY_EXISTS);
			return false;
		} else if (str == 'LENGTH_ERROR') {
			alert(alert_arr.LENGTH_OUT_OF_RANGE);
			return false;
		}
		document.getElementById('newBlockcDiv').style.display = 'none';
	});
}


function saveDetailLayoutMapAction() {
	type = document.getElementById('fieldtype').value;
	let params = 'mapid='+document.getElementById('MapID').value+'&tmodule='+document.getElementById('msmodules').value;
	params += '&type='+document.getElementById('fieldtype').value;
	switch (type) {
		case 'ApplicationFields':
			params += '&content='+document.getElementById('content_holder').value;
			break;
		case 'FieldList':
			params += '&content='+document.getElementById('content_holder').value;
			break;
		case 'RelatedList':
			params += '&content=loadfrom##'+document.getElementById('relloadfrom').value;
			break;
		case 'Widget':
		case 'CodeWithHeader':
		case 'CodeWithoutHeader':
			param = getWidgetCodeContent();
			params += '&content='+param;
			break;
	}
	params = encodeURI(params);
	saveMapAction(params);
}