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
<script type="text/javascript" src="modules/CronTasks/CronTasks.js"></script>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td valign="top" width="100%">
				<div align=center>
				<br>
					{include file='SetMenu.tpl'}
							<!-- DISPLAY Cron / Scheduler-->
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
																					<img src="{'Cron.png'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_SCHEDULER}" title="{$MOD.LBL_SCHEDULER}"/>
																				</span>
																			</div>
																		</span>
																	</div>
																</div>
																<!-- Title and help text -->
																<div class="slds-media__body">
																	<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
																		<span class="uiOutputText" style="width: 100%;">
																			<b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > {$MOD.LBL_SCHEDULER} </b>
																		</span>
																		<span class="small">{$MOD.LBL_SCHEDULER}</span>
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

													<table width="100%" border="0" cellpadding="5" cellspacing="0" class="listTableTopButtons">
														<tr>
															<td class="big">
																<div class="forceRelatedListSingleContainer">
																	<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																		<div class="slds-card__header slds-grid">
																			<header class="slds-media slds-media--center slds-has-flexi-truncate">
																				<div class="slds-media__body">
																					<h2>
																						<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																							<strong>{$MOD.LBL_SCHEDULER}</strong>
																						</span>
																					</h2>
																				</div>
																			</header>
																		</div>
																	</article>
																</div>

																<div class="slds-truncate" id="notifycontents">
																{include file='modules/CronTasks/CronContents.tpl'}
																</div>

															</td>
														</tr>
													</table>

													<table border=0 cellspacing=0 cellpadding=5 width=100% >
														<tr><td class="small" nowrap align=right><a href="#top">{$APP.LBL_SCROLL}</a></td></tr>
													</table>

												</td>
											</tr>
										</table>

							</td></tr></table><!-- close tables from setMenu -->
							</td></tr></table><!-- close tables from setMenu -->

				</div>
			</td>
		</tr>
	</tbody>
</table>


<div id="editdiv" style="display:none;position:absolute;width:450px;"></div>