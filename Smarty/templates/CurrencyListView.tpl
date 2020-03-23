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
<script type="text/javascript" src="include/js/smoothscroll.js"></script>
{include file='SetMenu.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
<div class="slds-modal__container slds-p-around_none">
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
	<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
<br>
	<div align=center>


			<!-- DISPLAY -->
			<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
			<form action="index.php" onsubmit="VtigerJS_DialogBox.block();">
			<input type="hidden" name="module" value="Settings">
			<input type="hidden" name="action" value="CurrencyEditView">
			<tr>
				<td rowspan=2 valign=top class="cblds-p_none" style="width:50px"><img src="{'currency.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_CURRENCY_SETTINGS}" style="width:48px;height:48px" border=0 title="{$MOD.LBL_CURRENCY_SETTINGS}"></td>
				<td class="heading2" valign="bottom"><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > {$MOD.LBL_CURRENCY_SETTINGS} </b></td>
			</tr>
			<tr>
				<td valign=top class="small cblds-p-v_none">{$MOD.LBL_CURRENCY_DESCRIPTION}</td>
				<td align="right" class="small cblds-t-align_right" width='40%'>
				{if isset($CRON_TASK)}
				{assign var=MODULE_NAME value="Workflow"}
					<b>
					{if $CRON_TASK->isDisabled() }{'LBL_DISABLED'|@getTranslatedString:$MODULE_NAME}{/if}
					{if $CRON_TASK->isRunning() }{'LBL_RUNNING'|@getTranslatedString:$MODULE_NAME}{/if}
					{if $CRON_TASK->isEnabled()}
						{if $CRON_TASK->hadTimedout()}
							{'LBL_LAST_SCAN_TIMED_OUT'|@getTranslatedString:$MODULE_NAME}.
						{elseif $CRON_TASK->getLastEndDateTime() neq ''}
							{'LBL_LAST_SCAN_AT'|@getTranslatedString:$MODULE_NAME}
							{$CRON_TASK->getLastEndDateTime()}
							&
							{'LBL_TIME_TAKEN'|@getTranslatedString:$MODULE_NAME}:
							{$CRON_TASK->getTimeDiff()}
							{'LBL_SHORT_SECONDS'|@getTranslatedString:$MODULE_NAME}
						{else}
						{/if}
					{/if}
					</b>
				{/if}
				</td>
			</tr>
			</table>
			<br>
			<table border=0 cellspacing=0 cellpadding=10 width=100% >
			<tr>
			<td>

			<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
				<tr>
					<td class="big"><strong>{$MOD.LBL_CURRENCY_LIST}</strong></td>
					<td class="small" align="right">&nbsp;</td>
				</tr>
			</table>

			<table width="100%" border="0" cellpadding="5" cellspacing="0" class="listTableTopButtons">
			<tr>
				<td class="small cblds-t-align_right" align=right>
					<input type="submit" value="{$MOD.LBL_NEW_CURRENCY}" class="crmButton create small">
				</td>
			</tr>
			</table>

			<div id="CurrencyListViewContents">
				{include file="CurrencyListViewEntries.tpl"}
			</div>

	<table border=0 cellspacing=0 cellpadding=5 width=100% >
	<tr><td class="small cblds-t-align_right" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td></tr>
	</table>
	</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>
	</td>
	</tr>
	</form>
	</table>
	</div>

</td>
</tr>
</tbody>
</table>

<div id="currencydiv" style="display:block;position:absolute;width:250px;"></div>
</div>
</section>
{literal}
<script>
	function deleteCurrency(currid){
		document.getElementById('status').style.display='inline';
		jQuery.ajax({
			method: 'POST',
			url:'index.php?action=SettingsAjax&file=CurrencyDeleteStep1&return_action=CurrencyListView&return_module=Settings&module=Settings&parenttab=Settings&id='+currid,
		}).done(function(response) {
			jQuery('#status').hide();
			jQuery('#currencydiv').html(response);
		});
	}

	function transferCurrency(del_currencyid){
		document.getElementById('status').style.display='inline';
		jQuery('#CurrencyDeleteLay').hide();
		var trans_currencyid=jQuery('#transfer_currency_id').val();
		jQuery.ajax({
			method: 'POST',
			url:'index.php?module=Settings&action=SettingsAjax&file=CurrencyDelete&ajax=true&delete_currency_id='+del_currencyid+'&transfer_currency_id='+trans_currencyid,
		}).done(function(response) {
			jQuery('#status').hide();
			jQuery('#CurrencyListViewContents').html(response);
		});
	}
</script>
{/literal}
