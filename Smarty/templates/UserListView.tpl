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

<script type="text/javascript" src="include/js/smoothscroll.js"></script>

<br/>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td valign="top" width="100%">
				<form action="index.php" method="post" name="EditView" id="form" onsubmit="VtigerJS_DialogBox.block();">
					<input type='hidden' name='module' value='Users'>
					<input type='hidden' name='action' value='EditView'>
					<input type='hidden' name='return_action' value='ListView'>
					<input type='hidden' name='return_module' value='Users'>
					<input type='hidden' name='parenttab' value='Settings'>

					<div align=center>
						{include file='SetMenu.tpl'}
								<!-- DISPLAY Users-->
								<!-- Users Header-->
								<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz" style="background-color: #f7f9fb;">
									<tr class="slds-text-title--caps">
										<td style="padding: 0;">
											<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilSettings" style="height: 70px;">
												<div class="slds-grid primaryFieldRow" style="transform: translate3d(0, -8.65823px, 0);">
													<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
														<div class="slds-media slds-no-space" style="transform: scale3d(0.864715, 0.864715, 1) translate3d(4.32911px, 2.16456px, 0);">
															<div class="slds-media__figure slds-icon forceEntityIcon">
																<span class="photoContainer forceSocialPhoto">
																	<div class="small roundedSquare forceEntityIcon">
																		<span class="uiImage">
																			<img src="{'ico-users.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" title="{$MOD.LBL_USERS}"/>
																		</span>
																	</div>
																</span>
															</div>
														</div>
														<div class="slds-media__body">
															<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
																<span class="uiOutputText">
																	<b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > {$MOD.LBL_USERS}</b>
																</span>
																<span class="small">
																	{$MOD.LBL_USER_DESCRIPTION}
																</span>
															</h1>
														</div>
													</div>
												</div>
											</div>
										</td>
									</tr>
								</table>

								<!-- Users List View Content -->
								<table border=0 cellspacing=0 cellpadding=10 width=100% >
									<tr>
										<td>
											<div id="ListViewContents">
												{include file="UserListViewContents.tpl"}
											</div>
										</td>
									</tr>
								</table>

						</td></tr></table><!-- /.close table on setMenu -->
						</td></tr></table><!-- /.close table on setMenu -->
					</div>

				</form>
			</td>
		</tr>
	</tbody>
</table>

<div id="tempdiv" style="display:block;position:absolute;left:350px;top:200px;"></div>
{literal}
<script>
function getListViewEntries_js(module,url)
{
		document.getElementById("status").style.display="inline";
		jQuery.ajax({
				method:"POST",
				url:'index.php?module=Users&action=UsersAjax&file=ListView&ajax=true&'+url
		}).done(function(response) {
				document.getElementById("status").style.display="none";
				document.getElementById("ListViewContents").innerHTML= response;
			}
		);
}

function deleteUser(obj,userid)
{
		document.getElementById("status").style.display="inline";
		jQuery.ajax({
				method:"POST",
				url:'index.php?action=UsersAjax&file=UserDeleteStep1&return_action=ListView&return_module=Users&module=Users&parenttab=Settings&record='+userid
		}).done(function(response) {
				document.getElementById("status").style.display="none";
				document.getElementById("tempdiv").innerHTML= response;
				fnvshobj(obj,"tempdiv");
			}
		);
}

function logoutUser(userid) {
	document.getElementById("status").style.display="inline";
	jQuery.ajax({
		method:"POST",
		url:'index.php?module=Users&action=UsersAjax&file=LogoutUser&logoutuserid='+userid
	}).done(function(response) {
		console.log(userid,response);
		document.getElementById("status").style.display="none";
		alert(response);
	});
}

function transferUser(del_userid)
{
		document.getElementById("status").style.display="inline";
		document.getElementById("DeleteLay").style.display="none";
		var trans_userid=document.getElementById('transfer_user_id').options[document.getElementById('transfer_user_id').options.selectedIndex].value;
		jQuery.ajax({
				method:"POST",
				url:'index.php?module=Users&action=UsersAjax&file=DeleteUser&ajax=true&delete_user_id='+del_userid+'&transfer_user_id='+trans_userid
		}).done(function(response) {
				document.getElementById("status").style.display="none";
				document.getElementById("ListViewContents").innerHTML= response;
			}
		);
}
</script>
{/literal}

