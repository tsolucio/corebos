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
 *************************************************************************************************/
// module=Utilities&action=UtilitiesAjax&file=importTiddlers
// block://importTiddlers:modules/Utilities/importTiddlers.php

require_once 'modules/Vtiger/DeveloperWidget.php';
require_once 'include/Webservices/upsert.php';
global $currentModule;

class importTiddlers {
	// Get class name of the object that will implement the widget functionality
	public static function getWidget($name) {
		return (new importTiddlers_DetailViewBlock());
	}
}

class importTiddlers_DetailViewBlock extends DeveloperBlock {

	protected $widgetName = 'importTiddlers';

	// This one is called to get the contents to show on screen
	public function process($context = false) {
		global $currentModule;
		$smarty = $this->getViewer();
		$this->context = $context;
		$smarty->assign('ID', $this->getFromContext('record', true));
		if (isset($_REQUEST['method'])) {
			$this->processtiddler($_REQUEST['tiddler']);
		}
		$smarty->assign('MODULE', $currentModule);
		return $smarty->fetch('Smarty/templates/modules/Utilities/importtiddlers.tpl');
	}
}

if (isset($_REQUEST['action']) && $_REQUEST['action']==$currentModule.'Ajax') {
	$mdu = new importTiddlers_DetailViewBlock();
	echo $mdu->process($_REQUEST);
}
?>