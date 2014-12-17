<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 ********************************************************************************/

global $theme;
require_once('modules/Users/Users.php');
require_once('modules/Vtiger/layout_utils.php');
require_once('include/logging.php');

global $app_strings;
global $mod_strings;

$badpassword = $mod_strings["PASSWORD REQUIREMENTS"].'\n----------------------------------------\n';
$badpassword.= $mod_strings["REQUIRED"].':\n~ ';
$badpassword.= $mod_strings["Min. 8 characters"].'\n\n';
$badpassword.= $mod_strings["Contains3of4"].':\n~ ';
$badpassword.= $mod_strings["Min. 1 uppercase"].':\n~ ';
$badpassword.= $mod_strings["Min. 1 lowercase"].':\n~ ';
$badpassword.= $mod_strings["Min. 1 number"].':\n~ ';
$badpassword.= $mod_strings["Min. 1 special character"].'\n';
insert_popup_header($theme);
?>
<script type='text/javascript' src="include/js/general.js"></script>
<script type='text/javascript' language='JavaScript'>
function set_password(form) {
	if (form.is_admin.value == 1 && trim(form.old_password.value) == "") {
		alert("<?php echo $mod_strings['ERR_ENTER_OLD_PASSWORD']; ?>");
		return false;
	}
	if (trim(form.new_password.value) == "") {
		alert("<?php echo $mod_strings['ERR_ENTER_NEW_PASSWORD']; ?>");
		return false;
	}
	if (trim(form.confirm_new_password.value) == "") {
		alert("<?php echo $mod_strings['ERR_ENTER_CONFIRMATION_PASSWORD']; ?>");
		return false;
	}
	//Check Password
	var passwordOK = passwordChecker(form);
	if(passwordOK) { //Complex Password is ok
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
		}
		else {
			alert("<?php echo $mod_strings['ERR_REENTER_PASSWORDS']; ?>");
			return false;
		}
	} else { //Password is not ok
		alert("<?php echo $badpassword;?>");
	}
}
function passwordChecker(form) {
	//New Password Value
	var passwordValue = trim(form.new_password.value);
	//Length Password
	var passwordLength = (passwordValue.length);
	//alert("Length: " + passwordLength);
	//Capital?
	var containsCapital = checkCapital(passwordValue);
	//alert("Capital: " + containsCapital);
	//Lower?
	var containsLower = checkLower(passwordValue);
	//alert("Lower: " + containsLower);
	//Number?
	var containsNumber = checkNumber(passwordValue);
	//alert("number: " + containsNumber);
	//Special Char?
	var containsSpecialChar = checkSpecialChar(passwordValue);
	//alert("Special Char:" + containsSpecialChar);

	//COMPLEX PASSWORD: Minimum 8 characters, and three of the four conditions needs to be ok --> Capital, Lowercase, Special Character, Number
	if(passwordLength < 8) {
		return false;
	} else {
		//Combination Match All
		if((containsNumber == true)&&(containsCapital == true)&&(containsLower == true)&&(containsSpecialChar == true)) {
			return true;
		} else {
			//Combination 1
			if((containsNumber == true)&&(containsCapital == true)&&(containsLower == true)) {
				return true;
			} else {
				//Combination 2
				if((containsCapital == true)&&(containsLower == true)&&(containsSpecialChar == true)) {
					return true;
				} else {
					//Combination 3
					if((containsLower == true)&&(containsSpecialChar == true)&&(containsNumber == true)) {
						return true;
					} else {
						//Combination 4
						if((containsNumber == true)&&(containsCapital == true)&&(containsSpecialChar == true)) {
							return true;
						} else {
							return false;
						}
					}
				}
			}
		}
	}
}
//Check for special character
function checkSpecialChar(passwordValue) {
	var i=0;
	var ch='';
	while (i <= passwordValue.length) {
		character = passwordValue.charAt(i);
		if ((character == ".")||(character =="!")||(character =="?")||(character ==",")||(character ==";")||(character =="-")||(character =="@")||(character =="#")){
			return true;
		}
		i++;
	}
	return false;
}
//check for number
function checkNumber(passwordValue) {
	var i=0;
	while (i < passwordValue.length){
		var character = passwordValue.charAt(i);
		if (!isNaN(character)){
			return true;
		}
		i++;
	}
	return false;
}
//Check for lowercase character
function checkLower(passwordValue) {
	var i=0;
	while (i < passwordValue.length) {
		var character = passwordValue.charAt(i);
		if (character == character.toLowerCase()){
			return true;
		}
		i++;
	}
	return false;
}
//Check for capital
function checkCapital(passwordValue) {
	var i=0;
	while (i < passwordValue.length) {
		var character = passwordValue.charAt(i);
		if (character == character.toUpperCase()) {
			return true;
		}
		i++;
	}
	return false;
}
</script>
<form name="ChangePassword" onsubmit="VtigerJS_DialogBox.block();">
<?php echo get_form_header($mod_strings['LBL_CHANGE_PASSWORD'], "", false); ?>
<table width='100%' cellspacing='0' cellpadding='5' border='0' class="small">
<tr>
	<td class="detailedViewHeader" colspan="2"><b><?php echo $mod_strings['LBL_CHANGE_PASSWORD']; ?></b></td>
</tr>
<?php if (!is_admin($current_user)) {
	echo "<tr>";
	echo "<td width='40%' class='dvtCellLabel' align='right'><b> ".$mod_strings['LBL_OLD_PASSWORD']."</b></td>\n";
	echo "<td width='60%' class='dvtCellInfo'><input name='old_password' type='password' tabindex='1' size='15'></td>\n";
	echo "<input name='is_admin' type='hidden' value='1'>";
	echo "</tr><tr>\n";
}
else echo "<input name='old_password' type='hidden'><input name='is_admin' type='hidden' value='0'>";
?>
<td width='40%' class='dvtCellLabel' nowrap align="right"><b><?php echo $mod_strings['LBL_NEW_PASSWORD']; ?></b></td>
<td width='60%' class='dvtCellInfo'><input name='new_password' type='password' tabindex='1' size='15'></td>
</tr><tr>
<td width='40%' class='dvtCellLabel' nowrap align="right"><b><?php echo $mod_strings['LBL_CONFIRM_PASSWORD']; ?></b></td>
<td width='60%' class='dvtCellInfo'><input name='confirm_new_password' type='password' tabindex='1' size='15'></td>
</tr><tr>
<td width='40%' class='dataLabel'></td>
<td width='60%' class='dvtCellInfo'></td>
</td></tr>
</table>
<br>
<table width='100%' cellspacing='0' cellpadding='1' border='0'>
<tr>
<td align='right'><input title='<?php echo $app_strings['LBL_SAVE_BUTTON_TITLE']; ?>' accessKey='<?php echo $app_strings['LBL_SAVE_BUTTON_KEY']; ?>' class='crmbutton small save' LANGUAGE=javascript onclick='if (set_password(this.form)) window.close(); else return false;' type='submit' name='button' value='  <?php echo $app_strings['LBL_SAVE_BUTTON_LABEL']; ?>  '></td>
<td align='left'><input title='<?php echo $app_strings['LBL_CANCEL_BUTTON_TITLE']; ?>' accessyKey='<?php echo $app_strings['LBL_CANCEL_BUTTON_KEY']; ?>' class='crmbutton small cancel' LANGUAGE=javascript onclick='window.close()' type='submit' name='button' value='  <?php echo $app_strings['LBL_CANCEL_BUTTON_LABEL']; ?>  '></td>
</tr>
</table>
</form>
<script language="JavaScript">
document.ChangePassword.new_password.focus();
</script>
<br>
