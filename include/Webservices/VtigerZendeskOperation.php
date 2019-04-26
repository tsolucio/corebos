<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
include_once 'include/Webservices/VtigerModuleOperation.php';
require_once 'include/Webservices/VtigerCRMZendeskMeta.php';
include_once 'include/integrations/zendesk/Zendesk.php';

class VtigerZendeskOperation extends VtigerModuleOperation {
	protected $tabId;
	protected $isEntity = true;
	protected $user = '';
	private $zd = null;

	public function __construct($webserviceObject, $user, $adb, $log) {
		parent::__construct($webserviceObject, $user, $adb, $log);
		$this->user = $user;
		$this->tabId = 0;
		$this->zd = new corebos_zendesk();
	}

	protected function getMetaInstance() {
		global $adb;
		if (empty(WebserviceEntityOperation::$metaCache['Zendesk'][$this->user->id])) {
			WebserviceEntityOperation::$metaCache['Zendesk'][$this->user->id]=new VtigerCRMZendeskMeta($this->webserviceObject, $adb, $this->user);
		}
		return WebserviceEntityOperation::$metaCache['Zendesk'][$this->user->id];
	}

	public function query($q) {
		$columns = trim(substr($q, 6, stripos($q, ' from ')-5));
		if (stripos($q, ' where ')) {
			$output = $this->zd->searchTickets(substr($q, stripos($q, ' where ')+6));
		} else {
			$output = $this->zd->getTickets();
		}
		if ($columns!='*') {
			$cols = explode(',', $columns);
			$colout = array();
			foreach ($output as $row) {
				$newrow = array();
				foreach ($cols as $col) {
					$newrow[$col] = isset($row[$col]) ? $row[$col] : '';
				}
				$colout[] = $newrow;
			}
			$output = $colout;
		}
		return $output;
	}
}
?>
