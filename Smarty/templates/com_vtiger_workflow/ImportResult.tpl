<div class="slds-card slds-m-around--x-small" style="height: 75vh;">
<div id="view" class="workflows-list">
	{include file='com_vtiger_workflow/ModuleTitle.tpl'}
</div>
<div class="slds-p-around_small">
	<table class="slds-table slds-table_cell-buffer slds-table_bordered">
	<thead>
	<tr class="slds-line-height_reset">
		<th style="width:68px;text-align:center;" scope="col">
			<div class="slds-truncate" title="{'LBL_RESULT'|@getTranslatedString:'Import'}">{'LBL_ROW'|@getTranslatedString:'com_vtiger_workflow'}</div>
		</th>
		<th style="width:98px;text-align:center;" scope="col">
			<div class="slds-truncate" title="{'LBL_RESULT'|@getTranslatedString:'Import'}">{'LBL_RESULT'|@getTranslatedString:'Import'}</div>
		</th>
		<th class="" scope="col">
			<div class="slds-truncate" title="{'com_vtiger_workflow'|@getTranslatedString:$MODULE}">{'com_vtiger_workflow'|@getTranslatedString:$MODULE}</div>
		</th>
	</tr>
	</thead>
	<tbody>
	{foreach from=$IMPRDO item=item}
	<tr class="slds-hint-parent">
		<td style="width:68px;text-align:center;" data-label="{'LBL_ROW'|@getTranslatedString:'com_vtiger_workflow'}">
			{$item['row']}
		</td>
		<td style="width:98px;text-align:center;" data-label="{'LBL_RESULT'|@getTranslatedString:'Import'}">
		{if $item['result']}
			<div class="slds-truncate" title="{'Accepted'|@getTranslatedString:'Quotes'}">
				<svg class="slds-button__icon slds-button__icon_left slds-icon-text-success" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#approval"></use>
				</svg>
			</div>
		{else}
			<div class="slds-truncate" title="{'Rejected'|@getTranslatedString:'Quotes'}">
				<svg class="slds-button__icon slds-button__icon_left slds-icon-text-error" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#close"></use>
				</svg>
			</div>
		{/if}
		</td>
		<td data-label="{'com_vtiger_workflow'|@getTranslatedString:$MODULE}">
			<div class="slds-truncate">
			{if $item['result']}
				<a href="{$item['url']}">{$item['summary']}</a>
			{else}
				{$item['summary']}
			{/if}
			</div>
		</td>
	</tr>
	{/foreach}
	</tbody>
	</table>
</div>
</div>
