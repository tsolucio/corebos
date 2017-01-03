<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************/

class WebserviceField{
	private $fieldId;
	private $uitype;
	private $blockId;
	private $blockName;
	private $blockSequence;
	private $nullable;
	private $default;
	private $tableName;
	private $columnName;
	private $fieldName;
	private $fieldLabel;
	private $fieldSequence;
	private $editable;
	private $fieldType;
	private $displayType;
	private $mandatory;
	private $massEditable;
	private $tabid;
	private $presence;
	private $quickCreate;
	/**
	 *
	 * @var PearDatabase
	 */
	private $pearDB;
	private $typeOfData;
	private $fieldDataType;
	private $dataFromMeta;
	private static $tableMeta = array();
	private static $fieldTypeMapping = array();
	private $referenceList;
	private $defaultValuePresent;
	private $explicitDefaultValue;
	
	private $genericUIType = 10;

	private $readOnly = 0;
	
	private function __construct($adb,$row){
		$this->uitype = (isset($row['uitype']))? $row['uitype'] : 0;
		$this->blockId = (isset($row['block']))? $row['block'] : 0;
		$this->blockName = null;
		$this->blockSequence = $this->getBlockSequence();
		$this->tableName = (isset($row['tablename']))? $row['tablename'] : '';
		$this->columnName = (isset($row['columnname']))? $row['columnname'] : '';
		$this->fieldName = (isset($row['fieldname']))? $row['fieldname'] : '';
		$this->fieldLabel = (isset($row['fieldlabel']))? $row['fieldlabel'] : '';
		$this->fieldSequence = (isset($row['sequence']))? $row['sequence'] : 0;
		$this->displayType = (isset($row['displaytype']))? $row['displaytype'] : -1;
		$this->massEditable = isset($row['masseditable']) ? ($row['masseditable'] === '1')? true: false: false;
		$typeOfData = (isset($row['typeofdata']))? $row['typeofdata'] : '';
		$this->presence = (isset($row['presence']))? $row['presence'] : -1;
		$this->quickCreate = isset($row['quickcreate']) ? ($row['quickcreate'] === '0' || $row['quickcreate'] === '2')? true: false: false;
		$this->typeOfData = $typeOfData;
		$typeOfData = explode("~",$typeOfData);
		$this->mandatory = isset($typeOfData[1]) ? ($typeOfData[1] == 'M')? true: false: false;
		if($this->uitype == 4){
			$this->mandatory = false;
		}
		$this->fieldType = $typeOfData[0];
		$this->tabid = (isset($row['tabid']))? $row['tabid'] : 0;
		$this->fieldId = (isset($row['fieldid']))? $row['fieldid'] : 0;
		$this->pearDB = $adb;
		$this->fieldDataType = null;
		$this->dataFromMeta = false;
		$this->defaultValuePresent = false;
		$this->referenceList = null;
		$this->explicitDefaultValue = false;

		$this->readOnly = (isset($row['readonly']))? $row['readonly'] : 0;

		if(is_array($row) && array_key_exists('defaultvalue', $row)) {
			$this->setDefault($row['defaultvalue']);
		}
	}
	
	public static function fromQueryResult($adb,$result,$rowNumber){
		 return new WebserviceField($adb,$adb->query_result_rowdata($result,$rowNumber));
	}
	
	public static function fromArray($adb,$row){
		return new WebserviceField($adb,$row);
	}
	
	public function getTableName(){
		return $this->tableName;
	}
	
	public function getFieldName(){
		return $this->fieldName;
	}
	
	public function getFieldLabelKey(){
		return $this->fieldLabel;
	}
	
	public function getFieldType(){
		return $this->fieldType;
	}
	
	public function isMandatory(){
		return $this->mandatory;
	}
	
	public function getTypeOfData(){
		return $this->typeOfData;
	}
	
	public function getDisplayType(){
		return $this->displayType;
	}
	
	public function getMassEditable(){
		return $this->massEditable;
	}
	
	public function getFieldId(){
		return $this->fieldId;
	}
	
	public function getDefault(){
		if($this->dataFromMeta !== true && $this->explicitDefaultValue !== true){
			$this->fillColumnMeta();
		}
		return $this->default;
	}
	
	public function getColumnName(){
		return $this->columnName;
	}
	
	public function getBlockId(){
		return $this->blockId;
	}
	
	public function getBlockName(){
		if(empty($this->blockName)) {
			$this->blockName = getBlockName($this->blockId);
		}
		return $this->blockName;
	}

	public function getBlockSequence(){
		if(empty($this->blockSequence)) {
			if(empty($this->blockId)) {
				$this->blockSequence = 0;
			} else {
				global $adb;
				$blkseqrs = $adb->query('select sequence from vtiger_blocks where blockid='.$this->blockId);
				$this->blockSequence = $adb->query_result($blkseqrs,0,0);
			}
		}
		return $this->blockSequence;
	}

	public function getFieldSequence(){
		return $this->fieldSequence;
	}

	public function getQuickCreate(){
		return $this->quickCreate;
	}

	public function getTabId(){
		return $this->tabid;
	}

	public function isNullable(){
		if($this->dataFromMeta !== true){
			$this->fillColumnMeta();
		}
		return $this->nullable;
	}
	
	public function hasDefault(){
		if($this->dataFromMeta !== true && $this->explicitDefaultValue !== true){
			$this->fillColumnMeta();
		}
		return $this->defaultValuePresent;
	}
	
	public function getUIType(){
		return $this->uitype;
	}

	public function isReadOnly() {
		if($this->readOnly == 1) return true;
		return false;
	}
	
	private function setNullable($nullable){
		$this->nullable = $nullable;
	}
	
	public function setDefault($value){
		$this->default = $value;
		$this->explicitDefaultValue = true;
		$this->defaultValuePresent = true;
	}
	
	public function setFieldDataType($dataType){
		$this->fieldDataType = $dataType;
	}
	
	public function setReferenceList($referenceList){
		$this->referenceList = $referenceList;
	}
	
	public function getTableFields(){
		$tableFields = null;
		if(isset(WebserviceField::$tableMeta[$this->getTableName()])){
			$tableFields = WebserviceField::$tableMeta[$this->getTableName()];
		}else{
			$dbMetaColumns = $this->pearDB->database->MetaColumns($this->getTableName());
			$tableFields = array();
                        if(is_array($dbMetaColumns)){
                            foreach ($dbMetaColumns as $key => $dbField) {
                                    $tableFields[$dbField->name] = $dbField;
                            }
                        }
			WebserviceField::$tableMeta[$this->getTableName()] = $tableFields;
		}
		return $tableFields;
	}
	public function fillColumnMeta(){
		$tableFields = $this->getTableFields();
		foreach ($tableFields as $fieldName => $dbField) {
			if(strcmp($fieldName,$this->getColumnName())===0){
				$this->setNullable(!$dbField->not_null);
				if($dbField->has_default === true && !$this->explicitDefaultValue){
					$this->defaultValuePresent = $dbField->has_default;
					$this->setDefault($dbField->default_value);
				}
			}
		}
		$this->dataFromMeta = true;
	}
	
	public function getFieldDataType(){
		if($this->fieldDataType === null){
			$fieldDataType = $this->getFieldTypeFromUIType();
			if($fieldDataType === null){
				$fieldDataType = $this->getFieldTypeFromTypeOfData();
			}
			if($fieldDataType == 'date' || $fieldDataType == 'datetime' || $fieldDataType == 'time') {
				$tableFieldDataType = $this->getFieldTypeFromTable();
				if($tableFieldDataType == 'datetime'){
					$fieldDataType = $tableFieldDataType;
				}
			}
			$this->fieldDataType = $fieldDataType;
		}
		return $this->fieldDataType;
	}
	
	public function getReferenceList(){
		static $referenceList = array();
		if($this->referenceList === null){
			if(isset($referenceList[$this->getFieldId()])){
				$this->referenceList = $referenceList[$this->getFieldId()];
				return $referenceList[$this->getFieldId()];
			}
			if(!isset(WebserviceField::$fieldTypeMapping[$this->getUIType()])){
				$this->getFieldTypeFromUIType();
			}
			$fieldTypeData = WebserviceField::$fieldTypeMapping[$this->getUIType()];
			$referenceTypes = array();
			if($this->getUIType() != $this->genericUIType){
				$sql = "select * from vtiger_ws_referencetype where fieldtypeid=?";
				$params = array($fieldTypeData['fieldtypeid']);
			}else{
				$sql = 'select relmodule as type from vtiger_fieldmodulerel where fieldid=?';
				$params = array($this->getFieldId());
			}
			$result = $this->pearDB->pquery($sql,$params);
			$numRows = $this->pearDB->num_rows($result);
			for($i=0;$i<$numRows;++$i){
				array_push($referenceTypes,$this->pearDB->query_result($result,$i,"type"));
			}
			
			//to handle hardcoding done for Calendar module todo activities.
			if($this->tabid == 9 && $this->fieldName =='parent_id'){
				$referenceTypes[] = 'Invoice';
				$referenceTypes[] = 'Quotes';
				$referenceTypes[] = 'PurchaseOrder';
				$referenceTypes[] = 'SalesOrder';
				$referenceTypes[] = 'Campaigns';
			}
			if ($this->getUIType()==26) { // DocumentFolders
				$referenceTypes[] = 'DocumentFolders';
			}
			
			global $current_user;
			$types = vtws_listtypes(null, $current_user);
			$accessibleTypes = $types['types'];
			if(!is_admin($current_user)) {
				array_push($accessibleTypes, 'Users');
			}
			$referenceTypes = array_values(array_intersect($accessibleTypes,$referenceTypes));
			$referenceList[$this->getFieldId()] = $referenceTypes;
			$this->referenceList = $referenceTypes;
			return $referenceTypes;
		}
		return $this->referenceList;
	}
	
	private function getFieldTypeFromTable(){
		$tableFields = $this->getTableFields();
		foreach ($tableFields as $fieldName => $dbField) {
			if(strcmp($fieldName,$this->getColumnName())===0){
				return $dbField->type;
			}
		}
		//This should not be returned if entries in DB are correct.
		return null;
	}
	
	private function getFieldTypeFromTypeOfData(){
		switch($this->fieldType){
			case 'T': return "time";
			case 'D':
			case 'DT': return "date";
			case 'E': return "email";
			case 'N':
			case 'NN': return "double";
			case 'P': return "password";
			case 'I': return "integer";
			case 'V':
			default: return "string";
		}
	}
	
	private function getFieldTypeFromUIType(){
		
		// Cache all the information for futher re-use
		if(empty(self::$fieldTypeMapping)) {
			$result = $this->pearDB->pquery("select * from vtiger_ws_fieldtype", array());
			while($resultrow = $this->pearDB->fetch_array($result)) {
				self::$fieldTypeMapping[$resultrow['uitype']] = $resultrow;
			}
		}
		
		if(isset(WebserviceField::$fieldTypeMapping[$this->getUIType()])){
			if(WebserviceField::$fieldTypeMapping[$this->getUIType()] === false){
				return null;
			}
			$row = WebserviceField::$fieldTypeMapping[$this->getUIType()];
			return $row['fieldtype'];
		} else {
			WebserviceField::$fieldTypeMapping[$this->getUIType()] = false;
			return null;
		}
	}
	
	function getPicklistDetails(){
		$hardCodedPickListNames = array("hdntaxtype","email_flag");
		$hardCodedPickListValues = array(
				"hdntaxtype"=>array(
					array("label"=>"Individual","value"=>"individual"),
					array("label"=>"Group","value"=>"group")
				),
				"email_flag" => array(
					array('label'=>'SAVED','value'=>'SAVED'),
					array('label'=>'SENT','value' => 'SENT'),
					array('label'=>'MAILSCANNER','value' => 'MAILSCANNER')
				)
			);
		if(in_array(strtolower($this->getFieldName()),$hardCodedPickListNames)){
			return $hardCodedPickListValues[strtolower($this->getFieldName())];
		}
		$uitype = $this->getUIType();
		switch ($uitype) {
			case '1613':
			case '1614':
			case '3313':
			case '3314':
			case '1024':
				return $this->getPickListOptionsSpecialUitypes($uitype);
				break;
			default: // 15 and 33
				return $this->getPickListOptions($this->getFieldName());
				break;
		}
	}

	function getPickListOptions(){
		global $app_strings, $mod_strings, $log, $current_language;
		static $purified_plcache = array();
		$fieldName = $this->getFieldName();
		
		$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');
		$moduleName = getTabModuleName($this->getTabId());
		if($moduleName == 'Events') $moduleName = 'Calendar';
		$temp_mod_strings = ($moduleName != '' ) ? return_module_language($current_language, $moduleName) : $mod_strings;
		if (array_key_exists($moduleName.$fieldName, $purified_plcache)) {
			return $purified_plcache[$moduleName.$fieldName];
		}
		$options = array();
		$sql = "select * from vtiger_picklist where name=?";
		$result = $this->pearDB->pquery($sql,array($fieldName));
		$numRows = $this->pearDB->num_rows($result);
		if($numRows == 0){
			$sql = "select * from vtiger_$fieldName";
			$result = $this->pearDB->pquery($sql,array());
			$numRows = $this->pearDB->num_rows($result);
			for($i=0;$i<$numRows;++$i){
				$elem = array();
				$picklistValue = $this->pearDB->query_result($result,$i,$fieldName);
				$picklistValue = decode_html($picklistValue);
				$trans_str = (!empty($temp_mod_strings[$picklistValue])) ? $temp_mod_strings[$picklistValue] : ((!empty($app_strings[$picklistValue])) ? $app_strings[$picklistValue] : $picklistValue);
				while ($trans_str != preg_replace('/(.*) {.+}(.*)/', '$1$2', $trans_str)) $trans_str = preg_replace('/(.*) {.+}(.*)/', '$1$2', $trans_str);
				$elem["label"] = $trans_str;
				$elem["value"] = $picklistValue;
				array_push($options,$elem);
			}
		}else{
			$user = VTWS_PreserveGlobal::getGlobal('current_user');
			$details = getPickListValues($fieldName,$user->roleid);
			for($i=0;$i<sizeof($details);++$i){
				$elem = array();
				$picklistValue = decode_html($details[$i]);
				$trans_str = (!empty($temp_mod_strings[$picklistValue])) ? $temp_mod_strings[$picklistValue] : ((!empty($app_strings[$picklistValue])) ? $app_strings[$picklistValue] : $picklistValue);
				while ($trans_str != preg_replace('/(.*) {.+}(.*)/', '$1$2', $trans_str)) $trans_str = preg_replace('/(.*) {.+}(.*)/', '$1$2', $trans_str);
				$elem["label"] = $trans_str;
				$elem["value"] = $picklistValue;
				array_push($options,$elem);
			}
		}
		$purified_plcache[$moduleName.$fieldName] = $options;
		return $options;
	}

	function getPresence() {
		return $this->presence;
	}

	function getPickListOptionsSpecialUitypes($uitype){
		global $log, $current_language;
		require_once 'modules/PickList/PickListUtils.php';
		static $purified_plcache = array();
		$fieldName = $this->getFieldName();

		$moduleName = getTabModuleName($this->getTabId());
		if($moduleName == 'Events') $moduleName = 'Calendar';

		if (array_key_exists($moduleName.$fieldName, $purified_plcache)) {
			return $purified_plcache[$moduleName.$fieldName];
		}
		$options = array();
		$list_options = getPicklistValuesSpecialUitypes($uitype,$fieldName,'');
		foreach ($list_options as $key => $value) {
				$elem = array();
				$elem["label"] = $value[0];
				$elem["value"] = $value[1];
				array_push($options,$elem);
		}
		$purified_plcache[$moduleName.$fieldName] = $options;
		return $options;
	}
}

?>
