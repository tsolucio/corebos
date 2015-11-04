<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
class VTSimpleTemplate{

	function __construct($templateString){
		$this->template = $templateString;
	}

	function render($entityCache, $entityId){
		$this->cache = $entityCache;
		$this->parent = $this->cache->forId($entityId);
		return $this->parseTemplate();
	}

	private function matchHandler($match){
		global $current_user;
		preg_match('/\((\w+) : \(([_\w]+)\) (\w+)\)/', $match[1], $matches);
		if(count($matches)==0){
			$fieldname = $match[1];
			$data = $this->parent->getData();
			if($this->useValue($data, $fieldname)){
				$moduleName = $this->parent->getModuleName();
				$module_focus = Vtiger_Module::getInstance($moduleName);
				$field_focus = Vtiger_Field::getInstance($fieldname,$module_focus);
				
				$uitype = $field_focus->uitype;
				if($uitype == '15' || $uitype == '33' || ($uitype == '55' && $fieldname == 'salutationtype'))
					$result = getTranslatedString($data[$fieldname],($moduleName == 'Events') ? 'Calendar' : $moduleName);
				elseif ($uitype == '5' || $uitype == '6' || $uitype == '23') {  // date
					$date = new DateTimeField($data[$fieldname]);
					$result = $date->getDisplayDate($current_user);
				} elseif ($uitype == '70') {  // datetime
					$date = new DateTimeField($data[$fieldname]);
					$result = $date->getDisplayDateTimeValue($current_user);
				} elseif ($uitype == '2' && ($fieldname == "time_start" || $fieldname == "time_end")) {  // time
					$date = new DateTimeField($data[$fieldname]);
					$result = $date->getDisplayTime($current_user);
				} else
					$result = $data[$fieldname];
			}else{
				$result ='';
			}
		}else{
			list($full, $referenceField, $referenceModule, $fieldname) = $matches;
			if($referenceModule === '__VtigerMeta__'){
				$result = $this->getMetaValue($fieldname);
			}else{
				$referenceId = $this->parent->get($referenceField);
				if($referenceId==null){
					$result="";
				}else{
					try{
						$entity = $this->cache->forId($referenceId);
						if($referenceModule==="Users" && $entity->getModuleName()=="Groups"){
							$result = $this->getGroupFieldList($referenceId,$fieldname);
						} elseif($entity->getModuleName()===$referenceModule){
							$data = $entity->getData();
							if($this->useValue($data, $fieldname)){
								$module_focus = Vtiger_Module::getInstance($referenceModule);
								$field_focus = Vtiger_Field::getInstance($fieldname,$module_focus);
								
								$uitype = $field_focus->uitype;
								if($uitype == '15' || $uitype == '33' || ($uitype == '55' && $fieldname == 'salutationtype'))
									$result = getTranslatedString($data[$fieldname],$referenceModule);
								elseif ($uitype == '5' || $uitype == '6' || $uitype == '23') {  // date
									$date = new DateTimeField($data[$fieldname]);
									$result = $date->getDisplayDate($current_user);
								} elseif ($uitype == '70') {  // datetime
									$date = new DateTimeField($data[$fieldname]);
									$result = $date->getDisplayDateTimeValue($current_user);
								} elseif ($uitype == '2' && ($fieldname == "time_start" || $fieldname == "time_end")) {  // time
									$date = new DateTimeField($data[$fieldname]);
									$result = $date->getDisplayTime($current_user);
								} else
									$result = $data[$fieldname];
							}else{
								$result = '';
							}
						}else{
							$result = '';
						}
					}catch(Exception $e){
						$result = '';
					}
				}
			}
		}
		return $result;

	}

	function getGroupFieldList($groupref,$fieldname) {
		require_once('include/utils/GetGroupUsers.php');
		$groupId = getGrpId($groupref);
		if ($groupId==0) {
			list($groupEntityId, $groupId) = vtws_getIdComponents($groupref);
		}
		$ggu = new GetGroupUsers();
		$ggu->getAllUsersInGroup($groupId);

		$users = $ggu->group_users;
		$parts = Array();
		foreach($users as $userId){
			$refId = vtws_getWebserviceEntityId("Users", $userId);
			$entity = $this->cache->forId($refId);
			$data = $entity->getData();
			if($this->useValue($data, $fieldname)){
				$parts[] = $data[$fieldname];
			}
		}
		$result = implode(",", $parts);
		return $result;
	}

	protected function useValue($data, $fieldname) {
		return !empty($data[$fieldname]);
	}

	function parseTemplate(){
		global $current_user;
		$data = $this->parent->getData();
		$backCurrentUser = $current_user;
		list($wsId,$assigned_user_id) = explode('x',$data['assigned_user_id']);
		$current_user = new Users();
		$current_user->retrieveCurrentUserInfoFromFile($assigned_user_id);
		$return = preg_replace_callback('/\\$(\w+|\((\w+) : \(([_\w]+)\) (\w+)\))/', array($this,"matchHandler"), $this->template);
		$current_user = $backCurrentUser;
		return $return;
	}

	function getMetaValue($fieldname){
		require_once 'config.inc.php';
		global $site_URL, $PORTAL_URL, $current_user, $HELPDESK_SUPPORT_NAME, $HELPDESK_SUPPORT_EMAIL_ID;
		if (substr($fieldname,0,14)=='groupEmailList') {
			$groupname = trim(str_replace('_', ' ', substr($fieldname, 14)));
			$fieldname = 'groupEmailList';
		}
		switch($fieldname){
			case 'date'					:	return getNewDisplayDate();
			case 'time'					:	return getNewDisplayTime();
			case 'dbtimezone'			:	return $current_user->column_fields['time_zone'];
			case 'crmdetailviewurl'		:	$wsId = $this->parent->getId();
											$parts = explode('x', $wsId);
											$recordId = $parts[1];
											$moduleName = $this->parent->getModuleName();
											if($moduleName == 'Events' || $moduleName == 'Calendar')
											{
												if($moduleName == 'Events')
													return $site_URL.'/index.php?action=EventDetailView&module=Calendar4You&record='.$recordId.'&activity_mode=Events';
												elseif($moduleName == 'Calendar')
													return $site_URL.'/index.php?action=EventDetailView&module=Calendar4You&record='.$recordId.'&activity_mode=Task';
											}
											else
												return $site_URL.'/index.php?action=DetailView&module='.$moduleName.'&record='.$recordId;
			case 'portaldetailviewurl'	: 	$wsId = $this->parent->getId();
											$parts = explode('x', $wsId);
											$recordId = $parts[1];
											$moduleName = $this->parent->getModuleName();
											$recorIdName='id';
											if($moduleName == 'HelpDesk') $recorIdName = 'ticketid';
											if($moduleName == 'Faq') $recorIdName = 'faqid';
											if($moduleName == 'Products') $recorIdName = 'productid';
											return $PORTAL_URL.'/index.php?module='.$moduleName.'&action=index&'.$recorIdName.'='.$recordId.'&fun=detail';
			case 'siteurl'				: return $site_URL;
			case 'portalurl'			: return $PORTAL_URL;
			case 'logo'					: return '<img src="cid:logo" />';

			case 'recordId'				: list($moduleId, $recordId) = vtws_getIdComponents($this->parent->getId());
										  return $recordId;

			case 'supportName'			: return $HELPDESK_SUPPORT_NAME;
			case 'supportEmailId'		: return $HELPDESK_SUPPORT_EMAIL_ID;
			case 'groupEmailList'		: return $this->getGroupFieldList($groupname,'email1');
			default: '';
		}
	}
}
?>
