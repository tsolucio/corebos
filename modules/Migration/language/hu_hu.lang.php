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
 ********************************************************************************
 * $Header:  \modules\Migration\language\hu_hu.lang.php - 12:07 2011.11.12. $
 * Description:  Defines the Hungarian language pack for the Migration module vtiger 5.3.x
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): Istvan Holbok,  e-mail: holbok@gmail.com , mobil: +3670-3420900 , Skype: holboki
 ********************************************************************************/

$mod_strings = array(
	'LBL_MIGRATE_INFO' => 'Költözési adatok megadása a <b><i> Forrás </i></b>-ból az <b><i> Aktuális (Legutóbbi) vtigerCRM </i></b>-be',
	'LBL_CURRENT_VT_MYSQL_EXIST' => 'Aktuális vtiger MySQL itt van ',
	'LBL_THIS_MACHINE' => 'Ezen a gépen',
	'LBL_DIFFERENT_MACHINE' => 'Másik gépen',
	'LBL_CURRENT_VT_MYSQL_PATH' => 'Aktuális vtiger MySQL elérési útvonal',
	'LBL_SOURCE_VT_MYSQL_DUMPFILE' => 'vtiger <b>Forrás</b> Dump (Mentés) fájlnév',
	'LBL_NOTE_TITLE' => 'Megjegyzés:',
	'LBL_NOTES_LIST1' => 'Ha az aktuális MySQL ugyanazon a gépen van, akkor add meg a MySQL elérési útvonalat (vagy) megadhatod a Mentés (Dump) fájlt, ha van ilyened.',
	'LBL_NOTES_LIST2' => 'Ha az aktuális MySQL egy másik gépen van, akkor add meg a (Forrás) Mentés (Dump) fájl nevét a teljes elérési útvonallal.',
	'LBL_NOTES_DUMP_PROCESS' => 'Az adatbázis mentéshez futtasd le a következő parancsot a <b>mysql/bin</b> könyvtárban <br><b>mysqldump --user="mysql_username"  --password="mysql-password" -h "hostname"  --port="mysql_port" "database_name" > dump_filename</b>			   <br>add <b>SET FOREIGN_KEY_CHECKS = 0;</b> -- at the start of the dump file			   <br>add <b>SET FOREIGN_KEY_CHECKS = 1;</b> -- at the end of the dump file',
	'LBL_NOTES_LIST3' => 'Add meg a MySQL elérést, pl. <b>/home/crm/vtigerCRM4_5/mysql</b>',
	'LBL_NOTES_LIST4' => 'Add meg a Mentés fájlnevét teljes eléréssel pl. <b>/home/fullpath/4_2_dump.txt</b>',
	'LBL_CURRENT_MYSQL_PATH_FOUND' => 'Az aktuális telepítéshez a MySQL elérés útvonalát megtaláltuk.',
	'LBL_SOURCE_HOST_NAME' => 'Forrás Host Név :',
	'LBL_SOURCE_MYSQL_PORT_NO' => 'Forrás MySql Port sorszám :',
	'LBL_SOURCE_MYSQL_USER_NAME' => 'Forrás MySql Felhasználónév :',
	'LBL_SOURCE_MYSQL_PASSWORD' => 'Forrás MySql Jelszó :',
	'LBL_SOURCE_DB_NAME' => 'Forrás Adatbázis Név :',
	'LBL_MIGRATE' => 'Költöztetés az aktuális verzióba',
	'LBL_UPGRADE_VTIGER' => 'Vtiger CRM adatbázis naprakész állapotba hozása',
	'LBL_UPGRADE_FROM_VTIGER_423' => 'Adatbázis naprakész állapotba hozása a vtiger CRM 4.2.3-ból 5.0.0-ba',
	'LBL_SETTINGS' => 'Beállítások',
	'LBL_STEP' => 'Lépés',
	'LBL_SELECT_SOURCE' => 'Forrás kiválasztása',
	'LBL_STEP1_DESC' => 'Az adatbázis költöztetéshez meg kell adnod a formátumot, amiben a régi adatok rendelkezésedre állnak',
	'LBL_RADIO_BUTTON1_TEXT' => 'Hozzáférésem van a vtiger CRM futó adatbázis rendszeréhez',
	'LBL_RADIO_BUTTON1_DESC' => 'Ez a lehetőség igényli, hogy rendelkezz a HOST gép ( ahol az adatbázis tárolva van ) címével és az adatbázis elérés paramétereivel. Mind a helyi, mind pedig a távoli elérést támogatja ez a mód. Lásd a dokumentációt a további segítségért.',
	'LBL_RADIO_BUTTON2_TEXT' => 'Hozzáférésem van a vtiger CRM archivált adatbázis mentéséhez.',
	'LBL_RADIO_BUTTON2_DESC' => 'This option requires database dump available locally in the same machine in which you are upgrading. You cannot access data dump from a different machine (remote database server). Refer documentation for Help.',
	'LBL_RADIO_BUTTON3_TEXT' => 'Új adatbázisom van 4.2.3 adatokkal',
	'LBL_RADIO_BUTTON3_DESC' => 'This option requires vtiger CRM 4.2.3 database system details, including database server ID, user name, and password. You cannot access data dump from a different machine (remote database server).',
	'LBL_HOST_DB_ACCESS_DETAILS' => 'Host Adatbázis elérés adatai',
	'LBL_MYSQL_HOST_NAME_IP' => 'MySQL Host név vagy IP cím : ',
	'LBL_MYSQL_PORT' => 'MySQL Port szám : ',
	'LBL_MYSQL_USER_NAME' => 'MySql felhasználó név : ',
	'LBL_MYSQL_PASSWORD' => 'MySql jelszó : ',
	'LBL_DB_NAME' => 'Adatbázis neve : ',
	'LBL_LOCATE_DB_DUMP_FILE' => 'Helyezd el az Adatbázis Dump fájlt',
	'LBL_DUMP_FILE_LOCATION' => 'Dump fájl helye : ',
	'LBL_RADIO_BUTTON3_PROCESS' => '<font color="red">Please do not specify the 4.2.3 database details. This option will alter the given database directly.</font><br>It is strongly recommended that to do the following.<br>1. Take a dump of your 4.2.3 database<br>2. Create new database (Better is to create a database in the server where your vtiger 5.0 Database is running.)<br>3. Apply this 4.2.3 dump to this new database.<br>Now give this new database access details. This migration will modify this Database to fit with the 5.',
	'LBL_ENTER_MYSQL_SERVER_PATH' => 'Add meg a MySQL Server elérési útvonalát',
	'LBL_SERVER_PATH_DESC' => 'MySQL útvonal a szerveren, pl. <b>/home/5beta/vtigerCRM5_beta/mysql/bin</b> vagy <b>c://Program Files/mysql',
	'LBL_MYSQL_SERVER_PATH' => 'MySQL Server elérési útvonal : ',
	'LBL_MIGRATE_BUTTON' => 'Költöztetés',
	'LBL_CANCEL_BUTTON' => 'Visszavon',
	'LBL_UPGRADE_FROM_VTIGER_5X' => 'Adatbázis frissítése vtiger CRM 5.x formátumból a következő verzióra',
	'LBL_PATCH_OR_MIGRATION' => 'Meg kell határoznod a forrás adatbázis verzióját (Frissítés vagy Költöztetés)',
	'ENTER_SOURCE_HOST' => 'Add meg a forrás Host nevet',
	'ENTER_SOURCE_MYSQL_PORT' => 'Add meg a forrás MySql Port számot',
	'ENTER_SOURCE_MYSQL_USER' => 'Add meg a forrás MySql felhasználó nevet',
	'ENTER_SOURCE_DATABASE' => 'Add meg a forrás (kiindulási) Adatbázis nevét',
	'ENTER_SOURCE_MYSQL_DUMP' => 'Adj meg egy érvényes MySQL Dump fájlt',
	'ENTER_HOST' => 'Add meg a Host nevet (általában localhost)',
	'ENTER_MYSQL_PORT' => 'Add meg a MySql Port számát',
	'ENTER_MYSQL_USER' => 'Add meg a MySql felhasználó nevet',
	'ENTER_DATABASE' => 'Add meg az adatbázis nevét',
	'SELECT_ANYONE_OPTION' => 'Válassz egyet a lehetőségek közül',
	'ENTER_CORRECT_MYSQL_PATH' => 'Add meg a helyes MySQL elérési útvonalat'
);
?>