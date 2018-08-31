<?php
include "modules/MapGenerator/dbclass.php";
 $db=$_POST['nameDbViews'];
// istanza della classe
$data = new MysqlClass();
// chiamata alla funzione di connessione
$data->connetti($db);
$i=0;
$numTab2=0;
$elencoTabelle = mysql_list_tables ($db);
while ($i < mysql_num_rows ($elencoTabelle)){
    $stringa=mysql_tablename ($elencoTabelle, $i);
    if($stringa[0]=='m' && $stringa[1]=='v' && $stringa[2]=='_' && !(strstr($stringa, "_delta")) ){
        $vista[$numTab2] = mysql_tablename ($elencoTabelle, $i);
        $numTab2++;
    }     
    $i++;
}
        
for($i=0;$i<count($vista);$i++){ 
    $selTab=$selTab."<option id='".$vista[$i]."' name='".$vista[$i]."'>".$vista[$i]."</option>";      
} 
echo $selTab;
        
?>



