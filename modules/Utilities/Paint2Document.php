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
// block://paint2Document:modules/Utilities/Paint2Document.php:forrecord=$RECORD$&formodule=$MODULE$
// javascript:window.open('index.php?module=Utilities&action=UtilitiesAjax&file=Paint2Document&formodule=$MODULE$&forrecord=$RECORD$&inwindow=1','photo2doc','width=800,height=860');

require_once 'modules/cbtranslation/cbtranslation.php';
require_once 'include/ListView/ListViewJSON.php';
require_once 'modules/Vtiger/DeveloperWidget.php';
global $currentModule;

class paint2Document {
	// Get class name of the object that will implement the widget functionality
	public static function getWidget($name) {
		return (new paint2Document_DetailViewBlock());
	}
}

class paint2Document_DetailViewBlock extends DeveloperBlock {

	protected $widgetName = 'LBL_PAINT2DOCUMENT';

	// This one is called to get the contents to show on screen
	public function process($context = false) {
		global $current_user, $app_strings;
		$this->context = $context;
		$smarty = $this->getViewer();
		$inwindow = $this->getFromContext('inwindow');
		$formodule = $this->getFromContext('formodule');
		$forrecord = $this->getFromContext('forrecord');
		$companyDetails = retrieveCompanyDetails();
		$smarty->assign('COMPANY_DETAILS', $companyDetails);
		$smarty->assign('APP', $app_strings);
		$smarty->assign('INWINDOW', $inwindow);
		$smarty->assign('MODULE', $formodule);
		$smarty->assign('RECORD', $forrecord);
		$smarty->assign('USERID', vtws_getEntityId('Users').'x'.$current_user->id);
		$smarty->assign('DOCID', vtws_getEntityId('DocumentFolders').'x');
		$smarty->assign('WSID', vtws_getEntityId($formodule).'x'.$forrecord);
		$lv = new GridListView('DocumentFolders');
		$folders = $lv->findDocumentFolders();
		$smarty->assign('FOLDERS', $folders);
		$smarty->assign('FOLDERID', $this->getFromContext('folderid'));
		$smarty->assign('USER_LANG', $current_user->language);
		return $smarty->fetch('Smarty/templates/Components/Paint/index.tpl');
	}
}

if (isset($_REQUEST['action']) && $_REQUEST['action']==$currentModule.'Ajax') {
	$smq = new paint2Document_DetailViewBlock();
	echo $smq->process($_REQUEST);
}
