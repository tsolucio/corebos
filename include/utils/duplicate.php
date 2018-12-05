<?php
 /*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Module       : Duplicate Related Record functionality
 *  Version      : 5.4.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

function duplicaterec($currentModule, $record_id, $bmap) {
	global $adb, $current_user;

	$focus = CRMEntity::getInstance($currentModule);
	$focus->retrieve_entity_info($record_id, $currentModule);

	if (is_numeric($bmap)) {
		$cbMapid = $bmap;
	} else {
		//$bmapname = 'BusinessMapping_'.$currentModule.'_DuplicateRelations';
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmap, cbMap::getMapIdByName($bmap));
	}
	// Retrieve relations map
	if ($cbMapid) {
		$cbMap = cbMap::getMapByID($cbMapid);
		$maped_relations = $cbMap->DuplicateRelations()->getRelatedModules();
	} else {
		$maped_relations = array();
	}

	// Duplicate Records that this Record is dependent of
	if ($cbMapid && $cbMap->DuplicateRelations()->DuplicateDirectRelations()) {
		$invmods = getInventoryModules();
		foreach ($focus->column_fields as $fieldname => $value) {
			$sql = 'SELECT * FROM vtiger_field WHERE columnname = ? AND uitype IN (10,51,57,73,76,78,80)';
			$result = $adb->pquery($sql, array($fieldname));
			if ($adb->num_rows($result) == 1 && !empty($value)) {
				$module = getSalesEntityType($value);
				if (in_array($module, $invmods)) {
					continue; // we can't duplicate these
				}
				$handler = vtws_getModuleHandlerFromName($module, $current_user);
				$meta = $handler->getMeta();
				$entity = CRMEntity::getInstance($module);
				$entity->mode='';
				$entity->retrieve_entity_info($value, $module);
				$imageFields = $meta->getImageFields();
				if (count($imageFields)>0) {
					foreach ($imageFields as $imgfld) {
						$_FILES[$imgfld] = array('name'=>'','size'=>0);
						$_REQUEST[$imgfld.'_hidden'] = $entity->column_fields[$imgfld];
					}
					$_REQUEST['__cbisduplicatedfromrecordid'] = $value;
				}
				$entity->column_fields = DataTransform::sanitizeRetrieveEntityInfo($entity->column_fields, $meta);
				$entity->save($module);
				$focus->column_fields[$fieldname] = $entity->id;
				if (count($imageFields)>0) {
					foreach ($imageFields as $imgfld) {
						unset($_FILES[$imgfld], $_REQUEST[$imgfld.'_hidden']);
					}
					unset($_REQUEST['__cbisduplicatedfromrecordid']);
				}
			}
		}
	}

	$handler = vtws_getModuleHandlerFromName($currentModule, $current_user);
	$meta = $handler->getMeta();
	$imageFields = $meta->getImageFields();
	if (count($imageFields)>0) {
		foreach ($imageFields as $imgfld) {
			$_FILES[$imgfld] = array('name'=>'','size'=>0);
			$_REQUEST[$imgfld.'_hidden'] = $focus->column_fields[$imgfld];
		}
		$_REQUEST['__cbisduplicatedfromrecordid'] = $record_id;
	}
	$focus->column_fields = DataTransform::sanitizeRetrieveEntityInfo($focus->column_fields, $meta);
	$focus->saveentity($currentModule); // no workflows for this one => so we don't reenter this process
	if (count($imageFields)>0) {
		foreach ($imageFields as $imgfld) {
			unset($_FILES[$imgfld], $_REQUEST[$imgfld.'_hidden']);
		}
		unset($_REQUEST['__cbisduplicatedfromrecordid']);
	}
	$new_record_id = $focus->id;
	$curr_tab_id = gettabid($currentModule);
	$related_list = get_related_lists($curr_tab_id, $maped_relations);
	dup_related_lists($new_record_id, $currentModule, $related_list, $record_id, $maped_relations);
	$dependents_list = get_dependent_lists($curr_tab_id);
	$dependent_tables = get_dependent_tables($dependents_list, $currentModule);
	dup_dependent_rec($record_id, $currentModule, $new_record_id, $dependent_tables, $maped_relations);
	return $new_record_id;
}

// The duplicate has already been created elsewhere, so here we just do the relations, not the direct relations, only the related lists
function duplicateRecordRelations($currentModule, $duplicatedrecord, $duplicatedfrom, $bmap) {
	if (is_numeric($bmap)) {
		$cbMapid = $bmap;
	} else {
		//$bmapname = 'BusinessMapping_'.$currentModule.'_DuplicateRelations';
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmap, cbMap::getMapIdByName($bmap));
	}
	// Retrieve relations map
	if ($cbMapid) {
		$cbMap = cbMap::getMapByID($cbMapid);
		$maped_relations = $cbMap->DuplicateRelations()->getRelatedModules();
	} else {
		$maped_relations = array();
	}

	$curr_tab_id = gettabid($currentModule);
	$related_list = get_related_lists($curr_tab_id, $maped_relations);
	dup_related_lists($duplicatedrecord, $currentModule, $related_list, $duplicatedfrom, $maped_relations);
	$dependents_list = get_dependent_lists($curr_tab_id);
	$dependent_tables = get_dependent_tables($dependents_list, $currentModule);
	dup_dependent_rec($duplicatedfrom, $currentModule, $duplicatedrecord, $dependent_tables, $maped_relations);
	return $duplicatedrecord;
}

function get_related_lists($curr_tab_id, $maped_relations) {
	// Get related list
	global $adb;
	$related_list = array();
	$result = $adb->pquery('select related_tabid from vtiger_relatedlists where tabid=? and name=?', array($curr_tab_id, 'get_related_list'));
	$noofrows = $adb->num_rows($result);
	if ($noofrows) {
		while ($r = $adb->fetch_array($result)) {
			$related_list[] = getTabModuleName($r['related_tabid']);
		}
	}
	if (isset($maped_relations['Documents'])) {
		$related_list[] = 'Documents';
	}
	return $related_list;
}

function dup_related_lists($new_record_id, $currentModule, $related_list, $record_id, $maped_relations) {
	global $adb;
	$sql = 'INSERT INTO vtiger_crmentityrel (crmid,module,relcrmid,relmodule) SELECT ?,?,relcrmid,relmodule FROM vtiger_crmentityrel WHERE crmid=? AND relmodule=?';
	$sqldocs = 'INSERT INTO vtiger_senotesrel (crmid,notesid) SELECT ?,notesid FROM vtiger_senotesrel WHERE crmid=?';
	foreach ($related_list as $rel_module) {
		// Get and check condition type
		$condition = $maped_relations[$rel_module]['condition'];

		if (!empty($condition)) {
			if (is_numeric($condition)) {
				$cbmap = cbMap::getMapByID($condition);
			} else {
				$cbmapid = GlobalVariable::getVariable('BusinessMapping_'.$condition, cbMap::getMapIdByName($condition));
				$cbmap = cbMap::getMapByID($cbmapid);
			}
		} else {
			$cbmap = '';
		}

		// Get business map
		if (!empty($cbmap)) {
			$businessMap = $cbmap->column_fields['maptype'];
		} else {
			$businessMap = '';
		}

		if (empty($maped_relations) || isset($maped_relations[$rel_module])) {
			// WebserviceID
			$entityId = vtws_getEntityId($rel_module);

			if ($rel_module=='Documents') {
				if ($businessMap == 'Condition Query') {
					// Get crmids
					$ids = $cbmap->ConditionQuery($record_id);
					if ($ids && count($ids) > 0) {
						$adb->pquery(
							'INSERT IGNORE INTO vtiger_senotesrel (crmid,notesid) 
								SELECT ?,notesid FROM vtiger_senotesrel WHERE notesid IN ('.generateQuestionMarks($ids).')',
							array($new_record_id,$ids)
						);
					}
				} elseif ($businessMap == 'Condition Expression') {
					// Get crmids
					$result = $adb->pquery('SELECT notesid FROM vtiger_senotesrel WHERE crmid=?', array($record_id));
					if ($result && $adb->num_rows($result) > 0) {
						while ($related = $adb->fetch_array($result)) {
							if ($cbmap->ConditionExpression($entityId.'x'.$related['notesid'])) {
								$adb->pquery(
									'INSERT INTO vtiger_senotesrel (crmid,notesid) VALUES(?,?)',
									array($new_record_id,$related['notesid'])
								);
							}
						}
					}
				} else {
					$adb->pquery($sqldocs, array($new_record_id,$record_id));
				}
			} else {
				if ($businessMap == 'Condition Query') {
					// Get crmids
					$ids = $cbmap->ConditionQuery($record_id);
					if ($ids && count($ids) > 0) {
						$adb->pquery(
							'INSERT INTO vtiger_crmentityrel (crmid,module,relcrmid,relmodule) 
								SELECT ?,?,relcrmid,relmodule FROM vtiger_crmentityrel WHERE relcrmid IN ('.generateQuestionMarks($ids).') AND relmodule=?',
							array($new_record_id,$currentModule,$ids,$rel_module)
						);
					}
				} elseif ($businessMap == 'Condition Expression') {
					// Get crmids
					$result = $adb->pquery('SELECT relcrmid FROM vtiger_crmentityrel WHERE crmid=? AND relmodule=?', array($record_id, $rel_module));
					if ($result && $adb->num_rows($result) > 0) {
						while ($related = $adb->fetch_array($result)) {
							if ($cbmap->ConditionExpression($entityId.'x'.$related['relcrmid'])) {
								$adb->pquery(
									'INSERT INTO vtiger_crmentityrel (crmid,module,relcrmid,relmodule) VALUES(?,?,?,?)',
									array($new_record_id,$currentModule,$related['relcrmid'],$rel_module)
								);
							}
						}
					}
				} else {
					$adb->pquery($sql, array($new_record_id,$currentModule,$record_id,$rel_module));
				}
			}
		}
	}
}

function get_dependent_lists($curr_tab_id) {
	// Get dependents list
	global $adb;
	$dependents_list = array();
	$result = $adb->pquery('select related_tabid from vtiger_relatedlists where tabid=? and name=?', array($curr_tab_id, 'get_dependents_list'));
	$noofrows = $adb->num_rows($result);
	if ($noofrows) {
		while ($r = $adb->fetch_array($result)) {
			$moduleName = getTabModuleName($r['related_tabid']);
			$dependents_list[] = $moduleName;
		}
	}
	return $dependents_list;
}

function get_dependent_tables($dependents_list, $currentModule) {
	// Dependents table
	global $adb;
	$dependent_tables = $dependent_row = array();
	foreach ($dependents_list as $module) {
		$sql = 'SELECT * FROM vtiger_fieldmodulerel JOIN vtiger_field ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid WHERE module=? AND relmodule=?';
		$result = $adb->pquery($sql, array($module,$currentModule));
		$noofrows = $adb->num_rows($result);
		if ($noofrows) {
			while ($r = $adb->fetch_array($result)) {
				$dependent_row['tablename'] = $r['tablename'];
				$dependent_row['columname'] = $r['columnname'];
				$dependent_tables[$module] = $dependent_row;
			}
		}
	}
	return $dependent_tables;
}

function dup_dependent_rec($record_id, $relatedModule, $new_record_id, $dependent_tables, $maped_relations) {
	global $adb, $current_user;
	$invmods = getInventoryModules();
	foreach ($dependent_tables as $module => $tables) {
		if (in_array($module, $invmods) || !vtlib_isModuleActive($module)) {
			continue; // we can't duplicate these
		}
		if (empty($maped_relations) || isset($maped_relations[$module])) {
			require_once 'modules/'.$module.'/'.$module.'.php';
			$handler = vtws_getModuleHandlerFromName($module, $current_user);
			$meta = $handler->getMeta();
			$related_field = $tables['columname'];
			$imageFields = $meta->getImageFields();
			if (count($imageFields)>0) {
				foreach ($imageFields as $imgfld) {
					$_FILES[$imgfld] = array('name'=>'','size'=>0);
				}
			}
			$queryGenerator = new QueryGenerator($module, $current_user);
			$queryGenerator->setFields(array('id'));
			$queryGenerator->addReferenceModuleFieldCondition($relatedModule, $related_field, 'id', $record_id, 'e');
			$query = $queryGenerator->getQuery();
			$result=$adb->pquery($query, array());
			while ($r = $adb->fetch_array($result)) {
				// Duplicate dependent records
				$entity = new $module();
				$entity->mode='';
				$entity->isduplicate = true;
				$entity->retrieve_entity_info($r[0], $module);
				if (count($imageFields)>0) {
					foreach ($imageFields as $imgfld) {
						$_REQUEST[$imgfld.'_hidden'] = $entity->column_fields[$imgfld];
					}
					$_REQUEST['__cbisduplicatedfromrecordid'] = $r[0];
				}
				$entity->column_fields[$related_field] = $new_record_id;
				$entity->column_fields = DataTransform::sanitizeRetrieveEntityInfo($entity->column_fields, $meta);
				$entity->column_fields['isduplicatedfromrecordid'] = $entity->column_fields['record_id']; // in order to support duplicate workflows
				$entity->save($module);
			}
			if (count($imageFields)>0) {
				foreach ($imageFields as $imgfld) {
					unset($_FILES[$imgfld], $_REQUEST[$imgfld.'_hidden']);
				}
				unset($_REQUEST['__cbisduplicatedfromrecordid']);
			}
		}
	}
}
?>