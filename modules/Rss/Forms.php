<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'modules/Rss/Rss.php';

$oRss = new vtigerRSS();
$allrsshtml = $oRss->getAllRssFeeds();

function get_rssfeeds_form() {
	$oRss = new vtigerRSS();
	$allrsshtml = $oRss->getRSSCategoryHTML();
	//$starred_rss_html = $oRss->getStarredRssFolder();
	$the_form  = '<table width="100%" border="0" cellspacing="2" cellpadding="0" style="margin-top:10px">';
	$the_form .= $allrsshtml;
	$the_form .= '</table>';
	return $the_form;
}
?>
