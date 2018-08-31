<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
* granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************/
require_once 'modules/Reports/ReportUtils.php';
$advft_fieldscriteria = $_POST['criteriaConditions'];
$advft_fieldscriteria = json_decode($advft_fieldscriteria);
$dbname = $_POST['dbname'];
$advft_criteria_groups = $_POST['criteriaGroups'];
$advft_criteria_groups = json_decode($advft_criteria_groups);
function objectToArray($d) {
        if (is_object($d)) {
                // Gets the properties of the given object
                // with get_object_vars function
                $d = get_object_vars($d);
        }

        if (is_array($d)) {
                /*
                * Return array converted to object
                * Using __FUNCTION__ (Magic constant)
                * for recursive call
                */
                return array_map(__FUNCTION__, $d);
        }
        else {
                // Return array
                return $d;
        }
}

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
        
function getAdvFilterList($advft_fieldscriteria,$advft_criteria_groups){
$advft_criteria = array();
//var_dump($advft_fieldscriteria);
//$i = 1;
//$j = 0;
foreach($advft_criteria_groups as $key=>$value){
    if($key !=0){
         $groupCond= objectToArray($value);
         $groupCondition = $groupCond['groupcondition'];
    foreach ($advft_fieldscriteria as $index =>$vl){
        if($vl->groupid == $key){
        $advft_criteria[$key]['columns'][$index] = objectToArray($vl);
	$advft_criteria[$key]['condition'] = $groupCondition;
        }
    }

    if(!empty($advft_criteria[$key]['columns'][$index]['column_condition'])) {
				$advft_criteria[$key]['columns'][$index]['column_condition'] = '';
			}
}
}
    if(!empty($advft_criteria[$key]['condition'])) $advft_criteria[$key]['condition'] = '';

     return $advft_criteria;
}

function generateAdvFilterSql($advfilterlist) {
//    var_dump($advfilterlist);
global $adb,$dbname;
$advfiltersql = "";

		foreach($advfilterlist as $groupindex => $groupinfo) {
			$groupcondition = $groupinfo['condition'];
			$groupcolumns = $groupinfo['columns'];

			if(count($groupcolumns) > 0) {

				$advfiltergroupsql = "";
				foreach($groupcolumns as $columnindex => $columninfo) {
					$fieldcolname = $columninfo["columnname"];
					$comparator = $columninfo["comparator"];
					$value = $columninfo["value"];
					$columncondition = $columninfo["columncondition"];
					$openbrackets=$columninfo["openbackets"];
                    $closebrackets=$columninfo["closebackets"];

					if($fieldcolname != "" && $comparator != "") {//if fieldscolname different from null
						$selectedfields = explode(":",$fieldcolname);
						$moduleFieldLabel = $selectedfields[2];
						list($moduleName, $fieldLabel) = explode('_', $moduleFieldLabel, 2);
						$fieldInfo = getFieldByReportLabel($moduleName, $fieldLabel,$dbname);

                        $concatSql = getSqlForNameInDisplayFormat(array('first_name'=>$selectedfields[0].".first_name",'last_name'=>$selectedfields[0].".last_name"), 'Users',$dbname);


						

						// Added to handle the crmentity table name for Primary module
//                        if($selectedfields[0] == "vtiger_crmentity".$this->primarymodule) {
//                            $selectedfields[0] = "vtiger_crmentity";
//                        }
						//Added to handle yes or no for checkbox  field in reports advance filters. -shahul
						if($selectedfields[4] == 'C') {
							if(strcasecmp(trim($value),"yes")==0)
								$value="1";
							if(strcasecmp(trim($value),"no")==0)
								$value="0";
						}
						$valuearray = explode(",",trim($value));

						$datatype = (isset($selectedfields[4])) ? $selectedfields[4] : "";

						if(isset($valuearray) && count($valuearray) > 1 && $comparator != 'bw') {

							$advcolumnsql = "";

							for($n=0;$n<count($valuearray);$n++) {



		                		if(($selectedfields[0] == "vtiger_users".$this->primarymodule || $selectedfields[0] == "vtiger_users".$this->secondarymodule) && $selectedfields[1] == 'user_name') {
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

						}
//                                                elseif(($selectedfields[0] == "vtiger_users".$this->primarymodule || $selectedfields[0] == "vtiger_users".$this->secondarymodule) && $selectedfields[1] == 'user_name') {
//							$module_from_tablename = str_replace("vtiger_users","",$selectedfields[0]);
//							$fieldvalue = " trim(case when (".$selectedfields[0].".last_name NOT LIKE '') then ".$concatSql." else vtiger_groups".$module_from_tablename.".groupname end) ".$this->getAdvComparator($comparator,trim($value),$datatype);
//						} 
                                                elseif($comparator == 'bw' && count($valuearray) == 2) {
//							if($selectedfields[0] == "vtiger_crmentity".$this->primarymodule) {
//								$fieldvalue = "("."vtiger_crmentity.".$selectedfields[1]." between '".trim($valuearray[0])."' and '".trim($valuearray[1])."')";
//							} else {
								$fieldvalue = "(".$selectedfields[0].".".$selectedfields[1]." between '".trim($valuearray[0])."' and '".trim($valuearray[1])."')";
//							}
						} 
//                                                elseif($selectedfields[0] == "vtiger_crmentity".$this->primarymodule) {
//							$fieldvalue = "vtiger_crmentity.".$selectedfields[1]." ".$this->getAdvComparator($comparator,trim($value),$datatype);
//						} 
                                                elseif($selectedfields[2] == 'Quotes_Inventory_Manager'){
							$fieldvalue = ("trim($concatSql)" . getAdvComparator($comparator,trim($value),$datatype));
						} elseif($selectedfields[1]=='modifiedby') {
                            $module_from_tablename = str_replace("vtiger_crmentity","",$selectedfields[0]);
                            if($module_from_tablename != '') {
								$tableName = 'vtiger_lastModifiedBy'.$module_from_tablename;
							} else {
								$tableName = 'vtiger_lastModifiedBy'.$this->primarymodule;
							}
							$fieldvalue = getSqlForNameInDisplayFormat(array('last_name'=>"$tableName.last_name",'first_name'=>"$tableName.first_name"), 'Users').
									getAdvComparator($comparator,trim($value),$datatype);
						} elseif($selectedfields[0] == "vtiger_activity" && $selectedfields[1] == 'status') {
							$fieldvalue = "(case when (vtiger_activity.status not like '') then vtiger_activity.status else vtiger_activity.eventstatus end)".$this->getAdvComparator($comparator,trim($value),$datatype);
						} elseif($comparator == 'e' && (trim($value) == "NULL" || trim($value) == '')) {
							$fieldvalue = "".$selectedfields[0].".".$selectedfields[1]." IS NULL OR ".$selectedfields[0].".".$selectedfields[1]." = '' ";
						}
//                                                elseif($selectedfields[0] == 'vtiger_inventoryproductrel' && ($selectedfields[1] == 'productid' || $selectedfields[1] == 'serviceid')) {
//							if($selectedfields[1] == 'productid'){
//								$fieldvalue = "vtiger_products{$this->primarymodule}.productname ".$this->getAdvComparator($comparator,trim($value),$datatype);
//							} else if($selectedfields[1] == 'serviceid'){
//								$fieldvalue = "vtiger_service{$this->primarymodule}.servicename ".$this->getAdvComparator($comparator,trim($value),$datatype);
//							}
//						}
                                                elseif($fieldInfo['uitype'] == '10' || isReferenceUIType($fieldInfo['uitype'])) {

							$comparatorValue = $this->getAdvComparator($comparator,trim($value),$datatype);
							$fieldSqls = array();
							
							$fieldSqlColumns = $this->getReferenceFieldColumnList($moduleName, $fieldInfo);
							foreach($fieldSqlColumns as $columnSql) {
							 	$fieldSqls[] = $columnSql.$comparatorValue;

							}

							$fieldvalue = ' ('. implode(' OR ', $fieldSqls).') ';
							} else {
							$fieldvalue = $selectedfields[0].".".$selectedfields[1].getAdvComparator($comparator,trim($value),$datatype);
						}

                        if ($openbrackets==1){
                            $fieldvalue=substr_replace($fieldvalue, '(', 0, 0);;
                        }
                       if ($closebrackets==1){
                           $fieldvalue=substr_replace($fieldvalue, ')', strlen ( $fieldvalue ), 0);
                        }

						$advfiltergroupsql .= $fieldvalue;
						if(!empty($columncondition)) {
							$advfiltergroupsql .= ' '.$columncondition.' ';
						}
					}//end if fieldcolname different from null

				}

				if (trim($advfiltergroupsql) != "") {
				    //first brackets
					$advfiltergroupsql =  " $advfiltergroupsql  ";
					if(!empty($groupcondition)) {
						$advfiltergroupsql .= ' '. $groupcondition . ' ';
					}

					$advfiltersql .= $advfiltergroupsql;
				}
			}
		}

		
			if (trim($advfiltersql) != "") $advfiltersql = ''.$advfiltersql.'';//second brackets
		return $advfiltersql;
	}
$advlist = getAdvFilterList($advft_fieldscriteria,$advft_criteria_groups);
//$result=generateAdvFilterSql($advlist);  <strong>WHERE </strong>
//$provapakllapa=str_replace( array('(', ')'), array(' ', ' '),generateAdvFilterSql($advlist));
echo "<strong>WHERE </strong>".generateAdvFilterSql($advlist);// str_replace( array('(', ')'), array(' ', ' '),generateAdvFilterSql($advlist));
?>
