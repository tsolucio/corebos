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

{assign var=row value=$UIINFO->getLeadInfo()}

<form name="ConvertLead" method="POST" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="module" value="Leads">
	<input type="hidden" name="transferToName" value="{$row.company}">
	<input type="hidden" name="record" value="{$UIINFO->getLeadId()}">
	<input type="hidden" name="action">
	<input type="hidden" name="parenttab" value="{$CATEGORY}">
	<input type="hidden" name="current_user_id" value="{$UIINFO->getUserId()}'">

	<div id="orgLay" style="display: block; z-index: 9999;" class="layerPopup">

		<!-- Convert Lead Title -->
		<table class="slds-table slds-no-row-hover" width="100%" style="border-bottom: 1px solid #d4d4d4;">
			<tr class="slds-text-title--header">
				<th scope="col">
					<div class="slds-truncate moduleName">
						<img src="{'Leads.gif'|@vtiger_imageurl:$THEME}">
						<b>{'ConvertLead'|@getTranslatedString:$MODULE} : {$row.firstname} {$row.lastname}</b>
					</div>
				</th>
				<th scope="col" style="padding: .5rem;">
					<div class="slds-truncate">
						<a href="javascript:fninvsh('orgLay');">
							<img src="{'close.gif'|@vtiger_imageurl:$THEME}" align="absmiddle">
						</a>
					</div>
				</th>
			</tr>
		</table>

		<!-- Module options -->
		<table border=0 cellspacing=0 cellpadding=5 width=95% align=center>

			{if $UIINFO->isModuleActive('Accounts') && $row.company neq '' }
				<tr>
					<td class="small" >
						<table border="0" cellspacing="0" cellpadding="0" width="95%" align="center" bgcolor="white">
							<tr>
								<td colspan="4" class="">
									<!-- Organization Option -->
									<div class="forceRelatedListSingleContainer">
										<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
											<div class="slds-card__header slds-grid">
												<!-- Title -->
												<header class="slds-media slds-media--center slds-has-flexi-truncate">
													<div class="slds-media__body">
														<h2>
															<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small">
																<span class="slds-checkbox">
																	<input type="checkbox" onclick="javascript:showHideStatus('account_block',null,null);" id="select_account" name="entities[]" value="Accounts" {if $row.company neq ''} checked {/if} />
																	<label class="slds-checkbox__label" for="select_account">
																		<span class="slds-checkbox--faux"></span>
																	</label>
																	<span class="slds-form-element__label"><b>{'SINGLE_Accounts'|@getTranslatedString:$MODULE}</b></span>
																</span>
															</span>
														</h2>
													</div>
												</header>
											</div>
										</article>
									</div>
									<!-- Organization name and industry options -->
									<div id="account_block" class="slds-truncate" {if $row.company neq ''} style="display:block;" {else} style="display:none;" {/if}>
										<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table convertLeads-options-table">
											<tr>
												<td align="right" width="50%" class="dvtCellLabel">{if $UIINFO->isMandatory('Accounts','accountname') eq true}<font color="red">*</font>{/if}{'LBL_ACCOUNT_NAME'|@getTranslatedString:$MODULE}</td>
												<td class="dvtCellInfo">
													<input type="hidden" name="accountname" id="txtbox_accountname" value="{$UIINFO->getMappedFieldValue('Accounts','accountname',0)}" module="Accounts" {if $UIINFO->isMandatory('Accounts','accountname') eq true}record="true"{/if}>
													<input type="text" name="accountname_display" id="accountname_display" class="slds-input" style="width:90%;" value="{$UIINFO->getMappedFieldValue('Accounts','accountname',0)}" readonly="readonly">
													<img src="themes/softed/images/select.gif" tabindex="" alt="{'LBL_SELECT'|@getTranslatedString}" title="{'LBL_SELECT'|@getTranslatedString}" onclick="return window.open('index.php?module=Accounts&action=Popup&html=Popup_picker&form=ConvertLead&forfield=accountname&srcmodule=Leads&forrecord=','convertleadcompany','width=640,height=602,resizable=0,scrollbars=0,top=150,left=200');" style="cursor:hand;cursor:pointer" align="absmiddle">
												</td>
											</tr>
											{if $UIINFO->isActive('industry','Accounts')}
											<tr>
												<td align="right" class="dvtCellLabel">{if $UIINFO->isMandatory('Accounts','industry') eq true}<font color="red">*</font>{/if}{'industry'|@getTranslatedString:$MODULE}</td>
												<td class="dvtCellInfo">
														{assign var=industry_map_value value=$UIINFO->getMappedFieldValue('Accounts','industry',1)}
														<select name="industry" class="slds-select" module="Accounts" {if $UIINFO->isMandatory('Accounts','industry') eq true}record="true"{/if}>
															{foreach item=industry from=$UIINFO->getIndustryList() name=industryloop}
																<option value="{$industry.value}" {if $industry.value eq $industry_map_value}selected="selected"{/if}>{$industry.value|@getTranslatedString:$MODULE}</option>
															{/foreach}
														</select>
												</td>
											</tr>
											{/if}
										</table>
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			{/if}

			{if $UIINFO->isModuleActive('Potentials')}
				<tr>
					<td class="small">
						<table border="0" cellspacing="0" cellpadding="0" width="95%" align="center" bgcolor="white">
							<tr>
								<td colspan="4">
									<!-- Opportunity/Potentials Options -->
									<div class="forceRelatedListSingleContainer">
										<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
											<div class="slds-card__header slds-grid">
												<!-- Title -->
												<header class="slds-media slds-media--center slds-has-flexi-truncate">
													<div class="slds-media__body">
														<h2>
															<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small">
																<span class="slds-checkbox">
																	<input type="checkbox" onclick="javascript:showHideStatus('potential_block',null,null);" id="select_potential" name="entities[]" value="Potentials" {if $LeadConvertOpportunitySelected neq 'false'}checked{/if}>
																	<label class="slds-checkbox__label" for="select_potential">
																		<span class="slds-checkbox--faux"></span>
																	</label>
																	<span class="slds-form-element__label"><b>{'SINGLE_Potentials'|@getTranslatedString:$MODULE}</b></span>
																</span>
																{if $LeadConvertOpportunitySelected neq 'false'}
																<script type="text/javascript">showHideStatus('potential_block',null,null);</script>
																{/if}
															</span>
														</h2>
													</div>
												</header>
											</div>
										</article>
									</div>
									<!-- Opportunity/Potentials body options -->
									<div id="potential_block" class="slds-truncate" style="display:none;">
										<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table convertLeads-options-table">
											{if $UIINFO->isActive('potentialname','Potentials')}
											<tr>
												<td align="right" width="50%" class="dvtCellLabel">{if $UIINFO->isMandatory('Potentials','potentialname') eq true}<font color="red">*</font>{/if}{'LBL_POTENTIAL_NAME'|@getTranslatedString:$MODULE}</td>
												<td class="dvtCellInfo"><input class="slds-input" name="potentialname" id="potentialname" {if $UIINFO->isMandatory('Potentials','potentialname') eq true}record="true"{/if} module="Potentials" value="{$UIINFO->getMappedFieldValue('Potentials','potentialname',0)}" class="detailedViewTextBox" /></td>
											</tr>
											{/if}
											{if $UIINFO->isActive('closingdate','Potentials')}
											<tr>
												<td align="right" class="dvtCellLabel">{if $UIINFO->isMandatory('Potentials','closingdate') eq true}<font color="red">*</font>{/if}{'Expected Close Date'|@getTranslatedString:$MODULE}</td>
												<td class="dvtCellInfo">
													<input class="slds-input" name="closingdate" {if $UIINFO->isMandatory('Potentials','closingdate') eq true}record="true"{/if} module="Potentials" id="jscal_field_closedate" type="text" tabindex="4" size="10" maxlength="10" value="{$UIINFO->getMappedFieldValue('Potentials','closingdate',1)}">
													<br/>
													<img src="{'miniCalendar.gif'|@vtiger_imageurl:$THEME}" id="jscal_trigger_closedate" style="vertical-align: middle;width: 16px;">
													<font size=1><em old="(yyyy-mm-dd)">({$DATE_FORMAT})</em></font>
													<script id="conv_leadcal">
														getCalendarPopup('jscal_trigger_closedate','jscal_field_closedate','{$CAL_DATE_FORMAT}')
													</script>
												</td>
											</tr>
											{/if}
											{if $UIINFO->isActive('sales_stage','Potentials')}
											<tr>
												<td align="right" class="dvtCellLabel">{if $UIINFO->isMandatory('Potentials','sales_stage') eq true}<font color="red">*</font>{/if}{'LBL_SALES_STAGE'|@getTranslatedString:$MODULE}</td>
												<td class="dvtCellInfo">
													{assign var=sales_stage_map_value value=$UIINFO->getMappedFieldValue('Potentials','sales_stage',1)}
													<select name="sales_stage" class="slds-select" {if $UIINFO->isMandatory('Potentials','sales_stage') eq true}record="true"{/if} module="Potentials">
														{foreach item=salesStage from=$UIINFO->getSalesStageList() name=salesStageLoop}
															<option value="{$salesStage.value}" {if $salesStage.value eq $sales_stage_map_value}selected="selected"{/if} >{$salesStage.value|@getTranslatedString:$MODULE}</option>
														{/foreach}
													</select>
												</td>
											</tr>
											{/if}
											{if $UIINFO->isActive('amount','Potentials')}
											<tr>
												<td align="right" class="dvtCellLabel">{if $UIINFO->isMandatory('Potentials','amount') eq true}<font color="red">*</font>{/if}{'Amount'|@getTranslatedString:$MODULE}</td>
												<td class="dvtCellInfo"><input type="text" name="amount" class="slds-input" {if $UIINFO->isMandatory('Potentials','amount') eq true}record="true"{/if} module="Potentials" value="{$UIINFO->getMappedFieldValue('Potentials','amount',1)}"></input></td>
											</tr>
											{/if}
										</table>
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			{/if}

			{if $UIINFO->isModuleActive('Contacts')}
				<tr>
					<td class="small">
						<table border="0" cellspacing="0" cellpadding="0" width="95%" align="center" bgcolor="white">
							<tr>
								<td colspan="4">
									<!-- Contacts options section-->
									<div class="forceRelatedListSingleContainer">
										<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
											<div class="slds-card__header slds-grid">
												<!-- Title -->
												<header class="slds-media slds-media--center slds-has-flexi-truncate">
													<div class="slds-media__body">
														<h2>
															<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small">
																<span class="slds-checkbox">
																	<input type="checkbox" checked="checked" onclick="javascript:showHideStatus('contact_block',null,null);" id="select_contact" name="entities[]" value="Contacts"/>
																	<label class="slds-checkbox__label" for="select_contact">
																		<span class="slds-checkbox--faux"></span>
																	</label>
																	<span class="slds-form-element__label"><b>{'SINGLE_Contacts'|@getTranslatedString:$MODULE}</b></span>
																</span>
															</span>
														</h2>
													</div>
												</header>
											</div>
										</article>
									</div>
									<!-- Contacts options block -->
									<div id="contact_block" style="display:block;" >
										<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table convertLeads-options-table">
											<tr>
												<td align="right" width="50%" class="dvtCellLabel">{if $UIINFO->isMandatory('Contacts','lastname') eq true}<font color="red">*</font>{/if}{'Last Name'|@getTranslatedString:$MODULE}</td>
												<td class="dvtCellInfo"><input type="text" name="lastname" class="slds-input" {if $UIINFO->isMandatory('Contacts','lastname') eq true}record="true"{/if} module="Contacts" value="{$UIINFO->getMappedFieldValue('Contacts','lastname',0)}"></td>
											</tr>
											{if $UIINFO->isActive('firstname','Contacts')}
											<tr>
												<td align="right" width="50%" class="dvtCellLabel">{if $UIINFO->isMandatory('Contacts','firstname') eq true}<font color="red">*</font>{/if}{'First Name'|@getTranslatedString:$MODULE}</td>
												<td class="dvtCellInfo"><input type="text" name="firstname" class="slds-input" module="Contacts" style="width: 100%;" value="{$UIINFO->getMappedFieldValue('Contacts','firstname',0)}" {if $UIINFO->isMandatory('Contacts','firstname') eq true}record="true"{/if} ></td>
											</tr>
											{/if}
											{if $UIINFO->isActive('email','Contacts')}
											<tr>
												<td align="right" width="50%" class="dvtCellLabel">{if $UIINFO->isMandatory('Contacts','email') eq true}<font color="red">*</font>{/if}{'SINGLE_Emails'|@getTranslatedString:$MODULE}</td>
												<td class="dvtCellInfo"><input type="text" name="email" class="slds-input" value="{$UIINFO->getMappedFieldValue('Contacts','email',0)}" {if $UIINFO->isMandatory('Contacts','email') eq true}record="true"{/if} module="Contacts"></td>
											</tr>
											{/if}
										</table>
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
			{/if}

			<tr>
				<td class="small">
					<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table convertLeads-options-table">
						<tr>
							<td align="right" class="dvtCellLabel" width="50%" style="border-top:1px solid #DEDEDE;">{'LBL_LIST_ASSIGNED_USER'|@getTranslatedString:$MODULE}</td>
							<td class="dvtCellInfo" width="50%" style="border-top:1px solid #DEDEDE;">
								<span class="slds-radio">
									<input type="radio" name="c_assigntype" value="U" id="u" onclick="javascript: c_toggleAssignType(this.value)" {$UIINFO->getUserSelected()} />
									<label class="slds-radio__label" for="u">
										<span class="slds-radio--faux"></span>
									</label>
									<span class="slds-form-element__label"><b>{'LBL_USER'|@getTranslatedString:$MODULE}</b></span>
								</span>
								{if $UIINFO->getOwnerList('group')|@count neq 0}
								<span class="slds-radio">
									<input type="radio" name="c_assigntype" value="T" id="t" onclick="javascript: c_toggleAssignType(this.value)" {$UIINFO->getGroupSelected()} />
									<label class="slds-radio__label" for="t">
										<span class="slds-radio--faux"></span>
									</label>
									<span class="slds-form-element__label"><b>{'LBL_GROUP'|@getTranslatedString:$MODULE}</b></span>
								</span>
								{/if}
								<span id="c_assign_user" style="display:{$UIINFO->getUserDisplay()}">
									<select name="c_assigned_user_id" class="slds-select">
										{foreach item=user from=$UIINFO->getOwnerList('user') name=userloop}
											<option value="{$user.userid}" {if $user.selected eq true}selected="selected"{/if}>{$user.username}</option>
										{/foreach}
									</select>
								</span>
								<span id="c_assign_team" style="display:{$UIINFO->getGroupDisplay()}">
									{if $UIINFO->getOwnerList('group')|@count neq 0}
									<select name="c_assigned_group_id" class="slds-select">
										{foreach item=group from=$UIINFO->getOwnerList('group') name=grouploop}
											<option value="{$group.groupid}" {if $group.selected eq true}selected="selected"{/if}>{$group.groupname}</option>
										{/foreach}
									</select>
									{/if}
								</span>
							</td>
						</tr>
						<tr>
							<td align="right" class="dvtCellLabel" width="50%">{'LBL_TRANSFER_RELATED_RECORDS_TO'|@getTranslatedString:$MODULE}</td>
							<td class="dvtCellInfo" width="50%">
								{if $UIINFO->isModuleActive('Accounts') eq true && $row.company neq ''}
									<span class="slds-radio">
										<input type="radio" name="transferto" id="transfertoacc" value="Accounts" onclick="selectTransferTo('Accounts')" {if $UIINFO->isModuleActive('Contacts') neq true || $LeadConvertTransferToAccount eq 'true'}checked="checked"{/if} />
										<label class="slds-radio__label" for="transfertoacc">
											<span class="slds-radio--faux"></span>
										</label>
										<span class="slds-form-element__label"><b>{'SINGLE_Accounts'|@getTranslatedString:$MODULE}</b></span>
									</span>
								{/if}
								{if $UIINFO->isModuleActive('Contacts') eq true}
									<span class="slds-radio">
										<input type="radio" name="transferto" id="transfertocon" value="Contacts" {if $LeadConvertTransferToAccount neq 'true'}checked="checked"{/if} onclick="selectTransferTo('Contacts')" />
										<label class="slds-radio__label" for="transfertocon">
											<span class="slds-radio--faux"></span>
										</label>
										<span class="slds-form-element__label"><b>{'SINGLE_Contacts'|@getTranslatedString:$MODULE}</b></span>
									</span>
								{/if}
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport" style="background-color: #f7f9fb;">
			<tr>
				<td align="center"  style="padding: 5px;">
					<input name="Save" value="{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString:$MODULE}" onclick="javascript:this.form.action.value='LeadConvertToEntities'; return verifyConvertLeadData(ConvertLead)" type="submit"  class="slds-button slds-button--small slds-button_success">&nbsp;&nbsp;
					<input type="button" name=" Cancel " value="{'LBL_CANCEL_BUTTON_LABEL'|@getTranslatedString:$MODULE}" onClick="hide('orgLay')" class="slds-button slds-button--small slds-button--destructive">
				</td>
			</tr>
		</table>
	</div>
</form>
<script>jQuery('#orgLay').draggable();</script>