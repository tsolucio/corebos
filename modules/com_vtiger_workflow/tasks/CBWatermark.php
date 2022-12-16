<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'include/events/include.inc';

class CBWatermark extends VTTask {
	public $executeImmediately = true;
	public $queable = true;

	public function getFieldNames() {
		return array('imagesvalue', 'imagesx', 'imagesy', 'exptype');
	}

	public function doTask(&$entity) {
		global $current_user, $logbg, $from_wf, $currentModule;
		$from_wf = true;
		$logbg->debug('> CBWatermark');
		$hold_ajxaction = isset($_REQUEST['ajxaction']) ? $_REQUEST['ajxaction'] : '';
		$_REQUEST['ajxaction'] = 'Workflow';
		if (!empty($this->imagesvalue)) {
			if ($this->exptype == 'rawtext') {
				$watermark = $this->imagesvalue;
			} elseif ($this->exptype == 'fieldname') {
				$util = new VTWorkflowUtils();
				$adminUser = $util->adminUser();
				$entityCache = new VTEntityCache($adminUser);
				$fn = new VTSimpleTemplate($this->imagesvalue);
				$watermark = $fn->render($entityCache, $entity->getId(), [], $entity->WorkflowContext);
			} else {
				$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($this->imagesvalue)));
				$expression = $parser->expression();
				$exprEvaluater = new VTFieldExpressionEvaluater($expression);
				$watermark = $exprEvaluater->evaluate($entity);
			}
			$data = $entity->getData();
			$filetype = $data['filetype'];
			switch ($filetype) {
				case 'image/png':
					break;
				case 'image/jpg':
					break;
				case 'image/jpeg':
					break;
				default:
					//do nothing
					break;
			}
		}
		$_REQUEST['ajxaction'] = $hold_ajxaction;
		$from_wf = false;
		$logbg->debug('< CBWatermark');
	}
}
?>