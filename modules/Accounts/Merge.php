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
require_once 'data/CRMEntity.php';
global $app_strings, $default_charset;

$randomfilename = 'vt_' . str_replace(array('.',' '), '', microtime());

// $mergeTemplatePath and $mergeTemplateName are set in module/evvtgendoc/gendocAction.php
$fileContent = base64_encode(file_get_contents($mergeTemplatePath));
$extension=GetFileExtension($mergeTemplateName);
$filename= $randomfilename . "_mmrg.$extension";

$filesize=filesize($mergeTemplatePath);
$wordtemplatedownloadpath =$root_directory .'/cache/wordtemplatedownload/';
if (!is_dir($wordtemplatedownloadpath)) {
	@mkdir($wordtemplatedownloadpath);
}
$handle = fopen($wordtemplatedownloadpath.$filename, 'wb');
fwrite($handle, base64_decode($fileContent), $filesize);
fclose($handle);

//for mass merge
$single_record = isset($_REQUEST['recordval']) ? trim($_REQUEST['recordval'], ';') : 0;
$mass_merge = strpos($single_record, ';') ? $single_record : '';

if ($mass_merge != '') {
	$mass_merge = explode(';', $mass_merge);
	$temp_mass_merge = $mass_merge;
	if (array_pop($temp_mass_merge)=='') {
		array_pop($mass_merge);
	}
} elseif ($single_record != '') {
	$mass_merge = $single_record;
} else {
	die('Record Id is not found, cannot merge the document');
}

//for setting vtiger_accountid=0 for the contacts which are deleted
$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Contacts');
$ct_query = "select vtiger_crmentity.crmid from ".$crmEntityTable." where setype='Contacts' and deleted=1";
$result = $adb->pquery($ct_query, array());
$deleted_id = array();
while ($row = $adb->fetch_array($result)) {
	$deleted_id[] = $row['crmid'];
}

if (!empty($deleted_id)) {
	$update_query = "update vtiger_contactdetails set accountid = 0 where contactid in (". generateQuestionMarks($deleted_id) .")";
	$result = $adb->pquery($update_query, array($deleted_id));
}
//End setting accountid=0 for the contacts which are deleted

//<<<<<<<<<<<<<<<<header for csv and select columns for query>>>>>>>>>>>>>>>>>>>>>>>>
global $current_user;
$userprivs = $current_user->getPrivileges();
if ($userprivs->hasGlobalReadPermission() || $module == 'Users' || $module == 'Emails') {
	$query1="select vtiger_tab.name,vtiger_field.tablename,vtiger_field.columnname,vtiger_field.fieldlabel
		from vtiger_field
		inner join vtiger_tab on vtiger_tab.tabid = vtiger_field.tabid
		where vtiger_field.tabid in (6) and vtiger_field.uitype <> 61 and block <> 75 and block <> 30 and vtiger_field.presence in (0,2)
			and vtiger_field.tablename <> 'vtiger_campaignrelstatus'
		order by vtiger_field.tablename";
	$params1 = array();
} else {
	$profileList = getCurrentUserProfileList();
	$query1='select vtiger_tab.name,vtiger_field.tablename,vtiger_field.columnname,vtiger_field.fieldlabel
		from vtiger_field
		inner join vtiger_tab on vtiger_tab.tabid = vtiger_field.tabid
		INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid
		INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid
		where vtiger_field.tabid in (6) and vtiger_field.uitype <> 61 and vtiger_field.block <> 75 AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0
			AND vtiger_profile2field.profileid IN ('. generateQuestionMarks($profileList) .") and vtiger_field.presence in (0,2)
			AND vtiger_field.tablename <> 'vtiger_campaignrelstatus'
		GROUP BY vtiger_field.fieldid
		order by vtiger_field.tablename";
	$params1 = array($profileList);
}
$result = $adb->pquery($query1, $params1);
$y=$adb->num_rows($result);

for ($x=0; $x<$y; $x++) {
	$tablename = $adb->query_result($result, $x, 'tablename');
	$columnname = $adb->query_result($result, $x, 'columnname');
	$modulename = $adb->query_result($result, $x, 'name');

	if ($tablename == 'crmentity' && $modulename == 'Contacts') {
		$tablename = 'vtiger_crmentityContacts';
	}
	$querycolumns[$x] = $tablename.'.'.$columnname;
	if ($columnname == 'smownerid') {
		if ($modulename == 'Accounts') {
			$querycolumns[$x] = "case when (vtiger_users.user_name not like '') then vtiger_users.ename else vtiger_groups.groupname end as userjoinname,"
				.'vtiger_users.first_name,vtiger_users.last_name,vtiger_users.user_name,vtiger_users.secondaryemail,vtiger_users.title,vtiger_users.phone_work,'
				.'vtiger_users.department,vtiger_users.phone_mobile,vtiger_users.phone_other,vtiger_users.phone_fax,vtiger_users.email1,vtiger_users.phone_home,'
				.'vtiger_users.email2,vtiger_users.address_street,vtiger_users.address_city,vtiger_users.address_state,vtiger_users.address_postalcode,'
				.'vtiger_users.address_country';
		}
		if ($modulename == 'Contacts') {
			$querycolumns[$x] = "case when (usersContacts.user_name not like '') then usersContacts.ename else groupsContacts.groupname end as userjoincname";
		}
	}
	if ($columnname == 'parentid') {
		$querycolumns[$x] = 'vtiger_accountAccount.accountname';
	}
	if ($columnname == 'accountid') {
		$querycolumns[$x] = 'vtiger_accountContacts.accountname';
	}
	if ($columnname == 'reportsto') {
		$querycolumns[$x] = 'vtiger_contactdetailsContacts.lastname';
	}

	if ($modulename == 'Accounts') {
		$field_label[$x] = 'ACCOUNT_'.strtoupper(str_replace(' ', '', $adb->query_result($result, $x, 'fieldlabel')));
		if ($columnname == 'smownerid') {
			$field_label[$x] = $field_label[$x].',USER_FIRSTNAME,USER_LASTNAME,USER_USERNAME,USER_SECONDARYEMAIL,USER_TITLE,USER_OFFICEPHONE,USER_DEPARTMENT,'
				.'USER_MOBILE,USER_OTHERPHONE,USER_FAX,USER_EMAIL,USER_HOMEPHONE,USER_OTHEREMAIL,USER_PRIMARYADDRESS,USER_CITY,USER_STATE,USER_POSTALCODE,USER_COUNTRY';
		}
	}

	if ($modulename == 'Contacts') {
		$field_label[$x] = 'CONTACT_'.strtoupper(str_replace(' ', '', $adb->query_result($result, $x, 'fieldlabel')));
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
$csvheader = implode(',', $field_label);
//<<<<<<<<<<<<<<<<End>>>>>>>>>>>>>>>>>>>>>>>>

if (!empty($querycolumns)) {
	$selectcolumns = implode(',', $querycolumns);
	$crmEntityTable1 = CRMEntity::getcrmEntityTableAlias('Accounts');
	$crmEntityTable2 = CRMEntity::getcrmEntityTableAlias('Contacts', true);
	$query = "select  $selectcolumns from vtiger_account
		inner join $crmEntityTable1 on vtiger_crmentity.crmid=vtiger_account.accountid
		inner join vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid
		inner join vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid
		inner join vtiger_accountscf on vtiger_account.accountid = vtiger_accountscf.accountid
		left join vtiger_account as vtiger_accountAccount on vtiger_accountAccount.accountid = vtiger_account.parentid
		left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
		LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
		left join vtiger_contactdetails on vtiger_contactdetails.accountid=vtiger_account.accountid
		left join $crmEntityTable2 as vtiger_crmentityContacts on vtiger_crmentityContacts.crmid = vtiger_contactdetails.contactid
		left join vtiger_contactaddress on vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid
		left join vtiger_contactsubdetails on vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid
		left join vtiger_contactscf on vtiger_contactdetails.contactid = vtiger_contactscf.contactid
		left join vtiger_customerdetails on vtiger_contactdetails.contactid = vtiger_customerdetails.customerid
		left join vtiger_contactdetails as vtiger_contactdetailsContacts on vtiger_contactdetailsContacts.contactid = vtiger_contactdetails.reportsto
		left join vtiger_account as vtiger_accountContacts on vtiger_accountContacts.accountid = vtiger_contactdetails.accountid
		left join vtiger_users as usersContacts on usersContacts.id = vtiger_crmentityContacts.smownerid
		LEFT JOIN vtiger_groups as groupsContacts ON groupsContacts.groupid = vtiger_crmentity.smownerid
		where vtiger_crmentity.deleted=0 and (vtiger_crmentityContacts.deleted=0 || vtiger_crmentityContacts.deleted is null)
			and vtiger_account.accountid in(". generateQuestionMarks($mass_merge) .')';
	$result = $adb->pquery($query, array($mass_merge));
	$avail_pick_arr = getAccessPickListValues('Accounts');
	while ($columnValues = $adb->fetch_array($result)) {
		$y=$adb->num_fields($result);
		for ($x=0; $x<$y; $x++) {
			$value = $columnValues[$x];
			foreach ($columnValues as $key => $val) {
				if ($val == $value && $value != '' && array_key_exists($key, $avail_pick_arr) && !in_array($val, $avail_pick_arr[$key])) {
					$value = 'Not Accessible';
				}
			}
			//<<<<<<<<<<<<<<< For blank Fields >>>>>>>>>>>>>>>>>>>>>>>>>>>>
			if (trim($value) == '--None--' || trim($value) == '--none--') {
				$value = '';
			}
			//<<<<<<<<<<<<<<< End >>>>>>>>>>>>>>>>>>>>>>>>>>>>
			$actual_values[$x] = $value;
			$actual_values[$x] = str_replace('"', ' ', $actual_values[$x]);
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
		$mergevalue[] = implode(',', $actual_values);
	}
	$csvdata = implode('###', $mergevalue);
} else {
	die('No fields to do Merge');
}
echo '<br><br><br>';
if ($extension == 'doc') {
	$datafilename = $randomfilename . '_data.csv';
	$handle = fopen($wordtemplatedownloadpath.$datafilename, 'wb');
	fwrite($handle, $csvheader."\r\n");
	fwrite($handle, str_replace('###', "\r\n", $csvdata));
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
		echo "&nbsp;&nbsp;<font size=+1><b><a href=cache/wordtemplatedownload/$entityid$filename>".$app_strings['DownloadMergeFile'].'</a></b></font><br>';
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
		echo "&nbsp;&nbsp;<font size=+1><b><a href=cache/wordtemplatedownload/$entityid$filename>".$app_strings['DownloadMergeFile'].'</a></b></font><br>';
	}
} else {
	die('unknown file format');
}
?>
