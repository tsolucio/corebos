<?php
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : Whatsapp Integration
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

include_once 'include/utils/utils.php';
include_once 'include/Webservices/Utils.php';
include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Update.php';
include_once 'include/integrations/mautic/mautic.php';

use Mautic\MauticApi;

function accountsync($input) {
	global $adb;

    $current_user = Users::getActiveAdminUser();
	$usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;


    global $log;
    $log->fatal(array('AccSyncRAW'=> $input));

	$data = json_decode($input, true);
    global $log;
    $log->fatal(array('AccSync'=> $data));
}