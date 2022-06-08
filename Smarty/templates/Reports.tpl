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
<div id="orgLay" style="display:none;cursor:move;" class="layerPopup slds-p-around_x-small">
	<div class="slds-page-header">
		<div class="slds-grid">
			<div class="slds-col slds-size_1-of-2 slds-p-vertical_small">
				<div class="slds-page-header__col-title">
					<div class="slds-page-header__name">
						<div class="slds-text-title">
							<h1> <strong id="editfolder_info"> {$MOD.LBL_ADD_NEW_GROUP} </strong> </h1>
						</div>
					</div>
				</div>
			</div>
			<div class="slds-col slds-size_1-of-2 slds-p-vertical_small slds-text-align_right" style="cursor:pointer;">
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
<script>
var i18nReportStrings = {
	'LBL_ADD_NEW_GROUP': '{$MOD.LBL_ADD_NEW_GROUP}',
	'DELETE_FOLDER_CONFIRMATION': '{$APP.DELETE_FOLDER_CONFIRMATION}',
	'FOLDERNAME_CANNOT_BE_EMPTY': '{$APP.FOLDERNAME_CANNOT_BE_EMPTY}',
	'FOLDER_NAME_ALLOW_20CHARS': '{$APP.FOLDER_NAME_ALLOW_20CHARS}',
	'FOLDER_NAME_ALREADY_EXISTS': '{$APP.FOLDER_NAME_ALREADY_EXISTS}',
	'SPECIAL_CHARS_NOT_ALLOWED': '{$APP.SPECIAL_CHARS_NOT_ALLOWED}',
	'LBL_RENAME_FOLDER': ' {$MOD.LBL_RENAME_FOLDER} ',
	'DELETE_CONFIRMATION': '{$APP.DELETE_CONFIRMATION}',
	'RECORDS': '{$APP.RECORDS}',
	'SELECT_ATLEAST_ONE_REPORT': '{$APP.SELECT_ATLEAST_ONE_REPORT}',
	'DELETE_REPORT_CONFIRMATION': '{$APP.DELETE_REPORT_CONFIRMATION}',
	'MOVE_REPORT_CONFIRMATION': '{$APP.MOVE_REPORT_CONFIRMATION}',
	'FOLDER': '{$APP.FOLDER}',
	'SELECT_ATLEAST_ONE_REPORT': '{$APP.SELECT_ATLEAST_ONE_REPORT}',
}
</script>
