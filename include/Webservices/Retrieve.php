<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
	
	function vtws_retrieve($id, $user){
		
		global $log,$adb;
		
		$webserviceObject = VtigerWebserviceObject::fromId($adb,$id);
		$handlerPath = $webserviceObject->getHandlerPath();
		$handlerClass = $webserviceObject->getHandlerClass();
		
		require_once $handlerPath;
		
		$handler = new $handlerClass($webserviceObject,$user,$adb,$log);
		$meta = $handler->getMeta();
		$entityName = $meta->getObjectEntityName($id);
		$types = vtws_listtypes(null, $user);
		if(!in_array($entityName,$types['types'])){
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED,"Permission to perform the operation is denied");
		}
		if($meta->hasReadAccess()!==true){
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED,"Permission to write is denied");
		}

		if($entityName !== $webserviceObject->getEntityName()){
			throw new WebServiceException(WebServiceErrorCode::$INVALIDID,"Id specified is incorrect");
		}
		
		if(!$meta->hasPermission(EntityMeta::$RETRIEVE,$id)){
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED,"Permission to read given object is denied");
		}
		
		$idComponents = vtws_getIdComponents($id);
		if(!$meta->exists($idComponents[1])){
			throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND,"Record you are trying to access is not found");
		}
		
		$entity = $handler->retrieve($id);
		//return product lines
		if($entityName == 'Quotes' || $entityName == 'PurchaseOrder' || $entityName == 'SalesOrder' || $entityName == 'Invoice') {
			list($wsid,$recordid) = split('x',$id);
			$result = $adb->query("Select * from vtiger_inventoryproductrel where id =".$recordid);
			while ($row=$adb->getNextRow($result, false)) {
				if($row['discount_amount'] == NULL && $row['discount_percent'] == NULL) {
					$discount = 0;$discount_type = 0;
				} else
					$discount = 1;

				if($row['discount_amount'] == NULL)
					$discount_amount = 0;
				else {
					$discount_amount = $row['discount_amount'];
					$discount_type = 'amount';
				}
				if($row['discount_percent'] == NULL)
					$discount_percent = 0;
				else {
					$discount_percent = $row['discount_percent'];
					$discount_type = 'percentage';
				}

				$onlyPrd = Array(
					"productid"=>$row['productid'],
					"comment"=>$row['comment'],
					"qty"=>$row['quantity'],
					"listprice"=>$row['listprice'],
					'discount'=>$discount,  // 0 no discount, 1 discount
					"discount_type"=>$discount_type,  //  amount/percentage
					"discount_percentage"=>$discount_percent,
					"discount_amount"=>$discount_amount,
				);
				$entity['pdoInformation'][] = $onlyPrd;
			}
		}
		VTWS_PreserveGlobal::flush();
		return $entity;
	}
?>
