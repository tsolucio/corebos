<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'include/Webservices/DescribeObject.php';
include_once 'include/Webservices/Query.php';
require_once 'include/utils/utils.php';
include_once 'modules/Settings/MailScanner/core/MailScannerAction.php';
include_once 'modules/Settings/MailScanner/core/MailAttachmentMIME.php';
require_once dirname(__FILE__) . '/RelationControllerAction.php';

/**
 * Class used to manage the emails relationship with vtiger records
 */
class MailManager_RelationController extends MailManager_Controller {

    /**
     * Used to check the MailBox connection
     * @var Boolean
     */
	protected $skipConnection = false;

	/** To avoid working with mailbox */
	protected function getMailboxModel() {
		if ($this->skipConnection) return false;
		return parent::getMailboxModel();
	}

    /**
     * List of modules used to match the Email address
     * @var Array
     */
	static $MODULES = array ( 'Contacts', 'Accounts', 'Leads');

    /**
     * Process the request to perform relationship operations
     * @global Users Instance $current_user
     * @global PearDataBase Instance $adb
     * @global String $currentModule
     * @param MailManager_Request $request
     * @return boolean
     */
	function process(MailManager_Request $request) {
		global $current_user, $adb;
		$response = new MailManager_Response(true);
		$viewer = $this->getViewer();

		if ('find' == $request->getOperationArg()) {
			$this->skipConnection = true; // No need to connect to mailbox here, improves performance
			
			// Check if the message is already linked.
			$linkedto = MailManager_RelationControllerAction::associatedLink($request->get('_msguid'));
			// If the message was not linked, lookup for matching records, using FROM address
			if (empty($linkedto)) {
				$results = array();
				$modules = array();
				$allowedModules = $this->getCurrentUserMailManagerAllowedModules();
				foreach (self::$MODULES as $MODULE) {
					if(!in_array($MODULE, $allowedModules)) continue;

                    $from = $request->get('_mfrom');
                    if(empty($from)) continue;

                    $results[$MODULE] = $this->lookupModuleRecordsWithEmail($MODULE, $from);
                    $describe = $this->ws_describe($MODULE);
					$modules[$MODULE] = array('label' => $describe['label'], 'name' => textlength_check($describe['name']), 'id' => $describe['idPrefix'] );
					
					// If look is found in a module, skip rest. - for performance
					//if (!empty($results[$MODULE])) break;
				}				
				$viewer->assign('LOOKUPS', $results);
				$viewer->assign('MODULES', $modules);
			} else {
				$viewer->assign('LINKEDTO', $linkedto);
			}

			$viewer->assign('LinkToAvailableActions', $this->linkToAvailableActions());
			$viewer->assign('AllowedModules', $allowedModules);
			$viewer->assign('MSGNO', $request->get('_msgno'));
			$viewer->assign('FOLDER', $request->get('_folder'));
			
			$response->setResult( array( 'ui' => $viewer->fetch( $this->getModuleTpl('Relationship.tpl') ) ) );
			
		} else if ('link' == $request->getOperationArg()) {

			$linkto = $request->get('_mlinkto');
			$foldername = $request->get('_folder');
			$connector = $this->getConnector($foldername);

            // This is to handle larger uploads
            $memory_limit = ConfigPrefs::get('MEMORY_LIMIT');
            ini_set('memory_limit', $memory_limit);

			$mail = $connector->openMail($request->get('_msgno'));
			$mail->attachments(); // Initialize attachments
			
			$linkedto = MailManager_RelationControllerAction::associate($mail, $linkto);

			$viewer->assign('LinkToAvailableActions', $this->linkToAvailableActions());
			$viewer->assign('AllowedModules', $this->getCurrentUserMailManagerAllowedModules());
			$viewer->assign('LINKEDTO', $linkedto);
			$viewer->assign('MSGNO', $request->get('_msgno'));
			$viewer->assign('FOLDER', $foldername);
			$response->setResult( array( 'ui' => $viewer->fetch( $this->getModuleTpl('Relationship.tpl') ) ) );
		
		} else if ('create_wizard' == $request->getOperationArg()) {
			global $currentModule;
			$moduleName = $request->get('_mlinktotype');
			$parent =  $request->get('_mlinkto');
			$foldername = $request->get('_folder');

			$connector = $this->getConnector($foldername);
			$mail = $connector->openMail($request->get('_msgno'));

			$qcreate_array = QuickCreate($moduleName);
			$validationData = $qcreate_array['data'];
			$data = split_validationdataArray($validationData);

			$qcreate_array['form'] = $this->processFormData($qcreate_array['form'], $mail);
			
			$viewer->assign("QUICKCREATE", $qcreate_array['form']);
			
			if($moduleName == 'Calendar')
				$viewer->assign("QCMODULE", getTranslatedString('Todo', 'Calendar'));
			elseif($moduleName == "HelpDesk")
				$viewer->assign("QCMODULE", getTranslatedString('Ticket', 'HelpDesk'));
			else
				$viewer->assign("QCMODULE",getTranslatedString("SINGLE_".$moduleName, $moduleName));

			$viewer->assign("PARENT", $parent);
			$viewer->assign("MODULE", $moduleName);
			$viewer->assign('MSGNO', $request->get('_msgno'));
			$viewer->assign('FOLDER', $foldername);
			$viewer->assign("VALIDATION_DATA_FIELDNAME",$data['fieldname']);
			$viewer->assign("VALIDATION_DATA_FIELDDATATYPE",$data['datatype']);
			$viewer->assign("VALIDATION_DATA_FIELDLABEL",$data['fieldlabel']);
			$viewer->display( $this->getModuleTpl('Relationship.CreateWizard.tpl') );
			$response = false;
		
		} else if ('create' == $request->getOperationArg()) {
			$linkModule = $request->get('_mlinktotype');
			$parent =  $request->get('_mlinkto');

			$focus = CRMEntity::getInstance($linkModule);
            
            // This is added as ModComments module has a bug that will not initialize column_fields
            // Basically $currentModule is set to MailManager, so the fields are not set properly.
            if(empty($focus->column_fields)) {
                $focus->column_fields = getColumnFields($linkModule);
            }
            
			setObjectValuesFromRequest($focus);
            
			if($request->get('assigntype') == 'U') {
				$focus->column_fields['assigned_user_id'] = $request->get('assigned_user_id');
			} elseif($request->get('assigntype') == 'T') {
				$focus->column_fields['assigned_user_id'] = $request->get('assigned_group_id');
			}

			$foldername = $request->get('_folder');

            if(!empty($foldername)) {
                // This is to handle larger uploads
                $memory_limit = ConfigPrefs::get('MEMORY_LIMIT');
                ini_set('memory_limit', $memory_limit);

                $connector = $this->getConnector($foldername);
                $mail = $connector->openMail($request->get('_msgno'));
                $attachments = $mail->attachments(); // Initialize attachments
            }
            
			$linkedto = MailManager_RelationControllerAction::getSalesEntityInfo($parent);
            
			switch ($linkModule) {
				case 'Calendar' :   if (empty($focus->column_fields['activitytype'])) {
                                        $focus->column_fields['activitytype'] = 'Task';
                                    }

                                    if (empty($focus->column_fields['due_date'])) {
                                        if(!empty($focus->column_fields['date_start'])) {
                                            $dateStart = getValidDBInsertDateValue($focus->column_fields['date_start']);
                                            $focus->column_fields['due_date'] = date("Y-m-d", strtotime(date("Y-m-d", strtotime($dateStart)) . " +1 day"));
                                        } else {
                                            $focus->column_fields['due_date'] = date('Y-m-d', strtotime("+1 day"));
                                        }
                                    }
                                    if(!empty($parent)) {
                                        if($linkedto['module'] == 'Contacts') {
                                            $focus->column_fields['contact_id'] = $parent;
                                        } else {
                                            $focus->column_fields['parent_id'] = $parent;
                                        }
                                    }
                                    break;

				case 'HelpDesk' :   $from = $mail->from();
                                    $focus->column_fields['parent_id'] = $this->setParentForHelpDesk($parent, $from);
                                    break;

                case 'ModComments': $focus->column_fields['assigned_user_id'] = $current_user->id;
                                    $focus->column_fields['creator'] = $current_user->id;
                                    $focus->column_fields['related_to'] = $parent;
                                    break;
			}

			try {
				$focus->save($linkModule);

                // This condition is added so that emails are not created for Tickets and Todo without Parent,
                // as there is no way to relate them
				if(empty($parent) && $linkModule != 'HelpDesk' && $linkModule != 'Calendar') {
					$linkedto = MailManager_RelationControllerAction::associate($mail, $focus->id);
				}

                // add attachments to the tickets as Documents
                if($linkModule == 'HelpDesk' && !empty($attachments)) {
                    $relationController = new MailManager_RelationControllerAction();
                    $relationController->__SaveAttachements($mail, $linkModule, $focus);
                }

				$viewer->assign('MSGNO', $request->get('_msgno'));
				$viewer->assign('LINKEDTO', $linkedto);
				$viewer->assign('AllowedModules', $this->getCurrentUserMailManagerAllowedModules());
				$viewer->assign('LinkToAvailableActions', $this->linkToAvailableActions());
                $viewer->assign('FOLDER', $foldername);
                
				$response->setResult( array( 'ui' => $viewer->fetch( $this->getModuleTpl('Relationship.tpl') ) ) );
			} catch(Exception $e) {
				$response->setResult( array( 'ui' => '', 'error' => $e ));
			}
			
		} else if ('savedraft' == $request->getOperationArg()) {
			$connector = $this->getConnector('__vt_drafts');
			$draftResponse = $connector->saveDraft($request);
			$response->setResult($draftResponse);
		} else if ('saveattachment' == $request->getOperationArg()) {
			$connector = $this->getConnector('__vt_drafts');
			$uploadResponse = $connector->saveAttachment($request);
			$response->setResult($uploadResponse);
		} else if ('commentwidget' == $request->getOperationArg()) {
            $viewer->assign('LINKMODULE', $request->get('_mlinktotype'));
            $viewer->assign('PARENT', $request->get('_mlinkto'));
            $viewer->assign('MSGNO', $request->get('_msgno'));
            $viewer->assign('FOLDER', $request->get('_folder'));
            $viewer->display( $this->getModuleTpl('MailManager.CommentWidget.tpl') );
			$response = false;
        }
		return $response;
	}

    /**
     * Returns the Parent for Tickets module
     * @global Users Instance $current_user
     * @param Integer $parent - crmid of Parent
     * @param Email Address $from - Email Address of the received mail
     * @return Integer - Parent(crmid)
     */
	public function setParentForHelpDesk($parent, $from) {
		global $current_user;
		if(empty($parent)) {
			if(!empty($from)) {
				$parentInfo = MailManager::lookupMailInVtiger($from[0], $current_user);
				if(!empty($parentInfo[0]['record'])) {
					$parentId = vtws_getIdComponents($parentInfo[0]['record']);
					return $parentId[1];
				}
			}
		} else {
			return $parent;
		}
	}


    /**
     * Function used to set the record fields with the information from mail.
     * @param Array $qcreate_array
     * @param MailManager_Model_Message $mail
     * @return Array
     */
	 function processFormData($qcreate_array, $mail) {
		$subject = $mail->subject();
		$from = $mail->from();
		
		if(!empty($from)) $mail_fromAddress = implode(',', $from);
		if(!empty($mail_fromAddress)) $name = explode('@', $mail_fromAddress);
		if(!empty($name[1])) $companyName = explode('.', $name[1]);
		
		$defaultFieldValueMap =  array( 'lastname'	=>	$name[0],
				'email'			=>	$mail_fromAddress,
				'email1'		=>	$mail_fromAddress,
				'accountname'	=> $companyName[0],
				'company'		=> $companyName[0], 
				'ticket_title'	=> $subject,
				'subject'		=> $subject,
		);
		$defaultFieldValueMapKeys = array_keys($defaultFieldValueMap);
		
		foreach($qcreate_array as $qc_array) {
			$new_qc_array = array();
			foreach($qc_array as $q_array) {
				if(in_array($q_array[2][0], $defaultFieldValueMapKeys)) {
					if($q_array[2][0] == "lastname") {
						$q_array[3][1] = $defaultFieldValueMap[$q_array[2][0]];
					} else {
						$q_array[3][0] = $defaultFieldValueMap[$q_array[2][0]];
					}
				}
				$new_qc_array[] = $q_array;
			}
			$new_qcreate_array[] = $new_qc_array;
		}
		return $new_qcreate_array;
	 }

     /**
      * Returns the available List of accessible modules for Mail Manager
      * @return Array
      */
	 public function getCurrentUserMailManagerAllowedModules() {
		 $moduleListForCreateRecordFromMail = array('Contacts', 'Accounts', 'Leads', 'HelpDesk', 'Calendar');
		 
		 foreach($moduleListForCreateRecordFromMail as $module) {
			if(MailManager::checkModuleWriteAccessForCurrentUser($module)) {
				$mailManagerAllowedModules[] = $module;
			}
		 }
		 return $mailManagerAllowedModules;
	 }

     /**
      * Returns the list of accessible modules on which Actions(Relationship) can be taken.
      * @return string 
      */
	 public function linkToAvailableActions() {
		 $moduleListForLinkTo = array('Calendar','HelpDesk','ModComments','Emails');
		 
		 foreach($moduleListForLinkTo as $module) {
			 if(MailManager::checkModuleWriteAccessForCurrentUser($module)) {
				 $mailManagerAllowedModules[] = $module;
			 }
		 }
		 return $mailManagerAllowedModules;
	 }
	 
	/**
	 * Helper function to scan for relations
	 */
	protected $wsDescribeCache = array();	
	function ws_describe($module) {
		global $current_user;
		if (!isset($this->wsDescribeCache[$module])) {
			$this->wsDescribeCache[$module] = vtws_describe( $module, $current_user );
		}
		return $this->wsDescribeCache[$module];
	}

    /**
     * Funtion used to build Web services query
     * @param String $module - Name of the module
     * @param String $text - Search String
     * @param String $type - Tyoe of fields Phone, Email etc
     * @return String
     */
	function buildSearchQuery($module, $text, $type) {
		$describe = $this->ws_describe($module);
		$whereClause = '';
		foreach($describe['fields'] as $field) {
			if (strcasecmp($type, $field['type']['name']) === 0) {
				$whereClause .= sprintf( " %s LIKE '%%%s%%' OR", $field['name'], $text );
			}
		}
		return sprintf( "SELECT %s FROM %s WHERE %s;", $describe['labelFields'], $module, rtrim($whereClause, 'OR') );
	}

    /**
     * Returns the List of Matching records with the Email Address
     * @global Users Instance $current_user
     * @param String $module
     * @param Email Address $email
     * @return Array
     */
	function lookupModuleRecordsWithEmail($module, $email) {
		global $current_user;
		$query = $this->buildSearchQuery($module, $email, 'EMAIL');
		$qresults = vtws_query( $query, $current_user );
		$describe = $this->ws_describe($module);
		$labelFields = explode(',', $describe['labelFields']);

		$results = array();
		foreach($qresults as $qresult) {
			$labelValues = array();
			foreach($labelFields as $fieldname) {
				if(isset($qresult[$fieldname])) $labelValues[] = $qresult[$fieldname]; 
			}
			$ids = vtws_getIdComponents($qresult['id']);
			$results[] = array( 'wsid' => $qresult['id'], 'id' => $ids[1], 'label' => implode(' ', $labelValues));
		}	
		return $results;
	}
}
?>