<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'modules/Dashboard/Entity_charts.php';
require_once 'include/utils/ChartUtils.php';

global $current_user, $mod_strings;
$type='recordsforuser';

$graph_details = module_Chart_HomePageDashboard($current_user);

if (!empty($graph_details) && $graph_details[1] != 0) {
	$labels = ChartUtils::convertToArray($graph_details[0], true, true);
	$values = $graph_details[1];
	$graph_title = $graph_details[2];
	$target_values = ChartUtils::convertToArray($graph_details[3], false, true);
	$graph_date = $graph_details[4];
	$urlstring = $graph_details[5];
	$cnt_table = $graph_details[6];
	$test_target_val = $graph_details[7];

	$width=560;
	$height=225;
	$top=30;
	$left=140;
	$right=0;
	$bottom=120;
	$html_imagename='setype';
	$sHTML = ChartUtils::getChartHTML($labels, $values, $graph_title, $target_values, $html_imagename, $width, $height, $left, $right, $top, $bottom, 'bar', 'top');
	echo $sHTML;
} else {
	echo $mod_strings['LBL_NO_DATA'];
}
?>
