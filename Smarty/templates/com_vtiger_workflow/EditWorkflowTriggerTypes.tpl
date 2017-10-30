{*<!--
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}
<script src="modules/com_vtiger_workflow/resources/jquery.timepicker.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/functional.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/json2.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/fieldvalidator.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/edittaskscript.js" type="text/javascript" charset="utf-8"></script>
{literal}
<style type="text/css" media="screen">
.wfslabel {
	width: 150px;
}
.wfsfield, .wfslabel {
	float: left;
	border: 1px solid orange;
	padding: 4px;
	margin: 0px 4px;
}

.wfsclear {
	clear:both;
}
</style>
<script type="text/javascript" charset="utf-8">
	fn.addStylesheet('modules/com_vtiger_workflow/resources/style.css');
	var returnUrl = '{$returnUrl}';
	var validator;
	edittaskscript(jQuery);
</script>
{/literal}
<!-- When to run workflow title and options container -->
<table class="slds-table slds-no-row-hover tableHeading" style="background-color: #fff;">
	<tr class="blockStyleCss">
		<td class="detailViewContainer" valign="top">
			<!-- When to run workflow Title Container-->
			<div class="forceRelatedListSingleContainer">
				<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
					<div class="slds-card__header slds-grid">
						<!-- Title -->
						<header class="slds-media slds-media--center slds-has-flexi-truncate">
							<div class="slds-media__body">
								<h2>
									<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
										<strong>{$MOD.LBL_WHEN_TO_RUN_WORKFLOW}</strong>
									</span>
								</h2>
							</div>
						</header>
					</div>
				</article>
			</div>

			<div class="slds-truncate">
				<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table">
					<tr>
						<td class="dvtCellLabel text-center" width="5%">
							<span class="slds-radio">
								<input type="radio" name="execution_condition" id="ON_FIRST_SAVE" value="ON_FIRST_SAVE" onclick="onschedule_preparescreen(this);"
								{if $workflow->executionConditionAsLabel() eq 'ON_FIRST_SAVE'}checked{/if} {if $workflow->executionConditionAsLabel() eq 'MANUAL'}disabled{/if}/>
								<label class="slds-radio__label" for="ON_FIRST_SAVE">
									<span class="slds-radio--faux" style="margin-right: 0;"></span>
								</label>
							</span>
						</td>
						<td class="dvtCellInfo">{$MOD.LBL_ONLY_ON_FIRST_SAVE}.</td>
					</tr>
					<tr>
						<td class="dvtCellLabel text-center" width="5%">
							<span class="slds-radio">
								<input type="radio" name="execution_condition" id="ONCE" value="ONCE" onclick="onschedule_preparescreen(this);"
								{if $workflow->executionConditionAsLabel() eq 'ONCE'}checked{/if} {if $workflow->executionConditionAsLabel() eq 'MANUAL'}disabled{/if}/>
								<label class="slds-radio__label" for="ONCE">
									<span class="slds-radio--faux" style="margin-right: 0;"></span>
								</label>
							</span>
						</td>
						<td class="dvtCellInfo">{$MOD.LBL_UNTIL_FIRST_TIME_CONDITION_TRUE}.</td>
					</tr>
					<tr>
						<td class="dvtCellLabel text-center" width="5%">
							<span class="slds-radio">
								<input type="radio" name="execution_condition" id="ON_EVERY_SAVE" value="ON_EVERY_SAVE" onclick="onschedule_preparescreen(this);"
								{if $workflow->executionConditionAsLabel() eq 'ON_EVERY_SAVE'}checked{/if} {if $workflow->executionConditionAsLabel() eq 'MANUAL'}disabled{/if}/>
								<label class="slds-radio__label" for="ON_EVERY_SAVE">
									<span class="slds-radio--faux" style="margin-right: 0;"></span>
								</label>
							</span>
						</td>
						<td class="dvtCellInfo">{$MOD.LBL_EVERYTIME_RECORD_SAVED}.</td>
					</tr>
					<tr>
						<td class="dvtCellLabel text-center" width="5%">
							<span class="slds-radio">
								<input type="radio" name="execution_condition" id="ON_MODIFY" value="ON_MODIFY" onclick="onschedule_preparescreen(this);"
								{if $workflow->executionConditionAsLabel() eq 'ON_MODIFY'}checked{/if} {if $workflow->executionConditionAsLabel() eq 'MANUAL'}disabled{/if}/>
								<label class="slds-radio__label" for="ON_MODIFY">
									<span class="slds-radio--faux" style="margin-right: 0;"></span>
								</label>
							</span>
						</td>
						<td class="dvtCellInfo">{$MOD.LBL_ON_MODIFY}.</td>
					</tr>
					<tr>
						<td class="dvtCellLabel text-center" width="5%">
							<span class="slds-radio">
								<input type="radio" name="execution_condition" id="ON_DELETE" value="ON_DELETE" onclick="onschedule_preparescreen(this);"
								{if $workflow->executionConditionAsLabel() eq 'ON_DELETE'}checked{/if} {if $workflow->executionConditionAsLabel() eq 'MANUAL'}disabled{/if}/>
								<label class="slds-radio__label" for="ON_DELETE">
									<span class="slds-radio--faux" style="margin-right: 0;"></span>
								</label>
							</span>
						</td>
						<td class="dvtCellInfo">{$MOD.LBL_ON_DELETE}.</td>
					</tr>
					<tr>
						<td class="dvtCellLabel text-center" width="5%">
							<span class="slds-radio">
								<input type="radio" name="execution_condition" id="ON_SCHEDULE" value="ON_SCHEDULE" onclick="onschedule_preparescreen(this);"
								{if $workflow->executionConditionAsLabel() eq 'ON_SCHEDULE'}checked{/if} {if $ScheduledWorkflowsCount>$MaxAllowedScheduledWorkflows}disabled{/if} {if $workflow->executionConditionAsLabel() eq 'MANUAL'}disabled{/if}/>
								<label class="slds-radio__label" for="ON_SCHEDULE">
									<span class="slds-radio--faux" style="margin-right: 0;"></span>
								</label>
							</span>
						<td class="dvtCellInfo">
							<!-- Schedule Label-->
							{$MOD.LBL_ON_SCHEDULE}
							{if $ScheduledWorkflowsCount>$MaxAllowedScheduledWorkflows}
								<!-- Schedule exceeds max limit -->
								<span class='errorMessage' style="color:red;margin-left: 10px;">{'EXCEEDS_MAX'|@getTranslatedString} : {$MaxAllowedScheduledWorkflows}</span>
							{else}
								<!-- Schedule Run Workflow-->
								<div id="scheduleBox" style="{if $workflow->executionCondition neq 6}display:none;{/if}">
									<div id='scheduledType' class='wfsclear'>
										<!-- Run Workflow Box -->
										<div class="wfslabel"><label for="schtypeid">{'LBL_RUN_WORKFLOW'|@getTranslatedString:$MODULE_NAME}</label></div>
										<!-- Selected Hourly by default -->
										<!-- Select (Daily,Weekly,Specific Date,Monthly,Year,Interval) -->
										<div style='margin-left:6px;float:left;'>
											<select id='schtypeid' name='schtypeid' class="slds-select" onchange="onschedule_selectschedule(this);">
												<option value="1" {if $workflow->schtypeid eq 1}selected{/if}>{'LBL_HOURLY'|@getTranslatedString:$MODULE_NAME}</option>
												<option value="2" {if $workflow->schtypeid eq 2}selected{/if}>{'LBL_DAILY'|@getTranslatedString:$MODULE_NAME}</option>
												<option value="3" {if $workflow->schtypeid eq 3}selected{/if}>{'LBL_WEEKLY'|@getTranslatedString:$MODULE_NAME}</option>
												<option value="4" {if $workflow->schtypeid eq 4}selected{/if}>{'LBL_SPECIFIC_DATE'|@getTranslatedString:$MODULE_NAME}</option>
												<option value="5" {if $workflow->schtypeid eq 5}selected{/if}>{'LBL_MONTHLY_BY_DATE'|@getTranslatedString:$MODULE_NAME}</option>
												<!--option value="6" {if $workflow->schtypeid eq 6}selected{/if}>{'LBL_MONTHLY_BY_WEEKDAY'|@getTranslatedString:$MODULE_NAME}</option-->
												<option value="7" {if $workflow->schtypeid eq 7}selected{/if}>{'LBL_YEARLY'|@getTranslatedString:$MODULE_NAME}</option>
												<option value="8" {if $workflow->schtypeid eq 8}selected{/if}>{'LBL_MINUTES_INTERVAL'|@getTranslatedString:$MODULE_NAME}</option>
											</select>
										</div>
									</div>
									<!-- if weekly is selected -->
									{* show weekdays for weekly option *}
									<div id='scheduledWeekDay' style='padding:5px 0px;clear:both;display:{if $workflow->schtypeid neq 3}none{else}block{/if};'>
										<!-- On these days label box -->
										<div class="wfslabel">{'LBL_ON_THESE_DAYS'|@getTranslatedString:$MODULE_NAME}</div>
										<!-- Days of the week -->
										<div style='margin-left:6px;float:left;'>
											<table border=0 cellspacing=0 cellpadding=2 class="weekDays-table"> {* name='schdayofweek' *}
												<tr>
													<!-- Sunday -->
													<td class="checkbox-inputs">
														<span class="slds-checkbox">
															<input name="sun_flag" id="sun_flag" value="1" type="checkbox" {if is_array($dayOfWeek) && in_array('1', $dayOfWeek)}checked{/if}>
															<label class="slds-checkbox__label" for="sun_flag">
																<span class="slds-checkbox--faux" style="margin-right: 0;"></span>
															</label>
															<span class="slds-form-element__label">{'LBL_DAY0'|@getTranslatedString:'Calendar'}</span>
														</span>
													</td>
													<!-- Monday -->
													<td class="checkbox-inputs">
														<span class="slds-checkbox">
															<input name="mon_flag" id="mon_flag" value="2" type="checkbox" {if is_array($dayOfWeek) && in_array('2', $dayOfWeek)}checked{/if}>
															<label class="slds-checkbox__label" for="mon_flag">
																<span class="slds-checkbox--faux" style="margin-right: 0;"></span>
															</label>
															<span class="slds-form-element__label">{'LBL_DAY1'|@getTranslatedString:'Calendar'}</span>
														</span>
													</td>
													<!-- Tuesday -->
													<td class="checkbox-inputs">
														<span class="slds-checkbox">
															<input name="tue_flag" id="tue_flag" value="3" type="checkbox" {if is_array($dayOfWeek) && in_array('3', $dayOfWeek)}checked{/if}>
															<label class="slds-checkbox__label" for="tue_flag">
																<span class="slds-checkbox--faux" style="margin-right: 0;"></span>
															</label>
															<span class="slds-form-element__label">{'LBL_DAY2'|@getTranslatedString:'Calendar'}</span>
														</span>
													</td>
													<!-- Wednesday -->
													<td class="checkbox-inputs">
													<span class="slds-checkbox">
														<input name="wed_flag" id="wed_flag" value="4" type="checkbox" {if is_array($dayOfWeek) && in_array('4', $dayOfWeek)}checked{/if}>
															<label class="slds-checkbox__label" for="wed_flag">
																<span class="slds-checkbox--faux" style="margin-right: 0;"></span>
															</label>
															<span class="slds-form-element__label">{'LBL_DAY3'|@getTranslatedString:'Calendar'}</span>
														</span>
													</td>
													<!-- Thursday -->
													<td class="checkbox-inputs">
													<span class="slds-checkbox">
														<input name="thu_flag" id="thu_flag" value="5" type="checkbox" {if is_array($dayOfWeek) && in_array('5', $dayOfWeek)}checked{/if}>
															<label class="slds-checkbox__label" for="thu_flag">
																<span class="slds-checkbox--faux" style="margin-right: 0;"></span>
															</label>
															<span class="slds-form-element__label">{'LBL_DAY4'|@getTranslatedString:'Calendar'}</span>
														</span>
													</td>
													<!-- Friday -->
													<td class="checkbox-inputs">
														<span class="slds-checkbox">
															<input name="fri_flag" id="fri_flag" value="6" type="checkbox" {if is_array($dayOfWeek) && in_array('6', $dayOfWeek)}checked{/if}>
															<label class="slds-checkbox__label" for="fri_flag">
																<span class="slds-checkbox--faux" style="margin-right: 0;"></span>
															</label>
															<span class="slds-form-element__label">{'LBL_DAY5'|@getTranslatedString:'Calendar'}</span>
														</span>
													</td>
													<!-- Saturday -->
													<td class="checkbox-inputs">
														<span class="slds-checkbox">
															<input name="sat_flag" id="sat_flag" value="7" type="checkbox" {if is_array($dayOfWeek) && in_array('7', $dayOfWeek)}checked{/if}>
															<label class="slds-checkbox__label" for="sat_flag">
																<span class="slds-checkbox--faux" style="margin-right: 0;"></span>
															</label>
															<span class="slds-form-element__label">{'LBL_DAY6'|@getTranslatedString:'Calendar'}</span>
														</span>
													</td>
												</tr>
											</table>
										</div>
									</div>

									<!-- If monthly by date is selected -->
									{* show month view by dates *}
									<div id='scheduleMonthByDates' style="padding:5px 0px;clear:both;display:{if $workflow->schtypeid neq 5}none{else}block{/if};">
										<!-- On these days box label -->
										<div class="wfslabel">{'LBL_ON_THESE_DAYS'|@getTranslatedString:$MODULE_NAME}</div>
										<!-- Show days of the month -->
										<div style='margin-left:6px;float:left;'>
											<select class="slds-select" multiple='multiple' name='schdayofmonth[]' id='schdayofmonth'>
												{html_options options=$days1_31 selected=$selected_days1_31}
											</select>
										</div>
									</div>

									<!-- If On specific Date is selected -->
									{* show specific date *}
									<div id='scheduleByDate' style="padding:5px 0px;clear:both;display:{if $workflow->schtypeid eq 4 || $workflow->schtypeid eq 7}block{else}none{/if};">
										<!-- Choose Date box Label -->
										<div class="wfslabel">{'LBL_CHOOSE_DATE'|@getTranslatedString:$MODULE_NAME}</div>
										<!-- Input date and Calendar -->
										<div style='margin-left:6px;float:left;'>
											<input type="text" name="schdate" id="schdate" value="{$schdate}" class="slds-input" style="min-height: 26px;height:26px;">
											<img src="{$IMAGE_PATH}btnL3Calendar.gif" style="vertical-align: middle;" alt="{$MOD.LBL_SET_DATE}" title="{$MOD.LBL_SET_DATE}" id="jscal_trigger_schdate">
											<script type="text/javascript">
											Calendar.setup ({ldelim}
												inputField : "schdate", ifFormat : "{$dateFormat}", showsTime : false, button : "jscal_trigger_schdate", singleClick : true, step : 1
											{rdelim})
											</script>
										</div>
									</div>

									{* show month view by weekday *}
									<div id='scheduleMonthByWeekDays' style='padding:5px 0px;clear:both;display:{if $workflow->schtypeid neq 6}none{else}block{/if};'>
									</div>

									<!-- If Daily is selected -->
									{* show time for all other than Hourly option*}
									<div id='scheduledTime' class='wfsclear' style='padding:5px 0px;display:{if $workflow->schtypeid < 2 || $workflow->schtypeid eq 8}none{else}block{/if};'>
										<!-- At time label box -->
										<div class="wfslabel">{'LBL_AT_TIME'|@getTranslatedString:$MODULE_NAME}</div>
										<!-- Set time from jquery.timepicked.js -->
										<div style='margin-left:6px;float:left;' id='schtimerow'>
											<input type="hidden" name="schtime" value="{$schdtime_12h}" id="schtime" style="width:60px" class="time_field">
										</div>
									</div>

									{* show minutes interval*}
									<div id="minutesinterval" class='wfsclear' style='padding:5px 0px;display:{if $workflow->schtypeid neq 8}none{else}block{/if};'>
										<div class="wfslabel">{'LBL_EVERY_MINUTEINTERVAL'|@getTranslatedString:$MODULE_NAME}</div>
											<select style='width:50px; margin-left: 5px;' name='schminuteinterval' id='schminuteinterval' class="slds-select">
												{html_options options=$interval_range selected=$selected_minute_interval}
											</select>
											{'LBL_MINUTES'|@getTranslatedString:$MODULE_NAME}
									</div>
									{if $workflow->nexttrigger_time}
										<div class='wfsclear'>
											<div class="wfslabel" style="width: 100%;">{'LBL_NEXT_TRIGGER_TIME'|@getTranslatedString:$MODULE_NAME}:&nbsp;{$wfnexttrigger_time}</div>
										</div>
									{/if}
								</div>
							{/if}
						</td>
					</tr>
					<!-- If Record Access Control is selected -->
					<tr>
						<td class="dvtCellLabel text-center" width="5%">
							<span class="slds-radio">
								<input type="radio" name="execution_condition" id="RECORD_ACCESS_CONTROL" value="RECORD_ACCESS_CONTROL" onclick="onschedule_preparescreen(this);"
								{if $workflow->executionConditionAsLabel() eq 'RECORD_ACCESS_CONTROL'}checked{/if} {if $workflow->executionConditionAsLabel() eq 'MANUAL'}disabled{/if} />
								<label class="slds-radio__label" for="RECORD_ACCESS_CONTROL">
									<span class="slds-radio--faux" style="margin-right: 0;"></span>
								</label>
							</span>
						</td>
						<td class="dvtCellInfo">{$MOD.LBL_RECORD_ACCESS_CONTROL}.</td>
					</tr>
					<!-- If system is selected -->
					<tr>
						<td class="dvtCellLabel text-center" width="5%">
							<span class="slds-radio">
								<input type="radio" name="execution_condition" id="MANUAL" value="MANUAL"
								{if $workflow->executionConditionAsLabel() eq 'MANUAL'}checked{/if} disabled />
								<label class="slds-radio__label" for="MANUAL">
									<span class="slds-radio--faux" style="margin-right: 0;"></span>
								</label>
							</span>
						</td>
						<td class="dvtCellInfo text-left" width="90%">{$MOD.LBL_MANUAL}.</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
</table>