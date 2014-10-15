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

$mod_strings = Array(
    'LBL_MIGRATE_INFO'=>'Geben Sie die Werte ein um Daten zu migrieren. Von <b><i> Quelle </i></b> zu <b><i> aktullem (jetzigem) vtigerCRM </i></b>',
    'LBL_CURRENT_VT_MYSQL_EXIST'=>'Aktuelle vtiger\'s MySQL existiert in',
    'LBL_THIS_MACHINE'=>'Diese Maschine',
    'LBL_DIFFERENT_MACHINE'=>'Andere Maschine',
    'LBL_CURRENT_VT_MYSQL_PATH'=>'Aktueller vtiger\'s MySQL Pfad',
    'LBL_SOURCE_VT_MYSQL_DUMPFILE'=>'vtiger <b>Quelle</b> Dump Dateiname',
    'LBL_NOTE_TITLE'=>'Notiz:',
    'LBL_NOTES_LIST1'=>'Wenn MySQL auf der selben Maschine existiert, dann geben Sie den MySQL Pfad an. Sie können auch die Dump Datei angeben, wenn Sie diese haben.',
    'LBL_NOTES_LIST2'=>'Wenn MySQL au einer anderen Maschine existiert, dann geben Sie den (Qullen) Dump Dateinamen mit vollem Pfad an.',
    'LBL_NOTES_DUMP_PROCESS'=>'Um einen Datenbank dump zu starte, führen Sie die folgenden Kommandos aus
                               <br><b>mysqldump --user="mysql_username"  --password="mysql-password" -h "hostname"  --port="mysql_port" "database_name" > dump_filename</b>
                               <br>add <b>SET_FOREIGN_KEY_CHECKS = 0;</b> -- am Anfang der dump Datei
                               <br>add <b>SET_FOREIGN_KEY_CHECKS = 1;</b> -- am Ende der dump Datei',
    'LBL_NOTES_LIST3'=>'Geben Sie den MySQL Pfad an, wie z.B. <b>/home/crm/vtigerCRM4_5/mysql</b>',
    'LBL_NOTES_LIST4'=>'Geben Sie den Namen der Dump-Datei mit vollem Pfadnamen an,  wie z.B. <b>/home/vollerpfad/4_2_dump.txt</b>',

    'LBL_CURRENT_MYSQL_PATH_FOUND'=>'Der Pfad zur aktuellen MySQL Installation wurde gefunden.',
    'LBL_SOURCE_HOST_NAME'=>'Hostname der Quelle:',
    'LBL_SOURCE_MYSQL_PORT_NO'=>'Port Nr. der MySql Quelle :',
    'LBL_SOURCE_MYSQL_USER_NAME'=>'Nutzername der MySql Quelle :',
    'LBL_SOURCE_MYSQL_PASSWORD'=>'Passwort der MySql Quelle :',
    'LBL_SOURCE_DB_NAME'=>'Datenbankname der Quelle :',
    'LBL_MIGRATE'=>'Migriere zur aktuellen Version',
    //Added after 5 Beta
    'LBL_UPGRADE_VTIGER'=>'Upgrade vtiger CRM Datenbank',
    'LBL_UPGRADE_FROM_VTIGER_423'=>'Upgrade Datenbank von vtiger CRM 4.2.3 zu 5.0.0',
    'LBL_SETTINGS'=>'Einstellungen',
    'LBL_STEP'=>'Schritt',
    'LBL_SELECT_SOURCE'=>'Quelle auswählen',
    'LBL_STEP1_DESC'=>'Um die Datenbank Migration zu starten, müssen Sie das Format spezifizieren, in dem die alten Daten vorliegen.',
    'LBL_RADIO_BUTTON1_TEXT'=>'Ich habe Zugang zu dem aktuellen vtiger CRM Datenbank System.',
    'LBL_RADIO_BUTTON1_DESC'=>'Diese Option erfordert den Zugang zu der Host Rechner Adresse (wo die DB gespeichert wird) mit dem Wissen um die DB Zugangsdetails. Mit der Methode werden sowohl lokale als auch remote Systeme unterstützt. Hilfe finden Sie in der Dokumentation.',
    'LBL_RADIO_BUTTON2_TEXT'=>'Ich habe Zugang zu einem archivierten Datenbank Dump vom vtiger CRM system.',
    'LBL_RADIO_BUTTON2_DESC'=>'Diese Option erfordert, dass ein Datenbank Dump lokal auf dem Rechner zur Verfügung steht, der upgegraded werden soll. Sie können den Datenbank Dump nicht von einem anderen Rechner aus nutzen (remote Datenbank Server). Hilfe finden Sie in der Dokumentation.',
    'LBL_RADIO_BUTTON3_TEXT'=>'Ich habe eine neue Datenbank mit 4.2.3 Daten.',
    'LBL_RADIO_BUTTON3_DESC'=>'Diese Option erfordert das Wissen um die vtiger CRM 4.2.3 Datenbank Systemdetails, incl.  Datenbankserver ID, Nutzername, und Password. Sie können den Datenbank Dump nicht von einem anderen Rechner aus nutzen (remote Datenbank Server).',

    'LBL_HOST_DB_ACCESS_DETAILS'=>'Host Datenbank Zugangsdetails',
    'LBL_MYSQL_HOST_NAME_IP'=>'MySQL Host Name oder IP Adresse : ',
    'LBL_MYSQL_PORT'=>'MySQL Port Nummer : ',
    'LBL_MYSQL_USER_NAME'=>'MySql Nutzername : ',
    'LBL_MYSQL_PASSWORD'=>'MySql Passwort : ',
    'LBL_DB_NAME'=>'Datenbank Name : ',

    'LBL_LOCATE_DB_DUMP_FILE'=>'Lokalisiere Datenbank Dump Datei',
    'LBL_DUMP_FILE_LOCATION'=>'Dump Datei Lokation : ',

    'LBL_RADIO_BUTTON3_PROCESS'=>'<font color="red">Bitte spezifiezieren Sie nicht die 4.2.3 Datenbankdetails. Diese Option wird die Datenbank direkt ändern.</font>
                                  <br>Es wird dringend empfohlen, folgendes auszuführen:
                                  <br>1. Machen Sie einen Dump von Ihrer 4.2.3 Datenbank
                                  <br>2. Erzeugen Sie eine neue Datenbank (Es ist am Besten, wenn Sie eine Datenbank auf dem Server erzeugen, auf dem auch die vtiger 5.0 Datenbank läuft.)
                                  <br>3. Wenden Sie den 4.2.3 Dump auf die neue Datenbank an.
                                  <br>Definieren Sie die Zugangsdaten für die neue Datenbank. Die Migration wird die Datenbank verändern, so dass diese dem 5.0 Schema entspricht.
                                  Danach können Sie die neue Datenbank in der config.inc.php Datei angeben, wie z.B.: $dbconfig[\'db_name\'] = \'new db name\';',

    'LBL_ENTER_MYSQL_SERVER_PATH'=>'MySQL Server Pfad angeben',
    'LBL_SERVER_PATH_DESC'=>'Der MySQL Pfad muss angegeben werden, wie z.B.  <b>/home/5beta/vtigerCRM5_beta/mysql/bin</b> oder <b>c:\Program Files\mysql\bin</b>',
    'LBL_MYSQL_SERVER_PATH'=>'MySQL Server Pfad : ',
    'LBL_MIGRATE_BUTTON'=>'Migriere',
    'LBL_CANCEL_BUTTON'=>'Abbruch',
    'LBL_UPGRADE_FROM_VTIGER_5X'=>'Upgrade Datenbank von vtiger CRM 5.x zur nächsten Version',
    'LBL_PATCH_OR_MIGRATION'=>'Sie müssen die Quelle angeben (Patch update oder Migration)',
    //Added for java script alerts
    'ENTER_SOURCE_HOST' => 'Geben Sie den Host Namen der Quelle an.',
    'ENTER_SOURCE_MYSQL_PORT' => 'Geben Sie die MySql Port Nummer der Quelle an',
    'ENTER_SOURCE_MYSQL_USER' => 'Geben Sie den MySql Nutzernamen der Quelle an',
    'ENTER_SOURCE_DATABASE' => 'Geben Sie den Datenbanknamen der Quelle an',
    'ENTER_SOURCE_MYSQL_DUMP' => 'Geben Sie eine MySQL Dump Datei an',
    'ENTER_HOST' => 'Geben Sie den Host Namen an',
    'ENTER_MYSQL_PORT' => 'Geben Sie die MySql Port Nummer an',
    'ENTER_MYSQL_USER' => 'Geben Sie den MySql Nutzernamen an',
    'ENTER_DATABASE' => 'Geben Sie den Datenbanknamen an',
    'SELECT_ANYONE_OPTION' => 'Bitte eine Option auswählen',
    'ENTER_CORRECT_MYSQL_PATH' => 'Geben Sie einen korrekten MySQL Pfad an',
);
?>