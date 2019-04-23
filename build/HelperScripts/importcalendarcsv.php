<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************
 *  Module       : Template script to import a Calendar CSV file
 *  Version      : 1.0
 *    This script reads in a csv file with the separator a colon.
 *    The first row must be a header with the columns of task/event, even custom fields:
 *      "assigned_user_id","sendnotification","subject","date_start","time_start","due_date","time_end",
 *      "taskstatus","eventstatus","taskpriority","activitytype","location","description",
 *      "contact_id=>'12x22;12x23'","parent_id=>9x45"
 *    For large imports break the csv file into multiple files of say 50,000 each and then call
 *    this script from a bash shell script wrapper which does something like this:
 *   for FILE in ${FILES}
 *   do
 *     php -f importcalendarcsv.php ${FILEPATH}${FILE}
 *   done
 *************************************************************************************************/

$Vtiger_Utils_Log = true;

include_once 'vtlib/Vtiger/Module.php';

$current_user = Users::getActiveAdminUser();

$file = $argv[1];

if (!file_exists($file) || !is_readable($file)) {
	echo "No suitable file specified" . PHP_EOL;
	die;
}

function csv_to_array($file = '', $length = 0, $delimiter = ',') {
	$header = null;
	$data = array();
	if (($handle = fopen($file, 'r')) !== false) {
		while (($row = fgetcsv($handle, $length, $delimiter)) !== false) {
			if (!$header) {
				$header = $row;
			} else {
				$data[] = array_combine($header, $row);
			}
		}
		fclose($handle);
	}
	return $data;
}

include_once 'include/Webservices/Create.php';
$i=0;
foreach (csv_to_array($file) as $row) {
	global $adb;
	//print_r($row);
	try {
		if ($row['activitytype'] == 'Task') {
			$mod = 'Calendar';
		} else {
			$mod = 'Events';
		}
		$row['recurringtype'] = '--None--';
		//Validate if we have the parent_id
		$relate_to = explode('x', $row['parent_id']);
		if (count($relate_to) == 2) {
			if (is_numeric($relate_to[0]) && is_numeric($relate_to[0])) {
				$wsrs = $adb->pquery('select name FROM vtiger_ws_entity where id=?', array($relate_to[0]));
				if (!$wsrs || $adb->num_rows($wsrs)==0) {
					$setype = getSalesEntityType($relate_to[1]);
					$wsrs = $adb->pquery('select id FROM vtiger_ws_entity where name=?', array($setype));
					if ($wsrs || $adb->num_rows($wsrs)==1) {
						$wsid = $adb->query_result($wsrs, 0, 'id');
						$row['parent_id'] = $wsid.'x'.$relate_to[1];
					} else {
						$row['parent_id'] = '';
					}
				} else {
					$wsname = $adb->query_result($wsrs, 0, 'name');
					$setype = getSalesEntityType($relate_to[1]);
					if ($wsname != $setype) {
						$row['parent_id'] = '';
					}
				}
			} elseif (is_numeric($relate_to[1])) {
				$setype = getSalesEntityType($relate_to[1]);
				$wsrs = $adb->pquery('select id FROM vtiger_ws_entity where name=?', array($setype));
				if ($wsrs || $adb->num_rows($wsrs)==1) {
					$wsid = $adb->query_result($wsrs, 0, 'name');
					$row['parent_id'] = $wsid.'x'.$relate_to[1];
				} else {
					$row['parent_id'] = '';
				}
			} else {
				$row['parent_id'] = '';
			}
		} else {
			$queryLeads = "SELECT crmid,id FROM vtiger_crmentity INNER JOIN vtiger_leaddetails ON vtiger_leaddetails.leadid = vtiger_crmentity.crmid and vtiger_leaddetails.lastname = ? INNER JOIN vtiger_ws_entity ON vtiger_ws_entity.name = vtiger_crmentity.setype";
			$resultLeads = $adb->pquery($queryLeads, array($row['parent_id']));

			$queryAcc = "SELECT crmid,id FROM vtiger_crmentity INNER JOIN vtiger_account ON vtiger_account.accountid = vtiger_crmentity.crmid and vtiger_account.accountname = ? INNER JOIN vtiger_ws_entity ON vtiger_ws_entity.name = vtiger_crmentity.setype";
			$resultAcc = $adb->pquery($queryAcc, array($row['parent_id']));

			$queryPot = "SELECT crmid,id FROM vtiger_crmentity INNER JOIN vtiger_potential ON vtiger_potential.potentialid = vtiger_crmentity.crmid and vtiger_potential.potentialname = ? INNER JOIN vtiger_ws_entity ON vtiger_ws_entity.name = vtiger_crmentity.setype";
			$resultPot = $adb->pquery($queryPot, array($row['parent_id']));

			$queryHD = "SELECT crmid,id FROM vtiger_crmentity INNER JOIN vtiger_troubletickets ON vtiger_troubletickets.ticketid = vtiger_crmentity.crmid and vtiger_troubletickets.title = ? INNER JOIN vtiger_ws_entity ON vtiger_ws_entity.name = vtiger_crmentity.setype";
			$resultHD = $adb->pquery($queryHD, array($row['parent_id']));

			$queryCmp = "SELECT crmid,id FROM vtiger_crmentity INNER JOIN vtiger_campaign ON vtiger_campaign.campaignid = vtiger_crmentity.crmid and vtiger_campaign.campaignname = ? INNER JOIN vtiger_ws_entity ON vtiger_ws_entity.name = vtiger_crmentity.setype";
			$resultCmp = $adb->pquery($queryCmp, array($row['parent_id']));

			$queryVnd = "SELECT crmid,id FROM vtiger_crmentity INNER JOIN  vtiger_vendor ON  vtiger_vendor.vendorid =  vtiger_crmentity.crmid and vtiger_vendor.vendorname = ? INNER JOIN vtiger_ws_entity ON vtiger_ws_entity.name = vtiger_crmentity.setype";
			$resultVnd = $adb->pquery($queryVnd, array($row['parent_id']));

			if ($resultLeads && $adb->num_rows($resultLeads)>= 1) {
				$wsid = $adb->query_result($resultLeads, 0, 'id');
				$parentid = $adb->query_result($resultLeads, 0, 'crmid');
				$row['parent_id'] = $wsid.'x'.$parentid;
			} elseif ($resultAcc && $adb->num_rows($resultAcc) >= 1) {
				$wsid = $adb->query_result($resultAcc, 0, 'id');
				$parentid = $adb->query_result($resultAcc, 0, 'crmid');
				$row['parent_id'] = $wsid.'x'.$parentid;
			} elseif ($resultPot && $adb->num_rows($resultPot) >= 1) {
				$wsid = $adb->query_result($resultPot, 0, 'id');
				$parentid = $adb->query_result($resultPot, 0, 'crmid');
				$row['parent_id'] = $wsid.'x'.$parentid;
			} elseif ($resultHD && $adb->num_rows($resultHD) >= 1) {
				$wsid = $adb->query_result($resultHD, 0, 'id');
				$parentid = $adb->query_result($resultHD, 0, 'crmid');
				$row['parent_id'] = $wsid.'x'.$parentid;
			} elseif ($resultCmp && $adb->num_rows($resultCmp) >= 1) {
				$wsid = $adb->query_result($resultCmp, 0, 'id');
				$parentid = $adb->query_result($resultCmp, 0, 'crmid');
				$row['parent_id'] = $wsid.'x'.$parentid;
			} elseif ($resultVnd && $adb->num_rows($resultVnd)>= 1) {
				$wsid = $adb->query_result($resultVnd, 0, 'id');
				$parentid = $adb->query_result($resultVnd, 0, 'crmid');
				$row['parent_id'] = $wsid.'x'.$parentid;
			} else {
				$row['parent_id'] = '';
			}
		}

		$row = vtws_create($mod, $row, $current_user);
		echo $mod.": " . $row['id'] . PHP_EOL;
	} catch (WebServiceException $ex) {
		$msg = $ex->getMessage();
		$msg .= print_r($row, true) . "\n";
		error_log($msg, 3, $file . "-error.log");
		echo $msg;
	}
	//if ($i++==10) break;  // for testing before full launch
}
?>