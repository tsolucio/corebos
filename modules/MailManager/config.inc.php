<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

$MAILMANAGER_CONFIG = array(
	// Max upload limit in bytes
	'MAXUPLOADLIMIT'=> 5242880,

    // Max Download Limit in Bytes, as the files are encoded the file size increases
    // so the limit is set to close to 7MB
    'MAXDOWNLOADLIMIT'=>7000000,
    
	// Increase the memory_limit for larger attachments
	'MEMORY_LIMIT'	=> '256M'
);


/**
 * Manages Mail Manager configurations
 */
class ConfigPrefs {
	
	/**
	 * Get configuration parameter configured value or default one
	 */
	static function get($key, $defvalue=false) {
		global $MAILMANAGER_CONFIG;
		if(isset($MAILMANAGER_CONFIG)){
			if(isset($MAILMANAGER_CONFIG[$key])) {
				return $MAILMANAGER_CONFIG[$key];
			}
		}
		return $defvalue;
	}
}
?>