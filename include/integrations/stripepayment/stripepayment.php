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
 *  Module    : Stripe Payment Integration
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'vendor/autoload.php';

class corebos_stripepayment {
	private $isactive = 0;
	const KEY_ISACTIVE = 'stripepayment_isactive';
	const KEY_STRIPE_API_KEY = 'stripe_key';

	public function __construct() {
		$this->initGlobalScope();
	}

	public function initGlobalScope() {
		$this->isactive = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, 0);
	}

	public function saveSettings($isactive, $stripekey) {
		coreBOS_Settings::setSetting(self::KEY_ISACTIVE, $isactive);
		coreBOS_Settings::setSetting(self::KEY_STRIPE_API_KEY, $stripekey);
	}

	public function getSettings() {
		return array(
			'stripepayment_isactive' => coreBOS_Settings::getSetting(self::KEY_ISACTIVE, ''),
			'stripe_key' => coreBOS_Settings::getSetting(self::KEY_STRIPE_API_KEY, ''),
		);
	}

	public function isActive() {
		$isactive = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, 0);
		return ($isactive==1);
	}

	public static function checkStripeConfiguration() {
		$isactive = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, '0');
		$stripekey = coreBOS_Settings::getSetting(self::KEY_STRIPE_API_KEY, '');
		return ($isactive != '0' && !empty($stripekey));
	}

	public function createPaymentIntent($data) {
		global $logbg;
		if (self::checkStripeConfiguration()) {
			if (!isset($data['email'])) {
				return 'MISSING_EMAIL';
			}
			$customerId = $this->retrieveCustomer($data);
			if (!$customerId) {
				return 'MISSING_CUSTOMER';
			}
			if (isset($customerId[0])) {
				$customerId = $customerId[0]->id;
			} else {
				$customerId = $customerId->id;
			}
			$paymentMethod = $this->getCustomerStripePaymentMethod($customerId);
			if (!empty($paymentMethod)) {
				$data['payment_method'] = $paymentMethod;
			}
			\Stripe\Stripe::setApiKey(coreBOS_Settings::getSetting(self::KEY_STRIPE_API_KEY, ''));
			try {
				$data['currency'] = 'EUR';
				$data['payment_method_types'] = ['card'];
				$data['customer'] = $customerId;
				$data['capture_method'] = 'automatic';
				unset($data['token']);
				unset($data['email']);
				$intentres = \Stripe\PaymentIntent::create($data);
				if ($intentres->status == 'requires_confirmation') {
					return $intentres->confirm();
				}
				return $intentres;
			} catch (Exception $e) {
				$logbg->debug('createPaymentIntent failed:: '. $e->getMessage());
				return $e->getMessage();
			}
		}
		return 0;
	}

	public function retrieveCustomer($data) {
		global $logbg;
		$stripe = new \Stripe\StripeClient(coreBOS_Settings::getSetting(self::KEY_STRIPE_API_KEY, ''));
		$customers = $stripe->customers->all();
		$customer = array_map(function ($key) use ($data) {
			if ($key->email == $data['email']) {
				return $key;
			}
			return false;
		}, $customers['data']);
		$customer = array_filter($customer);
		if (!$customer[0]) {
			$customer = $this->createCustomer($data);
		}
		return $customer;
	}

	public function createCustomer($data) {
		global $logbg;
		$stripe = new \Stripe\StripeClient(coreBOS_Settings::getSetting(self::KEY_STRIPE_API_KEY, ''));
		try {
			$customer = $stripe->customers->create([
				'source' => $data['token'],
				'email' => $data['email']
			]);
			return $customer;
		} catch (Exception $e) {
			$logbg->debug('createCustomer failed:: '. $e->getMessage());
			return $e->getMessage();
		}
	}

	public function getCustomerStripePaymentMethod($customerId) {
		global $logbg;
		if (self::checkStripeConfiguration()) {
			try {
				$stripe = new \Stripe\StripeClient(coreBOS_Settings::getSetting(self::KEY_STRIPE_API_KEY, ''));
				$paymentMethod = $stripe->paymentMethods->all([
					'customer' => $customerId,
					'type' => 'card',
				]);
				if (isset($paymentMethod->data[0]->id)) {
					return $paymentMethod->data[0]->id;
				}
			} catch (Exception $e) {
				$logbg->debug('getCustomerStripePaymentMethod failed:: '. $e->getMessage());
			}
		}
		return '';
	}
}
?>