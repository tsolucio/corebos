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

<div style="visibility: hidden; height: 0px;" id="defaultValuesElementsContainer">
	{foreach key=_FIELD_NAME item=_FIELD_INFO from=$AVAILABLE_FIELDS}
	<span id="{$_FIELD_NAME}_defaultvalue_container" name="{$_FIELD_NAME}_defaultvalue" class="small">
		{assign var="_FIELD_TYPE" value=$_FIELD_INFO->getFieldDataType()}
		{if $_FIELD_TYPE eq 'picklist' || $_FIELD_TYPE eq 'multipicklist'}
			<select id="{$_FIELD_NAME}_defaultvalue" name="{$_FIELD_NAME}_defaultvalue" class="small">
			{foreach item=_PICKLIST_DETAILS from=$_FIELD_INFO->getPicklistDetails()}
				<option value="{$_PICKLIST_DETAILS.value}">{$_PICKLIST_DETAILS.label|@getTranslatedString:$FOR_MODULE}</option>
			{/foreach}
			</select>
		{elseif $_FIELD_TYPE eq 'integer'}
			<input type="text" id="{$_FIELD_NAME}_defaultvalue" name="{$_FIELD_NAME}_defaultvalue" class="small" value="0" />
		{elseif $_FIELD_TYPE eq 'owner' || $_FIELD_INFO->getUIType() eq '52'}
			<select id="{$_FIELD_NAME}_defaultvalue" name="{$_FIELD_NAME}_defaultvalue" class="small">
				<option value="">--{'LBL_NONE'|@getTranslatedString:$FOR_MODULE}--</option>
			{foreach key=_ID item=_NAME from=$USERS_LIST}
				<option value="{$_ID}">{$_NAME}</option>
			{/foreach}
			{if $_FIELD_INFO->getUIType() eq '53'}
				{foreach key=_ID item=_NAME from=$GROUPS_LIST}
				<option value="{$_ID}">{$_NAME}</option>
				{/foreach}
			{/if}
			</select>
		{elseif $_FIELD_TYPE eq 'date'}
			<input type="text" id="{$_FIELD_NAME}_defaultvalue" name="{$_FIELD_NAME}_defaultvalue" class="small" value="" />
			<img border=0 src="{'btnL3Calendar.gif'|@vtiger_imageurl:$THEME}" id="jscal_trigger_{$_FIELD_NAME}"
				 alt="{'LBL_SET_DATE'|@getTranslatedString:$FOR_MODULE}" title="{'LBL_SET_DATE'|@getTranslatedString:$FOR_MODULE}" />
			<script type="text/javascript">
			Calendar.setup (
				{ldelim}
					inputField : "{$_FIELD_NAME}_defaultvalue",
					ifFormat : "%Y-%m-%d",
					showsTime : false,
					button : "jscal_trigger_{$_FIELD_NAME}",
					singleClick : true, step : 1
				{rdelim}
			);
			</script>
		{elseif $_FIELD_TYPE eq 'datetime'}
			<input type="text" id="{$_FIELD_NAME}_defaultvalue" name="{$_FIELD_NAME}_defaultvalue" class="small" value="" />
			<img border=0 src="{'btnL3Calendar.gif'|@vtiger_imageurl:$THEME}" id="jscal_trigger_{$_FIELD_NAME}"
				 alt="{'LBL_SET_DATE_TIME'|@getTranslatedString:$FOR_MODULE}" title="{'LBL_SET_DATE_TIME'|@getTranslatedString:$FOR_MODULE}" />
			<script type="text/javascript">
			Calendar.setup (
				{ldelim}
					inputField : "{$_FIELD_NAME}_defaultvalue",
					ifFormat : "%Y-%m-%d",
					showsTime : true,
					button : "jscal_trigger_{$_FIELD_NAME}",
					singleClick : true, step : 1
				{rdelim}
			);
			</script>
		{elseif $_FIELD_TYPE eq 'boolean'}
			<input type="checkbox" id="{$_FIELD_NAME}_defaultvalue" name="{$_FIELD_NAME}_defaultvalue" class="small" />
		{elseif $_FIELD_TYPE neq 'reference'}
			<input type="input" id="{$_FIELD_NAME}_defaultvalue" name="{$_FIELD_NAME}_defaultvalue" class="small" />
		{/if}
	</span>
	{/foreach}
</div>