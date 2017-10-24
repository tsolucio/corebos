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
	var rolename = document.getElementById('rolename').value;
	var mode = getObj('mode').value;
	var roleid = getObj('roleid').value;
	if(mode == 'edit')
		var urlstring ="&mode="+mode+"&roleName="+rolename+"&roleid="+roleid;
	else
		var urlstring ="&roleName="+rolename;
	//var status = CharValidation(rolename,'namespace');
	//if(status)
	//{ldelim}
	jQuery.ajax({ldelim}
			method:"POST",
			url:'index.php?module=Settings&action=SettingsAjax&file=SaveRole&ajax=true&dup_check=true'+urlstring
	{rdelim}).done(function(response) {ldelim}
					if(response.indexOf('SUCCESS') > -1)
						document.newRoleForm.submit();
					else
						alert(response);
			{rdelim}
				);
	//{rdelim}
	//else
	//	alert(alert_arr.NO_SPECIAL+alert_arr.IN_ROLENAME)

{rdelim}
function validate()
{ldelim}
	formSelectColumnString();
	if( !emptyCheck("roleName", "Role Name", "text" ) )
		return false;

	if(document.newRoleForm.selectedColumnsString.value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0)
	{ldelim}

		alert('{$APP.ROLE_SHOULDHAVE_INFO}');
		return false;
	{rdelim}
	dup_validation();return false
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
				<!-- DISPLAY Edit Role & DISPLAY View Role-->
				<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz" style="background-color: #f7f9fb;">
				{literal}
				<form name="newRoleForm" action="index.php" method="post" onSubmit="if(validate()) { VtigerJS_DialogBox.block();} else { return false;} ">
				{/literal}
					<input type="hidden" name="module" value="Settings">
					<input type="hidden" name="action" value="SaveRole">
					<input type="hidden" name="parenttab" value="Settings">
					<input type="hidden" name="returnaction" value="{$RETURN_ACTION}">
					<input type="hidden" name="roleid" value="{$ROLEID}">
					<input type="hidden" name="mode" value="{$MODE}">
					<input type="hidden" name="parent" value="{$PARENT}">

					<!-- Edit Role HEADER -->
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
															<img src="{'ico-roles.gif'|@vtiger_imageurl:$THEME}" alt="{$CMOD.LBL_ROLES}" width="48" height="48" title="{$CMOD.LBL_ROLES}">
														</span>
													</div>
												</span>
											</div>
										</div>
										<div class="slds-media__body">
											<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
												<span class="uiOutputText">
													{if $MODE eq 'edit'}
														<a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > <a href="index.php?module=Settings&action=listroles&parenttab=Settings">{$CMOD.LBL_ROLES}</a> &gt; {$MOD.LBL_EDIT} &quot;{$ROLENAME}&quot; </b>
													{else}
														<a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > <a href="index.php?module=Settings&action=listroles&parenttab=Settings">{$CMOD.LBL_ROLES}</a> &gt; {$CMOD.LBL_CREATE_NEW_ROLE}</b>
													{/if}
												</span>
												<span class="small">
													{if $MODE eq 'edit'}
														{$MOD.LBL_EDIT} {$CMOD.LBL_PROPERTIES} &quot;{$ROLENAME}&quot; {$MOD.LBL_LIST_CONTACT_ROLE}
													{else}
														{$CMOD.LBL_NEW_ROLE}
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

				<br/>

				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td>

							<!-- Properties of Selected Member Section -->
							<table border=0 cellspacing=0 cellpadding=5 width=100% >
								<tr>
									<td>
										<!-- Edit Role Properties Header-->
										<div class="forceRelatedListSingleContainer">
											<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
												<div class="slds-card__header slds-grid">
													<header class="slds-media slds-media--center slds-has-flexi-truncate">
														<div class="slds-media__body">
															<h2>
																<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																	<strong>
																		{if $MODE eq 'edit'}
																			{$CMOD.LBL_PROPERTIES} &quot;{$ROLENAME}&quot;
																		{else}
																			{$CMOD.LBL_NEW_ROLE}
																		{/if}
																	</strong>
																</span>
															</h2>
														</div>
													</header>
													<div class="slds-no-flex">
														<div class="actionsContainer">
															<input type="button" class="slds-button slds-button--small slds-button_success" name="add" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " onClick="return validate()">
															<input type="button" class="slds-button slds-button--small slds-button--destructive" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onClick="window.history.back()">
														</div>
													</div>
												</div>
											</article>
										</div>

										<!-- Edit Role Properties Body -->
										<div class="slds-truncate">
											<table class="slds-table slds-no-row-hover detailview_table">
												<tr class="slds-line-height--reset">
													<td width="15%" class="dvtCellLabel"><font color="red">*</font><strong>{$CMOD.LBL_ROLE_NAME}</strong></td>
													<td width="85%" class="dvtCellInfo" ><input id="rolename" name="roleName" type="text" value="{$ROLENAME}" class="slds-input"></td>
												</tr>
												<tr class="slds-line-height--reset">
													<td class="dvtCellLabel"><strong>{$CMOD.LBL_REPORTS_TO}</strong></td>
													<td class="dvtCellInfo">{$PARENTNAME}</td>
												</tr>
												<!-- Profile Section -->
												<tr class="slds-line-height--reset">
													<td colspan="2" valign=top class="dvtCellLabel text-center big"><strong>{$CMOD.LBL_PROFILE_M}</strong></td>
												</tr>
												<tr class="slds-line-height--reset">
													<td colspan="2" valign=top>
														<table class="slds-table slds-no-row-hover">
															<tr class="slds-line-height--reset">
																<td width="45%" valign=top class="dvtCellLabel text-left cellBottomDotLinePlain small"><strong>{$CMOD.LBL_PROFILES_AVLBL}</strong></td>
																<td width="10%">&nbsp;</td>
																<td width="45%" class="dvtCellLabel text-left cellBottomDotLinePlain small"><strong>{$CMOD.LBL_ASSIGN_PROFILES}</strong></td>
															</tr>
															<tr class="slds-line-height--reset">
																<td valign=top width="45%">
																	<!-- Profiles Members -->
																	<table class="slds-table slds-no-row-hover">
																		<tr class="slds-line-height--reset">
																			<td width="25%" class="dvtCellLabel text-left">
																				{$CMOD.LBL_PROFILE_M} {$CMOD.LBL_MEMBER}
																			</td>
																		</tr>
																		<tr>
																			<td width="75%" class="dvtCellInfo">
																				<select multiple id="availList" name="availList" class="small crmFormList" size=10 >
																					{foreach item=element from=$PROFILELISTS}
																						<option value="{$element.0}">{$element.1}</option>
																					{/foreach}
																				</select>
																			</td>
																		</tr>
																	</table>
																</td>
																<td>
																	<!-- Add & Remove buttons -->
																	<div width="10%" align="center">
																		<input type="hidden" name="selectedColumnsString"/>
																		<input type="button" name="Button" value="&nbsp;&rsaquo;&rsaquo;&nbsp;" onClick="addColumn()" class="slds-button slds-button--small slds-button_success" /><br /><br />
																		<input type="button" name="Button1" value="&nbsp;&lsaquo;&lsaquo;&nbsp;" onClick="delColumn()" class="slds-button slds-button--small slds-button--destructive"/>
																	</div>
																</td>
																<td width="45%" class="small" valign=top>
																	<!-- Members of  'selected member' -->
																	<table class="slds-table slds-no-row-hover">
																		<tr class="slds-line-height--reset">
																			<td width="25%" class="dvtCellLabel text-left">
																				{$CMOD.LBL_MEMBER} {'LBL_LIST_OF'|@getTranslatedString} <b>{$ROLENAME}</b>
																			</td>
																		</tr>
																		<tr class="slds-line-height--reset">
																			<td width="75%" class="dvtCellInfo">
																				<select multiple id="selectedColumns" name="selectedColumns" class="small crmFormList" size=10 >
																					{foreach item=element from=$SELPROFILELISTS}
																						<option value="{$element.0}">{$element.1}</option>
																					{/foreach}
																				</select>
																			</td>
																		</tr>
																	</table>
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

							<table border=0 cellspacing=0 cellpadding=5 width=100% >
								<tr><td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td></tr>
							</table>

						</td>
					</tr>
				</table>

						</td></tr></table><!-- close tables from setMenu -->
						</td></tr></table><!-- close tables from setMenu -->


						</form>
					</table><!-- / newRoleForm -->

				</div>
			</td>
		</tr>
	</tbody>
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
							rowFound=true
							existingObj=selectedColumnsObj.options[j]
							break
						{rdelim}
					{rdelim}

					if (rowFound!=true) {ldelim}
						var newColObj=document.createElement("OPTION")
						newColObj.value=availListObj.options[i].value
						if (browser_ie) newColObj.innerText=availListObj.options[i].innerText
						else if (browser_nn4 || browser_nn6) newColObj.text=availListObj.options[i].text
						selectedColumnsObj.appendChild(newColObj)
						availListObj.options[i].selected=false
						newColObj.selected=true
						rowFound=false
					{rdelim}
					else
					{ldelim}
						if(existingObj != null) existingObj.selected=true
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
			document.newRoleForm.selectedColumnsString.value = selectedColStr;
		{rdelim}
	setObjects();
</script>
