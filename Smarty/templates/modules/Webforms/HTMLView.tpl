{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*********************************************************************************/
-->*}
{* NOTE: PLEASE MAKE SURE THE SPACES BESIDE TAGS ARE STRIPPED TO PRESEVE FORMATTING OF THE OUTPUT *}
<form name="{$WEBFORMMODEL->getName()}" action="{$ACTIONPATH}/modules/Webforms/capture.php" method="post" accept-charset="utf-8">
	<p>
		<input type="hidden" name="publicid" value="{$WEBFORMMODEL->getpublicId()}"></input>
		<input type="hidden" name="name" value="{$WEBFORMMODEL->getName()}"></input>
	</p>
	{foreach item=field from=$WEBFORMFIELDS name=fieldloop}{assign var=fieldinfo value=$WEBFORM->getFieldInfo($WEBFORMMODEL->getTargetModule(), $field->getFieldName())}

	<p>
		<label>{$fieldinfo.label}</label>
		{if $fieldinfo.type.name eq picklist | $fieldinfo.type.name eq multipicklist}
<select name="{$field->getNeutralizedField()}[]" {if $field->getRequired() eq 1}required="true"{/if}{if $fieldinfo.type.name eq multipicklist}multiple="multiple" size="5"{/if}>{foreach item=option from=$fieldinfo.type.picklistValues name=optionloop}

		<option value="{$option.value|escape:'html'}">{$option.label|escape:'html'}</option>
		{/foreach}
</select>
{elseif $fieldinfo.type.name eq boolean}
<input type="checkbox"  name="{$field->getNeutralizedField()}" >
	{else}{if $field->getNeutralizedField() eq salutationtype}
<select name="{$field->getNeutralizedField()}" {if $field->getRequired() eq 1}required="true"{/if} >
			<option value="">--None--</option>
			<option value="Mr.">Mr.</option>
			<option value="Ms.">Ms.</option>
			<option value="Mrs.">Mrs.</option>
			<option value="Dr.">Dr.</option>
			<option value="Prof.">Prof</option>
		</select>{else}<input type="text" value="" name="{$field->getNeutralizedField()}"  {if $field->getRequired() eq 1}required="true"{/if}></input>{/if}{/if}

	</p>{/foreach}

	<p>
		<input type="submit" value="Submit" ></input>
	</p>
</form>