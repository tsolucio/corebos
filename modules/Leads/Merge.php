<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
********************************************************************************/
require_once 'include/database/PearDatabase.php';
require_once 'include/utils/MergeUtils.php';
global $app_strings, $default_charset;

$randomfilename = 'vt_' . str_replace(array('.',' '), '', microtime());

$templateid = $_REQUEST['mergefile'];
if ($templateid == '') {
	die('Select Mail Merge Template');
}
//get the particular file from db and store it in the local hard disk.
//store the path to the location where the file is stored and pass it  as parameter to the method
$result = $adb->pquery('select filename,data,filesize from vtiger_wordtemplates where templateid=?', array($templateid));
$temparray = $adb->fetch_array($result);

$fileContent = $temparray['data'];
$filename=html_entity_decode($temparray['filename'], ENT_QUOTES, $default_charset);
$extension=GetFileExtension($filename);
$filename= $randomfilename . "_mmrg.$extension";

$filesize=$temparray['filesize'];
$wordtemplatedownloadpath =$root_directory .'/cache/wordtemplatedownload/';
if (!is_dir($wordtemplatedownloadpath)) {
	@mkdir($wordtemplatedownloadpath);
}
$handle = fopen($wordtemplatedownloadpath.$filename, 'wb');
fwrite($handle, base64_decode($fileContent), $filesize);
fclose($handle);

//for mass merge
$mass_merge = isset($_REQUEST['allselectedboxes']) ? $_REQUEST['allselectedboxes'] : '';
$single_record = isset($_REQUEST['record']) ? $_REQUEST['record'] : 0;

if ($mass_merge != '') {
	$mass_merge = explode(';', $mass_merge);
	$temp_mass_merge = $mass_merge;
	if (array_pop($temp_mass_merge)=='') {
		array_pop($mass_merge);
	}
	//$mass_merge = implode(",",$mass_merge);
} elseif ($single_record != "") {
	$mass_merge = $single_record;
} else {
	die('Record Id is not found, cannot merge the document');
}

//<<<<<<<<<<<<<<<<header for csv and select columns for query>>>>>>>>>>>>>>>>>>>>>>>>
global $current_user;
require 'user_privileges/user_privileges_'.$current_user->id.'.php';
if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0 || $module == "Users" || $module == "Emails") {
	$query1="select vtiger_field.tablename,vtiger_field.columnname,vtiger_field.fieldlabel
		from vtiger_field
		where vtiger_field.tabid=7 and vtiger_field.presence in (0,2)
		order by vtiger_field.tablename";
	$params1 = array();
} else {
	$profileList = getCurrentUserProfileList();
	$query1="select vtiger_field.tablename,vtiger_field.columnname,vtiger_field.fieldlabel
		from vtiger_field
		INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid
		INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid
		where vtiger_field.tabid in (7) AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0
			AND vtiger_profile2field.profileid IN (". generateQuestionMarks($profileList) .") and vtiger_field.presence in (0,2)
			and vtiger_field.tablename <> 'vtiger_campaignrelstatus'
		GROUP BY vtiger_field.fieldid
		order by vtiger_field.tablename";
	$params1 = array($profileList);
}
$result = $adb->pquery($query1, $params1);
$y=$adb->num_rows($result);
$userNameSql = getSqlForNameInDisplayFormat(array('first_name' => 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

for ($x=0; $x<$y; $x++) {
	$tablename = $adb->query_result($result, $x, "tablename");
	$columnname = $adb->query_result($result, $x, "columnname");
	$querycolumns[$x] = $tablename.".".$columnname;
	if ($columnname == "smownerid") {
		$querycolumns[$x] = "case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as username,vtiger_users.first_name,vtiger_users.last_name,vtiger_users.user_name,vtiger_users.secondaryemail,vtiger_users.title,vtiger_users.phone_work,vtiger_users.department,vtiger_users.phone_mobile,vtiger_users.phone_other,vtiger_users.phone_fax,vtiger_users.email1,vtiger_users.phone_home,vtiger_users.email2,vtiger_users.address_street,vtiger_users.address_city,vtiger_users.address_state,vtiger_users.address_postalcode,vtiger_users.address_country";
	}
	$field_label[$x] = "LEAD_".strtoupper(str_replace(" ", "", $adb->query_result($result, $x, "fieldlabel")));
	if ($columnname == "smownerid") {
		$field_label[$x] = $field_label[$x].",USER_FIRSTNAME,USER_LASTNAME,USER_USERNAME,USER_SECONDARYEMAIL,USER_TITLE,USER_OFFICEPHONE,USER_DEPARTMENT,USER_MOBILE,USER_OTHERPHONE,USER_FAX,USER_EMAIL,USER_HOMEPHONE,USER_OTHEREMAIL,USER_PRIMARYADDRESS,USER_CITY,USER_STATE,USER_POSTALCODE,USER_COUNTRY";
	}
}

// Ordena etiquetas más grandes primero para que no se sutituyan subcadenas en el documento
// Por ejemplo, pongo LEAD_TIPOVIVIENDA delante de LEAD_TIPO, para que no se sustituya la subcadena LEAD_TIPO
$labels_length=$field_label;
function strlength($label, $clave) {
	global $labels_length;
	$labels_length[$clave] = strlen($label);
}
array_walk($labels_length, 'strlength');
array_multisort($labels_length, $field_label, $querycolumns);
$field_label=array_reverse($field_label);
$querycolumns=array_reverse($querycolumns);
$labels_length=array_reverse($labels_length);
$csvheader = implode(",", $field_label);
//<<<<<<<<<<<<<<<<End>>>>>>>>>>>>>>>>>>>>>>>>

if (count($querycolumns) > 0) {
	$selectcolumns = implode($querycolumns, ",");

	$query = "select ".$selectcolumns." from vtiger_leaddetails
	  inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_leaddetails.leadid
	  inner join vtiger_leadsubdetails on vtiger_leadsubdetails.leadsubscriptionid=vtiger_leaddetails.leadid
	  inner join vtiger_leadaddress on vtiger_leadaddress.leadaddressid=vtiger_leadsubdetails.leadsubscriptionid
	  inner join vtiger_leadscf on vtiger_leaddetails.leadid = vtiger_leadscf.leadid
	  left join vtiger_campaignleadrel on vtiger_leaddetails.leadid = vtiger_campaignleadrel.leadid
	  left join vtiger_campaignrelstatus on vtiger_campaignrelstatus.campaignrelstatusid = vtiger_campaignleadrel.campaignrelstatusid
	  LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
	  left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
	  where vtiger_crmentity.deleted=0 and vtiger_leaddetails.leadid in (". generateQuestionMarks($mass_merge) .")";

	$result = $adb->pquery($query, array($mass_merge));
	$avail_pick_arr = getAccessPickListValues('Leads');
	while ($columnValues = $adb->fetch_array($result)) {
		$y=$adb->num_fields($result);
		for ($x=0; $x<$y; $x++) {
			$value = $columnValues[$x];
			foreach ($columnValues as $key => $val) {
				if ($val == $value && $value != '') {
					if (array_key_exists($key, $avail_pick_arr)) {
						if (!in_array($val, $avail_pick_arr[$key])) {
							$value = "Not Accessible";
						}
					}
				}
			}
			//<<<<<<<<<<<<<<< For blank Fields >>>>>>>>>>>>>>>>>>>>>>>>>>>>
			if (trim($value) == "--None--" || trim($value) == "--none--") {
				$value = "";
			}
			//<<<<<<<<<<<<<<< End >>>>>>>>>>>>>>>>>>>>>>>>>>>>
			$actual_values[$x] = $value;
			$actual_values[$x] = str_replace('"', " ", $actual_values[$x]);
			//if value contains any line feed or carriage return replace the value with ".value."
			if (preg_match("/(\r?\n)/", $actual_values[$x])) {
				// <<< pag 21-Sep-2011 >>>
				// Replacement see: php.net/manual/en/function.str-replace.php
				// $str     = "Line 1\nLine 2\rLine 3\r\nLine 4\n";
				$order   = array("\r\n", "\n", "\r"); // order of replacement matters
				$replace = '!!'; // you choose your appropriate delimiters
				// They'll be replaced by an OO/LO macro once the resulting document has been downloaded
				// We now processes \r\n's first so they aren't converted twice.
				// $newstr = str_replace($order, $replace, $str);
				$actual_values[$x] = str_replace($order, $replace, $actual_values[$x]);
				// <<< pag 21-Sep-2011 END >>>
				// not needed ??? // $actual_values[$x] = '"'.$actual_values[$x].'"';
			}
			$actual_values[$x] = decode_html(str_replace(',', ' ', $actual_values[$x]));
		}
		$mergevalue[] = implode($actual_values, ',');
	}
	$csvdata = implode($mergevalue, '###');
} else {
	die('No fields to do Merge');
}
echo '<br><br><br>';
if ($extension == 'doc') {
	$datafilename = $randomfilename . '_data.csv';
	$handle = fopen($wordtemplatedownloadpath.$datafilename, 'wb');
	fwrite($handle, $csvheader."\r\n");
	fwrite($handle, str_replace("###", "\r\n", $csvdata));
	fclose($handle);
} elseif ($extension == 'odt') {
	//delete old .odt files in the wordtemplatedownload directory
	foreach (glob("$wordtemplatedownloadpath/*.odt") as $delefile) {
		unlink($delefile);
	}
	$mass_merge = (array)$mass_merge;
	foreach ($mass_merge as $idx => $entityid) {
		$temp_dir=entpack($filename, $wordtemplatedownloadpath, $fileContent);
		$concontent=file_get_contents($wordtemplatedownloadpath.$temp_dir.'/content.xml');
		unlink($wordtemplatedownloadpath.$temp_dir.'/content.xml');
		$new_filecontent=crmmerge($csvheader, $concontent, $idx, 'htmlspecialchars');
		$stycontent=file_get_contents($wordtemplatedownloadpath.$temp_dir.'/styles.xml');
		unlink($wordtemplatedownloadpath.$temp_dir.'/styles.xml');
		$new_filestyle=crmmerge($csvheader, $stycontent, $idx, 'htmlspecialchars');
		packen($entityid.$filename, $wordtemplatedownloadpath, $temp_dir, $new_filecontent, $new_filestyle);
		//Send Document to the Browser
		//header("Content-Type: $mimetype");
		//header("Content-Disposition: attachment; filename=$filename");
		//echo file_get_contents($wordtemplatedownloadpath .$filename);
		//readfile($root_directory .$wordtemplatedownloadpath .$filename);
		echo "&nbsp;&nbsp;<font size=+1><b><a href=cache/wordtemplatedownload/$entityid$filename>".$app_strings['DownloadMergeFile']."</a></b></font><br>";
		remove_dir($wordtemplatedownloadpath.$temp_dir);
	}
} elseif ($extension == 'rtf') {
	foreach (glob("$wordtemplatedownloadpath/*.rtf") as $delefile) {
		unlink($delefile);
	}
	$mass_merge = (array)$mass_merge;
	$filecontent = base64_decode($fileContent);
	foreach ($mass_merge as $idx => $entityid) {
		$handle = fopen($wordtemplatedownloadpath.$entityid.$filename, 'wb');
		$new_filecontent = crmmerge($csvheader, $filecontent, $idx, 'utf8Unicode');
		fwrite($handle, $new_filecontent);
		fclose($handle);
		echo "&nbsp;&nbsp;<font size=+1><b><a href=cache/wordtemplatedownload/$entityid$filename>".$app_strings['DownloadMergeFile']."</a></b></font><br>";
	}
} else {
	die('unknown file format');
}
?>
