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
<tbody><tr>
        <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
<br>
	<div align=center>
	<form action="index.php" method="post" name="company" onsubmit="VtigerJS_DialogBox.block();">
    	<input type="hidden" name="module" value="Settings">
    	<input type="hidden" name="parenttab" value="Settings">
	<input type="hidden" name="action">
			{include file="SetMenu.tpl"}	
				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{'company.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" width="48" height="48" border=0 title="{$MOD.LBL_USERS}"></td>
					<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > {$MOD.LBL_COMPANY_DETAILS} </b></td>
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_COMPANY_DESC} </td>
				</tr>
				</table>
				
				<br>
					<form action="index.php" method="post" name="company" onsubmit="VtigerJS_DialogBox.block();">
						<input type="hidden" name="module" value="Settings">
						<input type="hidden" name="parenttab" value="Settings">
						<input type="hidden" name="action">
							{include file="SetMenu.tpl"}
								<!-- DISPLAY Company Details Settings-->

									<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz" style="background-color: #f7f9fb;">
										<tr class="slds-text-title--caps">
											<td style="padding: 0;">
												<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilSettings" style="height: 70px;">
													<div class="slds-grid primaryFieldRow" style="transform: translate3d(0, -8.65823px, 0);">
														<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
															<!-- Image -->
															<div class="slds-media slds-no-space" style="transform: scale3d(0.864715, 0.864715, 1) translate3d(4.32911px, 2.16456px, 0);">
																<div class="slds-media__figure slds-icon forceEntityIcon">
																	<span class="photoContainer forceSocialPhoto">
																		<div class="small roundedSquare forceEntityIcon">
																			<span class="uiImage">
																				<img src="{'company.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" title="{$MOD.LBL_USERS}" />
																			</span>
																		</div>
																	</span>
																</div>
															</div>
															<!-- Title and help text -->
															<div class="slds-media__body">
																<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
																	<span class="uiOutputText" style="width: 100%;">
																		<b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > {$MOD.LBL_COMPANY_DETAILS} </b>
																	</span>
																	<span class="small">{$MOD.LBL_COMPANY_DESC}</span>
																</h1>
															</div>
														</div>
													</div>
												</div>
											</td>
										</tr>
									</table>

									<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
										<tr>
											<td class="big">

												<div class="forceRelatedListSingleContainer">
													<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
														<div class="slds-card__header slds-grid">
															<header class="slds-media slds-media--center slds-has-flexi-truncate">
																<div class="slds-media__body">
																	<h2>
																		<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																			<strong>{$MOD.LBL_COMPANY_DETAILS} </strong>
																		</span>
																	</h2>
																</div>
															</header>
															<div class="slds-no-flex">
																<input class="slds-button--small slds-button slds-button--brand" title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" onclick="this.form.action.value='EditCompanyDetails'" type="submit" name="Edit" value="{$APP.LBL_EDIT_BUTTON_LABEL}">
															</div>
														</div>
													</article>
												</div>

												<div class="slds-truncate">
													<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
														<tr>
															<td class="small" valign=top >

																<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table">
																	<tr>
																		<td width="20%" class="small dvtCellLabel"><strong>{$MOD.LBL_ORGANIZATION_NAME}</strong></td>
																		<td width="80%" class="small dvtCellInfo"><strong>{$ORGANIZATIONNAME}</strong></td>
																	</tr>
																	<tr>
																		<td class="small dvtCellLabel"><strong>{$MOD.LBL_ORGANIZATION_LOGO}</strong></td>
																		<td class="small dvtCellInfo" style="background-image: url({$ORGANIZATIONLOGOPATH}/{$ORGANIZATIONLOGONAME}); background-position: left; background-repeat: no-repeat;" height="60" border="0"></td>
																	</tr>
																	<tr>
																		<td class="small dvtCellLabel"><strong>{$MOD.LBL_FRONT_LOGO}</strong></td>
																		<td class="small dvtCellInfo" style="background-image: url({$FRONTLOGOPATH}/{$FRONTLOGONAME}); background-position: left; background-repeat: no-repeat;" height="60" border="0"></td>
																	</tr>
																	<tr>
																		<td class="small dvtCellLabel"><strong>{$MOD.LBL_FAVICON_LOGO}</strong></td>
																		<td class="small dvtCellInfo" style="background-image: url({$FAVICONLOGOPATH}/{$FAVICONLOGONAME}); background-position: left; background-repeat: no-repeat;" height="60" border="0"></td>
																	</tr>
																	<tr>
																		<td class="small dvtCellLabel"><strong>{$MOD.LBL_ORGANIZATION_ADDRESS}</strong></td>
																		<td class="small dvtCellInfo">{$ORGANIZATIONADDRESS}</td>
																	</tr>
																	<tr>
																		<td class="small dvtCellLabel"><strong>{$MOD.LBL_ORGANIZATION_CITY}</strong></td>
																		<td class="small dvtCellInfo">{$ORGANIZATIONCITY}</td>
																	</tr>
																	<tr>
																		<td class="small dvtCellLabel"><strong>{$MOD.LBL_ORGANIZATION_STATE}</strong></td>
																		<td class="small dvtCellInfo">{$ORGANIZATIONSTATE}</td>
																	</tr>
																	<tr>
																		<td class="small dvtCellLabel"><strong>{$MOD.LBL_ORGANIZATION_CODE}</strong></td>
																		<td class="small dvtCellInfo">{$ORGANIZATIONCODE}</td>
																	</tr>
																	<tr>
																		<td class="small dvtCellLabel"><strong>{$MOD.LBL_ORGANIZATION_COUNTRY}</strong></td>
																		<td class="small dvtCellInfo">{$ORGANIZATIONCOUNTRY}</td>
																	</tr>
																	<tr>
																		<td class="small dvtCellLabel"><strong>{$MOD.LBL_ORGANIZATION_PHONE}</strong></td>
																		<td class="small dvtCellInfo">{$ORGANIZATIONPHONE}</td>
																	</tr>
																	<tr>
																		<td class="small dvtCellLabel"><strong>{$MOD.LBL_ORGANIZATION_FAX}</strong></td>
																		<td class="small dvtCellInfo">{$ORGANIZATIONFAX}</td>
																	</tr>
																	<tr>
																		<td class="small dvtCellLabel"><strong>{$MOD.LBL_ORGANIZATION_WEBSITE}</strong></td>
																		<td class="small dvtCellInfo">{$ORGANIZATIONWEBSITE}</td>
																	</tr>
																</table>

															</td>
														</tr>
													</table>
												</div>

											</td>
										</tr>
									</table>

					</form>

					</td></tr></table><!-- close tables from setMenu -->
					</td></tr></table><!-- close tables from setMenu -->

				</div>
			</td>
		</tr>
	</tbody>
</table>
