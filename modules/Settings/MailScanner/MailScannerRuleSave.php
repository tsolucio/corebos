<?php
/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'modules/Settings/MailScanner/core/MailScannerInfo.php';
require_once 'modules/Settings/MailScanner/core/MailScannerRule.php';
require_once 'modules/Settings/MailScanner/core/MailScannerAction.php';
require_once 'Smarty_setup.php';

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
$scannerrule->cc = vtlib_purify($_REQUEST['rule_cc']);
$scannerrule->assign_to= vtlib_purify(($_REQUEST['assigntype'] == 'U' ? $_REQUEST['assigned_user_id'] : $_REQUEST['assigned_group_id']));
$scannerrule->add_email_as = (empty($_REQUEST['add_email_as']) ? 'CommentAndEmail' : vtlib_purify($_REQUEST['add_email_as']));
$scannerrule->workflowid = (empty($_REQUEST['workflowid']) ? null : vtlib_purify($_REQUEST['workflowid']));
$scannerrule->workflowname = (empty($_REQUEST['workflowid_display']) ? null : vtlib_purify($_REQUEST['workflowid_display']));
$scannerrule->must_be_related = (empty($_REQUEST['must_be_related']) ? false : true);
$scannerrule->update();

$scannerrule->updateAction($scanneractionid, vtlib_purify($_REQUEST['rule_actiontext']));

include 'modules/Settings/MailScanner/MailScannerRule.php';
?>
