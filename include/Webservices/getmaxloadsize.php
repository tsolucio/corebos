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
require_once 'include/utils/CommonUtils.php';

/** return maximum upload size as per PHP settings
 * @return int maximum upload size as per PHP settings
 */
function get_maxloadsize($user = '') {
	require_once 'include/utils/UserInfoUtil.php';
	require_once 'modules/Users/Users.php';

	$max_size = parse_size(ini_get('post_max_size'));

	// If upload_max_size is less, then reduce. Except if upload_max_size is
	// zero, which indicates no limit.
	$upload_max = parse_size(ini_get('upload_max_filesize'));
	if ($upload_max > 0 && $upload_max < $max_size) {
		$max_size = $upload_max;
	}

	return $max_size;
}

/** convert given numeric string with optional byte size magnitud to a number of bytes
 * @param int byte size string to convert to bytes
 * @return int number of bytes in given string
 */
function parse_size($size) {
	return numberBytes($size);
}
?>
