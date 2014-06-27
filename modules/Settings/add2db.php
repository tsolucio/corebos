<?php
/* * *******************************************************************************
 * * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * 
 * ****************************************************************************** */

require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');
global $upload_badext, $root_directory, $adb;

$uploaddir = $root_directory . "/test/logo/"; // set this to wherever
$saveflag = "true";
$error_flag = "";
$savelogo = "false";
$nologo_specified="false";
$binFile = '';
if (isset($_FILES) and isset($_FILES['binFile']) and !empty($_FILES['binFile']['name'])) {
	$binFile = $_FILES['binFile']['name'];
	$image_extensions_allowed = array('jpeg', 'png', 'jpg', 'pjpeg', 'x-png');
	if (isset($_REQUEST['binFile_hidden'])) {
		$filename = sanitizeUploadFileName(vtlib_purify($_REQUEST['binFile_hidden']), $upload_badext);
	} else {
		$binFile = sanitizeUploadFileName($binFile, $upload_badext);
		$filename = ltrim(basename(" " . $binFile));
	}

	$filetype = $_FILES['binFile']['type'];
	$filesize = $_FILES['binFile']['size'];
	$filetype_array = explode("/", $filetype);
	$file_type_val = strtolower($filetype_array[1]);

	if ($filesize != 0) {
		if (in_array($file_type_val, $image_extensions_allowed)) { //Checking whether the file is an image or not
				$savelogo = "true";
		} else {
			$savelogo = "false";
			$error_flag = "1";
		}
	} else {
		$savelogo = "false";
		if ($filename != "")
			$error_flag = "2";
	}
	$errorCode = $_FILES['binFile']['error'];
	if ($errorCode == 4) {
		$savelogo = "false";
		$error_flag = "5";
		$nologo_specified = "true";
	} else if ($errorCode == 2) {
		$error_flag = "3";
		$savelogo = "false";
		$nologo_specified = "false";
	} else if ($errorCode == 3) {
		$error_flag = "4";
		$savelogo = "false";
		$nologo_specified = "false";
	}
}
if ($error_flag == "") {
	if ($savelogo == "true") {
		move_uploaded_file($_FILES["binFile"]["tmp_name"], $uploaddir . $_FILES["binFile"]["name"]);
	}
		$organization_name = vtlib_purify($_REQUEST['organization_name']);
		$org_name = vtlib_purify($_REQUEST['org_name']);
		$organization_address = from_html($_REQUEST['organization_address']);
		$organization_city = from_html($_REQUEST['organization_city']);
		$organization_state = from_html($_REQUEST['organization_state']);
		$organization_code = from_html($_REQUEST['organization_code']);
		$organization_country = from_html($_REQUEST['organization_country']);
		$organization_phone = from_html($_REQUEST['organization_phone']);
		$organization_fax = from_html($_REQUEST['organization_fax']);
		$organization_website = from_html($_REQUEST['organization_website']);

		$organization_logoname = $filename;
		if (!isset($organization_logoname))
			$organization_logoname = "";

		$sql = "SELECT * FROM vtiger_organizationdetails WHERE organizationname = ?";
		$result = $adb->pquery($sql, array($org_name));
		$org_name = decode_html($adb->query_result($result, 0, 'organizationname'));
		$org_logo = $adb->query_result($result, 0, 'logoname');

		if ($org_name == '') {
			$organizationId = $this->db->getUniqueID('vtiger_organizationdetails');
			$sql = "INSERT INTO vtiger_organizationdetails
				(organization_id,organizationname, address, city, state, code, country, phone, fax, website, logoname) values (?,?,?,?,?,?,?,?,?,?)";
			$params = array($organizationId, $organization_name, $organization_address, $organization_city, $organization_state, $organization_code,
				$organization_country, $organization_phone, $organization_fax, $organization_website, $organization_logoname);
		} else {
			if ($savelogo == "true") {
				$organization_logoname = $filename;
			} elseif ($savelogo == "false" && $error_flag == "") {
				$savelogo = "true";
				$organization_logoname = vtlib_purify($_REQUEST['PREV_FILE']);
			} else {
				$organization_logoname = vtlib_purify($_REQUEST['PREV_FILE']);
			}
			if ($nologo_specified == "true") {
				$savelogo = "true";
				$organization_logoname = $org_logo;
			}

			$sql = "UPDATE vtiger_organizationdetails
				SET organizationname = ?, address = ?, city = ?, state = ?, code = ?, country = ?, 
				phone = ?, fax = ?, website = ?, logoname = ? WHERE organizationname = ?";
			$params = array($organization_name, $organization_address, $organization_city, $organization_state, $organization_code,
				$organization_country, $organization_phone, $organization_fax, $organization_website, decode_html($organization_logoname), $org_name);
		}
		$adb->pquery($sql, $params);

		if ($savelogo == "true") {
			header("Location: index.php?parenttab=Settings&module=Settings&action=OrganizationConfig");
		} elseif ($savelogo == "false") {
			header("Location: index.php?parenttab=Settings&module=Settings&action=EditCompanyDetails&flag=" . $error_flag);
		}
} else {
	header("Location: index.php?parenttab=Settings&module=Settings&action=EditCompanyDetails&flag=" . $error_flag);
}
?>

