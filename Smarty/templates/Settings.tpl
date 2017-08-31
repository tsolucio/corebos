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

<script type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>

<!-- Lighting Design Components by Endrit -->
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center class=small style="background-color: #f7f9fb;">
	<tr class="slds-text-title--caps">
		{assign var="action" value="WebformsListView"}
		{assign var="MODULELABEL" value=$MODULE|@getTranslatedString:$MODULE}
		<th scope="col" style="padding: 1rem 1.5rem 1rem 1rem;">
			<div class="slds-truncate moduleName" title="{$MODULELABEL}">
				<a class="hdrLink" href="#">{$MODULELABEL}</a>
			</div>
		</th>
	</tr>
</table>

<table class="slds-table slds-no-row-hover slds-table--fixed-layout" style="border-collapse:separate; border-spacing: 1rem;">
	{foreach key=BLOCKID item=BLOCKLABEL from=$BLOCKS}
		{if $BLOCKLABEL neq 'LBL_MODULE_MANAGER'}
			<tr>
				<td>
					<!-- Start Head Titles -->
						<div class="forceRelatedListSingleContainer">
							<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
								<div class="slds-card__header slds-grid">
									<header class="slds-media slds-media--center slds-has-flexi-truncate">
										<div class="slds-media__body">
											<h2>
												<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small">
													<b>{$MOD.$BLOCKLABEL}</b>
												</span>
											</h2>
										</div>
									</header>
								</div>
							</article>
						</div>
					<!-- End Head Titles -->

					<!-- End Fields Blocks -->
						<table class="slds-table slds-no-row-hover slds-table--fixed-layout">
							<tr class="slds-line-height--reset">
								{foreach item=data from=$FIELDS.$BLOCKID name=itr}
									<td width=25% valign=top>
										{if $data.name eq ''}
											&nbsp;
										{else}
											<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilSettings" style="height: 100px;">
												<div class="slds-grid primaryFieldRow" style="transform: translate3d(0, -8.65823px, 0);">
													<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
														<table border=0 cellspacing=0 cellpadding=5 width=100%>
															<tr class="row-2nd-css">
																{assign var=label value=$data.name|@getTranslatedString:$data.module}
																{if $data.name eq $label}
																{assign var=label value=$data.name|@getTranslatedString:'Settings'}
																{/if}
																{assign var=count value=$smarty.foreach.itr.iteration}
																<td rowspan=2 valign=top style="width: 72px;">
																	<div class="profilePicWrapper slds-media slds-no-space" style="transform: scale3d(0.864715, 0.864715, 1) translate3d(4.32911px, 2.16456px, 0);">
																		<div class="slds-media__figure slds-icon forceEntityIcon">
																			<span class="photoContainer forceSocialPhoto">
																				<div class="small roundedSquare forceEntityIcon">
																				<span class="uiImage">
																					<a href="{$data.link}">
																						<img src="{$data.icon|@vtiger_imageurl:$THEME}" alt="{$label}" border=0 title="{$label}">
																					</a>
																				</span>
																				</div>
																			</span>
																		</div>
																	</div>
																</td>
																<td class=big valign=top>
																	<div class="slds-media__body">
																		<h2>
																			<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small" title="{$header}" style="white-space: normal;">
																				<a href="{$data.link}">{$label}</a>
																			</span>
																		</h2>
																	</div>
																</td>
															</tr>
															<tr>
																{assign var=description value=$data.description|@getTranslatedString:$data.module}
																{if $data.description eq $description}
																{assign var=description value=$data.description|@getTranslatedString:'Settings'}
																{/if}
																<td class="small" valign=top>
																	<div class="slds-media__body">
																		<span class="small">{$description}</span>
																	</div>
																</td>
															</tr>
														</table>
													</div>
												</div>
											</div>
										{/if}
									</td>
									{if $count mod $NUMBER_OF_COLUMNS eq 0}
										</tr>
										<tr class="second-row">
									{/if}
								{/foreach}
						</table>
					<!-- End Fields Blocks -->

				</td>
			</tr>
		{/if}
	{/foreach}
</table>

