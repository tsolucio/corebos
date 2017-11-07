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
<!-- BEGIN: main -->
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
									<div class="small roundedSquare forceEntityIcon sites-settings">
										<span class="uiImage">
											<a href="#" onclick="fetchContents('data');"><img src="{'webmail_settings.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border=0 /></a>
										</span>
									</div>
								</span>
							</div>
						</div>
						<!-- Title and help text -->
						<div class="slds-media__body">
							<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
								<span class="uiOutputText componentName">
									<b>{$MOD.LBL_MY_SITES}</b>
								</span>
							</h1>
							<a href="#" onclick="fetchContents('data');">{$MOD.LBL_MY_SITES}</a>
						</div>
						
					</div>
				</div>
			</div>
		</td>
	</tr>
</table>

<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr>
		<td colspan="3" class="genHeaderSmall" align="left">
			<div class="forceRelatedListSingleContainer">
				<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
					<div class="slds-card__header slds-grid">
						<header class="slds-media slds-media--center slds-has-flexi-truncate">
							<div class="slds-media__body">
								<h2>
									<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
										<strong>{$MOD.LBL_MY_BOOKMARKS}</strong>
									</span>
								</h2>
							</div>
						</header>
						<div class="slds-no-flex">
							<input name="bookmark" value=" {$MOD.LBL_NEW_BOOKMARK} " class="slds-button slds-button--small slds-button_success" onclick="fnvshobj(this,'editportal_cont');fetchAddSite('');" type="button">
						</div>
					</div>
				</article>
			</div>
			<div class="slds-truncate">
				<table class="slds-table slds-table--bordered ld-font listTable"> 
					<thead>
						<tr>
							<th class="colHeader slds-text-title--caps" scope="col"><span class="slds-truncate"><b>{$MOD.LBL_SNO}</b></span></th>
							<th class="colHeader slds-text-title--caps" scope="col"><span class="slds-truncate"><b>{$MOD.LBL_BOOKMARK_NAME_URL}</b></span></th>
							<th class="colHeader slds-text-title--caps" scope="col"><span class="slds-truncate"><b>{$MOD.LBL_TOOLS}</b></span></th>
						</tr>
					</thead>
					<tbody>
						{foreach name=portallists item=portaldetails key=sno from=$PORTALS}
							<tr class="slds-hint-parent slds-line-height--reset">
								<th scope="row" class="listTableRow"><div class="slds-truncate">{$smarty.foreach.portallists.iteration}</div></th>
								<th scope="row" class="listTableRow">
									<div class="slds-truncate">
										<b>{$portaldetails.portalname}</b>
										<br>
										<span class="big">{$portaldetails.portaldisplayurl}</span>
									</div>
								</th>
								<th scope="row" class="listTableRow">
									<div class="slds-truncate">
										<a href="javascript:;" onclick="fnvshobj(this,'editportal_cont');fetchAddSite('{$portaldetails.portalid}');" class="webMnu">{$APP.LBL_EDIT}</a>&nbsp;|&nbsp;
										<a href="javascript:;" onclick="DeleteSite('{$portaldetails.portalid}');"class="webMnu">{$APP.LBL_MASS_DELETE}</a>
									</div>
								</th>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</td>
	</tr>
</table>