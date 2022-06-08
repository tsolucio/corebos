<?php
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
require_once 'modules/Vtiger/DeveloperWidget.php';
global $currentModule;

class ShareDocLinkWidgetDetailViewBlock extends DeveloperBlock {

	protected $widgetName = 'ShareDocLinkWidget';
	public $widgetPrefix = 'shdclk';

	public function __construct() {
		global $adb;
		$adb->query(
			'CREATE TABLE IF NOT EXISTS vtiger_notificationdrivers (
				id int(11) NOT NULL AUTO_INCREMENT,
				type varchar(250) NOT NULL,
				path varchar(250) NOT NULL,
				functionname varchar(250) NOT NULL,
				PRIMARY KEY (id)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8'
		);
		$adb->query(
			"INSERT INTO vtiger_notificationdrivers (type,path,functionname) VALUES ('docshare','modules/Documents/ShareDocLinkWidgetDetailViewBlock.php','cbdocshare')"
		);
	}

	// This one is called to get the contents to show on screen
	public function process($context = false) {
		$this->context = $context;
		$recid = $this->getFromContext('ID');
		$msg = '';
		if ($recid) {
			$msg = '<a href="javascript:docsdowork(\'createShareLink\', '.$recid.', reloadShareLinkWidget)">'.getTranslatedString('Share').'</a>';
			$shdclkkey = coreBOS_Settings::getSetting($this->widgetPrefix.$recid, 0);
			if ($shdclkkey && coreBOS_Settings::getSetting($this->widgetPrefix.$shdclkkey, 0)) {
				$msg = getTranslatedString('linkurl', 'BusinessActions');
				$msg.= '<script>new ClipboardJS(\'.shdclkcopy\');</script>';
				$shdoclink = $this->constructShareLink($shdclkkey);
				$msg.= '<button class="btn slds-m-left_x-small shdclkcopy" data-clipboard-text="'.$shdoclink.'" title="'.getTranslatedString('Copy', 'EtiquetasOO')
					.'" onclick="this.dataset.clipboardText=\''.$shdoclink.'\';"'
					.'><img src="modules/EtiquetasOO/copyclipboard.png" style="height:16px;vertical-align:middle"></button>';
				$msg.= '<button class="btn slds-m-left_x-small shdclkcopy" title="'.getTranslatedString('LBL_DELETE_BUTTON')
					.'" onClick="javascript:docsdowork(\'deleteShareLink\', '.$recid.', reloadShareLinkWidget)"'
					.'><img src="themes/images/settingsActBtnDelete.gif">'
					.'</button>';
			}
		}
		return $msg;
	}

	public function getShareCode($docid) {
		$skey = '';
		if ($docid) {
			$shdclkkey = coreBOS_Settings::getSetting($this->widgetPrefix.$docid, 0);
			if ($shdclkkey && coreBOS_Settings::getSetting($this->widgetPrefix.$shdclkkey, 0)==$docid) {
				$skey = $shdclkkey;
			}
		}
		return $skey;
	}

	public function isShared($docid) {
		return (!empty($docid) && !empty(coreBOS_Settings::getSetting($this->widgetPrefix.$docid, 0)));
	}

	public function constructShareLink($shdclkkey) {
		global $site_URL;
		return $site_URL.'/notifications.php?type=docshare&doc='.$shdclkkey;
	}

	public function createShareLink($docid) {
		$docid = isset($docid) ? vtlib_purify($docid) : 0;
		$skey = '';
		if (!empty($docid)) {
			$skey = bin2hex(random_bytes(14));
			coreBOS_Settings::setSetting($this->widgetPrefix.$skey, $docid);
			coreBOS_Settings::setSetting($this->widgetPrefix.$docid, $skey);
		}
		return $skey;
	}

	public function deleteShareLink($docid) {
		$docid = isset($docid) ? vtlib_purify($docid) : 0;
		if (!empty($docid)) {
			$shdclkkey = coreBOS_Settings::getSetting($this->widgetPrefix.$docid, 0);
			if ($shdclkkey) {
				coreBOS_Settings::delSetting($this->widgetPrefix.$shdclkkey);
				coreBOS_Settings::delSetting($this->widgetPrefix.$docid);
			} else {
				$docid = 0;
			}
		}
		return $docid;
	}

	public function swapDocument($olddocid, $newdocid) {
		if (!empty($olddocid) && !empty($newdocid)) {
			$shdclkkey = coreBOS_Settings::getSetting($this->widgetPrefix.$olddocid, 0);
			if ($shdclkkey) {
				coreBOS_Settings::setSetting($this->widgetPrefix.$shdclkkey, $newdocid);
				coreBOS_Settings::setSetting($this->widgetPrefix.$newdocid, $shdclkkey);
				coreBOS_Settings::delSetting($this->widgetPrefix.$olddocid);
			}
		}
	}

	public function sendError($error) {
		$msg = getTranslatedString($error, 'Documents');
		header('Content-type: text/plain');
		header('Pragma: public');
		header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private');
		header('Content-Description: File Transfer');
		header('Content-length: '.strlen($msg));
		header('Content-Disposition: attachment; filename="error.txt"');
		echo $msg;
	}

	public function sendFile($docid) {
		$_REQUEST['fileid'] = getSingleFieldValue('vtiger_seattachmentsrel', 'attachmentsid', 'crmid', $docid);
		$_REQUEST['entityid'] = $docid;
		include_once 'include/utils/downloadfile.php';
	}
}

function cbdocshare($input) {
	$smq = new ShareDocLinkWidgetDetailViewBlock();
	$skey = isset($_REQUEST['doc']) ? vtlib_purify($_REQUEST['doc']) : '';
	$docid = coreBOS_Settings::getSetting($smq->widgetPrefix.$skey, 0);
	if (empty($docid)) {
		$smq->sendError('NotShared');
	} elseif (getSalesEntityType($docid)!='Documents') {
		$smq->sendError('LBL_RECORD_NOT_FOUND');
	} else {
		$smq->sendFile($docid);
	}
	die();
}