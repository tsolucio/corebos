<?php

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once 'include/Webservices/CustomerPortalWS.php';

header('Content-Type: application/json');

$data = json_decode(vtlib_purify($_REQUEST['data']), TRUE);

$search_value = $data['term'];
$search_fields = explode(",", $data['searchfields']);
$search_value .= str_repeat("," . $search_value, count($search_fields) - 1 );
$limit = 10;

$filters = array(
	'logic' => 'or',
	'filters' => array(
		array(
			'value' => $search_value,
			'operator' => $data['searchcondition'],
			'field' => $data['searchfields'],
			'ignoreCase' => true
		)
	)
);


$res = getAutocomplete($data,$limit,$filters);
echo json_encode($res);


function getAutocomplete($data,$limit,$fltr) {

	global $adb, $log, $current_user;

	$searchinmodule = vtlib_purify($data["searchmodule"]);
	$fields = vtlib_purify($data['searchfields']);
	$returnfields = vtlib_purify($data['showfields']);

	$fill_fields = explode(",", $data['fillfields']);
	foreach ($fill_fields as $field) {
		$returnfields .= "," . substr($field, strpos($field, "=") + 1);
	}

	$limit = vtlib_purify($limit);
	$filter = vtlib_purify($fltr);
	if (is_array($filter)) {
		// Filter array format looks like this:
		/**************************************
		[filter] => Array(
			[logic] => and
			[filters] => Array(
				[0] => Array(
					[value] => {value to search}
					[operator] => startswith
					[field] => crmname
					[ignoreCase] => true
				)
			)
		)
		***************************************/
		$term = $filter['filters'][0]['value'];
		$op = isset($filter['filters'][0]['operator']) ? $filter['filters'][0]['operator'] : 'startswith';
	} else {
		$term = vtlib_purify($_REQUEST['term']);
		$op = empty($filter) ? 'startswith' : $filter;
	}
	$retvals = getFieldAutocomplete($term, $op, $searchinmodule, $fields, $returnfields, $limit, $current_user);
	$ret = array();
	foreach ($retvals as $value) {
		$ret[] = array('crmid'=>$value['crmid']) + $value['crmfields'];
	}
	return $ret;
}

die();