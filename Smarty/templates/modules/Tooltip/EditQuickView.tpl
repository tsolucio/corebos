<script type="text/javascript" src="modules/Tooltip/TooltipSettings.js"></script>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td valign="top" width="100%">
				<div align=center>
					<form action="index.php" method="post" name="new" id="form" onsubmit="VtigerJS_DialogBox.block();">
						<input type="hidden" id="fieldid" name="fieldid" value="{$FIELDID}">
						<input type="hidden" name="" value="">

						<!-- Tooltip managemend body content -->
						<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
							<tr>
								<td class="big">
									<!-- Select fields to display as tooltip Header and buttons container -->
									<div class="forceRelatedListSingleContainer">
										<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
											<div class="slds-card__header slds-grid">
												<!-- Header -->
												<header class="slds-media slds-media--center slds-has-flexi-truncate">
													<div class="slds-media__body">
														<h2>
															<span class="slds-text-title--caps slds-truncate actionLabel prvPrfBigText">
																<strong>{$CMOD.LBL_TOOLTIP_HELP_TEXT}</strong>
															</span>
														</h2>
													</div>
												</header>
												<!-- Save & Back buttons content -->
												<div class="slds-no-flex">
													<div class="actionsContainer">
														<input title="save" class="slds-button slds-button--small slds-button_success" type="button" name="save" onClick="doSaveTooltipInfo();" value="{$APP.LBL_SAVE_BUTTON_LABEL}">
														<input title="back" class="slds-button slds-button--small slds-button--destructive" type="button" name="Back" onClick="window.history.back();" value="{$APP.LBL_BACK}">
													</div>
												</div>
											</div>
										</article>
									</div>
									<!-- Tooltips to be displayed container -->
									{foreach key=module item=info from=$FIELD_LISTS}
										<div id="{$module}_fields" style="display:block">
											<table cellspacing=0 cellpadding=5 width=100% class="listTable small">
												<tr>
													<td valign=top width="25%">
														<table border=0 cellspacing=0 cellpadding=5 width=100% class=small>
															{foreach item=elements name=groupfields from=$info}
																<tr>
																	{foreach item=elementinfo name=curvalue from=$elements}
																		<!-- <td class="prvPrfTexture" style="width:20px">&nbsp;</td> -->
																		<!-- Fields Checkboxes -->
																		<td class="dvtCellLabel" width="1%" id="{$smarty.foreach.groupfields.iteration}_{$smarty.foreach.curvalue.iteration}">
																			{$elementinfo.input}
																		</td>
																		<!-- Field Labels -->
																		<td class="dvtCellInfo" width="15%">
																			{$elementinfo.fieldlabel}
																		</td>
																		<!-- nowrap onMouseOver="this.className='prvPrfHoverOn',document.getElementById('{$smarty.foreach.groupfields.iteration}_{$smarty.foreach.curvalue.iteration}').className='prvPrfHoverOn'" onMouseOut="this.className='prvPrfHoverOff',document.getElementById('{$smarty.foreach.groupfields.iteration}_{$smarty.foreach.curvalue.iteration}').className='prvPrfHoverOff'" -->
																	{/foreach}
																</tr>
															{/foreach}
														</table>
													</td>
												</tr>
											</table>
										</div>
									{/foreach}

								</td>
							</tr>
						</table>

					</form>
				</div>
			</td>
		</tr>
	</tbody>
</table>
