<td align=left colspan="4">
	<br>
	<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
	<tr><td>
		<table border=0 cellspacing=0 cellpadding=3 width=100%>
		<tr>
			<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
			<td id="cellTabInvite" class="dvtSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','on');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','off');hide('addEventAlarmUI');showBlock('addEventInviteUI');hide('addEventRepeatUI');">{$MOD.LBL_INVITE}</a></td>
			<td class="dvtTabCache" style="width:10px">&nbsp;</td>
			{if $LABEL.reminder_time neq ''}
				<td id="cellTabAlarm" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','on');switchClass('cellTabRepeat','off');showBlock('addEventAlarmUI');hide('addEventInviteUI');hide('addEventRepeatUI');">{$MOD.LBL_REMINDER}</a></td>
			{/if}
			<td class="dvtTabCache" style="width:10px">&nbsp;</td>
			{if $LABEL.recurringtype neq ''}
				<td id="cellTabRepeat" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','on');hide('addEventAlarmUI');hide('addEventInviteUI');showBlock('addEventRepeatUI');">{$MOD.LBL_REPEAT}</a></td>
			{/if}
			<td class="dvtTabCache" style="width:100%">&nbsp;</td>
		</tr>
		</table>
	</td></tr>
	<tr>
		<td width=100% valign=top align=left class="dvtContentSpace" style="padding:10px;height:120px">
			<!-- Invite UI -->
			<div id="addEventInviteUI" style="display:block;width:100%">
				<table border=0 cellspacing=0 cellpadding=2 width=100%>
					<tr>
						<td valign=top>
							<table border=0 cellspacing=0 cellpadding=2 width=100%>
								<tr>
									<td><b>{$MOD.LBL_AVL_USERS}</b></td>
									<td>&nbsp;</td>
									<td><b>{$MOD.LBL_SEL_USERS}</b></td>
								</tr>
								<tr>
									<td width=40% align=center valign=top>
									<select name="availableusers" id="availableusers" class=small size=5 multiple style="height:70px;width:100%">
										{foreach item=username key=userid from=$USERSLIST}
											{if $userid != ''}
											<option value="{$userid}">{$username}</option>
											{/if}
										{/foreach}
									</select>
									</td>
									<td width=20% align=center valign=top>
									<input type=button value="{$MOD.LBL_ADD_BUTTON} >>" class="crm button small save" style="width:100%"  onClick="incUser('availableusers','selectedusers');formSelectColumnString('inviteesid','selectedusers');"><br>
									<input type=button value="<< {$MOD.LBL_RMV_BUTTON} " class="crm button small cancel" style="width:100%" onClick="rmvUser('selectedusers');formSelectColumnString('inviteesid','selectedusers');">
									</td>
									<td width=40% align=center valign=top>
									<input type=hidden name="inviteesid" id="inviteesid" value="">
									<select name="selectedusers" id="selectedusers" class=small size=5 multiple style="height:70px;width:100%">
										{foreach item=username key=userid from=$INVITEDUSERS}
											{if $userid != ''}
											<option value="{$userid}">{$username}</option>
											{/if}
										{/foreach}
									</select>
									<div align=left> {$MOD.LBL_SELUSR_INFO}
									</div>
									</td>
								</tr>
								<tr><td colspan=3>
									<ul style="padding-left:20px">
									<li>{$MOD.LBL_INVITE_INST1}
									<li>{$MOD.LBL_INVITE_INST2}
									</ul>
								</td></tr>
							</table>
						</td>
					</tr>
				</table>
				</div>
				<!-- Reminder UI -->
				<div id="addEventAlarmUI" style="display:none;width:100%">
				{if $LABEL.reminder_time neq ''}
					<table>
						{assign var=secondval value=$secondvalue.reminder_time}
						{assign var=check value=$secondval[0]}
						{assign var=yes_val value=$secondval[1]}
						{assign var=no_val value=$secondval[2]}

						<tr><td>{$LABEL.reminder_time}</td><td>

						{if $check eq 'CHECKED'}
							{assign var=reminstyle value='style="display:block;width:100%"'}
							<input type="radio" name="set_reminder" value="Yes" {$check} onClick="showBlock('reminderOptions')">&nbsp;{$yes_val}&nbsp;
							<input type="radio" name="set_reminder" value="No" onClick="fnhide('reminderOptions')">&nbsp;{$no_val}&nbsp;
	
						{else}
							{assign var=reminstyle value='style="display:none;width:100%"'}
							<input type="radio" name="set_reminder" value="Yes" onClick="showBlock('reminderOptions')">&nbsp;{$yes_val}&nbsp;
							<input type="radio" name="set_reminder" value="No" checked onClick="fnhide('reminderOptions')">&nbsp;{$no_val}&nbsp;
	
						{/if}
						</td></tr>
					</table>
					<div id="reminderOptions" {$reminstyle}>
						<table border=0 cellspacing=0 cellpadding=2  width=100%>
							<tr>
								<td nowrap align=right width=20% valign=top><b>{$MOD.LBL_RMD_ON} : </b></td>
								<td width=80%>
									<table border=0>
									<tr>
										<td colspan=2>
										{foreach item=val_arr from=$ACTIVITYDATA.reminder_time}
										{assign var=start value=$val_arr[0]}
										{assign var=end value=$val_arr[1]}
										{assign var=sendname value=$val_arr[2]}
										{assign var=disp_text value=$val_arr[3]}
										{assign var=sel_val value=$val_arr[4]}
										<select name="{$sendname}">
										{section name=reminder start=$start max=$end loop=$end step=1 }
										{if $smarty.section.reminder.index eq $sel_val}
											<OPTION VALUE="{$smarty.section.reminder.index}" SELECTED>{$smarty.section.reminder.index}</OPTION>
										{else}
											<OPTION VALUE="{$smarty.section.reminder.index}" >{$smarty.section.reminder.index}</OPTION>
										{/if}
										{/section}
										</select>
										&nbsp;{$disp_text}
										{/foreach}
										</td>
									</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
				{/if}
				</div>
				<!-- Repeat UI -->
				<div id="addEventRepeatUI" style="display:none;width:100%">
				{if $LABEL.recurringtype neq ''}
				<table border=0 cellspacing=0 cellpadding=2  width=100%>
					<tr>
						<td nowrap align=right width=20% valign=top>
							<strong>{$MOD.LBL_REPEAT}</strong>
						</td>
						<td nowrap width=80% valign=top>
							<table border=0 cellspacing=0 cellpadding=0>
							<tr>

								<td width=20>
								{if $ACTIVITYDATA.recurringcheck eq 'Yes'}
									{assign var=rptstyle value='style="display:block"'}
									{if $ACTIVITYDATA.eventrecurringtype eq 'Daily'}
										{assign var=rptmonthstyle value='style="display:none"'}
										{assign var=rptweekstyle value='style="display:none"'}
									{elseif $ACTIVITYDATA.eventrecurringtype eq 'Weekly'}
										{assign var=rptmonthstyle value='style="display:none"'}
										{assign var=rptweekstyle value='style="display:block"'}
									{elseif $ACTIVITYDATA.eventrecurringtype eq 'Monthly'}
										{assign var=rptmonthstyle value='style="display:block"'}
										{assign var=rptweekstyle value='style="display:none"'}
									{elseif $ACTIVITYDATA.eventrecurringtype eq 'Yearly'}
										{assign var=rptmonthstyle value='style="display:none"'}
										{assign var=rptweekstyle value='style="display:none"'}
									{/if}
									<input type="checkbox" name="recurringcheck" onClick="showhide('repeatOptions')" checked>
								{else}
									{assign var=rptstyle value='style="display:none"'}
									{assign var=rptmonthstyle value='style="display:none"'}
									{assign var=rptweekstyle value='style="display:none"'}
									<input type="checkbox" name="recurringcheck" onClick="showhide('repeatOptions')">
								{/if}
								&nbsp;&nbsp;{$MOD.LBL_ENABLE_REPEAT}
								</td>
								<td>&nbsp;<td>
							</tr>
							<tr>
								<td colspan=2>
								<div id="repeatOptions" {$rptstyle}>
								<table border=0 cellspacing=0 cellpadding=2>
								<tr>
								<td>{$MOD.LBL_REPEAT_ONCE}</td>
								<td>
								<select name="repeat_frequency">
								{section name="repeat" loop=15 start=1 step=1}
									{if $smarty.section.repeat.iteration eq $ACTIVITYDATA.repeat_frequency}
										{assign var="test" value="selected"}
									{else}
										{assign var="test" value=""}
									{/if}
									<option "{$test}" value="{$smarty.section.repeat.iteration}">{$smarty.section.repeat.iteration}</option>
								{/section}
								</select>
								</td>
								<td>
									<select name="recurringtype" onChange="rptoptDisp(this)">
										<option value="Daily" {if $ACTIVITYDATA.eventrecurringtype eq 'Daily'} selected {/if}>{$MOD.LBL_DAYS}</option>
										<option value="Weekly" {if $ACTIVITYDATA.eventrecurringtype eq 'Weekly'} selected {/if}>{$MOD.LBL_WEEKS}</option>
										<option value="Monthly" {if $ACTIVITYDATA.eventrecurringtype eq 'Monthly'} selected {/if}>{$MOD.LBL_MONTHS}</option>
										<option value="Yearly" {if $ACTIVITYDATA.eventrecurringtype eq 'Yearly'} selected {/if}>{$MOD.LBL_YEAR}</option>
									</select>
									<!-- Repeat Feature Enhanced -->
									<b>{$MOD.LBL_UNTIL}</b> <input type="text" name="calendar_repeat_limit_date" id="calendar_repeat_limit_date" class="textbox" style="width:90px" value="" ></td><td align="left"><img border=0 src="{$IMAGE_PATH}btnL3Calendar.gif" alt="{$MOD.LBL_SET_DATE}..." title="{$MOD.LBL_SET_DATE}..." id="jscal_trigger_calendar_repeat_limit_date">
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "calendar_repeat_limit_date",
											ifFormat : "{$REPEAT_LIMIT_DATEFORMAT}",
											showsTime : false,
											button : "jscal_trigger_calendar_repeat_limit_date",
											singleClick : true,
											step : 1
										});
									</script>
								</td>
								</tr>
							</table>
							<div id="repeatWeekUI" {$rptweekstyle}>
								<table border=0 cellspacing=0 cellpadding=2>
								<tr>
									<td><input name="sun_flag" value="sunday" {$ACTIVITYDATA.week0} type="checkbox"></td><td>{$MOD.LBL_SM_SUN}</td>
									<td><input name="mon_flag" value="monday" {$ACTIVITYDATA.week1} type="checkbox"></td><td>{$MOD.LBL_SM_MON}</td>
									<td><input name="tue_flag" value="tuesday" {$ACTIVITYDATA.week2} type="checkbox"></td><td>{$MOD.LBL_SM_TUE}</td>
									<td><input name="wed_flag" value="wednesday" {$ACTIVITYDATA.week3} type="checkbox"></td><td>{$MOD.LBL_SM_WED}</td>
									<td><input name="thu_flag" value="thursday" {$ACTIVITYDATA.week4} type="checkbox"></td><td>{$MOD.LBL_SM_THU}</td>
									<td><input name="fri_flag" value="friday" {$ACTIVITYDATA.week5} type="checkbox"></td><td>{$MOD.LBL_SM_FRI}</td>
									<td><input name="sat_flag" value="saturday" {$ACTIVITYDATA.week6} type="checkbox"></td><td>{$MOD.LBL_SM_SAT}</td>
								</tr>
								</table>
							</div>
							<div id="repeatMonthUI" {$rptmonthstyle}>
								<table border=0 cellspacing=0 cellpadding=2>
								<tr>
									<td>
										<table border=0 cellspacing=0 cellpadding=2>
										<tr>
										<td><input type="radio" checked name="repeatMonth" {if $ACTIVITYDATA.repeatMonth eq 'date'} checked {/if} value="date"></td>
										<td>{$MOD.on}</td>
										<td><input type="text" class=textbox style="width:20px" value="{$ACTIVITYDATA.repeatMonth_date}" name="repeatMonth_date" ></td>
										<td>{$MOD['day of the month']}</td>
										</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>
										<table border=0 cellspacing=0 cellpadding=2>
										<tr>
										<td><input type="radio" name="repeatMonth" {if $ACTIVITYDATA.repeatMonth eq 'day'} checked {/if} value="day"></td>
										<td>{$MOD.on}</td>
										<td>
										<select name="repeatMonth_daytype">
											<option value="first" {if $ACTIVITYDATA.repeatMonth_daytype eq 'first'} selected {/if}>{$MOD.First}</option>
											<option value="second" {if $ACTIVITYDATA.repeatMonth_daytype eq 'second'} selected {/if}>{$MOD.Second}</option>
											<option value="third" {if $ACTIVITYDATA.repeatMonth_daytype eq 'third'} selected {/if}>{$MOD.Third}</option>
											<option value="last" {if $ACTIVITYDATA.repeatMonth_daytype eq 'last'} selected {/if}>{$MOD.Last}</option>
										</select>
										</td>
										<td>
										<select name="repeatMonth_day">
											<option value=1 {if $ACTIVITYDATA.repeatMonth_day eq 1} selected {/if}>{$MOD.LBL_DAY1}</option>
											<option value=2 {if $ACTIVITYDATA.repeatMonth_day eq 2} selected {/if}>{$MOD.LBL_DAY2}</option>
											<option value=3 {if $ACTIVITYDATA.repeatMonth_day eq 3} selected {/if}>{$MOD.LBL_DAY3}</option>
											<option value=4 {if $ACTIVITYDATA.repeatMonth_day eq 4} selected {/if}>{$MOD.LBL_DAY4}</option>
											<option value=5 {if $ACTIVITYDATA.repeatMonth_day eq 5} selected {/if}>{$MOD.LBL_DAY5}</option>
											<option value=6 {if $ACTIVITYDATA.repeatMonth_day eq 6} selected {/if}>{$MOD.LBL_DAY6}</option>
										</select>
										</td>
										</tr>
										</table>
									</td>
								</tr>
								</table>
							</div>

							</div>
						</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		{/if}
		</div>
	</td>
	</tr>
	</table>
</td>