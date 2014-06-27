{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{literal}
<style type="text/css">
.detailedViewTextBoxThisOn {
	background-color:#FFFFDD;
	border:1px solid #BABABA;
	color:#000000;
	font-family:Arial,Helvetica,sans-serif;
	font-size:11px;
	padding-left:5px;
	width:20%;
}

.detailedViewTextBoxThis {
	background-color:#FFFFFF;
	border:1px solid #BABABA;
	color:#000000;
	font-family:Arial,Helvetica,sans-serif;
	font-size:11px;
	padding-left:5px;
	width:20%;
}
</style>
	<script type="text/javascript">
		function msg()
		{
			alert("invalid entry");
			exit;
		}

		function trim(str)
		{
			while (str.substring(0,1) == ' ') // check for white spaces from beginning
			{
				str = str.substring(1, str.length);
			}
			while (str.substring(str.length-1, str.length) == ' ') // check white space from end
			{
				str = str.substring(0,str.length-1);
			}
			return str;
		}

		function valid(name) {
			var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
			var id=document.getElementById('key_HELPDESK_SUPPORT_EMAIL_ID');
			var name = document.getElementById('key_HELPDESK_SUPPORT_NAME');
			var size = document.getElementById('key_upload_maxsize');
			var maxEntries = document.getElementById('key_list_max_entries_per_page');
			var history1 = document.getElementById('key_history_max_viewed');
			var maxtext = document.getElementById('key_listview_max_textlength');
			var reg1 = /^([0-9]*)$/;
			name.value = trim(name.value);
			if ((name.value).indexOf("\"") != -1 || (name.value).indexOf("'") != -1 || (name.value).indexOf(";") != -1){
						var invalidSupportName = document.getElementById('invalidSupportName').value;
						document.getElementById('msg_HELPDESK_SUPPORT_NAME').innerHTML = invalidSupportName;
						name.focus();
						return false;
			}

			maxEntries.value = trim(maxEntries.value);
			size.value = trim(size.value);
			history1.value = trim(history1.value);
			maxtext.value = trim(maxtext.value);
			var uploadSize = document.getElementById('uploadSize').value;
			var invalidEmail = document.getElementById('invalidEmail').value;
			var emptyName = document.getElementById('emptyName').value;
			var maxListEntries= document.getElementById('maxListEntries').value;
			var maxHistory= document.getElementById('maxHistory').value;
			var maxTextLength= document.getElementById('maxTextLength').value;
			if(reg.test(id.value) == false){
				document.getElementById('msg_HELPDESK_SUPPORT_EMAIL_ID').innerHTML = invalidEmail;
				id.focus();
				return false;
			}else if(name.value == ''){
				document.getElementById('msg_HELPDESK_SUPPORT_NAME').innerHTML = emptyName;
				name.focus();
				return false;
			}else if((reg1.test(size.value) == false) || (size.value <= 0) || (size.value > 5) || (size.value == NaN)){
				document.getElementById('msg_upload_maxsize').innerHTML =uploadSize;
				size.focus();
				return false;
			}else if((reg1.test(maxEntries.value) == false)||(maxEntries.value <= 0) || (maxEntries.value>100)){
				document.getElementById('msg_list_max_entries_per_page').innerHTML=maxListEntries;
				maxEntries.focus();
				return false;
			}else if((reg1.test(history1.value) == false)||(history1.value <= 0) || (history1.value > 5)){
				document.getElementById('msg_history_max_viewed').innerHTML=maxHistory;
				history1.focus();
				return false;
			}else if((reg1.test(maxtext.value) == false) || (maxtext.value < 0) || (maxtext.value > 100) || (maxtext.value === "")){
				document.getElementById('msg_listview_max_textlength').innerHTML=maxTextLength;
				maxtext.focus();
				return false;
			}
			return true;
		}


	</script>
{/literal}
<script language="JavaScript" type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>
<body onload="replaceUploadSize();">
<input type="hidden" value="{$MOD.LBL_MAX_UPLOAD_SIZE_MSG}" id="uploadSize"></input>
<input type="hidden" value="{$MOD.LBL_INVALID_EMAIL_MSG}" id="invalidEmail"></input>
<input type="hidden" value="{$MOD.LBL_RESTRICTED_CHARACTERS}" id="invalidSupportName"></input>
<input type="hidden" value="{$MOD.LBL_MAX_LISTVIEW_ENTRIES_MSG}" id="maxListEntries"></input>
<input type="hidden" value="{$MOD.LBL_MAX_HISTORY_VIEWED_MSG}" id="maxHistory"></input>
<input type="hidden" value="{$MOD.LBL_MAX_TEXTLENGTH_LISTVIEW_MSG}" id="maxTextLength"></input>
<input type="hidden" value="{$MOD.LBL_EMPTY_NAME_MSG}" id="emptyName"></input>
<input type="hidden" value="{$MOD.LBL_HELP_INFO}" id="helpInfo"></input>

<table cellspacing="0" cellpadding="2" border="0" width="100%" class="level2Bg">
	<tbody>
		<tr>
			<td>
				<table cellspacing="0" cellpadding="0" border="0">
					<tbody>
						<tr>
							<td nowrap="" class="level2SelTab"><a href="index.php?module=Settings&amp;action=index&amp;parenttab=Settings">Settings</a></td>
							<td nowrap="" class="level2SelTab"><a href="index.php?module=Settings&amp;action=ModuleManager&amp;parenttab=Settings">Module Manager</a></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
	<tr>
		<td>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td valign=top align=right width=8><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
		<td class="showPanelBg" valign="top" width="100%" >

			<div style="padding: 20px;">
				<form onsubmit="VtigerJS_DialogBox.block();" action="index.php" method="POST" name="EditView">
					<div align="center">
						{include file="SetMenu.tpl"}
						<table cellspacing="0" cellpadding="5" border="0" width="100%" class="settingsSelUITopLine">
							<tbody>
								<tr>
									<td width="50" valign="top" rowspan="2"><img height="48" border="0" width="48" title="Users" alt="Users" src="themes/images/migrate.gif"></td>
									<td valign="bottom" class="heading2"><b><a href="index.php?module=Settings&amp;action=index&amp;parenttab=Settings">{$MOD.LBL_SETTINGS}</a> &gt; {$MOD.LBL_CONFIG_EDITOR} </b></td>
								</tr>
								<tr>
									<td valign="top"class="small">{$MOD.LBL_CONFIG_EDIT}</td>
								</tr>
							</tbody>
						</table>
						<br>
						<br>
						<table width="95%" cellspacing="0" cellpadding="0" border="0" class="small" align="center">
							<tr>
								<td>
									<table width="100%" cellspacing="0" cellpadding="3" border="0" class="small">
									<tr>
										<td nowrap="" style="width: 10px;" class="dvtTabCache">&nbsp;</td>
										<td width="75" nowrap="" align="center" class="dvtSelectedCell" style="width: 15%;"><b>{$MOD.LBL_CONFIG_FILE}</b></td>
										<td nowrap="" align="center" style="width: 100px;" class="dvtTabCache">
										{if $WARNING}
										<div style='background-color: #FFFABF; padding: 2px; margin: 0 0 2px 0; border: 1px solid yellow'>
										<b style='color: red'>{$WARNING}</b>
										{/if}&nbsp;
										</td>
										<td nowrap="" style="width: 65%;" class="dvtTabCache">&nbsp;</td>
									</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td valign="top" align="left">
									<div id="basicTab">
										<table border=0 cellspacing=0 cellpadding=3 width=100% class="dvtContentSpace">
											<tr>
												<td align="left" style="padding:10px;">
													<table border=0 cellspacing=0 cellpadding=5 width=100% class="small">
														<tr>
															<td colspan=2>
																<div align="center">
																	<input type='submit' class="crmbutton small save" value="{$APP.LBL_SAVE_LABEL}" onclick="if(valid(this)){ldelim}return true;{rdelim}else{ldelim}return false;{rdelim}">
																	<input type='button' class="crmbutton small cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onclick="location.href='index.php?module=Settings&action=index&parenttab=Settings'">

																	<input type='hidden' name='module' value='ConfigEditor'>
																	<input type='hidden' name='action' value='ConfigEditorAjax'>
																	<input type='hidden' name='file' value='index'>
																	<input type='hidden' name='type' value='save'>

																</div>
															</td>
														</tr>
														{if $CONFIGREADER}
														<tr>
															<td class="detailedViewHeader" colspan="2">
																{$MOD.LBL_CONFIG_EDIT_CAUTION_INFO}<b>{$MOD.LBL_DOUBLE_CHECK_INFO}</b>
															</td>
														</tr>
														{/if}

														{foreach item=CONFIGLINE from=$CONFIGREADER->getAll()}
															{if $CONFIGLINE->isViewable() || $CONFIGLINE->isEditable()}

																{assign var="VARMETA" value=$CONFIGLINE->meta()}

																<tr bgcolor=white valign=center style="height:25px;">
																	<td class="dvtCellLabel" width="15%">
																		{if $VARMETA.label}
																			{if $VARMETA.label == 'Helpdesk Support Email-Id'}
																				{$VARMETA.label} <img border="0" src="themes/images/help_icon.gif" onclick="vtlib_field_help_show_this(this, '{$CONFIGLINE->variableName()}' );" style="cursor: pointer;">
																			{else}
																				{$VARMETA.label}
																			{/if}
																		{else}
																			{$CONFIGLINE->variableName()}
																		{/if}
																	</td>
																	<td class="dvtCellInfo">
																		{if $CONFIGLINE->isEditable()}
																			{if $VARMETA.values}
																				<select class="small" name="key_{$CONFIGLINE->variableName()}" id="key_{$CONFIGLINE->variableName()}">
																				{foreach item=VARVALUEOPTION key=VARVALUEOPTIONKEY from=$VARMETA.values}
																					<option value="{$VARVALUEOPTIONKEY}" {if $CONFIGLINE->variableValue() eq $VARVALUEOPTIONKEY}selected=true{/if}>{$VARVALUEOPTION}</option>
																				{/foreach}
																				</select>
																			{else}
																				<b><span class="warning" id="msg_{$CONFIGLINE->variableName()}"></span></b>
																				{if $CONFIGLINE->variableName() == 'upload_maxsize'}
																				<input size="2" type="text" name="key_{$CONFIGLINE->variableName()}" id="key_{$CONFIGLINE->variableName()}" value="{$CONFIGLINE->variableValue()}"  onblur="class='detailedViewTextBoxThis'" onfocus="class='detailedViewTextBoxThisOn'" class="detailedViewTextBoxThis" >     {$MOD.LBL_MB}
																				{else}
																						<input type="text" name="key_{$CONFIGLINE->variableName()}" id="key_{$CONFIGLINE->variableName()}" value="{$CONFIGLINE->variableValue()}"  onblur="this.className='detailedViewTextBox'" onfocus="this.className='detailedViewTextBoxOn'" class="detailedViewTextBox" >
																				{/if}
																			{/if}
																		{else}
																			{$CONFIGLINE->variableValue()}
																		{/if}
																	</td>
																</tr>
															{/if}
														{/foreach}
													</table>

													<tr>
														<td colspan=2>
															<div align="center">
																<input type='submit' class="crmbutton small save" value="{$APP.LBL_SAVE_LABEL}" onclick="if(valid(this)){ldelim}return true;{rdelim}else{ldelim}return false;{rdelim}">
																<input type='button' class="crmbutton small cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onclick="location.href='index.php?module=Settings&action=index&parenttab=Settings'">

																<input type='hidden' name='module' value='ConfigEditor'>
																<input type='hidden' name='action' value='ConfigEditorAjax'>
																<input type='hidden' name='file' value='index'>
																<input type='hidden' name='type' value='save'>
															</div>
														</td>
													</tr>
												</td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
						</table>
					</div>
				</form>
			</div>
		</td>
		<td valign=top align=right><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
	</tr>
</table>
</body>
