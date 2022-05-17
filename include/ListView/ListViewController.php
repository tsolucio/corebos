<?php
/*+*******************************************************************************
 *  The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

class ListViewController {

	private $queryGenerator;
	private $db;
	private $nameList;
	private $typeList;
	private $ownerNameList;
	private $ownerNameListrel;
	private $user;
	private $picklistValueMap;
	private $picklistRoleMap;
	private $headerSortingEnabled;

	public function __construct($data_db, $user, $generator) {
		$this->queryGenerator = $generator;
		$this->db = $data_db;
		$this->user = $user;
		$this->nameList = array();
		$this->typeList = array();
		$this->ownerNameList = array();
		$this->picklistValueMap = array();
		$this->picklistRoleMap = array();
		$this->headerSortingEnabled = true;
	}

	public function isHeaderSortingEnabled() {
		return $this->headerSortingEnabled;
	}

	public function setHeaderSorting($enabled) {
		$this->headerSortingEnabled = $enabled;
	}

	public function setupAccessiblePicklistValueList($name) {
		$isRoleBased = vtws_isRoleBasedPicklist($name);
		$this->picklistRoleMap[$name] = $isRoleBased;
		if ($this->picklistRoleMap[$name]) {
			$this->picklistValueMap[$name] = getAssignedPicklistValues($name, $this->user->roleid, $this->db);
		}
	}

	public function fetchNameList($field, $result, $rel = null) {
		$referenceFieldInfoList = $this->queryGenerator->getReferenceFieldInfoList();
		$fieldName = $field->getFieldName();
		$rowCount = $this->db->num_rows($result);

		$idList = array();
		for ($i = 0; $i < $rowCount; $i++) {
			if ($rel==1) {
				$modrel=getTabModuleName($field->getTabId());
				$colname=strtolower($modrel).$field->getColumnName();
			} else {
				$colname=$field->getColumnName();
			}
			$id = $this->db->query_result($result, $i, $colname);
			if (!isset($this->nameList[$fieldName][$id])) {
				$idList[$id] = $id;
			}
		}

		$idList = array_keys($idList);
		if (empty($idList)) {
			return;
		}
		if (isset($referenceFieldInfoList[$fieldName])) {
			$moduleList = $referenceFieldInfoList[$fieldName];
		} else {
			$moduleList = array();
		}
		foreach ($moduleList as $module) {
			$meta = $this->queryGenerator->getMeta($module);
			if ($meta->isModuleEntity()) {
				if ($module == 'Users') {
					$listName = getOwnerNameList($idList);
				} else {
					//TODO handle multiple module names overriding each other.
					$listName = getEntityName($module, $idList);
				}
			} else {
				$listName = vtws_getActorEntityName($module, $idList);
			}
			$entityTypeList = array_intersect(array_keys($listName), $idList);
			foreach ($entityTypeList as $id) {
				$this->typeList[$id] = $module;
			}
			if (empty($this->nameList[$fieldName])) {
				$this->nameList[$fieldName] = array();
			}
			foreach ($entityTypeList as $id) {
				$this->typeList[$id] = $module;
				$this->nameList[$fieldName][$id] = $listName[$id];
			}
		}
	}

	/**This function generates the List view entries in a list view
	 * Param $focus - module object
	 * Param $result - resultset of a listview query
	 * Param $navigation_array - navigation values in an array
	 * Param $relatedlist - check for related list flag
	 * Param $returnset - list query parameters in url string
	 * Param $edit_action - Edit action value
	 * Param $del_action - delete action value
	 * Param $oCv - vtiger_customview object
	 * Returns an array type
	 */
	public function getListViewEntries($focus, $module, $result, $navigationInfo, $skipActions = false) {
		global $theme, $default_charset, $current_user, $currentModule, $adb;
		$is_admin = is_admin($current_user);
		$listview_max_textlength = GlobalVariable::getVariable('Application_ListView_Max_Text_Length', 40, $currentModule);
		$fields = $this->queryGenerator->getFields();
		$meta = $this->queryGenerator->getMeta($this->queryGenerator->getModule());

		$moduleFields = $meta->getModuleFields();
		$accessibleFieldList = array_keys($moduleFields);
		if ($this->queryGenerator->getReferenceFieldInfoList()) {
			$accessibleFieldList = array_merge($this->queryGenerator->getReferenceFieldNameList(), $accessibleFieldList);
		}
		$listViewFields = array_intersect($fields, $accessibleFieldList);

		$referenceFieldList = $this->queryGenerator->getReferenceFieldList();
		foreach ($referenceFieldList as $fieldName) {
			if (in_array($fieldName, $listViewFields)) {
				$field = $moduleFields[$fieldName];
				$this->fetchNameList($field, $result);
			}
		}

		$rowCount = $this->db->num_rows($result);
		$listviewcolumns = $this->db->getFieldsArray($result);
		$ownerFieldList = $this->queryGenerator->getOwnerFieldList();
		foreach ($ownerFieldList as $fieldName) {
			if (in_array($fieldName, $listViewFields)) {
				if (!empty($moduleFields[$fieldName])) {
					$field = $moduleFields[$fieldName];
				} else {
					$field = $this->queryGenerator->getReferenceField($fieldName, false);
					if (is_null($field)) {
						continue;
					}
				}
				$fldcolname = $field->getColumnName();
				$idList = array();
				for ($i = 0; $i < $rowCount; $i++) {
					$id = $this->db->query_result($result, $i, $fldcolname);
					if (!isset($this->ownerNameList[$fieldName][$id])) {
						$idList[] = $id;
					}
				}
				if (!empty($idList)) {
					if (!isset($this->ownerNameList[$fieldName]) || !is_array($this->ownerNameList[$fieldName])) {
						$this->ownerNameList[$fieldName] = getOwnerNameList($idList);
					} else {
						$newOwnerList = getOwnerNameList($idList);
						$this->ownerNameList[$fieldName] = $this->ownerNameList[$fieldName] + $newOwnerList;
					}
				}
			}
		}
		$k = 0;
		foreach ($listViewFields as $fieldName) {
			if (!empty($moduleFields[$fieldName])) {
				$field = $moduleFields[$fieldName];
			} else {
				$field = $this->queryGenerator->getReferenceField($fieldName, false);
				if (is_null($field)) {
					continue;
				}
			}
			if (!$is_admin && ($field->getFieldDataType() == 'picklist' || $field->getFieldDataType() == 'multipicklist')) {
				$this->setupAccessiblePicklistValueList($field->getFieldName());
			}
			$idList=array();
			if ($fieldName!='assigned_user_id' && false !== strpos($fieldName, '.assigned_user_id')) {
				$modrel=getTabModuleName($field->getTabId());
				$fldcolname = 'smowner'.strtolower($modrel);
				$j=$rowCount*$k;
				$k++;
				for ($i = 0; $i < $rowCount; $i++) {
					$id = $this->db->query_result($result, $i, $fldcolname);
					if (!isset($this->ownerNameListrel[$fieldName][$id])) {
						$idList[$j] = $id;
						$j++;
					}
				}
			} elseif (getTabid($currentModule)!=$field->getTabId() && $field->getFieldDataType()=='reference') {
				$this->fetchNameList($field, $result, 1);
			}
			if (!empty($idList)) {
				if (!isset($this->ownerNameListrel[$fieldName]) || !is_array($this->ownerNameListrel[$fieldName])) {
					$this->ownerNameListrel[$fieldName] = getOwnerNameList($idList);
				} else {
					$newOwnerList = getOwnerNameList($idList);
					$this->ownerNameListrel[$fieldName] = $this->ownerNameListrel[$fieldName] + $newOwnerList;
				}
			}
		}

		$useAsterisk = get_use_asterisk($this->user->id);
		$wfs = new VTWorkflowManager($adb);
		$totals = array();
		$data = array();

		$tabid = getTabid($currentModule);
		include_once 'vtlib/Vtiger/Link.php';
		$customlink_params = array('MODULE'=>$currentModule, 'ACTION'=>vtlib_purify($_REQUEST['action']));
		for ($i = 0; $i < $rowCount; ++$i) {
			//Getting the recordId
			if ($module != 'Users') {
				$baseTable = $meta->getEntityBaseTable();
				$moduleTableIndexList = $meta->getEntityTableIndexList();
				$baseTableIndex = $moduleTableIndexList[$baseTable];
				$recordId = $this->db->query_result($result, $i, $baseTableIndex);
			} else {
				$recordId = $this->db->query_result($result, $i, 'id');
			}
			$row = array();

			foreach ($listViewFields as $fieldName) {
				if (!empty($moduleFields[$fieldName])) {
					$field = $moduleFields[$fieldName];
				} else {
					$field = $this->queryGenerator->getReferenceField($fieldName, false);
					if (is_null($field)) {
						continue;
					}
				}
				$uitype = $field->getUIType();
				if ($fieldName!='assigned_user_id' && false !== strpos($fieldName, '.assigned_user_id')) {
					$modrel=getTabModuleName($field->getTabId());
					$rawValue = $this->db->query_result($result, $i, 'smowner'.strtolower($modrel));
				} elseif (getTabid($currentModule)!=$field->getTabId()) {
					$modrel=getTabModuleName($field->getTabId());
					$relfieldname = strtolower($modrel).$field->getColumnName();
					if (in_array($relfieldname, $listviewcolumns)) {
						$rawValue = $this->db->query_result($result, $i, $relfieldname);
					} else {
						$rawValue = $this->db->query_result($result, $i, $field->getColumnName());
					}
				} else {
					$rawValue = $this->db->query_result($result, $i, $field->getColumnName());
				}

				if ($uitype != 8) {
					$value = html_entity_decode($rawValue, ENT_QUOTES, $default_charset);
				} else {
					$value = $rawValue;
				}

				if (($module == 'Documents' && $fieldName == 'filename') || $fieldName == 'Documents.filename') {
					if ($fieldName == 'Documents.filename') {
						$docrs = $this->db->pquery(
							'select filename,filelocationtype,filestatus,notesid from vtiger_notes where note_no=?',
							array($this->db->query_result($result, $i, 'documentsnote_no'))
						);
						$downloadtype = $this->db->query_result($docrs, 0, 'filelocationtype');
						$fileName = $this->db->query_result($docrs, 0, 'filename');
						$status = $this->db->query_result($docrs, 0, 'filestatus');
						$docid = $this->db->query_result($docrs, 0, 'notesid');
					} else {
						$docid = $recordId;
						$downloadtype = $this->db->query_result($result, $i, 'filelocationtype');
						$fileName = $this->db->query_result($result, $i, 'filename');
						$status = $this->db->query_result($result, $i, 'filestatus');
					}
					$fileIdQuery = 'select attachmentsid from vtiger_seattachmentsrel where crmid=?';
					$fileIdRes = $this->db->pquery($fileIdQuery, array($docid));
					$fileId = $this->db->query_result($fileIdRes, 0, 'attachmentsid');
					$fileicon = FileField::getFileIcon($value, $downloadtype, $module);
					if ($fileicon=='') {
						$value = ' --';
					}
					if ($fileName != '' && $status == 1) {
						if ($downloadtype == 'I') {
							$value = "<a href='index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=downloadfile".
								"&entityid=$docid&fileid=$fileId' title='".getTranslatedString('LBL_DOWNLOAD_FILE', $module).
								"' onclick='javascript:dldCntIncrease($docid);'>".textlength_check($value).'</a>';
						} elseif ($downloadtype == 'E') {
							$value = "<a target='_blank' href='$fileName' onclick='javascript:".
								"dldCntIncrease($docid);' title='".getTranslatedString('LBL_DOWNLOAD_FILE', $module)."'>".textlength_check($value).'</a>';
						} else {
							$value = ' --';
						}
					}
					$value = $fileicon.$value;
				} elseif ($module == 'Documents' && $fieldName == 'filesize') {
					$downloadType = $this->db->query_result($result, $i, 'filelocationtype');
					if ($downloadType == 'I') {
						$value = FileField::getFileSizeDisplayValue($value);
					} else {
						$value = ' --';
					}
				} elseif ($module == 'Documents' && $fieldName == 'filestatus') {
					$value = BooleanField::getBooleanDisplayValue($value, $module);
				} elseif ($module == 'Documents' && $fieldName == 'filetype') {
					$downloadType = $this->db->query_result($result, $i, 'filelocationtype');
					if ($downloadType == 'E' || $downloadType != 'I') {
						$value = '--';
					}
				} elseif ($field->getUIType() == '27') {
					if ($value == 'I') {
						$value = getTranslatedString('LBL_INTERNAL', $module);
					} elseif ($value == 'E') {
						$value = getTranslatedString('LBL_EXTERNAL', $module);
					} else {
						$value = ' --';
					}
				} elseif ($field->getFieldDataType() == 'date' || $field->getFieldDataType() == 'datetime') {
					if (!empty($value) && $value != '0000-00-00' && $value != '0000-00-00 00:00') {
						$date = new DateTimeField($value);
						$value = $date->getDisplayDate();
						if ($field->getFieldDataType() == 'datetime') {
							$value .= ' ' . $date->getDisplayTime();
							$user_format = ($current_user->hour_format=='24' ? '24' : '12');
							if ($user_format != '24') {
								$curr_time = DateTimeField::formatUserTimeString($value, '12');
								$time_format = substr($curr_time, -2);
								$curr_time = substr($curr_time, 0, 5);
								list($dt,$tm) = explode(' ', $value);
								$value = $dt . ' ' . $curr_time . $time_format;
							}
						}
					} elseif (empty($value) || $value == '0000-00-00' || $value == '0000-00-00 00:00') {
						$value = '';
					}
				} elseif ($field->getFieldDataType() == 'currency') {
					if ($value != '') {
						if (!isset($totals[$fieldName])) {
							$totals[$fieldName]=0;
						}
						$totals[$fieldName] = $totals[$fieldName] + $value;
						if ($field->getUIType() == 72) {
							if ($fieldName == 'unit_price') {
								$currencyId = getProductBaseCurrency($recordId, $module);
								$cursym_convrate = getCurrencySymbolandCRate($currencyId);
								$currencySymbol = $cursym_convrate['symbol'];
							} else {
								$currencyInfo = getInventoryCurrencyInfo($module, $recordId);
								$currencySymbol = $currencyInfo['currency_symbol'];
							}
							$currencyValue = CurrencyField::convertToUserFormat($value, null, true);
							$value = CurrencyField::appendCurrencySymbol($currencyValue, $currencySymbol);
						} else {
							$value = CurrencyField::convertToUserFormat($value);
						}
						$value = '<span style="float:right;padding-right:10px;">'.$value.'</span>';
					}
				} elseif ($field->getFieldDataType() == 'double') {
					if ($value != '') {
						$value = CurrencyField::convertToUserFormat($value, $current_user, true);
					}
				} elseif ($field->getFieldDataType() == 'url') {
					$matchPattern = "^[\w]+:\/\/^";
					preg_match($matchPattern, $rawValue, $matches);
					if (!empty($matches[0])) {
						$value = '<a href="'.$rawValue.'" target="_blank">'.textlength_check($value).'</a>';
					} else {
						$value = '<a href="http://'.$rawValue.'" target="_blank">'.textlength_check($value).'</a>';
					}
				} elseif ($field->getFieldDataType() == 'email') {
					if ($_SESSION['internal_mailer'] == 1) {
						//check added for email link in user detailview
						$fieldId = $field->getFieldId();
						$value = "<a href=\"javascript:InternalMailer($recordId,$fieldId,".
						"'$fieldName','$module','record_id');\">".textlength_check($value).'</a>';
					} else {
						$value = '<a href="mailto:'.$rawValue.'">'.textlength_check($value).'</a>';
					}
				} elseif ($field->getFieldDataType() == 'boolean') {
					$value = BooleanField::getBooleanDisplayValue($value, $module);
				} elseif ($field->getUIType() == 98) {
					$value = '<a href="index.php?action=RoleDetailView&module=Settings&roleid='.$value.'">'.textlength_check(getRoleName($value)).'</a>';
				} elseif ($field->getUIType() == '69m') {
					if ($module == 'Products') {
						$queryPrdt = 'SELECT vtiger_attachments.path,vtiger_attachments.attachmentsid,vtiger_attachments.`name`
							FROM vtiger_attachments
							INNER JOIN vtiger_seattachmentsrel ON vtiger_attachments.attachmentsid = vtiger_seattachmentsrel.attachmentsid
							INNER JOIN vtiger_products ON vtiger_seattachmentsrel.crmid = vtiger_products.productid
							where vtiger_seattachmentsrel.crmid=?';
						$resultprdt = $this->db->pquery($queryPrdt, array($recordId));
						if ($resultprdt && $this->db->num_rows($resultprdt)>0) {
							$imgpath = $this->db->query_result($resultprdt, 0, 'path');
							$attid = $this->db->query_result($resultprdt, 0, 'attachmentsid');
							$imgfilename = $this->db->query_result($resultprdt, 0, 'name');
							$value = "<div style='text-align:center;width:100%;'><img src='./".$imgpath.$attid.'_'.$imgfilename."' height='50'></div>";
						} else {
							$value = '';
						}
					}
				} elseif ($field->getUIType() == 69) {
					if ($module == 'Contacts' && $field->getColumnName()=='imagename') {
						$imageattachment = 'Image';
					} else {
						$imageattachment = 'Attachment';
					}
					$sql = "select vtiger_attachments.*,vtiger_crmentity.setype
						from vtiger_attachments
						inner join vtiger_seattachmentsrel on vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
						inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
						where vtiger_crmentity.setype='$module $imageattachment' and vtiger_attachments.name = ? and vtiger_seattachmentsrel.crmid=?";
					$image_res = $this->db->pquery($sql, array(str_replace(' ', '_', $value),$recordId));
					$image_id = $this->db->query_result($image_res, 0, 'attachmentsid');
					$image_path = $this->db->query_result($image_res, 0, 'path');
					$image_name = decode_html($this->db->query_result($image_res, 0, 'name'));
					$imgpath = $image_path . $image_id . '_' . urlencode($image_name);
					if ($image_name != '') {
						$ftype = $this->db->query_result($image_res, 0, 'type');
						$isimage = stripos($ftype, 'image') !== false;
						if ($isimage) {
							$imgtxt = getTranslatedString('SINGLE_'.$module, $module).' '.getTranslatedString('Image');
							$value = '<div style="width:100%;text-align:center;"><img src="' . $imgpath . '" alt="' . $imgtxt . '" title= "'.
									$imgtxt . '" style="max-width: 50px;"></div>';
						} else {
							$imgtxt = getTranslatedString('SINGLE_'.$module, $module).' '.getTranslatedString('SINGLE_Documents');
							$value = '<a href="' . $imgpath . '" alt="' . $imgtxt . '" title= "' . $imgtxt . '" target="_blank">'.$image_name.'</a>';
						}
					} else {
						$value = '';
					}
				} elseif ($field->getFieldDataType() == 'multipicklist') {
					$value = ($value != '') ? str_replace(Field_Metadata::MULTIPICKLIST_SEPARATOR, ', ', $value) : '';
					if (!$is_admin && $value != '') {
						$valueArray = ($rawValue != '') ? explode(Field_Metadata::MULTIPICKLIST_SEPARATOR, $rawValue) : array();
						$tmp = '';
						$tmpArray = array();
						foreach ($valueArray as $val) {
							if (!$is_admin && !in_array(trim(decode_html($val)), $this->picklistValueMap[$field->getFieldName()])) {
								continue;
							}
							if (!$listview_max_textlength || strlen(preg_replace("/(<\/?)(\w+)([^>]*>)/i", '', $tmp)) <= $listview_max_textlength) {
								$tmpArray[] = $val;
								$tmp .= ', '.$val;
							} else {
								$tmpArray[] = '...';
								$tmp .= '...';
							}
						}
						$value = implode(', ', $tmpArray);
						$value = textlength_check($value);
					}
				} elseif ($uitype == 1616) {
					$cvrs = $adb->pquery('select viewname,entitytype from vtiger_customview where cvid=?', array($value));
					if ($cvrs && $adb->num_rows($cvrs)>0) {
						$cv = $adb->fetch_array($cvrs);
						$value = $cv['viewname'].' ('.getTranslatedString($cv['entitytype'], $cv['entitytype']).')';
					}
				} elseif ($field->getUIType() == 1024) {
					if ($value != '') {
						$value = textlength_check(implode(', ', array_map('getRoleName', explode(Field_Metadata::MULTIPICKLIST_SEPARATOR, $value))));
					}
				} elseif ($field->getUIType() == 1025) {
					$content=array();
					if ($value != '') {
						$values=explode(Field_Metadata::MULTIPICKLIST_SEPARATOR, $value);
						foreach ($values as $crmid) {
							$srchmod=  getSalesEntityType($crmid);
							$displayValueArray = getEntityName($srchmod, $crmid);
							if (!empty($displayValueArray)) {
								foreach ($displayValueArray as $value2) {
									$shown_val = $value2;
								}
							}
							if (!(vtlib_isModuleActive($srchmod) && isPermitted($srchmod, 'DetailView', $crmid))) {
								$content[]=textlength_check($shown_val);
							} else {
								$content[]='<a href="index.php?module='.$srchmod.'&action=DetailView&record='.$crmid.'">'.textlength_check($shown_val).'</a>';
							}
						}
					}
					$value = implode(', ', $content);
				} elseif ($field->getUIType() == 3313 || $field->getUIType() == 3314) {
					require_once 'modules/PickList/PickListUtils.php';
					$modlist = explode(Field_Metadata::MULTIPICKLIST_SEPARATOR, $value);
					$modlist = array_map(
						function ($m) {
							return getTranslatedString($m, $m);
						},
						$modlist
					);
					$value = implode(', ', $modlist);
				} elseif ($field->getUIType() == '1613' || $field->getUIType() == '1614') {
					$value = getTranslatedString($value, $value);
					$value = textlength_check($value);
				} elseif ($field->getFieldDataType() == 'picklist') {
					$value = getTranslatedString($value, $module);
					$value = textlength_check($value);
				} elseif ($field->getFieldDataType() == 'skype') {
					$value = ($value != '') ? "<a href='skype:$value?call'>".textlength_check($value).'</a>' : '';
				} elseif ($field->getFieldDataType() == 'phone') {
					if ($useAsterisk == 'true') {
						$value = "<a href='javascript:;' onclick='startCall(&quot;$value&quot;, &quot;$recordId&quot;)'>".textlength_check($value).'</a>';
					} else {
						$value = textlength_check($value);
					}
				} elseif ($field->getFieldDataType() == 'reference') {
					$referenceFieldInfoList = $this->queryGenerator->getReferenceFieldInfoList();
					if (getTabid($currentModule)!=$field->getTabId()) {
						$modrel=getTabModuleName($field->getTabId());
						$fieldName=str_replace($modrel.'.', '', $fieldName);
					}
					if (isset($referenceFieldInfoList[$fieldName])) {
						$moduleList = $referenceFieldInfoList[$fieldName];
					} else {
						$setype = getSalesEntityType($value);
						$moduleList = array($setype);
						if (!isset($this->nameList[$fieldName])) {
							$this->nameList[$fieldName] = array();
						}
						if (!isset($this->nameList[$fieldName][$value]) && !empty($value)) {
							$en = getEntityName($setype, $value);
							if (empty($en)) {
								$en = getUserFullName($value);
								$this->nameList[$fieldName][$value] = $en;
							} else {
								$this->nameList[$fieldName][$value] = $en[$value];
							}
						}
					}
					if (!empty($value) && !empty($this->nameList[$fieldName]) && !empty($this->nameList[$fieldName][$value])) {
						if (count($moduleList) == 1) {
							$parentModule = $moduleList[0];
						} else {
							$parentModule = $this->typeList[$value];
						}
						if (!empty($parentModule)) {
							$parentMeta = $this->queryGenerator->getMeta($parentModule);
							$value = textlength_check($this->nameList[$fieldName][$value]);
							if ($parentMeta->isModuleEntity() && $parentModule != 'Users') {
								$value = "<a href='index.php?module=$parentModule&action=DetailView&".
									"record=$rawValue' title='".getTranslatedString($parentModule, $parentModule)."'>$value</a>";
								$modMetaInfo=getEntityFieldNames($parentModule);
								$fieldName=(is_array($modMetaInfo['fieldname']) ? $modMetaInfo['fieldname'][0] : $modMetaInfo['fieldname']);
								// vtlib customization: For listview javascript triggers
								$value = "$value <span type='vtlib_metainfo' vtrecordid='{$rawValue}' vtfieldname=".
								"'{$fieldName}' vtmodule='$parentModule' style='display:none;'></span>";
							}
						}
					} else {
						$value = '--';
					}
				} elseif ($field->getFieldDataType() == 'owner') {
					if ($fieldName!='assigned_user_id' && false !== strpos($fieldName, '.assigned_user_id')) {
						$value = empty($this->ownerNameListrel[$fieldName][$value]) ? '' : textlength_check($this->ownerNameListrel[$fieldName][$value]);
					} else {
						$value = textlength_check($this->ownerNameList[$fieldName][$value]);
					}
				} elseif ($field->getUIType() == 8) {
					if (!empty($value)) {
						$temp_val = html_entity_decode($value, ENT_QUOTES, $default_charset);
						$value = vt_suppressHTMLTags(implode(',', json_decode($temp_val, true)));
					}
				} elseif (in_array($uitype, array(7,9,90))) {
					$value = "<span align='right'>".textlength_check($value).'</div>';
				} elseif ($field->getUIType() == 55) {
					$value = getTranslatedString($value, $currentModule);
				} elseif ($module == 'Emails' && ($fieldName == 'subject')) {
					$value = '<a href="javascript:;" onClick="ShowEmail(\'' . $recordId . '\');">' . textlength_check($value) . '</a>';
				} else {
					$field_val = trim(vt_suppressHTMLTags(vtlib_purify($value), true));
					$value = textlength_check($value);
					if (substr($value, -3) == '...') {
						$value = '<span title="'.$field_val.'">'.$value.'<span>';
					}
				}
				if ($field->getFieldDataType() != 'reference') {
					$nameFields = $this->queryGenerator->getModuleNameFields($module);
					$nameFieldList = explode(',', $nameFields);
					if (($fieldName == $focus->list_link_field || in_array($fieldName, $nameFieldList)) && $module != 'Emails') {
						$opennewtab = GlobalVariable::getVariable('Application_OpenRecordInNewXOnListView', '', $module);
						if ($opennewtab=='') {
							$value = "<a href='index.php?module=$module&action=DetailView&record=".
								"$recordId' title='".getTranslatedString($module, $module)."'>$value</a>";
						} elseif ($opennewtab=='window') {
							$value = "<a href='#' onclick='window.open(\"index.php?module=$module&action=DetailView&record="
								."$recordId\", \"$module".'_'."$recordId\", cbPopupWindowSettings); return false;' title='"
								.getTranslatedString($module, $module)."'>$value</a>";
						} else {
							$value = "<a href='index.php?module=$module&action=DetailView&record="
								."$recordId' title='".getTranslatedString($module, $module)."' target='_blank'>$value</a>";
						}
					}
					// vtlib customization: For listview javascript triggers
					$value = "$value <span type='vtlib_metainfo' vtrecordid='{$recordId}' vtfieldname=".
						"'{$fieldName}' vtmodule='$module' style='display:none;'></span>";
				}
				$row[] = $value;
			}

			//Added for Actions ie., edit and delete links in listview
			$actionLinkInfo = '';
			if (isPermitted($module, 'EditView', $recordId) == 'yes') {
				$racbr = $wfs->getRACRuleForRecord($currentModule, $recordId);
				if (!$racbr || $racbr->hasListViewPermissionTo('edit')) {
					$edit_link = $this->getListViewEditLink($module, $recordId);
					if (isset($navigationInfo['start']) && $navigationInfo['start'] > 1 && $module != 'Emails') {
						$actionLinkInfo .= "<a href=\"$edit_link&start=".$navigationInfo['start']."\">".getTranslatedString('LNK_EDIT', $module).'</a> ';
					} else {
						$actionLinkInfo .= "<a href=\"$edit_link\">".getTranslatedString('LNK_EDIT', $module).'</a> ';
					}
				}
			}

			if (isPermitted($module, 'Delete', $recordId) == 'yes') {
				$racbr = $wfs->getRACRuleForRecord($currentModule, $recordId);
				if (!$racbr || $racbr->hasListViewPermissionTo('delete')) {
					$del_link = $this->getListViewDeleteLink($module, $recordId);
					if ($actionLinkInfo != '' && $del_link != '') {
						$actionLinkInfo .= ' | ';
					}
					if ($del_link != '') {
						$actionLinkInfo.="<a href='javascript:confirmdelete(\"".addslashes(urlencode($del_link))."\")'>".getTranslatedString('LNK_DELETE', $module).'</a>';
					}
				}
			}

			$customlink_params['RECORD'] = $recordId;
			$linksurls = BusinessActions::getAllByType($tabid, array('LISTVIEWROW'), $customlink_params, null, $recordId);
			if (!empty($linksurls['LISTVIEWROW'])) {
				$linkviewrows = $linksurls['LISTVIEWROW'];
				for ($x =0; $x < count($linkviewrows); $x++) {
					$lvrobj = $linkviewrows[$x];
					$linklabel = $lvrobj->linklabel;
					$linkurl = $lvrobj->linkurl;
					$actionLinkInfo .= ' | ';
					$actionLinkInfo .= "<a href=\"$linkurl\">".getTranslatedString($linklabel, $module).'</a> ';
				}
			}

			// Record Change Notification
			if (method_exists($focus, 'isViewed') && GlobalVariable::getVariable('Application_ListView_Record_Change_Indicator', 1, $module) && !$focus->isViewed($recordId)) {
				$actionLinkInfo .= " | <img src='" . vtiger_imageurl('important1.gif', $theme) . "' border=0>";
			}
			if ($actionLinkInfo != '' && !$skipActions) {
				$row[] = $actionLinkInfo;
			}
			list($row,$unused,$unused2) = cbEventHandler::do_filter('corebos.filter.listview.render', array($row,$this->db->query_result_rowdata($result, $i),$recordId));
			$data[$recordId] = $row;
		}
		if (!empty($totals) && GlobalVariable::getVariable('Application_ListView_Sum_Currency', 1, $module)) {
			$trow = array();
			foreach ($listViewFields as $fieldName) {
				if (isset($totals[$fieldName])) {
					$currencyField = new CurrencyField($totals[$fieldName]);
					$currencyValue = $currencyField->getDisplayValueWithSymbol();
					$trow[] = '<span class="listview_row_total">'.$currencyValue.'</span>';
				} else {
					$trow[] = '';
				}
			}
			$data[-1] = $trow;
		}
		return $data;
	}

	public function getListViewEditLink($module, $recordId, $activityType = '') {
		if ($module == 'Emails') {
			return 'javascript:;" onclick="OpenCompose(\''.$recordId.'\',\'edit\');';
		}
		$url = getBasic_Advance_SearchURL();
		//Appending view name while editing from ListView
		return "index.php?module=$module&action=EditView&record=$recordId&return_module=$module&return_action=index$url&return_viewname=".
			((isset($_SESSION['lvs']) && isset($_SESSION['lvs'][$module])) ? $_SESSION['lvs'][$module]['viewname'] : '');
	}

	public function getListViewDeleteLink($module, $recordId) {
		$viewname = ((isset($_SESSION['lvs']) && isset($_SESSION['lvs'][$module])) ? $_SESSION['lvs'][$module]['viewname'] : '');
		$url = getBasic_Advance_SearchURL();
		//This is added to avoid the del link in Product related list for the following modules
		$link = "index.php?module=$module&action=Delete&record=$recordId&return_module=$module&return_action=index&return_viewname=".$viewname.$url;

		// vtlib customization: override default delete link for custom modules
		$requestModule = isset($_REQUEST['module']) ? vtlib_purify($_REQUEST['module']) : '';
		$requestRecord = isset($_REQUEST['record']) ? vtlib_purify($_REQUEST['record']) : '';
		$requestAction = isset($_REQUEST['action']) ? vtlib_purify($_REQUEST['action']) : '';
		$requestFile = isset($_REQUEST['file']) ? vtlib_purify($_REQUEST['file']) : '';
		$isCustomModule = vtlib_isCustomModule($requestModule);

		if ($isCustomModule && (!in_array($requestAction, array('index','ListView'))
			&& ($requestAction == $requestModule.'Ajax' && !in_array($requestFile, array('index','ListView'))))
		) {
			$link = "index.php?module=$requestModule&action=updateRelations&parentid=$requestRecord&destination_module=$module&idlist=$recordId&mode=delete";
		}
		return $link;
	}

	public function getListViewHeader($focus, $module, $sort_qry = '', $sorder = '', $orderBy = '', $skipActions = false) {
		global $theme, $current_user;

		$arrow='';
		$sorder=trim($sorder);
		$header = array();

		$fields = $this->queryGenerator->getFields();
		$meta = $this->queryGenerator->getMeta($this->queryGenerator->getModule());

		$moduleFields = $meta->getModuleFields();
		$accessibleFieldList = array_keys($moduleFields);
		if ($this->queryGenerator->getReferenceFieldInfoList()) {
			$accessibleFieldList = array_merge($this->queryGenerator->getReferenceFieldNameList(), $accessibleFieldList);
		}
		$listViewFields = array_intersect($fields, $accessibleFieldList);
		$change_sorder = array('ASC'=>'DESC','DESC'=>'ASC');
		$arrow_gif = array('ASC'=>'arrow_down.gif','DESC'=>'arrow_up.gif');
		$default_sort_order = strtoupper(GlobalVariable::getVariable('Application_ListView_Default_Sort_Order', 'ASC', $module));
		foreach ($listViewFields as $fieldName) {
			if (!empty($moduleFields[$fieldName])) {
				$field = $moduleFields[$fieldName];
			} else {
				$field = $this->queryGenerator->getReferenceField($fieldName, false);
				if (is_null($field)) {
					continue;
				}
			}
			$fieldLabel = $field->getFieldLabelKey();
			if (in_array($field->getColumnName(), $focus->sortby_fields)) {
				if ($orderBy == $field->getFieldName() || ($orderBy == 'title' && $field->getFieldName() == 'notes_title')) {
					$temp_sorder = $change_sorder[$sorder];
					$arrow = "&nbsp;<img src ='".vtiger_imageurl($arrow_gif[$sorder], $theme)."' border='0'>";
				} else {
					$temp_sorder = $default_sort_order;
				}
				$label = getTranslatedString($fieldLabel, $module);
				//added to display currency symbol in listview header
				if ($fieldLabel == 'Amount') {
					$label .= ' ('.getTranslatedString('LBL_IN', $module).' '.$current_user->column_fields['currency_symbol'].')';
				}
				if ($field->getUIType() == '9') {
					$label .=' (%)';
				}
				if ($module == 'Users' && $fieldName == 'User Name') {
					$name = "<a href='javascript:;' onClick='getListViewEntries_js(\"".$module.
						'","order_by='.$field->getFieldName().'&sorder='.$temp_sorder.$sort_qry."\");' class='listFormHeaderLinks'>".
						getTranslatedString('LBL_LIST_USER_NAME_ROLE', $module).$arrow.'</a>';
				} else {
					if ($this->isHeaderSortingEnabled()) {
						if (isset($_SESSION['lvs'][$module]['start'])) {
							if (is_array($_SESSION['lvs'][$module]['start'])) {
								$sesStart = reset($_SESSION['lvs'][$module]['start']);
							} else {
								$sesStart = $_SESSION['lvs'][$module]['start'];
							}
						} else {
							$sesStart = '';
						}
						$name = "<a href='javascript:;' onClick='getListViewEntries_js(\"".$module.
							'","foldername=Default&order_by='.$field->getFieldName().'&start='.
							$sesStart.'&sorder='.$temp_sorder.$sort_qry."\");' class='listFormHeaderLinks'>".$label.$arrow.'</a>';
					} else {
						$name = $label;
					}
				}
				$arrow = '';
			} else {
				$name = getTranslatedString($fieldLabel, getTabModuleName($field->getTabId()));
			}
			$header[]=$name;
		}

		//Added for Action - edit and delete link header in listview
		if (!$skipActions && (isPermitted($module, 'EditView', '') == 'yes' || isPermitted($module, 'Delete', '') == 'yes')) {
			$header[] = getTranslatedString('LBL_ACTION', $module);
		}
		return cbEventHandler::do_filter('corebos.filter.listview.header', $header);
	}

	public function getBasicSearchFieldInfoList() {
		$fields = $this->queryGenerator->getFields();
		$meta = $this->queryGenerator->getMeta($this->queryGenerator->getModule());

		$moduleFields = $meta->getModuleFields();
		$accessibleFieldList = array_keys($moduleFields);
		$listViewFields = array_intersect($fields, $accessibleFieldList);
		$basicSearchFieldInfoList = array();
		foreach ($listViewFields as $fieldName) {
			$field = $moduleFields[$fieldName];
			$basicSearchFieldInfoList[$fieldName] = getTranslatedString($field->getFieldLabelKey(), $this->queryGenerator->getModule());
		}
		return $basicSearchFieldInfoList;
	}

	public function getAdvancedSearchOptionString() {
		$module = $this->queryGenerator->getModule();
		$meta = $this->queryGenerator->getMeta($module);

		$moduleFields = $meta->getModuleFields();
		$i =0;
		$OPTION_SET = array();
		foreach ($moduleFields as $fieldName => $field) {
			if ($field->getFieldDataType() == 'reference') {
				$typeOfData = 'V';
			} elseif ($field->getFieldDataType() == 'boolean') {
				$typeOfData = 'C';
			} else {
				$typeOfData = $field->getTypeOfData();
				$typeOfData = explode('~', $typeOfData);
				$typeOfData = $typeOfData[0];
			}
			$label = getTranslatedString($field->getFieldLabelKey(), $module);
			$label = str_replace(array("\n","\r"), '', $label);
			if (empty($label)) {
				$label = $field->getFieldLabelKey();
			}
			$selected = '';
			if ($i++ == 0) {
				$selected = 'selected';
			}

			// place option in array for sorting later
			$blockName = getTranslatedString($field->getBlockName(), $module);

			$fieldLabelEscaped = str_replace(' ', '_', $field->getFieldLabelKey());
			$optionvalue = $field->getTableName().':'.$field->getColumnName().':'.$fieldName.':'.$module.'_'.$fieldLabelEscaped.':'.$typeOfData;

			$OPTION_SET[$blockName][$label] = "<option value=\'$optionvalue\' $selected>$label</option>";
		}
		// sort array on block label
		ksort($OPTION_SET, SORT_STRING);
		$shtml = '';
		foreach ($OPTION_SET as $key => $value) {
			$shtml .= "<optgroup label='$key' class='select' style='border:none'>";
			// sort array on field labels
			ksort($value, SORT_STRING);
			$shtml .= implode('', $value);
		}
		return $shtml;
	}

	public function getAdvancedSearchOptionArray() {
		$module = $this->queryGenerator->getModule();
		$meta = $this->queryGenerator->getMeta($module);

		$moduleFields = $meta->getModuleFields();
		$i =0;
		$OPTION_SET = array();
		foreach ($moduleFields as $fieldName => $field) {
			if ($field->getFieldDataType() == 'reference') {
				$typeOfData = 'V';
			} elseif ($field->getFieldDataType() == 'boolean') {
				$typeOfData = 'C';
			} else {
				$typeOfData = $field->getTypeOfData();
				$typeOfData = explode("~", $typeOfData);
				$typeOfData = $typeOfData[0];
			}
			$label = getTranslatedString($field->getFieldLabelKey(), $module);
			$label = str_replace(array("\n","\r"), '', $label);
			if (empty($label)) {
				$label = $field->getFieldLabelKey();
			}

			$selected = '';
			if ($i++ == 0) {
				$selected = true;
			}

			// place option in array for sorting later
			$blockName = getTranslatedString($field->getBlockName(), $module);

			$fieldLabelEscaped = str_replace(" ", "_", $field->getFieldLabelKey());
			$optionvalue = $field->getTableName().":".$field->getColumnName().":".$fieldName.":".$module."_".$fieldLabelEscaped.":".$typeOfData;

			$OPTION_SET[$blockName][$label] = array('label' => $label, 'value' => $optionvalue, 'selected' => $selected, 'typeofdata' => $typeOfData);
		}
		// sort array on block label
		ksort($OPTION_SET, SORT_STRING);
		$OPTIONS = array();
		foreach ($OPTION_SET as $key => $value) {
			$OPTIONS[$key] = $value;
			ksort($OPTIONS[$key], SORT_STRING);
		}
		return array($module => $OPTIONS);
	}
}
?>
