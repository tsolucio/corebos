<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
global $adb, $log, $current_user;

function executefunctionsvalidate($functiontocall, $module, $structure = null) {
	switch ($functiontocall) {
		case 'ValidationExists':
			$valmod = vtlib_purify($module);
			if (file_exists("modules/{$valmod}/{$valmod}Validation.php")) {
				return 'yes';
			} else {
				include_once 'modules/cbMap/processmap/Validations.php';
				if (Validations::ValidationsExist($valmod)) {
					return 'yes';
				} else {
					return 'no';
				}
			}
			break;
		case 'ValidationLoad':
			$valmod = vtlib_purify($module);
			include_once 'modules/cbMap/processmap/Validations.php';
			$_REQUEST['structure'] = $structure;
			if (Validations::ValidationsExist($valmod)) {
				$validation = Validations::processAllValidationsFor($valmod);
				if ($validation!==true) {
					return Validations::formatValidationErrors($validation, $valmod);
				}
			}
			if (file_exists("modules/{$valmod}/{$valmod}Validation.php")) {
				ob_start();
				include "modules/{$valmod}/{$valmod}Validation.php";
				return ob_get_clean();
			} else {
				return '%%%OK%%%';
			}
			break;
	}
}
?>
