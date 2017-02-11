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

function buildMainMenu(object){ //main menu
	for (var i in object) {
		if(object[i].items != null) {
			jQuery('#cbmenu').append('<li class="slds-context-bar__item slds-context-bar__dropdown-trigger slds-dropdown-trigger slds-dropdown-trigger--hover" aria-haspopup="true"> \
				<a href="javascript:void(0);" class="slds-context-bar__label-action" title="' + object[i].text + '">\
				<span class="slds-truncate">' + object[i].text + '</span>\
		</a>\
		<div class="slds-context-bar__icon-action slds-p-left--none" tabindex="0">\
			<svg aria-hidden="true" class="slds-button__icon">\
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevrondown"></use>\
			</svg>\
		</div>\
		<div class="slds-dropdown slds-dropdown--right">\
		<ul class="slds-dropdown__list" role="menu" id="menu' + i + '">\
		</ul>\
		</div>\
		</li>');
		} else {
			jQuery('#cbmenu').append('<li class="slds-context-bar__item">\
				<a href="'+object[i].url+ '" class="slds-context-bar__label-action" title="'+object[i].text+'">\
				<span class="slds-truncate">'+object[i].text+'</span>\
				</a>\
				</li>');
		}
		if(object[i].items != null) {
			buildSubMenu(object[i].items, i)
		}
	}
}

function buildSubMenu(object, index){ //submenu
	var menuid = 'menu'+index;
	for (var i in object){
		if (object[i].type == 'sep') {
			jQuery('#' + menuid).append('<li class="slds-dropdown__header slds-has-divider--top-space" role="separator"></li>');
		} else if (object[i].type == 'headtop') {
			jQuery('#' + menuid).append('<li class="slds-dropdown__header slds-has-divider--top-space" role="separator">\
				<span class="slds-text-title--caps">' + object[i].text + '</span></li>');
		} else if (object[i].type == 'headbottom') {
			jQuery('#' + menuid).append('<li class="slds-dropdown__header slds-has-divider--bottom-space" role="separator">\
				<span class="slds-text-title--caps">' + object[i].text + '</span></li>');
		} else {
			if (object[i].items === undefined || object[i].items === null) {
				jQuery('#' + menuid).append('<li class="slds-dropdown__item" role="presentation">\
					<a href="' + object[i].url + '" role="menuitem" tabindex="-1">\
					<span class="slds-truncate">' + object[i].text + '</span>\
					</a>\
					</li>');
			} else {
				jQuery('#' + menuid).append('<li class="slds-dropdown__item" role="presentation">\
					<a href="' + (object[i].url == undefined ? 'javascript:void(0);' : object[i].url) + '" role="menuitem" tabindex="-1">\
					<span class="slds-truncate" style="padding-right:20px">' + object[i].text + '</span>\
					<svg aria-hidden="true" class="slds-button__icon">\
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevronright"></use>\
					</svg>\
					</a>\
					<ul class="moreMenu" id="submenu' + i + '-' + index+ '">\
					</ul>\
					</li>');
				var pld = i + '-' + index;
				buildMoreMenu(object[i].items, pld);//kallxom kur pe marron lvl3
			}
		}
	}
}

function buildMoreMenu(object, index){ //pjest shtes qe duhen mmu shtu
	var subMenuId = 'submenu' +index;
	for (var i in object) {
		if (object[i].items === undefined || object[i].items === null) {
			jQuery('#' + subMenuId).append('<li class="slds-dropdown__item" role="presentation">\
					<a href="' + object[i].url + '" role="menuitem" tabindex="-1">\
					<span class="slds-truncate">' + object[i].text + '</span>\
					</a>\
					</li>');
		} else {
			jQuery('#' + subMenuId).append('<li class="slds-dropdown__item" role="presentation" id="test">\
					<a href="' + object[i].url + '" role="menuitem" tabindex="-1" id="test">\
					<span class="slds-truncate" style="padding-right:20px">' + object[i].text + '</span>\
					<svg aria-hidden="true" class="slds-button__icon">\
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevronright"></use>\
					</svg>\
					<ul class="moreMenu2" id="submenu' + i + '-' + index + '">\
					</ul>\
					</a>\
					</li>');
			var pld = i + '-' + index;
			buildMoreMenu(object[i].items, pld);
		}
	}
}

jQuery(document).ready(function() {
	buildMainMenu(evvtmenu);

	jQuery(function () {
		jQuery(".slds-dropdown__item").hover(function () {
			var id = jQuery(this).children('ul').attr('id');
			if (id === undefined || id === null) {
				id = jQuery(this).find('ul').attr('id');
			}
			jQuery(this).find('#' + id).toggle();
		});
	});
});
