<?php

include_once("include/database/PearDatabase.php");
include_once("include/utils/utils.php");
global $root_directory,$adb,$site_URL;
session_start();
set_include_path($root_directory. "modules/Calendar4You/");
require_once 'gcal/src/Google/Client.php';
require_once 'gcal/src/Google/Service/Calendar.php';
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
  $client->authenticate($_GET['code']);
  $_SESSION['token'] = $client->getAccessToken();
  $token=json_decode($_SESSION['token']);
  if($token->refresh_token!='' && $token->refresh_token!=null)
  $adb->pquery("update its4you_googlesync4you_access set refresh_token=? where userid=?",array($token->refresh_token,$_SESSION['authenticated_user_id']));
  header("Location: $site_URL/index.php?module=Calendar4You&action=index");
}

?>
