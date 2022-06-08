<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module       : CobroPago
 *  Version      : 5.4.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

require_once 'include/wsClient/WSClient.php';
require_once 'include/utils/utils.php';
require 'vendor/autoload.php';
require_once 'modules/CobroPago/Pay.config.php';

function __payDoNothing() {
	// stub, as functionality is directly in file
}

use Omnipay\Omnipay;
use Omnipay\Common\CreditCard;

class Purchase {

	public $ws;

	public function __construct() {
		global $PURCHASE_CONFIG;
		$this->ws = new Vtiger_WSClient($PURCHASE_CONFIG['corebos']['server']);
		$this->ws->doLogin($PURCHASE_CONFIG['corebos']['username'], $PURCHASE_CONFIG['corebos']['accessKey']);
	}

	// get one contact with given NIF exists
	public function contactByNif($nif) {
		return $this->ws->doQuery("select * from Contacts where nif='$nif' limit 1");
	}

	public function create($data) {
		$contact = array(
			'nif' => $data['nif'],
			'firstname' => $data['firstname'],
			'lastname' => $data['lastname'],
			'mobile' => $data['mobile'],
			'email' => $data['email'],
			'description' => $data['moreinfo'],
		);
		$contactByNIF = $this->contactByNif($contact['nif']);
		if (empty($contactByNIF)) {
			$result = $this->ws->doCreate('Contacts', $contact);
			$contactId = $result['id'];
		} else {
			$contactId = $contactByNIF[0]['id'];
		}

		$items = json_decode($data['cartitems'], true);
		$salesorder =  array (
			'subject' => $data['subject'],
			'bill_city' => '',
			'bill_code' => '',
			'bill_country' => '',
			'bill_pobox' => '',
			'bill_state' => '',
			'bill_street' => '',
			'account_id' => '', // XXX Optional only for mes3events
			'carrier' => null,
			'contact_id' => $contactId,
			'conversion_rate' => '1.000',
			'currency_id' => '21x1',
			'customerno' => '', // should put transaction ID here ($tid)
			'transaccion' => '',
			'description' => $data['moreinfo'],
			'duedate' => $data['fecha'],
			'enable_recurring' => '0',
			'end_period' => null,
			'exciseduty' => '0.000',
			'invoicestatus' => 'Approved',
			'payment_duration' => null,
			'pending' => null,
			'potential_id' => null,
			'vtiger_purchaseorder' => null,
			'quote_id' => null,
			'recurring_frequency' => null,
			'salescommission' => '0.000',
			'ship_city' => '',
			'ship_code' => '',
			'ship_country' => '',
			'ship_pobox' => '',
			'ship_state' => '',
			'ship_street' => '',
			'sostatus' => 'Approved',
			'start_period' => null,
			'salesorder_no' => null,
			'terms_conditions' => null,
			'discount_type_final' => 'percentage',  //  zero/amount/percentage
			'hdnDiscountAmount' => '0',  // only used if 'discount_type_final' == 'amount'
			'hdnDiscountPercent' => '0',  // only used if 'discount_type_final' == 'percentage'
			'shipping_handling_charge' => 0,
			'shtax1' => 0,   // apply this tax, MUST exist in the application with this internal taxname
			'shtax2' => 0,   // apply this tax, MUST exist in the application with this internal taxname
			'shtax3' => 0,   // apply this tax, MUST exist in the application with this internal taxname
			'adjustmentType' => 'none',  //  none/add/deduct
			'adjustment' => '0',
			'taxtype' => 'group',  // group or individual  taxes are obtained from the application
			'pdoInformation' => array(
				array(
					'productid'=>$data['pdoid'],
					'comment'=>$data['fecha'],
					'qty'=>$items[0]['quantity'],
					'listprice'=>($items[0]['amount']/1.21),
					'discount'=>0,  // 0 no discount, 1 discount
					'discount_type'=>0,  //  amount/percentage
					'discount_percentage'=>0,  // not needed nor used if type is amount
					'discount_amount'=>0,  // not needed nor used if type is percentage
				),
			),
		);
		$result = $this->ws->doCreate('SalesOrder', $salesorder);
		$salesorderId = $result['id'];

		$cobropago = array(
			'reference' => $data['subject'],
			'parent_id' => $contactId,
			'related_id' => $salesorderId,
			'register' => $data['fecha'],
			'duedate' => $data['fecha'],
			'amount' => $items[0]['amount'],
			'credit' => 1,
		);
		$result = $this->ws->doCreate('CobroPago', $cobropago);

		return $result['id'];
	}

	public function pay($cobropagoId, $options = array(), $complete = false) {
		global $PURCHASE_CONFIG, $site_URL;

		$notifyUrl = '';
		if (isset($options['notifyUrl'])) {
			$notifyUrl = $options['notifyUrl'];
		}

		$defaults = array(
			'returnUrl' => "{$site_URL}/index.php?action=DetailView&module=CobroPago&record={$cobropagoId}",
			'cancelUrl' => "{$site_URL}/index.php?action=DetailView&module=CobroPago&record={$cobropagoId}",
		);
		$options = array_merge($defaults, $options);

		$options['notifyUrl'] = "{$site_URL}/PaymentNotification.php?cpid={$cobropagoId}";

		$current_user = new Users();
		$current_user->retrieveCurrentUserInfoFromFile(1);

		if (preg_match('/^\d+$/', $cobropagoId)) {
			$describeCobroPago = $this->ws->doDescribe('CobroPago');
			$cobropagoId = $describeCobroPago['idPrefix'] . 'x' . $cobropagoId;
		}

		$gateway = Omnipay::create($PURCHASE_CONFIG['omnipay']['driver']);
		$gateway->initialize($PURCHASE_CONFIG['omnipay']);

		$cobropago = $this->ws->doRetrieve($cobropagoId);

		if (empty($cobropago) || $cobropago['paid'] || !$cobropago['credit']) {
			throw new InvalidArgumentException('CPID_ERROR');
		}

		$contactId = $cobropago['parent_id'];
		$contact = $this->ws->doRetrieve($contactId);

		$card = new CreditCard(array(
			'billingFirstName' => $contact['firstname'],
			'billingLastName' => $contact['lastname'],
			'email' => $contact['email'],
		));

		$parameters = array(
			'amount' => $cobropago['amount'],
			'transactionId' => $cobropago['reference'],
			'currency' => 'EUR',
			'card' => $card,
			'extraData' => $notifyUrl,
		);

		if ($PURCHASE_CONFIG['omnipay']['testmode']) {
			//$parameters['transactionId'] = mt_rand();
		}

		$parameters += $options;

		if ($complete) {
			$response = $gateway->completePurchase($parameters)->send();
		} else {
			$response = $gateway->purchase($parameters)->send();
		}

		if ($response->isSuccessful()) {
			$data = $response->getData();
			$date = $data['Ds_Date'];
			$dateFormat = $this->ws->doInvoke('getPortalUserDateFormat');
			switch ($dateFormat) {
				case 'yyyy-mm-dd':
					$replace = '$3-$2-$1';
					break;
				case 'dd-mm-yyyy':
					$replace = '$1-$2-$3';
					break;
				case 'mm-dd-yyyy':
					$replace = '$2-$1-$3';
					break;
			}
			$date = preg_replace('#^(\d\d)/(\d\d)/(\d\d\d\d)$#', $replace, $date);
			$cobropago['duedate'] = $date;
			$cobropago['paid'] = 1;
			$this->ws->doUpdate('CobroPago', $cobropago);
		}

		return $response;
	}

	public function notification($cobropagoId) {
		$response = $this->pay($cobropagoId, array(), true);
		$extraData = $response->getExtraData();
		if (!empty($extraData)) {
			$notifyUrl = json_decode($extraData);
			$options = array(
				'http' => array(
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query($response->getData()),
				),
			);
			$context = stream_context_create($options);
			file_get_contents($notifyUrl, false, $context);
		}
	}
}
