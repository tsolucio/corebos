<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
require_once 'modules/Vtiger/DeveloperWidget.php';
require_once 'modules/cbQuestion/cbQuestion.php';
global $currentModule;

class bqanswer {
	// Get class name of the object that will implement the widget functionality
	public static function getWidget($name) {
		return (new bqAnswer_DetailViewBlock());
	}
}

class bqAnswer_DetailViewBlock extends DeveloperBlock {

	protected $widgetName = 'BusinessQuestionAnswer';

	// This one is called to get the contents to show on screen
	public function process($context = false) {
		global $current_user, $currentModule;
		$this->context = $context;
		$sourceRecordId = $this->getFromContext('QID');
		if (!empty($sourceRecordId)) {
			$recordid = $this->getFromContext('RECORDID');
			$module = $this->getFromContext('MODULE');
			if (empty($module)) {
				$module = $currentModule;
			}
			$params = array(
				'$RECORD$' => $recordid,
				'$MODULE$' => $module,
				'$USERID$' => $current_user->id,
			);
			if (isset($recordid)) {
				$ctxtmodule = getSalesEntityType($recordid);
				$params['$MODULE$'] = $ctxtmodule;
				$ent = CRMEntity::getInstance($ctxtmodule);
				$ent->id = $recordid;
				$ent->retrieve_entity_info($recordid, $ctxtmodule, false, true);
				foreach ($ent->column_fields as $fname => $fvalue) {
					$params['$'.$fname.'$'] = $fvalue;
				}
			}
			return cbQuestion::getFormattedAnswer($sourceRecordId, $params);
		}
		return getTranslatedString('LBL_PERMISSION');
	}
}

if (isset($_REQUEST['action']) && $_REQUEST['action']==$currentModule.'Ajax') {
	$bqa = new bqAnswer_DetailViewBlock();
	echo $bqa->process($_REQUEST);
}
