<?php
/*************************************************************************************************
 * Copyright 2020 Spike, JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *************************************************************************************************
 *  Module    : Third Party Updating SMTP User settings
 *  Version   : 1.0
 *  Author    : Spike, JPL TSolucio, S. L.
 *************************************************************************************************/

global $adb;
$inc_smtp_user = vtlib_purify($_REQUEST['inc_smtp_user']);
$og_smtp_user = vtlib_purify($_REQUEST['og_smtp_user']);
if (isset($inc_smtp_user) && $inc_smtp_user != '') {
	$sql = "update vtiger_mail_accounts set mail_servername=?, mail_username=?, mail_password=?, status=?, box_refresh=?, mail_protocol=?, ssltype=?, sslmeth=?, display_name=? where mail_username=?";
	$result = $adb->pquery($sql, array('', '', '', 0, 0, 'IMAP4', 'ssl', 'novalidate-cert', '', $inc_smtp_user));
	echo 'updated';
}
if (isset($og_smtp_user) && $og_smtp_user != '') {
	$sql = "update vtiger_mail_accounts set og_server_name=?, og_server_username=?, og_server_password=?, og_server_status=?, og_smtp_auth=? where og_server_username=?";
	$result = $adb->pquery($sql, array('', '', '', 0, 'false', $og_smtp_user));
	echo 'updated';
}
?>