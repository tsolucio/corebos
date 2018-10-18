<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/database/PearDatabase.php';
require_once 'data/CRMEntity.php';
require_once 'modules/Reports/ReportUtils.php';
require_once 'modules/Reports/ReportRun.php';
global $app_strings,$mod_strings, $modules, $blocks, $adv_filter_options;
global $log, $report_modules, $related_modules, $old_related_modules;

$adv_filter_options = array(
	'e'=>'equals',
	'n'=>'not equal to',
	's'=>'starts with',
	'ew'=>'ends with',
	'c'=>'contains',
	'k'=>'does not contain',
	'l'=>'less than',
	'g'=>'greater than',
	'm'=>'less or equal',
	'h'=>'greater or equal',
	'bw'=>'between',
	'a'=>'after',
	'b'=>'before',
);

//$report_modules = Array('Faq','Rss','Portal','Recyclebin','Emails','Reports','Dashboard','Home','Activities');

$old_related_modules = array(
	'Accounts'=>array('Potentials','Contacts','Products','Quotes','Invoice','SalesOrder'),
	'Contacts'=>array('Accounts','Potentials','Quotes','PurchaseOrder','Invoice'),
	'Potentials'=>array('Accounts','Contacts','Quotes'),
	'Calendar'=>array('Leads','Accounts','Contacts','Potentials'),
	'Products'=>array('Accounts','Contacts'),
	'Quotes'=>array('Accounts','Contacts','Potentials'),
	'PurchaseOrder'=>array('Contacts'),
	'Invoice'=>array('Accounts','Contacts'),
	'SalesOrder'=>array('Accounts','Contacts','Potentials','Quotes'),
	'Timecontrol'=>array('Leads','Accounts','Contacts','Vendors','Campaigns','Potentials','Quotes','PurchaseOrder','SalesOrder','Invoice','HelpDesk', 'Project', 'ProjectMilestone', 'ProjectTask', 'Assets', 'ServiceContracts','Products','Services'),
);

$related_modules =array();

class Reports extends CRMEntity {
	/**
	 * This class has the informations for Reports. It inherits class CRMEntity and has the variables
	 * required to generate, save, restore reports and also the required functions for the same
	 */

	public $srptfldridjs;

	public $column_fields = array();

	public $sort_fields = array();
	public $sort_values = array();

	public $id;
	public $mode;
	public $mcount;

	public $startdate;
	public $enddate;

	public $ascdescorder;

	public $stdselectedfilter;
	public $stdselectedcolumn;

	public $primodule;
	public $secmodule;
	public $columnssummary;
	public $is_editable;
	public $reporttype;
	public $cbreporttype;
	public $reportname;
	public $reportdescription;
	public $folderid;
	public $module_blocks;

	public $pri_module_columnslist;
	public $sec_module_columnslist;

	public $advft_criteria;
	public $adv_rel_fields = array();

	public $module_list = array();

	/** Function to set primodule,secmodule,reporttype,reportname,reportdescription,folderid for given vtiger_reportid
	 *  This function accepts the vtiger_reportid as argument
	 *  It sets primodule,secmodule,reporttype,reportname,reportdescription,folderid for the given vtiger_reportid
	 */
	public function __construct($reportid = '') {
		global $adb, $current_user, $app_strings;
		$current_user_parent_role_seq='';
		require 'user_privileges/user_privileges_'.$current_user->id.'.php';
		$this->initListOfModules();
		if ($reportid != "") {
			// Lookup information in cache first
			$cachedInfo = VTCacheUtils::lookupReport_Info($current_user->id, $reportid);
			$subordinate_users = VTCacheUtils::lookupReport_SubordinateUsers($reportid);

			if ($cachedInfo === false) {
				$ssql = 'select vtiger_reportmodules.*,vtiger_report.*
					from vtiger_report
					inner join vtiger_reportmodules on vtiger_report.reportid = vtiger_reportmodules.reportmodulesid
					where vtiger_report.reportid = ?';
				$params = array($reportid);

				require_once 'include/utils/GetUserGroups.php';
				$userGroups = new GetUserGroups();
				$userGroups->getAllUserGroups($current_user->id);
				$user_groups = $userGroups->user_groups;
				$user_group_query = '';
				if (!empty($user_groups) && $is_admin==false) {
					$user_group_query = " (shareid IN (".generateQuestionMarks($user_groups).") AND setype='groups') OR";
					$params[] = $user_groups;
				}

				$non_admin_query = " vtiger_report.reportid IN (SELECT reportid from vtiger_reportsharing WHERE $user_group_query (shareid=? AND setype='users'))";
				if ($is_admin==false) {
					$ssql .= ' and ( ('.$non_admin_query.") or vtiger_report.sharingtype='Public' or vtiger_report.owner = ?
						or vtiger_report.owner in (select vtiger_user2role.userid
							from vtiger_user2role
							inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid
							inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid
							where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%'))";
					$params[] = $current_user->id;
					$params[] = $current_user->id;
				}

				$query = $adb->pquery(
					"select userid
					from vtiger_user2role
					inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid
					inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid
					where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%'",
					array()
				);
				$subordinate_users = array();
				for ($i=0; $i<$adb->num_rows($query); $i++) {
					$subordinate_users[] = $adb->query_result($query, $i, 'userid');
				}

				// Update subordinate user information for re-use
				VTCacheUtils::updateReport_SubordinateUsers($reportid, $subordinate_users);

				$result = $adb->pquery($ssql, $params);
				if ($result && $adb->num_rows($result)) {
					$reportmodulesrow = $adb->fetch_array($result);

					// Update information in cache now
					VTCacheUtils::updateReport_Info(
						$current_user->id,
						$reportid,
						$reportmodulesrow['primarymodule'],
						$reportmodulesrow['secondarymodules'],
						$reportmodulesrow['reporttype'],
						$reportmodulesrow['reportname'],
						$reportmodulesrow['description'],
						$reportmodulesrow['folderid'],
						$reportmodulesrow['owner'],
						$reportmodulesrow['cbreporttype']
					);
				}

				// Re-look at cache to maintain code-consistency below
				$cachedInfo = VTCacheUtils::lookupReport_Info($current_user->id, $reportid);
			}

			if ($cachedInfo) {
				$this->primodule = $cachedInfo['primarymodule'];
				$this->secmodule = $cachedInfo['secondarymodules'];
				$this->reporttype = $cachedInfo['reporttype'];
				$this->cbreporttype = $cachedInfo['cbreporttype'];
				$this->reportname = decode_html($cachedInfo['reportname']);
				$this->reportdescription = decode_html($cachedInfo['description']);
				$this->folderid = $cachedInfo['folderid'];
				if ($is_admin==true || in_array($cachedInfo['owner'], $subordinate_users) || $cachedInfo['owner']==$current_user->id) {
					$this->is_editable = 'true';
				} else {
					$this->is_editable = 'false';
				}
			} elseif ($_REQUEST['module'] != 'Home') {
				if (empty($_REQUEST['mode']) || $_REQUEST['mode'] != 'ajax') {
					include 'modules/Vtiger/header.php';
				}
				require_once 'Smarty_setup.php';
				$smarty = new vtigerCRM_Smarty();
				$smarty->assign('APP', $app_strings);
				$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
				exit;
			}
		}
	}

	// Initializes the module list for listing columns for report creation.
	public function initListOfModules() {
		global $adb, $old_related_modules;

		$restricted_modules = array('Events');
		$restricted_blocks = array('LBL_IMAGE_INFORMATION','LBL_COMMENTS','LBL_COMMENT_INFORMATION');

		$this->module_id = array();
		$this->module_list = array();

		// Prefetch module info to check active or not and also get list of tabs
		$modulerows = vtlib_prefetchModuleActiveInfo(false);

		$cachedInfo = VTCacheUtils::lookupReport_ListofModuleInfos();

		if ($cachedInfo !== false) {
			$this->module_list = $cachedInfo['module_list'];
			$this->related_modules = $cachedInfo['related_modules'];
		} else {
			if ($modulerows) {
				foreach ($modulerows as $resultrow) {
					if ($resultrow['presence'] == '1') {
						continue; // skip disabled modules
					}
					if ($resultrow['isentitytype'] != '1') {
						continue; // skip extension modules
					}
					if (in_array($resultrow['name'], $restricted_modules)) { // skip restricted modules
						continue;
					}
					if ($resultrow['name']!='Calendar') {
						$this->module_id[$resultrow['tabid']] = $resultrow['name'];
					} else {
						$this->module_id[9] = $resultrow['name'];
						$this->module_id[16] = $resultrow['name'];
					}
					$this->module_list[$resultrow['name']] = array();
				}

				$moduleids = array_keys($this->module_id);
				$reportblocks =
					$adb->pquery(
						'SELECT blockid, blocklabel, tabid FROM vtiger_blocks WHERE tabid IN (' .generateQuestionMarks($moduleids) .')',
						array($moduleids)
					);
				$prev_block_label = '';
				if ($adb->num_rows($reportblocks)) {
					global $doconvert;
					$doconvert = false;
					while ($resultrow = $adb->fetch_array($reportblocks)) {
						$blockid = $resultrow['blockid'];
						$blocklabel = $resultrow['blocklabel'];
						$module = $this->module_id[$resultrow['tabid']];

						if (in_array($blocklabel, $restricted_blocks) ||
							in_array($blockid, $this->module_list[$module]) ||
							isset($this->module_list[$module][getTranslatedString($blocklabel, $module)])
						) {
							continue;
						}

						if (!empty($blocklabel)) {
							if ($module == 'Calendar' && $blocklabel == 'LBL_CUSTOM_INFORMATION') {
								$this->module_list[$module][$blockid] = getTranslatedString($blocklabel, $module);
							} else {
								$this->module_list[$module][$blockid] = getTranslatedString($blocklabel, $module);
							}
							$prev_block_label = $blocklabel;
						} else {
							$this->module_list[$module][$blockid] = getTranslatedString($prev_block_label, $module);
						}
					}
				}

				$relatedmodules = $adb->pquery(
					"SELECT vtiger_tab.name, vtiger_relatedlists.tabid FROM vtiger_tab
					INNER JOIN vtiger_relatedlists on vtiger_tab.tabid=vtiger_relatedlists.related_tabid
					WHERE vtiger_tab.isentitytype=1
					AND vtiger_tab.name NOT IN(".generateQuestionMarks($restricted_modules).")
					AND vtiger_tab.presence = 0 AND vtiger_relatedlists.label!='Activity History' AND vtiger_relatedlists.name not like 'getPotentialsMonth\_%'
					UNION
					SELECT module, vtiger_tab.tabid FROM vtiger_fieldmodulerel
					INNER JOIN vtiger_tab on vtiger_tab.name = vtiger_fieldmodulerel.relmodule
					WHERE vtiger_tab.isentitytype = 1
					AND vtiger_tab.name NOT IN(".generateQuestionMarks($restricted_modules).")
					AND vtiger_tab.presence = 0
					UNION
					SELECT relmodule, vtiger_tab.tabid FROM vtiger_fieldmodulerel
					INNER JOIN vtiger_tab on vtiger_tab.name = vtiger_fieldmodulerel.module
					WHERE vtiger_tab.isentitytype = 1
					AND vtiger_tab.name NOT IN(".generateQuestionMarks($restricted_modules).")
					AND vtiger_tab.presence = 0",
					array($restricted_modules,$restricted_modules,$restricted_modules)
				);
				if ($adb->num_rows($relatedmodules)) {
					while ($resultrow = $adb->fetch_array($relatedmodules)) {
						$module = isset($this->module_id[$resultrow['tabid']]) ? $this->module_id[$resultrow['tabid']] : '';

						if (!isset($this->related_modules[$module])) {
							$this->related_modules[$module] = array();
						}

						if ($module != $resultrow['name']) {
							$this->related_modules[$module][] = $resultrow['name'];
						}

						// To achieve Backward Compatability with Report relations
						if (isset($old_related_modules[$module])) {
							$rel_mod = array();
							foreach ($old_related_modules[$module] as $name) {
								if (vtlib_isModuleActive($name) && isPermitted($name, 'index', '')) {
									$rel_mod[] = $name;
								}
							}
							if (!empty($rel_mod)) {
								$this->related_modules[$module] = array_merge($this->related_modules[$module], $rel_mod);
								$this->related_modules[$module] = array_unique($this->related_modules[$module]);
							}
						}
					}
				}
				foreach ($this->related_modules as $module => $related_modules) {
					if ($module == 'Emails') {
						$this->related_modules[$module] = getEmailRelatedModules();
					}
				}
				// Put the information in cache for re-use
				VTCacheUtils::updateReport_ListofModuleInfos($this->module_list, $this->related_modules);
			}
		}
	}

	/** Get the information to generate the Listview of Reports per folder
	 *  param $mode type of reports to return
	 */
	public function sgetRptFldr($mode = '') {
		global $adb,$log,$mod_strings;
		$returndata = array();
		$result = $adb->pquery('select * from vtiger_reportfolder order by folderid', array());
		$reportfldrow = $adb->fetch_array($result);
		if ($mode != '') {
			// Fetch detials of all reports of folder at once
			$reportsInAllFolders = $this->sgetRptsforFldr(false);
			do {
				if ($reportfldrow['state'] == $mode) {
					$details = array();
					$details['state'] = $reportfldrow['state'];
					$details['id'] = $reportfldrow['folderid'];
					$details['name'] = getTranslatedString($reportfldrow['foldername'], 'Reports');
					$details['description'] = $reportfldrow['description'];
					$details['fname'] = popup_decode_html($details['name']);
					$details['fdescription'] = popup_decode_html($reportfldrow['description']);
					$details['details'] = isset($reportsInAllFolders[$reportfldrow['folderid']]) ? $reportsInAllFolders[$reportfldrow['folderid']] : array();
					$returndata[] = $details;
				}
			} while ($reportfldrow = $adb->fetch_array($result));
		} else {
			do {
				$details = array();
				$details['state'] = $reportfldrow['state'];
				$details['id'] = $reportfldrow['folderid'];
				$details['name'] = empty($mod_strings[$reportfldrow['foldername']]) ? $reportfldrow['foldername']:$mod_strings[$reportfldrow['foldername']];
				$details['description'] = $reportfldrow['description'];
				$details['fname'] = popup_decode_html($details['name']);
				$details['fdescription'] = popup_decode_html($reportfldrow['description']);
				$returndata[] = $details;
			} while ($reportfldrow = $adb->fetch_array($result));
		}
		$log->info('Reports :: sgetRptFldr -> returned report folder information');
		return $returndata;
	}

	/** Get Report information for reports inside each folder
	 *  param folderid if not given will return all folders
	 *  returns only the reports the current user has access to
	 */
	public function sgetRptsforFldr($rpt_fldr_id) {
		global $adb, $log, $current_user;
		$current_user_parent_role_seq='';
		$returndata = array();

		require_once 'include/utils/UserInfoUtil.php';

		$sql = 'select vtiger_report.*, vtiger_reportmodules.*, vtiger_reportfolder.folderid
			from vtiger_report
			inner join vtiger_reportfolder on vtiger_reportfolder.folderid = vtiger_report.folderid
			inner join vtiger_reportmodules on vtiger_reportmodules.reportmodulesid = vtiger_report.reportid';
		$params = array();

		// If information is required only for specific report folder?
		if ($rpt_fldr_id !== false) {
			$sql .= ' where vtiger_reportfolder.folderid=?';
			$params[] = $rpt_fldr_id;
		}

		require 'user_privileges/user_privileges_'.$current_user->id.'.php';
		require_once 'include/utils/GetUserGroups.php';
		$userGroups = new GetUserGroups();
		$userGroups->getAllUserGroups($current_user->id);
		$user_groups = $userGroups->user_groups;
		$user_group_query = '';
		if (!empty($user_groups) && $is_admin==false) {
			$user_group_query = ' (shareid IN ('.generateQuestionMarks($user_groups).") AND setype='groups') OR";
			$params[] = $user_groups;
		}

		$non_admin_query = " vtiger_report.reportid IN (SELECT reportid from vtiger_reportsharing WHERE $user_group_query (shareid=? AND setype='users'))";
		if ($is_admin==false) {
			$sql .= ' and ( ('.$non_admin_query.") or vtiger_report.sharingtype='Public' or vtiger_report.owner = ?";
			$sql .= " or vtiger_report.owner in (
				select vtiger_user2role.userid
				from vtiger_user2role
				inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid
				inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid
				where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%'))";
			$params[] = $current_user->id;
			$params[] = $current_user->id;
		}
		$query = $adb->pquery(
			"select userid
			from vtiger_user2role
			inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid
			inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid
			where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%'",
			array()
		);
		$subordinate_users = array();
		for ($i=0; $i<$adb->num_rows($query); $i++) {
			$subordinate_users[] = $adb->query_result($query, $i, 'userid');
		}
		$result = $adb->pquery($sql, $params);

		$report = $adb->fetch_array($result);
		if (count($report)>0) {
			do {
				$report_details = array();
				$report_details['customizable'] = $report["customizable"];
				$report_details['reportid'] = $report["reportid"];
				$report_details['primarymodule'] = $report["primarymodule"];
				$report_details['secondarymodules'] = $report["secondarymodules"];
				$report_details['state'] = $report["state"];
				$report_details['description'] = $report["description"];
				$report_details['reportname'] = $report["reportname"];
				$report_details['sharingtype'] = $report["sharingtype"];
				$report_details['reporttype'] = $report['reporttype'];
				$report_details['cbreporttype'] = $report['cbreporttype'];
				if ($report['cbreporttype']=='external') {
					$minfo = unserialize(decode_html($report['moreinfo']));
					$report_details['moreinfo'] = $minfo['url'];
					if ($minfo['adduserinfo']==1) {
						$report_details['moreinfo'] = rtrim($report_details['moreinfo'], '/');
						$report_details['moreinfo'] .= strpos($report_details['moreinfo'], '?') ? '&' : '?';
						$report_details['moreinfo'] .= 'usrid='.$current_user->id.'&role='.$current_user_parent_role_seq;
						$report_details['moreinfo'] .= ((isset($current_user_groups) && count($current_user_groups))>0 ? '&grpid='.implode(',', $current_user_groups):'');
					}
				} else {
					$report_details['moreinfo'] = $report['moreinfo'];
				}
				if ($is_admin==true || in_array($report['owner'], $subordinate_users) || $report['owner']==$current_user->id) {
					$report_details['editable'] = 'true';
				} else {
					$report_details['editable'] = 'false';
				}
				$report_details['export'] = isPermitted($report['primarymodule'], 'export');

				if (isPermitted($report['primarymodule'], 'index') == 'yes') {
					$returndata[$report['folderid']][] = $report_details;
				}
			} while ($report = $adb->fetch_array($result));
		}

		if ($rpt_fldr_id !== false) {
			$returndata = $returndata[$rpt_fldr_id];
		}

		$log->info('Reports :: sgetRptsforFldr -> returned report folder information');
		return $returndata;
	}

	/** Function to set the Primary module vtiger_fields for the given Report
	 *  This function sets the primary module columns for the given Report
	 *  It accepts the Primary module as the argument and set the vtiger_fields of the module
	 *  to the varialbe pri_module_columnslist and returns true if sucess
	 */
	public function getPriModuleColumnsList($module) {
		if (empty($module)) {
			return;
		}
		foreach ($this->module_list[$module] as $key => $value) {
			$temp = $this->getColumnsListbyBlock($module, $key);
			if (!empty($ret_module_list[$module][$value])) {
				if (!empty($temp)) {
					$ret_module_list[$module][$value] = array_merge($ret_module_list[$module][$value], $temp);
				}
			} else {
				$ret_module_list[$module][$value] = $this->getColumnsListbyBlock($module, $key);
			}
		}
		if ($module == 'Emails') {
			foreach ($ret_module_list[$module] as $key => $value) {
				foreach ($value as $key1 => $value1) {
					if ($key1 == 'vtiger_activity:time_start:Emails_Time_Start:time_start:T') {
						unset($ret_module_list[$module][$key][$key1]);
					}
				}
			}
		}
		$this->pri_module_columnslist = $ret_module_list;
		return true;
	}

	/** Function to set the Secondary module fileds for the given Report
	 *  This function sets the secondary module columns for the given module
	 *  It accepts the module as the argument and set the vtiger_fields of the module
	 *  to the varialbe sec_module_columnslist and returns true if sucess
	 */
	public function getSecModuleColumnsList($module) {
		if ($module != '') {
			$secmodule = explode(':', $module);
			for ($i=0; $i < count($secmodule); $i++) {
				if ($this->module_list[$secmodule[$i]]) {
					$this->sec_module_columnslist[$secmodule[$i]] = $this->getModuleFieldList(
						$secmodule[$i]
					);
					if ($this->module_list[$secmodule[$i]] == 'Calendar') {
						if ($this->module_list['Events']) {
							$this->sec_module_columnslist['Events'] = $this->getModuleFieldList('Events');
						}
					}
				}
			}
			if ($module == 'Emails') {
				foreach ($this->sec_module_columnslist[$module] as $key => $value) {
					foreach ($value as $key1 => $value1) {
						if ($key1 == 'vtiger_activity:time_start:Emails_Time_Start:time_start:T') {
							unset($this->sec_module_columnslist[$module][$key][$key1]);
						}
					}
				}
			}
		}
		return true;
	}

	/**
	 *
	 * @param String $module
	 * @param array $blockIdList
	 * @param Array $currentFieldList
	 * @return Array
	 */
	public function getBlockFieldList($module, $blockIdList, $currentFieldList) {
		if (!empty($currentFieldList)) {
			$temp = $this->getColumnsListbyBlock($module, $blockIdList);
			if (!empty($temp)) {
				$currentFieldList = array_merge($currentFieldList, $temp);
			}
		} else {
			$currentFieldList = $this->getColumnsListbyBlock($module, $blockIdList);
		}
		return $currentFieldList;
	}

	public function getModuleFieldList($module) {
		$ret_module_list = array();
		foreach ($this->module_list[$module] as $key => $value) {
			if (isset($ret_module_list[$module]) && isset($ret_module_list[$module][$value])) {
				$currentFieldList = $ret_module_list[$module][$value];
			} else {
				$currentFieldList = array();
			}
			$ret_module_list[$module][$value] = $this->getBlockFieldList($module, $key, $currentFieldList);
		}
		return $ret_module_list[$module];
	}

	/** Function to get vtiger_fields for the given module and block
	 *  This function gets the vtiger_fields for the given module
	 *  It accepts the module and the block as arguments and
	 *  returns the array column lists
	 *  Array module_columnlist[ vtiger_fieldtablename:fieldcolname:module_fieldlabel1:fieldname:fieldtypeofdata]=fieldlabel
	 */
	public function getColumnsListbyBlock($module, $block) {
		global $adb, $log, $current_user;

		if (is_string($block)) {
			$block = explode(',', $block);
		}
		$skipTalbes = array('vtiger_emaildetails','vtiger_attachments');

		$tabid = getTabid($module);
		if ($module == 'Calendar') {
			$tabid = array('9','16');
		}

		require 'user_privileges/user_privileges_'.$current_user->id.'.php';
		//Security Check
		if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] ==0) {
			if ($module == 'Calendar') {
				// calendar is special because it is two modules and has many overlapping fields so we have to filter them
				$sql = 'select * from vtiger_field where vtiger_field.block in ('. generateQuestionMarks($block) .') and vtiger_field.displaytype in (1,2,3,4) and vtiger_field.presence in (0,2) AND tablename NOT IN ('.generateQuestionMarks($skipTalbes).') ';
				$sql.= ' and vtiger_field.fieldid in (select min(fieldid) from vtiger_field where vtiger_field.tabid in ('. generateQuestionMarks($tabid) .') group by fieldlabel) order by sequence';
				$params = array($block, $skipTalbes, $tabid);
			} else {
				$sql = 'select *
					from vtiger_field
					where vtiger_field.tabid in ('. generateQuestionMarks($tabid) .') and vtiger_field.block in ('. generateQuestionMarks($block)
					.') and vtiger_field.displaytype in (1,2,3,4) and vtiger_field.presence in (0,2) AND tablename NOT IN ('.generateQuestionMarks($skipTalbes)
					.') order by sequence';
				$params = array($tabid, $block, $skipTalbes);
			}
		} else {
			if ($module == 'Calendar') {
				// calendar is special because it is two modules and has many overlapping fields so we have to filter them
				$sql = 'select distinct vtiger_field.*
					from vtiger_field
					inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid
					inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid
					where vtiger_field.block in ('. generateQuestionMarks($block)
					.') and vtiger_field.displaytype in (1,2,3,4) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0 and vtiger_field.presence in (0,2)';
				$params = array($block);
			} else {
				$sql = 'select distinct vtiger_field.*
					from vtiger_field
					inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid
					inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid
					where vtiger_field.tabid in ('. generateQuestionMarks($tabid) .') and vtiger_field.block in ('. generateQuestionMarks($block)
					.') and vtiger_field.displaytype in (1,2,3,4) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0 and vtiger_field.presence in (0,2)';
				$params = array($tabid, $block);
			}
			$profileList = getCurrentUserProfileList();
			if (count($profileList) > 0) {
				$sql .= " and vtiger_profile2field.profileid in (". generateQuestionMarks($profileList) .")";
				$params[] = $profileList;
			}
			$sql .= ' and tablename NOT IN ('.generateQuestionMarks($skipTalbes).') ';
			$params[] = $skipTalbes;
			if ($module == 'Calendar') {
				$sql.= ' and vtiger_field.fieldid in (select min(fieldid) from vtiger_field where vtiger_field.tabid in ('.generateQuestionMarks($tabid);
				$sql.= ') group by fieldlabel) order by sequence';
				$params[] = $tabid;
			} else {
				$sql.= ' group by vtiger_field.fieldid order by sequence';
			}
		}
		$module_columnlist = array();
		$result = $adb->pquery($sql, $params);
		$noofrows = $adb->num_rows($result);
		for ($i=0; $i<$noofrows; $i++) {
			$fieldtablename = $adb->query_result($result, $i, 'tablename');
			$fieldcolname = $adb->query_result($result, $i, 'columnname');
			$fieldname = $adb->query_result($result, $i, 'fieldname');
			$fieldtype = $adb->query_result($result, $i, 'typeofdata');
			//$uitype = $adb->query_result($result, $i, 'uitype');
			$fieldtype = explode('~', $fieldtype);
			$fieldtypeofdata = $fieldtype[0];
			//$blockid = $adb->query_result($result, $i, 'block');

			//Here we Changing the displaytype of the field. So that its criteria will be displayed correctly in Reports Advance Filter.
			$fieldtypeofdata=ChangeTypeOfData_Filter($fieldtablename, $fieldcolname, $fieldtypeofdata);

			if ($fieldtablename == 'vtiger_crmentity') {
				$fieldtablename = $fieldtablename.$module;
			}
			if ($fieldname == 'assigned_user_id') {
				$fieldtablename = 'vtiger_users'.$module;
				$fieldcolname = 'user_name';
			}
			if ($fieldname == 'assigned_user_id1') {
				$fieldtablename = 'vtiger_usersRel1';
				$fieldcolname = 'user_name';
			}

			$fieldlabel = $adb->query_result($result, $i, 'fieldlabel');
			if ($module == 'Emails' && $fieldlabel == 'Date & Time Sent') {
				$fieldlabel = 'Date Sent';
				$fieldtypeofdata = 'D';
			}
			$fieldlabel1 = str_replace(' ', '_', $fieldlabel);
			$fieldlabel1 = ReportRun::replaceSpecialChar($fieldlabel1);
			$optionvalue = $fieldtablename.":".$fieldcolname.":".$module."_".$fieldlabel1.":".$fieldname.":".$fieldtypeofdata;
			$comparefield = '$'.$module.'#'.$fieldname.'$'."::".getTranslatedString($module, $module)." ".getTranslatedString($fieldlabel, $module);
			switch ($fieldtypeofdata) {
				case 'NN':
				case 'N':
				case 'I':
					$this->adv_rel_fields['NN'][] = $comparefield;
					$this->adv_rel_fields['N'][] = $comparefield;
					$this->adv_rel_fields['I'][] = $comparefield;
					break;
				default:
					$this->adv_rel_fields[$fieldtypeofdata][] = $comparefield;
					break;
			}
			$module_columnlist[$optionvalue] = $fieldlabel;
		}
		foreach ($this->adv_rel_fields as $ftypes => $flds) {
			$uniq = array();
			foreach ($flds as $val) {
				$uniq[$val] = true;
			}
			$uniq = array_keys($uniq);
			$this->adv_rel_fields[$ftypes] = $uniq;
		}
		$blockname = getBlockName($block);
		if ($blockname == 'LBL_RELATED_PRODUCTS' && in_array($module, getInventoryModules())) {
			$fieldtablename = 'vtiger_inventoryproductrel';
			$fields = array('productid'=>getTranslatedString('Product Name', $module),
							'serviceid'=>getTranslatedString('Service Name', $module),
							'listprice'=>getTranslatedString('List Price', $module),
							'discount'=>getTranslatedString('Discount', $module),
							'quantity'=>getTranslatedString('Quantity', $module),
							'comment'=>getTranslatedString('Comments', $module),
			);
			$fields_datatype = array('productid'=>'V',
							'serviceid'=>'V',
							'listprice'=>'N',
							'discount'=>'N',
							'quantity'=>'N',
							'comment'=>'V',
			);
			foreach ($fields as $fieldcolname => $label) {
				$fieldtypeofdata = $fields_datatype[$fieldcolname];
				if ($fieldcolname != 'productid' || $fieldcolname !='serviceid') {
					$optionvalue = $fieldtablename.$module.":".$fieldcolname.":".$module."_".$label.":".$fieldcolname.":".$fieldtypeofdata;
				} else {
					$optionvalue = $fieldtablename.":".$fieldcolname.":".$module."_".$label.":".$fieldcolname.":".$fieldtypeofdata;
				}
				$module_columnlist[$optionvalue] = $label;
			}
		}
		$log->info("Reports :: FieldColumns->Successfully returned ColumnslistbyBlock".$module.$block);
		return $module_columnlist;
	}

	/** Function to set the standard filter vtiger_fields for the given vtiger_report
	 *  This function gets the standard filter vtiger_fields for the given vtiger_report
	 *  and set the values to the corresponding variables
	 *  It accepts the repordid as argument
	 */
	public function getSelectedStandardCriteria($reportid) {
		global $adb;
		$sSQL = 'select vtiger_reportdatefilter.*
			from vtiger_reportdatefilter
			inner join vtiger_report on vtiger_report.reportid = vtiger_reportdatefilter.datefilterid
			where vtiger_report.reportid=?';
		$result = $adb->pquery($sSQL, array($reportid));
		$selectedstdfilter = $adb->fetch_array($result);

		$this->stdselectedcolumn = $selectedstdfilter['datecolumnname'];
		$this->stdselectedfilter = $selectedstdfilter['datefilter'];

		if ($selectedstdfilter['datefilter'] == 'custom') {
			if ($selectedstdfilter['startdate'] != '0000-00-00') {
				$startDateTime = new DateTimeField($selectedstdfilter['startdate'].' '. date('H:i:s'));
				$this->startdate = $startDateTime->getDisplayDate();
			}
			if ($selectedstdfilter['enddate'] != '0000-00-00') {
				$endDateTime = new DateTimeField($selectedstdfilter['enddate'].' '. date('H:i:s'));
				$this->enddate = $endDateTime->getDisplayDate();
			}
		}
	}

	/** Function to get the combo values for the standard filter
	 *  This function get the combo values for the standard filter for the given vtiger_report
	 *  and return a HTML string
	 */
	public function getSelectedStdFilterCriteria($selecteddatefilter = "") {
		global $mod_strings;
		$options = array();
		$datefiltervalue = array("custom","prevfy","thisfy","nextfy","prevfq","thisfq","nextfq",
				"yesterday","today","tomorrow","lastweek","thisweek","nextweek","lastmonth","thismonth",
				"nextmonth","last7days","last14days","last30days", "last60days","last90days","last120days",
				"next30days","next60days","next90days","next120days"
				);

		$datefilterdisplay = array("Custom","Previous FY", "Current FY","Next FY","Previous FQ","Current FQ","Next FQ","Yesterday",
				"Today","Tomorrow","Last Week","Current Week","Next Week","Last Month","Current Month",
				"Next Month","Last 7 Days","Last 14 Days","Last 30 Days","Last 60 Days","Last 90 Days","Last 120 Days",
				"Next 7 Days","Next 30 Days","Next 60 Days","Next 90 Days","Next 120 Days"
				);

		for ($i=0; $i<count($datefiltervalue); $i++) {
			if ($selecteddatefilter == $datefiltervalue[$i]) {
				$options[] = array("selected"=>true,"value"=>$datefiltervalue[$i],"label"=>$mod_strings[$datefilterdisplay[$i]]);
			} else {
				$options[] = array("value"=>$datefiltervalue[$i],"label"=>$mod_strings[$datefilterdisplay[$i]]);
			}
		}

		return $options;
	}

	/** Function to get the selected standard filter columns
	 *  This function returns the selected standard filter criteria
	 *  which is selected for vtiger_reports as an array
	 *  Array stdcriteria_list[fieldtablename:fieldcolname:module_fieldlabel1]=fieldlabel
	 */
	public function getStdCriteriaByModule($module) {
		global $adb, $log, $current_user;
		require 'user_privileges/user_privileges_'.$current_user->id.'.php';

		$tabid = getTabid($module);
		foreach ($this->module_list[$module] as $blockid) {
			$blockids[] = $blockid;
		}
		$blockids = implode(',', $blockids);

		$params = array($tabid, $blockids);
		if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
			//uitype 6 and 23 added for start_date,EndDate,Expected Close Date
			$sql = 'select *
				from vtiger_field
				where vtiger_field.tabid=? and (vtiger_field.uitype=5 or vtiger_field.uitype=6 or vtiger_field.uitype=23 or vtiger_field.displaytype=2 or vtiger_field.displaytype=4)
					and vtiger_field.block in ('. generateQuestionMarks($block) .') and vtiger_field.presence in (0,2) order by vtiger_field.sequence';
		} else {
			$profileList = getCurrentUserProfileList();
			$sql = 'select *
				from vtiger_field
				inner join vtiger_tab on vtiger_tab.tabid = vtiger_field.tabid
				inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid
				inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid
				where vtiger_field.tabid=? and (vtiger_field.uitype=5 or vtiger_field.displaytype=2 or vtiger_field.displaytype=4) and vtiger_profile2field.visible=0
					and vtiger_def_org_field.visible=0 and vtiger_field.block in ('. generateQuestionMarks($block) .') and vtiger_field.presence in (0,2)';
			if (count($profileList) > 0) {
				$sql .= ' and vtiger_profile2field.profileid in ('. generateQuestionMarks($profileList) .')';
				$params[] = $profileList;
			}
			$sql .= ' order by vtiger_field.sequence';
		}

		$result = $adb->pquery($sql, $params);

		while ($criteriatyperow = $adb->fetch_array($result)) {
			$fieldtablename = $criteriatyperow['tablename'];
			$fieldcolname = $criteriatyperow['columnname'];
			$fieldlabel = $criteriatyperow['fieldlabel'];

			if ($fieldtablename == 'vtiger_crmentity') {
				$fieldtablename = $fieldtablename.$module;
			}
			$fieldlabel1 = str_replace(" ", "_", $fieldlabel);
			$optionvalue = $fieldtablename.":".$fieldcolname.":".$module."_".$fieldlabel1;
			$stdcriteria_list[$optionvalue] = $fieldlabel;
		}

		$log->info('Reports :: StdfilterColumns->Successfully returned Stdfilter for'.$module);
		return $stdcriteria_list;
	}

	/** Function to form a javascript to determine the start date and end date for a standard filter */
	public function getCriteriaJS() {
		return getCriteriaJS('NewReport');
	}

	public function getEscapedColumns($selectedfields) {
		$fieldname = $selectedfields[3];
		if ($fieldname == 'parent_id') {
			if ($this->primarymodule == 'HelpDesk' && $selectedfields[0] == 'vtiger_crmentityRelHelpDesk') {
				$querycolumn = "case vtiger_crmentityRelHelpDesk.setype
					when 'Accounts' then vtiger_accountRelHelpDesk.accountname
					when 'Contacts' then vtiger_contactdetailsRelHelpDesk.lastname End '".$selectedfields[2]."', vtiger_crmentityRelHelpDesk.setype 'Entity_type'";
				return $querycolumn;
			}
			if ($this->primarymodule == 'Products' || $this->secondarymodule == 'Products') {
				$querycolumn = "case vtiger_crmentityRelProducts.setype
					when 'Accounts' then vtiger_accountRelProducts.accountname
					when 'Leads' then vtiger_leaddetailsRelProducts.lastname
					when 'Potentials' then vtiger_potentialRelProducts.potentialname End '".$selectedfields[2]."', vtiger_crmentityRelProducts.setype 'Entity_type'";
			}
		}
		return $querycolumn;
	}

	public function getaccesfield($module) {
		global $adb;
		$access_fields = array();

		$profileList = getCurrentUserProfileList();
		$query = 'select vtiger_field.fieldname
			from vtiger_field
			inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid
			inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid where';
		$params = array();
		if ($module == "Calendar") {
			$query .= " vtiger_field.tabid in (9,16) and vtiger_field.displaytype in (1,2,3,4) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0 and vtiger_field.presence in (0,2)";
			if (count($profileList) > 0) {
				$query .= " and vtiger_profile2field.profileid in (". generateQuestionMarks($profileList) .")";
				$params[] = $profileList;
			}
			$query .= ' group by vtiger_field.fieldid order by block,sequence';
		} else {
			array_push($params, $this->primodule, $this->secmodule);
			$query .= ' vtiger_field.tabid in (
				select tabid
				from vtiger_tab
				where vtiger_tab.name in (?,?)) and vtiger_field.displaytype in (1,2,3,4) and vtiger_profile2field.visible=0
					and vtiger_def_org_field.visible=0 and vtiger_field.presence in (0,2)';
			if (count($profileList) > 0) {
				$query .= ' and vtiger_profile2field.profileid in ('. generateQuestionMarks($profileList) .')';
				$params[] = $profileList;
			}
			$query .= ' group by vtiger_field.fieldid order by block,sequence';
		}
		$result = $adb->pquery($query, $params);

		while ($collistrow = $adb->fetch_array($result)) {
			$access_fields[] = $collistrow['fieldname'];
		}
		return $access_fields;
	}

	/** Function to set the order of grouping and to find the columns responsible
	 *  to the grouping
	 *  This function accepts the vtiger_reportid as variable,sets the variable ascdescorder[] to the sort order and
	 *  returns the array array_list which has the column responsible for the grouping
	 *  Array array_list[0]=columnname
	 */
	public function getSelctedSortingColumns($reportid) {
		global $adb, $log;

		$sreportsortsql = 'select vtiger_reportsortcol.* from vtiger_report';
		$sreportsortsql .= ' inner join vtiger_reportsortcol on vtiger_report.reportid = vtiger_reportsortcol.reportid';
		$sreportsortsql .= ' where vtiger_report.reportid =? order by vtiger_reportsortcol.sortcolid';

		$result = $adb->pquery($sreportsortsql, array($reportid));
		$noofrows = $adb->num_rows($result);
		$array_list = array();
		for ($i=0; $i<$noofrows; $i++) {
			$fieldcolname = $adb->query_result($result, $i, 'columnname');
			$sort_values = $adb->query_result($result, $i, 'sortorder');
			$this->ascdescorder[] = $sort_values;
			$array_list[] = $fieldcolname;
		}

		$log->info('Reports :: Successfully returned getSelctedSortingColumns');
		return $array_list;
	}

	/** Function to get the selected columns list for a selected vtiger_report
	 *  This function accepts the vtiger_reportid as the argument and get the selected columns
	 *  for the given vtiger_reportid and it forms a combo lists and returns
	 *  HTML of the combo values
	 */
	public function getSelectedColumnsList($reportid) {
		global $adb, $log,$current_user;

		$ssql = 'select vtiger_selectcolumn.* from vtiger_report inner join vtiger_selectquery on vtiger_selectquery.queryid = vtiger_report.queryid';
		$ssql .= ' left join vtiger_selectcolumn on vtiger_selectcolumn.queryid = vtiger_selectquery.queryid';
		$ssql .= ' where vtiger_report.reportid = ?';
		$ssql .= ' order by vtiger_selectcolumn.columnindex';
		$result = $adb->pquery($ssql, array($reportid));
		$permitted_fields = array();

		$selected_mod = explode(':', $this->secmodule);
		$selected_mod[] = $this->primodule;

		$inventory_fields = array('quantity','listprice','serviceid','productid','discount','comment');
		$inventory_modules = getInventoryModules();
		$options = array();
		while ($columnslistrow = $adb->fetch_array($result)) {
			$fieldname = '';
			$fieldcolname = decode_html($columnslistrow['columnname']);

			$selmod_field_disabled = true;
			foreach ($selected_mod as $smod) {
				if ((stripos($fieldcolname, ':'.$smod.'_')>-1) && vtlib_isModuleActive($smod)) {
					$selmod_field_disabled = false;
					break;
				}
			}
			if ($selmod_field_disabled==false) {
				list($tablename, $colname, $module_field, $fieldname, $single) = explode(':', $fieldcolname);
				require 'user_privileges/user_privileges_'.$current_user->id.'.php';
				list($module,$field) = explode('_', $module_field);
				if (count($permitted_fields) == 0 && $is_admin == false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1) {
					$permitted_fields = $this->getaccesfield($module);
				}
				$fieldlabel = trim(str_replace($module, ' ', $module_field));
				$mod_arr=explode('_', $fieldlabel);
				$mod = ($mod_arr[0] == '')?$module:$mod_arr[0];
				$fieldlabel = decode_html(trim(str_replace('_', ' ', $fieldlabel)));
				//modified code to support i18n issue
				$mod_lbl = getTranslatedString($mod, $module); //module
				$fld_lbl = getTranslatedString($fieldlabel, $module); //fieldlabel
				$fieldlabel = $mod_lbl.' '.$fld_lbl;
				if (in_array($fieldname, $inventory_fields) && in_array($mod, $inventory_modules)) {
					$options[] = array('permission'=>'yes','value'=>$fieldcolname,'label'=>$fieldlabel);
				} else {
					if (CheckFieldPermission($fieldname, $mod) != 'true' && $colname!='crmid') {
						$options[] = array('permission'=>'no','value'=>$fieldcolname,'label'=>$fieldlabel,'disabled'=>'true');
					} else {
						$options[] = array('permission'=>'yes','value'=>$fieldcolname,'label'=>$fieldlabel);
					}
				}
			}
		}
		$log->info('ReportRun :: Successfully returned getQueryColumnsList'.$reportid);
		return $options;
	}

	public function getAdvancedFilterList($reportid) {
		global $adb, $log, $current_user;

		$advft_criteria = array();
		$i = 1;
		$j = 0;
		$groupsresult = $adb->pquery('SELECT * FROM vtiger_relcriteria_grouping WHERE queryid = ? ORDER BY groupid', array($reportid));
		while ($relcriteriagroup = $adb->fetch_array($groupsresult)) {
			$groupId = $relcriteriagroup["groupid"];
			$groupCondition = $relcriteriagroup["group_condition"];

			$ssql = 'select vtiger_relcriteria.*
				from vtiger_report
				inner join vtiger_relcriteria on vtiger_relcriteria.queryid = vtiger_report.queryid
				left join vtiger_relcriteria_grouping on vtiger_relcriteria.queryid = vtiger_relcriteria_grouping.queryid
					and vtiger_relcriteria.groupid = vtiger_relcriteria_grouping.groupid
				where vtiger_report.reportid = ? AND vtiger_relcriteria.groupid = ? order by vtiger_relcriteria.columnindex';

			$result = $adb->pquery($ssql, array($reportid, $groupId));
			$noOfColumns = $adb->num_rows($result);
			if ($noOfColumns <= 0) {
				continue;
			}

			while ($relcriteriarow = $adb->fetch_array($result)) {
				//$columnIndex = $relcriteriarow['columnindex'];
				$criteria = array();
				$criteria['columnname'] = html_entity_decode($relcriteriarow['columnname']);
				$criteria['comparator'] = $relcriteriarow['comparator'];
				$advfilterval = $relcriteriarow['value'];
				$col = explode(':', $relcriteriarow['columnname']);

				$moduleFieldLabel = $col[2];
				//$fieldName = $col[3];

				list($module, $fieldLabel) = explode('_', $moduleFieldLabel, 2);
				$fieldInfo = getFieldByReportLabel($module, $fieldLabel);
				$fieldType = null;
				if (!empty($fieldInfo)) {
					$field = WebserviceField::fromArray($adb, $fieldInfo);
					$fieldType = $field->getFieldDataType();
				}
				if ($fieldType == 'currency') {
					if ($field->getUIType() == '71') {
						$advfilterval = CurrencyField::convertToUserFormat($advfilterval, $current_user);
					} elseif ($field->getUIType() == '72') {
						$advfilterval = CurrencyField::convertToUserFormat($advfilterval, $current_user, true);
					}
				}

				$temp_val = explode(",", $relcriteriarow['value']);
				if ($col[4] == 'D' || ($col[4] == 'T' && $col[1] != 'time_start' && $col[1] != 'time_end') || ($col[4] == 'DT')) {
					$val = array();
					for ($x=0; $x<count($temp_val); $x++) {
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
				$criteria['value'] = decode_html($advfilterval);
				$criteria['column_condition'] = $relcriteriarow['column_condition'];

				$advft_criteria[$i]['columns'][$j] = $criteria;
				$advft_criteria[$i]['condition'] = $groupCondition;
				$j++;
			}
			$i++;
		}
		// Clear the condition (and/or) for last group, if any.
		if (!empty($advft_criteria[$i-1]['condition'])) {
			$advft_criteria[$i-1]['condition'] = '';
		}
		$this->advft_criteria = $advft_criteria;
		$log->info('Reports :: Successfully returned getAdvancedFilterList');
		return true;
	}

	/** Function to get the list of report folders when Save and run the report
	 *  This function gets the vtiger_report folders from database and form
	 *  a combo values of the folders and return HTML of the combo values
	 */
	public function sgetRptFldrSaveReport() {
		global $adb, $log;
		$result = $adb->pquery('select * from vtiger_reportfolder order by folderid', array());
		$reportfldrow = $adb->fetch_array($result);
		$shtml = '';
		do {
			$shtml .= "<option value='".$reportfldrow['folderid']."'>".$reportfldrow['foldername'].'</option>';
		} while ($reportfldrow = $adb->fetch_array($result));
		$log->info('Reports :: Successfully returned sgetRptFldrSaveReport');
		return $shtml;
	}

	/** Function to get the column to total vtiger_fields in Reports
	 *  This function gets columns to total vtiger_field
	 *  and generated the html for that vtiger_fields
	 *  It returns the HTML of the vtiger_fields along with the check boxes
	 */
	public function sgetColumntoTotal($primarymodule, $secondarymodule) {
		$options = array();
		$options []= $this->sgetColumnstoTotalHTML($primarymodule, 0);
		if (!empty($secondarymodule)) {
			//$secondarymodule = explode(":",$secondarymodule);
			for ($i=0; $i < count($secondarymodule); $i++) {
				$options []= $this->sgetColumnstoTotalHTML($secondarymodule[$i], ($i+1));
			}
		}
		return $options;
	}

	/** Function to get the selected columns of total vtiger_fields in Reports
	 *  This function gets selected columns of total vtiger_field
	 *  and generated the html for that vtiger_fields
	 *  It returns the HTML of the vtiger_fields along with the check boxes
	 */
	public function sgetColumntoTotalSelected($primarymodule, $secondarymodule, $reportid) {
		global $adb, $log;
		$options = array();
		if ($reportid != '') {
			$ssql = 'select vtiger_reportsummary.*
				from vtiger_reportsummary
				inner join vtiger_report on vtiger_report.reportid = vtiger_reportsummary.reportsummaryid
				where vtiger_report.reportid=?';
			$result = $adb->pquery($ssql, array($reportid));
			if ($result) {
				while ($reportsummaryrow = $adb->fetch_array($result)) {
					$this->columnssummary[] = decode_html($reportsummaryrow['columnname']);
				}
			}
		}
		$options []= $this->sgetColumnstoTotalHTML($primarymodule, 0);
		if ($secondarymodule != '') {
			$secondarymodule = explode(':', $secondarymodule);
			for ($i=0; $i < count($secondarymodule); $i++) {
				$options []= $this->sgetColumnstoTotalHTML($secondarymodule[$i], ($i+1));
			}
		}
		$log->info('Reports :: Successfully returned sgetColumntoTotalSelected');
		return $options;
	}

	/** Function to form the HTML for columns to total
	 *  This function formulates the HTML format of the
	 *  vtiger_fields along with four checkboxes
	 *  It returns the HTML of the vtiger_fields along with the check boxes
	 */
	public function sgetColumnstoTotalHTML($module) {
		global $adb, $log, $current_user;
		require 'user_privileges/user_privileges_'.$current_user->id.'.php';
		$tabid = getTabid($module);
		$escapedchars = array('_SUM','_AVG','_MIN','_MAX');
		$sparams = array($tabid);
		if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] ==0) {
			$ssql = 'select *
				from vtiger_field
				inner join vtiger_tab on vtiger_tab.tabid = vtiger_field.tabid
				where vtiger_field.uitype != 50 and vtiger_field.tabid=? and vtiger_field.displaytype in (1,2,3,4) and vtiger_field.presence in (0,2)';
			$calcf = "select *
				from vtiger_field
				inner join vtiger_tab on vtiger_tab.tabid = vtiger_field.tabid
				where vtiger_field.uitype!=50 and vtiger_field.tablename='vtiger_activitycf' and vtiger_field.displaytype in (1,2,3,4) and vtiger_field.presence in (0,2)";
		} else {
			$profileList = getCurrentUserProfileList();
			$ssql = 'select *
				from vtiger_field
				inner join vtiger_tab on vtiger_tab.tabid = vtiger_field.tabid
				inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid
				inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid
				where vtiger_field.uitype != 50 and vtiger_field.tabid=? and vtiger_field.displaytype in (1,2,3,4)
					and vtiger_def_org_field.visible=0 and vtiger_profile2field.visible=0 and vtiger_field.presence in (0,2)';
			if (count($profileList) > 0) {
				$ssql .= ' and vtiger_profile2field.profileid in ('. generateQuestionMarks($profileList) .')';
				$sparams[] = $profileList;
			}
			$calcf = "select *
				from vtiger_field
				inner join vtiger_tab on vtiger_tab.tabid = vtiger_field.tabid inner
				join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid
				inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid
				where vtiger_field.uitype != 50 and vtiger_field.tablename='vtiger_activitycf' and vtiger_field.displaytype in (1,2,3,4)
					and vtiger_def_org_field.visible=0 and vtiger_profile2field.visible=0 and vtiger_field.presence in (0,2)";
			if ($tabid==9 && count($profileList) > 0) {
				$calcf .= ' and vtiger_profile2field.profileid in ('. generateQuestionMarks($profileList) .')';
				$sparams[] = $profileList;
			}
		}

		//Added to avoid display the Related fields (Account name,Vandor name,product name, etc) in Report Calculations(SUM,AVG..)
		switch ($tabid) {
			case 2://Potentials
				//ie. Campaign name will not displayed in Potential's report calcullation
				$ssql.= " and vtiger_field.fieldname not in ('campaignid')";
				break;
			case 4://Contacts
				$ssql.= " and vtiger_field.fieldname not in ('account_id')";
				break;
			case 6://Accounts
				$ssql.= " and vtiger_field.fieldname not in ('account_id')";
				break;
			case 9://Calendar
				$ssql.= " and vtiger_field.fieldname not in ('parent_id','contact_id') UNION $calcf";
				break;
			case 13://Trouble tickets(HelpDesk)
				$ssql.= " and vtiger_field.fieldname not in ('parent_id','product_id')";
				break;
			case 14://Products
				$ssql.= " and vtiger_field.fieldname not in ('vendor_id','product_id')";
				break;
			case 20://Quotes
				$ssql.= " and vtiger_field.fieldname not in ('potential_id','assigned_user_id1','account_id','currency_id')";
				break;
			case 21://Purchase Order
				$ssql.= " and vtiger_field.fieldname not in ('contact_id','vendor_id','currency_id')";
				break;
			case 22://SalesOrder
				$ssql.= " and vtiger_field.fieldname not in ('potential_id','account_id','contact_id','quote_id','currency_id')";
				break;
			case 23://Invoice
				$ssql.= " and vtiger_field.fieldname not in ('salesorder_id','contact_id','account_id','currency_id')";
				break;
			case 26://Campaigns
				$ssql.= " and vtiger_field.fieldname not in ('product_id')";
				break;
		}

		$ssql.= ' order by sequence';

		$result = $adb->pquery($ssql, $sparams);
		$columntototalrow = $adb->fetch_array($result);
		$options_list = array();
		do {
			$typeofdata = explode('~', $columntototalrow['typeofdata']);
			$columntototalrow['fieldlabel'] = decode_html($columntototalrow['fieldlabel']);
			if (($typeofdata[0] == 'N' || $typeofdata[0] == 'NN' || $typeofdata[0] == 'I' || $typeofdata[0] == 'T' || $columntototalrow['columnname']=='totaltime')
					&& ($columntototalrow['uitype']!=10 && $columntototalrow['uitype']!=101)
			) {
				$options = array();
				$filters = array();
				$options['label'][] = getTranslatedString($columntototalrow['tablabel'], $columntototalrow['tablabel']).' -'.getTranslatedString($columntototalrow['fieldlabel'], $columntototalrow['tablabel']);
				$filters['label'][] = getTranslatedString($columntototalrow['tablabel'], $columntototalrow['tablabel']).' -'.getTranslatedString($columntototalrow['fieldlabel'], $columntototalrow['tablabel']);
				if (isset($this->columnssummary)) {
					$selectedcolumn = '';
					$selectedcolumn1 = '';
					for ($i=0; $i < count($this->columnssummary); $i++) {
						$selectedcolumnarray = explode(':', $this->columnssummary[$i]);
						$selectedcolumn = $selectedcolumnarray[1].':'.$selectedcolumnarray[2].':'.str_replace($escapedchars, '', $selectedcolumnarray[3]);
						if ($selectedcolumn!=$columntototalrow['tablename'].':'.$columntototalrow['columnname'].':'.str_replace(' ', '_', $columntototalrow['fieldlabel'])) {
							$selectedcolumn = '';
						} else {
							$selectedcolumn1[$selectedcolumnarray[4]] = $this->columnssummary[$i];
						}
					}

					$columntototalrow['fieldlabel'] = str_replace(' ', '_', $columntototalrow['fieldlabel']);
					$options[] = getTranslatedString($columntototalrow['tablabel'], $columntototalrow['tablabel']).' - '.getTranslatedString($columntototalrow['fieldlabel'], $columntototalrow['tablabel']);
					$baseColName = 'cb:'.$columntototalrow['tablename'].':'.$columntototalrow['columnname'].':'.$columntototalrow['fieldlabel'];
					if (isset($selectedcolumn1[2]) && $selectedcolumn1[2] == $baseColName.'_SUM:2') {
						$filters['checkboxes'][] = array('name'=>$baseColName.'_SUM:2','checked'=>true);
					} else {
						$filters['checkboxes'][] = array('name'=>$baseColName.'_SUM:2');
					}
					if (isset($selectedcolumn1[3]) && $selectedcolumn1[3] == $baseColName.'_AVG:3') {
						$filters['checkboxes'][] = array('name' => $baseColName.'_AVG:3', 'checked'=>true);
					} else {
						$filters['checkboxes'][] = array('name'=>$baseColName.'_AVG:3');
					}

					if (isset($selectedcolumn1[4]) && $selectedcolumn1[4] == $baseColName.'_MIN:4') {
						$filters['checkboxes'][] = array('name'=>$baseColName.'_MIN:4', 'checked'=>true);
					} else {
						$filters['checkboxes'][] = array('name'=>$baseColName.'_MIN:4');
					}

					if (isset($selectedcolumn1[5]) && $selectedcolumn1[5] == $baseColName.'_MAX:5') {
						$filters['checkboxes'][] = array('name'=>$baseColName.'_MAX:5', 'checked'=>true);
					} else {
						$filters['checkboxes'][] = array('name'=>$baseColName.'_MAX:5');
					}
				} else {
					$baseColName = 'cb:'.$columntototalrow['tablename'].':'.$columntototalrow['columnname'].':'.$columntototalrow['fieldlabel'];
					$filters['checkboxes'][] = array('name'=>$baseColName.'_SUM:2');
					$filters['checkboxes'][] = array('name'=>$baseColName.'_AVG:3');
					$filters['checkboxes'][] = array('name'=>$baseColName.'_MIN:4');
					$filters['checkboxes'][] = array('name' => $baseColName.'_MAX:5');
				}
				$options_list [] = $filters;
			}
		} while ($columntototalrow = $adb->fetch_array($result));

		$log->info('Reports :: Successfully returned sgetColumnstoTotalHTML');
		return $options_list;
	}

	/** Function to get the advanced filter criteria for an option
	 *  This function accepts The option in the advanced filter as an argument
	 *  This generate filter criteria for the advanced filter
	 *  It returns a HTML string of combo values
	 */
	public static function getAdvCriteriaHTML($selected = '') {
		global $adv_filter_options;
		$filters = array();
		foreach ($adv_filter_options as $key => $value) {
			if ($selected == $key) {
				$filters[] = array('selected'=>true, 'value'=>$key, 'label'=>$value);
			} else {
				$filters[] = array('value'=>$key, 'label'=>$value);
			}
		}
		return $filters;
	}
}

/** Function to get the primary module list in vtiger_reports
 *  This function generates the list of primary modules in vtiger_reports
 *  and returns an array of permitted modules
 */
function getReportsModuleList($focus) {
	$modules = array();
	foreach ($focus->module_list as $key => $value) {
		if (isPermitted($key, 'index') == 'yes') {
			$modules[$key] = getTranslatedString($key, $key);
		}
	}
	asort($modules);
	return $modules;
}

/** Function to get the Related module list in vtiger_reports
 *  This function generates the list of secondary modules in vtiger_reports
 *  and returns the related module as an Array
 */
function getReportRelatedModules($module, $focus) {
	$optionhtml = array();
	if (vtlib_isModuleActive($module)) {
		if (!empty($focus->related_modules[$module])) {
			foreach ($focus->related_modules[$module] as $rel_modules) {
				if (isPermitted($rel_modules, 'index') == 'yes') {
					$optionhtml []= $rel_modules;
				}
			}
		}
	}
	uasort($optionhtml, function ($a, $b) {
		return (strtolower(getTranslatedString($a, $a)) < strtolower(getTranslatedString($b, $b))) ? -1 : 1;
	});
	return $optionhtml;
}

function updateAdvancedCriteria($reportid, $advft_criteria, $advft_criteria_groups) {
	global $adb;

	$adb->pquery('delete from vtiger_relcriteria where queryid=?', array($reportid));
	$adb->pquery('delete from vtiger_relcriteria_grouping where queryid=?', array($reportid));

	if (empty($advft_criteria)) {
		return;
	}
	$irelcriteriasql = 'insert into vtiger_relcriteria(QUERYID,COLUMNINDEX,COLUMNNAME,COMPARATOR,VALUE,GROUPID,COLUMN_CONDITION) values (?,?,?,?,?,?,?)';
	foreach ($advft_criteria as $column_index => $column_condition) {
		if (empty($column_condition)) {
			continue;
		}

		$adv_filter_column = $column_condition['columnname'];
		$adv_filter_comparator = $column_condition['comparator'];
		$adv_filter_value = $column_condition['value'];
		$adv_filter_column_condition = $column_condition['columncondition'];
		$adv_filter_groupid = $column_condition['groupid'];

		$column_info = explode(':', $adv_filter_column);
		$moduleFieldLabel = $column_info[2];
		//$fieldName = $column_info[3];

		list($module, $fieldLabel) = explode('_', $moduleFieldLabel, 2);
		$fieldInfo = getFieldByReportLabel($module, $fieldLabel);
		$fieldType = null;
		if (!empty($fieldInfo)) {
			$field = WebserviceField::fromArray($adb, $fieldInfo);
			$fieldType = $field->getFieldDataType();
		}
		if (($fieldType == 'currency' || $fieldType == 'double') && (substr($adv_filter_value, 0, 1) != '$' && substr($adv_filter_value, -1, 1) != '$')) {
			$flduitype = $fieldInfo['uitype'];
			if ($flduitype == '72' || $flduitype == 9 || $flduitype ==7) {
				$adv_filter_value = CurrencyField::convertToDBFormat($adv_filter_value, null, true);
			} else {
				$adv_filter_value = CurrencyField::convertToDBFormat($adv_filter_value);
			}
		}

		$temp_val = explode(',', $adv_filter_value);
		if (($column_info[4] == 'D' || ($column_info[4] == 'T' && $column_info[1] != 'time_start' && $column_info[1] != 'time_end')
			|| ($column_info[4] == 'DT')) && ($column_info[4] != '' && $adv_filter_value != '' )
		) {
			$val = array();
			for ($x=0; $x<count($temp_val); $x++) {
				if (trim($temp_val[$x]) != '') {
					$date = new DateTimeField(trim($temp_val[$x]));
					if ($column_info[4] == 'D') {
						$val[$x] = DateTimeField::convertToDBFormat(trim($temp_val[$x]));
					} elseif ($column_info[4] == 'DT') {
						$val[$x] = $date->getDBInsertDateTimeValue();
					} else {
						$val[$x] = $date->getDBInsertTimeValue();
					}
				}
			}
			$adv_filter_value = implode(',', $val);
		}

		$adb->pquery(
			$irelcriteriasql,
			array($reportid, $column_index, $adv_filter_column, $adv_filter_comparator, $adv_filter_value, $adv_filter_groupid, $adv_filter_column_condition)
		);

		// Update the condition expression for the group to which the condition column belongs
		$groupConditionExpression = '';
		if (!empty($advft_criteria_groups[$adv_filter_groupid]['conditionexpression'])) {
			$groupConditionExpression = $advft_criteria_groups[$adv_filter_groupid]['conditionexpression'];
		}
		$groupConditionExpression = $groupConditionExpression .' '. $column_index .' '. $adv_filter_column_condition;
		$advft_criteria_groups[$adv_filter_groupid]['conditionexpression'] = $groupConditionExpression;
	}

	$irelcriteriagroupsql = 'insert into vtiger_relcriteria_grouping(GROUPID,QUERYID,GROUP_CONDITION,CONDITION_EXPRESSION) values (?,?,?,?)';
	foreach ($advft_criteria_groups as $group_index => $group_condition_info) {
		if (empty($group_condition_info)) {
			continue;
		}
		if (empty($group_condition_info['conditionexpression'])) {
			continue; // Case when the group doesn't have any column criteria
		}
		$adb->pquery($irelcriteriagroupsql, array($group_index, $reportid, $group_condition_info['groupcondition'], $group_condition_info['conditionexpression']));
	}
}
?>
