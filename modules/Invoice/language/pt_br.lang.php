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
/*********************************************************************************
 * $Header$
 * Description:  Defines the English language pack for Invoice module.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): Valmir Carlos Trindade/Translate to Brazilian Portuguese| 03/03/2012 |Curitiba/Paraná/Brasil.|www.ttcasolucoes.com.br
 ********************************************************************************/

$mod_strings = Array(
'LBL_MODULE_NAME'=>'Fatura',
'LBL_SO_MODULE_NAME'=>'Fatura',
'LBL_RELATED_PRODUCTS'=>'Detalhes do Produto',
'LBL_MODULE_TITLE'=>'Fatura: Principal',
'LBL_SEARCH_FORM_TITLE'=>'Pesquisar Fatura',
'LBL_LIST_FORM_TITLE'=>'Listar Fatura',
'LBL_LIST_SO_FORM_TITLE'=>'Listar Pedidos Vendas',
'LBL_NEW_FORM_TITLE'=>'Nova Fatura',
'LBL_NEW_FORM_SO_TITLE'=>'Novo Pedido Vendas',
'LBL_MEMBER_ORG_FORM_TITLE'=>'Organizações Membro',

'LBL_LIST_ACCOUNT_NAME'=>'Nome Organização',
'LBL_LIST_CITY'=>'Cidade',
'LBL_LIST_WEBSITE'=>'Website',
'LBL_LIST_STATE'=>'Estado',
'LBL_LIST_PHONE'=>'Fone',
'LBL_LIST_EMAIL_ADDRESS'=>'Endereço E-mail',
'LBL_LIST_CONTACT_NAME'=>'Nome Contato',

//DON'T CONVERT THESE THEY ARE MAPPINGS
'db_name' => 'LBL_LIST_ACCOUNT_NAME',
'db_website' => 'LBL_LIST_WEBSITE',
'db_billing_address_city' => 'LBL_LIST_CITY',

//END DON'T CONVERT

'LBL_ACCOUNT'=>'Organização:',
'LBL_ACCOUNT_NAME'=>'Nome Organização:',
'LBL_PHONE'=>'Fone:',
'LBL_WEBSITE'=>'Website:',
'LBL_FAX'=>'Fax:',
'LBL_TICKER_SYMBOL'=>'Cod. Bolsa:',
'LBL_OTHER_PHONE'=>'Fone Alternativo:',
'LBL_ANY_PHONE'=>'Outro Fone:',
'LBL_MEMBER_OF'=>'Membro de:',
'LBL_EMAIL'=>'E-mail:',
'LBL_EMPLOYEES'=>'Empregados:',
'LBL_OTHER_EMAIL_ADDRESS'=>'E-mail Alternativo:',
'LBL_ANY_EMAIL'=>'Outro E-mail:',
'LBL_OWNERSHIP'=>'Propriedade:',
'LBL_RATING'=>'Avaliação:',
'LBL_INDUSTRY'=>'Atividade:',
'LBL_SIC_CODE'=>'Cod CNAE:',
'LBL_TYPE'=>'Tipo:',
'LBL_ANNUAL_REVENUE'=>'Receita Anual:',
'LBL_ADDRESS_INFORMATION'=>'Dados do Endereço',
'LBL_Quote_INFORMATION'=>'Dados da Organização',
'LBL_CUSTOM_INFORMATION'=>'Informação Customizada',
'LBL_BILLING_ADDRESS'=>'Endereço Faturamento:',
'LBL_SHIPPING_ADDRESS'=>'Endereço Entrega:',
'LBL_ANY_ADDRESS'=>'Outro Endereço:',
'LBL_CITY'=>'Cidade:',
'LBL_STATE'=>'Estado:',
'LBL_POSTAL_CODE'=>'CEP:',
'LBL_COUNTRY'=>'País:',
'LBL_DESCRIPTION_INFORMATION'=>'Descrição',
'LBL_DESCRIPTION'=>'Descrição:',
'LBL_TERMS_INFORMATION'=>'Termos & Condições',
'NTC_COPY_BILLING_ADDRESS'=>'Copiar endereço Faturamento para endereço de Entrega',
'NTC_COPY_SHIPPING_ADDRESS'=>'Copiar endereço de Entrega para endereço de Faturamento',
'NTC_REMOVE_MEMBER_ORG_CONFIRMATION'=>'Tem certeza que deseja remover este registro como um membro da organização?',
'LBL_DUPLICATE'=>'Possibilidade Duplicação de Organizações',
'MSG_DUPLICATE' => 'Criando esta Organização pode ser que a mesma seja duplicada. Você pode selecionar também uma Organização da lista abaixo ou clicar sobre Criar Nova Organização para continuar criando uma nova Organização com os dados inseridos anteriormente.',

'LBL_INVITEE'=>'Contatos',
'ERR_DELETE_RECORD'=>"Defina um número de registro para deletar a Organização.",

'LBL_SELECT_ACCOUNT'=>'Selecione a Organização',
'LBL_GENERAL_INFORMATION'=>'Informação Geral',

//for v4 release added
'LBL_NEW_POTENTIAL'=>'Nova Oportunidade',
'LBL_POTENTIAL_TITLE' => 'Oportunidades',

'LBL_NEW_TASK'=>'Nova Tarefa',
'LBL_TASK_TITLE'=>'Tarefas',
'LBL_NEW_CALL'=>'Nova Chamada',
'LBL_CALL_TITLE'=>'Chamadas',
'LBL_NEW_MEETING'=>'Nova Reunião',
'LBL_MEETING_TITLE'=>'Reuniões',
'LBL_NEW_EMAIL'=>'Novo E-mail',
'LBL_EMAIL_TITLE'=>'E-mails',
'LBL_NEW_CONTACT'=>'Novo Contato',
'LBL_CONTACT_TITLE'=>'Contatos',

//Added vtiger_fields after RC1 - Release
'LBL_ALL'=>'Todos',
'LBL_PROSPECT'=>'Prospect',
'LBL_INVESTOR'=>'Investidor',
'LBL_RESELLER'=>'Revendedor',
'LBL_PARTNER'=>'Parceiro',

// Added for 4GA
'LBL_TOOL_FORM_TITLE'=>'Ferramentas Organização',
//Added for 4GA
'Subject'=>'Assunto',
'Quote Name'=>'Nome Cotação',
'Vendor Name'=>'Nome Fornecedor',
'Invoice Terms'=>'Condições Fatura',
'Contact Name'=>'Nome Contato', //to include contact name vtiger_field in Invoice
'Invoice Date'=>'Data Fatura',
'Sub Total'=>'Sub-Total',
'Due Date'=>'Data Vencimento',
'Carrier'=>'Transportador',
'Type'=>'Tipo',
'Sales Tax'=>'ICMS',
'Sales Commission'=>'Comissão Vendas',
'Excise Duty'=>'IPI',
'Total'=>'Total',
'Product Name'=>'Nome Produto',
'Assigned To'=>'Responsável',
'Billing Address'=>'Endereço Faturamento',
'Shipping Address'=>'Endereço Entrega',
'Billing City'=>'Cidade Faturamento',
'Billing State'=>'Estado Faturamento',
'Billing Code'=>'CEP Faturamento',
'Billing Country'=>'País Faturamento',
'Billing Po Box'=>'Cx Postal Faturamento',
'Shipping Po Box'=>'Cx Postal Entrega',
'Shipping City'=>'Cidade Entrega',
'Shipping State'=>'Estado Entrega',
'Shipping Code'=>'CEP Entrega',
'Shipping Country'=>'País Entrega',
'City'=>'Cidade',
'State'=>'Estado',
'Code'=>'CEP',
'Country'=>'País',
'Created Time'=>'Data Criação',
'Modified Time'=>'Data Modificação',
'Description'=>'Descrição',
'Potential Name'=>'Nome Oportunidade',
'Customer No'=>'Cod. Cliente',
'Sales Order'=>'Pedido Vendas',
'Pending'=>'Pendente',
'Account Name'=>'Nome Organização',
'Terms & Conditions'=>'Prazos & Condições',
//Quote Info
'LBL_INVOICE_INFORMATION'=>'Informação Fatura',
'LBL_INVOICE'=>'Fatura:',
'LBL_SO_INFORMATION'=>'Dados do Pedido de Vendas',
'LBL_SO'=>'Pedido Vendas:',

//Added in release 4.2
'LBL_SUBJECT'=>'Assunto:',
'LBL_SALES_ORDER'=>'Pedido Vendas:',
'Invoice Id'=>'No. Fatura',
'LBL_MY_TOP_INVOICE'=>'Principais Faturas Abertas',
'LBL_INVOICE_NAME'=>'Nome Fatura:',
'Purchase Order'=>'Pedido Compra:',
'Status'=>'Status',
'Id'=>'No. Fatura',
'Invoice'=>'Fatura',

//Added for existing Picklist Entries

'Created'=>'Criada',
'Approved'=>'Aprovada',
'Sent'=>'Enviada',
'Credit Invoice'=>'Creditar Fatura',
'Paid'=>'Paga',
'AutoCreated'=>'Auto Criado',
//Added to Custom Invoice Number
'Invoice No'=>'No. Fatura',
'Adjustment'=>'Ajuste',

//Added for Reports (5.0.4)
'Tax Type'=>'Tipo Imposto',
'Discount Percent'=>'Percentual Desconto',
'Discount Amount'=>'Total Desconto',
'No'=>'Não',
'Date'=>'Data',

// Added affter 5.0.4 GA
//Added for Documents module
'Documents'=>'Documentos',

'SINGLE_Invoice'=>'Fatura',
'Invoice ID'=>'ID Fatura',
);

?>
