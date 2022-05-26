<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'modules/Users/UserPrivileges.php';
include_once 'modules/Users/authTypes/TwoFactorAuth/autoload.php';
include_once 'include/utils/utils.php';
require_once 'modules/com_vtiger_workflow/expression_engine/include.inc';
require_once 'modules/cbMap/cbMap.php';
require_once 'include/Webservices/Utils.php';
use \RobThree\Auth\TwoFactorAuth;

class VTSimpleTemplate {

	private $formatValue = true;

	public function __construct($templateString, $keepvariable = false, $formatValue = true) {
		$this->template = $templateString;
		$this->keepvariable = $keepvariable;
		$this->formatValue = $formatValue;
	}

	public function render($entityCache, $entityId, $void = array(), $context = array()) {
		$this->cache = $entityCache;
		$this->parent = $this->cache->forId($entityId);
		if (empty($context['__WorkflowID'])) {
			$this->parent->WorkflowID = 0;
		} else {
			$this->parent->WorkflowID = $context['__WorkflowID'];
		}
		$this->parent->WorkflowEvent = '';
		$this->parent->WorkflowContext = $context;
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
				if ($uitype == '15' || $uitype == '33') {
					$result = getTranslatedString($data[$fieldname], $moduleName);
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
				if ($this->keepvariable) {
					$result = '$'.$fieldname;
				} else {
					$result = '';
				}
			}
		} else {
			list($full, $referenceField, $referenceModule, $fieldname) = $matches;
			if ($referenceModule === '__VtigerMeta__') {
				$result = $this->getMetaValue($fieldname);
			} elseif ($referenceModule === '__WorkflowFunction__') {
				$result = $this->getWorkflowFunctionValue($fieldname);
			} else {
				$referenceId = $this->parent->get($referenceField);
				if ($referenceId==null && !$this->keepvariable) {
					$result = '';
				} else {
					try {
						if (strpos($referenceId, 'x')===false) {
							$referenceId = vtws_getEntityId($referenceModule).'x'.$referenceId;
						}
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
								if ($uitype == '15' || $uitype == '33') {
									$result = getTranslatedString($data[$fieldname], $referenceModule);
								} elseif ($uitype == '5' || $uitype == '6' || $uitype == '23') {  // date
									$date = new DateTimeField($data[$fieldname]);
									$result = $date->getDisplayDate($current_user);
								} elseif ($uitype == '50') {  // datetime
									$date = new DateTimeField($data[$fieldname]);
									$result = $date->getDisplayDateTimeValue($current_user);
								} elseif ($uitype == '71' || $uitype == '72' || $uitype == '7' || $uitype == '9') {
									if (!$this->formatValue) {
										return $data[$fieldname];
									}
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
								if ($this->keepvariable) {
									$result = '$'.$full;
								} else {
									$result = '';
								}
							}
						} else {
							if ($this->keepvariable) {
								$result = '$'.$full;
							} else {
								$result = '';
							}
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
			if (UserPrivileges::hasPrivileges($dfuid)) {
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
		require_once 'data/CRMEntity.php';
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
			$fields[$fidx] = trim($fields[$fidx]);
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
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('InventoryDetails');
		$wsid = $this->parent->getId();
		list($wsid,$crmid) = explode('x', $wsid);
		$relfield = getFirstFieldForModule('InventoryDetails', $this->parent->getModuleName());
		if ($relfield=='') {
			return '';
		}
		$cmsqlws = 'SELECT '.$field.'
			FROM vtiger_inventorydetails
			INNER JOIN '.$crmEntityTable.' ON vtiger_inventorydetails.inventorydetailsid=vtiger_crmentity.crmid
			LEFT JOIN vtiger_products ON vtiger_inventorydetails.productid=vtiger_products.productid
			LEFT JOIN vtiger_service ON vtiger_inventorydetails.productid=vtiger_service.serviceid
			WHERE vtiger_inventorydetails.'.$relfield.'=? order by vtiger_inventorydetails.sequence_no';
		$retcm = '';
		$wsid = $this->parent->getId();
		$cmrs = $adb->pquery($cmsqlws, array($crmid));
		if ($html) {
			$retcm .= '<div class="inventorydetailslist">';
		}
		if ($table) {
			$retcm .= '<table class="inventorydetails">';
		}
		while ($inv = $adb->fetch_array($cmrs)) {
			if ($html) {
				$retcm .= '<div class="inventorydetails"><ul class="inventorydetailsfields">';
			}
			if ($table) {
				$retcm .= '<tr class="inventorydetailsfields">';
			}
			foreach ($fields as $fname) {
				if ($html) {
					$retcm .= '<li class="inventorydetailsfield">';
				}
				if ($table) {
					$retcm .= '<td class="inventorydetailsfield">';
				}
				if (strpos($fname, '.')) { // FQN
					list($void, $fname) = explode('.', $fname);
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
			if ($html) {
				$retcm .= '</ul></div>';
			}
			if ($table) {
				$retcm .= '</tr>';
			}
		}
		if ($html) {
			$retcm .= '</div>';
		}
		if ($table) {
			$retcm .= '</table>';
		}
		return $retcm;
	}

	private function getRelatedList($relmodule, $format, $field, $rlcondition = '') {
		global $adb, $currentModule;
		$modulename = $this->parent->getModuleName();
		$mmtabid = getTabid($modulename);
		$rmtabid = getTabid($relmodule);
		$rs = $adb->pquery('select relation_id,name from vtiger_relatedlists where tabid=? and related_tabid=? limit 1', array($mmtabid, $rmtabid));
		if ($adb->num_rows($rs)==0) {
			return '';
		}
		$func = $adb->query_result($rs, 0, 'name');
		$relid = $adb->query_result($rs, 0, 'relation_id');
		$mod = CRMEntity::getInstance($modulename);
		$wsid = $this->parent->getId();
		list($wsid,$crmid) = explode('x', $wsid);
		$maptype = '';
		if (!empty($rlcondition)) {
			$cbmap = cbMap::getMapByID($rlcondition);
			if ($cbmap) {
				$maptype = $cbmap->column_fields['maptype']; // we can add condition query support here in the future
			}
		}
		$mod->retrieve_entity_info($crmid, $modulename);
		$mod->id = $crmid;
		$_REQUEST['relation_id'] = $relid;
		$_REQUEST['module'] = $modulename;
		$_REQUEST['record'] = $crmid;
		$_REQUEST['action'] = 'CallRelatedList';
		$hold = isset($currentModule) ? $currentModule : '';
		$currentModule = $modulename;
		$params = array($crmid, $mmtabid, $rmtabid);
		$relationData = call_user_func_array(array($mod, $func), array_values($params));
		$currentModule = $hold;
		if (empty($relationData['entries'])) {
			return '';
		}
		$fields = array();
		$idx = 1;
		$firstentry = reset($relationData['entries']);
		foreach ($firstentry as $row) {
			$matches = preg_match_all("/vtfieldname='(.+?)'/", $row, $fname);
			if ($matches) {
				$fields[$idx] = $fname[1][0];
			} else {
				$fields[$idx] = 'nodef';
			}
			$idx++;
		}
		if ($field!='*') {
			$fields2show = explode(',', $field);
		} else {
			$fields2show = $fields;
		}
		$format = strtolower($format);
		if ($format!='html' && $format!='text' && $format!='table') {
			$format = 'html';
		}
		$html = ($format == 'html');
		$table = ($format == 'table');
		$retrl = '';
		if ($html) {
			$retrl .= '<div class="relatedlist">';
		}
		if ($table) {
			$retrl .= '<table class="relatedlist"><tbody>';
		}
		$numelems = count($relationData['header']);
		$idx = 1;
		if ($html) {
			$retrl .= '<div class="relatedlistheaderrow"><ul class="relatedlistheaderfields">';
		}
		if ($table) {
			$retrl .= '<tr class="relatedlistheaderrow">';
		}
		foreach ($relationData['header'] as $row) {
			if ($idx==$numelems) {
				$idx++;
				continue;
			}
			if (!in_array($fields[$idx], $fields2show)) {
				$idx++;
				continue;
			}
			if ($html) {
				$retrl .= '<li class="relatedlistheaderfield">';
			}
			if ($table) {
				$retrl .= '<td class="relatedlistheaderfield">';
			}
			$retrl .= vtlib_purify(evvt_strip_html_links($row));
			if ($html) {
				$retrl .= '</li>';
			}
			if ($table) {
				$retrl .= '</td>';
			}
			$idx++;
		}
		if ($html) {
			$retrl .= '</ul></div>';
		}
		if ($table) {
			$retrl .= '</tr>';
		}
		if (!$html && !$table) {
			$retrl .= "\n";
		}
		$relmod_entId = vtws_getEntityId($relmodule);
		foreach ($relationData['entries'] as $rowindex => $row) {
			$relrecid = (int) $rowindex;
			try {
				if ($maptype == 'Condition Expression' && !$cbmap->ConditionExpression($relmod_entId.'x'.$relrecid)) {
					continue;
				}
			} catch (Exception $e) {
			}
			$idx = 1;
			if ($html) {
				$retrl .= '<div class="relatedlistrow"><ul class="relatedlistfields">';
			}
			if ($table) {
				$retrl .= '<tr class="relatedlistrow">';
			}
			foreach ($row as $fieldvalue) {
				if ($idx==$numelems) {
					$idx++;
					continue;
				}
				if (!in_array($fields[$idx], $fields2show)) {
					$idx++;
					continue;
				}
				if ($html) {
					$retrl .= '<li class="relatedlistfield">';
				}
				if ($table) {
					$retrl .= '<td class="relatedlistfield">';
				}
				$retrl .= str_replace('<span></span>', '', vtlib_purify(evvt_strip_html_links($fieldvalue)));
				if ($html) {
					$retrl .= '</li>';
				}
				if ($table) {
					$retrl .= '</td>';
				}
				$idx++;
			}
			if (!$html && !$table) {
				$retrl .= "\n";
			}
			if ($html) {
				$retrl .= '</ul></div>';
			}
			if ($table) {
				$retrl .= '</tr>';
			}
		}
		if ($html) {
			$retrl .= '</div>';
		}
		if ($table) {
			$retrl .= '</tbody></table>';
		}
		return $retrl;
	}

	private function getActivityRelatedEmailList($recid, $query) {
		global $adb;
		$modulename = $this->parent->getModuleName();
		if ($modulename!='cbCalendar') {
			return '';
		}
		list($wsid,$activity_id) = explode('x', $recid);
		$user_result = $adb->pquery($query, array($activity_id));
		$ulist = array();
		while ($user_result_row = $adb->fetch_array($user_result)) {
			if (!in_array($user_result_row['email1'], $ulist)) {
				$ulist[] = $user_result_row['email1'];
			}
		}
		if (!empty($ulist)) {
			return implode(',', $ulist);
		}
		return '';
	}

	private function getinvitedUsersList($recid) {
		return $this->getActivityRelatedEmailList(
			$recid,
			'SELECT vtiger_users.email1
			FROM vtiger_salesmanactivityrel
			inner join vtiger_users on vtiger_users.id=vtiger_salesmanactivityrel.smid
			where vtiger_salesmanactivityrel.activityid=? and vtiger_users.deleted=0'
		);
	}

	private function getRelatedContactsList($recid) {
		return $this->getActivityRelatedEmailList(
			$recid,
			'SELECT email
			FROM vtiger_contactdetails
			inner join vtiger_cntactivityrel on vtiger_cntactivityrel.contactid=vtiger_contactdetails.contactid
			inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid
			where vtiger_cntactivityrel.activityid=? and vtiger_crmentity.deleted=0'
		);
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
		return implode(',', $parts);
	}

	protected function useValue($data, $fieldname) {
		return !empty($data[$fieldname]);
	}

	public function parseTemplate() {
		global $current_user;
		$backCurrentUser = $current_user;
		$data = $this->parent->getData();
		if (!empty($data['assigned_user_id'])) {
			if (strpos($data['assigned_user_id'], 'x')>0) {
				list($wsId,$assigned_user_id) = explode('x', $data['assigned_user_id']);
			} else {
				$assigned_user_id = $data['assigned_user_id'];
			}
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
		}
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
		if (substr($fieldname, 0, 18)=='groupUsersWsIdList') {
			$groupname = trim(str_replace('_', ' ', substr($fieldname, 18)));
			$fieldname = 'groupUsersWsIdList';
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
			list($fieldname, $field) = explode('->', decode_html($fieldname));
		}
		if (substr($fieldname, 0, 16)=='inventorydetails') {
			$inventoryspec = substr($fieldname, 17);
			if (strlen($inventoryspec)>0) {
				$underscore = strpos($inventoryspec, '_');
				if ($underscore!==false) {
					$format = substr($inventoryspec, 0, $underscore);
					$field = substr($inventoryspec, $underscore+1);
				} else {
					$format = $inventoryspec;
					$field = '*';
				}
			} else {
				$format = 'html';
				$field = '*';
			}
			$fieldname = 'inventorydetails';
		}
		if (substr($fieldname, 0, 11) == 'scanBarCode') {
			list($fieldname, $codetype, $field) = explode('->', decode_html($fieldname));
		}
		$bmapid = '';
		if (strtolower(substr($fieldname, 0, 11)) == 'relatedlist') {
			$rlspec = substr($fieldname, 12);
			if (strpos($rlspec, 'bmap_') !== false) {
				$maploc = strpos($rlspec, 'bmap_');
				$mapsec = substr($rlspec, $maploc, strlen($rlspec));
				$rlspec = trim(str_replace($mapsec, '', $rlspec));
				list ($identifier, $bmapid) = explode('_', $mapsec);
				if (!intval($bmapid)) {
					$bmapid = cbMap::getMapIdByName($bmapid);
				}
			}
			if (strlen($rlspec)>0) {
				$underscore = strpos($rlspec, '_');
				if ($underscore!==false) {
					$relmodule = substr($rlspec, 0, $underscore);
					$rlspec = substr($rlspec, $underscore+1);
					if (strlen($rlspec)>0) {
						$underscore = strpos($rlspec, '_');
						if ($underscore!==false) {
							$format = substr($rlspec, 0, $underscore);
							$field = substr($rlspec, $underscore+1);
						} else {
							$format = $rlspec;
							$field = '*';
						}
					} else {
						$format = 'html';
						$field = '*';
					}
				} else {
					$relmodule = $rlspec;
					$format = 'html';
					$field = '*';
				}
			} else {
				return '';
			}
			$fieldname = 'RelatedList';
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
				return $site_URL.'/index.php?action=DetailView&module='.$moduleName.'&record='.$recordId;
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
				$PORTAL_URL = GlobalVariable::getVariable('Application_Customer_Portal_URL', 'https://your_support_domain.tld/customerportal');
				$PORTALUSED = GlobalVariable::getVariable('Application_Customer_Portal_BeingUsed', 'vtigercrmportal');
				switch ($PORTALUSED) {
					case 'coreboscp':
						$purl = $PORTAL_URL.'/index.php#vtentity/'.$moduleName.'/list/'.$wsId.'/dvcpage/0';
						break;
					case 'reactadmin':
						$purl = $PORTAL_URL.'/#/'.$moduleName.'/'.$wsId.'/show';
						break;
					case 'vtigercrmportal':
					default:
						$purl = $PORTAL_URL.'/index.php?module='.$moduleName.'&action=index&'.$recorIdName.'='.$recordId.'&fun=detail';
				}
				return $purl;
			case 'siteurl':
				return $site_URL;
			case 'portalurl':
				return GlobalVariable::getVariable('Application_Customer_Portal_URL', 'https://your_support_domain.tld/customerportal');
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
			case 'groupUsersWsIdList':
				return $this->getGroupFieldList($groupname, 'id');
			case 'Events_Users_Invited':
				return $this->getinvitedUsersList($this->parent->getId());
			case 'Events_Related_Contacts':
				return $this->getRelatedContactsList($this->parent->getId());
			case 'RelatedList':
				return $this->getRelatedList($relmodule, $format, $field, $bmapid);
			case 'scanQRCode':
				$wsId = $this->parent->getId();
				$parts = explode('x', $wsId);
				$recordId = $parts[1];
				$moduleName = $this->parent->getModuleName();
				$focus = CRMEntity::getInstance($moduleName);
				$focus->retrieve_entity_info($recordId, $moduleName);
				if (!empty($focus->column_fields[$field])) {
					$fieldValue = $focus->column_fields[$field];
					$tfa = new TwoFactorAuth('emailwfqrcode');
					$qrcodeprovider = $tfa->getQrCodeProvider();
					$img = $qrcodeprovider->getQRCodeImage($fieldValue, 150);
					$fname = uniqid('qrcode');
					@unlink('cache/images/'.$fname.'.png');
					file_put_contents('cache/images/'.$fname.'.png', $img);
					return '<img src="cid:'.$fname.'" />';
				} else {
					return '';
				}
			case 'scanBarCode':
				$wsId = $this->parent->getId();
				$parts = explode('x', $wsId);
				$recordId = $parts[1];
				$moduleName = $this->parent->getModuleName();
				$focus = CRMEntity::getInstance($moduleName);
				$focus->retrieve_entity_info($recordId, $moduleName);
				if (!empty($focus->column_fields[$field])) {
					$tcpdf1dEncodingTypes = array('EAN8' => 'EAN8','EAN13' => 'EAN13');
					$tcpdf2dEncodingTypes = array('QRCODE,L' => 'QRCODE,L','QRCODE,M' => 'QRCODE,M','QRCODE,Q' => 'QRCODE,Q','QRCODE,H' => 'QRCODE,H');
					$fieldValue = $focus->column_fields[$field];

					require_once 'include/tcpdf/tcpdf_barcodes_2d.php';
					require_once 'include/tcpdf/tcpdf_barcodes_1d.php';

					if (array_key_exists($codetype, $tcpdf1dEncodingTypes)) {
						$barcodeobj = new TCPDFBarcode($fieldValue, $codetype);
					} elseif (array_key_exists($codetype, $tcpdf2dEncodingTypes)) {
						$barcodeobj = new TCPDF2DBarcode($fieldValue, $codetype);
					}
					$img = $barcodeobj->getBarcodePngData();
					$fname = uniqid('barcode');
					@unlink('cache/images/'.$fname.'.png');
					file_put_contents('cache/images/'.$fname.'.png', $img);
					return '<img src="cid:'.$fname.'" />';
				} else {
					return '';
				}
			default:
				return '';
		}
	}

	public function getWorkflowFunctionValue($tplexpression) {
		$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($tplexpression)));
		$expression = $parser->expression();
		$exprEvaluater = new VTFieldExpressionEvaluater($expression);
		return $exprEvaluater->evaluate($this->parent);
	}
}
?>
