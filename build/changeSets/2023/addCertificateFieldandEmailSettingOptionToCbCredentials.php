<?php
/*************************************************************************************************
 * Copyright 2023 Spike, JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class addCertificateFieldandEmailSettingOptionToCbCredentials extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset ' . get_class($this) . ' already applied!');
		} else {
			$fields = array(
				'cbCredentials' => array(
					'Credentials File' => array(
						'certificate' => array(
							'columntype'=>'varchar(100)',
							'typeofdata'=>'V~O',
							'uitype'=>69,
							'label' => 'certificate'
						),
					),
					'LBL_EMAIL_SETTINGS' => array(
						'server' => array(
							'columntype'=>'varchar(100)',
							'typeofdata'=>'V~O',
							'uitype'=>1,
							'label' => 'server'
						),
						'LBL_USERNAME' => array(
							'columntype'=>'varchar(100)',
							'typeofdata'=>'V~O',
							'uitype'=>1,
							'label' => 'LBL_USERNAME'
						),
						'LBL_PASWRD' => array(
							'columntype'=>'varchar(100)',
							'typeofdata'=>'V~O',
							'uitype'=>1,
							'label' => 'LBL_PASWRD'
						),
						'LBL_FROM_EMAIL_FIELD' => array(
							'columntype'=>'varchar(50)',
							'typeofdata'=>'V~O',
							'uitype'=>13,
							'label' => 'LBL_FROM_EMAIL_FIELD'
						),
						'LBL_REQUIRES_AUTHENT' => array(
							'columntype'=>'varchar(50)',
							'typeofdata'=>'V~O',
							'uitype'=>15,
							'label' => 'LBL_REQUIRES_AUTHENT',
							'vals' => ['false','true','ssl','sslnc','tls','tlsnc'],
						),
					),
				),
			);
			$this->massCreateFields($fields);
			$this->sendMsg('Changeset ' . get_class($this) . ' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}