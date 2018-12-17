{*<!--
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}
<script type="text/javascript" src="include/js/ListView.js"></script>
<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
<tr>
	<td class="big"><strong>{$MOD.LBL_USERS_LIST}</strong></td>
	<td class="small" align=right>&nbsp;</td>
</tr>
</table>

<table border=0 cellspacing=0 cellpadding=5 width=100% class="listTableTopButtons">
<tr>
	<td class="small" nowrap align="left">
		{$recordListRange}
	</td>
	<!-- Page Navigation -->
	<td nowrap width="35%" align="center">
		<table border=0 cellspacing=0 cellpadding=0 class="small">
			<tr>{$NAVIGATION}</tr>
		</table>
	</td>
	<td class="big" nowrap align="right">
		<div align="right">
		<input title="{$CMOD.LBL_NEW_USER_BUTTON_TITLE}" accessyKey="{$CMOD.LBL_NEW_USER_BUTTON_KEY}" type="submit" name="button" value="{$CMOD.LBL_NEW_USER_BUTTON_LABEL}" class="crmButton create small">
		<input title="{$CMOD.LBL_EXPORT_USER_BUTTON_TITLE}" accessyKey="{$CMOD.LBL_EXPORT_USER_BUTTON_KEY}" type="button" onclick="return selectedRecords('Users','ptab')" value="{$CMOD.LBL_EXPORT_USER_BUTTON_LABEL}" class="crmButton small cancel">
		</div>
	</td>
</{$APP.LBL_EXPORT}
{if !empty($ERROR_MSG)}
<tr>
	{$ERROR_MSG}
</tr>
{/if}
</table>

<table border=0 cellspacing=0 cellpadding=5 width=100% class="listTable">
<tr>
	<td class="colHeader small cblds-p-v--mediumsmall" valign=top>#</td>
	<td class="colHeader small cblds-p-v--mediumsmall" valign=top>{$APP.Tools}</td>
	<td class="colHeader small cblds-p-v--mediumsmall" valign=top>{$LIST_HEADER.3}</td>
	<td class="colHeader small cblds-p-v--mediumsmall" valign=top>{$LIST_HEADER.5}</td>
	<td class="colHeader small cblds-p-v--mediumsmall" valign=top>{$LIST_HEADER.7}</td>
	<td class="colHeader small cblds-p-v--mediumsmall" valign=top>{$LIST_HEADER.6}</td>
	<td class="colHeader small cblds-p-v--mediumsmall" valign=top>{$LIST_HEADER.4}</td>
</tr>
	{foreach name=userlist item=listvalues key=userid from=$LIST_ENTRIES}
		{assign var=flag value=0}
		{assign var=flag_edit value=0}
<tr>
	<td class="listTableRow small" valign=top>{math equation="x + y" x=$smarty.foreach.userlist.iteration y=$PAGE_START_RECORD}</td>
	<td class="listTableRow small" nowrap valign=top>
	{foreach item=name key=id from=$USERNOEDIT}
		{if $userid eq $id && $userid neq $CURRENT_USERID}
			{assign var=flag_edit value=1}
		{/if}
	{/foreach}
	{if $flag_edit eq 0}
		<a href="index.php?action=EditView&return_action=ListView&return_module=Users&module=Users&parenttab=Settings&record={$userid}"><img src="{'editfield.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_EDIT_BUTTON}" title="{$APP.LBL_EDIT_BUTTON}" border="0"></a>
	{/if}
	{foreach item=name key=id from=$USERNODELETE}
		{if $userid eq $id || $userid eq $CURRENT_USERID}
			{assign var=flag value=1}
		{/if}
	{/foreach}
	{if $flag eq 0 && $listvalues.4|@strip_tags|@trim eq 'Active'}
		<img src="{'delete.gif'|@vtiger_imageurl:$THEME}" onclick="deleteUser(this,'{$userid}')" border="0"  alt="{$APP.LBL_DELETE_BUTTON}" title="{$APP.LBL_DELETE_BUTTON}" style="cursor:pointer;"/>
	{/if}
	{if $listvalues.4|@strip_tags|@trim eq 'Active'}
		<img src="{'logout.png'|@vtiger_imageurl:$THEME}" onclick="logoutUser('{$userid}')" border="0" alt="{$APP.LBL_LOGOUT}" title="{$APP.LBL_LOGOUT}" style="cursor:pointer;width:16px;"/>
	{/if}
	<a href="index.php?action=EditView&return_action=ListView&return_module=Users&module=Users&parenttab=Settings&record={$userid}&isDuplicate=true"><img src="{'settingsActBtnDuplicate.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_DUPLICATE_BUTTON}" title="{$APP.LBL_DUPLICATE_BUTTON}" border="0"></a>
</td>
	<td class="listTableRow small" valign=top><b>
	{if $flag_edit eq 0}
		<a href="index.php?module=Users&action=DetailView&parenttab=Settings&record={$userid}"> {$listvalues.3} </a></b><br><a href="index.php?module=Users&action=DetailView&parenttab=Settings&record={$userid}"> {$listvalues.1} </a> ({$listvalues.2})</td>
	{else}
		{$listvalues.3} </b></td>
	{/if}
	<td class="listTableRow small" valign=top>{$listvalues.5}&nbsp;</td>
	<td class="listTableRow small" valign=top>{$listvalues.7}&nbsp;</td>
	<td class="listTableRow small" valign=top>{$listvalues.6}&nbsp;</td>
	{if $listvalues.4|@strip_tags|@trim eq 'Active'}
	<td class="listTableRow small active" valign=top>{$APP.Active}</td>
	{else}
	<td class="listTableRow small inactive" valign=top>{$APP.Inactive}</td>
	{/if}	

</tr>
	{foreachelse}
	<tr>
		<td colspan="7">
			<table width="100%">
				<tr>
					<td rowspan="2" width="45%" align="right"><img src="{'empty.jpg'|@vtiger_imageurl:$THEME}" height="60" width="61"></td>
					<td nowrap="nowrap" width="65%" align="left">
						<span class="genHeaderSmall">
							{$APP.LBL_NO} {$MOD.LBL_USERS} {$APP.LBL_FOUND} !
						</span>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	{/foreach}
</table>
<table border=0 cellspacing=0 cellpadding=5 width=100% >
	<tr><td class="small cblds-t-align--right" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td></tr>
</table>

