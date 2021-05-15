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

<form name="ConvertLead" id="ConvertLead" method="POST" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="module" value="Leads">
	<input type="hidden" name="transferToName" value="{$row.company}">
	<input type="hidden" name="record" value="{$UIINFO->getLeadId()}">
	<input type="hidden" name="action">
	<input type="hidden" name="current_user_id" value="{$UIINFO->getUserId()}'">
	<span id="convertLeadHeaderTitle" style="display:none;">
		<svg class="slds-button__icon slds-button__icon_large" aria-hidden="true">
			<use xlink: href="include/LD/assets/icons/action-sprite/svg/symbols.svg#lead_convert"></use>
		</svg>
		{'ConvertLead'|@getTranslatedString:$MODULE} : {$row.firstname} {$row.lastname}
	</span>
	{if $UIINFO->isModuleActive('Accounts') && $row.company neq '' }
		<fieldset class="slds-form-element slds-form-element_compound">
			<div class="slds-page-header">
			<div class="slds-page-header__row">
				<div class="slds-page-header__col-title">
				<div class="slds-media">
					<div class="slds-media__body">
					<div class="slds-page-header__name">
						<div class="slds-page-header__name-title">
						<h1>
							<span class="slds-page-header__title slds-truncate" title="{'SINGLE_Accounts'|@getTranslatedString:$MODULE}">
							<input type="checkbox" onclick="javascript:showHideStatus('account_block',null,null);" id="select_account" name="entities[]" value="Accounts" {if $row.company neq ''} checked {/if} />
							{'SINGLE_Accounts'|@getTranslatedString:$MODULE}
							</span>
						</h1>
						</div>
					</div>
					</div>
				</div>
				</div>
			</div>
			</div>
			<div class="slds-form-element slds-form-element__row slds-form-element_horizontal" id="account_block" style="margin-left:0.5rem;{if $row.company neq ''}display:block;{else}display:none;{/if}" >
				<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="accountname_display">
				{if $UIINFO->isMandatory('Accounts','accountname') eq true}<abbr class="slds-required" title="{'NTC_REQUIRED'|@getTranslatedString}">* </abbr>{/if}
				{'LBL_ACCOUNT_NAME'|@getTranslatedString:$MODULE}
				</label>
				<div class="slds-form-element__control slds-m-top_x-small">
					<input type="hidden" name="accountname" id="accountname" value="{$UIINFO->getMappedFieldValue('Accounts','accountname',0)}" module="Accounts" {if $UIINFO->isMandatory('Accounts','accountname') eq true}record="true"{/if}>
					<input type="text" name="accountname_display" id="accountname_display" class="slds-input" style="width:80%;border:1px solid #dddbda;" value="{$UIINFO->getMappedFieldValue('Accounts','accountname',0)}" readonly="readonly">
					<button class="slds-button slds-button_icon" title="{'LBL_SELECT'|@getTranslatedString}" type="button" onclick="return window.open('index.php?module=Accounts&action=Popup&html=Popup_picker&form=ConvertLead&forfield=accountname&srcmodule=Leads&forrecord=', 'convertleadcompany', cbPopupWindowSettings);">
						<svg class="slds-button__icon slds-button__icon_large" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
						</svg>
						<span class="slds-assistive-text">{'LBL_SELECT'|@getTranslatedString}</span>
					</button>
				</div>
				{if $UIINFO->isActive('industry','Accounts')}
				<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="industry">
				{if $UIINFO->isMandatory('Accounts','industry') eq true}<abbr class="slds-required" title="{'NTC_REQUIRED'|@getTranslatedString}">* </abbr>{/if}
				{'industry'|@getTranslatedString:$MODULE}
				</label>
				<div class="slds-form-element__control slds-m-top_x-small">
					{assign var=industry_map_value value=$UIINFO->getMappedFieldValue('Accounts','industry',1)}
					<select name="industry" class="slds-select slds-page-header__meta-text" style="width:80%;" module="Accounts" {if $UIINFO->isMandatory('Accounts','industry') eq true}record="true"{/if}>
						{foreach item=industry from=$UIINFO->getIndustryList() name=industryloop}
							<option value="{$industry.value}" {if $industry.value eq $industry_map_value}selected="selected"{/if}>{$industry.value|@getTranslatedString:$MODULE}</option>
						{/foreach}
					</select>
				</div>
				{/if}
			</div>
		</fieldset>
	{/if}
	{if $UIINFO->isModuleActive('Potentials')}
		<fieldset class="slds-form-element slds-form-element_compound">
			<div class="slds-page-header">
			<div class="slds-page-header__row">
				<div class="slds-page-header__col-title">
				<div class="slds-media">
					<div class="slds-media__body">
					<div class="slds-page-header__name">
						<div class="slds-page-header__name-title">
						<h1>
							<span class="slds-page-header__title slds-truncate" title="{'SINGLE_Potentials'|@getTranslatedString:$MODULE}">
							<input type="checkbox" onclick="javascript:showHideStatus('potential_block',null,null);"id="select_potential" name="entities[]" value="Potentials" {if $LeadConvertOpportunitySelected neq 'false'}checked{/if}>
							{'SINGLE_Potentials'|@getTranslatedString:$MODULE}
							</span>
						</h1>
						</div>
					</div>
					</div>
				</div>
				</div>
			</div>
			</div>
			<div class="slds-form-element slds-form-element__row slds-form-element_horizontal" id="potential_block" style="margin-left:0.5rem;{if $LeadConvertOpportunitySelected neq 'false'}display:block;{else}display:none;{/if}" >
				{if $UIINFO->isActive('potentialname','Potentials')}
				<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="potentialname">
				{if $UIINFO->isMandatory('Potentials','potentialname') eq true}<abbr class="slds-required" title="{'NTC_REQUIRED'|@getTranslatedString}">* </abbr>{/if}
				{'LBL_POTENTIAL_NAME'|@getTranslatedString:$MODULE}
				</label>
				<div class="slds-form-element__control slds-m-top_x-small">
					<input name="potentialname" id="potentialname" {if $UIINFO->isMandatory('Potentials','potentialname') eq true}record="true"{/if} module="Potentials" value="{$UIINFO->getMappedFieldValue('Potentials','potentialname',0)}" class="slds-input slds-page-header__meta-text" style="width:80%;" />
				</div>
				{/if}
				{if $UIINFO->isActive('closingdate','Potentials')}
				<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="closingdate">
				{if $UIINFO->isMandatory('Potentials','closingdate') eq true}<abbr class="slds-required" title="{'NTC_REQUIRED'|@getTranslatedString}">* </abbr>{/if}
				{'Expected Close Date'|@getTranslatedString:$MODULE}&nbsp;<span style="font-size:smaller"><em old="(yyyy-mm-dd)">({$DATE_FORMAT})</em></span>
				</label>
				<div class="slds-form-element__control slds-m-top_x-small">
					<input name="closingdate" class="slds-input slds-page-header__meta-text" {if $UIINFO->isMandatory('Potentials','closingdate') eq true}record="true"{/if} module="Potentials" id="jscal_field_closedate" type="text" tabindex="4" size="10" maxlength="10" value="{$UIINFO->getMappedFieldValue('Potentials','closingdate',1)}" style="width:80%;">
					<button class="slds-button slds-button_icon" title="{'LBL_SELECT'|@getTranslatedString}" type="button" id="jscal_trigger_closedate">
						<svg class="slds-button__icon slds-button__icon_large" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#date_input"></use>
						</svg>
						<span class="slds-assistive-text">{'LBL_SELECT'|@getTranslatedString}</span>
					</button>
					<script>getCalendarPopup('jscal_trigger_closedate','jscal_field_closedate','{$CAL_DATE_FORMAT}');</script>
				</div>
				{/if}
				{if $UIINFO->isActive('sales_stage','Potentials')}
				<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="sales_stage">
				{if $UIINFO->isMandatory('Potentials','sales_stage') eq true}<abbr class="slds-required" title="{'NTC_REQUIRED'|@getTranslatedString}">* </abbr>{/if}
				{'LBL_SALES_STAGE'|@getTranslatedString:$MODULE}
				</label>
				<div class="slds-form-element__control slds-m-top_x-small">
					{assign var=sales_stage_map_value value=$UIINFO->getMappedFieldValue('Potentials','sales_stage',1)}
					<select name="sales_stage" class="slds-select slds-page-header__meta-text" style="width:80%;" module="Potentials" {if $UIINFO->isMandatory('Potentials','sales_stage') eq true}record="true"{/if}>
						{foreach item=salesStage from=$UIINFO->getSalesStageList() name=salesStageLoop}
							<option value="{$salesStage.value}" {if $salesStage.value eq $sales_stage_map_value}selected="selected"{/if} >{$salesStage.value|@getTranslatedString:$MODULE}</option>
						{/foreach}
					</select>
				</div>
				{/if}
				{if $UIINFO->isActive('amount','Potentials')}
				<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="amount">
				{if $UIINFO->isMandatory('Potentials','amount') eq true}<abbr class="slds-required" title="{'NTC_REQUIRED'|@getTranslatedString}">* </abbr>{/if}
				{'Amount'|@getTranslatedString:$MODULE}
				</label>
				<div class="slds-form-element__control slds-m-top_x-small">
					<input name="amount" id="amount" {if $UIINFO->isMandatory('Potentials','amount') eq true}record="true"{/if} module="Potentials" value="{$UIINFO->getMappedFieldValue('Potentials','amount',0)}" class="slds-input slds-page-header__meta-text" style="width:80%;" />
				</div>
				{/if}
			</div>
		</fieldset>
	{/if}
	{if $UIINFO->isModuleActive('Contacts')}
		<fieldset class="slds-form-element slds-form-element_compound">
			<div class="slds-page-header">
			<div class="slds-page-header__row">
				<div class="slds-page-header__col-title">
				<div class="slds-media">
					<div class="slds-media__body">
					<div class="slds-page-header__name">
						<div class="slds-page-header__name-title">
						<h1>
							<span class="slds-page-header__title slds-truncate" title="{'SINGLE_Contacts'|@getTranslatedString:$MODULE}">
							<input type="checkbox" {if $LeadConvertContactSelected neq 'false'}checked{/if} onclick="javascript:showHideStatus('contact_block',null,null);" id="select_contact" name="entities[]" value="Contacts">
							{'SINGLE_Contacts'|@getTranslatedString:$MODULE}
							</span>
						</h1>
						</div>
					</div>
					</div>
				</div>
				</div>
			</div>
			</div>
			<div class="slds-form-element slds-form-element__row slds-form-element_horizontal" id="contact_block" style="margin-left:0.5rem;{if $LeadConvertContactSelected neq 'false'}display:block;{else}display:none;{/if}" >
				{if $UIINFO->isActive('lastname','Contacts')}
				<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="lastname">
				{if $UIINFO->isMandatory('Contacts','lastname') eq true}<abbr class="slds-required" title="{'NTC_REQUIRED'|@getTranslatedString}">* </abbr>{/if}
				{'Last Name'|@getTranslatedString:$MODULE}
				</label>
				<div class="slds-form-element__control slds-m-top_x-small">
					<input type="text" name="lastname" id="lastname" class="slds-input slds-page-header__meta-text" {if $UIINFO->isMandatory('Contacts','lastname') eq true}record="true"{/if} module="Contacts" value="{$UIINFO->getMappedFieldValue('Contacts','lastname',0)}" style="width:80%;" />
				</div>
				{/if}
				{if $UIINFO->isActive('firstname','Contacts')}
				<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="firstname">
				{if $UIINFO->isMandatory('Contacts','firstname') eq true}<abbr class="slds-required" title="{'NTC_REQUIRED'|@getTranslatedString}">* </abbr>{/if}
				{'First Name'|@getTranslatedString:$MODULE}
				</label>
				<div class="slds-form-element__control slds-m-top_x-small">
					<input type="text" name="firstname" id="firstname" class="slds-input slds-page-header__meta-text" module="Contacts" value="{$UIINFO->getMappedFieldValue('Contacts','firstname',0)}" {if $UIINFO->isMandatory('Contacts','firstname') eq true}record="true"{/if} style="width:80%;" />
				</div>
				{/if}
				{if $UIINFO->isActive('email','Contacts')}
				<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="email">
				{if $UIINFO->isMandatory('Contacts','email') eq true}<abbr class="slds-required" title="{'NTC_REQUIRED'|@getTranslatedString}">* </abbr>{/if}
				{'SINGLE_Emails'|@getTranslatedString:$MODULE}
				</label>
				<div class="slds-form-element__control slds-m-top_x-small">
					<input type="text" name="email" id="email" class="slds-input slds-page-header__meta-text" value="{$UIINFO->getMappedFieldValue('Contacts','email',0)}" {if $UIINFO->isMandatory('Contacts','email') eq true}record="true"{/if} module="Contacts" style="width:80%;" />
				</div>
				{/if}
			</div>
		</fieldset>
	{/if}
		<fieldset class="slds-form-element slds-form-element_compound">
			<div class="slds-page-header">
			<div class="slds-page-header__row">
				<div class="slds-page-header__col-title">
				<div class="slds-media">
					<div class="slds-media__body">
					<div class="slds-page-header__name">
						<div class="slds-page-header__name-title">
						<h1>
							<span class="slds-page-header__title slds-truncate" title="{'LBL_GENERAL_FIELDS'|@getTranslatedString:$MODULE}">
							{'LBL_GENERAL_FIELDS'|@getTranslatedString:$MODULE}
							</span>
						</h1>
						</div>
					</div>
					</div>
				</div>
				</div>
			</div>
			</div>
			<div class="slds-form-element slds-form-element__row slds-form-element_horizontal" id="contact_block" style="margin-left:0.5rem;{if $row.company neq ''}display:block;{else}display:none;{/if}" >

				<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="c_assigntype">
				{'LBL_LIST_ASSIGNED_USER'|@getTranslatedString:$MODULE}
				</label>
				<div class="slds-form-element__control slds-m-top_x-small slds-page-header__meta-text">
					<div class="slds-radio_button-group slds-m-bottom_xx-small">
						<span class="slds-button slds-radio_button">
							<input type="radio" name="c_assigntype" id="c_assigntypeu" value="U" onclick="javascript: c_toggleAssignType(this.value)" {$UIINFO->getUserSelected()} />
							<label class="slds-radio_button__label" for="c_assigntypeu">
								<span class="slds-radio_faux">{'LBL_USER'|@getTranslatedString:$MODULE}</span>
							</label>
						</span>
						{if $UIINFO->getOwnerList('group')|@count neq 0}
						<span class="slds-button slds-radio_button">
							<input type="radio" name="c_assigntype" id="c_assigntypeg" value="T" onclick="javascript: c_toggleAssignType(this.value)" {$UIINFO->getGroupSelected()} />
							<label class="slds-radio_button__label" for="c_assigntypeg">
								<span class="slds-radio_faux">{'LBL_GROUP'|@getTranslatedString:$MODULE}</span>
							</label>
						</span>
						{/if}
					</div>
					<span id="c_assign_user" style="display:{$UIINFO->getUserDisplay()}">
						<select name="c_assigned_user_id" class="slds-select slds-page-header__meta-text" style="width:80%;">
							{foreach item=user from=$UIINFO->getOwnerList('user') name=userloop}
								<option value="{$user.userid}" {if $user.selected eq true}selected="selected"{/if}>{$user.username}</option>
							{/foreach}
						</select>
					</span>
					<span id="c_assign_team" style="display:{$UIINFO->getGroupDisplay()}">
						{if $UIINFO->getOwnerList('group')|@count neq 0}
						<select name="c_assigned_group_id" class="slds-select slds-page-header__meta-text" style="width:80%;"
							{foreach item=group from=$UIINFO->getOwnerList('group') name=grouploop}
								<option value="{$group.groupid}" {if $group.selected eq true}selected="selected"{/if}>{$group.groupname}</option>
							{/foreach}
						</select>
						{/if}
					</span>
				</div>

				<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="transferto">
				{'LBL_TRANSFER_RELATED_RECORDS_TO'|@getTranslatedString:$MODULE}
				</label>
				<div class="slds-form-element__control slds-m-top_x-small slds-page-header__meta-text">
					<div class="slds-radio_button-group">
						{if $UIINFO->isModuleActive('Accounts') eq true && $row.company neq ''}
						<span class="slds-button slds-radio_button">
							<input type="radio" name="transferto" id="transfertoacc" value="Accounts" onclick="selectTransferTo('Accounts')" {if $UIINFO->isModuleActive('Contacts') neq true || $LeadConvertTransferToAccount eq 'true'}checked="checked"{/if} />
							<label class="slds-radio_button__label" for="transfertoacc">
								<span class="slds-radio_faux">{'SINGLE_Accounts'|@getTranslatedString:$MODULE}</span>
							</label>
						</span>
						{/if}
						{if $UIINFO->isModuleActive('Contacts') eq true}
						<span class="slds-button slds-radio_button">
							<input type="radio" name="transferto" id="transfertocon" value="Contacts" {if $LeadConvertTransferToAccount neq 'true'}checked="checked"{/if} onclick="selectTransferTo('Contacts')" />
							<label class="slds-radio_button__label" for="transfertocon">
								<span class="slds-radio_faux">{'SINGLE_Contacts'|@getTranslatedString:$MODULE}</span>
							</label>
						</span>
						{/if}
					</div>
				</div>
			</div>
		</fieldset>
</form>