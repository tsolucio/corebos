<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************/
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
		'CronTasks' => true,
		'CustomerPortal' => false,
		'ModComments' => true,
		'ProjectMilestone' => false,
		'ProjectTask' => false,
		'Project' => false,
		'RecycleBin' => true,
		'SMSNotifier' => false,
		'Tooltip' => false,
		'Webforms' => false,
	),
	'lang' => array(
		'it_it' => false,
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
