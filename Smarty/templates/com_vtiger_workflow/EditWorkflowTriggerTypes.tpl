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
<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td class="big" nowrap="nowrap">
			<strong>{$MOD.LBL_WHEN_TO_RUN_WORKFLOW}</strong>
		</td>
	</tr>
</table>
<table border="0" >
	<tr><td><input type="radio" name="execution_condition" value="ON_FIRST_SAVE" onclick="onschedule_preparescreen(this);"
		{if $workflow->executionConditionAsLabel() eq 'ON_FIRST_SAVE'}checked{/if} {if $workflow->executionConditionAsLabel() eq 'MANUAL'}disabled{/if}/></td>
		<td>{$MOD.LBL_ONLY_ON_FIRST_SAVE}.</td></tr>
	<tr><td><input type="radio" name="execution_condition" value="ONCE" onclick="onschedule_preparescreen(this);"
		{if $workflow->executionConditionAsLabel() eq 'ONCE'}checked{/if} {if $workflow->executionConditionAsLabel() eq 'MANUAL'}disabled{/if}/></td>
		<td>{$MOD.LBL_UNTIL_FIRST_TIME_CONDITION_TRUE}.</td></tr>
	<tr><td><input type="radio" name="execution_condition" value="ON_EVERY_SAVE" onclick="onschedule_preparescreen(this);"
		{if $workflow->executionConditionAsLabel() eq 'ON_EVERY_SAVE'}checked{/if} {if $workflow->executionConditionAsLabel() eq 'MANUAL'}disabled{/if}/></td>
		<td>{$MOD.LBL_EVERYTIME_RECORD_SAVED}.</td></tr>
	<tr><td><input type="radio" name="execution_condition" value="ON_MODIFY" onclick="onschedule_preparescreen(this);"
		{if $workflow->executionConditionAsLabel() eq 'ON_MODIFY'}checked{/if} {if $workflow->executionConditionAsLabel() eq 'MANUAL'}disabled{/if}/></td>
		<td>{$MOD.LBL_ON_MODIFY}.</td></tr>
	<tr><td><input type="radio" name="execution_condition" value="ON_DELETE" onclick="onschedule_preparescreen(this);"
		{if $workflow->executionConditionAsLabel() eq 'ON_DELETE'}checked{/if} {if $workflow->executionConditionAsLabel() eq 'MANUAL'}disabled{/if}/></td>
		<td>{$MOD.LBL_ON_DELETE}.</td></tr>
	<tr><td valign="top"><input type="radio" name="execution_condition" value="ON_SCHEDULE" onclick="onschedule_preparescreen(this);"
		{if $workflow->executionConditionAsLabel() eq 'ON_SCHEDULE'}checked{/if} {if $ScheduledWorkflowsCount>$MaxAllowedScheduledWorkflows}disabled{/if} {if $workflow->executionConditionAsLabel() eq 'MANUAL'}disabled{/if}/></td>
		<td>{$MOD.LBL_ON_SCHEDULE}.
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
							<td><input name="sun_flag" value="1" type="checkbox" {if is_array($dayOfWeek) && in_array('1', $dayOfWeek)}checked{/if}></td><td>{'LBL_DAY0'|@getTranslatedString:'Calendar'}</td>
							<td><input name="mon_flag" value="2" type="checkbox" {if is_array($dayOfWeek) && in_array('2', $dayOfWeek)}checked{/if}></td><td>{'LBL_DAY1'|@getTranslatedString:'Calendar'}</td>
							<td><input name="tue_flag" value="3" type="checkbox" {if is_array($dayOfWeek) && in_array('3', $dayOfWeek)}checked{/if}></td><td>{'LBL_DAY2'|@getTranslatedString:'Calendar'}</td>
							<td><input name="wed_flag" value="4" type="checkbox" {if is_array($dayOfWeek) && in_array('4', $dayOfWeek)}checked{/if}></td><td>{'LBL_DAY3'|@getTranslatedString:'Calendar'}</td>
							<td><input name="thu_flag" value="5" type="checkbox" {if is_array($dayOfWeek) && in_array('5', $dayOfWeek)}checked{/if}></td><td>{'LBL_DAY4'|@getTranslatedString:'Calendar'}</td>
							<td><input name="fri_flag" value="6" type="checkbox" {if is_array($dayOfWeek) && in_array('6', $dayOfWeek)}checked{/if}></td><td>{'LBL_DAY5'|@getTranslatedString:'Calendar'}</td>
							<td><input name="sat_flag" value="7" type="checkbox" {if is_array($dayOfWeek) && in_array('7', $dayOfWeek)}checked{/if}></td><td>{'LBL_DAY6'|@getTranslatedString:'Calendar'}</td>
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
						<div class="wfslabel" style="width: 100%;">{'LBL_NEXT_TRIGGER_TIME'|@getTranslatedString:$MODULE_NAME}:&nbsp;{$wfnexttrigger_time}</div>
					</div>
				{/if}
			</div>
		{/if}
		</td></tr>
	<tr><td><input type="radio" name="execution_condition" value="RECORD_ACCESS_CONTROL" onclick="onschedule_preparescreen(this);"
		{if $workflow->executionConditionAsLabel() eq 'RECORD_ACCESS_CONTROL'}checked{/if} {if $workflow->executionConditionAsLabel() eq 'MANUAL'}disabled{/if} /></td>
		<td>{$MOD.LBL_RECORD_ACCESS_CONTROL}.</td></tr>
	<tr><td><input type="radio" name="execution_condition" value="MANUAL"
		{if $workflow->executionConditionAsLabel() eq 'MANUAL'}checked{/if} disabled /></td>
		<td>{$MOD.LBL_MANUAL}.</td></tr>
</table>
