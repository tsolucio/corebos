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
		if (isset($map['showonmodule'])) {
			$mainmodule = $map['showonmodule']['module'];
			$relatedwith = $map['showonmodule']['relatedwith'];
			$relatedfield = $map['showonmodule']['relatedfield'];
			$tooltip = $map['showonmodule']['tooltip'];
		}
		$cbgridactioncol = str_replace('"RLActionRender"', 'RLActionRender', json_encode(gridRelatedListActionColumn('RLActionRender', $map)));
		$smarty->assign('RelatedListWidgetMap', $map);
		$smarty->assign('cbgridactioncol', $cbgridactioncol);
		$smarty->assign('CurrentRecord', $_REQUEST['record']);
		$smarty->assign('title', $map['title']);
		$smarty->assign('originmodule', $map['originmodule']['name']);
		$smarty->assign('targetmodule', $map['targetmodule']['name']);
		$current_module = $currentModule;
		if (isset($relatedwith)) {
			$current_module = $relatedwith;
		}
		$smarty->assign('currentModule', $current_module);
		$smarty->assign('mapname', $mapname);
		$smarty->assign('ID', $id);
		$originid = getRelatedFieldId($current_module, $map['originmodule']['name']);
		$targetid = getRelatedFieldId($map['originmodule']['name'], $map['targetmodule']['name']);
		$SublevelsField = getRelatedFieldId($map['targetmodule']['name'], $map['targetmodule']['name']);
		$origin_related_fieldname = getFieldNameByFieldId($originid);
		$target_related_fieldname = getFieldNameByFieldId($targetid);
		$sub_related_fieldname = getFieldNameByFieldId($SublevelsField);
		$smarty->assign('origin_related_fieldname', $origin_related_fieldname);
		$smarty->assign('target_related_fieldname', $target_related_fieldname);
		$smarty->assign('sub_related_fieldname', $sub_related_fieldname);
		if (empty($map['tooltip'])) {
			$map['tooltip'] = null;
		}
		$smarty->assign('tooltip', json_encode($map['tooltip']));
		$cachedFields = VTCacheUtils::lookupFieldInfo_Module($map['originmodule']['name']);
		$fieldsLabel = array();
		if ($cachedFields) {
			foreach ($cachedFields as $key) {
				$fieldsLabel[$key['fieldname']] = $key['fieldlabel'];
			}
		}
		$showonfieldsLabel = array();
		if (isset($relatedwith)) {
			$cachedFields = VTCacheUtils::lookupFieldInfo_Module($relatedwith);
			foreach ($cachedFields as $key) {
				$showonfieldsLabel[$key['fieldname']] = $key['fieldlabel'];
			}
		}
		$smarty->assign('FieldLables', json_encode($fieldsLabel));
		$smarty->assign('ShowOnFieldLables', json_encode($showonfieldsLabel));
		$smarty->assign('ShowOnModule', isset($relatedwith) ? $relatedwith : false);
		$smarty->assign('ShowOnRelation', isset($relatedfield) ? $relatedfield : false);
		$smarty->assign('ShowOnTooltip', isset($tooltip) ? json_encode($tooltip['fields']) : false);
		return $smarty->fetch('Components/MasterDetail/RelatedListWidget.tpl');
	}
}