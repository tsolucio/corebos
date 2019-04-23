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

<!-- header - level 2 tabs -->
{include file='Buttons_List.tpl'}

<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tr>
		<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}" /></td>
		<td class="showPanelBg" valign="top" width="100%">
			<table  cellpadding="0" cellspacing="0" width="100%" border=0>
				<tr>
					<td width="50%" valign=top>
						<form  name="Export_Records"  method="POST" onsubmit="VtigerJS_DialogBox.block();">
							<input type="hidden" name="module" value="{$MODULE}">
							<input type="hidden" name="action" value="Export">
							<input type="hidden" name="idstring" value="{if isset($IDSTRING)}{$IDSTRING}{/if}">
							<table align="center" cellpadding="15" cellspacing="0" width="85%" class="mailClient importLeadUI small" border="0">
								<tr>
									<td colspan="2" valign="middle" align="left" class="mailClientBg genHeaderSmall cblds-p_x-large">{$MODULELABEL} >> {$APP.LBL_EXPORT} </td>
									<br>
								</tr>
								<tr>
									<td border="0" cellpadding="5" cellspacing="0" width="50%" class="cblds-p_large">
										<table>
											<tr>
												<td colspan="2" align="left" valign="top" style="padding-left:40px;">
													<span class="genHeaderSmall">{$APP.LBL_SEARCH_CRITERIA_RECORDS}:</span>
												</td>
											</tr>
											<tr>
												{if $SESSION_WHERE neq ''}
												<td align="right" valign="top" width="50%" class="small cblds-p_large cblds-t-align_right">{$APP.LBL_WITH_SEARCH}</td>
												<td align="left" valign="top" width="5%" class="small cblds-p_large">
													<input type="radio" name="search_type" checked value="includesearch" {if $MODULE eq 'Users'}disabled{/if}>
												</td>
												{else}
												<td align="right" valign="top" width="50%" class="small cblds-p_large cblds-t-align_right">{$APP.LBL_WITH_SEARCH}</td>
												<td align="left" valign="top" width="5%" class="small cblds-p_large">
													<input type="radio" name="search_type" value="includesearch" {if $MODULE eq 'Users'}disabled{/if}>
												</td>
												{/if}
											</tr>
											<tr>
												{if $SESSION_WHERE eq ''}
												<td align="right" valign="top" width="50%" class="small cblds-p_large cblds-t-align_right">{$APP.LBL_WITHOUT_SEARCH}</td>
												<td align="left" valign="top" width="5%" class="small cblds-p_large">
													<input type="radio" name="search_type" checked value="withoutsearch">
												</td>
												{else}
												<td align="right" valign="top" width="50%" class="small cblds-p_large cblds-t-align_right">{$APP.LBL_WITHOUT_SEARCH}</td>
												<td align="left" valign="top" width="5%" class="small cblds-p_large">
													<input type="radio" name="search_type" value="withoutsearch">
												</td>
												{/if}
											</tr>
											<tr>
												<td colspan="2" align="left" valign="top" style="padding-left:40px;">
													<span class="genHeaderSmall">{$APP.LBL_EXPORT_RECORDS}:</span>
												</td>
											</tr>
											<tr>
												{if empty($IDSTRING)}
												<td align="right" valign="top" width="50%" class="small cblds-p_large cblds-t-align_right">{$APP.LBL_ALL_DATA}</td>
												<td align="left" valign="top" width="5%" class="small cblds-p_large">
													<input type="radio" name="export_data" checked value="all">
												</td>
												{else}
												<td align="right" valign="top" width="50%" class="small cblds-p_large cblds-t-align_right">{$APP.LBL_ALL_DATA}</td>
												<td align="left" valign="top" width="5%" class="small cblds-p_large">
													<input type="radio" name="export_data" value="all">
												</td>
												{/if}
											</tr>
											<tr>
												<td align="right" valign="top" width="50%" class="small cblds-p_large cblds-t-align_right">{$APP.LBL_DATA_IN_CURRENT_PAGE}</td>
												<td align="left" valign="top" width="5%" class="small cblds-p_large">
													<input type="radio" name="export_data" value="currentpage" {if $MODULE eq 'Users'}disabled{/if}>
												</td>
											</tr>
											<tr>
												{if !empty($IDSTRING)}
													<td align="right" valign="top" width="50%" class="small cblds-p_large cblds-t-align_right">{$APP.LBL_ONLY_SELECTED_RECORDS}</td>
												<td align="left" valign="top" width="5%" class="small cblds-p_large">
													<input type="radio" name="export_data" checked value="selecteddata" {if $MODULE eq 'Users'}disabled{/if}>
												</td>
												{else}
												<td align="right" valign="top" width="50%" class="small cblds-p_large cblds-t-align_right">{$APP.LBL_ONLY_SELECTED_RECORDS}</td>
												<td align="left" valign="top" width="5%" class="small cblds-p_large">
													<input type="radio" name="export_data" value="selecteddata" {if $MODULE eq 'Users'}disabled{/if}>
												</td>
												{/if}
											</tr>
										</table>
									</td>
									<td border="0" cellpadding="5" cellspacing="0" width="50%">
										<table >
											<tr>
												<td><div id="not_search" style="position:absolute;display:none;width:400px;height:25px;"></div></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td align="center" colspan="2" border=0 cellspacing=0 cellpadding=5 width=98% class="layerPopupTransport cblds-p_large cblds-t-align_center">
										<input type="button" name="{$APP.LBL_EXPORT}" value="{$APP.LBL_EXPORT} {$MODULELABEL} " class="crmbutton small create" onclick="record_export('{$MODULELABEL}','{$CATEGORY}',this.form,'{if isset($smarty.request.idstring)}{$smarty.request.idstring}{/if}')"/>&nbsp;&nbsp;
										<input type="button" name="{$APP.LBL_CANCEL_BUTTON_LABEL}" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="crmbutton small cancel" onclick="window.history.back()" />
									</td>
								</tr>
							</table>
						</form>
					</td>
				</tr>
			</table>
		</td>
		<td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}" /></td>
	</tr>
</table>

