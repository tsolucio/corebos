<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
require 'modules/Contacts/views/List.php';
require_once 'modules/Contacts/connectors/Oauth2.php';
require_once 'modules/Contacts/controllers/Contacts.php';
require_once 'modules/Contacts/connectors/Contacts.php';
require_once 'modules/Contacts/models/Module.php';
require_once 'modules/Contacts/connectors/Vtiger.php';
require_once 'modules/Contacts/models/Contacts.php';
require_once 'modules/Contacts/helpers/Utils.php';
require_once 'Smarty_setup.php';

global $mod_strings, $app_strings, $currentModule, $current_user, $theme, $log;

$listFocus=new Google_List_View();
$listFocus->process($_REQUEST);
