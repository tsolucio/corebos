{*<!--

/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

-->*}

<script type='text/javascript' src='include/js/Mail.js'></script>
{if $SinglePane_View eq 'true'}
	{assign var = return_modname value='DetailView'}
{else}
	{assign var = return_modname value='CallRelatedList'}
{/if}

{foreach key=header item=detail from=$RELATEDLISTS}

{assign var=rel_mod value=$header}
{assign var="HEADERLABEL" value=$header|@getTranslatedString:$rel_mod}

<table border=0 cellspacing=0 cellpadding=0 width=100% class="small" style="border-bottom:1px solid #999999;padding:5px;">
	<tr>
		<td  valign=bottom><b>{$HEADERLABEL}</b> 
			{if $MODULE eq 'Campaigns' && ($rel_mod eq 'Contacts' || $rel_mod eq 'Leads')}
					<br><br>{$APP.LBL_SELECT_BUTTON_LABEL}: <a href="javascript:;" onclick="clear_checked_all('{$rel_mod}');">{$APP.LBL_NONE_NO_LINE}</a>
			{/if} 
		</td>
		{if $detail ne ''}
		<td align=center>{$detail.navigation.0}</td>
			{$detail.navigation.1}
		{/if}
		<td align=right>
			{$detail.CUSTOM_BUTTON}
		</td>

			{if $header eq 'Contacts' && $MODULE neq 'Campaigns' && $MODULE neq 'Accounts' && $MODULE neq 'Potentials' && $MODULE neq 'Products' && $MODULE neq 'Vendors'}
				{if $MODULE eq 'Calendar'}
					<input alt="{$APP.LBL_SELECT_CONTACT_BUTTON_LABEL}" title="{$APP.LBL_SELECT_CONTACT_BUTTON_LABEL}" accessKey="" class="crmbutton small edit" value="{$APP.LBL_SELECT_BUTTON_LABEL} {$APP.Contacts}" LANGUAGE=javascript onclick='return window.open("index.php?module=Contacts&return_module={$MODULE}&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid={$ID}{$search_string}","test","width=640,height=602,resizable=0,scrollbars=0");' type="button"  name="button"></td>
				{elseif $MODULE neq 'Services'}
					<input title="{$APP.LBL_ADD_NEW} {$APP.Contact}" accessyKey="F" class="crmbutton small create" onclick="this.form.action.value='EditView';this.form.module.value='Contacts'" type="submit" name="button" value="{$APP.LBL_ADD_NEW} {$APP.Contact}"></td>
				{/if}
			{elseif $header eq 'Users'}
                    {if $MODULE eq 'Calendar'}
						<input title="Change" accessKey="" tabindex="2" type="button" class="crmbutton small edit" value="{$APP.LBL_SELECT_USER_BUTTON_LABEL}" name="button" LANGUAGE=javascript onclick='return window.open("index.php?module=Users&return_module=Calendar&return_action={$return_modname}&activity_mode=Events&action=Popup&popuptype=detailview&form=EditView&form_submit=true&select=enable&return_id={$ID}&recordid={$ID}","test","width=640,height=525,resizable=0,scrollbars=0")';>
                    {/if}
            {elseif $header eq 'Activity History'}
                    &nbsp;</td>
            {/if}
	</tr>
</table>
{assign var=check_status value=$detail}
{if $detail ne '' && $detail.header neq ''}
	{foreach key=header item=detail from=$detail}
		{if $header eq 'header'}
			<table border=0 cellspacing=1 cellpadding=3 width=100% style="background-color:#eaeaea;" class="small">
				<tr style="height:25px" bgcolor=white>
                                {if $MODULE eq 'Campaigns' && ($rel_mod eq 'Contacts' || $rel_mod eq 'Leads')}
                                        <td class="lvtCol"><input name ="{$rel_mod}_selectall" onclick="rel_toggleSelect(this.checked,'{$rel_mod}_selected_id','{$rel_mod}');"  type="checkbox"></td>
                                {/if}
				{foreach key=header item=headerfields from=$detail}
					<td class="lvtCol">{$headerfields}</td>
				{/foreach}
                                </tr>
		{elseif $header eq 'entries'}
			{foreach key=header item=detail from=$detail}
				<tr bgcolor=white>
                                {if $MODULE eq 'Campaigns' && ($rel_mod eq 'Contacts' || $rel_mod eq 'Leads')}
                                        <td><input name="{$rel_mod}_selected_id" id="{$header}" value="{$header}" onclick="rel_check_object(this,'{$rel_mod}');" toggleselectall(this.name,="" selectall="" )="" type="checkbox"  {$check_status.checked.$header}></td>
                                {/if}
				{foreach key=header item=listfields from=$detail}
									 {* vtlib customization: Trigger events on listview cell *}
	                                 <td onmouseover="vtlib_listview.trigger('cell.onmouseover', $(this))" onmouseout="vtlib_listview.trigger('cell.onmouseout', $(this))">{$listfields}</td>
	                                 {* END *}
				{/foreach}
				</tr>
			{/foreach}
			</table>
		{/if}
	{/foreach}
{else}
	<table style="background-color:#eaeaea;color:#000000" border="0" cellpadding="3" cellspacing="1" width="100%" class="small">
		<tr style="height: 25px;" bgcolor="white">
			<td><i>{$APP.LBL_NONE_INCLUDED}</i></td>
		</tr>
	</table>
{/if}
<br><br>
{ if $MODULE eq 'Campaigns' && ($rel_mod eq 'Contacts' || $rel_mod eq 'Leads')}
<script>
rel_default_togglestate('{$rel_mod}');
</script>
{/if}
{/foreach}
<table border=0 cellspacing=0 cellpadding=0 width=100%>
<tr>
  <td>
    <a href="{$FULL_GANTT_CHART_IMAGE_URL}" border='0' target='_new'><img src="{$THUMB_GANTT_CHART_IMAGE_URL}" border='0'></a>
  </td>
</tr>
</table>

