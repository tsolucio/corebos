<?php
/********************************************************************************* 
 * The contents of this file are subject to the SugarCRM Public License  
Version 1.1.2 
 * ("License"); You may not use this file except in compliance with the 
 * License. You may obtain a copy of the License at  
http://www.sugarcrm.com/SPL 
 * Software distributed under the License is distributed on an  "AS IS"   
basis, 
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the  
License for 
 * the specific language governing rights and limitations under the License. 
 * The Original Code is:  SugarCRM Open Source 
 * The Initial Developer of the Original Code is SugarCRM, Inc. 
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.; 
 * All Rights Reserved. 
********************************************************************************
*  Module       : CustomView
*  Language     : Español
*  Version      : 504
*  Created Date : 2007-03-30
*  Author       : Rafael Soler
*  Last change  : 2008-09-20
*  Author       : Joe Bordes JPL TSolucio, S.L.
*  Author       : Francisco Hernandez Odin Consultores www.odin.mx
 ********************************************************************************/ 
$mod_strings = array (
'LBL_MODULE_NAME'=>'Vista personalizada',
'LBL_STEP_1_TITLE'=>'Ver la Información',
'LBL_VIEW_NAME'=>'Nombre de la Vista:',
'LBL_SETDEFAULT'=>'Fijar por defecto',
'LBL_LIST_IN_METRICS'=>'Listar en métricas',
'LBL_STEP_2_TITLE'=>'Elegir columnas',
'LBL_STEP_3_TITLE'=>'Filtros Estandar',
'LBL_STEP_4_TITLE'=>'Filtros Avanzados',
'LBL_STEP_5_TITLE'=>'Información de Acceso',
'LBL_SF_COLUMNS'=>'Columna',
'LBL_SF_STARTDATE'=>'Fecha de Inicio',
'LBL_SF_ENDDATE'=>'Vencimiento',
'LBL_AF_HDR1'=>'Fije las condiciones de la búsqueda para restringir la lista.',
'LBL_AF_HDR2'=>'Puede utilizar filtros &quot;or&quot; introduciendo varios elementos en el tercer campo.',
'LBL_AF_HDR3'=>'Puede incorporar hasta 10 artículos, separados por comas.',
'LBL_AF_HDR4'=>'Si "Evento" está selecionado, dele un valor de los siguientes "Llamada", "Reunión" o "Tarea"',

//strings added for vtiger 5, date format... 
'LBL_NONE'=>'Ninguno',
'View_Name'=>'Ver_Nombre',
'LBL_AND'=>'y',
'LBL_DATE_FORMAT_CUSTOMVIEW'=>'A-m-d',
//Strings added for filter 
'Custom'=>'Personalizado',
'Previous FY'=>'Año Anterior',
'Current FY'=>'Año Actual',
'Next FY'=>'Año Siguiente',
'Previous FQ'=>'Cuatrimestre Anterior',
'Current FQ'=>'Cuatrimestre Actual',
'Next FQ'=>'Cuatrimestre Siguiente',
'Yesterday'=>'Ayer',
'Today'=>'Hoy',
'Tomorrow'=>'Mañana',
'Last Week'=>'Semana Anterior',
'Current Week'=>'Semana Actual',
'Next Week'=>'Semana Siguiente',
'Last Month'=>'Último Mes',
'Current Month'=>'Mes Actual',
'Next Month'=>'Mes Siguiente',
'Last 7 Days'=>'Últimos 7 Días',
'Last 30 Days'=>'Últimos 30 Días',
'Last 60 Days'=>'Últimos 60 Días',
'Last 90 Days'=>'Últimos 90 Días',
'Last 120 Days'=>'Últimos 120 Días',
'Next 30 Days'=>'Siguientes 30 Días',
'Next 60 Days'=>'Siguientes 60 Días',
'Next 90 Days'=>'Siguientes 90 Días',
'Next 120 Days'=>'Siguientes 120 Días',

'equals'=>'iguales',
'contains'=>'Contiene',
'does not contain'=>'No contiene',
'less than'=>'menor que',
'greater than'=>'mayor que',
'less or equal'=>'menor o igual',
'greater or equal'=>'mayor o igual',
 
//Strings added to translate field label vtiger_groups 
'Address'=>'Dirección',
'Information'=>'Información',
'Description'=>'Descripción',
'Custom Information'=>'Información Personalizada',
'- Event Information'=>'- Información de Evento',
'- Event Description'=>'- Descripción de Evento',
'- Task Information'=>'- Información de Tarea',
'- Task Description'=>'- Descripción de Tarea',
 
//Strings added for helpdesk module fields 
'Title'=>'Asunto',
'Assigned To'=>'Asignado A',
'Related to'=>'Relacionado con',
'Priority'=>'Prioridad',
'Product Name'=>'Producto',
'Severity'=>'Importancia',
'Status'=>'Estado',
'Category'=>'Categoria',
'Created Time'=>'Creado',
'Modified Time'=>'Modificado',
'Attachment'=>'Adjunto',
 
//Strings added for Leads module fields 
'First Name'=>'Nombre',
'Phone'=>'Teléfono',
'Last Name'=>'Apellidos',
'Company'=>'Cuenta',
'Lead Source'=>'Origen de Prospecto',
'Website'=>'Página Web',
'Industry'=>'Industria',
'Lead Status'=>'Estado de Prospecto',
'Annual Revenue'=>'Facturación Anual',
'Rating'=>'Importancia',
'No Of Employees'=>'Número de Empleados',
'Street'=>'Dirección',
'Po Box'=>'Colonia',
'Postal Code'=>'Código Postal',
'City'=>'Deleg./Mpio.',
'Country'=>'País',
'State'=>'Estado',
 
//Strings added for Accounts module fields 
'Account Name'=>'Cuenta',
'Ticker Symbol'=>'Símbolo de bolsa',
'Other Phone'=>'Tel. Directo',
'Member Of'=>'Miembro de',
'Employees'=>'Empleados',
'Other Email'=>'Email (Otro)',
'Ownership'=>'Propietario',
'industry'=>'Actividad',
'SIC Code'=>'RFC',
'Email Opt Out'=>'No Enviar Email',
'Billing Address'=>'Dirección (Factura)',
'Shipping Address'=>'Dirección (Envío)',
'Shipping Po Box'=>'Colonia (Envío)',
'Billing Po Box'=>'Colonia (Factura)',
'Billing City'=>'Deleg./Mpio.  (Factura)',
'Shipping City'=>'Deleg./Mpio. (Envío)',
'Billing State'=>'Estado (Factura)',
'Shipping State'=>'Estado (Envío)',
'Billing Code'=>'Código Postal (Factura)',
'Shipping Code'=>'Código Postal (Envío)',
'Shipping Country'=>'País (Envío)',
'Billing Country'=>'País (Factura)',
 
 
//Strings added for Contacts module fields 
 
'Office Phone'=>'Tel. Oficina',
'Home Phone'=>'Tel. Particular',
'Birthdate'=>'Cumpleaños',
'Reports To'=>'Informa a',
'Assistant Phone'=>'Teléfono de la Secretaria',
'Do Not Call'=>'No Llamar',
'Mailing Street'=>'Dirección (Envío)',
'Other Street'=>'Dirección (Otra)',
'Mailing Po Box'=>'Apdo. Postal (Envío)',
'Other Po Box'=>'Apdo. Postal (Otra)',
'Mailing City'=>'Deleg./Mpio. (Envío)',
'Other City'=>'Deleg./Mpio. (Otra)',
'Mailing State'=>'Estado (Envío)',
'Other State'=>'Estado (Otra)',
'Mailing Zip'=>'Código Postal (Envío)',
'Other Zip'=>'Código Postal (Otra)',
'Mailing Country'=>'País (Envío)',
'Other Country'=>'País (Otra)',
 
 
//Strings added for Potential module fields 
 
'Potential Name'=>'Nombre de Oportunidad',
'Amount'=>'Importe',
'Expected Close Date'=>'Fecha Estimada de Cierre',
'Next Step'=>'Siguiente Paso',
'Sales Stage'=>'Etapa de Venta',
'Probability'=>'Probabilidad',
 
 
//Strings added for Quotes module fields 
'Subject'=>'Asunto',
'Quote Stage'=>'Etapa de Cotización',
'Valid Till'=>'Validez',
'Team'=>'Equipo',
'Contact Name'=>'Persona de Contacto',
'Carrier'=>'Transportista',
'Shipping'=>'Tipo de Envío',
'Inventory Manager'=>'Responsable de Inventario',
 
//Strings added for Sales Orders module fields 
'Customer No'=>'Nº de Cliente',
'Quote Name'=>'Nombre de la Cotización',
'Purchase Order'=>'Orden de Compra',
'Due Date'=>'Fecha de Entrega',
'Pending'=>'Pendiente',
'Sales Commission'=>'Comisión de Venta',
'Excise Duty'=>'Impuestos',
 
//Strings added for Invoices module fields 
'Sales Order'=>'Pedido',
'Invoice Date'=>'Fecha de Factura',
 
//Strings added for Product module fields 
'Product Active'=>'Producto Activo',
'Product Category'=>'Categoría de Producto',
'Sales Start Date'=>'Inicio de Comercialización',
'Sales End Date'=>'Fin de Comercialización',
'Support Start Date'=>'Inicio de Soporte',
'Support Expiry Date'=>'Fin de Soporte',
'Vendor Name'=>'Proveedor',
'Mfr PartNo'=>'Nº de Parte del Fabricante',
'Vendor PartNo'=>'Nº de Parte del Proveedor',
 
'Serial No'=>'Nº Serie',
'Product Sheet'=>'Hoja de Producto',
'GL Account'=>'Cuenta Contable',
 
//Strings added for Price book module fields 
'Price Book Name'=>'Listas de precios',
'Active'=>'Activo',
 
//Strings added for tasks & events module fields 
'Start Date & Time'=>'Fecha de Inicio',
 
//error message 
'Missing required fields'=>'Faltan Campos Obligatorios',
//Strings added for campaigns 
'Campaign Name'=>'Nombre de Campaña',
'Campaign Type'=>'Tipo de Campaña',
'Product'=>'Producto',
'Campaign Status'=>'Estado de la Campaña',
'Expected Revenue'=>'Beneficio Esperado',
'Budget Cost'=>'Costo Presupuestado',
'Actual Cost'=>'Costo Actual',
'Expected Response'=>'Respuesta Estimada',
'Num Sent'=>'Nº Envío',
'Target Audience'=>'Público Objetivo',
'TargetSize'=>'Tamaño Objetivo',
'Sponsor'=>'Patrocinador',
'Expected Sales Count'=>'Cuenta de Ventas Estimada',
'Expected Response Count'=>'Cuenta de Respuesta Estimada',
'Expected ROI'=>'Expectativas de ROI',
'Actual Sales Count'=>'Ventas Reales',
'Actual Response Count'=>'Respuesta Real',
'Actual ROI'=>'ROI Real',
 
 
 
//Added for customview.tpl 
 
'LBL_Select_a_Column'=>'Seleccione una Columna',
'Missing_required_fields'=>'Faltan Campos Obligatorios',
'Details'=>'Detalles',
'New_Custom_View'=>'Nueva Vista Personalizada',
'Edit_Custom_View'=>'Editar Vista Personalizada', 
'LBL_AF_HDR5'=>'El Filtro de Tiempo permite seleccionar una fecha basándose en la fecha de <b>Creación</b> o',

'Select_Duration'=>'Seleccionar Duración',
'Simple_Time_Filter'=>'Filtro de Tiempo Sencillo',
'Start_Date'=>'Inicio',
'End_Date'=>'Vencimiento',
'LBL_RULE'=>'REGLA',

// Added/Updated for vtiger CRM 5.0.4
'not equal to'=>'no igual a',
'starts with'=>'Empieza con',
'ends with'=>'Termina en',
//'Product Code'=>'Código de Producto',

// Added after 5.0.4 GA

//Added for Role based Custom filters 
'LBL_SET_AS_PUBLIC'=>'Hacer Público ',
'LBL_NEW'=>'Nuevo',
'LBL_EDIT'=>'Editar',
'LBL_STATUS_PUBLIC_APPROVE'=>'Aprobar',
'LBL_STATUS_PUBLIC_DENY'=>'Denegar',

'LBL_ADVANCED_FILTER' => 'Regla',
); 
?>
