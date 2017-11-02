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

<script type="text/javascript">
	var allOptions = null;

	function setAllOptions(inputOptions) 
	{ldelim}
		allOptions = inputOptions;
	{rdelim}

	function modifyMergeFieldSelect(cause, effect) 
	{ldelim}
		var selected = cause.options[cause.selectedIndex].value;  id="mergeFieldValue"
		var s = allOptions[cause.selectedIndex];
		effect.length = s;
		for (var i = 0; i < s; i++) 
	{ldelim}
			effect.options[i] = s[i];
		{rdelim}
		document.getElementById('mergeFieldValue').value = '';
	{rdelim}
{literal}
	function init() 
	{
		var blankOption = new Option('--None--', '--None--');
		var options = null;
{/literal}

		var allOpts = new Object({$ALL_VARIABLES|@count}+1);
		{assign var="alloptioncount" value="0"}
		{foreach key=index item=module from=$ALL_VARIABLES}
			options = new Object({$module|@count}+1);
			{assign var="optioncount" value="0"}
			options[{$optioncount}] = blankOption;
			{foreach key=header item=detail from=$module}
			 {assign var="optioncount" value=$optioncount+1}
				options[{$optioncount}] = new Option('{$detail.0}', '{$detail.1}');
			{/foreach}	  
			 {assign var="alloptioncount" value=$alloptioncount+1}
			 allOpts[{$alloptioncount}] = options;
		{/foreach}
		setAllOptions(allOpts);
	}

{literal}
	function cancelForm(frm)
	{
		frm.action.value='detailviewemailtemplate';
		frm.parenttab.value='Settings';
		frm.submit();
	}
{/literal}
</script>

<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td valign="top" width="100%">
				<div align=center>
					<br>
						{include file='SetMenu.tpl'}
							<!-- DISPLAY Create/Edit Email Template-->
									{literal}
									<form action="index.php" method="post" name="templatecreate" onsubmit="if(check4null(templatecreate)) { VtigerJS_DialogBox.block(); } else { return false; }">
									{/literal}
										<input type="hidden" name="action">
										<input type="hidden" name="mode" value="{$EMODE}">
										<input type="hidden" name="module" value="Settings">
										<input type="hidden" name="templateid" value="{$TEMPLATEID}">
										<input type="hidden" name="parenttab" value="{$PARENTTAB}">

											<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz" style="background-color: #f7f9fb;">
												<tr class="slds-text-title--caps">
													<td style="padding: 0;">
														<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilSettings" style="height: 70px;">
															<div class="slds-grid primaryFieldRow" style="transform: translate3d(0, -8.65823px, 0);">
																<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
																	<!-- Image -->
																	<div class="slds-media slds-no-space" style="transform: scale3d(0.864715, 0.864715, 1) translate3d(4.32911px, 2.16456px, 0);">
																		<div class="slds-media__figure slds-icon forceEntityIcon">
																			<span class="photoContainer forceSocialPhoto">
																				<div class="small roundedSquare forceEntityIcon">
																					<span class="uiImage">
																						<img src="{'ViewTemplate.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_MODULE_NAME}" title="{$MOD.LBL_MODULE_NAME}"/>
																					</span>
																				</div>
																			</span>
																		</div>
																	</div>
																	<!-- Title and help text -->
																	<div class="slds-media__body">
																		<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
																			<span class="uiOutputText" style="width: 100%;">
																				<b>
																				{if $EMODE eq 'edit'}
																					<a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > 
																					<a href="index.php?module=Settings&action=listemailtemplates&parenttab=Settings">{$UMOD.LBL_EMAIL_TEMPLATES}</a> 
																					&gt; {$MOD.LBL_EDIT} &quot;{$TEMPLATENAME}&quot;
																				{else}
																					<a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > 
																					<a href="index.php?module=Settings&action=listemailtemplates&parenttab=Settings">{$UMOD.LBL_EMAIL_TEMPLATES}</a> 
																					&gt; {$MOD.LBL_CREATE_EMAIL_TEMPLATES}
																				{/if}
																				</b>
																			</span>
																			<span class="small">{$MOD.LBL_EMAIL_TEMPLATE_DESC}</span>
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

														<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
															<tr>
																<td class="big">
																	<div class="forceRelatedListSingleContainer">
																		<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																			<div class="slds-card__header slds-grid">
																				<header class="slds-media slds-media--center slds-has-flexi-truncate">
																					<div class="slds-media__body">
																						<h2>
																							<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																								<strong>
																									{if $EMODE eq 'edit'}
																										{$UMOD.LBL_PROPERTIES} &quot;{$TEMPLATENAME}&quot;
																									{else}
																										{$MOD.LBL_CREATE_EMAIL_TEMPLATES}
																									{/if}
																								</strong>
																							</span>
																						</h2>
																					</div>
																				</header>
																				<div class="slds-no-flex">
																						<input type="submit" value="{$APP.LBL_SAVE_BUTTON_LABEL}" class="slds-button slds-button--small slds-button_success" onclick="this.form.action.value='saveemailtemplate'; this.form.parenttab.value='Settings'" >&nbsp;
																					{if $EMODE eq 'edit'}
																						<input type="submit" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="slds-button slds-button--small slds-button--destructive" onclick="cancelForm(this.form)" />
																					{else}
																						<input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="slds-button slds-button--small slds-button--destructive" onclick="window.history.back()" >
																					{/if}
																				</div>
																			</div>
																		</article>
																	</div>

																	<div class="slds-truncate">
																		<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table">
																			<tr>
																				<td width=15% class="small dvtCellLabel"><font color="red">*</font><strong>{$UMOD.LBL_NAME}</strong></td>
																				<td width=85% class="small dvtCellInfo"><input name="templatename" type="text" value="{$TEMPLATENAME}" class="slds-input" tabindex="1"></td>
																			</tr>
																			<tr>
																				<td valign=top class="small dvtCellLabel"><strong>{$UMOD.LBL_DESCRIPTION}</strong></td>
																				<td class="dvtCellInfo small" valign=top>
																					<span class="small">
																						<input name="description" type="text" value="{$DESCRIPTION}" class="slds-input" tabindex="2">
																					</span>
																				</td>
																			</tr>
																			<tr>
																				<td valign=top class="small dvtCellLabel"><strong>{$UMOD.LBL_FOLDER}</strong></td>
																				<td class="dvtCellInfo small" valign=top>
																				{if $EMODE eq 'edit'}
																					<select name="foldername" class="slds-select" style="width: 75%;" tabindex="3">
																						{foreach item=arr from=$FOLDERNAME}
																							<option value="{$FOLDERNAME}" {$arr}>{$FOLDERNAME}</option>
																							{if $FOLDERNAME == 'Public'}
																								<option value="Personal">{$UMOD.LBL_PERSONAL}</option>
																							{else}
																								<option value="Public">{$UMOD.LBL_PUBLIC}</option>
																							{/if}
																						{/foreach}
																					</select>
																				{else}
																					<select name="foldername" class="slds-select" style="width: 75%;" value="{$FOLDERNAME}" tabindex="3">
																						<option value="Personal">{$UMOD.LBL_PERSONAL}</option>
																						<option value="Public" selected>{$UMOD.LBL_PUBLIC}</option>
																					</select>
																				{/if}
																				</td>
																			</tr>
																		</table>
																	</div>

																</td>
															</tr>
														</table>

														<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
															<tr>
																<td class="big" valign="top" style="padding-left: 0; padding-right: 0;">

																	<div class="forceRelatedListSingleContainer">
																		<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																			<div class="slds-card__header slds-grid">
																				<header class="slds-media slds-media--center slds-has-flexi-truncate">
																					<div class="slds-media__body">
																						<h2>
																							<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																								<strong>{$UMOD.LBL_EMAIL_TEMPLATE}</strong>
																							</span>
																						</h2>
																					</div>
																				</header>
																			</div>
																		</article>
																	</div>

																	<div class="slds-truncate">
																		<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table">
																			<tr>
																				<td width="15%" class="dvtCellLabel small">{$UMOD.SendEmailFrom}</td>
																				<td width="85%" colspan="2" class="dvtCellInfo small">
																					<span class="small ">
																						<input name="emailfrom" type="text" value="{$EMAILFROM}" class="slds-input" tabindex="3">
																					</span>
																				</td>
																			</tr>
																			<tr>
																				<td width="15%" class="dvtCellLabel small"><font color='red'>*</font>{$UMOD.LBL_SUBJECT}</td>
																				<td width="85%" colspan="2" class="dvtCellInfo small">
																					<span class="small">
																						<input name="subject" type="text" value="{$SUBJECT}" class="slds-input" tabindex="4">
																					</span>
																				</td>
																			</tr>
																			<tr>
																				<td width="15%"  class="dvtCellLabel small" valign="center">{$UMOD.LBL_SELECT_FIELD_TYPE}</td>
																				<td width="85%" colspan="2" class="dvtCellInfo small">
																					<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table field-type-table">
																						<tr>
																							<td class="dvtCellLabel text-left">{$UMOD.LBL_STEP}1</td>
																							<td class="dvtCellLabel text-left" style="border-left:2px dotted #cccccc;">{$UMOD.LBL_STEP}2</td>
																							<td class="dvtCellLabel text-left" style="border-left:2px dotted #cccccc;">{$UMOD.LBL_STEP}3</td>
																						</tr>
																						<tr>
																							<td>
																								<select class="slds-select" id="entityType" ONCHANGE="modifyMergeFieldSelect(this, document.getElementById('mergeFieldSelect'));" tabindex="6">
																									<OPTION VALUE="0" selected>{$APP.LBL_NONE}
																									<OPTION VALUE="1">{$UMOD.LBL_ACCOUNT_FIELDS}
																									<OPTION VALUE="2">{$UMOD.LBL_CONTACT_FIELDS}
																									<OPTION VALUE="3" >{$UMOD.LBL_LEAD_FIELDS}
																									<OPTION VALUE="4" >{$UMOD.LBL_USER_FIELDS}
																									<OPTION VALUE="5" >{$UMOD.LBL_HELPDESK_FIELDS}
																									<OPTION VALUE="6" >{$UMOD.LBL_GENERAL_FIELDS}
																								</select>
																							</td>
																							<td style="border-left:2px dotted #cccccc;">
																								<select id="mergeFieldSelect" class="slds-select" onchange="document.getElementById('mergeFieldValue').value=this.options[this.selectedIndex].value;" tabindex="7">
																									<option value="0" selected>{$APP.LBL_NONE}</option>
																								</select>
																							</td>
																							<td style="border-left:2px dotted #cccccc;">
																								<input type="text" class="slds-input" style="width: 100%" id="mergeFieldValue" name="variable" value="variable" tabindex="8"/>
																							</td>
																						</tr>
																					</table>
																				</td>
																			</tr>
																			<tr>
																				<td valign="top" width=10% class="dvtCellLabel small">{$UMOD.LBL_MESSAGE}</td>
																				<td valign="top" colspan="2" width=60% class="dvtCellInfo small"><p><textarea name="body" class="slds-textarea" tabindex="5">{$BODY}</textarea></p>
																			</tr>
																		</table>
																	</div>

																</td>
															</tr>
														</table>


														<table border=0 cellspacing=0 cellpadding=5 width=100% >
															<tr>
																<td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td>
															</tr>
														</table>

													</td>
												</tr>
											</table>
									</form>


					</td></tr></table><!-- close tables from setMenu -->
					</td></tr></table><!-- close tables from setMenu -->

				</div>
			</td>
		</tr>
	</tbody>
</table>

<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
<script type="text/javascript" defer="1">var textAreaName = null;
	var textAreaName = 'body';
	CKEDITOR.replace( textAreaName,	{ldelim}
		extraPlugins : 'uicolor',
		uiColor: '#dfdff1'
	{rdelim} ) ;
	var oCKeditor = CKEDITOR.instances[textAreaName];
</script>

<script>

function check4null(form)
{ldelim}

		var isError = false;
		var errorMessage = "";
		// Here we decide whether to submit the form.
		if (trim(form.templatename.value) =='') {ldelim}
				isError = true;
				errorMessage += "\n template name";
				form.templatename.focus();
		{rdelim}
		if (trim(form.foldername.value) =='') {ldelim}
				isError = true;
				errorMessage += "\n folder name";
				form.foldername.focus();
		{rdelim}
		if (trim(form.subject.value) =='') {ldelim}
				isError = true;
				errorMessage += "\n subject";
				form.subject.focus();
		{rdelim}
		if (trim(form.emailfrom.value) !='' && !patternValidate('emailfrom','{$UMOD.SendEmailFrom}','EMAIL')) {ldelim}
				isError = true;
				errorMessage += "\n email from";
				form.emailfrom.focus();
		{rdelim}

		// Here we decide whether to submit the form.
		if (isError == true) {ldelim}
				alert("{$APP.MISSING_FIELDS}" + errorMessage);
				return false;
		{rdelim}
 return true;

{rdelim}

init();

</script>
