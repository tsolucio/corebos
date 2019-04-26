<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/logging.php';
require_once 'modules/Users/LoginHistory.php';
require_once 'modules/Users/Users.php';
require_once 'include/database/PearDatabase.php';
require_once 'include/utils/Session.php';
global $adb,$current_user;

// Recording Logout Info
$usip = Vtiger_Request::get_ip();
$outtime=date("Y/m/d H:i:s");
$loghistory=new LoginHistory();
$loghistory->user_logout($current_user->user_name, $usip, $outtime);

coreBOS_Settings::delSetting('cbodUserConnection'.$current_user->id);

$local_log = LoggerManager::getLogger('Logout');

// clear out the autthenticating flag
coreBOS_Session::destroy();

// go to the login screen.
header('Location: index.php?action=Login&module=Users');
?>