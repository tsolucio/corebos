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
<div id="pickListContents">

	<table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
		<tr>
			<td>
				<div class="forceRelatedListSingleContainer">
					<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
						<div class="slds-card__header slds-grid">
							<header class="slds-media slds-media--center slds-has-flexi-truncate">
								<div class="slds-media__body">
									<h2>
										<span class="slds-text-title--caps slds-truncate actionLabel">
											<strong>{$MOD.LBL_SELECT_PICKLIST}</strong>
										</span>
									</h2>
								</div>
							</header>
							<div class="slds-no-flex">
								<div class="actionsContainer">
									<input type="button" value="{'LBL_ADD_BUTTON'|@getTranslatedString}" name="add" class="slds-button slds-button--small slds-button_success" onclick="showAddDiv();">
									<input type="button" value="{'LBL_EDIT_BUTTON'|@getTranslatedString}" name="del" class="slds-button slds-button--small slds-button--brand" onclick="showEditDiv();">
									<input type="button" value="{'LBL_DELETE_BUTTON'|@getTranslatedString}" name="del" class="slds-button slds-button--small slds-button--destructive" onclick="showDeleteDiv();">
								</div>
							</div>
						</div>
							<div class="slds-card__body slds-card__body--inner">
								<div class="commentData">

									<select name="avail_picklists" id="allpick" class="slds-select">
										{foreach key=fld_nam item=fld_lbl from=$ALL_LISTS}
											<option value="{$fld_nam}">{$fld_lbl|getTranslatedString:$MODULE}</option>
										{/foreach}
									</select>

									<table class="tableHeading" border="0" cellpadding="7" cellspacing="0" width="100%" style="padding: .3rem 0;">
										<tr>
											<td>

												<div class="forceRelatedListSingleContainer">
													<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
														<div class="slds-card__header slds-grid">
															<header class="slds-media slds-media--center slds-has-flexi-truncate">
																<div class="slds-media__body">
																	<h2>
																		<span class="slds-text-title--caps slds-truncate actionLabel">
																			<strong>{$MOD.LBL_PICKLIST_AVAIL} {$MODULE|@getTranslatedString:$MODULE} {$MOD.LBL_FOR}</strong>
																		</span>
																	</h2>
																</div>
															</header>
														</div>
															<div class="slds-card__body slds-card__body--inner">
																<div class="commentData">

																	<select name="pickrole" id="pickid" class="slds-select" onChange="showPicklistEntries('{$MODULE}' );">
																		{foreach key=roleid item=role from=$ROLE_LISTS}
																			{if $SEL_ROLEID eq $roleid}
																				<option value="{$roleid}" selected>{$role}</option>
																			{else}
																				<option value="{$roleid}">{$role}</option>
																			{/if}
																		{/foreach}
																	</select>

																	<font class="slds-text-color--error">* {$MOD_PICKLIST.LBL_DISPLAYED_VALUES}</font>

																	<table class="listTable slds-table slds-no-row-hover detailview_table">
																		{foreach item=picklists from=$PICKLIST_VALUES}
																			<tr>
																				{foreach item=picklistfields from=$picklists}
																					{if $picklistfields neq ''}
																						<td class="listTableTopButtons dvtCellLabel text-left" width="25%">
																							{if !empty($TEMP_MOD[$picklistfields.fieldlabel])}
																								<b>{$TEMP_MOD[$picklistfields.fieldlabel]}</b>
																							{else}
																								<b>{$picklistfields.fieldlabel}</b>
																							{/if}
																						</td>
																						<td class="listTableTopButtons dvtCellInfo" valign="top" width="5%">
																							<input type="button" value="{$MOD_PICKLIST.LBL_ASSIGN_BUTTON}" class="slds-button slds-button--small slds-button_success" onclick="assignPicklistValues('{$MODULE}','{$picklistfields.fieldname}','{$picklistfields.fieldlabel}');" >
																						</td>
																					{else}
																						<td class="listTableTopButtons small" colspan="2">&nbsp;</td>
																					{/if}
																				{/foreach}
																			</tr>
																			<tr>
																				{foreach item=picklistelements from=$picklists}
																					{if $picklistelements neq ''}
																						<td class="dvtCellInfo" colspan="2" valign="top">
																						<ul style="list-style-type: none;">
																							{foreach item=elements from=$picklistelements.value}
																								{if !empty($TEMP_MOD[$elements])}
																									<li>{$TEMP_MOD[$elements]}</li>
																								{elseif !empty($MOD_PICKLIST[$elements])}
																									<li>{$MOD_PICKLIST[$elements]}</li>
																								{else}
																									<li>{$elements}</li>
																								{/if}
																							{/foreach}
																						</ul>
																						</td>
																					{else}
																						<td colspan="2">&nbsp;</td>
																					{/if}
																				{/foreach}
																			</tr>
																		{/foreach}
																	</table>

																</div>
															</div>
													</article>
												</div><!-- /.forceRelatedListSingleContainer -->

											</td>
										</tr>
									</table><!-- /.tableHeading -->

								</div>
							</div>
					</article>
				</div><!-- /.forceRelatedListSingleContainer -->
			</td>
		</tr>
	</table><!-- /.tableHeading -->

</div>
