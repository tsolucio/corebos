{*
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/ *}
<script language="JavaScript" type="text/javascript" src="include/js/customview.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/general.js"></script>
<script src="modules/com_vtiger_workflow/resources/jquery-1.2.6.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/functional.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/json2.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/parallelexecuter.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/fieldexpressionpopup.js" type="text/javascript" charset="utf-8"></script>
<script language="JavaScript">
var moduleName = '{$MODULE}';
</script>
<script src="modules/Settings/LayoutBlockList.js" type="text/javascript" charset="utf-8"></script>
{include file="com_vtiger_workflow/FieldExpressions.tpl"}
<div id = "layoutblock">
<div id="relatedlistdiv" style="display:none; position: absolute; width: 225px; left: 300px; top: 300px;"></div>
<br>
{assign var=entries value=$CFENTRIES}
{if $CFENTRIES.0.tabpresence eq '0' }
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tr>
		<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
		<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
		<br>
			<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
				<tr>
					<td rowspan="2" valign="top" width="50"><img src="{'orgshar.gif'|@vtiger_imageurl:$THEME}" alt="Users" title="Users" border="0" height="48" width="48"></td>
					<td class="heading2" valign="bottom">
						<b><a href="index.php?module=Settings&action=ModuleManager&parenttab=Settings">{$MOD.VTLIB_LBL_MODULE_MANAGER}</a> 
						&gt;<a href="index.php?module=Settings&action=ModuleManager&module_settings=true&formodule={$MODULE}&parenttab=Settings">{if $APP.$MODULE } {$APP.$MODULE} {elseif $MOD.$MODULE} {$MOD.$MODULE} {else} {$MODULE} {/if}</a> &gt; 
						{$MOD.LBL_LAYOUT_EDITOR}</b>
					</td>
				</tr>
				<tr>
					<td class="small" valign="top">{$MOD.LBL_LAYOUT_EDITOR_DESCRIPTION}
					</td>
					<td align="right" width="15%"><input type="button" class="crmButton create small" onclick="callRelatedList('{$CFENTRIES.0.module}');fnvshNrm('relatedlistdiv');posLay(this,'relatedlistdiv');" alt="{$MOD.ARRANGE_RELATEDLIST}" title="{$MOD.ARRANGE_RELATEDLIST}" value="{$MOD.ARRANGE_RELATEDLIST}"/>
					</td>
					<td align="right" width="8%"><input type="button" class="crmButton create small" onclick="fnvshobj(this,'addblock');" alt="{$MOD.ADD_BLOCK}" title="{$MOD.ADD_BLOCK}" value="{$MOD.ADD_BLOCK}"/>
					</td>
					&nbsp; <img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" id="vtbusy_info" style="display:none;position:absolute;top:180px;right:100px;" border="0" />
				</tr>
			</table>
			<div id="cfList">
			{include file="Settings/LayoutBlockEntries.tpl"}
			</div>
			<table border="0" cellpadding="5" cellspacing="0" width="100%">
				<tr>
					<td class="small" align="right" nowrap="nowrap"><a href="#top">{$MOD.LBL_SCROLL}</a></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<!-- End of Display for field -->
{else}
	<link rel='stylesheet' type='text/css' href='themes/$theme/style.css'>
	<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>
	<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>
		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src="{'denied.gif'|@vtiger_imageurl:$THEME}" ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>{$APP.LBL_PERMISSION}</span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>
		<a href='javascript:window.history.back();'>{$MOD.LBL_GO_BACK}</a><br></td>
		</tr>
		</tbody></table> 
		</div>
		</td></tr></table>
{/if}
</div>
<script language="JavaScript">
	Drag.init(document.getElementById('editpopup_draghandle'), document.getElementById('editpopup'));
	var editpopupobj = fieldExpressionPopup(moduleName, jQuery);
	editpopupobj.setModule(moduleName);
	editpopupobj.close();
</script>
