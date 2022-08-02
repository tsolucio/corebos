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

class cbBCase extends CRMEntity {
	public $table_name = 'vtiger_cbbcase';
	public $table_index= 'cbbcase_id';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-account', 'class' => 'slds-icon', 'icon'=>'case_transcript');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_cbbcasecf', 'cbbcase_id');
	// related_tables variable should define the association (relation) between dependent tables
	// FORMAT: related_tablename => array(related_tablename_column[, base_tablename, base_tablename_column[, related_module]] )
	// Here base_tablename_column should establish relation with related_tablename_column
	// NOTE: If base_tablename and base_tablename_column are not specified, it will default to modules (table_name, related_tablename_column)
	// Uncomment the line below to support custom field columns on related lists
	// public $related_tables = array('vtiger_cbbcasecf' => array('cbbcase_id', 'vtiger_cbbcase', 'cbbcase_id', 'cbbcase'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_cbbcase', 'vtiger_cbbcasecf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_cbbcase'   => 'cbbcase_id',
		'vtiger_cbbcasecf' => 'cbbcase_id',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'businesscasename'=> array('cbbcase' => 'businesscase_name'),
		'businesscaseno' => array('cbbcase'=>'businesscase_no'),
		'Account Name' => array('cbbcase'=>'accid'),
		'contactid' => array('cbbcase'=>'ctoid'),
		'businesscasestatus' => array('cbbcase'=>'businesscasestatus'),
		'Assigned To' => array('crmentity' => 'smownerid')
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'businesscasename'=> 'businesscase_name',
		'businesscaseno' => 'businesscase_no',
		'Account Name' => 'accid',
		'contactid' => 'ctoid',
		'businesscasestatus' => 'businesscasestatus',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'businesscase_name';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'businesscasename'=> array('cbbcase' => 'businesscase_name'),
		'businesscaseno' => array('cbbcase'=>'businesscase_no'),
		'Account Name' => array('cbbcase'=>'accid'),
		'contactid' => array('cbbcase'=>'ctoid'),
		'businesscasestatus' => array('cbbcase'=>'businesscasestatus'),
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'businesscasename'=> 'businesscase_name',
		'businesscaseno' => 'businesscase_no',
		'Account Name' => 'accid',
		'contactid' => 'ctoid',
		'businesscasestatus' => 'businesscasestatus',
	);

	// For Popup window record selection
	public $popup_fields = array('businesscase_name');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'businesscase_name';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'businesscase_name';

	// Required Information for enabling Import feature
	public $required_fields = array('businesscase_name'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'businesscase_name';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'businesscase_name');

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
		if ($event_type == 'module.postinstall') {
			// Handle post installation actions
			$this->setModuleSeqNumber('configure', $modulename, 'BCase-', '00000001');
			require_once 'include/events/VTEventsManager.inc';
			$evManezer = new VTEventsManager($adb);
			$evManezer->registerHandler('vtiger.entity.aftersave', 'modules/cbBCase/cbBCaseHandler.php', 'cbBCaseHandler');
			$evManezer->registerHandler('corebos.entity.link.after', 'modules/cbBCase/cbBCaseHandler.php', 'cbBCaseHandler');
			$moduleInstance = Vtiger_Module::getInstance($modulename);
			$tabid = $moduleInstance->id;
			$mods = array('Accounts', 'Contacts', 'Leads', 'Users', 'Vendors', 'Potentials', 'Quotes', 'SalesOrder', 'Invoice', 'PurchaseOrder');
			$r_actions = 'ADD,SELECT';
			$sql='INSERT INTO vtiger_blocks (tabid, blockid, sequence, blocklabel,iscustom,isrelatedlist) values (?,?,?,?,?,?)';
			$res_seq= $adb->pquery('select max(sequence) from vtiger_blocks where tabid=?', array($tabid));
			$newblock_sequence = $adb->query_result($res_seq, 0, 0);
			$modInstance = Vtiger_Module::getInstance('Documents');
			$moduleInstance->setRelatedList($modInstance, 'Documents', $r_actions, 'get_attachments');
			$rlrs = $adb->pquery(
				'select relation_id from vtiger_relatedlists where tabid=? and related_tabid=?',
				array($tabid, $modInstance->id)
			);
			if ($rlrs && $adb->num_rows($rlrs)>0) {
				$relatedlistid = $adb->query_result($rlrs, 0, 'relation_id');
				$max_blockid = $adb->getUniqueID('vtiger_blocks');
				$params = array($tabid, $max_blockid, ++$newblock_sequence, 'Documents', 1, $relatedlistid);
				$adb->pquery($sql, $params);
			}
			require_once 'modules/Documents/DocumentsUtils.php';
			DocumentsUtils::enableDocumentsForModule($tabid);
			foreach ($mods as $mod) {
				$modInstance = Vtiger_Module::getInstance($mod);
				$moduleInstance->setRelatedList($modInstance, $mod, ($mod=='Users' ? 'SELECT' : $r_actions), 'get_related_list');
				if ($mod=='Accounts' || $mod=='Contacts' || $mod=='Potentials') {
					$modInstance->setRelatedList(
						$moduleInstance,
						$modulename,
						($mod=='Potentials' ? $r_actions : 'ADD'),
						($mod=='Potentials' ? 'get_related_list' : 'get_dependents_list')
					);
					if ($mod=='Accounts') {
						$relfield = 'accid=$RECORD$';
					} elseif ($mod=='Contacts') {
						$relfield = 'ctoid=$RECORD$';
					} else {
						$relfield = 'accid=$related_to&ctoid=$related_to';
					}
					$modInstance->addLink(
						'DETAILVIEWBASIC',
						'Create Business Case',
						'index.php?module=cbBCase&action=EditView&return_module=cbBCase&return_action=DetailView&return_id=$RECORD$&'.$relfield.'&RLparent_id=$RECORD$&createmode=link',
						'{"library":"standard", "icon":"case_transcript"}',
						'1'
					);
				}
				$rlrs = $adb->pquery(
					'select relation_id from vtiger_relatedlists where tabid=? and related_tabid=?',
					array($tabid, $modInstance->id)
				);
				if ($rlrs && $adb->num_rows($rlrs)>0) {
					$relatedlistid = $adb->query_result($rlrs, 0, 'relation_id');
					$max_blockid = $adb->getUniqueID('vtiger_blocks');
					$params = array($tabid, $max_blockid, ++$newblock_sequence, $mod, 1, $relatedlistid);
					$adb->pquery($sql, $params);
				}
			}
			$moduleInstance->addLink('HEADERSCRIPT', 'MailJS', 'include/js/Mail.js', '', 1, null, true);
			$moduleInstance->addLink('DETAILVIEWWIDGET', 'QuickRelatedList', 'module=Utilities&action=UtilitiesAjax&file=QuickRelatedList&formodule=$MODULE$&forrecord=$RECORD$');
			$moduleInstance->addLink('DETAILVIEWWIDGET', 'DetailViewBlockCommentWidget', 'block://ModComments:modules/ModComments/ModComments.php');
			$action = array(
				'menutype' => 'item',
				'title' => 'Recalculate',
				'href' => 'javascript:cbbcrecalculate($RECORD$);',
				'icon' => '{"library":"utility", "icon":"formula"}',
			);
			BusinessActions::addLink($moduleInstance->id, 'DETAILVIEWBASIC', $action['title'], $action['href'], $action['icon'], 0, null, true, 0);
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

	public function calculateActuals($record, $module) {
		switch ($module) {
			case 'PurchaseOrder':
				$this->setActualFields($record, $module, 'actualcost');
				$this->setActualROI($record, $module, 'actualroi');
				break;
			case 'SalesOrder':
				$this->setActualFields($record, $module, 'sumofsalesorder', " and sostatus!='Cancelled' ");
				break;
			case 'Invoice':
				$this->setActualFields($record, $module, 'actualrevenue', " and invoicestatus='Paid' ");
				$this->setActualROI($record, $module, 'actualroi');
				break;
			case 'Quotes':
				$this->setAcceptedQuotes($record, $module, 'acceptedquotes', " and quotestage='Accepted' ");
				break;
		}
	}

	public function reCalculateActuals($record) {
		foreach (array('PurchaseOrder', 'SalesOrder', 'Invoice', 'Quotes') as $formodule) {
			$this->calculateActuals($record, $formodule);
		}
	}

	public function setActualFields($record, $relmodule, $bcfield, $more_where = '') {
		global $current_user, $adb;
		require 'user_privileges/user_privileges_'.$current_user->id.'.php';
		$cur_tab_id = getTabid('cbBCase');
		$rel_tab_id = getTabid($relmodule);
		$sql = $this->getRelatedSql($record, $cur_tab_id, $rel_tab_id, $more_where);
		if ($sql=='') {
			return '';
		}
		$result = $adb->query($sql);
		$Actuals = 0;
		$num_row = $adb->num_rows($result);
		for ($i = 0; $i < $num_row; $i++) {
			$currency_id = $adb->query_result($result, $i, 'currency_id');
			$conversion_rs = $adb->pquery('SELECT conversion_rate FROM vtiger_currency_info WHERE id=?', array($currency_id));
			$conversion_rate = $adb->query_result($conversion_rs, 0, 'conversion_rate');
			$total = ($adb->query_result($result, $i, 'total')/$conversion_rate);
			$total = number_format($total, 2, '.', '');
			$Actuals += $total;
		}
		$adb->pquery("update vtiger_cbbcase set $bcfield=? where cbbcase_id=?", array($Actuals,$record));
	}

	public function setActualROI($record, $relmodule, $bcfield, $more_where = '') {
		global $current_user, $adb;
		require 'user_privileges/user_privileges_'.$current_user->id.'.php';
		$cur_tab_id = getTabid('cbBCase');
		$Invoice_tab_id = getTabid('Invoice');
		$inv_sql = $this->getRelatedSql($record, $cur_tab_id, $Invoice_tab_id, $more_where);
		if ($inv_sql=='') {
			return '';
		}
		$inv_result = $adb->query($inv_sql);
		$Inv_Actuals = 0;
		$inv_num_row = $adb->num_rows($inv_result);
		for ($i = 0; $i < $inv_num_row; $i++) {
			$inv_currency_id = $adb->query_result($inv_result, $i, 'currency_id');
			$inv_conversion_rs = $adb->pquery('SELECT conversion_rate FROM vtiger_currency_info WHERE id=?', array($inv_currency_id));
			$inv_conversion_rate = $adb->query_result($inv_conversion_rs, 0, 'conversion_rate');
			$inv_total = ($adb->query_result($inv_result, $i, 'total')/$inv_conversion_rate);
			$inv_total = number_format($inv_total, 2, '.', '');
			$Inv_Actuals += $inv_total;
		}
		$PO_tab_id = getTabid('PurchaseOrder');
		$po_sql = $this->getRelatedSql($record, $cur_tab_id, $PO_tab_id, $more_where);
		if ($po_sql=='') {
			return '';
		}
		$po_result = $adb->query($po_sql);
		$po_Actuals = 0;
		$po_num_row = $adb->num_rows($po_result);
		for ($i = 0; $i < $po_num_row; $i++) {
			$po_currency_id = $adb->query_result($po_result, $i, 'currency_id');
			$po_conversion_rs = $adb->pquery('SELECT conversion_rate FROM vtiger_currency_info WHERE id=?', array($po_currency_id));
			$po_conversion_rate = $adb->query_result($po_conversion_rs, 0, 'conversion_rate');
			$po_total = ($adb->query_result($po_result, $i, 'total')/$po_conversion_rate);
			$po_total = number_format($po_total, 2, '.', '');
			$po_Actuals += $po_total;
		}
		if ($po_Actuals>0) {
			$ActualsROI = number_format((($Inv_Actuals/$po_Actuals) *100), 3, '.', '');
			$adb->pquery("update vtiger_cbbcase set $bcfield=? where cbbcase_id=?", array($ActualsROI,$record));
		}
	}

	public function setAcceptedQuotes($record, $relmodule, $bcfield, $more_where = '') {
		global $current_user, $adb;
		require 'user_privileges/user_privileges_'.$current_user->id.'.php';
		$cur_tab_id = getTabid('cbBCase');
		$rel_tab_id = getTabid($relmodule);
		$sql = $this->getRelatedSql($record, $cur_tab_id, $rel_tab_id);
		if ($sql=='') {
			return '';
		}
		$result = $adb->query($sql);
		$num_quotes = $adb->num_rows($result);
		$sql_accepted = $this->getRelatedSql($record, $cur_tab_id, $rel_tab_id, $more_where);
		if ($num_quotes==0 || $sql_accepted=='') {
			return '';
		}
		$result_accepted = $adb->query($sql_accepted);
		$accepted = $adb->num_rows($result_accepted);
		$percentual = 100 * ($accepted / $num_quotes);
		$adb->pquery("update vtiger_cbbcase set $bcfield=? where cbbcase_id=?", array($percentual, $record));
	}

	public function getRelatedSql($id, $cur_tab_id, $rel_tab_id, $more_where = '') {
		require_once 'include/utils/VtlibUtils.php';
		global $GetRelatedList_ReturnOnlyQuery, $currentModule, $adb;
		$holdValue = $GetRelatedList_ReturnOnlyQuery;
		$GetRelatedList_ReturnOnlyQuery = true;
		$relationResult = $adb->pquery(
			'SELECT name FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=? limit 1',
			array($cur_tab_id, (int)$rel_tab_id)
		);
		if (!$relationResult || $adb->num_rows($relationResult)==0) {
			return '';
		}
		$relationInfo = $adb->fetch_array($relationResult);
		$params = array($id, $cur_tab_id, $rel_tab_id);
		$holdCM = $currentModule;
		$currentModule = vtlib_getModuleNameById($cur_tab_id);
		$focus = CRMEntity::getInstance($currentModule);
		$focus->id = $id;
		$focus->retrieve_entity_info($id, $currentModule, false, true);
		$relationData = call_user_func_array(array($focus, $relationInfo['name']), array_values($params));
		$GetRelatedList_ReturnOnlyQuery = $holdValue;
		$currentModule = $holdCM;
		if (isset($relationData['query'])) {
			return $relationData['query'].$more_where;
		}
		return '';
	}
}
?>
