/*
* Kendo UI Localization Project for v2012.3.1114 
* Copyright 2012 Telerik AD. All rights reserved.
* 
* Portugal Portuguese (pt-PT) Language Pack
*
* Project home  : https://github.com/loudenvier/kendo-global
* Kendo UI home : http://kendoui.com
* Author        : Pedro Carvalho  
*                 
*
* This project is released to the public domain, although one must abide to the 
* licensing terms set forth by Telerik to use Kendo UI, as shown bellow.
*
* Telerik's original licensing terms:
* -----------------------------------
* Kendo UI Web commercial licenses may be obtained at
* https://www.kendoui.com/purchase/license-agreement/kendo-ui-web-commercial.aspx
* If you do not own a commercial license, this file shall be governed by the
* GNU General Public License (GPL) version 3.
* For GPL requirements, please review: http://www.gnu.org/copyleft/gpl.html
*/

kendo.ui.Locale = "Português Portugal (pt-PT)";
kendo.ui.ColumnMenu.prototype.options.messages = 
  $.extend(kendo.ui.ColumnMenu.prototype.options.messages, {

/* COLUMN MENU MESSAGES 
 ****************************************************************************/   
  sortAscending: "Ascendente",
  sortDescending: "Descendente",
  filter: "Filtro",
  columns: "Colunas"
 /***************************************************************************/   
});

kendo.ui.Groupable.prototype.options.messages = 
  $.extend(kendo.ui.Groupable.prototype.options.messages, {

/* GRID GROUP PANEL MESSAGES 
 ****************************************************************************/   
  empty: "Arraste colunas aqui para agrupar pelas mesmas"
 /***************************************************************************/   
});

kendo.ui.FilterMenu.prototype.options.messages = 
  $.extend(kendo.ui.FilterMenu.prototype.options.messages, {
  
/* FILTER MENU MESSAGES 
 ***************************************************************************/   
	info: "Título:",        // sets the text on top of the filter menu
	filter: "Filtrar",      // sets the text for the "Filter" button
	clear: "Limpar",        // sets the text for the "Clear" button
	// when filtering boolean numbers
	isTrue: "Verdadeiro", // sets the text for "isTrue" radio button
	isFalse: "Falso",     // sets the text for "isFalse" radio button
	//changes the text of the "And" and "Or" of the filter menu
	and: "E",
	or: "Ou",
  selectValue: "-selecione um valor-"
 /***************************************************************************/   
});
         
kendo.ui.FilterMenu.prototype.options.operators =           
  $.extend(kendo.ui.FilterMenu.prototype.options.operators, {

/* FILTER MENU OPERATORS (for each supported data type) 
 ****************************************************************************/   
  string: {
      eq: "Igual a",
      neq: "Diferente de",
      startswith: "Começa com",
      contains: "Contém",
      doesnotcontain: "Não contém",
      endswith: "Termina com"
  },
  number: {
      eq: "Igual a",
      neq: "Diferente de",
      gte: "Maior ou igual a",
      gt: "Maior que",
      lte: "Menor ou igual a",
      lt: "Menor que"
  },
  date: {
      eq: "Igual a",
      neq: "Diferente de",
      gte: "Igual ou mais recente que",
      gt: "Mais recente que",
      lte: "Igual ou mais antigo que",
      lt: "Mais antigo que"
  },
  enums: {
      eq: "Igual a",
      neq: "Diferente de"
  }
 /***************************************************************************/   
});

kendo.ui.Pager.prototype.options.messages = 
  $.extend(kendo.ui.Pager.prototype.options.messages, {
  
/* PAGER MESSAGES 
 ****************************************************************************/   
  display: "{0} - {1} de {2} itens",
  empty: "Nada a exibir",
  page: "Página",
  of: "de {0}",
  itemsPerPage: "itens por página",
  first: "Ir para a primeira página",
  previous: "Ir para a página anterior",
  next: "Ir para a página seguinte",
  last: "Ir para a última página",
  refresh: "Atualizar"
 /***************************************************************************/   
});

kendo.ui.Validator.prototype.options.messages = 
  $.extend(kendo.ui.Validator.prototype.options.messages, {

/* VALIDATOR MESSAGES 
 ****************************************************************************/   
  required: "{0} é obrigatório",
  pattern: "{0} não é válido",
  min: "{0} deve ser maior ou igual a {1}",
  max: "{0} deve ser menor ou igual a {1}",
  step: "{0} não é válido",
  email: "{0} não é um email válido",
  url: "{0} não é uma URL válida",
  date: "{0} não é uma data válida"
 /***************************************************************************/   
});

kendo.ui.ImageBrowser.prototype.options.messages = 
  $.extend(kendo.ui.ImageBrowser.prototype.options.messages, {

/* IMAGE BROWSER MESSAGES 
 ****************************************************************************/   
  uploadFile: "Enviar",
  orderBy: "Ordenar por",
  orderByName: "Nome",
  orderBySize: "Tamanho",
  directoryNotFound: "Uma pasta com este nome não foi encontrada.",
  emptyFolder: "Pasta Vazia",
  deleteFile: 'Tem a certeza que deseja eliminar "{0}"?',
  invalidFileType: "O arquivo selecionado \"{0}\" não é válido. Os tipos de arquivo suportados são {1}.",
  overwriteFile: "Um arquivo com o nome \"{0}\" já existe na pasta atual. Deseja sobrepor?",
  dropFilesHere: "coloque aqui os arquivos para enviá-los"
 /***************************************************************************/   
});

kendo.ui.Editor.prototype.options.messages = 
  $.extend(kendo.ui.Editor.prototype.options.messages, {

/* EDITOR MESSAGES 
 ****************************************************************************/   
  bold: "Negrito",
  italic: "Itálico",
  underline: "Sublinhado",
  strikethrough: "Rasurado",
  superscript: "Sobrescrito",
  subscript: "Subscrito",
  justifyCenter: "Centrar texto",
  justifyLeft: "Alinhar texto à esquerda",
  justifyRight: "Alinhar texto à direita",
  justifyFull: "Justificar",
  insertUnorderedList: "Inserir lista não ordenada",
  insertOrderedList: "Inserir lista ordenada",
  indent: "Aumentar recuo",
  outdent: "Diminuir recuo",
  createLink: "Inserir link",
  unlink: "Remover link",
  insertImage: "Inserir imagem",
  insertHtml: "Inserir HTML",
  fontName: "Selecionar família da fonte",
  fontNameInherit: "(fonte herdada)",
  fontSize: "Selecionar tamanho da fonte",
  fontSizeInherit: "(tamanho herdado)",
  formatBlock: "Formatar",
  foreColor: "Cor",
  backColor: "Cor de fundo",
  style: "Estilos",
  emptyFolder: "Pasta Vazia",
  uploadFile: "Enviar",
  orderBy: "Ordenar por:",
  orderBySize: "Tamanho",
  orderByName: "Nome",
  invalidFileType: "O arquivo selecionado \"{0}\" não é válido. Os arquivos suportados são {1}.",
  deleteFile: 'Tem a certeza que deseja eliminar "{0}"?',
  overwriteFile: 'Um arquivo com o nome "{0}" já existe na pasta atual. Deseja sobrepor?',
  directoryNotFound: "Uma pasta com este nome não foi encontrada.",
  imageWebAddress: "Endereço internet",
  imageAltText: "Texto alternativo",
  dialogInsert: "Inserir",
  dialogButtonSeparator: "ou",
  dialogCancel: "Cancelar"
 /***************************************************************************/   
});
