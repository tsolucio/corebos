<?php
include "modules/MapGenerator/dbclass.php";
$db=$_POST['nameDb'];
$connect = new MysqlClass();
$connect->connetti($db);
$stringaselTab1=$_POST['selTab1'];//stringa con tutte le tabelle scelte di selTab1
$stringaselTab2=$_POST['selTab2'];//stringa con tutte le tabelle scelte di selTab2
$stringaselField1=$_POST['selField1'];//stringa con tutte i campi scelti in selField1
$stringaselField2=$_POST['selField2'];//stringa con tutte i campi scelti in selField1
$stringaCampiSelezionati=$_POST['campiSelezionati'];//stringa con tutte i campi scelti in selField1
$nameView="";
if (isset($_POST['nameView'])) { 
    $nameView=$_POST['nameView'];//nome della vista; 
} 

/*
 * Vengono convertite le stringhe precedenti in array
 */
$selTab1 = explode(',',$stringaselTab1);
$selTab2 = explode(',',$stringaselTab2);
$selField1 = explode(',',$stringaselField1);
$selField2 = explode(',',$stringaselField2);
$tabelleCampi = explode(',',$stringaCampiSelezionati);


for($i=0;$i<count($tabelleCampi);$i++){
    $tabellaCampo=explode('.',$tabelleCampi[$i]);
    $tabelle[$i]=$tabellaCampo[0];
    $campi[$i]=$tabellaCampo[1];
}


$allFields=listaCampiCostructor( $tabelle, $campi, $tabelleCampi);//array con tutti i campi della query
$stringaFields=concatenaAllField($allFields);//stringa di tutti i campi della query
showJoinArray($selTab1, $selField1, $selTab2, $selField2, $nameView, $stringaFields);
$connect->disconnettiMysql();
/*
 * Stampa a video nel <div> con id="results" la query per la creazione della vista materializzata
 */
function showJoinArray($selTab1, $selField1, $selTab2, $selField2, $nameView, $stringaFields){
   echo '<p id="query">' ;
      for($i=0;$i<count($selTab1);$i++){
        if($i==0){
                echo '<b> CREATE TABLE </b>'.$nameView.'<b> AS SELECT </b>'.$stringaFields.'<b> FROM </b>'.$selTab1[$i].'<b> INNER JOIN </b>'.$selTab2[$i].'<b> ON </b>'.$selTab1[$i].'.'.$selField1[$i].'<b> = </b>'.$selTab2[$i].'.'.$selField2[$i];
            }
            else{
                $tab2=$selTab2[$i];
                $numTab=0;
                for($j=0;$j<count($selTab2[$j]);$j++){
                  if($tab2==$selTab2[$j]){
                      $numTab=$numTab+1;
                  }
                }
                if ($numTab< 1){
                   echo '<b> INNER JOIN </b>'.$selTab2[$i].'<b> ON </b>'.$selTab1[$i].'.'.$selField1[$i].'<b> = </b>'.$selTab2[$i].'.'.$selField2[$i]; 
                }
                else
                {
                   echo '<b> INNER JOIN </b>'.$selTab1[$i].'<b> ON </b>'.$selTab1[$i].'.'.$selField1[$i].'<b> = </b>'.$selTab2[$i].'.'.$selField2[$i]; 
                }
                
                             
                
            }
            
    }
    echo ";";
    echo '</p>';
}


/*
 * Ricevendo il nome di una tabella, fornisce il un array contenente tutti
 * i nomi dei campi in essa contenuta.
 */
function getCampi($table){
        global $db;
        $fields = mysql_list_fields($db, $table);
        $numColumn= mysql_num_fields($fields);
        for ($i = 0; $i < $numColumn; $i++){
            $fieldList[$i]=mysql_field_name($fields,$i);
        }
        return $fieldList;
}

/*
 * Riceve in ingresso un array e un intero, e restituisce un sub array 
 */
function prelevaArray($array, $indice){
    $subArray=array();
    for($i=0; $i<$indice;$i++){
        $subArray[$i]=$array[$i];
    }
    return $subArray;
}


/*
 * Riceve in ingresso un array, e concatena ogni elemento in un'unica stringa
 */
function concatenaAllField($allFields)
{
      for($i=0;$i<count($allFields);$i++){
         if($i==0){
             $stringa=$allFields[$i];
         }
         else{
             $stringa=$stringa.', '.$allFields[$i];
         }
       }
    return $stringa;
}

function listaCampiCostructor($listaTab, $listaCampi, $tabelleCampi){
   
    for($i=0; $i<count($listaCampi); $i++){
        $campoDacontrollare=$listaCampi[$i];
        $tabDelCampo=$listaTab[$i];
         for($j=0; $j<count($listaCampi); $j++){
             if($campoDacontrollare==$listaCampi[$j] && $listaTab[$j]!=$tabDelCampo){
                $tabelleCampi[$j]=$listaTab[$j].'.'.$listaCampi[$j].' <b>AS</b> '.$listaTab[$j].'_'.$listaCampi[$j];      
                $tabelleCampi[$i]=$listaTab[$i].'.'.$listaCampi[$i].' <b>AS</b> '.$listaTab[$i].'_'.$listaCampi[$i];
             }
   
         }
   
    }
    
     return $tabelleCampi;
}









?>


