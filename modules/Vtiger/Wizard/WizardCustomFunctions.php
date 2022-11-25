<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of coreBOS Customizations.
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
require_once 'include/ListView/GridUtils.php';

class WizardCustomFunctions {

	public function CustomOfferDetail() {
		require_once 'include/Webservices/Create.php';
		require_once 'include/Webservices/Revise.php';
		global $adb, $current_user;
		$data = json_decode(vtlib_purify($_REQUEST['data']), true);
		if ($data) {
			$masterid = isset($data['masterid']) ? $data['masterid'] : false;
			$records = $this->GetSession();
			$cnod = $adb->getColumnNames('vtiger_cbofferdetail');
			if (!$masterid || empty($records) || !in_array('related_product', $cnod) || !in_array('cboffers_relation', $cnod)) {
				return false;
			}
			$productid = array_keys($records[-1]);
			try {
				$UsersTabid = vtws_getEntityId('Users');
				$ProductComponentId = vtws_getEntityId('ProductComponent');
				if (!isset($records['wizard']['parentoffer'])) {
					$od = vtws_create('cbOfferDetail', array(
						'related_product' => $productid[0],
						'cboffers_relation' => $masterid,
						'assigned_user_id' => $UsersTabid.'x'.$current_user->id
					), $current_user);
					coreBOS_Session::set('DuplicatedRecords^wizard^parentoffer', $od['id']);
					$offerid = $od['id'];
				} else {
					$offerid = $records['wizard']['parentoffer'];
				}
				foreach ($records as $key => $pcs) {
					if ($key == -1) {
						continue;
					}
					foreach ($pcs as $pid) {
						$seType = getSalesEntityType($pid);
						if ($seType != 'ProductComponent') {
							continue;
						}
						vtws_revise(array(
							'id' => $ProductComponentId.'x'.$pid,
							'related_cbofferdetail' => $offerid
						), $current_user);
					}
				}
				return true;
			} catch (Throwable $e) {
				return false;
			}
		}
		return false;
	}

	public function Create_ProductComponent() {
		global $current_user;
		$UsersTabid = vtws_getEntityId('Users');
		$ProductsTabid = vtws_getEntityId('Products');
		$data = json_decode($_REQUEST['data'], true);
		$fromProduct = $data[0][0];
		unset($data[0]);
		$target = array();
		if (isset($data)) {
			foreach ($data as $ids) {
				foreach ($ids as $id) {
					$target[] = array(
						'elementType' => $this->module,
						'referenceId' => '',
						'searchon' => '',
						'element' => array(
							'frompdo' => $ProductsTabid.'x'.$fromProduct,
							'topdo' => $ProductsTabid.'x'.$id,
							'assigned_user_id' => $UsersTabid.'x'.$current_user->id
						)
					);
				}
			}
		}
		return $target;
	}

	public function Create_PurchaseOrder() {
		global $current_user;
		$UsersTabid = vtws_getEntityId('Users');
		$VendorTabid = vtws_getEntityId('Vendors');
		$data = json_decode($_REQUEST['data'], true);
		$target = array();
		$element = array();
		foreach ($data as $id => $relids) {
			$vendorname = getEntityName('Vendors', $id);
			$target[] = array(
				'elementType' => $this->module,
				'referenceId' => 'entity_id_'.$id,
				'searchon' => '',
				'element' => array(
					'vendor_id' => $VendorTabid.'x'.$id,
					'subject' => 'Quotes by ('.$vendorname[$id].')',
					'postatus' => 'Created',
					'bill_street' => '-',
					'ship_street' => '-',
					'assigned_user_id' => $UsersTabid.'x'.$current_user->id
				)
			);
			foreach ($relids as $rid) {
				$target[] = array(
					'elementType' => 'InventoryDetails',
					'referenceId' => '',
					'searchon' => 'id',
					'element' => array(
						'related_to' => '@{entity_id_'.$id.'}',
						'id' => $rid,
					)
				);
			}
		}
		return $target;
	}

	public function CreatePCMorsettiera() {
		//special use case
		require_once 'include/Webservices/Create.php';
		global $adb, $current_user;
		$step = vtlib_purify($_REQUEST['step']);
		$DuplicatedRecords = coreBOS_Session::get('DuplicatedRecords');
		$frompdo = array_values($DuplicatedRecords[-1]);
		$topdo = array_values($DuplicatedRecords[$step-1]);
		$st = $step-1;
		if (!empty($frompdo[0]) && !empty($topdo[0])) {
			try {
				$UsersTabid = vtws_getEntityId('Users');
				$pc = vtws_create('ProductComponent', array(
					'frompdo' => $frompdo[0],
					'topdo' => end($topdo),
					'assigned_user_id' => $UsersTabid.'x'.$current_user->id
				), $current_user);
				if (isset($pc['id'])) {
					$id = explode('x', $pc['id']);
					//save this for the next step "frompdo"
					coreBOS_Session::set('DuplicatedRecords^'.$st.'^parentpc', $id[1]);
					return true;
				}
				return false;
			} catch (Throwable $e) {
				return false;
			}
		}
		return false;
	}

	public function CreatePCMorsetti() {
		//special use case
		require_once 'include/Webservices/Create.php';
		require_once 'include/Webservices/MassCreate.php';
		global $adb, $current_user;
		$step = vtlib_purify($_REQUEST['step']);
		$data = json_decode($_REQUEST['data'], true);
		$records = $this->GetSession();
		$frompdo = array_values($records[$step-2]);
		$UsersTabid = vtws_getEntityId('Users');
		$ProductsTabid = vtws_getEntityId('Products');
		$PCTabid = vtws_getEntityId('ProductComponent');
		$target = array();
		foreach ($data as $page) {
			foreach ($page as $id) {
				$target[] = array(
					'elementType' => 'ProductComponent',
					'referenceId' => '',
					'searchon' => '',
					'element' => array(
						'frompdo' => $ProductsTabid.'x'.end($frompdo),
						'topdo' => $ProductsTabid.'x'.$id,
						'rel_pc' => $PCTabid.'x'.$records[$step-2]['parentpc'],
						'assigned_user_id' => $UsersTabid.'x'.$current_user->id
					)
				);
			}
		}
		MassCreate($target, $current_user);
		$st = $step-2;
		return true;
	}
}
?>