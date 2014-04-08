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
 * Contributor(s): Valmir Carlos Trindade/Translate to Brazilian Portuguese| 03/03/2012 |Curitiba/Paraná/Brasil.|www.ttcasolucoes.com.br
 ********************************************************************************/


$mod_strings = Array(
'LBL_MIGRATE_INFO'=>'Preencha o formulário para migrar dados da <b><i> Fonte </i></b> para <b><i> versão atual do vtigerCRM</i></b>',
'LBL_CURRENT_VT_MYSQL_EXIST'=>'O MySQL atual do vtiger encontra-se em',
'LBL_THIS_MACHINE'=>'Nesta Máquina',
'LBL_DIFFERENT_MACHINE'=>'Em outra Máquina',
'LBL_CURRENT_VT_MYSQL_PATH'=>'Caminho para o MySQL do vtiger atual',
'LBL_SOURCE_VT_MYSQL_DUMPFILE'=>'Nome do arquivo <b>Fonte de Dados</b> do vtiger',
'LBL_NOTE_TITLE'=>'Nota:',
'LBL_NOTES_LIST1'=>'Se o MySQL da versão atual do vtiger estiver na mesma Máquina, então entre com o caminho para o MySQL, ou você poderá entrar com o arquivo de dados (Dump) se você o tiver.',
'LBL_NOTES_LIST2'=>'Se o MySQL da versão atual do vtiger estiver em Máquina Diferente, então entre com o caminho completo do arquivo de dados (Dump).',
'LBL_NOTES_DUMP_PROCESS'=>'Para obter a Base de Dados Dump, por favor execute o seguinte comando a partir do diretório <b>mysql/bin</b>
               		   <br><b>mysqldump --user=<b>mysql_username</b> --password=<b>mysql-password</b> -h <b>hostname</b> --port=<b>mysql_port</b> <b>database_name</b> > dump_filename</b>
               		   <br>adicionar <b>SET FOREIGN_KEY_CHECKS = 0</b>; -- no início do arquivo dump
               		   <br>adicionar <b>SET FOREIGN_KEY_CHECKS = 1</b>; -- no final do arquivo dump',
'LBL_NOTES_LIST3'=>'Informe o caminho do MySQL como <b>/home/crm/vtigerCRM4_5/mysql</b>',
'LBL_NOTES_LIST4'=>'Informe o caminho completo para o arquivo Dump, como <b>/home/fullpath/4_2_dump.txt</b>',

'LBL_CURRENT_MYSQL_PATH_FOUND'=>'O diretório do MySQL da instalação atual foi localizado.',
'LBL_SOURCE_HOST_NAME'=>'Nome Host Origem :',
'LBL_SOURCE_MYSQL_PORT_NO'=>'No. Porta MySql Origem:',
'LBL_SOURCE_MYSQL_USER_NAME'=>'Nome Usuário MySql Origem:',
'LBL_SOURCE_MYSQL_PASSWORD'=>'Senha MySql Origem :',
'LBL_SOURCE_DB_NAME'=>'Nome Banco Dados Origem:',
'LBL_MIGRATE'=>'Migrar para Versão Atual',
//Added after 5 Beta
'LBL_UPGRADE_VTIGER'=>'Atualizar Banco de Dados do vtiger CRM',
'LBL_UPGRADE_FROM_VTIGER_423'=>'Atualizar Banco de Dados do vtiger CRM 4.2.3 para 5.0.0',
'LBL_SETTINGS'=>'Configuração',
'LBL_STEP'=>'Passo',
'LBL_SELECT_SOURCE'=>'Selecionar Fonte',
'LBL_STEP1_DESC'=>'Para iniciar a migração do Banco de Dados, você deve especificar o formato no qual os dados antigos estão disponíveis',
'LBL_RADIO_BUTTON1_TEXT'=>'Tenho acesso ao sistema de Banco de Dados do vtiger CRM',
'LBL_RADIO_BUTTON1_DESC'=>'Esta opção requer que você tenha o endereço do host da máquina (onde o Banco de Dados está armazenado) e os detalhes de acesso ao BD. Tanto o sistema local como o remoto são suportados neste método. Recorra à documentação para ajuda.',
'LBL_RADIO_BUTTON2_TEXT'=>'Tenho acesso ao Banco de Dados Dump do vtiger CRM arquivado',
'LBL_RADIO_BUTTON2_DESC'=>'Esta opção requer Banco de Dados Dump disponível localmente na mesma máquina na qual você está atualizando. Você não pode acessar dados dump de uma máquina diferente (Servidor de Banco de Dados remoto). Recorra à documentação para ajuda.',
'LBL_RADIO_BUTTON3_TEXT'=>'Tenho um novo Banco de Dados com dados da versão 4.2.3',
'LBL_RADIO_BUTTON3_DESC'=>'Esta opção requer detalhes do sistema de Banco de Dados do vtiger CRM 4.2.3, inclusive o ID do Servidor do Banco de Dados, nome de usuário e senha. Você não pode acessar os dados dump de uma máquina diferente (Servidor de Banco de Dados Remoto).',

'LBL_HOST_DB_ACCESS_DETAILS'=>'Detalhes do Acesso ao Host do Banco de Dados',
'LBL_MYSQL_HOST_NAME_IP'=>'Endereço IP ou Nome do Host MySQL : ',
'LBL_MYSQL_PORT'=>'Número Porta MySQL : ',
'LBL_MYSQL_USER_NAME'=>'Nome Usuário MySql : ',
'LBL_MYSQL_PASSWORD'=>'Senha MySql : ',
'LBL_DB_NAME'=>'Nome Banco de Dados : ',

'LBL_LOCATE_DB_DUMP_FILE'=>'Local arquivo Banco de Dados Dump',
'LBL_DUMP_FILE_LOCATION'=>'Local do Arquivo Dump : ',

'LBL_RADIO_BUTTON3_PROCESS'=>'<b>Por favor não especifique detalhes do Banco de Dados da versão 4.2.3. Esta opção alterará diretamente o Banco de Dados.</b>
<br>Recomenda-se fazer o seguinte:
<br>1. Gere arquivo dump do Banco de Dados de sua versão 4.2.3
<br>2. Crie novo Banco de Dados (Melhor criar um Banco de Dados no servidor onde o Banco de Dados do seu viger 5.0 está rodando.)
<br>3. Aplique o aquivo dump desta versão 4.2.3 para este novo Banco de Dados.
<br>Agora forneça detalhes do acesso a este novo Banco de Dados. Esta migração modificará este Banco de Dados para ajustar ao esquema da versão 5.0.
Então você pode fornecer este nome de Banco de Dados no arquivo config.inc.php para utilizar este Banco de Dados ie., $dbconfig[\'db_name\'] = \'novo nome BD\';',

'LBL_ENTER_MYSQL_SERVER_PATH'=>'Entre com o endereço do Servidor do MySQL',
'LBL_SERVER_PATH_DESC'=>'O endereço do servidor MySQL como <b>/home/5beta/vtigerCRM5_beta/mysql/bin</b> ou <b>c:\Arquivos de Programas\mysql\bin</b>',
'LBL_MYSQL_SERVER_PATH'=>'Endereço do Servidor MySQL : ',
'LBL_MIGRATE_BUTTON'=>'Migrar',
'LBL_CANCEL_BUTTON'=>'Cancelar',
'LBL_UPGRADE_FROM_VTIGER_5X'=>'Atualize a base de dados de vtiger CRM 5.x para próxima versão',
'LBL_PATCH_OR_MIGRATION'=>'você deve especificar a versão da fonte da base de dados (Patch de atualização ou Migração)',
//Added for java script alerts
'ENTER_SOURCE_HOST' => 'Por favor, digite Nome da Fonte do Host',
'ENTER_SOURCE_MYSQL_PORT' => 'Por favor, digite o Número da Porta da Fonte MySql',
'ENTER_SOURCE_MYSQL_USER' => 'Por favor, digite o Nome de Usuário da Fonte MySql',
'ENTER_SOURCE_DATABASE' => 'Por favor, digite o Nome da Fonte do Banco de Dados',
'ENTER_SOURCE_MYSQL_DUMP' => 'Por favor, digite Arquivo Dump Válido do MySql',
'ENTER_HOST' => 'Por favor, digite o Nome do Host',
'ENTER_MYSQL_PORT' => 'Por favor, digite o Número da Porta MySql',
'ENTER_MYSQL_USER' => 'Por favor, digite o Nome de Usuário MySql',
'ENTER_DATABASE' => 'Por favor, digite o Nome do Banco de Dados',
'SELECT_ANYONE_OPTION' => 'Por favor, selecione qualquer uma das opções',
'ENTER_CORRECT_MYSQL_PATH' => 'Por favor, digite o Caminho Correto do MySql',

);






?>
