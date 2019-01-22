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
			// TODO Handle post installation actions
			$this->setModuleSeqNumber('configure', $modulename, 'cbQ-', '0000001');
		} elseif ($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
		} elseif ($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
		} elseif ($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} elseif ($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} elseif ($event_type == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
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

	public static function getAnswer($qid) {
		global $current_user, $default_charset;
		if (isPermitted('cbQuestion', 'DetailView', $qid) != 'yes') {
			return array('type' => 'ERROR', 'answer' => 'LBL_PERMISSION');
		}
		include_once 'include/Webservices/Query.php';
		$q = new cbQuestion();
		$q->retrieve_entity_info($qid, 'cbQuestion');
		$query = 'SELECT '.$q->column_fields['qcolumns'].' FROM '.$q->column_fields['qmodule'];
		if (!empty($q->column_fields['qcondition'])) {
			$query .= ' WHERE '.$q->column_fields['qcondition'];
		}
		if (!empty($q->column_fields['orderby'])) {
			$query .= ' ORDER BY '.$q->column_fields['orderby'];
		}
		if (!empty($q->column_fields['groupby'])) {
			$query .= ' GROUP BY '.$q->column_fields['groupby'];
		}
		if (!empty($q->column_fields['qpagesize'])) {
			$query .= ' LIMIT '.$q->column_fields['qpagesize'];
		}
		$query .= ';';
		return array(
			'type' => $q->column_fields['qtype'],
			'module' => $q->column_fields['qmodule'],
			'title' => html_entity_decode($q->column_fields['qname'], ENT_QUOTES, $default_charset),
			'type' => html_entity_decode($q->column_fields['qtype'], ENT_QUOTES, $default_charset),
			'properties' => html_entity_decode($q->column_fields['typeprops'], ENT_QUOTES, $default_charset),
			'answer' => vtws_query($query, $current_user)
		);
	}

	public static function getFormattedAnswer($qid) {
		$ans = self::getAnswer($qid);
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
			$columnLabels = $properties->columnlabels;
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
			for ($x = 0; $x < count($answer); $x++) {
				$labels[] = getTranslatedString($answer[$x][$properties->key_label], $module);
				$values[] = $answer[$x][$properties->key_value];
			}
			$chart .= '<script src="include/chart.js/Chart.min.js"></script>
				<script src="include/chart.js/randomColor.js"></script>';
			$chart .= '<div style="width: 80%;">';
			$chart .= '<h2>'.$title.' - '.$type.' Chart</h2>';
			$chart .= '<canvas id="chartAns" style="width:500px;height:250px;margin:auto;padding:10px;"></canvas>';
			$chart .= '
				<script type="text/javascript">
					function getRandomColor() {
						return randomColor({
							luminosity: "dark",
							hue: "random"
						});
					}

					window.doChartAns = function(charttype) {
						let chartans = document.getElementById("chartAns");
						let context = chartans.getContext("2d");
						context.clearRect(0, 0, chartans.width, chartans.height);
					
						let chartDataObject = {
							labels: '.json_encode($labels).',
							datasets: [{
								data: '.json_encode($values).',
								backgroundColor: [getRandomColor(),getRandomColor()]
							}]
						};
						var maxnum = Math.max.apply(Math, chartDataObject.datasets[0].data);
						var maxgrph = Math.ceil(maxnum + (5 * maxnum / 100));
						Chart.scaleService.updateScaleDefaults("linear", {
							ticks: {
								min: 0,
								max: maxgrph
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
}
?>
