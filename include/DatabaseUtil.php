<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
use \PHPSQLParser\PHPSQLParser;
use \PHPSQLParser\PHPSQLCreator;

// check database charset and collation are set to UTF8.
function check_db_utf8_support($conn) {
	$dbvarRS = $conn->Execute("show variables like '%_database'");
	$db_character_set = null;
	$db_collation_type = null;
	while (!$dbvarRS->EOF) {
		$arr = $dbvarRS->FetchRow();
		$arr = array_change_key_case($arr);
		if ($arr['variable_name'] == 'character_set_database') {
			$db_character_set = $arr['value'];
		}
		if ($arr['variable_name'] == 'collation_database') {
			$db_collation_type = $arr['value'];
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
	if (strripos($query, ' GROUP BY ') > 0) {
		$query = substr($query, 0, strripos($query, ' GROUP BY '));
	}

	// Strip off any "ORDER BY" clause
	if (strripos($query, ' ORDER BY ') > 0) {
		$query = substr($query, 0, strripos($query, ' ORDER BY '));
	}

	return $query;
}

//Strip tailing commands
function stripTailCommandsFromQuery($query, $stripgroup = true) {
	if ($stripgroup && strripos($query, ' GROUP BY ') > 0) {
		$query = substr($query, 0, strripos($query, ' GROUP BY '));
	}
	if (strripos($query, ' ORDER BY ') > 0) {
		$query = substr($query, 0, strripos($query, ' ORDER BY '));
	}
	if (strripos($query, ' LIMIT ') > 0) {
		$query = substr($query, 0, strripos($query, ' LIMIT '));
	}
	return $query;
}

//Make a count query
function mkCountQuery($query, $eliminateGroupBy = true) {
	$query = mkXQuery($query, 'count(*) AS count');

	// Strip off any "GROUP BY" clause
	if ($eliminateGroupBy && stripos($query, ' GROUP BY ') > 0) {
		$query = substr($query, 0, stripos($query, ' GROUP BY '));
	}
	// Strip off any "ORDER BY" clause
	if (stripos($query, ' ORDER BY ') > 0) {
		$query = substr($query, 0, stripos($query, ' ORDER BY '));
	}
	return $query;
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

/**
 * @param string module name for which query needs to be generated
 * @param Users user for which query needs to be generated
 * @return string Access control Query for the user
 */
function getNonAdminAccessControlQuery($module, $user, $scope = '') {
	$instance = CRMEntity::getInstance($module);
	return $instance->getNonAdminAccessControlQuery($module, $user, $scope);
}

function getFromClauseAlreadyPresent($parsed, $fromClause) {
	$found = '';
	if (isset($parsed['FROM'])) {
		$whereisjoin = stripos($fromClause, ' join ');
		$fromClause = substr($fromClause, $whereisjoin+6, stripos($fromClause, ' on ')-$whereisjoin-2);
		$fromClause = strtolower(str_replace(' ', '', $fromClause));
		foreach ($parsed['FROM'] as $clause) {
			$existingClause = substr($clause['base_expr'], 0, stripos($clause['base_expr'], ' on ')+4); // strip tables
			$existingClause = strtolower(str_replace(' ', '', $existingClause));
			if ($clause['ref_type']=='ON' && $existingClause==$fromClause) {
				return ($clause['join_type']=='JOIN' ? 'INNER' : $clause['join_type']).' join '.$clause['base_expr'];
			}
		}
	}
	return $found;
}

function appendFromClauseToQuery($query, $fromClause, $fromClauseNoConditions = '') {
	$query = preg_replace('/\s+/', ' ', $query);
	if (trim($fromClause)=='') {
		return $query;
	}
	$fromClause = trim($fromClause);
	$parser = new PHPSQLParser();
	$parsed = $parser->parse($query);
	$alreadyPresent = getFromClauseAlreadyPresent($parsed, ($fromClauseNoConditions=='' ? $fromClause : $fromClauseNoConditions));
	if ($alreadyPresent=='') {
		if (!isset($parsed['WHERE'])) {
			return $query.' '.$fromClause;
		} else {
			unset($parsed['WHERE'], $parsed['ORDER'], $parsed['LIMIT'], $parsed['GROUP'], $parsed['HAVING']);
			$creator = new PHPSQLCreator($parsed);
			// we need to find the 'where' of the SQL, $creator->created contains the query up to that 'where' so we start searching from there backwards
			$whereposition = strripos(substr($query, 0, strlen($creator->created)+8), ' where ');
			return substr($query, 0, $whereposition).' '.$fromClause.substr($query, $whereposition);
		}
	} else {
		return str_ireplace($alreadyPresent, $fromClause, $query);
	}
}

function appendConditionClauseToQuery($query, $condClause, $glue = 'and') {
	$query = preg_replace('/\s+/', ' ', $query);
	if (trim($condClause)=='') {
		return $query;
	}
	$parser = new PHPSQLParser();
	$parsed = $parser->parse($query);
	if (!isset($parsed['WHERE'])) {
		return $query.' WHERE '.$condClause;
	} else {
		unset($parsed['WHERE'], $parsed['ORDER'], $parsed['LIMIT'], $parsed['GROUP'], $parsed['HAVING']);
		$creator = new PHPSQLCreator($parsed);
		// we need to find the 'where' of the SQL, $creator->created contains the query up to that 'where' so we start searching from there backwards
		$whereposition = strripos(substr($query, 0, strlen($creator->created)+8), ' where ');
		return substr($query, 0, $whereposition).' WHERE ('.$condClause.') '.$glue.' '.substr($query, $whereposition+7);
	}
}
?>
