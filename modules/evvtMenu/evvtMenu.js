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

function getMenuInfo(json){
	console.log(json);
	$('#evvtmenuid').val(json['evvtmenuid']);
	$('#mparent').val(json['mparent']);
	$('#mtype').val(json['mtype']);
	$('#mlabel').val(json['mlabel']);
	if(json.mtype==='module') $('#modname').val(json['mvalue']);
	if(json.mtype==='url') $('#mvalue').val(json['mvalue']);
	$('#mpermission').val(json['mpermission'].split(','));
}
function expandSection(i){
	var id = 'trees-' + i;
	var element = document.getElementById(id);
	element.classList.remove("hide");
	element.classList.add("show");

}
function contractSection(i){
	var id = 'trees-' + i;
	var element = document.getElementById(id);
	element.classList.remove("show");
	element.classList.add("hide");
}
function expandTree(){
	var elements = document.getElementsByClassName("treeitems");
	for(var i = 0; i<elements.length; i++){
		elements[i].classList.remove("hide");
		elements[i].classList.add("show");
	}
}
function contractTree(){
	var elements = document.getElementsByClassName("treeitems");
	for(var i = 0; i<elements.length; i++){
		elements[i].classList.remove("show");
		elements[i].classList.add("hide");
	}
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