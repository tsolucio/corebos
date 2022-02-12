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
include_once 'include/Webservices/Create.php';

class installcbgendoc extends cbupdaterWorker {
	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb, $current_user;
			$module = 'EtiquetasOO';
			if ($this->isModuleInstalled($module)) {
				vtlib_toggleModuleAccess($module, true);
				$this->sendMsg("$module activated!");
			} else {
				$this->installManifestModule($module);
			}
			$module = 'evvtgendoc';
			if ($this->isModuleInstalled($module)) {
				vtlib_toggleModuleAccess($module, true);
				$this->sendMsg("$module activated!");
				$mod_pr = VTiger_Module::getInstance('Documents');
				$block_pr = VTiger_Block::getInstance('LBL_NOTE_INFORMATION', $mod_pr);
				$field8 = new Vtiger_Field();
				$field8->name = 'mergetemplate';
				$field8->label= 'Merge Template';
				$field8->column = 'mergetemplate';
				$field8->columntype = 'INT(1)';
				$field8->uitype = 56;
				$field8->displaytype = 1;
				$field8->presence = 0;
				$block_pr->addField($field8);
			} else {
				$this->installManifestModule($module);
			}
			$foldername = 'Application Templates';
			$dbQuery = 'select folderid from vtiger_attachmentsfolder where foldername=?';
			$result1 = $adb->pquery($dbQuery, array($foldername));
			if ($result1 && $adb->num_rows($result1)==0) {
				$rs = $adb->pquery('select max(folderid),max(sequence) from vtiger_attachmentsfolder', array());
				$fid = (int)$adb->query_result($rs, 0, 0) + 1;
				$sequence = (int)$adb->query_result($rs, 0, 1) + 1;
				$sql = 'insert into vtiger_attachmentsfolder (folderid,foldername,description,createdby,sequence) values (?,?,?,?,?)';
				$params = array($fid, $foldername, '', $current_user->id, $sequence);
				$adb->pquery($sql, $params);
			} else {
				$fid = $adb->query_result($result1, 0, 0);
			}
			$result = $adb->pquery('select * from vtiger_wordtemplates where deleted=0', array());
			while ($mm = $adb->fetch_array($result)) {
				$model_filename = array(
					'name'=>$mm['filename'],
					'size'=>$mm['filesize'],
					'type'=>$mm['filetype'],
					'content'=>$mm['data']
				);
				$docData = array(
					'assigned_user_id'=>vtws_getEntityId('Users').'x'.$current_user->id,
					'notes_title' => $mm['filename'],
					'filename'=>$model_filename,
					'filetype'=>$model_filename['type'],
					'filesize'=>$model_filename['size'],
					'filelocationtype'=>'I',
					'filedownloadcount'=> 0,
					'filestatus'=>1,
					'template'=>1,
					'template_for'=>$mm['module'],
					'mergetemplate'=>1,
					'description'=>$mm['description'],
					'notecontent' => (empty($mm['description']) ? $mm['filename'] : $mm['description']),
					'folderid' => vtws_getEntityId('DocumentFolders').'x'.$fid,
				);
				$response = vtws_create('Documents', $docData, $current_user);
				list($wsid, $crmid) = $response['id'];
				$adb->pquery('update vtiger_crmentity set createdtime=? where crmid=?', array($mm['date_entered'], $crmid));
			}
			$adb->query("delete from vtiger_settings_field where name='LBL_MAIL_MERGE'");
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}