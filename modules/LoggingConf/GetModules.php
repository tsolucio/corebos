<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('include/utils/UserInfoUtil.php');
require_once('include/utils/utils.php');
global $adb;
$action=$_REQUEST['which'];

$result='';
$loggingModules=array();

$query=$adb->pquery("Select distinct(tabid) from vtiger_loggingconfiguration");
$number=$adb->num_rows($query);

$i=0;
while($i<$number)
{
    $loggingModules[]=getTabname($adb->query_result($query,$i));
    $i++;
}

$allModules=array_values(getFieldModuleAccessArray());

if($action!='LoggedModules')
{
$interestModules=array_diff($allModules,$loggingModules);
$j=0;
$result='<tr>';
foreach($interestModules as $module)
{
    $tabid=getTabid($module);
    $result.="<td style=\"width:50%;\" align=\"$align\"><input name='tabids[]' id='tabids[]' value=\"$tabid\" type='checkbox'>".$module."</td>";
    $j++;
    if($j==2)
    {
        $result.="</tr><tr>";
        $j=0;        
    }    
}
}
else{
//$result='<select>';
foreach($loggingModules as $module)
{
    $result.="<option value=\"$module\" > $module</option>";    
}
//$result.='</select>';
}
echo $result;

?>
