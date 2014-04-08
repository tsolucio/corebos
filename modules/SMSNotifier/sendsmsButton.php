<?php
ini_set('error_reporting', 'on');
error_reporting(E_ALL & ~E_NOTICE);

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