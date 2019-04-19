<?php
/***********************************************************************************
 * Copyright 2012-2014 JPL TSolucio, S.L.  --  This file is a part of coreBOSCP.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 ************************************************************************************/
require_once 'include/Webservices/Utils.php';

/*
 * Given a record ID and a related module, this function returns the set of related records that belong to that ID
 * Only the columns and records the current user has access to will be returned.
 * If the current user cannot access the main module or the related module, an error will be returned.
 *
 * Parameters:
 * id:
 *    a webservice ID corresponding to the main record we want to relate
 * module:
 *    the module name of the id
 * relatedModule:
 *    the name of the module related to the main module, this represents the type of records to be returned
 * queryParameters:
 *    an array with parameters to modify the query and set of returned results in different ways
 *    accepted values in the array are:
 *  productDiscriminator: a string with one of the next values
 *    ProductBundle: relation Products-Products, return bundle products, this is the default behavior
 *    ProductParent: relation Products-Products, return parent product
 *    ProductLineInvoice{Only}: relation Account|Contact-Products, return products related through Invoice (only)
 *    ProductLineSalesOrder{Only}: relation Account|Contact-Products, return products related through SalesOrder (only)
 *    ProductLineQuote{Only}: relation Account|Contact-Products, return products related through Quote (only)
 *    ProductLineAll: relation Account|Contact-Products, return products related through Quote, SalesOrder and Invoice
 *    ProductLineNone: relation Account|Contact-Products, return only products directly related, this is the default behavior
 *  limit: a string indicating the limit of records to be returned. this is needed for paging
 *  offset: a string indicating the initial offset for returning values. this is needed for paging
 *  orderby: a syntactically and semantically correct order by directive wihtout the "order by", only the fields and their order (no validation is done)
 *  columns: a a comma separated string of column names that are to be returned. The special value "*" will return all fields.
 *       for example: 'assigned_user_id,id,createdtime,notes_title,filedownloadcount,filelocationtype,filesize'
 *
 * Author: JPL TSolucio, S.L. June 2012.  Joe Bordes
 *
 */
function getRelatedRecords($id, $module, $relatedModule, $queryParameters, $user) {
	global $adb, $log;

	// pickup meta data of related module
	$webserviceObject = VtigerWebserviceObject::fromName($adb, $relatedModule);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	if ($relatedModule=='Products' && $module!='Products') {
		$srvwebserviceObject = VtigerWebserviceObject::fromName($adb, 'Services');
		$srvhandlerPath = $srvwebserviceObject->getHandlerPath();
		$srvhandlerClass = $srvwebserviceObject->getHandlerClass();
		require_once $srvhandlerPath;
		$srvhandler = new $srvhandlerClass($srvwebserviceObject, $user, $adb, $log);
		$srvmeta = $srvhandler->getMeta();
	} elseif ($relatedModule=='Products' && $module=='Products') {
		$relatedModule = 'ProductComponent';
	}

	require_once $handlerPath;

	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();

	$query = __getRLQuery($id, $module, $relatedModule, $queryParameters, $user);
	$result = $adb->pquery($query, array());
	$records = array();

	// Return results
	$pdowsid = vtws_getEntityID('Products').'x';
	$srvwsid = vtws_getEntityID('Services').'x';
	while ($row = $adb->fetch_array($result)) {
		if (($module=='HelpDesk' || $module=='Faq') && $relatedModule=='ModComments') {
			$records[] = $row;
		} else {
			if ($relatedModule=='Products') {
				if (isset($row['productid']) && isset($row['sequence_no'])) {
					if (isset($row['productid']) && getSalesEntityType($row['productid'])=='Services') {
						$rec = DataTransform::sanitizeData($row, $srvmeta);
						$rec['id'] = $srvwsid.$row['productid'];
						$rec['productid'] = $srvwsid.$row['productid'];
						$rec['linetype'] = 'Services';
					} else {
						$rec = DataTransform::sanitizeData($row, $meta);
						$rec['productid'] = $pdowsid.$row['productid'];
						$rec['id'] = $pdowsid.$row['productid'];
						$rec['linetype'] = 'Products';
					}
					$records[] = $rec;
				} else {
					$records[] =  DataTransform::sanitizeData($row, $meta);
				}
			} else {
				$records[] =  DataTransform::sanitizeData($row, $meta);
			}
		}
	}
	return array ('records' => $records);
}

// see parameter description in getRelatedRecords() above
function __getRLQuery($id, $module, $relatedModule, $queryParameters, $user) {
	global $adb, $currentModule, $log, $current_user;
	if (strtolower($relatedModule)=='products' && strtolower($module)=='products') {
		$relatedModule = 'ProductComponent';
	}

	// Initialize required globals
	$currentModule = $module;
	// END
	if (empty($queryParameters['productDiscriminator'])) {
		$queryParameters['productDiscriminator'] = '';
	}
	if (empty($queryParameters['columns'])) {
		$queryParameters['columns'] = '*';
	}
	$productDiscriminator = strtolower($queryParameters['productDiscriminator']);

	// check modules
	$webserviceObject = VtigerWebserviceObject::fromName($adb, $relatedModule);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;

	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();
	$relatedModule = $meta->getEntityName();
	if (!$meta->isModuleEntity()) {
		throw new WebserviceException('INVALID_MODULE', "Given related module ($relatedModule) cannot be found");
	}
	$relatedModuleId = getTabid($relatedModule);

	$webserviceObject = VtigerWebserviceObject::fromName($adb, $module);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;

	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();
	$module = $meta->getEntityName();
	if (!$meta->isModuleEntity()) {
		throw new WebserviceException('INVALID_MODULE', "Given module ($module) cannot be found");
	}
	$moduleId = getTabid($module);

	// check permission on module
	list($wsid,$crmid) = explode('x', $id);
	if ((vtws_getEntityId('Calendar')==$wsid || vtws_getEntityId('Events')==$wsid) && getSalesEntityType($crmid)=='cbCalendar') {
		$id = vtws_getEntityId('cbCalendar') . 'x' . $crmid;
	}
	if (vtws_getEntityId('cbCalendar')==$wsid && getSalesEntityType($crmid)=='Calendar') {
		$rs = $adb->pquery('select activitytype from vtiger_activity where activityid=?', array($crmid));
		if ($rs && $adb->num_rows($rs)==1) {
			if ($adb->query_result($rs, 0, 0)=='Task') {
				$id = vtws_getEntityId('Calendar') . 'x' . $crmid;
			} else {
				$id = vtws_getEntityId('Events') . 'x' . $crmid;
			}
		}
	}
	$webserviceObject = VtigerWebserviceObject::fromId($adb, $id);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;

	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();
	$entityName = $meta->getObjectEntityName($id);
	$types = vtws_listtypes(null, $user);
	if (!in_array($entityName, $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Permission to perform the operation on module ($module) is denied");
	}

	if ($entityName !== $webserviceObject->getEntityName()) {
		throw new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Id specified is incorrect');
	}

	if (!$meta->hasPermission(EntityMeta::$RETRIEVE, $id)) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read given object is denied');
	}

	$idComponents = vtws_getIdComponents($id);
	if (!$meta->exists($idComponents[1])) {
		throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND, 'Record you are trying to access is not found');
	}

	$crmid = $idComponents[1];

	// check permission on related module and pickup meta data for further processing
	if ($relatedModule == 'Products' && !vtlib_isModuleActive('Products') && vtlib_isModuleActive('Services')) {
		$webserviceObject = VtigerWebserviceObject::fromName($adb, 'Services');
	} else {
		$webserviceObject = VtigerWebserviceObject::fromName($adb, $relatedModule);
	}
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;

	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();

	if ($relatedModule == 'Products') {
		if (!(in_array('Products', $types['types']) || in_array('Services', $types['types']))) {
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Permission to perform the operation on module ($relatedModule) is denied");
		}
	} elseif (!in_array($relatedModule, $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Permission to perform the operation on module ($relatedModule) is denied");
	}

	if (!$meta->hasReadAccess()) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read given object is denied');
	}

	// user has enough permission to start process
	$query = '';
	switch ($relatedModule) {
		case 'ModComments':
			$wsUserIdrs = $adb->query("select id from vtiger_ws_entity where name='Users'");
			$wsUserId = $adb->query_result($wsUserIdrs, 0, 0).'x';
			$wsContactIdrs = $adb->query("select id from vtiger_ws_entity where name='Contacts'");
			$wsContactId = $adb->query_result($wsContactIdrs, 0, 0).'x';
			switch ($module) {
				case 'HelpDesk':
					$query="select
						concat(case when (ownertype = 'user') then '$wsUserId' else '$wsContactId' end,ownerid) as creator,
						concat(case when (ownertype = 'user') then '$wsUserId' else '$wsContactId' end,ownerid) as assigned_user_id,
						'TicketComments' as setype,
						createdtime,
						createdtime as modifiedtime,
						0 as id,
						comments as commentcontent,
						'$id' as related_to,
						'' as parent_comments,
						ownertype,
						case when (ownertype = 'user') then vtiger_users.user_name else vtiger_portalinfo.user_name end as owner_name
					 from vtiger_ticketcomments
					 left join vtiger_users on vtiger_users.id = ownerid
					 left join vtiger_portalinfo on vtiger_portalinfo.id = ownerid
					 where ticketid=$crmid";
					break;
				case 'Faq':
					$query="select
						0 as creator,
						0 as assigned_user_id,
						'FaqComments' as setype,
						createdtime,
						createdtime as modifiedtime,
						0 as id,
						comments as commentcontent,
						'$id' as related_to,
						'' as parent_comments
					  from vtiger_faqcomments where faqid=$crmid";
					break;
				default:
					$entityInstance = CRMEntity::getInstance($relatedModule);
					$queryCriteria  = '';
					$criteria='All';  // currently hard coded to all  ** TODO **
					switch ($criteria) { // currently hard coded to all  ** TODO **
						case 'All':
							$queryCriteria = '';
							break;
						case 'Last5':
							$queryCriteria = sprintf(' ORDER BY %s.%s DESC LIMIT 5', $entityInstance->table_name, $entityInstance->table_index);
							break;
						case 'Mine':
							$queryCriteria = ' AND vtiger_crmentity.smcreatorid=' . $current_user->id;
							break;
					}
					$query = $entityInstance->getListQuery('ModComments', sprintf(" AND %s.related_to=$crmid", $entityInstance->table_name));
					$query .= $queryCriteria;
					$qfields = __getRLQueryFields($meta, $queryParameters['columns']);
					// Remove all the \n, \r and white spaces to keep the space between the words consistent.
					$query = preg_replace("/[\n\r\s]+/", ' ', $query);
					$query = "select $qfields ".substr($query, stripos($query, ' FROM '), strlen($query));
					break;
			} // end switch ModComments
			break;
		default:
			$relation_criteria = '';
			switch ($relatedModule) {
				case 'ProductComponent':
					if ($module == 'Products') {  // Product Bundles
						if (!empty($productDiscriminator) && $productDiscriminator == 'productparent') {
							$relation_criteria = " and label like '%parent%'";
						} else {
							$relation_criteria = " and label like '%bundle%'";  // bundle by default
						}
					}
					break;
				case 'Calendar':
					$relation_criteria = " and label like '%Activities%'";
					// History not supported
					//$relation_criteria = " and label like '%History%'";
					break;
			}
			// special product relation with Q/SO/I/PO
			if ($relatedModule == 'Products' && in_array($module, array('Invoice','Quotes','SalesOrder','PurchaseOrder'))) {
				$qparams = ' ' . $queryParameters['columns'] . ' ';
				$qparams = str_replace(' id ', ' productid as id ', $qparams);
				$qparams = str_replace(',id ', ',productid as id ', $qparams);
				$qparams = str_replace(' id,', ' productid as id,', $qparams);
				$qparams = str_replace(',id,', ',productid as id,', $qparams);
				$query = 'select ' . $qparams . ' FROM vtiger_inventoryproductrel where id=' . $crmid;
			} else {
				$relationResult = $adb->pquery(
					"SELECT * FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=? $relation_criteria",
					array($moduleId, $relatedModuleId)
				);

				if (!$relationResult || !$adb->num_rows($relationResult)) {
					throw new WebserviceException('MODULES_NOT_RELATED', "Cannot find relation between $module and $relatedModule");
				}

				if ($adb->num_rows($relationResult) > 1) {
					throw new WebserviceException('MANY_RELATIONS', "More than one relation exists between $module and $relatedModule");
				}

				$relationInfo = $adb->fetch_array($relationResult);

				$moduleInstance = CRMEntity::getInstance($module);
				$params = array($crmid, $moduleId, $relatedModuleId);

				global $GetRelatedList_ReturnOnlyQuery;
				$holdValue = $GetRelatedList_ReturnOnlyQuery;
				$GetRelatedList_ReturnOnlyQuery = true;
				$relationData = call_user_func_array(array($moduleInstance,$relationInfo['name']), $params);
				if (!isset($relationData['query'])) {
					throw new WebServiceException(WebServiceErrorCode::$OPERATIONNOTSUPPORTED, 'getRelatedRecords can only be called from Webservice interface');
				}
				$GetRelatedList_ReturnOnlyQuery = $holdValue;
				$query = $relationData['query'];
				if ($relatedModule == 'ProductComponent') {
					$webserviceObject = VtigerWebserviceObject::fromName($adb, 'Products');
					$handlerPath = $webserviceObject->getHandlerPath();
					$handlerClass = $webserviceObject->getHandlerClass();
					require_once $handlerPath;
					$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
					$meta = $handler->getMeta();
					if (!empty($productDiscriminator) && $productDiscriminator == 'productparent') {
						$query = appendFromClauseToQuery($query, ' inner join vtiger_products on vtiger_products.productid=vtiger_productcomponent.frompdo');
					} else {
						$query = appendFromClauseToQuery($query, ' inner join vtiger_products on vtiger_products.productid=vtiger_productcomponent.topdo');
					}
				}
				// select the fields the user has access to and prepare query
				$qfields = __getRLQueryFields($meta, $queryParameters['columns']);
				// Remove all the \n, \r and white spaces to keep the space between the words consistent.
				$query = preg_replace("/[\n\r\s]+/", ' ', $query);
				$query = "select $qfields ".substr($query, stripos($query, ' FROM '), strlen($query));
				// Append additional joins for some queries
				$query = __getRLQueryFromJoins($query, $meta, $relatedModule);
				//Appending Access Control
				if ($relatedModule != 'Faq' && $relatedModule != 'PriceBook' && $relatedModule != 'Vendors' && $relatedModule != 'Users') {
					$secQuery = getNonAdminAccessControlQuery($relatedModule, $current_user);
					if (strlen($secQuery) > 1) {
						$query = appendFromClauseToQuery($query, $secQuery);
					}
				}

				// This is for getting products related to Account/Contact through their Quote/SO/Invoice
				if (($module == 'Accounts' || $module == 'Contacts')
					&& ($relatedModule == 'Products' || $relatedModule == 'Services')
					&& in_array(
						$productDiscriminator,
						array(
							'productlineinvoice','productlinesalesorder','productlinequote','productlineall',
							'productlineinvoiceonly','productlinesalesorderonly','productlinequoteonly'
						)
					)
				) {
					// Here we add list of products contained in related invoice, so and quotes
					$relatedField = ($module == 'Accounts' ? 'accountid' : 'contactid');
					$pstable = $meta->getEntityBaseTable();
					$psfield = $meta->getIdColumn();

					if (substr($productDiscriminator, -4)=='only') {
						$productDiscriminator = substr($productDiscriminator, 0, strlen($productDiscriminator)-4);
						$query = '';
					}
					if ($productDiscriminator=='productlinequote' || $productDiscriminator=='productlineall') {
						$q = "select distinct $qfields from vtiger_quotes
							inner join vtiger_crmentity as crmq on crmq.crmid=vtiger_quotes.quoteid
							left join vtiger_inventoryproductrel on vtiger_inventoryproductrel.id=vtiger_quotes.quoteid
							inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_inventoryproductrel.productid
							left join vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
							left join $pstable on $pstable.$psfield = vtiger_inventoryproductrel.productid
							where vtiger_inventoryproductrel.productid = $pstable.$psfield AND crmq.deleted=0
							and $relatedField = $crmid";
						$query .= ($query=='' ? '' : ' UNION DISTINCT ').$q;
					}
					if ($productDiscriminator=='productlineinvoice' || $productDiscriminator=='productlineall') {
						$q = "select distinct $qfields from vtiger_invoice
							inner join vtiger_crmentity as crmi on crmi.crmid=vtiger_invoice.invoiceid
							left join vtiger_inventoryproductrel on vtiger_inventoryproductrel.id=vtiger_invoice.invoiceid
							inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_inventoryproductrel.productid
							left join vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid 
							left join $pstable on $pstable.$psfield = vtiger_inventoryproductrel.productid
							where vtiger_inventoryproductrel.productid = $pstable.$psfield AND crmi.deleted=0
							and $relatedField = $crmid";
						$query .= ($query=='' ? '' : ' UNION DISTINCT ').$q;
					}
					if ($productDiscriminator=='productlinesalesorder' || $productDiscriminator=='productlineall') {
						$q = "select distinct $qfields from vtiger_salesorder
						inner join vtiger_crmentity as crms on crms.crmid=vtiger_salesorder.salesorderid
						left join vtiger_inventoryproductrel on vtiger_inventoryproductrel.id=vtiger_salesorder.salesorderid
						inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_inventoryproductrel.productid
						left join vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
						left join $pstable on $pstable.$psfield = vtiger_inventoryproductrel.productid
						where vtiger_inventoryproductrel.productid = $pstable.$psfield AND crms.deleted=0
						and $relatedField = $crmid";
						$query .= ($query=='' ? '' : ' UNION DISTINCT ').$q;
					}
				}
			} // q/so/i/po-product relation
			break;
	}  // end switch $relatedModule
	// now we add order by if needed
	if ($query!='' && !empty($queryParameters['orderby'])) {
		$query .= ' order by '.$queryParameters['orderby'];
	}
	// now we add limit and offset if needed
	if ($query!='' && !empty($queryParameters['limit'])) {
		$query .= ' limit '.$queryParameters['limit'];
		if (!empty($queryParameters['offset'])) {
			$query .= ','.$queryParameters['offset'];
		}
	}
	return $query;
}

// retrieves the fully qualified column names for the select query
// $meta is the metaata object related to the main entity
// $cols is a a comma separated string of column names that are to be returned. The special value "*" will return all fields.
//       for example: 'assigned_user_id,id,createdtime,notes_title,filedownloadcount,filelocationtype,filesize'
function __getRLQueryFields($meta, $cols = '*') {
	$cols=trim($cols);
	$fieldcol = $meta->getFieldColumnMapping();
	if ($cols!='*') {
		$fldcol = explode(',', $cols);
		$fldcol = array_combine($fldcol, $fldcol);
		$fieldcol = array_intersect_key($fieldcol, $fldcol);
	}
	$columnTable = $meta->getColumnTableMapping();
	$qfields = '';
	foreach ($fieldcol as $col) {
		$cl = $col;
		if ($col=='smownerid') {
			$cl = 'smownerid as assigned_user_id,vtiger_crmentity.smownerid,vtiger_users.first_name as owner_firstname, vtiger_users.last_name as owner_lastname';
		}
		if ($col=='smcreatorid') {
			$cl = 'smcreatorid as creator,vtiger_crmentity.smcreatorid,vtiger_users.first_name as creator_firstname, vtiger_users.last_name as creator_lastname';
		}
		$qfields .= $columnTable[$col].".$cl,";
	}
	$qfields = trim($qfields, ',');  // eliminate last comma
	return $qfields;
}

// We obtain the query that relates both entities by means of the Related List function that joins them
// but this query doesn't always have support for all the tables that are need to obtain all the possible fields
// this function is an intent to add the necessary joins to the default query so all fields will work
function __getRLQueryFromJoins($query, $meta, $relatedModule = '') {
	if ($meta->getEntityName()=='Emails') {
		// this query is non-standard, I try to fix it a bit to get it working
		$chgFrom = 'from vtiger_activity, vtiger_seactivityrel, vtiger_contactdetails, vtiger_users, vtiger_crmentity';
		$chgTo = 'from vtiger_activity
					inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_activity.activityid
					left join vtiger_seactivityrel on vtiger_seactivityrel.activityid = vtiger_activity.activityid
					left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
					left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_seactivityrel.crmid ';
		$query = str_replace($chgFrom, $chgTo, $query);
	}
	$etable = $meta->getEntityBaseTable();
	$eindex = $meta->getIdColumn();
	$posFrom = stripos($query, ' from ');
	foreach ($meta->getEntityTableIndexList() as $tbl => $fld) {
		if ($tbl=='vtiger_crmentity' || $tbl==$etable) {
			continue;  // these are always in the query
		}
		if (stripos($query, "join $tbl")>0) {
			continue;  // it is already joined
		}
		if (stripos($query, $tbl)>$posFrom) {
			continue;  // the table is present after FROM
		}
		if ($tbl=='vtiger_ticketcomments' || $tbl=='vtiger_faqcomments' || ($relatedModule=='ProductComponent' && $tbl='vtiger_seproductsrel')) {
			continue;  // these are obtained through comments
		}
		$secQuery = " left join $tbl on $tbl.$fld = $etable.$eindex ";
		$query = appendFromClauseToQuery($query, $secQuery);
	}
	return $query;
}