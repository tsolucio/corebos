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

{assign var="MODULEICON" value='sossession'}
{assign var="MODULESECTION" value=$MOD.SyncHelpDesk}
{assign var="MODULESECTIONDESC" value=$MOD.SyncHelpDeskDescription}
{include file='SetMenu.tpl'}

<form name="myform" action="index.php" method="POST">
	<input type="hidden" name="module" value="ServiceContracts">
	<input type="hidden" name="action" value="HDSync">
	<input type="hidden" name="mode" value="Save">
	<div class="slds-form-element slds-m-top_small">
		<label class="slds-checkbox_toggle slds-grid">
		<span class="slds-form-element__label slds-m-bottom_none">{'SyncHelpDesk'|@getTranslatedString:$MODULE}</span>
		<input type="checkbox" name="synchd" aria-describedby="synchd" {$hdsyncactive} onchange="document.myform.submit();" />
		<span id="synchd" class="slds-checkbox_faux_container" aria-live="assertive">
		<span class="slds-checkbox_faux"></span>
		<span class="slds-checkbox_on"></span>
		<span class="slds-checkbox_off"></span>
		</span>
		</label>
	</div>
</form>
