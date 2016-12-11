/*******************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 ******************************************************************************/
var moveupLinkObj, moveupDisabledObj, movedownLinkObj, movedownDisabledObj;

function setObjects() {
	availListObj = getObj("availList")
	selectedColumnsObj = getObj("selectedColumns")
}

function addColumn() {
	setObjects();
	for (i = 0; i < selectedColumnsObj.length; i++) {
		selectedColumnsObj.options[i].selected = false
	}

	for (i = 0; i < availListObj.length; i++) {
		if (availListObj.options[i].selected == true) {
			var rowFound = false;
			var existingObj = null;
			for (j = 0; j < selectedColumnsObj.length; j++) {
				if (selectedColumnsObj.options[j].value == availListObj.options[i].value) {
					rowFound = true
					existingObj = selectedColumnsObj.options[j]
					break
				}
			}
			if (rowFound != true) {
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
			} else {
				if (existingObj != null)
					existingObj.selected = true
			}
		}
	}
}

function delColumn() {
	setObjects();
	for (i = selectedColumnsObj.options.length; i > 0; i--) {
		if (selectedColumnsObj.options.selectedIndex >= 0)
			selectedColumnsObj.remove(selectedColumnsObj.options.selectedIndex)
	}
}

function formSelectColumnString() {
	var selectedColStr = "";
	setObjects();
	for (i = 0; i < selectedColumnsObj.options.length; i++) {
		selectedColStr += selectedColumnsObj.options[i].value + ",";
	}
	if (selectedColStr == "") {
		alert(alert_arr.LBL_MERGE_SHOULDHAVE_INFO);
		return false;
	}
	document.mergeDuplicates.selectedColumnsString.value = selectedColStr;
	return;
}
setObjects();