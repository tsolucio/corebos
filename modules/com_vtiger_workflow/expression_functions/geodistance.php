<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************/

function __cb_getLatitude($address) {
	$addr = urlencode($address);
	$email = urlencode(GlobalVariable::getVariable('Workflow_GeoDistance_Email', ''));
	$country = GlobalVariable::getVariable('Workflow_GeoDistance_Country_Default', '');
	$nmserverip = GlobalVariable::getVariable('Workflow_GeoDistance_Nominatim_Server', 'nominatim.openstreetmap.org');
	$data = file_get_contents("http://$nmserverip/?format=json&addressdetails=1&q=$addr&format=json&limit=1&email=$email&countrycodes=$country");
	$data = json_decode($data);
	return $data[0]->lat;
}

function __cb_getLongitude($address) {
	$addr = urlencode($address);
	$email = urlencode(GlobalVariable::getVariable('Workflow_GeoDistance_Email', ''));
	$country = GlobalVariable::getVariable('Workflow_GeoDistance_Country_Default', '');
	$nmserverip = GlobalVariable::getVariable('Workflow_GeoDistance_Nominatim_Server', 'nominatim.openstreetmap.org');
	$data = file_get_contents("http://$nmserverip/?format=json&addressdetails=1&q=$addr&format=json&limit=1&email=$email&countrycodes=$country");
	$data = json_decode($data);
	return $data[0]->lon;
}

function __cb_getLongitudeLatitude($address) {
	$addr = urlencode($address);
	$email = urlencode(GlobalVariable::getVariable('Workflow_GeoDistance_Email', ''));
	$country = GlobalVariable::getVariable('Workflow_GeoDistance_Country_Default', '');
	$nmserverip = GlobalVariable::getVariable('Workflow_GeoDistance_Nominatim_Server', 'nominatim.openstreetmap.org');
	$data = file_get_contents("http://$nmserverip/?format=json&addressdetails=1&q=$addr&format=json&limit=1&email=$email&countrycodes=$country");
	$data = json_decode($data);
	return $data[0]->lon.','.$data[0]->lat;
}

function __cb_getGEODistance($arr) {
	$from = decode_html($arr[0]);
	$to = decode_html($arr[1]);
	$coo1 = __cb_getLongitudeLatitude($from);
	$coo2 = __cb_getLongitudeLatitude($to);
	$gdserverip = GlobalVariable::getVariable('Workflow_GeoDistance_ServerIP', 'router.project-osrm.org');
	$distance = file_get_contents("http://$gdserverip/route/v1/driving/$coo1;$coo2?overview=false");
	$dis = json_decode($distance);
	$total_distance = $dis->routes[0]->distance/1000;
	return $total_distance." km";
}

function __cb_getGEODistanceFromCompanyAddress($arr) {
	require_once 'include/utils/utils.php';
	$companyDetails = retrieveCompanyDetails();
	$from = $companyDetails['address'];
	$fld = $companyDetails['state'];
	$from.= empty($fld) ? '':', '.$fld;
	$fld = $companyDetails['city'];
	$from.= empty($fld) ? '':', '.$fld;
	$fld = $companyDetails['postalcode'];
	$from.= empty($fld) ? '':', '.$fld;
	$country = GlobalVariable::getVariable('Workflow_GeoDistance_Country_Default', '');
	if ($country == '') {
		$fld = $companyDetails['country'];
		$from.= empty($fld) ? '':', '.$fld;
	}
	$array = array($from, $arr[0]);
	return __cb_getGEODistance($array);
}

function __cb_getGEODistanceFromUserAddress($arr) {
	$from = __cb_getCurrentUserAddress();
	$array = array($from, $arr[0]);
	return __cb_getGEODistance($array);
}

function __cb_getCurrentUserAddress($userid = '') {
	global $adb,$current_user;
	if ($userid == '') {
		$userid = $current_user->id;
	} else {
		list($wsid,$userid) = explode('x', $userid);
	}
	$compAdr = $adb->pquery('Select address_street,address_city,address_state,address_country,address_postalcode from vtiger_users where id=?', array($userid));
	$from = $adb->query_result($compAdr, 0, 'address_street');
	$fld = $adb->query_result($compAdr, 0, 'address_state');
	$from.= empty($fld) ? '':', '.$fld;
	$fld = $adb->query_result($compAdr, 0, 'address_city');
	$from.= empty($fld) ? '':', '.$fld;
	$fld = $adb->query_result($compAdr, 0, 'address_postalcode');
	$from.= empty($fld) ? '':', '.$fld;
	$country = GlobalVariable::getVariable('Workflow_GeoDistance_Country_Default', '');
	if ($country == '') {
		$fld = $adb->query_result($compAdr, 0, 'address_country');
		$from.= empty($fld) ? '':', '.$fld;
	}
	return $from;
}

/**
 * Calculate distance from current user and account billing address
 * @param Array $arr $arr[0] - accountid,
 * $arr[1] - Is for specify from wich address field you want to calculate the distance
 * The next values are accepted
 * country - this values is needed and we will add in the query always
 * state - to calculate the distance from the state
 * city - to calculate the distance from the city
 * code - to calculate the distnace from the postal code.
 * And if the $a[1] not exist we always search from all the address fields if they have values.
 * @return int km distance
 */
function __cb_getGEODistanceFromUser2AccountBilling($arr) {
	global $adb;
	$accid = $arr[0];
	if (empty($accid)) {
		return '0';
	}
	list($wsid,$accid) = explode('x', $accid);
	if (isset($arr[1])) {
		$field_address = $arr[1];
	} else {
		$field_address = '';
	}
	switch ($field_address) {
		case 'country':
			$columns = 'bill_country';
			break;
		case 'state':
			$columns = 'bill_country, bill_state';
			break;
		case 'city':
			$columns = 'bill_country, bill_city';
			break;
		case 'code':
			$columns = 'bill_country, bill_code';
			break;
		default:
			$columns = 'bill_street,bill_city,bill_state,bill_country,bill_code';
			break;
	}
	$compAdr = $adb->pquery('Select '.$columns.' from vtiger_accountbillads where accountaddressid=?', array($accid));
	if ($compAdr && $adb->num_rows($compAdr)>0) {
		$to = $adb->query_result($compAdr, 0, 'bill_street');
		$fld = $adb->query_result($compAdr, 0, 'bill_state');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'bill_city');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'bill_code');
		$to.= empty($fld) ? '':', '.$fld;
		$country = GlobalVariable::getVariable('Workflow_GeoDistance_Country_Default', '');
		if ($country == '') {
			$fld = $adb->query_result($compAdr, 0, 'bill_country');
			$to.= empty($fld) ? '':', '.$fld;
		}
	} else {
		return '0';
	}
	$from = __cb_getCurrentUserAddress();
	$array = array($from, $to);
	return __cb_getGEODistance($array);
}

/**
 * Calculate distance from assign user and account billing address
 * @param Array $arr $arr[0] - accountid,
 * $arr[1] - Is for specify from wich address field you want to calculate the distance
 * The next values are accepted
 * country - this values is needed and we will add in the query always
 * state - to calculate the distance from the state
 * city - to calculate the distance from the city
 * code - to calculate the distnace from the postal code.
 * And if the $a[1] not exist we always search from all the address fields if they have values.
 * @return int km distance
 */
function __cb_getGEODistanceFromAssignUser2AccountBilling($arr) {
	global $adb;
	$accid = $arr[0];
	$userid = $arr[1];
	if (empty($accid)) {
		return '0';
	}
	list($wsid,$accid) = explode('x', $accid);
	if (isset($arr[2])) {
		$field_address = $arr[2];
	} else {
		$field_address = '';
	}
	switch ($field_address) {
		case 'country':
			$columns = 'bill_country';
			break;
		case 'state':
			$columns = 'bill_country, bill_state';
			break;
		case 'city':
			$columns = 'bill_country, bill_city';
			break;
		case 'code':
			$columns = 'bill_country, bill_code';
			break;
		default:
			$columns = 'bill_street,bill_city,bill_state,bill_country,bill_code';
			break;
	}
	$compAdr = $adb->pquery('Select '.$columns.' from vtiger_accountbillads where accountaddressid=?', array($accid));
	if ($compAdr && $adb->num_rows($compAdr)>0) {
		$to = $adb->query_result($compAdr, 0, 'bill_street');
		$fld = $adb->query_result($compAdr, 0, 'bill_state');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'bill_city');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'bill_code');
		$country = GlobalVariable::getVariable('Workflow_GeoDistance_Country_Default', '');
		if ($country == '') {
			$fld = $adb->query_result($compAdr, 0, 'bill_country');
			$to.= empty($fld) ? '':', '.$fld;
		}
	} else {
		return '0';
	}
	$from = __cb_getCurrentUserAddress($userid);
	$array = array($from, $to);
	return __cb_getGEODistance($array);
}

/**
 * Calculate distance from current user and account shipping address
 * @param Array $arr $arr[0] - accountid,
 * $arr[1] - Is for specify from wich address field you want to calculate the distance
 * The next values are accepted
 * country - this values is needed and we will add in the query always
 * state - to calculate the distance from the state
 * city - to calculate the distance from the city
 * code - to calculate the distnace from the postal code.
 * And if the $a[1] not exist we always search from all the address fields if they have values.
 * @return int km distance
 */
function __cb_getGEODistanceFromUser2AccountShipping($arr) {
	global $adb;
	$accid = $arr[0];
	if (empty($accid)) {
		return '0';
	}
	list($wsid,$accid) = explode('x', $accid);
	if (isset($arr[1])) {
		$field_address = $arr[1];
	} else {
		$field_address = '';
	}
	switch ($field_address) {
		case 'country':
			$columns = 'ship_country';
			break;
		case 'state':
			$columns = 'ship_country, ship_state';
			break;
		case 'city':
			$columns = 'ship_country, ship_city';
			break;
		case 'code':
			$columns = 'ship_country, ship_code';
			break;
		default:
			$columns = 'ship_street,ship_city,ship_state,ship_country,ship_code';
			break;
	}
	$compAdr = $adb->pquery('Select '.$columns.' from vtiger_accountshipads where accountaddressid=?', array($accid));
	if ($compAdr && $adb->num_rows($compAdr)>0) {
		$to = $adb->query_result($compAdr, 0, 'ship_street');
		$fld = $adb->query_result($compAdr, 0, 'ship_state');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'ship_city');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'ship_code');
		$to.= empty($fld) ? '':', '.$fld;
		$country = GlobalVariable::getVariable('Workflow_GeoDistance_Country_Default', '');
		if ($country == '') {
			$fld = $adb->query_result($compAdr, 0, 'ship_country');
			$to.= empty($fld) ? '':', '.$fld;
		}
	} else {
		return '0';
	}
	$from = __cb_getCurrentUserAddress();
	$array = array($from, $to);
	return __cb_getGEODistance($array);
}

/**
 * Calculate distance from assign user and account shipping address
 * @param Array $arr $arr[0] - accountid,
 * $arr[1] - Is for specify from wich address field you want to calculate the distance
 * The next values are accepted
 * country - this values is needed and we will add in the query always
 * state - to calculate the distance from the state
 * city - to calculate the distance from the city
 * code - to calculate the distnace from the postal code.
 * And if the $a[1] not exist we always search from all the address fields if they have values.
 * @return int km distance
 */
function __cb_getGEODistanceFromAssignUser2AccountShipping($arr) {
	global $adb;
	$accid = $arr[0];
	$userid = $arr[1];
	if (empty($accid)) {
		return '0';
	}
	list($wsid,$accid) = explode('x', $accid);
	if (isset($arr[2])) {
		$field_address = $arr[2];
	} else {
		$field_address = '';
	}
	switch ($field_address) {
		case 'country':
			$columns = 'ship_country';
			break;
		case 'state':
			$columns = 'ship_country, ship_state';
			break;
		case 'city':
			$columns = 'ship_country, ship_city';
			break;
		case 'code':
			$columns = 'ship_country, ship_code';
			break;
		default:
			$columns = 'ship_street,ship_city,ship_state,ship_country,ship_code';
			break;
	}
	$compAdr = $adb->pquery('Select '.$columns.' from vtiger_accountshipads where accountaddressid=?', array($accid));
	if ($compAdr && $adb->num_rows($compAdr)>0) {
		$to = $adb->query_result($compAdr, 0, 'ship_street');
		$fld = $adb->query_result($compAdr, 0, 'ship_state');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'ship_city');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'ship_code');
		$to.= empty($fld) ? '':', '.$fld;
		$country = GlobalVariable::getVariable('Workflow_GeoDistance_Country_Default', '');
		if ($country == '') {
			$fld = $adb->query_result($compAdr, 0, 'ship_country');
			$to.= empty($fld) ? '':', '.$fld;
		}
	} else {
		return '0';
	}
	$from = __cb_getCurrentUserAddress($userid);
	$array = array($from, $to);
	return __cb_getGEODistance($array);
}

/**
 * Calculate distance from current user and contact billing address
 * @param Array $arr $arr[0] - contactid,
 * $arr[1] - Is for specify from wich address field you want to calculate the distance
 * The next values are accepted
 * country - this values is needed and we will add in the query always
 * state - to calculate the distance from the state
 * city - to calculate the distance from the city
 * code - to calculate the distnace from the postal code.
 * And if the $a[1] not exist we always search from all the address fields if they have values.
 * @return int km distance
 */
function __cb_getGEODistanceFromUser2ContactBilling($arr) {
	global $adb;
	$ctoid = $arr[0];
	if (empty($ctoid)) {
		return '0';
	}
	list($wsid,$ctoid) = explode('x', $ctoid);
	if (isset($arr[1])) {
		$field_address = $arr[1];
	} else {
		$field_address = '';
	}
	switch ($field_address) {
		case 'country':
			$columns = 'mailingcountry';
			break;
		case 'state':
			$columns = 'mailingcountry, mailingstate';
			break;
		case 'city':
			$columns = 'mailingcountry, mailingcity';
			break;
		case 'code':
			$columns = 'mailingcountry, mailingzip';
			break;
		default:
			$columns = 'mailingstreet,mailingcity,mailingstate,mailingcountry,mailingzip';
			break;
	}
	$compAdr = $adb->pquery('Select '.$columns.' from vtiger_contactaddress where contactaddressid=?', array($ctoid));
	if ($compAdr && $adb->num_rows($compAdr)>0) {
		$to = $adb->query_result($compAdr, 0, 'mailingstreet');
		$fld = $adb->query_result($compAdr, 0, 'mailingstate');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'mailingcity');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'mailingzip');
		$to.= empty($fld) ? '':', '.$fld;
		$country = GlobalVariable::getVariable('Workflow_GeoDistance_Country_Default', '');
		if ($country == '') {
			$fld = $adb->query_result($compAdr, 0, 'mailingcountry');
			$to.= empty($fld) ? '':', '.$fld;
		}
	} else {
		return '0';
	}
	$from = __cb_getCurrentUserAddress();
	$array = array($from, $to);
	return __cb_getGEODistance($array);
}

/**
 * Calculate distance from assign user and contact billing address
 * @param Array $arr $arr[0] - contactid,
 * $arr[1] - Is for specify from wich address field you want to calculate the distance
 * The next values are accepted
 * country - this values is needed and we will add in the query always
 * state - to calculate the distance from the state
 * city - to calculate the distance from the city
 * code - to calculate the distnace from the postal code.
 * And if the $a[1] not exist we always search from all the address fields if they have values.
 * @return int km distance
 */
function __cb_getGEODistanceFromAssignUser2ContactBilling($arr) {
	global $adb;
	$ctoid = $arr[0];
	$userid = $arr[1];
	if (empty($ctoid)) {
		return '0';
	}
	list($wsid,$ctoid) = explode('x', $ctoid);
	if (isset($arr[2])) {
		$field_address = $arr[2];
	} else {
		$field_address = '';
	}
	switch ($field_address) {
		case 'country':
			$columns = 'mailingcountry';
			break;
		case 'state':
			$columns = 'mailingcountry, mailingstate';
			break;
		case 'city':
			$columns = 'mailingcountry, mailingcity';
			break;
		case 'code':
			$columns = 'mailingcountry, mailingzip';
			break;
		default:
			$columns = 'mailingstreet,mailingcity,mailingstate,mailingcountry,mailingzip';
			break;
	}
	$compAdr = $adb->pquery('Select '.$columns.' from vtiger_contactaddress where contactaddressid=?', array($ctoid));
	if ($compAdr && $adb->num_rows($compAdr)>0) {
		$to = $adb->query_result($compAdr, 0, 'mailingstreet');
		$fld = $adb->query_result($compAdr, 0, 'mailingstate');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'mailingcity');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'mailingzip');
		$to.= empty($fld) ? '':', '.$fld;
		$country = GlobalVariable::getVariable('Workflow_GeoDistance_Country_Default', '');
		if ($country == '') {
			$fld = $adb->query_result($compAdr, 0, 'mailingcountry');
			$to.= empty($fld) ? '':', '.$fld;
		}
	} else {
		return '0';
	}
	$from = __cb_getCurrentUserAddress($userid);
	$array = array($from, $to);
	return __cb_getGEODistance($array);
}

/**
 * Calculate distance from current user and contact shipping address
 * @param Array $arr $arr[0] - contactid,
 * $arr[1] - Is for specify from wich address field you want to calculate the distance
 * The next values are accepted
 * country - this values is needed and we will add in the query always
 * state - to calculate the distance from the state
 * city - to calculate the distance from the city
 * code - to calculate the distnace from the postal code.
 * And if the $a[1] not exist we always search from all the address fields if they have values.
 * @return int km distance
 */
function __cb_getGEODistanceFromUser2ContactShipping($arr) {
	global $adb;
	$ctoid = $arr[0];
	if (empty($ctoid)) {
		return '0';
	}
	list($wsid,$ctoid) = explode('x', $ctoid);
	if (isset($arr[1])) {
		$field_address = $arr[1];
	} else {
		$field_address = '';
	}
	switch ($field_address) {
		case 'country':
			$columns = 'othercountry';
			break;
		case 'state':
			$columns = 'othercountry, otherstate';
			break;
		case 'city':
			$columns = 'othercountry, othercity';
			break;
		case 'code':
			$columns = 'othercountry, otherzip';
			break;
		default:
			$columns = 'otherstreet,othercity,otherstate,othercountry,otherzip';
			break;
	}
	$compAdr = $adb->pquery('Select '.$columns.' from vtiger_contactaddress where contactaddressid=?', array($ctoid));
	if ($compAdr && $adb->num_rows($compAdr)>0) {
		$to = $adb->query_result($compAdr, 0, 'otherstreet');
		$fld = $adb->query_result($compAdr, 0, 'otherstate');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'othercity');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'otherzip');
		$to.= empty($fld) ? '':', '.$fld;
		$country = GlobalVariable::getVariable('Workflow_GeoDistance_Country_Default', '');
		if ($country == '') {
			$fld = $adb->query_result($compAdr, 0, 'othercountry');
			$to.= empty($fld) ? '':', '.$fld;
		}
	} else {
		return '0';
	}

	$from = __cb_getCurrentUserAddress();
	$array = array($from, $to);
	return __cb_getGEODistance($array);
}

/**
 * Calculate distance from assign user and contact shipping address
 * @param Array $arr $arr[0] - contactid,
 * $arr[1] - Is for specify from wich address field you want to calculate the distance
 * The next values are accepted
 * country - this values is needed and we will add in the query always
 * state - to calculate the distance from the state
 * city - to calculate the distance from the city
 * code - to calculate the distnace from the postal code.
 * And if the $a[1] not exist we always search from all the address fields if they have values.
 * @return int km distance
 */
function __cb_getGEODistanceFromAssignUser2ContactShipping($arr) {
	global $adb;
	$ctoid = $arr[0];
	$userid = $arr[1];
	if (empty($ctoid)) {
		return '0';
	}
	list($wsid,$ctoid) = explode('x', $ctoid);
	if (isset($arr[2])) {
		$field_address = $arr[2];
	} else {
		$field_address = '';
	}
	switch ($field_address) {
		case 'country':
			$columns = 'othercountry';
			break;
		case 'state':
			$columns = 'othercountry, otherstate';
			break;
		case 'city':
			$columns = 'othercountry, othercity';
			break;
		case 'code':
			$columns = 'othercountry, otherzip';
			break;
		default:
			$columns = 'otherstreet,othercity,otherstate,othercountry,otherzip';
			break;
	}
	$compAdr = $adb->pquery('Select '.$columns.' from vtiger_contactaddress where contactaddressid=?', array($ctoid));
	if ($compAdr && $adb->num_rows($compAdr)>0) {
		$to = $adb->query_result($compAdr, 0, 'otherstreet');
		$fld = $adb->query_result($compAdr, 0, 'otherstate');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'othercity');
		$to.= empty($fld) ? '':', '.$fld;
		$fld = $adb->query_result($compAdr, 0, 'otherzip');
		$to.= empty($fld) ? '':', '.$fld;
		$country = GlobalVariable::getVariable('Workflow_GeoDistance_Country_Default', '');
		if ($country == '') {
			$fld = $adb->query_result($compAdr, 0, 'othercountry');
			$to.= empty($fld) ? '':', '.$fld;
		}
	} else {
		return '0';
	}
	$from = __cb_getCurrentUserAddress($userid);
	$array = array($from, $to);
	return __cb_getGEODistance($array);
}

function __cb_getGEODistanceFromCoordinates($arr) {
	if (count($arr) == 2) {
		$from = $arr[0];
		$to = $arr[1];
	} else {
		$from = $arr[0].", ".$arr[1];
		$to = $arr[2].", ".$arr[3];
	}
	$array = array($from, $to);
	return __cb_getGEODistance($array);
}
?>