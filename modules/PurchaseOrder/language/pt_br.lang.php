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
 * Description:  Defines the English language pack for Puchase Order
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): Valmir Carlos Trindade/Translate to Brazilian Portuguese| 03/03/2012 |Curitiba/Paraná/Brasil.|www.ttcasolucoes.com.br
 ********************************************************************************/

$mod_strings = Array(
'LBL_MODULE_NAME'=>'Pedido de Compra',
'LBL_RELATED_PRODUCTS'=>'Detalhes do Produto',
'LBL_MODULE_TITLE'=>'Pedido de Compra: Principal',
'LBL_SEARCH_FORM_TITLE'=>'Pesquisar Pedido de Compra',
'LBL_LIST_FORM_TITLE'=>'Listar Pedido de Compra',
'LBL_NEW_FORM_TITLE'=>'Novo Pedido de Compra',
'LBL_MEMBER_ORG_FORM_TITLE'=>'Organizações Membro',

'LBL_LIST_ACCOUNT_NAME'=>'Nome da Organização',
'LBL_LIST_CITY'=>'Cidade',
'LBL_LIST_WEBSITE'=>'Website',
'LBL_LIST_STATE'=>'Estado',
'LBL_LIST_PHONE'=>'Fone',
'LBL_LIST_EMAIL_ADDRESS'=>'E-mail',
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
'LBL_SIC_CODE'=>'Cod. CNAE:',
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
'LBL_TERMS_INFORMATION'=>'Prazos & Condições',
'LBL_DESCRIPTION'=>'Descrição:',
'NTC_COPY_BILLING_ADDRESS'=>'Copiar endereço de Cobrança para endereço de Entrega',
'NTC_COPY_SHIPPING_ADDRESS'=>'Copiar endereço de Entrega para endereço de Cobrança',
'NTC_REMOVE_MEMBER_ORG_CONFIRMATION'=>'Você tem certeza que deseja remover este registro como um membro da organização?',
'LBL_DUPLICATE'=>'Possibilidade Duplicação de Organizações',
'MSG_DUPLICATE' => 'Criando esta Organização poderá duplicá-la. Você pode selecionar também uma Organização da lista abaixo ou clicar sobre Criar Nova Organização para continuar criando uma nova Organização com os dados inseridos anteriormente.',

'LBL_INVITEE'=>'Contatos',
'ERR_DELETE_RECORD'=>"Um registro deve ser especificado para deletar uma vtiger_account.",

'LBL_SELECT_ACCOUNT'=>'Selecione a Organização',
'LBL_GENERAL_INFORMATION'=>'Informação Geral',

//for v4 release added
'LBL_NEW_POTENTIAL'=>'Nova Oportunidade',
'LBL_POTENTIAL_TITLE'=>'Oportunidades',

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
'Requisition No'=>'No. Requisição',
'Tracking Number'=>'No. Rastreamento',
'Contact Name'=>'Nome Contato',
'Due Date'=>'Data Vencimento',
'Carrier'=>'Transportadora',
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
'Shipping Po Box'=>'Cx Postal',
'Shipping City'=>'Cidade Entrega',
'Shipping State'=>'Estado Entrega',
'Shipping Code'=>'CEP Entrega',
'Shipping Country'=>'País Entrega',
'City'=>'Cidade',
'State'=>'Estado',
'Code'=>'CEP',
'Country'=>'País',
'Created Time'=>'Hora Criação',
'Modified Time'=>'Hora Modificação',
'Description'=>'Descrição',
'Potential Name'=>'Nome Oportunidade',
'Customer No'=>'Cód. Cliente',
'Purchase Order'=>'Pedido Compra',
'Vendor Terms'=>'Condições Pagamento Fornecedor',
'Pending'=>'Pendente',
'Account Name'=>'Nome Organização',
'Terms & Conditions'=>'Prazos & Condições',
//Quote Info
'LBL_PO_INFORMATION'=>'Informação Pedido Compra',
'LBL_PO'=>'Pedido Compra:',

 //Added for 4.2 GA
'LBL_SO_FORM_TITLE'=>'Vendas',
'LBL_PO_FORM_TITLE'=>'Compras',
'LBL_SUBJECT_TITLE'=>'Assunto',
'LBL_VENDOR_NAME_TITLE'=>'Fornecedor',
'LBL_TRACKING_NO_TITLE'=>'No. Rastreamento:',
'LBL_PO_SEARCH_TITLE'=>'Pesquisa Pedido Compra',
'LBL_SO_SEARCH_TITLE'=>'Pesquisa Pedido Vendas',
'LBL_QUOTE_NAME_TITLE'=>'Nome Cotação',
'Order No'=>'No. Pedido',
'Status'=>'Status',
'PurchaseOrder'=>'Pedido Compra',
'LBL_MY_TOP_PO'=>'Principais Pedidos Compra Abertos',

//Added for existing Picklist Entries

'FedEx'=>'FedEx',
'UPS'=>'Correio',
'USPS'=>'Varilog',
'DHL'=>'DHL',
'BlueDart'=>'BrasPress',

'Created'=>'Criado',
'Approved'=>'Aprovado',
'Delivered'=>'Entregue',
'Cancelled'=>'Cancelado',
'Received Shipment'=>'Entrega Recebida',

//Added for Reports (5.0.4)
'Tax Type'=>'Tipo Imposto',
'Discount Percent'=>'Percentual Desconto',
'Discount Amount'=>'Total Desconto',
'Adjustment'=>'Ajuste',
'Sub Total'=>'Sub-Total',
'S&H Amount'=>'Total Frete',

//Added after 5.0.4 GA
'PurchaseOrder No'=>'No. Pedido Compra',

'SINGLE_PurchaseOrder'=>'Pedido Compra',
'PurchaseOrder ID'=>'ID Pedido Compra',
);

?>
