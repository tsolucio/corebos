<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 
//require_once('Smarty_setup.php');
 global $adb,$current_user;
 $query2=$adb->query("SELECT  vtiger_customview.viewname,vtiger_customview.entitytype,vtiger_customview.cvid 
                      FROM vtiger_customview 
                      WHERE entitytype='Project' ");
$rows2=$adb->num_rows($query2);
$i=0;
$select1="<SELECT id='filters' onchange='ispermit()'>";
while($rows2=$adb->fetch_array($query2)){
$select1.="<option value='".$rows2[2]."'>".$rows2[0]."</option>";

$i++;
}

$i = 0;

$query=$adb->query("SELECT  vtiger_project.lavorato,vtiger_project.dayson,vtiger_project.lavorato,vtiger_project.projectid,vtiger_project.progetto,vtiger_project.linktoaccountscontacts,vtiger_account.accountid,vtiger_project.project_id,e2.projectname AS proj,e3.accountname as proj2,vtiger_project.linktoaccountscontacts,vtiger_project.linktobuyer,vtiger_account.accountname,vtiger_crmentity.assigned_user_id, vtiger_project.projectname,vtiger_project.progetto,vtiger_project.project_no,vtiger_project.substatusproj,vtiger_crmentity.crmid, vtiger_crmentity.createdtime,vtiger_project.serial_number,vtiger_project.rma,vtiger_crmentity.smownerid,vtiger_users.id,vtiger_users.user_name
FROM vtiger_project 
LEFT JOIN vtiger_project AS e2 ON e2.projectid=vtiger_project.progetto
INNER JOIN vtiger_crmentity
ON vtiger_project.projectid=vtiger_crmentity.crmid 
LEFT JOIN vtiger_account
ON vtiger_project.linktoaccountscontacts=vtiger_account.accountid
LEFT JOIN vtiger_account AS e3 on e3.accountid=vtiger_project.linktobuyer
LEFT JOIN vtiger_users
ON vtiger_crmentity.smownerid=vtiger_users.id 
WHERE  vtiger_crmentity.deleted=0   
");
$rows=$adb->num_rows($query);
$data=array();
while($rows=$adb->fetch_array($query)){

    $data[$i]['id']=$rows['projectid'];
    $data[$i]['title'] =$rows['projectid'];
    $data[$i]['added21']=$rows['dayson'];
    $data[$i]['added22']=$rows['lavorato'];
    $data[$i]['duration']=$rows['smownerid'];
    $data[$i]['percentComplete']=$rows['createdtime'];
    $data[$i]['annualrevenue']=$rows['project_no'];
    $data[$i]['added1']=$rows['project_no'];
    $data[$i]['added2']=$rows['rma'];
    $data[$i]['added3']=$rows['proj'];
    
    
    if ($rows['projectname']== NULL)
        $asc2="-";
    else $asc2=$rows['projectname'];
    $data[$i]['added4']=$asc2;
    if ($rows['substatusproj']==NULL)
        $asc3="-";
    else $asc3=$rows['substatusproj'];
    $data[$i]['added5']=$asc3;
    $data[$i]['added6']=$rows['user_name'];
    if($rows['proj2'] == NULL)
        $asc="-";
            else $asc= $rows['proj2'];
        
        $data[$i]['added7']=$rows['accountname'];
    $data[$i]['added8']=$asc;
    $data[$i]['added9'] =$rows['project_id'];
    $data[$i]['added10']=$rows['progetto'];
    $data[$i]['added11'] =$rows['linktoaccountscontacts'];  
    $i++;
}

$select1.="</SELECT>";
$dataout=  json_encode($data);

$smarty = new vtigerCRM_Smarty;
$smarty->assign("selectContent", $select1);
$smarty->assign("data", $dataout);
$smarty->display("modules/test/index.tpl");
?>
