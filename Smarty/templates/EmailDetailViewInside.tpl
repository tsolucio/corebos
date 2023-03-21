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
{if $smarty.request.action eq 'EmailsAjax'}
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$LBL_CHARSET}">
<title>{$MOD.TITLE_VTIGERCRM_MAIL}</title>
<link REL="SHORTCUT ICON" HREF="themes/images/favicon.ico">
<link rel="stylesheet" type="text/css" media="all" href="themes/{$THEME}/style.css">
<link rel="stylesheet" href="include/LD/assets/styles/salesforce-lightning-design-system.css" type="text/css" />
<link rel="stylesheet" href="include/LD/assets/styles/override_lds.css" type="text/css" />
<script type="text/javascript" src="include/jquery/jquery.js"></script>
{include file='BrowserVariables.tpl'}
<script type="text/javascript" src="include/js/vtlib.js"></script>
<script type="text/javascript" src="include/js/general.js"></script>
</head>
<body marginheight="0" marginwidth="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
{/if}
<style>
#adv_filter_div > table:first-child td,
#adv_filter_div > div:nth-child(3) > table:first-child tr:first-child td,
.dvtCellLabel {
  text-align: unset;
}
</style>
{assign var=CREATE_PERMISSION value='no'}
{assign var=DELETE value='no'}
{assign var=EDIT_PERMISSION value='no'}
<div class="slds-page-header">
<div class="slds-page-header__row">
	<div class="slds-page-header__col-title">
		<div class="slds-media">
			<div class="slds-media__body">
				<div class="slds-page-header__name">
					<div class="slds-page-header__name-title">
						<h1>
							<span class="slds-page-header__title slds-truncate" title="{$MOD.LBL_EMAILS_TITLE}">
								{$MOD.LBL_EMAILS_TITLE}
							</span>
						</h1>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
<table class="slds-card" style="width:96%; margin:auto;">
<tbody>
	{foreach item=row from=$BLOCKS}
	{foreach item=elements key=title from=$row}
	{if $elements.fldname eq 'subject'}
	<tr>
	<td class="lvtCol" width="15%" style="padding: 5px;" align="right"><b>{$MOD.LBL_FROM}</b></td>
	<td class="dvtCellLabel" style="padding: 5px;">&nbsp;{$FROM_MAIL}</td>
	<td class="dvtCellLabel" width="20%" rowspan="5"><div id="attach_cont" class="addEventInnerBox" style="overflow:auto;height:140px;width:100%;position:relative;left:0px;top:0px;"></td>
	</tr>
	<tr>
	<td class="lvtCol" width="15%" height="50px" style="padding: 5px;" align="right"><b>{$MOD.LBL_TO}</b></td>
	<td class="dvtCellLabel" style="padding: 5px;">&nbsp;{$TO_MAIL}</td>
	</tr>
		{if 'ccmail'|@emails_checkFieldVisiblityPermission eq '0'}
		<tr>
		<td class="lvtCol" style="padding: 5px;" align="right"><b>{$MOD.LBL_CC}</b></td>
		<td class="dvtCellLabel" style="padding: 5px;">&nbsp;{$CC_MAIL}</td>
		</tr>
		{/if}
		{if 'bccmail'|@emails_checkFieldVisiblityPermission eq '0'}
		<tr>
		<td class="lvtCol" style="padding: 5px;" align="right"><b>{$MOD.LBL_BCC}</b></td>
		<td class="dvtCellLabel" style="padding: 5px;">&nbsp;{$BCC_MAIL}</td>
		</tr>
		{/if}
	<tr>
		<td class="lvtCol" style="padding: 5px;" align="right"><b>{$MOD.LBL_DATE}</b></td>
		<td class="dvtCellLabel" style="padding: 5px;">{$DATE_START}&nbsp;</td>
	</tr>
	<tr>
		<td class="lvtCol" style="padding: 5px;" align="right"><b>{$MOD.LBL_TIME}</b></td>
		<td class="dvtCellLabel" style="padding: 5px;">{$TIME_START}&nbsp;</td>
	</tr>
	<tr>
	<td class="lvtCol" style="padding: 5px;" align="right"><b>{$MOD.LBL_SUBJECT}</b></td>
	<td class="dvtCellLabel" style="padding: 5px;">&nbsp;{$elements.value}</td>
	</tr>
	<tr>
	<td colspan=3><table width="100%" border=0>
	<tr>
	<td class="lvtCol" style="padding: 5px;" align="right"><b>{'Delivered'|@getTranslatedString:'Messages'}</b></td>
	<td class="dvtCellLabel" style="padding: 5px;">&nbsp;{$EMDelivered}</td>
	<td class="lvtCol" style="padding: 5px;" align="right"><b>{'Dropped'|@getTranslatedString:'Messages'}</b></td>
	<td class="dvtCellLabel" style="padding: 5px;">&nbsp;{$EMDropped}</td>
	<td class="lvtCol" style="padding: 5px;" align="right"><b>{'Bounce'|@getTranslatedString:'Messages'}</b></td>
	<td class="dvtCellLabel" style="padding: 5px;">&nbsp;{$EMBounce}</td>
	</tr>
	<tr>
	<td class="lvtCol" style="padding: 5px;" align="right"><b>{'Open'|@getTranslatedString:'Messages'}</b></td>
	<td class="dvtCellLabel" style="padding: 5px;">&nbsp;{$EMOpen}</td>
	<td class="lvtCol" style="padding: 5px;" align="right"><b>{'Clicked'|@getTranslatedString:'Messages'}</b></td>
	<td class="dvtCellLabel" style="padding: 5px;">&nbsp;{$EMClicked}</td>
	<td class="lvtCol" style="padding: 5px;" align="right"><b>{'Unsubscribe'|@getTranslatedString:'Messages'}</b></td>
	<td class="dvtCellLabel" style="padding: 5px;">&nbsp;{$EMUnsubscribe}</td>
	</tr>
	</table>
	</tr>
	<tr>
	<td colspan="3" class="dvtCellLabel" style="padding: 10px;text-align:center;">
	{assign var='BUTTONWITHICON' value=[
		'variation' => 'outline-brand',
		'title' => $MOD.LBL_REPLY_BUTTON,
		'id' => 'Send',
		'size' => 'small',
		'position' => 'left',
		'library' => 'utility',
		'icon' => 'reply',
		'onclick' => "OpenCompose('{$ID}','reply')"
	]}
	{include file='Components/ButtonWithIcon.tpl'}
	{assign var='BUTTONWITHICON' value=[
		'variation' => 'outline-brand',
		'title' => $MOD.LBL_FORWARD_BUTTON,
		'id' => 'forward',
		'size' => 'small',
		'position' => 'left',
		'library' => 'utility',
		'icon' => 'forward',
		'onclick' => "OpenCompose('{$ID}','forward')"
	]}
	{include file='Components/ButtonWithIcon.tpl'}
	{assign var='BUTTONWITHICON' value=[
		'variation' => 'outline-brand',
		'title' => $APP.LBL_EDIT,
		'id' => 'edit',
		'size' => 'small',
		'position' => 'left',
		'library' => 'utility',
		'icon' => 'edit',
		'onclick' => "OpenCompose('{$ID}','edit')"
	]}
	{include file='Components/ButtonWithIcon.tpl'}
	{if $smarty.request.action eq 'EmailsAjax'}
	{assign var='BUTTONWITHICON' value=[
		'variation' => 'text-destructive',
		'title' => $APP.LBL_CANCEL_BUTTON_LABEL,
		'id' => 'cancel',
		'size' => 'small',
		'position' => 'left',
		'library' => 'utility',
		'icon' => 'close',
		'onclick' => 'window.close()'
	]}
	{include file='Components/ButtonWithIcon.tpl'}
	{/if}
	{assign var='BUTTONWITHICON' value=[
		'variation' => 'outline-brand',
		'title' => $MOD.LBL_PRINT_EMAIL,
		'id' => 'print',
		'size' => 'small',
		'position' => 'left',
		'library' => 'utility',
		'icon' => 'print',
		'onclick' => "OpenCompose('{$ID}', 'print')"
	]}
	{include file='Components/ButtonWithIcon.tpl'}
	</td>
	</tr>
	{elseif $elements.fldname eq 'description'}
	<tr>
	<td style="padding: 5px;" colspan="3" valign="top"><div style="overflow:auto;height:415px;width:100%;">{$elements.value}</div></td>
	</tr>
	{elseif $elements.fldname eq 'filename'}
	<tr><td colspan="3">
	<div id="attach_temp_cont" style="display:none;">
		<table class="small" width="100% ">
		{foreach item=attachments from=$elements.options}
			<tr><td width="90%">{$attachments}</td></tr>
		{/foreach}
		</table>
	</div>
	</td></tr>
	{/if}
	{/foreach}
	{/foreach}
</table>
<script>
document.getElementById('attach_cont').innerHTML = document.getElementById('attach_temp_cont').innerHTML;
</script>
{if $smarty.request.action eq 'EmailsAjax'}
</body>
</html>
{/if}
