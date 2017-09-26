{************************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
<div class="slds-table--scoped">
		<ul class="slds-tabs--scoped__nav" role="tablist" style="margin-bottom: 0;">
			<li class="slds-tabs--scoped__item active" role="presentation" style="border-top-left-radius: 5px;">
				<a href="javascript:void(0);" class="slds-tabs--scoped__link dvHeaderText moduleName" id="mail_fldrname" role="tab" tabindex="0" aria-selected="true" aria-controls="tab--scoped-1">{'LBL_Drafts'|@getTranslatedString}</a>
			</li>
		</ul>
		<div id="tab--scoped-1" role="tabpanel" aria-labelledby="tab--scoped-1__item" class="slds-tabs--scoped__content slds-truncate">
			<div class=" mm_outerborder" id="email_con" name="email_con" style="border:none;">
				<table class="slds-table slds-no-row-hover slds-table-moz detailview_table">
					<tr class="blockStyleCss">
						<td class="detailViewContainer" valign="top">
							{if $FOLDER->mails()}
							<div class="forceRelatedListSingleContainer">
								<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
									<div class="slds-card__header slds-grid">
										<header class="slds-media slds-media--center slds-has-flexi-truncate">
											<div class="slds-media__body" style="text-align: right;">
												<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small">
													{if $FOLDER->hasPrevPage()}
														<a href="#{$FOLDER->name()}/page/{$FOLDER->pageCurrent(-1)}" onclick="MailManager.folder_drafts({$FOLDER->pageCurrent(-1)});">
															<img border="0" src="modules/Webmails/images/previous.gif" title="{'LBL_Previous'|@getTranslatedString}" style="vertical-align: bottom;">
														</a>
													{/if}
													<b>{$FOLDER->pageInfo()}</b>
													{if $FOLDER->hasNextPage()}
														<a href="#{$FOLDER->name()}/page/{$FOLDER->pageCurrent(1)}" onclick="MailManager.folder_drafts({$FOLDER->pageCurrent(1)});">
															<img border="0" src="modules/Webmails/images/next.gif" title="{'LBL_Next'|@getTranslatedString}" style="vertical-align: bottom;">
														</a>
													{/if}
												</span>
											</div>
										</header>
									</div>
								</article>
							</div>

							<div class="slds-truncate">
								<table class="slds-table slds-no-row-hover slds-table_resizable-cols slds-table--fixed-layout ld-font">
									<thead>
										<tr>
											<th scope="col" class="slds-text-align--center" style="width: 3.25rem; text-align: center;">
												<div class="slds-th_action slds-th__action_form">
													<span class="slds-checkbox">
														<input align="left" type="checkbox" class='small' name="selectall" id="parentCheckBox" onClick='MailManager.toggleSelect(this.checked,"mc_box");'/>
														<label class="slds-checkbox__label" for="parentCheckBox">
															<span class="slds-checkbox--faux"></span>
														</label>
													</span>
												</div>
												<input type=button class='slds-button slds-button--small slds-button--destructive' onclick="MailManager.massMailDelete('__vt_drafts');" name="{'LBL_Delete'|@getTranslatedString}" value="{'LBL_Delete'|@getTranslatedString}" />
											</th>
											<td class="moduleName" align="right">{'LBL_Search'|@getTranslatedString}
												<input type="text" id='search_txt' class='slds-input' style="width: 30%;" />&nbsp;{'LBL_IN'|@getTranslatedString}
												<select class='slds-select' id="search_type" style="width: 20%;">
													{foreach item=label key=value from=$SEARCHOPTIONS}
														<option value="{$value}" >{$label|@getTranslatedString}</option>
													{/foreach}
												</select>
												<input type=button class="slds-button slds-button--small slds-button_success" onclick="MailManager.search_drafts();" value="{'LBL_FIND'|@getTranslatedString}" id="mm_search"/>
											</td>
										</tr>
									</thead>
										
									<tbody>
										<tr>
											<td colspan="2" style="padding: 0;">
												<table class="slds-table slds-table--bordered slds-table--fixed-layout ld-font">
													{foreach item=MAIL from=$MAILS}
													<tr style="cursor: pointer" class="mm_lvtColData mm_normal" id="_mailrow_{$MAIL.id}" >
														<td width="3%">
															<span class="slds-checkbox">
																<input type='checkbox' value="{$MAIL.id}" id="{$MAIL.id}" name='mc_box' class='small' onclick="MailManager.toggleSelectMail(this.checked, this);">
																<label class="slds-checkbox__label" for="{$MAIL.id}">
																	<span class="slds-checkbox--faux"></span>
																</label>
															</span>
														</td>
														<td width="27%" onclick="MailManager.mail_draft('{$MAIL.id}')">{$MAIL.saved_toid}</td>
														<td onclick="MailManager.mail_draft('{$MAIL.id}')"> {$MAIL.subject}</td>
														<td width="17%" align="right" onclick="MailManager.mail_draft('{$MAIL.id}')">{$MAIL.date_start}</td>
													</tr>
													{/foreach}
												</table>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							{/if}
						</td>
					</tr>
				</table>

				{if $FOLDER->mails()}
					
				{/if}

				{if $FOLDER->mails() eq null}
					<table  cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr>
							<td><a href='javascript:void(0);' onclick="MailManager.folder_drafts();"><b>{'LBL_Drafts'|@getTranslatedString}</b></a></td>
						</tr>
						<tr>
							<td>{'LBL_No_Mails_Found'|@getTranslatedString}</td>
						</tr>
					</table>
				{/if}
			</div>
		</div>
</div>
