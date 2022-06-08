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

{assign var='MODAL' value=['label'=>$CMOD.LBL_CALSETTINGS, 'ariaDescribe'=>$CMOD.LBL_CALSETTINGS, 'hideID'=>'calSettings']}
{extends file='Components/Modal.tpl'}
{block name=ModalContent}
<form name="SharingForm" method="post" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
<input type="hidden" name="__vt5rftk" value="{''|csrf_get_tokens}">
<input type="hidden" name="module" value="Calendar4You">
<input type="hidden" name="action" value="updateCalendarSharing">
<input type="hidden" name="user_view_type" value="{if isset($smarty.request.user_view_type)}{$smarty.request.user_view_type|@vtlib_purify}{/if}">
<input type="hidden" name="view" value="{if isset($smarty.request.view)}{$smarty.request.view|@vtlib_purify}{/if}">
<input type="hidden" name="hour" value="{if isset($smarty.request.hour)}{$smarty.request.hour|@vtlib_purify}{/if}">
<input type="hidden" name="day" value="{if isset($smarty.request.day)}{$smarty.request.day|@vtlib_purify}{/if}">
<input type="hidden" name="month" value="{if isset($smarty.request.month)}{$smarty.request.month|@vtlib_purify}{/if}">
<input type="hidden" name="year" value="{if isset($smarty.request.year)}{$smarty.request.year|@vtlib_purify}{/if}">
<input type="hidden" name="current_userid" value="{$USERID}">
<input type="hidden" name="shar_userid" id="shar_userid">
{$OUT}
</form>
{/block}
{block name=ModalFooter}
	<button class="slds-button slds-button_neutral" name="cancel" onClick="hide('calSettings');">{$APP.LBL_CANCEL_BUTTON_LABEL}</button>
	<button class="slds-button slds-button_brand" name="save" onClick="userEventSharing('shar_userid', 'selected_users');controlGoogleSync();">
		{$APP.LBL_SAVE_BUTTON_LABEL}
	</button>
{/block}
