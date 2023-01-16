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
		global $currentModule, $current_user;
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
		if (!isset($map['modules'])) {
			return 'Map Not Found';
		}
		$RLInstance = array();
		$FieldLabels = array();
		$RelatedFields = array();
		$Tooltips = array();
		$Wizard = array();
		$WizardWorkflows = array();
		$NextStep = array();
		$PopupAction = array();
		$functionName = '';
		$MainModule = '';
		$MainRelateField = '';
		$LastModule = '';
		$idx = 0;
		foreach ($map['modules'] as $module) {
			if ($idx == 0) {
				$MainRelateField = $module['relatedfield'];
			}
			if ($idx == 1) {
				$MainModule = $module['name'];
			}
			$LastModule = $module['name'];
			$functionName .= $module['name'];
			$RLInstance[] = $module;
			if (isset($module['listview'])) {
				foreach ($module['listview'] as $fld) {
					if (!isset($fld['fieldinfo']['name'])) {
						continue;
					}
					$Columns[] = array(
						'name' => $fld['fieldinfo']['name'],
						'label' => $fld['fieldinfo']['label'],
						'uitype' => $fld['fieldinfo']['uitype'],
					);
				}
			}
			$labels = array();
			$moduleHandler = vtws_getModuleHandlerFromName($module['name'], $current_user);
			$moduleMeta = $moduleHandler->getMeta();
			$moduleFields = $moduleMeta->getModuleFields();
			$accessibleFields = array_keys($moduleFields);
			foreach ($moduleFields as $key) {
				$labels[$key->getFieldName()] = $key->getFieldLabelKey();
			}
			$FieldLabels[$module['name']] = $labels;
			if (!isset($module['relatedfield'])) {
				$module['relatedfield'] = '';
			}
			$RelatedFields[$module['name']] = $module['relatedfield'];
			if (isset($module['tooltip'])) {
				$Tooltips[$module['name']] = $module['tooltip']['fields'];
			}
			$Wizard[$module['name']] = '';
			if (isset($module['wizard'])) {
				$Wizard[$module['name']] = $module['wizard'];
			}
			$NextStep[$module['name']] = true;
			if (isset($module['wizard'])) {
				$NextStep[$module['name']] = boolval($module['nextstep']);
			}
			if (isset($module['workflows'])) {
				$WizardWorkflows = $module['workflows'];
			}
			if (isset($module['popupaction'])) {
				$PopupAction[$module['name']] = $module['popupaction'];
			}
			$idx++;
		}
		$smarty->assign('ID', $id);
		$smarty->assign('CurrentRecord', $_REQUEST['record']);
		$smarty->assign('currentModule', $currentModule);
		$smarty->assign('MainModule', $MainModule);
		$smarty->assign('MainRelateField', $MainRelateField);
		$smarty->assign('LastModule', $LastModule);
		$smarty->assign('Columns', $Columns);
		$smarty->assign('RLInstance', json_encode($RLInstance));
		$smarty->assign('FieldLabels', json_encode($FieldLabels));
		$smarty->assign('RelatedFields', json_encode($RelatedFields));
		$smarty->assign('Tooltips', json_encode($Tooltips));
		$smarty->assign('Wizard', json_encode($Wizard));
		$smarty->assign('WizardArray', $Wizard);
		$smarty->assign('WizardWorkflows', json_encode($WizardWorkflows));
		$smarty->assign('NextStep', json_encode($NextStep));
		$smarty->assign('PopupAction', json_encode($PopupAction));
		$smarty->assign('mapname', $mapname);
		$smarty->assign('functionName', $functionName);
		$smarty->assign('title', $map['title']);
		$cbgridactioncol = str_replace('"RLActionRender"', 'RLActionRender', json_encode(gridRelatedListActionColumn('RLActionRender', $map)));
		$smarty->assign('cbgridactioncol', $cbgridactioncol);
		return $smarty->fetch('Components/MasterDetail/RelatedListWidget.tpl');
	}
}