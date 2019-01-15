{*<!--/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
********************************************************************************/ -->*}
<form action="index.php" method="post" name="form" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="fld_module" value="{$MODULE}">
	<input type="hidden" name="module" value="Settings">
	<input type="hidden" name="parenttab" value="Settings">
	{assign var=entries value=$CFENTRIES}
	<br>
	<br>
	<div style="display:block; position:relative; width:225px;" class="layerPopup">
		<table width="100%" border="0" cellpadding="3" cellspacing="0" class="layerHeadingULine" >
				<tr class="colHeader big">
					<td width="80%" colspan="2" class="cblds-p_medium">
						{$MOD.LBL_RELATED_LIST}
					</td>
					<td width="20%" align="right" colspan="2" class="cblds-t-align_right cblds-p_medium">
						<img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" onclick="fninvsh('relatedlistdiv');" alt="{$APP.LBL_CLOSE}" title="{$APP.LBL_CLOSE}" />
					</td>
				</tr>
				<tr class="big">
					<td width="80%" colspan="2" class="cblds-p_medium" style="border-bottom-color: #ddd;border-bottom-width: 1px; border-bottom-style: solid;">
						<select class="small" style='width:155px;' name='relatewithmodule' id='relatewithmodule'>
							{html_options options=$NotRelatedModules}
						</select>
					</td>
					<td width="20%" align="right" colspan="2" style="border-bottom-color: #ddd;border-bottom-width: 1px; border-bottom-style: solid;">
						<input class="crmButton small save" title="{$APP.LBL_ADD_NEW}" onclick="createRelatedList('{$MODULE}');" type="button" name="crlbutton" value=" {$APP.LBL_ADD_NEW} ">
					</td>
				</tr>
				{foreach item=related from=$RELATEDLIST name=relinfo}
				<tr>
					<td>{$related.label}</td>
				{if $smarty.foreach.relinfo.first}
					<td align="right" class="cblds-t-align_right cblds-p-v_medium">
						<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
					</td>
					<td align="right" valign="middle" class="cblds-t-align_right cblds-p-v_medium">
						<img src="{'arrow_down.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeRelatedListorder('move_down','{$related.tabid}','{$related.sequence}','{$related.id}','{$MODULE}'); " alt="{$MOD.DOWN}" title="{$MOD.DOWN}">&nbsp;&nbsp;
					</td>
				{elseif $smarty.foreach.relinfo.last}
					<td align="right" valign="middle" class="cblds-t-align_right cblds-p-v_medium">
						<img src="{'arrow_up.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeRelatedListorder('move_up','{$related.tabid}','{$related.sequence}','{$related.id}','{$MODULE}'); " alt="{$MOD.UP}" title="{$MOD.UP}">&nbsp;&nbsp;
					</td>
					<td align="right" class="cblds-t-align_right cblds-p-v_medium">
						<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
					</td>
				{else}
					<td align="right" valign="middle" class="cblds-t-align_right cblds-p-v_medium">
						<img src="{'arrow_up.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeRelatedListorder('move_up','{$related.tabid}','{$related.sequence}','{$related.id}','{$MODULE}') " alt="{$MOD.UP}" title="{$MOD.UP}">&nbsp;&nbsp;
					</td>
					<td align="right" valign="middle" class="cblds-t-align_right cblds-p-v_medium">
						<img src="{'arrow_down.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeRelatedListorder('move_down','{$related.tabid}','{$related.sequence}','{$related.id}','{$MODULE}') " alt="{$MOD.DOWN}" title="{$MOD.DOWN}">&nbsp;&nbsp;
					</td>
				{/if}
				<td align="right" valign="middle" class="cblds-t-align_right cblds-p-v_medium">
					<img src="{'delete.gif'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="if (confirm(alert_arr.ARE_YOU_SURE_YOU_WANT_TO_DELETE)) deleteRelatedList('{$related.tabid}','{$related.sequence}','{$related.id}','{$MODULE}');" alt="{$MOD.LBL_DELETE}" title="{$MOD.LBL_DELETE}">
				</td>
			</tr>
			{/foreach}
		</table>
	</div>
</form>
