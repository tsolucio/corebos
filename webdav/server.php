<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : WEBDAV
 *************************************************************************************************/
use Sabre\DAV;

chdir('..');
/** All service invocation needs have valid app_key parameter sent */
require_once 'config.inc.php';

/* If app_key is not set, pick the value from cron configuration */
if (empty($_REQUEST['app_key'])) {
	$_REQUEST['app_key'] = $application_unique_key;
}

set_include_path(get_include_path().PATH_SEPARATOR.'./Sync/');

include 'vendor/autoload.php';
require_once 'include/logging.php';
require_once 'include/database/PearDatabase.php';
require_once 'modules/Documents/Documents.php';
require_once 'modules/Users/Users.php';
require_once 'include/utils/UserInfoUtil.php';
require_once 'modules/PickList/PickListUtils.php';
require_once 'Authenticate.php';
require_once 'DirectoryModule.php';
require_once 'Browser.php';
require_once 'DirectoryLetter.php';
require_once 'DirectoryRecord.php';
require_once 'DirectoryModuleList.php';
require_once 'DirectoryFolder.php';
require_once 'DirectoryGroup.php';
require_once 'CRMFile.php';

global $current_user;
if (empty($current_user)) {
	$current_user = Users::getActiveAdminUser();
}
// Change public to something else, if you are using a different directory for your files
// Make sure there is a directory in your current directory named 'public'. We will be exposing that directory to WebDAV
$publicDir = new DAV\Tree(new DirectoryGroup());

// The object tree needs in turn to be passed to the server class
$server = new DAV\Server($publicDir);

if ($_SERVER['REQUEST_METHOD'] == 'GET' || $_SERVER['REQUEST_METHOD'] == 'POST') {
	$plugin = new cbBrowserPlugin();
	$server->addPlugin($plugin);
}

$server->setBaseUri(dirname($_SERVER['SCRIPT_NAME'])); // ideally, SabreDAV lives on a root directory with mod_rewrite sending every request to index.php

// The lock manager is reponsible for making sure users don't overwrite each others changes
$lockFile = sys_get_temp_dir().'/locks';
$lockBackend = new DAV\Locks\Backend\File($lockFile);
$lockPlugin = new DAV\Locks\Plugin($lockBackend);
$server->addPlugin($lockPlugin);

// Creating the backend
$authBackend = new Authenticate();

// Creating the plugin. The realm parameter is ignored by the backend
$authPlugin = new DAV\Auth\Plugin($authBackend, 'Login WebDAV');

coreBOS_Session::init(true, true);
if (!isset($_SESSION['authenticated_user_id'])) {
	// Adding the plugin to the server
	$server->addPlugin($authPlugin);
}

// All we need to do now, is to fire up the server
$server->start();
