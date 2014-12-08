<?php
require_once('vtlib/Vtiger/Module.php');

function installAddons() {
	$packageList = array('module' => array(
		'ConfigEditor',
		'Import',
		'Integration',
		'MailManager',
		'Mobile',
		'ModTracker',
		'PBXManager',
		'ServiceContracts',
		'Services',
		'VtigerBackup',
		'WSAPP',
		'cbupdater',
		'CobroPago' => false,
		'Assets' => false,
		'CronTasks' => false,
		'CustomerPortal' => false,
		'FieldFormulas' => false,
		'ModComments' => false,
		'ProjectMilestone' => false,
		'ProjectTask' => false,
		'Project' => false,
		'RecycleBin' => false,
		'SMSNotifier' => false,
		'Tooltip' => false,
		'Webforms' => false,
	),
	'lang' => array(
		'pt_br' => false,
		'en_gb' => false,
		'de_de' => false,
		'nl_nl' => false,
		'fr_fr' => false,
		'hu_hu' => false,
		'es_mx' => false,
		'es_es' => false,
	));

	$packageImport = new Vtiger_PackageImport();
	foreach ($packageList as $type => $packages) {
		foreach ($packages as $package => $enabled) {
			if (is_numeric($package)) {
				$package = $enabled;
				$enabled = true;
			}
			switch ($type) {
			case 'module':
				$packageImport->importManifest('modules/' . $package);
				vtlib_toggleModuleAccess($package, $enabled, true);
				break;
			case 'lang':
				$packageImport->importManifest('include/language/' . $package . '.manifest.xml');
				vtlib_toggleLanguageAccess($package, $enabled);
				break;
			}
		}
	}

}
