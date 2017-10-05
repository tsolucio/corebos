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

<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td class="" valign="top" width="100%">
				<form action="index.php" method="post" name="new" id="form" onsubmit="VtigerJS_DialogBox.block();">
					<input type="hidden" name="module" value="Users">
					<input type="hidden" name="mode" value="create">
					<input type="hidden" name="action" value="CreateProfile">
					<input type="hidden" name="parenttab" value="Settings">
					<div align=center>
						<br>
						{include file='SetMenu.tpl'}

						<!-- DISPLAY Profiles Settings-->
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
																	<img src="{'ico-profile.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_PROFILES}" width="48" height="48" border=0 title="{$MOD.LBL_PROFILES}">
																	</span>
															</div>
														</span>
													</div>
												</div>
												<div class="slds-media__body">
													<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
														<span class="uiOutputText"><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > {$MOD.LBL_PROFILES} </b></span>
														<span class="small">{$MOD.LBL_PROFILE_DESCRIPTION}</span>
													</h1>
												</div>
											</div>
										</div>
									</div>
								</td>
							</tr>
						</table>
						<table border=0 cellspacing=0 cellpadding=10 width=100% >
							<tr>
								<td>
									<div class="forceRelatedListSingleContainer">
										<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
											<div class="slds-card__header slds-grid">
												<header class="slds-media slds-media--center slds-has-flexi-truncate">
													<div class="slds-media__body">
														<h2>
															<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																<b>{$MOD.LBL_PROFILES_LIST}</b>
															</span>
														</h2>
													</div>
												</header>
												<div class="slds-no-flex">
													<div class="actionsContainer">
														&nbsp;<input type="submit" value="{$CMOD.LBL_NEW_PROFILE}" title="{$CMOD.LBL_NEW_PROFILE}" class="slds-button slds-button--small slds-button_success">
													</div>
												</div>
											</div>
											<div class="slds-card__body slds-card__body--inner">
												<div class="commentData">
													{$CMOD.LBL_TOTAL} {$COUNT} {$MOD.LBL_PROFILES}
												</div>
											</div>
										</article>
									</div>
									<br/>
									<table class="slds-table slds-table--bordered slds-table--fixed-layout listTable">
										<thead>
											<tr>
												<td role="gridcell" class="slds-text-align--center" style="width: 1.5rem;" >{$LIST_HEADER.0}</td>
												<th class="slds-text-title--caps" scope="col">
													<span class="slds-truncate" style="padding: .5rem 0;">
														{$LIST_HEADER.1}
													</span>
												</th>
												<th class="slds-text-title--caps" scope="col">
													<span class="slds-truncate" style="padding: .5rem 0;">
														{$LIST_HEADER.2}
													</span>
												</th>
												<th class="slds-text-title--caps" scope="col">
													<span class="slds-truncate" style="padding: .5rem 0;">
														{$LIST_HEADER.3}
													</span>
												</th>
											</tr>
										</thead>
										<tbody>
											{foreach name=profilelist item=listvalues from=$LIST_ENTRIES}
											<tr class="slds-hint-parent slds-line-height--reset" id="row_{$entity_id}">
												<td role="gridcell" class="slds-text-align--center">
													{$smarty.foreach.profilelist.iteration}
												</td>
												<th scope="row">
													<div class="slds-truncate">
														<a href="index.php?module=Settings&action=profilePrivileges&return_action=ListProfiles&parenttab=Settings&mode=edit&profileid={$listvalues.profileid}"><img src="{'editfield.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_EDIT}" title="{$APP.LBL_EDIT}" border="0" align="absmiddle"></a>
														{if $listvalues.del_permission eq 'yes'}
															&nbsp;|&nbsp;
															<a href="javascript:;"><img src="{'delete.gif'|@vtiger_imageurl:$THEME}" border="0" onclick="DeleteProfile(this,'{$listvalues.profileid}')" align="absmiddle" title="{$APP.LBL_DELETE_BUTTON}"></a>
														{/if}
													</div>
												</th>
												<th scope="row">
													<div class="slds-truncate">
														<a href="index.php?module=Settings&action=profilePrivileges&mode=view&parenttab=Settings&profileid={$listvalues.profileid}"><b>{$listvalues.profilename}</b></a>
													</div>
												</th>
												<th scope="row">
													<div class="slds-truncate">
														{$listvalues.description}
													</div>
												</th>
											</tr>
											{/foreach}
										</tbody>
									</table>
									<table border=0 cellspacing=0 cellpadding=5 width=100% >
										<tr><td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td></tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</form>
			</td>
		</tr>
	</tbody>
</table>
<div id="tempdiv" style="display:block;position:absolute;left:350px;top:200px;"></div>
<script>
function DeleteProfile(obj,profileid)
{ldelim}
		document.getElementById("status").style.display="inline";
		jQuery.ajax({ldelim}
				method:"POST",
				url:'index.php?module=Users&action=UsersAjax&file=ProfileDeleteStep1&profileid='+profileid,
		{rdelim}).done(function(response) {ldelim}
					document.getElementById("status").style.display="none";
					document.getElementById("tempdiv").innerHTML=response;
					fnvshobj(obj,"tempdiv");
			{rdelim}
		);
{rdelim}
</script>
