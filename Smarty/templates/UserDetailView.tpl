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
<script type="text/javascript" src="include/js/dtlviewajax.js"></script>
<script type='text/javascript' src="include/js/PasswordManagement.js"></script>
<script type="text/javascript" src="modules/Users/ChangePassword.js"></script>
<span id="crmspanid" style="display:none;position:absolute;"  onmouseover="show('crmspanid');">
	<a class="link"  align="right" href="javascript:;">{$APP.LBL_EDIT_BUTTON}</a>
</span>
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
<div class="slds-modal__container slds-p-around_none">
<!-- Shadow table -->
<table class="user-detailview" style="border:0;padding: 2px 10px;">
<tr>
	<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
	<br>
	<div align=center>
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td class="padTab" align="left">
						<form name="DetailView" method="POST" action="index.php" ENCTYPE="multipart/form-data" id="form" style="margin:0px" onsubmit="VtigerJS_DialogBox.block();">
							<input type="hidden" name="module" value="Users" style="margin:0px">
							<input type="hidden" name="record" id="userid" value="{$ID}" style="margin:0px">
							<input type="hidden" name="isDuplicate" value=false style="margin:0px">
							<input type="hidden" name="action" style="margin:0px">
							<input type="hidden" name="changepassword" style="margin:0px">
							<input type="hidden" name="old_password" style="margin:0px">
							<input type="hidden" name="new_password" style="margin:0px">
							<input type="hidden" name="return_module" value="Users" style="margin:0px">
							<input type="hidden" name="return_action" value="ListView"  style="margin:0px">
							<input type="hidden" name="return_id" style="margin:0px">
							<input type="hidden" name="forumDisplay" style="margin:0px">
							<input type="hidden" name="hour_format" id="hour_format" value="{$HOUR_FORMAT}" style="margin:0px">
							<input type="hidden" name="start_hour" id="start_hour" value="{$START_HOUR}" style="margin:0px">
							<input type="hidden" name="form_token" value="{$FORM_TOKEN}">
							<input type="hidden" id="hdtxt_IsAdmin" value="{if $IS_ADMIN eq 'true'}1{else}0{/if}">
							{include file='applicationmessage.tpl'}
							<table width="100%" border="0" cellpadding="0" cellspacing="0" >
							<tr>
								<td>
									<!-- Heading and Icons -->
									<table width="100%" cellpadding="5" cellspacing="0" border="0" class="settingsSelUITopLine">
									<tr>
										<td width=50 rowspan="2" class="cblds-p_none"><img src="{'ico-users.gif'|@vtiger_imageurl:$THEME}" align="absmiddle"></td>
										<td>
											{if $IS_ADMIN eq 'true'}
											<span class="heading2">
											<b>
											<a href="index.php?module=Settings&action=index">{'LBL_SETTINGS'|@getTranslatedString} </a> &gt;
											<a href="index.php?module=Users&action=index"> {$MOD.LBL_USERS} </a>&gt;"{$USERNAME}"
											</b>
											</span>
											{else}
											<span class="heading2">
											<b>{$APP.LBL_MY_PREFERENCES}</b>
											</span>
											{/if}
											<span id="vtbusy_info" style="display:none;" valign="bottom">
											<div role="status" class="slds-spinner slds-spinner_brand slds-spinner_x-small" style="position:relative; top:6px;">
												<div class="slds-spinner__dot-a"></div>
												<div class="slds-spinner__dot-b"></div>
											</div>
											</span>
										</td>
									</tr>
									<tr>
										<td>{$UMOD.LBL_USERDETAIL_INFO} "{$USERNAME}"</td>
									</tr>
									</table>
								</td>
								<td class="cblds-t-align_right settingsSelUITopLine">
									{include file='UserActionMenu.tpl'}
								</td>
							</tr>
							<tr>
								<td colspan="2" align=left>
								<!-- User detail blocks -->
								<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="user-detailview__table">
								<tr>
								<td align="left" valign="top">
									{foreach key=header name=blockforeach item=detail from=$BLOCKS}
									<br>
									<table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
									<tr>
										{strip}
										 <td class="big">
										<strong>{$smarty.foreach.blockforeach.iteration}. {$header}</strong>
										 </td>
										 <td class="small cblds-t-align_right" align="right">&nbsp;</td>
										{/strip}
									</tr>
									</table>

									<table border="0" cellpadding="5" cellspacing="0" width="100%">
									{foreach item=detailInfo from=$detail}
									<tr >
										{foreach key=label item=data from=$detailInfo}
											{assign var=keyid value=$data.ui}
											{assign var=keyval value=$data.value}
											{assign var=keytblname value=$data.tablename}
											{assign var=keyfldname value=$data.fldname}
											{assign var=keyfldid value=$data.fldid}
											{assign var=keyoptions value=$data.options}
											{assign var=keysecid value=$data.secid}
											{assign var=keyseclink value=$data.link}
											{assign var=keycursymb value=$data.cursymb}
											{assign var=keysalut value=$data.salut}
											{assign var=keycntimage value=$data.cntimage}
											{assign var=keyadmin value=$data.isadmin}

											{if $label ne ''}
											<td class="dvtCellLabel cblds-t-align_right" align=right width=25%>{$label}</td>
												{if $cbodUserBlocked}
													{if $keyfldname eq 'user_password'}
														{assign var=keyval value=''}
													{/if}
													{include file="DetailViewFields.tpl"}
												{else}
													{include file="DetailViewUI.tpl"}
												{/if}
											{else}
												<td class="dvtCellLabel cblds-t-align_right" align=right>&nbsp;</td>
												<td class="dvtCellInfo" align=left >&nbsp;</td>
											{/if}
										{/foreach}
									</tr>
									{/foreach}
									</table>
									{assign var=list_numbering value=$smarty.foreach.blockforeach.iteration}
									{/foreach}

									<br>
									<!-- Home page components -->
									<table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
									<tr>
										 <td class="big">
										<strong>{$list_numbering+1}. {$UMOD.LBL_HOME_PAGE_COMP}</strong>
										 </td>
										 <td class="small cblds-t-align_right" align="right"><img src="{'showDown.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_EXPAND_COLLAPSE}" title="{$APP.LBL_EXPAND_COLLAPSE}" onClick="ShowHidefn('home_comp');"></td>
									</tr>
									</table>

									<div style="float: none; display: none;" id="home_comp">
									<table border="0" cellpadding="5" cellspacing="0" width="100%">
									{foreach item=homeitems key=values from=$HOMEORDER}
										<tr><td class="dvtCellLabel cblds-t-align_right" align="right" width="30%">{$UMOD.$values|@getTranslatedString:'Home'}</td>
											{if $homeitems neq ''}
												<td class="dvtCellInfo cblds-t-align_center" align="center" width="5%">
												<img src="{'prvPrfSelectedTick.gif'|@vtiger_imageurl:$THEME}" alt="{$UMOD.LBL_SHOWN}" title="{$UMOD.LBL_SHOWN}" height="12" width="12"></td><td class="dvtCellInfo" align="left">{$UMOD.LBL_SHOWN}</td>
												{else}
												<td class="dvtCellInfo cblds-t-align_center" align="center" width="5%">
												<img src="{'no.gif'|@vtiger_imageurl:$THEME}" alt="{$UMOD.LBL_HIDDEN}" title="{$UMOD.LBL_HIDDEN}" height="12" width="12"></td><td class="dvtCellInfo" align="left">{$UMOD.LBL_HIDDEN}</td>
											{/if}
										</tr>
									{/foreach}
									</table>
									</div>

									<br>
									<!-- Tag Cloud Display -->
									<table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
									<tr>
										<td class="big">
										<strong>{$list_numbering+2}. {$UMOD.LBL_TAGCLOUD_DISPLAY}</strong>
										</td>
										<td class="small cblds-t-align_right" align="right"><img src="{'showDown.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_EXPAND_COLLAPSE}" title="{$APP.LBL_EXPAND_COLLAPSE}" onClick="ShowHidefn('tagcloud_disp');"></td>
									</tr>
									</table>
									<div style="float: none; display: none;" id="tagcloud_disp">
									<table border="0" cellpadding="5" cellspacing="0" width="100%">
										<tr><td class="dvtCellLabel cblds-t-align_right" align="right" width="30%">{$UMOD.LBL_TAG_CLOUD}</td>
											{if $TAGCLOUDVIEW eq 'true'}
												<td class="dvtCellInfo cblds-t-align_center" align="center" width="5%">
												<img src="{'prvPrfSelectedTick.gif'|@vtiger_imageurl:$THEME}" alt="{$UMOD.LBL_SHOWN}" title="{$UMOD.LBL_SHOWN}" height="12" width="12"></td><td class="dvtCellInfo" align="left">{$UMOD.LBL_SHOWN}</td>
											{else}
												<td class="dvtCellInfo cblds-t-align_center" align="center" width="5%">
												<img src="{'no.gif'|@vtiger_imageurl:$THEME}" alt="{$UMOD.LBL_HIDDEN}" title="{$UMOD.LBL_HIDDEN}" height="12" width="12"></td><td class="dvtCellInfo" align="left">{$UMOD.LBL_HIDDEN}</td>
											{/if}
										</tr>
										<tr><td class="dvtCellLabel cblds-t-align_right" align="right" width="30%">{$MOD.LBL_Show}</td>
											<td class="dvtCellInfo" align="left" colspan=3>{$SHOWTAGAS}</td>
										</tr>
									</table>
									</div>
									<br>
									<!-- My Groups -->
									<table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
									<tr>
										<td class="big">
										<strong>{$list_numbering+3}. {$UMOD.LBL_MY_GROUPS}</strong>
										 </td>
										 <td class="small cblds-t-align_right" align="right">
										{if $GROUP_COUNT > 0}
										<img src="{'showDown.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_EXPAND_COLLAPSE}" title="{$APP.LBL_EXPAND_COLLAPSE}" onClick="fetchGroups_js({$ID});">
										{else}
											&nbsp;
										{/if}
										</td>
									</tr>
									</table>

									<table border="0" cellpadding="5" cellspacing="0" width="100%">
									<tr><td align="left"><div id="user_group_cont" style="display:none;"></div></td></tr>
									</table>
									<br>
								</td>
								</tr>
								</table>
								<!-- User detail blocks ends -->

								</td>
							</tr>
							</table>

						</form>

					</td>
				</tr>
				</table>

	</div>
	</td>
</tr>
</table>

			</td>
			</tr>
			</table>

			</td>
			</tr>
			</table>

<br>
<div id="tempdiv" style="display:block;position:absolute;left:350px;top:200px;"></div>
</div>
</section>
<!-- added for validation -->
<script>
var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
{literal}
function ShowHidefn(divid) {
	if (document.getElementById(divid).style.display != 'none') {
		jQuery('#'+divid).fadeOut();
	} else {
		jQuery('#'+divid).fadeIn();
	}
}

function fetchGroups_js(id) {
	if (document.getElementById('user_group_cont').style.display != 'none') {
		jQuery('#user_group_cont').fadeOut();
	} else {
		fetchUserGroups(id);
	}
}

function fetchUserGroups(id) {
	document.getElementById('status').style.display='inline';
	jQuery.ajax({
		method:'POST',
		url:'index.php?module=Users&action=UsersAjax&file=UserGroups&ajax=true&record='+id
	}).done(function(response) {
		document.getElementById('status').style.display='none';
		document.getElementById('user_group_cont').innerHTML= response;
		jQuery('#user_group_cont').fadeIn();
	});
}

function deleteUser(userid) {
	document.getElementById('status').style.display='inline';
	jQuery.ajax({
		method:'POST',
		url:'index.php?action=UsersAjax&file=UserDeleteStep1&return_action=ListView&return_module=Users&module=Users&record='+userid
	}).done(function(response) {
		document.getElementById('status').style.display='none';
		document.getElementById('tempdiv').innerHTML= response;
	});
}

function transferUser(del_userid) {
	document.getElementById('status').style.display='inline';
	document.getElementById('DeleteLay').style.display='none';
	var trans_userid=document.getElementById('transfer_user_id').options[document.getElementById('transfer_user_id').options.selectedIndex].value;
	window.document.location.href = 'index.php?module=Users&action=DeleteUser&ajax_delete=false&delete_user_id='+del_userid+'&transfer_user_id='+trans_userid;
}
{/literal}
</script>
