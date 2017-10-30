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

<div id="pickListContents">
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
		<!-- Select Picklist Dependency -->
		<tr>
			<td class="small dvtCellLabel text-left" width="20%" nowrap>
				<strong>{$APP.LBL_MODULE} : </strong>&nbsp; {$MODULE|@getTranslatedString:$MODULE}
			</td>
			<td class="small dvtCellInfo" width="30%">
				<b>{$MOD_PICKLIST.LBL_SOURCE_FIELD}:</b>&nbsp;
				<select name="sourcefield" id="sourcefield" class="slds-select" style="width:50%;" {if !empty($DEPENDENCY_MAP.sourcefield)} disabled {/if}>
					{foreach key=fld_nam item=fld_lbl from=$ALL_LISTS}
						<option value="{$fld_nam}" {if !empty($DEPENDENCY_MAP) && is_array($DEPENDENCY_MAP) && $DEPENDENCY_MAP.sourcefield eq $fld_nam} selected {/if}>
							{$fld_lbl|getTranslatedString:$MODULE}
						</option>
					{/foreach}
				</select>
			</td>
			<td class="small dvtCellInfo" width="30%">
				<b>{$MOD_PICKLIST.LBL_TARGET_FIELD} :</b>&nbsp;
				<select name="targetfield" id="targetfield" class="slds-select" style="width:50%;" {if !empty($DEPENDENCY_MAP.sourcefield)} disabled {/if}>
					{foreach key=fld_nam item=fld_lbl from=$ALL_LISTS}
						<option value="{$fld_nam}" {if !empty($DEPENDENCY_MAP) && is_array($DEPENDENCY_MAP) && $DEPENDENCY_MAP.targetfield eq $fld_nam} selected {/if}>
							{$fld_lbl|getTranslatedString:$MODULE}
						</option>
					{/foreach}
				</select>
			</td>
			<td nowrap align="right" class="small dvtCellLabel">
				<input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" name="cancel" class="slds-button slds-button--small slds-button--destructive" onclick="location.href='index.php?module=PickList&action=PickListDependencySetup&parenttab=Settings&moduleName={$MODULE}';" />
				{if $DEPENDENCY_MAP neq '' && $DEPENDENCY_MAP|@count > 0}
					<input type="submit" value="{$APP.LBL_SAVE_BUTTON_LABEL}" name="save" class="slds-button--small slds-button slds-button_success" onclick="saveDependency('{$MODULE}');" />
				{else}
					<input type="submit" value="{$APP.LBL_NEXT_BUTTON_LABEL}" name="next" class="slds-button--small slds-button slds-button_success" onclick="editNewDependencyPicklist('{$MODULE}');" />
				{/if}
			</td>
		</tr>

		<!-- Create picklist Dependency -->
		{if $DEPENDENCY_MAP neq '' && $DEPENDENCY_MAP|@count > 0}
		<tr class="small">
			<td align="left" class="dvtCellInfo" colspan="3">
				<ul>
					<li>{$MOD_PICKLIST.LBL_CONFIGURE_DEPENDENCY_HELP_1}.</li>
					<li>{$MOD_PICKLIST.LBL_CONFIGURE_DEPENDENCY_HELP_2}.</li>
					<li>{$MOD_PICKLIST.LBL_CONFIGURE_DEPENDENCY_HELP_3}
						<span class="selectedCellIndex">{$MOD_PICKLIST.LBL_SELECTED_VALUES}</span>
					</li>
				</ul>
			</td>
			<td align="right" class="dvtCellLabel">
				<input  type="button" class="slds-button slds-button--brand slds-button--small" onclick="show('sourceValuesSelectionDiv');placeAtCenter(document.getElementById('sourceValuesSelectionDiv'));"
						value="{'LBL_BUTTON_SELECTED_SOURCE_VALUES'|@getTranslatedString:$PICKLIST_MODULE}"
						title="{'LBL_BUTTON_SELECTED_SOURCE_VALUES'|@getTranslatedString:$PICKLIST_MODULE}"/>
				<div style="display:none;position:absolute;width: 750px;" id="sourceValuesSelectionDiv">
					<div class="layerPopup" style="position:relative; display:block; padding:10px">
						<table cellspacing="0" cellpadding="5" border="0" class="layerHeadingULine" width="100%">
							<tr>
								<td nowrap="" align="left" class="layerPopupHeading warning">
									{$MOD_PICKLIST.LBL_SELECTED_SOURCE_VALUES_MSG}
								</td>
							</tr>
						</table>
						<table cellspacing="0" cellpadding="5" border="0" class="small" width="100%">
							<tr>
							{foreach key=SOURCE_INDEX item=SOURCE_VALUE from=$SOURCE_VALUES name=sourceValuesLoop}
								{if $smarty.foreach.sourceValuesLoop.index % 4 == 0}
									</tr><tr>
								{/if}
								<td class="dvtCellInfo" align="left" width="25%">
									<span class="slds-checkbox">
										<input type="checkbox" name="selectedSourceValues" id="sourceValue{$SOURCE_INDEX}" value="{$DEPENDENCY_MAP.sourcefield}{$SOURCE_INDEX}" />
										<label class="slds-checkbox__label" for="sourceValue{$SOURCE_INDEX}">
											<span class="slds-checkbox--faux"></span>
										</label>
										<span class="slds-form-element__label">{$SOURCE_VALUE|@getTranslatedString:$MODULE}</span>
									</span>
								</td>
							{/foreach}
							</tr>
							<tr>
								<td valign="top" colspan="4" align="center">
									<input type="button" value="{$APP.LBL_APPLY_BUTTON_LABEL}" name="apply" class="slds-button slds-button--small slds-button_success" onclick="loadMappingForSelectedValues(); fnhide('sourceValuesSelectionDiv');">
									<input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" name="cancel" class="slds-button slds-button--small slds-button--destructive" onclick="fnhide('sourceValuesSelectionDiv');">
								</td>
							</tr>
						</table>
					</div>
				</div>
			</td>
		</tr>
	</table>

	<br/>
	<!-- Edit picklist dependency -->
	<table class="listTable" width="90%" cellpadding="5" cellspacing="0" align="center">
		<tbody id="valueMapping">
			<tr>
				<td class="dvtCellLabel text-left tableHeading big warning fixedHeight18px" width="20%">{$ALL_LISTS[$DEPENDENCY_MAP.sourcefield]|getTranslatedString:$MODULE}</td>
				{foreach key=SOURCE_INDEX item=SOURCE_VALUE from=$SOURCE_VALUES}
					<td class="dvtCellInfo tableHeading big fixedHeight18px {$DEPENDENCY_MAP.sourcefield}{$SOURCE_INDEX} picklistValueMapping" width="10%" style="display:none;">
						<input type="hidden" id="{$DEPENDENCY_MAP.sourcefield}{$SOURCE_INDEX}" value="{$SOURCE_INDEX}" />
						{$SOURCE_VALUE|@getTranslatedString:$MODULE}
					</td>
				{/foreach}
			</tr>
			{foreach key=TARGET_INDEX item=TARGET_VALUE from=$TARGET_VALUES name=targetValuesLoop}
				<tr>
					{if $smarty.foreach.targetValuesLoop.index eq 0}
						<td class="dvtCellLabel text-left tableHeading big warning" width="20%" rowspan="{$TARGET_VALUES|@count}" valign="top">{$ALL_LISTS[$DEPENDENCY_MAP.targetfield]|getTranslatedString:$MODULE}</td>
					{/if}
					{foreach key=SOURCE_INDEX item=SOURCE_VALUE from=$SOURCE_VALUES}
						<td id='mapping{$DEPENDENCY_MAP.sourcefield}{$SOURCE_INDEX}' onmouseover="handleCellMouseOver(event, this, '{$DEPENDENCY_MAP.sourcefield}{$SOURCE_INDEX|@htmlentities|@addslashes}');"
							onmousedown="handleCellMouseDown(event, this);" onmouseup="handleCellMouseUp(event, this);" width="10%"
							class="selectedCell {$DEPENDENCY_MAP.sourcefield}{$SOURCE_INDEX} picklistValueMapping" style="display:none;">
							<input type="hidden" name="valueMapping{$SOURCE_INDEX}" id="valueMapping{$SOURCE_INDEX}" value="{$TARGET_VALUE}" />
							{$TARGET_VALUE|@getTranslatedString:$MODULE}
						</td>
					{/foreach}
				</tr>
			{/foreach}
			<tr>
			</tr>
		</tbody>
	</table>

	<script type="text/javascript">
	{if $DEPENDENCY_MAP.valuemapping|@count > 0}
		{foreach key=MAPPING_COUNT item=MAPPING_DATA from=$DEPENDENCY_MAP.valuemapping}
			{assign var="sourceValue" value=$MAPPING_DATA.sourcevalue}
			{assign var="targetValues" value=$MAPPING_DATA.targetvalues}
			{assign var="sourceIndex" value=$sourceValue|@array_search:$SOURCE_VALUES}
			selectSourceValue("{$sourceIndex|@decode_html|@addslashes}");
			{foreach item=TARGET_VALUE from=$TARGET_VALUES}
				{if $TARGET_VALUE|@in_array:$targetValues neq '1'}
					unselectTargetValue('{$sourceIndex|@decode_html|@addslashes}', '{$TARGET_VALUE}');
				{/if}
			{/foreach}
		{/foreach}
	{else}
		{foreach key=SOURCE_INDEX item=SOURCE_VALUE from=$SOURCE_VALUES}
			selectSourceValue("{$SOURCE_VALUE|@addslashes}");
		{/foreach}
	{/if}
	loadMappingForSelectedValues();
	</script>
	{/if}
</div>