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

<div class="slds-page-header">
<div class="slds-page-header__row">
	<div class="slds-page-header__col-title">
	<div class="slds-media">
		<div class="slds-media__body">
		<div class="slds-page-header__name">
			<div class="slds-page-header__name-title">
			<h1>
				<span class="slds-page-header__title slds-truncate" title="{$MOD.LBL_WHEN_TO_RUN_WORKFLOW}">
				{$MOD.LBL_WHEN_TO_RUN_WORKFLOW}
				</span>
			</h1>
			</div>
		</div>
		</div>
	</div>
	</div>
</div>
</div>
<div class="slds-grid slds-gutters">
{if $ISADMIN}
<div class="slds-col slds-size_3-of-8 slds-page-header__meta-text">
	<fieldset class="slds-form-element slds-m-left_small">
		<legend class="slds-form-element__legend slds-form-element__label slds-page-header__meta-text">{$MOD.LBL_SAVEBASED}</legend>
		<div class="slds-form-element__control">
		<span class="slds-radio">
		<input type="radio" id="execcondofs" value="ON_FIRST_SAVE" name="execution_condition" {if $workflow->executionConditionAsLabel() eq 'ON_FIRST_SAVE'}checked{/if} onclick="onschedule_preparescreen(this);" />
		<label class="slds-radio__label" for="execcondofs">
		<span class="slds-radio_faux"></span>
		<span class="slds-form-element__label slds-page-header__meta-text">{$MOD.LBL_ONLY_ON_FIRST_SAVE}.</span>
		</label>
		</span>
		<span class="slds-radio">
		<input type="radio" id="execcondo" value="ONCE" name="execution_condition" {if $workflow->executionConditionAsLabel() eq 'ONCE'}checked{/if} onclick="onschedule_preparescreen(this);" />
		<label class="slds-radio__label" for="execcondo">
		<span class="slds-radio_faux"></span>
		<span class="slds-form-element__label slds-page-header__meta-text">{$MOD.LBL_UNTIL_FIRST_TIME_CONDITION_TRUE}.</span>
		</label>
		</span>
		<span class="slds-radio">
		<input type="radio" id="execcondoes" value="ON_EVERY_SAVE" name="execution_condition" {if $workflow->executionConditionAsLabel() eq 'ON_EVERY_SAVE'}checked{/if} onclick="onschedule_preparescreen(this);" />
		<label class="slds-radio__label" for="execcondoes">
		<span class="slds-radio_faux"></span>
		<span class="slds-form-element__label slds-page-header__meta-text">{$MOD.LBL_EVERYTIME_RECORD_SAVED}.</span>
		</label>
		</span>
		<span class="slds-radio">
		<input type="radio" id="execcondom" value="ON_MODIFY" name="execution_condition" {if $workflow->executionConditionAsLabel() eq 'ON_MODIFY'}checked{/if} onclick="onschedule_preparescreen(this);" />
		<label class="slds-radio__label" for="execcondom">
		<span class="slds-radio_faux"></span>
		<span class="slds-form-element__label slds-page-header__meta-text">{$MOD.LBL_ON_MODIFY}.</span>
		</label>
		</span>
		<span class="slds-radio">
		<input type="radio" id="execcondod" value="ON_DELETE" name="execution_condition" {if $workflow->executionConditionAsLabel() eq 'ON_DELETE'}checked{/if} onclick="onschedule_preparescreen(this);" />
		<label class="slds-radio__label" for="execcondod">
		<span class="slds-radio_faux"></span>
		<span class="slds-form-element__label slds-page-header__meta-text">{$MOD.LBL_ON_DELETE}.</span>
		</label>
		</span>
		<span class="slds-radio">
		<input type="radio" id="execcondor" value="ON_RELATE" name="execution_condition" {if $workflow->executionConditionAsLabel() eq 'ON_RELATE'}checked{/if} onclick="onschedule_preparescreen(this);" />
		<label class="slds-radio__label" for="execcondor">
		<span class="slds-radio_faux"></span>
		<span class="slds-form-element__label slds-page-header__meta-text">
			{$MOD.LBL_ON_RELATE}
			<select id='onrelatemodule' name='onrelatemodule' class="slds-select" style="width:fit-content;" onchange="onschedule_selectschedule(this);">
				{html_options options=$relatedmodules selected=$onrelatedmodule}
			</select>
		</span>
		</label>
		</span>
		<span class="slds-radio">
		<input type="radio" id="execcondour" value="ON_UNRELATE" name="execution_condition" {if $workflow->executionConditionAsLabel() eq 'ON_UNRELATE'}checked{/if} onclick="onschedule_preparescreen(this);" />
		<label class="slds-radio__label" for="execcondour">
		<span class="slds-radio_faux"></span>
		<span class="slds-form-element__label slds-page-header__meta-text">
			{$MOD.LBL_ON_UNRELATE}
			<select id='onunrelatemodule' name='onunrelatemodule' class="slds-select" style="width:fit-content;" onchange="onschedule_selectschedule(this);">
				{html_options options=$relatedmodules selected=$onunrelatedmodule}
			</select>
		</span>
		</label>
		</span>
		</div>
	</fieldset>
</div>
{/if}
<div class="slds-col slds-size_5-of-8 slds-page-header__meta-text">
	<fieldset class="slds-form-element slds-m-left_small">
		<legend class="slds-form-element__legend slds-form-element__label slds-page-header__meta-text">{$MOD.LBL_TIMEBASED}</legend>
		<div class="slds-form-element__control">
			<div class="slds-grid slds-grid_vertical">
				<div class="slds-col slds-p-left_none">
					<span class="slds-radio">
					<input type="radio" id="execcondos" value="ON_SCHEDULE" name="execution_condition" {if $workflow->executionConditionAsLabel() eq 'ON_SCHEDULE'}checked{/if} {if $ScheduledWorkflowsCount>$MaxAllowedScheduledWorkflows}disabled{/if} onclick="onschedule_preparescreen(this);" />
					<label class="slds-radio__label" for="execcondos">
					<span class="slds-radio_faux"></span>
					<span class="slds-form-element__label slds-page-header__meta-text">
					{$MOD.LBL_ON_SCHEDULE}.
					{if $ScheduledWorkflowsCount>$MaxAllowedScheduledWorkflows}
					<span class='errorMessage' style="color:red;margin-left: 10px;">{'EXCEEDS_MAX'|@getTranslatedString} : {$MaxAllowedScheduledWorkflows}</span>
					{else}
						<div id="scheduleBox" style="margin-left: 50px;margin-top: 8px;{if $workflow->executionCondition neq 6}display:none;{/if}">
							<div id='scheduledType' class='wfsclear'>
								<div class="wfslabel"><label for="schtypeid">{'LBL_RUN_WORKFLOW'|@getTranslatedString:$MODULE_NAME}</label></div>
								<div style='margin-left:6px;float:left;'>
								<select id='schtypeid' name='schtypeid' class="small" onchange="onschedule_selectschedule(this);">
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

							{* show weekdays for weekly option *}
							<div id='scheduledWeekDay' style='padding:5px 0px;clear:both;display:{if $workflow->schtypeid neq 3}none{else}block{/if};'>
								<div class="wfslabel">{'LBL_ON_THESE_DAYS'|@getTranslatedString:$MODULE_NAME}</div>
								<div style='margin-left:6px;float:left;'>
									<table border=0 cellspacing=0 cellpadding=2> {* name='schdayofweek' *}
										<tr>
										<td><input name="sun_flag" value="1" type="checkbox" {if is_array($dayOfWeek) && in_array('1', $dayOfWeek)}checked{/if}></td><td>{'LBL_DAY0'|@getTranslatedString:'cbCalendar'}</td>
										<td><input name="mon_flag" value="2" type="checkbox" {if is_array($dayOfWeek) && in_array('2', $dayOfWeek)}checked{/if}></td><td>{'LBL_DAY1'|@getTranslatedString:'cbCalendar'}</td>
										<td><input name="tue_flag" value="3" type="checkbox" {if is_array($dayOfWeek) && in_array('3', $dayOfWeek)}checked{/if}></td><td>{'LBL_DAY2'|@getTranslatedString:'cbCalendar'}</td>
										<td><input name="wed_flag" value="4" type="checkbox" {if is_array($dayOfWeek) && in_array('4', $dayOfWeek)}checked{/if}></td><td>{'LBL_DAY3'|@getTranslatedString:'cbCalendar'}</td>
										<td><input name="thu_flag" value="5" type="checkbox" {if is_array($dayOfWeek) && in_array('5', $dayOfWeek)}checked{/if}></td><td>{'LBL_DAY4'|@getTranslatedString:'cbCalendar'}</td>
										<td><input name="fri_flag" value="6" type="checkbox" {if is_array($dayOfWeek) && in_array('6', $dayOfWeek)}checked{/if}></td><td>{'LBL_DAY5'|@getTranslatedString:'cbCalendar'}</td>
										<td><input name="sat_flag" value="7" type="checkbox" {if is_array($dayOfWeek) && in_array('7', $dayOfWeek)}checked{/if}></td><td>{'LBL_DAY6'|@getTranslatedString:'cbCalendar'}</td>
										</tr>
									</table>
								</div>
							</div>

							{* show month view by dates *}
							<div id='scheduleMonthByDates' style="padding:5px 0px;clear:both;display:{if $workflow->schtypeid neq 5}none{else}block{/if};">
								<div class="wfslabel">{'LBL_ON_THESE_DAYS'|@getTranslatedString:$MODULE_NAME}</div>
								<div style='margin-left:6px;float:left;'>
									<select style='width:230px;' multiple='multiple' name='schdayofmonth[]' id='schdayofmonth'>
										{html_options options=$days1_31 selected=$selected_days1_31}
									</select>
								</div>
							</div>

							{* show specific date *}
							<div id='scheduleByDate' style="padding:5px 0px;clear:both;display:{if $workflow->schtypeid eq 4 || $workflow->schtypeid eq 7}block{else}none{/if};">
								<div class="wfslabel">{'LBL_CHOOSE_DATE'|@getTranslatedString:$MODULE_NAME}</div>
								<div style='margin-left:6px;float:left;'>
									<input type="text" name="schdate" id="schdate" style="width:90px;vertical-align: top;" value="{$schdate}"><img border=0 src="{$IMAGE_PATH}btnL3Calendar.gif" alt="{$MOD.LBL_SET_DATE}" title="{$MOD.LBL_SET_DATE}" id="jscal_trigger_schdate">
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

							{* show time for all other than Hourly option*}
							<div id='scheduledTime' class='wfsclear' style='padding:5px 0px;display:{if $workflow->schtypeid < 2 || $workflow->schtypeid eq 8}none{else}block{/if};'>
								<div class="wfslabel">{'LBL_AT_TIME'|@getTranslatedString:$MODULE_NAME}</div>
								<div style='margin-left:6px;float:left;' id='schtimerow'>
									<input type="hidden" name="schtime" value="{$schdtime_12h}" id="schtime" style="width:60px" class="time_field">
								</div>
							</div>
							{* show minutes interval*}
							<div id="minutesinterval" class='wfsclear' style='padding:5px 0px;display:{if $workflow->schtypeid neq 8}none{else}block{/if};'>
								<div class="wfslabel">{'LBL_EVERY_MINUTEINTERVAL'|@getTranslatedString:$MODULE_NAME}</div>
									<select style='width:50px;' name='schminuteinterval' id='schminuteinterval'>
										{html_options options=$interval_range selected=$selected_minute_interval}
									</select>
									{'LBL_MINUTES'|@getTranslatedString:$MODULE_NAME}
							</div>
							{if $workflow->nexttrigger_time}
								<div class='wfsclear'>
									<div class="wfslabel" style="width: 98%;">{'LBL_NEXT_TRIGGER_TIME'|@getTranslatedString:$MODULE_NAME}:&nbsp;{$wfnexttrigger_time}</div>
								</div>
							{/if}
						</div>
					{/if}
					</span>
					</label>
					</span>
				</div>
				{if $ISADMIN}
				<div class="slds-col slds-p-left_none">
					<span class="slds-radio">
					<input type="radio" id="execcondrac" value="RECORD_ACCESS_CONTROL" name="execution_condition" {if $workflow->executionConditionAsLabel() eq 'RECORD_ACCESS_CONTROL'}checked{/if} onclick="onschedule_preparescreen(this);" />
					<label class="slds-radio__label" for="execcondrac">
					<span class="slds-radio_faux"></span>
					<span class="slds-form-element__label slds-page-header__meta-text">{$MOD.LBL_RECORD_ACCESS_CONTROL}.</span>
					</label>
					</span>
				</div>
				{/if}
				<div class="slds-col slds-p-left_none">
					<span class="slds-radio">
					<input type="radio" id="execcondman" value="MANUAL" name="execution_condition" {if $workflow->executionConditionAsLabel() eq 'MANUAL'}checked{/if} onclick="onschedule_preparescreen(this);" />
					<label class="slds-radio__label" for="execcondman">
					<span class="slds-radio_faux"></span>
					<span class="slds-form-element__label slds-page-header__meta-text">{$MOD.LBL_MANUAL}.</span>
					</label>
					</span>
				</div>
			</div>
		</div>
	</fieldset>
</div>
</div>
