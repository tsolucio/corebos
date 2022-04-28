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

class Vendors extends CRMEntity {
	public $table_name = 'vtiger_vendor';
	public $table_index= 'vendorid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-person-account', 'class' => 'slds-icon', 'icon'=>'person_account');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_vendorcf', 'vendorid');
	// Uncomment the line below to support custom field columns on related lists
	// public $related_tables = array('vtiger_vendorcf'=>array('vendorid','vtiger_vendor', 'vendorid'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity','vtiger_vendor','vtiger_vendorcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity'=>'crmid',
		'vtiger_vendor'=>'vendorid',
		'vtiger_vendorcf'=>'vendorid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Vendor Name'=>array('vendor'=>'vendorname'),
		'Phone'=>array('vendor'=>'phone'),
		'Email'=>array('vendor'=>'email'),
		'Category'=>array('vendor'=>'category')
	);
	public $list_fields_name = array(
		'Vendor Name'=>'vendorname',
		'Phone'=>'phone',
		'Email'=>'email',
		'Category'=>'category'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'vendorname';

	// For Popup listview and UI type support
	public $search_fields = array(
		'Vendor Name'=>array('vendor'=>'vendorname'),
		'Phone'=>array('vendor'=>'phone')
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'Vendor Name'=>'vendorname',
		'Phone'=>'phone'
	);

	// For Popup window record selection
	public $popup_fields = array('vendorname');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array('vendorname','category');

	// For Alphabetical search
	public $def_basicsearch_col = 'vendorname';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'vendorname';

	// Required Information for enabling Import feature
	public $required_fields = array();

	public $default_order_by = 'vendorname';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'vendorname');

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
	}

	/**	function used to get the list of products which are related to the vendor
	 *	@param int $id - vendor id
	 *	@return array - array which will be returned from the function GetRelatedList
	 */
	public function get_products($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view, $currentModule;
		$log->debug('> get_products '.$id);
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		checkFileAccessForInclusion("modules/$related_module/$related_module.php");
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
					"cbPopupWindowSettings);\" value='". getTranslatedString('LBL_SELECT').' '.getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). ' '. $singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.parent_id.value=\"\";' type='submit'".
					" name='button' value='". getTranslatedString('LBL_ADD_NEW'). ' ' . $singular_modname ."'>";
			}
		}
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Products');
		$query = "SELECT vtiger_products.*,vtiger_productcf.*, vtiger_crmentity.crmid, vtiger_crmentity.smownerid,vtiger_vendor.vendorname
			FROM vtiger_products
			INNER JOIN vtiger_vendor ON vtiger_vendor.vendorid = vtiger_products.vendor_id
			INNER JOIN $crmEntityTable ON vtiger_crmentity.crmid = vtiger_products.productid
			INNER JOIN vtiger_productcf ON vtiger_productcf.productid = vtiger_products.productid
			LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted = 0 AND vtiger_vendor.vendorid = $id";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_products');
		return $return_value;
	}

	/**	function used to get the list of purchase orders which are related to the vendor
	 *	@param int $id - vendor id
	 *	@return array - array which will be returned from the function GetRelatedList
	 */
	public function get_purchase_orders($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		return parent::get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions);
	}

	/**	function used to get the list of contacts which are related to the vendor
	 *	@param int $id - vendor id
	 *	@return array - array which will be returned from the function GetRelatedList
	 */
	public function get_contacts($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view, $currentModule;
		$log->debug('> get_contacts '.$id);
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		checkFileAccessForInclusion("modules/$related_module/$related_module.php");
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
				$button .= "<input title='".getTranslatedString('LBL_SELECT').' '.getTranslatedString($related_module, $related_module).
					"' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule".
					"&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id','test',".
					"cbPopupWindowSettings);\" value='".getTranslatedString('LBL_SELECT').' '.getTranslatedString($related_module, $related_module) ."'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .=  "<input type='hidden' name='createmode' value='link' />"
					."<input title='".getTranslatedString('LBL_ADD_NEW').' '.$singular_modname ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW').' '. $singular_modname ."'>&nbsp;";
			}
		}

		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Contacts');
		$query = "SELECT case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name,vtiger_contactdetails.*,
			vtiger_crmentity.crmid, vtiger_crmentity.smownerid,vtiger_vendorcontactrel.vendorid,vtiger_account.accountname
			from vtiger_contactdetails
			inner join $crmEntityTable on vtiger_crmentity.crmid = vtiger_contactdetails.contactid
			inner join vtiger_vendorcontactrel on vtiger_vendorcontactrel.contactid=vtiger_contactdetails.contactid
			left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
			left join vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid
			left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
			where vtiger_crmentity.deleted=0 and vtiger_vendorcontactrel.vendorid = $id";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug('< get_contacts');
		return $return_value;
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
			'Contacts'=>'vtiger_vendorcontactrel',
		);
		$tbl_field_arr = array(
			'vtiger_vendorcontactrel'=>'contactid',
		);
		$entity_tbl_field_arr = array(
			'vtiger_vendorcontactrel'=>'vendorid',
		);
		foreach ($transferEntityIds as $transferId) {
			foreach ($rel_table_arr as $rel_table) {
				$id_field = $tbl_field_arr[$rel_table];
				$entity_id_field = $entity_tbl_field_arr[$rel_table];
				// IN clause to avoid duplicate entries
				$sel_result = $adb->pquery(
					"select $id_field from $rel_table where $entity_id_field=? and $id_field not in (select $id_field from $rel_table where $entity_id_field=?)",
					array($transferId,$entityId)
				);
				$res_cnt = $adb->num_rows($sel_result);
				if ($res_cnt > 0) {
					for ($i=0; $i<$res_cnt; $i++) {
						$id_field_value = $adb->query_result($sel_result, $i, $id_field);
						$adb->pquery(
							"update $rel_table set $entity_id_field=? where $entity_id_field=? and $id_field=?",
							array($entityId,$transferId,$id_field_value)
						);
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
			"Products" =>array("vtiger_products"=>array("vendor_id","productid"),"vtiger_vendor"=>"vendorid"),
			"PurchaseOrder" =>array("vtiger_purchaseorder"=>array("vendorid","purchaseorderid"),"vtiger_vendor"=>"vendorid"),
			"Contacts" =>array("vtiger_vendorcontactrel"=>array("vendorid","contactid"),"vtiger_vendor"=>"vendorid"),
		);
		return isset($rel_tables[$secmodule]) ? $rel_tables[$secmodule] : '';
	}

	// Function to unlink all the dependent entities of the given Entity by Id
	public function unlinkDependencies($module, $id) {
		//Deleting Vendor related PO.
		global $adb;
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('PurchaseOrder');
		$crmEntityTable1 = CRMEntity::getcrmEntityTableAlias('PurchaseOrder', true);
		$po_q = 'SELECT vtiger_crmentity.crmid FROM '.$crmEntityTable.' 
			INNER JOIN vtiger_purchaseorder ON vtiger_crmentity.crmid=vtiger_purchaseorder.purchaseorderid
			INNER JOIN vtiger_vendor ON vtiger_vendor.vendorid=vtiger_purchaseorder.vendorid
			WHERE vtiger_crmentity.deleted=0 AND vtiger_purchaseorder.vendorid=?';
		$po_res = $adb->pquery($po_q, array($id));
		$po_ids_list = array();
		for ($k=0; $k < $adb->num_rows($po_res); $k++) {
			$po_id = $adb->query_result($po_res, $k, "crmid");
			$po_ids_list[] = $po_id;
			$adb->pquery('UPDATE '.$crmEntityTable1.' SET deleted=1 WHERE crmid=?', array($po_id));
			$adb->pquery('UPDATE vtiger_crmobject SET deleted=1 WHERE crmid=?', array($po_id));
		}
		//Backup deleted Vendors related Potentials.
		$params = array($id, RB_RECORD_UPDATED, $crmEntityTable1, 'deleted', 'crmid', implode(",", $po_ids_list));
		$adb->pquery('INSERT INTO vtiger_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);

		//Backup Product-Vendor Relation
		$pro_q = 'SELECT productid FROM vtiger_products WHERE vendor_id=?';
		$pro_res = $adb->pquery($pro_q, array($id));
		if ($adb->num_rows($pro_res) > 0) {
			$pro_ids_list = array();
			for ($k=0; $k < $adb->num_rows($pro_res); $k++) {
				$pro_ids_list[] = $adb->query_result($pro_res, $k, "productid");
			}
			$params = array($id, RB_RECORD_UPDATED, 'vtiger_products', 'vendor_id', 'productid', implode(",", $pro_ids_list));
			$adb->pquery('INSERT INTO vtiger_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
		}
		//Deleting Product-Vendor Relation.
		$pro_q = 'UPDATE vtiger_products SET vendor_id = 0 WHERE vendor_id = ?';
		$adb->pquery($pro_q, array($id));

		/*//Backup Contact-Vendor Relaton
		$con_q = 'SELECT contactid FROM vtiger_vendorcontactrel WHERE vendorid = ?';
		$con_res = $adb->pquery($con_q, array($id));
		if ($adb->num_rows($con_res) > 0) {
			for($k=0;$k < $adb->num_rows($con_res);$k++)
			{
				$con_id = $adb->query_result($con_res,$k,"contactid");
				$params = array($id, RB_RECORD_DELETED, 'vtiger_vendorcontactrel', 'vendorid', 'contactid', $con_id);
				$adb->pquery('INSERT INTO vtiger_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
			}
		}
		//Deleting Contact-Vendor Relaton
		$vc_sql = 'DELETE FROM vtiger_vendorcontactrel WHERE vendorid=?';
		$adb->pquery($vc_sql, array($id));*/

		parent::unlinkDependencies($module, $id);
	}

	// Function to unlink an entity with given Id from another entity
	public function unlinkRelationship($id, $return_module, $return_id) {
		global $adb;
		if (empty($return_module) || empty($return_id)) {
			return;
		}
		if ($return_module == 'Contacts') {
			$data = array();
			$data['sourceModule'] = getSalesEntityType($id);
			$data['sourceRecordId'] = $id;
			$data['destinationModule'] = $return_module;
			$data['destinationRecordId'] = $return_id;
			cbEventHandler::do_action('corebos.entity.link.delete', $data);
			$sql = 'DELETE FROM vtiger_vendorcontactrel WHERE vendorid=? AND contactid=?';
			$adb->pquery($sql, array($id,$return_id));
			cbEventHandler::do_action('corebos.entity.link.delete.final', $data);
		} else {
			parent::unlinkRelationship($id, $return_module, $return_id);
		}
	}

	public function delete_related_module($module, $crmid, $with_module, $with_crmid) {
		global $adb;
		if ($with_module == 'Contacts' || $with_module == 'Products') {
			$with_crmid = (array)$with_crmid;
			$data = array();
			$data['sourceModule'] = $module;
			$data['sourceRecordId'] = $crmid;
			$data['destinationModule'] = $with_module;
			foreach ($with_crmid as $relcrmid) {
				$data['destinationRecordId'] = $relcrmid;
				cbEventHandler::do_action('corebos.entity.link.delete', $data);
				if ($with_module == 'Products') {
					$adb->pquery(
						'update vtiger_products set vendor_id=0 where productid=?',
						array($relcrmid)
					);
				} else {
					$adb->pquery(
						'DELETE FROM vtiger_vendorcontactrel WHERE vendorid=? AND contactid=?',
						array($crmid, $relcrmid)
					);
				}
			}
		} else {
			parent::delete_related_module($module, $crmid, $with_module, $with_crmid);
		}
	}

	public function save_related_module($module, $crmid, $with_module, $with_crmids) {
		$adb = PearDatabase::getInstance();

		$with_crmids = (array)$with_crmids;
		foreach ($with_crmids as $with_crmid) {
			if ($with_module == 'Contacts') {
				$adb->pquery("insert into vtiger_vendorcontactrel values (?,?)", array($crmid, $with_crmid));
			} elseif ($with_module == 'Products') {
				$adb->pquery("update vtiger_products set vendor_id=? where productid=?", array($crmid, $with_crmid));
			} else {
				parent::save_related_module($module, $crmid, $with_module, $with_crmid);
			}
		}
	}
}
?>
