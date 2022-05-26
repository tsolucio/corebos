{*<!--
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
-->*}
{include file='Buttons_List.tpl'}
<script type="text/javascript" src="modules/{$MODULE}/language/{$LANGUAGE}.lang.js"></script>
<script type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>
<script type="text/javascript">
	{if $WEBFORM->hasId()}
		var mode="edit";
	{else}
		var mode="save";
	{/if}
</script>
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
	<tr>
		<td class="showPanelBg" valign="top" width="100%">
			<div class="small" style="padding:20px">
				{if $WEBFORM->hasId()}
					<span class="lvtHeaderText">{'LBL_EDIT'|@getTranslatedString} : {$WEBFORM->getName()}</span> <br>
				{else}
					<span class="lvtHeaderText">{'LBL_CREATE'|@getTranslatedString} {$MODULE}</span> <br>
				{/if}
				<hr noshade="noshade" size="1">
				<br>
				<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%">
				<tr>
					<td>
						<table class="small" border="0" cellpadding="3" cellspacing="0" width="100%">
							<tr>
								<td class="dvtTabCache" style="width:10px" nowrap="nowrap">&nbsp;</td>
								<td class="dvtSelectedCell" nowrap="nowrap" align="center">{'LBL_MODULE_INFORMATION'|@getTranslatedString:$MODULE}</td>
								<td class="dvtTabCache" style="width:65%">&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td align="left" valign="top">

					<!-- Basic Information Tab Opened -->
					<div id="basicTab">
					<table class="dvtContentSpace" border="0" cellpadding="3" cellspacing="0" width="100%">
						<tr>
							<td align="left">
							<!-- content cache -->
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td id="autocom"></td>
									</tr>
									<tr>
										<td style="padding:10px">
										<!-- General details -->
										<form name="webform_edit" id="webform_edit" action="index.php?module=Webforms&action=Save" method="post">
											{if $WEBFORM->hasId()}
											<input type="hidden" name="id" value={$WEBFORM->getId()}>
											{/if}
											<table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td colspan="4" style="padding:5px">
														<div align="center" >
														<input title="{'LBL_SAVE_BUTTON_TITLE'|@getTranslatedString:$MODULE}" accesskey="{'LBL_SAVE_BUTTON_KEY'|@getTranslatedString:$MODULE}" class="crmbutton small save" onclick="javascript:return Webforms.validateForm('webform_edit','index.php?module=Webforms&action=Save')" name="button" value="{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString:$MODULE} " style="width:70px" type="submit">
														<input title="{'LBL_CANCEL_BUTTON_TITLE'|@getTranslatedString:$MODULE}" accesskey="{'LBL_CANCEL_BUTTON_KEY'|@getTranslatedString:$MODULE}" class="crmbutton small cancel" onclick="window.history.back()" name="button" value="{'LBL_CANCEL_BUTTON_LABEL'|@getTranslatedString:$MODULE}" style="width:70px" type="button">
														</div>
													</td>
												</tr>
												<!--Block Head-->
												<tr>
													<td colspan={if $WEBFORM->hasId()}"3"{else}"4"{/if} class="detailedViewHeader">
														<b>{'LBL_MODULE_INFORMATION'|@getTranslatedString:$MODULE}</b>
													</td>
													{if $WEBFORM->hasId()}
													<td colspan="1" class="detailedViewHeader" align="right">
														{'LBL_ENABLE'|@getTranslatedString:$MODULE}
														{if $WEBFORM->getEnabled() eq 1}
															<input type="checkbox" name="enabled" id="enabled" checked="checked">
														{else}
															<input type="checkbox" name="enabled" id="enabled" >
														{/if}
													</td>
													{/if}
												</tr>
												<!-- Cell information -->
												<tr style="height:25px">
													<td class="dvtCellLabel" align="right" width="10%" nowrap="nowrap">
														<font color="red">*</font>{'LBL_WEBFORM_NAME'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" align="left" width="40%">
														<input type="text" onblur="this.className='detailedViewTextBox';" onfocus="this.className='detailedViewTextBoxOn';" class="detailedViewTextBox" id="name" name="name" value="{$WEBFORM->getName()}" {if $WEBFORM->hasId()}readonly="readonly"{/if}>
													</td>
													<td class="dvtCellLabel" align="right" width="10%" nowrap="nowrap">
														<font color="red">*</font>{'LBL_MODULE'|@getTranslatedString:$MODULE} :
													</td>
													<td class="dvtCellInfo" align="left" width="40%">
														{if $WEBFORM->hasId()}
															{$WEBFORM->getTargetModule()}
															<input type="hidden" value="{$WEBFORM->getTargetModule()}" name="targetmodule" id="targetmodule">
														{else}
															<select id="targetmodule" name="targetmodule" onchange='javascript:Webforms.fetchFieldsView(this.value);' class="small">
																<option value="">--{'LBL_MODULE'|@getTranslatedString}--</option>
																 {foreach item=module from=$WEBFORMMODULES name=moduleloop}
																	<option value="{$module}">{$module|@getTranslatedString:$module}</option>
																{/foreach}
															</select>
														{/if}
													</td>
												</tr>
												<tr style="height:25px">
													<td class="dvtCellLabel" align="right" >
														<font color="red">*</font>{'LBL_ASSIGNED_TO'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" align="left" >
														{if $usr_selected eq 1}
															{assign var=select_user value='checked'}
															{assign var=select_group value=''}
															{assign var=style_user value='display:block'}
															{assign var=style_group value='display:none'}
														{else}
															{assign var=select_user value=''}
															{assign var=select_group value='checked'}
															{assign var=style_user value='display:none'}
															{assign var=style_group value='display:block'}
														{/if}
														<input type="radio" name="assigntype" {$select_user} value="U" onclick="toggleAssignType(this.value);jQuery('#ownerid').val(jQuery('#assigned_user_id').val());" >&nbsp;{$APP.LBL_USER}
														<input type="radio" name="assigntype" {$select_group} value="T" onclick="toggleAssignType(this.value);jQuery('#ownerid').val(jQuery('#assigned_group_id').val());">&nbsp;{$APP.LBL_GROUP}
														<input type="hidden" name="ownerid" id="ownerid" value="{$WEBFORM->getOwnerId()}">
														<span id="assign_user" style="{$style_user}">
															<select name="assigned_user_id" id="assigned_user_id" class="small" onchange="jQuery('#ownerid').val(jQuery('#assigned_user_id').val());">
															{foreach key=userid item=username name=assigned_user from=$USERS}
																<option value="{$userid}" {if !empty($WEBFORMID) && $userid eq $WEBFORM->getOwnerId()} selected {/if}>{$username}</option>
															{/foreach}
															</select>
														</span>
														<span id="assign_team" style="{$style_group}">
															<select name="assigned_group_id" id="assigned_group_id" class="small" onchange="jQuery('#ownerid').val(jQuery('#assigned_group_id').val());">
															{foreach key=userid item=username name=assigned_user from=$GROUPS}
																<option value="{$userid}" {if !empty($WEBFORMID) && $userid eq $WEBFORM->getOwnerId()} selected {/if}>{$username}</option>
															{/foreach}
															</select>
														</span>
													</td>
													<td class="dvtCellLabel" align="right" >
														{'LBL_RETURNURL'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" align="left" >
														<input type="text" onblur="this.className='detailedViewTextBox';" onfocus="this.className='detailedViewTextBoxOn';" class="detailedViewTextBox" id="returnurl" name="returnurl" value="{$WEBFORM->getReturnUrl()}">
													</td>
												</tr>
												{if $WEBFORM->hasId()}
												<tr style="height:25px;">
													<td class="dvtCellLabel" align="right" >
														{'LBL_PUBLICID'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" align="left" >
														{$WEBFORM->getPublicId()}
													</td>
													<td class="dvtCellLabel" align="right" >
														{'LBL_POSTURL'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" align="left" >
														{$ACTIONPATH}
													</td>
												</tr>
												{/if}
												<tr style="height:25px">
													<td class="dvtCellLabel" align="right" >
														<font color="red">*</font>{'LBL_WEB_DOMAIN'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" align="left" >
														<input type="text" onblur="this.className='detailedViewTextBox';" onfocus="this.className='detailedViewTextBoxOn';" class="detailedViewTextBox" id="web_domain" name="web_domain" value="{$WEBFORM->getWebDomain()}">
													</td>
												</tr>
												<!--Cell Information end-->
												<tr style="height:25px">
													<td>&nbsp;</td>
												</tr>
												<!--Cell Description Information-->
												<tr>
													<td colspan="4" class="detailedViewHeader">
														<b>{'LBL_DESCRIPTION'|@getTranslatedString:$MODULE}</b>
													</td>
												</tr>
												<tr>
													<td class="dvtCellLabel" align="right" colspan="1">
														{'LBL_DESCRIPTION'|@getTranslatedString:$MODULE}
													</td>
													<td colspan="3">
														<textarea onblur="this.className='detailedViewTextBox';" onfocus="this.className='detailedViewTextBoxOn';" class="detailedViewTextBox" rows="8" cols="90" onblur="this.className='detailedViewTextBox'" name="description" id="description" onfocus="this.className='detailedViewTextBoxOn'" class="detailedViewTextBox" >{if $WEBFORM->hasId()}{$WEBFORM->getDescription()}{/if}</textarea>
													</td>
												</tr>
												<!--Cell Information end-->
												<tr style="height:25px">
													<td>&nbsp;</td>
												</tr>
												<!--Block Head-->
												<tr>
													<td colspan="3" class="detailedViewHeader">
														<b>{'LBL_FIELD_INFORMATION'|@getTranslatedString:$MODULE}</b>
													</td>
													<td colspan="1" class="detailedViewHeader" align="right">
													</td>
												</tr>
	<!-- Cell information for fields -->
												<tr >
													<td colspan="4">
														<div id="Webforms_FieldsView"></div>
														{if $WEBFORM->hasId()}{include file="modules/Webforms/FieldsView.tpl"}{/if}
													</td>
												</tr>
	<!--Cell Information end-->
												<tr style="height:25px">
													<td>&nbsp;</td>
												</tr>
												<tr>
													<td colspan="4" style="padding:5px">
														<div align="center" >
														<input title="{'LBL_SAVE_BUTTON_TITLE'|@getTranslatedString:$MODULE}" accesskey="{'LBL_SAVE_BUTTON_KEY'|@getTranslatedString:$MODULE}" class="crmbutton small save" onclick="javascript:return Webforms.validateForm('webform_edit','index.php?module=Webforms&action=Save')" name="button" value="{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString:$MODULE} " style="width:70px" type="submit">
														<input title="{'LBL_CANCEL_BUTTON_TITLE'|@getTranslatedString:$MODULE}" accesskey="{'LBL_CANCEL_BUTTON_KEY'|@getTranslatedString:$MODULE}" class="crmbutton small cancel" onclick="window.history.back()" name="button" value="{'LBL_CANCEL_BUTTON_LABEL'|@getTranslatedString:$MODULE}" style="width:70px" type="button">
														</div>
													</td>
												</tr>
											</table>
										</form>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
		<!-- Basic Information Tab Closed -->
			</td>
		</tr>
	</table>
	</form></div>
	</td>
	<td align="right" valign="top"><img src="themes/softed/images/showPanelTopRight.gif"></td>
</tr>
</table>
