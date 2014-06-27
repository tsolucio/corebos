<?php
/***********************************************************
*  Module       : Migration
*  Language     : French
*  Version      : 5.4.0 
*  License      : GPL
*  Author       : ABOnline solutions http://www.vtiger-crm.fr
***********************************************************/

$mod_strings = array (
	'LBL_MIGRATE_INFO' => 'Saisissez les valeurs pour migrer de votre version vers la dernière version disponible de vtigerCRM ',
	'LBL_CURRENT_VT_MYSQL_EXIST' => 'La base de données vtiger CRM est située sur',
	'LBL_THIS_MACHINE' => 'cette machine',
	'LBL_DIFFERENT_MACHINE' => 'une autre machine',
	'LBL_CURRENT_VT_MYSQL_PATH' => 'Chemin vers MySQL',
	'LBL_SOURCE_VT_MYSQL_DUMPFILE' => 'Fichier dump source',
	'LBL_NOTE_TITLE' => 'Note :',
	'LBL_NOTES_LIST1' => 'Si le moteur de bases MySQL est sur le même serveur, entrer le chemin (ou) le fichier dump si vous avez.',
	'LBL_NOTES_LIST2' => 'Si le moteur de bases MySQL est sur un serveur différent, entrer le chemin absolu (source) du fichier dump.',
	'LBL_NOTES_DUMP_PROCESS' => 'Pour obtenir un dump de votre base de données, exécutez la commande
	mysqldump --user="mysql_username" --password="mysql-password" -h "hostname" --port="mysql_port" "database_name" > dump_filename
	Ajoutez SET_FOREIGN_KEY_CHECKS = 0; -- Au début du fichier dump
	Ajoutez SET_FOREIGN_KEY_CHECKS = 1; -- A la fin du fichier dump',
	'LBL_NOTES_LIST3' => 'Saisissez le chemin vers mysql. Ex: /home/crm/vtigerCRM4_5/mysql',
	'LBL_NOTES_LIST4' => 'Saisissez le chemin absolu vers le dump. Ex: /home/fullpath/4_2_dump.txt',
	'LBL_CURRENT_MYSQL_PATH_FOUND' => 'Le chemin vers l\'installation courante a été trouvé.',
	'LBL_SOURCE_HOST_NAME' => 'Hôte source :',
	'LBL_SOURCE_MYSQL_PORT_NO' => 'Source MySQL port :',
	'LBL_SOURCE_MYSQL_USER_NAME' => 'Source MySQL nom d\'utilisateur :',
	'LBL_SOURCE_MYSQL_PASSWORD' => 'Source MySQL mot de passe :',
	'LBL_SOURCE_DB_NAME' => 'Base de données source :',
	'LBL_MIGRATE' => 'Migrer vers la version courante',
	'LBL_UPGRADE_VTIGER' => 'Mise à jour base de données vtiger CRM',
	'LBL_UPGRADE_FROM_VTIGER_423' => 'Mise à jour de votre base vtiger CRM 4.2.3 vers 5.0.0',
	'LBL_SETTINGS' => 'Configuration',
	'LBL_STEP' => 'Etape',
	'LBL_SELECT_SOURCE' => 'Source',
	'LBL_STEP1_DESC' => 'Pour débuter la migration, vous devez spécifier le format dans lequel votre base de données est disponible',
	'LBL_RADIO_BUTTON1_TEXT' => 'J\'ai accès au système de base de données de vtiger CRM',
	'LBL_RADIO_BUTTON1_DESC' => 'Cette option exige de posséder l\'adresse de machine host (où la base est stockée) ainsi que les détails d\'accès. Sur systèmes locaux et distants, appliquer la même méthode. Voir documentation.',
	'LBL_RADIO_BUTTON2_TEXT' => 'J\'ai accès aux archives de la base de données de vtiger CRM',
	'LBL_RADIO_BUTTON2_DESC' => 'Cette option exige la décharge de base de données disponible localement sur la même machine de mise à jour. Vous ne pouvez accéder aux sauvegardes des données provenant de différentes machines (remote database server). Voir documentation.',
	'LBL_RADIO_BUTTON3_TEXT' => 'J\'ai une nouvelle base de données en version Vtiger 4.2.3 ',
	'LBL_RADIO_BUTTON3_DESC' => 'Cette option exige une base vtiger CRM 4.2.3, avec les détails de type : server ID, user name, et password. Vous ne pouvez accéder aux sauvegardes des données provenant de différentes machines (remote database server).',
	'LBL_HOST_DB_ACCESS_DETAILS' => 'Détails de l\'accès à la base',
	'LBL_MYSQL_HOST_NAME_IP' => 'Adresse serveur MySQL ou adresse IP : ',
	'LBL_MYSQL_PORT' => 'Port : ',
	'LBL_MYSQL_USER_NAME' => 'Utilisateur : ',
	'LBL_MYSQL_PASSWORD' => 'Mot de passe : ',
	'LBL_DB_NAME' => 'Nom de la base : ',
	'LBL_LOCATE_DB_DUMP_FILE' => 'Localisation du dump',
	'LBL_DUMP_FILE_LOCATION' => 'Chemin vers le fichier dump : ',
	'LBL_RADIO_BUTTON3_PROCESS' => 'SVP ne spécifiez pas les détails de votre base Vtiger 4.2.3. Cette option changera la base de données données directement.
	Il est vivement recommandé de procéder de la façon suivante :
	1. Prenez une sauvegarde de votre base de données 4.2.3
	2. Créez une nouvelle base de données (il vaut mieux créer une base de données sur le serveur où votre base de données du vtiger 5.0 tourne).
	3. Appliquez la sauvegarde de votre base de données 4.2.3 dans la nouvelle base.
	Donnez maintenant les nouveaux détails d\'accès aux bases de données. Cette migration modifiera cette base de données dans le schéma de la 5.0. Vous pouvez ensuite entrer le nom de la base dans le fichier config.inc.php file pour l\'utiliser, $dbconfig[\'db_name\'] = \'nouveau nom db\';',
	'LBL_ENTER_MYSQL_SERVER_PATH' => 'Veuillez saisir le chemin vers MySQL',
	'LBL_SERVER_PATH_DESC' => 'Le chemin MySQL sur le serveur est du type /home/5beta/vtigerCRM5_beta/mysql/bin ou c:\\Program Files\\mysql\\bin',
	'LBL_MYSQL_SERVER_PATH' => 'Chemin vers MySQL : ',
	'LBL_MIGRATE_BUTTON' => 'Migrer',
	'LBL_CANCEL_BUTTON' => 'Annuler',
	'LBL_UPGRADE_FROM_VTIGER_5X' => 'Upgrade de votre base de données vtiger CRM 5.x vers la version courante',
	'LBL_PATCH_OR_MIGRATION' => 'Vous devez spécifier la version de la base de données à upgrader (upgrade ou migration)',
	'ENTER_SOURCE_HOST' => 'Veuillez saisir l\'hôte source',
	'ENTER_SOURCE_MYSQL_PORT' => 'Veuillez saisir le port source',
	'ENTER_SOURCE_MYSQL_USER' => 'Veuillez saisir l\'utilisateur source',
	'ENTER_SOURCE_DATABASE' => 'Veuillez saisir la base source',
	'ENTER_SOURCE_MYSQL_DUMP' => 'Veuillez utiliser un dump correct',
	'ENTER_HOST' => 'Veuillez saisir l\'hôte MySQL',
	'ENTER_MYSQL_PORT' => 'Veuillez saisir le port MySQL',
	'ENTER_MYSQL_USER' => 'Veuillez saisir un nom d\'utilisateur',
	'ENTER_DATABASE' => 'Veuillez saisir le nom de la base',
	'SELECT_ANYONE_OPTION' => 'Veuillez sélectionner au moins une option',
	'ENTER_CORRECT_MYSQL_PATH' => 'Veuillez saisir un chemin correct vers le binaire MySQL',
);
$mod_list_strings = array (
);
?>