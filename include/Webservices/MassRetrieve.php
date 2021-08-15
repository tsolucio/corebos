<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
include_once 'include/Webservices/CustomerPortalWS.php';
include_once 'include/Webservices/getRecordImages.php';

function vtws_massretrieve($ids, $user) {
	global $log, $adb;
	$wsIds = array();
	$mname = '';
	$paramIds = explode(',', $ids);
	$types = vtws_listtypes(null, $user);
	foreach ($paramIds as $wsid) {
		if (empty($wsid)) {
			continue;
		}
		$id = vtws_getWSID($wsid);
		$webserviceObject = VtigerWebserviceObject::fromId($adb, $id);
		$handlerPath = $webserviceObject->getHandlerPath();
		$handlerClass = $webserviceObject->getHandlerClass();
		require_once $handlerPath;
		$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
		$meta = $handler->getMeta();
		$entityName = $meta->getObjectEntityName($id);
		if (!in_array($entityName, $types['types'])) {
			continue;
		}
		if ($meta->hasReadAccess()!==true) {
			continue;
		}
		if ($entityName !== $webserviceObject->getEntityName()) {
			continue;
		}
		if (!$meta->hasPermission(EntityMeta::$RETRIEVE, $id)) {
			continue;
		}
		$idComponents = vtws_getIdComponents($id);
		if (!$meta->exists($idComponents[1])) {
			continue;
		}
		if ($mname=='') {
			$mname = $entityName;
		}
		if ($mname!=$entityName) {
			continue;
		}
		$wsIds[] = $idComponents[1];
	}
	$actors = vtws_getActorModules();
	$rdo = array();
	if (in_array($mname, $actors) && !empty($wsIds)) {
		$rdo = $handler->massRetrieve($wsIds);
	}
	if (!in_array($mname, $actors) && !empty($wsIds)) {
		$r = $meta->getReferenceFieldDetails();
		$imgs = $meta->getImageFields();
		$crmentity = CRMEntity::getInstance($mname);
		$crmentity->retrieve_entities_info($wsIds, $mname, false);
		foreach ($crmentity->fetched_records as $crmid => $entity) {
			$cbuuid = isset($entity['cbuuid']) ? $entity['cbuuid'] : '';
			$entity = DataTransform::filterAndSanitize($entity, $meta);
			$entity['cbuuid'] = $cbuuid;
			// Dereference WSIDs
			$listofrelfields = array();
			if (!empty($entity['assigned_user_id'])) {
				$r['assigned_user_id'] = array('Users');
				$listofrelfields[] = $entity['assigned_user_id'];
			}
			foreach ($r as $relfield => $mods) {
				if (!empty($entity[$relfield])) {
					$listofrelfields[] = $entity[$relfield];
				}
			}
			if (!empty($listofrelfields)) {
				if ($entityName=='Emails' && $entity['parent_id']!='') {
					unset($listofrelfields['parent_id'], $r['parent_id']);
				}
				$deref = unserialize(vtws_getReferenceValue(serialize($listofrelfields), $user));
				foreach ($r as $relfield => $mods) {
					if (!empty($entity[$relfield])) {
						$entity[$relfield.'ename'] = $deref[$entity[$relfield]];
					}
				}
				if ($entityName=='Emails' && $entity['parent_id']!='') {
					$entity['parent_idename'] = unserialize(vtws_getReferenceValue(serialize(array($entity['parent_id'])), $user));
				}
			}
			// Add attachment information
			if (count($imgs)>0) {
				$imginfo = cbws_getrecordimageinfo($id, $user);
				if ($imginfo['results']>0) {
					foreach ($imgs as $img) {
						if (!empty($entity[$img]) && !empty($imginfo['images'][$img])) {
							$entity[$img.'imageinfo'] = $imginfo['images'][$img];
						}
					}
				}
			}
			//return product lines
			if ($entityName == 'Quotes' || $entityName == 'PurchaseOrder' || $entityName == 'SalesOrder' || $entityName == 'Invoice') {
				$cbMap = cbMap::getMapByName($entityName.'InventoryDetails', 'MasterDetailLayout');
				$MDMapFound = ($cbMap!=null && isPermitted('InventoryDetails', 'index')=='yes');
				if ($MDMapFound) {
					$cbMapFields = $cbMap->MasterDetailLayout();
				}
				$invdTabid = getTabid('InventoryDetails');
				$pdowsid = vtws_getEntityId('Products').'x';
				$srvwsid = vtws_getEntityId('Services').'x';
				list($wsid, $recordid) = explode('x', $id);
				$result = $adb->pquery('select * from vtiger_inventoryproductrel where id=?', array($recordid));
				while ($row=$adb->getNextRow($result, false)) {
					if ($row['discount_amount'] == null && $row['discount_percent'] == null) {
						$discount = 0;
						$discount_type = 0;
					} else {
						$discount = 1;
					}
					if ($row['discount_amount'] == null) {
						$discount_amount = 0;
					} else {
						$discount_amount = $row['discount_amount'];
						$discount_type = 'amount';
					}
					if ($row['discount_percent'] == null) {
						$discount_percent = 0;
					} else {
						$discount_percent = $row['discount_percent'];
						$discount_type = 'percentage';
					}
					$ltype = getSalesEntityType($row['productid']);
					$onlyPrd = array(
						'productid'=>$row['productid'],
						'wsproductid' => ($ltype=='Products' ? $pdowsid : $srvwsid).$row['productid'],
						'linetype' => $ltype,
						'comment'=>$row['comment'],
						'qty'=>$row['quantity'],
						'listprice'=>$row['listprice'],
						'discount'=>$discount, // 0 no discount, 1 discount
						'discount_type'=>$discount_type, // amount/percentage
						'discount_percentage'=>$discount_percent,
						'discount_amount'=>$discount_amount,
					);
					if ($MDMapFound) {
						foreach ($cbMapFields['detailview']['fields'] as $mdfield) {
							if ($mdfield['fieldinfo']['name']=='id') {
								continue;
							}
							$crmEntityTable = CRMEntity::getcrmEntityTableAlias('InventoryDetails');
							$mdrs = $adb->pquery(
								'select '.$mdfield['fieldinfo']['name'].',vtiger_inventorydetails.inventorydetailsid from vtiger_inventorydetails
									inner join '.$crmEntityTable.' on vtiger_crmentity.crmid=vtiger_inventorydetails.inventorydetailsid
									inner join vtiger_inventorydetailscf on vtiger_inventorydetailscf.inventorydetailsid=vtiger_inventorydetails.inventorydetailsid
									where vtiger_crmentity.deleted=0 and related_to=? and lineitem_id=?',
								array($recordid, $row['lineitem_id'])
							);
							if ($mdrs) {
								$col_fields = array();
								$col_fields[$mdfield['fieldinfo']['name']] = $adb->query_result($mdrs, 0, $mdfield['fieldinfo']['name']);
								$col_fields['record_id'] = $adb->query_result($mdrs, 0, 'inventorydetailsid');
								$foutput = getDetailViewOutputHtml($mdfield['fieldinfo']['uitype'], $mdfield['fieldinfo']['name'], $mdfield['fieldinfo']['label'], $col_fields, 0, $invdTabid, $entityName);
								if ($foutput[2]==69) { // image
									$foutput = str_replace('style="max-width: 500px;"', 'style="max-width: 100px;"', $foutput[1]);
								} else {
									$foutput = $foutput[1];
								}
								$onlyPrd[$mdfield['fieldinfo']['name']] = $foutput;
							}
						}
					}
					$entity['pdoInformation'][] = $onlyPrd;
				}
			}
			$rdo[vtws_getWSID($crmid)] = $entity;
		}
	}
	VTWS_PreserveGlobal::flush();
	return $rdo;
}
?>
