<table class="slds-table slds-table_cell-buffer slds-table_bordered">
<thead>
<tr class="slds-line-height_reset">
	<th id="group_tax_div_title" colspan="2" class="slds-p-left_none" scope="col" colspan="2"></th>
	<th class="cblds-t-align_right slds-p-right_none" scope="col"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0" onClick="fnhide('group_tax_div')" style="cursor:pointer;"></th>
</tr>
</thead>
<tbody>
{foreach item=tax_detail name=group_tax_loop key=loop_count from=$GROUP_TAXES}
{assign var=taxfilledvalue value=$tax_detail.percentage}
{assign var=taxfilledpercent value=''}
{if $MODE != 'edit' && $CONVERT_MODE == ''}
	{if $TAXFILLINMODE=='None'}
		{assign var=taxfilledvalue value=0.00}
		{assign var=taxfilledpercent value=$tax_detail.percentage}
	{elseif $TAXFILLINMODE=='Default'}
		{if $tax_detail.default==1}
			{assign var=taxfilledvalue value=$tax_detail.percentage}
		{else}
			{assign var=taxfilledvalue value=0.00}
		{/if}
		{assign var=taxfilledpercent value=$tax_detail.percentage}
	{/if}
{/if}
<tr class="slds-hint-parent">
	<td scope="row" class="slds-p-left_none">
		<input type="text" class="small" size="5" name="{$tax_detail.taxname}_group_percentage" id="group_tax_percentage{$smarty.foreach.group_tax_loop.iteration}" value="{$taxfilledvalue}" onBlur="calcTotal()">&nbsp;{$taxfilledpercent}%
	</td>
	<td>{$tax_detail.taxlabel}</td>
	<td class="cblds-t-align_right slds-p-right_none">
		<input type="text" class="small" size="6" name="{$tax_detail.taxname}_group_amount" id="group_tax_amount{$smarty.foreach.group_tax_loop.iteration}" style="cursor:pointer;" value="0.00" readonly>
	</td>
</tr>
{/foreach}
</tbody>
</table>
<input type="hidden" id="group_tax_count" value="{$smarty.foreach.group_tax_loop.iteration}">
