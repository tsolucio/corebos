<?php
 /*************************************************************************************************
 * Copyright 2014 Opencubed -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *************************************************************************************************
 *  Module       : Adocmaster
 *  Version      : 5.4.0
 *  Author       : Opencubed
 *************************************************************************************************/
global $current_user, $currentModule;

checkFileAccess("modules/$currentModule/$currentModule.php");
require_once("modules/$currentModule/$currentModule.php");
require_once("include/utils/CommonUtils.php");
require_once("modules/Adocdetail/Adocdetail.php");
$focus = new $currentModule();
setObjectValuesFromRequest($focus);

$mode = $_REQUEST['mode'];
$record=$_REQUEST['record'];

if($mode) $focus->mode = $mode;
if($record)$focus->id  = $record;

if($_REQUEST['assigntype'] == 'U') {
	$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_user_id'];
} elseif($_REQUEST['assigntype'] == 'T') {
	$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_group_id'];
}

$focus->save($currentModule);
$return_id = $focus->id;
$but=vtlib_purify($_REQUEST['buttontemp']);

if($but){
   $query = "SELECT * 
FROM vtiger_adocdetail
INNER JOIN vtiger_adocmaster ON vtiger_adocmaster.adocmasterid = vtiger_adocdetail.adoctomaster
INNER JOIN vtiger_products ON vtiger_adocdetail.adoc_product = vtiger_products.productid
WHERE adocmasterid =?";
   $result = $adb->pquery($query,array($_REQUEST['return_pid']));
    $name_nr = 1;

  for($i=0;$i<$adb->num_rows($result);$i++)
    {
     
      $adocdetail = new Adocdetail();
                $adocdetail->column_fields['adocdetailname'] = "ADOC";
                $adocdetail->column_fields['nrline'] = $name_nr;
               $adocdetail->column_fields['adoctomaster'] = $focus->id;
                $adocdetail->column_fields['adoc_product'] = $adb->query_result($result,$i,'adoc_product');
                $adocdetail->column_fields['adoc_stock'] = $adb->query_result($result,$i,'adoc_stock');
                $adocdetail->column_fields['adoc_quantity']=$adb->query_result($result,$i,'adoc_quantity');
                //$adocdetail->column_fields['adoc_price']= $price_tariff;
                //$adocdetail->column_fields['total']= $unit_price;
                $_REQUEST['assigntype'] = 'U';
                $adocdetail->column_fields['assigned_user_id'] = $current_user->id;
              
$adocdetail->save("Adocdetail");
$name_nr++;
    }


}
$search = vtlib_purify($_REQUEST['search_url']);

$parenttab = getParentTab();
if($_REQUEST['return_module'] != '') {
	$return_module = vtlib_purify($_REQUEST['return_module']);
} else {
	$return_module = $currentModule;
}

if($_REQUEST['return_action'] != '') {
	$return_action = vtlib_purify($_REQUEST['return_action']);
} else {
	$return_action = "DetailView";
}
//if($_REQUEST['productid']!=''){
//include_once('modules/Adocdetail/Adocdetail.php');
//$pid=explode(";",$_REQUEST['productid']);
//$qty=explode(";",$_REQUEST['qty']);
//$prc=explode(";",$_REQUEST['price']);
//
//for($i=0;$i<sizeof($pid);$i++){
//$f=new Adocdetail();
//$f->column_fields['adocdproduct']=$pid[$i];
//$f->column_fields['adocdtomaster']=$return_id;
//$f->column_fields['adocdquantity']=$qty[$i];
//$f->column_fields['punitprice']=$prc[$i];
//$f->column_fields['adocdtotalamount']=$prc[$i]*$qty[$i];
//$f->column_fields['adocdtax']=0.21;
//$f->column_fields['adocdtotal']=1.21*$prc[$i]*$qty[$i];
//$f->column_fields['assigned_user_id']=$focus->column_fields['assigned_user_id'];
//
//$f->save("Adocdetail");
//
//}
//}
if($_REQUEST['return_id'] != '') {
	$return_id = vtlib_purify($_REQUEST['return_id']);
}

header("Location: index.php?action=$return_action&module=$return_module&record=$return_id&parenttab=$parenttab&start=".vtlib_purify($_REQUEST['pagenumber']).$search);

?>