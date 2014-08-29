<?php
/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/
require_once('modules/Settings/MailScanner/core/MailScannerInfo.php');
require_once('modules/Settings/MailScanner/core/MailScannerRule.php');
require_once('modules/Settings/MailScanner/core/MailScannerAction.php');
require_once('Smarty_setup.php');

global $app_strings, $mod_strings, $currentModule, $theme, $current_language;

$scannername = vtlib_purify($_REQUEST['scannername']);
$scannerruleid= vtlib_purify($_REQUEST['ruleid']);
$scanneractionid= vtlib_purify($_REQUEST['actionid']);

$scannerinfo = new Vtiger_MailScannerInfo($scannername);
$scannerrule = new Vtiger_MailScannerRule($scannerruleid);

$scannerrule->scannerid   = $scannerinfo->scannerid;
$scannerrule->fromaddress = vtlib_purify($_REQUEST['rule_from']);
$scannerrule->toaddress = vtlib_purify($_REQUEST['rule_to']);
$scannerrule->subjectop = vtlib_purify($_REQUEST['rule_subjectop']);
$scannerrule->subject   = vtlib_purify($_REQUEST['rule_subject']);
$scannerrule->bodyop    = vtlib_purify($_REQUEST['rule_bodyop']);
$scannerrule->body      = vtlib_purify($_REQUEST['rule_body']);
$scannerrule->matchusing= vtlib_purify($_REQUEST['rule_matchusing']);

$scannerrule->update();

$scannerrule->updateAction($scanneractionid, vtlib_purify($_REQUEST['rule_actiontext']));

include('modules/Settings/MailScanner/MailScannerRule.php');

?>
