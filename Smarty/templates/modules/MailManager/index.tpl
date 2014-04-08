{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ('License'); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{include file='Buttons_List1.tpl'}
<script type='text/javascript' src='include/js/json.js'></script>
<script type='text/javascript' src='include/ckeditor/ckeditor.js'></script>

<script type='text/javascript' src='modules/MailManager/resources/jquery-1.6.2.min.js'></script>
<script type='text/javascript' src='modules/MailManager/resources/jquery-ui-1.8.16.custom.min.js'></script>
<script type='text/javascript'>
	jQuery.noConflict();
</script>

<script type='text/javascript' src='modules/MailManager/resources/jquery.tokeninput.js'></script>
<link rel='stylesheet' type='text/css' href='modules/MailManager/resources/token-input-facebook.css'>

<script type='text/javascript' src='modules/MailManager/MailManager.js'></script>

<link href="modules/MailManager/third-party/AjaxUpload/fileuploader.css" rel="stylesheet" type="text/css">
<script src="modules/MailManager/third-party/AjaxUpload/fileuploader.js" type="text/javascript"></script>
<script src="modules/MailManager/MailManagerUploadFile.js" type="text/javascript"></script>

{* Parse the translation string applicable to javascript *}
<script type='text/javascript'>
var MailManageri18nInfo = {ldelim}{rdelim};
{foreach item=i18nValue key=i18nKey from=$MOD}
	{if strpos($i18nKey, 'JSLBL_') === 0}
		MailManageri18nInfo['{$i18nKey}'] = '{$i18nValue}';
	{/if}
{/foreach}
</script>

<table border=0 cellspacing=0 cellpadding=0 width=98%>
<tr>
	<td valign=top align=right width='8px'><img src='{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}' ></td>
	<td class='showPanelBg' valign='top' >

		<div id='_progress_' style='float: right; display: none; position: absolute; right: 35px; font-weight: bold;'>
		<span id='_progressmsg_'>...</span><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border='0' align='absmiddle'></div>

		<div style='padding: 20px 5px 20px 20px; min-height: 300px;' id='_mailmanagermaindiv_'>
			<table width="100%" cellpadding=0 cellspacing=0 align=left>
			<tr valign=top>
				<td nowrap="nowrap" width="15%">
					<div id="_quicklinks_mainuidiv_">{include file="modules/MailManager/Mainui.QuickLinks.tpl"}</div>
					<div id='_folderprogress_' style='float: right; display: none; position: absolute;left: 30px; font-weight: bold;'>
						<span>{$MOD.JSLBL_LOADING_FOLDERS}</span><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border='0' align='absmiddle'>
					</div>
					<div id="_mainfolderdiv_" >
					</div>
				</td>
				<td width="85%">
					<span id="_messagediv_">{if $ERROR}<p>{$ERROR}</p>{/if}</span>
						<div id="_contentdiv_"></div>
						<div id="_contentdiv2_"></div>
						<div id="_settingsdiv_"></div>
						<div id="_relationpopupdiv_" style="display:none;position:absolute;width:800px;z-index:80000;"></div>
						<div id="_replydiv_" style="display:none;">
							{include file="modules/MailManager/Mail.Send.tpl"}
						</div>
						<div id="replycontentdiv" style="display:none;">
							{include file="modules/MailManager/Mail.Send.tpl"}
						</div>
				</td>
			</tr>
			</table>
		</div>
		<div id = '__vtiger__'></div>
	</td>
	<td valign=top align=right width='8px'><img src='{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}'></td>
</tr>
</table>

<script type='text/javascript'>
{literal}
	Event.observe(window, 'load', MailManager.mainui);
{/literal}
</script>
<input type="hidden" name="module" value="MailManager">