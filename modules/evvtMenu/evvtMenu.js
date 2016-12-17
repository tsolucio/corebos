/*************************************************************************************************
 * Copyright 2013 JPL TSolucio, S.L.  --  This file is a part of JPL TSolucio vtiger CRM Extensions.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
 *  Module       : evvtMenu
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

function getMenuInfo(info){
	$('#evvtmenuid').val(info['evvtmenuid']);
	$('#mparent').val(info['mparent']);
	$('#mtype').val(info['mtype']);
	$('#mlabel').val(info['mlabel']);
	if(info.mtype==='module') $('#modname').val(info['mvalue']);
	if(info.mtype==='url') $('#mvalue').val(info['mvalue']);
	$('#mpermission').val(info['mpermission'].split(','));
	showFormParts(info['mtype']);
}


function processTree(action){
	document.getElementById("evvtmenudo").value = action;
	document.getElementById("menuitemform").submit();
}
function clearForm(){
	document.getElementById('evvtmenuid').value = '';
	document.getElementById('mparent').value = '';
	document.getElementById('mtype').value = '';
	document.getElementById('mlabel').value = '';
	document.getElementById('mpermission').value = '';
}
function showFormParts(mtype){
	var element;
	switch (mtype){
		case 'module':
			element = document.getElementById('moduleForm');
			element.classList.remove("hide");
			element = document.getElementById('actionForm');
			element.classList.add("hide");
			break;
		case 'url':
			element = document.getElementById('actionForm');
			element.classList.remove("hide");
			element = document.getElementById('moduleForm');
			element.classList.add("hide");
			break;
		case 'menu':
			element = document.getElementById('actionForm');
			element.classList.add("hide");
			element = document.getElementById('moduleForm');
			element.classList.add("hide");
			break;
		case 'sep':
			element = document.getElementById('actionForm');
			element.classList.add("hide");
			element = document.getElementById('moduleForm');
			element.classList.add("hide");
			break;
	}
}

function saveTree() {
	document.getElementById("evvtmenudo").value = 'updateTree';
	document.getElementById("treeIds").value = ids;
	document.getElementById("treeParents").value = parents;
	document.getElementById("treePositions").value = positions;
	document.getElementById("menuitemform").submit();
}