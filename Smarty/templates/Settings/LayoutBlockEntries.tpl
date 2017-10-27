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
<input type="hidden" name="parenttab" value="Settings">
<input type="hidden" name="mode">
<script type="text/javascript" src="include/js/customview.js"></script>
<script type="text/javascript" src="include/js/general.js"></script>
<table class="slds-table slds-no-row-hover layout-editor-table">
	{foreach item=entries key=id from=$CFENTRIES name=outer}
		{if isset($entries.blockid) && $entries.blockid ne $RELPRODUCTSECTIONID && isset($entries.blocklabel) && $entries.blocklabel neq '' }
			{if $entries.blockid eq $COMMENTSECTIONID || $entries.blockid eq $SOLUTIONBLOCKID || $entries.isrelatedlist > 0}
				{assign var='showactionbuttons' value=false}
			{else}
				{assign var='showactionbuttons' value=true}
			{/if}
			{if $smarty.foreach.outer.first neq true}
			<tr><td><img src="{'blank.gif'|@vtiger_imageurl:$THEME}" border="0" />&nbsp;&nbsp;</td></tr>
			{/if}
			<tr>
				<td class="small" colspan="2">
				<!-- Left Header with title and hide/show select -->
					<div class="forceRelatedListSingleContainer">
						<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
							<div class="slds-card__header slds-grid">
								<div class="slds-no-flex">
									<div class="actionsContainer">
										<select class="slds-select" name="display_status_{$entries.blockid}" style="width:auto" onChange="changeShowstatus('{$entries.tabid}','{$entries.blockid}','{$MODULE}')" id='display_status_{$entries.blockid}'>
											<option value="show" {if $entries.display_status==1}selected{/if}>{$MOD.LBL_Show}</option>
											<option value="hide" {if $entries.display_status==0}selected{/if}>{$MOD.LBL_Hide}</option>
										</select>
									</div>
								</div>
								&nbsp;&nbsp;&nbsp;&nbsp;
								<header class="slds-media slds-media--center slds-has-flexi-truncate">
									<div class="slds-media__body">
										<h2>
											<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
												<b>{$entries.blocklabel}</b>
											</span>
										</h2>
									</div>
								</header>
							</div>
						</article>
					</div>
				</td>
				<td class="small" id="blockid_{$entries.blockid}" colspan="2" align='right'>
					<!-- Right Header with link icons -->
					<div class="forceRelatedListSingleContainer">
						<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
							<div class="slds-card__header slds-grid">
								<header class="slds-media slds-media--center slds-has-flexi-truncate">
									<div class="slds-media__body">
										<h2>
											&nbsp;
										</h2>
									</div>
								</header>
								<div class="slds-no-flex">
									<div class="actionsContainer">
										<!-- Delete Custom Block Popup Image Link -->
										{if $entries.iscustom == 1 }
										<img onClick=" deleteCustomBlock('{$MODULE}','{$entries.blockid}','{$entries.no}')" src="{'delete.gif'|@vtiger_imageurl:$THEME}" border="0" alt="{$APP.LBL_DELETE}" title="{$APP.LBL_DELETE}"/>&nbsp;&nbsp;
										{/if}
										<!-- Hidden Fields Popup Image Link -->
										{if $showactionbuttons}
										<img src="{'hidden_fields.png'|@vtiger_imageurl:$THEME}" onclick="fnvshobj(this,'hiddenfields_{$entries.blockid}');" alt="{$MOD.HIDDEN_FIELDS}" title="{$MOD.HIDDEN_FIELDS}"/>&nbsp;&nbsp;
										{/if}
										<!-- Add Custom Fields Popup Image Link -->
										{if $entries.hascustomtable && $showactionbuttons}
											<img src="{'plus_layout.gif'|@vtiger_imageurl:$THEME}" onclick="fnvshobj(this,'addfield_{$entries.blockid}'); " alt="{$MOD.LBL_ADD_CUSTOMFIELD}" title="{$MOD.LBL_ADD_CUSTOMFIELD}"/>&nbsp;&nbsp;
										{/if}
										<!-- Move Into Popup Image Link -->
										{if $showactionbuttons}
											<img src="{'moveinto.png'|@vtiger_imageurl:$THEME}" onClick="fnvshobj(this,'movefields_{$entries.blockid}');" alt="{$MOD.LBL_MOVE_FIELDS}" title="{$MOD.LBL_MOVE_FIELDS}"/>&nbsp;&nbsp;
										{/if}
										&nbsp;
										<!-- Move Down arrow Image Link -->
										{if $smarty.foreach.outer.first}
											<img src="{'blank.gif'|@vtiger_imageurl:$THEME}"  border="0" />&nbsp;&nbsp;
											<img src="{'arrow_down.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeBlockorder('block_down','{$entries.tabid}','{$entries.blockid}','{$MODULE}') " alt="{$MOD.DOWN}" title="{$MOD.DOWN}">&nbsp;&nbsp;
										{elseif $smarty.foreach.outer.last}
											<img src="{'arrow_up.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeBlockorder('block_up','{$entries.tabid}','{$entries.blockid}','{$MODULE}') " alt="{$MOD.UP}" title="{$MOD.UP}">&nbsp;&nbsp;
											<img src="{'blank.gif'|@vtiger_imageurl:$THEME}"  border="0" />&nbsp;&nbsp;
										{else}
											<img src="{'arrow_up.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeBlockorder('block_up','{$entries.tabid}','{$entries.blockid}','{$MODULE}') " alt="{$MOD.UP}" title="{$MOD.UP}">&nbsp;&nbsp;
											<img src="{'arrow_down.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeBlockorder('block_down','{$entries.tabid}','{$entries.blockid}','{$MODULE}') " alt="{$MOD.DOWN}" title="{$MOD.DOWN}">&nbsp;&nbsp;
										{/if}
									</div>
								</div>
							</div>
						</article>
					</div>

					<!-- Hidden fields popup -->
					<div id="hiddenfields_{$entries.blockid}" style="display:none; position:absolute; width:300px;" class="layerPopup">
						<div style="position:relative; display:block">
							<!-- Header and close icon -->
							<table class="slds-table slds-no-row-hover" style="border-bottom: 1px solid #d4d4d4;">
								<tr class="slds-text-title--header">
									<th scope="col">
										<div class="slds-truncate moduleName">
											<b>{$MOD.HIDDEN_FIELDS}</b>
										</div>
									</th>
									<th scope="col" style="padding: .5rem; text-align: right;">
										<div class="slds-truncate">
											<a href="javascript:fninvsh('hiddenfields_{$entries.blockid}');"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" /></a>
										</div>
									</th>
								</tr>
							</table>
							<!-- Hidden fields Body content -->
							<table class="slds-table slds-no-row-hover">
								<tr class="slds-line-height--reset">
									<!-- Name of field to unhide -->
									<td class="dvtCellLabel dataLabel" width="50%" align="right" nowrap >
										{if !empty($entries.hidden_count)}
											<b>{$MOD.LBL_SELECT_FIELD_TO_MOVE}</b>
										{/if}
									</td>
									<!-- Select fields to unhide -->
									<td class="dvtCellInfo" align="left" width="50%">
										<!-- Show list of hidden fields -->
										{if !empty($entries.hidden_count)}
											<select class="slds-select" id="hiddenfield_assignid_{$entries.blockid}" style="height: 180px;" size="5" multiple>
												{foreach name=inner item=value from=$entries.hiddenfield}
													<option value="{$value.fieldselect}">{$value.fieldlabel|@getTranslatedString:$MODULE}</option>
												{/foreach}
											</select>
										{else}
											<!-- No hidden fields to unhide -->
											{$MOD.NO_HIDDEN_FIELDS}
										{/if}
									</td>
								</tr>
							</table>
							<!-- Unhide and Cancel buttons -->
							<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
								<tr>
									<td align="center" style="padding: 5px;">
										<input type="button" name="save" value="{$APP.LBL_UNHIDE_FIELDS}" class="slds-button slds-button--small slds-button_success" onclick ="show_move_hiddenfields('{$MODULE}','{$entries.tabid}','{$entries.blockid}','showhiddenfields');"/>
										<input type="button" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="slds-button slds-button--small slds-button--destructive" onclick="fninvsh('hiddenfields_{$entries.blockid}');" />
									</td>
								</tr>
							</table>
						</div>
					</div>

					<!-- Custom Fields Popup-->
					<div id="addfield_{$entries.blockid}" style="display:none; position:absolute; width:500px;" class="layerPopup">
						<input type="hidden" name="mode" id="cfedit_mode" value="add">
						<!-- Custom fields Title and close icon -->
						<table class="slds-table slds-no-row-hover" style="border-bottom: 1px solid #d4d4d4;">
							<tr class="slds-text-title--header">
								<th scope="col">
									<div class="slds-truncate moduleName">
										{$MOD.LBL_ADD_FIELD}
									</div>
								</th>
								<th scope="col" style="padding: .5rem; text-align: right;">
									<div class="slds-truncate">
										<a href="javascript:fninvsh('addfield_{$entries.blockid}');"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" /></a>
									</div>
								</th>
							</tr>
						</table>
						<!-- Create custom fields body content -->
						<table class="slds-table slds-no-row-hover">
							<tr class="slds-line-height--reset">
								<!-- Create custom fields left content title and table-->
								<td class="dvtCellInfo">
									<table>
										<!-- Select fields type Title -->
										<tr>
											<td class="dvtCellLabel text-left dataLabel" width="50%" align="right" nowrap >{$MOD.LBL_SELECT_FIELD_TYPE}</td>
										</tr>
										<!-- Field types table -->
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
								<!-- Create custom fields right content labels and input fields -->
								<td width="50%" class="dvtCellInfo">
									<table class="slds-table slds-no-row-hover">
										<!-- Label & Input field -->
										<tr class="slds-line-height--reset">
											<td class="dvtCellLabel text-left" align="left">
												<b>{$MOD.LBL_LABEL} </b>
											<br/>
												<input id="fldLabel_{$entries.blockid}" value="" type="text" class="slds-input">
											</td>
										</tr>
										<!-- Length Label & Input field-->
										<tr class="slds-line-height--reset" id="lengthdetails_{$entries.blockid}">
											<td class="dvtCellLabel text-left" align="left">
												<b>{$MOD.LBL_LENGTH}</b>
											<br/>
												<input type="text" id="fldLength_{$entries.blockid}" value="" class="slds-input">
											</td>
										</tr>
										<!-- Decimal Label & Input field-->
										<tr id="decimaldetails_{$entries.blockid}" style="display:none;">
											<td class="dvtCellLabel text-left" align="left">
												<b>{$MOD.LBL_DECIMAL_PLACES}</b>
											<br>
												<input type="text" id="fldDecimal_{$entries.blockid}" value="" class="slds-input">
											</td>
										</tr>
										<!-- Picklist Label & fields-->
										<tr id="picklistdetails_{$entries.blockid}" style="display:none;">
											<td class="dvtCellLabel text-left" align="left">
												<b>{$MOD.LBL_PICK_LIST_VALUES}</b>
											<br>
												<textarea id="fldPickList_{$entries.blockid}" rows="10" class="slds-textarea" ></textarea>
											</td>
										</tr>
										<!-- Multiple select Label & options-->
										<tr id="relationmodules_{$entries.blockid}" style="display:none;">
											<td class="dvtCellLabel text-left" align="left">
												<b>{$MOD.LBL_SELECT_MODULE}</b>
											<br>
												<select id="fldRelMods_{$entries.blockid}" rows="10" class="slds-select" multiple="multiple">
													 {html_options options=$entityrelmods}
												</select>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<!-- Save and Cancel button -->
						<table border="0" cellspacing="0" cellpadding="5" width="100%" class="layerPopupTransport">
							<tr>
								<td align="center" style="padding: 5px;">
									<input type="button" name="save" value=" {$APP.LBL_SAVE_BUTTON_LABEL}" class="slds-button--small slds-button  slds-button_success" onclick = "getCreateCustomFieldForm('{$MODULE}','{$entries.blockid}','add');"/>&nbsp;
									<input type="button" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL} " class="slds-button slds-button--small slds-button--destructive" onclick="fninvsh('addfield_{$entries.blockid}');" />
								</td>
								<input type="hidden" name="fieldType_{$entries.blockid}" id="fieldType_{$entries.blockid}" value="">
								<input type="hidden" name="selectedfieldtype_{$entries.blockid}" id="selectedfieldtype_{$entries.blockid}" value="">
							</tr>
						</table>
					</div>

					<!-- Move Fields Popup -->
					<div id="movefields_{$entries.blockid}" style="display:none; position:absolute; width:300px;" class="layerPopup">
						<div style="position:relative; display:block">
						<!-- Move fields Title and close icon -->
							<table class="slds-table slds-no-row-hover" style="border-bottom: 1px solid #d4d4d4;">
								<tr class="slds-text-title--header">
									<!-- Title -->
									<th scope="col">
										<div class="slds-truncate moduleName">
											{$MOD.LBL_MOVE_FIELDS}
										</div>
									</th>
									<!-- Close icon -->
									<th scope="col" style="padding: .5rem; text-align: right;">
										<div class="slds-truncate">
											<a href="javascript:fninvsh('movefields_{$entries.blockid}');"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" /></a>
										</div>
									</th>
								</tr>
							</table>
							<!-- Move fields Body content -->
							<table class="slds-table slds-no-row-hover">
								<tr class="slds-line-height--reset">
									<!-- Field to move Label-->
									<td class="dvtCellLabel text-left dataLabel" width="50%" align="right" nowrap >
										{$MOD.LBL_SELECT_FIELD_TO_MOVE}
									</td>
								</tr>
								<tr>
									<!-- Select fields to move -->
									<td class="dvtCellInfo" align="left" width="50%">
										<select class="slds-select" id="movefield_assignid_{$entries.blockid}" style="height: 180px" size="5" multiple>
											{foreach name=inner item=value from=$entries.movefield}
												<option value="{$value.fieldid}">{$value.fieldlabel}</option>
											{/foreach}
										</select>
									</td>
								</tr>
							</table>
							<!-- Apply and Cancel buttons -->
							<table border="0" cellspacing="0" cellpadding="5" width="100%" class="layerPopupTransport">
								<tr>
									<td align="center" style="padding: 5px;">
										<input type="button" name="save" value="{$APP.LBL_APPLY_BUTTON_LABEL}" class="slds-button slds-button--small slds-button_success" onclick ="show_move_hiddenfields('{$MODULE}','{$entries.tabid}','{$entries.blockid}','movehiddenfields');"/>
										<input type="button" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="slds-button slds-button--small slds-button--destructive" onclick="fninvsh('movefields_{$entries.blockid}');" />
									</td>
								</tr>
							</table>
						</div>
					</div>
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
					<!-- Labels Value foreach property -->
					<td class="dvtCellLabel " id="colourButton">
						<span onmouseover="tooltip.tip(this, showProperties('{$value.label}',{$value.mandatory},{$value.presence},{$value.quickcreate},{$value.massedit}));" onmouseout="tooltip.untip(false);" style="padding: .2rem;">
							{$value.label}
						</span>
						{if $value.fieldtype eq 'M'}
							<font color='red'> *</font>
						{/if}
					</td>
					<!-- Link icons foreach property -->
					<td align="left" class="dvtCellInfo colData small">
						<!-- Edit field link icon -->
						<img src="{'editfield.gif'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="fnvshNrm('editfield_{$value.fieldselect}'); posLay(this, 'editfield_{$value.fieldselect}');" alt="Popup" title="{$MOD.LBL_EDIT_PROPERTIES}"/>
						&nbsp;
						<!-- Edit Field Popup -->
						<div id="editfield_{$value.fieldselect}" style="display:none; position: absolute; width: 500px; left: 300px; top: 300px;" >
							<div class="layerPopup" style="position:relative; display:block">
								<!-- Edit field Header -->
								<table class="slds-table slds-no-row-hover" style="border-bottom: 1px solid #d4d4d4;">
									<tr class="slds-text-title--header">
										<!-- Title -->
										<th scope="col">
											<div class="slds-truncate moduleName">
												{$value.label} ({$value.columnname})
											</div>
										</th>
										<!-- Close icon -->
										<th scope="col" style="padding: .5rem; text-align: right;">
											<div class="slds-truncate">
												<a href="javascript:fninvsh('editfield_{$value.fieldselect}');"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" /></a>
											</div>
										</th>
									</tr>
								</table>
								<!-- Edit field Body  -->
								<table class="slds-table slds-no-row-hover">
									<!-- Checkbox and Mandatory option -->
									<tr class="slds-line-height--reset">
										<!-- Field to check option-->
										<td class="dvtCellInfo dataLabel" align="center" nowrap width="10px">
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
											onclick = "{if $value.presence neq '0'} enableDisableCheckBox(this,presence_check_{$value.fieldselect}); {/if} {if $value.quickcreate neq '0' && $value.quickcreate neq '3'} enableDisableCheckBox(this,quickcreate_check_{$value.fieldselect}); {/if}">
										</td>
										<!-- Mandator field -->
										<td class="dvtCellInfo" align="left">
											<label for="mandatory_check_{$value.fieldselect}"> {$MOD.LBL_MANDATORY_FIELD}</label>
										</td>
									</tr>
									<!-- Checkbox and Active option -->
									<tr class="slds-line-height--reset">
										<!-- Field to check option-->
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
											/>
										</td>
										<!-- Active field -->
										<td class="dvtCellInfo" align="left">
											<label for="presence_check_{$value.fieldselect}"> {$MOD.LBL_ACTIVE}</label>
										</td>
									</tr>
									<!-- Checkbox and Quick Create option -->
									<tr class="slds-line-height--reset">
										<!-- Field to check option-->
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
										<!-- Quick Create Field -->
										<td class="dvtCellInfo" align="left">
											<label for="quickcreate_check_{$value.fieldselect}"> {$MOD.LBL_QUICK_CREATE}</label>
										</td>
									</tr>
									<!-- Checkbox and Mass Edit option -->
									<tr class="slds-line-height--reset">
										<!-- Field to check option-->
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
										<!-- Mass Edit Field -->
										<td class="dvtCellInfo" align="left">
											<label for="massedit_check_{$value.fieldselect}"> {$MOD.LBL_MASS_EDIT}</label>
										</td>
									</tr>
									<!-- Checkbox and Quick Create option -->
									<tr>
										<!-- Field to check option-->
										<td valign="top" class="dvtCellInfo" align="left" width="10px">
											{assign var="defaultsetting" value=$value.defaultvalue}
											<input id="defaultvalue_check_{$value.fieldselect}" type="checkbox"
											{if $defaultsetting.permitted eq false} disabled{/if}
											{if $defaultsetting.value neq ''} checked{/if}>
										</td>
										<!-- Default value Field -->
										<td class="dvtCellInfo" align="left">
											<label for="defaultvalue_check_{$value.fieldselect}"> {$MOD.LBL_DEFAULT_VALUE}</label><br>
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
									<!-- Save and Cancel buttons -->
									<tr>
										<td colspan="3" class="dvtCellInfo" align="center">
											<input type="button" name="save" value=" &nbsp; {$APP.LBL_SAVE_BUTTON_LABEL} &nbsp; " class="slds-button slds-button--small slds-button_success" onclick="saveFieldInfo('{$value.fieldselect}','{$MODULE}','updateFieldProperties','{$value.typeofdata}');" />&nbsp;
											{if $value.customfieldflag neq 0}
												<input type="button" name="delete" value=" {$APP.LBL_DELETE_BUTTON_LABEL} " class="slds-button slds-button--small slds-button--destructive" onclick="deleteCustomField('{$value.fieldselect}','{$MODULE}','{$value.columnname}','{$value.uitype}')" />
											{/if}
											<input type="button" name="cancel" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="slds-button slds-button--small slds-button--destructive" onclick="fninvsh('editfield_{$value.fieldselect}');" />
										</td>
									</tr>
								</table>
							</div>
						</div>

						<!-- Blank field, (Up, Down, Left, Right) arrows for each condition -->
						{if $smarty.foreach.inner.first}
							{if $value.no % 2 != 0}
								<img src="{'blank.gif'|@vtiger_imageurl:$THEME}"  border="0" />&nbsp;&nbsp;
							{/if}
							<img src="{'blank.gif'|@vtiger_imageurl:$THEME}"  border="0" />&nbsp;&nbsp;
							{if $value.no != ($entries.field|@count - 2) && $entries.no!=1}
								<img src="{'arrow_down.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeFieldorder('down','{$value.fieldselect}','{$value.blockid}','{$MODULE}') " alt="{$MOD.DOWN}" title="{$MOD.DOWN}">&nbsp;&nbsp;
							{else}
								<img src="{'blank.gif'|@vtiger_imageurl:$THEME}"  border="0" />&nbsp;&nbsp;
							{/if}
							{if $entries.no!=1}
								<img src="{'arrow_right.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeFieldorder('Right','{$value.fieldselect}','{$value.blockid}','{$MODULE}')" alt="{$MOD.RIGHT}" title="{$MOD.RIGHT}"/>&nbsp;&nbsp;
							{else}
								<img src="{'blank.gif'|@vtiger_imageurl:$THEME}"  border="0" />&nbsp;&nbsp;
							{/if}
						{elseif $smarty.foreach.inner.last}
							{if $value.no % 2 != 0}
								<img src="{'arrow_left.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeFieldorder('Left','{$value.fieldselect}','{$value.blockid}','{$MODULE}')" alt="{$MOD.LEFT}" title="{$MOD.LEFT}"/>&nbsp;&nbsp;
							{/if}
							{if $value.no != 1}
								<img src="{'arrow_up.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeFieldorder('up','{$value.fieldselect}','{$value.blockid}','{$MODULE}') " alt="{$MOD.UP}" title="{$MOD.UP}"/>&nbsp;&nbsp;
							{else}
								<img src="{'blank.gif'|@vtiger_imageurl:$THEME}"  border="0" />&nbsp;&nbsp;
							{/if}
							<img src="{'blank.gif'|@vtiger_imageurl:$THEME}"  border="0" />&nbsp;&nbsp;
							{if $value.no % 2 == 0}
								<img src="{'blank.gif'|@vtiger_imageurl:$THEME}"  border="0" />&nbsp;&nbsp;
							{/if}
						{else}
							{if $value.no % 2 != 0}
								<img src="{'arrow_left.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeFieldorder('Left','{$value.fieldselect}','{$value.blockid}','{$MODULE}')" alt="{$MOD.LEFT}" title="{$MOD.LEFT}"/>&nbsp;&nbsp;
							{/if}
							{if $value.no != 1}
								<img src="{'arrow_up.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeFieldorder('up','{$value.fieldselect}','{$value.blockid}','{$MODULE}') " alt="{$MOD.UP}" title="{$MOD.UP}"/>&nbsp;&nbsp;
							{else}
								<img src="{'blank.gif'|@vtiger_imageurl:$THEME}"  border="0" />&nbsp;&nbsp;
							{/if}
							{if $value.no != ($entries.field|@count - 2)}
								<img src="{'arrow_down.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeFieldorder('down','{$value.fieldselect}','{$value.blockid}','{$MODULE}') " alt="{$MOD.DOWN}" title="{$MOD.DOWN}">&nbsp;&nbsp;
							{else}
								<img src="{'blank.gif'|@vtiger_imageurl:$THEME}"  border="0" />&nbsp;&nbsp;
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
			<tr><td><img src="{'blank.gif'|@vtiger_imageurl:$THEME}"/>&nbsp;&nbsp;</td></tr>
			{/if}
			<tr>
				<td class="colHeader small" colspan="2">
					<div class="forceRelatedListSingleContainer">
						<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
							<div class="slds-card__header slds-grid">
								<header class="slds-media slds-media--center slds-has-flexi-truncate">
									<div class="slds-media__body">
										<h2>
											<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
												<b>{$entries.label}</b>
											</span>
										</h2>
									</div>
								</header>
							</div>
						</article>
					</div>
				</td>
				<td class="colHeader small" id = "blockid_dvb{$entries.DVB}" colspan="2" align='right'>
				{if $smarty.foreach.outer.first}
					<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" />&nbsp;&nbsp;
					<img src="{'arrow_down.png'|@vtiger_imageurl:$THEME}" onclick="changeBlockorder('block_down','{$entries.tabid}','dvb{$entries.DVB}','{$MODULE}') " alt="{$MOD.DOWN}" title="{$MOD.DOWN}">&nbsp;&nbsp;
				{elseif $smarty.foreach.outer.last}
					<img src="{'arrow_up.png'|@vtiger_imageurl:$THEME}" onclick="changeBlockorder('block_up','{$entries.tabid}','dvb{$entries.DVB}','{$MODULE}') " alt="{$MOD.UP}" title="{$MOD.UP}">&nbsp;&nbsp;
					<img src="{'blank.gif'|@vtiger_imageurl:$THEME}"/>&nbsp;&nbsp;
				{else}
					<img src="{'arrow_up.png'|@vtiger_imageurl:$THEME}" onclick="changeBlockorder('block_up','{$entries.tabid}','dvb{$entries.DVB}','{$MODULE}') " alt="{$MOD.UP}" title="{$MOD.UP}">&nbsp;&nbsp;
					<img src="{'arrow_down.png'|@vtiger_imageurl:$THEME}" onclick="changeBlockorder('block_down','{$entries.tabid}','dvb{$entries.DVB}','{$MODULE}') " alt="{$MOD.DOWN}" title="{$MOD.DOWN}">&nbsp;&nbsp;
				{/if}
			</tr>
		{/if}
	{/foreach}
</table>

	<!-- Add block Popup -->
	<div id="addblock" style="display:none; position:absolute; width:500px;" class="addBlockPopup layerPopup">
		<!-- Header and close icon -->
		<table class="slds-table slds-no-row-hover" width="100%">
			<tr class="slds-text-title--header">
				<th scope="col">
					<div class="slds-truncate moduleName">
						<b>{$MOD.LBL_ADD_BLOCK}</b>
					</div>
				</th>
				<th scope="col" style="padding: .5rem; text-align: right;">
					<div class="slds-truncate">
						<a href="javascript:fninvsh('addblock');"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" /></a>
					</div>
				</th>
			</tr>
		</table>
		<!-- Body Content -->
		<table class="slds-table slds-no-row-hover">
			<tr>
				<td class="small" style="border-top: 1px solid #d4d4d4;">

					<table class="slds-table slds-no-row-hover">
						<!-- Enter block name -->
						<tr class="slds-line-height--reset">
							<td class="dvtCellLabel dataLabel" align="right" nowrap ><b>{$MOD.LBL_BLOCK_NAME}</b></td>
							<td class="dvtCellInfo" align="left"><input id="blocklabel" value="" type="text" class="slds-input" style="width: 100%;"></td>
						</tr>
						<!-- Select after option -->
						<tr class="slds-line-height--reset">
							<td class="dvtCellLabel dataLabel" align="right"><b>{$MOD.AFTER}</b></td>
							<td class="dvtCellInfo" align="left" width="70%">
								<select id="after_blockid" name="after_blockid" class="slds-select">
									{foreach key=blockid item=blockname from=$BLOCKS}
										<option value = {$blockid}> {$blockname} </option>
									{/foreach}
								</select>
							</td>
						</tr>
						<tr><td colspan=2><hr width="100%"></td></tr>
						<!-- Related modules options -->
						<tr class="slds-line-height--reset">
							<td class="dvtCellLabel dataLabel" nowrap align="right"><b>{'QuickRelatedList'|@gettranslatedString:$MODULE}:</b></td>
							<td class="dvtCellInfo" align="left" width="70%">
								<select name='relatedlistblock' class="slds-select" id='relatedlistblock' onchange="getElementById('blocklabel').value=this.value;">
									<option value="no" selected>{'LBL_NO'|@gettranslatedString:$MODULE}</option>
									{foreach key=rlmname item=rllabel from=$NotBlockRelatedModules}
									<option value="{$rlmname}">{$rlmname|@gettranslatedString:$rlmname}</option>
									{/foreach}
								</select>
							</td>
						</tr>
						<!-- Save & Cancel buttons -->
						<tr class="slds-line-height--reset">
							<td align="center" colspan="2">
								<input type="button" name="save" value= "{$APP.LBL_SAVE_BUTTON_LABEL}" class="slds-button slds-button--small slds-button_success" onclick="getCreateCustomBlockForm('{$MODULE}','add');"/>&nbsp;
								<input type="button" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="slds-button slds-button--small slds-button--destructive" onclick= "fninvsh('addblock');" />
							</td>
						</tr>
					</table>

				</td>
			</tr>
		</table>
	</div>

</form>
