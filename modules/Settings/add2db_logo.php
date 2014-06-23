<?php

/*********************************************************************************
** The contents of this file are subject to the Evolutivo BPM License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');
global $log;


	//$uploaddir = $root_directory ."/test/logo/" ;// set this to wherever
	$saveflag="true";
	$nologo_specified="true";
	$error_flag ="";
	$nologo_specified="false";
	$binFile1 = $_FILES['binFile1']['name'];
	if(isset($_REQUEST['binFile1_hidden'])) {
		$filename1 = $_REQUEST['binFile1_hidden'];
	} else {
		$filename1 = ltrim(basename(" ".$binFile1));
	}
        $log->debug("brisi".$filename1);
        $binFile2 = $_FILES['binFile2']['name'];
	if(isset($_REQUEST['binFile2_hidden'])) {
		$filename2 = $_REQUEST['binFile2_hidden'];
	} else {
		$filename2 = ltrim(basename(" ".$binFile2));
	}
	$filetype= $_FILES['binFile1']['type'];
	$filesize = $_FILES['binFile1']['size'];

	$filetype_array=explode("/",$filetype);

	$file_type_val=strtolower($filetype_array[1]);

	if($filesize != 0)
	{
		if (($file_type_val == "jpeg" ) || ($file_type_val == "png") || ($file_type_val == "jpg" ) ||  ($file_type_val == "pjpeg" ) || ($file_type_val == "x-png") ) //Checking whether the file is an image or not
		{
			if(stristr($binFile, '.gif') != FALSE)
			{
				$savelogo="false";
				$error_flag ="1";
			}
			else if($result!=false)
			{
				$savelogo="true";
			}
		}
		else
		{
			$savelogo="false";
			$error_flag ="1";
		}

	}
	else
	{
		$savelogo="false";
		if($filename1 != "")
			$error_flag ="2";
	}

	$errorCode =  $_FILES['binFile1']['error'];
	if($errorCode == 4)
	{
	  	$savelogo="false";
		$errorcode="";
		$error_flag="5";
		$nologo_specified="true";
	}
	else if($errorCode == 2)
	{
	   	$error_flag ="3";
	   	$savelogo="false";
		$nologo_specified="false";
	}
	else if($errorCode == 3 )
	{
		$error_flag ="4";
  		$savelogo="false";
		$nologo_specified="false";
	}
	if($savelogo=="true")
	{
		move_uploaded_file($_FILES["binFile1"]["tmp_name"],$root_directory."/themes/login/images/".$filename1);
                move_uploaded_file($_FILES["binFile2"]["tmp_name"],$root_directory."/themes/login/images/".$filename2);
	}

	if($saveflag=="true")
	{
		$organization_logoname1=$filename1;                $organization_logoname2=$filename2;
                    if($organization_logoname1 != "" && $organization_logoname2 != ""){
			$sql = "UPDATE vtiger_parametrize
				SET logo_login = ?, logo_top = ?
                                WHERE param_id = 1";
			$params = array(decode_html($organization_logoname1), decode_html($organization_logoname2));
                    }
                    else if($organization_logoname1 == "" && $organization_logoname2 != ""){
			$sql = "UPDATE vtiger_parametrize
				SET logo_top = ?
                                WHERE param_id = 1";
			$params = array(decode_html($organization_logoname2));
                    }
                    else if($organization_logoname2 == "" && $organization_logoname1 != "" ){
                        $sql = "UPDATE vtiger_parametrize
				SET logo_login = ?
                                WHERE param_id = 1";
			$params = array(decode_html($organization_logoname1));
                    }
		$adb->pquery($sql, $params);

		if($savelogo=="true")
		{
			header("Location: index.php?parenttab=Settings&module=Settings&action=Parametrize");
		}
		elseif($savelogo=="false")
		{

    		header("Location: index.php?parenttab=Settings&module=Settings&action=Parametrize&flag=error");
		}


	}
?>