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
require_once 'include/Webservices/SetRelation.php';
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
	private $customerModule = '1';
	private $productModule = '1';
	private $orderModule = '1';
	private $apiversion = 'wc/v3';
	public static $supportedModules = array('Accounts','Contacts','Products','Services','SalesOrder','Invoice','wcProductCategory');

	// Configuration Keys
	const KEY_ISACTIVE = 'woocommerce_isactive';
	const KEY_CS = 'wcconsumersecret';
	const KEY_CK = 'wcconsumerkey';
	const KEY_URL = 'wcwpurl';
	const KEY_SCT = 'wcwpsecret';
	const KEY_CM = 'wcwpcustomermodule';
	const KEY_PM = 'wcwpproductmodule';
	const KEY_OM = 'wcwpordermodule';

	// Debug
	const DEBUG = true;

	// Errors
	public static $ERROR_NONE = 0;
	public static $ERROR_NOTCONFIGURED = 1;
	public static $ERROR_NOACCESSTOKEN = 2;

	// Utilities
	public $wcclient = null;
	private $messagequeue = null;
	private $moduleMeta = array();

	public function __construct() {
		$this->initGlobalScope();
	}

	public function initGlobalScope() {
		$this->cs = coreBOS_Settings::getSetting(self::KEY_CS, '');
		$this->ck = coreBOS_Settings::getSetting(self::KEY_CK, '');
		$this->url = coreBOS_Settings::getSetting(self::KEY_URL, '');
		$this->customerModule = coreBOS_Settings::getSetting(self::KEY_CM, '1');
		$this->productModule = coreBOS_Settings::getSetting(self::KEY_PM, '1');
		$this->orderModule = coreBOS_Settings::getSetting(self::KEY_OM, '1');
		if (!empty($this->ck) && !empty($this->cs) && !empty($this->url)) {
			$this->wcclient = new Client($this->url, $this->ck, $this->cs, array('version' => $this->apiversion));
		}
		$this->messagequeue = coreBOS_MQTM::getInstance();
	}

	public function saveSettings($isactive, $cs, $ck, $url, $sct, $cm, $pm, $om) {
		coreBOS_Settings::setSetting(self::KEY_ISACTIVE, $isactive);
		coreBOS_Settings::setSetting(self::KEY_CS, $cs);
		coreBOS_Settings::setSetting(self::KEY_CK, $ck);
		coreBOS_Settings::setSetting(self::KEY_URL, $url);
		coreBOS_Settings::setSetting(self::KEY_SCT, $sct);
		coreBOS_Settings::setSetting(self::KEY_CM, $cm);
		coreBOS_Settings::setSetting(self::KEY_PM, $pm);
		coreBOS_Settings::setSetting(self::KEY_OM, $om);
		$cs = new woocommercechangeset(0, false);
		if ($this->isActive()) {
			$cs->applyChange();
		} else {
			$cs->undoChange();
		}
	}

	public function getSettings() {
		return array(
			'isActive' => coreBOS_Settings::getSetting(self::KEY_ISACTIVE, ''),
			'cs' => coreBOS_Settings::getSetting(self::KEY_CS, ''),
			'ck' => coreBOS_Settings::getSetting(self::KEY_CK, ''),
			'url' => coreBOS_Settings::getSetting(self::KEY_URL, ''),
			'sct' => coreBOS_Settings::getSetting(self::KEY_SCT, ''),
			'cm' => coreBOS_Settings::getSetting(self::KEY_CM, '1'),
			'pm' => coreBOS_Settings::getSetting(self::KEY_PM, '1'),
			'om' => coreBOS_Settings::getSetting(self::KEY_OM, '1'),
		);
	}

	public function isActive() {
		$isactive = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, '0');
		return ($isactive=='1');
	}

	public function WCChangeSync() {
		$this->initGlobalScope();
		if (!$this->isActive()) {
			return;
		}
		while ($msg = $this->messagequeue->getMessage('WooCChangeChannel', 'WCChangeSync', 'WCChangeHandler')) {
			$change = json_decode($msg['information'], true);
			$moduleName = $change['module'];
			if (in_array($moduleName, self::$supportedModules)) {
				switch ($moduleName) {
					case 'Accounts':
					case 'Contacts':
						$this->sendCustomer2WC($change);
						break;
					case 'Products':
					case 'Services':
						$this->sendProduct2WC($change);
						break;
					case 'SalesOrder':
					case 'Invoice':
						$this->sendOrder2WC($change);
						break;
					case 'wcProductCategory':
						$this->sendCategory2WC($change);
						break;
					default:
						$this->messagequeue->rejectMessage($msg, 'Module not supported: '.$moduleName);
				}
			}
		}
	}

	public function WCDeleteSync() {
		$this->initGlobalScope();
		if (!$this->isActive()) {
			return;
		}
		while ($msg = $this->messagequeue->getMessage('WooCChangeChannel', 'WCDeleteSync', 'WCChangeHandler')) {
			$change = json_decode($msg['information'], true);
			$moduleName = $change['module'];
			if (in_array($moduleName, self::$supportedModules)) {
				switch ($moduleName) {
					case 'Accounts':
					case 'Contacts':
						$this->deleteCustomerInWC($change);
						break;
					case 'SalesOrder':
					case 'Invoice':
						$this->deleteOrderInWC($change);
						break;
					case 'Products':
					case 'Services':
						$this->deleteProductInWC($change);
						break;
					case 'wcProductCategory':
						$this->deleteCategoryInWC($change);
						break;
					default:
						$this->messagequeue->rejectMessage($msg, 'Module not supported: '.$moduleName);
				}
			}
		}
	}

	public function cbChangeSync() {
		$this->initGlobalScope();
		if (!$this->isActive()) {
			return;
		}
		while ($msg = $this->messagequeue->getMessage('WooCChangeChannel', 'cbChangeSync', 'WCChangeHandler')) {
			$change = json_decode($msg['information'], true);
			list($moduleName, $action) = explode('.', $change['event']);
			if ($moduleName=='customer') {
				$moduleName = $this->customerModule ? 'Contacts' : 'Accounts';
			} elseif ($moduleName=='order') {
				$moduleName = $this->orderModule ? 'SalesOrder' : 'Invoice';
			} elseif ($moduleName=='product') {
				$moduleName = $this->productModule ? 'Products' : 'Services';
			} else {
				return;
			}
			switch ($action) {
				case 'created':
					$crmid = $this->getCBIDFromEntity($moduleName, $change['data']['id']);
					if (empty($crmid)) {
						$this->createFromWC($moduleName, $change['data']);
					} else {
						$this->updateFromWC($moduleName, $change['data']);
					}
					break;
				case 'updated':
					$this->updateFromWC($moduleName, $change['data']);
					break;
				case 'deleted':
					$this->deleteFromWC($moduleName, $change['data']);
					break;
				case 'restored':
					$this->restoreFromWC($moduleName, $change['data']);
					break;
				default:
					$this->messagequeue->rejectMessage($msg, "Action $action not supported for $moduleName");
			}
		}
	}

	public function sendCustomer2WC($change) {
		$send2wc = $this->getPropertiesToWC($change);
		if (count($send2wc)>0) {
			$wcid = $this->getWCIDFromEntity($change['module'], $change['record_id']);
			if ($wcid == 'CREATEIT') {
				try {
					$rdo = $this->wcclient->post('customers', $send2wc);
					if (isset($rdo->data) && isset($rdo->data->status) && isset($rdo->message)) {
						$this->logMessage('sendCustomer2WC', $rdo->code.' - '.$rdo->message, $send2wc, $rdo);
					} else {
						$this->updateControlFields($change['module'], $change['record_id'], $rdo->id);
						$this->logMessage('sendCustomer2WC', 'OK', $send2wc, $rdo, false);
					}
				} catch (Exception $e) {
					$this->logMessage('sendCustomer2WC', $e->getMessage(), $send2wc, 0);
				}
			} elseif ($wcid!='') {
				try {
					$rdo = $this->wcclient->put('customers/'.$wcid, $send2wc);
					if (isset($rdo->data) && isset($rdo->data->status) && isset($rdo->message)) {
						$this->logMessage('sendCustomer2WC', $rdo->code.' - '.$rdo->message, $send2wc, $rdo);
					} else {
						$this->logMessage('sendCustomer2WC', 'OK', $send2wc, $rdo, false);
					}
				} catch (Exception $e) {
					$this->logMessage('sendCustomer2WC', $e->getMessage(), $send2wc, 0);
				}
			}
		}
	}

	public function sendProduct2WC($change) {
		$send2wc = $this->getPropertiesToWC($change);
		if (count($send2wc)>0) {
			$wcid = $this->getWCIDFromEntity($change['module'], $change['record_id']);
			if ($wcid == 'CREATEIT') {
				try {
					$rdo = $this->wcclient->post('products', $send2wc);
					if (isset($rdo->data) && isset($rdo->data->status) && isset($rdo->message)) {
						$this->logMessage('sendProduct2WC', $rdo->code.' - '.$rdo->message, $send2wc, $rdo);
					} else {
						$this->updateControlFields($change['module'], $change['record_id'], $rdo->id);
						$this->logMessage('sendProduct2WC', 'OK', $send2wc, $rdo, false);
					}
				} catch (Exception $e) {
					$this->logMessage('sendProduct2WC', $e->getMessage(), $send2wc, 0);
				}
			} elseif ($wcid!='') {
				try {
					$rdo = $this->wcclient->put('products/'.$wcid, $send2wc);
					if (isset($rdo->data) && isset($rdo->data->status) && isset($rdo->message)) {
						$this->logMessage('sendProduct2WC', $rdo->code.' - '.$rdo->message, $send2wc, $rdo);
					} else {
						$this->logMessage('sendProduct2WC', 'OK', $send2wc, $rdo, false);
					}
				} catch (Exception $e) {
					$this->logMessage('sendProduct2WC', $e->getMessage(), $send2wc, 0);
				}
			}
		}
	}

	public function sendCategory2WC($change) {
		$send2wc = $this->getPropertiesToWC($change);
		if (count($send2wc)>0) {
			$wcid = $this->getWCIDFromEntity($change['module'], $change['record_id']);
			if ($wcid == 'CREATEIT') {
				try {
					$rdo = $this->wcclient->post('products/categories', $send2wc);
					if (isset($rdo->data) && isset($rdo->data->status) && isset($rdo->message)) {
						$this->logMessage('sendCategory2WC', $rdo->code.' - '.$rdo->message, $send2wc, $rdo);
					} else {
						$this->updateControlFields($change['module'], $change['record_id'], $rdo->id);
						$this->logMessage('sendCategory2WC', 'OK', $send2wc, $rdo, false);
					}
				} catch (Exception $e) {
					$this->logMessage('sendCategory2WC', $e->getMessage(), $send2wc, 0);
				}
			} elseif ($wcid!='') {
				try {
					$rdo = $this->wcclient->put('products/categories/'.$wcid, $send2wc);
					if (isset($rdo->data) && isset($rdo->data->status) && isset($rdo->message)) {
						$this->logMessage('sendCategory2WC', $rdo->code.' - '.$rdo->message, $send2wc, $rdo);
					} else {
						$this->logMessage('sendCategory2WC', 'OK', $send2wc, $rdo, false);
					}
				} catch (Exception $e) {
					$this->logMessage('sendCategory2WC', $e->getMessage(), $send2wc, 0);
				}
			}
		}
	}

	public function sendOrder2WC($change) {
		$send2wc = $this->getPropertiesToWC($change);
		if (count($send2wc)>0) {
			// we do not support this yet
		}
	}

	public function deleteCustomerInWC($change) {
		$wcid = $this->getWCIDFromEntity($change['module'], $change['record_id']);
		if ($wcid!='' && $this->isActive()) {
			try {
				$rdo = $this->wcclient->delete('customers/'.$wcid, ['force' => true]);
				if (isset($rdo->data) && isset($rdo->data->status) && isset($rdo->message)) {
					$this->logMessage('delCustomerInWC', $rdo->code.' - '.$rdo->message, $change, $rdo);
				} else {
					$this->updateDeleteFields($change['module'], $change['record_id']);
					$this->logMessage('delCustomerInWC', 'OK', $wcid, $rdo, false);
				}
			} catch (Exception $e) {
				$this->logMessage('delCustomerInWC', $e->getMessage(), $change, 0);
			}
		}
	}

	public function deleteProductInWC($change) {
		$wcid = $this->getWCIDFromEntity($change['module'], $change['record_id']);
		if ($wcid!='' && $this->isActive()) {
			try {
				$rdo = $this->wcclient->delete('products/'.$wcid, ['force' => true]);
				if (isset($rdo->data) && isset($rdo->data->status) && isset($rdo->message)) {
					$this->logMessage('delProductInWC', $rdo->code.' - '.$rdo->message, $change, $rdo);
				} else {
					$this->updateDeleteFields($change['module'], $change['record_id']);
					$this->logMessage('delProductInWC', 'OK', $wcid, $rdo, false);
				}
			} catch (Exception $e) {
				$this->logMessage('delProductInWC', $e->getMessage(), $change, 0);
			}
		}
	}

	public function deleteCategoryInWC($change) {
		$wcid = $this->getWCIDFromEntity($change['module'], $change['record_id']);
		if ($wcid!='' && $this->isActive()) {
			try {
				$rdo = $this->wcclient->delete('products/categories/'.$wcid, ['force' => true]);
				if (isset($rdo->data) && isset($rdo->data->status) && isset($rdo->message)) {
					$this->logMessage('delCategoryInWC', $rdo->code.' - '.$rdo->message, $change, $rdo);
				} else {
					$this->updateDeleteFields($change['module'], $change['record_id']);
					$this->logMessage('delCategoryInWC', 'OK', $wcid, $rdo, false);
				}
			} catch (Exception $e) {
				$this->logMessage('delCategoryInWC', $e->getMessage(), $change, 0);
			}
		}
	}

	public function deleteOrderInWC($change) {
		$send2wc = $this->getPropertiesToWC($change);
		if (!empty($send2wc)) {
			// we do not support this yet
		}
	}

	public function createFromWC($moduleName, $data) {
		global $current_user, $adb;
		$send2cb = $this->getPropertiesFromWC($moduleName, $data);
		if (!empty($send2cb)) {
			$hold = '';
			try {
				if (isset($send2cb['pdoInformation'])) {
					$hold = $send2cb['pdoInformation'];
					unset($send2cb['pdoInformation']);
				}
				if (isset($send2cb['taxtype'])) {
					$holdtax = $send2cb['taxtype'];
					unset($send2cb['taxtype']);
				}
				$send2cb = DataTransform::sanitizeData($send2cb, $this->getModuleMetaData($moduleName));
				if (is_array($hold)) {
					$send2cb['pdoInformation'] = $hold;
				}
				if (isset($holdtax)) {
					$send2cb['taxtype'] = $holdtax;
				}
				$send2cb['wcsyncstatus'] = 'Active';
				$send2cb['wccreated'] = '1';
				$send2cb['wccode'] = $data['id'];
				$send2cb['wcdeleted'] = '0';
				$send2cb['wcurl'] = $this->getWCURL($moduleName, $data['id']);
				coreBOS_Settings::setSetting('woocommerce_syncing', 'creating');
				$new = vtws_create($moduleName, $send2cb, $current_user);
				coreBOS_Settings::delSetting('woocommerce_syncing');
				$this->logMessage('get'.$moduleName.'FromWC', 'OK:Create', $send2cb, $new, false);
				$mod = CRMEntity::getInstance($moduleName);
				list($wsid, $crmid) = $new['id'];
				$adb->pquery('update '.$mod->table_name.' set wccreated=1,wcdeleted=0,wcdeletedon=null where '.$mod->table_index.'=?', array($crmid));
				$this->setCategoryRelations($moduleName, $crmid, $send2cb);
			} catch (Exception $e) {
				$this->logMessage('get'.$moduleName.'FromWC', $e->getMessage(), $send2cb, 0);
			}
		}
	}

	public function updateFromWC($moduleName, $data) {
		global $current_user;
		$crmid = $this->getCBIDFromEntity($moduleName, $data['id']);
		if ($crmid=='') {
			$this->createFromWC($moduleName, $data);
			return;
		}
		$send2cb = $this->getPropertiesFromWC($moduleName, $data);
		if (!empty($send2cb)) {
			$hold = '';
			try {
				if (isset($send2cb['pdoInformation'])) {
					$hold = $send2cb['pdoInformation'];
					unset($send2cb['pdoInformation']);
				}
				if (isset($send2cb['taxtype'])) {
					$holdtax = $send2cb['taxtype'];
					unset($send2cb['taxtype']);
				}
				$send2cb = DataTransform::sanitizeData($send2cb, $this->getModuleMetaData($moduleName));
				if (is_array($hold)) {
					$send2cb['pdoInformation'] = $hold;
				}
				if (isset($holdtax)) {
					$send2cb['taxtype'] = $holdtax;
				}
				coreBOS_Settings::setSetting('woocommerce_syncing', $crmid);
				$rdo = vtws_revise($send2cb, $current_user);
				coreBOS_Settings::delSetting('woocommerce_syncing');
				$this->logMessage('get'.$moduleName.'FromWC', 'OK:Update', $send2cb, $rdo, false);
				$this->setCategoryRelations($moduleName, $crmid, $send2cb);
			} catch (Exception $e) {
				$this->logMessage('get'.$moduleName.'FromWC', $e->getMessage(), $send2cb, 0);
			}
		}
	}

	public function setCategoryRelations($moduleName, $crmid, $send2cb) {
		global $adb, $current_user;
		if (($moduleName=='Products' || $moduleName=='Services') && !empty($send2cb['categories']) && vtlib_isModuleActive('wcProductCategory')) {
			// delete existing relations
			$adb->pquery(
				'delete from vtiger_crmentityrel where (crmid=? and relmodule=?) OR (relcrmid=? and module=?)',
				array($crmid, 'wcProductCategory', $crmid, 'wcProductCategory')
			);
			// establish relations
			$cats = array();
			foreach ($send2cb['categories'] as $wccat) {
				$c = getSingleFieldValue('vtiger_wcproductcategory', 'wcproductcategoryid', 'wccode', $wccat);
				if (!empty($c)) {
					$cats[] = $c;
				}
			}
			if (!empty($cats)) {
				vtws_setrelation($crmid, $cats, $current_user);
			}
		}
	}

	public function deleteFromWC($moduleName, $data) {
		$crmid = $this->getCBIDFromEntity($moduleName, $data['id']);
		if (!empty($crmid)) {
			$this->updateDeleteFields($moduleName, $crmid);
		}
	}

	public function restoreFromWC($moduleName, $data) {
		global $adb;
		$crmid = $this->getCBIDFromEntity($moduleName, $data['id']);
		if (!empty($crmid)) {
			$mod = CRMEntity::getInstance($moduleName);
			$adb->pquery('update '.$mod->table_name.' set wcdeleted=0,wcdeletedon=null where '.$mod->table_index.'=?', array($crmid));
		}
	}

	// Utility functions

	private function getModuleMetaData($module) {
		global $adb, $log, $current_user;
		if (empty($this->moduleMeta[$module])) {
			$webserviceObject = VtigerWebserviceObject::fromName($adb, $module);
			$handlerPath = $webserviceObject->getHandlerPath();
			$handlerClass = $webserviceObject->getHandlerClass();
			require_once $handlerPath;
			$handler = new $handlerClass($webserviceObject, $current_user, $adb, $log);
			$this->moduleMeta[$module] = $handler->getMeta();
		}
		return $this->moduleMeta[$module];
	}

	private function getWCURL($setype, $wcid) {
		$endpoint = '';
		switch ($setype) {
			case 'Accounts':
			case 'Contacts':
				$endpoint = $this->url.'/wp-admin/user-edit.php?user_id='.$wcid;
				break;
			case 'Products':
			case 'Services':
			case 'SalesOrder':
			case 'Invoice':
				$endpoint = $this->url.'/wp-admin/post.php?post='.$wcid.'&action=edit';
				break;
		}
		return $endpoint;
	}

	public function updateControlFields($moduleName, $crmid, $wcid) {
		global $adb;
		$mod = CRMEntity::getInstance($moduleName);
		$upd = 'update '.$mod->table_name." set wcsyncstatus='Active'";
		$params = array();
		if (!empty($wcid)) {
			$upd .= ', wccode=?, wcurl=?';
			$params[] = $wcid;
			$params[] = $this->getWCURL($moduleName, $wcid);
		}
		$upd .= ' where ' . $mod->table_index . '=?';
		$params[] = $crmid;
		$adb->pquery($upd, $params);
	}

	public function updateDeleteFields($setype, $crmid) {
		global $adb;
		$mod = CRMEntity::getInstance($setype);
		$adb->pquery('update '.$mod->table_name.' set wcdeleted=?,wcdeletedon=? where '.$mod->table_index.'=?', array(1, date('Y-m-d H:i:s'), $crmid));
	}

	public function getPropertiesToWC($change) {
		global $adb, $current_user, $site_URL;
		$wcprops = array();
		$cbfrommodule = $change['module'];
		$cbfrom = CRMEntity::getInstance($cbfrommodule);
		$cbfromid = $change['record_id'];
		$cbfrom->retrieve_entity_info($cbfromid, $cbfrommodule);
		switch ($cbfrommodule) {
			case 'Accounts':
				if (!isset($wcprops['email'])) {
					$wcprops['email'] = $cbfrom->column_fields['email1'];
				}
				if (!isset($wcprops['accountname']) && !empty($cbfrom->column_fields['firstname'])) {
					$wcprops['accountname'] = decode_html($cbfrom->column_fields['firstname']);
				}
				$wcprops['billing'] = array(
					'first_name' => $wcprops['accountname'],
					'last_name' => '',
					'company' => isset($wcprops['accountname']) ? $wcprops['accountname'] : (empty($wcprops['account_id']) ? '' : getAccountName($wcprops['account_id'])),
					'address_1' => isset($wcprops['bill_street']) ? $wcprops['bill_street'] : '',
					'address_2' => '',
					'city' => isset($wcprops['bill_city']) ? $wcprops['bill_city'] : '',
					'state' => isset($wcprops['bill_state']) ? $wcprops['bill_state'] : '',
					'postcode' => isset($wcprops['bill_code']) ? $wcprops['bill_code'] : '',
					'country' => isset($wcprops['bill_country']) ? $wcprops['bill_country'] : '',
					'email' => isset($wcprops['email']) ? $wcprops['email'] : (empty($wcprops['email1']) ? '' : $wcprops['email1']),
					'phone' => isset($wcprops['phone']) ? $wcprops['phone'] : '',
				);
				$wcprops['shipping'] = array(
					'first_name' => $wcprops['first_name'],
					'last_name' => '',
					'company' => $wcprops['billing']['company'],
					'address_1' => isset($wcprops['ship_street']) ? $wcprops['ship_street'] : '',
					'address_2' => '',
					'city' => isset($wcprops['ship_city']) ? $wcprops['ship_city'] : '',
					'state' => isset($wcprops['ship_state']) ? $wcprops['ship_state'] : '',
					'postcode' => isset($wcprops['ship_code']) ? $wcprops['ship_code'] : '',
					'country' => isset($wcprops['ship_country']) ? $wcprops['ship_country'] : '',
					'email' => $wcprops['billing']['email'],
					'phone' => $wcprops['billing']['phone'],
				);
				break;
			case 'Contacts':
				if (!isset($wcprops['email'])) {
					$wcprops['email'] = $cbfrom->column_fields['email'];
				}
				if (!isset($wcprops['first_name'])) {
					$wcprops['first_name'] = decode_html($cbfrom->column_fields['firstname']);
				}
				if (!isset($wcprops['last_name'])) {
					$wcprops['last_name'] = decode_html($cbfrom->column_fields['lastname']);
				}
				$wcprops['billing'] = array(
					'first_name' => $wcprops['first_name'],
					'last_name' => $wcprops['last_name'],
					'company' => isset($wcprops['accountname']) ? $wcprops['accountname'] : (empty($wcprops['account_id']) ? '' : getAccountName($wcprops['account_id'])),
					'address_1' => isset($wcprops['mailingstreet']) ? $wcprops['mailingstreet'] : '',
					'address_2' => '',
					'city' => isset($wcprops['mailingcity']) ? $wcprops['mailingcity'] : '',
					'state' => isset($wcprops['mailingstate']) ? $wcprops['mailingstate'] : '',
					'postcode' => isset($wcprops['mailingzip']) ? $wcprops['mailingzip'] : '',
					'country' => isset($wcprops['mailingcountry']) ? $wcprops['mailingcountry'] : '',
					'email' => isset($wcprops['email']) ? $wcprops['email'] : '',
					'phone' => isset($wcprops['phone']) ? $wcprops['phone'] : '',
				);
				$wcprops['shipping'] = array(
					'first_name' => $wcprops['first_name'],
					'last_name' => $wcprops['last_name'],
					'company' => $wcprops['billing']['company'],
					'address_1' => isset($wcprops['otherstreet']) ? $wcprops['otherstreet'] : '',
					'address_2' => '',
					'city' => isset($wcprops['othercity']) ? $wcprops['othercity'] : '',
					'state' => isset($wcprops['otherstate']) ? $wcprops['otherstate'] : '',
					'postcode' => isset($wcprops['otherzip']) ? $wcprops['otherzip'] : '',
					'country' => isset($wcprops['othercountry']) ? $wcprops['othercountry'] : '',
					'email' => $wcprops['billing']['email'],
					'phone' => $wcprops['billing']['phone'],
				);
				break;
			case 'Products':
				if (!isset($wcprops['name'])) {
					$wcprops['name'] = decode_html($cbfrom->column_fields['productname']);
				}
				if (!isset($wcprops['regular_price'])) {
					$wcprops['regular_price'] = $cbfrom->column_fields['unit_price'];
				}
				if (!isset($wcprops['status'])) {
					$wcprops['status'] = ($cbfrom->column_fields['wcsyncstatus']=='Published' ? 'publish' : 'draft');
				}
				if (vtlib_isModuleActive('wcProductCategory')) {
					$wcCatEntityTable = CRMEntity::getcrmEntityTableAlias('wcProductCategory');
					$cats = $adb->pquery(
						'select vtiger_wcproductcategory.wccode
						from vtiger_wcproductcategory
						INNER JOIN '.$wcCatEntityTable.' ON vtiger_crmentity.crmid = vtiger_wcproductcategory.wcproductcategoryid
						INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid=vtiger_crmentity.crmid OR vtiger_crmentityrel.crmid=vtiger_crmentity.crmid)
						WHERE vtiger_crmentity.deleted=0 AND (vtiger_crmentityrel.relcrmid=? OR vtiger_crmentityrel.crmid=?)',
						array($cbfromid, $cbfromid)
					);
					$categories = array();
					foreach ($cats as $cat) {
						$categories[] = array('id' => $cat['wccode']);
					}
					if (!empty($categories)) {
						$wcprops['categories'] = $categories;
					}
				}
				if (vtlib_isModuleActive('wcProductImage')) {
					$images = vtws_query(
						"select wcpiname,wcpialt,wcpimage from wcProductImage where Products.id='".vtws_getEntityId('Products')."x$cbfromid';",
						$current_user
					);
					$wcprops['images'] = array();
					foreach ($images as $image) {
						$wcprops['images'][] = array(
							'name' => $image['wcpiname'],
							'src' => $image['wcpimagefullpath'],
							'alt' => $image['wcpialt'],
						);
					}
				} else {
					$query = 'select vtiger_attachments.name, vtiger_attachments.type, vtiger_attachments.attachmentsid, vtiger_attachments.path
						from vtiger_attachments
						inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
						inner join vtiger_seattachmentsrel on vtiger_attachments.attachmentsid=vtiger_seattachmentsrel.attachmentsid
						where (vtiger_crmentity.setype LIKE "%Image" or vtiger_crmentity.setype LIKE "%Attachment") and deleted=0 and vtiger_seattachmentsrel.crmid=?';
					$result_image = $adb->pquery($query, array($cbfromid));
					$wcprops['images']=array();
					while ($image = $adb->fetch_array($result_image)) {
						$wcprops['images'][] = array(
							'src' => $site_URL.'/'.$image['path'].$image['attachmentsid'].'_'.$image['name'],
						);
					}
				}
				if (count($wcprops['images'])==0) {
					unset($wcprops['images']);
				}
				$wcprops['meta_data'] = [
					['key' => '_wpm_gtin_code',
					'value' => $cbfrom->column_fields['serial_no'],],
				];
				break;
			case 'Services':
				if (!isset($wcprops['name'])) {
					$wcprops['name'] = decode_html($cbfrom->column_fields['servicename']);
				}
				if (!isset($wcprops['regular_price'])) {
					$wcprops['regular_price'] = $cbfrom->column_fields['unit_price'];
				}
				if (!isset($wcprops['status'])) {
					$wcprops['status'] = ($cbfrom->column_fields['wcsyncstatus']=='Published' ? 'publish' : 'pending');
				}
				if (vtlib_isModuleActive('wcProductCategory')) {
					$wcCatEntityTable = CRMEntity::getcrmEntityTableAlias('wcProductCategory');
					$cats = $adb->pquery(
						'select vtiger_wcproductcategory.wccode
						from vtiger_wcproductcategory
						INNER JOIN '.$wcCatEntityTable.' ON vtiger_crmentity.crmid = vtiger_wcproductcategory.wcproductcategoryid
						INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid=vtiger_crmentity.crmid OR vtiger_crmentityrel.crmid=vtiger_crmentity.crmid)
						WHERE vtiger_crmentity.deleted=0 AND (vtiger_crmentityrel.relcrmid=? OR vtiger_crmentityrel.crmid=?)',
						array($cbfromid, $cbfromid)
					);
					$categories = array();
					foreach ($cats as $cat) {
						$categories[] = array('id' => $cat['wccode']);
					}
					if (!empty($categories)) {
						$wcprops['categories'] = $categories;
					}
				}
				if (vtlib_isModuleActive('wcProductImage')) {
					$images = vtws_query(
						"select wcpiname,wcpialt,wcpimage from wcProductImage where Services.id='".vtws_getEntityId('Services')."x$cbfromid';",
						$current_user
					);
					$wcprops['images'] = array();
					foreach ($images as $image) {
						$wcprops['images'][] = array(
							'name' => $image['wcpiname'],
							'src' => $image['wcpimagefullpath'],
							'alt' => $image['wcpialt'],
						);
					}
				} else {
					$images = cbws_getrecordimageinfo($cbfromid, $current_user);
					$wcprops['images']=array();
					foreach ($images['images'] as $image) {
						$wcprops['images'][] = array(
							'name' => $image['name'],
							'src' => $image['fullpath'],
						);
					}
				}
				if (count($wcprops['images'])==0) {
					unset($wcprops['images']);
				}
				$wcprops['meta_data'] = [
					['key' => '_wpm_gtin_code',
					'value' => $cbfrom->column_fields['serial_no'],],
				];
				break;
			case 'wcProductCategory':
				if (!isset($wcprops['name'])) {
					$wcprops['name'] = decode_html($cbfrom->column_fields['category_name']);
				}
				if (!isset($wcprops['slug'])) {
					$wcprops['slug'] = decode_html($cbfrom->column_fields['slug']);
				}
				if (!isset($wcprops['description'])) {
					$wcprops['description'] = decode_html($cbfrom->column_fields['description']);
				}
				if (!isset($wcprops['display'])) {
					$wcprops['display'] = $cbfrom->column_fields['display'];
				}
				if (!isset($wcprops['parent']) && !empty($cbfrom->column_fields['parent_category'])) {
					$p = getSingleFieldValue('vtiger_wcproductcategory', 'wccode', 'wcproductcategoryid', $cbfrom->column_fields['parent_category']);
					if (!empty($p)) {
						$wcprops['parent'] = $p;
					}
				}
				break;
			case 'SalesOrder':
			case 'Invoice':
				// not supported yet
				break;
			default:
				break;
		}
		$bmapname = $cbfrommodule . '2WC';
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
		if ($cbMapid) {
			$cbMap = cbMap::getMapByID($cbMapid);
			$wcprops = $cbMap->Mapping($cbfrom->column_fields, $wcprops);
		}
		return $wcprops;
	}

	public function getPropertiesFromWC($cbfrommodule, $data) {
		global $current_user, $adb;
		$send2cb = array();
		$cbfromid = $this->getCBIDFromEntity($cbfrommodule, $data['id']);
		if (!empty($cbfromid)) {
			$cbfrom = CRMEntity::getInstance($cbfrommodule);
			$cbfrom->retrieve_entity_info($cbfromid, $cbfrommodule);
			$cbfrom->column_fields = DataTransform::sanitizeRetrieveEntityInfo($cbfrom->column_fields, $this->getModuleMetaData($cbfrommodule));
			$send2cb = $cbfrom->column_fields;
		}
		switch ($cbfrommodule) {
			case 'Accounts':
				if (empty($send2cb['accountname'])) {
					if (!empty($data['company'])) {
						$send2cb['accountname'] = $data['company'];
					} elseif (!empty($data['firstname'])) {
						$send2cb['accountname'] = $data['firstname'];
					} else {
						$send2cb['accountname'] = 'notdefined';
					}
				}
				$checkEmpty = array(
					'email1' => 'email',
				);
				foreach ($checkEmpty as $cbfield => $wcfield) {
					if (!empty($data[$wcfield])) {
						$send2cb[$cbfield] = $data[$wcfield];
					}
				}
				$checkEmpty = array(
					'bill_street' => 'address_1',
					'bill_city' => 'city',
					'bill_state' => 'state',
					'bill_code' => 'postcode',
					'bill_country' => 'country',
				);
				foreach ($checkEmpty as $cbfield => $wcfield) {
					if (!empty($data['billing'][$wcfield])) {
						$send2cb[$cbfield] = $data['billing'][$wcfield];
					}
				}
				$checkEmpty = array(
					'ship_street' => 'address_1',
					'ship_city' => 'city',
					'ship_state' => 'state',
					'ship_code' => 'postcode',
					'ship_country' => 'country',
				);
				foreach ($checkEmpty as $cbfield => $wcfield) {
					if (!empty($data['shipping'][$wcfield])) {
						$send2cb[$cbfield] = $data['shipping'][$wcfield];
					}
				}
				$checkShippingEmpty = array(
					'ship_street' => 'bill_street',
					'ship_city' => 'bill_city',
					'ship_state' => 'bill_state',
					'ship_code' => 'bill_code',
					'ship_country' => 'bill_country',
				);
				foreach ($checkShippingEmpty as $dstfield => $orgfield) {
					if (empty($send2cb[$dstfield]) && !empty($send2cb[$orgfield])) {
						$send2cb[$dstfield] = $send2cb[$orgfield];
					}
				}
				break;
			case 'Contacts':
				if (empty($send2cb['account_id']) && !empty($data['company'])) {
					$rs = $adb->pquery(
						'select accountid
						from vtiger_account
						inner join vtiger_crmobject on vtiger_crmobject.crmid=accountid
						where vtiger_crmobject.deleted=0 and accountname=?',
						array($data['company'])
					);
					if ($rs && $adb->num_rows($rs)>0) {
						$send2cb['account_id'] = $adb->query_result($rs, 0, 'accountid');
					}
				}
				$checkEmpty = array(
					'email' => 'email',
					'firstname' => 'first_name',
					'lastname' => 'last_name',
				);
				foreach ($checkEmpty as $cbfield => $wcfield) {
					if (empty($send2cb[$cbfield]) && !empty($data[$wcfield])) {
						$send2cb[$cbfield] = $data[$wcfield];
					}
				}
				$checkEmpty = array(
					'mailingstreet' => 'address_1',
					'mailingcity' => 'city',
					'mailingstate' => 'state',
					'mailingzip' => 'postcode',
					'mailingcountry' => 'country',
				);
				foreach ($checkEmpty as $cbfield => $wcfield) {
					if (empty($send2cb[$cbfield]) && !empty($data['billing'][$wcfield])) {
						$send2cb[$cbfield] = $data['billing'][$wcfield];
					}
				}
				$checkEmpty = array(
					'otherstreet' => 'address_1',
					'othercity' => 'city',
					'otherstate' => 'state',
					'otherzip' => 'postcode',
					'othercountry' => 'country',
				);
				foreach ($checkEmpty as $cbfield => $wcfield) {
					if (empty($send2cb[$cbfield]) && !empty($data['shipping'][$wcfield])) {
						$send2cb[$cbfield] = $data['shipping'][$wcfield];
					}
				}
				$checkShippingEmpty = array(
					'otherstreet' => 'mailingstreet',
					'othercity' => 'mailingcity',
					'otherstate' => 'mailingstate',
					'otherzip' => 'mailingzip',
					'othercountry' => 'mailingcountry',
				);
				foreach ($checkShippingEmpty as $dstfield => $orgfield) {
					if (empty($send2cb[$dstfield]) && !empty($send2cb[$orgfield])) {
						$send2cb[$dstfield] = $send2cb[$orgfield];
					}
				}
				break;
			case 'Products':
				$checkEmpty = array(
					'productname' => 'name',
					'unit_price' => 'regular_price',
					'qtyinstock' => 'stock_quantity',
				);
				foreach ($checkEmpty as $cbfield => $wcfield) {
					if (!empty($data[$wcfield])) {
						$send2cb[$cbfield] = $data[$wcfield];
					}
				}
				if (!empty($data['status'])) {
					$data['wcsyncstatus'] = ($data['status']=='publish' ? 'Published' : 'Active');
				}
				if (!empty($data['meta_data'])) {
					$key = array_search('_wpm_gtin_code', array_column($data['meta_data'], 'key'));
					if ($key) {
						$send2cb['serial_no'] = $data['meta_data'][$key]['value'];
					}
				}
				break;
			case 'Services':
				$checkEmpty = array(
					'servicename' => 'name',
					'unit_price' => 'regular_price',
				);
				foreach ($checkEmpty as $cbfield => $wcfield) {
					if (!empty($data[$wcfield])) {
						$send2cb[$cbfield] = $data[$wcfield];
					}
				}
				if (!empty($data['status'])) {
					$data['wcsyncstatus'] = ($data['status']=='publish' ? 'Published' : 'Active');
				}
				if (!empty($data['meta_data'])) {
					$key = array_search('_wpm_gtin_code', array_column($data['meta_data'], 'key'));
					if ($key) {
						$send2cb['serial_no'] = $data['meta_data'][$key]['value'];
					}
				}
				break;
			case 'wcProductCategory':
				// not supported yet
				break;
			case 'SalesOrder':
			case 'Invoice':
				$checkEmpty = array(
					'subject' => 'number',
					'invoicedate' => 'date_created',
					'duedate' => 'date_created',
					'date_paid' => 'date_paid',
					'transaction_id' => 'transaction_id',
					'payment_method_title' => 'payment_method_title',
					'description' => 'customer_note',
					'hdnGrandTotal' => 'total',
					'hdnTaxType' => 'group',
					'hdnDiscountAmount' => 'discount_total',
					'shipping_handling_charge' => 'shipping_total',
				);
				foreach ($checkEmpty as $cbfield => $wcfield) {
					if (!empty($data[$wcfield])) {
						$send2cb[$cbfield] = $data[$wcfield];
					}
				}
				$send2cb['currency_id'] = getCurrencyId($data['currency']);
				$send2cb['account_id'] = $this->getCBIDFromEntity('Accounts', $data['customer_id']);
				$send2cb['contact_id'] = $this->getCBIDFromEntity('Contacts', $data['customer_id']);
				$checkEmpty = array(
					'bill_street' => 'address_1',
					'bill_city' => 'city',
					'bill_state' => 'state',
					'bill_code' => 'postcode',
					'bill_country' => 'country',
				);
				foreach ($checkEmpty as $cbfield => $wcfield) {
					if (empty($send2cb[$cbfield]) && !empty($data['billing'][$wcfield])) {
						$send2cb[$cbfield] = $data['billing'][$wcfield];
					}
				}
				$checkEmpty = array(
					'ship_street' => 'address_1',
					'ship_city' => 'city',
					'ship_state' => 'state',
					'ship_code' => 'postcode',
					'ship_country' => 'country',
				);
				foreach ($checkEmpty as $cbfield => $wcfield) {
					if (empty($send2cb[$cbfield]) && !empty($data['shipping'][$wcfield])) {
						$send2cb[$cbfield] = $data['shipping'][$wcfield];
					}
				}
				$checkShippingEmpty = array(
					'ship_street' => 'bill_street',
					'ship_city' => 'bill_city',
					'ship_state' => 'bill_state',
					'ship_code' => 'bill_code',
					'ship_country' => 'bill_country',
				);
				foreach ($checkShippingEmpty as $dstfield => $orgfield) {
					if (empty($send2cb[$dstfield]) && !empty($send2cb[$orgfield])) {
						$send2cb[$dstfield] = $send2cb[$orgfield];
					}
				}
				$status = '';
				switch ($data['status']) {
					case 'pending':
						$status = 'Created';
						break;
					case 'processing':
						$status = 'Approved';
						break;
					case 'completed':
						$status = 'Delivered';
						break;
					case 'cancelled':
						$status = 'Cancelled';
						break;
					case 'refunded':
					case 'on-hold':
					case 'failed':
					case 'trash':
					default:
						$status = $data['status'];
						break;
				}
				$send2cb['sostatus'] = empty($data['set_paid']) ? $status : 'Paid';
				$send2cb['invoicestatus'] = empty($data['set_paid']) ? $status : 'Paid';
				$litems = array();
				foreach ($data['line_items'] as $litem) {
					$li = $this->getCBIDFromEntity('Products', $litem['product_id']);
					if ($li=='') {
						$li = $this->getCBIDFromEntity('Services', $litem['product_id']);
					}
					$litems[] = array(
						'productid' => $li,
						'comment' => '',
						'qty' => $litem['quantity'],
						'listprice' => $litem['price'],
						'discount' => 0,  // 0 no discount, 1 discount
						'discount_type' => 'amount',  //  amount/percentage
						'discount_percentage' => 0,  // not needed nor used if type is amount
						'discount_amount' => 0,  // not needed nor used if type is percentage
					);
				}
				$send2cb['pdoInformation'] = $litems;
				break;
			default:
				break;
		}
		$bmapname = 'WC2'.$cbfrommodule;
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
		if ($cbMapid) {
			$cbMap = cbMap::getMapByID($cbMapid);
			$data['wccode'] = $data['id'];
			$data['module'] = $cbfrommodule;
			unset($data['record_id']);
			$send2cb = $cbMap->Mapping($data, $send2cb);
			$send2cb['record_id'] = $cbfromid;
		}
		if (empty($send2cb['wccode'])) {
			$send2cb['wccode'] = $data['id'];
		}
		if (empty($send2cb['assigned_user_id'])) {
			$send2cb['assigned_user_id'] = $current_user->id;
		}
		if (!empty($cbfromid)) {
			$send2cb['id'] = $cbfromid;
		}
		return $send2cb;
	}

	private function getWCIDFromEntity($module, $crmid) {
		global $adb, $current_user;
		$queryGenerator = new QueryGenerator($module, $current_user);
		$queryGenerator->setFields(array('wccode','wcsyncstatus','wcdeleted'));
		$queryGenerator->addCondition('id', $crmid, 'e');
		$query = $queryGenerator->getQuery();
		$crmtbl = CRMEntity::getcrmEntityTableAlias($module, true);
		$query = str_ireplace($crmtbl.'.deleted=0 AND', '', $query); // for deleted records
		$rs = $adb->pquery($query, array());
		$wcid = '';
		if ($rs && $adb->num_rows($rs)>0) {
			$sw = $adb->query_result($rs, 0, 'wcsyncstatus');
			$dl = $adb->query_result($rs, 0, 'wcdeleted');
			if ($sw != 'Inactive' && $dl!='1') {
				$wcid = $adb->query_result($rs, 0, 'wccode');
				if (empty($wcid)) {
					$wcid = 'CREATEIT';
				}
			}
		}
		return $wcid;
	}

	private function getCBIDFromEntity($module, $wcid) {
		global $adb, $current_user;
		$queryGenerator = new QueryGenerator($module, $current_user);
		$queryGenerator->setFields(array('id'));
		$queryGenerator->addCondition('wccode', $wcid, 'e');
		$query = $queryGenerator->getQuery();
		$rs = $adb->pquery($query, array());
		$cbid = '';
		if ($rs && $adb->num_rows($rs)>0) {
			$cbid = $adb->query_result($rs, 0, 0);
		}
		return $cbid;
	}

	public function logMessage($operation, $message, $data, $result, $error = true) {
		if (self::DEBUG) {
			$information = array(
				'info' => $data
			);
			$key = $error ? 'error' : 'result';
			$information[$key] = '['.$operation.']: ' . $message;
			if (!empty($result)) {
				$information['response'] = $result;
			}
			$this->messagequeue->sendMessage($key.'log', 'woocommerce', 'logmanager', 'Event', 'P:S', 0, 32000000, 0, 0, print_r($information, true));
		}
	}
}
?>
