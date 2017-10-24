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
<script>

function dup_validation()
{ldelim}
	var mode = getObj('mode').value;
	var groupname = document.getElementById('groupName').value;
	var groupid = getObj('groupId').value;
	if(mode == 'edit')
		var reminstr = '&mode='+mode+'&groupName='+groupname+'&groupid='+groupid;
	else
		var reminstr = '&mode=&groupName='+groupname;
	VtigerJS_DialogBox.block();
	//var status = CharValidation(groupname,'namespace');
	//if(status)
	//{ldelim}
	jQuery.ajax({ldelim}
		method:"POST",
		url:'index.php?module=Users&action=UsersAjax&file=SaveGroup&ajax=true&dup_check=true'+reminstr,
	{rdelim}).done(function(response) {ldelim}
		if(response.indexOf('SUCCESS') >-1)
			document.newGroupForm.submit();
		else {ldelim}
			VtigerJS_DialogBox.unblock();
			alert(response);
		{rdelim}
	{rdelim});
	//{rdelim}
	//else
	//	alert(alert_arr.NO_SPECIAL+alert_arr.IN_GROUPNAME)
{rdelim}
var constructedOptionValue;
var constructedOptionName;

var roleIdArr=new Array({$ROLEIDSTR});
var roleNameArr=new Array({$ROLENAMESTR});
var userIdArr=new Array({$USERIDSTR});
var userNameArr=new Array({$USERNAMESTR});
var grpIdArr=new Array({$GROUPIDSTR});
var grpNameArr=new Array({$GROUPNAMESTR});

function showOptions()
{ldelim}
	var selectedOption=document.newGroupForm.memberType.value;
	//Completely clear the select box
	document.forms['newGroupForm'].availList.options.length = 0;

	if(selectedOption == 'groups')
	{ldelim}
		constructSelectOptions('groups',grpIdArr,grpNameArr);
	{rdelim}
	else if(selectedOption == 'roles')
	{ldelim}
		constructSelectOptions('roles',roleIdArr,roleNameArr);
	{rdelim}
	else if(selectedOption == 'rs')
	{ldelim}
		constructSelectOptions('rs',roleIdArr,roleNameArr);
	{rdelim}
	else if(selectedOption == 'users')
	{ldelim}
		constructSelectOptions('users',userIdArr,userNameArr);
	{rdelim}
{rdelim}

function constructSelectOptions(selectedMemberType,idArr,nameArr)
{ldelim}
	var i;
	var findStr=document.newGroupForm.findStr.value;
	if(findStr.replace(/^\s+/g, '').replace(/\s+$/g, '').length !=0)
	{ldelim}
		var k=0;
		for(i=0; i<nameArr.length; i++)
		{ldelim}
			if(nameArr[i].indexOf(findStr) ==0)
			{ldelim}
				constructedOptionName[k]=nameArr[i];
				constructedOptionValue[k]=idArr[i];
				k++;
			{rdelim}
		{rdelim}
	{rdelim}
	else
	{ldelim}
		constructedOptionValue = idArr;
		constructedOptionName = nameArr;
	{rdelim}

	//Constructing the selectoptions
	var j;
	var nowNamePrefix;
	for(j=0;j<constructedOptionName.length;j++)
	{ldelim}
		if(selectedMemberType == 'roles')
		{ldelim}
			nowNamePrefix = 'Roles::'
		{rdelim}
		else if(selectedMemberType == 'rs')
		{ldelim}
			nowNamePrefix = 'RoleAndSubordinates::'
		{rdelim}
		else if(selectedMemberType == 'groups')
		{ldelim}
			nowNamePrefix = 'Group::'
		{rdelim}
		else if(selectedMemberType == 'users')
		{ldelim}
			nowNamePrefix = 'User::'
		{rdelim}

		var nowName = nowNamePrefix + constructedOptionName[j];
		var nowId = selectedMemberType + '::' + constructedOptionValue[j];
		document.forms['newGroupForm'].availList.options[j] = new Option(nowName,nowId);
	{rdelim}
	//clearing the array
	constructedOptionValue = new Array();
	constructedOptionName = new Array();

{rdelim}

function validate()
{ldelim}
	formSelectColumnString();
	if( !emptyCheck( "groupName", "Group Name","text" ) )
		return false;
	//check to restrict the & < > , characters
	var str = document.getElementById("groupName").value;
	var re1=/[&\<\>\,]/
	if (re1.test(str))
	{ldelim}
		alert(alert_arr.SPECIAL_CHARACTERS+" & < > , "+alert_arr.NOT_ALLOWED)
		return false;
	{rdelim}

	if(document.newGroupForm.selectedColumnsString.value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0)
	{ldelim}
		alert('{$APP.GROUP_SHOULDHAVE_ONEMEMBER_INFO}');
		return false;
	{rdelim}
	dup_validation();return false;
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
							<!-- DISPLAY Groups > Create new group -->
								<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz" style="background-color: #f7f9fb;">
									{literal}
									<form name="newGroupForm" action="index.php" method="post" onSubmit="if(validate()) { VtigerJS_DialogBox.block();} else { return false; }">
									{/literal}
										<input type="hidden" name="module" value="Users">
										<input type="hidden" name="action" value="SaveGroup">
										<input type="hidden" name="mode" value="{$MODE}">
										<input type="hidden" name="parenttab" value="Settings">
										<input type="hidden" name="groupId" value="{$GROUPID}">
										<input type="hidden" name="returnaction" value="{$RETURN_ACTION}">

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
																				<img src="{'ico-groups.gif'|@vtiger_imageurl:$THEME}" alt="{$CMOD.LBL_GROUPS}" title="{$CMOD.LBL_GROUPS}">
																			</span>
																		</div>
																	</span>
																</div>
															</div>
															<div class="slds-media__body">
																<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
																	<span class="uiOutputText">
																		<b>
																			{if $MODE eq 'edit'}
																				<a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > <a href="index.php?module=Settings&action=listgroups&parenttab=Settings">{$CMOD.LBL_GROUPS}</a> &gt; {$MOD.LBL_EDIT} &quot;{$GROUPNAME}&quot;
																			{else}
																				<a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > <a href="index.php?module=Settings&action=listgroups&parenttab=Settings">{$CMOD.LBL_GROUPS}</a> &gt; {$CMOD.LBL_CREATE_NEW_GROUP}
																			{/if}
																		</b>
																	</span>
																	<span class="small">
																		{if $MODE eq 'edit'}
																			{$MOD.LBL_EDIT} {$CMOD.LBL_PROPERTIES} &quot;{$GROUPNAME}&quot; {$CMOD.LBL_GROUP}
																		{else}
																			{$CMOD.LBL_NEW_GROUP}
																		{/if}
																	</span>
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
										<td valign=top>

											<div class="forceRelatedListSingleContainer">
												<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
													<div class="slds-card__header slds-grid">
														<header class="slds-media slds-media--center slds-has-flexi-truncate">
															<div class="slds-media__figure">
																<div class="extraSmall forceEntityIcon">
																	<span class="uiImage subheader-image">
																		<img src="{'prvPrfHdrArrow.gif'|@vtiger_imageurl:$THEME}">
																	</span>
																</div>
															</div>
															<div class="slds-media__body">
																<h2>
																	<span class="slds-text-title--caps slds-truncate actionLabel prvPrfBigText">
																		<strong>
																			{if $MODE eq 'edit'}
																				{$CMOD.LBL_PROPERTIES} &quot;{$GROUPNAME}&quot;
																			{else}
																				{$CMOD.LBL_NEW_GROUP}
																			{/if}
																		</strong>
																	</span>
																</h2>
															</div>
														</header>
														<div class="slds-no-flex">
															<div class="actionsContainer">
																{if $MODE eq 'edit'}
																	<input type="submit" class="slds-button slds-button--small slds-button_success" name="add" value="{$APP.LBL_SAVE_BUTTON_LABEL}" onClick="return validate()">
																{else}
																	<input type="submit" class="slds-button slds-button--small slds-button_success" name="add" value="{$CMOD.LBL_ADD_GROUP_BUTTON}" onClick="return validate()">
																{/if}
																&nbsp;
																<input type="button" class="slds-button slds-button--small slds-button--destructive" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onClick="window.history.back()">
															</div>
														</div>
													</div>
														<div class="slds-card__body slds-card__body--inner">
															<div class="commentData">
																<font class="small">{$CMOD.LBL_USE_OPTION_TO_SET_PRIV}</font>
															</div>
														</div>
												</article>
											</div>

											<div class="slds-truncate">
												<table class="slds-table slds-no-row-hover detailview_table">
													<tr class="slds-line-height--reset">
														<td width="15%" class="dvtCellLabel"><font color="red">*</font><strong>{$CMOD.LBL_GROUP_NAME}</strong></td>
														<td width="85%" class="dvtCellInfo" ><input id="groupName" name="groupName" type="text" value="{$GROUPNAME}" class="slds-input"></td>
													</tr>
													<tr class="slds-line-height--reset">
														<td class="dvtCellLabel"><strong>{$CMOD.LBL_DESCRIPTION}</strong></td>
														<td class="dvtCellInfo"><input name="description" type="text" value="{$DESCRIPTION}" class="slds-input"></td>
													</tr>
													<tr class="slds-line-height--reset">
														<td colspan="2" valign=top class="dvtCellLabel text-center big"><strong>{$CMOD.LBL_MEMBER}</strong></td>
													</tr>
													<tr class="slds-line-height--reset">
														<td colspan="2" valign=top>

															<table class="slds-table slds-no-row-hover">
																<tr class="slds-line-height--reset">
																	<td width="45%" valign=top class="dvtCellLabel text-left cellBottomDotLinePlain small"><strong>{$CMOD.LBL_MEMBER_AVLBL}</strong></td>
																	<td width="10%">&nbsp;</td>
																	<td width="45%" class="dvtCellLabel text-left cellBottomDotLinePlain small"><strong>{$CMOD.LBL_MEMBER_SELECTED}</strong></td>
																</tr>
																<tr class="slds-line-height--reset">
																	<td valign=top class="small" style="padding: 0">
																		<table class="slds-table slds-no-row-hover">
																			<tr class="slds-line-height--reset">
																				<td width="25%" class="dvtCellLabel">
																					{$CMOD.LBL_ENTITY}:
																				</td>
																				<td width="75%" class="dvtCellInfo">
																					<select id="memberType" name="memberType" class="slds-select" onchange="showOptions()">
																						<option value="groups" selected>{$CMOD.LBL_GROUPS}</option>
																						<option value="roles">{$CMOD.LBL_ROLES}</option>
																						<option value="rs">{$CMOD.LBL_ROLES_SUBORDINATES}</option>
																						<option value="users">{$MOD.LBL_USERS}</option>
																					</select>
																					<input type="hidden" name="findStr" class="small">
																				</td>
																			</tr>
																		</table>
																	</td>
																	<td colspan="2">&nbsp;</td>
																</tr>
																<tr class="slds-line-height--reset">
																	<td valign=top width="45%">
																		<table class="slds-table slds-no-row-hover">
																			<tr class="slds-line-height--reset">
																				<td class="dvtCellLabel text-left">
																					{$CMOD.LBL_MEMBER} {$CMOD.LBL_OF} {$CMOD.LBL_ENTITY}
																				</td>
																			</tr>
																			<tr class="slds-line-height--reset">
																				<td class="dvtCellInfo">
																					<select id="availList" name="availList" multiple size="5" style="height: 180px" class="small slds-select crmFormList"></select>
																					<input type="hidden" name="selectedColumnsString"/>
																				</td>
																			</tr>
																		</table>
																	</td>
																	<td>
																		<div width="10%" align="center">
																			<input type="button" name="Button" value="&nbsp;&rsaquo;&rsaquo;&nbsp;" onClick="addColumn()" class="slds-button slds-button--small slds-button_success"/><br /><br />
																			<input type="button" name="Button1" value="&nbsp;&lsaquo;&lsaquo;&nbsp;" onClick="delColumn()" class="slds-button slds-button--small slds-button--destructive"/>
																		</div>
																	</td>
																	<td width="45%" class="small" valign=top>
																		<table class="slds-table slds-no-row-hover">
																			<tr class="slds-line-height--reset">
																				<td width="25%" class="dvtCellLabel text-left">
																					{$CMOD.LBL_MEMBER} {$CMOD.LBL_OF} <b>{$GROUPNAME}</b>
																				</td>
																			</tr>
																			<tr class="slds-line-height--reset">
																				<td width="75%" class="dvtCellInfo">
																					<select id="selectedColumns" name="selectedColumns" multiple size="5" style="height: 180px" class="slds-select crmFormList">
																						{foreach item=element from=$MEMBER}
																							<option value="{$element.0}">{$element.1}</option>
																						{/foreach}
																					</select>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
																<tr class="slds-line-height--reset">
																	<td colspan=3 class="dvtCellLabel text-left">
																		<ul class=small>
																			<li>{$CMOD.LBL_GROUP_MESG1}</li>
																			<li>{$CMOD.LBL_GROUP_MESG2}</li>
																			<li>{$CMOD.LBL_GROUP_MESG3}</li>
																		</ul>
																	</td>
																</tr>
															</table>

															<table class="slds-table slds-no-row-hover">
																	<tr class="slds-line-height--reset">
																		<td class="small" nowrap align=right>
																			<a href="#top">{$MOD.LBL_SCROLL}</a>
																		</td>
																	</tr>
															</table>

														</td>
													</tr>
												</table>
											</div>

										</td>
									</tr>
								</table>

								</td></tr></table>
							</td>
						</tr>
					</form>
				</table>
			</div>
		</td>
	</tr>
</table>

<script type="text/JavaScript">
var moveupLinkObj,moveupDisabledObj,movedownLinkObj,movedownDisabledObj;
function setObjects() {ldelim}
	availListObj=getObj("availList")
	selectedColumnsObj=getObj("selectedColumns")
{rdelim}

function addColumn() {ldelim}
	for (i=0;i<selectedColumnsObj.length;i++) {ldelim}
		selectedColumnsObj.options[i].selected=false
	{rdelim}

	for (i=0;i<availListObj.length;i++) {ldelim}
		if (availListObj.options[i].selected==true) {ldelim}
			var rowFound=false;
			var existingObj=null;
			for (j=0;j<selectedColumnsObj.length;j++) {ldelim}
				if (selectedColumnsObj.options[j].value==availListObj.options[i].value) {ldelim}
					rowFound = true;
					existingObj = selectedColumnsObj.options[j];
					break
				{rdelim}
			{rdelim}

			if (rowFound!=true) {ldelim}
				var newColObj = document.createElement("OPTION");
				newColObj.value = availListObj.options[i].value;
				if (browser_ie)
					newColObj.innerText=availListObj.options[i].innerText;
				else if (browser_nn4 || browser_nn6)
					newColObj.text=availListObj.options[i].text;
				selectedColumnsObj.appendChild(newColObj);
				availListObj.options[i].selected = false;
				newColObj.selected = true;
				rowFound = false;
			{rdelim}
			else
			{ldelim}
				if(existingObj != null)
					existingObj.selected = true;
			{rdelim}
		{rdelim}
	{rdelim}
{rdelim}

function delColumn() {ldelim}
	for (i=selectedColumnsObj.options.length;i>0;i--) {ldelim}
		if (selectedColumnsObj.options.selectedIndex>=0)
			selectedColumnsObj.remove(selectedColumnsObj.options.selectedIndex)
	{rdelim}
{rdelim}

function formSelectColumnString()
{ldelim}
	var selectedColStr = "";
	for (i=0;i<selectedColumnsObj.options.length;i++) {ldelim}
		selectedColStr += selectedColumnsObj.options[i].value + ";";
	{rdelim}
	document.newGroupForm.selectedColumnsString.value = selectedColStr;
{rdelim}
setObjects();
showOptions();
</script>
