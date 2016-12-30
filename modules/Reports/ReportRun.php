<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
global $app_strings,$mod_strings, $theme, $log;

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once('include/database/PearDatabase.php');
require_once('data/CRMEntity.php');
require_once("modules/Reports/Reports.php");
require_once 'modules/Reports/ReportUtils.php';
require_once("vtlib/Vtiger/Module.php");
include_once("include/fields/InventoryLineField.php");

class ReportRun extends CRMEntity {

	var $primarymodule;
	var $secondarymodule;
	var $orderbylistsql;
	var $orderbylistcolumns;

	var $selectcolumns;
	var $groupbylist;
	var $reporttype;
	var $reportname;
	var $totallist;
	var $number_of_rows;
	var $page = 1;
	var $islastpage = false;

	var $_groupinglist  = false;
	var $_columnslist    = false;
	var $_stdfilterlist = false;
	var $_columnstotallist = false;
	var $_columnstotallistaddtoselect = false;
	var $_advfiltersql = false;

	var $append_currency_symbol_to_value = array('Products_Unit_Price','Services_Price',
						'Invoice_Total', 'Invoice_Sub_Total', 'Invoice_S&H_Amount', 'Invoice_Discount_Amount', 'Invoice_Adjustment',
						'Quotes_Total', 'Quotes_Sub_Total', 'Quotes_S&H_Amount', 'Quotes_Discount_Amount', 'Quotes_Adjustment',
						'SalesOrder_Total', 'SalesOrder_Sub_Total', 'SalesOrder_S&H_Amount', 'SalesOrder_Discount_Amount', 'SalesOrder_Adjustment',
						'PurchaseOrder_Total', 'PurchaseOrder_Sub_Total', 'PurchaseOrder_S&H_Amount', 'PurchaseOrder_Discount_Amount', 'PurchaseOrder_Adjustment',
						'Issuecards_Total', 'Issuecards_Sub_Total', 'Issuecards_S&H_Amount', 'Issuecards_Discount_Amount', 'Issuecards_Adjustment',
						);
	var $ui10_fields = array();
	var $ui101_fields = array();
	var $groupByTimeParent = array(
		'Quarter'=>array('Year'),
		'Month'=>array('Year'),
		'Day'=>array('Year','Month')
	);

	/** Function to set reportid,primarymodule,secondarymodule,reporttype,reportname, for given reportid
	 *  This function accepts the $reportid as argument
	 *  It sets reportid,primarymodule,secondarymodule,reporttype,reportname for the given reportid
	 */
	function __construct($reportid) {
		$oReport = new Reports($reportid);
		$this->reportid = $reportid;
		$this->primarymodule = $oReport->primodule;
		$this->secondarymodule = $oReport->secmodule;
		$this->reporttype = $oReport->reporttype;
		$this->reportname = $oReport->reportname;
	}

	/** Function to get the columns for the reportid
	 *  This function accepts the $reportid and $outputformat (optional)
	 *  This function returns  $columnslist Array: $tablename:$columnname:$fieldlabel:$fieldname:$typeofdata=>$tablename.$columnname As Header value,
	 *					$tablename1:$columnname1:$fieldlabel1:$fieldname1:$typeofdata1=>$tablename1.$columnname1 As Header value,
	 *					|
	 *					$tablenamen:$columnnamen:$fieldlabeln:$fieldnamen:$typeofdatan=>$tablenamen.$columnnamen As Header value
	 */
	function getQueryColumnsList($reportid,$outputformat='')
	{
		// Have we initialized information already?
		if($this->_columnslist !== false) {
			return $this->_columnslist;
		}

		global $adb, $modules, $log,$current_user,$current_language;
		$ssql = "select vtiger_selectcolumn.* from vtiger_report inner join vtiger_selectquery on vtiger_selectquery.queryid = vtiger_report.queryid";
		$ssql .= " left join vtiger_selectcolumn on vtiger_selectcolumn.queryid = vtiger_selectquery.queryid";
		$ssql .= " where vtiger_report.reportid = ?";
		$ssql .= " order by vtiger_selectcolumn.columnindex";
		$result = $adb->pquery($ssql, array($reportid));
		$permitted_fields = Array();

		while($columnslistrow = $adb->fetch_array($result))
		{
			$fieldname = '';
			$fieldcolname = decode_html($columnslistrow['columnname']);
			if (strpos($fieldcolname, ':')===false) continue;
			list($tablename,$colname,$module_field,$fieldname,$single) = explode(":",$fieldcolname);
			$module_field = decode_html($module_field);
			list($module,$field) = explode("_",$module_field,2);
			$inventory_fields = array('quantity','listprice','serviceid','productid','discount','comment');
			$inventory_modules = getInventoryModules();
			require('user_privileges/user_privileges_'.$current_user->id.'.php');
			if((!isset($permitted_fields[$module]) || sizeof($permitted_fields[$module]) == 0) && $is_admin == false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1)
			{
				$permitted_fields[$module] = $this->getaccesfield($module);
			}
			if(in_array($module,$inventory_modules) and isset($permitted_fields[$module]) and is_array($permitted_fields[$module])){
				$permitted_fields[$module] = array_merge($permitted_fields[$module],$inventory_fields);
			}
			$selectedfields = explode(":",$fieldcolname);
			if($is_admin == false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1
					&& !in_array($selectedfields[3], $permitted_fields[$module])) {
				//user has no access to this field, skip it.
				continue;
			}
			$concatSql = getSqlForNameInDisplayFormat(array('first_name'=>$selectedfields[0].".first_name",'last_name'=>$selectedfields[0].".last_name"), 'Users');
			$querycolumns = $this->getEscapedColumns($selectedfields);

			if(isset($module) && $module!="") {
				$mod_strings = return_module_language($current_language,$module);
			}

			$fieldlabel = trim(preg_replace("/$module/"," ",$selectedfields[2],1));
			$mod_arr=explode('_',$fieldlabel);
			$fieldlabel = trim(str_replace("_"," ",$fieldlabel));
			//modified code to support i18n issue
			$fld_arr = explode(" ",$fieldlabel);
			if(($mod_arr[0] == '')) {
				$mod = $module;
				$mod_lbl = getTranslatedString($module,$module); //module
			} else {
				$mod = $mod_arr[0];
				array_shift($fld_arr);
				$mod_lbl = getTranslatedString($fld_arr[0],$mod); //module
			}
			$fld_lbl_str = implode(" ",$fld_arr);
			$fld_lbl = getTranslatedString($fld_lbl_str,$module); //fieldlabel
			$fieldlabel = $mod_lbl." ".$fld_lbl;
			if(($selectedfields[0] == "vtiger_usersRel1") && ($selectedfields[1] == 'user_name') && ($selectedfields[2] == 'Quotes_Inventory_Manager')){
				$columnslist[$fieldcolname] = "trim( $concatSql ) as ".$module."_Inventory_Manager";
				continue;
			}

			if((CheckFieldPermission($fieldname,$mod) != 'true' && $colname!="crmid" && (!in_array($fieldname,$inventory_fields) && in_array($module,$inventory_modules))) || empty($fieldname))
			{
				continue;
			}
			else
			{
				$header_label = $selectedfields[2]; // Header label to be displayed in the reports table
				// To check if the field in the report is a custom field
				// and if yes, get the label of this custom field freshly from the vtiger_field as it would have been changed.
				// Asha - Reference ticket : #4906

				if($querycolumns == '') {
					if($selectedfields[4] == 'C')
					{
						$field_label_data = explode("_",$selectedfields[2]);
						$module= $field_label_data[0];
						if($module!=$this->primarymodule)
							$columnslist[$fieldcolname] = "case when (".$selectedfields[0].".".$selectedfields[1]."='1')then '".getTranslatedString('LBL_YES')."' else case when (vtiger_crmentity$module.crmid !='') then '".getTranslatedString('LBL_NO')."' else '-' end end as '$selectedfields[2]'";
						else
							$columnslist[$fieldcolname] = "case when (".$selectedfields[0].".".$selectedfields[1]."='1')then '".getTranslatedString('LBL_YES')."' else case when (vtiger_crmentity.crmid !='') then '".getTranslatedString('LBL_NO')."' else '-' end end as '$selectedfields[2]'";
					}
					elseif($selectedfields[0] == 'vtiger_activity' && $selectedfields[1] == 'status')
					{
						$columnslist[$fieldcolname] = " case when (vtiger_activity.status not like '') then vtiger_activity.status else vtiger_activity.eventstatus end as Calendar_Status";
					}
					elseif($selectedfields[0] == 'vtiger_activity' && $selectedfields[1] == 'date_start')
					{
						$columnslist[$fieldcolname] = "cast(concat(vtiger_activity.date_start,'  ',vtiger_activity.time_start) as DATETIME) as Calendar_Start_Date_and_Time";
					}
					elseif(stristr($selectedfields[0],"vtiger_users") && ($selectedfields[1] == 'user_name'))
					{
						$temp_module_from_tablename = str_replace("vtiger_users","",$selectedfields[0]);
						if($module!=$this->primarymodule){
							$condition = "and vtiger_crmentity".$module.".crmid!=''";
						} else {
							$condition = "and vtiger_crmentity.crmid!=''";
						}
						if($temp_module_from_tablename == $module)
							$columnslist[$fieldcolname] = " case when(".$selectedfields[0].".last_name NOT LIKE '' $condition ) THEN ".$concatSql." else vtiger_groups".$module.".groupname end as '".$module."_$field'";
						else//Some Fields can't assigned to groups so case avoided (fields like inventory manager)
							$columnslist[$fieldcolname] = $selectedfields[0].".user_name as '".$header_label."'";

					}
					elseif(stristr($selectedfields[0],"vtiger_crmentity") && ($selectedfields[1] == 'modifiedby')) {
						$concatSql = getSqlForNameInDisplayFormat(array('last_name'=>'vtiger_lastModifiedBy'.$module.'.last_name', 'first_name'=>'vtiger_lastModifiedBy'.$module.'.first_name'), 'Users');
						$columnslist[$fieldcolname] = "trim($concatSql) as $header_label";
					}
					elseif($selectedfields[0] == "vtiger_crmentity".$this->primarymodule)
					{
						$columnslist[$fieldcolname] = "vtiger_crmentity.".$selectedfields[1]." AS '".$header_label."'";
					}
					elseif($selectedfields[0] == 'vtiger_products' && $selectedfields[1] == 'unit_price')//handled for product fields in Campaigns Module Reports
					{
						$columnslist[$fieldcolname] = "concat(".$selectedfields[0].".currency_id,'::',innerProduct.actual_unit_price) as '". $header_label ."'";
					}
					elseif(in_array($selectedfields[2], $this->append_currency_symbol_to_value)) {
						$columnslist[$fieldcolname] = "concat(".$selectedfields[0].".currency_id,'::',".$selectedfields[0].".".$selectedfields[1].") as '" . $header_label ."'";
					}
					elseif($selectedfields[0] == 'vtiger_notes' && ($selectedfields[1] == 'filelocationtype' || $selectedfields[1] == 'filesize' || $selectedfields[1] == 'folderid' || $selectedfields[1]=='filestatus'))//handled for product fields in Campaigns Module Reports
					{
						if($selectedfields[1] == 'filelocationtype'){
							$columnslist[$fieldcolname] = "case ".$selectedfields[0].".".$selectedfields[1]." when 'I' then 'Internal' when 'E' then 'External' else '-' end as '$selectedfields[2]'";
						} else if($selectedfields[1] == 'folderid'){
							$columnslist[$fieldcolname] = "vtiger_attachmentsfolder.foldername as '$selectedfields[2]'";
						} elseif($selectedfields[1] == 'filestatus'){
							$columnslist[$fieldcolname] = "case ".$selectedfields[0].".".$selectedfields[1]." when '1' then '".getTranslatedString('LBL_YES')."' when '0' then '".getTranslatedString('LBL_NO')."' else '-' end as '$selectedfields[2]'";
						} elseif($selectedfields[1] == 'filesize'){
							$columnslist[$fieldcolname] = "case ".$selectedfields[0].".".$selectedfields[1]." when '' then '-' else concat(".$selectedfields[0].".".$selectedfields[1]."/1024,'  ','KB') end as '$selectedfields[2]'";
						}
					}
					elseif($selectedfields[0] == 'vtiger_inventoryproductrel')//handled for product fields in Campaigns Module Reports
					{
						if($selectedfields[1] == 'discount'){
							$columnslist[$fieldcolname] = " case when (vtiger_inventoryproductrel{$module}.discount_amount != '') then vtiger_inventoryproductrel{$module}.discount_amount else ROUND((vtiger_inventoryproductrel{$module}.listprice * vtiger_inventoryproductrel{$module}.quantity * (vtiger_inventoryproductrel{$module}.discount_percent/100)),3) end as '" . $header_label ."'";
						} else if($selectedfields[1] == 'productid'){
							$columnslist[$fieldcolname] = "vtiger_products{$module}.productname as '" . $header_label ."'";
						} else if($selectedfields[1] == 'serviceid'){
							$columnslist[$fieldcolname] = "vtiger_service{$module}.servicename as '" . $header_label ."'";
						} else {
							$columnslist[$fieldcolname] = $selectedfields[0].$module.".".$selectedfields[1]." as '".$header_label."'";
						}
					}
					elseif($selectedfields[0] == 'vtiger_inventoryproductrel'.$module)//handled for product fields in Campaigns Module Reports
					{
						if($selectedfields[1] == 'discount'){
							$columnslist[$fieldcolname] = " case when (vtiger_inventoryproductrel{$module}.discount_amount != '') then vtiger_inventoryproductrel{$module}.discount_amount else ROUND((vtiger_inventoryproductrel{$module}.listprice * vtiger_inventoryproductrel{$module}.quantity * (vtiger_inventoryproductrel{$module}.discount_percent/100)),3) end as '" . $header_label ."'";
						} else if($selectedfields[1] == 'productid'){
							$columnslist[$fieldcolname] = "vtiger_products{$module}.productname as '" . $header_label ."'";
						} else if($selectedfields[1] == 'serviceid'){
							$columnslist[$fieldcolname] = "vtiger_service{$module}.servicename as '" . $header_label ."'";
						} else {
							$columnslist[$fieldcolname] = $selectedfields[0].".".$selectedfields[1]." as '".$header_label."'";
						}
					}
					elseif(stristr($selectedfields[1],'cf_')==true && stripos($selectedfields[1],'cf_')==0)
					{
						$columnslist[$fieldcolname] = $selectedfields[0].".".$selectedfields[1]." AS '".$adb->sql_escape_string(decode_html($header_label))."'";
					}
					else
					{
						$columnslist[$fieldcolname] = $selectedfields[0].".".$selectedfields[1]." AS '".$header_label."'";
					}
				}
				else
				{
					$columnslist[$fieldcolname] = $querycolumns;
				}
			}
		}
		$columnslist['vtiger_crmentity:crmid:LBL_ACTION:crmid:I'] = 'vtiger_crmentity.crmid AS "LBL_ACTION"' ;
		// Save the information
		$this->_columnslist = $columnslist;

		$log->info("ReportRun :: Successfully returned getQueryColumnsList".$reportid);
		return $columnslist;
	}

	/** Function to get field columns based on profile
	 *  @ param $module : Type string
	 *  returns permitted fields in array format
	 */
	function getaccesfield($module) {
		global $current_user;
		global $adb;
		$access_fields = Array();

		$profileList = getCurrentUserProfileList();
		$query = "select vtiger_field.fieldname from vtiger_field inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid where";
		$params = array();
		if($module == "Calendar")
		{
			if (count($profileList) > 0) {
				$query .= " vtiger_field.tabid in (9,16) and vtiger_field.displaytype in (1,2,3) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0
								and vtiger_field.presence IN (0,2) and vtiger_profile2field.profileid in (". generateQuestionMarks($profileList) .") group by vtiger_field.fieldid order by block,sequence";
				array_push($params, $profileList);
			} else {
				$query .= " vtiger_field.tabid in (9,16) and vtiger_field.displaytype in (1,2,3) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0
								and vtiger_field.presence IN (0,2) group by vtiger_field.fieldid order by block,sequence";
			}
		}
		else
		{
			array_push($params, $module);
			if (count($profileList) > 0) {
				$query .= " vtiger_field.tabid in (select tabid from vtiger_tab where vtiger_tab.name in (?)) and vtiger_field.displaytype in (1,2,3,5) and vtiger_profile2field.visible=0
								and vtiger_field.presence IN (0,2) and vtiger_def_org_field.visible=0 and vtiger_profile2field.profileid in (". generateQuestionMarks($profileList) .") group by vtiger_field.fieldid order by block,sequence";
				array_push($params, $profileList);
			} else {
				$query .= " vtiger_field.tabid in (select tabid from vtiger_tab where vtiger_tab.name in (?)) and vtiger_field.displaytype in (1,2,3,5) and vtiger_profile2field.visible=0
								and vtiger_field.presence IN (0,2) and vtiger_def_org_field.visible=0 group by vtiger_field.fieldid order by block,sequence";
			}
		}
		$result = $adb->pquery($query, $params);

		while($collistrow = $adb->fetch_array($result))
		{
			$access_fields[] = $collistrow["fieldname"];
		}
		//added to include ticketid for Reports module in select columnlist for all users
		if($module == "HelpDesk")
			$access_fields[] = "ticketid";
		return $access_fields;
	}

	/** Function to get Escapedcolumns for the field in case of multiple parents
	 *  @ param $selectedfields : Type Array
	 *  returns the case query for the escaped columns
	 */
	function getEscapedColumns($selectedfields) {
		$tableName = $selectedfields[0];
		$columnName = $selectedfields[1];
		$moduleFieldLabel = $selectedfields[2];
		$fieldName = $selectedfields[3];
		list($moduleName, $fieldLabel) = explode('_', $moduleFieldLabel, 2);
		$fieldInfo = getFieldByReportLabel($moduleName, $fieldLabel);
		$queryColumn = '';
		if($moduleName == 'ModComments' && $fieldName == 'creator') {
			$concatSql = getSqlForNameInDisplayFormat(array('first_name' => 'vtiger_usersModComments.first_name',
															'last_name' => 'vtiger_usersModComments.last_name'), 'Users');
			$queryColumn = "trim(case when (vtiger_usersModComments.user_name not like '' and vtiger_crmentity.crmid!='') then $concatSql end) as 'ModComments_Creator'";
		} elseif(($fieldInfo['uitype'] == '10' || isReferenceUIType($fieldInfo['uitype']))
				&& $fieldInfo['uitype'] != '52' && $fieldInfo['uitype'] != '53') {
			$fieldSqlColumns = $this->getReferenceFieldColumnList($moduleName, $fieldInfo);
			if(count($fieldSqlColumns) > 0) {
				$queryColumn = "(CASE WHEN $tableName.$columnName NOT LIKE '' THEN (CASE";
				foreach($fieldSqlColumns as $columnSql) {
					$queryColumn .= " WHEN $columnSql NOT LIKE '' THEN $columnSql";
				}
				$queryColumn .= " ELSE '' END) ELSE '' END) AS $moduleFieldLabel";
			}
		}
		return $queryColumn;
	}

	/** Function to get selectedcolumns for the given reportid
	 *  @ param $reportid : Type Integer
	 *  returns the query of columnlist for the selected columns
	 */
	function getSelectedColumnsList($reportid)
	{
		global $adb, $modules, $log;

		$ssql = "select vtiger_selectcolumn.* from vtiger_report inner join vtiger_selectquery on vtiger_selectquery.queryid = vtiger_report.queryid";
		$ssql .= " left join vtiger_selectcolumn on vtiger_selectcolumn.queryid = vtiger_selectquery.queryid where vtiger_report.reportid = ? ";
		$ssql .= " order by vtiger_selectcolumn.columnindex";

		$result = $adb->pquery($ssql, array($reportid));
		$noofrows = $adb->num_rows($result);

		if ($this->orderbylistsql != "")
		{
			$sSQL .= $this->orderbylistsql.", ";
		}

		for($i=0; $i<$noofrows; $i++)
		{
			$fieldcolname = $adb->query_result($result,$i,"columnname");
			$ordercolumnsequal = true;
			if($fieldcolname != "")
			{
				for($j=0;$j<count($this->orderbylistcolumns);$j++)
				{
					if($this->orderbylistcolumns[$j] == $fieldcolname)
					{
						$ordercolumnsequal = false;
						break;
					}else
					{
						$ordercolumnsequal = true;
					}
				}
				if($ordercolumnsequal)
				{
					$selectedfields = explode(":",$fieldcolname);
					if($selectedfields[0] == "vtiger_crmentity".$this->primarymodule)
						$selectedfields[0] = "vtiger_crmentity";
					$sSQLList[] = $selectedfields[0].".".$selectedfields[1]." '".$selectedfields[2]."'";
				}
			}
		}
		$sSQL .= implode(",",$sSQLList);

		$log->info("ReportRun :: Successfully returned getSelectedColumnsList".$reportid);
		return $sSQL;
	}

	/** Function to get advanced comparator in query form for the given Comparator and value
	 *  @ param $comparator : Type String
	 *  @ param $value : Type String
	 *  returns the check query for the comparator
	 */
	function getAdvComparator($comparator,$value,$datatype="")
	{

		global $log,$adb,$default_charset,$ogReport;
		$value=html_entity_decode(trim($value),ENT_QUOTES,$default_charset);
		$value_len = strlen($value);
		$is_field = false;
		if($value_len > 1 && $value[0]=='$' && $value[$value_len-1]=='$'){
			$temp = str_replace('$','',$value);
			$is_field = true;
		}
		if($datatype=='C'){
			$value = str_replace("yes","1",str_replace("no","0",$value));
		}

		if($is_field==true){
			$value = $this->getFilterComparedField($temp);
		}
		if($comparator == "e")
		{
			if(trim($value) == "NULL")
			{
				$rtvalue = " is NULL";
			}elseif(trim($value) != "")
			{
				$rtvalue = " = ".$adb->quote($value);
			}elseif(trim($value) == "" && $datatype == "V")
			{
				$rtvalue = " = ".$adb->quote($value);
			}else
			{
				$rtvalue = " is NULL";
			}
		}
		if($comparator == "n")
		{
			if(trim($value) == "NULL")
			{
				$rtvalue = " is NOT NULL";
			}elseif(trim($value) != "")
			{
				$rtvalue = " <> ".$adb->quote($value);
			}elseif(trim($value) == "" && $datatype == "V")
			{
				$rtvalue = " <> ".$adb->quote($value);
			}else
			{
				$rtvalue = " is NOT NULL";
			}
		}
		if($comparator == "s")
		{
			$rtvalue = " like '". formatForSqlLike($value, 2,$is_field) ."'";
		}
		if($comparator == "ew")
		{
			$rtvalue = " like '". formatForSqlLike($value, 1,$is_field) ."'";
		}
		if($comparator == "c")
		{
			$rtvalue = " like '". formatForSqlLike($value,0,$is_field) ."'";
		}
		if($comparator == "k")
		{
			$rtvalue = " not like '". formatForSqlLike($value,0,$is_field) ."'";
		}
		if($comparator == "l")
		{
			$rtvalue = " < ".$adb->quote($value);
		}
		if($comparator == "g")
		{
			$rtvalue = " > ".$adb->quote($value);
		}
		if($comparator == "m")
		{
			$rtvalue = " <= ".$adb->quote($value);
		}
		if($comparator == "h")
		{
			$rtvalue = " >= ".$adb->quote($value);
		}
		if($comparator == "b") {
			$rtvalue = " < ".$adb->quote($value);
		}
		if($comparator == "a") {
			$rtvalue = " > ".$adb->quote($value);
		}
		if($is_field==true){
			$rtvalue = str_replace("'","",$rtvalue);
			$rtvalue = str_replace("\\","",$rtvalue);
		}
		$log->info("ReportRun :: Successfully returned getAdvComparator");
		return $rtvalue;
	}

	/** Function to get field that is to be compared in query form for the given Comparator and field
	 *  @ param $field : field
	 *  returns the value for the comparator
	 */
	function getFilterComparedField($field){
		global $adb,$ogReport;
			$field = explode('#',$field);
			$module = $field[0];
			$fieldname = trim($field[1]);
			$tabid = getTabId($module);
			$field_query = $adb->pquery("SELECT tablename,columnname,typeofdata,fieldname,uitype FROM vtiger_field WHERE tabid = ? AND fieldname= ?",array($tabid,$fieldname));
			$fieldtablename = $adb->query_result($field_query,0,'tablename');
			$fieldcolname = $adb->query_result($field_query,0,'columnname');
			$typeofdata = $adb->query_result($field_query,0,'typeofdata');
			$fieldtypeofdata=ChangeTypeOfData_Filter($fieldtablename,$fieldcolname,$typeofdata[0]);
			$uitype = $adb->query_result($field_query,0,'uitype');
			/*if($tr[0]==$ogReport->primodule)
				$value = $adb->query_result($field_query,0,'tablename').".".$adb->query_result($field_query,0,'columnname');
			else
				$value = $adb->query_result($field_query,0,'tablename').$tr[0].".".$adb->query_result($field_query,0,'columnname');
			*/
			if($uitype == 68 || $uitype == 59)
			{
				$fieldtypeofdata = 'V';
			}
			if($fieldtablename == "vtiger_crmentity")
			{
				$fieldtablename = $fieldtablename.$module;
			}
			if($fieldname == "assigned_user_id")
			{
				$fieldtablename = "vtiger_users".$module;
				$fieldcolname = "user_name";
			}
			if($fieldtablename == "vtiger_crmentity" && $fieldname == "modifiedby")
			{
				$fieldtablename = "vtiger_lastModifiedBy".$module;
				$fieldcolname = "user_name";
			}
			if($fieldname == "assigned_user_id1")
			{
				$fieldtablename = "vtiger_usersRel1";
				$fieldcolname = "user_name";
			}
			$value = $fieldtablename.".".$fieldcolname;
		return $value;
	}

	/** Function to get the advanced filter columns for the reportid
	 *  This function accepts the $reportid
	 *  This function returns  $columnslist Array: $columnname => $tablename:$columnname:$fieldlabel:$fieldname:$typeofdata=>$tablename.$columnname filtercriteria,
	 *					$tablename1:$columnname1:$fieldlabel1:$fieldname1:$typeofdata1=>$tablename1.$columnname1 filtercriteria,
	 *					|
	 *					$tablenamen:$columnnamen:$fieldlabeln:$fieldnamen:$typeofdatan=>$tablenamen.$columnnamen filtercriteria
	 */
	function getAdvFilterList($reportid) {
		global $adb, $log;

		$advft_criteria = array();

		$sql = 'SELECT * FROM vtiger_relcriteria_grouping WHERE queryid = ? ORDER BY groupid';
		$groupsresult = $adb->pquery($sql, array($reportid));

		$i = 1;
		$j = 0;
		while($relcriteriagroup = $adb->fetch_array($groupsresult)) {
			$groupId = $relcriteriagroup["groupid"];
			$groupCondition = $relcriteriagroup["group_condition"];

			$ssql = 'select vtiger_relcriteria.* from vtiger_report
						inner join vtiger_relcriteria on vtiger_relcriteria.queryid = vtiger_report.queryid
						left join vtiger_relcriteria_grouping on vtiger_relcriteria.queryid = vtiger_relcriteria_grouping.queryid
								and vtiger_relcriteria.groupid = vtiger_relcriteria_grouping.groupid';
			$ssql.= " where vtiger_report.reportid = ? AND vtiger_relcriteria.groupid = ? order by vtiger_relcriteria.columnindex";

			$result = $adb->pquery($ssql, array($reportid, $groupId));
			$noOfColumns = $adb->num_rows($result);
			if($noOfColumns <= 0) continue;

			while($relcriteriarow = $adb->fetch_array($result)) {
				$columnIndex = $relcriteriarow["columnindex"];
				$criteria = array();
				$criteria['columnname'] = html_entity_decode($relcriteriarow["columnname"]);
				$criteria['comparator'] = $relcriteriarow["comparator"];
				$advfilterval = $relcriteriarow["value"];
				$col = explode(":",$relcriteriarow["columnname"]);
				list($module,$void) = explode('_',$col[2],2);
				$uitype_value = getUItypeByFieldName($module, $col[3]);
				if($uitype_value == '15' || $uitype_value == '16' || $uitype_value == '33') {
					if(!isValueInPicklist($advfilterval,$col[3]))
						$advfilterval = getTranslationKeyFromTranslatedValue($module, $advfilterval);
				}
				$criteria['value'] = $advfilterval;
				$criteria['column_condition'] = $relcriteriarow["column_condition"];

				$advft_criteria[$i]['columns'][$j] = $criteria;
				$advft_criteria[$i]['condition'] = $groupCondition;
				$j++;
			}
			if(!empty($advft_criteria[$i]['columns'][$j-1]['column_condition'])) {
				$advft_criteria[$i]['columns'][$j-1]['column_condition'] = '';
			}
			$i++;
		}
		// Clear the condition (and/or) for last group, if any.
		if(!empty($advft_criteria[$i-1]['condition'])) $advft_criteria[$i-1]['condition'] = '';
		return $advft_criteria;
	}

	function generateAdvFilterSql($advfilterlist) {
		global $adb;

		$advfiltersql = "";

		foreach($advfilterlist as $groupindex => $groupinfo) {
			$groupcondition = isset($groupinfo['condition']) ? $groupinfo['condition'] : '';
			$groupcolumns = $groupinfo['columns'];

			if(count($groupcolumns) > 0) {

				$advfiltergroupsql = "";
				foreach($groupcolumns as $columnindex => $columninfo) {
					$fieldcolname = $columninfo["columnname"];
					$comparator = $columninfo["comparator"];
					$value = $columninfo["value"];
					$columncondition = $columninfo["column_condition"];

					if($fieldcolname != "" && $comparator != "") {
						$selectedfields = explode(":",$fieldcolname);
						$moduleFieldLabel = $selectedfields[2];
						list($moduleName, $fieldLabel) = explode('_', $moduleFieldLabel, 2);
						$fieldInfo = getFieldByReportLabel($moduleName, $fieldLabel);
						$concatSql = getSqlForNameInDisplayFormat(array('first_name'=>$selectedfields[0].".first_name",'last_name'=>$selectedfields[0].".last_name"), 'Users');
						// Added to handle the crmentity table name for Primary module
						if($selectedfields[0] == "vtiger_crmentity".$this->primarymodule) {
							$selectedfields[0] = "vtiger_crmentity";
						}
						//Added to handle yes or no for checkbox field in reports advance filters. -shahul
						if($selectedfields[4] == 'C') {
							if(strcasecmp(trim($value),"yes")==0)
								$value="1";
							if(strcasecmp(trim($value),"no")==0)
								$value="0";
						}
						$valuearray = explode(",",trim($value));
						$datatype = (isset($selectedfields[4])) ? $selectedfields[4] : "";
						$secondarymodules = explode(':', $this->secondarymodule);
						array_walk($secondarymodules,function(&$val) {$val = 'vtiger_users'.$val;});
						if(isset($valuearray) && count($valuearray) > 1 && $comparator != 'bw') {

							$advcolumnsql = "";
							for($n=0;$n<count($valuearray);$n++) {
								if(($selectedfields[0] == 'vtiger_users'.$this->primarymodule || in_array($selectedfields[0],$secondarymodules)) && $selectedfields[1] == 'user_name') {
									$module_from_tablename = str_replace("vtiger_users","",$selectedfields[0]);
									$advcolsql[] = " trim($concatSql)".$this->getAdvComparator($comparator,trim($valuearray[$n]),$datatype)." or vtiger_groups".$module_from_tablename.".groupname ".$this->getAdvComparator($comparator,trim($valuearray[$n]),$datatype);
								} elseif($selectedfields[1] == 'status') {//when you use comma seperated values.
									if($selectedfields[2] == 'Calendar_Status')
									$advcolsql[] = "(case when (vtiger_activity.status not like '') then vtiger_activity.status else vtiger_activity.eventstatus end)".$this->getAdvComparator($comparator,trim($valuearray[$n]),$datatype);
									elseif($selectedfields[2] == 'HelpDesk_Status')
									$advcolsql[] = "vtiger_troubletickets.status".$this->getAdvComparator($comparator,trim($valuearray[$n]),$datatype);
								} elseif($selectedfields[1] == 'description') {//when you use comma seperated values.
									if($selectedfields[0]=='vtiger_crmentity'.$this->primarymodule)
										$advcolsql[] = "vtiger_crmentity.description".$this->getAdvComparator($comparator,trim($valuearray[$n]),$datatype);
									else
										$advcolsql[] = $selectedfields[0].".".$selectedfields[1].$this->getAdvComparator($comparator,trim($valuearray[$n]),$datatype);
								} elseif($selectedfields[2] == 'Quotes_Inventory_Manager'){
									$advcolsql[] = ("trim($concatSql)".$this->getAdvComparator($comparator,trim($valuearray[$n]),$datatype));
								} else {
									$advcolsql[] = $selectedfields[0].".".$selectedfields[1].$this->getAdvComparator($comparator,trim($valuearray[$n]),$datatype);
								}
							}
							//If negative logic filter ('not equal to', 'does not contain') is used, 'and' condition should be applied instead of 'or'
							if($comparator == 'n' || $comparator == 'k')
								$advcolumnsql = implode(" and ",$advcolsql);
							else
								$advcolumnsql = implode(" or ",$advcolsql);
							$fieldvalue = " (".$advcolumnsql.") ";
						} elseif(($selectedfields[0] == 'vtiger_users'.$this->primarymodule || in_array($selectedfields[0],$secondarymodules)) && $selectedfields[1] == 'user_name') {
							$module_from_tablename = str_replace("vtiger_users","",$selectedfields[0]);
							$fieldvalue = " trim(case when (".$selectedfields[0].".last_name NOT LIKE '') then ".$concatSql." else vtiger_groups".$module_from_tablename.".groupname end) ".$this->getAdvComparator($comparator,trim($value),$datatype);
						} elseif($comparator == 'bw' && count($valuearray) == 2) {
							if($selectedfields[0] == "vtiger_crmentity".$this->primarymodule) {
								$fieldvalue = "("."vtiger_crmentity.".$selectedfields[1]." between '".trim($valuearray[0])."' and '".trim($valuearray[1])."')";
							} elseif($selectedfields[2]=='Calendar_Start_Date_and_Time') {
								$fieldvalue = "(cast(concat(vtiger_activity.date_start,'  ',vtiger_activity.time_start) as DATETIME) between '".trim($valuearray[0])."' and '".trim($valuearray[1])."')";
							} else {
								$fieldvalue = "(".$selectedfields[0].".".$selectedfields[1]." between '".trim($valuearray[0])."' and '".trim($valuearray[1])."')";
							}
						} elseif($selectedfields[0] == "vtiger_crmentity".$this->primarymodule) {
							$fieldvalue = "vtiger_crmentity.".$selectedfields[1]." ".$this->getAdvComparator($comparator,trim($value),$datatype);
						} elseif($selectedfields[2] == 'Quotes_Inventory_Manager'){
							$fieldvalue = ("trim($concatSql)" . $this->getAdvComparator($comparator,trim($value),$datatype));
						} elseif($selectedfields[1]=='modifiedby') {
							$module_from_tablename = str_replace("vtiger_crmentity","",$selectedfields[0]);
							if($module_from_tablename != '') {
								$tableName = 'vtiger_lastModifiedBy'.$module_from_tablename;
							} else {
								$tableName = 'vtiger_lastModifiedBy'.$this->primarymodule;
							}
							$fieldvalue = getSqlForNameInDisplayFormat(array('last_name'=>"$tableName.last_name",'first_name'=>"$tableName.first_name"), 'Users').
									$this->getAdvComparator($comparator,trim($value),$datatype);
						} elseif($selectedfields[0] == "vtiger_activity" && $selectedfields[1] == 'status') {
							$fieldvalue = "(case when (vtiger_activity.status not like '') then vtiger_activity.status else vtiger_activity.eventstatus end)".$this->getAdvComparator($comparator,trim($value),$datatype);
						} elseif($comparator == 'e' && (trim($value) == "NULL" || trim($value) == '')) {
							$fieldvalue = "(".$selectedfields[0].".".$selectedfields[1]." IS NULL OR ".$selectedfields[0].".".$selectedfields[1]." = '')";
						} elseif($comparator == 'e' && $datatype == 'D' && (trim($value) == "--$" || trim($value) == '$')) {
							$fieldvalue = "(".$selectedfields[0].".".$selectedfields[1]." IS NULL OR ".$selectedfields[0].".".$selectedfields[1]." = '')";
						} elseif(substr($selectedfields[0],0,26) == 'vtiger_inventoryproductrel' && ($selectedfields[1] == 'productid' || $selectedfields[1] == 'serviceid' || $selectedfields[1] == 'discount')) {
							$invmod = (in_array($this->primarymodule, getInventoryModules()) ? $this->primarymodule : $this->secondarymodule);
							if($selectedfields[1] == 'productid'){
								$fieldvalue = "vtiger_products{$invmod}.productname ".$this->getAdvComparator($comparator,trim($value),$datatype);
							} else if($selectedfields[1] == 'serviceid'){
								$fieldvalue = "vtiger_service{$invmod}.servicename ".$this->getAdvComparator($comparator,trim($value),$datatype);
							} else if($selectedfields[1] == 'discount'){
								$fieldvalue = "(vtiger_inventoryproductrel{$invmod}.discount_amount ".$this->getAdvComparator($comparator,trim($value),$datatype)."
									OR ROUND((vtiger_inventoryproductrel{$invmod}.listprice * vtiger_inventoryproductrel{$invmod}.quantity * (vtiger_inventoryproductrel{$invmod}.discount_percent/100)),3) ".$this->getAdvComparator($comparator,trim($value),$datatype).") ";
							}
						} elseif($fieldInfo['uitype'] == '10' || isReferenceUIType($fieldInfo['uitype'])) {

							$comparatorValue = $this->getAdvComparator($comparator,trim($value),$datatype);
							$fieldSqls = array();
							$fieldSqlColumns = $this->getReferenceFieldColumnList($moduleName, $fieldInfo);
							foreach($fieldSqlColumns as $columnSql) {
								$fieldSqls[] = $columnSql.$comparatorValue;
							}
							$fieldvalue = ' ('. implode(' OR ', $fieldSqls).') ';
							} else {
							$fieldvalue = $selectedfields[0].".".$selectedfields[1].$this->getAdvComparator($comparator,trim($value),$datatype);
						}

						$advfiltergroupsql .= $fieldvalue;
						if(!empty($columncondition)) {
							$advfiltergroupsql .= ' '.$columncondition.' ';
						}
					}
				}

				if (trim($advfiltergroupsql) != "") {
					$advfiltergroupsql = "( $advfiltergroupsql ) ";
					if(!empty($groupcondition)) {
						$advfiltergroupsql .= ' '. $groupcondition . ' ';
					}
					$advfiltersql .= $advfiltergroupsql;
				}
			}
		}
		if (trim($advfiltersql) != "") $advfiltersql = '('.$advfiltersql.')';

		return $advfiltersql;
	}

	function getAdvFilterSql($reportid) {
		// Have we initialized information already?
		if($this->_advfiltersql !== false) {
			return $this->_advfiltersql;
		}
		global $log;

		$advfilterlist = $this->getAdvFilterList($reportid);
		$advfiltersql = $this->generateAdvFilterSql($advfilterlist);

		// Save the information
		$this->_advfiltersql = $advfiltersql;

		$log->info("ReportRun :: Successfully returned getAdvFilterSql".$reportid);
		return $advfiltersql;
	}

	/** Function to get the Standard filter columns for the reportid
	 *  This function accepts the $reportid datatype Integer
	 *  This function returns  $stdfilterlist Array: $columnname => $tablename:$columnname:$fieldlabel:$fieldname:$typeofdata=>$tablename.$columnname filtercriteria,
	 *					$tablename1:$columnname1:$fieldlabel1:$fieldname1:$typeofdata1=>$tablename1.$columnname1 filtercriteria,
	 */
	function getStdFilterList($reportid)
	{
		// Have we initialized information already?
		if($this->_stdfilterlist !== false) {
			return $this->_stdfilterlist;
		}

		global $adb, $log;
		$stdfilterlist = array();

		$stdfiltersql = "select vtiger_reportdatefilter.* from vtiger_report";
		$stdfiltersql .= " inner join vtiger_reportdatefilter on vtiger_report.reportid = vtiger_reportdatefilter.datefilterid";
		$stdfiltersql .= " where vtiger_report.reportid = ?";

		$result = $adb->pquery($stdfiltersql, array($reportid));
		$stdfilterrow = $adb->fetch_array($result);
		if(isset($stdfilterrow)) {
			$fieldcolname = $stdfilterrow["datecolumnname"];
			$datefilter = $stdfilterrow["datefilter"];
			$startdate = $stdfilterrow["startdate"];
			$enddate = $stdfilterrow["enddate"];

			if($fieldcolname != "none") {
				$selectedfields = explode(":",$fieldcolname);
				if($selectedfields[0] == "vtiger_crmentity".$this->primarymodule)
					$selectedfields[0] = "vtiger_crmentity";

				$moduleFieldLabel = $selectedfields[3];
				list($moduleName, $fieldLabel) = explode('_', $moduleFieldLabel, 2);
				$fieldInfo = getFieldByReportLabel($moduleName, $fieldLabel);
				$typeOfData = $fieldInfo['typeofdata'];
				list($type, $typeOtherInfo) = explode('~', $typeOfData, 2);

				if($datefilter != "custom") {
					$startenddate = $this->getStandarFiltersStartAndEndDate($datefilter);
					$startdate = $startenddate[0];
					$enddate = $startenddate[1];
				}

				if($startdate != "0000-00-00" && $enddate != "0000-00-00" && $startdate != "" && $enddate != ""
						&& $selectedfields[0] != "" && $selectedfields[1] != "") {

					$startDateTime = new DateTimeField($startdate.' '. date('H:i:s'));
					$userStartDate = $startDateTime->getDisplayDate();
					if($type == 'DT') {
						$userStartDate = $userStartDate.' 00:00:00';
					}
					$startDateTime = getValidDBInsertDateTimeValue($userStartDate);

					$endDateTime = new DateTimeField($enddate.' '. date('H:i:s'));
					$userEndDate = $endDateTime->getDisplayDate();
					if($type == 'DT') {
						$userEndDate = $userEndDate.' 23:59:00';
					}
					$endDateTime = getValidDBInsertDateTimeValue($userEndDate);

					if ($selectedfields[1] == 'birthday') {
						$tableColumnSql = "DATE_FORMAT(".$selectedfields[0].".".$selectedfields[1].", '%m%d')";
						$startDateTime = "DATE_FORMAT('$startDateTime', '%m%d')";
						$endDateTime = "DATE_FORMAT('$endDateTime', '%m%d')";
					} else {
						if($selectedfields[0] == 'vtiger_activity' && ($selectedfields[1] == 'date_start' || $selectedfields[1] == 'due_date')) {
							$tableColumnSql = '';
							if($selectedfields[1] == 'date_start') {
								$tableColumnSql = "CAST((CONCAT(date_start,' ',time_start)) AS DATETIME)";
							} else {
								$tableColumnSql = "CAST((CONCAT(due_date,' ',time_end)) AS DATETIME)";
							}
						} else {
							$tableColumnSql = $selectedfields[0].".".$selectedfields[1];
						}
						$startDateTime = "'$startDateTime'";
						$endDateTime = "'$endDateTime'";
					}

					$stdfilterlist[$fieldcolname] = $tableColumnSql." between ".$startDateTime." and ".$endDateTime;
				}
			}
		}
		// Save the information
		$this->_stdfilterlist = $stdfilterlist;

		$log->info("ReportRun :: Successfully returned getStdFilterList".$reportid);
		return $stdfilterlist;
	}

	/** Function to get the RunTime filter columns for the given $filtercolumn,$filter,$startdate,$enddate
	 *  @ param $filtercolumn : Type String
	 *  @ param $filter : Type String
	 *  @ param $startdate: Type String
	 *  @ param $enddate : Type String
	 *  This function returns  $stdfilterlist Array($columnname => $tablename:$columnname:$fieldlabel=>$tablename.$columnname 'between' $startdate 'and' $enddate)
	 *
	 */
	function RunTimeFilter($filtercolumn,$filter,$startdate,$enddate)
	{
		if($filtercolumn != "none")
		{
			$selectedfields = explode(":",$filtercolumn);
			if($selectedfields[0] == "vtiger_crmentity".$this->primarymodule)
				$selectedfields[0] = "vtiger_crmentity";
			if($filter == "custom")
			{
				if($startdate != "0000-00-00" && $enddate != "0000-00-00" && $startdate != "" &&
						$enddate != "" && $selectedfields[0] != "" && $selectedfields[1] != "") {
					$stdfilterlist[$filtercolumn] = $selectedfields[0].".".$selectedfields[1]." between '".$startdate." 00:00:00' and '".$enddate." 23:59:00'";
				}
			}else
			{
				if($startdate != "" && $enddate != "")
				{
					$startenddate = $this->getStandarFiltersStartAndEndDate($filter);
					if($startenddate[0] != "" && $startenddate[1] != "" && $selectedfields[0] != "" && $selectedfields[1] != "")
					{
						$stdfilterlist[$filtercolumn] = $selectedfields[0].".".$selectedfields[1]." between '".$startenddate[0]." 00:00:00' and '".$startenddate[1]." 23:59:00'";
					}
				}
			}

		}
		return $stdfilterlist;
	}

	/** Function to get the RunTime Advanced filter conditions
	 *  @ param $advft_criteria : Type Array
	 *  @ param $advft_criteria_groups : Type Array
	 *  This function returns  $advfiltersql
	 */
	function RunTimeAdvFilter($advft_criteria,$advft_criteria_groups) {
		$adb = PearDatabase::getInstance();

		$advfilterlist = array();
		$advfiltersql = null;
		if(!empty($advft_criteria)) {
			foreach($advft_criteria as $column_index => $column_condition) {

				if(empty($column_condition)) continue;

				$adv_filter_column = $column_condition["columnname"];
				$adv_filter_comparator = $column_condition["comparator"];
				$adv_filter_value = $column_condition["value"];
				$adv_filter_column_condition = $column_condition["columncondition"];
				$adv_filter_groupid = $column_condition["groupid"];

				$column_info = explode(":",$adv_filter_column);

				$moduleFieldLabel = $column_info[2];
				$fieldName = $column_info[3];
				list($module, $fieldLabel) = explode('_', $moduleFieldLabel, 2);
				$fieldInfo = getFieldByReportLabel($module, $fieldLabel);
				$fieldType = null;
				if(!empty($fieldInfo)) {
					$field = WebserviceField::fromArray($adb, $fieldInfo);
					$fieldType = $field->getFieldDataType();
				}

				if(($fieldType == 'currency' or $fieldType == 'double') and (substr($adv_filter_value,0,1) != "$" and substr($adv_filter_value,-1,1) != "$")) {
					$flduitype = $fieldInfo['uitype'];
					if($flduitype == '72' or $flduitype == 9 or $flduitype ==7) {
						$adv_filter_value = CurrencyField::convertToDBFormat($adv_filter_value, null, true);
					} else {
						$adv_filter_value = CurrencyField::convertToDBFormat($adv_filter_value);
					}
				}

				$temp_val = explode(",",$adv_filter_value);
				if(($column_info[4] == 'D' || ($column_info[4] == 'T' && $column_info[1] != 'time_start' && $column_info[1] != 'time_end') || ($column_info[4] == 'DT'))
				&& ($column_info[4] != '' && $adv_filter_value != '' )) {
					$val = Array();
					for($x=0;$x<count($temp_val);$x++) {
						if($column_info[4] == 'D') {
							$date = new DateTimeField(trim($temp_val[$x]));
							$val[$x] = $date->getDBInsertDateValue();
						} elseif($column_info[4] == 'DT') {
							$date = new DateTimeField(trim($temp_val[$x]));
							$val[$x] = $date->getDBInsertDateTimeValue();
						} else {
							$date = new DateTimeField(trim($temp_val[$x]));
							$val[$x] = $date->getDBInsertTimeValue();
						}
					}
					$adv_filter_value = implode(",",$val);
				}
				if($fieldType=='picklist' || $fieldType=='multipicklist') {
					if(!isValueInPicklist($adv_filter_value,$fieldName))
						$adv_filter_value = getTranslationKeyFromTranslatedValue($module, $adv_filter_value);
				}
				$criteria = array();
				$criteria['columnname'] = $adv_filter_column;
				$criteria['comparator'] = $adv_filter_comparator;
				$criteria['value'] = $adv_filter_value;
				$criteria['column_condition'] = $adv_filter_column_condition;

				$advfilterlist[$adv_filter_groupid]['columns'][] = $criteria;
			}

			if (is_array($advft_criteria_groups))
			foreach($advft_criteria_groups as $group_index => $group_condition_info) {
				if(empty($group_condition_info)) continue;
				if(empty($advfilterlist[$group_index])) continue;
				$advfilterlist[$group_index]['condition'] = $group_condition_info["groupcondition"];
				$noOfGroupColumns = count($advfilterlist[$group_index]['columns']);
				if(!empty($advfilterlist[$group_index]['columns'][$noOfGroupColumns-1]['column_condition'])) {
					$advfilterlist[$group_index]['columns'][$noOfGroupColumns-1]['column_condition'] = '';
				}
			}
			$noOfGroups = count($advfilterlist);
			if(!empty($advfilterlist[$noOfGroups]['condition'])) {
				$advfilterlist[$noOfGroups]['condition'] = '';
			}

			$advfiltersql = $this->generateAdvFilterSql($advfilterlist);
		}
		return $advfiltersql;
	}

	/** Function to get standardfilter for the given reportid
	 *  @ param $reportid : Type Integer
	 *  returns the query of columnlist for the selected columns
	 */
	function getStandardCriterialSql($reportid)
	{
		global $adb, $modules, $log;

		$sreportstdfiltersql = "select vtiger_reportdatefilter.* from vtiger_report";
		$sreportstdfiltersql .= " inner join vtiger_reportdatefilter on vtiger_report.reportid = vtiger_reportdatefilter.datefilterid";
		$sreportstdfiltersql .= " where vtiger_report.reportid = ?";

		$result = $adb->pquery($sreportstdfiltersql, array($reportid));
		$noofrows = $adb->num_rows($result);

		for($i=0; $i<$noofrows; $i++) {
			$fieldcolname = $adb->query_result($result,$i,"datecolumnname");
			$datefilter = $adb->query_result($result,$i,"datefilter");
			$startdate = $adb->query_result($result,$i,"startdate");
			$enddate = $adb->query_result($result,$i,"enddate");

			if($fieldcolname != "none") {
				$selectedfields = explode(":",$fieldcolname);
				if($selectedfields[0] == "vtiger_crmentity".$this->primarymodule)
					$selectedfields[0] = "vtiger_crmentity";
				if($datefilter == "custom") {

					if($startdate != "0000-00-00" && $enddate != "0000-00-00" && $selectedfields[0] != "" && $selectedfields[1] != ""
							&& $startdate != '' && $enddate != '') {

						$startDateTime = new DateTimeField($startdate.' '. date('H:i:s'));
						$startdate = $startDateTime->getDisplayDate();
						$endDateTime = new DateTimeField($enddate.' '. date('H:i:s'));
						$enddate = $endDateTime->getDisplayDate();

						$sSQL .= $selectedfields[0].".".$selectedfields[1]." between '".$startdate."' and '".$enddate."'";
					}
				} else {

					$startenddate = $this->getStandarFiltersStartAndEndDate($datefilter);

					$startDateTime = new DateTimeField($startenddate[0].' '. date('H:i:s'));
					$startdate = $startDateTime->getDisplayDate();
					$endDateTime = new DateTimeField($startenddate[1].' '. date('H:i:s'));
					$enddate = $endDateTime->getDisplayDate();

					if($startenddate[0] != "" && $startenddate[1] != "" && $selectedfields[0] != "" && $selectedfields[1] != "") {
						$sSQL .= $selectedfields[0].".".$selectedfields[1]." between '".$startdate."' and '".$enddate."'";
					}
				}
			}
		}
		$log->info("ReportRun :: Successfully returned getStandardCriterialSql".$reportid);
		return $sSQL;
	}

	/** Function to get standardfilter startdate and enddate for the given type
	 *  @ param $type : Type String
	 *  returns the $datevalue Array in the given format
	 * 		$datevalue = Array(0=>$startdate,1=>$enddate)
	 */
	function getStandarFiltersStartAndEndDate($type)
	{
		$today = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
		$tomorrow  = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
		$yesterday  = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-1, date("Y")));

		$currentmonth0 = date("Y-m-d",mktime(0, 0, 0, date("m"), "01",   date("Y")));
		$currentmonth1 = date("Y-m-t");
		$lastmonth0 = date("Y-m-d",mktime(0, 0, 0, date("m")-1, "01",   date("Y")));
		$lastmonth1 = date("Y-m-t", strtotime("-1 Month"));
		$nextmonth0 = date("Y-m-d",mktime(0, 0, 0, date("m")+1, "01",   date("Y")));
		$nextmonth1 = date("Y-m-t", strtotime("+1 Month"));

		$lastweek0 = date("Y-m-d",strtotime("-2 week Sunday"));
		$lastweek1 = date("Y-m-d",strtotime("-1 week Saturday"));

		$thisweek0 = date("Y-m-d",strtotime("-1 week Sunday"));
		$thisweek1 = date("Y-m-d",strtotime("this Saturday"));

		$nextweek0 = date("Y-m-d",strtotime("this Sunday"));
		$nextweek1 = date("Y-m-d",strtotime("+1 week Saturday"));

		$next7days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+6, date("Y")));
		$next30days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+29, date("Y")));
		$next60days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+59, date("Y")));
		$next90days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+89, date("Y")));
		$next120days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+119, date("Y")));

		$last7days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-6, date("Y")));
		$last30days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-29, date("Y")));
		$last60days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-59, date("Y")));
		$last90days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-89, date("Y")));
		$last120days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-119, date("Y")));

		$currentFY0 = date("Y-m-d",mktime(0, 0, 0, "01", "01",   date("Y")));
		$currentFY1 = date("Y-m-t",mktime(0, 0, 0, "12", date("d"),   date("Y")));
		$lastFY0 = date("Y-m-d",mktime(0, 0, 0, "01", "01",   date("Y")-1));
		$lastFY1 = date("Y-m-t", mktime(0, 0, 0, "12", date("d"), date("Y")-1));
		$nextFY0 = date("Y-m-d",mktime(0, 0, 0, "01", "01",   date("Y")+1));
		$nextFY1 = date("Y-m-t", mktime(0, 0, 0, "12", date("d"), date("Y")+1));

		if(date("m") <= 3)
		{
			$cFq = date("Y-m-d",mktime(0, 0, 0, "01","01",date("Y")));
			$cFq1 = date("Y-m-d",mktime(0, 0, 0, "03","31",date("Y")));
			$nFq = date("Y-m-d",mktime(0, 0, 0, "04","01",date("Y")));
			$nFq1 = date("Y-m-d",mktime(0, 0, 0, "06","30",date("Y")));
			$pFq = date("Y-m-d",mktime(0, 0, 0, "10","01",date("Y")-1));
			$pFq1 = date("Y-m-d",mktime(0, 0, 0, "12","31",date("Y")-1));
		}else if(date("m") > 3 and date("m") <= 6)
		{
			$pFq = date("Y-m-d",mktime(0, 0, 0, "01","01",date("Y")));
			$pFq1 = date("Y-m-d",mktime(0, 0, 0, "03","31",date("Y")));
			$cFq = date("Y-m-d",mktime(0, 0, 0, "04","01",date("Y")));
			$cFq1 = date("Y-m-d",mktime(0, 0, 0, "06","30",date("Y")));
			$nFq = date("Y-m-d",mktime(0, 0, 0, "07","01",date("Y")));
			$nFq1 = date("Y-m-d",mktime(0, 0, 0, "09","30",date("Y")));

		}else if(date("m") > 6 and date("m") <= 9)
		{
			$nFq = date("Y-m-d",mktime(0, 0, 0, "10","01",date("Y")));
			$nFq1 = date("Y-m-d",mktime(0, 0, 0, "12","31",date("Y")));
			$pFq = date("Y-m-d",mktime(0, 0, 0, "04","01",date("Y")));
			$pFq1 = date("Y-m-d",mktime(0, 0, 0, "06","30",date("Y")));
			$cFq = date("Y-m-d",mktime(0, 0, 0, "07","01",date("Y")));
			$cFq1 = date("Y-m-d",mktime(0, 0, 0, "09","30",date("Y")));
		}
		else if(date("m") > 9 and date("m") <= 12)
		{
			$nFq = date("Y-m-d",mktime(0, 0, 0, "01","01",date("Y")+1));
			$nFq1 = date("Y-m-d",mktime(0, 0, 0, "03","31",date("Y")+1));
			$pFq = date("Y-m-d",mktime(0, 0, 0, "07","01",date("Y")));
			$pFq1 = date("Y-m-d",mktime(0, 0, 0, "09","30",date("Y")));
			$cFq = date("Y-m-d",mktime(0, 0, 0, "10","01",date("Y")));
			$cFq1 = date("Y-m-d",mktime(0, 0, 0, "12","31",date("Y")));

		}

		if($type == "today" )
		{

			$datevalue[0] = $today;
			$datevalue[1] = $today;
		}
		elseif($type == "yesterday" )
		{

			$datevalue[0] = $yesterday;
			$datevalue[1] = $yesterday;
		}
		elseif($type == "tomorrow" )
		{

			$datevalue[0] = $tomorrow;
			$datevalue[1] = $tomorrow;
		}
		elseif($type == "thisweek" )
		{

			$datevalue[0] = $thisweek0;
			$datevalue[1] = $thisweek1;
		}
		elseif($type == "lastweek" )
		{

			$datevalue[0] = $lastweek0;
			$datevalue[1] = $lastweek1;
		}
		elseif($type == "nextweek" )
		{

			$datevalue[0] = $nextweek0;
			$datevalue[1] = $nextweek1;
		}
		elseif($type == "thismonth" )
		{

			$datevalue[0] =$currentmonth0;
			$datevalue[1] = $currentmonth1;
		}

		elseif($type == "lastmonth" )
		{

			$datevalue[0] = $lastmonth0;
			$datevalue[1] = $lastmonth1;
		}
		elseif($type == "nextmonth" )
		{

			$datevalue[0] = $nextmonth0;
			$datevalue[1] = $nextmonth1;
		}
		elseif($type == "next7days" )
		{

			$datevalue[0] = $today;
			$datevalue[1] = $next7days;
		}
		elseif($type == "next30days" )
		{

			$datevalue[0] =$today;
			$datevalue[1] =$next30days;
		}
		elseif($type == "next60days" )
		{

			$datevalue[0] = $today;
			$datevalue[1] = $next60days;
		}
		elseif($type == "next90days" )
		{

			$datevalue[0] = $today;
			$datevalue[1] = $next90days;
		}
		elseif($type == "next120days" )
		{

			$datevalue[0] = $today;
			$datevalue[1] = $next120days;
		}
		elseif($type == "last7days" )
		{

			$datevalue[0] = $last7days;
			$datevalue[1] = $today;
		}
		elseif($type == "last30days" )
		{

			$datevalue[0] = $last30days;
			$datevalue[1] =  $today;
		}
		elseif($type == "last60days" )
		{

			$datevalue[0] = $last60days;
			$datevalue[1] = $today;
		}
		else if($type == "last90days" )
		{

			$datevalue[0] = $last90days;
			$datevalue[1] = $today;
		}
		elseif($type == "last120days" )
		{

			$datevalue[0] = $last120days;
			$datevalue[1] = $today;
		}
		elseif($type == "thisfy" )
		{

			$datevalue[0] = $currentFY0;
			$datevalue[1] = $currentFY1;
		}
		elseif($type == "prevfy" )
		{

			$datevalue[0] = $lastFY0;
			$datevalue[1] = $lastFY1;
		}
		elseif($type == "nextfy" )
		{

			$datevalue[0] = $nextFY0;
			$datevalue[1] = $nextFY1;
		}
		elseif($type == "nextfq" )
		{

			$datevalue[0] = $nFq;
			$datevalue[1] = $nFq1;
		}
		elseif($type == "prevfq" )
		{

			$datevalue[0] = $pFq;
			$datevalue[1] = $pFq1;
		}
		elseif($type == "thisfq" )
		{
			$datevalue[0] = $cFq;
			$datevalue[1] = $cFq1;
		}
		else
		{
			$datevalue[0] = "";
			$datevalue[1] = "";
		}
		return $datevalue;
	}

	/** Function to get getGroupingList for the given reportid
	 *  @ param $reportid : Type Integer
	 *  returns the $grouplist Array in the following format
	 *  		$grouplist = Array($tablename:$columnname:$fieldlabel:fieldname:typeofdata=>$tablename:$columnname $sorder,
	 *				   $tablename1:$columnname1:$fieldlabel1:fieldname1:typeofdata1=>$tablename1:$columnname1 $sorder,
	 *				   $tablename2:$columnname2:$fieldlabel2:fieldname2:typeofdata2=>$tablename2:$columnname2 $sorder)
	 * This function also sets the return value in the class variable $this->groupbylist
	 */
	function getGroupingList($reportid)
	{
		global $adb, $modules, $log;

		// Have we initialized information already?
		if($this->_groupinglist !== false) {
			return $this->_groupinglist;
		}

		$sreportsortsql = " SELECT vtiger_reportsortcol.*, vtiger_reportgroupbycolumn.* FROM vtiger_report";
		$sreportsortsql .= " inner join vtiger_reportsortcol on vtiger_report.reportid = vtiger_reportsortcol.reportid";
		$sreportsortsql .= " LEFT JOIN vtiger_reportgroupbycolumn ON (vtiger_report.reportid = vtiger_reportgroupbycolumn.reportid AND vtiger_reportsortcol.sortcolid = vtiger_reportgroupbycolumn.sortid)";
		$sreportsortsql .= " where vtiger_report.reportid =? AND vtiger_reportsortcol.columnname IN (SELECT columnname from vtiger_selectcolumn WHERE queryid=?) order by vtiger_reportsortcol.sortcolid";

		$result = $adb->pquery($sreportsortsql, array($reportid,$reportid));
		$grouplist = array();

		while($reportsortrow = $adb->fetch_array($result))
		{
			$fieldcolname = $reportsortrow['columnname'];
			if($fieldcolname != 'none') {
				list($tablename,$colname,$module_field,$fieldname,$single) = explode(":",$fieldcolname);
				$sortorder = $reportsortrow["sortorder"];
				if($sortorder == "Ascending")
				{
					$sortorder = "ASC";
				}elseif($sortorder == "Descending")
				{
					$sortorder = "DESC";
				}
				$selectedfields = explode(":",$fieldcolname);
				if($selectedfields[0] == "vtiger_crmentity".$this->primarymodule)
					$selectedfields[0] = "vtiger_crmentity";
				if(stripos($selectedfields[1],'cf_')==0 && stristr($selectedfields[1],'cf_')==true){
					$sqlvalue = "".$adb->sql_escape_string(decode_html($selectedfields[2]))." ".$sortorder;
				} else {
					$sqlvalue = "`".self::replaceSpecialChar($selectedfields[2])."` ".$sortorder;
				}
				/************** MONOLITHIC phase 6 customization********************************/
				if($selectedfields[4]=="D" && strtolower($reportsortrow["dategroupbycriteria"])!="none"){
					$groupField = $module_field;
					$groupCriteria = $reportsortrow["dategroupbycriteria"];
					if(in_array($groupCriteria,array_keys($this->groupByTimeParent))){
						$parentCriteria = $this->groupByTimeParent[$groupCriteria];
						foreach($parentCriteria as $criteria){
							$groupByCondition[]=$this->GetTimeCriteriaCondition($criteria, $groupField)." ".$sortorder;
						}
					}
					$groupByCondition[] =$this->GetTimeCriteriaCondition($groupCriteria, $groupField)." ".$sortorder;
					$sqlvalue = implode(", ",$groupByCondition);
				}
				$grouplist[$fieldcolname] = $sqlvalue;
				$temp = explode("_",$selectedfields[2],2);
				$module = $temp[0];
				if((strpos($selectedfields[0],'vtiger_inventoryproductrel') !== false && ($selectedfields[1]=='productid' || $selectedfields[1]=='serviceid')) || CheckFieldPermission($fieldname,$module) == 'true')
				{
					$grouplist[$fieldcolname] = $sqlvalue;
				} else {
					$grouplist[$fieldcolname] = $selectedfields[0].".".$selectedfields[1];
				}
			}
		}

		// Save the information
		$this->_groupinglist = $grouplist;

		$log->info("ReportRun :: Successfully returned getGroupingList".$reportid);
		return $grouplist;
	}

	/** function to replace special characters
	 *  @ param $selectedfield : type string
	 *  this returns the string for grouplist
	 */
	public static function replaceSpecialChar($selectedfield){
		$selectedfield = decode_html(decode_html($selectedfield));
		preg_match('/&/', $selectedfield, $matches);
		if(!empty($matches)){
			$selectedfield = str_replace('&', 'and',($selectedfield));
		}
		return $selectedfield;
	}

	/** function to get the selectedorderbylist for the given reportid
	 *  @ param $reportid : type integer
	 *  this returns the columns query for the sortorder columns
	 *  this function also sets the return value in the class variable $this->orderbylistsql
	 */
	function getSelectedOrderbyList($reportid)
	{
		global $adb, $modules, $log;

		$sreportsortsql = "select vtiger_reportsortcol.* from vtiger_report";
		$sreportsortsql .= " inner join vtiger_reportsortcol on vtiger_report.reportid = vtiger_reportsortcol.reportid";
		$sreportsortsql .= " where vtiger_report.reportid =? order by vtiger_reportsortcol.sortcolid";

		$result = $adb->pquery($sreportsortsql, array($reportid));
		$noofrows = $adb->num_rows($result);

		for($i=0; $i<$noofrows; $i++)
		{
			$fieldcolname = $adb->query_result($result,$i,"columnname");
			$sortorder = $adb->query_result($result,$i,"sortorder");

			if($sortorder == "Ascending")
			{
				$sortorder = "ASC";
			}
			elseif($sortorder == "Descending")
			{
				$sortorder = "DESC";
			}

			if($fieldcolname != "none")
			{
				$this->orderbylistcolumns[] = $fieldcolname;
				$n = $n + 1;
				$selectedfields = explode(":",$fieldcolname);
				if($n > 1)
				{
					$sSQL .= ", ";
					$this->orderbylistsql .= ", ";
				}
				if($selectedfields[0] == "vtiger_crmentity".$this->primarymodule)
					$selectedfields[0] = "vtiger_crmentity";
				$sSQL .= $selectedfields[0].".".$selectedfields[1]." ".$sortorder;
				$this->orderbylistsql .= $selectedfields[0].".".$selectedfields[1]." ".$selectedfields[2];
			}
		}
		$log->info("ReportRun :: Successfully returned getSelectedOrderbyList".$reportid);
		return $sSQL;
	}

	/** function to get secondary Module for the given Primary module and secondary module
	 *  @ param $module : type String
	 *  @ param $secmodule : type String
	 *  this returns join query for the given secondary module
	 */
	function getRelatedModulesQuery($module,$secmodule,$type = '',$where_condition = '')
	{
		global $log,$current_user;
		$query = '';
		if($secmodule!=''){
			$secondarymodule = explode(":",$secmodule);
			foreach($secondarymodule as $key=>$value) {
					$foc = CRMEntity::getInstance($value);
					$query .= $foc->generateReportsSecQuery($module,$value,$type,$where_condition);
					$query .= getNonAdminAccessControlQuery($value,$current_user,$value);
			}
		}
		$log->info("ReportRun :: Successfully returned getRelatedModulesQuery".$secmodule);
		return $query;
	}

	/** function to get report query for the given module
	 *  @ param $module : type String
	 *  this returns join query for the given module
	 */
	function getReportsQuery($module, $type='', $where_condition = '')
	{
		global $log, $current_user;
		$secondary_module ="'";
		$secondary_module .= str_replace(":","','",$this->secondarymodule);
		$secondary_module .="'";

		if($module == "Leads")
		{
			$val_conv = ((isset($_COOKIE['LeadConv']) && $_COOKIE['LeadConv'] == 'true') ? 1 : 0);
			$query = "from vtiger_leaddetails
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_leaddetails.leadid
				inner join vtiger_leadsubdetails on vtiger_leadsubdetails.leadsubscriptionid=vtiger_leaddetails.leadid
				inner join vtiger_leadaddress on vtiger_leadaddress.leadaddressid=vtiger_leadsubdetails.leadsubscriptionid
				inner join vtiger_leadscf on vtiger_leaddetails.leadid = vtiger_leadscf.leadid
				left join vtiger_groups as vtiger_groupsLeads on vtiger_groupsLeads.groupid = vtiger_crmentity.smownerid
				left join vtiger_users as vtiger_usersLeads on vtiger_usersLeads.id = vtiger_crmentity.smownerid
				left join vtiger_groups on vtiger_groups.groupid = vtiger_crmentity.smownerid
				left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
				left join vtiger_users as vtiger_lastModifiedByLeads on vtiger_lastModifiedByLeads.id = vtiger_crmentity.modifiedby
				".$this->getRelatedModulesQuery($module,$this->secondarymodule,$type,$where_condition).
						getNonAdminAccessControlQuery($this->primarymodule,$current_user)."
				where vtiger_crmentity.deleted=0 and vtiger_leaddetails.converted=$val_conv";
		}
		else if($module == "Accounts")
		{
			$focus = CRMEntity::getInstance($module);
			$query = $focus->generateReportsQuery($module);
			$query.= " inner join vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid
				inner join vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid
				left join vtiger_account as vtiger_accountAccounts on vtiger_accountAccounts.accountid = vtiger_account.parentid
				".$this->getRelatedModulesQuery($module,$this->secondarymodule,$type,$where_condition).
						getNonAdminAccessControlQuery($this->primarymodule,$current_user)."
				where vtiger_crmentity.deleted=0 ";
		}

		else if($module == "Contacts")
		{
			$query = "from vtiger_contactdetails
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid
				inner join vtiger_contactaddress on vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid
				inner join vtiger_customerdetails on vtiger_customerdetails.customerid = vtiger_contactdetails.contactid
				inner join vtiger_contactsubdetails on vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid
				inner join vtiger_contactscf on vtiger_contactdetails.contactid = vtiger_contactscf.contactid
				left join vtiger_groups vtiger_groupsContacts on vtiger_groupsContacts.groupid = vtiger_crmentity.smownerid
				left join vtiger_contactdetails as vtiger_contactdetailsContacts on vtiger_contactdetailsContacts.contactid = vtiger_contactdetails.reportsto
				left join vtiger_account as vtiger_accountContacts on vtiger_accountContacts.accountid = vtiger_contactdetails.accountid
				left join vtiger_users as vtiger_usersContacts on vtiger_usersContacts.id = vtiger_crmentity.smownerid
				left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
				left join vtiger_groups on vtiger_groups.groupid = vtiger_crmentity.smownerid
				left join vtiger_users as vtiger_lastModifiedByContacts on vtiger_lastModifiedByContacts.id = vtiger_crmentity.modifiedby
				".$this->getRelatedModulesQuery($module,$this->secondarymodule,$type,$where_condition).
						getNonAdminAccessControlQuery($this->primarymodule,$current_user)."
				where vtiger_crmentity.deleted=0";
		}

		else if($module == "Potentials")
		{
			$focus = CRMEntity::getInstance($module);
			$query = $focus->generateReportsQuery($module);
			$query.= " left join vtiger_campaign as vtiger_campaignPotentials on vtiger_potential.campaignid = vtiger_campaignPotentials.campaignid
				".$this->getRelatedModulesQuery($module,$this->secondarymodule,$type,$where_condition).
						getNonAdminAccessControlQuery($this->primarymodule,$current_user)."
				where vtiger_crmentity.deleted=0 ";
		}

		//For this Product - we can related Accounts, Contacts (Also Leads, Potentials)
		else if($module == "Products")
		{
			$focus = CRMEntity::getInstance($module);
			$query = $focus->generateReportsQuery($module);
			$query.= " left join vtiger_vendor as vtiger_vendorRelProducts on vtiger_vendorRelProducts.vendorid = vtiger_products.vendor_id
				LEFT JOIN (
						SELECT vtiger_products.productid,
								(CASE WHEN (vtiger_products.currency_id = 1 ) THEN vtiger_products.unit_price
									ELSE (vtiger_products.unit_price / vtiger_currency_info.conversion_rate) END
								) AS actual_unit_price
						FROM vtiger_products
						LEFT JOIN vtiger_currency_info ON vtiger_products.currency_id = vtiger_currency_info.id
						LEFT JOIN vtiger_productcurrencyrel ON vtiger_products.productid = vtiger_productcurrencyrel.productid
						AND vtiger_productcurrencyrel.currencyid = ". $current_user->currency_id . "
				) AS innerProduct ON innerProduct.productid = vtiger_products.productid
				".$this->getRelatedModulesQuery($module,$this->secondarymodule,$type,$where_condition).
						getNonAdminAccessControlQuery($this->primarymodule,$current_user)."
				where vtiger_crmentity.deleted=0";
		}

		else if($module == "HelpDesk")
		{
			$query = "from vtiger_troubletickets
				inner join vtiger_crmentity
				on vtiger_crmentity.crmid=vtiger_troubletickets.ticketid
				inner join vtiger_ticketcf on vtiger_ticketcf.ticketid = vtiger_troubletickets.ticketid
				left join vtiger_crmentity as vtiger_crmentityRelHelpDesk on vtiger_crmentityRelHelpDesk.crmid = vtiger_troubletickets.parent_id
				left join vtiger_account as vtiger_accountRelHelpDesk on vtiger_accountRelHelpDesk.accountid=vtiger_crmentityRelHelpDesk.crmid
				left join vtiger_contactdetails as vtiger_contactdetailsRelHelpDesk on vtiger_contactdetailsRelHelpDesk.contactid= vtiger_crmentityRelHelpDesk.crmid
				left join vtiger_products as vtiger_productsRel on vtiger_productsRel.productid = vtiger_troubletickets.product_id
				left join vtiger_groups as vtiger_groupsHelpDesk on vtiger_groupsHelpDesk.groupid = vtiger_crmentity.smownerid
				left join vtiger_users as vtiger_usersHelpDesk on vtiger_crmentity.smownerid=vtiger_usersHelpDesk.id
				left join vtiger_groups on vtiger_groups.groupid = vtiger_crmentity.smownerid
				left join vtiger_users on vtiger_crmentity.smownerid=vtiger_users.id
				left join vtiger_users as vtiger_lastModifiedByHelpDesk on vtiger_lastModifiedByHelpDesk.id = vtiger_crmentity.modifiedby
				".$this->getRelatedModulesQuery($module,$this->secondarymodule,$type,$where_condition).
						getNonAdminAccessControlQuery($this->primarymodule,$current_user)."
				where vtiger_crmentity.deleted=0 ";
		}

		else if($module == "Calendar")
		{
			$query = "from vtiger_activity
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
				left join vtiger_activitycf on vtiger_activitycf.activityid = vtiger_crmentity.crmid
				left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid= vtiger_activity.activityid
				left join vtiger_contactdetails as vtiger_contactdetailsCalendar on vtiger_contactdetailsCalendar.contactid= vtiger_cntactivityrel.contactid
				left join vtiger_groups as vtiger_groupsCalendar on vtiger_groupsCalendar.groupid = vtiger_crmentity.smownerid
				left join vtiger_users as vtiger_usersCalendar on vtiger_usersCalendar.id = vtiger_crmentity.smownerid
				left join vtiger_groups on vtiger_groups.groupid = vtiger_crmentity.smownerid
				left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
				left join vtiger_seactivityrel on vtiger_seactivityrel.activityid = vtiger_activity.activityid
				left join vtiger_activity_reminder on vtiger_activity_reminder.activity_id = vtiger_activity.activityid
				left join vtiger_recurringevents on vtiger_recurringevents.activityid = vtiger_activity.activityid
				left join vtiger_crmentity as vtiger_crmentityRelCalendar on vtiger_crmentityRelCalendar.crmid = vtiger_seactivityrel.crmid
				left join vtiger_account as vtiger_accountRelCalendar on vtiger_accountRelCalendar.accountid=vtiger_crmentityRelCalendar.crmid
				left join vtiger_leaddetails as vtiger_leaddetailsRelCalendar on vtiger_leaddetailsRelCalendar.leadid = vtiger_crmentityRelCalendar.crmid
				left join vtiger_potential as vtiger_potentialRelCalendar on vtiger_potentialRelCalendar.potentialid = vtiger_crmentityRelCalendar.crmid
				left join vtiger_quotes as vtiger_quotesRelCalendar on vtiger_quotesRelCalendar.quoteid = vtiger_crmentityRelCalendar.crmid
				left join vtiger_purchaseorder as vtiger_purchaseorderRelCalendar on vtiger_purchaseorderRelCalendar.purchaseorderid = vtiger_crmentityRelCalendar.crmid
				left join vtiger_invoice as vtiger_invoiceRelCalendar on vtiger_invoiceRelCalendar.invoiceid = vtiger_crmentityRelCalendar.crmid
				left join vtiger_salesorder as vtiger_salesorderRelCalendar on vtiger_salesorderRelCalendar.salesorderid = vtiger_crmentityRelCalendar.crmid
				left join vtiger_troubletickets as vtiger_troubleticketsRelCalendar on vtiger_troubleticketsRelCalendar.ticketid = vtiger_crmentityRelCalendar.crmid
				left join vtiger_campaign as vtiger_campaignRelCalendar on vtiger_campaignRelCalendar.campaignid = vtiger_crmentityRelCalendar.crmid
				left join vtiger_vendor as vtiger_vendorRelCalendar on vtiger_vendorRelCalendar.vendorid = vtiger_crmentityRelCalendar.crmid
				left join vtiger_users as vtiger_lastModifiedByCalendar on vtiger_lastModifiedByCalendar.id = vtiger_crmentity.modifiedby
				".$this->getRelatedModulesQuery($module,$this->secondarymodule).
						getNonAdminAccessControlQuery($this->primarymodule,$current_user)."
				WHERE vtiger_crmentity.deleted=0 and (vtiger_activity.activitytype != 'Emails')";
		}
		else if($module == "Emails") {
			$query = "FROM vtiger_activity
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid AND vtiger_activity.activitytype = 'Emails'";
		
			$query .= " LEFT JOIN vtiger_email_track ON vtiger_email_track.mailid = vtiger_activity.activityid";
			$query .= " LEFT JOIN vtiger_emaildetails ON vtiger_emaildetails.emailid=vtiger_activity.activityid";
				
			// TODO optimize inclusion of these tables
			$query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
			$query .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";
		
			$query .= " LEFT JOIN vtiger_users AS vtiger_lastModifiedBy".$module." ON vtiger_lastModifiedBy".$module.".id = vtiger_crmentity.modifiedby";
		
			$query .= " ".$this->getRelatedModulesQuery($module,$this->secondarymodule).
			getNonAdminAccessControlQuery($this->primarymodule,$current_user).
			" WHERE vtiger_crmentity.deleted = 0";
		}
		
		else if($module == "Quotes")
		{
			$query = "from vtiger_quotes
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_quotes.quoteid
				inner join vtiger_quotesbillads on vtiger_quotes.quoteid=vtiger_quotesbillads.quotebilladdressid
				inner join vtiger_quotesshipads on vtiger_quotes.quoteid=vtiger_quotesshipads.quoteshipaddressid
				left join vtiger_currency_info as vtiger_currency_info$module on vtiger_currency_info$module.id = vtiger_quotes.currency_id";
			if(($type !== 'COLUMNSTOTOTAL') || ($type == 'COLUMNSTOTOTAL' && $where_condition == 'add')) {
				$query .= " left join vtiger_inventoryproductrel as vtiger_inventoryproductrelQuotes on vtiger_quotes.quoteid = vtiger_inventoryproductrelQuotes.id
				left join vtiger_products as vtiger_productsQuotes on vtiger_productsQuotes.productid = vtiger_inventoryproductrelQuotes.productid
				left join vtiger_service as vtiger_serviceQuotes on vtiger_serviceQuotes.serviceid = vtiger_inventoryproductrelQuotes.productid";
			}
			$query .= " left join vtiger_quotescf on vtiger_quotes.quoteid = vtiger_quotescf.quoteid
				left join vtiger_groups as vtiger_groupsQuotes on vtiger_groupsQuotes.groupid = vtiger_crmentity.smownerid
				left join vtiger_users as vtiger_usersQuotes on vtiger_usersQuotes.id = vtiger_crmentity.smownerid
				left join vtiger_groups on vtiger_groups.groupid = vtiger_crmentity.smownerid
				left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
				left join vtiger_users as vtiger_lastModifiedByQuotes on vtiger_lastModifiedByQuotes.id = vtiger_crmentity.modifiedby
				left join vtiger_users as vtiger_usersRel1 on vtiger_usersRel1.id = vtiger_quotes.inventorymanager
				left join vtiger_potential as vtiger_potentialRelQuotes on vtiger_potentialRelQuotes.potentialid = vtiger_quotes.potentialid
				left join vtiger_contactdetails as vtiger_contactdetailsQuotes on vtiger_contactdetailsQuotes.contactid = vtiger_quotes.contactid
				left join vtiger_account as vtiger_accountQuotes on vtiger_accountQuotes.accountid = vtiger_quotes.accountid
				".$this->getRelatedModulesQuery($module,$this->secondarymodule,$type,$where_condition).
						getNonAdminAccessControlQuery($this->primarymodule,$current_user)."
				where vtiger_crmentity.deleted=0";
		}

		else if($module == "PurchaseOrder")
		{
			$query = "from vtiger_purchaseorder
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_purchaseorder.purchaseorderid
				inner join vtiger_pobillads on vtiger_purchaseorder.purchaseorderid=vtiger_pobillads.pobilladdressid
				inner join vtiger_poshipads on vtiger_purchaseorder.purchaseorderid=vtiger_poshipads.poshipaddressid
				left join vtiger_currency_info as vtiger_currency_info$module on vtiger_currency_info$module.id = vtiger_purchaseorder.currency_id";
			if(($type !== 'COLUMNSTOTOTAL') || ($type == 'COLUMNSTOTOTAL' && $where_condition == 'add')) {
				$query .= " left join vtiger_inventoryproductrel as vtiger_inventoryproductrelPurchaseOrder on vtiger_purchaseorder.purchaseorderid = vtiger_inventoryproductrelPurchaseOrder.id
				left join vtiger_products as vtiger_productsPurchaseOrder on vtiger_productsPurchaseOrder.productid = vtiger_inventoryproductrelPurchaseOrder.productid
				left join vtiger_service as vtiger_servicePurchaseOrder on vtiger_servicePurchaseOrder.serviceid = vtiger_inventoryproductrelPurchaseOrder.productid";
			}
			$query .= " left join vtiger_purchaseordercf on vtiger_purchaseorder.purchaseorderid = vtiger_purchaseordercf.purchaseorderid
				left join vtiger_groups as vtiger_groupsPurchaseOrder on vtiger_groupsPurchaseOrder.groupid = vtiger_crmentity.smownerid
				left join vtiger_users as vtiger_usersPurchaseOrder on vtiger_usersPurchaseOrder.id = vtiger_crmentity.smownerid
				left join vtiger_groups on vtiger_groups.groupid = vtiger_crmentity.smownerid
				left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
				left join vtiger_users as vtiger_lastModifiedByPurchaseOrder on vtiger_lastModifiedByPurchaseOrder.id = vtiger_crmentity.modifiedby
				left join vtiger_vendor as vtiger_vendorRelPurchaseOrder on vtiger_vendorRelPurchaseOrder.vendorid = vtiger_purchaseorder.vendorid
				left join vtiger_contactdetails as vtiger_contactdetailsPurchaseOrder on vtiger_contactdetailsPurchaseOrder.contactid = vtiger_purchaseorder.contactid
				".$this->getRelatedModulesQuery($module,$this->secondarymodule,$type,$where_condition).
						getNonAdminAccessControlQuery($this->primarymodule,$current_user)."
				where vtiger_crmentity.deleted=0";
		}

		else if($module == "Invoice")
		{
			$query = "from vtiger_invoice
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_invoice.invoiceid
				inner join vtiger_invoicebillads on vtiger_invoice.invoiceid=vtiger_invoicebillads.invoicebilladdressid
				inner join vtiger_invoiceshipads on vtiger_invoice.invoiceid=vtiger_invoiceshipads.invoiceshipaddressid
				left join vtiger_currency_info as vtiger_currency_info$module on vtiger_currency_info$module.id = vtiger_invoice.currency_id";
			if(($type !== 'COLUMNSTOTOTAL') || ($type == 'COLUMNSTOTOTAL' && $where_condition == 'add')) {
				$query .=" left join vtiger_inventoryproductrel as vtiger_inventoryproductrelInvoice on vtiger_invoice.invoiceid = vtiger_inventoryproductrelInvoice.id
					left join vtiger_products as vtiger_productsInvoice on vtiger_productsInvoice.productid = vtiger_inventoryproductrelInvoice.productid
					left join vtiger_service as vtiger_serviceInvoice on vtiger_serviceInvoice.serviceid = vtiger_inventoryproductrelInvoice.productid";
			}
			$query .= " left join vtiger_salesorder as vtiger_salesorderInvoice on vtiger_salesorderInvoice.salesorderid=vtiger_invoice.salesorderid
				left join vtiger_invoicecf on vtiger_invoice.invoiceid = vtiger_invoicecf.invoiceid
				left join vtiger_groups as vtiger_groupsInvoice on vtiger_groupsInvoice.groupid = vtiger_crmentity.smownerid
				left join vtiger_users as vtiger_usersInvoice on vtiger_usersInvoice.id = vtiger_crmentity.smownerid
				left join vtiger_groups on vtiger_groups.groupid = vtiger_crmentity.smownerid
				left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
				left join vtiger_users as vtiger_lastModifiedByInvoice on vtiger_lastModifiedByInvoice.id = vtiger_crmentity.modifiedby
				left join vtiger_account as vtiger_accountInvoice on vtiger_accountInvoice.accountid = vtiger_invoice.accountid
				left join vtiger_contactdetails as vtiger_contactdetailsInvoice on vtiger_contactdetailsInvoice.contactid = vtiger_invoice.contactid
				".$this->getRelatedModulesQuery($module,$this->secondarymodule,$type,$where_condition).
						getNonAdminAccessControlQuery($this->primarymodule,$current_user)."
				where vtiger_crmentity.deleted=0";
		}
		else if($module == "SalesOrder")
		{
			$query = "from vtiger_salesorder
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_salesorder.salesorderid
				inner join vtiger_sobillads on vtiger_salesorder.salesorderid=vtiger_sobillads.sobilladdressid
				inner join vtiger_soshipads on vtiger_salesorder.salesorderid=vtiger_soshipads.soshipaddressid
				left join vtiger_currency_info as vtiger_currency_info$module on vtiger_currency_info$module.id = vtiger_salesorder.currency_id";
			if(($type !== 'COLUMNSTOTOTAL') || ($type == 'COLUMNSTOTOTAL' && $where_condition == 'add')) {
				$query .= " left join vtiger_inventoryproductrel as vtiger_inventoryproductrelSalesOrder on vtiger_salesorder.salesorderid = vtiger_inventoryproductrelSalesOrder.id
				left join vtiger_products as vtiger_productsSalesOrder on vtiger_productsSalesOrder.productid = vtiger_inventoryproductrelSalesOrder.productid
				left join vtiger_service as vtiger_serviceSalesOrder on vtiger_serviceSalesOrder.serviceid = vtiger_inventoryproductrelSalesOrder.productid";
			}
			$query .=" left join vtiger_salesordercf on vtiger_salesorder.salesorderid = vtiger_salesordercf.salesorderid
				left join vtiger_contactdetails as vtiger_contactdetailsSalesOrder on vtiger_contactdetailsSalesOrder.contactid = vtiger_salesorder.contactid
				left join vtiger_quotes as vtiger_quotesSalesOrder on vtiger_quotesSalesOrder.quoteid = vtiger_salesorder.quoteid
				left join vtiger_account as vtiger_accountSalesOrder on vtiger_accountSalesOrder.accountid = vtiger_salesorder.accountid
				left join vtiger_potential as vtiger_potentialRelSalesOrder on vtiger_potentialRelSalesOrder.potentialid = vtiger_salesorder.potentialid
				left join vtiger_invoice_recurring_info on vtiger_invoice_recurring_info.salesorderid = vtiger_salesorder.salesorderid
				left join vtiger_groups as vtiger_groupsSalesOrder on vtiger_groupsSalesOrder.groupid = vtiger_crmentity.smownerid
				left join vtiger_users as vtiger_usersSalesOrder on vtiger_usersSalesOrder.id = vtiger_crmentity.smownerid
				left join vtiger_groups on vtiger_groups.groupid = vtiger_crmentity.smownerid
				left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
				left join vtiger_users as vtiger_lastModifiedBySalesOrder on vtiger_lastModifiedBySalesOrder.id = vtiger_crmentity.modifiedby
				".$this->getRelatedModulesQuery($module,$this->secondarymodule,$type,$where_condition).
						getNonAdminAccessControlQuery($this->primarymodule,$current_user)."
				where vtiger_crmentity.deleted=0";


		}
		else if($module == "Campaigns")
		{
			$query = "from vtiger_campaign
				inner join vtiger_campaignscf as vtiger_campaignscf on vtiger_campaignscf.campaignid=vtiger_campaign.campaignid
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_campaign.campaignid
				left join vtiger_products as vtiger_productsCampaigns on vtiger_productsCampaigns.productid = vtiger_campaign.product_id
				left join vtiger_groups as vtiger_groupsCampaigns on vtiger_groupsCampaigns.groupid = vtiger_crmentity.smownerid
				left join vtiger_users as vtiger_usersCampaigns on vtiger_usersCampaigns.id = vtiger_crmentity.smownerid
				left join vtiger_groups on vtiger_groups.groupid = vtiger_crmentity.smownerid
				left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
				left join vtiger_users as vtiger_lastModifiedBy".$module." on vtiger_lastModifiedBy".$module.".id = vtiger_crmentity.modifiedby
				".$this->getRelatedModulesQuery($module,$this->secondarymodule,$type,$where_condition).
					getNonAdminAccessControlQuery($this->primarymodule,$current_user)."
				where vtiger_crmentity.deleted=0";
		}
		else if($module == "Issuecards")
		{
			$focus = CRMEntity::getInstance($module);
			$query = $focus->generateReportsQuery($module);
			$query .= " left join vtiger_currency_info as vtiger_currency_info$module on vtiger_currency_info$module.id = vtiger_issuecards.currency_id";
			if(($type !== 'COLUMNSTOTOTAL') || ($type == 'COLUMNSTOTOTAL' && $where_condition == 'add')) {
				$query .=" left join vtiger_inventoryproductrel as vtiger_inventoryproductrelIssuecards on vtiger_issuecards.issuecardid = vtiger_inventoryproductrelIssuecards.id
					left join vtiger_products as vtiger_productsIssuecards on vtiger_productsIssuecards.productid = vtiger_inventoryproductrelIssuecards.productid
					left join vtiger_service as vtiger_serviceIssuecards on vtiger_serviceIssuecards.serviceid = vtiger_inventoryproductrelIssuecards.productid";
			}
			$query .= $this->getRelatedModulesQuery($module,$this->secondarymodule,$type,$where_condition).
						getNonAdminAccessControlQuery($this->primarymodule,$current_user)."
				where vtiger_crmentity.deleted=0";
		}
		else {
			if($module!=''){
				$focus = CRMEntity::getInstance($module);
				$query = $focus->generateReportsQuery($module)
							.$this->getRelatedModulesQuery($module,$this->secondarymodule,$type,$where_condition)
							.getNonAdminAccessControlQuery($this->primarymodule,$current_user).
						" WHERE vtiger_crmentity.deleted=0";
			}
		}
		$log->info("ReportRun :: Successfully returned getReportsQuery".$module);

		return $query;
	}

	/** function to get query for the given reportid,filterlist,type
	 * @param $reportid : integer
	 * @param $filtersql : Array
	 * @param $type : String output format of the report
	 * @param $chartReport : boolean
	 *  this returns join query for the report
	 */
	function sGetSQLforReport($reportid,$filtersql,$type='',$chartReport=false)
	{
		global $log;

		$columnlist = $this->getQueryColumnsList($reportid,$type);
		$groupslist = $this->getGroupingList($reportid);
		$groupTimeList = $this->getGroupByTimeList($reportid);
		$stdfilterlist = $this->getStdFilterList($reportid);
		$columnstotallist = $this->getColumnsTotal($reportid,$columnlist);
		$advfiltersql = $this->getAdvFilterSql($reportid);

		$this->totallist = $columnstotallist;
		global $current_user;
		$tab_id = getTabid($this->primarymodule);
		//Fix for ticket #4915.
		$selectlist = $columnlist;
		//columns list
		if(isset($selectlist))
		{
			$selectedcolumns = implode(", ",$selectlist);
		}
		//groups list
		if(isset($groupslist))
		{
			$groupsquery = implode(", ",$groupslist);
		}
		if(isset($groupTimeList)){
			$groupTimeQuery = implode(', ',$groupTimeList);
		}

		//standard list
		if(isset($stdfilterlist))
		{
			$stdfiltersql = implode(", ",$stdfilterlist);
		}
		//columns to total list
		if(isset($columnstotallist))
		{
			$columnstotalsql = implode(', ',$columnstotallist);
		} else {
			$columnstotalsql = '';
		}
		if($stdfiltersql != '')
		{
			$wheresql = ' and '.$stdfiltersql;
		} else {
			$wheresql = '';
		}

		if(isset($filtersql) && $filtersql !== false) {
			$advfiltersql = $filtersql;
		}
		$where_condition = '';
		if($advfiltersql != "") {
			if($type == 'COLUMNSTOTOTAL')
			{
				if (strstr($advfiltersql,'vtiger_products'.$this->primarymodule) || strstr($advfiltersql,'vtiger_service'.$this->primarymodule))
					$where_condition='add';
			}
			$wheresql .= " and ".$advfiltersql;
		}

		$reportquery = $basereportquery = $this->getReportsQuery($this->primarymodule, $type,$where_condition);

		// If we don't have access to any columns, let us select one column and limit result to show we have no results
		// Fix for: http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/4758 - Prasad
		$allColumnsRestricted = false;

		if($type == 'COLUMNSTOTOTAL')
		{
			if($columnstotalsql != '')
			{
				$totalsselectedcolumns = $columnlist;
				// eliminate product/service columns for inventory modules
				foreach ($columnlist as $key => $value) {
					$finfo = explode(':',$key);
					if ($finfo[0]=='vtiger_inventoryproductrel'.$this->primarymodule) {
						unset($totalsselectedcolumns[$key]);
					}
				}
				if (isset($this->_columnstotallistaddtoselect) and is_array($this->_columnstotallistaddtoselect) and count($this->_columnstotallistaddtoselect)>0) {
					$_columnstotallistaddtoselect = ', '.implode(', ', $this->_columnstotallistaddtoselect);
					$totalscolalias = array();
					foreach ($this->_columnstotallistaddtoselect as $key => $value) {
						list($void,$calias) = explode(' AS ',$value);
						$calias = str_replace("'", '', $calias);
						$totalscolalias[] = $calias;
					}
					foreach ($columnlist as $key => $value) {
						foreach ($totalscolalias as $cal) {
							if (preg_match("/\b".trim($cal)."\b/i", $value)) {
								unset($totalsselectedcolumns[$key]);
								break;
							}
						}
					}
				} else {
					$_columnstotallistaddtoselect = '';
				}
				$totalsselectedcolumns = implode(', ',$totalsselectedcolumns);
				$reportquery = "select ".$columnstotalsql.' from (select DISTINCT '.$totalsselectedcolumns.$_columnstotallistaddtoselect." ".$reportquery." ".$wheresql.') as summary_calcs';
			}
		}else
		{
			if($selectedcolumns == '') { // Fix for: http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/4758 - Prasad
				$selectedcolumns = "''"; // "''" to get blank column name
				$allColumnsRestricted = true;
			}
			$reportquery = "select DISTINCT ".$selectedcolumns." ".$reportquery." ".$wheresql;
		}
		$reportquery = listQueryNonAdminChange($reportquery, $this->primarymodule);

		if(trim($groupsquery) != "" && $type !== 'COLUMNSTOTOTAL')
		{
			if($chartReport == true){
				reset($groupslist);
				$first_key = key($groupslist);
				$reportquery = 'select '.$columnlist[$first_key].", count(*) AS 'groupby_count' $basereportquery $wheresql group by ".$this->GetFirstSortByField($reportid);
			}else{
				$reportquery .= " order by ".$groupsquery;
			}
		}

		// Prasad: No columns selected so limit the number of rows directly.
		if($allColumnsRestricted) {
			$reportquery .= " limit 0";
		}

		preg_match('/&amp;/', $reportquery, $matches);
		if(!empty($matches)){
			$report=str_replace('&amp;', '&', $reportquery);
			$reportquery = $this->replaceSpecialChar($report);
		}
		if ($type == 'HTMLPAGED' and !$allColumnsRestricted) {
			$rowsperpage = GlobalVariable::getVariable('Report_ListView_PageSize',40);
			$reportquery .= ' limit '.(($this->page-1)*$rowsperpage).', '.$rowsperpage;
		}
		$log->info("ReportRun :: Successfully returned sGetSQLforReport".$reportid);
		if (GlobalVariable::getVariable('Debug_Report_Query', '0')=='1') {
			echo '<br>'.$reportquery.'<br>';
		}
		return $reportquery;
	}

	/** function to get the report output in HTML,PDF,TOTAL,PRINT,PRINTTOTAL formats depends on the argument $outputformat
	 *  @ param $outputformat : Type String (valid parameters HTML,PDF,TOTAL,PRINT,PRINT_TOTAL)
	 *  @ param $filtersql : Type String
	 *  This returns HTML Report if $outputformat is HTML
	 *  		Array for PDF if  $outputformat is PDF
	 *		HTML strings for TOTAL if $outputformat is TOTAL
	 *		Array for PRINT if $outputformat is PRINT
	 *		HTML strings for TOTAL fields  if $outputformat is PRINTTOTAL
	 *		HTML strings for
	 */

	// Performance Optimization: Added parameter directOutput to avoid building big-string!
	function GenerateReport($outputformat,$filtersql, $directOutput=false, &$returnfieldinfo=array())
	{
		global $adb,$current_user,$php_max_execution_time;
		global $modules,$app_strings, $mod_strings,$current_language;
		require('user_privileges/user_privileges_'.$current_user->id.'.php');
		$picklistarray = array();
		$modules_selected = array();
		$modules_selected[] = $this->primarymodule;
		if(!empty($this->secondarymodule)){
			$sec_modules = explode(":",$this->secondarymodule);
			for($i=0;$i<count($sec_modules);$i++){
				$modules_selected[] = $sec_modules[$i];
			}
		}

		// Update Reference fields list list
		$referencefieldres = $adb->pquery("SELECT tabid, fieldlabel, uitype from vtiger_field WHERE uitype in (10,101)", array());
		if($referencefieldres) {
			foreach($referencefieldres as $referencefieldrow) {
				$uiType = $referencefieldrow['uitype'];
				$modprefixedlabel = getTabModuleName($referencefieldrow['tabid']).' '.$referencefieldrow['fieldlabel'];
				$modprefixedlabel = str_replace(' ','_',$modprefixedlabel);

				if($uiType == 10 && !in_array($modprefixedlabel, $this->ui10_fields)) {
					$this->ui10_fields[] = $modprefixedlabel;
				} elseif($uiType == 101 && !in_array($modprefixedlabel, $this->ui101_fields)) {
					$this->ui101_fields[] = $modprefixedlabel;
				}
			}
		}

		if($outputformat == 'HTML' || $outputformat == 'HTMLPAGED')
		{
			if ($outputformat=='HTMLPAGED') $directOutput = false;
			$sSQL = $this->sGetSQLforReport($this->reportid,$filtersql,$outputformat);
			$result = $adb->query($sSQL);
			$error_msg = $adb->database->ErrorMsg();
			if(!$result && $error_msg!=''){
				// Performance Optimization: If direct output is requried
				if($directOutput) {
					echo getTranslatedString('LBL_REPORT_GENERATION_FAILED', 'Reports') . "<br>" . $error_msg;
					$error_msg = false;
				}
				return $error_msg;
			}

			// Performance Optimization: If direct output is required
			if($directOutput) {
				echo '<table cellpadding="5" cellspacing="0" align="center" class="rptTable"><tr>';
			}

			if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1) {
				$picklistarray = $this->getAccessPickListValues();
			}
			if($result)
			{
				$y=$adb->num_fields($result);
				$noofrows = $adb->num_rows($result);
				$this->number_of_rows = $noofrows;
				if($outputformat == 'HTMLPAGED') {
					$rowsperpage = GlobalVariable::getVariable('Report_ListView_PageSize',40);
					if ($this->page*$rowsperpage>$noofrows-($noofrows % $rowsperpage)) {
						$this->islastpage = true;
					}
				}
				$reportmaxrows = GlobalVariable::getVariable('Report_MaxRows_OnScreen',5000);
				$custom_field_values = $adb->fetch_array($result);
				$groupslist = $this->getGroupingList($this->reportid);
				$header = '';
				for ($x=0; $x<$y; $x++)
				{
					$fld = $adb->field_name($result, $x);
					if ($fld->name=='LBL_ACTION') {
						$module = 'Reports';
						$fieldLabel = 'LBL_ACTION';
					} else {
						list($module, $fieldLabel) = explode('_', $fld->name, 2);
					}
					$fieldInfo = getFieldByReportLabel($module, $fieldLabel);
					if(!empty($fieldInfo)) {
						$field = WebserviceField::fromArray($adb, $fieldInfo);
					}
					if(!empty($fieldInfo)) {
						$headerLabel = getTranslatedString($field->getFieldLabelKey(), $module);
					} else {
						$headerLabel = getTranslatedString(str_replace('_', " ", $fieldLabel), $module);
					}
					/*STRING TRANSLATION starts */
					$moduleLabel = '';
					if(in_array($module,$modules_selected))
						$moduleLabel = getTranslatedString($module,$module);

					if(empty($headerLabel)) {
							$headerLabel = getTranslatedString(str_replace('_', " ", $fld->name));
					}
					if(!empty($this->secondarymodule)) {
						if($moduleLabel != '') {
							$headerLabel = $moduleLabel." ". $headerLabel;
						}
					}
					$header .= "<td class='rptCellLabel'>".$headerLabel."</td>";

					// Performance Optimization: If direct output is required
					if($directOutput) {
						echo $header;
						$header = '';
					}
					// END
				}

				// Performance Optimization: If direct output is required
				if($directOutput) {
					echo '</tr><tr>';
				}

				$valtemplate = '';
				$lastvalue = '';
				$secondvalue = '';
				$thirdvalue = '';
				$sHTML = '';
				if ($noofrows<=$reportmaxrows) {
				do {
					$newvalue = '';
					$snewvalue = '';
					$tnewvalue = '';
					if(count($groupslist) == 1)
					{
						$newvalue = $custom_field_values[0];
					}elseif(count($groupslist) == 2)
					{
						$newvalue = $custom_field_values[0];
						$snewvalue = $custom_field_values[1];
					}elseif(count($groupslist) == 3)
					{
						$newvalue = $custom_field_values[0];
						$snewvalue = $custom_field_values[1];
						$tnewvalue = $custom_field_values[2];
					}
					if($newvalue == '') $newvalue = '-';

					if($snewvalue == '') $snewvalue = '-';

					if($tnewvalue == '') $tnewvalue = '-';

					$valtemplate .= '<tr>';

					// Performance Optimization
					if($directOutput) {
						echo $valtemplate;
						$valtemplate = '';
					}

					for ($i=0; $i<$y; $i++) {
						$fld = $adb->field_name($result, $i);
						$fieldvalue = getReportFieldValue($this, $picklistarray, $fld, $custom_field_values, $i);

						if($fieldvalue == '')
						{
							$fieldvalue = "-";
						}
						else if($fld->name == 'LBL_ACTION' && $fieldvalue != '-')
						{
							$fieldvalue = "<a href='index.php?module={$this->primarymodule}&action=DetailView&record={$fieldvalue}' target='_blank'>".getTranslatedString('LBL_VIEW_DETAILS','Reports')."</a>";
						}

						if(($lastvalue == $fieldvalue) && $this->reporttype == "summary")
						{
							if($this->reporttype == "summary")
							{
								$valtemplate .= "<td class='rptEmptyGrp'>&nbsp;</td>";
							}else
							{
								$valtemplate .= "<td class='rptData'>".$fieldvalue."</td>";
							}
						}else if(($secondvalue === $fieldvalue) && $this->reporttype == "summary")
						{
							if($lastvalue === $newvalue)
							{
								$valtemplate .= "<td class='rptEmptyGrp'>&nbsp;</td>";
							}else
							{
								$valtemplate .= "<td class='rptGrpHead'>".$fieldvalue."</td>";
							}
						}
						else if(($thirdvalue === $fieldvalue) && $this->reporttype == "summary")
						{
							if($secondvalue === $snewvalue)
							{
								$valtemplate .= "<td class='rptEmptyGrp'>&nbsp;</td>";
							}else
							{
								$valtemplate .= "<td class='rptGrpHead'>".$fieldvalue."</td>";
							}
						}
						else
						{
							if($this->reporttype == "tabular")
							{
								$valtemplate .= "<td class='rptData'>".$fieldvalue."</td>";
							}else
							{
								$valtemplate .= "<td class='rptGrpHead'>".$fieldvalue."</td>";
							}
						}

						// Performance Optimization: If direct output is required
						if($directOutput) {
							echo $valtemplate;
							$valtemplate = '';
						}
					}

					$valtemplate .= "</tr>";

					// Performance Optimization: If direct output is required
					if($directOutput) {
						echo $valtemplate;
						$valtemplate = '';
					}

					$lastvalue = $newvalue;
					$secondvalue = $snewvalue;
					$thirdvalue = $tnewvalue;
					set_time_limit($php_max_execution_time);
				}while($custom_field_values = $adb->fetch_array($result));
				} else {
					$reporterrmsg = new vtigerCRM_Smarty();
					$errormessage = getTranslatedString('ERR_TOO_MANY_ROWS','Reports');
					$reporterrmsg->assign('ERROR_MESSAGE', $errormessage);
					$errormessage = $reporterrmsg->fetch('applicationmessage.tpl');
					$errormessage = '<tr><td colspan="'.$y.'">'.$errormessage.'</td>';
					if($directOutput) {
						echo $errormessage;
					} else {
						$valtemplate = $errormessage;
					}
				}
				// Performance Optimization
				if($directOutput) {
					echo "</tr></table>";
					echo "<script type='text/javascript' id='__reportrun_directoutput_recordcount_script'>
						if(document.getElementById('_reportrun_total')) document.getElementById('_reportrun_total').innerHTML=$noofrows;</script>";
				} else {
					if ($this->page==1) {
						$sHTML = '<table cellpadding="5" cellspacing="0" align="center" class="rptTable">
						<tr>'.
						$header
						.'<!-- BEGIN values -->';
					}
					$sHTML .= '<tr>'.$valtemplate.'</tr>';
					if ($this->islastpage) {
						$sHTML .= '</table>';
					}
				}
				//<<<<<<<<construct HTML>>>>>>>>>>>>
				$return_data[] = $sHTML;
				$return_data[] = $noofrows;
				$return_data[] = $sSQL;
				return $return_data;
			}
		}elseif($outputformat == 'HEADERS') {
			$sSQL = $this->sGetSQLforReport($this->reportid,$filtersql,'HTML');
			$result = $adb->query($sSQL.' limit 1');
			$error_msg = $adb->database->ErrorMsg();
			if(!$result && $error_msg!=''){
				$resp = array(
					'has_contents' => false,
					'jsonheaders' => array(),
					'i18nheaders' => array(),
					'error' => true,
					'error_message' => getTranslatedString('LBL_REPORT_GENERATION_FAILED', 'Reports') . ':' . $error_msg,
				);
				return $resp;
			}
			$fldcnt=$adb->num_fields($result);
			$i18nheader = $jsonheader = array();
			for ($x=0; $x<$fldcnt; $x++) {
				$fld = $adb->field_name($result, $x);
				if ($fld->name=='LBL_ACTION') {
					$module = 'Reports';
					$fieldLabel = 'LBL_ACTION';
				} else {
					list($module, $fieldLabel) = explode('_', $fld->name, 2);
				}
				$fieldInfo = getFieldByReportLabel($module, $fieldLabel);
				if(!empty($fieldInfo)) {
					$field = WebserviceField::fromArray($adb, $fieldInfo);
				}
				if(!empty($fieldInfo)) {
					$headerLabel = $field->getFieldLabelKey();
				} else {
					$headerLabel = str_replace('_', ' ', $fieldLabel);
				}
				if(empty($headerLabel)) {
					$headerLabel = str_replace('_', ' ', $fld->name);
				}
				$i18nheaderLabel = getTranslatedString($headerLabel, $module);
				$moduleLabel = '';
				if(!empty($this->secondarymodule) and in_array($module,$modules_selected)) {
					$headerLabel = $module.' '.$headerLabel;
					$i18nheaderLabel = getTranslatedString($module,$module).' '.$i18nheaderLabel;
				}
				if ($fld->name=='LBL_ACTION') {
					$jsonheader[] = 'reportrowaction';
				} else {
					$jsonheader[] = $headerLabel;
				}
				$i18nheader[] = $i18nheaderLabel;
			}
			$resp = array(
				'has_contents' => ($adb->num_rows($result)==1 ? true : false),
				'jsonheaders' => $jsonheader,
				'i18nheaders' => $i18nheader,
				'error' => false,
			);
			return $resp;
		}elseif($outputformat == 'JSON' or $outputformat == 'JSONPAGED') {
			$sSQL = $this->sGetSQLforReport($this->reportid,$filtersql,($outputformat == 'JSON' ? 'HTML' : 'HTMLPAGED'));
			$result = $adb->query($sSQL);
			$error_msg = $adb->database->ErrorMsg();
			if(!$result && $error_msg!=''){
				$resp = array(
					'total' => 0,
					'data' => array(),
					'sql' => $sSQL,
					'error' => true,
					'error_message' => getTranslatedString('LBL_REPORT_GENERATION_FAILED', 'Reports') . ':' . $error_msg,
				);
				return json_encode($resp);
			}

			if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1) {
				$picklistarray = $this->getAccessPickListValues();
			}
			if($result)
			{
				$fldcnt=$adb->num_fields($result);
				if ($outputformat == 'JSONPAGED') {
					$fullsSQL = $this->sGetSQLforReport($this->reportid,$filtersql, 'HTML');
					$fullresult = $adb->query($fullsSQL);
					$noofrows = $adb->num_rows($fullresult);
					unset($fullsSQL);
					unset($fullresult);
				} else {
					$noofrows = $adb->num_rows($result);
				}
				$this->number_of_rows = $noofrows;
				$resp = array(
					'total' => $noofrows,
					'current_page' => $this->page,
					'error' => false,
				);
				if($outputformat == 'JSONPAGED') {
					$rowsperpage = GlobalVariable::getVariable('Report_ListView_PageSize',40);
					$resp['per_page'] = $rowsperpage;
					$resp['from'] = ($this->page-1)*$rowsperpage+1;
					if ($this->page*$rowsperpage>$noofrows-($noofrows % $rowsperpage)) {
						$this->islastpage = true;
						$resp['to'] = $noofrows;
					} else {
						$this->islastpage = false;
						$resp['to'] = $this->page*$rowsperpage;
					}
					$resp['last_page'] = ceil($noofrows/$rowsperpage);
				} else {
					$resp['per_page'] = $noofrows;
					$resp['from'] = 1;
					$resp['to'] = $noofrows;
					$resp['current_page'] = 1;
					$resp['last_page'] = 1;
				}
				if ($this->islastpage and $this->page!=1) {
					$resp['next_page_url'] = null;
				} else {
					$resp['next_page_url'] = 'index.php?module=Reports&action=ReportsAjax&file=getJSON&record='.$this->reportid.'&page='.($this->islastpage ? $this->page : $this->page+1);
				}
				$resp['prev_page_url'] = 'index.php?module=Reports&action=ReportsAjax&file=getJSON&record='.$this->reportid.'&page='.($this->page == 1 ? 1 : $this->page-1);
				$custom_field_values = $adb->fetch_array($result);
				$groupslist = $this->getGroupingList($this->reportid);
				$header = array();
				for ($x=0; $x<$fldcnt; $x++)
				{
					$fld = $adb->field_name($result, $x);
					if ($fld->name=='LBL_ACTION') {
						$module = 'Reports';
						$fieldLabel = 'LBL_ACTION';
					} else {
						list($module, $fieldLabel) = explode('_', $fld->name, 2);
					}
					$fieldInfo = getFieldByReportLabel($module, $fieldLabel);
					if(!empty($fieldInfo)) {
						$field = WebserviceField::fromArray($adb, $fieldInfo);
					}
					if(!empty($fieldInfo)) {
						$headerLabel = $field->getFieldLabelKey();
					} else {
						$headerLabel = str_replace('_', ' ', $fieldLabel);
					}
					if(empty($headerLabel)) {
						$headerLabel = str_replace('_', ' ', $fld->name);
					}
					if(!empty($this->secondarymodule) and in_array($module,$modules_selected)) {
						$headerLabel = $module.' '.$headerLabel;
					}
					$header[] = $headerLabel;
				}

				$data = array();
				$rowcnt = 0;
				do {
					$crmid = $adb->query_result($result, $rowcnt++, $fldcnt-1);
					$datarow = array();
					$datarow['crmid'] = $crmid;
					for ($i=0; $i<$fldcnt; $i++) {
						$fld = $adb->field_name($result, $i);
						$fieldvalue = getReportFieldValue($this, $picklistarray, $fld, $custom_field_values, $i);
						if($fieldvalue == '')
						{
							$fieldvalue = "-";
						}
						else if($fld->name == 'LBL_ACTION' && $fieldvalue != '-')
						{
							$fieldvalue = "index.php?module={$this->primarymodule}&action=DetailView&record={$fieldvalue}";
						}
						if ($header[$i]=='LBL ACTION') {
							$datarow['reportrowaction'] = $fieldvalue;
						} else {
							$datarow[$header[$i]] = $fieldvalue;
						}
					}
					$data[] = $datarow;
					set_time_limit($php_max_execution_time);
				}while($custom_field_values = $adb->fetch_array($result));
				$resp['data'] = $data;
				return json_encode($resp);
			}
		}elseif($outputformat == "PDF")
		{
			$sSQL = $this->sGetSQLforReport($this->reportid,$filtersql);
			$result = $adb->pquery($sSQL,array());
			if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1)
			$picklistarray = $this->getAccessPickListValues();

			if($result)
			{
				$y=$adb->num_fields($result);
				$noofrows = $adb->num_rows($result);
				$this->number_of_rows = $noofrows;
				$custom_field_values = $adb->fetch_array($result);
				$ILF = new InventoryLineField();
				$invMods = getInventoryModules();
				$arr_val = array();
				do {
					$arraylists = Array();
					for ($i=0; $i<$y-1; $i++) //No tratamos la última columna por ser el ACTION con el CRMID.
					{
						$fld = $adb->field_name($result, $i);
						list($module, $fieldLabel) = explode('_', $fld->name, 2);
						$fieldInfo = getFieldByReportLabel($module, $fieldLabel);
						if(!empty($fieldInfo)) {
							$field = WebserviceField::fromArray($adb, $fieldInfo);
						} else {
							if(in_array($module, $invMods)) {
							  if(substr($fld->table,0,26) == 'vtiger_inventoryproductrel') {
								foreach ($ILF->getInventoryLineFieldsByName() as $ilfname => $ilfinfo) {
									$ilflabel = getTranslatedString($ilfinfo['fieldlabel'], $module);
									if ($ilflabel==$fieldLabel) {
										$fieldInfo = $ilfinfo;
										$fieldInfo['tabid'] = getTabid($module);
										$fieldInfo['presence'] = 1;
										$field = WebserviceField::fromArray($adb, $fieldInfo);
										break;
									}
								}
							  } else if(substr($fld->table,0,15) == 'vtiger_products' or substr($fld->table,0,14) == 'vtiger_service') {
								foreach ($ILF->getInventoryLineProductServiceNameFields() as $ilfname => $ilfinfo) {
									$ilflabel = getTranslatedString($ilfinfo['fieldlabel'], $module);
									if ($ilflabel==$fieldLabel) {
										$fieldInfo = $ilfinfo;
										$fieldInfo['tabid'] = getTabid($ilfinfo['module']);
										$fieldInfo['presence'] = 1;
										$field = WebserviceField::fromArray($adb, $fieldInfo);
										break;
									}
								}
							  }
							}
						}
						if(!empty($fieldInfo)) {
							$headerLabel = getTranslatedString($field->getFieldLabelKey(), $module);
						} else {
							$headerLabel = getTranslatedString(str_replace('_', " ", $fieldLabel), $module);
						}
						/*STRING TRANSLATION starts */
						$moduleLabel ='';
						if(in_array($module,$modules_selected))
							$moduleLabel = getTranslatedString($module,$module);

						if(empty($headerLabel)) {
								$headerLabel = getTranslatedString(str_replace('_', " ", $fld->name));
						}
						if(!empty($this->secondarymodule)) {
							if($moduleLabel != '') {
								$headerLabel = $moduleLabel." ". $headerLabel;
							}
						}
						$fieldvalue = getReportFieldValue($this, $picklistarray, $fld,$custom_field_values, $i);
						if (empty($returnfieldinfo[$headerLabel])) $returnfieldinfo[$headerLabel] = $field;
						$arraylists[$headerLabel] = $fieldvalue;
					}
					$arr_val[] = $arraylists;
					set_time_limit($php_max_execution_time);
				}while($custom_field_values = $adb->fetch_array($result));

				return $arr_val;
			}
		}elseif($outputformat == "TOTALXLS")
		{
				$escapedchars = Array('_SUM','_AVG','_MIN','_MAX');
				$totalpdf=array();
				$sSQL = $this->sGetSQLforReport($this->reportid,$filtersql,"COLUMNSTOTOTAL");
				if(isset($this->totallist) and count($this->totallist)>0) {
						if($sSQL != '')
						{
								$result = $adb->query($sSQL);
								$y=$adb->num_fields($result);
								$custom_field_values = $adb->fetch_array($result);

								foreach($this->totallist as $key=>$value)
								{
										$fieldlist = explode(":",$key);
										$mod_query = $adb->pquery("SELECT distinct(tabid) as tabid, uitype as uitype from vtiger_field where tablename = ? and columnname=?",array($fieldlist[1],$fieldlist[2]));
										if($adb->num_rows($mod_query)>0){
												$module_name = getTabModuleName($adb->query_result($mod_query,0,'tabid'));
												$fieldlabel = trim(str_replace($escapedchars," ",$fieldlist[3]));
												$fieldlabel = str_replace("_", " ", $fieldlabel);
												if($module_name){
														$field = getTranslatedString($module_name,$module_name)." ".getTranslatedString($fieldlabel,$module_name);
												} else {
													$field = getTranslatedString($fieldlabel);
												}
										}
										$uitype_arr[str_replace($escapedchars," ",$module_name."_".$fieldlist[3])] = $adb->query_result($mod_query,0,"uitype");
										$totclmnflds[str_replace($escapedchars," ",$module_name."_".$fieldlist[3])] = $field;
								}
								for($i =0;$i<$y;$i++)
								{
										$fld = $adb->field_name($result, $i);
										$keyhdr[$fld->name] = $custom_field_values[$i];
								}

								$rowcount=0;
								foreach($totclmnflds as $key=>$value)
								{
										$col_header = trim(str_replace($modules," ",$value));
										$fld_name_1 = $this->primarymodule . "_" . trim($value);
										$fld_name_2 = $this->secondarymodule . "_" . trim($value);
										if($uitype_arr[$key] == 71 || $uitype_arr[$key] == 72 ||
											in_array($fld_name_1,$this->append_currency_symbol_to_value) || in_array($fld_name_2,$this->append_currency_symbol_to_value)) {
												$col_header .= " (".$app_strings['LBL_IN']." ".$current_user->currency_symbol.")";
												$convert_price = true;
										} else{
												$convert_price = false;
										}
										$value = decode_html(trim($key));
										$arraykey = $value.'_SUM';
										if(isset($keyhdr[$arraykey]))
										{
											if($convert_price)
												$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey]);
											else
												$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey], null, true);
												$totalpdf[$rowcount][$arraykey] = $conv_value;
										}else
										{
												$totalpdf[$rowcount][$arraykey] = '';
										}

										$arraykey = $value.'_AVG';
										if(isset($keyhdr[$arraykey]))
										{
											if($convert_price)
												$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey]);
											else
												$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey], null, true);
											$totalpdf[$rowcount][$arraykey] = $conv_value;
										}else
										{
												$totalpdf[$rowcount][$arraykey] = '';
										}

										$arraykey = $value.'_MIN';
										if(isset($keyhdr[$arraykey]))
										{
											if($convert_price)
												$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey]);
											else
												$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey], null, true);
											$totalpdf[$rowcount][$arraykey] = $conv_value;
										}else
										{
												$totalpdf[$rowcount][$arraykey] = '';
										}

										$arraykey = $value.'_MAX';
										if(isset($keyhdr[$arraykey]))
										{
											if($convert_price)
												$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey]);
											else
												$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey], null, true);
											$totalpdf[$rowcount][$arraykey] = $conv_value;
										}else
										{
												$totalpdf[$rowcount][$arraykey] = '';
										}
										$rowcount++;
								}
						}
				}
				return $totalpdf;
		}elseif($outputformat == "TOTALHTML")
		{
			$escapedchars = Array('_SUM','_AVG','_MIN','_MAX');
			$sSQL = $this->sGetSQLforReport($this->reportid,$filtersql,"COLUMNSTOTOTAL");
			$coltotalhtml = '';
			if(isset($this->totallist) and count($this->totallist)>0) {
				if($sSQL != "")
				{
					$result = $adb->query($sSQL);
					$y=$adb->num_fields($result);
					$custom_field_values = $adb->fetch_array($result);
					$coltotalhtml = "<table align='center' width='60%' cellpadding='3' cellspacing='0' border='0' class='rptTable'><tr><td class='rptCellLabel'>".$mod_strings['Totals']."</td><td class='rptCellLabel'>".$mod_strings['SUM']."</td><td class='rptCellLabel'>".$mod_strings['AVG']."</td><td class='rptCellLabel'>".$mod_strings['MIN']."</td><td class='rptCellLabel'>".$mod_strings['MAX']."</td></tr>";

					// Performation Optimization: If Direct output is desired
					if($directOutput) {
						echo $coltotalhtml;
						$coltotalhtml = '';
					}

					foreach($this->totallist as $key=>$value)
					{
						$fieldlist = explode(":",$key);
						$mod_query = $adb->pquery("SELECT distinct(tabid) as tabid, uitype as uitype from vtiger_field where tablename = ? and columnname=?",array($fieldlist[1],$fieldlist[2]));
						if($adb->num_rows($mod_query)>0){
							$module_name = getTabModuleName($adb->query_result($mod_query,0,'tabid'));
							$fieldlabel = trim(str_replace($escapedchars," ",$fieldlist[3]));
							$fieldlabel = str_replace("_", " ", $fieldlabel);
							if($module_name){
								$field = getTranslatedString($module_name, $module_name)." ".getTranslatedString($fieldlabel,$module_name);
							} else {
								$field = getTranslatedString($fieldlabel);
							}
						}
						$uitype_arr[str_replace($escapedchars," ",$module_name."_".$fieldlist[3])] = $adb->query_result($mod_query,0,"uitype");
						$totclmnflds[str_replace($escapedchars," ",$module_name."_".$fieldlist[3])] = $field;
					}
					for($i =0;$i<$y;$i++)
					{
						$fld = $adb->field_name($result, $i);
						$keyhdr[$fld->name] = $custom_field_values[$i];
					}

					foreach($totclmnflds as $key=>$value)
					{
						$coltotalhtml .= '<tr class="rptGrpHead" valign=top>';
						$col_header = trim(str_replace($modules," ",$value));
						$fld_name_1 = $this->primarymodule . "_" . trim($value);
						$fld_name_2 = $this->secondarymodule . "_" . trim($value);
						if($uitype_arr[$key]==71 || $uitype_arr[$key] == 72 ||
											in_array($fld_name_1,$this->append_currency_symbol_to_value) || in_array($fld_name_2,$this->append_currency_symbol_to_value)) {
							$col_header .= " (".$app_strings['LBL_IN']." ".$current_user->currency_symbol.")";
							$convert_price = true;
						} else{
							$convert_price = false;
						}
						$coltotalhtml .= '<td class="rptData">'. $col_header .'</td>';
						$value = decode_html(trim($key));
						$arraykey = $value.'_SUM';
						if(isset($keyhdr[$arraykey]))
						{
							if($convert_price)
								$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey]);
							else
								$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey], null, true);
							if (substr($arraykey, 0, 21)=='Timecontrol_TotalTime' or substr($arraykey, 0, 18)=='TCTotals_TotalTime') $conv_value=$keyhdr[$arraykey];
							$coltotalhtml .= '<td class="rptTotal">'.$conv_value.'</td>';
						}else
						{
							$coltotalhtml .= '<td class="rptTotal">&nbsp;</td>';
						}

						$arraykey = $value.'_AVG';
						if(isset($keyhdr[$arraykey]))
						{
							if($convert_price)
								$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey]);
							else
								$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey], null, true);
							if (substr($arraykey, 0, 21)=='Timecontrol_TotalTime' or substr($arraykey, 0, 18)=='TCTotals_TotalTime') $conv_value=$keyhdr[$arraykey];
							$coltotalhtml .= '<td class="rptTotal">'.$conv_value.'</td>';
						}else
						{
							$coltotalhtml .= '<td class="rptTotal">&nbsp;</td>';
						}

						$arraykey = $value.'_MIN';
						if(isset($keyhdr[$arraykey]))
						{
							if($convert_price)
								$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey]);
							else
								$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey], null, true);
							if (substr($arraykey, 0, 21)=='Timecontrol_TotalTime' or substr($arraykey, 0, 18)=='TCTotals_TotalTime') $conv_value=$keyhdr[$arraykey];
							$coltotalhtml .= '<td class="rptTotal">'.$conv_value.'</td>';
						}else
						{
							$coltotalhtml .= '<td class="rptTotal">&nbsp;</td>';
						}

						$arraykey = $value.'_MAX';
						if(isset($keyhdr[$arraykey]))
						{
							if($convert_price)
								$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey]);
							else
								$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey], null, true);
							if (substr($arraykey, 0, 21)=='Timecontrol_TotalTime' or substr($arraykey, 0, 18)=='TCTotals_TotalTime') $conv_value=$keyhdr[$arraykey];
							$coltotalhtml .= '<td class="rptTotal">'.$conv_value.'</td>';
						}else
						{
							$coltotalhtml .= '<td class="rptTotal">&nbsp;</td>';
						}

						$coltotalhtml .= '<tr>';

						// Performation Optimization: If Direct output is desired
						if($directOutput) {
							echo $coltotalhtml;
							$coltotalhtml = '';
						}
					}

					$coltotalhtml .= "</table>";

					// Performation Optimization: If Direct output is desired
					if($directOutput) {
						echo $coltotalhtml;
						$coltotalhtml = '';
					}
				}
			}
			return $coltotalhtml;
		}elseif($outputformat == "PRINT")
		{
			$sSQL = $this->sGetSQLforReport($this->reportid,$filtersql);
			$result = $adb->query($sSQL);
			if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1)
			$picklistarray = $this->getAccessPickListValues();

			if($result)
			{
				$noofrows = $adb->num_rows($result);
				$this->number_of_rows = $noofrows;
				$custom_field_values = $adb->fetch_array($result);
				$groupslist = $this->getGroupingList($this->reportid);
				$y=$adb->num_fields($result);
				$header = '';
				for ($x=0; $x<$y-1; $x++)
				{
					$fld = $adb->field_name($result, $x);
					list($module, $fieldLabel) = explode('_', $fld->name, 2);
					$fieldInfo = getFieldByReportLabel($module, $fieldLabel);
					if(!empty($fieldInfo)) {
						$field = WebserviceField::fromArray($adb, $fieldInfo);
					}
					if(!empty($fieldInfo)) {
						$headerLabel = getTranslatedString($field->getFieldLabelKey(), $module);
					} else {
						$headerLabel = getTranslatedString(str_replace('_', " ", $fieldLabel), $module);
					}
					/*STRING TRANSLATION starts */
					$moduleLabel = '';
					if(in_array($module,$modules_selected))
						$moduleLabel = getTranslatedString($module,$module);

					if(empty($headerLabel)) {
							$headerLabel = getTranslatedString(str_replace('_', " ", $fld->name));
					}
					if(!empty($this->secondarymodule)) {
						if($moduleLabel != '') {
							$headerLabel = $moduleLabel." ". $headerLabel;
						}
					}
					$header .= "<th>".$headerLabel."</th>";
				}

				$valtemplate = '';
				$lastvalue = '';
				$secondvalue = '';
				$thirdvalue = '';
				do {
					$newvalue = '';
					$snewvalue = '';
					$tnewvalue = '';
					if(count($groupslist) == 1)
					{
						$newvalue = $custom_field_values[0];
					}elseif(count($groupslist) == 2)
					{
						$newvalue = $custom_field_values[0];
						$snewvalue = $custom_field_values[1];
					}elseif(count($groupslist) == 3)
					{
						$newvalue = $custom_field_values[0];
						$snewvalue = $custom_field_values[1];
						$tnewvalue = $custom_field_values[2];
					}

					if($newvalue == '') $newvalue = '-';
					if($snewvalue == '') $snewvalue = '-';
					if($tnewvalue == '') $tnewvalue = '-';
					$valtemplate .= '<tr>';

					for ($i=0; $i<$y-1; $i++)
					{
						$fld = $adb->field_name($result, $i);
						$fieldvalue = getReportFieldValue($this, $picklistarray, $fld,$custom_field_values, $i);
						if(($lastvalue == $fieldvalue) && $this->reporttype == "summary")
						{
							if($this->reporttype == "summary")
							{
								$valtemplate .= "<td style='border-top:1px dotted #FFFFFF;'>&nbsp;</td>";
							}else
							{
								$valtemplate .= "<td>".$fieldvalue."</td>";
							}
						}else if(($secondvalue == $fieldvalue) && $this->reporttype == "summary")
						{
							if($lastvalue == $newvalue)
							{
								$valtemplate .= "<td style='border-top:1px dotted #FFFFFF;'>&nbsp;</td>";
							}else
							{
								$valtemplate .= "<td>".$fieldvalue."</td>";
							}
						}
						else if(($thirdvalue == $fieldvalue) && $this->reporttype == "summary")
						{
							if($secondvalue == $snewvalue)
							{
								$valtemplate .= "<td style='border-top:1px dotted #FFFFFF;'>&nbsp;</td>";
							}else
							{
								$valtemplate .= "<td>".$fieldvalue."</td>";
							}
						}
						else
						{
							if($this->reporttype == "tabular")
							{
								$valtemplate .= "<td>".$fieldvalue."</td>";
							}else
							{
								$valtemplate .= "<td>".$fieldvalue."</td>";
							}
						}
					}
					$valtemplate .= "</tr>";
					$lastvalue = $newvalue;
					$secondvalue = $snewvalue;
					$thirdvalue = $tnewvalue;
					set_time_limit($php_max_execution_time);
				}while($custom_field_values = $adb->fetch_array($result));

				$sHTML = '<tr>'.$header.'</tr>'.$valtemplate;
				$return_data[] = $sHTML;
				$return_data[] = $noofrows;
				return $return_data;
			}
		}elseif($outputformat == "PRINT_TOTAL")
		{
			$escapedchars = Array('_SUM','_AVG','_MIN','_MAX');
			$sSQL = $this->sGetSQLforReport($this->reportid,$filtersql,"COLUMNSTOTOTAL");
			$coltotalhtml = '';
			if(isset($this->totallist) and count($this->totallist)>0) {
				if($sSQL != "")
				{
					$result = $adb->query($sSQL);
					$y=$adb->num_fields($result);
					$custom_field_values = $adb->fetch_array($result);

					$coltotalhtml = "<br /><table align='center' width='60%' cellpadding='3' cellspacing='0' border='1' class='printReport'><tr><td class='rptCellLabel'>".$mod_strings['Totals']."</td><td><b>".$mod_strings['SUM']."</b></td><td><b>".$mod_strings['AVG']."</b></td><td><b>".$mod_strings['MIN']."</b></td><td><b>".$mod_strings['MAX']."</b></td></tr>";

					// Performation Optimization: If Direct output is desired
					if($directOutput) {
						echo $coltotalhtml;
						$coltotalhtml = '';
					}

					foreach($this->totallist as $key=>$value)
					{
						$fieldlist = explode(":",$key);
						$mod_query = $adb->pquery("SELECT distinct(tabid) as tabid, uitype as uitype from vtiger_field where tablename = ? and columnname=?",array($fieldlist[1],$fieldlist[2]));
						if($adb->num_rows($mod_query)>0){
							$module_name = getTabModuleName($adb->query_result($mod_query,0,'tabid'));
							$fieldlabel = trim(str_replace($escapedchars," ",$fieldlist[3]));
							$fieldlabel = str_replace("_", " ", $fieldlabel);
							if($module_name){
								$field = getTranslatedString($module_name, $module_name)." ".getTranslatedString($fieldlabel,$module_name);
							} else {
								$field = getTranslatedString($fieldlabel);
							}
						}
						$uitype_arr[str_replace($escapedchars," ",$module_name."_".$fieldlist[3])] = $adb->query_result($mod_query,0,"uitype");
						$totclmnflds[str_replace($escapedchars," ",$module_name."_".$fieldlist[3])] = $field;
					}

					for($i =0;$i<$y;$i++)
					{
						$fld = $adb->field_name($result, $i);
						$keyhdr[$fld->name] = $custom_field_values[$i];

					}
					foreach($totclmnflds as $key=>$value)
					{
						$coltotalhtml .= '<tr class="rptGrpHead">';
						$col_header = getTranslatedString(trim(str_replace($modules," ",$value)));
						$fld_name_1 = $this->primarymodule . "_" . trim($value);
						$fld_name_2 = $this->secondarymodule . "_" . trim($value);
						if($uitype_arr[$key]==71 || $uitype_arr[$key] == 72 ||
										in_array($fld_name_1,$this->append_currency_symbol_to_value) || in_array($fld_name_2,$this->append_currency_symbol_to_value)) {
							$col_header .= " (".$app_strings['LBL_IN']." ".$current_user->currency_symbol.")";
							$convert_price = true;
						} else{
							$convert_price = false;
						}
						$coltotalhtml .= '<td class="rptData">'. $col_header .'</td>';
						$value = decode_html(trim($key));
						$arraykey = $value.'_SUM';
						if(isset($keyhdr[$arraykey]))
						{
							if($convert_price)
								$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey]);
							else
								$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey], null, true);
							$coltotalhtml .= "<td class='rptTotal'>".$conv_value.'</td>';
						}else
						{
							$coltotalhtml .= "<td class='rptTotal'>&nbsp;</td>";
						}

						$arraykey = $value.'_AVG';
						if(isset($keyhdr[$arraykey]))
						{
							if($convert_price)
								$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey]);
							else
								$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey], null, true);
							$coltotalhtml .= "<td class='rptTotal'>".$conv_value.'</td>';
						}else
						{
							$coltotalhtml .= "<td class='rptTotal'>&nbsp;</td>";
						}

						$arraykey = $value.'_MIN';
						if(isset($keyhdr[$arraykey]))
						{
							if($convert_price)
								$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey]);
							else
								$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey], null, true);
							$coltotalhtml .= "<td class='rptTotal'>".$conv_value.'</td>';
						}else
						{
							$coltotalhtml .= "<td class='rptTotal'>&nbsp;</td>";
						}

						$arraykey = $value.'_MAX';
						if(isset($keyhdr[$arraykey]))
						{
							if($convert_price)
								$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey]);
							else
								$conv_value = CurrencyField::convertToUserFormat ($keyhdr[$arraykey], null, true);
							$coltotalhtml .= "<td class='rptTotal'>".$conv_value.'</td>';
						}else
						{
							$coltotalhtml .= "<td class='rptTotal'>&nbsp;</td>";
						}

						$coltotalhtml .= '</tr>';

						// Performation Optimization: If Direct output is desired
						if($directOutput) {
							echo $coltotalhtml;
							$coltotalhtml = '';
						}
					}

					$coltotalhtml .= "</table>";
					// Performation Optimization: If Direct output is desired
					if($directOutput) {
						echo $coltotalhtml;
						$coltotalhtml = '';
					}
				}
			}
			return $coltotalhtml;
		}
	}

	function getColumnsTotal($reportid,$selectlist='')
	{
		// Have we initialized it already?
		if($this->_columnstotallist !== false) {
			return $this->_columnstotallist;
		}

		global $adb, $modules, $log, $current_user;
		static $modulename_cache = array();
		$query = "select * from vtiger_reportmodules where reportmodulesid =?";
		$res = $adb->pquery($query , array($reportid));
		$modrow = $adb->fetch_array($res);
		$premod = $modrow["primarymodule"];
		$secmod = $modrow["secondarymodules"];
		$coltotalsql = "select vtiger_reportsummary.* from vtiger_report";
		$coltotalsql .= " inner join vtiger_reportsummary on vtiger_report.reportid = vtiger_reportsummary.reportsummaryid";
		$coltotalsql .= " where vtiger_report.reportid =?";

		$result = $adb->pquery($coltotalsql, array($reportid));
		$seltotalcols = array();
		$stdfilterlist = array();
		while($coltotalrow = $adb->fetch_array($result))
		{
			$fieldcolname = $coltotalrow["columnname"];
			if($fieldcolname != "none")
			{
				$sckey = $scval = '';
				$fieldlist = explode(":",$fieldcolname);
				$field_tablename = $fieldlist[1];
				$field_columnname = $fieldlist[2];

				$cachekey = $field_tablename . ":" . $field_columnname;
				if (!isset($modulename_cache[$cachekey])) {
					$mod_query = $adb->pquery("SELECT distinct(tabid) as tabid from vtiger_field where tablename = ? and columnname=?",array($fieldlist[1],$fieldlist[2]));
					if($adb->num_rows($mod_query)>0){
						$module_name = getTabModuleName($adb->query_result($mod_query,0,'tabid'));
						$modulename_cache[$cachekey] = $module_name;
					}
				} else {
					$module_name = $modulename_cache[$cachekey];
				}

				$fieldlabel = trim($fieldlist[3]);
				$field_columnalias = $module_name."_".$fieldlist[3];
				$field_columnalias = decode_html($field_columnalias);
				$query_columnalias = substr($field_columnalias, 0, strrpos($field_columnalias, '_'));
				$query_columnalias = str_replace(array(' ','&'),'_',$query_columnalias);
				$sckey = $field_tablename.':'.$field_columnname.':'.$query_columnalias.':'.$field_columnname.':N'; // vtiger_invoice:subject:Invoice_Subject:subject:V
				$scval = $field_tablename.'.'.$field_columnname." AS '".$query_columnalias."'"; // vtiger_invoice.subject AS 'Invoice_Subject'
				$seltotalcols[$sckey] = $scval;
				$field_permitted = false;
				if(CheckColumnPermission($field_tablename,$field_columnname,$module_name) != "false"){
					$field_permitted = true;
				}
				/* one call to CheckColumnPermission with $module_name is better than the block below
				if(CheckColumnPermission($field_tablename,$field_columnname,$premod) != "false"){
					$field_permitted = true;
				} else {
					$mod = explode(":",$secmod);
					foreach($mod as $key){
						if(CheckColumnPermission($field_tablename,$field_columnname,$key) != "false"){
							$field_permitted=true;
						}
					}
				}
				*/
				if($field_permitted == true)
				{
					if($field_tablename == 'vtiger_products' && $field_columnname == 'unit_price') {
						// Query needs to be rebuild to get the value in user preferred currency. [innerProduct and actual_unit_price are table and column alias.]
						$query_columnalias = ' actual_unit_price';
						$seltotalcols['innerProduct:actual_unit_price:Products_Unit_Price:actual_unit_price:N'] = 'innerProduct.actual_unit_price AS actual_unit_price';
					}
					if($field_tablename == 'vtiger_service' && $field_columnname == 'unit_price') {
						// Query needs to be rebuild to get the value in user preferred currency. [innerProduct and actual_unit_price are table and column alias.]
						$query_columnalias = ' actual_unit_price';
						$seltotalcols['innerService:actual_unit_price:Services_Unit_Price:actual_unit_price:N'] = 'innerService.actual_unit_price AS actual_unit_price';
					}
					if(($field_tablename == 'vtiger_invoice' || $field_tablename == 'vtiger_quotes' || $field_tablename == 'vtiger_purchaseorder' || $field_tablename == 'vtiger_salesorder' || $field_tablename == 'vtiger_issuecards')
							&& ($field_columnname == 'total' || $field_columnname == 'subtotal' || $field_columnname == 'discount_amount' || $field_columnname == 's_h_amount')) {
						$query_columnalias = " $query_columnalias/$module_name"."_Conversion_Rate ";
						$seltotalcols[$field_tablename.':conversion_rate:'.$module_name.'_Conversion_Rate:conversion_rate:N'] = "$field_tablename.conversion_rate AS $module_name"."_Conversion_Rate ";
					}
					if($fieldlist[4] == 2)
					{
						if ($fieldlist[2]=='totaltime')
							$stdfilterlist[$fieldcolname] = "sec_to_time(sum(time_to_sec(".$query_columnalias."))) '".$field_columnalias."'";
						else
							$stdfilterlist[$fieldcolname] = "sum($query_columnalias) '".$field_columnalias."'";
					}
					if($fieldlist[4] == 3)
					{
						//Fixed average calculation issue due to NULL values ie., when we use avg() function, NULL values will be ignored.to avoid this we use (sum/count) to find average.
						//$stdfilterlist[$fieldcolname] = "avg(".$fieldlist[1].".".$fieldlist[2].") '".$fieldlist[3]."'";
						if ($fieldlist[2]=='totaltime')
							$stdfilterlist[$fieldcolname] = "sec_to_time(sum(time_to_sec(".$query_columnalias."))/count(*)) '".$field_columnalias."'";
						else
							$stdfilterlist[$fieldcolname] = "(sum($query_columnalias)/count(*)) '".$field_columnalias."'";
					}
					if($fieldlist[4] == 4)
					{
						if ($fieldlist[2]=='totaltime')
							$stdfilterlist[$fieldcolname] = "sec_to_time(min(time_to_sec(".$query_columnalias."))) '".$field_columnalias."'";
						else
							$stdfilterlist[$fieldcolname] = "min($query_columnalias) '".$field_columnalias."'";
					}
					if($fieldlist[4] == 5)
					{
						if ($fieldlist[2]=='totaltime')
							$stdfilterlist[$fieldcolname] = "sec_to_time(max(time_to_sec(".$query_columnalias."))) '".$field_columnalias."'";
						else
							$stdfilterlist[$fieldcolname] = "max($query_columnalias) '".$field_columnalias."'";
					}
				}
			}
		}
		// Save the information
		$this->_columnstotallist = $stdfilterlist;
		$stc = array_diff($seltotalcols,$selectlist);
		$this->_columnstotallistaddtoselect = $stc;
		$log->info("ReportRun :: Successfully returned getColumnsTotal".$reportid);
		return $stdfilterlist;
	}

	/** function to get query for the columns to total for the given reportid
	 *  @ param $reportid : Type integer
	 *  This returns columnstoTotal query for the reportid
	 */
	function getColumnsToTotalColumns($reportid)
	{
		global $adb, $modules, $log;

		$sreportstdfiltersql = "select vtiger_reportsummary.* from vtiger_report";
		$sreportstdfiltersql .= " inner join vtiger_reportsummary on vtiger_report.reportid = vtiger_reportsummary.reportsummaryid";
		$sreportstdfiltersql .= " where vtiger_report.reportid =?";

		$result = $adb->pquery($sreportstdfiltersql, array($reportid));
		$noofrows = $adb->num_rows($result);

		for($i=0; $i<$noofrows; $i++)
		{
			$fieldcolname = $adb->query_result($result,$i,"columnname");

			if($fieldcolname != "none")
			{
				$fieldlist = explode(":",$fieldcolname);
				if($fieldlist[4] == 2)
				{
					$sSQLList[] = "sum(".$fieldlist[1].".".$fieldlist[2].") ".$fieldlist[3];
				}
				if($fieldlist[4] == 3)
				{
					$sSQLList[] = "avg(".$fieldlist[1].".".$fieldlist[2].") ".$fieldlist[3];
				}
				if($fieldlist[4] == 4)
				{
					$sSQLList[] = "min(".$fieldlist[1].".".$fieldlist[2].") ".$fieldlist[3];
				}
				if($fieldlist[4] == 5)
				{
					$sSQLList[] = "max(".$fieldlist[1].".".$fieldlist[2].") ".$fieldlist[3];
				}
			}
		}
		if(isset($sSQLList))
		{
			$sSQL = implode(",",$sSQLList);
		}
		$log->info("ReportRun :: Successfully returned getColumnsToTotalColumns".$reportid);
		return $sSQL;
	}

	/** Function to get picklist value array based on profile returns permitted fields in array format */
	function getAccessPickListValues()
	{
		global $adb, $current_user;
		$id = array(getTabid($this->primarymodule));
		if($this->secondarymodule != '')
			array_push($id, getTabid($this->secondarymodule));

		$query = 'select fieldname,columnname,fieldid,fieldlabel,tabid,uitype from vtiger_field where tabid in('. generateQuestionMarks($id) .') and uitype in (15,33,55)'; //and columnname in (?)';
		$result = $adb->pquery($query, $id);//,$select_column));
		$roleid=$current_user->roleid;
		$subrole = getRoleSubordinates($roleid);
		if(count($subrole)> 0)
		{
			$roleids = $subrole;
			array_push($roleids, $roleid);
		}
		else
		{
			$roleids = $roleid;
		}

		$temp_status = Array();
		$fieldlists = array();
		for($i=0;$i < $adb->num_rows($result);$i++)
		{
			$fieldname = $adb->query_result($result,$i,"fieldname");
			$fieldlabel = $adb->query_result($result,$i,"fieldlabel");
			$tabid = $adb->query_result($result,$i,"tabid");
			$uitype = $adb->query_result($result,$i,"uitype");

			$fieldlabel1 = str_replace(" ","_",$fieldlabel);
			$keyvalue = getTabModuleName($tabid)."_".$fieldlabel1;
			$fieldvalues = Array();
			if (count($roleids) > 1) {
				$mulsel="select distinct $fieldname from vtiger_$fieldname inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_$fieldname.picklist_valueid where roleid in (\"". implode($roleids,"\",\"") ."\") and picklistid in (select picklistid from vtiger_picklist)"; // order by sortid asc - not requried
			} else {
				$mulsel="select distinct $fieldname from vtiger_$fieldname inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_$fieldname.picklist_valueid where roleid ='".$roleid."' and picklistid in (select picklistid from vtiger_picklist)"; // order by sortid asc - not requried
			}
			if($fieldname != 'firstname')
				$mulselresult = $adb->query($mulsel);
			for($j=0;$j < $adb->num_rows($mulselresult);$j++)
			{
				$fldvalue = $adb->query_result($mulselresult,$j,$fieldname);
				if(in_array($fldvalue,$fieldvalues)) continue;
				$fieldvalues[] = $fldvalue;
			}
			$field_count = count($fieldvalues);
			if( $uitype == 15 && $field_count > 0 && ($fieldname == 'taskstatus' || $fieldname == 'eventstatus'))
			{
				$temp_count =count($temp_status[$keyvalue]);
				if($temp_count > 0)
				{
					for($t=0;$t < $field_count;$t++)
					{
						$temp_status[$keyvalue][($temp_count+$t)] = $fieldvalues[$t];
					}
					$fieldvalues = $temp_status[$keyvalue];
				}
				else
					$temp_status[$keyvalue] = $fieldvalues;
			}

			if($uitype == 33)
				$fieldlists[1][$keyvalue] = $fieldvalues;
			else if($uitype == 55 && $fieldname == 'salutationtype')
				$fieldlists[$keyvalue] = $fieldvalues;
			else if($uitype == 15)
				$fieldlists[$keyvalue] = $fieldvalues;
		}
		return $fieldlists;
	}

	function getReportPDF($filterlist=false) {
		require_once 'include/tcpdf/tcpdf.php';

		$arr_val = $this->GenerateReport("PDF",$filterlist);

		if(isset($arr_val)) {
			foreach($arr_val as $wkey=>$warray_value) {
				foreach($warray_value as $whd=>$wvalue) {
					if(strlen($wvalue) < strlen($whd)) {
						$w_inner_array[] = strlen($whd);
					} else {
						$w_inner_array[] = strlen($wvalue);
					}
				}
				$warr_val[] = $w_inner_array;
				unset($w_inner_array);
			}

			foreach($warr_val[0] as $fkey=>$fvalue) {
				foreach($warr_val as $wkey=>$wvalue) {
					$f_inner_array[] = $warr_val[$wkey][$fkey];
				}
				sort($f_inner_array,1);
				$farr_val[] = $f_inner_array;
				unset($f_inner_array);
			}

			foreach($farr_val as $skkey=>$skvalue) {
				if($skvalue[count($arr_val)-1] == 1) {
					$col_width[] = ($skvalue[count($arr_val)-1] * 35);
				} else {
					$col_width[] = ($skvalue[count($arr_val)-1] * 6) + 10 ;
				}
			}
			$count = 0;
			$headerHTML = '';
			foreach($arr_val[0] as $key=>$value) {
				$headerHTML .= '<td width="'.$col_width[$count].'" bgcolor="#DDDDDD"><b>'.$key.'</b></td>';
				$count = $count + 1;
			}
			$dataHTML = '';
			foreach($arr_val as $key=>$array_value) {
				$valueHTML = '';
				$count = 0;
				foreach($array_value as $hd=>$value) {
					$valueHTML .= '<td width="'.$col_width[$count].'">'.$value.'</td>';
					$count = $count + 1;
				}
				$dataHTML .= '<tr>'.$valueHTML.'</tr>';
			}

		}

		$totalpdf = $this->GenerateReport("PRINT_TOTAL",$filterlist);
		$html = '<table border="1"><tr>'.$headerHTML.'</tr>'.$dataHTML.'</table>';
		$columnlength = array_sum($col_width);
		if($columnlength > 14400) {
			global $log, $app_strings;
			$log->fatal('PDF REPORT GENERATION: '.$app_strings['LBL_PDF']);
			$columnlength = 14400;
		}
		if($columnlength <= 420 ) {
			$pdf = new TCPDF('P','mm','A5',true);

		} elseif($columnlength >= 421 && $columnlength <= 800) {
			$pdf = new TCPDF('L','mm','A4',true);

		} elseif($columnlength >= 801 && $columnlength <= 1120) {
			$pdf = new TCPDF('L','mm','A3',true);

		}elseif($columnlength >=1121 && $columnlength <= 1600) {
			$pdf = new TCPDF('L','mm','A2',true);

		}elseif($columnlength >=1601 && $columnlength <= 2200) {
			$pdf = new TCPDF('L','mm','A1',true);
		}
		elseif($columnlength >=2201 && $columnlength <= 3370) {
			$pdf = new TCPDF('L','mm','A0',true);
		}
		elseif($columnlength >=3371 && $columnlength <= 4690) {
			$pdf = new TCPDF('L','mm','2A0',true);
		}
		elseif($columnlength >=4691 && $columnlength <= 6490) {
			$pdf = new TCPDF('L','mm','4A0',true);
		}
		else {
			$columnhight = count($arr_val)*15;
			$format = array($columnhight,$columnlength);
			$pdf = new TCPDF('L','mm',$format,true);
		}
		$pdf->SetMargins(10, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		//$pdf->setLanguageArray($l);
		//echo '<pre>';print_r($columnlength);echo '</pre>';
		$pdf->AddPage();

		$pdf->SetFillColor(224,235,255);
		$pdf->SetTextColor(0);
		$pdf->SetFont('FreeSerif','B',14);
		$pdf->Cell(($columnlength*50),10,getTranslatedString($this->reportname),0,0,'C',0);
		//$pdf->writeHTML($oReport->reportname);
		$pdf->Ln();

		$pdf->SetFont('FreeSerif','',10);

		$pdf->writeHTML($html.$totalpdf);

		return $pdf;
	}

	function writeReportToExcelFile($fileName, $filterlist='') {
		global $currentModule, $current_language, $current_user;
		$mod_strings = return_module_language($current_language, $currentModule);

		require_once('include/PHPExcel/PHPExcel.php');
		$xlsrowheight = GlobalVariable::getVariable('Report.Excel.Export.RowHeight', 20);
		$workbook = new PHPExcel();
		$worksheet = $workbook->setActiveSheetIndex(0);
		$fieldinfo = array();
		$arr_val = $this->GenerateReport('PDF',$filterlist,false,$fieldinfo);
		$totalxls = $this->GenerateReport('TOTALXLS',$filterlist);

		$header_styles = array(
			'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb'=>'E1E0F7')),
			'font' => array('bold' => true)
		);

		if(isset($arr_val)) {
			$FieldDataTypes = array();
			foreach($arr_val[0] as $hdr=>$value) {
				$FieldDataTypes[$hdr] = $fieldinfo[$hdr]->getFieldDataType();
				if ($fieldinfo[$hdr]->getColumnName()=='totaltime') $FieldDataTypes[$hdr] = 'time';
				if ($fieldinfo[$hdr]->getColumnName()=='totaldaytime') $FieldDataTypes[$hdr] = 'time';
			}
			$BoolTrue = getTranslatedString('LBL_YES');
			//$BoolFalse = getTranslatedString('LBL_NO');
			$count = 0;
			$rowcount = 1;
			$workbook->getActiveSheet()->getRowDimension($rowcount)->setRowHeight($xlsrowheight);
			//copy the first value details
			$arrayFirstRowValues = $arr_val[0];
			foreach($arrayFirstRowValues as $key=>$value) {
				$worksheet->setCellValueExplicitByColumnAndRow($count, $rowcount, $key, true);
				$worksheet->getStyleByColumnAndRow($count, $rowcount)->applyFromArray($header_styles);

				// NOTE Performance overhead: http://stackoverflow.com/questions/9965476/phpexcel-column-size-issues
				$worksheet->getColumnDimensionByColumn($count)->setAutoSize(true);

				$count = $count + 1;
				if ($FieldDataTypes[$key]=='currency') {
					$worksheet->setCellValueExplicitByColumnAndRow($count, $rowcount, getTranslatedString('LBL_CURRENCY'), true);
					$worksheet->getStyleByColumnAndRow($count, $rowcount)->applyFromArray($header_styles);
					$worksheet->getColumnDimensionByColumn($count)->setAutoSize(true);
					$count = $count + 1;
				}
			}

			$rowcount++;
			$workbook->getActiveSheet()->getRowDimension($rowcount)->setRowHeight($xlsrowheight);
			foreach($arr_val as $key=>$array_value) {
				$count = 0;
				foreach($array_value as $hdr=>$value) {
					$value = decode_html($value);
					$datetime = false;
					switch ($FieldDataTypes[$hdr]) {
						case 'boolean':
							$celltype = PHPExcel_Cell_DataType::TYPE_BOOL;
							$value = ($value==$BoolTrue ? 1:0);
							break;
						case 'integer':
						case 'double':
						case 'currency':
							$celltype = PHPExcel_Cell_DataType::TYPE_NUMERIC;
							break;
						case 'date':
						case 'time':
							if ($value!='-') {
								if (strpos($value,':')>0 and (strpos($value,'-')===false)) {
									// only time, no date
									$dt = new DateTime("1970-01-01 $value");
								} elseif(strpos($value,':')>0 and (strpos($value,'-')>0)){
									// date and time
									$dt = new DateTime($value);
									$datetime = true;
								} else {
									$value = DateTimeField::__convertToDBFormat($value, $current_user->date_format);
									$dt = new DateTime($value);
								}
								$value = PHPExcel_Shared_Date::PHPToExcel($dt);
								$celltype = PHPExcel_Cell_DataType::TYPE_NUMERIC;
							} else {
								$celltype = PHPExcel_Cell_DataType::TYPE_STRING;
							}
							break;
						default:
							$celltype = PHPExcel_Cell_DataType::TYPE_STRING;
							break;
					}
					if ($FieldDataTypes[$hdr]=='currency') {
						$csym = preg_replace('/[0-9,.-]/', '', $value);
						$value = preg_replace('/[^0-9,.-]/', '', $value);
						$value = str_replace($current_user->currency_grouping_separator, '', $value);
						if ($current_user->currency_decimal_separator!='.')
							$value = str_replace($current_user->currency_decimal_separator, '.', $value);
					}
					$worksheet->setCellValueExplicitByColumnAndRow($count, $rowcount, $value, $celltype);
					if ($FieldDataTypes[$hdr]=='date') {
						if($datetime){
							$worksheet->getStyleByColumnAndRow($count, $rowcount)->getNumberFormat()->setFormatCode($current_user->date_format." ".PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME4);
						}else{
							$worksheet->getStyleByColumnAndRow($count, $rowcount)->getNumberFormat()->setFormatCode($current_user->date_format);
						}
					} elseif ($FieldDataTypes[$hdr]=='time') {
						$worksheet->getStyleByColumnAndRow($count, $rowcount)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME4);
					}
					if ($FieldDataTypes[$hdr]=='currency') {
						$count = $count + 1;
						$worksheet->setCellValueExplicitByColumnAndRow($count, $rowcount, $csym, PHPExcel_Cell_DataType::TYPE_STRING);
					}
					$count = $count + 1;
				}
				$rowcount++;
				$workbook->getActiveSheet()->getRowDimension($rowcount)->setRowHeight($xlsrowheight);
			}

			// Summary Total
			$rowcount++;
			$workbook->getActiveSheet()->getRowDimension($rowcount)->setRowHeight($xlsrowheight);
			$count=0;
			if(isset($totalxls) and is_array($totalxls) and count($totalxls)>0) {
				if (is_array($totalxls[0])) {
					$worksheet->setCellValueExplicitByColumnAndRow($count, $rowcount, getTranslatedString('Totals','Reports'), PHPExcel_Cell_DataType::TYPE_STRING);
					$worksheet->getStyleByColumnAndRow($count, $rowcount)->applyFromArray($header_styles);
					$count = $count + 1;
					foreach($totalxls[0] as $key=>$value) {
						$chdr=substr($key,-3,3);
						$translated_str = in_array($chdr ,array_keys($mod_strings))?$mod_strings[$chdr]:$key;
						$worksheet->setCellValueExplicitByColumnAndRow($count, $rowcount, decode_html($translated_str), PHPExcel_Cell_DataType::TYPE_STRING);
						$worksheet->getStyleByColumnAndRow($count, $rowcount)->applyFromArray($header_styles);
						$count = $count + 1;
					}
				}
				$rowcount++;
				$workbook->getActiveSheet()->getRowDimension($rowcount)->setRowHeight($xlsrowheight);
				foreach($totalxls as $key=>$array_value) {
					$count = 0;
					foreach($array_value as $hdr=>$value) {
						if ($count==0) {
							$lbl = substr($hdr, 0, strrpos($hdr, '_'));
							$mname = substr($lbl, 0, strpos($lbl, '_'));
							$lbl = substr($lbl, strpos($lbl, '_')+1);
							$lbl = str_replace('_', ' ', $lbl);
							$lbl = getTranslatedString($lbl,$mname);
							$worksheet->setCellValueExplicitByColumnAndRow($count, $rowcount, decode_html($lbl), PHPExcel_Cell_DataType::TYPE_STRING);
							$worksheet->getStyleByColumnAndRow($count, $rowcount)->applyFromArray($header_styles);
							$workbook->getActiveSheet()->getRowDimension($rowcount)->setRowHeight($xlsrowheight);
							$count = $count + 1;
						}
						$value = str_replace($current_user->currency_grouping_separator, '', $value);
						if ($current_user->currency_decimal_separator!='.')
							$value = str_replace($current_user->currency_decimal_separator, '.', $value);
						$worksheet->setCellValueExplicitByColumnAndRow($count, $rowcount, $value, PHPExcel_Cell_DataType::TYPE_NUMERIC);
						$count = $count + 1;
					}
					$rowcount++;
				}
			}
		}
		$workbookWriter = PHPExcel_IOFactory::createWriter($workbook, 'Excel5');
		$workbookWriter->save($fileName);
	}

	function writeReportToCSVFile($fileName, $filterlist='') {
		global $currentModule, $current_language;
		$mod_strings = return_module_language($current_language, $currentModule);

		$arr_val = $this->GenerateReport("PDF",$filterlist);

		$fp = fopen($fileName, 'w+');

		if(isset($arr_val)) {
			$csv_values = array();
			// Header
			$csv_values = array_keys($arr_val[0]);
			fputcsv($fp, $csv_values);
			foreach($arr_val as $key=>$array_value) {
				$csv_values = array_map('decode_html', array_values($array_value));
				fputcsv($fp, $csv_values);
			}
		}
		fclose($fp);
	}

	function getGroupByTimeList($reportId){
		global $adb;
		$groupByTimeQuery = 'SELECT * FROM vtiger_reportgroupbycolumn WHERE reportid=?';
		$groupByTimeRes = $adb->pquery($groupByTimeQuery,array($reportId));
		$num_rows = $adb->num_rows($groupByTimeRes);
		$groupByCondition = array();
		for($i=0;$i<$num_rows;$i++){
			$sortColName = $adb->query_result($groupByTimeRes, $i,'sortcolname');
			list($tablename,$colname,$module_field,$fieldname,$single) = explode(':',$sortColName);
			$groupField = $module_field;
			$groupCriteria = $adb->query_result($groupByTimeRes, $i,'dategroupbycriteria');
			if(in_array($groupCriteria,array_keys($this->groupByTimeParent))){
				$parentCriteria = $this->groupByTimeParent[$groupCriteria];
				foreach($parentCriteria as $criteria){
					$groupByCondition[]=$this->GetTimeCriteriaCondition($criteria, $groupField);
				}
			}
			$groupByCondition[] = $this->GetTimeCriteriaCondition($groupCriteria, $groupField);
		}
		return $groupByCondition;
	}

	function GetTimeCriteriaCondition($criteria,$dateField){
		$condition = '';
		if(strtolower($criteria)=='year'){
			$condition = "DATE_FORMAT($dateField, '%Y' )";
		}
		else if (strtolower($criteria)=='month'){
			$condition = "CEIL(DATE_FORMAT($dateField,'%m')%13)";
		}
		else if(strtolower($criteria)=='quarter'){
			$condition = "CEIL(DATE_FORMAT($dateField,'%m')/3)";
		}
		if(strtolower($criteria)=='day'){
			$condition = "DATE_FORMAT($dateField,'%d')";
		}
		return $condition;
	}

	function GetFirstSortByField($reportid)
	{
		global $adb;
		$groupByField = '';
		$sortFieldQuery = "SELECT * FROM vtiger_reportsortcol
			LEFT JOIN vtiger_reportgroupbycolumn ON (vtiger_reportsortcol.sortcolid = vtiger_reportgroupbycolumn.sortid and vtiger_reportsortcol.reportid = vtiger_reportgroupbycolumn.reportid)
			WHERE columnname!='none' and vtiger_reportsortcol.reportid=? ORDER By sortcolid";
		$sortFieldResult= $adb->pquery($sortFieldQuery,array($reportid));
		if($adb->num_rows($sortFieldResult)>0){
			$fieldcolname = $adb->query_result($sortFieldResult,0,'columnname');
			list($tablename,$colname,$module_field,$fieldname,$typeOfData) = explode(":",$fieldcolname);
			list($modulename,$fieldlabel) = explode('_', $module_field, 2);
			$groupByField = "`".$module_field."`";
			if($typeOfData == "D"){
				$groupCriteria = $adb->query_result($sortFieldResult,0,'dategroupbycriteria');
				if(strtolower($groupCriteria)!='none'){
					if(in_array($groupCriteria,array_keys($this->groupByTimeParent))){
						$parentCriteria = $this->groupByTimeParent[$groupCriteria];
						foreach($parentCriteria as $criteria){
							$groupByCondition[]=$this->GetTimeCriteriaCondition($criteria, $groupByField);
						}
					}
					$groupByCondition[] = $this->GetTimeCriteriaCondition($groupCriteria, $groupByField);
					$groupByField = implode(", ",$groupByCondition);
				}

			} elseif(CheckFieldPermission($fieldname,$modulename) != 'true') {
				if((strpos($tablename,'vtiger_inventoryproductrel') === false && ($colname != 'productid' || $colname != 'serviceid')))
					$groupByField = $tablename.".".$colname;
			}
		}
		return $groupByField;
	}

	function getReferenceFieldColumnList($moduleName, $fieldInfo) {
		$adb = PearDatabase::getInstance();

		$columnsSqlList = array();

		$fieldInstance = WebserviceField::fromArray($adb, $fieldInfo);
		$referenceModuleList = $fieldInstance->getReferenceList();
		$reportSecondaryModules = explode(':', $this->secondarymodule);

		if($moduleName != $this->primarymodule && in_array($this->primarymodule, $referenceModuleList)) {
			$entityTableFieldNames = getEntityFieldNames($this->primarymodule);
			$entityTableName = $entityTableFieldNames['tablename'];
			$entityFieldNames = $entityTableFieldNames['fieldname'];

			$columnList = array();
			if(is_array($entityFieldNames)) {
				foreach ($entityFieldNames as $entityColumnName) {
					$columnList["$entityColumnName"] = "$entityTableName.$entityColumnName";
				}
			} else {
				$columnList[] = "$entityTableName.$entityFieldNames";
			}
			if(count($columnList) > 1) {
				$columnSql = getSqlForNameInDisplayFormat($columnList, $this->primarymodule);
			} else {
				$columnSql = implode('', $columnList);
			}
			$columnsSqlList[] = $columnSql;

		} else {
			foreach($referenceModuleList as $referenceModule) {
				$entityTableFieldNames = getEntityFieldNames($referenceModule);
				$entityTableName = $entityTableFieldNames['tablename'];
				$entityFieldNames = $entityTableFieldNames['fieldname'];

				if($moduleName == 'HelpDesk' && $referenceModule == 'Accounts') {
					$referenceTableName = 'vtiger_accountRelHelpDesk';
				} elseif ($moduleName == 'HelpDesk' && $referenceModule == 'Contacts') {
					$referenceTableName = 'vtiger_contactdetailsRelHelpDesk';
				} elseif ($moduleName == 'HelpDesk' && $referenceModule == 'Products') {
					$referenceTableName = 'vtiger_productsRel';
				} elseif ($moduleName == 'Calendar' && $referenceModule == 'Accounts') {
					$referenceTableName = 'vtiger_accountRelCalendar';
				} elseif ($moduleName == 'Calendar' && $referenceModule == 'Contacts') {
					$referenceTableName = 'vtiger_contactdetailsCalendar';
				} elseif ($moduleName == 'Calendar' && $referenceModule == 'Leads') {
					$referenceTableName = 'vtiger_leaddetailsRelCalendar';
				} elseif ($moduleName == 'Calendar' && $referenceModule == 'Potentials') {
					$referenceTableName = 'vtiger_potentialRelCalendar';
				} elseif ($moduleName == 'Calendar' && $referenceModule == 'Invoice') {
					$referenceTableName = 'vtiger_invoiceRelCalendar';
				} elseif ($moduleName == 'Calendar' && $referenceModule == 'Quotes') {
					$referenceTableName = 'vtiger_quotesRelCalendar';
				} elseif ($moduleName == 'Calendar' && $referenceModule == 'PurchaseOrder') {
					$referenceTableName = 'vtiger_purchaseorderRelCalendar';
				} elseif ($moduleName == 'Calendar' && $referenceModule == 'SalesOrder') {
					$referenceTableName = 'vtiger_salesorderRelCalendar';
				} elseif ($moduleName == 'Calendar' && $referenceModule == 'HelpDesk') {
					$referenceTableName = 'vtiger_troubleticketsRelCalendar';
				} elseif ($moduleName == 'Calendar' && $referenceModule == 'Campaigns') {
					$referenceTableName = 'vtiger_campaignRelCalendar';
				} elseif ($moduleName == 'Calendar' && $referenceModule == 'Vendors') {
					$referenceTableName = 'vtiger_vendorRelCalendar';
				} elseif ($moduleName == 'Contacts' && $referenceModule == 'Accounts') {
					$referenceTableName = 'vtiger_accountContacts';
				} elseif ($moduleName == 'Contacts' && $referenceModule == 'Contacts') {
					$referenceTableName = 'vtiger_contactdetailsContacts';
				} elseif ($moduleName == 'Accounts' && $referenceModule == 'Accounts') {
					$referenceTableName = 'vtiger_accountAccounts';
				} elseif ($moduleName == 'Campaigns' && $referenceModule == 'Products') {
					$referenceTableName = 'vtiger_productsCampaigns';
				} elseif ($moduleName == 'Faq' && $referenceModule == 'Products') {
					$referenceTableName = 'vtiger_productsFaq';
				} elseif ($moduleName == 'Invoice' && $referenceModule == 'SalesOrder') {
					$referenceTableName = 'vtiger_salesorderInvoice';
				} elseif ($moduleName == 'Invoice' && $referenceModule == 'Contacts') {
					$referenceTableName = 'vtiger_contactdetailsInvoice';
				} elseif ($moduleName == 'Invoice' && $referenceModule == 'Accounts') {
					$referenceTableName = 'vtiger_accountInvoice';
				} elseif ($moduleName == 'Products' && $referenceModule == 'Vendors') {
					$referenceTableName = 'vtiger_vendorRelProducts';
				} elseif ($moduleName == 'PurchaseOrder' && $referenceModule == 'Contacts') {
					$referenceTableName = 'vtiger_contactdetailsPurchaseOrder';
				} elseif ($moduleName == 'PurchaseOrder' && $referenceModule == 'Vendors') {
					$referenceTableName = 'vtiger_vendorRelPurchaseOrder';
				} elseif ($moduleName == 'Quotes' && $referenceModule == 'Potentials') {
					$referenceTableName = 'vtiger_potentialRelQuotes';
				} elseif ($moduleName == 'Quotes' && $referenceModule == 'Accounts') {
					$referenceTableName = 'vtiger_accountQuotes';
				} elseif ($moduleName == 'Quotes' && $referenceModule == 'Contacts') {
					$referenceTableName = 'vtiger_contactdetailsQuotes';
				} elseif ($moduleName == 'SalesOrder' && $referenceModule == 'Potentials') {
					$referenceTableName = 'vtiger_potentialRelSalesOrder';
				} elseif ($moduleName == 'SalesOrder' && $referenceModule == 'Accounts') {
					$referenceTableName = 'vtiger_accountSalesOrder';
				} elseif ($moduleName == 'SalesOrder' && $referenceModule == 'Contacts') {
					$referenceTableName = 'vtiger_contactdetailsSalesOrder';
				} elseif ($moduleName == 'SalesOrder' && $referenceModule == 'Quotes') {
					$referenceTableName = 'vtiger_quotesSalesOrder';
				} elseif (in_array($referenceModule, $reportSecondaryModules) and $moduleName != 'Timecontrol') {
					if($fieldInstance->getFieldId() != '') $referenceTableName = "{$entityTableName}Rel{$moduleName}{$fieldInstance->getFieldId()}";
					else $referenceTableName = "{$entityTableName}Rel$referenceModule";
				} elseif (in_array($moduleName, $reportSecondaryModules) and $moduleName != 'Timecontrol') {
					$referenceTableName = "{$entityTableName}Rel$moduleName";
				} else {
					$referenceTableName = "{$entityTableName}Rel{$moduleName}{$fieldInstance->getFieldId()}";
				}

				$columnList = array();
				if(is_array($entityFieldNames)) {
					foreach ($entityFieldNames as $entityColumnName) {
						$columnList["$entityColumnName"] = "$referenceTableName.$entityColumnName";
					}
				} else {
					$columnList[] = "$referenceTableName.$entityFieldNames";
				}
				if(count($columnList) > 1) {
					$columnSql = getSqlForNameInDisplayFormat($columnList, $referenceModule);
				} else {
					$columnSql = implode('', $columnList);
				}
				if ($referenceModule == 'DocumentFolders' && $fieldInstance->getFieldName() == 'folderid') {
					$columnSql = 'vtiger_attachmentsfolder.foldername';
				}
				if ($referenceModule == 'Currency' && $fieldInstance->getFieldName() == 'currency_id') {
					$columnSql = "vtiger_currency_info$moduleName.currency_name";
				}
				$columnsSqlList[] = $columnSql;
			}
		}
		return $columnsSqlList;
	}
}
?>
