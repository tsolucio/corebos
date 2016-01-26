{*<!--
/***********************************************************************************************
** The contents of this file are subject to the Vtiger Module-Builder License Version 1.0
 * ( "License" ); You may not use this file except in compliance with the License
 * The Original Code is:  Technokrafts Labs Pvt Ltd
 * The Initial Developer of the Original Code is Technokrafts Labs Pvt Ltd.
 * Portions created by Technokrafts Labs Pvt Ltd are Copyright ( C ) Technokrafts Labs Pvt Ltd.
 * All Rights Reserved.
**
*************************************************************************************************/
-->*}

			<form action="index.php" method="post" name="form" onSubmit="VtigerJS_DialogBox.block();">
				<input type="hidden" name="fld_module" value="{$MODULE}">
				<input type="hidden" name="module" value="ModuleBuilder">
				<input type="hidden" name="parenttab" value="Tools">
				<input type="hidden" name="mode">
				<script language="JavaScript" type="text/javascript" src="include/js/customview.js"></script>
				<script language="JavaScript" type="text/javascript" src="include/js/general.js"></script>
						
				<table class="listTable" border="0" cellpadding="3" cellspacing="0" width="100%">
				
						{foreach key=id item=blockname from=$BLOCKS}
						<tr>
						<td  class="colHeader small" colspan="2">
								
								&nbsp;&nbsp;{$blockname}&nbsp;&nbsp;
				  		</td>
							<td class="colHeader small"  id = "blockid"_{$id} colspan="2" align='right'> 
							<img style="cursor:pointer;" onClick="{if empty($FIELDS.$id)} deleteCustomBlock('{$MODULE}','{$id}'); {else} alert(alert_arr.TKS_CAN_NOT_DELETE_BLOCK_AS_IT_CONTAINS_FIELDS);{/if}" src="{'delete.gif'|@vtiger_imageurl:$THEME}" border="0"  alt="Delete" title="Delete"/>&nbsp;&nbsp;
							
						<img src="{'plus_layout.gif'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;"  onclick="fnvshobj(this,'addfield_{$id}'); " alt="{$MOD.LBL_ADD_CUSTOMFIELD}" title="{$MOD.LBL_ADD_CUSTOMFIELD}"/>&nbsp;&nbsp;
						
						<!-- for adding customfield -->
												<div id="addfield_{$id}" style="display:none; position:absolute; width:500px;" class="layerPopup">
												  	<input type="hidden" name="mode" id="cfedit_mode" value="add">
	  												<table width="100%" border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
														<tr>
															<td width="60%" align="left" class="layerPopupHeading">{$MOD.LBL_ADD_FIELD}
															</td>
															<td width="40%" align="right"><a href="javascript:fninvsh('addfield_{$id}');">
															<img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0"  align="absmiddle" /></a>
															</td>
														</tr>
													</table>
													<table border="0" cellspacing="0" cellpadding="5" width="95%" align="center"> 
														<tr>
															<td class="small" >
																<table border="0" celspacing="0" cellpadding="5" width="100%" align="center" bgcolor="white">
																	<tr>
																		<td>
																			<table>
																				<tr>
																					<td>{$APP.LBL_SELECT_FIELD_TYPE}
																					</td>
																				</tr>
																				<tr>
																					<td>
																						<div name="cfcombo" id="cfcombo" class="small"  style="width:205px; height:250px; overflow-y:auto ;overflow-x:hidden ;overflow:auto; border:1px  solid #CCCCCC ;">
																							<table>
																								<tr><td align="left"><a id="field0_{$id}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'text.gif'|@vtiger_imageurl:$THEME});" 		onclick = "makeFieldSelected(this,0,{$id});">  {$MOD.Text} </a></td></tr>
																								<tr><td align="left"><a id="field1_{$id}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'number.gif'|@vtiger_imageurl:$THEME});" 		onclick = "makeFieldSelected(this,1,{$id})" >  {$MOD.Number} </a></td></tr>
																								<tr><td align="left"><a id="field2_{$id}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'number.gif'|@vtiger_imageurl:$THEME});" 		onclick = "makeFieldSelected(this,2,{$id})" >  {$MOD.Decimal} </a></td></tr>
																								<tr><td align="left"><a id="field3_{$id}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'percent.gif'|@vtiger_imageurl:$THEME});" 	onclick = "makeFieldSelected(this,3,{$id});">  {$MOD.Percent} </a></td></tr>
																								<tr><td align="left"><a id="field4_{$id}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'cfcurrency.gif'|@vtiger_imageurl:$THEME});" 	onclick = "makeFieldSelected(this,4,{$id});">  {$MOD.Currency} </a></td></tr>
																								<tr><td align="left"><a id="field5_{$id}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'date.gif'|@vtiger_imageurl:$THEME});" 		onclick = "makeFieldSelected(this,5,{$id});">  {$MOD.Date} </a></td></tr>
																								<tr><td align="left"><a id="field6_{$id}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'email.gif'|@vtiger_imageurl:$THEME});" 		onclick = "makeFieldSelected(this,6,{$id});">  {$MOD.Email} </a></td></tr>
																								<tr><td align="left"><a id="field7_{$id}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'phone.gif'|@vtiger_imageurl:$THEME});" 		onclick = "makeFieldSelected(this,7,{$id});">  {$MOD.Phone} </a>	</td></tr>
																								<tr><td align="left"><a id="field8_{$id}" 	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'cfpicklist.gif'|@vtiger_imageurl:$THEME});" 	onclick = "makeFieldSelected(this,8,{$id});">  {$MOD.PickList} </a></td></tr>
																								<tr><td align="left"><a id="field9_{$id}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'url.gif'|@vtiger_imageurl:$THEME});" 		onclick = "makeFieldSelected(this,9,{$id});">  {$MOD.LBL_URL} </a></td></tr>
																								<tr><td align="left"><a id="field10_{$id}" 	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'checkbox.gif'|@vtiger_imageurl:$THEME});" 	onclick = "makeFieldSelected(this,10,{$id});">  {$MOD.LBL_CHECK_BOX} </a></td></tr>
																								<tr><td align="left"><a id="field11_{$id}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'text.gif'|@vtiger_imageurl:$THEME});" 		onclick = "makeFieldSelected(this,11,{$id});"> {$MOD.LBL_TEXT_AREA} </a></td></tr>
																								<tr><td align="left"><a id="field12_{$id}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'cfpicklist.gif'|@vtiger_imageurl:$THEME});" 	onclick = "makeFieldSelected(this,12,{$id});"> {$MOD.LBL_MULTISELECT_COMBO} </a></td></tr>
																								<tr><td align="left"><a id="field13_{$id}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'skype.gif'|@vtiger_imageurl:$THEME});" 		onclick = "makeFieldSelected(this,13,{$id});"> {$MOD.Skype} </a></td></tr>
                                                                                                <tr><td align="left"><a id="field14_{$id}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'time.PNG'|@vtiger_imageurl:$THEME});" 		onclick = "makeFieldSelected(this,14,{$id});"> {$MOD.Time} </a></td></tr>
																								<tr><td align="left"><a id="field15_{$id}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url(themes/softed/images/btnL3Add.gif);" 		onclick = "makeFieldSelected(this,15,{$id});"> {$MOD.Relate} </a></td></tr>
																							</table>
																						</div>	
																					</td>
																				</tr>
																			</table>
																		</td>				
																		<td width="50%">
																			<table width="100%" border="0" cellpadding="5" cellspacing="0">
																				<tr>
																					<td class="dataLabel" nowrap="nowrap" align="right"><b>{$MOD.LBL_MANDATORY_FIELD}:</b>
																					</td>																					
																					</td>
																					<td align="left">
																					<input type="checkbox" id="mandatory_{$id}" value=""  align="left" class="small">
																					</td>
																				</tr>
																				<tr>
																					<td class="dataLabel" nowrap="nowrap" align="right"><b>{$MOD.LBL_FILTER_FIELD_TKS}:</b>
																					</td>																					
																					</td>
																					<td align="left">
																					<input type="checkbox" id="filter_{$id}" value=""  align="left" class="small">
																					</td>
																				</tr>
																				<tr>
																					<td class="dataLabel" nowrap="nowrap" align="right" width="30%"><b>{$MOD.LBL_LABEL} </b>
																					</td>
																					<td align="left" width="70%">
																					<input id="fldLabel_{$id}"  value="" type="text" class="txtBox">
																					</td>
																				</tr>
																				<tr id="lengthdetails_{$id}">
																					<td class="dataLabel" nowrap="nowrap" align="right"><b>{$MOD.LBL_LENGTH}</b>
																					</td>
																					<td align="left">
																					<input type="text" id="fldLength_{$id}" value="" class="txtBox">
																					</td>
																				</tr>
																				<tr id="relatedmodule_{$id}" style="visibility:hidden;" valign="top">
																					<td class="dataLabel" nowrap="nowrap" align="right"><b>{$MOD.SELECT_MODULE}</b>
																					</td>
																					<td align="left">
																		<select multiple name="module_{$id}" id="module_{$id}" size="5" class="small">
																			{foreach from=$RELATED_MODULES key=k item=v}
																				{assign var=moduletks value=$k|@getTranslatedString:'$MODULE'}
																				<option value="{$k}">{$moduletks}</option>
																			{/foreach}
																		</select>
																					</td>
																				</tr>
																					
																				<tr id="decimaldetails_{$id}" style="visibility:hidden;">
																					<td class="dataLabel_{$id}" nowrap="nowrap" align="right"><b>{$MOD.LBL_DECIMAL_PLACES}</b>
																					</td>
																					<td align="left">
																					<input type="text" id="fldDecimal_{$id}" value=""  class="txtBox">
																					</td>
																				</tr>
																				<tr id="picklistdetails_{$id}" style="visibility:hidden;">
																					<td class="dataLabel" nowrap="nowrap" align="right" valign="top"><b>{$MOD.LBL_PICK_LIST_VALUES}</b>
																					</td>
																					<td align="left" valign="top">
																					<textarea id="fldPickList_{$id}" rows="10" class="txtBox" ></textarea>
																					</td>
																				</tr>
																			</table>
																		</td>
																		
																	</tr>				
																</table>
															</td>
														</tr>
													</table>
													
													<table border="0" cellspacing="0" cellpadding="5" width="100%" class="layerPopupTransport">
														<tr>
															<td align="center">
																<input type="button" name="save" value=" {$APP.LBL_SAVE_BUTTON_LABEL}" class="crmButton small save"  onclick = "getCreateCustomFieldForm('{$MODULE}','{$id}','add');"/>&nbsp;
																<input type="button" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL} " class="crmButton small cancel" onClick="fninvsh('addfield_{$id}');" />
															</td>
														<input type="hidden" name="fieldType_{$id}" id="fieldType_{$id}" value="">
														<input type="hidden" name="selectedfieldtype_{$id}" id="selectedfieldtype_{$id}" value="">
														</tr>
													</table>									
												</div>	
									<!-- end custom field -->
							</td>
							</tr>
							
								{if !empty($FIELDS.$id)}
								<tr>
								{foreach name=inner item=value from=$FIELDS.$id}	
								
									{if $value.sequence % 2 == 0}
								  		</tr>
								  		{assign var="rightcellclass" value=""}
								  		<tr>
								 	{else}
								 		{assign var="rightcellclass" value="class='rightCell'"}
								 	{/if}
										
								<td width="30%" id="colourButton" >&nbsp;
							
							 	<span onmouseover="tooltip.tip(this, showProperties('{$value.fieldlabel}','{$value.fieldtype}','{$value.uitype}'));" onmouseout="tooltip.untip(false);" >{$value.fieldlabel}</span>
							 		{if $value.fieldtype eq 'M'}
							 			<font color='red'> *</font>
							 		{/if}
							 	</td>
								<td width="19%" align = "right" class="colData small" >
								{if $value.fieldname neq ''}
								
									<img src="{'editfield.gif'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="fnvshNrm('editfield_{$value.fieldselect}'); posLay(this, 'editfield_{$value.fieldselect}');" alt="Popup" title="{$MOD.LBL_EDIT_PROPERTIES}"/>&nbsp;&nbsp;
							 	{/if}	
							 		<div id="editfield_{$value.fieldselect}" style="display:none; position: absolute; width: 300px; left: 300px; top: 300px;" >
							 			<div class="layerPopup" style="position:relative; display:block">
		 									<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small">
												<tr class="detailedViewHeader">
													<th width="95%" align="left">
														{$value.fieldlabel}
													</th>
													<th width="5%" align="right">
														<a href="javascript:fninvsh('editfield_{$value.fieldselect}');"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0"  align="absmiddle" /></a>
													</th>
												</tr>
											</table>
											<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small">												
										
												<tr>
													
													<td valign="top" class="dvtCellInfo" align="left" width="40%">
														&nbsp;{$MOD.LBL_MANDATORY_FIELD}
													</td>
													<td valign="top" class="dvtCellInfo" align="left"  width="60%">
														<input id="mandatory_check_{$value.fieldselect}"  type="checkbox"
														{if $value.fieldtype eq 'M' }
															checked 
														{elseif $value.fieldtype neq 'M'}
														 	unchecked 
														 {/if}
													    >
													</td>
												</tr>
												<tr>
													
													<td valign="top" class="dvtCellInfo" align="left" width="40%">
														&nbsp;{$MOD.LBL_FILTER_FIELD_TKS}
													</td>
													<td valign="top" class="dvtCellInfo" align="left"  width="60%">
														<input id="filter_check_{$value.fieldselect}"  type="checkbox"
														{if $value.filter eq 'Yes' }
															checked 
														{elseif $value.filter neq 'Yes'}
														 	unchecked 
														{/if}
													    >
													</td>
													
												</tr>
												<tr>
													<td valign="top" class="dvtCellInfo" align="left">
														&nbsp;Rename Field
													</td>
													<td valign="top" class="dvtCellInfo" align="left" width="10px">
														<input id="rename_fld_{$value.fieldselect}"  class="txtBox" type="text" value="{$value.fieldlabel}">
													</td>
													
												</tr>
												
												<tr>
													<td colspan="3" class="dvtCellInfo" align="center">
														<input  type="button" name="save"  value=" &nbsp; {$APP.LBL_SAVE_BUTTON_LABEL} &nbsp; " class="crmButton small save" onclick="saveFieldInfo('{$value.fieldselect}','{$MODULE}','updateFieldProperties','{$value.typeofdata}');" />&nbsp;
														
															<input type="button" name="delete" value=" {$APP.LBL_DELETE_BUTTON_LABEL} " class="crmButton small delete" onclick="deleteCustomField('{$value.fieldselect}','{$MODULE}','{$value.columnname}','{$value.uitype}')" />
														
														<input  type="button" name="cancel" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="crmButton small cancel" onclick="fninvsh('editfield_{$value.fieldselect}');" />
													</td>
												</tr>
											</table>
										</div>							 		
							 		</div>
									{/foreach}
									
									</tr>
							{/if}
							<tr><td><img border="0" style="width:16px;height:16px;" src="themes/images/blank.gif">&nbsp;&nbsp;</td></tr>
							{/foreach}
									
									
	</table>
	<div id="addblock" style="display:none; position:absolute; width:500px;" class="layerPopup">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
							<tr>
								<td width="95%" align="left" class="layerPopupHeading">{$MOD.LBL_ADD_BLOCK}
								</td>
								<td width="5%" align="right"><a href="javascript:fninvsh('addblock');"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0"  align="absmiddle" /></a>
								</td>
							</tr>
						</table>
						<table border="0" cellspacing="0" cellpadding="0" width="95%" align="center"> 
							<tr>
								<td class="small" >
									<table border="0" celspacing="0" cellpadding="0" width="100%" align="center" bgcolor="white">
										<tr>
											<td width="50%">
												<table width="100%" border="0" cellpadding="5" cellspacing="0">
													<tr>
														<td class="dataLabel" nowrap="nowrap" align="right" width="30%"><b>{$MOD.LBL_BLOCK_NAME}</b></td>
														<td align="left" width="70%">
														<input id="blocklabel" value="" type="text" class="txtBox">
														</td>
													</tr>
													<tr>
													{if $BLOCKS_COUNT eq 1}
														<td class="dataLabel" align="right" width="30%"><b>{$MOD.AFTER}</b></td>
														<td align="left" width="70%">
														<select id="after_blockid" name="after_blockid" class="small">
															{foreach key=blockid item=blockname from=$BLOCKS}
															<option value = {$blockid}> {$blockname} </option>
															{/foreach}
														</select>																
														</td>
													{/if}		
													</tr>	
												</table>
											</td>
										</tr>
									</table>
									<table border=0 cellspacing=0 cellpadding=5 width=100% >
										<tr>
											<td align="center">
												<input type="button" name="save"  value= "{$APP.LBL_SAVE_BUTTON_LABEL}"  class="crmButton small save" onclick=" getCreateCustomBlockForm('{$MODULE}','add') "/>&nbsp;
												<input type="button" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}"  class="crmButton small cancel" onclick= "fninvsh('addblock');" />
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						
					</div>
					<table border=0 cellspacing=0 cellpadding=10 width=100% >
					
					<tr valign="center" style="height:25px;" bgcolor="white">
					<td width="20%" class="dvtCellLabel" >{$MOD.ENTITY_IDENTIFIER_FIELD}</td>
					<td class="dvtCellInfo" >
					<select name="tks_entity" id="tks_entity" class="small">
						<option>{$MOD.SELECT_FIELD}</option>
						{if $entity_var eq 0}
						{foreach item=field from=$ENTITY_IDENTIFIER}							
							<option value="{$field}">{$field}</option>
						{/foreach}
						{/if}
					</select></td>
				</tr>  
				<tr>
					<td colspan="2">
						<div align="center">
								<input type="button" value="Save" onclick="if(valid() != false) copytks({$RELATED_LIST});" class="crmbutton small save">
							<input type="button" onclick="location.href='index.php?module=Settings&amp;action=index&amp;parenttab=Settings'" value="Cancel" class="crmbutton small cancel">
							<input type="button" onclick="download_zip();" value="Download" class="crmbutton small save" id="download1" style="display:none;">	
						
						</div>
					</td>
				</tr>
			
					</table>
</form>