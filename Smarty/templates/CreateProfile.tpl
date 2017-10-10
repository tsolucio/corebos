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

<script type="text/javascript" src="include/js/smoothscroll.js"></script>

<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td valign="top" width="100%">
				<div align=center>
					<br>
							{include file='SetMenu.tpl'}

								<!-- DISPLAY Field Access Settings-->
											<form action="index.php" method="post" name="profileform" id="form" onSubmit="if (rolevalidate()) { VtigerJS_DialogBox.block();return true; } else { return false; }">
												<input type="hidden" name="module" value="Settings">
												<input type="hidden" name="mode" value="{$MODE}">
												<input type="hidden" name="action" value="profilePrivileges">
												<input type="hidden" name="parenttab" value="Settings">
												<input type="hidden" name="parent_profile" value="{$PARENT_PROFILE}">
												<input type="hidden" name="radio_button" value="{$RADIO_BUTTON}">

												<!-- DISPLAY -->
												<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz" style="background-color: #f7f9fb;">
													<tr class="slds-text-title--caps">
														<td style="padding: 0;">
															<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilSettings" style="height: 70px;">
																<div class="slds-grid primaryFieldRow" style="transform: translate3d(0, -8.65823px, 0);">
																	<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
																		<div class="slds-media slds-no-space" style="transform: scale3d(0.864715, 0.864715, 1) translate3d(4.32911px, 2.16456px, 0);">
																			<div class="slds-media__figure slds-icon forceEntityIcon">
																				<span class="photoContainer forceSocialPhoto">
																					<div class="small roundedSquare forceEntityIcon">
																						<span class="uiImage">
																							<img src="{'ico-profile.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_PROFILES}" title="{$MOD.LBL_PROFILES}">
																						</span>
																					</div>
																				</span>
																			</div>
																		</div>
																		<div class="slds-media__body">
																			<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
																				<span class="uiOutputText">
																					<b> <a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > <a href="index.php?module=Settings&action=ListProfiles&parenttab=Settings">{$CMOD.LBL_PROFILE_PRIVILEGES}</a></b>
																				</span>
																				<span class="small">{$MOD.LBL_PROFILE_DESCRIPTION}</span>
																			</h1>
																		</div>
																	</div>
																</div>
															</div>
														</td>
													</tr>
												</table>

												<br/>

												<table border="0" cellpadding="0" cellspacing="0" width="100%">
													<tbody>
														<tr>
															<td>

																<table class="prvPrfOutline" border="0" cellpadding="0" cellspacing="0" width="100%">
																	<tbody>
																		<tr class="small">
																			<td>

																				<!-- Module name heading -->
																				<div class="forceRelatedListSingleContainer">
																					<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																						<div class="slds-card__header slds-grid">
																							<header class="slds-media slds-media--center slds-has-flexi-truncate">
																								<div class="slds-media__figure">
																									<div class="extraSmall forceEntityIcon" data-aura-rendered-by="3:1782;a" data-aura-class="forceEntityIcon">
																										<span data-aura-rendered-by="6:1782;a" class="uiImage" data-aura-class="uiImage">
																											<img src="{'prvPrfHdrArrow.gif'|@vtiger_imageurl:$THEME}">
																										</span>
																									</div>
																								</div>
																								<div class="slds-media__body">
																									<h2>
																										<span class="prvPrfBigText slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																											<b> {$CMOD.LBL_STEP_1_2} : {$CMOD.LBL_WELCOME_PROFILE_CREATE} </b>
																										</span>
																									</h2>
																								</div>
																							</header>
																						</div>
																							<div class="slds-card__body slds-card__body--inner">
																								<div class="commentData"><font class="small"> {$CMOD.LBL_SELECT_CHOICE_NEW_PROFILE} </font></div>
																							</div>
																					</article>
																				</div>

																			</td>
																		</tr>
																	</tbody>
																</table>

																<table class="slds-table slds-no-row-hover detailview_table">
																	<!-- Profile name and description -->
																	<tr class="slds-line-height--reset">
																		<td class="dvtCellLabel" width="25%">
																			<b style="color:#FF0000;font-size:16px;">{$APP.LBL_REQUIRED_SYMBOL}</b>&nbsp;<b>{$CMOD.LBL_NEW_PROFILE_NAME} : </b>
																		</td>
																		<td class="dvtCellInfo" width="75%">
																			<input type="text" name="profile_name" id="pobox" value="{$PROFILE_NAME}" class="slds-input" style="width: 100%;" />
																		</td>
																	</tr>
																	<tr class="slds-line-height--reset">
																		<td class="dvtCellLabel" width="25%"><b>{$CMOD.LBL_DESCRIPTION} : </b></td>
																		<td class="dvtCellInfo" width="75%">
																			<textarea name="profile_description" class="slds-textarea">{$PROFILE_DESCRIPTION}</textarea>
																		</td>
																	</tr>

																	<tr><td colspan="2">&nbsp;</td></tr>

																	<!-- Radio button selection -->
																	<tr class="slds-line-height--reset">
																		<td class="dvtCellLabel" width="25%">
																			{if $RADIO_BUTTON neq 'newprofile'}
																				<span class="slds-radio">
																					<input name="radiobutton" id="baseprofile" checked type="radio" value="baseprofile" />
																					<label class="slds-radio__label" for="baseprofile">
																						<span class="slds-radio--faux"></span>
																					</label>
																				</span>
																			{else}
																				<span class="slds-radio">
																					<input name="radiobutton" id="baseprofile" type="radio" value="baseprofile" />
																					<label class="slds-radio__label" for="baseprofile">
																						<span class="slds-radio--faux"></span>
																					</label>
																				</span>
																			{/if}
																		</td>
																		<td class="dvtCellInfo" width="75%">
																			{$CMOD.LBL_BASE_PROFILE_MESG}
																			<br/>
																			{$CMOD.LBL_BASE_PROFILE}
																			<select name="parentprofile" class="importBox slds-select" style="width: 50%;">
																				{foreach item=combo from=$PROFILE_LISTS}
																					{if $PARENT_PROFILE eq $combo.1}
																						<option selected value="{$combo.1}">{$combo.0}</option>
																					{else}
																						<option value="{$combo.1}">{$combo.0}</option>
																					{/if}
																				{/foreach}
																			</select>
																		</td>
																	</tr>
																	<tr><td class="text-center" colspan="2"><b>(&nbsp;{$CMOD.LBL_OR}&nbsp;)</b></td></tr>
																	<tr>
																		<td class="dvtCellLabel" align="right">
																			{if $RADIO_BUTTON eq 'newprofile'}
																				<span class="slds-radio">
																					<input name="radiobutton" id="newprofile" checked type="radio" value="newprofile" />
																					<label class="slds-radio__label" for="newprofile">
																						<span class="slds-radio--faux"></span>
																					</label>
																				</span>
																			{else}
																				<span class="slds-radio">
																					<input name="radiobutton" id="newprofile" type="radio" value="newprofile" />
																					<label class="slds-radio__label" for="newprofile">
																						<span class="slds-radio--faux"></span>
																					</label>
																				</span>
																			{/if}
																		</td>
																		<td class="dvtCellInfo" align="left">{$CMOD.LBL_BASE_PROFILE_MESG_ADV}</td>
																	</tr>
																	<tr>
																		<!-- Next step and cancel buttons -->
																		<td colspan="2" align="right">
																			<input type="button" value=" {$APP.LNK_LIST_NEXT} &rsaquo; " title="{$APP.LNK_LIST_NEXT}" name="Next" class="slds-button slds-button--small slds-button_success" onClick="return rolevalidate();"/>&nbsp;&nbsp;
																			<input type="button" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " title="{$APP.LBL_CANCEL_BUTTON_TITLE}" name="Cancel" onClick="window.history.back();" class="slds-button slds-button--small slds-button--destructive" />
																		</td>
																	</tr>
																</table>

																<!-- Scroll to top -->
																<table border="0" cellpadding="5" cellspacing="0" width="100%">
																	<tbody>
																		<tr>
																			<td class="small" align="right" nowrap="nowrap">
																				<a href="#top">{$APP.LBL_SCROLL}</a>
																			</td>
																		</tr>
																	</tbody>
																</table>

															</td>
														</tr>
													</tbody>
												</table>


											</form>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<script>
var profile_err_msg='{$MOD.LBL_ENTER_PROFILE}';
function rolevalidate()
{ldelim}
	var profilename = document.getElementById('pobox').value;
	profilename = trim(profilename);
	if(profilename != '')
		dup_validation(profilename);
	else
	{ldelim}
		alert(profile_err_msg);
		document.getElementById('pobox').focus();
		return false
	{rdelim}
	return false
{rdelim}

function dup_validation(profilename)
{ldelim}
	//var status = CharValidation(profilename,'namespace');
	//if(status)
	//{ldelim}
	jQuery.ajax({ldelim}
		method:"POST",
		url:'index.php?module=Users&action=UsersAjax&file=CreateProfile&ajax=true&dup_check=true&profile_name='+profilename,
	{rdelim}).done(function(response) {ldelim}
		if(response.indexOf('SUCCESS') > -1)
			document.profileform.submit();
		else
			alert(response);
	{rdelim});
	//{rdelim}
	//else
	//	alert(alert_arr.NO_SPECIAL+alert_arr.IN_PROFILENAME)
{rdelim}
</script>
