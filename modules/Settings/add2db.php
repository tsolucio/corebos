<?php
/* * *******************************************************************************
 * * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ****************************************************************************** */
require_once 'include/database/PearDatabase.php';
require_once 'include/utils/utils.php';
global $upload_badext, $root_directory, $adb, $log;

$uploaddir = $root_directory . '/test/logo/'; // set this to wherever
$saveflag = 'true';
$error_flag = '';
$savelogo = 'false';
$savefrontlogo = 'false';
$savefaviconlogo = 'false';
$nologo_specified='false';
$binFile = '';
$binFrontFile = '';
$binFaviconFile = '';
$image_extensions_allowed = array('jpeg', 'png', 'jpg', 'pjpeg', 'x-png');
$filename = '';
if (isset($_FILES) && isset($_FILES['binFile']) && !empty($_FILES['binFile']['name'])) {
	$binFile = $_FILES['binFile']['name'];
	if (isset($_REQUEST['binFile_hidden'])) {
		$filename = sanitizeUploadFileName(vtlib_purify($_REQUEST['binFile_hidden']), $upload_badext);
	} else {
		$binFile = sanitizeUploadFileName($binFile, $upload_badext);
		$filename = ltrim(basename(' ' . $binFile));
	}

	$filetype = $_FILES['binFile']['type'];
	$filesize = $_FILES['binFile']['size'];
	$filetype_array = explode('/', $filetype);
	$file_type_val = strtolower($filetype_array[1]);

	if ($filesize != 0) {
		if (in_array($file_type_val, $image_extensions_allowed)) { //Checking whether the file is an image or not
			$savelogo = 'true';
		} else {
			$savelogo = 'false';
			$error_flag = '1';
		}
	} else {
		$savelogo = 'false';
		if ($filename != '') {
			$error_flag = '2';
		}
	}
	$errorCode = $_FILES['binFile']['error'];
	if ($errorCode == 4) {
		$savelogo = 'false';
		$error_flag = '5';
		$nologo_specified = 'true';
	} elseif ($errorCode == 2) {
		$error_flag = '3';
		$savelogo = 'false';
		$nologo_specified = 'false';
	} elseif ($errorCode == 3) {
		$error_flag = '4';
		$savelogo = 'false';
		$nologo_specified = 'false';
	}
}
$front_filename = '';
if (isset($_FILES) && isset($_FILES['binFrontFile']) && !empty($_FILES['binFrontFile']['name'])) {
	$binFrontFile = $_FILES['binFrontFile']['name'];
	if (isset($_REQUEST['binFrontFile_hidden'])) {
		$front_filename = sanitizeUploadFileName(vtlib_purify($_REQUEST['binFrontFile_hidden']), $upload_badext);
	} else {
		$binFrontFile = sanitizeUploadFileName($binFrontFile, $upload_badext);
		$front_filename = ltrim(basename(' ' . $binFrontFile));
	}
	$front_filetype = $_FILES['binFrontFile']['type'];
	$front_filesize = $_FILES['binFrontFile']['size'];
	$font_filetype_array = explode('/', $front_filetype);
	$front_file_type_val = strtolower($font_filetype_array[1]);
	if ($front_filesize != 0) {
		if (in_array($front_file_type_val, $image_extensions_allowed)) { //Checking whether the file is an image or not
				$savefrontlogo = 'true';
		} else {
			$savefrontlogo = 'false';
			$error_flag = '1';
		}
	} else {
		$savefrontlogo = 'false';
		if ($front_filename != '') {
			$error_flag = '2';
		}
	}
	$errorCode = $_FILES['binFrontFile']['error'];
	if ($errorCode == 4) {
		$savefrontlogo = 'false';
		$error_flag = '5';
		$nologo_specified = 'true';
	} elseif ($errorCode == 2) {
		$error_flag = '3';
		$savefrontlogo = 'false';
		$nologo_specified = 'false';
	} elseif ($errorCode == 3) {
		$error_flag = '4';
		$savefrontlogo = 'false';
		$nologo_specified = 'false';
	}
}
$favicon_filename = '';
if (isset($_FILES) && isset($_FILES['binFaviconFile']) && !empty($_FILES['binFaviconFile']['name'])) {
	$binFaviconFile = $_FILES['binFaviconFile']['name'];
	if (isset($_REQUEST['binFaviconFile_hidden'])) {
		$favicon_filename = sanitizeUploadFileName(vtlib_purify($_REQUEST['binFaviconFile_hidden']), $upload_badext);
	} else {
		$binFaviconFile = sanitizeUploadFileName($binFaviconFile, $upload_badext);
		$favicon_filename = ltrim(basename(' ' . $binFaviconFile));
	}
	$favicon_filetype = $_FILES['binFaviconFile']['type'];
	$favicon_filesize = $_FILES['binFaviconFile']['size'];
	$font_filetype_array = explode('/', $favicon_filetype);
	$favicon_file_type_val = strtolower($font_filetype_array[1]);
	if ($favicon_filesize != 0) {
		if (in_array($favicon_file_type_val, $image_extensions_allowed)) { //Checking whether the file is an image or not
				$savefaviconlogo = 'true';
		} else {
			$savefaviconlogo = 'false';
			$error_flag = '1';
		}
	} else {
		$savefaviconlogo = 'false';
		if ($favicon_filename != '') {
			$error_flag = '2';
		}
	}
	$errorCode = $_FILES['binFaviconFile']['error'];
	if ($errorCode == 4) {
		$savefaviconlogo = 'false';
		$error_flag = '5';
		$nologo_specified = 'true';
	} elseif ($errorCode == 2) {
		$error_flag = '3';
		$savefaviconlogo = 'false';
		$nologo_specified = 'false';
	} elseif ($errorCode == 3) {
		$error_flag = '4';
		$savefaviconlogo = 'false';
		$nologo_specified = 'false';
	}
}
if ($error_flag == '') {
	if ($savelogo == 'true') {
		move_uploaded_file($_FILES['binFile']['tmp_name'], $uploaddir . $filename);
	}
	if ($savefrontlogo == 'true') {
		move_uploaded_file($_FILES['binFrontFile']['tmp_name'], $uploaddir . $front_filename);
	}
	if ($savefaviconlogo == 'true') {
		move_uploaded_file($_FILES['binFaviconFile']['tmp_name'], $uploaddir . $favicon_filename);
	}
		$organization_name = vtlib_purify($_REQUEST['organization_name']);
		$org_name = $_REQUEST['org_name'];
		$organization_address = vtlib_purify($_REQUEST['organization_address']);
		$organization_city = vtlib_purify($_REQUEST['organization_city']);
		$organization_state = vtlib_purify($_REQUEST['organization_state']);
		$organization_code = vtlib_purify($_REQUEST['organization_code']);
		$organization_country = vtlib_purify($_REQUEST['organization_country']);
		$organization_phone = vtlib_purify($_REQUEST['organization_phone']);
		$organization_fax = vtlib_purify($_REQUEST['organization_fax']);
		$organization_website = vtlib_purify($_REQUEST['organization_website']);

		$organization_logoname = $filename;
		$front_logoname = $front_filename;
		$favicon_logoname = $favicon_filename;
	if (!isset($organization_logoname)) {
		$organization_logoname = '';
	}

		$sql = 'SELECT * FROM vtiger_organizationdetails WHERE organizationname = ?';
		$result = $adb->pquery($sql, array($org_name));
		$org_name = decode_html($adb->query_result($result, 0, 'organizationname'));
		$org_logo = $adb->query_result($result, 0, 'logoname');
	if (!isset($front_logoname)) {
		$front_logo = $adb->query_result($result, 0, 'frontlogo');
	}
	if (!isset($favicon_logoname)) {
		$favicon_logo = $adb->query_result($result, 0, 'faviconlogo');
	}
	if ($org_name == '') {
		$organizationId = $adb->getUniqueID('vtiger_organizationdetails');
		$sql = 'INSERT INTO vtiger_organizationdetails
			(organization_id,organizationname,address,city,state,code,country,phone,fax,website,logoname,frontlogo,faviconlogo) values (?,?,?,?,?,?,?,?,?,?,?,?,?)';
		$params = array(
			$organizationId, $organization_name, $organization_address, $organization_city, $organization_state, $organization_code,
			$organization_country, $organization_phone, $organization_fax, $organization_website, $organization_logoname,$front_logoname,$favicon_logoname
		);
	} else {
		if ($savelogo == 'true') {
			$organization_logoname = $filename;
		} elseif ($savelogo == 'false' && $error_flag == '') {
			$savelogo = 'true';
			$organization_logoname = vtlib_purify($_REQUEST['PREV_FILE']);
		} else {
			$organization_logoname = vtlib_purify($_REQUEST['PREV_FILE']);
		}
		if ($nologo_specified == 'true') {
			$savelogo = 'true';
			$organization_logoname = $org_logo;
		}
		if ($savefrontlogo == 'true') {
			$front_logoname = $front_filename;
		} elseif ($savefrontlogo == 'false' && $error_flag == '') {
			$savefrontlogo = 'true';
			$front_logoname = vtlib_purify($_REQUEST['PREV_FRONT_FILE']);
		} else {
			$front_logoname = vtlib_purify($_REQUEST['PREV_FRONT_FILE']);
		}
		if ($nologo_specified == 'true') {
			$savefrontlogo = 'true';
			$front_logoname = $front_logo;
		}
		if ($savefaviconlogo == 'true') {
			$favicon_logoname = $favicon_filename;
		} elseif ($savefaviconlogo == 'false' && $error_flag == '') {
			$savefrontlogo = 'true';
			$favicon_logoname = vtlib_purify($_REQUEST['PREV_FAVICON_FILE']);
		} else {
			$favicon_logo = vtlib_purify($_REQUEST['PREV_FAVICON_FILE']);
		}
		if ($nologo_specified == 'true') {
			$savefaviconlogo = 'true';
			$favicon_logoname = $favicon_logo;
		}
		$sql = 'UPDATE vtiger_organizationdetails
			SET organizationname = ?, address = ?, city = ?, state = ?, code = ?, country = ?,
			phone = ?, fax = ?, website = ?, logoname = ?,frontlogo = ?,faviconlogo = ? WHERE organizationname = ?';
		$params = array(
			$organization_name, $organization_address, $organization_city, $organization_state, $organization_code,
			$organization_country, $organization_phone, $organization_fax, $organization_website, decode_html($organization_logoname),
			decode_html($front_logoname), decode_html($favicon_logoname), $org_name
		);
	}
	$adb->pquery($sql, $params);

	if ($savelogo == 'true') {
		header('Location: index.php?parenttab=Settings&module=Settings&action=OrganizationConfig');
	} elseif ($savelogo == 'false') {
		header('Location: index.php?parenttab=Settings&module=Settings&action=EditCompanyDetails&flag=' . $error_flag);
	}
} else {
	header('Location: index.php?parenttab=Settings&module=Settings&action=EditCompanyDetails&flag=' . $error_flag);
}
?>