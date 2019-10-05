<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'include/Webservices/Query.php';
include_once __DIR__ . '/Filter.php';

class crmtogo_WS_SearchFilterModel extends crmtogo_WS_FilterModel {
	protected $criterias;

	public function __construct($moduleName) {
		$this->moduleName = $moduleName;
	}

	public function query() {
		return false;
	}

	public function queryParameters() {
		return false;
	}

	public function setCriterias($criterias) {
		$this->criterias = $criterias;
	}

	public function execute($fieldnames, $paging = false, $calwhere = '') {
		$selectClause = sprintf('SELECT %s', implode(',', $fieldnames));
		$fromClause = sprintf('FROM %s', $this->moduleName);
		if ($this->moduleName == 'cbCalendar' && $calwhere !='') {
			$whereClause = " WHERE date_start >= '".$calwhere['start']."' AND date_start <= '".$calwhere['end']."'";
		} else {
			$whereClause = '';
		}
		$orderClause = '';
		$groupClause = '';
		$limitClause = '';
		if ($paging) {
			$config = crmtogo_WS_Controller::getUserConfigSettings();
			$limitClause = 'LIMIT 0,'.$config['NavigationLimit'];
		}
		if (!empty($this->criterias)) {
			$_sortCriteria = $this->criterias['_sort'];
			if (!empty($_sortCriteria)) {
				$orderClause = $_sortCriteria;
			}
		}
		$query = sprintf('%s %s %s %s %s %s;', $selectClause, $fromClause, $whereClause, $orderClause, $groupClause, $limitClause);
		return vtws_query($query, $this->getUser());
	}

	public static function modelWithCriterias($moduleName, $criterias = false) {
		$model = new crmtogo_WS_SearchFilterModel($moduleName);
		$model->setCriterias($criterias);
		return $model;
	}
}