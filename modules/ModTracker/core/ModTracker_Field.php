<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************** */

include_once('include/utils/utils.php');
include_once('include/utils/VTCacheUtils.php');
include_once('include/utils/CommonUtils.php');

class ModTracker_Field {

	var $parent;
	var $moduleMeta = null;
	var $fieldInfo = null;

	function __construct($parent) {
		$this->parent = $parent;
	}

	function getFieldLabel() {
		return $this->fieldInfo->getFieldLabelKey();
	}

	function getDisplayLabel($value) {
		$recordId = $this->parent->getRecordId();
		$fieldInstance = $this->fieldInfo;
		$moduleName = $this->parent->getModuleName();
		$value = $this->getFieldDisplayValue($moduleName, $recordId, $fieldInstance, $value);
		return $value;
	}

	function getFieldDisplayValue($moduleName, $recordId, $fieldInstance, $value) {
		global $current_user;
		$adb = PearDatabase::getInstance();

		$fieldName = $fieldInstance->getFieldName();
		$uitype = $fieldInstance->getUIType();

		if ($moduleName == 'Documents') {
			if ($fieldName == 'filesize') {
				$filesize = $value;
				if (empty($fieldsize)) {
					$value = '--';
				} elseif ($filesize < 1024) {
					$value = $filesize . ' B';
				} elseif ($filesize > 1024 && $filesize < 1048576) {
					$value = round($filesize / 1024, 2) . ' KB';
				} else if ($filesize > 1048576) {
					$value = round($filesize / (1024 * 1024), 2) . ' MB';
				}
			}
			if ($fieldName == 'filestatus') {
				if ($value == 1) {
					$value = getTranslatedString('yes', $moduleName);
				} elseif ($value == 0) {
					$value = getTranslatedString('no', $moduleName);
				} else {
					$value = '--';
				}
			}
			if ($fieldName == 'filetype') {
				if ($value == 1) {
					$value = getTranslatedString('yes', $moduleName);
				} elseif ($value == 0) {
					$value = getTranslatedString('no', $moduleName);
				} else {
					$value = '--';
				}
			}
		}

		if ($fieldInstance->getUIType() == '27') {
			if ($value == 'I') {
				$value = getTranslatedString('LBL_INTERNAL', $moduleName);
			} elseif ($value == 'E') {
				$value = getTranslatedString('LBL_EXTERNAL', $moduleName);
			} else {
				$value = ' --';
			}
		}

		if ($fieldInstance->getFieldDataType() == 'picklist') {
			$isRoleBased = vtws_isRoleBasedPicklist($fieldName);
			if ($isRoleBased && ($fieldName != 'activitytype' || $value != 'Task')) {
				$accessiblePicklistValues = getAssignedPicklistValues($fieldName, $current_user->roleid, $adb);
				if (!empty($value) && !is_admin($current_user) && !in_array($value, $accessiblePicklistValues)) {
					
					$value = "<font color='red'>" . getTranslatedString('LBL_NOT_ACCESSIBLE',
									$moduleName) . "</font>";
				} else {
					$value = getTranslatedString($value, $moduleName);
				}
			} else {
				$value = getTranslatedString($value, $moduleName);
			}
		}

		if ($fieldInstance->getFieldDataType() == 'date'
				|| $fieldInstance->getFieldDataType() == 'datetime'
				|| $fieldInstance->getFieldDataType() == 'time') {

			if ($value != '' && $value != '0000-00-00') {
				$date = new DateTimeField($value);
				if ($fieldInstance->getFieldDataType() == 'date') {
					$value = $date->getDisplayDate();
				}
				if ($fieldInstance->getFieldDataType() == 'datetime') {
					$value = $date->getDisplayDateTimeValue();
				}
				if ($fieldInstance->getFieldDataType() == 'time') {
					$value = $date->getDisplayTime();
				}
			} else {
				$value = '';
			}
		}

		if ($fieldInstance->getFieldDataType() == 'currency') {
			if ($value != '' && $value != 0) {
				if ($fieldInstance->getUIType() == 72) {
					if ($fieldName == 'unit_price') {
						$currencyId = getProductBaseCurrency($recordId, $moduleName);
						$cursym_convrate = getCurrencySymbolandCRate($currencyId);
						$currencySymbol = $cursym_convrate['symbol'];
					} else {
						$currencyInfo = getInventoryCurrencyInfo($moduleName, $recordId);
						$currencySymbol = $currencyInfo['currency_symbol'];
					}
					$currencyValue = CurrencyField::convertToUserFormat($value, null, true);
					$value = CurrencyField::appendCurrencySymbol($currencyValue, $currencySymbol);
				} else {
					$currencyField = new CurrencyField($value);
					$value = $currencyField->getDisplayValueWithSymbol();
				}
			}
		}

		if ($fieldInstance->getFieldDataType() == 'url') {
			$matchPattern = "^[\w]+:\/\/^";
			preg_match($matchPattern, $value, $matches);
			if (!empty($matches[0])) {
				$value = '<a href="' . $value . '" target="_blank">' . $value . '</a>';
			} else {
				$value = '<a href="http://' . $value . '" target="_blank">' . $value . '</a>';
			}
		}
		if ($fieldInstance->getFieldDataType() == 'boolean') {
			if ($value == 1) {
				$value = getTranslatedString('yes', $moduleName);
			} elseif ($value == 0) {
				$value = getTranslatedString('no', $moduleName);
			} else {
				$value = '--';
			}
		}

		if ($fieldInstance->getFieldDataType() == 'multipicklist') {
			$value = ($value != "") ? str_replace(' |##| ', ', ', $value) : "";
			$isRoleBased = vtws_isRoleBasedPicklist($fieldName);
			if (!is_admin($current_user) && $value != '' && $isRoleBased) {
				$accessiblePicklistValues = getAssignedPicklistValues($fieldName, $current_user->roleid, $adb);
				$valueArray = ($value != "") ? explode(', ', $value) : array();
				$notaccess = '<font color="red">' . getTranslatedString('LBL_NOT_ACCESSIBLE', $moduleName) . "</font>";
				$tmpArray = array();
				foreach ($valueArray as $index => $val) {
					if (!in_array(trim($val), $accessiblePicklistValues)) {
						$tmpArray[] = $notaccess;
					} else {
						$tmpArray[] = $val;
					}
				}
				$value = implode(', ', $tmpArray);
			}
		}
		if ($fieldInstance->getFieldDataType() == 'reference') {
			if (!empty($value)) {
				$referenceList = $fieldInstance->getReferenceList();
				if (count($referenceList) > 0) {
					$firstReferenceModule = $referenceList[0];
					if ($firstReferenceModule == 'Users') {
						$value = getUserFullName($value);
					} elseif ($firstReferenceModule == 'DocumentFolders') {
						$wsFolderId = vtws_getWebserviceEntityId('DocumentFolders', $value);
						$value = vtws_getName($wsFolderId, $current_user);
					} elseif ($firstReferenceModule == 'Currency') {
						$value = getCurrencyName($value);
					} else {
						$referenceModule = getSalesEntityType($value);
						$entityNames = getEntityName($referenceModule, array($value));
						$value = $entityNames[$value];
					}
				} else {
					$value = '--';
				}
			} else {
				$value = '--';
			}
		}
		if ($fieldInstance->getFieldDataType() == 'owner') {
			$ownerName = trim(getUserFullName($value));
			if (empty($ownerName)) {
				$ownerInfo = getGroupName($value);
				$ownerName = $ownerInfo[0];
			}
			$value = $ownerName;
		}

		return $value;
	}

	function initialize() {
		global $adb, $current_user;
		if ($this->moduleMeta === null) {
			$moduleHandler = vtws_getModuleHandlerFromName($this->parent->getModuleName(), $current_user);
			$this->moduleMeta = $moduleHandler->getMeta();
		}
		$moduleFields = $this->moduleMeta->getModuleFields();
		$this->fieldInfo = $moduleFields[$this->parent->getFieldName()];
	}

}

?>
