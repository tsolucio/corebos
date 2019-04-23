<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'Smarty_setup.php';

class ModComments_DetailViewBlockCommentWidget {
	private $_name = 'DetailViewBlockCommentWidget';

	private $defaultCriteria = 'All';

	protected $context = false;
	protected $criteria= false;

	public function __construct() {
		$this->defaultCriteria = GlobalVariable::getVariable('ModComments_DefaultCriteria', $this->defaultCriteria);
	}

	public function getFromContext($key, $purify = false) {
		if ($this->context) {
			$value = $this->context[$key];
			if ($purify && !empty($value)) {
				$value = vtlib_purify($value);
			}
			return $value;
		}
		return false;
	}

	public function title() {
		return getTranslatedString('LBL_MODCOMMENTS_INFORMATION', 'ModComments');
	}

	public function name() {
		return $this->_name;
	}

	public function uikey() {
		return 'ModCommentsDetailViewBlockCommentWidget';
	}

	public function setCriteria($newCriteria) {
		$this->criteria = $newCriteria;
	}

	public function getViewer() {
		global $theme, $app_strings, $current_language;

		$smarty = new vtigerCRM_Smarty();
		$smarty->assign('APP', $app_strings);
		$smarty->assign('MOD', return_module_language($current_language, 'ModComments'));
		$smarty->assign('THEME', $theme);
		$smarty->assign('IMAGE_PATH', "themes/$theme/images/");

		$smarty->assign('UIKEY', $this->uikey());
		$smarty->assign('WIDGET_TITLE', $this->title());
		$smarty->assign('WIDGET_NAME', $this->name());

		return $smarty;
	}

	protected function getModels($parentRecordId, $criteria) {
		global $adb, $current_user;

		$moduleName = 'ModComments';
		if (vtlib_isModuleActive($moduleName)) {
			$entityInstance = CRMEntity::getInstance($moduleName);

			$queryCriteria  = '';
			switch ($criteria) {
				case 'All':
					$queryCriteria = sprintf(' ORDER BY %s.%s DESC ', $entityInstance->table_name, $entityInstance->table_index);
					break;
				case 'Last5':
					$queryCriteria =  sprintf(' ORDER BY %s.%s DESC LIMIT 5', $entityInstance->table_name, $entityInstance->table_index) ;
					break;
				case 'Mine':
					$queryCriteria = ' AND vtiger_crmentity.smcreatorid=' . $current_user->id.
						sprintf(' ORDER BY %s.%s DESC ', $entityInstance->table_name, $entityInstance->table_index);
					break;
			}
			list($void, $queryCriteria) = cbEventHandler::do_filter('corebos.filter.ModComments.queryCriteria', array($parentRecordId, $queryCriteria));
			$query = $entityInstance->getListQuery($moduleName, sprintf(' AND %s.related_to=?', $entityInstance->table_name));
			$query .= $queryCriteria;
			$result = $adb->pquery($query, array($parentRecordId));
			$instances = array();
			if ($adb->num_rows($result)) {
				while ($resultrow = $adb->fetch_array($result)) {
					$instances[] = new ModComments_CommentsModel($resultrow);
				}
			}
		}
		return $instances;
	}

	public function processItem($model) {
		$viewer = $this->getViewer();
		$viewer->assign('COMMENTMODEL', $model);
		return $viewer->fetch(vtlib_getModuleTemplate('ModComments', 'widgets/DetailViewBlockCommentItem.tpl'));
	}

	public function process($context = false) {
		$this->context = $context;
		$sourceRecordId =  $this->getFromContext('ID', true);
		$usecriteria = ($this->criteria === false)? $this->defaultCriteria : $this->criteria;

		$viewer = $this->getViewer();
		$viewer->assign('ID', $sourceRecordId);
		$viewer->assign('CRITERIA', $usecriteria);
		$BLOCKOPEN = GlobalVariable::getVariable('ModComments_DefaultBlockStatus', 1);
		$viewer->assign('BLOCKOPEN', $BLOCKOPEN);
		list($void, $canaddcomments) = cbEventHandler::do_filter('corebos.filter.ModComments.canAdd', array($sourceRecordId, true));
		$viewer->assign('CANADDCOMMENTS', ($canaddcomments ? 'YES' : 'NO'));
		$viewer->assign('COMMENTS', $this->getModels($sourceRecordId, $usecriteria));

		return $viewer->fetch(vtlib_getModuleTemplate('ModComments', 'widgets/DetailViewBlockComment.tpl'));
	}
}