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
include_once 'include/Webservices/Create.php';

class URLDropzone_Action extends CoreBOS_ActionController {

	public function Save() {
		global $current_user;
		$url = vtlib_purify($_REQUEST['url']);
		if (!filter_var($url, FILTER_VALIDATE_URL)) {
			throw new WebServiceException(WebServiceErrorCode::$INVALID_URL, 'Invalid URL specified');
		}
		$tabid = vtws_getEntityId('Users');
		$element = array(
			'notes_title' => $url,
			'filename'=> $url,
			'filelocationtype'=> 'E',
			'filedownloadcount'=> 0,
			'filestatus'=> 1,
			'assigned_user_id'=> $tabid.'x'.$current_user->id
		);
		$response = vtws_create('Documents', $element, $current_user);
		echo json_encode($response);
	}
}
?>