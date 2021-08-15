<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : ActionController
 *  Version      : 5.4.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

require_once 'vtlib/Vtiger/controllers/Controller.php';

class CoreBOS_ActionController extends CoreBOS_Controller {

	protected $request;

	/**
	 * Constructor method, calls another controller method if it exists
	 *
	 * @param Vtiger_Request $request sanitized REQUEST parameters
	 */
	public function __construct(Vtiger_Request $request) {
		$this->request = $request;
		$method = $request->get('method');
		if ($method != '') {
			if (method_exists($this, $method)) {
				$this->$method();
			} else {
				throw new InvalidArgumentException('Method does not Exist', 404);
			}
		} elseif (method_exists($this, 'main')) {
			$this->main();
		} else {
			throw new InvalidArgumentException('Method does not Exist', 404);
		}
	}
}