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
<script type="text/javascript" src="modules/Rss/Rss.js"></script>
<script>
var i18n_DELETE_RSSFEED_CONFIRMATION = '{$APP.DELETE_RSSFEED_CONFIRMATION}';
</script>

<!-- Contents -->
{include file="Buttons_List.tpl"}
<div id="temp_alert" style="display:none"></div>
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
	<tr>
		<td>
			<!-- RSS Reader UI Starts here-->
			<br>
			<table border=0 cellspacing=0 cellpadding=0 width=100% align=center class="mailClient rss-table">
				<tr>
					<td valign=top align=left>
						<table class="slds-table slds-no-row-hover dvtContentSpace">
							<tr class="slds-line-height--reset">
								<td class="noprint action-block-rss" style="padding-right: 20px;">
									<div class="forceRelatedListSingleContainer">
										<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
											<div class="slds-card__header slds-grid">
												<div class="slds-media__figure" style="margin-right: 1px;">
													<div class="extraSmall forceEntityIcon">
														<span class="uiImage">
															<img src='{"rssroot.gif"|@vtiger_imageurl:$THEME}' width="20" />
														</span>
													</div>
												</div>
												<header class="slds-media slds-media--center slds-has-flexi-truncate">
													<div class="slds-media__body">
														<h2>
															<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small">
																<a href="javascript:;" onClick="fnvshobj(this,'PopupLay');document.getElementById('rssurl').focus();" title='{$APP.LBL_ADD_RSS_FEEDS}'>{$MOD.LBL_ADD_RSS_FEED}</a>
															</span>
														</h2>
													</div>
												</header>
											</div>
										</article>
									</div>
									<br/>
									<!-- Feed Folders -->
									<div class="flexipageComponent">
										<article class="slds-card container MEDIUM forceBaseCard runtime_sales_mergeMergeCandidatesPreviewCard" aria-describedby="header" style="margin: 0;">
											<div class="slds-card__header slds-grid">
												<header class="slds-media slds-media--center slds-has-flexi-truncate">
													<div class="slds-media__body">
														<h2 class="header-title-container">
															<span class="slds-text-heading--small slds-truncate actionLabel"><b>{$MOD.LBL_FEED_SOURCES}</b></span>
														</h2>
													</div>
												</header>
											</div>
											<div class="slds-card__body slds-card__body--inner">
												<div class="actionData">
													<div id="rssfolders" style="height:100%;overflow:auto;">{$RSSFEEDS}</div>
												</div>
											</div>
										</article>
									</div>
								</td>
								<td valign="top" style="padding: 0;">
									<div class="slds-table--scoped">
										<ul class="slds-tabs--scoped__nav" role="tablist" style="margin-bottom: 0;">
											<li class="slds-tabs--scoped__item active" title="{$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}" role="presentation">
												<a class="slds-tabs--scoped__link " href="javascript:void(0);" role="tab" tabindex="0" aria-selected="true" aria-controls="tab--scoped-1" id="tab--scoped--1__item">{$MOD.LBL_VTIGER_RSS_READER}</a>
											</li>
										</ul>
										<div id="tab--scoped-1" role="tabpanel" aria-labelledby="tab--scoped-1__item" class="slds-tabs--scoped__content slds-truncate">
											<div class="slds-truncate">
												<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout small detailview_table">
													<tr class="slds-line-height--reset">
														<td class="text-left" valign="top" width=40% style="padding-right: .5rem;">
															<!-- Feed Folders -->
															<div class="flexipageComponent">
																<article class="slds-card container MEDIUM forceBaseCard runtime_sales_mergeMergeCandidatesPreviewCard" aria-describedby="header" style="margin: 0;">
																	<div class="slds-card__header slds-grid">
																		<header class="slds-media slds-media--center slds-has-flexi-truncate">
																			<div class="slds-media__body">
																				<h2 class="header-title-container">
																					<span class="slds-text-heading--small slds-truncate actionLabel"><b>{$MOD.LBL_FEED_SOURCES}</b></span>
																				</h2>
																			</div>
																		</header>
																	</div>
																</article>
															</div>
															<div class="slds-truncate">
																<div id="rssfolders" style="height:100%;overflow:auto;">{$RSSFEEDS}</div>
															</div>
														</td>
														<td class=" text-left" valign="top" width=60% style="padding-right: .5rem;">
															<div id="rssfeedscont" style="padding: 0 .5rem;">
																{include file='RssFeeds.tpl'}
															</div>
														</td>
													</tr>
												</table>
											</div>
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<td id="rsstitle" class="dvtCellLabel" colspan="2" align="center" style="padding-top: .5rem;">&nbsp;</td>
							</tr>
							<tr>
								<!-- RSS Display -->
								<td colspan="2" style="padding:2px">
									<iframe width="100%" height="250" frameborder="0" id="mysite" scrolling="auto" marginheight="0" marginwidth="0" style="background-color:#FFFFFF;"></iframe>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<!-- RSS Reader UI ends here -->
		</td>
	</tr>
</table>

<div id="PopupLay" class="layerPopup">
	<form onSubmit="SaveRssFeeds(); return false;">
		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
			<tr>
				<td class="layerPopupHeading" align="left"><img src="{'rssroot.gif'|@vtiger_imageurl:$THEME}" width="24" height="22" align="absmiddle" />&nbsp;{$MOD.LBL_ADD_RSS_FEED}</td>
				<td align="right"><a href="javascript:fninvsh('PopupLay');"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" /></a></td>
			</tr>
		</table>
		<table border=0 cellspacing=0 cellpadding=5 width=95% align=center>
			<tr>
				<td class=small >
					<!-- popup specific content fill in starts -->
					<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
						<tr>
							<td align="right" width="25%"><b>{$MOD.LBL_FEED}</b></td>
							<td align="left" width="75%"><input type="text" id="rssurl" class="txtBox" value=""/></td>
						</tr>
					</table>
					<!-- popup specific content fill in ends -->
				</td>
			</tr>
		</table>
		<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
			<tr>
				<td align="center">
					<input type="submit" name="save" value=" &nbsp;{$APP.LBL_SAVE_BUTTON_LABEL}&nbsp; " class="crmbutton small save"/>&nbsp;&nbsp;
				</td>
			</tr>
		</table>
	</form>
</div>
