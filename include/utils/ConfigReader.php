<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class ConfigReader {

	protected $properties = array();
	protected $name;

	static private $propertiesCache = array();

	public function __construct($path, $name, $force = false) {
		$this->load($path, $name, $force);
	}

	public function load($path, $name, $force = false) {
		$this->name = $path;
		if (!$force && isset(self::$propertiesCache[$path])) {
			$this->properties = self::$propertiesCache[$path];
			return;
		}
		require $path;
		$this->properties = $$name;
		self::$propertiesCache[$path] = $this->properties;
	}

	public function setConfig($key, $value) {
		if (!empty($key)) {
			$this->properties[$key] = $value;
			//not neccessary for php5.x versions
			self::$propertiesCache[$this->name] = $this->properties;
		}
	}

	public function getConfig($key) {
		return (empty($key) ? '' : $this->properties[$key]);
	}
}
?>