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
{literal}
<style>
DIV.fixedLay{
	border:3px solid #CCCCCC;
	background-color:#FFFFFF;
	width:500px;
	position:fixed;
	left:250px;
	top:200px;
	display:block;
}
</style>
{/literal}
{literal}
<!--[if lte IE 6]>
<STYLE type=text/css>
DIV.fixedLay {
	POSITION: absolute;
}
</STYLE>
<![endif]-->

{/literal}
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tr>
        <td valign="top"><img src="{$IMAGE_PATH}showPanelTopLeft.gif"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
			<br>
			<div align=center>
	
			{include file='SetMenu.tpl'}

			<!-- DISPLAY -->
			<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
			<tr>
				<td width="50" rowspan="2" valign="top"><img src="{'proxy.gif'|@vtiger_imageurl:$THEME}" alt="{$CMOD.SERVER_CONFIGURATION}" width="48" height="48" border=0 title="{$CMOD.SERVER_CONFIGURATION}"></td>
				<td colspan="2" class="heading2" valign=bottom align="left"><b><a href="index.php?module=Settings&action=ModuleManager&parenttab=Settings">{$MOD.VTLIB_LBL_MODULE_MANAGER}</a> &gt; {$MODULE_LBL} > {$CMOD.SERVER_CONFIGURATION} </b></td>
				<td rowspan=2 class="small" align=right>&nbsp;</td>
			</tr>
			<tr>
				<td valign=top class="small" align="left">{$CMOD.SERVER_CONFIGURATION_DESCRIPTION}</td>
			</tr>
			</table>
			
			<br/>
			<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tableHeading">
			<tr>
				<td style="padding-left:5px;" class="big">{$MOD.SMS_SERVER_CONFIGURATION}</td>                    
				<td valign=top class="small" align="right">
				<input id="_smsserver_add_button_" type="button" class="small create" value="Add New" onclick="_SMSConfigServerFetchEdit('');fnvshobj(this,'editdiv');">
				</td>
			</tr>
			</table>
	
			<div id="_smsservers_">
			{include file='modules/SMSNotifier/SMSConfigServerListContents.tpl'}
			</div>
			
			<table border=0 cellspacing=0 cellpadding=5 width=100% >
			<tr><td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td></tr>
			</table>
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
        <td valign="top"><img src="{$IMAGE_PATH}showPanelTopRight.gif"></td>
   </tr>
</tbody>
</table>

<form method="POST" action="javascript:void(0);">
	<div id="editdiv" class="fixedlay" style="display:none;position:absolute;width:450px;"></div>
</form>

{literal}
<script type="text/javascript">

function _SMSCongiServerShowReqParams(selectBox)
{
	var providers = selectBox.options;
	for(var index = 0; index < providers.length; ++index) {
		var provideropt = providers[index];
		
		if($('paramrows_' + provideropt.value)) {	
			if(provideropt.selected) {
				$('paramrows_' + provideropt.value).show();
			} else {
				$('paramrows_' + provideropt.value).hide();
			}
		}
	}		
}

function _SMSConfigServerSaveForm(form)
{

	if(form.smsserver_provider.value == '') {
		form.smsserver_provider.style.background = '#FFF4BF';
		return false;
	} else {
		form.smsserver_provider.style.background = '#FFFFFF';
	}
	
	if(form.smsserver_username.value == '') {
		form.smsserver_username.className = 'detailedViewTextBoxOn';
		form.smsserver_username.focus();
		return false;
	}
	
	if(form.smsserver_password.value == '') {
		form.smsserver_password.className = 'detailedViewTextBoxOn';
		form.smsserver_password.focus();
		return false;
	}

	$("editdiv").style.display="none";
	var frmvalues = Form.serialize(form);

	$("status").show();
    new Ajax.Request(
    	'index.php',
        {queue: {position: 'end', scope: 'command'},
        	method: 'post',
            postBody: 'action=SMSNotifierAjax&module=SMSNotifier&file=SMSConfigServer&mode=Save&' + frmvalues,
            onComplete: function(response) {
            	$("status").style.display="none";
				$("_smsservers_").innerHTML = response.responseText;
        	}
     	}
	);
}

function _SMSConfigServerDelete(id)
{
	$("editdiv").style.display="none";
	$("status").style.display="inline";
    new Ajax.Request(
    	'index.php',
        {queue: {position: 'end', scope: 'command'},
        	method: 'post',
            postBody: 'action=SMSNotifierAjax&module=SMSNotifier&file=SMSConfigServer&ajax=true&mode=Delete&record='+id,
            onComplete: function(response) {
            	$("status").style.display="none";
				$("_smsservers_").innerHTML = response.responseText;
        	}
     	}
	);
}

function _SMSConfigServerFetchEdit(id)
{
	$("status").style.display="inline";
	new Ajax.Request(
    	'index.php',
        {queue: {position: 'end', scope: 'command'},
        	method: 'post',
            postBody: 'action=SMSNotifierAjax&module=SMSNotifier&file=SMSConfigServer&ajax=true&mode=Edit&record='+id,
            onComplete: function(response) {
            	$("status").style.display="none";
				$("editdiv").innerHTML=response.responseText;
				$("editdiv").show();
            }
        }
	);
}

</script>
{/literal}
