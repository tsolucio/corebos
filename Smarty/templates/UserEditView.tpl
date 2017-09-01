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

<table width="100%" cellpadding="2" cellspacing="0" border="0" class="detailview_wrapper_table">
	<tr>
		<td style="padding: 0;">
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

					<table width="100%" border="0" cellpadding="0" cellspacing="0">

						<tr>
							<td valign=top align=left>
								<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz">
									<tr>
										<td style="padding: 0;">
											<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilSettings" style="height: 70px;">
												<div class="slds-grid primaryFieldRow" style="transform: translate3d(0, -8.65823px, 0);">
													<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
														<div class="profilePicWrapper slds-media slds-no-space" style="transform: scale3d(0.864715, 0.864715, 1) translate3d(4.32911px, 2.16456px, 0);">
															<div class="slds-media__figure slds-icon forceEntityIcon">
																<span class="photoContainer forceSocialPhoto">
																	<div class="small roundedSquare forceEntityIcon">
																		<span class="uiImage">
																			<img src="{'ico-users.gif'|@vtiger_imageurl:$THEME}" align="absmiddle"/>
																		</span>
																	</div>
																</span>
															</div>
														</div>
														<div class="slds-media__body">
															<h2 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
																{if $PARENTTAB neq ''}
																	<span class="uiOutputText" style="display: initial;">
																		<b>
																			<a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a>&gt;
																			<a href="index.php?module=Users&action=index&parenttab=Settings">{$MOD.LBL_USERS}</a>&gt;
																			{if $MODE eq 'edit'}
																				{$UMOD.LBL_EDITING} "{$USERNAME}"
																			{else}
																				{if $DUPLICATE neq 'true'}
																					{$UMOD.LBL_CREATE_NEW_USER}
																				{else}
																					{$APP.LBL_DUPLICATING} "{$USERNAME}"
																				{/if}
																			{/if}
																		</b>
																	</span>
																{else}
																	<span class="uiOutputText" style="display: initial;">
																		<b>{$APP.LBL_MY_PREFERENCES}</b>
																	</span>
																{/if}
																<br/>
																{if $MODE eq 'edit'}
																	<span class="small"><b>{$UMOD.LBL_EDIT_VIEW} "{$USERNAME}"</b>
																{else}
																	{if $DUPLICATE neq 'true'}
																	<span class="small"><b>{$UMOD.LBL_CREATE_NEW_USER}</b>
																	{/if}
																{/if}
															</h2>
														</div>
													</div>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<td colspan="4" style="padding: .5rem 0 0 0;">
											<div align="center">
												{if $LDAP_BUTTON neq ''}
													<input type="text" id="LdapSearchUser" class="slds-input" placeholder="{$UMOD.LBL_FORE_LASTNAME}" style="width: 25%;vertical-align: middle;">
													<input type="button" class="slds-button slds-button--neutral not-selected slds-not-selected uiButton" aria-live="assertive" value="{$UMOD.LBL_QUERY} {$LDAP_BUTTON}" onClick="QueryLdap('LdapSearchUser');">
													<select id="LdapSelectUser" class="slds-select visibility:hidden;" onChange="QueryLdap('LdapSelectUser');" style="width: 25%;vertical-align: sub;"></select>
												{/if}
												<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accesskey="{$APP.LBL_SAVE_BUTTON_KEY}" class="slds-button slds-button_success slds-button--small" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " onclick="this.form.action.value='Save'; return verify_data(EditView)" type="button" />
												<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accesskey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="slds-button slds-button--destructive slds-button--small" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " onclick="window.history.back()" type="button" />
											</div>
										</td>
									</tr>
									<tr>
										<td style="padding: 0">
											<table class="slds-table slds-no-row-hover slds-table-moz" style="border-collapse: separate;border-spacing: 1rem 2rem;">
												{foreach key=header name=blockforeach item=data from=$BLOCKS}
													<tr class="blockStyleCss">
														<td class="detailViewContainer" valign="top" style="padding: 0 .5rem">
															{strip}
															<div class="forceRelatedListSingleContainer">
																<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																	<div class="slds-card__header slds-grid">
																		<header class="slds-media slds-media--center slds-has-flexi-truncate">
																			<div class="slds-media__body">
																				<h2>
																					<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small">
																						<b>{$smarty.foreach.blockforeach.iteration}. {$header}</b>
																					</span>
																				</h2>
																			</div>
																		</header>
																	</div>
																</article>
															</div>
															{/strip}
															<!-- Handle the ui types display -->
															{include file="DisplayFields.tpl"}
															{assign var=list_numbering value=$smarty.foreach.blockforeach.iteration}
														</td>
													</tr>
												{/foreach}
													<!-- Added for HOME PAGE COMPONENT -->
													<tr class="blockStyleCss">
														<td class="detailViewContainer" valign="top">
															{strip}
															<div class="forceRelatedListSingleContainer">
																<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																	<div class="slds-card__header slds-grid">
																		<header class="slds-media slds-media--center slds-has-flexi-truncate">
																			<div class="slds-media__body">
																				<h2>
																					<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small">
																						<b>{$list_numbering+1}. {$UMOD.LBL_HOME_PAGE_COMP}</b>
																					</span>
																				</h2>
																			</div>
																		</header>
																	</div>
																</article>
															</div>
															{/strip}
															<!-- Handle the ui types display -->
															<div class="createview_field_row">
																<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table">
																	{foreach item=homeitems key=values from=$HOMEORDER}
																		<tr class="slds-line-height--reset">
																			<td class="dvtCellLabel" align="right" width="20%">{$UMOD.$values|@getTranslatedString:'Home'}</td>
																			<td class="dvtCellInfo" align="center" width="5%"><input name="{$values}" value="{$values}" {if $homeitems neq ''}checked{/if} type="radio"></td>
																			<td class="dvtCellInfo" align="left" width="35%">{$UMOD.LBL_SHOW}</td>
																			<td class="dvtCellInfo" align="center" width="5%"><input name="{$values}" value="" {if $homeitems eq ''}checked{/if} type="radio"></td>
																			<td class="dvtCellInfo" align="left" width="35%">{$UMOD.LBL_HIDE}</td>
																		</tr>
																	{/foreach}
																</table>
															</div>
														</td>
													</tr>
													<!-- Added for User Based TagCloud -->
													<tr class="blockStyleCss">
														<td class="detailViewContainer" valign="top">
															{strip}
															<div class="forceRelatedListSingleContainer">
																<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																	<div class="slds-card__header slds-grid">
																		<header class="slds-media slds-media--center slds-has-flexi-truncate">
																			<div class="slds-media__body">
																				<h2>
																					<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small">
																						<b>{$list_numbering+2}. {$UMOD.LBL_TAGCLOUD_DISPLAY}</b>
																					</span>
																				</h2>
																			</div>
																		</header>
																	</div>
																</article>
															</div>
															{/strip}
															<!-- End of Header -->
															<div class="createview_field_row">
																<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table">
																	<tr class="slds-line-height--reset">
																			<td class="dvtCellLabel" align="right" width="20%">{$UMOD.LBL_TAG_CLOUD}</td>
																		{if $TAGCLOUDVIEW eq 'true'}
																			<td class="dvtCellInfo" align="center" width="5%">
																				<input name="tagcloudview" value="true" checked type="radio"></td>
																			<td class="dvtCellInfo" align="left" width="35%">{$UMOD.LBL_SHOW}</td>
																			<td class="dvtCellInfo" align="center" width="5%">
																				<input name="tagcloudview" value="false" type="radio"></td>
																			<td class="dvtCellInfo" align="left" width="35%">{$UMOD.LBL_HIDE}</td>
																		{else}
																			<td class="dvtCellInfo" align="center" width="5%">
																				<input name="tagcloudview" value="true" type="radio"></td>
																			<td class="dvtCellInfo" align="left" width="35%">{$UMOD.LBL_SHOW}</td>
																			<td class="dvtCellInfo" align="center" width="5%">
																				<input name="tagcloudview" value="false" checked type="radio"></td>
																			<td class="dvtCellInfo" align="left" width="35%">{$UMOD.LBL_HIDE}</td>
																		{/if}
																	</tr>
																</table>
																<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table">
																	<tr class="slds-line-height--reset">
																		<td class="dvtCellLabel" align="right" width="20%">{$MOD.LBL_Show}</td>
																		<td class="dvtCellInfo" align="left" width="80%">
																			<select class="slds-select" name="showtagas" style="width: 25%;">
																				{html_options options=$tagshow_options selected=$SHOWTAGAS}
																			</select>
																		</td>
																	</tr>
																</table>
															</div>
															<!--end of Added for User Based TagCloud -->
														</td>
													</tr>
											</table>
											<table class="slds-table slds-no-row-hover slds-table-moz dvtContentSpace">
												<tr>
													<td colspan="4" style="padding: 5px;">
														<div align="center">
															<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accesskey="{$APP.LBL_SAVE_BUTTON_KEY}" class="slds-button slds-button_success slds-button--small" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " onclick="this.form.action.value='Save'; return verify_data(EditView)" type="button" />
															<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accesskey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="slds-button slds-button--destructive slds-button--small" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " onclick="window.history.back()" type="button" />
														</div>
													</td>
												</tr>
												<tr>
													<td class="small">
														<div align="right">
															<a href="#top">{$MOD.LBL_SCROLL}</a>
														</div>
													</td>
												</tr>
											</table>
										</td>
									</tr>

								</table>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</td>
	</tr>
</table>
</td>
</tr>
</table>
<br>
{$JAVASCRIPT}