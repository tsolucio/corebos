<?php
require_once 'modules/Vtiger/DeveloperWidget.php';
global $currentModule;

class MasterDetailGridLayoutWidget {
	// Get class name of the object that will implement the widget functionality
	public static function getWidget($name) {
		return (new MasterDetailGridLayout_DetailViewBlock());
	}
}

class MasterDetailGridLayout_DetailViewBlock extends DeveloperBlock {
	// Implement widget functionality
	protected $widgetName = 'MasterDetailGridLayout';

	// This one is called to get the contents to show on screen
	public function process($context = false) {
		if (!empty($context['mapname'])) {
			$mapname = $context['mapname'];
		} elseif (!empty($_REQUEST['mapname'])) {
			$mapname = $_REQUEST['mapname'];
		} else {
			return 'No Map Defined';
		}
		if (!empty($context['PID'])) {
			$masterid = $context['PID'];
		} elseif (!empty($_REQUEST['PID'])) {
			$masterid = $_REQUEST['PID'];
		} else {
			return 'No Master Record';
		}
		$cbMap = cbMap::getMapByName($mapname, 'MasterDetailLayout');
		if (empty($cbMap)) {
			return 'Map Not Found';
		}
		$this->context = $context;
		$smarty = $this->getViewer();
		$mdmap = $cbMap->MasterDetailLayout();
		if (isset($mdmap['listview']['toolbar'])) {
			$mdactions = array(
				'moveup' => $mdmap['listview']['toolbar']['moveup'],
				'movedown' => $mdmap['listview']['toolbar']['movedown'],
				'edit' => $mdmap['listview']['toolbar']['edit'],
				'delete' => $mdmap['listview']['toolbar']['delete'],
			);
			$mdmap['listview']['cbgridactioncol'] = str_replace('"mdActionRender"', 'mdActionRender', json_encode(gridGetActionColumn('mdActionRender', $mdactions)));
		}
		$smarty->assign('MasterDetailLayoutMap', $mdmap);
		$smarty->assign('MasterDetaiCurrentRecord', $_REQUEST['record']);
		$smarty->assign('MasterID', $masterid);
		$smarty->assign('MasterDetail_Pagination', GlobalVariable::getVariable('MasterDetail_Pagination', 0));
		return $smarty->fetch('Components/MasterDetail/Grid.tpl');
	}
}