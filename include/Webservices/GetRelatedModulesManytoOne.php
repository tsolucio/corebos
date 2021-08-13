<?php
/***********************************************************************************
 * Copyright 2012-2014 JPL TSolucio, S.L.  --  This file is a part of coreBOSCP.
 * You can copy, adapt and distribute the work under the 'Attribution-NonCommercial-ShareAlike'
 * Vizsage Public License (the 'License'). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  'AS IS' BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 ************************************************************************************/
require_once 'include/Webservices/Utils.php';

/* Given a module, get all the many to one related modules */
function getRelatedModulesManytoOne($module, $user) {
	global $adb;
	$types = vtws_checkListTypesPermission($module, $user);
	$result = $adb->pquery(
		'SELECT module,fieldname
			from vtiger_fieldmodulerel
			join vtiger_field on vtiger_field.fieldid=vtiger_fieldmodulerel.fieldid
			where relmodule=? and module in (select name from vtiger_tab where presence=0)',
		array($module)
	);
	$modules=array();
	while ($rel = $adb->fetch_array($result)) {
		if (in_array($rel['module'], $types['types'])) {
			$modules[] = array(
				'label' => getTranslatedString($rel['module'], $rel['module']),
				'name' => $rel['module'],
				'field' => $rel['fieldname'],
			);
		}
	}
	return $modules;
}
