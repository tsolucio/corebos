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
<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout small detailview_table">
	<tr class="slds-line-height--reset">
		<td class="dvtCellLabel text-left heading2" width=20%>
			{'LBL_IMPORT_STEP_4'|@getTranslatedString:$MODULE}:
		</td>
		<td class="dvtCellInfo" width=80%>
			<span class="big">{'LBL_IMPORT_STEP_4_DESCRIPTION'|@getTranslatedString:$MODULE}</span>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td class="dvtCellInfo text-left">
			<div id="savedMapsContainer">
				{include file="modules/Import/Import_Saved_Maps.tpl"}
			</div>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td class="dvtCellInfo text-left">
			<input type="hidden" name="field_mapping" id="field_mapping" value="" />
			<input type="hidden" name="default_values" id="default_values" value="" />
			<table class="slds-table slds-table--fixed-layout slds-table--bordered ld-font listRow">
				<thead>
					<tr>
						{if $HAS_HEADER eq true}
							<th class="slds-text-title--caps big" scope="col"><b>{'LBL_FILE_COLUMN_HEADER'|@getTranslatedString:$MODULE}</b></th>
						{/if}
						<th class="slds-text-title--caps big" scope="col"><b>{'LBL_ROW_1'|@getTranslatedString:$MODULE}</b></th>
						<th class="slds-text-title--caps big" scope="col"><b>{'LBL_CRM_FIELDS'|@getTranslatedString:$MODULE}</b></th>
						<th class="slds-text-title--caps big" scope="col"><b>{'LBL_DEFAULT_VALUE'|@getTranslatedString:$MODULE}</b></th>
					</tr>
				</thead>
				<tbody class="no-padding-td">
					{foreach key=_HEADER_NAME item=_FIELD_VALUE from=$ROW_1_DATA name="headerIterator"}
						{assign var="_COUNTER" value=$smarty.foreach.headerIterator.iteration}
						<tr class="slds-hint-parent slds-line-height--reset fieldIdentifier" id="fieldIdentifier{$_COUNTER}">
							{if $HAS_HEADER eq true}
								<td role="gridcell" style="padding: 0 .5rem 0 1.5rem;">
									<div class="slds-truncate">
										<span name="header_name">{$_HEADER_NAME}</span>
									</div>
								</td>
							{/if}
							<td role="gridcell" style="padding: 0 .5rem;">
								<div class="slds-truncate">
									<span>{$_FIELD_VALUE|@textlength_check}</span>
								</div>
							</td>
							<td role="gridcell" style="padding: 0 .5rem;">
								<div class="slds-truncate">
									<input type="hidden" name="row_counter" value="{$_COUNTER}" />
									<select name="mapped_fields" class="slds-select" onchange="ImportJs.loadDefaultValueWidget('fieldIdentifier{$_COUNTER}')" style="margin: .2rem 0;">
										<option value="">{'LBL_NONE'|@getTranslatedString:$FOR_MODULE}</option>
										{foreach key=_FIELD_NAME item=_FIELD_INFO from=$AVAILABLE_FIELDS}
											{assign var="_TRANSLATED_FIELD_LABEL" value=$_FIELD_INFO->getFieldLabelKey()|@getTranslatedString:$FOR_MODULE}
											<option value="{$_FIELD_NAME}" {if $_HEADER_NAME eq $_TRANSLATED_FIELD_LABEL} selected {/if} >{$_TRANSLATED_FIELD_LABEL}{if $_FIELD_INFO->isMandatory() eq 'true'}&nbsp; (*){/if}</option>
										{/foreach}
									</select>
								</div>
							</td>
							<td role="gridcell" style="padding: 0 .5rem;">
								<div class="slds-truncate" name="default_value_container">&nbsp;</div>
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td class="dvtCellInfo text-left">
			<span class="slds-checkbox">
				<input type="checkbox" name="save_map" id="save_map" />
				<label class="slds-checkbox__label" for="save_map">
					<span class="slds-checkbox--faux"></span>
				</label>
			</span>&nbsp;
			<span class="small">{'LBL_SAVE_AS_CUSTOM_MAPPING'|@getTranslatedString:$MODULE}</span>&nbsp; : &nbsp;
			<input type="text" name="save_map_as" id="save_map_as" class="slds-input" style="width:20%" />
		</td>
	</tr>
	<tr class="slds-line-height--reset">
		<td>&nbsp;</td>
		<td class="dvtCellInfo text-left">
			{include file="modules/Import/Import_Default_Values_Widget.tpl"}
		</td>
	</tr>