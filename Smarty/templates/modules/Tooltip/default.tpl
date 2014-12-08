{*<!-- this template file creates the tooltip information from a given text.
this is the default template for tooltip. it presents the tooltip information in
a linear way, i.e. makes the fieldlabel bold and put the value after it.
e.g. <b>fieldlabel:</b>  fieldvalue-->*}

{assign var=tip value=""}
{foreach key=label item=value from=$TEXT}
	{assign var=tip value="$tip<b>$label:</b>&nbsp; $value<br>"}
{/foreach}
{$tip}
