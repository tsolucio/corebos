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
<br>
{include file='applicationmessage.tpl'}
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
	<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
<br>
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{'backupserver.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" width="48" height="48" border=0 title="{$MOD.LBL_USERS}"></td>
					<td class=heading2 valign=bottom><b>{$MOD.LBL_BACKUP_SERVER_SETTINGS}</b></td>
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_BACKUP_SERVER_DESC} </td>
				</tr>
				</table>
				<form action="index.php" method="post" name="tandd" onsubmit="VtigerJS_DialogBox.block();">
				<input type="hidden" name="server_type" value="local_backup">
				<input type="hidden" name="module" value="VtigerBackup">
				<input type="hidden" name="action" value="index">
				<input type="hidden" name="local_server_mode">
				<table border=0 cellspacing=0 cellpadding=0 width=100% class="tableHeading">
					<tr>
						<td class="big" height="40px;" width="70%">&nbsp;&nbsp;<strong>{$MOD.LBL_BACKUP_SERVER_SETTINGS}</strong></td>
						<td class="small cellText" align="center" width="30%">&nbsp;
							<span id="view_info" class="crmButton small cancel" style="display:none;"></span>
						</td>
					</tr>
				</table>
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="listRow">
					<tr>
						<td class="small" valign=top >
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr height='30px'>
									<td width="20%" class="cellLabel"><strong>{$MOD.LBL_ENABLE} {$MOD.LBL_BACKUP_SERVER_SETTINGS} ({$MOD.LBL_LOCAL})</strong>
									</td>
									<td width='60%' class="small cellText">
										{if $LOCAL_BACKUP_STATUS eq 'enabled'}
											<input type="checkbox" checked name="enable_local_backup" onclick="localbackupenabled(this)">
										{else}
											<input type="checkbox" name="enable_local_backup" onclick="localbackupenabled(this)">
										{/if}
									</td>
									<td class="small cellText" align=right width=20%>
									{if $LOCAL_BACKUP_STATUS eq 'enabled'}
										<span id='localbackup_buttons' style="display:block;">
									{else}
										<span id='localbackup_buttons' style="display:none;">
									{/if}
										<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr height='30px'>
											{if $LOCAL_SERVER_MODE eq 'edit'}
												<td width="10%" align='right' class="small cellText" colspan=2>
													<input title="{$APP.LBL_SAVE_BUTTON_LABEL}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmButton small save" type="submit" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" onclick="this.form.action.value='Save'; return validate('Local')">&nbsp;&nbsp;
												</td>
											{else}
												<td width="20%" align='right' class="small cellText">
													<input title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="crmButton small edit" onclick="this.form.action.value='BackupServerConfig';this.form.local_server_mode.value='edit'" type="submit" name="Edit" value="{$APP.LBL_EDIT_BUTTON_LABEL}">&nbsp;
													{if $LOCAL_SERVER_MODE neq 'edit'}
														<input title="{$APP.LBL_SAVE_BUTTON_LABEL}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmButton small save" type="submit" name="backupnow" value="{$MOD.LBL_BACKUP} {$APP.LBL_NOW}" onclick="this.form.action.value='BackupServerConfig';" >&nbsp;&nbsp;
													{else}
														<input title="{$APP.LBL_SAVE_BUTTON_LABEL}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmButton small save" type="submit" name="backupnow" value="{$MOD.LBL_BACKUP} {$APP.LBL_NOW}" onclick="this.form.action.value='BackupServerConfig';" style='visibility:none;' disabled='true'>&nbsp;&nbsp;
													{/if}
												</td>
											{/if}
											</tr>
										</table>
										</span>
									</td>
								</tr>
								<tr width='100%'>
									<td colspan=3>
									{if $LOCAL_BACKUP_STATUS eq 'enabled'}
										<div id='localbackup_fields' style="display:block;">
									{else}
										<div id='localbackup_fields' style="display:none;">
									{/if}
										<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr height='30px'>
											<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_BACKUP_LOCATION}</strong></td>
											{if $LOCAL_SERVER_MODE eq 'edit'}
												<td width="80%" colspan=3>&nbsp;<input type="text" size=80 class="detailedViewTextBox small" value="{$SERVER_BACKUP_PATH}" name="server_path" /></strong>
													{if !empty($ERROR_STR)}<p style="padding:6px;margin-left:10px;">{$ERROR_STR}</p>{/if}
												</td>
											{else}
												<td width="80%" class="small cellText">
													&nbsp;{$SERVER_BACKUP_PATH}
												</td>
											{/if}
											</tr>
										</table>
										</div>
									</td>
								</tr>
							</table>
						</form>
								<table width="100%" border="0" cellspacing="0" cellpadding="5">
								<tr>
									<td class="small" valign="top"><br>{$MOD.LBL_BACKUP_DESC}</td>
								</tr>
								</table>
						</td>
					</tr>
				<form action="index.php" method="post" name="tandc" onsubmit="VtigerJS_DialogBox.block();">
				<input type="hidden" name="server_type" value="ftp_backup">
				<input type="hidden" name="module" value="VtigerBackup">
				<input type="hidden" name="action" value="index">
				<input type="hidden" name="bkp_server_mode">
				<tr>
						<td class="small" valign=top >
							<table width="100%" border="0" cellspacing="0" cellpadding="5" class="tableHeading">
								<tr>
									<td nowrap class="small cellLabel"><strong>{$MOD.LBL_ENABLE} {$MOD.LBL_BACKUP_SERVER_SETTINGS} ({$MOD.LBL_FTP})</strong></td>
									<td width="50%" class="small cellText">
										{if $FTP_BACKUP_STATUS eq 'enabled'}
											<input type="checkbox" checked name="enable_ftp_backup" onclick="backupenabled(this)">
										{else}
											<input type="checkbox" name="enable_ftp_backup" onclick="backupenabled(this)">
										{/if}
									</td>
									<td class="small cellText" align="right" width="30%">&nbsp;
										{if $FTP_BACKUP_STATUS eq 'enabled'}
										<div id='ftp_buttons' style="display:block;">
										{else}
										<div id='ftp_buttons' style="display:none;">
										{/if}
										{if $BKP_SERVER_MODE neq 'edit'}
											<input title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="crmButton small edit" onclick="this.form.action.value='BackupServerConfig';this.form.bkp_server_mode.value='edit'" type="submit" name="Edit" value="{$APP.LBL_EDIT_BUTTON_LABEL}">&nbsp;
											<input title="{$MOD.LBL_CLEAR_DATA}" accessKey="{$MOD.LBL_CLEAR_DATA}" class="crmButton small cancel" onclick="document.tandc.enable_ftp_backup.checked=false;clearBackupServer(this);" type="button" name="Clear" value="{$MOD.LBL_CLEAR_DATA}">
										{else}
											<input title="{$APP.LBL_SAVE_BUTTON_LABEL}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmButton small save" type="submit" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" onclick="this.form.action.value='Save'; return validate('FTP')">&nbsp;&nbsp;
											<input title="{$APP.LBL_CANCEL_BUTTON_LABEL}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmButton small cancel" onclick="backupenable_check()" type="button" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}">
										{/if}
										</div>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td class="small" valign=top>
						{if $FTP_BACKUP_STATUS eq 'enabled'}
							<div id='bckcontents' style="display:block;">
						{else}
							<div id='bckcontents' style="display:none;">
						{/if}
								<table border=0 cellspacing=0 cellpadding=0 width=100% >
									<tr>
										<td>
											<div id="BackupServerContents">
												{include file="modules/VtigerBackup/BackupServerContents.tpl"}
											</div>
										</td>
									</tr>
								</table>
							</div>
						</td>
					</tr>
				</table>
		</td>
		</tr>
		</form>
		</table>
		</td></tr>
		</table>
	</div>
	</td>
</tr>
</tbody>
</table>
{literal}
<script>
function validate(type) {
	if (type == 'FTP') {
		if (!emptyCheck('server', 'FTP {/literal}{$MOD.LBL_SERVER_ADDRESS}{literal}', 'text')) {
			return false;
		}
		if (!emptyCheck('server_username', 'FTP {/literal}{$MOD.LBL_USERNAME}{literal}', 'text')) {
			return false;
		}
		if (!emptyCheck('server_password', 'FTP {/literal}{$MOD.LBL_PASWRD}{literal}', 'text')) {
			return false;
		}
		return true;
	}
	if (type == 'Local') {
		if (!emptyCheck('server_path', '{/literal}{$MOD.LBL_BACKUP_LOCATION}{literal}', 'text')) {
			return false;
		} else {
			return true;
		}
	}
}

function clearBackupServer(Obj) {
	jQuery.ajax({
		method:'POST',
		url:'index.php?module=VtigerBackup&action=VtigerBackupAjax&ajax=true&file=BackupServerConfig&opmode=del'
	}).done(function(response) {
		document.getElementById('BackupServerContents').innerHTML=response;
	});
	backupenabled(Obj);
}

function backupenabled(ochkbox) {
	if (ochkbox.checked == true) {
		document.getElementById('bckcontents').style.display='block';
		var status='enabled';
		document.getElementById('view_info').innerHTML = alert_arr.MSG_FTP_BACKUP_ENABLED+', ' + alert_arr.MSG_CONFIRM_FTP_DETAILS;
		document.getElementById('view_info').style.display = 'block';
		document.getElementById('ftp_buttons').style.display='block';

		jQuery.ajax({
			method:'POST',
			url:'index.php?module=VtigerBackup&action=VtigerBackupAjax&file=SaveEnableBackup&ajax=true&GetBackupDetail=true&servertype=ftp_backup'
		}).done(function(response) {
			if (response.indexOf('FAILURE') > -1) {
				document.location.href = 'index.php?module=VtigerBackup&action=BackupServerConfig&bkp_server_mode=edit';
				return false;
			}
		});
	} else {
		document.getElementById('bckcontents').style.display='none';
		var status = 'disabled';
		document.getElementById('view_info').innerHTML = alert_arr.MSG_FTP_BACKUP_DISABLED;
		document.getElementById('view_info').style.display = 'block';
		document.getElementById('ftp_buttons').style.display='none';
	}

	jQuery.ajax({
		method:"POST",
		url:'index.php?module=VtigerBackup&action=VtigerBackupAjax&file=SaveEnableBackup&ajax=true&enable_ftp_backup='+status
	}).done(function(response) {
		document.getElementById('status').style.display='none';
	});
	setTimeout("hide('view_info')",3000);
}

function backupenable_check() {
	jQuery.ajax({
		method:'POST',
		url:'index.php?module=VtigerBackup&action=VtigerBackupAjax&file=SaveEnableBackup&ajax=true&GetBackupDetail=true&servertype=ftp_backup'
	}).done(function(response) {
		if(response.indexOf('FAILURE') > -1) {
			document.forms['tandc'].enable_ftp_backup.checked = false;
			backupenabled(document.tandc.enable_ftp_backup);
			window.history.back();
		}
	});
}

function localbackupenabled(ochkbox) {
	if (ochkbox.checked == true) {
		document.getElementById('localbackup_buttons').style.display='block';
		document.getElementById('localbackup_fields').style.display='block';
		var status='enabled';
		document.getElementById('view_info').innerHTML = alert_arr.MSG_LOCAL_BACKUP_ENABLED+', '+alert_arr.MSG_CONFIRM_PATH;
		document.getElementById('view_info').style.display = 'block';
		jQuery.ajax({
			method:'POST',
			url:'index.php?module=VtigerBackup&action=VtigerBackupAjax&file=SaveEnableBackup&ajax=true&GetBackupDetail=true&servertype=local_backup'
		}).done(function(response) {
			if (response.indexOf('FAILURE') > -1) {
				document.location.href = 'index.php?module=VtigerBackup&action=BackupServerConfig&local_server_mode=edit';
				return false;
			}
		});
	} else {
		document.getElementById('localbackup_buttons').style.display='none';
		document.getElementById('localbackup_fields').style.display='none';
		var status = 'disabled';
		document.getElementById('view_info').innerHTML = alert_arr.MSG_LOCAL_BACKUP_DISABLED;
		document.getElementById('view_info').style.display = 'block';
	}
	jQuery.ajax({
		method:'POST',
		url:'index.php?module=VtigerBackup&action=VtigerBackupAjax&file=SaveEnableBackup&ajax=true&enable_local_backup='+status
	}).done(function(response) {
		document.getElementById("status").style.display="none";
	});
	setTimeout("hide('view_info')",3000);
}
</script>
{/literal}
