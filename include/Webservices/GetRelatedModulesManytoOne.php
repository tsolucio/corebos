<?php
/***********************************************************************************
 * Copyright 2012-2014 JPL TSolucio, S.L.  --  This file is a part of coreBOSCP.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 ************************************************************************************/
require_once("include/Webservices/Utils.php");

/*
Given a module, get all the many to one related modules
 */
function getRelatedModulesManytoOne($module) {

	global $adb;
	$query = "SELECT  module,fieldname from  vtiger_fieldmodulerel 
             join  vtiger_field on  vtiger_field.fieldid= vtiger_fieldmodulerel.fieldid
             where relmodule='$module' and module in (select name from  vtiger_tab where presence=0)";

	$result1 = $adb->query($query);
	$num_rows1 = $adb->num_rows($result1);
	$modules[]=array();
	for ($i = 0; $i < $num_rows1; $i++) {
		$modules[$i]['label'] = getTranslatedString($adb->query_result($result1, $i, 'module'));
		$modules[$i]['name'] = $adb->query_result($result1, $i, 'module');
		$modules[$i]['field'] = $adb->query_result($result1, $i, 'fieldname');
	}

	$customuimod=array("Accounts;;account_id"=>"Accounts","Contacts;;account_id"=>"Accounts","SalesOrder;;account_id"=>"Accounts",
	"Invoice;;account_id"=>"Accounts","Quotes;;account_id"=>"Accounts","Contacts;;contact_id"=>"Contacts","Calendar;;contact_id"=>"Contacts",
	"Events;;contact_id"=>"Contacts","Quotes;;contact_id"=>"Contacts","PurchaseOrder;;contact_id"=>"Contacts","SalesOrder;;contact_id"=>"Contacts","Invoice;;contact_id"=>"Contacts",
	"Quotes;;potential_id"=>"Potentials","SalesOrder;;potential_id"=>"Potentials","Products;;vendor_id"=>"Vendors","PurchaseOrder;;vendor_id"=>"Vendors",
	"SalesOrder;;quote_id"=>"Quotes","Invoice;;salesorder_id"=>"SalesOrder");

	$j=$num_rows1;
	foreach ($customuimod as $key => $val) {
		if ($val==$module) {
			$keysplit=explode(";;", $key);
			$modules[$j]['label'] = getTranslatedString($keysplit[0]);
			$modules[$j]['name'] = $keysplit[0];
			$modules[$j]['field']= $keysplit[1];
			$j++;
		}
	}
	return $modules;
}
