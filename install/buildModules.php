<?php
require_once('vtlib/Vtiger/PackageTools.php');

function buildModules() {
	$info = array( 'packages/mandatory' => array(
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
	),
	'packages/optional' => array(
		'Assets',
		'BrazilianLanguagePack_bz_bz' => array(
			'type' => 'lang',
			'code' => 'pt_br',
		),
		'BritishLanguagePack_br_br' => array(
			'type' => 'lang',
			'code' => 'en_gb',
		),
		'CronTasks',
		'CustomerPortal',
		'Deutsch' => array(
			'type' => 'lang',
			'code' => 'de_de',
		),
		'Dutch' => array(
			'type' => 'lang',
			'code' => 'nl_nl',
		),
		'FieldFormulas',
		'French' => array(
			'type' => 'lang',
			'code' => 'fr_fr',
		),
		'Hungarian' => array(
			'type' => 'lang',
			'code' => 'hu_hu',
		),
		'MexicanSpanishLanguagePack_es_mx' => array(
			'type' => 'lang',
			'code' => 'es_mx',
		),
		'ModComments',
		'Projects' => array(
			'type' => 'bundle',
			'manifest' => array(
				'version' => '2.7',
				'vtiger_version' => '5.1.0',
				'vtiger_max_version' => '5.*',
			),
			'moduleList' => array(
				'ProjectMilestone',
				'ProjectTask',
				'Project',
			),
		),
		'RecycleBin',
		'SMSNotifier',
		'Spanish' => array(
			'type' => 'lang',
			'code' => 'es_es',
		),
		'Tooltip',
		'Webforms',
	));

	foreach ($info as $path => $packages) {
		foreach ($packages as $packageName => $packageInfo) {
			if (is_numeric($packageName)) {
				$packageName = $packageInfo;
				$packageInfo = array('type' => 'module');;
			}
			if (is_file($path . '/' . $packageName . '.zip')) {
				continue;
			}
			switch ($packageInfo['type']) {
			case 'module':
				PackageTools::buildModulePackage($packageName, $path);
				break;
			case 'bundle':
				$moduleList = $packageInfo['moduleList'];
				PackageTools::buildBundlePackage($packageName, $moduleList, $packageInfo['manifest'], $path);
				break;
			case 'lang':
				PackageTools::buildLangPackage($packageInfo['code'], $packageName, $path);
				break;
			}
		}
	}

}
