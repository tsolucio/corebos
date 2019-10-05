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
 *  Module       : Template script to import a Email CSV file
 *  Version      : 1.0
 *    This script reads in a csv file with the separator a colon.
 *    The first row must be a header with the columns of task/event, even custom fields:
 *      assigned_user_id,subject,date_start,time_start,activitytype=>Emails,
 *      saved_toid,from_email,ccmail,bccmail,description=>Body,parent_id=>9x45,
 *      email_flag=>'SENT|SAVED|MAILSCANNER|MailManager'
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
	//print_r($row);
	try {
		$row = vtws_create('Emails', $row, $current_user);
		echo $row['id'] . PHP_EOL;
	} catch (WebServiceException $ex) {
		$msg = $ex->getMessage();
		$msg .= print_r($row, true) . "\n";
		error_log($msg, 3, $file . "-error.log");
		echo $msg;
	}
	//if ($i++>=1) break;  // for testing before full launch
}
?>