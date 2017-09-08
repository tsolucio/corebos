<!-- Lighting Design Tab Conrols by Endrit -->
<div class="slds-truncate">
	<table class="slds-table slds-no-row-hover dvtContentSpace">
		<tr>
			<td valign="top" style="padding: 0;">
				<div class="slds-table--scoped">
					<!-- Tab Controls -->
						<ul class="slds-tabs--scoped__nav" role="tablist" style="margin-bottom: 0;">
							<li class="slds-tabs--scoped__item active" id="cellTabInvite" onClick="switchClass('cellTabInvite','on');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','off');hide('addEventAlarmUI');showBlock('addEventInviteUI');hide('addEventRepeatUI');" role="presentation" style="border-top-left-radius: .25rem;">
								<a href="javascript:doNothing()" class="slds-tabs--scoped__link " role="tab" tabindex="0" aria-selected="true">{$MOD.LBL_INVITE}</a>
							</li>
							{if $LABEL.reminder_time neq ''}
							<li class="slds-tabs--scoped__item" id="cellTabAlarm" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','on');switchClass('cellTabRepeat','off');showBlock('addEventAlarmUI');hide('addEventInviteUI');hide('addEventRepeatUI');" role="presentation">
								<a href="javascript:doNothing()" class="slds-tabs--scoped__link" role="tab" tabindex="-1" aria-selected="false" ><b>{$MOD.LBL_REMINDER}</b></a>
							</li>
							{/if}

							{if $LABEL.recurringtype neq ''}
							<li class="slds-tabs--scoped__item" id="cellTabRepeat" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','on');hide('addEventAlarmUI');hide('addEventInviteUI');showBlock('addEventRepeatUI');" role="presentation">
								<a href="javascript:doNothing()" class="slds-tabs--scoped__link" role="tab" tabindex="-1" aria-selected="false" ><b>{$MOD.LBL_REPEAT}</b></a>
							</li>
							{/if}
						</ul>
					<!-- End Tab Controls -->

					<!-- Invite UI -->
						<div id="addEventInviteUI" role="tabpanel" aria-labelledby="tab--scoped-1__item" class="slds-tabs--scoped__content slds-truncate" style="display:block;">
							<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table">
								<tr class="slds-line-height--reset">
									<td class="dvtCellLabel text-left" width="45%"><b>{$MOD.LBL_USERS}</b></td>
									<td class="dvtCellLabel" width="10%">&nbsp;</td>
									<td class="dvtCellLabel text-left" width="45%"><b>{$MOD.LBL_SEL_USERS}</b></td>
								</tr>
								<tr class="slds-line-height--reset">
									<td class="dvtCellInfo" width="45%">
										<select name="availableusers" id="availableusers" class="slds-select" size=5 multiple style="height:100px;width:100%">
											{foreach item=username key=userid from=$USERSLIST}
												{if $userid != ''}
													<option value="{$userid}">{$username}</option>
												{/if}
											{/foreach}
										</select>
									</td>
									<td class="dvtCellInfo" width="10%">
										<input type=button value="{$MOD.LBL_ADD_BUTTON} >>" class="slds-button slds-button--small slds-button_success slds-width" onClick="incUser('availableusers','selectedusers');formSelectColumnString('inviteesid','selectedusers');">
										<br>
										<input type=button value="<< {$MOD.LBL_RMV_BUTTON} " class="slds-button slds-button--small slds-button--destructive slds-width" onClick="rmvUser('selectedusers');formSelectColumnString('inviteesid','selectedusers');">
									</td>
									<td class="dvtCellInfo" width="45%">
										<input type=hidden name="inviteesid" id="inviteesid" value="">
										<select name="selectedusers" id="selectedusers" class="slds-select" size=5 multiple style="height:100px;width:100%">
											{foreach item=username key=userid from=$INVITEDUSERS}
												{if $userid != ''}
													<option value="{$userid}">{$username}</option>
												{/if}
											{/foreach}
										</select>
										<div align=left>{$MOD.LBL_SELUSR_INFO}</div>
									</td>
								</tr>
							</table>
							<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table"><table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table">
								<tr class="slds-line-height--reset">
									<td class="dvtCellLabel" width="100%" class="text-left">
										<p>{$MOD.LBL_INVITE_INST1}</p>
										<p>{$MOD.LBL_INVITE_INST2}</p>
									</td>
								</tr>
							</table>
						</div>
					<!-- End Invite UI -->

					<!-- Reminder UI -->
						<div id="addEventAlarmUI" role="tabpanel" aria-labelledby="tab--scoped-2__item" class="slds-tabs--scoped__content slds-truncate" style="display:none;">
							{if $LABEL.reminder_time neq ''}
								<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table">
									{assign var=secondval value=$secondvalue.reminder_time} {assign var=check value=$secondval[0]} {assign var=yes_val value=$secondval[1]} {assign var=no_val value=$secondval[2]}
									<tr class="slds-line-height--reset">
										<td class="dvtCellLabel" width="30%"><b>{$MOD.LBL_SENDREMINDER}</b></td>
										<td class="dvtCellInfo" width="70%">
											{if $check eq 'CHECKED'} {assign var=reminstyle value='style="display:block;width:100%"'}
												<span class="slds-radio">
													<input type="radio" name="set_reminder" id="radio-yes1" value="Yes" {$check} onClick="showBlock('reminderOptions')">
													<label class="slds-radio__label" for="radio-yes1">
														<span class="slds-radio--faux"></span>
														<span class="slds-form-element__label">{$yes_val}</span>
													</label>
												</span>
												<span class="slds-radio">
													<input type="radio" name="set_reminder" id="radio-no1" value="No" onClick="fnhide('reminderOptions')">
													<label class="slds-radio__label" for="radio-no1">
														<span class="slds-radio--faux"></span>
														<span class="slds-form-element__label">{$no_val}</span>
													</label>
												</span>
											{else} {assign var=reminstyle value='style="display:none;width:100%"'}
												<span class="slds-radio">
													<input type="radio" name="set_reminder" id="radio-yes2" value="Yes" onClick="showBlock('reminderOptions')">
													<label class="slds-radio__label" for="radio-yes2">
														<span class="slds-radio--faux"></span>
														<span class="slds-form-element__label">{$yes_val}</span>
													</label>
												</span>
												<span class="slds-radio">
													<input type="radio" name="set_reminder" id="radio-no2" value="No" checked onClick="fnhide('reminderOptions')">
													<label class="slds-radio__label" for="radio-no2">
														<span class="slds-radio--faux"></span>
														<span class="slds-form-element__label">{$no_val}</span>
													</label>
												</span>
											{/if}
										</td>
									</tr>
								</table>

								<div id="reminderOptions" {$reminstyle}>
									<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table">
										<tr class="slds-line-height--reset">
											<td class="dvtCellLabel" width="30%"><b>{$MOD.LBL_RMD_ON} : </b></td>
											<td class="dvtCellInfo" width="70%">
												{foreach item=val_arr from=$ACTIVITYDATA.reminder_time} {assign var=start value=$val_arr[0]} {assign var=end value=$val_arr[1]} {assign var=sendname value=$val_arr[2]} {assign var=disp_text value=$val_arr[3]} {assign var=sel_val value=$val_arr[4]}
													<select name="{$sendname}" class="slds-select" style="width:40px;">
														{section name=reminder start=$start max=$end loop=$end step=1 } {if $smarty.section.reminder.index eq $sel_val}
														<OPTION VALUE="{$smarty.section.reminder.index}" SELECTED>{$smarty.section.reminder.index}</OPTION>
														{else}
														<OPTION VALUE="{$smarty.section.reminder.index}">{$smarty.section.reminder.index}</OPTION>
														{/if} {/section}
													</select>&nbsp;{$disp_text}
												{/foreach}
											</td>
										</tr>
									</table>
								</div>
							{/if}
						</div>
					<!-- End Reminder UI -->

					<!-- Repeat UI -->
						<div id="addEventRepeatUI" role="tabpanel" aria-labelledby="tab--scoped-3__item" class="slds-tabs--scoped__content slds-truncate" style="display:none;">
							{if $LABEL.recurringtype neq ''}
								<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table">
									<tr class="slds-line-height--reset">
										<td class="dvtCellLabel" width="30%"><b>{$MOD.LBL_REPEAT}</b></td>
										<td style="border:none;" width="70%">
											<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table">
												<tr class="slds-line-height--reset">
													<td class="dvtCellInfo" width="30%">
														{if $ACTIVITYDATA.recurringcheck eq 'Yes'} {assign var=rptstyle value='style="display:block"'}
															{if $ACTIVITYDATA.eventrecurringtype eq 'Daily'} {assign var=rptmonthstyle value='style="display:none"'} {assign var=rptweekstyle value='style="display:none"'}
															{elseif $ACTIVITYDATA.eventrecurringtype eq 'Weekly'} {assign var=rptmonthstyle value='style="display:none"'} {assign var=rptweekstyle value='style="display:block"'}
															{elseif $ACTIVITYDATA.eventrecurringtype eq 'Monthly'} {assign var=rptmonthstyle value='style="display:block"'} {assign var=rptweekstyle value='style="display:none"'}
															{elseif $ACTIVITYDATA.eventrecurringtype eq 'Yearly'} {assign var=rptmonthstyle value='style="display:none"'} {assign var=rptweekstyle value='style="display:none"'}
															{/if}
															<span class="slds-checkbox">
																<input type="checkbox" id="checkbox-repeat1" name="recurringcheck" onClick="showhide('repeatOptions')" checked> 
																<input name="sun_flag"  value="sunday" {$ACTIVITYDATA.week0} type="checkbox">
																<label class="slds-checkbox__label" for="checkbox-repeat1">
																	<span class="slds-checkbox--faux"></span>
																	<span class="slds-form-element__label">&nbsp;{$MOD.LBL_ENABLE_REPEAT}</span>
																</label>
															</span>
														{else} {assign var=rptstyle value='style="display:none"'} {assign var=rptmonthstyle value='style="display:none"'} {assign var=rptweekstyle value='style="display:none"'}
															<span class="slds-checkbox">
																<input type="checkbox" id="checkbox-repeat2" name="recurringcheck" onClick="showhide('repeatOptions')"> 
																<label class="slds-checkbox__label" for="checkbox-repeat2">
																	<span class="slds-checkbox--faux"></span>
																	<span class="slds-form-element__label">&nbsp;{$MOD.LBL_ENABLE_REPEAT}</span>
																</label>
															</span>
														{/if}
													</td>
												</tr>
												<tr class="slds-line-height--reset">
													<td width="70%" style="border:none;">
													<!-- Start repeat options -->
														<div id="repeatOptions" {$rptstyle}>
															<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table">
																<tr class="slds-line-height--reset">
																	<td class="dvtCellLabel" width="20%">{$MOD.LBL_REPEAT_ONCE}</td>
																	<td class="dvtCellInfo" width="80%">
																		<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table">
																			<tr class="slds-line-height--reset">
																				<td style="border:none;" width="80%">
																					<select name="repeat_frequency" class="slds-select" style="width: 50px;">
																						{section name="repeat" loop=15 start=1 step=1} {if $smarty.section.repeat.iteration eq $ACTIVITYDATA.repeat_frequency} {assign var="test" value="selected"} {else} {assign var="test" value=""} {/if}
																							<option "{$test}" value="{$smarty.section.repeat.iteration}">{$smarty.section.repeat.iteration}</option>
																						{/section}
																					</select>&nbsp;
																					<select name="recurringtype" onChange="rptoptDisp(this)" class="slds-select" style="width: 80px;">
																						<option value="Daily" {if $ACTIVITYDATA.eventrecurringtype eq 'Daily'} selected {/if}>{$MOD.LBL_DAYS}</option>
																						<option value="Weekly" {if $ACTIVITYDATA.eventrecurringtype eq 'Weekly'} selected {/if}>{$MOD.LBL_WEEKS}</option>
																						<option value="Monthly" {if $ACTIVITYDATA.eventrecurringtype eq 'Monthly'} selected {/if}>{$MOD.LBL_MONTHS}</option>
																						<option value="Yearly" {if $ACTIVITYDATA.eventrecurringtype eq 'Yearly'} selected {/if}>{$MOD.LBL_YEAR}</option>
																					</select>&nbsp;
																					<!-- Repeat Feature Enhanced -->
																					<b>{$MOD.LBL_UNTIL}</b>&nbsp;
																					<input type="text" name="calendar_repeat_limit_date" id="calendar_repeat_limit_date" style="width: 100px;" class="textbox slds-input" value="">
																					<img style="width: 24px;vertical-align: middle;" src="{$IMAGE_PATH}btnL3Calendar.gif" alt="{$MOD.LBL_SET_DATE}..." title="{$MOD.LBL_SET_DATE}..." id="jscal_trigger_calendar_repeat_limit_date">
																					<script type="text/javascript">
																						Calendar.setup({
																							inputField: "calendar_repeat_limit_date",
																							ifFormat: "{$REPEAT_LIMIT_DATEFORMAT}",
																							showsTime: false,
																							button: "jscal_trigger_calendar_repeat_limit_date",
																							singleClick: true,
																							step: 1
																						});
																					</script>
																				</td>
																			</tr>
																			<tr class="slds-line-height--reset">
																				<td width="100%" style="border:none;">
																					<!-- Start Repeat WeekUI -->
																						<div id="repeatWeekUI" {$rptweekstyle}>
																							<span class="slds-checkbox">
																								<input name="sun_flag" id="checkbox-sunday" value="sunday" {$ACTIVITYDATA.week0} type="checkbox">
																								<label class="slds-checkbox__label" for="checkbox-sunday">
																									<span class="slds-checkbox--faux"></span>
																									<span class="slds-form-element__label">{$MOD.LBL_SM_SUN}</span>
																								</label>
																							</span>
																							<span class="slds-checkbox">
																								<input name="mon_flag" id="checkbox-monday" value="monday" {$ACTIVITYDATA.week1} type="checkbox">
																								<label class="slds-checkbox__label" for="checkbox-monday">
																									<span class="slds-checkbox--faux"></span>
																									<span class="slds-form-element__label">{$MOD.LBL_SM_MON}</span>
																								</label>
																							</span>
																							<span class="slds-checkbox">
																								<input name="tue_flag" id="checkbox-tuesday" value="tuesday" {$ACTIVITYDATA.week2} type="checkbox">
																								<label class="slds-checkbox__label" for="checkbox-tuesday">
																									<span class="slds-checkbox--faux"></span>
																									<span class="slds-form-element__label">{$MOD.LBL_SM_TUE}</span>
																								</label>
																							</span>
																							<span class="slds-checkbox">
																								<input name="wed_flag" id="checkbox-wednesday" value="wednesday" {$ACTIVITYDATA.week3} type="checkbox">
																								<label class="slds-checkbox__label" for="checkbox-wednesday">
																									<span class="slds-checkbox--faux"></span>
																									<span class="slds-form-element__label">{$MOD.LBL_SM_WED}</span>
																								</label>
																							</span>
																							<span class="slds-checkbox">
																								<input name="thu_flag" id="checkbox-thursday" value="thursday" {$ACTIVITYDATA.week4} type="checkbox">
																								<label class="slds-checkbox__label" for="checkbox-thursday">
																									<span class="slds-checkbox--faux"></span>
																									<span class="slds-form-element__label">{$MOD.LBL_SM_THU}</span>
																								</label>
																							</span>
																							<span class="slds-checkbox">
																								<input name="fri_flag" id="checkbox-friday" value="friday" {$ACTIVITYDATA.week5} type="checkbox">
																								<label class="slds-checkbox__label" for="checkbox-friday">
																									<span class="slds-checkbox--faux"></span>
																									<span class="slds-form-element__label">{$MOD.LBL_SM_FRI}</span>
																								</label>
																							</span>
																							<span class="slds-checkbox">
																								<input name="sat_flag" id="checkbox-saturday" value="saturday" {$ACTIVITYDATA.week6} type="checkbox">
																								<label class="slds-checkbox__label" for="checkbox-saturday">
																									<span class="slds-checkbox--faux"></span>
																									<span class="slds-form-element__label">{$MOD.LBL_SM_SAT}</span>
																								</label>
																							</span>
																						</div>
																					<!-- End Repeat WeekUI -->

																					<!-- Start Repeat MonthUI -->
																						<div id="repeatMonthUI" {$rptmonthstyle}>
																							<!-- First Option -->
																							<span class="slds-radio">
																								<input type="radio" checked name="repeatMonth" id="radio-repeatMonth1" {if $ACTIVITYDATA.repeatMonth eq 'date'} checked {/if} value="date">
																								<label class="slds-radio__label" for="radio-repeatMonth1">
																									<span class="slds-radio--faux"></span>
																									<span class="slds-form-element__label">{$MOD.on}</span>
																								</label>
																							</span>&nbsp;
																							<input type="text" class="slds-input" style="width:50px" value="{$ACTIVITYDATA.repeatMonth_date}" name="repeatMonth_date">&nbsp;
																							<span>{$MOD['day of the month']}</span>
																							<br/>
																							<!-- Second Option -->
																							<span class="slds-radio">
																								<input type="radio" name="repeatMonth" id="radio-repeatMonth2" {if $ACTIVITYDATA.repeatMonth eq 'day'} checked {/if} value="day">
																								<label class="slds-radio__label" for="radio-repeatMonth2">
																									<span class="slds-radio--faux"></span>
																									<span class="slds-form-element__label">{$MOD.on}</span>
																								</label>
																							</span>&nbsp;
																							<select name="repeatMonth_daytype" class="slds-select" style="width: 80px;">
																								<option value="first" {if $ACTIVITYDATA.repeatMonth_daytype eq 'first'} selected {/if}>{$MOD.First}</option>
																								<option value="second" {if $ACTIVITYDATA.repeatMonth_daytype eq 'second'} selected {/if}>{$MOD.Second}</option>
																								<option value="third" {if $ACTIVITYDATA.repeatMonth_daytype eq 'third'} selected {/if}>{$MOD.Third}</option>
																								<option value="last" {if $ACTIVITYDATA.repeatMonth_daytype eq 'last'} selected {/if}>{$MOD.Last}</option>
																							</select>&nbsp;
																							<select name="repeatMonth_day" class="slds-select" style="width: 80px;">
																								<option value=1 {if $ACTIVITYDATA.repeatMonth_day eq 1} selected {/if}>{$MOD.LBL_DAY1}</option>
																								<option value=2 {if $ACTIVITYDATA.repeatMonth_day eq 2} selected {/if}>{$MOD.LBL_DAY2}</option>
																								<option value=3 {if $ACTIVITYDATA.repeatMonth_day eq 3} selected {/if}>{$MOD.LBL_DAY3}</option>
																								<option value=4 {if $ACTIVITYDATA.repeatMonth_day eq 4} selected {/if}>{$MOD.LBL_DAY4}</option>
																								<option value=5 {if $ACTIVITYDATA.repeatMonth_day eq 5} selected {/if}>{$MOD.LBL_DAY5}</option>
																								<option value=6 {if $ACTIVITYDATA.repeatMonth_day eq 6} selected {/if}>{$MOD.LBL_DAY6}</option>
																							</select>
																						</div>
																					<!-- Start Repeat MonthUI -->
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														</div>
														<!-- End repeat options -->
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							{/if}
						</div>
					<!-- End Repeat UI -->

				</div>
			</td>
		</tr>
	</table>
</div>
<!-- End Lighting Design Tab Conrols by Endrit