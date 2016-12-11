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
{if $MODULE eq 'PurchaseOrder' || $MODULE eq 'SalesOrder' || $MODULE eq 'Invoice' || $MODULE eq 'Quotes'}
	<!-- (id="frmEditView") content added to form tag and new hidden field added,  -->
	<form id="frmEditView" name="EditView" method="POST" ENCTYPE="multipart/form-data" action="index.php" onSubmit="settotalnoofrows();calcTotal();">
	<input type="hidden" name="hidImagePath" id="hidImagePath" value="{$IMAGE_PATH}"/>
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
	<input type="hidden" name="activity_mode" value="{$ACTIVITY_MODE}">
	<input type="hidden" name="opportunity_id" value="{$OPPORTUNITY_ID}">
	<input type="hidden" name="contact_role">
	<input type="hidden" name="case_id" value="{$CASE_ID}">
	<input type="hidden" name="campaignid" value="{$campaignid}">

{elseif $MODULE eq 'Potentials'}
	<input type="hidden" name="contact_id" value="{$CONTACT_ID}">

{elseif $MODULE eq 'Calendar'}
	<input type="hidden" name="activity_mode" value="{$ACTIVITY_MODE}">
	<input type="hidden" name="product_id" value="{$PRODUCTID}">

{elseif $MODULE eq 'Leads'}
	<input type="hidden" name="campaignid" value="{$campaignid}">

{elseif $MODULE eq 'Documents'}
	<input type="hidden" name="max_file_size" value="{$MAX_FILE_SIZE}">
	<input type="hidden" name="form">
	<input type="hidden" name="email_id" value="{$EMAILID}">
	<input type="hidden" name="ticket_id" value="{$TICKETID}">
	<input type="hidden" name="fileid" value="{$FILEID}">
	<input type="hidden" name="parentid" value="{$PARENTID}">

{elseif $MODULE eq 'Products'}
	<input type="hidden" name="activity_mode" value="{$ACTIVITY_MODE}">
{/if}

<input type="hidden" name="pagenumber" value="{if isset($smarty.request.start)}{$smarty.request.start|@vtlib_purify}{/if}">
<input type="hidden" name="module" value="{$MODULE}">
<input type="hidden" name="record" value="{$ID}">
<input type="hidden" name="mode" value="{$MODE}">
<input type="hidden" name="action">
<input type="hidden" name="parenttab" value="{$CATEGORY}">
<input type="hidden" name="return_module" value="{if isset($RETURN_MODULE)}{$RETURN_MODULE}{/if}">
<input type="hidden" name="return_id" value="{if isset($RETURN_ID)}{$RETURN_ID}{/if}">
<input type="hidden" name="return_action" value="{if isset($RETURN_ACTION)}{$RETURN_ACTION}{/if}">
<input type="hidden" name="return_viewname" value="{if isset($RETURN_VIEWNAME)}{$RETURN_VIEWNAME}{/if}">
<input type="hidden" name="createmode" value="{$CREATEMODE}" />
<input type="hidden" name="cbcustominfo1" value="{if isset($smarty.request.cbcustominfo1)}{$smarty.request.cbcustominfo1|@urlencode}{/if}" />
<input type="hidden" name="cbcustominfo2" value="{if isset($smarty.request.cbcustominfo2)}{$smarty.request.cbcustominfo2|@urlencode}{/if}" />

