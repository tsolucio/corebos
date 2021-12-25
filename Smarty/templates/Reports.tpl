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
{include file="Buttons_List.tpl"}</td>

<div id="reportContents">
	{include file="ReportContents.tpl"}
</div>
<!-- Reports Table Ends Here -->

<!-- POPUP LAYER FOR CREATE NEW REPORT -->
<div style="display: none; left: 193px; top: 106px;width:300px;" id="reportLay" class="slds-p-around_x-small layerPopup">
	<div class="slds-page-header">
		<div class="slds-grid">
			<div class="slds-col slds-size_1-of-2 slds-p-vertical_small">
				<div class="slds-page-header__col-title">
					<div class="slds-page-header__name">
						<div class="slds-text-title">
							<h1 id="cportatereor_info"> <strong> {$MOD.LBL_CREATE_REPORT} </strong> </h1>
						</div>
					</div>
				</div>
			</div>
			<div class="slds-col slds-size_1-of-2 slds-p-vertical_small slds-text-align_right">
				<svg class="slds-icon slds-icon_x-small slds-icon-text-default" aria-hidden="true" onClick="fninvsh('reportLay');">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use> 
				</svg>
			</div>
		</div>
	</div>
	<div class="slds-grid">
		<div class="slds-col slds-size_1-of-1 slds-p-vertical_small">
			<span> <strong> {$MOD.LBL_REPORT_MODULE} </strong> </span>
			<div class="slds-form-element">
				<div class="slds-form-element__control">
					<div class="slds-select_container">
						<select class="slds-select" name="selectModuleElement" id="selectModuleElement">
							{foreach item=modulelabel key=modulename from=$REPT_MODULES}
								<option value="{$modulename}">{$modulelabel}</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="slds-grid">
		<div class="slds-col slds-size_1-of-1 slds-p-vertical_small">
			<strong> {'Choose Report Type'|@getTranslatedString:'Reports'} </strong>
		</div>
	</div>
	<div class="slds-grid">
		<fieldset class="slds-form-element">
			<div class="slds-form-element__control">
				<span class="slds-radio">
				<input type="radio" name="cbreporttype" id="corebos" value="corebos" checked>
				<label class="slds-radio__label" for="corebos">
					<span class="slds-radio_faux"></span>
					<span class="slds-form-element__label"> {'Application Report'|@getTranslatedString:'Reports'} </span>
				</label>
				</span>
				<span class="slds-radio">
				<input type="radio" name="cbreporttype" id="external" value="external">
				<label class="slds-radio__label" for="external">
					<span class="slds-radio_faux"></span>
					<span class="slds-form-element__label"> {'External Application'|@getTranslatedString:'Reports'} </span>
				</label>
				</span>
				<span class="slds-radio">
				<input type="radio" name="cbreporttype" id="crosstabsql" value="crosstabsql">
				<label class="slds-radio__label" for="crosstabsql">
					<span class="slds-radio_faux"></span>
					<span class="slds-form-element__label"> {'Cross Tab'|@getTranslatedString:'Reports'} </span>
				</label>
				</span>
				<span class="slds-radio">
				<input type="radio" name="cbreporttype" id="directsql" value="directsql">
				<label class="slds-radio__label" for="directsql">
					<span class="slds-radio_faux"></span>
					<span class="slds-form-element__label"> {'Direct SQL Statement'|@getTranslatedString:'Reports'} </span>
				</label>
				</span>
			</div>
		</fieldset>
	</div>
	<div class="slds-grid">
		<div class="slds-col slds-size_1-of-2 slds-p-vertical_small slds-align_absolute-center">
			<input name="save" value=" &nbsp;{$APP.LBL_CREATE_BUTTON_LABEL}&nbsp; " class="slds-button slds-button_brand" onClick="CreateReport('selectModuleElement'); fninvsh('reportLay');" type="button">
		</div>
		<div class="slds-col slds-size_1-of-2 slds-p-vertical_small slds-align_absolute-center">
			<input name="cancel" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="slds-button slds-button_destructive" onclick="fninvsh('reportLay');" type="button">
		</div>
	</div>
</div>
<!-- END OF POPUP LAYER -->

<!-- Add new Folder UI starts -->
<div id="orgLay" style="display:none;" class="layerPopup slds-p-around_x-small">
	<div class="slds-page-header">
		<div class="slds-grid">
			<div class="slds-col slds-size_1-of-2 slds-p-vertical_small">
				<div class="slds-page-header__col-title">
					<div class="slds-page-header__name">
						<div class="slds-text-title">
							<h1 id="cportatereor_info"> <strong> {$MOD.LBL_ADD_NEW_GROUP} </strong> </h1>
						</div>
					</div>
				</div>
			</div>
			<div class="slds-col slds-size_1-of-2 slds-p-vertical_small slds-text-align_right">
				<svg class="slds-icon slds-icon_x-small slds-icon-text-default" aria-hidden="true" onClick="closeEditReport();">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use> 
				</svg>
			</div>
		</div>
	</div>
	<div class="slds-grid">
		<div class="slds-col slds-size_1-of-1 slds-p-vertical_small">
			<div class="slds-form-element">
				<label class="slds-form-element__label" for="text-input-id-1"> {$MOD.LBL_REP_FOLDER_NAME} </label>
					<div class="slds-form-element__control">
						<input id="folder_id" class="slds-input" name="folderId" type="hidden" value=''>
						<input id="fldrsave_mode" name="folderId" type="hidden" value='save' class="slds-input">
						<input id="folder_name" name="folderName" type="text" class="slds-input">
					</div>
			</div>
		</div>
	</div>
	<div class="slds-grid">
		<div class="slds-col slds-size_1-of-1 slds-p-vertical_small">
			<div class="slds-form-element">
				<label class="slds-form-element__label" for="text-input-id-1"> {$MOD.LBL_REP_FOLDER_DESC} </label>
					<div class="slds-form-element__control">
						<input id="folder_desc" name="folderDesc" type="text" class="slds-input">
					</div>
			</div>
		</div>
	</div>
	<div class="slds-grid">
		<div class="slds-col slds-size_1-of-2 slds-p-vertical_small slds-align_absolute-center">
			<input name="save" value=" {$APP.LBL_SAVE_BUTTON_LABEL} " class="slds-button slds-button_brand" onClick="AddFolder();" type="button">
		</div>
		<div class="slds-col slds-size_1-of-2 slds-p-vertical_small slds-align_absolute-center">
			<input name="cancel" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="slds-button slds-button_destructive" onclick="closeEditReport();" type="button">
		</div>
	</div>
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
