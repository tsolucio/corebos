<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

/**	function used to get the permitted blocks
 *	@param string $module - module name
 *	@param string $disp_view - view name, this may be create_view, edit_view or detail_view
 *	@return string $blockid_list - list of block ids within the paranthesis with comma seperated
 */
function getPermittedBlocks($module, $disp_view) {
	global $adb, $log;
	$log->debug("> getPermittedBlocks $module, $disp_view");

	$tabid = getTabid($module);
	$query="select blockid,blocklabel,show_title from vtiger_blocks where tabid=? and $disp_view=0 and visible = 0 order by sequence";
	$result = $adb->pquery($query, array($tabid));
	$noofrows = $adb->num_rows($result);
	$blockid_list ='(';
	for ($i=0; $i<$noofrows; $i++) {
		$blockid = $adb->query_result($result, $i, 'blockid');
		if ($i != 0) {
			$blockid_list .= ', ';
		}
		$blockid_list .= $blockid;
	}
	$blockid_list .= ')';

	$log->debug('< getPermittedBlocks '.$blockid_list);
	return $blockid_list;
}

/**	function used to get the query which will list the permitted fields
 *	@param string $module - module name
 *	@param string $disp_view - view name, this may be create_view, edit_view or detail_view
 *	@return string $sql - query to get the list of fields which are permitted to the current user
 */
function getPermittedFieldsQuery($module, $disp_view) {
	global $log, $current_user;
	$log->debug("> getPermittedFieldsQuery $module, $disp_view");

	$userprivs = $current_user->getPrivileges();

	//To get the permitted blocks
	$blockid_list = getPermittedBlocks($module, $disp_view);

	$tabid = getTabid($module);
	if ($userprivs->hasGlobalReadPermission() || $module == 'Users') {
		$sql = 'SELECT vtiger_field.columnname, vtiger_field.fieldlabel, vtiger_field.tablename
			FROM vtiger_field
			WHERE vtiger_field.tabid='.$tabid." AND vtiger_field.block IN $blockid_list AND vtiger_field.displaytype IN (1,2,4) and vtiger_field.presence in (0,2)
			ORDER BY block,sequence";
	} else {
		$profileList = getCurrentUserProfileList();
		$sql = 'SELECT vtiger_field.columnname, vtiger_field.fieldlabel, vtiger_field.tablename
			FROM vtiger_field
			INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid
			INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid
			WHERE vtiger_field.tabid='.$tabid.' AND vtiger_field.block IN '.$blockid_list.' AND vtiger_field.displaytype IN (1,2,4)
				and vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0 AND vtiger_profile2field.profileid IN ('. implode(',', $profileList) .')
				and vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid
			ORDER BY block,sequence';
	}

	$log->debug('< getPermittedFieldsQuery '.$sql);
	return $sql;
}

/**
 * Function to export specific user comments.
 * @param string $module - module name
 * @param integer $recordid - record number
 * @param array $field_arr - list of fields to export
 * @return object rows in either CSV or XLS format to export
 */
function exportUserCommentsForModule($module, $recordid, $format, $field_arr = array('commentcontent', 'createdtime')) {
	global $adb, $current_user;
	$mapname = 'Comments_Export_Columns';
	if (!empty($recordid)) {
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$mapname, cbMap::getMapIdByName($mapname));
		if ($cbMapid) {
			$cbMap = cbMap::getMapByID($cbMapid);
			$arr = $cbMap->FieldSetMapping()->getFieldSetModule('ModComments');
			$field_arr = array();
			for ($i=0; $i<sizeof($arr); $i++) {
				$field_arr[] = $arr[$i]['name'];
			}
		}
		$queryGenerator = new QueryGenerator('ModComments', $current_user);
		$queryGenerator->setFields($field_arr);
		$queryGenerator->addReferenceModuleFieldCondition($module, 'related_to', 'id', $recordid, 'e');
		$query = $queryGenerator->getQuery();
		$queryres = $adb->pquery($query, array());
		$fields_array = $adb->getFieldsArray($queryres);
		$fields_query = getPermittedFieldsQuery('ModComments', 'edit_view');
		$f_list = " vtiger_field.columnname IN ('".implode("','", $fields_array)."') AND ";
		$fields_query = preg_replace('/\sWHERE\s/', ' WHERE'.$f_list, $fields_query);
		$get_fields = getFieldsListFromQuery($fields_query);
		$all_fields = str_replace(',vtiger_crmentity.cbuuid', '', $get_fields);
		$query_sub = substr($query, stripos($query, ' FROM '));
		$new_query = "SELECT $all_fields $query_sub";
		$result_query = $adb->pquery($new_query, array());
		$fields_array_result = $adb->getFieldsArray($result_query);
		$columnsToExport = array_map(
			function ($field) {
				return strtolower($field);
			},
			$fields_array_result
		);
		$focus = CRMEntity::getInstance('ModComments');
		$processor = new ExportUtils('ModComments', $fields_array_result);
		if ($format == 'CSV') {
			$CSV_Separator = GlobalVariable::getVariable('Export_Field_Separator_Symbol', ',', 'ModComments');
			$header = '"'.implode('"'.$CSV_Separator.'"', $fields_array_result)."\"\r\n";
			/** Output header information */
			echo $header;
			dumpRowsToCSV('ModComments', $processor, $CSV_Separator, $columnsToExport, $result_query, $focus);
		} else {
			return dumpRowsToXLS('ModComments', $processor, $columnsToExport, $fields_array_result, $result_query);
		}
	}
	return true;
}


/**
 * Function to exporting XLS rows file format for modules.
 * @param object $rowsonfo object contain rows to export
 * @param string $modulename - module name
 * @param array $fieldinfo - fields info list
 * @param array $totalxclinfo - number of rows
 * @param string $fldname - contain list of fields
 * @return object PhpSpreadsheet rows in XLS format
 */
function exportExcelFileRows($rowsonfo, $totalxclinfo, $fldname, $fieldinfo, $modulename = '') {
	global $currentModule, $current_language, $current_user;
	if (empty($currentModule)) {
		$currentModule = $modulename;
	}
	$mod_strings = return_module_language($current_language, $currentModule);

	require 'include/PhpSpreadsheet/autoload.php';

	$xlsrowheight = GlobalVariable::getVariable('Report_Excel_Export_RowHeight', 20);
	$workbook = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
	$worksheet = $workbook->setActiveSheetIndex(0);
	$header_styles = array(
		'fill' => array('fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => array('rgb'=>'E1E0F7')),
		'font' => array('bold' => true)
	);

	if (!empty($rowsonfo)) {
		$FieldDataTypes = array();
		foreach ($rowsonfo[0] as $hdr => $value) {
			if (!isset($fieldinfo[$hdr])) {
				$FieldDataTypes[$hdr] = 'string';
				continue;
			}
			$FieldDataTypes[$hdr] = $fieldinfo[$hdr]->getFieldDataType();
			if ($fieldinfo[$hdr]->getColumnName()=='totaltime') {
				$FieldDataTypes[$hdr] = 'time';
			}
			if ($fieldinfo[$hdr]->getColumnName()=='totaldaytime') {
				$FieldDataTypes[$hdr] = 'time';
			}
		}
		$BoolTrue = getTranslatedString('LBL_YES');
		$count = 1;
		$rowcount = 1;
		$workbook->getActiveSheet()->getRowDimension($rowcount)->setRowHeight($xlsrowheight);
		//copy the first value details
		$arrayFirstRowValues = $rowsonfo[0];
		$report_header = GlobalVariable::getVariable('Report_HeaderOnXLS', '');
		if ($report_header == 1) {
			$rowcount++;
			$worksheet->setCellValueExplicitByColumnAndRow(1, 1, getTranslatedString($fldname), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			$worksheet->getStyleByColumnAndRow(1, 1)->applyFromArray($header_styles);
			$worksheet->getColumnDimensionByColumn(1)->setAutoSize(true);
		}
		foreach ($arrayFirstRowValues as $key => $value) {
			$worksheet->setCellValueExplicitByColumnAndRow($count, $rowcount, $key, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			$worksheet->getStyleByColumnAndRow($count, $rowcount)->applyFromArray($header_styles);
			$worksheet->getColumnDimensionByColumn($count)->setAutoSize(true);
			$count = $count + 1;
			if ($FieldDataTypes[$key]=='currency') {
				$worksheet->setCellValueExplicitByColumnAndRow($count, $rowcount, getTranslatedString('LBL_CURRENCY'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
				$worksheet->getStyleByColumnAndRow($count, $rowcount)->applyFromArray($header_styles);
				$worksheet->getColumnDimensionByColumn($count)->setAutoSize(true);
				$count = $count + 1;
			}
		}
		$rowcount++;
		$workbook->getActiveSheet()->getRowDimension($rowcount)->setRowHeight($xlsrowheight);
		foreach ($rowsonfo as $key => $array_value) {
			$count = 1;
			foreach ($array_value as $hdr => $value) {
				$value = decode_html($value);
				$datetime = false;
				switch ($FieldDataTypes[$hdr]) {
					case 'boolean':
						$celltype = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_BOOL;
						$value = ($value==$BoolTrue ? 1:0);
						break;
					case 'double':
						$value = CurrencyField::convertToDBFormat($value);
						// fall through intentional
					case 'integer':
					case 'currency':
						$celltype = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC;
						break;
					case 'date':
					case 'time':
						try {
							if ($value!='-') {
								if (!empty($value)) {
									if (strpos($value, ':')>0 && (strpos($value, '-')===false)) {
										// only time, no date
										$dt = new DateTime("1970-01-01 $value");
									} elseif (strpos($value, ':')>0 && (strpos($value, '-')>0)) {
										// date and time
										$dt = new DateTime($value);
										$datetime = true;
									} else {
										$value = DateTimeField::__convertToDBFormat($value, $current_user->date_format);
										$dt = new DateTime($value);
									}
									$value = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($dt);
									$celltype = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC;
								} else {
									$celltype = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NULL;
								}
							} else {
								$celltype = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING;
							}
						} catch (Exception $e) {
							$celltype = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING;
						}
						break;
					default:
						$celltype = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING;
						break;
				}
				if ($FieldDataTypes[$hdr]=='currency') {
					$csym = preg_replace('/[0-9,.-]/', '', $value);
					$value = preg_replace('/[^0-9,.-]/', '', $value);
					$value = str_replace($current_user->currency_grouping_separator, '', $value);
					if ($current_user->currency_decimal_separator!='.') {
						$value = str_replace($current_user->currency_decimal_separator, '.', $value);
					}
				}
				$worksheet->setCellValueExplicitByColumnAndRow($count, $rowcount, $value, $celltype);
				if ($FieldDataTypes[$hdr]=='date') {
					if ($datetime) {
						$worksheet->getStyleByColumnAndRow($count, $rowcount)->getNumberFormat()->setFormatCode($current_user->date_format.' '.\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME4);
					} else {
						$worksheet->getStyleByColumnAndRow($count, $rowcount)->getNumberFormat()->setFormatCode($current_user->date_format);
					}
				} elseif ($FieldDataTypes[$hdr]=='time') {
					$worksheet->getStyleByColumnAndRow($count, $rowcount)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME4);
				}
				if ($FieldDataTypes[$hdr]=='currency') {
					$count = $count + 1;
					$worksheet->setCellValueExplicitByColumnAndRow($count, $rowcount, $csym, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
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
		if (!empty($totalxclinfo) && is_array($totalxclinfo)) {
			if (is_array($totalxclinfo[0])) {
				$worksheet->setCellValueExplicitByColumnAndRow($count, $rowcount, getTranslatedString('Totals', 'Reports'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
				$worksheet->getStyleByColumnAndRow($count, $rowcount)->applyFromArray($header_styles);
				$count = $count + 1;
				foreach ($totalxclinfo[0] as $key => $value) {
					$chdr=substr($key, -3, 3);
					$translated_str = in_array($chdr, array_keys($mod_strings))?$mod_strings[$chdr]:$key;
					$worksheet->setCellValueExplicitByColumnAndRow($count, $rowcount, decode_html($translated_str), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
					$worksheet->getStyleByColumnAndRow($count, $rowcount)->applyFromArray($header_styles);
					$count = $count + 1;
				}
			}
			$rowcount++;
			$workbook->getActiveSheet()->getRowDimension($rowcount)->setRowHeight($xlsrowheight);
			foreach ($totalxclinfo as $key => $array_value) {
				$count = 0;
				foreach ($array_value as $hdr => $value) {
					if ($count==0) {
						$lbl = substr($hdr, 0, strrpos($hdr, '_'));
						$mname = substr($lbl, 0, strpos($lbl, '_'));
						$lbl = substr($lbl, strpos($lbl, '_')+1);
						$lbl = str_replace('_', ' ', $lbl);
						$lbl = getTranslatedString($lbl, $mname);
						$worksheet->setCellValueExplicitByColumnAndRow($count, $rowcount, decode_html($lbl), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
						$worksheet->getStyleByColumnAndRow($count, $rowcount)->applyFromArray($header_styles);
						$workbook->getActiveSheet()->getRowDimension($rowcount)->setRowHeight($xlsrowheight);
						$count = $count + 1;
					}
					$value = str_replace($current_user->currency_grouping_separator, '', $value);
					if ($current_user->currency_decimal_separator!='.') {
						$value = str_replace($current_user->currency_decimal_separator, '.', $value);
					}
					$worksheet->setCellValueExplicitByColumnAndRow($count, $rowcount, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
					$count = $count + 1;
				}
				$rowcount++;
			}
		}
	}
	return $workbook;
}

/**
 * Function to dump csv rows file format for modules.
 * @param object $result object contain rows to export
 * @param string $type - module name
 * @param array $columnsToExport - fields list
 * @param string $CSV_Separator - export fields separators
 * @param object $processor - object contain list of fields
 * @param object $focus - object of module information for record
 * @return object headers and record rows in CSV format
 */
function dumpRowsToCSV($type, $processor, $CSV_Separator, $columnsToExport, $result, $focus) {
	global $adb;

	while ($val = $adb->fetchByAssoc($result, -1, false)) {
		$new_arr = array();
		$val = $processor->sanitizeValues($val);
		foreach ($columnsToExport as $col) {
			$value = $val[$col];
			if ($type == 'Documents' && $col == 'description') {
				$value = strip_tags($value);
				$value = str_replace('&nbsp;', '', $value);
				$new_arr[] = $value;
			} elseif ($type == 'com_vtiger_workflow' && $col == 'workflow_id') {
				$wfm = new VTworkflowManager($adb);
				$workflow = $wfm->retrieve($value);
				$value = $wfm->serializeWorkflow($workflow);
				$new_arr[] = base64_encode($value);
			} elseif ($col != 'user_name') {
				// Let us provide the module to transform the value before we save it to CSV file
				$value = $focus->transform_export_value($col, $value);
				$new_arr[] = preg_replace('/"/', '""', $value);
			}
		}
		$line = '"'.implode('"'.$CSV_Separator.'"', $new_arr)."\"\r\n";
		/** Output each row information */
		echo $line;
	}
}

/**
 * Function to dump excel rows file format for modules.
 * @param object $result object contain rows to export
 * @param string $type - module name
 * @param array $columnsToExport - fields list
 * @param array $translated_fields_array - headers to export
 * @param object $processor - object contain list of fields
 * @return object PhpSpreadsheet rows in XLS format
 */
function dumpRowsToXLS($type, $processor, $columnsToExport, $translated_fields_array, $result) {
	global $adb;

	$xlsrows = array();
	$column_arr = array();
	$field_list = array();
	while ($val = $adb->fetchByAssoc($result, -1, false)) {
		$new_arr = array();
		$val = $processor->sanitizeValues($val);
		foreach ($columnsToExport as $col) {
			$column_arr[] = $col;
		}
		foreach ($translated_fields_array as $field_arr) {
			$field_list[] = $field_arr;
		}
		for ($i = 0; $i < sizeof($field_list); $i++) {
			$new_arr[$field_list[$i]] = $val[$column_arr[$i]];
		}
		$xlsrows[] = $new_arr;
	}
	$totalxclinfo = array();
	$fieldinfo = array();
	$fldname = $type.' Excel Format';
	$workbook = exportExcelFileRows($xlsrows, $totalxclinfo, $fldname, $fieldinfo, $type);
	return \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($workbook, 'Xls');
}

/**	function used to get the list of fields from the input query as a comma seperated string
 *	@param string $query - field table query which contains the list of fields
 *	@return string $fields - list of fields as a comma seperated string
 */
function getFieldsListFromQuery($query) {
	global $adb, $log;
	$log->debug("> getFieldsListFromQuery $query");

	$result = $adb->query($query);
	$num_rows = $adb->num_rows($result);
	$fields = '';
	$isUsers = false;
	for ($i=0; $i < $num_rows; $i++) {
		$columnName = $adb->query_result($result, $i, 'columnname');
		$fieldlabel = $adb->query_result($result, $i, 'fieldlabel');
		$fieldlabel = str_replace('?', '', $fieldlabel);
		$tablename = $adb->query_result($result, $i, 'tablename');
		$isUsers = ($isUsers || $tablename=='vtiger_users');

		//HANDLE HERE - Mismatch fieldname-tablename in field table, in future we have to avoid these if elses
		if ($columnName == 'smownerid') {//for all assigned to user name
			$fields .= "case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as '".$fieldlabel."',";
		} elseif ($columnName == 'smcreatorid') {
			$fields .= "vtigerCreatedBy.user_name as '".$fieldlabel."',";
		} elseif ($tablename == 'vtiger_account' && $columnName == 'parentid') {//Account - Member Of
			$fields .= "vtiger_account2.accountname as '".$fieldlabel."',";
		} elseif ($tablename == 'vtiger_contactdetails' && $columnName == 'accountid') {//Contact - Account Name
			$fields .= "vtiger_account.accountname as '".$fieldlabel."',";
		} elseif ($tablename == 'vtiger_contactdetails' && $columnName == 'reportsto') {//Contact - Reports To
			$fields .= " concat(vtiger_contactdetails2.lastname,' ',vtiger_contactdetails2.firstname) as 'Reports To Contact',";
		} elseif ($tablename == 'vtiger_potential' && $columnName == 'related_to') {//Potential - Related to (changed for B2C model support)
			$fields .= "vtiger_potential.related_to as '".$fieldlabel."',";
		} elseif ($tablename == 'vtiger_potential' && $columnName == 'campaignid') {//Potential - Campaign Source
			$fields .= "vtiger_campaign.campaignname as '".$fieldlabel."',";
		} elseif ($tablename == 'vtiger_seproductsrel' && $columnName == 'crmid') {//Product - Related To
			$fields .= "case vtiger_crmentityRelatedTo.setype
				when 'Leads' then concat('Leads::::',vtiger_ProductRelatedToLead.lastname,' ',vtiger_ProductRelatedToLead.firstname)
				when 'Accounts' then concat('Accounts::::',vtiger_ProductRelatedToAccount.accountname)
				when 'Potentials' then concat('Potentials::::',vtiger_ProductRelatedToPotential.potentialname)
				End as 'Related To',";
		} elseif ($tablename == 'vtiger_products' && $columnName == 'contactid') {//Product - Contact
			$fields .= " concat(vtiger_contactdetails.lastname,' ',vtiger_contactdetails.firstname) as 'Contact Name',";
		} elseif ($tablename == 'vtiger_products' && $columnName == 'vendor_id') {//Product - Vendor Name
			$fields .= "vtiger_vendor.vendorname as '".$fieldlabel."',";
		} elseif ($tablename == 'vtiger_producttaxrel' && $columnName == 'taxclass') {//avoid product - taxclass
			$fields .= "";
		} elseif ($tablename == 'vtiger_attachments' && $columnName == 'name') {//Emails filename
			$fields .= $tablename.".name as '".$fieldlabel."',";
		} elseif ($tablename == 'vtiger_troubletickets' && $columnName == 'product_id') {//Ticket - Product
			$fields .= "vtiger_products.productname as '".$fieldlabel."',";
		} elseif (($tablename == 'vtiger_invoice' || $tablename == 'vtiger_quotes' || $tablename == 'vtiger_salesorder')&& $columnName == 'accountid') {
			$fields .= 'concat("Accounts::::",vtiger_account.accountname) as "'.$fieldlabel.'",';
		} elseif (($tablename == 'vtiger_invoice' || $tablename == 'vtiger_quotes' || $tablename == 'vtiger_salesorder' || $tablename == 'vtiger_purchaseorder') && $columnName == 'contactid') {
			$fields .= 'concat("Contacts::::",vtiger_contactdetails.lastname," ",vtiger_contactdetails.firstname) as "'.$fieldlabel.'",';
		} elseif ($tablename == 'vtiger_invoice' && $columnName == 'salesorderid') {
			$fields .= 'concat("SalesOrder::::",vtiger_salesorder.subject) as "'.$fieldlabel.'",';
		} elseif (($tablename == 'vtiger_quotes' || $tablename == 'vtiger_salesorder') && $columnName == 'potentialid') {
			$fields .= 'concat("Potentials::::",vtiger_potential.potentialname) as "'.$fieldlabel.'",';
		} elseif ($tablename == 'vtiger_quotes' && $columnName == 'inventorymanager') {
			$fields .= 'vtiger_inventoryManager.ename as "'.$fieldlabel.'",';
		} elseif ($tablename == 'vtiger_salesorder' && $columnName == 'quoteid') {
			$fields .= 'concat("Quotes::::",vtiger_quotes.subject) as "'.$fieldlabel.'",';
		} elseif ($tablename == 'vtiger_purchaseorder' && $columnName == 'vendorid') {
			$fields .= 'concat("Vendors::::",vtiger_vendor.vendorname) as "'.$fieldlabel.'",';
		} elseif (($tablename == 'vtiger_users' && ($columnName == 'user_password' || $columnName == 'confirm_password' || $columnName == 'accesskey'))
			|| ($tablename == 'vtiger_notes' && ($columnName == 'filename' || $columnName == 'filetype' || $columnName == 'filesize' || $columnName == 'filelocationtype' || $columnName == 'filestatus' || $columnName == 'filedownloadcount' ||$columnName == 'folderid'))
		) {
			// do nothing, just continue
		} else {
			$fields .= $tablename.'.'.$columnName. " as '" .$fieldlabel."',";
		}
	}
	$fields = trim($fields, ',');
	if (!$isUsers) {
		$fields .= ',vtiger_crmentity.cbuuid';
	}
	$log->debug('< getFieldsListFromQuery '.$fields);
	return $fields;
}

/**
 * this class will provide utility functions to process the export data.
 * this is to make sure that the data is sanitized before sending for export
 */
class ExportUtils {
	public $fieldsArr = array();
	public $picklistValues = array();

	public function __construct($module, $fields_array) {
		self::__init($module, $fields_array);
	}

	public function __init($module, $fields_array) {
		$infoArr = self::getInformationArray($module);
		//attach extra fields related information to the fields_array; this will be useful for processing the export data
		foreach ($infoArr as $fieldname => $fieldinfo) {
			if (in_array($fieldinfo['fieldlabel'], $fields_array)) {
				$this->fieldsArr[$fieldname] = $fieldinfo;
			}
		}
	}

	/**
	 * this function takes in an array of values for an user and sanitizes it for export
	 * @param array $arr - the array of values
	 */
	public function sanitizeValues($arr) {
		global $current_user, $adb;
		$roleid = fetchUserRole($current_user->id);
		$decimal = $current_user->currency_decimal_separator;
		$numsep = $current_user->currency_grouping_separator;
		foreach ($arr as $fieldlabel => &$value) {
			if (empty($this->fieldsArr[$fieldlabel])) {
				continue;
			}
			$fieldInfo = $this->fieldsArr[$fieldlabel];

			$uitype = $fieldInfo['uitype'];
			$fieldname = $fieldInfo['fieldname'];
			if ($uitype == 15 || $uitype == 16 || $uitype == 33) {
				//picklists
				if (empty($this->picklistValues[$fieldname])) {
					$this->picklistValues[$fieldname] = getAssignedPicklistValues($fieldname, $roleid, $adb);
				}
				$value = trim($value);
			} elseif ($uitype == 10) {
				//have to handle uitype 10
				$value = trim($value);
				if (!empty($value) && is_numeric($value)) {
					$parent_module = getSalesEntityType($value);
					$Export_RelatedField_GetValueFrom = GlobalVariable::getVariable('Export_RelatedField_GetValueFrom', '', $parent_module);
					if ($Export_RelatedField_GetValueFrom != '') {
						$qg = new QueryGenerator($parent_module, $current_user);
						$qg->setFields(array($Export_RelatedField_GetValueFrom));
						$qg->addCondition('id', $value, 'e');
						$query = $qg->getQuery();
						$rs = $adb->query($query);
						if ($rs && $adb->num_rows($rs) == 1) {
							$displayValue = $adb->query_result($rs, 0, $Export_RelatedField_GetValueFrom);
						} else {
							$displayValue = $value;
						}
					} else {
						$displayValueArray = getEntityName($parent_module, $value);
						if (!empty($displayValueArray)) {
							foreach ($displayValueArray as $v) {
								$displayValue = $v;
							}
						}
					}
					if (!empty($parent_module) && !empty($displayValue)) {
						$value = $parent_module.'::::'.$displayValue;
						$Export_RelatedField_NameForSearch = GlobalVariable::getVariable('Export_RelatedField_NameForSearch', '', $parent_module);
						if ($Export_RelatedField_NameForSearch != '') {
							$value = $value.'::::'.$Export_RelatedField_NameForSearch;
						}
					} else {
						$value = '';
					}
				} else {
					$value = empty($value) ? '' : $value;
				}
			} elseif ($uitype == 71) {
				$value = CurrencyField::convertToUserFormat($value);
			} elseif ($uitype == 72) {
				$value = CurrencyField::convertToUserFormat($value, null, true);
			} elseif ($uitype == 7 || $fieldInfo['typeofdata'] == 'N~O' || $uitype == 9) {
				$value = number_format($value, 2, $decimal, $numsep);
			} elseif ($uitype == 98) {
				$value = getRoleName($value);
			}
		}
		return $arr;
	}

	/**
	 * this function takes in a module name and returns the field information for it
	 */
	public function getInformationArray($module) {
		require_once 'include/utils/utils.php';
		global $adb;
		$tabid = getTabid($module);

		$result = $adb->pquery('select * from vtiger_field where tabid=?', array($tabid));
		$count = $adb->num_rows($result);
		$arr = array();
		$data = array();

		for ($i=0; $i<$count; $i++) {
			$arr['uitype'] = $adb->query_result($result, $i, 'uitype');
			$arr['fieldname'] = $adb->query_result($result, $i, 'fieldname');
			$arr['columnname'] = $adb->query_result($result, $i, 'columnname');
			$arr['tablename'] = $adb->query_result($result, $i, 'tablename');
			$arr['fieldlabel'] = $adb->query_result($result, $i, 'fieldlabel');
			$arr['typeofdata'] = $adb->query_result($result, $i, 'typeofdata');
			$fieldlabel = strtolower($arr['fieldlabel']);
			$data[$fieldlabel] = $arr;
		}
		if (in_array($module, getInventoryModules())) {
			include_once 'include/fields/InventoryLineField.php';
			$ilfields = new InventoryLineField();
			$data = array_merge($data, $ilfields->getInventoryLineFieldsByLabel());
		}
		return $data;
	}
}
?>
