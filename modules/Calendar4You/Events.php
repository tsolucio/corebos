<?php
/*********************************************************************************
* The content of this file is subject to the Calendar4You Free license.
* ("License"); You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
* Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
* All Rights Reserved.
********************************************************************************/
global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $current_user, $adb, $default_charset;

$app = $app_strings;
$mod = $mod_strings;

$Activities = array();
$record = '';

require_once('include/fields/DateTimeField.php');
require_once('modules/Calendar4You/Calendar4You.php');
require_once('modules/Calendar4You/CalendarUtils.php');
require_once('include/QueryGenerator/QueryGenerator.php');

$typeids = vtlib_purify($_REQUEST['typeids']);
$Type_Ids = explode(',',$typeids);

$user_view_type = vtlib_purify($_REQUEST['user_view_type']);
$save = vtlib_purify($_REQUEST['save']);
$full_calendar_view = vtlib_purify($_REQUEST['view']);
if (isset($_REQUEST['record']) && $_REQUEST['record'] != '') $record = $_REQUEST['record'];

if (!empty($_REQUEST['usersids'])) {
	$all_users = true;
	$Users_Ids = explode(',',$_REQUEST['usersids']);
} else {
	$all_users = false;
	if ($user_view_type != 'all')
		$Users_Ids = array($user_view_type);
	else{
		echo '[]';die();
	}
}

$Load_Event_Status = array();
$event_status = vtlib_purify($_REQUEST["event_status"]);
if ($event_status != "") { 
	$Load_Event_Status = explode(",",$event_status);
}

$Load_Task_Status = array();
$task_status = vtlib_purify($_REQUEST["task_status"]);
if ($task_status != "") {
	$Load_Task_Status = explode(",",$task_status);
}

$Calendar4You = new Calendar4You();
$Calendar4You->GetDefPermission($current_user->id);

if ($record == "" && $save != "") {
	$Calendar4You->SaveView($Type_Ids, $Users_Ids, $all_users, $Load_Event_Status, $Load_Task_Status, array());
}
$detailview_permissions = $Calendar4You->CheckPermissions("DETAIL");

require('user_privileges/user_privileges_'.$current_user->id.'.php');
require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

$ParentUsers = array();

$u_query = "select vtiger_user2role.userid as id from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like ?";
$u_params = array($current_user_parent_role_seq."::%");
$u_result = $adb->pquery($u_query,$u_params);

while($u_row = $adb->fetchByAssoc($u_result)) {
	$ParentUsers[] = $u_row["id"];
}

$view = convertFullCalendarView($full_calendar_view);

$calendar_tabid = getTabId("Calendar");
$events_tabid = getTabId("Events");

$Showed_Field = array();
$Event_Info = array();

if ($detailview_permissions) {
	$sql0 = "SELECT * FROM its4you_calendar4you_event_fields WHERE userid = ? AND view = ?";
	$result0 = $adb->pquery($sql0, array($current_user->id,$view));
	$num_rows0 = $adb->num_rows($result0);
	if ($num_rows0 > 0) {
		while($row0 = $adb->fetchByAssoc($result0)) {
			$sql01 = 'SELECT uitype, columnname, fieldlabel, vtiger_tab.name
				FROM vtiger_field
				INNER JOIN vtiger_tab on vtiger_tab.tabid=vtiger_field.tabid
				WHERE fieldid = ?';
			list($fname,$fid) = explode(':', $row0['fieldname']);
			$result01 = $adb->pquery($sql01, array($fid));
			if ($adb->num_rows($result01)==0) continue;
			$columnname = $adb->query_result($result01,0,"columnname");
			$fieldlabel = $adb->query_result($result01,0,"fieldlabel");
			$uitype = $adb->query_result($result01,0,"uitype");
			$modname = $adb->query_result($result01,0,'name');
			$Field_data = array('fieldid'=>$fid,'module'=>$modname,'fieldname'=>$row0['fieldname'], 'columnname'=>$columnname, 'fieldlabel' => $fieldlabel, 'uitype'=>$uitype);
			if ($row0['type'] == "1") {
				$Showed_Field[$row0['event']] = $Field_data;
			} else {
				$Event_Info[$row0['event']][] = $Field_data;
			}
		}
	}
}

if (isset($_REQUEST["start"]) && $_REQUEST["start"] != "") $start_time = $_REQUEST["start"]; else $start_time = time();
if (isset($_REQUEST["end"]) && $_REQUEST["end"] != "") $end_time = $_REQUEST["end"]; else $end_time = time();

$start_date = date("Y-m-d",$start_time);
$end_date = date("Y-m-d",$end_time);

$tasklabel = getAllModulesWithDateFields();
$timeModules = getAllModulesWithDateTimeFields();

$Event_Status = array();
if (count($Load_Event_Status) > 0) {
	foreach ($Load_Event_Status AS $sid) {
		$s_sql = "SELECT eventstatus FROM vtiger_eventstatus WHERE picklist_valueid = ?";
		$s_result = $adb->pquery($s_sql, array($sid));
		$eventstatus = $adb->query_result($s_result,0,"eventstatus");
		array_push($Event_Status, $eventstatus); 
		$eventstatus = html_entity_decode($eventstatus,ENT_QUOTES,$default_charset);
		array_push($Event_Status, $eventstatus);
	}
}

$Task_Status = array();
if (count($Load_Task_Status) > 0) {
	foreach ($Load_Task_Status AS $sid) {
		$s_sql = "SELECT taskstatus FROM vtiger_taskstatus WHERE picklist_valueid = ?";
		$s_result = $adb->pquery($s_sql, array($sid));
		$taskstatus = $adb->query_result($s_result,0,"taskstatus");
		array_push($Task_Status, $taskstatus);
		$taskstatus = html_entity_decode($taskstatus,ENT_QUOTES,$default_charset);
		array_push($Task_Status, $taskstatus);
	}
}
$modtab = array_flip($tasklabel);
foreach($Users_Ids AS $userid) {
	foreach($Type_Ids AS $activitytypeid) {
		$allDay = true;
		$list_array = array();
		$invites = false;
		if (is_numeric($activitytypeid)) {
			$sql1 = "SELECT activitytype FROM vtiger_activitytype WHERE activitytypeid = ?";
			$result1 = $adb->pquery($sql1, array($activitytypeid));
			$activitytype = $adb->query_result($result1,0,"activitytype");
			$activitytype = html_entity_decode($activitytype,ENT_QUOTES,$default_charset);
			$allDay = false;
		} elseif ($activitytypeid == "invite") {
			$activitytype = $activitytypeid;
			$invites = true;
			$allDay = false;
		} else {
			$activitytype = $activitytypeid;
		}
		if (in_array($activitytypeid,$tasklabel)) {
			require_once('modules/'.$activitytypeid.'/'.$activitytypeid.'.php');
			$Module_Status_Fields = getModuleStatusFields($activitytypeid);
			$modact = new $activitytypeid;
			$subject = $modact->list_link_field;
			$tablename = $modact->table_name;
			$queryGenerator = new QueryGenerator($activitytypeid, $current_user);
			$stfields = getModuleCalendarFields($activitytypeid);
			$queryFields = array('id',$subject,$stfields['start'],'assigned_user_id'); // we force the users module with assigned_user_id
			if ($stfields['start']!=$stfields['end']) $queryFields[] = $stfields['end'];
			if (!empty($stfields['stime'])) $queryFields[] = $stfields['stime'];
			if (!empty($stfields['etime'])) $queryFields[] = $stfields['etime'];
			if (isset($stfields['subject'])) {
				$descflds = explode(',', $stfields['subject']);
				foreach ($descflds as $dfld) {
					$queryFields[] = $dfld;
				}
			}
			$queryGenerator->setFields($queryFields);
			if ($record != "") {
				$queryGenerator->addCondition('id',$record,'e',$queryGenerator::$AND);
			} else {
				$dtflds = getDateFieldsOfModule($modtab[$activitytypeid]);
				$queryGenerator->startGroup();
				foreach ($dtflds as $field) {
					$queryGenerator->addCondition($field,array(0=>$start_date,1=>$end_date),'bw',$queryGenerator::$OR);
				}
				$queryGenerator->startGroup('OR');
				$queryGenerator->addCondition($stfields['start'],$start_date,'b');
				$queryGenerator->addCondition(empty($stfields['end']) ? $stfields['start'] : $stfields['end'],$end_date,'a',$queryGenerator::$AND);
				$queryGenerator->endGroup();
				$queryGenerator->endGroup();
				$queryGenerator->addCondition('assigned_user_id',getUserFullName($userid),'e',$queryGenerator::$AND);
				if (count($Event_Status) > 0) {
					$evuniq = array_diff(array('Held','Not Held','Planned'),array_unique($Event_Status));
					$encompas_group = false;
					foreach ($evuniq AS $evstat) {
						if (isset($Module_Status_Fields[$evstat])) {
							if (!$encompas_group) {
								$queryGenerator->startGroup('AND');
								$encompas_group = true;
								$queryGenerator->startGroup();
							} else {
								$queryGenerator->startGroup('OR');
							}
							foreach ($Module_Status_Fields[$evstat] as $condition) {
								$queryGenerator->addCondition($condition['field'],$condition['value'],$condition['operator'],$condition['glue']);
							}
							$queryGenerator->endGroup();
						}
					}
					if ($encompas_group) {
						$queryGenerator->endGroup();
					}
				}
			}
			$list_query = $queryGenerator->getQuery();
			$userNameSql = getSqlForNameInDisplayFormat(array('first_name' => 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
			$list_query = "SELECT distinct vtiger_crmentity.crmid, vtiger_groups.groupname, $userNameSql as user_name, ".$queryGenerator->getSelectClauseColumnSQL().$queryGenerator->getFromClause().$queryGenerator->getWhereClause();
			$list_array = array();
			if ($activitytypeid=='HelpDesk' and $modact->list_link_field == 'ticket_title') $subject = 'title';
		} else {
			$list_query = getCalendar4YouListQuery($userid, $invites);
			if ($record != "") {
				$list_query .= " AND vtiger_crmentity.crmid = '".$record."'";
			} else {
				$list_query .= " AND vtiger_activity.date_start <= '".$end_date."'";
				$list_query .= " AND vtiger_activity.due_date >= '".$start_date."'";
			}
			if (!$invites) {
				$list_query .= " AND vtiger_crmentity.smownerid = ?";
				$list_query .= " AND vtiger_activity.activitytype = ?";
				$list_array = array($userid,$activitytype);
			}
			if (count($Event_Status) > 0) {
				$list_query .= " AND (vtiger_activity.eventstatus NOT IN (" . generateQuestionMarks($Event_Status) . ") OR vtiger_activity.eventstatus IS NULL)";
				$list_array = array_merge($list_array, $Event_Status);
			}
			if (count($Task_Status) > 0) {
				$list_query .= " AND (vtiger_activity.status NOT IN (" . generateQuestionMarks($Task_Status) . ") OR vtiger_activity.status IS NULL)";
				$list_array = array_merge($list_array, $Task_Status);
			}
		}
		$list_result = $adb->pquery($list_query, $list_array);
		while($row = $adb->fetchByAssoc($list_result)) {
			$visibility = "private";
			$editable = false;
			$for_me = false;
			$add_more_info = false;
			$event = $activitytypeid;
			$into_title = isset($row["subject"]) ? $row["subject"] : (isset($row[$subject]) ? $row[$subject] : getTranslatedString('LBL_NONE'));
			if ($detailview_permissions) {
				if (($Calendar4You->view_all && $Calendar4You->edit_all) || ($userid == $current_user->id || $row["visibility"] == "Public" || in_array($userid,$ParentUsers) || $activitytypeid == "invite")) {
					if (isset($Showed_Field[$event]))
						$into_title = transferForAddIntoTitle(1,$row,$Showed_Field[$event]);
					$add_more_info = true;
					$visibility = "public";
				}
				if ($Calendar4You->edit_all || ($userid == $current_user->id || in_array($userid,$ParentUsers))) {
					$editable = true;
				}
			}
			if ($activitytypeid == "task" or in_array($activitytypeid,$tasklabel)){
				$activity_mode = "Task";
			} else {
				$activity_mode = "Events";
			}
			if ($record != "") {
				$Actions = array();
				if ($visibility == "public") {
					if(in_array($activitytypeid,$tasklabel))
					$Actions[] = "<a target='_new' href='index.php?action=DetailView&module=".$activitytypeid."&record=".$record."'>".$mod['LBL_DETAIL']."</a>";
					else
					$Actions[] = "<a target='_new' href='index.php?action=EventDetailView&module=Calendar4You&record=".$record."&activity_mode=$activity_mode&parenttab=Tools'>".$mod['LBL_DETAIL']."</a>";
				}
				if($Calendar4You->CheckPermissions("EDIT",$record)) {
					if(in_array($activitytypeid,$tasklabel))
						$Actions[] = "<a target='_new' href='index.php?action=EditView&module=".$activitytypeid."&record=".$record."'>".$app['LNK_EDIT']."</a>";
					else
						$Actions[] = "<a target='_new' href='index.php?action=EventEditView&module=Calendar4You&record=".$record."&activity_mode=$activity_mode&parenttab=Tools'>".$app['LNK_EDIT']."</a>";
				}
				if (vtlib_isModuleActive('Timecontrol') and !in_array($activitytypeid,$tasklabel)) {
					$Actions[] = "<a target='_newtc' href='index.php?action=EditView&module=Timecontrol&calendarrecord=$record&activity_mode=$activity_mode'>".getTranslatedString('LBL_TIME_TAKEN').'</a>';
				}
				if($Calendar4You->CheckPermissions("DELETE",$record)) {
					$Actions[] = "<a href='javascript:void(0)' onclick='EditView.record.value=".$record.";EditView.return_module.value=".'"Calendar4You"; EditView.return_action.value="index";  var confirmMsg = "'.getTranslatedString('NTC_DELETE_CONFIRMATION').'"; submitFormForActionWithConfirmation("EditView", "Delete", confirmMsg);\'>'.$app['LNK_DELETE'].'</a>';
				}
				$actions = implode(" | ",$Actions);
				if (isset($stfields['subject'])) {
					$descflds = explode(',', $stfields['subject']);
					$descvals = array();
					$descvals[] = html_entity_decode($into_title,ENT_QUOTES,$default_charset);
					foreach ($descflds as $dfld) {
						if (strpos($dfld, '.')) {
							$fld = substr($dfld, strpos($dfld, '.')+1);
						} else {
							$fld = $dfld;
						}
						// convert fieldname to columnname
						$rscol = $adb->pquery('select columnname from vtiger_field where tabid=? and fieldname=?',array(getTabid($activitytypeid),$fld));
						if ($rscol and $adb->num_rows($rscol)==1) {
							$fname = $adb->query_result($rscol, 0, 0);
						} else {
							$fname = $fld;
						}
						$descvals[] = html_entity_decode($row[$fname],ENT_QUOTES,$default_charset);
					}
					$into_title = implode(' -- ', $descvals);
				}
				$into_title = $app['LBL_ACTION'].": ".$actions."<hr>".nl2br($into_title);
			}
			$title = "<font style='font-size:12px'>".$into_title."</font>";
			if ($add_more_info) {
				if (count($Event_Info[$event]) > 0) {
					foreach($Event_Info[$event] AS $CD) {
						$title .= transferForAddIntoTitle(2,$row,$CD);
					}
				}
			}
			if(in_array($activitytypeid,$tasklabel)){
				$stfst = $row[$stfields['start']];
				$stfed = empty($stfields['end']) ? $stfst : $row[$stfields['end']];
				if ($stfields['start']=='birthday') {  // we bring it up to the current calendar year
					$stfst = date('Y',$start_time).'-'.substr($stfst, 6);
					$stfed = date('Y',$start_time).'-'.substr($stfed, 6);
				}
				if (in_array($activitytypeid,$timeModules)) {
					$stfst = $stfst . ' ' . $row[$stfields['stime']];
					$stfed = $stfed . ' ' . $row[$stfields['etime']];
					$allDay = false;
				}
				$convert_date_start = DateTimeField::convertToUserTimeZone($stfst);
				$user_date_start = $convert_date_start->format('Y-m-d H:i');
				$convert_due_date = DateTimeField::convertToUserTimeZone($stfed);
				$user_due_date = $convert_due_date->format('Y-m-d H:i');
			} else {
				$convert_date_start = DateTimeField::convertToUserTimeZone($row["date_start"]." ".$row["time_start"]);
				$user_date_start = $convert_date_start->format('Y-m-d H:i');
				$convert_due_date = DateTimeField::convertToUserTimeZone($row["due_date"]." ".$row["time_end"]);
				$user_due_date = $convert_due_date->format('Y-m-d H:i');
			}
			$Activities[] = array(
				'id' => $row['crmid'],
				'typeid' => $activitytypeid,
				'userid' => $userid,
				'visibility' => $visibility,
				'editable' => $editable,
				'activity_mode' => $activity_mode,
				'title' => $title,
				'start' => $user_date_start,
				'end' => $user_due_date,
				'allDay' => $allDay,
				'url' => "");
		}
	}
}
echo json_encode($Activities);
?>