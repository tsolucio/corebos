{*<!--
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
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************//
-->*}
<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/generateReportWfTask.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">var moduleName = '{$entityName}';</script>
<script type="text/javascript" charset="utf-8">
	var reportName = {$task->report_name|json_encode};
	var mod_alert_arr = {
		'selectReport': '{'Select Report'|@getTranslatedString:'Reports'}',
	};
</script>
<div class="slds-form-element">
	<div class="slds-form-element__control">
		<table class="slds-table slds-table_cell-buffer" id="typeDiv" style="width:100%;">
			<tr>
				<td>
					<label class="slds-form-element__label" for="case_type">{'Select source module'|@getTranslatedString}</label>
				</td>
			</tr>
			<tr>
				<td>
					<div class="slds-select_container">
						<select class="slds-select" name ="case_type" id="case_type" onchange="displayDivsection()">
							<option {if ($task->case_type eq "report")}{"selected"}{/if} value="report">{'LBL_MODULE_NAME'|@getTranslatedString:'Reports'}</option>
							<option {if ($task->case_type eq "question")}{"selected"}{/if} value="question">{'cbQuestion'|@getTranslatedString:'cbQuestion'}</option>
						</select>
					</div>
				</td>
			</tr>
		</table>
		<table class="slds-table slds-table_cell-buffer" id="reportDiv" style="width:100%;">
			<tr>
				<td>
					<label class="slds-form-element__label" for="report_name">{'Select Report'|@getTranslatedString:'Reports'}</label>
				</td>
			</tr>
			<tr>
				<td>
					<div class="slds-select_container">
						<select class="slds-select" name ="report_name" id="report_name">
						</select>
					</div>
				</td>
			</tr>
		</table>
		<table class="slds-table slds-table_cell-buffer" id="questionDiv" style="width:100%; display:none;">
			<tr>
				<td>
					<input id="qnfield" name="qnfield" type="hidden" {if !empty($task->qnfield )} value = {$task->qnfield} {else} value ="" {/if}>
					<input type='hidden' class='small' name="qnfield_type" id="qnfield_type" value="cbQuestion">
					<span>{'cbQuestion'|@getTranslatedString:'cbQuestion'}</span>
					<input
						id="qnfield_display"
						name="qnfield_display"
						readonly
						type="text"
						style="border:1px solid #bababa;"
						onclick='return vtlib_open_popup_window("new_task_form", "qnfield", "cbQuestion", "");'
						{if !empty($task->qnfield_display )} value = "{$task->qnfield_display}" {else} value ="" {/if}>&nbsp;
						<span class="slds-icon_container slds-icon-standard-choice" title="{'LBL_SELECT'|getTranslatedString}" onclick='return vtlib_open_popup_window("new_task_form", "qnfield", "cbQuestion", "");'>
							<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#choice"></use>
							</svg>
						</span>
						<span class="slds-icon_container slds-icon-standard-choice" title="{'LBL_ADD_ITEM'|getTranslatedString}" onclick='return window.open("index.php?module=cbQuestion&action=EditView");'>
							<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#record_create"></use>
							</svg>
						</span>
						<span class="slds-icon_container slds-icon-standard-choice" title="{'LBL_CLEAR'|getTranslatedString}" onclick="document.getElementById('qnfield').value=''; document.getElementById('qnfield_display').value=''; return false;">
							<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use>
							</svg>
						</span>
				</td>
			</tr><br/>
		</table>
		<table class="slds-table slds-table_cell-buffer" id="fileTypeDiv" style="width:100%;">
			<tr>
				<td>
					<label class="slds-form-element__label" for="file_type">{'Select File Type'|@getTranslatedString}</label>
				</td>
			</tr>
			<tr>
				<td>
					<div class="slds-select_container">
						<select class="slds-select" name ="file_type"  id="file_type">
							<option {if ($task->file_type eq "csv")}{"selected"}{/if} value="csv">{'CSV'|@getTranslatedString}</option>
							<option {if ($task->file_type eq "excel")}{"selected"}{/if} value="excel">{'Excel'|@getTranslatedString}</option>
							<option {if ($task->file_type eq "pdf")}{"selected"}{/if} value="pdf">{'PDF'|@getTranslatedString}</option>
						</select>
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>