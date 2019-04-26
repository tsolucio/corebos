<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L.  --  This file is a part of coreBOSCP.
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
*************************************************************************************************/

function cbws_jslog($level, $message, $user) {
	$logjs= LoggerManager::getLogger('JAVASCRIPT');
	$msg = json_decode($message, true);
	if (json_last_error() !== JSON_ERROR_NONE) {
		$msg = $message;
	}
	switch ($level) {
		case 'fatal':
			$logjs->fatal($msg);
			break;
		case 'trace':
			$logjs->trace($msg);
			break;
		case 'error':
			$logjs->error($msg);
			break;
		case 'warn':
			$logjs->warn($msg);
			break;
		case 'debug':
			$logjs->debug($msg);
			break;
		case 'info':
		default:
			$logjs->info($msg);
			break;
	}
}
?>
