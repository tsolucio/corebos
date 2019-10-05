<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once __DIR__ . '/src/controllers/Controller.php';
include_once __DIR__ . '/src/connectors/Connector.php';
include_once __DIR__ . '/MailManager.php';

class MailManager_IndexController extends MailManager_Controller {

	public static $controllers = array(
		'mainui' => array( 'file' => 'src/controllers/MainUIController.php', 'class' => 'MailManager_MainUIController' ),
		'folder' => array( 'file' => 'src/controllers/FolderController.php', 'class' => 'MailManager_FolderController' ),
		'mail'   => array( 'file' => 'src/controllers/MailController.php',   'class' => 'MailManager_MailController'   ),
		'relation'=>array( 'file' => 'src/controllers/RelationController.php','class'=> 'MailManager_RelationController'),
		'settings'=>array( 'file' => 'src/controllers/SettingsController.php','class'=> 'MailManager_SettingsController'),
		'search'  =>array( 'file' => 'src/controllers/SearchController.php','class'=> 'MailManager_SearchController'),
	);

	public function process(MailManager_Request $request) {
		if (!$request->has('_operation')) {
			return $this->processRoot($request);
		}
		$operation = $request->getOperation();
		$controllerInfo = self::$controllers[$operation];

		$controllerFile = __DIR__ . '/' . $controllerInfo['file'];
		checkFileAccessForInclusion($controllerFile);
		include_once $controllerFile;
		$controller = new $controllerInfo['class'];

		// Making sure to close the open connection
		if ($controller) {
			$controller->closeConnector();
		}
		$response = $controller->process($request);
		if ($response) {
			$response->emit();
		}
		unset($request, $response);
	}

	public function processRoot(MailManager_Request $request) {
		$viewer = $this->getViewer();
		$tool_buttons = array(
			'EditView' => 'no',
			'CreateView' => 'no',
			'index' => 'no',
			'Import' => 'no',
			'Export' => 'no',
			'Merge' => 'no',
			'DuplicatesHandling' => 'no',
			'Calendar' => 'no',
			'moduleSettings' => 'no',
		);
		$viewer->assign('CHECK', $tool_buttons);
		$viewer->assign('ERROR', '');
		$viewer->assign('SHOW_SENTTO_LINKS', GlobalVariable::getVariable('MailManager_Show_SentTo_Links', 0));
		$viewer->display($this->getModuleTpl('index.tpl'));
		return true;
	}
}

$controller = new MailManager_IndexController();
$controller->process(new MailManager_Request($_REQUEST));
?>
