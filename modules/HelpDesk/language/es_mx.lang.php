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
*  Module       : Helpdesk
*  Language     : Español
*  Version      : 5.4.0.
*  Created Date : 2007-03-30 Last change : 2012-02-28
*  Author       : Rafael Soler
*  Author       : Francisco Hernandez Odin Consultores www.odin.mx
 ********************************************************************************/

$mod_strings = Array(
// Added in release 4.0
'LBL_MODULE_NAME' => 'Casos',
'LBL_GROUP' => 'Grupo',
'LBL_ACCOUNT_NAME' => 'Nombre de Cuenta',
'LBL_CONTACT_NAME' => 'Nombre de Contacto',
'LBL_SUBJECT' => 'Asunto',
'LBL_NEW_FORM_TITLE' => 'Nuevo Caso',
'LBL_DESCRIPTION' => 'Descripción',
'NTC_DELETE_CONFIRMATION' => '¿Está seguro que desea eliminar este registro?',
'LBL_CUSTOM_FIELD_SETTINGS' => 'Configuración de campos personalizados:',
'LBL_PICKLIST_FIELD_SETTINGS' => 'Configuración de Campos de Lista:',
'Leads' => 'Prospecto',
'Accounts' => 'Cuenta',
'Contacts' => 'Contacto',
'Opportunities' => 'Oportunidad',
'LBL_CUSTOM_INFORMATION' => 'Información personalizada',
'LBL_DESCRIPTION_INFORMATION' => 'Caso a resolver',

'LBL_ACCOUNT' => 'Cuenta',
'LBL_OPPURTUNITY' => 'Oportunidad',
'LBL_PRODUCT' => 'Producto',

'LBL_COLON' => ':',
'LBL_TICKET' => 'Caso',
'LBL_CONTACT' => 'Contacto',
'LBL_STATUS' => 'Estado',
'LBL_ASSIGNED_TO' => 'Asignado a',
'LBL_FAQ' => 'FAQ',
'LBL_VIEW_FAQS' => 'Ver FAQs',
'LBL_ADD_FAQS' => 'Agregar FAQs',
'LBL_FAQ_CATEGORIES' => 'Categorías FAQs',

'LBL_PRIORITY' => 'Prioridad',
'LBL_CATEGORY' => 'Categoría',

'LBL_ANSWER' => 'Respuesta',
'LBL_COMMENTS' => 'COMENTARIOS',

'LBL_AUTHOR' => 'Autor',
'LBL_QUESTION' => 'Pregunta',

//Added vtiger_fields for File Attachment and Mail send in Tickets
'LBL_ATTACHMENTS' => 'Adjuntos',
'LBL_NEW_ATTACHMENT' => 'Nuevo Adjunto',
'LBL_SEND_MAIL' => 'Enviar Email',

//Added vtiger_fields for search option  in TicketsList -- 4Beta
'LBL_CREATED_DATE' => 'Fecha de Creación',
'LBL_IS' => 'es',
'LBL_IS_NOT' => 'no es',
'LBL_IS_BEFORE' => 'es antes',
'LBL_IS_AFTER' => 'es después',
'LBL_STATISTICS' => 'Estadísticas',
'LBL_TICKET_ID' => 'Nº de Caso',
'LBL_MY_TICKETS' => 'Mis casos',
'LBL_MY_FAQ' => 'Mis FAQ\'s',
'LBL_ESTIMATED_FINISHING_TIME' => 'Tiempo estimado de resolución',
'LBL_SELECT_TICKET' => 'Seleccionar Caso',
'LBL_CHANGE_OWNER' => 'Modificar Propietario',
'LBL_CHANGE_STATUS' => 'Modificar Estado',
'LBL_TICKET_TITLE' => 'Referencia',
'LBL_TICKET_DESCRIPTION' => 'Explicación',
'LBL_TICKET_CATEGORY' => 'Categoría',
'LBL_TICKET_PRIORITY' => 'Prioridad',

//Added vtiger_fields after 4 -- Beta
'LBL_NEW_TICKET' => 'Nuevo Caso',
'LBL_TICKET_INFORMATION' => 'Información del Caso',

'LBL_LIST_FORM_TITLE' => 'Lista de casos',
'LBL_SEARCH_FORM_TITLE' => 'Buscar Caso',

//Added vtiger_fields after RC1 - Release
'LBL_CHOOSE_A_VIEW' => 'Seleccionar una vista...',
'LBL_ALL' => 'Todos',
'LBL_LOW' => 'Baja',
'LBL_MEDIUM' => 'Media',
'LBL_HIGH' => 'Alta',
'LBL_CRITICAL' => 'Crítica',
//Added vtiger_fields for 4GA
'Assigned To' => 'Asignado a',
'Contact Name' => 'Nombre de Contacto',
'Priority' => 'Prioridad',
'Status' => 'Estado',
'Category' => 'Categoría',
'Update History' => 'Histórico de Actualizaciones',
'Created Time' => 'Fecha de Creación',
'Modified Time' => 'Última Modificación',
'Title' => ' Referencia',
'Description' => 'Caso',

'LBL_TICKET_CUMULATIVE_STATISTICS' => 'Estadísticas acumuladas de casos:',
'LBL_CASE_TOPIC' => 'Tópico de Incidentes',
'LBL_OPEN' => 'Abierto',
'LBL_CLOSED' => 'Cerrado',
'LBL_TOTAL' => 'Total',
'LBL_TICKET_HISTORY' => 'Historia del Caso:',
'LBL_CATEGORIES' => 'Categorías',
'LBL_PRIORITIES' => 'Prioridades',
'LBL_SUPPORTERS' => 'Agentes',

//Added vtiger_fields after 4_0_1
'LBL_TICKET_RESOLUTION' => 'Solución Propuesta',
'Solution' => 'Solución',
'Add Comment' => 'Agregar comentario',
'LBL_ADD_COMMENT' => 'Agregar comentario',

//Added for 4.2 Release -- CustomView
'Ticket ID' => 'ID del Caso',
'Subject' => 'Asunto',

//Added after 4.2 alpha
'Severity' => 'Importancia',
'Product Name' => 'Producto',
'Related To' => 'Relacionado con',
'LBL_MORE' => 'Más',

'LBL_TICKETS' => 'casos',

//Added on 09-12-2005
'LBL_CUMULATIVE_STATISTICS' => 'Estadísticas Acumuladas',

//Added on 12-12-2005
'LBL_CONVERT_AS_FAQ_BUTTON_TITLE' => 'Convertir en FAQ',
'LBL_CONVERT_AS_FAQ_BUTTON_KEY' => 'C',
'LBL_CONVERT_AS_FAQ_BUTTON_LABEL' => 'Convertir en FAQ',
'Attachment' => 'Adjunto',
'LBL_COMMENT_INFORMATION' => 'Comentarios al Caso',

//Added for existing picklist entries

'Big Problem' => 'Problema Grave',
'Small Problem' => 'Problema menor',
'Other Problem' => 'Otro tipo de Problema',
'Low' => 'Baja',

'Normal' => 'Normal',
'High' => 'Alta',
'Urgent' => 'Urgente',

'Minor' => 'Menor',
'Major' => 'Mayor',
'Feature' => 'Característica',
'Critical' => 'Critica',

'Open' => 'Abierta',
'In Progress' => 'En Progreso',
'Wait For Response' => 'Esperando Respuesta',
'Closed' => 'Cerrada',

//added to support i18n in ticket mails
'Hi' => 'Hola',
'Dear' => 'Estimado',
'LBL_PORTAL_BODY_MAILINFO' => 'El Caso ha sido',
'LBL_DETAIL' => ', los detalles son:',
'LBL_REGARDS' => 'Atentamente,',
'LBL_TEAM' => 'Equipo de Soporte Técnico',
'LBL_TICKET_DETAILS' => 'Detalles de Caso',
'LBL_SUBJECT' => 'Asunto : ',
'created' => 'creado',
'replied' => 'respondido',
'reply' => 'Hay una respuesta al caso: ',
'customer_portal' => ' en el Portal de Clientes de VtigerCRM. ',
'link' => 'Utilice el siguiente enlace para ver las respuestas del caso en referencia:',
'Thanks' => 'Gracias',
'Support_team' => 'Equipo de Soporte Técnico',
'The comments are' => 'Los comentarios son',
'Ticket Title' => 'Título Caso',
'Re' => 'Re :',
// Added/Updated for vtiger CRM 5.0.4

//this label for customerportal.
'LBL_STATUS_CLOSED' =>'Closed',//Do not convert this label. This is used to check the status. If the status 'Closed' is changed in vtigerCRM server side then you have to change in customerportal language file also.
'LBL_STATUS_UPDATE' => 'Estado de Caso actualizado a',
'LBL_COULDNOT_CLOSED' => 'El Caso no puede ser',
'LBL_CUSTOMER_COMMENTS' => 'EL Cliente ha incluido la siguiente información a su respuesta:',
'LBL_RESPOND'=> 'Por favor responde al Caso lo más pronto posible.',
'LBL_REGARDS' =>'Saludos Cordiales,',
'LBL_SUPPORT_ADMIN' => 'Atención al Cliente',
'LBL_RESPONDTO_TICKETID' =>'Responde al Nº de Caso',
'LBL_CUSTOMER_PORTAL' => 'en el Portal del Cliente - URGENTE', 
'LBL_LOGIN_DETAILS' => 'Sus datos de conexión al Portal de Cliente son:',
'LBL_MAIL_COULDNOT_SENT' =>'No se puede enviar el correo',
'LBL_USERNAME' => 'Usuario :',
'LBL_PASSWORD' => 'Contraseña :',
'LBL_SUBJECT_PORTAL_LOGIN_DETAILS' => 'Datos de Conexión al Portal del Cliente',
'LBL_GIVE_MAILID' => 'Introduzca dirección de email',
'LBL_CHECK_MAILID' => 'Compruebe su dirección de email para el Portal del Cliente',
'LBL_LOGIN_REVOKED' => 'Datos de Usuario no válidos, consulte con su administrador.',
'LBL_MAIL_SENT' => 'Se le ha enviado un correo con los datos de conexión al Portal del Cliente',
'LBL_ALTBODY' => 'Este es el mensaje de correo para los clientes que no soportan HTML',
'Hours' => 'Horas',
'Days' => 'Días',
// Added after 5.0.4 GA

// Module Sequence Numbering
'Ticket No' => 'Núm. Caso',
// END
'From Portal' => 'Proviene del Portal',
'HelpDesk ID' => 'Id Incidencia',
);

?>
