<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************/
require_once 'include/Webservices/upsert.php';
class ImportTiddlers_Action extends CoreBOS_ActionController {

	public function Save() {
		global $current_user;
		$tiddler = $_REQUEST['url'];
		$clean = new SimpleXMLElement($tiddler);
		$tiddlers = substr(urldecode($clean['href']), strlen('data:text/vnd.tiddler,'));
		if (substr($tiddlers, 0, 1) == '[') {
			$tiddlers = json_decode($tiddlers, true);
		} else {
			$tiddlers = json_decode('['.$tiddlers.']', true);
		}
		echo json_encode($tiddlers);
		foreach ((array) $tiddlers as $tid) {
			$keys = implode(',', array_keys($tid));
			$tid['assigned_user_id'] = vtws_getEntityId('Users').'x'.$current_user->id;
			if (!empty($tid['module']) && vtlib_isModuleActive($tid['module'])) {
				vtws_upsert($tid['module'], $tid, $keys, $keys, $current_user);
			}
		}
	}
}
?>