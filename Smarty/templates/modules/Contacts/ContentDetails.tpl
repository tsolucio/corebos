{*<!--
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
-->*}
<style>
{literal}
    .paddingLeftRight10px{
	padding-left: 10px;
	padding-right: 10px;
}
{/literal}
</style>
<div class="row-fluid paddingLeftRight10px content" style="min-width: 800px;">
    <table border=0 cellspacing=0 cellpadding=5 width=100% class=layerHeadingULine>
            <tr>
                    <td width="90%" align="left" class="genHeaderSmall">{$MOD.SYNC_RESULTS}&nbsp;</td>
                    <td width="10%" align="right">
                            <a href="javascript:fninvsh('GoogleContactsSettings');"><img title="{$APP.LBL_CLOSE}" alt="{$APP.LBL_CLOSE}" src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" /></a>
                    </td>
            </tr>
    </table>
    {foreach from=$RECORDS item=RECORD key=type }
        {if $type eq 'vtiger'}
            <div class='row-fluid'>
                <span class="span12"><b>{$MOD.LBL_UPDATES_CRM}</b></span>
                <div class="row-fluid"><span class="span7 "> {$MOD.LBL_ADDED} :</span><span class='span5 '>{$RECORD['create']} </span></div>
                <div class="row-fluid"><span class="span7 "> {$MOD.LBL_UPDATED} :</span> <span class='span5 '>{$RECORD['update']} </span></div>
                <div class="row-fluid"><span class="span7 "> {$MOD.LBL_DELETED} :</span> <span class='span5 '>{$RECORD['delete']} </span></div>
                {if $RECORD['more']}
					<div class="row-fluid"><span style='background:#FFFBCF;' class="span11" title="{$MOD.LBL_MORE_VTIGER}">{$MOD.LBL_MORE_VTIGER}</span>
                {/if}
            </div>
         {else}
            <br>
            <div class='row-fluid'> 
                <span class="span12"><b>{$MOD.LBL_UPDATES_GOOGLE}</b></span>
                <div class="row-fluid"><span class="span7 "> {$MOD.LBL_ADDED} :</span><span class='span5 '>{$RECORD['create']} </span></div>
                <div class="row-fluid"><span class="span7 "> {$MOD.LBL_UPDATED} :</span> <span class='span5 '>{$RECORD['update']} </span></div>
                <div class="row-fluid"><span class="span7 "> {$MOD.LBL_DELETED} :</span> <span class='span5 '>{$RECORD['delete']} </span></div>
                {if $RECORD['more']}
					<div class="row-fluid"><span style='background:#FFFBCF;' class="span11" title="{$MOD.LBL_MORE_GOOGLE}">{$MOD.LBL_MORE_GOOGLE}</span>
                {/if}
            </div>
         {/if}
    {/foreach}
    {*<div class='row-fluid'>
        {if $SYNCTIME}<p class="muted span12"><small title="{Vtiger_Util_Helper::formatDateTimeIntoDayString($SYNCTIME)}">{$MOD.LBL_SYNCRONIZED} : {Vtiger_Util_Helper::formatDateDiffInStrings($SYNCTIME)}</small></p>{/if}
    </div>*}
{if $NORECORDS}
        <input type="hidden" value='yes' id ='norefresh'/>
{/if} 
    <table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
            <tr>
                <td align=center class="small">
                    <input type="button" name="cancel_syncsetting" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="crmbutton small cancel" onclick="fninvsh('GoogleContactsSettings');" />
                </td>
            </tr>
    </table>
</div>
