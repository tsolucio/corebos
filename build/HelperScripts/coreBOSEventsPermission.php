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
 *  Module       : coreBOS Events Account/Contact Permission Control
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
class coreBOSEventsPermissionExample extends VTEventHandler {
	// uncomment one of the $typeOfPermissionOverride assignments to test the different scenarios
	private $typeOfPermissionOverride = 'none';
	//private $typeOfPermissionOverride = 'fullOverride';
	//private $typeOfPermissionOverride = 'addToUserPermission';
	//private $typeOfPermissionOverride = 'showTheseRecords';

	public function handleEvent($handlerType, $entityData) {
	}

	public function handleFilter($handlerType, $parameter) {
		global $currentModule;
		if ($handlerType == 'corebos.permissions.accessquery') {
			$module = $parameter[2];
			if ($module == 'HelpDesk') {
				$user = $parameter[3];
				$parameter[1] = $this->typeOfPermissionOverride;
				switch ($this->typeOfPermissionOverride) {
					case 'none':
						// normal behavior
						$parameter[0] = ''; // this is ignored
						break;
					case 'fullOverride':
						// show only tickets related to account Chemex (accountid=74)
						$parameter[0] = ' inner join vtiger_account as accountpermission on (vtiger_troubletickets.parent_id = accountpermission.accountid and accountpermission.accountid=74) ';
						break;
					case 'addToUserPermission':
						// all the tickets the user can access plus those related to
						//  accounts or products assigned to him
						$parameter[0] = $this->getHelpDeskAccessQuery($module, $user);
						break;
					case 'showTheseRecords':
						// show tickets related to accounts or products assigned to him
						$parameter[0] = $this->getHelpDeskAccessQuery($module, $user);
						break;
				}
			}
		} elseif ($handlerType == 'corebos.permissions.ispermitted') {
			if ($currentModule == 'HelpDesk') {
				$permission = $parameter[0];
				if ($permission == 'no') {
					$module = $parameter[1];
					$actionname = $parameter[2];
					$record_id = $parameter[3];
					$parameter[0] = $this->HelpDeskisPermitted($module, $actionname, $record_id);
				}
			}
		}
		return $parameter;
	}

	public function HelpDeskisPermitted($module, $actionname, $record_id = '') {
		global $adb, $current_user;
		if ($record_id=='' || $this->typeOfPermissionOverride=='none') {
			// permission on module so we just return what has already been calculated
			return 'no';
		}
		$ret = 'no';
		if ($this->typeOfPermissionOverride=='fullOverride') {
			// check if ticket is related to account Chemex
			$hdownerrs = $adb->pquery(
				'select 1
				from vtiger_troubletickets
				where vtiger_troubletickets.parent_id=74 and ticketid=?',
				array($record_id)
			);
			if ($hdownerrs && $adb->num_rows($hdownerrs)>0) {
				$ret = 'yes';
			}
		} elseif ($this->typeOfPermissionOverride=='addToUserPermission') {
			// check if ticket is owned by user or the related account or product is owned by user
			$hdownerrs = $adb->pquery(
				'select 1
				from vtiger_troubletickets
				inner join vtiger_crmentity as hdcrm on hdcrm.crmid = vtiger_troubletickets.ticketid
				inner join vtiger_crmentity as acccrm on acccrm.crmid = vtiger_troubletickets.parent_id
				inner join vtiger_crmentity as pdocrm on pdocrm.crmid = vtiger_troubletickets.product_id
				where (hdcrm.smownerid=? or acccrm.smownerid=? or pdocrm.smownerid=?) and ticketid=?',
				array($current_user->id,$current_user->id,$current_user->id,$record_id)
			);
			if ($hdownerrs && $adb->num_rows($hdownerrs)>0) {
				$ret = 'yes';
			}
		} else { // showTheseRecords
			// check if the related account or product is owned by user
			$hdownerrs = $adb->pquery(
				'select 1
				from vtiger_troubletickets
				inner join vtiger_crmentity as acccrm on acccrm.crmid = vtiger_troubletickets.parent_id
				inner join vtiger_crmentity as pdocrm on pdocrm.crmid = vtiger_troubletickets.product_id
				where (acccrm.smownerid=? or pdocrm.smownerid=?) and ticketid=?',
				array($current_user->id,$current_user->id,$record_id)
			);
			if ($hdownerrs && $adb->num_rows($hdownerrs)>0) {
				$ret = 'yes';
			}
		}
		return $ret;
	}

	/*
	 * Get a query that retrieves all HelpDesk ids visible for a given user using a custom
	 * permission system based on the user assigned to the related Account or Product
	 * This query is to be used inside the getNonAdminAccessControlQuery function.
	 */
	public function getHelpDeskAccessQuery($module, $user) {
		global $adb;
		$query = "select distinct vtiger_troubletickets.ticketid as id
			from vtiger_troubletickets
			inner join vtiger_crmentity on 
			(vtiger_crmentity.crmid = vtiger_troubletickets.parent_id or vtiger_crmentity.crmid = vtiger_troubletickets.product_id)
			where vtiger_crmentity.deleted=0 and (vtiger_crmentity.setype='Accounts' or vtiger_crmentity.setype='Products') and vtiger_crmentity.smownerid=".$user->id;
		return $query;
	}

	/********* BELOW IS ANOTHER SCENARIO **************/
	// Give access to all records if the user has access to the related account.
	// With the two functions below you should be able to give access to a set of accounts/contacts
	// and the user will be able to access ALL the records in the CRM that are related to them
	// independent of who they are assigned to.
	public function AccountContactRelatedisPermitted($module, $actionname, $record_id = '') {
		global $adb, $current_user;
		if ($record_id=='') { // permission on module so we just return what has already been calculated
			return 'no';
		}
		$ret = 'no';
		// get related account/contact
		$acid = getRelatedAccountContact($record_id);
		if (!empty($acid)) {
			$acownerrs = $adb->pquery('select smownerid from vtiger_crmentity where crmid=? and deleted=0', array($acid));
			if ($acownerrs && $adb->num_rows($acownerrs)>0) {
				$owner = $adb->query_result($acownerrs, 0, 0);
				if ($owner==$current_user->id) {
					$ret = 'yes';
				}
			}
		}
		return $ret;
	}

	/*
	 * Get a query that retrieves all ids visible for a given user in a given module using a custom
	 * permission system based on the user assigned to the related Account/Contact.
	 * This query is to be used inside the getNonAdminAccessControlQuery function.
	 */
	public function getAccountContactRelatedAccessQuery($module, $user) {
		global $adb;
		if (GlobalVariable::getVariable('Application_B2B', '1')) {
			$parentmodule = 'Accounts';
		} else {
			$parentmodule = 'Contacts';
		}
		$query = '';
		switch ($module) {
			case 'Accounts':
				return '';
				break;
			case 'Contacts':
				if ($parentmodule=='Contacts') {
					return '';
				} else {
					$query = 'select vtiger_contactdetails.contactid as id
						from vtiger_contactdetails
						inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.accountid
						where smownerid='.$user->id;
				}
				break;
			case 'Potentials':
				$query = "select vtiger_potential.potentialid as id
					from vtiger_potential
					inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_potential.related_to
					where setype='$parentmodule' and smownerid=".$user->id;
				break;
			case 'HelpDesk':
				$query = "select vtiger_troubletickets.ticketid as id
					from vtiger_troubletickets
					inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_troubletickets.parent_id
					where vtiger_crmentity.deleted=0 and vtiger_crmentity.setype='$parentmodule' and vtiger_crmentity.smownerid=".$user->id;
				break;
			case 'Quotes':
				if ($parentmodule=='Accounts') {
					$query = "select vtiger_quotes.quoteid as id
						from vtiger_quotes
						inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_quotes.accountid
						where setype='Accounts' and smownerid=".$user->id;
				} else {
					$query = "select vtiger_quotes.quoteid as id
						from vtiger_quotes
						inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_quotes.contactid
						where setype='Contacts' and smownerid=".$user->id;
				}
				break;
			case 'SalesOrder':
				if ($parentmodule=='Accounts') {
					$query = "select vtiger_salesorder.salesorderid as id
						from vtiger_salesorder
						inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_salesorder.accountid
						where setype='Accounts' and smownerid=".$user->id;
				} else {
					$query = "select vtiger_salesorder.salesorderid as id
						from vtiger_salesorder
						inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_salesorder.contactid
						where setype='Contacts' and smownerid=".$user->id;
				}
				break;
			case 'PurchaseOrder':
				if ($parentmodule=='Accounts') {
					return '';
				} else {
					$query = "select vtiger_purchaseorder.purchaseorderid as id
						from vtiger_purchaseorder
						inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_purchaseorder.contactid
						where setype='Contacts' and smownerid=".$user->id;
				}
				break;
			case 'Invoice':
				if ($parentmodule=='Accounts') {
					$query = "select vtiger_invoice.invoiceid as id
						from vtiger_invoice
						inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_invoice.accountid
						where setype='Accounts' and smownerid=".$user->id;
				} else {
					$query = "select vtiger_invoice.invoiceid as id
						from vtiger_invoice
						inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_invoice.contactid
						where setype='Contacts' and smownerid=".$user->id;
				}
				break;
			case 'InventoryDetails':
				$rspot = $adb->pquery("select account_id,contact_id from vtiger_inventorydetails where inventorydetailsid=?", array($crmid));
				if ($parentmodule=='Accounts') {
					$query = "select vtiger_inventorydetails.inventorydetailsid as id
						from vtiger_inventorydetails
						inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_inventorydetails.account_id
						where setype='Accounts' and smownerid=".$user->id;
				} else {
					$query = "select vtiger_inventorydetails.inventorydetailsid as id
						from vtiger_inventorydetails
						inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_inventorydetails.contact_id
						where setype='Contacts' and smownerid=".$user->id;
				}
				break;
			case 'ServiceContracts':
				$query = "select vtiger_servicecontracts.servicecontractsid as id
					from vtiger_servicecontracts
					inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_servicecontracts.sc_related_to
					where setype='$parentmodule' and smownerid=".$user->id;
				break;
			case 'Assets':
				if ($parentmodule=='Accounts') {
					$query = "select vtiger_assets.assetsid as id
						from vtiger_assets
						inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_assets.account
						where setype='Accounts' and smownerid=".$user->id;
				} else {
					return '';
				}
				break;
			case 'ProjectMilestone':
				$query = "select vtiger_projectmilestone.projectmilestoneid as id
					from vtiger_projectmilestone
					inner join vtiger_project on vtiger_project.projectid = vtiger_projectmilestone.projectid
					inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_project.linktoaccountscontacts
					where setype='$parentmodule' and smownerid=".$user->id;
				break;
			case 'ProjectTask':
				$query = "select vtiger_projecttask.projecttaskid as id
					from vtiger_projecttask
					inner join vtiger_project on vtiger_project.projectid = vtiger_projecttask.projectid
					inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_project.linktoaccountscontacts
					where setype='$parentmodule' and smownerid=".$user->id;
				break;
			case 'Project':
				$query = "select vtiger_project.projectid as id
					from vtiger_project
					inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_project.linktoaccountscontacts
					where setype='$parentmodule' and smownerid=".$user->id;
				break;
			case 'CobroPago':
				$query = "select vtiger_cobropago.cobropagoid as id
					from vtiger_cobropago
					inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_cobropago.parent_id
					where setype='$parentmodule' and smownerid=".$user->id;
				break;
			case 'Calendar':
			case 'Events':
				if ($parentmodule=='Accounts') {
					$query = "select vtiger_seactivityrel.activityid as id
						from vtiger_seactivityrel
						inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_seactivityrel.crmid
						where setype='Accounts' and deleted=0 and smownerid=".$user->id;
				} else {
					$query = "select vtiger_cntactivityrel.activityid as id
						from vtiger_cntactivityrel
						inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_cntactivityrel.contactid
						where setype='Contacts' and deleted=0 and smownerid=".$user->id;
				}
				break;
			default:  // we look for uitype 10
				$rsfld = $adb->pquery('SELECT fieldname from vtiger_fieldmodulerel
					INNER JOIN vtiger_field on vtiger_field.fieldid=vtiger_fieldmodulerel.fieldid
					WHERE module=? and relmodule=?', array($module,$parentmodule));
				if ($rsfld && $adb->num_rows($rsfld)>0) {
					$fname = $adb->query_result($rsfld, 0, 'fieldname');
					$mod = Vtiger_Module::getInstance($module);
					$query = 'select '.$mod->basetable.'.'.$mod->basetableid.' as id
						from '.$mod->basetable.'
						inner join vtiger_crmentity on vtiger_crmentity.crmid = '.$mod->basetable.'.'.$fname."
						where setype='$parentmodule' and deleted=0 and smownerid=".$user->id;
				}
		}
		return $query;
	}
}
