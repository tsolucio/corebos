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
 * Description:  Defines the English language pack for the Account module.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): Valmir Carlos Trindade/Translate to Brazilian Portuguese|09/11/2011|Curitiba/Paraná/Brasil.|www.ttcasolucoes.com.br
 ********************************************************************************/


$mod_strings = Array(
'LBL_IMPORT_MODULE_NO_DIRECTORY'=>'O diretório ',
'LBL_IMPORT_MODULE_NO_DIRECTORY_END'=>' não existe ou não está disponível para escrita',
'LBL_IMPORT_MODULE_ERROR_NO_UPLOAD'=>'O arquivo não foi transferido com sucesso, tente novamente',
'LBL_IMPORT_MODULE_ERROR_LARGE_FILE'=>'O arquivo é muito grande. Max:',
'LBL_IMPORT_MODULE_ERROR_LARGE_FILE_END'=>'Bytes. Mude $upload_maxsize no config.php',
'LBL_MODULE_NAME'=>'Importar',
'LBL_TRY_AGAIN'=>'Tente Novamente',
'LBL_ERROR'=>'Erro:',
'ERR_MULTIPLE'=>'Várias colunas foram definidas com o mesmo nome de campo.',
'ERR_MISSING_REQUIRED_FIELDS'=>'Campos requeridos ausentes:',
'ERR_SELECT_FULL_NAME'=>'Você não pode selecionar o Nome Completo quanto o Nome e Sobrenome já tenha sido selecionados.',
'ERR_SELECT_FILE'=>'Selecione um arquivo para transferência.',
'LBL_SELECT_FILE'=>'Selecione arquivo:',
'LBL_CUSTOM'=>'Customizar',
'LBL_DONT_MAP'=>'-- Não mapear este campo --',
'LBL_STEP_1_TITLE'=>'Selecionar o arquivo .CSV',
'LBL_WHAT_IS'=>'Por favor, selecione uma fonte de dados a partir das seguintes:',
'LBL_MICROSOFT_OUTLOOK'=>'Microsoft Outlook',
'LBL_ACT'=>'Act!',
'LBL_SALESFORCE'=>'Salesforce.com',
'LBL_MY_SAVED'=>'Minhas Fontes Salvas:',
'LBL_PUBLISH'=>'publicar',
'LBL_DELETE'=>'deletar',
'LBL_PUBLISHED_SOURCES'=>'Fontes Publicadas:',
'LBL_UNPUBLISH'=>'não publicar',
'LBL_NEXT'=>'Próxima',
'LBL_BACK'=>'Voltar',
'LBL_STEP_2_TITLE'=>'Passo 2 de 4: Carregar Arquivo',
'LBL_HAS_HEADER'=>'Tem Cabeçalho:',

'LBL_NUM_1'=>'1.',
'LBL_NUM_2'=>'2.',
'LBL_NUM_3'=>'3.',
'LBL_NUM_4'=>'4.',
'LBL_NUM_5'=>'5.',
'LBL_NUM_6'=>'6.',
'LBL_NUM_7'=>'7.',
'LBL_NUM_8'=>'8.',
'LBL_NUM_9'=>'9.',
'LBL_NUM_10'=>'10.',
'LBL_NUM_11'=>'11.',
'LBL_NUM_12'=>'12.',
'LBL_NOW_CHOOSE'=>'Agora escolha o arquivo a ser importado:',
'LBL_IMPORT_OUTLOOK_TITLE'=>'Microsoft Outlook 98 e 2000 podem exportar dados em formato <b>CSV - Valores Separados por Vígula</b> o qual pode ser usado para importar dados para o Sistema. Para exportar seus dados do Outlook, siga os passos abaixo:',
'LBL_OUTLOOK_NUM_1'=>'Inicie o <b>Outlook</b>',
'LBL_OUTLOOK_NUM_2'=>'Selecione o menu <b>Arquivo</b>, depois a opção <b>Importar e Exportar ...</b>',
'LBL_OUTLOOK_NUM_3'=>'Escolha <b>Exportar para um arquivo</b> e click Próximo',
'LBL_OUTLOOK_NUM_4'=>'Escolha <b>Valores Separados por Vígula(Windows)</b> e clique <b>Próximo</b>.<br>  Nota: Você pode ser solicitado a instalar o componente exportação',
'LBL_OUTLOOK_NUM_5'=>'Selecione a pasta <b>Contatos</b> e clique <b>Próximo</b>. Você pode selecionar diferentes pastas de contato se seus contatos estão arquivados em múltiplas pastas',
'LBL_OUTLOOK_NUM_6'=>'Escolha um nome de arquivo e clique <b>Próximo</b>',
'LBL_OUTLOOK_NUM_7'=>'Clique <b>Finalizar</b>',
'LBL_IMPORT_ACT_TITLE'=>'O Act! pode exportar dados no formato <b>CSV - Valores Separados por Vírgula [Comma Separated Values]</b> o qual pode ser utilizado para importar dados para o Sistema. Para exportar seus dados do Act!, siga os passos abaixo:',
'LBL_ACT_NUM_1'=>'Abra o <b>ACT!</b>',
'LBL_ACT_NUM_2'=>'Selecione o menu <b>Arquivo/File</b>, a opção <b>Troca de Dados/Data Exchange</b>, depois a opção <b>Exportar/Export...</b>',
'LBL_ACT_NUM_3'=>'Selecione o tipo de arquivo <b>Text-Delimited</b>',
'LBL_ACT_NUM_4'=>'Escolha um nome de arquivo e local para exportar os dados e clique <b>Próximo</b>',
'LBL_ACT_NUM_5'=>'Selecione <b>Somente Registro de Contatos</b>',
'LBL_ACT_NUM_6'=>'Clique no botão <b>Opções/Options...</b>',
'LBL_ACT_NUM_7'=>'Selecione <b>Vírgula/Comma</b> como caracter separador de campo',
'LBL_ACT_NUM_8'=>'Verifique checkbox <b>Sim, exportar nomes de campos/Yes, export field names</b> e click <b>OK</b>',
'LBL_ACT_NUM_9'=>'Clique <b>Próximo/Next</b>',
'LBL_ACT_NUM_10'=>'Selecione <b>Todos os Registro/All Records</b> e Click <b>Finalizar/Finish</b>',

'LBL_IMPORT_SF_TITLE'=>'Salesforce.com pode exportar dados no formato <b>CSV - Comma Separated Values</b> o qual pode ser usado para importar dados para o Sistema. Para exportar seus dados para o Salesforce.com, siga os passos abaixo:',
'LBL_SF_NUM_1'=>'Abra seu browser, vá para http://www.salesforce.com, e faça login com seu endereço de email e senha',
'LBL_SF_NUM_2'=>'Clique na aba <b>Relatórios/Reports</b> no menu superior',
'LBL_SF_NUM_3'=>'Para exportar Contas:</b> Clique sobre <b>Ativar Contas/Active Accounts</b> link<br><b>Para exportar Contatos/To export Contacts:</b> Click sobre o link <b>Mailing List</b>',
'LBL_SF_NUM_4'=>'No <b>Passo 1: Selecione seu tipo de relatório</b>, selecione <b>Tabular Report</b>clique <b>Próximo/Next</b>',
'LBL_SF_NUM_5'=>'No <b>Passo 2: Selecione as colunas do relatório</b>, escolha as colunas que você quer exportar e click <b>Próximo/Next</b>',
'LBL_SF_NUM_6'=>'No <b>Passo 3: Selecione a informação a ser resumida</b>, clicando <b>Próximo/Next</b>',
'LBL_SF_NUM_7'=>'No <b>Passo 4: Ordene as colunas do relatório</b>, clicando <b>Próximo/Next</b>',
'LBL_SF_NUM_8'=>'No <b>Passo 5: Selecione o critério de relatório</b>, sob <b>Data Inicial</b>, escolha  uma data suficientemente distante no passado para incluir todos suas Contas. Você também pode exportar um subconjunto de Contas utilizando critérios mais avançados. Quando você , clicar <b>Executar Relatório/Run Report</b>',
'LBL_SF_NUM_9'=>'Um relatório será gerado, e página deve indicar <b>Status de Geração de Relatório: Completo.</b> Agora click <b>Exporte para Excel</b>',
'LBL_SF_NUM_10'=>'Sobre <b>Exportar Relatório/Export Report:</b>, para <b>Exportar Formato Arquivo/Export File Format:</b>, escolha <b>Comma Delimited .csv</b>. Click <b>Exportar/Export</b>.',
'LBL_SF_NUM_11'=>'Um pop up abrirá pra você salvar o arquivo exportado para seu computador.',
'LBL_IMPORT_CUSTOM_TITLE'=>'Muitas aplicações permitirão exportar dados em um <b>arquivo texto Delimitado por Vírgula (.csv)</b>. Generalmente a maioria das aplicações seguirão estes passos:',
'LBL_CUSTOM_NUM_1'=>'Acesse a aplicação e abra o arquivo de dados',
'LBL_CUSTOM_NUM_2'=>'Selecione a opção de menu <b>Salvar como/Save As...</b> ou <b>Exportar/Export...</b>',
'LBL_CUSTOM_NUM_3'=>'Salve o arquivo em <b>CSV</b> ou formato <b>Comma Separated Values</b>',

'LBL_STEP_3_TITLE'=>'Passo 3 de 4: Confirmar Importar e Campos',
'LBL_STEP_1'=>'Passo 1 de 3 : ',
'LBL_STEP_1_TITLE'=>'Selecione o arquivo .CSV',
'LBL_STEP_1_TEXT'=> ' vtiger CRM suporta importação de registros de arquivos .csv (<b> Comma Separated Values</b> ). Para iniciar a importação, localize o arquivo .CSV e click sobre o botão Próximo para Continuar.',

'LBL_SELECT_FIELDS_TO_MAP'=>'Na lista abaixo, selecione os campos que devem ser importados em cada campo no Sistema. Quando terminar, click <b>Importar Agora</b>:',

'LBL_DATABASE_FIELD'=>'Campo Base de Dados',
'LBL_HEADER_ROW'=>'Linha de Cabeçalho',
'LBL_ROW'=>'Linha',
'LBL_SAVE_AS_CUSTOM'=>'Salvar como Mapeamento Customizado:',
'LBL_CONTACTS_NOTE_1'=>'Sobrenome ou Primeiro Nome devem ser mapeados.',
'LBL_CONTACTS_NOTE_2'=>'Se Nome Completo está mapeado, então Primeiro Nome e Sobrenome são ignorados.',
'LBL_CONTACTS_NOTE_3'=>'Se Nome Completo está mapeado, então o dado deste campo será separado em Primeiro Nome e Sobrenome quando inserido na base de dados.',
'LBL_CONTACTS_NOTE_4'=>'Campos do Endereço 2 e 3 serão concatenados com o campo Endereço (principal) quando inserido na base de dados.',
'LBL_ACCOUNTS_NOTE_1'=>'Nome da Conta deve estar mapeado.',
'LBL_ACCOUNTS_NOTE_2'=>'Campos do Endereço 2 e 3 serão concatenados com o campo Endereço (principal) quando inserido na base de dados.',
'LBL_POTENTIALS_NOTE_1'=>'Nome Oportunidade, Nome Conta, Data Fechamento e Estágio de Vendas são campos obrigatórios.',
'LBL_OPPORTUNITIES_NOTE_1'=>'Nome Oportunidade, Nome Conta, Data Fechamento, e Estágio de Vendas são campos obrigatórios.',
'LBL_LEADS_NOTE_1'=>'O Último Nome deve ser mapeado.',
'LBL_LEADS_NOTE_2'=>'O Nome da Empresa deve ser mapeado.',
'LBL_IMPORT_NOW'=>'Importar Agora',
'LBL_'=>'',
'LBL_CANNOT_OPEN'=>'Não é possível abrir o arquivo importado para leitura',
'LBL_NOT_SAME_NUMBER'=>'Não há o mesmo número de campos por linha em seu arquivo',
'LBL_NO_LINES'=>'Não há linhas (registros) em seu arquivo de importação',
'LBL_FILE_ALREADY_BEEN_OR'=>'O arquivo de importação já foi processado ou não existe',
'LBL_SUCCESS'=>'Sucesso!',
'LBL_SUCCESSFULLY'=>'Importados com Sucesso!!',
'LBL_LAST_IMPORT_UNDONE'=>'Sua última importação foi desfeita',
'LBL_NO_IMPORT_TO_UNDO'=>'Não há importação para desfazer.',
'LBL_FAIL'=>'Falha:',
'LBL_RECORDS_SKIPPED'=>'registro omitido porque um ou mais campos requeridos foram perdidos',
'LBL_IDS_EXISTED_OR_LONGER'=>'registros omitidos porque um ou outro ID\'s já existe ou possui mais de 36 caracteres',
'LBL_RESULTS'=>'Resultados',
'LBL_IMPORT_MORE'=>'Importar Mais',
'LBL_FINISHED'=>'Finalizar',
'LBL_UNDO_LAST_IMPORT'=>'Desfazer Última Importação',

'LBL_SUCCESS_1' => 'No. de Registros Importados com Sucesso : ',
'LBL_SKIPPED_1' => 'No. de Registros Omitidos em função de um ou mais campos requeridos perdidos: ',

//Added for patch2 - Products Import Notes
'LBL_PRODUCTS_NOTE_1'=>'Nome do Produto deve ser mapeado',
'LBL_PRODUCTS_NOTE_2'=>'Antes de importar favor checar se uma coluna foi mapeada duas vezes',

//Added for version 5
'LBL_FILE_LOCATION'=>'Localização Arquivo :',
'LBL_STEP_2_3'=>'Passo 2 de 3 :',
'LBL_LIST_MAPPING'=>'Listar & Mapear',
'LBL_STEP_2_MSG'=>'As seguintes tabelas foram importadas',
'LBL_STEP_2_MSG1'=>'e outros detalhes.',
'LBL_STEP_2_TXT'=>'Para mapear os campos, Selecione o campo correspondente na caixa de combinação',
'LBL_USE_SAVED_MAPPING'=>'Mapeamento Utilizado Salvo :',
'LBL_MAPPING'=>'Mapeando',
'LBL_HEADERS'=>'Cabeçalho :',
'LBL_ERROR_MULTIPLE'=>'Alguns campos podem ser duplamente mapeados. Por favor verifique os campos mapeados.',
'LBL_STEP_3_3'=>'Passo 3 de 3',
'LBL_MAPPING_RESULTS'=>'Mapeando Resultados',
'LBL_LAST_IMPORTED'=>'Último Importado',
//Added for sript alerts
'PLEASE_CHECK_MAPPING' => "' é mapeado mais de uma vez. Por favor verifique o mapeamento.",
'MAP_MANDATORY_FIELD' => 'Por favor, mapear o campo obrigatório "',
'ENTER_SAVEMAP_NAME' => 'Por favor, digite Salvar Nome do Mapa',

//Added for 5.0.3
'to'=>'para',
'of'=>'de',
'are_imported_succesfully'=>'importado com sucesso',

// Added after 5.0.4 GA

//added for duplicate handling 
'LBL_LAST_IMPORT'=>'Último Importado',
'Select_Criteria_For_Duplicate' => 'Selecione Critério para Manusear Registros Duplicados',
'Manual_Merging' => 'Mesclagem Manual',
'Auto_Merging' => 'Mesclagem Automática',
'Ignore_Duplicate' => 'Ignorar registros importados duplicados',
'Overwrite_Duplicate' => 'Sobrescrever os registros duplicados',
'Duplicate_Records_Skipped_Info' => 'No. de Registros desconsiderados porque estavam duplicados : ',
'Duplicate_Records_Overwrite_Info' => 'No. de Registros Sobrescritos porque estavam duplicados : ',
'LBL_STEP_4_4' => 'Passo 4 de 4 : ',
'LBL_STEP_3_4'=>'Passo 3 de 4 :',
'LBL_STEP_2_4'=>'Passo 2 de 4 :',
'LBL_STEP_1_4'=>'Passo 1 de 4 : ',

'LBL_DELIMITER' => 'Delimitador:',
'LBL_FORMAT' => 'Formatar:',
'LBL_MAX_FILE_SIZE' => ' é o tamanho de arquivo máximo permitido',

'LBL_MERGE_FIELDS_DUPLICATE' => 'Mesclar Campos para Registros Duplicados durante importação',
'Customer Portal Login Details' => 'Detalhes Acesso Portal do Cliente',
);

$mod_list_strings = Array(
'contacts_import_fields' => Array(
	"firstname"=>"Nome"
	,"lastname"=>"Sobrenome"
	,"salutationtype"=>"Saudação"
	,"leadsource"=>"Fonte Lead"
	,"birthday"=>"Aniversário"
	,"donotcall"=>"Rejeita Chamada"
	,"emailoptout"=>"Rejeita Email"
	,"account_id"=>"Nome Conta"
	,"title"=>"Função"
	,"department"=>"Departamento"
	,"homephone"=>"Fone Residencial"
	,"mobile"=>"Celular"
	,"phone"=>"Fone Trabalho"
	,"otherphone"=>"Fone Alternativo"
	,"fax"=>"Fax"
	,"email"=>"Email"
	,"otheremail"=>"Email (Outro)"
	,"secondaryemail"=>"Email Secundário"
	,"assistant"=>"Assistente"
	,"assistantphone"=>"Fone Assistente"
	,"mailingstreet"=>"Endereço Correspondência"
	,"mailingpobox"=>"Cx Postal Endereço Correspondência"
	,"mailingcity"=>"Cidade Endereço Correspondência"
	,"mailingstate"=>"Estado Endereço Correspondência"
	,"mailingzip"=>"CEP Endereço Correspondência"
	,"mailingcountry"=>"País Endereço Correspondência"
	,"otherstreet"=>"Endereço Alternativo"
	,"otherpobox"=>"Cx Postal Endereço Alternativo"
	,"othercity"=>"Cidade Endereço Alternativo"
	,"otherstate"=>"Estado Endereço Alternativo"
	,"otherzip"=>"CEP Endereço Alternativo"
	,"othercountry"=>"País Endereço Alternativo"
	,"description"=>"Descrição"
	,"assigned_user_id"=>"Responsável"
    	),

'accounts_import_fields' => Array(
	//"id"=>"Account ID",
	"accountname"=>"Nome Conta",
	"website"=>"Website",
	"industry"=>"Atividade",
	"accounttype"=>"Tipo",
	"tickersymbol"=>"Cod. Bolsa",
	"parent_name"=>"Matriz",
	"employees"=>"Empregados",
	"ownership"=>"Proprietário",
	"phone"=>"Fone",
	"fax"=>"Fax",
	"otherphone"=>"Fone Alternativo",
	"email1"=>"Email",
	"email2"=>"Email Alternativo",
	"rating"=>"Avaliação",
	"siccode"=>"Cod. CNAE",
	"annual_revenue"=>"Receita Anual",
	"bill_street"=>"Endereço Faturamento",
	"bill_pobox"=>"Cx Postal Endereço Faturamento",
	"bill_city"=>"Cidade Endereço Faturamento",
	"bill_state"=>"Estado Endereço Faturamento",
	"bill_code"=>"CEP Endereço Faturamento",
	"bill_country"=>"País Endereço Faturamento",
	"ship_street"=>"Endereço Entrega",
	"ship_pobox"=>"Cx Postal Endereço Entrega",
	"ship_city"=>"Cidade Endereço Entrega",
	"ship_state"=>"Estado Endereço Entrega",
	"ship_code"=>"CEP Endereço Entrega",
	"ship_country"=>"País Endereço Entregay",
	"description"=>"Descrição",
	"assigned_user_id"=>"Responsável"
	),

'potentials_import_fields' => Array(
        	//"id"=>"ID Conta"
                 "potentialname"=>"Nome Oportunidade"
                , "account_id"=>"Nome Conta"
                , "opportunity_type"=>"Tipo Oportunidade"
                , "leadsource"=>"Fonte Lead"
                , "amount"=>"Quantidade"
                , "closingdate"=>"Data Fechamento"
                , "nextstep"=>"Próximo Passo"
                , "sales_stage"=>"Estágio de Vendas"
                , "probability"=>"Probabilidade"
                , "description"=>"Descrição"
        	,"assigned_user_id"=>"Responsável"
    	),


'leads_import_fields' => Array(
        	"salutationtype"=>"Saudação",
        	"firstname"=>"Nome",
        	"phone"=>"Telefone",
        	"lastname"=>"Sobrenome",
        	"mobile"=>"Celular",
        	"company"=>"Empresa",
        	"fax"=>"Fax",
        	"designation"=>"Função",
        	"email"=>"Email",
        	"leadsource"=>"Fonte Lead",
        	"website"=>"Website",
        	"industry"=>"Atividade",
        	"leadstatus"=>"Status Lead",
        	"annualrevenue"=>"Receita Anual",
        	"rating"=>"Avaliação",
        	"noofemployees"=>"Número Empregados",
        	"assigned_user_id"=>"Responsável",
		"secondaryemail"=>"Email Alternativo",
        	"lane"=>"Endereço",
        	"pobox"=>"Cx Postal",
        	"code"=>"CEP",
        	"city"=>"Cidade",
       		"country"=>"País",
        	"state"=>"Estado",
        	"description"=>"Descrição"
        	,"assigned_user_id"=>"Responsável"
    ),
    
 'products_import_fields' => Array(
    	'productname'=>'Nome Produto',
    	'productcode'=>'Código Produto',
    	'productcategory'=>'Categoria Produto',
    	'manufacturer'=>'Fabricante',
    	'product_description'=>'Descrição Produto',
    	'qty_per_unit'=>'Quantidade Por/Unidade',
    	'unit_price'=>'Preço Unitário',
    	'weight'=>'Peso',
    	'pack_size'=>'Dimensões',
    	'start_date'=>'Data Início',
    	'expiry_date'=>'Data Expiração',
    	'cost_factor'=>'Custo Produção',
    	'commissionmethod'=>'Método Comissão',
    	'discontinued'=>'Descontinuado',
    	'commissionrate'=>'Comissão',
    	'sales_start_date'=>'Data Início Vendas',
    	'sales_end_date'=>'Data Final Vendas',
    	'usageunit'=>'Unidade Utilizada',
    	'serialno'=>'No. Série',
    	'currency'=>'moeda',
    	'reorderlevel'=>'Nível Reabastecimento',
    	'website'=>'Web Site',
    	'taxclass'=>'Tipo de Imposto',
    	'mfr_part_no'=>'Cód Fabricante',
    	'vendor_part_no'=>'Cód Fornecedor',
    	'qtyinstock'=>'Quantidade em Estoque',
    	'productsheet'=>'Ficha Produto',
    	'qtyindemand'=>'Qde. Última Compra',
    	'glacct'=>'Conta Plano Contas',
    	'assigned_user_id'=>'Responsável'
	 ),
//Pavani...adding list of import fields for helpdesk and vendors
'helpdesk_import_fields' => Array(
        "ticketid"=>"No. Ticket",
        "priority"=>"Prioridade",
        "severity"=>"Gravidade",
        "status"=>"Status",
        "category"=>"Categoria",
        "title"=>"Título",
        "description"=>"Descrição",
        "solution"=>"Solução"
        ),

'vendors_import_fields' => Array(
        "vendorid"=>"Cód. Fornecedor",
        "vendorname"=>"Fornecedor",
        "phone"=>"Fone",
        "email"=>"E-mail",
        "website"=>"Website",
        "category"=>"Categoria",
        "street"=>"Endereço",
        "city"=>"Cidade",
        "state"=>"Estado",
        "pobox"=>"Cx Postal",
        "postalcode"=>"CEP",
        "country"=>"País",
        "description"=>"Descrição"
        )
//Pavani...end list
);

?>
