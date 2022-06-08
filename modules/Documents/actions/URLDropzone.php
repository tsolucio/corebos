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
		$noteurl = vtlib_purify($_REQUEST['url']);
		$notetitle = $noteurl;
		if (!filter_var($noteurl, FILTER_VALIDATE_URL)) {
			// we try as HTML for drag and drop
			$dom = new DOMDocument();
			$parse = @$dom->loadHTML($noteurl);
			$error = true;
			if ($parse) {
				$links = $dom->getElementsByTagName('a');
				foreach ($links as $link) {
					if (!empty($link->getAttribute('href'))) {
						$noteurl = $link->getAttribute('href');
						if (empty($link->textContent)) {
							$notetitle = $noteurl;
						} else {
							$notetitle = $link->textContent;
						}
						$error = false;
					}
					break;
				}
			}
			if ($error) {
				header('HTTP/1.1 415 Invalid URL specified');
				die();
			}
		}
		$element = array(
			'notes_title' => $notetitle,
			'filename'=> $noteurl,
			'filelocationtype'=> 'E',
			'filedownloadcount'=> 0,
			'filestatus'=> 1,
			'assigned_user_id'=> vtws_getEntityId('Users').'x'.$current_user->id
		);
		if (!empty($_REQUEST['fromrecord']) && is_numeric($_REQUEST['fromrecord'])) {
			$cleanID = vtlib_purify($_REQUEST['fromrecord']);
			$element['relations'] = vtws_getEntityId(getSalesEntityType($cleanID)).'x'.$cleanID;
		}
		$response = vtws_create('Documents', $element, $current_user);
		echo json_encode($response);
	}
}
?>