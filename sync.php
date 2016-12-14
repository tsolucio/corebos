<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************
 *  Module       : Calendar::Google Sync
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once("include/database/PearDatabase.php");
include_once("include/utils/utils.php");
global $root_directory,$adb,$site_URL;
coreBOS_Session::init();
set_include_path($root_directory. "modules/Calendar4You/");
require_once 'gcal/vendor/autoload.php';

$q=$adb->pquery("select * from its4you_googlesync4you_access where userid=?",array($_SESSION['authenticated_user_id']));
$client_id=$adb->query_result($q,0,'google_clientid');
$client_secret=$adb->query_result($q,0,'google_login');
$redirect_uri=$adb->query_result($q,0,'google_keyfile');
$api_key=$adb->query_result($q,0,'google_apikey');

$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->setDeveloperKey($api_key);
$client->setAccessType("offline");
$client->setApplicationName("corebos");
$client->setScopes(array("https://www.googleapis.com/auth/calendar","https://www.googleapis.com/auth/calendar.readonly"));

if (isset($_GET['code'])) {
  $q=$client->authenticate($_GET['code']);
  coreBOS_Session::set('token', $client->getAccessToken());
  $token=json_decode($_SESSION['token']);
  var_dump($_SESSION['token']);
  if($token->refresh_token!='' && $token->refresh_token!=null)
  $adb->pquery("update its4you_googlesync4you_access set refresh_token=? where userid=?",array($token->refresh_token,$_SESSION['authenticated_user_id']));
  header("Location: $site_URL/index.php?module=Calendar4You&action=index");
}

?>
