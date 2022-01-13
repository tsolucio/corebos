<?php
/*********************************************************************************
 * The content of this file is subject to the Calendar4You Free license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/
include_once 'include/fields/metainformation.php';
require_once 'data/CRMEntity.php';
include_once 'modules/cbCalendar/CalendarCommon.php';

function getaddITSEventPopupTime($starttime, $endtime, $format) {
	if (empty($format)) {
		$format = '24';
	}
	$timearr = array();
	list($sthr,$stmin) = explode(':', $starttime);
	list($edhr,$edmin) = explode(':', $endtime);
	if ($format == '12' || $format == 'am/pm') {
		$hr = $sthr+0;
		$timearr['startfmt'] = ($hr >= 12) ? 'pm' : 'am';
		if ($hr == 0) {
			$hr = 12;
		}
		$timearr['starthour'] = twoDigit(($hr>12) ? ($hr-12) : $hr);
		$timearr['startmin']  = $stmin;

		$edhr = $edhr+0;
		$timearr['endfmt'] = ($edhr >= 12) ? 'pm' : 'am';
		if ($edhr == 0) {
			$edhr = 12;
		}
		$timearr['endhour'] = twoDigit(($edhr>12) ? ($edhr-12) : $edhr);
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
function get_its_mini_calendar(&$cal) {
	global $mod_strings;
	$count = 0;
	//To decide number of rows(weeks) in a month
	if ($cal['calendar']->month_array[$cal['calendar']->slices[35]]->start_time->month != $cal['calendar']->date_time->month) {
		$rows = 5;
	} else {
		$rows = 6;
	}
	$mt = substr('0' . $cal['calendar']->date_time->month, -2);
	$dy = substr('0' . $cal['calendar']->date_time->day, -2);
	$minical = '<div class="slds-grid slds-badge_lightest">
		<div class="slds-col slds-size_1-of-6">&nbsp;</div>
		<div class="slds-col slds-size_1-of-6 slds-align_absolute-center">'.get_previous_its_cal($cal).'</div>';
	$minical .= "<div class='slds-col slds-size_2-of-6 slds-align_absolute-center'>
		<a style='text-decoration:none;' href='javascript:void(0);' onclick='return changeCalendarMonthDate(\"".$cal['calendar']->date_time->year.'","'.$mt.'","'.$dy."\");'>"
		.'<b>'.display_date($cal['view'], $cal['calendar']->date_time).'</b></a></div>';
	$minical .=  '<div class="slds-col slds-size_1-of-6 slds-align_absolute-center">'.get_next_its_cal($cal).'</div>';
	$minical .= '<div class="slds-col slds-size_1-of-6" align="right">
		<button class="slds-button slds-button_icon slds-button_icon-border-filled" aria-haspopup="true"
			onClick="ghide(\'miniCal\');" title="'.getTranslatedString('LBL_CLOSE').'">
			<svg class="slds-button__icon" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
			</svg>
			<span class="slds-assistive-text">'.getTranslatedString('LBL_CLOSE').'</span>
		</button></div></div>';
	$minical .= '<table class="slds-table slds-table_cell-buffer slds-table_bordered slds-p-around_xx-small"><thead><tr class="slds-line-height_reset">';
	//To display days in week
	$minical .= '<th width="12%" scope="col">'.$mod_strings['LBL_WEEK'].'</th>';
	for ($i = 0; $i < 7; $i ++) {
		$weekday = $mod_strings['cal_weekdays_short'][$i];
		$minical .= '<th width="12%" scope="col">'.$weekday.'</th>';
	}
	$minical .= '</tr></thead><tbody>';
	$start_date = date('Y-m-01');
	$end_date = date('Y-m-t');
	$acts = getEventList($cal, $start_date, $end_date);
	$events = array();
	foreach ($acts[0] as $act) {
		list($d, $t) = explode(' ', $act['starttime']);
		$events[] = $d;
	}
	$event_class = '';
	$class = '';
	for ($i = 0; $i < $rows; $i++) {
		$minical .= '<tr class="slds-hint-parent">';

		//calculate blank days for first week
		for ($j = 0; $j < 7; $j ++) {
			$cal['slice'] = $cal['calendar']->month_array[$cal['calendar']->slices[$count]];
			$class = dateCheck($cal['slice']->start_time->get_formatted_date());
			if ($j == 0) {
				$mt = substr('0' . $cal['slice']->start_time->month, -2);
				$dy = substr('0' . $cal['slice']->start_time->day, -2);
				$minical .= "<td scope='row' style='text-align:center'><a href='javascript:void(0);' onclick='return changeCalendarWeekDate(\"".$cal['slice']->start_time->year.'","'.$mt.'","'.$dy."\");'>"
					.$cal['slice']->start_time->week.'</td>';
			}

			//To differentiate day having events from other days
			if (!empty($acts) && (in_array($cal['slice']->start_time->get_formatted_date(), $events))) {
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
				$minical .= "<a href='javascript:void(0);' onClick='return changeCalendarDayDate(\"".$cal['slice']->start_time->year.'","'.$mt.'","'.$dy."\");'>";
				$minical .= $cal['slice']->start_time->get_Date()."</a></a>";
			} else {
				$minical .= "<td style='text-align:center'></td>";
			}
			$count++;
		}
		$minical .= '</tr>';
	}
	$minical .= '</tbody></table>';
	echo $minical;
}

function get_previous_its_cal(&$cal) {
	return '<button class="slds-button slds-button_icon slds-button_icon-border-filled" aria-haspopup="true"
		onClick="getITSMiniCal(\'view='.$cal['calendar']->view.$cal['calendar']->get_datechange_info('prev').'\');" title="'.getTranslatedString('LNK_LIST_PREVIOUS').'">
		<svg class="slds-button__icon" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#left"></use>
		</svg>
		<span class="slds-assistive-text">'.getTranslatedString('LNK_LIST_PREVIOUS').'</span>
	</button>';
}

function get_next_its_cal(&$cal) {
	return '<button class="slds-button slds-button_icon slds-button_icon-border-filled" aria-haspopup="true"
		onClick="getITSMiniCal(\'view='.$cal['calendar']->view.$cal['calendar']->get_datechange_info('next').'\');" title="'.getTranslatedString('LNK_LIST_NEXT').'">
		<svg class="slds-button__icon" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#right"></use>
		</svg>
		<span class="slds-assistive-text">'.getTranslatedString('LNK_LIST_NEXT').'</span>
	</button>';
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
		return getTranslatedString($value, 'cbCalendar');
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
	$log->debug('> getCalendar4YouListQuery '.$userid.','.$where);
	$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Calendar4You');

	$query = 'SELECT distinct vtiger_activity.activityid as act_id, vtiger_crmentity.*, vtiger_activity.*, vtiger_activitycf.*, vtiger_contactdetails.lastname,
		vtiger_contactdetails.firstname, vtiger_contactdetails.contactid, vtiger_activity.rel_id AS parent_id,its4you_googlesync4you_events.geventid,
		vtiger_activity_reminder.reminder_time
	FROM vtiger_activity
	LEFT JOIN vtiger_activitycf ON vtiger_activitycf.activityid = vtiger_activity.activityid
	LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_activity.cto_id
	LEFT OUTER JOIN vtiger_activity_reminder ON vtiger_activity_reminder.activity_id = vtiger_activity.activityid
	LEFT JOIN '.$crmEntityTable.' ON vtiger_crmentity.crmid = vtiger_activity.activityid
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

	$query.=" WHERE vtiger_crmentity.deleted = 0 AND activitytype != 'Emails' " . $where;

	$query = listQueryNonAdminChange($query, 'cbCalendar');

	$log->debug('< getListQuery');
	return $query;
}

function transferForAddIntoTitle($type, $row, $CD) {
	global $current_user, $adb;
	list($CD['fieldname'],$void) = explode(':', $CD['fieldname']);
	$Col_Field = array();
	if (isset($row[$CD['columnname']])) {
		$Col_Field = array($CD['fieldname']=> $row[$CD['columnname']]);
	}

	if ($CD['fieldname'] == 'duration_hours') {
		$Col_Field['duration_minutes'] = $row['duration_minutes'];
	}

	if ($CD['fieldname'] == 'contact_id' || $CD['fieldname'] == 'cto_id') {
		$Col_Field['contact_id'] = $Col_Field['cto_id'] = getAssignedContactsForEvent($row['crmid']);
		$CD['uitype'] = '1';
	}
	if ($CD['module']=='cbCalendar') {
		$Cal_Data = getDetailViewOutputHtml($CD['uitype'], $CD['fieldname'], $CD['fieldlabel'], $Col_Field, '2', getTabid('cbCalendar'), 'cbCalendar');
		if (Field_Metadata::isPicklistUIType($CD['uitype'])) {
			$Cal_Data[1] = getTranslatedString($Cal_Data[1], $CD['module']);
		}
		if ($CD['fieldname'] == 'subject' && strpos($Cal_Data[1], 'a href') === false) {
			$Cal_Data[1] = '<a target=_blank href="index.php?module=cbCalendar&action=DetailView&record=' . $row['crmid'] . '">' . $Cal_Data[1] . '</a>';
		}
		if (strpos($Cal_Data[1], 'vtlib_metainfo')===false) {
			$Cal_Data[1] .= "<span type='vtlib_metainfo' vtrecordid='".$row['crmid']."' vtfieldname='".$CD['fieldname']
				."' vtmodule='cbCalendar' style='display:none;'></span>";
		}
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
		if ($frs && $adb->num_rows($frs)>0) {
			$relfield = $adb->query_result($frs, 0, 0);
			$queryGenerator->addCondition('id', $row[$relfield], 'e', $queryGenerator::$AND);
			$rec_query = $queryGenerator->getQuery();
			$recinfo = $adb->pquery($rec_query, array());
			$Cal_Data = array();
			$Cal_Data[0] = getTranslatedString($CD['fieldlabel'], $CD['module']);
			$Cal_Data[1] = $adb->query_result($recinfo, 0, $CD['columnname']);
			if (Field_Metadata::isPicklistUIType($CD['uitype'])) {
				$Cal_Data[1] = getTranslatedString($Cal_Data[1], $CD['module']);
			}
			if (Field_Metadata::isReferenceUIType($CD['uitype']) && !empty($Cal_Data[1])) {
				$relModule = getSalesEntityType($Cal_Data[1]);
				$einfo = getEntityName($relModule, $Cal_Data[1]);
				$Cal_Data[1] = '<a href="index.php?module='.$relModule.'&action=DetailView&record='.$Cal_Data[1].'">'.$einfo[$Cal_Data[1]].'</a>';
			}
		} else {
			if (empty($row[$CD['columnname']])) {
				$Cal_Data[1] = '';
			} else {
				$Cal_Data[1] = '<a href="index.php?module='.$CD['module'].'&action=DetailView&record='.$row['crmid'].'">'.$row[$CD['columnname']].'</a>';
			}
		}
	}

	if ($type == '1') {
		return vtlib_purify($Cal_Data[1]);
	} else {
		return '<table><tr><td><b>'.$Cal_Data[0].':</b></td>
			<td onmouseover="vtlib_listview.trigger(\'cell.onmouseover\', this)" onmouseout="vtlib_listview.trigger(\'cell.onmouseout\', this)">'
			.vtlib_purify($Cal_Data[1]).'</td></tr></table>';
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
	if ($current_user->getPrivileges()->isAdmin()) {
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
				where roleid in (\"". implode('","', $roleids) ."\") and picklistid in (select picklistid from $tablename)
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
		$label = getTranslatedString($value, 'cbCalendar');
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
	$sqlmods .= " and vtiger_tab.name != 'cbCalendar'";
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
	$sqlmods .= " and vtiger_tab.name != 'cbCalendar'";
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
	$rsmwd = $adb->pquery('SELECT distinct fieldname FROM vtiger_field as cbfld WHERE tabid = ? and uitype=5', array($tabid));
	$datefields = array();
	while ($fld = $adb->fetch_array($rsmwd)) {
		$datefields[] = $fld['fieldname'];
	}
	return $datefields;
}

function getTimeFieldsOfModule($tabid) {
	global $adb;
	$rsmwd = $adb->pquery('SELECT distinct fieldname FROM vtiger_field as cbfld WHERE tabid = ? and uitype=14', array($tabid));
	$datefields = array();
	while ($fld = $adb->fetch_array($rsmwd)) {
		$datefields[] = $fld['fieldname'];
	}
	return $datefields;
}

function getDateAndTimeFieldsOfModule($tabid) {
	global $adb;
	$rsmwd = $adb->pquery('SELECT distinct fieldname FROM vtiger_field as cbfld WHERE tabid = ? and (uitype=14 or uitype=5)', array($tabid));
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
		if (!empty($dtflds)) {
			$tmflds = getTimeFieldsOfModule($tid);
			$efields = getEntityFieldNames($module);
			$Module_StartEnd_Fields = array(
				'start'   => $dtflds[0],
				'end'     => isset($dtflds[1]) ? $dtflds[1] : '',
				'stime'   => isset($tmflds[0]) ? $tmflds[0] : '',
				'etime'   => isset($tmflds[1]) ? $tmflds[1] : '',
				'subject' => $efields['fieldname'],
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
