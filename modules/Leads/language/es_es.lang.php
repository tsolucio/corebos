<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************
*  Module       : Leads
*  Language     : Español
*  Version      : 5.4.0
*  Created Date : 2007-03-30
*  Last change  : 2012-02-28
*  Author       : Joe Bordes JPL TSolucio, S.L.
 ********************************************************************************/

if ((isset($_COOKIE['LeadConv']) && $_COOKIE['LeadConv'] == 'true')) {
	$toggle_historicos = 'Ver Pre-Contactos SIN Convertir';
	$toggle_name = 'Pre-Contactos Convertidos';
} else {
	$toggle_historicos = 'Ver Pre-Contactos Convertidos';
	$toggle_name = 'Pre-Contactos';
}

$mod_strings = Array(
'LBL_TGL_HISTORICOS' => $toggle_historicos,
'LBL_MODULE_NAME'=>$toggle_name,
'Leads' => $toggle_name,
'LBL_DIRECT_REPORTS_FORM_NAME'=>'Informes Directos',
'LBL_MODULE_TITLE'=>'Pre-Contactos: Inicio',
'LBL_SEARCH_FORM_TITLE'=>'Buscar Pre-Contacto',
'LBL_LIST_FORM_TITLE'=>'Lista de Pre-Contactos',
'LBL_NEW_FORM_TITLE'=>'Nuevo Pre-Contactos',
'LBL_LEAD_OPP_FORM_TITLE'=>'Contacto-Oportunidad:',
'LBL_LEAD'=>'Pre-Contacto:',
'LBL_ADDRESS_INFORMATION'=>'Información de la Dirección',
'LBL_CUSTOM_INFORMATION'=>'Información Específica',

'LBL_LIST_NAME'=>'Nombre',
'LBL_LIST_LAST_NAME'=>'Apellido',
'LBL_LIST_COMPANY'=>'Cuenta',
'LBL_LIST_WEBSITE'=>'Página Web',
'LBL_LIST_LEAD_NAME'=>'Pre-Contactos ',
'LBL_LIST_EMAIL'=>'Email',
'LBL_LIST_PHONE'=>'Teléfono',
'LBL_LIST_LEAD_ROLE'=>'Rol',

'LBL_NAME'=>'Nombre:',
'LBL_LEAD_NAME'=>' Pre-Contacto:',
'LBL_LEAD_INFORMATION'=>'Infomación del Pre-Contacto',
'LBL_FIRST_NAME'=>'Nombre:',
'LBL_PHONE'=>'Teléfono:',
'LBL_COMPANY'=>'Cuenta:',
'LBL_DESIGNATION'=>'Designación:',
'LBL_PHONE'=>'Teléfono:',
'LBL_LAST_NAME'=>'Apellido:',
'LBL_MOBILE'=>'Tel. Móvil:',
'LBL_EMAIL'=>'Email:',
'LBL_LEAD_SOURCE'=>'Origen:',
'LBL_LEAD_STATUS'=>'Estado:',
'LBL_WEBSITE'=>'Página Web:',
'LBL_FAX'=>'Fax:',
'LBL_INDUSTRY'=>'Actividad:',
'LBL_ANNUAL_REVENUE'=>'Facturación Anual:',
'LBL_RATING'=>'Valoración:',
'LBL_LICENSE_KEY'=>'Clave de la Licencia:',
'LBL_NO_OF_EMPLOYEES'=>'Numero de Empleados:',
'LBL_YAHOO_ID'=>'Mensajería instantanea:',

'LBL_ADDRESS_STREET'=>'Dirección:',
'LBL_ADDRESS_POSTAL_CODE'=>'Código Postal:',
'LBL_ADDRESS_CITY'=>'Población:',
'LBL_ADDRESS_COUNTRY'=>'País:',
'LBL_ADDRESS_STATE'=>'Provincia:',
'LBL_ADDRESS'=>'Dirección:',
'LBL_DESCRIPTION_INFORMATION'=>'Información Adicional',
'LBL_DESCRIPTION'=>'Descripción:',

'LBL_CONVERT_LEAD'=>'Convertir Pre-Contacto:',
'LBL_CONVERT_LEAD_INFORMATION'=>'Convertir Información del Pre-Contacto',
'LBL_ACCOUNT_NAME'=>'Nombre de la Cuenta',
'LBL_POTENTIAL_NAME'=>'Oportunidad',
'LBL_POTENTIAL_CLOSE_DATE'=>'Fecha de cierre de la Oportunidad',
'LBL_POTENTIAL_AMOUNT'=>'Importe de la Oportunidad',
'LBL_POTENTIAL_SALES_STAGE'=>'Estado de la Oportunidad',

'NTC_DELETE_CONFIRMATION'=>'¿Está seguro que desea eliminar este registro?',
'NTC_REMOVE_CONFIRMATION'=>'¿Está seguro que desea eliminar el contacto de este Pre-contacto?',
'NTC_REMOVE_DIRECT_REPORT_CONFIRMATION'=>'¿Está usted seguro usted desea quitar este expediente como un informe directo?',
'NTC_REMOVE_OPP_CONFIRMATION'=>'¿Est&aacute seguro usted desea eliminar este contacto de esta oportunidad?',
'ERR_DELETE_RECORD'=>'Debe especificar un registro para poder eliminar el contacto.',

'LBL_COLON'=>' : ',
'LBL_IMPORT_LEADS'=>'Importar Pre-Contacto',
'LBL_LEADS_FILE_LIST'=>'Lista de archivos Pre-Contacto',
'LBL_INSTRUCTIONS'=>'Instruciones',
'LBL_KINDLY_PROVIDE_AN_XLS_FILE'=>'Indique un archivo .xls como entrada',
'LBL_PROVIDE_ATLEAST_ONE_FILE'=>'Por favor, indique al menos un archivo como entrada',

'LBL_NONE'=>'Ninguno',
'LBL_ASSIGNED_TO'=>'Asignado a:',
'LBL_SELECT_LEAD'=>'Seleccionar Pre-Contactos',
'LBL_GENERAL_INFORMATION'=>'Información General',
'LBL_DO_NOT_CREATE_NEW_POTENTIAL'=>'No crear una nueva oportunidad tras la conversión',

'LBL_NEW_POTENTIAL'=>'Nueva Oportunidad',
'LBL_POTENTIAL_TITLE'=>'Oportunidades',

'LBL_NEW_TASK'=>'Nueva Tarea',
'LBL_TASK_TITLE'=>'Tareas',
'LBL_NEW_CALL'=>'Nueva Llamada',
'LBL_CALL_TITLE'=>'Llamada',
'LBL_NEW_MEETING'=>'Nueva Reunión',
'LBL_MEETING_TITLE'=>'Reuniones',
'LBL_NEW_EMAIL'=>'Nuevo Email',
'LBL_EMAIL_TITLE'=>'Emails',
'LBL_NEW_NOTE'=>'Nuevo Documento',
'LBL_NOTE_TITLE'=>'Documentos',
'LBL_NEW_ATTACHMENT'=>'Nuevo Adjunto',
'LBL_ATTACHMENT_TITLE'=>'Adjuntos',

'LBL_ALL'=>'Todo',
'LBL_CONTACTED'=>'Contactado',
'LBL_LOST'=>'Perdido',
'LBL_HOT'=>'Caliente',
'LBL_COLD'=>'Frio',

'LBL_TOOL_FORM_TITLE'=>'Herramientas de Pre-Contacto',

'LBL_SELECT_TEMPLATE_TO_MAIL_MERGE'=>'Seleccione una plantilla para enviar el Mailing:',

'Salutation'=>'Saludo',
'First Name'=>'Nombre',
'Phone'=>'Teléfono',
'Last Name'=>'Apellidos',
'Mobile'=>'Tel .Móvil',
'Company'=>'Empresa',
'Fax'=>'Fax',
'Email'=>'Email',
'Lead Source'=>'Origen del Pre-Contacto',
'Website'=>'Página Web',
'Annual Revenue'=>'Facturación Anual',
'Lead Status'=>'Estado del Pre-Contacto',
'Industry'=>'Actividad',
'Rating'=>'Valoración',
'No Of Employees'=>'Número de Empleados',
'Assigned To'=>'Asignado a',
'Yahoo Id'=>'Mensajería instantanea',
'Created Time'=>'Fecha de Creación',
'Modified Time'=>'Última Modificación',
'Street'=>'Dirección',
'Postal Code'=>'Código Postal',
'City'=>'Población',
'Country'=>'País',
'State'=>'Provincia',
'Description'=>'Descripción',
'Po Box'=>'Apdo. Correos',
'Campaign Source'=>'Campaña Origen',
'Name'=>'Nombre',
'LBL_NEW_LEADS'=>'Mis Pre-Contactos',

//Added for Existing Picklist Entries
'--None--'=>'-----',
'Mr.'=>'Sr.',
'Ms.'=>'Sra.',
'Mrs.'=>'Srta.',
'Dr.'=>'Dr.',
'Prof.'=>'Prof.',

'Acquired'=>'Adquirido',
'Active'=>'Activo',
'Market Failed'=>'Mercado Inmaduro',
'Project Cancelled'=>'Cancelado',
'Shutdown'=>'Suspendido',

'Apparel'=>'Ropa/Lenceria',
'Banking'=>'Banca',
'Biotechnology'=>'Biotecnología',
'Chemicals'=>'Químicas',
'Communications'=>'Comunicaciones',
'Construction'=>'Construcción',
'Consulting'=>'Consultoría',
'Education'=>'Educación',
'Electronics'=>'Electronica',
'Energy'=>'Energía',
'Engineering'=>'Ingeniería',
'Entertainment'=>'Entretenimiento',
'Environmental'=>'Medio Ambiente',
'Finance'=>'Finanzas',
'Food & Beverage'=>'Restauración',
'Government'=>'Gobierno',
'Healthcare'=>'Salud',
'Hospitality'=>'Hospital',
'Insurance'=>'Seguros',
'Machinery'=>'Maquinaria',
'Manufacturing'=>'Fabricación',
'Media'=>'Medios',
'Not For Profit'=>'ONG',
'Recreation'=>'Ocio',
'Retail'=>'Venta al por menor',
'Shipping'=>'Logística',
'Technology'=>'Tecnología',
'Telecommunications'=>'Telecomunicaciones',
'Transportation'=>'Transportes',
'Utilities'=>'Utilidades',
'Other'=>'Otros',

'Cold Call'=>'Llamada',
'Existing Customer'=>'Cliente',
'Self Generated'=>'Autogenerada',
'Employee'=>'Trabajador',
'Partner'=>'Socio',
'Public Relations'=>'Relaciones Públicas',
'Direct Mail'=>'Mailing',
'Conference'=>'Conferencia',
'Trade Show'=>'Feria',
'Web Site'=>'Web Site',
'Word of mouth'=>'Boca a Boca',

'Attempted to Contact'=>'Intentado Contactar',
'Cold'=>'Frio',
'Contact in Future'=>'Contactar más adelante',
'Contacted'=>'Contactado',
'Hot'=>'Caliente',
'Junk Lead'=>'Pre-Contacto Basura',
'Lost Lead'=>'Pre-Contacto Fallido',
'Not Contacted'=>'No Contactado',
'Pre Qualified'=>'Pre Calificado',
'Qualified'=>'Calificado',
'Warm'=>'Tibio',

'Designation'=>'Designación',

//Module Sequence Numbering
'Lead No'=>'Núm. Pre-Contacto',

'LBL_TRANSFER_RELATED_RECORDS_TO' => 'Transferir elementos relacionados a',

'LBL_FOLLOWING_ARE_POSSIBLE_REASONS' => 'A continuación se detallan algunas de las posibles causas',
'LBL_LEADS_FIELD_MAPPING_INCOMPLETE' => 'No se han vinculado todos los campos obligatorios',
'LBL_MANDATORY_FIELDS_ARE_EMPTY' => 'Algún campo obligatorio está vacío',
'LBL_LEADS_FIELD_MAPPING' => 'Vinculación de Campos Personalizados',

'LBL_FIELD_SETTINGS' => 'Configuración Campos',
'Leads ID' => 'Id PreContacto',
'LeadAlreadyConverted' => 'Este PreContacto no se puede convertir. O bien ya ha sido convertido, o te faltan permisos en uno de los módulos dependientes.',
'Is Converted From Lead' => 'Convertido desde PreContacto',
'Converted From Lead' => 'Convertido del PreContacto',
);

?>
