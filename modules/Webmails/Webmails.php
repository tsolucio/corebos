<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is FOSS Labs.
 * Portions created by FOSS Labs are Copyright (C) FOSS Labs.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

class Webmails extends CRMEntity {
	public $table_name = '';
	public $table_index= '';
	public $column_fields = array();
	public $tab_name = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-list_email', 'class' => 'slds-icon', 'icon'=>'list_email');
}
?>
