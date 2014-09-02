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

<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/menu.js"></script>
<script language="JavaScript" type="text/javascript" src="modules/LoggingConf/loggUtils.js"></script>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
        <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
	<br>
        {*<!-- Selecting Module to Log -->*}
        <div id="undermodules" class="layerPopup" style="display:none; visibility: visible;width: 100%;height:100%;opacity:0.5 ">
</div><div id="getmodules" class="layerPopup" style="display:none; visibility:visible; left:35%;top:25%; width: 25%; ">

        <form method="POST" id="generatedoc" name="generatedoc">
            <table class="layerHeadingULine" cellpadding="5" width="100%">
                <tr>
                <td class="genHeaderSmall" width="90%" align="left">Please Select a Module!</td>
                <td width="10%" align="right"><img src="themes//images/close.gif" onclick="hide('undermodules');hide('getmodules');" id="closegetmodule" border="0"></td>
                </tr>
            </table>
            <table width="95%" align="center" cellpadding="5">
                <tr><td><table id="showmodules" width="100%"></table></td></tr></table>
        <table width="100%" class="layerPopupTransport" cellpadding="5" align="center">
        <tr><td class="small" align="center">
                <input name='modulefrom' type='hidden'  value='{$MODULE}'>
                <input name='recordval' id='recordval' type='hidden'>
                <input type='button' class='crmbutton small edit' value='Add' onclick="addModuleToLog();"></td></tr>
        </table>
        </form>


        </div>
	<div align=center>

	
		<!-- DISPLAY -->
		<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
		<form action="index.php" method="post" name="new" id="form" onsubmit="VtigerJS_DialogBox.block();">
		<input type="hidden" name="module" value="LoggingConf">
		<input type="hidden" name="parenttab" value="Settings">
		<input type="hidden" name="fld_module" id="fld_module" value="{$DEF_MODULE}">
		{if $MODE neq 'view'}
			<input type="hidden" name="action" value="UpdateLoggingConfiguration">
		{else}
			<input type="hidden" name="action" value="EditLoggingConfiguration">
		{/if}
		<tr>
			<td width=50 rowspan=2 valign=top><img src="{'orgshar.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_MODULE_NAME}" width="48" height="48" border=0 title="{$MOD.LBL_MODULE_NAME}"></td>
			<td colspan=2 class=heading2 valign=bottom><b><a href="index.php?module=LoggingConf&action=index&parenttab=Settings">{$MOD.MODULE_LABEL}</a> </b></td>
			<td rowspan=2 class="small" align=right>&nbsp;</td>
		</tr>
		<tr>
			<td valign=top class="small">
                        {if $MODE neq 'edit'}{$MOD.LBL_VIEW_LOGGING_MAP}
                        {else}{$MOD.LBL_MODIFY_LOGGING_MAP}
                        {/if}
                        </td>
		</tr>
		</table>
		<br>
		<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
		<tr>
			
			<td class="small" align=right>
			{if $MODE neq 'edit'}
				<input name="Edit" type="submit" class="crmButton small edit" value="{$APP.LBL_EDIT_BUTTON}" >
                                <input name="Add" type="button" class="crmButton small edit" value="{$APP.LBL_ADD_BUTTON} {$APP.LBL_MODULE}" onclick="OpenSelectModule();">
			{else}
				<input title="save" accessKey="S" class="crmButton small save" type="button" onclick="saveit()" name="Save" value="{$APP.LBL_SAVE_LABEL}">
				<input name="Cancel" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="crmButton small cancel" type="button" onClick="window.history.back();">
			{/if}
			</td>
		</tr>
		</table>
		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="listTableTopButtons">
                <tr>
        		<td  style="padding-left:5px;" class="big">{$CMOD.LBL_SELECT_SCREEN}&nbsp;
			<select name="Screen" id="Screen" class="detailedViewTextBox" style="width:30%;"  onChange="changemodules(this)">
			{foreach item=module from=$FIELD_INFO}
				{assign var="MODULELABEL" value=$module|@getTranslatedString:$module}
				{if $module == $DEF_MODULE}
					<option selected="selected" value='{$module}'>{$MODULELABEL}</option>
				{else}
					<option value='{$module}' >{$MODULELABEL}</option>
				{/if}
			{/foreach}
			</select>
		    	</td>
	                <td align="right">&nbsp;</td>
                </tr>
		</table>
		{foreach key=module item=info name=allmodules from=$FIELD_LISTS}
		{assign var="MODULELABEL" value=$module}
		{if $APP.$module neq ''}
			{assign var="MODULELABEL" value=$APP.$module}
		{/if}
		{if $module eq $DEF_MODULE}
			<div id="{$module}_fields" style="display:block">
		{else}
			<div id="{$module}_fields" style="display:none">
		{/if}
	 	<table cellspacing=0 cellpadding=5 width=100% class="listTable small">
       		<tr>
			<td colspan="2" class="listRow" valign="top" nowrap>
			<b>{$CMOD.LBL_FIELDS_AVLBL} {$MODULELABEL}</b>
			</td>
		</tr>
		<tr>
                	<td valign=top width="25%" >
		     	<table border=0 cellspacing=0 cellpadding=5 width=100% class=small>
				{foreach item=elements name=groupfields from=$info}
                        	<tr>
					{foreach item=elementinfo name=curvalue from=$elements}
                           		<td class="prvPrfTexture" style="width:20px">&nbsp;</td>
                           		<td width="5%" id="{$smarty.foreach.allmodules.iteration}_{$smarty.foreach.groupfields.iteration}_{$smarty.foreach.curvalue.iteration}">{$elementinfo.1}</td>
                           		<td width="25%" nowrap  onMouseOver="this.className='prvPrfHoverOn',$('{$smarty.foreach.allmodules.iteration}_{$smarty.foreach.groupfields.iteration}_{$smarty.foreach.curvalue.iteration}').className='prvPrfHoverOn'" onMouseOut="this.className='prvPrfHoverOff',$('{$smarty.foreach.allmodules.iteration}_{$smarty.foreach.groupfields.iteration}_{$smarty.foreach.curvalue.iteration}').className='prvPrfHoverOff'">{$elementinfo.0}</td>
					{/foreach}
                         	</tr>
                         	{/foreach}
                     	</table>
			</td>
                </tr>
                </table>
		</div>
		{/foreach}
	</td>
	</tr>
        </table>
	<br>
	<br>
	<table border=0 cellspacing=0 cellpadding=5 width=100% >
		<tr><td class="small" ><div align=right><a href="#top">{$MOD.LBL_SCROLL}</a></div></td></tr>
	</table>
</td>
</tr>
</table>
</td>
</tr>
</form>
</table>
</div>
</td>
        <td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
   </tr>
</tbody>
</table>
<script>
var def_field='{$DEF_MODULE}_fields';
{literal}
function changemodules(selectmodule)
{
	hide(def_field);
	module=selectmodule.options[selectmodule.options.selectedIndex].value;
	document.getElementById('fld_module').value = module;
	def_field = module+"_fields";
	show(def_field);
}
</script>
{/literal}

