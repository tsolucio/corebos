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
require_once 'include/logging.php';
require_once 'include/utils/utils.php';
require_once 'modules/Contacts/Contacts.php';
require_once 'modules/Documents/Documents.php';
require_once 'modules/Emails/Emails.php';
require 'modules/Vtiger/default_module_view.php';

class Potentials extends CRMEntity {
	public $table_name = 'vtiger_potential';
	public $table_index= 'potentialid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = false;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-opportunity', 'class' => 'slds-icon', 'icon'=>'opportunity');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_potentialscf', 'potentialid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity','vtiger_potential','vtiger_potentialscf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_potential' => 'potentialid',
		'vtiger_potentialscf' => 'potentialid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		'Potential'=>array('potential'=>'potentialname'),
		'Related to'=>array('potential'=>'related_to'),
		'Sales Stage'=>array('potential'=>'sales_stage'),
		'Amount'=>array('potential'=>'amount'),
		'Expected Close Date'=>array('potential'=>'closingdate'),
		'Assigned To'=>array('crmentity' =>'smownerid')
	);
	public $list_fields_name = array(
		'Potential'=>'potentialname',
		'Related to'=>'related_to',
		'Sales Stage'=>'sales_stage',
		'Amount'=>'amount',
		'Expected Close Date'=>'closingdate',
		'Assigned To'=>'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field= 'potentialname';

	// For Popup listview and UI type support
	public $search_fields = array(
		'Potential'=>array('potential'=>'potentialname'),
		'Related To'=>array('potential'=>'related_to'),
		'Expected Close Date'=>array('potential'=>'closedate')
	);
	public $search_fields_name = array(
		'Potential'=>'potentialname',
		'Related To'=>'related_to',
		'Expected Close Date'=>'closingdate'
	);

	// For Popup window record selection
	public $popup_fields = array('potentialname');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array('potentialname','amount','closingdate','smownerid','accountname');

	// For Alphabetical search
	public $def_basicsearch_col = 'potentialname';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'potentialname';

	// Required Information for enabling Import feature
	public $required_fields = array();

	// Callback function list during Importing
	//public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'potentialname';
	public $default_sort_order = 'ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'potentialname', 'related_to');

	public $sales_stage = '';

	public function save($module, $fileid = '') {
		global $adb;
		if ($this->mode=='edit') {
			$rs = $adb->pquery('select sales_stage,convertedfromlead from vtiger_potential where potentialid = ?', array($this->id));
			$this->sales_stage = $adb->query_result($rs, 0, 'sales_stage');
			$this->column_fields['convertedfromlead'] = $adb->query_result($rs, 0, 'convertedfromlead');
		}
		parent::save($module, $fileid);
	}

	public function save_module($module) {
		global $adb, $current_user;
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
		if ($this->mode=='edit' && !empty($this->sales_stage) && $this->sales_stage != $this->column_fields['sales_stage'] && $this->column_fields['sales_stage'] != '') {
			$date_var = date("Y-m-d H:i:s");
			$closingDateField = new DateTimeField($this->column_fields['closingdate']);
			$closingdate = (isset($_REQUEST['ajxaction']) && $_REQUEST['ajxaction'] == 'DETAILVIEW' && !empty($this->column_fields['closingdate'])) ?
				$this->column_fields['closingdate'] :
				$closingDateField->getDBInsertDateValue();
			$sql = 'insert into vtiger_potstagehistory (potentialid, amount, stage, probability, expectedrevenue, closedate, lastmodified) values (?,?,?,?,?,?,?)';
			$amountField = empty($this->column_fields['amount']) ? 0 : $this->column_fields['amount'];
			$amountField = new CurrencyField($amountField);
			$amountField = $amountField->getDBInsertedValue($current_user, false);
			$prbField = empty($this->column_fields['probability']) ? 0 : $this->column_fields['probability'];
			$prbField = new CurrencyField($prbField);
			$prbField = $prbField->getDBInsertedValue($current_user, false);
			$forecast_amount = ($amountField==0 || $prbField==0) ? 0 : ($amountField * $prbField / 100);
			$params = array(
				$this->id,
				$amountField,
				decode_html($this->sales_stage),
				$prbField,
				$forecast_amount,
				$adb->formatDate($closingdate, true),
				$adb->formatDate($date_var, true)
			);
			$adb->pquery($sql, $params);
		}
		$relModule = getSalesEntityType($this->column_fields['related_to']);
		if ($relModule == "Contacts") {
			if (isset($this->column_fields['campaignid']) && $this->column_fields['campaignid'] != null && $this->column_fields['campaignid'] !=  0) {
				$res_cnt = $adb->pquery(
					'SELECT COUNT(*) as num FROM vtiger_campaigncontrel WHERE (contactid = ? AND campaignid = ?)',
					array($this->column_fields['related_to'],$this->column_fields['campaignid'])
				);
				$relacionado = $adb->query_result($res_cnt, 0, 'num');
				if ($relacionado == 0) {
					$sql = 'INSERT INTO vtiger_campaigncontrel VALUES(?,?,1)';
					$adb->pquery($sql, array($this->column_fields['campaignid'], $this->column_fields['related_to']));
				}
			}
		} else {
			if (isset($this->column_fields['campaignid']) && $this->column_fields['campaignid'] != null && $this->column_fields['campaignid'] !=  0) {
				$res_acc = $adb->pquery(
					'SELECT COUNT(*) as num FROM vtiger_campaignaccountrel WHERE (accountid = ? AND campaignid = ?)',
					array($this->column_fields['related_to'],$this->column_fields['campaignid'])
				);
				$relacionado = $adb->query_result($res_acc, 0, 'num');
				if ($relacionado == 0) {
					$sql = 'INSERT INTO vtiger_campaignaccountrel VALUES(?,?,1)';
					$adb->pquery($sql, array($this->column_fields['campaignid'], $this->column_fields['related_to']));
				}
			}
		}
	}

	/** Returns a list of the associated contacts */
	public function get_contacts($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $adb,$log, $singlepane_view,$currentModule;
		$log->debug('> get_contacts '.$id);
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once "modules/$related_module/$related_module.php";
		$other = new $related_module();

		if ($singlepane_view == 'true') {
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		} else {
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
		}
		$button = '';

		$search_string = '&fromPotential=true&acc_id=';  // leave it empty for compatibility
		$relrs = $adb->pquery('select related_to from vtiger_potential where potentialid=?', array($id));
		if ($relrs && $adb->num_rows($relrs)==1) {
			$relatedid = $adb->query_result($relrs, 0, 0);
			$reltype = getSalesEntityType($relatedid);
			if ($reltype=='Accounts') {
				$search_string = '&fromPotential=true&acc_id='.$relatedid;
			} elseif ($reltype=='Contacts') {
				$relrs = $adb->pquery('select accountid from vtiger_contactdetails where contactid=?', array($relatedid));
				if ($relrs && $adb->num_rows($relrs)==1) {
					$relatedid = $adb->query_result($relrs, 0, 0);
					if (!empty($relatedid)) {
						$search_string = '&fromPotential=true&acc_id='.$relatedid;
					}
				}
			}
		}

		if ($actions) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module, $related_module).
				"' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule".
				"&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id$search_string','test',".
				"cbPopupWindowSettings);\" value='". getTranslatedString('LBL_SELECT').' '.
				getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$wfs = new VTWorkflowManager($adb);
				$racbr = $wfs->getRACRuleForRecord($currentModule, $id);
				if (!$racbr || $racbr->hasRelatedListPermissionTo('create', $related_module)) {
					$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
					$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
						" value='". getTranslatedString('LBL_ADD_NEW'). " " . $singular_modname ."'>&nbsp;";
				}
			}
		}

		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Contacts');
		$query = 'select case when (vtiger_users.user_name not like "") then vtiger_users.user_name else vtiger_groups.groupname end as user_name,
			vtiger_contactdetails.*,vtiger_potential.potentialid, vtiger_potential.potentialname,
			vtiger_contactscf.*, vtiger_crmentity.crmid, vtiger_crmentity.smownerid,
			vtiger_crmentity.modifiedtime , vtiger_account.accountname from vtiger_potential
			inner join vtiger_contpotentialrel on vtiger_contpotentialrel.potentialid = vtiger_potential.potentialid
			inner join vtiger_contactdetails on vtiger_contpotentialrel.contactid = vtiger_contactdetails.contactid
			inner join vtiger_contactscf on vtiger_contactscf.contactid = vtiger_contactdetails.contactid
			inner join '.$crmEntityTable.' on vtiger_crmentity.crmid = vtiger_contactdetails.contactid
			left join vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid
			left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
			left join vtiger_users on vtiger_crmentity.smownerid=vtiger_users.id
			where vtiger_potential.potentialid = '.$id.' and vtiger_crmentity.deleted=0';

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_contacts');
		return $return_value;
	}

	 /**
	 * Function to get Contact related Products
	 * @param  integer   $id  - contactid
	 * returns related Products record in array format
	 */
	public function get_products($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view, $currentModule;
		$log->debug('> get_products '.$id);
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once "modules/$related_module/$related_module.php";
		$other = new $related_module();

		if ($singlepane_view == 'true') {
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		} else {
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
		}

		$button = '';

		if ($actions) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module, $related_module).
				"' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule".
				"&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id','test',".
				"cbPopupWindowSettings);\" value='". getTranslatedString('LBL_SELECT').' '.
				getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . $singular_modname ."'>&nbsp;";
			}
		}

		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Products');
		$query = "SELECT vtiger_products.*,vtiger_productcf.*,
			vtiger_crmentity.crmid, vtiger_crmentity.smownerid
			FROM vtiger_products
			INNER JOIN vtiger_seproductsrel ON vtiger_products.productid = vtiger_seproductsrel.productid and vtiger_seproductsrel.setype = 'Potentials'
			INNER JOIN $crmEntityTable ON vtiger_crmentity.crmid = vtiger_products.productid
			INNER JOIN vtiger_productcf ON vtiger_productcf.productid = vtiger_products.productid
			INNER JOIN vtiger_potential ON vtiger_potential.potentialid = vtiger_seproductsrel.crmid
			LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted = 0 AND vtiger_potential.potentialid = $id";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_products');
		return $return_value;
	}

	/**	Function used to get the Sales Stage history of the Potential
	 *	@param $id - potentialid
	 *	return $return_data - array with header and the entries in format array('header'=>$header,'entries'=>$entries_list)
	 *	 where as $header and $entries_list are array which contains all the column values of an row
	 */
	public function get_stage_history($id) {
		global $log, $adb, $app_strings, $current_user;
		$log->debug('> get_stage_history '.$id);

		$query = 'select vtiger_potstagehistory.*, vtiger_potential.potentialname
			from vtiger_potstagehistory
			inner join vtiger_potential on vtiger_potential.potentialid = vtiger_potstagehistory.potentialid
			inner join '.$this->crmentityTableAlias.' on vtiger_crmentity.crmid = vtiger_potential.potentialid
			where vtiger_crmentity.deleted = 0 and vtiger_potential.potentialid = ?';
		$result=$adb->pquery($query, array($id));
		$header = array();
		$header[] = $app_strings['LBL_AMOUNT'];
		$header[] = $app_strings['LBL_SALES_STAGE'];
		$header[] = $app_strings['LBL_PROBABILITY'];
		$header[] = $app_strings['LBL_CLOSE_DATE'];
		$header[] = $app_strings['LBL_LAST_MODIFIED'];

		//Getting the field permission for the current user. 1 - Not Accessible, 0 - Accessible
		//Sales Stage, Expected Close Dates are mandatory fields. So no need to do security check to these fields.

		//If field is accessible then getFieldVisibilityPermission function will return 0 else return 1
		$amount_access = (getFieldVisibilityPermission('Potentials', $current_user->id, 'amount') != '0')? 1 : 0;
		$probability_access = (getFieldVisibilityPermission('Potentials', $current_user->id, 'probability') != '0')? 1 : 0;
		$entries_list = array();
		while ($row = $adb->fetch_array($result)) {
			$entries = array();

			$amount = new CurrencyField($row['amount']);
			$entries[] = ($amount_access != 1)? $amount->getDisplayValueWithSymbol($current_user) : 0;
			$entries[] = getTranslatedString($row['stage'], 'Potentials');
			$entries[] = ($probability_access != 1) ? number_format($row['probability'], 2) : 0;
			$entries[] = DateTimeField::convertToUserFormat($row['closedate']);
			$date = new DateTimeField($row['lastmodified']);
			$entries[] = $date->getDisplayDate();

			$entries_list[] = $entries;
		}

		$return_data = array('header'=>$header, 'entries'=>$entries_list, 'navigation'=>array('',''));
		$log->debug('< get_stage_history');
		return $return_data;
	}

	/**
	 * Move the related records of the specified list of id's to the given record.
	 * @param string This module name
	 * @param Array List of Entity Id's from which related records need to be transfered
	 * @param Integer Id of the the Record to which the related records are to be moved
	 */
	public function transferRelatedRecords($module, $transferEntityIds, $entityId) {
		global $adb,$log;
		$log->debug('> transferRelatedRecords', ['module' => $module, 'transferEntityIds' => $transferEntityIds, 'entityId' => $entityId]);
		parent::transferRelatedRecords($module, $transferEntityIds, $entityId);
		$rel_table_arr = array(
			'Contacts'=>'vtiger_contpotentialrel',
			'Products'=>'vtiger_seproductsrel',
			'Attachments'=>'vtiger_seattachmentsrel',
		);
		$tbl_field_arr = array(
			'vtiger_contpotentialrel'=>'contactid',
			'vtiger_seproductsrel'=>'productid',
			'vtiger_seattachmentsrel'=>'attachmentsid',
		);
		$entity_tbl_field_arr = array(
			'vtiger_contpotentialrel'=>'potentialid',
			'vtiger_seproductsrel'=>'crmid',
			'vtiger_seattachmentsrel'=>'crmid',
		);
		foreach ($transferEntityIds as $transferId) {
			foreach ($rel_table_arr as $rel_table) {
				$id_field = $tbl_field_arr[$rel_table];
				$entity_id_field = $entity_tbl_field_arr[$rel_table];
				// IN clause to avoid duplicate entries
				$sel_result = $adb->pquery(
					"select $id_field from $rel_table where $entity_id_field=? and $id_field not in (select $id_field from $rel_table where $entity_id_field=?)",
					array($transferId, $entityId)
				);
				$res_cnt = $adb->num_rows($sel_result);
				if ($res_cnt > 0) {
					for ($i=0; $i<$res_cnt; $i++) {
						$id_field_value = $adb->query_result($sel_result, $i, $id_field);
						$adb->pquery("update $rel_table set $entity_id_field=? where $entity_id_field=? and $id_field=?", array($entityId,$transferId,$id_field_value));
					}
				}
			}
		}
		$log->debug('< transferRelatedRecords');
	}

	/*
	 * Function to get the relation tables for related modules
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	public function setRelationTables($secmodule) {
		$rel_tables = array (
			"Products" => array("vtiger_seproductsrel"=>array("crmid","productid"),"vtiger_potential"=>"potentialid"),
			"Quotes" => array("vtiger_quotes"=>array("potentialid","quoteid"),"vtiger_potential"=>"potentialid"),
			"SalesOrder" => array("vtiger_salesorder"=>array("potentialid","salesorderid"),"vtiger_potential"=>"potentialid"),
			"Documents" => array("vtiger_senotesrel"=>array("crmid","notesid"),"vtiger_potential"=>"potentialid"),
			"Accounts" => array("vtiger_potential"=>array("potentialid","related_to")),
		);
		return isset($rel_tables[$secmodule]) ? $rel_tables[$secmodule] : '';
	}

	// Function to unlink all the dependent entities of the given Entity by Id
	public function unlinkDependencies($module, $id) {
		/*//Backup Activity-Potentials Relation
		global $adb;
		$act_q = "select activityid from vtiger_seactivityrel where crmid = ?";
		$act_res = $adb->pquery($act_q, array($id));
		if ($adb->num_rows($act_res) > 0) {
			for($k=0;$k < $adb->num_rows($act_res);$k++)
			{
				$act_id = $adb->query_result($act_res,$k,"activityid");
				$params = array($id, RB_RECORD_DELETED, 'vtiger_seactivityrel', 'crmid', 'activityid', $act_id);
				$adb->pquery("insert into vtiger_relatedlists_rb values (?,?,?,?,?,?)", $params);
			}
		}
		$sql = 'delete from vtiger_seactivityrel where crmid = ?';
		$adb->pquery($sql, array($id));*/

		parent::unlinkDependencies($module, $id);
	}

	// Function to unlink an entity with given Id from another entity
	public function unlinkRelationship($id, $return_module, $return_id) {
		global $adb;
		if (empty($return_module) || empty($return_id)) {
			return;
		}
		$customRelModules = ['Accounts', 'Campaigns', 'Products', 'Contacts', 'Documents'];
		if (in_array($return_module, $customRelModules)) {
			$data = array();
			$data['sourceModule'] = getSalesEntityType($id);
			$data['sourceRecordId'] = $id;
			$data['destinationModule'] = $return_module;
			$data['destinationRecordId'] = $return_id;
			cbEventHandler::do_action('corebos.entity.link.delete', $data);
		}
		if ($return_module == 'Accounts') {
			$this->trash('Potentials', $id);
		} elseif ($return_module == 'Campaigns') {
			$sql = 'UPDATE vtiger_potential SET campaignid = ? WHERE potentialid = ?';
			$adb->pquery($sql, array(null, $id));
		} elseif ($return_module == 'Products') {
			$sql = 'DELETE FROM vtiger_seproductsrel WHERE crmid=? AND productid=?';
			$adb->pquery($sql, array($id, $return_id));
		} elseif ($return_module == 'Contacts') {
			$sql = 'DELETE FROM vtiger_contpotentialrel WHERE potentialid=? AND contactid=?';
			$adb->pquery($sql, array($id, $return_id));
			// Potential directly linked with Contact (not through Account - vtiger_contpotentialrel)
			$directRelCheck = $adb->pquery('SELECT related_to FROM vtiger_potential WHERE potentialid=? AND related_to=?', array($id, $return_id));
			if ($adb->num_rows($directRelCheck)) {
				$this->trash('Potentials', $id);
			}
		} elseif ($return_module == 'Documents') {
			$sql = 'DELETE FROM vtiger_senotesrel WHERE crmid=? AND notesid=?';
			$adb->pquery($sql, array($id, $return_id));
		} else {
			parent::unlinkRelationship($id, $return_module, $return_id);
		}
		if (in_array($return_module, $customRelModules)) {
			cbEventHandler::do_action('corebos.entity.link.delete.final', $data);
		}
	}

	public function save_related_module($module, $crmid, $with_module, $with_crmids) {
		$adb = PearDatabase::getInstance();
		$with_crmids = (array)$with_crmids;
		foreach ($with_crmids as $with_crmid) {
			if ($with_module == 'Contacts') { //When we select contact from potential related list
				$sql = 'insert ignore into vtiger_contpotentialrel values (?,?)';
				$adb->pquery($sql, array($with_crmid, $crmid));
			} elseif ($with_module == 'Products') {//when we select product from potential related list
				$sql = 'insert ignore into vtiger_seproductsrel values (?,?,?)';
				$adb->pquery($sql, array($crmid, $with_crmid, 'Potentials'));
			} else {
				parent::save_related_module($module, $crmid, $with_module, $with_crmid);
			}
		}
	}

	public function getListButtons($app_strings) {
		$list_buttons = array ();

		if (isPermitted('Potentials', 'Delete', '') == 'yes') {
			$list_buttons['del'] = $app_strings['LBL_MASS_DELETE'];
		}
		if (isPermitted('Potentials', 'EditView', '') == 'yes') {
			$list_buttons['mass_edit'] = $app_strings['LBL_MASS_EDIT'];
		}
		if (isPermitted('Emails', 'CreateView', '') == 'yes') {
			$list_buttons['s_mail'] = $app_strings['LBL_SEND_MAIL_BUTTON'];
		}
		return $list_buttons;
	}

	public function getvtlib_open_popup_window_function($fieldname, $basemodule) {
		if ($basemodule=='SalesOrder' && $fieldname=='potential_id') {
			return 'selectPotential';
		} else {
			return 'vtlib_open_popup_window';
		}
	}
}
?>
