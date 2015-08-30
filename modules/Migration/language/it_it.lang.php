<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/


$mod_strings = array (
  'LBL_MIGRATE_INFO' => 'Inserisci i Valori per Migrare i Dati da <b><i> Sorgente </i></b> a <b><i> Attuale (Piu Recente) vtigerCRM </i></b>',
  'LBL_CURRENT_VT_MYSQL_EXIST' => 'L`attuale installazione MySQL di vtiger si trova su',
  'LBL_THIS_MACHINE' => 'Questo computer',
  'LBL_DIFFERENT_MACHINE' => 'Un altro computer',
  'LBL_CURRENT_VT_MYSQL_PATH' => 'Attuale percorso (path) MySQL di vtiger',
  'LBL_SOURCE_VT_MYSQL_DUMPFILE' => 'Nome del Dump File del vtiger <b>Sorgente</b> ',
  'LBL_NOTE_TITLE' => 'Note:',
  'LBL_NOTES_LIST1' => 'Se il MySQL Attuale si trova stessa macchina inserisci il path MySQL,  oppure specifica il Dump file se lo hai.',
  'LBL_NOTES_LIST2' => 'Se il MySQL Attuale si trova su un`altra Macchina inserisci il nome file di Dump (Sorgente) specificando il percorso completo.',
  'LBL_NOTES_DUMP_PROCESS' => 'Per estrarre il dump del Database esegui i seguenti comandi da dentro la cartella mysql/bin (cioe` dalla directory dove risiedono i binari di MySQL
					<br><b>mysqldump --user=\"mysql_username\" --password=\"mysql-password\" -h \"hostname\" --port=\"mysql_port\" \"database_name\" > nomefile_dump </b>
					<br> aggiungi <b>SET FOREIGN_KEY_CHECKS = 0; </b> all`inizio del file di dump e
					<br> aggiungi <b>SET FOREIGN_KEY_CHECKS = 1;</b> alla fine del file di dump',
  'LBL_NOTES_LIST3' => 'IIndica il percorso di MySQL nel formato <b>/home/crm/vtigerCRM4_5/mysql</b>',
  'LBL_NOTES_LIST4' => 'Indica il nome del file di dump con il percorso completo, come <b>/home/fullpath/4_2_dump.txt</b>',
  'LBL_CURRENT_MYSQL_PATH_FOUND' => 'Il percorso MySQL dell`installazione Attuale e` stato trovato.',
  'LBL_SOURCE_HOST_NAME' => 'Nome macchina Sorgente',
  'LBL_SOURCE_MYSQL_PORT_NO' => 'Porta MySql macchina sorgente :',
  'LBL_SOURCE_MYSQL_USER_NAME' => 'Nome utente MySql macchina Sorgente:',
  'LBL_SOURCE_MYSQL_PASSWORD' => 'Password MySql macchina Sorgente:',
  'LBL_SOURCE_DB_NAME' => 'Nome database MySql macchina Sorgente:',
  'LBL_MIGRATE' => 'Migra alla versione Attuale',
  'LBL_UPGRADE_VTIGER' => 'Aggiorna il Database di vtiger CRM ',
  'LBL_UPGRADE_FROM_VTIGER_423' => 'Aggiorna il DataBase da vtiger CRM 4.2.3 alla versione  5.0.0',
  'LBL_STEP' => 'Passo',
  'LBL_SELECT_SOURCE' => 'Seleziona Fonte',
  'LBL_STEP1_DESC' => 'Per iniziare la migrazione del DataBase, devi specificare il formato nel quale il vecchio database e` disponibile',
  'LBL_RADIO_BUTTON1_TEXT' => 'Ho accesso al sistema database live di vtiger ',
  'LBL_RADIO_BUTTON1_DESC' => 'Questa opzione richiede che tu abbia l`indirizzo della macchina host (dove il  DB risiede) e le credenziali di accesso al DB. Sia il sistema locale che remoti sono supportati con questo metodo. Fai riferimento alla documentazione per ulteriori informazioni.',
  'LBL_RADIO_BUTTON2_TEXT' => 'Ho accesso ad un dump archiviato di un database di vtiger CRM',
  'LBL_RADIO_BUTTON2_DESC' => 'Questa opzione richiede che il dump del database sia disponibile localmente, sulla stessa macchina su cui stai aggiornando. Non puoi accedere al dump del database da una macchina differente (database server remoto). Fai riferimento alla documentazione per ulteriori informazioni.',
  'LBL_RADIO_BUTTON3_TEXT' => 'Ho un database nuovo con i dati della versione 4.2.3',
  'LBL_RADIO_BUTTON3_DESC' => 'Questa opzione richiede i dettagli database vtiger CRM 4.2.3, incluso database server ID, user name, e password. Non puoi accedere al database dump da una macchina differente (database server remoto)',
  'LBL_HOST_DB_ACCESS_DETAILS' => 'Dettagli accesso database host',
  'LBL_MYSQL_HOST_NAME_IP' => 'MySQL Host Name o Indirizzo IP : ',
  'LBL_MYSQL_PORT' => 'MySQL Numero di Porta : ',
  'LBL_MYSQL_USER_NAME' => 'MySql User Name : ',
  'LBL_MYSQL_PASSWORD' => 'MySql Password : ',
  'LBL_DB_NAME' => 'Nome Database : ',
  'LBL_LOCATE_DB_DUMP_FILE' => 'Specifica il database dump file',
  'LBL_DUMP_FILE_LOCATION' => 'Posizione del File di Dump: ',
  'LBL_RADIO_BUTTON3_PROCESS' => '<font color=\\\\\"red\\\\\">Non specificare i dettagli del database 4.2.3. Questa opzione modifichera` direttamente e permanentemente il database selezionato.</font>. E` fortemente consigliato di fare un dump del database 4.2.3, creare un nuovo database, e applicare al nuovo database il dump del database 4.2.3. Questa migrazione modifica il database per farlo corrispondere allo schema della versione 5.0',






  'LBL_ENTER_MYSQL_SERVER_PATH' => 'Inserisci il percorso del Server MySQL',
  'LBL_SERVER_PATH_DESC' => 'Percorso dell`installazione MySQL, es. <b>/home/5beta/vtigerCRM5_beta/mysql/bin</b> or <b>c:\\Programmi\\mysql\\bin</b>',
  'LBL_MYSQL_SERVER_PATH' => 'Percorso Server MySQL : ',
  'LBL_MIGRATE_BUTTON' => 'Migra',
  'LBL_CANCEL_BUTTON' => 'Annulla',
  'LBL_UPGRADE_FROM_VTIGER_5X' => 'Aggiorna il database da vtiger 5.x a una versione successiva',
  'LBL_PATCH_OR_MIGRATION' => 'devi specificare la versione del database di origine (aggiornamento da Patch o Migrazione)',
  'ENTER_SOURCE_HOST' => 'Prego inserire il nome Host di origine',
  'ENTER_SOURCE_MYSQL_PORT' => 'Prego inserire la porta MySql di origine',
  'ENTER_SOURCE_MYSQL_USER' => 'Prego inserire l`utente MySql di origine',
  'ENTER_SOURCE_DATABASE' => 'Prego inserire il Database  MySql di origine',
  'ENTER_SOURCE_MYSQL_DUMP' => 'Prego inserire un file di dump Mysql valido',
  'ENTER_HOST' => 'Prego inserire il nome Host',
  'ENTER_MYSQL_PORT' => 'Prego inserire la porta MySql',
  'ENTER_MYSQL_USER' => 'Prego inserire l`utente MySql',
  'ENTER_DATABASE' => 'Prego inserire il nome del Database',
  'SELECT_ANYONE_OPTION' => 'Prego selezionare un`opzione',
  'ENTER_CORRECT_MYSQL_PATH' => 'Prego inserire il percorso MySql corretto',

);






?>
