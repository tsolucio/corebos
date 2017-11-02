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

<script type="text/javascript">
{literal}
function performScanNow(app_key, scannername) {
	document.getElementById('status').style.display = 'inline';
	jQuery.ajax({
				method: 'POST',
				url: 'index.php?module=Settings&action=SettingsAjax&file=MailScanner' +
					'&mode=scannow&service=MailScanner&app_key=' + encodeURIComponent(app_key)+ '&scannername=' + encodeURIComponent(scannername),
			}).done(function(response) {
				document.getElementById('status').style.display = 'none';
				document.getElementById(scannername).innerHTML = response;
				document.getElementById(scannername).style.display = 'block';
			}
			);
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
						<!-- DISPLAY Mail Converter-->

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
																	<img src="{'mailScanner.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_MAIL_SCANNER}" border=0 title="{$MOD.LBL_MAIL_SCANNER}" />
																</span>
															</div>
														</span>
													</div>
												</div>
												<!-- Title and help text -->
												<div class="slds-media__body">
													<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
														<span class="uiOutputText" style="width: 100%;">
															<b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > {$MOD.LBL_MAIL_SCANNER}</b>
														</span>
														<span class="small">{$MOD.LBL_MAIL_SCANNER_DESCRIPTION}</span>
													</h1>
												</div>
												<div class="slds-no-flex">
													<b>
													{if $CRON_TASK->isDisabled() }{'LBL_DISABLED'|@getTranslatedString:'CronTasks'}{/if}
													{if $CRON_TASK->isRunning() }{'LBL_RUNNING'|@getTranslatedString:'CronTasks'}{/if}
													{if $CRON_TASK->isEnabled()}
														{if $CRON_TASK->hadTimedout()}
															{'LBL_LAST_SCAN_TIMED_OUT'|@getTranslatedString:'CronTasks'}.
														{elseif $CRON_TASK->getLastEndDateTime() neq ''}
															{'LBL_LAST_SCAN_AT'|@getTranslatedString:'CronTasks'}
															{$CRON_TASK->getLastEndDateTime()}
															&
															{'LBL_TIME_TAKEN'|@getTranslatedString:'CronTasks'}:
															{$CRON_TASK->getTimeDiff()}
															{'LBL_SHORT_SECONDS'|@getTranslatedString:'CronTasks'}
														{/if}
													{/if}
													</b>
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
									<table border=0 cellspacing=0 cellpadding=2 width=100% class="tableHeading">
										<tr>
											<td class="big">
												<div class="forceRelatedListSingleContainer">
													<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
														<div class="slds-card__header slds-grid">
															<header class="slds-media slds-media--center slds-has-flexi-truncate">
																<div class="slds-media__body">
																	<h2>
																		<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																			<strong>{$MOD.LBL_MAILBOX}</strong>
																		</span>
																	</h2>
																</div>
															</header>
															<div class="slds-no-flex">
																<a href="index.php?module=Settings&action=MailScanner&parenttab=Settings&mode=edit&scannername=">
																	<img src="{'btnL3Add.gif'|@vtiger_imageurl:$THEME}" border="0" style="width: 18px;" />
																</a>
															</div>
														</div>
													</article>
												</div>
											</td>
										</tr>
									</table>
								</td>
							</tr>
								{foreach item=SCANNER from=$SCANNERS}
									{assign var="SCANNERINFO" value=$SCANNER->getAsMap()}
										<tr>
											<td>
												<form action="index.php" method="post" id="form" onsubmit="VtigerJS_DialogBox.block();">
													<input type='hidden' name='module' value='Settings'>
													<input type='hidden' name='action' value='MailScanner'>
													<input type='hidden' name='mode' value='edit'>
													<input type='hidden' name='scannername' value='{$SCANNERINFO.scannername}'>
													<input type='hidden' name='return_action' value='MailScanner'>
													<input type='hidden' name='return_module' value='Settings'>
													<input type='hidden' name='parenttab' value='Settings'>

														{* When mode is Ajax, xmode will be set *}
														<input type='hidden' name='xmode' value=''>
														<input type='hidden' name='file' value=''>
														<div class="cb-alert-info" id="{$SCANNERINFO.scannername|@decode_html|@addslashes|@to_html}" style="display:none;"></div>

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
																								<strong>{$SCANNERINFO.scannername} {$MOD.LBL_INFORMATION}</strong>
																							</span>
																						</h2>
																					</div>
																				</header>
																			</div>
																		</article>
																	</div>
																</td>

																<td width="30%" nowrap align="right">
																	{if $SCANNERINFO.isvalid eq true}
																		{if $SCANNERINFO.rules neq false}
																		<input type="button" class="crmbutton small delete" value="{$MOD.LBL_SCAN_NOW}" onclick="performScanNow('{$APP_KEY}','{$SCANNERINFO.scannername|@decode_html|@addslashes|@to_html}')" />
																		{/if}
																		<input type="submit" class="crmbutton small cancel" onclick="this.form.mode.value='folder'" value="{$MOD.LBL_SELECT} {$MOD.LBL_FOLDERS}" />
																		<input type="submit" class="crmbutton small create" onclick="this.form.mode.value='rule'" value="{$MOD.LBL_SETUP} {$MOD.LBL_RULE}" />
																	{/if}
																		<input type="submit" class="crmbutton small edit" value="{$APP.LBL_EDIT}" />
																		<input type="submit" class="crmbutton small delete" onclick="if(confirm(alert_arr.ARE_YOU_SURE)){ldelim}with(this.form) {ldelim}action.value='SettingsAjax';file.value='MailScanner';mode.value='Ajax';xmode.value='remove';{rdelim}{rdelim}else return false;" value="{$MOD.LBL_DELETE}" />
																</td>
															</tr>
														</table>

														<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
															<tr>
																<td class="small" valign=top >
																	<table width="100%" border="0" cellspacing="0" cellpadding="5">
																		<tr>
																			<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_SCANNER} {$MOD.LBL_NAME}</strong></td>
																			<td width="80%" class="small cellText">{$SCANNERINFO.scannername}</td>
																		</tr>
																		<tr>
																			<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_SERVER} {$MOD.LBL_NAME}</strong></td>
																			<td width="80%" class="small cellText">{$SCANNERINFO.server}</td>
																		</tr>
																		<tr>
																			<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_PROTOCOL}</strong></td>
																			<td width="80%" class="small cellText">{$SCANNERINFO.protocol}</td>
																		</tr>
																		<tr>
																			<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_USERNAME}</strong></td>
																			<td width="80%" class="small cellText">{$SCANNERINFO.username}</td>
																		</tr>
																		<tr>
																			<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_SSL} {$MOD.LBL_TYPE}</strong></td>
																			<td width="80%" class="small cellText">{$SCANNERINFO.ssltype}</td>
																		</tr>
																		<tr>
																			<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_SSL} {$MOD.LBL_METHOD}</strong></td>
																			<td width="80%" class="small cellText">{$SCANNERINFO.sslmethod}</td>
																		</tr>
																		<tr>
																			<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_CONNECT} {$MOD.LBL_URL_CAPS}</strong></td>
																			<td width="80%" class="small cellText">{$SCANNERINFO.connecturl}</td>
																		</tr>
																		<tr>
																			<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_STATUS}</strong></td>
																			<td width="80%" class="small cellText">
																				{if $SCANNERINFO.isvalid eq true}<font color=green><b>{$MOD.LBL_ENABLED}</b></font>
																				{elseif $SCANNERINFO.isvalid eq false}<font color=red><b>{$MOD.LBL_DISABLED}</b></font>{/if}
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>
														</table>

														{if $SCANNERINFO.isvalid}
															<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
																<tr>
																	<td class="big" width="70%"><strong>{$MOD.LBL_SCANNING} {$MOD.LBL_INFORMATION}</strong></td>
																	<td width="30%" nowrap align="right">&nbsp;</td>
																</tr>
															</table>

															<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
																<tr>
																	<td class="small" valign=top >

																		<table width="100%" border="0" cellspacing="0" cellpadding="5">
																			<tr>
																				<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_LOOKFOR}</strong></td>
																				<td width="80%" class="small cellText">
																					{if $SCANNERINFO.searchfor eq 'ALL'}{$MOD.LBL_ALL} {$MOD.LBL_MESSAGES_FROM_LASTSCAN}
																					{elseif $SCANNERINFO.searchfor eq 'ALLUNSEEN'}{$MOD.LBL_ALLUNREAD}
																					{elseif $SCANNERINFO.searchfor eq 'UNSEEN'}{$MOD.LBL_UNREAD} {$MOD.LBL_MESSAGES_FROM_LASTSCAN}{/if}
																					{if $SCANNERINFO.requireRescan} [{$MOD.LBL_INCLUDE} {$MOD.LBL_SKIPPED}] {/if}
																					</td>
																				</tr>
																				<tr>
																					<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_AFTER_SCAN}</strong></td>
																					<td width="80%" class="small cellText">
																						{if $SCANNERINFO.markas eq 'SEEN'}{$MOD.LBL_MARK_MESSAGE_AS} {$MOD.LBL_READ}{/if}
																					</td>
																				</tr>
																			</td>
																		</table>

																	</td>
																</tr>
															</table>
														{/if}
												</form>

											</td>
										</tr>
								{/foreach}
						</table>

					</td></tr></table><!-- close tables from setMenu -->
					</td></tr></table><!-- close tables from setMenu -->

				</div>
			</td>
		</tr>
	</tbody>
</table>