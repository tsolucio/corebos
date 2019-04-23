<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small">
<tr>
	<td id="group_tax_div_title" colspan="2" nowrap align="left" ></td>
	<td align="right"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0" onClick="fnhide('group_tax_div')" style="cursor:pointer;"></td>
</tr>
{foreach item=tax_detail name=group_tax_loop key=loop_count from=$GROUP_TAXES}
<tr>
	<td align="left" class="lineOnTop">
		<input type="text" class="small" size="5" name="{$tax_detail.taxname}_group_percentage" id="group_tax_percentage{$smarty.foreach.group_tax_loop.iteration}" value="{$tax_detail.percentage}" onBlur="calcTotal()">&nbsp;%
	</td>
	<td align="center" class="lineOnTop">{$tax_detail.taxlabel}</td>
	<td align="right" class="lineOnTop">
		<input type="text" class="small" size="6" name="{$tax_detail.taxname}_group_amount" id="group_tax_amount{$smarty.foreach.group_tax_loop.iteration}" style="cursor:pointer;" value="0.00" readonly>
	</td>
</tr>
{/foreach}
<input type="hidden" id="group_tax_count" value="{$smarty.foreach.group_tax_loop.iteration}">
</table>
