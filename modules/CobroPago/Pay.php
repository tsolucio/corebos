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

require_once 'modules/CobroPago/Purchase.php';

$options = array();
if (isset($_REQUEST['notify_url'])) {
	$options['notifyUrl'] = $_REQUEST['notify_url'];
}
if (isset($_REQUEST['return_url'])) {
	$options['returnUrl'] = $_REQUEST['return_url'];
}
if (isset($_REQUEST['cancel_url'])) {
	$options['cancelUrl'] = $_REQUEST['cancel_url'];
}

$purchase = new Purchase();

try {
	if (isset($_REQUEST['create']) && $_REQUEST['create']) {
		$cobropagoId = $purchase->create($_REQUEST);
	} else {
		$cobropagoId = $_REQUEST['cpid'];
	}
	$response = $purchase->pay($cobropagoId, $options);
} catch (Exception $e) {
	if ($e->getMessage() === 'CPID_ERROR') {
		$message = getTranslatedString('Invalid payment.', 'CobroPago');
	} else {
		throw $e;
	}
}

if ($response !== null) {
	if ($response->isSuccessful()) {
		if (isset($options['returnUrl'])) {
			header('Location: ' . $options['returnUrl']);
			exit;
		}
		$message = getTranslatedString('Payment done.', 'CobroPago');
	} elseif ($response->isRedirect()) {
		$message = getTranslatedString('Redirecting to payment gateway...', 'CobroPago');
	} else {
		if (isset($options['cancelUrl'])) {
			header('Location: ' . $options['cancelUrl']);
			exit;
		}
		$message = getTranslatedString('Payment error.', 'CobroPago');
	}
}

require 'modules/CobroPago/Pay.tpl.php';
