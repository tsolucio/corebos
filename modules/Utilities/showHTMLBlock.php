<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************/

// block://showHTMLBlock:modules/Utilities/showHTMLBlock.php:recordid=$RECORD$

require_once 'modules/Vtiger/DeveloperWidget.php';
global $currentModule;

class showHTMLBlock {
	// Get class name of the object that will implement the widget functionality
	public static function getWidget($name) {
		return (new showHTMLBlock_DetailViewBlock());
	}
}

class showHTMLBlock_DetailViewBlock extends DeveloperBlock {

	protected $widgetName = 'showHTMLBlock';

	// This one is called to get the contents to show on screen
	public function process($context = false) {
		global $adb;
		$this->context = $context;
		$ex = $this->getFromContext('ex');
		$strtemplate = new Vtiger_StringTemplate();
		$strtemplate->assign('RECORD', (empty($context['ID']) ? (empty($context['RECORDID']) ? 0 : $context['RECORDID']) : $context['ID']));
		$strtemplate->assign('MODULE', (empty($context['MODULE']) ? '' : $context['MODULE']));
		$ex = $strtemplate->merge($ex);
		$op = strtolower($this->getFromContext('op'));
		/*
		if ($op=='exec') {
			system($ex);
		} elseif ($op=='load') {
			readfile($ex);
		} else
		*/
		if ($op=='iframe') {
			$height = $this->getFromContext('height');
			if (!empty($height)) {
				$height = 'height:'.$height;
			}
			echo '<iframe style="width:100%;'.$height.'" src="'.$ex.'"></iframe>';
		} else {
			echo '';
		}
	}
}
