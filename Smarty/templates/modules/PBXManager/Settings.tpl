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

<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody>
	<tr>
        <td valign="top">
        	<img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}">
        </td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
			<form action="index.php" method="post" id="form">
			<input type='hidden' name='module' value='Users'>
			<input type='hidden' name='action' value='DefModuleView'>
			<input type='hidden' name='return_action' value='ListView'>
			<input type='hidden' name='return_module' value='Users'>
			<input type='hidden' name='parenttab' value='Settings'>
			<br>
			<div align=center>
				{include file='SetMenu.tpl'}
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
					<tr>
						<td width=50 rowspan=3 valign=top><img src="{'Call.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_SOFTPHONE_SERVER_SETTINGS}" width="48" height="38" border=0 title="{$MOD.LBL_SOFTPHONE_SERVER_SETTINGS}"></td>
						<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > {$MOD.LBL_SOFTPHONE_SERVER_SETTINGS}</b></td>
					</tr>
					<tr>
						<td valign=top class="small">{$MOD.LBL_SOFTPHONE_SERVER_SETTINGS_DESCRIPTION}</td>
					</tr>
					<tr>
						<td valign="top" class="small">
							{$ERROR}
						</td>
					</tr>
				</table>
				
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
					<tr>
						<td>
						<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
							<tr>
								<td width='70%'>
								<table border=0 cellspacing=0 cellpadding=5 width=100%>
									<tr>
										<td id='asterisk' class="big" height="20px;" width="75%">
											<strong>{$MOD.ASTERISK_CONFIGURATION}</strong>
										</td>
										<!-- for now only asterisk is there :: later we can add a dropdown here and add settings for all -->
									</tr>
								</table>
								</td>
							</tr>
						</table>

						<span id='AsteriskCustomization' style='display:block'>
							<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
								<tr>
	         	    				<td class="small" valign=top >
	         	    				<table width="100%"  border="0" cellspacing="0" cellpadding="5">
                        			<tr>
                            			<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.ASTERISK_SERVER_IP}</strong></td>
                            			<td width="80%" class="small cellText">
											<input type="text" id="asterisk_server_ip" name="asterisk_server_ip" class="small" style="width:30%" value="{$ASTERISK_SERVER_IP}" title="{$MOD.ASTERISK_SERVER_IP_TITLE}"/>
										</td>
                        			</tr>
			                        <tr>
										<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.ASTERISK_PORT}</strong></td>
                						<td width="80%" class="small cellText">
											<input type="text" id="asterisk_port" name="asterisk_port" class="small" style="width:30%" value="{$ASTERISK_PORT}" title="{$MOD.ASTERISK_PORT_TITLE}"/>
										</td>
									</tr>
                        			<tr>
                            			<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.ASTERISK_USERNAME}</strong></td>
                            			<td width="80%" class="small cellText">
											<input type="text" id="asterisk_username" name="asterisk_username" class="small" style="width:30%" value="{$ASTERISK_USERNAME}" title="{$MOD.ASTERISK_USERNAME_TITLE}"/>
										</td>
                        			</tr>
			                        <tr>
										<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.ASTERISK_PASSWORD}</strong></td>
                						<td width="80%" class="small cellText">
											<input type="password" id="asterisk_password" name="asterisk_password" class="small" style="width:30%" value="{$ASTERISK_PASSWORD}" title="{$MOD.ASTERISK_PASSWORD_TITLE}"/>
										</td>
									</tr>
									<tr>
										<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.ASTERISK_VERSION}</strong></td>
                						<td width="80%" class="small cellText">
                							<select name="asterisk_version" id="asterisk_version" title="{$MOD.ASTERISK_VERSION_TITLE}">                								
												<option value="1.4" {if $ASTERISK_VERSION eq '1.4'} selected {/if}>1.4</option>
												<option value="1.6" {if $ASTERISK_VERSION eq '1.6'} selected {/if}>1.6</option>
                							</select>
										</td>
									</tr>
									<tr>
										<td width="20%" nowrap colspan="2" align ="center">
											<input type="button" name="update" class="crmbutton small create" value="{$MOD.LBL_UPDATE_BUTTON}" onclick="validatefn1('asterisk');" />
											<input type="button" name="cancel" class="crmbutton small cancel" value="{$MOD.LBL_CANCEL_BUTTON}"  onClick="window.history.back();"/>
									    </td>
                        			</tr>
                        			</table>
									</td>
								</tr>                       
                       		</table>
      	        		</span>
                		<!-- asterisk ends :: can add another <span> for another SIP, say asterisk -->
                
						</td>
					</tr>
				</table>
			</div>
		</td>
		<td valign="top">
			<img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}">
		</td>
	</tr>
</tbody>
</form>
</table>

{literal}
<script>

function setSoftphoneDetails(module){
	var asterisk_server_ip = document.getElementById("asterisk_server_ip").value;
	var asterisk_port = document.getElementById("asterisk_port").value;
	var asterisk_username = document.getElementById("asterisk_username").value;
	var asterisk_password = document.getElementById("asterisk_password").value;
	var asterisk_version = $('asterisk_version').value;
	
	if(asterisk_port == ""){
		//port not specified :: so set default
		asterisk_port = "5038";
	}
	$("status").style.display="block";
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=PBXManager&action=PBXManagerAjax&file=UpdatePBXDetails&ajax=true&qserver='+asterisk_server_ip+'&qport='+asterisk_port+'&qusername='+asterisk_username+'&qpassword='+asterisk_password+'&semodule='+module+'&version='+asterisk_version,
			onComplete: function(response) {
				if((response.responseText != '')){
					alert(response.responseText);
				}else{
					window.history.back();	//successfully saved, so go back
				}		
				$("status").style.display="none";
		    }
		}
    );
}

function validatefn1(module){
	var asterisk_server_ip = document.getElementById("asterisk_server_ip").value;
	var asterisk_port = document.getElementById("asterisk_port").value;

	if (!emptyCheck("asterisk_server_ip","Asterisk Server","text")){
		return false;
	}
	if (!emptyCheck("asterisk_username","Asterisk Username","text")){
		return false;
	}
	if (!emptyCheck("asterisk_password","Asterisk Password","text")){
		return false;
	}
	setSoftphoneDetails(module);
}

</script>
{/literal}

