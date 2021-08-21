<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/database/PearDatabase.php';
require_once 'include/utils/CommonUtils.php';
require_once 'include/utils/UserInfoUtil.php';
require_once 'modules/cbCalendar/CalendarCommon.php';

/**
 * Function to get date info depending upon on the calendar view(Eg: 21 July 2000)
 * @param string calendar view(day/week/month/year)
 * @param object DateTime object
 * @return string date info (eg for dayview : 13 July 2000)
 */
function display_date($view, $date_time) {
	global $cal_log;
	$cal_log->debug('> display_date');
	if ($view == 'day') {
		$label = $date_time->get_Date().' ';
		$label .= $date_time->getmonthName().' ';
		$label .= $date_time->year;
		$cal_log->debug('< display_date');
		return $label;
	} elseif ($view == 'week') {
		$week_start = $date_time->getThisweekDaysbyIndex(1);
		$week_end = $date_time->getThisweekDaysbyIndex(7);
		$label = $week_start->get_Date().' ';
		$label .= $week_start->getmonthName().' ';
		$label .= $week_start->year;
		$label .= ' - ';
		$label .= $week_end->get_Date().' ';
		$label .= $week_end->getmonthName().' ';
		$label .= $week_end->year;
		$cal_log->debug('< display_date');
		return $label;
	} elseif ($view == 'month') {
		$label = $date_time->getmonthName().' ';
		$label .= $date_time->year;
		$cal_log->debug('< display_date');
		return $label;
	} elseif ($view == 'year') {
		$cal_log->debug('< display_date');
		return $date_time->year;
	}
}

/**
 *  Function to get css class name for date
 *  @param date
 *  @return string  css class name or empty string
 */
function dateCheck($slice_date) {
	global $cal_log;
	$cal_log->debug('> dateCheck');
	$userCurrenDate = new DateTimeField(date('Y-m-d H:i:s'));
	$today = $userCurrenDate->getDisplayDate();
	if ($today == $slice_date) {
		$cal_log->debug('< dateCheck');
		//css class for day having event(s)
		return 'currDay';
	} else {
		$cal_log->debug('< dateCheck');
		return '';
	}
}

/**
 * Fuction constructs Events ListView depends on the view
 * @param   array  $cal            - collection of objects and strings
 * @param   string $mode           - string 'listcnt' or empty. if empty means get Events ListView else get total no. of events and no. of pending events Info.
 * @return  string $activity_list  - total no. of events and no. of pending events Info(Eg: Total Events : 2, 1 Pending).
 */
function getEventListView(&$cal, $mode = '') {
	global $cal_log,$theme;
	$list_view = '';
	$cal_log->debug('> getEventListView');
	if ($cal['calendar']->view == 'day') {
		$start_date = $end_date = $cal['calendar']->date_time->get_DB_formatted_date();
	} elseif ($cal['calendar']->view == 'week') {
		$start_date = $cal['calendar']->slices[0];
		$end_date = $cal['calendar']->slices[6];
		$start_date = DateTimeField::convertToDBFormat($start_date);
		$end_date = DateTimeField::convertToDBFormat($end_date);
	} elseif ($cal['calendar']->view == 'month') {
		$start_date = $cal['calendar']->date_time->getThismonthDaysbyIndex(0);
		$end_date = $cal['calendar']->date_time->getThismonthDaysbyIndex($cal['calendar']->date_time->daysinmonth - 1);
		$start_date = $start_date->get_DB_formatted_date();
		$end_date = $end_date->get_DB_formatted_date();
	} elseif ($cal['calendar']->view == 'year') {
		$start_date = $cal['calendar']->date_time->getThisyearMonthsbyIndex(0);
		$end_date = $cal['calendar']->date_time->get_first_day_of_changed_year('increment');
		$start_date = $start_date->get_DB_formatted_date();
		$end_date = $end_date->get_DB_formatted_date();
	} else {
		die('view:'.$cal['calendar']->view.' is not defined');
	}
	//if $mode value is empty means get Events list in array format else get the count of total events and pending events in array format.
	if ($mode != '') {
		$activity_list = getEventList($cal, $start_date, $end_date, $mode);
		$cal_log->debug('< getEventListView');
		return $activity_list;
	} else {
		$ret_arr = getEventList($cal, $start_date, $end_date, $mode);
		$activity_list = $ret_arr[0];
		$navigation_array = $ret_arr[1];
	}
	//To get Events listView
	$list_view .= "<br><div id='listView'>";
	$list_view .=constructEventListView($cal, $activity_list, $navigation_array);
	$list_view .='<br></div>
		</div>';
	$list_view .="<br></td></tr></table></td></tr></table>
			</td></tr></table>
		</td></tr></table>
		</div>
		</td></tr></table>
		</td>
		<td valign=top><img src='".vtiger_imageurl('showPanelTopRight.gif', $theme)."'></td>
		</tr>
	</table>
	<br>";
	echo $list_view;
	$cal_log->debug('< getEventListView');
}

/**
 * Fuction constructs Todos ListView depends on the view
 * @param  array collection of objects and strings
 * @param  string 'listcnt' or empty. if empty means get Calendar ListView else get total no. of Events and no. of pending events information
 * @return string total no. of events and no. of pending events information (Eg: Total Events : 2, 1 Pending).
 */
function getTodosListView($cal, $check = '', $subtab = '') {
	global $cal_log,$theme;
	$list_view = '';
		$cal_log->debug('> getTodosListView');
	if ($cal['calendar']->view == 'day') {
		$start_date = $end_date = $cal['calendar']->date_time->get_DB_formatted_date();
	} elseif ($cal['calendar']->view == 'week') {
		$start_date = $cal['calendar']->slices[0];
		$end_date = $cal['calendar']->slices[6];
		$start_date = DateTimeField::convertToDBFormat($start_date);
		$end_date = DateTimeField::convertToDBFormat($end_date);
	} elseif ($cal['calendar']->view == 'month') {
		$start_date = $cal['calendar']->date_time->getThismonthDaysbyIndex(0);
		$end_date = $cal['calendar']->date_time->getThismonthDaysbyIndex($cal['calendar']->date_time->daysinmonth - 1);
		$start_date = $start_date->get_DB_formatted_date();
		$end_date = $end_date->get_DB_formatted_date();
	} elseif ($cal['calendar']->view == 'year') {
		$start_date = $cal['calendar']->date_time->getThisyearMonthsbyIndex(0);
		$end_date = $cal['calendar']->date_time->get_first_day_of_changed_year('increment');
		$start_date = $start_date->get_DB_formatted_date();
		$end_date = $end_date->get_DB_formatted_date();
	} else {
		die('view:' . $cal['calendar']->view . ' is not defined');
	}
	//if $check value is empty means get Todos list in array format else get the count of total todos and pending todos in array format.
	if ($check != '') {
		$todo_list = getTodoList($cal, $start_date, $end_date, $check);
		$cal_log->debug('< getTodosListView');
		return $todo_list;
	} else {
		$ret_arr = getTodoList($cal, $start_date, $end_date, $check);
		$todo_list = $ret_arr[0];
		$navigation_arr = $ret_arr[1];
	}
	$cal_log->debug('< getTodosListView');
	$list_view .="<div id='mnuTab2' style='background-color: rgb(255, 255, 215); display:block;'>";
	//To get Todos listView
	$list_view .= constructTodoListView($todo_list, $cal, $subtab, $navigation_arr);
	$list_view .="</div></div></td></tr></table></td></tr></table>
		</td></tr></table>
		</td></tr></table>
		</td></tr></table>
		</div>
		</td>
		<td valign=top><img src='".vtiger_imageurl('showPanelTopRight.gif', $theme)."'></td>
	</tr>
	</table>";
	echo $list_view;
}

/**
 * Function creates HTML to display Calendar DayView
 * @param  array     $cal            - collections of objects and strings.
 * @return string    $dayview_layout - html tags in string format
 */
function getDayViewLayout(&$cal) {
	global $theme, $cal_log;
	$no_of_rows = 1;
	$cal_log->debug('> getDayViewLayout');
	$day_start_hour = $cal['calendar']->day_start_hour;
	$day_end_hour = $cal['calendar']->day_end_hour;
	$format = $cal['calendar']->hour_format;
	$show_complete_view = false;
	if (!empty($_REQUEST['complete_view'])) {
		$show_complete_view =true;
	}
	$dayview_layout = '<!-- Day view layout starts here --> <table border="0" cellpadding="10" cellspacing="0" width="100%">';
	$dayview_layout .= '<tr>
		<td id="mainContent" style="border-top: 1px solid rgb(204, 204, 204);">
		<table border="0" cellpadding="5" cellspacing="0" width="100%">';
	if (!empty($show_complete_view)) {
		$dayview_layout .= '<tr><td width=12% class="lvtCol" bgcolor="blue" valign=top><img onClick="document.EventViewOption.complete_view.value=0;fnRedirect();" src="'
			.vtiger_imageurl('activate.gif', $theme).'" border="0"></td><td class="dvtCellInfo">&nbsp;</td><td class="dvtCellInfo">&nbsp;</td></tr>';
		$day_start_hour = 0;
		$day_end_hour = 23;
	} else {
		$dayview_layout .= '<tr><td width=12% class="lvtCol" bgcolor="blue" valign=top><img onClick="document.EventViewOption.complete_view.value=1;fnRedirect();" src="'
			.vtiger_imageurl('inactivate.gif', $theme).'" border="0"></td><td class="dvtCellInfo">&nbsp;</td><td class="dvtCellInfo">&nbsp;</td></tr>';
	}
	for ($j=0; $j<24; $j++) {
		$slice = $cal['calendar']->slices[$j];
		$act = $cal['calendar']->day_slice[$slice]->activities;
		if (!empty($act)) {
			$temprows = count($act);
			$no_of_rows = ($no_of_rows>$temprows)?$no_of_rows:$temprows;
		}
	}
	for ($i=$day_start_hour; $i<=$day_end_hour; $i++) {
		$time = array('hour'=>$i,'minute'=>0);
		$sub_str = formatUserTimeString($time, $format);
		$y = $i+1;
		$hour_startat = formatUserTimeString(array('hour'=>$i,'minute'=>0), '24');
		$hour_endat = formatUserTimeString(array('hour'=>$y,'minute'=>0), '24');

		$time_arr = getaddEventPopupTime($hour_startat, $hour_endat, $format);
		$date = new DateTimeField(null);
		$endDate = new DateTimeField(date('Y-m-d', time() + (1*24*50*60)));
		$sttemp_date = $date->getDisplayDate();
		$endtemp_date = $endDate->getDisplayDate();

		$js_string = '';
		if (isPermitted('cbCalendar', 'CreateView') == 'yes') {
			$js_string = "onClick=\"fnvshobj(this, 'addEvent'); gshow('addEvent', 'Call', '".$sttemp_date."','".$endtemp_date."','".$time_arr['starthour']."','"
				.$time_arr['startmin']."','".$time_arr['startfmt']."','".$time_arr['endhour']."','".$time_arr['endmin']."','".$time_arr['endfmt']
				."', 'hourview', 'event')\"";
		}
		$dayview_layout .= '<tr><td style="cursor:pointer;" class="lvtCol" valign=top height="75"  width="10%" '.$js_string.'>'.$sub_str.'</td>';
		//To display events in Dayview
		$dayview_layout .= getdayEventLayer($cal, $cal['calendar']->slices[$i], $no_of_rows);
		$dayview_layout .= '</tr>';
	}
	$dayview_layout .= '</table>
		</td></tr></table>';
	$cal_log->debug('< getDayViewLayout');
	return $dayview_layout;
}

/**
 * Function creates HTML to display Calendar WeekView
 * @param  array     $cal             - collections of objects and strings.
 * @return string    $weekview_layout - html tags in string format
 */
function getWeekViewLayout(&$cal) {
	global $cal_log, $theme;
	$cal_log->debug('> getWeekViewLayout');
	$day_start_hour = $cal['calendar']->day_start_hour;
	$day_end_hour = $cal['calendar']->day_end_hour;
	$format = $cal['calendar']->hour_format;
	$show_complete_view = false;
	if (!empty($_REQUEST['complete_view'])) {
		$show_complete_view =true;
	}
	$weekview_layout = '';
	$weekview_layout .= '<table border="0" cellpadding="10" cellspacing="0" width="98%" class="calDayHour" style="background-color: #dadada">';
	for ($col=0; $col<=7; $col++) {
		if ($col==0) {
				$weekview_layout .= '<tr>';
			if (!empty($show_complete_view)) {
				$weekview_layout.='<td width=12% class="lvtCol" bgcolor="blue" valign=top><img onClick="document.EventViewOption.complete_view.value=0;fnRedirect();" src="'
					.vtiger_imageurl('activate.gif', $theme).'" border="0"></td>';
				$day_start_hour = 0;
				$day_end_hour = 23;
			} else {
				$weekview_layout.='<td width=12% class="lvtCol" bgcolor="blue" valign=top><img onClick="document.EventViewOption.complete_view.value=1;fnRedirect();" src="'
					.vtiger_imageurl('inactivate.gif', $theme).'" border="0"></td>';
			}
		} else {
			//To display Days in Week
			$cal['slice'] = $cal['calendar']->week_array[$cal['calendar']->slices[$col-1]];
			$date = $cal['calendar']->date_time->getThisweekDaysbyIndex($col);
			$day = $date->getdayofWeek_inshort();
			$weekview_layout .= '<td width=12% class="lvtCol" bgcolor="blue" valign=top>';
			$weekview_layout .= '<a href="index.php?module=cbCalendar&action=index&view='.$cal['slice']->getView().'&'.$cal['slice']->start_time->get_date_str().'">';
			$weekview_layout .= $date->get_Date().' - '.$day;
			$weekview_layout .= '</a>';
			$weekview_layout .= '</td>';
		}
	}
	$weekview_layout .= '</tr></table>';
	$weekview_layout .= '<table border="0" cellpadding="10" cellspacing="1" width="98%" class="calDayHour" style="background-color: #dadada">';
	//To display Hours in User selected format
	for ($i=$day_start_hour; $i<=$day_end_hour; $i++) {
		$count = $i;
		$hour_startat = formatUserTimeString(array('hour'=>$i,'minute'=>0), '24');
		$hour_endat = formatUserTimeString(array('hour'=>($i+1),'minute'=>0), '24');
		$time_arr = getaddEventPopupTime($hour_startat, $hour_endat, $format);

		$weekview_layout .= '<tr>';
		for ($column=1; $column<=1; $column++) {
			$time = array('hour'=>$i,'minute'=>0);
			$sub_str = formatUserTimeString($time, $format);
			$weekview_layout .= '<td style="border-top: 1px solid rgb(239, 239, 239); background-color: rgb(234, 234, 234); height: 40px;" valign="top" width="12%">';
			$weekview_layout .=$sub_str;
			$weekview_layout .= '</td>';
		}
		for ($column=0; $column<=6; $column++) {
			$temp_ts = $cal['calendar']->week_array[$cal['calendar']->slices[$column]]->start_time->ts;
			$date = new DateTimeField(date('Y-m-d', $temp_ts));
			$sttemp_date = $date->getDisplayDate();
			if ($i != 23) {
				$endtemp_date = $sttemp_date;
			} else {
				$endtemp_ts = $cal['calendar']->week_array[$cal['calendar']->slices[$column+1]]->start_time->ts;
				$endDate = new DateTimeField(date('Y-m-d', $temp_ts));
				$endtemp_date = $endDate->getDisplayDate();
			}

			$weekview_layout .= '<td class="cellNormal" onMouseOver="cal_show(\'create_'.$sttemp_date.''.$time_arr['starthour'].''.$time_arr['startfmt']
				.'\')" onMouseOut="fnHide_Event(\'create_'.$sttemp_date.''.$time_arr['starthour'].''.$time_arr['startfmt']
				.'\')" style="height: 40px;" bgcolor="white" valign="top" width="12%" align=right vlign=top>';
			$weekview_layout .= '<div id="create_'.$sttemp_date.''.$time_arr['starthour'].''.$time_arr['startfmt'].'" style="visibility: hidden;">';
			if (isPermitted('cbCalendar', 'CreateView') == 'yes') {
				$weekview_layout .= "<img onClick=\"fnvshobj(this,'addEvent'); gshow('addEvent', 'Call', '".$sttemp_date."','".$endtemp_date."','".$time_arr['starthour']
					."','".$time_arr['startmin']."','".$time_arr['startfmt']."','".$time_arr['endhour']."','".$time_arr['endmin']."','".$time_arr['endfmt']
					."', 'hourview', 'event')\" src=\"".vtiger_imageurl('cal_add.gif', $theme).'" border="0">';
			}
			$weekview_layout .='</div>';
			//To display events in WeekView
			$weekview_layout .=getweekEventLayer($cal, $cal['calendar']->week_hour_slices[$count]);
			$weekview_layout .= '</td>';
			$count = $count+24;
		}
		$weekview_layout .= '</tr>';
	}
	$weekview_layout .= '</table>';
	$cal_log->debug('< getWeekViewLayout');
	return $weekview_layout;
}

/**
 * Function creates HTML to display Calendar MonthView
 * @param  array     $cal            - collections of objects and strings.
 * @return  string    $monthview_layout - html tags in string format
 */
function getMonthViewLayout(&$cal) {
	global $current_user, $cal_log, $theme;
	$cal_log->debug('> getMonthViewLayout');
	$date_format = $current_user->date_format;
	$count = 0;
	//To get no. of rows(weeks) in month
	if ($cal['calendar']->month_array[$cal['calendar']->slices[35]]->start_time->month != $cal['calendar']->date_time->month) {
		$rows = 5;
	} else {
		$rows = 6;
	}
	$format = $cal['calendar']->hour_format;
	$hour_startat = formatUserTimeString(array('hour'=>date('H:i'),'minute'=>0), '24');
	$hour_endat = formatUserTimeString(array('hour'=>date('H:i', (time() + (60 * 60))),'minute'=>0), '24');
	$time_arr = getaddEventPopupTime($hour_startat, $hour_endat, $format);
	$monthview_layout = '';
	$monthview_layout .= '<table class="calDayHour" style="background-color: rgb(218, 218, 218);" border="0" cellpadding="5" cellspacing="1" width="98%"><tr>';
	//To display days in week
	for ($i = 0; $i < 7; $i ++) {
		$first_row = $cal['calendar']->month_array[$cal['calendar']->slices[$i]];
		$weekday = $first_row->start_time->getdayofWeek();
		$monthview_layout .= '<td class="lvtCol" valign="top" width="14%">'.$weekday.'</td>';
	}
	$monthview_layout .= '</tr></table>';
	$monthview_layout .= '<!-- month headers --> <table border=0 cellspacing=1 cellpadding=5 width=98% class="calDayHour" >';
	$cnt = 0;
	for ($i = 0; $i < $rows; $i ++) {
		$monthview_layout .= '<tr>';
		for ($j = 0; $j < 7; $j ++) {
			$temp_ts = $cal['calendar']->month_array[$cal['calendar']->slices[$count]]->start_time->ts;
			$temp_date = (($date_format == 'dd-mm-yyyy') ?
				(date('d-m-Y', $temp_ts)) :
				(($date_format== 'mm-dd-yyyy') ? (date('m-d-Y', $temp_ts)) : (($date_format == 'yyyy-mm-dd') ? (date('Y-m-d', $temp_ts)) : ('')))
			);
			if ($cal['calendar']->day_start_hour != 23) {
				$endtemp_date = $temp_date;
			} else {
				$endtemp_ts = $cal['calendar']->month_array[$cal['calendar']->slices[$count+1]]->start_time->ts;
				$endtemp_date = (($date_format == 'dd-mm-yyyy') ?
					(date('d-m-Y', $endtemp_ts)) :
					(($date_format== 'mm-dd-yyyy') ? (date('m-d-Y', $endtemp_ts)) : (($date_format == 'yyyy-mm-dd') ? (date('Y-m-d', $endtemp_ts)) : ('')))
				);
			}
			$cal['slice'] = $cal['calendar']->month_array[$cal['calendar']->slices[$count]];
			$monthclass = dateCheck($cal['slice']->start_time->get_formatted_date());
			if ($monthclass != '') {
				$monthclass = 'calSel';
			} else {
				$monthclass = 'dvtCellLabel';
			}
			//to display dates in month
			if ($cal['slice']->start_time->getMonth() == $cal['calendar']->date_time->getMonth()) {
				$monthview_layout .= '<td style="text-align:left;" class="'.$monthclass.'" width="14%" onMouseOver="cal_show(\'create_'.$temp_date.$time_arr['starthour']
					.'\')" onMouseOut="fnHide_Event(\'create_'.$temp_date.''.$time_arr['starthour'].'\')">';
				$monthview_layout .= '<a href="index.php?module=cbCalendar&action=index&view='.$cal['slice']->getView().''.$cal['slice']->start_time->get_date_str().'">';
				$monthview_layout .= $cal['slice']->start_time->get_Date();
				$monthview_layout .= '</a>';
				$monthview_layout .= '<div id="create_'.$temp_date.''.$time_arr['starthour'].'" style="visibility:hidden;">';
				if (isPermitted('cbCalendar', 'CreateView') == 'yes') {
					$monthview_layout .= "<a onClick=\"fnvshobj(this, 'addEvent'); gshow('addEvent', 'Call', '".$temp_date."','".$endtemp_date."','"
						.$time_arr['starthour']."','".$time_arr['startmin']."','".$time_arr['startfmt']."','".$time_arr['endhour']."','".$time_arr['endmin']."','"
						.$time_arr['endfmt']."', 'hourview','event')".'" href="javascript:void(0)"><img src="'.vtiger_imageurl('cal_add.gif', $theme).'" border="0"></a>';
				}
					$monthview_layout .= '  </div></td>';
			} else {
				$monthview_layout .= '<td class="dvtCellLabel" width="14%">&nbsp;</td>';
			}
			$count++;
		}
		$monthview_layout .= '</tr>';
		$monthview_layout .= '<tr>';
		for ($j = 0; $j < 7; $j ++) {
			$monthview_layout .= '<td bgcolor="white" height="90" valign="top" width="200" align=right>';
			$monthview_layout .= getmonthEventLayer($cal, $cal['calendar']->slices[$cnt]);
			$monthview_layout .= '</td>';
			$cnt++;
		}
		$monthview_layout .= '</tr>';
	}
	$monthview_layout .= '</table>';
	$cal_log->debug('< getMonthViewLayout');
	return $monthview_layout;
}

/**
 * Function creates HTML to display Calendar YearView
 * @param  array     $cal            - collections of objects and strings.
 * @return  string    $yearview_layout - html tags in string format
 */
function getYearViewLayout(&$cal) {
	global $mod_strings, $cal_log;
	$cal_log->debug('> getYearViewLayout');
	$yearview_layout = '';
	$yearview_layout .= '<table border="0" cellpadding="5" cellspacing="0" width="100%">';
	$count = 0;
	//year view divided as 4 rows and 3 columns
	for ($i=0; $i<4; $i++) {
		$yearview_layout .= '<tr>';
		for ($j=0; $j<3; $j++) {
			$cal['slice'] = $cal['calendar']->year_array[$cal['calendar']->slices[$count]];
			$yearview_layout .= '<td width="33%">
				<table class="mailClient " border="0" cellpadding="2" cellspacing="0" width="98%">
					<tr>
						<td colspan="7" class="calHdr" style="padding:5px">
						<a style="text-decoration: none;" href="index.php?module=cbCalendar&action=index&view=month&hour=0&day=1&month='.($count+1)
							.'&year='.$cal['calendar']->date_time->year.'"><b>'.$cal['slice']->start_time->month_inlong.'</b></a>
						</td>
					</tr><tr class="hdrNameBg">';
			for ($w=0; $w<7; $w++) {
				$yearview_layout .= '<th width="14%">'.$mod_strings['cal_weekdays_short'][$w].'</th>';
			}
			$yearview_layout .= '</tr>';
			$date = DateTimeField::convertToDBFormat($cal['calendar']->month_day_slices[$count][35]);
			list($_3rdyear,$_3rdmonth,$_3rddate) = explode('-', $date);
			$date = DateTimeField::convertToDBFormat($cal['calendar']->month_day_slices[$count][6]);
			list($_2ndyear,$_2ndmonth,$_2nddate) = explode('-', $date);
			//to get no. of rows(weeks) in month
			if ($_3rdmonth != $_2ndmonth) {
				$rows = 5;
			} else {
				$rows = 6;
			}
			$cnt = 0;
			$date_stack = array();
			for ($k = 0; $k < 5; $k ++) {
				$yearview_layout .= '<tr>';
				for ($mr = 0; $mr < 7; $mr ++) {
					$date = DateTimeField::convertToDBFormat($cal['calendar']->month_day_slices[$count][$cnt]);
					list($_1styear,$_1stmonth,$_1stdate) = explode('-', $date);
					if (count($cal['slice']->activities) != 0) {
						for ($act_count = 0; $act_count<count($cal['slice']->activities); $act_count++) {
							$date_stack[] = $cal['slice']->activities[$act_count]->start_time->get_formatted_date();
						}
					}
					if (in_array($cal['calendar']->month_day_slices[$count][$cnt], $date_stack)) {
						$event_class = 'class="eventDay"';
					} else {
						$event_class = '';
					}
					if ($_1stmonth == $_2ndmonth) {
						$curclass = dateCheck($cal['calendar']->month_day_slices[$count][$cnt]);
					}
					if ($curclass != '') {
						$class = 'class="'.$curclass.'"';
						$curclass = '';
					} else {
						$class = $event_class;
						$event_class = '';
					}
					$date = $_1stdate + 0;
					$month = $_1stmonth + 0;
					$yearview_layout .= '<td '.$class.' style="text-align:center">';
					if ($rows == 6 && $k==0) {
						$tdate = DateTimeField::convertToDBFormat($cal['calendar']->month_day_slices[$count][35+$mr]);
						list($tempyear,$tempmonth,$tempdate) = explode('-', $tdate);
						if ($tempmonth == $_2ndmonth) {
							$yearview_layout .= '<a href="index.php?module=cbCalendar&action=index&view=day&hour=0&day='
								.$tempdate.'&month='.$tempmonth.'&year='.$tempyear.'">'.$tempdate;
						}
					}
					if ($_1stmonth == $_2ndmonth) {
						$yearview_layout .= '<a href="index.php?module=cbCalendar&action=index&view=day&hour=0&day='.$date.'&month='.$month.'&year='.$_1styear.'">'.$date;
					}
					$yearview_layout .= '</a></td>';
					$cnt++;
				}
				$yearview_layout .= '</tr>';
			}
			$yearview_layout .= '</table>';
			$count++;
		}
		$yearview_layout .= '</tr>';
	}
	$yearview_layout .= '</table>';
	$cal_log->debug('< getYearViewLayout');
	return $yearview_layout;
}

/**
 * Function creates HTML To display events in day view
 * @param  array     $cal         - collection of objects and strings
 * @param  string    $slice       - date:time(eg: 2006-07-13:10)
 * @return string    $eventlayer  - hmtl in string format
 */
function getdayEventLayer(&$cal, $slice, $rows) {
	global $mod_strings, $cal_log, $listview_max_textlength, $adb, $current_user, $theme;
	$cal_log->debug('> getdayEventLayer');
	$eventlayer = '';
	$arrow_img_name = '';
	$rows = $rows + 1;
	$last_colwidth = 100 / $rows;
	$width = 100 / $rows ;
	$act = $cal['calendar']->day_slice[$slice]->activities;
	if (!empty($act)) {
		for ($i=0; $i<count($act); $i++) {
			$rowspan = 1;
			$arrow_img_name = 'event'.$cal['calendar']->day_slice[$slice]->start_time->hour.'_'.$i;
			$subject = $act[$i]->subject;
			$id = $act[$i]->record;
			if ($listview_max_textlength && (strlen($subject)>$listview_max_textlength)) {
				$subject = substr($subject, 0, $listview_max_textlength).'...';
			}
			$format = $cal['calendar']->hour_format;
			$duration_hour = $act[$i]->duration_hour;
			$duration_min =$act[$i]->duration_minute;
			$user = $act[$i]->owner;
			$priority = $act[$i]->priority;
			if ($duration_min != '00') {
				$rowspan = $duration_hour+$rowspan;
			} elseif ($duration_hour != '0') {
				$rowspan = $duration_hour;
			}
			$row_cnt = $rowspan;
			$start_hour = timeString($act[$i]->start_time, $format);
			$end_hour = timeString($act[$i]->end_time, $format);
			$account_name = $act[$i]->accountname;
			$eventstatus = $act[$i]->eventstatus;
			$color = $act[$i]->color;
			$image = vtiger_imageurl($act[$i]->image_name, $theme);
			if ($act[$i]->recurring) {
				$recurring = '<img src="'.vtiger_imageurl($act[$i]->recurring, $theme).'" align="middle" border="0"></img>';
			} else {
				$recurring = '&nbsp;';
			}
			$height = $rowspan * 75;
			$javacript_str = '';
			$idShared = 'normal';
			if ($act[$i]->shared) {
				$idShared = 'shared';
			}
			if ($idShared == 'normal') {
				if (isPermitted('cbCalendar', 'EditView') == 'yes' || isPermitted('cbCalendar', 'Delete') == 'yes') {
					$javacript_str = 'onMouseOver="cal_show(\''.$arrow_img_name.'\');" onMouseOut="fnHide_Event(\''.$arrow_img_name.'\');"';
				}
				$action_str = '<img src="' . vtiger_imageurl('cal_event.jpg', $theme). '" id="'.$arrow_img_name
					.'" style="visibility: hidden;" onClick="getcalAction(this,\'eventcalAction\','.$id.",'".$cal['view']."','".$cal['calendar']->date_time->hour."','"
					.$cal['calendar']->date_time->get_DB_formatted_date()."','event');".'" align="middle" border="0">';
			} else {
				$javacript_str = '';
				$eventlayer .= '&nbsp;';
			}
			$eventlayer .= '<td class="dvtCellInfo" rowspan="'.$rowspan.'" colspan="1" width="'.$width.'%" >';

			$visibility_query=$adb->pquery('SELECT visibility from vtiger_activity where activityid=?', array($id));
			$visibility = $adb->query_result($visibility_query, 0, 'visibility');
			$user_query = $adb->pquery(
				"SELECT vtiger_crmobject.smownerid,vtiger_users.user_name from vtiger_crmobject,vtiger_users where crmid=? and vtiger_crmobject.smownerid=vtiger_users.id",
				array($id)
			);
			$userid = $adb->query_result($user_query, 0, 'smownerid');
			$assigned_role_query=$adb->pquery('select roleid from vtiger_user2role where userid=?', array($userid));
			$assigned_role_id = $adb->query_result($assigned_role_query, 0, 'roleid');
			$role_list = $adb->pquery(
				"SELECT * from vtiger_role WHERE parentrole LIKE '".formatForSqlLike($current_user->column_fields['roleid']).formatForSqlLike($assigned_role_id)."'",
				array()
			);
			$is_shared = $adb->pquery('SELECT * from vtiger_sharedcalendar where userid=? and sharedid=?', array($userid,$current_user->id));
			$userName = getFullNameFromArray('Users', $current_user->column_fields);
			if (($current_user->column_fields['is_admin']!='on' && $adb->num_rows($role_list)==0
				&& (($adb->num_rows($is_shared)==0 && ($visibility=='Public' || $visibility=='Private')) || $visibility=='Private'))
				&& $userName!=$user
			) {
				$eventlayer .= '<div id="event_'.$cal['calendar']->day_slice[$slice]->start_time->hour.'_'.$i.'" class="event" style="height:'.$height.'px;">';
			} else {
				$eventlayer .= '<div id="event_'.$cal['calendar']->day_slice[$slice]->start_time->hour.'_'.$i.'" class="event" style="height:'.$height.'px;" '.$javacript_str.'>';
			}
			$eventlayer .= '<table border="0" cellpadding="1" cellspacing="0" width="100%">
				<tr>
				<td width="10%" align="center"><img src="'.$image.'" align="middle" border="0"></td>
				<td width="90%"><b>'.$start_hour.' - '.$end_hour.'</b></td></tr>';
			$eventlayer .= '<tr><td align="center">'.$recurring.'</td>';

			if (($current_user->column_fields['is_admin']!='on' && $adb->num_rows($role_list)==0
				&& (($adb->num_rows($is_shared)==0 && ($visibility=='Public' || $visibility=='Private')) || $visibility=='Private'))
				&& $userName!=$user
			) {
				$eventlayer .= '<td><font color="silver"><b>'.$user.' - '.$mod_strings['LBL_BUSY'].'</b></font></td>';
			} else {
				$eventlayer .= '<td><a href="index.php?action=DetailView&module=cbCalendar&record='.$id.'&activity_mode=Events&viewtype=calendar"><span class="orgTab">'
					.$subject.'</span></a></td>
				</tr>
				<tr><td align="center">';
				if ($act[$i]->shared) {
					$eventlayer .= '<img src="' . vtiger_imageurl('cal12x12Shared.gif', $theme). '" align="middle" border="0">';
				} else {
					$eventlayer .= '&nbsp;';
				}
				$eventlayer .= '</td><td>('.$user.' | '.getTranslatedString($eventstatus).' | '.getTranslatedString($priority).')</td></tr><tr><td align="center">'
					.$action_str.'</td><td>&nbsp;</td></tr>';
			}
			$eventlayer .= '</table></div></td>';
		}
		$eventlayer .= '<td class="dvtCellInfo" rowspan="1" width="'.$last_colwidth.'%">&nbsp;</td>';
	} else {
		$eventlayer .= '<td class="dvtCellInfo" colspan="'.($rows - 1).'" width="'.($last_colwidth * ($rows - 1)).'%" rowspan="1">&nbsp;</td>';
		$eventlayer .= '<td class="dvtCellInfo" rowspan="1" width="'.$last_colwidth.'%">&nbsp;</td>';
	}
	$cal_log->debug('< getdayEventLayer');
	return $eventlayer;
}

/**
 * Function creates HTML To display events in week view
 * @param  array     $cal         - collection of objects and strings
 * @param  string    $slice       - date:time(eg: 2006-07-13:10)
 * @return string    $eventlayer  - hmtl in string format
 */
function getweekEventLayer(&$cal, $slice) {
	global $mod_strings,$cal_log,$listview_max_textlength,$adb,$current_user,$theme;
	$cal_log->debug('> getweekEventLayer');
	$eventlayer = '';
	$arrow_img_name = '';
	$height = 1 * 75;
	$act = $cal['calendar']->week_slice[$slice]->activities;
	if (!empty($act)) {
		for ($i=0; $i<count($act); $i++) {
			$arrow_img_name = 'weekevent'.$cal['calendar']->week_slice[$slice]->start_time->get_formatted_date().'_'.$act[$i]->start_time->hour.'_'.$i;
			$id = $act[$i]->record;
			$subject = $act[$i]->subject;
			if ($listview_max_textlength && (strlen($subject)>$listview_max_textlength)) {
				$subject = substr($subject, 0, $listview_max_textlength).'...';
			}
			$format = $cal['calendar']->hour_format;
			$start_hour = timeString($act[$i]->start_time, $format);
			$end_hour = timeString($act[$i]->end_time, $format);
			$account_name = $act[$i]->accountname;
			$eventstatus = $act[$i]->eventstatus;
			$user = $act[$i]->owner;
			$priority = $act[$i]->priority;
			$image =  vtiger_imageurl($act[$i]->image_name, $theme);
			$idShared = 'normal';
			if ($act[$i]->shared) {
				$idShared = 'shared';
			}
			if ($act[$i]->recurring) {
				$recurring = '<img src="'.vtiger_imageurl($act[$i]->recurring, $theme).'" align="middle" border="0"></img>';
			} else {
				$recurring = '&nbsp;';
			}
			$color = $act[$i]->color;
			if ($idShared == 'normal') {
				if (isPermitted('cbCalendar', 'EditView') == 'yes' || isPermitted('cbCalendar', 'Delete') == 'yes') {
					$javacript_str = 'onMouseOver="cal_show(\''.$arrow_img_name.'\');" onMouseOut="fnHide_Event(\''.$arrow_img_name.'\');"';
				}
				$action_str = '<img src="' . vtiger_imageurl('cal_event.jpg', $theme). '" id="'.$arrow_img_name
					.'" style="visibility: hidden;" onClick="getcalAction(this,\'eventcalAction\','.$id.",'".$cal['view']."','".$cal['calendar']->date_time->hour."','"
					.$cal['calendar']->date_time->get_DB_formatted_date()."','event');".'" align="middle" border="0">';
			} else {
				$javacript_str = '';
				$eventlayer .= '&nbsp;';
			}

			$visibility_query=$adb->pquery('SELECT visibility from vtiger_activity where activityid=?', array($id));
			$visibility = $adb->query_result($visibility_query, 0, 'visibility');
			$user_query = $adb->pquery(
				'SELECT vtiger_crmobject.smownerid,vtiger_users.user_name from vtiger_crmobject,vtiger_users where crmid=? and vtiger_crmobject.smownerid=vtiger_users.id',
				array($id)
			);
			$userid = $adb->query_result($user_query, 0, 'smownerid');
			$assigned_role_query=$adb->pquery('select roleid from vtiger_user2role where userid=?', array($userid));
			$assigned_role_id = $adb->query_result($assigned_role_query, 0, 'roleid');
			$role_list = $adb->pquery(
				"SELECT * from vtiger_role WHERE parentrole LIKE '".formatForSqlLike($current_user->column_fields['roleid']).formatForSqlLike($assigned_role_id)."'",
				array()
			);
			$is_shared = $adb->pquery('SELECT * from vtiger_sharedcalendar where userid=? and sharedid=?', array($userid,$current_user->id));
			$userName = getFullNameFromArray('Users', $current_user->column_fields);
			if (($current_user->column_fields['is_admin']!='on' && $adb->num_rows($role_list)==0
				&& (($adb->num_rows($is_shared)==0 && ($visibility=='Public' || $visibility=='Private')) || $visibility=='Private'))
				&& $userName!=$user
			) {
				$eventlayer .= '<div id="event_'.$cal['calendar']->day_slice[$slice]->start_time->hour.'_'.$i.'" class="event" style="height:'.$height.'px;">';
			} else {
				$eventlayer .= '<div id="event_'.$cal['calendar']->day_slice[$slice]->start_time->hour.'_'.$i.'" class="event" style="height:'.$height.'px;" '.$javacript_str.'>';
			}

			$eventlayer .='<table border="0" cellpadding="1" cellspacing="0" width="100%">
				<tr>
					<td width="10%" align="center"><img src="'.$image.'" align="middle" border="0"></td>
					<td width="90%"><b>'.$start_hour.' - '.$end_hour.'</b></td>
				</tr>
				<tr>
					<td align="center">'.$recurring.'</td>';
			if (($current_user->column_fields['is_admin']!='on' && $adb->num_rows($role_list)==0
				&& (($adb->num_rows($is_shared)==0 && ($visibility=='Public' || $visibility=='Private')) || $visibility=='Private'))
				&& $userName!=$user
			) {
				$eventlayer .= '<td><font color="silver"><b>'.$user.'-'.$mod_strings['LBL_BUSY'].'</b></font></td>';
			} else {//CALUSER CUST END
				$eventlayer .= '<td><a href="index.php?action=DetailView&module=cbCalendar&record='.$id.'&activity_mode=Events&viewtype=calendar"><span class="orgTab">'
					.$subject.'</span></a></td></tr><tr><td align="center">';
				if ($act[$i]->shared) {
					$eventlayer .= '<img src="' . vtiger_imageurl('cal12x12Shared.gif', $theme). '" align="middle" border="0">';
				} else {
					$eventlayer .= '&nbsp;';
				}
				$eventlayer .= '</td><td>('.$user.' | '.getTranslatedString($eventstatus).' | '.getTranslatedString($priority).')</td></tr><tr><td align="center">'
					.$action_str.'</td><td>&nbsp;</td></tr>';
			}
				$eventlayer .= '</table></div><br>';
		}
		$cal_log->debug('< getweekEventLayer');
		return $eventlayer;
	}
}

/**
 * Function creates HTML To display events in month view
 * @param  array     $cal         - collection of objects and strings
 * @param  string    $slice       - date(eg: 2006-07-13)
 * @return string    $eventlayer  - hmtl in string format
 */
function getmonthEventLayer(&$cal, $slice) {
	global $mod_strings,$cal_log,$adb,$current_user,$theme;
	$cal_log->debug('> getmonthEventLayer');
	$eventlayer = '';
	$arrow_img_name = '';
	$act = $cal['calendar']->month_array[$slice]->activities;
	if (!empty($act)) {
		$no_of_act = count($act);
		if ($no_of_act>2) {
			$act_row = 2;
			$remin_list = $no_of_act - $act_row;
		} else {
			$act_row = $no_of_act;
			$remin_list = null;
		}
		for ($i=0; $i<$act_row; $i++) {
			$arrow_img_name = 'event'.$cal['calendar']->month_array[$slice]->start_time->hour.'_'.$i;
			$id = $act[$i]->record;
			$subject = $act[$i]->subject;
			if (strlen($subject)>10) {
				$subject = substr($subject, 0, 10).'...';
			}
			$format = $cal['calendar']->hour_format;
			$start_hour = timeString($act[$i]->start_time, $format);
			$end_hour = timeString($act[$i]->end_time, $format);
			$account_name = $act[$i]->accountname;
			$image = vtiger_imageurl($act[$i]->image_name, $theme);
			$color = $act[$i]->color;
			//Added for User Based Customview for Calendar Module
			$visibility_query=$adb->pquery('SELECT visibility from vtiger_activity where activityid=?', array($id));
			$visibility = $adb->query_result($visibility_query, 0, 'visibility');
			$user_query = $adb->pquery(
				'SELECT vtiger_crmobject.smownerid,vtiger_users.user_name from vtiger_crmobject,vtiger_users where crmid=? and vtiger_crmobject.smownerid=vtiger_users.id',
				array($id)
			);
			$userid = $adb->query_result($user_query, 0, 'smownerid');
			$username = $adb->query_result($user_query, 0, 'user_name');
			$assigned_role_query=$adb->pquery('select roleid from vtiger_user2role where userid=?', array($userid));
			$assinged_role_id = $adb->query_result($assigned_role_query, 0, 'roleid');
			$role_list = $adb->pquery(
				"SELECT * from vtiger_role WHERE parentrole LIKE '".formatForSqlLike($current_user->column_fields['roleid']).formatForSqlLike($assinged_role_id)."'",
				array()
			);
			$is_shared = $adb->pquery('SELECT * from vtiger_sharedcalendar where userid=? and sharedid=?', array($userid,$current_user->id));

			if (($current_user->column_fields['is_admin']!='on' && $adb->num_rows($role_list)==0
				&& (($adb->num_rows($is_shared)==0 && ($visibility=='Public' || $visibility=='Private')) || $visibility=='Private'))
				&& $current_user->id != $userid
			) {
				$eventlayer .='<div class ="event" id="event_'.$cal['calendar']->month_array[$slice]->start_time->hour.'_'.$i.'">
					<nobr><img src="'.$image.'" border="0"></img>&nbsp;'.$username.' - '.$mod_strings["LBL_BUSY"].'</nobr></div><br>';
			} else {
				$eventlayer .='<div class ="event" id="event_'.$cal['calendar']->month_array[$slice]->start_time->hour.'_'.$i.'">
					<nobr><img src="'.$image.'" border="0"></img>&nbsp;<a href="index.php?action=DetailView&module=cbCalendar&record='.$id
					.'&activity_mode=Events&viewtype=calendar"><span class="orgTab">'.$start_hour.' - '.$end_hour.'</span></a></nobr></div><br>';
			}
		}

		if ($remin_list != null) {
			$eventlayer.='<div valign=bottom align=right width=10%><a href="index.php?module=cbCalendar&action=index&view='.$cal['calendar']->month_array[$slice]->getView()
				.'&'.$cal['calendar']->month_array[$slice]->start_time->get_date_str().'" class="webMnu">+'.$remin_list.'&nbsp;'.$mod_strings['LBL_MORE'].'</a></div>';
		}
		$cal_log->debug('< getmonthEventLayer');
		return $eventlayer;
	}
}

/**
 * Function to get events list scheduled between specified dates
 * @param array collection of objects and strings
 * @param date
 * @param date
 * @param string  'listcnt'|empty. if 'listcnt' means it returns no. of events and no. of pending events in array format else it returns events list in array format
 * @return array  eventslists in array format
 */
function getEventList(&$calendar, $start_date, $end_date, $info = '') {
	global $adb, $current_user, $mod_strings, $app_strings, $cal_log, $theme, $currentModule;
	$list_max_entries_per_page = GlobalVariable::getVariable('Application_ListView_PageSize', 20, $currentModule);
	$listview_max_textlength = GlobalVariable::getVariable('Application_ListView_Max_Text_Length', 40, $currentModule);
	$Entries = array();
	$userprivs = $current_user->getPrivileges();
	$cal_log->debug('> getEventList');

	$and = "AND ((((dtstart >= ? AND dtstart <= ?) OR (dtend >= ? AND dtend <= ?) OR (dtstart <= ? AND dtend >= ?)) AND vtiger_recurringevents.activityid is NULL)
		OR
		(
			(CAST(CONCAT(vtiger_recurringevents.recurringdate,' ',time_start) AS DATETIME) >= ?
				AND CAST(CONCAT(vtiger_recurringevents.recurringdate,' ',time_start) AS DATETIME) <= ?)
			OR (dtend >= ? AND dtend <= ?) OR (CAST(CONCAT(vtiger_recurringevents.recurringdate,' ',time_start) AS DATETIME) <= ? AND dtend >= ?)
		)
	)";
	$crmEntityTable = CRMEntity::getcrmEntityTableAlias('cbCalendar');
	$query = "SELECT vtiger_groups.groupname, vtiger_users.ename as user_name,vtiger_crmentity.smownerid, vtiger_crmentity.crmid,vtiger_activity.*
		FROM vtiger_activity
		INNER JOIN ".$crmEntityTable." ON vtiger_crmentity.crmid = vtiger_activity.activityid
		LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
		LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
		LEFT OUTER JOIN vtiger_recurringevents ON vtiger_recurringevents.activityid = vtiger_activity.activityid
		WHERE vtiger_crmentity.deleted = 0 AND vtiger_activity.activitytype != 'Emails' $and ";

	$list_query = $query.' AND vtiger_crmentity.smownerid = ' . $current_user->id;

	$query_filter_prefix = calendarview_getSelectedUserFilterQuerySuffix();
	$query .= $query_filter_prefix;

	$startDate = new DateTimeField($start_date.' 00:00');
	$endDate = new DateTimeField($end_date. ' 23:59');
	$params = $info_params = array(
		$startDate->getDBInsertDateTimeValue(), $endDate->getDBInsertDateTimeValue(),
		$startDate->getDBInsertDateTimeValue(), $endDate->getDBInsertDateTimeValue(),
		$startDate->getDBInsertDateTimeValue(), $endDate->getDBInsertDateTimeValue(),
		$startDate->getDBInsertDateTimeValue(), $endDate->getDBInsertDateTimeValue(),
		$startDate->getDBInsertDateTimeValue(), $endDate->getDBInsertDateTimeValue(),
		$startDate->getDBInsertDateTimeValue(), $endDate->getDBInsertDateTimeValue()
	);
	if ($info != '') {
		$groupids = explode(',', fetchUserGroupids($current_user->id)); // Explode can be removed, once implode is removed from fetchUserGroupids
		if (count($groupids) > 0) {
			$com_q = ' AND (vtiger_crmentity.smownerid = ? OR vtiger_groups.groupid in ('. generateQuestionMarks($groupids) .')) GROUP BY vtiger_activity.activityid';
		} else {
			$com_q = ' AND vtiger_crmentity.smownerid = ? GROUP BY vtiger_activity.activityid';
		}

		$pending_query = $query." AND (vtiger_activity.eventstatus = 'Planned')".$com_q;
		$total_q =  $query.$com_q;
		$info_params[] = $current_user->id;

		if (count($groupids) > 0) {
			$info_params[] = $groupids;
		}

		$total_res = $adb->pquery($total_q, $info_params);
		$total = $adb->num_rows($total_res);

		$res = $adb->pquery($pending_query, $info_params);
		$pending_rows = $adb->num_rows($res);
		$cal_log->debug('< getEventList');
		return array('totalevent'=>$total,'pendingevent'=>$pending_rows);
	}
	if (!$userprivs->hasGlobalReadPermission() && !$userprivs->hasModuleReadSharing(getTabid('cbCalendar'))) {
		$sec_parameter=getCalendarViewSecurityParameter();
		$query .= $sec_parameter;
	}
	$group_cond = ' GROUP BY vtiger_activity.activityid ORDER BY vtiger_activity.date_start,vtiger_activity.time_start ASC';

	if (GlobalVariable::getVariable('Application_ListView_Compute_Page_Count', 0)) {
		$count_result = $adb->pquery(mkCountQuery($query), $params);
		$noofrows = $adb->query_result($count_result, 0, 'count');
	} else {
		$noofrows = null;
	}
	$queryMode = (isset($_REQUEST['query']) && $_REQUEST['query'] == 'true');
	//$viewid is used as a key for cache query and other info so pass the dates as viewid
	$viewid = $start_date.$end_date;
	$start = ListViewSession::getRequestCurrentPage($currentModule, $adb->convert2sql($query, $params), $viewid, $queryMode);

	$navigation_array = VT_getSimpleNavigationValues($start, $list_max_entries_per_page, $noofrows);

	$start_rec = ($start-1) * $list_max_entries_per_page;
	$end_rec = $navigation_array['end_val'];

	$list_query = $adb->convert2Sql($query, $params);
	$_SESSION['Calendar_listquery'] = $list_query;

	if ($start_rec < 0) {
		$start_rec = 0;
	}
	$query .= $group_cond." limit $start_rec,$list_max_entries_per_page";

	$result = $adb->pquery($query, $params);
	$rows = $adb->num_rows($result);
	$c = 0;
	if ($start > 1) {
		$c = ($start-1) * $list_max_entries_per_page;
	}
	for ($i=0; $i<$rows; $i++) {
		$element = array();
		$element['no'] = $c+1;
		$image_tag = '';
		$contact_data = '';
		$more_link = '';
		$start_time = $adb->query_result($result, $i, 'time_start');
		$end_time = $adb->query_result($result, $i, 'time_end');
		$date_start = $adb->query_result($result, $i, 'date_start');
		$due_date = $adb->query_result($result, $i, 'due_date');
		$date = new DateTimeField($date_start.' '.$start_time);
		$endDate = new DateTimeField($due_date.' '.$end_time);
		if (!empty($start_time)) {
			$start_time = $date->getDisplayTime();
		}
		if (!empty($end_time)) {
			$end_time = $endDate->getDisplayTime();
		}
		$format = $calendar['calendar']->hour_format;
		$value = getaddEventPopupTime($start_time, $end_time, $format);
		$start_hour = $value['starthour'].':'.$value['startmin'].''.$value['startfmt'];
		$end_hour = $value['endhour'] .':'.$value['endmin'].''.$value['endfmt'];
		$element['starttime'] = $date->getDisplayDate().' '.$start_hour;
		$element['endtime'] = $endDate->getDisplayDate().' '.$end_hour;
		$contact_id = $adb->query_result($result, $i, 'contactid');
		$id = $adb->query_result($result, $i, 'activityid');
		$subject = $adb->query_result($result, $i, 'subject');
		$eventstatus = $adb->query_result($result, $i, 'eventstatus');
		$assignedto = $adb->query_result($result, $i, 'user_name');
		$userid = $adb->query_result($result, $i, 'smownerid');
		$idShared = 'normal';
		if (!empty($assignedto) && $userid != $current_user->id && $adb->query_result($result, $i, 'visibility') == 'Public') {
			$row = $adb->pquery('select * from vtiger_sharedcalendar where sharedid=? and userid=?', array($current_user->id, $userid));
			$no = $adb->getRowCount($row);
			if ($no > 0) {
				$idShared = 'shared';
			} else {
				$idShared = 'normal';
			}
		}
		if ($listview_max_textlength && (strlen($subject) > $listview_max_textlength)) {
			$subject = substr($subject, 0, $listview_max_textlength).'...';
		}
		if ($contact_id != '') {
			$displayValueArray = getEntityName('Contacts', $contact_id);
			if (!empty($displayValueArray)) {
				foreach ($displayValueArray as $field_value) {
					$contactname = $field_value;
				}
			}
			$contact_data = '<b>'.$contactname.'</b>,';
		}
		$more_link = "<a href='index.php?action=DetailView&module=cbCalendar&record=".$id."&activity_mode=Events&viewtype=calendar' class='webMnu'>["
			.$mod_strings['LBL_MORE'].'...]</a>';
		$type = $adb->query_result($result, $i, 'activitytype');
		if ($type == 'Call') {
			$image_tag = "<img src='" . vtiger_imageurl('Calls.gif', $theme). "' align='middle'>&nbsp;".$app_strings['Call'];
		} elseif ($type == 'Meeting') {
			$image_tag = "<img src='" . vtiger_imageurl('Meetings.gif', $theme). "' align='middle'>&nbsp;".$app_strings['Meeting'];
		} else {
			$image_tag = '&nbsp;'.getTranslatedString($type);
		}
		$element['eventtype'] = $image_tag;
		$element['eventdetail'] = $contact_data.' '.$subject.'&nbsp;'.$more_link;
		if ($idShared == 'normal') {
			if (isPermitted('cbCalendar', 'EditView') == 'yes' || isPermitted('cbCalendar', 'Delete') == 'yes') {
				$element['action'] = "<img onClick='getcalAction(this,\"eventcalAction\",".$id.',"'.$calendar['view'].'","'.$calendar['calendar']->date_time->hour.'","'
					.$calendar['calendar']->date_time->get_DB_formatted_date()."\",\"event\");' src='" . vtiger_imageurl('cal_event.jpg', $theme). "' border='0'>";
			}
		} else {
			if (isPermitted('cbCalendar', 'EditView') == 'yes' || isPermitted('cbCalendar', 'Delete') == 'yes') {
				$element['action']="<img onClick=\"alert('".$mod_strings["SHARED_EVENT_DEL_MSG"]."')\"; src='" . vtiger_imageurl('cal_event.jpg', $theme). "' border='0'>";
			}
		}
		if (getFieldVisibilityPermission('Events', $current_user->id, 'eventstatus') == '0') {
			if (!is_admin($current_user) && $eventstatus != '') {
				$roleid=$current_user->roleid;
				$roleids = array();
				$subrole = getRoleSubordinates($roleid);
				if (count($subrole)> 0) {
					$roleids = $subrole;
				}
				$roleids[] = $roleid;

				// check if the table contains the sortorder column .If present in the main picklist table, then the role2picklist will be applicable for this table
				$res = $adb->pquery('select * from vtiger_eventstatus where eventstatus=?', array(decode_html($eventstatus)));
				$picklistvalueid = $adb->query_result($res, 0, 'picklist_valueid');
				if ($picklistvalueid != null) {
					$pick_query="select * from vtiger_role2picklist where picklistvalueid=$picklistvalueid and roleid in (". generateQuestionMarks($roleids) .')';
					$res_val=$adb->pquery($pick_query, array($roleids));
					$num_val = $adb->num_rows($res_val);
				}
				if ($num_val > 0) {
					$element['status'] = getTranslatedString(decode_html($eventstatus));
				} else {
					$element['status'] = "<font color='red'>".$app_strings['LBL_NOT_ACCESSIBLE']."</font>";
				}
			} else {
				$element['status'] = getTranslatedString(decode_html($eventstatus));
			}
		}
		if (!empty($assignedto)) {
			$element['assignedto'] = $assignedto;
		} else {
			$element['assignedto'] = $adb->query_result($result, $i, 'groupname');
		}
		$element['visibility'] = $adb->query_result($result, $i, 'visibility');
		$c++;
		$Entries[] = $element;
	}
	$ret_arr[0] = $Entries;
	$ret_arr[1] = $navigation_array;
	$cal_log->debug('< getEventList');
	return $ret_arr;
}

/**
 * Function to get todos list scheduled between specified dates
 * @param array collection of objects and strings
 * @param date
 * @param date
 * @param string 'listcnt'|empty. if 'listcnt' means it returns no. of todos and no. of pending todos in array format else it returns todos list in array format
 * @return array todolists in array format
 */
function getTodoList(&$calendar, $start_date, $end_date, $info = '') {
	global $app_strings,$theme, $adb, $current_user, $cal_log, $list_max_entries_per_page;
	$cal_log->debug('> getTodoList');
	$Entries = array();
	$crmEntityTable = CRMEntity::getcrmEntityTableAlias('cbCalendar');
	$query = "SELECT vtiger_groups.groupname, vtiger_users.ename as user_name, vtiger_crmentity.crmid, vtiger_cntactivityrel.contactid,vtiger_activity.*
		FROM vtiger_activity
		INNER JOIN ".$crmEntityTable.' ON vtiger_crmentity.crmid = vtiger_activity.activityid
		LEFT JOIN vtiger_cntactivityrel ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid
		LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
		LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid';
	$query .= getNonAdminAccessControlQuery('cbCalendar', $current_user);
	$query .= "WHERE vtiger_crmentity.deleted = 0 AND vtiger_activity.activitytype != 'Emails'".
		" AND ((CAST(CONCAT(date_start,' ',time_start) AS DATETIME) >= ? AND CAST(CONCAT(date_start,' ',time_start) AS DATETIME) <= ?)
				OR	(CAST(CONCAT(due_date,' ',time_end) AS DATETIME) >= ? AND CAST(CONCAT(due_date,' ',time_end) AS DATETIME) <= ? )
				OR	(CAST(CONCAT(date_start,' ',time_start) AS DATETIME) <= ? AND CAST(CONCAT(due_date,' ',time_end) AS DATETIME) >= ?)
			)";

	$list_query = $query.' AND vtiger_crmentity.smownerid = '  . $current_user->id;

	$startDate = new DateTimeField($start_date.' 00:00');
	$endDate = new DateTimeField($end_date. ' 23:59');
	$params = $info_params = array(
		$startDate->getDBInsertDateTimeValue(),
		$endDate->getDBInsertDateTimeValue(),
		$startDate->getDBInsertDateTimeValue(),
		$endDate->getDBInsertDateTimeValue(),
		$startDate->getDBInsertDateTimeValue(),
		$endDate->getDBInsertDateTimeValue()
	);

	if ($info != '') {
		$groupids = explode(',', fetchUserGroupids($current_user->id));
		if (count($groupids) > 0 && !is_admin($current_user)) {
			$com_q = ' AND (vtiger_crmentity.smownerid = ? OR vtiger_groups.groupid in ('. generateQuestionMarks($groupids) .'))';
			$info_params[] = $current_user->id;
			$info_params[] = $groupids;
		} elseif (!is_admin($current_user)) {
			$com_q = ' AND vtiger_crmentity.smownerid = ?';
			$info_params[] = $current_user->id;
		}

		$pending_query = $query." AND (vtiger_activity.status != 'Completed')".$com_q;
		$total_q =  $query.$com_q;
		$total_res = $adb->pquery($total_q, $info_params);
		$total = $adb->num_rows($total_res);
		$res = $adb->pquery($pending_query, $info_params);
		$pending_rows = $adb->num_rows($res);
		$cal_log->debug('< getTodoList');
		return array('totaltodo'=>$total,'pendingtodo'=>$pending_rows);
	}

	$group_cond = '';
	$group_cond .= ' ORDER BY vtiger_activity.date_start,vtiger_activity.time_start ASC';
	if (isset($_REQUEST['start']) && $_REQUEST['start'] != '') {
		$start = vtlib_purify($_REQUEST['start']);
	} else {
		$start = 1;
	}

	if (GlobalVariable::getVariable('Application_ListView_Compute_Page_Count', 0)) {
		$count_res = $adb->pquery(mkCountQuery($query), $params);
		$total_rec_count = $adb->query_result($count_res, 0, 'count');
	} else {
		$total_rec_count = null;
	}

	$navigation_array = VT_getSimpleNavigationValues($start, $list_max_entries_per_page, $total_rec_count);

	$start_rec = ($start-1) * $list_max_entries_per_page;
	$end_rec = $navigation_array['end_val'];

	$list_query = $adb->convert2Sql($query, $params);
	$_SESSION['Calendar_listquery'] = $list_query;

	if ($start_rec < 0) {
		$start_rec = 0;
	}

	$query .= $group_cond." limit $start_rec,$list_max_entries_per_page";

	$result = $adb->pquery($query, $params);
	$rows = $adb->num_rows($result);
	$c=0;
	if ($start > 1) {
		$c = ($start-1) * $list_max_entries_per_page;
	}
	for ($i=0; $i<$rows; $i++) {
		$element = array();
		$element['no'] = $c+1;
		$more_link = '';
		$start_time = $adb->query_result($result, $i, 'time_start');
		$date_start = $adb->query_result($result, $i, 'date_start');
		$due_date = $adb->query_result($result, $i, 'due_date');
		$date = new DateTimeField($date_start.' '.$start_time);
		$endDate = new DateTimeField($due_date);
		if (!empty($start_time)) {
			$start_time = $date->getDisplayTime();
		}
		$format = $calendar['calendar']->hour_format;
		$value = getaddEventPopupTime($start_time, $start_time, $format);
		$element['starttime'] = $value['starthour'].':'.$value['startmin'].''.$value['startfmt'];
		$element['startdate'] = $date->getDisplayDate();
		$element['duedate'] = $endDate->getDisplayDate();

		$id = $adb->query_result($result, $i, 'activityid');
		$subject = $adb->query_result($result, $i, 'subject');
		$more_link = "<a href='index.php?action=DetailView&module=cbCalendar&record=".$id."&activity_mode=Task&viewtype=calendar' class='webMnu'>".$subject."</a>";
		$element['tododetail'] = $more_link;
		if (getFieldVisibilityPermission('cbCalendar', $current_user->id, 'taskstatus') == '0') {
			$taskstatus = $adb->query_result($result, $i, 'status');

			if (!is_admin($current_user) && $taskstatus != '') {
				$roleid=$current_user->roleid;
				$roleids = array();
				$subrole = getRoleSubordinates($roleid);
				if (count($subrole)> 0) {
					$roleids = $subrole;
				}
				$roleids[] = $roleid;

				// check if the table contains the sortorder column .If present in the main picklist table, then the role2picklist will be applicable for this table
				$res = $adb->pquery('select * from vtiger_taskstatus where taskstatus=?', array(decode_html($taskstatus)));
				$picklistvalueid = $adb->query_result($res, 0, 'picklist_valueid');
				if ($picklistvalueid != null) {
					$pick_query="select * from vtiger_role2picklist where picklistvalueid=$picklistvalueid and roleid in (". generateQuestionMarks($roleids) .')';
					$res_val=$adb->pquery($pick_query, array($roleids));
					$num_val = $adb->num_rows($res_val);
				}
				if ($num_val > 0) {
					$element['status'] = getTranslatedString(decode_html($taskstatus));
				} else {
					$element['status'] = "<font color='red'>".$app_strings['LBL_NOT_ACCESSIBLE']."</font>";
				}
			} else {
				$element['status'] = getTranslatedString(decode_html($taskstatus));
			}
		}
		if (isPermitted('cbCalendar', 'EditView') == 'yes' || isPermitted('cbCalendar', 'Delete') == 'yes') {
			$element['action'] = "<img onClick='getcalAction(this,\"taskcalAction\",".$id.',"'.$calendar['view'].'","'.$calendar['calendar']->date_time->hour.'","'
				.$calendar['calendar']->date_time->get_DB_formatted_date().'","todo");\' src=\'' . vtiger_imageurl('cal_event.jpg', $theme). "' border='0'>";
		}
		$assignedto = $adb->query_result($result, $i, 'user_name');
		if (!empty($assignedto)) {
			$element['assignedto'] = $assignedto;
		} else {
			$element['assignedto'] = $adb->query_result($result, $i, 'groupname');
		}
		$c++;
		$Entries[] = $element;
	}
	$ret_arr[0] = $Entries;
		$ret_arr[1] = $navigation_array;
	$cal_log->debug('< getTodoList');
	return $ret_arr;
}

/**
 * Function to get number of calendar information
 * @param array collection of objects and strings
 * @param string 'listcnt' or empty. if empty means get Calendar ListView else get total number of events and pending calendar information
 * @return array collection of calendar information
 */
function getEventInfo(&$cal, $mode) {
	global $mod_strings,$cal_log;
	$cal_log->debug('> getEventInfo');
	$event = array();
	$event['event']=getEventListView($cal, $mode);
	$event_info = '';
	$event_info .= $mod_strings['LBL_TOTALEVENTS'].'&nbsp;'.$event['event']['totalevent'];
	if ($event['event']['pendingevent'] != null) {
		$event_info .= ', '.$event['event']['pendingevent'].'&nbsp;'.$mod_strings['LBL_PENDING'];
	}
	$cal_log->debug('< getEventInfo');
	return $event_info;
}

function getTodoInfo(&$cal, $mode) {
	global $mod_strings,$cal_log;
	$cal_log->debug('> getTodoInfo');
	$todo = array();
	$todo['todo'] = getTodosListView($cal, $mode);
	$todo_info =$mod_strings['LBL_TOTALTODOS'].'&nbsp;'.$todo['todo']['totaltodo'];
	if ($todo['todo']['pendingtodo'] != null) {
		$todo_info .= ', '.$todo['todo']['pendingtodo'].'&nbsp;'.$mod_strings['LBL_PENDING'];
	}
	$cal_log->debug('< getTodoInfo');
	return $todo_info;
}

/**
 * Function creates HTML to display Events ListView
 * @param array  $entry_list    - collection of strings(Event Information)
 * @return string $list_view     - html tags in string format
 */
function constructEventListView(&$cal, $entry_list, $navigation_array = '') {
	global $mod_strings,$app_strings,$adb,$cal_log,$current_user,$theme;
	$cal_log->debug('> constructEventListView');
	$format = $cal['calendar']->hour_format;
	$date = new DateTimeField(null);
	$endDate = new DateTimeField(date('Y-m-d H:i:s', (time() + (1 * 24 * 60 * 60))));
	$hour_startat = $date->getDisplayTime();
	$hour_endat = $endDate->getDisplayTime();
	$time_arr = getaddEventPopupTime($hour_startat, $hour_endat, $format);
	$temp_ts = $cal['calendar']->date_time->ts;
	//to get date in user selected date format
	$temp_date = $date->getDisplayDate();
	if ($cal['calendar']->day_start_hour != 23) {
		$endtemp_date = $temp_date;
	} else {
		$endtemp_date = $endDate->getDisplayDate();
	}
	$list_view = '';
	$start_datetime = $app_strings['LBL_START_DATE_TIME'];
	$end_datetime = $app_strings['LBL_END_DATE_TIME'];
	//Events listview header labels
	$header = array(
		'0'=>'#',
		'1'=>$start_datetime,
		'2'=>$end_datetime,
		'3'=>$mod_strings['LBL_EVENTTYPE'],
		'4'=>$mod_strings['LBL_EVENTDETAILS']
	);
	$header_width = array(
		'0'=>'5%',
		'1'=>'15%',
		'2'=>'15%',
		'3'=>'10%',
		'4'=>'33%'
	);
	if (isPermitted('cbCalendar', 'EditView') == 'yes' || isPermitted('cbCalendar', 'Delete') == 'yes') {
		$header[] = $mod_strings['LBL_ACTION'];
		$header_width[] = '10%';
	}
	if (getFieldVisibilityPermission('Events', $current_user->id, 'eventstatus') == '0') {
		$header[] = $mod_strings['LBL_STATUS'];
		$header_width[] = '$10%';
	}
	$header[] = $mod_strings['LBL_ASSINGEDTO'];
	$header_width[] = '15%';

	$list_view .="<table style='background-color: rgb(204, 204, 204);' class='small' align='center' border='0' cellpadding='5' cellspacing='1' width='98%'><tr>";
	$header_rows = count($header);

	$navigationOutput = getTableHeaderSimpleNavigation($navigation_array, '', 'cbCalendar', 'index');

	if ($navigationOutput != '') {
		$list_view .= "<tr width=100% bgcolor=white><td align=center colspan=$header_rows>";
		$list_view .= "<table align=center width='98%'><tr>".$navigationOutput.'</tr></table></td></tr>';
	}
	$list_view .= '<tr>';
	for ($i=0; $i<$header_rows; $i++) {
		$list_view .="<td nowrap='nowrap' class='lvtCol' width='".$header_width[$i]."'>".$header[$i].'</td>';
	}
	$list_view .='</tr>';
	$rows = count($entry_list);
	if ($rows != 0) {
		$userName = getFullNameFromArray('Users', $current_user->column_fields);

		for ($i=0; $i<count($entry_list); $i++) {
			$list_view .="<tr class='lvtColData' onmouseover='this.className=\"lvtColDataHover\"' onmouseout='this.className=\"lvtColData\"' bgcolor='white'>";

			$assigned_role_query=$adb->pquery(
				"select vtiger_user2role.roleid,vtiger_user2role.userid
					from vtiger_user2role
					INNER JOIN vtiger_users ON vtiger_users.id=vtiger_user2role.userid
					WHERE vtiger_users.ename=?",
				array($entry_list[$i]['assignedto'])
			);
			$assigned_user_role_id = $adb->query_result($assigned_role_query, 0, 'roleid');
			$assigned_user_id = $adb->query_result($assigned_role_query, 0, 'userid');
			$role_list = $adb->pquery(
				"SELECT * from vtiger_role WHERE parentrole LIKE '".formatForSqlLike($current_user->column_fields['roleid']).formatForSqlLike($assigned_user_role_id)."'",
				array()
			);
			$is_shared = $adb->pquery('SELECT * from vtiger_sharedcalendar where userid=? and sharedid=?', array($assigned_user_id,$current_user->id));

			foreach ($entry_list[$i] as $key => $entry) {
				if ($key!='visibility') {
					if (($key=='eventdetail'|| $key=='action')
						&& ($current_user->column_fields['is_admin']!='on'
						&& $adb->num_rows($role_list)==0
						&& ($adb->num_rows($is_shared)==0  || $entry_list[$i]['visibility']=='Private'))
						&& $userName!=$entry_list[$i]['assignedto']
					) {
						if ($key=='eventdetail') {
							$list_view .="<td nowrap='nowrap'><font color='red'><b>".$entry_list[$i]['assignedto'].' - '.$mod_strings['LBL_BUSY'].'</b></font></td>';
						} else {
							$list_view .="<td nowrap='nowrap'><font color='red'>".$app_strings['LBL_NOT_ACCESSIBLE'].'</font></td>';
						}
					} else {
						$list_view .="<td nowrap='nowrap'>$entry</td>";
					}
				}
			}
			$list_view .='</tr>';
		}
	} else {
		$list_view .="<tr><td style='background-color:#efefef;height:340px' align='center' colspan='9'>";
			$list_view .="<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 45%; position: relative; z-index: 5000;'>
				<table border='0' cellpadding='5' cellspacing='0' width='98%'>
					<tr>
						<td rowspan='2' width='25%'>
							<img src='" . vtiger_imageurl('empty.jpg', $theme). "' height='60' width='61'></td>
						<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='75%'><span class='genHeaderSmall'>".$app_strings['LBL_NO'].' '
						.$app_strings['Events'].' '.$app_strings['LBL_FOUND'].' !</span></td>
					</tr>
					<tr>';
			//checking permission for Create/Edit Operation
		if (isPermitted('cbCalendar', 'EditView') == 'yes') {
			$list_view .="<td class='small' align='left' nowrap='nowrap'>".$app_strings['LBL_YOU_CAN_CREATE'].'&nbsp;'.$app_strings['LBL_AN'].'&nbsp;'
				.$app_strings['Event'].'&nbsp;'.$app_strings['LBL_NOW'].".&nbsp;".$app_strings['LBL_CLICK_THE_LINK'].":<br>&nbsp;&nbsp;-
				<a href='javascript:void(0);' onClick='gshow(\"addEvent\",\"Call\",\"".$temp_date.'","'.$endtemp_date.'","'.$time_arr['starthour'].'","'
				.$time_arr['startmin'].'","'.$time_arr['startfmt'].'","'.$time_arr['endhour'].'","'.$time_arr['endmin'].'","'.$time_arr['endfmt']
				.'","listview","event");\'>'.$app_strings['LBL_CREATE'].'&nbsp;'.$app_strings['LBL_AN'].'&nbsp;'.$app_strings['Event'].'</a><br></td>';
		} else {
			$list_view .="<td class='small' align='left' nowrap='nowrap'>".$app_strings['LBL_YOU_ARE_NOT_ALLOWED_TO_CREATE'].'&nbsp;'.$app_strings['LBL_AN']
				.'&nbsp;'.$app_strings['Event'].'<br></td>';
		}
		$list_view .='</tr>
			</table>
			</div>';
		$list_view .='</td></tr>';
	}
	$list_view .='</table>';
	$cal_log->debug('< constructEventListView');
	return $list_view;
}

/**
 * Function creates HTML to display Todos ListView
 * @param array collection of strings (Task Information)
 * @param array collection of objects and strings
 * @return string html tags in string format
 */
function constructTodoListView($todo_list, $cal, $subtab, $navigation_array = '') {
	global $mod_strings,$cal_log,$adb,$theme;
	$cal_log->debug('> constructTodoListView');
	global $current_user,$app_strings;
	$format = $cal['calendar']->hour_format;
	$date = new DateTimeField(null);
	$endDate = new DateTimeField(date('Y-m-d H:i:s', (time() + (1 * 24 * 60 * 60))));
	$hour_startat = $date->getDisplayTime();
	$hour_endat = $endDate->getDisplayTime();

	$time_arr = getaddEventPopupTime($hour_startat, $hour_endat, $format);
	//to get date in user selected date format
	$temp_date = $date->getDisplayDate();
	if ($cal['calendar']->day_start_hour != 23) {
		$endtemp_date = $temp_date;
	} else {
		$endtemp_date = $endDate->getDisplayDate();
	}
	$list_view = '';
	//labels of listview header
	if ($cal['view'] == 'day') {
		$colspan = 9;
		$header = array(
			'0'=>'#','1'=>$mod_strings['LBL_TIME'],
			'2'=>$mod_strings['LBL_START_DATE'],
			'3'=>$mod_strings['LBL_DUE_DATE'],
			'4'=>$mod_strings['LBL_TODO']
		);
		$header_width = array('0'=>'5%','1'=>'10%','2'=>'10%','3'=>'38%',);
		if (getFieldVisibilityPermission('cbCalendar', $current_user->id, 'taskstatus') == '0') {
			$header[] = $mod_strings['LBL_STATUS'];
			$header_width[] = '10%';
		}

		if (isPermitted('cbCalendar', 'EditView') == 'yes' || isPermitted('cbCalendar', 'Delete') == 'yes') {
			$header[] = $mod_strings['LBL_ACTION'];
			$header_width[] = '10%';
		}
		$header[] = $mod_strings['LBL_ASSINGEDTO'];
		$header_width[] = '15%';
	} else {
		$colspan = 10;
			$header = array(
				'0'=>'#',
				'1'=>$mod_strings['LBL_TIME'],
				'2'=>$mod_strings['LBL_START_DATE'],
				'3'=>$mod_strings['LBL_DUE_DATE'],
				'4'=>$mod_strings['LBL_TODO']
			);
			$header_width = array(
				'0'=>'5%',
				'1'=>'10%',
				'2'=>'10%',
				'3'=>'10%',
				'4'=>'28%'
			);
			if (getFieldVisibilityPermission('cbCalendar', $current_user->id, 'taskstatus') == '0') {
				$header[] = $mod_strings['LBL_STATUS'];
				$header_width[] = '10%';
			}
			if (isPermitted('cbCalendar', 'EditView') == 'yes' || isPermitted('cbCalendar', 'Delete') == 'yes') {
				$header[] = $mod_strings['LBL_ACTION'];
			}
			$header[] = $mod_strings['LBL_ASSINGEDTO'];
			$header_width[] = '15%';
	}
	if ($current_user->column_fields['is_admin']=='on') {
		$Res = $adb->pquery('select * from vtiger_activitytype', array());
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
			$Res=$adb->pquery(
				'select distinct activitytype
					from vtiger_activitytype
					inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid=vtiger_activitytype.picklist_valueid
					where roleid in ('. generateQuestionMarks($roleids) .') and picklistid in (select picklistid from vtiger_picklist) order by sortid asc',
				array($roleids)
			);
		} else {
			$Res=$adb->pquery(
				'select distinct activitytype
					from vtiger_activitytype
					inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid=vtiger_activitytype.picklist_valueid
					where roleid=? and picklistid in (select picklistid from vtiger_picklist) order by sortid asc',
				array($roleid)
			);
		}
	}
	$eventlist='';
	for ($i=0; $i<$adb->num_rows($Res); $i++) {
		$eventlist .= $adb->query_result($Res, $i, 'activitytype').';';
	}

	$list_view .="<table align='center' border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tr><td colspan='3'>&nbsp;</td></tr>";
		//checking permission for Create/Edit Operation
	if (isPermitted('cbCalendar', 'CreateView') == 'yes') {
		$list_view .="<tr>
			<td class='calAddButton' onMouseOver='fnAddEvent(this,\"addEventDropDown\",\"".$temp_date.'","'.$endtemp_date.'","'.$time_arr['starthour'].'","'
			.$time_arr['startmin'].'","'.$time_arr['startfmt'].'","'.$time_arr['endhour'].'","'.$time_arr['endmin'].'","'.$time_arr['endfmt'].'","","'.$subtab.'","'
			.$eventlist."\");'style='border: 1px solid #666666;cursor:pointer;height:30px' align='center' width='10%'>".
		$mod_strings['LBL_ADD']."<img src='".vtiger_imageurl('menuDnArrow.gif', $theme)."' style='padding-left: 5px;' border='0'></td>";
	} else {
		$list_view .='<tr><td>&nbsp;</td>';
	}
	$list_view .="<td align='center' width='60%'><span  id='total_activities'>".getTodoInfo($cal, 'listcnt')."</span>&nbsp;</td>
				<td align='right' width='28%'>&nbsp;</td>
			</tr>
		</table>
		<br><table style='background-color: rgb(204, 204, 204);' class='small' align='center' border='0' cellpadding='5' cellspacing='1' width='98%'>";
	$header_rows = count($header);
	$navigationOutput = getTableHeaderSimpleNavigation($navigation_array, '', 'cbCalendar', 'index');

	if ($navigationOutput != '') {
		$list_view .= "<tr width=100% bgcolor=white><td align=center colspan=$header_rows>";
		$list_view .= "<table align=center width='98%'><tr>".$navigationOutput.'</tr></table></td></tr>';
	}
	$list_view .= '<tr>';
	for ($i=0; $i<$header_rows; $i++) {
		$list_view .="<td class='lvtCol' width='".$header_width[$i]."' nowrap='nowrap'>".$header[$i].'</td>';
	}
	$list_view .='</tr>';
	$rows = count($todo_list);
	if ($rows != 0) {
		for ($i=0; $i<count($todo_list); $i++) {
			$list_view .="<tr style='height: 25px;' bgcolor='white'>";
			foreach ($todo_list[$i] as $entry) {
				$list_view .='<td>'.$entry.'</td>';
			}
			$list_view .='</tr>';
		}
	} else {
		$list_view .="<tr><td style='background-color:#efefef;height:340px' align='center' colspan='".$colspan."'>";
		$list_view .="<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 45%; position: relative; z-index: 5000;'>
			<table border='0' cellpadding='5' cellspacing='0' width='98%'>
			<tr>
				<td rowspan='2' width='25%'>
					<img src='" . vtiger_imageurl('empty.jpg', $theme). "' height='60' width='61'></td>
				<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='75%'><span class='genHeaderSmall'>".$app_strings['LBL_NO'].' '
				.$app_strings['Todos'].' '.$app_strings['LBL_FOUND'].' !</span></td>
			</tr>
			<tr>';
		//checking permission for Create/Edit Operation
		if (isPermitted('cbCalendar', 'CreateView') == 'yes') {
			$list_view .="<td class='small' align='left' nowrap='nowrap'>".$app_strings['LBL_YOU_CAN_CREATE'].'&nbsp;'.$app_strings['LBL_A'].'&nbsp;'.$app_strings['Todo']
				.'&nbsp;'.$app_strings['LBL_NOW'].'.&nbsp;'.$app_strings['LBL_CLICK_THE_LINK']."&nbsp;:<br>&nbsp;&nbsp;-
				<a href='javascript:void(0);' onClick='gshow(\"createTodo\",\"todo\",\"".$temp_date.'","'.$temp_date.'","'.$time_arr['starthour'].'","'.
				$time_arr['startmin'].'","'.$time_arr['startfmt'].'","'.$time_arr['endhour'].'","'.$time_arr['endmin'].'","'
				.$time_arr['endfmt'].'","listview","todo");\'>'.$app_strings['LBL_CREATE'].' '.$app_strings['LBL_A'].' '.$app_strings['Todo'].'</a></td>';
		} else {
			$list_view .="<td class='small' align='left' nowrap='nowrap'>".$app_strings['LBL_YOU_ARE_NOT_ALLOWED_TO_CREATE'].'&nbsp;'.$app_strings['LBL_A']
				.'&nbsp;'.$app_strings['Todo'].'<br></td>';
		}
		$list_view .='</tr>
			</table>
			</div>';
		$list_view .='</td></tr>';
	}
	$list_view .='</table><br>';
	$cal_log->debug('< constructTodoListView');
	return $list_view;
}

/**
 * Function returns the list of privileges and permissions of the events that the current user can view the details of.
 * @return string - query that is used as secondary parameter to fetch the events that the user can view and the schedule of the users
 */
function getCalendarViewSecurityParameter() {
	global $current_user;
	$userprivs = $current_user->getPrivileges();
	$shared_ids = getSharedCalendarId($current_user->id);
	if (isset($shared_ids) && $shared_ids != '') {
		$condition = " or (vtiger_crmentity.smownerid in ($shared_ids)) or (vtiger_crmentity.smownerid!=$current_user->id)"; // and vtiger_activity.visibility='Public')
	} else {
		$condition = " or (vtiger_crmentity.smownerid!=$current_user->id)";
	}
	$sec_query = " and (vtiger_crmentity.smownerid=$current_user->id $condition or vtiger_crmentity.smownerid in ";
	$sec_query.= "(select vtiger_user2role.userid
		from vtiger_user2role
		inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid
		where vtiger_role.parentrole like '".$userprivs->getParentRoleSequence()."::%')";

	if ($userprivs->hasGroups()) {
		$sec_query .= ' or (vtiger_groups.groupid in ('. implode(',', $userprivs->getGroups()) .'))';
	}
	$sec_query .= ')';
	return $sec_query;
}
?>
