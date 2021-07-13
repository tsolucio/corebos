<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
require_once 'include/utils/utils.php';
require_once 'modules/BusinessActions/BusinessActions.php';

class DocumentsUtils {

	public static function modDocsChangeModuleVisibility($tabid, $status) {
		if ($status == 'module_disable') {
			self::disableDocumentsForModule($tabid);
		} else {
			self::enableDocumentsForModule($tabid);
		}
	}

	public static function modDocsGetModuleinfo() {
		return BusinessActions::getModuleLinkStatusInfoSortedFlat('DETAILVIEWWIDGET', 'Upload Documents');
	}

	/**
	 *Invoked to enable widget for the module.
	 * @param Integer $tabid
	 */
	public static function enableDocumentsForModule($tabid) {
		$moduleInstance=Vtiger_Module::getInstance($tabid);
		$moduleInstance->addLink('HEADERCSS', 'DocumentsDropzoneCSS', 'include/dropzone/dropzone.css', 0, '', null, true);
		$moduleInstance->addLink('HEADERCSS', 'DocumentsDropzoneCustomCSS', 'include/dropzone/custom.css', 1, '', null, true);
		$moduleInstance->addLink('HEADERSCRIPT', 'DocumentsDropzoneJS', 'include/dropzone/dropzone.js', '', 2, null, true);
		$moduleInstance->addLink('DETAILVIEWWIDGET', 'Upload Documents', 'module=Documents&action=DocumentsAjax&file=WidgetUpload&record=$RECORD$');
	}

	/**
	 *Invoked to disable widget for the module.
	 * @param Integer $tabid
	 */
	public static function disableDocumentsForModule($tabid) {
		$moduleInstance=Vtiger_Module::getInstance($tabid);
		$moduleInstance->deleteLink('HEADERCSS', 'DocumentsDropzoneCSS');
		$moduleInstance->deleteLink('HEADERCSS', 'DocumentsDropzoneCustomCSS');
		$moduleInstance->deleteLink('HEADERSCRIPT', 'DocumentsDropzoneJS');
		$moduleInstance->deleteLink('DETAILVIEWWIDGET', 'Upload Documents');
	}
}
?>
