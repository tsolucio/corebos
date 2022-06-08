<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'vtlib/Vtiger/LanguageExport.php';

/**
 * Provides API to import language into vtiger CRM
 * @package vtlib
 */
class Vtiger_LanguageImport extends Vtiger_LanguageExport {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}

	public function getPrefix() {
		return (string)$this->_modulexml->prefix;
	}

	/**
	 * Initialize Import
	 */
	public function initImport($zipfile, $overwrite) {
		$this->__initSchema();
		$this->getModuleNameFromZip($zipfile);
	}

	/**
	 * Import Module from zip file
	 * @param string Zip file name
	 * @param boolean True for overwriting existing module
	 */
	public function import($zipfile, $overwrite = false) {
		$this->initImport($zipfile, $overwrite);
		// Call module import function
		$this->import_Language($zipfile);
	}

	/**
	 * Update Module from zip file
	 * @param object Instance of Language (to keep Module update API consistent)
	 * @param string Zip file name
	 * @param boolean True for overwriting existing module
	 */
	public function update($instance, $zipfile, $overwrite = true) {
		$this->import($zipfile, $overwrite);
	}

	/**
	 * Import Module
	 */
	public function import_Language($zipfile) {
		$name = $this->_modulexml->name;
		$prefix = $this->_modulexml->prefix;
		$label = $this->_modulexml->label;

		self::log("Importing $label [$prefix] ... STARTED");
		$unzip = new Vtiger_Unzip($zipfile);
		$filelist = $unzip->getList();

		foreach ($filelist as $filename => $fileinfo) {
			if (!$unzip->isdir($filename)) {
				if (strpos($filename, '/') === false) {
					continue;
				}

				$targetdir  = substr($filename, 0, strrpos($filename, '/'));
				$targetfile = basename($filename);

				$prefixparts = explode('_', $prefix);

				$dounzip = false;
				if (is_dir($targetdir)) {
					// Case handling for jscalendar
					if (stripos($targetdir, 'jscalendar/lang') === 0
						&& stripos($targetfile, 'calendar-'.$prefixparts[0].'.js')===0) {
						if (file_exists("$targetdir/calendar-en.js")) {
							$dounzip = true;
						}
					} elseif (stripos($targetdir, 'modules/Emails/language') === 0
						// Case handling for phpmailer
						&& stripos($targetfile, "phpmailer.lang-$prefix.php")===0) {
						if (file_exists("$targetdir/phpmailer.lang-en_us.php")) {
							$dounzip = true;
						}
					} elseif (preg_match("/$prefix.lang.js/", $targetfile)) {
						// Handle javascript language file
						$corelangfile = "$targetdir/en_us.lang.js";
						if (file_exists($corelangfile)) {
							$dounzip = true;
						}
					} elseif (preg_match("/$prefix.lang.php/", $targetfile)) {
						// Handle php language file
						$corelangfile = "$targetdir/en_us.lang.php";
						if (file_exists($corelangfile)) {
							$dounzip = true;
						}
					}
				}

				if ($dounzip) {
					if ($unzip->unzip($filename, $filename) !== false) {
						self::log("Copying file $filename ... DONE");
					} else {
						self::log("Copying file $filename ... FAILED");
					}
				} else {
					self::log("Copying file $filename ... SKIPPED");
				}
			}
		}
		if ($unzip) {
			$unzip->close();
		}

		self::register($prefix, $label, $name);
		self::log("Importing $label [$prefix] ... DONE");
	}
}
?>
