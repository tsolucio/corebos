<?php
/*********************************************************************************
 * The content of this file is subject to the Calendar4You Free license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/

class Calendar4You extends CRMEntity {
	private $basicModules;

	private $profilesActions;
	private $profilesPermissions;

	private $profile_Global_Permission = array();
	private $subordinate_roles_users = array();
	private $current_user_groups = array();

	private $is_admin = false;
	public $tabid = '';
	public $view_all = false;
	public $edit_all = false;
	public $delete_all = false;

	public $View = array();

	private $modulename = 'Calendar4You';
	private $service='GoogleCalendar';

	public $db;
	public $log;

	// constructor of Calendar4You class
	public function __construct() {
		$this->log =LoggerManager::getLogger('account');
		$this->db = PearDatabase::getInstance();

		// array of modules that are allowed for basic version type
		$this->basicModules = array('20', '21', '22', '23');
		// array of action names used in profiles permissions
		$this->profilesActions = array(
			'EDIT'=>'EditView',        // Edit
			'CREATE'=>'CreateView',        // Create
			'DETAIL'=>'DetailView',    // View
			'DELETE'=>'Delete',        // Delete
		);
		$this->profilesPermissions = array();
	}

	/**
	 * Function to remove module tables when uninstalling module
	 */
	private function dropModuleTables() {
		$this->db->query('DROP TABLE IF EXISTS its4you_calendar4you_colors');
		$this->db->query('DROP TABLE IF EXISTS its4you_calendar4you_event_fields');
		$this->db->query('DROP TABLE IF EXISTS its4you_calendar4you_settings');
		$this->db->query('DROP TABLE IF EXISTS its4you_calendar4you_view');
		$this->db->query('DROP TABLE IF EXISTS its4you_googlesync4you_access');
		$this->db->query('DROP TABLE IF EXISTS its4you_googlesync4you_calendar');
		$this->db->query('DROP TABLE IF EXISTS its4you_googlesync4you_dis');
		$this->db->query('DROP TABLE IF EXISTS its4you_googlesync4you_events');
		if (empty($this->tabid)) {
			$this->tabid = getTabid('Calendar4You');
		}
		$this->db->pquery('DELETE FROM vtiger_org_share_action2tab WHERE tabid = ?', array($this->tabid));
		$this->db->pquery('DELETE FROM vtiger_def_org_share WHERE tabid = ?', array($this->tabid));
		$this->db->query("DELETE FROM vtiger_cron_task WHERE name = 'Calendar4You - GoogleSync'");
	}

	public function GetProfilesActions() {
		return $this->profilesActions;
	}

	public function setgoogleaccessparams($userid) {
		$conf=$this->db->query("select * from its4you_googlesync4you_access where userid=$userid and service='$this->service'");
		$admin=$this->db->query("select * from its4you_googlesync4you_access where userid=1 and service='$this->service'");
		if ($this->db->num_rows($conf)==0 && $this->db->num_rows($admin)>0) {
			$google_login=$this->db->query_result($admin, 0, 'google_login');
			$google_apikey=$this->db->query_result($admin, 0, 'google_apikey');
			$google_keyfile=$this->db->query_result($admin, 0, 'google_keyfile');
			$google_clientid=$this->db->query_result($admin, 0, 'google_clientid');
			$google_password=$this->db->query_result($admin, 0, 'google_password');
			$this->db->pquery(
				'INSERT INTO its4you_googlesync4you_access
					(userid,google_login,google_password,google_apikey,google_keyfile,google_clientid,googleinsert,service) VALUES (?,?,?,?,?,?,?,?)',
				array($userid,$google_login,$google_password,$google_apikey,$google_keyfile,$google_clientid,'1',$this->service)
			);
		}
	}

	public function GetDefPermission($userid) {
		require 'user_privileges/user_privileges_'.$userid.'.php';
		require 'user_privileges/sharing_privileges_'.$userid.'.php';

		$this->is_admin = $is_admin;

		if ($this->is_admin==false) {
			if (empty($this->tabid)) {
				$this->tabid = getTabid('Calendar4You');
			}

			$this->profile_Global_Permission = $profileGlobalPermission;
			$this->subordinate_roles_users = $subordinate_roles_users;
			$this->current_user_groups = $current_user_groups;

			if ($this->profile_Global_Permission[1] == '0') {
				$this->view_all = true;
			}

			if ($this->profile_Global_Permission[2] == '0') {
				$this->edit_all = true;
			}

			$dosp = $defaultOrgSharingPermission[$this->tabid];

				//0 - Public: Read Only
			//1 - Public: Read, Create/Edit
			//2 - Public: Read, Create/Edit, Delete
			//3 - private
			if ($dosp == '0' || $dosp == '1' || $dosp == '2') {
				$this->view_all = true;
			}

			if ($dosp == '1' || $dosp == '2') {
				$this->edit_all = true;
			}

			if ($dosp == '2') {
				$this->delete_all = true;
			}
		} else {
			$this->view_all = true;
			$this->edit_all = true;
			$this->delete_all = true;
		}
	}

	//PUBLIC METHODS SECTION
	//ListView data
	public function GetCalendarUsersData($orderby = 'templateid', $dir = 'asc') {
		global $current_user;
		include_once 'modules/Calendar4You/class/color_converter.class.php';
		include_once 'modules/Calendar4You/class/color_harmony.class.php';

		if (count($this->View) > 0) {
			$load_ch = true;
		} else {
			$load_ch = false;
		}

		$colorHarmony = new colorHarmony();

		$sortusersby = GlobalVariable::getVariable('Calendar_sort_users_by', 'first_name, last_name');
		if ($this->view_all) {
			$calshowinactiveusers = GlobalVariable::getVariable('Calendar_Show_Inactive_Users', 1);
			if ($calshowinactiveusers) {
				$sqllshowinactiveusers = '';
			} else {
				$sqllshowinactiveusers = "and status='Active'";
			}
			$query = "SELECT id, user_name, first_name, last_name, status FROM vtiger_users WHERE deleted=0 $sqllshowinactiveusers ORDER BY $sortusersby";
			$params = array();
		} else {
			if (empty($this->tabid)) {
				$this->tabid = getTabid('Calendar4You');
			}

			require 'user_privileges/sharing_privileges_'.$current_user->id.'.php';
			require 'user_privileges/user_privileges_'.$current_user->id.'.php';

			$query = "select status as status, id as id,user_name as user_name,first_name,last_name from vtiger_users where id=?
				union
				select status as status,vtiger_user2role.userid as id,vtiger_users.user_name as user_name,vtiger_users.first_name as first_name,vtiger_users.last_name as last_name
					from vtiger_user2role
					inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid
					inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid
					where vtiger_role.parentrole like ?
				union
				select status as status, shareduserid as id,vtiger_users.user_name as user_name, vtiger_users.first_name as first_name,vtiger_users.last_name as last_name
					from vtiger_tmp_write_user_sharing_per
					inner join vtiger_users on vtiger_users.id=vtiger_tmp_write_user_sharing_per.shareduserid
					where vtiger_tmp_write_user_sharing_per.userid=? and vtiger_tmp_write_user_sharing_per.tabid=?
				union
				select status as status, id as id,user_name as user_name,first_name,last_name
					from vtiger_users
					inner join vtiger_sharedcalendar on vtiger_sharedcalendar.userid = vtiger_users.id
					where sharedid=?
				ORDER BY $sortusersby";
			$params = array($current_user->id, $current_user_parent_role_seq.'::%', $current_user->id, $this->tabid, $current_user->id);
		}
		$result = $this->db->pquery($query, $params);

		$return_data = array();
		$num_rows = $this->db->num_rows($result);

		for ($i=0; $i < $num_rows; $i++) {
			$userid = $this->db->query_result($result, $i, 'id');
			$user_name = $this->db->query_result($result, $i, 'user_name');
			$first_name = $this->db->query_result($result, $i, 'first_name');
			$last_name = $this->db->query_result($result, $i, 'last_name');
			$status = $this->db->query_result($result, $i, 'status');

			if ($this->CheckUserPermissions($userid) === false) {
				continue;
			}

			$User_Colors = getEColors('user', $userid);
			$User_Colors_Palette = $colorHarmony->Monochromatic($User_Colors['bg']);

			if (!$load_ch || !empty($this->View['2'][$userid])) {
				$user_checked = true;
			} else {
				$user_checked = false;
			}

			$user_array = array(
				'id'=> $userid,
				'firstname' => $first_name,
				'lastname' => $last_name,
				'fullname' => trim($first_name.' '.$last_name),
				'color' => $User_Colors_Palette[1],
				'textColor' => $User_Colors['text'],
				'title_color' => $User_Colors_Palette[0],
				'status' => $status,
				'checked'=>$user_checked
			);
			$return_data [$userid]= $user_array;
			unset($User_Colors, $User_Colors_Palette);
		}
		return $return_data;
	}

	public function CheckUserPermissions($userid) {
		return true;
	}

	/**
	 * Handle module events
	 * @param string $modulename
	 * @param type $event_type
	 */
	public function vtlib_handler($modulename, $event_type) {
		if ($modulename == '') {
			$modulename = 'Calendar4You';
		}
		switch ($event_type) {
			case 'module.postinstall':
				$this->actualizeOrgShareAction2Tab();
				$this->actualizeDocRel();
				$this->actualizeRegister();
				break;
			case 'module.preupdate':
				break;
			case 'module.postupdate':
				$this->actualizeOrgShareAction2Tab();
				$this->actualizeDocRel();
				$this->actualizeRegister();
				break;
			case 'module.disabled':
				$this->deleteRegister();
				break;
			case 'module.enabled':
				$this->actualizeRegister();
				break;
			case 'module.preuninstall':
				$this->deleteRegister();
				$this->dropModuleTables();
				break;
		}
	}

	public function actualizeOrgShareAction2Tab() {
		global $adb;

		if (empty($this->tabid)) {
			$this->tabid = getTabid('Calendar4You');
		}

		$result1 = $adb->query(
			"SELECT share_action_id
				FROM vtiger_org_share_action_mapping
				WHERE share_action_name in ('Public: Read Only', 'Public: Read, Create/Edit', 'Public: Read, Create/Edit, Delete', 'Private')"
		);

		$sql2 = 'SELECT tabid FROM vtiger_org_share_action2tab WHERE share_action_id = ? AND tabid = ? limit 1';
		for ($index = 0; $index < $adb->num_rows($result1); ++$index) {
			$actionid = $adb->query_result($result1, $index, 'share_action_id');
			$result2 = $adb->pquery($sql2, array($actionid,$this->tabid));
			$num_rows2 = $adb->num_rows($result2);
			if ($num_rows2 == 0) {
				$adb->pquery('INSERT INTO vtiger_org_share_action2tab(share_action_id,tabid) VALUES(?,?)', array($actionid, $this->tabid));
			}
		}
	}

	public function actualizeRegister() {
		$moduleInstance = Vtiger_Module::getInstance('Calendar4You');
		Vtiger_Event::register($moduleInstance, 'vtiger.entity.aftersave', 'GoogleSync4YouHandler', 'modules/Calendar4You/GoogleSync4YouHandler.php');
		Vtiger_Event::register($moduleInstance, 'vtiger.entity.beforedelete', 'GoogleSync4YouHandler', 'modules/Calendar4You/GoogleSync4YouHandler.php');
	}

	public function deleteRegister() {
		global $adb;
		$adb->pquery('DELETE FROM vtiger_eventhandlers WHERE handler_path = ?', array('modules/Calendar4You/GoogleSync4YouHandler.php'));
	}

	public function actualizeDocRel() {
		global $adb;
		$e_tabid = getTabid('Events');
		$c_tabid = getTabid('Calendar4You');
		$d_tabid = getTabid('Documents');

		$s_sql = 'SELECT relation_id FROM vtiger_relatedlists WHERE tabid = ? AND related_tabid = ? AND name = ? AND label = ?';
		$d_sql = 'DELETE FROM vtiger_relatedlists WHERE tabid = ? AND related_tabid = ? AND name = ? AND label = ?';
		$i_sql = 'INSERT INTO vtiger_relatedlists (relation_id,tabid,related_tabid,name,sequence,label,presence,actions) VALUES (?,?,?,?,?,?,?,?)';

		$result1 = $adb->pquery($s_sql, array($e_tabid,$d_tabid,'get_attachments','Documents'));
		$num_rows1 = $adb->num_rows($result1);

		if ($num_rows1 != 1) {
			if ($num_rows1 > 1) {
				$adb->pquery($d_sql, array($e_tabid,$d_tabid,'get_attachments','Documents'));
			}

			$relation_id1 = $adb->getUniqueID('vtiger_relatedlists');
			$adb->pquery($i_sql, array($relation_id1,$e_tabid,$d_tabid,'get_attachments','1','Documents','0','ADD,SELECT'));
		}

		$result2 = $adb->pquery($s_sql, array($c_tabid,$d_tabid,'get_attachments','Documents'));
		$num_rows2 = $adb->num_rows($result2);

		if ($num_rows2 != 1) {
			if ($num_rows2 > 1) {
				$adb->pquery($d_sql, array($c_tabid,$d_tabid,'get_attachments','Documents'));
			}

			$relation_id2 = $adb->getUniqueID('vtiger_relatedlists');
			$adb->pquery($i_sql, array($relation_id2,$c_tabid,$d_tabid,'get_attachments','1','Documents','0','ADD,SELECT'));
		}
	}

	public function getRandomColorHex($max_r = 255, $max_g = 255, $max_b = 255) {
		return sprintf('#%02X%02X%02X', rand(50, $max_r), rand(50, $max_g), rand(50, $max_b));
	}

	public function getSettings() {
		global $adb,$current_user;

		$Settings = array();
		$Settings['hour_format'] = $current_user->hour_format;
		$Settings['start_hour'] = round($current_user->start_hour).':00:00';
		$Settings['end_hour'] = round(24).':00:00';
		$Settings['dayoftheweek'] = 'Sunday';
		$Settings['number_dayoftheweek'] = '0';
		$Settings['show_weekends'] = 'true';
		$Settings['user_view'] = 'me';

		$result1 = $adb->pquery('SELECT user_view, dayoftheweek, show_weekends FROM its4you_calendar4you_settings WHERE userid=?', array($current_user->id));
		if ($adb->num_rows($result1) > 0) {
			$user_view = $adb->query_result($result1, 0, 'user_view');
			if ($user_view != '') {
				$Settings['user_view'] = $user_view;
			}

			$Settings['dayoftheweek'] = $adb->query_result($result1, 0, 'dayoftheweek');
			$Settings['number_dayoftheweek'] = $this->getDayNumber($Settings['dayoftheweek']);
			$show_weekends = $adb->query_result($result1, 0, 'show_weekends');

			if ($show_weekends == '0') {
				$Settings['show_weekends'] = 'false';
			}
		}
		return $Settings;
	}

	public function getEventColors() {
		global $adb,$current_user;
		$result1 = $adb->pquery('SELECT mode, entity, type, color FROM its4you_calendar4you_colors WHERE userid=?', array($current_user->id));
		$Colors = array();
		if ($adb->num_rows($result1) > 0) {
			while ($row = $adb->fetchByAssoc($result1)) {
				$Colors[$row['mode']][$row['entity']][$row['type']] = $row['color'];
			}
		}
		return $Colors;
	}

	public function getEventColor($mode, $entity) {
		global $adb,$current_user;
		$result1 = $adb->pquery('SELECT type, color FROM its4you_calendar4you_colors WHERE userid=? AND mode=? AND entity=?', array($current_user->id, $mode, $entity));
		$Colors = array();
		if ($adb->num_rows($result1) > 0) {
			while ($row = $adb->fetchByAssoc($result1)) {
				$Colors[$row['type']] = $row['color'];
			}
		}
		return $Colors;
	}

	public function getDayNumber($day) {
		switch ($day) {
			case 'Sunday':
				$dn = '0';
				break;
			case 'Monday':
				$dn = '1';
				break;
			case 'Tuesday':
				$dn = '2';
				break;
			case 'Wednesday':
				$dn = '3';
				break;
			case 'Thursday':
				$dn = '4';
				break;
			case 'Friday':
				$dn = '5';
				break;
			case 'Saturday':
				$dn = '6';
				break;
		}
		return $dn;
	}

	//Method for checking the permissions, whether the user has privilegies to perform specific action on PDF Maker.
	public function CheckPermissions($actionKey, $record_id = '') {
		global $current_user;

		if (empty($this->view_all)) {
			$this->GetDefPermission($current_user->id);
		}

		if ($this->is_admin) {
			return true;
		}

		if ($this->profile_Global_Permission[1] == '0' && $actionKey == 'DETAIL') {
			return true;
		} elseif ($this->profile_Global_Permission[2] == '0' && ($actionKey == 'EDIT' || $actionKey == 'CREATE')) {
			return true;
		} else {
			//$profileid = fetchUserProfileId($current_user->id);
			if (isset($this->profilesActions[$actionKey])) {
				//$actionid = getActionid($this->profilesActions[$actionKey]);
				$permissions = isPermitted('Calendar', $this->profilesActions[$actionKey]);

				if ($permissions == 'yes') {
					if (($this->edit_all && ($actionKey == 'DETAIL' || $actionKey == 'EDIT' || $actionKey == 'CREATE')) || ($this->delete_all && $actionKey=='DELETE')) {
						return true;
					} elseif ($record_id != '') {
						$recOwnType='';
						$recOwnId='';
						$recordOwnerArr=getRecordOwnerId($record_id);
						foreach ($recordOwnerArr as $type => $id) {
							$recOwnType=$type;
							$recOwnId=$id;
						}

						if ($recOwnType == 'Users') {
							if ($current_user->id == $recOwnId) {
								return true;
							}
							//Checking if the Record Owner is the Subordinate User
							foreach ($this->subordinate_roles_users as $userids) {
								if (in_array($recOwnId, $userids)) {
									return true;
								}
							}

							$permission = isCalendarPermittedBySharing($record_id);

							if ($permission == 'yes' && $actionKey == 'DETAIL') {
								return true;
							}
						} elseif ($recOwnType == 'Groups') {
							//Checking if the record owner is the current user's group
							if (in_array($recOwnId, $this->current_user_groups)) {
								return true;
							}
						}

						if ($actionKey == 'DETAIL') {
							$ui = $this->isUserCalendarPermittedByInviti($record_id);
							if ($ui) {
								return true;
							}
						}
					} else {
						return true;
					}
				}
			}
		}
		return false;
	}

	public function isUserCalendarPermittedByInviti($recordId) {
		global $adb, $current_user;
		$result = $adb->pquery('select activityid from vtiger_invitees where activityid=? and inviteeid=? limit 1', array($recordId, $current_user->id));
		return ($adb->num_rows($result) > 0);
	}

	public function getActStatusFieldValues($fieldname, $tablename) {
		global $adb, $current_user, $default_charset;
		require 'user_privileges/user_privileges_'.$current_user->id.'.php';

		if (count($this->View) > 0) {
			$load_ch = true;
		} else {
			$load_ch = false;
		}

		$type ='';

		if ($fieldname == 'eventstatus') {
			$type = '3';
		} elseif ($fieldname == 'taskstatus') {
			$type = '4';
		} elseif ($fieldname == 'taskpriority') {
			$type = '5';
		}

		$Data = array();

		if ($is_admin) {
			$q = 'select * from '.$tablename;
		} else {
			$roleid=$current_user->roleid;
			$subrole = getRoleSubordinates($roleid);
			if (count($subrole)> 0) {
				$roleids = $subrole;
				$roleids[] = $roleid;
			} else {
				$roleids = $roleid;
			}

			if (count($roleids) > 1) {
				$q="select $fieldname, picklist_valueid
					from $tablename
					inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = $tablename.picklist_valueid
					where roleid in (\"". implode($roleids, "\",\"") ."\") and picklistid in (select picklistid from $tablename)
					order by sortid asc";
			} else {
				$q="select $fieldname, picklist_valueid
					from $tablename
					inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = $tablename.picklist_valueid
					where roleid ='".$roleid."' and picklistid in (select picklistid from $tablename)
					order by sortid asc";
			}
		}

		$Res = $adb->query($q);
		$noofrows = $adb->num_rows($Res);
		$previousValue = '';
		for ($i = 0; $i < $noofrows; $i++) {
			$value = $adb->query_result($Res, $i, $fieldname);
			if ($previousValue == $value) {
				continue;
			}
			$previousValue = $value;
			$value = html_entity_decode($value, ENT_QUOTES, $default_charset);
			$checked = true;
			$valueid = $adb->query_result($Res, $i, 'picklist_valueid');
			$label = getTranslatedString($value, 'Calendar');
			if ($type != '' || $load_ch) {
				if (!empty($this->View[$type][$valueid])) {
					$checked = false;
				}
			}
			$Data[$value] = array('id'=>$valueid,'value'=>$value,'label'=>$label,'checked'=>$checked);
		}
		return $Data;
	}

	//Function Call for Related List -- Start
	/**
	 * Function to get Activity related Contacts
	 * @param  integer   $id      - activityid
	 * returns related Contacts record in array format
	 */
	public function get_contacts($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $currentModule;
		$log->debug('Entering get_contacts('.$id.') method ...');
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once "modules/$related_module/$related_module.php";
		$other = new $related_module();

		$parenttab = getParentTab();

		$returnset = '&return_module='.$this_module.'&return_action=DetailView&activity_mode=Events&return_id='.$id;

		$search_string = '';
		$button = '';

		if ($actions) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT').' '. getTranslatedString($related_module, $related_module)
					."' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule"
					."&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab$search_string','test',"
					."'width=640,height=602,resizable=0,scrollbars=0');\" value='".getTranslatedString('LBL_SELECT').' '
					.getTranslatedString($related_module, $related_module)."'>&nbsp;";
			}
		}

		$query = 'select vtiger_users.user_name,vtiger_contactdetails.accountid,vtiger_contactdetails.contactid, vtiger_contactdetails.firstname,
				vtiger_contactdetails.lastname, vtiger_contactdetails.department, vtiger_contactdetails.title, vtiger_contactdetails.email,
				vtiger_contactdetails.phone, vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime
			from vtiger_contactdetails
			inner join vtiger_cntactivityrel on vtiger_cntactivityrel.contactid=vtiger_contactdetails.contactid
			inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid
			left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
			left join vtiger_groups on vtiger_groups.groupid = vtiger_crmentity.smownerid
			where vtiger_cntactivityrel.activityid='.$id.' and vtiger_crmentity.deleted=0';
		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);
		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;
		$log->debug('Exiting get_contacts');
		return $return_value;
	}

	/**
	 * Function to get Activity related Users
	 * @param  integer   $id      - activityid
	 * returns related Users record in array format
	 */
	public function get_users($id) {
		global $log;
		$log->debug('Entering get_contacts('.$id.') method ...');

		$focus = new Users();

		$button = '<input title="Change" accessKey="" tabindex="2" type="button" class="crmbutton small edit" value="'.getTranslatedString('LBL_SELECT_USER_BUTTON_LABEL')
			.'" name="button" onclick=\'return window.open("index.php?module=Users&return_module=Calendar&return_action={$return_modname}&activity_mode=Events'
			.'&action=Popup&popuptype=detailview&form=EditView&form_submit=true&select=enable&return_id='.$id.'&recordid='.$id
			.'","test","width=640,height=525,resizable=0,scrollbars=0")\';>';

		$returnset = '&return_module=Calendar&return_action=CallRelatedList&return_id='.$id;

		$query = 'SELECT vtiger_users.id, vtiger_users.first_name,vtiger_users.last_name, vtiger_users.user_name, vtiger_users.email1, vtiger_users.email2,
				vtiger_users.status, vtiger_users.is_admin, vtiger_user2role.roleid, vtiger_users.secondaryemail, vtiger_users.phone_home, vtiger_users.phone_work,
				vtiger_users.phone_mobile, vtiger_users.phone_other, vtiger_users.phone_fax,vtiger_activity.date_start,vtiger_activity.due_date,
				vtiger_activity.time_start,vtiger_activity.duration_hours,vtiger_activity.duration_minutes
			from vtiger_users
			inner join vtiger_salesmanactivityrel on vtiger_salesmanactivityrel.smid=vtiger_users.id
			inner join vtiger_activity on vtiger_activity.activityid=vtiger_salesmanactivityrel.activityid
			inner join vtiger_user2role on vtiger_user2role.userid=vtiger_users.id
			where vtiger_activity.activityid='.$id;

		$return_data = GetRelatedList('Calendar', 'Users', $focus, $query, $button, $returnset);

		if ($return_data == null) {
			$return_data = array();
		}
		$return_data['CUSTOM_BUTTON'] = $button;

		$log->debug('Exiting get_users');
		return $return_data;
	}

	public function SaveView($Type_Ids, $Users_Ids, $all_users, $Load_Event_Status, $Load_Modules, $Load_Task_Priority) {
		global $adb,$current_user;
		$Save = array('1' => $Type_Ids, '2' => $Users_Ids, '3' => $Load_Event_Status, '4' => $Load_Modules, '5' => $Load_Task_Priority);
		$block_status = json_decode($_REQUEST['block_status'], true);
		$Save['6'] = array(vtlib_purify($block_status['event_type']));
		$Save['7'] = array(vtlib_purify($block_status['module_type']));
		$Save['8'] = array(vtlib_purify($block_status['et_status']));
		$Save['9'] = array(vtlib_purify($block_status['task_priority']));
		$d_sql = 'DELETE FROM its4you_calendar4you_view WHERE userid = ? AND type = ?';
		$i_sql = 'INSERT its4you_calendar4you_view (userid,type,parent) VALUES (?,?,?)';
		foreach ($Save as $type => $Save_Array) {
			if (($type == 2 && $all_users) || $type != 2) {
				$adb->pquery($d_sql, array($current_user->id,$type));
				if (count($Save_Array) > 0) {
					foreach ($Save_Array as $parent) {
						if ($parent != '') {
							$adb->pquery($i_sql, array($current_user->id, $type, $parent));
						}
					}
				}
			}
		}
	}

	public function GetView() {
		global $adb,$current_user;
		$result = $adb->pquery('SELECT type, parent FROM its4you_calendar4you_view WHERE userid = ?', array($current_user->id));
		if ($adb->num_rows($result) > 0) {
			while ($row = $adb->fetchByAssoc($result)) {
				if ($row['type']>5 && $row['type']<10) {
					$this->View[$row['type']] = $row['parent'];
				} else {
					$this->View[$row['type']][$row['parent']] = true;
				}
			}
		}
		return $this->View;
	}
}
?>
