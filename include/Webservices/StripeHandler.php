<?php
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'include/integrations/stripepayment/stripepayment.php';

function StripeHandler($method, $data, $stripeObject, $user) {
	if (empty($method)) {
		throw new WebServiceException(WebServiceErrorCode::$VALIDATION_FAILED, 'Method specified is empty');
	}
	if (empty($data)) {
		throw new WebServiceException(WebServiceErrorCode::$VALIDATION_FAILED, 'Data specified is empty');
	}
	$stripe = new corebos_stripepayment();
	switch ($method) {
		case 'payment_intent':
			$data = json_decode($data, true);
			$ret = $stripe->createPaymentIntent($data);
			break;
		default:
			$ret = 'MISSING_METHOD';
	}
	return $ret;
}
?>
