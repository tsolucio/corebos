<?php
/*********************************************************************************
 * The content of this file is subject to the Calendar4You Free license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/

function getaddITSEventPopupTime($starttime, $endtime, $format) {
	if (empty($format)) {
		$format = '24';
	}
	$timearr = array();
	list($sthr,$stmin) = explode(':', $starttime);
	list($edhr,$edmin) = explode(':', $endtime);
	if ($format == 'am/pm') {
		$hr = $sthr+0;
		$timearr['startfmt'] = ($hr >= 12) ? 'pm' : 'am';
		if ($hr == 0) {
			$hr = 12;
		}
		$timearr['starthour'] = twoDigit(($hr>12)?($hr-12):$hr);
		$timearr['startmin']  = $stmin;

		$edhr = $edhr+0;
		$timearr['endfmt'] = ($edhr >= 12) ? 'pm' : 'am';
		if ($edhr == 0) {
			$edhr = 12;
		}
		$timearr['endhour'] = twoDigit(($edhr>12)?($edhr-12):$edhr);
		$timearr['endmin']  = $edmin;
		return $timearr;
	}
	if ($format == '24') {
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
	global $mod_strings, $theme;
	$count = 0;
	//To decide number of rows(weeks) in a month
	if ($cal['calendar']->month_array[$cal['calendar']->slices[35]]->start_time->month != $cal['calendar']->date_time->month) {
		$rows = 5;
	} else {
		$rows = 6;
	}
	$minical = '';
	$mt = substr('0' . $cal['calendar']->date_time->month, -2);
	$dy = substr('0' . $cal['calendar']->date_time->day, -2);
	$minical .= "<table class='mailClient ' bgcolor='white' border='0' cellpadding='2' cellspacing='0' width='98%'>
		<tr>
			<td class='calHdr'>&nbsp;</td>
			<td style='padding:5px' colspan='6' class='calHdr' align='center'>".get_previous_its_cal($cal).'&nbsp;';
			$minical .= "<a style='text-decoration: none;' href='javascript:changeCalendarMonthDate(\"".$cal['calendar']->date_time->year.'","'.$mt.'","'.$dy."\");'><b>"
				.display_date($cal['view'], $cal['calendar']->date_time).'</b></a>&nbsp;'.get_next_its_cal($cal).'</td>';
			//$minical .= "<a style='text-decoration: none;' href='index.php?module=Calendar&action=index&view=".$cal['view']."".$cal['calendar']->date_time->get_date_str()."&parenttab=".$category."'><b>".display_date($cal['view'],$cal['calendar']->date_time)."</b></a>&nbsp;".get_next_its_cal($cal)."</td>";
			$minical .= "<td class='calHdr' align='right'><a href='javascript:ghide(\"miniCal\");'><img src='"
				.vtiger_imageurl('close.gif', $theme). "' align='right' border='0'></a>
		</td></tr>";
	$minical .= "<tr class='hdrNameBg'>";
	//To display days in week
	$minical .= '<th width="12%">'.$mod_strings['LBL_WEEK'].'</th>';
	for ($i = 0; $i < 7; $i ++) {
		$weekday = $mod_strings['cal_weekdays_short'][$i];
		$minical .= '<th width="12%">'.$weekday.'</th>';
	}
	$minical .= '</tr>';
	$event_class = '';
	$class = '';
	for ($i = 0; $i < $rows; $i++) {
		$minical .= '<tr>';

		//calculate blank days for first week
		for ($j = 0; $j < 7; $j ++) {
			$cal['slice'] = $cal['calendar']->month_array[$cal['calendar']->slices[$count]];
			$class = dateCheck($cal['slice']->start_time->get_formatted_date());
			if ($j == 0) {
				$mt = substr('0' . $cal['slice']->start_time->month, -2);
				$dy = substr('0' . $cal['slice']->start_time->day, -2);
				$minical .= "<td style='text-align:center' ><a href='javascript:changeCalendarWeekDate(\"".$cal['slice']->start_time->year.'","'.$mt.'","'.$dy."\");'>"
					.$cal['slice']->start_time->week.'</td>';
				//index.php?module=Calendar&action=index&view=week".$cal['slice']->start_time->get_date_str()."&parenttab=".$category
			}

			//To differentiate day having events from other days
			if (count($cal['slice']->activities)!=0 && ($cal['slice']->start_time->get_formatted_date()==$cal['slice']->activities[0]->start_time->get_formatted_date())) {
				$event_class = 'class="eventDay"';
			} else {
				$event_class = '';
			}
			//To differentiate current day from other days
			if ($class != '') {
				$class = 'class="'.$class.'"';
			} else {
				$class = $event_class;
			}

			//To display month dates
			if ($cal['slice']->start_time->getMonth() == $cal['calendar']->date_time->getMonth()) {
				$minical .= "<td ".$class." style='text-align:center' >";
				$mt = substr('0' . $cal['slice']->start_time->month, -2);
				$dy = substr('0' . $cal['slice']->start_time->day, -2);
				$minical .= "<a href='javascript:changeCalendarDayDate(\"".$cal['slice']->start_time->year.'","'.$mt.'","'.$dy."\");'>";
				//$minical .= "<a href='index.php?module=Calendar&action=index&view=".$cal['slice']->getView()."".$cal['slice']->start_time->get_date_str()."&parenttab=".$category."'>BBBBBB";
				$minical .= $cal['slice']->start_time->get_Date()."</a></td>";
			} else {
				$minical .= "<td style='text-align:center' ></td>";
			}
			$count++;
		}
		$minical .= '</tr>';
	}
	$minical .= '</table>';
	echo $minical;
}

function get_previous_its_cal(& $cal) {
	global $theme;
	$link = "<a href='javascript:getITSMiniCal(\"view=".$cal['calendar']->view."".$cal['calendar']->get_datechange_info('prev')."\")'><img src= '"
		.vtiger_imageurl('small_left.gif', $theme)."' border='0' align='absmiddle' /></a>";
	return $link;
}

function get_next_its_cal(& $cal) {
	global $theme;
	$link = "<a href='javascript:getITSMiniCal(\"view=".$cal['calendar']->view."".$cal['calendar']->get_datechange_info('next')."\")' ><img src='"
		.vtiger_imageurl('small_right.gif', $theme)."' border='0' align='absmiddle' /></a>";
	return $link;
}

function getActTypeForCalendar($activitytypeid, $translate = true) {
	global $adb,$default_charset;
	$q = 'select activitytype from vtiger_activitytype where activitytypeid = ?';
	$Res = $adb->pquery($q, array($activitytypeid));
	if ($adb->num_rows($Res)>0) {
		$value = $adb->query_result($Res, 0, 'activitytype');
	} else {
		$q1 = 'select activitytype from vtiger_activitytype order by activitytypeid limit 1';
		$Res1 = $adb->pquery($q1, array());
		$value = $adb->query_result($Res1, 0, 'activitytype');
	}
	$value = html_entity_decode($value, ENT_QUOTES, $default_charset);
	if ($translate) {
		return getTranslatedString($value, 'Calendar');
	} else {
		return $value;
	}
}

function getActTypesForCalendar() {
	global $adb, $current_user;

	$ActTypes = $params = array();
	if (is_admin($current_user)) {
		$q = 'select activitytypeid, activitytype from vtiger_activitytype where activitytype!=?';
		$params = array('Emails');
	} else {
		$roleid=$current_user->roleid;
		$subrole = getRoleSubordinates($roleid);
		if (count($subrole)> 0) {
			$roleids = $subrole;
			$roleids[] = $roleid;
		} else {
			$roleids = $roleid;
		}

		$q = 'select activitytypeid, activitytype
			from vtiger_activitytype
			inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_activitytype.picklist_valueid
			where activitytype!=? and roleid in ('. generateQuestionMarks($roleids) .') and picklistid in (select picklistid from vtiger_picklist)
			order by sortid asc';
		$params = array_merge(array('Emails'), (is_array($roleids) ? $roleids : array($roleids)));
	}
	$Res = $adb->pquery($q, $params);
	$noofrows = $adb->num_rows($Res);
	$previousValue = '';
	for ($i = 0; $i < $noofrows; $i++) {
		$value = $adb->query_result($Res, $i, 'activitytype');
		if ($previousValue == $value) {
			continue;
		}
		$previousValue = $value;
		$id = $adb->query_result($Res, $i, 'activitytypeid');
		$ActTypes[$id] = $value;
	}

	return $ActTypes;
}

function getEColors($mode, $entity) {
	global $Calendar4You, $Event_Colors, $current_user, $adb;

	if (isset($Event_Colors[$mode][$entity]['bg']) && $Event_Colors[$mode][$entity]['bg'] != '') {
		$color_bg = $Event_Colors[$mode][$entity]['bg'];
	} else {
		if ($mode == 'type' && $entity == 'task') {
			$color_bg = '#00AAFF';
		} elseif ($mode == 'type' && $entity == 'invite') {
			$color_bg = '#F070FF';
		} elseif ($mode == 'type' && $entity == '1') {
			$color_bg = '#FFFB00';
		} elseif ($mode == 'type' && $entity == '2') {
			$color_bg = '#FF3700';
		} else {
			$color_bg = $Calendar4You->getRandomColorHex();
		}
		$sql1 = 'INSERT INTO its4you_calendar4you_colors (userid, mode, entity, type, color) VALUES (?,?,?,?,?)';
		$adb->pquery($sql1, array($current_user->id,$mode,$entity,'bg',$color_bg));
	}

	if (isset($Event_Colors[$mode][$entity]['text']) && $Event_Colors[$mode][$entity]['text'] != '') {
		$color_text = $Event_Colors[$mode][$entity]['text'];
	} else {
		$color_text = '#000000';
		$sql2 = 'INSERT INTO its4you_calendar4you_colors (userid, mode, entity, type, color) VALUES (?,?,?,?,?)';
		$adb->pquery($sql2, array($current_user->id,$mode,$entity,'text',$color_text));
	}

	return array('bg' => $color_bg,'text'=> $color_text);
}

function convertFullCalendarView($view) {
	switch ($view) {
		case 'month':
			$c_view = 'month';
			break;
		case 'agendaWeek':
			$c_view = 'week';
			break;
		case 'agendaDay':
			$c_view = 'day';
			break;
		default:
			$c_view = 'day';
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
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>".$app_strings['LBL_PERMISSION']."</span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>
		<a href='javascript:window.history.back();'>".$app_strings['LBL_GO_BACK']."</a><br></td>
		</tr>
		</tbody></table>
		</div>
		</td></tr></table>";
	die($output);
}

function getCalendar4YouListQuery($userid, $invites, $where = '', $type = '1') {
	global $log, $adb;
	$log->debug('Entering getCalendar4YouListQuery(' . $userid . ',' . $where . ') method ...');
	if ($userid != '') {
		require 'user_privileges/user_privileges_' . $userid . '.php';
		require 'user_privileges/sharing_privileges_' . $userid . '.php';
	}
	//$tab_id = getTabid('Calendar4You');
	//$userNameSql = getSqlForNameInDisplayFormat(array('first_name' => 'vtiger_users.first_name', 'last_name' =>'vtiger_users.last_name'), 'Users');

	$query = 'SELECT distinct vtiger_activity.activityid as act_id, vtiger_crmentity.*, vtiger_activity.*, vtiger_activitycf.*, vtiger_contactdetails.lastname,
		vtiger_contactdetails.firstname, vtiger_contactdetails.contactid, vtiger_activity.rel_id AS parent_id,its4you_googlesync4you_events.geventid,
		vtiger_activity_reminder.reminder_time
	FROM vtiger_activity
	LEFT JOIN vtiger_activitycf ON vtiger_activitycf.activityid = vtiger_activity.activityid
	LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_activity.cto_id
	LEFT OUTER JOIN vtiger_activity_reminder ON vtiger_activity_reminder.activity_id = vtiger_activity.activityid
	LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid
	LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
	LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
	LEFT JOIN vtiger_users vtiger_users2 ON vtiger_crmentity.modifiedby = vtiger_users2.id
	LEFT JOIN vtiger_groups vtiger_groups2 ON vtiger_crmentity.modifiedby = vtiger_groups2.groupid ';
	$tabid = getTabid('cbCalendar');
	$dependentFieldIDrs = $adb->pquery("SELECT fieldid FROM vtiger_field WHERE uitype='10' AND fieldname='rel_id' and tabid=?", array($tabid));
	$dependentFieldRelModsrs = $adb->pquery(
		'SELECT vtiger_entityname.*
			FROM vtiger_entityname
			INNER JOIN vtiger_fieldmodulerel ON modulename=relmodule
			WHERE vtiger_fieldmodulerel.fieldid = ? AND module=?',
		array($adb->query_result($dependentFieldIDrs, 0, 0), 'cbCalendar')
	);
	while ($join = $adb->fetch_array($dependentFieldRelModsrs)) {
		$query .= ' LEFT OUTER JOIN ' . $join['tablename'] . ' ON vtiger_activity.rel_id = ' . $join['tablename'] . '.' . $join['entityidfield'];
	}
	$query .= ' ';

	if (isset($_REQUEST['from_homepage']) && ($_REQUEST['from_homepage'] == 'upcoming_activities' || $_REQUEST['from_homepage'] == 'pending_activities')) {
		$query.='LEFT OUTER JOIN vtiger_recurringevents ON vtiger_recurringevents.activityid=vtiger_activity.activityid ';
	}

	//google cal sync
	$query.= "LEFT JOIN its4you_googlesync4you_events ON its4you_googlesync4you_events.crmid = vtiger_activity.activityid
		AND its4you_googlesync4you_events.userid = '".$userid."' ";

	if ($invites && $userid != '') {
		$query.= "INNER JOIN vtiger_invitees ON vtiger_invitees.activityid = vtiger_activity.activityid AND vtiger_invitees.inviteeid = '".$userid."' ";
	}

	//$query .= getCalendar4YouNonAdminAccessControlQuery($userid);
	$query.=" WHERE vtiger_crmentity.deleted = 0 AND activitytype != 'Emails' " . $where;

	$query = listQueryNonAdminChange($query, 'Calendar');

	$log->debug('Exiting getListQuery method ...');
	return $query;
}

function getCalendar4YouNonAdminAccessControlQuery($userid, $scope = '') {
	require 'user_privileges/user_privileges_'.$userid.'.php';
	require 'user_privileges/sharing_privileges_'.$userid.'.php';
	$module = 'Calendar';
	$query = ' ';
	$tabId = getTabid($module);
	if ($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2]
			== 1 && $defaultOrgSharingPermission[$tabId] == 3) {
		$tableName = 'vt_tmp_u'.$userid.'_t'.$tabId;
		$sharingRuleInfoVariable = $module.'_share_read_permission';
		$sharingRuleInfo = $$sharingRuleInfoVariable;
		$sharedTabId = null;
		setupCalendar4YouTemporaryTable(
			$tableName,
			$sharedTabId,
			$userid,
			$current_user_parent_role_seq,
			$current_user_groups
		);
		$query = " INNER JOIN $tableName $tableName$scope ON ($tableName$scope.id = vtiger_crmentity$scope.smownerid and $tableName$scope.shared=0) ";
		$sharedIds = getCalendar4YouSharedCalendarId($userid);
		if (!empty($sharedIds)) {
			$query .= "or ($tableName$scope.id = vtiger_crmentity$scope.smownerid AND $tableName$scope.shared=1 and vtiger_activity.visibility = 'Public') ";
		}
	}
	return $query;
}

function getCalendar4YouSharedCalendarId($sharedid) {
	global $adb;
	$result = $adb->pquery('SELECT userid from vtiger_sharedcalendar where sharedid=?', array($sharedid));
	if ($adb->num_rows($result)!=0) {
		for ($j=0; $j<$adb->num_rows($result); $j++) {
			$userid[] = $adb->query_result($result, $j, 'userid');
		}
		$shared_ids = implode(',', $userid);
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
	$query = "create temporary table IF NOT EXISTS $tableName(id int(11) primary key, shared int(1) default 0) ignore ".$query;

	$result = $adb->pquery($query, array());
	if (is_object($result)) {
		$query="create temporary table IF NOT EXISTS $tableName(id int(11) primary key, shared int(1) default 0) replace select 1, userid as id
			from vtiger_sharedcalendar where sharedid = $userid";
		$result = $adb->pquery($query, array());
		if (is_object($result)) {
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
	$query = "(SELECT $userid as id)
		UNION
		(SELECT vtiger_user2role.userid AS userid
			FROM vtiger_user2role
			INNER JOIN vtiger_users ON vtiger_users.id=vtiger_user2role.userid
			INNER JOIN vtiger_role ON vtiger_role.roleid=vtiger_user2role.roleid
			WHERE vtiger_role.parentrole like '$parentRole::%')";
	if (count($userGroups) > 0) {
		$query .= ' UNION (SELECT groupid FROM vtiger_groups where groupid in (' . implode(',', $userGroups) . '))';
	}
	return $query;
}

/**
 *
 * @param <type> $module
 * @param <type> $user
 */
function getCalendar4YouNonAdminModuleAccessQuery($module, $userid) {
	require 'user_privileges/sharing_privileges_' . $userid . '.php';
	$tabId = getTabid($module);
	$sharingRuleInfoVariable = $module . '_share_read_permission';
	$sharingRuleInfo = $$sharingRuleInfoVariable;
	$sharedTabId = null;
	$query = '';
	if (!empty($sharingRuleInfo) && (count($sharingRuleInfo['ROLE']) > 0 || count($sharingRuleInfo['GROUP']) > 0)) {
		$query = " (SELECT shareduserid
				FROM vtiger_tmp_read_user_sharing_per
				WHERE userid=$userid AND tabid=$tabId)
			UNION
			(SELECT vtiger_tmp_read_group_sharing_per.sharedgroupid
				FROM vtiger_tmp_read_group_sharing_per
				WHERE userid=$userid AND tabid=$tabId)";
	}
	return $query;
}

function transferForAddIntoTitle($type, $row, $CD) {
	global $current_user, $adb;
	list($CD['fieldname'],$void) = explode(':', $CD['fieldname']);
	$Col_Field = array();
	if ($CD['uitype'] == '66' && !empty($row['parent_id'])) {
		$Col_Field = array($CD['fieldname']=> $row['parent_id']);
	} elseif (!empty($row[$CD['columnname']])) {
		$Col_Field = array($CD['fieldname']=> $row[$CD['columnname']]);
	}

	if ($CD['fieldname'] == 'duration_hours') {
		$Col_Field['duration_minutes'] = $row['duration_minutes'];
	}

	if ($CD['fieldname'] == 'contact_id') {
		$Col_Field['contact_id'] = getAssignedContactsForEvent($row['crmid']);
		$CD['uitype'] = '1';
	}
	if ($CD['module']=='Calendar' || $CD['module']=='Events') {
		$Cal_Data = getDetailViewOutputHtml($CD['uitype'], $CD['fieldname'], $CD['fieldlabel'], $Col_Field, '2', getTabid('cbCalendar'), 'cbCalendar');
		if ($CD['fieldname'] == 'subject' && strpos($Cal_Data[1], 'a href') === false) {
			$Cal_Data[1] = '<a target=_blank href="index.php?module=cbCalendar&action=DetailView&record=' . $row['crmid'] . '">' . $Cal_Data[1] . '</a>';
		}
		if (strpos($Cal_Data[1], 'vtlib_metainfo')===false) {
			$Cal_Data[1] .= "<span type='vtlib_metainfo' vtrecordid='".$row['crmid']."' vtfieldname='".$CD['fieldname']
				."' vtmodule='cbCalendar' style='display:none;'></span>";
		}
		$trmodule = 'Calendar';
	} else {
		$queryGenerator = new QueryGenerator($CD['module'], $current_user);
		$queryGenerator->setFields(array($CD['fieldname']));
		$frs = $adb->pquery(
			'select fieldname
				from vtiger_field
				inner join vtiger_fieldmodulerel on vtiger_field.fieldid = vtiger_fieldmodulerel.fieldid
				where relmodule=? and module=?',
			array($CD['module'],'cbCalendar')
		);
		$relfield = $adb->query_result($frs, 0, 0);
		$queryGenerator->addCondition('id', $row[$relfield], 'e', $queryGenerator::$AND);
		$rec_query = $queryGenerator->getQuery();
		$recinfo = $adb->pquery($rec_query, array());
		$Cal_Data = array();
		$Cal_Data[0] = getTranslatedString($CD['fieldlabel'], $CD['module']);
		$Cal_Data[1] = $adb->query_result($recinfo, 0, $CD['columnname']);
		$trmodule = $CD['module'];
	}

	if ($CD['uitype'] == '10') {
		$value = getEntityName(getSalesEntityType($Cal_Data[1]), $Cal_Data[1]);
		$value = $value[$Cal_Data[1]];
	} elseif ($CD['uitype'] == '15') {
		$value = getTranslatedString($Cal_Data[1], $trmodule);
	} else {
		$value = $Cal_Data[1];
	}

	if ($type == '1') {
		return vtlib_purify($Cal_Data[1]);
	} else { //		return '<br><b>'.$Cal_Data[0].'</b>: '.$value;
		return '<table><tr><td><b>'.$Cal_Data[0].':</b></td>
			<td onmouseover="vtlib_listview.trigger(\'cell.onmouseover\', this)" onmouseout="vtlib_listview.trigger(\'cell.onmouseout\', this)">'
			.vtlib_purify($value).'</td></tr></table>';
	}
}

function getEventActivityMode($id) {
	global $adb;
	$result = $adb->pquery('select activitytype from vtiger_activity where activityid=?', array($id));
	$actType = $adb->query_result($result, 0, 'activitytype');
	if ($actType == 'Task') {
		$activity_mode = $actType;
	} elseif ($actType != 'Emails') {
		$activity_mode = 'Events';
	}
	return $activity_mode;
}

function getITSActFieldCombo($fieldname, $tablename, $from_module = '', $follow_activitytype = false) {
	global $adb, $current_user, $default_charset;
	require 'user_privileges/user_privileges_'.$current_user->id.'.php';
	$combo = '';
	$js_fn = '';
	$def = '';

	if ($from_module != '') {
		$from_tab_id = getTabid($from_module);
		$sql_d = "SELECT defaultvalue FROM vtiger_field WHERE uitype = '15' AND fieldname = ? AND tabid = ?";
		$Res_D = $adb->pquery($sql_d, array($fieldname,$from_tab_id));
		$noofrows_d = $adb->num_rows($Res_D);

		if ($noofrows_d == 1) {
			$def = $adb->query_result($Res_D, 0, 'defaultvalue');
		}
	}

	if ($fieldname == 'eventstatus') {
		$js_fn = 'onChange = "getSelectedStatus();"';
	}
	if ($follow_activitytype) {
		$combo .= '<select name="follow_'.$fieldname.'" id="follow_'.$fieldname.'" class=small '.$js_fn.'>';
	} else {
		$combo .= '<select name="'.$fieldname.'" id="'.$fieldname.'" class=small '.$js_fn.'>';
	}
	if ($is_admin) {
		$q = 'select * from '.$tablename;
	} else {
		$roleid=$current_user->roleid;
		$subrole = getRoleSubordinates($roleid);
		if (count($subrole)> 0) {
			$roleids = $subrole;
			$roleids[] = $roleid;
		} else {
			$roleids = $roleid;
		}
		if (count($roleids) > 1) {
			$q="select $fieldname
				from $tablename
				inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = $tablename.picklist_valueid
				where roleid in (\"". implode($roleids, "\",\"") ."\") and picklistid in (select picklistid from $tablename)
				order by sortid asc";
		} else {
			$q="select $fieldname
				from $tablename
				inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = $tablename.picklist_valueid
				where roleid ='".$roleid."' and picklistid in (select picklistid from $tablename)
				order by sortid asc";
		}
	}
	$Res = $adb->query($q);
	$noofrows = $adb->num_rows($Res);
	$previousValue = '';
	for ($i = 0; $i < $noofrows; $i++) {
		$value = $adb->query_result($Res, $i, $fieldname);
		if ($previousValue == $value) {
			continue;
		}
		$previousValue = $value;
		$value = html_entity_decode($value, ENT_QUOTES, $default_charset);
		$label = getTranslatedString($value, 'Calendar');
		if ($value == $def) {
			$selected = ' selected';
		} else {
			$selected = '';
		}
		$combo .= '<option value="'.$value.'"'.$selected.'>'.$label.'</option>';
	}

	$combo .= '</select>';
	return $combo;
}

function getAssignedContactsForEvent($actid) {
	global $adb;
	$contacts = '';
	$Contacts = array();
	$query = 'SELECT vtiger_contactdetails.lastname, vtiger_contactdetails.firstname, vtiger_contactdetails.contactid
		FROM vtiger_activity
		INNER JOIN vtiger_cntactivityrel ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid
		INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid
		WHERE vtiger_activity.activityid = ?';
	$Result = $adb->pquery($query, array($actid));
	$num_rows = $adb->num_rows($Result);
	if ($num_rows > 0) {
		while ($row = $adb->fetchByAssoc($Result)) {
			$contact_name = trim($row['firstname'].' '.$row['lastname']);
			$Contacts[] = "<a href='index.php?module=Contacts&action=DetailView&record=".$row['contactid']."'>".$contact_name.'</a>';
		}
		$contacts = implode(', ', $Contacts);
	}
	return $contacts;
}

function getAllModulesWithDateFields() {
	global $adb, $current_user;
	if (is_admin($current_user)) {
		$sqlmods = 'SELECT distinct cbfld.tabid,vtiger_tab.name
			FROM vtiger_field as cbfld
			INNER JOIN vtiger_tab on cbfld.tabid = vtiger_tab.tabid
			WHERE vtiger_tab.presence=0 and vtiger_tab.isentitytype=1 and uitype=5';
		$params = array();
	} else {
		$sqlmods = '';
		$profileList = getCurrentUserProfileList();
		$sql = 'select * from vtiger_profile2globalpermissions where globalactionid=1 and profileid in ('.generateQuestionMarks($profileList).');';
		$result = $adb->pquery($sql, array($profileList));
		if ($result && $adb->num_rows($result)>0) {
			for ($i=0; $i<$adb->num_rows($result); $i++) {
				$permission = $adb->query_result($result, $i, 'globalactionpermission');
				if ($permission != 1 || $permission != '1') { // can see everything
					$sqlmods = 'SELECT distinct cbfld.tabid,vtiger_tab.name
						FROM vtiger_field as cbfld
						INNER JOIN vtiger_tab on cbfld.tabid = vtiger_tab.tabid
						WHERE vtiger_tab.presence=0 and vtiger_tab.isentitytype=1 and uitype=5';
					break;
				}
			}
			$params = array();
		}
		if ($sqlmods=='') {
			$sqlmods = 'SELECT distinct cbfld.tabid,vtiger_tab.name
				FROM vtiger_field as cbfld
				INNER JOIN vtiger_tab on cbfld.tabid = vtiger_tab.tabid
				INNER JOIN vtiger_profile2tab on vtiger_profile2tab.tabid = vtiger_tab.tabid
				WHERE vtiger_tab.presence=0 and vtiger_tab.isentitytype=1 and uitype=5 and vtiger_profile2tab.profileid in ('
					.generateQuestionMarks($profileList).') and vtiger_profile2tab.permissions=0';
			$params = array($profileList);
		}
	}
	$sqlmods .= " and vtiger_tab.name not in ('cbCalendar','Calendar','Events')";
	$rsmwd = $adb->pquery($sqlmods, $params);
	$modswithdates = array();
	while ($mod = $adb->fetch_array($rsmwd)) {
		$modswithdates[$mod['tabid']] = $mod['name'];
	}
	uasort($modswithdates, function ($a, $b) {
		return (strtolower(getTranslatedString($a, $a)) < strtolower(getTranslatedString($b, $b))) ? -1 : 1;
	});
	return $modswithdates;
}

function getAllModulesWithDateTimeFields() {
	global $adb, $current_user;
	if (is_admin($current_user)) {
		$sqlmods = 'SELECT distinct cbfld.tabid,vtiger_tab.name
			FROM vtiger_field as cbfld
			INNER JOIN vtiger_tab on cbfld.tabid = vtiger_tab.tabid
			WHERE vtiger_tab.presence=0 and vtiger_tab.isentitytype=1 and uitype=14 and
				exists (select 1 from vtiger_field where vtiger_field.tabid = cbfld.tabid and uitype=5)';
		$params = array();
	} else {
		$sqlmods = '';
		$profileList = getCurrentUserProfileList();
		$sql = 'select * from vtiger_profile2globalpermissions where globalactionid=1 and profileid in ('.generateQuestionMarks($profileList).');';
		$result = $adb->pquery($sql, array($profileList));
		if ($result && $adb->num_rows($result)>0) {
			for ($i=0; $i<$adb->num_rows($result); $i++) {
				$permission = $adb->query_result($result, $i, 'globalactionpermission');
				if ($permission != 1 || $permission != '1') { // can see everything
					$sqlmods = 'SELECT distinct cbfld.tabid,vtiger_tab.name
						FROM vtiger_field as cbfld
						INNER JOIN vtiger_tab on cbfld.tabid = vtiger_tab.tabid
						WHERE vtiger_tab.presence=0 and vtiger_tab.isentitytype=1 and uitype=14 and
							exists (select 1 from vtiger_field where vtiger_field.tabid = cbfld.tabid and uitype=5)';
					break;
				}
			}
			$params = array();
		}
		if ($sqlmods=='') {
			$sqlmods = 'SELECT distinct cbfld.tabid,vtiger_tab.name
				FROM vtiger_field as cbfld
				INNER JOIN vtiger_tab on cbfld.tabid = vtiger_tab.tabid
				INNER JOIN vtiger_profile2tab on vtiger_profile2tab.tabid = vtiger_tab.tabid
				WHERE vtiger_tab.presence=0 and vtiger_tab.isentitytype=1 and uitype=14
					and vtiger_profile2tab.profileid in ('.generateQuestionMarks($profileList).') and vtiger_profile2tab.permissions=0 and
					exists (select 1 from vtiger_field where vtiger_field.tabid = cbfld.tabid and uitype=5)';
			$params = array($profileList);
		}
	}
	$sqlmods .= " and vtiger_tab.name not in ('cbCalendar','Calendar','Events')";
	$rsmwd = $adb->pquery($sqlmods, $params);
	$modswithdt = array();
	while ($mod = $adb->fetch_array($rsmwd)) {
		$modswithdt[$mod['tabid']] = $mod['name'];
	}
	uasort($modswithdt, function ($a, $b) {
		return (strtolower(getTranslatedString($a, $a)) < strtolower(getTranslatedString($b, $b))) ? -1 : 1;
	});
	return $modswithdt;
}

function getDateFieldsOfModule($tabid) {
	global $adb;
	$rsmwd = $adb->query("SELECT distinct fieldname FROM vtiger_field as cbfld WHERE tabid = $tabid and uitype=5");
	$datefields = array();
	while ($fld = $adb->fetch_array($rsmwd)) {
		$datefields[] = $fld['fieldname'];
	}
	return $datefields;
}

function getTimeFieldsOfModule($tabid) {
	global $adb;
	$rsmwd = $adb->query("SELECT distinct fieldname FROM vtiger_field as cbfld WHERE tabid = $tabid and uitype=14");
	$datefields = array();
	while ($fld = $adb->fetch_array($rsmwd)) {
		$datefields[] = $fld['fieldname'];
	}
	return $datefields;
}

function getDateAndTimeFieldsOfModule($tabid) {
	global $adb;
	$rsmwd = $adb->query("SELECT distinct fieldname FROM vtiger_field as cbfld WHERE tabid = $tabid and (uitype=14 or uitype=5)");
	$datefields = array();
	while ($fld = $adb->fetch_array($rsmwd)) {
		$datefields[] = $fld['fieldname'];
	}
	return $datefields;
}

function getModuleCalendarFields($module) {
	global $adb, $current_user;
	$rscalflds = $adb->pquery(
		'select * from its4you_calendar_modulefields where module=? and (userid=? or userid=1) order by userid desc',
		array($module, $current_user->id)
	);
	if ($rscalflds && $adb->num_rows($rscalflds)>0) {
		$calflds = $adb->fetch_row($rscalflds);
		$Module_StartEnd_Fields = array(
			'start'   => $calflds['start_field'],
			'end'     => $calflds['end_field'],
			'stime'   => $calflds['start_time'],
			'etime'   => $calflds['end_time'],
			'subject' => $calflds['subject_fields'],
			'color' => $calflds['color'],
		);
	} else {
		// it isn't registered > we look for custom fields
		$tid = getTabid($module);
		$dtflds = getDateFieldsOfModule($tid);
		if (count($dtflds)>0) {
			$tmflds = getTimeFieldsOfModule($tid);
			$Module_StartEnd_Fields = array(
				'start'   => $dtflds[0],
				'end'     => isset($dtflds[1]) ? $dtflds[1] : '',
				'stime'   => isset($tmflds[0]) ? $tmflds[0] : '',
				'etime'   => isset($tmflds[1]) ? $tmflds[1] : '',
				'subject' => '',
				'color' => '',
			);
		} else {
			$Module_StartEnd_Fields = array();
		}
	}
	return $Module_StartEnd_Fields;
}

function getModuleStatusFields($module) {
	global $adb;
	$rscalflds = $adb->pquery('select * from its4you_calendar_modulestatus where module=? order by status,glue', array($module));
	if ($rscalflds && $adb->num_rows($rscalflds)>0) {
		$currentstatus = '';
		while ($calflds = $adb->fetch_row($rscalflds)) {
			if ($currentstatus != $calflds['status']) {
				$currentstatus = $calflds['status'];
				$Module_Status_Fields[$currentstatus] = array();
			}
			$Module_Status_Fields[$currentstatus][] = array(
				'field'    => $calflds['field'],
				'value'    => $calflds['value'],
				'operator' => $calflds['operator'],
				'glue'     => $calflds['glue'],
			);
		}
	} else {
		$Module_Status_Fields = array();
	}
	return $Module_Status_Fields;
}

function Calendar_getReferenceFieldColumnsList($module, $sort = true) {
	global $current_user;
	$handler = vtws_getModuleHandlerFromName($module, $current_user);
	$meta = $handler->getMeta();
	$reffields = $meta->getReferenceFieldDetails();
	$ret_module_list = array();
	foreach ($reffields as $mods) {
		foreach ($mods as $mod) {
			if (!vtlib_isEntityModule($mod)) {
				continue; // reference to a module without fields
			}
			if (isset($ret_module_list[$mod])) {
				continue; // we already have this one
			}
			$Fields_Array = array();
			$module_handler = vtws_getModuleHandlerFromName($mod, $current_user);
			$module_meta = $module_handler->getMeta();
			$mflds = $module_meta->getModuleFields();
			foreach ($mflds as $finfo) {
				$fieldid = $finfo->getFieldId();
				$fieldlabel = getTranslatedString($finfo->getFieldLabelKey(), $mod);
				$field_data = array();
				$field_data['fieldid'] = $fieldid;
				$field_data['fieldname'] = $finfo->getFieldName();
				$field_data['fieldlabel'] = $fieldlabel;
				$field_data['module'] = $mod;
				$Fields_Array[$fieldid] = $field_data;
				unset($field_data);
			}
			if ($sort) {
				uasort($Fields_Array, function ($a, $b) {
					return (strtolower($a['fieldlabel']) < strtolower($b['fieldlabel'])) ? -1 : 1;
				});
			}
			$ret_module_list[$mod] = $Fields_Array;
			unset($Fields_Array);
		}
	}
	if ($sort) {
		uksort($ret_module_list, function ($a, $b) {
			return (strtolower(getTranslatedString($a, $a)) < strtolower(getTranslatedString($b, $b))) ? -1 : 1;
		});
	}
	return $ret_module_list;
}
?>
