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
<script type="text/javascript">
function openPopup(del_roleid) {ldelim}
	window.open("index.php?module=Users&action=UsersAjax&file=RolePopup&maskid="+del_roleid+"&parenttab=Settings","roles_popup_window","height=425,width=640,toolbar=no,menubar=no,dependent=yes,resizable =no");
{rdelim}
</script>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td valign="top" width="100%">
				<div align=center>
					<br>
						{include file='SetMenu.tpl'}

						{literal}
						<form name="newProfileForm" action="index.php" onsubmit="if(roleDeleteValidate()) { VtigerJS_DialogBox.block();} else { return false; }">
						{/literal}
							<input type="hidden" name="module" value="Users">
							<input type="hidden" name="action" value="DeleteRole">
							<input type="hidden" name="delete_role_id" value="{$ROLEID}">

							<!-- Delete Role HEADER -->
							<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz" style="background-color: #f7f9fb;">
								<tr class="slds-text-title--caps">
									<td style="padding: 0;">
										<div class="forceRelatedListSingleContainer">
											<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
												<div class="slds-card__header slds-grid">
													<!-- Delete Role Title -->
													<header class="slds-media slds-media--center slds-has-flexi-truncate">
														<div class="slds-media__body">
															<h2>
																<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																	<strong>{$CMOD.LBL_DELETE_ROLE}</strong>
																</span>
															</h2>
														</div>
													</header>
													<!-- Go back link -->
													<div class="slds-no-flex">
														<div class="actionsContainer">
															<a href="#" onClick="window.history.back();">{$APP.LBL_BACK}</a>
														</div>
													</div>
												</div>
											</article>
										</div>
									</td>
								</tr>
							</table>

							<!-- Delete role and transfer section -->
							<table class="slds-table slds-no-row-hover slds-table--cell-buffer detailview_table">
								<!-- Delete and transfer labels -->
								<tr class="slds-line-height--reset">
									<td class="dvtCellLabel" width="30%"><b>{$CMOD.LBL_ROLE_TO_BE_DELETED}:</b></td>
									<td class="dvtCellInfo" width="70%"><b>{$ROLENAME}</b></td>
								</tr>

								<!-- Role and role to transfer -->
								<tr class="slds-line-height--reset">
									<td class="dvtCellLabel" width="30%"><b>{$CMOD.LBL_TRANSFER_USER_ROLE}:</b></td>
									<td class="dvtCellInfo" width="70%">
										<input type="text" name="role_name"  id="role_name" value="" class="slds-input" readonly="readonly">
										&nbsp;{$ROLEPOPUPBUTTON}<input type="hidden" name="user_role" id="user_role" value="">
									</td>
								</tr>

								<!-- Save button for deleting or transfering role -->
								<tr class="slds-line-height--reset">
									<td colspan="2" align="right">
										<input type="submit" name="Delete" value="{$APP.LBL_SAVE_BUTTON_LABEL}" class="slds-button slds-button--small slds-button_success">
									</td>
								</tr>
							</table>
						</form>

					</td></tr></table><!-- close table from setMenu -->
					</td></tr></table><!-- close table from setMenu -->

			</div><!-- close align center -->
		</td>
	</tr>
</table>

<br>
<script>
{literal}
function roleDeleteValidate() {
	if (document.getElementById('role_name').value == '') {
{/literal}
		alert('{$APP.SPECIFY_ROLE_INFO}');
		return false;
{literal}
	}
	return true;
}
{/literal}
</script>
