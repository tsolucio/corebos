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

class DiscountLine extends CRMEntity {
	public $table_name = 'vtiger_discountline';
	public $table_index= 'discountlineid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-account', 'class' => 'slds-icon', 'icon'=>'sales_path');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_discountlinecf', 'discountlineid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_discountline', 'vtiger_discountlinecf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_discountline'   => 'discountlineid',
		'vtiger_discountlinecf' => 'discountlineid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'discountline_no'=> array('discountline' => 'discountline_no'),
		'Return Value' => array('discountline' => 'returnvalue'),
		'Line'=> array('discountline' => 'productcategory'),
		'Discount'=> array('discountline' => 'discount'),
		'Active'=> array('discountline' => 'activestatus'),
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'discountline_no'=> 'discountline_no',
		'Return Value' => 'returnvalue',
		'Line'=> 'productcategory',
		'Discount'=>'discount',
		'Active'=> 'activestatus',
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'discountline_no';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'discountline_no'=> array('discountline' => 'discountline_no'),
		'Return Value' => array('discountline' => 'returnvalue'),
		'Line'=> array('discountline' => 'productcategory'),
		'Discount'=> array('discountline' => 'discount'),
		'Active'=> array('discountline' => 'activestatus'),
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'discountline_no'=> 'discountline_no',
		'Return Value' => 'returnvalue',
		'Line'=> 'productcategory',
		'Discount'=>'discount',
		'Active'=> 'activestatus',
	);

	// For Popup window record selection
	public $popup_fields = array('discountline_no');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'discountline_no';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'discountline_no';

	// Required Information for enabling Import feature
	public $required_fields = array('discountline_no'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'discountline_no';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('dlcategory','discount');

	private static $validationinfo = array();

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param string Module name
	 * @param string Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		global $adb;
		include_once 'vtlib/Vtiger/Module.php';
		if ($event_type == 'module.postinstall') {
			// Handle post installation actions
			$this->setModuleSeqNumber('configure', $modulename, 'PRCCALC-', '0000001');
			$modAccounts = Vtiger_Module::getInstance('Accounts');
			$modAccounts->setRelatedList(Vtiger_Module::getInstance('Discountline'), 'Price Modification', array('ADD','SELECT'));
			$modServices = Vtiger_Module::getInstance('Services');
			$modServices->setRelatedList(Vtiger_Module::getInstance('Discountline'), 'Price Modification', array('ADD','SELECT'));
			$modProducts = Vtiger_Module::getInstance('Products');
			$modProducts->setRelatedList(Vtiger_Module::getInstance('Discountline'), 'Price Modification', array('ADD','SELECT'));
			$modContacts = Vtiger_Module::getInstance('Contacts');
			$modContacts->setRelatedList(Vtiger_Module::getInstance('Discountline'), 'Price Modification', array('ADD','SELECT'));
			$ev = new VTEventsManager($adb);
			$ev->registerHandler('corebos.entity.link.after', 'modules/DiscountLine/CheckDuplicateRelatedRecords.php', 'CheckDuplicateRelatedRecords');
			$ev->registerHandler('corebos.filter.inventory.getprice', 'modules/DiscountLine/GetPriceHandler.php', 'PriceCalculationGetPriceEventHandler');
		} elseif ($event_type == 'module.disabled') {
			// Handle actions when this module is disabled.
			$ev = new VTEventsManager($adb);
			$ev->unregisterHandler('CheckDuplicateRelatedRecords');
			$ev->unregisterHandler('PriceCalculationGetPriceEventHandler');
		} elseif ($event_type == 'module.enabled') {
			// Handle actions when this module is enabled.
			$ev = new VTEventsManager($adb);
			$ev->registerHandler('corebos.entity.link.after', 'modules/DiscountLine/CheckDuplicateRelatedRecords.php', 'CheckDuplicateRelatedRecords');
			$ev->registerHandler('corebos.filter.inventory.getprice', 'modules/DiscountLine/GetPriceHandler.php', 'PriceCalculationGetPriceEventHandler');
		} elseif ($event_type == 'module.preuninstall') {
			// Handle actions when this module is about to be deleted.
			$ev = new VTEventsManager($adb);
			$ev->unregisterHandler('CheckDuplicateRelatedRecords');
			$ev->unregisterHandler('PriceCalculationGetPriceEventHandler');
		} elseif ($event_type == 'module.preupdate') {
			// Handle actions before this module is updated.
		} elseif ($event_type == 'module.postupdate') {
			// Handle actions after this module is updated.
		}
	}

	/**
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	public function save_related_module($module, $crmid, $with_module, $with_crmids) {
		global $adb;
		if ($with_module == 'Products' || $with_module == 'Services' || $with_module == 'Accounts' || $with_module == 'Contacts') {
			/* algorithm:
				Calculate the set of DiscountLine Records that are related to $with_crmid, call it DLW
				if DLW is empty => we can relate this one as there is no other DL related
				else:
					calculate the set of Products/Accounts on this DiscountLine, call it DLP
					calculate the set of Products/Accounts related to all the records in DLW, call it DLWP
					if the intersection of the DLP and DLWP is empty we can relate, else we cannot
			*/
			if ($with_module == 'Products' || $with_module == 'Services') {
				$otherModules = array('Accounts', 'Contacts');
			} else {
				$otherModules = array('Products', 'Services');
			}
			$with_crmids = (array)$with_crmids;
			foreach ($with_crmids as $with_crmid) {
				$checkpresence = $adb->pquery(
					'SELECT 1 FROM vtiger_crmentityrel WHERE (crmid=? AND relcrmid=?) OR (crmid=? AND relcrmid=?)',
					array($crmid, $with_crmid, $with_crmid, $crmid)
				);
				// Relation already exists? No need to add again
				if ($checkpresence && $adb->num_rows($checkpresence)) {
					continue;
				}
				$dlwrs = $adb->pquery(
					'SELECT * FROM vtiger_crmentityrel WHERE (crmid=? AND relmodule=?) OR (relcrmid=? AND module=?)',
					array($with_crmid, 'DiscountLine', $with_crmid, 'DiscountLine')
				);
				$dlw = array();
				while ($rels = $adb->fetch_array($dlwrs)) {
					$dlw[] = ($rels['module']=='DiscountLine' ? $rels['crmid'] : $rels['relcrmid']);
				}
				// if DLW is empty => we can relate
				if (empty($dlw)) {
					$adb->pquery('INSERT INTO vtiger_crmentityrel VALUES(?,?,?,?)', array($crmid, $module, $with_crmid, $with_module));
					continue;
				}
				$dlwprs = $adb->pquery(
					'SELECT * FROM vtiger_crmentityrel
						WHERE (crmid in ('.generateQuestionMarks($dlw).') AND (relmodule=? OR relmodule=?))
							OR (relcrmid in ('.generateQuestionMarks($dlw).') AND (module=? OR module=?))',
					array($dlw, $otherModules, $dlw, $otherModules)
				);
				$dlwp = array();
				while ($rels = $adb->fetch_array($dlwprs)) {
					$dlwp[] = ($rels['module']=='DiscountLine' ? $rels['relcrmid'] : $rels['crmid']);
				}
				$dlwp = array_unique($dlwp);
				$dlprs = $adb->pquery(
					'SELECT * FROM vtiger_crmentityrel
						WHERE (crmid=? AND (relmodule=? OR relmodule=?)) OR (relcrmid=? AND (module=? OR module=?))',
					array($crmid, $otherModules, $crmid, $otherModules)
				);
				$dlp = array();
				while ($rels = $adb->fetch_array($dlprs)) {
					$dlp[] = ($rels['module']=='DiscountLine' ? $rels['relcrmid'] : $rels['crmid']);
				}
				$dlp = array_unique($dlp);
				// if DLWP intersect with DLP is empty => we can relate
				$intersect = array_intersect($dlwp, $dlp);
				if (empty($intersect)) {
					$adb->pquery('INSERT INTO vtiger_crmentityrel VALUES(?,?,?,?)', array($crmid, $module, $with_crmid, $with_module));
					continue;
				}
				coreBOS_Settings::setSetting('RLERRORMESSAGE', getTranslatedString('RELATE_PERMISSION', 'DiscountLine'));
				coreBOS_Settings::setSetting('RLERRORMESSAGECLASS', 'warning');
			}
		} else {
			parent::save_related_module($module, $crmid, $with_module, $with_crmid);
		}
	}

	public static function getDiscount($productid, $accountid, $contactid, $moduleid) {
		global $adb, $current_user;
		self::$validationinfo = array();
		$context = array(
			'record_id' => $productid,
			'accountid' => $accountid,
			'contactid' => $contactid,
			'moduleid' => $moduleid,
		);
		$searchModule = (1 == GlobalVariable::getVariable('Application_B2B', '1')) ? 'Accounts' : 'Contacts';
		if ($searchModule=='Accounts') {
			$search_in = $accountid;
		} else {
			$search_in = $contactid;
		}
		$pdotype = getSalesEntityType($productid);
		self::$validationinfo[] = "search for discount $productid ($pdotype), $search_in ($searchModule), $moduleid (module ID)";
		$qg = new QueryGenerator($pdotype, $current_user);
		if ($pdotype=='Products') {
			$qg->setFields(array('productcategory'));
		} else {
			$qg->setFields(array('servicecategory'));
		}
		$qg->addCondition('id', $productid, 'e');
		$query = $qg->getQuery();
		$pdrs = $adb->query($query);
		if ($pdrs && $adb->num_rows($pdrs)>0) {
			if ($pdotype=='Products') {
				$category = $pdrs->fields['productcategory'];
			} else {
				$category = $pdrs->fields['servicecategory'];
			}
			self::$validationinfo[] = 'Product Category: '.$category;
			if (empty($category) || $category=='--None--') { // we set it to an inexistent value
				$category = '-1';
				self::$validationinfo[] = 'Product Category: '.$category;
			}
			$basequery = 'SELECT distinct discountlineid
				FROM vtiger_discountline
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = discountlineid ';
			$basewhere = ' WHERE deleted=0 AND activestatus=1 AND vtiger_discountline.dlcategory=? ';
			//// category and client
			$query = $basequery
				.'INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.crmid=discountlineid OR vtiger_crmentityrel.relcrmid=discountlineid) '
				.$basewhere.' AND (vtiger_crmentityrel.crmid=? OR vtiger_crmentityrel.relcrmid=?)';
			$params = array($category, $search_in, $search_in);
			$rs = $adb->pquery($query, $params);
			if ($rs && $adb->num_rows($rs)>0) {
				self::$validationinfo[] = 'Found category and client';
				return self::getDiscountValue($rs->fields['discountlineid'], $context);
			}
			//// category and NO client NOR product
			$query = $basequery.$basewhere
				.'AND NOT EXISTS
					(select 1 from vtiger_crmentityrel
						where vtiger_crmentityrel.crmid=discountlineid OR vtiger_crmentityrel.relcrmid=discountlineid)';
			$params = array($category);
			$rs = $adb->pquery($query, $params);
			if ($rs && $adb->num_rows($rs)>0) {
				self::$validationinfo[] = 'Found category and NO client NOR product';
				return self::getDiscountValue($rs->fields['discountlineid'], $context);
			}
			//// NO category, client and product
			$query = $basequery
				.'INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.crmid=discountlineid OR vtiger_crmentityrel.relcrmid=discountlineid)'
				.$basewhere
				.'AND EXISTS
					(select 1 from vtiger_crmentityrel
						where (vtiger_crmentityrel.crmid=discountlineid AND vtiger_crmentityrel.relcrmid=?)
							OR (vtiger_crmentityrel.crmid=? AND vtiger_crmentityrel.relcrmid=discountlineid))
				AND EXISTS
					(select 1 from vtiger_crmentityrel
						where (vtiger_crmentityrel.crmid=discountlineid AND vtiger_crmentityrel.relcrmid=?)
							OR (vtiger_crmentityrel.crmid=? AND vtiger_crmentityrel.relcrmid=discountlineid))';
			$params = array('--None--', $search_in, $search_in, $productid, $productid);
			$rs = $adb->pquery($query, $params);
			if ($rs && $adb->num_rows($rs)>0) {
				self::$validationinfo[] = 'Found NO category, client and product';
				return self::getDiscountValue($rs->fields['discountlineid'], $context);
			}
			//// NO category, client and NO product
			$query = $basequery
				.'INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.crmid=discountlineid OR vtiger_crmentityrel.relcrmid=discountlineid)'
				.$basewhere.'AND (vtiger_crmentityrel.crmid=? OR vtiger_crmentityrel.relcrmid=?)
					AND NOT EXISTS
						(select 1 from vtiger_crmentityrel
							where (vtiger_crmentityrel.crmid=discountlineid OR vtiger_crmentityrel.relcrmid=discountlineid)
								AND (vtiger_crmentityrel.module=? OR vtiger_crmentityrel.relmodule=? OR vtiger_crmentityrel.module=? OR vtiger_crmentityrel.relmodule=?))';
			$params = array('--None--', $search_in, $search_in, 'Products', 'Products', 'Services', 'Services');
			$rs = $adb->pquery($query, $params);
			if ($rs && $adb->num_rows($rs)>0) {
				self::$validationinfo[] = 'Found NO category, client and NO product';
				return self::getDiscountValue($rs->fields['discountlineid'], $context);
			}
			//// NO category, NO client and product
			$query = $basequery
				.'INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.crmid=discountlineid OR vtiger_crmentityrel.relcrmid=discountlineid)'
				.$basewhere.'AND (vtiger_crmentityrel.crmid=? OR vtiger_crmentityrel.relcrmid=?)
				AND NOT EXISTS
				(select 1 from vtiger_crmentityrel
					where (vtiger_crmentityrel.crmid=discountlineid OR vtiger_crmentityrel.relcrmid=discountlineid)
						AND (vtiger_crmentityrel.module=? OR vtiger_crmentityrel.relmodule=?))';
			$params = array('--None--', $productid, $productid, $searchModule, $searchModule);
			$rs = $adb->pquery($query, $params);
			if ($rs && $adb->num_rows($rs)>0) {
				self::$validationinfo[] = 'Found NO category, NO client and product';
				return self::getDiscountValue($rs->fields['discountlineid'], $context);
			}
			//// NO category, NO client and NO product
			$query = $basequery.$basewhere
				.'AND NOT EXISTS
					(select 1 from vtiger_crmentityrel
						where vtiger_crmentityrel.crmid=discountlineid OR vtiger_crmentityrel.relcrmid=discountlineid)';
			$params = array('--None--');
			$rs = $adb->pquery($query, $params);
			if ($rs && $adb->num_rows($rs)>0) {
				self::$validationinfo[] = 'Found NO category, NO client and NO product';
				return self::getDiscountValue($rs->fields['discountlineid'], $context);
			}
			return false;
		} else {
			self::$validationinfo[] = 'Product NOT FOUND';
			return false;
		}
	}

	private static function getDiscountValue($dtolineid, $context) {
		global $adb, $current_user;
		$rs = $adb->pquery('SELECT distinct cbmapid,returnvalue,discount FROM vtiger_discountline WHERE discountlineid=?', array($dtolineid));
		if ($rs && $adb->num_rows($rs) > 0) {
			$productid = $context['record_id'];
			$pdotype = getSalesEntityType($productid);
			$qg = new QueryGenerator($pdotype, $current_user);
			$qg->setFields(array('unit_price','cost_price'));
			$qg->addCondition('id', $productid, 'e');
			$query = $qg->getQuery();
			$rsprice = $adb->query($query);
			$rettype = $adb->query_result($rs, 0, 'returnvalue');
			$mapid = $adb->query_result($rs, 0, 'cbmapid');
			if (empty($mapid)) {
				$value = $adb->query_result($rs, 0, 'discount');
			} else {
				if (!empty($context['contactid'])) {
					$f = CRMEntity::getInstance('Accounts');
					$f->retrieve_entity_info($context['contactid'], 'Contacts');
					$context = array_merge($f->column_fields, $context);
				}
				if (!empty($context['accountid'])) {
					$f = CRMEntity::getInstance('Accounts');
					$f->retrieve_entity_info($context['accountid'], 'Accounts');
					$context = array_merge($f->column_fields, $context);
				}
				if (!empty($productid)) {
					$f = CRMEntity::getInstance($pdotype);
					$f->retrieve_entity_info($productid, $pdotype);
					$context = array_merge($f->column_fields, $context);
				}
				$value = coreBOS_Rule::evaluate($mapid, $context);
			}
			if ($rettype == 'Cost+Margin') {
				$return = array('unit price' => ((float)$adb->query_result($rsprice, 0, 'cost_price') * (1 + ($value/100))) , 'discount' => 0);
			} else { // Unit+Discount
				$return = array('unit price' => $adb->query_result($rsprice, 0, 'unit_price') , 'discount' => $value);
			}
			self::$validationinfo[]=sprintf('Record %s (%s), Price %s, Discount %s, Value: %s', $dtolineid, $rettype, $return['unit price'], $return['discount'], $value);
			return $return;
		} else {
			self::$validationinfo[] = 'Discount Record NOT FOUND';
			return false;
		}
	}

	public static function getValidationInfo() {
		return self::$validationinfo;
	}
}
?>
