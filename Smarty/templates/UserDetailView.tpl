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
<script type="text/javascript" src="include/js/dtlviewajax.js"></script>
<script type="text/javascript" src="include/js/smoothscroll.js"></script>
<span id="crmspanid" style="display:none;position:absolute;" onmouseover="show('crmspanid');">
	<a class="link"  align="right" href="javascript:;" style="padding:5px 5px 0 0;">{$APP.LBL_EDIT_BUTTON}</a>
</span>

						<!-- Shadow table -->
						<table width="100%" cellpadding="2" cellspacing="0" border="0" class="detailview_wrapper_table">
							<tr>
								<td style="padding: 0;">
									<div align=center>
										{if $CATEGORY eq 'Settings'}
											{include file='SetMenu.tpl'}
										{/if}
										<table width="98%" align="center" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td align="left">
													<form name="DetailView" method="POST" action="index.php" ENCTYPE="multipart/form-data" id="form" style="margin:0px" onsubmit="VtigerJS_DialogBox.block();">
														<input type="hidden" name="module" value="Users" style="margin:0px">
														<input type="hidden" name="record" id="userid" value="{$ID}" style="margin:0px">
														<input type="hidden" name="isDuplicate" value=false style="margin:0px">
														<input type="hidden" name="action" style="margin:0px">
														<input type="hidden" name="changepassword" style="margin:0px">
														{if $CATEGORY neq 'Settings'}
															<input type="hidden" name="modechk" value="prefview" style="margin:0px">
														{/if}
														<input type="hidden" name="old_password" style="margin:0px">
														<input type="hidden" name="new_password" style="margin:0px">
														<input type="hidden" name="return_module" value="Users" style="margin:0px">
														<input type="hidden" name="return_action" value="ListView"  style="margin:0px">
														<input type="hidden" name="return_id" style="margin:0px">
														<input type="hidden" name="forumDisplay" style="margin:0px">
														<input type="hidden" name="hour_format" id="hour_format" value="{$HOUR_FORMAT}" style="margin:0px">
														<input type="hidden" name="start_hour" id="start_hour" value="{$START_HOUR}" style="margin:0px">
														<input type="hidden" name="form_token" value="{$FORM_TOKEN}">
														{if $CATEGORY eq 'Settings'}
															<input type="hidden" name="parenttab" value="{$PARENTTAB}" style="margin:0px">
														{/if}
														<input type="hidden" id="hdtxt_IsAdmin" value="{if $IS_ADMIN eq 'true'}1{else}0{/if}">
														{include file='applicationmessage.tpl'}
														<table width="100%" border="0" cellpadding="0" cellspacing="0">

															<tr>
																<td valign=top align=left>
																	<!-- Heading and Icons -->
																	<table class="slds-table slds-no-row-hover dvtContentSpace">
																		<tr>
																			<td valign="top" style="padding: 0;">
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
																								<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
																									{if $CATEGORY eq 'Settings'}
																										<span class="uiOutputText" style="display: initial;">
																											<b>
																												<a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString} </a>
																												&gt; 
																												<a href="index.php?module=Users&action=index&parenttab=Settings"> {$MOD.LBL_USERS} </a>
																												&gt;"{$USERNAME}" 
																											</b>
																										</span>
																									{else}
																										<span class="uiOutputText" style="display: initial;">
																											<b>{$APP.LBL_MY_PREFERENCES}</b>
																										</span>
																									{/if}
																									<br/>
																									<span class="small"">{$UMOD.LBL_USERDETAIL_INFO} "{$USERNAME}"</span>
																									<span id="vtbusy_info" style="display:none;" valign="bottom">
																										<img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0">
																									</span>
																								</h1>
																							</div>
																						</div>
																						<div class="slds-col slds-no-flex slds-grid slds-align-middle actionsContainer">
																							<div class="slds-grid forceActionsContainer">
																								<input type="button" onclick="gotourl('index.php?module=Utilities&action=integration&_op=getconfig2fa&user_list={$ID}');" value="{'GoTo2FAActivation'|getTranslatedString:'Utilities'}" class="slds-button slds-button--neutral not-selected slds-not-selected uiButton"/>
																								{if $IS_ADMIN eq 'true' && !$mustChangePassword}
																									<input type="button" onclick="gotourl('index.php?module=cbLoginHistory&action=ListView&page=1&user_list={$ID}');" value="{$MOD.LBL_LOGIN_HISTORY_DETAILS}" class="slds-button slds-button--neutral not-selected slds-not-selected uiButton" aria-live="assertive"/>
																									<input type="button" onclick="gotourl('index.php?module=cbAuditTrail&action=ListView&page=1&user_list={$ID}');" value="{$MOD.LBL_VIEW_AUDIT_TRAIL}" class="slds-button slds-button--neutral not-selected slds-not-selected uiButton" aria-live="assertive"/>
																									<input type="button" onclick="VtigerJS_DialogBox.block();window.document.location.href = 'index.php?module=Users&action=UsersAjax&file=CalculatePrivilegeFiles&record={$ID}';" value="{$MOD.LBL_RECALCULATE_BUTTON}" class="slds-button slds-button--neutral not-selected slds-not-selected uiButton" aria-live="assertive"/>
																								{/if}
																								{if $IS_ADMIN eq 'true'}
																									{$DUPLICATE_BUTTON}
																								{/if}
																									{$EDIT_BUTTON}
																								{if $CATEGORY eq 'Settings' && $ID neq 1 && $ID neq $CURRENT_USERID}
																									<input type="button" onclick="deleteUser({$ID});" class="slds-button slds-button--neutral not-selected slds-not-selected uiButton" aria-live="assertive" value="{$UMOD.LBL_DELETE}"/>
																								{/if}
																							</div>
																						</div>
																					</div>
																				</div>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>

															<tr>
																<td valign="top" style="padding: 0;">
																	<!-- User detail blocks -->
																	<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
																		<tr>
																			<td align="left" valign="top">
																				<table class="slds-table slds-no-row-hover slds-table-moz" style="border-collapse:separate; border-spacing: 1rem;">
																					{foreach key=header name=blockforeach item=detail from=$BLOCKS}
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
																																<strong>{$smarty.foreach.blockforeach.iteration}. {$header}</strong>
																															</span>
																														</h2>
																													</div>
																												</header>
																											</div>
																										</article>
																									</div>
																									{/strip}

																									<div class="slds-truncate">
																										<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout small detailview_table">
																											{foreach item=detailInfo from=$detail}
																												<tr>
																													{foreach key=label item=data from=$detailInfo}
																														{assign var=keyid value=$data.ui}
																														{assign var=keyval value=$data.value}
																														{assign var=keytblname value=$data.tablename}
																														{assign var=keyfldname value=$data.fldname}
																														{assign var=keyfldid value=$data.fldid}
																														{assign var=keyoptions value=$data.options}
																														{assign var=keysecid value=$data.secid}
																														{assign var=keyseclink value=$data.link}
																														{assign var=keycursymb value=$data.cursymb}
																														{assign var=keysalut value=$data.salut}
																														{assign var=keycntimage value=$data.cntimage}
																														{assign var=keyadmin value=$data.isadmin}
																															{if $label ne ''}
																																<td class="dvtCellLabel" align=right width=25%>{$label}</td>
																																	{include file="DetailViewUI.tpl"}
																															{else}
																																<td class="dvtCellLabel" align=right>&nbsp;</td>
																																<td class="dvtCellInfo" align=left >&nbsp;</td>
																															{/if}
																													{/foreach}
																												</tr>
																											{/foreach}
																										</table>
																									</div>
																								</td>
																							</tr>
																						{assign var=list_numbering value=$smarty.foreach.blockforeach.iteration}
																					{/foreach}

																					<!-- Home page components -->
																					<tr class="blockStyleCss">
																						<td class="detailViewContainer" valign="top">
																							<div class="forceRelatedListSingleContainer">
																								<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																									<div class="slds-card__header slds-grid">
																										<header class="slds-media slds-media--center slds-has-flexi-truncate">
																											<div class="slds-media__body">
																												<h2>
																													<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small">
																														<strong>{$list_numbering+1}. {$UMOD.LBL_HOME_PAGE_COMP}</strong>
																													</span>
																												</h2>
																											</div>
																											<div class="slds-media__figure" style="float: right;">
																												<div class="extraSmall forceEntityIcon" data-aura-rendered-by="3:1782;a" data-aura-class="forceEntityIcon">
																													<span data-aura-rendered-by="6:1782;a" class="uiImage" data-aura-class="uiImage">
																													<a href="javascript:ShowHidefn('home_comp', 'arrowDownHome', 'arrowUpHome');">
																														<img src="{'showDown.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_EXPAND_COLLAPSE}" title="{$APP.LBL_EXPAND_COLLAPSE}" id="arrowDownHome" width="16" style="display: none;">
																														<img src="{'showUp.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_EXPAND_COLLAPSE}" title="{$APP.LBL_EXPAND_COLLAPSE}" id="arrowUpHome" width="16">
																													</a>
																													</span>
																												</div>
																											</div>
																										</header>
																									</div>
																								</article>
																							</div>
																							<div class="slds-truncate" style="float: none; display: none;" id="home_comp">
																								<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout small detailview_table">
																									{foreach item=homeitems key=values from=$HOMEORDER}
																										<tr>
																												<td class="dvtCellLabel" align="right" width="25%">{$UMOD.$values|@getTranslatedString:'Home'}</td>
																											{if $homeitems neq ''}
																												<td class="dvtCellInfo" align="center" width="5%">
																													<img src="{'prvPrfSelectedTick.gif'|@vtiger_imageurl:$THEME}" alt="{$UMOD.LBL_SHOWN}" title="{$UMOD.LBL_SHOWN}" height="12" width="12">
																												</td>
																												<td class="dvtCellInfo" align="left" width="70%">{$UMOD.LBL_SHOWN}</td>
																											{else}
																												<td class="dvtCellInfo" align="center" width="5%">
																													<img src="{'no.gif'|@vtiger_imageurl:$THEME}" alt="{$UMOD.LBL_HIDDEN}" title="{$UMOD.LBL_HIDDEN}" height="12" width="12">
																												</td>
																												<td class="dvtCellInfo" align="left" width="70%">{$UMOD.LBL_HIDDEN}</td>
																											{/if}
																										</tr>
																									{/foreach}
																								</table>
																							</div>
																						</td>
																					</tr>

																					<!-- Tag Cloud Display -->
																					<tr class="blockStyleCss">
																						<td class="detailViewContainer" valign="top">
																							<div class="forceRelatedListSingleContainer">
																								<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																									<div class="slds-card__header slds-grid">
																										<header class="slds-media slds-media--center slds-has-flexi-truncate">
																											<div class="slds-media__body">
																												<h2>
																													<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small">
																														<strong>{$list_numbering+2}. {$UMOD.LBL_TAGCLOUD_DISPLAY}</strong>
																													</span>
																												</h2>
																											</div>
																											<div class="slds-media__figure" style="float: right;">
																												<div class="extraSmall forceEntityIcon" data-aura-rendered-by="3:1782;a" data-aura-class="forceEntityIcon">
																													<span data-aura-rendered-by="6:1782;a" class="uiImage" data-aura-class="uiImage">
																													<a href="javascript:ShowHidefn('tagcloud_disp', 'arrowDownCloud', 'arrowUpCloud');">
																														<img src="{'showDown.gif'|@vtiger_imageurl:$THEME}" id="arrowDownCloud" alt="{$APP.LBL_EXPAND_COLLAPSE}" title="{$APP.LBL_EXPAND_COLLAPSE}" width="16" style="display: none;">
																														<img src="{'showUp.gif'|@vtiger_imageurl:$THEME}" id="arrowUpCloud" alt="{$APP.LBL_EXPAND_COLLAPSE}" title="{$APP.LBL_EXPAND_COLLAPSE}" width="16">
																													</a>
																													</span>
																												</div>
																											</div>
																										</header>
																									</div>
																								</article>
																							</div>
																							<div class="slds-truncate" style="float: none; display: none;" id="tagcloud_disp">
																								<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout small detailview_table">
																									<tr>
																										<td class="dvtCellLabel" align="right" width="25%">{$UMOD.LBL_TAG_CLOUD}</td>
																										{if $TAGCLOUDVIEW eq 'true'}
																											<td class="dvtCellInfo" align="center" width="5%">
																											<img src="{'prvPrfSelectedTick.gif'|@vtiger_imageurl:$THEME}" alt="{$UMOD.LBL_SHOWN}" title="{$UMOD.LBL_SHOWN}" height="12" width="12">
																											</td>
																											<td class="dvtCellInfo" align="left" width="70%">{$UMOD.LBL_SHOWN}</td>
																										{else}
																											<td class="dvtCellInfo" align="center" width="5%">
																											<img src="{'no.gif'|@vtiger_imageurl:$THEME}" alt="{$UMOD.LBL_HIDDEN}" title="{$UMOD.LBL_HIDDEN}" height="12" width="12">
																											</td>
																											<td class="dvtCellInfo" align="left" width="70%">{$UMOD.LBL_HIDDEN}</td>
																										{/if}
																									</tr>
																									<tr>
																										<td class="dvtCellLabel" align="right" width="25%">{$MOD.LBL_Show}</td>
																										<td class="dvtCellInfo" align="left" colspan=2>{$SHOWTAGAS}</td>
																									</tr>
																								</table>
																							</div>
																						</td>
																					</tr>

																					<!-- My Groups -->
																					<tr class="blockStyleCss">
																						<td class="detailViewContainer" valign="top">
																							<div class="forceRelatedListSingleContainer">
																								<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																									<div class="slds-card__header slds-grid">
																										<header class="slds-media slds-media--center slds-has-flexi-truncate">
																											<div class="slds-media__body">
																												<h2>
																													<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small">
																														<strong>{$list_numbering+3}. {$UMOD.LBL_MY_GROUPS}</strong>
																													</span>
																												</h2>
																											</div>
																											<div class="slds-media__figure" style="float: right;">
																												<div class="extraSmall forceEntityIcon" data-aura-rendered-by="3:1782;a" data-aura-class="forceEntityIcon">
																													<span data-aura-rendered-by="6:1782;a" class="uiImage" data-aura-class="uiImage">
																													{if $GROUP_COUNT > 0}
																														<a href="javascript:fetchGroups_js({$ID});">
																															<img src="{'showDown.gif'|@vtiger_imageurl:$THEME}" id="arrowDownGroups" alt="{$APP.LBL_EXPAND_COLLAPSE}" title="{$APP.LBL_EXPAND_COLLAPSE}" width="16px;" style="display: none;">
																															<img src="{'showUp.gif'|@vtiger_imageurl:$THEME}" id="arrowUpGroups" alt="{$APP.LBL_EXPAND_COLLAPSE}" title="{$APP.LBL_EXPAND_COLLAPSE}" width="16">
																														</a>
																													{else}
																														&nbsp;
																													{/if}
																													</span>
																												</div>
																											</div>
																										</header>
																									</div>
																								</article>
																							</div>
																							<div class="slds-truncate" id="user_group_cont" style="display:none; padding: 0 .5rem;"></div>
																						</td>
																					</tr>
																				</table>
																			</td>
																		</tr>
																	</table>
																	<!-- User detail blocks ends -->
																</td>
															</tr>

															<tr>
																<td colspan=2 class="small">
																	<div align="right">
																		<a href="#top">{$MOD.LBL_SCROLL}</a>
																	</div>
																</td>
															</tr>

														</table>
													</form>
												</td>
											</tr>
										</table>
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

<br>
<div id="tempdiv" style="display:block;position:absolute;left:350px;top:200px;"></div>
<!-- added for validation -->
<script>
  var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
  var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
  var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
function ShowHidefn(divid, imgidDown, imgidUp)
{ldelim}
	if(document.getElementById(divid).style.display != 'none')
		{ldelim}
		jQuery("#"+divid).fadeOut();
		jQuery("#"+imgidDown).hide();
		jQuery("#"+imgidUp).show();
		{rdelim}
	else
		{ldelim}
		jQuery("#"+divid).fadeIn();
		jQuery("#"+imgidDown).show();
		jQuery("#"+imgidUp).hide();
		{rdelim}
{rdelim}
{literal}
function fetchGroups_js(id)
{
	if(document.getElementById('user_group_cont').style.display != 'none')
		{
		jQuery('#user_group_cont').fadeOut();
		jQuery('#arrowDownGroups').hide();
		jQuery('#arrowUpGroups').show();
		}
	else
		{
		fetchUserGroups(id);
		jQuery('#arrowDownGroups').show();
		jQuery('#arrowUpGroups').hide();
		}
}
function fetchUserGroups(id)
{
		document.getElementById("status").style.display="inline";
		jQuery.ajax({
				method:"POST",
				url:'index.php?module=Users&action=UsersAjax&file=UserGroups&ajax=true&record='+id
		}).done(function(response) {
					document.getElementById("status").style.display="none";
					document.getElementById("user_group_cont").innerHTML= response;
					jQuery('#user_group_cont').fadeIn();
			}
		);
}

function deleteUser(userid)
{
		document.getElementById("status").style.display="inline";
		jQuery.ajax({
				method:"POST",
				url:'index.php?action=UsersAjax&file=UserDeleteStep1&return_action=ListView&return_module=Users&module=Users&parenttab=Settings&record='+userid
		}).done(function(response) {
				document.getElementById("status").style.display="none";
				document.getElementById("tempdiv").innerHTML= response;
			}
		);
}
function transferUser(del_userid)
{
	document.getElementById("status").style.display="inline";
	document.getElementById("DeleteLay").style.display="none";
	var trans_userid=document.getElementById('transfer_user_id').options[document.getElementById('transfer_user_id').options.selectedIndex].value;
	window.document.location.href = 'index.php?module=Users&action=DeleteUser&ajax_delete=false&delete_user_id='+del_userid+'&transfer_user_id='+trans_userid;
}
{/literal}
</script>

