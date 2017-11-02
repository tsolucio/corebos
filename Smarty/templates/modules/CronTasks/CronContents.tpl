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
<table class="slds-table slds-table--bordered  slds-table--cell-buffer listTable">
	<thead>
		<tr>
			<th class="slds-text-title--caps" scope="col"><span class="slds-truncate">#</span></th>
			<th class="slds-text-title--caps" scope="col"><span class="slds-truncate">{'Cron Job'|@getTranslatedString:'CronTasks'}</span></th>
			<th class="slds-text-title--caps" scope="col"><span class="slds-truncate">{$MOD.LBL_FREQUENCY}{$MOD.LBL_HOURMIN}</span></th>
			<th class="slds-text-title--caps" scope="col"><span class="slds-truncate">{$CMOD.LBL_STATUS}</span></th>
			<th class="slds-text-title--caps" scope="col"><span class="slds-truncate">{$CMOD.LAST_START}</span></th>
			<th class="slds-text-title--caps" scope="col"><span class="slds-truncate">{$CMOD.LAST_END}</span></th>
			<th class="slds-text-title--caps" scope="col"><span class="slds-truncate">{$CMOD.LBL_SEQUENCE}</span></th>
			<th class="slds-text-title--caps" scope="col"><span class="slds-truncate">{$CMOD.LBL_TOOLS}</span></th>
		</tr>
	</thead>
	<tbody>
		{foreach name=cronlist item=elements from=$CRON}
			<tr class="slds-hint-parent slds-line-height--reset">
				<th scope="row"><div class="slds-truncate">{$smarty.foreach.cronlist.iteration}</div></th>
				<th scope="row"><div class="slds-truncate">{$elements.cronname}</div></th>
				<th scope="row"><div class="slds-truncate">{$elements.hours}:{$elements.mins}</div></th>
				{if $elements.status eq 'Active'|@getTranslatedString:'CronTasks'}
				<th scope="row" class="active"><div class="slds-truncate">{$elements.status}</div></th>
				{elseif $elements.status eq 'LBL_RUNNING'|@getTranslatedString:'CronTasks'}
				<th scope="row" style="color: red"><div class="slds-truncate">{$elements.status}</div></th>
				{else}
				<th scope="row" class="inactive"><div class="slds-truncate">{$elements.status}</div></th>
				{/if}
				<th scope="row"><div class="slds-truncate">{$elements.laststart}</div></th>
				<th scope="row"><div class="slds-truncate">{$elements.lastend}</div></th>
				{if $smarty.foreach.cronlist.first neq true}
					<th scope="row"><div class="slds-truncate" style="padding-left: 1.5rem;">
						<a href="javascript:move_module('{$elements.id}','Up');" >
							<img src="{'arrow_up.png'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />
						</a>
				{/if}
				{if $smarty.foreach.cronlist.last eq true}
					<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />
				{/if}
				{if $smarty.foreach.cronlist.first eq true}
					<th scope="row">
						<div class="slds-truncate" style="padding-left: 1.5rem;">
							<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />
							<a href="javascript:move_module('{$elements.id}','Down');" >
								<img src="{'arrow_down.png'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />
							</a>
						</div>
					</th>
				{/if}
				{if $smarty.foreach.cronlist.last neq true && $smarty.foreach.cronlist.first neq true}
						<a href="javascript:move_module('{$elements.id}','Down');" >
							<img src="{'arrow_down.png'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />
						</a>
					</div></th>
				{/if}
				<th scope="row">
					<div class="slds-truncate" style="padding-left: 1rem;">
						<img onClick="fnvshobj(this,'editdiv');fetchEditCron('{$elements.id}');" src="{'editfield.gif'|@vtiger_imageurl:$THEME}" title="{$APP.LBL_EDIT}">
					</div>
				</th>
			</tr>
		{/foreach}
	</tbody>
</table>

