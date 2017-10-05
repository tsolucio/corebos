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

										<!-- DISPLAY Field Access Settings-->
															<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz" style="background-color: #f7f9fb;">
																<form action="index.php" method="post" name="new" id="form" onsubmit="VtigerJS_DialogBox.block();">
																	<input type="hidden" name="module" value="Settings">
																	<input type="hidden" name="parenttab" value="Settings">
																	<input type="hidden" name="fld_module" id="fld_module" value="{$DEF_MODULE}">

																	{if $MODE neq 'view'}
																		<input type="hidden" name="action" value="UpdateDefaultFieldLevelAccess">
																	{else}
																		<input type="hidden" name="action" value="EditDefOrgFieldLevelAccess">
																	{/if}

																<tr class="slds-text-title--caps">
																	<td style="padding: 0;">
																		<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilSettings" style="height: 70px;">
																			<div class="slds-grid primaryFieldRow" style="transform: translate3d(0, -8.65823px, 0);">
																				<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
																					<div class="slds-media slds-no-space" style="transform: scale3d(0.864715, 0.864715, 1) translate3d(4.32911px, 2.16456px, 0);">
																						<div class="slds-media__figure slds-icon forceEntityIcon">
																							<span class="photoContainer forceSocialPhoto">
																								<div class="small roundedSquare forceEntityIcon">
																									<span class="uiImage">
																										<img src="{'orgshar.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_MODULE_NAME}" title="{$MOD.LBL_MODULE_NAME}">
																									</span>
																								</div>
																							</span>
																						</div>
																					</div>
																					<div class="slds-media__body">
																						<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
																							<span class="uiOutputText">
																								<b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > {$MOD.LBL_FIELDS_ACCESS} </b>
																							</span>
																							<span class="small">{$MOD.LBL_SHARING_FIELDS_DESCRIPTION}</span>
																						</h1>
																					</div>
																				</div>
																			</div>
																		</div>
																	</td>
																</tr>
															</table>

															<br>

															<table border=0 cellspacing=0 cellpadding=5 width=100% >
																<tr>
																	<td>

																		<div class="forceRelatedListSingleContainer">
																			<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																				<div class="slds-card__header slds-grid">
																					<header class="slds-media slds-media--center slds-has-flexi-truncate">
																						<div class="slds-media__body">
																							<h2>
																								<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																									<strong>{'LBL_GLOBAL_FIELDS_MANAGER'|@getTranslatedString:'Users'}</strong>
																								</span>
																							</h2>
																						</div>
																					</header>
																					<div class="slds-no-flex">
																						<div class="actionsContainer">
																							{if $MODE neq 'edit'}
																								<input name="Edit" type="submit" class="slds-button slds-button--small slds-button--brand" value="{$APP.LBL_EDIT_BUTTON}" >
																							{else}
																								<input title="save" accessKey="S" class="slds-button slds-button--small slds-button_success" type="submit" name="Save" value="{$APP.LBL_SAVE_LABEL}">
																								<input name="Cancel" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="slds-button slds-button--small slds-button--destructive" type="button" onClick="window.history.back();">
																							{/if}
																						</div>
																					</div>
																				</div>
																			</article>
																		</div>

																		<div class="slds-truncate" style="padding: 5px;">
																			<table class="slds-table slds-table--bordered slds-no-row-hover slds-no-padding">
																				<tr class="slds-line-height--reset">
																					<td class="dvtCellLabel">
																						<strong>{'LBL_SELECT_SCREEN'|@getTranslatedString:'Users'}</strong>
																					</td>
																					<td class="dvtCellInfo">
																						<select name="Screen" class="slds-select" style="width:50%" onChange="changemodules(this)">
																							{foreach item=module from=$FIELD_INFO}
																								{assign var="MODULELABEL" value=$module|@getTranslatedString:$module}
																								{if $module == $DEF_MODULE}
																									<option selected value='{$module}'>{$MODULELABEL}</option>
																								{else}
																									<option value='{$module}' >{$MODULELABEL}</option>
																								{/if}
																							{/foreach}
																						</select>
																					</td>
																				</tr>
																			</table>
																		</div>
															{foreach key=module item=info name=allmodules from=$FIELD_LISTS}
																{assign var="MODULELABEL" value=$module|@getTranslatedString:$module}
																{if $module eq $DEF_MODULE}
																	<div class="slds-truncate" id="{$module}_fields" style="display:block">
																{else}
																	<div class="slds-truncate" id="{$module}_fields" style="display:none">
																{/if}

																		<table class="slds-table slds-no-row-hover slds-table--cell-buffer">
																			<tr class="slds-line-height--reset">
																				<td colspan="8" valign="top">
																					<div class="forceRelatedListSingleContainer">
																						<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																							<div class="slds-card__header slds-grid">
																								<header class="slds-media slds-media--center slds-has-flexi-truncate">
																									<div class="slds-media__body">
																										<h2>
																											<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																												<b>{'LBL_FIELDS_AVLBL'|@getTranslatedString:'Users'} {$MODULELABEL}</b>
																											</span>
																										</h2>
																									</div>
																								</header>
																							</div>
																						</article>
																					</div>
																					<table class="slds-table">
																						{foreach item=elements name=groupfields from=$info}
																							<tr class="slds-line-height--reset">
																								{foreach item=elementinfo name=curvalue from=$elements}
																									<!-- <td class="prvPrfTexture" style="width:20px">&nbsp;</td>
																									<td class="dvtCellLabel" width="5%" id="{$smarty.foreach.allmodules.iteration}_{$smarty.foreach.groupfields.iteration}_{$smarty.foreach.curvalue.iteration}">{$elementinfo.1}</td>
																									<td class="dvtCellInfo" width="25%" nowrap  onMouseOver="this.className='prvPrfHoverOn',document.getElementById('{$smarty.foreach.allmodules.iteration}_{$smarty.foreach.groupfields.iteration}_{$smarty.foreach.curvalue.iteration}').className='prvPrfHoverOn'" onMouseOut="this.className='prvPrfHoverOff',document.getElementById('{$smarty.foreach.allmodules.iteration}_{$smarty.foreach.groupfields.iteration}_{$smarty.foreach.curvalue.iteration}').className='prvPrfHoverOff'">{$elementinfo.0}</td> -->
																									<td class="dvtCellLabel" width="5%" id="{$smarty.foreach.allmodules.iteration}_{$smarty.foreach.groupfields.iteration}_{$smarty.foreach.curvalue.iteration}">
																										{$elementinfo.1}
																									</td>
																									<td class="dvtCellInfo" width="25%">{$elementinfo.0}</td>
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


														<!-- </td></tr></table> -->

												<table border=0 cellspacing=0 cellpadding=5 width=100% >
													<tr>
														<td class="small" >
															<div align=right><a href="#top">{$MOD.LBL_SCROLL}</a></div>
														</td>
													</tr>
												</table>
											</td><!-- /.settingsSelectedUI from SetMenu.tpl-->
										</tr>
									</table><!-- 2nd table from SetMenu.tpl -->
								</td>
							</tr>
						</form>
					</table><!-- 1st table from SetMenu.tpl -->
				</div><!-- /aling=center -->
			</td>
		</tr>
	</tbody>
</table>

<script>
var def_field='{$DEF_MODULE}_fields';
{literal}
function changemodules(selectmodule) {
	hide(def_field);
	module=selectmodule.options[selectmodule.options.selectedIndex].value;
	document.getElementById('fld_module').value = module;
	def_field = module+"_fields";
	show(def_field);
}
</script>
{/literal}
