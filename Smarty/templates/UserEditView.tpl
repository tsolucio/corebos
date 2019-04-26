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
<script type="text/javascript" src="include/js/ColorPicker2.js"></script>
<script type="text/javascript" src="include/js/smoothscroll.js"></script>
<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
var cp2 = new ColorPicker('window');

function pickColor(color)
{ldelim}
	ColorPicker_targetInput.value = color;
	ColorPicker_targetInput.style.backgroundColor = color;
{rdelim}

function openPopup(){ldelim}
	window.open("index.php?module=Users&action=UsersAjax&file=RolePopup&parenttab=Settings","roles_popup_window","height=425,width=640,toolbar=no,menubar=no,dependent=yes,resizable =no");
{rdelim}
</script>

<script>
function check_duplicate()
{ldelim}
	var user_name = window.document.EditView.user_name.value;
	var status = CharValidation(user_name,'name');
	
	VtigerJS_DialogBox.block();
	if(status)
	{ldelim}
	jQuery.ajax({ldelim}
				method:"POST",
				url:'index.php?module=Users&action=UsersAjax&file=Save&ajax=true&dup_check=true&userName='+user_name
	{rdelim}).done(function(response) {ldelim}
				if(response.indexOf('SUCCESS') > -1)
				{ldelim}
				//	$('user_status').disabled = false;
					document.EditView.submit();
				{rdelim}
					else {ldelim}
						VtigerJS_DialogBox.unblock();
						alert(response);
					{rdelim}
			{rdelim}
		);
	{rdelim}
	else
		alert(alert_arr.NO_SPECIAL+alert_arr.IN_USERNAME)
{rdelim}

	// sCommand = "LdapSearchUser" --> search a user which meets the name entered by the admin --> fill Drop Down box
	// sCommand = "LdapSelectUser" --> retrieve the details of the user --> Fill all fields
	function QueryLdap(sCommand)
	{
		sUser = document.getElementById(sCommand).value;

		if (sCommand == "LdapSearchUser") // hide Drop-Down box
			document.getElementById("LdapSelectUser").style.visibility="hidden";

		jQuery.ajax({
			method: 'POST',
			url:'index.php?module=Users&action=UsersAjax&file=QueryLdap&command='+sCommand+'&user='+sUser
			}).done(function (response) {
				if (response.indexOf("Error=") == 0)
				{
					sError = response.substring(6);
					alert (sError);
				}
				else if (response.indexOf("Options=") == 0)
				{
					sOptions = response.substring(8).split("\n");
					var oSelBox = document.getElementById("LdapSelectUser");
					oSelBox.innerHTML = "";
					for (o=0; o<sOptions.length; o++)
					{
						sParts = sOptions[o].split("\t");
						// Using DOM here because assigning innerHTML does not work on MSIE 6.0
						var oOption = document.createElement("OPTION");
						oOption.value = sParts[0];
						oOption.text  = sParts[1];
						if (sParts[0].length) oOption.text += " (" + sParts[0] + ")";
						try
						{
							oSelBox.add(oOption, null); // Standard compliant
						}
						catch (ex)
						{
							oSelBox.add(oOption); // Internet Explorer
						}
					}
					oSelBox.style.visibility="visible";
				}
				else if (response.indexOf("Values=") == 0)
				{
					sValues = response.substring(7).split("\n");
					for (v=0; v<sValues.length; v++)
					{
						sParts = sValues[v].split("\t");
						try { document.EditView[sParts[0]].value = sParts[1]; }
						catch (ex) {}
					}
				}
			});
	}
</script>

<!-- vtlib customization: Help information assocaited with the fields -->
{if $FIELDHELPINFO}
<script type='text/javascript'>
{literal}var fieldhelpinfo = {}; {/literal}
{foreach item=FIELDHELPVAL key=FIELDHELPKEY from=$FIELDHELPINFO}
	fieldhelpinfo["{$FIELDHELPKEY}"] = "{$FIELDHELPVAL}";
{/foreach}
</script>
{/if}
<!-- END -->

<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
	<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
	<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
	<br>
	<div align=center>
	{if $PARENTTAB eq 'Settings'}
		{include file='SetMenu.tpl'}
	{/if}

	<form name="EditView" method="POST" action="index.php" ENCTYPE="multipart/form-data" onsubmit="VtigerJS_DialogBox.block();">
		<input type="hidden" name="module" value="Users">
		<input type="hidden" name="record" value="{if isset($ID)}{$ID}{/if}">
		<input type="hidden" name="mode" value="{$MODE}">
		<input type='hidden' name='parenttab' value='{$PARENTTAB}'>
		<input type="hidden" name="action">
		<input type="hidden" name="return_module" value="{$RETURN_MODULE}">
		<input type="hidden" name="return_id" value="{$RETURN_ID}">
		<input type="hidden" name="return_action" value="{$RETURN_ACTION}">
		<input type="hidden" name="tz" value="Europe/Berlin">
		<input type="hidden" name="holidays" value="de,en_uk,fr,it,us,">
		<input type="hidden" name="workdays" value="0,1,2,3,4,5,6,">
		<input type="hidden" name="namedays" value="">
		<input type="hidden" name="weekstart" value="1">
		<input type="hidden" name="hour_format" value="{$HOUR_FORMAT}">
		<input type="hidden" name="start_hour" value="{$START_HOUR}">
		<input type="hidden" name="form_token" value="{$FORM_TOKEN}">

	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="settingsSelUITopLine">
	<tr><td align="left">
		<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
		<tr>
			<td rowspan="2" width="50"><img src="{'ico-users.gif'|@vtiger_imageurl:$THEME}" align="absmiddle"></td>
			<td>
				<span class="lvtHeaderText">
				{if $PARENTTAB neq ''}
				<b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString} </a> &gt; <a href="index.php?module=Users&action=index&parenttab=Settings">{$MOD.LBL_USERS}</a> &gt;
					{if $MODE eq 'edit'}
						{$UMOD.LBL_EDITING} "{$USERNAME}"
					{else}
						{if $DUPLICATE neq 'true'}
						{$UMOD.LBL_CREATE_NEW_USER}
						{else}
						{$APP.LBL_DUPLICATING} "{$USERNAME}"
						{/if}
					{/if}
					</b></span>
				{else}
					<span class="lvtHeaderText"><b>{$APP.LBL_MY_PREFERENCES}</b></span>
				{/if}
			</td>
			<td rowspan="2" nowrap>&nbsp;
			</td>
		</tr>
		<tr>
			{if $MODE eq 'edit'}
				<td><b class="small">{$UMOD.LBL_EDIT_VIEW} "{$USERNAME}"</b>
			{else}
				{if $DUPLICATE neq 'true'}
				<td><b class="small">{$UMOD.LBL_CREATE_NEW_USER}</b>
				{/if}
			{/if}
			</td>
		</tr>
		</table>
	</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td nowrap align="right" class="cblds-t-align_right">
			{if $LDAP_BUTTON neq ''}
				<input type="text" id="LdapSearchUser" class="detailedViewTextBox" style="width:150px;" placeholder="{$UMOD.LBL_FORE_LASTNAME}">
				<input type="button" class="crmbutton small create" value="{$UMOD.LBL_QUERY} {$LDAP_BUTTON}" onClick="QueryLdap('LdapSearchUser');">
				<select id="LdapSelectUser" class="small" style="width:250px; visibility:hidden;" onChange="QueryLdap('LdapSelectUser');"></select>
			{/if}
			<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accesskey="{$APP.LBL_SAVE_BUTTON_KEY}" class="small crmbutton save" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " onclick="this.form.action.value='Save'; return verify_data(EditView)" type="button" />
			<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accesskey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="small crmbutton cancel" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " onclick="window.history.back()" type="button" />
		</td>
	</tr>
	<tr><td class="padTab" align="left">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr><td colspan="2">
			<table align="center" border="0" cellpadding="0" cellspacing="0" width="99%">
			<tr>
				<td align="left" valign="top">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr><td align="left">
						{foreach key=header name=blockforeach item=data from=$BLOCKS}
						<br>
						<table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
						<tr>{strip}
							<td class="big"><strong>{$smarty.foreach.blockforeach.iteration}. {$header}</strong></td>
							<td class="small" align="right">&nbsp;</td>
						{/strip}</tr>
						</table>
						<table border="0" cellpadding="5" cellspacing="0" width="100%">
						<!-- Handle the ui types display -->
							{include file="DisplayFields.tpl"}
						</table>
						{assign var=list_numbering value=$smarty.foreach.blockforeach.iteration}
					{/foreach}
				<br>
				<table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
					<tr>
						<td class="big"><strong>{$list_numbering+1}. {$UMOD.LBL_HOME_PAGE_COMP}</strong></td>
						<td class="small" align="right">&nbsp;</td>
					</tr>
				</table>
				<table border="0" cellpadding="5" cellspacing="0" width="100%" id="useredit__homeorder">
				{foreach item=homeitems key=values from=$HOMEORDER}
					<tr>
						<td class="dvtCellLabel" align="right" width="30%">{$UMOD.$values|@getTranslatedString:'Home'}</td>
						<td class="dvtCellInfo" align="center" width="5%"><input name="{$values}" value="{$values}" {if $homeitems neq ''}checked{/if} type="radio"></td>
						<td class="dvtCellInfo" align="left" width="20%">{$UMOD.LBL_SHOW}</td>
						<td class="dvtCellInfo" align="center" width="5%"><input name="{$values}" value="" {if $homeitems eq ''}checked{/if} type="radio"></td>
						<td class="dvtCellInfo" align="left">{$UMOD.LBL_HIDE}</td>
					</tr>
				{/foreach}
				</table>
				<!-- Added for User Based TagCloud -->
				<table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
				<tr>
					<td class="big"><strong>{$list_numbering+2}. {$UMOD.LBL_TAGCLOUD_DISPLAY}</strong></td>
					<td class="small" align="right">&nbsp;</td>
				</tr>
				</table>
				<!-- End of Header -->
				<table border="0" cellpadding="5" cellspacing="0" width="100%">
					<tr><td class="dvtCellLabel" align="right" width="30%">{$UMOD.LBL_TAG_CLOUD}</td>
				{if $TAGCLOUDVIEW eq 'true'}
					<td class="dvtCellInfo" align="center" width="5%">
					<input name="tagcloudview" value="true" checked type="radio"></td><td class="dvtCellInfo" align="left" >{$UMOD.LBL_SHOW}</td>
					<td class="dvtCellInfo" align="center" width="5%">
					<input name="tagcloudview" value="false" type="radio"></td><td class="dvtCellInfo" align="left">{$UMOD.LBL_HIDE}</td>
				{else}
					<td class="dvtCellInfo" align="center" width="5%">
					<input name="tagcloudview" value="true" type="radio"></td><td class="dvtCellInfo" align="left">{$UMOD.LBL_SHOW}</td>
					<td class="dvtCellInfo" align="center" width="5%">
					<input name="tagcloudview" value="false" checked type="radio"></td><td class="dvtCellInfo" align="left">{$UMOD.LBL_HIDE}</td>
				{/if}
					</tr>
					<tr><td class="dvtCellLabel" align="right" width="30%">{$MOD.LBL_Show}</td>
						<td class="dvtCellInfo" align="left" colspan=4>
							<select class="small" name="showtagas">
							{html_options options=$tagshow_options selected=$SHOWTAGAS}
							</select>
						</td>
					</tr>
				</table>
				<!--end of Added for User Based TagCloud -->
				<br>
				<tr><td colspan=4>&nbsp;</td></tr>
						<tr>
							<td colspan=4 align="right" class="cblds-t-align_right">
							<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accesskey="{$APP.LBL_SAVE_BUTTON_KEY}" class="small crmbutton save" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " onclick="this.form.action.value='Save'; return verify_data(EditView)" type="button" />
							<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accesskey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="small crmbutton cancel" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " onclick="window.history.back()" type="button" />
							</td>
						</tr>
					</table>
					</td></tr>
					</table>
					</td></tr>
				</table>
				<br>
				</td></tr>
				<tr><td class="small cblds-t-align_right"><div align="right"><a href="#top">{$MOD.LBL_SCROLL}</a></div></td></tr>
				</table>
			</td>
			</tr>
			</table>
			</form>	
</td>
</tr>
</table>
</td></tr></table>
<br>
{$JAVASCRIPT}