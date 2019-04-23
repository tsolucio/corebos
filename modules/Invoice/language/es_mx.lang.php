<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************
*  Module       : invoice
*  Language     : Español
*  Version      : 5.4.0
*  Created Date : 2007-03-30
*  Author       : Rafael Soler
*  Last change  : 2012-02-27
*  Author       : Joe Bordes  JPL TSolucio, S.L.
*  Author       : Francisco Hernandez Odin Consultores www.odin.mx
 ********************************************************************************/

$mod_strings = array(
'LBL_MODULE_NAME'=>'Facturas',
'SINGLE_Invoice'=>'Factura',
'LBL_SO_MODULE_NAME'=>'Factura',
'LBL_RELATED_PRODUCTS'=>'Elementos',
'LBL_MODULE_TITLE'=>'Factura: Inicio',
'LBL_SEARCH_FORM_TITLE'=>'Buscar Factura',
'LBL_LIST_FORM_TITLE'=>'Lista de Facturas',
'LBL_LIST_SO_FORM_TITLE'=>'Listado de Pedidos',
'LBL_NEW_FORM_TITLE'=>'Nueva Factura',
'LBL_NEW_FORM_SO_TITLE'=>'Nuevo Pedido',
'LBL_MEMBER_ORG_FORM_TITLE'=>'Organizaciones Miembro',

'LBL_LIST_ACCOUNT_NAME'=>'Nombre de la Cuenta',
'LBL_LIST_CITY'=>'Deleg./Mpio.',
'LBL_LIST_WEBSITE'=>'Página Web',
'LBL_LIST_STATE'=>'Estado',
'LBL_LIST_PHONE'=>'Teléfono',
'LBL_LIST_EMAIL_ADDRESS'=>'Direccion de Email',
'LBL_LIST_CONTACT_NAME'=>'Persona de Contacto',
'LBL_ACCOUNT'=>'Cuenta:',
'LBL_ACCOUNT_NAME'=>'Nombre de la cuenta:',
'LBL_PHONE'=>'Teléfono:',
'LBL_WEBSITE'=>'Página Web:',
'LBL_FAX'=>'Fax:',
'LBL_TICKER_SYMBOL'=>'Símbolo de bolsa:',
'LBL_OTHER_PHONE'=>'Tel. Directo:',
'LBL_ANY_PHONE'=>'Tel. Adicional:',
'LBL_MEMBER_OF'=>'Miembro de:',
'LBL_EMAIL'=>'Email:',
'LBL_EMPLOYEES'=>'Empleados:',
'LBL_OTHER_EMAIL_ADDRESS'=>'Email (Otro):',
'LBL_ANY_EMAIL'=>'Email (Alternativo):',
'LBL_OWNERSHIP'=>'Propietario:',
'LBL_RATING'=>'Valoración:',
'LBL_INDUSTRY'=>'Actividad:',
'LBL_SIC_CODE'=>'RFC:',
'LBL_TYPE'=>'Tipo:',
'LBL_ANNUAL_REVENUE'=>'Ingresos Anuales:',
'LBL_ADDRESS_INFORMATION'=>'Información de la Dirección',
'LBL_Invoice_FINANCIALINFO' => 'Información Financiera',
'LBL_Quote_INFORMATION'=>'Información de la Empresa',
'LBL_CUSTOM_INFORMATION'=>'Información personalizada',
'LBL_BILLING_ADDRESS'=>'Dirección (Factura):',
'LBL_SHIPPING_ADDRESS'=>'Dirección (Envío):',
'LBL_ANY_ADDRESS'=>'Dirección (Alternativa):',
'LBL_CITY'=>'Deleg./Mpio.:',
'LBL_STATE'=>'Estado:',
'LBL_POSTAL_CODE'=>'Código Postal:',
'LBL_COUNTRY'=>'País:',
'LBL_DESCRIPTION_INFORMATION'=>'Información Adicional',
'LBL_DESCRIPTION'=>'Descripción:',
'LBL_TERMS_INFORMATION'=>'Condiciones Generales',
'NTC_COPY_BILLING_ADDRESS'=>'Copiar Factura a Envío',
'NTC_COPY_SHIPPING_ADDRESS'=>'Copiar Envío a Factura',
'NTC_REMOVE_MEMBER_ORG_CONFIRMATION'=>'¿Esta seguro que desea eliminar este expediente como organización miembro?',
'LBL_DUPLICATE'=>'Posibles Cuentas Duplicadas',
'MSG_DUPLICATE' => 'Al dar de alta esta cuenta puede que se cree una cuenta duplicada. Puede seleccionar una cuenta del listado inferior o hacer click en Crear Nueva Cuenta para continuar creando la cuenta con los datos introducidos.',

'LBL_INVITEE'=>'Contactos',
'ERR_DELETE_RECORD'=>" Debe especificar un registro para poder suprimir la cuenta.",

'LBL_SELECT_ACCOUNT'=>'Seleccionar cuenta',
'LBL_GENERAL_INFORMATION'=>'Informació General',

'LBL_NEW_POTENTIAL'=>'Nueva Oportunidad',
'LBL_POTENTIAL_TITLE'=>'Oportunidades',

'LBL_NEW_TASK'=>'Nueva Tarea',
'LBL_TASK_TITLE'=>'Tareas',
'LBL_NEW_CALL'=>'Nueva Llamada',
'LBL_CALL_TITLE'=>'Llamadas',
'LBL_NEW_MEETING'=>'Nueva Reunión',
'LBL_MEETING_TITLE'=>'Reuniones',
'LBL_NEW_EMAIL'=>'Nuevo Email',
'LBL_EMAIL_TITLE'=>'Emails',
'LBL_NEW_CONTACT'=>'Nuevo Contacto',
'LBL_CONTACT_TITLE'=>'Contactos',

'LBL_ALL'=>'Todos',
'LBL_PROSPECT'=>'Prospecto',
'LBL_INVESTOR'=>'Inversionista',
'LBL_RESELLER'=>'Revendedor',
'LBL_PARTNER'=>'Socio',

'LBL_TOOL_FORM_TITLE'=>'Herramientas de Cuenta',
'Subject'=>'Referencia',
'Quote Name'=>'Nombre de la Cotización',
'Vendor Name'=>'Nombre del Proveedor',
'Invoice Terms'=>'Condiciones Generales de Facturación',
'Contact Name'=>'Persona de Contacto',
'Invoice Date'=>'Fecha de Factura',
'Sub Total'=>'Sub Total',
'S&H Amount' => 'Importe Envío y Manipulado',
'Due date'=>'Fecha de Vencimiento',
'Due Date'=>'Fecha de Vencimiento',
'Carrier'=>'Transportista',
'Type'=>'Tipo',
'Sales Tax'=>'Impuesto sobre Ventas',
'Sales Commission'=>'Comisión sobre Ventas',
'Excise Duty'=>'Impuestos',
'Total'=>'Total',
'Product Name'=>'Nombre del Producto',
'Assigned To'=>'Asignado a',
'Billing Address'=>'Dirección (Facturación)',
'Shipping Address'=>'Dirección (Envío)',
'Billing City'=>'Deleg./Mpio. (Facturación)',
'Billing State'=>'Estado (Facturación)',
'Billing Code'=>'Código (Facturación)',
'Billing Country'=>'País (Facturación)',
'Billing Po Box'=>'Colonia (Facturación)',
'Shipping Po Box'=>'Colonia (Envío)',
'Shipping City'=>'Deleg./Mpio. (Envío)',
'Shipping State'=>'Estado (Envío)',
'Shipping Code'=>'Código (Envío)',
'Shipping Country'=>'País (Envío)',
'City'=>'Deleg./Mpio.',
'State'=>'Estado',
'Code'=>'Código Postal',
'Country'=>'País',
'Created Time'=>'Fecha de creación',
'Modified Time'=>'Última Modificación',
'Description'=>'Descripción',
'Potential Name'=>'Nombre de la Oportunidad',
'Customer No'=>'Código de cliente',
'Sales Order'=>'Pedido',
'Pending'=>'Pendientes',
'Account Name'=>'Nombre de la cuenta',
//Quote Info
'LBL_INVOICE_INFORMATION'=>'Información de la Facturación',
'LBL_INVOICE'=>'Facturación:',
'LBL_SO_INFORMATION'=>'Información de Pedido',
'LBL_SO'=>'Pedido:',

'LBL_SUBJECT'=>'Referencia:',
'LBL_SALES_ORDER'=>'Pedido:',
'LBL_MY_TOP_INVOICE'=>'Mis Facturas pendientes',
'LBL_INVOICE_NAME'=>'Nombre de la Factura:',
'Purchase Order'=>'Orden de Compra',
'Status'=>'Estado',
'Id'=>'Número de Factura',
'Invoice'=>'Factura',

//Added for existing Picklist Entries

'Created'=>'Creada',
'Approved'=>'Aprobada',
'Sent'=>'Enviada',
'Credit Invoice'=>'a Crédito',
'Paid'=>'Pagada',
'AutoCreated'=>'Automática',
//Added to Custom Invoice Number
'Invoice No'=>'Nº Factura',
'Adjustment'=>'Ajuste',

'Tax Type'=>'Impuesto',
'Discount Percent'=>'Descuento %',
'Discount Amount'=>'Descuento Importe',
'Terms & Conditions'=>'Terminos y Condiciones',
'No'=>'No',
'Date'=>'Fecha',

//Added for Documents module
'Documents'=>'Documentos',
'Invoice ID'=>'Id Factura',
);
?>
