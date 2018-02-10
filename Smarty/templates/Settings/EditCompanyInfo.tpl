{*<!--
/*********************************************************************************
	** The contents of this file are subject to the vtiger CRM Public License Version 1.0
	 * ("License"); You may not use this file except in compliance with the License
	 * The Original Code is:	vtiger CRM Open Source
	 * The Initial Developer of the Original Code is vtiger.
	 * Portions created by vtiger are Copyright (C) vtiger.
	 * All Rights Reserved.
 ********************************************************************************/
-->*}
<script type="text/javascript" src="include/js/smoothscroll.js"></script>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
        <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
<br>
	<form action="index.php?module=Settings&action=add2db" method="post" name="index" enctype="multipart/form-data" onsubmit="VtigerJS_DialogBox.block();">
 	<input type="hidden" name="return_module" value="Settings">
 	<input type="hidden" name="parenttab" value="Settings">
    	<input type="hidden" name="return_action" value="OrganizationConfig">
	<div align=center>
			{include file="SetMenu.tpl"}
				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{'company.gif'|@vtiger_imageurl:$THEME}" width="48" height="48" border=0 ></td>
					<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > {$MOD.LBL_EDIT} {$MOD.LBL_COMPANY_DETAILS} </b></td>
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_COMPANY_DESC}</td>
				</tr>
				</table>

				<br>
					<form action="index.php?module=Settings&action=add2db" method="post" name="index" enctype="multipart/form-data" onsubmit="VtigerJS_DialogBox.block();">
						<input type="hidden" name="return_module" value="Settings">
						<input type="hidden" name="parenttab" value="Settings">
						<input type="hidden" name="return_action" value="OrganizationConfig">
							{include file="SetMenu.tpl"}
							<!-- DISPLAY Editing Company Info-->
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
																		<img src="{'company.gif'|@vtiger_imageurl:$THEME}"/>
																	</span>
																</div>
															</span>
														</div>
													</div>
													<!-- Title and help text -->
													<div class="slds-media__body">
														<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
															<span class="uiOutputText" style="width: 100%;">
																<b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > {$MOD.LBL_EDIT} {$MOD.LBL_COMPANY_DETAILS} </b>
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
																	{if isset($ERRORFLAG)}{$ERRORFLAG}{/if}<br>
																</span>
															</h2>
														</div>
													</header>
													<div class="slds-no-flex">
														<input title="{$APP.LBL_SAVE_BUTTON_LABEL}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="slds-button--small slds-button slds-button_success" type="submit" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" onclick="return verify_data(form,'{$MOD.LBL_ORGANIZATION_NAME}');" >
														<input title="{$APP.LBL_CANCEL_BUTTON_LABEL}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="slds-button slds-button--small slds-button--destructive" onclick="window.history.back()" type="button" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}">
													</div>
												</div>
											</article>
										</div>

										<div class="slds-truncate">
											<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table">
												<tr>
													<td width="20%" class="small dvtCellLabel"><font color="red">*</font><strong>{$MOD.LBL_ORGANIZATION_NAME}</strong></td>
													<td width="80%" class="small dvtCellInfo">
														<input type="text" name="organization_name" class="slds-input small" value="{$ORGANIZATIONNAME}">
														<input type="hidden" name="org_name" value="{$ORGANIZATIONNAME}">
													</td>
												</tr>
												<tr>
													<td class="small dvtCellLabel"><strong>{$MOD.LBL_ORGANIZATION_LOGO}</strong></td>
													{if $ORGANIZATIONLOGONAME neq ''}
														<td class="small dvtCellInfo" style="background-image: url(test/logo/{$ORGANIZATIONLOGONAME}); background-position: left; background-repeat: no-repeat;" height="60" border="0" >
													{else}
														<td class="small dvtCellInfo" style="background-image: url(include/images/noimage.gif); background-position: left; background-repeat: no-repeat;" height="60" border="0" >
													{/if}
													<br/><br/><br/><br/><br/>
													{$MOD.LBL_SELECT_LOGO}
															<INPUT TYPE="HIDDEN" NAME="MAX_FILE_SIZE" VALUE="800000">
															<INPUT TYPE="HIDDEN" NAME="PREV_FILE" VALUE="{$ORGANIZATIONLOGONAME}">
															<input type="file" name="binFile" class="small" value="{$ORGANIZATIONLOGONAME}" onchange="validateFilename(this);">[{$ORGANIZATIONLOGONAME}]
															<input type="hidden" name="binFile_hidden" value="{$ORGANIZATIONLOGONAME}" />
														</td>
												</tr>
												<tr>
													<td class="small dvtCellLabel"><strong>{$MOD.LBL_FRONT_LOGO}</strong></td>
													{if $FORNTLOGONAME neq ''}
														<td class="small dvtCellInfo" style="background-image: url(test/logo/{$FORNTLOGONAME}); background-position: left; background-repeat: no-repeat;"  height="60" border="0" >
													{else}
														<td class="small dvtCellInfo" style="background-image: url(include/images/noimage.gif); background-position: left; background-repeat: no-repeat;" height="60" border="0" >
													{/if}
													<br/><br/><br/><br/><br/>
															{$MOD.LBL_SELECT_LOGO}
															<INPUT TYPE="HIDDEN" NAME="MAX_FILE_SIZE" VALUE="800000">
															<INPUT TYPE="HIDDEN" NAME="PREV_FRONT_FILE" VALUE="{$FORNTLOGONAME}">
															<input type="file" name="binFrontFile" class="small" value="{$FORNTLOGONAME}" onchange="validateFilename(this);">[{$FORNTLOGONAME}]
															<input type="hidden" name="binFrontFile_hidden" value="{$FORNTLOGONAME}" />
														</td>
												</tr>
												<tr>
													<td class="small dvtCellLabel"><strong>{$MOD.LBL_FAVICON_LOGO}</strong></td>
													{if $FAVICONLOGONAME neq ''}
														<td class="small dvtCellInfo" style="background-image: url(test/logo/{$FAVICONLOGONAME}); background-position: left; background-repeat: no-repeat;" height="60" border="0" >
													{else}
														<td class="small dvtCellInfo" style="background-image: url(include/images/noimage.gif); background-position: left; background-repeat: no-repeat;" height="60" border="0" >
													{/if}
													<br/><br/><br/><br/><br/>
														{$MOD.LBL_SELECT_LOGO}
															<INPUT TYPE="HIDDEN" NAME="MAX_FILE_SIZE" VALUE="800000">
															<INPUT TYPE="HIDDEN" NAME="PREV_FAVICON_FILE" VALUE="{$FAVICONLOGONAME}">
															<input type="file" name="binFaviconFile" class="small" value="{$FAVICONLOGONAME}" onchange="validateFilename(this);">[{$FAVICONLOGONAME}]
															<input type="hidden" name="binFaviconFile_hidden" value="{$FAVICONLOGONAME}" />
														</td>
												</tr>
												<tr>
													<td class="small dvtCellLabel"><strong>{$MOD.LBL_ORGANIZATION_ADDRESS}</strong></td>
													<td class="small dvtCellInfo"><input type="text" name="organization_address" class="slds-input small" value="{$ORGANIZATIONADDRESS}"></td>
												</tr>
												<tr>
													<td class="small dvtCellLabel"><strong>{$MOD.LBL_ORGANIZATION_CITY}</strong></td>
													<td class="small dvtCellInfo"><input type="text" name="organization_city" class="slds-input small" value="{$ORGANIZATIONCITY}"></td>
												</tr>
												<tr>
													<td class="small dvtCellLabel"><strong>{$MOD.LBL_ORGANIZATION_STATE}</strong></td>
													<td class="small dvtCellInfo"><input type="text" name="organization_state" class="slds-input small" value="{$ORGANIZATIONSTATE}"></td>
												</tr>
												<tr>
													<td class="small dvtCellLabel"><strong>{$MOD.LBL_ORGANIZATION_CODE}</strong></td>
													<td class="small dvtCellInfo"><input type="text" name="organization_code" class="slds-input small" value="{$ORGANIZATIONCODE}"></td>
												</tr>
												<tr>
													<td class="small dvtCellLabel"><strong>{$MOD.LBL_ORGANIZATION_COUNTRY}</strong></td>
													<td class="small dvtCellInfo"><input type="text" name="organization_country" class="slds-input small" value="{$ORGANIZATIONCOUNTRY}"></td>
												</tr>
												<tr>
													<td class="small dvtCellLabel"><strong>{$MOD.LBL_ORGANIZATION_PHONE}</strong></td>
													<td class="small dvtCellInfo"><input type="text" name="organization_phone" class="slds-input small" value="{$ORGANIZATIONPHONE}"></td>
												</tr>
												<tr>
													<td class="small dvtCellLabel"><strong>{$MOD.LBL_ORGANIZATION_FAX}</strong></td>
													<td class="small dvtCellInfo"><input type="text" name="organization_fax" class="slds-input small" value="{$ORGANIZATIONFAX}"></td>
												</tr>
												<tr>
													<td class="small dvtCellLabel"><strong>{$MOD.LBL_ORGANIZATION_WEBSITE}</strong></td>
													<td class="small dvtCellInfo"><input type="text" name="organization_website" class="slds-input small" value="{$ORGANIZATIONWEBSITE}"></td>
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

					</form>

					</td></tr></table><!-- close tables from setMenu -->
					</td></tr></table><!-- close tables from setMenu -->

				</div>
			</td>
		</tr>
	</tbody>
</table>

{literal}
<script>
function verify_data(form,company_name)
{
	if (form.organization_name.value == "" )
	{
		{/literal}
								alert(company_name +"{$APP.CANNOT_BE_NONE}");
								form.organization_name.focus();
								return false;
								{literal}
	}
	else if (form.organization_name.value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0)
	{
	{/literal}
								alert(company_name +"{$APP.CANNOT_BE_EMPTY}");
								form.organization_name.focus();
								return false;
								{literal}
	}
else if (! upload_filter("binFile","png|jpg|jpeg|JPG|JPEG"))
{
								form.binFile.focus();
								return false;
				}
				 else if (! upload_filter("binFrontFile","png|jpg|jpeg|JPG|JPEG"))
				{
							 form.binFrontFile.focus();
							 return false;
				}
				else if (! upload_filter("binFaviconFile","png|jpg|jpeg|JPG|JPEG"))
				{
								form.binFaviconFile.focus();
								return false;
				}
	else
	{
		return true;
	}
}
</script>
{/literal}
