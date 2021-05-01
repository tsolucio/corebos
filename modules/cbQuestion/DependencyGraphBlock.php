<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************/

// DEPENDENCY WF ACTION: block://DependencyGraphBlock:modules/cbQuestion/DependencyGraphBlock.php:targetid=$RECORD$&qnid=qnid&targetfield=sales_stage

require_once 'modules/Vtiger/DeveloperWidget.php';
require_once 'modules/cbQuestion/cbQuestion.php';
require_once 'modules/ModTracker/ModTracker.php';
global $currentModule;

class DependencyGraphBlock {
	public static function getWidget($name) {
		return (new DependencyGraphBlock_DetailViewBlock());
	}
}

class DependencyGraphBlock_DetailViewBlock extends DeveloperBlock {

	protected $widgetName = 'DependencyGraphBlock';

	public function process($context = false) {
		$this->context = $context;
		$smarty = $this->getViewer();
		$recordid = $this->getFromContext('targetid');
		$qnid = $this->getFromContext('qnid');
		if (!empty($qnid) && isset($recordid)) {
			$qn_params = array(
				'recordid' => $recordid,
				'states' => ModTracker::getRecordFieldHistory($recordid, $this->getFromContext('targetfield')),
			);
			$answer_res = cbQuestion::getAnswer($qnid, $qn_params);
			$graph = $answer_res['answer'];
			$style_dependencygraph = '';
			if (strpos($graph, 'style') !== false) {
				$style_dependencygraph = str_replace(substr($graph, 0, strpos($graph, 'style')), '', $graph);
			}
			$graph .= ' '.$style_dependencygraph;
			$smarty->assign('DEPENDENCYGRAPHDEVBLOCK', $graph);
			return $smarty->fetch('modules/cbQuestion/dependencyGraphBlock.tpl');
		}
		return '';
	}
}

if (isset($_REQUEST['action']) && $_REQUEST['action']==$currentModule.'Ajax') {
	$smq = new DependencyGraphBlock_DetailViewBlock();
	echo $smq->process($_REQUEST);
}
