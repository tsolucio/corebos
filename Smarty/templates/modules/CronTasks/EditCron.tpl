{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
<div id="EditInv" class="layerPopup">
<input id="min_freq" type="hidden" value="{$MIN_CRON_FREQUENCY}">
<input id="desc" type="hidden" value="{'LBL_MINIMUM_FREQUENCY'|@getTranslatedString:$MODULE} {$MIN_CRON_FREQUENCY} {'LBL_MINS'|@getTranslatedString:$MODULE}" size="35" maxlength="40">
<table border=0 cellspacing=0 cellpadding=5 width=100% class=layerHeadingULine>
<tr>
	<td class="layerPopupHeading" align="left">{$CRON_DETAILS.label}</td>
	<td align="right" class="small"><img onClick="hide('editdiv');" style="cursor:pointer;" src="{'close.gif'|@vtiger_imageurl:$THEME}" align="middle" border="0"></td>
</tr>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=95% align=center>
<tr>
	<td class="small">
	<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
	<tr>
		<td align="right"  class="cellLabel small" width="40%"><b>{$MOD.LBL_STATUS} :</b></td>
	<td align="left"  class="cellText small" width="60%">
		<select class="small" id="cron_status" name="cron_status">
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
		<td align="right" class="cellLabel small"><b>{$MOD.LBL_FREQUENCY}</b></td>
		<td align="left" class="cellText small" width="104px"><input class="txtBox" id="CronTime" name="CronTime" value="{$CRON_DETAILS.frequency}" style="width:25px;" type="text">
                <select class="small" id="cron_time" name="cron_status">
                {if $CRON_DETAILS.time eq 'min'}
                 <option value="min" selected>{$MOD.LBL_MINS}</option>
		<option value="hours">{$MOD.LBL_HOURS}</option>
                {else}
                 <option value="min" >{$MOD.LBL_MINS}</option>
                 <option value="hours" selected>{$MOD.LBL_HOURS}</option>
                {/if}
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
	<td align="center" class="small">
		<input name="save" value="{$APP.LBL_SAVE_BUTTON_LABEL}" class="crmButton small save" type="button" onClick="fetchSaveCron('{$CRON_DETAILS.id}')">
		<input name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" type="button" onClick="hide('editdiv');">
	</td>
	</tr>
</table>
</div>
