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

 ********************************************************************************
*  Module       : Emails
*  Language     : Español
*  Version      : 504
*  Created Date : 2007-03-30 Last change : 2007-10-10
*  Author       : Rafael Soler
*  Author       : Francisco Hernandez Odin Consultores www.odin.mx
 ********************************************************************************/

$mod_strings = Array(
// Mike Crowe Mod --------------------------------------------------------added for general search
'LBL_GENERAL_INFORMATION'=>'Información General',

'LBL_MODULE_NAME'=>'Email',
'LBL_MODULE_TITLE'=>'Email: Inicio',
'LBL_SEARCH_FORM_TITLE'=>'Buscar correo',
'LBL_LIST_FORM_TITLE'=>'Lista de correos',
'LBL_NEW_FORM_TITLE'=>'Seguimiento de Correo',

'LBL_LIST_SUBJECT'=>'Asunto',
'LBL_LIST_CONTACT'=>'Contacto',
'LBL_LIST_RELATED_TO'=>'Relacionado con',
'LBL_LIST_DATE'=>'Fecha de Envío',
'LBL_LIST_TIME'=>'Hora de Envío',

'ERR_DELETE_RECORD'=>'Debe especificar un registro para eliminar la cuenta.',
'LBL_DATE_SENT'=>'Fecha de Envío:',
'LBL_DATE_AND_TIME'=>'Fecha y Hora de envío:',
'LBL_DATE'=>'Fecha de envío:',
'LBL_TIME'=>'Hora de envío:',
'LBL_SUBJECT'=>'Asunto:',
'LBL_BODY'=>'Cuerpo:',
'LBL_CONTACT_NAME'=>' Nombre: ',
'LBL_EMAIL'=>'Email:',
'LBL_DETAILVIEW_EMAIL'=>'E-Mail',
'LBL_COLON'=>':',
'LBL_CHK_MAIL'=>'Comprobar Correo',
'LBL_COMPOSE'=>'Redactar',
//Single change for 5.0.3
'LBL_SETTINGS'=>'Configuración',
'LBL_EMAIL_FOLDERS'=>'Carpetas de Email',
'LBL_INBOX'=>'Bandeja de Entrada',
'LBL_SENT_MAILS'=>'Emails Enviados',
'LBL_TRASH'=>'Basura',
'LBL_JUNK_MAILS'=>'Eliminados',
'LBL_TO_LEADS'=>'A Prospectos',
'LBL_TO_CONTACTS'=>'A Contactos',
'LBL_TO_ACCOUNTS'=>'A Cuentas',
'LBL_MY_MAILS'=>'Mis Correos',
'LBL_QUAL_CONTACT'=>'Emails Clasificados por Contacto',
'LBL_MAILS'=>'Correos',
'LBL_QUALIFY_BUTTON'=>'Clasificar',
'LBL_REPLY_BUTTON'=>'Responder',
'LBL_FORWARD_BUTTON'=>'Reenviar',
'LBL_DOWNLOAD_ATTCH_BUTTON'=>'Descargar Adjuntos',
'LBL_FROM'=>'De: ',
'LBL_CC'=>'Cc: ',
'LBL_BCC'=>'Cco: ',

'NTC_REMOVE_INVITEE'=>'¿Está seguro de eliminar esta dirección de Email?',
'LBL_INVITEE'=>'Invitados',

// Added Fields
// Contacts-SubPanelViewContactsAndUsers.php
'LBL_BULK_MAILS'=>'Emails masivos',
'LBL_ATTACHMENT'=>'Adjunto',
'LBL_UPLOAD'=>'Actualizar',
'LBL_FILE_NAME'=>'Nombre de Archivo',
'LBL_SEND'=>'Enviar',

'LBL_EMAIL_TEMPLATES'=>'Plantillas de Email',
'LBL_TEMPLATE_NAME'=>'Nombre de Plantilla',
'LBL_DESCRIPTION'=>'Descripción',
'LBL_EMAIL_TEMPLATES_LIST'=>'Lista de Plantillas de Email',
'LBL_EMAIL_INFORMATION'=>'Información de email',




//for v4 release added
'LBL_NEW_LEAD'=>'Nuevo Prospecto',
'LBL_LEAD_TITLE'=>'Prospectos',

'LBL_NEW_PRODUCT'=>'Nuevo Producto',
'LBL_PRODUCT_TITLE'=>'Productos',
'LBL_NEW_CONTACT'=>'Nuevo Contacto',
'LBL_CONTACT_TITLE'=>'Contactos',
'LBL_NEW_ACCOUNT'=>'Nueva Cuenta',
'LBL_ACCOUNT_TITLE'=>'Cuentas',

// Added vtiger_fields after vtiger4 - Beta
'LBL_USER_TITLE'=>'Usuarios',
'LBL_NEW_USER'=>'Nuevo Usuario',

// Added for 4 GA
'LBL_TOOL_FORM_TITLE'=>'Herramientas de Correo',
//Added for 4GA
'Date & Time Sent'=>'Fecha y Hora de envío',
'Sales Enity Module'=>'Módulo Entidad de Ventas',
'Related To'=>'Relacionado con',
'Assigned To'=>'Asignado a',
'Subject'=>'Asunto',
'Attachment'=>'Adjunto',
'Description'=>'Descripción',
'Time Start'=>'Fecha de Inicio',
'Created Time'=>'Fecha Creación ',
'Modified Time'=>'Fecha Modificación',

'MESSAGE_CHECK_MAIL_SERVER_NAME'=>'Por favor, verifique el nombre del servidor de correo...',
'MESSAGE_CHECK_MAIL_ID'=>'Por favor, verifique el Email de "Asignado A" ...',
'MESSAGE_MAIL_HAS_SENT_TO_USERS'=>'El Email ha sido enviado a los seguientes usuarios :',
'MESSAGE_MAIL_HAS_SENT_TO_CONTACTS'=>'El Email ha sido enviado a los siguientes contactos :',
'MESSAGE_MAIL_ID_IS_INCORRECT'=>'El Email es incorrecto. Por favor verifique este Email...',
'MESSAGE_ADD_USER_OR_CONTACT'=>'Por favor, agregue usuarios y contactos...',
'MESSAGE_MAIL_SENT_SUCCESSFULLY'=>' ¡El Correo ha sido enviado con exito!',

// Added for web mail post 4.0.1 release
'LBL_FETCH_WEBMAIL'=>'Cargar WebMail',
//Added for 4.2 Release -- CustomView
'LBL_ALL'=>'Todos',
'MESSAGE_CONTACT_NOT_WANT_MAIL'=>'Este contacto no desea recibir correos.',
'LBL_WEBMAILS_TITLE'=>'Email',
'LBL_EMAILS_TITLE'=>'Email',
'LBL_MAIL_CONNECT_ERROR_INFO'=>'¡Error conectando con el servidor de email!<br> Compruebe en Configuración -> Lista de Servidores de Correo -> Lista de cuentas de correo',
'LBL_ALLMAILS'=>'Todos los Correos',
'LBL_TO_USERS'=>'A Usuarios',
'LBL_TO'=>'A: ',
'LBL_IN_SUBJECT'=>'en Asunto',
'LBL_IN_SENDER'=>'en Remitente',
'LBL_IN_SUBJECT_OR_SENDER'=>'en Asunto o Remitente',
'SELECT_EMAIL'=>'Selecciona Email',
'Sender'=>'Remitente',
'LBL_CONFIGURE_MAIL_SETTINGS'=>'Su Servidor Entrante de Email no está configurado',
'LBL_MAILSELECT_INFO1'=>'Los siguientes tipos de email están asociados a la selección',
'LBL_MAILSELECT_INFO2'=>'Seleccione los tipos de email a los que hay que enviar el correo',
'LBL_MULTIPLE'=>'Multiples',
'LBL_COMPOSE_EMAIL'=>'Redactar Correo',
'LBL_VTIGER_EMAIL_CLIENT'=>'Cliente de Correo',

//Added for 5.0.3
'TITLE_VTIGERCRM_MAIL'=>'Correo',
'TITLE_COMPOSE_MAIL'=>'Redactar Correo',

'MESSAGE_MAIL_COULD_NOT_BE_SEND'=>'No se ha podido mandar correo al usuario asignado.',
'MESSAGE_PLEASE_CHECK_ASSIGNED_USER_EMAILID'=>'Por favor verifica la cuenta de correo del usuario asignado...',
'MESSAGE_PLEASE_CHECK_THE_FROM_MAILID'=>'Por favor verifica la cuenta de correo del remitente',
'MESSAGE_MAIL_COULD_NOT_BE_SEND_TO_THIS_EMAILID'=>'No se ha podido mandar correo a esta cuenta de correo',
'PLEASE_CHECK_THIS_EMAILID'=>'Por favor verifica esta cuenta de correo...',
'LBL_CC_EMAIL_ERROR'=>'Cuenta de correo cc incorrecta',
'LBL_BCC_EMAIL_ERROR'=>'Cuenta de correo bcc incorrecta',
'LBL_NO_RCPTS_EMAIL_ERROR'=>'No se ha especificado destinatario',
'LBL_CONF_MAILSERVER_ERROR'=>'Configura el servidor de correo saliente en Herramientas --> Servidor de Correo',
'LBL_VTIGER_EMAIL_CLIENT'=>'Webmail Vtiger',
'LBL_MAILSELECT_INFO3'=>'No tienes permiso para visualizar las cuentas de correo del registro seleccionado.',
//Added  for script alerts
'FEATURE_AVAILABLE_INFO' => 'Característica disponible sólo para Microsoft Internet Explorer 5.5+\n\n 
¡Actualice su navegador!',
'DOWNLOAD_CONFIRAMATION' => '¿Descargar Archivo?',
'LBL_PLEASE_ATTACH' => '¡Adjunte un archivo permitido!',
'LBL_KINDLY_UPLOAD' => '¡Configure la variable<font color="red">upload_tmp_dir</font> en el php.ini',
'LBL_EXCEED_MAX' => 'El Archivo sobrepasa el tamaño máximo permitido.Inténtelo con un archivo más pequeño.',
'LBL_BYTES' => ' bytes',
'LBL_CHECK_USER_MAILID' => 'Compruebe que el correo del usuario es una cuenta de correo válida',

// Added/Updated for vtiger CRM 5.0.4
'Activity Type'=>'Tipo de Evento',
'LBL_MAILSELECT_INFO'=>'tiene los siguientes emails configurados. Seleccione los que quiera utilizar',
'LBL_NO_RECORDS'=>'No hay registros en esta carpeta',
'LBL_PRINT_EMAIL'=> 'Imprimir',

);

?>
