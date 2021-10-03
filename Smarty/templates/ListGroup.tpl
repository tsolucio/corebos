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
{assign var="MODULEICON" value='groups'}
{assign var="MODULESECTION" value=$MOD.LBL_GROUPS}
{assign var="MODULESECTIONDESC" value=$MOD.LBL_GROUP_DESC}
{include file='SetMenu.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
	<div class="slds-modal__container slds-p-around_none slds-card">
		<!-- DISPLAY -->
		<div class="slds-align-content-center" style="align-self:normal;">
			<form action="index.php" method="post" name="new" id="form" onsubmit="VtigerJS_DialogBox.block();">
				<input type="hidden" name="module" value="Settings">
				<input type="hidden" name="action" value="createnewgroup">
				<input type="hidden" name="mode" value="create">
				<table style="border:0;width:100%;" class="slds-table slds-table_cell-buffer slds-table_header-hidden slds-page-header">
					<tr class="slds-line-height_reset slds-page-header">
						<td style="font-size:16px">
							<b>{$MOD.LBL_GROUP_LIST}</b></div>
						</td>
						<td scope="col" >
							<div class="slds-truncate slds-page-header">
								<div style="float: right">
									<b>{$CMOD.LBL_TOTAL} {$GRPCNT} {$CMOD.LBL_GROUPS}</b>
									&nbsp;&nbsp;&nbsp;
									<button type="submit" class="slds-button slds-button_brand">
										<svg class="slds-button__icon slds-button__icon_right" aria-hidden="true">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use>
										</svg>
										&nbsp;&nbsp;&nbsp;{$CMOD.LBL_NEW_GROUP}
									</button>
								</div>
							</div>
						</td>
					</tr>
				</table>
				<table class="slds-table slds-table_cell-buffer slds-table_bordered">
					<thead>
						<tr class="slds-line-height_reset">
							<th scope="col" width="2%">
								<div class="slds-truncate">#</div>
							</th>
							<th scope="col" width="8%">
								<div class="slds-truncate">{$LIST_HEADER.0}</div>
							</th>
							<th scope="col" width="30%">
								<div class="slds-truncate">{$LIST_HEADER.1}</div>
							</th>
							<th scope="col" width="60%">
								<div class="slds-truncate">{$LIST_HEADER.2}</div>
							</th>
						</tr>
					</thead>
					<tbody>
						{foreach name=grouplist item=groupvalues from=$LIST_ENTRIES}
						<tr class="slds-truncate">
							<td>
								<div class="slds-truncate">{$smarty.foreach.grouplist.iteration}</div>
							</td>
							<td>
								<div class="slds-truncate">
									<a href="index.php?module=Settings&action=createnewgroup&returnaction=listgroups&mode=edit&groupId={$groupvalues.groupid}">
										<span class="slds-icon_container slds-icon_container_circle slds-icon-action-edit" title="{$APP.LNK_EDIT}">
											<svg class="slds-icon slds-icon_xx-small" aria-hidden="true">
												<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#edit"></use>
											</svg>
											<span class="slds-assistive-text">{$APP.LNK_EDIT}</span>
										</span>
									</a>
									&nbsp;
									<a href="#" onclick="deletegroup(this,'{$groupvalues.groupid}')">
										<span av="id:workflow_id" class="slds-icon_container slds-icon_container_circle slds-icon-action-delete" title="{$APP.LNK_DELETE}">
											<svg class="slds-icon slds-icon_xx-small" aria-hidden="true">
												<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#delete"></use>
											</svg>
											<span class="slds-assistive-text">{$APP.LNK_DELETE}</span>
										</span>
									</a>
								</div>
							</td>
							<td class="slds-truncate"><a href="index.php?module=Settings&action=GroupDetailView&groupId={$groupvalues.groupid}">{$groupvalues.groupname}</b></a></td>
							<td class="slds-truncate">{$groupvalues.description}</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			</form>
		</div>
	</div>
	<div id="tempdiv" style="display:block;position:absolute;left:350px;top:200px;"></div>
</section>
<script>
	function deletegroup(obj, groupid) {ldelim}
		document.getElementById('status').style.display='inline';
		jQuery.ajax({ldelim}
			method: 'POST',
			url:'index.php?module=Users&action=UsersAjax&file=GroupDeleteStep1&groupid='+groupid,
		{rdelim}).done(function(response) {ldelim}
			document.getElementById('status').style.display='none';
			document.getElementById('tempdiv').innerHTML=response;
			fnvshobj(obj, 'tempdiv');
		{rdelim}
		);
	{rdelim}
</script>