/*
* Kendo UI Localization Project for v2013.3.1316
* Copyright 2014 Telerik AD. All rights reserved.
* 
* Mexican Spanish (es-MX) Language Pack
*
* Project home  : https://github.com/loudenvier/kendo-global
* Kendo UI home : http://kendoui.com
* Author        : Salvador Parra (sparra@gmail.com)
* Update        : Joe Bordes (JPL TSolucio, S.L.)
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

kendo.ui.Locale = "Español de México (es-MX)";
kendo.ui.ColumnMenu.prototype.options.messages = 
  jQuery.extend(kendo.ui.ColumnMenu.prototype.options.messages, {

/* COLUMN MENU MESSAGES 
 ****************************************************************************/
  sortAscending: "Ascendente",
  sortDescending: "Descendente",
  filter: "Filtro",
  columns: "Columnas",
  done: "Terminado",
  settings: "Configuración Columna"
 /***************************************************************************/
});

kendo.ui.Groupable.prototype.options.messages = 
  jQuery.extend(kendo.ui.Groupable.prototype.options.messages, {

/* GRID GROUP PANEL MESSAGES 
 ****************************************************************************/
  empty: "Arrastre una columna aquí para agrupar por dicha columna"
 /***************************************************************************/
});

kendo.ui.FilterMenu.prototype.options.messages = 
  jQuery.extend(kendo.ui.FilterMenu.prototype.options.messages, {
  
/* FILTER MENU MESSAGES 
 ***************************************************************************/
	info: "Título:",        // sets the text on top of the filter menu
	filter: "Filtrar",      // sets the text for the "Filter" button
	clear: "Limpiar",        // sets the text for the "Clear" button
	// when filtering boolean numbers
	isTrue: "Verdadero", // sets the text for "isTrue" radio button
	isFalse: "Falso",     // sets the text for "isFalse" radio button
	//changes the text of the "And" and "Or" of the filter menu
	and: "Y",
	or: "O",
	selectValue: "Seleccione un valor",
	operator: "Operador",
	value: "Valor",
	cancel: "Cancelar"
 /***************************************************************************/
});
         
kendo.ui.FilterMenu.prototype.options.operators =
  jQuery.extend(kendo.ui.FilterMenu.prototype.options.operators, {

/* FILTER MENU OPERATORS (for each supported data type) 
 ****************************************************************************/
  string: {
      eq: "Es igual a",
      neq: "Es diferente a",
      startswith: "Comienza con",
      contains: "Contiene",
      doesnotcontain: "No contiene",
      endswith: "Termina con"
  },
  number: {
      eq: "Es igual a",
      neq: "Es diferente a",
      gte: "Es mayor que o igual a",
      gt: "Es mayor que",
      lte: "Es menor que o igual a",
      lt: "Es menor que"
  },
  date: {
      eq: "Es igual a",
      neq: "Es diferente de",
      gte: "Es igual o posterior a",
      gt: "Es posterior a",
      lte: "Es igual o anterior a",
      lt: "Es anterior a"
  },
  enums: {
      eq: "Es igual a",
      neq: "Es diferente de"
  }
 /***************************************************************************/
});

kendo.ui.Pager.prototype.options.messages = 
  jQuery.extend(kendo.ui.Pager.prototype.options.messages, {
  
/* PAGER MESSAGES 
 ****************************************************************************/
  display: "{0} - {1} de {2} elementos.",
  empty: "Sin datos para mostrar.",
  page: "Página",
  of: "de {0}",
  itemsPerPage: "elementos por página.",
  first: "Ir a la primera página",
  previous: "Ir a la página anterior",
  next: "Ir a la página siguiente",
  last: "Ir a la última página",
  refresh: "Refrescar"
 /***************************************************************************/
});

kendo.ui.Validator.prototype.options.messages = 
  jQuery.extend(kendo.ui.Validator.prototype.options.messages, {

/* VALIDATOR MESSAGES 
 ****************************************************************************/
  required: "{0} es obligatorio",
  pattern: "{0} no es válido",
  min: "{0} debe ser mayor o igual que {1}",
  max: "{0} debe ser menor o igual que {1}",
  step: "{0} no es válido",
  email: "{0} no es un correo electrónico válido",
  url: "{0} no es un URL válido",
  date: "{0} no es una fecha válida"
 /***************************************************************************/
});

kendo.ui.ImageBrowser.prototype.options.messages = 
  jQuery.extend(kendo.ui.ImageBrowser.prototype.options.messages, {

/* IMAGE BROWSER MESSAGES 
 ****************************************************************************/
  uploadFile: "Enviar",
  orderBy: "Ordenar por",
  orderByName: "Nombre",
  orderBySize: "Tamaño",
  directoryNotFound: "El directorio no fue encontrado.",
  emptyFolder: "Carpeta vacía",
  deleteFile: '¿Está seguro de que desea eliminar "{0}"?',
  invalidFileType: "El archivo seleccionado \"{0}\" no es válido. Los tipos de archivos soportados son {1}.",
  overwriteFile: "Un archivo con el nombre \"{0}\" ya existe en la carpeta actual. ¿Desea sobrescribirlo?",
  dropFilesHere: "Coloque los archivos aquí",
  search: "Buscar"
 /***************************************************************************/
});

kendo.ui.Editor.prototype.options.messages = 
  jQuery.extend(kendo.ui.Editor.prototype.options.messages, {

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
  viewHtml: "Ver HTML",
  fontName: "Seleccionar fuente",
  fontNameInherit: "(fuente heredada)",
  fontSize: "Seleccionar tamaño de la fuente",
  fontSizeInherit: "(tamaño heredado)",
  formatBlock: "Formatear",
  formatting: "Formateando",
  paragraph: "Párrafo",
  foreColor: "Color",
  backColor: "Color de fondo",
  style: "Estilos",
  emptyFolder: "Carpeta vacía",
  uploadFile: "Enviar",
  orderBy: "Ordenar por:",
  orderBySize: "Tamaño",
  orderByName: "Nombre",
  invalidFileType: "El archivo seleccionado \"{0}\" no es válido. Los tipos de archivos soportados son {1}.",
  deleteFile: '¿Está seguro de que desea eliminar "{0}"?',
  overwriteFile: "Un archivo con el nombre \"{0}\" ya existe en la carpeta actual. ¿Desea sobrescribirlo?",
  directoryNotFound: "El directorio no fue encontrado.",
  imageWebAddress: "Dirección de internet",
  imageAltText: "Texto alternativo",
  linkWebAddress: "URL Web",
  linkText: "Texto",
  linkToolTip: "ToolTip",
  linkOpenInNewWindow: "Abrir enlace en nueva ventana",
  dialogUpdate: "Actualizar",
  dialogInsert: "Insertar",
  dialogButtonSeparator: "o",
  dialogCancel: "Cancelar",
  createTable: "Crear tabla",
  addColumnLeft: "Añadir columna a la izquierda",
  addColumnRight: "Añadir columna a la derecha",
  addRowAbove: "Añadir fila arriba",
  addRowBelow: "Añadir fila abajo",
  deleteRow: "Eliminar fila",
  deleteColumn: "Eliminar columna"
 /***************************************************************************/
});

kendo.ui.NumericTextBox.prototype.options =
    jQuery.extend(kendo.ui.NumericTextBox.prototype.options, {

        /* NUMERIC TEXT BOX OR INTEGER TEXT BOX MESSAGES
        ****************************************************************************/
        upArrowText: "Incrementa valor",
        downArrowText: "Decrementa valor"
        /***************************************************************************/
    });

//The upload part add by IKKI
kendo.ui.Upload.prototype.options.localization = 
	jQuery.extend(kendo.ui.Upload.prototype.options.localization, {

/* UPLOAD LOCALIZATION
 ****************************************************************************/
	select: "Selecciona ficheros...",
	cancel: "Cancelar",
	retry: "Intentar de nuevo",
	remove: "Eliminar",
	uploadSelectedFiles: "Subir ficheros",
	dropFilesHere: "Arrastra ficheros aquí para subir",
	statusUploading: "subiendo",
	statusUploaded: "subidos",
	statusWarning: "aviso",
	statusFailed: "error",
	headerStatusUploading: "Subiendo...",
	headerStatusUploaded: "Terminado"
 /***************************************************************************/
});

