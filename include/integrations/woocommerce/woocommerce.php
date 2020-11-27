<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module    : Woocommerce Integration
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'vtlib/Vtiger/Module.php';
require_once 'include/Webservices/Revise.php';
require_once 'include/Webservices/Create.php';
require_once 'include/integrations/woocommerce/wcchangeset.php';
require 'vendor/autoload.php';
use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;

// Consumer key: ck_d7848445408231864e1dd3095b5499a229e26129
// Consumer secret: cs_fa17958c32af29e126e896f8c06de11fcb64cf38
// ck_d7848445408231864e1dd3095b5499a229e26129:cs_fa17958c32af29e126e896f8c06de11fcb64cf38
//print_r($this->wcclient->get('system_status'));

class corebos_woocommerce {
	// Configuration Properties
	private $ck = '';
	private $cs = '';
	private $url = '';
	private $apiversion = 'wc/v3';

	// Configuration Keys
	const KEY_ISACTIVE = 'woocommerce_isactive';
	const KEY_CS = 'wcconsumersecret';
	const KEY_CK = 'wcconsumerkey';
	const KEY_URL = 'wcwpurl';

	// Debug
	const DEBUG = true;

	// Errors
	public static $ERROR_NONE = 0;
	public static $ERROR_NOTCONFIGURED = 1;
	public static $ERROR_NOACCESSTOKEN = 2;

	// Utilities
	public $wcclient = null;

	public function __construct() {
		$this->initGlobalScope();
	}

	public function initGlobalScope() {
		$this->cs = coreBOS_Settings::getSetting(self::KEY_CS, '');
		$this->ck = coreBOS_Settings::getSetting(self::KEY_CK, '');
		$this->url = coreBOS_Settings::getSetting(self::KEY_URL, '');
		if (!empty($this->ck) && !empty($this->cs) && !empty($this->url)) {
			$this->wcclient = new Client($this->url, $this->ck, $this->cs, array('version' => $this->apiversion));
		}
	}

	public function saveSettings($isactive, $cs, $ck, $url) {
		coreBOS_Settings::setSetting(self::KEY_ISACTIVE, $isactive);
		coreBOS_Settings::setSetting(self::KEY_CS, $cs);
		coreBOS_Settings::setSetting(self::KEY_CK, $ck);
		coreBOS_Settings::setSetting(self::KEY_URL, $url);
	}

	public function getSettings() {
		return array(
			'isActive' => coreBOS_Settings::getSetting(self::KEY_ISACTIVE, ''),
			'cs' => coreBOS_Settings::getSetting(self::KEY_CS, ''),
			'ck' => coreBOS_Settings::getSetting(self::KEY_CK, ''),
			'url' => coreBOS_Settings::getSetting(self::KEY_URL, ''),
		);
	}

	public function isActive() {
		$isactive = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, '0');
		return ($isactive=='1');
	}
}
?>