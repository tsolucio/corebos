<?php
/*********************************************************************************
 * The content of this file is subject to the Calendar4You Free license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/
 
function getaddITSEventPopupTime($starttime,$endtime,$format) {
	$timearr = Array();
	list($sthr,$stmin) = explode(":",$starttime);
	list($edhr,$edmin)  = explode(":",$endtime);
	if($format == 'am/pm') {
		$hr = $sthr+0;
		$timearr['startfmt'] = ($hr >= 12) ? "pm" : "am";
		if($hr == 0) $hr = 12;
		$timearr['starthour'] = twoDigit(($hr>12)?($hr-12):$hr);
		$timearr['startmin']  = $stmin;

		$edhr = $edhr+0;
		$timearr['endfmt'] = ($edhr >= 12) ? "pm" : "am";
		if($edhr == 0) $edhr = 12;
		$timearr['endhour'] = twoDigit(($edhr>12)?($edhr-12):$edhr);
		$timearr['endmin']    = $edmin;
		return $timearr;
	}
	if($format == '24')	{
		$timearr['starthour'] = twoDigit($sthr);
		$timearr['startmin']  = $stmin;
		$timearr['startfmt']  = '';
		$timearr['endhour']   = twoDigit($edhr);
		$timearr['endmin']    = $edmin;
		$timearr['endfmt']    = '';
		return $timearr;
	}
}

/**
 * Function creates HTML to display small(mini) Calendar 
 * @param array   $cal    - collection of objects and strings
 */
function get_its_mini_calendar(& $cal) {
	global $current_user,$adb,$mod_strings,$theme;
	$category = getParentTab();
	$count = 0;
	//To decide number of rows(weeks) in a month
	if ($cal['calendar']->month_array[$cal['calendar']->slices[35]]->start_time->month != $cal['calendar']->date_time->month) {
		$rows = 5;
	} else {
		$rows = 6;
	}
	$minical = "";
	$minical .= "<table class='mailClient ' bgcolor='white' border='0' cellpadding='2' cellspacing='0' width='98%'>
				<tr>
					<td class='calHdr'>&nbsp;</td>
					<td style='padding:5px' colspan='6' class='calHdr' align='center'>".get_previous_its_cal($cal)."&nbsp;";
					$minical .= "<a style='text-decoration: none;' href='javascript:changeCalendarMonthDate(".$cal['calendar']->date_time->year.",".$cal['calendar']->date_time->month.",".$cal['calendar']->date_time->day.");'><b>".display_date($cal['view'],$cal['calendar']->date_time)."</b></a>&nbsp;".get_next_its_cal($cal)."</td>";
                    //$minical .= "<a style='text-decoration: none;' href='index.php?module=Calendar&action=index&view=".$cal['view']."".$cal['calendar']->date_time->get_date_str()."&parenttab=".$category."'><b>".display_date($cal['view'],$cal['calendar']->date_time)."</b></a>&nbsp;".get_next_its_cal($cal)."</td>";
					$minical .= "<td class='calHdr' align='right'><a href='javascript:ghide(\"miniCal\");'><img src='". vtiger_imageurl('close.gif', $theme). "' align='right' border='0'></a>
				</td></tr>";
	$minical .= "<tr class='hdrNameBg'>";
	//To display days in week 
	$minical .= '<th width="12%">'.$mod_strings['LBL_WEEK'].'</th>';
	for ($i = 0; $i < 7; $i ++){
		$weekday = $mod_strings['cal_weekdays_short'][$i];
		$minical .= '<th width="12%">'.$weekday.'</th>';
	}
	$minical .= "</tr>";	
	$event_class = '';
	$class = '';
	for ($i = 0; $i < $rows; $i++){
		$minical .= "<tr>";

		//calculate blank days for first week
		for ($j = 0; $j < 7; $j ++){
			$cal['slice'] = $cal['calendar']->month_array[$cal['calendar']->slices[$count]];
			$class = dateCheck($cal['slice']->start_time->get_formatted_date());
			if($j == 0){
				$minical .= "<td style='text-align:center' ><a href='javascript:changeCalendarWeekDate(".$cal['slice']->start_time->year.",".$cal['slice']->start_time->month.",".$cal['slice']->start_time->day.");'>".$cal['slice']->start_time->week."</td>";
                //index.php?module=Calendar&action=index&view=week".$cal['slice']->start_time->get_date_str()."&parenttab=".$category
			}
			
			//To differentiate day having events from other days
			if(count($cal['slice']->activities) != 0 && ($cal['slice']->start_time->get_formatted_date() == $cal['slice']->activities[0]->start_time->get_formatted_date())){
				$event_class = 'class="eventDay"';
			}else{
				$event_class = '';
			}
			//To differentiate current day from other days
			if($class != '' ){
				$class = 'class="'.$class.'"';
			}else{
				$class = $event_class;
			}
			
			//To display month dates
			if ($cal['slice']->start_time->getMonth() == $cal['calendar']->date_time->getMonth()){
				$minical .= "<td ".$class." style='text-align:center' >";
			
                $minical .= "<a href='javascript:changeCalendarDayDate(".$cal['slice']->start_time->year.",".$cal['slice']->start_time->month.",".$cal['slice']->start_time->day.");'>";
                //$minical .= "<a href='index.php?module=Calendar&action=index&view=".$cal['slice']->getView()."".$cal['slice']->start_time->get_date_str()."&parenttab=".$category."'>BBBBBB";
				$minical .= $cal['slice']->start_time->get_Date()."</a></td>";
			}else{
				$minical .= "<td style='text-align:center' ></td>";
			}
			$count++;
		}
		$minical .= '</tr>';
	}
	$minical .= "</table>";
	echo $minical;
}

function get_previous_its_cal(& $cal) {
	global $mod_strings,$theme;
	$category = getParentTab();
	
    $link = "<a href='javascript:getITSMiniCal(\"view=".$cal['calendar']->view."".$cal['calendar']->get_datechange_info('prev')."&parenttab=".$category."\")'><img src= '". vtiger_imageurl('small_left.gif', $theme)."' border='0' align='absmiddle' /></a>";

	return $link;
}

function get_next_its_cal(& $cal) {
	global $mod_strings,$theme;
	$category = getParentTab();
	
    $link = "<a href='javascript:getITSMiniCal(\"view=".$cal['calendar']->view."".$cal['calendar']->get_datechange_info('next')."&parenttab=".$category."\")'  ><img src='". vtiger_imageurl('small_right.gif', $theme)."' border='0' align='absmiddle' /></a>";
	return $link;
}

function getActTypeForCalendar($activitytypeid, $translate = true) {
	global $adb,$default_charset;
	
	$q = "select * from vtiger_activitytype where activitytypeid = ?";
	$Res = $adb->pquery($q,array($activitytypeid));
	$value = $adb->query_result($Res,0,"activitytype");
    $value = html_entity_decode($value,ENT_QUOTES,$default_charset);
    
    if ($translate) 
        return getTranslatedString($value,'Calendar'); 
    else  
        return $value;
}

function getActTypesForCalendar() {
	global $adb,$mod_strings,$current_user;
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	
    $ActTypes = array();
    
	if($is_admin)
		$q = "select * from vtiger_activitytype";
	else {
		$roleid=$current_user->roleid;
		$subrole = getRoleSubordinates($roleid);
		if(count($subrole)> 0) {
			$roleids = $subrole;
			array_push($roleids, $roleid);
		} else {	
			$roleids = $roleid;
		}

        $q = "select distinct activitytypeid, activitytype from vtiger_activitytype inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_activitytype.picklist_valueid where roleid";

		if (count($roleids) > 1) {
			$q .= " in (\"". implode($roleids,"\",\"") ."\") and picklistid in (select picklistid from vtiger_activitytype) order by sortid asc";
		} else {
			$q .= " ='".$roleid."' and picklistid in (select picklistid from vtiger_activitytype) order by sortid asc";
		}
	}
	$Res = $adb->query($q);
	$noofrows = $adb->num_rows($Res);

	for($i = 0; $i < $noofrows; $i++) {
	    $id = $adb->query_result($Res,$i,"activitytypeid");
        $value = $adb->query_result($Res,$i,"activitytype");
      
    	$ActTypes[$id] = $value;
	}

	return $ActTypes;
}

function getEColors($mode,$entity) {
    global $Calendar4You, $Event_Colors, $current_user, $adb; 

    if (isset($Event_Colors[$mode][$entity]["bg"]) && $Event_Colors[$mode][$entity]["bg"] != "") {
        $color_bg = $Event_Colors[$mode][$entity]["bg"];
    } else {
        if ($mode == "type" && $entity == "task")
            $color_bg = "#00AAFF";
        elseif ($mode == "type" && $entity == "invite")
            $color_bg = "#F070FF";
        elseif ($mode == "type" && $entity == "1")
            $color_bg = "#FFFB00";
        elseif ($mode == "type" && $entity == "2")
            $color_bg = "#FF3700";  
        else
            $color_bg = $Calendar4You->getRandomColorHex();
    
        $sql1 = "INSERT INTO its4you_calendar4you_colors (userid, mode, entity, type, color) VALUES (?,?,?,?,?)";
        $adb->pquery($sql1, array($current_user->id,$mode,$entity,"bg",$color_bg));
    }
        
    if (isset($Event_Colors[$mode][$entity]["text"]) && $Event_Colors[$mode][$entity]["text"] != "") {
        $color_text = $Event_Colors[$mode][$entity]["text"];
    } else {
        $color_text = "#000000";
        
        $sql2 = "INSERT INTO its4you_calendar4you_colors (userid, mode, entity, type, color) VALUES (?,?,?,?,?)";
        $adb->pquery($sql2, array($current_user->id,$mode,$entity,"text",$color_text));
    }

    return array("bg" => $color_bg,"text"=> $color_text);
}

function convertFullCalendarView($view) {
    switch($view) {
        case "month": $c_view = 'month'; break;
        case "agendaWeek": $c_view = 'week'; break;
        case "agendaDay": $c_view = 'day'; break;
        default: $c_view = 'day';
    }
    
    return $c_view;
}

function NOPermissionDiv() {      
    global $theme, $app_strings;
    
    $output = "<link rel='stylesheet' type='text/css' href='themes/$theme/style.css'>";	
	$output .= "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
	$output .= "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>

		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme) . "' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>".$app_strings["LBL_PERMISSION"]."</span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>			   	
		<a href='javascript:window.history.back();'>".$app_strings["LBL_GO_BACK"]."</a><br></td>
		</tr>
		</tbody></table> 
		</div>";
	$output .= "</td></tr></table>";
  die($output);
}

function getCalendar4YouListQuery($userid, $invites, $where = '', $type='1') {
	global $log;
    global $current_user;
	$log->debug("Entering getCalendar4YouListQuery(" . $userid . "," . $where . ") method ...");
    if ($userid != "") {
        require('user_privileges/user_privileges_' . $userid . '.php');
	   require('user_privileges/sharing_privileges_' . $userid . '.php');
    }
	//$tab_id = getTabid("Calendar4You");
	$userNameSql = getSqlForNameInDisplayFormat(array('first_name' => 'vtiger_users.first_name', 'last_name' =>
				'vtiger_users.last_name'), 'Users');

	$query = "SELECT distinct vtiger_activity.activityid as act_id, vtiger_crmentity.*, vtiger_activity.*, vtiger_activitycf.*, ";
	
    if ($type == '1') $query .= "vtiger_contactdetails.lastname, vtiger_contactdetails.firstname, vtiger_contactdetails.contactid, vtiger_account.accountid, vtiger_account.accountname, ";
    
	$query .= "vtiger_seactivityrel.crmid AS parent_id,
    its4you_googlesync4you_events.geventid,
    vtiger_activity_reminder.reminder_time  
	FROM vtiger_activity
	LEFT JOIN vtiger_activitycf
		ON vtiger_activitycf.activityid = vtiger_activity.activityid
	LEFT JOIN vtiger_cntactivityrel
		ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid
	LEFT JOIN vtiger_contactdetails
		ON vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid
	LEFT JOIN vtiger_seactivityrel
		ON vtiger_seactivityrel.activityid = vtiger_activity.activityid
	LEFT OUTER JOIN vtiger_activity_reminder
		ON vtiger_activity_reminder.activity_id = vtiger_activity.activityid
	LEFT JOIN vtiger_crmentity
		ON vtiger_crmentity.crmid = vtiger_activity.activityid
	LEFT JOIN vtiger_users
		ON vtiger_users.id = vtiger_crmentity.smownerid
	LEFT JOIN vtiger_groups
		ON vtiger_groups.groupid = vtiger_crmentity.smownerid
	LEFT JOIN vtiger_users vtiger_users2
		ON vtiger_crmentity.modifiedby = vtiger_users2.id
	LEFT JOIN vtiger_groups vtiger_groups2
		ON vtiger_crmentity.modifiedby = vtiger_groups2.groupid
	LEFT OUTER JOIN vtiger_account
		ON vtiger_account.accountid = vtiger_contactdetails.accountid
	LEFT OUTER JOIN vtiger_leaddetails
       		ON vtiger_leaddetails.leadid = vtiger_seactivityrel.crmid
	LEFT OUTER JOIN vtiger_account vtiger_account2
        	ON vtiger_account2.accountid = vtiger_seactivityrel.crmid
	LEFT OUTER JOIN vtiger_potential
       		ON vtiger_potential.potentialid = vtiger_seactivityrel.crmid
	LEFT OUTER JOIN vtiger_troubletickets
       		ON vtiger_troubletickets.ticketid = vtiger_seactivityrel.crmid
	LEFT OUTER JOIN vtiger_salesorder
		ON vtiger_salesorder.salesorderid = vtiger_seactivityrel.crmid
	LEFT OUTER JOIN vtiger_purchaseorder
		ON vtiger_purchaseorder.purchaseorderid = vtiger_seactivityrel.crmid
	LEFT OUTER JOIN vtiger_quotes
		ON vtiger_quotes.quoteid = vtiger_seactivityrel.crmid
	LEFT OUTER JOIN vtiger_invoice
                ON vtiger_invoice.invoiceid = vtiger_seactivityrel.crmid
	LEFT OUTER JOIN vtiger_campaign
	ON vtiger_campaign.campaignid = vtiger_seactivityrel.crmid ";

	//added to fix #5135
	if (isset($_REQUEST['from_homepage']) && ($_REQUEST['from_homepage'] ==
			"upcoming_activities" || $_REQUEST['from_homepage'] == "pending_activities")) {
		$query.="LEFT OUTER JOIN vtiger_recurringevents
	             ON vtiger_recurringevents.activityid=vtiger_activity.activityid ";
	}
	//end

    //google cal sync
    $query.= "LEFT JOIN its4you_googlesync4you_events 
    ON its4you_googlesync4you_events.crmid = vtiger_activity.activityid AND its4you_googlesync4you_events.userid = '".$userid."' "; 

    if($invites && $userid != "") $query.= "INNER JOIN vtiger_invitees ON vtiger_invitees.activityid = vtiger_activity.activityid AND vtiger_invitees.inviteeid = '".$userid."' ";

	//$query .= getCalendar4YouNonAdminAccessControlQuery($userid);
	$query.=" WHERE vtiger_crmentity.deleted = 0 AND activitytype != 'Emails' " . $where;

	$query = listQueryNonAdminChange($query, "Calendar");

	$log->debug("Exiting getListQuery method ...");
	return $query;
}

function getCalendar4YouNonAdminAccessControlQuery($userid,$scope='') {
        
        require('user_privileges/user_privileges_'.$userid.'.php');
		require('user_privileges/sharing_privileges_'.$userid.'.php');
        
        $module = "Calendar";
        $query = ' ';
		$tabId = getTabid($module);
		if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2]
				== 1 && $defaultOrgSharingPermission[$tabId] == 3) {
			$tableName = 'vt_tmp_u'.$userid.'_t'.$tabId;
			$sharingRuleInfoVariable = $module.'_share_read_permission';
			$sharingRuleInfo = $$sharingRuleInfoVariable;
			$sharedTabId = null;
			setupCalendar4YouTemporaryTable($tableName, $sharedTabId, $user,
					$current_user_parent_role_seq, $current_user_groups);
			$query = " INNER JOIN $tableName $tableName$scope ON ($tableName$scope.id = ".
					"vtiger_crmentity$scope.smownerid and $tableName$scope.shared=0) ";
			$sharedIds = getCalendar4YouSharedCalendarId($userid);
			if(!empty($sharedIds)){
				$query .= "or ($tableName$scope.id = vtiger_crmentity$scope.smownerid AND ".
					"$tableName$scope.shared=1 and vtiger_activity.visibility = 'Public') ";
			}
		}
		return $query;
}

function getCalendar4YouSharedCalendarId($sharedid) {
	global $adb;
	$query = "SELECT * from vtiger_sharedcalendar where sharedid=?";
	$result = $adb->pquery($query, array($sharedid));
	if($adb->num_rows($result)!=0)
	{
		for($j=0;$j<$adb->num_rows($result);$j++)
			$userid[] = $adb->query_result($result,$j,'userid');
		$shared_ids = implode (",",$userid);
	}
	return $shared_ids;
}
 
function setupCalendar4YouTemporaryTable($tableName, $tabId, $userid, $parentRole, $userGroups) {
	global $adb;
    
    $module = null;
	if (!empty($tabId)) {
		$module = getTabname($tabId);
	}
	$query = getCalendar4YouNonAdminAccessQuery($module, $userid, $parentRole, $userGroups);
	$query = "create temporary table IF NOT EXISTS $tableName(id int(11) primary key, shared ".
		"int(1) default 0) ignore ".$query;

	$result = $adb->pquery($query, array());
	if(is_object($result)) {
		$query = "create temporary table IF NOT EXISTS $tableName(id int(11) primary key, shared ".
		"int(1) default 0) replace select 1, userid as id from vtiger_sharedcalendar where ".
		"sharedid = $userid";
		$result = $adb->pquery($query, array());
		if(is_object($result)) {
			return true;
		}
	}
	return false;
}

function getCalendar4YouNonAdminAccessQuery($module, $userid, $parentRole, $userGroups) {
	$query = getCalendar4YouNonAdminUserAccessQuery($userid, $parentRole, $userGroups);
	if (!empty($module)) {
		$moduleAccessQuery = getCalendar4YouNonAdminModuleAccessQuery($module, $userid);
		if (!empty($moduleAccessQuery)) {
			$query .= " UNION $moduleAccessQuery";
		}
	}
	return $query;
}

/**
 *
 * @param <type> $user
 * @param <type> $parentRole
 * @param <type> $userGroups
 */
function getCalendar4YouNonAdminUserAccessQuery($user, $parentRole, $userGroups) {
	$query = "(SELECT $userid as id) UNION (SELECT vtiger_user2role.userid AS userid FROM " .
			"vtiger_user2role INNER JOIN vtiger_users ON vtiger_users.id=vtiger_user2role.userid " .
			"INNER JOIN vtiger_role ON vtiger_role.roleid=vtiger_user2role.roleid WHERE " .
			"vtiger_role.parentrole like '$parentRole::%')";
	if (count($userGroups) > 0) {
		$query .= " UNION (SELECT groupid FROM vtiger_groups where" .
				" groupid in (" . implode(",", $userGroups) . "))";
	}
	return $query;
}

/**
 *
 * @param <type> $module
 * @param <type> $user
 */
function getCalendar4YouNonAdminModuleAccessQuery($module, $userid) {
	require('user_privileges/sharing_privileges_' . $userid . '.php');
	$tabId = getTabid($module);
	$sharingRuleInfoVariable = $module . '_share_read_permission';
	$sharingRuleInfo = $$sharingRuleInfoVariable;
	$sharedTabId = null;
	$query = '';
	if (!empty($sharingRuleInfo) && (count($sharingRuleInfo['ROLE']) > 0 ||
			count($sharingRuleInfo['GROUP']) > 0)) {
		$query = " (SELECT shareduserid FROM vtiger_tmp_read_user_sharing_per " .
				"WHERE userid=$user->id AND tabid=$tabId) UNION (SELECT " .
				"vtiger_tmp_read_group_sharing_per.sharedgroupid FROM " .
				"vtiger_tmp_read_group_sharing_per WHERE userid=$userid AND tabid=$tabId)";
	}
	return $query;
}

function transferForAddIntoTitle($type, $row, $CD) {
    if ($CD["uitype"] == "66") 
        $Col_Field = array($CD["fieldname"]=> $row["parent_id"]);
    else
        $Col_Field = array($CD["fieldname"]=> $row[$CD["columnname"]]);
    
    if ($CD["fieldname"] == "duration_hours") 
        $Col_Field["duration_minutes"] = $row["duration_minutes"];
    
    if ($CD["fieldname"] == "contact_id") {
        $Col_Field["contact_id"] = getAssignedContactsForEvent($row["crmid"]); 
        $CD["uitype"] = "1";   
    }    
    $Cal_Data = getDetailViewOutputHtml($CD["uitype"], $CD["fieldname"], $CD["fieldlabel"], $Col_Field, "2", $calendar_tabid, "Calendar");
    
    if ($CD["uitype"] == "15")
        $value = getTranslatedString($Cal_Data[1],'Calendar');
    else
        $value = $Cal_Data[1];
       
    if ($type == "1")
        return $Cal_Data[1];
    else
        return "<br><b>".$Cal_Data[0]."</b>: ".$value;
}

function getEventActivityMode($id) {
	global $adb;

	$query = "select activitytype from vtiger_activity where activityid=?";
	$result = $adb->pquery($query, array($id));
	$actType = $adb->query_result($result,0,'activitytype');
	
	if( $actType == 'Task')	{
		$activity_mode = $actType;	
	} elseif($actType != 'Emails') {
		$activity_mode = 'Events';
	}
	return $activity_mode;
}

function getITSActFieldCombo($fieldname,$tablename,$from_module = '',$follow_activitytype = false) {
	global $adb, $mod_strings,$current_user,$default_charset;
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	$combo = '';
	$js_fn = '';
	$def = '';

    if ($from_module != '') {
        $from_tab_id = getTabid($from_module);
        
        $sql_d = "SELECT defaultvalue FROM vtiger_field WHERE uitype = '15' AND fieldname = ? AND tabid = ?";
        $Res_D = $adb->pquery($sql_d,array($fieldname,$from_tab_id));
        $noofrows_d = $adb->num_rows($Res_D);

        if ($noofrows_d == 1) {
            $def = $adb->query_result($Res_D,0,"defaultvalue");  
        }
    }
    
    
    if($fieldname == 'eventstatus')
		$js_fn = 'onChange = "getSelectedStatus();"';
	if($follow_activitytype)
		$combo .= '<select name="follow_'.$fieldname.'" id="follow_'.$fieldname.'" class=small '.$js_fn.'>';
	else
		$combo .= '<select name="'.$fieldname.'" id="'.$fieldname.'" class=small '.$js_fn.'>';
	if($is_admin)
		$q = "select * from ".$tablename;
	else {
		$roleid=$current_user->roleid;
		$subrole = getRoleSubordinates($roleid);
		if(count($subrole)> 0) {
			$roleids = $subrole;
			array_push($roleids, $roleid);
		} else {	
			$roleids = $roleid;
		}

		if (count($roleids) > 1) {
			$q="select distinct $fieldname from  $tablename inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = $tablename.picklist_valueid where roleid in (\"". implode($roleids,"\",\"") ."\") and picklistid in (select picklistid from $tablename) order by sortid asc";
		} else {
			$q="select distinct $fieldname from $tablename inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = $tablename.picklist_valueid where roleid ='".$roleid."' and picklistid in (select picklistid from $tablename) order by sortid asc";
		}
	}
	$Res = $adb->query($q);
	$noofrows = $adb->num_rows($Res);

	for($i = 0; $i < $noofrows; $i++) {
		$value = $adb->query_result($Res,$i,$fieldname);
        $value = html_entity_decode($value,ENT_QUOTES,$default_charset);
        $label = getTranslatedString($value,'Calendar');
        
        if ($value == $def) $selected = " selected"; else $selected = "";
        
		$combo .= '<option value="'.$value.'"'.$selected.'>'.$label.'</option>';
	}

	$combo .= '</select>';
	return $combo;
}

function getAssignedContactsForEvent($actid) {
    global $adb;
    
    $contacts = "";
    $Contacts = array();
    
    $query = "SELECT vtiger_contactdetails.lastname, vtiger_contactdetails.firstname, vtiger_contactdetails.contactid
              FROM vtiger_activity
              INNER JOIN vtiger_cntactivityrel
         	     ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid
              INNER JOIN vtiger_contactdetails
        	     ON vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid
        	  WHERE vtiger_activity.activityid = ?";
    $Result = $adb->pquery($query,array($actid));
	$num_rows = $adb->num_rows($Result);
    
    if ($num_rows > 0) {
        while($row = $adb->fetchByAssoc($Result)) {
        	
            $contact_name = trim($row['firstname']." ".$row['lastname']);
           
            $Contacts[] = "<a href='index.php?module=Contacts&action=DetailView&record=".$row['contactid']."'>".$contact_name."</a>"; 
        }
        
        $contacts = implode(", ",$Contacts); 
    }
    
    return $contacts;
}
?> 