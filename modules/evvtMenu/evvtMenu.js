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

var drag_menuinfo = [];

function clear_evvtMenuForm(clearMType) {
//jQuery('#evvtmenuid').val(0);
	jQuery('#mparent').val(0);
	if (clearMType) jQuery('#mtype').data("kendoDropDownList").value('module');
	jQuery('#mlabel').val('');
	jQuery('#mvalue').val('');
	jQuery('#modname').val('');
	jQuery('#mpermission').data("kendoMultiSelect").value('');
}

function getMenuBranchJSON(branch_top) {
	var menubranch = [];
	var mseq = 1;
	branch_top.children('li').each(function (key,obj) {
		var mbranch = {};
		minfo = jQuery.parseJSON($(obj).attr('menuinfo'));
		mbranch.mid = minfo.evvtmenuid;
		mbranch.mvisible = ($('#evvtmcb-'+minfo.evvtmenuid).is(':checked') ? 1 : 0);
		msubsub = [];
		$(obj).children('ul').each(function (key,obj) {
			mss = getMenuBranchJSON($(obj));
			msubsub.push(mss);
		});
		mbranch.msubmenu = msubsub;
		mbranch.mseq = mseq;
		mseq++;
		//menubranch.push(JSON.stringify(mbranch));
		menubranch.push(mbranch);
	});
	return menubranch;
}

function treeToJson(treeview, root) {
    root = root || treeview.element;
    var mseq = 1;
    return root.children().map(function(ky,obj) {
    	var minfo = jQuery.parseJSON($(obj).attr('menuinfo'));
        var result = {
        	text: treeview.text(this).replace(/\n|\t/g,'').trim(),
			mid: minfo.evvtmenuid,
			mvisible: ($('#evvtmcb-'+minfo.evvtmenuid).is(':checked') ? 1 : 0),
			mseq: mseq
        };
        mseq++;
        items = treeToJson(treeview, $(this).children(".k-group"));
        if (items.length) {
            result.items = items;
        }
        return JSON.stringify(result);
      }).toArray();
}

function evvtmSelectTreeElement(n) {
	var treeView = $("#treeview").data("kendoTreeView");
	var selectedNode = treeView.select();
	minfo = jQuery.parseJSON($(selectedNode).attr('menuinfo'));
	jQuery('#mtype').data("kendoDropDownList").value(minfo.mtype);
	menuTypeChange();
	jQuery('#evvtmenuid').val(minfo.evvtmenuid);
	jQuery('#mparent').val(minfo.mparent);
	jQuery('#mlabel').val(minfo.mlabel);
	jQuery('#mvalue').val(minfo.mvalue);
	jQuery('#modname').val(minfo.mvalue);
	jQuery('#modname').data("kendoDropDownList").select(jQuery('#modname')[0].selectedIndex);
	jQuery('#mpermission').data("kendoMultiSelect").value(minfo.mpermission.toArray());
}

function saveMenuInfo(topnode) {
	var elem ={};
	elem.datauid = $(topnode).attr('data-uid');
	elem.minfo = $(topnode).attr('menuinfo');
	elem.minputid = $(topnode).find('input:first-child').attr('id');
	drag_menuinfo.push(elem);
	$(topnode).find('ul,li').each(function (k,o) {
		var minfo = $(o).attr('menuinfo');
		if (minfo!=undefined) var menuinfo = jQuery.parseJSON(minfo);
		var elem ={};
		elem.datauid = $(o).attr('data-uid');
		elem.minfo = minfo;
		elem.minputid = $(o).find('input:first').attr('id');
		if (menuinfo!=undefined) elem.mvisible = $('#evvtmcb-'+menuinfo.evvtmenuid).attr('checked');
		drag_menuinfo.push(elem);
	});
}

function assignMenuInfo() {
	for (index = 0; index < drag_menuinfo.length; ++index) {
		elem = drag_menuinfo[index];
		$('[data-uid='+elem.datauid+']').attr('menuinfo',elem.minfo);
		$('[data-uid='+elem.datauid+']').find('input:first').attr('id',elem.minputid);
		if (elem.minfo!=undefined) {
			var minfo = jQuery.parseJSON(elem.minfo);
			if (minfo.mvisible!=undefined) $('#evvtmcb-'+minfo.evvtmenuid).prop('checked',minfo.mvisible==0 ? false : true);
		}
	}
	drag_menuinfo = [];
}

function evvtmDragStartTreeElement(moveinfo) {
	saveMenuInfo(moveinfo.sourceNode);
}

function evvtmDragEndTreeElement(moveinfo) {
	assignMenuInfo();
}

function sendMenuConfig() {
	VtigerJS_DialogBox.block();
	$('[name=evvtmenudo]').val('doSave');
	$('#evvtmenutree').val(treeToJson($('#treeview').data("kendoTreeView")));
	$('#menuconfigform').submit();
}

function sendDoDel() {
	if (jQuery('#evvtmenuid').val()!=0 && confirm(alert_arr.DELETE+jQuery('#mlabel').val()+alert_arr.RECORDS)) {
		VtigerJS_DialogBox.block();
		$('[name=evvtmenudo]').val('doDel');
		return true;
	} else {
		return false;
	}
}

function setModuleType() {
	$("#mlabel").show();
	$("#hidemvalue").hide();
	$("#hidemodname").show();
}

function setActionType() {
	$("#mlabel").show();
	$("#hidemvalue").show();
	$("#hidemodname").hide();
}

function menuTypeChange() {
	var value = $("#mtype").val();
	clear_evvtMenuForm(false);
	switch (value) {
	case 'menu':
		$("#mlabel").show();
		$("#hidemodname").hide();
		$("#hidemvalue").hide();
		break;
	case 'sep':
		$("#mlabel").hide();
		$("#hidemodname").hide();
		$("#hidemvalue").hide();
		break;
	case 'module':
		setModuleType();
		break;
	case 'url':
		setActionType();
		break;
	}
}

$(document).ready(function() {
	$("#treeview").kendoTreeView({
		//template: kendo.template($("#evvtMenuItem-template").html()),
        dragAndDrop: true,
        checkboxes: {
            checkChildren: true
        },
        change: evvtmSelectTreeElement,
        dragstart: evvtmDragStartTreeElement,
        dragend: evvtmDragEndTreeElement,
    });
    $("#mtype").kendoDropDownList({change: menuTypeChange});
    $("#modname").kendoDropDownList();
    $("#mpermission").kendoMultiSelect();
});
