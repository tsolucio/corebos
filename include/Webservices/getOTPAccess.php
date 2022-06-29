<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L.  --  This file is a part of coreBOS.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
*************************************************************************************************/

/**
 * return the session and login information from an OTP
 * @param string OTP code
 * @return array result of the session login operation
 */
function cbws_getOTPAccess($OTPCode) {
	global $adb, $default_charset;
	$expireAfter = GlobalVariable::getVariable('Webservice_ExpireOTPAfter', 30);
	// eliminate expired OTPs
	$cbstrs = $adb->pquery('select * from cb_settings where setting_key like ?', array('OTPCODE:%'));
	while ($set = $adb->fetch_array($cbstrs)) {
		$return = json_decode(html_entity_decode($set['setting_value'], ENT_QUOTES, $default_charset), true);
		if ($return['isWSOTP'] && (time()-$return['now']>$expireAfter)) {
			coreBOS_Settings::delSetting($set['setting_key']);
		}
	}
	$return = array(
		'wssuccess' => false,
		'wsresult' => 'OTP code expired or invalid',
	);
	// try to get the one they asked for
	$otp = 'OTPCODE:'.$OTPCode;
	if (coreBOS_Settings::settingExists($otp)) {
		$set = coreBOS_Settings::getSetting($otp, '');
		coreBOS_Settings::delSetting($otp);
		$set = json_decode(html_entity_decode($set, ENT_QUOTES, $default_charset), true);
		if ($set['isWSOTP'] && (time()-$set['now']<$expireAfter)) {
			unset($set['isWSOTP'], $set['now']);
			$return = $set;
		}
	}
	return $return;
}