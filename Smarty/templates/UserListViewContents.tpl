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

<div class="forceRelatedListSingleContainer">
	<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
		<div class="slds-card__header slds-grid">
			<header class="slds-media slds-media--center slds-has-flexi-truncate">
				<div class="slds-media__body">
					<h2>
						<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
							<strong>{$MOD.LBL_USERS_LIST}</strong>
						</span>
					</h2>
				</div>
			</header>
			<div class="slds-no-flex">
				<div class="actionsContainer">
					<input title="{$CMOD.LBL_NEW_USER_BUTTON_TITLE}" accessyKey="{$CMOD.LBL_NEW_USER_BUTTON_KEY}" type="submit" name="button" value="{$CMOD.LBL_NEW_USER_BUTTON_LABEL}" class="slds-button slds-button--small slds-button_success">
				</div>
			</div>
		</div>
	</article>
</div>


<table class="slds-table slds-no-row-hover slds-table--fixed-layout listTableTopButtons">
	<tr class="slds-line-height--reset">
		<td>
			{$recordListRange}
		</td>
		<!-- Page Navigation -->
		{$NAVIGATION}
	</tr>
	{if !empty($ERROR_MSG)}
	<tr class="slds-line-height--reset">
		{$ERROR_MSG}
	</tr>
	{/if}
</table>

<table class="slds-table slds-table--bordered listTable">
	<thead>
		<tr>
			<td role="gridcell" class="slds-text-align--center" style="width: 1.5rem;" >#</td>
			<th class="slds-text-title--caps" scope="col">
				<span class="slds-truncate slds-text-link--reset" style="padding: .5rem 0;">
					{$APP.Tools}
				</span>
			</th>
			<th class="slds-text-title--caps" scope="col">
				<span class="slds-truncate slds-text-link--reset" style="padding: .5rem 0;">
					{$LIST_HEADER.3}
				</span>
			</th>
			<th class="slds-text-title--caps" scope="col">
				<span class="slds-truncate slds-text-link--reset" style="padding: .5rem 0;">
					{$LIST_HEADER.5}
				</span>
			</th>
			<th class="slds-text-title--caps" scope="col">
				<span class="slds-truncate slds-text-link--reset" style="padding: .5rem 0;">
					{$LIST_HEADER.6}
				</span>
			</th>
			<th class="slds-text-title--caps" scope="col">
				<span class="slds-truncate slds-text-link--reset" style="padding: .5rem 0;">
					{$LIST_HEADER.7}
				</span>
			</th>
			<th class="slds-text-title--caps" scope="col">
				<span class="slds-truncate slds-text-link--reset" style="padding: .5rem 0;">
					{$LIST_HEADER.4}
				</span>
			</th>
		</tr>
	</thead>
	<tbody>
		{foreach name=userlist item=listvalues key=userid from=$LIST_ENTRIES}
			{assign var=flag value=0}
			<tr class="slds-hint-parent slds-line-height--reset">
				<td role="gridcell" class="slds-text-align--center">{math equation="x + y" x=$smarty.foreach.userlist.iteration y=$PAGE_START_RECORD}</td>
				<th scope="row">
					<div class="slds-truncate">
						<a href="index.php?action=EditView&return_action=ListView&return_module=Users&module=Users&parenttab=Settings&record={$userid}"><img src="{'editfield.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_EDIT_BUTTON}" title="{$APP.LBL_EDIT_BUTTON}" border="0"></a>
						{foreach item=name key=id from=$USERNODELETE}
							{if $userid eq $id || $userid eq $CURRENT_USERID}
								{assign var=flag value=1}
							{/if}
						{/foreach}
						{if $flag eq 0 && $listvalues.4|@strip_tags|@trim eq 'Active'}
							|<img src="{'delete.gif'|@vtiger_imageurl:$THEME}" onclick="deleteUser(this,'{$userid}')" border="0"  alt="{$APP.LBL_DELETE_BUTTON}" title="{$APP.LBL_DELETE_BUTTON}"/>
						{/if}
						|<a href="index.php?action=EditView&return_action=ListView&return_module=Users&module=Users&parenttab=Settings&record={$userid}&isDuplicate=true"><img src="{'settingsActBtnDuplicate.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_DUPLICATE_BUTTON}" title="{$APP.LBL_DUPLICATE_BUTTON}"></a>
					</div>
				</th>
				<th scope="row">
					<div class="slds-truncate">
						<b><a href="index.php?module=Users&action=DetailView&parenttab=Settings&record={$userid}"> {$listvalues.3} </a></b>
						<br>
						<a href="index.php?module=Users&action=DetailView&parenttab=Settings&record={$userid}"> {$listvalues.1} </a>
						({$listvalues.2})
					</div>
				</th>
				<th scope="row">
					<div class="slds-truncate">
						{$listvalues.5}
					</div>
				</th>
				<th scope="row">
					<div class="slds-truncate">
						{$listvalues.7}
					</div>
				</th>
				<th scope="row">
					<div class="slds-truncate">
						{$listvalues.6}
					</div>
				</th>
				{if $listvalues.4|@strip_tags|@trim eq 'Active'}
				<th scope="row">
					<div class="slds-truncate active">
						{$APP.Active}
					</div>
				</th>
				{else}
				<th scope="row">
					<div class="slds-truncate inactive">
						{$APP.Inactive}
					</div>
				</th>
				{/if}
			</tr>
		{foreachelse}
			<tr>
				<td colspan="7">
					<table class="slds-table slds-no-row-hover empty-table" border="0">
						<tr>
							<td rowspan="2" align="center">
							<img src="{'empty.jpg'|@vtiger_imageurl:$THEME}">
							<span class="genHeaderSmall">
								{$APP.LBL_NO} {$MOD.LBL_USERS} {$APP.LBL_FOUND} !
							</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=100% >
	<tr><td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td></tr>
</table>

