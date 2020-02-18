<script type="text/javascript" src="include/js/FieldDependencies.js"></script>
<script type="text/javascript" src="include/js/FieldDepFunc.js"></script>
<style>
.sep1 {
	background:#ffffdc;
}
</style>
{include file='MassEditHtml.tpl'}
<div class="slds-page-header">
	<div class="slds-page-header__row">
		<div class="slds-page-header__col-title">
			<div class="slds-page-header__name">
				<div class="slds-page-header__name-title">
					<h1>
					<span class="slds-page-header__title slds-truncate">{$APP.LBL_DUPLICATE_DATA_IN} {$MODULE|getTranslatedString:$MODULE}</span>
					</h1>
				</div>
			</div>
		</div>
	</div>
</div>
<table class="slds-p-around_x-small" border="0" align ="center" width ="95%">
	<tr>
		<td>
			{assign var='massDelete' value='LBL_MASS_DELETE'|getTranslatedString:$MODULE}
			{if $DELETE eq $massDelete}
				<input class="crmbutton small delete" type="button" value="{$APP.LBL_DELETE}" onclick="return delete_fields('{$MODULE}')"/>
				<input class="crmbutton small delete" type="button" value="{$APP.LBL_DELETE_DUPLICATES}" onclick="return deleteExactDuplicates('{$MODULE}')"/>
			{/if}
		</td>
		<td nowrap >
			<table border=0 cellspacing=0 cellpadding=0 class="small">
				<tr>{$NAVIGATION}</tr>
			</table>
		</td>
	</tr>
</table>

<table class="slds-table slds-table_cell-buffer slds-table_bordered">
<thead>
<tr class="slds-line-height_reset">
	<th class="" scope="col" width="40px">
	<input type="checkbox" name="CheckAll" id="dedupselectall" onclick='selectAllDel(this.checked,"del");'>
	</th>
	{foreach key=key item=field_values from=$FIELD_NAMES}
		<th class="" scope="col">
			{$key|@getTranslatedString:$MODULE}
		</th>
	{/foreach}
	<th class="" scope="col">
		{$APP.LBL_MERGE_SELECT}
	</th>
	<th class="" width="120px" scope="col">
		{$APP.LBL_ACTION}
	</th>
</tr>
</thead>
<tbody>
	{assign var=tdclass value='IvtColdata'}
	{foreach key=key1 item=data from=$ALL_VALUES}
		{assign var=cnt value=$data|@count}
		{assign var=cnt2 value=0}
		{if $tdclass eq 'IvtColdata'}
			{assign var=tdclass value='sep1'}
		{else if $tdclass eq 'sep1'}
			{assign var=tdclass value='IvtColdata'}
		{/if}
			{foreach key=key3 item=newdata1 from=$data}
				<tr class="{$tdclass}" nowrap="nowrap">
					<td>
						<input type="checkbox" name="del" value="{$data.$key3.recordid}" onclick='selectDel(this.name,"CheckAll");'>
					</td>
					{foreach key=key item=newdata2 from=$newdata1}
						<td>
							{if $key eq 'recordid'}
								<a href="index.php?module={$MODULE}&action=DetailView&record={$data.$key3.recordid}" target ="blank">{$newdata2}</a>
							{else}
								{if $key eq 'Entity Type'}
									{if $newdata2 eq 0 && $newdata2 neq NULL}
										{if $VIEW eq true}
											{$APP.LBL_LAST_IMPORTED}
										{else}
											{$APP.LBL_NOW_IMPORTED}
										{/if}
									{else}
										{$APP.LBL_EXISTING}
									{/if}
								{else}
									{$newdata2}
								{/if}
							{/if}
						</td>
					{/foreach}
					<td style="width:80px;">
						<input name="{$key1}" id="{$key1}" value="{$data.$key3.recordid}" type="checkbox" class="slds-m-left_large">
					</td>
					{if $cnt2 eq 0}
						<td rowspan='{$cnt}'>
						<input class="crmbutton small edit" name="merge" value="{$APP.LBL_MERGE}" onclick="merge_fields('{$key1}','{$MODULE}');" type="button">
						<input class="crmbutton small edit" type="button" value="{$APP.LBL_MASS_EDIT}" onclick="return mergeMassEditRecords('{$key1}', document.getElementById('dedupselectall'), 'massedit', '{$MODULE}')"/>
						</td>
					{/if}
					{assign var=cnt2 value=$cnt2+1}
				</tr>
			{/foreach}
	{/foreach}
</tbody>
</table>
<div name="group_count" id="group_count" style="display :none">
	{$NUM_GROUP}
</div>
