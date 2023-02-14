<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************/
// block://sliderwidget:modules/Utilities/cbSliderWidget.php:RECORDID=$RECORD$&title=Slider
// top://sliderwidget:modules/Utilities/cbSliderWidget.php:RECORDID=$RECORD$&title=Slider
// module=Utilities&action=UtilitiesAjax&file=cbSliderWidget&RECORDID=$RECORD$&title=Slider

require_once 'modules/Vtiger/DeveloperWidget.php';
require_once 'include/Webservices/getRecordImages.php';
global $currentModule;

class sliderwidget {

	public static function getWidget($name) {
		return (new sliderwidget_DetailViewBlock());
	}
}

class sliderwidget_DetailViewBlock extends DeveloperBlock {

	protected $widgetName = 'sliderWidget';

	public function process($context = false) {
		global $adb, $site_URL, $currentModule, $current_user;
		$this->context = $context;
		$smarty = $this->getViewer();
		$BAInfo = json_decode($this->getFromContext('BusinessActionInformation'), true);
		$ID = $this->getFromContext('RECORDID');
		if (empty($ID)) {
			return 'ID '.getTranslatedString('LBL_NO_SEARCHRESULT', 'Mobile');
		}
		$title = $this->getFromContext('title');
		$autoplay = $this->getFromContext('autoplay');
		$infinite = $this->getFromContext('infinite');
		$initial = $this->getFromContext('initial');
		$dots = $this->getFromContext('dots');
		$arrows = $this->getFromContext('arrows');
		$rs = $adb->pquery(
			'select vtiger_attachments.attachmentsid,vtiger_attachments.type filetype,vtiger_attachments.path,vtiger_attachments.name,vtiger_notes.title,vtiger_notes.notesid
			from vtiger_notes
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_notes.notesid and vtiger_crmentity.deleted=0
			inner join vtiger_senotesrel on vtiger_senotesrel.notesid=vtiger_notes.notesid
			inner join vtiger_crmobject crm2 on crm2.crmid=vtiger_senotesrel.crmid
			left join vtiger_seattachmentsrel on vtiger_seattachmentsrel.crmid=vtiger_notes.notesid
			left join vtiger_attachments on vtiger_seattachmentsrel.attachmentsid=vtiger_attachments.attachmentsid'
			.getNonAdminAccessControlQuery('Documents', $current_user)
			.' where crm2.crmid=? and vtiger_notes.filetype in ("image/png", "image/jpg", "image/jpeg") order by vtiger_attachments.attachmentsid',
			array($ID)
		);
		if ($adb->num_rows($rs) == 0) {
			return getTranslatedString('MSG_IMAGE_ERROR', $currentModule);
		}
		$dataIMG = array();
		while ($image = $adb->fetch_array($rs)) {
			$dataIMG[] = array(
				'id' => $image['notesid'],
				'title' => $image['title'],
				'path' => $site_URL.'/'.$image['path'].$image['attachmentsid'].'_'.$image['name']
			);
		}
		$customstyle = '';
		if (!empty($BAInfo['widget_height'])) {
			$customstyle .= 'height:'.$BAInfo['widget_height'].';';
		}
		if (!empty($BAInfo['widget_width'])) {
			$customstyle .= 'width:'.$BAInfo['widget_width'].';';
		}
		$Application_Menu_Show = GlobalVariable::getVariable('Application_ImageSlider_Mode', 'documents', $currentModule, $current_user->id);
		if ($Application_Menu_Show == 'fields' || $Application_Menu_Show == 'both') {
			if ($Application_Menu_Show == 'fields') {
				$dataIMG = array();
			}
			$recordImages = cbws_getrecordimageinfo($ID, $current_user);
			foreach ($recordImages['images'] as $key => $value) {
				$dataIMG[] = array(
					'id' => $value['id'],
					'title' => $value['name'],
					'path' => $site_URL . '/' . $value['path'] . $value['id'] . '_' . $value['name']
				);
			}
		}
		$smarty->assign('images', $dataIMG);
		$smarty->assign('totalslides', count($dataIMG));
		$smarty->assign('imagesjson', json_encode($dataIMG));
		$smarty->assign('dots', empty($dots) ? 'true' : (string)$dots);
		$smarty->assign('arrows', empty($arrows) ? 'true' : (string)$arrows);
		$smarty->assign('autoplay', empty($autoplay) ? 'false' : (string)$autoplay);
		$smarty->assign('infinite', empty($infinite) ? 'true' : (string)$infinite);
		$smarty->assign('initial', empty($initial) ? '0' : (string)$initial);
		$smarty->assign('customstyle', $customstyle);
		$smarty->assign('title', empty($title) ? getTranslatedString('Slider') : $title);
		return $smarty->fetch('sliderwidget.tpl');
	}
}

if (isset($_REQUEST['action']) && $_REQUEST['action']==$currentModule.'Ajax') {
	$smq = new sliderwidget_DetailViewBlock();
	echo $smq->process($_REQUEST);
}
