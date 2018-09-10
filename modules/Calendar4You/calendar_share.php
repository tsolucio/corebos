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
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once 'include/database/PearDatabase.php';
require_once 'modules/Calendar/CalendarCommon.php';
require_once 'modules/Calendar4You/Calendar4You.php';
require_once 'modules/Calendar4You/GoogleSync4You.php';
$t=Date('Ymd');
$userDetails=getSharingUserName($current_user->id);
$shareduser_ids = getSharedUserId($current_user->id);

$c_mod_strings = return_module_language($current_language, 'Calendar');
$users_mod_strings = return_module_language($current_language, 'Users');

$Calendar4You = new Calendar4You();
$Calendar_Settings = $Calendar4You->getSettings();
?>
<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerHeadingULine">
	<tr>
		<td class="layerPopupHeading" align="left"><?php echo $c_mod_strings['LBL_CALSETTINGS']?></td>
		<td align=right>
			<a href="javascript:fninvsh('calSettings');"><img src="<?php echo vtiger_imageurl('close.gif', $theme) ?>" border="0" align="absmiddle" /></a>
		</td>
	</tr>
</table>
<form name="SharingForm" method="post" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
<input type="hidden" name="module" value="Calendar4You">
<input type="hidden" name="action" value="updateCalendarSharing">
<input type="hidden" name="user_view_type" value="<?php echo vtlib_purify($_REQUEST['user_view_type']) ?>">
<input type="hidden" name="view" value="<?php echo vtlib_purify($_REQUEST['view']) ?>">
<input type="hidden" name="hour" value="<?php echo (isset($_REQUEST['hour']) ? vtlib_purify($_REQUEST['hour']) : ''); ?>">
<input type="hidden" name="day" value="<?php echo (isset($_REQUEST['day']) ? vtlib_purify($_REQUEST['day']) : ''); ?>">
<input type="hidden" name="month" value="<?php echo (isset($_REQUEST['month']) ? vtlib_purify($_REQUEST['month']) : ''); ?>">
<input type="hidden" name="year" value="<?php echo (isset($_REQUEST['year']) ? vtlib_purify($_REQUEST['year']) : ''); ?>">
<input type="hidden" name="current_userid" value="<?php echo $current_user->id ?>" >
<input type="hidden" name="shar_userid" id="shar_userid" >

<table border=0 cellspacing=0 cellpadding=5 width=95% align=center>
	<tr>
		<td class=small >
			<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
			<tr>
			<td align="right" width="10%" valign="top" rowspan="6"><img src="<?php echo vtiger_imageurl('cal_clock.jpg', $theme); ?>" align="absmiddle"></td>
			<td align="left" width="90%">
			<b><?php echo $c_mod_strings['LBL_TIMESETTINGS']?></b><br>
			<input type="checkbox" name="sttime_check" <?php echo ($current_user->start_hour != '' ? 'checked' : ''); ?> onClick="enableCalstarttime();">&nbsp;
			<?php echo $c_mod_strings['LBL_CALSTART']?>&nbsp;
			<select name="start_hour" <?php echo ($current_user->start_hour == '' ? 'disabled' : '') ?> class="small">
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
			?>
				<option <?php echo $selected?> value="<?php echo $value?>"><?php echo $hour?></option>
			<?php
			}
			?>
			</select>
			</td>
			</tr>
			<tr>
			<td align="left">
				<input type="checkbox" name="hour_format" <?php echo ($current_user->hour_format == '24' ? 'checked' : ''); ?> value="24">&nbsp;
				<?php echo $c_mod_strings['LBL_USE24']?>
			</td>
			</tr>
			<tr>
			<td align="left">
				<input type="checkbox" name="show_weekends" <?php echo ($Calendar_Settings['show_weekends'] == 'true' ? 'checked' : ''); ?> value="1">&nbsp;
				<?php echo $mod_strings['LBL_SHOW_WEEKENDS']?>
			</td>
			</tr>
			<tr>
			<td align="left">
			<?php echo $users_mod_strings['LBL_ACTIVITY_VIEW']?>:&nbsp;
				<select name="activity_view" tabindex="" class="small">
					<option value="Today" <?php echo ($current_user->activity_view == 'Today' ? 'selected' : ''); ?>><?php echo $app_strings['Today']?></option>
					<option value="This Week" <?php echo $current_user->activity_view == 'This Week' ? 'selected' : '';?>><?php echo $app_strings['This Week']?></option>
					<option value="This Month" <?php echo $current_user->activity_view=='This Month' ? 'selected' : '';?>><?php echo $app_strings['This Month']?></option>
				</select>
			</td>
			</tr>
			<tr>
			<td align="left">
				<?php echo $mod_strings['LBL_DEFAULT_USER_VIEW']; ?>:&nbsp;
				<select name="user_view" tabindex="" class="small">
					<option value="me" <?php echo ($Calendar_Settings['user_view'] == 'me' ? 'selected' : ''); ?>><?php echo $mod_strings['LBL_ME']?></option>
					<option value="all" <?php echo ($Calendar_Settings['user_view'] == 'all' ? 'selected' : ''); ?>><?php echo $mod_strings['LBL_ALL_USERS']?></option>
				</select>
			</td>
			</tr>
			<tr>
			<td align="left">
			<?php
			echo $mod_strings['LBL_WEEK_STARTS_AT'].': ';
			/*
			$Days_Values = array("Sunday"=>$c_mod_strings["LBL_DAY0"],
			"Monday"=>$c_mod_strings["LBL_DAY1"],
			"Tuesday"=>$c_mod_strings["LBL_DAY2"],
			"Wednesday"=>$c_mod_strings["LBL_DAY3"],
			"Thursday"=>$c_mod_strings["LBL_DAY4"],
			"Friday"=>$c_mod_strings["LBL_DAY5"],
			"Saturday"=>$c_mod_strings["LBL_DAY6"]);
			*/
			$Days_Values = array("Sunday"=>$c_mod_strings["LBL_DAY0"], "Monday"=>$c_mod_strings["LBL_DAY1"]);
			echo '<select name="dayoftheweek" tabindex="" class="small">';
			foreach ($Days_Values as $day_key => $day_label) {
				if ($Calendar_Settings["dayoftheweek"] == $day_key) {
					$sel = 'selected';
				} else {
					$sel = '';
				}
				echo '<option value="'.$day_key.'" '.$sel.'>'.$day_label.'</option>';
			}
			echo '</select>';
			?>
			</td>
			</tr>
		<tr><td colspan="2" style="border-bottom:1px dotted #CCCCCC;"></td></tr>
		<tr>
			<td align="right" valign="top"><img src="<?php echo vtiger_imageurl('cal_sharing.jpg', $theme) ?>" width="45" height="38" align="absmiddle"></td>
			<td align="left">
			<b><?php echo $c_mod_strings['LBL_CALSHARE']?></b><br>
			<?php echo $c_mod_strings['LBL_CALSHAREMESSAGE']?><br><br>
			<!-- Calendar sharing UI-->
			<DIV id="cal_shar" style="display:block;width:100%;height:200px">
			<table border=0 cellspacing=0 cellpadding=2 width=100% bgcolor="#FFFFFF">
				<tr>
				<td valign=top>
					<table border=0 cellspacing=0 cellpadding=2 width=100%>
					<tr>
						<td colspan=3>
							<ul style="padding-left:20px">
							<li><?php echo $c_mod_strings['LBL_INVITE_SHARE']?>
							<li><?php echo $c_mod_strings['LBL_INVITE_INST2']?>
							</ul>
						</td>
					</tr>
					<tr>
						<td><b><?php echo $c_mod_strings['LBL_AVL_USERS']?></b></td>
						<td>&nbsp;</td>
						<td><b><?php echo $c_mod_strings['LBL_SEL_USERS']?></b></td>
					</tr>
					<tr>
						<td width=40% align=center valign=top>
							<select name="available_users" id="available_users" class=small size=5 multiple style="height:70px;width:100%">
							<?php
							foreach ($userDetails as $id => $name) {
								if ($id != '') {
									echo "<option value=".$id.">".$name."</option>";
								}
							}
							?>
							</select>
						</td>
						<td width=20% align=center valign=top>
							<input type=button value="<?php echo $c_mod_strings['LBL_ADD_BUTTON'] ?> >>" class="crm button small save" style="width:100%" onClick="incUser('available_users','selected_users')">
							<br>
							<input type=button value="<< <?php echo $c_mod_strings['LBL_RMV_BUTTON'] ?> " class="crm button small cancel" style="width:100%" onClick="rmvUser('selected_users')">
						</td>
						<td>
							<select name="selected_users" id="selected_users" class=small size=5 multiple style="height:70px;width:100%">
							<?php
							foreach ($shareduser_ids as $shar_id => $share_user) {
								if ($shar_id != '') {
									echo '<option value='.$shar_id.'>'.$share_user.'</option>';
								}
							}
							?>
							</select>
						</td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
			</div>
			</td>
		</tr>
		<tr><td colspan="2" style="border-bottom:1px dotted #CCCCCC;"></td></tr>
		<tr>
			<td align="right" valign="top"><img src="modules/Calendar4You/images/sync_icon.png" width="45" height="38" align="absmiddle"></td>
			<td align="left"><b>
			<?php
				echo $mod_strings['LBL_GOOGLE_SYNC_ACCESS_DATA'];
				echo ' &quot;'.trim($current_user->first_name.' '.$current_user->last_name).'&quot;';
			?>
			</b>
			<br /><br />
			<div><?php echo $mod_strings['Gmail_ChangeAccount']; ?>
			<input type="button" name="clear_tokens" class="crmbutton small save" value="<?php echo $app_strings['LBL_CLEAR_BUTTON_LABEL']; ?>" onclick="cleartokens(<?php echo $current_user->id ?>)">
			</div>
			<br /><br />
			<div id="google_sync_verifying" style="display:none;">
			<img src="themes/images/vtbusy.gif" align="absmiddle"><? echo $mod_strings['LBL_GOOGLE_SYNC_CONTROL_ACCESS_DATA']; ?>
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
			</div><br>
			<?php
			if (is_admin($current_user)) {
				if ($google_login != '') {
					echo "<div id='google_account_info_div'>";
					echo getTranslatedString('LBL_GOOGLECLIENTSECRET', 'Calendar4You').':';
					echo $google_login;
					echo '&nbsp;<input title="'.$mod_strings["LBL_SET_ACCESS_DATA"].'" class="crmButton password small" onclick="changeGoogleAccount();" ';
					echo 'name="change_google_user" value="'.$mod_strings["LBL_CHANGE_GOOGLE_ACCOUNT"].'" type="button">';
					echo "</div>";
					$update_google_account = 0;
					echo "<div id='google_account_change_div' style='display:none'>";
				} else {
					echo "<div>";
					$update_google_account = 1;
				}
				echo '<table border=0 cellspacing=0 cellpadding=2><tr><td>';
				echo getTranslatedString("LBL_GOOGLECLIENTSECRET", "Calendar4You").":";
				echo '</td><td>';
				echo '<input type="hidden" name="google_refresh" id="google_refresh" value="'.$google_refresh.'" class="small">';
				echo '<input type="text" name="google_login" id="google_login" value="'.$google_login.'" class="small"><br />';
				echo '</td><tr>';
				echo '<tr><td>';
				echo getTranslatedString("LBL_GOOGLEAPIKEY", "Calendar4You").": ";
				echo '</td><td>';
				echo '<input type="text" name="google_apikey" id="google_apikey" value="'.$google_apikey.'" class="small">';
				echo '</td></tr>';
				echo '<tr><td>';
				echo '<tr><td>';
				echo getTranslatedString("LBL_GOOGLECLIENTID", "Calendar4You").": ";
				echo '</td><td>';
				echo '<input type="text" name="google_clientid" id="google_clientid" value="'.$google_clientid.'" class="small">';
				echo '</td></tr>';
				echo '<tr><td>';
				echo '<tr><td>';
				echo getTranslatedString("LBL_GOOGLEURI", "Calendar4You").": ";
				echo '</td><td>';
				echo '<input type="text" name="google_keyfile" id="google_keyfile" value="'.$google_keyfile.'" class="small">';
				echo '</td></tr>';
				echo '<tr><td>';
				echo '<tr><td>';
				echo '<tr><td>';
				echo getTranslatedString("LBL_GOOGLEINS", "Calendar4You").' ';
				echo '</td><td>';
				echo '<input type="checkbox" name="googleinsert" id="googleinsert" '.$checked.' value="'.$googleinsert.'" class="small" >';
				echo '</td></tr>';
				echo '<tr><td>';
				/*
				echo getTranslatedString("LBL_PASSWORD","Users").": ";
				echo '</td><td>';
				echo '<input type="password" name="google_password" id="google_password" value="" class="small">';
				echo '</td></tr>
				*/
				echo '</table>';
				echo '</div>';
				echo '<input type="hidden" name="update_google_account" id="update_google_account" value="'.$update_google_account.'">';
			} // is admin
			?>
		</td>
		</tr>
	</table>
	</td>
	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
	<tr>
		<td align="center">
			<input type="button" name="save" value=" &nbsp;<?php echo $app_strings['LBL_SAVE_BUTTON_LABEL'] ?>&nbsp;" class="crmbutton small save" onClick="userEventSharing('shar_userid','selected_users');controlGoogleSync();"/>
			&nbsp;&nbsp;
			<input type="button" name="cancel" value=" <?php echo $app_strings['LBL_CANCEL_BUTTON_LABEL'] ?> " class="crmbutton small cancel" onclick="fninvsh('calSettings');" />
		</td>
	</tr>
	</table>
</form>
</div>
<hr size="1" color="black">
