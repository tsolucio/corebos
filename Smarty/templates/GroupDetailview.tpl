{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
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
						<!-- DISPLAY Group Name Details-->
						<form action="index.php" method="post" name="new" id="form" onsubmit="VtigerJS_DialogBox.block();">
							<input type="hidden" name="module" value="Settings">
							<input type="hidden" name="action" value="createnewgroup">
							<input type="hidden" name="groupId" value="{$GROUPID}">
							<input type="hidden" name="mode" value="edit">
							<input type="hidden" name="parenttab" value="Settings">

							<!-- Group Detail View Header -->
							<table class="slds-table slds-no-row-hover slds-table--cell-buffer" style="background-color: #f7f9fb;">
								<tr class="slds-text-title--caps">
									<td style="padding: 0;">
										<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilSettings" style="height: 70px;">
											<div class="slds-grid primaryFieldRow">
												<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
													<!-- LDS icon -->
													<div class="slds-media slds-no-space">
														<div class="slds-media__figure slds-icon forceEntityIcon">
															<span class="photoContainer forceSocialPhoto">
																<div class="small roundedSquare forceEntityIcon">
																	<span class="uiImage">
																	<img src="{'ico-groups.gif'|@vtiger_imageurl:$THEME}"/>
																	</span>
																</div>
															</span>
														</div>
													</div>
													<!-- Header title and help text -->
													<div class="slds-media__body">
														<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
															<span class="uiOutputText">
																<b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > <a href="index.php?module=Settings&action=listgroups&parenttab=Settings">{$CMOD.LBL_GROUPS}</a> &gt; {$CMOD.LBL_VIEWING} &quot;{$GROUPINFO.0.groupname}&quot; </b>
															</span>
															<span class="small">
																{$CMOD.LBL_VIEWING} {$CMOD.LBL_PROPERTIES} &quot;{$GROUPINFO.0.groupname}`&quot; {$CMOD.LBL_GROUP_NAME}
															</span>
														</h1>
													</div>
												</div>
											</div>
										</div>
									</td>
								</tr>
							</table>

							<!-- Group Detail View Content -->
							<table border=0 cellspacing=0 cellpadding=10 width=100% >
								<tr>
									<td valign=top>

										<!-- Properties and Edit button -->
										<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
											<tr>
												<td class="big">
													<div class="forceRelatedListSingleContainer">
														<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
															<div class="slds-card__header slds-grid">
																<header class="slds-media slds-media--center slds-has-flexi-truncate">
																	<div class="slds-media__body">
																		<h2>
																			<span class="slds-text-title--caps slds-truncate actionLabel prvPrfBigText">
																				<strong>{$CMOD.LBL_PROPERTIES} &quot;{$GROUPINFO.0.groupname}&quot; </strong>
																			</span>
																		</h2>
																	</div>
																</header>
																<div class="slds-no-flex">
																	<div class="actionsContainer">
																		<input value="   {$APP.LBL_EDIT_BUTTON_LABEL} " title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="slds-button slds-button--small slds-button--brand" type="submit" name="Edit">
																	</div>
																</div>
															</div>
														</article>
													</div>

													<!-- Properties Table Content -->
													<div class="slds-truncate">
														<table class="slds-table slds-no-row-hover detailview_table">
															<tr class="small">
																<td width="15%" class="small cellLabel"><strong>{$CMOD.LBL_GROUP_NAME}</strong></td>
																<td width="85%" class="cellText">{$GROUPINFO.0.groupname}</td>
															</tr>
															<tr class="small">
																<td class="small cellLabel"><strong>{$CMOD.LBL_DESCRIPTION}</strong></td>
																<td class="cellText">{$GROUPINFO.0.description}</td>
															</tr>
															<tr class="small">
																<td valign=top class="cellLabel"><strong>{$CMOD.LBL_MEMBER}</strong></td>

																<td class="cellText">
																	<table class="slds-table slds-no-row-hover detailview_table">
																		{foreach key=type item=details from=$GROUPINFO.1}
																			{if $details.0 neq ''}
																				<tr class="small">
																					{if $type == "User"}
																						<td width="50%" class="dvtCellLabel cellBottomDotLine">
																							<div align="left"><strong>{$MOD.LBL_USERS}</strong></div>
																						</td>
																					{/if}

																					{if $type == "Role"}
																						<td width="50%" class="dvtCellLabel cellBottomDotLine">
																							<div align="left"><strong>{$MOD.LBL_ROLES}</strong></div>
																						</td>
																					{/if}

																					{if $type == "Role and Subordinates"}
																						<td width="50%" class="dvtCellLabel cellBottomDotLine">
																							<div align="left"><strong>{$type}</strong></div>
																						</td>
																					{/if}

																					{if $type == "Group"}
																						<td width="50%" class="dvtCellLabel cellBottomDotLine">
																							<div align="left"><strong>{$CMOD.LBL_GROUPS}</strong></div>
																						</td>
																					{/if}
																				</tr>
																				<tr class="small">
																					<td width="50%" class="dvtCellInfo">
																						{foreach item=element from=$details}
																							{if $element.memberaction == "GroupDetailView"}
																								<a href="index.php?module=Settings&action={$element.memberaction}&{$element.actionparameter}={$element.memberid}">{$element.membername}</a>
																								<br />
																							{/if}

																							{if $element.memberaction == "RoleDetailView"}
																								<a href="index.php?module=Settings&action={$element.memberaction}&{$element.actionparameter}={$element.memberid}">{$element.membername}</a>
																								<br />
																							{/if}

																							{if $element.memberaction == "DetailView"}
																								<a href="index.php?module=Users&action={$element.memberaction}&{$element.actionparameter}={$element.memberid}">{$element.membername}</a>
																								<br />
																							{/if}
																						{/foreach}
																					</td>
																				</tr>
																			{/if}
																		{/foreach}
																	</table>
																</td>
															</tr>
														</table>
													</div>

												</td>
											</tr>
										</table>


										<table border=0 cellspacing=0 cellpadding=5 width=100%>
											<tr>
												<td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td>
											</tr>
										</table>

									</td>
								</tr>
							</table>
						</form>

						</td></tr></table>
						</td></tr></table>
				</div>
			</td>
		</tr>
	</tbody>
</table>