<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('include/utils/utils.php');
require_once 'include/utils/CommonUtils.php';

class ChartUtils {

	//Generates Chart Data in form of an array from the Query Result of reports
	public static function generateChartDataFromReports($queryResult, $groupbyField, $fieldDetails='', $reportid='') {
		require_once 'modules/Reports/CustomReportUtils.php';
		require_once('include/Webservices/Utils.php');
		require_once('include/Webservices/Query.php');
		global $adb, $current_user, $theme, $default_charset;
		$inventorymodules = array('Quotes', 'SalesOrder', 'PurchaseOrder', 'Invoice', 'Products', 'PriceBooks', 'Vendors', 'Services');
		$rows = $adb->num_rows($queryResult);
		$condition = "is";
		$current_theme = $theme;
		$groupByFields = array();
		$yaxisArray = array();
		$ChartDataArray = array();
		$target_val = array();

		$report = new ReportRun($reportid);
		$restrictedModules = array();
		if($report->secondarymodule!='') {
			$reportModules = explode(":",$report->secondarymodule);
		} else {
			$reportModules = array();
		}
		array_push($reportModules,$report->primarymodule);

		$restrictedModules = false;
		foreach($reportModules as $mod) {
			if(isPermitted($mod,'index') != "yes" || vtlib_isModuleActive($mod) == false) {
				if(!is_array($restrictedModules)) $restrictedModules = array();
				$restrictedModules[] = $mod;
			}
		}

		if(is_array($restrictedModules) && count($restrictedModules) > 0) {
			$ChartDataArray['error'] = "<h4>".getTranslatedString('LBL_NO_ACCESS', 'Reports').' - '.implode(',', $restrictedModules)."</h4>";
			return $ChartDataArray;
		}

		if ($fieldDetails != '') {
			list($tablename, $colname, $module_field, $fieldname, $single) = explode(":", $fieldDetails);
			list($module, $field) = explode("_", $module_field);
			$dateField = false;
			if ($single == 'D') {
				$dateField = true;
				$query = "SELECT * FROM vtiger_reportgroupbycolumn WHERE reportid=? ORDER BY sortid";
				$result = $adb->pquery($query, array($reportid));
				$criteria = $adb->query_result($result, 0, 'dategroupbycriteria');
			}
		}
		preg_match('/&amp;/', $groupbyField, $matches);
		if (!empty($matches)) {
			$groupfield = str_replace('&amp;', '&', $groupbyField);
			$groupbyField = $report->replaceSpecialChar($groupfield);
		}
		$handler = vtws_getModuleHandlerFromName($module, $current_user);
		$meta = $handler->getMeta();
		$meta->retrieveMeta();
		$referenceFields = $meta->getReferenceFieldDetails();

		if($rows > 0) {
			$resultRow = $adb->query_result_rowdata($queryResult, 0);
			if(!array_key_exists($groupbyField, $resultRow)) {
				$ChartDataArray['error'] = "<h4>".getTranslatedString('LBL_NO_PERMISSION_FIELD', 'Dashboard')."</h4>";
				return $ChartDataArray;
			}
		}
		for ($i = 0; $i < $rows; $i++) {
			$groupFieldValue = $adb->query_result($queryResult, $i, strtolower($groupbyField));
			$decodedGroupFieldValue = html_entity_decode($groupFieldValue, ENT_QUOTES, $default_charset);
			if (!empty($groupFieldValue)) {
				if (in_array($module_field, $report->append_currency_symbol_to_value)) {
					$valueComp = explode('::', $groupFieldValue);
					$groupFieldValue = $valueComp[1];
				}
				if ($dateField) {
					if (!empty($groupFieldValue)) {
						$grpField = CustomReportUtils::getXAxisDateFieldValue($groupFieldValue, $criteria);
						$groupByFields[] = getTranslatedString($grpField,$module);
					} else {
						$groupByFields[] = "Null";
					}
				}
				else if (in_array($fieldname, array_keys($referenceFields))) {
					if (count($referenceFields[$fieldname]) > 1) {
						$refenceModule = CustomReportUtils::getEntityTypeFromName($decodedGroupFieldValue, $referenceFields[$fieldname]);
					}
					else {
						$refenceModule = $referenceFields[$fieldname][0];
					}
					$groupByFields[] = getTranslatedString($groupFieldValue,$module);

					if ($fieldname == 'currency_id' && in_array($module, $inventorymodules)) {
						$tablename = 'vtiger_currency_info';
					} elseif ($refenceModule == 'DocumentFolders' && $fieldname == 'folderid') {
						$tablename = 'vtiger_attachmentsfolder';
						$colname = 'foldername';
					} else {
						require_once "modules/$refenceModule/$refenceModule.php";
						$focus = new $refenceModule();
						$tablename = $focus->table_name;
						$colname = $focus->list_link_field;
						$condition = "c";
					}
				} else {
					$groupByFields[] = getTranslatedString($groupFieldValue,$module);
				}
				$yaxisArray[] = $adb->query_result($queryResult, $i, 'groupby_count');
				if ($fieldDetails != '') {
					if ($dateField) {
						$advanceSearchCondition = CustomReportUtils::getAdvanceSearchCondition($fieldDetails, $criteria, $groupFieldValue);
						if ($module == 'Calendar') {
							$link_val = "index.php?module=" . $module . "&query=true&action=ListView&" . $advanceSearchCondition;
						}else
							$link_val = "index.php?module=" . $module . "&query=true&action=index&" . $advanceSearchCondition;
					}
					else {
						$cvid = getCvIdOfAll($module);
						$esc_search_str = urlencode($decodedGroupFieldValue);
						if ($single == 'DT') {
							$esc_search_str = urlencode($groupFieldValue);
							if (strtolower($fieldname) == 'modifiedtime' || strtolower($fieldname) == 'createdtime') {
								$tablename = 'vtiger_crmentity';
								$colname = $fieldname;
							}
						}
						if ($fieldname == 'assigned_user_id') {
							$tablename = 'vtiger_crmentity';
							$colname = 'smownerid';
						}

						if ($module == 'Calendar') {
							$link_val = "index.php?module=" . $module . "&action=ListView&search_text=" . $esc_search_str . "&search_field=" . $fieldname . "&searchtype=BasicSearch&query=true&operator=e&viewname=" . $cvid;
						} else {
							$link_val = "index.php?module=" . $module . "&action=index&search_text=" . $esc_search_str . "&search_field=" . $fieldname . "&searchtype=BasicSearch&query=true&operator=e&viewname=" . $cvid;
						}
					}

					$target_val[] = $link_val;
				}
			}
		}
		if(count($groupByFields) == 0) {
			$ChartDataArray['error'] = "<div class='componentName'>".getTranslatedString('LBL_NO_DATA', 'Reports')."</div";
		}
		$ChartDataArray['xaxisData'] = $groupByFields;
		$ChartDataArray['yaxisData'] = $yaxisArray;
		$ChartDataArray['targetLink'] = $target_val;
		$theme = $current_theme;
		return $ChartDataArray;
	}

	static public function getChartHTML($labels, $values, $graph_title, $target_values, $html_imagename, $width, $height, $left, $right, $top, $bottom, $graph_type, $legend_position='right', $responsive=true) {
		$lbls = implode(',',$labels);
		$vals = str_replace('::',',',$values);
		$lnks = array();
		$cnt=0;
		foreach ($target_values as $value) {
			$lnks[] = $cnt.':'.$value;
			$cnt++;
		}
		$lnks = implode(',',$lnks);
		$bcolor = array();
		for ($cnt=1;$cnt<count($labels);$cnt++) {
			$bcolor[] = 'getRandomColor()';
		}
		$bcolor = implode(',',$bcolor);
		if ($graph_title!='') {
			$gtitle = 'label:"'.$graph_title.'",';
			$display = 'display:true,';
		} else {
			$gtitle = '';
			$display = 'display:false,';
		}
		if ($graph_title=='pie') {
			$display = 'display:true,';
		}
		if ($legend_position!='') {
			$legend_position = 'position: "'.$legend_position.'",';
		}
		if ($responsive) {
			$respproperty = 'true';
		} else {
			$respproperty = 'false';
		}
		$sHTML = <<<EOF
<canvas id="$html_imagename" style="width:{$width}px;height:{$height}px;margin:auto;padding:10px;"></canvas>
<script type="text/javascript">
window.doChart{$html_imagename} = function(charttype) {
	let stuffchart = document.getElementById('{$html_imagename}');
	let chartDataObject = {
		labels: [{$lbls}],
		datasets: [{
			data: [ $vals ],
			$gtitle
			backgroundColor: [ $bcolor ]
		}]
	};
	window.schart{$html_imagename} = new Chart(stuffchart,{
		type: '{$graph_type}',
		data: chartDataObject,
		options: {
			responsive: $respproperty,
			legend: {
				$legend_position
				$display
				labels: {
					fontSize: 11,
					boxWidth: 18
				}
			}
		}
	});
	stuffchart.addEventListener('click',function(evt) {
		let activePoint = schart{$html_imagename}.getElementAtEvent(evt);
		if (activePoint.length == 0) return;
		let clickzone = { $lnks };
		if (clickzone == undefined || clickzone == {} || clickzone.length == 0) return;
		let a = document.createElement("a");
		a.target = "_blank";
		a.href = clickzone[activePoint[0]._index];
		document.body.appendChild(a);
		a.click();
	});
}
doChart{$html_imagename}('{$graph_type}');
</script>
EOF;
		return $sHTML;
	}

	static public function getChartHTMLwithObject($chartObject, $targetObject, $html_imagename, $width, $height, $left, $right, $top, $bottom) {
		$tgtarray = json_decode($targetObject,true);
		$tgt = reset($tgtarray);
		if (is_array($tgt)) {
			$czone = 'clickzone[activePoint[0]._datasetIndex][activePoint[0]._index]';
		} else {
			$czone = 'clickzone[activePoint[0]._index]';
		}
		$sHTML = <<<EOF
<canvas id="$html_imagename" style="width:{$width}px;height:{$height}px;margin:auto;padding:10px;"></canvas>
<script type="text/javascript">
window.doChart{$html_imagename} = function() {
	let stuffchart = document.getElementById('{$html_imagename}');
	window.schart{$html_imagename} = new Chart(stuffchart,$chartObject);
	stuffchart.addEventListener('click',function(evt) {
		let activePoint = schart{$html_imagename}.getElementAtEvent(evt);
		if (activePoint.length == 0) return;
		let clickzone = $targetObject;
		if (clickzone == undefined || clickzone == {} || clickzone.length == 0) return;
		let a = document.createElement("a");
		a.target = "_blank";
		a.href = $czone;
		document.body.appendChild(a);
		a.click();
	});
}
doChart{$html_imagename}();
</script>
EOF;
		return $sHTML;
	}

	static public function convertToArray($values,$translate=false,$withquotes=false) {
		if (strpos($values,'::')===false) $values = urldecode($values);
		$vals = explode('::',$values);
		if ($translate) {
			$vals = array_map(function($v) {
				return getTranslatedString($v,$v);
			}, $vals);
		}
		if ($withquotes) {
			$vals = array_map(function($v) {
				return '"'.urldecode($v).'"';
			}, $vals);
		}
		return $vals;
	}

}

?>
