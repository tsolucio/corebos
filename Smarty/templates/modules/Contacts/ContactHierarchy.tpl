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
<script type="text/javascript" src="include/js/general.js"></script>
<script type="text/javascript" src="include/js/{$LANGUAGE}.lang.js"></script>
{include file='Buttons_List.tpl'}
		</td>
	</tr>
</table>

{*<!-- Contents -->*}
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
	<tr>
		<td>
			<!-- PUBLIC CONTENTS STARTS-->
			<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz" style="background-color: #f7f9fb;margin-bottom: 1rem;">
				<tr class="slds-text-title--caps">
					<td style="padding: 0;">
						<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilDesktop" style="height: 70px;border:none;">
							<div class="slds-grid primaryFieldRow" style="transform: translate3d(0, -8.65823px, 0);">
								<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
									<div class="profilePicWrapper slds-media slds-no-space" style="transform: scale3d(0.864715, 0.864715, 1) translate3d(4.32911px, 2.16456px, 0);">
										<div class="slds-media__figure slds-icon forceEntityIcon">
											<span class="photoContainer forceSocialPhoto">
												<div class="small roundedSquare forceEntityIcon img-background">
													<span class="uiImage">
														<img src="{'contact_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="Contact" title="Contact">
													</span>
												</div>
											</span>
										</div>
									</div>
									<div class="slds-media__body">
										<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
											<span class="uiOutputText">{$MOD.LBL_CONTACT_HIERARCHY}</span>
										</h1>
									</div>
								</div>
								<div class="slds-col slds-no-flex slds-grid slds-align-middle actionsContainer" id="detailview_utils_thirdfiller">
									<div class="slds-grid forceActionsContainer">
										<input type="button" class="slds-button slds-button--small slds-button--destructive" onclick="window.history.back();" value="{$APP.LBL_BACK}"/>
									</div>
								</div>
							</div>
						</div>
					</td>
				</tr>
			</table>

			<div id="ListViewContents">
				{foreach key=header item=detail from=$CONTACT_HIERARCHY}
					{if $header eq 'header'}
						<table class="slds-table slds-table--bordered slds-table--fixed-layout ld-font">
							<thead>
								<tr class="slds-line-height_reset">
								{foreach key=header item=headerfields from=$detail}
									<th scope="col" class="slds-text-title_caps">
										<div class="slds-truncate" style="padding: .5rem 0;">{$headerfields}</div>
									</th>
								{/foreach}
								</tr>
							</thead>
					{elseif $header eq 'entries'}
						{foreach key=header item=entriesfields from=$detail}
							<tbody>
								<tr class="slds-hint-parent slds-line-height--reset">
									{foreach key=header item=listfields from=$entriesfields}
										<th scope="row">
											<div class="slds-truncate">{$listfields}</div>
										</th>
									{/foreach}
								</tr>
							</tbody>
						{/foreach}
						</table>
					{/if}
				{/foreach}
			</div>

		</td>
	</tr>
</table>