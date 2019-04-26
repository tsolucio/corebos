<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
$Vtiger_Utils_Log = true;

include_once 'vtlib/Vtiger/Module.php';

$smsnotifierModuleInstance = Vtiger_Module::getInstance('SMSNotifier');
$smsnotifierModuleInstance->addLink("HEADERSCRIPT", "SMSNotifierCommonJS", "modules/SMSNotifier/SMSNotifierCommon.js");

$leadsModuleInstance = Vtiger_Module::getInstance('Leads');

$leadsModuleInstance->addLink("LISTVIEWBASIC", "Send SMS", "SMSNotifierCommon.displaySelectWizard(this, '\$MODULE\$');");
$leadsModuleInstance->addLink("DETAILVIEWBASIC", "Send SMS", "javascript:SMSNotifierCommon.displaySelectWizard_DetailView('\$MODULE\$', '\$RECORD\$');");

$contactsModuleInstance = Vtiger_Module::getInstance('Contacts');
$contactsModuleInstance->addLink('LISTVIEWBASIC', 'Send SMS', "SMSNotifierCommon.displaySelectWizard(this, '\$MODULE\$');");
$contactsModuleInstance->addLink("DETAILVIEWBASIC", "Send SMS", "javascript:SMSNotifierCommon.displaySelectWizard_DetailView('\$MODULE\$', '\$RECORD\$');");

$accountsModuleInstance = Vtiger_Module :: getInstance('Accounts');
?>