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
require_once 'include/database/PearDatabase.php';
require_once 'include/utils/utils.php';
require_once 'include/utils/VtlibUtils.php';
require 'vendor/autoload.php';

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
						'columntype'=>'varchar(3)',
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
						'uitype'=>'17',
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
		$eIcbwrk->ExecuteQuery(
			'CREATE TABLE IF NOT EXISTS `invoiceees` (
				`inveid` INT(11) NOT NULL AUTO_INCREMENT,
				`invoiceid` INT(11) DEFAULT NULL,
				`fraeversion` VARCHAR(20) DEFAULT NULL,
				`accislegalentity` VARCHAR(3) DEFAULT NULL,
				`accbrbook` VARCHAR(150) DEFAULT NULL,
				`accbrmregister` VARCHAR(150) DEFAULT NULL,
				`accbrsheet` VARCHAR(30) DEFAULT NULL,
				`accbrfolio` VARCHAR(30) DEFAULT NULL,
				`accbrsection` VARCHAR(30) DEFAULT NULL,
				`accbrvolume` VARCHAR(30) DEFAULT NULL,
				`acccnae` VARCHAR(40) DEFAULT NULL,
				`accinecode` VARCHAR(40) DEFAULT NULL,
				`accsiccode` VARCHAR(35) DEFAULT NULL,
				`accaccountname` VARCHAR(250) DEFAULT NULL,
				`accbill_street` VARCHAR(250) DEFAULT NULL,
				`accbill_code` VARCHAR(15) DEFAULT NULL,
				`accbill_city` VARCHAR(150) DEFAULT NULL,
				`accbill_state` VARCHAR(150) DEFAULT NULL,
				`accbill_country` VARCHAR(35) DEFAULT NULL,
				`accemail1` VARCHAR(50) DEFAULT NULL,
				`accphone` VARCHAR(50) DEFAULT NULL,
				`accfax` VARCHAR(50) DEFAULT NULL,
				`accwebsite` VARCHAR(250) DEFAULT NULL,
				`ctoislegalentity` VARCHAR(3) DEFAULT NULL,
				`ctosiccode` VARCHAR(50) DEFAULT NULL,
				`ctowebsite` VARCHAR(150) DEFAULT NULL,
				`ctocnae` VARCHAR(40) DEFAULT NULL,
				`ctoinecode` VARCHAR(40) DEFAULT NULL,
				`ctofirstname` VARCHAR(150) DEFAULT NULL,
				`ctolastname` VARCHAR(150) DEFAULT NULL,
				`ctosecondlastname` VARCHAR(150) DEFAULT NULL,
				`ctomailingstreet` VARCHAR(250) DEFAULT NULL,
				`ctomailingzip` VARCHAR(35) DEFAULT NULL,
				`ctomailingcity` VARCHAR(150) DEFAULT NULL,
				`ctomailingstate` VARCHAR(150) DEFAULT NULL,
				`ctomailingcountry` VARCHAR(35) DEFAULT NULL,
				`ctoemail` VARCHAR(50) DEFAULT NULL,
				`ctophone` VARCHAR(50) DEFAULT NULL,
				`ctofax` VARCHAR(50) DEFAULT NULL,
				`casiccode` VARCHAR(50) DEFAULT NULL,
				`caname` VARCHAR(250) DEFAULT NULL,
				`caaddress` VARCHAR(250) DEFAULT NULL,
				`cacode` VARCHAR(35) DEFAULT NULL,
				`cacity` VARCHAR(150) DEFAULT NULL,
				`castate` VARCHAR(150) DEFAULT NULL,
				`cagrole` VARCHAR(15) DEFAULT NULL,
				`cagname` VARCHAR(150) DEFAULT NULL,
				`cagaddress` VARCHAR(250) DEFAULT NULL,
				`cagcode` VARCHAR(35) DEFAULT NULL,
				`cagsiccode` VARCHAR(50) DEFAULT NULL,
				`cagcity` VARCHAR(150) DEFAULT NULL,
				`cagstate` VARCHAR(150) DEFAULT NULL,
				`cagcountry` VARCHAR(50) DEFAULT NULL,
				`catrole` VARCHAR(15) DEFAULT NULL,
				`catname` VARCHAR(150) DEFAULT NULL,
				`cataddress` VARCHAR(250) DEFAULT NULL,
				`catcode` VARCHAR(35) DEFAULT NULL,
				`catsiccode` VARCHAR(50) DEFAULT NULL,
				`catcity` VARCHAR(150) DEFAULT NULL,
				`catstate` VARCHAR(150) DEFAULT NULL,
				`cacrole` VARCHAR(15) DEFAULT NULL,
				`cacname` VARCHAR(150) DEFAULT NULL,
				`cacaddress` VARCHAR(250) DEFAULT NULL,
				`caccode` VARCHAR(35) DEFAULT NULL,
				`caccity` VARCHAR(150) DEFAULT NULL,
				`cacstate` VARCHAR(150) DEFAULT NULL,
				`taxperiodstart` DATE DEFAULT NULL,
				`taxperiodend` DATE DEFAULT NULL,
				`discountdesc` VARCHAR(150) DEFAULT NULL,
				`discountamount` FLOAT(25,6) DEFAULT NULL,
				`discountpercent` TINYINT DEFAULT NULL,
				`chargedesc` VARCHAR(150) DEFAULT NULL,
				`chargeamount` FLOAT(25,6) DEFAULT NULL,
				`chargepercent` TINYINT DEFAULT NULL,
				`expcode` VARCHAR(60) DEFAULT NULL,
				`invrefours` VARCHAR(200) DEFAULT NULL,
				`invreftheirs` VARCHAR(200) DEFAULT NULL,
				`invtandc` TEXT DEFAULT NULL,
				`invdesc` TEXT DEFAULT NULL,
				`relatedinvoice` VARCHAR(100) DEFAULT NULL,
				`recttaxperiodstart` DATE DEFAULT NULL,
				`recttaxperiodend` DATE DEFAULT NULL,
				`rectinvoiceseriescode` VARCHAR(150) DEFAULT NULL,
				`rectinvoicenumber` VARCHAR(150) DEFAULT NULL,
				`rectreasoncode` VARCHAR(5) DEFAULT NULL,
				`rectreasondesc` VARCHAR(250) DEFAULT NULL,
				`rectcorrectionmethod` VARCHAR(150) DEFAULT NULL,
				`rdocodigo` VARCHAR(15) DEFAULT NULL,
				`rdodescripcion` TEXT DEFAULT NULL,
				`rdocall` JSON DEFAULT NULL,
				`rdoxml` MEDIUMTEXT DEFAULT NULL,
				PRIMARY KEY (`inveid`),
				index (`invoiceid`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
		);
		$eIcbwrk->ExecuteQuery(
			'CREATE TABLE IF NOT EXISTS `invoiceeesline` (
				`invelineid` INT(11) NOT NULL AUTO_INCREMENT,
				`inveid` INT(11) DEFAULT NULL,
				`lineseq` INT(11) DEFAULT NULL,
				`pdoname` VARCHAR(250) DEFAULT NULL,
				`pdodesc` TEXT DEFAULT NULL,
				`pdocode` VARCHAR(100) DEFAULT NULL,
				`unitpricevat` FLOAT(25,6) DEFAULT NULL,
				`unitpriceneto` FLOAT(25,6) DEFAULT NULL,
				`unitofmeasure` VARCHAR(20) DEFAULT NULL,
				`quantity` FLOAT(20,6) DEFAULT NULL,
				`vat1` FLOAT(6,2) DEFAULT NULL,
				`vat2` FLOAT(6,2) DEFAULT NULL,
				`vat3` FLOAT(6,2) DEFAULT NULL,
				`vat4` FLOAT(6,2) DEFAULT NULL,
				`vat1withheld` TINYINT DEFAULT NULL,
				`vat2withheld` TINYINT DEFAULT NULL,
				`vat3withheld` TINYINT DEFAULT NULL,
				`vat4withheld` TINYINT DEFAULT NULL,
				`vat1surcharge` TINYINT DEFAULT NULL,
				`vat2surcharge` TINYINT DEFAULT NULL,
				`vat3surcharge` TINYINT DEFAULT NULL,
				`vat4surcharge` TINYINT DEFAULT NULL,
				PRIMARY KEY (`invelineid`),
				index (`inveid`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
		);
		$eIcbwrk->ExecuteQuery(
			'CREATE TABLE IF NOT EXISTS `invoiceeeslinedc` (
				`inveldcid` INT(11) NOT NULL AUTO_INCREMENT,
				`invelineid` INT(11) DEFAULT NULL,
				`dctype` VARCHAR(10) DEFAULT NULL,
				`reason` VARCHAR(250) DEFAULT NULL,
				`rate` FLOAT(25,6) DEFAULT NULL,
				`hastaxes` TINYINT DEFAULT NULL,
				PRIMARY KEY (`inveldcid`),
				index (`invelineid`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
		);
		$eIcbwrk->ExecuteQuery(
			'CREATE TABLE IF NOT EXISTS `invoiceeessuplido` (
				`invesuplidoid` INT(11) NOT NULL AUTO_INCREMENT,
				`inveid` INT(11) DEFAULT NULL,
				`lineseq` INT(11) DEFAULT NULL,
				`issuedate` DATE DEFAULT NULL,
				`invoicenumber` VARCHAR(100) DEFAULT NULL,
				`invoiceseriescode` VARCHAR(100) DEFAULT NULL,
				`amount` FLOAT(25,6) DEFAULT NULL,
				PRIMARY KEY (`invesuplidoid`),
				index (`inveid`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
		);
	}

	public function deactivateFieldsModules() {
		$module = Vtiger_Module::getInstance('Invoice');
		$field = Vtiger_Field::getInstance('invoicestatus', $module);
		if ($field) {
			$field->delPicklistValues(array('signede', 'sende'));
		}

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