<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'modules/Users/authTypes/TwoFactorAuth/autoload.php';
use \RobThree\Auth\TwoFactorAuth;

class VTSimpleTemplate {

	public function __construct($templateString) {
		$this->template = $templateString;
	}

	public function render($entityCache, $entityId, $void = array()) {
		$this->cache = $entityCache;
		$this->parent = $this->cache->forId($entityId);
		return $this->parseTemplate();
	}

	private function matchHandler($match) {
		global $current_user;
		preg_match('/\((\w+) : \(([_\w]+)\) (.+)\)/', $match[1], $matches);
		if (count($matches)==0) {
			$fieldname = $match[1];
			$data = $this->parent->getData();
			if ($this->useValue($data, $fieldname)) {
				$moduleName = $this->parent->getModuleName();
				$module_focus = Vtiger_Module::getInstance($moduleName);
				$field_focus = Vtiger_Field::getInstance($fieldname, $module_focus);
				$uitype = $field_focus->uitype;
				if ($uitype == '15' || $uitype == '33' || ($uitype == '55' && $fieldname == 'salutationtype')) {
					$result = getTranslatedString($data[$fieldname], ($moduleName == 'Events') ? 'Calendar' : $moduleName);
				} elseif ($uitype == '5' || $uitype == '6' || $uitype == '23') {  // date
					$date = new DateTimeField($data[$fieldname]);
					$result = $date->getDisplayDate($current_user);
				} elseif ($uitype == '50') {  // datetime
					$date = new DateTimeField($data[$fieldname]);
					$result = $date->getDisplayDateTimeValue($current_user);
				} elseif ($uitype == '71' || $uitype == '72' || $uitype == '7' || $uitype == '9') {
					$currencyField = new CurrencyField($data[$fieldname]);
					$result = $currencyField->getDisplayValue(null, true);
				} elseif ($uitype == '2' && ($fieldname == 'time_start' || $fieldname == 'time_end')) { // time
					$date = new DateTimeField($data[$fieldname]);
					$result = $date->getDisplayTime($current_user);
				} else {
					$result = $data[$fieldname];
				}
			} else {
				$result ='';
			}
		} else {
			list($full, $referenceField, $referenceModule, $fieldname) = $matches;
			if ($referenceModule === '__VtigerMeta__') {
				$result = $this->getMetaValue($fieldname);
			} elseif ($referenceModule === '__WorkflowFunction__') {
				$result = $this->getWorkflowFunctionValue($fieldname);
			} else {
				$referenceId = $this->parent->get($referenceField);
				if ($referenceId==null) {
					$result="";
				} else {
					try {
						$entity = $this->cache->forId($referenceId);
						if ($referenceModule==='Users' && $entity->getModuleName()=='Groups') {
							$result = $this->getGroupFieldList($referenceId, $fieldname);
						} elseif ($entity->getModuleName()===$referenceModule) {
							$data = $entity->getData();
							if (isset($data['id']) && !isset($data['record_id']) && strpos($data['id'], 'x')) {
								list($wsid, $data['record_id']) = explode('x', $data['id']);
								$data['record_module'] = $referenceModule;
							}
							if ($this->useValue($data, $fieldname)) {
								if ($fieldname == 'record_id' || $fieldname == 'record_module') {
									$uitype = 0;
								} else {
									$module_focus = Vtiger_Module::getInstance($referenceModule);
									$field_focus = Vtiger_Field::getInstance($fieldname, $module_focus);
									$uitype = $field_focus->uitype;
								}
								if ($uitype == '15' || $uitype == '33' || ($uitype == '55' && $fieldname == 'salutationtype')) {
									$result = getTranslatedString($data[$fieldname], $referenceModule);
								} elseif ($uitype == '5' || $uitype == '6' || $uitype == '23') {  // date
									$date = new DateTimeField($data[$fieldname]);
									$result = $date->getDisplayDate($current_user);
								} elseif ($uitype == '50') {  // datetime
									$date = new DateTimeField($data[$fieldname]);
									$result = $date->getDisplayDateTimeValue($current_user);
								} elseif ($uitype == '71' || $uitype == '72' || $uitype == '7' || $uitype == '9') {
									$currencyField = new CurrencyField($data[$fieldname]);
									$result = $currencyField->getDisplayValue(null, true);
								} elseif ($uitype == '14' || ($uitype == '2' && ($fieldname == 'time_start' || $fieldname == 'time_end'))) { // time
									$date = new DateTimeField($data[$fieldname]);
									$result = $date->getDisplayTime($current_user);
								} elseif ($uitype == '10') { // related field
									list($void,$result) = explode('x', $data[$fieldname]);
								} else {
									$result = $data[$fieldname];
								}
							} else {
								$result = '';
							}
						} else {
							$result = '';
						}
					} catch (Exception $e) {
						$result = '';
					}
				}
			}
		}
		return $result;
	}

	private function getComments($num, $sort, $format, $field, $dtformat) {
		global $adb, $current_user;
		$wsId = $this->parent->getId();
		if (empty($wsId)) {
			return '';
		}
		$sort = strtolower($sort);
		if ($sort!='a' && $sort!='d') {
			$sort = 'a';
		}
		$format = strtolower($format);
		if ($format!='html' && $format!='text' && $format!='table') {
			$format = 'html';
		}
		$html = ($format == 'html');
		$table = ($format == 'table');
		if (substr($field, 0, 4)=='date' && strlen($field)>4) {
			$dateformat = substr($field, 4);
			$field = 'date';
		} else {
			$dateformat = $dtformat;
		}
		$field = strtolower($field);
		if ($field!='creator' && $field!='comment' && $field!='date' && $field!='all') {
			$field = 'all';
		}
		if ($field=='all') {
			$fields = array('creator','date','comment');
		} else {
			$fields = array($field);
		}
		$singlefield = (count($fields)==1);
		$datefmtuserid = $this->parent->get('assigned_user_id');
		if (!empty($datefmtuserid)) {
			list($void,$dfuid) = explode('x', $datefmtuserid);
			if (file_exists('user_privileges/user_privileges_' . $dfuid . '.php')) {
				$datefmtuser = new Users();
				$datefmtuser->retrieveCurrentUserInfoFromFile($dfuid);
			} else {
				$datefmtuser = $current_user;
			}
		} else {
			$datefmtuser = $current_user;
		}
		$moduleName = $this->parent->getModuleName();
		$wsUsersId = vtws_getEntityId('Users');
		include_once 'include/Webservices/GetRelatedRecords.php';
		$queryParameters = array(
			'orderby' => 'createdtime ' . ($sort=='a'?'asc':'desc'),
			'columns' => '*',
		);
		if ($num>0) {
			$queryParameters['limit'] = $num;
		}
		$cmsqlws = __getRLQuery($wsId, $moduleName, 'ModComments', $queryParameters, $current_user);
		$retcm = '';
		if ($html || $table) {
			$retcm = '<div class="comments">';
		}
		$cmrs = $adb->pquery($cmsqlws, array());
		while ($cm = $adb->fetch_array($cmrs)) {
			if ($html) {
				$retcm .= '<div class="commentdetails"><ul class="commentfields">';
			}
			if ($table) {
				$retcm .= '<table class="commentdetails"><tr class="commentfields">';
			}
			foreach ($fields as $field) {
				switch ($field) {
					case 'creator':
						if ($html) {
							$retcm .= '<li class="commentcreator">';
						}
						if ($table) {
							$retcm .= '<td class="commentcreator">';
						}
						if ($moduleName!='HelpDesk' && $moduleName!='Faq') {
							$wsid = $wsUsersId;
						} else {
							list($wsid,$crmid) = explode('x', $cm['creator']);
						}
						if ($wsUsersId==$wsid) {
							$un = getUserFullName($cm['creator']);
						} else {
							$un = getParentName($cm['creator']);
						}
						$retcm .= $un;
						if (!$html && !$table && !$singlefield) {
							$retcm .= "\n";
						}
						if ($html) {
							$retcm .= '</li>';
						}
						if ($table) {
							$retcm .= '</td>';
						}
						break;
					case 'date':
						if ($html) {
							$retcm .= '<li class="commentdate">';
						}
						if ($table) {
							$retcm .= '<td class="commentdate">';
						}
						if ($dateformat == 'user') {
							$dt = new DateTimeField($cm['createdtime']);
							$retcm .= $dt->getDisplayDateTimeValue($datefmtuser);
						} else {
							$dt = strtotime($cm['createdtime']);
							$retcm .= date($dateformat, $dt);
						}
						if (!$html && !$table && !$singlefield) {
							$retcm .= "\n";
						}
						if ($html) {
							$retcm .= '</li>';
						}
						if ($table) {
							$retcm .= '</td>';
						}
						break;
					case 'comment':
						if ($html) {
							$retcm .= '<li class="commentcomment">';
						}
						if ($table) {
							$retcm .= '<td class="commentcomment">';
						}
						$retcm .= vtlib_purify($cm['commentcontent']);
						if (!$html && !$table && !$singlefield) {
							$retcm .= "\n";
						}
						if ($html) {
							$retcm .= '</li>';
						}
						if ($table) {
							$retcm .= '</td>';
						}
						break;
				}
			}
			if ($html) {
				$retcm .= '</ul></div>';
			}
			if ($table) {
				$retcm .= '</tr></table>';
			}
		}
		if ($html ||$table) {
			$retcm .= '</div>';
		}
		return $retcm;
	}

	private function getInventoryDetails($format, $field) {
		global $adb, $current_user;
		$format = strtolower($format);
		if ($format!='html' && $format!='text'&& $format!='table') {
			$format = 'html';
		}
		$html = ($format == 'html');
		$table = ($format == 'table');
		$fieldsql = "case when vtiger_products.productid != '' then vtiger_products.productname else vtiger_service.servicename end as pdoname,";
		if ($field=='*') {
			$fieldsql .= 'vtiger_inventorydetails.quantity, vtiger_inventorydetails.listprice, vtiger_inventorydetails.extgross, vtiger_inventorydetails.linetotal';
		} else {
			$fieldsql .= strtolower($field);
		}
		$field = $fieldsql;
		$fields = explode(',', $field);
		for ($fidx = 0; $fidx<count($fields); $fidx++) {
			$fields[$fidx] = trim(str_replace('vtiger_inventorydetails.', '', $fields[$fidx]));
			if ($fields[$fidx]=='productname' || $fields[$fidx]=='servicename') {
				$fields[$fidx]='pdoname';
			}
			if ($fields[$fidx]=="case when vtiger_products.productid != '' then vtiger_products.productname else vtiger_service.servicename end as pdoname") {
				$fields[$fidx]='pdoname';
			}
		}
		$fields = array_unique($fields);
		$singlefield = (count($fields)==1);
		// Checking if field present in the table.
		// To Do.

		$moduleName = $this->parent->getModuleName();
		$wsUsersId = vtws_getEntityId('Users');
		$wsid = $this->parent->getId();
		list($wsid,$crmid) = explode('x', $wsid);
		$cmsqlws = 'SELECT '.$field.'
			FROM vtiger_inventorydetails
			INNER JOIN vtiger_crmentity ON vtiger_inventorydetails.inventorydetailsid=vtiger_crmentity.crmid
			LEFT JOIN vtiger_products ON vtiger_inventorydetails.productid=vtiger_products.productid
			LEFT JOIN vtiger_service ON vtiger_inventorydetails.productid=vtiger_service.serviceid
			WHERE related_to=?';
		$retcm = '';
		$wsid = $this->parent->getId();
		$cmrs = $adb->pquery($cmsqlws, array($crmid));
		if ($html) {
			$retcm .= '<div class="inventorydetails"><ul class="inventorydetailsfields">';
		}
		if ($table) {
			$retcm .= '<table class="inventorydetails"><tr class="inventorydetailsfields">';
		}
		while ($inv = $adb->fetch_array($cmrs)) {
			foreach ($fields as $fname) {
				if ($html) {
					$retcm .= '<li class="inventorydetailsfield">';
				}
				if ($table) {
					$retcm .= '<td class="inventorydetailsfield">';
				}
				$fval = vtlib_purify($inv[$fname]);
				if (is_numeric($fval)) {
					$fval = CurrencyField::convertToUserFormat($fval, null, true);
				}
				$retcm .= $fval;
				if (!$html && !$table && $singlefield) {
					$retcm .= "\n";
				}
				if ($html) {
					$retcm .= '</li>';
				}
				if ($table) {
					$retcm .= '</td>';
				}
			}
		}
		if ($html) {
			$retcm .= '</ul></div>';
		}
		if ($table) {
			$retcm .= '</tr></table>';
		}
		return $retcm;
	}

	private function getinvitedUsersList($recid) {
		global $adb;
		$modulename = $this->parent->getModuleName();
		if ($modulename!='Events') {
			return '';
		}
		list($wsid,$activity_id) = explode('x', $recid);
		$user_result = $adb->pquery(
			'SELECT vtiger_users.email1
			FROM vtiger_salesmanactivityrel
			inner join vtiger_users on vtiger_users.id=vtiger_salesmanactivityrel.smid
			where vtiger_salesmanactivityrel.activityid=? and vtiger_users.deleted=0',
			array($activity_id)
		);
		$ulist = array();
		while ($user_result_row = $adb->fetch_array($user_result)) {
			if (!in_array($user_result_row['email1'], $ulist)) {
				$ulist[] = $user_result_row['email1'];
			}
		}
		if (count($ulist)>0) {
			return implode(',', $ulist);
		}
		return '';
	}

	public function getGroupFieldList($groupref, $fieldname) {
		require_once 'include/utils/GetGroupUsers.php';
		$groupId = getGrpId($groupref);
		if ($groupId==0) {
			list($groupEntityId, $groupId) = vtws_getIdComponents($groupref);
		}
		$ggu = new GetGroupUsers();
		$ggu->getAllUsersInGroup($groupId);

		$users = $ggu->group_users;
		$parts = array();
		foreach ($users as $userId) {
			$refId = vtws_getWebserviceEntityId('Users', $userId);
			try {
				$entity = $this->cache->forId($refId);
			} catch (WebServiceException $ex) {
				continue; // inactive or deleted user
			}
			$data = $entity->getData();
			if ($this->useValue($data, $fieldname)) {
				$parts[] = $data[$fieldname];
			}
		}
		$result = implode(",", $parts);
		return $result;
	}

	protected function useValue($data, $fieldname) {
		return !empty($data[$fieldname]);
	}

	public function parseTemplate() {
		global $current_user;
		$data = $this->parent->getData();
		$backCurrentUser = $current_user;
		list($wsId,$assigned_user_id) = explode('x', $data['assigned_user_id']);
		if (vtws_getOwnerType($assigned_user_id) == 'Groups') {
			require_once 'include/utils/GetGroupUsers.php';
			$ggu = new GetGroupUsers();
			$ggu->getAllUsersInGroup($assigned_user_id);
			$users = $ggu->group_users;
			if (!empty($users)) {
				$assigned_user_id = $users[0];
			} else {
				$assigned_user_id = $backCurrentUser->id;
			}
		}
		$current_user = new Users();
		$current_user->retrieveCurrentUserInfoFromFile($assigned_user_id);
		$return = preg_replace_callback(
			'/\\$(\w+?|\(general : \(__WorkflowFunction__\) (.+?) \)[ |&._\-\]}<>\/!"%ºª?,;]|\((\w+) : \(([_\w]+)\) (.+)\))/U',
			array($this, 'matchHandler'),
			$this->template
		);
		$current_user = $backCurrentUser;
		return $return;
	}

	public function getMetaValue($fieldname) {
		require_once 'config.inc.php';
		global $site_URL, $current_user;
		$fieldname = trim($fieldname);
		if (substr($fieldname, 0, 14)=='groupEmailList') {
			$groupname = trim(str_replace('_', ' ', substr($fieldname, 14)));
			$fieldname = 'groupEmailList';
		}
		if (substr($fieldname, 0, 8)=='comments' || strpos($fieldname, '_comments')>0) {
			if (strpos($fieldname, '_')>0) {
				$commspec = explode('_', $fieldname);
				if ($commspec[0]!='comments') {
					$cs = array_reverse($commspec);
					$dtformat = array_pop($cs);
					$commspec = array_reverse($cs);
				} else {
					$dtformat = 'user';
				}
				$fieldname = 'comments';
				if (count($commspec)>1) {
					$srtcomm = substr($commspec[1], -1);
					if (is_numeric($srtcomm)) {
						$srtcomm = 'a';
						$numcomm = $commspec[1];
					} else {
						$numcomm = substr($commspec[1], 0, strlen($commspec[1])-1);
					}
				} else {
					$numcomm = 0;
					$srtcomm = 'a';
				}
				if (count($commspec)>2) {
					$fmtcomm = $commspec[2];
				} else {
					$fmtcomm = 'html';
				}
				if (count($commspec)>3) {
					$fldcomm = $commspec[3];
				} else {
					$fldcomm = 'all';
				}
			} else {
				$numcomm = 0;
				$srtcomm = 'a';
				$fmtcomm = 'html';
				$fldcomm = 'all';
				$dtformat = 'user';
			}
		}
		if (substr($fieldname, 0, 10) == 'scanQRCode') {
			$field = substr($fieldname, 16);
			$fieldname = 'scanQRCode';
		}
		if (substr($fieldname, 0, 16)=='inventorydetails') {
			$inventoryspec = explode('_', $fieldname);
			$format = isset($inventoryspec[1]) ? $inventoryspec[1] : 'html';
			$field = isset($inventoryspec[2]) ? $inventoryspec[2] : '*';
			$fieldname = 'inventorydetails';
		}
		switch ($fieldname) {
			case 'date':
				return getNewDisplayDate();
			case 'time':
				return getNewDisplayTime();
			case 'dbtimezone':
				return $current_user->column_fields['time_zone'];
			case 'crmdetailviewurl':
				$wsId = $this->parent->getId();
				$parts = explode('x', $wsId);
				$recordId = $parts[1];
				$moduleName = $this->parent->getModuleName();
				if ($moduleName == 'Events' || $moduleName == 'Calendar') {
					return $site_URL.'/index.php?action=DetailView&module=cbCalendar&record='.$recordId;
				} else {
					return $site_URL.'/index.php?action=DetailView&module='.$moduleName.'&record='.$recordId;
				}
			case 'portaldetailviewurl':
				$wsId = $this->parent->getId();
				$parts = explode('x', $wsId);
				$recordId = $parts[1];
				$moduleName = $this->parent->getModuleName();
				$recorIdName='id';
				if ($moduleName == 'HelpDesk') {
					$recorIdName = 'ticketid';
				}
				if ($moduleName == 'Faq') {
					$recorIdName = 'faqid';
				}
				if ($moduleName == 'Products') {
					$recorIdName = 'productid';
				}
				$PORTAL_URL = GlobalVariable::getVariable('Application_Customer_Portal_URL', 'http://your_support_domain.tld/customerportal');
				$PORTALUSED = GlobalVariable::getVariable('Application_Customer_Portal_BeingUsed', 'vtigercrmportal');
				switch ($PORTALUSED) {
					case 'coreboscp':
						$purl = $PORTAL_URL.'/index.php#vtentity/'.$moduleName.'/list/'.$wsId.'/dvcpage/0';
						break;
					case 'vtigercrmportal':
					default:
						$purl = $PORTAL_URL.'/index.php?module='.$moduleName.'&action=index&'.$recorIdName.'='.$recordId.'&fun=detail';
				}
				return $purl;
			case 'siteurl':
				return $site_URL;
			case 'portalurl':
				return GlobalVariable::getVariable('Application_Customer_Portal_URL', 'http://your_support_domain.tld/customerportal');
			case 'logo':
				return '<img src="cid:logo" />';
			case 'recordId':
				list($moduleId, $recordId) = vtws_getIdComponents($this->parent->getId());
				return $recordId;
			case 'supportName':
				return GlobalVariable::getVariable('HelpDesk_Support_Name', 'your-support name', 'HelpDesk');
			case 'supportEmailId':
				return GlobalVariable::getVariable('HelpDesk_Support_EMail', 'support@your_support_domain.tld', 'HelpDesk');
			case 'comments':
				return $this->getComments($numcomm, $srtcomm, $fmtcomm, $fldcomm, $dtformat);
			case 'inventorydetails':
				return $this->getInventoryDetails($format, $field);
			case 'groupEmailList':
				return $this->getGroupFieldList($groupname, 'email1');
			case 'Events_Users_Invited':
				return $this->getinvitedUsersList($this->parent->getId());
			case 'scanQRCode':
				$wsId = $this->parent->getId();
				$parts = explode('x', $wsId);
				$recordId = $parts[1];
				$moduleName = $this->parent->getModuleName();
				$focus = CRMEntity::getInstance($moduleName);
				$focus->retrieve_entity_info($recordId, $moduleName);
				$fieldValue = $focus->column_fields[$field];
				$tfa = new TwoFactorAuth('emailwfqrcode');
				$qrcodeprovider = $tfa->getQrCodeProvider();
				$img = $qrcodeprovider->getQRCodeImage($fieldValue, 150);
				$fname = uniqid('qrcode');
				@unlink('cache/images/'.$fname.'.png');
				file_put_contents('cache/images/'.$fname.'.png', $img);
				return '<img src="cid:'.$fname.'" />';
			default:
				'';
		}
	}

	public function getWorkflowFunctionValue($tplexpression) {
		$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($tplexpression)));
		$expression = $parser->expression();
		$exprEvaluater = new VTFieldExpressionEvaluater($expression);
		$exprEvaluation = $exprEvaluater->evaluate($this->parent);
		return $exprEvaluation;
	}
}
?>
