<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
class MysqlClass{
    
  // parametri per la connessione al database
  private $nomehost = "127.0.0.1";     
  private $nomeuser = "root";          
  private $password = "";
  // controllo sulle connessioni attive
  private $attiva = false;
  public $getDbList = "";
  // funzione per la connessione a MySQL
  // funzione per la connessione a MySQL

  public function connetti($db){
    if(!$this->attiva){
        if($connessione = mysql_connect($this->nomehost,$this->nomeuser,$this->password) or die (mysql_error())){
          // selezione del database
          mysql_select_db($db,$connessione) or die (mysql_error());
        }
        }else{
            return true;
        }
    }
    
    public function connettiMysql(){
    if(!$this->attiva){
       mysql_connect($this->nomehost,$this->nomeuser,$this->password) or die (mysql_error())or die (mysql_error());
        }
        else{
            return true;
        }
    }


// funzione per la chiusura della connessione
public function disconnettiMysql(){
        if($this->attiva){
                if(mysql_close()){
         $this->attiva = false; 
             return true; 
                }else{
                        return false; 
                }
        }
 }
  

       
    public function getTableList($db) {
    $i=0;
    $elencoTabelle = mysql_list_tables ($db);
    while ($i < mysql_num_rows ($elencoTabelle)){
    $table[$i] = mysql_tablename ($elencoTabelle, $i);
    $i++;
}
  
    return $table;
    }
    public function getFields($table, $db){

        $fields = mysql_list_fields($db, $table);
        $numero_colonne = mysql_num_fields($fields);
        for ($i = 0; $i < $numero_colonne; $i++){
            $arrayFields[$i]=mysql_field_name($fields,$i);
        }
        return $arrayFields;
}

}



?>
