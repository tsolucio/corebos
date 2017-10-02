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
<tbody><tr>
        <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
<br>
	<div align=center>
	
			{include file='SetMenu.tpl'}
		
			<!-- DISPLAY -->
			<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
			<tr>
				<td width="50" rowspan="2" valign="top"><img src="{'notification.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_MODULE_NAME}" width="48" height="48" border=0 title="{$MOD.LBL_MODULE_NAME}"></td>
				<td colspan="2" class="heading2" valign=bottom align="left"><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > {$MOD.NOTIFICATIONSCHEDULERS} </b></td>
				<td rowspan=2 class="small" align=right>&nbsp;</td>
			</tr>
			<tr>
				<td valign=top class="small" align="left">{$MOD.LBL_NOTIF_SCHED_DESCRIPTION}</td>
			</tr>
			</table>
<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
						<tr><td>&nbsp;</td></tr>
				</table>

				<table width="100%" border="0" cellpadding="5" cellspacing="0" class="listTableTopButtons">
                  <tr >

                    <td  style="padding-left:5px;" class="big">{$MOD.NOTIFICATIONSCHEDULERS}</td>
                    <td align="right">&nbsp;</td>
                  </tr>
			  </table>


	
	<div id="notifycontents">
	{include file='Settings/EmailNotificationContents.tpl'}
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
        <td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
   </tr>
</tbody>
</table>
	<div id="editdiv" style="display:none;position:absolute;width:400px;"></div>
{literal}
<script>
function fetchSaveNotify(id)
{
	document.getElementById("editdiv").style.display="none";
	document.getElementById("status").style.display="inline";
	var active = document.getElementById("notify_status").options[document.getElementById("notify_status").options.selectedIndex].value;
	var subject = document.getElementById("notifysubject").value;
	var body = document.getElementById("notifybody").value;
	jQuery.ajax({
			method:"POST",
			url:'index.php?action=SettingsAjax&module=Settings&file=SaveNotification&active='+active+'&notifysubject='+subject+'&notifybody='+body+'&record='+id
	}).done(function(response) {
			if(response.indexOf(":#:FAILURE") > -1) {
				alert(alert_arr.VALID_DATA);
			} else {
				document.getElementById("notifycontents").innerHTML=response;
			}
			document.getElementById("status").style.display="none";
	});
}

function fetchEditNotify(id)
{
	document.getElementById("status").style.display="inline";
	jQuery.ajax({
			method:"POST",
			url:'index.php?action=SettingsAjax&module=Settings&file=EditNotification&record='+id
	}).done(function(response) {
				document.getElementById("status").style.display="none";
				document.getElementById("editdiv").innerHTML=response;
	});
}
</script>
{/literal}
