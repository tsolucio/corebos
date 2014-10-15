/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

document.write("<script type='text/javascript' src='include/js/Merge.js'></"+"script>");
document.write("<script type='text/javascript' src='modules/RecycleBin/language/en_us.lang.js'></"+"script>");

function callRBSearch(searchtype)
{
	for(i=1;i<=26;i++)
    {
		var data_td_id = 'alpha_'+ eval(i);
        getObj(data_td_id).className = 'searchAlph';
    }
    gPopupAlphaSearchUrl = '';
	search_fld_val= $('bas_searchfield').options[$('bas_searchfield').selectedIndex].value;
	search_txt_val=document.basicSearch.search_text.value;
	var urlstring = '';
	if(searchtype == 'Basic')
	{
		urlstring = 'search_field='+search_fld_val+'&searchtype=BasicSearch&search_text='+search_txt_val+'&';
	}
	var selectedmodule = $('select_module').options[$('select_module').selectedIndex].value 
	urlstring += 'selected_module='+selectedmodule;
        	new Ajax.Request(
		'index.php',
		{
			queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody:urlstring +'&query=true&module=RecycleBin&action=RecycleBinAjax&file=index&ajax=true&mode=ajax',
			onComplete: function(response) 
			{
				$("status").style.display="none";
                $("modules_datas").innerHTML=response.responseText;
				$("search_ajax").innerHTML = '';
			}
	      }
        );

}
function changeModule(pickmodule)
{
	$("status").style.display="inline";
	var module=pickmodule.options[pickmodule.options.selectedIndex].value;
	new Ajax.Request(
                'index.php',
                {
			queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'action=RecycleBinAjax&module=RecycleBin&mode=ajax&file=ListView&selected_module='+module,
	                onComplete: function(response) 
					{
						$("status").style.display="none";
						$("modules_datas").innerHTML=response.responseText;
						$("searchAcc").innerHTML = $("search_ajax").innerHTML; 
						$("search_ajax").innerHTML = '';
					}
                }
        );
}

function massRestore()
{
	var excludedRecords = document.getElementById('excludedRecords').value;
	var select_options  =  document.getElementById('allselectedboxes').value;
	var searchurl = document.getElementById('search_url').value;
	var numOfRows = document.getElementById('numOfRows').value;
	var idstring = "";
	if(select_options=='all'){
		idstring = select_options;
		var skiprecords = excludedRecords.split(";");
		var count = skiprecords.length;
		if(count > 1){
			count = numOfRows - count + 1;
		}
		else{
			count = numOfRows;
		}
	} else {
		var x = select_options.split(";");
		var count=x.length
		if (count > 1)
		{
			document.getElementById('idlist').value=select_options;
			idstring = select_options;
		} else{
			alert(mod_alert_arr.SELECT_ATLEAST_ONE_ENTITY);
			return false;
		}
		count = count-1;
	}
	if(count > getMaxMassOperationLimit())
	{
		var confirm_str = alert_arr.MORE_THAN_500;
		if(confirm(confirm_str)) var confirm_status = true;
		else return false;
	}
	else confirm_status = true;

	if(confirm_status){
		var selectmodule = $('selected_module').value;
		var selectmoduletranslated =  $('selected_module_translated').value;
		if(confirm(mod_alert_arr.MSG_RESTORE_CONFIRMATION + " " + count + " " + selectmoduletranslated + "?"))
		{
			$("status").style.display="inline";
			new Ajax.Request(
				'index.php',
				{
					queue: {
						position: 'end',
						scope: 'command'
					},
					method: 'post',
					postBody: 'action=RecycleBinAjax&module=RecycleBin&mode=ajax&file=Restoration&idlist='+idstring+'&selectmodule='+selectmodule+'&excludedRecords='+excludedRecords,
					onComplete: function(response)
					{
						$("status").style.display="none";
						$("modules_datas").innerHTML=response.responseText;
						$("search_ajax").innerHTML = '';
					}
				}
				);
		}
	}
}

function restore(entityid,select_module)
{
	if(confirm(mod_alert_arr.MSG_RESTORE_CONFIRMATION + " " + select_module + "?"))
	{
		$("status").style.display="inline";
		new Ajax.Request(
			'index.php',
	        {
				queue: {position: 'end', scope: 'command'},
	            method: 'post',
	            postBody: 'action=RecycleBinAjax&module=RecycleBin&mode=ajax&file=Restoration&idlist='+entityid+'&selectmodule='+select_module,
		        onComplete: function(response) {
		            $("status").style.display="none";
		            $("modules_datas").innerHTML=response.responseText;
					$("search_ajax").innerHTML = '';
	            }
			}
		);
	}
}

function getListViewEntries_js(module,url)
{
	var all_selected = $('allselectedboxes').value;
	var excludedRecords = $('excludedRecords').value;

	$("status").show();
	var selected_module = $("select_module").value;
	var urlstring = "&selected_module=" + selected_module;
	<!-- Ticket 6330 -->
	if($('search_url').value!='')
		urlstring = $('search_url').value+"&selected_module="+selected_module;

	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody : "module=RecycleBin&action=RecycleBinAjax&file=ListView&mode=ajax&ajax=true&"+url+urlstring+"&allselobjs="+all_selected+"&excludedRecords="+excludedRecords,
			onComplete: function(response) {
				$("status").hide();
				if($("modules_datas")) {
					$("modules_datas").innerHTML = response.responseText;
				}
				if(all_selected == 'all'){
					$('linkForSelectAll').show();
					$('selectAllRec').style.display='none';
					$('deSelectAllRec').style.display='inline';
					var exculdedArray=excludedRecords.split(';');
					var obj = document.getElementsByName('selected_id');
					if (obj) {
						var viewForSelectLink = showSelectAllLink(obj,exculdedArray);
						$('selectCurrentPageRec').checked = viewForSelectLink;
						$('allselectedboxes').value='all';
						$('excludedRecords').value = $('excludedRecords').value+excludedRecords;
					}
				}else{
					$('linkForSelectAll').hide();
					update_selected_checkbox();
				}
			}
		}
	);
}

function alphabetic(module,url,dataid)
{
        for(i=1;i<=26;i++)
        {
                var data_td_id = 'alpha_'+ eval(i);
                getObj(data_td_id).className = 'searchAlph';

        }
	var selectedmodule = $('select_module').options[$('select_module').selectedIndex].value 
	url += '&selected_module='+selectedmodule;
	getObj(dataid).className = 'searchAlphselected';
	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody:"module="+module+"&action="+module+"Ajax&file=index&mode=ajax&ajax=true&"+url,
			onComplete: function(response) {
				$("status").style.display="none";
				$("modules_datas").innerHTML=response.responseText;
				$("search_ajax").innerHTML = '';
			}
		}
	);
}

function callEmptyRecyclebin() {
	document.getElementById('rb_empty_conf_id').style.display = 'block';
}

function emptyRecyclebin(id) {
	if($(id)) $(id).hide();
	VtigerJS_DialogBox.progress();
	var pickmodule = $('select_module');
	var module=pickmodule.options[pickmodule.options.selectedIndex].value;
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
            postBody:"module=RecycleBin&action=RecycleBinAjax&file=EmptyRecyclebin&mode=ajax&ajax=true&selected_module="+module,
			onComplete: function(response) {
                $("status").style.display="none";
               	$("modules_datas").innerHTML= response.responseText;
				$("searchAcc").innerHTML = $("search_ajax").innerHTML; 
				$("search_ajax").innerHTML = '';
				VtigerJS_DialogBox.hideprogress();
			}
		}
	);	
}
