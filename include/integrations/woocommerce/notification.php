<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************
 *  Module       : coreBOS WC Notification
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'include/integrations/woocommerce/woocommerce.php';

/* WC Events
	'coupon.created'
	'coupon.updated'
	'coupon.deleted'
	'coupon.restored'
	'customer.created'
	'customer.updated'
	'customer.deleted'
	'order.created'
	'order.updated'
	'order.deleted'
	'order.restored'
	'product.created'
	'product.updated'
	'product.deleted'
	'product.restored'
*/

function wcnotification($input) {
	$wc = new corebos_woocommerce();
	$wcsettings = $wc->getSettings();
	$headers = apache_request_headers();
	$signature = base64_encode(hash_hmac('sha256', $input, $wcsettings['sct'], true));
	$wcsig = isset($headers['X-WC-Webhook-Signature']) ? $headers['X-WC-Webhook-Signature'] : (isset($headers['X-Wc-Webhook-Signature']) ? $headers['X-Wc-Webhook-Signature'] : '');
	$source = isset($headers['X-WC-Webhook-Source']) ? $headers['X-WC-Webhook-Source'] : (isset($headers['X-Wc-Webhook-Source']) ? $headers['X-Wc-Webhook-Source'] : '');
	if ($wc->isActive() && trim($source, '/')==trim($wcsettings['url'], '/') && $wcsig==$signature) {
		$event = isset($headers['X-WC-Webhook-Topic']) ? $headers['X-WC-Webhook-Topic'] : (isset($headers['X-Wc-Webhook-Topic']) ? $headers['X-Wc-Webhook-Topic'] : '');
		$msg = array(
			'event' => $event,
			'data' => json_decode($input, true),
		);
		$cbmq = coreBOS_MQTM::getInstance();
		$cbmq->sendMessage('WooCChangeChannel', 'WCChangeHandler', 'cbChangeSync', 'Data', '1:M', 1, 43200, 0, 0, json_encode($msg));
	}
}