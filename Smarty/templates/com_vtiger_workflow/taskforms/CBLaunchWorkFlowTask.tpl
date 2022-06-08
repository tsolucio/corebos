{*
<!--
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Author   : JPL TSolucio, S. L.
 *************************************************************************************************//
-->*}
<script type="text/javascript">
	var selectedModule = {$task->relModlist|json_encode};
	var moduleName = '{$entityName}';
</script>
<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/launchworkflowtask.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
{literal}
	var searchConditionsConditionExpressionMapping = [{
		'columnname': 'vtiger_cbmap:maptype:maptype:cbMap_Map_Type:V',
		'comparator': 'e',
		'value': 'Condition Expression',
		'columncondition': '',
		'groupid': 1
	}];
	var advSearchConditionExpressionMapping = '&query=true&searchtype=advance&advft_criteria=' + convertArrayOfJsonObjectsToString(searchConditionsConditionExpressionMapping);
	var SpecialSearchConditionExpressionMapping = encodeURI(advSearchConditionExpressionMapping);
	var searchConditionsRecordSetMapping = [{
		'columnname': 'vtiger_cbmap:maptype:maptype:cbMap_Map_Type:V',
		'comparator': 'e',
		'value': 'Record Set Mapping',
		'columncondition': 'or',
		'groupid': 1
	},{
		'columnname': 'vtiger_cbmap:maptype:maptype:cbMap_Map_Type:V',
		'comparator': 'e',
		'value': 'Condition Query',
		'columncondition': 'or',
		'groupid': 1
	},{
		'columnname': 'vtiger_cbmap:maptype:maptype:cbMap_Map_Type:V',
		'comparator': 'e',
		'value': 'Condition Expression',
		'columncondition': 'or',
		'groupid': 1
	},{
		'columnname': 'vtiger_cbmap:maptype:maptype:cbMap_Map_Type:V',
		'comparator': 'e',
		'value': 'Decision Table',
		'columncondition': '',
		'groupid': 1
	}];
	var advSearchRecordSetMapping = '&query=true&searchtype=advance&advft_criteria=' + convertArrayOfJsonObjectsToString(searchConditionsRecordSetMapping);
	var SpecialSearchRecordSetMapping = encodeURI(advSearchRecordSetMapping);
{/literal}
</script>
<div class="slds-form-element slds-m-top_small">
	<div class="slds-form-element__control">
		<table class="slds-table slds-table_cell-buffer" id="questionDiv" style="width:100%;">
			<tr>
				<td>
					<input id="workflowid" name="workflowid" type="hidden" {if !empty($task->workflowid )} value={$task->workflowid} {else} value=""{/if}>
					<input type='hidden' name="workflowid_type" id="workflowid_type" value="com_vtiger_workflow">
					<span class="slds-m-right_xx-small">{'LBL_EXECUTE_THIS_WORKFLOW'|@getTranslatedString:'LBL_EXECUTE_THIS_WORKFLOW'}</span>
					<input id="workflowid_display" name="workflowid_display" readonly type="text" class="slds-input" style="width:360px;border:1px solid #bababa;" onclick='return vtlib_open_popup_window("new_task_form", "workflowid", "com_vtiger_workflow", "");' {if !empty($task->workflowid_display )} value="{$task->workflowid_display}" {else} value="" {/if}>&nbsp;
					<span class="slds-icon_container slds-icon-standard-choice" title="{'LBL_SELECT'|getTranslatedString}" id="workflowid_clear" onclick='return vtlib_open_popup_window("new_task_form", "workflowid", "com_vtiger_workflow", "");'>
						<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#choice"></use>
						</svg>
					</span>
					<span class="slds-icon_container slds-icon-standard-choice" title="{'LBL_CLEAR'|getTranslatedString}" onclick="document.getElementById('workflowid').value=''; document.getElementById('workflowid_display').value=''; return false;">
						<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
						</svg>
					</span>
				</td>
			</tr>
			<tr>
				<td>
					<input type="radio" name="record_filter_opt" id="current_record_opt" {if $task->record_filter_opt eq "filterByCurrentRecord"}checked{/if} value="filterByCurrentRecord"/>
					<label class="slds-radio__label" for="current_record_opt">
						<span class="slds-radio_faux"></span>
						<span class="slds-form-element__label">{'LBL_CURRENT_RECORD'|@getTranslatedString}</span>
					</label>
				</td>
			</tr>
			<tr>
				<td>
					<input type="radio" name="record_filter_opt" id="context_variable_opt" {if $task->record_filter_opt eq "filterByThese"}checked{/if} value="filterByThese"/>
					<label class="slds-radio__label" for="context_variable_opt">
						<span class="slds-radio_faux"></span>
						<span class="slds-form-element__label">{'LBL_IDS'|@getTranslatedString}</span>
					</label>
					<input type="text" class="slds-input" style="width:80%;" name="crmids_list" id="crmids_list" {if !empty($task->crmids_list )} value = "{$task->crmids_list}" {else} value ="" {/if}>
				</td>
			</tr>
			<tr>
				<td>
					<input type="radio" name="record_filter_opt" id="record_filter_opt_0" {if $task->record_filter_opt eq "filterByrelModuleAndConditionExpressionMap"}checked{/if} value="filterByrelModuleAndConditionExpressionMap"/>
					<label class="slds-radio__label" for="record_filter_opt_0">
						<span class="slds-radio_faux"></span>
						<span class="slds-form-element__label">{'On the Related'|@getTranslatedString}</span>
					</label>
					<select class="slds-select" name="relModlist" id="relModlist" style="width:25%;" onchange="filterWorkFlowBasedOnRelatedModule()">
						<option value="">{'No Module'|@getTranslatedString}</option>
					</select>
					<input id="conditionexpressionmapid" name="conditionexpressionmapid" type="hidden" {if !empty($task->conditionexpressionmapid )} value = {$task->conditionexpressionmapid} {else} value ="" {/if}>
					{* <input type='hidden' class='small' name="workflow_type" id="workflow_type" value="com_vtiger_workflow"> *}
					<span class="slds-m-left_xx-small slds-m-right_xx-small">{'LBL_WITH_THIS_CONDITION'|@getTranslatedString:'LBL_WITH_THIS_CONDITION'}</span>
					<input id="conditionexpressionmapid_display" name="conditionexpressionmapid_display" readonly type="text" class="slds-input" style="width:350px;border:1px solid #bababa;" onclick="return window.open('index.php?module=cbMap&action=Popup&html=Popup_picker&form=new_task&forfield=conditionexpressionmapid&srcmodule=GlobalVariable'+SpecialSearchConditionExpressionMapping, 'vtlibui10wf', cbPopupWindowSettings);" {if !empty($task->conditionexpressionmapid_display )} value = "{$task->conditionexpressionmapid_display}" {else} value ="" {/if}>&nbsp;
					<span class="slds-icon_container slds-icon-standard-choice" title="{'LBL_SELECT'|getTranslatedString}" onclick="return window.open('index.php?module=cbMap&action=Popup&html=Popup_picker&form=new_task&forfield=conditionexpressionmapid&srcmodule=GlobalVariable'+SpecialSearchConditionExpressionMapping, 'vtlibui10wf', cbPopupWindowSettings);">
						<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#choice"></use>
						</svg>
					</span>
					<span class="slds-icon_container slds-icon-standard-choice" title="{'LBL_CLEAR'|getTranslatedString}" onclick="document.getElementById('conditionexpressionmapid').value=''; document.getElementById('conditionexpressionmapid_display').value=''; return false;">
						<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
						</svg>
					</span>
				</td>
			</tr>
			<tr>
				<td>
					<input type="radio" name="record_filter_opt" id="record_filter_opt_1" {if $task->record_filter_opt eq "filterByRecordSetMap"}checked{/if} value="filterByRecordSetMap"/>
					<label class="slds-radio__label" for="record_filter_opt_1">
						<span class="slds-radio_faux"></span>
						<span class="slds-form-element__label">{'LBL_WITH_THIS_RECORDSET'|@getTranslatedString}</span>
					</label>
					<input id="recordsetmapid" name="recordsetmapid" type="hidden" {if !empty($task->recordsetmapid )} value = {$task->recordsetmapid} {else} value ="" {/if}>
					<input id="recordsetmapid_display" name="recordsetmapid_display" readonly type="text" class="slds-input" style="width:350px;border:1px solid #bababa;" onclick="return window.open('index.php?module=cbMap&action=Popup&html=Popup_picker&form=new_task&forfield=recordsetmapid&srcmodule=GlobalVariable'+SpecialSearchRecordSetMapping, 'vtlibui10wf', cbPopupWindowSettings);" {if !empty($task->recordsetmapid_display )} value = "{$task->recordsetmapid_display}" {else} value ="" {/if}>&nbsp;
					<span class="slds-icon_container slds-icon-standard-choice" title="{'LBL_SELECT'|getTranslatedString}" onclick="return window.open('index.php?module=cbMap&action=Popup&html=Popup_picker&form=new_task&forfield=recordsetmapid&srcmodule=GlobalVariable'+SpecialSearchRecordSetMapping, 'vtlibui10wf', cbPopupWindowSettings);">
						<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#choice"></use>
						</svg>
					</span>
					<span class="slds-icon_container slds-icon-standard-choice" title="{'LBL_CLEAR'|getTranslatedString}" onclick="document.getElementById('recordsetmapid').value=''; document.getElementById('recordsetmapid_display').value=''; return false;">
						<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
						</svg>
					</span>
				</td>
			</tr>
		</table>
	</div>
</div>