<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************/
interface VTExpressionEnv{
	function get($var);
}

function __vt_add($arr){
	if(sizeof($arr)==1){
		return $arr[0];
	}else{
		return $arr[0]+$arr[1];
	}
}

function __vt_sub($arr){
	if(sizeof($arr)==1){
		return -$arr[0];
	}else{
		return $arr[0]-$arr[1];
	}
}

function __vt_mul($arr){
	return $arr[0]*$arr[1];
}

function __vt_div($arr){
	try {
	return $arr[0]/$arr[1];
	}catch(Exception $e) {
		return 0;
	}
}

function __vt_equals($arr){
	return $arr[0] == $arr[1];
}

function __cb_distinct($arr){
	return $arr[0] != $arr[1];
}

function __vt_ltequals($arr) {
	return $arr[0] <= $arr[1];
}

function __vt_gtequals($arr) {
	return $arr[0] >= $arr[1];
}

function __vt_lt($arr) {
	return $arr[0] < $arr[1];
}

function __vt_gt($arr) {
	return $arr[0] > $arr[1];
}

function __vt_concat($arr){
	return implode($arr);
}

/* Date difference between (input times) or (current time and input time)
 *
 * @param Array $a $a[0] - Input time1, $a[1] - Input time2
 * (if $a[1] is not available $a[0] = Current Time, $a[1] = Input time1)
 * @return int difference timestamp
 */
function __vt_time_diff($arr) {
	$time_operand1 = $time_operand2 = 0;
	if(count($arr) > 1) {
		$time_operand1 = $time1 = $arr[0];
		$time_operand2 = $time2 = $arr[1];
	} else {
		// Added as we need to compare with the values based on the user date format and timezone
		global $default_timezone;
		$time_operand1 = date('Y-m-d H:i:s'); // Current time
		$time_operand2 = $arr[0];
	}

	if(empty($time_operand1) || empty($time_operand2)) return 0;

	$time_operand1 = getValidDBInsertDateTimeValue($time_operand1);
	$time_operand2 = getValidDBInsertDateTimeValue($time_operand2);

	//to give the difference if it is only time field
	if(empty($time_operand1) && empty($time_operand2)) {
		$pattern = "/([01]?[0-9]|2[0-3]):[0-5][0-9]/";
		if(preg_match($pattern, $time1) && preg_match($pattern, $time2)){
			$timeDiff = strtotime($time1) - strtotime($time2);
			return date('H:i:s', $timeDiff);
		}
	}
	return (strtotime($time_operand1) - strtotime($time_operand2));
}

/**
 * Calculate the time difference (input times) or (current time and input time) and
 * convert it into number of days.
 * @param Array $a $a[0] - Input time1, $a[1] - Input time2
 * (if $a[1] is not available $a[0] = Current Time, $a[1] = Input time1)
 * @return int number of days
 */
function __vt_time_diffdays($arr) {
	$timediff  = __vt_time_diff($arr);
	$days_diff = floor($timediff / (60 * 60 * 24));
	return $days_diff;
}

function __vt_add_days($arr) {
	if (count($arr) > 1) {
		$baseDate = $arr[0];
		$noOfDays = $arr[1];
	} else {
		$noOfDays = $arr[0];
	}
	if(empty($baseDate)) {
		$baseDate = date('Y-m-d'); // Current date
	}
	preg_match('/\d\d\d\d-\d\d-\d\d/', $baseDate, $match);
	$baseDate = strtotime($match[0]);
	$date = strftime('%Y-%m-%d', $baseDate + ($noOfDays * 24 * 60 * 60));
	return $date;
}

function __vt_sub_days($arr) {
	if (count($arr) > 1) {
		$baseDate = $arr[0];
		$noOfDays = $arr[1];
	} else {
		$noOfDays = $arr[0];
	}
	if(empty($baseDate)) {
		$baseDate = date('Y-m-d'); // Current date
	}
	preg_match('/\d\d\d\d-\d\d-\d\d/', $baseDate, $match);
	$baseDate = strtotime($match[0]);
	$date = strftime('%Y-%m-%d', $baseDate - ($noOfDays * 24 * 60 * 60));
	return $date;
}

function __vt_get_date($arr) {
	$type = $arr[0];
	switch ($type) {
		case 'today': return date('Y-m-d');
			break;
		case 'tomorrow': return date('Y-m-d', strtotime('+1 day'));
			break;
		case 'yesterday': return date('Y-m-d', strtotime('-1 day'));
			break;
		case 'time': global $default_timezone;
			return date('H:i:s');
			break;
		default : return date('Y-m-d');
			break;
	}
}

function __cb_format_date($arr) {
	$fmt = empty($arr[1]) ? 'Y-m-d' : $arr[1];
	list($y,$m,$d) = explode('-', $arr[0]);
	$dt = mktime(0,0,0,$m,$d,$y);
	return date($fmt,$dt);
}

function __vt_substring($arr) {
	if (count($arr)<2 or count($arr)>3) return $arr[0];
	if (count($arr)==2) {
		return substr($arr[0],$arr[1]);
	} else {
		return substr($arr[0],$arr[1],$arr[2]);
	}
}

function __vt_uppercase($arr) {
	if (count($arr)==0) {
		return '';
	} else {
		return strtoupper($arr[0]);
	}
}

function __vt_lowercase($arr) {
	if (count($arr)==0) {
		return '';
	} else {
		return strtolower($arr[0]);
	}
}

function __vt_uppercasefirst($arr) {
	if (count($arr)==0) {
		return '';
	} else {
		return ucfirst($arr[0]);
	}
}

function __vt_uppercasewords($arr) {
	if (count($arr)==0) {
		return '';
	} else {
		return ucwords($arr[0]);
	}
}

function __vt_round($arr) {
	if (count($arr)<1 or count($arr)>2) return $arr[0];
	$decs = (isset($arr[1]) ? $arr[1] : 0);
	if (is_numeric($arr[0]) and is_numeric($decs)) {
		return round($arr[0],$decs);
	} else {
		return $arr[0];
	}
}
function __vt_ceil($num) {
	if (is_numeric($num[0])) {
		return ceil($num[0]);
	} else {
		return 0;
	}
}
function __vt_floor($num) {
	if (is_numeric($num[0])) {
		return floor($num[0]);
	} else {
		return 0;
	}
}

function __vt_add_time($arr) {
	if(count($arr) > 1) {
		$baseTime = $arr[0];
		$minutes = $arr[1];
	} else {
		$baseTime = date('H:i:s');
		$minutes = $arr[0];
	}
	$endTime = strtotime("+$minutes minutes", strtotime($baseTime));
	return date('H:i:s',$endTime);
}

function __vt_sub_time($arr) {
	if(count($arr) > 1) {
		$baseTime = $arr[0];
		$minutes = $arr[1];
	} else {
		$baseTime = date('H:i:s');
		$minutes = $arr[0];
	}
	$endTime = strtotime("-$minutes minutes", strtotime($baseTime));
	return date('H:i:s',$endTime);
}

function __cb_getLatitude($address) {
	$addr = urlencode($address);
	$data = file_get_contents("http://nominatim.openstreetmap.org/?format=json&addressdetails=1&q=$addr&format=json&limit=1");
	$data = json_decode($data);
	return $data[0]->lat;
}

function __cb_getLongitude($address) {
	$addr = urlencode($address);
	$data = file_get_contents("http://nominatim.openstreetmap.org/?format=json&addressdetails=1&q=$addr&format=json&limit=1");
	$data = json_decode($data);
	return $data[0]->lon;
}

function __cb_getGEODistance($arr){
	$from = decode_html($arr[0]);
	$to = decode_html($arr[1]);
	$coo1 = __cb_getLatitude($from).",".__cb_getLongitude($from);
	$coo2 = __cb_getLatitude($to).",".__cb_getLongitude($to);
	$distance = file_get_contents("http://router.project-osrm.org/viaroute?loc=$coo1&loc=$coo2");
	$dis = json_decode($distance);
	$total_distance = $dis->route_summary->total_distance/1000;
	return $total_distance." km";
}

function __cb_getGEODistanceFromCompanyAddress($arr){
	global $adb;
	$compAdr = $adb->query('Select address,city,state,country,code from vtiger_organizationdetails');
	$from = $adb->query_result($compAdr, 0, 'country');
	$fld = $adb->query_result($compAdr, 0, 'state');
	$from.= empty($fld) ? '':', '.$fld;
	$fld = $adb->query_result($compAdr, 0, 'city');
	$from.= empty($fld) ? '':', '.$fld;
	$fld = $adb->query_result($compAdr, 0, 'code');
	$from.= empty($fld) ? '':', '.$fld;
	$fld = $adb->query_result($compAdr, 0, 'address');
	$from.= empty($fld) ? '':', '.$fld;	
	$array = array($from, $arr[0]);
	return __cb_getGEODistance($array);
}

function __cb_getGEODistanceFromUserAddress($arr){
	$from = __cb_getCurrentUserAddress();
	$array = array($from, $arr[0]);
	return __cb_getGEODistance($array);
}

function __cb_getCurrentUserAddress($userid = ''){
	global $adb,$current_user;
	if($userid == '')
		$userid = $current_user->id;
	else
		list($wsid,$userid) = explode('x', $userid);
	$compAdr = $adb->pquery('Select address_street,address_city,address_state,address_country,address_postalcode
		from vtiger_users where id=?',array($userid));
	$from = $adb->query_result($compAdr, 0, 'address_country');
	$fld = $adb->query_result($compAdr, 0, 'address_state');
	$from.= empty($fld) ? '':', '.$fld;
	$fld = $adb->query_result($compAdr, 0, 'address_city');
	$from.= empty($fld) ? '':', '.$fld;
	$fld = $adb->query_result($compAdr, 0, 'address_postalcode');
	$from.= empty($fld) ? '':', '.$fld;
	$fld = $adb->query_result($compAdr, 0, 'address_street');
	$from.= empty($fld) ? '':', '.$fld;
	return $from;
}

function __cb_getGEODistanceFromUser2AccountBilling($arr){
	global $adb,$current_user;
	$accid = $arr[0];
	if (empty($accid)) return '0';
	list($wsid,$accid) = explode('x', $accid);
	$compAdr = $adb->pquery('Select bill_street,bill_city,bill_state,bill_country,bill_code
		from vtiger_accountbillads
		where accountaddressid=?',array($accid));
	if ($compAdr and $adb->num_rows($compAdr)>0) {
		$to = $adb->query_result($compAdr, 0, 'bill_country');
		$fld = $adb->query_result($compAdr, 0, 'bill_state');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'bill_city');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'bill_code');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'bill_street');
		$to.= empty($fld) ? '':', '.$fld;
	} else {
		return '0';
	}
	$from = __cb_getCurrentUserAddress();
	$array = array($from, $to);
	return __cb_getGEODistance($array);
}

function __cb_getGEODistanceFromAssignUser2AccountBilling($arr){
	global $adb,$current_user;
	$accid = $arr[0];
	$userid = $arr[1];
	if (empty($accid)) return '0';
	list($wsid,$accid) = explode('x', $accid);
	$compAdr = $adb->pquery('Select bill_street,bill_city,bill_state,bill_country,bill_code
		from vtiger_accountbillads
		where accountaddressid=?',array($accid));
	if ($compAdr and $adb->num_rows($compAdr)>0) {
		$to = $adb->query_result($compAdr, 0, 'bill_country');
		$fld = $adb->query_result($compAdr, 0, 'bill_state');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'bill_city');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'bill_code');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'bill_street');
		$to.= empty($fld) ? '':', '.$fld;
	} else {
		return '0';
	}
	$from = __cb_getCurrentUserAddress($userid);
	$array = array($from, $to);
	return __cb_getGEODistance($array);
}

function __cb_getGEODistanceFromUser2AccountShipping($arr){
	global $adb,$current_user;
	$accid = $arr[0];
	if (empty($accid)) return '0';
	list($wsid,$accid) = explode('x', $accid);
	$compAdr = $adb->pquery('Select ship_street,ship_city,ship_state,ship_country,ship_code
		from vtiger_accountshipads
		where accountaddressid=?',array($accid));
	if ($compAdr and $adb->num_rows($compAdr)>0) {
		$to = $adb->query_result($compAdr, 0, 'ship_country');
		$fld = $adb->query_result($compAdr, 0, 'ship_state');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'ship_city');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'ship_code');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'ship_street');
		$to.= empty($fld) ? '':', '.$fld;
	} else {
		return '0';
	}
	$from = __cb_getCurrentUserAddress();
	$array = array($from, $to);
	return __cb_getGEODistance($array);
}

function __cb_getGEODistanceFromAssignUser2AccountShipping($arr){
	global $adb,$current_user;
	$accid = $arr[0];
	$userid = $arr[1];
	if (empty($accid)) return '0';
	list($wsid,$accid) = explode('x', $accid);
	$compAdr = $adb->pquery('Select ship_street,ship_city,ship_state,ship_country,ship_code
		from vtiger_accountshipads
		where accountaddressid=?',array($accid));
	if ($compAdr and $adb->num_rows($compAdr)>0) {
		$to = $adb->query_result($compAdr, 0, 'ship_country');
		$fld = $adb->query_result($compAdr, 0, 'ship_state');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'ship_city');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'ship_code');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'ship_street');
		$to.= empty($fld) ? '':', '.$fld;
	} else {
		return '0';
	}
	$from = __cb_getCurrentUserAddress($userid);
	$array = array($from, $to);
	return __cb_getGEODistance($array);
}

function __cb_getGEODistanceFromUser2ContactBilling($arr){
	global $adb,$current_user;
	$ctoid = $arr[0];
	if (empty($ctoid)) return '0';
	list($wsid,$ctoid) = explode('x', $ctoid);
	$compAdr = $adb->pquery('Select mailingstreet,mailingcity,mailingstate,mailingcountry,mailingzip
		from vtiger_contactaddress
		where contactaddressid=?',array($ctoid));
	if ($compAdr and $adb->num_rows($compAdr)>0) {
		$to = $adb->query_result($compAdr, 0, 'mailingcountry');
		$fld = $adb->query_result($compAdr, 0, 'mailingstate');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'mailingcity');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'mailingzip');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'mailingstreet');
		$to.= empty($fld) ? '':', '.$fld;
	} else {
		return '0';
	}
	$from = __cb_getCurrentUserAddress();
	$array = array($from, $to);
	return __cb_getGEODistance($array);
}

function __cb_getGEODistanceFromAssignUser2ContactBilling($arr){
	global $adb,$current_user;
	$ctoid = $arr[0];
	$userid = $arr[1];
	if (empty($ctoid)) return '0';
	list($wsid,$ctoid) = explode('x', $ctoid);
	$compAdr = $adb->pquery('Select mailingstreet,mailingcity,mailingstate,mailingcountry,mailingzip
		from vtiger_contactaddress
		where contactaddressid=?',array($ctoid));
	if ($compAdr and $adb->num_rows($compAdr)>0) {
		$to = $adb->query_result($compAdr, 0, 'mailingcountry');
		$fld = $adb->query_result($compAdr, 0, 'mailingstate');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'mailingcity');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'mailingzip');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'mailingstreet');
		$to.= empty($fld) ? '':', '.$fld;
	} else {
		return '0';
	}
	$from = __cb_getCurrentUserAddress($userid);
	$array = array($from, $to);
	return __cb_getGEODistance($array);
}

function __cb_getGEODistanceFromUser2ContactShipping($arr){
	global $adb,$current_user;
	$ctoid = $arr[0];
	if (empty($ctoid)) return '0';
	list($wsid,$ctoid) = explode('x', $ctoid);
	$compAdr = $adb->pquery('Select otherstreet,othercity,otherstate,othercountry,otherzip
		from vtiger_contactaddress
		where accountaddressid=?',array($ctoid));
	if ($compAdr and $adb->num_rows($compAdr)>0) {
		$to = $adb->query_result($compAdr, 0, 'othercountry');
		$fld = $adb->query_result($compAdr, 0, 'otherstate');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'othercity');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'otherzip');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'otherstreet');
		$to.= empty($fld) ? '':', '.$fld;
	} else {
		return '0';
	}
	$from = __cb_getCurrentUserAddress();
	$array = array($from, $to);
	return __cb_getGEODistance($array);
}

function __cb_getGEODistanceFromAssignUser2ContactShipping($arr){
	global $adb,$current_user;
	$ctoid = $arr[0];
	$userid = $arr[1];
	if (empty($ctoid)) return '0';
	list($wsid,$ctoid) = explode('x', $ctoid);
	$compAdr = $adb->pquery('Select otherstreet,othercity,otherstate,othercountry,otherzip
		from vtiger_contactaddress
		where accountaddressid=?',array($ctoid));
	if ($compAdr and $adb->num_rows($compAdr)>0) {
		$to = $adb->query_result($compAdr, 0, 'othercountry');
		$fld = $adb->query_result($compAdr, 0, 'otherstate');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'othercity');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'otherzip');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'otherstreet');
		$to.= empty($fld) ? '':', '.$fld;
	} else {
		return '0';
	}
	$from = __cb_getCurrentUserAddress($userid);
	$array = array($from, $to);
	return __cb_getGEODistance($array);
}

function __cb_getGEODistanceFromCoordinates($arr){
	if (sizeof($arr)==2) {
		$from = $arr[0];
		$to = $arr[1];
	}
	else {
		$from = $arr[0].", ".$arr[1];
		$to = $arr[2].", ".$arr[3];
	}
	$array = array($from, $to);
	return __cb_getGEODistance($array);
}
/** END * */
class VTFieldExpressionEvaluater{
	function __construct($expr){

		$this->debug = array();
		$this->operators = array(
				'+'  => '__vt_add',
				'-'  => '__vt_sub',
				'*'  => '__vt_mul',
				'/'  => '__vt_div',
				'==' => '__vt_equals',
				'!=' => '__cb_distinct',
				'<=' => '__vt_ltequals',
				'>=' => '__vt_gtequals',
				'<' => '__vt_lt',
				'>' => '__vt_gt',
		);
		$this->functions = array(
				'concat'=>'__vt_concat',
				'time_diff' => '__vt_time_diff',
				'time_diffdays' => '__vt_time_diffdays',
				'add_days' => '__vt_add_days',
				'sub_days' => '__vt_sub_days',
				'get_date' => '__vt_get_date',
				'format_date' => '__cb_format_date',
				'add_time' => '__vt_add_time',
				'sub_time' => '__vt_sub_time',
				'substring' => '__vt_substring',
				'uppercase' => '__vt_uppercase',
				'lowercase' => '__vt_lowercase',
				'uppercasefirst' => '__vt_uppercasefirst',
				'uppercasewords' => '__vt_uppercasewords',
				'round' => '__vt_round',
				'ceil' => '__vt_ceil',
				'floor' => '__vt_floor',
				'getLatitude' => '__cb_getLatitude',
				'getLongitude' => '__cb_getLongitude',
				'getGEODistance' => '__cb_getGEODistance',
				'getGEODistanceFromCompanyAddress' => '__cb_getGEODistanceFromCompanyAddress',
				'getGEODistanceFromUserAddress' => '__cb_getGEODistanceFromUserAddress',
				'getGEODistanceFromUser2AccountBilling' => '__cb_getGEODistanceFromUser2AccountBilling',
				'getGEODistanceFromAssignUser2AccountBilling' => '__cb_getGEODistanceFromAssignUser2AccountBilling',
				'getGEODistanceFromUser2AccountShipping' => '__cb_getGEODistanceFromUser2AccountShipping',
				'getGEODistanceFromAssignUser2AccountShipping' => '__cb_getGEODistanceFromAssignUser2AccountShipping',
				'getGEODistanceFromUser2ContactBilling' => '__cb_getGEODistanceFromUser2ContactBilling',
				'getGEODistanceFromAssignUser2ContactBilling' => '__cb_getGEODistanceFromAssignUser2ContactBilling',
				'getGEODistanceFromUser2ContacttShipping' => '__cb_getGEODistanceFromUser2ContacttShipping',
				'getGEODistanceFromAssignUser2ContacttShipping' => '__cb_getGEODistanceFromAssignUser2ContacttShipping',
				'getGEODistanceFromCoordinates' => '__cb_getGEODistanceFromCoordinates'
		);

		$this->operations = array_merge($this->functions, $this->operators);
		$this->expr = $expr;
	}

	function evaluate($env){
		$this->env = $env;
		return $this->exec($this->expr);
	}

	function exec($expr){
		global $logbg;
		if($expr instanceof VTExpressionSymbol){
			$loginfo = print_r($expr,true);
			$logbg->debug("VTExpressionSymbol: $loginfo");
			$this->debug[] = $loginfo;
			return $this->env($expr);
		}else if($expr instanceof VTExpressionTreeNode){
			$op = $expr->getName();
			if($op->value=='if'){
				$params = $expr->getParams();
				$cond = $this->exec($params[0]);
				$loginfo = print_r($params,true);
				$logbg->debug("VTExpressionTreeNode: if $loginfo");
				$logbg->debug('VTExpressionTreeNode: condition => '.($cond?'true':'false'));
				$this->debug[] = $loginfo;
				$this->debug[] = $cond;
				if($cond){
					return $this->exec($params[1]);
				}else{
					return $this->exec($params[2]);
				}
			}else{
				$func = $this->operations[$op->value];
				if (substr($func, 0, 28)=='__cb_getGEODistanceFromUser2' || substr($func, 0, 33)=='__cb_getGEODistanceFromAssignUser') {
					$relto = $expr->getParams();
					$relto0 = $relto[0];
					$params = array($this->env($relto0,true));
					if(isset($relto[1])){
						$relto1 = $relto[1];
						$params[1] = $this->env($relto1,true);
					}
				} else {
					$params = array_map(array($this, 'exec'), $expr->getParams());
				}
				$loginfo = print_r($params,true);
				$logbg->debug("VTExpressionTreeNode: function $func $loginfo");
				$this->debug[] = $loginfo;
				return $func($params);
			}
		}else{
			$loginfo = print_r($expr,true);
			$logbg->debug("VTExpression: $loginfo");
			$this->debug[] = $loginfo;
			return $expr;
		}
	}

	function env($sym,$raw=false){
		if($this->env) {
			$val = $this->env->get($sym->value);
			$module = Vtiger_Module::getInstance($this->env->moduleName);
			$field = Vtiger_Field::getInstance($sym->value,$module);
			if ($field and !$raw) {
				global $adb,$log;
				$rsfld = $adb->pquery('select * from vtiger_field where fieldid=?',array($field->id));
				$field = WebserviceField::fromQueryResult($adb, $rsfld, 0);
				$fieldType = $field->getFieldDataType();
				if ($fieldType=='reference') {
					preg_match("/([0-9]+)x([0-9]+)/", $val, $results);
					if (count($results)>0) {  // if it has the form of a webserviceid
						$query = 'select name from vtiger_ws_entity where id = ?';
						$rs = $adb->pquery($query, array($results[1]));
						if ($rs and $adb->num_rows($rs)==1) {  // if the first part is a valid webservice entity id
							$modname = $adb->query_result($rs, 0, 'name');
							$modinfo = getEntityName($modname, array($results[2]));
							if (is_array($modinfo) and count($modinfo)>0) {  // if the second part is recognized as a module record
								$val = $modinfo[$results[2]];
							}
						}
					}
				}
			}
			return $val;
		} else {
			return $sym->value;
		}
	}
}
?>