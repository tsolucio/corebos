{*<!--
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/
-->*}
<table id="field_table" class="small" border="0" cellpadding="5" cellspacing="1" width="100%">
	<tr>
		<td style="width:2%;height:25px;" class="lvtCol"></td>
		<td style="height:25px;" class="lvtCol">{'LBL_FIELDLABEL'|@getTranslatedString:$MODULE}</td>
		<td style="height:25px;" class="lvtCol">{'LBL_DEFAULT_VALUE'|@getTranslatedString:$MODULE}</td>
		<td style="width:2%;height:25px;" class="lvtCol">{'LBL_REQUIRED'|@getTranslatedString:$MODULE}</td>
		<td style="height:25px;" class="lvtCol" style="width:20%;">{'LBL_NEUTRALIZEDFIELD'|@getTranslatedString:$MODULE}</td>
	</tr>
	{assign var="CNT" value=0}
	{foreach item=field from=$WEBFORMFIELDS name=fieldloop}
	{assign var="CNT" value=$CNT+1}
	{if $field.editable eq true && $field.type.name neq reference && $field.name neq assigned_user_id}
	<tr style="height:25px" id="field_row">
		<td class="dvtCellInfo" align="right" colspan="1">
		{if $field.mandatory eq 1}
			<input type="checkbox" name="fields[]"  checked="checked"  value="{$field.name}" record="true" disabled="true">
			<input type="hidden" name="fields[]"  value="{$field.name}" record="true" >
		{else}
			{if $WEBFORMID}
				{if $WEBFORM->isWebformField($WEBFORMID,$field.name) eq true}
					<input type="checkbox" name="fields[]"  record="false" checked="checked" value="{$field.name}" onClick=Webforms.showHideElement('value[{$field.name}]','required[{$field.name}]','jscal_trigger_{$field.name}','mincal_{$field.name}')>
				{else}
					<input type="checkbox" name="fields[]"   record="false" value="{$field.name}" onClick=Webforms.showHideElement('value[{$field.name}]','required[{$field.name}]','jscal_trigger_{$field.name}','mincal_{$field.name}')>
				{/if}
			{else}
					<input type="checkbox" name="fields[]"  record="false" value="{$field.name}" onClick=Webforms.showHideElement('value[{$field.name}]','required[{$field.name}]','jscal_trigger_{$field.name}','mincal_{$field.name}')>
			{/if}
		{/if}
		</td>
		<td class="dvtCellLabel" align="left" colspan="1">
		{if $field.mandatory eq 1}
			<font color="red">*</font>
		{/if}
			{$field.label|@getTranslatedString:$MODULE}
		</td>
		<td class="dvtCellInfo">
		{if $WEBFORMID && $WEBFORM->isWebformField($WEBFORMID,$field.name) eq true }
		{assign var="defaultvalue" value=$WEBFORM->retrieveDefaultValue($WEBFORMID,$field.name)}
			{if $field.type.name eq picklist | $field.type.name eq multipicklist}{assign var="val_arr" value=$WEBFORM->retrieveDefaultValue($WEBFORMID,$field.name)}{assign var="values" value=","|explode:$val_arr}
				<select fieldtype="{$field.type.name}" fieldlabel="{$field.label}" class="small" name="value[{$field.name}][]" id="value[{$field.name}]" style="display:inline;" {if $field.type.name eq multipicklist}multiple="multiple" size="5"{/if}>
						<option value="">{'LBL_SELECT_VALUE'|@getTranslatedString:$MODULE}</option>
					{foreach item=option from=$field.type.picklistValues name=optionloop}
						<option value="{$option.value}" {if in_array($option.value,$defaultvalue)}selected="selected"{/if}>{$option.label}</option>
					{/foreach}
				</select>
			{elseif $field.type.name eq date}
			<input fieldtype="{$field.type.name}" fieldlabel="{$field.label}" type="text" onblur="this.className='detailedViewTextBox';" onfocus="this.className='detailedViewTextBoxOn';" class="detailedViewTextBox" id="value[{$field.name}]"  name="value[{$field.name}]" value="{$defaultvalue[0]}" >
												<img src="{'miniCalendar.gif'|@vtiger_imageurl:$THEME}" id="jscal_trigger_{$field.name}">
												<font size=1 id="mincal_{$field.name}"><em old="(yyyy-mm-dd)">({$DATE_FORMAT})</em></font>
												<script id="date_{$CNT}">
													getCalendarPopup('jscal_trigger_{$field.name}','value[{$field.name}]','{$CAL_DATE_FORMAT}')
												</script>
			{elseif $field.type.name eq text}
					<textarea fieldtype="{$field.type.name}" fieldlabel="{$field.label}" rows="2" onblur="this.className='detailedViewTextBox'" onfocus="this.className='detailedViewTextBoxOn'" class="detailedViewTextBox"  id="value[{$field.name}]" name="value[{$field.name}]"  value="{$defaultvalue[0]}">{$defaultvalue[0]}</textarea>
			
			{elseif $field.type.name eq boolean}
					<input fieldtype="{$field.type.name}" fieldlabel="{$field.label}" type="checkbox"  id="value[{$field.name}]" name="value[{$field.name}]" {if $defaultvalue[0] eq 'on'}checked="checked"{/if}" >
			{else}
					{if $field.name eq salutationtype}
							<select fieldtype="{$field.type.name}" fieldlabel="{$field.label}" class="small" id="value[{$field.name}]" name="value[{$field.name}]">
								<option value="" {if $WEBFORM->retrieveDefaultValue($WEBFORMID,$field.name) eq ""}selected="selected"{/if}>--None--</option>
								<option value="Mr." {if $WEBFORM->retrieveDefaultValue($WEBFORMID,$field.name) eq "Mr."}selected="selected"{/if}>Mr.</option>
								<option value="Ms." {if $WEBFORM->retrieveDefaultValue($WEBFORMID,$field.name) eq "Ms."}selected="selected"{/if}>Ms.</option>
								<option value="Mrs." {if $WEBFORM->retrieveDefaultValue($WEBFORMID,$field.name) eq "Mrs."}selected="selected"{/if}>Mrs.</option>
								<option value="Dr." {if $WEBFORM->retrieveDefaultValue($WEBFORMID,$field.name) eq "Dr."}selected="selected"{/if}>Dr.</option>
								<option value="Prof." {if $WEBFORM->retrieveDefaultValue($WEBFORMID,$field.name) eq "Prof."}selected="selected"{/if}>Prof</option>
							</select>
					{else}
						<input fieldtype="{$field.type.name}" fieldlabel="{$field.label}" type="text" onblur="this.className='detailedViewTextBox';" onfocus="this.className='detailedViewTextBoxOn';" class="detailedViewTextBox" id="value[{$field.name}]"  name="value[{$field.name}]" value="{$defaultvalue[0]}" style="display:inline;"></input>
					{/if}
			{/if}
		{else}
			{if $field.mandatory eq 1}
				{if $field.type.name eq picklist | $field.type.name eq multipicklist}{assign var="val_arr" value=$WEBFORM->retrieveDefaultValue($WEBFORMID,$field.name)}{assign var="values" value=","|explode:$val_arr}
					<select fieldtype="{$field.type.name}" fieldlabel="{$field.label}" class="small" name="value[{$field.name}][]" id="value[{$field.name}]" style="display:inline;" class="small" {if $field.type.name eq multipicklist}multiple="multiple" size="5"{/if}>
							<option value="" {if $field.default eq $option.value} selected="selected"{/if}>{'LBL_SELECT_VALUE'|@getTranslatedString:$MODULE}</option>
						{foreach item=option from=$field.type.picklistValues name=optionloop}
							<option value="{$option.value}" {if $field.default eq $option.value} selected="selected"{/if}>{$option.label}</option>
						{/foreach}
					</select>
				{elseif $field.type.name eq date}
				<input fieldtype="{$field.type.name}" fieldlabel="{$field.label}" type="text" onblur="this.className='detailedViewTextBox';" onfocus="this.className='detailedViewTextBoxOn';" class="detailedViewTextBox" id="value[{$field.name}]"  name="value[{$field.name}]" value="{$field.default}" >
												<img src="{'miniCalendar.gif'|@vtiger_imageurl:$THEME}" id="jscal_trigger_{$field.name}" >
												<font size=1 id="mincal_{$field.name}"><em old="(yyyy-mm-dd)">({$DATE_FORMAT})</em></font>
												<script id="date_{$CNT}">
													getCalendarPopup('jscal_trigger_{$field.name}','value[{$field.name}]','{$CAL_DATE_FORMAT}')
												</script>
				{elseif $field.type.name eq text}
						<textarea fieldtype="{$field.type.name}" fieldlabel="{$field.label}" rows="2" onblur="this.className='detailedViewTextBox'" onfocus="this.className='detailedViewTextBoxOn'" class="detailedViewTextBox"  id="value[{$field.name}]" name="value[{$field.name}]"  value="$field.default" style="display:inline;">{$field.default}</textarea>
				{elseif $field.type.name eq boolean}
					<input fieldtype="{$field.type.name}" fieldlabel="{$field.label}" type="checkbox"  id="value[{$field.name}]" name="value[{$field.name}]" style="display:inline;" {if $field.default}checked="checked"{/if} >
				{else}
						{if $field.name eq salutationtype}
							<select fieldtype="{$field.type.name}" fieldlabel="{$field.label}" class="small" id="value[{$field.name}]" name="value[{$field.name}]">
								<option value="" {if $field.default eq ""}selected="selected"{/if}>--None--</option>
								<option value="Mr." {if $field.default eq "Mr."}selected="selected"{/if}>Mr.</option>
								<option value="Ms." {if $field.default eq "Ms."}selected="selected"{/if}>Ms.</option>
								<option value="Mrs." {if $field.default eq "Mrs."}selected="selected"{/if}>Mrs.</option>
								<option value="Dr." {if $field.default eq "Dr."}selected="selected"{/if}>Dr.</option>
								<option value="Prof." {if $field.default eq "Prof."}selected="selected"{/if}>Prof</option>
							</select>
						{else}
							<input fieldtype="{$field.type.name}" fieldlabel="{$field.label}" type="text" onblur="this.className='detailedViewTextBox';" onfocus="this.className='detailedViewTextBoxOn';" class="detailedViewTextBox" id="value[{$field.name}]"  name="value[{$field.name}]" value="{$field.default}" style="display:inline;"></input>
						{/if}
				{/if}
			{else}
				{if $field.type.name eq picklist | $field.type.name eq multipicklist}{assign var="val_arr" value=$WEBFORM->retrieveDefaultValue($WEBFORMID,$field.name)}{assign var="values" value=","|explode:$val_arr}
					<select fieldtype="{$field.type.name}" fieldlabel="{$field.label}" class="small" name="value[{$field.name}][]" id="value[{$field.name}]" style="display:none;" class="small" {if $field.type.name eq multipicklist}multiple="multiple" size="5"{/if}>
							<option value="" {if $field.default eq $option.value} selected="selected"{/if}>{'LBL_SELECT_VALUE'|@getTranslatedString:$MODULE}</option>
						{foreach item=option from=$field.type.picklistValues name=optionloop}
							<option value="{$option.value}" {if $field.default eq $option.value} selected="selected"{/if} >{$option.label}</option>
						{/foreach}
					</select>
				{elseif $field.type.name eq date}
				<input fieldtype="{$field.type.name}" fieldlabel="{$field.label}" type="text" onblur="this.className='detailedViewTextBox';" onfocus="this.className='detailedViewTextBoxOn';" class="detailedViewTextBox" id="value[{$field.name}]"  name="value[{$field.name}]" value="{$field.default}" style="display:none;">
												<img src="{'miniCalendar.gif'|@vtiger_imageurl:$THEME}" id="jscal_trigger_{$field.name}" style="display:none;">
												<font size=1 id="mincal_{$field.name}" style="display:none;"><em old="(yyyy-mm-dd)">({$DATE_FORMAT})</em></font>
												<script id="date_{$CNT}">
													getCalendarPopup('jscal_trigger_{$field.name}','value[{$field.name}]','{$CAL_DATE_FORMAT}')
												</script>
				{elseif $field.type.name eq text}
						<textarea fieldtype="{$field.type.name}" fieldlabel="{$field.label}" rows="2" onblur="this.className='detailedViewTextBox'" onfocus="this.className='detailedViewTextBoxOn'" class="detailedViewTextBox"  id="value[{$field.name}]" name="value[{$field.name}]"  value="{$field.default}" style="display:none;">{$field.default}</textarea>
				{elseif $field.type.name eq boolean}
					<input fieldtype="{$field.type.name}" fieldlabel="{$field.label}" type="checkbox"  id="value[{$field.name}]" name="value[{$field.name}]" style="display:none;" {if $field.default}checked="checked"{/if}>
				{else}
						{if $field.name eq salutationtype}
							<select fieldtype="{$field.type.name}" fieldlabel="{$field.label}" class="small" id="value[{$field.name}]" name="value[{$field.name}]" style="display:none;">
								<option value="" {if $field.default eq ""}selected="selected"{/if}>--None--</option>
								<option value="Mr." {if $field.default eq "Mr."}selected="selected"{/if}>Mr.</option>
								<option value="Ms." {if $field.default eq "Ms."}selected="selected"{/if}>Ms.</option>
								<option value="Mrs." {if $field.default eq "Mrs."}selected="selected"{/if}>Mrs.</option>
								<option value="Dr." {if $field.default eq "Dr."}selected="selected"{/if}>Dr.</option>
								<option value="Prof." {if $field.default eq "Prof."}selected="selected"{/if}>Prof</option>
							</select>
						{else}
							<input fieldtype="{$field.type.name}" fieldlabel="{$field.label}" type="text" onblur="this.className='detailedViewTextBox';" onfocus="this.className='detailedViewTextBoxOn';" class="detailedViewTextBox" id="value[{$field.name}]"  name="value[{$field.name}]" value="{$field.default}" style="display:none;"></input>
						{/if}
				{/if}
			{/if}
		{/if}
		</td>
		<td class="dvtCellInfo" align="center" colspan="1">
			{if $field.mandatory eq 1}
				<input  type="checkbox" checked="checked" disabled="disabled" value="{$field.name}" style="display:inline;" >
				<input type="hidden" id="required[{$field.name}]" name="required[]" value="{$field.name}"></input>
			{else}
				{if $WEBFORMID}
					{if $WEBFORM->isWebformField($WEBFORMID,$field.name) eq true && $WEBFORM->isRequired($WEBFORMID,$field.name) eq true}
						<input  type="checkbox" id="required[{$field.name}]" name="required[]" value="{$field.name}" checked="checked" style="display:inline;" >
					{else}
						{if $WEBFORM->isWebformField($WEBFORMID,$field.name)}
							<input  type="checkbox" id="required[{$field.name}]" name="required[]" value="{$field.name}" style="display:inline;">
						{else}
							<input type="checkbox" id="required[{$field.name}]" name="required[]" value="{$field.name}" style="display:none;">
						{/if}
					{/if}
				{else}
					<input type="checkbox" id="required[{$field.name}]" name="required[]" value="{$field.name}" style="display:none;">
				{/if}
			{/if}
		</td>
		<td class="dvtCellLabel" align="left" colspan="1">
			{if $WEBFORM->isCustomField($field.name) eq true}
				label:{$field.label}
			{else}
				{$field.name}
			{/if}
		</td>
	</tr>
{/if}
{/foreach}
<script type="test/javascript" id="counter">
	var count={$CNT};
</script>
</table>