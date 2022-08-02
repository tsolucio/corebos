<?php
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  TSolucio Open Source
   * The Initial Developer of the Original Code is TSolucio.
   * Portions created by TSolucio are Copyright (C) TSolucio.
   * All Rights Reserved.
 ********************************************************************************/
require_once 'include/utils/utils.php';

class cbBCaseHandler extends VTEventHandler {

	public function handleEvent($handlerType, $entityData) {
		global $adb;
		if ($handlerType!='vtiger.entity.aftersave' && $handlerType!='corebos.entity.link.after') {
			return;
		}
		if ($handlerType=='vtiger.entity.aftersave') {
			$moduleName = $entityData->getModuleName();
			$recordId = $entityData->getId();
			$returnmodule = empty($_REQUEST['return_module']) ? '' : vtlib_purify($_REQUEST['return_module']);
			$returnId = empty($_REQUEST['return_id']) ? 0 : vtlib_purify($_REQUEST['return_id']);
			$entityfocus = $entityData->focus;
		} else {
			$moduleName = $entityData['destinationModule'];
			$recordId = $entityData['destinationRecordId'];
			$returnmodule = $entityData['sourceModule'];
			$returnId = $entityData['sourceRecordId'];
			$entityfocus = CRMEntity::getInstance($moduleName);
			$entityfocus->retrieve_entity_info($recordId, $moduleName);
		}
		$relModulesArr = array('Invoice', 'SalesOrder', 'PurchaseOrder', 'Quotes', 'Potentials');
		if ($moduleName!='cbBCase' && !in_array($moduleName, $relModulesArr)) {
			return;
		}
		// create module record -> from module
		// Create Invoice FROM Quote Detail
		// Create SalesOrder FROM Quote Detail
		// Create Invoice FROM Sales Order Detail
		// Create Invoice FROM Potential Detail
		// Create Quote with selected Potential in field list realated to Business Case
		$linkRelations = array(
			'Invoice' => array('Potentials'=>'Potentials', 'Quotes'=>'Quotes', 'SalesOrder'=>'SalesOrder'),
			'SalesOrder'=>array('Quotes'=>'Quotes','PurchaseOrder'=>'PurchaseOrder'),
			'PurchaseOrder'=>array('SalesOrder'=>'SalesOrder'),
		);
		if (!isset($linkRelations[$moduleName])) {
			$linkRelations[$moduleName] = '';
		}
		// create module record -> with field Module selected
		// create Invoice with selected SalesOrder in field list
		// create SalesOrder with selected Potential or Quote in field list
		// Create Quote with selected Potential in field list realated to Business Case
		$focusRelFieldsArr = array(
			'Invoice'=>array('salesorder_id'),
			'SalesOrder'=>array('potential_id','quote_id'),
			'Quotes'=>array('potential_id'),
		);
		if (!isset($focusRelFieldsArr[$moduleName])) {
			$focusRelFieldsArr[$moduleName] = '';
		}
		// modules of related fields from field list
		$fieldModules = array(
			'salesorder_id'=>'SalesOrder',
			'potential_id'=>'Potentials',
			'quote_id'=>'Quotes'
		);
		// create array with IDs of related records from field list
		$i = 0;
		if (!empty($focusRelFieldsArr[$moduleName])) {
			foreach ($focusRelFieldsArr[$moduleName] as $value) {
				$relRecordFromField[$i][$value] = $entityfocus->column_fields[$focusRelFieldsArr[$moduleName][$i]];
				$i++;
			}
		}

		// this is used when is records created by CreateLinks from Record related to Business Case
		if ($handlerType=='vtiger.entity.aftersave' && in_array($moduleName, $relModulesArr) && !empty($returnmodule) && $linkRelations[$moduleName]!=''
			&& in_array($returnmodule, $linkRelations[$moduleName])
		) {
			$focus = CRMEntity::getInstance($returnmodule);
			$sql='SELECT DISTINCT coalesce(businesscase1.cbbcase_id, businesscase2.cbbcase_id) as cbbcase_id FROM '.$focus->table_name
			.' LEFT JOIN vtiger_crmentityrel as crmentityrel1 on crmentityrel1.crmid='.$focus->table_name.'.'.$focus->table_index
			.' LEFT JOIN vtiger_crmentity as crmentity1 on crmentity1.crmid = crmentityrel1.relcrmid AND crmentity1.deleted=0
			LEFT JOIN vtiger_cbbcase as businesscase1 on crmentity1.crmid = businesscase1.cbbcase_id
			LEFT JOIN vtiger_crmentityrel as crmentityrel2 on crmentityrel2.relcrmid='.$focus->table_name.'.'.$focus->table_index
			.' LEFT JOIN vtiger_crmentity as crmentity2 on crmentity2.crmid = crmentityrel2.crmid AND crmentity2.deleted=0
			LEFT JOIN vtiger_cbbcase as businesscase2 on crmentity2.crmid = businesscase2.cbbcase_id
			WHERE '.$focus->table_name.'.'.$focus->table_index.'=? AND (businesscase2.cbbcase_id is not null OR businesscase1.cbbcase_id is not null)';
			$result = $adb->pquery($sql, array($returnId));
			if ($adb->num_rows($result)>0) {
				$BCfocus = CRMEntity::getInstance('cbBCase');
				while ($row = $adb->fetch_array($result)) {
					if (!empty($row['cbbcase_id'])) {
						$BCfocus->save_related_module('cbBCase', $row['cbbcase_id'], $moduleName, $recordId);
					}
				}
			}
		}
		// this is used when is records created by CreateView with RecordFields in focus which are related to Business Case
		if ($handlerType=='vtiger.entity.aftersave' && in_array($moduleName, $relModulesArr) && isset($focusRelFieldsArr[$moduleName]) && !empty($relRecordFromField)) {
			$BCfocus = CRMEntity::getInstance('cbBCase');
			foreach ($relRecordFromField as $relRecordvalue) {
				foreach ($relRecordvalue as $fieldname => $relRecord) {
					if ($relRecord!='') {
						$focus = CRMEntity::getInstance($fieldModules[$fieldname]);
						$sql='SELECT DISTINCT coalesce(businesscase1.cbbcase_id, businesscase2.cbbcase_id) as cbbcase_id FROM '.$focus->table_name
						.' LEFT JOIN vtiger_crmentityrel as crmentityrel1 on crmentityrel1.crmid='.$focus->table_name.'.'.$focus->table_index
						.' LEFT JOIN vtiger_crmentity as crmentity1 on crmentity1.crmid = crmentityrel1.relcrmid AND crmentity1.deleted=0
						LEFT JOIN vtiger_cbbcase as businesscase1 on crmentity1.crmid = businesscase1.cbbcase_id
						LEFT JOIN vtiger_crmentityrel as crmentityrel2 on crmentityrel2.relcrmid='.$focus->table_name.'.'.$focus->table_index
						.' LEFT JOIN vtiger_crmentity as crmentity2 on crmentity2.crmid = crmentityrel2.crmid AND crmentity2.deleted=0
						LEFT JOIN vtiger_cbbcase as businesscase2 on crmentity2.crmid = businesscase2.cbbcase_id
						WHERE '.$focus->table_name.'.'.$focus->table_index.'=? AND (businesscase2.cbbcase_id is not null OR businesscase1.cbbcase_id is not null)';
						$result = $adb->pquery($sql, array($relRecord));
						if ($adb->num_rows($result)>0) {
							while ($row = $adb->fetch_array($result)) {
								if (!empty($row['cbbcase_id'])) {
									$BCfocus->save_related_module('cbBCase', $row['cbbcase_id'], $moduleName, $recordId);
								}
							}
						}
					}
				}
			}
		}
		if ($handlerType=='corebos.entity.link.after' && in_array($moduleName, $relModulesArr) && isset($focusRelFieldsArr[$moduleName]) && !empty($relRecordFromField)) {
			$BCfocus = CRMEntity::getInstance('cbBCase');
			foreach ($relRecordFromField as $relRecordvalue) {
				foreach ($relRecordvalue as $fieldname => $relRecord) {
					if ($relRecord!='') {
						$BCfocus->save_related_module('cbBCase', $returnId, $fieldModules[$fieldname], $relRecord);
					}
				}
			}
		}
		if ($returnmodule=='cbBCase' && in_array($moduleName, array('PurchaseOrder', 'SalesOrder', 'Invoice', 'Quotes'))) {
			$BCfocus = CRMEntity::getInstance('cbBCase');
			$BCfocus->reCalculateActuals($returnId);
		}
	}
}
?>