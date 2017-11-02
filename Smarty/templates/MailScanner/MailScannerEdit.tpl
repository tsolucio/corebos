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
						<!-- DISPLAY Mail  Converter Edit view-->
							<form action="index.php" method="post" id="form" onsubmit="VtigerJS_DialogBox.block();">
								<input type='hidden' name='module' value='Settings'>
								<input type='hidden' name='action' value='MailScanner'>
								<input type='hidden' name='mode' value='save'>
								<input type='hidden' name='return_action' value='MailScanner'>
								<input type='hidden' name='return_module' value='Settings'>
								<input type='hidden' name='parenttab' value='Settings'>

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
																			<img src="{'mailScanner.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_MAIL_SCANNER}" title="{$MOD.LBL_MAIL_SCANNER}">
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
													</div>
												</div>
											</div>
										</td>
									</tr>
								</table>


								<table border=0 cellspacing=0 cellpadding=10 width=100% >
									<tr>
										<td>

											{if !empty($CONNECTFAIL)}
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
																						<font color='red'><b>{$CONNECTFAIL}</b></font>
																					</span>
																				</h2>
																			</div>
																		</header>
																	</div>
																</article>
															</div>
														</td>
													</tr>
												</table>
											{/if}

											<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
												<tr>
													<td class="big" width="70%">

														<div class="forceRelatedListSingleContainer">
															<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																<div class="slds-card__header slds-grid">
																	<header class="slds-media slds-media--center slds-has-flexi-truncate">
																		<div class="slds-media__body">
																			<h2>
																				<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																					<strong>{$MOD.LBL_MAILBOX} {$MOD.LBL_INFORMATION}</strong>
																				</span>
																			</h2>
																		</div>
																	</header>
																</div>
															</article>
														</div>
														<div class="slds-truncate">
															<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table listRow">
																<tr>
																	<td width="15%" nowrap class="small dvtCellLabel"><strong>{$MOD.LBL_SCANNER} {$MOD.LBL_NAME}</strong> <font color="red">*</font></td>
																	<td width="85%" class="dvtCellInfo">
																		<input type="hidden" name="hidden_scannername" class="small" value="{$SCANNERINFO.scannername}" readonly>
																		<input type="text" name="mailboxinfo_scannername" class="small slds-input" value="{$SCANNERINFO.scannername}" size=50>
																	</td>
																</tr>
																<tr>
																	<td nowrap class="small dvtCellLabel"><strong>{$MOD.LBL_SERVER} {$MOD.LBL_NAME}</strong> <font color="red">*</font></td>
																	<td class="dvtCellInfo"><input type="text" name="mailboxinfo_server" class="small slds-input" value="{$SCANNERINFO.server}" size=50></td>
																</tr>
																<tr>
																	<td nowrap class="small dvtCellLabel"><strong>{$MOD.LBL_PROTOCOL}</strong> <font color="red">*</font></td>
																	<td class="dvtCellInfo">
																		{assign var="imapused" value=""}
																		{assign var="imap4used" value=""}
																		{if $SCANNERINFO.protocol eq 'imap4'}
																			{assign var="imap4used" value="checked='true'"}
																		{else}
																			{assign var="imapused" value="checked='true'"}
																		{/if}
																		<span class="slds-radio">
																			<input type="radio" name="mailboxinfo_protocol" id="imap" value="imap" {$imapused}>
																			<label class="slds-radio__label" for="imap">
																				<span class="slds-radio--faux"></span>
																				<span class="slds-form-element__label">{$MOD.LBL_IMAP2}</span>
																			</label>
																		</span>
																		<span class="slds-radio">
																			<input type="radio" name="mailboxinfo_protocol" id="imap4" value="imap4" {$imap4used}>
																			<label class="slds-radio__label" for="imap4">
																				<span class="slds-radio--faux"></span>
																				<span class="slds-form-element__label">{$MOD.LBL_IMAP4}</span>
																			</label>
																		</span>
																	</td>
																</tr>
																<tr>
																	<td nowrap class="small dvtCellLabel"><strong>{$MOD.LBL_USERNAME}</strong> <font color="red">*</font></td>
																	<td class="dvtCellInfo"><input type="text" name="mailboxinfo_username" class="small slds-input" value="{$SCANNERINFO.username}" size=50></td>
																</tr>
																<tr>
																	<td nowrap class="small dvtCellLabel"><strong>{$MOD.LBL_PASSWORD}</strong> <font color="red">*</font></td>
																	<td class="dvtCellInfo"><input type="password" name="mailboxinfo_password" class="small slds-input" value="{$SCANNERINFO.password}" size=50></td>
																</tr>
																<tr>
																	<td nowrap class="small dvtCellLabel"><strong>{$MOD.LBL_SSL} {$MOD.LBL_TYPE}</strong></td>
																	<td class="small dvtCellInfo">
																		{assign var="notls_type" value=""}
																		{assign var="tls_type" value=""}
																		{assign var="ssl_type" value=""}

																		{if $SCANNERINFO.ssltype eq 'notls'}
																			{assign var="notls_type" value="checked='true'"}
																		{elseif $SCANNERINFO.ssltype eq 'tls'}
																			{assign var="tls_type" value="checked='true'"}
																		{elseif $SCANNERINFO.ssltype eq 'ssl'}
																			{assign var="ssl_type" value="checked='true'"}
																		{/if}
																		<span class="slds-radio">
																			<input type="radio" name="mailboxinfo_ssltype" id="notls" value="notls" {$notls_type}>
																			<label class="slds-radio__label" for="notls">
																				<span class="slds-radio--faux"></span>
																				<span class="slds-form-element__label">{$MOD.LBL_NO} {$MOD.LBL_TLS}</span>
																			</label>
																		</span>

																		<span class="slds-radio">
																			<input type="radio" name="mailboxinfo_ssltype" id="tls" value="tls" {$tls_type}>
																			<label class="slds-radio__label" for="tls">
																				<span class="slds-radio--faux"></span>
																				<span class="slds-form-element__label">{$MOD.LBL_TLS}</span>
																			</label>
																		</span>

																		<span class="slds-radio">
																			<input type="radio" name="mailboxinfo_ssltype" id="ssl" value="ssl" {$ssl_type}>
																			<label class="slds-radio__label" for="ssl">
																				<span class="slds-radio--faux"></span>
																				<span class="slds-form-element__label">{$MOD.LBL_SSL}</span>
																			</label>
																		</span>
																	</td>
																</tr>
																<tr>
																	<td width="20%" nowrap class="small dvtCellLabel"><strong>{$MOD.LBL_SSL} {$MOD.LBL_METHOD}</strong></td>
																	<td width="80%" class="small cellText">
																		{assign var="novalidatecert_type" value=""}
																		{assign var="validatecert_type" value=""}

																		{if $SCANNERINFO.sslmethod eq 'validate-cert'}
																			{assign var="validatecert_type" value="checked='true'"}
																		{else}
																			{assign var="novalidatecert_type" value="checked='true'"}
																		{/if}
																		<span class="slds-radio">
																			<input type="radio" name="mailboxinfo_sslmethod" id="validate-cert" value="validate-cert" {$validatecert_type}>
																			<label class="slds-radio__label" for="validate-cert">
																				<span class="slds-radio--faux"></span>
																				<span class="slds-form-element__label">{$MOD.LBL_VAL_SSL_CERT}</span>
																			</label>
																		</span>
																		<span class="slds-radio">
																			<input type="radio" name="mailboxinfo_sslmethod" id="novalidate-cert" value="novalidate-cert" {$novalidatecert_type}>
																			<label class="slds-radio__label" for="novalidate-cert">
																				<span class="slds-radio--faux"></span>
																				<span class="slds-form-element__label">{$MOD.LBL_DONOT_VAL_SSL_CERT}</span>
																			</label>
																		</span>
																	</td>
																</tr>
																<tr>
																	<td width="20%" nowrap class="small dvtCellLabel"><strong>{$MOD.LBL_STATUS}</strong></td>
																	<td width="80%" class="small dvtCellInfo">
																		{assign var="mailbox_enable" value=""}
																		{assign var="mailbox_disable" value=""}

																		{if $SCANNERINFO.isvalid eq false}
																			{assign var="mailbox_disable" value="checked='true'"}
																		{else}
																			{assign var="mailbox_enable" value="checked='true'"}
																		{/if}
																		<span class="slds-radio">
																			<input type="radio" name="mailboxinfo_enable" id="enable" value="true" {$mailbox_enable}>
																			<label class="slds-radio__label" for="enable">
																				<span class="slds-radio--faux"></span>
																				<span class="slds-form-element__label">{$MOD.LBL_ENABLE}</span>
																			</label>
																		</span>
																		<span class="slds-radio">
																			<input type="radio" name="mailboxinfo_enable" id="disable" value="false" {$mailbox_disable}>
																			<label class="slds-radio__label" for="disable">
																				<span class="slds-radio--faux"></span>
																				<span class="slds-form-element__label">{$MOD.LBL_DISABLE}</span>
																			</label>
																		</span>
																	</td>
																</tr>
															</table>
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
																					<strong>{$MOD.LBL_SCANNING} {$MOD.LBL_INFORMATION}</strong>
																				</span>
																			</h2>
																		</div>
																	</header>
																</div>
															</article>
														</div>
														<div class="slds-truncate">
															<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table listRow">
																<tr>
																	<td width="15%" nowrap class="small dvtCellLabel"><strong>{$MOD.LBL_LOOKFOR}</strong></td>
																	<td width="85%" class="small dvtCellInfo">
																		<select name="mailboxinfo_searchfor" class="small slds-select" style="width: 75%;">
																			<option value="ALL" {if $SCANNERINFO.searchfor eq 'ALL'}selected=true{/if} onclick="jQuery('#mailboxinfo_rescan_folders_span').show();">{$MOD.LBL_ALL} {$MOD.LBL_MESSAGES_FROM_LASTSCAN}</option>
																			<option value="UNSEEN" {if $SCANNERINFO.searchfor eq 'UNSEEN'}selected=true{/if} onclick="this.form.mailboxinfo_rescan_folders.checked=false;jQuery('#mailboxinfo_rescan_folders_span').hide();">{$MOD.LBL_UNREAD} {$MOD.LBL_MESSAGES_FROM_LASTSCAN}</option>
																			<option value="ALLUNSEEN" {if $SCANNERINFO.searchfor eq 'ALLUNSEEN'}selected=true{/if} onclick="this.form.mailboxinfo_rescan_folders.checked=false;jQuery('#mailboxinfo_rescan_folders_span').hide();">{$MOD.LBL_ALLUNREAD}</option>
																		</select>
																		{if $SCANNERINFO.searchfor eq 'ALL'}
																			<span id="mailboxinfo_rescan_folders_span">
																			{* Disabling Rescanning of messages for later use *}
																			{* -- START
																			<input type="checkbox" name="mailboxinfo_rescan_folders" value="true" class="small" {if $SCANNERINFO.requireRescan}checked=true{/if}> {$MOD.LBL_INCLUDE} {$MOD.LBL_SKIPPED}</input>
																			-- END *}
																			</span>
																		{/if}
																	</td>
																</tr>
																<tr>
																	<td nowrap class="small dvtCellLabel"><strong>{$MOD.LBL_AFTER_SCAN}</strong></td>
																	<td class="small dvtCellInfo">
																		{$MOD.LBL_MARK_MESSAGE_AS}:
																		<select name="mailboxinfo_markas" class="small slds-select" style="width: 67%;">
																			<option value=""></option>
																			<option value="SEEN" {if $SCANNERINFO.markas eq 'SEEN'}selected=true{/if} >{$MOD.LBL_READ}</option>
																		</select>
																	</td>
																</tr>
															</table>
														</div>
													</td>
												</tr>
											</table>

											<table width="100%">
												<tr>
													<td colspan=2 nowrap align="center" style="padding: 5px;background-color: #f7f9fb;">
														<input type="submit" class="slds-button slds-button--small slds-button_success" value="{$APP.LBL_SAVE_LABEL}" onclick="return mailscaneredit_validateform(this.form);" />
														<input type="button" class="slds-button slds-button--small slds-button--destructive" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onclick="window.location.href='index.php?module=Settings&action=MailScanner&parenttab=Settings'"/>
													</td>
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

{literal}
<script type="text/javascript">
function mailscaneredit_validateform(form) {
	var scannername = form.mailboxinfo_scannername;
	if(scannername.value == '') {
		alert(alert_arr.VALID_SCANNER_NAME);
		scannername.focus();
		return false;
	} else {
		var regex=/^[0-9A-Za-z]+$/;
		if(regex.test(scannername.value)){
			return true;
		}else {
			alert(alert_arr.VALID_SCANNER_NAME);
			scannername.focus();
			return false;
		}
	}
}
</script>
{/literal}
