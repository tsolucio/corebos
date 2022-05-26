<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

global $app_strings,$mod_strings, $theme, $log;

$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';
require_once 'include/database/PearDatabase.php';
require_once 'data/CRMEntity.php';
require_once 'modules/Reports/Reports.php';
require_once 'modules/Reports/ReportUtils.php';
require_once 'modules/Reports/ReportRunQueryPlanner.php';
require_once 'vtlib/Vtiger/Module.php';
include_once 'include/fields/InventoryLineField.php';

class ReportRun extends CRMEntity {

	public $primarymodule;
	public $secondarymodule;
	public $orderbylistsql;
	public $orderbylistcolumns;

	public $selectcolumns;
	public $groupbylist;
	public $reporttype;
	public $cbreporttype;
	public $reportname;
	public $totallist;
	public $number_of_rows;
	public $page = 1;
	public $islastpage = false;

	public $_groupinglist  = false;
	public $_columnslist   = array();
	public $_stdfilterlist = false;
	public $_columnstotallist = false;
	public $_columnstotallistaddtoselect = false;
	public $_advfiltersql = false;

	public $append_currency_symbol_to_value = array('Products_Unit_Price','Services_Price',
						'Invoice_Total', 'Invoice_Sub_Total', 'Invoice_S&H_Amount', 'Invoice_Discount_Amount', 'Invoice_Adjustment',
						'Quotes_Total', 'Quotes_Sub_Total', 'Quotes_S&H_Amount', 'Quotes_Discount_Amount', 'Quotes_Adjustment',
						'SalesOrder_Total', 'SalesOrder_Sub_Total', 'SalesOrder_S&H_Amount', 'SalesOrder_Discount_Amount', 'SalesOrder_Adjustment',
						'PurchaseOrder_Total', 'PurchaseOrder_Sub_Total', 'PurchaseOrder_S&H_Amount', 'PurchaseOrder_Discount_Amount', 'PurchaseOrder_Adjustment',
						'Issuecards_Total', 'Issuecards_Sub_Total', 'Issuecards_S&H_Amount', 'Issuecards_Discount_Amount', 'Issuecards_Adjustment','Invoice_SandH_Amount',
						'Quotes_SandH_Amount','SalesOrder_SandH_Amount','PurchaseOrder_SandH_Amount','Issuecards_SandH_Amount',
						);
	public $ui10_fields = array();
	public $ui101_fields = array();
	public $groupByTimeParent = array(
		'Quarter'=>array('Year'),
		'Month'=>array('Year'),
		'Day'=>array('Year','Month')
	);
	public $queryPlanner = null;
	public $_tmptablesinitialized = false;

	/** Function to set reportid,primarymodule,secondarymodule,reporttype,reportname, for given reportid
	 *  This function accepts the $reportid as argument
	 *  It sets reportid,primarymodule,secondarymodule,reporttype,reportname for the given reportid
	 */
	public function __construct($reportid) {
		$oReport = new Reports($reportid);
		$this->reportid = $reportid;
		$this->primarymodule = $oReport->primodule;
		$this->secondarymodule = $oReport->secmodule;
		$this->reporttype = $oReport->reporttype;
		$this->cbreporttype = $oReport->cbreporttype;
		$this->reportname = $oReport->reportname;
		$this->queryPlanner = new ReportRunQueryPlanner();
		$this->queryPlanner->reportRun = $this;
	}

	public function getReportName($nospaces = false, $i18n = false) {
		$rep = ($i18n ? getTranslatedString($this->reportname, 'Reports') : $this->reportname);
		return ($nospaces ? str_replace(' ', '_', $rep) : $rep);
	}

	/** Function to get the columns for the reportid
	 *  This function accepts the $reportid and $outputformat (optional)
	 *  This function returns  $columnslist Array: $tablename:$columnname:$fieldlabel:$fieldname:$typeofdata=>$tablename.$columnname As Header value,
	 *					$tablename1:$columnname1:$fieldlabel1:$fieldname1:$typeofdata1=>$tablename1.$columnname1 As Header value,
	 *					|
	 *					$tablenamen:$columnnamen:$fieldlabeln:$fieldnamen:$typeofdatan=>$tablenamen.$columnnamen As Header value
	 */
	public function getQueryColumnsList($reportid, $outputformat = '') {
		// Have we initialized information already?
		if (!empty($this->_columnslist[$outputformat])) {
			return $this->_columnslist[$outputformat];
		}

		global $adb, $log,$current_user,$current_language;
		$ssql = 'select vtiger_selectcolumn.* from vtiger_report inner join vtiger_selectquery on vtiger_selectquery.queryid = vtiger_report.queryid';
		$ssql .= ' left join vtiger_selectcolumn on vtiger_selectcolumn.queryid = vtiger_selectquery.queryid';
		$ssql .= ' where vtiger_report.reportid = ?';
		$ssql .= ' order by vtiger_selectcolumn.columnindex';
		$result = $adb->pquery($ssql, array($reportid));
		$permitted_fields = array();
		$columnslist = array();
		$userprivs = $current_user->getPrivileges();
		$hasGlobalReadPermission = $userprivs->hasGlobalReadPermission();
		while ($columnslistrow = $adb->fetch_array($result)) {
			$fieldname = '';
			$fieldcolname = decode_html($columnslistrow['columnname']);
			if (strpos($fieldcolname, ':')===false) {
				continue;
			}
			list($tablename, $colname, $module_field, $fieldname, $single) = explode(':', $fieldcolname);
			$module_field = decode_html($module_field);
			list($module, $field) = explode('_', $module_field, 2);
			$fielduitype = getUItype($module, $colname);
			$inventory_fields = array('quantity', 'listprice', 'serviceid', 'productid', 'discount', 'comment');
			$inventory_modules = getInventoryModules();
			if ((!isset($permitted_fields[$module]) || count($permitted_fields[$module]) == 0) && !$userprivs->hasGlobalReadPermission()) {
				$permitted_fields[$module] = $this->getaccesfield($module);
			}
			if (in_array($module, $inventory_modules) && isset($permitted_fields[$module]) && is_array($permitted_fields[$module])) {
				$permitted_fields[$module] = array_merge($permitted_fields[$module], $inventory_fields);
			}
			$selectedfields = explode(':', $fieldcolname);
			if (!$hasGlobalReadPermission && !in_array($selectedfields[3], $permitted_fields[$module])) {
				//user has no access to this field, skip it.
				continue;
			}
			$querycolumns = $this->getEscapedColumns($selectedfields);

			$targetTableName = $tablename;
			$fieldlabel = trim(preg_replace("/$module/", ' ', $selectedfields[2], 1));
			$mod_arr=explode('_', $fieldlabel);
			$fieldlabel = trim(str_replace('_', ' ', $fieldlabel));
			//modified code to support i18n issue
			$fld_arr = explode(' ', $fieldlabel);
			if ($mod_arr[0] == '') {
				$mod = $module;
				$mod_lbl = getTranslatedString($module, $module); //module
			} else {
				$mod = $mod_arr[0];
				array_shift($fld_arr);
				$mod_lbl = getTranslatedString($fld_arr[0], $mod); //module
			}
			$fld_lbl_str = implode(' ', $fld_arr);
			$fld_lbl = getTranslatedString($fld_lbl_str, $module); //fieldlabel
			$fieldlabel = $mod_lbl.' '.$fld_lbl;
			if (($selectedfields[0] == 'vtiger_usersRel1') && ($selectedfields[1] == 'user_name') && ($selectedfields[2] == 'Quotes_Inventory_Manager')) {
				$columnslist[$fieldcolname] = "trim( $selectedfields[0].ename ) as ".$module.'_Inventory_Manager';
				$this->queryPlanner->addTable($selectedfields[0]);
				continue;
			}

			if (!((CheckFieldPermission($fieldname, $mod) != 'true' && $colname!='crmid' && (!in_array($fieldname, $inventory_fields) && in_array($module, $inventory_modules))) || empty($fieldname))) {
				$header_label = $selectedfields[2]; // Header label to be displayed in the reports table
				// check if the field in the report is a custom field. If it is, get the label from the vtiger_field as it would have been changed.
				if ($querycolumns == '') {
					if ($selectedfields[4] == 'C') {
						$field_label_data = explode('_', $selectedfields[2]);
						$module= $field_label_data[0];
						if ($module!=$this->primarymodule) {
							$columnslist[$fieldcolname] = 'case when ('.$selectedfields[0].'.'.$selectedfields[1]."='1')
								then '".getTranslatedString('LBL_YES')."' else case when (vtiger_crmentity$module.crmid !='') then '".getTranslatedString('LBL_NO')
								."' else '-' end end as '$selectedfields[2]'";
							$this->queryPlanner->addTable("vtiger_crmentity$module");
						} else {
							$columnslist[$fieldcolname] = 'case when ('.$selectedfields[0].'.'.$selectedfields[1]."='1')
								then '".getTranslatedString('LBL_YES')."' else case when (vtiger_crmentity.crmid !='') then '".getTranslatedString('LBL_NO')
								."' else '-' end end as '$selectedfields[2]'";
							$this->queryPlanner->addTable($selectedfields[0]);
						}
					} elseif ($selectedfields[0] == 'vtiger_activity' && $selectedfields[1] == 'status') {
						$columnslist[$fieldcolname] = " case when (vtiger_activity.status not like '') then vtiger_activity.status else vtiger_activity.eventstatus end as cbCalendar_Status";
						$this->queryPlanner->addTable($selectedfields[0]);
					} elseif ($selectedfields[0] == 'vtiger_activity' && $selectedfields[1] == 'date_start') {
						$columnslist[$fieldcolname] = "cast(concat(vtiger_activity.date_start,'  ',vtiger_activity.time_start) as DATETIME) as cbCalendar_Start_Date_and_Time";
						$this->queryPlanner->addTable($selectedfields[0]);
					} elseif (stristr($selectedfields[0], 'vtiger_users') && ($selectedfields[1] == 'user_name')) {
						$temp_module_from_tablename = str_replace('vtiger_users', '', $selectedfields[0]);
						if ($module!=$this->primarymodule) {
							$condition = 'and vtiger_crmentity'.$module.".crmid!=''";
							$this->queryPlanner->addTable("vtiger_crmentity$module");
						} else {
							$condition = "and vtiger_crmentity.crmid!=''";
						}
						if ($temp_module_from_tablename == $module) {
							$columnslist[$fieldcolname] = ' case when('.$selectedfields[0].".last_name NOT LIKE '' $condition )
								THEN ".$selectedfields[0].'.ename else vtiger_groups'.$module.".groupname end as '".$module."_$field'";
							$this->queryPlanner->addTable('vtiger_groups' . $module); // Auto-include the dependent module table.
						} else { //Some Fields can't assigned to groups so case avoided (fields like inventory manager)
							$columnslist[$fieldcolname] = $selectedfields[0].".user_name as '".$header_label."'";
						}
						$this->queryPlanner->addTable($selectedfields[0]);
					} elseif (stristr($selectedfields[0], 'vtiger_crmentity') && ($selectedfields[1] == 'modifiedby')) {
						$targetTableName = 'vtiger_lastModifiedBy' . $module;
						$columnslist[$fieldcolname] = "trim($targetTableName.ename) as $header_label";
						$this->queryPlanner->addTable("vtiger_crmentity$module");
						$this->queryPlanner->addTable($targetTableName);
						// Added when no fields from the secondary module are selected but lastmodifiedby field is selected
						$moduleInstance = CRMEntity::getInstance($module);
						$this->queryPlanner->addTable($moduleInstance->table_name);
					} elseif (stristr($selectedfields[0], 'vtiger_crmentity') && ($selectedfields[1] == 'smcreatorid')) {
						$targetTableName = 'vtiger_CreatedBy' . $module;
						$columnslist[$fieldcolname] = "trim($targetTableName.ename) as $header_label";
						$this->queryPlanner->addTable("vtiger_crmentity$module");
						$this->queryPlanner->addTable($targetTableName);
						// Added when no fields from the secondary module is selected but creator field is selected
						$moduleInstance = CRMEntity::getInstance($module);
						$this->queryPlanner->addTable($moduleInstance->table_name);
					} elseif ($selectedfields[0] == 'vtiger_crmentity'.$this->primarymodule) {
						$columnslist[$fieldcolname] = 'vtiger_crmentity.'.$selectedfields[1]." AS '".$header_label."'";
					} elseif ($selectedfields[0] == 'vtiger_products' && $selectedfields[1] == 'unit_price') {//handled for product fields in Campaigns Module Reports
						$columnslist[$fieldcolname] = 'concat('.$selectedfields[0].".currency_id,'::',innerProduct.actual_unit_price) as '". $header_label ."'";
						$this->queryPlanner->addTable('innerProduct');
					} elseif ($fielduitype == '72' || in_array($selectedfields[2], $this->append_currency_symbol_to_value)) {
						$columnslist[$fieldcolname] = 'concat('.$selectedfields[0].".currency_id,'::',".$selectedfields[0].'.'.$selectedfields[1].") as '" . $header_label ."'";
					} elseif ($selectedfields[0] == 'vtiger_notes' && ($selectedfields[1] == 'filelocationtype' || $selectedfields[1] == 'filesize' || $selectedfields[1] == 'folderid' || $selectedfields[1]=='filestatus')) {//handled for product fields in Campaigns Module Reports
						if ($selectedfields[1] == 'filelocationtype') {
							$columnslist[$fieldcolname] = 'case '.$selectedfields[0].'.'.$selectedfields[1]
								." when 'I' then 'Internal' when 'E' then 'External' else '-' end as '$selectedfields[2]'";
						} elseif ($selectedfields[1] == 'folderid') {
							$columnslist[$fieldcolname] = "vtiger_attachmentsfolder.foldername as '$selectedfields[2]'";
							$this->queryPlanner->addTable('vtiger_attachmentsfolder');
						} elseif ($selectedfields[1] == 'filestatus') {
							$columnslist[$fieldcolname] = 'case '.$selectedfields[0].'.'.$selectedfields[1]
								." when '1' then '".getTranslatedString('LBL_YES')
								."' when '0' then '".getTranslatedString('LBL_NO')."' else '-' end as '$selectedfields[2]'";
						} elseif ($selectedfields[1] == 'filesize') {
							$columnslist[$fieldcolname] = 'case '.$selectedfields[0].'.'.$selectedfields[1]
								." when '' then '-' else concat(".$selectedfields[0].'.'.$selectedfields[1]."/1024,'  ','KB') end as '$selectedfields[2]'";
						}
					} elseif ($selectedfields[0] == 'vtiger_inventoryproductrel' || $selectedfields[0] == 'vtiger_inventoryproductrel'.$module) {
						if ($outputformat !== 'COLUMNSTOTOTAL') {
							if ($selectedfields[1] == 'discount') {
								$columnslist[$fieldcolname] = " case
									when (vtiger_inventoryproductrel{$module}.discount_amount != '')
									then vtiger_inventoryproductrel{$module}.discount_amount
									else ROUND((vtiger_inventoryproductrel{$module}.listprice * vtiger_inventoryproductrel{$module}.quantity * (vtiger_inventoryproductrel{$module}.discount_percent/100)),3) end as '" . $header_label ."'";
							} elseif ($selectedfields[1] == 'productid') {
								$columnslist[$fieldcolname] = "vtiger_products{$module}.productname as '" . $header_label ."'";
								$this->queryPlanner->addTable("vtiger_products{$module}");
							} elseif ($selectedfields[1] == 'serviceid') {
								$columnslist[$fieldcolname] = "vtiger_service{$module}.servicename as '" . $header_label ."'";
								$this->queryPlanner->addTable("vtiger_service{$module}");
							} else {
								$columnslist[$fieldcolname] = 'vtiger_inventoryproductrel'.$module.'.'.$selectedfields[1]." as '".$header_label."'";
							}
							$this->queryPlanner->addTable('vtiger_inventoryproductrel'.$module);
						}
					} elseif (stristr($selectedfields[1], 'cf_') && stripos($selectedfields[1], 'cf_')==0) {
						$columnslist[$fieldcolname] = $selectedfields[0].'.'.$selectedfields[1]." AS '".$adb->sql_escape_string(decode_html($header_label))."'";
						$this->queryPlanner->addTable($selectedfields[0]);
					} else {
						$columnslist[$fieldcolname] = $selectedfields[0].'.'.$selectedfields[1]." AS '".$header_label."'";
						$this->queryPlanner->addTable($selectedfields[0]);
					}
				} else {
					$columnslist[$fieldcolname] = $querycolumns;
				}
				$this->queryPlanner->addTable($targetTableName);
			}
		}
		$columnslist['vtiger_crmentity:crmid:LBL_ACTION:crmid:I'] = 'vtiger_crmentity.crmid AS "LBL_ACTION"' ;
		// Save the information
		$this->_columnslist[$outputformat] = $columnslist;

		$log->debug('ReportRun :: getQueryColumnsList'.$reportid);
		return $columnslist;
	}

	/** Function to get field columns based on profile
	 * @param string module
	 * @return array permitted fields
	 */
	public function getaccesfield($module) {
		global $adb;
		$access_fields = array();

		$profileList = getCurrentUserProfileList();
		$query = 'select vtiger_field.fieldname
			from vtiger_field
			inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid
			inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid
			where';
		$params = array();
		$params[] = $module;
		if (count($profileList) > 0) {
			$query .= ' vtiger_field.tabid in (select tabid from vtiger_tab where vtiger_tab.name in (?)) and vtiger_field.displaytype in (1,2,3,4,5)
				and vtiger_profile2field.visible=0 and vtiger_field.presence IN (0,2) and vtiger_def_org_field.visible=0
				and vtiger_profile2field.profileid in ('. generateQuestionMarks($profileList).') group by vtiger_field.fieldid order by block,sequence';
			$params[] = $profileList;
		} else {
			$query .= ' vtiger_field.tabid in (select tabid from vtiger_tab where vtiger_tab.name in (?)) and vtiger_field.displaytype in (1,2,3,4,5)
				and vtiger_profile2field.visible=0 and vtiger_field.presence IN (0,2) and vtiger_def_org_field.visible=0 group by vtiger_field.fieldid
				order by block,sequence';
		}
		$result = $adb->pquery($query, $params);

		while ($collistrow = $adb->fetch_array($result)) {
			$access_fields[] = $collistrow['fieldname'];
		}
		//added to include ticketid for Reports module in select columnlist for all users
		if ($module == 'HelpDesk') {
			$access_fields[] = 'ticketid';
		}
		return $access_fields;
	}

	/** Function to get Escapedcolumns for the field in case of multiple parents
	 * @param array of selected fields
	 * @return string the case query for the escaped columns
	 */
	public function getEscapedColumns($selectedfields) {
		$tableName = $selectedfields[0];
		$columnName = $selectedfields[1];
		$moduleFieldLabel = $selectedfields[2];
		$fieldName = $selectedfields[3];
		list($moduleName, $fieldLabel) = explode('_', $moduleFieldLabel, 2);
		$fieldInfo = getFieldByReportLabel($moduleName, $fieldLabel);
		if (is_null($fieldInfo)) {
			return '';
		}
		$queryColumn = '';
		if ($moduleName == 'ModComments' && $fieldName == 'creator') {
			$queryColumn = "trim(case when (vtiger_usersModComments.user_name not like '' and vtiger_crmentity.crmid!='') then vtiger_usersModComments.ename end) as 'ModComments_Creator'";
			$this->queryPlanner->addTable('vtiger_usersModComments');
		} elseif (($fieldInfo['uitype'] == '10' || isReferenceUIType($fieldInfo['uitype'])) && $fieldInfo['uitype'] != '52' && $fieldInfo['uitype'] != '53') {
			$fieldSqlColumns = $this->getReferenceFieldColumnList($moduleName, $fieldInfo);
			if (count($fieldSqlColumns) > 0) {
				$queryColumn = "(CASE WHEN $tableName.$columnName NOT LIKE '' THEN (CASE";
				foreach ($fieldSqlColumns as $columnSql) {
					$queryColumn .= " WHEN $columnSql NOT LIKE '' THEN $columnSql";
				}
				$queryColumn .= " ELSE '' END) ELSE '' END) AS $moduleFieldLabel";
				$this->queryPlanner->addTable($tableName);
			}
		}
		return $queryColumn;
	}

	/** Function to get selected columns for the given report
	 * @param integer report id
	 * @return string query of columnlist for the selected columns
	 */
	public function getSelectedColumnsList($reportid) {
		global $adb, $log;

		$scsql = 'select vtiger_selectcolumn.* from vtiger_report inner join vtiger_selectquery on vtiger_selectquery.queryid = vtiger_report.queryid';
		$scsql .= ' left join vtiger_selectcolumn on vtiger_selectcolumn.queryid = vtiger_selectquery.queryid where vtiger_report.reportid=?';
		$scsql .= ' order by vtiger_selectcolumn.columnindex';

		$result = $adb->pquery($scsql, array($reportid));
		$noofrows = $adb->num_rows($result);
		$sSQL = '';
		if ($this->orderbylistsql != '') {
			$sSQL .= $this->orderbylistsql.', ';
		}
		$sSQLList = array();
		for ($i=0; $i<$noofrows; $i++) {
			$fieldcolname = $adb->query_result($result, $i, 'columnname');
			$ordercolumnsequal = true;
			if ($fieldcolname != '') {
				for ($j=0; $j<count($this->orderbylistcolumns); $j++) {
					if ($this->orderbylistcolumns[$j] == $fieldcolname) {
						$ordercolumnsequal = false;
						break;
					} else {
						$ordercolumnsequal = true;
					}
				}
				if ($ordercolumnsequal) {
					$selectedfields = explode(':', $fieldcolname);
					if ($selectedfields[0] == 'vtiger_crmentity'.$this->primarymodule) {
						$selectedfields[0] = 'vtiger_crmentity';
					}
					$sSQLList[] = $selectedfields[0].'.'.$selectedfields[1]." '".$selectedfields[2]."'";
				}
			}
		}
		$sSQL .= implode(',', $sSQLList);

		$log->debug('ReportRun :: getSelectedColumnsList'.$reportid);
		return $sSQL;
	}

	/** Function to get advanced comparator in query form for the given Comparator and value
	 * @param string comparator
	 * @param string value
	 * @return string the check query for the comparator
	 */
	public function getAdvComparator($comparator, $value, $datatype = '') {
		global $log,$adb,$default_charset;
		$value=html_entity_decode(trim($value), ENT_QUOTES, $default_charset);
		$value_len = strlen($value);
		$is_field = false;
		if ($value_len > 1 && $value[0]=='$' && $value[$value_len-1]=='$') {
			$temp = str_replace('$', '', $value);
			$is_field = true;
		}
		if ($datatype=='C') {
			$value = str_replace('yes', '1', str_replace('no', '0', $value));
		}
		$rtvalue = ' = ';
		if ($is_field) {
			$value = $this->getFilterComparedField($temp);
		}
		if ($comparator == 'e') {
			if (trim($value) == 'NULL') {
				$rtvalue = ' is NULL';
			} elseif (trim($value) != '') {
				$rtvalue = ' = '.$adb->quote($value);
			} elseif (trim($value) == '' && $datatype == 'V') {
				$rtvalue = ' = '.$adb->quote($value);
			} else {
				$rtvalue = ' is NULL';
			}
		}
		if ($comparator == 'n') {
			if (trim($value) == 'NULL') {
				$rtvalue = ' is NOT NULL';
			} elseif (trim($value) != '') {
				$rtvalue = ' <> '.$adb->quote($value);
			} elseif (trim($value) == '' && $datatype == 'V') {
				$rtvalue = ' <> '.$adb->quote($value);
			} else {
				$rtvalue = ' is NOT NULL';
			}
		}
		if ($comparator == 's') {
			$rtvalue = " like '". formatForSqlLike($value, 2, $is_field) ."'";
		}
		if ($comparator == 'ew') {
			$rtvalue = " like '". formatForSqlLike($value, 1, $is_field) ."'";
		}
		if ($comparator == 'c') {
			$rtvalue = " like '". formatForSqlLike($value, 0, $is_field) ."'";
		}
		if ($comparator == 'k') {
			$rtvalue = " not like '". formatForSqlLike($value, 0, $is_field) ."'";
		}
		if ($comparator == 'l') {
			$rtvalue = ' < '.$adb->quote($value);
		}
		if ($comparator == 'g') {
			$rtvalue = ' > '.$adb->quote($value);
		}
		if ($comparator == 'm') {
			$rtvalue = ' <= '.$adb->quote($value);
		}
		if ($comparator == 'h') {
			$rtvalue = ' >= '.$adb->quote($value);
		}
		if ($comparator == 'b') {
			$rtvalue = ' < '.$adb->quote($value);
		}
		if ($comparator == 'a') {
			$rtvalue = ' > '.$adb->quote($value);
		}
		if ($is_field) {
			$rtvalue = str_replace("'", '', $rtvalue);
			$rtvalue = str_replace("\\", '', $rtvalue);
		}
		$log->debug('ReportRun :: getAdvComparator');
		return $rtvalue;
	}

	/** Function to get field that is to be compared in query form for the given Comparator and field
	 *  @param string field expression
	 *  @return string the value for the comparator
	 */
	public function getFilterComparedField($field) {
		global $adb;
		$field = explode('#', $field);
		$module = $field[0];
		$fieldname = trim($field[1]);
		$tabid = getTabId($module);
		$field_query = $adb->pquery(
			'SELECT tablename,columnname FROM vtiger_field WHERE tabid=? AND fieldname=?',
			array($tabid,$fieldname)
		);
		$fieldtablename = $adb->query_result($field_query, 0, 'tablename');
		$fieldcolname = $adb->query_result($field_query, 0, 'columnname');
		if ($fieldtablename == 'vtiger_crmentity') {
			$fieldtablename = $fieldtablename.$module;
		}
		if ($fieldname == 'assigned_user_id') {
			$fieldtablename = 'vtiger_users'.$module;
			$fieldcolname = 'user_name';
		}
		if ($fieldtablename == 'vtiger_crmentity' && $fieldname == 'modifiedby') {
			$fieldtablename = 'vtiger_lastModifiedBy'.$module;
			$fieldcolname = 'user_name';
		}
		if ($fieldtablename == 'vtiger_crmentity' && $fieldname == 'smcreatorid') {
			$fieldtablename = 'vtiger_CreatedBy'.$module;
			$fieldcolname = 'user_name';
		}
		if ($fieldname == 'assigned_user_id1') {
			$fieldtablename = 'vtiger_usersRel1';
			$fieldcolname = 'user_name';
		}
		$this->queryPlanner->addTable($fieldtablename);
		return $fieldtablename.'.'.$fieldcolname;
	}

	/** Function to get the advanced filter columns for the reportid
	 *  This function accepts the $reportid
	 *  This function returns  $columnslist Array: $columnname => $tablename:$columnname:$fieldlabel:$fieldname:$typeofdata=>$tablename.$columnname filtercriteria,
	 *					$tablename1:$columnname1:$fieldlabel1:$fieldname1:$typeofdata1=>$tablename1.$columnname1 filtercriteria,
	 *					|
	 *					$tablenamen:$columnnamen:$fieldlabeln:$fieldnamen:$typeofdatan=>$tablenamen.$columnnamen filtercriteria
	 */
	public function getAdvFilterList($reportid) {
		global $adb;

		$advft_criteria = array();
		$groupsresult = $adb->pquery('SELECT * FROM vtiger_relcriteria_grouping WHERE queryid = ? ORDER BY groupid', array($reportid));
		$i = 1;
		$j = 0;
		while ($relcriteriagroup = $adb->fetch_array($groupsresult)) {
			$groupId = $relcriteriagroup['groupid'];
			$groupCondition = $relcriteriagroup['group_condition'];

			$ssql = 'select vtiger_relcriteria.*
				from vtiger_report
				inner join vtiger_relcriteria on vtiger_relcriteria.queryid = vtiger_report.queryid
				left join vtiger_relcriteria_grouping on vtiger_relcriteria.queryid = vtiger_relcriteria_grouping.queryid
					and vtiger_relcriteria.groupid = vtiger_relcriteria_grouping.groupid
				where vtiger_report.reportid = ? AND vtiger_relcriteria.groupid = ? order by vtiger_relcriteria.columnindex';

			$result = $adb->pquery($ssql, array($reportid, $groupId));
			$noOfColumns = $adb->num_rows($result);
			if ($noOfColumns <= 0) {
				continue;
			}

			while ($relcriteriarow = $adb->fetch_array($result)) {
				$criteria = array();
				$criteria['columnname'] = html_entity_decode($relcriteriarow['columnname']);
				$criteria['comparator'] = $relcriteriarow['comparator'];
				$advfilterval = $relcriteriarow['value'];
				$col = explode(':', $relcriteriarow['columnname']);
				list($module,$void) = explode('_', $col[2], 2);
				$uitype_value = getUItypeByFieldName($module, $col[3]);

				$criteria['value'] = $advfilterval;
				$criteria['column_condition'] = $relcriteriarow['column_condition'];

				$advft_criteria[$i]['columns'][$j] = $criteria;
				$advft_criteria[$i]['condition'] = $groupCondition;
				$j++;

				$this->queryPlanner->addTable($col[0]);
			}
			if (!empty($advft_criteria[$i]['columns'][$j-1]['column_condition'])) {
				$advft_criteria[$i]['columns'][$j-1]['column_condition'] = '';
			}
			$i++;
		}
		// Clear the condition (and/or) for last group, if any.
		if (!empty($advft_criteria[$i-1]['condition'])) {
			$advft_criteria[$i-1]['condition'] = '';
		}
		return $advft_criteria;
	}

	public function generateAdvFilterSql($advfilterlist) {
		global $current_user;

		$advfiltersql = '';
		$currentUserFullName = getUserFullName($current_user->id);
		foreach ($advfilterlist as $groupinfo) {
			$groupcondition = isset($groupinfo['condition']) ? $groupinfo['condition'] : '';
			$groupcolumns = $groupinfo['columns'];

			if (count($groupcolumns) > 0) {
				$advfiltergroupsql = '';
				foreach ($groupcolumns as $columninfo) {
					$fieldcolname = $columninfo['columnname'];
					$comparator = $columninfo['comparator'];
					$value = $columninfo['value'];
					$columncondition = $columninfo['column_condition'];

					if ($fieldcolname != '' && $comparator != '') {
						$selectedfields = explode(':', $fieldcolname);
						$moduleFieldLabel = $selectedfields[2];
						list($moduleName, $fieldLabel) = explode('_', $moduleFieldLabel, 2);
						$fieldInfo = getFieldByReportLabel($moduleName, $fieldLabel);
						// Added to handle the crmentity table name for Primary module
						if ($selectedfields[0] == 'vtiger_crmentity'.$this->primarymodule) {
							$selectedfields[0] = 'vtiger_crmentity';
						}
						//Added to handle yes or no for checkbox field in reports advance filters. -shahul
						if ($selectedfields[4] == 'C') {
							if (strcasecmp(trim($value), 'yes')==0) {
								$value='1';
							}
							if (strcasecmp(trim($value), 'no')==0) {
								$value='0';
							}
						}
						$valuearray = explode(',', trim($value));
						$datatype = (isset($selectedfields[4])) ? $selectedfields[4] : '';
						$secondarymodules = explode(':', $this->secondarymodule);
						array_walk($secondarymodules, function (&$val) {
							$val = 'vtiger_users'.$val;
						});
						if (isset($valuearray) && count($valuearray) > 1 && $comparator != 'bw') {
							$advcolumnsql = '';
							for ($n=0; $n<count($valuearray); $n++) {
								$valuearray[$n] = trim($valuearray[$n]);
								if (($selectedfields[0] == 'vtiger_users'.$this->primarymodule || in_array($selectedfields[0], $secondarymodules)) && $selectedfields[1] == 'user_name') {
									if ($valuearray[$n]=='current_user') {
										$valuearray[$n] = $currentUserFullName;
									}
									$module_from_tablename = str_replace('vtiger_users', '', $selectedfields[0]);
									$advcolsql[] = " trim($selectedfields[0].ename)".$this->getAdvComparator($comparator, $valuearray[$n], $datatype).' or vtiger_groups'.$module_from_tablename.'.groupname '.$this->getAdvComparator($comparator, $valuearray[$n], $datatype);
								} elseif ($selectedfields[1] == 'status') {//when you use comma seperated values.
									if ($selectedfields[2] == 'cbCalendar_Status') {
										$advcolsql[] = "(case when (vtiger_activity.status not like '') then vtiger_activity.status else vtiger_activity.eventstatus end)".$this->getAdvComparator($comparator, $valuearray[$n], $datatype);
									} elseif ($selectedfields[2] == 'HelpDesk_Status') {
										$advcolsql[] = 'vtiger_troubletickets.status'.$this->getAdvComparator($comparator, $valuearray[$n], $datatype);
									}
								} elseif ($selectedfields[1] == 'description') {//when you use comma seperated values.
									if ($selectedfields[0]=='vtiger_crmentity'.$this->primarymodule) {
										$advcolsql[] = 'vtiger_crmentity.description'.$this->getAdvComparator($comparator, $valuearray[$n], $datatype);
									} else {
										$advcolsql[] = $selectedfields[0].'.'.$selectedfields[1].$this->getAdvComparator($comparator, $valuearray[$n], $datatype);
									}
								} elseif ($selectedfields[2] == 'Quotes_Inventory_Manager') {
									$advcolsql[] = ("trim($selectedfields[0].ename)".$this->getAdvComparator($comparator, $valuearray[$n], $datatype));
								} else {
									$advcolsql[] = $selectedfields[0].'.'.$selectedfields[1].$this->getAdvComparator($comparator, $valuearray[$n], $datatype);
								}
							}
							//If negative logic filter ('not equal to', 'does not contain') is used, 'and' condition should be applied instead of 'or'
							if ($comparator == 'n' || $comparator == 'k') {
								$advcolumnsql = implode(' and ', $advcolsql);
							} else {
								$advcolumnsql = implode(' or ', $advcolsql);
							}
							$fieldvalue = ' ('.$advcolumnsql.') ';
						} elseif (($selectedfields[0] == 'vtiger_users'.$this->primarymodule || in_array($selectedfields[0], $secondarymodules)) && $selectedfields[1] == 'user_name') {
							$value = trim($value);
							if ($value=='current_user') {
								$value = $currentUserFullName;
							}
							$module_from_tablename = str_replace('vtiger_users', '', $selectedfields[0]);
							$fieldvalue = ' trim(case when ('.$selectedfields[0].".last_name NOT LIKE '') then ".$selectedfields[0].'.ename else vtiger_groups'.$module_from_tablename.'.groupname end) '.$this->getAdvComparator($comparator, $value, $datatype);
						} elseif ($comparator == 'bw' && count($valuearray) == 2) {
							if ($selectedfields[0] == 'vtiger_crmentity'.$this->primarymodule) {
								$fieldvalue = '(vtiger_crmentity.'.$selectedfields[1]." between '".trim($valuearray[0])."' and '".trim($valuearray[1])."')";
							} elseif ($selectedfields[2]=='cbCalendar_Start_Date_and_Time') {
								$fieldvalue = "(cast(concat(vtiger_activity.date_start,'  ',vtiger_activity.time_start) as DATETIME) between '".trim($valuearray[0])."' and '".trim($valuearray[1])."')";
							} else {
								$fieldvalue = '('.$selectedfields[0].'.'.$selectedfields[1]." between '".trim($valuearray[0])."' and '".trim($valuearray[1])."')";
							}
						} elseif ($selectedfields[0] == 'vtiger_crmentity'.$this->primarymodule) {
							$fieldvalue = 'vtiger_crmentity.'.$selectedfields[1].' '.$this->getAdvComparator($comparator, trim($value), $datatype);
						} elseif ($selectedfields[2] == 'Quotes_Inventory_Manager') {
							$fieldvalue = ("trim($selectedfields[0].ename)" . $this->getAdvComparator($comparator, trim($value), $datatype));
						} elseif ($selectedfields[1]=='smcreatorid') {
							$module_from_tablename = str_replace('vtiger_crmentity', '', $selectedfields[0]);
							if ($module_from_tablename != '') {
								$tableName = 'vtiger_CreatedBy'.$module_from_tablename;
							} else {
								$tableName = 'vtiger_CreatedBy'.$this->primarymodule;
							}
							$value = trim($value);
							if ($value=='current_user') {
								$value = $currentUserFullName;
							}
							$fieldvalue = $tableName.'.ename'.$this->getAdvComparator($comparator, $value, $datatype);
							$this->queryPlanner->addTable($tableName);
						} elseif ($selectedfields[1]=='modifiedby') {
							$module_from_tablename = str_replace('vtiger_crmentity', '', $selectedfields[0]);
							if ($module_from_tablename != '') {
								$tableName = 'vtiger_lastModifiedBy'.$module_from_tablename;
							} else {
								$tableName = 'vtiger_lastModifiedBy'.$this->primarymodule;
							}
							$value = trim($value);
							if ($value=='current_user') {
								$value = $currentUserFullName;
							}
							$fieldvalue = $tableName.'.ename'.$this->getAdvComparator($comparator, $value, $datatype);
							$this->queryPlanner->addTable($tableName);
						} elseif ($selectedfields[0] == 'vtiger_activity' && $selectedfields[1] == 'status') {
							$fieldvalue = "(case when (vtiger_activity.status not like '') then vtiger_activity.status else vtiger_activity.eventstatus end)".$this->getAdvComparator($comparator, trim($value), $datatype);
						} elseif ($comparator == 'e' && (trim($value) == 'NULL' || trim($value) == '')) {
							$fieldvalue = '('.$selectedfields[0].'.'.$selectedfields[1].' IS NULL OR '.$selectedfields[0].'.'.$selectedfields[1]." = '')";
						} elseif ($comparator == 'e' && $datatype == 'D' && (trim($value) == '--$' || trim($value) == '$')) {
							$fieldvalue = '('.$selectedfields[0].'.'.$selectedfields[1].' IS NULL )';
						} elseif (substr($selectedfields[0], 0, 26) == 'vtiger_inventoryproductrel' && ($selectedfields[1] == 'productid' || $selectedfields[1] == 'serviceid' || $selectedfields[1] == 'discount')) {
							$invmod = (in_array($this->primarymodule, getInventoryModules()) ? $this->primarymodule : $this->secondarymodule);
							if ($selectedfields[1] == 'productid') {
								$fieldvalue = "vtiger_products{$invmod}.productname ".$this->getAdvComparator($comparator, trim($value), $datatype);
								$this->queryPlanner->addTable('vtiger_products'.$invmod);
							} elseif ($selectedfields[1] == 'serviceid') {
								$fieldvalue = "vtiger_service{$invmod}.servicename ".$this->getAdvComparator($comparator, trim($value), $datatype);
								$this->queryPlanner->addTable('vtiger_service'.$invmod);
							} elseif ($selectedfields[1] == 'discount') {
								$fieldvalue = "(vtiger_inventoryproductrel{$invmod}.discount_amount ".$this->getAdvComparator($comparator, trim($value), $datatype)."
									OR ROUND((vtiger_inventoryproductrel{$invmod}.listprice * vtiger_inventoryproductrel{$invmod}.quantity * (vtiger_inventoryproductrel{$invmod}.discount_percent/100)),3) ".$this->getAdvComparator($comparator, trim($value), $datatype).') ';
							}
							$this->queryPlanner->addTable('vtiger_inventoryproductrel'.$invmod);
						} elseif ($fieldInfo['uitype'] == '10' || isReferenceUIType($fieldInfo['uitype'])) {
							$comparatorValue = $this->getAdvComparator($comparator, trim($value), $datatype);
							$fieldSqls = array();
							$fieldSqlColumns = $this->getReferenceFieldColumnList($moduleName, $fieldInfo);
							foreach ($fieldSqlColumns as $columnSql) {
								$fieldSqls[] = $columnSql.$comparatorValue;
							}
							$fieldvalue = ' ('. implode(' OR ', $fieldSqls).') ';
						//Commented, on test works, but is not necessary (for translations on picklist Values)
//						} elseif(isPicklistUIType($fieldInfo['uitype'])) {
//							$cmp = $this->getAdvComparator($comparator,trim($value),$datatype);
//							if(!isValueInPicklist($value,$fieldInfo['fieldname']))
//								$fieldvalue = $fieldInfo['tablename'].'.'.$fieldInfo['columnname'].$cmp;
//							else
//								$fieldvalue = $fieldInfo['tablename'].'.'.$fieldInfo['columnname'].$cmp;
						} else {
							if (($fieldInfo['uitype']==15 || $fieldInfo['uitype']==16) && hasMultiLanguageSupport($selectedfields[3])) {
								$fieldvalue = '('.$selectedfields[0].'.'.$selectedfields[1].' IN (select translation_key from vtiger_cbtranslation
									where locale="'.$current_user->language.'" and forpicklist="'.$moduleName.'::'.$selectedfields[3]
									.'" and i18n '.$this->getAdvComparator($comparator, trim($value), $datatype).')'
									.(in_array($comparator, array('n', 'k')) ? ' AND ' : ' OR ')
									.$selectedfields[0].'.'.$selectedfields[1].$this->getAdvComparator($comparator, trim($value), $datatype).')';
							} else {
								$fieldvalue = $selectedfields[0].'.'.$selectedfields[1].$this->getAdvComparator($comparator, trim($value), $datatype);
							}
						}

						$advfiltergroupsql .= $fieldvalue;
						if (!empty($columncondition)) {
							$advfiltergroupsql .= ' '.$columncondition.' ';
						}
						$this->queryPlanner->addTable($selectedfields[0]);
					}
				}

				if (trim($advfiltergroupsql) != '') {
					$advfiltergroupsql = "( $advfiltergroupsql ) ";
					if (!empty($groupcondition)) {
						$advfiltergroupsql .= ' '. $groupcondition . ' ';
					}
					$advfiltersql .= $advfiltergroupsql;
				}
			}
		}
		if (trim($advfiltersql) != '') {
			$advfiltersql = '('.$advfiltersql.')';
		}

		return $advfiltersql;
	}

	public function getAdvFilterSql($reportid) {
		// Have we initialized information already?
		if ($this->_advfiltersql !== false) {
			return $this->_advfiltersql;
		}
		global $log;

		$advfilterlist = $this->getAdvFilterList($reportid);
		$advfiltersql = $this->generateAdvFilterSql($advfilterlist);

		// Save the information
		$this->_advfiltersql = $advfiltersql;

		$log->debug('ReportRun :: getAdvFilterSql'.$reportid);
		return $advfiltersql;
	}

	/** Function to get the Standard filter columns for the reportid
	 *  This function accepts the $reportid datatype Integer
	 *  This function returns  $stdfilterlist Array: $columnname => $tablename:$columnname:$fieldlabel:$fieldname:$typeofdata=>$tablename.$columnname filtercriteria,
	 *					$tablename1:$columnname1:$fieldlabel1:$fieldname1:$typeofdata1=>$tablename1.$columnname1 filtercriteria,
	 */
	public function getStdFilterList($reportid) {
		// Have we initialized information already?
		if ($this->_stdfilterlist !== false) {
			return $this->_stdfilterlist;
		}

		global $adb, $log;
		$stdfilterlist = array();

		$stdfiltersql = 'select vtiger_reportdatefilter.* from vtiger_report';
		$stdfiltersql .= ' inner join vtiger_reportdatefilter on vtiger_report.reportid = vtiger_reportdatefilter.datefilterid';
		$stdfiltersql .= ' where vtiger_report.reportid = ?';

		$result = $adb->pquery($stdfiltersql, array($reportid));
		$stdfilterrow = $adb->fetch_array($result);
		if (isset($stdfilterrow)) {
			$fieldcolname = $stdfilterrow['datecolumnname'];
			$datefilter = $stdfilterrow['datefilter'];
			$startdate = $stdfilterrow['startdate'];
			$enddate = $stdfilterrow['enddate'];

			if ($fieldcolname != 'none') {
				$selectedfields = explode(':', $fieldcolname);
				if ($selectedfields[0] == 'vtiger_crmentity'.$this->primarymodule) {
					$selectedfields[0] = 'vtiger_crmentity';
				}

				$moduleFieldLabel = $selectedfields[3];
				list($moduleName, $fieldLabel) = explode('_', $moduleFieldLabel, 2);
				$fieldInfo = getFieldByReportLabel($moduleName, $fieldLabel);
				$typeOfData = $fieldInfo['typeofdata'];
				list($type, $typeOtherInfo) = explode('~', $typeOfData, 2);

				if ($datefilter != 'custom') {
					$startenddate = $this->getStandarFiltersStartAndEndDate($datefilter);
					$startdate = $startenddate[0];
					$enddate = $startenddate[1];
				}

				if ($startdate != '0000-00-00' && $enddate != '0000-00-00' && $startdate != '' && $enddate != ''
						&& $selectedfields[0] != '' && $selectedfields[1] != '') {
					$startDateTime = new DateTimeField($startdate.' '. date('H:i:s'));
					$userStartDate = $startDateTime->getDisplayDate();
					if ($type == 'DT') {
						$userStartDate = $userStartDate.' 00:00:00';
					}
					$startDateTime = getValidDBInsertDateTimeValue($userStartDate);

					$endDateTime = new DateTimeField($enddate.' '. date('H:i:s'));
					$userEndDate = $endDateTime->getDisplayDate();
					if ($type == 'DT') {
						$userEndDate = $userEndDate.' 23:59:00';
					}
					$endDateTime = getValidDBInsertDateTimeValue($userEndDate);

					if ($selectedfields[1] == 'birthday') {
						$tableColumnSql = 'DATE_FORMAT('.$selectedfields[0].'.'.$selectedfields[1].", '%m%d')";
						$startDateTime = "DATE_FORMAT('$startDateTime', '%m%d')";
						$endDateTime = "DATE_FORMAT('$endDateTime', '%m%d')";
					} else {
						if ($selectedfields[0] == 'vtiger_activity' && ($selectedfields[1] == 'date_start' || $selectedfields[1] == 'due_date')) {
							$tableColumnSql = '';
							if ($selectedfields[1] == 'date_start') {
								$tableColumnSql = "CAST((CONCAT(date_start,' ',time_start)) AS DATETIME)";
							} else {
								$tableColumnSql = "CAST((CONCAT(due_date,' ',time_end)) AS DATETIME)";
							}
						} else {
							$tableColumnSql = $selectedfields[0].'.'.$selectedfields[1];
						}
						$startDateTime = "'$startDateTime'";
						$endDateTime = "'$endDateTime'";
					}

					$stdfilterlist[$fieldcolname] = $tableColumnSql.' between '.$startDateTime.' and '.$endDateTime;
					$this->queryPlanner->addTable($selectedfields[0]);
				}
			}
		}
		// Save the information
		$this->_stdfilterlist = $stdfilterlist;

		$log->debug('ReportRun :: getStdFilterList'.$reportid);
		return $stdfilterlist;
	}

	/** Function to get the RunTime filter columns for the given $filtercolumn,$filter,$startdate,$enddate
	 * @param string filter column
	 * @param string filter
	 * @param string start date
	 * @param string end date
	 * @return array ($columnname => $tablename:$columnname:$fieldlabel=>$tablename.$columnname 'between' $startdate 'and' $enddate)
	 */
	public function RunTimeFilter($filtercolumn, $filter, $startdate, $enddate) {
		$stdfilterlist = array();
		if ($filtercolumn != 'none') {
			$selectedfields = explode(':', $filtercolumn);
			if ($selectedfields[0] == 'vtiger_crmentity'.$this->primarymodule) {
				$selectedfields[0] = 'vtiger_crmentity';
			}
			if ($filter == 'custom') {
				if ($startdate!='0000-00-00' && $enddate != '0000-00-00' && $startdate != '' && $enddate != '' && $selectedfields[0] != '' && $selectedfields[1] != '') {
					$stdfilterlist[$filtercolumn] = $selectedfields[0].'.'.$selectedfields[1]." between '".$startdate." 00:00:00' and '".$enddate." 23:59:00'";
				}
			} else {
				if ($startdate != '' && $enddate != '') {
					$startenddate = $this->getStandarFiltersStartAndEndDate($filter);
					if ($startenddate[0] != '' && $startenddate[1] != '' && $selectedfields[0] != '' && $selectedfields[1] != '') {
						$stdfilterlist[$filtercolumn] = $selectedfields[0].'.'.$selectedfields[1]." between '".$startenddate[0]." 00:00:00' and '".$startenddate[1]." 23:59:00'";
					}
				}
			}
			$this->queryPlanner->addTable($selectedfields[0]);
		}
		return $stdfilterlist;
	}

	/** Function to get the RunTime Advanced filter conditions
	 * @param array $advft_criteria
	 * @param array $advft_criteria_groups
	 * @return string $advfiltersql
	 */
	public function RunTimeAdvFilter($advft_criteria, $advft_criteria_groups) {
		$adb = PearDatabase::getInstance();

		$advfilterlist = array();
		$advfiltersql = null;
		if (!empty($advft_criteria)) {
			foreach ($advft_criteria as $column_condition) {
				if (empty($column_condition)) {
					continue;
				}

				$adv_filter_column = $column_condition['columnname'];
				$adv_filter_comparator = $column_condition['comparator'];
				$adv_filter_value = $column_condition['value'];
				$adv_filter_column_condition = $column_condition['columncondition'];
				$adv_filter_groupid = $column_condition['groupid'];

				$column_info = explode(':', $adv_filter_column);

				$moduleFieldLabel = $column_info[2];
				$fieldName = $column_info[3];
				list($module, $fieldLabel) = explode('_', $moduleFieldLabel, 2);
				$fieldInfo = getFieldByReportLabel($module, $fieldLabel);
				$fieldType = null;
				if (!empty($fieldInfo)) {
					$field = WebserviceField::fromArray($adb, $fieldInfo);
					$fieldType = $field->getFieldDataType();
				}

				if (($fieldType == 'currency' || $fieldType == 'double') && (substr($adv_filter_value, 0, 1) != '$' && substr($adv_filter_value, -1, 1) != '$')) {
					$flduitype = $fieldInfo['uitype'];
					if ($flduitype == '72' || $flduitype == 9 || $flduitype ==7) {
						$adv_filter_value = CurrencyField::convertToDBFormat($adv_filter_value, null, true);
					} else {
						$adv_filter_value = CurrencyField::convertToDBFormat($adv_filter_value);
					}
				}

				$temp_val = explode(',', $adv_filter_value);
				if (($column_info[4] == 'D' || ($column_info[4] == 'T' && $column_info[1] != 'time_start' && $column_info[1] != 'time_end') || ($column_info[4] == 'DT'))
					&& ($column_info[4] != '' && $adv_filter_value != '' )
				) {
					$val = array();
					for ($x=0; $x<count($temp_val); $x++) {
						if ($column_info[4] == 'D') {
							$date = new DateTimeField(trim($temp_val[$x]));
							$val[$x] = $date->getDBInsertDateValue();
						} elseif ($column_info[4] == 'DT') {
							$date = new DateTimeField(trim($temp_val[$x]));
							$val[$x] = $date->getDBInsertDateTimeValue();
						} else {
							$date = new DateTimeField(trim($temp_val[$x]));
							$val[$x] = $date->getDBInsertTimeValue();
						}
					}
					$adv_filter_value = implode(',', $val);
				}

				$criteria = array();
				$criteria['columnname'] = $adv_filter_column;
				$criteria['comparator'] = $adv_filter_comparator;
				$criteria['value'] = $adv_filter_value;
				$criteria['column_condition'] = $adv_filter_column_condition;

				$advfilterlist[$adv_filter_groupid]['columns'][] = $criteria;
			}

			if (is_array($advft_criteria_groups)) {
				foreach ($advft_criteria_groups as $group_index => $group_condition_info) {
					if (empty($group_condition_info)) {
						continue;
					}
					if (empty($advfilterlist[$group_index])) {
						continue;
					}
					$advfilterlist[$group_index]['condition'] = $group_condition_info['groupcondition'];
					$noOfGroupColumns = count($advfilterlist[$group_index]['columns']);
					if (!empty($advfilterlist[$group_index]['columns'][$noOfGroupColumns-1]['column_condition'])) {
						$advfilterlist[$group_index]['columns'][$noOfGroupColumns-1]['column_condition'] = '';
					}
				}
			}
			$noOfGroups = count($advfilterlist);
			if (!empty($advfilterlist[$noOfGroups]['condition'])) {
				$advfilterlist[$noOfGroups]['condition'] = '';
			}

			$advfiltersql = $this->generateAdvFilterSql($advfilterlist);
		}
		return $advfiltersql;
	}

	/** Function to get standardfilter for the given reportid
	 * @param integer report id
	 * @return string the query of columnlist for the selected columns
	 */
	public function getStandardCriterialSql($reportid) {
		global $adb, $log;

		$sreportstdfiltersql = 'select vtiger_reportdatefilter.* from vtiger_report';
		$sreportstdfiltersql .= ' inner join vtiger_reportdatefilter on vtiger_report.reportid = vtiger_reportdatefilter.datefilterid';
		$sreportstdfiltersql .= ' where vtiger_report.reportid = ?';

		$result = $adb->pquery($sreportstdfiltersql, array($reportid));
		$noofrows = $adb->num_rows($result);
		$sSQL = '';
		for ($i=0; $i<$noofrows; $i++) {
			$fieldcolname = $adb->query_result($result, $i, 'datecolumnname');
			$datefilter = $adb->query_result($result, $i, 'datefilter');
			$startdate = $adb->query_result($result, $i, 'startdate');
			$enddate = $adb->query_result($result, $i, 'enddate');

			if ($fieldcolname != 'none') {
				$selectedfields = explode(':', $fieldcolname);
				if ($selectedfields[0] == 'vtiger_crmentity'.$this->primarymodule) {
					$selectedfields[0] = 'vtiger_crmentity';
				}
				if ($datefilter == 'custom') {
					if ($startdate!='0000-00-00' && $enddate!='0000-00-00' && $selectedfields[0]!='' && $selectedfields[1] != '' && $startdate != '' && $enddate != '') {
						$startDateTime = new DateTimeField($startdate.' '. date('H:i:s'));
						$startdate = $startDateTime->getDisplayDate();
						$endDateTime = new DateTimeField($enddate.' '. date('H:i:s'));
						$enddate = $endDateTime->getDisplayDate();

						$sSQL .= $selectedfields[0].'.'.$selectedfields[1]." between '".$startdate."' and '".$enddate."'";
					}
				} else {
					$startenddate = $this->getStandarFiltersStartAndEndDate($datefilter);

					$startDateTime = new DateTimeField($startenddate[0].' '. date('H:i:s'));
					$startdate = $startDateTime->getDisplayDate();
					$endDateTime = new DateTimeField($startenddate[1].' '. date('H:i:s'));
					$enddate = $endDateTime->getDisplayDate();

					if ($startenddate[0] != '' && $startenddate[1] != '' && $selectedfields[0] != '' && $selectedfields[1] != '') {
						$sSQL .= $selectedfields[0].'.'.$selectedfields[1]." between '".$startdate."' and '".$enddate."'";
					}
				}
				$this->queryPlanner->addTable($selectedfields[0]);
			}
		}
		$log->debug('ReportRun :: getStandardCriterialSql'.$reportid);
		return $sSQL;
	}

	/** Function to get standardfilter startdate and enddate for the given type
	 * @param string type
	 * @return array $datevalue = Array(0=>$startdate,1=>$enddate)
	 */
	public function getStandarFiltersStartAndEndDate($type) {
		return getDateforStdFilterBytype($type);
	}

	/** Function to get getGroupingList for the given reportid
	 * @param integer report id
	 * @return array in the following format
	 * 	$grouplist = Array($tablename:$columnname:$fieldlabel:fieldname:typeofdata=>$tablename:$columnname $sorder,
	 *	   $tablename1:$columnname1:$fieldlabel1:fieldname1:typeofdata1=>$tablename1:$columnname1 $sorder,
	 *	   $tablename2:$columnname2:$fieldlabel2:fieldname2:typeofdata2=>$tablename2:$columnname2 $sorder)
	 * This function also sets the return value in the class variable $this->groupbylist
	 */
	public function getGroupingList($reportid) {
		global $adb, $log;

		// Have we initialized information already?
		if ($this->_groupinglist !== false) {
			return $this->_groupinglist;
		}

		$sreportsortsql = ' SELECT vtiger_reportsortcol.*, vtiger_reportgroupbycolumn.* FROM vtiger_report';
		$sreportsortsql .= ' inner join vtiger_reportsortcol on vtiger_report.reportid = vtiger_reportsortcol.reportid';
		$sreportsortsql .= ' LEFT JOIN vtiger_reportgroupbycolumn ON
			(vtiger_report.reportid = vtiger_reportgroupbycolumn.reportid AND vtiger_reportsortcol.sortcolid = vtiger_reportgroupbycolumn.sortid)';
		$sreportsortsql .= ' where vtiger_report.reportid =? AND vtiger_reportsortcol.columnname IN (SELECT columnname from vtiger_selectcolumn WHERE queryid=?)';
		$sreportsortsql .= ' order by vtiger_reportsortcol.sortcolid';

		$result = $adb->pquery($sreportsortsql, array($reportid,$reportid));
		$grouplist = array();

		while ($reportsortrow = $adb->fetch_array($result)) {
			$fieldcolname = $reportsortrow['columnname'];
			if ($fieldcolname != 'none') {
				list($tablename, $colname, $module_field, $fieldname, $single) = explode(':', $fieldcolname);
				$sortorder = $reportsortrow['sortorder'];
				if ($sortorder == 'Ascending') {
					$sortorder = 'ASC';
				} elseif ($sortorder == 'Descending') {
					$sortorder = 'DESC';
				}
				$selectedfields = explode(':', $fieldcolname);
				if ($selectedfields[0] == 'vtiger_crmentity'.$this->primarymodule) {
					$selectedfields[0] = 'vtiger_crmentity';
				}
				if (stripos($selectedfields[1], 'cf_')==0 && stristr($selectedfields[1], 'cf_')) {
					$sqlvalue = '`'.$adb->sql_escape_string(decode_html($selectedfields[2])).'` '.$sortorder;
				} else {
					$sqlvalue = '`'.self::replaceSpecialChar($selectedfields[2]).'` '.$sortorder;
				}
				/************** MONOLITHIC phase 6 customization********************************/
				if ($selectedfields[4]=='D' && strtolower($reportsortrow['dategroupbycriteria'])!='none') {
					$groupField = $module_field;
					$groupCriteria = $reportsortrow['dategroupbycriteria'];
					if (in_array($groupCriteria, array_keys($this->groupByTimeParent))) {
						$parentCriteria = $this->groupByTimeParent[$groupCriteria];
						foreach ($parentCriteria as $criteria) {
							$groupByCondition[]=$this->GetTimeCriteriaCondition($criteria, $groupField).' '.$sortorder;
						}
					}
					$groupByCondition[] =$this->GetTimeCriteriaCondition($groupCriteria, $groupField).' '.$sortorder;
					$sqlvalue = implode(', ', $groupByCondition);
				}
				$grouplist[$fieldcolname] = $sqlvalue;
				$temp = explode('_', $selectedfields[2], 2);
				$module = $temp[0];
				if ((strpos($selectedfields[0], 'vtiger_inventoryproductrel') !== false && ($selectedfields[1]=='productid' || $selectedfields[1]=='serviceid'))
					|| CheckFieldPermission($fieldname, $module) == 'true'
				) {
					$grouplist[$fieldcolname] = $sqlvalue;
				} else {
					$grouplist[$fieldcolname] = $selectedfields[0].'.'.$selectedfields[1];
				}

				$this->queryPlanner->addTable($tablename);
			}
		}

		// Save the information
		$this->_groupinglist = $grouplist;

		$log->debug('ReportRun :: getGroupingList'.$reportid);
		return $grouplist;
	}

	/** function to replace special characters
	 * @param string selected field
	 * @return string for grouplist
	 */
	public static function replaceSpecialChar($selectedfield) {
		$selectedfield = decode_html(decode_html($selectedfield));
		preg_match('/&/', $selectedfield, $matches);
		if (!empty($matches)) {
			$selectedfield = str_replace('&', 'and', ($selectedfield));
		}
		return $selectedfield;
	}

	/** function to get the selected orderby list for the given reportid
	 *  @param integer report id
	 *  @return string orderby clause for the sort order columns
	 *  @sideeffect this function also sets the return value in the class variable $this->orderbylistsql
	 */
	public function getSelectedOrderbyList($reportid) {
		global $adb, $log;

		$sreportsortsql = 'select vtiger_reportsortcol.* from vtiger_report';
		$sreportsortsql .= ' inner join vtiger_reportsortcol on vtiger_report.reportid = vtiger_reportsortcol.reportid';
		$sreportsortsql .= ' where vtiger_report.reportid =? order by vtiger_reportsortcol.sortcolid';

		$result = $adb->pquery($sreportsortsql, array($reportid));
		$noofrows = $adb->num_rows($result);
		$sSQL = '';
		$n = 0;
		for ($i=0; $i<$noofrows; $i++) {
			$fieldcolname = $adb->query_result($result, $i, 'columnname');
			$sortorder = $adb->query_result($result, $i, 'sortorder');

			if ($sortorder == 'Ascending') {
				$sortorder = 'ASC';
			} elseif ($sortorder == 'Descending') {
				$sortorder = 'DESC';
			}

			if ($fieldcolname != 'none') {
				$this->orderbylistcolumns[] = $fieldcolname;
				$n = $n + 1;
				$selectedfields = explode(':', $fieldcolname);
				if ($n > 1) {
					$sSQL .= ', ';
					$this->orderbylistsql .= ', ';
				}
				if ($selectedfields[0] == 'vtiger_crmentity'.$this->primarymodule) {
					$selectedfields[0] = 'vtiger_crmentity';
				}
				$sSQL .= $selectedfields[0].'.'.$selectedfields[1].' '.$sortorder;
				$this->orderbylistsql .= $selectedfields[0].'.'.$selectedfields[1].' '.$selectedfields[2];
			}
		}
		$log->debug('ReportRun :: getSelectedOrderbyList'.$reportid);
		return $sSQL;
	}

	/** function to get secondary Module for the given Primary module and secondary module
	 * @param string module
	 * @param string secondary module
	 * @return string join query for the given secondary module
	 */
	public function getRelatedModulesQuery($module, $secmodule, $type = '', $where_condition = '') {
		global $log,$current_user;
		$query = '';
		if ($secmodule!='') {
			foreach (explode(':', $secmodule) as $value) {
				$foc = CRMEntity::getInstance($value);
				// Case handling: Force table requirement ahead of time.
				$this->queryPlanner->addTable('vtiger_crmentity' . $value);
				$query .= $foc->generateReportsSecQuery($module, $value, $this->queryPlanner, $type, $where_condition);
				$query .= getNonAdminAccessControlQuery($value, $current_user, $value);
			}
		}
		$log->debug('ReportRun :: getRelatedModulesQuery'.$secmodule);
		return $query;
	}

	/** function to get report query for the given module
	 * @param string module
	 * @return string join query for the given module
	 */
	public function getReportsQuery($module, $type = '', $where_condition = '') {
		global $log, $current_user;
		if ($module == 'Leads') {
			$crmtalias = CRMEntity::getcrmEntityTableAlias('Leads');
			$val_conv = ((isset($_COOKIE['LeadConv']) && $_COOKIE['LeadConv'] == 'true') ? 1 : 0);
			$query = "from vtiger_leaddetails
				inner join $crmtalias on vtiger_crmentity.crmid=vtiger_leaddetails.leadid";
			if ($this->queryPlanner->requireTable('vtiger_leadsubdetails')) {
				$query .= ' inner join vtiger_leadsubdetails on vtiger_leadsubdetails.leadsubscriptionid=vtiger_leaddetails.leadid';
			}
			if ($this->queryPlanner->requireTable('vtiger_leadaddress')) {
				$query .= ' inner join vtiger_leadaddress on vtiger_leadaddress.leadaddressid=vtiger_leaddetails.leadid';
			}
			if ($this->queryPlanner->requireTable('vtiger_leadscf')) {
				$query .= ' inner join vtiger_leadscf on vtiger_leaddetails.leadid = vtiger_leadscf.leadid';
			}
			if ($this->queryPlanner->requireTable('vtiger_usersLeads') || $this->queryPlanner->requireTable('vtiger_groupsLeads')) {
				$query .= ' left join vtiger_users as vtiger_usersLeads on vtiger_usersLeads.id = vtiger_crmentity.smownerid';
				$query .= ' left join vtiger_groups as vtiger_groupsLeads on vtiger_groupsLeads.groupid = vtiger_crmentity.smownerid';
			}
			$query .= ' left join vtiger_groups on vtiger_groups.groupid = vtiger_crmentity.smownerid
				left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid';
			if ($this->queryPlanner->requireTable('vtiger_lastModifiedByLeads')) {
				$query .= ' left join vtiger_users as vtiger_lastModifiedByLeads on vtiger_lastModifiedByLeads.id = vtiger_crmentity.modifiedby';
			}
			if ($this->queryPlanner->requireTable('vtiger_CreatedByLeads')) {
				$query .= ' left join vtiger_users as vtiger_CreatedByLeads on vtiger_CreatedByLeads.id = vtiger_crmentity.smcreatorid';
			}
			$query .= ' ' . $this->getRelatedModulesQuery($module, $this->secondarymodule, $type, $where_condition).
				getNonAdminAccessControlQuery($this->primarymodule, $current_user).
				" where vtiger_crmentity.deleted=0 and vtiger_leaddetails.converted=$val_conv";
		} elseif ($module == 'Accounts') {
			$focus = CRMEntity::getInstance($module);
			$query = $focus->generateReportsQuery($module, $this->queryPlanner);
			if ($this->queryPlanner->requireTable('vtiger_accountbillads')) {
				$query .= ' inner join vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid';
			}
			if ($this->queryPlanner->requireTable('vtiger_accountshipads')) {
				$query .= ' inner join vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid';
			}
			if ($this->queryPlanner->requireTable('vtiger_accountAccounts')) {
				$query .= ' left join vtiger_account as vtiger_accountAccounts on vtiger_accountAccounts.accountid = vtiger_account.parentid';
			}
			$query.= ' '.$this->getRelatedModulesQuery($module, $this->secondarymodule, $type, $where_condition).
				getNonAdminAccessControlQuery($this->primarymodule, $current_user).' where vtiger_crmentity.deleted=0 ';
		} elseif ($module == 'Contacts') {
			$crmtalias = CRMEntity::getcrmEntityTableAlias('Contacts');
			$query = "from vtiger_contactdetails
				inner join $crmtalias on vtiger_crmentity.crmid = vtiger_contactdetails.contactid";
			if ($this->queryPlanner->requireTable('vtiger_contactaddress')) {
				$query .= ' inner join vtiger_contactaddress on vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid';
			}
			if ($this->queryPlanner->requireTable('vtiger_customerdetails')) {
				$query .= ' inner join vtiger_customerdetails on vtiger_customerdetails.customerid = vtiger_contactdetails.contactid';
			}
			if ($this->queryPlanner->requireTable('vtiger_contactsubdetails')) {
				$query .= ' inner join vtiger_contactsubdetails on vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid';
			}
			if ($this->queryPlanner->requireTable('vtiger_contactscf')) {
				$query .= ' inner join vtiger_contactscf on vtiger_contactdetails.contactid = vtiger_contactscf.contactid';
			}
			if ($this->queryPlanner->requireTable('vtiger_contactdetailsContacts')) {
				$query .=' left join vtiger_contactdetails as vtiger_contactdetailsContacts on vtiger_contactdetailsContacts.contactid = vtiger_contactdetails.reportsto';
			}
			if ($this->queryPlanner->requireTable('vtiger_accountContacts')) {
				$query .= ' left join vtiger_account as vtiger_accountContacts on vtiger_accountContacts.accountid = vtiger_contactdetails.accountid';
			}
			if ($this->queryPlanner->requireTable('vtiger_usersContacts') || $this->queryPlanner->requireTable('vtiger_groupsContacts')) {
				$query .= ' left join vtiger_users as vtiger_usersContacts on vtiger_usersContacts.id = vtiger_crmentity.smownerid';
				$query .= ' left join vtiger_groups vtiger_groupsContacts on vtiger_groupsContacts.groupid = vtiger_crmentity.smownerid';
			}

			$query .= ' left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
				left join vtiger_groups on vtiger_groups.groupid = vtiger_crmentity.smownerid';

			if ($this->queryPlanner->requireTable('vtiger_lastModifiedByContacts')) {
				$query .= ' left join vtiger_users as vtiger_lastModifiedByContacts on vtiger_lastModifiedByContacts.id = vtiger_crmentity.modifiedby';
			}
			if ($this->queryPlanner->requireTable('vtiger_CreatedByContacts')) {
				$query .= ' left join vtiger_users as vtiger_CreatedByContacts on vtiger_CreatedByContacts.id = vtiger_crmentity.smcreatorid';
			}

			$query .= ' '.$this->getRelatedModulesQuery($module, $this->secondarymodule, $type, $where_condition).
				getNonAdminAccessControlQuery($this->primarymodule, $current_user).' where vtiger_crmentity.deleted=0';
		} elseif ($module == 'Potentials') {
			$focus = CRMEntity::getInstance($module);
			$query = $focus->generateReportsQuery($module, $this->queryPlanner);
			if ($this->queryPlanner->requireTable('vtiger_campaignPotentials')) {
				$query .= ' left join vtiger_campaign as vtiger_campaignPotentials on vtiger_potential.campaignid = vtiger_campaignPotentials.campaignid';
			}
			$query .= ' '.$this->getRelatedModulesQuery($module, $this->secondarymodule, $type, $where_condition).
				getNonAdminAccessControlQuery($this->primarymodule, $current_user).' where vtiger_crmentity.deleted=0 ';
		} elseif ($module == 'Products') {
			//For this Product - we can related Accounts, Contacts (Also Leads, Potentials)
			$focus = CRMEntity::getInstance($module);
			$query = $focus->generateReportsQuery($module, $this->queryPlanner);
			if ($this->queryPlanner->requireTable('vtiger_vendorRelProducts')) {
				$query .= ' left join vtiger_vendor as vtiger_vendorRelProducts on vtiger_vendorRelProducts.vendorid = vtiger_products.vendor_id';
			}
			if ($this->queryPlanner->requireTable('innerProduct')) {
				$query .= ' LEFT JOIN (
					SELECT vtiger_products.productid,
						(CASE WHEN (vtiger_products.currency_id = 1 ) THEN vtiger_products.unit_price
							ELSE (vtiger_products.unit_price / vtiger_currency_info.conversion_rate) END
						) AS actual_unit_price
					FROM vtiger_products
					LEFT JOIN vtiger_currency_info ON vtiger_products.currency_id = vtiger_currency_info.id
					LEFT JOIN vtiger_productcurrencyrel ON vtiger_products.productid = vtiger_productcurrencyrel.productid
					AND vtiger_productcurrencyrel.currencyid=' . $current_user->currency_id
				.') AS innerProduct ON innerProduct.productid = vtiger_products.productid';
			}
			$query .= ' '.$this->getRelatedModulesQuery($module, $this->secondarymodule, $type, $where_condition).
				getNonAdminAccessControlQuery($this->primarymodule, $current_user).' where vtiger_crmentity.deleted=0';
		} elseif ($module == 'HelpDesk') {
			$focus = CRMEntity::getInstance($module);
			$query = $focus->generateReportsQuery($module, $this->queryPlanner);
			$matrix = $this->queryPlanner->newDependencyMatrix();

			$matrix->setDependency('vtiger_crmentityRelHelpDesk', array('vtiger_accountRelHelpDesk', 'vtiger_contactdetailsRelHelpDesk'));

			if ($this->queryPlanner->requireTable('vtiger_crmentityRelHelpDesk', $matrix)) {
				$crmEntityTable = CRMEntity::getcrmEntityTableAlias('HelpDesk', true);
				$query .= " left join $crmEntityTable as vtiger_crmentityRelHelpDesk on vtiger_crmentityRelHelpDesk.crmid=vtiger_troubletickets.parent_id";
			}
			if ($this->queryPlanner->requireTable('vtiger_accountRelHelpDesk')) {
				$query .= ' left join vtiger_account as vtiger_accountRelHelpDesk on vtiger_accountRelHelpDesk.accountid=vtiger_crmentityRelHelpDesk.crmid';
			}
			if ($this->queryPlanner->requireTable('vtiger_contactdetailsRelHelpDesk')) {
				$query .= ' left join vtiger_contactdetails as vtiger_contactdetailsRelHelpDesk on vtiger_contactdetailsRelHelpDesk.contactid= vtiger_crmentityRelHelpDesk.crmid';
			}

			$query .= ' '.$this->getRelatedModulesQuery($module, $this->secondarymodule, $type, $where_condition).
				getNonAdminAccessControlQuery($this->primarymodule, $current_user).' where vtiger_crmentity.deleted=0 ';
		} elseif ($module == 'Quotes') {
			$matrix = $this->queryPlanner->newDependencyMatrix();

			$matrix->setDependency('vtiger_inventoryproductrelQuotes', array('vtiger_productsQuotes', 'vtiger_serviceQuotes'));
			$focus = CRMEntity::getInstance($module);
			$query = $focus->generateReportsQuery($module, $this->queryPlanner);

			if ($this->queryPlanner->requireTable('vtiger_quotesbillads')) {
				$query .= ' inner join vtiger_quotesbillads on vtiger_quotes.quoteid=vtiger_quotesbillads.quotebilladdressid';
			}
			if ($this->queryPlanner->requireTable('vtiger_quotesshipads')) {
				$query .= ' inner join vtiger_quotesshipads on vtiger_quotes.quoteid=vtiger_quotesshipads.quoteshipaddressid';
			}
			if ($this->queryPlanner->requireTable("vtiger_currency_info$module")) {
				$query .= " left join vtiger_currency_info as vtiger_currency_info$module on vtiger_currency_info$module.id = vtiger_quotes.currency_id";
			}
			if (($type !== 'COLUMNSTOTOTAL') || ($type == 'COLUMNSTOTOTAL' && $where_condition == 'add')) {
				if ($this->queryPlanner->requireTable('vtiger_inventoryproductrelQuotes', $matrix)) {
					$query .= ' left join vtiger_inventoryproductrel as vtiger_inventoryproductrelQuotes on vtiger_quotes.quoteid = vtiger_inventoryproductrelQuotes.id';
				}
				if ($this->queryPlanner->requireTable('vtiger_productsQuotes')) {
					$query .= ' left join vtiger_products as vtiger_productsQuotes on vtiger_productsQuotes.productid = vtiger_inventoryproductrelQuotes.productid';
				}
				if ($this->queryPlanner->requireTable('vtiger_serviceQuotes')) {
					$query .= ' left join vtiger_service as vtiger_serviceQuotes on vtiger_serviceQuotes.serviceid = vtiger_inventoryproductrelQuotes.productid';
				}
			}
			if ($this->queryPlanner->requireTable('vtiger_usersRel1')) {
				$query .= ' left join vtiger_users as vtiger_usersRel1 on vtiger_usersRel1.id = vtiger_quotes.inventorymanager';
			}
			if ($this->queryPlanner->requireTable('vtiger_potentialRelQuotes')) {
				$query .= ' left join vtiger_potential as vtiger_potentialRelQuotes on vtiger_potentialRelQuotes.potentialid = vtiger_quotes.potentialid';
			}
			if ($this->queryPlanner->requireTable('vtiger_contactdetailsQuotes')) {
				$query .= ' left join vtiger_contactdetails as vtiger_contactdetailsQuotes on vtiger_contactdetailsQuotes.contactid = vtiger_quotes.contactid';
			}
			if ($this->queryPlanner->requireTable('vtiger_accountQuotes')) {
				$query .= ' left join vtiger_account as vtiger_accountQuotes on vtiger_accountQuotes.accountid = vtiger_quotes.accountid';
			}
			if ($this->queryPlanner->requireTable('vtiger_currency_info')) {
				$query .= ' LEFT JOIN vtiger_currency_info ON vtiger_currency_info.id = vtiger_quotes.currency_id';
			}
			$query .= ' ' . $this->getRelatedModulesQuery($module, $this->secondarymodule, $type, $where_condition).
				getNonAdminAccessControlQuery($this->primarymodule, $current_user).' where vtiger_crmentity.deleted=0';
		} elseif ($module == 'PurchaseOrder') {
			$matrix = $this->queryPlanner->newDependencyMatrix();

			$matrix->setDependency('vtiger_inventoryproductrelPurchaseOrder', array('vtiger_productsPurchaseOrder', 'vtiger_servicePurchaseOrder'));
			$focus = CRMEntity::getInstance($module);
			$query = $focus->generateReportsQuery($module, $this->queryPlanner);

			if ($this->queryPlanner->requireTable('vtiger_pobillads')) {
				$query .= ' inner join vtiger_pobillads on vtiger_purchaseorder.purchaseorderid=vtiger_pobillads.pobilladdressid';
			}
			if ($this->queryPlanner->requireTable('vtiger_poshipads')) {
				$query .= ' inner join vtiger_poshipads on vtiger_purchaseorder.purchaseorderid=vtiger_poshipads.poshipaddressid';
			}
			if ($this->queryPlanner->requireTable("vtiger_currency_info$module")) {
				$query .= " left join vtiger_currency_info as vtiger_currency_info$module on vtiger_currency_info$module.id = vtiger_purchaseorder.currency_id";
			}
			if (($type !== 'COLUMNSTOTOTAL') || ($type == 'COLUMNSTOTOTAL' && $where_condition == 'add')) {
				if ($this->queryPlanner->requireTable('vtiger_inventoryproductrelPurchaseOrder', $matrix)) {
					$query .= ' left join vtiger_inventoryproductrel as vtiger_inventoryproductrelPurchaseOrder on vtiger_purchaseorder.purchaseorderid = vtiger_inventoryproductrelPurchaseOrder.id';
				}
				if ($this->queryPlanner->requireTable('vtiger_productsPurchaseOrder')) {
					$query .= ' left join vtiger_products as vtiger_productsPurchaseOrder on vtiger_productsPurchaseOrder.productid = vtiger_inventoryproductrelPurchaseOrder.productid';
				}
				if ($this->queryPlanner->requireTable('vtiger_servicePurchaseOrder')) {
					$query .= ' left join vtiger_service as vtiger_servicePurchaseOrder on vtiger_servicePurchaseOrder.serviceid = vtiger_inventoryproductrelPurchaseOrder.productid';
				}
			}
			if ($this->queryPlanner->requireTable('vtiger_accountsPurchaseOrder')) {
				$query .= ' left join vtiger_account as vtiger_accountsPurchaseOrder on vtiger_accountsPurchaseOrder.accountid = vtiger_purchaseorder.accountid';
			}

			if ($this->queryPlanner->requireTable('vtiger_vendorRelPurchaseOrder')) {
				$query .= ' left join vtiger_vendor as vtiger_vendorRelPurchaseOrder on vtiger_vendorRelPurchaseOrder.vendorid = vtiger_purchaseorder.vendorid';
			}
			if ($this->queryPlanner->requireTable('vtiger_contactdetailsPurchaseOrder')) {
				$query .= ' left join vtiger_contactdetails as vtiger_contactdetailsPurchaseOrder on vtiger_contactdetailsPurchaseOrder.contactid = vtiger_purchaseorder.contactid';
			}
			if ($this->queryPlanner->requireTable('vtiger_currency_info')) {
				$query .= ' LEFT JOIN vtiger_currency_info ON vtiger_currency_info.id = vtiger_purchaseorder.currency_id';
			}
			$query .= ' ' . $this->getRelatedModulesQuery($module, $this->secondarymodule, $type, $where_condition) .
				getNonAdminAccessControlQuery($this->primarymodule, $current_user).' where vtiger_crmentity.deleted=0';
		} elseif ($module == 'Invoice') {
			$matrix = $this->queryPlanner->newDependencyMatrix();

			$matrix->setDependency('vtiger_inventoryproductrelInvoice', array('vtiger_productsInvoice', 'vtiger_serviceInvoice'));
			$focus = CRMEntity::getInstance($module);
			$query = $focus->generateReportsQuery($module, $this->queryPlanner);

			if ($this->queryPlanner->requireTable('vtiger_invoicebillads')) {
				$query .=' inner join vtiger_invoicebillads on vtiger_invoice.invoiceid=vtiger_invoicebillads.invoicebilladdressid';
			}
			if ($this->queryPlanner->requireTable('vtiger_invoiceshipads')) {
				$query .=' inner join vtiger_invoiceshipads on vtiger_invoice.invoiceid=vtiger_invoiceshipads.invoiceshipaddressid';
			}
			if ($this->queryPlanner->requireTable("vtiger_currency_info$module")) {
				$query .=" left join vtiger_currency_info as vtiger_currency_info$module on vtiger_currency_info$module.id = vtiger_invoice.currency_id";
			}
			if (($type !== 'COLUMNSTOTOTAL') || ($type == 'COLUMNSTOTOTAL' && $where_condition == 'add')) {
				if ($this->queryPlanner->requireTable('vtiger_inventoryproductrelInvoice', $matrix)) {
					$query .=' left join vtiger_inventoryproductrel as vtiger_inventoryproductrelInvoice on vtiger_invoice.invoiceid = vtiger_inventoryproductrelInvoice.id';
				}
				if ($this->queryPlanner->requireTable('vtiger_productsInvoice')) {
					$query .=' left join vtiger_products as vtiger_productsInvoice on vtiger_productsInvoice.productid = vtiger_inventoryproductrelInvoice.productid';
				}
				if ($this->queryPlanner->requireTable('vtiger_serviceInvoice')) {
					$query .=' left join vtiger_service as vtiger_serviceInvoice on vtiger_serviceInvoice.serviceid = vtiger_inventoryproductrelInvoice.productid';
				}
			}
			if ($this->queryPlanner->requireTable('vtiger_salesorderInvoice')) {
				$query .= ' left join vtiger_salesorder as vtiger_salesorderInvoice on vtiger_salesorderInvoice.salesorderid=vtiger_invoice.salesorderid';
			}
			if ($this->queryPlanner->requireTable('vtiger_accountInvoice')) {
				$query .= ' left join vtiger_account as vtiger_accountInvoice on vtiger_accountInvoice.accountid = vtiger_invoice.accountid';
			}
			if ($this->queryPlanner->requireTable('vtiger_contactdetailsInvoice')) {
				$query .= ' left join vtiger_contactdetails as vtiger_contactdetailsInvoice on vtiger_contactdetailsInvoice.contactid = vtiger_invoice.contactid';
			}
			if ($this->queryPlanner->requireTable('vtiger_currency_info')) {
				$query .= ' LEFT JOIN vtiger_currency_info ON vtiger_currency_info.id = vtiger_invoice.currency_id';
			}
			$query .= ' ' . $this->getRelatedModulesQuery($module, $this->secondarymodule, $type, $where_condition) .
				getNonAdminAccessControlQuery($this->primarymodule, $current_user).' where vtiger_crmentity.deleted=0';
		} elseif ($module == 'SalesOrder') {
			$matrix = $this->queryPlanner->newDependencyMatrix();

			$matrix->setDependency('vtiger_inventoryproductrelSalesOrder', array('vtiger_productsSalesOrder', 'vtiger_serviceSalesOrder'));
			$focus = CRMEntity::getInstance($module);
			$query = $focus->generateReportsQuery($module, $this->queryPlanner);

			if ($this->queryPlanner->requireTable('vtiger_sobillads')) {
				$query .= ' inner join vtiger_sobillads on vtiger_salesorder.salesorderid=vtiger_sobillads.sobilladdressid';
			}
			if ($this->queryPlanner->requireTable('vtiger_soshipads')) {
				$query .= ' inner join vtiger_soshipads on vtiger_salesorder.salesorderid=vtiger_soshipads.soshipaddressid';
			}
			if ($this->queryPlanner->requireTable("vtiger_currency_info$module")) {
				$query .= " left join vtiger_currency_info as vtiger_currency_info$module on vtiger_currency_info$module.id = vtiger_salesorder.currency_id";
			}
			if (($type !== 'COLUMNSTOTOTAL') || ($type == 'COLUMNSTOTOTAL' && $where_condition == 'add')) {
				if ($this->queryPlanner->requireTable('vtiger_inventoryproductrelSalesOrder', $matrix)) {
					$query .= ' left join vtiger_inventoryproductrel as vtiger_inventoryproductrelSalesOrder on vtiger_salesorder.salesorderid = vtiger_inventoryproductrelSalesOrder.id';
				}
				if ($this->queryPlanner->requireTable('vtiger_productsSalesOrder')) {
					$query .= ' left join vtiger_products as vtiger_productsSalesOrder on vtiger_productsSalesOrder.productid = vtiger_inventoryproductrelSalesOrder.productid';
				}
				if ($this->queryPlanner->requireTable('vtiger_serviceSalesOrder')) {
					$query.=' left join vtiger_service as vtiger_serviceSalesOrder on vtiger_serviceSalesOrder.serviceid = vtiger_inventoryproductrelSalesOrder.productid';
				}
			}
			if ($this->queryPlanner->requireTable('vtiger_contactdetailsSalesOrder')) {
				$query .=' left join vtiger_contactdetails as vtiger_contactdetailsSalesOrder on vtiger_contactdetailsSalesOrder.contactid = vtiger_salesorder.contactid';
			}
			if ($this->queryPlanner->requireTable('vtiger_quotesSalesOrder')) {
				$query .= ' left join vtiger_quotes as vtiger_quotesSalesOrder on vtiger_quotesSalesOrder.quoteid = vtiger_salesorder.quoteid';
			}
			if ($this->queryPlanner->requireTable('vtiger_accountSalesOrder')) {
				$query .= ' left join vtiger_account as vtiger_accountSalesOrder on vtiger_accountSalesOrder.accountid = vtiger_salesorder.accountid';
			}
			if ($this->queryPlanner->requireTable('vtiger_potentialRelSalesOrder')) {
				$query .= ' left join vtiger_potential as vtiger_potentialRelSalesOrder on vtiger_potentialRelSalesOrder.potentialid = vtiger_salesorder.potentialid';
			}
			if ($this->queryPlanner->requireTable('vtiger_invoice_recurring_info')) {
				$query .= ' left join vtiger_invoice_recurring_info on vtiger_invoice_recurring_info.salesorderid = vtiger_salesorder.salesorderid';
			}
			if ($this->queryPlanner->requireTable('vtiger_currency_info')) {
				$query .= ' LEFT JOIN vtiger_currency_info ON vtiger_currency_info.id = vtiger_salesorder.currency_id';
			}
			$query .= ' ' . $this->getRelatedModulesQuery($module, $this->secondarymodule, $type, $where_condition) .
				getNonAdminAccessControlQuery($this->primarymodule, $current_user).' where vtiger_crmentity.deleted=0';
		} elseif ($module == 'Emails') {
			$crmtalias = CRMEntity::getcrmEntityTableAlias('Emails');
			$query = "from vtiger_activity
			INNER JOIN $crmtalias ON vtiger_crmentity.crmid = vtiger_activity.activityid AND vtiger_activity.activitytype = 'Emails'
			LEFT JOIN vtiger_emaildetails ON vtiger_emaildetails.emailid=vtiger_activity.activityid";

			if ($this->queryPlanner->requireTable('vtiger_email_track')) {
				$query .= ' LEFT JOIN vtiger_email_track ON vtiger_email_track.mailid = vtiger_activity.activityid';
			}
			if ($this->queryPlanner->requireTable('vtiger_usersEmails') || $this->queryPlanner->requireTable('vtiger_groupsEmails')) {
				$query .= ' LEFT JOIN vtiger_users AS vtiger_usersEmails ON vtiger_usersEmails.id = vtiger_crmentity.smownerid';
				$query .= ' LEFT JOIN vtiger_groups AS vtiger_groupsEmails ON vtiger_groupsEmails.groupid = vtiger_crmentity.smownerid';
			}

			$query .= ' LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid';
			$query .= ' LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid';

			if ($this->queryPlanner->requireTable("vtiger_lastModifiedBy$module")) {
				$query .= ' LEFT JOIN vtiger_users AS vtiger_lastModifiedBy' . $module . ' ON vtiger_lastModifiedBy' . $module . '.id = vtiger_crmentity.modifiedby';
			}
			if ($this->queryPlanner->requireTable("vtiger_CreatedBy$module")) {
				$query .= " left join vtiger_users as vtiger_CreatedBy$module on vtiger_CreatedBy$module.id = vtiger_crmentity.smcreatorid";
			}

			$query .= ' '.$this->getRelatedModulesQuery($module, $this->secondarymodule).
				getNonAdminAccessControlQuery($this->primarymodule, $current_user).' WHERE vtiger_crmentity.deleted = 0';
		} elseif ($module == 'Issuecards') {
			$focus = CRMEntity::getInstance($module);
			$query = $focus->generateReportsQuery($module, $this->queryPlanner);
			$query .= " left join vtiger_currency_info as vtiger_currency_info$module on vtiger_currency_info$module.id = vtiger_issuecards.currency_id";
			if (($type !== 'COLUMNSTOTOTAL') || ($type == 'COLUMNSTOTOTAL' && $where_condition == 'add')) {
				$query .= ' left join vtiger_inventoryproductrel as vtiger_inventoryproductrelIssuecards on vtiger_issuecards.issuecardid = vtiger_inventoryproductrelIssuecards.id
					left join vtiger_products as vtiger_productsIssuecards on vtiger_productsIssuecards.productid = vtiger_inventoryproductrelIssuecards.productid
					left join vtiger_service as vtiger_serviceIssuecards on vtiger_serviceIssuecards.serviceid = vtiger_inventoryproductrelIssuecards.productid';
			}
			$query .= ' '.$this->getRelatedModulesQuery($module, $this->secondarymodule, $type, $where_condition).
				getNonAdminAccessControlQuery($this->primarymodule, $current_user).' WHERE vtiger_crmentity.deleted=0';
		} else {
			if ($module != '') {
				$focus = CRMEntity::getInstance($module);
				$query = $focus->generateReportsQuery($module, $this->queryPlanner) .
					$this->getRelatedModulesQuery($module, $this->secondarymodule, $type, $where_condition) .
					getNonAdminAccessControlQuery($this->primarymodule, $current_user).' WHERE vtiger_crmentity.deleted=0';
			}
		}
		$log->debug('ReportRun :: getReportsQuery'.$module);

		return $query;
	}

	public static function sGetDirectSQL($reportid, $cbreporttype, $eliminatenewlines = true) {
		global $adb;
		$rptrs = $adb->pquery('select moreinfo from vtiger_report where reportid=?', array($reportid));
		if ($rptrs && $adb->num_rows($rptrs)>0) {
			$minfo = $adb->query_result($rptrs, 0, 0);
			if (!empty($minfo)) {
				if ($cbreporttype == 'crosstabsql') {
					$minfo = unserialize($minfo);
					$minfo = $minfo['sql'];
				}
				if ($eliminatenewlines) {
					$minfo = preg_replace('#\R+#', ' ', $minfo);
				}
				return $minfo;
			}
		}
		return 'select 0'; // just return a valid empty SQL statement
	}

	/** function to get query for the given reportid,filterlist,type
	 * @param integer report id
	 * @param array filtersql
	 * @param string output format of the report
	 * @param boolean chart report
	 * @return string join query for the report
	 */
	public function sGetSQLforReport($reportid, $filtersql, $type = '', $chartReport = false) {
		global $log;
		$groupsquery = '';
		if ($this->cbreporttype == 'directsql' || $this->cbreporttype == 'crosstabsql') {
			$reportquery = self::sGetDirectSQL($reportid, $this->cbreporttype, true);
			$columnstotalsql = '';
			if (stripos($reportquery, ' order by ')) {
				$groupsquery = substr($reportquery, stripos($reportquery, ' order by ')+10);
				$reportquery = substr($reportquery, 0, stripos($reportquery, ' order by '));
			}
			$basereportquery = $reportquery;
			if (stripos($reportquery, ' group by ')) {
				$groupbyquery = substr($reportquery, stripos($reportquery, ' group by '));
				$reportquery = substr($reportquery, 0, stripos($reportquery, ' group by '));
			} else {
				$groupbyquery = '';
			}
			if (stripos($reportquery, ' where ')) {
				$glue = ' AND ';
			} else {
				$glue = ' WHERE ';
			}
			if (empty($filtersql)) {
				$wheresql = '';
			} else {
				$wheresql = $glue . $filtersql;
			}
			$reportquery .= $wheresql . $groupbyquery;
		} else {
			$columnlist = $this->getQueryColumnsList($reportid, $type);
			$groupslist = $this->getGroupingList($reportid);
			$this->getGroupByTimeList($reportid);
			$stdfilterlist = $this->getStdFilterList($reportid);
			$columnstotallist = $this->getColumnsTotal($reportid, $columnlist);
			$advfiltersql = $this->getAdvFilterSql($reportid);

			$this->totallist = $columnstotallist;
			$selectlist = $columnlist;
			//columns list
			if (isset($selectlist)) {
				$selectedcolumns = implode(', ', $selectlist);
			}
			//groups list
			if (isset($groupslist)) {
				$groupsquery = implode(', ', $groupslist);
			}

			//standard list
			if (isset($stdfilterlist)) {
				$stdfiltersql = implode(', ', $stdfilterlist);
			}
			//columns to total list
			if (isset($columnstotallist)) {
				$columnstotalsql = implode(', ', $columnstotallist);
			} else {
				$columnstotalsql = '';
			}
			if ($stdfiltersql != '') {
				$wheresql = ' and '.$stdfiltersql;
			} else {
				$wheresql = '';
			}

			if (isset($filtersql) && !empty($filtersql)) {
				$advfiltersql = $filtersql;
			}
			$where_condition = '';
			if ($advfiltersql != '') {
				if ($type=='COLUMNSTOTOTAL' && (strstr($advfiltersql, 'vtiger_products'.$this->primarymodule) || strstr($advfiltersql, 'vtiger_service'.$this->primarymodule))) {
					$where_condition='add';
				}
				$wheresql .= ' and '.$advfiltersql;
			}

			$reportquery = $basereportquery = $this->getReportsQuery($this->primarymodule, $type, $where_condition);
		}
		// If we don't have access to any columns, let us select one column and limit result to show we have no results
		$allColumnsRestricted = false;

		if ($type == 'COLUMNSTOTOTAL') {
			if ($columnstotalsql != '') {
				$totalsselectedcolumns = $columnlist;
				// eliminate product/service columns for inventory modules
				foreach ($columnlist as $key => $value) {
					$finfo = explode(':', $key);
					if ($finfo[0]=='vtiger_inventoryproductrel'.$this->primarymodule) {
						unset($totalsselectedcolumns[$key]);
					}
				}
				if (isset($this->_columnstotallistaddtoselect) && is_array($this->_columnstotallistaddtoselect) && count($this->_columnstotallistaddtoselect)>0) {
					$columnstotallistaddtoselect = ', '.implode(', ', $this->_columnstotallistaddtoselect);
					$totalscolalias = array();
					foreach ($this->_columnstotallistaddtoselect as $key => $value) {
						list($void,$calias) = explode(' AS ', $value);
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
					$columnstotallistaddtoselect = '';
				}
				$totalsselectedcolumns = implode(', ', $totalsselectedcolumns);
				$reportquery = 'select '.$columnstotalsql.' from (select DISTINCT '.$totalsselectedcolumns.$columnstotallistaddtoselect.' '
					.$reportquery.' '.$wheresql.') as summary_calcs';
			}
		} elseif ($this->cbreporttype != 'directsql' && $this->cbreporttype != 'crosstabsql') {
			if ($selectedcolumns == '') {
				$selectedcolumns = "''"; // "''" to get blank column name
				$allColumnsRestricted = true;
			}
			$reportquery = 'select DISTINCT '.$selectedcolumns.' '.$reportquery.' '.$wheresql;
		}
		$reportquery = listQueryNonAdminChange($reportquery, $this->primarymodule);

		if (trim($groupsquery) != '' && $type !== 'COLUMNSTOTOTAL') {
			if ($chartReport) {
				reset($groupslist);
				$first_key = key($groupslist);
				$reportquery='select '.$columnlist[$first_key].", count(*) AS 'groupby_count' $basereportquery $wheresql group by ".$this->GetFirstSortByField($reportid);
			} else {
				$reportquery .= ' order by '.$groupsquery;
			}
		}

		// No columns selected so limit the number of rows directly.
		if ($allColumnsRestricted) {
			$reportquery .= ' limit 0';
		}

		preg_match('/&amp;/', $reportquery, $matches);
		if (!empty($matches)) {
			$report=str_replace('&amp;', '&', $reportquery);
			$reportquery = $this->replaceSpecialChar($report);
		}
		if ($type == 'HTMLPAGED' && !$allColumnsRestricted) {
			$rowsperpage = GlobalVariable::getVariable('Report_ListView_PageSize', 40);
			$reportquery .= ' limit '.(($this->page-1)*$rowsperpage).', '.$rowsperpage;
		}
		if (!$this->_tmptablesinitialized) {
			$this->queryPlanner->initializeTempTables();
			$this->_tmptablesinitialized = true;
		}
		$log->debug('ReportRun :: sGetSQLforReport '.$reportid);
		if (GlobalVariable::getVariable('Debug_Report_Query', '0')=='1') {
			$log->fatal('Report Query for '.$this->reportname." ($reportid)");
			$log->fatal($reportquery);
		}
		return $reportquery;
	}

	/** function to get the report output in HTML,PDF,TOTAL,PRINT,PRINTTOTAL formats depends on the argument $outputformat
	 * @param string outputformat (valid parameters HTML,PDF,TOTAL,PRINT,PRINT_TOTAL)
	 * @param string filtersql
	 * @return string HTML Report if $outputformat is HTML
	 *  		Array for PDF if  $outputformat is PDF
	 *		HTML strings for TOTAL if $outputformat is TOTAL
	 *		Array for PRINT if $outputformat is PRINT
	 *		HTML strings for TOTAL fields  if $outputformat is PRINTTOTAL
	 *		HTML strings for
	 */
	public function GenerateReport($outputformat, $filtersql, $directOutput = false, &$returnfieldinfo = array()) {
		global $adb, $current_user, $php_max_execution_time, $modules, $app_strings, $mod_strings;
		$userprivs = $current_user->getPrivileges();
		$picklistarray = array();
		$modules_selected = array();
		$modules_selected[] = $this->primarymodule;
		if (!empty($this->secondarymodule)) {
			$sec_modules = explode(':', $this->secondarymodule);
			for ($i=0; $i<count($sec_modules); $i++) {
				$modules_selected[] = $sec_modules[$i];
			}
		}

		// Update Reference fields list list
		$referencefieldres = $adb->pquery('SELECT tabid, fieldlabel, uitype from vtiger_field WHERE uitype in (10,101)', array());
		if ($referencefieldres) {
			foreach ($referencefieldres as $referencefieldrow) {
				$uiType = $referencefieldrow['uitype'];
				$modprefixedlabel = getTabModuleName($referencefieldrow['tabid']).' '.$referencefieldrow['fieldlabel'];
				$modprefixedlabel = str_replace(' ', '_', $modprefixedlabel);

				if ($uiType == 10 && !in_array($modprefixedlabel, $this->ui10_fields)) {
					$this->ui10_fields[] = $modprefixedlabel;
				} elseif ($uiType == 101 && !in_array($modprefixedlabel, $this->ui101_fields)) {
					$this->ui101_fields[] = $modprefixedlabel;
				}
			}
		}

		if ($outputformat == 'HTML' || $outputformat == 'HTMLPAGED') {
			if ($outputformat=='HTMLPAGED') {
				$directOutput = false;
			}
			$sSQL = $this->sGetSQLforReport($this->reportid, $filtersql, $outputformat);
			$result = $adb->query($sSQL);
			$error_msg = $adb->database->ErrorMsg();
			if (!$result && $error_msg!='') {
				$tmptables = $this->queryPlanner->getTemporaryTables();
				if (count($tmptables)>0) {
					$this->queryPlanner->disableTempTables();
					$sSQL = $this->sGetSQLforReport($this->reportid, $filtersql, $outputformat);
					$result = $adb->query($sSQL);
				}
				if (!$result) {
					if ($directOutput) {
						echo getTranslatedString('LBL_REPORT_GENERATION_FAILED', 'Reports') . '<br>' . $error_msg;
						$error_msg = false;
					}
					return $error_msg;
				}
			}

			// Performance Optimization: If direct output is required
			if ($directOutput) {
				echo '<table cellpadding="5" cellspacing="0" align="center" class="rptTable"><tr>';
			}

			if (!$userprivs->hasGlobalReadPermission()) {
				$picklistarray = $this->getAccessPickListValues();
			}
			if ($result) {
				$y=$adb->num_fields($result);
				$noofrows = $adb->num_rows($result);
				$this->number_of_rows = $noofrows;
				if ($outputformat == 'HTMLPAGED') {
					$rowsperpage = GlobalVariable::getVariable('Report_ListView_PageSize', 40);
					if ($this->page*$rowsperpage>$noofrows-($noofrows % $rowsperpage)) {
						$this->islastpage = true;
					}
				}
				$reportmaxrows = GlobalVariable::getVariable('Report_MaxRows_OnScreen', 5000);
				$custom_field_values = $adb->fetch_array($result);
				$groupslist = $this->getGroupingList($this->reportid);
				$header = '';
				for ($x=0; $x<$y; $x++) {
					$fld = $adb->field_name($result, $x);
					if ($fld->name=='LBL_ACTION') {
						$module = 'Reports';
						$fieldLabel = 'LBL_ACTION';
					} else {
						list($module, $fieldLabel) = explode('_', $fld->name, 2);
					}
					$fieldInfo = getFieldByReportLabel($module, $fieldLabel);
					if (!empty($fieldInfo)) {
						$field = WebserviceField::fromArray($adb, $fieldInfo);
					}
					if (!empty($fieldInfo)) {
						$headerLabel = getTranslatedString($field->getFieldLabelKey(), $module);
					} else {
						$headerLabel = getTranslatedString(str_replace('_', ' ', $fieldLabel), $module);
					}
					/*STRING TRANSLATION starts */
					$moduleLabel = '';
					if (in_array($module, $modules_selected)) {
						$moduleLabel = getTranslatedString($module, $module);
					}

					if (empty($headerLabel)) {
						$headerLabel = getTranslatedString(str_replace('_', ' ', $fld->name));
					}
					if (!empty($this->secondarymodule) && $moduleLabel != '') {
						$headerLabel = $moduleLabel.' '. $headerLabel;
					}
					$header .= "<td class='rptCellLabel'>".$headerLabel.'</td>';

					// Performance Optimization: If direct output is required
					if ($directOutput) {
						echo $header;
						$header = '';
					}
				}

				// Performance Optimization: If direct output is required
				if ($directOutput) {
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
						if (count($groupslist) == 1) {
							$newvalue = $custom_field_values[0];
						} elseif (count($groupslist) == 2) {
							$newvalue = $custom_field_values[0];
							$snewvalue = $custom_field_values[1];
						} elseif (count($groupslist) == 3) {
							$newvalue = $custom_field_values[0];
							$snewvalue = $custom_field_values[1];
							$tnewvalue = $custom_field_values[2];
						}
						if ($newvalue == '') {
							$newvalue = '-';
						}

						if ($snewvalue == '') {
							$snewvalue = '-';
						}

						if ($tnewvalue == '') {
							$tnewvalue = '-';
						}

						$valtemplate .= '<tr>';

						// Performance Optimization
						if ($directOutput) {
							echo $valtemplate;
							$valtemplate = '';
						}

						for ($i=0; $i<$y; $i++) {
							$fld = $adb->field_name($result, $i);
							$fieldvalue = getReportFieldValue($this, $picklistarray, $fld, $custom_field_values, $i);

							if ($fieldvalue == '') {
								$fieldvalue = '-';
							} elseif ($fld->name == 'LBL_ACTION' && $fieldvalue != '-') {
								$fieldvalue = "<a href='index.php?module=".$this->primarymodule."&action=DetailView&record={$fieldvalue}' target='_blank'>"
									.getTranslatedString('LBL_VIEW_DETAILS', 'Reports').'</a>';
							}

							if (($lastvalue == $fieldvalue) && $this->reporttype == 'summary') {
								if ($this->reporttype == 'summary') {
									$valtemplate .= "<td class='rptEmptyGrp'>&nbsp;</td>";
								} else {
									$valtemplate .= "<td class='rptData'>".$fieldvalue.'</td>';
								}
							} elseif (($secondvalue === $fieldvalue) && $this->reporttype == 'summary') {
								if ($lastvalue === $newvalue) {
									$valtemplate .= "<td class='rptEmptyGrp'>&nbsp;</td>";
								} else {
									$valtemplate .= "<td class='rptGrpHead'>".$fieldvalue.'</td>';
								}
							} elseif (($thirdvalue === $fieldvalue) && $this->reporttype == 'summary') {
								if ($secondvalue === $snewvalue) {
									$valtemplate .= "<td class='rptEmptyGrp'>&nbsp;</td>";
								} else {
									$valtemplate .= "<td class='rptGrpHead'>".$fieldvalue.'</td>';
								}
							} else {
								if ($this->reporttype == 'tabular') {
									$valtemplate .= "<td class='rptData'>".$fieldvalue.'</td>';
								} else {
									$valtemplate .= "<td class='rptGrpHead'>".$fieldvalue.'</td>';
								}
							}

							// Performance Optimization: If direct output is required
							if ($directOutput) {
								echo $valtemplate;
								$valtemplate = '';
							}
						}

						$valtemplate .= '</tr>';

						// Performance Optimization: If direct output is required
						if ($directOutput) {
							echo $valtemplate;
							$valtemplate = '';
						}

						$lastvalue = $newvalue;
						$secondvalue = $snewvalue;
						$thirdvalue = $tnewvalue;
						set_time_limit($php_max_execution_time);
					} while ($custom_field_values = $adb->fetch_array($result));
				} else {
					$reporterrmsg = new vtigerCRM_Smarty();
					$errormessage = getTranslatedString('ERR_TOO_MANY_ROWS', 'Reports');
					$reporterrmsg->assign('ERROR_MESSAGE', $errormessage);
					$errormessage = $reporterrmsg->fetch('applicationmessage.tpl');
					$errormessage = '<tr><td colspan="'.$y.'">'.$errormessage.'</td>';
					if ($directOutput) {
						echo $errormessage;
					} else {
						$valtemplate = $errormessage;
					}
				}
				// Performance Optimization
				if ($directOutput) {
					echo '</tr></table>';
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
		} elseif ($outputformat == 'HEADERS') {
			$sSQL = $this->sGetSQLforReport($this->reportid, $filtersql, 'HTML');
			$result = $adb->query($sSQL.' limit 1');
			$error_msg = $adb->database->ErrorMsg();
			if (!$result && $error_msg!='') {
				$tmptables = $this->queryPlanner->getTemporaryTables();
				if (count($tmptables)>0) {
					// we have temp tables so we deactivate them and try again
					$this->queryPlanner->disableTempTables();
					$sSQL = $this->sGetSQLforReport($this->reportid, $filtersql, 'HTML');
					$result = $adb->query($sSQL.' limit 1');
					$error_msg = $adb->database->ErrorMsg();
				}
				if (!$result && $error_msg!='') {
					return array(
						'has_contents' => false,
						'jsonheaders' => array(),
						'i18nheaders' => array(),
						'error' => true,
						'error_message' => getTranslatedString('LBL_REPORT_GENERATION_FAILED', 'Reports') . ':' . $error_msg,
					);
				}
			}
			$fldcnt=$adb->num_fields($result);
			$i18nheader = $jsonheader = array();
			for ($x=0; $x<$fldcnt; $x++) {
				$fld = $adb->field_name($result, $x);
				if ($fld->name=='LBL_ACTION') {
					$module = 'Reports';
					$fieldLabel = 'LBL_ACTION';
				} elseif (strpos($fld->name, '_')) {
					list($module, $fieldLabel) = explode('_', $fld->name, 2);
				} else {
					$module = $this->primarymodule;
					$fieldLabel = $fld->name;
				}
				$fieldInfo = getFieldByReportLabel($module, $fieldLabel);
				if (!empty($fieldInfo)) {
					$field = WebserviceField::fromArray($adb, $fieldInfo);
				}
				if (!empty($fieldInfo)) {
					$headerLabel = $field->getFieldLabelKey();
				} else {
					$headerLabel = str_replace('_', ' ', $fieldLabel);
				}
				if (empty($headerLabel)) {
					$headerLabel = str_replace('_', ' ', $fld->name);
				}
				$i18nheaderLabel = getTranslatedString($headerLabel, $module);
				$moduleLabel = '';
				if ($module != $this->primarymodule && !empty($this->secondarymodule) && in_array($module, $modules_selected)) {
					$headerLabel = $module.' '.$headerLabel;
					$i18nheaderLabel = getTranslatedString($module, $module).' '.$i18nheaderLabel;
				}
				if ($fld->name=='LBL_ACTION') {
					$jsonheader[] = 'reportrowaction';
				} else {
					$jsonheader[] = $headerLabel;
				}
				$i18nheader[] = $i18nheaderLabel;
			}
			return array(
				'has_contents' => ($adb->num_rows($result) == 1),
				'jsonheaders' => $jsonheader,
				'i18nheaders' => $i18nheader,
				'error' => false,
			);
		} elseif ($outputformat == 'JSON' || $outputformat == 'JSONPAGED') {
			$sSQL = $this->sGetSQLforReport($this->reportid, $filtersql, ($outputformat == 'JSON' ? 'HTML' : 'HTMLPAGED'));
			$result = $adb->query($sSQL);
			if ($result) {
				$count_result = $adb->query(mkXQuery(stripTailCommandsFromQuery($sSQL, false), 'count(*) AS count'));
			}
			$error_msg = $adb->database->ErrorMsg();
			if (!$result && $error_msg!='') {
				$tmptables = $this->queryPlanner->getTemporaryTables();
				if (count($tmptables)>0) {
					// we have temp tables so we deactivate them and try again
					$this->queryPlanner->disableTempTables();
					$sSQL = $this->sGetSQLforReport($this->reportid, $filtersql, ($outputformat == 'JSON' ? 'HTML' : 'HTMLPAGED'));
					$result = $adb->query($sSQL);
					if ($result) {
						$count_result = $adb->query(mkXQuery(stripTailCommandsFromQuery($sSQL, false), 'count(*) AS count'));
					}
					$error_msg = $adb->database->ErrorMsg();
				}
				if (!$result && $error_msg!='') {
					$resp = array(
						'total' => 0,
						'data' => array(),
						'sql' => $sSQL,
						'error' => true,
						'error_message' => getTranslatedString('LBL_REPORT_GENERATION_FAILED', 'Reports') . ':' . $error_msg,
					);
					foreach ($tmptables as $tmptblname => $tmptbldetails) {
						$resp[$tmptblname] = $tmptbldetails['query'];
					}
					return json_encode($resp);
				}
			}

			if (!$userprivs->hasGlobalReadPermission()) {
				$picklistarray = $this->getAccessPickListValues();
			}
			if ($result) {
				$fldcnt=$adb->num_fields($result);
				$noofrows = $adb->query_result($count_result, 0, 0);
				$this->number_of_rows = $noofrows;
				$resp = array(
					'total' => $noofrows,
					'current_page' => $this->page,
					'error' => false,
				);
				if (GlobalVariable::getVariable('Debug_Report_Query', '0')=='1') {
					$resp['sql'] = $sSQL;
					foreach ($this->queryPlanner->getTemporaryTables() as $tmptblname => $tmptbldetails) {
						$resp[$tmptblname] = $tmptbldetails['query'];
					}
				}
				if ($noofrows==0) {
					$resp['data'] = array();
					return json_encode($resp);
				}
				if ($outputformat == 'JSONPAGED') {
					$rowsperpage = GlobalVariable::getVariable('Report_ListView_PageSize', 40);
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
				if ($this->islastpage && $this->page!=1) {
					$resp['next_page_url'] = null;
				} else {
					$resp['next_page_url'] = 'index.php?module=Reports&action=ReportsAjax&file=getJSON&record='.$this->reportid.'&page='.($this->islastpage ? $this->page : $this->page+1);
				}
				$resp['prev_page_url'] = 'index.php?module=Reports&action=ReportsAjax&file=getJSON&record='.$this->reportid.'&page='.($this->page == 1 ? 1 : $this->page-1);
				$custom_field_values = $adb->fetch_array($result);
				$groupslist = $this->getGroupingList($this->reportid);
				$header = array();
				for ($x=0; $x<$fldcnt; $x++) {
					$fld = $adb->field_name($result, $x);
					if ($fld->name=='LBL_ACTION') {
						$module = 'Reports';
						$fieldLabel = 'LBL_ACTION';
					} elseif (strpos($fld->name, '_')) {
						list($module, $fieldLabel) = explode('_', $fld->name, 2);
					} else {
						$module = $this->primarymodule;
						$fieldLabel = $fld->name;
					}
					$fieldInfo = getFieldByReportLabel($module, $fieldLabel);
					if (!empty($fieldInfo)) {
						$field = WebserviceField::fromArray($adb, $fieldInfo);
					}
					if (!empty($fieldInfo)) {
						$headerLabel = $field->getFieldLabelKey();
					} else {
						$headerLabel = str_replace('_', ' ', $fieldLabel);
					}
					if (empty($headerLabel)) {
						$headerLabel = str_replace('_', ' ', $fld->name);
					}
					if ($module != $this->primarymodule && !empty($this->secondarymodule) && in_array($module, $modules_selected)) {
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
						if ($fieldvalue == '') {
							$fieldvalue = '-';
						} elseif ($fld->name == 'LBL_ACTION' && $fieldvalue != '-') {
							$fieldvalue = 'index.php?module='.$this->primarymodule."&action=DetailView&record={$fieldvalue}";
						}
						if ($header[$i]=='LBL ACTION') {
							$datarow['reportrowaction'] = $fieldvalue;
						} else {
							$datarow[$header[$i]] = $fieldvalue;
						}
					}
					$data[] = $datarow;
					set_time_limit($php_max_execution_time);
				} while ($custom_field_values = $adb->fetch_array($result));
				$resp['data'] = $data;
				return json_encode($resp);
			}
		} elseif ($outputformat == 'PDF') {
			$sSQL = $this->sGetSQLforReport($this->reportid, $filtersql);
			$result = $adb->pquery($sSQL, array());
			$tmptables = $this->queryPlanner->getTemporaryTables();
			if (!$result && count($tmptables)>0) {
				$this->queryPlanner->disableTempTables();
				$sSQL = $this->sGetSQLforReport($this->reportid, $filtersql);
				$result = $adb->pquery($sSQL, array());
			}
			if (!$userprivs->hasGlobalReadPermission()) {
				$picklistarray = $this->getAccessPickListValues();
			}
			if ($result) {
				$y=$adb->num_fields($result);
				$noofrows = $adb->num_rows($result);
				$this->number_of_rows = $noofrows;
				$custom_field_values = $adb->fetch_array($result);
				$ILF = new InventoryLineField();
				$invMods = getInventoryModules();
				$arr_val = array();
				do {
					$arraylists = array();
					for ($i=0; $i<$y-1; $i++) { //No tratamos la última columna por ser el ACTION con el CRMID.
						$field = null;
						$fld = $adb->field_name($result, $i);
						list($module, $fieldLabel) = explode('_', $fld->name, 2);
						$fieldInfo = getFieldByReportLabel($module, $fieldLabel);
						if (!empty($fieldInfo)) {
							$field = WebserviceField::fromArray($adb, $fieldInfo);
						} else {
							if (in_array($module, $invMods)) {
								if (substr($fld->table, 0, 26) == 'vtiger_inventoryproductrel') {
									foreach ($ILF->getInventoryLineFieldsByName() as $ilfinfo) {
										$ilflabel = getTranslatedString($ilfinfo['fieldlabel'], $module);
										if ($ilflabel==$fieldLabel) {
											$fieldInfo = $ilfinfo;
											$fieldInfo['tabid'] = getTabid($module);
											$fieldInfo['presence'] = 1;
											$field = WebserviceField::fromArray($adb, $fieldInfo);
											break;
										}
									}
								} elseif (substr($fld->table, 0, 15) == 'vtiger_products' || substr($fld->table, 0, 14) == 'vtiger_service') {
									foreach ($ILF->getInventoryLineProductServiceNameFields() as $ilfinfo) {
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
						if (!empty($fieldInfo)) {
							$headerLabel = getTranslatedString($field->getFieldLabelKey(), $module);
						} else {
							$headerLabel = getTranslatedString(str_replace('_', ' ', $fieldLabel), $module);
						}
						/*STRING TRANSLATION starts */
						$moduleLabel ='';
						if (in_array($module, $modules_selected)) {
							$moduleLabel = getTranslatedString($module, $module);
						}

						if (empty($headerLabel)) {
							$headerLabel = getTranslatedString(str_replace('_', ' ', $fld->name));
						}
						if ($moduleLabel != $this->primarymodule && !empty($this->secondarymodule) && $moduleLabel != '') {
							$headerLabel = $module.' '.$headerLabel;
						}
						$fieldvalue = getReportFieldValue($this, $picklistarray, $fld, $custom_field_values, $i);
						if (empty($returnfieldinfo[$headerLabel]) && !empty($field)) {
							$returnfieldinfo[$headerLabel] = $field;
						}
						$arraylists[$headerLabel] = $fieldvalue;
					}
					$arr_val[] = $arraylists;
					set_time_limit($php_max_execution_time);
				} while ($custom_field_values = $adb->fetch_array($result));

				return $arr_val;
			}
		} elseif ($outputformat == 'TOTALXLS') {
			$escapedchars = array('_SUM','_AVG','_MIN','_MAX');
			$totalpdf=array();
			$sSQL = $this->sGetSQLforReport($this->reportid, $filtersql, 'COLUMNSTOTOTAL');
			if (isset($this->totallist) && count($this->totallist)>0 && $sSQL != '') {
				$result = $adb->query($sSQL);
				$y=$adb->num_fields($result);
				$custom_field_values = $adb->fetch_array($result);

				foreach ($this->totallist as $key => $value) {
					$fieldlist = explode(':', $key);
					$mod_query = $adb->pquery(
						'SELECT distinct(tabid) as tabid, uitype as uitype from vtiger_field where tablename=? and columnname=?',
						array($fieldlist[1], $fieldlist[2])
					);
					if ($adb->num_rows($mod_query)>0) {
						$module_name = getTabModuleName($adb->query_result($mod_query, 0, 'tabid'));
						$fieldlabel = trim(str_replace($escapedchars, ' ', $fieldlist[3]));
						$fieldlabel = str_replace('_', ' ', $fieldlabel);
						if ($module_name) {
							$field = getTranslatedString($module_name, $module_name).' '.getTranslatedString($fieldlabel, $module_name);
						} else {
							$field = getTranslatedString($fieldlabel);
						}
					}
					$uitype_arr[str_replace($escapedchars, ' ', $module_name.'_'.$fieldlist[3])] = $adb->query_result($mod_query, 0, 'uitype');
					$totclmnflds[str_replace($escapedchars, ' ', $module_name.'_'.$fieldlist[3])] = $field;
				}
				for ($i =0; $i<$y; $i++) {
					$fld = $adb->field_name($result, $i);
					$keyhdr[$fld->name] = $custom_field_values[$i];
				}

				$rowcount=0;
				foreach ($totclmnflds as $key => $value) {
					$col_header = trim(str_replace($modules, ' ', $value));
					$fld_name_1 = $this->primarymodule . '_' . trim($value);
					$fld_name_2 = $this->secondarymodule . '_' . trim($value);
					if ($uitype_arr[$key] == 71 || $uitype_arr[$key] == 72 ||
						in_array($fld_name_1, $this->append_currency_symbol_to_value) || in_array($fld_name_2, $this->append_currency_symbol_to_value)
					) {
						$col_header .= ' ('.$app_strings['LBL_IN'].' '.$current_user->currency_symbol.')';
						$convert_price = true;
					} else {
						$convert_price = false;
					}
					$value = decode_html(trim($key));
					$arraykey = $value.'_SUM';
					if (isset($keyhdr[$arraykey])) {
						if ($convert_price) {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey]);
						} else {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey], null, true);
						}
						if (substr($arraykey, 0, 21)=='Timecontrol_TotalTime' || substr($arraykey, 0, 18)=='TCTotals_TotalTime') {
							$conv_value=$keyhdr[$arraykey];
						}
							$totalpdf[$rowcount][$arraykey] = $conv_value;
					} else {
							$totalpdf[$rowcount][$arraykey] = '';
					}

					$arraykey = $value.'_AVG';
					if (isset($keyhdr[$arraykey])) {
						if ($convert_price) {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey]);
						} else {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey], null, true);
						}
						$totalpdf[$rowcount][$arraykey] = $conv_value;
					} else {
							$totalpdf[$rowcount][$arraykey] = '';
					}

					$arraykey = $value.'_MIN';
					if (isset($keyhdr[$arraykey])) {
						if ($convert_price) {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey]);
						} else {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey], null, true);
						}
						$totalpdf[$rowcount][$arraykey] = $conv_value;
					} else {
						$totalpdf[$rowcount][$arraykey] = '';
					}

					$arraykey = $value.'_MAX';
					if (isset($keyhdr[$arraykey])) {
						if ($convert_price) {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey]);
						} else {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey], null, true);
						}
						$totalpdf[$rowcount][$arraykey] = $conv_value;
					} else {
						$totalpdf[$rowcount][$arraykey] = '';
					}
					$rowcount++;
				}
			}
			return $totalpdf;
		} elseif ($outputformat == 'TOTALHTML') {
			$escapedchars = array('_SUM','_AVG','_MIN','_MAX');
			$sSQL = $this->sGetSQLforReport($this->reportid, $filtersql, 'COLUMNSTOTOTAL');
			$coltotalhtml = '';
			if (isset($this->totallist) && count($this->totallist)>0 && $sSQL != '') {
				$result = $adb->query($sSQL);
				$y=$adb->num_fields($result);
				$custom_field_values = $adb->fetch_array($result);
				$coltotalhtml = "<table align='center' width='60%' cellpadding='3' cellspacing='0' border='0' class='rptTable'><tr><td class='rptCellLabel'>"
					.$mod_strings['Totals']."</td><td class='rptCellLabel'>".$mod_strings['SUM']."</td><td class='rptCellLabel'>".$mod_strings['AVG']
					."</td><td class='rptCellLabel'>".$mod_strings['MIN']."</td><td class='rptCellLabel'>".$mod_strings['MAX'].'</td></tr>';

				// Performation Optimization: If Direct output is desired
				if ($directOutput) {
					echo $coltotalhtml;
					$coltotalhtml = '';
				}

				foreach ($this->totallist as $key => $value) {
					$fieldlist = explode(':', $key);
					$mod_query = $adb->pquery(
						'SELECT distinct(tabid) as tabid, uitype as uitype from vtiger_field where tablename = ? and columnname=?',
						array($fieldlist[1], $fieldlist[2])
					);
					if ($adb->num_rows($mod_query)>0) {
						$module_name = getTabModuleName($adb->query_result($mod_query, 0, 'tabid'));
						$fieldlabel = trim(str_replace($escapedchars, ' ', $fieldlist[3]));
						$fieldlabel = str_replace('_', ' ', $fieldlabel);
						if ($module_name) {
							$field = getTranslatedString($module_name, $module_name).' '.getTranslatedString($fieldlabel, $module_name);
						} else {
							$field = getTranslatedString($fieldlabel);
						}
					}
					$uitype_arr[str_replace($escapedchars, ' ', $module_name.'_'.$fieldlist[3])] = $adb->query_result($mod_query, 0, 'uitype');
					$totclmnflds[str_replace($escapedchars, ' ', $module_name.'_'.$fieldlist[3])] = $field;
				}
				for ($i =0; $i<$y; $i++) {
					$fld = $adb->field_name($result, $i);
					$keyhdr[$fld->name] = $custom_field_values[$i];
				}

				foreach ($totclmnflds as $key => $value) {
					$coltotalhtml .= '<tr class="rptGrpHead" valign=top>';
					$col_header = trim(str_replace($modules, ' ', $value));
					$fld_name_1 = $this->primarymodule . '_' . trim($value);
					$fld_name_2 = $this->secondarymodule . '_' . trim($value);
					if ($uitype_arr[$key]==71 || $uitype_arr[$key] == 72 ||
						in_array($fld_name_1, $this->append_currency_symbol_to_value) || in_array($fld_name_2, $this->append_currency_symbol_to_value)
					) {
						$col_header .= ' ('.$app_strings['LBL_IN'].' '.$current_user->currency_symbol.')';
						$convert_price = true;
					} else {
						$convert_price = false;
					}
					$coltotalhtml .= '<td class="rptData">'. $col_header .'</td>';
					$value = decode_html(trim($key));
					$arraykey = $value.'_SUM';
					if (isset($keyhdr[$arraykey])) {
						if ($convert_price) {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey]);
						} else {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey], null, true);
						}
						if (substr($arraykey, 0, 21)=='Timecontrol_TotalTime' || substr($arraykey, 0, 18)=='TCTotals_TotalTime') {
							$conv_value=$keyhdr[$arraykey];
						}
						$coltotalhtml .= '<td class="rptTotal">'.$conv_value.'</td>';
					} else {
						$coltotalhtml .= '<td class="rptTotal">&nbsp;</td>';
					}

					$arraykey = $value.'_AVG';
					if (isset($keyhdr[$arraykey])) {
						if ($convert_price) {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey]);
						} else {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey], null, true);
						}
						if (substr($arraykey, 0, 21)=='Timecontrol_TotalTime' || substr($arraykey, 0, 18)=='TCTotals_TotalTime') {
							$conv_value=$keyhdr[$arraykey];
						}
						$coltotalhtml .= '<td class="rptTotal">'.$conv_value.'</td>';
					} else {
						$coltotalhtml .= '<td class="rptTotal">&nbsp;</td>';
					}

					$arraykey = $value.'_MIN';
					if (isset($keyhdr[$arraykey])) {
						if ($convert_price) {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey]);
						} else {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey], null, true);
						}
						if (substr($arraykey, 0, 21)=='Timecontrol_TotalTime' || substr($arraykey, 0, 18)=='TCTotals_TotalTime') {
							$conv_value=$keyhdr[$arraykey];
						}
						$coltotalhtml .= '<td class="rptTotal">'.$conv_value.'</td>';
					} else {
						$coltotalhtml .= '<td class="rptTotal">&nbsp;</td>';
					}

					$arraykey = $value.'_MAX';
					if (isset($keyhdr[$arraykey])) {
						if ($convert_price) {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey]);
						} else {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey], null, true);
						}
						if (substr($arraykey, 0, 21)=='Timecontrol_TotalTime' || substr($arraykey, 0, 18)=='TCTotals_TotalTime') {
							$conv_value=$keyhdr[$arraykey];
						}
						$coltotalhtml .= '<td class="rptTotal">'.$conv_value.'</td>';
					} else {
						$coltotalhtml .= '<td class="rptTotal">&nbsp;</td>';
					}

					$coltotalhtml .= '<tr>';

					// Performation Optimization: If Direct output is desired
					if ($directOutput) {
						echo $coltotalhtml;
						$coltotalhtml = '';
					}
				}

				$coltotalhtml .= '</table>';

				// Performation Optimization: If Direct output is desired
				if ($directOutput) {
					echo $coltotalhtml;
					$coltotalhtml = '';
				}
			}
			return $coltotalhtml;
		} elseif ($outputformat == 'PRINT') {
			$sSQL = $this->sGetSQLforReport($this->reportid, $filtersql);
			$result = $adb->query($sSQL);
			if (!$userprivs->hasGlobalReadPermission()) {
				$picklistarray = $this->getAccessPickListValues();
			}

			if ($result) {
				$noofrows = $adb->num_rows($result);
				$this->number_of_rows = $noofrows;
				$custom_field_values = $adb->fetch_array($result);
				$groupslist = $this->getGroupingList($this->reportid);
				$y=$adb->num_fields($result);
				$header = '';
				for ($x=0; $x<$y-1; $x++) {
					$fld = $adb->field_name($result, $x);
					list($module, $fieldLabel) = explode('_', $fld->name, 2);
					$fieldInfo = getFieldByReportLabel($module, $fieldLabel);
					if (!empty($fieldInfo)) {
						$field = WebserviceField::fromArray($adb, $fieldInfo);
					}
					if (!empty($fieldInfo)) {
						$headerLabel = getTranslatedString($field->getFieldLabelKey(), $module);
					} else {
						$headerLabel = getTranslatedString(str_replace('_', ' ', $fieldLabel), $module);
					}
					/*STRING TRANSLATION starts */
					$moduleLabel = '';
					if (in_array($module, $modules_selected)) {
						$moduleLabel = getTranslatedString($module, $module);
					}

					if (empty($headerLabel)) {
						$headerLabel = getTranslatedString(str_replace('_', ' ', $fld->name));
					}
					if ($moduleLabel != $this->primarymodule && !empty($this->secondarymodule) && $moduleLabel != '') {
						$headerLabel = $module.' '.$headerLabel;
					}
					$header .= '<th>'.$headerLabel.'</th>';
				}

				$valtemplate = '';
				$lastvalue = '';
				$secondvalue = '';
				$thirdvalue = '';
				$emptycell = "<td style='border-top:1px dotted #FFFFFF;'>&nbsp;</td>";
				do {
					$newvalue = '';
					$snewvalue = '';
					$tnewvalue = '';
					if (count($groupslist) == 1) {
						$newvalue = $custom_field_values[0];
					} elseif (count($groupslist) == 2) {
						$newvalue = $custom_field_values[0];
						$snewvalue = $custom_field_values[1];
					} elseif (count($groupslist) == 3) {
						$newvalue = $custom_field_values[0];
						$snewvalue = $custom_field_values[1];
						$tnewvalue = $custom_field_values[2];
					}

					if ($newvalue == '') {
						$newvalue = '-';
					}
					if ($snewvalue == '') {
						$snewvalue = '-';
					}
					if ($tnewvalue == '') {
						$tnewvalue = '-';
					}
					$valtemplate .= '<tr>';

					for ($i=0; $i<$y-1; $i++) {
						$fld = $adb->field_name($result, $i);
						$fieldvalue = getReportFieldValue($this, $picklistarray, $fld, $custom_field_values, $i);
						if (($lastvalue == $fieldvalue) && $this->reporttype == 'summary') {
							$valtemplate .= $emptycell;
						} elseif (($secondvalue == $fieldvalue) && $this->reporttype == 'summary') {
							if ($lastvalue == $newvalue) {
								$valtemplate .= $emptycell;
							} else {
								$valtemplate .= '<td>'.$fieldvalue.'</td>';
							}
						} elseif (($thirdvalue == $fieldvalue) && $this->reporttype == 'summary') {
							if ($secondvalue == $snewvalue) {
								$valtemplate .= $emptycell;
							} else {
								$valtemplate .= '<td>'.$fieldvalue.'</td>';
							}
						} else {
							$valtemplate .= '<td>'.$fieldvalue.'</td>';
						}
					}
					$valtemplate .= '</tr>';
					$lastvalue = $newvalue;
					$secondvalue = $snewvalue;
					$thirdvalue = $tnewvalue;
					set_time_limit($php_max_execution_time);
				} while ($custom_field_values = $adb->fetch_array($result));

				$sHTML = '<tr>'.$header.'</tr>'.$valtemplate;
				$return_data[] = $sHTML;
				$return_data[] = $noofrows;
				return $return_data;
			}
		} elseif ($outputformat == 'PRINT_TOTAL') {
			$escapedchars = array('_SUM','_AVG','_MIN','_MAX');
			$sSQL = $this->sGetSQLforReport($this->reportid, $filtersql, 'COLUMNSTOTOTAL');
			$coltotalhtml = '';
			if (isset($this->totallist) && count($this->totallist)>0 && $sSQL != '') {
				$result = $adb->query($sSQL);
				$y=$adb->num_fields($result);
				$custom_field_values = $adb->fetch_array($result);

				$coltotalhtml = "<br /><table align='center' width='60%' cellpadding='3' cellspacing='0' border='1' class='printReport'><tr><td class='rptCellLabel'>"
					.$mod_strings['Totals'].'</td><td><b>'.$mod_strings['SUM'].'</b></td><td><b>'.$mod_strings['AVG'].'</b></td><td><b>'.$mod_strings['MIN']
					.'</b></td><td><b>'.$mod_strings['MAX'].'</b></td></tr>';

				// Performation Optimization: If Direct output is desired
				if ($directOutput) {
					echo $coltotalhtml;
					$coltotalhtml = '';
				}

				foreach ($this->totallist as $key => $value) {
					$fieldlist = explode(':', $key);
					$mod_query = $adb->pquery(
						'SELECT distinct(tabid) as tabid, uitype as uitype from vtiger_field where tablename = ? and columnname=?',
						array($fieldlist[1],$fieldlist[2])
					);
					if ($adb->num_rows($mod_query)>0) {
						$module_name = getTabModuleName($adb->query_result($mod_query, 0, 'tabid'));
						$fieldlabel = trim(str_replace($escapedchars, ' ', $fieldlist[3]));
						$fieldlabel = str_replace('_', ' ', $fieldlabel);
						if ($module_name) {
							$field = getTranslatedString($module_name, $module_name).' '.getTranslatedString($fieldlabel, $module_name);
						} else {
							$field = getTranslatedString($fieldlabel);
						}
					}
					$uitype_arr[str_replace($escapedchars, ' ', $module_name.'_'.$fieldlist[3])] = $adb->query_result($mod_query, 0, 'uitype');
					$totclmnflds[str_replace($escapedchars, ' ', $module_name.'_'.$fieldlist[3])] = $field;
				}

				for ($i =0; $i<$y; $i++) {
					$fld = $adb->field_name($result, $i);
					$keyhdr[$fld->name] = $custom_field_values[$i];
				}
				foreach ($totclmnflds as $key => $value) {
					$coltotalhtml .= '<tr class="rptGrpHead">';
					$col_header = getTranslatedString(trim(str_replace($modules, ' ', $value)));
					$fld_name_1 = $this->primarymodule . '_' . trim($value);
					$fld_name_2 = $this->secondarymodule . '_' . trim($value);
					if ($uitype_arr[$key]==71 || $uitype_arr[$key] == 72 ||
						in_array($fld_name_1, $this->append_currency_symbol_to_value) || in_array($fld_name_2, $this->append_currency_symbol_to_value)
					) {
						$col_header .= ' ('.$app_strings['LBL_IN'].' '.$current_user->currency_symbol.')';
						$convert_price = true;
					} else {
						$convert_price = false;
					}
					$coltotalhtml .= '<td class="rptData">'. $col_header .'</td>';
					$value = decode_html(trim($key));
					$arraykey = $value.'_SUM';
					if (isset($keyhdr[$arraykey])) {
						if ($convert_price) {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey]);
						} else {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey], null, true);
						}
						if (substr($arraykey, 0, 21)=='Timecontrol_TotalTime' || substr($arraykey, 0, 18)=='TCTotals_TotalTime') {
							$conv_value=$keyhdr[$arraykey];
						}
						$coltotalhtml .= "<td class='rptTotal'>".$conv_value.'</td>';
					} else {
						$coltotalhtml .= "<td class='rptTotal'>&nbsp;</td>";
					}

					$arraykey = $value.'_AVG';
					if (isset($keyhdr[$arraykey])) {
						if ($convert_price) {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey]);
						} else {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey], null, true);
						}
						$coltotalhtml .= "<td class='rptTotal'>".$conv_value.'</td>';
					} else {
						$coltotalhtml .= "<td class='rptTotal'>&nbsp;</td>";
					}

					$arraykey = $value.'_MIN';
					if (isset($keyhdr[$arraykey])) {
						if ($convert_price) {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey]);
						} else {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey], null, true);
						}
						$coltotalhtml .= "<td class='rptTotal'>".$conv_value.'</td>';
					} else {
						$coltotalhtml .= "<td class='rptTotal'>&nbsp;</td>";
					}

					$arraykey = $value.'_MAX';
					if (isset($keyhdr[$arraykey])) {
						if ($convert_price) {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey]);
						} else {
							$conv_value = CurrencyField::convertToUserFormat($keyhdr[$arraykey], null, true);
						}
						$coltotalhtml .= "<td class='rptTotal'>".$conv_value.'</td>';
					} else {
						$coltotalhtml .= "<td class='rptTotal'>&nbsp;</td>";
					}

					$coltotalhtml .= '</tr>';

					// Performation Optimization: If Direct output is desired
					if ($directOutput) {
						echo $coltotalhtml;
						$coltotalhtml = '';
					}
				}

				$coltotalhtml .= '</table>';
				// Performation Optimization: If Direct output is desired
				if ($directOutput) {
					echo $coltotalhtml;
					$coltotalhtml = '';
				}
			}
			return $coltotalhtml;
		}
	}

	public function getColumnsTotal($reportid, $selectlist = '') {
		// Have we initialized it already?
		if ($this->_columnstotallist !== false) {
			return $this->_columnstotallist;
		}

		global $adb, $log;
		static $modulename_cache = array();
		static $fielduitype_cache = array();
		$coltotalsql = 'select vtiger_reportsummary.* from vtiger_report';
		$coltotalsql .= ' inner join vtiger_reportsummary on vtiger_report.reportid = vtiger_reportsummary.reportsummaryid';
		$coltotalsql .= ' where vtiger_report.reportid =?';

		$result = $adb->pquery($coltotalsql, array($reportid));
		$seltotalcols = array();
		$stdfilterlist = array();
		while ($coltotalrow = $adb->fetch_array($result)) {
			$fieldcolname = $coltotalrow['columnname'];
			if ($fieldcolname != 'none') {
				$sckey = $scval = '';
				$fieldlist = explode(':', $fieldcolname);
				$field_tablename = $fieldlist[1];
				$field_columnname = $fieldlist[2];

				$cachekey = $field_tablename . ':' . $field_columnname;
				if (!isset($modulename_cache[$cachekey])) {
					$mod_query = $adb->pquery(
						'SELECT distinct(tabid) as tabid from vtiger_field where tablename = ? and columnname=?',
						array($fieldlist[1],$fieldlist[2])
					);
					if ($adb->num_rows($mod_query)>0) {
						$module_name = getTabModuleName($adb->query_result($mod_query, 0, 'tabid'));
						$modulename_cache[$cachekey] = $module_name;
					}
				} else {
					$module_name = $modulename_cache[$cachekey];
				}
				if (!isset($fielduitype_cache[$cachekey])) {
					$mod_query = $adb->pquery(
						'SELECT uitype from vtiger_field where tablename=? and columnname=?',
						array($fieldlist[1], $fieldlist[2])
					);
					if ($adb->num_rows($mod_query)>0) {
						$fielduitype_cache[$cachekey] = $adb->query_result($mod_query, 0, 'uitype');
						$fielduitype = $fielduitype_cache[$cachekey];
					} else {
						$fielduitype = 0;
					}
				} else {
					$fielduitype = $fielduitype_cache[$cachekey];
				}

				$field_columnalias = $module_name.'_'.$fieldlist[3];
				$field_columnalias = decode_html($field_columnalias);
				$query_columnalias = substr($field_columnalias, 0, strrpos($field_columnalias, '_'));
				$query_columnalias = str_replace(array(' ','&','(',')'), '_', $query_columnalias);
				$sckey = $field_tablename.':'.$field_columnname.':'.$query_columnalias.':'.$field_columnname.':N'; // vtiger_invoice:subject:Invoice_Subject:subject:V
				$scval = $field_tablename.'.'.$field_columnname." AS '".$query_columnalias."'"; // vtiger_invoice.subject AS 'Invoice_Subject'
				$seltotalcols[$sckey] = $scval;
				$field_permitted = false;
				if (CheckColumnPermission($field_tablename, $field_columnname, $module_name) != 'false') {
					$field_permitted = true;
				}
				if ($field_permitted) {
					if ($field_tablename == 'vtiger_products' && $field_columnname == 'unit_price') {
						// Query needs to be rebuild to get the value in user preferred currency. [innerProduct and actual_unit_price are table and column alias.]
						$query_columnalias = ' actual_unit_price';
						$seltotalcols['innerProduct:actual_unit_price:Products_Unit_Price:actual_unit_price:N'] = 'innerProduct.actual_unit_price AS actual_unit_price';
					}
					if ($field_tablename == 'vtiger_service' && $field_columnname == 'unit_price') {
						// Query needs to be rebuild to get the value in user preferred currency. [innerProduct and actual_unit_price are table and column alias.]
						$query_columnalias = ' actual_unit_price';
						$seltotalcols['innerService:actual_unit_price:Services_Unit_Price:actual_unit_price:N'] = 'innerService.actual_unit_price AS actual_unit_price';
					}
					if (($field_tablename == 'vtiger_invoice' || $field_tablename == 'vtiger_quotes' || $field_tablename == 'vtiger_purchaseorder' || $field_tablename == 'vtiger_salesorder' || $field_tablename == 'vtiger_issuecards')
						&& ($fielduitype == '72' || $field_columnname=='total' || $field_columnname=='subtotal' || $field_columnname=='discount_amount' || $field_columnname=='s_h_amount')
					) {
						$query_columnalias = $query_columnalias.'`/`'.$module_name.'_Conversion_Rate';
						$seltotalcols[$field_tablename.':conversion_rate:'.$module_name.'_Conversion_Rate:conversion_rate:N'] = "$field_tablename.conversion_rate AS $module_name".'_Conversion_Rate ';
					}
					if ($fieldlist[4] == 2) {
						if ($fieldlist[2]=='totaltime') {
							$summinutes = "sum(SUBSTRING_INDEX($query_columnalias, ':', 1)*60+SUBSTRING_INDEX($query_columnalias, ':', -1))";
							$stdfilterlist[$fieldcolname] = "concat(floor($summinutes / 60), ':', floor($summinutes % 60)) '$field_columnalias'";
						} else {
							$stdfilterlist[$fieldcolname] = "sum(`$query_columnalias`) '".$field_columnalias."'";
						}
					}
					if ($fieldlist[4] == 3) {
						//when we use avg() function, NULL values will be ignored.to avoid this we use (sum/count) to find average.
						//$stdfilterlist[$fieldcolname] = "avg(".$fieldlist[1].".".$fieldlist[2].") '".$fieldlist[3]."'";
						if ($fieldlist[2]=='totaltime') {
							$avgminutes = "sum(SUBSTRING_INDEX($query_columnalias, ':', 1)*60+SUBSTRING_INDEX($query_columnalias, ':', -1))/count(*)";
							$stdfilterlist[$fieldcolname] = "concat(floor($avgminutes / 60), ':', floor($avgminutes % 60)) '$field_columnalias'";
						} else {
							$stdfilterlist[$fieldcolname] = "(sum(`$query_columnalias`)/count(*)) '".$field_columnalias."'";
						}
					}
					if ($fieldlist[4] == 4) {
						if ($fieldlist[2]=='totaltime') {
							$minminutes = "min(SUBSTRING_INDEX($query_columnalias, ':', 1)*60+SUBSTRING_INDEX($query_columnalias, ':', -1))";
							$stdfilterlist[$fieldcolname] = "concat(floor($minminutes / 60), ':', floor($minminutes % 60)) '$field_columnalias'";
						} else {
							$stdfilterlist[$fieldcolname] = "min(`$query_columnalias`) '".$field_columnalias."'";
						}
					}
					if ($fieldlist[4] == 5) {
						if ($fieldlist[2]=='totaltime') {
							$maxminutes = "max(SUBSTRING_INDEX($query_columnalias, ':', 1)*60+SUBSTRING_INDEX($query_columnalias, ':', -1))";
							$stdfilterlist[$fieldcolname] = "concat(floor($maxminutes / 60), ':', floor($maxminutes % 60)) '$field_columnalias'";
						} else {
							$stdfilterlist[$fieldcolname] = "max(`$query_columnalias`) '".$field_columnalias."'";
						}
					}
				}
			}
		}
		// Save the information
		$this->_columnstotallist = $stdfilterlist;
		$stc = array_diff($seltotalcols, $selectlist);
		$this->_columnstotallistaddtoselect = $stc;
		$log->debug('ReportRun :: getColumnsTotal'.$reportid);
		return $stdfilterlist;
	}

	/** function to get query for the columns to total for the given reportid
	 * @param integer report id
	 * @return string columnstoTotal query for the reportid
	 */
	public function getColumnsToTotalColumns($reportid) {
		global $adb, $log;

		$sreportstdfiltersql = 'select vtiger_reportsummary.* from vtiger_report';
		$sreportstdfiltersql .= ' inner join vtiger_reportsummary on vtiger_report.reportid = vtiger_reportsummary.reportsummaryid';
		$sreportstdfiltersql .= ' where vtiger_report.reportid =?';

		$result = $adb->pquery($sreportstdfiltersql, array($reportid));
		$noofrows = $adb->num_rows($result);

		for ($i=0; $i<$noofrows; $i++) {
			$fieldcolname = $adb->query_result($result, $i, 'columnname');

			if ($fieldcolname != 'none') {
				$fieldlist = explode(':', $fieldcolname);
				if ($fieldlist[4] == 2) {
					$sSQLList[] = 'sum('.$fieldlist[1].'.'.$fieldlist[2].') '.$fieldlist[3];
				}
				if ($fieldlist[4] == 3) {
					$sSQLList[] = 'avg('.$fieldlist[1].'.'.$fieldlist[2].') '.$fieldlist[3];
				}
				if ($fieldlist[4] == 4) {
					$sSQLList[] = 'min('.$fieldlist[1].'.'.$fieldlist[2].') '.$fieldlist[3];
				}
				if ($fieldlist[4] == 5) {
					$sSQLList[] = 'max('.$fieldlist[1].'.'.$fieldlist[2].') '.$fieldlist[3];
				}
			}
		}
		if (isset($sSQLList)) {
			$sSQL = implode(',', $sSQLList);
		}
		$log->debug('ReportRun :: getColumnsToTotalColumns'.$reportid);
		return $sSQL;
	}

	/** Function to get picklist value array based on profile returns permitted fields in array format */
	public function getAccessPickListValues() {
		global $adb, $current_user;
		$id = array(getTabid($this->primarymodule));
		if ($this->secondarymodule != '') {
			$id[] = getTabid($this->secondarymodule);
		}

		$query = 'select fieldname,columnname,fieldid,fieldlabel,tabid,uitype
			from vtiger_field
			where tabid in('. generateQuestionMarks($id) .") and uitype in (15,33,55) and columnname != 'firstname'";
		$result = $adb->pquery($query, $id);
		$roleid=$current_user->roleid;
		$roleids = getRoleSubordinates($roleid);
		$roleids[] = $roleid;
		$roleidslist = implode('","', $roleids);
		$temp_status = array();
		$fieldlists = array();
		for ($i=0; $i < $adb->num_rows($result); $i++) {
			$fieldname = $adb->query_result($result, $i, 'fieldname');
			$fieldlabel = $adb->query_result($result, $i, 'fieldlabel');
			$tabid = $adb->query_result($result, $i, 'tabid');
			$uitype = $adb->query_result($result, $i, 'uitype');

			$fieldlabel1 = str_replace(' ', '_', $fieldlabel);
			$keyvalue = getTabModuleName($tabid).'_'.$fieldlabel1;
			$fieldvalues = array();
			$mulsel = "select distinct $fieldname
				from vtiger_$fieldname
				inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_$fieldname.picklist_valueid
				where roleid in (\"". $roleidslist .'") and picklistid in (select picklistid from vtiger_picklist)'; // order by sortid asc - not requried
			$mulselresult = $adb->query($mulsel);
			for ($j=0; $j < $adb->num_rows($mulselresult); $j++) {
				$fldvalue = $adb->query_result($mulselresult, $j, $fieldname);
				if (in_array($fldvalue, $fieldvalues)) {
					continue;
				}
				$fieldvalues[] = $fldvalue;
			}
			$field_count = count($fieldvalues);
			if ($uitype == 15 && $field_count > 0 && ($fieldname == 'taskstatus' || $fieldname == 'eventstatus')) {
				$temp_count = (empty($temp_status[$keyvalue]) ? 0 : count($temp_status[$keyvalue]));
				if ($temp_count > 0) {
					for ($t=0; $t < $field_count; $t++) {
						$temp_status[$keyvalue][($temp_count+$t)] = $fieldvalues[$t];
					}
					$fieldvalues = $temp_status[$keyvalue];
				} else {
					$temp_status[$keyvalue] = $fieldvalues;
				}
			}

			if ($uitype == 33) {
				$fieldlists[1][$keyvalue] = $fieldvalues;
			} elseif ($uitype == 15) {
				$fieldlists[$keyvalue] = $fieldvalues;
			}
		}
		return $fieldlists;
	}

	public function getReportPDF($filterlist = false) {
		require_once 'include/tcpdf/tcpdf.php';

		$arr_val = $this->GenerateReport('PDF', $filterlist);

		if (isset($arr_val)) {
			foreach ($arr_val as $wkey => $warray_value) {
				$w_inner_array = array();
				foreach ($warray_value as $whd => $wvalue) {
					if (strlen($wvalue) < strlen($whd)) {
						$w_inner_array[] = strlen($whd);
					} else {
						$w_inner_array[] = strlen($wvalue);
					}
				}
				$warr_val[] = $w_inner_array;
				unset($w_inner_array);
			}
			$farr_val = array();
			foreach ($warr_val[0] as $fkey => $fvalue) {
				foreach ($warr_val as $wkey => $wvalue) {
					$f_inner_array[] = $warr_val[$wkey][$fkey];
				}
				sort($f_inner_array, 1);
				$farr_val[] = $f_inner_array;
				unset($f_inner_array);
			}
			$col_width = array();
			foreach ($farr_val as $skvalue) {
				if ($skvalue[count($arr_val)-1] == 1) {
					$col_width[] = ($skvalue[count($arr_val)-1] * 35);
				} else {
					$col_width[] = ($skvalue[count($arr_val)-1] * 6) + 10 ;
				}
			}
			$count = 0;
			$headerHTML = '';
			foreach ($arr_val[0] as $key => $value) {
				$headerHTML .= '<td width="'.$col_width[$count].'" bgcolor="#DDDDDD"><b>'.$key.'</b></td>';
				$count = $count + 1;
			}
			$dataHTML = '';
			foreach ($arr_val as $key => $array_value) {
				$valueHTML = '';
				$count = 0;
				foreach ($array_value as $value) {
					$valueHTML .= '<td width="'.$col_width[$count].'">'.$value.'</td>';
					$count = $count + 1;
				}
				$dataHTML .= '<tr>'.$valueHTML.'</tr>';
			}
		}

		$totalpdf = $this->GenerateReport('PRINT_TOTAL', $filterlist);
		$report_header = GlobalVariable::getVariable('Report_HeaderOnPDF', '');
		($report_header == 1 ? $html = '<h1>'.getTranslatedString($this->reportname).'</h1>': $html = '');
		$html = $html.'<table border="1"><tr>'.$headerHTML.'</tr>'.$dataHTML.'</table>';
		$columnlength = array_sum($col_width);
		if ($columnlength > 14400) {
			global $log, $app_strings;
			$log->fatal('PDF REPORT GENERATION: '.$app_strings['LBL_PDF']);
			$columnlength = 14400;
		}
		if ($columnlength <= 420) {
			$pdf = new TCPDF('P', 'mm', 'A5', true);
		} elseif ($columnlength >= 421 && $columnlength <= 800) {
			$pdf = new TCPDF('L', 'mm', 'A4', true);
		} elseif ($columnlength >= 801 && $columnlength <= 1120) {
			$pdf = new TCPDF('L', 'mm', 'A3', true);
		} elseif ($columnlength >=1121 && $columnlength <= 1600) {
			$pdf = new TCPDF('L', 'mm', 'A2', true);
		} elseif ($columnlength >=1601 && $columnlength <= 2200) {
			$pdf = new TCPDF('L', 'mm', 'A1', true);
		} elseif ($columnlength >=2201 && $columnlength <= 3370) {
			$pdf = new TCPDF('L', 'mm', 'A0', true);
		} elseif ($columnlength >=3371 && $columnlength <= 4690) {
			$pdf = new TCPDF('L', 'mm', '2A0', true);
		} elseif ($columnlength >=4691 && $columnlength <= 6490) {
			$pdf = new TCPDF('L', 'mm', '4A0', true);
		} else {
			$columnhight = count($arr_val)*15;
			$format = array($columnhight,$columnlength);
			$pdf = new TCPDF('L', 'mm', $format, true);
		}
		$pdf->SetMargins(10, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$pdf->AddPage();

		$pdf->SetFillColor(224, 235, 255);
		$pdf->SetTextColor(0);
		$pdf->SetFont('FreeSerif', 'B', 14);
		$pdf->Cell(($columnlength*50), 10, getTranslatedString($this->reportname), 0, 0, 'C', 0);
		$pdf->Ln();

		$pdf->SetFont('FreeSerif', '', 10);

		$pdf->writeHTML($html.$totalpdf);

		return $pdf;
	}

	public function writeReportToExcelFile($fileName, $filterlist = '', $modulename = '') {
		$fieldinfo = array();
		$arr_val = $this->GenerateReport('PDF', $filterlist, false, $fieldinfo);
		$totalxls = $this->GenerateReport('TOTALXLS', $filterlist);
		require_once 'include/utils/ExportUtils.php';
		$workbook = exportExcelFileRows($arr_val, $totalxls, $this->reportname, $fieldinfo);
		$workbookWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($workbook, 'Xls');
		$workbookWriter->save($fileName);
	}

	public function writeReportToCSVFile($fileName, $filterlist = '') {
		$arr_val = $this->GenerateReport('PDF', $filterlist);
		$fp = fopen($fileName, 'w+');
		if (isset($arr_val)) {
			$CSV_Separator = GlobalVariable::getVariable('Export_Field_Separator_Symbol', ',', $this->primarymodule);
			$csv_values = array();
			// Header
			$csv_values = array_keys($arr_val[0]);
			fputcsv($fp, $csv_values, $CSV_Separator);
			foreach ($arr_val as $array_value) {
				$csv_values = array_map('decode_html', array_values($array_value));
				fputcsv($fp, $csv_values, $CSV_Separator);
			}
		}
		fclose($fp);
	}

	public function getGroupByTimeList($reportId) {
		global $adb;
		$groupByTimeRes = $adb->pquery('SELECT * FROM vtiger_reportgroupbycolumn WHERE reportid=?', array($reportId));
		$num_rows = $adb->num_rows($groupByTimeRes);
		$groupByCondition = array();
		for ($i=0; $i<$num_rows; $i++) {
			$sortColName = $adb->query_result($groupByTimeRes, $i, 'sortcolname');
			list($tablename,$colname,$module_field,$fieldname,$single) = explode(':', $sortColName);
			$groupField = $module_field;
			$groupCriteria = $adb->query_result($groupByTimeRes, $i, 'dategroupbycriteria');
			if (in_array($groupCriteria, array_keys($this->groupByTimeParent))) {
				$parentCriteria = $this->groupByTimeParent[$groupCriteria];
				foreach ($parentCriteria as $criteria) {
					$groupByCondition[]=$this->GetTimeCriteriaCondition($criteria, $groupField);
				}
			}
			$groupByCondition[] = $this->GetTimeCriteriaCondition($groupCriteria, $groupField);
			$this->queryPlanner->addTable($tablename);
		}
		return $groupByCondition;
	}

	public function GetTimeCriteriaCondition($criteria, $dateField) {
		$condition = '';
		if (strtolower($criteria)=='year') {
			$condition = "DATE_FORMAT($dateField, '%Y' )";
		} elseif (strtolower($criteria)=='month') {
			$condition = "CEIL(DATE_FORMAT($dateField,'%m')%13)";
		} elseif (strtolower($criteria)=='quarter') {
			$condition = "CEIL(DATE_FORMAT($dateField,'%m')/3)";
		}
		if (strtolower($criteria)=='day') {
			$condition = "DATE_FORMAT($dateField,'%d')";
		}
		return $condition;
	}

	public function GetFirstSortByField($reportid) {
		global $adb;
		$groupByField = '';
		$sortFieldQuery = "SELECT * FROM vtiger_reportsortcol
			LEFT JOIN vtiger_reportgroupbycolumn ON (vtiger_reportsortcol.sortcolid = vtiger_reportgroupbycolumn.sortid
				and vtiger_reportsortcol.reportid = vtiger_reportgroupbycolumn.reportid)
			WHERE columnname!='none' and vtiger_reportsortcol.reportid=? ORDER By sortcolid";
		$sortFieldResult= $adb->pquery($sortFieldQuery, array($reportid));
		if ($adb->num_rows($sortFieldResult)>0) {
			$fieldcolname = $adb->query_result($sortFieldResult, 0, 'columnname');
			list($tablename,$colname,$module_field,$fieldname,$typeOfData) = explode(':', $fieldcolname);
			list($modulename,$fieldlabel) = explode('_', $module_field, 2);
			$groupByField = '`'.ReportRun::replaceSpecialChar($module_field).'`';
			if ($typeOfData == 'D') {
				$groupCriteria = $adb->query_result($sortFieldResult, 0, 'dategroupbycriteria');
				if (strtolower($groupCriteria)!='none') {
					if (in_array($groupCriteria, array_keys($this->groupByTimeParent))) {
						$parentCriteria = $this->groupByTimeParent[$groupCriteria];
						foreach ($parentCriteria as $criteria) {
							$groupByCondition[]=$this->GetTimeCriteriaCondition($criteria, $groupByField);
						}
					}
					$groupByCondition[] = $this->GetTimeCriteriaCondition($groupCriteria, $groupByField);
					$groupByField = implode(', ', $groupByCondition);
				}
			} elseif (CheckFieldPermission($fieldname, $modulename) != 'true') {
				if (strpos($tablename, 'vtiger_inventoryproductrel') === false && ($colname != 'productid' || $colname != 'serviceid')) {
					$groupByField = $tablename.'.'.$colname;
				}
			}
		}
		return $groupByField;
	}

	public function getReferenceFieldColumnList($moduleName, $fieldInfo) {
		$adb = PearDatabase::getInstance();

		$columnsSqlList = array();

		$fieldInstance = WebserviceField::fromArray($adb, $fieldInfo);
		$referenceModuleList = $fieldInstance->getReferenceList();
		$reportSecondaryModules = explode(':', $this->secondarymodule);

		if ($moduleName != $this->primarymodule && in_array($this->primarymodule, $referenceModuleList)) {
			$entityTableFieldNames = getEntityFieldNames($this->primarymodule);
			$entityTableName = $entityTableFieldNames['tablename'];
			$entityFieldNames = $entityTableFieldNames['fieldname'];

			$columnList = array();
			if (is_array($entityFieldNames)) {
				foreach ($entityFieldNames as $entityColumnName) {
					$columnList["$entityColumnName"] = "$entityTableName.$entityColumnName";
				}
			} else {
				$columnList[] = "$entityTableName.$entityFieldNames";
			}
			if (count($columnList) > 1) {
				$columnSql = getSqlForNameInDisplayFormat($columnList, $this->primarymodule);
			} else {
				$columnSql = implode('', $columnList);
			}
			$columnsSqlList[] = $columnSql;
		} else {
			foreach ($referenceModuleList as $referenceModule) {
				$entityTableFieldNames = getEntityFieldNames($referenceModule);
				$entityTableName = $entityTableFieldNames['tablename'];
				$entityFieldNames = $entityTableFieldNames['fieldname'];

				if ($moduleName == 'HelpDesk' && $referenceModule == 'Accounts') {
					$referenceTableName = 'vtiger_accountRelHelpDesk';
				} elseif ($moduleName == 'HelpDesk' && $referenceModule == 'Contacts') {
					$referenceTableName = 'vtiger_contactdetailsRelHelpDesk';
				} elseif ($moduleName == 'Contacts' && $referenceModule == 'Accounts') {
					$referenceTableName = 'vtiger_accountContacts';
				} elseif ($moduleName == 'Contacts' && $referenceModule == 'Contacts') {
					$referenceTableName = 'vtiger_contactdetailsContacts';
				} elseif ($moduleName == 'Accounts' && $referenceModule == 'Accounts') {
					$referenceTableName = 'vtiger_accountAccounts';
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
				} elseif (in_array($referenceModule, $reportSecondaryModules) && $moduleName != 'Timecontrol') {
					if ($fieldInstance->getFieldId() != '') {
						$referenceTableName = "{$entityTableName}Rel{$moduleName}{$fieldInstance->getFieldId()}";
					} else {
						$referenceTableName = "{$entityTableName}Rel$referenceModule";
					}
				} else {
					$referenceTableName = "{$entityTableName}Rel{$moduleName}{$fieldInstance->getFieldId()}";
					$dependentTableName = "vtiger_crmentityRel{$moduleName}{$fieldInstance->getFieldId()}";
				}
				$this->queryPlanner->addTable($referenceTableName);

				if (isset($dependentTableName)) {
					$this->queryPlanner->addTable($dependentTableName);
				}
				$columnList = array();
				if (is_array($entityFieldNames)) {
					foreach ($entityFieldNames as $entityColumnName) {
						$columnList["$entityColumnName"] = "$referenceTableName.$entityColumnName";
					}
				} else {
					$columnList[] = "$referenceTableName.$entityFieldNames";
				}
				if (count($columnList) > 1) {
					$columnSql = getSqlForNameInDisplayFormat($columnList, $referenceModule);
				} else {
					$columnSql = implode('', $columnList);
				}
				if ($referenceModule == 'DocumentFolders' && $fieldInstance->getFieldName() == 'folderid') {
					$columnSql = 'vtiger_attachmentsfolder.foldername';
					$this->queryPlanner->addTable('vtiger_attachmentsfolder');
				}
				if ($referenceModule == 'Currency' && $fieldInstance->getFieldName() == 'currency_id') {
					$columnSql = "vtiger_currency_info$moduleName.currency_name";
					$this->queryPlanner->addTable("vtiger_currency_info$moduleName");
				}
				$columnsSqlList[] = $columnSql;
			}
		}
		return $columnsSqlList;
	}
}
?>
