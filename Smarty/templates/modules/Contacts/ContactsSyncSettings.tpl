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
{strip}
<div class="content googleSettings" style="min-width: 800px;">
    <table border=0 cellspacing=0 cellpadding=5 width=100% class=layerHeadingULine>
            <tr>
                    <td width="90%" align="left" class="genHeaderSmall">{$MOD.SYNC_SETTINGS}&nbsp;</td>
                    <td width="10%" align="right">
                            <a href="javascript:fninvsh('GoogleContactsSettings');"><img title="{$APP.LBL_CLOSE}" alt="{$APP.LBL_CLOSE}" src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" /></a>
                    </td>
            </tr>
    </table>
    <form class="form-horizontal" name="contactsyncsettings" id="contactsyncsettings" onsubmit="VtigerJS_DialogBox.block();" method="POST">
        <input type="hidden" name="module" value="{$MODULENAME}" />
        <input type="hidden" name="action" value="{$MODULENAME}Ajax" />
        <input type="hidden" name="file" value="GSaveSyncSettings" />
        <input type="hidden" name="sourcemodule" value="{$SOURCE_MODULE}" />
        <input id="user_field_mapping" type="hidden" name="fieldmapping" value="fieldmappings" />
        <input id="google_fields" type="hidden" value='{$GOOGLE_FIELDS|@json_encode}' />
        <div class="modal-body">
            <div class="sync-settings" style="display: flex;flex-direction: row;">
            <div class="row-fluid">
                <div class="control-group">
                    <label class="control-label">{$MOD.LBL_SELECT_GOOGLE_GROUP_TO_SYNC}&nbsp;</label>
                    <div class="controls">
                        <select class="select2 stretched" name="google_group" style="width:250px;">
                            <option value="all">{$MOD.LBL_ALL}</option>
                            {assign var=IS_GROUP_DELETED value=1}
                            {if !empty($GOOGLE_GROUPS['entry'])}
                            {foreach item=ENTRY from=$GOOGLE_GROUPS['entry']}
                                <option value="{$ENTRY['id']}" {if $ENTRY['id'] eq $SELECTED_GROUP} {assign var=IS_GROUP_DELETED value=0} selected {/if}>{$ENTRY['title']}</option>
                            {/foreach}
                            {/if}
                            {if $IS_GROUP_DELETED && $SELECTED_GROUP != 'all'}
                                {if $SELECTED_GROUP != ''}<option value="none" selected>{$MOD.LBL_NONE}</option>{/if}
                            {/if}
                        </select>
                    </div>
                </div>
            </div>
            <div class="pull-right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
            {*<div class="btn-group pull-right">
                <button id="googlesync_addcustommapping" class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                    <span class="caret"></span>&nbsp;{$MOD.LBL_ADD_CUSTOM_FIELD_MAPPING}
                </button>
                <ul class="dropdown-menu dropdown-menu-left" role="menu">
                    <li class="addCustomFieldMapping" data-type="email" data-vtigerfields='{$VTIGER_EMAIL_FIELDS|@json_encode}'><a>{$MOD.LBL_EMAIL}</a></li>
                    <li class="addCustomFieldMapping" data-type="phone" data-vtigerfields='{$VTIGER_PHONE_FIELDS|@json_encode}'><a>{$MOD.LBL_PHONE}</a></li>
                    <li class="addCustomFieldMapping" data-type="url" data-vtigerfields='{$VTIGER_URL_FIELDS|@json_encode}'><a>{$MOD.LBL_URL}</a></li>
                    <li class="divider"></li>
                    <li class="addCustomFieldMapping" data-type="custom" data-vtigerfields='{$VTIGER_OTHER_FIELDS|@json_encode}'><a>{$MOD.LBL_CUSTOM}</a></li>
                </ul>
            </div>*}
            <div class="row-fluid">
                <div class="control-group">
                    <label class="control-label">{$MOD.LBL_SELECT_SYNC_DIRECTION}</label>
                    <div class="controls">
                        <select class="select2 stretched" name="sync_direction" style="width:250px;">
                            <option value="11" {if $SYNC_DIRECTION eq '11'}selected{/if}>{$MOD.LBL_BI_DIRECTIONAL_SYNC}</option>
                            <option value="10" {if $SYNC_DIRECTION eq '10'}selected{/if}>{$MOD.LBL_ONLY_SYNC_FROM_GOOGLE_TO_VTIGER}</option>
                            <option value="01" {if $SYNC_DIRECTION eq '01'}selected{/if}>{$MOD.LBL_ONLY_SYNC_FROM_VTIGER_TO_GOOGLE}</option>
                        </select>
                    </div>
                </div>
            </div>
            </div>
            <div id="googlesyncfieldmapping" style="margin:15px;">
                <table  class="table table-bordered" cellpadding="10">
                    <thead>
                        <tr align="center">
                            <td><b>{$coreBOS_uiapp_name}</b></td>
                            <td><b>{$MOD.EXTENTIONNAME}</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            {assign var=FLDNAME value="salutationtype"}
                            <td>
                                {$MOD.Salutation}
                                <input type="hidden" class="vtiger_field_name" value="{$FLDNAME}" />
                            </td>
                            <td>
                                {$MOD.Name_Prefix}
                                <input type="hidden" class="google_field_name" value="{$GOOGLE_FIELDS[$FLDNAME]['name']}" />
                            </td>
                        </tr>
                        <tr>
                            {assign var=FLDNAME value="firstname"}
                            <td>
                                {$MOD.First_Name}
                                <input type="hidden" class="vtiger_field_name" value="{$FLDNAME}" />
                            </td>
                            <td>
                                {$MOD.First_Name}
                                <input type="hidden" class="google_field_name" value="{$GOOGLE_FIELDS[$FLDNAME]['name']}" />
                            </td>
                        </tr>
                        <tr>
                            {assign var=FLDNAME value="lastname"}
                            <td>
                                {$MOD.Last_Name}
                                <input type="hidden" class="vtiger_field_name" value="{$FLDNAME}" />
                            </td>
                            <td>
                                {$MOD.Last_Name}
                                <input type="hidden" class="google_field_name" value="{$GOOGLE_FIELDS[$FLDNAME]['name']}" />
                            </td>
                        </tr>
                        <tr>
                            {assign var=FLDNAME value="title"}
                            <td>
                                {$MOD.Title}
                                <input type="hidden" class="vtiger_field_name" value="{$FLDNAME}" />
                            </td>
                            <td>
                                {$MOD.Job_Title}
                                <input type="hidden" class="google_field_name" value="{$GOOGLE_FIELDS[$FLDNAME]['name']}" />
                            </td>
                        </tr>
                        <tr>
                            {assign var=FLDNAME value="account_id"}
                            <td>
                                {$MOD.Organization_Name}
                                <input type="hidden" class="vtiger_field_name" value="{$FLDNAME}" />
                            </td>
                            <td>
                                {$MOD.Company}
                                <input type="hidden" class="google_field_name" value="{$GOOGLE_FIELDS['organizationname']['name']}" />
                            </td>
                        </tr>
                        <tr>
                            {assign var=FLDNAME value="birthday"}
                            <td>
                                {$MOD.Date_of_Birth}
                                <input type="hidden" class="vtiger_field_name" value="{$FLDNAME}" />
                            </td>
                            <td>
                                {$MOD.Birthday}
                                <input type="hidden" class="google_field_name" value="{$GOOGLE_FIELDS[$FLDNAME]['name']}" />
                            </td>
                        </tr>
                        <tr>
                            {assign var=FLDNAME value="email"}
                            <td>
                                {$MOD.Email}
                                <input type="hidden" class="vtiger_field_name" value="{$FLDNAME}" />
                            </td>
                            <td>
                                <input type="hidden" class="google_field_name" value="{$GOOGLE_FIELDS['email']['name']}" />
                                {assign var="GOOGLE_TYPES" value=$GOOGLE_FIELDS[$FLDNAME]['types']}
                                <select class="select2 google-type" style="width:200px;" data-category="email" onclick="display_custom('email');">
                                    {foreach item=TYPE from=$GOOGLE_TYPES}
                                        <option value="{$TYPE}" {if $FIELD_MAPPING[{$FLDNAME}]['google_field_type'] eq $TYPE}selected{/if}>{$MOD.Email} ({$MOD.$TYPE})</option>
                                    {/foreach}
                                </select>&nbsp;&nbsp;
                                <input type="text" class="google-custom-label" style="visibility:{if $FIELD_MAPPING[$FLDNAME]['google_field_type'] neq 'custom'}hidden{else}visible{/if};width:190px;"
                                       value="{if $FIELD_MAPPING[$FLDNAME]['google_field_type'] eq 'custom'}{$FIELD_MAPPING[$FLDNAME]['google_custom_label']}{/if}"
                                       data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
                            </td>
                        </tr>
                        <tr>
                            {assign var=FLDNAME value="secondaryemail"}
                            <td>
                                {$MOD.Secondary_Email}
                                <input type="hidden" class="vtiger_field_name" value="{$FLDNAME}" />
                            </td>
                            <td>
                                <input type="hidden" class="google_field_name" value="{$GOOGLE_FIELDS['email']['name']}" />
                                {assign var=GOOGLE_TYPES value=$GOOGLE_FIELDS['email']['types']}
                                <select class="select2 google-type" style="width:200px;" data-category="email">
                                    {foreach item=TYPE from=$GOOGLE_TYPES}
                                        <option value="{$TYPE}" {if $FIELD_MAPPING['secondaryemail']['google_field_type'] eq $TYPE}selected{/if}>{$MOD.Email} ({$MOD.$TYPE})</option>
                                    {/foreach}
                                </select>&nbsp;&nbsp;
                                <input type="text" class="google-custom-label" style="visibility:{if $FIELD_MAPPING[$FLDNAME]['google_field_type'] neq 'custom'}hidden{else}visible{/if};width:190px;"
                                       value="{if $FIELD_MAPPING[$FLDNAME]['google_field_type'] eq 'custom'}{$FIELD_MAPPING[$FLDNAME]['google_custom_label']}{/if}"
                                       data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                            </td>
                        </tr>
                        <tr>
                            {assign var=FLDNAME value="mobile"}
                            <td>
                                {$MOD.Mobile_Phone}
                                <input type="hidden" class="vtiger_field_name" value="{$FLDNAME}" />
                            </td>
                            <td>
                                <input type="hidden" class="google_field_name" value="{$GOOGLE_FIELDS['phone']['name']}" />
                                {assign var=GOOGLE_TYPES value=$GOOGLE_FIELDS['phone']['types']}
                                <select class="select2 stretched google-type" style="width:200px;" data-category="phone">
                                    {foreach item=TYPE from=$GOOGLE_TYPES}
                                        <option value="{$TYPE}" {if $FIELD_MAPPING[$FLDNAME]['google_field_type'] eq $TYPE}selected{/if}>{$MOD.Phone} ({$MOD.$TYPE})</option>
                                    {/foreach}
                                </select>&nbsp;&nbsp;
                                <input type="text" class="google-custom-label" style="visibility:{if $FIELD_MAPPING[$FLDNAME]['google_field_type'] neq 'custom'}hidden{else}visible{/if};width:190px;"
                                       value="{if $FIELD_MAPPING[$FLDNAME]['google_field_type'] eq 'custom'}{$FIELD_MAPPING[$FLDNAME]['google_custom_label']}{/if}"
                                       data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                            </td>
                        </tr>
                        <tr>
                            {assign var=FLDNAME value="phone"}
                            <td>
                                {$MOD.Office_Phone}
                                <input type="hidden" class="vtiger_field_name" value="{$FLDNAME}" />
                            </td>
                            <td>
                                <input type="hidden" class="google_field_name" value="{$GOOGLE_FIELDS['phone']['name']}" />
                                {assign var=GOOGLE_TYPES value=$GOOGLE_FIELDS['phone']['types']}
                                <select class="select2 stretched google-type" style="width:200px;" data-category="phone">
                                    {foreach item=TYPE from=$GOOGLE_TYPES}
                                        <option value="{$TYPE}" {if $FIELD_MAPPING[$FLDNAME]['google_field_type'] eq $TYPE}selected{/if}>{$MOD.Phone} ({$MOD.$TYPE})</option>
                                    {/foreach}
                                </select>&nbsp;&nbsp;
                                <input type="text" class="google-custom-label" style="visibility:{if $FIELD_MAPPING[$FLDNAME]['google_field_type'] neq 'custom'}hidden{else}visible{/if};width:190px;"
                                       value="{if $FIELD_MAPPING[$FLDNAME]['google_field_type'] eq 'custom'}{$FIELD_MAPPING[$FLDNAME]['google_custom_label']}{/if}"
                                       data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                            </td>
                        </tr>
                        <tr>
                            {assign var=FLDNAME value="homephone"}
                            <td>
                                {$MOD.Home_Phone}
                                <input type="hidden" class="vtiger_field_name" value="{$FLDNAME}" />
                            </td>
                            <td>
                                <input type="hidden" class="google_field_name" value="{$GOOGLE_FIELDS['phone']['name']}" />
                                {assign var=GOOGLE_TYPES value=$GOOGLE_FIELDS['phone']['types']}
                                <select class="select2 stretched google-type" style="width:200px;" data-category="phone">
                                    {foreach item=TYPE from=$GOOGLE_TYPES}
                                        <option value="{$TYPE}" {if $FIELD_MAPPING[$FLDNAME]['google_field_type'] eq $TYPE}selected{/if}>{$MOD.Phone} ({$MOD.$TYPE})</option>
                                    {/foreach}
                                </select>&nbsp;&nbsp;
                                <input type="text" class="google-custom-label" style="visibility:{if $FIELD_MAPPING[$FLDNAME]['google_field_type'] neq 'custom'}hidden{else}visible{/if};width:190px;"
                                       value="{if $FIELD_MAPPING[$FLDNAME]['google_field_type'] eq 'custom'}{$FIELD_MAPPING[$FLDNAME]['google_custom_label']}{/if}"
                                       data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                            </td>
                        </tr>
                        <tr>
                            {assign var=FLDNAME value="mailingaddress"}
                            <td>
                                {$MOD.Mailing_Address}
                                <input type="hidden" class="vtiger_field_name" value="{$FLDNAME}">
                            </td>
                            <td>
                                <input type="hidden" class="google_field_name" value="{$GOOGLE_FIELDS['address']['name']}" />
                                {assign var=GOOGLE_TYPES value=$GOOGLE_FIELDS['address']['types']}
                                <select class="select2 stretched google-type" style="width:200px;" data-category="address">
                                    {foreach item=TYPE from=$GOOGLE_TYPES}
                                        <option value="{$TYPE}" {if $FIELD_MAPPING[$FLDNAME]['google_field_type'] eq $TYPE}selected{/if}>{$MOD.Mailing_Address} ({$MOD.$TYPE})</option>
                                    {/foreach}
                                </select>&nbsp;&nbsp;
                                <input type="text" class="google-custom-label" style="visibility:{if $FIELD_MAPPING[$FLDNAME]['google_field_type'] neq 'custom'}hidden{else}visible{/if};width:190px;"
                                       value="{if $FIELD_MAPPING[$FLDNAME]['google_field_type'] eq 'custom'}{$FIELD_MAPPING[$FLDNAME]['google_custom_label']}{/if}"
                                       data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                            </td>
                        </tr>
                        <tr>
                            {assign var=FLDNAME value="otheraddress"}
                            <td>
                                {$MOD.Other_Address}
                                <input type="hidden" class="vtiger_field_name" value="{$FLDNAME}">
                            </td>
                            <td>
                                <input type="hidden" class="google_field_name" value="{$GOOGLE_FIELDS['address']['name']}" />
                                {assign var=GOOGLE_TYPES value=$GOOGLE_FIELDS['address']['types']}
                                <select class="select2 stretched google-type" style="width:200px;" data-category="address">
                                    {foreach item=TYPE from=$GOOGLE_TYPES}
                                        <option value="{$TYPE}" {if $FIELD_MAPPING[$FLDNAME]['google_field_type'] eq $TYPE}selected{/if}>{$MOD.Other_Address} ({$MOD.$TYPE})</option>
                                    {/foreach}
                                </select>&nbsp;&nbsp;
                                <input type="text" class="google-custom-label" style="visibility:{if $FIELD_MAPPING[$FLDNAME]['google_field_type'] neq 'custom'}hidden{else}visible{/if};width:190px;"
                                       value="{if $FIELD_MAPPING[$FLDNAME]['google_field_type'] eq 'custom'}{$FIELD_MAPPING[$FLDNAME]['google_custom_label']}{/if}"
                                       data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                            </td>
                        </tr>
                        <tr>
                            {assign var=FLDNAME value="description"}
                            <td>
                                {$MOD.Description}
                                <input type="hidden" class="vtiger_field_name" value="{$FLDNAME}">
                            </td>
                            <td>
                                {$MOD.Note}
                                <input type="hidden" class="google_field_name" value="{$GOOGLE_FIELDS[$FLDNAME]['name']}" />
                            </td>
                        </tr>
                        {foreach key=VTIGER_FIELD_NAME item=CUSTOM_FIELD_MAP from=$CUSTOM_FIELD_MAPPING}
                            <tr>
                                <td>
                                    {if $CUSTOM_FIELD_MAP['google_field_name'] eq 'gd:email'}
                                        <select class="select2 stretched vtiger_field_name" style="width:200px;" data-category="email">
                                            {foreach key=EMAIL_FIELD_NAME item=EMAIL_FIELD_LABEL from=$VTIGER_EMAIL_FIELDS}
                                                <option value="{$EMAIL_FIELD_NAME}" {if $VTIGER_FIELD_NAME eq $EMAIL_FIELD_NAME}selected{/if}>{$MOD.$EMAIL_FIELD_LABEL}</option>
                                            {/foreach}
                                        </select>
                                    {else if $CUSTOM_FIELD_MAP['google_field_name'] eq 'gd:phoneNumber'}
                                        <select class="select2 stretched vtiger_field_name" style="width:200px;" data-category="phone">
                                            {foreach key=PHONE_FIELD_NAME item=PHONE_FIELD_LABEL from=$VTIGER_PHONE_FIELDS}
                                                <option value="{$PHONE_FIELD_NAME}" {if $VTIGER_FIELD_NAME eq $PHONE_FIELD_NAME}selected{/if}>{$MOD.$PHONE_FIELD_LABEL}</option>
                                            {/foreach}
                                        </select>
                                    {else if $CUSTOM_FIELD_MAP['google_field_name'] eq 'gContact:userDefinedField'}
                                        <select class="select2 stretched vtiger_field_name" style="width:200px;" data-category="custom">
                                            {foreach key=OTHER_FIELD_NAME item=OTHER_FIELD_LABEL from=$VTIGER_OTHER_FIELDS}
                                                <option value="{$OTHER_FIELD_NAME}" {if $VTIGER_FIELD_NAME eq $OTHER_FIELD_NAME}selected{/if}>{$MOD.$OTHER_FIELD_LABEL}</option>
                                            {/foreach}
                                        </select>
                                    {else if $CUSTOM_FIELD_MAP['google_field_name'] eq 'gContact:website'}
                                        <select class="select2 stretched vtiger_field_name" style="width:200px;" data-category="url">
                                            {foreach key=URL_FIELD_NAME item=URL_FIELD_LABEL from=$VTIGER_URL_FIELDS}
                                                <option value="{$URL_FIELD_NAME}" {if $VTIGER_FIELD_NAME eq $URL_FIELD_NAME}selected{/if}>{$MOD.$URL_FIELD_LABEL}</option>
                                            {/foreach}
                                        </select>
                                    {/if}
                                </td>
                                <td>
                                    <input type="hidden" class="google_field_name" value="{$CUSTOM_FIELD_MAP['google_field_name']}" />
                                    {if $CUSTOM_FIELD_MAP['google_field_name'] eq 'gd:email'}
                                        {assign var=GOOGLE_TYPES value=$GOOGLE_FIELDS['email']['types']}
                                        <select class="select2 google-type" style="width:200px;" data-category="email">
                                            {foreach item=TYPE from=$GOOGLE_TYPES}
                                                <option value="{$TYPE}" {if $CUSTOM_FIELD_MAP['google_field_type'] eq $TYPE}selected{/if}>{$MOD.Email} ({$MOD.$TYPE})</option>
                                            {/foreach}
                                        </select>&nbsp;&nbsp;
                                        <input type="text" class="google-custom-label" style="visibility:{if $CUSTOM_FIELD_MAP['google_field_type'] neq 'custom'}hidden{else}visible{/if};width:190px;"
                                               value="{if $CUSTOM_FIELD_MAP['google_field_type'] eq 'custom'}{$CUSTOM_FIELD_MAP['google_custom_label']}{/if}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                                    {else if $CUSTOM_FIELD_MAP['google_field_name'] eq 'gd:phoneNumber'}
                                        {assign var=GOOGLE_TYPES value=$GOOGLE_FIELDS['phone']['types']}
                                        <select class="select2 google-type" style="width:200px;" data-category="phone">
                                            {foreach item=TYPE from=$GOOGLE_TYPES}
                                                <option value="{$TYPE}" {if $CUSTOM_FIELD_MAP['google_field_type'] eq $TYPE}selected{/if}>{$MOD.Phone} ({$MOD.$TYPE})</option>
                                            {/foreach}
                                        </select>&nbsp;&nbsp;
                                        <input type="text" class="google-custom-label" style="visibility:{if $CUSTOM_FIELD_MAP['google_field_type'] neq 'custom'}hidden{else}visible{/if};width:190px;"
                                               value="{if $CUSTOM_FIELD_MAP['google_field_type'] eq 'custom'}{$CUSTOM_FIELD_MAP['google_custom_label']}{/if}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                                    {else if $CUSTOM_FIELD_MAP['google_field_name'] eq 'gContact:userDefinedField'}
                                        <input type="hidden" class="google-type" value="{$CUSTOM_FIELD_MAP['google_field_type']}">
                                        <input type="text" class="google-custom-label" value="{$CUSTOM_FIELD_MAP['google_custom_label']}" style="width:190px;" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                                    {else if $CUSTOM_FIELD_MAP['google_field_name'] eq 'gContact:website'}
                                        {assign var=GOOGLE_TYPES value=$GOOGLE_FIELDS['url']['types']}
                                        <select class="select2 google-type" style="width:200px;" data-category="url">
                                            {foreach item=TYPE from=$GOOGLE_TYPES}
                                                <option value="{$TYPE}" {if $CUSTOM_FIELD_MAP['google_field_type'] eq $TYPE}selected{/if}>{$MOD.URL} ({$MOD.$TYPE})</option>
                                            {/foreach}
                                        </select>&nbsp;&nbsp;
                                        <input type="text" class="google-custom-label" style="visibility:{if $CUSTOM_FIELD_MAP['google_field_type'] neq 'custom'}hidden{else}visible{/if};width:190px;"
                                               value="{if $CUSTOM_FIELD_MAP['google_field_type'] eq 'custom'}{$CUSTOM_FIELD_MAP['google_custom_label']}{/if}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                                    {/if}
                                    <a class="deleteCustomMapping pull-right"><i title="Delete" class="icon-trash"></i></a>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
                <br>
                <br><br>
            </div>
        </div>

    <table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
            <tr>
                <td align=center class="small">
                    <input name="save_syncsetting" value=" {$APP.LBL_SAVE_LABEL} "id="save_syncsetting" class="crmbutton small save" onclick="return saveSettings()" type="submit" />
                    <input type="button" name="cancel_syncsetting" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="crmbutton small cancel" onclick="fninvsh('GoogleContactsSettings');" />
                </td>
            </tr>
    </table>
    </form>
</div>
{/strip}