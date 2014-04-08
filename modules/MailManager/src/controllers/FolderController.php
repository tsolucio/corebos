<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
/**
 * Class that handles all the MailBox folder operations
 */
class MailManager_FolderController extends MailManager_Controller {

    /**
     * Process the request for Folder opertions
     * @global <type> $list_max_entries_per_page
     * @param MailManager_Request $request
     * @return MailManager_Response
     */
	function process(MailManager_Request $request) {
		global $list_max_entries_per_page;
		$response = new MailManager_Response();
        
		if ('open' == $request->getOperationArg()) {
			$q = $request->get('q');
			$foldername = $request->get('_folder');
			$type = $request->get('type');

			$connector = $this->getConnector($foldername);
			$folder = $connector->folderInstance($foldername);
			
			if (empty($q)) {
				$connector->folderMails($folder, intval($request->get('_page', 0)), $list_max_entries_per_page);
			} else {
				if(empty($type)) {
					$type='ALL';
				}
				$q = ''.$type.' "'.vtlib_purify($q).'"';
				$connector->searchMails($q, $folder, intval($request->get('_page', 0)), $list_max_entries_per_page);
			}
			
			$folderList = $connector->getFolderList();
			
			$viewer = $this->getViewer();
			
			$viewer->assign('TYPE', $type);
			$viewer->assign('QUERY', $request->get('q'));
			$viewer->assign('FOLDER', $folder);
			$viewer->assign('FOLDERLIST',  $folderList);
			$viewer->assign('SEARCHOPTIONS' ,self::getSearchOptions());
			
			$response->setResult( $viewer->fetch( $this->getModuleTpl( 'Folder.Open.tpl' ) ) );
		} elseif('drafts' == $request->getOperationArg()) {
			$q = $request->get('q');
			$type = $request->get('type');
			$page = intval($request->get('_page', 0));

			$connector = $this->getConnector('__vt_drafts');
			$folder = $connector->folderInstance();

			if(empty($q)) {
				$draftMails = $connector->getDrafts($page, $list_max_entries_per_page, $folder);
			} else {
				$draftMails = $connector->searchDraftMails($q, $type, $page, $list_max_entries_per_page, $folder);
			}

			$viewer = $this->getViewer();
			$viewer->assign('MAILS', $draftMails);
			$viewer->assign('FOLDER', $folder);
			$viewer->assign('SEARCHOPTIONS' ,MailManager_DraftController::getSearchOptions());
			$response->setResult($viewer->fetch($this->getModuleTpl('Folder.Drafts.tpl')));
		}
		return $response;
	}

    /**
     * Returns the List of search string on the MailBox
     * @return string
     */
	static function getSearchOptions(){
		$options = array('SUBJECT','TO','BODY','BCC','CC','FROM');
		return $options;
	}
}
?>