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
<div id="EditInv" class="layerPopup" style="width:270px;">
<input id="min_freq" type="hidden" value="{$MIN_CRON_FREQUENCY}">
<input id="desc" type="hidden" value="{'LBL_MINIMUM_FREQUENCY'|@getTranslatedString:'CronTasks'} {$MIN_CRON_FREQUENCY} {'LBL_MINUTES'|@getTranslatedString:'CronTasks'}" size="35" maxlength="40">

<table border=0 cellspacing=0 cellpadding=5 width=100% class=layerHeadingULine>
	<tr>
		<td class="layerPopupHeading moduleName" align="left">{$CRON_DETAILS.label}</td>
		<td align="right" class="small"><img onClick="hide('editdiv');" style="cursor:pointer;" src="{'close.gif'|@vtiger_imageurl:$THEME}" align="middle" border="0"></td>
	</tr>
</table>

<table border=0 cellspacing=0 cellpadding=5 width=95% align=center>
	<tr>
		<td class="small">
			<table border="0" celspacing="0" cellpadding="5" width="100%" align="center" class="scheduler-popup">
				<tr>
					<td class="dvtCellLabel small" width="20%"><b>{$MOD.LBL_STATUS} :</b></td>
					<td align="left" class="dvtCellInfo small" width="80%">
						<select class="small slds-select" id="cron_status" name="cron_status">
							{if $CRON_DETAILS.status eq 1}
								<option value="1" selected>{$MOD.LBL_ACTIVE}</option>
								<option value="0">{$MOD.LBL_INACTIVE}</option>
							{else}
								<option value="1">{$MOD.LBL_ACTIVE}</option>
								<option value="0" selected>{$MOD.LBL_INACTIVE}</option>
							{/if}
						</select>
					</td>
				</tr>
				<tr>
					<td align="right" class="dvtCellLabel small"><b>{$MOD.LBL_FREQUENCY}</b></td>
					<td align="left" class="dvtCellInfo small" width="104px">
						<input class="txtBox slds-input" id="CronTime" name="CronTime" value="{$CRON_DETAILS.frequency}" style="width:45px;{if $CRON_DETAILS.time eq 'daily'}display: none;{/if}" type="text">
						<input class="txtBox slds-input" id="CronDay" name="CronDay" value="{if $CRON_DETAILS.time neq 'daily'}00:00{else}{$CRON_DETAILS.hourmin}{/if}" style="width:50px; {if $CRON_DETAILS.time neq 'daily'}display: none;{/if}" type="text">
						<select class="small slds-select" id="cron_time" style="width: 65%" name="cron_status" onchange="change_input_time()">
							{if $CRON_DETAILS.time eq 'daily'}
								<option value="min">{$MOD.LBL_MINUTES}</option>
								<option value="hours">{$MOD.LBL_HOURS}</option>
								<option value="daily" selected>{$MOD.LBL_DAILY}</option>
							{elseif $CRON_DETAILS.time eq 'hour'}
								<option value="min" >{$MOD.LBL_MINUTES}</option>
								<option value="hours" selected>{$MOD.LBL_HOURS}</option>
								<option value="daily">{$MOD.LBL_DAILY}</option>
							{else}
								<option value="min" selected>{$MOD.LBL_MINUTES}</option>
								<option value="hours">{$MOD.LBL_HOURS}</option>
								<option value="daily">{$MOD.LBL_DAILY}</option>
							{/if}
						</select>
					</td>
				</tr>
				<tr>
					<td colspan=2>
					{$CRON_DETAILS.description}
					</td>
				<tr>
			</table>
		</td>
	</tr>
</table>

<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
	<tr>
		<td align="center" class="small" style="padding: 5px;background-color: #f7f9fb;">
			<input name="save" value="{$APP.LBL_SAVE_BUTTON_LABEL}" class="slds-button slds-button--small slds-button_success" type="button" onClick="fetchSaveCron('{$CRON_DETAILS.id}')">
			<input name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="slds-button slds-button--small slds-button--destructive" type="button" onClick="hide('editdiv');">
		</td>
	</tr>
</table>

</div>