/*************************************************************************************************
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
*************************************************************************************************/

if (window.cbRelatedListsLoaded == undefined) {
	window.cbRelatedListsLoaded = 'loaded';

	function editProductListPrice(id, pbid, price) {
		document.getElementById('status').style.display='inline';
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?action=ProductsAjax&file=EditListPrice&return_action=DetailView&return_module=PriceBooks&module=Products&record='+id+'&pricebook_id='+pbid+'&listprice='+price,
		}).done(function (response) {
			document.getElementById('status').style.display='none';
			document.getElementById('editlistprice').innerHTML= response;
		});
	}

	function gotoUpdateListPrice(id, pbid, proid) {
		document.getElementById('status').style.display='inline';
		document.getElementById('roleLay').style.display = 'none';
		var listprice=document.getElementById('list_price').value;
		var url = 'index.php?module=Products&action=ProductsAjax&file=UpdateListPrice&ajax=true&return_action=CallRelatedList&return_module=PriceBooks&record='+id+'&pricebook_id='+pbid+'&product_id='+proid+'&list_price='+listprice;
		gotourl(url);
		return false;
	}

	function OpenWindow(url) {
		openPopUp('xAttachFile', this, url, 'attachfileWin', 380, 375, 'menubar=no,toolbar=no,location=no,status=no,resizable=no');
	}

	function isRelatedListBlockLoaded(id, urldata) {
		var elem = document.getElementById(id);
		if (elem == null || typeof elem == 'undefined' || urldata.indexOf('order_by') != -1 || urldata.indexOf('start') != -1 ||
		urldata.indexOf('withCount') != -1 || urldata.indexOf('email_filter') != -1 || urldata.indexOf('cbcalendar_filter') != -1) {
			return false;
		}
		var tables = elem.getElementsByTagName('table');
		return tables.length > 0;
	}

	function loadRelatedListBlock(urldata, target, imagesuffix) {
		if ( document.getElementById('return_module').value == 'Campaigns') {
			var selectallActivation = document.getElementById(imagesuffix+'_selectallActivate').value;
			var excludedRecords = document.getElementById(imagesuffix+'_excludedRecords').value = document.getElementById(imagesuffix+'_excludedRecords').value;
			var numofRows = document.getElementById(imagesuffix+'_numOfRows').value;
		}
		var showdata = 'show_'+imagesuffix;
		var showdata_element = document.getElementById(showdata);

		var hidedata = 'hide_'+imagesuffix;
		var hidedata_element = document.getElementById(hidedata);
		if (isRelatedListBlockLoaded(target, urldata) == true) {
			jQuery('#'+target).show();
			jQuery(showdata_element).hide();
			showdata_element.parentElement.style.display = 'none';
			jQuery(hidedata_element).show();
			hidedata_element.parentElement.style.display = 'inline-block';
			jQuery('#delete_'+imagesuffix).show();
			return;
		}
		var indicator = 'indicator_'+imagesuffix;
		var indicator_element = document.getElementById(indicator);
		jQuery(indicator_element).show();
		document.getElementById('delete_'+imagesuffix).style.display='block';

		var target_element = document.getElementById(target);
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?'+urldata,
		}).done(function (response) {
			var responseData = trim(response);
			target_element.innerHTML=responseData;
			jQuery(target_element).show();
			jQuery(showdata_element).hide();
			showdata_element.parentElement.style.display = 'none';
			jQuery(hidedata_element).show();
			hidedata_element.parentElement.style.display = 'inline-block';
			jQuery(indicator_element).hide();
			if (document.getElementById('return_module').value == 'Campaigns') {
				var obj = document.getElementsByName(imagesuffix+'_selected_id');
				var relatedModule = imagesuffix.replace('Campaigns_', '');
				document.getElementById(relatedModule+'_count').innerHTML = numofRows;
				if (selectallActivation == 'true') {
					document.getElementById(imagesuffix+'_selectallActivate').value='true';
					jQuery('#'+imagesuffix+'_linkForSelectAll').show();
					document.getElementById(imagesuffix+'_selectAllRec').style.display='none';
					document.getElementById(imagesuffix+'_deSelectAllRec').style.display='inline';
					var exculdedArray=excludedRecords.split(';');
					if (obj) {
						var viewForSelectLink = showSelectAllLink(obj, exculdedArray);
						document.getElementById(imagesuffix+'_selectCurrentPageRec').checked = viewForSelectLink;
						document.getElementById(imagesuffix+'_excludedRecords').value = document.getElementById(imagesuffix+'_excludedRecords').value+excludedRecords;
					}
				} else {
					jQuery('#'+imagesuffix+'_linkForSelectAll').hide();
					//rel_toggleSelect(false,imagesuffix+'_selected_id',relatedModule);
				}
				updateParentCheckbox(obj, imagesuffix);
			}
			if (typeof RLColorizerList === 'function') {
				var relation = getQueryVariable(urldata, 'relation_id');
				if (relation != '') {
					jQuery.ajax({
						method: 'POST',
						url: 'index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=getRelatedListInfo&relation_id='+relation,
					}).done(function (response) {
						var resp = JSON.parse(response);
						var rlModule = resp.modulerel;
						RLColorizerList(rlModule);
					});
				} else {
					rlModule = imagesuffix.replace(document.getElementById('return_module').value + '_', '');
					RLColorizerList(rlModule);
				}
			}
		});
	}

	function getQueryVariable(urldata, variable) {
		var query =urldata;
		var vars = query.split('&');
		for (var i = 0; i < vars.length; i++) {
			var pair = vars[i].split('=');
			if (decodeURIComponent(pair[0]) == variable) {
				return decodeURIComponent(pair[1]);
			}
		}
		return '';
	}

	function hideRelatedListBlock(target, imagesuffix) {
		var showdata = 'show_'+imagesuffix;
		var showdata_element = document.getElementById(showdata);
		var hidedata = 'hide_'+imagesuffix;
		var hidedata_element = document.getElementById(hidedata);
		jQuery('#'+target).hide();
		jQuery('#hide_'+imagesuffix).hide();
		hidedata_element.parentElement.style.display = 'none';
		jQuery('#show_'+imagesuffix).show();
		showdata_element.parentElement.style.display = 'inline-block';
		jQuery('#delete_'+imagesuffix).hide();
	}

	function disableRelatedListBlock(urldata, target, imagesuffix) {
		jQuery('#indicator_'+imagesuffix).show();
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?'+urldata,
		}).done(function (response) {
			var responseData = trim(response);
			jQuery('#'+target).hide();
			jQuery('#delete_'+imagesuffix).hide();
			jQuery('#hide_'+imagesuffix).hide();
			jQuery('#show_'+imagesuffix).show();
			var showdata_element = document.getElementById('show_'+imagesuffix);
			showdata_element.parentElement.style.display = 'inline-block';
			jQuery('#indicator_'+imagesuffix).hide();
		});
	}

	function showHideStatus(sId, anchorImgId, sImagePath) {
		oObj = document.getElementById(sId);
		if (oObj.style.display == 'block') {
			oObj.style.display = 'none';
			if (anchorImgId !=null) {
				document.getElementById(anchorImgId).src = 'themes/images/inactivate.gif';
				document.getElementById(anchorImgId).alt = alert_arr.LBL_Show;
				document.getElementById(anchorImgId).title = alert_arr.LBL_Show;
				document.getElementById(anchorImgId).parentElement.className = 'exp_coll_block activate';
			}
		} else {
			oObj.style.display = 'block';
			if (anchorImgId !=null) {
				document.getElementById(anchorImgId).src = 'themes/images/activate.gif';
				document.getElementById(anchorImgId).alt = alert_arr.LBL_Hide;
				document.getElementById(anchorImgId).title = alert_arr.LBL_Hide;
				document.getElementById(anchorImgId).parentElement.className = 'exp_coll_block inactivate';
			}
		}
	}
} // cbRelatedListsLoaded
