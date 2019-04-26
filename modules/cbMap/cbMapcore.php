<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : Business Mappings
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

require_once 'modules/cbMap/libs/crXml.php';

class cbMapcore {
	private $Map;

	public function __construct($map) {
		$this->Map = $map;
	}

	public static function isXML($xml) {
		if (empty($xml)) {
			return 'Empty XML string';
		}
		libxml_use_internal_errors(true);
		$doc = new DOMDocument('1.0', 'utf-8');
		$doc->loadXML($xml);
		$errors = libxml_get_errors();
		if (empty($errors)) {
			return true;
		}

		$error = $errors[0];
		if ($error->level < 3) {
			return true;
		}

		$explodedxml = explode('r', $xml);
		$badxml = $explodedxml[($error->line)-1];

		$message = $error->message . ' at line ' . $error->line . '. Bad XML: ' . htmlentities($badxml);
		return $message;
	}

	public function getMap() {
		return $this->Map;
	}

	public function getXMLContent() {
		$xmlcontent=html_entity_decode($this->Map->column_fields['content'], ENT_QUOTES, 'UTF-8');
		if (self::isXML($xmlcontent)) {
			return simplexml_load_string($xmlcontent, null, LIBXML_NOCDATA);
		} else {
			return null;
		}
	}
}
?>