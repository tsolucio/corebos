{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  coreBOS Open Source
 * The Initial Developer of the Original Code is coreBOS
 * All Rights Reserved.
 ********************************************************************************/
-->*}
{if $showDesert}
	{assign var='DESERTInfo' value='LBL_NO_DATA'|@getTranslatedString:$MODULE}
	{include file='Components/Desert.tpl'}
{else}
<script src="include/pivottable/pivot.js"></script>
<link href="include/pivottable/pivot.css" rel="stylesheet">

<script type="text/javascript">
{literal}
    $(function(){
        var sum = $.pivotUtilities.aggregatorTemplates.sum;
        var numberFormat = $.pivotUtilities.numberFormat;
        var intFormat = numberFormat({digitsAfterDecimal: 0});
        $("#output").pivot(
            [
               {/literal}{$RECORDS}{literal}
            ],
            {
                rows: [{/literal}{$ROWS}{literal}],
                cols: [{/literal}{$COLS}{literal}],
                {/literal}{$aggreg}{literal}
            }
        );
     });
{/literal}
</script>
<div id="output" style="margin: 30px;overflow-x: scroll; width:1000px; "></div>
{/if}