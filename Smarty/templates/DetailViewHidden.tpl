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
{*//Hidden fields for modules DetailView//  *}
<input type="hidden"  name="allselectedboxes" id="allselectedboxes">
<input name="from_link" id="from_link" type="hidden" value="DetailView">
<input type="hidden" name="cbfromid" id="cbfromid" value="{$ID}">
<input type="hidden" id="module" name="module" value="{$MODULE}">
<input type="hidden" id="record" name="record" value="{$ID}">
<input type="hidden" name="isDuplicate" value=false>
<input type="hidden" name="action">
<input type="hidden" name="return_module" value="{if isset($RETURN_MODULE)}{$RETURN_MODULE}{/if}">
<input type="hidden" name="return_id" value="{if isset($RETURN_ID)}{$RETURN_ID}{/if}">
<input type="hidden" name="return_action" value="{if isset($RETURN_ACTION)}{$RETURN_ACTION}{/if}">
{if $MODULE eq 'Accounts'}
	<input type="hidden" name="contact_id">
	<input type="hidden" name="member_id">
	<input type="hidden" name="opportunity_id">
	<input type="hidden" name="case_id">
	<input type="hidden" name="task_id">
	<input type="hidden" name="meeting_id">
	<input type="hidden" name="call_id">
	<input type="hidden" name="email_id">
	<input type="hidden" name="source_module">
	<input type="hidden" name="entity_id">

{elseif $MODULE eq 'Contacts'}
	<input type="hidden" name="reports_to_id">
	<input type="hidden" name="opportunity_id">
	<input type="hidden" name="parent_id" value="{$ID}">
	<input type="hidden" name="contact_role">
	<input type="hidden" name="task_id">
	<input type="hidden" name="meeting_id">
	<input type="hidden" name="call_id">
	<input type="hidden" name="case_id">
	<input type="hidden" name="new_reports_to_id">
	<input type="hidden" name="email_directing_module">
	{if isset($HIDDEN_PARENTS_LIST)}{$HIDDEN_PARENTS_LIST}{/if}
{elseif $MODULE eq 'Potentials'}
	<input type="hidden" name="contact_id">
	<input type="hidden" name="contact_role">
	<input type="hidden" name="opportunity_id" value="{$ID}">
	<input type="hidden" name="task_id">
	<input type="hidden" name="meeting_id">
	<input type="hidden" name="call_id">
	<input type="hidden" name="email_id">
	<input type="hidden" name="source_module">
	<input type="hidden" name="entity_id">
	<input type="hidden" name="convertmode">
	<input type="hidden" name="account_id" value="{if isset($ACCOUNTID)}{$ACCOUNTID}{/if}">
{elseif $MODULE eq 'Leads'}
	<input type="hidden" name="lead_id" value="{$ID}">
	<input type="hidden" name="parent_id" value="{$ID}">
	<input type="hidden" name="email_directing_module">
	{if isset($HIDDEN_PARENTS_LIST)}{$HIDDEN_PARENTS_LIST}{/if}
{elseif $MODULE eq 'Products' || $MODULE eq 'Vendors' || $MODULE eq 'PriceBooks' || $MODULE eq 'Services'}
	{if $MODULE eq 'Products'}
		<input type="hidden" name="product_id" value="{$ID}">
	{elseif $MODULE eq 'Vendors'}
		<input type="hidden" name="vendor_id" value="{$ID}">
	{/if}
	<input type="hidden" name="parent_id" value="{$ID}">
	<input type="hidden" name="mode">
{elseif $MODULE eq 'Emails'}
	<input type="hidden" name="contact_id" value="{$CONTACT_ID}">
	<input type="hidden" name="user_id" value="{$USER_ID}">
	<input type="hidden" name="source_module">
	<input type="hidden" name="entity_id">
{elseif $MODULE eq 'HelpDesk'}
	<input type="hidden" name="mode">
{elseif $MODULE eq 'Faq'}
	<input type="hidden" name="source_module">
	<input type="hidden" name="entity_id">
{elseif $MODULE eq 'Quotes'}
	<input type="hidden" name="convertmode">
	<input type="hidden" name="member_id">
	<input type="hidden" name="opportunity_id">
	<input type="hidden" name="case_id">
	<input type="hidden" name="task_id">
	<input type="hidden" name="meeting_id">
	<input type="hidden" name="call_id">
	<input type="hidden" name="email_id">
	<input type="hidden" name="source_module">
	<input type="hidden" name="entity_id">
{elseif $MODULE eq 'PurchaseOrder' || $MODULE eq 'SalesOrder' || $MODULE eq 'Invoice'}
	<input type="hidden" name="member_id">
	<input type="hidden" name="opportunity_id">
	<input type="hidden" name="case_id">
	<input type="hidden" name="task_id">
	<input type="hidden" name="meeting_id">
	<input type="hidden" name="call_id">
	<input type="hidden" name="email_id">
	<input type="hidden" name="source_module">
	<input type="hidden" name="entity_id">
	{if $MODULE eq 'Invoice' && $SinglePane_View neq 'true'}
		<input type="hidden" name="RLreturn_module" id="RLreturn_module" value="{$MODULE}">
		<input type="hidden" name="RLparent_id" id="RLparent_id" value="{$ID}">
	{/if}
	{if $MODULE eq 'SalesOrder'}
		<input type="hidden" name="convertmode">
	{/if}
{/if}

<input type="hidden" name="cbcustominfo1" id="cbcustominfo1" value="{if isset($smarty.request.cbcustominfo1)}{$smarty.request.cbcustominfo1|@urlencode}{/if}" />
<input type="hidden" name="cbcustominfo2" id="cbcustominfo2" value="{if isset($smarty.request.cbcustominfo2)}{$smarty.request.cbcustominfo2|@urlencode}{/if}" />
{if isset($CUSTOM_LINKS) && !empty($CUSTOM_LINKS.DETAILVIEWHTML)}
{foreach from=$CUSTOM_LINKS.DETAILVIEWHTML item=dvhtml}
	{eval var=$dvhtml->linkurl}
{/foreach}
{/if}
</form>
