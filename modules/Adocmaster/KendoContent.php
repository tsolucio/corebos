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
$kaction=$_REQUEST['kaction'];
 
global $adb;
require_once("modules/Adocdetail/Adocdetail.php");

$content=array();
if($kaction=='retrieve'){

$query=$adb->pquery("Select * from vtiger_adocdetail a 
                    join vtiger_crmentity ce on a.adocdetailid=ce.crmid 
                    where ce.deleted=0 and a.adoctomaster=?",array($_REQUEST['record']));
$count=$adb->num_rows($query);
for($i=0;$i<$count;$i++){
   $content[$i]['adocdetailid']=$adb->query_result($query,$i,'adocdetailid');
   $content[$i]['adocdetailno']=$adb->query_result($query,$i,'adocdetailno');
   $content[$i]['adocdetailname']=$adb->query_result($query,$i,'adocdetailname');
   $content[$i]['nrline']=$adb->query_result($query,$i,'nrline');
   $product=$adb->query_result($query,$i,'adoc_product');
   $content[$i]['adoc_product']=  getProductName($product);  
   $content[$i]['urlproduct']='index.php?module=Products&action=DetailView&record='.$product;
   $content[$i]['url']='index.php?module=Adocdetail&action=DetailView&record='.$content[$i]['adocdetailid'];
   $content[$i]['adoc_quantity']=  $adb->query_result($query,$i,'adoc_quantity');
   $content[$i]['adoc_price']=  $adb->query_result($query,$i,'adoc_price');
   $content[$i]['inout_docnr']=  $adb->query_result($query,$i,'inout_docnr');
   $content[$i]['poteknema']=  $adb->query_result($query,$i,'poteknema');
   $content[$i]['posupplier']=  $adb->query_result($query,$i,'posupplier');
   $content[$i]['riferimento']=  $adb->query_result($query,$i,'riferimento');
   $stockid=$adb->query_result($query,$i,'adoc_stock');
   $stock=getEntityName('Stock',$stockid);
   $content[$i]['adoc_stock']= $stock[$stockid];
   $content[$i]['urlstock']='index.php?module=Stock&action=DetailView&record='.$stockid;
}
echo json_encode($content);

}
elseif($kaction=='update'){
 
    $models=$_REQUEST['models'];
    $model_values=array();
    $model_values=json_decode($models);
    $mv=$model_values[0];
    
    $focus = CRMEntity::getInstance("Adocdetail");
    $focus->id=$mv->adocdetailid;
    $focus->retrieve_entity_info($mv->adocdetailid,"Adocdetail");

    $focus->mode = 'edit';
    $focus->column_fields['adocdetailno']=$mv->adocdetailno;
    $focus->column_fields['adocdetailname']=$mv->adocdetailname;
    $focus->column_fields['nrline']=$mv->nrline;
    $focus->column_fields['adoc_product']=$mv->adoc_product;
    $focus->column_fields['adoc_quantity']= $mv->adoc_quantity;
    $focus->column_fields['adoc_price']= $mv->adoc_price;
    $focus->column_fields['inout_docnr']= $mv->inout_docnr;
    $focus->column_fields['poteknema']=  $mv->poteknema;
    $focus->column_fields['posupplier']=  $mv->posupplier;
    $focus->column_fields['riferimento']=  $mv->riferimento;
    $focus->column_fields['adoc_stock']=$mv->adoc_stock;
    $focus->save("Adocdetail"); 
}
elseif($kaction=="destroy"){
    $models=$_REQUEST['models'];
    $model_values=array();
    $model_values=json_decode($models);
    $mv=$model_values[0];     
    $id=$mv->adocdetailid;

    $query=$adb->pquery("update vtiger_crmentity set deleted=1 where crmid=?",array($id));
    echo $query;
}
elseif($kaction=="create"){
    $models=$_REQUEST['models'];
    $model_values=array();
    $model_values=json_decode($models);
    $mv=$model_values[0];
    global $current_user;
    $focus = CRMEntity::getInstance("Adocdetail");
    $focus->id='';

    //$focus->mode = 'edit';
    $focus->column_fields['adocdetailno']=$mv->adocdetailno;
    $focus->column_fields['adocdetailname']=$mv->adocdetailname;
    $focus->column_fields['nrline']=$mv->nrline;
    $focus->column_fields['adoc_product']=$mv->adoc_product;
    $focus->column_fields['adoc_quantity']= $mv->adoc_quantity;
    $focus->column_fields['adoc_price']= $mv->adoc_price;
    $focus->column_fields['inout_docnr']= $mv->inout_docnr;
    $focus->column_fields['poteknema']=  $mv->poteknema;
    $focus->column_fields['posupplier']=  $mv->posupplier;
    $focus->column_fields['riferimento']=  $mv->riferimento;
    $focus->column_fields['adoc_stock']=$mv->adoc_stock;
    $focus->column_fields['assigned_user_id']=$current_user->id;
    $focus->save("Adocdetail"); 
}
?>
