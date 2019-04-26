<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/home.php';
require_once 'modules/Rss/Rss.php';

$iHomestuff=new Homestuff();

if (!empty($_REQUEST['stufftype'])) {
	$iHomestuff->stufftype=$_REQUEST['stufftype'];
}

if (!empty($_REQUEST['selmodule'])) {
	$iHomestuff->selmodule=$_REQUEST['selmodule'];
}

if (!empty($_REQUEST['maxentries'])) {
	$iHomestuff->maxentries=$_REQUEST['maxentries'];
}

if (!empty($_REQUEST['selFiltername'])) {
	$iHomestuff->selFiltername=$_REQUEST['selFiltername'];
}

if (!empty($_REQUEST['selAggregatename'])) {
	$iHomestuff->selAggregatename=$_REQUEST['selAggregatename'];
}

if (!empty($_REQUEST['fldname'])) {
	$iHomestuff->fieldvalue=$_REQUEST['fldname'];
}

if (!empty($_REQUEST['txtRss'])) {
	$ooRss=new vtigerRSS();
	if ($ooRss->setRSSUrl($_REQUEST['txtRss'])) {
		$iHomestuff->txtRss=$_REQUEST['txtRss'];
	} else {
		return false;
	}
}

if (!empty($_REQUEST['txtURL'])) {
	$iHomestuff->txtURL = $_REQUEST['txtURL'];
}
if (isset($_REQUEST['seldashbd']) && $_REQUEST['seldashbd']!="") {
	$iHomestuff->seldashbd=$_REQUEST['seldashbd'];
}

if (isset($_REQUEST['seldashtype']) && $_REQUEST['seldashtype']!="") {
	$iHomestuff->seldashtype=$_REQUEST['seldashtype'];
}

if (isset($_REQUEST['seldeftype']) && $_REQUEST['seldeftype']!="") {
	$seldeftype=$_REQUEST['seldeftype'];
	$defarr=explode(",", $seldeftype);
	$iHomestuff->defaultvalue=$defarr[0];
	$deftitlehash=$defarr[1];
	$iHomestuff->defaulttitle=str_replace("#", " ", $deftitlehash);
}

$loaddetail=$iHomestuff->addCustomWidgetFilter();
echo $loaddetail;
?>