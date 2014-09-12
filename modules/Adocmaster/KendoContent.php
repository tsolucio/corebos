<?php
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
