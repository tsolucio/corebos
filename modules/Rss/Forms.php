<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header$
 * Description:  Contains a variety of utility functions specific to this module.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once("modules/Rss/Rss.php");

$oRss = new vtigerRSS();
$allrsshtml = $oRss->getAllRssFeeds();
function get_rssfeeds_form() {
global $mod_strings;
global $app_strings;
global $app_list_strings;
global $image_path;

$oRss = new vtigerRSS();
$allrsshtml = $oRss->getRSSCategoryHTML();
//$starred_rss_html = $oRss->getStarredRssFolder();

$the_form .= '<table width="100%" border="0" cellspacing="2" cellpadding="0" style="margin-top:10px">';

$the_form .= $allrsshtml;

$the_form .= "</table>";
return $the_form;
}

?>
