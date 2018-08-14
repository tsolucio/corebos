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

class Faq extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_faq';
	public $table_index= 'id';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = false;
	public $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_faqcf', 'faqid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity','vtiger_faq','vtiger_faqcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_faq' => 'id',
		'vtiger_faqcomments' => 'faqid',
		'vtiger_faqcf' => 'faqid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		'FAQ No'=>array('faq'=>'faq_no'),
		'Question'=>array('faq'=>'question'),
		'Category'=>array('faq'=>'faqcategories'),
		'Product Name'=>array('faq'=>'product_id'),
		'Created Time'=>array('crmentity'=>'createdtime'),
		'Modified Time'=>array('crmentity'=>'modifiedtime')
	);
	public $list_fields_name = array(
		'FAQ No'=>'faq_no',
		'Question'=>'question',
		'Category'=>'faqcategories',
		'Product Name'=>'product_id',
		'Created Time'=>'createdtime',
		'Modified Time'=>'modifiedtime'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'question';

	// For Popup listview and UI type support
	public $search_fields = array(
		'Question'=>array('faq'=>'question'),
		'Category'=>array('faq'=>'faqcategories'),
		'Product Name'=>array('faq'=>'product_id'),
	);
	public $search_fields_name = array(
		'Question'=>'question',
		'Category'=>'faqcategories',
		'Product Name'=>'product_id',
	);

	// For Popup window record selection
	public $popup_fields = array('question');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'question';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'question';

	// Required Information for enabling Import feature
	public $required_fields = array('question'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'id';
	public $default_sort_order='DESC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('question', 'faq_answer', 'createdtime', 'modifiedtime');

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
		//Inserting into Faq comment table
		if (!((isset($_REQUEST['mode']) && $_REQUEST['mode'] =='Import') || $_REQUEST['action'] =='MassEditSave')) {
			$this->insertIntoFAQCommentTable('vtiger_faqcomments', $module);
		}
	}

	/** Function to insert values in vtiger_faqcomments table for the specified module,
	 * @param $table_name -- table name:: Type varchar
	 * @param $module -- module:: Type varchar
	 */
	public function insertIntoFAQCommentTable($table_name, $module) {
		global $log, $adb;
		$log->info("in insertIntoFAQCommentTable $table_name module is $module");

		$current_time = $adb->formatDate(date('Y-m-d H:i:s'), true);

		if (!empty($this->column_fields['comments'])) {
			$comment = $this->column_fields['comments'];
		} elseif (!empty($_REQUEST['comments'])) {
			$comment = vtlib_purify($_REQUEST['comments']);
		} else {
			$comment = '';
		}
		if ($comment != '') {
			$params = array($this->id, $comment, $current_time);
			$adb->pquery('insert into vtiger_faqcomments (faqid, comments, createdtime) values(?, ?, ?)', $params);
		}
	}

	/** Function to get the list of comments for the given FAQ id
	 * @param  int  $faqid - FAQ id
	 * @return list $list - list of comments and comment informations as a html output where as these comments and comments informations will be formed in div tag.
	 **/
	public function getFAQComments($faqid) {
		global $log, $default_charset, $mod_strings;
		$log->debug("Entering getFAQComments(".$faqid.") method ...");

		$result = $this->db->pquery('select * from vtiger_faqcomments where faqid=?', array($faqid));
		$noofrows = $this->db->num_rows($result);
		$list = '';
		$enddiv = '';
		// In ajax save we should not add this div
		if ($_REQUEST['action'] != 'FaqAjax') {
			$list = '<div id="comments_div" style="overflow: auto;height:200px;width:100%;">';
			$enddiv = '</div>';
		}

		for ($i=0; $i<$noofrows; $i++) {
			$comment = $this->db->query_result($result, $i, 'comments');
			$date = new DateTimeField($this->db->query_result($result, $i, 'createdtime'));
			$createdtime = $date->getDisplayDateTimeValue();
			if ($comment != '') {
				//this div is to display the comment
				if ($_REQUEST['action'] == 'FaqAjax') {
					$comment = htmlentities($comment, ENT_QUOTES, $default_charset);
				}
				$list .= '<div valign="top" style="width:99%;padding-top:10px;" class="dataField">'.make_clickable(nl2br($comment)).'</div>';
				//this div is to display the created time
				$list .= '<div valign="top" style="width:99%;border-bottom:1px dotted #CCCCCC;padding-bottom:5px;" class="dataLabel"><font color=darkred>';
				$list .= $mod_strings['Created Time'].' : '.$createdtime.'</font></div>';
			}
		}
		$list .= $enddiv;
		$log->debug("Exiting getFAQComments method ...");
		return $list;
	}

	/*
	 * Function to get the relation tables for related modules
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	public function setRelationTables($secmodule) {
		$rel_tables = parent::setRelationTables($secmodule);
		$rel_tables['Documents'] = array('vtiger_senotesrel' => array('crmid', 'notesid'), 'vtiger_faq' => 'id');
		return (isset($rel_tables[$secmodule]) ? $rel_tables[$secmodule] : '');
	}

	public function clearSingletonSaveFields() {
		$this->column_fields['comments'] = '';
	}
}
?>
