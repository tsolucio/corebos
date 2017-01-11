<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/utils/utils.php');
require_once("include/utils/ChartUtils.php");

/* Function to get the Account name for a given account id
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 */
function get_account_name($acc_id)
{
	global $adb;
	$acc_qry="select accountname from vtiger_account where accountid =?";
	$acc_result=$adb->pquery($acc_qry, array($acc_id));
	$no_acc_rows=$adb->num_rows($acc_result);

	if($no_acc_rows!=0)
	{
		while($acc_row = $adb->fetch_array($acc_result))
		{
			$name_val=$acc_row['accountname'];
		}
		$name=$name_val;
	}
	else
		$name="";
	return $name;
}

/**
 * Performance Optimization: Module Chart for Home Page Dashboard
 */
function module_Chart_HomePageDashboard($userinfo) {

	global $adb, $app_strings;

	$user_id = $userinfo->id;

	$graph_details = Array();
	$modrecords  = Array();

	// List of modules which needs to be considered for chart
	$module_list = Array('Accounts','Potentials','Contacts','Leads','Quotes','SalesOrder','PurchaseOrder','Invoice','HelpDesk','Calendar','Campaigns');
	// List of special module to handle
	$spl_modules = Array('Leads', 'HelpDesk', 'Potentials', 'Calendar');

	// Leads module
	$val_conv = ((isset($_COOKIE['LeadConv']) && $_COOKIE['LeadConv'] == 'true') ? 'le.converted = 1' : 'le.converted = 0 OR le.converted IS NULL');
	$leadcountres = $adb->query("SELECT count(*) as count FROM vtiger_crmentity se INNER JOIN vtiger_leaddetails le on le.leadid = se.crmid
		WHERE se.deleted = 0 AND se.smownerid = $user_id AND ($val_conv)");
	$modrecords['Leads'] = $adb->query_result($leadcountres, 0, 'count');

	// HelpDesk module
	$helpdeskcountres = $adb->query("SELECT count(*) as count FROM vtiger_crmentity se INNER JOIN vtiger_troubletickets tt ON tt.ticketid = se.crmid
		WHERE se.deleted = 0 AND se.smownerid = $user_id AND (tt.status != 'Closed' OR tt.status IS NULL)");
	$modrecords['HelpDesk']=$adb->query_result($helpdeskcountres,0,'count');

	// Potentials module
	$potcountres = $adb->query("SELECT count(*) as count FROM vtiger_crmentity se INNER JOIN vtiger_potential pot ON pot.potentialid = se.crmid
		WHERE se.deleted = 0 AND se.smownerid = $user_id AND (pot.sales_stage NOT IN ('".$app_strings['LBL_CLOSE_WON']."','".
		$app_strings['LBL_CLOSE_LOST']."') OR pot.sales_stage IS NULL)");
	$modrecords['Potentials']= $adb->query_result($potcountres,0,'count');

	// Calendar moudule
	$calcountres = $adb->query("SELECT count(*) as count FROM vtiger_crmentity se INNER JOIN vtiger_activity act ON act.activityid = se.crmid
		WHERE se.deleted = 0 AND se.smownerid = $user_id AND act.activitytype != 'Emails' AND
			((act.status!='Completed' AND act.status!='Deferred') OR act.status IS NULL)
			AND ((act.eventstatus!='Held' AND act.eventstatus!='Not Held') OR act.eventstatus IS NULL)");
	$modrecords['Calendar']= $adb->query_result($calcountres,0,'count');

	// Ignore the special module
	$nor_modules = array_diff($module_list, $spl_modules);
	// Prepare module string to use in SQL (check permission)
	$inmodulestr = '';
	foreach($nor_modules as $modulename) {
		if(isPermitted("$modulename","index",'') == 'yes') {
			if($inmodulestr != '') $inmodulestr .= ",'$modulename'";
			else $inmodulestr = "'$modulename'";
		}
	}

	// Get count for module that needs special conditions
	$query = "SELECT setype, count(setype) setype_count FROM vtiger_crmentity se WHERE
		se.deleted = 0 AND se.smownerid=$user_id AND se.setype in ($inmodulestr) GROUP BY se.setype";
	$queryres = $adb->query($query);
	while($resrow = $adb->fetch_array($queryres)) {
		$modrecords[$resrow['setype']] = $resrow['setype_count'];
	}

	// Get module custom filter info
	$cvidres = $adb->query("SELECT cvid,entitytype FROM vtiger_customview WHERE viewname='All' AND entitytype in ('".
		implode("','", array_keys($modrecords)). "')");

	$cvidinfo = Array();
	while($cvidrow = $adb->fetch_array($cvidres)) {
		$cvidinfo[$cvidrow['entitytype']] = $cvidrow['cvid'];
	}

	$name_val = '';
	$cnt_val = '';
	$target_val = '';
	$urlstring	= '';
	$cnt_table  = '<table border="0" cellpadding="3" cellspacing="1"><tbody><tr><th>Status</th><th>Total</th></tr>';
	$test_target_val='';

	$total_records= 0;
	foreach($module_list as $modulename) {
		if(isset($modrecords[$modulename])) {
			$modrec_count = $modrecords[$modulename];
			if($modrec_count > 0) {
				if($name_val != '') $name_val .= '::';
				$name_val .= $modulename;

				if($cnt_val != '') $cnt_val .= '::';
				$cnt_val .= $modrec_count;

				$modviewid = $cvidinfo[$modulename];
				$username = getFullNameFromArray('Users', $userinfo->column_fields);
				if($target_val!= '') $target_val.= '::';
				$target_val.= urlencode("index.php?module=$modulename&action=ListView&from_homepagedb=true&type=dbrd&query=true&owner=$username&viewname=$modviewid");
				if($test_target_val!='') $test_target_val.= 'K';
				$test_target_val.=urlencode("index.php?module=$modulename&action=ListView&from_homepagedb=true&type=dbrd&query=true&owner=$username&viewname=$modviewid");

				$urlstring .= 'K';
				$cnt_table .= "<tr><td>$modulename</td><td align='center'>$modrec_count</td></tr>";

				$total_records += $modrec_count;
			}
		}
	}
	$cnt_table .= '</tbody></table>';

	$graph_details[] = $name_val;
	$graph_details[] = $cnt_val;
	$graph_details[] = " $userinfo->user_name : $total_records ";
	$graph_details[] = $target_val;
	$graph_details[] = '';
	$graph_details[] = $urlstring;
	$graph_details[] = $cnt_table;
	$graph_details[] = $test_target_val;

	return $graph_details;
}

/* Function returns the values to render the graph for a particular type
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*/
function module_Chart($user_id,$date_start="2000-01-01",$end_date="2017-01-01",$query,$graph_for,$title,$added_qry="",$module="",$graph_type)
{
	global $adb,$current_user,$mod_strings, $default_charset, $days,$date_array,$period_type;

	if($added_qry!="")
		$query.=$added_qry;

	$result=$adb->query($query);

	$no_of_rows=$adb->num_rows($result);
	$mod_count_array=array();
	$search_str_array=array();
	$mod_name_array=array();
	$count_by_date[]=array();
	$mod_tot_cnt_array=array();

	$mod_name_val="";
	$mod_cnt_crtd_date="";
	$target_val="";
	$bar_target_val="";
	$test_target_val="";
	$urlstring = '';
	$mod_graph_date = '';
	$max_label_length = GlobalVariable::getVariable('Application_ListView_Max_Text_Length',40,$currentModule);
	if($no_of_rows!=0)
	{
		while($row = $adb->fetch_array($result))
		{
			if($graph_for == 'sostatus'||$graph_for == 'leadsource'||$graph_for == 'leadstatus'||$graph_for == 'industry'||$graph_for == 'productcategory'||$graph_for =='postatus'||$graph_for == 'invoicestatus'||$graph_for == 'ticketstatus'||$graph_for == 'priority'||$graph_for == 'category'||$graph_for == 'quotestage'||$graph_for == 'salesstage')
			{
				$mod_name= getTranslatedString($row[$graph_for]);
				$search_str = $row[$graph_for];
			}
			else
			{
				$mod_name= $row[$graph_for];
				$search_str = $row[$graph_for];
			}
			if($mod_name=="")
			{
				$mod_name=$mod_strings["Un Assigned"];
				$search_str = " ";
			}
			if (strlen($mod_name)>$max_label_length) $mod_name = substr($mod_name, 0, $max_label_length);
			$crtd_time= isset($row['createdtime']) ? $row['createdtime'] : date('Y-m-d H:i:s');
			$crtd_time_array=explode(" ",$crtd_time);
			$crtd_date=$crtd_time_array[0];
			if(!isset($mod_tot_cnt_array[$crtd_date]))
				$mod_tot_cnt_array[$crtd_date]=0;

			$mod_tot_cnt_array[$crtd_date]+=1;

			if (in_array($mod_name,$mod_name_array) == false) {
				array_push($mod_name_array,$mod_name);
			}
			if (in_array($search_str,$search_str_array) == false)
			{
				array_push($search_str_array,$search_str);
			}

			//Counting the number of values for a type of graph
			if($graph_for == "productname")
			{
				if($row['qtyinstock'] =='')
					$mod_count_array[$mod_name] = 1;
				else
					$mod_count_array[$mod_name]=$row['qtyinstock'];
			}
			else
			{
				if(!isset($mod_count_array[$mod_name]))
					$mod_count_array[$mod_name]=0;
				$mod_count_array[$mod_name]++;
			}

			//Counting the number of values for a type of graph for a particular date
			if(!isset($count_by_date[$mod_name][$crtd_date]))
				$count_by_date[$mod_name][$crtd_date]=0;

			$count_by_date[$mod_name][$crtd_date]+=1;
		}
		$mod_by_mod_cnt=count($mod_name_array);

		if($mod_by_mod_cnt!=0)
		{
			$url_string="";

			$mod_cnt_table = '<table border=0 cellspacing=1 cellpadding=3><tr><th>  '.getTranslatedString('LBL_STATUS').'  </th>';

			//Assigning the Header values to the table and giving the dates as graph format
			for($i=0; $i<$days; $i++)
			{
				$tdate=$date_array[$i];
				$values=Graph_n_table_format($period_type,$tdate);
				$graph_format=$values[0];
				$table_format=$values[1];
				$mod_cnt_table.= "<th>$table_format</th>";
			}
			$mod_cnt_table .= '<th>'.getTranslatedString('LBL_TOTAL').'</th></tr>';

			//For all type of the array
			for ($i=0;$i<count($mod_name_array); $i++)
			{
				$search_str = $search_str_array[$i];
				$mod_name=$mod_name_array[$i];
				$id_name = "";
				if($mod_name=="Un Assigned"){
					$mod_name=$mod_strings["Un Assigned"];
					$search_str = " ";
				}

				if($graph_for =="accountid")
				{
					$name_val_table=get_account_name($mod_name);
				}
				else
				{
					$name_val_table=$mod_name;
				}

				$mod_cnt_table .= "<tr><td>$name_val_table</td>";
				$mod_cnt_crtd_date="";
				//For all the days
				for($j=0;$j<$days;$j++)
				{
					$tdate=$date_array[$j];

					if (!isset($count_by_date[$mod_name][$tdate]))
					{
						$count_by_date[$mod_name][$tdate]="0";
					}
					$cnt_by_date=$count_by_date[$mod_name][$tdate];
					$mod_cnt_table .= "<td>$cnt_by_date </td>";

					if($i==0)
					{
						$values=Graph_n_table_format($period_type,$tdate);
						$graph_format=$values[0];
						$table_format=$values[1];


						//passing the created dates to graph
						if($mod_graph_date!="")
							$mod_graph_date="$mod_graph_date,$graph_format";
						else
							$mod_graph_date="$graph_format";

					}

					//passing the name count by date to graph
					if($mod_cnt_crtd_date!="")
						$mod_cnt_crtd_date.=",$cnt_by_date";
					else
						$mod_cnt_crtd_date="$cnt_by_date";

				}

				$mod_count_val=$mod_count_array[$mod_name];
				$tot_mod_cnt=array_sum($count_by_date[$mod_name]);
				$mod_cnt_table .= "<td align=center>$tot_mod_cnt</td></tr>";

				if($graph_for =="accountid")
				{
					$name_val=get_account_name($mod_name);
					if($name_val!="")
					{
						$mod_name=$name_val;
						$search_str=$name_val;
					}
				}
				if($graph_for =="smownerid")
				{
					$name_val=getOwnerName($mod_name);
					if($name_val!="")
					{
						$mod_name=$name_val;
						$search_str=$name_val;
					}
				}
				if($graph_for =="product_id" || $graph_for =="productid")
				{
					$query = "SELECT productname FROM vtiger_products WHERE productid=?";
					$result = $adb->pquery($query, array($mod_name));
					$name_val = $adb->query_result($result,0,"productname");
					if($name_val!="")
					{
						$mod_name=$name_val;
						$search_str=$name_val;
					}
				}
				if($graph_for =="purchaseorderid")
				{
					$query = "SELECT subject FROM vtiger_purchaseorder WHERE purchaseorderid=?";
					$result = $adb->pquery($query, array($mod_name));
					$name_val = $adb->query_result($result,0,"subject");
					$id_name = $mod_name;
					if($name_val!="")
					{
						$mod_name=$name_val;
						$search_str=$name_val;
					}
				}
				if($graph_for =="quoteid")
				{
					$query = "SELECT subject FROM vtiger_quotes WHERE quoteid=?";
					$result = $adb->pquery($query, array($mod_name));
					$name_val = $adb->query_result($result,0,"subject");
					$id_name = $mod_name;
					if($name_val!="")
					{
						$mod_name=$name_val;
						$search_str=$name_val;
					}
				}
				if($graph_for =="invoiceid")
				{
					$query = "SELECT subject FROM vtiger_invoice WHERE invoiceid=?";
					$result = $adb->pquery($query, array($mod_name));
					$name_val = $adb->query_result($result,0,"subject");
					$id_name = $mod_name;
					if($name_val!="")
					{
						$mod_name=$name_val;
						$search_str=$name_val;
					}
				}
				if($graph_for =="campaignid")
				{
					//this will return the list of the names of the campaign``:w for the y-axis
					$query = "SELECT campaignname FROM vtiger_campaign WHERE campaignid=?";
					$result = $adb->pquery($query, array($mod_name));
					$name_val = $adb->query_result($result,0,"campaignname");
					$id_name = $mod_name;
					if($name_val!="")
					{
						$mod_name=$name_val;
						$search_str=$name_val;
					}
				}
				if($graph_for =="parent_id" || $graph_for =="related_to")
				{
					$seType = getSalesEntityType($mod_name);
					if($seType == 'Contacts') {
						$query = "SELECT lastname, firstname FROM vtiger_contactdetails
							WHERE contactid=?";
						$result = $adb->pquery($query, array($mod_name));
						$name_val = $adb->query_result($result,0,"lastname");
						if($name_val!="") {
							if(getFieldVisibilityPermission('Contacts', $current_user->id,
									'firstname') == '0') {
								$first_name = $adb->query_result($result,0,"firstname");
								if($first_name != '') {
									$name_val .= " ".$first_name;
								}
							}
						}
					} else {
						$query = "SELECT accountname FROM vtiger_account WHERE accountid=?";
						$result = $adb->pquery($query, array($mod_name));
						$name_val = $adb->query_result($result,0,"accountname");
					}
					$mod_name=$name_val;
					$search_str=$name_val;
				}
				//Passing name to graph
				$mod_name = str_replace(":", "&#58;", $mod_name);
				if($mod_name_val!="") $mod_name_val.="::$mod_name";
				else $mod_name_val="$mod_name";


				//Passing count to graph
				if(!empty($mod_cnt_val)) $mod_cnt_val.="::$mod_count_val";
				else $mod_cnt_val="$mod_count_val";
				if($module!="")
				{
					//Check for Ticket Priority
					if(($graph_type=="ticketsbypriority"))
					{
						$graph_for="ticketpriorities";
					}

					//added to get valid url in dashbord for tickets by team
					if($graph_for == "smownerid"){
						$searchField = "assigned_user_id";
					} elseif($graph_for == 'category') {
						$searchField = 'ticketcategories';
					} elseif($graph_for == 'priority') {
						$searchField = 'ticketpriorities';
					} elseif($graph_for == "accountid") {
						$searchField = "account_id";
					} else{
						$searchField = $graph_for;
					}
					$cvid = getCvIdOfAll($module);
					if($module == "Home")
					{
						$cvid = getCvIdOfAll($mod_name);
						$link_val="index.php?module=".$mod_name."&action=ListView&from_homepagedb=true&type=dbrd&query=true&owner=".$current_user->user_name."&viewname=".$cvid;
					}
					else if($module == "Contacts" || ($module=="Products" && ($graph_for == "quoteid" || $graph_for == "invoiceid" || $graph_for == "purchaseorderid")))
						$link_val="index.php?module=".$module."&action=ListView&from_dashboard=true&type=dbrd&query=true&".$searchField."=".$id_name."&viewname=".$cvid;
					else {
						$esc_search_str = urlencode($search_str);
						//$esc_search_str = htmlentities($search_str, ENT_QUOTES, $default_charset);
						$link_val="index.php?module=".$module."&action=index&from_dashboard=true&search_text=".$esc_search_str."&search_field=".$searchField."&searchtype=BasicSearch&query=true&type=entchar&operator=e&viewname=".$cvid;
					}

					//Adding the links to the graph
					$link_val = str_replace(':', '&#58;', $link_val);
					if($i==0)
						$bar_target_val .=$link_val;
					else
						$bar_target_val .="::".$link_val;
				}
				//The data as per given date
				if($i==0)
					$urlstring .=$mod_cnt_crtd_date;
				else
					$urlstring .="K".$mod_cnt_crtd_date;

				if($i==0)
					$test_target_val.=$link_val;
				else
					$test_target_val.="K".$link_val;
			}
			$mod_cnt_table .="</tr><tr><td>".getTranslatedString('LBL_TOTAL').'</td>';
			//For all Days getting the table
			for($k=0; $k<$days;$k++)
			{
				$tdate=$date_array[$k];
				if(!isset($mod_tot_cnt_array[$tdate]))
					$mod_tot_cnt_array[$tdate]="0";
				$tot= $mod_tot_cnt_array[$tdate];
				if($period_type!="yday")
					$mod_cnt_table.="<td>$tot</td>";
			}
			if($graph_for == "productname")
			{
				$cnt_total=array_sum($mod_count_array);
			}
			else
			{
				$cnt_total=array_sum($mod_tot_cnt_array);
			}

			$mod_cnt_table.="<td align=\"center\">$cnt_total</td></tr></table>";
			$mod_cnt_table.="</table>";
			$title_of_graph="$title : $cnt_total";
			$bar_target_val=urlencode($bar_target_val);
			$test_target_val=urlencode($test_target_val);

			$Prod_mod_val=array($mod_name_val,$mod_cnt_val,$title_of_graph,$bar_target_val,$mod_graph_date,$urlstring,$mod_cnt_table,$test_target_val);
			return $Prod_mod_val;
		}
		else
		{
			$data=0;
		}
	}
	else
	{
		$data=0;
		return "<h3> The data is not available with the specified time period</h3>";
	}
	return $data;
}

function get_graph_by_type($graph_by,$graph_title,$module,$where,$query,$width=900,$height=900,$frompage='')
{
	global $user_id,$date_start,$end_date,$type,$mod_strings;
	$time = time();
	$html_imagename=$graph_by; //Html image name for the graph

	$graph_details=module_Chart($user_id,$date_start,$end_date,$query,$graph_by,$graph_title,$where,$module,$type);
	if($graph_details!=0)
	{
		$labels = ChartUtils::convertToArray($graph_details[0],true,true);
		$values = $graph_details[1];
		$graph_title = $graph_details[2];
		$target_values = ChartUtils::convertToArray($graph_details[3],false,true);
		$graph_date = $graph_details[4];
		$urlstring = $graph_details[5];
		$cnt_table = $graph_details[6];
		$test_target_val = $graph_details[7];

		if(isset($_REQUEST['display_view']) && $_REQUEST['display_view'] == 'MATRIX')
		{
			$width = 450;
			$height = 350;
		}

		$top=20;
		$right=20;
		$left=140;
		$bottom=120;
		if (isset($_REQUEST['Chart_Type'])) {
			switch ($_REQUEST['Chart_Type']) {
				case 'horizontalbarchart':
					$Chart_Type = 'horizontalBar';
					$Chart_position = 'top';
					break;
				case 'piechart':
					$Chart_Type = 'pie';
					$Chart_position = 'right';
					break;
				case 'verticalbarchart':
				default:
					$Chart_Type = 'bar';
					$Chart_position = 'top';
					break;
			}
		} else {
			$Chart_Type = 'bar';
			$Chart_position = 'top';
		}

		if($frompage != '')
		{
			return ChartUtils::getChartHTML($labels, $values, $graph_title, $target_values,$html_imagename, $width, $height, $left, $right, $top, $bottom, $Chart_Type, $Chart_position);
		}else
		{
			return ChartUtils::getChartHTML($labels, $values, $graph_title, $target_values,$html_imagename, $width, $height, $left, $right, $top, $bottom, $Chart_Type, $Chart_position, false);
		}
	}
	else
	{
		sleep(1);
		echo '<h3>'.$mod_strings['LBL_NO_DATA'].'</h3>';
	}
}

?>