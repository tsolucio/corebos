<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

abstract class SyncHandler {

	protected $user;
	protected $key;
	protected $syncServer;
	protected $syncModule;

	abstract public function get($module, $token, $user);
	abstract public function put($element, $user);
	abstract public function map($element, $user);
	abstract public function nativeToSyncFormat($element);
	abstract public function syncToNativeFormat($element);
}
?>
