<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class VtigerModuleOperation extends WebserviceEntityOperation {
	protected $tabId;
	protected $isEntity = true;
	private $queryTotalRows = 0;
	private $returnFormattedValues = 0;

	public function __construct($webserviceObject, $user, $adb, $log) {
		parent::__construct($webserviceObject, $user, $adb, $log);
		$this->meta = $this->getMetaInstance();
		$this->tabId = $this->meta->getTabId();
		$this->returnFormattedValues = (int)GlobalVariable::getVariable('Webservice_Return_FormattedValues', 0);
	}

	protected function getMetaInstance() {
		if (empty(WebserviceEntityOperation::$metaCache[$this->webserviceObject->getEntityName()][$this->user->id])) {
			WebserviceEntityOperation::$metaCache[$this->webserviceObject->getEntityName()][$this->user->id]=new VtigerCRMObjectMeta($this->webserviceObject, $this->user);
		}
		return WebserviceEntityOperation::$metaCache[$this->webserviceObject->getEntityName()][$this->user->id];
	}

	public function getCache($module = '') {
		if (empty($module)) {
			return WebserviceEntityOperation::$metaCache;
		} else {
			return WebserviceEntityOperation::$metaCache[$module][$this->user->id];
		}
	}

	public function emptyCache($module = '') {
		if (empty($module)) {
			WebserviceEntityOperation::$metaCache = array();
		} else {
			unset(WebserviceEntityOperation::$metaCache[$module]);
		}
	}

	public function create($elementType, $element) {
		$crmObject = new VtigerCRMObject($elementType, false);

		$element = DataTransform::sanitizeForInsert($element, $this->meta);

		$error = $crmObject->create($element);
		if (!$error) {
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR, vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR));
		}

		$id = $crmObject->getObjectId();

		// Bulk Save Mode
		if (CRMEntity::isBulkSaveMode()) {
			// Avoiding complete read, as during bulk save mode, $result['id'] is enough
			return array('id' => vtws_getId($this->meta->getEntityId(), $id));
		}

		$error = $crmObject->read($id);
		if (!$error) {
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR, vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR));
		}

		$fields = $crmObject->getFields();
		$return = DataTransform::filterAndSanitize($fields, $this->meta);
		if ($this->returnFormattedValues) {
			$return = DataTransform::sanitizeRetrieveEntityInfo($return, $this->meta, false);
		}
		if (isset($fields['cbuuid'])) {
			$return['cbuuid'] = $fields['cbuuid'];
		}
		return $return;
	}

	public function retrieve($id, $deleted = false) {
		$ids = vtws_getIdComponents($id);
		$elemid = $ids[1];

		$crmObject = new VtigerCRMObject($this->tabId, true);
		$error = $crmObject->read($elemid, $deleted);
		if (!$error) {
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR, vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR));
		}
		$fields = $crmObject->getFields();
		$return = DataTransform::filterAndSanitize($fields, $this->meta);
		if ($this->returnFormattedValues) {
			$return = DataTransform::sanitizeRetrieveEntityInfo($return, $this->meta, false);
		}
		if (isset($fields['cbuuid'])) {
			$return['cbuuid'] = $fields['cbuuid'];
		}
		return $return;
	}

	public function update($element) {
		$ids = vtws_getIdComponents($element['id']);
		$element = DataTransform::sanitizeForInsert($element, $this->meta);
		$crmObject = new VtigerCRMObject($this->tabId, true);
		$crmObject->setObjectId($ids[1]);
		$error = $crmObject->read($crmObject->getObjectId());
		if (!$error) {
			return $error;
		}
		$cfields = $crmObject->getFields();
		$cfields = DataTransform::sanitizeRetrieveEntityInfo($cfields, $this->meta);
		$element = array_merge($cfields, $element);
		$error = $crmObject->update($element);
		if (!$error) {
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR, vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR));
		}

		$id = $crmObject->getObjectId();

		$error = $crmObject->read($id);
		if (!$error) {
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR, vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR));
		}
		$fields = $crmObject->getFields();
		$return = DataTransform::filterAndSanitize($fields, $this->meta);
		if ($this->returnFormattedValues) {
			$return = DataTransform::sanitizeRetrieveEntityInfo($return, $this->meta, false);
		}
		if (isset($fields['cbuuid'])) {
			$return['cbuuid'] = $fields['cbuuid'];
		}
		return $return;
	}

	public function revise($element) {
		return $this->update($element);
	}

	public function delete($id) {
		$ids = vtws_getIdComponents($id);
		$elemid = $ids[1];
		$crmObject = new VtigerCRMObject($this->tabId, true);
		$error = $crmObject->delete($elemid);
		if (!$error) {
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR, vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR));
		}
		return array('status'=>'successful');
	}

	public function wsVTQL2SQL($q, &$meta, &$queryRelatedModules) {
		require_once 'include/Webservices/GetExtendedQuery.php';
		$q = str_replace(array("\n", "\t", "\r"), ' ', $q);
		if (__FQNExtendedQueryIsRelatedQuery($q)) { // related query
			require_once 'include/Webservices/GetRelatedRecords.php';
			$queryParameters = array();
			$queryParameters['columns'] = trim(substr($q, 6, stripos($q, ' from ')-5));
			$moduleRegex = "/[fF][rR][Oo][Mm]\s+([^\s;]+)/";
			preg_match($moduleRegex, $q, $m);
			$relatedModule = trim($m[1]);
			$moduleRegex = "/\s+\(*\s*[rR][eE][lL][aA][tT][eE][dD]\.([^\s;]+)\s*=\s*([^\s;]+)/";
			preg_match($moduleRegex, $q, $m);
			$moduleName = trim($m[1]);
			$id = trim($m[2], "(')");
			$mysql_query = __getRLQuery($id, $moduleName, $relatedModule, $queryParameters, $this->user);
			// where, limit and order
			$afterwhere=substr($q, stripos($q, ' where ')+6);
			// eliminate related conditions
			$relatedCond = "/\(*[rR][eE][lL][aA][tT][eE][dD]\.([^\s;]+)\s*=\s*([^\s;]+)\)*\s*([aA][nN][dD]|[oO][rR]\s)*/";
			preg_match($relatedCond, $afterwhere, $pieces);
			$glue = isset($pieces[3]) ? trim($pieces[3]) : 'and';
			if (strtolower($relatedModule)=='documents') {
				$docrelcond = substr($mysql_query, stripos($mysql_query, 'where')+6);
				$addDocGlue = (stripos($docrelcond, ' and ') > 0 || stripos($docrelcond, ' or ') > 0);
				$mysql_query = substr($mysql_query, 0, stripos($mysql_query, 'where')+6);
				$relatedCond = '/related.'.$moduleName.'\s*=\s*'.trim($m[2], ')').'/i';
				$afterwhere=trim(preg_replace($relatedCond, $docrelcond, $afterwhere), ' ;');
			} else {
				$addDocGlue = true;
				$afterwhere=trim(preg_replace($relatedCond, '', $afterwhere), ' ;');
			}
			$relatedCond = "/\s+([aA][nN][dD]|[oO][rR])+\s+([oO][rR][dD][eE][rR])+/";
			$afterwhere=trim(preg_replace($relatedCond, ' order ', $afterwhere), ' ;');
			$relatedCond = "/\s+([aA][nN][dD]|[oO][rR])+\s+([lL][iI][mM][iI][tT])+/";
			$afterwhere=trim(preg_replace($relatedCond, ' limit ', $afterwhere), ' ;');
			// if related is at the end of condition we need to strip last and|or
			if (strtolower(substr($afterwhere, -3))=='and') {
				$afterwhere = substr($afterwhere, 0, strlen($afterwhere)-3);
			}
			if (strtolower(substr($afterwhere, -2))=='or') {
				$afterwhere = substr($afterwhere, 0, strlen($afterwhere)-2);
			}
			// transform REST ids
			$relatedCond = "/=\s*'*\d+x(\d+)'*/";
			$afterwhere=preg_replace($relatedCond, ' = $1 ', $afterwhere);
			// kill unbalanced parenthesis
			$balanced=0;
			$pila=array();
			for ($ch=0; $ch<strlen($afterwhere); $ch++) {
				if ($afterwhere[$ch]=='(') {
					$pila[$balanced]=array('pos'=>$ch,'dir'=>'(');
					$balanced++;
				} elseif ($afterwhere[$ch]==')') {
					if ($balanced>0 && $pila[$balanced-1]['dir']=='(') {
						array_pop($pila);
						$balanced--;
					} else {
						$pila[$balanced]=array('pos'=>$ch,'dir'=>')');
						$balanced++;
					}
				}
			}
			foreach ($pila as $paren) {
				$afterwhere[$paren['pos']]=' ';
			}
			// transform artificial commentcontent for FAQ and Ticket comments
			if (strtolower($relatedModule)=='modcomments' && (strtolower($moduleName)=='helpdesk' || strtolower($moduleName)=='faq')) {
				$afterwhere = str_ireplace('commentcontent', 'comments', $afterwhere);
			}
			$relhandler = vtws_getModuleHandlerFromName($moduleName, $this->user);
			$relmeta = $relhandler->getMeta();
			$queryRelatedModules[$moduleName] = $relmeta;
			// transform fieldnames to columnnames
			$handler = vtws_getModuleHandlerFromName($relatedModule, $this->user);
			$meta = $handler->getMeta();
			$fldmap = $meta->getFieldColumnMapping();
			$tblmap = $meta->getColumnTableMapping();
			$tok = strtok($afterwhere, ' ');
			$chgawhere = '';
			while ($tok !== false) {
				if (!empty($fldmap[$tok])) {
					$chgawhere .= (strpos($tok, '.') ? '' : $tblmap[$fldmap[$tok]].'.').$fldmap[$tok].' ';
				} else {
					$chgawhere .= $tok.' ';
				}
				$tok = strtok(' ');
			}
			$afterwhere = $chgawhere;
			if (!empty($afterwhere)) {
				$start = strtolower(substr(trim($afterwhere), 0, 5));
				if ($start!='limit' && $start!='order' && $addDocGlue) { // there is a condition we add the glue
					$mysql_query.=" $glue ";
				}
				$mysql_query.=" $afterwhere";
			}
			if (stripos($q, 'count(*)')>0) {
				$mysql_query = str_ireplace(' as count ', '', mkCountQuery($mysql_query));
			}
		} elseif (__ExtendedQueryConditionQuery($q)) {  // extended workflow condition syntax
			$moduleRegex = '/[fF][rR][Oo][Mm]\s+([^\s;]+)/';
			preg_match($moduleRegex, $q, $m);
			$fromModule = trim($m[1]);
			$handler = vtws_getModuleHandlerFromName($fromModule, $this->user);
			$meta = $handler->getMeta();
			list($mysql_query, $queryRelatedModules) = __ExtendedQueryConditionGetQuery($q, $fromModule, $this->user);
		} elseif (__FQNExtendedQueryIsFQNQuery($q)) {  // FQN extended syntax
			list($mysql_query,$queryRelatedModules) = __FQNExtendedQueryGetQuery($q, $this->user);
			$moduleRegex = "/[fF][rR][Oo][Mm]\s+([^\s;]+)/";
			preg_match($moduleRegex, $q, $m);
			$fromModule = trim($m[1]);
			$handler = vtws_getModuleHandlerFromName($fromModule, $this->user);
			$meta = $handler->getMeta();
		} else {
			$parser = new Parser($this->user, $q);
			$error = $parser->parse();
			if ($error) {
				return $parser->getError();
			}
			$mysql_query = $parser->getSql();
			$meta = $parser->getObjectMetaData();
		}
		if (!empty(coreBOS_Session::get('authenticatedUserIsPortalUser', false))) {
			$contactId = coreBOS_Session::get('authenticatedUserPortalContact', 0);
			if (empty($contactId)) {
				$mysql_query = 'select 1';
			} else {
				$accountId = getSingleFieldValue('vtiger_contactdetails', 'accountid', 'contactid', $contactId);
				$mysql_query = addPortalModuleRestrictions($mysql_query, $meta->getEntityName(), $accountId, $contactId);
			}
		}
		return $mysql_query;
	}

	public function query($q) {
		$mysql_query = $this->wsVTQL2SQL($q, $meta, $queryRelatedModules);
		return $this->querySQLResults($mysql_query, $q, $meta, $queryRelatedModules);
	}

	public function querySQLResults($mysql_query, $q, $meta, $queryRelatedModules) {
		global $site_URL, $adb, $default_charset, $currentModule;
		$holdCM = $currentModule;
		$currentModule = $meta->getEntityName();
		if (strpos($mysql_query, 'vtiger_inventoryproductrel')) {
			$invlines = true;
			$pdowsid = vtws_getEntityId('Products');
			$srvwsid = vtws_getEntityId('Services');
		} else {
			$invlines = false;
		}
		$this->pearDB->startTransaction();
		$result = $this->pearDB->pquery($mysql_query, array());
		$error = $this->pearDB->hasFailedTransaction();
		$this->pearDB->completeTransaction();

		if ($error) {
			$currentModule = $holdCM;
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR, vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR));
		}
		$imageFields = $meta->getImageFields();
		$imgquery = 'select vtiger_attachments.name, vtiger_attachments.attachmentsid, vtiger_attachments.path
			from vtiger_attachments
			inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
			inner join vtiger_seattachmentsrel on vtiger_attachments.attachmentsid=vtiger_seattachmentsrel.attachmentsid
			where (vtiger_crmentity.setype LIKE "%Image" or vtiger_crmentity.setype LIKE "%Attachment") and deleted=0 and vtiger_seattachmentsrel.crmid=?';
		$isDocModule = ($meta->getEntityName()=='Documents');
		$isRelatedQuery = __FQNExtendedQueryIsFQNQuery($q);
		$noofrows = $this->pearDB->num_rows($result);
		$output = array();
		$streamraw = (isset($_REQUEST['format']) && strtolower($_REQUEST['format'])=='streamraw');
		$streaming = (isset($_REQUEST['format']) && (strtolower($_REQUEST['format'])=='stream' || $streamraw));
		$stream = '';
		for ($i=0; $i<$noofrows; $i++) {
			$row = $this->pearDB->fetchByAssoc($result, $i);
			$rowcrmid = (isset($row[$meta->idColumn]) ? $row[$meta->idColumn] : (isset($row['crmid']) ? $row['crmid'] : (isset($row['id']) ? $row['id'] : '')));
			if (!$meta->hasPermission(EntityMeta::$RETRIEVE, $rowcrmid)) {
				continue;
			}
			if ($streamraw) {
				$newrow = $row;
			} else {
				$newrow = DataTransform::sanitizeDataWithColumn($row, $meta);
				if ($this->returnFormattedValues) {
					$newrow = DataTransform::sanitizeRetrieveEntityInfo($newrow, $meta, false);
				}
				if ($isRelatedQuery) {
					if ($invlines) {
						$newrow = $row;
						if (!empty($newrow['id'])) {
							$newrow['id'] = vtws_getEntityId(getSalesEntityType($newrow['id'])) . 'x' . $newrow['id'];
						}
						$newrow['linetype'] = '';
						if (!empty($newrow['productid'])) {
							$newrow['linetype'] = getSalesEntityType($newrow['productid']);
							$newrow['productid'] = ($newrow['linetype'] == 'Products' ? $pdowsid : $srvwsid) . 'x' . $newrow['productid'];
						}
						if (!empty($newrow['serviceid'])) {
							$newrow['linetype'] = 'Services';
							$newrow['serviceid'] = $srvwsid . 'x' . $newrow['serviceid'];
						}
					} else {
						$relflds = array_diff_key($row, $newrow);
						foreach ($queryRelatedModules as $relmod => $relmeta) {
							$lrm = strtolower($relmod);
							$newrflds = array();
							foreach ($relflds as $fldname => $fldvalue) {
								$fldmod = substr($fldname, 0, strlen($relmod));
								if (isset($row[$fldname]) && $fldmod==$lrm) {
									$newkey = substr($fldname, strlen($lrm));
									$newrflds[$newkey] = $fldvalue;
								}
							}
							$relrow = DataTransform::sanitizeDataWithColumn($newrflds, $relmeta);
							$newrelrow = array();
							foreach ($relrow as $key => $value) {
								$newrelrow[$lrm.$key] = $value;
							}
							$newrow = array_merge($newrow, $newrelrow);
						}
					}
				}
				if ($isDocModule) {
					$relatt=$adb->pquery('SELECT attachmentsid FROM vtiger_seattachmentsrel WHERE crmid=?', array($rowcrmid));
					if ($relatt && $adb->num_rows($relatt)==1) {
						$fileid = $adb->query_result($relatt, 0, 0);
						$attrs=$adb->pquery('SELECT * FROM vtiger_attachments WHERE attachmentsid=?', array($fileid));
						if ($attrs && $adb->num_rows($attrs) == 1) {
							$name = @$adb->query_result($attrs, 0, 'name');
							$filepath = @$adb->query_result($attrs, 0, 'path');
							$name = html_entity_decode($name, ENT_QUOTES, $default_charset);
							$newrow['_downloadurl'] = $site_URL.'/'.$filepath.$fileid.'_'.$name;
							$newrow['filename'] = $name;
						}
					}
				} elseif (!empty($imageFields)) {
					foreach ($imageFields as $imgvalue) {
						$newrow[$imgvalue.'fullpath'] = ''; // initialize so we have same number of columns in all rows
					}
					$result_image = $adb->pquery($imgquery, array($rowcrmid));
					while ($img = $adb->fetch_array($result_image)) {
						foreach ($imageFields as $imgvalue) {
							if (!empty($row[$imgvalue]) && ($img['name'] == $row[$imgvalue] || $img['name'] == str_replace(' ', '_', $row[$imgvalue]))) {
								$newrow[$imgvalue.'fullpath'] = $site_URL.'/'.$img['path'].$img['attachmentsid'].'_'.$img['name'];
								break;
							}
						}
					}
				}
			}
			if ($streaming) {
				$stream .= json_encode($newrow)."\n";
				if (($i % 500)==0) {
					echo $stream;
					flush();
					$stream = '';
				}
			} else {
				$output[] = $newrow;
			}
		}
		if ($stream!='') {
			echo $stream;
			flush();
			$stream = '';
		}
		$mysql_query = mkXQuery(stripTailCommandsFromQuery($mysql_query, false), 'count(*) AS cnt');
		$result = $this->pearDB->pquery($mysql_query, array());
		if ($result) {
			$this->queryTotalRows = $result->fields['cnt'];
		} else {
			$this->queryTotalRows = 0;
		}
		$currentModule = $holdCM;
		return $output;
	}

	public function getQueryTotalRows() {
		return $this->queryTotalRows;
	}

	public function describe($elementType) {
		global $current_user;
		$wsuserlanguage =$current_user->language;
		vtws_preserveGlobal('current_language', $wsuserlanguage);
		vtws_preserveGlobal('current_user', $this->user);
		$label = getTranslatedString($elementType, $elementType);
		$createable = strcasecmp(isPermitted($elementType, EntityMeta::$CREATE), 'yes') === 0;
		$updateable = strcasecmp(isPermitted($elementType, EntityMeta::$UPDATE), 'yes') === 0;
		$deleteable = $this->meta->hasDeleteAccess();
		$retrieveable = $this->meta->hasReadAccess();
		VTWS_PreserveGlobal::restore('current_language');
		$fields = $this->getModuleFields($wsuserlanguage);
		return array(
			'label'=>$label,'label_raw'=>$elementType,'name'=>$elementType,'createable'=>$createable,'updateable'=>$updateable,
			'deleteable'=>$deleteable,'retrieveable'=>$retrieveable,'fields'=>$fields,
			'idPrefix'=>$this->meta->getEntityId(),'isEntity'=>$this->isEntity,'labelFields'=>$this->meta->getNameFields()
		);
	}

	public function getModuleFields($i18nlanguage) {
		static $purified_mfcache = array();
		$mfkey = $this->meta->getTabName();
		if (array_key_exists($mfkey, $purified_mfcache)) {
			return $purified_mfcache[$mfkey];
		}
		$fields = array();
		$moduleFields = $this->meta->getModuleFields();
		foreach ($moduleFields as $webserviceField) {
			if (((int)$webserviceField->getPresence()) == 1) {
				continue;
			}
			$fields[] = $this->getDescribeFieldArray($webserviceField);
		}
		$fields[] = $this->getIdField($this->meta->getObectIndexColumn());
		$purified_mfcache[$mfkey] = $fields;
		return $fields;
	}

	public function getDescribeFieldArray($webserviceField) {
		static $purified_dfcache = array();
		$dfkey = $webserviceField->getFieldName().$webserviceField->getTabId();
		if (array_key_exists($dfkey, $purified_dfcache)) {
			return $purified_dfcache[$dfkey];
		}
		$fieldLabel = $webserviceField->getFieldLabelKey();
		$fieldLabeli18n = getTranslatedString($fieldLabel, $this->meta->getTabName());
		$typeDetails = $this->getFieldTypeDetails($webserviceField);

		//set type name, in the type details array.
		$typeDetails['name'] = $webserviceField->getFieldDataType();
		$editable = $this->isEditable($webserviceField);

		$blkname = $webserviceField->getBlockName();
		$describeArray = array(
			'name' => $webserviceField->getFieldName(),
			'label' => $fieldLabeli18n,
			'label_raw' => $fieldLabel,
			'mandatory' => $webserviceField->isMandatory(),
			'type' => $typeDetails,
			'nullable' => $webserviceField->isNullable(),
			'editable' => $editable,
			'uitype' => $webserviceField->getUIType(),
			'typeofdata' => $webserviceField->getTypeOfData(),
			'sequence' => $webserviceField->getFieldSequence(),
			'quickcreate' => $webserviceField->getQuickCreate(),
			'displaytype' => $webserviceField->getDisplayType(),
			'summary' => $webserviceField->getSummary(),
			'block' => array(
				'blockid' => $webserviceField->getBlockId(),
				'blocksequence' => $webserviceField->getBlockSequence(),
				'blocklabel' => $blkname,
				'blockname' => getTranslatedString($blkname, $this->meta->getTabName())
			)
		);
		if ($webserviceField->hasDefault()) {
			$describeArray['default'] = $webserviceField->getDefault();
		}
		$purified_dfcache[$dfkey] = $describeArray;
		return $describeArray;
	}

	public function getMeta() {
		return $this->meta;
	}

	public function getTabId() {
		return $this->tabId;
	}

	public function getField($fieldName) {
		$moduleFields = $this->meta->getModuleFields();
		return $this->getDescribeFieldArray($moduleFields[$fieldName]);
	}
}
?>
