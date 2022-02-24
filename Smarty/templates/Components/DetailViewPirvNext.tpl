
{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}
<!-- This file is used to display the previous and next part on the detail view-->
{if $privrecord neq ''}
    <button class="slds-button slds-button_icon slds-button_icon-border-filled slds-is-selected" 
        title="{$APP.LNK_LIST_PREVIOUS}"
        value="{$APP.LNK_LIST_PREVIOUS}"
        accessKey="{$APP.LNK_LIST_PREVIOUS}"
        onclick="location.href='index.php?module={$MODULE}&action=DetailView&record={$privrecord}'"
        name="privrecord"
    >
    <svg class="slds-button__icon" aria-hidden="true">
        <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#left"></use>
    </svg>
        <span class="slds-assistive-text">Previous</span>
    </button>
{else}
    <button class="slds-button slds-button_icon slds-button_icon-border-filled slds-is-selected" 
        title="{$APP.LNK_LIST_PREVIOUS}"
        value="{$APP.LNK_LIST_PREVIOUS}"
        disabled=""
    >
    <svg class="slds-button__icon" aria-hidden="true">
        <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#left"></use>
    </svg>
        <span class="slds-assistive-text">Previous</span>
    </button>
{/if}
{if $privrecord neq '' || $nextrecord neq ''}
    <button class="slds-button slds-button_icon slds-button_icon-more" 
    aria-haspopup="true" 
    aria-expanded="false"
    title="More Actions"
    title="{$APP.LBL_JUMP_BTN}" accessKey="{$APP.LBL_JUMP_BTN}" onclick="var obj = this;var lhref = getListOfRecords(obj, '{$MODULE}',{$ID});"
    name="jumpBtnIdBottom" 
    id="jumpBtnIdBottom"
    >
        <svg class="slds-button__icon" aria-hidden="true">
            <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#layout_tile"></use>
        </svg>
        
        <span class="slds-assistive-text">Jump To</span>
    </button>
    
{/if}
{if $nextrecord neq ''}
    <button class="slds-button slds-button_icon slds-button_icon-border-filled"
        aria-pressed="false"
        title="{$APP.LNK_LIST_NEXT}"
        value="{$APP.LNK_LIST_NEXT}"
        accessKey="{$APP.LNK_LIST_NEXT}"
        onclick="location.href='index.php?module={$MODULE}&action=DetailView&record={$nextrecord}'"
        name="nextrecord"
        >
            <svg class="slds-button__icon" aria-hidden="true">
            <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#right"></use>
            </svg>
            <span class="slds-assistive-text">Next</span>
    </button>
{else}
    <button class="slds-button slds-button_icon slds-button_icon-border-filled"
        aria-pressed="false"
        title="{$APP.LNK_LIST_NEXT}"
        value="{$APP.LNK_LIST_NEXT}"
        disabled=""
        >
            <svg class="slds-button__icon" aria-hidden="true">
            <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#right"></use>
            </svg>
            <span class="slds-assistive-text">Next</span>
    </button>
{/if}

    <div class="slds-dropdown-trigger slds-dropdown-trigger_click slds-button_last">
        <button class="slds-button slds-button_icon slds-button_icon-brand"
        aria-haspopup="true" 
        title="Toggle Actions"
        title="{$APP.TOGGLE_ACTIONS}"
        onclick="{literal}

        if (document.getElementById('actioncolumn').style.display=='none') {
        
            document.getElementById('actioncolumn').style.display='table-cell';
            document.getElementById('action-on').style.display='block';
            document.getElementById('action-off').style.display='none';
        }
        else
        {
            document.getElementById('actioncolumn').style.display='none';
            document.getElementById('action-on').style.display='none';
            document.getElementById('action-off').style.display='block';
    
        }
            window.dispatchEvent(new Event('resize'));{/literal}"
        >
        <svg class="slds-button__icon" id="action-on" style="display: block;">
            <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#contract_alt"></use>
        </svg>
        <svg class="slds-button__icon" id="action-off" style="display: none;">
            <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#expand_alt"></use>
        </svg>
        <span class="slds-assistive-text">Toggle Actions</span>
        </button>
    </div>
</div>