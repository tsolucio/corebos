{************************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{if $FOLDERS}
<table cellpadding=0 cellspacing=0 border=0 class="small" width="98%">
    <tr>
        <td class="dvtSelectedCell" style="padding-left: 5px; padding-bottom: 5px;">{'LBL_Folders'|@getTranslatedString}</td>
    </tr>
    <tr></tr>
    <tr>
        <td class="dvtContentSpace">
        <table cellpadding=2 cellspacing=0 border=0 class="small" width="100%">
        {foreach item=FOLDER from=$FOLDERS}
        <tr>
            <td>
                <a class="mm_folder" id='_mailfolder_{$FOLDER->name()|@htmlentities}' href='#{$FOLDER->name()|@htmlentities}' onclick="MailManager.clearSearchString(); MailManager.folder_open('{$FOLDER->name()|@htmlentities}'); "
                >{if $FOLDER->unreadCount()}<b>{$FOLDER->name()|@htmlentities} ({$FOLDER->unreadCount()})</b>{else}{$FOLDER->name()|@htmlentities}{/if}</a>
            </td>
        </tr>
        {/foreach}
        </table>
        </td>
    </tr>
</table>
{/if}