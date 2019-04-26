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
{include file='applicationmessage.tpl'}
{if $MODULE eq 'PurchaseOrder' || $MODULE eq 'SalesOrder' || $MODULE eq 'Invoice' || $MODULE eq 'Quotes' || $MODULE eq 'Issuecards' || $MODULE eq 'Receiptcards'}
	<!-- (id="frmEditView") content added to form tag and new hidden field added,  -->
	<form id="frmEditView" name="EditView" method="POST" ENCTYPE="multipart/form-data" action="index.php" onSubmit="settotalnoofrows();calcTotal();">
	<input type="hidden" name="hidImagePath" id="hidImagePath" value="{$IMAGE_PATH}"/>
	{if isset($OP_MODE) && $OP_MODE eq 'create_view'}
		<input type="hidden" name="convert_from" value="{$CONVERT_MODE}">
		<input type="hidden" name="duplicate_from" value="{if isset($DUPLICATE_FROM)}{$DUPLICATE_FROM}{/if}">
	{/if}
	{if $MODULE neq 'Quotes'}
		 <input type="hidden" name="convertmode">
	{/if}
{else}
	<form name="EditView" method="POST" ENCTYPE="multipart/form-data" action="index.php">
	<INPUT TYPE="HIDDEN" NAME="MAX_FILE_SIZE" VALUE="{$UPLOAD_MAXSIZE}">
{/if}
{if $MODULE eq 'Emails'}
	<input type="hidden" name="form">
	<input type="hidden" name="send_mail">
	<input type="hidden" name="contact_id" value="{$CONTACT_ID}">
	<input type="hidden" name="user_id" value="{$USER_ID}">
	<input type="hidden" name="filename" value="{$FILENAME}">

{elseif $MODULE eq 'Contacts'}
	<input type="hidden" name="activity_mode" value="{if isset($ACTIVITY_MODE)}{$ACTIVITY_MODE}{/if}">
	<input type="hidden" name="opportunity_id" value="{if isset($OPPORTUNITY_ID)}{$OPPORTUNITY_ID}{/if}">
	<input type="hidden" name="contact_role">
	<input type="hidden" name="case_id" value="{if isset($CASE_ID)}{$CASE_ID}{/if}">
	<input type="hidden" name="campaignid" value="{if isset($campaignid)}{$campaignid}{/if}">

{elseif $MODULE eq 'Potentials'}
	<input type="hidden" name="contact_id" value="{if isset($CONTACT_ID)}{$CONTACT_ID}{/if}">

{elseif $MODULE eq 'Calendar'}
	<input type="hidden" name="activity_mode" value="{$ACTIVITY_MODE}">
	<input type="hidden" name="product_id" value="{$PRODUCTID}">

{elseif $MODULE eq 'Leads'}
	<input type="hidden" name="campaignid" value="{if isset($campaignid)}{$campaignid}{/if}">

{elseif $MODULE eq 'Documents'}
	<input type="hidden" name="max_file_size" value="{$MAX_FILE_SIZE}">
	<input type="hidden" name="form">
	<input type="hidden" name="email_id" value="{if isset($EMAILID)}{$EMAILID}{/if}">
	<input type="hidden" name="ticket_id" value="{if isset($TICKETID)}{$TICKETID}{/if}">
	<input type="hidden" name="fileid" value="{if isset($FILEID)}{$FILEID}{/if}">
	<input type="hidden" name="parentid" value="{if isset($PARENTID)}{$PARENTID}{/if}">

{elseif $MODULE eq 'Products'}
	<input type="hidden" name="activity_mode" value="{if isset($ACTIVITY_MODE)}{$ACTIVITY_MODE}{/if}">
{/if}

<input type="hidden" name="pagenumber" value="{if isset($smarty.request.start)}{$smarty.request.start|@vtlib_purify}{/if}">
<input type="hidden" name="module" value="{$MODULE}">
<input type="hidden" name="record" value="{$ID}">
<input type="hidden" name="mode" value="{$MODE}">
<input type="hidden" name="action">
<input type="hidden" name="saverepeat" value="0">
<input type="hidden" name="parenttab" value="{$CATEGORY}">
<input type="hidden" name="return_module" value="{if isset($RETURN_MODULE)}{$RETURN_MODULE}{/if}">
<input type="hidden" name="return_id" value="{if isset($RETURN_ID)}{$RETURN_ID}{/if}">
<input type="hidden" name="return_action" value="{if isset($RETURN_ACTION)}{$RETURN_ACTION}{/if}">
<input type="hidden" name="return_viewname" value="{if isset($RETURN_VIEWNAME)}{$RETURN_VIEWNAME}{/if}">
<input type="hidden" name="createmode" value="{$CREATEMODE}" />
<input type="hidden" name="cbcustominfo1" id="cbcustominfo1" value="{if isset($smarty.request.cbcustominfo1)}{$smarty.request.cbcustominfo1|@urlencode}{/if}" />
<input type="hidden" name="cbcustominfo2" id="cbcustominfo2" value="{if isset($smarty.request.cbcustominfo2)}{$smarty.request.cbcustominfo2|@urlencode}{/if}" />
{if isset($DUPLICATE) && $DUPLICATE eq 'true'}
<input type="hidden" name="__cbisduplicatedfromrecordid" value="{$__cbisduplicatedfromrecordid}" />
{/if}
<input type="hidden" name="Module_Popup_Edit" value="{if isset($smarty.request.Module_Popup_Edit)}{$smarty.request.Module_Popup_Edit|@urlencode}{/if}" />
<input name='search_url' id="search_url" type='hidden' value='{if isset($SEARCH)}{$SEARCH}{/if}'>

