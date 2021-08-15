<?php
include_once 'include/Webservices/GetRelatedRecords.php';
require_once 'data/CRMEntity.php';
global $adb, $current_user;
if (empty($current_user)) {
	$current_user = Users::getActiveAdminUser();
}
$lastExecution = coreBOS_Settings::getSetting('mvLastScheduleWf', '');
if (empty($lastExecution)) {
	$lastExecution = '1970-01-01 00:00:00';
}
$result = $adb->pquery('SELECT * from vtiger_cbquestion where run_updateview = ? and mviewwf=? ', array(1, 1));
if ($result && $adb->num_rows($result) > 0) {
	include_once 'modules/cbQuestion/cbQuestion.php';
	$totalRows = $adb->num_rows($result);
	for ($x=0; $x < $totalRows; $x++) {
		$qvname = $adb->query_result($result, $x, 'qname');
		$qvname = str_replace(' ', '_', $qvname);
		$uniqueid = $adb->query_result($result, $x, 'uniqueid');
		$qmodule = $adb->query_result($result, $x, 'qmodule');
		$relatedModules = $adb->query_result($result, $x, 'mvrelated_modulelist');
		$relatedModulesArr = explode(Field_Metadata::MULTIPICKLIST_SEPARATOR, $relatedModules);
		$crmentity_table = $adb->query_result($result, $x, 'crmentityalias');
		$maintablealias = $adb->query_result($result, $x, 'maintablealias');
		$qid = $adb->query_result($result, $x, 'cbquestionid');
		$sql = cbQuestion::getSQL($qid);
		$crmentity_table = !empty($crmentity_table) ? $crmentity_table : 'vtiger_crmentity';
		if (!empty($relatedModulesArr)) {
			for ($z = 0; $z < count($relatedModulesArr); $z++) {
				$setype = $relatedModulesArr[$z];
				$crmEntityTable = CRMEntity::getcrmEntityTableAlias($setype);
				$relatedRecords = $adb->pquery('SELECT crmid from '.$crmEntityTable.' where modifiedtime > ? AND setype = ?', array($lastExecution, $setype));
				while ($crmidArr = $adb->fetch_array($relatedRecords)) {
					$crmid = $crmidArr['crmid'];
					$crmid = vtws_getEntityId($setype) . 'x'. $crmid;
					$queryParameters = array(
						'productDiscriminator'=>'',
						'columns'=>'id',
						'limit'=>'',
						'offset'=>'0',
					);
					$rel_records = getRelatedRecords($crmid, $setype, $qmodule, $queryParameters, $current_user);
					$rel_recordsArr = $rel_records['records'];
					$rec_ids = array();
					if (!empty($rel_recordsArr)) {
						for ($y = 0; $y < count($rel_recordsArr); $y++) {
							$rec_id = $rel_recordsArr[$y][0];
							array_push($rec_ids, $rec_id);
						}
						if (count($rec_ids) > 0) {
							$rec_ids = implode('","', $rec_ids);
							$adb->query('delete from '.$qvname.' where '.$uniqueid.' IN ("' . $rec_ids . '")');
							if (!empty($maintablealias)) {
								$sql = preg_replace('/where\s+('.$crmentity_table.'\.)?deleted\s*=\s*0/i', 'where '.$maintablealias.'.'.$uniqueid.' IN ("' . $rec_ids . '") AND '.$crmentity_table.'.deleted=0', $sql);
							} else {
								$sql = preg_replace('/where\s+('.$crmentity_table.'\.)?deleted\s*=\s*0/i', 'where '.$uniqueid.' IN ("' . $rec_ids . '") AND '.$crmentity_table.'.deleted=0', $sql);
							}
							$adb->query('INSERT INTO '.$qvname.' '.$sql);
						}
					}
				}
			}
		}
	}
}
coreBOS_Settings::setSetting('mvLastScheduleWf', date('Y-m-d H:i:s'));