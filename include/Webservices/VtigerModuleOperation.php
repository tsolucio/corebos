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
	
	public function __construct($webserviceObject,$user,$adb,$log){
		parent::__construct($webserviceObject,$user,$adb,$log);
		$this->meta = $this->getMetaInstance();
		$this->tabId = $this->meta->getTabId();
	}
	
	protected function getMetaInstance(){
		if(empty(WebserviceEntityOperation::$metaCache[$this->webserviceObject->getEntityName()][$this->user->id])){
			WebserviceEntityOperation::$metaCache[$this->webserviceObject->getEntityName()][$this->user->id]  = new VtigerCRMObjectMeta($this->webserviceObject,$this->user);
		}
		return WebserviceEntityOperation::$metaCache[$this->webserviceObject->getEntityName()][$this->user->id];
	}
	
	public function create($elementType,$element){
		$crmObject = new VtigerCRMObject($elementType, false);
		
		$element = DataTransform::sanitizeForInsert($element,$this->meta);
		
		$error = $crmObject->create($element);
		if(!$error){
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR,
					vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR));
		}
		
		$id = $crmObject->getObjectId();

		// Bulk Save Mode
		if(CRMEntity::isBulkSaveMode()) {
			// Avoiding complete read, as during bulk save mode, $result['id'] is enough
			return array('id' => vtws_getId($this->meta->getEntityId(), $id) );
		}
		
		$error = $crmObject->read($id);
		if(!$error){
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR,
				vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR));
		}
		
		return DataTransform::filterAndSanitize($crmObject->getFields(),$this->meta);
	}
	
	public function retrieve($id,$deleted=false){
		$ids = vtws_getIdComponents($id);
		$elemid = $ids[1];
		
		$crmObject = new VtigerCRMObject($this->tabId, true);
		$error = $crmObject->read($elemid,$deleted);
		if(!$error){
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR,
					vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR));
		}
		return DataTransform::filterAndSanitize($crmObject->getFields(),$this->meta);
	}
	
	public function update($element){
		$ids = vtws_getIdComponents($element["id"]);
		$element = DataTransform::sanitizeForInsert($element,$this->meta);
		
		$crmObject = new VtigerCRMObject($this->tabId, true);
		$crmObject->setObjectId($ids[1]);
		$error = $crmObject->read($crmObject->getObjectId());
		if($error == false){
			return $error;
		}
		$cfields = $crmObject->getFields();
		$cfields = DataTransform::sanitizeForInsert($cfields,$this->meta);
		$cfields = DataTransform::sanitizeTextFieldsForInsert($cfields,$this->meta);
		$element = array_merge($cfields,$element);
		$error = $crmObject->update($element);
		if(!$error){
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR,
					vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR));
		}
		
		$id = $crmObject->getObjectId();
		
		$error = $crmObject->read($id);
		if(!$error){
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR,
				vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR));
		}
		
		return DataTransform::filterAndSanitize($crmObject->getFields(),$this->meta);
	}
	
	public function revise($element){
		$ids = vtws_getIdComponents($element["id"]);
		$element = DataTransform::sanitizeForInsert($element,$this->meta);

		$crmObject = new VtigerCRMObject($this->tabId, true);
		$crmObject->setObjectId($ids[1]);
		$error = $crmObject->revise($element);
		if(!$error){
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR,
					vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR));
		}

		$id = $crmObject->getObjectId();

		$error = $crmObject->read($id);
		if(!$error){
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR,
					vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR));
		}

		return DataTransform::filterAndSanitize($crmObject->getFields(),$this->meta);
	}

	public function delete($id){
		$ids = vtws_getIdComponents($id);
		$elemid = $ids[1];
		$crmObject = new VtigerCRMObject($this->tabId, true);
		$error = $crmObject->delete($elemid);
		if(!$error){
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR,
					vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR));
		}
		return array("status"=>"successful");
	}
	
	public function wsVTQL2SQL($q,&$meta,&$queryRelatedModules){
		require_once 'include/Webservices/GetExtendedQuery.php';
		if (__FQNExtendedQueryIsRelatedQuery($q)) { // related query
			require_once 'include/Webservices/GetRelatedRecords.php';
			$queryParameters = array();
			$queryParameters['columns'] = trim(substr($q,6,stripos($q,' from ')-5));
			$moduleRegex = "/[fF][rR][Oo][Mm]\s+([^\s;]+)/";
			preg_match($moduleRegex, $q, $m);
			$relatedModule = trim($m[1]);
			$moduleRegex = "/[rR][eE][lL][aA][tT][eE][dD]\.([^\s;]+)\s*=\s*([^\s;]+)/";
			preg_match($moduleRegex, $q, $m);
			$moduleName = trim($m[1]);
			$id = trim($m[2],"(')");
			$mysql_query = __getRLQuery($id, $moduleName, $relatedModule, $queryParameters, $this->user);
			// where, limit and order
			$afterwhere=substr($q,stripos($q,' where ')+6);
			// eliminate related conditions
			$relatedCond = "/\(*[rR][eE][lL][aA][tT][eE][dD]\.([^\s;]+)\s*=\s*([^\s;]+)\)*\s*([aA][nN][dD]|[oO][rR]\s)*/";
			preg_match($relatedCond,$afterwhere,$pieces);
			$glue = isset($pieces[3]) ? trim($pieces[3]) : 'and';
			$afterwhere=trim(preg_replace($relatedCond,'',$afterwhere),' ;');
			$relatedCond = "/\s+([aA][nN][dD]|[oO][rR])+\s+([oO][rR][dD][eE][rR])+/";
			$afterwhere=trim(preg_replace($relatedCond,' order ',$afterwhere),' ;');
			$relatedCond = "/\s+([aA][nN][dD]|[oO][rR])+\s+([lL][iI][mM][iI][tT])+/";
			$afterwhere=trim(preg_replace($relatedCond,' limit ',$afterwhere),' ;');
			// if related is at the end of condition we need to strip last and|or
			if (strtolower(substr($afterwhere,-3))=='and')
				$afterwhere = substr($afterwhere,0,strlen($afterwhere)-3);
			if (strtolower(substr($afterwhere,-2))=='or')
				$afterwhere = substr($afterwhere,0,strlen($afterwhere)-2);
			// transform REST ids
			$relatedCond = "/=\s*'*\d+x(\d+)'*/";
			$afterwhere=preg_replace($relatedCond,' = $1 ',$afterwhere);
			// kill unbalanced parenthesis
			$balanced=0;
			$pila=array();
			for ($ch=0;$ch<strlen($afterwhere);$ch++) {
				if ($afterwhere[$ch]=='(') {
					$pila[$balanced]=array('pos'=>$ch,'dir'=>'(');
					$balanced++;
				} elseif ($afterwhere[$ch]==')') {
					if ($balanced>0 and $pila[$balanced-1]['dir']=='(') {
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
			if (strtolower($relatedModule)=='modcomments' and (strtolower($moduleName)=='helpdesk' or strtolower($moduleName)=='faq')) {
				$afterwhere = str_ireplace('commentcontent','comments',$afterwhere);
			}
			$relhandler = vtws_getModuleHandlerFromName($moduleName,$this->user);
			$relmeta = $relhandler->getMeta();
			$queryRelatedModules[$moduleName] = $relmeta;
			// transform fieldnames to columnnames
			$handler = vtws_getModuleHandlerFromName($relatedModule,$this->user);
			$meta = $handler->getMeta();
			$fldmap = $meta->getFieldColumnMapping();
			$tblmap = $meta->getColumnTableMapping();
			$tok = strtok($afterwhere,' ');
			$chgawhere = '';
			while ($tok !== false) {
				if (!empty($fldmap[$tok]))
					$chgawhere .= (strpos($tok, '.') ? '' : $tblmap[$fldmap[$tok]].'.').$fldmap[$tok].' ';
				else
					$chgawhere .= $tok.' ';
				$tok = strtok(' ');
			}
			$afterwhere = $chgawhere;
			if (!empty($afterwhere)) {
				$start = strtolower(substr(trim($afterwhere),0,5));
				if ($start!='limit' and $start!='order') // there is a condition we add the glue
					$mysql_query.=" $glue ";
				$mysql_query.=" $afterwhere";
			}
			if (stripos($q,'count(*)')>0)
				$mysql_query = str_ireplace(' as count ','',mkCountQuery($mysql_query));
		} elseif (__FQNExtendedQueryIsFQNQuery($q)) {  // FQN extended syntax
			list($mysql_query,$queryRelatedModules) = __FQNExtendedQueryGetQuery($q, $this->user);
			$moduleRegex = "/[fF][rR][Oo][Mm]\s+([^\s;]+)/";
			preg_match($moduleRegex, $q, $m);
			$fromModule = trim($m[1]);
			$handler = vtws_getModuleHandlerFromName($fromModule,$this->user);
			$meta = $handler->getMeta();
		} else {
			$parser = new Parser($this->user, $q);
			$error = $parser->parse();
			if($error){
				return $parser->getError();
			}
			$mysql_query = $parser->getSql();
			$meta = $parser->getObjectMetaData();
		}
		return $mysql_query;
	}

	public function query($q){
		$mysql_query = $this->wsVTQL2SQL($q,$meta,$queryRelatedModules);
		$this->pearDB->startTransaction();
		$result = $this->pearDB->pquery($mysql_query, array());
		$error = $this->pearDB->hasFailedTransaction();
		$this->pearDB->completeTransaction();
		
		if($error){
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR,
				vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$DATABASEQUERYERROR));
		}

		$noofrows = $this->pearDB->num_rows($result);
		$output = array();
		for($i=0; $i<$noofrows; $i++){
			$row = $this->pearDB->fetchByAssoc($result,$i);
			if(!$meta->hasPermission(EntityMeta::$RETRIEVE,$row["crmid"])){
				continue;
			}
			$newrow = DataTransform::sanitizeDataWithColumn($row,$meta);
			if (__FQNExtendedQueryIsFQNQuery($q)) { // related query
				$relflds = array_diff_key($row,$newrow);
				foreach ($queryRelatedModules as $relmod => $relmeta) {
					$lrm = strtolower($relmod);
					$newrflds = array();
					foreach ($relflds as $fldname => $fldvalue) {
						$fldmod = substr($fldname, 0, strlen($relmod));
						if (isset($row[$fldname]) and $fldmod==$lrm) {
							$newkey = substr($fldname, strlen($lrm));
							$newrflds[$newkey] = $fldvalue;
						}
					}
					$relrow = DataTransform::sanitizeDataWithColumn($newrflds,$relmeta);
					$newrelrow = array();
					foreach ($relrow as $key => $value) {
						$newrelrow[$lrm.$key] = $value;
					}
					$newrow = array_merge($newrow,$newrelrow);
				}
			}
			$output[] = $newrow;
		}
		return $output;
	}
	
	public function describe($elementType){
		$app_strings = VTWS_PreserveGlobal::getGlobal('app_strings');
		$current_user = vtws_preserveGlobal('current_user',$this->user);

		$label = (isset($app_strings[$elementType]))? $app_strings[$elementType]:$elementType;
		$createable = (strcasecmp(isPermitted($elementType,EntityMeta::$CREATE),'yes')===0)? true:false;
		$updateable = (strcasecmp(isPermitted($elementType,EntityMeta::$UPDATE),'yes')===0)? true:false;
		$deleteable = $this->meta->hasDeleteAccess();
		$retrieveable = $this->meta->hasReadAccess();
		$fields = $this->getModuleFields();
		return array("label"=>$label,"name"=>$elementType,"createable"=>$createable,"updateable"=>$updateable,
				"deleteable"=>$deleteable,"retrieveable"=>$retrieveable,"fields"=>$fields,
				"idPrefix"=>$this->meta->getEntityId(),'isEntity'=>$this->isEntity,'labelFields'=>$this->meta->getNameFields());
	}
	
	function getModuleFields(){
		static $purified_mfcache = array();
		$mfkey = $this->meta->getTabName();
		if (array_key_exists($mfkey, $purified_mfcache)) {
			return $purified_mfcache[$mfkey];
		}
		$fields = array();
		$moduleFields = $this->meta->getModuleFields();
		foreach ($moduleFields as $fieldName=>$webserviceField) {
			if(((int)$webserviceField->getPresence()) == 1) {
				continue;
			}
			array_push($fields,$this->getDescribeFieldArray($webserviceField));
		}
		array_push($fields,$this->getIdField($this->meta->getObectIndexColumn()));
		$purified_mfcache[$mfkey] = $fields;
		return $fields;
	}

	function getDescribeFieldArray($webserviceField){
		static $purified_dfcache = array();
		$dfkey = $webserviceField->getFieldName().$webserviceField->getTabId();
		if (array_key_exists($dfkey, $purified_dfcache)) {
			return $purified_dfcache[$dfkey];
		}
		$default_language = isset($this->user->language) ? $this->user->language : VTWS_PreserveGlobal::getGlobal('default_language');

		require 'modules/'.$this->meta->getTabName()."/language/$default_language.lang.php";
		$fieldLabel = $webserviceField->getFieldLabelKey();
		if(isset($mod_strings[$fieldLabel])){
			$fieldLabel = $mod_strings[$fieldLabel];
		}
		$typeDetails = $this->getFieldTypeDetails($webserviceField);

		//set type name, in the type details array.
		$typeDetails['name'] = $webserviceField->getFieldDataType();
		$editable = $this->isEditable($webserviceField);

		$blkname = $webserviceField->getBlockName();
		$describeArray = array('name'=>$webserviceField->getFieldName(),'label'=>$fieldLabel,'mandatory'=>
			$webserviceField->isMandatory(),'type'=>$typeDetails,'nullable'=>$webserviceField->isNullable(),
			"editable"=>$editable,'uitype'=>$webserviceField->getUIType(),'typeofdata'=>$webserviceField->getTypeOfData(),
			'sequence'=>$webserviceField->getFieldSequence(),'quickcreate'=>$webserviceField->getQuickCreate(),'displaytype'=>$webserviceField->getDisplayType(),
			'block'=>array('blockid'=>$webserviceField->getBlockId(),'blocksequence'=>$webserviceField->getBlockSequence(),
				'blocklabel'=>$blkname,'blockname'=>getTranslatedString($blkname,$this->meta->getTabName())));
		if($webserviceField->hasDefault()){
			$describeArray['default'] = $webserviceField->getDefault();
		}
		$purified_dfcache[$dfkey] = $describeArray;
		return $describeArray;
	}
	
	function getMeta(){
		return $this->meta;
	}
	
	function getField($fieldName){
		$moduleFields = $this->meta->getModuleFields();
		return $this->getDescribeFieldArray($moduleFields[$fieldName]);
	}
	
}
?>
