{*<!-- this template file creates the tooltip information from a given text.
this is the default template for tooltip. it presents the tooltip information in
a linear way, i.e. makes the fieldlabel bold and put the value after it.
e.g. <b>fieldlabel:</b> fieldvalue-->*}

{assign var=tip value=""}
{foreach key=label item=value from=$TEXT}
	{assign var=tip value="$tip<b>$label:</b>&nbsp; $value<br>"}
{/foreach}
{$tip}
{if !empty($Products)}
<table>
	<tr>
		<td><strong>{$CMOD.LBL_QUANTITY}</strong></td>
		<td><strong>{$CMOD.LBL_PRODUCT}</strong></td>
		<td><strong>{$CMOD.LBL_PRICE}</strong></td>
	</tr>
	{foreach key=row_no item=data from=$Products}
		{assign var="prod" value="prod"|cat:$row_no}
		{assign var="qta" value="qta"|cat:$row_no}
		{assign var="price" value="price"|cat:$row_no}
		<tr>
			<td>{$data.$qta}</td>
			<td>{$data.$prod}</td>
			<td>{$data.$price}</td>
		</tr>
	{/foreach}
</table>
<br>
{/if}