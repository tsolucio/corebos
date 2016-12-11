<?php
/*+*******************************************************************************
 *  The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
require_once 'data/CRMEntity.php';
require_once 'modules/CustomView/CustomView.php';
require_once 'include/Webservices/Utils.php';
require_once 'include/Webservices/RelatedModuleMeta.php';

/**
 * QueryGenerator: class to obtain SQL queries from CRM objects
 *
 * @author MAK
 * @modified Joe Bordes
 */
class QueryGenerator {
	private $module;
	private $customViewColumnList;
	private $stdFilterList;
	private $conditionals;
	private $manyToManyRelatedModuleConditions;
	private $groupType;
	private $whereFields;
	/**
	 * @var VtigerCRMObjectMeta
	 */
	private $meta;
	/**
	 * @var Users
	 */
	private $user;
	private $advFilterList;
	private $fields;
	private $referenceModuleMetaInfo;
	private $moduleNameFields;
	private $referenceFieldInfoList;
	private $referenceFieldList;
	private $referenceFieldNameList;
	private $referenceFields;
	private $ownerFields;
	private $columns;
	private $fromClause;
	private $whereClause;
	private $query;
	private $groupInfo;
	public $conditionInstanceCount;
	private $conditionalWhere;
	public static $AND = 'AND';
	public static $OR = 'OR';
	private $customViewFields;

	public function __construct($module, $user) {
		$db = PearDatabase::getInstance();
		$this->module = $module;
		$this->customViewColumnList = null;
		$this->stdFilterList = null;
		$this->conditionals = array();
		$this->user = $user;
		$this->advFilterList = null;
		$this->fields = array();
		$this->referenceModuleMetaInfo = array();
		$this->moduleNameFields = array();
		$this->whereFields = array();
		$this->groupType = self::$AND;
		$this->meta = $this->getMeta($module);
		$this->moduleNameFields[$module] = $this->meta->getNameFields();
		$this->referenceFieldInfoList = $this->meta->getReferenceFieldDetails();
		$this->referenceFieldList = array_keys($this->referenceFieldInfoList);;
		$this->ownerFields = $this->meta->getOwnerFields();
		$this->columns = null;
		$this->fromClause = null;
		$this->whereClause = null;
		$this->query = null;
		$this->conditionalWhere = null;
		$this->groupInfo = '';
		$this->manyToManyRelatedModuleConditions = array();
		$this->conditionInstanceCount = 0;
		$this->customViewFields = array();
		$this->setReferenceFields();
	}

	/**
	 *
	 * @param String:ModuleName $module
	 * @return EntityMeta
	 */
	public function getMeta($module) {
		$db = PearDatabase::getInstance();
		if (empty($this->referenceModuleMetaInfo[$module])) {
			$handler = vtws_getModuleHandlerFromName($module, $this->user);
			$meta = $handler->getMeta();
			$this->referenceModuleMetaInfo[$module] = $meta;
			$this->moduleNameFields[$module] = $meta->getNameFields();
		}
		return $this->referenceModuleMetaInfo[$module];
	}

	public function reset() {
		$this->fromClause = null;
		$this->whereClause = null;
		$this->columns = null;
		$this->query = null;
	}

	public function setFields($fields) {
		$this->fields = $fields;
		$this->setReferenceFields();
	}

	// Adding support for reference module fields
	public function setReferenceFields() {
		global $current_user;
		$this->referenceFieldNameList = array();
		$this->referenceFields = array();
		if(isset($this->referenceModuleField)) {
			foreach ($this->referenceModuleField as $index=>$conditionInfo) {
				$refmod = $conditionInfo['relatedModule'];
				if (!vtlib_isEntityModule($refmod)) continue; // reference to a module without fields
				$handler = vtws_getModuleHandlerFromName($refmod, $current_user);
				$meta = $handler->getMeta();
				$fields = $meta->getModuleFields();
				foreach ($fields as $fname => $finfo) {
					if ($fname=='roleid') continue;
					$this->referenceFieldNameList[] = $fname;
					$this->referenceFieldNameList[] = $refmod.'.'.$fname;
					if ($fname==$conditionInfo['fieldName']) {
						$this->referenceFields[$conditionInfo['referenceField']][$refmod][$fname] = $finfo;
					}
				}
			}
		}
		if (count($this->referenceFieldInfoList)>0 and count($this->fields)>0) {
			foreach ($this->referenceFieldInfoList as $fld => $mods) {
				if ($fld=='modifiedby') $fld = 'assigned_user_id';
				foreach ($mods as $module) {
					if (!vtlib_isEntityModule($module)) continue; // reference to a module without fields
					$handler = vtws_getModuleHandlerFromName($module, $current_user);
					$meta = $handler->getMeta();
					$fields = $meta->getModuleFields();
					foreach ($fields as $fname => $finfo) {
						if ($fname=='roleid') continue;
						$this->referenceFieldNameList[] = $fname;
						$this->referenceFieldNameList[] = $module.'.'.$fname;
						if (in_array($fname, $this->fields) or in_array($module.'.'.$fname, $this->fields)) {
							$this->referenceFields[$fld][$module][$fname] = $finfo;
						}
					}
				}
			}
		}
	}

	public function getCustomViewFields() {
		return $this->customViewFields;
	}

	public function getFields() {
		return $this->fields;
	}

	public function getWhereFields() {
		return $this->whereFields;
	}

	public function addWhereField($fieldName) {
		$this->whereFields[] = $fieldName;
	}

	public function getOwnerFieldList() {
		return $this->ownerFields;
	}

	public function getModuleNameFields($module) {
		return $this->moduleNameFields[$module];
	}

	public function getReferenceFieldList() {
		return $this->referenceFieldList;
	}

	public function getReferenceFieldInfoList() {
		return $this->referenceFieldInfoList;
	}

	public function getReferenceFieldNameList() {
		return $this->referenceFieldNameList;
	}

	public function getReferenceFields() {
		return $this->referenceFields;
	}

	public function getReferenceField($fieldName,$returnName=true,$alias=true) {
		if (strpos($fieldName, '.')) {
			list($fldmod,$fldname) = explode('.',$fieldName);
		} else {
			$fldmod = '';
			$fldname = $fieldName;
		}
		$field = '';
		if ($fldmod == '') {  // not FQN > we have to look for it
			foreach ($this->referenceFieldInfoList as $fld => $mods) {
				if ($fld=='modifiedby') $fld=='assigned_user_id';
				foreach ($mods as $mname) {
					if (!empty($this->referenceFields[$fld][$mname][$fldname])) {
						$field = $this->referenceFields[$fld][$mname][$fldname];
						if ($returnName) {
							if ($mname=='Users') {
								return $field->getTableName().'.'.$fldname;
							} else {
								if($fldname=='assigned_user_id' && strstr($field->getTableName(),"vtiger_crmentity")) {
									$fldname='smownerid as smowner'.strtolower(getTabModuleName($field->getTabId()));
								} else {
									if ($alias) {
										$fldname=$field->getColumnName().' as '.strtolower(getTabModuleName($field->getTabId())).$field->getColumnName();
									} else {
										$fldname=$field->getColumnName();
									}
								}
								return $field->getTableName().$fld.'.'.$fldname;
							}
						} else {
							return $field;
						}
					}
				}
			}
		} else {  // FQN
			foreach ($this->referenceFieldInfoList as $fld => $mods) {
				if ($fld=='modifiedby') $fld = 'assigned_user_id';
				if (!empty($this->referenceFields[$fld][$fldmod][$fldname])) {
					$field = $this->referenceFields[$fld][$fldmod][$fldname];
					if ($returnName) {
						if ($fldmod=='Users') {
							return $field->getTableName().'.'.$fldname;
						} else {
							if($fldname=='assigned_user_id' && strstr($field->getTableName(),"vtiger_crmentity")) {
								$fldname='smownerid as smowner'.strtolower(getTabModuleName($field->getTabId()));
							} else {
								if ($alias) {
									$fldname=$field->getColumnName().' as '.strtolower(getTabModuleName($field->getTabId())).$field->getColumnName();
								} else {
									$fldname=$field->getColumnName();
								}
							}
							return $field->getTableName().$fld.'.'.$fldname;
						}
					} else {
						return $field;
					}
				}
			}
		}
		return null;
	}

	public function getModule () {
		return $this->module;
	}

	public function getModuleFields() {
	$moduleFields = $this->meta->getModuleFields();

	$module = $this->getModule();
	if($module == 'Calendar') {
		$eventmoduleMeta = $this->getMeta('Events');
		$eventModuleFieldList = $eventmoduleMeta->getModuleFields();
		$moduleFields = array_merge($moduleFields, $eventModuleFieldList);
	}
	return $moduleFields;
	}

	public function getConditionalWhere() {
		return $this->conditionalWhere;
	}

	public function getDefaultCustomViewQuery() {
		$customView = new CustomView($this->module);
		$viewId = $customView->getViewId($this->module);
		return $this->getCustomViewQueryById($viewId);
	}

	public function initForDefaultCustomView() {
		$customView = new CustomView($this->module);
		$viewId = $customView->getViewId($this->module);
		$this->initForCustomViewById($viewId);
	}

	public function initForCustomViewById($viewId) {
		$customView = new CustomView($this->module);
		$this->customViewColumnList = $customView->getColumnsListByCvid($viewId);
		$viewfields = array();
		foreach ($this->customViewColumnList as $customViewColumnInfo) {
			$details = explode(':', $customViewColumnInfo);
			if(empty($details[2]) && $details[1] == 'crmid' && $details[0] == 'vtiger_crmentity') {
				$name = 'id';
				$this->customViewFields[] = $name;
			} else {
				$minfo = explode('_', $details[3]);
				if ($minfo[0]==$this->module or ($minfo[0]=='Notes' and $this->module=='Documents')) {
					$viewfields[] = $details[2];
				} else {
					$viewfields[] = $minfo[0].'.'.$details[2];
				}
				$this->customViewFields[] = $details[2];
			}
		}

		if($this->module == 'Calendar' && !in_array('activitytype', $viewfields)) {
			$viewfields[] = 'activitytype';
		}

		if($this->module == 'Documents' and in_array('filename', $viewfields)) {
			if(!in_array('filelocationtype', $viewfields)) {
				$viewfields[] = 'filelocationtype';
			}
			if(!in_array('filestatus', $viewfields)) {
				$viewfields[] = 'filestatus';
			}
		}
		if(in_array('Documents.filename', $viewfields) and !in_array('Documents.note_no', $viewfields)) {
			$viewfields[] = 'Documents.note_no';
		}
		$viewfields[] = 'id';
		$this->setFields($viewfields);

		$this->stdFilterList = $customView->getStdFilterByCvid($viewId);
		$this->advFilterList = $customView->getAdvFilterByCvid($viewId);

		if(is_array($this->stdFilterList)) {
			$value = array();
			if(!empty($this->stdFilterList['columnname'])) {
				$this->startGroup('');
				$name = explode(':',$this->stdFilterList['columnname']);
				$name = $name[2];
				$value[] = $this->fixDateTimeValue($name, $this->stdFilterList['startdate']);
				$value[] = $this->fixDateTimeValue($name, $this->stdFilterList['enddate'], false);
				$this->addCondition($name, $value, 'BETWEEN');
			}
		}
		if($this->conditionInstanceCount <= 0 && is_array($this->advFilterList) && count($this->advFilterList) > 0) {
			$this->startGroup('');
		} elseif($this->conditionInstanceCount > 0 && is_array($this->advFilterList) && count($this->advFilterList) > 0) {
			$this->addConditionGlue(self::$AND);
		}
		if(is_array($this->advFilterList) && count($this->advFilterList) > 0) {
			foreach ($this->advFilterList as $groupindex=>$groupcolumns) {
				$filtercolumns = $groupcolumns['columns'];
				if(count($filtercolumns) > 0) {
					$this->startGroup('');
					foreach ($filtercolumns as $index=>$filter) {
						$name = explode(':',$filter['columnname']);
						$mlbl = explode('_', $name[3]);
						$mname = $mlbl[0];
						if(empty($name[2]) && $name[1] == 'crmid' && $name[0] == 'vtiger_crmentity') {
							$name = $this->getSQLColumn('id');
						} else {
							$name = $name[2];
						}
						if ($mname==$this->getModule()) {
							$this->addCondition($name, $filter['value'], $filter['comparator']);
						} else {
							$reffld = '';
							foreach ($this->referenceFieldInfoList as $rfld => $refmods) {
								if (in_array($mname, $refmods)) {
									$reffld = $rfld;
									break;
								}
							}
							$this->addReferenceModuleFieldCondition($mname, $rfld, $name, $filter['value'], $filter['comparator']);
						}
						$columncondition = $filter['column_condition'];
						if(!empty($columncondition)) {
							$this->addConditionGlue($columncondition);
						}
					}
					$this->endGroup();
					$groupConditionGlue = $groupcolumns['condition'];
					if(!empty($groupConditionGlue))
						$this->addConditionGlue($groupConditionGlue);
				}
			}
		}
		if($this->conditionInstanceCount > 0) {
			$this->endGroup();
		}
	}

	public function getCustomViewQueryById($viewId) {
		$this->initForCustomViewById($viewId);
		return $this->getQuery();
	}

	public function getQuery($distinct=false) {
		if(empty($this->query)) {
			$conditionedReferenceFields = array();
			$allFields = array_merge($this->whereFields,$this->fields);
			foreach ($allFields as $fieldName) {
				if(in_array($fieldName,$this->referenceFieldList)) {
					$moduleList = $this->referenceFieldInfoList[$fieldName];
					foreach ($moduleList as $module) {
						if(empty($this->moduleNameFields[$module])) {
							$meta = $this->getMeta($module);
						}
					}
				} elseif(in_array($fieldName, $this->ownerFields )) {
					$meta = $this->getMeta('Users');
					$meta = $this->getMeta('Groups');
				}
			}

			$query  = $this->getSelectClauseColumnSQL();
			$query .= $this->getFromClause();
			$query .= $this->getWhereClause();
			list($specialPermissionWithDuplicateRows,$cached) = VTCacheUtils::lookupCachedInformation('SpecialPermissionWithDuplicateRows');
			$query = 'SELECT '.(($distinct or $specialPermissionWithDuplicateRows) ? 'DISTINCT ' : '') . $query;
			$this->query = $query;
			return $query;
		} else {
			return $this->query;
		}
	}

	public function getSQLColumn($name,$alias=true) {
		if ($name == 'id') {
			$baseTable = $this->meta->getEntityBaseTable();
			$moduleTableIndexList = $this->meta->getEntityTableIndexList();
			$baseTableIndex = $moduleTableIndexList[$baseTable];
			return $baseTable.'.'.$baseTableIndex;
		}

		$moduleFields = $this->getModuleFields();
		if (!empty($moduleFields[$name])) {
			$field = $moduleFields[$name];
		} elseif($this->referenceFieldInfoList) { // Adding support for reference module fields
			return $this->getReferenceField($name,true,$alias);
		}
		if(empty($field)) return '';
		//TODO optimization to eliminate one more lookup of name, in case the field refers to only
		//one module or is of type owner.
		$column = $field->getColumnName();
		return $field->getTableName().'.'.$column;
	}

	public function getSelectClauseColumnSQL(){
		$columns = array();
		$moduleFields = $this->getModuleFields();
		$accessibleFieldList = array_keys($moduleFields);
		$accessibleFieldList[] = 'id';
		if($this->referenceFieldInfoList) { // Adding support for reference module fields
			$accessibleFieldList = array_merge($this->referenceFieldNameList,$accessibleFieldList);
		}
		$this->fields = array_intersect($this->fields, $accessibleFieldList);
		foreach ($this->fields as $field) {
			$sql = $this->getSQLColumn($field);
			$columns[] = $sql;

			//To merge date and time fields
			if($this->meta->getEntityName() == 'Calendar' && ($field == 'date_start' || $field == 'due_date' || $field == 'taskstatus' || $field == 'eventstatus')) {
				if($field=='date_start') {
					$timeField = 'time_start';
					$sql = $this->getSQLColumn($timeField);
				} else if ($field == 'due_date') {
					$timeField = 'time_end';
					$sql = $this->getSQLColumn($timeField);
				} else if ($field == 'taskstatus' || $field == 'eventstatus') {
					//In calendar list view, Status value = Planned is not displaying
					$sql = "CASE WHEN (vtiger_activity.status not like '') THEN vtiger_activity.status ELSE vtiger_activity.eventstatus END AS ";
					if ( $field == 'taskstatus') {
						$sql .= "status";
					} else {
						$sql .= $field;
					}
				}
				$columns[] = $sql;
			}
		}
		$this->columns = implode(', ',$columns);
		return $this->columns;
	}

	public function getFromClause() {
		global $current_user;
		if(!empty($this->query) || !empty($this->fromClause)) {
			return $this->fromClause;
		}
		$baseModule = $this->getModule();
		$moduleFields = $this->getModuleFields();
		$tableList = array();
		$tableJoinMapping = array();
		$tableJoinCondition = array();

		$moduleTableIndexList = $this->meta->getEntityTableIndexList();
		foreach ($this->fields as $fieldName) {
			if ($fieldName == 'id' or empty($moduleFields[$fieldName])) {
				continue;
			}

			$field = $moduleFields[$fieldName];
			$baseTable = $field->getTableName();
			$tableIndexList = $this->meta->getEntityTableIndexList();
			$baseTableIndex = $tableIndexList[$baseTable];
			if($field->getFieldDataType() == 'reference') {
				$moduleList = $this->referenceFieldInfoList[$fieldName];
				$tableJoinMapping[$field->getTableName()] = 'INNER JOIN';
				foreach($moduleList as $module) {
					if($module == 'Users' && $baseModule != 'Users') {
						$tableJoinCondition[$fieldName]['vtiger_users'.$fieldName] = $field->getTableName().
							".".$field->getColumnName()." = vtiger_users".$fieldName.".id";
						$tableJoinCondition[$fieldName]['vtiger_groups'.$fieldName] = $field->getTableName().
							".".$field->getColumnName()." = vtiger_groups".$fieldName.".groupid";
						$tableJoinMapping['vtiger_users'.$fieldName] = 'LEFT JOIN vtiger_users AS';
						$tableJoinMapping['vtiger_groups'.$fieldName] = 'LEFT JOIN vtiger_groups AS';
					}
				}
			} elseif($field->getFieldDataType() == 'owner') {
				$tableList['vtiger_users'] = 'vtiger_users';
				$tableList['vtiger_groups'] = 'vtiger_groups';
				$tableJoinMapping['vtiger_users'] = 'LEFT JOIN';
				$tableJoinMapping['vtiger_groups'] = 'LEFT JOIN';
			}
			$tableList[$field->getTableName()] = $field->getTableName();
			$tableJoinMapping[$field->getTableName()] = $this->meta->getJoinClause($field->getTableName());
		}
		$baseTable = $this->meta->getEntityBaseTable();
		$moduleTableIndexList = $this->meta->getEntityTableIndexList();
		$baseTableIndex = $moduleTableIndexList[$baseTable];
		foreach ($this->whereFields as $fieldName) {
			if(empty($fieldName)) {
				continue;
			}
			if(empty($moduleFields[$fieldName])) {
				// not accessible field.
				continue;
			}
			$field = $moduleFields[$fieldName];
			$baseTable = $field->getTableName();
			// When a field is included in Where Clause, but not in Select Clause, and the field table is not base table,
			// The table will not be present in tablesList and hence needs to be added to the list.
			if(empty($tableList[$baseTable])) {
				$tableList[$baseTable] = $field->getTableName();
				$tableJoinMapping[$baseTable] = $this->meta->getJoinClause($field->getTableName());
			}
			if($field->getFieldDataType() == 'reference') {
				$moduleList = $this->referenceFieldInfoList[$fieldName];
				// This is special condition as the data is not stored in the base table,
				// If empty search is performed on this field then it fails to retrieve any information.
				if ($fieldName == 'parent_id' && $field->getTableName() == 'vtiger_seactivityrel') {
					$tableJoinMapping[$field->getTableName()] = 'LEFT JOIN';
				} else if ($fieldName == 'contact_id' && $field->getTableName() == 'vtiger_cntactivityrel') {
					$tableJoinMapping[$field->getTableName()] = "LEFT JOIN";
				} else {
					$tableJoinMapping[$field->getTableName()] = 'INNER JOIN';
				}
				foreach($moduleList as $module) {
					$meta = $this->getMeta($module);
					$nameFields = $this->moduleNameFields[$module];
					$nameFieldList = explode(',',$nameFields);
					foreach ($nameFieldList as $index=>$column) {
						$joinas = 'LEFT JOIN';
						// for non admin user users module is inaccessible.
						// so need hard code the tablename.
						if($module == 'Users' && $baseModule != 'Users') {
							$referenceTable = 'vtiger_users'.$fieldName;
							$referenceTableIndex = 'id';
							$joinas = 'LEFT JOIN vtiger_users AS';
						} else {
							$referenceField = $meta->getFieldByColumnName($column);
							if (!$referenceField) continue;
							$referenceTable = $referenceField->getTableName();
							$tableIndexList = $meta->getEntityTableIndexList();
							$referenceTableIndex = $tableIndexList[$referenceTable];
						}
						if(isset($moduleTableIndexList[$referenceTable])) {
							$referenceTableName = "$referenceTable $referenceTable$fieldName";
							$referenceTable = "$referenceTable$fieldName";
						} else {
							$referenceTableName = $referenceTable;
						}
						//should always be left join for cases where we are checking for null
						//reference field values.
						if(!array_key_exists($referenceTable, $tableJoinMapping)) { // table already added in from clause
						$tableJoinMapping[$referenceTableName] = $joinas;
						$tableJoinCondition[$fieldName][$referenceTableName] = $baseTable.'.'.
							$field->getColumnName().' = '.$referenceTable.'.'.$referenceTableIndex;
						}
					}
				}
			} elseif($field->getFieldDataType() == 'owner') {
				$tableList['vtiger_users'] = 'vtiger_users';
				$tableList['vtiger_groups'] = 'vtiger_groups';
				$tableJoinMapping['vtiger_users'] = 'LEFT JOIN';
				$tableJoinMapping['vtiger_groups'] = 'LEFT JOIN';
			} else {
				$tableList[$field->getTableName()] = $field->getTableName();
				$tableJoinMapping[$field->getTableName()] = $this->meta->getJoinClause($field->getTableName());
			}
		}

		$defaultTableList = $this->meta->getEntityDefaultTableList();
		foreach ($defaultTableList as $table) {
			if(!in_array($table, $tableList)) {
				$tableList[$table] = $table;
				$tableJoinMapping[$table] = 'INNER JOIN';
			}
		}
		$ownerFields = $this->meta->getOwnerFields();
		if (count($ownerFields) > 0) {
			$ownerField = $ownerFields[0];
		}
		$baseTable = $this->meta->getEntityBaseTable();
		$sql = " FROM $baseTable ";
		unset($tableList[$baseTable]);
		foreach ($defaultTableList as $tableName) {
			$sql .= " $tableJoinMapping[$tableName] $tableName ON $baseTable.".
					"$baseTableIndex = $tableName.$moduleTableIndexList[$tableName]";
			unset($tableList[$tableName]);
		}
		foreach ($tableList as $tableName) {
			if($tableName == 'vtiger_users') {
				$field = $moduleFields[$ownerField];
				$sql .= " $tableJoinMapping[$tableName] $tableName ON ".$field->getTableName().".".
					$field->getColumnName()." = $tableName.id";
			} elseif($tableName == 'vtiger_groups') {
				$field = $moduleFields[$ownerField];
				$sql .= " $tableJoinMapping[$tableName] $tableName ON ".$field->getTableName().".".
					$field->getColumnName()." = $tableName.groupid";
			} else {
				$sql .= " $tableJoinMapping[$tableName] $tableName ON $baseTable.".
					"$baseTableIndex = $tableName.$moduleTableIndexList[$tableName]";
			}
		}

		if( $this->meta->getTabName() == 'Documents') {
			$tableJoinCondition['folderid'] = array(
				'vtiger_attachmentsfolder'=>"$baseTable.folderid = vtiger_attachmentsfolder.folderid"
			);
			$tableJoinMapping['vtiger_attachmentsfolder'] = 'LEFT JOIN';
		}

		$alias_count=2;
		foreach ($tableJoinCondition as $fieldName=>$conditionInfo) {
			foreach ($conditionInfo as $tableName=>$condition) {
				if(!empty($tableList[$tableName])) {
					$tableNameAlias = $tableName.$alias_count;
					$alias_count++;
					$condition = str_replace($tableName, $tableNameAlias, $condition);
				} else {
					$tableNameAlias = '';
				}
				$sql .= " $tableJoinMapping[$tableName] $tableName $tableNameAlias ON $condition";
			}
		}

		foreach ($this->manyToManyRelatedModuleConditions as $conditionInfo) {
			$relatedModuleMeta = RelatedModuleMeta::getInstance($this->meta->getTabName(),
					$conditionInfo['relatedModule']);
			$relationInfo = $relatedModuleMeta->getRelationMeta();
			$relatedModule = $this->meta->getTabName();
			$sql .= ' INNER JOIN '.$relationInfo['relationTable']." ON ".
			$relationInfo['relationTable'].".$relationInfo[$relatedModule]=$baseTable.$baseTableIndex";
		}

		// Adding support for conditions on reference module fields
		if(count($this->referenceFieldInfoList)>0) {
			$alreadyinfrom = array_keys($tableJoinMapping);
			$alreadyinfrom[] = $baseTable;
			$referenceFieldTableList = array();
			if (isset($this->referenceModuleField) and is_array($this->referenceModuleField)) {
			foreach ($this->referenceModuleField as $index=>$conditionInfo) {
				if ($conditionInfo['relatedModule'] == 'Users' && $baseModule != 'Users'
				 && !in_array('vtiger_users', $referenceFieldTableList) && !in_array('vtiger_users', $tableList)) {
					$sql .= ' LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid ';
					$referenceFieldTableList[] = 'vtiger_users';
					$sql .= ' LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid ';
					$referenceFieldTableList[] = 'vtiger_groups';
					continue;
				}
				$handler = vtws_getModuleHandlerFromName($conditionInfo['relatedModule'], $current_user);
				$meta = $handler->getMeta();
				$reltableList = $meta->getEntityTableIndexList();
				$fieldName = $conditionInfo['fieldName'];
				$referenceFieldObject = $moduleFields[$conditionInfo['referenceField']];
				$fields = $meta->getModuleFields();
				if ($fieldName=='id') {
					$tableName = $meta->getEntityBaseTable();
				} else {
					if (empty($fields[$fieldName])) continue;
					$fieldObject = $fields[$fieldName];
					$tableName = $fieldObject->getTableName();
				}

				if(!in_array($tableName, $referenceFieldTableList)) {
					if($referenceFieldObject->getFieldName() == 'parent_id' && ($this->getModule() == 'Calendar' || $this->getModule() == 'Events')) {
						$joinclause = 'LEFT JOIN vtiger_seactivityrel ON vtiger_seactivityrel.activityid = vtiger_activity.activityid';
						$referenceFieldTableList[] = 'vtiger_seactivityrel';
						if (strpos($sql, $joinclause)===false)
							$sql .= " $joinclause ";
					}
					if($referenceFieldObject->getFieldName() == 'contact_id' && ($this->getModule() == 'Calendar' || $this->getModule() == 'Events')) {
						$joinclause = 'LEFT JOIN vtiger_cntactivityrel ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid';
						$referenceFieldTableList[] = 'vtiger_cntactivityrel';
						if (strpos($sql, $joinclause)===false)
							$sql .= " $joinclause ";
					}
					if($this->getModule() == 'Emails') {
						$joinclause = 'INNER JOIN vtiger_emaildetails ON vtiger_activity.activityid = vtiger_emaildetails.emailid';
						$referenceFieldTableList[] = 'vtiger_emaildetails';
						if (strpos($sql, $joinclause)===false)
							$sql .= " $joinclause ";
					}
					$sql .= " LEFT JOIN ".$tableName.' AS '.$tableName.$conditionInfo['referenceField'].' ON '.
						$tableName.$conditionInfo['referenceField'].'.'.$reltableList[$tableName].'='.
						$referenceFieldObject->getTableName().'.'.$referenceFieldObject->getColumnName();
					$referenceFieldTableList[] = $tableName;
				}
			}}
			foreach ($this->fields as $fieldName) {
				if ($fieldName == 'id' or !empty($moduleFields[$fieldName])) {
					continue;
				}
				if (strpos($fieldName, '.')) {
					list($fldmod,$fldname) = explode('.',$fieldName);
				} else {
					$fldmod = '';
					$fldname = $fieldName;
				}
				$field = '';
				if ($fldmod == '') {  // not FQN > we have to look for it
					foreach ($this->referenceFieldInfoList as $fld => $mods) {
						if ($fld=='modifiedby' or $fld == 'assigned_user_id') continue;
						foreach ($mods as $mname) {
							if (!empty($this->referenceFields[$fld][$mname][$fldname])) {
								$handler = vtws_getModuleHandlerFromName($mname, $current_user);
								$meta = $handler->getMeta();
								$reltableList = $meta->getEntityTableIndexList();
								$referenceFieldObject = $this->referenceFields[$fld][$mname][$fldname];
								$tableName = $referenceFieldObject->getTableName();
								if(!in_array($tableName, $referenceFieldTableList)) {
									if(($referenceFieldObject->getFieldName() == 'parent_id' || $fld == 'parent_id') && ($this->getModule() == 'Calendar' || $this->getModule() == 'Events')) {
										$joinclause = 'LEFT JOIN vtiger_seactivityrel ON vtiger_seactivityrel.activityid = vtiger_activity.activityid';
										if (strpos($sql, $joinclause)===false)
											$sql .= " $joinclause ";
									}
									if(($referenceFieldObject->getFieldName() == 'contact_id' || $fld == 'contact_id') && ($this->getModule() == 'Calendar' || $this->getModule() == 'Events')) {
										$joinclause = 'LEFT JOIN vtiger_cntactivityrel ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid';
										if (strpos($sql, $joinclause)===false)
											$sql .= " $joinclause ";
									}
									$sql .= " LEFT JOIN ".$tableName.' AS '.$tableName.$fld.' ON '.
										$tableName.$fld.'.'.$reltableList[$tableName].'='.$moduleFields[$fld]->getTableName().'.'.$moduleFields[$fld]->getColumnName();
									$referenceFieldTableList[] = $tableName;
								}
								break 2;
							}
						}
					}
				} else {  // FQN
					foreach ($this->referenceFieldInfoList as $fld => $mods) {
						if ($fld=='modifiedby' or $fld == 'assigned_user_id') continue;
						if (!empty($this->referenceFields[$fld][$fldmod][$fldname])) {
							$handler = vtws_getModuleHandlerFromName($fldmod, $current_user);
							$meta = $handler->getMeta();
							$reltableList = $meta->getEntityTableIndexList();
							$referenceFieldObject = $this->referenceFields[$fld][$fldmod][$fldname];
							$tableName = $referenceFieldObject->getTableName();
							if(!in_array($moduleFields[$fld]->getTableName(), array_merge($referenceFieldTableList,$alreadyinfrom))) {
								$fldtname = $moduleFields[$fld]->getTableName();
								$sql .= " LEFT JOIN $fldtname ON $fldtname".'.'.$moduleTableIndexList[$fldtname].'='.$baseTable.'.'.$baseTableIndex;
								$alreadyinfrom[] = $fldtname;
							}
							if(!in_array($tableName, $referenceFieldTableList)) {
								if(($referenceFieldObject->getFieldName() == 'parent_id' || $fld == 'parent_id') && ($this->getModule() == 'Calendar' || $this->getModule() == 'Events')) {
									$joinclause = 'LEFT JOIN vtiger_seactivityrel ON vtiger_seactivityrel.activityid = vtiger_activity.activityid';
									if (strpos($sql, $joinclause)===false)
										$sql .= " $joinclause ";
								}
								if(($referenceFieldObject->getFieldName() == 'contact_id' || $fld == 'contact_id') && ($this->getModule() == 'Calendar' || $this->getModule() == 'Events')) {
									$joinclause = 'LEFT JOIN vtiger_cntactivityrel ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid';
									if (strpos($sql, $joinclause)===false)
										$sql .= " $joinclause ";
								}
								$sql .= " LEFT JOIN ".$tableName.' AS '.$tableName.$fld.' ON '.
									$tableName.$fld.'.'.$reltableList[$tableName].'='.$moduleFields[$fld]->getTableName().'.'.$moduleFields[$fld]->getColumnName();
								$referenceFieldTableList[] = $tableName;
							}
							break;
						}
					}
				}
			}
		}

		$sql .= $this->meta->getEntityAccessControlQuery();
		$this->fromClause = $sql;
		return $sql;
	}

	public function getWhereClause() {
		global $current_user;
		if(!empty($this->query) || !empty($this->whereClause)) {
			return $this->whereClause;
		}
		$db = PearDatabase::getInstance();
		$deletedQuery = $this->meta->getEntityDeletedQuery();
		$sql = '';
		if(!empty($deletedQuery)) {
			$sql .= " WHERE $deletedQuery";
		}
		if($this->conditionInstanceCount > 0) {
			$sql .= ' AND ';
		} elseif(empty($deletedQuery)) {
			$sql .= ' WHERE ';
		}
		$baseModule = $this->getModule();
		$moduleFieldList = $this->getModuleFields();
		$baseTable = $this->meta->getEntityBaseTable();
		$moduleTableIndexList = $this->meta->getEntityTableIndexList();
		$baseTableIndex = $moduleTableIndexList[$baseTable];
		$groupSql = $this->groupInfo;
		$fieldSqlList = array();
		foreach ($this->conditionals as $index=>$conditionInfo) {
			$fieldName = $conditionInfo['name'];
			if ($fieldName=='id') {
				if(empty($conditionInfo['value'])) {
					$conditionInfo['value'] = '0';
				}
				$value = "'".$conditionInfo['value']."'";
				switch($conditionInfo['operator']) {
					case 'e': $sqlOperator = "=";
						break;
					case 'n': $sqlOperator = "<>";
						break;
					case 'l': $sqlOperator = "<";
						break;
					case 'g': $sqlOperator = ">";
						break;
					case 'm': $sqlOperator = "<=";
						break;
					case 'h': $sqlOperator = ">=";
						break;
					case 'i':
					case 'ni':
					case 'nin':
						$sqlOperator = '';
						$vals = array_map(array( $db, 'quote'), $conditionInfo['value']);
						$value = (($conditionInfo['operator']=='ni' or $conditionInfo['operator']=='nin') ? 'NOT ':'').'IN ('.implode(',', $vals).')';
						break;
					default: $sqlOperator = "=";
				}
				$fieldSqlList[$index] = "($baseTable.$baseTableIndex $sqlOperator $value)";
				continue;
			}
			$field = $moduleFieldList[$fieldName];
			if(empty($field) || $conditionInfo['operator'] == 'None') {
				continue;
			}
			$fieldSql = '(';
			$fieldGlue = '';
			$valueSqlList = $this->getConditionValue($conditionInfo['value'], $conditionInfo['operator'], $field);
			if ($conditionInfo['operator']=='exists') {
				$fieldSqlList[$index] = '('.$valueSqlList[0].')';
				continue;
			}
			if(!is_array($valueSqlList)) {
				$valueSqlList = array($valueSqlList);
			}
			foreach ($valueSqlList as $valueSql) {
				if (in_array($fieldName, $this->referenceFieldList)) {
					$moduleList = $this->referenceFieldInfoList[$fieldName];
					foreach($moduleList as $module) {
						$nameFields = $this->moduleNameFields[$module];
						$nameFieldList = explode(',',$nameFields);
						$meta = $this->getMeta($module);
						$columnList = array();
						foreach ($nameFieldList as $column) {
							if($module == 'Users')
								$referenceTable = "vtiger_users".$fieldName;
							else {
								$referenceField = $meta->getFieldByColumnName($column);
								if (!$referenceField) continue;
								$referenceTable = $referenceField->getTableName();
							}
							if(isset($moduleTableIndexList[$referenceTable])) {
								$referenceTable = "$referenceTable$fieldName";
							}
							$columnList[] = "$referenceTable.$column";
						}
						if(count($columnList) > 1) {
							$columnSql = getSqlForNameInDisplayFormat(array('first_name'=>$columnList[0],'last_name'=>$columnList[1]),'Users');
						} else {
							$columnSql = implode('', $columnList);
						}

						$fieldSql .= "$fieldGlue trim($columnSql) $valueSql";
						$fieldGlue = ' OR';
					}
				} elseif (in_array($fieldName, $this->ownerFields)) {
					$concatSql = getSqlForNameInDisplayFormat(array('first_name'=>"vtiger_users.first_name",'last_name'=>"vtiger_users.last_name"), 'Users');
					$fieldSql .= "$fieldGlue (trim($concatSql) $valueSql or "."vtiger_groups.groupname $valueSql)";
				} else {
					if($fieldName == 'birthday' && !$this->isRelativeSearchOperators(
							$conditionInfo['operator'])) {
						$fieldSql .= "$fieldGlue DATE_FORMAT(".$field->getTableName().'.'.
								$field->getColumnName().",'%m%d') ".$valueSql;
					} else {
						$fieldSql .= "$fieldGlue ".$field->getTableName().'.'.
								$field->getColumnName().' '.$valueSql;
					}
				}
				if(($conditionInfo['operator'] == 'n' || $conditionInfo['operator'] == 'k') && ($field->getFieldDataType() == 'owner' || $field->getFieldDataType() == 'picklist') ) {
					$fieldGlue = ' AND';
				} else {
					$fieldGlue = ' OR';
				}
			}
			$fieldSql .= ')';
			$fieldSqlList[$index] = $fieldSql;
		}
		foreach ($this->manyToManyRelatedModuleConditions as $index=>$conditionInfo) {
			$relatedModuleMeta = RelatedModuleMeta::getInstance($this->meta->getTabName(),
					$conditionInfo['relatedModule']);
			$relationInfo = $relatedModuleMeta->getRelationMeta();
			$relatedModule = $this->meta->getTabName();
			$fieldSql = "(".$relationInfo['relationTable'].'.'.
			$relationInfo[$conditionInfo['column']].$conditionInfo['SQLOperator'].
			$conditionInfo['value'].")";
			$fieldSqlList[$index] = $fieldSql;
		}

		// This is added to support reference module fields
		if(isset($this->referenceModuleField)) {
			foreach ($this->referenceModuleField as $index=>$conditionInfo) {
				$handler = vtws_getModuleHandlerFromName($conditionInfo['relatedModule'], $current_user);
				$meta = $handler->getMeta();
				$fieldName = $conditionInfo['fieldName'];
				$fields = $meta->getModuleFields();
				if ($fieldName=='id') {
					$value = "'".$conditionInfo['value']."'";
					switch($conditionInfo['SQLOperator']) {
						case 'e': $sqlOperator = "=";
							break;
						case 'n': $sqlOperator = "<>";
							break;
						case 'l': $sqlOperator = "<";
							break;
						case 'g': $sqlOperator = ">";
							break;
						case 'm': $sqlOperator = "<=";
							break;
						case 'h': $sqlOperator = ">=";
							break;
						case 'i':
						case 'ni':
						case 'nin':
							$sqlOperator = '';
							$vals = array_map(array( $db, 'quote'), $conditionInfo['value']);
							$value = (($conditionInfo['SQLOperator']=='ni' or $conditionInfo['SQLOperator']=='nin') ? 'NOT ':'').'IN ('.implode(',', $vals).')';
							break;
						default: $sqlOperator = "=";
					}
					if(!empty($value)) {
						$fname = $meta->getObectIndexColumn();
						$bTable = $meta->getEntityBaseTable();
						if ($bTable=='vtiger_users') {
							$fieldSqlList[$index] = "(vtiger_users.id $sqlOperator $value or vtiger_groups.groupid $sqlOperator $value)";
						} else {
							$fieldSqlList[$index] = "($bTable".$conditionInfo['referenceField'].".$fname $sqlOperator $value)";
						}
					}
					continue;
				}
				if (empty($fields[$fieldName])) continue;
				$fieldObject = $fields[$fieldName];
				$columnName = $fieldObject->getColumnName();
				$tableName = $fieldObject->getTableName();
				$valueSQL = $this->getConditionValue($conditionInfo['value'], $conditionInfo['SQLOperator'], $fieldObject, $tableName.$conditionInfo['referenceField']);
				if ($conditionInfo['SQLOperator']=='exists') {
					$fieldSqlList[$index] = '('.$valueSQL[0].')';
					continue;
				}
				if ($tableName=='vtiger_users') {
					$reffield = $moduleFieldList[$conditionInfo['referenceField']];
					if ($reffield->getUIType() == '101') {
						$fieldSql = "(".$tableName.$conditionInfo['referenceField'].'.'.$columnName.' '.$valueSQL[0].")";
					} else {
						$fieldSql = "(".$tableName.'.'.$columnName.' '.$valueSQL[0].")";
					}
				} else {
					$fieldSql = "(".$tableName.$conditionInfo['referenceField'].'.'.$columnName.' '.$valueSQL[0].")";
				}
				$fieldSqlList[$index] = $fieldSql;
			}
		}
		// This is needed as there can be condition in different order and there is an assumption in makeGroupSqlReplacements API
		// that it expects the array in an order and then replaces the sql with its the corresponding place
		ksort($fieldSqlList);
		$groupSql = $this->makeGroupSqlReplacements($fieldSqlList, $groupSql);
		if($this->conditionInstanceCount > 0) {
			$this->conditionalWhere = $groupSql;
			$sql .= $groupSql;
		}
		$sql .= " AND $baseTable.$baseTableIndex > 0";
		$this->whereClause = $sql;
		return $sql;
	}

	/**
	 *
	 * @param mixed $value
	 * @param String $operator
	 * @param WebserviceField $field
	 */
	private function getConditionValue($value, $operator, $field, $referenceFieldName='') {
		$operator = strtolower($operator);
		$db = PearDatabase::getInstance();
		$noncommaSeparatedFieldTypes = array('currency','percentage','double','integer','number');

		if(in_array($field->getFieldDataType(), $noncommaSeparatedFieldTypes)) {
			if(is_array($value)) {
				$valueArray = $value;
			} else {
				$valueArray = array($value);
			}
			// if ($field->getFieldDataType() == 'multipicklist' && in_array($operator, array('e', 'n'))) {
				// $valueArray = getCombinations($valueArray);
				// foreach ($valueArray as $key => $value) {
					// $valueArray[$key] = ltrim($value, ' |##| ');
				// }
			// }
		} elseif(is_string($value)) {
			$valueArray = explode(',' , $value);
		} elseif(is_array($value)) {
			$valueArray = $value;
		} else {
			$valueArray = array($value);
		}
		$sql = array();
		if ($operator=='exists') {
			global $current_user,$adb;
			$mid = getTabModuleName($field->getTabId());
			$qg = new QueryGenerator($mid,$current_user);
			$qg->addCondition($field->getFieldName(), $value, 'e');
			$sql[] = 'SELECT EXISTS(SELECT 1 '.$qg->getFromClause().$qg->getWhereClause().')';
			return $sql;
		}
		if ($operator=='i' or $operator=='in' or $operator=='ni' or $operator=='nin') {
			$vals = array_map(array( $db, 'quote'), $valueArray);
			$sql[] = (($operator=='ni' or $operator=='nin') ? ' NOT ':'').'IN ('.implode(',', $vals).')';
			return $sql;
		}
		if($operator == 'between' || $operator == 'bw' || $operator == 'notequal') {
			if($field->getFieldName() == 'birthday') {
				$valueArray[0] = getValidDBInsertDateTimeValue($valueArray[0]);
				$valueArray[1] = getValidDBInsertDateTimeValue($valueArray[1]);
				$sql[] = "BETWEEN DATE_FORMAT(".$db->quote($valueArray[0]).", '%m%d') AND ".
						"DATE_FORMAT(".$db->quote($valueArray[1]).", '%m%d')";
			} else {
				if($this->isDateType($field->getFieldDataType())) {
					$valueArray[0] = getValidDBInsertDateTimeValue($valueArray[0]);
					$valueArray[1] = getValidDBInsertDateTimeValue($valueArray[1]);
				}
				$sql[] = "BETWEEN ".$db->quote($valueArray[0])." AND ". $db->quote($valueArray[1]);
			}
			return $sql;
		}
		$yes = strtolower(getTranslatedString('yes'));
		$no = strtolower(getTranslatedString('no'));
		foreach ($valueArray as $value) {
			if(!$this->isStringType($field->getFieldDataType())) {
				$value = trim($value);
			}
			if ($operator == 'empty' || $operator == 'y') {
				$sql[] = sprintf("IS NULL OR %s = ''", ($referenceFieldName=='' ? $this->getSQLColumn($field->getFieldName(),false) : $referenceFieldName.'.'.$field->getColumnName()));
				continue;
			}
			if($operator == 'ny'){
				$sql[] = sprintf("IS NOT NULL AND %s != ''", ($referenceFieldName=='' ? $this->getSQLColumn($field->getFieldName(),false) : $referenceFieldName.'.'.$field->getColumnName()));
				continue;
			}
			if((strtolower(trim($value)) == 'null') ||
					(trim($value) == '' && !$this->isStringType($field->getFieldDataType())) &&
							($operator == 'e' || $operator == 'n')) {
				if($operator == 'e'){
					$sql[] = "IS NULL";
					continue;
				}
				$sql[] = "IS NOT NULL";
				continue;
			} elseif($field->getFieldDataType() == 'boolean') {
				$value = strtolower($value);
				if ($value == 'yes' or $value == $yes) {
					$value = 1;
				} elseif($value == 'no' or $value == $no) {
					$value = 0;
				}
			} elseif($this->isDateType($field->getFieldDataType())) {
				$value = getValidDBInsertDateTimeValue($value);
				if (empty($value)) {
					$sql[] = 'IS NULL or '.$field->getTableName().'.'.$field->getColumnName()." = ''";
					return $sql;
				}
			} elseif($field->getFieldDataType()=='picklist' || $field->getFieldDataType()=='multipicklist') {
				if(!isValueInPicklist($value,$field->getFieldName()))
					$value = getTranslationKeyFromTranslatedValue($this->module, $value);
			} else if ($field->getFieldDataType() === 'currency') {
				$uiType = $field->getUIType();
				if ($uiType == 72) {
					$value = CurrencyField::convertToDBFormat($value, null, true);
				} elseif ($uiType == 71) {
					$value = CurrencyField::convertToDBFormat($value,$this->user);
				}
			}

			if($field->getFieldName() == 'birthday' && !$this->isRelativeSearchOperators($operator)) {
				$value = "DATE_FORMAT(".$db->quote($value).", '%m%d')";
			} else {
				$value = $db->sql_escape_string($value);
			}

			if(trim($value) == '' && ($operator == 's' || $operator == 'ew' || $operator == 'c')
					&& ($this->isStringType($field->getFieldDataType()) ||
					$field->getFieldDataType() == 'picklist' ||
					$field->getFieldDataType() == 'multipicklist')) {
				$sql[] = "LIKE ''";
				continue;
			}

			if(trim($value) == '' && ($operator == 'k') && $this->isStringType($field->getFieldDataType())) {
				$sql[] = "NOT LIKE ''";
				continue;
			}

			switch($operator) {
				case 'e': $sqlOperator = "=";
					break;
				case 'n': $sqlOperator = "<>";
					break;
				case 's': $sqlOperator = "LIKE";
					$value = "$value%";
					break;
				case 'ew': $sqlOperator = "LIKE";
					$value = "%$value";
					break;
				case 'c': $sqlOperator = "LIKE";
					$value = "%$value%";
					break;
				case 'k': $sqlOperator = "NOT LIKE";
					$value = "%$value%";
					break;
				case 'l': $sqlOperator = "<";
					break;
				case 'g': $sqlOperator = ">";
					break;
				case 'm': $sqlOperator = "<=";
					break;
				case 'h': $sqlOperator = ">=";
					break;
				case 'a': $sqlOperator = ">";
					break;
				case 'b': $sqlOperator = "<";
					break;
			}
			if(!$this->isNumericType($field->getFieldDataType()) &&
					($field->getFieldName() != 'birthday' || ($field->getFieldName() == 'birthday'
							&& $this->isRelativeSearchOperators($operator)))){
				$value = "'$value'";
			}
			if($this->isNumericType($field->getFieldDataType()) && empty($value)) {
				$value = '0';
			}
			$sql[] = "$sqlOperator $value";
		}
		return $sql;
	}

	private function makeGroupSqlReplacements($fieldSqlList, $groupSql) {
		$pos = 0;
		$nextOffset = 0;
		foreach ($fieldSqlList as $index => $fieldSql) {
			$pos = strpos($groupSql, $index.'', $nextOffset);
			if($pos !== false) {
				$beforeStr = substr($groupSql,0,$pos);
				$afterStr = substr($groupSql, $pos + strlen($index));
				$nextOffset = strlen($beforeStr.$fieldSql);
				$groupSql = $beforeStr.$fieldSql.$afterStr;
			}
		}
		return $groupSql;
	}

	private function isRelativeSearchOperators($operator) {
		$nonDaySearchOperators = array('l','g','m','h');
		return in_array($operator, $nonDaySearchOperators);
	}
	private function isNumericType($type) {
		return ($type == 'integer' || $type == 'double' || $type == 'currency');
	}

	private function isStringType($type) {
		return ($type == 'string' || $type == 'text' || $type == 'email' || $type == 'reference' || $type == 'phone');
	}

	private function isDateType($type) {
		return ($type == 'date' || $type == 'datetime');
	}

	public function fixDateTimeValue($name, $value, $first = true) {
		$moduleFields = $this->getModuleFields();
		$field = $moduleFields[$name];
		$type = $field ? $field->getFieldDataType() : false;
		if($type == 'datetime') {
			if(strrpos($value, ' ') === false) {
				if($first) {
					return $value.' 00:00:00';
				}else{
					return $value.' 23:59:59';
				}
			}
		}
		return $value;
	}

	public function addCondition($fieldname,$value,$operator,$glue= null,$newGroup = false,$newGroupType = null) {
		$conditionNumber = $this->conditionInstanceCount++;
		if($glue != null && $conditionNumber > 0)
			$this->addConditionGlue ($glue);

		$this->groupInfo .= "$conditionNumber ";
		$this->whereFields[] = $fieldname;
		$this->reset();
		$this->conditionals[$conditionNumber] = $this->getConditionalArray($fieldname, $value, $operator);
	}

	public function addRelatedModuleCondition($relatedModule,$column, $value, $SQLOperator) {
		$conditionNumber = $this->conditionInstanceCount++;
		$this->groupInfo .= "$conditionNumber ";
		$this->manyToManyRelatedModuleConditions[$conditionNumber] = array('relatedModule'=>
			$relatedModule,'column'=>$column,'value'=>$value,'SQLOperator'=>$SQLOperator);
		$this->setReferenceFields();
	}

	public function addReferenceModuleFieldCondition($relatedModule, $referenceField, $fieldName, $value, $SQLOperator, $glue=null) {
		$conditionNumber = $this->conditionInstanceCount++;
		if($glue != null && $conditionNumber > 0)
			$this->addConditionGlue($glue);

		$this->groupInfo .= "$conditionNumber ";
		$this->referenceModuleField[$conditionNumber] = array('relatedModule'=> $relatedModule,'referenceField'=> $referenceField,'fieldName'=>$fieldName,'value'=>$value,'SQLOperator'=>$SQLOperator);
		$this->setReferenceFields();
	}

	private function getConditionalArray($fieldname,$value,$operator) {
		if(is_string($value)) {
			$value = trim($value);
		} elseif(is_array($value)) {
			$value = array_map(trim, $value);
		}
		return array('name'=>$fieldname,'value'=>$value,'operator'=>$operator);
	}

	public function startGroup($groupType='') {
		$this->groupInfo .= " $groupType (";
	}

	public function endGroup() {
		$this->groupInfo .= ')';
	}

	public function addConditionGlue($glue) {
		$this->groupInfo .= " $glue ";
	}

	public function addUserSearchConditions($input) {
		global $log,$default_charset;
		if(isset($input['searchtype']) and $input['searchtype']=='advance') {

			$advft_criteria = (empty($input['advft_criteria']) ? $_REQUEST['advft_criteria'] : $input['advft_criteria']);
			if(!empty($advft_criteria)) $advft_criteria = json_decode($advft_criteria,true);
			$advft_criteria_groups = (empty($input['advft_criteria_groups']) ? $_REQUEST['advft_criteria_groups'] : $input['advft_criteria_groups']);
			if(!empty($advft_criteria_groups)) $advft_criteria_groups = json_decode($advft_criteria_groups,true);

			if(empty($advft_criteria) || count($advft_criteria) <= 0) {
				return ;
			}

			$advfilterlist = getAdvancedSearchCriteriaList($advft_criteria, $advft_criteria_groups, $this->getModule());

			if(empty($advfilterlist) || count($advfilterlist) <= 0) {
				return ;
			}

			if($this->conditionInstanceCount > 0) {
				$this->startGroup(self::$AND);
			} else {
				$this->startGroup('');
			}
			foreach ($advfilterlist as $groupindex=>$groupcolumns) {
				$filtercolumns = $groupcolumns['columns'];
				if(count($filtercolumns) > 0) {
					$this->startGroup('');
					foreach ($filtercolumns as $index=>$filter) {
						$name = explode(':',$filter['columnname']);
						if(empty($name[2]) && $name[1] == 'crmid' && $name[0] == 'vtiger_crmentity') {
							$name = $this->getSQLColumn('id');
						} else {
							$name = $name[2];
						}
						$this->addCondition($name, $filter['value'], $filter['comparator']);
						$columncondition = $filter['column_condition'];
						if(!empty($columncondition)) {
							$this->addConditionGlue($columncondition);
						}
					}
					$this->endGroup();
					$groupConditionGlue = $groupcolumns['condition'];
					if(!empty($groupConditionGlue))
						$this->addConditionGlue($groupConditionGlue);
				}
			}
			$this->endGroup();
		} elseif($input['type']=='dbrd') {
			if($this->conditionInstanceCount > 0) {
				$this->startGroup(self::$AND);
			} else {
				$this->startGroup('');
			}
			$allConditionsList = $this->getDashBoardConditionList();
			$conditionList = $allConditionsList['conditions'];
			$relatedConditionList = $allConditionsList['relatedConditions'];
			$noOfConditions = count($conditionList);
			$noOfRelatedConditions = count($relatedConditionList);
			foreach ($conditionList as $index=>$conditionInfo) {
				$this->addCondition($conditionInfo['fieldname'], $conditionInfo['value'],
						$conditionInfo['operator']);
				if($index < $noOfConditions - 1 || $noOfRelatedConditions > 0) {
					$this->addConditionGlue(self::$AND);
				}
			}
			foreach ($relatedConditionList as $index => $conditionInfo) {
				$this->addRelatedModuleCondition($conditionInfo['relatedModule'],
						$conditionInfo['conditionModule'], $conditionInfo['finalValue'],
						$conditionInfo['SQLOperator']);
				if($index < $noOfRelatedConditions - 1) {
					$this->addConditionGlue(self::$AND);
				}
			}
			$this->endGroup();
		} else {
			if(isset($input['search_field']) && $input['search_field'] !="") {
				$fieldName=vtlib_purify($input['search_field']);
			} else {
				return ;
			}
			if($this->conditionInstanceCount > 0) {
				$this->startGroup(self::$AND);
			} else {
				$this->startGroup('');
			}
			$moduleFields = $this->getModuleFields();
			$field = $moduleFields[$fieldName];
			$type = $field->getFieldDataType();
			if(isset($input['search_text']) && $input['search_text']!="") {
				// search other characters like "|, ?, ?" by jagi
				$value = $input['search_text'];
				$stringConvert = function_exists(iconv) ? @iconv("UTF-8",$default_charset,$value)
						: $value;
				if(!$this->isStringType($type)) {
					$value=trim($stringConvert);
				}

				if($type == 'picklist') {
					global $currentModule;
					if(!isValueInPicklist($value,$field->getFieldName()))
						$value = getTranslationKeyFromTranslatedValue($currentModule, $value);
				}
				if($type == 'currency') {
					// Some of the currency fields like Unit Price, Total, Sub-total etc of Inventory modules, do not need currency conversion
					if($field->getUIType() == '72') {
						$value = CurrencyField::convertToDBFormat($value, null, true);
					} else {
						$currencyField = new CurrencyField($value);
						if($this->getModule() == 'Potentials' && $fieldName == 'amount') {
							$currencyField->setNumberofDecimals(2);
						}
						$value = $currencyField->getDBInsertedValue();
					}
				}
			}
			if(!empty($input['operator'])) {
				$operator = $input['operator'];
			} elseif(trim(strtolower($value)) == 'null'){
				$operator = 'e';
			} else {
				if(!$this->isNumericType($type) && !$this->isDateType($type)) {
					$operator = 'c';
				} else {
					$operator = 'h';
				}
			}
			$this->addCondition($fieldName, $value, $operator);
			$this->endGroup();
		}
	}

	public function getDashBoardConditionList() {
		if(isset($_REQUEST['leadsource'])) {
			$leadSource = $_REQUEST['leadsource'];
		}
		if(isset($_REQUEST['date_closed'])) {
			$dateClosed = $_REQUEST['date_closed'];
		}
		if(isset($_REQUEST['sales_stage'])) {
			$salesStage = $_REQUEST['sales_stage'];
		}
		if(isset($_REQUEST['closingdate_start'])) {
			$dateClosedStart = $_REQUEST['closingdate_start'];
		}
		if(isset($_REQUEST['closingdate_end'])) {
			$dateClosedEnd = $_REQUEST['closingdate_end'];
		}
		if(isset($_REQUEST['owner'])) {
			$owner = vtlib_purify($_REQUEST['owner']);
		}
		if(isset($_REQUEST['campaignid'])) {
			$campaignId = vtlib_purify($_REQUEST['campaignid']);
		}
		if(isset($_REQUEST['quoteid'])) {
			$quoteId = vtlib_purify($_REQUEST['quoteid']);
		}
		if(isset($_REQUEST['invoiceid'])) {
			$invoiceId = vtlib_purify($_REQUEST['invoiceid']);
		}
		if(isset($_REQUEST['purchaseorderid'])) {
			$purchaseOrderId = vtlib_purify($_REQUEST['purchaseorderid']);
		}

		$conditionList = array();
		if(!empty($dateClosedStart) && !empty($dateClosedEnd)) {
			$conditionList[] = array('fieldname'=>'closingdate', 'value'=>$dateClosedStart, 'operator'=>'h');
			$conditionList[] = array('fieldname'=>'closingdate', 'value'=>$dateClosedEnd, 'operator'=>'m');
		}
		if(!empty($salesStage)) {
			if($salesStage == 'Other') {
				$conditionList[] = array('fieldname'=>'sales_stage', 'value'=>'Closed Won', 'operator'=>'n');
				$conditionList[] = array('fieldname'=>'sales_stage', 'value'=>'Closed Lost', 'operator'=>'n');
			} else {
				$conditionList[] = array('fieldname'=>'sales_stage', 'value'=> $salesStage, 'operator'=>'e');
			}
		}
		if(!empty($leadSource)) {
			$conditionList[] = array('fieldname'=>'leadsource', 'value'=>$leadSource, 'operator'=>'e');
		}
		if(!empty($dateClosed)) {
			$conditionList[] = array('fieldname'=>'closingdate', 'value'=>$dateClosed, 'operator'=>'h');
		}
		if(!empty($owner)) {
			$conditionList[] = array('fieldname'=>'assigned_user_id', 'value'=>$owner, 'operator'=>'e');
		}
		$relatedConditionList = array();
		if(!empty($campaignId)) {
			$relatedConditionList[] = array('relatedModule'=>'Campaigns','conditionModule'=>
				'Campaigns','finalValue'=>$campaignId, 'SQLOperator'=>'=');
		}
		if(!empty($quoteId)) {
			$relatedConditionList[] = array('relatedModule'=>'Quotes','conditionModule'=>
				'Quotes','finalValue'=>$quoteId, 'SQLOperator'=>'=');
		}
		if(!empty($invoiceId)) {
			$relatedConditionList[] = array('relatedModule'=>'Invoice','conditionModule'=>
				'Invoice','finalValue'=>$invoiceId, 'SQLOperator'=>'=');
		}
		if(!empty($purchaseOrderId)) {
			$relatedConditionList[] = array('relatedModule'=>'PurchaseOrder','conditionModule'=>
				'PurchaseOrder','finalValue'=>$purchaseOrderId, 'SQLOperator'=>'=');
		}
		return array('conditions'=>$conditionList,'relatedConditions'=>$relatedConditionList);
	}

	public function initForGlobalSearchByType($type, $value, $operator='s') {
		$fieldList = $this->meta->getFieldNameListByType($type);
		if($this->conditionInstanceCount <= 0) {
			$this->startGroup('');
		} else {
			$this->startGroup(self::$AND);
		}
		$nameFieldList = explode(',',$this->getModuleNameFields($this->module));
		foreach ($nameFieldList as $nameList) {
			$field = $this->meta->getFieldByColumnName($nameList);
			$this->fields[] = $field->getFieldName();
		}
		foreach ($fieldList as $index => $field) {
			$fieldName = $this->meta->getFieldByColumnName($field);
			$this->fields[] = $fieldName->getFieldName();
			if($index > 0) {
				$this->addConditionGlue(self::$OR);
			}
			$this->addCondition($fieldName->getFieldName(), $value, $operator);
		}
		$this->endGroup();
		if(!in_array('id', $this->fields)) {
			$this->fields[] = 'id';
		}
	}

}
?>
