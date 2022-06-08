{*<!--
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
********************************************************************************/
-->*}
{assign var="MODULEICON" value='user_role'}
{assign var="MODULESECTION" value=$MOD.LBL_PROFILES}
{assign var="MODULESECTIONDESC" value=$MOD.LBL_PROFILE_DESCRIPTION}
{include file='SetMenu.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
	<div class="slds-modal__container slds-p-around_none slds-card">
		<!-- DISPLAY -->
		<div class="slds-align-content-center" style="align-self:normal;">
			<form action="index.php" method="post" name="new" id="form" onsubmit="VtigerJS_DialogBox.block();">
				<input type="hidden" name="module" value="Users">
				<input type="hidden" name="mode" value="create">
				<input type="hidden" name="action" value="CreateProfile">
				<table style="border:0;width=100%;" class="slds-table slds-table_cell-buffer slds-table_header-hidden slds-page-header">
					<tr class="slds-line-height_reset slds-page-header">
						<td style="font-size:16px">
							<b>{$MOD.LBL_PROFILES_LIST}</b></div>
						</td>
						<td scope="col" >
							<div class="slds-truncate slds-page-header">
								<div style="float: right">
									<b>{$CMOD.LBL_TOTAL} {$COUNT} {$MOD.LBL_PROFILES}</b>
									&nbsp;&nbsp;&nbsp;
									<button type="submit" class="slds-button slds-button_brand">
										<svg class="slds-button__icon slds-button__icon_right" aria-hidden="true">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use>
										</svg>
										&nbsp;&nbsp;&nbsp;{$CMOD.LBL_NEW_PROFILE}
									</button>
								</div>
							</div>
						</td>
					</tr>
				</table>
				<table border="0" class="slds-table slds-table_cell-buffer slds-table_bordered">
					<thead>
						<tr class="slds-line-height_reset">
							<th scope="col" width="2%">
								<div class="slds-truncate">{$LIST_HEADER.0}</div>
							</th>
							<th scope="col" width="8%">
								<div class="slds-truncate">{$LIST_HEADER.1}</div>
							</th>
							<th scope="col" width="30%">
								<div class="slds-truncate">{$LIST_HEADER.2}</div>
							</th>
							<th scope="col" width="60%">
								<div class="slds-truncate">{$LIST_HEADER.3}</div>
							</th>
						</tr>
					</thead>
					<tbody>
						{foreach name=profilelist item=listvalues from=$LIST_ENTRIES}
						<tr class="slds-truncate">
							<td>
								<div class="slds-truncate">{$smarty.foreach.profilelist.iteration}</div>
							</td>
							<td>
								<div class="slds-truncate">
									<a href="index.php?module=Settings&action=profilePrivileges&return_action=ListProfiles&mode=edit&profileid={$listvalues.profileid}">
										<span class="slds-icon_container slds-icon_container_circle slds-icon-action-edit" title="{$APP.LBL_EDIT}">
											<svg class="slds-icon slds-icon_xx-small" aria-hidden="true">
												<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#edit"></use>
											</svg>
											<span class="slds-assistive-text">{$APP.LBL_EDIT}</span>
										</span>
									</a>
									{if $listvalues.del_permission eq 'yes'}
									&nbsp;
									<a href="javascript:;" onclick="DeleteProfile(this,'{$listvalues.profileid}');">
										<span av="id:workflow_id" class="slds-icon_container slds-icon_container_circle slds-icon-action-delete" title="{$APP.LBL_DELETE_BUTTON}">
											<svg class="slds-icon slds-icon_xx-small" aria-hidden="true">
												<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#delete"></use>
											</svg>
											<span class="slds-assistive-text">{$APP.LBL_DELETE_BUTTON}</span>
										</span>
									</a>
									{/if}
								</div>
							</td>
							<td class="slds-truncate"><a href="index.php?module=Settings&action=profilePrivileges&mode=view&profileid={$listvalues.profileid}"><b>{$listvalues.profilename}</b></a></td>
							<td class="slds-truncate">{$listvalues.description}</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			</form>
		</div>
		<div id="tempdiv" style="display:block;position:absolute;left:350px;top:200px;"></div>
	</div>
</section>
<script>
	function DeleteProfile(obj, profileid) {ldelim}
		document.getElementById('status').style.display='inline';
		jQuery.ajax({ldelim}
			method:'POST',
			url:'index.php?module=Users&action=UsersAjax&file=ProfileDeleteStep1&profileid='+profileid,
		{rdelim}).done(function (response) {ldelim}
			document.getElementById('status').style.display='none';
			document.getElementById('tempdiv').innerHTML=response;
			positionDivToCenter('tempdiv');
		{rdelim});
	{rdelim}
</script>