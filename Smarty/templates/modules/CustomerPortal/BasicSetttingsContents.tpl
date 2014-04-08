{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
<script language="JavaScript" type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>

<script type="text/javascript">
	var moduleInfo = JSON.parse('{$PORTALMODULES}');
	function initialModuleSettings(){ldelim}
		renderModuleSettings(moduleInfo);
	{rdelim}
	function visibleValue(sequence,tabid){ldelim}
		visibleValueChange(sequence,tabid,moduleInfo);
	{rdelim}
	function prefValue(sequence,tabid){ldelim}
		prefValueChange(sequence,tabid,moduleInfo);
	{rdelim}
	function moveModules(sequence,move){ldelim}
		if(move == "Up")
			moveUp(moduleInfo,sequence);
		else
			moveDown(moduleInfo,sequence);
	{rdelim}
	function renderModuleSettings(moduleInfo){ldelim}
	
		var upImage = "{'arrow_up.png'|@vtiger_imageurl:$THEME}";
		var downImage = "{'arrow_down.png'|@vtiger_imageurl:$THEME}";
		var blankImage = "{'blank.gif'|@vtiger_imageurl:$THEME}";
		var displayData =
				'<table id="my_table" border=0 cellspacing=0 cellpadding=5 width="100%" class="dvtContentSpace" align="center">'+
				'<tr>'+
					'<td class="detailedViewHeader" colspan="4" ><b>{'LBL_MODULE_INFORMATION'|@getTranslatedString:$MODULE}</b></td>'+
				'</tr>'+
				'<tr align="left">'+
					'<td class="colHeader small">{'LBL_MODULE'|@getTranslatedString:$MODULE}</td>'+
					'<td class="colHeader small">{'LBL_SEQUENCE'|@getTranslatedString:$MODULE}</td>'+
					'<td class="colHeader small">{'LBL_VISIBLE'|@getTranslatedString:$MODULE}</td>'+
					'<td class="colHeader small">{'LBL_VIEW_ALL_RECORD'|@getTranslatedString:$MODULE}</td>'+
				'</tr>';
			
		for(i=1;i<=moduleInfo.size();i++){ldelim}
			var upImageTag = '<img src="'+upImage+'" style="width:16px;height:16px;" border="0"/>';
			var downImageTag = '<img src="'+downImage+'" style="width:16px;height:16px;" border="0"/>';
			var blankImageTag = '<img src="'+blankImage+'" style="width:16px;height:16px;" border="0"/>';
			
			if(moduleInfo[i].sequence == 1) {ldelim}
				upImageTag = '';
			{rdelim}
			else if(moduleInfo[i].sequence == moduleInfo.size()){ldelim}
				downImageTag = '';
			{rdelim}
			else {ldelim}
				blankImageTag = '';
			{rdelim}
			var visibleTag;
			if(moduleInfo[i].visible == 1){ldelim}
				visibleTag = '<input type="checkbox" id="enable_disable_'+moduleInfo[i].name+'" onclick="javascript:visibleValue(\''+moduleInfo[i].sequence+'\',\''+moduleInfo[i].tabid+'\');" name="enable_disable_'+moduleInfo[i].name+'" checked>';
			{rdelim}
			else{ldelim}
				visibleTag = '<input type="checkbox" id="enable_disable_'+moduleInfo[i].name+'" onclick="javascript:visibleValue(\''+moduleInfo[i].sequence+'\',\''+moduleInfo[i].tabid+'\');" name="enable_disable_'+moduleInfo[i].name+'">';
			{rdelim}
			//alert(upImageTag);
			var valueTag = '';
			if(moduleInfo[i].value == 1){ldelim}
				valueTag = '{'LBL_YES'|@getTranslatedString:$MODULE}<input type="radio" name="view_'+moduleInfo[i].name+'" id="view_'+moduleInfo[i].name+'"  checked="checked" value="showall"> '+
							'{'LBL_NO'|@getTranslatedString:$MODULE}<input type="radio" name="view_'+moduleInfo[i].name+'" id="view_'+moduleInfo[i].name+'" onclick="javascript:prefValue(\''+moduleInfo[i].sequence+'\',\''+moduleInfo[i].tabid+'\');"  value="onlymine">';
			{rdelim}
			else{ldelim}
				valueTag = '{'LBL_YES'|@getTranslatedString:$MODULE}<input type="radio" name="view_'+moduleInfo[i].name+'" id="view_'+moduleInfo[i].name+'" onclick="javascript:prefValue(\''+moduleInfo[i].sequence+'\',\''+moduleInfo[i].tabid+'\');"  value="showall"> '+
							'{'LBL_NO'|@getTranslatedString:$MODULE}<input type="radio" name="view_'+moduleInfo[i].name+'" id="view_'+moduleInfo[i].name+'"  checked="checked" value="onlymine">';
			{rdelim}
			displayData +=
				'<tr><td class="listTableRow small" width="35%">'+moduleInfo[i].name+'</td>' +
				'<input type="hidden" name="seq_'+moduleInfo[i].name+'" value="'+moduleInfo[i].sequence+'">' +
				'<td  align="center" class="listTableRow">' +
				'<a href="javascript:moveModules(\''+moduleInfo[i].sequence+'\',\'Up\');">' +
					upImageTag + '</a>' +
				blankImageTag +
				'<a href="javascript:moveModules(\''+moduleInfo[i].sequence+'\',\'Down\');">' +
					downImageTag + '</a>' +
				'</td>' +
				'<td class="listTableRow cellText small"  align="center">' +
					visibleTag +
				'</td>' +
				'<td class="listTableRow">' +
					valueTag +
				'</td>' +
				'</tr>';
		{rdelim}
	displayData += '</table>';
	document.getElementById('displayData').innerHTML = displayData;
{rdelim}
</script>

<table width="100%" border=0>
	<tr>
		<td width="55%">
			<div id="displayData" ></div>
		</td>
		<td valign="top">
			<table border=0 cellspacing=0 cellpadding=3 width="100%" align="center" class="dvtContentSpace">
				<tr>
					<td class="detailedViewHeader" colspan="4" ><b>{'LBL_USER_INFORMATION'|@getTranslatedString:$MODULE}</b></td>
				</tr>
				<tr>
					<td  class="dvtCellLabel" align="right" width="40%">{'LBL_SELECT_USERS'|@getTranslatedString:$MODULE}</td>
					<td  class="dvtCellInfo" align="left">
						<select name="userid" class="small">
							{foreach item=user from=$USERS}
								{if $USERID eq $user.id}
									<option value="{$user.id}" selected>{$user.name}</option>
								{else}
									<option value="{$user.id}">{$user.name}</option>
	{/if}
							{/foreach}
						</select>
						<br><br>
						<span class="helpmessagebox" style="font-style: italic;">{'LBL_USER_DESCRIPTION'|@getTranslatedString:$MODULE}</span>
					</td>
				</tr>
				<tr>
					<td  class="dvtCellLabel" align="right">{'LBL_DEFAULT_USERS'|@getTranslatedString:$MODULE}</td>
					<td  class="dvtCellInfo" align="left">
						<select name="defaultAssignee" class="small">
							<optgroup style="border: none" label="Users" >
								{foreach item=user from=$USERS}
									{if $DEFAULTASSIGNEE eq $user.id}
										<option value="{$user.id}" selected>{$user.name}</option>
									{else}
										<option value="{$user.id}">{$user.name}</option>
	{/if}	
								{/foreach}
							</optgroup>
							<optgroup style="border: none" label="Groups">
								{foreach item=group from=$GROUPS}
									{if $DEFAULTASSIGNEE eq $group.groupid}
										<option value="{$group.groupid}" selected>{$group.groupname}</option>
		{else}
										<option value="{$group.groupid}">{$group.groupname}</option>
		{/if}
								{/foreach}
							</optgroup>
						</select>
						<br><br>
						<span class="helpmessagebox" style="font-style: italic;">{'LBL_GROUP_DESCRIPTION'|@getTranslatedString:$MODULE}</span>
					</td>
				</tr>
			</table>
	</td>
	</tr>
</table>	
<br><br>
		<center><input class="crmbutton small save" type="Submit" style="width:70px" title="{$APP.LBL_SAVE_LABEL}" value="{$APP.LBL_SAVE_LABEL}" alt="{$APP.LBL_SAVE_LABEL}" onclick=VtigerJS_DialogBox.block();></center>
		
<script>
	window.onload=function(){ldelim}
		initialModuleSettings();
	{rdelim}
</script>
