{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}

{*<!-- module header -->*}
<script type="text/javascript" src="modules/Reports/Reports.js"></script>

<!-- Toolbar -->
<TABLE border=0 cellspacing=0 cellpadding=0 width=100% class=background>
	<tr>
		<td class=small>
			<table border=0 cellspacing=0 cellpadding=0>
				<tr>
					<td>{include file="Buttons_List.tpl"}</td>
					<td>
						<table class="slds-table slds-no-row-hover background">
							<tr class="LD_buttonList">
								<th scope="col">
									<div class="globalCreateContainer oneGlobalCreate">
										<div class="forceHeaderMenuTrigger">
											<div class="LB_Button slds-truncate">
												<a href="javascript:;" onclick="gcurrepfolderid=0;fnvshobj(this,'reportLay');">
													<img src="{'reportsCreate.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_CREATE_REPORT}..." title="{$MOD.LBL_CREATE_REPORT}..." border=0>
												</a>
											</div>
										</div>
									</div>
								</th>
								<th scope="col">
									<div class="globalCreateContainer oneGlobalCreate">
										<div class="forceHeaderMenuTrigger">
											<div class="LB_Button slds-truncate">
												<a href="javascript:;" onclick="createrepFolder(this,'orgLay');"><img src="{'reportsFolderCreate.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.Create_New_Folder}..." title="{$MOD.Create_New_Folder}..." border=0></a>
											</div>
										</div>
									</div>
								</th>
								<th scope="col">
									<div class="globalCreateContainer oneGlobalCreate">
										<div class="forceHeaderMenuTrigger">
											<div class="LB_Button slds-truncate">
												<a href="javascript:;" onclick="fnvshobj(this,'folderLay');"><img src="{'reportsMove.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.Move_Reports}..." title="{$MOD.Move_Reports}..." border=0></a>
											</div>
										</div>
									</div>
								</th>
								<th scope="col">
									<div class="globalCreateContainer oneGlobalCreate">
										<div class="forceHeaderMenuTrigger">
											<div class="LB_Button slds-truncate">
												<a href="javascript:;" onClick="massDeleteReport();"><img src="{'reportsDelete.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_DELETE_FOLDER}..." title="{$MOD.Delete_Report}..." border=0></a>
											</div>
										</div>
									</div>
								</th>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</TABLE>

<div id="reportContents">
	{include file="ReportContents.tpl"}
</div>
<!-- Reports Table Ends Here -->

<!-- POPUP LAYER FOR CREATE NEW REPORT -->
<div style="display: none; left: 193px; top: 106px;width:300px;" id="reportLay" class="layerPopup">
	<table class="slds-table slds-no-row-hover layerHeadingULine">
		<tr class="slds-text-title--header">
			<th scope="col" class="genHeaderSmall">
				<div class="slds-truncate moduleName" id="cportatereor_info">{$MOD.LBL_CREATE_REPORT}</div>
			</th>
			<th scope="col" style="padding: .5rem;text-align: right;">
				<div class="slds-truncate">
					<a href="javascript:;" onClick="fninvsh('reportLay');">
						<img src="{'close.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border="0">
					</a>
				</div>
			</th>
		</tr>
	</table>
	<table class="slds-table slds-no-row-hover slds-table--bordered new-report-table">
		<tr class="slds-line-height--reset">
			<td class="dvtCellLabel text-left" nowrap><b>{$MOD.LBL_REPORT_MODULE}</b></td>
		</tr>
		<tr class="slds-line-height--reset">
			<td class="dvtCellInfo">
				<select name="selectModuleElement" id="selectModuleElement" class="slds-select">
					{foreach item=modulelabel key=modulename from=$REPT_MODULES}
						<option value="{$modulename}">{$modulelabel}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td class="dvtCellLabel text-left"><b>{'Choose Report Type'|@getTranslatedString:'Reports'}</b></td>
		</tr>
		<tr>
			<td class="small">
				<span class="slds-radio">
					<input type="radio" name="cbreporttype" id="Application Report" value="corebos" checked>
					<label class="slds-radio__label" for="Application Report">
						<span class="slds-radio--faux"></span>
					</label>
					<span class="slds-form-element__label">&nbsp;{'Application Report'|@getTranslatedString:'Reports'}</span>
				</span>
			</td>
		</tr>
		<tr>
			<td class="small">
				<span class="slds-radio">
					<input type="radio" name="cbreporttype" id="External Application" value="external">
					<label class="slds-radio__label" for="External Application">
						<span class="slds-radio--faux"></span>
					</label>
					<span class="slds-form-element__label">&nbsp;{'External Application'|@getTranslatedString:'Reports'}</span>
				</span>
			</td>
		</tr>
		<tr>
			<td class="small">
				<span class="slds-radio">
					<input type="radio" name="cbreporttype" id="Cross Tab" value="crosstabsql">
					<label class="slds-radio__label" for="Cross Tab">
						<span class="slds-radio--faux"></span>
					</label>
					<span class="slds-form-element__label">&nbsp;{'Cross Tab'|@getTranslatedString:'Reports'}</span>
				</span>
			</td>
		</tr>
		<!--
			<tr>
			<td class="small"><input type="radio" name="cbreporttype" value="pivottable">&nbsp;{'Pivot Table'|@getTranslatedString:'Reports'}</td>
			</tr>
		-->
		<tr>
			<td class="small">
				<span class="slds-radio">
					<input type="radio" name="cbreporttype" id="Direct SQL Statement" value="directsql">
					<label class="slds-radio__label" for="Direct SQL Statement">
						<span class="slds-radio--faux"></span>
					</label>
					<span class="slds-form-element__label">&nbsp;{'Direct SQL Statement'|@getTranslatedString:'Reports'}</span>
				</span>
			</td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport create-report-table">
		<tr>
			<td class="small" align="center">
				<input name="save" value=" &nbsp;{$APP.LBL_CREATE_BUTTON_LABEL}&nbsp; " class="slds-button--small slds-button  slds-button_success" onClick="CreateReport('selectModuleElement'); fninvsh('reportLay');" type="button">&nbsp;&nbsp;
				<input name="cancel" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="slds-button--destructive slds-button slds-button--small" onclick="fninvsh('reportLay');" type="button">
			</td>
		</tr>
	</table>
</div>
<!-- END OF POPUP LAYER -->

<!-- Add new Folder UI starts -->
<div id="orgLay" style="display:none;" class="layerPopup">
	<table class="slds-table slds-no-row-hover layerHeadingULine">
		<tr class="slds-text-title--header">
			<th scope="col" class="genHeaderSmall">
				<div class="slds-truncate moduleName" id="editfolder_info">{$MOD.LBL_ADD_NEW_GROUP}</div>
			</th>
			<th scope="col" style="padding: .5rem;text-align: right;">
				<div class="slds-truncate">
					<a href="javascript:;" onClick="closeEditReport();">
						<img src="{'close.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border="0">
					</a>
				</div>
			</th>
		</tr>
	</table>
	<table class="slds-table slds-no-row-hover slds-table--bordered new-report-table">
		<tr class="slds-line-height--reset">
			<td align="right" nowrap class="dvtCellLabel small"><b>{$MOD.LBL_REP_FOLDER_NAME} </b></td>
			<td align="left" class="dvtCellInfo">
				<input id="folder_id" name="folderId" type="hidden" value=''>
				<input id="fldrsave_mode" name="folderId" type="hidden" value='save'>
				<input id="folder_name" name="folderName" type="text" class="slds-input">
			</td>
		</tr>
		<tr class="slds-line-height--reset">
			<td class="dvtCellLabel" align="right" nowrap><b>{$MOD.LBL_REP_FOLDER_DESC} </b></td>
			<td class="dvtCellInfo" align="left"><input id="folder_desc" name="folderDesc" type="text" class="slds-input"></td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
		<tr>
			<td class="small" align="center" style="padding: 5px;">
				<input name="save" value=" &nbsp;{$APP.LBL_SAVE_BUTTON_LABEL}&nbsp; " class="slds-button slds-button_success slds-button--small" onClick="AddFolder();" type="button">&nbsp;&nbsp;
				<input name="cancel" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="slds-button slds-button--destructive slds-button--small" onclick="closeEditReport();" type="button">
			</td>
		</tr>
	</table>
</div>
<!-- Add new folder UI ends -->

{*<!-- Contents -->*}
{literal}
<script>
function createrepFolder(oLoc,divid)
{
	{/literal}
	document.getElementById('editfolder_info').innerHTML=' {$MOD.LBL_ADD_NEW_GROUP} ';
	{literal}
	getObj('fldrsave_mode').value = 'save';
	document.getElementById('folder_id').value = '';
	document.getElementById('folder_name').value = '';
	document.getElementById('folder_desc').value='';
	fnvshobj(oLoc,divid);
}
function DeleteFolder(id)
{
	var title = 'folder'+id;
	var fldr_name = getObj(title).innerHTML;
	{/literal}
	if(confirm("{$APP.DELETE_FOLDER_CONFIRMATION}"+fldr_name +"' ?"))
	{literal}
	{
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?action=ReportsAjax&mode=ajax&file=DeleteReportFolder&module=Reports&record='+id
		}).done(function (response) {
			var item = trim(response);
			if(item.charAt(0)=='<')
				getObj('reportContents').innerHTML = item;
			else
				alert(item);
		});
	}
	else
	{
		return false;
	}
}

function AddFolder()
{
	if(getObj('folder_name').value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0)
	{
		{/literal}
		alert('{$APP.FOLDERNAME_CANNOT_BE_EMPTY}');
		return false;
		{literal}
	}
	else if(getObj('folder_name').value.replace(/^\s+/g, '').replace(/\s+$/g, '').length > 20 )
	{
		{/literal}
		alert('{$APP.FOLDER_NAME_ALLOW_20CHARS}');
		return false;
		{literal}
	}
	else if((getObj('folder_name').value).match(/['"<>/\+]/) || (getObj('folder_desc').value).match(/['"<>/\+]/))
	{
		alert(alert_arr.SPECIAL_CHARS+' '+alert_arr.NOT_ALLOWED+alert_arr.NAME_DESC);
		return false;
	}
	/*else if((!CharValidation(getObj('folder_name').value,'namespace')) || (!CharValidation(getObj('folder_desc').value,'namespace')))
	{
			alert(alert_arr.NO_SPECIAL +alert_arr.NAME_DESC);
			return false;
	}*/
	else
	{
		var foldername = encodeURIComponent(getObj('folder_name').value);
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?action=ReportsAjax&mode=ajax&file=CheckReport&module=Reports&check=folderCheck&folderName='+foldername
		}).done(function (response) {
			var folderid = getObj('folder_id').value;
			var resresult =response.split("::");
			var mode = getObj('fldrsave_mode').value;
			if(resresult[0] != 0 && mode =='save' && resresult[0] != 999)
			{
				{/literal}
				alert("{$APP.FOLDER_NAME_ALREADY_EXISTS}");
				return false;
				{literal}
			}
			else if(((resresult[0] != 1 && resresult[0] != 0) || (resresult[0] == 1 && resresult[0] != 0 && resresult[1] != folderid )) && mode =='Edit' && resresult[0] != 999)
			{
				{/literal}
				alert("{$APP.FOLDER_NAME_ALREADY_EXISTS}");
				return false;
				{literal}
			}
			else if(response == 999) // 999 check for special chars
			{
				{/literal}
				alert("{$APP.SPECIAL_CHARS_NOT_ALLOWED}");
				return false;
				{literal}
			}
			else
			{
				fninvsh('orgLay');
				var folderdesc = encodeURIComponent(getObj('folder_desc').value);
				getObj('folder_name').value = '';
				getObj('folder_desc').value = '';
				foldername = foldername.replace(/^\s+/g, '').replace(/\s+$/g, '');
				foldername = foldername.replace(/&/gi,'*amp*');
				folderdesc = folderdesc.replace(/^\s+/g, '').replace(/\s+$/g, '');
				folderdesc = folderdesc.replace(/&/gi,'*amp*');
				if(mode == 'save')
				{
					url ='&savemode=Save&foldername='+foldername+'&folderdesc='+folderdesc;
				}
				else
				{
					var folderid = getObj('folder_id').value;
					url ='&savemode=Edit&foldername='+foldername+'&folderdesc='+folderdesc+'&record='+folderid;
				}
				getObj('fldrsave_mode').value = 'save';
				jQuery.ajax({
					method: 'POST',
					url: 'index.php?action=ReportsAjax&mode=ajax&file=SaveReportFolder&module=Reports'+url
				}).done(function (response) {
					var item = response;
					getObj('reportContents').innerHTML = item;
				});
			}
		});
	}
}

function EditFolder(id,name,desc)
{
{/literal}
	document.getElementById('editfolder_info').innerHTML= ' {$MOD.LBL_RENAME_FOLDER} ';
{literal}
	getObj('folder_name').value = name;
	getObj('folder_desc').value = desc;
	getObj('folder_id').value = id;
	getObj('fldrsave_mode').value = 'Edit';
}
function massDeleteReport()
{
	var folderids = getObj('folder_ids').value;
	var folderid_array = folderids.split(',')
	var idstring = '';
	var count = 0;
	for(i=0;i < folderid_array.length;i++)
	{
		var selectopt_id = 'selected_id'+folderid_array[i];
		var objSelectopt = getObj(selectopt_id);
		if(objSelectopt != null)
		{
			var length_folder = getObj(selectopt_id).length;
			if(length_folder != undefined)
			{
				var cur_rep = getObj(selectopt_id);
				for(row = 0; row < length_folder ; row++)
				{
					var currep_id = cur_rep[row].value;
					if(cur_rep[row].checked)
					{
						count++;
						idstring = currep_id +':'+idstring;
					}
				}
			} else {
				if(getObj(selectopt_id).checked)
				{
					count++;
					idstring = getObj(selectopt_id).value +':'+idstring;
				}
			}
		}
	}
	if(idstring != '')
	{
		{/literal}
		if(confirm("{$APP.DELETE_CONFIRMATION}"+count+"{$APP.RECORDS}"))
		{literal}
		{
			jQuery.ajax({
				method: 'POST',
				url: 'index.php?action=ReportsAjax&mode=ajax&file=Delete&module=Reports&idlist='+idstring
			}).done(function (response) {
				var item = response;
				getObj('customizedrep').innerHTML = item;
			});
		} else {
			return false;
		}
	}else
	{
		{/literal}
		alert('{$APP.SELECT_ATLEAST_ONE_REPORT}');
		return false;
		{literal}
	}
}
function DeleteReport(id)
{
	{/literal}
	if(confirm("{$APP.DELETE_REPORT_CONFIRMATION}"))
	{literal}
	{
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?action=ReportsAjax&file=Delete&module=Reports&record='+id
		}).done(function (response) {
			getObj('reportContents').innerHTML = response;
		});
	} else {
		return false;
	}
}
function MoveReport(id,foldername)
{
	fninvsh('folderLay');
	var folderids = getObj('folder_ids').value;
	var folderid_array = folderids.split(',')
	var idstring = '';
	var count = 0;
	for(i=0;i < folderid_array.length;i++)
	{
		var selectopt_id = 'selected_id'+folderid_array[i];
		var objSelectopt = getObj(selectopt_id);
		if(objSelectopt != null)
		{
			var length_folder = getObj(selectopt_id).length;
			if(length_folder != undefined)
			{
				var cur_rep = getObj(selectopt_id);
				for(row = 0; row < length_folder ; row++)
				{
					var currep_id = cur_rep[row].value;
					if(cur_rep[row].checked)
					{
						count++;
						idstring = currep_id +':'+idstring;
					}
				}
			} else {
				if(getObj(selectopt_id).checked)
				{
					count++;
					idstring = getObj(selectopt_id).value +':'+idstring;
				}
			}
		}
	}
	if(idstring != '')
	{
		{/literal}
		if(confirm("{$APP.MOVE_REPORT_CONFIRMATION}"+foldername+"{$APP.FOLDER}"))
		{literal}
		{
			jQuery.ajax({
				method: 'POST',
				url: 'index.php?action=ReportsAjax&file=ChangeFolder&module=Reports&folderid='+id+'&idlist='+idstring
			}).done(function (response) {
				getObj('reportContents').innerHTML = response;
			});
		}else
		{
			return false;
		}
	}else
	{
		{/literal}
		alert('{$APP.SELECT_ATLEAST_ONE_REPORT}');
		return false;
		{literal}
	}
}
</script>
{/literal}
