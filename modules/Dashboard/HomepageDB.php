<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once("modules/Dashboard/Entity_charts.php");
require('user_privileges/user_privileges_'.$current_user->id.'.php');

global $current_user,$user_id,$date_start,$end_date,$tmp_dir,$mod_strings,$app_strings;
$type='recordsforuser';

$graph_details = module_Chart_HomePageDashboard($current_user);

if (!empty($graph_details) && $graph_details[1] != 0) { // END
    $name_val=$graph_details[0];
    $cnt_val=$graph_details[1];
    $graph_title=$graph_details[2];
    $target_val=$graph_details[3];
    $graph_date=$graph_details[4];
    $urlstring=$graph_details[5];
    $cnt_table=$graph_details[6];
	$test_target_val=$graph_details[7];

    $width=560;
    $height=225;
    $top=30;
    $left=140;
    $right=0;
    $bottom=120;
    $title=$graph_title;
	//Giving the Cached image name
	$cache_file_name=abs(crc32($current_user->id))."_".$type."_".crc32($date_start.$end_date).".png";
	$html_imagename="setype"; //Html image name for the graph
	$sHTML = render_graph($tmp_dir."vert_".$cache_file_name,$html_imagename."_vert",$cnt_val,$name_val,$width,$height,$left,$right,$top,$bottom,$title,$target_val,"vertical");
	echo $sHTML;
}else{
	echo $mod_strings['LBL_NO_DATA'];
}
?>
