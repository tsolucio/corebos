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

class emailmsgTemplate extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset ' . get_class($this) . ' already applied!');
		} else {
			global $current_user;
			include_once 'modules/MsgTemplate/MsgTemplate.php';

			$langs = array('de'=>'de_de','en'=>'en_us','es'=>'es_es','fr'=>'fr_fr','hu'=>'hu_hu','it'=>'it_it','nl'=>'nl_nl','pt'=>'pt_br');
			foreach ($langs as $shortcode => $longcode) {
				@include "modules/HelpDesk/language/$longcode.lang.php";
				$contents = $mod_strings['LBL_LOGIN_DETAILS'];
				$contents .= '<br><br>'.$mod_strings['LBL_USERNAME'].' $user_name$';
				$contents .= '<br>'.$mod_strings['LBL_PASSWORD'].' $user_password$';
				$focus = new MsgTemplate();
				$focus->id = '';
				$focus->mode = '';
				$focus->column_fields['reference'] = 'CustomerPortal_Mail_Password';
				$focus->column_fields['msgt_type'] = 'Email';
				$focus->column_fields['msgt_status'] = 'Active';
				$focus->column_fields['msgt_language'] = $shortcode;
				$focus->column_fields['subject'] = $mod_strings['LBL_SUBJECT_PORTAL_LOGIN_DETAILS'];
				$focus->column_fields['template'] = $contents;
				$focus->column_fields['templateonlytext'] = $contents;
				$focus->column_fields['tags'] = '';
				$focus->column_fields['description'] = 'template that will be sent when customer portal contact requests a new password';
				$focus->column_fields['assigned_user_id'] = $current_user->id;
				$focus->save('MsgTemplate');
			}

			$mod = Vtiger_Module::getInstance('Contacts');
			$block = Vtiger_Block::getInstance('LBL_CONTACT_INFORMATION', $mod);
			$field = new Vtiger_Field();
			$field->name = 'template_language';
			$field->label = 'Template Language';
			$field->columntype = 'varchar(6)';
			$field->uitype = 15;
			$field->displaytype = 1;
			$field->typeofdata = 'V~O';
			$block->addField($field);
			// Adding picklist on module Contacts
			$langs = array_keys($langs);
			$field->setPicklistValues($langs);

			$this->sendMsg('Changeset ' . get_class($this) . ' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}