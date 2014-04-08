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

<table width="100%" cellpadding=0 cellspacing=0 border=0>

<tr valign="top">
<td width="350px">
	<table border=0 cellspacing=0 cellpadding=5 width="100%" align="" class="small listTable">
		<tr>
			<td class="colHeader small">{$MOD.LBL_MODULE}</td>
			<td class="colHeader small">{$MOD.LBL_VIEW_ALL_RECORD}</td>
		</tr>
		{foreach item=viewall from=$MODULE_VIEWALL}
		<tr onmouseover="this.className='prvPrfHoverOn'" onmouseout="this.className='prvPrfHoverOff'">
			<td class="listTableRow small" width="50%">{$viewall.module|@getTranslatedString}</td>
			<td class="listTableRow">
					{if $viewall.value eq 1}
						{assign var=select_all value='checked'}
						{assign var=select_mine value=''}
					{else}
						{assign var=select_all value=''}
						{assign var=select_mine value='checked'}
					{/if}
					<input type="radio" name="view_{$viewall.module}" {$select_all} value="showall"> {$MOD.YES}
					<input type="radio" name="view_{$viewall.module}" {$select_mine} value="onlymine">{$MOD.NO}				
			</td>
			</tr>
		{/foreach}	
	</table>
</td>

<td>
	<table class="small" width="100%" cellpadding=5 cellspacing=0>
	<tr valign="top">
		<td class="small"><b>{$MOD.SELECT_USERS}</b></td>
			<td width="70%" >
				<select name="userid" class="small">
					{foreach item=user from=$USERS}
						{if $USERID eq $user.id}
							<option value="{$user.id}" selected>{$user.name}</option>
						{else}
							<option value="{$user.id}">{$user.name}</option>
						{/if}
					{/foreach}		
				</select>	
			</td>
		</tr>
		<tr>
			<td class="small" colspan=2 width="100%" align="left">
				{$MOD.LBL_USER_DESCRIPTION}
			</td>
		</tr>
	</table>
</td>
</tr>

<tr>
	<td colspan=2 align=center>
		<input class="crmbutton small save" type="Submit" title="{$APP.LBL_SAVE_BUTTON_TITLE}" value="Save" alt="Save" onclick=VtigerJS_DialogBox.block();>
	</td>
</tr>

</table>
