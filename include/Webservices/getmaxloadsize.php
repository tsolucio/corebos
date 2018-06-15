<?php
/*************************************************************************************************
 * Copyright 2012 JPL TSolucio, S.L.  --  This file is a part of vtiger CRM.
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
*************************************************************************************************
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/

/**	function used to get the numeration of entites
 *	return array $entitynum - numertaion of entities
 */
function get_maxloadsize() {
	require_once 'include/utils/UserInfoUtil.php';
	require_once 'modules/Users/Users.php';
	global $log;
	$log->debug('Entering vtws_get_maxloadsize');

	$max_size = parse_size(ini_get('post_max_size'));

	// If upload_max_size is less, then reduce. Except if upload_max_size is
	// zero, which indicates no limit.
	$upload_max = parse_size(ini_get('upload_max_filesize'));
	if ($upload_max > 0 && $upload_max < $max_size) {
		$max_size = $upload_max;
	}

	$log->debug('Exiting get_maxloadsize');
	return $max_size;
}

function parse_size($size) {
	$unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
	$size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
	if ($unit) {
		// Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
		return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
	} else {
		return round($size);
	}
}
?>
