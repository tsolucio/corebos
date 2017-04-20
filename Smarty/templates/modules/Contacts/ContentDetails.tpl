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
<div class="row-fluid paddingLeftRight10px">
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
</div>
      
    
