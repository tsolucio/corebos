<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
global $current_user,$mod_strings,$app_strings, $theme;
require_once 'Smarty_setup.php';
require_once 'include/database/PearDatabase.php';
require_once 'modules/cbCalendar/CalendarCommon.php';
require_once 'modules/Calendar4You/Calendar4You.php';
$smarty = new vtigerCRM_Smarty;
$c_mod_strings = return_module_language($current_language, 'cbCalendar');
$users_mod_strings = return_module_language($current_language, 'Users');
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('CMOD', $c_mod_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('USERID', $current_user->id);
require_once 'modules/Calendar4You/GoogleSync4You.php';
$userDetails = getSharingUserName($current_user->id);
$shareduser_ids = getSharedUserId($current_user->id);
$Calendar4You = new Calendar4You();
$Calendar_Settings = $Calendar4You->getSettings();
$Days_Values = array('Sunday' => $c_mod_strings['LBL_DAY0'], 'Monday' => $c_mod_strings['LBL_DAY1']);
ob_start();
$smarty->assign(
	'ICON',
	array(
		'title' => $c_mod_strings['LBL_TIMESETTINGS'],
		'size' => 'small',
		'library' => 'utility',
		'icon' => 'clock',
	)
);
$smarty->assign('PAGESUBTITLE', $smarty->fetch('Components/Icon.tpl').'&nbsp;'.$c_mod_strings['LBL_TIMESETTINGS']);
$smarty->display('Components/PageSubTitle.tpl');
?>
<div class="slds-grid slds-wrap">
<div class="slds-col slds-size_1-of-2">
<div class="slds-form-element slds-form-element_horizontal slds-m-bottom_none">
	<label class="slds-form-element__label" for="activity_view"><?php echo $users_mod_strings['LBL_ACTIVITY_VIEW']; ?></label>
	<div class="slds-form-element__control">
		<select name="activity_view" id="activity_view" <?php echo $current_user->start_hour == '' ? 'disabled' : ''; ?> class="slds-select slds-truncate_container_50">
			<option value="Today" <?php echo ($current_user->activity_view == 'Today' ? 'selected' : ''); ?>><?php echo $app_strings['Today']?></option>
			<option value="This Week" <?php echo $current_user->activity_view == 'This Week' ? 'selected' : '';?>><?php echo $app_strings['This Week']?></option>
			<option value="This Month" <?php echo $current_user->activity_view=='This Month' ? 'selected' : '';?>><?php echo $app_strings['This Month']?></option>
		</select>
	</div>
</div>
</div>
<div class="slds-col slds-size_1-of-2">
	<div class="slds-form-element slds-form-element_stacked slds-m-bottom_none">
	<div class="slds-form-element__control">
		<div class="slds-checkbox">
			<input type="checkbox" name="sttime_check" id="sttime_check" <?php echo $current_user->start_hour != '' ? 'checked' : ''; ?> />
			<label class="slds-checkbox__label" for="sttime_check">
				<span class="slds-checkbox_faux"></span>
				<span class="slds-form-element__label">
					<?php echo $c_mod_strings['LBL_CALSTART']; ?>
					<select name="start_hour" id="start_hour" <?php echo $current_user->start_hour == '' ? 'disabled' : ''; ?> class="slds-select slds-truncate_container_25">
					<?php
					for ($i=0; $i <= 23; $i++) {
						if ($i == 0) {
							$hour = '12:00 am';
						} elseif ($i >= 12) {
							if ($i == 12) {
								$hour = $i;
							} else {
								$hour = $i - 12;
							}
							$hour = $hour.':00 pm';
						} else {
							$hour = $i.':00 am';
						}
						if ($i <= 9 && strlen(trim($i)) < 2) {
							$value = '0'.$i.':00';
						} else {
							$value = $i.':00';
						}
						if ($value === $current_user->start_hour) {
							$selected = 'selected';
						} else {
							$selected = '';
						}
						echo "<option $selected value=\"$value\">$hour</option>";
					}
					?>
					</select>
				</span>
			</label>
		</div>
	</div>
	</div>
</div>
<div class="slds-col slds-size_1-of-2">
<div class="slds-form-element slds-form-element_horizontal slds-m-bottom_none">
	<label class="slds-form-element__label" for="dayoftheweek"><?php echo $mod_strings['LBL_WEEK_STARTS_AT']; ?></label>
	<div class="slds-form-element__control">
		<select name="dayoftheweek" id="dayoftheweek" <?php echo $current_user->start_hour == '' ? 'disabled' : ''; ?> class="slds-select slds-truncate_container_50">
		<?php
		foreach ($Days_Values as $day_key => $day_label) {
			if ($Calendar_Settings["dayoftheweek"] == $day_key) {
				$sel = 'selected';
			} else {
				$sel = '';
			}
			echo '<option value="'.$day_key.'" '.$sel.'>'.$day_label.'</option>';
		}
		?>
		</select>
	</div>
</div>
</div>
<div class="slds-col slds-size_1-of-2">
	<div class="slds-form-element slds-form-element_stacked slds-m-bottom_none">
	<div class="slds-form-element__control">
		<div class="slds-checkbox">
			<input type="checkbox" name="hour_format" id="hour_format" <?php echo $current_user->hour_format == '24' ? 'checked' : ''; ?> value="24">
			<label class="slds-checkbox__label" for="hour_format">
				<span class="slds-checkbox_faux"></span>
				<span class="slds-form-element__label">
					<?php echo $c_mod_strings['LBL_USE24']; ?>
				</span>
			</label>
		</div>
	</div>
	</div>
</div>
<div class="slds-col slds-size_1-of-2">
<div class="slds-form-element slds-form-element_horizontal slds-m-bottom_none">
	<label class="slds-form-element__label" for="user_view"><?php echo $mod_strings['LBL_DEFAULT_USER_VIEW']; ?></label>
	<div class="slds-form-element__control">
		<select name="user_view" id="user_view" <?php echo $current_user->start_hour == '' ? 'disabled' : ''; ?> class="slds-select slds-truncate_container_50">
			<option value="me" <?php echo $Calendar_Settings['user_view'] == 'me' ? 'selected' : ''; ?>><?php echo $mod_strings['LBL_ME']; ?></option>
			<option value="all" <?php echo $Calendar_Settings['user_view'] == 'all' ? 'selected' : ''; ?>><?php echo $mod_strings['LBL_ALL_USERS']; ?></option>
		</select>
	</div>
</div>
</div>
<div class="slds-col slds-size_1-of-2">
<div class="slds-form-element slds-form-element_stacked slds-m-bottom_none">
	<div class="slds-form-element__control">
		<div class="slds-checkbox">
			<input type="checkbox" name="show_weekends" id="show_weekends" <?php echo $Calendar_Settings['show_weekends'] == 'true' ? 'checked' : ''; ?> value="1">
			<label class="slds-checkbox__label" for="show_weekends">
				<span class="slds-checkbox_faux"></span>
				<span class="slds-form-element__label">
					<?php echo $mod_strings['LBL_SHOW_WEEKENDS']; ?>
				</span>
			</label>
		</div>
	</div>
</div>
</div>
</div>
<?php
$smarty->assign(
	'ICON',
	array(
		'title' => $c_mod_strings['LBL_CALSHARE'],
		'size' => 'small',
		'library' => 'utility',
		'icon' => 'share',
	)
);
$smarty->assign('PAGESUBTITLE', $smarty->fetch('Components/Icon.tpl').'&nbsp;'.$c_mod_strings['LBL_CALSHARE']);
$smarty->display('Components/PageSubTitle.tpl');
echo $c_mod_strings['LBL_CALSHAREMESSAGE'];
?>
<div id="cal_shar" class="slds-grid">
	<div class="slds-col slds-size_2-of-5">
		<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="available_users">
		<?php
			echo $c_mod_strings['LBL_AVL_USERS'].'&nbsp';
			$smarty->display('modules/cbCalendar/AvailableUserTip.tpl');
		?>
		</label>
		<select name="available_users" id="available_users" class=small size=5 multiple style="height:90px;width:100%">
		<?php
		foreach ($userDetails as $id => $name) {
			if ($id != '') {
				echo '<option value="'.$id.'">'.$name.'</option>';
			}
		}
		?>
		</select>
	</div>
	<div class="slds-col slds-size_1-of-5 slds-m-top_large">
		<button class="slds-button slds-button_brand slds-m-top_medium" name="shrusr" onClick="incUser('available_users', 'selected_users')" type="button" style="width:75%;">
			<?php echo $c_mod_strings['LBL_ADD_BUTTON']; ?> >>
		</button>
		<br>
		<button class="slds-button slds-button_destructive slds-m-top_xx-small" name="rmvusr" onClick="rmvUser('selected_users');" type="button" style="width:75%;">
			<< <?php echo $c_mod_strings['LBL_RMV_BUTTON']; ?>
		</button>
	</div>
	<div class="slds-col slds-size_2-of-5">
	<label class="slds-form-element__label slds-page-header__meta-text slds-m-top_x-small" for="available_users">
		<?php
			echo $c_mod_strings['LBL_SEL_USERS'].'&nbsp';
			$smarty->assign('TOOLTIPLEFT', 1);
			$smarty->display('modules/cbCalendar/UnselectUserTip.tpl');
		?>
		</label>
		<select name="selected_users" id="selected_users" class="small" size=5 multiple style="height:90px;width:100%">
		<?php
		foreach ($shareduser_ids as $shar_id => $share_user) {
			if ($shar_id != '') {
				echo '<option value="'.$shar_id.'">'.$share_user.'</option>';
			}
		}
		?>
		</select>
	</div>
</div>
<?php
$smarty->assign(
	'ICON',
	array(
		'title' => $mod_strings['LBL_GOOGLE_SYNC_ACCESS_DATA'],
		'size' => 'small',
		'library' => 'utility',
		'icon' => 'share',
	)
);
$smarty->assign(
	'PAGESUBTITLE',
	'<img src="themes/images/GoogleCalendar.png">&nbsp;'.$mod_strings['LBL_GOOGLE_SYNC_ACCESS_DATA'].' &quot;'.trim($current_user->first_name.' '.$current_user->last_name).'&quot;'
);
$smarty->display('Components/PageSubTitle.tpl');
?>
<div class="slds-form slds-m-top_x-small">
<div>
	<?php echo $mod_strings['Gmail_ChangeAccount']; ?>
	<input type="button" name="clear_tokens" class="crmbutton small save" value="<?php echo $app_strings['LBL_CLEAR_BUTTON_LABEL']; ?>" onclick="cleartokens(<?php echo $current_user->id ?>)">
</div>
<div id="google_sync_verifying" style="display:none;">
	<img src="themes/images/vtbusy.gif" alt="working"><?php echo $mod_strings['LBL_GOOGLE_SYNC_CONTROL_ACCESS_DATA']; ?>
</div>
<div id="google_sync_text">
<?php
$GoogleSync4You = new GoogleSync4You();
$have_access_data = $GoogleSync4You->setAccessDataForUser($current_user->id);
if ($have_access_data) {
	$GoogleSync4You->connectToGoogle();
}
if (!$GoogleSync4You->isLogged()) {
	echo $GoogleSync4You->getStatus();
} else {
	echo $mod_strings['LBL_CONNECTING_WORK_CORRECT'];
	echo '&nbsp;&nbsp;<a href="'.$GoogleSync4You->getAuthURL(true).'">'.$mod_strings['LBL_CONNECT'].'</a>';
}
$google_login = $GoogleSync4You->getclientsecret();
$google_apikey= $GoogleSync4You->getAPI();
$google_keyfile = $GoogleSync4You->getkeyfile();
$google_clientid = $GoogleSync4You->getclientid();
$google_refresh = $GoogleSync4You->getrefreshtoken();
$googleinsert = $GoogleSync4You->getgoogleinsert();
if ($googleinsert==1) {
	$checked = 'checked';
} else {
	$checked = '';
}
?>
</div>
<?php
if (is_admin($current_user)) {
	if ($google_login != '') {
		echo "<div id='google_account_info_div'>";
		echo getTranslatedString('LBL_GOOGLECLIENTSECRET', 'Calendar4You').':';
		echo $google_login;
		echo '&nbsp;<input title="'.$mod_strings['LBL_SET_ACCESS_DATA'].'" class="crmButton password small" onclick="changeGoogleAccount();" ';
		echo 'name="change_google_user" value="'.$mod_strings['LBL_CHANGE_GOOGLE_ACCOUNT'].'" type="button">';
		echo '</div>';
		$update_google_account = 0;
		echo "<div id='google_account_change_div' style='display:none'>";
	} else {
		echo '<div>';
		$update_google_account = 1;
	}
	$cellCloseOpen = '</td><td>';
	$rowCloseOpen = '</td></tr><tr><td>';
	$inputClass = '" class="slds-input">';
	echo '<table><tr><td>';
	echo getTranslatedString('LBL_GOOGLECLIENTSECRET', 'Calendar4You').':';
	echo $cellCloseOpen;
	echo '<input type="hidden" name="google_refresh" id="google_refresh" value="'.$google_refresh.'">';
	echo '<input type="text" name="google_login" id="google_login" value="'.$google_login.$inputClass;
	echo $rowCloseOpen;
	echo getTranslatedString('LBL_GOOGLEAPIKEY', 'Calendar4You').': ';
	echo $cellCloseOpen;
	echo '<input type="text" name="google_apikey" id="google_apikey" value="'.$google_apikey.$inputClass;
	echo $rowCloseOpen;
	echo getTranslatedString('LBL_GOOGLECLIENTID', 'Calendar4You').': ';
	echo $cellCloseOpen;
	echo '<input type="text" name="google_clientid" id="google_clientid" value="'.$google_clientid.$inputClass;
	echo $rowCloseOpen;
	echo getTranslatedString('LBL_GOOGLEURI', 'Calendar4You').': ';
	echo $cellCloseOpen;
	echo '<input type="text" name="google_keyfile" id="google_keyfile" value="'.$google_keyfile.$inputClass;
	echo $rowCloseOpen;
	echo getTranslatedString('LBL_GOOGLEINS', 'Calendar4You').' ';
	echo $cellCloseOpen;
	echo '<input type="checkbox" name="googleinsert" id="googleinsert" '.$checked.' value="'.$googleinsert.'" class="slds-checkbox" >';
	echo '</td></tr>';
	echo '</table>';
	echo '</div>';
	echo '<input type="hidden" name="update_google_account" id="update_google_account" value="'.$update_google_account.'">';
} // is admin
?>
</div>
<?php
$out = ob_get_clean();
ob_end_clean();
$smarty->assign('OUT', $out);
$smarty->display('modules/cbCalendar/CalendarSettings.tpl');
?>