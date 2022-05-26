<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************** */
include_once 'include/utils/utils.php';
include_once 'include/utils/VTCacheUtils.php';
include_once 'include/utils/CommonUtils.php';

class ModTracker_Field {

	public $parent;
	public $moduleMeta = null;
	public $fieldInfo = null;

	public function __construct($parent) {
		$this->parent = $parent;
	}

	public function getFieldLabel() {
		return $this->fieldInfo->getFieldLabelKey();
	}

	public function getDisplayLabel($value) {
		$recordId = $this->parent->getRecordId();
		$fieldInstance = $this->fieldInfo;
		$moduleName = $this->parent->getModuleName();
		$value = $this->getFieldDisplayValue($moduleName, $recordId, $fieldInstance, $value);
		return $value;
	}

	public function getFieldDisplayValue($moduleName, $recordId, $fieldInstance, $value) {
		global $current_user;

		$fieldName = $fieldInstance->getFieldName();

		if ($moduleName == 'Documents') {
			if ($fieldName == 'filesize') {
				if (empty($value)) {
					$value = '--';
				} else {
					$value = FileField::getFileSizeDisplayValue($value);
				}
			} elseif ($fieldName == 'filestatus' || $fieldName == 'filetype') {
				$value = BooleanField::getBooleanDisplayValue($value, $moduleName);
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
			$value = getTranslatedString($value, $moduleName);
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

		if ($fieldInstance->getFieldDataType() == 'currency' && $value != '' && $value != 0) {
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
			$value = BooleanField::getBooleanDisplayValue($value, $moduleName);
		}

		if ($fieldInstance->getFieldDataType() == 'multipicklist') {
			$value = ($value != '') ? str_replace(Field_Metadata::MULTIPICKLIST_SEPARATOR, ', ', $value) : '';
		}
		if ($fieldInstance->getFieldDataType() == 'reference') {
			if (!empty($value)) {
				$referenceList = $fieldInstance->getReferenceList();
				if (!empty($referenceList)) {
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
			if (empty($ownerName) && !empty($value)) {
				$ownerInfo = getGroupName($value);
				$ownerName = $ownerInfo[0];
			}
			$value = $ownerName;
		}

		if ($fieldInstance->getFieldDataType() == 'multireference') {
			$ids = explode(' |##| ', $value);
			$value = '';
			if (count($ids) > 0) {
				$parent_module = getSalesEntityType($ids[0]);
				foreach ($ids as $id) {
					$displayValue = getEntityName($parent_module, $id);
					$value .= '<a href="index.php?module='.$parent_module.'&action=DetailView&record='.$id.'">'.$displayValue[$id].'</a>, ';
				}
				$value = rtrim($value, ', ');
			}
		}

		return $value;
	}

	public function initialize() {
		global $adb, $current_user;
		if ($this->moduleMeta === null) {
			$moduleHandler = vtws_getModuleHandlerFromName($this->parent->getModuleName(), $current_user, true);
			$this->moduleMeta = $moduleHandler->getMeta();
		}
		if ($this->parent->getModuleName()=='Products' && $this->parent->getFieldName()=='imagename') {
			$sql = "select *, '0' as readonly from vtiger_field where vtiger_field.tabid=14 and fieldname='imagename'";
			$result = $adb->pquery($sql, array());
			$webserviceField = WebserviceField::fromQueryResult($adb, $result, 0);
			$this->fieldInfo = $webserviceField;
		} elseif ($this->parent->getModuleName()=='HelpDesk' && $this->parent->getFieldName()=='comments') {
			$sql = "select *, '0' as readonly from vtiger_field where vtiger_field.tabid=13 and fieldname='comments'";
			$result = $adb->pquery($sql, array());
			$webserviceField = WebserviceField::fromQueryResult($adb, $result, 0);
			$this->fieldInfo = $webserviceField;
		} else {
			$moduleFields = $this->moduleMeta->getModuleFields();
			$this->fieldInfo = (isset($moduleFields[$this->parent->getFieldName()]) ? $moduleFields[$this->parent->getFieldName()] : '');
		}
	}
}
?>
