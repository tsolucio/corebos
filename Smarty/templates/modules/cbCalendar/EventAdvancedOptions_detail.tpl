<td valign=top align="left" style="padding: 0;">

<!-- Lighting Design Tab Conrols by Endrit -->
<div class="slds-truncate">
	<table class="slds-table slds-no-row-hover dvtContentSpace">
		<tr>
			<td valign="top" style="padding: 0;">
				<div class="slds-table--scoped">
					<ul class="slds-tabs--scoped__nav" role="tablist" style="margin-bottom: 0;">
						<li class="slds-tabs--scoped__item active" id="cellTabInvite" onClick="switchClass('cellTabInvite','on');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','off');ghide('addEventAlarmUI');dispLayer('addEventInviteUI');ghide('addEventRepeatUI');" role="presentation" style="border-top-left-radius: .25rem;">
							<a href="javascript:doNothing()" class="slds-tabs--scoped__link " role="tab" tabindex="0" aria-selected="true">{$MOD.LBL_INVITE}</a>
						</li>
						{if $LABEL.reminder_time neq ''}
						<li class="slds-tabs--scoped__item" id="cellTabAlarm" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','on');switchClass('cellTabRepeat','off');dispLayer('addEventAlarmUI');ghide('addEventInviteUI');ghide('addEventRepeatUI');" role="presentation">
							<a href="javascript:doNothing()" class="slds-tabs--scoped__link" role="tab" tabindex="-1" aria-selected="false" ><b>{$MOD.LBL_REMINDER}</b></a>
						</li>
						{/if}

						{if $LABEL.recurringtype neq ''}
						<li class="slds-tabs--scoped__item" id="cellTabRepeat" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','on');ghide('addEventAlarmUI');ghide('addEventInviteUI');dispLayer('addEventRepeatUI');" role="presentation">
							<a href="javascript:doNothing()" class="slds-tabs--scoped__link" role="tab" tabindex="-1" aria-selected="false" ><b>{$MOD.LBL_REPEAT}</b></a>
						</li>
						{/if}
					</ul>

					<!-- Invite UI -->
						<div id="addEventInviteUI" role="tabpanel" aria-labelledby="tab--scoped-1__item" class="slds-tabs--scoped__content slds-truncate" style="display:block;">
							<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table">
								<tr class="slds-line-height--reset">
									<td class="dvtCellLabel" width="50%"><b>{$MOD.LBL_USERS}</b></td>
									<td class="dvtCellInfo" width="50%">{foreach item=username key=userid from=$INVITEDUSERS}{$username}<br>{/foreach}</td>
								</tr>
							</table>
						</div>
					<!-- End Invite UI -->

					<!-- Reminder UI -->
						<div id="addEventAlarmUI" role="tabpanel" aria-labelledby="tab--scoped-2__item" class="slds-tabs--scoped__content slds-truncate" style="display:none;">
							{if $LABEL.reminder_time != ''}
								<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table">
									<tr class="slds-line-height--reset">
										<td class="dvtCellLabel" width="50%"><b>{$MOD.LBL_SENDREMINDER}</b></td>
										<td class="dvtCellInfo" width="50%">{$ACTIVITYDATA.set_reminder}</td>

											{if $ACTIVITYDATA.set_reminder != 'No'}
											<td class="dvtCellLabel" width="50%"><b>{$MOD.LBL_RMD_ON}</b></td>
											<td class="dvtCellInfo" width="50%">{$ACTIVITYDATA.reminder_str}</td>
										{/if}
									</tr>
								</table>
							{/if}
						</div>
					<!-- End Reminder UI -->

					<!-- Repeat UI -->
						<div id="addEventRepeatUI" role="tabpanel" aria-labelledby="tab--scoped-3__item" class="slds-tabs--scoped__content slds-truncate" style="display:none;">
							{if $LABEL.recurringtype neq ''}
								<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table">

									<tr class="slds-line-height--reset">
										<td class="dvtCellLabel" width="50%"><b>{$MOD.LBL_ENABLE_REPEAT}</b></td>
										<td class="dvtCellInfo" width="50%">{$ACTIVITYDATA.recurringcheck}</td>
									</tr>

									{if $ACTIVITYDATA.repeat_frequency neq ''}
										<tr class="slds-line-height--reset">
											<td class="dvtCellLabel" width="50%">&nbsp;</td>
											<td class="dvtCellInfo" width="50%">{$MOD.LBL_REPEATEVENT}&nbsp;{$ACTIVITYDATA.repeat_frequency}&nbsp;{$MOD[$ACTIVITYDATA.recurringtype]}</td>
										</tr>
									{/if}

									{if $ACTIVITYDATA.repeat_str neq ''}
										<tr class="slds-line-height--reset">
											<td class="dvtCellLabel" width="50%">&nbsp;</td>
											<td class="dvtCellInfo" width="50%">{$ACTIVITYDATA.repeat_str}</td>
										</tr>
									{/if}

								</table>
							{/if}
						</div>
					<!-- End Repeat UI -->

				</div>

			</td>
		</tr>
	</table>
</div>
<!-- End Lighting Design Tab Conrols by Endrit -->

</td>