<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

// check database charset and collation are set to UTF8.
function check_db_utf8_support($conn) {
	$dbvarRS = $conn->Execute("show variables like '%_database'");
	$db_character_set = null;
	$db_collation_type = null;
	while (!$dbvarRS->EOF) {
		$arr = $dbvarRS->FetchRow();
		$arr = array_change_key_case($arr);
		switch ($arr['variable_name']) {
			case 'character_set_database':
				$db_character_set = $arr['value'];
				break;
			case 'collation_database':
				$db_collation_type = $arr['value'];
				break;
		}
		// If we have all the required information break the loop.
		if ($db_character_set != null && $db_collation_type != null) {
			break;
		}
	}
	return (false !== stripos($db_character_set, 'utf8') && false !== stripos($db_collation_type, 'utf8'));
}

function get_db_charset($conn) {
	$dbvarRS = $conn->query("show variables like '%_database'");
	$db_character_set = null;
	while (!$dbvarRS->EOF) {
		$arr = $dbvarRS->FetchRow();
		$arr = array_change_key_case($arr);
		if ($arr['variable_name'] == 'character_set_database') {
			$db_character_set = $arr['value'];
			break;
		}
	}
	return $db_character_set;
}

function mkXQuery($query, $expression) {
	// Remove all the \n, \r and white spaces to keep the space between the words consistent.
	// This is required for proper pattern matching for words like ' FROM ', 'ORDER BY', 'GROUP BY' as they depend on the spaces between the words.
	$query = preg_replace("/[\n\r\s]+/", ' ', $query);
	// Strip off the current SELECT fields and replace them with expression
	return "SELECT $expression".substr($query, stripos($query, ' FROM '), strlen($query));
}

function mkMaxQuery($query, $field) {
	return mkXQuery($query, 'max('.$field.') as max');
}

function mkMinQuery($query, $field) {
	return mkXQuery($query, 'min('.$field.') as min');
}

function mkSumQuery($query, $field) {
	return mkXQuery($query, 'sum('.$field.') as sum');
}

function mkAvgQuery($query, $field) {
	return mkXQuery($query, 'avg('.$field.') as avg');
}

function mkTotQuery($query, $column) {
	$query = mkXQuery($query, 'sum('.$column.') AS total');

	// Strip off any "GROUP BY" clause
	if (strpos($query, ' GROUP BY ') > 0) {
		$query = substr($query, 0, strpos($query, ' GROUP BY '));
	}

	// Strip off any "ORDER BY" clause
	if (strpos($query, ' ORDER BY ') > 0) {
		$query = substr($query, 0, strpos($query, ' ORDER BY '));
	}

	return $query;
}

//Strip tailing commands
function stripTailCommandsFromQuery($query, $stripgroup = true) {
	if ($stripgroup && stripos($query, ' GROUP BY ') > 0) {
		$query = substr($query, 0, stripos($query, ' GROUP BY '));
	}
	if (stripos($query, ' ORDER BY ') > 0) {
		$query = substr($query, 0, stripos($query, ' ORDER BY '));
	}
	if (stripos($query, ' LIMIT ') > 0) {
		$query = substr($query, 0, stripos($query, ' LIMIT '));
	}
	return $query;
}

//Make a count query
function mkCountQuery($query) {
	$query = mkXQuery($query, 'count(*) AS count');

	// Strip off any "GROUP BY" clause
	if (stripos($query, ' GROUP BY ') > 0) {
		$query = substr($query, 0, stripos($query, ' GROUP BY '));
	}
	// Strip off any "ORDER BY" clause
	if (stripos($query, ' ORDER BY ') > 0) {
		$query = substr($query, 0, stripos($query, ' ORDER BY '));
	}
	return ($query);
}

// Make a count query with FULL query
function mkCountWithFullQuery($query) {
	// Remove all the \n, \r and white spaces to keep the space between the words consistent.
	// This is required for proper pattern matching for words like ' FROM ', 'ORDER BY', 'GROUP BY' as they depend on the spaces between the words.
	$query = preg_replace("/[\n\r\s]+/", ' ', $query);

	// Strip off any "ORDER BY" clause
	if (stripos($query, ' ORDER BY ') > 0) {
		$query = substr($query, 0, stripos($query, ' ORDER BY '));
	}
	return "SELECT count(*) AS count FROM ($query) as sqlcount";
}
?>
