{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  coreBOS Open Source
   * The Initial Developer of the Original Code is coreBOS.
   * Portions created by coreBOS are Copyright (C) coreBOS.
   * All Rights Reserved.
 ********************************************************************************/
-->*}

{assign var="MODULEICON" value='sync'}
{assign var="MODULESECTION" value='SyncHelpDesk'|@getTranslatedString:$MODULE}
{assign var="MODULESECTIONDESC" value='SyncHelpDeskDescription'|@getTranslatedString:$MODULE}
{include file='SetMenu.tpl'}
<form name="myform" action="index.php" method="POST">
	<input type="hidden" name="module" value="ServiceContracts">
	<input type="hidden" name="action" value="HDSync">
	<input type="hidden" name="mode" value="Save">
	<div class="slds-form-element slds-m-top_small slds-card slds-m-left_medium slds-m-right_medium slds-p-around_small">
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
