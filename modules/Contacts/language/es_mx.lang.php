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
*  Module       : Contacts
*  Language     : Español
*  Version      : 5.4.0
*  Created Date : 2007-03-30
*  Author       : Rafael Soler
*  Last change  : 2012-02-28
*  Author       : Joe Bordes JPL TSolucio, S.L.
*  Author       : Francisco Hernandez Odin Consultores www.odin.mx
 ********************************************************************************/

$mod_strings = array (
// Mike Crowe Mod --------------------------------------------------------Added for general search
'LBL_MODULE_NAME'=>'Contactos',
'LBL_INVITEE'=>'Informes',
'LBL_MODULE_TITLE'=>'Contactos: Inicio',
'LBL_SEARCH_FORM_TITLE'=>'Buscar Contacto',
'LBL_LIST_FORM_TITLE'=>'Lista de Contactos',
'LBL_NEW_FORM_TITLE'=>'Nuevo Contacto',
'LBL_CONTACT_OPP_FORM_TITLE'=>'Contacto-Oportunidad:',
'LBL_CONTACT'=>'Contacto:',

'LBL_LIST_NAME'=>'Nombre',
'LBL_LIST_LAST_NAME'=>'Apellidos',
'LBL_LIST_FIRST_NAME'=>'Nombre',
'LBL_LIST_CONTACT_NAME'=>'Contacto',
'LBL_LIST_TITLE'=>'Cargo',
'LBL_LIST_ACCOUNT_NAME'=>'Cuentas',
'LBL_LIST_EMAIL_ADDRESS'=>'Email',
'LBL_LIST_PHONE'=>'Teléfono',
'LBL_LIST_CONTACT_ROLE'=>'Rol',

//DON'T CONVERT THESE THEY ARE MAPPINGS
'db_last_name' => 'LBL_LIST_LAST_NAME',
'db_first_name' => 'LBL_LIST_FIRST_NAME',
'db_title' => 'LBL_LIST_TITLE',
'db_email1' => 'LBL_LIST_EMAIL_ADDRESS',
'db_email2' => 'LBL_LIST_EMAIL_ADDRESS',
//END DON'T CONVERT

'LBL_EXISTING_CONTACT' => 'El contacto ya existe',
'LBL_CREATED_CONTACT' => 'Nuevo contacto creado',
'LBL_EXISTING_ACCOUNT' => 'Usar cuenta existente',
'LBL_CREATED_ACCOUNT' => 'Nueva cuenta creada',
'LBL_CREATED_CALL' => 'Nueva llamada creada',
'LBL_CREATED_MEETING' => 'Nueva reunión creada',
'LBL_ADDMORE_BUSINESSCARD' => 'Agregar otra Tarjeta de Visita',

'LBL_BUSINESSCARD' => 'Tarjeta de Visita',

'LBL_NAME'=>'Nombre:',
'LBL_CONTACT_NAME'=>'Contacto:',
'LBL_CONTACT_INFORMATION'=>'Datos Personales',
'LBL_CUSTOM_INFORMATION'=>'Información Personalizada',
'LBL_FIRST_NAME'=>'Nombre:',
'LBL_OFFICE_PHONE'=>'Tel. Oficina:',
'LBL_ACCOUNT_NAME'=>'Cuenta:',
'LBL_ANY_PHONE'=>'Tel. Adicional:',
'LBL_PHONE'=>'Teléfono:',
'LBL_LAST_NAME'=>'Apellidos:',
'LBL_MOBILE_PHONE'=>'Tel. Móvil:',
'LBL_HOME_PHONE'=>'Tel. Particular:',
'LBL_LEAD_SOURCE'=>'Origen del Prospecto:',
'LBL_OTHER_PHONE'=>'Tel. Directo:',
'LBL_FAX_PHONE'=>'Fax:',
'LBL_TITLE'=>'Cargo:',
'LBL_DEPARTMENT'=>'Departamento:',
'LBL_BIRTHDATE'=>'Fecha de Nacimiento:',
'LBL_EMAIL_ADDRESS'=>'Email:',
'LBL_OTHER_EMAIL_ADDRESS'=>'Email (Otro):',
'LBL_ANY_EMAIL'=>'Email Adicional:',
'LBL_REPORTS_TO'=>'Informa a:',
'LBL_ASSISTANT'=>'Secretaria:',
'LBL_YAHOO_ID'=>'Mensajería Instantanea:',
'LBL_ASSISTANT_PHONE'=>'Teléfono Secretária:',
'LBL_DO_NOT_CALL'=>'No Llamar por Teléfono:',
'LBL_EMAIL_OPT_OUT'=>'No Enviar emails:',
'LBL_PRIMARY_ADDRESS'=>'Dirección (Principal):',
'LBL_ALTERNATE_ADDRESS'=>'Dirección (Otra):',
'LBL_ANY_ADDRESS'=>'Dirección (Alternativa):',
'LBL_CITY'=>'Deleg./Mpio.:',
'LBL_STATE'=>'Estado:',
'LBL_POSTAL_CODE'=>'Código Postal:',
'LBL_COUNTRY'=>'País:',
'LBL_DESCRIPTION_INFORMATION'=>'Descripción Adicional',
'LBL_IMAGE_INFORMATION'=>'Información de Foto del Contacto:',
'LBL_ADDRESS_INFORMATION'=>'Información de la Dirección',
'LBL_DESCRIPTION'=>'Descripción:',
'LBL_CONTACT_ROLE'=>'Rol:',
'LBL_OPP_NAME'=>'Oportunidad:',
'LBL_DUPLICATE'=>'Posible Contacto Duplicado',
'MSG_DUPLICATE'=>'Al crear este contacto puede duplicar un contacto existente. Seleccione un contacto de la lista inferior o pulse en el boton Crear Nuevo Contacto para crear un nuevo registro con los datos introducidos.',

'LNK_NEW_APPOINTMENT' => 'Agregar Evento',
'LBL_ADD_BUSINESSCARD' => 'Agregar Tarjeta de Visita',
'NTC_DELETE_CONFIRMATION'=>'¿Está seguro que desea eliminar este registro?',
'NTC_REMOVE_CONFIRMATION'=>'¿Está seguro que desea eliminar este contacto de este caso?',
'NTC_REMOVE_DIRECT_REPORT_CONFIRMATION'=>'¿Está seguro que desea eliminar este expediente como informe directo?',
'ERR_DELETE_RECORD'=>'Debe especificar un registro para poder eliminar el contacto.',
'NTC_COPY_PRIMARY_ADDRESS'=>'Copiar Principal a Alternativa',
'NTC_COPY_ALTERNATE_ADDRESS'=>'Copiar Alternativa a Principal',

'LBL_SELECT_CONTACT'=>'Seleccionar Contacto',
//Added for search heading
'LBL_GENERAL_INFORMATION'=>'Información General',



//for v4 release added
'LBL_NEW_POTENTIAL'=>'Nueva Oportunidad',
'LBL_POTENTIAL_TITLE'=>'Oportunidades',

'LBL_NEW_TASK'=>'Agregar Tarea',
'LBL_TASK_TITLE'=>'Tareas',
'LBL_NEW_CALL'=>'Agregar Llamada',
'LBL_CALL_TITLE'=>'Llamadas',
'LBL_NEW_MEETING'=>'Agregar Reunión',
'LBL_MEETING_TITLE'=>'Reuniones',
'LBL_NEW_EMAIL'=>'Nuevo Email',
'LBL_EMAIL_TITLE'=>'Emails',
'LBL_NEW_NOTE'=>'Agregar Documento',
'LBL_NOTE_TITLE'=>'Documentos',

// Added for 4GA
'LBL_TOOL_FORM_TITLE'=>'Herramientas de Contacto',

'Salutation'=>'Saludo',
'First Name'=>'Nombre',
'Office Phone'=>'Tel. Empresa',
'Last Name'=>'Apellidos',
'Mobile'=>'Tel. Móvil',
'Account Name'=>'Cuenta',
'Home Phone'=>'Tel. Particular',
'Lead Source'=>'Origen de Prospecto',
'Other Phone'=>'Tel. Directo',
'Title'=>'Cargo',
'Fax'=>'Fax',
'Department'=>'Departamento',
'Birthdate'=>'Fecha de Nacimiento',
'Email'=>'Email',
'Reports To'=>'Informa a',
'Assistant'=>'Secretaria',
'Yahoo Id'=>'Mensajería Instantanea',
'Assistant Phone'=>'Teléfono de la Secretaria',
'Do Not Call'=>'No Llamar por Teléfono',
'Email Opt Out'=>'No Enviar Emails',
'Assigned To'=>'Asignado a',
'Campaign Source'=>'Origen de Campaña',
'Reference' => 'Referencias',
'Created Time'=>'Fecha de Alta',
'Modified Time'=>'Fecha de Modificación',
'Mailing Street'=>'Dirección (Factura)',
'Other Street'=>'Dirección (Envío)',
'Mailing City'=>'Deleg./Mpio. (Factura)',
'Mailing State'=>'Estado (Factura)',
'Mailing Zip'=>'Código Postal (Factura)',
'Mailing Country'=>'País (Factura)',
'Mailing Po Box'=>'Colonia (Factura)',
'Other Po Box'=>'Colonia (Envío)',
'Other City'=>'Deleg./Mpio. (Envío)',
'Other State'=>'Estado (Envío)',
'Other Zip'=>'Código Postal (Envío)',
'Other Country'=>'País (Envío)',
'Contact Image'=>'Imagen del Contacto',
'Description'=>'Descripción',

// Added vtiger_fields for Add Business Card
'LBL_NEW_CONTACT'=>'Agregar Contacto',
'LBL_NEW_ACCOUNT'=>'Agregar Cuenta',
'LBL_NOTE_SUBJECT'=>'Asunto:',
'LBL_NOTE'=>'Nota:',
'LBL_WEBSITE'=>'Página Web:',
'LBL_NEW_APPOINTMENT'=>'Agregar Evento',
'LBL_SUBJECT'=>'Asunto:',
'LBL_START_DATE'=>'Fecha de Inicio:',
'LBL_START_TIME'=>'Hora de Inicio:',

//Added vtiger_field after 4_0_1
'Portal User'=>'Usuario de Portal',
'LBL_CUSTOMER_PORTAL_INFORMATION'=>'Información del Cliente',
'Support Start Date'=>'Inicio de Soporte',
'Support End Date'=>'Vencimiento de Soporte ',
//Added for 4.2 Release -- CustomView
'Name'=>'Nombre',
'LBL_ALL'=>'Todos',
'LBL_MAXIMUM_LIMIT_ERROR'=>'El archivo excede el tamaño máximo permitido. Pruebe con un archivo inferior a 800Kbytes',
'LBL_UPLOAD_ERROR'=>'Problemas al subir el archivo. ¡Inténtelo otra vez!',
'LBL_IMAGE_ERROR'=>'El archivo no es de tipo imágen(.gif/.jpg/.png)',
'LBL_INVALID_IMAGE'=>'Archivo inválido o no tiene datos',

//Added after 5Alpha5
'Notify Owner'=>'Notificar al Propietario',

//Added for Picklist Values
'--None--'=>'-----',

'Mr.'=>'Sr.',
'Ms.'=>'Sra.',
'Mrs.'=>'Srta.',
'Dr.'=>'Dr.',
'Prof.'=>'Prof.',

'Cold Call'=>'Llamada en frío',
'Existing Customer'=>'Cliente',
'Self Generated'=>'Autogenerada',
'Employee'=>'Trabajador',
'Partner'=>'Socio',
'Public Relations'=>'Relaciones Públicas',
'Direct Mail'=>'Mailing',
'Conference'=>'Conferencia',
'Trade Show'=>'Feria',
'Web Site'=>'Web',
'Word of mouth'=>'Boca a Boca',
'Other'=>'Otro',
'User List'=>'Lista de Usuarios',

//Added for 5.0.3
'Customer Portal Login Details'=>'Detalles de Identificación para Portal de Cliente',
'Dear'=>'Estimado',
'Your Customer Portal Login details are given below:'=>'Los datos de Identificación par el Portal de Cliente son:',
'User Id :'=>'Usuario:',
'Password :'=>'Contraseña:',
'Please Login Here'=>'Por favor entre aquí.',
'Note :'=>'Nota:',
'We suggest you to change your password after logging in first time'=>'Le sugerimos que cambie la contraseña al identificarse por primera vez',
'Support Team'=>'El Equipo de Soporte Técnico',

'TITLE_AJAX_CSS_POPUP_CHAT'=>'Chat',

// Added after 5.0.4 GA

// Module Sequence Numbering
'Contact Id' => 'Id Contacto',
// END
'Secondary Email'=>'Email Segundario',

'Contacts ID'=>'Id Contacto',

);

?>
