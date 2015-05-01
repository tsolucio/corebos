<?php
/***********************************************************************************
 * Copyright 2015 JPL TSolucio, S.L.  --  This file is a part of coreBOS.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 ************************************************************************************/

require_once("include/Webservices/Utils.php");

/*
 * Given a webservice formatted query with optional extended FQN syntax return a valid SQL statement
 * 
 * Parameters:
 * query: webservice formatted query with extended FQN syntax, for example
 *        select firstname, lastname, Accounts.accountname from Contacts
 * 
 * Author: JPL TSolucio, S.L. April 2015.  Joe Bordes
 * 
 */
function __FQNExtendedQueryGetQuery($q, $user) {
	global $adb, $log;

	$moduleRegex = "/[fF][rR][Oo][Mm]\s+([^\s;]+)(.*)/";
	preg_match($moduleRegex, $q, $m);
	$mainModule = trim($m[1]);

	// pickup meta data of module
	$webserviceObject = VtigerWebserviceObject::fromName($adb,$mainModule);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();
	require_once $handlerPath;
	$handler = new $handlerClass($webserviceObject,$user,$adb,$log);
	$meta = $handler->getMeta();

	// check modules
	if (!$meta->isModuleEntity()) {
		throw new WebserviceException('INVALID_MODULE',"Given main module ($mainModule) cannot be found");
	}

	// check permission on module
	$entityName = $meta->getEntityName();
	$types = vtws_listtypes(null, $user);
	if(!in_array($entityName,$types['types'])){
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED,"Permission to perform the operation on module ($mainModule) is denied");
	}

	if(!$meta->hasReadAccess()){
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED,"Permission to read module is denied");
	}

	// user has enough permission to start process
	$fieldcolumn = $meta->getFieldColumnMapping();
	$queryGenerator = new QueryGenerator($mainModule, $user);
	$queryColumns = trim(substr($q,6,stripos($q,' from ')-5));
	$queryColumns = explode(',',$queryColumns);
	$queryColumns = array_map(trim, $queryColumns);
	$queryRelatedModules = array();
	foreach ($queryColumns as $field) {
		if (strpos($field, '.')>0) {
			list($m,$f) = explode('.', $field);
			if (!isset($queryRelatedModules[$m])) {
				$relhandler = vtws_getModuleHandlerFromName($m,$user);
				$relmeta = $relhandler->getMeta();
				$queryRelatedModules[$m] = $relmeta;
			}
		}
	}
	$queryColumns[] = 'id';  // add ID column to follow REST interface behaviour
	$queryGenerator->setFields($queryColumns);
	// take apart conditionals
	$queryConditions = trim($m[2],' ;');
	$moduleRegex = "/[fF][rR][Oo][Mm]\s+([^\s;]+)(.*)/";
	preg_match($moduleRegex, $q, $m);
	$queryConditions = trim($m[2],' ;');
	if (strtolower(substr($queryConditions,0,5))=='where') {
		$queryConditions = substr($queryConditions,6);
	}
	$orderbyCond = "/([oO][rR][dD][eE][rR]\s+[bB][yY]\s+)+(.*)/";
	preg_match($orderbyCond, $queryConditions, $ob);
	$obflds = (isset($ob[2]) ? $ob[2] : '');
	if (stripos($obflds,' limit ')>0) $obflds = substr($obflds,0,stripos($obflds,' limit '));
	$limitCond = "/([lL][iI][mM][iI][tT]\s+)+(.*)/";
	preg_match($limitCond,$queryConditions,$lm);
	$lmoc = (isset($lm[2]) ? $lm[2] : '');
	if (stripos($lmoc,' order ')>0) $lmoc = substr($lmoc,0,stripos($lmoc,' order '));
	if (stripos($queryConditions,' order ')>0) $queryConditions = substr($queryConditions,0,stripos($queryConditions,' order '));
	if (stripos($queryConditions,' limit ')>0) $queryConditions = substr($queryConditions,0,stripos($queryConditions,' limit '));
	$qcst = strtolower(substr(trim($queryConditions), 0,5));
	if ($qcst=='order' or $qcst=='limit') $queryConditions='';
	// $queryConditions has all the where conditions
	// $obflds has the list of order by fields
	// $limit is the full correct limit SQL part
	// transform REST ids
	$relatedCond = "/=\s*'*\d+x(\d+)'*/";
	$afterwhere=preg_replace($relatedCond,' = $1 ',$afterwhere);
	// where
	$qc = $queryConditions;
	$posand = stripos(' and ', $qc);
	$posor = stripos(' or ', $qc);
	$glue = '';
	while ($posand>0 or $posor>0 or strlen($qc)) {
		if ($posand==0 and $posor==0) {
			__FQNExtendedQueryAddCondition($queryGenerator,$qc,$glue,$mainModule,$fieldcolumn, $user);
			$qc = '';
		} elseif ($posand>$posor) {
			$qcond = substr($qc, 0, $posor);
			__FQNExtendedQueryAddCondition($queryGenerator,$qcond,$glue,$mainModule,$fieldcolumn, $user);
			$glue = $queryGenerator::$OR;
			$qc = trim(substr($qc, 0, $posor+4));
		} else {
			$qcond = substr($qc, 0, $posand);
			__FQNExtendedQueryAddCondition($queryGenerator,$qcond,$glue,$mainModule,$fieldcolumn, $user);
			$glue = $queryGenerator::$AND;
			$qc = trim(substr($qc, 0, $posand+5));
		}
		$posand = stripos(' and ', $qc);
		$posor = stripos(' or ', $qc);
	}

	$query = $queryGenerator->getQuery();
	// limit and order
	if (!empty($obflds)) {
		$obflds = trim($obflds);
		if (strtolower(substr($obflds,-3))=='asc') {
			$dir = ' asc ';
			$obflds = trim(substr($obflds, 0, strlen($obflds)-3));
		} elseif (strtolower(substr($obflds,-4))=='desc') {
			$dir = ' desc ';
			$obflds = trim(substr($obflds, 0, strlen($obflds)-4));
		} else {
			$dir = '';
		}
		$obflds = explode(',',$obflds);
		foreach ($obflds as $k => $field) {
			$obflds[$k] = __FQNExtendedQueryField2Column($field,$mainModule,$fieldcolumn, $user);
		}
		$query .= ' order by '.implode(',', $obflds).$dir.' ';
	}
	if (!empty($lmoc)) {
		$query .= " limit $lmoc ";
	}
	return array($query,$queryRelatedModules);
}

function __FQNExtendedQueryAddCondition($queryGenerator,$condition,$glue,$mainModule,$maincolumnTable, $user) {
	global $adb, $log;
	// field operator value
	// conditionals: condition operations or in clauses or like clauses
	// conditional operators: <, >, <=, >=, =, !=.
	// in clauses: in (fieldlist).
	// like clauses: like 'sqlregex'.
	// value list: a comma separated list of values.
	$condition = trim($condition);
	$condition = str_replace('  ', ' ', $condition);
	$field = strtok($condition, ' ');
	$op = strtolower(strtok(' '));
	$val = strtok(' ');
	if ($op == 'not' and strtolower($val)=='like') {
		$op = 'notlike';
		$val = strtok($condition);
	}
	if ($op == 'is') {
		$secop = strtolower($val);
		if ($secop == 'not') {
			$val = '';
			$op = 'isnotnull';
		}
		if ($secop == 'null') {
			$val = '';
			$op = 'isnull';
		}
	}

	// TODO  add query generator operators for 'bw' = BETWEEN value1 and value2  (between two dates)
	switch ($op) {
		case '<':
			if (is_date_type) {
				$op = 'b';
			} else {
				$op = 'l';
			}
			break;
		case '>':
			if (is_date_type) {
				$op = 'a';
			} else {
				$op = 'g';
			}
			break;
		case '<=':
			$op = 'm';
			break;
		case '>=':
			$op = 'h';
			break;
		case '=':
			$op = 'e';
			break;
		case '!=':
			$op = 'n';
			break;
		case 'i':
		case 'in':
			$op = 'i';
			$val = trim($val);
			$val = ltrim($val,'(');
			$val = rtrim($val,')');
			$val = str_replace("'", '', $val);
			break;
		case 'like':
			$v = trim($val);
			$v = str_replace("'", '', $v);
			if (substr($v,-1)=='%' and substr($v,0,1)=='%') {
				$op = 'c';
			} elseif (substr($v,0,1)=='%') {
				$op = 'ew';
			} else {
				$op = 's';
			}
			$val = str_replace('%', '', $v);
			break;
		case 'notlike':
			$op = 'k';
			break;
		case 'isnotnull':
			$op = 'ny';
			break;
		case 'isnull':
			$op = 'y';
			break;
			
		default:
			$op = 'e';
			break;
	}

	if (strpos($field, '.')>0) {  // FQN
		list($fmod,$fname) = explode('.', $field);
		$webserviceObject = VtigerWebserviceObject::fromName($adb,$fmod);
		$handlerPath = $webserviceObject->getHandlerPath();
		$handlerClass = $webserviceObject->getHandlerClass();
		require_once $handlerPath;
		$handler = new $handlerClass($webserviceObject,$user,$adb,$log);
		$relmeta = $handler->getMeta();
		$rfs = $relmeta->getReferenceFieldDetails();
		$found = false;
		foreach ($rfs as $reffld => $mods) {
			if (in_array($fmod, $mods)) {
				$found = true;
				$queryGenerator->addReferenceModuleFieldCondition($fmod, $reffld, $fname, $val, $op, $glue);
				break;
			}
		}
		if (!$found and $fmod==$mainModule) { // didn't find the field on the relation so we try to find it on the main module
			$queryGenerator->addCondition($fname, $val, $op, $glue);
		}
	} else {
		$queryGenerator->addCondition($field, $val, $op, $glue);
	}
}

function __FQNExtendedQueryField2Column($field,$mainModule,$maincolumnTable,$user) {
	global $adb,$log;
	$field = trim($field);
	if (isset($maincolumnTable[$field])) {
		return $maincolumnTable[$field];
	}
	if (strpos($field, '.')>0) {  // FQN
		list($fmod,$fname) = explode('.', $field);
		if ($fmod==$mainModule) {
			return $fmod.'.'.$maincolumnTable[$fname];
		} else {
			$webserviceObject = VtigerWebserviceObject::fromName($adb,$fmod);
			$handlerPath = $webserviceObject->getHandlerPath();
			$handlerClass = $webserviceObject->getHandlerClass();
			require_once $handlerPath;
			$handler = new $handlerClass($webserviceObject,$user,$adb,$log);
			$relmeta = $handler->getMeta();
			$fieldcolumn = $relmeta->getFieldColumnMapping();
			return $fmod.'.'.$fieldcolumn[$fname];
		}
	}
}

function __FQNExtendedQueryIsFQNQuery($q) {
	$cq = __FQNExtendedQueryCleanQuery($q);
	return (stripos($cq,'.')>0 or stripos($cq,'(')>0);
}

function __FQNExtendedQueryIsRelatedQuery($q) {
	$cq = __FQNExtendedQueryCleanQuery($q);
	$cq = substr($cq,stripos(' where '));
	return (stripos($cq,'related.')>0);
}

function __FQNExtendedQueryCleanQuery($q) {
	$moduleRegex = "/ in \(.+\)/Us";  // eliminate IN operator
	$r = preg_replace($moduleRegex, '', $q);
	$moduleRegex = "/'.+'/Us";  // eliminate string literals
	$r = preg_replace($moduleRegex, '', $r);
	return $r;
}