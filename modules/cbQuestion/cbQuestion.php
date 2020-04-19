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

class cbQuestion extends CRMEntity {
	public $db;
	public $log;

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
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
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

	/**
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	// public function save_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function delete_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle getting related list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	/**
	 * Handle getting dependents list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	public static function getSQL($qid, $params = array()) {
		global $current_user, $adb, $log;
		$q = new cbQuestion();
		if (empty($qid) && !empty($params['cbQuestionRecord']) && is_array($params['cbQuestionRecord'])) {
			$q->column_fields = $params['cbQuestionRecord'];
			unset($params['cbQuestionRecord']);
			if (isset($params['cbQuestionContext'])) {
				$qctx = $params['cbQuestionContext'];
				unset($params['cbQuestionContext']);
				$params = array_merge($params, $qctx);
			}
		} else {
			if (isPermitted('cbQuestion', 'DetailView', $qid) != 'yes') {
				return array('type' => 'ERROR', 'answer' => 'LBL_PERMISSION');
			}
			$q->retrieve_entity_info($qid, 'cbQuestion');
		}
		include_once 'include/Webservices/Query.php';
		include_once 'include/Webservices/VtigerModuleOperation.php';
		if ($q->column_fields['sqlquery']=='1') {
			$mod = CRMEntity::getInstance($q->column_fields['qmodule']);
			$query = 'SELECT '.decode_html($q->column_fields['qcolumns']).' FROM '.$mod->table_name.' ';
			if (!empty($q->column_fields['qcondition'])) {
				$conds = decode_html($q->column_fields['qcondition']);
				foreach ($params as $param => $value) {
					$conds = str_replace($param, $value, $conds);
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
				foreach ($params as $param => $value) {
					$conds = str_replace($param, $value, $conds);
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
			unset($params['cbQuestionRecord']);
			if (isset($params['cbQuestionContext'])) {
				$qctx = $params['cbQuestionContext'];
				unset($params['cbQuestionContext']);
				$params = array_merge($params, $qctx);
			}
		} else {
			if (isPermitted('cbQuestion', 'DetailView', $qid) != 'yes') {
				return array('type' => 'ERROR', 'answer' => 'LBL_PERMISSION');
			}
			$q->retrieve_entity_info($qid, 'cbQuestion');
		}
		if ($q->column_fields['qtype']=='Mermaid') {
			return array(
				'columns' => html_entity_decode($q->column_fields['qcolumns'], ENT_QUOTES, $default_charset),
				'title' => html_entity_decode($q->column_fields['qname'], ENT_QUOTES, $default_charset),
				'type' => html_entity_decode($q->column_fields['qtype'], ENT_QUOTES, $default_charset),
				'properties' => html_entity_decode($q->column_fields['typeprops'], ENT_QUOTES, $default_charset),
				'answer' => 'graph '.$q->column_fields['typeprops']."\n\n".html_entity_decode($q->column_fields['qcolumns'], ENT_QUOTES, $default_charset),
			);
		} else {
			include_once 'include/Webservices/Query.php';
			if ($q->column_fields['sqlquery']=='0') {
				$query = 'SELECT '.decode_html($q->column_fields['qcolumns']).' FROM '.decode_html($q->column_fields['qmodule']);
				if (!empty($q->column_fields['qcondition'])) {
					$conds = decode_html($q->column_fields['qcondition']);
					foreach ($params as $param => $value) {
						$conds = str_replace($param, $value, $conds);
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
				return array(
					'module' => $q->column_fields['qmodule'],
					'columns' => $q->column_fields['qcolumns'],
					'title' => html_entity_decode($q->column_fields['qname'], ENT_QUOTES, $default_charset),
					'type' => html_entity_decode($q->column_fields['qtype'], ENT_QUOTES, $default_charset),
					'properties' => html_entity_decode($q->column_fields['typeprops'], ENT_QUOTES, $default_charset),
					'answer' => vtws_query($query, $current_user)
				);
			} else {
				require_once 'include/Webservices/GetExtendedQuery.php';
				$handler = vtws_getModuleHandlerFromName($q->column_fields['qmodule'], $current_user);
				$meta = $handler->getMeta();
				$queryRelatedModules = array(); // this has to be filled in with all the related modules in the query
				$webserviceObject = VtigerWebserviceObject::fromName($adb, $q->column_fields['qmodule']);
				$modOp = new VtigerModuleOperation($webserviceObject, $current_user, $adb, $log);
				$answer = $modOp->querySQLResults(cbQuestion::getSQL($qid, $params), ' not in ', $meta, $queryRelatedModules);
				return array(
					'module' => $q->column_fields['qmodule'],
					'columns' => $q->column_fields['qcolumns'],
					'title' => html_entity_decode($q->column_fields['qname'], ENT_QUOTES, $default_charset),
					'type' => html_entity_decode($q->column_fields['qtype'], ENT_QUOTES, $default_charset),
					'properties' => html_entity_decode($q->column_fields['typeprops'], ENT_QUOTES, $default_charset),
					'answer' => $answer
				);
			}
		}
	}

	public static function getFormattedAnswer($qid, $params = array()) {
		$ans = self::getAnswer($qid, $params);
		switch ($ans['type']) {
			case 'Table':
				$ret = self::getTableFromAnswer($ans);
				break;
			case 'Number':
				$ret = array_pop($ans['answer'][0]);
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
				$labels[] = getTranslatedString($answer[$x][$properties->key_label], $module);
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
		global $adb;
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
		if (strpos($qcols, '[')===false) {
			$qcols = preg_replace('/\s*,\s*/', ',', $qcols);
			$qcols = explode(',', $qcols);
			foreach ($qcols as $finfo) {
				$alias = '';
				if (strpos($finfo, ' ')) {
					$alias = preg_replace('/\s+/', ' ', $finfo);
					$alias = explode(' ', $alias);
					$alias = $alias[2];
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
}
?>
