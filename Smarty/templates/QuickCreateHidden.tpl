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

<form name="QcEditView" onSubmit="return getFormValidate();" method="POST" action="index.php" ENCTYPE="multipart/form-data">
	<input type="hidden" name="savefromqc" value="1">
{if isset($FROM) && $FROM eq 'popup'}
	<input type="hidden" name="from" value="{$FROM}">
	<input type="hidden" name="return_action" value="Popup">
	<input type="hidden" name="return_module" value="{$MODULE}">
	<input type="hidden" name="search_url" value="{$URLPOPUP}">
{/if}
	<input type="hidden" name="module" value="{$MODULE}">
	<input type="hidden" name="record" value="">
	<input type="hidden" name="action" value="Save">
