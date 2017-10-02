function getImportSavedMap(impoptions)
{
	var mapping = impoptions.options[impoptions.options.selectedIndex].value;

	//added to show the delete link
	if (mapping != -1)
		document.getElementById("delete_mapping").style.visibility = "visible";
	else
		document.getElementById("delete_mapping").style.visibility = "hidden";

	jQuery.ajax({
		method: "POST",
		url: 'index.php?module=Import&mapping=' + mapping + '&action=ImportAjax',
	}).done(function (response) {
		document.getElementById("importmapform").innerHTML = response;
	});
}

function deleteMapping() {
	var options_collection = document.getElementById("saved_source").options;
	var mapid = options_collection[options_collection.selectedIndex].value;

	jQuery.ajax({
		method: "POST",
		url: 'index.php?module=Import&mapping=' + mapid + '&action=ImportAjax&delete_map=' + mapid
	}).done(function (response) {
		document.getElementById("importmapform").innerHTML = response;
	});
	//we have emptied the map name from the select list
	document.getElementById("saved_source").options[options_collection.selectedIndex] = null;
	document.getElementById("delete_mapping").style.visibility = "hidden";
	alert(alert_arr.MAP_DELETED_INFO);
}

function check_submit() {
	var selectedColStr = "";
	if (document.getElementById("merge_check").checked == true)
	{
		setObjects();
		for (i = 0; i < selectedColumnsObj.options.length; i++)
			selectedColStr += selectedColumnsObj.options[i].value + ",";
		if (selectedColStr == "")
		{
			alert(alert_arr.LBL_MERGE_SHOULDHAVE_INFO);
			return false;
		}
		document.Import.selectedColumnsString.value = selectedColStr;
	}
	if (validate_import_map()) {
		if (document.getElementById("save_map").checked)
		{
			var name = document.getElementById("save_map_as").value;
			document.getElementById("status").style.display = "block";
			jQuery.ajax({
				method: "POST",
				url: 'index.php?module=Import&name=' + name + '&ajax_action=check_dup_map_name&action=ImportAjax'
			}).done(function (response) {
				if (response == 'true')
					document.Import.submit();
				else
				if (confirm(alert_arr.MAP_NAME_EXISTS))
					document.Import.submit();
				document.getElementById("status").style.display = "none";
			});
		}
		else
			document.Import.submit();
	}
}

//added for duplicate handling -srini
function show_option(obj)
{
	var sel_value = obj.value;
	if (sel_value == 'auto')
	{
		document.getElementById("auto_option").innerHTML = document.getElementById("option_div").innerHTML;
	}
	else
		document.getElementById("auto_option").innerHTML = "&nbsp;";
}

function showMergeOptions(curObj, arg)
{
	var ele = curObj;
	var mergeoptions = document.getElementsByName('dup_type');
	if (mergeoptions != null && ele != null) {
		if (ele.checked == true) {
			mergeoptions[0].checked = true;
			mergeoptions[1].checked = false;
		} else {
			mergeoptions[0].checked = false;
			mergeoptions[1].checked = false;
		}
		mergeshowhide(arg);
	}
}

var moveupLinkObj, moveupDisabledObj, movedownLinkObj, movedownDisabledObj;
function setObjects()
{
	availListObj = getObj("availList")
	selectedColumnsObj = getObj("selectedColumns")
}

function addColumn()
{
	setObjects();
	for (i = 0; i < selectedColumnsObj.length; i++)
	{
		selectedColumnsObj.options[i].selected = false
	}

	for (i = 0; i < availListObj.length; i++)
	{
		if (availListObj.options[i].selected == true)
		{
			var rowFound = false;
			var existingObj = null;
			for (j = 0; j < selectedColumnsObj.length; j++)
			{
				if (selectedColumnsObj.options[j].value == availListObj.options[i].value)
				{
					rowFound = true
					existingObj = selectedColumnsObj.options[j]
					break
				}
			}

			if (rowFound != true)
			{
				var newColObj = document.createElement("OPTION")
				newColObj.value = availListObj.options[i].value
				if (browser_ie)
					newColObj.innerText = availListObj.options[i].innerText
				else if (browser_nn4 || browser_nn6)
					newColObj.text = availListObj.options[i].text
				selectedColumnsObj.appendChild(newColObj)
				availListObj.options[i].selected = false
				newColObj.selected = true
				rowFound = false
			}
			else
			{
				if (existingObj != null)
					existingObj.selected = true
			}
		}
	}
}

function delColumn()
{
	setObjects();
	for (i = selectedColumnsObj.options.length; i > 0; i--)
	{
		if (selectedColumnsObj.options.selectedIndex >= 0)
			selectedColumnsObj.remove(selectedColumnsObj.options.selectedIndex)
	}
}

function formSelectColumnString()
{
	var selectedColStr = "";
	setObjects();
	for (i = 0; i < selectedColumnsObj.options.length; i++)
	{
		selectedColStr += selectedColumnsObj.options[i].value + ",";
	}
	if (selectedColStr == "")
	{
		alert(alert_arr.LBL_MERGE_SHOULDHAVE_INFO);
		return false;
	}
	document.Import.selectedColumnsString.value = selectedColStr;
	return;
}
setObjects();
