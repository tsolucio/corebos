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
{foreach key=index item=jsModel from=$SCRIPTS}
	<script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}
<div id = 'sync_message' class='row-fluid' >
    <div class='padding10 span12'>     
        {if $FIRSTTIME}
            <input type="hidden" id = "firsttime" value = 'no'/>
        {else}
            <input type="hidden" id = "firsttime" value = 'yes'/>
        {/if}
        <div id='sync_details'></div>
        {if $STATE eq 'home'}
            {if $SYNCTIME}
            <p class="muted" id='synctime'><small title="{Vtiger_Util_Helper::formatDateTimeIntoDayString($SYNCTIME)}">{vtranslate('LBL_SYNCRONIZED',$MODULE_NAME)} : {Vtiger_Util_Helper::formatDateDiffInStrings($SYNCTIME)}</small></p>
            {else}
                <p class="muted" id='synctime'><small>{vtranslate('LBL_NOT_SYNCRONIZED',$MODULE_NAME)}</small></p>    
            {/if}   
       {/if}
        <div class='row-fluid'>
            <span class='span0'>&nbsp;</span>
            <button id="sync_button" class="btn btn-success span9"  data-url='index.php?module=Google&view=List&operation=sync&sourcemodule={$SOURCEMODULE}'><b>{vtranslate('LBL_SYNC_BUTTON',$MODULE_NAME)}</b></button>
            {if $SOURCEMODULE == 'Calendar'} 
                <span class="span0"> 
                    <i class="icon-question-sign pushDown" id="popid"  data-placement="right" rel="popover" ></i> 
                </span> 
            {/if} 
            {if $SOURCEMODULE == 'Contacts'} 
                <span class="span0"> 
                    <a id="syncSetting" data-sourcemodule="{$SOURCEMODULE}" title="{vtranslate('SYNC_SETTINGS',$MODULE_NAME)}"><i class="icon-cog pushDown" style="margin-top:15px !important;"></i></a> 
                </span> 
            {/if} 
        </div>
        <br />
        <div class='row-fluid {if !$FIRSTTIME}hide {/if}' id="removeSyncBlock">
            <span class='span0'>&nbsp;</span>
            <button id="remove_sync" class="btn btn-danger span9"  data-url='index.php?module=Google&view=List&operation=removeSync&sourcemodule={$SOURCEMODULE}'><b>{vtranslate('LBL_REMOVE_SYNC',$MODULE_NAME)}</b></button>
            <span class="span0">
                <i class="icon-question-sign pushDown" id="removePop"  data-placement="right" rel="popover" ></i>
            </span>
        </div>
    </div>
        
</div>

{if $SOURCEMODULE == 'Calendar'} 
    <div id="mappingTable">
        <table  class="table table-condensed table-striped table-bordered "  >
            <thead>
                <tr>
                    <td><b>{vtranslate('APPTITLE',$MODULE_NAME)}</b></td>
                    <td><b>{vtranslate('EXTENTIONNAME',$MODULE_NAME)}</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{vtranslate('Subject',$SOURCEMODULE)}</td>
                    <td>{vtranslate('Event Title',$MODULE_NAME)}</td>
                </tr>
                <tr>
                    <td>{vtranslate('Start Date & Time',$SOURCEMODULE)}</td>
                    <td>{vtranslate('From Date',$MODULE_NAME)}</td>
                </tr>
                <tr>
                    <td>{vtranslate('End Date & Time',$SOURCEMODULE)}</td>
                    <td>{vtranslate('Until Date',$MODULE_NAME)}</td>
                </tr>
                <tr>
                    <td>{vtranslate('Description',$SOURCEMODULE)}</td>
                    <td>{vtranslate('Description',$MODULE_NAME)}</td>
                </tr>
            </tbody>
        </table>
    </div>
{/if}

{if $STATE eq 'CLOSEWINDOW'}
    {if $DENY}
        <script>
            window.close();
        </script>
    {else}
    <script>
        window.opener.sync();
        window.close();
    </script>
    {/if}
{/if}

