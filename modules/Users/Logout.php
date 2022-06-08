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
include_once 'include/integrations/saml/saml.php';
global $adb,$current_user;
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST[$GLOBALS['csrf']['input-name']] = empty($_REQUEST[$GLOBALS['csrf']['input-name']]) ? '' : $_REQUEST[$GLOBALS['csrf']['input-name']];
Vtiger_Request::validateRequest();

// Recording Logout Info
$loghistory=new LoginHistory();
$loghistory->user_logout($current_user->user_name, Vtiger_Request::get_ip(), date('Y/m/d H:i:s'));
cbEventHandler::do_action('corebos.logout', array($current_user));
coreBOS_Settings::delSetting('cbodUserConnection'.$current_user->id);

$local_log = LoggerManager::getLogger('Logout');

// clear out the autthenticating flag
coreBOS_Session::destroy();
$saml = new corebos_saml();
if ($saml->isActive() && !empty($saml->samlclient)) {
	$saml->logout();
}
// go to the login screen.
header('Location: index.php?action=Login&module=Users');
?>