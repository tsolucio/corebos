<?php

/*******************************************************************************
 * The contents of this file are subject to the following licences:
 * - SugarCRM Public License Version 1.1.2 http://www.sugarcrm.com/SPL
 * - vtiger CRM Public License Version 1.0 
 * You may not use this file except in compliance with the License
 * Software distributed under the License is distributed on an  "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is: SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by Vicus are Copyright (C) Vicus.
 * All Rights Reserved.
 * Feel free to use / redistribute these languagefiles under the VPL 1.0.
 * This translations is based on earlier work of: 
 * - IT-Online.nl <www.it-online.nl>
 * - Weltevree.org <www.Weltevree.org>
 ********************************************************************************/

/*******************************************************************************
 * Vicus eBusiness Solutions Version Control
 * @package 	NL-Dutch
 * Description	Dutch language pack for vtiger CRM version 5.3.x
 * @author	$Author: luuk $
 * @version 	$Revision: 1.2 $ $Date: 2011/11/14 17:07:26 $
 * @source	$Source: /var/lib/cvs/vtiger530/Dutch/modules/Migration/language/nl_nl.lang.php,v $
 * @copyright	Copyright (c)2005-2011 Vicus eBusiness Solutions bv <info@vicus.nl>
 * @license	vtiger CRM Public License Version 1.0 (by definition)
 ********************************************************************************/

$mod_strings = Array(
'LBL_MIGRATE_INFO'=>'Geef waarden voor de Data Migratie van <b><i> Bron </i></b> naar <b><i> Huidig (Laatste) vtigerCRM </i></b>',
'LBL_CURRENT_VT_MYSQL_EXIST'=>'Huidige vtiger\'s MySQL bestaat in',
'LBL_THIS_MACHINE'=>'Deze Machine',
'LBL_DIFFERENT_MACHINE'=>'Andere Machine',
'LBL_CURRENT_VT_MYSQL_PATH'=>'Huidig vtiger\'s MySQL pad',
'LBL_SOURCE_VT_MYSQL_DUMPFILE'=>'vtiger <b>Source</b> Dump Bestands naam',
'LBL_NOTE_TITLE'=>'Notitie',
'LBL_NOTES_LIST1'=>'Als Huidig MySQL bestaat op de zelfde Machine geef dan het MySQL Pad (of) geef het dump bestand indien aanwezig.',
'LBL_NOTES_LIST2'=>'Als Huidig MySQL bestaat op een Andere Machine Geef dan de (Bron) Dump bestandsnaam met het volledige Pad.',
'LBL_NOTES_DUMP_PROCESS'=>'Indien Database dump wordt gebruikt geef het volgende commando vanuit de <b>mysql/bin</b> directory
			   <br><b>mysqldump --user="mysql_username"  --password="mysql-password" -h "hostname"  --port="mysql_port" "database_name" > dump_filename</b>
			   <br>add <b>SET FOREIGN_KEY_CHECKS = 0;</b> -- at the start of the dump file
			   <br>add <b>SET FOREIGN_KEY_CHECKS = 1;</b> -- at the end of the dump file',
'LBL_NOTES_LIST3'=>'Geef het MySQL pad zoals <b>/home/crm/vtigerCRM4_5/mysql</b>',
'LBL_NOTES_LIST4'=>'Geef de Dump bestandsnaam met het volledige Pad zoals <b>/home/fullpath/4_2_dump.txt</b>',

'LBL_CURRENT_MYSQL_PATH_FOUND'=>'Huidige installatie\'s MySQL pad gevonden.',
'LBL_SOURCE_HOST_NAME'=>'Bron Host Naam :',
'LBL_SOURCE_MYSQL_PORT_NO'=>'Bron MySql Poort Nr :',
'LBL_SOURCE_MYSQL_USER_NAME'=>'Bron MySql Gebruikers Naam :',
'LBL_SOURCE_MYSQL_PASSWORD'=>'Bron MySql Wachtwoord :',
'LBL_SOURCE_DB_NAME'=>'Bron Database Naam :',
'LBL_MIGRATE'=>'Migratie naar Huidige Versie',
//Added after 5 Beta 
'LBL_UPGRADE_VTIGER'=>'Upgrade vtiger CRM Database',
'LBL_UPGRADE_FROM_VTIGER_423'=>'Upgrade database van vtiger CRM 4.2.3 naar 5.0.0',
'LBL_SETTINGS'=>'Instellingen',
'LBL_STEP'=>'Stap',
'LBL_SELECT_SOURCE'=>'Selecteer Bron',
'LBL_STEP1_DESC'=>'Om de database migratie te starten, dient u het formaat op te geven van de oude beschikbare data',
'LBL_RADIO_BUTTON1_TEXT'=>'Ik heb toegang tot vtiger CRM live database systeem',
'LBL_RADIO_BUTTON1_DESC'=>'Deze optie vereist de host machine\'s ( waar de DB is opgeslagen ) adres en DB toegang  details. Zowel lokaal als remote systemen zijn ondersteund bij deze methode. Lees de documentatie voor Help.',
'LBL_RADIO_BUTTON2_TEXT'=>'Ik heb toegang tot de to vtiger CRM opgeslagen database dump',
'LBL_RADIO_BUTTON2_DESC'=>'Deze optie vereist een database dump lokaal bechikbaar op de zelfde machine waar de update plaats vind. Remote toegang is niet mogelijk (remote database server). Lees de documentatie voor Help.',
'LBL_RADIO_BUTTON3_TEXT'=>'Ik heb een nieuwe database met 4.2.3 Data',
'LBL_RADIO_BUTTON3_DESC'=>'Deze optie vereist vtiger CRM 4.2.3 database systeem details, inclusief database server ID, gebruikers naam, en wachtwoord. Remote toegang is niet mogelijk (remote database server).',

'LBL_HOST_DB_ACCESS_DETAILS'=>'Host Database Toegang Details',
'LBL_MYSQL_HOST_NAME_IP'=>'MySQL Host Naam of IP Adres : ',
'LBL_MYSQL_PORT'=>'MySQL Poort Nummer : ',
'LBL_MYSQL_USER_NAME'=>'MySql Gebruikers naam : ',
'LBL_MYSQL_PASSWORD'=>'MySql Wachtwoord : ',
'LBL_DB_NAME'=>'Database Naam : ',

'LBL_LOCATE_DB_DUMP_FILE'=>'Locatie Database Dump Bestand',
'LBL_DUMP_FILE_LOCATION'=>'Dump File Locatie : ',

'LBL_RADIO_BUTTON3_PROCESS'=>'<font color="red">Geef geen 4.2.3 database details. Deze optie wijzigt de gegeven database direct.</font>
<br>Volgende wordt aangeraden.
<br>1. Maak een dump van de 4.2.3 database
<br>2. Maak een nieuwe database (Het beste is de database te maken op de server waar vtiger 5.0 Database draait.)
<br>3. Importeer de 4.2.3 dump naar de nieuwe database.
<br>Geef de nieuwe database de toegangas details. Deze migratie wijzigt de Database met het 5.0 Schema.
Daarna defineer de database naam in config.inc.php dus., $dbconfig[\'db_name\'] = \'new db name\';',

'LBL_ENTER_MYSQL_SERVER_PATH'=>'Geef MySQL Server Pad',
'LBL_SERVER_PATH_DESC'=>'MySQL pad op de server zoals <b>/home/5beta/vtigerCRM5_beta/mysql/bin</b> or <b>c:\Program Files\mysql\bin</b>',
'LBL_MYSQL_SERVER_PATH'=>'MySQL Server Pad : ',
'LBL_MIGRATE_BUTTON'=>'Migratie',
'LBL_CANCEL_BUTTON'=>'Annuleer',
'LBL_UPGRADE_FROM_VTIGER_5X'=>'Upgrade database van vtiger CRM 5.x naar de laatste versie',
'LBL_PATCH_OR_MIGRATION'=>'Geef de bron database versie (Patch update of Migratie)',
//Added for java script alerts
'ENTER_SOURCE_HOST' => 'Geef de Bron Host Naam',
'ENTER_SOURCE_MYSQL_PORT' => 'Geef het Bron MySql Poort Nummer',
'ENTER_SOURCE_MYSQL_USER' => 'Geef de Bron MySql Gebruikers Naam',
'ENTER_SOURCE_DATABASE' => 'Geef de Bron Database Naam',
'ENTER_SOURCE_MYSQL_DUMP' => 'Geef een geldig MySQL Dump Bestand',
'ENTER_HOST' => 'Geef de Host Naam',
'ENTER_MYSQL_PORT' => 'Geef het MySql Poort Nummer',
'ENTER_MYSQL_USER' => 'Geef de MySql Gebruikers Naam',
'ENTER_DATABASE' => 'Geef de Database Naam',
'SELECT_ANYONE_OPTION' => 'Selecteer een optie',
'ENTER_CORRECT_MYSQL_PATH' => 'Geef een Correct MySQL Pad',

);

?>
