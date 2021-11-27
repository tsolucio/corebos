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

class evvtgendoc extends CRMEntity {
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-account', 'class' => 'slds-icon', 'icon'=>'template');
	public $tab_name = array();

	/**
	 * Invoked when special actions are performed on the module.
	 * @param string Module name
	 * @param string Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		if ($event_type == 'module.postinstall') {
			$module = Vtiger_Module::getInstance('evvtgendoc');
			$module->addLink('HEADERSCRIPT', 'evvtgendoc.js', 'modules/evvtgendoc/evvtgendoc.js');
			require_once 'modules/evvtgendoc/OpenDocument.php';
			@mkdir(OpenDocument::GENDOCCACHE);

			global $adb;
			$em = new VTEventsManager($adb);
			$em->registerHandler('corebos.header', 'modules/evvtgendoc/addmergediv.php', 'AddGenDocMergeDIV');

			$global_variables = array(
				'GenDoc_Save_Document_Folder',
				'GenDoc_Default_Compile_Language',
				'GenDoc_Convert_URL',
				'GenDoc_CopyLabelToClipboard',
			);
			$moduleInstance = Vtiger_Module::getInstance('GlobalVariable');
			$field = Vtiger_Field::getInstance('gvname', $moduleInstance);
			if ($field) {
				$field->setPicklistValues($global_variables);
			}

			$mod_pr = Vtiger_Module::getInstance('Documents');
			$block_pr = Vtiger_Block::getInstance('LBL_NOTE_INFORMATION', $mod_pr);
			$field8 = new Vtiger_Field();
			$field8->name = 'template';
			$field8->label= 'Template';
			$field8->column = 'template';
			$field8->columntype = 'INT(1)';
			$field8->uitype = 56;
			$field8->displaytype = 1;
			$field8->presence = 0;
			$block_pr->addField($field8);

			$field8 = new Vtiger_Field();
			$field8->name = 'template_for';
			$field8->label= 'Template For';
			$field8->column = 'template_for';
			$field8->columntype = 'VARCHAR(150)';
			$field8->uitype = 1613;
			$field8->displaytype = 1;
			$field8->presence = 0;
			$block_pr->addField($field8);

			$field8 = new Vtiger_Field();
			$field8->name = 'mergetemplate';
			$field8->label= 'Merge Template';
			$field8->column = 'mergetemplate';
			$field8->columntype = 'INT(1)';
			$field8->uitype = 56;
			$field8->displaytype = 1;
			$field8->presence = 0;
			$block_pr->addField($field8);
			include_once 'modules/cbMap/cbMap.php';
			$focusnew = new cbMap();
			$focusnew->column_fields['assigned_user_id'] = Users::getActiveAdminID();
			$focusnew->column_fields['mapname'] = 'GenDocMerge_ConditionExpression';
			$focusnew->column_fields['maptype'] = 'Condition Expression';
			$focusnew->column_fields['targetname'] = 'Accounts';
			$focusnew->column_fields['content'] = '<map>
<function>
	<name>isPermitted</name>
	<parameters>
		<parameter>currentModule</parameter>
		<parameter>Merge</parameter>
		<parameter></parameter>
	</parameters>
</function>
</map>';
			$focusnew->save('cbMap');
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
}
?>
