<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  coreBOS Open Source
 * The Initial Developer of the Original Code is coreBOS.
 * Portions created by vtiger are Copyright (C) coreBOS.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'modules/Vtiger/DeveloperWidget.php';
global $currentModule;

class RelatedListWidget {
	// Get class name of the object that will implement the widget functionality
	public static function getWidget($name) {
		return (new RelatedListWidget_DetailViewBlock());
	}
}

class RelatedListWidget_DetailViewBlock extends DeveloperBlock {
	// Implement widget functionality
	protected $widgetName = 'RelatedListWidget';

	// This one is called to get the contents to show on screen
	public function process($context = false) {
		global $currentModule;
		if (!empty($context['mapname'])) {
			$mapname = $context['mapname'];
		} elseif (!empty($_REQUEST['mapname'])) {
			$mapname = $_REQUEST['mapname'];
		} else {
			return 'No Map Defined';
		}
		if (!empty($context['PID'])) {
			$id = $context['PID'];
		} elseif (!empty($_REQUEST['PID'])) {
			$id = $_REQUEST['PID'];
		} else {
			return 'No Master Record';
		}
		$cbMap = cbMap::getMapByName($mapname, 'RelatedListBlock');
		if (empty($cbMap)) {
			return 'Map Not Found';
		}
		$this->context = $context;
		$smarty = $this->getViewer();
		$map = $cbMap->RelatedListBlock();
		$cbgridactioncol = str_replace('"RLActionRender"', 'RLActionRender', json_encode(gridRelatedListActionColumn('RLActionRender', $map)));
		$smarty->assign('RelatedListWidgetMap', $map);
		$smarty->assign('cbgridactioncol', $cbgridactioncol);
		$smarty->assign('CurrentRecord', $_REQUEST['record']);
		$smarty->assign('originmodule', $map['originmodule']['name']);//Messages
		$smarty->assign('targetmodule', $map['targetmodule']['name']);//Assets
		$smarty->assign('currentModule', $currentModule);//Accounts
		$smarty->assign('mapname', $mapname);
		$smarty->assign('ID', $id);
		$OriginFieldID = getRelatedFieldId($currentModule, $map['originmodule']['name']);
		$TargetFieldID = getRelatedFieldId($map['originmodule']['name'], $map['targetmodule']['name']);
		$SublevelsField = getRelatedFieldId($map['targetmodule']['name'], $map['targetmodule']['name']);
		$origin_related_fieldname = getFieldNameByFieldId($OriginFieldID);
		$target_related_fieldname = getFieldNameByFieldId($TargetFieldID);
		$sub_related_fieldname = getFieldNameByFieldId($SublevelsField);
		$smarty->assign('origin_related_fieldname', $origin_related_fieldname);
		$smarty->assign('target_related_fieldname', $target_related_fieldname);
		$smarty->assign('sub_related_fieldname', $sub_related_fieldname);
		return $smarty->fetch('Components/MasterDetail/RelatedListWidget.tpl');
	}
}