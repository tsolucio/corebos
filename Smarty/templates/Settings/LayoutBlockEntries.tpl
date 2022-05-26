{*
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/ *}
<form action="index.php" method="post" name="form" onsubmit="VtigerJS_DialogBox.block();">
<input type="hidden" name="fld_module" value="{$MODULE}">
<input type="hidden" name="module" value="Settings">
<input type="hidden" name="mode">
<script type="text/javascript" src="include/js/customview.js"></script>
<script type="text/javascript" src="modules/Settings/Settings.js"></script>
<script src="include/jquery/jquery.js"></script>
<table class="listTable" border="0" cellpadding="3" cellspacing="0" width="100%">
	{foreach item=entries key=id from=$CFENTRIES name=outer}
		{if isset($entries.blockid) && $entries.blockid ne $RELPRODUCTSECTIONID && isset($entries.blocklabel) && $entries.blocklabel neq '' }
			{if $entries.blockid eq $COMMENTSECTIONID || $entries.blockid eq $SOLUTIONBLOCKID || $entries.isrelatedlist > 0}
				{assign var='showactionbuttons' value=false}
			{else}
				{assign var='showactionbuttons' value=true}
			{/if}
			{if $smarty.foreach.outer.first neq true}
			<tr><td><img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;</td></tr>
			{/if}
			<tr>
				<td class="colHeader small" colspan="2">
				<select name="display_status_{$entries.blockid}" style="border:1px solid #666666;font-family:Arial, Helvetica, sans-serif;font-size:11px; width:auto" onChange="changeShowstatus('{$entries.tabid}','{$entries.blockid}','{$MODULE}')" id='display_status_{$entries.blockid}'>
					<option value="show" {if $entries.display_status==1}selected{/if}>{$MOD.LBL_Show}</option>
					<option value="hide" {if $entries.display_status==0}selected{/if}>{$MOD.LBL_Hide}</option>
				</select>
				&nbsp;&nbsp;{$entries.blocklabel}&nbsp;&nbsp;
				</td>
				<td class="colHeader small cblds-t-align_right" id = "blockid_{$entries.blockid}" colspan="2" align='right'>
					{if $entries.iscustom == 1 }
					<img style="cursor:pointer;" onClick=" deleteCustomBlock('{$MODULE}','{$entries.blockid}','{$entries.no}')" src="{'delete.gif'|@vtiger_imageurl:$THEME}" border="0" alt="{$APP.LBL_DELETE}" title="{$APP.LBL_DELETE}"/>&nbsp;&nbsp;
					{/if}
					{if $showactionbuttons}
					<img src="{'hidden_fields.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="fnvshobj(this,'hiddenfields_{$entries.blockid}');" alt="{$MOD.HIDDEN_FIELDS}" title="{$MOD.HIDDEN_FIELDS}"/>&nbsp;&nbsp;
					{/if}
						<div id = "hiddenfields_{$entries.blockid}" style="display:none; position:absolute; width:300px;" class="layerPopup">
							<div style="position:relative; display:block">
										<table width="100%" border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
											<tr>
												<td width="95%" align="left" class="layerPopupHeading">
													{$MOD.HIDDEN_FIELDS}
												</td>
												<td width="5%" align="right" class="cblds-t-align_right">
													<a href="javascript:fninvsh('hiddenfields_{$entries.blockid}');"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" /></a>
												</td>
											</tr>
										</table>
										<table border="0" cellspacing="0" cellpadding="0" width="95%">
											<tr>
												<td class=small >
													<table border="0" celspacing="0" cellpadding="0" width="100%" align="center" bgcolor="white">
														<tr>
															<td align="center">
																<table border="0" cellspacing="0" cellpadding="0" width="100%">
																	<tr>
																		<td>{if !empty($entries.hidden_count)}
																			{$MOD.LBL_SELECT_FIELD_TO_MOVE}
																			{/if}
																		</td>
																	</tr>
																	<tr align="left">
																		<td>{if !empty($entries.hidden_count)}
																			<select class="small" id="hiddenfield_assignid_{$entries.blockid}" style="width:225px" size="10" multiple>
																	{foreach name=inner item=value from=$entries.hiddenfield}
																		<option value="{$value.fieldselect}">{$value.fieldlabel|@getTranslatedString:$MODULE}</option>
																	{/foreach}
																		</select>
																	{else}
																	{$MOD.NO_HIDDEN_FIELDS}
																	{/if}
																</td>
															</tr>
														</table>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
								<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
									<tr>
										<td align="center">
											<input type="button" name="save" value="{$APP.LBL_UNHIDE_FIELDS}" class="crmButton small save" onclick ="show_move_hiddenfields('{$MODULE}','{$entries.tabid}','{$entries.blockid}','showhiddenfields');"/>
											<input type="button" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" onclick="fninvsh('hiddenfields_{$entries.blockid}');" />
										</td>
									</tr>
								</table>
							</div>
						</div>
					{if $entries.hascustomtable && $showactionbuttons}
						<img src="{'plus_layout.gif'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="fnvshobj(this,'addfield_{$entries.blockid}'); " alt="{$MOD.LBL_ADD_CUSTOMFIELD}" title="{$MOD.LBL_ADD_CUSTOMFIELD}"/>&nbsp;&nbsp;
					{/if}
							<!-- for adding customfield -->
								<div id="addfield_{$entries.blockid}" style="display:none; position:absolute; width:500px;" class="layerPopup">
									<input type="hidden" name="mode" id="cfedit_mode" value="add">
									<table width="100%" border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
										<tr>
											<td width="60%" align="left" class="layerPopupHeading">{$MOD.LBL_ADD_FIELD}
											</td>
											<td width="40%" align="right" class="cblds-t-align_right"><a href="javascript:fninvsh('addfield_{$entries.blockid}');">
											<img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" /></a>
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
																	<td>{$MOD.LBL_SELECT_FIELD_TYPE}
																	</td>
																</tr>
																<tr>
																	<td>
																		<div name="cfcombo" id="cfcombo" class="small" style="width:205px; height:150px; overflow-y:auto ;overflow-x:hidden ;overflow:auto; border:1px solid #CCCCCC ;">
																			<table>
																				<tr><td align="left"><a id="field0_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'text.gif'|@vtiger_imageurl:$THEME});" onclick = "makeFieldSelected(this,0,{$entries.blockid});"> {$MOD.Text} </a></td></tr>
																				<tr><td align="left"><a id="field1_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'number.gif'|@vtiger_imageurl:$THEME});" onclick = "makeFieldSelected(this,1,{$entries.blockid})"> {$MOD.Number} </a></td></tr>
																				<tr><td align="left"><a id="field2_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'percent.gif'|@vtiger_imageurl:$THEME});" onclick = "makeFieldSelected(this,2,{$entries.blockid});"> {$MOD.Percent} </a></td></tr>
																				<tr><td align="left"><a id="field3_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'cfcurrency.gif'|@vtiger_imageurl:$THEME});" onclick = "makeFieldSelected(this,3,{$entries.blockid});"> {$MOD.Currency} </a></td></tr>
																				<tr><td align="left"><a id="field4_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'date.gif'|@vtiger_imageurl:$THEME});" onclick = "makeFieldSelected(this,4,{$entries.blockid});"> {$MOD.Date} </a></td></tr>
																				<tr><td align="left"><a id="field5_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'email.gif'|@vtiger_imageurl:$THEME});" onclick = "makeFieldSelected(this,5,{$entries.blockid});"> {$MOD.Email} </a></td></tr>
																				<tr><td align="left"><a id="field6_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'phone.gif'|@vtiger_imageurl:$THEME});" onclick = "makeFieldSelected(this,6,{$entries.blockid});"> {$MOD.Phone} </a>	</td></tr>
																				<tr><td align="left"><a id="field7_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'cfpicklist.gif'|@vtiger_imageurl:$THEME});" onclick = "makeFieldSelected(this,7,{$entries.blockid});"> {$MOD.PickList} </a></td></tr>
																				<tr><td align="left"><a id="field8_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'url.gif'|@vtiger_imageurl:$THEME});" onclick = "makeFieldSelected(this,8,{$entries.blockid});"> {$MOD.LBL_URL} </a></td></tr>
																				<tr><td align="left"><a id="field9_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'checkbox.gif'|@vtiger_imageurl:$THEME});" onclick = "makeFieldSelected(this,9,{$entries.blockid});"> {$MOD.LBL_CHECK_BOX} </a></td></tr>
																				<tr><td align="left"><a id="field10_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'text.gif'|@vtiger_imageurl:$THEME});" onclick = "makeFieldSelected(this,10,{$entries.blockid});"> {$MOD.LBL_TEXT_AREA} </a></td></tr>
																				<tr><td align="left"><a id="field11_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'cfpicklist.gif'|@vtiger_imageurl:$THEME});" onclick = "makeFieldSelected(this,11,{$entries.blockid});"> {$MOD.LBL_MULTISELECT_COMBO} </a></td></tr>
																				<tr><td align="left"><a id="field12_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'skype.gif'|@vtiger_imageurl:$THEME});" onclick = "makeFieldSelected(this,12,{$entries.blockid});"> {$MOD.Skype} </a></td></tr>
																				<tr><td align="left"><a id="field13_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'time.PNG'|@vtiger_imageurl:$THEME});" onclick = "makeFieldSelected(this,13,{$entries.blockid});"> {$MOD.Time} </a></td></tr>
																				<tr><td align="left"><a id="field14_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'createrelation.png'|@vtiger_imageurl:$THEME});" onclick = "makeFieldSelected(this,14,{$entries.blockid});"> {$MOD.Relation} </a></td></tr>
																				<tr><td align="left"><a id="field15_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'pictureicon.png'|@vtiger_imageurl:$THEME});" onclick = "makeFieldSelected(this,15,{$entries.blockid});"> {$APP.Image} </a></td></tr>
																				<tr><td align="left"><a id="field16_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'Cron.png'|@vtiger_imageurl:$THEME}); background-size: 20px 20px;" onclick = "makeFieldSelected(this,16,{$entries.blockid});"> {$MOD.Date} {$APP.AND} {$MOD.Time} </a></td></tr>
																			</table>
																		</div>
																	</td>
																</tr>
															</table>
														</td>
														<td width="50%">
															<table width="100%" border="0" cellpadding="5" cellspacing="0">
																<tr>
																	<td align="left" width="70%"><b>{$MOD.LBL_LABEL} </b><br>
																	<input id="fldLabel_{$entries.blockid}" value="" type="text" class="txtBox">
																	</td>
																</tr>
																<tr id="lengthdetails_{$entries.blockid}">
																	<td align="left"><b>{$MOD.LBL_LENGTH}</b><br>
																	<input type="text" id="fldLength_{$entries.blockid}" value="" class="txtBox">
																	</td>
																</tr>
																<tr id="decimaldetails_{$entries.blockid}" style="display:none;">
																	<td align="left"><b>{$MOD.LBL_DECIMAL_PLACES}</b><br>
																	<input type="text" id="fldDecimal_{$entries.blockid}" value="" class="txtBox">
																	</td>
																</tr>
																<tr id="picklistdetails_{$entries.blockid}" style="display:none;">
																	<td align="left" valign="top"><b>{$MOD.LBL_PICK_LIST_VALUES}</b><br>
																	<textarea id="fldPickList_{$entries.blockid}" rows="10" class="txtBox" ></textarea>
																	</td>
																</tr>
																<tr id="relationmodules_{$entries.blockid}" style="display:none;">
																	<td align="left" valign="top"><b>{$MOD.LBL_SELECT_MODULE}</b><br>
																	<select id="fldRelMods_{$entries.blockid}" rows="10" class="txtBox" multiple="multiple">
																		 {html_options options=$entityrelmods}
																	</select>
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
												<input type="button" name="save" value=" {$APP.LBL_SAVE_BUTTON_LABEL}" class="crmButton small save" onclick = "getCreateCustomFieldForm('{$MODULE}','{$entries.blockid}','add');"/>&nbsp;
												<input type="button" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL} " class="crmButton small cancel" onclick="fninvsh('addfield_{$entries.blockid}');" />
											</td>
										<input type="hidden" name="fieldType_{$entries.blockid}" id="fieldType_{$entries.blockid}" value="">
										<input type="hidden" name="selectedfieldtype_{$entries.blockid}" id="selectedfieldtype_{$entries.blockid}" value="">
										</tr>
									</table>
								</div>
					<!-- end custom field -->
					{if $showactionbuttons}
						<img src="{'moveinto.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer; height:16px; width:16px" onClick="fnvshobj(this,'movefields_{$entries.blockid}');" alt="{$MOD.LBL_MOVE_FIELDS}" title="{$MOD.LBL_MOVE_FIELDS}"/>&nbsp;&nbsp;
					{/if}
					<div id = "movefields_{$entries.blockid}" style="display:none; position:absolute; width:300px;" class="layerPopup">
							<div style="position:relative; display:block">
										<table width="100%" border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
											<tr>
												<td width="95%" align="left" class="layerPopupHeading">
													{$MOD.LBL_MOVE_FIELDS}
												</td>
												<td width="5%" align="right" class="cblds-t-align_right">
													<a href="javascript:fninvsh('movefields_{$entries.blockid}');"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" /></a>
												</td>
											</tr>
										</table>
										<table border="0" cellspacing="0" cellpadding="0" width="95%">
											<tr>
												<td class=small align="left" >
													<table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="white">
														<tr>
															<td>
																<table border="0" cellspacing="5" cellpadding="0" width="100%" align="left" class=small>
																	<tr>
																		<td>{$MOD.LBL_SELECT_FIELD_TO_MOVE}</td>
																	</tr>
																	<tr>
																		<td><select class="small" id="movefield_assignid_{$entries.blockid}" style="width:225px" size="10" multiple>
																	{foreach name=inner item=value from=$entries.movefield}
																		<option value="{$value.fieldid}">{$value.fieldlabel}</option>
																	{/foreach}
																	</select>
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
											<input type="button" name="save" value="{$APP.LBL_APPLY_BUTTON_LABEL}" class="crmButton small save" onclick ="show_move_hiddenfields('{$MODULE}','{$entries.tabid}','{$entries.blockid}','movehiddenfields');"/>
											<input type="button" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" onclick="fninvsh('movefields_{$entries.blockid}');" />
										</td>
									</tr>
								</table>
							</div>
						</div>
					{if $smarty.foreach.outer.first}
						<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
						<img src="{'arrow_down.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeBlockorder('block_down','{$entries.tabid}','{$entries.blockid}','{$MODULE}') " alt="{$MOD.DOWN}" title="{$MOD.DOWN}">&nbsp;&nbsp;
					{elseif $smarty.foreach.outer.last}
						<img src="{'arrow_up.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeBlockorder('block_up','{$entries.tabid}','{$entries.blockid}','{$MODULE}') " alt="{$MOD.UP}" title="{$MOD.UP}">&nbsp;&nbsp;
						<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
					{else}
						<img src="{'arrow_up.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeBlockorder('block_up','{$entries.tabid}','{$entries.blockid}','{$MODULE}') " alt="{$MOD.UP}" title="{$MOD.UP}">&nbsp;&nbsp;
						<img src="{'arrow_down.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeBlockorder('block_down','{$entries.tabid}','{$entries.blockid}','{$MODULE}') " alt="{$MOD.DOWN}" title="{$MOD.DOWN}">&nbsp;&nbsp;
					{/if}
				</td>
			</tr>
			<tr>
				{foreach name=inner item=value from=$entries.field}
					{if $value.no % 2 == 0}
						</tr>
						{assign var="rightcellclass" value=""}
						<tr>
					{else}
						{assign var="rightcellclass" value="class='rightCell'"}
					{/if}
				<td width="30%" id="colourButton" >&nbsp;
				<span onmouseover="tooltip.tip(this, showProperties('{$value.label}',{$value.mandatory},{$value.presence},{$value.quickcreate},{$value.massedit}));" onmouseout="tooltip.untip(false);" >{$value.label}</span>
					{if $value.fieldtype eq 'M'}
						<abbr class="slds-required" title="required">* </abbr>
					{/if}
				</td>
				<td width="19%" align="right" class="colData small cblds-t-align_right">
					<img src="{'editfield.gif'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="fnvshNrm('editfield_{$value.fieldselect}');" alt="Popup" title="{$MOD.LBL_EDIT_PROPERTIES}"/>&nbsp;&nbsp;
					<div id="editfield_{$value.fieldselect}" class="slds-panel slds-is-open" aria-hidden="false" style="display:none; position: absolute;">
						<div class="slds-panel__header">
							<h2 class="slds-panel__header-title slds-text-heading_small slds-truncate" title="{$value.label}">{$value.label}</h2>
							<button class="slds-button slds-button_icon slds-button_icon-small slds-panel__close" title="{$APP.LBL_CLOSE}" type="button" onclick="javascript:fninvsh('editfield_{$value.fieldselect}');">
							<svg class="slds-button__icon" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
							</svg>
							<span class="slds-assistive-text">{$APP.LBL_CLOSE}</span>
							</button>
						</div>
						<div class="slds-panel__body">
							<div class="cblds-t-align-left slds-page-header__meta-text slds-p-bottom_small" style="text-align:left;">
								<span> {$value.columnname} {$value.type} {$value.fieldsize}</span><br><span> {$value.colspec}</span>
							</div>
							<table class="slds-table slds-table_cell-buffer slds-table_bordered">
							<tbody>
								<tr>
									<td class="dvtCellInfo" align="left" width="10px">
										<input id="mandatory_check_{$value.fieldselect}" type="checkbox"
										{if $value.fieldtype neq 'M' && $value.mandatory eq '0'}
											disabled
										{elseif $value.mandatory eq '0' && $value.fieldtype eq 'M'}
											checked disabled
										{elseif $value.mandatory eq '3' }
											disabled
										{elseif $value.mandatory eq '2'}
											checked
										{/if}
										onclick = "{if $value.presence neq '0'} enableDisableCheckBox(this,presence_check_{$value.fieldselect}); {/if}
											{if $value.quickcreate neq '0' && $value.quickcreate neq '3'} enableDisableCheckBox(this,quickcreate_check_{$value.fieldselect}); {/if}">
								</td>
								<td valign="top" class="dvtCellInfo" align="left">
									&nbsp;<label for="mandatory_check_{$value.fieldselect}">{$MOD.LBL_MANDATORY_FIELD}</label>
								</td>
							</tr>
							<tr>
								<td valign="top" class="dvtCellInfo" align="left" width="10px">
									<input id="presence_check_{$value.fieldselect}" type="checkbox"
									{if $value.displaytype eq '2'}
										checked disabled
									{else}
										{if $value.presence eq '0' || $value.mandatory eq '0' || $value.quickcreate eq '0' || $value.mandatory eq '2'}
											checked disabled
										{/if}
										{if $value.presence eq '2'}
											checked
										{/if}
										{if $value.presence eq '3'}
											disabled
										{/if}
									{/if}
									>
								</td>
								<td valign="top" class="dvtCellInfo" align="left">
									&nbsp;<label for="presence_check_{$value.fieldselect}">{$MOD.LBL_ACTIVE}</label>
								</td>
							</tr>
							<tr>
								<td valign="top" class="dvtCellInfo" align="left" width="10px">
									<input id="quickcreate_check_{$value.fieldselect}" type="checkbox"
									{if $value.quickcreate eq '0'|| $value.quickcreate eq '2' && ($value.mandatory eq '0' || $value.mandatory eq '2')}
										checked disabled
									{/if}
									{if $value.quickcreate eq '2'}
										checked
									{/if}
									{if $value.quickcreate eq '3'}
										disabled
									{/if}
									>
								</td>
								<td valign="top" class="dvtCellInfo" align="left">
									&nbsp;<label for="quickcreate_check_{$value.fieldselect}">{$MOD.LBL_QUICK_CREATE}</label>
								</td>
							</tr>
							<tr>
								<td valign="top" class="dvtCellInfo" align="left" width="10px">
									<input id="massedit_check_{$value.fieldselect}" type="checkbox"
									{if $value.massedit eq '0'}
										disabled
									{/if}
									{if $value.massedit eq '1'}
										checked
									{/if}
									{if $value.displaytype neq '1' || $value.massedit eq '3'}
										disabled
									{/if}>
								</td>
								<td valign="top" class="dvtCellInfo" align="left">
								&nbsp;<label for="massedit_check_{$value.fieldselect}">{$MOD.LBL_MASS_EDIT}</label>
								</td>
							</tr>
							{if $value.uitype eq '19' || $value.uitype eq '21'}
								<tr>
									<td valign="top" class="dvtCellInfo" align="left" width="10px">
										<input id="longfield_check_{$value.fieldselect}" type="checkbox"
										{if $value.uitype eq '19'}
											checked
										{/if}
										{if $value.uitype eq '21'}
											unchecked
										{/if}>
									</td>
									<td valign="top" class="dvtCellInfo" align="left">
									&nbsp;<label for="longfield_check_{$value.fieldselect}">{$MOD.LBL_LONG_FIELD}</label>
									</td>
								</tr>
							{/if}
							<tr>
								<td valign="top" class="dvtCellInfo" align="left" width="10px">
									{assign var="defaultsetting" value=$value.defaultvalue}
									<input id="defaultvalue_check_{$value.fieldselect}" type="checkbox"
									{if $defaultsetting.permitted eq false} disabled{/if}
									{if $defaultsetting.value neq ''} checked{/if}>
								</td>
								<td valign="top" class="dvtCellInfo" align="left">
									&nbsp;<label for="defaultvalue_check_{$value.fieldselect}">{$MOD.LBL_DEFAULT_VALUE}</label><br>
									{assign var="fieldElementId" value='defaultvalue_'|cat:$value.fieldselect}
									{if $defaultsetting.permitted eq true}
										{include file="Settings/FieldUI.tpl"
											_FIELD_UI_TYPE=$value.uitype
											_FIELD_SELECTED_VALUE=$defaultsetting.value
											_FIELD_ELEMENT_ID=$fieldElementId
											_ALL_AVAILABLE_VALUES=$defaultsetting._allvalues
										}
									{/if}
								</td>
							</tr>
							{if $value.uitype eq 10}
							<tr>
								<td colspan='2' valign="top" class="dvtCellInfo" align="left">
									&nbsp;<label><b>{$MOD.LBL_SELECT_MODULE}</b></label><br>
									<select id="dependent_list_{$value.fieldselect}" name="dependent_list_{$value.fieldselect}" rows="10" class="slds-select" multiple="multiple">
										{foreach from=$entityrelmods key=outerkey item=outervalue}
											{assign var='isSelected' value=''}
											{foreach from=$curmodsinrel[$value.fieldselect] key=innerkey item=innervalue}
												{if ($innervalue eq $outerkey)}
													{assign var='isSelected' value='selected'}
												{/if}
											{/foreach}
											<option value={$outerkey} {$isSelected}>{$outervalue}</option>
										{/foreach}
									</select>
								</td>
							</tr>
							{/if}
						</tbody>
						</table>
					</div>
					<footer class="slds-modal__footer" style="width:100%;">
						<button name="cancel" type="button" class="slds-button slds-button_neutral" onclick="fninvsh('editfield_{$value.fieldselect}');">{$APP.LBL_CANCEL_BUTTON_LABEL}</button>
						{if $value.customfieldflag neq 0}
							<button name="delete" type="button" class="slds-button slds-button_destructive" onclick="getData('{$value.columnname}', '{$MODULE}', '{$value.label}'); fnvshobj(this,'hiddenfield_{$value.label}');">{$APP.LBL_DELETE_BUTTON_LABEL}</button>
						{/if}
						<button name="save" type="button" class="slds-button slds-button_brand" onclick="saveFieldInfo('{$value.fieldselect}','{$MODULE}','updateFieldProperties','{$value.typeofdata}','{$value.uitype}');">{$APP.LBL_SAVE_BUTTON_LABEL}</button>
					</footer>
				</div>
				<div id="hiddenfield_{$value.label}" style="display:none; position:absolute; width:500px; height: 500px; margin-top: -400px">
					<section role="dialog" tabindex="-1" class="slds-modal slds-fade-in-open">
						<div class="slds-modal__container">
							<div class="slds-box_border">
							<header class="slds-modal__header">
								<h2 id="modal-heading-01" class="slds-text-heading_medium slds-hyphenate">{$value.label} ({$value.columnname})</h2>
							</header>
							<div class="slds-modal__content slds-p-around_medium" id="modal-content-id-1">
								<p id="{$value.label}" style="text-align: left; padding-left: 6px;"></p>
							</div>
							<footer class="slds-modal__footer" style="width:100%">
								{if $value.customfieldflag neq 0}
								<button class="slds-button slds-button_destructive" onclick="deleteCustomField('{$value.fieldselect}','{$MODULE}','{$value.columnname}','{$value.uitype}'); return false;"> {$APP.LBL_DELETE_BUTTON_LABEL} </button>
								{/if}
								<button class="slds-button slds-button_neutral" onclick="fninvsh('hiddenfield_{$value.label}');return false;">{$APP.LBL_CANCEL_BUTTON_LABEL}</button>
							</footer>
							</div>
						</div>
					</section>
					<div class="slds-backdrop slds-backdrop_open"></div>
				</div>

					{if $smarty.foreach.inner.first}
						{if $value.no % 2 != 0}
							<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
						{/if}
						<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
						{if $value.no != ($entries.field|@count - 2) && $entries.no!=1}
							<img src="{'arrow_down.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeFieldorder('down','{$value.fieldselect}','{$value.blockid}','{$MODULE}') " alt="{$MOD.DOWN}" title="{$MOD.DOWN}">&nbsp;&nbsp;
						{else}
							<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
						{/if}
						{if $entries.no!=1}
							<img src="{'arrow_right.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeFieldorder('Right','{$value.fieldselect}','{$value.blockid}','{$MODULE}')" alt="{$MOD.RIGHT}" title="{$MOD.RIGHT}"/>&nbsp;&nbsp;
						{else}
							<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
						{/if}
					{elseif $smarty.foreach.inner.last}
						{if $value.no % 2 != 0}
							<img src="{'arrow_left.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeFieldorder('Left','{$value.fieldselect}','{$value.blockid}','{$MODULE}')" alt="{$MOD.LEFT}" title="{$MOD.LEFT}"/>&nbsp;&nbsp;
						{/if}
						{if $value.no != 1}
							<img src="{'arrow_up.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeFieldorder('up','{$value.fieldselect}','{$value.blockid}','{$MODULE}') " alt="{$MOD.UP}" title="{$MOD.UP}"/>&nbsp;&nbsp;
						{else}
							<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
						{/if}
						<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
						{if $value.no % 2 == 0}
							<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
						{/if}
					{else}
						{if $value.no % 2 != 0}
							<img src="{'arrow_left.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeFieldorder('Left','{$value.fieldselect}','{$value.blockid}','{$MODULE}')" alt="{$MOD.LEFT}" title="{$MOD.LEFT}"/>&nbsp;&nbsp;
						{/if}
						{if $value.no != 1}
							<img src="{'arrow_up.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeFieldorder('up','{$value.fieldselect}','{$value.blockid}','{$MODULE}') " alt="{$MOD.UP}" title="{$MOD.UP}"/>&nbsp;&nbsp;
						{else}
							<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
						{/if}
						{if $value.no != ($entries.field|@count - 2)}
							<img src="{'arrow_down.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeFieldorder('down','{$value.fieldselect}','{$value.blockid}','{$MODULE}') " alt="{$MOD.DOWN}" title="{$MOD.DOWN}">&nbsp;&nbsp;
						{else}
							<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
						{/if}
						{if $value.no % 2 == 0}
							<img src="{'arrow_right.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeFieldorder('Right','{$value.fieldselect}','{$value.blockid}','{$MODULE}')" alt="{$MOD.RIGHT}" title="{$MOD.RIGHT}"/>&nbsp;&nbsp;
						{/if}
					{/if}
				</td>
			{/foreach}
			</tr>
		{elseif $entries.DVB != ''}
		{if $smarty.foreach.outer.first neq true}
		<tr><td><img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;</td></tr>
		{/if}
		<tr>
			<td class="colHeader small" colspan="2">&nbsp;&nbsp;{$entries.label}</td>
			<td class="colHeader small" id = "blockid_dvb{$entries.DVB}" colspan="2" align='right' class="cblds-t-align_right">
			{if $smarty.foreach.outer.first}
				<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
				<img src="{'arrow_down.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeBlockorder('block_down','{$entries.tabid}','dvb{$entries.DVB}','{$MODULE}') " alt="{$MOD.DOWN}" title="{$MOD.DOWN}">&nbsp;&nbsp;
			{elseif $smarty.foreach.outer.last}
				<img src="{'arrow_up.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeBlockorder('block_up','{$entries.tabid}','dvb{$entries.DVB}','{$MODULE}') " alt="{$MOD.UP}" title="{$MOD.UP}">&nbsp;&nbsp;
				<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
			{else}
				<img src="{'arrow_up.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeBlockorder('block_up','{$entries.tabid}','dvb{$entries.DVB}','{$MODULE}') " alt="{$MOD.UP}" title="{$MOD.UP}">&nbsp;&nbsp;
				<img src="{'arrow_down.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeBlockorder('block_down','{$entries.tabid}','dvb{$entries.DVB}','{$MODULE}') " alt="{$MOD.DOWN}" title="{$MOD.DOWN}">&nbsp;&nbsp;
			{/if}
		</tr>
		{/if}
	{/foreach}
</table>
	<div id="addblock" style="display:none; position:absolute; width:500px;" class="layerPopup">
		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
			<tr>
				<td width="95%" align="left" class="layerPopupHeading cblds-p_medium">{$MOD.LBL_ADD_BLOCK}
				</td>
				<td width="5%" align="right" class="cblds-t-align_right cblds-p_medium"><a href="javascript:fninvsh('addblock');"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" /></a>
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
										<td class="dataLabel cblds-t-align_right cblds-p_medium" nowrap="nowrap" align="right" width="30%"><b>{$MOD.LBL_BLOCK_NAME}</b></td>
										<td align="left" width="70%">
										<input id="blocklabel" value="" type="text" class="txtBox">
										</td>
									</tr>
									<tr>
										<td class="dataLabel cblds-t-align_right cblds-p_medium" align="right" width="30%"><b>{$MOD.AFTER}</b></td>
										<td align="left" width="70%">
										<select id="after_blockid" name="after_blockid">
											{foreach key=blockid item=blockname from=$BLOCKS}
											{if !empty($blockname)}
											<option value = {$blockid}> {$blockname} </option>
											{/if}
											{/foreach}
										</select>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					<table border=0 cellspacing=0 cellpadding=5 width=100% >
						<tr>
						<td colspan=2><hr width="100%"></td>
						</tr>
						<tr>
						<td class="dataLabel cblds-t-align_right cblds-p_medium" nowrap="nowrap" align="right" width="30%"><b>{'QuickRelatedList'|@gettranslatedString:$MODULE}:</b></td>
						<td align="left" width="70%">
							<select name='relatedlistblock' id='relatedlistblock' onchange="getElementById('blocklabel').value=this.value;">
								<option value="no" selected>{'LBL_NO'|@gettranslatedString:$MODULE}</option>
								{foreach key=rlmname item=rllabel from=$NotBlockRelatedModules}
								{if is_numeric($rlmname)} {
									<option value="{$rlmname}">{$rllabel|@gettranslatedString:$MODULE}</option>
								{else}
									<option value="{$rlmname}">{$rlmname|@gettranslatedString:$rlmname}</option>
								{/if}}
								{/foreach}
							</select>
						</td>
						</tr>
					</table>
					<table border=0 cellspacing=0 cellpadding=5 width=100% >
						<tr>
							<td align="center" class="cblds-t-align_center">
								<input type="button" name="save" value= "{$APP.LBL_SAVE_BUTTON_LABEL}" class="crmButton small save" onclick="getCreateCustomBlockForm('{$MODULE}','add');"/>&nbsp;
								<input type="button" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" onclick= "fninvsh('addblock');" />
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</form>
