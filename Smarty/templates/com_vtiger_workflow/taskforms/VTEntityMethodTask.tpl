{*<!--
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
-->*}
<script src="modules/com_vtiger_workflow/resources/entitymethodtask.js" ></script>

<!-- Task Operations -->
<div class="slds-grid slds-gutters slds-grid_vertical-align-center">
<div class="slds-col slds-size_2-of-12 slds-text-align_right slds-p-around_small">
	<span><b>{$MOD.LBL_METHOD_NAME}</b></span>
</div>

<div class="slds-col slds-size_3-of-12 slds-p-around_small">
	<div class="slds-form-element">
		<div class="slds-form-element__control">
			<div class="slds-select_container">
				<span id="method_name_select_busyicon"> <b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"> </span>
					<select class="slds-select slds-page-header__meta-text" name="methodName" id="method_name_select">
					</select>
				<span id="message_text" style="display: none;">{$MOD.NO_METHOD_AVAILABLE}</span>
			</div>
		</div>
	</div>
</div>

</div>

<script>
var moduleName = '{$entityName}';
var methodName = '{if isset($task->methodName)}{$task->methodName}{/if}';
entityMethodScript(jQuery);
</script>