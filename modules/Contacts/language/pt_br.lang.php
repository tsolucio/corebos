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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Contacts/language/en_us.lang.php,v 1.14 2005/03/24 17:47:43 rank Exp $
 * Description:  Defines the English language pack for Contacts Module
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): Valmir Carlos Trindade/Translate to Brazilian Portuguese| 03/03/2012 |Curitiba/Paraná/Brasil.|www.ttcasolucoes.com.br
 ********************************************************************************/

$mod_strings = Array(
// Mike Crowe Mod --------------------------------------------------------Added for general search
'LBL_MODULE_NAME'=>'Contatos',
'LBL_INVITEE'=>'Subordinado à',
'LBL_MODULE_TITLE'=>'Contatos: Principal',
'LBL_SEARCH_FORM_TITLE'=>'Pesquisar Contatos',
'LBL_LIST_FORM_TITLE'=>'Lista Contatos',
'LBL_NEW_FORM_TITLE'=>'Novo Contato',
'LBL_CONTACT_OPP_FORM_TITLE'=>'Contato-Oportunidade:',
'LBL_CONTACT'=>'Contato:',
      
'LBL_LIST_NAME'=>'Nome',
'LBL_LIST_LAST_NAME'=>'Sobrenome',
'LBL_LIST_FIRST_NAME'=>'Nome',
'LBL_LIST_CONTACT_NAME'=>'Nome Contato',
'LBL_LIST_TITLE'=>'Título',
'LBL_LIST_ACCOUNT_NAME'=>'Nome Organização',
'LBL_LIST_EMAIL_ADDRESS'=>'Email',
'LBL_LIST_PHONE'=>'Fone',
'LBL_LIST_CONTACT_ROLE'=>'Função',
      
//DON'T CONVERT THESE THEY ARE MAPPINGS
'db_last_name' => 'LBL_LIST_LAST_NAME',
'db_first_name' => 'LBL_LIST_FIRST_NAME',
'db_title' => 'LBL_LIST_TITLE',
'db_email1' => 'LBL_LIST_EMAIL_ADDRESS',
'db_email2' => 'LBL_LIST_EMAIL_ADDRESS',
//END DON'T CONVERT
      
'LBL_EXISTING_CONTACT' => 'Usou um Contato existente',
'LBL_CREATED_CONTACT' => 'Criou um novo Contato',
'LBL_EXISTING_ACCOUNT' => 'Usou uma vtiger_account existente',
'LBL_CREATED_ACCOUNT' => 'Criou uma nova vtiger_account',
'LBL_CREATED_CALL' => 'Criou uma nova Chamada',
'LBL_CREATED_MEETING' => 'Criou uma nova Reunião',
'LBL_ADDMORE_BUSINESSCARD' => 'Adicione outro Cartão Pessoal',
      
'LBL_BUSINESSCARD' => 'Cartão Pessoal',
      
'LBL_NAME'=>'Nome:',
'LBL_CONTACT_NAME'=>'Nome Contato:',
'LBL_CONTACT_INFORMATION'=>'Informação do Contato',
'LBL_CUSTOM_INFORMATION'=>'Informação Customizada',
'LBL_FIRST_NAME'=>'Nome:',
'LBL_OFFICE_PHONE'=>'Telefone Escritório:',
'LBL_ACCOUNT_NAME'=>'Nome Organização:',
'LBL_ANY_PHONE'=>'Outro Telefone:',
'LBL_PHONE'=>'Telefone:',
'LBL_LAST_NAME'=>'Sobrenome:',
'LBL_MOBILE_PHONE'=>'Celular:',
'LBL_HOME_PHONE'=>'Telefone Residencial:',
'LBL_LEAD_SOURCE'=>'Origem do Lead:',
'LBL_OTHER_PHONE'=>'Telefone Alternativo:',
'LBL_FAX_PHONE'=>'Fax:',
'LBL_TITLE'=>'Título:',
'LBL_DEPARTMENT'=>'Departamento:',
'LBL_BIRTHDATE'=>'Nascimento:',
'LBL_EMAIL_ADDRESS'=>'Email:',
'LBL_OTHER_EMAIL_ADDRESS'=>'Email Alternativo:',
'LBL_ANY_EMAIL'=>'Outro Email:',
'LBL_REPORTS_TO'=>'Reporta-se à:',
'LBL_ASSISTANT'=>'Assistente:',
'LBL_YAHOO_ID'=>'ID Yahoo!:',
'LBL_ASSISTANT_PHONE'=>'Telefone do Assistente:',
'LBL_DO_NOT_CALL'=>'Recusa Chamada:',
'LBL_EMAIL_OPT_OUT'=>'Recusa Email:',
'LBL_PRIMARY_ADDRESS'=>'Enderenço Principal:',
'LBL_ALTERNATE_ADDRESS'=>'Endereço Alternativo:',
'LBL_ANY_ADDRESS'=>'Outro Endereço:',
'LBL_CITY'=>'Cidade:',
'LBL_STATE'=>'Estado:',
'LBL_POSTAL_CODE'=>'CEP:',
'LBL_COUNTRY'=>'País:',
'LBL_DESCRIPTION_INFORMATION'=>'Descrição',
'LBL_IMAGE_INFORMATION'=>'Informação Imagem Contato:',
'LBL_ADDRESS_INFORMATION'=>'Informação Endereço',
'LBL_DESCRIPTION'=>'Descrição:',
'LBL_CONTACT_ROLE'=>'Função:',
'LBL_OPP_NAME'=>'Nome Oportunidade:',
'LBL_DUPLICATE'=>'Possível Duplicação de Contatos',
'MSG_DUPLICATE' => 'Criando este Contato pode ser que vtiger_potentialy crie um contato duplicado. Você pode selecionar um Contato na lista abaixo ou clicar sobre Criar Novo Contato para continuar criando um novo Contato com os dados previamente inseridos.',

'LNK_NEW_APPOINTMENT' => 'Novo Compromisso',
'LBL_ADD_BUSINESSCARD' => 'Adicionar Cartão Pessoal',
'NTC_DELETE_CONFIRMATION'=>'Você tem certeza que deseja deletar este registro?',
'NTC_REMOVE_CONFIRMATION'=>'Você ter certeza que deseja remover este Contato desde Case?',
'NTC_REMOVE_DIRECT_REPORT_CONFIRMATION'=>'Você tem certeza que deseja remover este registro como um vtiger_report?',
'ERR_DELETE_RECORD'=>"en_us Defina um número de registro para deletar o Contato.",
'NTC_COPY_PRIMARY_ADDRESS'=>'Copiar endereço Principal para endereço Alternativo',
'NTC_COPY_ALTERNATE_ADDRESS'=>'Copiar endereço Alternativo para endereço Principal',

'LBL_SELECT_CONTACT'=>'Selecionar Contato',
//Added for search heading
'LBL_GENERAL_INFORMATION'=>'Informação Geral',
      
      
      
//for v4 release added
'LBL_NEW_POTENTIAL'=>'Nova Oportunidade',
'LBL_POTENTIAL_TITLE'=>'Oportunidades',
      
'LBL_NEW_TASK'=>'Nova Tarefa',
'LBL_TASK_TITLE'=>'Tarefa',
'LBL_NEW_CALL'=>'Nova Chamada',
'LBL_CALL_TITLE'=>'Chamadas',
'LBL_NEW_MEETING'=>'Nova Reunião',
'LBL_MEETING_TITLE'=>'Reuniões',
'LBL_NEW_EMAIL'=>'Novo Email',
'LBL_EMAIL_TITLE'=>'Emails',
'LBL_NEW_NOTE'=>'Novo Documento',
'LBL_NOTE_TITLE'=>'Documentos',

// Added for 4GA
'LBL_TOOL_FORM_TITLE'=>'Ferramentas Contato',

'Salutation'=>'Saudação',
'First Name'=>'Nome',
'Office Phone'=>'Telefone Escritório',
'Last Name'=>'Sobrenome',
'Mobile'=>'Celular',
'Account Name'=>'Nome Organização',
'Home Phone'=>'Telefone Residencial',
'Lead Source'=>'Origem do Lead',
'Other Phone'=>'Telefone Alternativo',
'Title'=>'Cargo',
'Fax'=>'Fax',
'Department'=>'Departamento',
'Birthdate'=>'Aniversário',
'Email'=>'Email',
'Reports To'=>'Reporta-se à',
'Assistant'=>'Assistente',
'Yahoo Id'=>'ID Yahoo!',
'Assistant Phone'=>'Telefone Assistente',
'Do Not Call'=>'Recusa Chamada',
'Email Opt Out'=>'Recusa Email',
'Assigned To'=>'Responsável',
'Campaign Source'=>'Fonte Campanha',
'Reference' =>'Referência',
'Created Time'=>'Data Criação',
'Modified Time'=>'Data Modificação',
'Mailing Street'=>'Endereço Correspondência',
'Other Street'=>'Endereço Alternativo',
'Mailing City'=>'Cidade Correspondência',
'Mailing State'=>'Estado Correspondência',
'Mailing Zip'=>'CEP Correspondência',
'Mailing Country'=>'País Correspondência',
'Mailing Po Box'=>'Cx Postal Correspondência',
'Other Po Box'=>'Cx Postal Alternativo',
'Other City'=>'Cidade Alternativo',
'Other State'=>'Estado Alternativo',
'Other Zip'=>'CEP Alternativo',
'Other Country'=>'País Alternativo',
'Contact Image'=>'Imagem Contato',
'Description'=>'Descrição',

// Added vtiger_fields for Add Business Card
'LBL_NEW_CONTACT'=>'Novo Contato',
'LBL_NEW_ACCOUNT'=>'Nova Organização',
'LBL_NOTE_SUBJECT'=>'Assunto Documento:',
'LBL_NOTE'=>'Nota:',
'LBL_WEBSITE'=>'Website:',
'LBL_NEW_APPOINTMENT'=>'Novo Compromisso',
'LBL_SUBJECT'=>'Assunto:',
'LBL_START_DATE'=>'Data Inicial:',
'LBL_START_TIME'=>'Hora Inicial:',
      
//Added vtiger_field after 4_0_1
'Portal User'=>'Portal Usuário',
'LBL_CUSTOMER_PORTAL_INFORMATION'=>'Informação Portal Cliente',
'Support Start Date'=>'Data Início Suporte',
'Support End Date'=>'Data Término Suporte',
//Added for 4.2 Release -- CustomView
'Name'=>'Nome',
'LBL_ALL'=>'Todos',
'LBL_MAXIMUM_LIMIT_ERROR'=>'Desculpe, o arquivo transferido excedeu limite máximo do vtiger_filesize. Por favor, tente um arquivo menor que 800000 bytes',
'LBL_UPLOAD_ERROR'=>'Problemas na transferência do arquivo. Por favor tente novamente!',
'LBL_IMAGE_ERROR'=>'O referido arquivo não é do tipo imagem(.gif/.jpg/.png)',
'LBL_INVALID_IMAGE'=>'Arquivo inválido OU não possui dados',
      
//Added after 5Alpha5
'Notify Owner'=>'Notificar Proprietário',
      
//Added for Picklist Values
'--None--'=>'--Nada--',
      
'Mr.'=>'Sr.',
'Ms.'=>'Sra.',
'Mrs.'=>'Srta.',
'Dr.'=>'Dr.',
'Prof.'=>'Prof.',
      
'Cold Call'=>'Cold Call',
'Existing Customer'=>'Cliente Existente',
'Self Generated'=>'Auto Gerado',
'Employee'=>'Empregado',
'Partner'=>'Parceiro',
'Public Relations'=>'Relações Públicas',
'Direct Mail'=>'Mala Direta',
'Conference'=>'Conferência',
'Trade Show'=>'Feira Negócios',
'Web Site'=>'Website',
'Word of mouth'=>'Boca-boca',
'Other'=>'Outro',
'User List'=>'Lista Usuário',

//Added for 5.0.3
'Customer Portal Login Details'=>'Detalhes Login Portal Cliente',
'Dear'=>'Prezado',
'Your Customer Portal Login details are given below:'=>'Os detalhes do seu Login no Portal do Cliente são apresentados abaixo: ',
'User Id :'=>'Usuário:',
'Password :'=>'Senha:',
'Please Login Here'=>'Por gentileza, faça o Login aqui',
'Note :'=>'Nota :',
'We suggest you to change your password after logging in first time'=>'Sugerimos que você altere sua senha após o primeiro acesso.',
'Support Team'=>'Equipe Suporte',

'TITLE_AJAX_CSS_POPUP_CHAT'=>'Bate-papo Ajax Css-Popup',

// Added after 5.0.4 GA

// Module Sequence Numbering
'Contact Id' => 'Cód. Contato',
'Secondary Email'=>'Email Alternativo',
// END

'Contacts ID'=>'ID Contatos',
);

?>
