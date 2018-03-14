<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module       : coreBOS OnDemand
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

// CONFIGURATION VARIABLES

// Activate OnDemand
$coreBOSOnDemandActive = false;

// Login Page
$cbodLoginPage = 'ldsnoimage';

// maximum storage space permitted per install in Gb
$cbodStorageSizeLimit = 5;

// fixed cron tasks
$cbodFixedCronTasks = array('StorageSpaceUsage');

// Blocked users
$cbodBlockedUsers = array();

// Unique user connection
$cbodUniqueUserConnection = false;

// Limit to permit unblocking a login session of a user
$cbodTimeToSessionLogout = 5; // minutes

// Show License on user creation
$cbodShowLicenseOnUserCreation = false;

// User Create/Update Log
$cbodUserLog = false;

// Install database
$corebosInstallDatabase = 'corebos_justinstalled_empty';

// Connection to Central Server
$cbodCSURL = '';
$cbodCSUsr = '';
$cbodCSKey = '';

// User IDs with permission to get full sync information
$cbodCSAppSyncUser = array(1);
