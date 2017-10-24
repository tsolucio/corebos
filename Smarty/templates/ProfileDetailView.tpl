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
{literal}
<style>
	.showTable{
		display:inline-table;
	}
	.hideTable{
		display:none;
	}
</style>
{/literal}
<script type="text/javascript" src="include/js/smoothscroll.js"></script>
<script type="text/javascript">
	{literal}
	function UpdateProfile()
	{
		var prof_name = document.getElementById('profile_name').value;
		var prof_desc = document.getElementById('description').value;
		if(prof_name == '')
		{
			document.getElementById('profile_name').focus();
			{/literal}
			alert("{$APP.PROFILENAME_CANNOT_BE_EMPTY}");
			{literal}
		}
		else
		{
			{/literal}
			var urlstring = "module=Users&action=UsersAjax&file=RenameProfile&profileid="+{$PROFILEID}+"&profilename="+encodeURIComponent(prof_name)+"&description="+encodeURIComponent(prof_desc);
			{literal}
			jQuery.ajax({
							method: 'POST',
							url: 'index.php?'+urlstring,
						}).done(function (response)
						{
							document.getElementById('renameProfile').style.display="none";
							window.location.reload();
							{/literal}
							alert("{$APP.PROFILE_DETAILS_UPDATED}");
							{literal}
						}
				);
		}
	}
	{/literal}
</script>

<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td valign="top" width="100%">
				<div align=center>
					<br>
						{include file='SetMenu.tpl'}
							<!-- DISPLAY Profile Name Details-->
								<form  method="post" name="new" id="form" onsubmit="VtigerJS_DialogBox.block();">
									<input type="hidden" name="module" value="Settings">
									<input type="hidden" name="action" value="profilePrivileges">
									<input type="hidden" name="parenttab" value="Settings">
									<input type="hidden" name="return_action" value="profilePrivileges">
									<input type="hidden" name="mode" value="edit">
									<input type="hidden" name="profileid" value="{$PROFILEID}">

									<!-- Profile Detail View Header -->
										<table class="slds-table slds-no-row-hover slds-table--cell-buffer" style="background-color: #f7f9fb;">
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
																					<img src="{'ico-profile.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_PROFILES}" title="{$MOD.LBL_PROFILES}"/>
																				</span>
																			</div>
																		</span>
																	</div>
																</div>
																<div class="slds-media__body">
																	<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
																		<span class="uiOutputText">
																			<b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > <a href="index.php?module=Settings&action=ListProfiles&parenttab=Settings">{$CMOD.LBL_PROFILE_PRIVILEGES}</a> &gt; {$CMOD.LBL_VIEWING} &quot;{$PROFILE_NAME}&quot;</b>
																		</span>
																		<span class="small">
																			{$CMOD.LBL_PROFILE_MESG} &quot;{$PROFILE_NAME}&quot;
																		</span>
																	</h1>
																</div>
															</div>
														</div>
													</div>
												</td>
											</tr>
										</table>

										<!-- Privileges Content -->
										<table border="0" cellpadding="5" cellspacing="0" width="100%">
											<tbody>
												<tr>
													<td>

														<!-- Module name heading -->
														<div class="forceRelatedListSingleContainer">
															<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																<div class="slds-card__header slds-grid">
																	<!-- Image and Title -->
																	<header class="slds-media slds-media--center slds-has-flexi-truncate">
																		<div class="slds-media__figure">
																			<div class="extraSmall forceEntityIcon">
																				<span class="uiImage subheader-image">
																					<img src="{'prvPrfHdrArrow.gif'|@vtiger_imageurl:$THEME}">
																				</span>
																			</div>
																		</div>
																		<div class="slds-media__body">
																			<h2>
																				<span class="slds-text-title--caps slds-truncate actionLabel prvPrfBigText">
																					<b> {$CMOD.LBL_DEFINE_PRIV_FOR} &lt;{$PROFILE_NAME}&gt; </b>
																				</span>
																			</h2>
																		</div>
																	</header>
																	<!-- Rename & Edit buttons -->
																	<div class="slds-no-flex">
																		<div class="actionsContainer">
																			<input type="button" value="{$APP.LBL_RENAMEPROFILE_BUTTON_LABEL}" title="{$APP.LBL_RENAMEPROFILE_BUTTON_LABEL}" class="slds-button--small slds-button slds-button--info" name="rename_profile"  onClick = "show('renameProfile');">
																			&nbsp;
																			<input type="submit" value="{$APP.LBL_EDIT_BUTTON_LABEL}" title="{$APP.LBL_EDIT_BUTTON_LABEL}" class="slds-button--small slds-button slds-button--brand" name="edit" >
																		</div>
																	</div>
																</div>
																<!-- Help text -->
																<div class="slds-card__body slds-card__body--inner">
																	<div class="commentData">
																		<font class="small">{$CMOD.LBL_USE_OPTION_TO_SET_PRIV}</font>
																	</div>
																</div>
															</article>
														</div>

														<!-- RenameProfile Div start -->
														<div class="layerPopup"  style="left:350px;width:500px;top:300px;display:none;z-index: 9999;" id="renameProfile">
															<!-- Rename Profile Title -->
															<table class="slds-table slds-no-row-hover" style="border-bottom: 1px solid #DEDEDE;">
																<th scope="col" style="cursor:move;">
																	<div class="slds-tabletruncate moduleName" id="renameUI">
																		<b>{$APP.LBL_RENAME_PROFILE}</b>
																	</div>
																</th>
																<th scope="col" style="padding: .5rem 0;">
																	<div class="slds-truncate">
																		<a href="javascript:fnhide('renameProfile');"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" align="middle"></a>
																	</div>
																</th>
															</table>

															<!-- Rename Profile content -->
															<table class="slds-table slds-no-row-hover">
																<tr>
																	<td class="dvtCellLabel" align="right" width="25%" nowrap><b>{$APP.LBL_PROFILE_NAME} :</b></td>
																	<td class="dvtCellInfo" align="left" width="75%"><input id = "profile_name" name="profile_name" class="slds-input" value="{$PROFILE_NAME}" style="width: 100%;" type="text"></td>
																</tr>
																<tr>
																	<td class="dvtCellLabel" align="right" width="25%" nowrap><b>{$APP.LBL_DESCRIPTION} :</b></td>
																	<td class="dvtCellInfo" align="left" width="75%"><textarea name="description" id = "description" class="slds-textarea">{$PROFILE_DESCRIPTION} </textarea></td>
																</tr>
															</table>

															<!-- Rename Profile Buttons -->
															<table border="0" cellpadding="5" cellspacing="0" width="100%" style="background-color: #f4f6f9;">
																<tr>
																	<td align="center" style="padding: 5px;">
																		<input name="save" value="{$APP.LBL_UPDATE}" class="slds-button--small slds-button slds-button_success" onclick="UpdateProfile();" type="button" title="{$APP.LBL_UPDATE}">&nbsp;&nbsp;
																		<input name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="slds-button--small slds-button slds-button--destructive" onclick="fnhide('renameProfile');" type="button" title="{$APP.LBL_CANCEL_BUTTON_LABEL}">
																	</td>
																</tr>
															</table>
														</div>
														<!-- RenameProfile Div end -->

														<!-- Global Privileges Section -->
														<div class="slds-truncate">
															<table border="0" cellpadding="5" cellspacing="0" width="100%">
																<tbody>
																	<tr>
																		<td>

																			<!-- Global Privileges -->
																			<div class="slds-table--scoped">
																				<ul class="slds-tabs--scoped__nav" role="tablist" style="margin-bottom: 0;">
																					<li class="slds-tabs--scoped__item active" role="presentation">
																						<a class="slds-tabs--scoped__link " role="tab" tabindex="0" aria-selected="true" aria-controls="globalPrivileges" id="globalPrivileges" style="cursor: default;">
																							{$CMOD.LBL_SUPER_USER_PRIV}
																						</a>
																					</li>
																				</ul>

																				<div id="globalPrivileges" role="tabpanel" aria-labelledby="globalPrivileges" class="slds-tabs--scoped__content slds-truncate">
																					<table class="slds-table slds-no-row-hover detailview_table">
																						<tbody>
																							<tr id="gva">
																								<td class="dvtCellLabel" width="10%" valign="top">{$GLOBAL_PRIV.0}</td>
																								<td class="dvtCellInfo">
																									<b>{$CMOD.LBL_VIEW_ALL}</b> 
																									<br/>
																									{$CMOD.LBL_ALLOW} "{$PROFILE_NAME}" {$CMOD.LBL_MESG_VIEW}
																								</td>
																							</tr>
																							<tr>
																								<td class="dvtCellLabel" width="15%" valign="top">{$GLOBAL_PRIV.1}</td>
																								<td class="dvtCellInfo">
																									<b>{$CMOD.LBL_EDIT_ALL}</b>
																									<br>
																									{$CMOD.LBL_ALLOW} "{$PROFILE_NAME}" {$CMOD.LBL_MESG_EDIT}
																								</td>
																							</tr>
																						</tbody>
																					</table>
																				</div>
																			</div>

																			<br/>

																			<!-- Privileges for each module tab -->
																			<div class="slds-table--scoped">
																				<ul class="slds-tabs--scoped__nav" role="tablist" style="margin-bottom: 0;">
																					<li class="slds-tabs--scoped__item active" role="presentation">
																						<a class="slds-tabs--scoped__link " role="tab" tabindex="0" aria-selected="true" aria-controls="eachModulePrivilege" id="eachModulePrivilege" style="cursor: default;">
																							{$CMOD.LBL_SET_PRIV_FOR_EACH_MODULE}
																						</a>
																					</li>
																				</ul>

																				<!-- Edit Permission Content -->
																				<div id="eachModulePrivilege" role="tabpanel" aria-labelledby="eachModulePrivilege" class="slds-tabs--scoped__content slds-truncate">
																					<table class="slds-table slds-no-row-hover detailview_table privilege-table">
																						<!-- Edit Permission Title -->
																						<tr id="gva">
																							<td colspan="7" class="dvtCellLabel text-left">
																								<strong>{$CMOD.LBL_EDIT_PERMISSIONS}</strong>
																							</td>
																						</tr>
																						<!-- Edit Permission Headers -->
																						<tr id="gva" class="slds-text-title--caps">
																							<td colspan="2" class="small dvtCellInfo">{$CMOD.LBL_TAB_MESG_OPTION}</td>
																							<td class="small dvtCellInfo" align="center">{$CMOD.LBL_CREATE}</td>
																							<td class="small dvtCellInfo" align="center">{$CMOD.Edit}</td>
																							<td class="small dvtCellInfo" align="center">{$CMOD.LBL_VIEW}</td>
																							<td class="small dvtCellInfo" align="center">{$CMOD.LBL_DELETE}</td>
																							<td class="small dvtCellInfo" nowrap="nowrap"> {$CMOD.LBL_FIELDS_AND_TOOLS_SETTINGS} </td>
																						</tr>

																						<!-- module loops-->
																						{foreach key=tabid item=elements from=$TAB_PRIV}
																							<tr id="module-loops">
																								{assign var=modulename value=$TAB_PRIV[$tabid][0]}
																								{assign var="MODULELABEL" value=$modulename|@getTranslatedString:$modulename}
																								<td class="smal dvtCellLabel" width="3%">
																									<div>{$TAB_PRIV[$tabid][1]}</div>
																								</td>
																								<td class="small dvtCellLabel text-left" width="40%"><p>{$MODULELABEL}</p></td>
																								<td class="small dvtCellInfo" width="10%">
																									<div align="center">{if !empty($STANDARD_PRIV[$tabid][4])}{$STANDARD_PRIV[$tabid][4]}{/if}</div>
																								</td>
																								<td class="small dvtCellInfo" width="10%">
																									<div align="center">{if !empty($STANDARD_PRIV[$tabid][1])}{$STANDARD_PRIV[$tabid][1]}{/if}</div>
																								</td>
																								<td class="small dvtCellInfo" width="10%">
																									<div align="center">{if !empty($STANDARD_PRIV[$tabid][3])}{$STANDARD_PRIV[$tabid][3]}{/if}</div>
																								</td>
																								<td class="small dvtCellInfo" width="10%">
																									<div align="center">{if !empty($STANDARD_PRIV[$tabid][2])}{$STANDARD_PRIV[$tabid][2]}{/if}</div>
																								</td>
																								<td class="small dvtCellInfo" width="17%">
																									<div align="center">
																										{if !empty($FIELD_PRIVILEGES[$tabid])}
																											<img src="{'showDown.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_EXPAND_COLLAPSE}" title="{$APP.LBL_EXPAND_COLLAPSE}" onclick="fnToggleVIew('{$modulename}_view')" style="height: 20px;cursor: pointer;">
																										{/if}
																									</div>
																								</td>
																							</tr>
																							<tr class="hideTable" id="{$modulename}_view" className="hideTable">
																								<td colspan="7" class="small settingsSelectedUI">

																									<!-- Edit Permissions Table -->
																									<table class="slds-table slds-no-row-hover detailview_table">
																										<tbody>

																											<!-- Fields to be shown Section-->
																											{if !empty($FIELD_PRIVILEGES[$tabid])}
																												<tr class="slds-line-height--reset">
																													<td colspan="7" class="small dvtCellLabel text-left" valign="top">
																														<b>
																														{if $modulename eq 'Calendar'}
																															{$CMOD.LBL_FIELDS_TO_BE_SHOWN} ({$APP.Tasks})
																														{else}
																															{$CMOD.LBL_FIELDS_TO_BE_SHOWN}
																														{/if}
																														</b>
																													</td>
																												</tr>
																												{foreach item=row_values from=$FIELD_PRIVILEGES[$tabid]}
																													<tr class="slds-line-height--reset">
																														{foreach item=element from=$row_values}
																															<td class="dvtCellInfo" align="right" valign="top">{$element.1}</td>
																															<td class="dvtCellLabel text-left">{$element.0}</td>
																														{/foreach}
																													</tr>
																												{/foreach}
																											{/if}

																											<!-- Fields to be shown Section for Calendar Module -->
																											{if $modulename eq 'Calendar'}
																												<tr class="slds-line-height--reset">
																													<td class="small dvtCellInfo" colspan="7" valign="top">{$CMOD.LBL_FIELDS_TO_BE_SHOWN} ({$APP.Events})</td>
																												</tr>
																												{foreach item=row_values from=$FIELD_PRIVILEGES[16]}
																													<tr class="slds-line-height--reset">
																														{foreach item=element from=$row_values}
																															<td class="dvtCellInfo" align="right" valign="top">{$element.1}</td>
																															<td class="dvtCellLabel text-left">{$element.0}</td>
																														{/foreach}
																													</tr>
																												{/foreach}
																											{/if}

																											<!-- Tools to be shown Section -->
																											{if !empty($UTILITIES_PRIV[$tabid])}
																												<tr class="slds-line-height--reset">
																													<td colspan="7" class="small dvtCellLabel text-left" valign="top"><b>{$CMOD.LBL_TOOLS_TO_BE_SHOWN}</b></td>
																												</tr>
																												{foreach item=util_value from=$UTILITIES_PRIV[$tabid]}
																													<tr class="slds-line-height--reset">
																														{foreach item=util_elements from=$util_value}
																															<td class="dvtCellInfo" align="right" valign="top">{$util_elements.1}</td>
																															<td class="dvtCellLabel text-left">{$APP[$util_elements.0]}</td>
																														{/foreach}
																													</tr>
																												{/foreach}
																											{/if}
																										</tbody>
																									</table>

																								</td>
																							</tr>
																						{/foreach}
																					</table>

																					<!-- Legend -->
																					<table class="slds-table slds-no-row-hover detailview_table">
																						<tr class="slds-line-height--reset">
																							<td class="dvtCellLabel text-left"><font color="red" size=5>*</font>&nbsp;{$CMOD.LBL_MANDATORY_MSG}</td>
																						</tr>
																						<tr class="slds-line-height--reset">
																							<td class="dvtCellLabel text-left"><font color="blue" size=5>*</font>&nbsp;{$CMOD.LBL_DISABLE_FIELD_MSG}</td>
																						</tr>
																						<tr class="slds-line-height--reset">
																							<td class="dvtCellLabel text-left"><img src="{'locked.png'|@vtiger_imageurl:$THEME}" style="height:18px;vertical-align: bottom;" />&nbsp;{$CMOD.LBL_READ_ONLY_ACCESS_MSG}</td>
																						</tr>
																						<tr class="slds-line-height--reset">
																							<td class="dvtCellLabel text-left"><img src="{'unlocked.png'|@vtiger_imageurl:$THEME}" style="height:18px;vertical-align: bottom;" />&nbsp;{$CMOD.LBL_READ_WRITE_ACCESS_MSG}</td>
																						</tr>
																					</table>
																				</div>
																			</div>

																		</td>
																	</tr>
																</tbody>
															</table>
														</div>

													</td>
												</tr>
											</tbody>
										</table>

										<!-- Edit Profile Details Buttons -->
										<table class="slds-table slds-no-row-hover">
											<tr class="slds-line-height--reset">
												<td class="dvtCellLabel">
													<input type="submit" value="{$APP.LBL_EDIT_BUTTON_LABEL}" title="{$APP.LBL_EDIT_BUTTON_LABEL}" class="slds-button--brand slds-button slds-button--small" name="edit">
												</td>
											</tr>
										</table>

										<!-- Scroll to top button -->
										<table border="0" cellpadding="5" cellspacing="0" width="100%">
											<tr><td class="small" align="right" nowrap="nowrap"><a href="#top">{$MOD.LBL_SCROLL}</a></td></tr>
										</table>

								</form>
					</td></tr></table><!-- close table from setMenu -->
					</td></tr></table><!-- close table from setMenu -->
				</div>
			</td>
		</tr>
	</tbody>
</table>

<script type="text/javascript">
	{literal}
	function fnToggleVIew(obj){
		obj = "#"+obj;
		if(jQuery(obj).hasClass('hideTable')) {
			jQuery(obj).removeClass('hideTable');
		} else {
			jQuery(obj).addClass('hideTable');
		}
	}
	{/literal}
	{literal}
		//for move RenameProfile
		jQuery("#renameProfile").draggable({ handle: "#renameUI" });
	{/literal}
</script>

