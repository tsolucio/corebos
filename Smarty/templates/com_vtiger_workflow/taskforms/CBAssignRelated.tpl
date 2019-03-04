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
	var selectedUser = '{if isset($task->username)}{$task->username}{/if}';
{literal}
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
		validator.mandatoryFields = ['relmodule','username'];

		vtinst.extendSession(handleError(function(result){

			jQuery.get('index.php', {
					module:'com_vtiger_workflow',
					action:'com_vtiger_workflowAjax',
					file:'WorkflowComponents',
					ajax:'true',
					modulename:moduleName,
					mode:'getrelatedmodules',
					relationtype:'1:N'
				},
				function (result) {
					result = JSON.parse(result);
					var entitytypes = jQuery('#relmodule');
					if(result != null) {
						jQuery.each(result, function(entityname, enamei18n){
							entitytypes.append('<option value="'+entityname+'">'+enamei18n+'</option>');
						});

						if (selectedEntityType != '') {
							entitytypes.val(selectedEntityType);
						}
					}

					jQuery('#entity_type-busyicon').hide();
					entitytypes.show();
				}
			);
			
			jQuery.get('index.php', {
					module:'com_vtiger_workflow',
					action:'com_vtiger_workflowAjax',
					file:'WorkflowComponents', ajax:'true',
					modulename:moduleName, mode:'getownerslist'},
				function (result){
					result = JSON.parse(result);
					var uname = jQuery('#username');
					if (result != null) {
						jQuery.each(result, function (entityid, enamei18n) {
							uname.append('<option value="'+enamei18n.id+'">'+enamei18n.label+'</option>');
						});
					}
					uname.append('<option value="assigneduser">{/literal}{'Assigned User'|@getTranslatedString:$module->name}{literal}</option>');
					if (selectedUser != '') {
						uname.val(selectedUser);
					}
					jQuery('#entity_type-busyicon').hide();
					uname.show();
				}
			);

			jQuery("#save").bind("click", function(){
				var validateFieldValues = new Array();
				for (var fieldName in validator.validateFieldData) {
					if (validateFieldValues.indexOf(fieldName) < 0) {
						delete validator.validateFieldData[fieldName];
					}
				}
			});
		}));
	});
</script>
{/literal}
<div style="float: left;">
	<h2>{'LBL_SELECT_MODULE'|@getTranslatedString:'Settings'}</h2>
	<span id="entity_type-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
	<select id="relmodule" name="relmodule" style="display:none;">
		<option value=''>{'LBL_SELECT_ENTITY_TYPE'|@getTranslatedString:$module->name}</option>
	</select>
</div>
<div style="float: left;margin-left:10px;">
	<h2>{'LBL_SELECT_USER_BUTTON_LABEL'|@getTranslatedString:$module->name}</h2>
	<select id="username" name="username" style="display:none;">
		<option value=''>-- {'LBL_SELECT_USER_BUTTON_LABEL'|@getTranslatedString:$module->name} --</option>
	</select>
</div>