<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Modified by crm-now GmbH, www.crm-now.com
 ************************************************************************************/
include_once 'include/Webservices/DescribeObject.php';
include_once __DIR__ . '/Utils.php';

class crmtogo_WS_Describe extends crmtogo_WS_Controller {

	protected function cacheDescribeInfo($describeInfo) {
		$this->_cachedDescribeInfo = $describeInfo;
		$this->_cachedDescribeFieldInfo = array();
		if (!empty($describeInfo['fields'])) {
			foreach ($describeInfo['fields'] as $describeFieldInfo) {
				$this->_cachedDescribeFieldInfo[$describeFieldInfo['name']] = $describeFieldInfo;
			}
		}
	}

	public static function process(crmtogo_API_Request $request) {
		$module = $request->get('module');
		$newrecord = self::transformToBlocks($module);
		$response = new crmtogo_API_Response();
		$response->setResult(array('record' => $newrecord));
		return $response;
	}

	protected static function transformToBlocks($module) {
		global $current_language,$current_user;
		if (empty($current_language)) {
			$current_language = crmtogo_WS_Controller::sessionGet('language');
		}
		$moduleFieldGroups = crmtogo_WS_Utils::gatherModuleFieldGroupInfo($module);
		$describeInfo = vtws_describe($module, $current_user);
		crmtogo_WS_Utils::fixDescribeFieldInfo($module, $describeInfo, $current_user);
		$modifiedResult = array();
		$blocks = array();
		$labelFields = false;
		foreach ($moduleFieldGroups as $blocklabel => $fieldgroups) {
			$fields = array();
			foreach ($fieldgroups as $fieldname => $fieldinfo) {
				$field = array();
				foreach ($describeInfo['fields'] as $describeField) {
					if ($describeField['name']== $fieldname) {
						$field['name'] = $fieldname;
						$field['label'] = $fieldinfo['label'];
						$field['uitype'] = $fieldinfo['uitype'];
						$field['typeofdata'] = $fieldinfo['typeofdata'];
						$field['type'] = '';
						$field['value'] = $describeField['default'];
						if (!empty($describeField['type']) && !empty($describeField['type']['picklistValues'])) {
							$picklistValues = $describeField['type']['picklistValues'];
							$field['type'] = array();
							$field['type']['value'] = array('value' => $picklistValues,'name' => $fieldname);
						}
						if (isset($describeField['type']) && $describeField['type']!='') {
							$field['quickcreate'] = $describeField['quickcreate'];
							$field['displaytype'] = $describeField['displaytype'];
						}
						if ($field['uitype'] == '10') {
							$field['relatedmodule'] = crmtogo_WS_Utils::getEntityName($field['name'], $module);
						}
					}
				}
				if (!empty($field)) {
					$fields[] = $field;
				}
			}
			if (!empty($fields)) {
				$blocks[] = array( 'label' => $blocklabel, 'fields' => $fields );
			}
		}
		$modifiedResult = array('blocks' => $blocks, 'id' => '');
		if ($labelFields) {
			$modifiedResult['labelFields'] = $labelFields;
		}
		return $modifiedResult;
	}
}
