{*<!--
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
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************//
-->*}
<script type="text/javascript" charset="utf-8">
	var moduleName = '{$entityName}';
	var selectedEntityType = '{if isset($task->relmodule)}{$task->relmodule}{/if}';
{literal}
	var searchConditions = [
		{"groupid":"1",
		 "columnname":"vtiger_cbmap:maptype:maptype:cbMap_Map_Type:V",
		 "comparator":"e",
		 "value":"Condition Expression",
		 "columncondition":"or"},
		{"groupid":"1",
		 "columnname":"vtiger_cbmap:maptype:maptype:cbMap_Map_Type:V",
		 "comparator":"e",
		 "value":"Condition Query",
		 "columncondition":""}
	];
	var advSearch = '&query=true&searchtype=advance&advft_criteria='+convertArrayOfJsonObjectsToString(searchConditions);
	var SpecialSearch = encodeURI(advSearch);
	var vtinst = new VtigerWebservices('webservice.php');

	function errorDialog(message){
		alert(message);
	}

	function handleError(fn){
		return function(status, result){
			if(status){
				fn(result);
			}else{
				errorDialog('Failure:'+result);
			}
		};
	}

	jQuery(document).ready(function(){
		validator.mandatoryFields = ['relmodule'];

		vtinst.extendSession(handleError(function(result){

			jQuery.get('index.php', {
					module:'com_vtiger_workflow',
					action:'com_vtiger_workflowAjax',
					file:'WorkflowComponents', ajax:'true',
					modulename:moduleName, mode:'getrelatedmodules'},
				function(result){
					result = JSON.parse(result);
					var entitytypes = jQuery('#relmodule');
					if(result != null) {
						jQuery.each(result, function(entityname, enamei18n){
							entitytypes.append('<option value="'+entityname+'">'+enamei18n+'</option>');
						});

						if(selectedEntityType != "") {
							entitytypes.val(selectedEntityType);
						}
					}

					jQuery('#entity_type-busyicon').hide();
					entitytypes.show();
				}
			);

			jQuery("#save").bind("click", function(){
				var validateFieldValues = new Array();
				for(var fieldName in validator.validateFieldData) {
					if(validateFieldValues.indexOf(fieldName) < 0) {
						delete validator.validateFieldData[fieldName];
					}
				}
			});
		}));
	});
</script>
{/literal}
<h2>{'LBL_SELECT_MODULE'|@getTranslatedString:'Settings'}</h2>
<span id="entity_type-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
<select id="relmodule" name="relmodule" style="display:none;">
	<option value=''>{'LBL_SELECT_ENTITY_TYPE'|@getTranslatedString:'com_vtiger_workflow'}</option>
</select>
<br /><br />
<input id="bmapid" name="bmapid" type="hidden" value="{$task->bmapid}">
<input id="bmapid_display" name="bmapid_display" readonly="" style="border:1px solid #bababa;" type="text" value="{$task->bmapid_display}">&nbsp;
<img src="{'select.gif'|@vtiger_imageurl:$THEME}" tabindex="1" alt="{'LBL_SELECT'|@getTranslatedString}" title="{'LBL_SELECT'|@getTranslatedString}"
 onclick="return window.open('index.php?module=cbMap&action=Popup&html=Popup_picker&form=new_task&forfield=bmapid&srcmodule=GlobalVariable'+SpecialSearch,'vtlibui10wf','width=680,height=602,resizable=0,scrollbars=0,top=150,left=200');" style="cursor:hand;cursor:pointer" align="absmiddle">&nbsp;
<input type="image" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}"
alt="{'LBL_CLEAR'|@getTranslatedString}" title="{'LBL_CLEAR'|@getTranslatedString}" onClick="this.form.bmapid.value=''; this.form.bmapid_display.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;