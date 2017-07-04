<td align=left>
	<br>
<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
	<tr>
		<td>
		<table border=0 cellspacing=0 cellpadding=3 width=100%>
			<tr>
				<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
				<td id="cellTabInvite" class="dvtSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','on');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','off');ghide('addEventAlarmUI');dispLayer('addEventInviteUI');ghide('addEventRepeatUI');">{$MOD.LBL_INVITE}</a></td>
				<td class="dvtTabCache" style="width:10px">&nbsp;</td>
				{if $LABEL.reminder_time neq ''}
				<td id="cellTabAlarm" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','on');switchClass('cellTabRepeat','off');dispLayer('addEventAlarmUI');ghide('addEventInviteUI');ghide('addEventRepeatUI');">{$MOD.LBL_REMINDER}</a></td>
				{/if}
				<td class="dvtTabCache" style="width:10px">&nbsp;</td>
				{if $LABEL.recurringtype neq ''}
				<td id="cellTabRepeat" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','on');ghide('addEventAlarmUI');ghide('addEventInviteUI');dispLayer('addEventRepeatUI');">{$MOD.LBL_REPEAT}</a></td>
				{/if}
				<td class="dvtTabCache" style="width:100%">&nbsp;</td>
			</tr>
		</table></td>
	</tr>

	<tr>
		<td width=100% valign=top align=left class="dvtContentSpace" style="padding:10px;height:120px"><!-- Invite UI -->
		<DIV id="addEventInviteUI" style="display:block;width:100%">
			<table width="100%" cellpadding="5" cellspacing="0" border="0">
				<tr>
					<td width="30%" valign="top" align=right><b>{$MOD.LBL_USERS}</b></td>
					<td width="70%" align=left valign="top"> {foreach item=username key=userid from=$INVITEDUSERS}
					{$username}
					<br>
					{/foreach} </td>
				</tr>
			</table>
		</DIV><!-- Reminder UI -->
		<DIV id="addEventAlarmUI" style="display:none;width:100%">
			{if $LABEL.reminder_time != ''}
			<table width="100%" cellpadding="5" cellspacing="0" border="0">
				<tr>
					<td width="30%" align=right><b>{$MOD.LBL_SENDREMINDER}</b></td>
					<td width="70%" align=left>{$ACTIVITYDATA.set_reminder}</td>
				</tr>
				{if $ACTIVITYDATA.set_reminder != 'No'}
				<tr>
					<td width="30%" align=right><b>{$MOD.LBL_RMD_ON}</b></td>
					<td width="70%" align=left>{$ACTIVITYDATA.reminder_str}</td>
				</tr>
				{/if}
			</table>
			{/if}
		</DIV><!-- Repeat UI -->
		<div id="addEventRepeatUI" style="display:none;width:100%">
			{if $LABEL.recurringtype neq ''}
			<table width="100%" cellpadding="5" cellspacing="0" border="0">
				<tr>
					<td width="30%" align=right><b>{$MOD.LBL_ENABLE_REPEAT}</b></td>
					<td width="70%" align=left>{$ACTIVITYDATA.recurringcheck}</td>
				</tr>
				{if $ACTIVITYDATA.repeat_frequency neq ''}
				<tr>
					<td width="30%" align=right>&nbsp;</td>
					<td>{$MOD.LBL_REPEATEVENT}&nbsp;{$ACTIVITYDATA.repeat_frequency}&nbsp;{$MOD[$ACTIVITYDATA.recurringtype]}</td>
				</tr>
				{/if}
				{if $ACTIVITYDATA.repeat_str neq ''}
				<tr>
					<td width="30%" align=right>&nbsp;</td>
					<td>{$ACTIVITYDATA.repeat_str}</td>
				</tr>
				{/if}
			</table>
			{/if}
		</div>
		</td>
	</tr>
</table>
</td>