<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'data/CRMEntity.php';
require_once 'data/Tracker.php';
include_once 'vtlib/Vtiger/Utils/StringTemplate.php';
include_once 'vtlib/Vtiger/LinkData.php';

class BusinessActions extends CRMEntity {
	public $table_name = 'vtiger_businessactions';
	public $table_index = 'businessactionsid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-case-change-status', 'class' => 'slds-icon', 'icon'=>'case_change_status');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_businessactionscf', 'businessactionsid');
	// related_tables variable should define the association (relation) between dependent tables
	// FORMAT: related_tablename => array(related_tablename_column[, base_tablename, base_tablename_column[, related_module]] )
	// Here base_tablename_column should establish relation with related_tablename_column
	// NOTE: If base_tablename and base_tablename_column are not specified, it will default to modules (table_name, related_tablename_column)
	// Uncomment the line below to support custom field columns on related lists
	// var $related_tables = array('vtiger_MODULE_NAME_LOWERCASEcf' => array('MODULE_NAME_LOWERCASEid', 'vtiger_MODULE_NAME_LOWERCASE',
	// 'MODULE_NAME_LOWERCASEid', 'MODULE_NAME_LOWERCASE'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_businessactions', 'vtiger_businessactionscf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_businessactions' => 'businessactionsid',
		'vtiger_businessactionscf' => 'businessactionsid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'businessactions_no' => array('businessactions' => 'businessactions_no'),
		'linklabel' => array('businessactions' => 'linklabel'),
		'linktype' => array('businessactions' => 'elementtype_action'),
		'module_list' => array('businessactions' => 'module_list'),
		'active' => array('businessactions' => 'active'),
		'Assigned To' => array('crmentity' => 'smownerid'),
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'businessactions_no' => 'businessactions_no',
		'linklabel' => 'linklabel',
		'linktype' => 'elementtype_action',
		'module_list' => 'module_list',
		'active' => 'active',
		'Assigned To' => 'assigned_user_id',
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'businessactions_no';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'businessactions_no' => array('businessactions' => 'businessactions_no'),
		'linklabel' => array('businessactions' => 'linklabel'),
		'linktype' => array('businessactions' => 'elementtype_action'),
		'module_list' => array('businessactions' => 'module_list'),
		'active' => array('businessactions' => 'active'),
		'Assigned To' => array('crmentity' => 'smownerid'),
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'businessactions_no' => 'businessactions_no',
		'linklabel' => 'linklabel',
		'linktype' => 'elementtype_action',
		'module_list' => 'module_list',
		'active' => 'active',
		'Assigned To' => 'assigned_user_id',
	);

	// For Popup window record selection
	public $popup_fields = array('businessactions_no');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'businessactions_no';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'businessactions_no';

	// Required Information for enabling Import feature
	public $required_fields = array('businessactions_no' => 1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'businessactions_no';
	public $default_sort_order = 'ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'businessactions_no');

	// Ignore module while selection
	const IGNORE_MODULE = -1;

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
	}

	public static function convertToObject($tabid, $valuemap) {
		$link_obj = new Vtiger_Link();
		$link_obj->tabid = (string) $tabid;
		$link_obj->linkid = $valuemap['businessactionsid'];
		$link_obj->linktype       = $valuemap['elementtype_action'];
		$link_obj->linklabel      = $valuemap['linklabel'];
		$link_obj->linkurl        = decode_html($valuemap['linkurl']);
		$link_obj->linkicon       = decode_html($valuemap['linkicon']);
		$link_obj->sequence       = $valuemap['sequence'];
		$link_obj->status         = (isset($valuemap['status']) ? $valuemap['status'] : false);
		$link_obj->handler_path   = $valuemap['handler_path'];
		$link_obj->handler_class  = $valuemap['handler_class'];
		$link_obj->handler        = $valuemap['handler'];
		$link_obj->onlyonmymodule = $valuemap['onlyonmymodule'];
		return $link_obj;
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param string Module name
	 * @param string Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		if ($event_type == 'module.postinstall') {
			// Handle post installation actions
			$this->setModuleSeqNumber('configure', $modulename, 'bact-', '0000001');
		} elseif ($event_type == 'module.disabled') {
			// Handle actions when this module is disabled.
		} elseif ($event_type == 'module.enabled') {
			// Handle actions when this module is enabled.
		} elseif ($event_type == 'module.preuninstall') {
			// Handle actions when this module is about to be deleted.
		} elseif ($event_type == 'module.preupdate') {
			// Handle actions before this module is updated.
		} elseif ($event_type == 'module.postupdate') {
			// Handle actions after this module is updated.
		}
	}

	/**
	 * Get all the link related to module based on type
	 * @param integer Module ID
	 * @param mixed String or List of types to select
	 * @param array Key-Value pair to use for formating the link url
	 * @param integer User Id
	 * @param integer Record Id
	 */
	public static function getAllByType($tabid, $type = false, $parameters = false, $userid = null, $recordid = null) {
		global $adb, $current_user, $currentModule;

		$accumulator = array();

		$module_sql = '';
		if ($tabid != self::IGNORE_MODULE) {
			$module_name = getTabModuleName($tabid);
			$module_sql = " AND (ba.module_list = '".$module_name."' OR ba.module_list LIKE '".$module_name." %' OR ba.module_list LIKE '% ".$module_name." %' OR ba.module_list LIKE '% ".$module_name."') ";
		}

		$multitype = false;

		if ($userid == null) {
			$userid = $current_user->id;
		}

		if ($recordid == null) {
			$recordid = '';
		}

		$type_sql = '';

		if ($type) {
			// Multiple link type selection
			if (is_array($type)) {
				$multitype = true;
				$type_sql = $adb->convert2Sql(' AND ba.elementtype_action IN ('.Vtiger_Utils::implodestr('?', count($type), ',') .') ', $adb->flatten_array($type));
				if ($tabid == self::IGNORE_MODULE && !empty($currentModule)) {
					$module_sql = " AND ((ba.onlyonmymodule AND (ba.module_list = '".$currentModule."' OR ba.module_list LIKE '".$currentModule." %' OR ba.module_list LIKE '% ".$currentModule." %' OR ba.module_list LIKE '% ".$currentModule."')) OR !ba.onlyonmymodule) ";
				}
			} else {
				$type_sql = $adb->convert2Sql(' AND ba.elementtype_action = ?', array($type));
			}
		}
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('BusinessActions');
		$query = 'SELECT ba.businessactionsid, ba.elementtype_action,ba.linklabel,ba.linkurl,ba.linkicon,ba.sequence,ba.handler_path,ba.handler_class,ba.handler,ba.onlyonmymodule,ba.brmap,ba.mandatory
			FROM vtiger_businessactions as ba INNER JOIN '.$crmEntityTable.' ON vtiger_crmentity.crmid=ba.businessactionsid
			WHERE vtiger_crmentity.deleted=0 AND ba.active=1 '.$module_sql.$type_sql;

		$orderby = ' ORDER BY ba.elementtype_action, ba.sequence';

		$role_condition = "EXISTS(SELECT 1
			FROM vtiger_user2role
			WHERE vtiger_user2role.userid=? AND ba.acrole LIKE CONCAT('%', vtiger_user2role.roleid, '%')
		)";
		$role_condition = $adb->convert2Sql($role_condition, array($userid));

		$user_condition = $adb->convert2sql('vtiger_crmentity.smownerid=?', array($userid));

		require_once 'include/utils/GetUserGroups.php';
		$UserGroups = new GetUserGroups();
		$UserGroups->getAllUserGroups($userid);

		$group_condition = '';
		if (count($UserGroups->user_groups)>0) {
			$groups = implode(',', $UserGroups->user_groups);
			$group_condition = 'OR vtiger_crmentity.smownerid IN ('.$groups.') ';
		}

		$where_ext = 'AND (ba.mandatory=1 OR '.$role_condition.' OR '.$user_condition. ' '.$group_condition.')';
		$sql = $query.$where_ext.$orderby;

		$business_actions = $adb->query($sql);

		while ($row = $adb->fetch_array($business_actions)) {
			$accumulator[] = $row;
		}

		$strtemplate = new Vtiger_StringTemplate();
		if ($parameters) {
			foreach ($parameters as $key => $value) {
				$strtemplate->assign($key, $value);
			}
		}

		$result = array();
		$alreadyLoaded = array();
		if ($multitype) {
			foreach ($type as $t) {
				$result[$t] = array();
				$alreadyLoaded[$t] = array();
			}
		}
		foreach ($accumulator as $row) {
			/** Should the widget be shown */
			$return = cbEventHandler::do_filter('corebos.filter.link.show', array($row, $type, $parameters));
			if (!$return) {
				continue;
			}

			//Get Vtiger_Link object
			$link = self::convertToObject($tabid, $row);

			if (!empty($row['handler_path']) && isInsideApplication($row['handler_path'])) {
				checkFileAccessForInclusion($row['handler_path']);
				require_once $row['handler_path'];
				$linkData = new Vtiger_LinkData($link, $current_user);
				$ignore = call_user_func(array($row['handler_class'], $row['handler']), $linkData);
				if (!$ignore) {
					continue;
				}
			}

			if ($row['brmap'] > 0 && !coreBOS_Rule::evaluate($row['brmap'], $recordid)) {
				continue;
			}

			if ($parameters) {
				$link->linkurl = $strtemplate->merge($link->linkurl);
				$link->linkicon= $strtemplate->merge($link->linkicon);
				if (!empty($parameters['RECORD'])) {
					include_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
					$entityCache = new VTEntityCache($current_user);
					$ct = new VTSimpleTemplate($link->linkurl, true);
					if ($module_name=='Users') {
						if (Users::is_ActiveUserID($parameters['RECORD'])) {
							$link->linkurl = $ct->render($entityCache, vtws_getEntityId('Users').'x'.$parameters['RECORD']);
						} else {
							$link->linkurl = '';
						}
					} else {
						$link->linkurl = $ct->render($entityCache, vtws_getEntityId(getSalesEntityType($parameters['RECORD'])).'x'.$parameters['RECORD']);
					}
				}
			}
			if ($multitype) {
				if (in_array($link->linktype, array('HEADERSCRIPT', 'HEADERCSS', 'HEADERSCRIPT_POPUP', 'HEADERCSS_POPUP', 'FOOTERSCRIPT')) && in_array($link->linkurl, $alreadyLoaded[$link->linktype])) {
					continue;
				}
				$alreadyLoaded[$link->linktype][] = $link->linkurl;
				$result[$link->linktype][] = $link;
			} else {
				if (in_array($link->linktype, array('HEADERSCRIPT', 'HEADERCSS', 'HEADERSCRIPT_POPUP', 'HEADERCSS_POPUP', 'FOOTERSCRIPT')) && in_array($link->linkurl, $alreadyLoaded)) {
					continue;
				}
				$alreadyLoaded[] = $link->linkurl;
				$result[] = $link;
			}
		}

		return $result;
	}

	/**
	 * Add link given module
	 * @param integer Module ID
	 * @param string Link Type (like DETAILVIEW). Useful for grouping based on pages.
	 * @param string Label to display
	 * @param string HREF value or URL to use for the link
	 * @param string ICON to use on the display
	 * @param integer Order or sequence of displaying the link
	 */
	public static function addLink($tabid, $type, $label, $url, $iconpath = '', $sequence = 0, $handlerInfo = null, $onlyonmymodule = false, $brmap = 0) {
		global $adb;
		$module_name = getTabModuleName($tabid);
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('BusinessActions');
		$linkcheck = $adb->pquery(
			'SELECT businessactionsid
				FROM vtiger_businessactions INNER JOIN '.$crmEntityTable.' 
				WHERE vtiger_crmentity.crmid = vtiger_businessactions.businessactionsid
				AND vtiger_crmentity.deleted = 0
				AND module_list = ?
				AND elementtype_action = ?
				AND linkurl = ?
				AND linkicon = ?
				AND linklabel = ?',
			array($module_name, $type, $url, $iconpath, $label)
		);

		if (!$adb->num_rows($linkcheck)) {
			$newBA = new BusinessActions();

			$newBA->column_fields['linktype'] = $type;
			$newBA->column_fields['linklabel'] = $label;
			$newBA->column_fields['linkurl'] = $url;
			$newBA->column_fields['sequence'] = (int) $sequence;
			$newBA->column_fields['module_list'] = $module_name;
			$newBA->column_fields['onlyonmymodule'] = $onlyonmymodule;
			$newBA->column_fields['linkicon'] = $iconpath;
			$newBA->column_fields['active'] = 1;
			$newBA->column_fields['mandatory'] = 1;
			$newBA->column_fields['brmap'] = $brmap;

			if (!empty($handlerInfo)) {
				$newBA->column_fields['handler_path'] = (isset($handlerInfo['path']) ? $handlerInfo['path'] : '');
				$newBA->column_fields['handler_class'] = (isset($handlerInfo['class']) ? $handlerInfo['class'] : '');
				$newBA->column_fields['handler'] = (isset($handlerInfo['method']) ? $handlerInfo['method'] : '');
			}

			$newBA->save('BusinessActions');
		}
	}

	/**
	 * Delete link of the module
	 * @param integer Module ID
	 * @param string Link Type (like DETAILVIEW). Useful for grouping based on pages.
	 * @param string Display label
	 * @param string URL of link to lookup while deleting
	 */
	public static function deleteLink($tabid, $type, $label, $url = false) {
		global $adb;
		$module_name = getTabModuleName($tabid);
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('BusinessActions');
		if ($url) {
			$ba = $adb->pquery(
				'SELECT vtiger_businessactions.businessactionsid
					FROM vtiger_businessactions
					INNER JOIN '.$crmEntityTable.' ON vtiger_businessactions.businessactionsid = vtiger_crmentity.crmid
						AND vtiger_crmentity.deleted = 0
						AND vtiger_businessactions.module_list = ?
						AND vtiger_businessactions.elementtype_action = ?
						AND vtiger_businessactions.linklabel = ?
						AND vtiger_businessactions.linkurl = ?',
				array($module_name, $type, $label, $url)
			);
		} else {
			$ba = $adb->pquery(
				'SELECT vtiger_businessactions.businessactionsid
					FROM vtiger_businessactions
					INNER JOIN '.$crmEntityTable.' ON vtiger_businessactions.businessactionsid = vtiger_crmentity.crmid
						AND vtiger_crmentity.deleted = 0
						AND vtiger_businessactions.module_list = ?
						AND vtiger_businessactions.elementtype_action = ?
						AND vtiger_businessactions.linklabel = ?',
				array($module_name, $type, $label)
			);
		}

		$focus = CRMEntity::getInstance('BusinessActions');
		while ($row = $adb->fetch_array($ba)) {
			$focus->id = $row['businessactionsid'];
			DeleteEntity('BusinessActions', 'BusinessActions', $focus, $row['businessactionsid'], 0);
		}
	}

	/**
	 * Delete all links related to module
	 * @param integer Module ID
	 */
	public static function deleteAll($tabid) {
		global $adb;
		$module_name = getTabModuleName($tabid);
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('BusinessActions');
		$ba = $adb->pquery(
			'SELECT vtiger_businessactions.businessactionsid
				FROM vtiger_businessactions
				INNER JOIN '.$crmEntityTable.' ON vtiger_businessactions.businessactionsid = vtiger_crmentity.crmid
					AND vtiger_crmentity.deleted = 0
					AND vtiger_businessactions.module_list = ?',
			array($module_name)
		);

		$countba = $adb->num_rows($ba);

		for ($i = 0; $i < $countba; $i++) {
			$recordid = $adb->query_result($ba, $i, 'businessactionsid');
			$focus = CRMEntity::getInstance('BusinessActions');
			DeleteEntity('BusinessActions', 'BusinessActions', $focus, $recordid, 0);
		}
	}

	public static function updateLink($tabId, $businessActionId, $linkInfo = array()) {
		if ($linkInfo && is_array($linkInfo)) {
			include_once 'include/Webservices/Revise.php';
			global $adb, $current_user;

			$module_name = getTabModuleName($tabId);
			$linkInfo['id'] = vtws_getEntityId('BusinessActions') . 'x' . $businessActionId;

			if (!empty($linkInfo['elementtype_action'])) {
				$linkInfo['linktype'] = $linkInfo['elementtype_action'];
			}

			if (!empty($linkInfo['module_list'])) {
				$linkInfo['module_list'] = $module_name;
			}

			if (isset($linkInfo['status'])) {
				$linkInfo['active'] = $linkInfo['status'];
			}
			$crmEntityTable = CRMEntity::getcrmEntityTableAlias('BusinessActions');
			$businessAction = $adb->pquery(
				'SELECT 1 
				FROM vtiger_businessactions
				INNER JOIN '.$crmEntityTable.' ON vtiger_businessactions.businessactionsid = vtiger_crmentity.crmid
					AND vtiger_crmentity.deleted = 0
					AND vtiger_businessactions.module_list = ?
					AND vtiger_businessactions.businessactionsid = ?',
				array($module_name, $businessActionId)
			);

			if ($adb->num_rows($businessAction)) {
				vtws_revise($linkInfo, $current_user);
			}
		}
	}

	public static function getModuleLinkStatusInfo($actiontype, $actionlabel) {
		global $adb;
		$allEntities = array();
		$allModules = array();
		$entityQuery = "SELECT tabid,name FROM vtiger_tab WHERE isentitytype=1 and name NOT IN ('Rss','Recyclebin')";
		$result = $adb->pquery($entityQuery, array());
		while ($result && $row = $adb->fetch_array($result)) {
			$allEntities[$row['tabid']] = getTranslatedString($row['name'], $row['name']);
			$allModules[$row['tabid']] = $row['name'];
		}
		asort($allEntities);
		$mlist = array();
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('BusinessActions');
		foreach ($allEntities as $tabid => $mname) {
			$checkres = $adb->pquery(
				'SELECT 1
					FROM vtiger_businessactions
					INNER JOIN '.$crmEntityTable.' ON crmid = businessactionsid
					WHERE vtiger_crmentity.deleted = 0
						AND (module_list = ? OR module_list LIKE ? OR module_list LIKE ? OR module_list LIKE ?)
						AND elementtype_action=? AND linklabel=?',
				array($allModules[$tabid], $allModules[$tabid].' %', '% '.$allModules[$tabid].' %', '% '.$allModules[$tabid], $actiontype, $actionlabel)
			);
			$mlist[$tabid] = array(
				'name' => $mname,
				'active' => $adb->num_rows($checkres),
			);
		}
		return $mlist;
	}

	public static function getModuleLinkStatusInfoSortedFlat($actiontype, $actionlabel) {
		$act = BusinessActions::getModuleLinkStatusInfo($actiontype, $actionlabel);
		$infomodules = array();
		$i = 0;
		foreach ($act as $tabid => $modinfo) {
			$infomodules[$i]['tabid'] = $tabid;
			$infomodules[$i]['visible'] = $modinfo['active'];
			$infomodules[$i]['name'] = $modinfo['name'];
			$i++;
		}
		usort($infomodules, function ($a, $b) {
			return (strtolower(getTranslatedString($a['name'], $a['name'])) < strtolower(getTranslatedString($b['name'], $b['name']))) ? -1 : 1;
		});
		return $infomodules;
	}
}
