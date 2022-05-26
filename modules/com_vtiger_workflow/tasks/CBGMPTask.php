<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'include/integrations/gmp/gmp.php';

class CBGMPTask extends VTTask {
	public $executeImmediately = true;
	public $queable = true;
	private $gmp_url = 'https://www.google-analytics.com/collect?';

	public function getFieldNames() {
		return array('url_query');
	}

	public function doTask(&$entity) {
		global $logbg;
		$gmp = new corebos_gmp();
		list($ent, $ent_id) = explode('x', $entity->getId());
		$entype = getSalesEntityType($ent_id);
		$acc_id = getRelatedAccountContact($entity->getId(), 'Accounts');
		if (!empty($acc_id) && !empty($this->url_query) && $gmp->isActive()) {
			$gmpsettings = $gmp->getSettings();
			if (!empty($gmpsettings['gid'])) {
				$focus_acc = CRMEntity::getInstance('Accounts');
				$focus_acc->retrieve_entity_info($acc_id, 'Accounts');
				$GAID = $gmpsettings['gid'];
				$version = $gmpsettings['gversion'];
				$conn = $this->gmp_url."v=$version&tid=$GAID&cid={$focus_acc->column_fields['ga_clientid']}&ds=crm&".$this->url_query;
				$url = getMergedDescription($conn, $ent_id, $entype);
				$url = getMergedDescription($url, $acc_id, 'Accounts');
				$url = vtlib_purify($url);

				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_exec($curl);
				$logbg->debug('Send to GMP: '.$url);
			}
		}
	}
}
?>