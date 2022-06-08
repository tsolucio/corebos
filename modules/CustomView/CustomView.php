<?php
/* +********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ****************************************************************************** */
global $app_strings, $mod_strings, $theme;
$theme_path = 'themes/' . $theme . '/';
$image_path = $theme_path . 'images/';
require_once 'include/utils/utils.php';
require_once 'include/Webservices/Utils.php';

class CustomView extends CRMEntity {

	public $module_list = array();
	public $customviewmodule;
	public $list_fields;
	public $list_fields_name;
	public $setdefaultviewid;
	public $escapemodule;
	public $mandatoryvalues;
	public $showvalues;
	public $data_type;
	// Information as defined for this instance in the database table.
	protected $_status = false;
	protected $_userid = false;
	protected $meta;
	protected $moduleMetaInfo;
	public static $conditionMapping = array(
		'e' => 'equal to',
		'n' => 'does not equal',
		'c' => 'contains',
		'k' => 'does not contain',
		's' => 'starts with',
		'dnsw' => 'does not start with',
		'ew' => 'ends with',
		'dnew' => 'does not end with',
		'l' => 'less than',
		'g' => 'greater than',
		'm' => 'less than or equal to',
		'h' => 'greater than or equal to',
		'b' => 'before',
		'a' => 'after',
		'y' => 'is empty',
		'ny' => 'is not empty',
		'bw' => 'between',
	);

	/** This function sets
	 * the currentuser id to the class variable smownerid,
	 * modulename to the class variable customviewmodule
	 * @param string module Name (optional)
	 * @return void
	 */
	public function __construct($module = '') {
		global $current_user;
		$this->customviewmodule = $module;
		$this->escapemodule[] = $module . '_';
		$this->escapemodule[] = '_';
		$this->smownerid = $current_user->id;
		$this->moduleMetaInfo = array();
		if ($module != '' && $module != 'com_vtiger_workflow') {
			$this->meta = $this->getMeta($module, $current_user);
		}
	}

	/**
	 *
	 * @param String:ModuleName $module
	 * @return EntityMeta
	 */
	public function getMeta($module, $user) {
		if (empty($this->moduleMetaInfo[$module])) {
			$handler = vtws_getModuleHandlerFromName($module, $user);
			$this->moduleMetaInfo[$module] = $handler->getMeta();
		}
		return $this->moduleMetaInfo[$module];
	}

	/** To get the custom view ID of the specified module
	 * @param string module Name
	 * @return integer custom view ID
	 */
	public function getViewId($module) {
		global $adb, $current_user;
		$now_action = isset($_REQUEST['action']) ? vtlib_purify($_REQUEST['action']) : '';
		if (empty($_REQUEST['viewname'])) {
			if (isset($_SESSION['lvs'][$module]['viewname']) && $_SESSION['lvs'][$module]['viewname'] != '') {
				$viewid = $_SESSION['lvs'][$module]['viewname'];
			} elseif ($this->setdefaultviewid != '') {
				$viewid = $this->setdefaultviewid;
			} else {
				$defcv_result = $adb->pquery(
					'select default_cvid from vtiger_user_module_preferences where userid = ? and tabid =?',
					array($current_user->id, getTabid($module))
				);
				if ($adb->num_rows($defcv_result) > 0) {
					$viewid = $adb->query_result($defcv_result, 0, 'default_cvid');
				} else {
					$query = 'select cvid from vtiger_customview where setdefault=1 and entitytype=?';
					$cvresult = $adb->pquery($query, array($module));
					if ($adb->num_rows($cvresult) > 0) {
						$viewid = $adb->query_result($cvresult, 0, 'cvid');
					} else {
						$viewid = '';
					}
				}
			}

			if ($viewid == '' || $viewid == 0 || $this->isPermittedCustomView($viewid, $now_action, $module) != 'yes') {
				$query = "select cvid from vtiger_customview where viewname='All' and entitytype=?";
				$cvresult = $adb->pquery($query, array($module));
				$viewid = $adb->query_result($cvresult, 0, 'cvid');
			}
		} else {
			$viewname = vtlib_purify($_REQUEST['viewname']);
			if (!is_numeric($viewname)) {
				if (strtolower($viewname) == 'all' || $viewname == 0) {
					$viewid = $this->getViewIdByName('All', $module);
				} else {
					$viewid = $this->getViewIdByName($viewname, $module);
				}
			} else {
				$viewid = $viewname;
			}
			if ($this->isPermittedCustomView($viewid, $now_action, $this->customviewmodule) != 'yes') {
				$viewid = 0;
			}
		}
		coreBOS_Session::set('lvs^'.$module.'^viewname', $viewid);
		return $viewid;
	}

	public function getViewIdByName($viewname, $module) {
		global $adb;
		if (isset($viewname)) {
			$cvresult = $adb->pquery('select cvid from vtiger_customview where viewname=? and entitytype=?', array($viewname, $module));
			return $adb->query_result($cvresult, 0, 'cvid');
		} else {
			return 0;
		}
	}

	/** to get the details of a custom view
	 * @param integer custom view ID
	 * @return array in the following format
	 * $customviewlist = array('viewname'=>value,
	 *                         'setdefault'=>defaultchk,
	 *                         'setmetrics'=>setmetricschk)
	 */
	public function getCustomViewByCvid($cvid) {
		global $adb, $current_user;
		$tabid = getTabid($this->customviewmodule);
		$userprivs = $current_user->getPrivileges();

		$ssql = 'select vtiger_customview.*
			from vtiger_customview inner join vtiger_tab on vtiger_tab.name = vtiger_customview.entitytype where vtiger_customview.cvid=?';
		$sparams = array($cvid);

		if (!$userprivs->isAdmin()) {
			$ssql .= ' and (vtiger_customview.status=0 or vtiger_customview.userid = ? or vtiger_customview.status = 3 or ';
			$ssql .= " vtiger_customview.userid in (select vtiger_user2role.userid
				from vtiger_user2role
				inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid
				where vtiger_role.parentrole like '" . $userprivs->getParentRoleSequence() . "::%'))";
			$sparams[] = $current_user->id;
		}
		$result = $adb->pquery($ssql, $sparams);

		$usercv_result = $adb->pquery('select default_cvid from vtiger_user_module_preferences where userid = ? and tabid = ?', array($current_user->id, $tabid));
		$def_cvid = $adb->query_result($usercv_result, 0, 'default_cvid');

		$customviewlist = array();
		while ($cvrow = $adb->fetch_array($result)) {
			$customviewlist['viewname'] = $cvrow['viewname'];
			if ((isset($def_cvid) || $def_cvid != '') && $def_cvid == $cvid) {
				$customviewlist['setdefault'] = 1;
			} else {
				$customviewlist['setdefault'] = $cvrow['setdefault'];
			}
			$customviewlist['setmetrics'] = $cvrow['setmetrics'];
			$customviewlist['userid'] = $cvrow['userid'];
			$customviewlist['status'] = $cvrow['status'];
		}
		return $customviewlist;
	}

	/** to get the custom view combo for the class variable customviewmodule
	 * @param integer view ID
	 * @return string custom view combo
	 */
	public function getCustomViewCombo($viewid = '', $markselected = true) {
		global $adb, $current_user, $app_strings;
		$tabid = getTabid($this->customviewmodule);
		$userprivs = $current_user->getPrivileges();

		$shtml_user = '';
		$shtml_pending = '';
		$shtml_public = '';
		$shtml_others = '';

		$selected = 'selected';
		if (!$markselected) {
			$selected = '';
		}

		$ssql = 'select vtiger_customview.*, vtiger_users.ename
			from vtiger_customview
			inner join vtiger_tab on vtiger_tab.name = vtiger_customview.entitytype
			left join vtiger_users on vtiger_customview.userid = vtiger_users.id
			where vtiger_tab.tabid=?';
		$sparams = array($tabid);

		if (!$userprivs->isAdmin()) {
			$ssql .= ' and (vtiger_customview.status=0 or vtiger_customview.userid = ? or vtiger_customview.status = 3 or ';
			$ssql .= " vtiger_customview.userid in(select vtiger_user2role.userid
				from vtiger_user2role
				inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid
				where vtiger_role.parentrole like '" . $userprivs->getParentRoleSequence() . "::%'))";
			$sparams[] = $current_user->id;
		}
		$ssql .= ' ORDER BY viewname';
		$cuserroles = getRoleAndSubordinateUserIds($current_user->column_fields['roleid']);
		$result = $adb->pquery($ssql, $sparams);
		while ($cvrow = $adb->fetch_array($result)) {
			if ($cvrow['viewname'] == 'All') {
				$cvrow['viewname'] = $app_strings['COMBO_ALL'];
			} else { /** Should the filter shown?  */
				$return = cbEventHandler::do_filter('corebos.filter.listview.filter.show', $cvrow);
				if (!$return) {
					continue;
				}
			}

			$option = '';
			$viewname = $cvrow['viewname'];
			if ($cvrow['status'] == CV_STATUS_DEFAULT || $cvrow['userid'] == $current_user->id) {
				$disp_viewname = $viewname;
			} else {
				$userName = getFullNameFromArray('Users', $cvrow);
				$disp_viewname = $viewname . ' [' . $userName . '] ';
			}

			if ($cvrow['setdefault'] == 1 && $viewid == '') {
				$option = "<option $selected value=\"" . $cvrow['cvid'] . "\">" . $disp_viewname . "</option>";
				$this->setdefaultviewid = $cvrow['cvid'];
			} elseif ($cvrow['cvid'] == $viewid) {
				$option = "<option $selected value=\"" . $cvrow['cvid'] . "\">" . $disp_viewname . "</option>";
				$this->setdefaultviewid = $cvrow['cvid'];
			} else {
				$option = "<option value=\"" . $cvrow['cvid'] . "\">" . $disp_viewname . "</option>";
			}

			// Add the option to combo box at appropriate section
			if ($option != '') {
				if ($cvrow['status'] == CV_STATUS_DEFAULT || $cvrow['userid'] == $current_user->id) {
					$shtml_user .= $option;
				} elseif ($cvrow['status'] == CV_STATUS_PUBLIC) {
					if ($shtml_public == '') {
						$shtml_public = '<option disabled>--- ' . $app_strings['LBL_PUBLIC'] . ' ---</option>';
					}
					$shtml_public .= $option;
				} elseif ($cvrow['status'] == CV_STATUS_PENDING) {
					if (in_array($cvrow['userid'], $cuserroles) || $userprivs->isAdmin()) {
						if ($shtml_pending == '') {
							$shtml_pending = '<option disabled>--- ' . $app_strings['LBL_PENDING'] . ' ---</option>';
						}
						$shtml_pending .= $option;
					}
				} else {
					if ($shtml_others == '') {
						$shtml_others = '<option disabled>--- ' . $app_strings['LBL_OTHERS'] . ' ---</option>';
					}
					$shtml_others .= $option;
				}
			}
		}
		$shtml = $shtml_user;
		$shtml .= $shtml_pending;
		$shtml = $shtml . $shtml_public . $shtml_others;
		return $shtml;
	}

	/** to get the getColumnsListbyBlock for the given module and Block
	 * @param string module
	 * @param integer block
	 * @param boolean mark mandatory
	 * @param boolean return field properties as associative array (true) or colon separated (false)
	 * @return array in the format
	 * $columnlist = Array ($fieldlabel =>'$fieldtablename:$fieldcolname:$fieldname:$module_$fieldlabel1:$fieldtypeofdata',
	  $fieldlabel1 =>'$fieldtablename1:$fieldcolname1:$fieldname1:$module_$fieldlabel11:$fieldtypeofdata1',
	  |
	  $fieldlabeln =>'$fieldtablenamen:$fieldcolnamen:$fieldnamen:$module_$fieldlabel1n:$fieldtypeofdatan')
	 */
	public function getColumnsListbyBlock($module, $block, $markMandatory = true, $assocArray = false) {
		global $adb, $current_user;
		$module_columnlist = null;
		$block_ids = explode(',', $block);
		$tabid = getTabid($module);
		$userprivs = $current_user->getPrivileges();
		if (empty($this->meta)) {
			$this->meta = $this->getMeta($module, $current_user);
		}
		$display_type = ' vtiger_field.displaytype in (1,2,3,4)';

		if ($userprivs->hasGlobalReadPermission()) {
			$sql = 'select * from vtiger_field ';
			$sql.= ' where vtiger_field.tabid=? and vtiger_field.block in (' . generateQuestionMarks($block_ids) . ')';
			$sql.= ' and vtiger_field.presence in (0,2) and ' . $display_type;
			$sql.= ' order by sequence';
			$params = array($tabid, $block_ids);
		} else {
			$profileList = getCurrentUserProfileList();
			$uniqueFieldsRestriction = 'vtiger_field.fieldid IN (select min(vtiger_field.fieldid) from vtiger_field
				where vtiger_field.tabid=? GROUP BY vtiger_field.columnname)';
			$sql = 'select distinct vtiger_field.*
				from vtiger_field
				inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid
				inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid';
			$sql.= " where $uniqueFieldsRestriction and vtiger_field.block in (" . generateQuestionMarks($block_ids) . ') and';
			$sql.= "$display_type and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0 and vtiger_field.presence in (0,2)";

			$params = array($tabid, $block_ids);

			if (count($profileList) > 0) {
				$sql.= '  and vtiger_profile2field.profileid in (' . generateQuestionMarks($profileList) . ')';
				$params[] = $profileList;
			}
			$sql.= ' order by sequence';
		}
		$result = $adb->pquery($sql, $params);
		$noofrows = $adb->num_rows($result);
		$moduleFieldList = $this->meta->getModuleFields();
		if ($noofrows > 0) {
			$module_columnlist = array();
		}
		for ($i = 0; $i < $noofrows; $i++) {
			$fieldtablename = $adb->query_result($result, $i, 'tablename');
			$fieldcolname = $adb->query_result($result, $i, 'columnname');
			if ($fieldtablename=='vtiger_user2role') {
				continue;
			}
			$fieldname = $adb->query_result($result, $i, 'fieldname');
			$fieldtype = $adb->query_result($result, $i, 'typeofdata');
			$fieldtype = explode('~', $fieldtype);
			$fieldtypeofdata = $fieldtype[0];
			$fieldlabel = $adb->query_result($result, $i, 'fieldlabel');
			if (!empty($moduleFieldList[$fieldname]) && $moduleFieldList[$fieldname]->getFieldDataType() == 'reference') {
				$fieldtypeofdata = 'V';
			} else {
				//Here we Changing the displaytype of the field. So that its criteria will be
				//displayed Correctly in Custom view Advance Filter.
				$fieldtypeofdata = ChangeTypeOfData_Filter($fieldtablename, $fieldcolname, $fieldtypeofdata);
			}
			if ($fieldlabel == 'Related To') {
				$fieldlabel = 'Related to';
			}
			if ($fieldlabel == 'Start Date & Time') {
				$fieldlabel = 'Start Date';
			}
			$fieldlabel1 = str_replace(' ', '_', $fieldlabel);
			$optionvalue = $fieldtablename . ':' . $fieldcolname . ':' . $fieldname . ':' . $module . '_' . $fieldlabel1 . ':' . $fieldtypeofdata;
			//added to escape attachments fields in customview as we have multiple attachments
			$fieldlabel = getTranslatedString($fieldlabel, $module); //added to support i18n issue
			if ($module != 'HelpDesk' || $fieldname != 'filename') {
				$module_columnlist[$optionvalue] = $fieldlabel;
			}
			if ($markMandatory && $fieldtype[1] == 'M') {
				$this->mandatoryvalues[] = "'" . $optionvalue . "'";
				$this->showvalues[] = $fieldlabel;
				$this->data_type[$fieldlabel] = $fieldtype[1];
			}
		}
		if ($assocArray) {
			$mod_columnlist_array = array();
			foreach ($module_columnlist as $optval => $fldname) {
				list($table, $column, $fldname, $mod_fldlbl, $tod) = explode(':', $optval);
				$fldlbl = str_replace('_', ' ', str_replace($module . '_', '', $mod_fldlbl));
				$mod_columnlist_array[$fldname] = array(
					'label' => getTranslatedString($fldlbl, $module),
					'value' => $optval,
					'selected' => '',
					'typeofdata' => $tod
				);
			}
			$module_columnlist = $mod_columnlist_array;
		}
		return $module_columnlist;
	}

	/** to get the getModuleColumnsList for the given module
	 * @param string module
	 * @param boolean return field properties as associative array (true) or colon separated (false)
	 * @return array in the following format
	 * $ret_module_list =
	  Array ('module' =>
	  Array('BlockLabel1' =>
	  Array('$fieldtablename:$fieldcolname:$fieldname:$module_$fieldlabel1:$fieldtypeofdata'=>$fieldlabel,
	  Array('$fieldtablename1:$fieldcolname1:$fieldname1:$module_$fieldlabel11:$fieldtypeofdata1'=>$fieldlabel1,
	  Array('BlockLabel2' =>
	  Array('$fieldtablename:$fieldcolname:$fieldname:$module_$fieldlabel1:$fieldtypeofdata'=>$fieldlabel,
	  Array('$fieldtablename1:$fieldcolname1:$fieldname1:$module_$fieldlabel11:$fieldtypeofdata1'=>$fieldlabel1,
	  |
	  Array('BlockLabeln' =>
	  Array('$fieldtablename:$fieldcolname:$fieldname:$module_$fieldlabel1:$fieldtypeofdata'=>$fieldlabel,
	  Array('$fieldtablename1:$fieldcolname1:$fieldname1:$module_$fieldlabel11:$fieldtypeofdata1'=>$fieldlabel1,
	 */
	public function getModuleColumnsList($module, $assocArray = false) {
		global $current_user;
		$this->getCustomViewModuleInfo($module);
		foreach ($this->module_list[$module] as $key => $value) {
			$columnlist = $this->getColumnsListbyBlock($module, $value, true, $assocArray);
			if (isset($columnlist)) {
				$ret_module_list[$module][$key] = $columnlist;
			}
		}
		$handler = vtws_getModuleHandlerFromName($module, $current_user);
		$meta_data = $handler->getMeta();
		$reffields = $meta_data->getReferenceFieldDetails();
		foreach ($reffields as $mods) {
			foreach ($mods as $mod) {
				if (!vtlib_isEntityModule($mod)) {
					continue; // reference to a module without fields
				}
				if (isset($ret_module_list[$mod])) {
					continue;  // we already have this one
				}
				$this->getCustomViewModuleInfo($mod);
				foreach ($this->module_list[$mod] as $key => $value) {
					$columnlist = $this->getColumnsListbyBlock($mod, $value, false, $assocArray);
					if (isset($columnlist)) {
						$ret_module_list[$mod][$key] = $columnlist;
					}
				}
			}
		}
		// assigned user fields
		$mod = 'Users';
		$this->getCustomViewModuleInfo($mod);
		$userFilterFields = array(
			'vtiger_users:user_name:user_name:Users_User_Name:V',
			'vtiger_users:first_name:first_name:Users_First_Name:V',
			'vtiger_users:last_name:last_name:Users_Last_Name:V',
			'vtiger_users:title:title:Users_Title:V',
			'vtiger_users:department:department:Users_Department:V',
		);
		foreach ($this->module_list[$mod] as $key => $value) {
			$columnlist = $this->getColumnsListbyBlock($mod, $value, false, $assocArray);
			if (isset($columnlist)) {
				foreach ($columnlist as $col => $label) {
					if (!in_array($col, $userFilterFields)) {
						unset($columnlist[$col]);
					}
				}
				if (!empty($columnlist)) {
					$ret_module_list[$mod][$key] = $columnlist;
				}
			}
		}
		return $ret_module_list;
	}

	/** to get the getColumnsListByCvid for the given customview
	 * @param integer custom view ID
	 * @return array in the following format
	 * $columnlist = Array( $columnindex => $columnname,
	 * 		$columnindex1 => $columnname1,
	 * 			|
	 * 		$columnindexn => $columnnamen)
	 */
	public function getColumnsListByCvid($cvid) {
		global $adb;
		$sSQL = 'select vtiger_cvcolumnlist.*
			from vtiger_cvcolumnlist
			inner join vtiger_customview on vtiger_customview.cvid = vtiger_cvcolumnlist.cvid
			where vtiger_customview.cvid =? order by vtiger_cvcolumnlist.columnindex';
		$result = $adb->pquery($sSQL, array($cvid));
		$columnlist = array();
		while ($columnrow = $adb->fetch_array($result)) {
			$columnlist[$columnrow['columnindex']] = $columnrow['columnname'];
		}
		return $columnlist;
	}

	/** to get the standard filter fields or the given module
	 * @param string module
	 * @return array in the following format
	 * $stdcriteria_list = Array( $tablename:$columnname:$fieldname:$module_$fieldlabel => $fieldlabel,
	 * 		$tablename1:$columnname1:$fieldname1:$module_$fieldlabel1 => $fieldlabel1,
	 * 			|
	 * 		$tablenamen:$columnnamen:$fieldnamen:$module_$fieldlabeln => $fieldlabeln)
	 */
	public function getStdCriteriaByModule($module) {
		global $adb, $current_user;
		$tabid = getTabid($module);

		$userprivs = $current_user->getPrivileges();

		$this->getCustomViewModuleInfo($module);
		foreach ($this->module_list[$module] as $blockid) {
			$blockids[] = $blockid;
		}

		if ($userprivs->hasGlobalReadPermission()) {
			$sql = 'select * from vtiger_field inner join vtiger_tab on vtiger_tab.tabid = vtiger_field.tabid ';
			$sql.= ' where vtiger_field.tabid=? and vtiger_field.block in (' . generateQuestionMarks($blockids) . ') and vtiger_field.uitype in (5,6,23,70,50)';
			$sql.= ' and vtiger_field.presence in (0,2) order by vtiger_field.sequence';
			$params = array($tabid, $blockids);
		} else {
			$profileList = getCurrentUserProfileList();
			$sql = 'select *
				from vtiger_field
				inner join vtiger_tab on vtiger_tab.tabid = vtiger_field.tabid
				inner join  vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid
				inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid';
			$sql.= ' where vtiger_field.tabid=? and vtiger_field.block in (' . generateQuestionMarks($blockids) . ') and vtiger_field.uitype in (5,6,23,70,50)';
			$sql.= ' and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0 and vtiger_field.presence in (0,2)';

			$params = array($tabid, $blockids);

			if (count($profileList) > 0) {
				$sql.= ' and vtiger_profile2field.profileid in (' . generateQuestionMarks($profileList) . ')';
				$params[] = $profileList;
			}

			$sql.= ' order by vtiger_field.sequence';
		}

		$result = $adb->pquery($sql, $params);
		$stdcriteria_list = array();
		while ($criteriatyperow = $adb->fetch_array($result)) {
			$fieldtablename = $criteriatyperow['tablename'];
			$fieldcolname = $criteriatyperow['columnname'];
			$fieldlabel = $criteriatyperow['fieldlabel'];
			$fieldname = $criteriatyperow['fieldname'];
			$fieldlabel1 = str_replace(' ', '_', $fieldlabel);
			$optionvalue = $fieldtablename . ':' . $fieldcolname . ':' . $fieldname . ':' . $module . '_' . $fieldlabel1;
			$stdcriteria_list[$optionvalue] = $fieldlabel;
		}

		return $stdcriteria_list;
	}

	public static function getFilterFieldDefinition($field, $module) {
		$fielddef = '';
		$mod = Vtiger_Module::getInstance($module);
		if ($mod) {
			$fld = Vtiger_Field::getInstance($field, $mod);
			if ($fld) {
				$fieldtablename = $fld->table;
				$fieldcolname = $fld->column;
				$fieldlabel = $fld->label;
				$fieldname = $fld->name;
				$fieldlabel1 = str_replace(' ', '_', $fieldlabel);
				$fielddef = $fieldtablename . ':' . $fieldcolname . ':' . $fieldname . ':' . $module . '_' . $fieldlabel1 . ':' . $fld->typeofdata;
			}
		}
		return $fielddef;
	}

	/** to get the standard filter criteria
	 * @param string selcriteria (optional)
	 * @return array in the following format
	 * $filter = Array( 0 => array('value'=>$filterkey,'text'=>$mod_strings[$filterkey],'selected'=>$selected)
	 * 		     1 => array('value'=>$filterkey1,'text'=>$mod_strings[$filterkey1],'selected'=>$selected)
	 * 		                             		|
	 * 		     n => array('value'=>$filterkeyn,'text'=>$mod_strings[$filterkeyn],'selected'=>$selected)
	 */
	public function getStdFilterCriteria($selcriteria = '') {
		global $mod_strings;
		$filter = array();

		$stdfilter = array('custom' => '' . $mod_strings['Custom'] . '',
			'prevfy' => '' . $mod_strings['Previous FY'] . '',
			'thisfy' => '' . $mod_strings['Current FY'] . '',
			'nextfy' => '' . $mod_strings['Next FY'] . '',
			'prevfq' => '' . $mod_strings['Previous FQ'] . '',
			'thisfq' => '' . $mod_strings['Current FQ'] . '',
			'nextfq' => '' . $mod_strings['Next FQ'] . '',
			'yesterday' => '' . $mod_strings['Yesterday'] . '',
			'today' => '' . $mod_strings['Today'] . '',
			'tomorrow' => '' . $mod_strings['Tomorrow'] . '',
			'lastweek' => '' . $mod_strings['Last Week'] . '',
			'thisweek' => '' . $mod_strings['Current Week'] . '',
			'nextweek' => '' . $mod_strings['Next Week'] . '',
			'priorToToday' => '' . $mod_strings['PriorToToday'] . '',
			'lastmonth' => '' . $mod_strings['Last Month'] . '',
			'thismonth' => '' . $mod_strings['Current Month'] . '',
			'nextmonth' => '' . $mod_strings['Next Month'] . '',
			'last7days' => '' . $mod_strings['Last 7 Days'] . '',
			'last30days' => '' . $mod_strings['Last 30 Days'] . '',
			'last60days' => '' . $mod_strings['Last 60 Days'] . '',
			'last90days' => '' . $mod_strings['Last 90 Days'] . '',
			'last120days' => '' . $mod_strings['Last 120 Days'] . '',
			'next7days' => '' . getTranslatedString('Next 7 Days', 'Reports') . '',
			'next30days' => '' . $mod_strings['Next 30 Days'] . '',
			'next60days' => '' . $mod_strings['Next 60 Days'] . '',
			'next90days' => '' . $mod_strings['Next 90 Days'] . '',
			'next120days' => '' . $mod_strings['Next 120 Days'] . '',
		);

		foreach ($stdfilter as $FilterKey => $FilterValue) {
			if ($FilterKey == $selcriteria) {
				$shtml['value'] = $FilterKey;
				$shtml['text'] = $FilterValue;
				$shtml['selected'] = 'selected';
			} else {
				$shtml['value'] = $FilterKey;
				$shtml['text'] = $FilterValue;
				$shtml['selected'] = '';
			}
			$filter[] = $shtml;
		}
		return $filter;
	}

	/**
	 * This function will return the script to set the start data and end date for the standard selection criteria
	 * @return string $jsStr
	 */
	public function getCriteriaJS() {
		return getCriteriaJS('CustomView');
	}

	/** to get the standard filter for the given customview Id
	 * @param integer custom view ID
	 * @return array in the following format
	 * $stdfilterlist = array('columnname' =>  $tablename:$columnname:$fieldname:$module_$fieldlabel,'stdfilter'=>$stdfilter,'startdate'=>$startdate,'enddate'=>$enddate)
	 */
	public function getStdFilterByCvid($cvid) {
		global $adb;

		$sSQL = 'select vtiger_cvstdfilter.* from vtiger_cvstdfilter inner join vtiger_customview on vtiger_customview.cvid = vtiger_cvstdfilter.cvid';
		$sSQL .= ' where vtiger_cvstdfilter.cvid=?';

		$result = $adb->pquery($sSQL, array($cvid));
		$stdfilterrow = $adb->fetch_array($result);

		$stdfilterlist = array();
		if (empty($stdfilterrow)) {
			return $stdfilterlist;
		}
		$stdfilterlist['columnname'] = $stdfilterrow['columnname'];
		$stdfilterlist['stdfilter'] = $stdfilterrow['stdfilter'];

		if ($stdfilterrow['stdfilter'] == 'custom' || $stdfilterrow['stdfilter'] == '') {
			if ($stdfilterrow['startdate'] != '0000-00-00' && $stdfilterrow['startdate'] != '') {
				$startDateTime = new DateTimeField($stdfilterrow['startdate'] . ' ' . date('H:i:s'));
				$stdfilterlist['startdate'] = $startDateTime->getDisplayDate();
			} else {
				$stdfilterlist['startdate'] = '';
			}
			if ($stdfilterrow['enddate'] != '0000-00-00' && $stdfilterrow['enddate'] != '') {
				$endDateTime = new DateTimeField($stdfilterrow['enddate'] . ' ' . date('H:i:s'));
				$stdfilterlist['enddate'] = $endDateTime->getDisplayDate();
			} else {
				$stdfilterlist['enddate'] = '';
			}
		} else { //if it is not custom get the date according to the selected duration
			$datefilter = $this->getDateforStdFilterBytype($stdfilterrow['stdfilter']);
			$startDateTime = new DateTimeField($datefilter[0] . ' ' . date('H:i:s'));
			$stdfilterlist['startdate'] = $startDateTime->getDisplayDate();
			$endDateTime = new DateTimeField($datefilter[1] . ' ' . date('H:i:s'));
			$stdfilterlist['enddate'] = $endDateTime->getDisplayDate();
		}
		return $stdfilterlist;
	}

	/** to get the Advanced filter for the given customview Id
	 * @param integer custom view ID
	 * @return array
	 */
	public function getAdvFilterByCvid($cvid) {
		global $adb, $default_charset, $current_user;

		$advft_criteria = array();

		$sql = 'SELECT * FROM vtiger_cvadvfilter_grouping WHERE cvid = ? ORDER BY groupid';
		$groupsresult = $adb->pquery($sql, array($cvid));

		$i = 1;
		$j = 0;
		while ($relcriteriagroup = $adb->fetch_array($groupsresult)) {
			$groupId = $relcriteriagroup['groupid'];
			$groupCondition = $relcriteriagroup['group_condition'];

			$ssql = 'select vtiger_cvadvfilter.*
				from vtiger_customview
				inner join vtiger_cvadvfilter on vtiger_cvadvfilter.cvid = vtiger_customview.cvid
				left join vtiger_cvadvfilter_grouping on vtiger_cvadvfilter.cvid = vtiger_cvadvfilter_grouping.cvid
					and vtiger_cvadvfilter.groupid = vtiger_cvadvfilter_grouping.groupid
				where vtiger_customview.cvid = ? AND vtiger_cvadvfilter.groupid = ? order by vtiger_cvadvfilter.columnindex';

			$result = $adb->pquery($ssql, array($cvid, $groupId));
			$noOfColumns = $adb->num_rows($result);
			if ($noOfColumns <= 0) {
				continue;
			}

			while ($relcriteriarow = $adb->fetch_array($result)) {
				$criteria = array();
				$full_name = array();
				$criteria['columnname'] = html_entity_decode($relcriteriarow['columnname'], ENT_QUOTES, $default_charset);
				$criteria['comparator'] = $relcriteriarow['comparator'];
				$advfilterval = html_entity_decode($relcriteriarow['value'], ENT_QUOTES, $default_charset);
				$col = explode(':', $relcriteriarow['columnname']);
				$temp_val = explode(',', $relcriteriarow['value']);
				if ($col[4] == 'D' || ($col[4] == 'T' && $col[1] != 'time_start' && $col[1] != 'time_end') || ($col[4] == 'DT')) {
					$val = array();
					for ($x = 0; $x < count($temp_val); $x++) {
						if ($col[4] == 'D') {
							$date = new DateTimeField(trim($temp_val[$x]));
							$val[$x] = $date->getDisplayDate();
						} elseif ($col[4] == 'DT') {
							$date = new DateTimeField(trim($temp_val[$x]));
							$val[$x] = $date->getDisplayDateTimeValue();
						} else {
							$date = new DateTimeField(trim($temp_val[$x]));
							$val[$x] = $date->getDisplayTime();
						}
					}
					$advfilterval = implode(',', $val);
				}
				$uitype = getUItypeByFieldName($this->customviewmodule, $col[2]);
				if (($col[1]=='smownerid' || $col[1]=='smcreatorid' || $col[1]=='modifiedby' || $uitype==101)
					&& ($advfilterval=='current_user' || $advfilterval=='current_userandgroup') && (empty($_REQUEST['action']) || $_REQUEST['action']!='CustomView') && empty($_REQUEST['record'])
				) {
					if ($advfilterval=='current_user') {
						$advfilterval = trim($current_user->first_name.' '.$current_user->last_name);
						$advfilterval = decode_html($advfilterval);
					} else {
						global $adb;
						$advfilterval = array();
						$result = $adb->pquery('select distinct roleid from vtiger_users2group inner join vtiger_group2role on vtiger_users2group.groupid = vtiger_group2role.groupid where userid=?', array($current_user->id));
						$num_rows=$adb->num_rows($result);
						for ($i=0; $i<$num_rows; $i++) {
							$user_roleid=$adb->query_result($result, $i, 'roleid');
						}

						$result = $adb->pquery('select distinct ename from vtiger_users inner join vtiger_user2role on vtiger_users.id = vtiger_user2role.userid where roleid=?', array($user_roleid));
						$num_rows=$adb->num_rows($result);
						for ($i=0; $i<$num_rows; $i++) {
							$advfilterval[] = decode_html($adb->query_result($result, $i, 'ename'));
						}
					}
				}
				$criteria['value'] = $advfilterval;
				$criteria['column_condition'] = $relcriteriarow['column_condition'];

				$advft_criteria[$i]['columns'][$j] = $criteria;
				$advft_criteria[$i]['condition'] = $groupCondition;
				$j++;
			}
			if (!empty($advft_criteria[$i]['columns'][$j - 1]['column_condition'])) {
				$advft_criteria[$i]['columns'][$j - 1]['column_condition'] = '';
			}
			$i++;
		}
		// Clear the condition (and/or) for last group, if any.
		if (!empty($advft_criteria[$i - 1]['condition'])) {
			$advft_criteria[$i - 1]['condition'] = '';
		}
		return $advft_criteria;
	}

	/**
	 * Cache information to perform re-lookups
	 *
	 * @var array
	 */
	protected $_fieldby_tblcol_cache = array();

	/**
	 * Function to check if field is present based on
	 *
	 * @param string $columnname
	 * @param string $tablename
	 */
	public function isFieldPresent_ByColumnTable($columnname, $tablename) {
		global $adb;

		if (!isset($this->_fieldby_tblcol_cache[$tablename])) {
			$query = 'SELECT columnname FROM vtiger_field WHERE tablename = ? and presence in (0,2)';

			$result = $adb->pquery($query, array($tablename));
			$numrows = $adb->num_rows($result);

			if ($numrows) {
				$this->_fieldby_tblcol_cache[$tablename] = array();
				for ($index = 0; $index < $numrows; ++$index) {
					$this->_fieldby_tblcol_cache[$tablename][] = $adb->query_result($result, $index, 'columnname');
				}
			}
		}
		// If still the field was not found (might be disabled or deleted?)
		if (!isset($this->_fieldby_tblcol_cache[$tablename])) {
			return false;
		}
		return in_array($columnname, $this->_fieldby_tblcol_cache[$tablename]);
	}

	/** to get the customview Columnlist Query for the given customview Id
	 * @param integer custmo view ID
	 * @return string $getCvColumnList
	 * This function will return the columns for the given customfield in comma seperated values in the format
	 *                     $tablename.$columnname,$tablename1.$columnname1, ------ $tablenamen.$columnnamen
	 */
	public function getCvColumnListSQL($cvid) {
		$columnslist = $this->getColumnsListByCvid($cvid);
		if (isset($columnslist)) {
			foreach ($columnslist as $value) {
				$tablefield = array();
				if ($value != '') {
					$list = explode(':', $value);
					//Added For getting status for Activities -Jaguar
					if ($this->customviewmodule == 'cbCalendar' && $list[0] == 'vtiger_cntactivityrel') {
						$sqllist_column = 'ctorel.' . $list[1];
					} else {
						$sqllist_column = $list[0] . '.' . $list[1];
					}
					//Added for assigned to sorting
					if ($list[1] == 'smownerid') {
						$sqllist_column = "case when (vtiger_users.user_name not like '') then vtiger_users.ename else vtiger_groups.groupname end as user_name";
					}
					if ($list[0] == 'vtiger_contactdetails' && $list[1] == 'lastname') {
						$sqllist_column = 'vtiger_contactdetails.lastname,vtiger_contactdetails.firstname';
					}
					$sqllist[] = $sqllist_column;

					$tablefield[$list[0]] = $list[1];

					//Changed as the replace of module name may replace the string if the fieldname has module name in it
					$fieldinfo = explode('_', $list[3], 2);
					$fieldlabel = $fieldinfo[1];
					$fieldlabel = str_replace('_', ' ', $fieldlabel);

					if ($this->isFieldPresent_ByColumnTable($list[1], $list[0])) {
						$this->list_fields[$fieldlabel] = $tablefield;
						$this->list_fields_name[$fieldlabel] = $list[2];
					}
				}
			}
			$returnsql = implode(',', array_unique($sqllist));
		}
		return $returnsql;
	}

	/** to get the customview stdFilter Query for the given customview Id
	 * @param integer custom view ID
	 * @return string $stdfiltersql
	 * This function will return the standard filter criteria for the given customfield
	 */
	public function getCVStdFilterSQL($cvid, $webserviceQL = false) {
		global $adb;

		$stdfiltersql = '';
		$stdfilterlist = array();

		$sSQL = 'select vtiger_cvstdfilter.* from vtiger_cvstdfilter inner join vtiger_customview on vtiger_customview.cvid = vtiger_cvstdfilter.cvid';
		$sSQL .= ' where vtiger_cvstdfilter.cvid=?';
		$result = $adb->pquery($sSQL, array($cvid));
		if (!$result || $adb->num_rows($result)==0) {
			return '';
		}
		$stdfilterrow = $adb->fetch_array($result);

		$stdfilterlist = array();
		$stdfilterlist['columnname'] = $stdfilterrow['columnname'];
		$stdfilterlist['stdfilter'] = $stdfilterrow['stdfilter'];

		if ($stdfilterrow['stdfilter'] == 'custom' || $stdfilterrow['stdfilter'] == '') {
			if ($stdfilterrow['startdate'] != '0000-00-00' && $stdfilterrow['startdate'] != '') {
				$stdfilterlist['startdate'] = $stdfilterrow['startdate'];
			}
			if ($stdfilterrow['enddate'] != '0000-00-00' && $stdfilterrow['enddate'] != '') {
				$stdfilterlist['enddate'] = $stdfilterrow['enddate'];
			}
		} else { //if it is not custom get the date according to the selected duration
			$datefilter = $this->getDateforStdFilterBytype($stdfilterrow['stdfilter']);
			$stdfilterlist['startdate'] = $datefilter[0];
			$stdfilterlist['enddate'] = $datefilter[1];
		}

		if (isset($stdfilterlist)) {
			$startDateTime = $endDateTime = '';
			$filtercolumn = $stdfilterlist['columnname'];
			if (!empty($stdfilterlist['startdate'])) {
				$startDateTime = new DateTimeField($stdfilterlist['startdate'] . ' ' . date('H:i:s'));
				$userStartDate = $startDateTime->getDisplayDate();
				$userStartDateTime = new DateTimeField($userStartDate . ' 00:00:00');
				$startDateTime = $userStartDateTime->getDBInsertDateTimeValue();
			}
			if (!empty($stdfilterlist['enddate'])) {
				$endDateTime = new DateTimeField($stdfilterlist['enddate'] . ' ' . date('H:i:s'));
				$userEndDate = $endDateTime->getDisplayDate();
				$userEndDateTime = new DateTimeField($userEndDate . ' 23:59:00');
				$endDateTime = $userEndDateTime->getDBInsertDateTimeValue();
			}
			if ($startDateTime != '' && $endDateTime != '') {
				$columns = explode(':', $filtercolumn);
				if ($columns[1] == 'birthday') {
					$tableColumnSql = 'DATE_FORMAT(' . $columns[0] . '.' . $columns[1] . ", '%m%d')";
					$startDateTime = "DATE_FORMAT('$startDateTime', '%m%d')";
					$endDateTime = "DATE_FORMAT('$endDateTime', '%m%d')";
					$stdfiltersql = $tableColumnSql . ' BETWEEN ' . $startDateTime . ' and ' . $endDateTime;
				} else {
					$tableColumnSql = $columns[0] . '.' . $columns[1];
					if ($webserviceQL) {
						$stdfiltersql = $columns[2] . " >= '" . $startDateTime . "' and ".$columns[2]." <= '" . $endDateTime . "'";
					} else {
						$stdfiltersql = $tableColumnSql . " BETWEEN '" . $startDateTime . "' and '" . $endDateTime . "'";
					}
				}
			}
		}
		return $stdfiltersql;
	}

	/** Get the customview AdvancedFilter Query for the given customview Id
	 * @param integer custom view ID
	 * @return string advanced filter SQL
	 */
	public function getCVAdvFilterSQL($cvid, $webserviceQL = false) {
		$advfilter = $this->getAdvFilterByCvid($cvid);

		$advcvsql = '';

		foreach ($advfilter as $groupid => $groupinfo) {
			$groupcolumns = $groupinfo['columns'];
			$groupcondition = $groupinfo['condition'];
			$advfiltergroupsql = '';
			$index = 1;
			foreach ($groupcolumns as $columninfo) {
				$columnname = $columninfo['columnname'];
				$comparator = $columninfo['comparator'];
				$value = $columninfo['value'];
				$columncondition = $columninfo['column_condition'];

				$columns = explode(':', $columnname);
				$datatype = (isset($columns[4])) ? $columns[4] : '';

				if ($columnname != '' && $comparator != '') {
					$valuearray = explode(',', trim($value));
					$advfiltersql = '';
					if (isset($valuearray) && count($valuearray) > 1 && $comparator != 'bw') {
						$advorsql = array();
						for ($n = 0; $n < count($valuearray); $n++) {
							$advorsql[] = $this->getRealValues($columns[0], $columns[1], $comparator, trim($valuearray[$n]), $datatype, $webserviceQL);
						}
						//If negative logic filter ('not equal to', 'does not contain') is used, 'and' condition should be applied instead of 'or'
						if ($comparator == 'n' || $comparator == 'k') {
							$advorsqls = implode(' and ', $advorsql);
						} else {
							$advorsqls = implode(' or ', $advorsql);
						}
						$advfiltersql = ' (' . $advorsqls . ') ';
					} elseif ($comparator == 'bw' && count($valuearray) == 2) {
						$advfiltersql = '(' . $columns[0] . '.' . $columns[1] . " between '" .
							getValidDBInsertDateTimeValue(trim($valuearray[0])) . "' and '" . getValidDBInsertDateTimeValue(trim($valuearray[1])) . "')";
					} else {
						if ($this->customviewmodule == 'Documents' && $columns[1] == 'folderid') {
							$advfiltersql = 'vtiger_attachmentsfolder.foldername' . $this->getAdvComparator($comparator, trim($value), $datatype);
						} elseif ($this->customviewmodule == 'Assets') {
							if ($columns[1] == 'account') {
								$advfiltersql = 'vtiger_account.accountname' . $this->getAdvComparator($comparator, trim($value), $datatype);
							}
							if ($columns[1] == 'product') {
								$advfiltersql = 'vtiger_products.productname' . $this->getAdvComparator($comparator, trim($value), $datatype);
							}
							if ($columns[1] == 'invoiceid') {
								$advfiltersql = 'vtiger_invoice.subject' . $this->getAdvComparator($comparator, trim($value), $datatype);
							}
						} else {
							$advfiltersql = $this->getRealValues($columns[0], $columns[1], $comparator, trim($value), $datatype, $webserviceQL);
						}
					}

					$advfiltergroupsql .= $advfiltersql;
					if ($columncondition != null && $columncondition != '' && count($groupcolumns) > $index) {
						$advfiltergroupsql .= ' ' . $columncondition . ' ';
					}
				}
				$index++;
			}

			if (trim($advfiltergroupsql) != '') {
				$advfiltergroupsql = "( $advfiltergroupsql ) ";
				if ($groupcondition != null && $groupcondition != '' && $advfilter > $groupid) {
					$advfiltergroupsql .= ' ' . $groupcondition . ' ';
				}

				$advcvsql .= $advfiltergroupsql;
			}
		}
		if (trim($advcvsql) != '' && !$webserviceQL) {
			$advcvsql = '(' . $advcvsql . ')';
		}
		return $advcvsql;
	}

	/** get the customview stdFilter Extended VQL Query conditions
	 * @param integer $cvid custom view ID
	 * @return string standard filter criteria for the given customfield
	 */
	public function getCVStdFilterEVQL($cvid) {
		$conds = $this->getStdFilterByCvid($cvid);
		if (empty($conds)) {
			return '';
		}
		$fspec = explode(':', $conds['columnname']);
		return json_encode(array(
			'fieldname' => $fspec[2],
			'operation' => 'between',
			'value' => $conds['startdate'].','.$conds['enddate'],
			'valuetype' => 'rawtext',
			'joincondition' => 'and',
			'groupid' => rand(),
		));
	}

	/** get the customview AdvancedFilter Extended VQL Query conditions
	 * @param integer $cvid custom view ID
	 * @return string standard filter criteria for the given customfield
	 */
	public function getCVAdvFilterEVQL($cvid) {
		$conds = $this->getAdvFilterByCvid($cvid);
		if (empty($conds)) {
			return '';
		}
		$evql = array();
		$groupjoin = '';
		foreach ($conds as $group => $cols) {
			$grp = $group.rand();
			foreach ($cols['columns'] as $column) {
				$fspec = explode(':', $column['columnname']);
				$evql[] = array(
					'fieldname' => $fspec[2],
					'operation' => $this->getOperatorFromComparator($column['comparator']),
					'value' => $column['value'],
					'valuetype' => 'rawtext',
					'joincondition' => empty($column['column_condition']) ? $cols['condition'] : $column['column_condition'],
					'groupid' => $grp,
					'groupjoin' => $groupjoin,
				);
			}
			$groupjoin = $cols['condition'];
		}
		return json_encode($evql);
	}

	/** get workflow operator from a query generator comparator
	 * @param string $comparator query generator comparator
	 * @return string workflow operator for the given comparator
	 */
	public function getOperatorFromComparator($comparator) {
		return self::$conditionMapping[$comparator];
	}

	/** to get the realvalues for the given value
	 * @param string $tablename
	 * @param string $fieldname
	 * @param string $comparator
	 * @param string $value
	 * @return string in the following format: $tablename.$fieldname comparator
	 */
	public function getRealValues($tablename, $fieldname, $comparator, $value, $datatype, $webserviceQL = false) {
		return getAdvancedSearchValue($tablename, $fieldname, $comparator, $value, $datatype, $webserviceQL);
	}

	/** to get the comparator value for the given comparator and value
	 * @param string $comparator
	 * @param string $value
	 * @return string in the format $comparator $value
	 */
	public function getAdvComparator($comparator, $value, $datatype = '') {
		return getAdvancedSearchComparator($comparator, $value, $datatype);
	}

	/** to get the date value for the given type
	 * @param string
	 * @return array in the following format: $datevalue = array(0=>$startdate, 1=>$enddate)
	 */
	public function getDateforStdFilterBytype($type) {
		return getDateforStdFilterBytype($type);
	}

	/** to get the customview query for the given customview
	 * @param integer custom view id
	 * @param string List View Query
	 * @param string Module Name
	 * @return string $query
	 */
	public function getModifiedCvListQuery($viewid, $listquery, $module) {
		$mod = CRMEntity::getInstance($module);
		$query = '';
		if ($viewid != '' && $listquery != '') {
			$query = 'select ' . $this->getCvColumnListSQL($viewid) . ' ,'.$mod->crmentityTable.'.crmid ';
			$listviewquery = substr($listquery, strpos($listquery, 'FROM'), strlen($listquery));
			if ($module == 'Emails') {
				$query.= ", vtiger_activity.activityid, vtiger_activity.activitytype as type, vtiger_activity.priority,
					case when (vtiger_activity.status not like '') then vtiger_activity.status else vtiger_activity.eventstatus end as status,
					vtiger_contactdetails.contactid " . $listviewquery;
			} elseif ($module == 'Documents') {
				$query.= ' ,vtiger_notes.* ' . $listviewquery;
			} elseif ($module == 'Products') {
				$query.= ' ,vtiger_products.* ' . $listviewquery;
			} elseif ($module == 'Potentials' || $module == 'Contacts') {
				$query.= ' ,vtiger_account.accountid ' . $listviewquery;
			} elseif ($module == 'Invoice' || $module == 'SalesOrder' || $module == 'Quotes') {
				$query.= ' ,vtiger_contactdetails.contactid,vtiger_account.accountid ' . $listviewquery;
			} elseif ($module == 'PurchaseOrder') {
				$query.= ' ,vtiger_contactdetails.contactid ' . $listviewquery;
			} else {
				$query.= $listviewquery;
			}
			$stdfiltersql = $this->getCVStdFilterSQL($viewid);
			$advfiltersql = $this->getCVAdvFilterSQL($viewid);
			if (isset($stdfiltersql) && $stdfiltersql != '') {
				$query .= ' and ' . $stdfiltersql;
			}
			if (isset($advfiltersql) && $advfiltersql != '') {
				$query .= ' and ' . $advfiltersql;
			}
		}
		return $query;
	}

	/** to get the Key Metrics for the home page query for the given customview  to find the no of records
	 * @param integer custom view id
	 * @param string List View Query
	 * @param string Module Name
	 * @return string $query
	 */
	public function getMetricsCvListQuery($viewid, $listquery, $module) {
		if ($viewid != '' && $listquery != '') {
			$listviewquery = substr($listquery, strpos($listquery, 'FROM'), strlen($listquery));

			$query = 'select count(*) AS count ' . $listviewquery;

			$stdfiltersql = $this->getCVStdFilterSQL($viewid);
			$advfiltersql = $this->getCVAdvFilterSQL($viewid);
			if (isset($stdfiltersql) && $stdfiltersql != '') {
				$query .= ' and ' . $stdfiltersql;
			}
			if (isset($advfiltersql) && $advfiltersql != '') {
				$query .= ' and ' . $advfiltersql;
			}
		}
		return $query;
	}

	/** This function sets the block information for the given module to the class variable module_list
	 * @return array
	 */
	public function getCustomViewModuleInfo($module, $allDisplayTypes = false) {
		global $adb, $current_language;
		$current_mod_strings = return_specified_module_language($current_language, $module);
		$block_info = array();
		$modules_list = explode(',', $module);

		// Tabid mapped to the list of block labels to be skipped for that tab.
		$showUserAdvancedBlock = GlobalVariable::getVariable('Webservice_showUserAdvancedBlock', 0);
		if ($showUserAdvancedBlock) {
			$userNoShowBlocks = array('LBL_USER_IMAGE_INFORMATION');
		} else {
			$userNoShowBlocks = array('LBL_USER_IMAGE_INFORMATION','LBL_USER_ADV_OPTIONS');
		}
		$skipBlocksList = array(
			getTabid('HelpDesk') => array('LBL_COMMENTS'),
			getTabid('Faq') => array('LBL_COMMENT_INFORMATION'),
			getTabid('Users') => $userNoShowBlocks,
		);
		foreach (getInventoryModules() as $invmod) {
			$skipBlocksList[getTabid($invmod)] = array('LBL_RELATED_PRODUCTS');
		}
		$displayTypeCondition = $allDisplayTypes ? '' : 'displaytype!=3 and ';
		$Sql = 'select distinct block,vtiger_field.tabid,name,blocklabel
			from vtiger_field
			inner join vtiger_blocks on vtiger_blocks.blockid=vtiger_field.block
			inner join vtiger_tab on vtiger_tab.tabid=vtiger_field.tabid
			where '.$displayTypeCondition.'vtiger_tab.name in (' . generateQuestionMarks($modules_list) . ') and vtiger_field.presence in (0,2) order by block';
		$result = $adb->pquery($Sql, $modules_list);

		$pre_block_label = '';
		while ($block_result = $adb->fetch_array($result)) {
			$block_label = $block_result['blocklabel'];
			$tabid = $block_result['tabid'];
			// Skip certain blocks of certain modules
			if (array_key_exists($tabid, $skipBlocksList) && in_array($block_label, $skipBlocksList[$tabid])) {
				continue;
			}

			if (trim($block_label) == '') {
				$block_info[$pre_block_label] = $block_info[$pre_block_label] . ',' . $block_result['block'];
			} else {
				$lan_block_label = isset($current_mod_strings[$block_label])?$current_mod_strings[$block_label]:$block_label;
				if (isset($block_info[$lan_block_label]) && $block_info[$lan_block_label] != '') {
					$block_info[$lan_block_label] = $block_info[$lan_block_label] . ',' . $block_result['block'];
				} else {
					$block_info[$lan_block_label] = $block_result['block'];
				}
			}
			$pre_block_label = $lan_block_label;
		}
		$this->module_list[$module] = $block_info;
		return $this->module_list;
	}

	/**
	 * Get the userid, status information of this custom view.
	 *
	 * @param integer $viewid
	 * @return array
	 */
	private function getStatusAndUserid($viewid) {
		global $adb;
		if ($this->_status === false || $this->_userid === false) {
			$query = 'SELECT status, userid FROM vtiger_customview WHERE cvid=?';
			$result = $adb->pquery($query, array($viewid));
			if ($result && $adb->num_rows($result)) {
				$this->_status = $adb->query_result($result, 0, 'status');
				$this->_userid = $adb->query_result($result, 0, 'userid');
			} else {
				return false;
			}
		}
		return array('status' => $this->_status, 'userid' => $this->_userid);
	}

	//Function to check if the current user is able to see the customView
	public function isPermittedCustomView($record_id, $action, $module) {
		global $log, $current_user;
		$log->debug("> isPermittedCustomView $record_id,$action,$module");

		$permission = 'yes';
		$permit_all = isset($_REQUEST['permitall']) ? vtlib_purify($_REQUEST['permitall']) : 'false';

		if ($record_id != '') {
			$status_userid_info = $this->getStatusAndUserid($record_id);

			if ($status_userid_info) {
				$status = $status_userid_info['status'];
				$userid = $status_userid_info['userid'];

				if ($status == CV_STATUS_DEFAULT) {
					$log->debug('status=0');
					if ($action == 'ListView' || $action == $module . 'Ajax' || $action == 'index' || $action == 'DetailView' || $action == 'Export' || ($permit_all === 'true')) {
						$permission = 'yes';
					} else {
						$permission = 'no';
					}
				} elseif (is_admin($current_user)) {
					$permission = 'yes';
				} elseif ($action != 'ChangeStatus') {
					if ($userid == $current_user->id) {
						$log->debug($userid.'='.$current_user->id);
						$permission = 'yes';
					} elseif ($status == CV_STATUS_PUBLIC) {
						$log->debug('status=3');
						if ($action == 'ListView' || $action == $module . 'Ajax' || $action == 'index' || $action == 'DetailView' || $action == 'Export') {
							$permission = 'yes';
						} else {
							$user_array = getRoleAndSubordinateUserIds($current_user->column_fields['roleid']);
							if (in_array($userid, $user_array)) {
								$permission = 'yes';
							} else {
								$permission = 'no';
							}
						}
					} elseif ($status == CV_STATUS_PRIVATE || $status == CV_STATUS_PENDING) {
						$log->debug('status=1 or 2');
						if ($userid == $current_user->id) {
							$permission = 'yes';
						} else {
							$log->debug('status=1 or status=2 and action equals ListView or '.$module.'Ajax or index');
							$user_array = getRoleAndSubordinateUserIds($current_user->column_fields['roleid']);
							if (count($user_array) > 0) {
								if (in_array($current_user->id, $user_array)) {
									$permission = 'yes';
								} else {
									$permission = 'no';
								}
							} else {
								$permission = 'no';
							}
						}
					} else {
						$permission = 'yes';
					}
				} else {
					$log->debug('else condition');
					$permission = 'no';
				}
			} else {
				$log->debug('count=0');
				$permission = 'no';
			}
		}
		$log->debug('< isPermittedCustomView '.$permission);
		return $permission;
	}

	public function isPermittedChangeStatus($status, $viewid = 0) {
		global $current_user, $log, $current_language;
		$custom_strings = return_module_language($current_language, 'CustomView');
		$log->debug('> isPermittedChangeStatus '.$status);
		$changed_status = $status_label = '';
		$status_details = array('Status' => CV_STATUS_DEFAULT, 'ChangedStatus' => $changed_status, 'Label' => $status_label);
		if ($viewid>0) {
			$cuserroles = getSubordinateUsersList();
			$status_userid_info = $this->getStatusAndUserid($viewid);
		}
		if (is_admin($current_user) || ($viewid>0 && in_array($status_userid_info['userid'], $cuserroles))) {
			if ($status == CV_STATUS_PENDING) {
				$changed_status = CV_STATUS_PUBLIC;
				$status_label = $custom_strings['LBL_STATUS_PUBLIC_APPROVE'];
			} elseif ($status == CV_STATUS_PUBLIC) {
				$changed_status = CV_STATUS_PENDING;
				$status_label = $custom_strings['LBL_STATUS_PUBLIC_DENY'];
			}
			$status_details = array('Status' => $status, 'ChangedStatus' => $changed_status, 'Label' => $status_label);
		}
		$log->debug('< isPermittedChangeStatus');
		return $status_details;
	}
}
?>
