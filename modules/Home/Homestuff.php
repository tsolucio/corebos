<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ('License'); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/home.php';
require_once 'modules/Rss/Rss.php';
$oHomestuff=new Homestuff();
if (!empty($_REQUEST['stufftype'])) {
	$oHomestuff->stufftype = vtlib_purify($_REQUEST['stufftype']);
}

if (!empty($_REQUEST['stufftitle'])) {
	if (strlen($_REQUEST['stufftitle'])>100) {
		$temp_str = substr($_REQUEST['stufftitle'], 0, 97).'...';
		$oHomestuff->stufftitle= $temp_str;
	} else {
		$oHomestuff->stufftitle = vtlib_purify($_REQUEST['stufftitle']);
	}
	// Remove HTML/PHP tags from the input
	if (isset($oHomestuff->stufftitle)) {
		$oHomestuff->stufftitle = strip_tags($oHomestuff->stufftitle);
	}
}

if (!empty($_REQUEST['selmodule'])) {
	$oHomestuff->selmodule = vtlib_purify($_REQUEST['selmodule']);
}

if (!empty($_REQUEST['maxentries'])) {
	$oHomestuff->maxentries = vtlib_purify($_REQUEST['maxentries']);
}

if (!empty($_REQUEST['selFiltername'])) {
	$oHomestuff->selFiltername = vtlib_purify($_REQUEST['selFiltername']);
}

if (!empty($_REQUEST['fldname'])) {
	$oHomestuff->fieldvalue = vtlib_purify($_REQUEST['fldname']);
}

if (!empty($_REQUEST['txtRss'])) {
	$ooRss=new vtigerRSS();
	if ($ooRss->setRSSUrl($_REQUEST['txtRss'])) {
		$oHomestuff->txtRss = vtlib_purify($_REQUEST['txtRss']);
	} else {
		return false;
	}
}

if (!empty($_REQUEST['txtURL'])) {
	$oHomestuff->txtURL = vtlib_purify($_REQUEST['txtURL']);
}
if (isset($_REQUEST['seldashbd']) && $_REQUEST['seldashbd']!='') {
	$oHomestuff->seldashbd = vtlib_purify($_REQUEST['seldashbd']);
}

if (isset($_REQUEST['seldashtype']) && $_REQUEST['seldashtype']!='') {
	$oHomestuff->seldashtype = vtlib_purify($_REQUEST['seldashtype']);
}

if (isset($_REQUEST['seldeftype']) && $_REQUEST['seldeftype']!='') {
	$seldeftype = vtlib_purify($_REQUEST['seldeftype']);
	$defarr=explode(',', $seldeftype);
	$oHomestuff->defaultvalue=$defarr[0];
	$deftitlehash=$defarr[1];
	$oHomestuff->defaulttitle=str_replace('#', ' ', $deftitlehash);
}

if (isset($_REQUEST['selreport']) && $_REQUEST['selreport']!='') {
	$oHomestuff->selreport = vtlib_purify($_REQUEST['selreport']);
}

if (isset($_REQUEST['selreportcharttype']) && $_REQUEST['selreportcharttype']!='') {
	$oHomestuff->selreportcharttype = vtlib_purify($_REQUEST['selreportcharttype']);
}

$loaddetail=$oHomestuff->addStuff();
echo $loaddetail;
?>
