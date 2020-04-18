<?php
/***********************************************************************************
 * Copyright 2012-2020 JPL TSolucio, S.L.  --  This file is a part of coreBOSCP.
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

include_once 'include/Webservices/GetRelatedRecords.php';
function getRelatedRecordsThroughModule($id, $module, $relatedModule, $bridgeModule, $queryParameters, $user) {
	global $current_user;
	$rel_records = getRelatedRecords($id, $relatedModule, $bridgeModule, $queryParameters, $current_user);
	$rel_dataArray = array();
	if (count($rel_records['records']) > 0) {
		$rel_recordsArr = $rel_records['records'];
		for ($x = 0; $x < count($rel_recordsArr); $x++) {
			$rec_id = $rel_recordsArr[$x][0];
			$parrel_records = getRelatedRecords($rec_id, $bridgeModule, $module, $queryParameters, $current_user);
			if (!empty($parrel_records['records'])) {
				$rel_dataArray = array_merge($rel_dataArray, $parrel_records['records']);
			}
		}
	}
	return array('records' => $rel_dataArray);
}