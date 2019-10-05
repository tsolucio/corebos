<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
class SMSProvider {

	public static function getInstance($providername) {
		if (!empty($providername)) {
			$providername = trim($providername);

			$filepath = __DIR__ . "/providers/{$providername}.php";
			checkFileAccessForInclusion($filepath);

			if (!class_exists($providername)) {
				include_once $filepath;
			}
			return new $providername();
		}
		return false;
	}

	public static function listAll() {
		$providers = array();
		if ($handle = opendir(__DIR__ . '/providers')) {
			while (false !== ($file = readdir($handle))) {
				if (!in_array($file, array('.', '..', '.svn', 'CVS'))) {
					if (preg_match("/(.*)\.php$/", $file, $matches)) {
						$providers[] = $matches[1];
					}
				}
			}
		}
		return $providers;
	}
}
?>