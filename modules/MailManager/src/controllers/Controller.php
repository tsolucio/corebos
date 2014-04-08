<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

include_once dirname(__FILE__) . '/../helpers/Utils.php';

include_once dirname(__FILE__) . '/../helpers/Request.php';
include_once dirname(__FILE__) . '/../helpers/Response.php';
include_once dirname(__FILE__) . '/../viewers/Viewer.php';
include_once dirname(__FILE__) . '/../models/Mailbox.php';
include_once dirname(__FILE__) . '/DraftController.php';

/**
 * Abstract Mail Manager Controller
 */
abstract class MailManager_Controller {

    /**
     * Abstract function which process request
     */
	abstract function process(MailManager_Request $request);	

    /**
     * Function which gets the template handler
     * @global String $currentModule
     * @return MailManager_Viewer
     */
	function getViewer() {
		global $currentModule;
		$viewer = new MailManager_Viewer();
		$viewer->assign('MAILBOX', $this->getMailboxModel());
		$viewer->assign('MODULE', $currentModule);
		return $viewer;
	}

    /**
     * Function which fetches the template file
     * @global String $currentModule
     * @param String $filename
     * @return template file
     */
	function getModuleTpl($filename) {
		global $currentModule;
		return vtlib_getModuleTemplate($currentModule, $filename);
	}

    /**
     * Mail Manager Connector 
     * @var MailManager_Connector
     */
	protected $mConnector = false;

    /**
     * MailBox folder name
     * @var string
     */
	protected $mFolder = false;

    /**
     * Connector to the IMAP server
     * @var MailManager_Model_Mailbox
     */
	protected $mMailboxModel = false;

    /**
     * Returns the active Instance of Current Users MailBox
     * @return MailManager_Model_Mailbox
     */
	protected function getMailboxModel() {
		if ($this->mMailboxModel === false) {
			$this->mMailboxModel = MailManager_Model_Mailbox::activeInstance();
		}
		return $this->mMailboxModel;
	}
	
    /**
     * Checks if the current users has provided Mail Server details
     * @return Boolean
     */
    protected function hasMailboxModel() {
		$model = $this->getMailboxModel();
		return $model->exists();
	}

    /**
     * Returns a Connector to either MailBox or Internal Drafts
     * @param String $folder - Name of the folder
     * @return MailManager_Connector
     */
	protected function getConnector($folder='') {
		if (!$this->mConnector || ($this->mFolder != $folder)) {
			if($folder == "__vt_drafts") {
				$draftController = new MailManager_DraftController();
				$this->mConnector = $draftController->connectorWithModel();
			} else {
				if ($this->mConnector) $this->mConnector->close();

				$model = $this->getMailboxModel();
				$this->mConnector = MailManager_Connector::connectorWithModel($model, $folder);
			}
			$this->mFolder = $folder;
		}
		return $this->mConnector;
	}

    /**
     * Function that closes connection to IMAP server
     */
	function closeConnector() {
		if ($this->mConnector) {
			$this->mConnector->close();
			$this->mConnector = false;
		}
	}
}
?>