<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'data/CRMEntity.php';
require_once 'data/Tracker.php';
include_once 'vtlib/Vtiger/Utils/StringTemplate.php';
include_once 'vtlib/Vtiger/LinkData.php';
use \PHPSQLParser\PHPSQLParser;
use \PHPSQLParser\utils\ExpressionType;

class cbQuestion extends CRMEntity {
	public $table_name = 'vtiger_cbquestion';
	public $table_index= 'cbquestionid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'custom', 'containerClass' => 'slds-icon_container slds-icon-custom-custom102', 'class' => 'slds-icon', 'icon'=>'custom102');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_cbquestioncf', 'cbquestionid');
	// related_tables variable should define the association (relation) between dependent tables
	// FORMAT: related_tablename => array(related_tablename_column[, base_tablename, base_tablename_column[, related_module]] )
	// Here base_tablename_column should establish relation with related_tablename_column
	// NOTE: If base_tablename and base_tablename_column are not specified, it will default to modules (table_name, related_tablename_column)
	// Uncomment the line below to support custom field columns on related lists
	// public $related_tables = array('vtiger_MODULE_NAME_LOWERCASEcf' => array('MODULE_NAME_LOWERCASEid', 'vtiger_MODULE_NAME_LOWERCASE', 'MODULE_NAME_LOWERCASEid', 'MODULE_NAME_LOWERCASE'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_cbquestion', 'vtiger_cbquestioncf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_cbquestion'   => 'cbquestionid',
		'vtiger_cbquestioncf' => 'cbquestionid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'qname'=> array('cbquestion' => 'qname'),
		'qtype'=> array('cbquestion' => 'qtype'),
		'qcollection'=> array('cbquestion' => 'qcollection'),
		'qstatus'=> array('cbquestion' => 'qstatus'),
		'qmodule'=> array('cbquestion' => 'qmodule'),
		'Assigned To' => array('crmentity' => 'smownerid')
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'qname'=> 'qname',
		'qtype'=> 'qtype',
		'qcollection'=> 'qcollection',
		'qstatus'=> 'qstatus',
		'qmodule'=> 'qmodule',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'qname';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'qname'=> array('cbquestion' => 'qname'),
		'qtype'=> array('cbquestion' => 'qtype'),
		'qcollection'=> array('cbquestion' => 'qcollection'),
		'qstatus'=> array('cbquestion' => 'qstatus'),
		'qmodule'=> array('cbquestion' => 'qmodule'),
		'Assigned To' => array('crmentity' => 'smownerid')
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'qname'=> 'qname',
		'qtype'=> 'qtype',
		'qcollection'=> 'qcollection',
		'qstatus'=> 'qstatus',
		'qmodule'=> 'qmodule',
		'Assigned To' => 'assigned_user_id'
	);

	// For Popup window record selection
	public $popup_fields = array('qname');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'qname';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'qname';

	// Required Information for enabling Import feature
	public $required_fields = array('qname'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'qname';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'qname');

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param string Module name
	 * @param string Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		if ($event_type == 'module.postinstall') {
			// Handle post installation actions
			$this->setModuleSeqNumber('configure', $modulename, 'cbQ-', '0000001');
		} elseif ($event_type == 'module.disabled') {
			// Handle actions when this module is disabled.
		} elseif ($event_type == 'module.enabled') {
			// Handle actions when this module is enabled.
		} elseif ($event_type == 'module.preuninstall') {
			// Handle actions when this module is about to be deleted.
		} elseif ($event_type == 'module.preupdate') {
			// Handle actions before this module is updated.
		} elseif ($event_type == 'module.postupdate') {
			// Handle actions after this module is updated.
		}
	}

	public static function getSQL($qid, $params = array()) {
		global $current_user, $adb, $log;
		$q = new cbQuestion();
		if (empty($qid) && !empty($params['cbQuestionRecord']) && is_array($params['cbQuestionRecord'])) {
			$q->column_fields = $params['cbQuestionRecord'];
			if (isset($params['cbQuestionContext'])) {
				$qctx = $params['cbQuestionContext'];
				unset($params['cbQuestionContext']);
				$params = array_merge($params, $qctx);
			}
		} else {
			$q->retrieve_entity_info($qid, 'cbQuestion');
		}
		if (isset($params['cbQuestionRecord'])) {
			unset($params['cbQuestionRecord']);
		}
		$q->id = (empty($q->column_fields['record_id']) ? 0 : $q->column_fields['record_id']);
		if (empty($q->id) || isPermitted('cbQuestion', 'DetailView', $q->id) != 'yes') {
			return getTranslatedString('SQLError', 'cbQuestion').': PERMISSION';
		}
		if ($q->column_fields['qtype']=='Global Search') {
			return 'select "<b>Global Search</b>";';
		}
		include_once 'include/Webservices/Query.php';
		include_once 'include/Webservices/VtigerModuleOperation.php';
		if ($q->column_fields['sqlquery']=='1') {
			$mod = CRMEntity::getInstance($q->column_fields['qmodule']);
			$query = 'SELECT '.decode_html($q->column_fields['qcolumns']).' FROM '.$mod->table_name.' ';
			if (!empty($q->column_fields['qcondition'])) {
				$conds = decode_html($q->column_fields['qcondition']);
				$queryparams = 'set ';
				$paramcount = 1;
				$qpprefix = '@qp'.time();
				foreach ($params as $param => $value) {
					$qp = $qpprefix.$paramcount;
					$paramcount++;
					$queryparams.= $adb->convert2Sql(" $qp = ?,", [$value]);
					$conds = str_replace(["'$param'", '"'.$param.'"', $param], $qp, $conds);
				}
				$queryparams = trim($queryparams, ',');
				if (!empty($params)) {
					$adb->query($queryparams);
				}
				if ($q->column_fields['condfilterformat']=='1') { // filter conditions
					$queryGenerator = new QueryGenerator($q->column_fields['qmodule'], $current_user);
					$fields = array();
					$cols = explode(',', decode_html(str_replace(' ', '', $q->column_fields['qcolumns'])));
					foreach ($cols as $col) {
						if (strpos($col, '.')) {
							list($t, $col) = explode('.', $col);
						}
						$fields[] = $col;
					}
					$queryGenerator->setFields($fields);
					$conds = json_decode($conds, true);
					$conditions = $queryGenerator->constructAdvancedSearchConditions($q->column_fields['qmodule'], $conds);
					$queryGenerator->addUserSearchConditions($conditions);
					$query = $queryGenerator->getQuery();
				} else {
					$query .= $conds;
				}
			}
			if (!empty($q->column_fields['groupby'])) {
				$query .= ' GROUP BY '.$q->column_fields['groupby'];
			}
			if (!empty($q->column_fields['orderby'])) {
				$query .= ' ORDER BY '.$q->column_fields['orderby'];
			}
			if (!empty($q->column_fields['qpagesize'])) {
				$query .= ' LIMIT '.$q->column_fields['qpagesize'];
			}
			$query .= ';';
		} else {
			$chkrs = $adb->pquery(
				'SELECT 1 FROM (select name from `vtiger_ws_entity` UNION select name from vtiger_tab) as tnames where name=?',
				array($q->column_fields['qmodule'])
			);
			if (!$chkrs || $adb->num_rows($chkrs)==0) {
				return getTranslatedString('SQLError', 'cbQuestion').': <b>Incorrect module name.</b>';
			}
			$query = 'SELECT '.decode_html($q->column_fields['qcolumns']).' FROM '.decode_html($q->column_fields['qmodule']);
			if (!empty($q->column_fields['qcondition'])) {
				$conds = decode_html($q->column_fields['qcondition']);
				if (!empty($_REQUEST['cbQuestionRecord'])) {
					$context_variable = vtlib_purify(json_decode(urldecode($_REQUEST['cbQuestionRecord']), true));
					if (isset($context_variable['context_variable'])) {
						foreach ($context_variable['context_variable'] as $value) {
							$conds = str_replace($value['variable'], $value['value'], $conds);
						}
					}
				}
				if (!empty($params) && is_array($params)) {
					foreach ($params as $param => $value) {
						$conds = str_replace($param, $value, $conds);
					}
				}
				$query .= ' WHERE '.$conds;
			}
			if (!empty($q->column_fields['groupby'])) {
				$query .= ' GROUP BY '.$q->column_fields['groupby'];
			}
			if (!empty($q->column_fields['orderby'])) {
				$query .= ' ORDER BY '.$q->column_fields['orderby'];
			}
			if (!empty($q->column_fields['qpagesize'])) {
				$query .= ' LIMIT '.$q->column_fields['qpagesize'];
			}
			$query .= ';';
			try {
				$webserviceObject = VtigerWebserviceObject::fromQuery($adb, $query);
				$handlerPath = $webserviceObject->getHandlerPath();
				$handlerClass = $webserviceObject->getHandlerClass();
				require_once $handlerPath;
				$handler = new $handlerClass($webserviceObject, $current_user, $adb, $log);
				$query = $handler->wsVTQL2SQL($query, $meta, $queryRelatedModules);
			} catch (Exception $e) {
				return getTranslatedString('SQLError', 'cbQuestion').': '.$query;
			}
		}
		return $query;
	}

	public static function getAnswer($qid, $params = array()) {
		global $current_user, $default_charset, $adb, $log;
		$q = new cbQuestion();
		if (empty($qid) && !empty($params['cbQuestionRecord']) && is_array($params['cbQuestionRecord'])) {
			$q->column_fields = $params['cbQuestionRecord'];
			if (isset($params['cbQuestionContext'])) {
				$qctx = $params['cbQuestionContext'];
				unset($params['cbQuestionContext']);
				$params = array_merge($params, $qctx);
			}
		} else {
			$q->retrieve_entity_info($qid, 'cbQuestion');
		}
		$q->id = (empty($q->column_fields['record_id']) ? 0 : $q->column_fields['record_id']);
		if (empty($q->id) || isPermitted('cbQuestion', 'DetailView', $q->id) != 'yes') {
			return array('type' => 'ERROR', 'answer' => 'PERMISSION');
		}
		if ($q->column_fields['qtype']=='Mermaid') {
			$graph = 'LR'; // default graph
			$propertyody = json_decode(html_entity_decode($q->column_fields['typeprops']));
			$nodeStyle = '';
			$linkStyle = '';
			if ($propertyody != null) {
				$graph = $propertyody->graph;
				if (!empty($params) && isset($params['states'])) {
					include_once 'modules/cbMap/cbRule.php';
					$record_id = $params['recordid'];
					$states = $params['states'];
					// style nodes
					$defaultstyleaccepted = $defaultstylerejected = '';
					if (isset($propertyody->defaults) && !empty($propertyody->defaults->nodestyleaccepted)) {
						$defaultstyleaccepted = $propertyody->defaults->nodestyleaccepted;
					}
					if (isset($propertyody->defaults) && !empty($propertyody->defaults->nodestylerejected)) {
						$defaultstylerejected = $propertyody->defaults->nodestylerejected;
					}
					foreach ($propertyody->nodes as $nodeob) {
						for ($x = 0; $x < count($states); $x++) {
							$fromstate = $states[$x]['from'];
							$tostate = $states[$x]['to'];
							if ($fromstate == $nodeob->nodestate) {
								try {
									if (!empty($nodeob->condition)
										&& coreBOS_Rule::evaluate($nodeob->condition, array('record_id'=>$record_id, 'fromnode'=>$fromstate, 'tonode'=>$tostate))
									) {
										$style2apply = empty($nodeob->nodestyleaccepted) ? $defaultstyleaccepted : $nodeob->nodestyleaccepted;
									} else {
										$style2apply = empty($nodeob->nodestylerejected) ? $defaultstylerejected : $nodeob->nodestylerejected;
									}
								} catch (Exception $e) {
									$style2apply = empty($nodeob->nodestylerejected) ? $defaultstylerejected : $nodeob->nodestylerejected;
								}
								if (!empty($style2apply)) {
									$nodeStyle .= ' style '.$nodeob->nodename .' '.$style2apply."\n";
								}
							}
						}
					}
					// style TO only states
					$fromarray = array_map(
						function ($e) {
							return $e['from'];
						},
						$states
					);
					$toarray = array_map(
						function ($e) {
							return $e['to'];
						},
						$states
					);
					$toarray = array_diff($toarray, $fromarray);
					foreach ($toarray as $endstate) {
						foreach ($propertyody->nodes as $nodeob) {
							if ($endstate == $nodeob->nodestate) {
								$nodeStyle .= ' style '.$nodeob->nodename .' '.$defaultstyleaccepted."\n";
							}
						}
					}
					// style Links
					$defaultstyleaccepted = $defaultstylerejected = '';
					if (isset($propertyody->defaults) && !empty($propertyody->defaults->linkstyleaccepted)) {
						$defaultstyleaccepted = $propertyody->defaults->linkstyleaccepted;
					}
					if (isset($propertyody->defaults) && !empty($propertyody->defaults->linkstylerejected)) {
						$defaultstylerejected = $propertyody->defaults->linkstylerejected;
					}
					foreach ($propertyody->links as $linksob) {
						for ($x = 0; $x < count($states); $x++) {
							$link_fromstate = $states[$x]['from'];
							$link_tostate = $states[$x]['to'];
							if ($linksob->from == $link_fromstate && $linksob->to == $link_tostate) {
								try {
									if (!empty($linksob->condition)
										&& coreBOS_Rule::evaluate($linksob->condition, array('record_id'=>$record_id,'fromnode'=>$link_fromstate,'tonode'=>$link_tostate))
									) {
										$style2apply = empty($linksob->linkstyleaccepted) ? $defaultstyleaccepted : $linksob->linkstyleaccepted;
									} else {
										$style2apply = empty($linksob->linkstylerejected) ? $defaultstylerejected : $linksob->linkstylerejected;
									}
								} catch (Exception $e) {
									$style2apply = empty($linksob->linkstylerejected) ? $defaultstylerejected : $linksob->linkstylerejected;
								}
								if (!empty($style2apply)) {
									$linkStyle .= ' linkStyle '.$linksob->position .' '.$style2apply."\n";
								}
							}
						}
					}
				}
			}
			return array(
				'columns' => html_entity_decode($q->column_fields['qcolumns'], ENT_QUOTES, $default_charset),
				'title' => html_entity_decode($q->column_fields['qname'], ENT_QUOTES, $default_charset),
				'type' => html_entity_decode($q->column_fields['qtype'], ENT_QUOTES, $default_charset),
				'properties' => $graph,
				'answer' => 'graph '.$graph."\n\n".html_entity_decode($q->column_fields['qcolumns'], ENT_QUOTES, $default_charset)."\n".$nodeStyle. "\n".$linkStyle,
			);
		} elseif ($q->column_fields['qtype']=='Global Search') {
			include_once 'include/Webservices/CustomerPortalWS.php';
			$propsjson = preg_replace("/[\n\r\s]+/", ' ', html_entity_decode($q->column_fields['typeprops'], ENT_QUOTES, $default_charset));
			$props = json_decode($propsjson, true);
			$restrictionids = array();
			if (!empty($props['user'])) {
				$restrictionids['userId'] = vtws_getWSID($props['user']);
			}
			if (!empty($props['account'])) {
				$restrictionids['accountId'] = vtws_getWSID($props['account']);
			}
			if (!empty($props['contact'])) {
				$restrictionids['contactId'] = vtws_getWSID($props['contact']);
			}
			return array(
				'columns' => html_entity_decode($q->column_fields['qcolumns'], ENT_QUOTES, $default_charset),
				'title' => html_entity_decode($q->column_fields['qname'], ENT_QUOTES, $default_charset),
				'type' => html_entity_decode($q->column_fields['qtype'], ENT_QUOTES, $default_charset),
				'properties' => $propsjson,
				'answer' => cbwsgetSearchResultsWithTotals($props['query'], $props['searchin'], $restrictionids, $current_user),
			);
		} else {
			include_once 'include/Webservices/Query.php';
			if ($q->column_fields['sqlquery']=='0') {
				$webserviceObject = VtigerWebserviceObject::fromName($adb, $q->column_fields['qmodule']);
				$handlerPath = $webserviceObject->getHandlerPath();
				$handlerClass = $webserviceObject->getHandlerClass();
				require_once $handlerPath;
				$handler = new $handlerClass($webserviceObject, $current_user, $adb, $log);
				$meta = $handler->getMeta();
				$queryRelatedModules = array();
				$sql_query = cbQuestion::getSQL($qid, $params);
				return array(
					'module' => $q->column_fields['qmodule'],
					'columns' =>  html_entity_decode($q->column_fields['qcolumns'], ENT_QUOTES, $default_charset),
					'title' => html_entity_decode($q->column_fields['qname'], ENT_QUOTES, $default_charset),
					'type' => html_entity_decode($q->column_fields['qtype'], ENT_QUOTES, $default_charset),
					'properties' => html_entity_decode($q->column_fields['typeprops'], ENT_QUOTES, $default_charset),
					'answer' => $handler->querySQLResults($sql_query, ' not in ', $meta, $queryRelatedModules),
				);
			} else {
				require_once 'include/Webservices/GetExtendedQuery.php';
				$handler = vtws_getModuleHandlerFromName($q->column_fields['qmodule'], $current_user);
				$meta = $handler->getMeta();
				$queryRelatedModules = array(); // this has to be filled in with all the related modules in the query
				$webserviceObject = VtigerWebserviceObject::fromName($adb, $q->column_fields['qmodule']);
				$modOp = new VtigerModuleOperation($webserviceObject, $current_user, $adb, $log);
				$sql_query = cbQuestion::getSQL($qid, $params);
				$sql_question_context_variable = json_decode($q->column_fields['typeprops']);
				if ($sql_question_context_variable) {
					$context_var_array = (array) $sql_question_context_variable->context_variables;
					if (!empty($context_var_array)) {
						foreach ($context_var_array as $key => $value) {
							$sql_query = str_replace($key, $value, $sql_query);
						}
					}
				}
				if (!empty(coreBOS_Session::get('authenticatedUserIsPortalUser', false))) {
					$contactId = coreBOS_Session::get('authenticatedUserPortalContact', 0);
					if (empty($contactId)) {
						$sql_query = 'select 1';
					} else {
						$accountId = getSingleFieldValue('vtiger_contactdetails', 'accountid', 'contactid', $contactId);
						$sql_query = addPortalModuleRestrictions($sql_query, $meta->getEntityName(), $accountId, $contactId);
					}
				}
				return array(
					'module' => $q->column_fields['qmodule'],
					'columns' => $q->column_fields['qcolumns'],
					'title' => html_entity_decode($q->column_fields['qname'], ENT_QUOTES, $default_charset),
					'type' => html_entity_decode($q->column_fields['qtype'], ENT_QUOTES, $default_charset),
					'properties' => html_entity_decode($q->column_fields['typeprops'], ENT_QUOTES, $default_charset),
					'answer' => $modOp->querySQLResults($sql_query, ' not in ', $meta, $queryRelatedModules),
				);
			}
		}
	}

	public static function getFormattedAnswer($qid, $params = array()) {
		$ans = self::getAnswer($qid, $params);
		switch ($ans['type']) {
			case 'File':
				$ret = self::getFileFromAnswer($ans, $params);
				break;
			case 'Table':
				$ret = self::getTableFromAnswer($ans);
				break;
			case 'Grid':
				$ret = self::getGridFromAnswer($qid, $params);
				break;
			case 'Number':
				$ret = array_pop($ans['answer'][0]);
				break;
			case 'Global Search':
				$ret = array_pop($ans['answer']['records']);
				break;
			case 'Pie':
				$ret = self::getChartFromAnswer($ans);
				break;
			case 'Mermaid':
				$ret = '<div class="mermaid" name="cbqm'.$qid.'">'.$ans['answer'].'</div>
				<script src="modules/cbQuestion/resources/mermaid.min.js"></script>
				<script>document.addEventListener("DOMContentLoaded", function(event) {
					mermaid.initialize({
						securityLevel: "loose"
					});
					mermaid.init();
				});
				mermaid.initialize({
					securityLevel: "loose"
				});
				mermaid.init();
				</script>';
				break;
			case 'ERROR':
			default:
				$ret = getTranslatedString('LBL_PERMISSION');
		}
		return $ret;
	}

	/**
	 * properties: see wiki for the latest definition
	 */
	public static function getFileFromAnswer($ans, $params = array()) {
		$bqfiles = 'cache/bqfiles';
		if (!is_dir($bqfiles)) {
			mkdir($bqfiles, 0777, true);
		}
		$fname = '';
		if (!empty($ans)) {
			$properties = json_decode($ans['properties']);
			if (!empty($properties->filename)) {
				$fname = utf8_decode($properties->filename);
				if (!empty($params) && is_array($params)) {
					$strtemplate = new Vtiger_StringTemplate();
					foreach ($params as $key => $value) {
						$strtemplate->assign($key, $value);
					}
					$fname = $strtemplate->merge($fname);
				}
				if (empty($properties->filenamedateformat)) {
					$now = date('YmdHis');
				} else {
					$now = date($properties->filenamedateformat);
				}
				$fname = preg_replace('/[^a-zA-Z0-9_\.\%]/', '', $fname);
				if (strpos($fname, '%s')===false) {
					$fname .= '_%s';
				} else {
					$fname = suppressAllButFirst('%s', $fname);
				}
				$fname = $bqfiles.'/'.sprintf($fname, $now);
			} else {
				$fname = tempnam($bqfiles, 'bq');
			}
			$fp = fopen($fname, 'w');
			$delim = empty($properties->delimiter) ? ',' : $properties->delimiter;
			$encls = empty($properties->enclosure) ? '"' : $properties->enclosure;
			$alllabels = array();
			$alltypes = array();
			$rowlabels = !empty($ans['answer'][0]) ? array_keys($ans['answer'][0]) : array();
			if (empty($rowlabels) && !empty($properties->columns)) {
				for ($i=0; $i < count($properties->columns); $i++) {
					$alllabels[] = $properties->columns[$i]->label;
				}
				$line = self::generateCSV($alllabels, $delim, $encls);
			}
			$ls = 0;
			foreach ($rowlabels as $label) {
				$alltypes[$label] = empty($properties->columns[$ls]->type) ? 'string' : $properties->columns[$ls]->type;
				$alllabels[] = empty($properties->columns[$ls]->label) ? $label : $properties->columns[$ls]->label;
				$ls++;
			}
			if (!empty($alllabels)) {
				$line = self::generateCSV($alllabels, $delim, $encls);
				if (isset($properties->postprocess)) {
					$line = self::postProcessFileLine($line, $properties->postprocess);
				}
				fputs($fp, $line);
			}
			foreach ($ans['answer'] as $row) {
				foreach ($row as $label => $value) {
					if ($alltypes[$label]!='string' && !empty($value)) {
						$type = $alltypes[$label];
						if (!empty($properties->format->$type)) {
							$row[$label] = self::getFormattedValue($value, $type, $properties->format->$type);
						}
					}
				}
				$line = self::generateCSV($row, $delim, $encls);
				if (isset($properties->postprocess)) {
					$line = self::postProcessFileLine($line, $properties->postprocess);
				}
				fputs($fp, $line);
			}
			fclose($fp);
		}
		return $fname;
	}

	public static function postProcessFileLine($line, $actions) {
		$postprocess = explode(',', $actions);
		foreach ($postprocess as $process) {
			switch ($process) {
				case 'deletedoublequotes':
					$line = str_replace('\"', '\ç', $line);
					$line = str_replace('"', '', $line);
					$line = str_replace('\ç', '\"', $line);
					break;
				default:
					break;
			}
		}
		return $line;
	}

	public static function getTableFromAnswer($ans) {
		$table = '';
		if (!empty($ans)) {
			$answer = $ans['answer'];
			$module = $ans['module'];
			$properties = json_decode($ans['properties']);
			$columnLabels = empty($properties->columnlabels) ? array() : $properties->columnlabels;
			$limit = GlobalVariable::getVariable('BusinessQuestion_TableAnswer_Limit', 2000);
			$table .= '<table>';
			$table .= '<tr>';
			foreach ($columnLabels as $columnLabel) {
				$table .= '<th>'.getTranslatedString($columnLabel, $module).'</th>';
			}
			$table .= '</tr>';
			for ($x = 0; $x < $limit; $x++) {
				if (isset($answer[$x])) {
					$table .= '<tr>';
					foreach ($answer[$x] as $columnValue) {
						$table .= '<td>'.$columnValue.'</td>';
					}
					$table .= '</tr>';
				}
			}
			$table .= '</table>';
		}
		return $table;
	}

	public static function getGridFromAnswer($qid, $params) {
		$smarty = new vtigerCRM_Smarty();
		$properties = json_encode(cbQuestion::getQuestionProperties($qid));
		$smarty->assign('Properties', $properties);
		$smarty->assign('QuestionID', $qid);
		$smarty->assign('RecordID', $params['$RECORD$']);
		$smarty->assign('RowsperPage', GlobalVariable::getVariable('MasterDetail_Pagination', 40));
		$smarty->display('modules/cbQuestion/Grid.tpl');
	}

	public static function getChartFromAnswer($ans) {
		$chart = '';
		if (!empty($ans)) {
			$title = $ans['title'];
			$answer = $ans['answer'];
			$module = $ans['module'];
			$type = $ans['type'];
			$properties = json_decode($ans['properties']);
			$labels = array();
			$values = array();
			$rc = array();
			for ($x = 0; $x < count($answer); $x++) {
				$labels[] = isset($answer[$x][$properties->key_label]) ? getTranslatedString($answer[$x][$properties->key_label], $module) : $properties->key_label;
				$values[] = $answer[$x][$properties->key_value];
				$rc[] = 'getRandomColor()';
			}
			$chartID = uniqid('chartAns');
			$chart .= '<script src="include/chart.js/Chart.min.js"></script>
				<link rel="stylesheet" type="text/css" media="all" href="include/chart.js/Chart.min.css">
				<script src="include/chart.js/randomColor.js"></script>';
			$chart .= '<div style="width: 80%;">';
			$chart .= '<h2>'.$title.' - '.$type.' Chart</h2>';
			$chart .= '<canvas id="'.$chartID.'" style="width:500px;height:250px;margin:auto;padding:10px;"></canvas>';
			$chart .= '
				<script type="text/javascript">
					function getRandomColor() {
						return randomColor({
							luminosity: "dark",
							hue: "random"
						});
					}

					window.doChartAns = function(charttype) {
						let chartans = document.getElementById("'.$chartID.'");
						let context = chartans.getContext("2d");
						context.clearRect(0, 0, chartans.width, chartans.height);
					
						let chartDataObject = {
							labels: '.json_encode($labels).',
							datasets: [{
								data: '.json_encode($values).',
								backgroundColor: ['.implode(',', $rc).']
							}]
						};
						var maxnum = Math.max.apply(Math, chartDataObject.datasets[0].data);
						var maxgrph = Math.ceil(maxnum + (6 * maxnum / 100));
						Chart.scaleService.updateScaleDefaults("linear", {
							ticks: {
								min: 0,
								max: maxgrph,
								precision: 0
							}
						});
						window.chartAns = new Chart(chartans,{
							type: charttype,
							data: chartDataObject,
							options: {
								responsive: true,
								legend: {
									position: "right",
									display: (charttype=="pie"),
									labels: {
										fontSize: 11,
										boxWidth: 18
									}
								}
							}
						});
					}

					let charttype = "'.strtolower($type).'";
					doChartAns(charttype);
				</script>
			';
			$chart .= '</div>';
		}
		return $chart;
	}

	public function convertColumns2DataTable() {
		global $adb, $log;
		$qcols = $this->column_fields;
		if (empty($qcols['qcolumns'])) {
			return array(
				array(
					'fieldname' => 'custom',
					'operators' => 'custom',
					'alias' => '',
					'sort' => 'NONE',
					'group' => '0',
					'instruction' => '',
				),
			);
		}
		$fldnecol = $adb->pquery('SELECT fieldname,columnname FROM vtiger_field WHERE fieldname!=columnname and tabid=?', array(getTabid($qcols['qmodule'])));
		$fnec = array();
		while ($r = $fldnecol->FetchRow()) {
			$fnec[$r['fieldname']] = $r['columnname'];
		}
		$fieldData = array();
		$orderby = explode(',', strtolower(str_replace(' ', '', decode_html($qcols['orderby']))));
		$groupby = explode(',', strtolower(str_replace(' ', '', decode_html($qcols['groupby']))));
		$qcols = decode_html($qcols['qcolumns']);

		$parser = new PHPSQLParser();
		$parsed = $parser->parse('select '.$qcols.' from stubtable');
		$generatedQColumns = '';
		if (isset($parsed['SELECT'])) {
			$selectCoulums = $parsed["SELECT"];
			foreach ($selectCoulums as $col) {
				if ($col['expr_type'] == 'colref' || $col['expr_type'] == 'function' || $col['expr_type'] == 'expression') {
					$value = '';
					if (!empty($col['alias'])) {
						$value = $col['alias']['name'];
					} else {
						$base_expr = explode(',', $col['base_expr']);
						if (count($base_expr) > 1) {
							$value = $base_expr[1];
						} else {
							$value = $col['base_expr'];
						}
					}
				} elseif ($col['expr_type'] == 'aggregate_function') {
					$value = '';
					$sub_tree = $col['sub_tree'][0]['base_expr'];
					$value = strtolower($col['base_expr']).'('.$sub_tree.')';
				}

				if (empty($generatedQColumns)) {
					$generatedQColumns = $value.' AS ' .$value;
				} else {
					$generatedQColumns = $generatedQColumns.','.$value.' AS ' .$value;
				}
			}
		}

		if (strpos($qcols, '[')===false) {
			$qcols = preg_replace('/\s*,\s*/', ',', $generatedQColumns);
			$qcols = explode(',', $qcols);
			foreach ($qcols as $finfo) {
				$alias = '';
				if (strpos($finfo, ' ')) {
					$alias = preg_replace('/\s+/', ' ', $finfo);
					$alias = explode(' ', $alias);
					$alias = $alias[2];
					$finfo = strtolower($alias);
				}
				$fieldData[] = array(
					'fieldname' => $finfo,
					'operators' => 'custom',
					'alias' => $alias,
					'sort' => (in_array($finfo.'asc', $orderby) || in_array($finfo, $orderby) ? 'ASC' : (in_array($finfo.'desc', $orderby) ? 'DESC' : 'NONE')),
					'group' => (in_array($finfo, $groupby) ? '1' : '0'),
					'instruction' => $finfo,
				);
			}
		} else {
			$columns = json_decode($qcols, true);
			foreach ($columns as $finfo) {
				$cnam = isset($fnec[$finfo['fieldname']]) ? $fnec[$finfo['fieldname']] : $finfo['fieldname'];
				$fieldData[] = array(
					'fieldname' => $finfo['groupjoin'],
					'operators' => $finfo['joincondition'],
					'alias' => ($finfo['groupjoin']==$finfo['fieldname'] ? '' : $finfo['fieldname']),
					'sort' => (in_array($cnam.'asc', $orderby) || in_array($cnam, $orderby) ? 'ASC' : (in_array($cnam.'desc', $orderby) ? 'DESC' : 'NONE')),
					'group' => (in_array($cnam, $groupby) ? '1' : '0'),
					'instruction' => $finfo['value'],
				);
			}
		}
		return $fieldData;
	}

	public static function getFormattedValue($value, $type, $format) {
		global $current_user, $log;
		switch ($type) {
			case 'date':
			case 'datetime':
				if (!empty($format)) {
					$dt = explode(' ', $value);
					list($y, $m, $d) = (strpos($dt[0], '/')) ? explode('/', $dt[0]) : explode('-', $dt[0]);
					if (empty($dt[1])) {
						$h = $i = $s = 0;
					} else {
						list($h, $i, $s) = explode(':', $dt[1]);
					}
					return date($format, mktime($h, $i, $s, $m, $d, $y));
				} else {
					require_once 'include/fields/DateTimeField.php';
					return DateTimeField::convertToUserFormat($value, $current_user);
				}
				break;
			case 'float':
			case 'decimal':
				if (!empty($format) && is_object($format)) {
					$decimalseparator = empty($format->decimalseparator) ? '' : $format->decimalseparator;
					$grouping = empty($format->grouping) ? '' : $format->grouping;
					$numberdecimals = empty($format->numberdecimals) ? '' : $format->numberdecimals;
					return number_format($value, (int)$numberdecimals, $decimalseparator, $grouping);
				} else {
					require_once 'include/fields/CurrencyField.php';
					return CurrencyField::convertToUserFormat($value, $current_user, true);
				}
				break;
			default:
				return $value;
				break;
		}
	}

	public static function generateCSV($data, $delimiter = ',', $enclosure = '"') {
		$handle = fopen('php://temp', 'r+');
		fputcsv($handle, $data, $delimiter, $enclosure);
		rewind($handle);
		$contents = '';
		while (!feof($handle)) {
			$contents .= fread($handle, 8192);
		}
		fclose($handle);
		return $contents;
	}

	public static function getQuestionProperties($qnid) {
		global $adb;
		$propertyody = null;
		$res = $adb->pquery('SELECT typeprops FROM vtiger_cbquestion WHERE cbquestionid = ? LIMIT 1', array($qnid));
		if ($res && $adb->num_rows($res) > 0) {
			$propertyody = json_decode(html_entity_decode($adb->query_result($res, 0, 'typeprops')));
		}
		return $propertyody;
	}

	public static function getQnDelimeterProperty($qnid) {
		$propertyody = self::getQuestionProperties($qnid);
		return ($propertyody && !empty($propertyody->delimiter)) ? $propertyody->delimiter : ',';
	}
}
?>
