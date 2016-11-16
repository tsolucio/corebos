/*
* Kendo UI Localization Project for v2012.3.1114 
* Copyright 2012 Telerik AD. All rights reserved.
* 
* Peruvian Spanish (es-PE) Language Pack
*
* Project home  : https://github.com/loudenvier/kendo-global
* Kendo UI home : http://kendoui.com
* Author        : Carlos Esquivel
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

kendo.ui.Locale = "Español Peruano (es-PE)";
kendo.ui.ColumnMenu.prototype.options.messages = 
  $.extend(kendo.ui.ColumnMenu.prototype.options.messages, {

/* COLUMN MENU MESSAGES 
 ****************************************************************************/   
  sortAscending: "Ascendente",
  sortDescending: "Descendente",
  filter: "Filtro",
  columns: "Columnas"
 /***************************************************************************/   
});

kendo.ui.Groupable.prototype.options.messages = 
  $.extend(kendo.ui.Groupable.prototype.options.messages, {

/* GRID GROUP PANEL MESSAGES 
 ****************************************************************************/   
  empty: "Arrastre el título de una columna aquí para agrupar por esa columna"
 /***************************************************************************/   
});

kendo.ui.FilterMenu.prototype.options.messages = 
  $.extend(kendo.ui.FilterMenu.prototype.options.messages, {
  
/* FILTER MENU MESSAGES 
 ***************************************************************************/   
	info: "Título:",        // sets the text on top of the filter menu
	filter: "Filtrar",      // sets the text for the "Filter" button
	clear: "Limpiar",        // sets the text for the "Clear" button
	// when filtering boolean numbers
	isTrue: "Es verdadero", // sets the text for "isTrue" radio button
	isFalse: "Es falso",     // sets the text for "isFalse" radio button
	//changes the text of the "And" and "Or" of the filter menu
	and: "Y",
	or: "O",
  selectValue: "-seleccione un valor-"
 /***************************************************************************/   
});
		 
kendo.ui.FilterMenu.prototype.options.operators =           
  $.extend(kendo.ui.FilterMenu.prototype.options.operators, {

/* FILTER MENU OPERATORS (for each supported data type) 
 ****************************************************************************/   
  string: {
	  eq: "Igual a",
	  neq: "Diferente a",
	  startswith: "Empieza con",
	  contains: "Contiene",
	  doesnotcontain: "No contiene",
	  endswith: "Termina con"
  },
  number: {
	  eq: "Igual a",
	  neq: "Diferente a",
	  gte: "Es mayor que o igual a",
	  gt: "Es mayor que",
	  lte: "Es menor que o igual a",
	  lt: "Es menor que"
  },
  date: {
	  eq: "Igual a",
	  neq: "Diferente a",
	  gte: "Es igual o más reciente que",
	  gt: "Es más reciente que",
	  lte: "Es igual o más antiguo que",
	  lt: "Es más antiguo que"
  },
  enums: {
	  eq: "Igual a",
	  neq: "Diferente a"
  }
 /***************************************************************************/   
});

kendo.ui.Pager.prototype.options.messages = 
  $.extend(kendo.ui.Pager.prototype.options.messages, {
  
/* PAGER MESSAGES 
 ****************************************************************************/   
  display: "{0} - {1} de {2} ítems",
  empty: "No hay datos",
  page: "Página",
  of: "de {0}",
  itemsPerPage: "ítems por página",
  first: "Ir a la primera página",
  previous: "Ir a la página anterior",
  next: "Ir a la página seguiente",
  last: "Ir a la última página",
  refresh: "Refrescar"
 /***************************************************************************/   
});

kendo.ui.Validator.prototype.options.messages = 
  $.extend(kendo.ui.Validator.prototype.options.messages, {

/* VALIDATOR MESSAGES 
 ****************************************************************************/   
  required: "{0} es requerido",
  pattern: "{0} no es válido",
  min: "{0} debe ser mayor o igual a {1}",
  max: "{0} debe ser menor o igual a {1}",
  step: "{0} no es válido",
  email: "{0} no es un correo electrónico válido",
  url: "{0} no es una URL válida",
  date: "{0} no es una fecha válida"
 /***************************************************************************/   
});

kendo.ui.ImageBrowser.prototype.options.messages = 
  $.extend(kendo.ui.ImageBrowser.prototype.options.messages, {

/* IMAGE BROWSER MESSAGES 
 ****************************************************************************/   
  uploadFile: "Enviar",
  orderBy: "Ordenar por",
  orderByName: "Nombre",
  orderBySize: "Tamaño",
  directoryNotFound: "El directorio no fue encontrado.",
  emptyFolder: "Carpeta vacía",
  deleteFile: '¿Está seguro que desea eliminar "{0}"?',
  invalidFileType: "El archivo seleccionado \"{0}\" no es válido. Los tipos de archivos soportados son {1}.",
  overwriteFile: "Un archivo con el nombre \"{0}\" ya existe en la carpeta actual. ¿Desea sobrescribirlo?",
  dropFilesHere: "coloque los archivos aquí"
 /***************************************************************************/   
});

kendo.ui.Editor.prototype.options.messages = 
  $.extend(kendo.ui.Editor.prototype.options.messages, {

/* EDITOR MESSAGES 
 ****************************************************************************/   
  bold: "Negrita",
  italic: "Cursiva",
  underline: "Subrayado",
  strikethrough: "Tachado",
  superscript: "Superíndice",
  subscript: "Subíndice",
  justifyCenter: "Centrar texto",
  justifyLeft: "Alinear texto a la izquierda",
  justifyRight: "Alinear texto a la derecha",
  justifyFull: "Justificar",
  insertUnorderedList: "Insertar una lista",
  insertOrderedList: "Insertar una lista ordenada",
  indent: "Aumentar sangría",
  outdent: "Disminuir sangría",
  createLink: "Crear enlace",
  unlink: "Remover enlace",
  insertImage: "Insertar imagen",
  insertHtml: "Insertar HTML",
  fontName: "Seleccionar la fuente",
  fontNameInherit: "(fuente heredada)",
  fontSize: "Seleccionar tamaño de la fuente",
  fontSizeInherit: "(tamaño heredado)",
  formatBlock: "Formatear",
  foreColor: "Color",
  backColor: "Color de fondo",
  style: "Estilos",
  emptyFolder: "Carpeta vacía",
  uploadFile: "Enviar",
  orderBy: "Ordenar por:",
  orderBySize: "Tamaño",
  orderByName: "Nombre",
  invalidFileType: "El archivo seleccionado \"{0}\" no es válido. Los tipos de archivos soportados son {1}.",
  deleteFile: '¿Está seguro que desea eliminar "{0}"?',
  overwriteFile: "Un archivo con el nombre \"{0}\" ya existe en la carpeta actual. ¿Desea sobrescribirlo?",
  directoryNotFound: "El directorio no fue encontrado.",
  imageWebAddress: "Dirección de internet",
  imageAltText: "Texto alternativo",
  dialogInsert: "Insertar",
  dialogButtonSeparator: "o",
  dialogCancel: "Cancelar"
 /***************************************************************************/   
});
