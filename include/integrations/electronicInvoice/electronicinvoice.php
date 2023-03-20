<?php
/*************************************************************************************************
 * Copyright 2023 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *************************************************************************************************
 *  Module    : Electronic Invoice Settings
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'vtlib/Vtiger/Module.php';
require_once 'include/Webservices/Revise.php';
require_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Update.php';
require_once 'modules/cbupdater/cbupdaterWorker.php';
require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');
require_once('include/utils/VtlibUtils.php');

require "vendor/autoload.php";


class electonicInvoiceUpdater extends cbupdaterWorker {
	// stub to access the updater worker methods
	public function __construct($cbid = 0, $dieonerror = true) {
		parent::__construct(0, false);
	}
}


class corebos_electronicInvoice {
	private $publickey = '';
	private $privatekey = '';
	private $pfkkey = '';
	private $admCenter = '';
	private $passphrase = '';
	private $accountMap = '';
	private $contactMap = '';
	private $FACe = '';
	private $FACB2B = '';
	private $EIbaseurl = '';
	private $EIUsername = '';
	private $EIpassword = '';


	// Configuration Keys
	const KEY_ISACTIVE = 'electronicInvoice_isactive';
	const KEY_PUBLICKEY = 'ispublickey_display';
	const KEY_PRIVATEKEY = 'isprivatekeyid_display';
	const KEY_PFKKEY = 'ispfkkeyid_display';
	const KEY_ADMCENTER = 'isadmcenter_display';
	const KEY_PASSPHRASE = 'ispassphrase';
	const KEY_ACCOUNNTMAP = 'isaccountmap_display';
	const KEY_CONTACTMAP = 'iscontactmap_display';
	const KEY_FACE = 'isassigntypeFACe';
	const KEY_FACB2B = 'isassigntypeFACB2B';
	const KEY_BASEURL = 'isEI_baseurl';
	const KEY_EINVOICEUSERNAME = 'isEI_username';
	const KEY_EINVOICEPASSWORD = 'isEI_password';


	public function __construct() {
		$this->initGlobalScope();
	}

	public function initGlobalScope() {
		$this->publickey = coreBOS_Settings::getSetting(self::KEY_PUBLICKEY, '');
		$this->privatekey = coreBOS_Settings::getSetting(self::KEY_PRIVATEKEY, '');
		$this->pfkkey = coreBOS_Settings::getSetting(self::KEY_PFKKEY, '');
		$this->admCenter = coreBOS_Settings::getSetting(self::KEY_ADMCENTER, '');
		$this->passphrase = coreBOS_Settings::getSetting(self::KEY_PASSPHRASE, '');
		$this->accountMap = coreBOS_Settings::getSetting(self::KEY_ACCOUNNTMAP, '');
		$this->contactMap = coreBOS_Settings::getSetting(self::KEY_CONTACTMAP, '');
		$this->FACe = coreBOS_Settings::getSetting(self::KEY_FACE, '');
		$this->FACB2B = coreBOS_Settings::getSetting(self::KEY_FACB2B, '');
		$this->EIbaseurl = coreBOS_Settings::getSetting(self::KEY_BASEURL, '');
		$this->EIUsername = coreBOS_Settings::getSetting(self::KEY_EINVOICEUSERNAME, '');
		$this->EIpassword = coreBOS_Settings::getSetting(self::KEY_EINVOICEPASSWORD, '');
	}

	public function saveSettings($isactive, $pubkey, $privkey, $pkey, $acenter, $pphrase, $accmap, $contmap, $face, $facb2b, $eibaseurl, $eiuname, $eipass) {
		coreBOS_Settings::setSetting(self::KEY_ISACTIVE, $isactive);
		coreBOS_Settings::setSetting(self::KEY_PUBLICKEY, $pubkey);
		coreBOS_Settings::setSetting(self::KEY_PRIVATEKEY, $privkey);
		coreBOS_Settings::setSetting(self::KEY_PFKKEY, $pkey);
		coreBOS_Settings::setSetting(self::KEY_ADMCENTER, $acenter);
		coreBOS_Settings::setSetting(self::KEY_PASSPHRASE, $pphrase);
		coreBOS_Settings::setSetting(self::KEY_ACCOUNNTMAP, $accmap);
		coreBOS_Settings::setSetting(self::KEY_CONTACTMAP, $contmap);
		coreBOS_Settings::setSetting(self::KEY_FACE, $face);
		coreBOS_Settings::setSetting(self::KEY_FACB2B, $facb2b);
		coreBOS_Settings::setSetting(self::KEY_BASEURL, $eibaseurl);
		coreBOS_Settings::setSetting(self::KEY_EINVOICEUSERNAME, $eiuname);
		coreBOS_Settings::setSetting(self::KEY_EINVOICEPASSWORD, $eipass);

		if ($isactive == '1') {
			$this->activateFieldsModules();
		} else {
			$this->deactivateFieldsModules();
		}
	}

	public function getSettings() {
		return array(
			'isActive' => coreBOS_Settings::getSetting(self::KEY_ISACTIVE, ''),
			'pubkey' => $this->publickey,
			'privkey' => $this->privatekey,
			'pkey' => $this->pfkkey,
			'acenter' => $this->admCenter,
			'pphrase' => $this->passphrase,
			'accmap' => $this->accountMap,
			'contmap' => $this->contactMap,
			'face' => $this->FACe,
			'facb2b' => $this->FACB2B,
			'eibaseurl' => $this->EIbaseurl,
			'eiuname' => $this->EIUsername,
			'eipass' => $this->EIpassword,
		);
	}
	public function isActive() {
		$isactive = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, '0');
		return ($isactive=='1');
	}

	public function activateFieldsModules() {

		$fieldLayout = array(
			'Contacts' => array(
				'LBL_EINVOICE' => array(
					'islegalentity' => array(
						'columntype'=>'varchar(100)',
						'typeofdata'=>'C~O',
						'uitype'=>56,
						'displaytype'=>'1',
						'label' => 'islegalentity'
					),
					'siccode' => array(
						'columntype'=>'varchar(50)',
						'typeofdata'=>'V~O',
						'uitype'=>'1',
						'displaytype'=>'1',
						'label'=>'siccode',
					),
					'secondlastname' => array(
						'columntype'=>'varchar(150)',
						'typeofdata'=>'V~O',
						'uitype'=>'1',
						'displaytype'=>'1',
						'label'=>'secondlastname',
					),
					'website' => array(
						'columntype'=>'varchar(150)',
						'typeofdata'=>'V~O',
						'uitype'=>'1',
						'displaytype'=>'1',
						'label'=>'website',
					),
					'cnae' => array(
						'columntype'=>'varchar(40)',
						'typeofdata'=>'V~O',
						'uitype'=>1,
						'displaytype'=>'1',
						'label' => 'cnae',
					),
					'inecode' => array(
						'columntype'=>'varchar(40)',
						'typeofdata'=>'V~O',
						'uitype'=>'1',
						'displaytype'=>'1',
						'label' => 'inecode',
					),
				)
			),
			'Accounts' => array(
				'LBL_EINVOICE' => array(
					'islegalentity' => array(
						'columntype'=>'varchar(100)',
						'typeofdata'=>'C~O',
						'uitype'=>56,
						'displaytype'=>'1',
						'label' => 'islegalentity'
					),
					'brbook' => array(
						'columntype'=>'varchar(150)',
						'typeofdata'=>'V~O',
						'uitype'=>1,
						'displaytype' => '1',
						'label' => 'brbook'
					),
					'brmregister' => array(
						'columntype'=>'varchar(150)',
						'typeofdata'=>'V~O',
						'uitype'=>1,
						'displaytype'=>'1',
						'label' => 'brmregister'
					),
					'brsheet' => array(
						'columntype'=>'varchar(30)',
						'typeofdata'=>'V~O',
						'uitype'=>1,
						'displaytype'=>'1',
						'label' => 'brsheet'
					),
					'brfolio' => array(
						'columntype'=>'varchar(30)',
						'typeofdata'=>'V~O',
						'uitype'=>1,
						'displaytype'=>'1',
						'label' => 'brfolio'
					),
					'brsection' => array(
						'columntype'=>'varchar(30)',
						'typeofdata'=>'V~O',
						'uitype'=>1,
						'displaytype'=>'1',
						'label' => 'brsection',
					),
					'brvolume' => array(
						'columntype'=>'varchar(30)',
						'typeofdata'=>'V~O',
						'uitype'=>1,
						'displaytype'=>'1',
						'label' => 'brvolume',
					),
					'cnae' => array(
						'columntype'=>'varchar(40)',
						'typeofdata'=>'V~O',
						'uitype'=>1,
						'displaytype'=>'1',
						'label' => 'cnae',
					),
					'inecode' => array(
						'columntype'=>'varchar(40)',
						'typeofdata'=>'V~O',
						'uitype'=>1,
						'displaytype'=>'1',
						'label' => 'inecode',
					),
				),
			),
		);
		$eIcbwrk = new electonicInvoiceUpdater();
		$eIcbwrk->massCreateFields($fieldLayout);
		$module = Vtiger_Module::getInstance('Invoice');
		$field = Vtiger_Field::getInstance('invoicestatus', $module);
		if ($field) {
			$field->setPicklistValues(array('signede', 'sende'));
		}
	}

	public function deactivateFieldsModules() {

		$module = Vtiger_Module::getInstance('Invoice');
		$field = Vtiger_Field::getInstance('invoicestatus', $module);
		if ($field) {
			$field->delPicklistValues(array('signede', 'sende'));
		}

		/*if ($field) {
			$picklistValues = $field->getPicklistValues();
			$updatedValues = array_diff($picklistValues, array('signede', 'sende'));
			$field->setPicklistValues($updatedValues);
		}*/

		$fieldLayout = array(
			'Contacts' => array(
				'islegalentity',
				'siccode',
				'secondlastname',
				'website',
				'cnae',
				'inecode',
			),
			'Accounts' => array(
				'islegalentity',
				'brbook',
				'brmregister',
				'brsheet',
				'brfolio',
				'brsection',
				'brvolume',
				'cnae',
				'inecode',
			),
		);
		$eIcbwrk = new electonicInvoiceUpdater();
		$eIcbwrk->massHideFields($fieldLayout);

	}
}
 ?>