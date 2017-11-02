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
							<!-- DISPLAY Edit Email Template-->
							<form method="post" action="index.php" name="etemplatedetailview" onsubmit="VtigerJS_DialogBox.block();">
								<input type="hidden" name="action" value="editemailtemplate">
								<input type="hidden" name="module" value="Settings">
								<input type="hidden" name="templatename" value="{$TEMPLATENAME}">
								<input type="hidden" name="templateid" value="{$TEMPLATEID}">
								<input type="hidden" name="foldername" value="{$FOLDERNAME}">

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
																				<img src="{'ViewTemplate.gif'|@vtiger_imageurl:$THEME}" />
																			</span>
																		</div>
																	</span>
																</div>
															</div>
															<!-- Title and help text -->
															<div class="slds-media__body">
																<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
																	<span class="uiOutputText" style="width: 100%;">
																		<b>
																			<a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > 
																			<a href="index.php?module=Settings&action=listemailtemplates&parenttab=Settings">{$UMOD.LBL_EMAIL_TEMPLATES}</a> 
																			&gt; {$MOD.LBL_VIEWING} &quot;{$TEMPLATENAME}&quot;
																		</b>
																	</span>
																	<span class="small">{$MOD.LBL_EMAIL_TEMPLATE_DESC}</span>
																</h1>
															</div>
														</div>
													</div>
												</div>
											</td>
										</tr>
									</table>

									<table border=0 cellspacing=0 cellpadding=10 width=100% >
										<tr>
											<td>

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
																						<strong>{$UMOD.LBL_PROPERTIES} &quot;{$TEMPLATENAME}&quot; </strong>
																					</span>
																				</h2>
																			</div>
																		</header>
																		<div class="slds-no-flex">
																			<input class="slds-button--small slds-button slds-button--brand" type="submit" name="Button" value="{$APP.LBL_EDIT_BUTTON_LABEL}" onclick="this.form.action.value='editemailtemplate'; this.form.parenttab.value='Settings'">
																		</div>
																	</div>
																</article>
															</div>

															<div class="slds-truncate">
																<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table">
																	<tr>
																		<td width=15% class="small dvtCellLabel"><strong>{$UMOD.LBL_NAME}</strong></td>
																		<td width=80% class="small dvtCellInfo"><strong>{$TEMPLATENAME}</strong></td>
																	</tr>
																	<tr>
																		<td valign=top class="small dvtCellLabel"><strong>{$UMOD.LBL_DESCRIPTION}</strong></td>
																		<td class="dvtCellInfo small" valign=top>&nbsp;{$DESCRIPTION}</td>
																	</tr>
																	<tr>
																		<td valign=top class="small dvtCellLabel"><strong>{$UMOD.LBL_FOLDER}</strong></td>
																		<td class="dvtCellInfo small" valign=top>{$FOLDERNAME}</td>
																	</tr>
																</table>
															</div>

														</td>
													</tr>
												</table>

												<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
													<tr>
														<td valign="top" style="padding-left: 0; padding-right: 0;">

															<div class="forceRelatedListSingleContainer">
																<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																	<div class="slds-card__header slds-grid">
																		<header class="slds-media slds-media--center slds-has-flexi-truncate">
																			<div class="slds-media__body">
																				<h2>
																					<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																						<strong>{$UMOD.LBL_EMAIL_TEMPLATE}</strong>
																					</span>
																				</h2>
																			</div>
																		</header>
																	</div>
																</article>
															</div>

															<div class="slds-truncate">
																<!-- <table style="width: 700px;"> -->
																<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table">
																	<tr>
																		<td width="15%" valign="top" class="dvtCellLabel small">{$UMOD.SendEmailFrom}</td>
																		<td width="80%" class="dvtCellInfo small">{$EMAILFROM}</td>
																	</tr>
																		<tr>
																		<td valign="top" class="dvtCellLabel small">{$UMOD.LBL_SUBJECT}</td>
																		<td class="dvtCellInfo small">{$SUBJECT}</td>
																	</tr>
																	<tr>
																		<td valign="top" class="dvtCellLabel small">{$UMOD.LBL_MESSAGE}</td>
																		<td class="dvtCellInfo small email-body">{$BODY}</td>
																	</tr>
																</table>
															</div>

														</td>
													</tr>
												</table>

												<table border=0 cellspacing=0 cellpadding=5 width=100% >
													<tr>
														<td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td>
													</tr>
												</table>

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