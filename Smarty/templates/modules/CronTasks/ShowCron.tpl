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
<div id="EditInv" class="layerPopup">
	<table border=0 cellspacing=0 cellpadding=5 width=100% class=layerHeadingULine>
		<tr>
			<td class="layerPopupHeading" align="left">{$CRON_DETAILS.label}</td>
			<td align="right" class="small"><img onClick="hide('editdiv');" style="cursor: pointer;" src="{'close.gif'|@vtiger_imageurl:$THEME}" align="middle" border="0"></td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=95% align=center>
		<tr>
			<td class="small">
				<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
					<tr>
						<td align="right" class="cellLabel small" width="40%"><b>{$MOD.LBL_STATUS}:</b></td>
						<td align="left" class="cellText small" width="60%">{if $CRON_DETAILS.status eq 1} {$MOD.LBL_ACTIVE} {else} {$MOD.LBL_INACTIVE} {/if}</td>
					</tr>
					<tr>
						<td align="right" class="cellLabel small"><b>{$MOD.LBL_FREQUENCY}</b></td>
						<td align="left" class="cellText small" width="104px">{$CRON_DETAILS.frequency} {if $CRON_DETAILS.time eq 'min'} {$MOD.LBL_MINS} {else} {$MOD.LBL_HOURS} {/if}</td>
					</tr>
					<tr>
						<td colspan=2>{$CRON_DETAILS.description}</td>
					<tr>
				</table>
			</td>
		</tr>
	</table>
</div>
