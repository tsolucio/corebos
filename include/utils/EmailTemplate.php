<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************* */
require_once 'include/events/SqlResultIterator.inc';

class EmailTemplate {

	protected $module;
	protected $rawDescription;
	protected $processedDescription;
	protected $recordId;
	protected $processed;
	protected $templateFields;
	protected $user;

	public function __construct($module, $description, $recordId, $user) {
		$this->module = $module;
		$this->recordId = $recordId;
		$this->processed = false;
		$this->user = $user;
		$this->setDescription($description);
		$this->processed = false;
	}

	public function setDescription($description) {
		$this->rawDescription = $description;
		$this->processedDescription = $description;
		$templateVariablePair = explode('$', $this->rawDescription);
		$this->templateFields = array();
		for ($i=1, $iMax = count($templateVariablePair); $i < $iMax; $i+=2) {
			if (strpos($templateVariablePair[$i], '-') === false) {
				continue;
			}
			list($module,$fieldName) = explode('-', $templateVariablePair[$i]);
			if (strpos($fieldName, '_fullpath')) {
				list($field,$fpath) = explode('_', $fieldName);
				$this->templateFields[$module][] = $field;
			}
			$this->templateFields[$module][] = $fieldName;
		}
		$this->processed = false;
	}

	private function getTemplateVariableListForModule($module) {
		$mname = strtolower($module);
		return isset($this->templateFields[$mname]) ? $this->templateFields[$mname] : array();
	}

	public function process() {
		global $site_URL;
		$imagefound = false;
		$variableList = $this->getTemplateVariableListForModule($this->module);
		$handler = vtws_getModuleHandlerFromName($this->module, $this->user);
		$meta = $handler->getMeta();
		$meta->getReferenceFieldDetails();
		$fieldColumnMapping = $meta->getFieldColumnMapping();
		$columnTableMapping = $meta->getColumnTableMapping();
		$tableList = array();
		$columnList = array();
		$columnList_full = array();
		$allColumnList = $meta->getUserAccessibleColumns();

		if (count($variableList) > 0) {
			foreach ($variableList as $column) {
				if (in_array($column, $allColumnList)) {
					$columnList[] = $column;
					$columnList_full[] = $columnTableMapping[$column].'.'.$column;
				}
			}

			foreach ($columnList as $column) {
				if (!empty($columnTableMapping[$column])) {
					$tableList[$columnTableMapping[$column]]='';
				}
			}
			$tableList = array_keys($tableList);
			$defaultTableList = $meta->getEntityDefaultTableList();
			$tableList = array_merge($tableList, $defaultTableList);
			$leadtables = array('vtiger_leadsubdetails','vtiger_leadaddress','vtiger_leadscf');
			$leadmerge = array_intersect($tableList, $leadtables);
			if (count($leadmerge)>0 && !in_array('vtiger_leaddetails', $tableList)) {
				// we need this one because the where condition for Leads uses the converted column from the main table
				$tableList[] = 'vtiger_leaddetails';
			}

			// right now this is will be limited to module type, entities.
			// need to extend it to non-module entities when we have a reliable way of getting
			// record type from the given record id. non webservice id.
			// can extend to non-module entity without many changes as long as the reference field
			// refers to one type of entity, either module entities or non-module entities.
			if (count($tableList) > 0) {
				$sql = 'select '.implode(', ', $columnList_full).' from '.$tableList[0];
				$moduleTableIndexList = $meta->getEntityTableIndexList();
				foreach ($tableList as $tableName) {
					if ($tableName != $tableList[0]) {
						$sql .=' INNER JOIN '.$tableName.' ON '.$tableList[0].'.'.
						$moduleTableIndexList[$tableList[0]].'='.$tableName.'.'.
						$moduleTableIndexList[$tableName];
					}
				}
				$sql .= ' WHERE';
				$deleteQuery = $meta->getEntityDeletedQuery();
				if (!empty($deleteQuery)) {
					$sql .= " $deleteQuery AND";
				}
				$sql .= ' '.$tableList[0].'.'.$moduleTableIndexList[$tableList[0]].'=?';
				$params = array($this->recordId);
				$db = PearDatabase::getInstance();
				$result = $db->pquery($sql, $params);
				$it = new SqlResultIterator($db, $result);
				//assuming there can only be one row.
				$values = array();
				foreach ($it as $row) {
					foreach ($columnList as $column) {
						$values[$column] = $row->get($column);
					}
				}
				$moduleFields = $meta->getModuleFields();
				foreach ($moduleFields as $fieldName => $webserviceField) {
					if (isset($values[$fieldColumnMapping[$fieldName]]) &&
						$values[$fieldColumnMapping[$fieldName]] !== null) {
						$fieldtype = $webserviceField->getFieldDataType();
						if (strcasecmp($fieldtype, 'reference') === 0) {
							$details = $webserviceField->getReferenceList();
							if (count($details)==1) {
								$referencedObjectHandler = vtws_getModuleHandlerFromName($details[0], $this->user);
							} else {
								$type = getSalesEntityType($values[$fieldColumnMapping[$fieldName]]);
								$referencedObjectHandler = vtws_getModuleHandlerFromName($type, $this->user);
							}
							$referencedObjectMeta = $referencedObjectHandler->getMeta();
							$values[$fieldColumnMapping[$fieldName]] =
								$referencedObjectMeta->getName(vtws_getId($referencedObjectMeta->getEntityId(), $values[$fieldColumnMapping[$fieldName]]));
						} elseif (strcasecmp($fieldtype, 'owner') === 0) {
							$referencedObjectHandler = vtws_getModuleHandlerFromName(vtws_getOwnerType($values[$fieldColumnMapping[$fieldName]]), $this->user);
							$referencedObjectMeta = $referencedObjectHandler->getMeta();
							$values[$fieldColumnMapping[$fieldName]] =
								$referencedObjectMeta->getName(vtws_getId($referencedObjectMeta->getEntityId(), $values[$fieldColumnMapping[$fieldName]]));
						} elseif (strcasecmp($fieldtype, 'picklist') === 0 || $fieldName== 'salutationtype') {
							$values[$fieldColumnMapping[$fieldName]] = getTranslatedString($values[$fieldColumnMapping[$fieldName]], $this->module);
						} elseif (strcasecmp($fieldtype, 'datetime') === 0) {
							$values[$fieldColumnMapping[$fieldName]] = $values[$fieldColumnMapping[$fieldName]] .' '. DateTimeField::getDBTimeZone();
						} elseif (strcasecmp($fieldtype, 'currency') === 0 || strcasecmp($fieldtype, 'double') === 0) {
							$currencyField = new CurrencyField($values[$fieldColumnMapping[$fieldName]]);
							$values[$fieldColumnMapping[$fieldName]] = $currencyField->getDisplayValue(null, true);
						} elseif ($webserviceField->getUIType() == 69) {
							$query = 'select vtiger_attachments.name, vtiger_attachments.type, vtiger_attachments.attachmentsid, vtiger_attachments.path
									from vtiger_attachments
									inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
									inner join vtiger_seattachmentsrel on vtiger_attachments.attachmentsid=vtiger_seattachmentsrel.attachmentsid
									where (vtiger_crmentity.setype LIKE "%Image" or vtiger_crmentity.setype LIKE "%Attachment")
									  and deleted=0 and vtiger_seattachmentsrel.crmid=?';
							$params = array($this->recordId);
							if (!empty($values[$fieldColumnMapping[$fieldName]])) {
								$query .= ' and vtiger_attachments.name = ?';
								$params[] = $values[$fieldColumnMapping[$fieldName]];
							}
							$result_image = $db->pquery($query, $params);
							if ($db->num_rows($result_image)>0) {
								$img = $db->fetch_array($result_image);
								$fullpath = $site_URL.'/'.$img['path'].$img['attachmentsid'].'_'.$img['name'];
								$values[$fieldColumnMapping[$fieldName].'_fullpath'] = $fullpath;
								$values[$fieldColumnMapping[$fieldName]] = $img['name'];
								$imagefound = true;
							}
						}
					}
				}
				foreach ($columnList as $column) {
					if ($imagefound) {
						$needle = '$'.strtolower($this->module)."-$column".'_fullpath$';
						$this->processedDescription = str_replace($needle, $values[$column.'_fullpath'], $this->processedDescription);
					}
					$needle = '$'.strtolower($this->module)."-$column$";
					$this->processedDescription = str_replace($needle, $values[$column], $this->processedDescription);
				}
			}
		}
		$this->processed = true;
	}

	public function getProcessedDescription() {
		if (!$this->processed) {
			$this->process();
		}
		return $this->processedDescription;
	}
}
?>
