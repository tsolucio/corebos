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
require_once 'include/Webservices/Utils.php';
require_once 'modules/com_vtiger_workflow/include.inc';
require_once 'modules/com_vtiger_workflow/WorkFlowScheduler.php';

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
	$webserviceObject = VtigerWebserviceObject::fromName($adb, $mainModule);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();
	require_once $handlerPath;
	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();
	$mainModule = $meta->getTabName();  // normalize module name
	// check modules
	if (!$meta->isModuleEntity()) {
		throw new WebserviceException('INVALID_MODULE', "Given main module ($mainModule) cannot be found");
	}

	// check permission on module
	$entityName = $meta->getEntityName();
	$types = vtws_listtypes(null, $user);
	if (!in_array($entityName, $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Permission to perform the operation on module ($mainModule) is denied");
	}

	if (!$meta->hasReadAccess()) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Permission to read module is denied");
	}

	// user has enough permission to start process
	$fieldcolumn = $meta->getFieldColumnMapping();
	$queryGenerator = new QueryGenerator($mainModule, $user);
	$queryColumns = trim(substr($q, 6, stripos($q, ' from ')-5));
	$queryColumns = explode(',', $queryColumns);
	$queryColumns = array_map('trim', $queryColumns);
	$countSelect = ($queryColumns == array('count(*)'));
	$queryRelatedModules = array();
	foreach ($queryColumns as $k => $field) {
		if (strpos($field, '.')>0) {
			list($m,$f) = explode('.', $field);
			if (!isset($queryRelatedModules[$m])) {
				$relhandler = vtws_getModuleHandlerFromName($m, $user);
				$relmeta = $relhandler->getMeta();
				$mn = $relmeta->getTabName();  // normalize module name
				$queryRelatedModules[$mn] = $relmeta;
				if ($m!=$mn) {
					$queryColumns[$k] = $mn.'.'.$f;
				}
			}
		}
	}
	$queryColumns[] = 'id';  // add ID column to follow REST interface behaviour
	$queryGenerator->setFields($queryColumns);
	// take apart conditionals
	$queryConditions = trim($m[2], ' ;');
	$moduleRegex = "/[fF][rR][Oo][Mm]\s+([^\s;]+)(.*)/";
	preg_match($moduleRegex, $q, $m);
	$queryConditions = trim($m[2], ' ;');
	if (strtolower(substr($queryConditions, 0, 5))=='where') {
		$queryConditions = substr($queryConditions, 6);
	}
	$orderbyCond = "/([oO][rR][dD][eE][rR]\s+[bB][yY]\s+)+(.*)/";
	preg_match($orderbyCond, $queryConditions, $ob);
	$obflds = (isset($ob[2]) ? $ob[2] : '');
	if (stripos($obflds, ' limit ')>0) {
		$obflds = substr($obflds, 0, stripos($obflds, ' limit '));
	}
	$limitCond = "/([lL][iI][mM][iI][tT]\s+)+(.*)/";
	preg_match($limitCond, $queryConditions, $lm);
	$lmoc = (isset($lm[2]) ? $lm[2] : '');
	if (stripos($lmoc, ' order ')>0) {
		$lmoc = substr($lmoc, 0, stripos($lmoc, ' order '));
	}
	if (stripos($queryConditions, ' order ')>0) {
		$queryConditions = substr($queryConditions, 0, stripos($queryConditions, ' order '));
	}
	if (stripos($queryConditions, ' limit ')>0) {
		$queryConditions = substr($queryConditions, 0, stripos($queryConditions, ' limit '));
	}
	$qcst = strtolower(substr(trim($queryConditions), 0, 5));
	if ($qcst=='order' || $qcst=='limit') {
		$queryConditions='';
	}
	// $queryConditions has all the where conditions
	// $obflds has the list of order by fields
	// $limit is the full correct limit SQL part
	// transform REST ids
	// where
	if (strlen($queryConditions)>0) {
		$queryGenerator->startGroup();
		$qc = trim($queryConditions);
		if (substr($qc, 0, 1)=='(') {
			$queryGenerator->startGroup();
			$qc = substr($qc, 1);
		}
		$inopRegex = "/\s+(in|IN)\s+\(/";
		$posand = stripos($qc, ' and ');
		$posor = stripos($qc, ' or ');
		$glue = '';
		while ($posand>0 || $posor>0 || strlen($qc)) {
			$endgroup = false;
			preg_match($inopRegex, $qc, $qcop);
			$inop = (count($qcop)>0);
			$lasttwo = '';
			if ($inop) {
				$lasttwo = str_replace(' ', '', $qc);
				$lasttwo = substr($lasttwo, -2);
			}
			if ($posand==0 && $posor==0) {
				if ((!$inop && substr($qc, -1)==')') || ($inop && $lasttwo=='))')) {
					$qc = substr($qc, 0, strlen($qc)-1);
					$endgroup = true;
				}
				__FQNExtendedQueryAddCondition($queryGenerator, $qc, $glue, $mainModule, $fieldcolumn, $user);
				$qc = '';
			} elseif ($posand==0 || ($posand>$posor && $posor!=0)) {
				$qcond = trim(substr($qc, 0, $posor));
				if ((!$inop && substr($qcond, -1)==')') || ($inop && $lasttwo=='))')) {
					$qcond = substr($qcond, 0, strlen($qcond)-1);
					$endgroup = true;
				}
				__FQNExtendedQueryAddCondition($queryGenerator, $qcond, $glue, $mainModule, $fieldcolumn, $user);
				$glue = $queryGenerator::$OR;
				$qc = trim(substr($qc, $posor+4));
			} else {
				$qcond = trim(substr($qc, 0, $posand));
				if ((!$inop && substr($qcond, -1)==')') || ($inop && $lasttwo=='))')) {
					$qcond = substr($qcond, 0, strlen($qcond)-1);
					$endgroup = true;
				}
				__FQNExtendedQueryAddCondition($queryGenerator, $qcond, $glue, $mainModule, $fieldcolumn, $user);
				$glue = $queryGenerator::$AND;
				$qc = trim(substr($qc, $posand+5));
			}
			if ($endgroup) {
				$queryGenerator->endGroup();
			}
			if (substr($qc, 0, 1)=='(') {
				$queryGenerator->startGroup($glue);
				$glue = '';
				$qc = substr($qc, 1);
			}
			$posand = stripos($qc, ' and ');
			$posor = stripos($qc, ' or ');
		}
		$queryGenerator->endGroup();
	}
	$query = 'select ';
	if ($countSelect) {
		$query .= 'count(*) ';
	} else {
		$query .= $queryGenerator->getSelectClauseColumnSQL().' ';
	}
	$query .= $queryGenerator->getFromClause().' ';
	$query .= $queryGenerator->getWhereClause().' ';
	// limit and order
	if (!empty($obflds)) {
		$obflds = trim($obflds);
		if (strtolower(substr($obflds, -3))=='asc') {
			$dir = ' asc ';
			$obflds = trim(substr($obflds, 0, strlen($obflds)-3));
		} elseif (strtolower(substr($obflds, -4))=='desc') {
			$dir = ' desc ';
			$obflds = trim(substr($obflds, 0, strlen($obflds)-4));
		} else {
			$dir = '';
		}
		$obflds = explode(',', $obflds);
		foreach ($obflds as $k => $field) {
			$obflds[$k] = __FQNExtendedQueryField2Column($field, $mainModule, $fieldcolumn, $user);
		}
		$query .= ' order by '.implode(',', $obflds).$dir.' ';
	}
	if (!empty($lmoc)) {
		$query .= " limit $lmoc ";
	}
	return array($query,$queryRelatedModules);
}

function __FQNExtendedQueryAddCondition($queryGenerator, $condition, $glue, $mainModule, $maincolumnTable, $user) {
	global $adb, $log;
	// field operator value
	// conditionals: condition operations or in clauses or like clauses
	// conditional operators: <, >, <=, >=, =, !=.
	// in clauses: in (fieldlist).
	// like clauses: like 'sqlregex'.
	// value list: a comma separated list of values.
	$condition = trim($condition);
	$condition = __FQNExtendedQueryProcessCondition($condition);
	$field = strtok($condition, ' ');
	$op = strtolower(strtok(' '));
	$secop = strtolower(strtok(' '));
	if ($op == 'not' && strtolower($secop)=='like') {
		$val = substr($condition, stripos($condition, 'not like')+8);
		$op = 'notlike';
	} elseif ($op == 'not' && strtolower($secop)=='in') {
		$val = substr($condition, stripos($condition, 'not in')+6);
		$op = 'notin';
	} elseif ($op == 'is') {
		if ($secop == 'not') {
			$val = '';
			strtok(' '); // consume the 'null'
			$op = 'isnotnull';
		}
		if ($secop == 'null') {
			$val = '';
			$op = 'isnull';
		}
	} else {
		if ($op=='like') {
			$val = substr($condition, stripos($condition, ' '.$op)+strlen(' '.$op));
		} else {
			$val = substr($condition, stripos($condition, $op)+strlen($op));
		}
	}
	$val = trim($val);
	$val = trim($val, "'");
	$val = str_replace("''", "'", $val);
	// TODO  add query generator operators for 'bw' = BETWEEN value1 and value2  (between two dates)
	switch ($op) {
		case '<':
			if (strtotime($val)) { // is date type
				$op = 'b';
			} else {
				$op = 'l';
			}
			break;
		case '>':
			if (strtotime($val)) { // is date type
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
		case 'notin':
			$op = ($op=='notin' ? 'ni' : 'i');
			$val = ltrim($val, '(');
			$val = rtrim($val, ')');
			$val = explode(',', $val);
			array_walk($val, function (&$elemento, $clave) {
				$elemento = trim($elemento, "'");
			});
			break;
		case 'like':
			if (substr($val, -1)=='%' && substr($val, 0, 1)=='%') {
				$op = 'c';
			} elseif (substr($val, 0, 1)=='%') {
				$op = 'ew';
			} else {
				$op = 's';
			}
			$val = str_replace('%', '', $val);
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
		$fromwebserviceObject = VtigerWebserviceObject::fromName($adb, $mainModule);
		$fromhandlerPath = $fromwebserviceObject->getHandlerPath();
		$fromhandlerClass = $fromwebserviceObject->getHandlerClass();
		require_once $fromhandlerPath;
		$fromhandler = new $fromhandlerClass($fromwebserviceObject, $user, $adb, $log);
		$fromrelmeta = $fromhandler->getMeta();
		$fromrfs = $fromrelmeta->getReferenceFieldDetails();
		$webserviceObject = VtigerWebserviceObject::fromName($adb, $fmod);
		$handlerPath = $webserviceObject->getHandlerPath();
		$handlerClass = $webserviceObject->getHandlerClass();
		require_once $handlerPath;
		$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
		$relmeta = $handler->getMeta();
		$fmod = $relmeta->getTabName();  // normalize module name
		$found = false;
		foreach ($fromrfs as $reffld => $mods) {
			if (in_array($fmod, $mods)) {
				$found = true;
				if ($fname=='id') {
					list($wsid,$val) = explode('x', $val);
					//$fname = $relmeta->getObectIndexColumn();
				}
				$fmodreffld = __FQNExtendedQueryGetRefFieldForModule($fromrfs, $fmod, $reffld);
				$queryGenerator->addReferenceModuleFieldCondition($fmod, $fmodreffld, $fname, $val, $op, $glue);
				break;
			}
		}
		if (!$found && $fmod==$mainModule) { // didn't find the field on the relation so we try to find it on the main module
			if ($fname=='id') {
				list($wsid,$val) = explode('x', $val);
			}
			$queryGenerator->addCondition($fname, $val, $op, $glue);
		}
	} else {
		if ($field=='id') {
			if (is_array($val)) {
				array_walk($val, function (&$elemento, $clave) {
					$elemento = trim($elemento, "'");
					list($void,$elemento) = explode('x', $elemento);
				});
			} else {
				list($void,$val) = explode('x', $val);
			}
		}
		$queryGenerator->addCondition($field, $val, $op, $glue);
	}
}

function __FQNExtendedQueryGetRefFieldForModule($fromrfs, $module, $reffld) {
	foreach ($fromrfs as $freffld => $mods) {
		if (in_array($module, $mods)) {
			$reffld = $freffld;
			break;
		}
	}
	return $reffld;
}

function __FQNExtendedQueryField2Column($field, $mainModule, $maincolumnTable, $user) {
	global $adb,$log;
	$field = trim($field);
	if (isset($maincolumnTable[$field])) {
		return $maincolumnTable[$field];
	}
	if (strpos($field, '.')>0) {  // FQN
		list($fmod,$fname) = explode('.', $field);
		$fromwebserviceObject = VtigerWebserviceObject::fromName($adb, $mainModule);
		$fromhandlerPath = $fromwebserviceObject->getHandlerPath();
		$fromhandlerClass = $fromwebserviceObject->getHandlerClass();
		require_once $fromhandlerPath;
		$fromhandler = new $fromhandlerClass($fromwebserviceObject, $user, $adb, $log);
		$fromrelmeta = $fromhandler->getMeta();
		$fromrfs = $fromrelmeta->getReferenceFieldDetails();
		$webserviceObject = VtigerWebserviceObject::fromName($adb, $fmod);
		$handlerPath = $webserviceObject->getHandlerPath();
		$handlerClass = $webserviceObject->getHandlerClass();
		require_once $handlerPath;
		$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
		$relmeta = $handler->getMeta();
		$fieldcolumn = $relmeta->getFieldColumnMapping();
		$fieldtable = $relmeta->getColumnTableMapping();
		$fmod = $relmeta->getTabName();  // normalize module name
		if ($fmod==$mainModule) {
			return $fieldtable[$fname].'.'.$maincolumnTable[$fname];
		} else {
			$fmodreffld = __FQNExtendedQueryGetRefFieldForModule($fromrfs, $fmod, $fname);
			return $fieldtable[$fname].$fmodreffld.'.'.$fieldcolumn[$fname];
		}
	}
	return $field;
}

function __FQNExtendedQueryProcessCondition($condition) {
	// add spaces in front and back of operator
	$condlen = strlen($condition);
	$foundop = false;
	$chrs = 0;
	$cond = '';
	while (!$foundop && $chrs < $condlen) {
		$foundop = in_array($condition[$chrs], array('<','>','=','!'));
		if ($foundop) {
			if ($condition[$chrs-1]!=' ') {
				$cond .= ' '.$condition[$chrs];
			} else {
				$cond .= $condition[$chrs];
			}
			while ($condition[$chrs+1]==' ') {
				$chrs++;
			}
			if ($condition[$chrs+1]=='=') {
				$cond .= '=';
				if ($condition[$chrs+2]!=' ') {
					$cond .= ' ';
				}
				$chrs++;
			} else {
				if ($condition[$chrs+1]!=' ') {
					$cond .= ' ';
				}
			}
		} else {
			$cond .= $condition[$chrs];
		}
		$chrs++;
	}
	$cond .= substr($condition, $chrs);
	return $cond;
}

function __FQNExtendedQueryIsFQNQuery($q) {
	$q = strtolower($q);
	$notinopRegex = "/\s+not\s+in\s+\(/";
	preg_match($notinopRegex, $q, $qop);
	if (count($qop)>0) {
		return true;  // "not in" operator is supported by QG
	}
	$cq = __FQNExtendedQueryCleanQuery($q);
	$isnotnullopRegex = "/\s+is\s+(not\s+)?null/";
	preg_match($isnotnullopRegex, $cq, $qop);
	if (count($qop)>0) {
		return true;  // "is not null" operator is supported by QG
	}
	return (strpos($cq, '.')>0 || strpos($cq, '(')>0);
}

function __FQNExtendedQueryIsRelatedQuery($q) {
	$q = strtolower($q);
	$cq = __FQNExtendedQueryCleanQuery($q);
	$cq = substr($cq, stripos($cq, ' where '));
	return (stripos($cq, 'related.')>0);
}

/*
 * param $q SQL command to analyze. MUST be in lower case
 */
function __FQNExtendedQueryCleanQuery($q) {
	$moduleRegex = "/ in \(.+\)/Us";  // eliminate IN operator
	$r = preg_replace($moduleRegex, '', $q);
	$moduleRegex = "/ count\(.+\)/Us";  // eliminate COUNT operator
	$r = preg_replace($moduleRegex, '', $r);
	$moduleRegex = "/'.+'/Us";  // eliminate string literals
	$r = preg_replace($moduleRegex, '', $r);
	$moduleRegex = "/\(\s*\)/Us";  // eliminate empty parenthesis
	$r = preg_replace($moduleRegex, '', $r);
	return $r;
}

function __ExtendedQueryConditionQuery($q) {
	preg_match('/\s*select\s+\[/i', $q, $sop);
	preg_match('/ where\s+\[/i', $q, $qop);
	return (count($qop)>0 || count($sop)>0);
}

function __ExtendedQueryConditionGetQuery($q, $fromModule, $user) {
	global $adb;
	$workflowScheduler = new WorkFlowScheduler($adb);
	$workflow = new Workflow();
	$wfvals = array(
		'workflow_id' => 0,
		'module_name' => $fromModule,
		'summary' => '',
		'test' => '',
		'execution_condition' => 6, // VTWorkflowManager::$ON_SCHEDULE
		'schtypeid' => '',
		'schtime' => '08:08:08',
		'schdayofmonth' => '[3]',
		'schdayofweek' => '[3]',
		'schannualdates' => '["2018-10-08"]',
		'schminuteinterval' => '3',
		'defaultworkflow' => 0,
		'nexttrigger_time' => '',
	);
	$hasGroupBy = (stripos($q, 'group by')>0);
	preg_match('/select\s+\[/i', $q, $selectSyntaxMatches);
	if (count($selectSyntaxMatches) == 0) {
		$queryColumns = trim(substr($q, 6, stripos($q, ' from ')-5));
		$queryColumns = explode(',', $queryColumns);
		$queryColumns = array_map('trim', $queryColumns);
		$queryRelatedModules = array();
		foreach ($queryColumns as $k => $field) {
			if (strpos($field, '.')>0) {
				list($m,$f) = explode('.', $field);
				if (!isset($queryRelatedModules[$m])) {
					$relhandler = vtws_getModuleHandlerFromName($m, $user);
					$relmeta = $relhandler->getMeta();
					$mn = $relmeta->getTabName();  // normalize module name
					$queryRelatedModules[$mn] = $relmeta;
					if ($m!=$mn) {
						$queryColumns[$k] = $mn.'.'.$f;
					}
				}
			}
		}
		if (!in_array('id', $queryColumns) && !$hasGroupBy) {
			$queryColumns[] = 'id';  // add ID column to follow REST interface behaviour
		}
	} else {
		$queryColumns = [];
		$queryRelatedModules = [];

		$selectExpressions = substr($q, stripos($q, 'select') + 6, stripos($q, ' from ') - 6);
		$selectExpressions = trim($selectExpressions);
		$selectExpressions = trim($selectExpressions, ';');

		$wfvals['select_expressions'] = $selectExpressions;
	}
	preg_match('/ where\s+\[/i', $q, $qop);
	if (count($qop)>0) {
		$startcond = stripos($q, ' where ')+7;
		$endcond = strrpos($q, ']')+1;
		$cond = substr($q, $startcond, $endcond-$startcond);
		$cond = trim($cond);
		$cond = trim($cond, ';');
		$ol_by = substr($q, $endcond);
		$ol_by = trim($ol_by);
		$ol_by = ' '.trim($ol_by, ';');
		$wfvals['test'] = $cond;
	} else {
		$wfvals['test'] = '';
		preg_match('/ from\s+\w+\s+(.*)/i', $q, $qfrom);
		if (isset($qfrom[1])) {
			$ol_by = ' '.$qfrom[1];
		} else {
			$ol_by = '';
		}
	}
	$workflow->setup($wfvals);
	return array(trim($workflowScheduler->getWorkflowQuery($workflow, $queryColumns, !$hasGroupBy, $user).$ol_by), $queryRelatedModules);
}
