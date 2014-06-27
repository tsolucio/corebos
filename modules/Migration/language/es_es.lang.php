<?php
/***********************************************************
*  Module       : Migration
*  Language     : ES Spanish
*  Version      : vt5.0.4
*  Created Date : 2008-01-18 17:46:07 Last change : 2008-01-20 00:48:29
*  Author       : JPL TSolucio, S.L.  -  rasomu
*  License      : 

***********************************************************/


$mod_strings = array (
      'LBL_MIGRATE_INFO' => 'Introduzca los Valores para Migrar los Datos desde la versión<b><i> Anterior </i></b> a la <b><i> Actual (Última) de vtigerCRM </i></b>',
      'LBL_CURRENT_VT_MYSQL_EXIST' => 'El MySQL del Actual vtiger Está en',
      'LBL_THIS_MACHINE' => 'Esta Máquina',
      'LBL_DIFFERENT_MACHINE' => 'Una Máquina Diferente',
      'LBL_CURRENT_VT_MYSQL_PATH' => 'Ruta de la base MySQL del vtiger Actual',
      'LBL_SOURCE_VT_MYSQL_DUMPFILE' => 'Nombre del fichero de Volcado <b>Origen</b> de vtiger',
      'LBL_NOTE_TITLE' => 'Nota:',
      'LBL_NOTES_LIST1' => 'Si MySQL está en la misma Máquina, introduzca la Ruta de MySQL (o) puede introducir el Fichero de volcado si dispone de uno.',
      'LBL_NOTES_LIST2' => 'Si MySQL está en otra Máquina, introduzca el Fichero de Volcado (origen) con la ruta completa.',
      'LBL_NOTES_DUMP_PROCESS' => 'Para obtener un volcado de la base de datos, ejecute el siguiente comando: 
        	   <br><b>mysqldump --user="mysql_username"  --password="mysql-password" -h "hostname"  --port="mysql_port" "database_name" > dump_filename</b>
			   <br>add <b>SET FOREIGN_KEY_CHECKS = 0;</b> -- at the start of the dump file
			   <br>add <b>SET FOREIGN_KEY_CHECKS = 1;</b> -- at the end of the dump file',
      'LBL_NOTES_LIST3' => 'Introduzca la ruta de Mysql como <b>/home/crm/vtigerCRM4_5/mysql</b>',
      'LBL_NOTES_LIST4' => 'Introduzca el la ruta al fichero de volcado como <b>/home/fullpath/4_2_dump.txt</b>',
      'LBL_CURRENT_MYSQL_PATH_FOUND' => 'La instalación Actual de MySQL ha sido encontrada.',
      'LBL_SOURCE_HOST_NAME' => 'Nombre de la Máquina Origen :',
      'LBL_SOURCE_MYSQL_PORT_NO' => 'Puerto de MySql Origen :',
      'LBL_SOURCE_MYSQL_USER_NAME' => 'Nombre de Usuario de MySql Origen  :',
      'LBL_SOURCE_MYSQL_PASSWORD' => 'Password de MySql Origen :',
      'LBL_SOURCE_DB_NAME' => 'Nombre de la Base de Datos de MySql Origen  :',
      'LBL_MIGRATE' => 'Migrar a la Versión Actual',
      'LBL_UPGRADE_VTIGER' => 'Actualizar BBDD  de Vtiger CRM',
      'LBL_UPGRADE_FROM_VTIGER_423' => 'Actualizar BBDD de Vtiger CRM de 4.2.3 a 5.0.0',
      'LBL_SETTINGS' => 'Configuración',
      'LBL_STEP' => 'Paso',
      'LBL_SELECT_SOURCE' => 'Seleccione Origen',
      'LBL_STEP1_DESC' => 'Para iniciar la migración de la BBDD, debe especificar el formato de la BBDD antigua',
      'LBL_RADIO_BUTTON1_TEXT' => 'Tengo acceso a la BBDD  de Vtiger CRM',
      'LBL_RADIO_BUTTON1_DESC' => 'Esta opción requiere que tenga acceso a la dirección de máquina que hospeda la BBDD y los datos de acceso de la BBDD. Es posible utilizar BBDD locales y remotas. Acuda a la documentación para ayuda',
      'LBL_RADIO_BUTTON2_TEXT' => 'Tengo acceso al volcado de la BBDD de Vtiger CRM',
      'LBL_RADIO_BUTTON2_DESC' => 'Esta opción requiere un volcado de la BBDD disponible localmente en la misma máquina que está actualizando. No se puede utilizar un volcado desde otra máquina (Servidor de BBDD remoto). Refiérase a la documentación para Ayuda.',
      'LBL_RADIO_BUTTON3_TEXT' => 'Tengo una BBDD nueva con datos de la versión 4.2.3',
      'LBL_RADIO_BUTTON3_DESC' => 'Esta opción requiere los datos de una BBDD de vtiger CRM 4.2.3, incluyendo Ip de la BBDD, usuario y contraseña. No se puede utilizar un volcado desde otra máquina (Servidor de BBDD remoto). Refiérase a la documentación para Ayuda.',
      'LBL_HOST_DB_ACCESS_DETAILS' => 'Detalles de acceso al servidor de base de datos',
      'LBL_MYSQL_HOST_NAME_IP' => 'Servidor MySQL o IP: ',
      'LBL_MYSQL_PORT' => 'Puerto MySQL: ',
      'LBL_MYSQL_USER_NAME' => 'Usuario MySql: ',
      'LBL_MYSQL_PASSWORD' => 'Password MySql: ',
      'LBL_DB_NAME' => 'Nombre de BBDD: ',
      'LBL_LOCATE_DB_DUMP_FILE' => 'Localizar el fichero de volcado',
      'LBL_DUMP_FILE_LOCATION' => 'Ubicacion del fichero de volcado: ',
      'LBL_RADIO_BUTTON3_PROCESS' => '<font color="red">Please do not specify the 4.2.3 database details. This option will alter the given database directly.</font>
<br>It is strongly recommended that to do the following.
<br>1. Take a dump of your 4.2.3 database
<br>2. Create new database (Better is to create a database in the server where your vtiger 5.0 Database is running.)
<br>3. Apply this 4.2.3 dump to this new database.
<br>Now give this new database access details. This migration will modify this Database to fit with the 5.0 Schema.
Then you can give this Database name in config.inc.php file to use this Database ie., $dbconfig[\'db_name\'] = \'new db name\';',
      'LBL_ENTER_MYSQL_SERVER_PATH' => 'Introduzca la ruta hasta el Servidor MySQL',
      'LBL_SERVER_PATH_DESC' => 'Ruta de MySQL en el servidor, p.e. <b>/home/5beta/vtigerCRM5_beta/mysql/bin</b> o <b>c:\Program Files\mysql\bin</b>',
      'LBL_MYSQL_SERVER_PATH' => 'Ruta del Servidor MySQL: ',
      'LBL_MIGRATE_BUTTON' => 'Migrar',
      'LBL_CANCEL_BUTTON' => 'Cancelar',
      'LBL_UPGRADE_FROM_VTIGER_5X' => 'Actualizar la BBDD de Vtiger CRM 5.x a la siguiente versión',
      'LBL_PATCH_OR_MIGRATION' => 'debes especificar la version de la base de datos origen (actualización con Patch o Migración)',
      'ENTER_SOURCE_HOST' => 'Introduzca el nombre del Servidor Origen',
      'ENTER_SOURCE_MYSQL_PORT' => 'Introduzca el nº de puerto del Servidor Mysql Origen',
      'ENTER_SOURCE_MYSQL_USER' => 'Introduzca el usuario del Servidor Mysql Origen',
      'ENTER_SOURCE_DATABASE' => 'Introduzca el nombre de la BBDD Origen',
      'ENTER_SOURCE_MYSQL_DUMP' => 'Introduzca un archivo de volcado Mysql válido',
      'ENTER_HOST' => 'Introduzca el nombre del Servidor Destino',
      'ENTER_MYSQL_PORT' => 'Introduzca el nombre del Servidor Destino',
      'ENTER_MYSQL_USER' => 'Introduzca el usuario del Servidor Mysql Destino',
      'ENTER_DATABASE' => 'Introduzca el nombre de la BBDD Destino',
      'SELECT_ANYONE_OPTION' => 'Seleccione alguna opción',
      'ENTER_CORRECT_MYSQL_PATH' => 'Introduzca una dirección Mysql correcta',
);
$mod_list_strings = array (
);
?>
