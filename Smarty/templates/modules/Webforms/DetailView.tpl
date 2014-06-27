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
{include file='modules/Webforms/Buttons_List.tpl'}
<script type="text/javascript" src="modules/{$MODULE}/language/{$LANGUAGE}.lang.js"></script>
<script type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
	<tr>
		<td class="showPanelBg" valign="top" width="100%">
			<div id="orgLay" class="layerPopup" style="display: none;position: absolute;top: 25%;left: 30%;height:70%;width:50%;z-index:100; " >
				<table class="layerHeadingULine" cellspacing="0" cellpadding="5" border="0" width="100%">
					<tr>
						<td class="genHeaderSmall" align="left">
							<img src="modules/Webforms/img/Webform_small.png">
							<p id="webform_popup_header" style="display:inline;"> {$WEBFORMMODEL->getName()}</p>
						</td>
						<td align="right">
							<a >
							<img border="0" align="absmiddle" src="themes/images/close.gif" onclick="Webforms.showHideElement('orgLay')">
							</a>
						</td>
					</tr>
				</table>
				<table cellspacing="0" cellpadding="0" border="0" align="center" width="95%" >
						<tr>
							<td class="small">
								<table cellpadding="5" border="0" bgcolor="white" align="center" width="100%"  celspacing="0">
									<tr>
										<td id="webform_source_description"></td>
									</tr>
									<tr>
										<td>
											<font color="green" >{'LBL_EMBED_MSG'|@getTranslatedString:$MODULE }</font>
										</td>
									</tr>
									<tr>
										<td rowspan="5">
											<textarea  readonly="readonly" rows="25" cols="25" style="height:auto;" id="webform_source" name="webform_source" value=""></textarea>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td align="right">
								<input type="button" style="width:70px" value="{'LBL_CANCEL_BUTTON_LABEL'|@getTranslatedString:$MODULE} " name="button" onclick="Webforms.showHideElement('orgLay')" class="crmbutton small cancel" >
							</td>
						</tr>
				</table>
			</div>
			<div class="small" style="padding:20px">
					<span class="lvtHeaderText"> {$WEBFORMMODEL->getName()}</span> <br>
				<hr noshade="noshade" size="1">
				<br>

				<form name="action_form" action="" method="post">
					<input type="hidden" name="id" value="{$WEBFORMMODEL->getId()}"></input>
				</form>
				<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%">
				<tr>
					<td>
						<table class="small" border="0" cellpadding="3" cellspacing="0" width="100%">
							<tr>
								<td class="dvtTabCache" style="width:10px" nowrap="nowrap">&nbsp;</td>
								<td class="dvtSelectedCell" nowrap="nowrap" align="center">Basic Information</td>
								<td class="dvtTabCache" style="width:65%">&nbsp;</td>
								<td align="right">
									<input type="button" id="edit_form" name="edit_form" value="{'LBL_EDIT_BUTTON_LABEL'|@getTranslatedString:$MODULE} " class="crmbutton small edit" onclick="Webforms.editForm({$WEBFORMMODEL->getId()})"></input>
									<input type="button" id="show_html" name="show_html" value="{'LBL_SOURCE'|@getTranslatedString:$MODULE}" class="crmbutton small create" onclick="Webforms.getHTMLSource({$WEBFORMMODEL->getId()})"></input>
									<input type="button" id="delete_form" name="delete_form" value="{'LBL_DELETE_BUTTON_LABEL'|@getTranslatedString:$MODULE} " class="crmbutton small delete" onclick="return Webforms.deleteForm('action_form',{$WEBFORMMODEL->getId()})"></input>
								</td>
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
											<table   class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
												<!--Block Head-->
												<tr>
													<td colspan={if $WEBFORMMODEL->hasId()}"3"{else}"4"{/if} class="detailedViewHeader">
														<b>{'LBL_MODULE_INFORMATION'|@getTranslatedString:$MODULE}</b>
													</td>
													<td  colspan="1" class="detailedViewHeader" align="right">
														{'LBL_ENABLED'|@getTranslatedString:$MODULE}
														{if $WEBFORMMODEL->getEnabled() eq 1}
															<img src="themes/images/prvPrfSelectedTick.gif">
														{else}
															<img src="themes/images/no.gif">
														{/if}
													</td>

												</tr>
												<!-- Cell information  -->
												<tr style="height:25px">
													<td class="dvtCellLabel" align="right" width="10%">
														<font color="red">*</font>{'LBL_WEBFORM_NAME'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" align="left" width="40%">
														{$WEBFORMMODEL->getName()}
													</td>
													<td class="dvtCellLabel" align="right" width="10%">
														<font color="red">*</font>{'LBL_MODULE'|@getTranslatedString:$MODULE} :
													</td>
													<td class="dvtCellInfo" align="left" width="40%">
														{$WEBFORMMODEL->getTargetModule()}
													</td>
												</tr>
												<tr style="height:25px">
													<td class="dvtCellLabel" align="right" >
														<font color="red">*</font>{'LBL_ASSIGNED_TO'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" align="left" >
														{$OWNER}
													</td>
													<td class="dvtCellLabel" align="right" >
														{'LBL_RETURNURL'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" align="left" >
														http://{$WEBFORMMODEL->getReturnUrl()}
													</td>
												</tr>
												<tr style="height:25px;">
													<td class="dvtCellLabel" align="right" >
														{'LBL_PUBLICID'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" align="left" >
														{$WEBFORMMODEL->getPublicId()}
													</td>
													<td class="dvtCellLabel" align="right" >
														{'LBL_POSTURL'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" align="left" >
														{$ACTIONPATH}
													</td>
												</tr>

												<tr>
													<td class="dvtCellLabel" align="right" style="height:25px;">
														{'LBL_DESCRIPTION'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" align="left" >
														{$WEBFORMMODEL->getDescription()
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
												</tr>
												<tr><td>&nbsp;</td></tr>
	<!-- Cell information for fields -->
												<tr>
													<td class="detailedViewHeader" colspan="4">
														<b>{'LBL_FIELD_INFORMATION'|@getTranslatedString:$MODULE}</b>
													</td>
												</tr>
												<tr >
													<td colspan="4"  >
														<div id="Webforms_FieldsView"></div>
<!--Fields View-->
														<table id="field_table" class="small" border="0" cellpadding="5" cellspacing="1" width="100%">
															<tr>
																<td style="height:25px;" class="lvtCol">{'LBL_FIELDLABEL'|@getTranslatedString:$MODULE}</td>
																<td style="height:25px;" class="lvtCol">{'LBL_DEFAULT_VALUE'|@getTranslatedString:$MODULE}</td>
																<td style="width:2%;height:25px;" class="lvtCol">{'LBL_REQUIRED'|@getTranslatedString:$MODULE}</td>
																<td style="height:25px;" class="lvtCol" style="width:20%;">{'LBL_NEUTRALIZEDFIELD'|@getTranslatedString:$MODULE}</td>
															</tr>
															{foreach item=field from=$WEBFORMMODEL->getFields() name=fieldloop}
															{assign var=fieldinfo value=$WEBFORM->getFieldInfo($WEBFORMMODEL->getTargetModule(), $field->getFieldName())}
															{if $WEBFORMMODEL->isActive($fieldinfo.name,$WEBFORMMODEL->getTargetModule())}
																<tr style="height:25px" id="field_row">
																	<td class="dvtCellLabel" align="left" colspan="1">
																	{if $fieldinfo.mandatory eq 1}
																		<font color="red">*</font>
																	{/if}
																		{$fieldinfo.label}
																	</td>
																	<td class="dvtCellInfo">
																		{assign var="defaultvalueArray" value=$WEBFORMMODEL->retrieveDefaultValue($WEBFORMMODEL->getId(),$fieldinfo.name)}
																		{if $fieldinfo.type.name eq 'boolean'}
																			{if $defaultvalueArray[0] eq 'off'}
																				no
																			{elseif $defaultvalueArray[0] eq 'on'}
																				yes
																			{/if}
																		{else}

																		{','|implode:$defaultvalueArray}
																		{/if}
																	</td>
																	<td class="dvtCellInfo" align="center" colspan="1">
																		{if  $WEBFORMMODEL->isRequired($WEBFORMMODEL->getId(),$fieldinfo.name) eq true}
																			<img src="themes/images/prvPrfSelectedTick.gif">
																		{else}
																			<img src="themes/images/no.gif">
																		{/if}
																	</td>
																	<td class="dvtCellLabel" align="left" colspan="1">
																		{if $WEBFORMMODEL->isCustomField($fieldinfo.name) eq true}
																			label:{$fieldinfo.label}
																		{else}
																			{$fieldinfo.name}
																		{/if}
																	</td>
																</tr>
															{/if}
														{/foreach}
														</table>
<!--Fields view ends here-->
													</td>
												</tr>
	<!--Cell Information end-->
												<tr style="height:25px">
													<td>&nbsp;</td>
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
