<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'modules/Users/Users.php';
require_once 'include/logging.php';

global $theme, $app_strings, $mod_strings, $default_charset;

$badpassword = $mod_strings['PASSWORD REQUIREMENTS'].'<br>----------------------------------------<br>';
$badpassword.= $mod_strings['REQUIRED'].':<br>~ ';
$badpassword.= $mod_strings['Min. 8 characters'].'<br><br>';
$badpassword.= $mod_strings['Contains3of4'].'<br>~ ';
$badpassword.= $mod_strings['Min. 1 uppercase'].'<br>~ ';
$badpassword.= $mod_strings['Min. 1 lowercase'].'<br>~ ';
$badpassword.= $mod_strings['Min. 1 number'].'<br>~ ';
$badpassword.= $mod_strings['Min. 1 special character'].'<br>';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><HEAD>
<meta http-equiv="Content-Type" content="text/html; charset='<?php echo $default_charset; ?>'">
<title><?php echo $app_strings['LBL_BROWSER_TITLE']; ?></title>
<style type="text/css">@import url("themes/<?php echo $theme; ?>/style.css"); </style>
</HEAD><BODY leftMargin="5" topMargin="5" MARGINHEIGHT="0" MARGINWIDTH="0">
<link REL="SHORTCUT ICON" HREF="themes/images/blank.gif">
<script type='text/javascript' src="include/js/general.js"></script>
<script type='text/javascript' src="include/js/PasswordManagement.js"></script>
<script type='text/javascript'>
function set_password(form) {
	var errmsg = document.getElementById('chgpasserrmsg');
	if (trim(form.old_password.value) == trim(form.new_password.value)) {
		errmsg.innerHTML = "<?php echo $mod_strings['ERR_PASSWORD_NOT_CHANGED']; ?>";
		errmsg.style.display = 'block';
		return false;
	}

	if (form.is_admin.value == 1 && trim(form.old_password.value) == "") {
		errmsg.innerHTML = "<?php echo $mod_strings['ERR_ENTER_OLD_PASSWORD']; ?>";
		errmsg.style.display = 'block';
		return false;
	}
	if (trim(form.new_password.value) == "") {
		errmsg.innerHTML = "<?php echo $mod_strings['ERR_ENTER_NEW_PASSWORD']; ?>";
		errmsg.style.display = 'block';
		return false;
	}
	if (trim(form.confirm_new_password.value) == "") {
		errmsg.innerHTML = "<?php echo $mod_strings['ERR_ENTER_CONFIRMATION_PASSWORD']; ?>";
		errmsg.style.display = 'block';
		return false;
	}
	//Check Password
	var passwordOK = corebos_Password.passwordChecker(form.new_password.value);
	if (passwordOK) { //Complex Password is ok
		if (trim(form.new_password.value) == trim(form.confirm_new_password.value)) {
			if (form.is_admin.value == 1) window.opener.document.DetailView.old_password.value = form.old_password.value;
			window.opener.document.DetailView.new_password.value = form.new_password.value;
			window.opener.document.DetailView.return_module.value = 'Users';
			window.opener.document.DetailView.return_action.value = 'DetailView';
			window.opener.document.DetailView.changepassword.value = 'true';
			window.opener.document.DetailView.return_id.value = window.opener.document.DetailView.record.value;
			window.opener.document.DetailView.action.value = 'Save';
			window.opener.document.DetailView.submit();
			return true;
		} else {
			errmsg.innerHTML = "<?php echo $mod_strings['ERR_REENTER_PASSWORDS']; ?>";
			errmsg.style.display = 'block';
			return false;
		}
	} else { //Password is not ok
		errmsg.innerHTML = "<?php echo $badpassword; ?>";
		errmsg.style.display = 'block';
		return false;
	}
}
</script>
<div class="cb-alert-danger" id="chgpasserrmsg" style="display: none;"></div>
<form name="ChangePassword" onsubmit="VtigerJS_DialogBox.block();">
<table width='100%' cellspacing='0' cellpadding='5' border='0' class="small">
<tr>
	<td class="detailedViewHeader" colspan="3"><b><?php echo $mod_strings['LBL_CHANGE_PASSWORD']; ?></b></td>
</tr>
<?php
if (!is_admin($current_user)) {
	echo "<tr>";
	echo "<td width='20%' class='dvtCellLabel' align='right'><b> ".$mod_strings['LBL_OLD_PASSWORD']."</b></td>\n";
	echo "<td width='50%' class='dvtCellInfo'><input name='old_password' type='password' tabindex='1' size='15'></td>\n";
	echo "<td width='30%' class='dvtCellInfo'></td>\n";
	echo "</tr>\n";
}
?>
<tr>
<td width='20%' class='dvtCellLabel' nowrap align="right"><b><?php echo $mod_strings['LBL_NEW_PASSWORD']; ?></b></td>
<td width='50%' class='dvtCellInfo'><input name='new_password' type='password' tabindex='1' size='15'></td>
<td width='30%' class='dvtCellInfo'>
<?php if (!is_admin($current_user)) { ?>
	<input name='is_admin' type='hidden' value='1'>
<?php } else { ?>
	<input name='old_password' type='hidden'><input name='is_admin' type='hidden' value='0'>
<?php } ?>
	<input type=button value='<?php echo $mod_strings['Generate password']; ?>' onClick='document.ChangePassword.new_password.value = corebos_Password.getPassword(12, true, true, true, true, false, true, true, true, false);  document.ChangePassword.confirm_new_password.value = document.ChangePassword.new_password.value;document.getElementById("rndpasswordshow").innerHTML=document.ChangePassword.new_password.value;'>
</td>
</tr><tr>
<td width='20%' class='dvtCellLabel' nowrap align="right"><b><?php echo $mod_strings['LBL_CONFIRM_PASSWORD']; ?></b></td>
<td width='50%' class='dvtCellInfo'><input name='confirm_new_password' type='password' tabindex='1' size='15'></td>
<td width='30%' class='dvtCellInfo' id='rndpasswordshow' style="font-size: large;"></td>
</tr>
</table>
<br>
<table width='100%' cellspacing='0' cellpadding='1' border='0'>
<tr>
<td align='right'><input title='<?php echo $app_strings['LBL_SAVE_BUTTON_TITLE']; ?>' accessKey='<?php echo $app_strings['LBL_SAVE_BUTTON_KEY']; ?>' class='crmbutton small save' onclick='if (set_password(this.form)) window.close(); else return false;' type='submit' name='button' value='  <?php echo $app_strings['LBL_SAVE_BUTTON_LABEL']; ?>  '></td>
<td align='left'><input title='<?php echo $app_strings['LBL_CANCEL_BUTTON_TITLE']; ?>' accessyKey='<?php echo $app_strings['LBL_CANCEL_BUTTON_KEY']; ?>' class='crmbutton small cancel' onclick='window.close()' type='submit' name='button' value='  <?php echo $app_strings['LBL_CANCEL_BUTTON_LABEL']; ?>  '></td>
</tr>
</table>
</form>
<script>
<?php
if (is_admin($current_user)) {
	echo 'document.ChangePassword.new_password.focus();';
} else {
	echo 'document.ChangePassword.old_password.focus();';
}
?>
</script>
<br>
</BODY>
</HTML>