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
*************************************************************************************************/
require_once 'modules/cbupdater/cbupdaterWorker.php';

class woocommercechangeset extends cbupdaterWorker {

	public function applyChange() {
		global $adb;
		$em = new VTEventsManager($adb);
		$em->registerHandler('vtiger.entity.afterdelete', 'include/integrations/woocommerce/change2message.php', 'woocommercechange2message');
		$em->registerHandler('vtiger.entity.aftersave.final', 'include/integrations/woocommerce/change2message.php', 'woocommercechange2message');
		$cbmq = coreBOS_MQTM::getInstance();
		$cbmq->subscribeToChannel('WooCChangeChannel', 'WCChangeHandler', 'WCChangeSync', array(
			'file'=>'include/integrations/woocommerce/woocommerce.php',
			'class'=>'corebos_woocommerce',
			'method'=>'WCChangeSync'
		));
		$cbmq->subscribeToChannel('WooCChangeChannel', 'WCChangeHandler', 'WCDeleteSync', array(
			'file'=>'include/integrations/woocommerce/woocommerce.php',
			'class'=>'corebos_woocommerce',
			'method'=>'WCDeleteSync'
		));
		$cbmq->subscribeToChannel('WooCChangeChannel', 'WCChangeHandler', 'cbChangeSync', array(
			'file'=>'include/integrations/woocommerce/woocommerce.php',
			'class'=>'corebos_woocommerce',
			'method'=>'cbChangeSync'
		));
		$rs = $adb->query("select 1 from vtiger_notificationdrivers where type='wcintegration' and functionname='wcnotification'");
		if ($rs && $adb->num_rows($rs)==0) {
			$this->ExecuteQuery(
				"INSERT INTO vtiger_notificationdrivers (type,path,functionname) VALUES ('wcintegration','include/integrations/woocommerce/notification.php','wcnotification')"
			);
		}
		$wcFinancialfields = array(
			'date_paid' => array(
				'label' => 'date_paid',
				'columntype'=>'date',
				'typeofdata'=>'D~O',
				'uitype'=>'5',
				'displaytype'=>'1',
				'massedit'=>'1',
			),
			'transaction_id' => array(
				'label' => 'transaction_id',
				'columntype'=>'varchar(100)',
				'typeofdata'=>'V~O',
				'uitype'=>'1',
				'displaytype'=>'1',
				'massedit'=>'1',
			),
			'payment_method_title' => array(
				'label' => 'payment_method_title',
				'columntype'=>'varchar(100)',
				'typeofdata'=>'V~O',
				'uitype'=>'1',
				'displaytype'=>'1',
				'massedit'=>'1',
			),
		);
		$wcfields=array(
			'LBL_WC_INFORMATION'=> array(
				'wcsyncstatus' => array(
					'label' => 'wcsyncstatus',
					'columntype'=>'varchar(10)',
					'typeofdata'=>'V~O',
					'uitype'=>'16',
					'displaytype'=>'1',
					'massedit'=>'1',
					'vals' => array(
						'Active',
						'Published',
						'Inactive',
					)
				),
				'wccreated' => array(
					'label' => 'wccreated',
					'columntype'=>'varchar(3)',
					'typeofdata'=>'C~O',
					'uitype'=>'56',
					'displaytype'=>'2',
					'massedit'=>'0',
				),
				'wccode' => array(
					'label' => 'wccode',
					'columntype'=>'varchar(110)',
					'typeofdata'=>'V~O',
					'uitype'=>'1',
					'displaytype'=>'1',
					'massedit'=>'0',
				),
				'wcdeleted' => array(
					'label' => 'wcdeleted',
					'columntype'=>'varchar(3)',
					'typeofdata'=>'C~O',
					'uitype'=>'56',
					'displaytype'=>'2',
					'massedit'=>'0',
				),
				'wcurl' => array(
					'label' => 'wcurl',
					'columntype'=>'varchar(183)',
					'typeofdata'=>'V~O',
					'uitype'=>'17',
					'displaytype'=>'4',
					'massedit'=>'0',
				),
				'wcdeletedon' => array(
					'label' => 'wcdeletedon',
					'columntype'=>'datetime',
					'typeofdata'=>'DT~O',
					'uitype'=>'70',
					'displaytype'=>'2',
					'massedit'=>'0',
				),
			),
		);
		$fieldLayout=array(
			'Products' => $wcfields,
			'Services' => $wcfields,
			'Accounts' => $wcfields,
			'Contacts' => $wcfields,
			'SalesOrder' => array_merge($wcfields, array('LBL_SalesOrder_FINANCIALINFO' => $wcFinancialfields)),
			'Invoice' => array_merge($wcfields, array('LBL_Invoice_FINANCIALINFO' => $wcFinancialfields)),
		);
		$this->massCreateFields($fieldLayout);
		$module = Vtiger_Module::getInstance('Invoice');
		$field = Vtiger_Field::getInstance('invoicestatus', $module);
		if ($field) {
			$field->setPicklistValues(array('refunded', 'on-hold', 'failed', 'trash'));
		}
		$module = Vtiger_Module::getInstance('SalesOrder');
		$field = Vtiger_Field::getInstance('sostatus', $module);
		if ($field) {
			$field->setPicklistValues(array('refunded', 'on-hold', 'failed', 'trash'));
		}
	}

	public function undoChange() {
		global $adb;
		$em = new VTEventsManager($adb);
		$em->unregisterHandler('woocommercechange2message');
		$cbmq = coreBOS_MQTM::getInstance();
		$cbmq->unsubscribeToChannel('WooCChangeChannel', 'WCChangeHandler', 'WCChangeSync', array(
			'file'=>'include/integrations/woocommerce/woocommerce.php',
			'class'=>'corebos_woocommerce',
			'method'=>'WCChangeSync'
		));
		$cbmq->unsubscribeToChannel('WooCChangeChannel', 'WCChangeHandler', 'WCDeleteSync', array(
			'file'=>'include/integrations/woocommerce/woocommerce.php',
			'class'=>'corebos_woocommerce',
			'method'=>'WCDeleteSync'
		));
		$cbmq->unsubscribeToChannel('WooCChangeChannel', 'WCChangeHandler', 'cbChangeSync', array(
			'file'=>'include/integrations/woocommerce/woocommerce.php',
			'class'=>'corebos_woocommerce',
			'method'=>'cbChangeSync'
		));
		$this->ExecuteQuery("DELETE FROM vtiger_notificationdrivers WHERE type='wcintegration' AND functionname='wcnotification'");
		$wcfields=array(
			'wcsyncstatus',
			'wccode',
			'wccreated',
			'wcdeleted',
			'wcurl',
			'wcdeletedon',
			'date_paid',
			'transaction_id',
			'payment_method_title',
		);
		$fieldLayout=array(
			'Products' => $wcfields,
			'Accounts' => $wcfields,
			'Contacts' => $wcfields,
			'SalesOrder' => $wcfields,
			'Invoice' => $wcfields,
		);
		$this->massHideFields($fieldLayout);
		$this->sendMsg('Changeset '.get_class($this).' undone!');
	}
}