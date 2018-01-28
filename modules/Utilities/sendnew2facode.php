<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************/
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/Users/Users.php';
include_once 'modules/Users/authTypes/TwoFactorAuth/autoload.php';
use \RobThree\Auth\TwoFactorAuth;

if (Users::is_ActiveUserID($_REQUEST['authuserid'])) {
	$tfa = new TwoFactorAuth('coreBOSWebApp');
	$twofasecret = coreBOS_Settings::getSetting('coreBOS_2FA_Secret_'.$_REQUEST['authuserid'], false);
	if ($twofasecret===false) {
		$secret = $tfa->createSecret(160);
		$twofasecret = $secret;
		coreBOS_Settings::setSetting('coreBOS_2FA_Secret_'.$_REQUEST['authuserid'], $twofasecret);
	}
	$code = $tfa->getCode($twofasecret);
	coreBOS_Settings::setSetting('coreBOS_2FA_Code_'.$_REQUEST['authuserid'], $code);
	Users::send2FACode($code, $_REQUEST['authuserid']);
	echo getTranslatedString('2FA_NEWCODESENT', 'Users');
} else {
	echo getTranslatedString('ERR_INVALIDUSERID', 'Users');
}
die();